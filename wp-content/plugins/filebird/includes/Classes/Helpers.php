<?php
namespace FileBird\Classes;

use FileBird\Model\Folder as FolderModel;

defined( 'ABSPATH' ) || exit;

class Helpers {

    protected static $instance = null;

    public static function getInstance() {
		if ( null == self::$instance ) {
            self::$instance = new self();
		}
        return self::$instance;
    }

    public static function sanitize_array( $var ) {
        if ( is_array( $var ) ) {
            return array_map( 'self::sanitize_array', $var );
        } else {
            return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
        }
    }

    public static function sanitize_for_excel( $input ) {
        $input               = (string) $input;
        $dangerousCharacters = array( '=', '+', '-', '@', '|' );

        // Strip leading whitespace and formula sigils repeatedly, e.g. " \t =1+1".
        while ( '' !== $input ) {
            $trimmed = ltrim( $input, " \t\r\n\0\x0B" );

            if ( $trimmed !== $input ) {
                $input = $trimmed;
                continue;
            }

            if ( in_array( $input[0], $dangerousCharacters, true ) ) {
                $input = substr( $input, 1 );
                continue;
            }

            break;
        }

        return $input;
    }

    /**
     * Serialize a value as a single RFC 4180 CSV field.
     *
     * Formula sigils are removed first, then the value is quoted with any
     * embedded double quote doubled so it can never terminate the field early.
     */
    public static function escape_csv_field( $input ) {
        return '"' . str_replace( '"', '""', self::sanitize_for_excel( $input ) ) . '"';
    }

    /**
     * Whether the current user may edit every supplied object.
     *
     * The whole batch is validated before any caller mutates anything, so a
     * request mixing authorized and unauthorized IDs is rejected atomically.
     *
     * @param array|int   $ids                Object IDs.
     * @param string|null $expected_post_type Required post type, or null to accept any.
     * @return bool
     */
    public static function canEditObjects( $ids, $expected_post_type = null ) {
        $ids = array_map( 'intval', (array) $ids );

        if ( count( $ids ) === 0 ) {
            return false;
        }

        foreach ( $ids as $id ) {
            if ( $id <= 0 ) {
                return false;
            }

            $post = get_post( $id );

            if ( ! $post ) {
                return false;
            }

            if ( null !== $expected_post_type && $post->post_type !== $expected_post_type ) {
                return false;
            }

            if ( ! current_user_can( 'edit_post', $id ) ) {
                return false;
            }
        }

        return true;
    }

    public static function sanitize_intval_array( $var ) {
        if ( is_array( $var ) ) {
            return array_map( 'intval', $var );
        } else {
            return intval( $var );
        }
    }

    const BACKUP_META_OPTION = 'fbv_backups_meta';

    public static function backupOptionLike() {
        global $wpdb;
        return $wpdb->esc_like( 'filebird_backup_' ) . '%';
    }

    /**
     * Snapshot the folder structure into an option.
     *
     * @param string $type auto | manual | before_import | before_restore
     * @param string $note Shown next to the backup, e.g. the imported file's name.
     */
    public static function backupFileBird( $type = 'auto', $note = '' ) {
        global $wpdb;
		$keep = 29;
		$count_backup = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->options} WHERE `option_name` LIKE %s", self::backupOptionLike() ) );
		// Make room for the one about to be added, so no more than $keep are ever stored.
		if( $count_backup >= $keep ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE %s ORDER BY `option_id` ASC LIMIT %d", self::backupOptionLike(), (int)($count_backup - $keep + 1) ) );
		}
		$folders = FolderModel::exportAll();
        $now = time();
        // The id is a timestamp, so two backups in the same second (e.g. Backup Now right before an import) would overwrite each other.
        while ( false !== get_option( 'filebird_backup_' . gmdate( 'Y_m_d_H_i_s', $now ), false ) ) {
            $now++;
        }
        $id = gmdate( 'Y_m_d_H_i_s', $now );

        $name = self::backupName( $id );
		update_option( 'filebird_backup_' . $id, $folders, false );

        $meta = array_merge(
            array(
                'type'       => $type,
                'note'       => sanitize_text_field( $note ),
                // Restore needs it to tell "no folders" from "not exported" for a post type.
                'post_types' => self::enabledPostTypes(),
            ),
            self::countBackupRows( $folders )
        );
        $all_meta = self::getBackupsMeta();
        $all_meta[ $id ] = $meta;
        update_option( self::BACKUP_META_OPTION, $all_meta, false );

        return array_merge( compact( 'id', 'name' ), $meta );
    }

    /** A backup's date in the site's date and time format; the id is the UTC time it was made (Y_m_d_H_i_s). */
    public static function backupName( $id ) {
        $date = \DateTime::createFromFormat( 'Y_m_d_H_i_s', $id, new \DateTimeZone( 'UTC' ) );
        if ( ! $date ) {
            return $id;
        }
        return wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $date->getTimestamp() );
    }

    /** Meta of the backups that still exist, keyed by backup id. */
    public static function getBackupsMeta() {
        global $wpdb;
        $meta = get_option( self::BACKUP_META_OPTION, array() );
        if ( ! is_array( $meta ) ) {
            $meta = array();
        }
        $ids = array_map(
            function( $option_name ) {
                return substr( $option_name, strlen( 'filebird_backup_' ) );
            },
            $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", self::backupOptionLike() ) )
        );
        return array_intersect_key( $meta, array_flip( $ids ) );
    }

    /** Post types that have FileBird folders turned on. */
    public static function enabledPostTypes() {
        $post_types = get_option( 'fbv_enabled_posttype', '' );
        // Saved comma-separated, though the settings page also copes with an array.
        return array_values( array_filter( is_array( $post_types ) ? $post_types : explode( ',', (string) $post_types ) ) );
    }

    /** How many folders and filed items a backup holds. */
    public static function countBackupRows( $rows ) {
        $folders = 0;
        $files   = 0;
        foreach ( (array) $rows as $row ) {
            $folders++;
            if ( ! empty( $row['attachment_ids'] ) ) {
                $files += count( explode( '|', $row['attachment_ids'] ) );
            }
        }
        return compact( 'folders', 'files' );
    }

    /**
     * Normalize a site URL so http/https, www and a trailing slash don't make the same site look different.
     */
    public static function siteKey( $url ) {
        $url = strtolower( trim( (string) $url ) );
        $url = preg_replace( '#^[a-z]+://#', '', $url );
        $url = preg_replace( '#^www\.#', '', $url );
        return untrailingslashit( $url );
    }

    public static function getAttachmentIdsByFolderId( $folder_id ) {
        global $wpdb;
        return $wpdb->get_col( 'SELECT `attachment_id` FROM ' . $wpdb->prefix . 'fbv_attachment_folder WHERE `folder_id` = ' . (int) $folder_id );
    }

    public static function getAttachmentCountByFolderId( $folder_id ) {
        return Tree::getCount( $folder_id );
    }

    public static function view( $path, $data = array() ) {
        extract( $data );
        ob_start();
        include_once NJFB_PLUGIN_PATH . 'views/' . $path . '.php';
        return ob_get_clean();
    }

    public static function isListMode() {
		if ( function_exists( 'get_current_screen' ) ) {
            $screen = get_current_screen();
            return ( isset( $screen->id ) && 'upload' == $screen->id );
		}
        return false;
    }

    public static function wp_kses_i18n( $string ) {
        return wp_kses(
            $string,
            array(
                'strong' => array(),
                'a'      => array(
                    'target' => array(),
                    'href'   => array(),
                ),
            )
        );
    }

    public static function findFolder( $folder_id, $tree ) {
        $folder = null;
        foreach ( $tree as $k => $v ) {
            if ( $v['id'] == $folder_id ) {
                $folder = $v;
                break;
            } else {
                $folder = self::findFolder( $folder_id, $v['children'] );
                if ( ! is_null( $folder ) ) {
                    break;
                } else {
                    continue;
                }
            }
        }
        return $folder;
    }

    public static function get_bytes( $post_id ) {
        $bytes = '';
        $meta  = wp_get_attachment_metadata( $post_id );
        if ( isset( $meta['filesize'] ) ) {
            $bytes = $meta['filesize'];
        } else {
            $attached_file = get_attached_file( $post_id );
            if ( file_exists( $attached_file ) ) {
                $bytes = \wp_filesize( $attached_file );
            }
        }
        return $bytes;
    }

    public static function loadView( $view, $data = array(), $return_html = false ) {
        $viewPath = NJFB_PLUGIN_PATH . 'views/' . $view . '.php';
        if ( ! file_exists( $viewPath ) ) {
            die( 'View <strong>' . esc_html( $viewPath ) . '</strong> not found!' );
        }
        extract( $data );
        if ( $return_html === true ) {
            ob_start();
            include_once $viewPath;
            return ob_get_clean();
        }
        include_once $viewPath;
    }
    public static function getDomain() {
		$url = get_site_url();
		if ( $url == '' || $url == null ) {
			$url = home_url();
		}
		$url = preg_replace( '#https?:\/\/#', '', $url );
		return apply_filters( 'fbv_domain_for_activate', $url );
	}
    public static function getIp() {
        return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
    }
    public static function removeEmojis( $text ) {
        // Emoji Unicode ranges
        $emojiRegex = '/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F700}-\x{1F77F}]|[\x{1F780}-\x{1F7FF}]|[\x{1F800}-\x{1F8FF}]|[\x{1F900}-\x{1F9FF}]|[\x{1FA00}-\x{1FA6F}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|\x{2B50}/u';

        // Remove emojis from the text
        $textWithoutEmojis = preg_replace( $emojiRegex, '', $text );

        return $textWithoutEmojis;
    }
    public static function isOptionCollationMb4() {
        global $wpdb;
        $query = $wpdb->get_row( "SHOW FULL COLUMNS FROM $wpdb->options LIKE 'option_value'" );
        if ( is_object( $query ) && isset( $query->Collation ) ) {
            return strpos( $query->Collation, 'mb4' ) !== false;
        } else {
            return false;
        }
    }

    public static function array_to_in_clause( $array ) {
        // Escape each value and wrap it in single quotes
        $escaped_values = array_map(
            function( $value ) {
            // Escape the value to prevent SQL injection
            return "'" . esc_sql( $value ) . "'";
			},
            $array
            );

        // Join the escaped values into a comma-separated string
        $values_string = implode( ', ', $escaped_values );

        // Return the formatted string
        return $values_string;
    }
    public static function buildExclusionConditions() {
        global $wpdb;
        
        $conditions = array();

        $exclusions = array(
            'elementor_screenshots' => function_exists( '_is_elementor_installed' ),
            'picu_collections'      => function_exists( 'picu_exclude_collection_images_from_library' ),
            'uncode_gallery'        => function_exists( 'uncode_get_gallery_attachment_ids' ),
            'pdf_thumbnails'        => class_exists( 'PIGEN' ),
            'w3tc_cache'            => defined( 'W3TC_VERSION' ),
        );

        // Build exclusion conditions safely using subqueries
        if ( ! empty( $exclusions['elementor_screenshots'] ) ) {
            $conditions[] = "posts.ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_is_screenshot')";
        }
        
        if ( ! empty( $exclusions['picu_collections'] ) ) {
            $conditions[] = "posts.post_parent NOT IN (SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_type = 'picu_collection')";
        }
        
        if ( ! empty( $exclusions['uncode_gallery'] ) && function_exists( 'uncode_get_gallery_attachment_ids' ) ) {
            $attachment_ids = uncode_get_gallery_attachment_ids();
            $sanitized_ids  = array_filter( array_map( 'absint', (array) $attachment_ids ) );
            if ( ! empty( $sanitized_ids ) ) {
                $media_attachments_ids = implode( ',', $sanitized_ids );
                $conditions[] = "posts.ID NOT IN ($media_attachments_ids)";
            }

        }
        
        if ( ! empty( $exclusions['pdf_thumbnails'] ) ) {
            $opt = get_option( 'pigen_options' );
            if ( isset( $opt['hidethumb'] ) && $opt['hidethumb'] !== '' ) {
                $conditions[] = "posts.ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_thumbnail_id')";
            }
        }
        
        if ( ! empty( $exclusions['w3tc_cache'] ) ) {
            $conditions[] = "posts.ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'w3tc_imageservice_file')";
        }

        if( function_exists( 'fifu_get_author' ) ) {
            $conditions[] = "posts.post_author != " . intval( fifu_get_author() );
        }
        
        return $conditions;
    }
}
