<?php
/**
 * TablePress Automatic Table Export.
 *
 * @package TablePress
 * @subpackage Automatic Table Export
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the Automatic Table Export feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_Automatic_Table_Export {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Automatic Table Export configuration option.
	 *
	 * @since 2.0.0
	 * @var TablePress_WP_Option
	 */
	protected static $auto_export_config;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		self::init_automatic_table_export();

		if ( is_admin() ) {
			self::init_admin();
		}
	}

	/**
	 * Loads the export configuration.
	 *
	 * @since 2.0.0
	 */
	public static function load_configuration(): void {
		$params = array(
			'option_name'   => 'tablepress_auto_export_config',
			'default_value' => array(),
		);
		self::$auto_export_config = TablePress::load_class( 'TablePress_WP_Option', 'class-wp_option.php', 'classes', $params );
	}

	/**
	 * Inits the Automatic Table Export module.
	 *
	 * @since 2.0.0
	 */
	public static function init_automatic_table_export(): void {
		add_action( 'tablepress_event_saved_table', array( __CLASS__, 'perform_automatic_export' ) );
	}

	/**
	 * Exports and saves a table to a file on the server.
	 *
	 * The exported file is saved to <path>/<table-id>-<table-name>.<export-format>.
	 *
	 * @since 2.0.0
	 *
	 * @param string $table_id Table ID.
	 */
	public static function perform_automatic_export( string $table_id ): void {
		self::load_configuration();

		// Bail, if the automatic table export is inactive.
		if ( ! self::$auto_export_config->get( 'active', false ) ) {
			return;
		}

		// Load table, with table data, options, and visibility settings.
		$table = TablePress::$model_table->load( $table_id, true, true );

		// Bail, if the table could not be loaded or is corrupted.
		if ( is_wp_error( $table ) || ( isset( $table['is_corrupted'] ) && $table['is_corrupted'] ) ) {
			return;
		}

		$path = self::$auto_export_config->get( 'path', '' );
		// Bail, if no path is given.
		if ( '' === $path ) {
			return;
		}

		$path = trailingslashit( $path );
		if ( ! is_dir( $path ) ) {
			mkdir( $path, 0777, true );
		}

		// If for some reason no export format is configured, default to CSV with , as the delimiter.
		$export_formats = self::$auto_export_config->get( 'formats', array( 'csv' ) );
		$csv_delimiter = self::$auto_export_config->get( 'csv_delimiter', ',' );

		$table_name = $table['id'] . '-' . sanitize_title_with_dashes( $table['name'] );

		$exporter = TablePress::load_class( 'TablePress_Export', 'class-export.php', 'classes' );

		// Export the table to all configured formats.
		foreach ( $export_formats as $export_format ) {
			$exported_table = $exporter->export_table( $table, $export_format, $csv_delimiter );

			$filename = "{$table_name}.{$export_format}";
			$filename = $path . sanitize_file_name( $filename );

			file_put_contents( $filename, $exported_table );
		}
	}

	/**
	 * Initializes the admin screens of the Automatic Table Export module.
	 *
	 * @since 2.0.0
	 */
	public static function init_admin(): void {
		if ( current_user_can( 'manage_options' ) ) {
			add_filter( 'tablepress_load_file_full_path', array( __CLASS__, 'change_export_view_full_path' ), 10, 3 );
			add_filter( 'tablepress_load_class_name', array( __CLASS__, 'change_view_export_class_name' ) );
			add_filter( 'tablepress_view_data', array( __CLASS__, 'add_automatic_table_export_view_data' ), 10, 2 );
			add_action( 'wp_ajax_tablepress_export', array( __CLASS__, 'handle_ajax_action_automatic_table_export' ) );
		}
	}

	/**
	 * Loads the Automatic Table Export view class when the TablePress Export view class is loaded.
	 *
	 * @since 2.0.0
	 *
	 * @param string $full_path Full path of the class file.
	 * @param string $file      File name of the class file.
	 * @param string $folder    Folder name of the class file.
	 * @return string Modified full path.
	 */
	public static function change_export_view_full_path( string $full_path, string $file, string $folder ): string {
		if ( 'view-export.php' === $file ) {
			require_once $full_path; // Load desired file first, as we inherit from it in the new $full_path file.
			$full_path = TABLEPRESS_ABSPATH . 'modules/views/view-automatic_table_export.php';
		}
		return $full_path;
	}

	/**
	 * Changes Export View class name, to load extended view.
	 *
	 * @since 2.0.0
	 *
	 * @param string $class_name Name of the class that shall be loaded.
	 * @return string Changed class name.
	 */
	public static function change_view_export_class_name( string $class_name ): string {
		if ( 'TablePress_Export_View' === $class_name ) {
			$class_name = 'TablePress_Automatic_Table_Export_View';
		}
		return $class_name;
	}

	/**
	 * Adds the view data for the Automatic Table Export view.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data   Data for this screen.
	 * @param string               $action Action for this screen.
	 * @return array<string, mixed> Modified data for this screen.
	 */
	public static function add_automatic_table_export_view_data( array $data, string $action ): array {
		if ( 'export' !== $action ) {
			return $data;
		}

		self::load_configuration();

		$data['auto_export_active'] = self::$auto_export_config->get( 'active', false );
		$data['auto_export_path'] = self::$auto_export_config->get( 'path', WP_CONTENT_DIR );
		$data['auto_export_formats'] = self::$auto_export_config->get( 'formats', array( 'csv' ) );
		$data['auto_export_csv_delimiter'] = self::$auto_export_config->get( 'csv_delimiter', ',' );

		return $data;
	}

	/**
	 * Saves the Automatic Table Export configuration.
	 *
	 * @since 2.0.0
	 */
	public static function handle_ajax_action_automatic_table_export(): void {
		if ( empty( $_POST['tablepress'] ) ) {
			wp_die( '-1' );
		}

		// Check if the submitted nonce matches the generated nonce we created earlier, dies -1 on failure.
		TablePress::check_nonce( 'export', false, '_ajax_nonce', true );

		// Ignore the request if the current user doesn't have sufficient permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( '-1' );
		}

		$auto_export_config = wp_unslash( $_POST['tablepress'] );
		$auto_export_config = json_decode( $auto_export_config, true );

		// Check if JSON could be decoded.
		if ( is_null( $auto_export_config ) ) {
			wp_die( '-1' );
		}

		// Specifically cast to an array again.
		$auto_export_config = (array) $auto_export_config;

		if ( ! isset( $auto_export_config['active'], $auto_export_config['path'], $auto_export_config['selectedFormats'], $auto_export_config['csvDelimiter'] ) || ! is_array( $auto_export_config['selectedFormats'] ) ) {
			wp_die( '-1' );
		}

		$configuration = array(
			'active'        => $auto_export_config['active'],
			'path'          => $auto_export_config['path'],
			'formats'       => array(),
			'csv_delimiter' => $auto_export_config['csvDelimiter'],
		);

		$allowed_formats = array( 'csv', 'json', 'html' );
		foreach ( $auto_export_config['selectedFormats'] as $format ) {
			if ( in_array( $format, $allowed_formats, true ) ) {
				$configuration['formats'][] = $format;
			}
		}

		self::load_configuration();
		self::$auto_export_config->update( $configuration );

		$response = array(
			'success' => true,
			'message' => 'success_save',
		);
		// Buffer all outputs, to prevent errors/warnings being printed that make the JSON invalid.
		$output_buffer = ob_get_clean();
		if ( ! empty( $output_buffer ) ) {
			$response['output_buffer'] = $output_buffer;
		}

		// Send the response.
		wp_send_json( $response );
	}

} // class TablePress_Module_Automatic_Table_Export
