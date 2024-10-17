<?php
/**
 * TablePress DataTables RowGroup.
 *
 * @package TablePress
 * @subpackage DataTables
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the DataTables RowGroup feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_DataTables_RowGroup {
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

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_css_files' ) );
		}
	}

	/**
	 * Adds options related to DataTables RowGroup to the table template.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['datatables_rowgroup'] = false;
		$table['options']['datatables_rowgroup_datasrc'] = '1';
		return $table;
	}

	/**
	 * Registers "Edit" screen elements for the "DataTables RowGroup" feature.
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
			add_meta_box( 'tablepress_edit-datatables-rowgroup', __( 'Row Grouping', 'tablepress' ), array( __CLASS__, 'postbox_datatables_rowgroup' ), null, 'normal', 'low' );

			TablePress_Modules_Helper::enqueue_script( 'datatables-rowgroup' );
			add_filter( 'tablepress_admin_page_script_dependencies', array( __CLASS__, 'add_script_dependencies' ), 10, 2 );
		}
		return $data;
	}

	/**
	 * Adds DataTables RowGroup script as a dependency for the "Edit" script, so that hooks are added before they are executed.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $dependencies List of the dependencies that the $name script relies on.
	 * @param string   $name         Name of the JS script, without extension.
	 * @return string[] Modified list of the dependencies that the $name script relies on.
	 */
	public static function add_script_dependencies( array $dependencies, string $name ): array {
		if ( 'edit' === $name ) {
			$dependencies[] = 'tablepress-datatables-rowgroup';
		}
		return $dependencies;
	}

	/**
	 * Prints the content of the "DataTables RowGroup" post meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public static function postbox_datatables_rowgroup( array $data, array $box ): void {
		$help_box_content = '';
		self::print_help_box_markup( $help_box_content );
		?>
		<p id="notice-datatables-rowgroup-requirements"><em><?php printf( __( 'This feature is only available when the “%1$s” and “%2$s” checkboxes in the “%3$s” and “%4$s” sections are checked.', 'tablepress' ), __( 'Table Head Row', 'tablepress' ), __( 'Enable Visitor Features', 'tablepress' ), __( 'Table Options', 'tablepress' ), __( 'Table Features for Site Visitors', 'tablepress' ) ); ?></em></p>
		<p><label for="option-datatables_rowgroup"><input type="checkbox" name="datatables_rowgroup" id="option-datatables_rowgroup"> <?php _e( 'Group rows that belong to the same category.', 'tablepress' ); ?></label></p>
		<details id="tablepress-datatables_rowgroup-advanced-settings">
			<summary><?php _e( 'Advanced settings', 'tablepress' ); ?></summary>
			<div>
				<label for="option-datatables_rowgroup_datasrc"><?php printf( __( 'Use these columns as group categories: %s', 'tablepress' ), '<input type="text" name="datatables_rowgroup_datasrc" id="option-datatables_rowgroup_datasrc" class="small-text" title="' . esc_attr__( 'This field can only contain letters, numbers, commas, spaces, and hyphens (-).', 'tablepress' ) . '" pattern="[0-9A-Z, -]*" required>' ); ?></label>
			</div>
		</details>
		<?php
	}

	/**
	 * Adds parameters for the DataTables RowGroup feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $default_atts Default attributes for the TablePress [table /] Shortcode.
	 * @return array<string, mixed> Extended attributes for the Shortcode.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['datatables_rowgroup'] = null;
		$default_atts['datatables_rowgroup_datasrc'] = null;
		return $default_atts;
	}

	/**
	 * Passes the DataTables RowGroup configuration from Shortcode parameters to JavaScript arguments.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $js_options     Current JS options.
	 * @param string               $table_id       Table ID.
	 * @param array<string, mixed> $render_options Render Options.
	 * @return array<string, mixed> Modified JS options.
	 */
	public static function pass_render_options_to_js_options( array $js_options, string $table_id, array $render_options ): array {
		$js_options['datatables_rowgroup'] = $render_options['datatables_rowgroup'];
		$js_options['datatables_rowgroup_datasrc'] = $render_options['datatables_rowgroup_datasrc'];

		if ( false !== $js_options['datatables_rowgroup'] ) {
			$js_url = plugins_url( 'modules/js/datatables.rowgroup.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-rowgroup', $js_url, array( 'tablepress-datatables' ), TablePress::version, true );
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
		// Exit early, if no or an empty "datatables_rowgroup" parameter is given.
		if ( empty( $js_options['datatables_rowgroup'] ) ) {
			return $parameters;
		}

		// The default value (first column) does not need a complex parameter value.
		if ( '1' === $js_options['datatables_rowgroup_datasrc'] || 'A' === $js_options['datatables_rowgroup_datasrc'] ) {
			$parameters['rowGroup'] = '"rowGroup":true';
			return $parameters;
		}

		// The columns that shall be used as group categories.
		$data_src = $js_options['datatables_rowgroup_datasrc'];
		// We have a list of columns (possibly with ranges in it).
		$data_src = explode( ',', $data_src );
		// Support for ranges like 3-6 or A-BA.
		$range_cells = array();
		foreach ( $data_src as $key => $value ) {
			$range_dash = strpos( $value, '-' );
			if ( false !== $range_dash ) {
				unset( $data_src[ $key ] );
				$start = trim( substr( $value, 0, $range_dash ) );
				if ( ! is_numeric( $start ) ) {
					$start = TablePress::letter_to_number( $start );
				}
				$end = trim( substr( $value, $range_dash + 1 ) );
				if ( ! is_numeric( $end ) ) {
					$end = TablePress::letter_to_number( $end );
				}
				$current_range = range( $start, $end );
				$range_cells = array_merge( $range_cells, $current_range );
			}
		}
		$data_src = array_merge( $data_src, $range_cells );
		foreach ( $data_src as $key => $value ) {
			$value = trim( $value );
			// Parse single letters.
			if ( ! is_numeric( $value ) ) {
				$value = TablePress::letter_to_number( $value );
			}
			// Subtract 1 to get from 1-based indexing to 0-based indexing.
			$data_src[ $key ] = (int) $value - 1;
		}
		// Remove duplicate entries and sort the array.
		$data_src = array_unique( $data_src, SORT_NUMERIC );
		$data_src = implode( ',', $data_src );

		$parameters['rowGroup'] = '"rowGroup":{"dataSrc":[' . $data_src . ']}';
		return $parameters;
	}

	/**
	 * Enqueues CSS files for the DataTables RowGroup module.
	 *
	 * @since 2.0.0
	 */
	public static function enqueue_css_files(): void {
		/** This filter is documented in modules/controllers/datatables-alphabetsearch.php */
		if ( ! apply_filters( 'tablepress_module_enqueue_css_files', true, self::$module['slug'] ) ) {
			return;
		}

		$css_url = plugins_url( 'modules/css/build/datatables.rowgroup.css', TABLEPRESS__FILE__ );
		wp_enqueue_style( 'tablepress-datatables-rowgroup', $css_url, array( 'tablepress-default' ), TablePress::version );
	}

} // class TablePress_Module_DataTables_RowGroup
