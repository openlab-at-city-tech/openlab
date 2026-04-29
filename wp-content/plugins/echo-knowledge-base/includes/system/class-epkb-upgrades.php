<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if plugin upgrade to a new version requires any actions like database upgrade
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Upgrades {

	const GRID_UPGRADE_DONE = 3;
	const NOT_INITIALIZED = '12.30.99'; // TODO remove 2026

	public function __construct() {
		// will run after plugin is updated but not always like front-end rendering
		add_action( 'admin_init', array( 'EPKB_Upgrades', 'update_plugin_version' ) );

		// show initial page after install addons
		//add_action( 'admin_init', array( 'EPKB_Upgrades', 'initial_addons_setup' ), 1 );

		// show initial page after install
		add_action( 'admin_init', array( 'EPKB_Upgrades', 'initial_setup' ), 20 );

		// show additional messages on the plugins page
		add_action( 'in_plugin_update_message-echo-knowledge-base/echo-knowledge-base.php',  array( $this, 'in_plugin_update_message' ) );
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
	}

	/**
	 * Display license screen on addon first activation or upgrade - redirect admin user once on visiting any KB admin page
	 */
	public static function initial_addons_setup() {

		// continue only for admin user, on any KB admin page
		if ( ! current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) || ! is_admin() || ! EPKB_KB_Handler::is_kb_request() ) {
			return;
		}

		// ensure all transients are deleted before redirecting user
		$redirect_to_licenses = false;
		$addons = [ 'emkb', 'epie',	'elay', 'kblk', 'eprf',	'asea',	'widg', 'amgp', 'amcr' ];
		foreach ( $addons as $addon ) {

			// check is addon not recently activated
			$addon_activated = get_transient( "_{$addon}_plugin_activated" );
			if ( ! empty( $addon_activated ) ) {
				delete_transient( "_{$addon}_plugin_activated" );
				$redirect_to_licenses = true;
			}
		}

		// redirect to Getting Started Licenses tab
		if ( ! empty( $redirect_to_licenses ) ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) . '&page=ep'.'kb-add-ons&epkb_after_addons_setup#licenses') );
			exit;
		}
	}

	/**
	 * Trigger display of wizard setup screen on plugin first activation or upgrade; does NOT work if multiple plugins installed at the same time
	 */
	public static function initial_setup() {

		$kb_version = EPKB_Utilities::get_wp_option( 'epkb_version', null );
		if ( empty( $kb_version) ) {
			return;
		}

		// ignore if plugin not recently activated
		$plugin_installed = get_transient( '_epkb_plugin_installed' );
		if ( empty( $plugin_installed ) ) {
			return;
		}

		// return if activating from network or doing bulk activation
		if ( is_network_admin() || isset($_GET['activate-multi']) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_epkb_plugin_installed' );

		// if setup ran then do not proceed
		if ( ! EPKB_Core_Utilities::run_setup_wizard_first_time() ) {
			return;
		}

		// run setup wizard
		wp_safe_redirect( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) . '&page=epkb-kb-configuration&setup-wizard-on' ) );
		exit;
	}

	/**
	 * If necessary run plugin database updates
	 */
	public static function update_plugin_version() {

		// ensure the plugin version and configuration is set
		$last_version = EPKB_Utilities::get_wp_option( 'epkb_version', null );
		if ( empty( $last_version ) ) {
			EPKB_Utilities::save_wp_option( 'epkb_version', Echo_Knowledge_Base::$version );
			epkb_get_instance()->kb_config_obj->set_value( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'first_plugin_version', Echo_Knowledge_Base::$version );
			return;
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID, true );
		if ( is_wp_error( $kb_config ) ) {
			// TODO report error in admin page
			return;
		}

		// initialize plugin first version if empty or not initialized
		if ( empty( $kb_config['first_plugin_version'] ) || $kb_config['first_plugin_version'] == self::NOT_INITIALIZED ) {
			$first_plugin_version = EPKB_Utilities::get_wp_option( 'epkb_version_first', '' );
			$first_plugin_version = empty( $first_plugin_version ) ? $last_version : $first_plugin_version;
			epkb_get_instance()->kb_config_obj->set_value( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'first_plugin_version', $first_plugin_version );
		}

		// initialize plugin upgraded version if empty or not initialized
		$last_upgrade_version = $kb_config['upgrade_plugin_version'];
		if ( empty( $last_upgrade_version ) || $last_upgrade_version == self::NOT_INITIALIZED ) {
			$last_upgrade_version = $last_version;
			epkb_get_instance()->kb_config_obj->set_value( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'upgrade_plugin_version', $last_upgrade_version );
		}

		// if plugin is up-to-date then return
		if ( version_compare( $last_upgrade_version, Echo_Knowledge_Base::$version, '>=' ) ) {
			return;
		}

		// upgrade the plugin
		self::invoke_upgrades( $last_upgrade_version );

		EPKB_Utilities::save_wp_option( 'epkb_version', Echo_Knowledge_Base::$version );
	}

	/**
	 * Invoke each database update as necessary.
	 *
	 * @param $last_version
	 */
	private static function invoke_upgrades( $last_version ) {

		self::maybe_upgrade_ai_configuration();

		// update all KBs
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		foreach ( $all_kb_configs as $kb_config ) {

			self::run_upgrades( $kb_config, $last_version );

			$kb_config['upgrade_plugin_version'] = Echo_Knowledge_Base::$version;

			// store the updated KB data
			epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_config['id'], $kb_config );
		}

		// ensure default KB is updated
		epkb_get_instance()->kb_config_obj->set_value( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'upgrade_plugin_version', Echo_Knowledge_Base::$version );
	}

	public static function run_upgrades( &$kb_config, $last_version ) {

		if ( version_compare( $last_version, '11.30.0', '<' ) ) {
			self::upgrade_to_v11_30_0( $kb_config );
		}

		if ( version_compare( $last_version, '11.30.1', '<' ) ) {
			self::upgrade_to_v11_30_1( $kb_config );
		}

		if ( version_compare( $last_version, '11.31.0', '<' ) ) {
			self::upgrade_to_v11_31_0( $kb_config );
		}
		
		if ( version_compare( $last_version, '11.40.0', '<' ) ) {
			self::upgrade_to_v11_40_0( $kb_config );
		}

		if ( version_compare( $last_version, '11.41.0', '<' ) ) {
			self::upgrade_to_v11_41_0( $kb_config );
		}

		if ( version_compare( $last_version, '12.0.0', '<' ) ) {
			self::upgrade_to_v12_0_0( $kb_config );
		}

		if ( version_compare( $last_version, '12.11.0', '<' ) ) {
			self::upgrade_to_v12_11_0( $kb_config );
		}

		if ( version_compare( $last_version, '12.21.0', '<' ) ) {
			self::upgrade_to_v12_21_0( $kb_config );
		}

		if ( version_compare( $last_version, '12.30.0', '<' ) ) {
			self::upgrade_to_v12_30_0( $kb_config );
		}

		if ( version_compare( $last_version, '12.42.0', '<' ) ) {
			self::upgrade_to_v12_42_0( $kb_config );
		}

		if ( version_compare( $last_version, '13.11.0', '<' ) ) {
			self::upgrade_to_v13_11_0( $kb_config );
		}

		if ( version_compare( $last_version, '13.60.0', '<' ) ) {	
			self::upgrade_to_v13_60_0( $kb_config );
		}

		if ( version_compare( $last_version, '15.210.0', '<' ) ) {
			self::upgrade_to_v15_210_0( $kb_config );
		}

		if ( version_compare( $last_version, '15.700.0', '<' ) ) {
			self::upgrade_to_v15_700_0( $kb_config );
		}

		if ( version_compare( $last_version, '15.900.0', '<' ) ) {
			self::upgrade_to_v15_900_0( $kb_config );
		}

		if ( version_compare( $last_version, '16.011.1', '<=' ) ) {
			self::upgrade_to_v16_011_1( $kb_config );
		}

		if ( version_compare( $last_version, '17.1.0', '<' ) ) {
			self::upgrade_to_v17_1_0();
		}

	}

	/**
	 * Migrate training data error records with empty content/deleted source to skipped status
	 */
	private static function upgrade_to_v17_1_0() {
		static $ai_training_data_migration_done = false;

		$ai_config = get_option( 'epkb_ai_configuration', array() );
		$has_ai_key = ! empty( $ai_config['ai_chatgpt_key'] ) || ! empty( $ai_config['ai_gemini_key'] );
		if ( $ai_training_data_migration_done || empty( $ai_config['ai_disclaimer_accepted'] ) || $ai_config['ai_disclaimer_accepted'] !== 'on' || ! $has_ai_key ) {
			return;
		}
		$ai_training_data_migration_done = true;

		$training_data_db = new EPKB_AI_Training_Data_DB();
		$training_data_db->migrate_error_to_skipped();
	}

	/**
	 * AI configuration migrations. Runs on every admin_init; idempotent via static flag.
	 *
	 * =========================================================================
	 * AI Provider/Model Maintenance Playbooks
	 * =========================================================================
	 *
		 * Architecture notes:
		 *  - Model IDs, aliases, defaults, supported params, and request formatting live in the
		 *    provider model catalogs:
		 *    class-epkb-chatgpt-model-catalog.php and class-epkb-gemini-model-catalog.php.
		 *  - EPKB_AI_Provider resolves provider + feature + preset into one runtime profile.
		 *    Stable preset routing is derived from each catalog's preset_key metadata, and
		 *    quizzes use the provider default model. Features and clients should consume the
		 *    runtime profile instead of hardcoding model logic.
		 *  - Chat and Search expose stable presets only. Quizzes use one internal runtime profile.
		 *
		 * Playbook A — Add a new model (no migration; catalog-first change):
		 *  1. Add the model entry to get_models() in the provider model catalog with:
		 *     name, type, description, default_params, supports_* flags, parameters,
		 *     option lists, and max_output_tokens_limit.
		 *  2. If it should back a stable Chat/Search preset, set or update its preset_key in
		 *     the catalog model entry.
		 *  3. If it should become the provider default (and therefore the quiz model), update
		 *     the catalog DEFAULT_MODEL constant.
		 *  4. Verify runtime resolution still matches the intended preset contract.
	 *
	 * Playbook B — Retire or rename a model (runs via maybe_upgrade_ai_configuration()):
	 *  1. Add 'old-id' => 'new-id' to the provider catalog DEPRECATED_MODELS constant.
	 *     That covers legacy stored model IDs when provider preset migration resolves them and
	 *     also covers runtime resolution.
	 *  2. If the replacement model needs preset or stored-config rewrites, add that logic to
	 *     EPKB_AI_Provider::migrate_ai_config(). Keep these migrations idempotent.
	 *  3. If the old ID appears outside the catalogs, remove or update those literals. In the
	 *     base plugin, remaining non-catalog literals should usually be limited to migration code.
	 *
	 * Playbook C — Add a brand-new generic parameter (not just a new model option):
	 *  1. Add support to the relevant model catalog:
	 *     supports_* flag, default_params, parameters list, options list, normalize_parameters(),
	 *     and apply_model_parameters().
	 *  2. Decide whether the parameter is internal-only or part of the stable preset contract.
	 *  3. If presets need different values, wire them into EPKB_AI_Provider runtime profiles.
	 *  4. If ai-features-pro is installed, keep AIPRO KB_Core request wrappers in sync so they
	 *     pass the new parameter through EPKB_AI_Provider::apply_model_parameters().
	 *
	 * Playbook D — Change the default provider (no migration; existing users keep their choice):
	 *  1. class-epkb-ai-config-specs.php — 'ai_provider' field 'default' in get_ai_config_specs().
	 *  2. class-epkb-ai-provider.php — normalize_provider() unknown-value fallback.
	 *  3. class-epkb-ai-provider.php — get_provider_label() else-branch (cosmetic).
	 *  4. class-epkb-ai-provider.php — get_provider_options() order (new default listed first).
	 *  5. class-epkb-ai-general-settings-tab.php — settings tab display order.
	 *  6. class-epkb-ai-training-data-config-specs.php — 'ai_training_data_provider' default.
	 *  7. class-epkb-ai-config-specs.php — collection fallback provider.
	 */
	private static function maybe_upgrade_ai_configuration() {
		static $ai_config_migration_done = false;

		if ( $ai_config_migration_done ) {
			return;
		}
		$ai_config_migration_done = true;

		$ai_config = get_option( 'epkb_ai_configuration', array() );
		if ( empty( $ai_config ) || ! is_array( $ai_config ) ) {
			return;
		}

		$migrated_config = EPKB_AI_Provider::migrate_ai_config( $ai_config );
		if ( maybe_serialize( $migrated_config ) === maybe_serialize( $ai_config ) ) {
			return;
		}

		update_option( 'epkb_ai_configuration', $migrated_config, true );
		EPKB_AI_Config_Specs::clear_cache();
	}

	/**
	 * Set glossary_enable to 'off' for existing users (16.011.1 or less). New users get default 'on' from spec.
	 */
	private static function upgrade_to_v16_011_1( &$kb_config ) {

		$kb_config['glossary_enable'] = 'off';
	}

	/**
	 * Run AI migration if legacy ai_key exists (for users who bypassed upgrade)
	 */
	public static function maybe_run_ai_migration() {
		$raw_config = get_option( 'epkb_ai_configuration', array() );
		if ( ! empty( $raw_config['ai_key'] ) ) {
			$dummy = array();
			self::upgrade_to_v15_700_0( $dummy );
		}
	}

	/**
	 * Migrate ai_search_mode from 'advanced_search' to 'smart_search'
	 */
	private static function upgrade_to_v15_900_0( &$kb_config ) {
		$raw_config = get_option( 'epkb_ai_configuration', array() );
		if ( ! empty( $raw_config['ai_search_mode'] ) && $raw_config['ai_search_mode'] === 'advanced_search' ) {
			$raw_config['ai_search_mode'] = 'smart_search';
			update_option( 'epkb_ai_configuration', $raw_config );
		}
	}

	/**
	 * Migrate AI settings to provider-specific fields (existing users are OpenAI)
	 */
	private static function upgrade_to_v15_700_0( &$kb_config ) {

		// Only run once (first KB triggers it)
		static $ai_migration_done = false;
		if ( $ai_migration_done ) {
			return;
		}
		$ai_migration_done = true;

		// Set chatgpt as default provider for existing users who don't have it set
		// New users will get 'gemini' from the default config spec
		$raw_config = get_option( 'epkb_ai_configuration', array() );
		if ( empty( $raw_config['ai_provider'] ) ) {
			$raw_config['ai_provider'] = 'chatgpt';
		}

		$updates = array();
		$use_cases = array( 'chat', 'search' );

		// Migrate ai_key to ai_chatgpt_key (all existing users are ChatGPT/OpenAI)
		if ( ! empty( $raw_config['ai_key'] ) && empty( $raw_config['ai_chatgpt_key'] ) ) {
			$updates['ai_chatgpt_key'] = $raw_config['ai_key'];
		}

		foreach ( $use_cases as $use_case ) {
			$preset_field = 'ai_' . $use_case . '_preset';
			if ( ! empty( $raw_config[ $preset_field ] ) ) {
				continue;
			}

			$provider_model_field = 'ai_' . EPKB_AI_Provider::PROVIDER_CHATGPT . '_' . $use_case . '_model';
			$shared_model_field = 'ai_' . $use_case . '_model';
			$legacy_model = ! empty( $raw_config[ $provider_model_field ] ) ? $raw_config[ $provider_model_field ] : ( ! empty( $raw_config[ $shared_model_field ] ) ? $raw_config[ $shared_model_field ] : '' );

			if ( $legacy_model !== '' ) {
				$updates[ $preset_field ] = EPKB_AI_Provider::get_preset_key_for_model( $legacy_model, EPKB_AI_Provider::PROVIDER_CHATGPT );
			}
		}

		foreach ( $use_cases as $use_case ) {
			unset( $raw_config[ 'ai_' . $use_case . '_model' ] );

			foreach ( EPKB_AI_Provider::get_supported_providers() as $provider ) {
				unset( $raw_config[ 'ai_' . $provider . '_' . $use_case . '_model' ] );
			}
		}

		// Clear legacy ai_key to mark migration as done
		$updates['ai_key'] = '';

		// Apply updates
		if ( ! empty( $updates ) ) {
			$new_config = array_merge( $raw_config, $updates );
			update_option( 'epkb_ai_configuration', $new_config, true );
		}

		// Migrate training data collections: set provider to 'chatgpt' for existing collections
		$collections = get_option( 'epkb_ai_training_data_configuration', array() );
		if ( ! empty( $collections ) && is_array( $collections ) ) {
			$needs_update = false;
			foreach ( $collections as $collection_id => &$collection_config ) {
				// For collections without provider set, use 'chatgpt' (existing users were using OpenAI)
				if ( empty( $collection_config['ai_training_data_provider'] ) ) {
					$collection_config['ai_training_data_provider'] = 'chatgpt';
					$needs_update = true;
				// Rename 'openai' to 'chatgpt'
				} else if ( $collection_config['ai_training_data_provider'] === 'openai' ) {
					$collection_config['ai_training_data_provider'] = 'chatgpt';
					$needs_update = true;
				}
			}
			unset( $collection_config );

			if ( $needs_update ) {
				update_option( 'epkb_ai_training_data_configuration', $collections, true );
			}
		}

		// Update training data table: ensure 'provider' column exists and set empty values to 'chatgpt'
		global $wpdb;
		$training_data_table = $wpdb->prefix . 'epkb_ai_training_data';
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $training_data_table ) ) === $training_data_table ) {
			// Add 'provider' column if it doesn't exist (dbDelta may be skipped if AI not configured)
			$column_exists = $wpdb->get_results( "SHOW COLUMNS FROM {$training_data_table} LIKE 'provider'" );
			if ( empty( $column_exists ) ) {
				$wpdb->query( "ALTER TABLE {$training_data_table} ADD COLUMN provider VARCHAR(20) NOT NULL DEFAULT '' AFTER collection_id" );
			}
			$wpdb->query( $wpdb->prepare( "UPDATE {$training_data_table} SET provider = %s WHERE provider = '' OR provider IS NULL", 'chatgpt' ) );
		}
	}

	private static function upgrade_to_v15_210_0( &$kb_config ) {

		/*** Update AI instructions if user hasn't changed them from old default **/

		// translators: %s is the AI refusal prompt text
		$old_instructions = sprintf(
			__( 'Avoid answering questions unrelated to your knowledge. DO NOT mention, reference, or describe documents, files, files you uploaded, or sources. Do not guess, speculate, or use outside knowledge. ONLY use the provided content. If no relevant information is found, reply exactly with: "%s"', 'echo-knowledge-base' ),
			EPKB_AI_Config_Specs::get_ai_refusal_prompt()
		);

		// translators: %s is the AI refusal prompt text
		$default_instructions = sprintf(
			__( 'You may ONLY answer using information from the vector store. Do not mention references, documents, files, or sources. Do not reveal retrieval, guess, speculate, or use outside knowledge. If no relevant information is found, reply exactly: "%s". If relevant information is found, you may give structured explanations, including comparisons, pros and cons, or decision factors, but only if they are in the data. Answer only what the data supports; when unsure, leave it out.', 'echo-knowledge-base' ),
			EPKB_AI_Config_Specs::get_ai_refusal_prompt()
		);

		// Get current AI search instructions
		$current_search_instructions = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_instructions' );

		// If the current instructions match the old default, update to new default
		if ( $current_search_instructions === $old_instructions ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_search_instructions', $default_instructions );
		}

		// Get current AI chat instructions
		$current_chat_instructions = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_instructions' );

		// If the current instructions match the old default, update to new default
		if ( $current_chat_instructions === $old_instructions ) {
			EPKB_AI_Config_Specs::update_ai_config_value( 'ai_chat_instructions', $default_instructions );
		}
	}

	private static function upgrade_to_v13_60_0( &$kb_config ) {
		if ( $kb_config['kb_main_page_layout'] == EPKB_Layout::CLASSIC_LAYOUT ) {
			$kb_config['sub_article_list_margin'] = 20;
			$kb_config['nof_articles_displayed'] = 0;
		}
	}

	private static function upgrade_to_v13_11_0( &$kb_config ) {
		$kb_config['tab_nav_overflow_mode'] = 'drop_down';
	}

	private static function upgrade_to_v12_42_0( &$kb_config ) {
		$api_key = EPKB_Utilities::get_wp_option( 'epkb_openai_api_key', '' );
		if ( empty( $api_key ) || ! is_string( $api_key ) ) {
			$api_key = '';
		}

		$api_key = EPKB_Utilities::encrypt_data( $api_key );

		$result = EPKB_Utilities::save_wp_option('epkb_openai_key', $api_key );
		if ( ! is_wp_error( $result ) ) {
			delete_option( 'epkb_openai_api_key' );
		}
	}

	private static function upgrade_to_v12_30_0( &$kb_config ) {
		$kb_config['template_for_archive_page'] = $kb_config['templates_for_kb'];
	}

	private static function upgrade_to_v12_21_0( &$kb_config ) {
		if ( EPKB_Utilities::is_advanced_search_enabled() && function_exists( 'asea_get_instance' ) && isset( asea_get_instance()->kb_config_obj ) ) {

			$asea_config = asea_get_instance()->kb_config_obj->get_kb_config( $kb_config['id'] );
			$asea_config_valid = !empty( $asea_config ) && is_array( $asea_config ) && !is_wp_error( $asea_config );

			if ( $asea_config_valid ) {
				$kb_config['search_box_hint'] = empty( $asea_config['advanced_search_mp_box_hint'] ) ? $kb_config['search_box_hint'] : $asea_config['advanced_search_mp_box_hint'];
				$kb_config['article_search_box_hint'] = empty( $asea_config['advanced_search_ap_box_hint'] ) ? $kb_config['article_search_box_hint'] : $asea_config['advanced_search_ap_box_hint'];
			}
		}
	}

	private static function upgrade_to_v12_11_0( &$kb_config ) {
		$kb_config['archive_content_articles_display_mode'] =  empty( $kb_config['archive_content_article_display_mode'] ) ? 'title' : $kb_config['archive_content_article_display_mode'];
	}

	private static function upgrade_to_v12_0_0( &$kb_config ) {
		// starting from version 12.00.0 the Archive Page is V3 by default (the toggle is 'on' in specs); ensure it is set to 'off' for all previous KB versions during the upgrade
		$kb_config['archive_page_v3_toggle'] = 'off';
	}

	private static function upgrade_to_v11_41_0( &$kb_config ) {

		if ( empty( $kb_config['ml_faqs_title_text'] ) ) {
			$kb_config['ml_faqs_title_text'] = esc_html__( 'Frequently Asked Questions', 'echo-knowledge-base' );
			$kb_config['ml_faqs_title_location'] = 'none';
		}
		if ( empty( $kb_config['ml_articles_list_title_text'] ) ) {
			$kb_config['ml_articles_list_title_text'] = esc_html__( 'Featured Articles', 'echo-knowledge-base' );
			$kb_config['ml_articles_list_title_location'] = 'none';
		}

		if ( EPKB_Utilities::is_advanced_search_enabled() && function_exists( 'asea_get_instance' ) && isset( asea_get_instance()->kb_config_obj ) ) {

			$asea_config = asea_get_instance()->kb_config_obj->get_kb_config( $kb_config['id'] );
			$asea_config_valid = !empty( $asea_config ) && is_array( $asea_config ) && !is_wp_error( $asea_config );

			if ( $asea_config_valid ) {
				$kb_config['article_search_toggle'] = ! empty( $asea_config['advanced_search_ap_box_visibility'] ) && $asea_config['advanced_search_ap_box_visibility'] == 'asea-visibility-search-form-2' ? 'off' : 'on';
			}
		}
	}

	private static function upgrade_to_v11_40_0( &$kb_config ) 	{

		// switch to single font family
		if ( ! EPKB_Utilities::is_new_user( $kb_config, '11.40.0' ) && ! empty( $kb_config['section_head_typography']['font-family'] ) ) {
			$kb_config['general_typography']['font-family'] = $kb_config['section_head_typography']['font-family'];
		}

		// remove common fields from Grid Layout; fix Sidebar typography
		if ( ( $kb_config['kb_main_page_layout'] == EPKB_Layout::GRID_LAYOUT || $kb_config['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT ) &&
				EPKB_Utilities::is_elegant_layouts_enabled() && function_exists( 'elay_get_instance' ) && isset( elay_get_instance()->kb_config_obj ) ) {

			$elay_config = elay_get_instance()->kb_config_obj->get_kb_config( $kb_config['id'] );
			$elay_config_valid = ! empty( $elay_config ) && is_array( $elay_config) && ! is_wp_error( $elay_config );

			// switch to single font family
			if ( $elay_config_valid && ! EPKB_Utilities::is_new_user( $kb_config, '11.40.0' ) ) {
				if ( $kb_config['kb_main_page_layout'] == EPKB_Layout::GRID_LAYOUT && ! empty( $elay_config['grid_section_typography']['font-family'] ) ) {
					$kb_config['general_typography']['font-family'] = $elay_config['grid_section_typography']['font-family'];
				} else if ( $kb_config['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT && ! empty( $elay_config['sidebar_section_category_typography']['font-family'] ) ) {
					$kb_config['general_typography']['font-family'] = $elay_config['sidebar_section_category_typography']['font-family'];
				}
			}

			if ( $elay_config_valid && $kb_config['kb_main_page_layout'] == EPKB_Layout::GRID_LAYOUT ) {
				$kb_config['section_head_category_icon_color'] = empty( $elay_config['grid_section_head_icon_color'] ) ? $kb_config['section_head_category_icon_color'] : $elay_config['grid_section_head_icon_color'];
				$kb_config['section_category_font_color'] = empty( $elay_config['grid_section_body_text_color'] ) ? $kb_config['section_category_font_color'] : $elay_config['grid_section_body_text_color'];
				$kb_config['section_border_radius'] = empty( $elay_config['grid_section_border_radius'] ) ? $kb_config['section_border_radius'] : $elay_config['grid_section_border_radius'];
				$kb_config['section_border_width'] = empty( $elay_config['grid_section_border_width'] ) ? $kb_config['section_border_width'] : $elay_config['grid_section_border_width'];
				$kb_config['section_border_color'] = empty( $elay_config['grid_section_border_color'] ) ? $kb_config['section_border_color'] : $elay_config['grid_section_border_color'];
				$kb_config['section_body_background_color'] = empty( $elay_config['grid_section_body_background_color'] ) ? $kb_config['section_body_background_color'] : $elay_config['grid_section_body_background_color'];
				$kb_config['section_head_background_color'] = empty( $elay_config['grid_section_head_background_color'] ) ? $kb_config['section_head_background_color'] : $elay_config['grid_section_head_background_color'];
				$kb_config['section_divider_color'] = empty( $elay_config['grid_section_divider_color'] ) ? $kb_config['section_divider_color'] : $elay_config['grid_section_divider_color'];
				$kb_config['section_head_font_color'] = empty( $elay_config['grid_section_head_font_color'] ) ? $kb_config['section_head_font_color'] : $elay_config['grid_section_head_font_color'];
				$kb_config['section_head_description_font_color'] = empty( $elay_config['grid_section_head_description_font_color'] ) ? $kb_config['section_head_description_font_color'] : $elay_config['grid_section_head_description_font_color'];
				$kb_config['category_empty_msg'] = empty( $elay_config['grid_category_empty_msg'] ) ? $kb_config['category_empty_msg'] : $elay_config['grid_category_empty_msg'];

				$kb_config['sidebar_article_list_spacing'] = self::GRID_UPGRADE_DONE;
			}
		}

		$kb_config['ml_categories_articles_sidebar_desktop_width'] = self::update_modular_sidebar_width( $kb_config );

		$kb_config['section_head_category_icon_size'] = $kb_config['section_head_category_icon_size'] > 225 ? 225 : $kb_config['section_head_category_icon_size'];
	}

	public static function update_modular_sidebar_width( $kb_config ) {

		// Find which Row the Categories Module is saved too.
		$module_name = '';
		foreach ( $kb_config as $key => $value ) {
			if ( $value === 'categories_articles' ) {
				$module_name = $key;
			}
		}

		if ( empty( $module_name ) ) {
			return 28;
		}

		// Get the Row Values based on which row the Category articles module has been assigned to.
		$row_width = '';
		$row_units = '';
		switch ( $module_name ) {
			case 'ml_row_1_module':
				$row_width = $kb_config['ml_row_1_desktop_width'];
				$row_units = $kb_config['ml_row_1_desktop_width_units'];
				break;
			case 'ml_row_2_module':
				$row_width = $kb_config['ml_row_2_desktop_width'];
				$row_units = $kb_config['ml_row_2_desktop_width_units'];
				break;
			case 'ml_row_3_module':
				$row_width = $kb_config['ml_row_3_desktop_width'];
				$row_units = $kb_config['ml_row_3_desktop_width_units'];
				break;
			case 'ml_row_4_module':
				$row_width = $kb_config['ml_row_4_desktop_width'];
				$row_units = $kb_config['ml_row_4_desktop_width_units'];
				break;
			case 'ml_row_5_module':
				$row_width = $kb_config['ml_row_5_desktop_width'];
				$row_units = $kb_config['ml_row_5_desktop_width_units'];
				break;
			default:
				break;
		}

		if ( empty( $row_units ) || ( $row_units == 'px' && empty( $row_width ) ) || ! is_numeric( $row_width ) ) {
			return 28;
		}

		// find closest standard value
		$width = $kb_config['ml_categories_articles_sidebar_desktop_width'];
		if ( $row_units == 'px' && ! empty( $row_width ) ) {
			$width = round( 100 * $kb_config['ml_categories_articles_sidebar_desktop_width'] / $row_width );
		}

		return $width < 27 ? 25 : ( $width < 29 ? 28 : 30 );
	}

	private static function upgrade_to_v11_31_0( &$kb_config ) {

		// do not upgrade if already upgraded
		if ( empty( $kb_config['article_toc_position'] ) || empty( $kb_config['article-structure-version'] ) || $kb_config['article-structure-version'] != 'version-1' ) {
			return;
		}

		// user with article v1 is switched to article v2
		if ( ! empty( $kb_config['article_toc_enable'] ) && $kb_config['article_toc_enable'] == 'on' ) {

			if ( $kb_config['article_toc_position'] == 'left' ) {
				$kb_config['article_sidebar_component_priority']['toc_left'] = 1;
				$kb_config['article-right-sidebar-toggle'] = 'on';
			} else if ( $kb_config['article_toc_position'] == 'right' ) {
				$kb_config['article_sidebar_component_priority']['toc_right'] = 1;
				$kb_config['article-right-sidebar-toggle'] = 'on';
			} else if ( $kb_config['article_toc_position'] == 'middle' ) {
				$kb_config['article_sidebar_component_priority']['toc_content'] = 1;
				$kb_config['article-right-sidebar-toggle'] = 'on';
			}
		}

		// recalculate width for version 1 article page
		$kb_config = EPKB_Core_Utilities::reset_article_sidebar_widths( $kb_config );
	}

	private static function upgrade_to_v11_30_1( &$kb_config ) {

		// handle article list spacing
		if ( EPKB_Utilities::is_elegant_layouts_enabled() && function_exists( 'elay_get_instance' ) && isset( elay_get_instance()->kb_config_obj ) ) {
			$elay_config = elay_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_config['id'] );
			if ( $kb_config['kb_main_page_layout'] == EPKB_Layout::GRID_LAYOUT && isset( $elay_config['grid_article_list_spacing'] ) ) {
				$kb_config['article_list_spacing'] = $elay_config['grid_article_list_spacing'];
			}
			if ( $kb_config['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT && isset( $elay_config['sidebar_article_list_spacing'] ) ) {
				$kb_config['article_list_spacing'] = $elay_config['sidebar_article_list_spacing'];
			}

			// ensure $kb_config['article_list_spacing'] is valid parameter for min function
			$article_list_spacing = (int)$kb_config['article_list_spacing'];
			$article_list_spacing =  min( $article_list_spacing, 50 );
			$kb_config['article_list_spacing'] = empty( $article_list_spacing ) ? 8 : $article_list_spacing;
		}

		// previously Article Page Search had the same layout as Main Page Search
		$kb_config['ml_article_search_layout'] = isset( $kb_config['ml_search_layout'] ) ? $kb_config['ml_search_layout'] : 'classic';

		// only new users have Article Page Search synced with Main Page Search by default
		$kb_config['article_search_sync_toggle'] = 'off';
	}

	private static function upgrade_to_v11_30_0( &$kb_config ) {

		$kb_config['ml_categories_articles_sidebar_location'] = isset( $kb_config['ml_categories_articles_sidebar_location'] ) ? $kb_config['ml_categories_articles_sidebar_location'] : 'right';
		if ( $kb_config['ml_categories_articles_sidebar_location'] == 'none' ) {
			$kb_config['ml_categories_articles_sidebar_toggle'] = 'off';
			$kb_config['ml_categories_articles_sidebar_location'] = 'right';
		}


		// transfer storing values of Modular config to corresponding refactored settings only if the Modular Main Page Layout is enabled, otherwise the default values will be used from specs
		if ( $kb_config['kb_main_page_layout'] == 'Modular' ) {

			// do not add Popular Articles to Featured Articles module after upgrade
			$kb_config['ml_articles_list_column_1'] = 'none';

			// refactor Modular settings for Categories & Articles module to use shared configuration
			$kb_config['section_head_category_icon_size'] = isset( $kb_config['ml_categories_articles_icon_size'] ) ? $kb_config['ml_categories_articles_icon_size'] : $kb_config['section_head_category_icon_size'];
			$kb_config['section_head_category_icon_color'] = isset( $kb_config['ml_categories_articles_icon_color'] ) ? $kb_config['ml_categories_articles_icon_color'] : $kb_config['section_head_category_icon_color'];
			if ( isset( $kb_config['ml_categories_articles_height_mode'] ) ) {
				$kb_config['section_box_height_mode'] = $kb_config['ml_categories_articles_height_mode'] == 'variable' ? 'section_no_height' : 'section_min_height';
			}
			$kb_config['section_body_height'] = isset( $kb_config['ml_categories_articles_fixed_height'] ) ? $kb_config['ml_categories_articles_fixed_height'] : $kb_config['section_body_height'];
			$kb_config['nof_articles_displayed'] = isset( $kb_config['ml_categories_articles_nof_articles_displayed'] ) ? $kb_config['ml_categories_articles_nof_articles_displayed'] : $kb_config['nof_articles_displayed'];
			$kb_config['section_head_font_color'] = isset( $kb_config['ml_categories_articles_top_category_title_color'] ) ? $kb_config['ml_categories_articles_top_category_title_color'] : $kb_config['section_head_font_color'];
			if ( isset( $kb_config['ml_categories_articles_sub_category_color'] ) ) {
				$kb_config['section_category_font_color'] = $kb_config['ml_categories_articles_sub_category_color'];
				$kb_config['section_category_icon_color'] = $kb_config['ml_categories_articles_sub_category_color'];
			}
			if ( isset( $kb_config['ml_categories_articles_article_color'] ) ) {
				$kb_config['article_font_color'] = $kb_config['ml_categories_articles_article_color'];
				$kb_config['article_icon_color'] = $kb_config['ml_categories_articles_article_color'];
			}
			$kb_config['section_head_description_font_color'] = isset( $kb_config['ml_categories_articles_cat_desc_color'] ) ? $kb_config['ml_categories_articles_cat_desc_color'] : $kb_config['section_head_description_font_color'];
			if ( isset( $kb_config['ml_categories_columns'] ) ) {
				switch ( $kb_config['ml_categories_columns'] ) {
					case '2-col': $kb_config['nof_columns'] = 'two-col'; break;
					case '3-col': $kb_config['nof_columns'] = 'three-col'; break;
					case '4-col': $kb_config['nof_columns'] = 'four-col'; break;
					default: break;
				}
			}

			// refactor Modular to Classic and Drill-Down
			if ( isset( $kb_config['ml_categories_articles_layout'] ) && $kb_config['ml_categories_articles_layout'] == 'classic' ) {
				$kb_config['kb_main_page_layout'] = EPKB_Layout::CLASSIC_LAYOUT;

				// fit previous styles in .css file
				$kb_config['section_border_color'] = '#ffffff';

			} else {
				$kb_config['kb_main_page_layout'] = EPKB_Layout::DRILL_DOWN_LAYOUT;

				// fit previous styles in .css file
				if( isset( $kb_config['ml_categories_articles_border_color'] ) ) {
					$kb_config['section_border_color'] = $kb_config['ml_categories_articles_border_color'];
				}
			}

			$kb_config['section_desc_text_on'] = 'on';

			// ensure icons are at the same place after refactoring from Modular to Classic or Drill-Down layout
			$kb_config['section_head_category_icon_location'] = 'top';

			// fit previous styles in .css file
			$kb_config['section_border_width'] = '1';
			$kb_config['section_border_radius'] = '15';
			$kb_config['background_color'] = '';
		}

		// rename settings
		$kb_config['ml_categories_articles_category_title_html_tag'] = isset( $kb_config['ml_categories_articles_title_html_tag'] ) ? $kb_config['ml_categories_articles_title_html_tag'] :
			( isset( $kb_config['ml_categories_articles_category_title_html_tag'] ) ? $kb_config['ml_categories_articles_category_title_html_tag'] : 'h2' );
		$kb_config['ml_categories_articles_top_category_icon_bg_color_toggle'] = isset( $kb_config['ml_categories_articles_icon_background_color_toggle'] ) ? $kb_config['ml_categories_articles_icon_background_color_toggle'] :
			( isset( $kb_config['ml_categories_articles_top_category_icon_bg_color_toggle'] ) ? $kb_config['ml_categories_articles_top_category_icon_bg_color_toggle'] : 'on' );
		$kb_config['ml_categories_articles_top_category_icon_bg_color'] = isset( $kb_config['ml_categories_articles_icon_background_color'] ) ? $kb_config['ml_categories_articles_icon_background_color'] :
			( isset( $kb_config['ml_categories_articles_top_category_icon_bg_color'] ) ? $kb_config['ml_categories_articles_top_category_icon_bg_color'] : '#e9f6ff' );

		// Copy search width to row settings
		$row_number = 5;
		while ( $row_number > 0 ) {
			if ( ! empty( $kb_config['ml_row_' . $row_number . '_module'] ) && $kb_config['ml_row_' . $row_number . '_module'] == 'search' ) {

				if ( $kb_config['width'] == 'epkb-boxed' ) {
					$kb_config['ml_row_' . $row_number . '_desktop_width'] = '1080';
					$kb_config['ml_row_' . $row_number . '_desktop_width_units'] = 'px';
				} else {
					$kb_config['ml_row_' . $row_number . '_desktop_width'] = '100';
					$kb_config['ml_row_' . $row_number . '_desktop_width_units'] = '%';
				}
			}

			$row_number--;
		}

		$plugin_first_version = EPKB_Utilities::get_wp_option( 'epkb_version_first', '' );
		if ( ! empty( $plugin_first_version ) ) {
			$kb_config['first_plugin_version'] = $plugin_first_version;
		}
	}

	/**
	 * Function for major updates
	 *
	 * @param $args
	 */
	public function in_plugin_update_message( $args ) {

		$current_version = Echo_Knowledge_Base::$version;
		$new_version = empty( $args['new_version'] ) ? $current_version : $args['new_version'];

		// versions x.y11.z are major releases
		if ( ! preg_match( '/^\d+\.\d{1,}11\.\d+$/', $new_version ) ) {
			return;
		}

		echo '<style> .epkb-update-warning+p { opacity: 0; height: 0;} </style> ';
		echo '<hr style="clear:left"><div class="epkb-update-warning"><span class="dashicons dashicons-info" style="float:left;margin-right: 6px;color: #d63638;"></span>';
		echo '<div class="epkb-update-warning__title">' . esc_html__( 'We highly recommend you back up your site before upgrading. Next, run the update in a staging environment.', 'echo-knowledge-base' ) . '</div>';
		echo '<div class="epkb-update-warning__message">' .	esc_html__( 'After you run the update, clear your browser cache, hosting cache, and caching plugins.', 'echo-knowledge-base' ) . '</div>';
		echo '<div class="epkb-update-warning__message">' .	esc_html__( 'The latest update includes some substantial changes across different areas of the plugin', 'echo-knowledge-base' ) . '</div>';
	}

	/**
	 * Avoid duplicate content on Article Page.
	 * @return void
	 */
	function after_switch_theme() {
		EPKB_Core_Utilities::remove_kb_flag( 'epkb_the_content_fix' );
	}
}
