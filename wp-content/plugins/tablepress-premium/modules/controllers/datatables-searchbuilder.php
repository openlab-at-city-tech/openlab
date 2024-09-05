<?php
/**
 * TablePress DataTables SearchBuilder.
 *
 * @package TablePress
 * @subpackage DataTables
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the DataTables SearchBuilder feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_DataTables_SearchBuilder {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Registers necessary plugin filter hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_filter( 'tablepress_view_data', array( __CLASS__, 'add_edit_screen_elements' ), 10, 2 );
		}

		add_filter( 'tablepress_table_template', array( __CLASS__, 'add_option_to_table_template' ) );
		add_filter( 'tablepress_shortcode_table_default_shortcode_atts', array( __CLASS__, 'add_shortcode_parameters' ) );
		add_filter( 'tablepress_table_js_options', array( __CLASS__, 'pass_render_options_to_js_options' ), 10, 3 );
		add_filter( 'tablepress_datatables_parameters', array( __CLASS__, 'set_datatables_parameters' ), 10, 4 );
		add_filter( 'tablepress_datatables_language_strings', array( __CLASS__, 'add_datatables_language_strings' ), 9, 2 ); // Run at priority 9 so that overriding is easier on default priority.

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_css_files' ) );
		}
	}

	/**
	 * Adds options related to DataTables SearchBuilder to the table template.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['datatables_searchbuilder'] = false;
		return $table;
	}

	/**
	 * Registers "Edit" screen elements for the "DataTables SearchBuilder" feature.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data   Data for this screen.
	 * @param string               $action Action for this screen.
	 * @return array<string, mixed> Modified data for this screen.
	 */
	public static function add_edit_screen_elements( array $data, string $action ): array {
		if ( 'edit' === $action ) {
			// Add a meta box below the default meta boxes, by using the "low" priority.
			add_meta_box( 'tablepress_edit-datatables-searchbuilder', __( 'Custom Search Builder', 'tablepress' ), array( __CLASS__, 'postbox_datatables_searchbuilder' ), null, 'normal', 'low' );

			TablePress_Modules_Helper::enqueue_script( 'datatables-searchbuilder' );
			add_filter( 'tablepress_admin_page_script_dependencies', array( __CLASS__, 'add_script_dependencies' ), 10, 2 );
		}
		return $data;
	}

	/**
	 * Adds DataTables SearchBuilder script as a dependency for the "Edit" script, so that hooks are added before they are executed.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $dependencies List of the dependencies that the $name script relies on.
	 * @param string   $name         Name of the JS script, without extension.
	 * @return string[] Modified list of the dependencies that the $name script relies on.
	 */
	public static function add_script_dependencies( array $dependencies, string $name ): array {
		if ( 'edit' === $name ) {
			$dependencies[] = 'tablepress-datatables-searchbuilder';
		}
		return $dependencies;
	}

	/**
	 * Prints the content of the "DataTables SearchBuilder" post meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public static function postbox_datatables_searchbuilder( array $data, array $box ): void {
		$help_box_content = '';
		self::print_help_box_markup( $help_box_content );
		?>
		<p id="notice-datatables-searchbuilder-requirements"><em><?php printf( __( 'This feature is only available when the “%1$s”, “%2$s”, and “%3$s” checkboxes in the “%4$s” and “%5$s” sections are checked.', 'tablepress' ), __( 'Table Head Row', 'tablepress' ), __( 'Enable Visitor Features', 'tablepress' ), __( 'Search/Filtering', 'tablepress' ), __( 'Table Options', 'tablepress' ), __( 'Table Features for Site Visitors', 'tablepress' ) ); ?></em></p>
		<p id="notice-datatables-searchbuilder-conflict-datatables-serverside-processing"><em><?php printf( __( 'This feature is only available when the “%1$s” feature is turned off.', 'tablepress' ), __( 'Server-side Processing', 'tablepress' ) ); ?></em></p>
		<p><label for="option-datatables_searchbuilder"><input type="checkbox" name="datatables_searchbuilder" id="option-datatables_searchbuilder"> <?php _e( 'Show a search builder interface for filtering from groups and using conditions.', 'tablepress' ); ?></label></p>
		<?php
	}

	/**
	 * Adds parameters for the DataTables SearchBuilder feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $default_atts Default attributes for the TablePress [table /] Shortcode.
	 * @return array<string, mixed> Extended attributes for the Shortcode.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['datatables_searchbuilder'] = null;
		return $default_atts;
	}

	/**
	 * Passes the DataTables SearchBuilder configuration from Shortcode parameters to JavaScript arguments.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $js_options     Current JS options.
	 * @param string               $table_id       Table ID.
	 * @param array<string, mixed> $render_options Render Options.
	 * @return array<string, mixed> Modified JS options.
	 */
	public static function pass_render_options_to_js_options( array $js_options, string $table_id, array $render_options ): array {
		$js_options['datatables_searchbuilder'] = $render_options['datatables_searchbuilder'];

		// Custom Search Builder is not supported with Server-side Processing.
		if ( isset( $render_options['datatables_serverside_processing'] ) && $render_options['datatables_serverside_processing'] ) {
			$js_options['datatables_searchbuilder'] = false;
		}

		if ( false !== $js_options['datatables_searchbuilder'] ) {
			$js_url = plugins_url( 'modules/js/datatables.datetime.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-datetime', $js_url, array( 'tablepress-datatables' ), TablePress::version, true );
			$js_url = plugins_url( 'modules/js/datatables.searchbuilder.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-searchbuilder', $js_url, array( 'tablepress-datatables', 'tablepress-datatables-datetime' ), TablePress::version, true );
		}

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
		if ( ! empty( $js_options['datatables_searchbuilder'] ) ) {
			// Prepend "Q" to the "dom" value, if one is already set, otherwise use the default.
			if ( isset( $parameters['dom'] ) ) {
				$parameters['dom'] = str_replace( ':"', ':"Q', $parameters['dom'] );
			} else {
				$parameters['dom'] = '"dom":"Qlfrtip"';
			}
		}
		return $parameters;
	}

	/**
	 * Adds strings that the module uses on the frontend to the DataTables language array.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, string|mixed[]> $datatables_strings The language strings for DataTables.
	 * @param string                        $datatables_locale  Current locale/language for the DataTables JS library.
	 * @return array<string, string|mixed[]> Extended array of strings for DataTables.
	 */
	public static function add_datatables_language_strings( array $datatables_strings, string $datatables_locale ): array {
		if ( 'en_US' === $datatables_locale ) {
			return $datatables_strings;
		}

		TablePress_Modules_Loader::load_language_file();

		$new_strings = array(
			'searchBuilder' => array(
				'add'         => _x( 'Add Condition', 'SearchBuilder module', 'tablepress' ),
				'button'      => array(
					'0' => _x( 'Search Builder', 'SearchBuilder module', 'tablepress' ),
					'_' => _x( 'Search Builder (%d)', 'SearchBuilder module', 'tablepress' ),
				),
				'clearAll'    => _x( 'Clear All', 'SearchBuilder module', 'tablepress' ),
				'condition'   => _x( 'Condition', 'SearchBuilder module', 'tablepress' ),
				'conditions'  => array(
					'array'  => array(
						'contains' => _x( 'Contains', 'SearchBuilder module', 'tablepress' ),
						'empty'    => _x( 'Empty', 'SearchBuilder module', 'tablepress' ),
						'equals'   => _x( 'Equals', 'SearchBuilder module', 'tablepress' ),
						'not'      => _x( 'Not', 'SearchBuilder module', 'tablepress' ),
						'notEmpty' => _x( 'Not Empty', 'SearchBuilder module', 'tablepress' ),
						'without'  => _x( 'Without', 'SearchBuilder module', 'tablepress' ),
					),
					'date'   => array(
						'after'      => _x( 'After', 'SearchBuilder module', 'tablepress' ),
						'before'     => _x( 'Before', 'SearchBuilder module', 'tablepress' ),
						'between'    => _x( 'Between', 'SearchBuilder module', 'tablepress' ),
						'empty'      => _x( 'Empty', 'SearchBuilder module', 'tablepress' ),
						'equals'     => _x( 'Equals', 'SearchBuilder module', 'tablepress' ),
						'not'        => _x( 'Not', 'SearchBuilder module', 'tablepress' ),
						'notBetween' => _x( 'Not Between', 'SearchBuilder module', 'tablepress' ),
						'notEmpty'   => _x( 'Not Empty', 'SearchBuilder module', 'tablepress' ),
					),
					'number' => array(
						'between'    => _x( 'Between', 'SearchBuilder module', 'tablepress' ),
						'empty'      => _x( 'Empty', 'SearchBuilder module', 'tablepress' ),
						'equals'     => _x( 'Equals', 'SearchBuilder module', 'tablepress' ),
						'gt'         => _x( 'Greater Than', 'SearchBuilder module', 'tablepress' ),
						'gte'        => _x( 'Greater Than Equal To', 'SearchBuilder module', 'tablepress' ),
						'lt'         => _x( 'Less Than', 'SearchBuilder module', 'tablepress' ),
						'lte'        => _x( 'Less Than Equal To', 'SearchBuilder module', 'tablepress' ),
						'not'        => _x( 'Not', 'SearchBuilder module', 'tablepress' ),
						'notBetween' => _x( 'Not Between', 'SearchBuilder module', 'tablepress' ),
						'notEmpty'   => _x( 'Not Empty', 'SearchBuilder module', 'tablepress' ),
					),
					'string' => array(
						'contains'      => _x( 'Contains', 'SearchBuilder module', 'tablepress' ),
						'empty'         => _x( 'Empty', 'SearchBuilder module', 'tablepress' ),
						'endsWith'      => _x( 'Ends With', 'SearchBuilder module', 'tablepress' ),
						'equals'        => _x( 'Equals', 'SearchBuilder module', 'tablepress' ),
						'not'           => _x( 'Not', 'SearchBuilder module', 'tablepress' ),
						'notContains'   => _x( 'Does Not Contain', 'SearchBuilder module', 'tablepress' ),
						'notEmpty'      => _x( 'Not Empty', 'SearchBuilder module', 'tablepress' ),
						'notEndsWith'   => _x( 'Does Not End With', 'SearchBuilder module', 'tablepress' ),
						'notStartsWith' => _x( 'Does Not Start With', 'SearchBuilder module', 'tablepress' ),
						'startsWith'    => _x( 'Starts With', 'SearchBuilder module', 'tablepress' ),
					),
				),
				'data'        => _x( 'Data', 'SearchBuilder module', 'tablepress' ),
				'delete'      => _x( '&times;', 'SearchBuilder module', 'tablepress' ),
				'deleteTitle' => _x( 'Delete filtering rule', 'SearchBuilder module', 'tablepress' ),
				'left'        => _x( '<', 'SearchBuilder module', 'tablepress' ),
				'leftTitle'   => _x( 'Outdent criteria', 'SearchBuilder module', 'tablepress' ),
				'logicAnd'    => _x( 'And', 'SearchBuilder module', 'tablepress' ),
				'logicOr'     => _x( 'Or', 'SearchBuilder module', 'tablepress' ),
				'right'       => _x( '>', 'SearchBuilder module', 'tablepress' ),
				'rightTitle'  => _x( 'Indent criteria', 'SearchBuilder module', 'tablepress' ),
				'title'       => array(
					'0' => _x( 'Custom Search Builder', 'SearchBuilder module', 'tablepress' ),
					'_' => _x( 'Custom Search Builder (%d)', 'SearchBuilder module', 'tablepress' ),
				),
				'value'       => _x( 'Value', 'SearchBuilder module', 'tablepress' ),
				'valueJoiner' => _x( 'and', 'SearchBuilder module', 'tablepress' ),
			),
			'datetime'      => array(
				'clear'    => _x( 'Clear', 'SearchBuilder module', 'tablepress' ),
				'previous' => _x( 'Previous', 'SearchBuilder module', 'tablepress' ),
				'next'     => _x( 'Next', 'SearchBuilder module', 'tablepress' ),
				'months'   => array(
					_x( 'January', 'SearchBuilder module', 'tablepress' ),
					_x( 'February', 'SearchBuilder module', 'tablepress' ),
					_x( 'March', 'SearchBuilder module', 'tablepress' ),
					_x( 'April', 'SearchBuilder module', 'tablepress' ),
					_x( 'May', 'SearchBuilder module', 'tablepress' ),
					_x( 'June', 'SearchBuilder module', 'tablepress' ),
					_x( 'July', 'SearchBuilder module', 'tablepress' ),
					_x( 'August', 'SearchBuilder module', 'tablepress' ),
					_x( 'September', 'SearchBuilder module', 'tablepress' ),
					_x( 'October', 'SearchBuilder module', 'tablepress' ),
					_x( 'November', 'SearchBuilder module', 'tablepress' ),
					_x( 'December', 'SearchBuilder module', 'tablepress' ),
				),
				'weekdays' => array(
					_x( 'Sun', 'SearchBuilder module', 'tablepress' ),
					_x( 'Mon', 'SearchBuilder module', 'tablepress' ),
					_x( 'Tue', 'SearchBuilder module', 'tablepress' ),
					_x( 'Wed', 'SearchBuilder module', 'tablepress' ),
					_x( 'Thu', 'SearchBuilder module', 'tablepress' ),
					_x( 'Fri', 'SearchBuilder module', 'tablepress' ),
					_x( 'Sat', 'SearchBuilder module', 'tablepress' ),
				),
				'amPm'     => array(
					_x( 'am', 'SearchBuilder module', 'tablepress' ),
					_x( 'pm', 'SearchBuilder module', 'tablepress' ),
				),
				'hours'    => _x( 'Hour', 'SearchBuilder module', 'tablepress' ),
				'minutes'  => _x( 'Minute', 'SearchBuilder module', 'tablepress' ),
				'seconds'  => _x( 'Second', 'SearchBuilder module', 'tablepress' ),
				'unknown'  => _x( '-', 'SearchBuilder module', 'tablepress' ),
				'today'    => _x( 'Today', 'SearchBuilder module', 'tablepress' ),
			),
		);
		// Merge existing strings into the new strings, so that existing translations are not lost.
		$datatables_strings = array_replace_recursive( $new_strings, $datatables_strings );

		return $datatables_strings;
	}

	/**
	 * Enqueues CSS files for the DataTables SearchBuilder module.
	 *
	 * @since 2.0.0
	 */
	public static function enqueue_css_files(): void {
		/** This filter is documented in modules/controllers/datatables-alphabetsearch.php */
		if ( ! apply_filters( 'tablepress_module_enqueue_css_files', true, self::$module['slug'] ) ) {
			return;
		}

		$css_url = plugins_url( 'modules/css/build/datatables.datetime.css', TABLEPRESS__FILE__ );
		wp_enqueue_style( 'tablepress-datatables-datetime', $css_url, array( 'tablepress-default' ), TablePress::version );
		$css_url = plugins_url( 'modules/css/build/datatables.searchbuilder.css', TABLEPRESS__FILE__ );
		wp_enqueue_style( 'tablepress-datatables-searchbuilder', $css_url, array( 'tablepress-default', 'tablepress-datatables-datetime' ), TablePress::version );
	}

} // class TablePress_Module_DataTables_SearchBuilder
