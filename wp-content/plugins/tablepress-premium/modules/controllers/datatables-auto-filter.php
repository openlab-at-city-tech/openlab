<?php
/**
 * TablePress DataTables Automatic Filtering.
 *
 * @package TablePress
 * @subpackage DataTables
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the DataTables Automatic Filtering feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_DataTables_Auto_Filter {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Registers necessary plugin filter hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'tablepress_table_template', array( __CLASS__, 'add_option_to_table_template' ) );
		add_filter( 'tablepress_shortcode_table_default_shortcode_atts', array( __CLASS__, 'add_shortcode_parameters' ) );
		add_filter( 'tablepress_table_js_options', array( __CLASS__, 'pass_render_options_to_js_options' ), 10, 3 );
		add_filter( 'tablepress_datatables_parameters', array( __CLASS__, 'set_datatables_parameters' ), 10, 4 );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_block_editor_js' ) );
	}

	/**
	 * Adds options related to DataTables Automatic Filtering to the table template.
	 *
	 * @since 2.0.0
	 * @since 2.0.4 The `datatables_auto_filter_url_parameter` option was added.
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['datatables_auto_filter'] = '';
		$table['options']['datatables_auto_filter_url_parameter'] = '';
		return $table;
	}

	/**
	 * Adds parameters for the DataTables Automatic Filtering feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 * @since 2.0.4 The `datatables_auto_filter_url_parameter` parameter was added.
	 *
	 * @param array<string, mixed> $default_atts Default attributes for the TablePress [table /] Shortcode.
	 * @return array<string, mixed> Extended attributes for the Shortcode.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['datatables_auto_filter'] = null;
		$default_atts['datatables_auto_filter_url_parameter'] = null;
		return $default_atts;
	}

	/**
	 * Registers the module's JS script for the block editor.
	 *
	 * @since 2.0.4
	 */
	public static function enqueue_block_editor_js(): void {
		TablePress_Modules_Helper::enqueue_script( 'datatables-auto-filter-block' );
	}

	/**
	 * Passes the DataTables Automatic Filtering configuration from Shortcode parameters to JavaScript arguments.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $js_options     Current JS options.
	 * @param string               $table_id       Table ID.
	 * @param array<string, mixed> $render_options Render Options.
	 * @return array<string, mixed> Modified JS options.
	 */
	public static function pass_render_options_to_js_options( array $js_options, string $table_id, array $render_options ): array {
		// If given, use the URL filter parameter.
		if ( ! empty( $render_options['datatables_auto_filter_url_parameter'] ) ) {
			// Only allow characters a-z, A-Z, 0-9, _, and - in the URL parameter name.
			$render_options['datatables_auto_filter_url_parameter'] = (string) preg_replace( '#[^a-zA-Z0-9_-]#', '', $render_options['datatables_auto_filter_url_parameter'] );
			if ( ! empty( $_GET[ $render_options['datatables_auto_filter_url_parameter'] ] ) ) {
				// Sanitization of the $_GET parameter value happens in set_datatables_parameters().
				$render_options['datatables_auto_filter'] = $_GET[ $render_options['datatables_auto_filter_url_parameter'] ];
			}
		}

		$js_options['datatables_auto_filter'] = $render_options['datatables_auto_filter'];
		return $js_options;
	}

	/**
	 * Evaluates JS parameters and converts them to DataTables parameters.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $parameters DataTables parameters.
	 * @param string               $table_id   Table ID.
	 * @param string               $html_id    HTML ID of the table.
	 * @param array<string, mixed> $js_options JS options for DataTables.
	 * @return array<string, mixed> Extended DataTables parameters.
	 */
	public static function set_datatables_parameters( array $parameters, string $table_id, string $html_id, array $js_options ): array {
		if ( '' !== $js_options['datatables_auto_filter'] ) {
			// Add an option to the "search" value, if one is already set, otherwise set it.
			$search_sub_option = '"search":"' . esc_js( $js_options['datatables_auto_filter'] ) . '"';
			if ( isset( $parameters['search'] ) ) {
				$parameters['search'] = str_replace( '"search":{', "\"search\":{{$search_sub_option},", $parameters['search'] );
			} else {
				$parameters['search'] = "\"search\":{{$search_sub_option}}";
			}
		}
		return $parameters;
	}

} // class TablePress_Module_DataTables_Auto_Filter
