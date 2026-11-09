<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Gettext_Manager
 *
 * Handles 'gettext' hook, replaces default with translation
 */
class TRP_Process_Gettext {
    protected $settings;
    /** @var TRP_Query */
    protected $trp_query;
    protected $machine_translator;
    protected $trp_languages;
    protected $gettext_manager;
    protected $plural_forms;
    protected $machine_translation_codes;
    protected $resolved_gettext_cache = array();
    protected $gettext_hook_has_listeners = array();
    protected $skip_gettext_processing_cache = array();
    protected $gettext_blacklist_functions_cache = array();
    protected $process_gettext_tags_cache = array();
    protected $skip_gettext_querying;
    protected $pending_gettext_storage = array();
    protected $pending_gettext_mt_candidates = array();
    protected $catalog_entry_index = array();

    /**
     * TRP_Gettext_Manager constructor.
     *
     * @param array $settings Settings option.
     */
    public function __construct( $settings, $plural_forms ) {
        $this->settings = $settings;
        $this->plural_forms = $plural_forms;
    }

    /**
     * Whether the loaded translation catalog contains a non-empty translation for a string.
     *
     * WordPress returns the original both when a catalog entry is missing and when its
     * translation intentionally matches the original. Cache a per-domain catalog index
     * so the latter is not sent to machine translation.
     *
     * @param string      $domain          Text domain.
     * @param string      $text            Original singular string.
     * @param string      $context         Gettext context.
     * @param string|null $original_plural Original plural string.
     *
     * @return bool
     */
    protected function gettext_string_has_catalog_translation( $domain, $text, $context, $original_plural = null ) {
        if ( ! isset( $this->catalog_entry_index[ $domain ] ) ) {
            $this->catalog_entry_index[ $domain ] = array();

            $translations = get_translations_for_domain( $domain );
            $entries = $translations ? $translations->entries : null;
            if ( is_array( $entries ) ) {
                foreach ( $entries as $entry ) {
                    if ( ! is_object( $entry ) || ! isset( $entry->singular ) || ! isset( $entry->translations ) ) {
                        continue;
                    }

                    $has_translation = false;
                    foreach ( (array) $entry->translations as $entry_translation ) {
                        if ( $entry_translation !== '' && $entry_translation !== null ) {
                            $has_translation = true;
                            break;
                        }
                    }

                    if ( ! $has_translation ) {
                        continue;
                    }

                    $entry_context = isset( $entry->context ) ? (string) $entry->context : '';
                    $this->catalog_entry_index[ $domain ][ $entry_context . "\4" . $entry->singular ] = true;
                }
            }
        }

        $lookup_context = ( $context && $context !== 'trp_context' ) ? (string) $context : '';

        if ( isset( $this->catalog_entry_index[ $domain ][ $lookup_context . "\4" . $text ] ) ) {
            return true;
        }

        return $original_plural && isset( $this->catalog_entry_index[ $domain ][ $lookup_context . "\4" . $original_plural ] );
    }


    /**
     * Function that replaces the translations with the ones in the database if they are different, wraps the texts in the html and
     * builds a global for machine translation with the strings that are not translated
     * @param $translation
     * @param $text
     * @param $domain
     * @return string
     */
    public function process_gettext_strings( $translation, $text, $domain, $context = 'trp_context', $number_of_items = null, $original_plural = null ) {
        // if we have nested gettexts strip previous ones, and consider only the outermost
        $text        = TRP_Gettext_Manager::strip_gettext_tags( $text );
        $translation = TRP_Gettext_Manager::strip_gettext_tags( $translation );

        //try here to exclude some strings that do not require translation
        $excluded_gettext_strings = array( '', ' ', '&hellip;', '&nbsp;', '&raquo;' );
        $trim_filter              = " \t\n\r\0\x0B\xA0�.,/`~!@#\$€£%^&*():;-_=+[]{}\\|?/<>1234567890'\"";

        if ( in_array( trim( $text, $trim_filter ), $excluded_gettext_strings ) || empty( $text ) )
            return $translation;

        global $TRP_LANGUAGE;

        if ( ( isset( $_REQUEST['trp-edit-translation'] ) && $_REQUEST['trp-edit-translation'] == 'true' ) || $domain == 'translatepress-multilingual' )
            return $translation;

        /* for our own actions don't do nothing */
        if (isset($_REQUEST['action']) && strpos( sanitize_text_field( $_REQUEST['action'] ), 'trp_') === 0)
            return $translation;

        if( $this->skip_gettext_querying === null ) {
            // apply filters takes time. Only do this once. Parameters $translation, $text, $domain are irrelevant but can't be removed due to backwards compatibility
            // Use trp_skip_gettext_processing hook for not adding wrappings.
            $this->skip_gettext_querying = apply_filters( 'trp_skip_gettext_querying', false, $translation, $text, $domain );
        }

        global $trp_wpdb_prefix, $wpdb;
        // When gettext querying is disabled, create_gettext_translated_global() never runs,
        // so $trp_wpdb_prefix stays unset. In that case we still need to reach the
        // gettext wrapper path so regular string detection can skip this output.
        if ( !$this->skip_gettext_querying && $trp_wpdb_prefix != $wpdb->get_blog_prefix() ){
            return $translation;
        }

        /* get_locale() returns WP Settings Language (WPLANG). It might not be a language in TP so it may not have a TP table. */
        $current_locale = get_locale();
        global $trp_translated_gettext_texts_language;
        if ( !$this->skip_gettext_querying && ( !in_array( $current_locale, $this->settings['translation-languages'] ) || empty( $trp_translated_gettext_texts_language ) || $trp_translated_gettext_texts_language !== $current_locale ) ) {
            return $translation;
        }

        $plural_form = $this->plural_forms->get_plural_form( $number_of_items, $current_locale );

        if ( $this->should_skip_gettext_processing( $current_locale, $translation, $text, $domain ) )
            return $translation;

        $in_foreign_locale_switch = $this->is_inside_foreign_locale_switch( $current_locale );

        // If WordPress looked this string up in a different locale than the TP language we would
        // store it under, treat it as untranslated so no foreign text is stored/served.
        if ( !$in_foreign_locale_switch && !$this->skip_gettext_querying && $translation !== $text
             && $this->wordpress_gettext_lookup_locale_differs( $current_locale ) ) {
            $translation = $text;
        }

        $cache_key = $this->get_resolved_gettext_cache_key( $current_locale, $context, $plural_form, $text, $domain );

        //use a global for is_ajax_on_frontend() so we don't execute it multiple times
        global $tp_gettext_is_ajax_on_frontend;
        if ( !isset( $tp_gettext_is_ajax_on_frontend ) )
            $tp_gettext_is_ajax_on_frontend = TRP_Gettext_Manager::is_ajax_on_frontend();

        if ( !defined( 'DOING_AJAX' ) || $tp_gettext_is_ajax_on_frontend || TRP_Woocommerce_Emails::is_translating_email() ) {
            $db_id = '';
            $cached_gettext_resolution = $in_foreign_locale_switch ? null : $this->get_resolved_gettext_cache_entry( $cache_key );
            if ( $cached_gettext_resolution !== null ) {
                $translation = $cached_gettext_resolution['translation'];
                $db_id       = $cached_gettext_resolution['db_id'];
            } else if ( !$in_foreign_locale_switch ) {
            $trp             = TRP_Translate_Press::get_trp_instance();

            if ( !$this->gettext_manager ) {
                $this->gettext_manager = $trp->get_component( 'gettext_manager' );
            }

            if ( !$this->gettext_manager->is_domain_loaded_in_locale( $domain, $current_locale ) ) {
                $translation = $text;
            }

            $db_id                 = '';
                $stored_translation    = null;
            if ( !$this->skip_gettext_querying ) {
                    global $trp_translated_gettext_texts;

                    $found_in_runtime_map = false;

                /* initiate trp query object */
                if (!$this->trp_query) {
                    $trp = TRP_Translate_Press::get_trp_instance();
                    $this->trp_query = $trp->get_component('query');
                }

                    $runtime_map_key = $this->get_gettext_runtime_map_key( $context, $plural_form, $domain, $text );

                if ( !empty( $trp_translated_gettext_texts ) ) {
                        if ( isset( $trp_translated_gettext_texts[ $runtime_map_key ] ) ) {
                            $trp_translated_gettext_text = $trp_translated_gettext_texts[ $runtime_map_key ];

                        $sprintf_reference = ( $original_plural !== null && $plural_form != 0 ) ? $original_plural : $text;
                        if (!empty($trp_translated_gettext_text['translated']) && $translation != $trp_translated_gettext_text['translated'] && $this->is_sprintf_compatible( $trp_translated_gettext_text['translated'], $sprintf_reference ) ) {
                            $translation = str_replace(trim($text), trp_sanitize_string($trp_translated_gettext_text['translated']), $text);
                        }
                            $stored_translation = $trp_translated_gettext_text['translated'];
                        $db_id       = $trp_translated_gettext_text['id'];
                            $found_in_runtime_map = true;
                        // update the db if a translation appeared in the po file later
                        if ( empty( $trp_translated_gettext_text['translated'] ) && $translation != $text && $translation != $original_plural ) {
                            $gettext_insert_update = $this->trp_query->get_query_component('gettext_insert_update');
                            $gettext_insert_update->update_gettext_strings( array(
                                array(
                                    'id'          => $db_id,
                                    'translated'  => $translation,
                                        'status'      => $this->trp_query->get_constant_gettext_translated_in_language_file(),
                                )
                            ), $current_locale, array('id', 'translated', 'status') );
                        }
                    }
                }

                    if ( !$found_in_runtime_map ) {
                        $translation = $this->maybe_get_older_version_translation($translation, $text, $domain, $context , $original_plural, $plural_form );

                        // Preview needs a real gettext row id in the current response so
                        // the editor can target first-seen strings immediately.
                        if ( $this->is_translation_editor_preview() ) {
                            $trp_translated_gettext_text = $this->resolve_or_insert_gettext_row_for_preview( $current_locale, $text, $translation, $domain, $context, $plural_form, $original_plural );

                            if ( ! empty( $trp_translated_gettext_text['id'] ) ) {
                                $sprintf_reference = ( $original_plural !== null && $plural_form != 0 ) ? $original_plural : $text;
                                if ( ! empty( $trp_translated_gettext_text['translated'] ) && $translation != $trp_translated_gettext_text['translated'] && $this->is_sprintf_compatible( $trp_translated_gettext_text['translated'], $sprintf_reference ) ) {
                                    $translation = str_replace(trim($text), trp_sanitize_string($trp_translated_gettext_text['translated']), $text);
                                }

                                $stored_translation                     = $trp_translated_gettext_text['translated'];
                                $db_id                                  = $trp_translated_gettext_text['id'];
                                $trp_translated_gettext_texts[ $runtime_map_key ] = $trp_translated_gettext_text;
                            }
                        } else {
                            $this->register_pending_gettext_storage( $current_locale, $text, $translation, $domain, $context, $plural_form, $original_plural );
                        }
                    }

                    $this->maybe_queue_gettext_for_machine_translation( $db_id, $text, $translation, $domain, $context, $plural_form, $original_plural, $current_locale, $stored_translation );
                }
                $this->store_resolved_gettext_cache_entry( $cache_key, $translation, $db_id );
            }

            $blacklist_functions = $this->get_gettext_blacklist_functions( $current_locale, $translation, $text, $domain );

            if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
                $callstack_functions = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 15 );//set a limit if it is supported to improve performance
            } else {
                $callstack_functions = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
            }
            if ( !empty( $callstack_functions ) ) {
                foreach ( $callstack_functions as $callstack_function ) {
                    if ( in_array( $callstack_function['function'], $blacklist_functions ) ) {
                        return $translation;
                    }

                    /* make sure we don't touch the woocommerce process_payment function in WC_Gateway_Stripe. It does a wp_remote_post() call to stripe with localized parameters */
                    if ( $callstack_function['function'] == 'process_payment' && $callstack_function['class'] == 'WC_Gateway_Stripe' ) {
                        return $translation;
                    }

                }
            }
            unset( $callstack_functions );//maybe free up some memory
            global $trp_output_buffer_started;
            if ( did_action( 'init' ) && ( ( isset( $trp_output_buffer_started ) && $trp_output_buffer_started ) || TRP_Woocommerce_Emails::is_order_email() ) ) {// Order emails also clean up gettext markers in wp_mail, including admin/AJAX sends.
                if ( ( !empty( $TRP_LANGUAGE ) && $this->settings["default-language"] != $TRP_LANGUAGE ) || ( isset( $_REQUEST['trp-edit-translation'] ) && $_REQUEST['trp-edit-translation'] == 'preview' ) ) {
                    //add special start and end tags so that it does not influence html in any way. we will replace them with < and > at the start of the translate function
	                /**
	                 * Compatibility with Woocomerce Payments
	                 *
	                 * In the file woocommerce-payments/includes/class-wc-payments-customer-service.php there is this line of code
	                 * $description = sprintf( __( 'Name: %1$s, Username: %2$s', 'woocommerce-payments' ), $name, $wc_customer->get_username() ); that should return admin or guest
	                 * but for some reason it returns our gettext string without the stripped gettext.
	                 */

		                $is_date_format_context = $text === 'Y' && (
			                stripos( $context, 'date format' ) !== false ||
			                stripos( $context, 'time format' ) !== false
                        );

		                if ( !$is_date_format_context && ( ($text != 'Name: %1$s, Username: %2$s' && $text != 'Name: %1$s, Guest' && $domain == 'woocommerce-payments') || $domain != 'woocommerce-payments') ) {
			                $translation = $this->process_gettext_tags( $translation, $this->skip_gettext_querying, $text, $domain, $db_id );
		                }
                }
            }
        }
        return $translation;
    }

    /**
     * Return the built-in list of caller functions for which gettext output should not be wrapped.
     *
     * @return array
     */
    protected function get_default_gettext_blacklist_functions() {
        return array(
                'wp_enqueue_script',
                'wp_enqueue_scripts',
                'wp_editor',
                'wp_enqueue_media',
                'wp_register_script',
                'wp_print_scripts',
                'wp_localize_script',
                'wp_print_media_templates',
                'get_bloginfo',
                'wp_get_document_title',
                'wp_title',
                'wp_trim_words',
                'sanitize_title',
                'sanitize_title_with_dashes',
                'esc_url',
                'wc_get_permalink_structure' // make sure we don't touch the woocommerce permalink rewrite slugs that are translated
            );
    }

    /**
     * Return the locale used for gettext processing in the current request.
     *
     * Prefer TP's resolved language when available to avoid repeated get_locale() calls
     * on the hot path.
     *
     * @return string
     */
    protected function get_current_gettext_locale() {
        global $TRP_LANGUAGE;

        if ( ! empty( $TRP_LANGUAGE ) ) {
            return $TRP_LANGUAGE;
        }

        return get_locale();
    }

    /**
     * Whether the locale WordPress used for this lookup (WP_Translation_Controller locale on
     * WP 6.5+, else determine_locale()) differs from the current TranslatePress language.
     *
     * @param string $current_locale The current TranslatePress language.
     * @return bool
     */
    protected function wordpress_gettext_lookup_locale_differs( $current_locale ) {
        if ( class_exists( 'WP_Translation_Controller' ) ) {
            $wp_lookup_locale = WP_Translation_Controller::get_instance()->get_locale();
        } else {
            $wp_lookup_locale = determine_locale();
        }

        return ( ! empty( $wp_lookup_locale ) && strtolower( $wp_lookup_locale ) !== strtolower( $current_locale ) );
    }

    /**
     * Inside switch_to_locale() to a locale other than the TP language, e.g. Contact Form 7 rendering a form in its own locale.
     * Such translations are neither discarded nor stored.
     *
     * @param string $current_locale
     * @return bool
     */
    protected function is_inside_foreign_locale_switch( $current_locale ) {
        global $wp_locale_switcher;

        if ( ! ( $wp_locale_switcher instanceof WP_Locale_Switcher ) || ! $wp_locale_switcher->is_switched() ) {
            return false;
        }

        return $this->wordpress_gettext_lookup_locale_differs( $current_locale );
    }

    /**
     * Build the request-local cache key for a resolved gettext lookup.
     *
     * @param string $locale
     * @param string $context
     * @param int    $plural_form
     * @param string $text
     * @param string $domain
     *
     * @return string
     */
    protected function get_resolved_gettext_cache_key( $locale, $context, $plural_form, $text, $domain ) {
        return $locale . '::' . $context . '::' . $plural_form . '::' . $text . '::' . $domain;
    }

    /**
     * Return a cached gettext resolution for the current request, if one exists.
     *
     * The cache stores the resolved translation and related DB id for repeated
     * gettext keys.
     *
     * @param string $cache_key
     *
     * @return array|null
     */
    protected function get_resolved_gettext_cache_entry( $cache_key ) {
        if ( isset( $this->resolved_gettext_cache[ $cache_key ] ) ) {
            return $this->resolved_gettext_cache[ $cache_key ];
        }

        return null;
    }

    /**
     * Store a resolved gettext result for reuse during the current request.
     *
     * @param string     $cache_key
     * @param string     $translation
     * @param int|string $db_id
     *
     * @return void
     */
    protected function store_resolved_gettext_cache_entry( $cache_key, $translation, $db_id ) {
        $this->resolved_gettext_cache[ $cache_key ] = array(
            'translation' => $translation,
            'db_id'       => $db_id,
        );
    }


    /**
     * Determine once per request whether a TP gettext hook has listeners attached.
     *
     * This lets the hot path skip unnecessary apply_filters() dispatch when a hook
     * has no listeners.
     *
     * @param string $hook_name
     *
     * @return bool
     */
    protected function gettext_filter_has_listeners( $hook_name ) {
        if ( !array_key_exists( $hook_name, $this->gettext_hook_has_listeners ) ) {
            $this->gettext_hook_has_listeners[ $hook_name ] = ( has_filter( $hook_name ) !== false );
        }

        return $this->gettext_hook_has_listeners[ $hook_name ];
    }

    /**
     * Build a cache key for memoized filter results in the current request.
     *
     * @param string $locale
     * @param string $translation
     * @param string $text
     * @param string $domain
     * @param string $extra
     *
     * @return string
     */
    protected function get_filter_cache_key( $locale, $translation, $text, $domain, $extra = '' ) {
        return $locale . '::' . $domain . '::' . $text . '::' . $translation . '::' . $extra;
    }

    /**
     * Return whether gettext processing should be skipped for this input.
     *
     * The result of trp_skip_gettext_processing is memoized per request so repeated
     * keys do not pay repeated filter-dispatch cost.
     *
     * @param string $locale
     * @param string $translation
     * @param string $text
     * @param string $domain
     *
     * @return bool
     */
    protected function should_skip_gettext_processing( $locale, $translation, $text, $domain ) {
        if ( !$this->gettext_filter_has_listeners( 'trp_skip_gettext_processing' ) ) {
            return false;
        }

        $cache_key = $this->get_filter_cache_key( $locale, $translation, $text, $domain );

        if ( array_key_exists( $cache_key, $this->skip_gettext_processing_cache ) ) {
            return $this->skip_gettext_processing_cache[ $cache_key ];
        }

        $this->skip_gettext_processing_cache[ $cache_key ] = apply_filters( 'trp_skip_gettext_processing', false, $translation, $text, $domain );

        return $this->skip_gettext_processing_cache[ $cache_key ];
    }

    /**
     * Return the blacklist of caller functions that should bypass gettext wrapping.
     *
     * When the filter has listeners, the result is memoized per request for repeated
     * gettext inputs.
     *
     * @param string $locale
     * @param string $translation
     * @param string $text
     * @param string $domain
     *
     * @return array
     */
    protected function get_gettext_blacklist_functions( $locale, $translation, $text, $domain ) {
        $default_blacklist_functions = $this->get_default_gettext_blacklist_functions();

        if ( !$this->gettext_filter_has_listeners( 'trp_gettext_blacklist_functions' ) ) {
            return $default_blacklist_functions;
        }

        $cache_key = $this->get_filter_cache_key( $locale, $translation, $text, $domain );

        if ( isset( $this->gettext_blacklist_functions_cache[ $cache_key ] ) ) {
            return $this->gettext_blacklist_functions_cache[ $cache_key ];
        }

        $this->gettext_blacklist_functions_cache[ $cache_key ] = apply_filters( 'trp_gettext_blacklist_functions', $default_blacklist_functions, $text, $translation, $domain );

        return $this->gettext_blacklist_functions_cache[ $cache_key ];
    }

    /**
     * Wrap gettext output in TP markers, optionally passing the wrapped value through
     * the trp_process_gettext_tags filter.
     *
     * Filtered results are memoized per request for repeated gettext inputs.
     *
     * @param string     $translation
     * @param bool       $skip_gettext_querying
     * @param string     $text
     * @param string     $domain
     * @param int|string $db_id
     *
     * @return string
     */
    protected function process_gettext_tags( $translation, $skip_gettext_querying, $text, $domain, $db_id ) {
        $db_id = !empty( $db_id ) ? $db_id : 0;
        $wrapped_translation = '#!trpst#trp-gettext data-trpgettextoriginal=' . $db_id . '#!trpen#' . $translation . '#!trpst#/trp-gettext#!trpen#';

        if ( !$this->gettext_filter_has_listeners( 'trp_process_gettext_tags' ) ) {
            return $wrapped_translation;
        }

        $cache_key = $this->get_filter_cache_key( '', $translation, $text, $domain, (string) $skip_gettext_querying . '::' . $db_id );

        if ( isset( $this->process_gettext_tags_cache[ $cache_key ] ) ) {
            return $this->process_gettext_tags_cache[ $cache_key ];
        }

        $this->process_gettext_tags_cache[ $cache_key ] = apply_filters( 'trp_process_gettext_tags', $wrapped_translation, $translation, $skip_gettext_querying, $text, $domain );

        return $this->process_gettext_tags_cache[ $cache_key ];
    }

    /**
     * Return the runtime map key used for resolved gettext rows in the current request.
     *
     * @param string $context
     * @param int    $plural_form
     * @param string $domain
     * @param string $text
     *
     * @return string
     */
    protected function get_gettext_runtime_map_key( $context, $plural_form, $domain, $text ) {
        return $context . '::' . $plural_form . '::' . $domain . '::' . $text;
    }

    /**
     * Return whether the current request is a translation editor preview.
     *
     * @return bool
     */
    protected function is_translation_editor_preview() {
        return isset( $_REQUEST['trp-edit-translation'] ) && $_REQUEST['trp-edit-translation'] === 'preview';
    }

    /**
     * Resolve or insert a gettext row immediately for preview requests.
     *
     * Preview mode needs the concrete DB id in the same response so the editor can
     * attach gettext metadata to first-seen strings before shutdown runs.
     *
     * @param string      $language
     * @param string      $text
     * @param string      $translation
     * @param string      $domain
     * @param string      $context
     * @param int         $plural_form
     * @param string|null $original_plural
     *
     * @return array
     */
    protected function resolve_or_insert_gettext_row_for_preview( $language, $text, $translation, $domain, $context, $plural_form, $original_plural ) {
        $gettext_row = $this->get_preview_gettext_row_from_db( $language, $text, $domain, $context, $plural_form );

        if ( ! empty( $gettext_row['id'] ) ) {
            return $gettext_row;
        }

                        $gettext_insert_update = $this->trp_query->get_query_component('gettext_insert_update');
                        // First-seen translation is sourced from a WordPress language (.mo) file:
                        // store it with the language-file status, not the human-reviewed default.
                        $db_id = $gettext_insert_update->insert_gettext_strings( array(
                            array(
	                            'original'        => $text,
	                            'translated'      => ( $translation != $text && $translation != $original_plural ) ? $translation : '',
	                            'status'          => $this->get_gettext_translated_in_language_file_status(),
	                            'domain'          => $domain,
	                            'context'         => $context,
	                            'plural_form'     => $plural_form,
	                            'original_plural' => $original_plural
                            )
        ), $language );

        if ( ! empty( $db_id ) ) {
            return array(
                'id'          => (int) $db_id,
                            'original'    => $text,
                            'translated'  => ( $translation != $text && $translation != $original_plural ) ? $translation : '',
                            'domain'      => $domain,
                            'context'     => $context,
                'plural_form' => $plural_form,
                        );
                    }

        return $this->get_preview_gettext_row_from_db( $language, $text, $domain, $context, $plural_form );
                }

    /**
     * Fetch an existing gettext row for preview-time synchronous resolution.
     *
     * @param string $language
     * @param string $text
     * @param string $domain
     * @param string $context
     * @param int    $plural_form
     *
     * @return array
     */
    protected function get_preview_gettext_row_from_db( $language, $text, $domain, $context, $plural_form ) {
        $rows = $this->trp_query->get_gettext_rows_by_composite_keys( $language, array(
            array(
                'original'    => $text,
                'domain'      => $domain,
                'context'     => $context,
                'plural_form' => $plural_form,
            )
        ) );

        if ( empty( $rows ) ) {
            return array();
                }

        $resolved_row = reset( $rows );
        foreach ( $rows as $row ) {
            if ( ! empty( $row['translated'] ) ) {
                $resolved_row = $row;
                break;
                }
                }

        return array(
            'id'          => (int) $resolved_row['id'],
            'original'    => ! empty( $resolved_row['original'] ) ? $resolved_row['original'] : $text,
            'translated'  => ! empty( $resolved_row['translated'] ) ? $resolved_row['translated'] : '',
            'domain'      => ! empty( $resolved_row['domain'] ) ? $resolved_row['domain'] : $domain,
            'context'     => ! empty( $resolved_row['context'] ) ? $resolved_row['context'] : $context,
            'plural_form' => isset( $resolved_row['plural_form'] ) ? (int) $resolved_row['plural_form'] : $plural_form,
        );
    }

    /**
     * Queue a gettext row for machine translation when it is eligible and still untranslated.
     *
     * Reuses the current row state so we do not need to rescan the gettext map by id.
     *
     * @param int|string $db_id
     * @param string     $text
     * @param string     $translation
     * @param string     $domain
     * @param string     $context
     * @param int        $plural_form
     * @param string     $original_plural
     * @param string     $language
     * @param string|null $stored_translation
     *
     * @return void
     */
    protected function maybe_queue_gettext_for_machine_translation( $db_id, $text, $translation, $domain, $context, $plural_form, $original_plural, $language, $stored_translation ) {
        if ( !$this->machine_translation_is_available_for_gettext( $language ) ) {
            return;
        }

        if ( $text != $translation && $original_plural != $translation ) {
            return;
        }

        if ( !empty( $stored_translation ) ) {
            return;
        }

        if ( $this->gettext_string_has_catalog_translation( $domain, $text, $context, $original_plural ) ) {
            if ( ! empty( $db_id ) ) {
                                        $gettext_insert_update = $this->trp_query->get_query_component( 'gettext_insert_update' );
                $gettext_insert_update->update_gettext_strings(
                    array(
                                            array(
                                                'id'         => $db_id,
                                                'translated' => $translation,
                            'status'     => $this->trp_query->get_constant_gettext_translated_in_language_file(),
                        ),
                    ),
                    $language,
                    array( 'id', 'translated', 'status' )
                );
            } else {
                $storage_key = $this->get_gettext_storage_key( $language, $context, $plural_form, $domain, $text );
                if ( isset( $this->pending_gettext_storage[ $language ][ $storage_key ] ) ) {
                    $this->pending_gettext_storage[ $language ][ $storage_key ]['translated'] = $translation;
                                    }
                                }

            return;
        }

        if ( empty( $db_id ) ) {
            $this->register_pending_gettext_mt_candidate( $language, $text, $translation, $domain, $context, $plural_form, $original_plural );
            return;
                            }

        global $trp_gettext_strings_for_machine_translation;
        if ( isset( $trp_gettext_strings_for_machine_translation[ $db_id ] ) ) {
            return;
        }

                                        $trp_gettext_strings_for_machine_translation[ $db_id ] = array(
                                            'id'         => $db_id,
                                            'original'   => $text,
                                            'translated' => '',
                                            'domain'     => $domain,
                                            'status'     => $this->trp_query->get_constant_machine_translated(),
                                            'context'     => $context,
                                            'plural_form' => $plural_form,
                                            'original_plural' => $original_plural
                                        );
                                    }

    /**
     * Build the request-local key used to dedupe observed gettext rows.
     *
     * @param string $language    Target language code.
     * @param string $context     Gettext context.
     * @param int    $plural_form Plural form index.
     * @param string $domain      Text domain.
     * @param string $original    Original gettext string.
     *
     * @return string
     */
    public function get_gettext_storage_key( $language, $context, $plural_form, $domain, $original ) {
        return md5( implode( "\x1F", array( $language, $context, (string) $plural_form, $domain, $original ) ) );
                        }

    /**
     * Return observed gettext strings that need DB existence checks at shutdown.
     *
     * @return array
     */
    public function get_pending_gettext_storage() {
        return $this->pending_gettext_storage;
                    }

    /**
     * Return observed gettext strings that may need machine translation after storage is resolved.
     *
     * @return array
     */
    public function get_pending_gettext_mt_candidates() {
        return $this->pending_gettext_mt_candidates;
                }

    /**
     * Clear request-local gettext storage and machine-translation buffers.
     *
     * @return void
     */
    public function clear_pending_gettext_buffers() {
        $this->pending_gettext_storage       = array();
        $this->pending_gettext_mt_candidates = array();
            }

    /**
     * Register a gettext row observed during runtime for deferred DB lookup/insert.
     *
     * @param string      $language        Target language code.
     * @param string      $text            Original gettext string.
     * @param string      $translation     Translation returned by WordPress gettext.
     * @param string      $domain          Text domain.
     * @param string      $context         Gettext context.
     * @param int         $plural_form     Plural form index.
     * @param string|null $original_plural Original plural string, when available.
     *
     * @return void
     */
    protected function register_pending_gettext_storage( $language, $text, $translation, $domain, $context, $plural_form, $original_plural ) {
        $key = $this->get_gettext_storage_key( $language, $context, $plural_form, $domain, $text );

        if ( isset( $this->pending_gettext_storage[ $language ][ $key ] ) ) {
            return;
                    }

        $translated_from_language_file = ( $translation != $text && $translation != $original_plural );

        // Observed translations come from a .mo file, not a human: store as language-file status
        // (empty ones fall through to NOT_TRANSLATED in insert_gettext_strings()).
        $this->pending_gettext_storage[ $language ][ $key ] = array(
            'key'                  => $key,
            'language'             => $language,
            'original'             => $text,
            'translated'           => $translated_from_language_file ? $translation : '',
            'status'               => $this->get_gettext_translated_in_language_file_status(),
            'domain'               => $domain,
            'context'              => $context,
            'plural_form'          => $plural_form,
            'original_plural'      => $original_plural,
            'observed_translation' => $translation,
        );
                    }

    /**
     * Return the gettext status constant for translations sourced from a WordPress language file.
     *
     * @return int
     */
    protected function get_gettext_translated_in_language_file_status() {
        if ( ! $this->trp_query ) {
            $trp             = TRP_Translate_Press::get_trp_instance();
            $this->trp_query = $trp->get_component( 'query' );
        }

        return $this->trp_query->get_constant_gettext_translated_in_language_file();
    }

	                /**
     * Register an observed gettext row as a machine-translation candidate.
	                 *
     * Candidate rows are translated only after shutdown storage resolution maps
     * them to concrete DB ids.
     *
     * @param string      $language        Target language code.
     * @param string      $text            Original gettext string.
     * @param string      $translation     Translation returned by WordPress gettext.
     * @param string      $domain          Text domain.
     * @param string      $context         Gettext context.
     * @param int         $plural_form     Plural form index.
     * @param string|null $original_plural Original plural string, when available.
     *
     * @return void
	                 */
    protected function register_pending_gettext_mt_candidate( $language, $text, $translation, $domain, $context, $plural_form, $original_plural ) {
        $key = $this->get_gettext_storage_key( $language, $context, $plural_form, $domain, $text );

        if ( isset( $this->pending_gettext_mt_candidates[ $language ][ $key ] ) ) {
            return;
        }

        $this->pending_gettext_mt_candidates[ $language ][ $key ] = array(
            'key'             => $key,
            'language'        => $language,
            'original'        => $text,
            'translated'      => '',
            'domain'          => $domain,
            'context'         => $context,
            'plural_form'     => $plural_form,
            'original_plural' => $original_plural,
        );
    }

	                /**
     * Return whether machine translation is available for the target gettext language.
     *
     * The availability result is memoized per request because this check can be hit
     * many times on high-volume gettext pages.
     *
     * @param string $language
	                 *
     * @return bool
	                 */
    protected function machine_translation_is_available_for_gettext( $language ) {
        $trp = TRP_Translate_Press::get_trp_instance();
        if ( !$this->machine_translator ) {
            $this->machine_translator = $trp->get_component( 'machine_translator' );
	                }
        if ( !$this->trp_languages ) {
            $this->trp_languages = $trp->get_component( 'languages' );
                }
        if ( !$this->machine_translation_codes ) {
            $this->machine_translation_codes = $this->trp_languages->get_iso_codes( $this->settings['translation-languages'] );
            }

        $is_available = false;

        /* We assume Gettext strings are in English so don't automatically translate into English */
        if ( !empty( $language ) && isset( $this->machine_translation_codes[ $language ] ) && $this->machine_translation_codes[ $language ] != 'en' ) {
            $is_available = $this->machine_translator->is_available( array( $language ) );
        }

        return $is_available;
    }

    /**
     * caller for woocommerce domain texts
     * @param $translation
     * @param $text
     * @param $domain
     * @return string
     */
    public function woocommerce_process_gettext_strings( $translation, $text, $domain ) {
        if ( $domain === 'woocommerce' ) {
            $translation = $this->process_gettext_strings( $translation, $text, $domain );
        }
        return $translation;
    }

    /**
     * Function that filters gettext strings with context _x
     * @param $translation
     * @param $text
     * @param $context
     * @param $domain
     * @return string
     */
    public function process_gettext_strings_with_context( $translation, $text, $context, $domain ) {
        $translation = $this->process_gettext_strings( $translation, $text, $domain, $context );
        return $translation;
    }

    /**
     * caller for woocommerce domain texts with context
     */
    public function woocommerce_process_gettext_strings_with_context( $translation, $text, $context, $domain ) {
        if ( $domain === 'woocommerce' ) {
            $translation = $this->process_gettext_strings_with_context( $translation, $text, $context, $domain );
        }
        return $translation;
    }

    /**
     * function that filters the _n translations
     * @param $translation
     * @param $single
     * @param $plural
     * @param $number
     * @param $domain
     * @return string
     */
    public function process_ngettext_strings( $translation, $single, $plural, $number, $domain ) {
        $translation = $this->process_gettext_strings( $translation, $single, $domain, 'trp_context', $number, $plural );
        return $translation;
    }

    /**
     * caller for woocommerce domain numeric texts
     */
    public function woocommerce_process_ngettext_strings( $translation, $single, $plural, $number, $domain ) {
        if ( $domain === 'woocommerce' ) {
            $translation = $this->process_ngettext_strings( $translation, $single, $plural, $number, $domain );
        }

        return $translation;
    }

    /**
     * function that filters the _nx translations
     * @param $translation
     * @param $single
     * @param $plural
     * @param $number
     * @param $context
     * @param $domain
     * @return string
     */
    public function process_ngettext_strings_with_context( $translation, $single, $plural, $number, $context, $domain ) {
        $translation = $this->process_gettext_strings( $translation, $single, $domain, $context, $number, $plural );
        return $translation;
    }

    /**
     * caller for woocommerce domain numeric texts with context
     */
    public function woocommerce_process_ngettext_strings_with_context( $translation, $single, $plural, $number, $context, $domain ) {
        if ( $domain === 'woocommerce' ) {
            $translation = $this->process_ngettext_strings_with_context( $translation, $single, $plural, $number, $context, $domain );
        }
        return $translation;
    }

    /** Caller for gettext with no context and no plural.
     * Can't call process_gettext_strings directly due to incorrect parameter number
     *
     * @param $translation
     * @param $text
     * @param $domain
     * @return string
     */
    public function process_gettext_strings_no_context( $translation, $text, $domain ){
        $translation = $this->process_gettext_strings( $translation, $text, $domain );
        return $translation;
    }

	 /**
	  * Caller for woocommerce domain with no context and no plural
	  * Can't call process_gettext_strings directly due to incorrect parameter number
	  */
    public function woocommerce_process_gettext_strings_no_context( $translation, $text, $domain )
    {
        if ($domain === 'woocommerce') {
            $translation = $this->process_gettext_strings($translation, $text, $domain);
        }
        return $translation;
    }

	/**
	 * If we have a translation without context and without plural form then return that translation
	 *
	 * @param $translation
	 * @param $text
	 * @param $domain
	 * @param $context
	 * @param $original_plural
	 * @param $plural_form
	 *
	 * @return string
	 */
    public function maybe_get_older_version_translation($translation, $text, $domain, $context , $original_plural, $plural_form){

        global $trp_translated_gettext_texts;
        if ( $context == 'trp_context' && $original_plural === null ){
            return $translation;
        }
        if ( $original_plural !== null && $plural_form != 0 ){
            $text = $original_plural;
        }

        if ( isset( $trp_translated_gettext_texts[ 'trp_context' . '::' . 0 . '::' . $domain . '::' . $text ] ) &&
            !empty($trp_translated_gettext_texts[ 'trp_context' . '::' . 0 . '::' . $domain . '::' . $text ]['translated']) &&
            $this->is_sprintf_compatible( $trp_translated_gettext_texts[ 'trp_context' . '::' . 0 . '::' . $domain . '::' . $text ]['translated'], $text )
        ){
            $translation = str_replace(trim($text), trp_sanitize_string($trp_translated_gettext_texts[ 'trp_context' . '::' . 0 . '::' . $domain . '::' . $text ]['translated']), $text);
        }

        return $translation;
    }

    public function is_sprintf_compatible($string, $original_text = null){

        if (! apply_filters('trp_check_sprintf_compatibility', true ) ){
            return true;
        }

        if ( $original_text !== null ) {
            // Fast path: no '%' in either string means no placeholders to compare.
            if ( strpos( $original_text, '%' ) === false && strpos( $string, '%' ) === false ) {
                return true;
            }

            // sprintf placeholder grammar: %[argnum$][flags][width][.precision]specifier
            $pattern = "/%(?:\d+\\\$)?[-+0 #]*(?:'.)?\d*(?:\.\d+)?[bcdeEfFgGhHosuxX%]/";

            preg_match_all( $pattern, $original_text, $original_matches );
            preg_match_all( $pattern, $string, $translated_matches );

            // %% is a literal percent, not an argument consumer.
            $original_placeholders   = array_values( array_filter( $original_matches[0],   function( $p ) { return $p !== '%%'; } ) );
            $translated_placeholders = array_values( array_filter( $translated_matches[0], function( $p ) { return $p !== '%%'; } ) );

            sort( $original_placeholders );
            sort( $translated_placeholders );

            if ( $original_placeholders !== $translated_placeholders ) {
                return false;
            }

            // A stray % the regex doesn't see (%. or %3$d% at the end) still makes sprintf() throw
            return strpos( $string, '%' ) === false || !$this->is_sprintf_compatible( $original_text ) || $this->is_sprintf_compatible( $string );
        }

        // 200 arguments should be enough. If a string has more than 200 placeholders then it might cause "Warning: sprintf(): Too few arguments" on certain php versions
        $arr = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
        $is_compatible = true;
        try{
            $test = sprintf($string, ...$arr);
        }catch(Throwable $e){
            $is_compatible = false;
        }
        return $is_compatible;
    }
}
