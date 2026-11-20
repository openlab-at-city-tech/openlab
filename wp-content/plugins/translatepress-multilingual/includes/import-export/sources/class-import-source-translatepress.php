<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Imports a .zip made by the TranslatePress exporter.
class TRP_Import_Source_TranslatePress implements TRP_Import_Source_Interface {

    const UPLOAD_NAME = 'import.zip';
    // Without a row count in the manifest, only files up to this size are counted line by line.
    const MAX_COUNT_LINES_SIZE = 20971520;

    protected $archive;
    protected $settings;

    public function __construct() {
        $this->archive = new TRP_Import_Export_Archive();
    }

    public function get_slug() {
        return 'translatepress';
    }

    public function get_name() {
        return __( 'TranslatePress', 'translatepress-multilingual' );
    }

    public function is_available() {
        return true;
    }

    protected function get_settings() {
        if ( ! $this->settings ) {
            $this->settings = TRP_Translate_Press::get_trp_instance()->get_component( 'settings' )->get_settings();
        }
        return $this->settings;
    }

    public function prepare( $job, $request, $files ) {
        $upload = $this->validate_upload( $files );
        if ( is_wp_error( $upload ) ) {
            return $upload;
        }

        $destination = $job->dir . self::UPLOAD_NAME;
        if ( ! move_uploaded_file( $upload['tmp_name'], $destination ) ) {
            return new WP_Error( 'trp_ie_upload_move', __( 'The file could not be saved on the server. Check that the uploads folder is writable.', 'translatepress-multilingual' ) );
        }
        @chmod( $destination, 0644 );//phpcs:ignore

        $entries = $this->archive->validate_zip( $destination );
        if ( is_wp_error( $entries ) ) {
            return $entries;
        }
        $extracted = $this->archive->extract( $destination, $job->dir, $entries );
        if ( is_wp_error( $extracted ) ) {
            return $extracted;
        }
        @unlink( $destination );//phpcs:ignore
        $job->protect_dir();

        $manifest = $this->archive->read_manifest( $job->dir );
        if ( is_wp_error( $manifest ) ) {
            return $manifest;
        }

        // A paid site's settings and add-on data need the paid version here too.
        $product = isset( $manifest['product'] ) ? $manifest['product'] : '';
        if ( TRP_Import_Export_Writer::is_paid_product( $product ) && ! TRP_Import_Export_Writer::is_paid_product( TRP_Import_Export_Writer::get_site_product() ) ) {
            return new WP_Error(
                'trp_ie_needs_paid',
                __( 'This file was exported from a paid version of TranslatePress, and this site runs the free version. The free version:', 'translatepress-multilingual' ),
                array(
                    'title' => __( 'This file needs a paid version of TranslatePress', 'translatepress-multilingual' ),
                    'list'  => array(
                        __( 'supports only two languages', 'translatepress-multilingual' ),
                        __( 'has no add-ons', 'translatepress-multilingual' ),
                        __( 'can\'t translate URL slugs', 'translatepress-multilingual' ),
                    ),
                    'after' => __( 'Install and activate the paid TranslatePress plugin from your account at translatepress.com, then upload the file again.', 'translatepress-multilingual' ),
                )
            );
        }

        $archive_files     = $this->get_archive_files( $job, $manifest );
        $settings_included = file_exists( $job->dir . TRP_Import_Export_Archive::SETTINGS );
        if ( empty( $archive_files ) && ! $settings_included ) {
            return new WP_Error( 'trp_ie_nothing', __( 'This file has nothing that can be imported.', 'translatepress-multilingual' ) );
        }

        $site            = $this->get_settings();
        $site_languages  = (array) $site['translation-languages'];
        $default         = $manifest['default_language'];
        $bundle          = $settings_included ? $this->read_settings_bundle( $job ) : array();
        $bundle_settings = ( is_array( $bundle ) && isset( $bundle['trp_settings'] ) && is_array( $bundle['trp_settings'] ) ) ? $bundle['trp_settings'] : array();
        $max_length      = TRP_Import_Export_Writer::MAX_LANGUAGE_CODE_LENGTH;

        // Codes longer than the writer accepts are never imported, so they are not shown either.
        $settings_languages = array();
        if ( isset( $bundle_settings['translation-languages'] ) && is_array( $bundle_settings['translation-languages'] ) ) {
            foreach ( $bundle_settings['translation-languages'] as $code ) {
                if ( is_string( $code ) && strlen( $code ) <= $max_length && trp_is_valid_language_code( $code ) ) {
                    $settings_languages[] = $code;
                }
            }
        }
        $settings_default = ( isset( $bundle_settings['default-language'] ) && is_string( $bundle_settings['default-language'] ) ) ? substr( $bundle_settings['default-language'], 0, $max_length ) : $site['default-language'];

        // Row counts per language, including languages that only have translated slugs.
        $counts = array();
        foreach ( $archive_files as $entry => $rows ) {
            $code = TRP_Import_Export_Archive::get_language_from_entry_name( $entry );
            if ( $code !== '' ) {
                $counts[ $code ][ strpos( $entry, 'dictionary/' ) === 0 ? 'dictionary_rows' : 'gettext_rows' ] = $rows;
            }
        }
        $slug_counts = $this->count_slug_translations( $job, $archive_files );
        foreach ( $slug_counts as $code => $count ) {
            $counts[ $code ]['slug_rows'] = $count;
        }

        $names = TRP_Import_Export_Writer::language_labels(
            array_merge( array_keys( $counts ), $site_languages, $settings_languages, array( $site['default-language'], $default, $settings_default ) ),
            $this->get_custom_language_names( $bundle )
        );

        $languages = array();
        foreach ( $counts as $code => $rows ) {
            $languages[] = array_merge(
                array( 'dictionary_rows' => 0, 'gettext_rows' => 0, 'slug_rows' => 0 ),
                $rows,
                array(
                    'code'       => $code,
                    'name'       => $names[ $code ],
                    'is_default' => ( $code === $default ),
                    'on_site'    => in_array( $code, $site_languages, true ),
                )
            );
        }
        // The file's default language first, then by name.
        usort( $languages, function ( $a, $b ) {
            return ( $a['is_default'] !== $b['is_default'] ) ? (int) $b['is_default'] - (int) $a['is_default'] : strcasecmp( $a['name'], $b['name'] );
        } );

        $archive = array(
            'files'              => $archive_files,
            'default'            => $default,
            'slug_languages'     => array_keys( $slug_counts ),
            'names'              => $names,
            'settings_included'  => $settings_included,
            'settings_languages' => $settings_languages,
            'settings_default'   => $settings_default,
            'add_ons'            => TRP_Import_Export_Writer::get_add_on_plan( ( is_array( $bundle ) && isset( $bundle['trp_add_ons_settings'] ) ) ? $bundle['trp_add_ons_settings'] : array() ),
        );
        $exported = isset( $manifest['exported_at'] ) ? strtotime( (string) $manifest['exported_at'] ) : false;

        $job->preview = array(
            'file_name'             => sanitize_file_name( wp_unslash( $upload['name'] ) ),
            'site_url'              => isset( $manifest['site_url'] ) ? (string) $manifest['site_url'] : '',
            'exported_at'           => $exported ? wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $exported ) : '',
            'default_language'      => $default,
            'default_language_name' => $names[ $default ],
            'languages'             => $languages,
            'settings_included'     => $settings_included,
            // key => label, for the keys the file holds.
            'keys'                  => TRP_Import_Export_Writer::get_key_labels( ( is_array( $bundle ) && isset( $bundle['keys'] ) ) ? $bundle['keys'] : array() ),
            'variants'              => array( 'without' => $this->plan( $archive, false ) ),
        );
        if ( $settings_included ) {
            $job->preview['variants']['with'] = $this->plan( $archive, true );
        }
        $job->cursor  = array();
        $job->options = array();

        return true;
    }

    // code => name for the custom languages in the file's advanced settings.
    protected function get_custom_language_names( $bundle ) {
        $custom = ( is_array( $bundle ) && isset( $bundle['trp_advanced_settings']['custom_language'] ) ) ? $bundle['trp_advanced_settings']['custom_language'] : array();
        $names  = array();
        if ( ! is_array( $custom ) || ! isset( $custom['cuslangcode'], $custom['cuslangname'] ) || ! is_array( $custom['cuslangcode'] ) || ! is_array( $custom['cuslangname'] ) ) {
            return $names;
        }
        foreach ( $custom['cuslangcode'] as $index => $code ) {
            $name = ( isset( $custom['cuslangname'][ $index ] ) && is_string( $custom['cuslangname'][ $index ] ) ) ? sanitize_text_field( $custom['cuslangname'][ $index ] ) : '';
            if ( is_string( $code ) && trp_is_valid_language_code( $code ) && $name !== '' ) {
                $names[ $code ] = $name;
            }
        }
        return $names;
    }

    protected function validate_upload( $files ) {
        if ( empty( $files['import_file'] ) || ! is_array( $files['import_file'] ) ) {
            return new WP_Error( 'trp_ie_upload_missing', __( 'No file was uploaded.', 'translatepress-multilingual' ) );
        }
        $file = $files['import_file'];
        /* translators: %s is a file size, e.g. 64 MB. */
        $too_large = sprintf( __( 'The uploaded file is larger than the maximum upload size allowed on this server (%s).', 'translatepress-multilingual' ), size_format( wp_max_upload_size() ) );

        if ( ! isset( $file['error'] ) || (int) $file['error'] !== UPLOAD_ERR_OK ) {
            if ( isset( $file['error'] ) && in_array( (int) $file['error'], array( UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ), true ) ) {
                return new WP_Error( 'trp_ie_upload_size', $too_large );
            }
            return new WP_Error( 'trp_ie_upload_error', __( 'The file could not be uploaded. Please try again.', 'translatepress-multilingual' ) );
        }
        if ( empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
            return new WP_Error( 'trp_ie_upload_error', __( 'The file could not be uploaded. Please try again.', 'translatepress-multilingual' ) );
        }
        if ( (int) $file['size'] <= 0 ) {
            return new WP_Error( 'trp_ie_upload_empty', __( 'The uploaded file is empty.', 'translatepress-multilingual' ) );
        }
        if ( (int) $file['size'] > wp_max_upload_size() ) {
            return new WP_Error( 'trp_ie_upload_size', $too_large );
        }

        $name     = isset( $file['name'] ) ? sanitize_file_name( $file['name'] ) : '';
        $checked  = wp_check_filetype_and_ext( $file['tmp_name'], $name, array( 'zip' => 'application/zip' ) );
        $zip_mime = array( 'application/zip', 'application/octet-stream', 'application/x-zip-compressed', 'multipart/x-zip' );
        if ( strtolower( pathinfo( $name, PATHINFO_EXTENSION ) ) !== 'zip' || empty( $checked['type'] ) || ! in_array( $checked['type'], $zip_mime, true ) ) {
            return new WP_Error( 'trp_ie_upload_type', __( 'This isn\'t a .zip file. Choose the .zip file made with Generate export file on the other site.', 'translatepress-multilingual' ) );
        }

        return $file;
    }

    // entry => rows, for the manifest's entries that are on disk.
    protected function get_archive_files( $job, $manifest ) {
        $files = array();
        foreach ( $manifest['files'] as $entry => $info ) {
            if ( ! is_string( $entry ) || ! TRP_Import_Export_Archive::is_allowed_entry_name( $entry ) || ! file_exists( $job->dir . $entry ) ) {
                continue;
            }
            if ( is_array( $info ) && isset( $info['rows'] ) ) {
                $files[ $entry ] = (int) $info['rows'];
                continue;
            }
            $size            = (int) @filesize( $job->dir . $entry );//phpcs:ignore
            $files[ $entry ] = ( $size > 0 && $size <= self::MAX_COUNT_LINES_SIZE ) ? TRP_Import_Export_Archive::count_lines( $job->dir . $entry ) : 0;
        }
        return $files;
    }

    // Language code => translated slugs in slugs.jsonl.
    protected function count_slug_translations( $job, $archive_files ) {
        $path = $job->dir . 'slugs.jsonl';
        if ( ! isset( $archive_files['slugs.jsonl'] ) || (int) @filesize( $path ) > self::MAX_COUNT_LINES_SIZE ) {//phpcs:ignore
            return array();
        }
        $handle = @fopen( $path, 'r' );//phpcs:ignore
        if ( ! $handle ) {
            return array();
        }
        $languages = array();
        while ( ( $line = fgets( $handle ) ) !== false ) {//phpcs:ignore
            $row = json_decode( $line, true );
            if ( ! is_array( $row ) || ! isset( $row['tr'] ) || ! is_array( $row['tr'] ) ) {
                continue;
            }
            foreach ( $row['tr'] as $code => $translated ) {
                $text = ( is_array( $translated ) && isset( $translated['t'] ) ) ? $translated['t'] : $translated;
                if ( is_string( $code ) && trp_is_valid_language_code( $code ) && is_string( $text ) && $text !== '' ) {
                    $languages[ $code ] = isset( $languages[ $code ] ) ? $languages[ $code ] + 1 : 1;
                }
            }
        }
        fclose( $handle );//phpcs:ignore
        return $languages;
    }

    // Steps, skip notes and settings changes for one choice of "Also import the settings"; with it, the file's languages decide, otherwise this site's.
    protected function plan( $archive, $import_settings ) {
        $site      = $this->get_settings();
        $languages = $import_settings ? $archive['settings_languages'] : (array) $site['translation-languages'];
        $default   = $import_settings ? $archive['default'] : $site['default-language'];
        $names     = $archive['names'];
        $files     = $archive['files'];
        $steps     = array();
        $imported  = 0;
        $mismatch  = array();
        $skipped   = array( 'dictionary' => array(), 'gettext' => array(), 'slugs' => array() );
        $notes     = array();

        if ( $import_settings ) {
            $steps[] = $this->new_step( 'settings', __( 'Importing settings', 'translatepress-multilingual' ), 1, array( 'entry' => TRP_Import_Export_Archive::SETTINGS ) );
            // Its own step, so it runs in a request that booted with the imported languages.
            $steps[] = $this->new_step( 'settings_other', __( 'Importing settings', 'translatepress-multilingual' ), 1, array( 'entry' => TRP_Import_Export_Archive::SETTINGS ) );
        }

        // Page translations only fit a site with the same default language.
        foreach ( $files as $entry => $rows ) {
            if ( strpos( $entry, 'dictionary/' ) !== 0 ) {
                continue;
            }
            $code = TRP_Import_Export_Archive::get_language_from_entry_name( $entry );
            if ( $archive['default'] !== $default ) {
                $mismatch[] = $names[ $code ];
            } elseif ( ! in_array( $code, $languages, true ) ) {
                $skipped['dictionary'][] = $names[ $code ];
            } else {
                $imported++;
                $steps[] = $this->new_step(
                    'dictionary:' . $code,
                    /* translators: %s is a language name. */
                    sprintf( __( 'Importing page translations for %s', 'translatepress-multilingual' ), $names[ $code ] ),
                    $rows,
                    /* translators: %s is a language name. */
                    array( 'entry' => $entry, 'language' => $code, 'report_label' => sprintf( __( '%s: page translations', 'translatepress-multilingual' ), $names[ $code ] ) )
                );
            }
        }

        // Original meta belongs to the page translations: without any, its originals would be added on their own.
        if ( isset( $files['original-meta.jsonl'] ) && $imported > 0 ) {
            $steps[] = $this->new_step( 'original_meta', __( 'Finishing page translations', 'translatepress-multilingual' ), $files['original-meta.jsonl'], array( 'entry' => 'original-meta.jsonl' ) );
        }

        foreach ( $files as $entry => $rows ) {
            if ( strpos( $entry, 'gettext/' ) !== 0 ) {
                continue;
            }
            $code = TRP_Import_Export_Archive::get_language_from_entry_name( $entry );
            if ( ! in_array( $code, $languages, true ) && $code !== $default ) {
                $skipped['gettext'][] = $names[ $code ];
            } else {
                $imported++;
                $steps[] = $this->new_step(
                    'gettext:' . $code,
                    /* translators: %s is a language name. */
                    sprintf( __( 'Importing theme and plugin strings for %s', 'translatepress-multilingual' ), $names[ $code ] ),
                    $rows,
                    /* translators: %s is a language name. */
                    array( 'entry' => $entry, 'language' => $code, 'report_label' => sprintf( __( '%s: theme and plugin strings', 'translatepress-multilingual' ), $names[ $code ] ) )
                );
            }
        }

        if ( isset( $files['gettext-original-meta.jsonl'] ) ) {
            $steps[] = $this->new_step( 'gettext_original_meta', __( 'Finishing theme and plugin strings', 'translatepress-multilingual' ), $files['gettext-original-meta.jsonl'], array( 'entry' => 'gettext-original-meta.jsonl' ) );
        }

        // With the settings, the file's add-ons are active by the time the slugs are imported.
        $seo_pack         = TRP_Import_Export_Writer::SEO_PACK;
        $slugs_supported  = $import_settings
            ? ( in_array( $seo_pack, $archive['add_ons']['activate'], true ) || ( $this->slugs_supported() && ! in_array( $seo_pack, $archive['add_ons']['deactivate'], true ) ) )
            : $this->slugs_supported();
        if ( isset( $files['slugs.jsonl'] ) && ! $slugs_supported ) {
            $notes[] = __( 'Translated slugs: they need the SEO Pack add-on, which isn\'t active on this site.', 'translatepress-multilingual' );
        } elseif ( isset( $files['slugs.jsonl'] ) ) {
            $steps[] = $this->new_step( 'slugs', __( 'Importing translated slugs', 'translatepress-multilingual' ), $files['slugs.jsonl'], array( 'entry' => 'slugs.jsonl', 'report_label' => __( 'Translated slugs', 'translatepress-multilingual' ) ) );
            // Same language filter as TRP_Import_Export_Writer::import_slug_rows().
            foreach ( $archive['slug_languages'] as $code ) {
                if ( in_array( $code, $languages, true ) ) {
                    $imported++;
                } else {
                    $skipped['slugs'][] = $names[ $code ];
                }
            }
        }

        $steps[] = $this->new_step( 'cleanup', __( 'Finishing up', 'translatepress-multilingual' ), 1 );

        if ( $import_settings && $archive['add_ons']['unavailable'] ) {
            /* translators: %s is a list of add-on names. */
            $notes[] = sprintf( __( 'Add-ons that are not part of the TranslatePress version on this site, and their settings: %s.', 'translatepress-multilingual' ), $this->list_add_ons( $archive['add_ons']['unavailable'] ) );
        }
        if ( ! $import_settings && $imported === 0 ) {
            $notes[] = __( 'Nothing in this file can be imported with these options.', 'translatepress-multilingual' );
        }
        if ( $mismatch ) {
            natcasesort( $mismatch );
            /* translators: 1: language names, 2: a language name, 3: a language name. */
            $notes[] = sprintf( __( 'Page translations for %1$s: made from %2$s, but this site\'s default language is %3$s.', 'translatepress-multilingual' ), implode( ', ', $mismatch ), $names[ $archive['default'] ], $names[ $default ] );
            if ( $archive['settings_included'] ) {
                $notes[] = $this->get_import_settings_hint();
            }
        }
        $parts = array(
            'dictionary' => __( 'Page translations', 'translatepress-multilingual' ),
            'gettext'    => __( 'Theme and plugin strings', 'translatepress-multilingual' ),
            'slugs'      => __( 'Translated slugs', 'translatepress-multilingual' ),
        );
        foreach ( $skipped as $part => $list ) {
            if ( $list ) {
                natcasesort( $list );
                $notes[] = sprintf(
                    $import_settings
                        /* translators: 1: a kind of translation, e.g. "Page translations", 2: language names. */
                        ? __( '%1$s for %2$s: language not in the imported settings.', 'translatepress-multilingual' )
                        /* translators: 1: a kind of translation, e.g. "Page translations", 2: language names. */
                        : __( '%1$s for %2$s: language not on this site.', 'translatepress-multilingual' ),
                    $parts[ $part ],
                    implode( ', ', $list )
                );
            }
        }

        return array(
            'steps'   => $steps,
            'notes'   => $notes,
            'changes' => $import_settings ? $this->get_settings_changes( $archive ) : array(),
        );
    }

    // How importing the file's settings changes this site's languages, one line per change.
    protected function get_settings_changes( $archive ) {
        $site    = $this->get_settings();
        $current = (array) $site['translation-languages'];
        $names   = $archive['names'];
        $list    = function ( $codes ) use ( $names ) {
            $list = array_intersect_key( $names, array_flip( $codes ) );
            natcasesort( $list );
            return implode( ', ', $list );
        };
        $changes = array();
        if ( ! $archive['settings_languages'] ) {
            return $changes;
        }
        if ( $archive['add_ons']['activate'] ) {
            /* translators: %s is a list of add-on names. */
            $changes[] = sprintf( __( 'Add-ons activated: %s', 'translatepress-multilingual' ), $this->list_add_ons( $archive['add_ons']['activate'] ) );
        }
        if ( $archive['add_ons']['deactivate'] ) {
            /* translators: %s is a list of add-on names. */
            $changes[] = sprintf( __( 'Add-ons deactivated: %s', 'translatepress-multilingual' ), $this->list_add_ons( $archive['add_ons']['deactivate'] ) );
        }
        if ( $archive['settings_default'] !== $site['default-language'] ) {
            /* translators: 1: a language name, 2: a language name. */
            $changes[] = sprintf( __( 'Default language: %1$s → %2$s', 'translatepress-multilingual' ), $names[ $site['default-language'] ], $names[ $archive['settings_default'] ] );
        }
        $added = array_diff( $archive['settings_languages'], $current );
        if ( $added ) {
            /* translators: %s is a list of language names. */
            $changes[] = sprintf( __( 'Languages added: %s', 'translatepress-multilingual' ), $list( $added ) );
        }
        $removed = array_diff( $current, $archive['settings_languages'] );
        if ( $removed ) {
            /* translators: %s is a list of language names. */
            $changes[] = sprintf( __( 'Languages removed: %s', 'translatepress-multilingual' ), $list( $removed ) );
        }
        return $changes;
    }

    protected function list_add_ons( $slugs ) {
        return implode( ', ', array_intersect_key( TRP_Import_Export_Writer::get_add_on_names(), array_flip( $slugs ) ) );
    }

    protected function get_import_settings_hint() {
        return __( 'To import them, also import the settings. Check what that changes on this site first.', 'translatepress-multilingual' );
    }

    protected function slugs_supported() {
        global $wpdb;
        return class_exists( 'TRP_Slug_Query' ) && TRP_Translate_Press::get_trp_instance()->get_component( 'query' )->table_exists( $wpdb->prefix . 'trp_slug_originals', true );
    }

    // The steps of the variant the review showed for this choice, plus the keys step when the user chose to import them.
    public function build_steps( $job, $options ) {
        $variants = $job->preview['variants'];
        $steps    = ( $options['import_settings'] === 'yes' && isset( $variants['with'] ) ) ? $variants['with']['steps'] : $variants['without']['steps'];
        if ( isset( $options['import_keys'] ) && $options['import_keys'] === 'yes' ) {
            // After the settings steps, so the add-ons the engine may need are loaded.
            $position = 0;
            foreach ( $steps as $index => $step ) {
                if ( strpos( $step['key'], 'settings' ) === 0 ) {
                    $position = $index + 1;
                }
            }
            array_splice( $steps, $position, 0, array( $this->new_step( 'keys', __( 'Importing the keys', 'translatepress-multilingual' ), 1, array( 'entry' => TRP_Import_Export_Archive::SETTINGS ) ) ) );
        }
        return $steps;
    }

    protected function read_settings_bundle( $job ) {
        $path = $job->dir . TRP_Import_Export_Archive::SETTINGS;
        if ( ! file_exists( $path ) ) {
            return new WP_Error( 'trp_ie_settings_missing', __( 'This file has no settings to import.', 'translatepress-multilingual' ) );
        }
        return $this->archive->read_json_file( $path );
    }

    protected function new_step( $key, $label, $total, $extra = array() ) {
        return array_merge(
            array(
                'key'       => $key,
                'label'     => $label,
                'total'     => (int) $total,
                'processed' => 0,
                'done'      => false,
            ),
            array_fill_keys( TRP_Import_Export_Writer::COUNT_KEYS, 0 ),
            $extra
        );
    }

    public function process_step( $job, $writer, $time_budget ) {
        $step = $job->get_current_step();
        if ( $step['key'] === 'settings' ) {
            return $this->process_settings_step( $job, $writer );
        }
        if ( $step['key'] === 'keys' ) {
            $bundle = $this->read_settings_bundle( $job );
            if ( is_wp_error( $bundle ) ) {
                return $bundle;
            }
            $result = $writer->import_keys( isset( $bundle['keys'] ) ? $bundle['keys'] : array(), isset( $bundle['trp_machine_translation_settings'] ) ? $bundle['trp_machine_translation_settings'] : array() );
            $job->summary['key_changes'] = $result['changes'];
            $job->update_current_step( array( 'processed' => 1, 'done' => true ) );
            return array( 'done' => true, 'warnings' => $result['warnings'] );
        }
        if ( $step['key'] === 'settings_other' ) {
            $bundle = $this->read_settings_bundle( $job );
            if ( is_wp_error( $bundle ) ) {
                return $bundle;
            }
            $warnings = $writer->import_other_settings( $bundle );
            $job->update_current_step( array( 'processed' => 1, 'done' => true ) );
            return array( 'done' => true, 'warnings' => $warnings );
        }
        if ( $step['key'] === 'cleanup' ) {
            return $this->process_cleanup_step( $job );
        }

        $start    = microtime( true );
        $warnings = array();
        do {
            $batch = $this->process_rows_batch( $job, $writer, $job->get_current_step() );
            if ( is_wp_error( $batch ) ) {
                return $batch;
            }
            $warnings = array_merge( $warnings, $batch['warnings'] );
            $job->save();
        } while ( ! $batch['done'] && ( microtime( true ) - $start ) < $time_budget );

        return array( 'done' => $batch['done'], 'warnings' => array_values( array_unique( $warnings ) ) );
    }

    // One batch of JSONL rows through the writer; the counts are added to the step.
    protected function process_rows_batch( $job, $writer, $step ) {
        $key    = $step['key'];
        $offset = isset( $job->cursor['line_offset'] ) ? (int) $job->cursor['line_offset'] : 0;
        $read   = $this->archive->read_jsonl( $job->dir . $step['entry'], $offset, TRP_Import_Export_Exporter::get_batch_size(), TRP_Import_Export_Archive::get_max_batch_bytes() );
        if ( is_wp_error( $read ) ) {
            return $read;
        }

        $rows = $this->map_rows( $read['rows'], $key );
        $mode = $job->options['conflict_mode'];
        if ( strpos( $key, 'dictionary:' ) === 0 ) {
            $counts = $writer->import_dictionary_rows( $step['language'], $rows, $mode );
        } elseif ( strpos( $key, 'gettext:' ) === 0 ) {
            $counts = $writer->import_gettext_rows( $step['language'], $rows, $mode );
        } elseif ( $key === 'original_meta' ) {
            $counts = $writer->import_original_meta_rows( $rows );
        } elseif ( $key === 'gettext_original_meta' ) {
            $counts = $writer->import_gettext_original_meta_rows( $rows );
        } else {
            $counts = $writer->import_slug_rows( $rows, $mode );
        }

        $warnings = $counts['warnings'];
        $label    = isset( $step['report_label'] ) ? $step['report_label'] : $step['label'];
        if ( $read['invalid'] > 0 ) {
            /* translators: 1: number of translations, 2: what was being imported, e.g. "French: page translations". */
            $warnings[] = sprintf( _n( '%2$s: %1$d translation in the file is damaged and was not imported.', '%2$s: %1$d translations in the file are damaged and were not imported.', $read['invalid'], 'translatepress-multilingual' ), $read['invalid'], $label );
            $counts['skipped'] += $read['invalid'];
        }
        if ( $writer->take_write_errors() > 0 ) {
            /* translators: %s is what was being imported, e.g. "French: page translations". */
            $warnings[] = sprintf( __( '%s: a database error stopped some translations from being saved. Import the file again; translations already saved are not duplicated.', 'translatepress-multilingual' ), $label );
        }

        $job->cursor['line_offset'] = $read['offset'];
        $update                     = array( 'processed' => $step['processed'] + $read['read'], 'done' => $read['eof'] );
        foreach ( TRP_Import_Export_Writer::COUNT_KEYS as $count_key ) {
            $update[ $count_key ] = $step[ $count_key ] + $counts[ $count_key ];
        }
        $job->update_current_step( $update );

        return array( 'done' => $read['eof'], 'warnings' => $warnings );
    }

    // The archive's short JSONL keys to the writer's row format.
    protected function map_rows( $rows, $key ) {
        $mapped = array();
        foreach ( $rows as $row ) {
            if ( strpos( $key, 'dictionary:' ) === 0 ) {
                $mapped[] = array(
                    'original'   => isset( $row['o'] ) ? $row['o'] : null,
                    'translated' => isset( $row['t'] ) ? $row['t'] : null,
                    'status'     => isset( $row['s'] ) ? $row['s'] : 0,
                    'block_type' => isset( $row['b'] ) ? $row['b'] : 0,
                );
            } elseif ( strpos( $key, 'gettext:' ) === 0 ) {
                $mapped[] = array(
                    'original'        => isset( $row['o'] ) ? $row['o'] : null,
                    'translated'      => isset( $row['t'] ) ? $row['t'] : null,
                    'domain'          => isset( $row['d'] ) ? $row['d'] : '',
                    'context'         => isset( $row['c'] ) ? $row['c'] : null,
                    'plural_form'     => isset( $row['p'] ) ? $row['p'] : 0,
                    'original_plural' => isset( $row['op'] ) ? $row['op'] : '',
                    'status'          => isset( $row['s'] ) ? $row['s'] : 0,
                );
            } elseif ( $key === 'original_meta' ) {
                $mapped[] = array(
                    'original'   => isset( $row['o'] ) ? $row['o'] : null,
                    'meta_key'   => isset( $row['k'] ) ? $row['k'] : null,
                    'meta_value' => isset( $row['v'] ) ? $row['v'] : '',
                );
            } elseif ( $key === 'gettext_original_meta' ) {
                $mapped[] = array(
                    'original'   => isset( $row['o'] ) ? $row['o'] : null,
                    'domain'     => isset( $row['d'] ) ? $row['d'] : '',
                    'context'    => isset( $row['c'] ) ? $row['c'] : null,
                    'meta_key'   => isset( $row['k'] ) ? $row['k'] : null,
                    'meta_value' => isset( $row['v'] ) ? $row['v'] : '',
                );
            } else {
                $mapped[] = array(
                    'original'     => isset( $row['o'] ) ? $row['o'] : null,
                    'type'         => isset( $row['type'] ) ? $row['type'] : 'other',
                    'translations' => ( isset( $row['tr'] ) && is_array( $row['tr'] ) ) ? $this->map_slug_translations( $row['tr'] ) : array(),
                );
            }
        }
        return $mapped;
    }

    protected function map_slug_translations( $translations ) {
        $mapped = array();
        foreach ( $translations as $language => $translation ) {
            if ( is_array( $translation ) ) {
                $mapped[ $language ] = array(
                    'translated' => isset( $translation['t'] ) ? $translation['t'] : '',
                    'status'     => isset( $translation['s'] ) ? $translation['s'] : TRP_Query::HUMAN_REVIEWED,
                );
            }
        }
        return $mapped;
    }

    protected function process_settings_step( $job, $writer ) {
        $bundle = $this->read_settings_bundle( $job );
        if ( is_wp_error( $bundle ) ) {
            return $bundle;
        }
        $warnings = array();
        if ( ! $writer->import_language_settings( $bundle, $warnings ) ) {
            // Nothing was changed, so the import goes on as planned for this site's own settings.
            $job->options['import_settings'] = 'no';
            $job->steps                      = $this->build_steps( $job, $job->options );
            $job->current                    = 0;
            $job->cursor                     = array();
            // The report must not advise importing the settings that were just rejected.
            $job->preview['variants']['without']['notes'] = array_values( array_diff( $job->preview['variants']['without']['notes'], array( $this->get_import_settings_hint() ) ) );
            return array( 'done' => false, 'warnings' => $warnings );
        }
        // The settings changed, so drop the copy this source works with.
        $this->settings = null;

        // The plan counted on the file's default language; page translations made from another one must not be written.
        $site    = get_option( 'trp_settings', array() );
        $default = isset( $site['default-language'] ) ? $site['default-language'] : '';
        if ( $default !== $job->preview['default_language'] ) {
            $job->steps = array_values( array_filter( $job->steps, function ( $step ) {
                return strpos( $step['key'], 'dictionary:' ) !== 0 && $step['key'] !== 'original_meta';
            } ) );
            $names      = TRP_Import_Export_Writer::language_labels( array( $default ) );
            /* translators: 1: a language name, 2: a language name. */
            $warnings[] = sprintf( __( 'Page translations were not imported: they were made from %1$s, but the default language in the imported settings is %2$s.', 'translatepress-multilingual' ), $job->preview['default_language_name'], isset( $names[ $default ] ) ? $names[ $default ] : $default );
        }

        $job->update_current_step( array( 'processed' => 1, 'done' => true ) );
        return array( 'done' => true, 'warnings' => $warnings );
    }

    // The extracted files are not needed once the import is done.
    protected function process_cleanup_step( $job ) {
        foreach ( array( 'dictionary', 'gettext' ) as $sub ) {
            TRP_Import_Export_Job::delete_dir( $job->dir . $sub );
        }
        foreach ( array( self::UPLOAD_NAME, TRP_Import_Export_Archive::MANIFEST, TRP_Import_Export_Archive::SETTINGS, TRP_Import_Export_Archive::README, 'original-meta.jsonl', 'gettext-original-meta.jsonl', 'slugs.jsonl' ) as $name ) {
            if ( file_exists( $job->dir . $name ) ) {
                @unlink( $job->dir . $name );//phpcs:ignore
            }
        }
        // Permalinks only change when the settings were imported.
        if ( $job->options['import_settings'] === 'yes' ) {
            flush_rewrite_rules();
        }
        // The tables just grew, so the export card counts them again.
        TRP_Import_Export_Exporter::flush_row_counts();

        $job->update_current_step( array( 'processed' => 1, 'done' => true ) );
        return array( 'done' => true, 'warnings' => array() );
    }
}
