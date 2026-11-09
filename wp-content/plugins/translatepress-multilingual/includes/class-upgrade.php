<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Upgrade
 *
 * When changing plugin version, do the necessary checks and database upgrades.
 */
class TRP_Upgrade {

	protected $settings;
	protected $db;
	/* @var TRP_Query */
	protected $trp_query;

    /** Major slug translation refactoring released in this version */
    const MINIMUM_SP_VERSION = '1.4.6';

    /** Used to check if the currently installed Pro version matches the minimum required version */
    const MINIMUM_PERSONAL_VERSION = '1.4.3';
    const MINIMUM_DEVELOPER_VERSION = '1.5.6';

	/**
	 * TRP_Upgrade constructor.
	 *
	 * @param $settings
	 */
	public function __construct( $settings ){
        global $wpdb;
        $this->db = $wpdb;
		$this->settings = $settings;

	}

	/**
	 * Register Settings subpage for TranslatePress
	 */
	public function register_menu_page(){
		add_submenu_page( 'TRPHidden', 'TranslatePress Remove Duplicate Rows', 'TRPHidden', apply_filters( 'trp_settings_capability', 'manage_options' ), 'trp_remove_duplicate_rows', array($this, 'trp_remove_duplicate_rows') );
		add_submenu_page( 'TRPHidden', 'TranslatePress Update Database', 'TRPHidden', apply_filters( 'trp_settings_capability', 'manage_options' ), 'trp_update_database', array( $this, 'trp_update_database_page' ) );
	}

	/**
	 * When changing plugin version, call certain database upgrade functions.
	 *
	 */
	public function check_for_necessary_updates(){
		$trp = TRP_Translate_Press::get_trp_instance();
		if( ! $this->trp_query ) {
			$this->trp_query = $trp->get_component( 'query' );
		}
		$stored_database_version = get_option('trp_plugin_version');
		if( empty($stored_database_version) ){
			$this->check_if_gettext_tables_exist();
        }else{

            // Updates that require admins to trigger manual update of db because of long duration. Set an option in DB if this is the case.
            $updates = $this->get_updates_details();
            foreach ( $updates as $update ) {
                if ( version_compare( $update['version'], $stored_database_version, '>' ) ) {
                    update_option( $update['option_name'], 'no' );
                }
            }

            if ( get_option( 'trp_updated_database_gettext_original_lookup_hash', 'is not set' ) === 'is not set' ) {
                $originals_table = $this->trp_query->get_table_name_for_gettext_original_strings();
                $lookup_hash_is_ready = (
                    ! $this->trp_query->table_exists( $originals_table ) ||
                    (
                        $this->trp_query->table_column_exists( $originals_table, 'lookup_hash' ) &&
                        $this->trp_query->table_index_exists( $originals_table, 'gettext_lookup_hash_unique' )
                    )
                );

                update_option( 'trp_updated_database_gettext_original_lookup_hash', $lookup_hash_is_ready ? 'yes' : 'no' );
            }

            if ( get_option( 'trp_updated_database_gettext_tables_optimization', 'is not set' ) === 'is not set' ) {
                update_option( 'trp_updated_database_gettext_tables_optimization', $this->gettext_tables_optimization_is_ready() ? 'yes' : 'no' );
            }

            // Updates that can be done right way. They should take very little time.
            if ( version_compare( $stored_database_version, '1.3.0', '<=' ) ) {
                $this->trp_query->check_for_block_type_column();
                $this->check_if_gettext_tables_exist();
            }
            if ( version_compare($stored_database_version, '1.5.3', '<=')) {
                $this->add_full_text_index_to_tables();
            }
            if ( version_compare($stored_database_version, '1.6.1', '<=')) {
                $this->upgrade_machine_translation_settings();
            }
            if ( version_compare( $stored_database_version, '1.6.5', '<=' ) ) {
                $this->trp_query->check_for_original_id_column();
                $this->trp_query->check_original_table();
                $this->trp_query->check_original_meta_table();
            }
            if ( version_compare($stored_database_version, '1.9.8', '<=')) {
                $this->set_force_slash_at_end_of_links();
            }

			if ( version_compare( $stored_database_version, '2.3.7', '<=' ) ) {
                $gettext_normalization = $this->trp_query->get_query_component('gettext_normalization');
                $gettext_normalization->check_for_gettext_original_id_column();

                $gettext_table_creation = $this->trp_query->get_query_component('gettext_table_creation');
                $gettext_table_creation->check_gettext_original_table();
                $gettext_table_creation->check_gettext_original_meta_table();
			}
            if ( version_compare($stored_database_version, '2.1.0', '<=')){
                $this->add_iso_code_to_language_code();
            }
            if ( version_compare($stored_database_version, '2.1.2', '<=')){
                $this->create_opposite_ls_option();
            }
            if( version_compare( $stored_database_version, '2.2.2', '<=' ) ){
                $this->migrate_auto_translate_slug_to_automatic_translation();
            }
            if ( version_compare( $stored_database_version, '2.7.2', '<=' ) ) {
                $this->migrate_machine_translation_counter();
            }
            if ( version_compare( $stored_database_version, '2.7.4', '<=' ) ) {
                $this->add_tp_block_index();
            }
            if ( version_compare( $stored_database_version, '2.8.4', '<=' ) ) {
                $this->dont_update_db_if_seopack_inactive();
                $this->set_the_options_set_in_db_optimization_tool_to_no();
            }
            if ( version_compare( $stored_database_version, '2.9.7', '==' ) ) {
                $this->set_publish_languages_from_translation_languages();
            }

            if ( version_compare( $stored_database_version, '2.10', '<') ) {
                $this->migrate_to_language_switcher_v2();
            }

            if ( version_compare( $stored_database_version, '3.1.5', '<=' ) ) {
                add_option( 'trp_failed_translations_cleanup_315', 'no' );
                $trp = TRP_Translate_Press::get_trp_instance();
                $trp->get_component( 'batch_processor' )->schedule();
            }

            if ( version_compare( $stored_database_version, '3.2.0', '<=' ) ) {
                $this->cleanup_language_switcher_menu_item_classes();
            }

            /**
             * Write an upgrading function above this comment to be executed only once: while updating plugin to a higher version.
             * Use example condition: version_compare( $stored_database_version, '2.9.9', '<=')
             * where 2.9.9 is the current version, and 3.0.0 will be the updated version where this code will be launched.
             */
        }

        // don't update the db version unless they are different. Otherwise the query is run on every page load.
        if( version_compare( TRP_PLUGIN_VERSION, $stored_database_version, '!=' ) ){
            update_option( 'trp_plugin_version', TRP_PLUGIN_VERSION );
		}
	}

    public function migrate_auto_translate_slug_to_automatic_translation(){
        $option = get_option( 'trp_advanced_settings', true );
        $mt_settings_option = get_option( 'trp_machine_translation_settings' );
        if( !isset( $mt_settings_option['automatically-translate-slug'] ) ){
            if( !isset( $option['enable_auto_translate_slug'] ) || $option['enable_auto_translate_slug'] == '' || $option['enable_auto_translate_slug'] == 'no' ){
                $mt_settings_option['automatically-translate-slug'] = 'no';
            }
            else{
                $mt_settings_option['automatically-translate-slug'] = 'yes';
            }
            update_option( 'trp_machine_translation_settings', $mt_settings_option );
        }
    }

    private function cleanup_language_switcher_menu_item_classes() {
        $menu_items = get_posts( array(
            'post_type'      => 'nav_menu_item',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_key'       => '_menu_item_object',
            'meta_value'     => 'language_switcher',
        ) );

        foreach ( $menu_items as $menu_item_id ) {
            $classes = get_post_meta( $menu_item_id, '_menu_item_classes', true );

            if ( ! is_array( $classes ) || ! in_array( 'current-language-menu-item', $classes, true ) ) {
                continue;
            }

            update_post_meta(
                $menu_item_id,
                '_menu_item_classes',
                array_values( array_filter( $classes, function( $class ) {
                    return $class !== 'current-language-menu-item';
                } ) )
            );
        }
    }

	/**
	 * Iterates over all languages to call gettext table checking
	 */
	public function check_if_gettext_tables_exist(){
		$trp = TRP_Translate_Press::get_trp_instance();
		if( ! $this->trp_query ) {
			$this->trp_query = $trp->get_component( 'query' );
		}
        $gettext_table_creation = $this->trp_query->get_query_component('gettext_table_creation');
		if( !empty( $this->settings['translation-languages'] ) ){
			foreach( $this->settings['translation-languages'] as $site_language_code ){
				$gettext_table_creation->check_gettext_table($site_language_code);
			}
		}
		$gettext_table_creation->check_gettext_original_table();
		$gettext_table_creation->check_gettext_original_meta_table();
	}

    /**
     * @param string $key of updates details array
     * @return string
     * @see get_updates_details()
     */
    public function get_updates_processing_message( $key ){
        $messages = [
            'remove_cdata_original_and_dictionary_rows'     => __('Removing cdata dictionary strings for language %s...', 'translatepress-multilingual' ),
            'remove_untranslated_links_dictionary_rows'     => __('Removing untranslated dictionary links for language %s...', 'translatepress-multilingual' ),
            'remove_duplicate_gettext_rows'                 => __('Removing duplicated gettext strings for language %s...', 'translatepress-multilingual' ),
            'remove_duplicate_dictionary_rows'              => __('Removing duplicated dictionary strings for language %s...', 'translatepress-multilingual' ),
            'remove_duplicate_untranslated_dictionary_rows' => __('Removing untranslated dictionary strings where translation is available for language %s...', 'translatepress-multilingual' ),
            'original_id_insert_166'                        => __('Inserting original strings for language %s...', 'translatepress-multilingual' ),
            'original_id_cleanup_166'                       => __('Cleaning original strings table for language %s...', 'translatepress-multilingual' ),
            'original_id_update_166'                        => __('Updating original string ids for language %s...', 'translatepress-multilingual' ),
            'regenerate_original_meta'                      => __('Regenerating original meta table for language %s...', 'translatepress-multilingual' ),
            'clean_original_meta'                           => __('Cleaning original meta table for language %s...', 'translatepress-multilingual' ),
            'replace_original_id_null'                      => __('Replacing original id NULL with value for language %s...', 'translatepress-multilingual' ),
            'gettext_original_id_insert'                    => __('Inserting gettext original strings for language %s...', 'translatepress-multilingual' ),
            'gettext_original_id_cleanup'                   => __('Cleaning gettext original strings table for language %s...', 'translatepress-multilingual' ),
            'gettext_original_id_update'                    => __('Updating gettext original string ids for language %s...', 'translatepress-multilingual' ),
            'gettext_tables_optimization'                   => __('Starting gettext database optimization in the background...', 'translatepress-multilingual' ),
            'migrate_old_slugs_to_the_new_translate_table_structure_post_type_and_tax_284' => __( 'Migrating taxonomy and post type base slugs to new table structure...', 'translatepress-multilingual' ),
            'migrate_old_slugs_to_the_new_translate_table_structure_post_meta_284'         => __( 'Migrating post slugs to new table structure for language %s...', 'translatepress-multilingual' ),
            'migrate_old_slugs_to_the_new_translate_table_structure_term_meta_284'         => __( 'Migrating term slugs to new table structure for language %s...', 'translatepress-multilingual' ),
            'show_error_db_message'                         => __( 'Finishing up...', 'translatepress-multilingual' )
        ];

        return in_array( $key, array_keys( $messages ) ) ? $messages[$key] : null;
    }

    public function get_updates_details(){
        return apply_filters( 'trp_updates_details',
            array(
                'remove_cdata_original_and_dictionary_rows' => array(
                    'version'           => '0',
                    'option_name'       => 'trp_remove_cdata_original_and_dictionary_rows',
                    'callback'          => array( $this->trp_query,'remove_cdata_in_original_and_dictionary_tables'),
                    'batch_size'        => 1000,
                    'message_initial'   => '',
                ),
                'remove_untranslated_links_dictionary_rows' => array(
                    'version'           => '0',
                    'option_name'       => 'trp_remove_untranslated_links_dictionary_rows',
                    'callback'          => array( $this->trp_query,'remove_untranslated_links_in_dictionary_table'),
                    'batch_size'        => 10000,
                    'message_initial'   => '',
                ),
				'full_trim_originals_140' => array(
					'version'           => '1.4.0',
					'option_name'       => 'trp_updated_database_full_trim_originals_140',
					'callback'          => array( $this, 'trp_updated_database_full_trim_originals_140' ),
					'batch_size'        => 200
				),
				'gettext_empty_rows_145' => array(
					'version'           => '1.4.5',
					'option_name'       => 'trp_updated_database_gettext_empty_rows_145',
					'callback'          => array( $this,'trp_updated_database_gettext_empty_rows_145'),
					'batch_size'        => 20000
				),
                'remove_duplicate_gettext_rows' => array(
                    'version'           => '0',
                    'option_name'       => 'trp_remove_duplicate_gettext_rows',
                    'callback'          => array( $this->trp_query,'remove_duplicate_rows_in_gettext_table'),
                    'batch_size'        => 5000,
                    'message_initial'   => '',
                ),
                'remove_duplicate_dictionary_rows' => array(
                    'version'           => '0',
                    'option_name'       => 'trp_remove_duplicate_dictionary_rows',
                    'callback'          => array( $this->trp_query,'remove_duplicate_rows_in_dictionary_table'),
                    'batch_size'        => 1000,
                    'message_initial'   => '',
                ),
                'original_id_insert_166' => array(
                    'version'           => '1.6.6',
                    'option_name'       => 'trp_updated_database_original_id_insert_166',
                    'callback'          => array( $this,'trp_updated_database_original_id_insert_166'),
                    'batch_size'        => 1000,
                ),
                'original_id_cleanup_166' => array(
                    'version'           => '1.6.6',
                    'option_name'       => 'trp_updated_database_original_id_cleanup_166',
                    'callback'          => array( $this,'trp_updated_database_original_id_cleanup_166'),
                    'progress_message'  => 'clean',
                    'batch_size'        => 1000,
                    'message_initial'   => '',
                ),
                'original_id_update_166' => array(
                    'version'           => '1.6.6',
                    'option_name'       => 'trp_updated_database_original_id_update_166',
                    'callback'          => array( $this,'trp_updated_database_original_id_update_166'),
                    'batch_size'        => 5000,
                    'message_initial'   => '',
                ),
                'regenerate_original_meta' => array(
                    'version'           => '0', // independent of tp version, available only on demand
                    'option_name'       => 'trp_regenerate_original_meta_table',
                    'callback'          => array( $this,'trp_regenerate_original_meta_table'),
                    'batch_size'        => 200,
                    'message_initial'   => '',
                ),
                'clean_original_meta' => array(
                    'version'           => '0', // independent of tp version, available only on demand
                    'option_name'       => 'trp_clean_original_meta_table',
                    'callback'          => array( $this,'trp_clean_original_meta_table'),
                    'batch_size'        => 20000,
                    'message_initial'   => '',
                ),
                'replace_original_id_null' => array(
                    'version'           => '0', // independent of tp version, available only on demand
                    'option_name'       => 'trp_replace_original_id_null',
                    'callback'          => array( $this,'trp_replace_original_id_null'),
                    'batch_size'        => 50,
                    'message_initial'   => '',
                ),
                'gettext_original_id_insert' => array(
                    'version'           => '2.3.8',
                    'option_name'       => 'trp_updated_database_gettext_original_id_insert',
                    'callback'          => array( $this,'trp_updated_database_gettext_original_id_insert'),
                    'batch_size'        => 1000,
                ),
                'gettext_original_id_cleanup' => array(
                    'version'           => '2.3.8',
                    'option_name'       => 'trp_updated_database_gettext_original_id_cleanup',
                    'callback'          => array( $this,'trp_updated_database_gettext_original_id_cleanup'),
                    'progress_message'  => 'clean',
                    'batch_size'        => 1000,
                    'message_initial'   => '',
                ),
                'gettext_original_id_update' => array(
                    'version'           => '2.3.8',
                    'option_name'       => 'trp_updated_database_gettext_original_id_update',
                    'callback'          => array( $this,'trp_updated_database_gettext_original_id_update'),
                    'batch_size'        => 5000,
                    'message_initial'   => '',
                ),
                'gettext_tables_optimization' => array(
                    'version'           => '3.3',
                    'option_name'       => 'trp_updated_database_gettext_tables_optimization',
                    'callback'          => array( $this,'trp_start_gettext_tables_optimization'),
                    'batch_size'        => 1,
                    'message_initial'   => '',
                    'execute_only_once' => true,
                    'background_only'   => true,
                ),
                'migrate_old_slugs_to_the_new_translate_table_structure_post_type_and_tax_284' => array(
                    'version'            => '0',
                    'option_name'        => 'trp_migrate_old_slug_to_new_parent_and_translate_slug_table_post_type_and_tax_284',
                    'callback'           => array( $this, 'trp_migrate_old_slug_to_new_parent_and_translate_slug_table_post_type_and_tax_284' ),
                    'batch_size'         => 1000000,
                    'message_initial'    => '',
                    'execute_only_once'  => true
                ),
                'migrate_old_slugs_to_the_new_translate_table_structure_post_meta_284'         => array(
                    'version'            => '0',
                    'option_name'        => 'trp_migrate_old_slug_to_new_parent_and_translate_slug_table_post_meta_284',
                    'callback'           => array( $this, 'trp_migrate_old_slug_to_new_parent_and_translate_slug_table_post_meta_284' ),
                    'batch_size'         => 500,
                    'message_initial'    => '',
                ),
                'migrate_old_slugs_to_the_new_translate_table_structure_term_meta_284'         => array(
                    'version'            => '0',
                    'option_name'        => 'trp_migrate_old_slug_to_new_parent_and_translate_slug_table_term_meta_284',
                    'callback'           => array( $this, 'trp_migrate_old_slug_to_new_parent_and_translate_slug_table_term_meta_284' ),
                    'batch_size'         => 500,
                    'message_initial'    => '',
                ),

                /** Add new entries above this line
                 * Write 3.0.0 if 2.9.9 is the current version, and 3.0.0 will be the updated version where this code will be launched.
                 */
                'show_error_db_message' => array(
                    'version'            => '0', // independent of tp version, available only on demand
                    'option_name'        => 'trp_show_error_db_message',
                    'callback'           => array( $this, 'trp_successfully_run_database_optimization' ),
                    'batch_size'         => 10,
                    'message_initial'    => '',
                    'execute_only_once'  => true
                )
			)
		);
	}

	/**
	 * Show admin notice about updating database
	 */
	public function show_admin_notice(){
        $notifications = TRP_Plugin_Notifications::get_instance();
        if ( $notifications->is_plugin_page() || ( isset( $GLOBALS['PHP_SELF']) && ( $GLOBALS['PHP_SELF'] === '/wp-admin/index.php' || $GLOBALS['PHP_SELF'] === '/wp-admin/plugins.php' ) ) ) {
            if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'trp_update_database' ) ) {
                return;
            }
            $updates_needed          = $this->get_updates_details();
            $option_db_error_message = get_option( $updates_needed['show_error_db_message']['option_name'] );
                foreach ( $updates_needed as $update_key => $update ) {
                $option = get_option( $update['option_name'], 'is not set' );
                if ( $option === 'no' && $option_db_error_message !== 'no' ) {
                        if ( $update_key === 'gettext_tables_optimization' && $this->gettext_tables_optimization_batch_has_started() ) {
                            continue;
                        }
                    add_action( 'admin_notices', array( $this, 'admin_notice_update_database' ) );
                    break;
                }
            }
        }
	}

	/**
	 * Print admin notice message
	 */
	public function admin_notice_update_database() {
        $database_update_confirmation_message = sprintf(
            "%s\n%s",
            __( 'IMPORTANT: It is strongly recommended to first backup the database!', 'translatepress-multilingual' ),
            __( 'Are you sure you want to continue?', 'translatepress-multilingual' )
        );

        if ( $this->only_gettext_tables_optimization_is_pending() ) {
            $url = wp_nonce_url(
                add_query_arg( array( 'trp_start_gettext_tables_optimization' => '1' ) ),
                'trp_start_gettext_tables_optimization'
            );

            $html = '<div id="message" class="notice notice-warning">';
            $html .= '<p><strong>' . esc_html__( 'TranslatePress data update', 'translatepress-multilingual' ) . '</strong> &#8211; ' . esc_html__( 'TranslatePress needs to optimize gettext database tables. Translations continue to work while this runs in the background.', 'translatepress-multilingual' ) . '</p>';
            $html .= '<p>' . esc_html__( 'Before starting, we strongly recommend creating a database backup.', 'translatepress-multilingual' ) . '</p>';
            $html .= '<p class="submit"><a href="' . esc_url( $url ) . '" onclick="return confirm( ' . esc_attr( wp_json_encode( $database_update_confirmation_message ) ) . ' );" class="button-primary">' . esc_html__( 'Start optimization', 'translatepress-multilingual' ) . '</a></p>';
            $html .= '</div>';
            echo $html;//phpcs:ignore
            return;
        }

		$url = add_query_arg( array(
			'page'                      => 'trp_update_database',
		), site_url('wp-admin/admin.php') );

		// maybe change notice color to blue #28B1FF
		$html = '<div id="message" class="updated">';
		$html .= '<p><strong>' . esc_html__( 'TranslatePress data update', 'translatepress-multilingual' ) . '</strong> &#8211; ' . esc_html__( 'We need to update your translations database to the latest version.', 'translatepress-multilingual' ) . '</p>';
        $html .= '<p class="submit"><a href="' . esc_url( $url ) . '" onclick="return confirm( ' . esc_attr( wp_json_encode( $database_update_confirmation_message ) ) . ' );" class="button-primary">' . esc_html__( 'Run the updater', 'translatepress-multilingual' ) . '</a></p>';
		$html .= '</div>';
		echo $html;//phpcs:ignore
	}

    /**
     * Start gettext table optimization directly from the admin notice.
     */
    public function maybe_start_gettext_tables_optimization_from_notice() {
        if ( empty( $_GET['trp_start_gettext_tables_optimization'] ) ) {
            return;
        }

        if ( ! current_user_can( apply_filters( 'trp_update_database_capability', 'manage_options' ) ) ) {
            return;
        }

        $nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'trp_start_gettext_tables_optimization' ) ) {
            return;
        }

        $this->trp_start_gettext_tables_optimization();

        wp_safe_redirect( remove_query_arg( array( 'trp_start_gettext_tables_optimization', '_wpnonce' ) ) );
        exit;
    }

    /**
     * Return whether gettext table optimization is the only pending DB updater item.
     *
     * @return bool
     */
    protected function only_gettext_tables_optimization_is_pending() {
        $updates_needed = $this->get_updates_details();
        $pending_keys   = array();

        foreach ( $updates_needed as $key => $update ) {
            if ( get_option( $update['option_name'], 'is not set' ) === 'no' ) {
                $pending_keys[] = $key;
            }
        }

        return count( $pending_keys ) === 1 && $pending_keys[0] === 'gettext_tables_optimization';
    }

    /**
     * Return whether the background gettext optimizer task has already started.
     *
     * @return bool
     */
    protected function gettext_tables_optimization_batch_has_started() {
        return in_array( get_option( 'trp_gettext_tables_optimization_330', 'is not set' ), array( 'no', 'failed' ), true );
    }

    public function trp_successfully_run_database_optimization($language_code= null, $inferior_size = null, $batch_size = null){
        delete_option('trp_show_error_db_message');

        return true;
    }

        /**
         * Schedule gettext database optimization in the background.
         *
         * @return array|bool
         */
    public function trp_start_gettext_tables_optimization() {
        $trp = TRP_Translate_Press::get_trp_instance();
        $batch_processor = $trp->get_component( 'batch_processor' );

        if ( $batch_processor && $batch_processor->start_task( 'trp_gettext_tables_optimization_330', true ) ) {
            return true;
        }

        $batch_error = get_option( 'trp_batch_error_trp_gettext_tables_optimization_330', array() );

        return array(
            'error' => ! empty( $batch_error['error'] ) ? $batch_error['error'] : __( 'Update aborted! Could not schedule gettext database optimization.', 'translatepress-multilingual' ),
        );
    }

        /**
         * Check if gettext tables already have the final optimized schema.
         *
         * @return bool
         */
    protected function gettext_tables_optimization_is_ready() {
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $originals_table = $this->trp_query->get_table_name_for_gettext_original_strings();

        if ( ! $this->trp_query->table_exists( $originals_table ) ) {
            return true;
        }

        if (
            ! $this->trp_query->table_column_exists( $originals_table, 'lookup_hash' ) ||
            ! $this->trp_query->table_index_exists( $originals_table, 'gettext_lookup_hash_unique' ) ||
            ! $this->trp_query->table_index_exists( $originals_table, 'gettext_lookup_original_domain_context' )
        ) {
            return false;
        }

        foreach ( $this->trp_query->get_all_gettext_table_names() as $table_name ) {
            $table_name = sanitize_text_field( $table_name );

            if ( ! $this->trp_query->table_exists( $table_name ) ) {
                continue;
            }

            if ( ! $this->trp_query->table_column_exists( $table_name, 'original_id' ) || ! $this->trp_query->table_column_exists( $table_name, 'plural_form' ) ) {
                return false;
            }

            if ( ! $this->trp_query->table_index_exists( $table_name, 'gettext_original_plural_unique' ) ) {
                return false;
            }
        }

        return true;
    }


    public function show_admin_error_message(){
        if ( ( isset( $_GET[ 'page'] ) && $_GET['page'] == 'trp_update_database' ) ){
            return;
        }
        $updates_needed = $this->get_updates_details();
        $option_db_error_message = get_option($updates_needed['show_error_db_message']['option_name'], 'is not set' );
        if ( $option_db_error_message === 'no' ) {
            add_action( 'admin_notices', array( $this, 'trp_admin_notice_error_database' ) );
        }

    }

    public function trp_admin_notice_error_database(){

        echo '<div class="notice notice-error is-dismissible">
            <p>' . wp_kses( sprintf( __('Database optimization did not complete successfully. We recommend restoring the original database or <a href="%s" >trying again.</a>', 'translatepress-multilingual'), admin_url('admin.php?page=trp_update_database') ), array('a' => array( 'href' => array() ) ) ) .'</p>
        </div>';

    }

	public function trp_update_database_page(){
		require_once TRP_PLUGIN_DIR . 'partials/trp-update-database.php';
	}

	/**
	 * Call all functions to update database
	 *
	 * hooked to wp_ajax_trp_update_database
	 */
	public function trp_update_database(){
		if ( ! current_user_can( apply_filters('trp_update_database_capability', 'manage_options') ) ){
			$this->stop_and_print_error( __('Update aborted! Your user account doesn\'t have the capability to perform database updates.', 'translatepress-multilingual' ) );
		}

		$nonce = isset( $_REQUEST['trp_updb_nonce'] ) ? wp_verify_nonce( sanitize_text_field( $_REQUEST['trp_updb_nonce'] ), 'tpupdatedatabase' ) : false;
		if ( $nonce === false ){
			$this->stop_and_print_error( __('Update aborted! Invalid nonce.', 'translatepress-multilingual' ) );
		}

		$request = array();
		$request['progress_message'] = '';
		$updates_needed = $this->get_updates_details();
        if (isset($_REQUEST['initiate_update']) && $_REQUEST['initiate_update']=== "true" ){
            update_option('trp_show_error_db_message', 'no');
        }
		if ( empty ( $_REQUEST['trp_updb_action'] ) ){
			foreach( $updates_needed as $update_action_key => $update ) {
				if ( ! empty( $update['background_only'] ) ) {
					continue;
				}

				$option = get_option( $update['option_name'], 'is not set' );
				if ( $option === 'no' ) {
					$_REQUEST['trp_updb_action'] = $update_action_key;
					break;
				}
			}
			if ( empty ( $_REQUEST['trp_updb_action'] ) ){
				$back_to_settings_button = '<a class="trp-submit-btn button" href="' . site_url('wp-admin/options-general.php?page=translate-press') . '">' . esc_html__('Back to TranslatePress Settings', 'translatepress-multilingual' ) . '</a>';
				// finished successfully
				echo json_encode( array(
					'trp_update_completed' => 'yes',
					'progress_message'  => '<p><strong>' . __('Successfully updated database!', 'translatepress-multilingual' ) . '</strong></p>' . $back_to_settings_button
				));
				wp_die();
			}else{
				$_REQUEST['trp_updb_lang'] = $this->settings['translation-languages'][0];
				$_REQUEST['trp_updb_batch'] = 0;
                $updb_action = sanitize_text_field( $_REQUEST['trp_updb_action'] );

                $update_message_initial = isset( $updates_needed[ $updb_action ]['message_initial'] ) ?
                                            $updates_needed[ $updb_action ]['message_initial']
                                            : __('Updating database to version %s+', 'translatepress-multilingual' );

                $update_message_processing = $this->get_updates_processing_message( $updb_action ) ?
                                             $this->get_updates_processing_message( $updb_action ) :
                                             __('Processing table for language %s...', 'translatepress-multilingual' );

                if ($updates_needed[ $updb_action ]['version'] != 0) {
                    $request['progress_message'] .= '<p>' . sprintf( $update_message_initial, $updates_needed[ $updb_action ]['version'] ) . '</p>';
                }
                $request['progress_message'] .= '<br>' . sprintf( $update_message_processing, sanitize_text_field( $_REQUEST['trp_updb_lang'] ) );//phpcs:ignore
			}
		}else{
			if ( !isset( $updates_needed[ $_REQUEST['trp_updb_action'] ] ) ){
				$this->stop_and_print_error( __('Update aborted! Incorrect action.', 'translatepress-multilingual' ) );
			}
			if ( ! empty( $updates_needed[ $_REQUEST['trp_updb_action'] ]['background_only'] ) ) {
				$this->stop_and_print_error( __( 'Update aborted! Start the gettext database optimization separately from the Advanced settings page.', 'translatepress-multilingual' ) );
			}
			if ( !in_array( $_REQUEST['trp_updb_lang'], $this->settings['translation-languages'] ) ) {//phpcs:ignore
				$this->stop_and_print_error( __('Update aborted! Incorrect language code.', 'translatepress-multilingual' ) );
			}
		}

		$request['trp_updb_action'] = sanitize_text_field( $_REQUEST['trp_updb_action'] );
		if ( !empty( $_REQUEST['trp_updb_batch'] ) && (int) $_REQUEST['trp_updb_batch'] > 0 ) {
			$get_batch = (int)$_REQUEST['trp_updb_batch'];
		}else{
			$get_batch = 0;
		}

        $extra_params = isset( $_REQUEST['trp_updb_extra_params'] ) ? json_decode(base64_decode(sanitize_text_field($_REQUEST['trp_updb_extra_params'] )), true) : array();
        if (!is_array($extra_params)) {
            $extra_params = array();
        }

		$request['trp_updb_batch'] = 0;
		$update_details = $updates_needed[ sanitize_text_field( $_REQUEST['trp_updb_action'] )];
		$batch_size = apply_filters( 'trp_updb_batch_size', $update_details['batch_size'], sanitize_text_field( $_REQUEST['trp_updb_action'] ), $update_details );
		$language_code = isset( $_REQUEST['trp_updb_lang'] ) ? sanitize_text_field( $_REQUEST['trp_updb_lang'] ) : '';

		if ( ! $this->trp_query ) {
			$trp = TRP_Translate_Press::get_trp_instance();
			/* @var TRP_Query */
			$this->trp_query = $trp->get_component( 'query' );
		}

		$start_time = microtime(true);
		$duration = 0;
		while( $duration < 2 ){
			$inferior_limit = $batch_size * $get_batch;
            $callback_return = call_user_func( $update_details['callback'], $language_code, $inferior_limit, $batch_size, $extra_params );

            if ( is_array( $callback_return ) && ! empty( $callback_return['error'] ) ) {
                $this->stop_and_print_error( esc_html( $callback_return['error'] ) );
            }

			if ( (isset($callback_return['finalize_with_language']) && $callback_return['finalize_with_language']) || ($callback_return === true)  ) {
				break;
			}else {
				$get_batch = $get_batch + 1;
                $extra_params = isset($callback_return['extra_params']) ? $callback_return['extra_params'] : array();
			}
			$stop_time = microtime( true );
			$duration = $stop_time - $start_time;
		}
        // the callback functions return different true or an array with finalized_with_language bool and extra_params based on what they need.
        // In case call_user_func fails with string, object, etc, it will continue with the callback and batching.
        // For example, if the function called uses to much memory, it will just continue.
        $finalized_with_language = (isset($callback_return['finalize_with_language']) && $callback_return['finalize_with_language']) || ($callback_return === true);

        if ( !$finalized_with_language ) {
			$request['trp_updb_batch'] = $get_batch;
            $request['trp_updb_extra_params'] = isset($callback_return['extra_params']) ? $callback_return['extra_params'] : array();
		}

		if ( $finalized_with_language ) {
            // finished with the current language
            $index = array_search( $language_code, $this->settings['translation-languages'] );

            if ( isset ( $this->settings['translation-languages'][ $index + 1 ] ) && (!isset($update_details['execute_only_once']) || $update_details['execute_only_once'] == false)) {
                // next language code in array
                $request['trp_updb_lang']    = $this->settings['translation-languages'][ $index + 1 ];
                $request['progress_message'] .= __( ' done.', 'translatepress-multilingual' ) . '</br>';
                $update_message_processing   = $this->get_updates_processing_message( sanitize_text_field( $_REQUEST['trp_updb_action'] ) ) ?
                    $this->get_updates_processing_message( sanitize_text_field( $_REQUEST['trp_updb_action'] ) )
                    : __( 'Processing table for language %s...', 'translatepress-multilingual' );
                $request['progress_message'] .= '</br>' . sprintf( $update_message_processing, $request['trp_updb_lang'] );

                    } else {
                        // finish action due to completing all the translation languages
                        $request['progress_message'] .= __( ' done.', 'translatepress-multilingual' ) . '</br>';
                        $request['trp_updb_lang']    = '';
                        $option_result = get_option( $update_details['option_name'], 'no' );

                        // the next IF is helpful in case we set the option to something else (such as seopack_inactive) during update
                        if ( $option_result === 'no' ) {
                            // setting option to yes will stop showing the admin notice
                            update_option( $update_details['option_name'], 'yes' );
                        }
                        $request['trp_updb_action'] = '';
                    }
		}else{
			$request['trp_updb_lang'] = $language_code;
            $request['progress_message'] .= '.';
		}

        if ( $this->db->last_error != '' ){
            $request['progress_message'] = '<p><strong>SQL Error:</strong> ' . esc_html($this->db->last_error) . '</p>' . $request['progress_message'];
        }
		$query_arguments = array(
			'action'                    => 'trp_update_database',
			'trp_updb_action'           => $request['trp_updb_action'],
			'trp_updb_lang'             => $request['trp_updb_lang'],
			'trp_updb_batch'            => $request['trp_updb_batch'],
			'trp_updb_extra_params'     => isset($request['trp_updb_extra_params']) ? base64_encode(json_encode($request['trp_updb_extra_params'])) : base64_encode(json_encode(array())),
			'trp_updb_nonce'            => wp_create_nonce('tpupdatedatabase'),
			'trp_update_completed'      => 'no',
			'progress_message'          => $request['progress_message']
		);
		echo( json_encode( $query_arguments ));
		wp_die();
	}

	public function stop_and_print_error( $error_message ){
		$back_to_settings_button = '<a class="trp-submit-btn button" href="' . site_url('wp-admin/options-general.php?page=translate-press') . '">' . esc_html__('Back to TranslatePress Settings', 'translatepress-multilingual' ) . '</a>';
		$query_arguments = array(
			'trp_update_completed'      => 'yes',
			'progress_message'          => '<p><strong>' . $error_message . '</strong></strong></p>' . $back_to_settings_button
		);
		echo( json_encode( $query_arguments ));
		wp_die();
	}

    /**
     * Build a structured legacy updater error without allowing the current
     * action to be marked as completed.
     *
     * @param string $operation Description of the failed operation.
     *
     * @return array|false
     */
    protected function get_legacy_database_update_error( $operation ) {
        if ( $this->db->last_error === '' ) {
            return false;
        }

        return array(
            'error' => sprintf(
                __( 'Update aborted while %1$s. SQL error: %2$s', 'translatepress-multilingual' ),
                $operation,
                $this->db->last_error
            ),
        );
    }

	/**
	 * Get all originals from the table, trim them and update originals back into table
	 *
	 * @param string $language_code     Language code of the table
	 * @param int $inferior_limit       Omit first X rows
	 * @param int $batch_size           How many rows to query
	 *
	 * @return bool
	 */
	public function trp_updated_database_full_trim_originals_140( $language_code, $inferior_limit, $batch_size ){
		if ( ! $this->trp_query ) {
			$trp = TRP_Translate_Press::get_trp_instance();
			/* @var TRP_Query */
			$this->trp_query = $trp->get_component( 'query' );
		}
		if ( $language_code == $this->settings['default-language']){
			// default language doesn't have a dictionary table
			return true;
		}
		$strings = $this->trp_query->get_rows_from_location( $language_code, $inferior_limit, $batch_size, array( 'id', 'original' ) );
		if ( count( $strings ) == 0 ) {
			return true;
		}
		foreach( $strings as $key => $string ){
			$strings[$key]['original'] = trp_full_trim( $strings[ $key ]['original'] );
		}

		// overwrite original only
		$this->trp_query->update_strings( $strings, $language_code, array( 'id', 'original' ) );
		return false;
	}

	/**
	 * Delete all empty gettext rows
	 *
	 * @param string $language_code     Language code of the table
	 * @param int $inferior_limit       Omit first X rows
	 * @param int $batch_size           How many rows to query
	 *
	 * @return bool
	 */
	public function trp_updated_database_gettext_empty_rows_145( $language_code, $inferior_limit, $batch_size ){
		if ( ! $this->trp_query ) {
			$trp = TRP_Translate_Press::get_trp_instance();
			/* @var TRP_Query */
			$this->trp_query = $trp->get_component( 'query' );
		}
		$rows_affected = $this->trp_query->delete_empty_gettext_strings( $language_code, $batch_size );
		if ( $rows_affected > 0 ){
			return false;
		}else{
			return true;
		}
	}

	/**
     * Normalize original ids for all dictionary entries
     *
     * @param string $language_code     Language code of the table
     * @param int $inferior_limit       Omit first X rows
     * @param int $batch_size           How many rows to query
     *
     * @return array|bool
     */
    public function trp_updated_database_original_id_insert_166( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        if ( $this->settings['default-language'] === $language_code ) {
            return true;
        }

        $this->trp_query->original_ids_insert( $language_code, $inferior_limit, $batch_size );
        $error = $this->get_legacy_database_update_error( __( 'inserting original strings', 'translatepress-multilingual' ) );

        if ( $error ) {
            return $error;
        }

        $last_id = $this->trp_query->get_last_id( $this->trp_query->get_table_name( $language_code, $this->settings['default-language'] ) );
        $error   = $this->get_legacy_database_update_error( __( 'checking original string insertion progress', 'translatepress-multilingual' ) );

        if ( $error ) {
            return $error;
        }

        return ( $inferior_limit + $batch_size ) >= $last_id;
    }

    public function trp_updated_database_original_id_cleanup_166( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $this->trp_query->original_ids_cleanup();
        $error = $this->get_legacy_database_update_error( __( 'cleaning duplicate original strings', 'translatepress-multilingual' ) );

        if ( $error ) {
            return $error;
        }

        return true;
    }

    /**
     * Normalize original ids for all dictionary entries
     *
     * @param string $language_code     Language code of the table
     * @param int $inferior_limit       Omit first X rows
     * @param int $batch_size           How many rows to query
     *
     * @return array|bool
     */
    public function trp_updated_database_original_id_update_166( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        if ( $this->settings['default-language'] === $language_code ) {
            return true;
        }

        $this->trp_query->original_ids_reindex( $language_code, $inferior_limit, $batch_size );
        $error = $this->get_legacy_database_update_error( __( 'updating original string IDs', 'translatepress-multilingual' ) );

        if ( $error ) {
            return $error;
        }

        $last_id = $this->trp_query->get_last_id( $this->trp_query->get_table_name( $language_code, $this->settings['default-language'] ) );
        $error   = $this->get_legacy_database_update_error( __( 'checking original string ID update progress', 'translatepress-multilingual' ) );

        if ( $error ) {
            return $error;
        }

        return ( $inferior_limit + $batch_size ) >= $last_id;
    }


    public function trp_prepare_options_for_database_optimization(){
        if ( !current_user_can( 'manage_options' ) || !isset( $_GET['trp_rm_nonce'] ) ) {
            return;
        }

        if ( !wp_verify_nonce( sanitize_text_field( $_GET['trp_rm_nonce'] ), 'tpremoveduplicaterows' ) ){
            exit;
        }

        $redirect = false;

        if(isset( $_GET['trp_rm_duplicates_gettext'] )){
            update_option('trp_remove_duplicate_gettext_rows', 'no');
            $redirect = true;
        }

        if(isset( $_GET['trp_rm_duplicates_dictionary'] )){
            update_option('trp_remove_duplicate_dictionary_rows', 'no');
            update_option('trp_remove_duplicate_untranslated_dictionary_rows', 'no');
            $redirect = true;
        }

        if ( isset( $_GET['trp_rm_duplicates_original_strings'] ) ){
            $this->trp_remove_duplicate_original_strings();
            $redirect = true;
        }

        if ( isset( $_GET['trp_rm_cdata_original_and_dictionary'])){
            update_option('trp_remove_cdata_original_and_dictionary_rows', 'no');
            $redirect = true;
        }

        if ( isset( $_GET['trp_rm_untranslated_links'] ) ){
            update_option('trp_remove_untranslated_links_dictionary_rows', 'no');
            $redirect = true;
        }

        if ( isset( $_GET['trp_replace_original_id_null'] ) ){
            update_option('trp_replace_original_id_null', 'no');
            $redirect = true;
        }

        if ( $redirect ) {
            $url = add_query_arg( array( 'page' => 'trp_update_database' ), site_url( 'wp-admin/admin.php' ) );
            wp_safe_redirect( $url );
            exit;
        }
    }

	/**
	 * Remove duplicate rows from DB for trp_dictionary tables.
	 * Removes untranslated strings if there is a translated version.
	 *
	 * Iterates over languages. Each language is iterated in batches of 10 000
	 *
	 * Not accessible from anywhere else
	 * http://example.com/wp-admin/admin.php?page=trp_remove_duplicate_rows
	 */
	public function trp_remove_duplicate_rows(){
		if ( ! current_user_can( 'manage_options' ) ){
			return;
		}
		// prepare page structure

		require_once TRP_PLUGIN_DIR . 'partials/trp-remove-duplicate-rows.php';
	}

	public function enqueue_update_script( $hook ) {
		if ( $hook === 'admin_page_trp_update_database' ) {
			wp_enqueue_script( 'trp-update-database', TRP_PLUGIN_URL . 'assets/js/trp-update-database.js', array(
				'jquery',
			), TRP_PLUGIN_VERSION );
		}

		wp_localize_script( 'trp-update-database', 'trp_updb_localized ', array(
			'admin_ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('tpupdatedatabase')
		) );
	}

    /**
     * Add full text index on the dictionary and gettext tables.
     * Gets executed once after update.
     */
	private function add_full_text_index_to_tables(){
	    $table_names = $this->trp_query->get_all_table_names('', array());
        $gettext_table_names = $this->trp_query->get_all_gettext_table_names();

        foreach (array_merge($table_names, $gettext_table_names) as $table_name){
            $possible_index = "SHOW INDEX FROM {$table_name} WHERE Key_name = 'original_fulltext';";
            if ($this->db->query($possible_index) === 1){
                continue;
            };

            $sql_index = "CREATE FULLTEXT INDEX original_fulltext ON `" . $table_name . "`(original);";
            $this->db->query( $sql_index );
        }
    }

    /**
     * Moving some settings from trp_settings option to trp_machine_translation_settings
     *
     * Upgrade settings from TP version 1.5.8 or earlier to 1.6.2
     */
    private function upgrade_machine_translation_settings(){
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_settings = $trp->get_component('settings' );
        $machine_translation_settings = get_option( 'trp_machine_translation_settings', false );

        $default_machine_translation_settings = $trp_settings->get_default_trp_machine_translation_settings();

        if ( $machine_translation_settings == false ) {
            // 1.5.8 did not have any machine_settings so port g-translate-key and g-translate settings if exists
            $machine_translation_settings = $default_machine_translation_settings;
            // move the old API key option
            if (!empty($this->settings['g-translate-key'] ) ) {
                $machine_translation_settings['google-translate-key'] = $this->settings['g-translate-key'];
            }

            // enable machine translation if it was activated before
            if (!empty($this->settings['g-translate']) && $this->settings['g-translate'] == 'yes'){
                $machine_translation_settings['machine-translation'] = 'yes';
            }
            update_option('trp_machine_translation_settings', $machine_translation_settings);
        }else{
            // targeting 1.5.9 to 1.6.1 where incomplete machine-translation settings may have resulted
            $machine_translation_settings = array_merge( $default_machine_translation_settings, $machine_translation_settings );
            update_option('trp_machine_translation_settings', $machine_translation_settings);
        }
    }

    /**
     *
     */
    private function set_force_slash_at_end_of_links(){
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_settings = $trp->get_component('settings' );
        $settings = $trp_settings->get_settings();

        if( !empty( $settings['trp_advanced_settings'] ) && !isset( $settings['trp_advanced_settings']['force_slash_at_end_of_links'] ) ){
            $advanced_settings = $settings['trp_advanced_settings'];
            $advanced_settings['force_slash_at_end_of_links'] = 'yes';
            update_option('trp_advanced_settings', $advanced_settings );
        }

    }

    public function add_iso_code_to_language_code(){
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_settings = $trp->get_component('settings' );
        $settings = $trp_settings->get_settings();

        if(isset($settings['trp_advanced_settings']) && isset($settings['trp_advanced_settings']['custom_language']) ){
            $advanced_settings = $settings['trp_advanced_settings'];
            if(!isset($advanced_settings['custom_language']['cuslangcode'])){
                $advanced_settings['custom_language']['cuslangcode'] = $advanced_settings['custom_language']['cuslangiso'];
            }
            update_option('trp_advanced_settings', $advanced_settings);
        }
    }

    public function create_opposite_ls_option(){

        add_filter('wp_loaded', array($this, 'call_create_menu_entries'));
    }

    public function call_create_menu_entries(){
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_settings = $trp->get_component('settings' );
        $settings = $trp_settings->get_settings();

        $trp_settings->create_menu_entries( $settings['publish-languages'] );
    }

    public function trp_remove_duplicate_original_strings(){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }
        $this->trp_query->rename_originals_table();
        $this->trp_query->check_original_table();

        update_option( 'trp_updated_database_original_id_insert_166', 'no' );
        update_option( 'trp_updated_database_original_id_cleanup_166', 'no' );
        update_option( 'trp_updated_database_original_id_update_166', 'no' );

        update_option( 'trp_regenerate_original_meta_table', 'no' );
        update_option( 'trp_clean_original_meta_table', 'no' );

    }

    public function trp_regenerate_original_meta_table($language_code, $inferior_limit, $batch_size ){

        if ( $language_code != $this->settings['default-language']) {
            // perform regeneration of original meta table only once
            return true;
        }
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }
        $this->trp_query->regenerate_original_meta_table($inferior_limit, $batch_size);

        $last_id = $this->db->get_var("SELECT MAX(meta_id) FROM " .  $this->trp_query->get_table_name_for_original_meta() );
        if ( $last_id < $inferior_limit ){
            // reached end of table
            return true;
        }else{
            // not done. get another batch
            return false;
        }
    }

    public function trp_clean_original_meta_table($language_code, $inferior_limit, $batch_size){
        if ( $language_code != $this->settings['default-language']) {
            // perform regeneration of original meta table only once
            return true;
        }
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }
        $rows_affected = $this->trp_query->clean_original_meta( $batch_size );
        if ( $rows_affected > 0 ){
            return false;
        }else{
            $old_originals_table = get_option( 'trp_original_strings_table_for_recovery', '' );
            if ( !empty ( $old_originals_table) && strpos($old_originals_table, 'trp_original_strings1') !== false ) {
                delete_option('trp_original_strings_table_for_recovery');
                $this->trp_query->drop_table( $old_originals_table );
            }
            return true;
        }
    }

    /**
     * Normalize original ids for all gettext entries
     *
     * @param string $language_code     Language code of the table
     * @param int $inferior_limit       Omit first X rows
     * @param int $batch_size           How many rows to query
     *
     * @return array|bool
     */
    public function trp_updated_database_gettext_original_id_insert( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }
        $gettext_normalization = $this->trp_query->get_query_component('gettext_normalization');
        $gettext_normalization->gettext_original_ids_insert( $language_code, $inferior_limit, $batch_size );
        $error = $this->get_legacy_database_update_error( __( 'inserting gettext original strings', 'translatepress-multilingual' ) );

        if ( $error ) {
            return $error;
        }

        $last_id = $this->trp_query->get_last_id( $this->trp_query->get_gettext_table_name($language_code) );
        $error   = $this->get_legacy_database_update_error( __( 'checking gettext original insertion progress', 'translatepress-multilingual' ) );

        if ( $error ) {
            return $error;
        }

        return ( $inferior_limit + $batch_size ) >= $last_id;
    }

    /**
     * Removes possible duplicates from within gettext_original_strings table
     *
     * @param $language_code
     * @param $inferior_limit
     * @param $batch_size
     * @return array|bool
     */
    public function trp_updated_database_gettext_original_id_cleanup( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $gettext_normalization = $this->trp_query->get_query_component('gettext_normalization');
        $gettext_normalization->gettext_original_ids_cleanup();
        $error = $this->get_legacy_database_update_error( __( 'cleaning duplicate gettext original strings', 'translatepress-multilingual' ) );

        if ( $error ) {
            return $error;
        }

        return true;
    }

    /**
     * Normalize original ids for all gettext entries
     *
     * @param string $language_code     Language code of the table
     * @param int $inferior_limit       Omit first X rows
     * @param int $batch_size           How many rows to query
     *
     * @return array|bool
     */
    public function trp_updated_database_gettext_original_id_update( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $gettext_normalization = $this->trp_query->get_query_component('gettext_normalization');
        $gettext_normalization->gettext_original_ids_reindex( $language_code, $inferior_limit, $batch_size );
        $error = $this->get_legacy_database_update_error( __( 'updating gettext original string IDs', 'translatepress-multilingual' ) );

        if ( $error ) {
            return $error;
        }

        $last_id = $this->trp_query->get_last_id( $this->trp_query->get_gettext_table_name( $language_code ) );
        $error   = $this->get_legacy_database_update_error( __( 'checking gettext original string ID update progress', 'translatepress-multilingual' ) );

        if ( $error ) {
            return $error;
        }

        return ( $inferior_limit + $batch_size ) >= $last_id;
    }

	    /**
	     * Run the gettext originals lookup hash migration through the dedicated migration class.
	     *
     * @param string $language_code Unused. Kept for updater callback signature.
     * @param int    $inferior_limit Unused. This migration resumes by DB state.
     * @param int    $batch_size Number of rows/groups/map entries per batch.
     * @param array  $extra_params Migration phase state.
     *
     * @return array|bool
     * @throws Exception
     */
    public function trp_updated_database_gettext_original_lookup_hash( $language_code, $inferior_limit, $batch_size, $extra_params = array() ) {
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        require_once TRP_PLUGIN_DIR . 'includes/upgrade/gettext-optimization/class-gettext-originals-lookup-hash-migration.php';

        $migration = new TRP_Gettext_Originals_Lookup_Hash_Migration( $this->trp_query );

        return $migration->run( $language_code, $inferior_limit, $batch_size, $extra_params );
    }

    /**
     * Classify gettext rows into language-file or runtime gettext statuses.
     *
     * Rows imported from .po/.mo files use status 4 when their stored
     * translation matches the active language-file translation. Rows that must
     * override runtime gettext output use the human-reviewed status 2. Machine
     * translated rows keep their existing status semantics.
     *
     * @param string $language_code
     * @param int    $inferior_limit
     * @param int    $batch_size
     * @param string $table_name Optional discovered gettext table name.
     *
     * @return bool
     */
    public function trp_updated_database_gettext_runtime_status_update( $language_code, $inferior_limit, $batch_size, $table_name = '' ) {
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $table_name      = is_string( $table_name ) && $table_name !== '' ? sanitize_text_field( $table_name ) : sanitize_text_field( $this->trp_query->get_gettext_table_name( $language_code ) );
        $originals_table = sanitize_text_field( $this->trp_query->get_table_name_for_gettext_original_strings() );

        if ( ! $this->trp_query->table_exists( $table_name ) ) {
            return true;
        }

        $rows = $this->db->get_results(
            $this->db->prepare(
                "SELECT tt.id, tt.translated, tt.status, tt.original AS tt_original, tt.domain AS tt_domain, tt.plural_form, ot.original, ot.domain, ot.context, ot.original_plural
                FROM `$table_name` AS tt
                LEFT JOIN `$originals_table` AS ot ON tt.original_id = ot.id
                ORDER BY tt.id
                LIMIT %d, %d",
                $inferior_limit,
                $batch_size
            ),
            ARRAY_A
        );

        if ( empty( $rows ) ) {
            return true;
        }

        $current_locale          = determine_locale();
        $switched                = switch_to_locale( $language_code );
        $language_file_available = $switched || $current_locale === $language_code;

        $ids_to_human_reviewed_status = array();
        $ids_to_language_file_status = array();

        foreach ( $rows as $row ) {
            $new_status = $this->get_migrated_gettext_status( $row, $language_file_available );

            if ( $new_status === TRP_Query::HUMAN_REVIEWED ) {
                $ids_to_human_reviewed_status[] = (int) $row['id'];
            } elseif ( $new_status === TRP_Query::GETTEXT_TRANSLATED_IN_LANGUAGE_FILE ) {
                $ids_to_language_file_status[] = (int) $row['id'];
            }
        }

        if ( $switched ) {
            restore_previous_locale();
        }

        $this->update_gettext_statuses( $table_name, $ids_to_human_reviewed_status, TRP_Query::HUMAN_REVIEWED );
        $this->update_gettext_statuses( $table_name, $ids_to_language_file_status, TRP_Query::GETTEXT_TRANSLATED_IN_LANGUAGE_FILE );

        $is_finished = count( $rows ) < $batch_size;

        return $is_finished;
    }

    /**
     * Decide the migrated gettext status for an existing DB row.
     *
     * Empty/untranslated rows and machine translated rows keep their existing
     * status semantics. Human/file-based rows use status 4 when they match the
     * active language-file output and status 2 when they must override it.
     *
     * @param array  $row                     Gettext row joined with the original gettext table.
     * @param bool   $language_file_available Whether WordPress loaded language files for the target locale.
     *
     * @return int|null
     */
    protected function get_migrated_gettext_status( $row, $language_file_available = true ) {
        if ( empty( $row['translated'] ) || (int) $row['status'] === TRP_Query::NOT_TRANSLATED ) {
            return null;
        }

        if ( (int) $row['status'] === TRP_Query::MACHINE_TRANSLATED ) {
            return TRP_Query::MACHINE_TRANSLATED;
        }

        $original        = ! empty( $row['original'] ) ? $row['original'] : $row['tt_original'];
        $domain          = ! empty( $row['domain'] ) ? $row['domain'] : $row['tt_domain'];
        $context         = ! empty( $row['context'] ) ? $row['context'] : 'trp_context';
        $plural_form     = isset( $row['plural_form'] ) ? (int) $row['plural_form'] : 0;
        $original_plural = ! empty( $row['original_plural'] ) ? $row['original_plural'] : null;

        if ( empty( $original ) || empty( $domain ) ) {
            return TRP_Query::HUMAN_REVIEWED;
        }

        if ( ! $language_file_available ) {
            $source_translation = ( ! empty( $original_plural ) && (int) $plural_form > 0 ) ? $original_plural : $original;

            return ( $row['translated'] !== $source_translation ) ? TRP_Query::HUMAN_REVIEWED : TRP_Query::GETTEXT_TRANSLATED_IN_LANGUAGE_FILE;
        }

        $mo_translation = $this->get_gettext_language_file_translation( $original, $domain, $context, $plural_form, $original_plural );

        return ( $row['translated'] !== $mo_translation ) ? TRP_Query::HUMAN_REVIEWED : TRP_Query::GETTEXT_TRANSLATED_IN_LANGUAGE_FILE;
    }

    /**
     * Return the translation WordPress would provide from loaded language files.
     *
     * The caller is responsible for switching to the target locale before calling
     * this method. If no matching entry exists in the language files, WordPress
     * returns the source string.
     *
     * @param string      $original        Original gettext string.
     * @param string      $domain          Text domain.
     * @param string      $context         Gettext context or trp_context placeholder.
     * @param int         $plural_form     Plural form index stored by TranslatePress.
     * @param string|null $original_plural Original plural string, when available.
     *
     * @return string
     */
    protected function get_gettext_language_file_translation( $original, $domain, $context, $plural_form, $original_plural ) {
        $translations = get_translations_for_domain( $domain );
        $context      = ( $context === 'trp_context' ) ? null : $context;

        if ( ! empty( $original_plural ) ) {
            $plural_forms = new TRP_Plural_Forms( $this->settings );

            if ( method_exists( $translations, 'translate_entry' ) ) {
                return $plural_forms->translate_plural( $original, $original_plural, $plural_form, $context, $translations );
            }

            return (int) $plural_form === 0 ? $original : $original_plural;
        }

        return $translations->translate( $original, $context );
    }

    /**
     * Bulk update gettext status for a list of gettext row ids.
     *
     * @param string $table_name     Gettext table name.
     * @param array  $ids            Row ids to update.
     * @param int    $status         New gettext status value.
     *
     * @return void
     */
    protected function update_gettext_statuses( $table_name, $ids, $status ) {
        if ( empty( $ids ) ) {
            return;
        }

        $ids = array_map( 'intval', $ids );
        $ids = array_filter( $ids );

        if ( empty( $ids ) ) {
            return;
        }

        $this->db->query(
            "UPDATE `$table_name` SET status = " . (int) $status . " WHERE id IN (" . implode( ',', $ids ) . ")"
        );
    }

    /**
     *
     * Hooked to admin_init
     */
    public function show_notification_about_add_ons_removal(){

        //if it's triggered in the frontend we need this include
        if( !function_exists('is_plugin_active') )
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $old_addon_list = array(
            'tp-add-on-automatic-language-detection/tp-automatic-language-detection.php',
            'tp-add-on-browse-as-other-roles/tp-browse-as-other-role.php',
            'tp-add-on-deepl/index.php',
            'tp-add-on-extra-languages/tp-extra-languages.php',
            'tp-add-on-navigation-based-on-language/tp-navigation-based-on-language.php',
            'tp-add-on-seo-pack/tp-seo-pack.php',
            'tp-add-on-translator-accounts/index.php',
        );

        foreach( $old_addon_list as $addon_slug ) {
            if (is_plugin_active($addon_slug)) {
                $notifications = TRP_Plugin_Notifications::get_instance();

                $notification_id = 'trp_add_ons_removal';
                //[utm37]
                $url_info = 'https://translatepress.com/docs/installation/upgrade-to-version-2-0-5-or-newer/?utm_source=wp-dashboard&utm_medium=client-site&utm_campaign=tp-bundle-update';
                //[utm38]
                $url_account = 'https://translatepress.com/account/?utm_source=wp-dashboard&utm_medium=client-site&utm_campaign=tp-bundle-update';
                $message = '<p style="padding-right:30px;">' . sprintf(__( 'All individual TranslatePress add-on plugins <a href="%1$s" target="_blank">have been discontinued</a> and are now included in the premium Personal, Business and Developer versions of TranslatePress. Please log into your <a href="%2$s" target="_blank">account page</a>, download the new premium version and install it. Your individual addons settings will be ported over.' , 'translatepress-multilingual' ), esc_url($url_info), esc_url($url_account)) . '</p>';
                //make sure to use the trp_dismiss_admin_notification arg
                $message .= '<a href="' . add_query_arg(array('trp_dismiss_admin_notification' => $notification_id)) . '" type="button" class="notice-dismiss" style="text-decoration: none;z-index:100;"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'translatepress-multilingual') . '</span></a>';

                $notifications->add_notification($notification_id, $message, 'trp-notice trp-narrow notice error is-dismissible', true, array('translate-press'), true);
                break;
            }
        }
    }

    /**
    * There is a very unfortunate error where the original_id is NULL for some gettext strings
    * We have to check is this is the case and create arrays that would help the editor to not get stuck and complete the original_id field.
    * We do this by getting the id from the wp_trp_gettext_original_strings and updating the wp_trp_gettext_current_language table with the original ids.
    */

    public function trp_replace_original_id_null($language_code, $inferior_limit, $batch_size){
        global $wpdb;
        $db       = $wpdb;

        $dictionary = array();
        $gettext_with_null_original_id_array= array();
        $original_id_get_ids_sync = array();
        $insert_gettext_original_id = array();

        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $last_id = $this->trp_query->get_last_id( $this->trp_query->get_gettext_table_name( $language_code ) );

        while ( $last_id > $inferior_limit ) {
            $dictionary = $this->trp_query->get_all_gettext_strings($language_code, $inferior_limit, $batch_size);
            $inferior_limit = $inferior_limit + $batch_size;


            if (!empty($dictionary)) {
                foreach ($dictionary as $current_language_string) {
                    if ($current_language_string['tt_original_id'] == NULL || $current_language_string['tt_original_id'] == 0) {
                        $gettext_with_null_original_id_array[] = array(
                            'original' => $current_language_string['tt_original'],
                            'id' => $current_language_string['id'],
                            'domain' => $current_language_string['tt_domain'],
                        );
                    }
                }

                $gettext_insert_update = $this->trp_query->get_query_component('gettext_insert_update');

                if (count($gettext_with_null_original_id_array) > 0) {
                    foreach ($gettext_with_null_original_id_array as $item) {
                        $original_id_get_ids_sync[] = $item;
                    }
                }
                $original_ids_null = $gettext_insert_update->gettext_original_strings_sync($original_id_get_ids_sync, false);
                if (count($original_ids_null) > 0) {
                    foreach ($original_ids_null as $key => $value) {
                        $insert_gettext_original_id[] = array(
                            'id' => $gettext_with_null_original_id_array[$key]['id'],
                            'original' => $gettext_with_null_original_id_array[$key]['original'],
                            'original_id' => $value,
                        );
                    }
                    $gettext_insert_update->update_gettext_strings($insert_gettext_original_id, $language_code, array('id', 'original', 'original_id'));
                }
            }


            $original_id_get_ids_sync = array();
            $gettext_with_null_original_id_array =array();

        }

        return true;
    }


    /*
     * @IMPORTANT
     * Here is the beginning of the slug data migration functions
     */

    /*
     * Functions that returns the name of the table needed for meta type slug inner join table
     * wp_posts or wp_terms
     */
    public function trp_get_table_name_for_original_join_in_meta_based_slugs( $meta_type ){
        if ( $meta_type == "postmeta" ) {
            return 'posts';
        }
        if ( $meta_type == "termmeta" ) {
            return 'terms';
        }
    }
    /*
     * Function that takes the name of the meta key to determine if the translation was manual or automatic
     */
    public function trp_get_meta_based_slug_status( $meta_values ) {
        if ( preg_match( '/automatically/', $meta_values['meta_key'] ) != false ) {
            return '1';
        } elseif ( preg_match( '/translated/', $meta_values['meta_key'] ) != false ) {
            return '2';
        }
        return '0';
    }

    /*
     * Function that gets the original slug by the selected [term/post]_id and forms the array structure needed for migration
     */
    public function trp_get_original_slugs_for_meta_based_slugs( $meta_type, $meta_slugs_and_ids, $language_code ) {

        $extracted_slugs_array      = array();
        if ( $meta_type == 'postmeta' ) {

            foreach ( $meta_slugs_and_ids as $meta_values ) {

                if ( !empty( $meta_values['post_name'] ) ) {
                    $extracted_slugs_array[ $meta_values['post_name'] ]["original"] = $meta_values['post_name'];
                    $extracted_slugs_array[ $meta_values['post_name'] ]["type"]     = 'post';

                    if ( isset( $meta_values['meta_value'] ) ) {
                        $extracted_slugs_array[ $meta_values['post_name'] ]["language"]   = $language_code;
                        $extracted_slugs_array[ $meta_values['post_name'] ]["status"]     = $this->trp_get_meta_based_slug_status( $meta_values );
                        $extracted_slugs_array[ $meta_values['post_name'] ]["translated"] = $meta_values['meta_value'];
                    }
                }
            }

            return $extracted_slugs_array;

        } elseif ( $meta_type == 'termmeta' ) {

            foreach ( $meta_slugs_and_ids as $meta_values ) {

                if ( !empty( $meta_values['slug'] ) ) {
                    $extracted_slugs_array[ $meta_values['slug'] ]["original"] = $meta_values['slug'];
                    $extracted_slugs_array[ $meta_values['slug'] ]["type"]     = 'term';

                    if ( isset( $meta_values['meta_value'] ) ) {
                        $extracted_slugs_array[ $meta_values['slug'] ]["language"]   = $language_code;
                        $extracted_slugs_array[ $meta_values['slug'] ]["status"]     = $this->trp_get_meta_based_slug_status( $meta_values );
                        $extracted_slugs_array[ $meta_values['slug'] ]["translated"] = $meta_values['meta_value'];
                    }
                }
            }

            return $extracted_slugs_array;
        }

        return $extracted_slugs_array;
    }

    public function get_last_id_for_meta_based_slugs( $meta_type, $language_code ){

        global $wpdb;

        $table_name = $wpdb->prefix . $meta_type;

        $select_query        = "SELECT COUNT(*) FROM `" . $table_name . "` ";
        $select_query       .= "WHERE ( meta_key LIKE %s OR meta_key LIKE %s )";
        $prepared_query      = $wpdb->prepare( $select_query, '%' . $wpdb->esc_like( 'trp_automatically_translated_slug_' . $language_code ) . '%', '%' . $wpdb->esc_like( 'trp_translated_slug_'. $language_code ) . '%' );
        $extracted_number    = $wpdb->get_var( $prepared_query );

        $last_id = intval( $extracted_number );

        return $last_id;

    }
    /*
     *  ~~~~~~~~ META BASED SLUG EXTRACTION ~~~~~~~~
     *
     * Function that gather all the information needed for the meta based slugs ro form a complete array with all the necessary
     * items for migration
     *
     * LOGISTIC: - the meta based slugs are saved in the *meta tables from wordpress with the meta key providing the information
     *             if the slug was manually or automatically translated, as well as, the language in which the slug was translated
     *           - the [post/term]_id tells as which post/page the original slug van be found
     *           - in the term case the slug can be found in the slug field dictated by the term_id
     *           - in the post case the slug can be found in the post_name field dictated by the post_id
     */
    public function trp_get_meta_based_slugs_from_db_284( $meta_type, $language_code, $inferior_limit, $batch_size ) {
        global $wpdb;

        $table_name_for_originals_for_the_selected_meta_type = $this->trp_get_table_name_for_original_join_in_meta_based_slugs( $meta_type );
        $extracted_slugs_array = array();

        if ( $meta_type == 'postmeta' ) {

            $select_query          = "SELECT meta_key, meta_value, trp_original_name.post_name FROM `" . $wpdb->postmeta . "` as trp_translation ";
            $select_query          .= "INNER JOIN `" . $wpdb->posts . "` as trp_original_name ";
            $select_query          .= "ON trp_translation.post_id = trp_original_name.ID ";
            $select_query          .= "WHERE ( meta_key LIKE %s OR meta_key LIKE %s ) LIMIT %d OFFSET %d";
            $prepared_query        = $wpdb->prepare( $select_query, '%' . $wpdb->esc_like( 'trp_automatically_translated_slug_' . $language_code ) . '%', '%' . $wpdb->esc_like( 'trp_translated_slug_' . $language_code ) . '%', $batch_size, $inferior_limit );
            $meta_slugs_and_ids    = $wpdb->get_results( $prepared_query, 'ARRAY_A' );

            $extracted_slugs_array = $this->trp_get_original_slugs_for_meta_based_slugs( 'postmeta', $meta_slugs_and_ids, $language_code );

        }elseif ( $meta_type == 'termmeta' ) {

            $select_query          = "SELECT meta_key, meta_value, trp_original_name.slug FROM `" . $wpdb->termmeta . "` as trp_translation ";
            $select_query          .= "INNER JOIN `" . $wpdb->terms . "` as trp_original_name ";
            $select_query          .= "ON trp_translation.term_id = trp_original_name.term_id ";
            $select_query          .= "WHERE ( meta_key LIKE %s OR meta_key LIKE %s ) LIMIT %d OFFSET %d";
            $prepared_query        = $wpdb->prepare( $select_query, '%' . $wpdb->esc_like( 'trp_automatically_translated_slug_' . $language_code ) . '%', '%' . $wpdb->esc_like( 'trp_translated_slug_' . $language_code ) . '%', $batch_size, $inferior_limit );
            $meta_slugs_and_ids    = $wpdb->get_results( $prepared_query, 'ARRAY_A' );

            $extracted_slugs_array = $this->trp_get_original_slugs_for_meta_based_slugs( 'termmeta', $meta_slugs_and_ids, $language_code );

        }

        return $extracted_slugs_array;
    }
    /*
     * ~~~~~~~~ OPTION BASED SLUGS EXTRACTION ~~~~~~~~
     *
     * In this function we extract the option based slugs and we arrange them in the needed structure for data migration
     *
     * LOGUSTIC: - the oprion based slugs are stored in the wp_options table under the option name of * trp_taxonomy_slug_translation
     *                                                                                                * trp_post_type_base_slug_translation
     *           - we extract the information from the field with is now stored in a nested array and refector it to the structure
     *           for slug data migration
     */
    public function trp_get_option_based_slugs_from_db_284( $option_name ) {
        $data = get_option( $option_name, array() );
        $extracted_slugs_array = array();

        $woocommerce_permalink_options = get_option( 'woocommerce_permalinks', false );

        foreach ( $data as $key => $values_array ) {
            $extracted_slugs_array[ $key ]["original"] = trim( $values_array["original"], '/' );

            if ( $values_array['type'] == 'post-type-base-slug' || $values_array['type'] == 'post-type-base' ) {
                $extracted_slugs_array[ $key ]["type"] = 'post-type-base';
                if ( apply_filters( 'trp_migrate_post_type_or_tax_original_only_if_active', true, $extracted_slugs_array[ $key ]["original"], $values_array['type'] ) && (
                    !post_type_exists( $extracted_slugs_array[ $key ]["original"] ) &&
                    ( $woocommerce_permalink_options === false ||
                        $extracted_slugs_array[ $key ]["original"] !== trim( $woocommerce_permalink_options['product_base'], '/' ) ) ) ) {
                    continue;
                }
            }else{
                if ( apply_filters( 'trp_migrate_post_type_or_tax_original_only_if_active', true, $extracted_slugs_array[ $key ]["original"], $values_array['type'] ) && (
                    !taxonomy_exists( $extracted_slugs_array[ $key ]["original"] ) &&
                    ( $woocommerce_permalink_options === false || (
                        $extracted_slugs_array[ $key ]["original"] !== $woocommerce_permalink_options['category_base'] &&
                        $extracted_slugs_array[ $key ]["original"] !== $woocommerce_permalink_options['tag_base'] ) ) ) ) {
                    continue;
                }
                $extracted_slugs_array[ $key ]["type"] = 'taxonomy';
            }
            foreach ( $values_array["translationsArray"] as $lang => $translation_element ) {
                if ( $translation_element["status"] == 0 ){
                    continue;
                }
                $extracted_slugs_array[ $key ][ $lang ]["language"] = $lang;
                $extracted_slugs_array[ $key ][ $lang ]["status"]      = $translation_element["status"];
                $extracted_slugs_array[ $key ][ $lang ]["translated"] = $translation_element["translated"];
            }
        }
        return $extracted_slugs_array;
    }

    /*
     *  All the extracted slugs from the old db are ordered when extrated
     *
     * The structure of slug type option based extracted array is:
     *
     *   (example using term slugs)
     *   taxonomy_slugs:
     *              taxonomy_slug_name:
     *                            original
     *                            type
     *                            language:
     *                                      status
     *                                      language
     *                                      translated
     *
     *  The structure of meta based slugs extracted array is:
     *  meta_slug_name:
     *                            original
     *                            type
     *                            language:
     *                                      status
     *                                      language
     *                                      translated
     *
     */

    /*
     * Migrating the option based slugs from the old structure to the new one
     *
     */
    public function trp_migrate_old_slug_to_new_parent_and_translate_slug_table_post_type_and_tax_284() {
        if ( class_exists( 'TRP_Slug_Query' ) ) {

            $trp                 = TRP_Translate_Press::get_trp_instance();
            $slug_query     = new TRP_Slug_Query();
            $trp_settings        = $trp->get_component( 'settings' );
            $settings            = $trp_settings->get_settings();
            $languages_to_verify = $settings['translation-languages'];

            $extracted_slugs_array                         = array();
            $extracted_slugs_array['taxonomy_slugs']       = $this->trp_get_option_based_slugs_from_db_284( 'trp_taxonomy_slug_translation' );
            $extracted_slugs_array['post_type_base_slugs'] = $this->trp_get_option_based_slugs_from_db_284( 'trp_post_type_base_slug_translation' );

            foreach ( $languages_to_verify as $language_code ) {

                if ( !( $language_code == $settings['default-language'] ) ) {
                    foreach ( $extracted_slugs_array as $key_slug_original => $slug_originals ) {

                        $array_for_translated_slugs               = array();
                        $language_is_found_in_this_array_of_slugs = false;

                        foreach ( $slug_originals as $keep_slug_to_determine_all_slugs_translation => $slug_original ) {

                            if ( isset( $slug_original[ $language_code ] ) ) {

                                $language_is_found_in_this_array_of_slugs = true;

                                $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['original']   = $slug_original['original'];
                                $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['translated'] = $slug_original[ $language_code ]['translated'];
                                $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['status']     = $slug_original[ $language_code ]['status'];
                                $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['type']       = $slug_original['type'];

                            }
                        }

                        if ( $language_is_found_in_this_array_of_slugs ) {
                            $slug_query->insert_slugs( $array_for_translated_slugs, $language_code );
                        }
                    }
                }
            }
            return true;
        }else{
            update_option( 'trp_migrate_old_slug_to_new_parent_and_translate_slug_table_post_type_and_tax_284', 'seopack_inactive' );
            return true;
        }
    }

    /*
     * Migrating the meta based post slugs from the old structure to the new one
     *
     * Using batches
     */
    public function trp_migrate_old_slug_to_new_parent_and_translate_slug_table_post_meta_284( $language_code, $inferior_limit, $batch_size ) {
        if ( class_exists( 'TRP_Slug_Query' ) ) {

            $trp             = TRP_Translate_Press::get_trp_instance();
            $slug_query = new TRP_Slug_Query();
            $trp_settings    = $trp->get_component( 'settings' );
            $settings        = $trp_settings->get_settings();

            $last_id = $this->get_last_id_for_meta_based_slugs( 'postmeta', $language_code );

            $extracted_slugs_array_post = array();
            $extracted_slugs_array_post = $this->trp_get_meta_based_slugs_from_db_284( 'postmeta', $language_code, $inferior_limit, $batch_size );

            $array_for_translated_slugs               = array();
            $language_is_found_in_this_array_of_slugs = false;

            foreach ( $extracted_slugs_array_post as $keep_slug_to_determine_all_slugs_translation => $slug_original ) {

                $language_is_found_in_this_array_of_slugs = true;

                $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['original'] = $keep_slug_to_determine_all_slugs_translation;
                $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['type']     = $slug_original['type'];

                if ( isset( $slug_original['translated'] ) ) {
                    $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['translated'] = $slug_original['translated'];
                    $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['status']     = $slug_original['status'];
                }
            }

            if ( $language_is_found_in_this_array_of_slugs ) {
                $slug_query->insert_slugs( $array_for_translated_slugs, $language_code );
            }

            if ( $inferior_limit + $batch_size <= $last_id ) {
                return false;
            } else {
                return true;
            }
        }else{
            update_option( 'trp_migrate_old_slug_to_new_parent_and_translate_slug_table_post_meta_284', 'seopack_inactive' );
            return true;
        }
    }

    /*
     * Migrating the meta based term slugs from the old structure to the new one
     *
     * Using batches
     */
    public function trp_migrate_old_slug_to_new_parent_and_translate_slug_table_term_meta_284( $language_code, $inferior_limit, $batch_size ) {

        if ( class_exists( 'TRP_Slug_Query' ) ) {

            $trp             = TRP_Translate_Press::get_trp_instance();
            $slug_query = new TRP_Slug_Query();
            $trp_settings    = $trp->get_component( 'settings' );
            $settings        = $trp_settings->get_settings();

            $last_id = $this->get_last_id_for_meta_based_slugs( 'termmeta', $language_code );

            $extracted_slugs_array_term = array();
            $extracted_slugs_array_term = $this->trp_get_meta_based_slugs_from_db_284( 'termmeta', $language_code, $inferior_limit, $batch_size );

            $array_for_translated_slugs               = array();
            $language_is_found_in_this_array_of_slugs = false;

            foreach ( $extracted_slugs_array_term as $keep_slug_to_determine_all_slugs_translation => $slug_original ) {

                $language_is_found_in_this_array_of_slugs = true;

                $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['original'] = $keep_slug_to_determine_all_slugs_translation;
                $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['type']     = $slug_original['type'];

                if ( isset( $slug_original['translated'] ) ) {
                    $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['translated'] = $slug_original['translated'];
                    $array_for_translated_slugs[ $keep_slug_to_determine_all_slugs_translation ]['status']     = $slug_original['status'];
                }
            }

            if ( $language_is_found_in_this_array_of_slugs ) {
                $slug_query->insert_slugs( $array_for_translated_slugs, $language_code );
            }

            if ( $inferior_limit + $batch_size <= $last_id ) {
                return false;
            } else {
                return true;
            }
        }else{
            update_option( 'trp_migrate_old_slug_to_new_parent_and_translate_slug_table_term_meta_284', 'seopack_inactive' );
            return true;
        }
    }

    /**
     * Migrate machine_translation_counter to a standalone row in the wp_options.
     * It's necessary for running atomic queries on it
     *
     * @return void
     */
    public function migrate_machine_translation_counter() {
        $mt_settings_option = get_option( 'trp_machine_translation_settings', null );
        if ( $mt_settings_option && isset( $mt_settings_option['machine_translation_counter'] ) ) {
            update_option( 'trp_machine_translation_counter', $mt_settings_option['machine_translation_counter'] );
        }
    }

    /*
     * Since Version 2.7.5
     * Adding index on the column block_type in dictionary table for performance improvement
     * In the function that creates the dictionaty tables, a syntax to add this index was also written
     */
    public function add_tp_block_index() {
        global $wpdb;

        $prefix = $wpdb->prefix;
        $suffix = 'trp_dictionary_';

        $tables = $wpdb->get_col("SHOW TABLES LIKE '%$prefix%$suffix%'");

        foreach ($tables as $table) {

            $indexes = $wpdb->get_results("SHOW INDEXES FROM $table WHERE Key_name = 'block_type'");

            if (empty($indexes)) {
                $columns = $wpdb->get_results("DESCRIBE $table");
                if (array_search('block_type', wp_list_pluck($columns, 'Field')) !== false) {

                    //using %1s because a sql syntax error was persistant when using %s; basically the %s added ''(quotes) around the table name
                    //after searhing online, it was sugested to use %i which worked fine but using %1s also seems o work
                    $query = $wpdb->prepare("ALTER TABLE %1s ADD INDEX block_type ( block_type )", $table);
                    $wpdb->query($query);

                }
            }
        }

    }

    /**
     * @return void
     *
     *  Verifies if the options set to 'no' in DB optimization tool are 'no' and, if so, setting them to 'yes'
     */
    public function set_the_options_set_in_db_optimization_tool_to_no(){
        $array_of_options_to_check_and_set_for_db_optimization = array( "trp_regenerate_original_meta_table",
                                                                        "trp_clean_original_meta_table",
                                                                        "trp_updated_database_original_id_insert_166",
                                                                        "trp_updated_database_original_id_cleanup_166",
                                                                        "trp_updated_database_original_id_update_166",
                                                                        "trp_remove_duplicate_dictionary_rows",
                                                                        "trp_remove_duplicate_gettext_rows",
                                                                        "trp_remove_duplicate_untranslated_dictionary_rows",
                                                                        "trp_remove_cdata_original_and_dictionary_rows",
                                                                        "trp_remove_untranslated_links_dictionary_rows",
                                                                        "trp_replace_original_id_null" );

        foreach ( $array_of_options_to_check_and_set_for_db_optimization as $option ){

            if ( ( get_option( $option, 'not_set' ) == 'no' ) ){
                update_option( $option, 'yes' );
            }
        }

    }

    /**
     *  Used to check if the minimum pro plugin version (required after refactoring slug translation) is installed.
     *
     * @return bool
     */
    public function is_seo_pack_minimum_version_met(){
        if ( !defined('TRP_IN_SP_PLUGIN_VERSION' ) ) return true;

        return version_compare( TRP_IN_SP_PLUGIN_VERSION, self::MINIMUM_SP_VERSION, '>=' );
    }

    public function dont_update_db_if_seopack_inactive(){
        $array_of_option_names = ['trp_migrate_old_slug_to_new_parent_and_translate_slug_table_post_type_and_tax_284','trp_migrate_old_slug_to_new_parent_and_translate_slug_table_post_meta_284','trp_migrate_old_slug_to_new_parent_and_translate_slug_table_term_meta_284'];
        foreach ($array_of_option_names as $option ) {
            $option_result = get_option( $option, 'not_set' );
            if ( $option_result === 'yes' ) {
                continue;
            }

            if ( $option_result === 'no' && !class_exists( 'TRP_Slug_Query' ) ) {
                update_option( $option, 'seopack_inactive' );
                delete_option('trp_show_error_db_message');
            }
        }
    }

    /**
     * Fix bug specific to 2.9.7 version where if the user saved settings, the publish-languages were lost
     *
     * @return void
     */
    public function set_publish_languages_from_translation_languages() {
        $extra_languages_is_active = class_exists( 'TRP_IN_Extra_Languages' );
        $trp_settings              = get_option( 'trp_settings' );
        if ( !$extra_languages_is_active &&
            count( $trp_settings['translation-languages'] ) <= 2 &&
            ( empty( array_diff( $trp_settings['translation-languages'], $trp_settings['publish-languages'] ) ) ||
            empty( array_diff( $trp_settings['publish-languages'], $trp_settings['translation-languages'] ) ) )
        ) {
            $trp_settings['publish-languages'] = $trp_settings['translation-languages'];
            update_option( 'trp_settings', $trp_settings );
        }
    }

    /**
     * Seed Language Switcher V2 settings on version change for upgraded sites.
     * - New installs (no stored version) are ignored (defaults applied later by LS tab).
     * - Upgrades: ensure option exists and enable legacy mode.
     *
     */
    public function migrate_to_language_switcher_v2(): void {
        // Seed from defaults or existing option
        $defaults = TRP_Language_Switcher_Tab::default_switcher_config();
        $cfg = get_option('trp_language_switcher_settings', null);
        if ( !is_array($cfg) || empty($cfg) ) {
            $cfg = $defaults;
        }

        // Ensure branches exist
        foreach (['floater','shortcode','menu'] as $scope) {
            if ( empty($cfg[$scope]) || !is_array($cfg[$scope]) ) {
                $cfg[$scope] = $defaults[$scope];
            }
            if ( empty( $cfg[$scope]['layoutCustomizer'] ) || !is_array( $cfg[$scope]['layoutCustomizer'] ) ) {
                $cfg[$scope]['layoutCustomizer'] = $defaults[$scope]['layoutCustomizer'];
            }
            foreach (['desktop','mobile'] as $device) {
                if ( empty( $cfg[$scope]['layoutCustomizer'][$device] ) || !is_array( $cfg[$scope]['layoutCustomizer'][$device] ) ) {
                    $cfg[$scope]['layoutCustomizer'][$device] = $defaults[$scope]['layoutCustomizer'][$device];
                }
            }
        }

        // Local utilities
        $map_preset = static function( string $preset ): array {
            switch ( $preset ) {
                case 'full-names':        return ['flagIconPosition' => 'hide',   'languageNames' => 'full'];
                case 'short-names':       return ['flagIconPosition' => 'hide',   'languageNames' => 'short'];
                case 'flags-full-names':  return ['flagIconPosition' => 'before', 'languageNames' => 'full'];
                case 'flags-short-names': return ['flagIconPosition' => 'before', 'languageNames' => 'short'];
                case 'only-flags':        return ['flagIconPosition' => 'before', 'languageNames' => 'none'];
                default:                  return ['flagIconPosition' => 'before', 'languageNames' => 'full'];
            }
        };

        $apply_layout = static function ( array $layout, array $pairs ): array {
            foreach ( [ 'desktop', 'mobile' ] as $device ) {
                if ( ! isset( $layout[ $device ] ) || ! is_array( $layout[ $device ] ) ) {
                    $layout[ $device ] = [];
                }
                foreach ( $pairs as $k => $v ) {
                    $layout[ $device ][ $k ] = $v; // force overwrite
                }
            }
            return $layout;
        };

        $cfg['floater']['enabled'] = $this->settings['trp-ls-floater'] === 'yes';

        // Map legacy presets -> new layout options
        $shortcode_preset = isset($this->settings['shortcode-options']) ? sanitize_key($this->settings['shortcode-options']) : '';
        $cfg['shortcode']['layoutCustomizer'] = $apply_layout(
            $cfg['shortcode']['layoutCustomizer'],
            $map_preset($shortcode_preset)
        );

        $menu_preset = isset($this->settings['menu-options']) ? sanitize_key($this->settings['menu-options']) : '';
        $cfg['menu']['layoutCustomizer'] = $apply_layout(
            $cfg['menu']['layoutCustomizer'],
            $map_preset($menu_preset)
        );

        $floater_preset = isset($this->settings['floater-options']) ? sanitize_key($this->settings['floater-options']) : '';
        $cfg['floater']['layoutCustomizer'] = $apply_layout(
            $cfg['floater']['layoutCustomizer'],
            $map_preset($floater_preset)
        );

        // ===== Floater position → layoutCustomizer (both devices) =====
        $allowed_positions = ['bottom-right','bottom-left','top-right','top-left'];
        $position = isset($this->settings['floater-position']) ? sanitize_key($this->settings['floater-position']) : '';
        if ( !in_array($position, $allowed_positions, true) ) {
            $position = $defaults['floater']['layoutCustomizer']['desktop']['position'];
        }
        $cfg['floater']['layoutCustomizer'] = $apply_layout(
            $cfg['floater']['layoutCustomizer'],
            ['position' => $position]
        );

        // Floater color scheme
        $color_scheme   = isset($this->settings['floater-color']) ? sanitize_key($this->settings['floater-color']) : 'light';

        if ( $color_scheme === 'dark' ) {
            $cfg['floater']['bgColor']        = '#000000';
            $cfg['floater']['bgHoverColor']   = '#444444';
            $cfg['floater']['textColor']      = '#ffffff';
            $cfg['floater']['textHoverColor'] = '#eeeeee';
            $cfg['floater']['borderColor']    = 'transparent';
        } else {
            $cfg['floater']['bgColor']        = '#ffffff';
            $cfg['floater']['bgHoverColor']   = '#0000000D';
            $cfg['floater']['textColor']      = '#143852';
            $cfg['floater']['textHoverColor'] = '#1D2327';
            $cfg['floater']['borderColor']    = '#1438521A';
        }

        $cfg['floater']['showPoweredBy'] = ( isset($this->settings['trp-ls-show-poweredby']) && $this->settings['trp-ls-show-poweredby'] === 'yes' );

        // Advanced settings -> new flags (only if missing)
        $adv_settings = ( isset($this->settings['trp_advanced_settings']) && is_array($this->settings['trp_advanced_settings']) )
            ? $this->settings['trp_advanced_settings']
            : [];

        if ( !isset($cfg['shortcode']['clickLanguage']) ) {
            $cfg['shortcode']['clickLanguage'] =
                ( isset($adv_settings['open_language_switcher_shortcode_on_click']) && $adv_settings['open_language_switcher_shortcode_on_click'] === 'yes' );
        }

        if ( !isset($cfg['floater']['oppositeLanguage']) ) {
            $is_opposite_language_enabled = ( isset($adv_settings['show_opposite_flag_language_switcher_shortcode']) && $adv_settings['show_opposite_flag_language_switcher_shortcode'] === 'yes' );

            $cfg['floater']['oppositeLanguage']   = $is_opposite_language_enabled;
            $cfg['shortcode']['oppositeLanguage'] = $is_opposite_language_enabled;
        }

        $adv_settings['load_legacy_language_switcher'] = 'yes';

        update_option( 'trp_advanced_settings', $adv_settings );
        update_option( 'trp_language_switcher_settings', $cfg );
        update_option( 'trp_ls_v2_migrated_from_legacy', 'yes' );
    }

    /**
     * If user migrated from the legacy switcher, register a dismissible admin notice.
     */
    public function show_language_switcher_v2_intro_notice() : void {
        if ( ! is_admin() || ! current_user_can( apply_filters( 'trp_settings_capability', 'manage_options' ) ) )
            return;

        $notification_id = 'trp_ls_v2_intro';

        // Dismissed
        if ( get_user_meta( get_current_user_id(), $notification_id . '_dismiss_notification', true ) )
            return;

        $migrated = get_option( 'trp_ls_v2_migrated_from_legacy', 'no' ) === 'yes';

        if ( !$migrated )
            return;

        $settings_url = add_query_arg(
            [ 'trp_dismiss_admin_notification' => $notification_id ],
            admin_url( 'admin.php?page=trp_language_switcher' )
        );

        $logo_url     = trailingslashit( TRP_PLUGIN_URL ) . 'assets/images/tp-logo.png';
        $dismiss_link = '<a style="text-decoration:none;z-index:100;" href="' .
            esc_url( add_query_arg( [ 'trp_dismiss_admin_notification' => $notification_id ] ) ) .
            '" type="button" class="notice-dismiss"><span class="screen-reader-text">' .
            esc_html__( 'Dismiss this notice.', 'translatepress-multilingual' ) .
            '</span></a>';

        //[utm39]
        $docs_url = 'https://translatepress.com/docs/settings/language-switcher/?utm_source=tp-language-switcher&utm_medium=client-site&utm_campaign=ls-legacy#legacy-mode';

        $css = '
            .trp-ls-v2-card{display:grid;gap:8px;padding:10px;}
            .trp-ls-v2-header{display:flex;align-items:center;gap:8px;}
            .trp-ls-v2-card .trp-ls-v2-logo img{max-width:36px;}
            .trp-ls-v2-card .trp-ls-v2-heading{margin:0;font-size:16px;line-height:1.3;}
            .trp-ls-v2-card .trp-ls-v2-desc{margin:0;color:#3c434a;}
            .trp-ls-v2-card .trp-ls-v2-cta{margin:0;display:flex;align-items:center;gap:15px;}
            .trp-ls-v2-card .trp-ls-v2-cta .trp-submit-btn{color:#ffffff; background:#2271B1;border-radius:5px;font-size:14px;border:1px solid #2271B1;padding:4px 12px;min-height:40px;cursor:pointer;}
            .trp-ls-v2-card .trp-ls-v2-cta .trp-submit-btn:hover{background:transparent !important;color:#2271B1;border-color:#2271B1}
            .trp-ls-v2-card .trp-ls-v2-cta .trp-doc-link{font-size:14px;text-decoration:none;color:#2271B1}
            .trp-ls-v2-card .trp-ls-v2-cta .trp-doc-link:hover{text-decoration:underline}
        ';

        add_action( 'admin_head', function () use ( $css ) : void {
            echo '<style id="trp-ls-v2-admin-css">' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        });

        $message = '<div class="trp-ls-v2-card">';
        $message .=   '<div class="trp-ls-v2-header">';
        $message .=       '<div class="trp-ls-v2-logo"><img src="' . esc_url( $logo_url ) . '" alt="TranslatePress logo" /></div>';
        $message .=       '<p class="trp-ls-v2-heading"><strong>' . esc_html__( 'Brand-new Language Switcher Settings are here!', 'translatepress-multilingual' ) . '</strong></p>';
        $message .=   '</div>';
        $message .=   '<p class="trp-ls-v2-desc">'
                        . esc_html__( 'Explore pre-made templates, switch colors, flag styles, spacing, layouts & more. Use the live preview to perfect your switcher in seconds.', 'translatepress-multilingual' )
                    . '</p>';

        $message .=   '<div class="trp-ls-v2-cta">'
                    .     '<a class="trp-submit-btn button" href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Start customizing', 'translatepress-multilingual' ) . '</a>'
                    .     '<a class="trp-doc-link" href="' . esc_url( $docs_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Read documentation', 'translatepress-multilingual' ) . '</a>'
                  .   '</div>';

        $message .=   $dismiss_link;
        $message .= '</div>';

        $notifications = TRP_Plugin_Notifications::get_instance();
        $notifications->add_notification(
            $notification_id,
            $message,
            'trp-notice notice notice-info is-dismissible',
            false,
            [ 'translate-press' ],
            true
        );
    }

}
