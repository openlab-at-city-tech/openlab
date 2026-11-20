<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// The write API into the TranslatePress tables and options; every import source writes only through it.
class TRP_Import_Export_Writer {

    const MODE_KEEP      = 'keep';
    const MODE_OVERWRITE = 'overwrite';

    // What happened to each row; a row repeated within one batch counts as unchanged and only its last copy is written.
    const COUNT_KEYS = array( 'inserted', 'updated', 'unchanged', 'kept', 'skipped' );

    // Option keys matching this are secrets and are not exported.
    const SECRET_KEY_PATTERN = '/api[-_]?key|license|secret|token/i';

    // The only machine translation settings an import may change.
    const PORTABLE_MT_KEYS = array( 'automatically-translate-slug', 'machine_translation_log' );

    // The automatic translation settings that come with the keys, when the user chooses to import them.
    const KEY_MT_SETTINGS = array( 'machine-translation', 'translation-engine', 'deepl-api-type' );
    const API_KEYS        = array( 'google-translate-key', 'deepl-api-key' );
    const LICENSE_KEY     = 'trp_license_key';

    // Add-on slug => main file inside the paid plugin. Multiple Domains is left out: its settings are site specific and never imported.
    const ADD_ONS = array(
        'tp-add-on-seo-pack/tp-seo-pack.php'                                         => 'add-ons-advanced/seo-pack/tp-seo-pack.php',
        'tp-add-on-extra-languages/tp-extra-languages.php'                           => 'add-ons-advanced/extra-languages/tp-extra-languages.php',
        'tp-add-on-deepl/index.php'                                                  => 'add-ons-pro/deepl/index.php',
        'tp-add-on-automatic-language-detection/tp-automatic-language-detection.php' => 'add-ons-pro/automatic-language-detection/tp-automatic-language-detection.php',
        'tp-add-on-translator-accounts/index.php'                                    => 'add-ons-pro/translator-accounts/index.php',
        'tp-add-on-browse-as-other-roles/tp-browse-as-other-role.php'                => 'add-ons-pro/browse-as-other-roles/tp-browse-as-other-role.php',
        'tp-add-on-navigation-based-on-language/tp-navigation-based-on-language.php' => 'add-ons-pro/navigation-based-on-language/tp-navigation-based-on-language.php',
    );
    const SEO_PACK = 'tp-add-on-seo-pack/tp-seo-pack.php';

    const MAX_LANGUAGES            = 100;
    const MAX_LANGUAGE_CODE_LENGTH = 20;
    const MAX_PLURAL_FORM          = 9;

    const SETTINGS_TEXT_KEYS = array( 'native_or_english_name', 'add-subdirectory-to-default-language', 'force-language-to-custom-links',
        'trp-ls-floater', 'shortcode-options', 'menu-options', 'floater-options', 'floater-position', 'floater-color', 'trp-ls-show-poweredby' );

    protected $settings;
    protected $db;
    protected $trp_query;
    protected $gettext_insert_update;
    protected $write_errors = 0;

    public function __construct( $settings ) {
        global $wpdb;
        $this->settings              = $settings;
        $this->db                    = $wpdb;
        $this->trp_query             = TRP_Translate_Press::get_trp_instance()->get_component( 'query' );
        $this->gettext_insert_update = $this->trp_query->get_query_component( 'gettext_insert_update' );
    }

    public function slug_tables_exist() {
        return $this->trp_query->table_exists( $this->db->prefix . 'trp_slug_originals', true )
            && $this->trp_query->table_exists( $this->db->prefix . 'trp_slug_translations', true );
    }

    // Always with the region ("French (France)"), so the page names a language the same way everywhere; the code is added only when two names collide.
    public static function language_labels( $codes, $file_names = array() ) {
        $codes = array_values( array_unique( array_filter( (array) $codes, 'is_string' ) ) );
        // Unfiltered list: the trp_language_name filter drops the region unless two variants are listed together.
        $names = TRP_Translate_Press::get_trp_instance()->get_component( 'languages' )->get_languages( 'english_name' );
        // Custom languages the file defines and this site does not know yet.
        $names += $file_names;

        $labels = array();
        foreach ( $codes as $code ) {
            $labels[ $code ] = isset( $names[ $code ] ) ? $names[ $code ] : $code;
        }
        $count = array_count_values( $labels );
        foreach ( $labels as $code => $label ) {
            if ( $count[ $label ] > 1 && $label !== $code ) {
                $labels[ $code ] = $label . ' (' . $code . ')';
            }
        }
        return $labels;
    }

    // The TRANSLATE_PRESS value of this site; the free version does not define it.
    public static function get_site_product() {
        return defined( 'TRANSLATE_PRESS' ) ? (string) TRANSLATE_PRESS : 'TranslatePress';
    }

    public static function is_paid_product( $product ) {
        return is_string( $product ) && $product !== '' && $product !== 'TranslatePress';
    }

    public static function get_add_on_names() {
        return array(
            'tp-add-on-seo-pack/tp-seo-pack.php'                                         => __( 'SEO Pack', 'translatepress-multilingual' ),
            'tp-add-on-extra-languages/tp-extra-languages.php'                           => __( 'Multiple Languages', 'translatepress-multilingual' ),
            'tp-add-on-deepl/index.php'                                                  => __( 'DeepL Automatic Translation', 'translatepress-multilingual' ),
            'tp-add-on-automatic-language-detection/tp-automatic-language-detection.php' => __( 'Automatic User Language Detection', 'translatepress-multilingual' ),
            'tp-add-on-translator-accounts/index.php'                                    => __( 'Translator Accounts', 'translatepress-multilingual' ),
            'tp-add-on-browse-as-other-roles/tp-browse-as-other-role.php'                => __( 'Browse As User Role', 'translatepress-multilingual' ),
            'tp-add-on-navigation-based-on-language/tp-navigation-based-on-language.php' => __( 'Navigation Based on Language', 'translatepress-multilingual' ),
        );
    }

    // What mirroring the file's add-ons does on this site: slugs to activate, to deactivate, and the ones this site's version does not have.
    public static function get_add_on_plan( $incoming ) {
        $plan = array( 'activate' => array(), 'deactivate' => array(), 'unavailable' => array() );
        if ( ! is_array( $incoming ) || ! $incoming ) {
            return $plan;
        }
        // The add-ons live in the paid plugin's folder, next to the class that loads them.
        $dir     = class_exists( 'TRP_Handle_Included_Addons' ) ? trailingslashit( dirname( ( new ReflectionClass( 'TRP_Handle_Included_Addons' ) )->getFileName() ) ) : '';
        $current = get_option( 'trp_add_ons_settings', array() );
        $current = is_array( $current ) ? $current : array();
        foreach ( self::ADD_ONS as $slug => $path ) {
            $wanted    = ! empty( $incoming[ $slug ] );
            $active    = ! empty( $current[ $slug ] );
            $available = $dir !== '' && file_exists( $dir . $path );
            if ( ! $available ) {
                if ( $wanted ) {
                    $plan['unavailable'][] = $slug;
                }
                continue;
            }
            if ( $wanted !== $active ) {
                $plan[ $wanted ? 'activate' : 'deactivate' ][] = $slug;
            }
        }
        return $plan;
    }

    // Through the Add-ons page's actions, so each add-on's activation code runs (SEO Pack creates its tables).
    public function import_add_ons( $incoming ) {
        $plan = self::get_add_on_plan( $incoming );
        foreach ( $plan['activate'] as $slug ) {
            do_action( 'trp_add_ons_activate', $slug );
        }
        foreach ( $plan['deactivate'] as $slug ) {
            do_action( 'trp_add_ons_deactivate', $slug );
        }
    }

    // Which keys a file's 'keys' section holds, as labels for the review.
    public static function get_key_labels( $keys ) {
        $labels = array(
            self::LICENSE_KEY      => __( 'TranslatePress license key', 'translatepress-multilingual' ),
            'google-translate-key' => __( 'Google Translate API key', 'translatepress-multilingual' ),
            'deepl-api-key'        => __( 'DeepL API key', 'translatepress-multilingual' ),
        );
        $found = array();
        foreach ( $labels as $key => $label ) {
            if ( is_array( $keys ) && isset( $keys[ $key ] ) && is_string( $keys[ $key ] ) && $keys[ $key ] !== '' && strlen( $keys[ $key ] ) <= 255 ) {
                $found[ $key ] = $label;
            }
        }
        return $found;
    }

    // The same labels as one sentence part: "A, B, and C".
    public static function get_key_list( $keys ) {
        return wp_sprintf( '%l', array_values( self::get_key_labels( $keys ) ) );
    }

    // The license key (activated for this site), the API keys, and with them whether automatic translation is on and its engine.
    // Returns array( 'changes' => lines for the report, 'warnings' => array() ).
    public function import_keys( $keys, $mt_settings ) {
        $changes  = array();
        $warnings = array();
        $found    = self::get_key_labels( $keys );

        if ( isset( $found[ self::LICENSE_KEY ] ) ) {
            $updater = TRP_Translate_Press::get_trp_instance()->get_component( 'plugin_updater' );
            $license = $updater->edd_sanitize_tp_api_key( trim( $keys[ self::LICENSE_KEY ] ) );
            update_option( self::LICENSE_KEY, $license );
            $updater->force_check_tp_api_key( 'true' );
            if ( get_option( 'trp_license_status' ) === 'valid' ) {
                trp_mtapi_sync_license_call( $license );
                $changes[] = __( 'License key: imported and activated for this site.', 'translatepress-multilingual' );
            } else {
                /* translators: %s is the reason, a short sentence. */
                $warnings[] = sprintf( __( 'The license key was imported, but it could not be activated for this site. %s Check it in Settings → TranslatePress → License.', 'translatepress-multilingual' ), $this->get_license_error() );
            }
        }

        $merged = get_option( 'trp_machine_translation_settings', array() );
        $merged = is_array( $merged ) ? $merged : array();
        foreach ( self::API_KEYS as $key ) {
            if ( isset( $found[ $key ] ) ) {
                $merged[ $key ] = sanitize_text_field( $keys[ $key ] );
                /* translators: %s is the name of a key, e.g. "DeepL API key". */
                $changes[]      = sprintf( __( '%s: imported.', 'translatepress-multilingual' ), $found[ $key ] );
            }
        }

        $engine = ( is_array( $mt_settings ) && isset( $mt_settings['translation-engine'] ) && is_string( $mt_settings['translation-engine'] ) ) ? sanitize_text_field( $mt_settings['translation-engine'] ) : '';
        if ( $engine !== '' && $this->is_engine_usable( $engine, $merged ) ) {
            // Only the values the settings page can save.
            $allowed = array( 'machine-translation' => array( 'yes', 'no' ), 'translation-engine' => array( $engine ), 'deepl-api-type' => array( 'free', 'pro' ) );
            foreach ( self::KEY_MT_SETTINGS as $key ) {
                if ( isset( $mt_settings[ $key ] ) && in_array( $mt_settings[ $key ], $allowed[ $key ], true ) ) {
                    $merged[ $key ] = $mt_settings[ $key ];
                }
            }
            $changes[] = ( isset( $merged['machine-translation'] ) && $merged['machine-translation'] === 'yes' )
                ? __( 'Automatic translation: on, with the engine of the other site.', 'translatepress-multilingual' )
                : __( 'Automatic translation: off, as on the other site.', 'translatepress-multilingual' );
        } elseif ( $engine !== '' ) {
            $warnings[] = __( 'The automatic translation engine of the other site can\'t be used on this site yet, so automatic translation was left as it is. Check it in Settings → TranslatePress → Automatic Translation.', 'translatepress-multilingual' );
        }
        update_option( 'trp_machine_translation_settings', $merged );

        return array( 'changes' => $changes, 'warnings' => $warnings );
    }

    // An engine needs its add-on, its key or a valid license on this site.
    protected function is_engine_usable( $engine, $mt_settings ) {
        if ( $engine === 'mtapi' ) {
            return get_option( 'trp_license_status' ) === 'valid';
        }
        if ( $engine === 'deepl' ) {
            return ( class_exists( 'TRP_IN_DeepL' ) || class_exists( 'TRP_DeepL' ) ) && ! empty( $mt_settings['deepl-api-key'] );
        }
        if ( $engine === 'google_translate_v2' ) {
            return ! empty( $mt_settings['google-translate-key'] );
        }
        return false;
    }

    protected function get_license_error() {
        $details = get_option( 'trp_license_details' );
        $error   = ( is_array( $details ) && ! empty( $details['invalid'][0]->error ) ) ? $details['invalid'][0]->error : '';
        $reasons = array(
            'expired'             => __( 'The license has expired.', 'translatepress-multilingual' ),
            'revoked'             => __( 'The license has been disabled.', 'translatepress-multilingual' ),
            'site_inactive'       => __( 'The license is disabled for this site\'s address.', 'translatepress-multilingual' ),
            'item_name_mismatch'  => __( 'The license does not match the TranslatePress version installed on this site.', 'translatepress-multilingual' ),
            'no_activations_left' => __( 'The license has reached its activation limit.', 'translatepress-multilingual' ),
        );
        return isset( $reasons[ $error ] ) ? $reasons[ $error ] : __( 'The license server did not accept it.', 'translatepress-multilingual' );
    }

    protected function empty_counts() {
        return array_fill_keys( self::COUNT_KEYS, 0 ) + array( 'warnings' => array() );
    }

    // Rows of one bulk write: their planned outcome, or skipped when the query failed.
    protected function count_updates( &$counts, $rows, $success ) {
        foreach ( $rows as $row ) {
            $counts[ $success ? $row['outcome'] : 'skipped' ]++;
        }
    }

    // inserted and updated mean "write it"; unchanged and kept mean "leave it".
    protected function resolve_conflict( $mode, $current_translated, $current_text, $current_status, $incoming_text, $incoming_status ) {
        if ( $current_translated && (string) $current_text === (string) $incoming_text && (int) $current_status === (int) $incoming_status ) {
            return 'unchanged';
        }
        // The row exists but holds no translation yet: for the user this is a new translation.
        if ( ! $current_translated ) {
            return 'inserted';
        }
        if ( $mode === self::MODE_OVERWRITE ) {
            return 'updated';
        }
        // Keep mode still lets a reviewed translation replace a machine one.
        if ( (int) $incoming_status === TRP_Query::HUMAN_REVIEWED && (int) $current_status !== TRP_Query::HUMAN_REVIEWED ) {
            return 'updated';
        }
        return 'kept';
    }

    protected function is_translated( $translated, $status ) {
        return ( $translated !== null && $translated !== '' && (int) $status !== TRP_Query::NOT_TRANSLATED );
    }

    // As TRP_Editor_Api_Regular_Strings::save_translations_of_strings() does, after create_translation_block() for blocks.
    // The original is the key a row is found by, so when sanitizing would only trim it, it stays exactly as stored.
    protected function sanitize_original( $original ) {
        return $this->keep_whitespace( $original, trp_sanitize_string( $original, false ) );
    }

    // Same for a translation, so a site's own export imports back without changing a row.
    protected function sanitize_translation( $translated ) {
        return $this->keep_whitespace( $translated, trp_sanitize_string( $translated ) );
    }

    protected function keep_whitespace( $raw, $clean ) {
        return ( $clean === trim( $raw ) ) ? $raw : $clean;
    }

    protected function sanitize_dictionary_row( $original, $translated, $block_type ) {
        if ( $block_type !== 0 ) {
            $original   = trp_full_trim( trp_sanitize_string( $original, false ) );
            $translated = trp_sanitize_string( $translated );
        } else {
            $original = $this->sanitize_original( $original );
        }
        if ( filter_var( $translated, FILTER_VALIDATE_URL ) ) {
            $translated = esc_url_raw( $translated );
        } else {
            $translated = $this->sanitize_translation( $translated );
        }
        return array( $original, $translated );
    }

    // As TRP_Editor_Api_Gettext_Strings::gettext_save_translations() does.
    protected function sanitize_gettext_row( $original, $translated, $domain, $context ) {
        return array( $this->sanitize_original( $original ), $this->sanitize_translation( $translated ), sanitize_text_field( $domain ), $context );
    }

    // Counts a failed write; the source turns it into a warning for that batch.
    protected function track_write( $result ) {
        if ( $result === false ) {
            $this->write_errors++;
        }
        return $result;
    }

    // Failed writes since the last call.
    public function take_write_errors() {
        $errors             = $this->write_errors;
        $this->write_errors = 0;
        return $errors;
    }

    protected function get_current_default_language() {
        $settings = get_option( 'trp_settings' );
        return $settings['default-language'];
    }

    protected function get_current_translation_languages() {
        $settings = get_option( 'trp_settings' );
        return (array) $settings['translation-languages'];
    }

    // One VALUES tuple from array( 'type' => '%s'|'%d', 'value' => mixed ) columns; a null value becomes SQL NULL.
    protected function prepare_tuple( $columns ) {
        $placeholders = array();
        $values       = array();
        foreach ( $columns as $column ) {
            if ( $column['value'] === null ) {
                $placeholders[] = 'NULL';
                continue;
            }
            $placeholders[] = $column['type'];
            $values[]       = $column['value'];
        }
        $sql = '( ' . implode( ', ', $placeholders ) . ' )';
        return empty( $values ) ? $sql : $this->db->prepare( $sql, $values );//phpcs:ignore
    }

    // Rows: array( 'original', 'translated', 'status', 'block_type' ). A row is identified by (original, block_type).
    public function import_dictionary_rows( $language_code, $rows, $mode ) {
        $counts = $this->empty_counts();
        if ( empty( $rows ) ) {
            return $counts;
        }
        if ( ! trp_is_valid_language_code( $language_code ) ) {
            $counts['skipped'] += count( $rows );
            /* translators: %s is a language code. */
            $counts['warnings'][] = sprintf( __( 'Page translations for %s were not imported: it is not a valid language code.', 'translatepress-multilingual' ), $language_code );
            return $counts;
        }
        $table = $this->trp_query->get_table_name( $language_code, $this->get_current_default_language() );
        if ( ! $this->trp_query->table_exists( $table, true ) ) {
            $counts['skipped'] += count( $rows );
            /* translators: %s is a language name. */
            $counts['warnings'][] = sprintf( __( 'Page translations for %s were not imported: the language is not set up on this site.', 'translatepress-multilingual' ), implode( '', self::language_labels( array( $language_code ) ) ) );
            return $counts;
        }

        $clean = array();
        foreach ( $rows as $row ) {
            if ( ! is_array( $row ) || ! isset( $row['original'] ) || ! is_string( $row['original'] ) || $row['original'] === '' ) {
                $counts['skipped']++;
                continue;
            }
            $block_type = $this->whitelist_block_type( isset( $row['block_type'] ) ? $row['block_type'] : 0 );
            list( $original, $translated ) = $this->sanitize_dictionary_row( $row['original'], ( isset( $row['translated'] ) && is_string( $row['translated'] ) ) ? $row['translated'] : '', $block_type );
            if ( $original === '' ) {
                $counts['skipped']++;
                continue;
            }
            $status = $this->whitelist_status( isset( $row['status'] ) ? $row['status'] : 0, false );
            // Never claim a row is translated when it is not.
            if ( $translated === '' ) {
                $translated = null;
                $status     = TRP_Query::NOT_TRANSLATED;
            }
            $key = $block_type . '|' . $original;
            if ( isset( $clean[ $key ] ) ) {
                $counts['unchanged']++;
            }
            $clean[ $key ] = array(
                'original'   => $original,
                'translated' => $translated,
                'status'     => $status,
                'block_type' => $block_type,
            );
        }
        if ( empty( $clean ) ) {
            return $counts;
        }

        $existing  = $this->get_existing_dictionary_rows( $table, array_keys( array_column( $clean, 'original', 'original' ) ) );
        $to_update = array();
        $to_insert = array();
        foreach ( $clean as $key => $row ) {
            if ( ! isset( $existing[ $key ] ) ) {
                $to_insert[ $key ] = $row;
                continue;
            }
            $current = $existing[ $key ];
            // Never downgrade a translated row; otherwise there is nothing to add.
            if ( ! $this->is_translated( $row['translated'], $row['status'] ) ) {
                $counts['unchanged']++;
                continue;
            }
            // A table with duplicate rows of this original already holds this translation in one of them.
            if ( isset( $current->translations[ $row['translated'] ] ) ) {
                $counts['unchanged']++;
                continue;
            }
            // The site's text sanitized like the incoming one: when they are equal there is nothing to import.
            $current_text = $current->translated;
            if ( $current_text !== $row['translated'] && $current_text !== null ) {
                list( , $current_text ) = $this->sanitize_dictionary_row( $row['original'], $current_text, $row['block_type'] );
            }
            $outcome = $this->resolve_conflict( $mode, $this->is_translated( $current->translated, $current->status ), $current_text, $current->status, $row['translated'], $row['status'] );
            if ( $outcome === 'unchanged' || $outcome === 'kept' ) {
                $counts[ $outcome ]++;
                continue;
            }
            $to_update[] = array_merge( $row, array( 'outcome' => $outcome, 'id' => (int) $current->id ) );
        }

        $changed = false;
        if ( $to_update ) {
            $updated = $this->update_dictionary_rows( $table, $to_update );
            $this->count_updates( $counts, $to_update, $updated );
            $changed = $updated;
        }
        if ( $to_insert ) {
            $inserted            = $this->insert_dictionary_rows( $table, $language_code, $to_insert );
            $counts['inserted'] += $inserted;
            $counts['skipped']  += count( $to_insert ) - $inserted;
            $changed             = $changed || $inserted > 0;
        }

        // These writes bypass TRP_Query::update_strings(), so browsers' cached dynamic translations must be dropped here.
        if ( $changed ) {
            TRP_Dynamic_Cache::invalidate();
        }

        return $counts;
    }

    // Keyed by "block_type|original"; BINARY matches like original_strings_sync(), and the first row wins when the table holds duplicates.
    protected function get_existing_dictionary_rows( $table, $originals ) {
        $placeholders = implode( ', ', array_fill( 0, count( $originals ), 'BINARY %s' ) );
        $rows         = $this->db->get_results(
            $this->db->prepare(
                "SELECT id, original, translated, status, block_type FROM `" . sanitize_text_field( $table ) . "` WHERE original IN ( $placeholders ) ORDER BY id ASC",//phpcs:ignore
                $originals
            )
        );
        $map = array();
        foreach ( (array) $rows as $row ) {
            $key = (int) $row->block_type . '|' . $row->original;
            if ( ! isset( $map[ $key ] ) ) {
                $map[ $key ]               = $row;
                $map[ $key ]->translations = array();
            }
            if ( $this->is_translated( $row->translated, $row->status ) ) {
                $map[ $key ]->translations[ $row->translated ] = true;
            }
        }
        return $map;
    }

    // Not TRP_Query::insert_strings(): it forces translated NULL and status 0.
    protected function insert_dictionary_rows( $table, $language_code, $rows ) {
        $originals = array_keys( array_column( $rows, 'original', 'original' ) );
        if ( $language_code !== $this->get_current_default_language() ) {
            $this->trp_query->original_strings_sync( $language_code, $originals );
        }
        $original_ids = $this->get_original_ids_map( $originals );

        $values = array();
        foreach ( $rows as $row ) {
            $values[] = $this->prepare_tuple( array(
                array( 'type' => '%s', 'value' => $row['original'] ),
                array( 'type' => '%s', 'value' => $row['translated'] ),
                array( 'type' => '%d', 'value' => $row['status'] ),
                array( 'type' => '%d', 'value' => $row['block_type'] ),
                array( 'type' => '%d', 'value' => isset( $original_ids[ $row['original'] ] ) ? $original_ids[ $row['original'] ] : null ),
            ) );
        }
        $result = $this->track_write( $this->db->query( "INSERT INTO `" . sanitize_text_field( $table ) . "` ( original, translated, status, block_type, original_id ) VALUES " . implode( ', ', $values ) ) );//phpcs:ignore
        return ( $result === false ) ? 0 : (int) $result;
    }

    protected function update_dictionary_rows( $table, $rows ) {
        $values = array();
        foreach ( $rows as $row ) {
            $values[] = $this->prepare_tuple( array(
                array( 'type' => '%d', 'value' => $row['id'] ),
                array( 'type' => '%s', 'value' => $row['original'] ),
                array( 'type' => '%s', 'value' => $row['translated'] ),
                array( 'type' => '%d', 'value' => $row['status'] ),
                array( 'type' => '%d', 'value' => $row['block_type'] ),
            ) );
        }
        $result = $this->track_write( $this->db->query(
            "INSERT INTO `" . sanitize_text_field( $table ) . "` ( id, original, translated, status, block_type ) VALUES " . implode( ', ', $values ) .
            " ON DUPLICATE KEY UPDATE original = VALUES( original ), translated = VALUES( translated ), status = VALUES( status ), block_type = VALUES( block_type )"//phpcs:ignore
        ) );
        return ( $result !== false );
    }

    // Rows: array( 'original', 'translated', 'domain', 'context', 'plural_form', 'original_plural', 'status' ).
    public function import_gettext_rows( $language_code, $rows, $mode ) {
        $counts = $this->empty_counts();
        if ( empty( $rows ) ) {
            return $counts;
        }
        if ( ! trp_is_valid_language_code( $language_code ) ) {
            $counts['skipped'] += count( $rows );
            /* translators: %s is a language code. */
            $counts['warnings'][] = sprintf( __( 'Theme and plugin strings for %s were not imported: it is not a valid language code.', 'translatepress-multilingual' ), $language_code );
            return $counts;
        }
        $table = $this->trp_query->get_gettext_table_name( $language_code );
        if ( ! $this->trp_query->table_exists( $table, true ) ) {
            $counts['skipped'] += count( $rows );
            /* translators: %s is a language name. */
            $counts['warnings'][] = sprintf( __( 'Theme and plugin strings for %s were not imported: the language is not set up on this site.', 'translatepress-multilingual' ), implode( '', self::language_labels( array( $language_code ) ) ) );
            return $counts;
        }

        $clean = array();
        foreach ( $rows as $row ) {
            if ( ! is_array( $row ) || ! isset( $row['original'] ) || ! is_string( $row['original'] ) || $row['original'] === '' ) {
                $counts['skipped']++;
                continue;
            }
            $plural_form = isset( $row['plural_form'] ) ? (int) $row['plural_form'] : 0;
            if ( $plural_form < 0 || $plural_form > self::MAX_PLURAL_FORM ) {
                $counts['skipped']++;
                continue;
            }
            list( $original, $translated, $domain, $context ) = $this->sanitize_gettext_row(
                $row['original'],
                ( isset( $row['translated'] ) && is_string( $row['translated'] ) ) ? $row['translated'] : '',
                ( isset( $row['domain'] ) && is_string( $row['domain'] ) ) ? $row['domain'] : '',
                ( isset( $row['context'] ) && is_string( $row['context'] ) && $row['context'] !== '' ) ? $row['context'] : null
            );
            if ( $original === '' ) {
                $counts['skipped']++;
                continue;
            }
            $status = $this->whitelist_status( isset( $row['status'] ) ? $row['status'] : 0, true );
            if ( $translated === '' ) {
                $translated = null;
                $status     = TRP_Query::NOT_TRANSLATED;
            }
            $clean[] = array(
                'original'        => $original,
                'translated'      => $translated,
                'domain'          => $domain,
                'context'         => $context,
                'plural_form'     => $plural_form,
                'original_plural' => ( isset( $row['original_plural'] ) && is_string( $row['original_plural'] ) ) ? $row['original_plural'] : '',
                'status'          => $status,
            );
        }
        if ( empty( $clean ) ) {
            return $counts;
        }

        $sync_input = array();
        foreach ( $clean as $index => $row ) {
            $sync_input[ $index ] = array(
                'original'        => $row['original'],
                'domain'          => $row['domain'],
                'context'         => $row['context'],
                'original_plural' => $row['original_plural'],
            );
        }
        $original_ids = (array) $this->gettext_insert_update->gettext_original_strings_sync( $sync_input );
        $existing     = $this->get_existing_gettext_rows( $table, array_map( 'intval', $original_ids ) );

        $to_update = array();
        $to_insert = array();
        foreach ( $clean as $index => $row ) {
            if ( ! isset( $original_ids[ $index ] ) ) {
                $counts['skipped']++;
                continue;
            }
            $original_id = (int) $original_ids[ $index ];
            $key         = $original_id . '|' . $row['plural_form'];

            if ( isset( $existing[ $key ] ) ) {
                $current = $existing[ $key ];
                if ( ! $this->is_translated( $row['translated'], $row['status'] ) ) {
                    $counts['unchanged']++;
                    continue;
                }
                // Compared sanitized, as in import_dictionary_rows().
                $current_text = ( $current->translated === $row['translated'] || $current->translated === null ) ? $current->translated : $this->sanitize_translation( $current->translated );
                $outcome      = $this->resolve_conflict( $mode, $this->is_translated( $current->translated, $current->status ), $current_text, $current->status, $row['translated'], $row['status'] );
                if ( $outcome === 'unchanged' || $outcome === 'kept' ) {
                    $counts[ $outcome ]++;
                    continue;
                }
                if ( isset( $to_update[ $key ] ) ) {
                    $counts['unchanged']++;
                }
                $to_update[ $key ] = array(
                    'outcome'     => $outcome,
                    'id'          => (int) $current->id,
                    'original'    => $row['original'],
                    'translated'  => $row['translated'],
                    'domain'      => $row['domain'],
                    'status'      => $row['status'],
                    'plural_form' => $row['plural_form'],
                );
                continue;
            }

            if ( isset( $to_insert[ $key ] ) ) {
                $counts['unchanged']++;
            }
            $row['original_id'] = $original_id;
            $to_insert[ $key ]  = $row;
        }

        if ( $to_update ) {
            $result = $this->track_write( $this->gettext_insert_update->update_gettext_strings( array_values( $to_update ), $language_code, array( 'id', 'original', 'translated', 'domain', 'status', 'plural_form' ) ) );
            $this->count_updates( $counts, $to_update, $result );
        }
        if ( $to_insert ) {
            $counts['inserted'] += $this->insert_gettext_rows( $table, $to_insert );
        }

        return $counts;
    }

    // Keyed by "original_id|plural_form".
    protected function get_existing_gettext_rows( $table, $original_ids ) {
        if ( empty( $original_ids ) ) {
            return array();
        }
        $placeholders = implode( ', ', array_fill( 0, count( $original_ids ), '%d' ) );
        $rows         = $this->db->get_results(
            $this->db->prepare(
                "SELECT id, original_id, plural_form, translated, status FROM `" . sanitize_text_field( $table ) . "` WHERE original_id IN ( $placeholders )",//phpcs:ignore
                $original_ids
            )
        );
        $map = array();
        foreach ( (array) $rows as $row ) {
            $map[ (int) $row->original_id . '|' . (int) $row->plural_form ] = $row;
        }
        return $map;
    }

    // Keeps the incoming status, unlike the core insert.
    protected function insert_gettext_rows( $table, $rows ) {
        $values = array();
        foreach ( $rows as $row ) {
            $values[] = $this->prepare_tuple( array(
                array( 'type' => '%s', 'value' => $row['original'] ),
                array( 'type' => '%s', 'value' => $row['translated'] ),
                array( 'type' => '%s', 'value' => $row['domain'] ),
                array( 'type' => '%d', 'value' => $row['status'] ),
                array( 'type' => '%d', 'value' => $row['plural_form'] ),
                array( 'type' => '%d', 'value' => $row['original_id'] ),
            ) );
        }
        $result = $this->track_write( $this->db->query( "INSERT INTO `" . sanitize_text_field( $table ) . "` ( original, translated, domain, status, plural_form, original_id ) VALUES " . implode( ', ', $values ) . " ON DUPLICATE KEY UPDATE id = id" ) );//phpcs:ignore
        return ( $result === false ) ? 0 : (int) $result;
    }

    // Rows: array( 'original', 'type', 'translations' => array( language => array( 'translated', 'status' ) ) ); counted per translated slug.
    public function import_slug_rows( $rows, $mode ) {
        $counts = $this->empty_counts();
        if ( empty( $rows ) ) {
            return $counts;
        }
        if ( ! class_exists( 'TRP_Slug_Query' ) || ! $this->slug_tables_exist() ) {
            $counts['skipped']   += count( $rows );
            $counts['warnings'][] = __( 'Translated slugs were not imported: they need the SEO Pack add-on, which is not active on this site.', 'translatepress-multilingual' );
            return $counts;
        }
        $slug_query     = new TRP_Slug_Query();
        $site_languages = $this->get_current_translation_languages();

        $originals   = array();
        $by_language = array();
        foreach ( $rows as $row ) {
            if ( ! is_array( $row ) || ! isset( $row['original'] ) || ! is_string( $row['original'] ) || $row['original'] === '' ) {
                $counts['skipped']++;
                continue;
            }
            $original               = strtolower( urlencode( urldecode( $row['original'] ) ) );
            $originals[ $original ] = array(
                'original' => $original,
                'type'     => ( isset( $row['type'] ) && is_string( $row['type'] ) && $row['type'] !== '' ) ? $row['type'] : 'other',
            );

            $translations = ( isset( $row['translations'] ) && is_array( $row['translations'] ) ) ? $row['translations'] : array();
            foreach ( $translations as $language => $translation ) {
                if ( ! is_array( $translation ) || ! isset( $translation['translated'] ) || ! is_string( $translation['translated'] ) || $translation['translated'] === '' ) {
                    continue;
                }
                // Languages not on this site: the review already listed them as skipped.
                if ( ! is_string( $language ) || ! trp_is_valid_language_code( $language ) || ! in_array( $language, $site_languages, true ) ) {
                    continue;
                }
                // Same sanitizing as a slug saved in the String Translation editor (TRP_String_Translation_Editor_Actions).
                $translated_slug = urldecode( sanitize_title( $translation['translated'] ) );
                if ( $translated_slug === '' ) {
                    $counts['skipped']++;
                    continue;
                }
                if ( isset( $by_language[ $language ][ $original ] ) ) {
                    $counts['unchanged']++;
                }
                $by_language[ $language ][ $original ] = array(
                    'translated' => strtolower( urlencode( urldecode( $translated_slug ) ) ),
                    'status'     => $this->whitelist_status( isset( $translation['status'] ) ? $translation['status'] : TRP_Query::HUMAN_REVIEWED, false ),
                );
            }
        }
        if ( empty( $originals ) ) {
            return $counts;
        }

        $original_ids = $slug_query->insert_original_slugs( array_values( $originals ) );
        if ( ! is_array( $original_ids ) ) {
            $original_ids = $slug_query->get_ids_from_original( array_keys( $originals ) );
        }
        if ( ! is_array( $original_ids ) ) {
            foreach ( $by_language as $translations ) {
                $counts['skipped'] += count( $translations );
            }
            $counts['warnings'][] = __( 'The translated slugs could not be written to the database.', 'translatepress-multilingual' );
            return $counts;
        }

        foreach ( $by_language as $language => $translations ) {
            $existing = array();
            $found    = $slug_query->get_translated_slugs_from_original( array_keys( $translations ), $language, false );
            foreach ( is_array( $found ) ? $found : array() as $existing_row ) {
                if ( ! isset( $existing[ $existing_row['original'] ] ) ) {
                    $existing[ $existing_row['original'] ] = $existing_row;
                }
            }

            $insert = array();
            $update = array();
            foreach ( $translations as $original => $translation ) {
                if ( isset( $existing[ $original ] ) ) {
                    $current = $existing[ $original ];
                    // Compared in the form a slug is saved in, as above.
                    $current_slug = strtolower( urlencode( urldecode( urldecode( sanitize_title( (string) $current['translated'] ) ) ) ) );
                    $outcome      = $this->resolve_conflict( $mode, ! empty( $current['translated'] ), $current_slug, $current['status'], $translation['translated'], $translation['status'] );
                    if ( $outcome === 'unchanged' || $outcome === 'kept' ) {
                        $counts[ $outcome ]++;
                        continue;
                    }
                    $update[] = array(
                        'outcome'    => $outcome,
                        'id'         => (int) $current['id'],
                        'original'   => $original,
                        'translated' => $translation['translated'],
                        'status'     => $translation['status'],
                    );
                    continue;
                }
                if ( ! isset( $original_ids[ $original ] ) ) {
                    $counts['skipped']++;
                    continue;
                }
                $insert[] = array(
                    'outcome'     => 'inserted',
                    'original'    => $original,
                    'original_id' => (int) $original_ids[ $original ],
                    'translated'  => $translation['translated'],
                    'status'      => $translation['status'],
                );
            }

            if ( $update ) {
                $this->count_updates( $counts, $update, $this->track_write( $slug_query->update_slugs( $update, $language ) ) !== false );
            }
            if ( $insert ) {
                $this->count_updates( $counts, $insert, $this->track_write( $slug_query->insert_translated_slugs( $insert, $language ) ) !== false );
            }
        }

        return $counts;
    }

    // Rows: array( 'original', 'meta_key', 'meta_value' ); missing originals are created.
    public function import_original_meta_rows( $rows ) {
        $counts = $this->empty_counts();
        if ( empty( $rows ) ) {
            return $counts;
        }
        $meta_table = $this->trp_query->get_table_name_for_original_meta();
        if ( ! $this->trp_query->table_exists( $meta_table, true ) ) {
            $counts['skipped']   += count( $rows );
            $counts['warnings'][] = __( 'Some translation details were not imported: their database table could not be created. Check that the database user is allowed to create tables, then import the file again.', 'translatepress-multilingual' );
            return $counts;
        }

        $clean = array();
        foreach ( $rows as $row ) {
            if ( ! is_array( $row ) || ! isset( $row['original'] ) || ! is_string( $row['original'] ) || $row['original'] === ''
                || ! isset( $row['meta_key'] ) || ! is_string( $row['meta_key'] ) || $row['meta_key'] === '' ) {
                $counts['skipped']++;
                continue;
            }
            // Stored like a dictionary original, so the meta attaches to that row.
            $original = $this->sanitize_original( $row['original'] );
            if ( $original === '' ) {
                $counts['skipped']++;
                continue;
            }
            $clean[] = array(
                'original'   => $original,
                'meta_key'   => $row['meta_key'],
                'meta_value' => ( isset( $row['meta_value'] ) && is_scalar( $row['meta_value'] ) ) ? (string) $row['meta_value'] : '',
            );
        }
        if ( empty( $clean ) ) {
            return $counts;
        }

        $original_ids = $this->ensure_original_ids( array_keys( array_column( $clean, 'original', 'original' ) ) );
        $ids          = array();
        foreach ( $clean as $index => $row ) {
            if ( isset( $original_ids[ $row['original'] ] ) ) {
                $ids[ $index ] = $original_ids[ $row['original'] ];
            }
        }
        $this->insert_meta_rows( $meta_table, $clean, $ids, $counts );
        return $counts;
    }

    // Rows: array( 'original', 'domain', 'context', 'meta_key', 'meta_value' ); missing gettext originals are created.
    public function import_gettext_original_meta_rows( $rows ) {
        $counts = $this->empty_counts();
        if ( empty( $rows ) ) {
            return $counts;
        }
        $meta_table = $this->trp_query->get_table_name_for_gettext_original_meta();
        if ( ! $this->trp_query->table_exists( $meta_table, true ) ) {
            $counts['skipped']   += count( $rows );
            $counts['warnings'][] = __( 'Some translation details were not imported: their database table could not be created. Check that the database user is allowed to create tables, then import the file again.', 'translatepress-multilingual' );
            return $counts;
        }

        $clean      = array();
        $sync_input = array();
        foreach ( $rows as $row ) {
            if ( ! is_array( $row ) || ! isset( $row['original'] ) || ! is_string( $row['original'] ) || $row['original'] === ''
                || ! isset( $row['meta_key'] ) || ! is_string( $row['meta_key'] ) || $row['meta_key'] === '' ) {
                $counts['skipped']++;
                continue;
            }
            // Stored like a gettext original; key and value like TRP_Gettext_Insert_Update::bulk_insert_original_id_meta().
            $original = $this->sanitize_original( $row['original'] );
            if ( $original === '' ) {
                $counts['skipped']++;
                continue;
            }
            $clean[]      = array(
                'meta_key'   => sanitize_text_field( $row['meta_key'] ),
                'meta_value' => sanitize_text_field( ( isset( $row['meta_value'] ) && is_scalar( $row['meta_value'] ) ) ? (string) $row['meta_value'] : '' ),
            );
            $sync_input[] = array(
                'original'        => $original,
                'domain'          => sanitize_text_field( ( isset( $row['domain'] ) && is_string( $row['domain'] ) ) ? $row['domain'] : '' ),
                'context'         => ( isset( $row['context'] ) && is_string( $row['context'] ) && $row['context'] !== '' ) ? $row['context'] : null,
                'original_plural' => '',
            );
        }
        if ( empty( $clean ) ) {
            return $counts;
        }

        $ids = (array) $this->gettext_insert_update->gettext_original_strings_sync( $sync_input );
        $this->insert_meta_rows( $meta_table, $clean, $ids, $counts );
        return $counts;
    }

    // Inserts the ( original_id, meta_key, meta_value ) triples the table does not hold yet; $ids maps a row index to its original id.
    protected function insert_meta_rows( $meta_table, $rows, $ids, &$counts ) {
        $existing = $this->get_existing_meta_pairs( $meta_table, array_map( 'intval', $ids ) );
        $values   = array();
        foreach ( $rows as $index => $row ) {
            if ( ! isset( $ids[ $index ] ) ) {
                $counts['skipped']++;
                continue;
            }
            $original_id = (int) $ids[ $index ];
            $key         = $original_id . '|' . $row['meta_key'] . '|' . $row['meta_value'];
            if ( isset( $existing[ $key ] ) ) {
                $counts['unchanged']++;
                continue;
            }
            $existing[ $key ] = true;
            $values[]         = $this->prepare_tuple( array(
                array( 'type' => '%d', 'value' => $original_id ),
                array( 'type' => '%s', 'value' => $row['meta_key'] ),
                array( 'type' => '%s', 'value' => $row['meta_value'] ),
            ) );
        }
        if ( empty( $values ) ) {
            return;
        }
        $result              = $this->track_write( $this->db->query( "INSERT INTO `" . sanitize_text_field( $meta_table ) . "` ( original_id, meta_key, meta_value ) VALUES " . implode( ', ', $values ) ) );//phpcs:ignore
        $inserted            = ( $result === false ) ? 0 : (int) $result;
        $counts['inserted'] += $inserted;
        $counts['skipped']  += count( $values ) - $inserted;
    }

    protected function get_existing_meta_pairs( $meta_table, $original_ids ) {
        if ( empty( $original_ids ) ) {
            return array();
        }
        $placeholders = implode( ', ', array_fill( 0, count( $original_ids ), '%d' ) );
        $rows         = $this->db->get_results(
            $this->db->prepare(
                "SELECT original_id, meta_key, meta_value FROM `" . sanitize_text_field( $meta_table ) . "` WHERE original_id IN ( $placeholders )",//phpcs:ignore
                array_values( $original_ids )
            ),
            ARRAY_A
        );
        $pairs = array();
        foreach ( (array) $rows as $row ) {
            $pairs[ (int) $row['original_id'] . '|' . $row['meta_key'] . '|' . $row['meta_value'] ] = true;
        }
        return $pairs;
    }

    // Creates the missing originals in trp_original_strings; returns original => id.
    protected function ensure_original_ids( $originals ) {
        $this->trp_query->check_original_table();
        // original_strings_sync() only inserts for a non-default language.
        $language = $this->get_non_default_language();
        if ( $language !== '' ) {
            $this->trp_query->original_strings_sync( $language, $originals );
        }
        return $this->get_original_ids_map( $originals );
    }

    // TRP_Query::get_original_string_ids() returns a positional list; this maps it back to original => id.
    protected function get_original_ids_map( $originals ) {
        $ids = (array) $this->trp_query->get_original_string_ids( $originals );
        $map = array();
        foreach ( $originals as $index => $original ) {
            if ( ! empty( $ids[ $index ] ) ) {
                $map[ $original ] = (int) $ids[ $index ];
            }
        }
        return $map;
    }

    protected function get_non_default_language() {
        $default = $this->get_current_default_language();
        foreach ( $this->get_current_translation_languages() as $language ) {
            if ( $language !== $default ) {
                return $language;
            }
        }
        return '';
    }

    protected function whitelist_status( $status, $allow_language_file ) {
        $status  = (int) $status;
        $allowed = array( TRP_Query::NOT_TRANSLATED, TRP_Query::MACHINE_TRANSLATED, TRP_Query::HUMAN_REVIEWED, TRP_Query::SIMILAR_TRANSLATED );
        if ( $allow_language_file ) {
            $allowed[] = TRP_Query::GETTEXT_TRANSLATED_IN_LANGUAGE_FILE;
        }
        return in_array( $status, $allowed, true ) ? $status : TRP_Query::NOT_TRANSLATED;
    }

    protected function whitelist_block_type( $block_type ) {
        $block_type = (int) $block_type;
        $allowed    = array( TRP_Query::BLOCK_TYPE_REGULAR_STRING, TRP_Query::BLOCK_TYPE_ACTIVE, TRP_Query::BLOCK_TYPE_DEPRECATED );
        return in_array( $block_type, $allowed, true ) ? $block_type : TRP_Query::BLOCK_TYPE_REGULAR_STRING;
    }

    // Languages only: trp_settings and the file's custom languages. False when the file's settings can't be used; nothing is saved then.
    public function import_language_settings( $bundle, &$warnings ) {
        if ( ! is_array( $bundle ) ) {
            $warnings[] = __( 'The settings were not imported: the settings in the file are damaged.', 'translatepress-multilingual' );
            return false;
        }
        if ( ! isset( $bundle['trp_settings'] ) || ! is_array( $bundle['trp_settings'] ) ) {
            $warnings[] = __( 'The settings were not imported: the file does not contain the main TranslatePress settings.', 'translatepress-multilingual' );
            return false;
        }
        $trp      = TRP_Translate_Press::get_trp_instance();
        $ignored  = array();
        $advanced = $this->has_advanced_settings( $bundle ) ? $this->sanitize_advanced_settings( $bundle['trp_advanced_settings'], $ignored ) : null;
        $incoming = $this->sanitize_trp_settings( $bundle['trp_settings'], $this->get_known_language_codes( $advanced ), $warnings );
        if ( $incoming === false ) {
            $warnings[] = __( 'The settings were not imported: the file has no valid default language.', 'translatepress-multilingual' );
            return false;
        }

        // The rest of the advanced settings waits for import_other_settings().
        if ( isset( $advanced['custom_language'] ) ) {
            $current                    = get_option( 'trp_advanced_settings', array() );
            $current                    = is_array( $current ) ? $current : array();
            $current['custom_language'] = $advanced['custom_language'];
            $this->update_advanced_settings( $current );
            $trp->get_component( 'languages' )->flush_cache();
        }

        // TRP_Settings::sanitize_settings() creates the language tables and menu entries and flushes the rewrite rules.
        if ( ! has_filter( 'sanitize_option_trp_settings' ) ) {
            $trp->get_component( 'settings' )->register_setting();
        }
        update_option( 'trp_settings', $incoming );
        // Loaded from the next request on, where the settings that belong to them are imported.
        $this->import_add_ons( isset( $bundle['trp_add_ons_settings'] ) ? $bundle['trp_add_ons_settings'] : array() );
        $this->settings['default-language']      = $incoming['default-language'];
        $this->settings['translation-languages'] = $incoming['translation-languages'];
        $this->settings['publish-languages']     = $incoming['publish-languages'];
        return true;
    }

    protected function has_advanced_settings( $bundle ) {
        return isset( $bundle['trp_advanced_settings'] ) && is_array( $bundle['trp_advanced_settings'] ) && $bundle['trp_advanced_settings'];
    }

    // Returns warnings. Runs in the request after import_language_settings(): these settings are validated against the site's languages.
    public function import_other_settings( $bundle ) {
        $warnings = array();
        $trp      = TRP_Translate_Press::get_trp_instance();

        if ( $this->has_advanced_settings( $bundle ) ) {
            $this->update_advanced_settings( $this->sanitize_advanced_settings( $bundle['trp_advanced_settings'], $warnings ) );
        }

        if ( isset( $bundle['trp_language_switcher_settings'] ) && is_array( $bundle['trp_language_switcher_settings'] ) ) {
            update_option( 'trp_language_switcher_settings', $this->sanitize_language_switcher_settings( $bundle['trp_language_switcher_settings'] ) );
        }

        if ( isset( $bundle['trp_glossary'] ) && is_array( $bundle['trp_glossary'] ) ) {
            update_option( 'trp_glossary', $this->sanitize_glossary( $bundle['trp_glossary'] ), false );
        }

        // MT and ALD settings go through update_option(), so their sanitize callbacks also run.
        if ( isset( $bundle['trp_machine_translation_settings'] ) && is_array( $bundle['trp_machine_translation_settings'] ) ) {
            update_option( 'trp_machine_translation_settings', $this->merge_machine_translation_settings( $bundle['trp_machine_translation_settings'] ) );
        }

        if ( isset( $bundle['trp_ald_settings'] ) && is_array( $bundle['trp_ald_settings'] ) ) {
            $this->import_ald_settings( $bundle['trp_ald_settings'], $warnings );
        }

        $this->import_slug_translation_options( $bundle, $warnings );

        // Menu entries again: the imported custom languages only have names now.
        $trp->get_component( 'languages' )->flush_cache();
        $trp_settings = get_option( 'trp_settings', array() );
        $trp->get_component( 'settings' )->create_menu_entries( isset( $trp_settings['publish-languages'] ) ? (array) $trp_settings['publish-languages'] : array() );

        return $warnings;
    }

    // The add-on's register_setting() sanitizer runs in update_option(); without the add-on there is nothing to validate against.
    protected function import_ald_settings( $incoming, &$warnings ) {
        if ( ! has_filter( 'sanitize_option_trp_ald_settings' ) ) {
            $warnings[] = __( 'Automatic User Language Detection settings were not imported: the add-on is not active on this site.', 'translatepress-multilingual' );
            return;
        }
        // The add-on's sanitizer throws on a value of the wrong type; the site's settings then stay as they are.
        try {
            update_option( 'trp_ald_settings', $incoming );
        } catch ( \Throwable $e ) {
            $warnings[] = __( 'Automatic User Language Detection settings were not imported: the settings in the file are damaged.', 'translatepress-multilingual' );
        }
    }

    // Post type base and taxonomy slugs; SEO Pack saves these through AJAX handlers that read $_POST, so they are sanitized here.
    protected function import_slug_translation_options( $bundle, &$warnings ) {
        $present = array();
        foreach ( array( 'trp_post_type_base_slug_translation', 'trp_taxonomy_slug_translation' ) as $option ) {
            if ( isset( $bundle[ $option ] ) && is_array( $bundle[ $option ] ) ) {
                $present[] = $option;
            }
        }
        if ( ! $present ) {
            return;
        }
        if ( ! $this->slug_tables_exist() ) {
            $warnings[] = __( 'Translated post type and taxonomy slugs were not imported: the SEO Pack add-on is not active on this site.', 'translatepress-multilingual' );
            return;
        }
        foreach ( $present as $option ) {
            update_option( $option, $this->sanitize_slug_translation_option( $bundle[ $option ] ) );
        }
    }

    // slug => array( 'original', 'type', 'translationsArray' => array( language => array( ... ) ) ), known keys only.
    protected function sanitize_slug_translation_option( $incoming ) {
        $clean = array();
        foreach ( $incoming as $slug => $entry ) {
            if ( ! is_string( $slug ) || $slug === '' || ! is_array( $entry ) ) {
                continue;
            }
            $row = array( 'translationsArray' => array() );
            foreach ( array( 'original', 'type' ) as $key ) {
                if ( isset( $entry[ $key ] ) && is_scalar( $entry[ $key ] ) ) {
                    $row[ $key ] = sanitize_text_field( (string) $entry[ $key ] );
                }
            }

            $translations = ( isset( $entry['translationsArray'] ) && is_array( $entry['translationsArray'] ) ) ? $entry['translationsArray'] : array();
            foreach ( $translations as $language_code => $translation ) {
                if ( ! is_string( $language_code ) || ! trp_is_valid_language_code( $language_code ) || ! is_array( $translation ) ) {
                    continue;
                }
                $clean_translation = array();
                foreach ( array( 'translated', 'editedTranslation', 'original', 'original_id', 'id' ) as $key ) {
                    if ( isset( $translation[ $key ] ) && is_scalar( $translation[ $key ] ) ) {
                        $clean_translation[ $key ] = sanitize_text_field( (string) $translation[ $key ] );
                    }
                }
                $clean_translation['status'] = (string) $this->whitelist_status( isset( $translation['status'] ) ? $translation['status'] : 0, false );

                $row['translationsArray'][ $language_code ] = $clean_translation;
            }

            $clean[ sanitize_text_field( $slug ) ] = $row;
        }
        return $clean;
    }

    // code => true: the languages this site knows, plus the file's custom languages that passed the Advanced tab's code check.
    protected function get_known_language_codes( $advanced ) {
        $trp   = TRP_Translate_Press::get_trp_instance();
        $known = array_fill_keys( array_keys( (array) $trp->get_component( 'languages' )->get_languages( 'english_name' ) ), true );
        if ( isset( $advanced['custom_language']['cuslangcode'] ) && is_array( $advanced['custom_language']['cuslangcode'] ) ) {
            foreach ( $advanced['custom_language']['cuslangcode'] as $code ) {
                $known[ $code ] = true;
            }
        }
        return $known;
    }

    // Known codes only, capped in number and length; $context names the setting in the warnings.
    protected function sanitize_language_codes( $codes, $known, &$warnings, $context ) {
        $valid   = array();
        $dropped = array();
        foreach ( $codes as $code ) {
            if ( count( $valid ) >= self::MAX_LANGUAGES ) {
                $warnings[] = sprintf(
                    /* translators: 1: a number, 2: which language list, e.g. "the site languages". */
                    _n( 'Only the first %1$d language in %2$s was imported.', 'Only the first %1$d languages in %2$s were imported.', self::MAX_LANGUAGES, 'translatepress-multilingual' ),
                    self::MAX_LANGUAGES,
                    $this->describe_language_setting( $context )
                );
                break;
            }
            if ( ! $this->is_known_language_code( $code, $known ) ) {
                $dropped[] = is_string( $code ) ? substr( $code, 0, self::MAX_LANGUAGE_CODE_LENGTH ) : gettype( $code );
                continue;
            }
            $valid[ $code ] = true;
        }
        if ( ! empty( $dropped ) ) {
            $dropped    = array_values( array_unique( $dropped ) );
            $warnings[] = sprintf(
                /* translators: 1: which language list, e.g. "the site languages", 2: language codes. */
                _n( 'A language in %1$s is not available on this site and was left out: %2$s.', 'Some languages in %1$s are not available on this site and were left out: %2$s.', count( $dropped ), 'translatepress-multilingual' ),
                $this->describe_language_setting( $context ),
                implode( ', ', $dropped )
            );
        }
        return array_keys( $valid );
    }

    protected function is_known_language_code( $code, $known ) {
        return is_string( $code ) && $code !== '' && strlen( $code ) <= self::MAX_LANGUAGE_CODE_LENGTH
            && trp_is_valid_language_code( $code ) && isset( $known[ $code ] );
    }

    protected function describe_language_setting( $context ) {
        $map = array(
            'translation-languages' => __( 'the site languages', 'translatepress-multilingual' ),
            'publish-languages'     => __( 'the published languages', 'translatepress-multilingual' ),
        );
        return isset( $map[ $context ] ) ? $map[ $context ] : __( 'the language settings', 'translatepress-multilingual' );
    }

    // TRP_Settings::sanitize_settings() runs on the result too, but accepts any key and wrong value types. False without a usable default language.
    protected function sanitize_trp_settings( $incoming, $known_languages, &$warnings ) {
        $default_language = isset( $incoming['default-language'] ) ? $incoming['default-language'] : '';
        if ( ! $this->is_known_language_code( $default_language, $known_languages ) ) {
            return false;
        }

        $known_keys = array_merge(
            array( 'default-language', 'translation-languages', 'publish-languages' ),
            self::SETTINGS_TEXT_KEYS,
            array( 'url-slugs', 'translation-languages-formality-parameter' )
        );

        $settings = array();
        foreach ( $known_keys as $key ) {
            if ( isset( $incoming[ $key ] ) ) {
                $settings[ $key ] = $incoming[ $key ];
            }
        }

        $settings['default-language'] = $default_language;
        foreach ( array( 'translation-languages', 'publish-languages' ) as $key ) {
            $codes = ( isset( $settings[ $key ] ) && is_array( $settings[ $key ] ) ) ? $settings[ $key ] : array();
            $codes = $this->sanitize_language_codes( $codes, $known_languages, $warnings, $key );
            if ( ! in_array( $default_language, $codes, true ) ) {
                array_unshift( $codes, $default_language );
            }
            $settings[ $key ] = $codes;
        }

        foreach ( self::SETTINGS_TEXT_KEYS as $key ) {
            if ( isset( $settings[ $key ] ) ) {
                $settings[ $key ] = is_scalar( $settings[ $key ] ) ? sanitize_text_field( (string) $settings[ $key ] ) : '';
            }
        }

        $settings['url-slugs'] = $this->sanitize_url_slugs( isset( $settings['url-slugs'] ) ? $settings['url-slugs'] : array(), $settings['translation-languages'] );

        // Core copies this key from the form as it is.
        $key = 'translation-languages-formality-parameter';
        if ( isset( $settings[ $key ] ) ) {
            $formality = array();
            foreach ( is_array( $settings[ $key ] ) ? $settings[ $key ] : array() as $language_code => $value ) {
                if ( ! is_string( $language_code ) || ! trp_is_valid_language_code( $language_code ) || ! is_scalar( $value ) ) {
                    continue;
                }
                $value                       = sanitize_text_field( (string) $value );
                $formality[ $language_code ] = in_array( $value, array( 'formal', 'informal', 'default' ), true ) ? $value : 'default';
            }
            $settings[ $key ] = $formality;
        }

        // Domains are site specific and are never imported.
        $current = get_option( 'trp_settings', array() );
        if ( is_array( $current ) && isset( $current['trp-multiple-domains'] ) ) {
            $settings['trp-multiple-domains'] = $current['trp-multiple-domains'];
        } else {
            unset( $settings['trp-multiple-domains'] );
        }

        return $settings;
    }

    // One unique slug per language: on a collision the later language falls back to its code.
    protected function sanitize_url_slugs( $slugs, $languages ) {
        if ( ! is_array( $slugs ) || empty( $slugs ) ) {
            // Same seed as TRP_Settings::sanitize_settings() when url-slugs is missing.
            $slugs = TRP_Translate_Press::get_trp_instance()->get_component( 'languages' )->get_iso_codes( $languages );
        }

        $clean = array();
        $taken = array();
        foreach ( $languages as $language_code ) {
            $slug = ( isset( $slugs[ $language_code ] ) && is_scalar( $slugs[ $language_code ] ) ) ? (string) $slugs[ $language_code ] : '';
            $slug = sanitize_title( strtolower( $slug ) );
            if ( $slug === '' || isset( $taken[ $slug ] ) ) {
                $slug = $language_code;
            }
            $taken[ $slug ]          = true;
            $clean[ $language_code ] = $slug;
        }
        return $clean;
    }

    // The rules of TRP_Advanced_Tab::sanitize_settings(), which is not called: it collapses form columns, rewrites $_REQUEST and makes the opt-in remote calls.
    protected function sanitize_advanced_settings( $incoming, &$warnings ) {
        $previous = get_option( 'trp_advanced_settings', array() );
        if ( ! is_array( $previous ) ) {
            $previous = array();
        }
        $registered = TRP_Translate_Press::get_trp_instance()->get_component( 'advanced_tab' )->get_registered_advanced_settings();
        $settings   = array();
        $known      = array();

        foreach ( $registered as $setting ) {
            if ( ! is_array( $setting ) || empty( $setting['name'] ) || empty( $setting['type'] ) ) {
                continue;
            }
            $name           = $setting['name'];
            $known[ $name ] = true;
            $value          = isset( $incoming[ $name ] ) ? $incoming[ $name ] : false;

            switch ( $setting['type'] ) {
                case 'checkbox':
                    $settings[ $name ] = ( $value === 'yes' ) ? 'yes' : 'no';
                    break;
                case 'select':
                case 'radio':
                    $options           = ( isset( $setting['options'] ) && is_array( $setting['options'] ) ) ? $setting['options'] : array();
                    $allowed           = array_map( 'strval', $setting['type'] === 'select' ? array_keys( $options ) : $options );
                    $settings[ $name ] = ( is_scalar( $value ) && in_array( (string) $value, $allowed, true ) )
                        ? sanitize_text_field( (string) $value )
                        : ( empty( $setting['default'] ) ? false : $setting['default'] );
                    break;
                case 'input':
                    $settings[ $name ] = is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
                    break;
                case 'number':
                    $settings[ $name ] = sanitize_text_field( (string) intval( is_scalar( $value ) ? $value : 0 ) );
                    break;
                case 'custom':
                    if ( isset( $setting['rows'] ) && is_array( $setting['rows'] ) && is_array( $value ) ) {
                        foreach ( $setting['rows'] as $row_label => $row_type ) {
                            if ( ! isset( $value[ $row_label ] ) || ! is_scalar( $value[ $row_label ] ) ) {
                                continue;
                            }
                            $settings[ $name ][ $row_label ] = ( $row_type !== 'textarea' )
                                ? sanitize_text_field( (string) $value[ $row_label ] )
                                : sanitize_textarea_field( (string) $value[ $row_label ] );
                        }
                    }
                    if ( $name === 'enable_hreflang_xdefault' ) {
                        $checkbox_key              = $name . '-checkbox';
                        $known[ $checkbox_key ]    = true;
                        $disabled                  = ( $value === false || ! is_scalar( $value ) );
                        $settings[ $name ]         = $disabled ? 'disabled' : sanitize_text_field( (string) $value );
                        $checkbox                  = isset( $incoming[ $checkbox_key ] ) && is_scalar( $incoming[ $checkbox_key ] ) ? (string) $incoming[ $checkbox_key ] : 'no';
                        $settings[ $checkbox_key ] = $disabled ? 'no' : sanitize_text_field( $checkbox );
                    }
                    break;
                case 'input_array':
                    $checkbox_key           = $name . '-checkbox';
                    $known[ $checkbox_key ] = true;
                    if ( isset( $setting['rows'] ) && is_array( $setting['rows'] ) && is_array( $value ) ) {
                        foreach ( $setting['rows'] as $row_label => $row_name ) {
                            if ( isset( $value[ $row_label ] ) && is_scalar( $value[ $row_label ] ) ) {
                                $settings[ $name ][ $row_label ] = sanitize_text_field( (string) $value[ $row_label ] );
                            }
                        }
                    }
                    $checkbox                  = ( isset( $incoming[ $checkbox_key ] ) && is_scalar( $incoming[ $checkbox_key ] ) ) ? (string) $incoming[ $checkbox_key ] : 'no';
                    $settings[ $checkbox_key ] = sanitize_text_field( $checkbox );
                    break;
                case 'list':
                case 'list_input':
                case 'mixed':
                    $settings[ $name ] = array();
                    $text_columns      = array();
                    foreach ( ( isset( $setting['columns'] ) && is_array( $setting['columns'] ) ) ? $setting['columns'] : array() as $column => $column_name ) {
                        $items                        = ( is_array( $value ) && isset( $value[ $column ] ) && is_array( $value[ $column ] ) ) ? $value[ $column ] : array();
                        $settings[ $name ][ $column ] = array();
                        foreach ( $items as $item ) {
                            if ( is_scalar( $item ) ) {
                                $settings[ $name ][ $column ][] = sanitize_text_field( (string) $item );
                            }
                        }
                        if ( ! is_array( $column_name ) || ! isset( $column_name['type'] ) || $column_name['type'] !== 'checkbox' ) {
                            $text_columns[] = $column;
                        }
                    }
                    // Columns of different lengths would move values to other rows (custom language codes and names).
                    $rows = 0;
                    foreach ( $text_columns as $column ) {
                        $rows = max( $rows, count( $settings[ $name ][ $column ] ) );
                    }
                    foreach ( $text_columns as $column ) {
                        $settings[ $name ][ $column ] = array_pad( $settings[ $name ][ $column ], $rows, '' );
                    }
                    // Like the Advanced tab: empty rows go, unless the setting has a checkbox column.
                    if ( count( $text_columns ) === count( $settings[ $name ] ) ) {
                        for ( $row = $rows - 1; $row >= 0; $row-- ) {
                            if ( implode( '', array_column( $settings[ $name ], $row ) ) === '' ) {
                                foreach ( $text_columns as $column ) {
                                    array_splice( $settings[ $name ][ $column ], $row, 1 );
                                }
                            }
                        }
                    }
                    break;
            }

            // Program storage, not user input: keep this site's value.
            if ( isset( $setting['data_model'] ) && $setting['data_model'] === 'not_updatable_by_user' && isset( $previous[ $name ] ) ) {
                $settings[ $name ] = $previous[ $name ];
            }
        }

        // Custom language codes become table names, so they get the same check the Advanced tab runs on save.
        if ( isset( $settings['custom_language'] ) && function_exists( 'trp_verify_custom_language_codes' ) ) {
            $verified = trp_verify_custom_language_codes( true, $settings );
            if ( is_array( $verified ) && empty( $verified['correct_code'] ) ) {
                $warnings[] = sprintf(
                    /* translators: %s is the reason the custom language codes were rejected. */
                    __( 'The custom languages in the file were not imported: %s', 'translatepress-multilingual' ),
                    isset( $verified['message'] ) ? $verified['message'] : ''
                );
                if ( isset( $previous['custom_language'] ) ) {
                    $settings['custom_language'] = $previous['custom_language'];
                } else {
                    unset( $settings['custom_language'] );
                }
            }
        }

        // Consent to marketing emails belongs to this site's administrator, not to the file.
        if ( isset( $previous['plugin_optin_setting'] ) ) {
            $settings['plugin_optin_setting'] = $previous['plugin_optin_setting'];
        } else {
            unset( $settings['plugin_optin_setting'] );
        }

        if ( array_diff_key( $incoming, $known ) ) {
            $warnings[] = __( 'Some advanced settings in the file are not used by this version of TranslatePress and were ignored.', 'translatepress-multilingual' );
        }

        return $settings;
    }

    // Saves the already sanitized advanced settings without the sanitize callback register_setting() added.
    protected function update_advanced_settings( $settings ) {
        $callback = array( TRP_Translate_Press::get_trp_instance()->get_component( 'advanced_tab' ), 'sanitize_settings' );
        $priority = has_filter( 'sanitize_option_trp_advanced_settings', $callback );
        if ( $priority !== false ) {
            remove_filter( 'sanitize_option_trp_advanced_settings', $callback, $priority );
        }
        update_option( 'trp_advanced_settings', $settings );
        if ( $priority !== false ) {
            add_filter( 'sanitize_option_trp_advanced_settings', $callback, $priority );
        }
    }

    // Each scope through the Language Switcher tab's sanitize_scope_config(), as its AJAX save does; a scope that fails keeps this site's value.
    protected function sanitize_language_switcher_settings( $settings ) {
        $current = get_option( 'trp_language_switcher_settings', array() );
        if ( ! is_array( $current ) ) {
            $current = array();
        }
        $ls_tab = TRP_Translate_Press::get_trp_instance()->get_component( 'language_switcher_tab' );
        foreach ( array( 'floater', 'shortcode', 'menu' ) as $scope ) {
            if ( ! isset( $settings[ $scope ] ) || ! is_array( $settings[ $scope ] ) ) {
                continue;
            }
            try {
                $current[ $scope ] = $ls_tab->sanitize_scope_config( $scope, $settings[ $scope ] );
            } catch ( \Throwable $e ) {
                continue;
            }
        }
        return $current;
    }

    // term => array( language => translated term ); the glossary page saves one term at a time through AJAX, so this is the import's own.
    protected function sanitize_glossary( $glossary ) {
        $clean = array();
        foreach ( $glossary as $term => $translations ) {
            if ( ! is_string( $term ) || $term === '' || ! is_array( $translations ) ) {
                continue;
            }
            $term_translations = array();
            foreach ( $translations as $language_code => $translation ) {
                if ( ! is_string( $language_code ) || ! trp_is_valid_language_code( $language_code ) || ! is_string( $translation ) ) {
                    continue;
                }
                $term_translations[ $language_code ] = sanitize_text_field( $translation );
            }
            if ( ! empty( $term_translations ) ) {
                $clean[ sanitize_text_field( $term ) ] = $term_translations;
            }
        }
        return $clean;
    }

    // This site's MT settings with only PORTABLE_MT_KEYS taken from the file; TRP_Machine_Translation_Tab::sanitize_settings() then runs on them.
    protected function merge_machine_translation_settings( $incoming ) {
        $merged = get_option( 'trp_machine_translation_settings', array() );
        if ( ! is_array( $merged ) ) {
            $merged = array();
        }
        foreach ( self::PORTABLE_MT_KEYS as $key ) {
            if ( isset( $incoming[ $key ] ) && is_scalar( $incoming[ $key ] ) ) {
                $merged[ $key ] = sanitize_text_field( (string) $incoming[ $key ] );
            }
        }
        return $merged;
    }
}
