<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle import and export of KB configuration
 *
 * @copyright   Copyright (C) 2019, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Export_Import {
	
	private $message = array(); // error/warning/success messages
	//private $operation_log = array();
	private $add_ons_info = array(
										'Echo_Knowledge_Base' => 'epkb',
										'Echo_Advanced_Search' => 'asea',
										'Echo_Article_Rating_And_Feedback' => 'eprf', 
										'Echo_Elegant_Layouts' => 'elay',
										'Echo_Widgets' => 'widg',
										'Echo_Article_Features' => 'eart',
										// FUTURE DODO Links Editor and MKB
							);

	private $ignored_fields = array('id', 'status', 'kb_main_pages', 'kb_name', 'kb_articles_common_path','categories_in_url_enabled','wpml_is_enabled');

	/**
	 * Run export
	 * @param $kb_id
	 * return text message about error or stop script and show export file
	 * @return array
	 */
	public function download_export_file( $kb_id ) {

		if ( ! current_user_can('manage_options') ) {
			$this->message['error'] = esc_html__( 'Login or refresh this page to export KB configuration.', 'echo-knowledge-base' );
			return $this->message;
		}

		// export data and report error if an issue found
		$exported_data = $this->export_kb_config( $kb_id );
		if ( empty( $exported_data ) ) {
			return $this->message;
		}

		ignore_user_abort( true );
		
		if ( ! $this->is_function_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
			set_time_limit( 0 );
		}

		// phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=kb_' . $kb_id . '_config_export_' . date('Y_m_d_H_i_s') . '.json' );
		header( "Expires: 0" );

		echo wp_json_encode($exported_data);

		return [];
	}
	
	/**
	 * Export KB configuration.
	 *
	 * @param $kb_id
	 * @return null
	 */
	private function export_kb_config( $kb_id ) {

		$export_data = array();

		// process each plugin (KB core and add-ons)
		foreach ($this->add_ons_info as $add_on_class => $add_on_prefix) {

			if ( ! class_exists( $add_on_class ) ) {
				continue;
			}

			// retrieve plugin instance
			/** @var $plugin_instance Echo_Knowledge_Base */
			$plugin_instance = $this->get_plugin_instance( $add_on_prefix );
			if ( empty($plugin_instance) ) {
				return null;
			}

			// retrieve plugin configuration
			$add_on_config = $plugin_instance->kb_config_obj->get_kb_config( $kb_id, true );
			if ( is_wp_error( $add_on_config ) ) {
				$this->message['error'] = $add_on_config->get_error_message();
				return null;
			}
			if ( ! is_array($add_on_config) ) {
				$this->message['error'] = esc_html__( 'Found invalid data.', 'echo-knowledge-base' ) . ' (' . $add_on_prefix . ')';
				return null;
			}

			// remove protected fields
			foreach( $this->ignored_fields as $ignored_field ) {
				if ( isset($add_on_config[$ignored_field]) )  {
					unset($add_on_config[$ignored_field]);
				}
			}
			
			$export_data[$add_on_prefix] = $add_on_config;
			$export_data[$add_on_prefix]['plugin_version'] = $plugin_instance::$version;
		}

		if ( empty($export_data) ) {
			$this->message['error'] = 'E40'; // do not translate;
			return null;
		}

		return $export_data;
	}

	/**
	 * Import KB configuration from a file.
	 *
	 * @param $kb_id
	 * @return array|null
	 */
	public function import_kb_config( $kb_id ) {

		if ( ! current_user_can('manage_options') ) {
			$this->message['error'] = esc_html__( 'You do not have permission.', 'echo-knowledge-base' );
			return $this->message;
		}

		// sanitize the file name
		$sanitized_file_name = empty( $_FILES['import_file']['name'] ) ? '' : sanitize_file_name( $_FILES['import_file']['name'] );
		if ( empty( $sanitized_file_name ) ) {
			$this->message['error'] = esc_html__( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' (4)';
			return $this->message;
		}

		// only check if the file tmp name is set - sanitization for the temporary file name is not appropriate
		$safe_file_tmp_name = empty( $_FILES['import_file']['tmp_name'] ) ? '' : $_FILES['import_file']['tmp_name'];// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -> sanitized below
		if ( empty( $safe_file_tmp_name ) ) {
			$this->message['error'] = esc_html__( 'Import tmp file format is not correct.', 'echo-knowledge-base' ) . ' (4b)';
			return $this->message;
		}

		// check if the uploaded temporary file exists
		if ( ! file_exists( $safe_file_tmp_name ) ) {
			$this->message['error'] = esc_html__( 'Import tmp file format is not correct.', 'echo-knowledge-base' ) . ' (4c)';
			return $this->message;
		}

		// check if the uploaded temporary file is readable
		if ( ! is_readable( $safe_file_tmp_name ) ) {
			$this->message['error'] = esc_html__( 'Import tmp file format is not correct.', 'echo-knowledge-base' ) . ' (4d)';
			return $this->message;
		}

		// validate that the file was uploaded via HTTP POST
		if ( empty( is_uploaded_file( $safe_file_tmp_name ) ) ) {
			$this->message['error'] = esc_html__( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' (2)';
			return $this->message;
		}

		// check for upload errors
		$file_error = empty( $_FILES['import_file']['error'] ) ? '' : sanitize_text_field( $_FILES['import_file']['error'] );
		if ( ! empty( $file_error ) ) {
			$this->message['error'] = esc_html__( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' ' . $file_error . ' (3)';
			return $this->message;
		}

		// validate file extension
		if ( pathinfo( $sanitized_file_name, PATHINFO_EXTENSION ) !== 'json' ) {
			$this->message['error'] = esc_html__( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' (5)' . esc_html__( 'File', 'echo-knowledge-base' ) . ': ' . esc_html( $sanitized_file_name );
			return $this->message;
		}

		// retrieve content of the imported file
		//phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$import_data_file = file_get_contents( $safe_file_tmp_name );
		if ( empty( $import_data_file ) ) {
			$this->message['error'] = esc_html__( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' (7)';
			return $this->message;
		}

		// validate the file is JSON and imported data is an array
		$import_data = json_decode( $import_data_file, true );
		if ( json_last_error() !== JSON_ERROR_NONE || empty( $import_data ) || ! is_array( $import_data ) ) {
			$this->message['error'] = esc_html__( 'Import file format is not correct.', 'echo-knowledge-base' ) . ' ' . json_last_error_msg() . ' (8)';
			return $this->message;
		}

		// KB Core needs to be present
		if ( ! isset( $import_data['epkb'] ) ) {
			$this->message['error'] = esc_html__( 'Knowledge Base data is missing', 'echo-knowledge-base' );
			return $this->message;
		}

		// process each plugin (KB core and add-ons)
		foreach ( $this->add_ons_info as $add_on_class => $add_on_prefix ) {

			$plugin_name = $this->get_plugin_name( $add_on_class );
			
			// add-on is installed but not active and no data is present in import for the add-on
			if ( empty( $import_data[$add_on_prefix]) && ! class_exists( $add_on_class ) ) {
				continue;
			}
			
			// import data exists but plugin is not active
			if ( isset( $import_data[$add_on_prefix] ) && ! class_exists( $add_on_class ) ) {
				$this->message['error'] = esc_html__( 'Import failed because found import data for a plugin that is not active: ', 'echo-knowledge-base' ) . $plugin_name;
				return $this->message;
			}

			// plugin is active but import data does not exist
			if ( ! isset( $import_data[$add_on_prefix] ) && class_exists( $add_on_class ) ) {
				/* OK to import less $this->message['error'] = esc_html__( 'Import failed because found a plugin that is active with no corresponding import data: ', 'echo-knowledge-base' ) . $plugin_name;
				return $this->message; */
				continue;
			}

			// ensure imported data have correct format
			if ( ! is_array( $import_data[$add_on_prefix] ) ) {
				$this->message['error'] = esc_html__( 'Import failed because found invalid data.', 'echo-knowledge-base' ) . ' (' . $plugin_name . ')';
				return $this->message;
			}

			// verify most data is preset
			$specs_class_name = strtoupper( $add_on_prefix ) . '_KB_Config_Specs';
			if ( ! class_exists($specs_class_name) || ! method_exists( $specs_class_name, 'get_specs_item_names' ) ) {
				$this->message['error'] = 'E34 (' . $plugin_name . ')'; // do not translate
				return $this->message;
			}

			$add_on_config = $import_data[$add_on_prefix];

			// check if we need to upgrade data
			$add_on_config['id'] = $kb_id;
			$this->upgrade_plugin_data( $add_on_prefix, $add_on_config );

			/** @var $specs_class_name EPKB_KB_Config_Specs */
			$specs_found = 0;
			$specs_not_found = 0;
			$fields_specification = $specs_class_name::get_specs_item_names();
			foreach( $fields_specification as $key ) {
				if ( isset( $add_on_config[$key] ) ) {
					$specs_found++;
				} else {
					$specs_not_found++;
				}
			}

			// validate imported data
			if ( $specs_found == 0 || $specs_not_found > $specs_found ) {
				$this->message['error'] = esc_html__( "Found invalid data.", 'echo-knowledge-base' ) . ' (' . $plugin_name . ',' . $specs_found . ',' . $specs_not_found . ')';
				return $this->message;
			}

			// retrieve plugin instance
			/** @var $plugin_instance Echo_Knowledge_Base */
			$plugin_instance = $this->get_plugin_instance( $add_on_prefix );
			if ( empty( $plugin_instance ) ) {
				$this->message['error'] =  esc_html__( 'Import failed', 'echo-knowledge-base' );
				return $this->message;
			}

			// for KB Core, Main and Article Page could have Elegant layout, so we need it enabled
			if ( $add_on_prefix == 'epkb' && in_array( $add_on_config['kb_main_page_layout'], [ EPKB_Layout::GRID_LAYOUT, EPKB_Layout::SIDEBAR_LAYOUT ] ) && ! EPKB_Utilities::is_elegant_layouts_enabled() ) {
				$layout = sanitize_text_field( $add_on_config['kb_main_page_layout'] );
				$this->message['error'] = esc_html__( "Elegant Layouts needs to be active.", 'echo-knowledge-base' ) . ' (' . esc_html( $layout ) . ')';
				return $this->message;
			}

			// remove protected fields
			foreach( $this->ignored_fields as $ignored_field ) {
				if ( isset( $add_on_config[$ignored_field] ) )  {
					unset( $add_on_config[$ignored_field] );
				}
			}
			
			$orig_config = $plugin_instance->kb_config_obj->get_kb_config( $kb_id, true );
			if ( is_wp_error( $orig_config ) ) {
				$this->message['error'] =  'E31 (' . $plugin_name . ')' . $orig_config->get_error_message();  // do not translate
				return $this->message;
			}

			$add_on_config = array_merge( $orig_config, $add_on_config);
			
			// update add-on configuration
			$add_on_config = $plugin_instance->kb_config_obj->update_kb_configuration( $kb_id, $add_on_config );
			/** @var $add_on_config WP_Error */
			if ( is_wp_error($add_on_config) ) {
				$this->message['error'] =  'E36 (' . $plugin_name . ')' . $add_on_config->get_error_message();  // do not translate
				return $this->message;
			}
		}
		
		$this->message['success'] =  esc_html__( 'Import finished successfully', 'echo-knowledge-base' );
		
		return $this->message;
	}

	private function upgrade_plugin_data( $add_on_prefix, &$plugin_config ) {

		$import_plugin_version = empty($plugin_config['plugin_version']) ? '' : $plugin_config['plugin_version'];

		switch ( $add_on_prefix ) {

			case 'epkb':
				$last_version = empty($import_plugin_version) ? '6.9.9' : $import_plugin_version;
				if ( $last_version != Echo_Knowledge_Base::$version ) {
					EPKB_Upgrades::run_upgrades( $plugin_config, $last_version );
				}
				break;

			case 'asea':
				$last_version = empty($import_plugin_version) ? '2.13.9' : $import_plugin_version;
				if ( class_exists('Echo_Advanced_Search') && $last_version != Echo_Advanced_Search::$version && class_exists('ASEA_Upgrades') && is_callable(array('ASEA_Upgrades', 'run_upgrade')) ) {
					ASEA_Upgrades::run_upgrade( $plugin_config, $last_version );
				}
				break;

			case 'elay':
				$last_version = empty($import_plugin_version) ? '2.5.4' : $import_plugin_version;
				if ( class_exists('Echo_Elegant_Layouts') && $last_version != Echo_Elegant_Layouts::$version && class_exists('ELAY_Upgrades') && is_callable(array('ELAY_Upgrades', 'run_upgrade')) ) {
					ELAY_Upgrades::run_upgrade( $plugin_config, $last_version );
				}
				break;

			case 'eprf':
				$last_version = empty($import_plugin_version) ? '1.4.0' : $import_plugin_version;
				if ( class_exists('Echo_Article_Rating_And_Feedback') && $last_version != Echo_Article_Rating_And_Feedback::$version && class_exists('EPRF_Upgrades') && is_callable(array('EPRF_Upgrades', 'run_upgrade')) ) {
					EPRF_Upgrades::run_upgrade( $plugin_config, $last_version );
				}
				break;

		}
	}

	/**
	 * Call function to get/save add_on configuration
	 * @param $prefix
	 * @return null on error (and set error message) or valid DB object
	 */
	private function get_plugin_instance( $prefix ) {

		if ( ! in_array( $prefix, $this->add_ons_info ) ) {
			$this->message['error'] = 'E37 (' . $prefix . ')'; // do not translate
			return null;
		}

		// get function
		$add_on_function_name = $prefix . '_get_instance';
		if ( ! function_exists($add_on_function_name) ) {
			$this->message['error'] = 'E38 (' . $add_on_function_name . ')'; // do not translate
			return null;
		}

		// get DB class instance
		$instance = call_user_func($add_on_function_name);
		if ( is_object($instance) ) {
			return $instance;
		}

		$plugin_name = array_flip($this->add_ons_info);
		$plugin_name = isset($plugin_name[$prefix]) ? $this->get_plugin_name($plugin_name[$prefix]) : 'Unknown plugin';

		$this->message['error'] = $plugin_name . ' - ' . esc_html__( 'is the plugin active?', 'echo-knowledge-base' );

		return null;
	}

	private function get_plugin_name( $add_on_class_name ) {
		return str_replace('_', ' ', $add_on_class_name);
	}

	/**
	 * Checks whether function is disabled.
	 * @param $function
	 * @return bool
	 */
	private function is_function_disabled( $function ) {
		$disabled = explode( ',',  ini_get( 'disable_functions' ) );
		return in_array( $function, $disabled );
	}

	/**
	 * Add JSON as allowed mime type for configuration import
	 * @param $allowed_mimes
	 * @return mixed
	 */
	public function add_config_import_mimes( $allowed_mimes ) {
		if ( empty( $allowed_mimes['json'] ) ) {
			$allowed_mimes['json'] = 'application/json';
		}
		return $allowed_mimes;
	}
}