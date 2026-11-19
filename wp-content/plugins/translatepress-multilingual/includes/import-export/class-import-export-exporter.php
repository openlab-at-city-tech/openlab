<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Builds and runs the export steps, then zips the result.
class TRP_Import_Export_Exporter {

    // Uncompressed bytes added to the zip per call: about a second of compression.
    const ZIP_BATCH_BYTES = 33554432;

    // Every progress reload renders the export card, so its counts are cached until the next export or import.
    const COUNTS_TRANSIENT = 'trp_import_export_row_counts';
    const COUNTS_TTL       = 600;

    protected $settings;
    protected $db;
    protected $trp_query;
    protected $archive;

    public function __construct( $settings ) {
        global $wpdb;
        $this->settings  = $settings;
        $this->db        = $wpdb;
        $this->trp_query = TRP_Translate_Press::get_trp_instance()->get_component( 'query' );
        $this->archive   = new TRP_Import_Export_Archive();
    }

    public static function get_batch_size() {
        return max( 1, (int) apply_filters( 'trp_import_export_batch_size', 1000 ) );
    }

    public static function flush_row_counts() {
        delete_transient( self::COUNTS_TRANSIENT );
    }

    // Slugs are exported only where they can be imported back: with the SEO Pack add-on active.
    protected function slug_tables_exist() {
        return class_exists( 'TRP_Slug_Query' ) && $this->trp_query->table_exists( $this->db->prefix . 'trp_slug_originals', true );
    }

    // Table names are lowercase, so the codes are mapped back to the real ones.
    protected function get_table_languages( $tables, $prefix ) {
        $codes = array_keys( (array) TRP_Translate_Press::get_trp_instance()->get_component( 'languages' )->get_languages( 'english_name' ) );
        foreach ( array( 'translation-languages', 'publish-languages' ) as $key ) {
            if ( ! empty( $this->settings[ $key ] ) && is_array( $this->settings[ $key ] ) ) {
                $codes = array_merge( $codes, $this->settings[ $key ] );
            }
        }
        $map = array();
        foreach ( $codes as $code ) {
            if ( is_string( $code ) && trp_is_valid_language_code( $code ) ) {
                $map[ strtolower( $code ) ] = $code;
            }
        }

        $languages = array();
        foreach ( $tables as $table ) {
            $code = ( strpos( $table, $prefix ) === 0 ) ? substr( $table, strlen( $prefix ) ) : '';
            if ( $code !== '' && trp_is_valid_language_code( $code ) ) {
                $languages[] = isset( $map[ $code ] ) ? $map[ $code ] : $code;
            }
        }
        return array_values( array_unique( $languages ) );
    }

    // One step per table with rows to export, in archive order, without totals.
    protected function get_data_steps() {
        $dictionary = $this->get_table_languages( $this->trp_query->get_all_table_names( $this->settings['default-language'] ), $this->trp_query->get_table_name( '', null, true ) );
        $gettext    = $this->get_table_languages( $this->trp_query->get_all_gettext_table_names(), $this->db->get_blog_prefix() . 'trp_gettext_' );
        // Tables left behind by languages the site no longer uses are not exported.
        $site       = (array) $this->settings['translation-languages'];
        $dictionary = array_values( array_intersect( $dictionary, $site ) );
        $gettext    = array_values( array_intersect( $gettext, $site ) );
        $names      = TRP_Import_Export_Writer::language_labels( array_merge( $dictionary, $gettext ) );

        $steps = array();
        foreach ( $dictionary as $code ) {
            /* translators: %s is a language name. */
            $steps[] = $this->new_step( 'dictionary:' . $code, sprintf( __( 'Exporting page translations for %s', 'translatepress-multilingual' ), $names[ $code ] ), array( 'entry' => 'dictionary/' . $code . '.jsonl', 'language' => $code ) );
        }
        if ( $this->trp_query->table_exists( $this->trp_query->get_table_name_for_original_meta(), true ) ) {
            $steps[] = $this->new_step( 'original_meta', __( 'Finishing page translations', 'translatepress-multilingual' ), array( 'entry' => 'original-meta.jsonl' ) );
        }
        foreach ( $gettext as $code ) {
            /* translators: %s is a language name. */
            $steps[] = $this->new_step( 'gettext:' . $code, sprintf( __( 'Exporting theme and plugin strings for %s', 'translatepress-multilingual' ), $names[ $code ] ), array( 'entry' => 'gettext/' . $code . '.jsonl', 'language' => $code ) );
        }
        if ( $this->trp_query->table_exists( $this->trp_query->get_table_name_for_gettext_original_meta(), true ) ) {
            $steps[] = $this->new_step( 'gettext_original_meta', __( 'Finishing theme and plugin strings', 'translatepress-multilingual' ), array( 'entry' => 'gettext-original-meta.jsonl' ) );
        }
        if ( $this->slug_tables_exist() ) {
            $steps[] = $this->new_step( 'slugs', __( 'Exporting translated slugs', 'translatepress-multilingual' ), array( 'entry' => 'slugs.jsonl' ) );
        }
        return $steps;
    }

    protected function new_step( $key, $label, $extra = array() ) {
        return array_merge( array( 'key' => $key, 'label' => $label, 'total' => 1, 'processed' => 0, 'done' => false, 'rows' => 0 ), $extra );
    }

    protected function get_step_type( $step ) {
        $parts = explode( ':', $step['key'] );
        return $parts[0];
    }

    // Rows per data step (same FROM and WHERE as the export query), plus translated slugs per language.
    protected function get_counts( $steps ) {
        $counts = get_transient( self::COUNTS_TRANSIENT );
        if ( is_array( $counts ) && array_keys( $counts['steps'] ) === array_column( $steps, 'key' ) ) {
            return $counts;
        }

        $counts = array( 'steps' => array(), 'slugs_by_language' => array() );
        foreach ( $steps as $step ) {
            $query                           = $this->get_step_query( $step );
            $counts['steps'][ $step['key'] ] = (int) $this->db->get_var( "SELECT COUNT(*) FROM {$query['from']} WHERE {$query['where']}" );//phpcs:ignore
        }
        if ( isset( $counts['steps']['slugs'] ) ) {
            $rows = $this->db->get_results( "SELECT language, COUNT(*) AS total FROM `" . sanitize_text_field( $this->db->prefix . 'trp_slug_translations' ) . "` WHERE translated <> '' GROUP BY language", ARRAY_A );//phpcs:ignore
            foreach ( (array) $rows as $row ) {
                if ( is_string( $row['language'] ) && trp_is_valid_language_code( $row['language'] ) ) {
                    $counts['slugs_by_language'][ $row['language'] ] = (int) $row['total'];
                }
            }
        }

        set_transient( self::COUNTS_TRANSIENT, $counts, self::COUNTS_TTL );
        return $counts;
    }

    // What the export card lists: rows per language and kind, null where the language has no table of that kind.
    public function get_export_summary() {
        $counts = $this->get_counts( $this->get_data_steps() );
        $slugs  = isset( $counts['steps']['slugs'] );

        $by_code = array();
        foreach ( $counts['steps'] as $key => $total ) {
            $parts = explode( ':', $key );
            if ( isset( $parts[1] ) ) {
                $by_code[ $parts[1] ][ $parts[0] ] = $total;
            }
        }
        foreach ( array_keys( $counts['slugs_by_language'] ) as $code ) {
            if ( ! isset( $by_code[ $code ] ) ) {
                $by_code[ $code ] = array();
            }
        }

        $names     = TRP_Import_Export_Writer::language_labels( array_keys( $by_code ) );
        $languages = array();
        foreach ( $by_code as $code => $row ) {
            $languages[] = array(
                'code'            => $code,
                'name'            => $names[ $code ],
                'dictionary_rows' => isset( $row['dictionary'] ) ? $row['dictionary'] : null,
                'gettext_rows'    => isset( $row['gettext'] ) ? $row['gettext'] : null,
                'slug_rows'       => $slugs ? ( isset( $counts['slugs_by_language'][ $code ] ) ? $counts['slugs_by_language'][ $code ] : 0 ) : null,
            );
        }

        return array(
            'default_language' => $this->settings['default-language'],
            'languages'        => $languages,
            'slugs_rows'       => $slugs ? array_sum( $counts['slugs_by_language'] ) : null,
        );
    }

    // Settings, one step per table, then the zip.
    public function build_steps() {
        $steps  = $this->get_data_steps();
        $counts = $this->get_counts( $steps );
        foreach ( $steps as $index => $step ) {
            $steps[ $index ]['total'] = $counts['steps'][ $step['key'] ];
        }
        array_unshift( $steps, $this->new_step( 'settings', __( 'Exporting settings', 'translatepress-multilingual' ), array( 'entry' => TRP_Import_Export_Archive::SETTINGS ) ) );
        $steps[] = $this->new_step( 'finish', __( 'Creating the export file', 'translatepress-multilingual' ) );
        return $steps;
    }

    // One batch of the job's current step.
    public function process_batch( $job ) {
        $step = $job->get_current_step();
        if ( $step['key'] === 'settings' ) {
            return $this->process_settings_step( $job );
        }
        if ( $step['key'] === 'finish' ) {
            return $this->process_finish_step( $job );
        }
        return $this->process_rows_step( $job, $step );
    }

    protected function process_settings_step( $job ) {
        // Secrets are stripped per option: the glossary and the slug maps are keyed by user text, so a term called "License" must survive.
        $bundle = array(
            'trp_settings'                     => $this->strip_secrets( get_option( 'trp_settings', array() ) ),
            'trp_advanced_settings'            => $this->strip_secrets( get_option( 'trp_advanced_settings', array() ) ),
            'trp_machine_translation_settings' => $this->strip_secrets( get_option( 'trp_machine_translation_settings', array() ) ),
            'trp_language_switcher_settings'   => $this->strip_secrets( get_option( 'trp_language_switcher_settings', array() ) ),
            'trp_glossary'                     => get_option( 'trp_glossary', null ),
            'trp_ald_settings'                 => get_option( 'trp_ald_settings', null ),
            // Which add-ons are active.
            'trp_add_ons_settings'             => get_option( 'trp_add_ons_settings', null ),
        );
        // Only when the user ticked "Include the license key and API keys".
        if ( isset( $job->options['include_keys'] ) && $job->options['include_keys'] === 'yes' ) {
            $bundle['keys'] = $this->get_keys();
        }
        if ( $this->slug_tables_exist() ) {
            $bundle['trp_post_type_base_slug_translation'] = get_option( 'trp_post_type_base_slug_translation', null );
            $bundle['trp_taxonomy_slug_translation']       = get_option( 'trp_taxonomy_slug_translation', null );
        }

        if ( file_put_contents( $job->dir . TRP_Import_Export_Archive::SETTINGS, wp_json_encode( $bundle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) === false ) {//phpcs:ignore
            return $this->write_error();
        }

        $job->update_current_step( array( 'processed' => 1, 'done' => true, 'rows' => 1 ) );
        return array( 'processed' => 1, 'done' => true );
    }

    // The license key and the automatic translation API keys this site has, by option key.
    public function get_keys() {
        $mt_settings = get_option( 'trp_machine_translation_settings', array() );
        $keys        = array( TRP_Import_Export_Writer::LICENSE_KEY => get_option( TRP_Import_Export_Writer::LICENSE_KEY, '' ) );
        foreach ( TRP_Import_Export_Writer::API_KEYS as $key ) {
            $keys[ $key ] = ( is_array( $mt_settings ) && isset( $mt_settings[ $key ] ) ) ? $mt_settings[ $key ] : '';
        }
        return array_filter( $keys, function ( $value ) {
            return is_string( $value ) && trim( $value ) !== '';
        } );
    }

    // Drops API keys, licenses, tokens and email addresses from one option, $depth levels down, so leaf maps keyed by user text are never reached.
    protected function strip_secrets( $settings, $depth = 1 ) {
        if ( ! is_array( $settings ) ) {
            return array();
        }
        // The usage-alert email is never imported, so it stays out of the file too.
        $explicit = array( 'deepl-api-key', 'google-translate-key', 'trp-ai-api-key', 'ai_words_notification_email' );
        foreach ( $settings as $key => $value ) {
            if ( in_array( $key, $explicit, true ) || preg_match( TRP_Import_Export_Writer::SECRET_KEY_PATTERN, (string) $key ) ) {
                unset( $settings[ $key ] );
                continue;
            }
            if ( $depth > 0 && is_array( $value ) ) {
                $settings[ $key ] = $this->strip_secrets( $value, $depth - 1 );
            }
        }
        return $settings;
    }

    // A failed read returns an empty array, which must not pass for "no more rows".
    protected function read_error() {
        return new WP_Error( 'trp_ie_db_read', __( 'The export stopped because of a database error. Generate the export file again.', 'translatepress-multilingual' ) );
    }

    protected function write_error() {
        return new WP_Error( 'trp_ie_write', __( 'The export file could not be created. Check that the uploads folder is writable.', 'translatepress-multilingual' ) );
    }

    // One keyset-paginated batch of a data step, appended to its entry as JSON lines.
    protected function process_rows_step( $job, $step ) {
        $type    = $this->get_step_type( $step );
        $query   = $this->get_step_query( $step );
        $last_id = isset( $job->cursor['last_id'] ) ? (int) $job->cursor['last_id'] : 0;
        $rows    = $this->db->get_results(
            $this->db->prepare(
                "SELECT {$query['select']} FROM {$query['from']} WHERE {$query['id']} > %d AND {$query['where']} ORDER BY {$query['id']} ASC LIMIT %d",//phpcs:ignore
                $last_id,
                self::get_batch_size()
            ),
            ARRAY_A
        );
        if ( $this->db->last_error !== '' ) {
            return $this->read_error();
        }
        if ( empty( $rows ) ) {
            $job->update_current_step( array( 'done' => true ) );
            return array( 'processed' => 0, 'done' => true );
        }

        $translations = ( $type === 'slugs' ) ? $this->get_slug_translations( array_column( $rows, 'id' ) ) : array();
        if ( is_wp_error( $translations ) ) {
            return $translations;
        }

        $lines = array();
        foreach ( $rows as $row ) {
            $data = $this->map_row( $type, $row, $translations );
            if ( $data !== null ) {
                $lines[] = TRP_Import_Export_Archive::encode_line( $data );
            }
        }
        $appended = $this->append_lines( $job, $step['entry'], $lines );
        if ( is_wp_error( $appended ) ) {
            return $appended;
        }

        $job->cursor['last_id'] = (int) $rows[ count( $rows ) - 1 ]['id'];
        $job->update_current_step( array(
            'processed' => $step['processed'] + count( $rows ),
            'rows'      => $step['rows'] + count( $lines ),
        ) );
        return array( 'processed' => count( $rows ), 'done' => false );
    }

    // select, from, id and where of the step's query; the select names the keyset column `id`.
    protected function get_step_query( $step ) {
        switch ( $this->get_step_type( $step ) ) {
            case 'dictionary':
                return $this->dictionary_query( $step['language'] );
            case 'gettext':
                return $this->gettext_query( $step['language'] );
            case 'original_meta':
                return $this->original_meta_query();
            case 'gettext_original_meta':
                return $this->gettext_original_meta_query();
            default:
                return $this->slugs_query();
        }
    }

    // One DB row as written to the archive, or null to leave it out.
    protected function map_row( $type, $row, $slug_translations ) {
        switch ( $type ) {
            case 'dictionary':
                return $this->dictionary_row( $row );
            case 'gettext':
                return $this->gettext_row( $row );
            case 'original_meta':
                return $this->original_meta_row( $row );
            case 'gettext_original_meta':
                return $this->gettext_original_meta_row( $row );
            default:
                return $this->slugs_row( $row, $slug_translations );
        }
    }

    protected function dictionary_query( $language_code ) {
        return array(
            'select' => 'id, original, translated, status, block_type',
            'from'   => '`' . sanitize_text_field( $this->trp_query->get_table_name( $language_code ) ) . '`',
            'id'     => 'id',
            'where'  => "NOT ( status = 0 AND block_type = 0 AND ( translated IS NULL OR translated = '' ) )",
        );
    }

    protected function dictionary_row( $row ) {
        if ( $row['original'] === null || $row['original'] === '' ) {
            return null;
        }
        return array(
            'o' => $row['original'],
            't' => ( $row['translated'] === null ) ? null : (string) $row['translated'],
            's' => (int) $row['status'],
            'b' => (int) $row['block_type'],
        );
    }

    protected function gettext_query( $language_code ) {
        return array(
            'select' => "tt.id, COALESCE( NULLIF( tt.original, '' ), ot.original ) AS original, tt.translated, COALESCE( NULLIF( tt.domain, '' ), ot.domain ) AS domain, ot.context, tt.plural_form, ot.original_plural, tt.status",
            'from'   => '`' . sanitize_text_field( $this->trp_query->get_gettext_table_name( $language_code ) ) . '` AS tt LEFT JOIN `' . sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() ) . '` AS ot ON tt.original_id = ot.id',
            'id'     => 'tt.id',
            'where'  => "NOT ( tt.status = 0 AND ( tt.translated IS NULL OR tt.translated = '' ) )",
        );
    }

    protected function gettext_row( $row ) {
        if ( $row['original'] === null || $row['original'] === '' ) {
            return null;
        }
        return array(
            'o'  => $row['original'],
            't'  => ( $row['translated'] === null ) ? null : (string) $row['translated'],
            'd'  => ( $row['domain'] === null ) ? '' : (string) $row['domain'],
            'c'  => ( $row['context'] === null || $row['context'] === '' ) ? null : (string) $row['context'],
            'p'  => (int) $row['plural_form'],
            'op' => ( $row['original_plural'] === null || $row['original_plural'] === '' ) ? null : (string) $row['original_plural'],
            's'  => (int) $row['status'],
        );
    }

    protected function original_meta_query() {
        return array(
            'select' => 'm.meta_id AS id, o.original, m.meta_key, m.meta_value',
            'from'   => '`' . sanitize_text_field( $this->trp_query->get_table_name_for_original_meta() ) . '` AS m INNER JOIN `' . sanitize_text_field( $this->trp_query->get_table_name_for_original_strings() ) . '` AS o ON m.original_id = o.id',
            'id'     => 'm.meta_id',
            'where'  => '1 = 1',
        );
    }

    protected function original_meta_row( $row ) {
        if ( $row['original'] === null || $row['original'] === '' || $row['meta_key'] === null || $row['meta_key'] === '' ) {
            return null;
        }
        return array(
            'o' => $row['original'],
            'k' => (string) $row['meta_key'],
            'v' => ( $row['meta_value'] === null ) ? '' : (string) $row['meta_value'],
        );
    }

    protected function gettext_original_meta_query() {
        return array(
            'select' => 'm.meta_id AS id, ot.original, ot.domain, ot.context, m.meta_key, m.meta_value',
            'from'   => '`' . sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_meta() ) . '` AS m INNER JOIN `' . sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() ) . '` AS ot ON m.original_id = ot.id',
            'id'     => 'm.meta_id',
            'where'  => '1 = 1',
        );
    }

    protected function gettext_original_meta_row( $row ) {
        if ( $row['original'] === null || $row['original'] === '' || $row['meta_key'] === null || $row['meta_key'] === '' ) {
            return null;
        }
        return array(
            'o' => $row['original'],
            'd' => ( $row['domain'] === null ) ? '' : (string) $row['domain'],
            'c' => ( $row['context'] === null || $row['context'] === '' ) ? null : (string) $row['context'],
            'k' => (string) $row['meta_key'],
            'v' => ( $row['meta_value'] === null ) ? '' : (string) $row['meta_value'],
        );
    }

    protected function slugs_query() {
        return array(
            'select' => 'id, original, type',
            'from'   => '`' . sanitize_text_field( $this->db->prefix . 'trp_slug_originals' ) . '`',
            'id'     => 'id',
            'where'  => '1 = 1',
        );
    }

    protected function slugs_row( $row, $translations ) {
        if ( $row['original'] === null || $row['original'] === '' ) {
            return null;
        }
        return array(
            'o'    => (string) $row['original'],
            'type' => ( $row['type'] === null || $row['type'] === '' ) ? 'other' : (string) $row['type'],
            'tr'   => isset( $translations[ (int) $row['id'] ] ) ? $translations[ (int) $row['id'] ] : array(),
        );
    }

    // original_id => language => array( 't', 's' ) for one batch of slug originals.
    protected function get_slug_translations( $ids ) {
        $placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
        $results      = $this->db->get_results(
            $this->db->prepare(
                "SELECT original_id, translated, language, status FROM `" . sanitize_text_field( $this->db->prefix . 'trp_slug_translations' ) . "` WHERE original_id IN ( $placeholders ) ORDER BY id ASC",//phpcs:ignore
                $ids
            ),
            ARRAY_A
        );
        if ( $this->db->last_error !== '' ) {
            return $this->read_error();
        }

        $translations = array();
        foreach ( $results as $result ) {
            $language = (string) $result['language'];
            if ( trp_is_valid_language_code( $language ) ) {
                $translations[ (int) $result['original_id'] ][ $language ] = array(
                    't' => ( $result['translated'] === null ) ? '' : (string) $result['translated'],
                    's' => (int) $result['status'],
                );
            }
        }
        return $translations;
    }

    // Writes the manifest and zips what the earlier steps wrote; a table without rows left no file.
    // Called until the zip holds every work file: a few files per call, so no request has to compress the whole export.
    protected function process_finish_step( $job ) {
        $entries = array( TRP_Import_Export_Archive::README, TRP_Import_Export_Archive::MANIFEST );
        $rows    = array();
        foreach ( $job->steps as $step ) {
            if ( empty( $step['entry'] ) ) {
                continue;
            }
            $entries[] = $step['entry'];
            if ( $step['entry'] !== TRP_Import_Export_Archive::SETTINGS && file_exists( $job->dir . $step['entry'] ) ) {
                $rows[ $step['entry'] ] = array( 'rows' => (int) $step['rows'] );
            }
        }

        // First call: every work file is still on disk, so the manifest can list them.
        if ( $job->file === '' ) {
            // Only format_version, default_language and files are read on import; site_url and exported_at are shown on the review.
            $manifest = array(
                'format_version'        => TRP_Import_Export_Archive::FORMAT_VERSION,
                'plugin_version'        => TRP_PLUGIN_VERSION,
                // The free version can't import a file made by a paid one.
                'product'               => TRP_Import_Export_Writer::get_site_product(),
                'keys_included'         => isset( $job->options['include_keys'] ) && $job->options['include_keys'] === 'yes',
                'exported_at'           => gmdate( 'c' ),
                'site_url'              => site_url(),
                'home_url'              => home_url(),
                'default_language'      => $this->settings['default-language'],
                'translation_languages' => isset( $this->settings['translation-languages'] ) ? array_values( (array) $this->settings['translation-languages'] ) : array(),
                'publish_languages'     => isset( $this->settings['publish-languages'] ) ? array_values( (array) $this->settings['publish-languages'] ) : array(),
                'url_slugs'             => isset( $this->settings['url-slugs'] ) ? (array) $this->settings['url-slugs'] : array(),
                'files'                 => $rows,
                'settings_included'     => file_exists( $job->dir . TRP_Import_Export_Archive::SETTINGS ),
                'seo_pack_active'       => $this->slug_tables_exist(),
            );
            if ( file_put_contents( $job->dir . TRP_Import_Export_Archive::MANIFEST, wp_json_encode( $manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) === false ) {//phpcs:ignore
                return $this->write_error();
            }
            // Explains the files to a translator; the export is still complete without it.
            @copy( TRP_PLUGIN_DIR . 'includes/import-export/export-readme.txt', $job->dir . TRP_Import_Export_Archive::README );//phpcs:ignore
            $host      = preg_replace( '/[^A-Za-z0-9\-\.]/', '-', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
            $job->file = 'translatepress-export-' . $host . '-' . wp_date( 'Ymd-His' ) . '.zip';
        }

        // A work file is deleted once it is in the zip, so the ones left are the ones still to add.
        $batch   = array();
        $bytes   = 0;
        $pending = 0;
        foreach ( $entries as $entry ) {
            if ( ! file_exists( $job->dir . $entry ) ) {
                continue;
            }
            $pending += filesize( $job->dir . $entry );
            if ( ! $batch || $bytes < self::ZIP_BATCH_BYTES ) {
                $batch[ $entry ] = $job->dir . $entry;
                $bytes          += filesize( $job->dir . $entry );
            }
        }

        if ( $batch ) {
            $added = $this->archive->add_to_zip( $job->dir . $job->file, $batch );
            if ( is_wp_error( $added ) ) {
                return $added;
            }
            foreach ( $batch as $path ) {
                @unlink( $path );//phpcs:ignore
            }
            $total = isset( $job->cursor['zip_bytes'] ) ? $job->cursor['zip_bytes'] : $pending;
            $job->cursor['zip_bytes'] = $total;
            /* translators: %d is a percentage. */
            $job->update_current_step( array( 'label' => sprintf( __( 'Creating the export file: %d%%', 'translatepress-multilingual' ), round( 100 * ( $total - $pending + $bytes ) / max( 1, $total ) ) ) ) );
            if ( $pending > $bytes ) {
                return array( 'processed' => 0, 'done' => false );
            }
        }

        $job->file_size = (int) filesize( $job->dir . $job->file );
        $job->status    = 'ready';
        $job->update_current_step( array( 'processed' => 1, 'done' => true, 'rows' => 1 ) );
        return array( 'processed' => 1, 'done' => true );
    }

    // The file is created even when the batch had no lines to write.
    protected function append_lines( $job, $entry, $lines ) {
        $path = $job->dir . $entry;
        if ( ! wp_mkdir_p( dirname( $path ) ) ) {
            return $this->write_error();
        }
        $job->protect_dir();
        if ( file_put_contents( $path, empty( $lines ) ? '' : implode( "\n", $lines ) . "\n", FILE_APPEND ) === false ) {//phpcs:ignore
            return $this->write_error();
        }
        return true;
    }
}
