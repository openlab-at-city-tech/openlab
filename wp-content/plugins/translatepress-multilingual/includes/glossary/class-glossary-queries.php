<?php

if ( !defined( 'ABSPATH' ) )
    exit();

/**
 * Class TRP_Glossary_Queries
 *
 * Handles persistence of glossary terms in the `trp_glossary` option and the
 * admin-ajax endpoints that back the glossary settings subpage.
 *
 * Storage structure:
 *   array(
 *       'default language term' => array(
 *           'fr_FR' => 'translated_term',
 *           'de_DE' => 'translated_term2',
 *       ),
 *       ...
 *   )
 *
 * The default-language term is the top-level key, so its uniqueness is
 * guaranteed by the uniqueness of array keys. Language codes without a
 * provided translation are not stored.
 */
class TRP_Glossary_Queries {

    /**
     * Option name used to store the glossary terms.
     */
    const OPTION_NAME = 'trp_glossary';

    /**
     * Ajax action / nonce names.
     */
    const GET_TERMS_ACTION   = 'trp_glossary_get_terms';
    const ADD_TERM_ACTION    = 'trp_glossary_add_term';
    const EDIT_TERM_ACTION   = 'trp_glossary_edit_term';
    const DELETE_TERM_ACTION = 'trp_glossary_delete_term';
    const REPLACE_ACTION     = 'trp_glossary_replace_in_dictionary';
    const SEARCH_ACTION      = 'trp_glossary_search_dictionary';

    /**
     * Slug of the hidden "search & replace in existing translations" page.
     */
    const REPLACE_PAGE_SLUG = 'trp_glossary_replace';

    /**
     * Batch-processing constants for the dictionary replacement.
     *
     * The replace runs one bounded pass synchronously in the request (so small/medium
     * jobs finish immediately); anything left over is handed to TRP_Batch_Processor
     * (cron), which continues it via the same id-cursor batch method.
     */
    const BATCH_FLAG      = 'trp_glossary_replace_batch'; // batch-processor flag option
    const BATCH_JOB       = 'trp_glossary_replace_job';   // job state (per-language cursors)
    const SYNC_TIME_LIMIT = 5.0;                          // seconds budget for the in-request pass
    const SYNC_BATCH_SIZE = 200;                          // rows fetched per query in-request

    /**
     * Full trp settings array (passed in like the other tabs).
     *
     * @var array
     */
    private $settings;

    public function __construct( $settings = array() ) {
        $this->settings = $settings;

        // Make the dictionary-replace batch task known to TRP_Batch_Processor (cron).
        add_filter( 'trp_batch_upgrade_tasks', array( $this, 'register_batch_task' ) );
    }

    /**
     * Register the glossary dictionary-replace task with the batch processor.
     *
     * Hooked to trp_batch_upgrade_tasks.
     *
     * @param array $tasks
     * @return array
     */
    public function register_batch_task( $tasks ) {
        $tasks[ self::BATCH_FLAG ] = array(
            'file'              => 'tasks/class-glossary-replace-task.php',
            'class'             => 'TRP_Glossary_Replace_Task',
            'completion_notice' => __( 'TranslatePress finished replacing the existing translations in your dictionary.', 'translatepress-multilingual' ),
        );
        return $tasks;
    }

    /**
     * Enqueue the glossary subpage script. Hooked to admin_enqueue_scripts.
     *
     * @param string $hook Current admin page hook suffix.
     */
    public function enqueue_scripts( $hook = null ) {
        if ( ! is_string( $hook ) && function_exists( 'get_current_screen' ) ) {
            $screen = get_current_screen();
            $hook   = $screen ? $screen->id : '';
        }

        if ( $hook === 'admin_page_' . self::REPLACE_PAGE_SLUG ) {
            $this->enqueue_replace_scripts();
            return;
        }

        if ( $hook !== 'admin_page_trp_machine_translation_glossary' ) {
            return;
        }

        $default_language = isset( $this->settings['default-language'] ) ? $this->settings['default-language'] : '';
        $language_names   = $this->get_english_language_names();
        $default_name     = isset( $language_names[ $default_language ] ) ? $language_names[ $default_language ] : $default_language;

        // Ordered list of target languages (translation languages except the default).
        $target_languages = array();
        foreach ( $this->get_target_language_codes() as $code ) {
            $target_languages[] = array(
                'code' => $code,
                'name' => isset( $language_names[ $code ] ) ? $language_names[ $code ] : $code,
            );
        }

        wp_enqueue_script(
            'trp-glossary-script',
            TRP_PLUGIN_URL . 'assets/js/trp-glossary.js',
            array( 'jquery' ),
            TRP_PLUGIN_VERSION,
            true
        );

        wp_localize_script( 'trp-glossary-script', 'trp_glossary_data', array(
            'admin_ajax'        => admin_url( 'admin-ajax.php' ),
            'get_terms_nonce'   => wp_create_nonce( self::GET_TERMS_ACTION ),
            'add_term_nonce'    => wp_create_nonce( self::ADD_TERM_ACTION ),
            'edit_term_nonce'   => wp_create_nonce( self::EDIT_TERM_ACTION ),
            'delete_term_nonce' => wp_create_nonce( self::DELETE_TERM_ACTION ),
            'language_names'    => $language_names,
            'default_language'  => array( 'code' => $default_language, 'name' => $default_name ),
            'target_languages'  => $target_languages,
            'columns'           => array(
                /* translators: %s is the default language name. */
                'term'         => sprintf( esc_html__( 'Term (%s)', 'translatepress-multilingual' ), $default_name ),
                'translations' => esc_html__( 'Translations', 'translatepress-multilingual' ),
            ),
            // NB: these are injected into the DOM as text nodes on the JS side, so they use __()
            // (not esc_html__) — otherwise entities like & would be shown literally as &amp;.
            'i18n'              => array(
                'term_added'     => __( 'Term added. Glossary terms only affect new automatic translations.', 'translatepress-multilingual' ),
                'term_updated'   => __( 'Term updated.', 'translatepress-multilingual' ),
                'term_deleted'   => __( 'Term deleted.', 'translatepress-multilingual' ),
                'confirm_delete' => __( 'Are you sure you want to delete this term?', 'translatepress-multilingual' ),
                'generic_error'  => __( 'Something went wrong. Please try again.', 'translatepress-multilingual' ),
                'quick_edit'     => __( 'Quick Edit', 'translatepress-multilingual' ),
                'edit'           => __( 'Edit', 'translatepress-multilingual' ),
                'delete'         => __( 'Delete', 'translatepress-multilingual' ),
                'update'         => __( 'Update', 'translatepress-multilingual' ),
                'cancel'         => __( 'Cancel', 'translatepress-multilingual' ),
                'no_matches'     => __( 'No glossary terms match your search.', 'translatepress-multilingual' ),
                'no_items'       => __( 'No glossary terms found.', 'translatepress-multilingual' ),
                'load_error'     => __( 'Could not load glossary terms. Please reload the page.', 'translatepress-multilingual' ),
                'items'          => __( 'items', 'translatepress-multilingual' ),
                'of'             => __( 'of', 'translatepress-multilingual' ),
                'first_page'     => __( 'First page', 'translatepress-multilingual' ),
                'prev_page'      => __( 'Previous page', 'translatepress-multilingual' ),
                'next_page'      => __( 'Next page', 'translatepress-multilingual' ),
                'last_page'      => __( 'Last page', 'translatepress-multilingual' ),
                /* translators: %s is the "search & replace tool" link. */
                'found_message'  => __( 'The inserted term was found in your existing translations, use the %s to make changes there.', 'translatepress-multilingual' ),
                'replace_link'   => __( 'search & replace tool', 'translatepress-multilingual' ),
            ),
        ) );
    }

    /**
     * Enqueue assets for the hidden search & replace page.
     */
    private function enqueue_replace_scripts() {
        wp_enqueue_style(
            'trp-settings-style',
            TRP_PLUGIN_URL . 'assets/css/trp-back-end-style.css',
            array(),
            TRP_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'trp-glossary-replace-script',
            TRP_PLUGIN_URL . 'assets/js/trp-glossary-replace.js',
            array( 'jquery' ),
            TRP_PLUGIN_VERSION,
            true
        );

        wp_localize_script( 'trp-glossary-replace-script', 'trp_glossary_replace_data', array(
            'admin_ajax'    => admin_url( 'admin-ajax.php' ),
            'replace_nonce' => wp_create_nonce( self::REPLACE_ACTION ),
            'search_nonce'  => wp_create_nonce( self::SEARCH_ACTION ),
            // Injected as text nodes in JS, so plain __() (not esc_html__).
            'i18n'          => array(
                'generic_error'  => __( 'Something went wrong. Please try again.', 'translatepress-multilingual' ),
                'nothing'        => __( 'Please provide at least one existing translation to replace.', 'translatepress-multilingual' ),
                'confirm_replace' => __( 'You are about to bulk edit a potentially large amount of translations. This action cannot be undone. It is recommended to make a backup of the database before completing this action.', 'translatepress-multilingual' ),
                'confirm_replace_question' => __( 'Continue with replacing existing translations?', 'translatepress-multilingual' ),
                'no_entries'     => __( 'No entries found.', 'translatepress-multilingual' ),
                'col_original'   => __( 'Original', 'translatepress-multilingual' ),
                'col_translated' => __( 'Translation', 'translatepress-multilingual' ),
                'items'          => __( 'items', 'translatepress-multilingual' ),
                'of'             => __( 'of', 'translatepress-multilingual' ),
                'first_page'     => __( 'First page', 'translatepress-multilingual' ),
                'prev_page'      => __( 'Previous page', 'translatepress-multilingual' ),
                'next_page'      => __( 'Next page', 'translatepress-multilingual' ),
                'last_page'      => __( 'Last page', 'translatepress-multilingual' ),
            ),
        ) );
    }

    /**
     * Ordered list of target language codes (translation languages minus default).
     *
     * @return array
     */
    private function get_target_language_codes() {
        $default_language      = isset( $this->settings['default-language'] ) ? $this->settings['default-language'] : '';
        $translation_languages = isset( $this->settings['translation-languages'] ) ? $this->settings['translation-languages'] : array();

        return array_values( array_diff( $translation_languages, array( $default_language ) ) );
    }

    /**
     * Return a map of language code => English language name for the languages
     * configured for translation (including the default language).
     *
     * @return array
     */
    public function get_english_language_names() {
        $default_language      = isset( $this->settings['default-language'] ) ? $this->settings['default-language'] : '';
        $translation_languages = isset( $this->settings['translation-languages'] ) ? $this->settings['translation-languages'] : array();

        $codes = array_unique( array_merge( array( $default_language ), $translation_languages ) );
        $codes = array_filter( $codes );

        if ( empty( $codes ) ) {
            return array();
        }

        $trp           = TRP_Translate_Press::get_trp_instance();
        $trp_languages = $trp->get_component( 'languages' );

        return $trp_languages->get_language_names( $codes, 'english_name' );
    }

    /**
     * Shared guard for the ajax endpoints: verify nonce + capability.
     * Sends a JSON error and exits on failure.
     *
     * @param string $action Nonce action name.
     */
    private function verify_request( $action ) {
        if ( ! check_ajax_referer( $action, 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Security check failed. Please reload the page and try again.', 'translatepress-multilingual' ) ), 403 );
        }

        if ( ! current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to do this.', 'translatepress-multilingual' ) ), 403 );
        }
    }

    /**
     * Parse and validate the submitted `term` field into a sanitized default
     * term + translations map. Sends a JSON error and exits on validation
     * failure (empty default term, or no translations provided).
     *
     * @return array [ string $default_term, array $translations ]
     */
    private function parse_submitted_term() {
        $default_language = isset( $this->settings['default-language'] ) ? $this->settings['default-language'] : '';
        $target_languages = $this->get_target_language_codes();

        $submitted_term = ( isset( $_POST['term'] ) && is_array( $_POST['term'] ) )
            ? array_map( 'sanitize_text_field', wp_unslash( $_POST['term'] ) )
            : array();

        // Sanitize + trim the default-language term (the key / uniqueness anchor).
        $default_term = isset( $submitted_term[ $default_language ] ) ? trim( sanitize_text_field( $submitted_term[ $default_language ] ) ) : '';

        if ( $default_term === '' ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Please enter the term in the default language.', 'translatepress-multilingual' ) ) );
        }

        // Sanitize + trim each provided translation. Skip empty ones.
        $translations = array();
        foreach ( $target_languages as $language_code ) {
            if ( ! isset( $submitted_term[ $language_code ] ) ) {
                continue;
            }
            $value = trim( sanitize_text_field( $submitted_term[ $language_code ] ) );
            if ( $value !== '' ) {
                $translations[ $language_code ] = $value;
            }
        }

        if ( empty( $translations ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Please provide at least one translation for the term.', 'translatepress-multilingual' ) ) );
        }

        return array( $default_term, $translations );
    }

    /**
     * Load the glossary option as an array.
     *
     * @return array
     */
    private function get_glossary() {
        $glossary = get_option( self::OPTION_NAME, array() );
        return is_array( $glossary ) ? $glossary : array();
    }

    /**
     * Ajax handler: return the glossary terms for populating the table.
     *
     * Returns an ordered list of { term, translations } (empty translations
     * removed) so the client can render rows.
     *
     * Hooked to wp_ajax_trp_glossary_get_terms.
     */
    public function ajax_get_terms() {
        $this->verify_request( self::GET_TERMS_ACTION );

        $terms = array();
        foreach ( $this->get_glossary() as $term => $translations ) {
            $clean = array();
            if ( is_array( $translations ) ) {
                foreach ( $translations as $code => $value ) {
                    if ( $value !== '' && $value !== null ) {
                        $clean[ $code ] = $value;
                    }
                }
            }
            $terms[] = array(
                'term'         => (string) $term,
                'translations' => $clean,
            );
        }

        wp_send_json_success( array( 'terms' => $terms ) );
    }

    /**
     * Ajax handler: add a single glossary term.
     *
     * Hooked to wp_ajax_trp_glossary_add_term.
     */
    public function add_term() {
        $this->verify_request( self::ADD_TERM_ACTION );

        list( $default_term, $translations ) = $this->parse_submitted_term();

        $glossary = $this->get_glossary();

        if ( array_key_exists( $default_term, $glossary ) ) {
            wp_send_json_error( array( 'message' => sprintf(
                /* translators: %s is the glossary term. */
                esc_html__( 'The term “%s” already exists in the glossary.', 'translatepress-multilingual' ),
                $default_term
            ) ) );
        }

        $glossary[ $default_term ] = $translations;
        update_option( self::OPTION_NAME, $glossary, false );

        $found_in_dictionary = $this->term_exists_in_dictionaries( $default_term );

        wp_send_json_success( array(
            'term'                => $default_term,
            'translations'        => $translations,
            'found_in_dictionary' => $found_in_dictionary,
            'replace_url'         => $found_in_dictionary ? add_query_arg(
                array(
                    'page' => self::REPLACE_PAGE_SLUG,
                    'term' => $default_term,
                ),
                admin_url( 'admin.php' )
            ) : '',
        ) );
    }

    /**
     * Case-insensitive substring search for the term in the `original` column of
     * each target language's dictionary table. Stops at the first table with a
     * match. Case-insensitivity is forced with LOWER() so it does not depend on
     * the column collation.
     *
     * @param string $term Default-language term.
     * @return bool
     */
    private function term_exists_in_dictionaries( $term ) {
        global $wpdb;

        $query = $this->get_query();
        if ( ! $query ) {
            return false;
        }

        $like = '%' . $wpdb->esc_like( mb_strtolower( $term, 'UTF-8' ) ) . '%';

        foreach ( $this->get_target_language_codes() as $lang ) {
            $table = $query->get_table_name( $lang );
            if ( ! $this->dictionary_table_exists( $table ) ) {
                continue;
            }
            $found = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM `{$table}` WHERE LOWER(original) LIKE %s LIMIT 1", $like ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name built from known prefix + validated language codes.
            if ( $found ) {
                return true;
            }
        }

        return false;
    }

    /**
     * The TRP_Query component, or null if unavailable.
     *
     * @return TRP_Query|null
     */
    private function get_query() {
        $trp   = TRP_Translate_Press::get_trp_instance();
        $query = $trp->get_component( 'query' );
        return $query ? $query : null;
    }

    /**
     * Whether a dictionary table name is valid and exists in the database.
     *
     * @param string $table
     * @return bool
     */
    private function dictionary_table_exists( $table ) {
        global $wpdb;
        if ( ! is_string( $table ) || $table === '' || strpos( $table, 'invalid' ) !== false ) {
            return false;
        }
        return (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
    }

    /**
     * Ajax handler: edit an existing glossary term.
     *
     * The term is located by `original_term`; the (possibly renamed) new term
     * key and translations come from the `term` field. Row position is
     * preserved even when the key changes.
     *
     * Hooked to wp_ajax_trp_glossary_edit_term.
     */
    public function edit_term() {
        $this->verify_request( self::EDIT_TERM_ACTION );

        $original_term = isset( $_POST['original_term'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['original_term'] ) ) ) : '';

        list( $default_term, $translations ) = $this->parse_submitted_term();

        $glossary = $this->get_glossary();

        if ( $original_term === '' || ! array_key_exists( $original_term, $glossary ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'The term you are trying to edit no longer exists. Please reload the page.', 'translatepress-multilingual' ) ) );
        }

        // Renaming to a key that already belongs to a different term is a duplicate.
        if ( $default_term !== $original_term && array_key_exists( $default_term, $glossary ) ) {
            wp_send_json_error( array( 'message' => sprintf(
                /* translators: %s is the glossary term. */
                esc_html__( 'The term “%s” already exists in the glossary.', 'translatepress-multilingual' ),
                $default_term
            ) ) );
        }

        // Rebuild preserving the original row position, swapping the key in place.
        $updated = array();
        foreach ( $glossary as $key => $value ) {
            if ( $key === $original_term ) {
                $updated[ $default_term ] = $translations;
            } else {
                $updated[ $key ] = $value;
            }
        }

        update_option( self::OPTION_NAME, $updated, false );

        wp_send_json_success( array(
            'original_term' => $original_term,
            'term'          => $default_term,
            'translations'  => $translations,
        ) );
    }

    /**
     * Ajax handler: delete a glossary term.
     *
     * Hooked to wp_ajax_trp_glossary_delete_term.
     */
    public function delete_term() {
        $this->verify_request( self::DELETE_TERM_ACTION );

        $term = isset( $_POST['term'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['term'] ) ) ) : '';

        $glossary = $this->get_glossary();

        if ( $term === '' || ! array_key_exists( $term, $glossary ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'The term you are trying to delete no longer exists. Please reload the page.', 'translatepress-multilingual' ) ) );
        }

        unset( $glossary[ $term ] );
        update_option( self::OPTION_NAME, $glossary, false );

        wp_send_json_success( array( 'term' => $term ) );
    }

    /**
     * Register the hidden search & replace page (not linked in any menu).
     * Hooked to admin_menu.
     */
    public function add_replace_submenu_page() {
        add_submenu_page(
            'TRPHidden',
            'TranslatePress Glossary — Replace Existing Translations',
            'TRPHidden',
            apply_filters( 'trp_translating_capability', 'manage_options' ),
            self::REPLACE_PAGE_SLUG,
            array( $this, 'replace_page_content' )
        );
    }

    /**
     * Render the search & replace page.
     */
    public function replace_page_content() {
        require_once TRP_PLUGIN_DIR . 'partials/glossary-replace-page.php';
    }

    /**
     * Ajax handler: replace existing translations in the dictionary tables.
     *
     * For each target language where both a new value and a non-empty existing
     * translation are provided, replaces case-insensitive substring occurrences
     * of the existing translation with the new value in the `translated` column
     * (adapting case per occurrence) and marks changed rows as human-reviewed.
     *
     * Hooked to wp_ajax_trp_glossary_replace_in_dictionary.
     */
    public function replace_in_dictionary() {
        $this->verify_request( self::REPLACE_ACTION );

        $new_values      = ( isset( $_POST['new'] ) && is_array( $_POST['new'] ) )
            ? array_map( 'sanitize_text_field', wp_unslash( $_POST['new'] ) )
            : array();
        $existing_values = ( isset( $_POST['existing'] ) && is_array( $_POST['existing'] ) )
            ? array_map( 'sanitize_text_field', wp_unslash( $_POST['existing'] ) )
            : array();

        $query = $this->get_query();
        if ( ! $query ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Something went wrong. Please try again.', 'translatepress-multilingual' ) ) );
        }

        // Build the job: one entry per target language that has both a new value and
        // something to replace, plus a valid dictionary table. Uses an id cursor so each
        // row is processed exactly once (no runaway when the new value contains the old).
        $languages = array();
        foreach ( $this->get_target_language_codes() as $lang ) {
            $new_val      = isset( $new_values[ $lang ] ) ? trim( sanitize_text_field( $new_values[ $lang ] ) ) : '';
            $existing_val = isset( $existing_values[ $lang ] ) ? trim( sanitize_text_field( $existing_values[ $lang ] ) ) : '';

            if ( $new_val === '' || $existing_val === '' ) {
                continue;
            }

            $table = $query->get_table_name( $lang );
            if ( ! self::is_valid_dictionary_table( $table ) ) {
                continue;
            }

            $languages[ $lang ] = array(
                'table'    => $table,
                'existing' => $existing_val,
                'new'      => $new_val,
                'cursor'   => 0,
                'done'     => false,
                'count'    => 0,
            );
        }

        if ( empty( $languages ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Please provide at least one existing translation to replace.', 'translatepress-multilingual' ) ) );
        }

        // One bounded pass in this request — finishes small/medium jobs on the spot.
        // Both knobs are filterable so hosts can tune for their PHP/DB limits.
        $time_limit = (float) apply_filters( 'trp_glossary_replace_sync_time_limit', self::SYNC_TIME_LIMIT );
        $batch_size = max( 1, (int) apply_filters( 'trp_glossary_replace_sync_batch_size', self::SYNC_BATCH_SIZE ) );

        $start    = microtime( true );
        $finished = true;
        foreach ( $languages as $lang => &$info ) {
            while ( ! $info['done'] ) {
                $batch = self::replace_batch_in_table( $info['table'], $info['existing'], $info['new'], $info['cursor'], $batch_size, $lang );
                if ( $batch['error'] !== '' ) {
                    $info['done'] = true; // skip a broken table rather than loop forever
                    break;
                }
                $info['cursor']  = $batch['cursor'];
                $info['count']  += $batch['changed'];
                if ( $batch['done'] ) {
                    $info['done'] = true;
                    break;
                }
                if ( ( microtime( true ) - $start ) >= $time_limit ) {
                    $finished = false;
                    break 2;
                }
            }
        }
        unset( $info );

        $language_names = $this->get_english_language_names();

        if ( $finished ) {
            $counts = array();
            $total  = 0;
            foreach ( $languages as $lang => $info ) {
                $counts[ $lang ] = $info['count'];
                $total          += $info['count'];
            }

            $parts = array();
            foreach ( $counts as $lang => $count ) {
                $name    = isset( $language_names[ $lang ] ) ? $language_names[ $lang ] : $lang;
                $parts[] = sprintf(
                    /* translators: 1: language name, 2: number of entries. */
                    _n( '%1$s: %2$d entry updated', '%1$s: %2$d entries updated', $count, 'translatepress-multilingual' ),
                    $name,
                    $count
                );
            }

            wp_send_json_success( array(
                'message'    => esc_html__( 'Replacement complete.', 'translatepress-multilingual' ) . ' ' . implode( ', ', $parts ),
                'counts'     => $counts,
                'total'      => $total,
                'background' => false,
            ) );
        }

        // Too big for one request — hand the remainder to the batch processor (cron),
        // resuming from the cursors already advanced above.
        update_option( self::BATCH_JOB, array( 'languages' => $languages ), false );
        delete_option( 'trp_batch_todo_' . self::BATCH_FLAG );      // force a fresh todo list
        delete_option( 'trp_batch_found_rows_' . self::BATCH_FLAG );
        update_option( self::BATCH_FLAG, 'no' );                    // mark pending for the processor

        $batch_processor = TRP_Translate_Press::get_trp_instance()->get_component( 'batch_processor' );
        if ( $batch_processor ) {
            $batch_processor->schedule();
        }

        $so_far = 0;
        foreach ( $languages as $info ) {
            $so_far += $info['count'];
        }

        wp_send_json_success( array(
            'message'    => sprintf(
                /* translators: %d is the number of entries already updated. */
                esc_html__( 'This is a large replacement. %d entries were updated now and the rest will finish automatically in the background over the next few minutes.', 'translatepress-multilingual' ),
                $so_far
            ),
            'background' => true,
            'total'      => $so_far,
        ) );
    }

    /**
     * Process one batch of a dictionary replacement using an id cursor: fetch up to
     * $limit matching rows with id greater than $after_id, apply the whole-word,
     * case-adaptive replacement, and write the changed rows in a single UPDATE.
     *
     * Because it advances by id (never re-reading a processed row), it is safe even
     * when $new contains $existing, and it bounds memory to $limit rows per call.
     *
     * @param string $table
     * @param string $existing
     * @param string $new
     * @param int    $after_id
     * @param int    $limit
     * @return array { int cursor, int changed, bool done, string error }
     */
    public static function replace_batch_in_table( $table, $existing, $new, $after_id, $limit, $language_code = '' ) {
        global $wpdb;

        $out = array( 'cursor' => (int) $after_id, 'changed' => 0, 'done' => true, 'error' => '' );

        if ( ! self::is_valid_dictionary_table( $table ) ) {
            return $out;
        }

        $glossary = TRP_Translate_Press::get_trp_instance()->get_component( 'glossary' );
        if ( ! $glossary ) {
            $out['error'] = 'glossary component unavailable';
            return $out;
        }

        $like = '%' . $wpdb->esc_like( mb_strtolower( $existing, 'UTF-8' ) ) . '%';
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, translated FROM `{$table}` WHERE id > %d AND LOWER(translated) LIKE %s ORDER BY id ASC LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name built from known prefix + validated language codes.
            (int) $after_id,
            $like,
            (int) $limit
        ) );

        if ( $wpdb->last_error ) {
            $out['error'] = $wpdb->last_error;
            return $out;
        }

        $count = is_array( $rows ) ? count( $rows ) : 0;
        // Signal "done" only on an empty page (matches the batch-processor contract:
        // productive batches return not-completed so trp_batch_found_rows_* is recorded).
        if ( $count === 0 ) {
            $out['done'] = true;
            return $out;
        }
        $out['done'] = false;

        $changes = array();
        $max_id  = (int) $after_id;
        foreach ( $rows as $row ) {
            $max_id  = max( $max_id, (int) $row->id );
            $updated = self::case_aware_replace( $row->translated, $existing, $new, $glossary, $language_code );
            if ( $updated !== $row->translated ) {
                $changes[ (int) $row->id ] = $updated;
            }
        }
        $out['cursor'] = $max_id; // strictly greater than $after_id, so the cursor always advances

        if ( ! empty( $changes ) ) {
            $out['changed'] = self::batch_update_translated( $table, $changes );
        }

        return $out;
    }

    /**
     * Update several rows' `translated` (and status=2) in a single CASE statement.
     *
     * @param string $table
     * @param array  $changes id => new translated value
     * @return int number of rows updated
     */
    private static function batch_update_translated( $table, $changes ) {
        global $wpdb;

        if ( empty( $changes ) ) {
            return 0;
        }

        $case_sql  = '';
        $case_args = array();
        $ids       = array();
        foreach ( $changes as $id => $value ) {
            $case_sql   .= ' WHEN %d THEN %s';
            $case_args[] = (int) $id;
            $case_args[] = $value;
            $ids[]       = (int) $id;
        }

        $in  = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
        $sql = "UPDATE `{$table}` SET translated = CASE id{$case_sql} END, status = 2 WHERE id IN ({$in})";

        $result = $wpdb->query( $wpdb->prepare( $sql, array_merge( $case_args, $ids ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name validated; values are prepared placeholders.

        return ( $result === false ) ? 0 : count( $changes );
    }

    /**
     * Validate that a table name is a real TranslatePress dictionary table.
     * Stricter than a plain existence check because the name is interpolated into SQL.
     *
     * @param string $table
     * @return bool
     */
    private static function is_valid_dictionary_table( $table ) {
        global $wpdb;

        if ( ! is_string( $table ) || $table === '' ) {
            return false;
        }
        if ( strpos( $table, $wpdb->prefix . 'trp_dictionary_' ) !== 0 ) {
            return false;
        }
        if ( ! preg_match( '/^[A-Za-z0-9_]+$/', $table ) ) {
            return false;
        }

        return (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
    }

    /**
     * Number of search-preview entries shown per page, per language table.
     */
    const SEARCH_PER_PAGE = 5;

    /**
     * Ajax handler: preview how a term is currently translated in each language.
     *
     * Server-side paginated (SEARCH_PER_PAGE per page): for each target language
     * it returns the requested page of dictionary entries whose `original` column
     * contains the search term (case-insensitive) plus the true total match count,
     * so pagination is accurate for any number of matches. Read-only.
     *
     * Params: term; optional `lang` (fetch a single language's page) and `page`.
     *
     * Hooked to wp_ajax_trp_glossary_search_dictionary.
     */
    public function search_dictionary() {
        $this->verify_request( self::SEARCH_ACTION );

        global $wpdb;

        $term      = isset( $_POST['term'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['term'] ) ) ) : '';
        $only_lang = isset( $_POST['lang'] ) ? sanitize_text_field( wp_unslash( $_POST['lang'] ) ) : '';
        $page      = isset( $_POST['page'] ) ? max( 1, absint( $_POST['page'] ) ) : 1;
        $per_page  = self::SEARCH_PER_PAGE;

        $query          = $this->get_query();
        $language_names = $this->get_english_language_names();

        // Fetching one language's page (pagination click) vs. all languages (initial search).
        $languages = $this->get_target_language_codes();
        if ( $only_lang !== '' ) {
            $languages = in_array( $only_lang, $languages, true ) ? array( $only_lang ) : array();
        }

        $results = array();

        if ( $term !== '' && $query ) {
            $like = '%' . $wpdb->esc_like( mb_strtolower( $term, 'UTF-8' ) ) . '%';

            foreach ( $languages as $lang ) {
                $entries = array();
                $total   = 0;
                $table   = $query->get_table_name( $lang );

                if ( $this->dictionary_table_exists( $table ) ) {
                    $total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `{$table}` WHERE LOWER(original) LIKE %s AND translated IS NOT NULL AND translated <> ''", $like ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name built from known prefix + validated language codes.

                    $offset = ( $page - 1 ) * $per_page;
                    $rows   = $wpdb->get_results( $wpdb->prepare( "SELECT original, translated FROM `{$table}` WHERE LOWER(original) LIKE %s AND translated IS NOT NULL AND translated <> '' ORDER BY id ASC LIMIT %d OFFSET %d", $like, $per_page, $offset ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name built from known prefix + validated language codes.
                    foreach ( (array) $rows as $row ) {
                        $entries[] = array(
                            'original'   => $row->original,
                            'translated' => $row->translated,
                        );
                    }
                }

                $results[] = array(
                    'code'     => $lang,
                    'name'     => isset( $language_names[ $lang ] ) ? $language_names[ $lang ] : $lang,
                    'entries'  => $entries,
                    'total'    => $total,
                    'page'     => $page,
                    'per_page' => $per_page,
                );
            }
        }

        wp_send_json_success( array( 'results' => $results, 'per_page' => $per_page ) );
    }

    /**
     * Replace every case-insensitive occurrence of $search in $haystack with
     * $replace, adapting the replacement's capitalization to each occurrence via
     * TRP_Glossary::match_case().
     *
     * @param string       $haystack
     * @param string       $search
     * @param string       $replace
     * @param TRP_Glossary $glossary
     * @return string
     */
    public static function case_aware_replace( $haystack, $search, $replace, $glossary, $language_code = '' ) {
        if ( $search === '' || ! is_string( $haystack ) ) {
            return $haystack;
        }

        // Whole-word for spaced languages ("care" in "with care." but not "caregiver");
        // substring for languages without spaces (Chinese, Japanese, Thai, ...).
        $pattern = $glossary->term_match_pattern( $search, $language_code );

        $result = preg_replace_callback( $pattern, function ( $matches ) use ( $replace, $glossary ) {
            return $glossary->match_case( $matches[0], $replace );
        }, $haystack );

        // On PCRE failure (e.g. invalid UTF-8), keep the original untouched.
        return $result === null ? $haystack : $result;
    }
}
