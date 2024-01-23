<?php

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
            foreach ($updates as $update) {
                if (version_compare($update['version'], $stored_database_version, '>')) {
                    update_option($update['option_name'], 'no');
                }
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

	public function get_updates_details(){
		return apply_filters( 'trp_updates_details',
			array(
                'remove_cdata_original_and_dictionary_rows' => array(
                    'version'           => '0',
                    'option_name'       => 'trp_remove_cdata_original_and_dictionary_rows',
                    'callback'          => array( $this->trp_query,'remove_cdata_in_original_and_dictionary_tables'),
                    'batch_size'        => 1000,
                    'message_initial'   => '',
                    'message_processing'=> __('Removing cdata dictionary strings for language %s...', 'translatepress-multilingual' )
                ),
                'remove_untranslated_links_dictionary_rows' => array(
                    'version'           => '0',
                    'option_name'       => 'trp_remove_untranslated_links_dictionary_rows',
                    'callback'          => array( $this->trp_query,'remove_untranslated_links_in_dictionary_table'),
                    'batch_size'        => 10000,
                    'message_initial'   => '',
                    'message_processing'=> __('Removing untranslated dictionary links for language %s...', 'translatepress-multilingual' )
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
                    'batch_size'        => 10000,
                    'message_initial'   => '',
                    'message_processing'=> __('Removing duplicated gettext strings for language %s...', 'translatepress-multilingual' )
                ),
                'remove_duplicate_untranslated_gettext_rows' => array(
                    'version'           => '0',
                    'option_name'       => 'trp_remove_duplicate_untranslated_gettext_rows',
                    'callback'          => array( $this->trp_query,'remove_untranslated_strings_if_gettext_translation_available'),
                    'batch_size'        => 10000,
                    'message_initial'   => '',
                    'message_processing'=> __('Removing untranslated gettext strings where translation is available for language %s...', 'translatepress-multilingual' )
                ),
                'remove_duplicate_dictionary_rows' => array(
                    'version'           => '0',
                    'option_name'       => 'trp_remove_duplicate_dictionary_rows',
                    'callback'          => array( $this->trp_query,'remove_duplicate_rows_in_dictionary_table'),
                    'batch_size'        => 1000,
                    'message_initial'   => '',
                    'message_processing'=> __('Removing duplicated dictionary strings for language %s...', 'translatepress-multilingual' )
                ),
                'remove_duplicate_untranslated_dictionary_rows' => array(
                    'version'           => '0',
                    'option_name'       => 'trp_remove_duplicate_untranslated_dictionary_rows',
                    'callback'          => array( $this->trp_query,'remove_untranslated_strings_if_translation_available'),
                    'batch_size'        => 10000,
                    'message_initial'   => '',
                    'message_processing'=> __('Removing untranslated dictionary strings where translation is available for language %s...', 'translatepress-multilingual' )
                ),
                'original_id_insert_166' => array(
                    'version'           => '1.6.6',
                    'option_name'       => 'trp_updated_database_original_id_insert_166',
                    'callback'          => array( $this,'trp_updated_database_original_id_insert_166'),
                    'batch_size'        => 1000,
                    'message_processing'=> __('Inserting original strings for language %s...', 'translatepress-multilingual' )
                ),
                'original_id_cleanup_166' => array(
                    'version'           => '1.6.6',
                    'option_name'       => 'trp_updated_database_original_id_cleanup_166',
                    'callback'          => array( $this,'trp_updated_database_original_id_cleanup_166'),
                    'progress_message'  => 'clean',
                    'batch_size'        => 1000,
                    'message_initial'   => '',
                    'message_processing'=> __('Cleaning original strings table for language %s...', 'translatepress-multilingual' )
                ),
                'original_id_update_166' => array(
                    'version'           => '1.6.6',
                    'option_name'       => 'trp_updated_database_original_id_update_166',
                    'callback'          => array( $this,'trp_updated_database_original_id_update_166'),
                    'batch_size'        => 5000,
                    'message_initial'   => '',
                    'message_processing'=> __('Updating original string ids for language %s...', 'translatepress-multilingual' )
                ),
                'regenerate_original_meta' => array(
                    'version'           => '0', // independent of tp version, available only on demand
                    'option_name'       => 'trp_regenerate_original_meta_table',
                    'callback'          => array( $this,'trp_regenerate_original_meta_table'),
                    'batch_size'        => 200,
                    'message_initial'   => '',
                    'message_processing'=> __('Regenerating original meta table for language %s...', 'translatepress-multilingual' )
                ),
                'clean_original_meta' => array(
                    'version'           => '0', // independent of tp version, available only on demand
                    'option_name'       => 'trp_clean_original_meta_table',
                    'callback'          => array( $this,'trp_clean_original_meta_table'),
                    'batch_size'        => 20000,
                    'message_initial'   => '',
                    'message_processing'=> __('Cleaning original meta table for language %s...', 'translatepress-multilingual' )
                ),
                'replace_original_id_null' => array(
                    'version'           => '0', // independent of tp version, available only on demand
                    'option_name'       => 'trp_replace_original_id_null',
                    'callback'          => array( $this,'trp_replace_original_id_null'),
                    'batch_size'        => 50,
                    'message_initial'   => '',
                    'message_processing'=> __('Replacing original id NULL with value for language %s...', 'translatepress-multilingual' )
                ),
                'gettext_original_id_insert' => array(
                    'version'           => '2.3.8',
                    'option_name'       => 'trp_updated_database_gettext_original_id_insert',
                    'callback'          => array( $this,'trp_updated_database_gettext_original_id_insert'),
                    'batch_size'        => 1000,
                    'message_processing'=> __('Inserting gettext original strings for language %s...', 'translatepress-multilingual' )
                ),
                'gettext_original_id_cleanup' => array(
                    'version'           => '2.3.8',
                    'option_name'       => 'trp_updated_database_gettext_original_id_cleanup',
                    'callback'          => array( $this,'trp_updated_database_gettext_original_id_cleanup'),
                    'progress_message'  => 'clean',
                    'batch_size'        => 1000,
                    'message_initial'   => '',
                    'message_processing'=> __('Cleaning gettext original strings table for language %s...', 'translatepress-multilingual' )
                ),
                'gettext_original_id_update' => array(
                    'version'           => '2.3.8',
                    'option_name'       => 'trp_updated_database_gettext_original_id_update',
                    'callback'          => array( $this,'trp_updated_database_gettext_original_id_update'),
                    'batch_size'        => 5000,
                    'message_initial'   => '',
                    'message_processing'=> __('Updating gettext original string ids for language %s...', 'translatepress-multilingual' )
                ),
                'show_error_db_message' => array(
                    'version'           => '0', // independent of tp version, available only on demand
                    'option_name'       => 'trp_show_error_db_message',
                    'callback'          => array( $this,'trp_successfully_run_database_optimization'),
                    'batch_size'        => 10,
                    'message_initial'   => '',
                    'message_processing'=> __('Finishing up...', 'translatepress-multilingual' ),
                    'execute_only_once' => true
                )
			)
		);
        /**
         * Write 3.0.0 if 2.9.9 is the current version, and 3.0.0 will be the updated version where this code will be launched.
         */
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
            foreach ( $updates_needed as $update ) {
                $option = get_option( $update['option_name'], 'is not set' );
                if ( $option === 'no' && $option_db_error_message !== 'no' ) {
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

		$url = add_query_arg( array(
			'page'                      => 'trp_update_database',
		), site_url('wp-admin/admin.php') );

		// maybe change notice color to blue #28B1FF
		$html = '<div id="message" class="updated">';
		$html .= '<p><strong>' . esc_html__( 'TranslatePress data update', 'translatepress-multilingual' ) . '</strong> &#8211; ' . esc_html__( 'We need to update your translations database to the latest version.', 'translatepress-multilingual' ) . '</p>';
		$html .= '<p class="submit"><a href="' . esc_url( $url ) . '" onclick="return confirm( \'' . __( 'IMPORTANT: It is strongly recommended to first backup the database!\nAre you sure you want to continue?', 'translatepress-multilingual' ) . '\');" class="button-primary">' . esc_html__( 'Run the updater', 'translatepress-multilingual' ) . '</a></p>';
		$html .= '</div>';
		echo $html;//phpcs:ignore
	}

    public function trp_successfully_run_database_optimization($language_code= null, $inferior_size = null, $batch_size = null){
        delete_option('trp_show_error_db_message');

        return true;
    }


    public function show_admin_error_message(){
        if ( ( isset( $_GET[ 'page'] ) && $_GET['page'] == 'trp_update_database' ) ){
            return;
        }
        $updates_needed = $this->get_updates_details();
        $option_db_error_message = get_option($updates_needed['show_error_db_message']['option_name']);
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
				$option = get_option( $update['option_name'], 'is not set' );
				if ( $option === 'no' ) {
					$_REQUEST['trp_updb_action'] = $update_action_key;
					break;
				}
			}
			if ( empty ( $_REQUEST['trp_updb_action'] ) ){
				$back_to_settings_button = '<p><a href="' . site_url('wp-admin/options-general.php?page=translate-press') . '"> <input type="button" value="' . esc_html__('Back to TranslatePress Settings', 'translatepress-multilingual' ) . '" class="button-primary"></a></p>';
				// finished successfully
				echo json_encode( array(
					'trp_update_completed' => 'yes',
					'progress_message'  => '<p><strong>' . __('Successfully updated database!', 'translatepress-multilingual' ) . '</strong></p>' . $back_to_settings_button
				));
				wp_die();
			}else{
				$_REQUEST['trp_updb_lang'] = $this->settings['translation-languages'][0];
				$_REQUEST['trp_updb_batch'] = 0;

                $update_message_initial = isset( $updates_needed[$_REQUEST['trp_updb_action']]['message_initial'] ) ?
                                            $updates_needed[sanitize_text_field( $_REQUEST['trp_updb_action'] )]['message_initial']
                                            : __('Updating database to version %s+', 'translatepress-multilingual' );

                $update_message_processing = isset( $updates_needed[$_REQUEST['trp_updb_action']]['message_processing'] ) ?
                                                $updates_needed[ sanitize_text_field( $_REQUEST['trp_updb_action'] )]['message_processing']
                                                : __('Processing table for language %s...', 'translatepress-multilingual' );

                if ($updates_needed[ sanitize_text_field( $_REQUEST['trp_updb_action'] ) ]['version'] != 0) {
                    $request['progress_message'] .= '<p>' . sprintf( $update_message_initial, $updates_needed[ sanitize_text_field( $_REQUEST['trp_updb_action'] ) ]['version'] ) . '</p>';
                }
                $request['progress_message'] .= '<br>' . sprintf( $update_message_processing, sanitize_text_field( $_REQUEST['trp_updb_lang'] ) );//phpcs:ignore
			}
		}else{
			if ( !isset( $updates_needed[ $_REQUEST['trp_updb_action'] ] ) ){
				$this->stop_and_print_error( __('Update aborted! Incorrect action.', 'translatepress-multilingual' ) );
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
			$finished_with_language = call_user_func( $update_details['callback'], $language_code, $inferior_limit, $batch_size );

			if ( $finished_with_language ) {
				break;
			}else {
				$get_batch = $get_batch + 1;
			}
			$stop_time = microtime( true );
			$duration = $stop_time - $start_time;
		}
		if ( ! $finished_with_language ) {
			$request['trp_updb_batch'] = $get_batch;
		}


		if ( $finished_with_language ) {
			// finished with the current language
            $index = array_search( $language_code, $this->settings['translation-languages'] );

                if ( isset ( $this->settings['translation-languages'][ $index + 1 ] ) && (!isset($update_details['execute_only_once']) || $update_details['execute_only_once'] == false)) {
                        // next language code in array
                        $request['trp_updb_lang']    = $this->settings['translation-languages'][ $index + 1 ];
                        $request['progress_message'] .= __( ' done.', 'translatepress-multilingual' ) . '</br>';
                        $update_message_processing   = isset( $updates_needed[ $_REQUEST['trp_updb_action'] ]['message_processing'] ) ?
                            $updates_needed[ sanitize_text_field( $_REQUEST['trp_updb_action'] ) ]['message_processing']
                            : __( 'Processing table for language %s...', 'translatepress-multilingual' );
                        $request['progress_message'] .= '</br>' . sprintf( $update_message_processing, $request['trp_updb_lang'] );

                    } else {
                        // finish action due to completing all the translation languages
                        $request['progress_message'] .= __( ' done.', 'translatepress-multilingual' ) . '</br>';
                        $request['trp_updb_lang']    = '';
                        // this will stop showing the admin notice
                        update_option( $update_details['option_name'], 'yes' );
                        $request['trp_updb_action'] = '';
                    }
		}else{
			$request['trp_updb_lang'] = $language_code;
            $request['progress_message'] = '.';
		}

        if ( $this->db->last_error != '' ){
            $request['progress_message'] = '<p><strong>SQL Error:</strong> ' . esc_html($this->db->last_error) . '</p>' . $request['progress_message'];
        }
		$query_arguments = array(
			'action'                    => 'trp_update_database',
			'trp_updb_action'           => $request['trp_updb_action'],
			'trp_updb_lang'             => $request['trp_updb_lang'],
			'trp_updb_batch'            => $request['trp_updb_batch'],
			'trp_updb_nonce'            => wp_create_nonce('tpupdatedatabase'),
			'trp_update_completed'      => 'no',
			'progress_message'          => $request['progress_message']
		);
		echo( json_encode( $query_arguments ));
		wp_die();
	}

	public function stop_and_print_error( $error_message ){
		$back_to_settings_button = '<p><a href="' . site_url('wp-admin/options-general.php?page=translate-press') . '"> <input type="button" value="' . __('Back to TranslatePress Settings', 'translatepress-multilingual' ) . '" class="button-primary"></a></p>';
		$query_arguments = array(
			'trp_update_completed'      => 'yes',
			'progress_message'          => '<p><strong>' . $error_message . '</strong></strong></p>' . $back_to_settings_button
		);
		echo( json_encode( $query_arguments ));
		wp_die();
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
     * @return bool
     */
    public function trp_updated_database_original_id_insert_166( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $rows_inserted = $this->trp_query->original_ids_insert( $language_code, $inferior_limit, $batch_size );

        if ( $rows_inserted > 0 ){
            return false;
        }else{
            return true;
        }
    }

    public function trp_updated_database_original_id_cleanup_166( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $this->trp_query->original_ids_cleanup();

        return true;
    }

    /**
     * Normalize original ids for all dictionary entries
     *
     * @param string $language_code     Language code of the table
     * @param int $inferior_limit       Omit first X rows
     * @param int $batch_size           How many rows to query
     *
     * @return bool
     */
    public function trp_updated_database_original_id_update_166( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $rows_updated = $this->trp_query->original_ids_reindex( $language_code, $inferior_limit, $batch_size );

        if ( $rows_updated > 0 ){
            return false;
        }else {
            return true;
        }
    }


    public function trp_prepare_options_for_database_optimization(){
        if ( !current_user_can('manage_options') ){
            return;
        }

        $redirect = false;

        if(isset( $_GET['trp_rm_duplicates_gettext'] )){
            update_option('trp_remove_duplicate_gettext_rows', 'no');
            update_option('trp_remove_duplicate_untranslated_gettext_rows', 'no');
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
     * @return bool
     */
    public function trp_updated_database_gettext_original_id_insert( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }
        $gettext_normalization = $this->trp_query->get_query_component('gettext_normalization');
        $rows_inserted = $gettext_normalization->gettext_original_ids_insert( $language_code, $inferior_limit, $batch_size );
        $last_id = $this->trp_query->get_last_id( $this->trp_query->get_gettext_table_name($language_code) );

        if ( $inferior_limit + $batch_size <= $last_id ){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Removes possible duplicates from within gettext_original_strings table
     *
     * @param $language_code
     * @param $inferior_limit
     * @param $batch_size
     * @return bool
     */
    public function trp_updated_database_gettext_original_id_cleanup( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $gettext_normalization = $this->trp_query->get_query_component('gettext_normalization');
        $gettext_normalization->gettext_original_ids_cleanup();

        return true;
    }

    /**
     * Normalize original ids for all gettext entries
     *
     * @param string $language_code     Language code of the table
     * @param int $inferior_limit       Omit first X rows
     * @param int $batch_size           How many rows to query
     *
     * @return bool
     */
    public function trp_updated_database_gettext_original_id_update( $language_code, $inferior_limit, $batch_size ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            /* @var TRP_Query */
            $this->trp_query = $trp->get_component( 'query' );
        }

        $gettext_normalization = $this->trp_query->get_query_component('gettext_normalization');
        $rows_updated = $gettext_normalization->gettext_original_ids_reindex( $language_code, $inferior_limit, $batch_size );

        if ( $rows_updated > 0 ){
            return false;
        }else {
            return true;
        }
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

                $url_info = 'https://translatepress.com/docs/installation/upgrade-to-version-2-0-5-or-newer/';
                $url_account = 'https://translatepress.com/account/';
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

}
