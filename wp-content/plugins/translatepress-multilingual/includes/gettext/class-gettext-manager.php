<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Gettext_Manager
 *
 * Handles Gettext strings
 */
class TRP_Gettext_Manager {
	protected $settings;
	/** @var TRP_Query */
	protected $trp_query;
	/** @var TRP_Process_Gettext */
	protected $process_gettext;
	/** @var TRP_Plural_Forms */
	protected $plural_forms;
	protected $machine_translator;
	protected $url_converter;
	protected $is_admin_request = null;


	/**
	 * TRP_Gettext_Manager constructor.
	 *
	 * @param array $settings Settings option.
	 */
	public function __construct( $settings ) {
		$this->settings        = $settings;
		$this->plural_forms    = new TRP_Plural_Forms( $this->settings );
		$this->process_gettext = new TRP_Process_Gettext( $this->settings, $this->plural_forms );
	}

	public function get_gettext_component( $component ) {
		return $this->$component;
	}


	/**
	 * Create a global with the gettext strings that exist in the database
	 */
	public function create_gettext_translated_global() {
		global $trp_translated_gettext_texts, $trp_translated_gettext_texts_language;
        // Create gettext translated global only if processing is needed
        if ( $this->processing_gettext_is_needed() ) {
			$language = get_locale();

			if ( in_array( $language, $this->settings['translation-languages'] ) ) {
				$trp_translated_gettext_texts_language = $language;
                global $wpdb, $trp_wpdb_prefix;
                $trp_wpdb_prefix = $wpdb->get_blog_prefix();
                $trp             = TRP_Translate_Press::get_trp_instance();
				if ( ! $this->trp_query ) {
					$this->trp_query = $trp->get_component( 'query' );
				}

				if ( $this->is_translation_editor_preview() || ! $this->gettext_runtime_status_migration_is_complete() ) {
					$strings = $this->trp_query->get_all_gettext_strings( $language );
				} else {
					$strings = $this->trp_query->get_runtime_gettext_strings( $language );
				}
				if ( ! empty( $strings ) ) {
					$trp_translated_gettext_texts = $strings;
					$trp_strings                  = array();
					foreach ( $trp_translated_gettext_texts as $key => $value ) {
						$context     = ( $value['context'] ) ? $value['context'] : 'trp_context';
						$plural_form = ( $value['plural_form'] ) ? $value['plural_form'] : 0;
						$domain      = ( $value['domain'] ) ? $value['domain'] : $value['tt_domain'];
						$original    = ( $value['original'] ) ? $value['original'] : $value['tt_original'];

						// trp_context::0::domain::original
						$trp_strings[ $context . '::' . $plural_form . '::' . $domain . '::' . $original ] = $value;
					}
					$trp_translated_gettext_texts = $trp_strings;
				} else {
                    // A language without saved overrides must not reuse the previous email's map.
                    $trp_translated_gettext_texts = [];
				}
			}
		}
	}

	/**
	 * function that applies the gettext filter on frontend on different hooks depending on what we need
	 */
	public function initialize_gettext_processing() {
		$is_ajax_on_frontend = $this::is_ajax_on_frontend();

		/* on ajax hooks from frontend that have the init hook ( we found WooCommerce has it ) apply it earlier */
		if ( $is_ajax_on_frontend || apply_filters( 'trp_apply_gettext_early', false ) ) {
			add_action( 'wp_loaded', array( $this, 'apply_gettext_filter' ) );
		} else if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ){ //if we have a block theme we need to start from template_redirect hook
            add_action( 'template_redirect', array( $this, 'apply_gettext_filter' ), 10 );
        }
        else {//otherwise start from the wp_head hook
			add_action( 'wp_head', array( $this, 'apply_gettext_filter' ), 100 );
		}

		//if we have woocommerce installed and it is not an ajax request add a gettext hook starting from wp_loaded and remove it on wp_head
		if ( class_exists( 'WooCommerce' ) && ! $is_ajax_on_frontend && ! apply_filters( 'trp_apply_gettext_early', false ) ) {
			// WooCommerce launches some ajax calls before wp_head, so we need to apply_gettext_filter earlier to catch them
			add_action( 'wp_loaded', array( $this, 'apply_woocommerce_gettext_filter' ), 19 );
		}
	}

	/* apply the gettext filter here */
	public function apply_gettext_filter() {

		//if we have wocommerce installed remove te hook that was added on wp_loaded
		if ( class_exists( 'WooCommerce' ) ) {
			// WooCommerce launches some ajax calls before wp_head, so we need to apply_gettext_filter earlier to catch them
			remove_action( 'wp_loaded', array( $this, 'apply_woocommerce_gettext_filter' ), 19 );
		}

		$this->call_gettext_filters();

	}

	public function apply_woocommerce_gettext_filter() {
		$this->call_gettext_filters( 'woocommerce_' );
	}

	protected function is_translation_editor_preview() {
		return isset( $_REQUEST['trp-edit-translation'] ) && $_REQUEST['trp-edit-translation'] === 'preview';
	}

	/**
	 * Return whether the gettext runtime status migration has completed.
	 *
	 * Upgraded sites temporarily fall back to loading all gettext rows until the
	 * full gettext optimization task has finished, including runtime-status
	 * classification.
	 *
	 * @return bool
	 */
	protected function gettext_runtime_status_migration_is_complete() {
		return get_option( 'trp_updated_database_gettext_tables_optimization', 'yes' ) === 'yes' &&
		       get_option( 'trp_updated_database_gettext_runtime_status_update', 'yes' ) !== 'no';
	}

    public function processing_gettext_is_needed() {
        global $pagenow;

        if ( ! $this->url_converter ) {
            $trp                 = TRP_Translate_Press::get_trp_instance();
            $this->url_converter = $trp->get_component( 'url_converter' );
        }
        if ( $this->is_admin_request === null ) {
            $this->is_admin_request = $this->url_converter->is_admin_request();
        }

        $should_process = (
            ( $pagenow != 'wp-login.php' )
            && ( ! is_admin() || $this::is_ajax_on_frontend() )
            && ! $this->is_admin_request
            && $pagenow != 'xmlrpc.php'
        );

        return apply_filters( 'trp_processing_gettext_is_needed', $should_process );
    }

	public function call_gettext_filters( $prefix = '' ) {
        // Add gettext filters only if processing is needed
        if ( !$this->processing_gettext_is_needed() )
            return;

        add_filter( 'gettext', array(
            $this->process_gettext,
            $prefix . 'process_gettext_strings_no_context'
        ), 100, 3 );
        add_filter( 'gettext_with_context', array(
            $this->process_gettext,
            $prefix . 'process_gettext_strings_with_context'
        ), 100, 4 );
        add_filter( 'ngettext', array( $this->process_gettext, $prefix . 'process_ngettext_strings' ), 100, 5 );
        add_filter( 'ngettext_with_context', array(
            $this->process_gettext,
            $prefix . 'process_ngettext_strings_with_context'
        ), 100, 6 );

        do_action( 'trp_call_gettext_filters' );
	}

	public function is_domain_loaded_in_locale( $domain, $locale ) {
		$localemo = $locale . '.mo';
		$length   = strlen( $localemo );

		global $l10n;

		// WP 6.5+ WP_Translations has no get_filename(); compare the controller's active locale
		// instead, otherwise this guard silently returns true and never detects a foreign catalog.
		if ( isset( $l10n[ $domain ] ) && $l10n[ $domain ] instanceof WP_Translations ) {
			if ( class_exists( 'WP_Translation_Controller' ) ) {
				return strtolower( WP_Translation_Controller::get_instance()->get_locale() ) === strtolower( $locale );
			}
			return true;
		}

		if ( isset( $l10n[ $domain ] ) && is_object( $l10n[ $domain ] ) && method_exists( $l10n[ $domain ], 'get_filename' ) ) {
			$mo_filename = $l10n[ $domain ]->get_filename();

			if ( is_string($mo_filename) ) {

				// $mo_filename does not end with string $locale
				if ( substr( strtolower( $mo_filename ), -$length ) == strtolower( $localemo ) ) {
					return true;
				} else {
					return false;
				}
			}
			return true;
		}

		// if something is not as expected, return true so that we do not interfere
		return true;
	}

	public function verify_locale_of_loaded_textdomain() {
		global $l10n;
		if ( ! empty( $l10n ) && is_array( $l10n ) ) {

			$reload_domains = array();
			$locale         = get_locale();


			foreach ( $l10n as $domain => $item ) {
				if ( ! $this->is_domain_loaded_in_locale( $domain, $locale ) ) {
					$reload_domains[] = $domain;
				}
			}

			foreach ( $reload_domains as $domain ) {
				// Skip WP_Translations (no get_filename() to rewrite); they already load in the
				// correct locale now that determine_locale() is aligned, and this avoids a fatal.
				if ( isset( $l10n[ $domain ] ) && is_object( $l10n[ $domain ] ) && method_exists( $l10n[ $domain ], 'get_filename' ) ) {
					$path     = $l10n[ $domain ]->get_filename();
					$new_path = preg_replace( '/' . $domain . '-(.*).mo$/i', $domain . '-' . $locale . '.mo', $path );
					if ( $new_path !== $path ) {
						unset( $l10n[ $domain ] );
						load_textdomain( $domain, $new_path );
					}
				}
			}
		}

		// do this function only once per execution. The init hook can be called more than once
		remove_action( 'trp_call_gettext_filters', array( $this, 'verify_locale_of_loaded_textdomain' ) );
	}

	/**
	 * Function that determines if an ajax request came from the frontend
	 * @return bool
	 */
	static function is_ajax_on_frontend() {

		/* for our own actions return false */
		if ( isset( $_REQUEST['action'] ) && strpos( sanitize_text_field( $_REQUEST['action'] ), 'trp_' ) === 0 ) {
			return false;
		}

		$trp           = TRP_Translate_Press::get_trp_instance();
		$url_converter = $trp->get_component( "url_converter" );

		//check here for wp ajax or woocommerce ajax
		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX ) ) {
			$referer = '';
			if (!empty( $_REQUEST['_wp_http_referer']) && is_string( $_REQUEST['_wp_http_referer'] ) ){
				// USUALLY this one is actually REQUEST_URI from the previous page. It's set by the wp_nonce_field() and wp_referer_field()
				// wp_get_referer() returns $_SERVER['REQUEST_URI'] from the prev page (not a full URL)
                // HOWEVER, the _wp_http_referer can be manually set by a plugin, so it can be a FULL URL in some cases
				$referer = wp_unslash( esc_url_raw( $_REQUEST['_wp_http_referer'] ) );
			} elseif (!empty($_SERVER['HTTP_REFERER']) && is_string( $_SERVER['HTTP_REFERER'] ) ) {
				// this one is an actual URL that the browser sets.
				$referer = wp_unslash( esc_url_raw( $_SERVER['HTTP_REFERER'] ) );
			}

			// No referer usually means a server-side loopback (Action Scheduler, WP_Async_Request etc.), not a visitor's page.
			// Treat it as not front-end, unless something in the request still points to a translated language.
			if ( $referer === '' && self::is_referer_less_request_in_default_language( $url_converter ) ) {
				return apply_filters( 'trp_is_ajax_on_frontend_without_referer', false );
			}

			//if the request did not come from the admin set proper variables for the request (being processed in ajax they got lost) and return true
            // Remove the absolute home prefix from the referer and admin URL
            $referer_uri    = trp_remove_prefix($url_converter->get_abs_home(), $referer);
            $admin_uri      = trp_remove_prefix($url_converter->get_abs_home(), admin_url());
            if(!(strpos(trim($referer_uri, '/\\'), trim($admin_uri, '/\\')) === 0)) {
                TRP_Gettext_Manager::set_vars_in_frontend_ajax_request( $referer );
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether an ajax request without a referer has nothing that points to a translated language:
	 * no trp-form-language field, not a WooCommerce ajax call and no language in the request URL (or domain).
	 *
	 * @param TRP_Url_Converter $url_converter
	 * @return bool
	 */
	protected static function is_referer_less_request_in_default_language( $url_converter ) {
		if ( ! empty( $_REQUEST['trp-form-language'] ) || ! empty( $_GET['wc-ajax'] ) || ( defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX ) ) {
			return false;
		}

		$trp          = TRP_Translate_Press::get_trp_instance();
		$settings     = $trp->get_component( 'settings' )->get_settings();
		$url_language = $url_converter->get_lang_from_url_string( $url_converter->cur_page_url( false ) );

		return empty( $url_language ) || $url_language === $settings['default-language'];
	}

	/**
	 * Function that sets the needed vars in the ajax request. Beeing ajax the globals got reset and also the REQUEST globals
	 *
	 * @param $referer
	 */
	static function set_vars_in_frontend_ajax_request( $referer ) {

		/* for our own actions don't do nothing */
		if ( isset( $_REQUEST['action'] ) && strpos( sanitize_text_field( $_REQUEST['action'] ), 'trp_' ) === 0 ) {
			return;
		}

		/* if the request came from preview mode make sure to keep it */
		if ( strpos( $referer, 'trp-edit-translation=preview' ) !== false && ! isset( $_REQUEST['trp-edit-translation'] ) ) {
			$_REQUEST['trp-edit-translation'] = 'preview';
		}

		if ( strpos( $referer, 'trp-edit-translation=preview' ) !== false && strpos( $referer, 'trp-view-as=' ) !== false && strpos( $referer, 'trp-view-as-nonce=' ) !== false ) {
			$parts = parse_url( $referer );
			parse_str( $parts['query'], $query );
			$_REQUEST['trp-view-as']       = $query['trp-view-as'];
			$_REQUEST['trp-view-as-nonce'] = $query['trp-view-as-nonce'];
		}

		global $TRP_LANGUAGE;
		$trp           = TRP_Translate_Press::get_trp_instance();
		$url_converter = $trp->get_component( 'url_converter' );
		$settings_obj  = new TRP_Settings();
		$settings      = $settings_obj->get_settings();

		if ( isset( $_REQUEST['trp-form-language'] ) && ! empty( $_REQUEST['trp-form-language'] ) ) {
			$form_language_slug = sanitize_text_field( wp_unslash( $_REQUEST['trp-form-language'] ) );
			$form_language      = array_search( $form_language_slug, $settings['url-slugs'], true );

			if ( ! empty( $form_language ) ) {
				$TRP_LANGUAGE = $form_language;
				return;
			}
		}

		$referer_language = $url_converter->get_lang_from_url_string( $referer );

		if ( ! empty( $referer_language ) ) {
			$TRP_LANGUAGE = $referer_language;
		} elseif ( empty( $TRP_LANGUAGE ) ) {
			$TRP_LANGUAGE = $settings["default-language"];
		}
	}


	/**
	 * function that machine translates gettext strings
	 */
	public function machine_translate_gettext() {
		$this->flush_deferred_gettext_storage_and_mt();

		/* @todo  set the original language to detect and also decide if we automatically translate for the default language */
		global $TRP_LANGUAGE, $trp_gettext_strings_for_machine_translation;
		if ( ! empty( $trp_gettext_strings_for_machine_translation ) ) {
			$this->machine_translate_gettext_queue( $trp_gettext_strings_for_machine_translation, $TRP_LANGUAGE );
			$trp_gettext_strings_for_machine_translation = array();
		}
	}

	/**
	 * Resolve all gettext rows observed during the request and process deferred MT.
	 *
	 * Runtime misses are collected in memory and flushed in batches at shutdown
	 * to avoid one DB query per gettext string.
	 *
	 * @return void
	 */
	protected function flush_deferred_gettext_storage_and_mt() {
		$pending_storage       = $this->process_gettext->get_pending_gettext_storage();
		$pending_mt_candidates = $this->process_gettext->get_pending_gettext_mt_candidates();

		if ( empty( $pending_storage ) && empty( $pending_mt_candidates ) ) {
			return;
		}

		if ( ! $this->trp_query ) {
			$trp             = TRP_Translate_Press::get_trp_instance();
			$this->trp_query = $trp->get_component( 'query' );
		}

		$chunk_size            = max( 1, (int) apply_filters( 'trp_gettext_pending_storage_chunk_size', 250 ) );
		$gettext_insert_update = $this->trp_query->get_query_component( 'gettext_insert_update' );

		foreach ( $pending_storage as $language => $items ) {
			if ( empty( $items ) || ! in_array( $language, $this->settings['translation-languages'] ) ) {
				continue;
			}

			$resolved_rows = $this->resolve_pending_gettext_rows( $language, $items, $chunk_size );
			$missing      = array_diff_key( $items, $resolved_rows );

			if ( ! empty( $missing ) ) {
				foreach ( array_chunk( $missing, $chunk_size, true ) as $missing_chunk ) {
					$gettext_insert_update->insert_gettext_strings( array_values( $missing_chunk ), $language );
				}

				$inserted_rows  = $this->resolve_pending_gettext_rows( $language, $missing, $chunk_size );
				$resolved_rows = array_replace( $resolved_rows, $inserted_rows );
			}

			$this->update_existing_gettext_rows_from_observed_translations( $language, $items, $resolved_rows );
			$language_mt_candidates = isset( $pending_mt_candidates[ $language ] ) ? $pending_mt_candidates[ $language ] : array();
			$mt_queue               = $this->build_deferred_gettext_mt_queue( $language, $language_mt_candidates, $resolved_rows );

			if ( ! empty( $mt_queue ) ) {
				$this->machine_translate_gettext_queue( $mt_queue, $language );
			}
		}

		$this->process_gettext->clear_pending_gettext_buffers();
	}

	/**
	 * Resolve observed gettext items to existing DB rows in bounded chunks.
	 *
	 * @param string $language   Target language code.
	 * @param array  $items      Observed gettext items keyed by storage key.
	 * @param int    $chunk_size Number of items to resolve per DB query.
	 *
	 * @return array
				 */
	protected function resolve_pending_gettext_rows( $language, $items, $chunk_size ) {
		$resolved_rows = array();

		foreach ( array_chunk( $items, $chunk_size, true ) as $items_chunk ) {
			$rows = $this->trp_query->get_gettext_rows_by_composite_keys( $language, array_values( $items_chunk ) );

			foreach ( $rows as $row ) {
				$key = $this->get_gettext_row_storage_key( $language, $row );

				if ( $key ) {
					if ( isset( $resolved_rows[ $key ] ) && ! empty( $resolved_rows[ $key ]['translated'] ) ) {
						continue;
					}

					$resolved_rows[ $key ] = $row;
				}
			}
		}

		return $resolved_rows;
	}

	/**
	 * Build the request-local storage key for a gettext DB row.
	 *
	 * @param string $language Target language code.
	 * @param array  $row      Gettext row joined with the original gettext table.
	 *
	 * @return string
	 */
	protected function get_gettext_row_storage_key( $language, $row ) {
		$original    = ! empty( $row['original'] ) ? $row['original'] : $row['tt_original'];
		$domain      = ! empty( $row['domain'] ) ? $row['domain'] : $row['tt_domain'];
		$context     = ! empty( $row['context'] ) ? $row['context'] : 'trp_context';
		$plural_form = isset( $row['plural_form'] ) ? (int) $row['plural_form'] : 0;

		if ( empty( $original ) || empty( $domain ) ) {
			return '';
		}

		return $this->process_gettext->get_gettext_storage_key( $language, $context, $plural_form, $domain, $original );
	}

	/**
	 * Persist observed language-file translations into existing untranslated rows.
	 *
	 * This updates storage/status only. Language-file translations stay on the
	 * non-runtime gettext status because they should not override themselves.
	 *
	 * @param string $language      Target language code.
	 * @param array  $items         Observed gettext items keyed by storage key.
	 * @param array  $resolved_rows Existing DB rows keyed by storage key.
	 *
	 * @return void
	 */
	protected function update_existing_gettext_rows_from_observed_translations( $language, $items, &$resolved_rows ) {
		$updates = array();

		foreach ( $items as $key => $item ) {
			if ( empty( $resolved_rows[ $key ]['id'] ) || empty( $item['translated'] ) || ! empty( $resolved_rows[ $key ]['translated'] ) ) {
				continue;
			}

			$updates[] = array(
				'id'         => (int) $resolved_rows[ $key ]['id'],
				'translated' => $item['translated'],
				'status'     => $this->trp_query->get_constant_gettext_translated_in_language_file(),
			);

			$resolved_rows[ $key ]['translated'] = $item['translated'];
			$resolved_rows[ $key ]['status']     = $this->trp_query->get_constant_gettext_translated_in_language_file();
		}

		if ( ! empty( $updates ) ) {
			$gettext_insert_update = $this->trp_query->get_query_component( 'gettext_insert_update' );
			$gettext_insert_update->update_gettext_strings( $updates, $language, array( 'id', 'translated', 'status' ) );
		}
	}

	/**
	 * Build an id-backed gettext machine translation queue after storage resolution.
	 *
	 * @param string $language      Target language code.
	 * @param array  $mt_candidates Observed MT candidates keyed by storage key.
	 * @param array  $resolved_rows Existing DB rows keyed by storage key.
	 *
	 * @return array
	 */
	protected function build_deferred_gettext_mt_queue( $language, $mt_candidates, $resolved_rows ) {
		if ( empty( $mt_candidates ) ) {
			return array();
		}

		$mt_queue = array();

		foreach ( $mt_candidates as $key => $candidate ) {
			if ( empty( $resolved_rows[ $key ]['id'] ) || ! empty( $resolved_rows[ $key ]['translated'] ) ) {
				continue;
			}

			$db_id = (int) $resolved_rows[ $key ]['id'];
			if ( isset( $mt_queue[ $db_id ] ) ) {
				continue;
			}

			$mt_queue[ $db_id ] = array(
				'id'              => $db_id,
				'original'        => $candidate['original'],
				'translated'      => '',
				'domain'          => $candidate['domain'],
				'status'          => $this->trp_query->get_constant_machine_translated(),
				'context'         => $candidate['context'],
				'plural_form'     => $candidate['plural_form'],
				'original_plural' => $candidate['original_plural'],
			);
		}

		return $mt_queue;
	}

	/**
	 * Machine translate gettext rows and persist successful MT translations.
	 *
	 * @param array  $gettext_queue Queue of gettext rows keyed by DB id or numeric index.
	 * @param string $language      Target language code.
	 *
	 * @return void
	 */
	protected function machine_translate_gettext_queue( $gettext_queue, $language ) {
		if ( empty( $gettext_queue ) || empty( $language ) ) {
			return;
		}

		if ( ! $this->machine_translator ) {
			$trp                      = TRP_Translate_Press::get_trp_instance();
			$this->machine_translator = $trp->get_component( 'machine_translator' );
		}

		// Gettext strings are considered by default to be in the English language.
		$source_language = apply_filters( 'trp_gettext_source_language', 'en_US', $language, array(), $gettext_queue );
		if ( ! $this->machine_translator->is_available( array( $source_language, $language ) ) ) {
			return;
		}

		$gettext_queue = array_values( $gettext_queue );
				$new_strings = array();
		foreach ( $gettext_queue as $gettext_string ) {
			$new_strings[] = ( $gettext_string['original_plural'] && (int)$gettext_string['plural_form'] > 0 ) ? $gettext_string['original_plural'] : $gettext_string['original'];
				}

				if ( ! $this->trp_query ) {
					$trp             = TRP_Translate_Press::get_trp_instance();
					$this->trp_query = $trp->get_component( 'query' );
				}

		$gettext_insert_update = $this->trp_query->get_query_component( 'gettext_insert_update' );

		if ( apply_filters( 'trp_gettext_allow_machine_translation', true, $source_language, $language, $new_strings, $gettext_queue ) ) {
					global $trp_machine_translation_deadline;
					if ( ! isset( $trp_machine_translation_deadline ) ) {
						$trp_machine_translation_deadline = microtime( true ) + apply_filters( 'trp_machine_translation_time_budget', 10 );
					}

			$unique_strings = array_values( array_unique( $new_strings ) );
			foreach ( array_chunk( $unique_strings, $this->machine_translator->get_chunk_size() ) as $strings_chunk ) {
						if ( microtime( true ) > $trp_machine_translation_deadline ) {
							break;
						}

				$machine_strings = $this->machine_translator->translate( $strings_chunk, $language, $source_language, 'gettext' );
				if ( empty( $machine_strings ) ) {
							continue;
						}

				// still saved, so it is never re-sent and re-billed; is_sprintf_compatible() keeps it off the page
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					foreach ( $machine_strings as $original => $machine_string ) {
						if ( ! $this->process_gettext->is_sprintf_compatible( $machine_string, $original ) ) {
							error_log( 'TranslatePress: the ' . $language . ' machine translation of the gettext string "' . $original . '" changed its printf placeholders, so the original is displayed instead. Edit the translation to fix it. Received: ' . substr( $machine_string, 0, 200 ) );
						}
					}
				}

						$strings_to_save = array();
				foreach ( $new_strings as $key => $new_string ) {
					if ( isset( $machine_strings[ $new_string ] ) ) {
						$gettext_queue[ $key ]['translated'] = $machine_strings[ $new_string ];
						$strings_to_save[]                   = $gettext_queue[ $key ];
							}
						}

				// keep a saved chunk's locks as recently translated markers; when the save is
				// skipped or fails, delete them so the strings can be retried right away
				$chunk_saved = false;
				if ( ! empty( $strings_to_save ) ) {
					$chunk_saved = $gettext_insert_update->update_gettext_strings( $strings_to_save, $language, array( 'id', 'original', 'translated', 'domain', 'status', 'plural_form' ) );
				}
				$this->machine_translator->release_locks( $chunk_saved );
					}

			return;
						}

		$machine_strings = apply_filters( 'trp_gettext_machine_translate_strings', array(), $new_strings, $language, $gettext_queue );
		if ( empty( $machine_strings ) ) {
			return;
					}

		foreach ( $new_strings as $key => $new_string ) {
			if ( isset( $machine_strings[ $new_string ] ) ) {
				$gettext_queue[ $key ]['translated'] = $machine_strings[ $new_string ];
				}
			}

		$gettext_queue = array_filter( $gettext_queue, function( $gettext_string ) {
			return ! empty( $gettext_string['translated'] );
		} );

		if ( ! empty( $gettext_queue ) ) {
			$gettext_insert_update->update_gettext_strings( $gettext_queue, $language, array( 'id', 'original', 'translated', 'domain', 'status', 'plural_form' ) );
		}
	}


	/**
	 * make sure we remove the trp-gettext wrap from the format the date_i18n receives
	 * ideally if in the gettext filter we would know 100% that a string is a valid date format then we would not wrap it but it seems that it is not easy to determine that ( explore further in the future $d = DateTime::createFromFormat('Y', date('y a') method); )
	 */
	public function handle_date_i18n_function_for_gettext( $j, $dateformatstring, $unixtimestamp, $gmt ) {

		/* remove trp-gettext wrap */
		$dateformatstring = preg_replace( '/#!trpst#trp-gettext (.*?)#!trpen#/i', '', $dateformatstring );
		$dateformatstring = preg_replace( '/#!trpst#(.?)\/trp-gettext#!trpen#/i', '', $dateformatstring );


		global $wp_locale;
		$i = $unixtimestamp;

		if ( false === $i ) {
			$i = current_time( 'timestamp', $gmt );
		}

		if ( ( ! empty( $wp_locale->month ) ) && ( ! empty( $wp_locale->weekday ) ) ) {
			$datemonth            = $wp_locale->get_month( date( 'm', $i ) );
			$datemonth_abbrev     = $wp_locale->get_month_abbrev( $datemonth );
			$dateweekday          = $wp_locale->get_weekday( date( 'w', $i ) );
			$dateweekday_abbrev   = $wp_locale->get_weekday_abbrev( $dateweekday );
			$datemeridiem         = $wp_locale->get_meridiem( date( 'a', $i ) );
			$datemeridiem_capital = $wp_locale->get_meridiem( date( 'A', $i ) );
			$dateformatstring     = ' ' . $dateformatstring;
			$dateformatstring     = preg_replace( "/([^\\\])D/", "\\1" . backslashit( $dateweekday_abbrev ), $dateformatstring );
			$dateformatstring     = preg_replace( "/([^\\\])F/", "\\1" . backslashit( $datemonth ), $dateformatstring );
			$dateformatstring     = preg_replace( "/([^\\\])l/", "\\1" . backslashit( $dateweekday ), $dateformatstring );
			$dateformatstring     = preg_replace( "/([^\\\])M/", "\\1" . backslashit( $datemonth_abbrev ), $dateformatstring );
			$dateformatstring     = preg_replace( "/([^\\\])a/", "\\1" . backslashit( $datemeridiem ), $dateformatstring );
			$dateformatstring     = preg_replace( "/([^\\\])A/", "\\1" . backslashit( $datemeridiem_capital ), $dateformatstring );

			$dateformatstring = substr( $dateformatstring, 1, strlen( $dateformatstring ) - 1 );
		}
		$timezone_formats    = array( 'P', 'I', 'O', 'T', 'Z', 'e' );
		$timezone_formats_re = implode( '|', $timezone_formats );
		if ( preg_match( "/$timezone_formats_re/", $dateformatstring ) ) {
			$timezone_string = get_option( 'timezone_string' );
			if ( $timezone_string ) {
				$timezone_object = timezone_open( $timezone_string );
                //date_create( null, $timezone_object );
                //date_create() passing null to parameter #1 ($datetime) of type string is deprecated, from what I found online the null should be replaced with ''
				$date_object     = date_create( '', $timezone_object );
				foreach ( $timezone_formats as $timezone_format ) {
					if ( false !== strpos( $dateformatstring, $timezone_format ) ) {
						$formatted        = date_format( $date_object, $timezone_format );
						$dateformatstring = ' ' . $dateformatstring;
						$dateformatstring = preg_replace( "/([^\\\])$timezone_format/", "\\1" . backslashit( $formatted ), $dateformatstring );
						$dateformatstring = substr( $dateformatstring, 1, strlen( $dateformatstring ) - 1 );
					}
				}
			}
		}
		$j = @date( $dateformatstring, $i );

		return $j;

	}

	/**
	 * Strip gettext tags from urls that were parsed by esc_url
	 *
	 * Esc_url() replaces spaces with %20. This is why it is not automatically stripped like the rest of the urls.
	 *
	 * @param $good_protocol_url
	 * @param $original_url
	 * @param $_context
	 *
	 * @return mixed
	 * @since 1.3.8
	 *
	 */
	public function trp_strip_gettext_tags_from_esc_url( $good_protocol_url, $original_url, $_context ) {
		if ( strpos( $good_protocol_url, '%20data-trpgettextoriginal=' ) !== false ) {
			// first replace %20 with space  so that gettext tags can be stripped.
			$good_protocol_url = str_replace( '%20data-trpgettextoriginal=', ' data-trpgettextoriginal=', $good_protocol_url );
			$good_protocol_url = TRP_Gettext_Manager::strip_gettext_tags( $good_protocol_url );
		}

		return $good_protocol_url;
	}

	/**
	 * Filter sanitize_title() to use our own remove_accents() function so it's based on the default language, not current locale.
	 *
	 * Also removes trp gettext tags before running the filter because it strip # and ! and / making it impossible to strip the #trpst later
	 *
	 * @param string $title
	 * @param string $raw_title
	 * @param string $context
	 *
	 * @return string
	 * @since 1.3.1
	 *
	 */
	public function trp_sanitize_title( $title, $raw_title, $context ) {
		// remove trp_tags before sanitization, because otherwise some characters (#,!,/, spaces ) are stripped later, and it becomes impossible to strip trp-gettext later
		$raw_title = TRP_Gettext_Manager::strip_gettext_tags( $raw_title );

		if ( 'save' == $context ) {
			$title = trp_remove_accents( $raw_title );
		}

		remove_filter( 'sanitize_title', array( $this, 'trp_sanitize_title' ), 1 );
		$title = apply_filters( 'sanitize_title', $title, $raw_title, $context );
		add_filter( 'sanitize_title', array( $this, 'trp_sanitize_title' ), 1, 3 );

		return $title;
	}


	/**
	 * function that strips the gettext tags from a string
	 *
	 * @param $string
	 *
	 * @return mixed
	 */
	static function strip_gettext_tags( $string ) {
		if ( is_string( $string ) && strpos( $string, 'data-trpgettextoriginal=' ) !== false ) {
			// \d* (not \d+) so the empty-id case (#!trpst#trp-gettext data-trpgettextoriginal=#!trpen#)
			// emitted when $db_id is empty in process_gettext_strings is also stripped — otherwise the
			// "data-trpgettextoriginal=" substring leaks into the cleaned output and gets persisted.
			// final 'i' is for case insensitive. same for the 'i' in  str_ireplace
			$string = preg_replace( '/ data-trpgettextoriginal=\d*#!trpen#/i', '', $string );
			$string = preg_replace( '/data-trpgettextoriginal=\d*#!trpen#/i', '', $string );//sometimes it can be without space
			$string = str_ireplace( '#!trpst#trp-gettext', '', $string );
			$string = str_ireplace( '#!trpst#/trp-gettext', '', $string );
			$string = str_ireplace( '#!trpst#\/trp-gettext', '', $string );
			$string = str_ireplace( '#!trpen#', '', $string );
		}


		return $string;
	}


	/**
	 * Function that inserts in db translation from language files for specified original string ids for a specific language
	 * This requests changes locale from the very beginning so all the active plugins/theme load their textdomain translations
	 *
	 * Also creates plural entries for all plural forms so we have an id
	 *
	 * @param $dictionary
	 * @param $language
	 *
	 * @return void
	 */
	public function add_missing_language_file_translations( $dictionary, $language ) {
        // Ensure translation files are loaded with the correct locale
        $locale   = determine_locale();
        $switched = switch_to_locale( $language );

        // This means that the language is not supported by WordPress. Either a custom language or a language that we support but WordPress does not.
        if ( !$switched && $language !== $locale )
            return;

        $trp_plural_forms    = $this->get_gettext_component( 'plural_forms' );
		if ( ! $this->trp_query ) {
			$trp             = TRP_Translate_Press::get_trp_instance();
			$this->trp_query = $trp->get_component( 'query' );
		}
		$insert_gettext_strings = array();
		$update_gettext_strings = array();

		$number_of_plural_forms = $trp_plural_forms->get_number_of_plural_forms( $language );
		if ( ! empty( $dictionary ) ) {
			foreach ( $dictionary as $current_key => $current_string ) {

				$translations = get_translations_for_domain( $current_string['domain'] );
				$context      = ( $current_string['context'] === 'trp_context' ) ? null : $current_string['context'];
				$translated = '';
				if ( $current_string['original_plural'] ) {

                    /* For some domains in some languages, $translations object is not of type Translations
                     * (but of type WP_Translations) on WP version 6.5+. So it doesn't have this method.
                     * Todo: find an alternative to access plural forms for these cases
                     */
                    if ( !method_exists( $translations, 'translate_entry' ) ) {
                        continue;
                    }

					// Insert translation for all other plural forms than the current one
					for ( $plural_form_i = 0; $plural_form_i < $number_of_plural_forms; $plural_form_i ++ ) {
						if ( $plural_form_i == $current_string['plural_form'] ) {
							continue;
						}
						$translation_exists_for_plural_form = false;
						$plural_form_id_translation_table   = null;
						foreach ( $dictionary as $secondary_key => $secondary_string ) {
							if ( $secondary_key == $current_key ) {
								continue;
							}
							if ( $current_string['ot_id'] === $secondary_string['ot_id'] &&
							     $secondary_string['plural_form'] == $plural_form_i
							) {
								if ( $secondary_string['status'] == 0 ) {
									$plural_form_id_translation_table = $secondary_string['id'];
								} else {
									$translation_exists_for_plural_form = true;
								}
								break;
							}
						}
						if ( ! $translation_exists_for_plural_form ) {
							$translated = $trp_plural_forms->translate_plural( $current_string['original'], $current_string['original_plural'], $plural_form_i, $context, $translations );

							if ( $translated && $translated != $current_string['original'] && $translated != $current_string['original_plural'] ) {
								$status = $this->trp_query->get_constant_gettext_translated_in_language_file();
							}else {
								$translated = '';
								$status = $this->trp_query->get_constant_not_translated();
							}
							if ( $plural_form_id_translation_table ) {
								if ( $translated ) {
									$update_gettext_strings[] = array(
										'id'         => $plural_form_id_translation_table,
										'translated' => $translated
									);
								}
							} else {
								$insert_gettext_strings[] = array(
									'original_id'     => $current_string['ot_id'],
									'original'        => $current_string['original'],
									'translated'      => $translated,
									'domain'          => $current_string['domain'],
									'plural_form'     => $plural_form_i,
									'status'          => $status,
									'context'         => $current_string['context'],
									'original_plural' => $current_string['original_plural']
								);
							}
						}

					}

					// Insert translation for this current string
					if ( $current_string['status'] == 0 ) {
						$translated = $trp_plural_forms->translate_plural( $current_string['original'], $current_string['original_plural'], (int) $current_string['plural_form'], $context, $translations );
					}
				} else {
					if ( $current_string['status'] == 0 && empty( $current_string['translated'] ) ) {
							$translated = $translations->translate( $current_string['original'], $context );
					}
				}
				if ( $current_string['status'] == 0 && empty( $current_string['translated'] ) ) {
					if ( $translated && $translated != $current_string['original'] && $translated != $current_string['original_plural'] ) {
						$status = $this->trp_query->get_constant_gettext_translated_in_language_file();
					} else {
						$translated = '';
						$status     = $this->trp_query->get_constant_not_translated();
					}

					if ( $current_string['id'] ) {
						if ( $translated ) {
							$update_gettext_strings[] = array(
								'id'         => $current_string['id'],
								'translated' => $translated,
								'status'     => $this->trp_query->get_constant_gettext_translated_in_language_file()
							);
						}
					} else {
						$insert_gettext_strings[] = array(
							'original_id'     => $current_string['ot_id'],
							'original'        => $current_string['original'],
							'translated'      => $translated,
							'domain'          => $current_string['domain'],
							'plural_form'     => (int) $current_string['plural_form'],
							'status'          => $status,
							'context'         => $current_string['context'],
							'original_plural' => $current_string['original_plural']
						);
					}
				}

			}
			$gettext_insert_update = $this->trp_query->get_query_component( 'gettext_insert_update' );
			$gettext_insert_update->insert_gettext_strings($insert_gettext_strings, $language);
			$gettext_insert_update->update_gettext_strings($update_gettext_strings, $language, array('translated', 'id', 'status'));

            if ( $switched )
                restore_previous_locale();
		}
	}
}
