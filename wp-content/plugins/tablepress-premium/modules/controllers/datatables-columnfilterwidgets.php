<?php
/**
 * TablePress DataTables ColumnFilterWidgets.
 *
 * @package TablePress
 * @subpackage DataTables
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the DataTables ColumnFilterWidgets feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_DataTables_ColumnFilterWidgets {
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
	 * Adds options related to DataTables ColumnFilterWidgets to the table template.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['datatables_columnfilterwidgets'] = false;
		$table['options']['datatables_columnfilterwidgets_columns'] = '';
		$table['options']['datatables_columnfilterwidgets_exclude_columns'] = '';
		$table['options']['datatables_columnfilterwidgets_separator'] = '';
		$table['options']['datatables_columnfilterwidgets_max_selections'] = '';
		$table['options']['datatables_columnfilterwidgets_group_terms'] = false;
		return $table;
	}

	/**
	 * Registers "Edit" screen elements for the "DataTables ColumnFilterWidgets" feature.
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
			add_meta_box( 'tablepress_edit-datatables-columnfilterwidgets', __( 'Column Filter Dropdowns', 'tablepress' ), array( __CLASS__, 'postbox_datatables_columnfilterwidgets' ), null, 'normal', 'low' );

			TablePress_Modules_Helper::enqueue_script( 'datatables-columnfilterwidgets' );
			add_filter( 'tablepress_admin_page_script_dependencies', array( __CLASS__, 'add_script_dependencies' ), 10, 2 );
		}
		return $data;
	}

	/**
	 * Adds DataTables ColumnFilterWidgets script as a dependency for the "Edit" script, so that hooks are added before they are executed.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $dependencies List of the dependencies that the $name script relies on.
	 * @param string   $name         Name of the JS script, without extension.
	 * @return string[] Modified list of the dependencies that the $name script relies on.
	 */
	public static function add_script_dependencies( array $dependencies, string $name ): array {
		if ( 'edit' === $name ) {
			$dependencies[] = 'tablepress-datatables-columnfilterwidgets';
		}
		return $dependencies;
	}

	/**
	 * Prints the content of the "DataTables ColumnFilterWidgets" post meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public static function postbox_datatables_columnfilterwidgets( array $data, array $box ): void {
		$help_box_content = '';
		self::print_help_box_markup( $help_box_content );
		?>
		<p id="notice-datatables-columnfilterwidgets-requirements"><em><?php printf( __( 'This feature is only available when the “%1$s”, “%2$s”, and “%3$s” checkboxes in the “%4$s” and “%5$s” sections are checked.', 'tablepress' ), __( 'Table Head Row', 'tablepress' ), __( 'Enable Visitor Features', 'tablepress' ), __( 'Search/Filtering', 'tablepress' ), __( 'Table Options', 'tablepress' ), __( 'Table Features for Site Visitors', 'tablepress' ) ); ?></em></p>
		<p id="notice-datatables-columnfilterwidgets-conflict-datatables-serverside-processing"><em><?php printf( __( 'This feature is only available when the “%1$s” feature is turned off.', 'tablepress' ), __( 'Server-side Processing', 'tablepress' ) ); ?></em></p>
		<p><label for="option-datatables_columnfilterwidgets"><input type="checkbox" name="datatables_columnfilterwidgets" id="option-datatables_columnfilterwidgets"> <?php _e( 'Add Column Filter Dropdowns.', 'tablepress' ); ?></label></p>
		<details id="tablepress-datatables_columnfilterwidgets-advanced-settings">
			<summary><?php _e( 'Advanced settings', 'tablepress' ); ?></summary>
			<div>
				<table class="tablepress-postbox-table fixed">
					<tr class="top-border">
						<th class="column-1 top-align" scope="row"><label for="option-datatables_columnfilterwidgets_columns"><?php _e( 'Columns', 'tablepress' ); ?>:</label></th>
						<td class="column-2">
							<input type="text" name="datatables_columnfilterwidgets_columns" id="option-datatables_columnfilterwidgets_columns" class="large-text" title="<?php esc_attr_e( 'This field can only contain letters, numbers, commas, spaces, and hyphens (-).', 'tablepress' ); ?>" pattern="[0-9A-Z, -]*"><p class="description"><?php _e( 'Enter a comma-separated list of the columns for which a dropdown should be shown, in the desired order, e.g. “3-5,1,7”. By default, all columns will get a dropdown.', 'tablepress' ); ?></p>
						</td>
					</tr>
					<tr class="top-border">
						<th class="column-1 top-align" scope="row"><label for="option-datatables_columnfilterwidgets_exclude_columns"><?php _e( 'Excluded Columns', 'tablepress' ); ?>:</label></th>
						<td class="column-2">
							<input type="text" name="datatables_columnfilterwidgets_exclude_columns" id="option-datatables_columnfilterwidgets_exclude_columns" class="large-text" title="<?php esc_attr_e( 'This field can only contain letters, numbers, commas, spaces, and hyphens (-).', 'tablepress' ); ?>" pattern="[0-9A-Z, -]*"><p class="description"><?php _e( 'Enter a comma-separated list of the columns which shall not get a filter dropdown, e.g. “1,3-5,7”.', 'tablepress' ); ?></p>
						</td>
					</tr>
					<tr class="top-border">
						<th class="column-1 top-align" scope="row"><?php _e( 'Filter Term Separator', 'tablepress' ); ?>:</th>
						<td class="column-2">
							<label for="option-datatables_columnfilterwidgets_separator"><?php printf( __( 'Split cell content by the string %s to get individual filter terms.', 'tablepress' ), '<input type="text" name="datatables_columnfilterwidgets_separator" id="option-datatables_columnfilterwidgets_separator" class="small-text code" title="' . esc_attr__( 'This field can only contain commas, semicolons, slashes, hyphens, and spaces.', 'tablepress' ) . '" pattern="[,;/ -]*">' ); ?></label>
						</td>
					</tr>
					<tr class="top-border">
						<th class="column-1 top-align" scope="row"><?php _e( 'Maximum selections', 'tablepress' ); ?>:</th>
						<td class="column-2">
							<label for="option-datatables_columnfilterwidgets_max_selections"><?php printf( __( 'Allow a maximum number of %s selections from each filter dropdown.', 'tablepress' ), '<input type="number" name="datatables_columnfilterwidgets_max_selections" id="option-datatables_columnfilterwidgets_max_selections" class="small-text" min="1" max="10">' ); ?></label>
						</td>
					</tr>
					<tr class="top-border">
						<th class="column-1" scope="row"><?php _e( 'Filter Terms Grouping', 'tablepress' ); ?>:</th>
						<td class="column-2"><label for="option-datatables_columnfilterwidgets_group_terms"><input type="checkbox" name="datatables_columnfilterwidgets_group_terms" id="option-datatables_columnfilterwidgets_group_terms"> <?php _e( 'List the selected filter terms in one common section instead of underneath each dropdown.', 'tablepress' ); ?></label></td>
					</tr>
				</table>
			</div>
		</details>
		<?php
	}

	/**
	 * Adds parameters for the DataTables ColumnFilterWidgets feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $default_atts Default attributes for the TablePress [table /] Shortcode.
	 * @return array<string, mixed> Extended attributes for the Shortcode.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['datatables_columnfilterwidgets'] = null;
		$default_atts['datatables_columnfilterwidgets_columns'] = null;
		$default_atts['datatables_columnfilterwidgets_exclude_columns'] = null;
		$default_atts['datatables_columnfilterwidgets_separator'] = null;
		$default_atts['datatables_columnfilterwidgets_max_selections'] = null;
		$default_atts['datatables_columnfilterwidgets_group_terms'] = null;
		return $default_atts;
	}

	/**
	 * Passes the DataTables ColumnFilterWidgets configuration from Shortcode parameters to JavaScript arguments.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $js_options     Current JS options.
	 * @param string               $table_id       Table ID.
	 * @param array<string, mixed> $render_options Render Options.
	 * @return array<string, mixed> Modified JS options.
	 */
	public static function pass_render_options_to_js_options( array $js_options, string $table_id, array $render_options ): array {
		$js_options['datatables_columnfilterwidgets'] = $render_options['datatables_columnfilterwidgets'];
		$js_options['datatables_columnfilterwidgets_columns'] = $render_options['datatables_columnfilterwidgets_columns'];
		$js_options['datatables_columnfilterwidgets_exclude_columns'] = $render_options['datatables_columnfilterwidgets_exclude_columns'];
		$js_options['datatables_columnfilterwidgets_separator'] = $render_options['datatables_columnfilterwidgets_separator'];
		$js_options['datatables_columnfilterwidgets_max_selections'] = $render_options['datatables_columnfilterwidgets_max_selections'];
		$js_options['datatables_columnfilterwidgets_group_terms'] = $render_options['datatables_columnfilterwidgets_group_terms'];

		// Column Filter Dropdowns is not supported with Server-side Processing.
		if ( isset( $render_options['datatables_serverside_processing'] ) && $render_options['datatables_serverside_processing'] ) {
			$js_options['datatables_columnfilterwidgets'] = false;
		}

		if ( false !== $js_options['datatables_columnfilterwidgets'] ) {
			$js_url = plugins_url( 'modules/js/datatables.columnfilterwidgets.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-columnfilterwidgets', $js_url, array( 'tablepress-datatables' ), TablePress::version, true );
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
		if ( ! $js_options['datatables_columnfilterwidgets'] ) {
			return $parameters;
		}

		// Prepend "W" to the "dom" value, if one is already set, otherwise use the default.
		if ( isset( $parameters['dom'] ) ) {
			$parameters['dom'] = str_replace( ':"', ':"W', $parameters['dom'] );
		} else {
			$parameters['dom'] = '"dom":"Wlfrtip"';
		}

		$columnfilterwidgets_parameters = array();

		$columns = trim( $js_options['datatables_columnfilterwidgets_columns'] );
		if ( '' !== $columns ) {
			// We have a list of columns (possibly with ranges in it).
			$columns = explode( ',', $columns );
			// Support for ranges like 3-6 or A-BA.
			$range_cells = array();
			foreach ( $columns as $key => $value ) {
				$range_dash = strpos( $value, '-' );
				if ( false !== $range_dash ) {
					unset( $columns[ $key ] );
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
			$columns = array_merge( $columns, $range_cells );
			// Parse single letters.
			foreach ( $columns as $key => $value ) {
				$value = trim( $value );
				if ( ! is_numeric( $value ) ) {
					$value = TablePress::letter_to_number( $value );
				}
				$columns[ $key ] = ( (int) $value ) - 1; // Convert column number to 0-based column index.
			}
			$columns = implode( ',', $columns );

			if ( '' !== $columns ) {
				$columnfilterwidgets_parameters['columns'] = '"columns":[ ' . $columns . ' ]';
			}
		}

		$excluded_columns = trim( $js_options['datatables_columnfilterwidgets_exclude_columns'] );
		if ( '' !== $excluded_columns ) {
			// We have a list of columns (possibly with ranges in it).
			$excluded_columns = explode( ',', $excluded_columns );
			// Support for ranges like 3-6 or A-BA.
			$range_cells = array();
			foreach ( $excluded_columns as $key => $value ) {
				$range_dash = strpos( $value, '-' );
				if ( false !== $range_dash ) {
					unset( $excluded_columns[ $key ] );
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
			$excluded_columns = array_merge( $excluded_columns, $range_cells );
			// Parse single letters.
			foreach ( $excluded_columns as $key => $value ) {
				$value = trim( $value );
				if ( ! is_numeric( $value ) ) {
					$value = TablePress::letter_to_number( $value );
				}
				$excluded_columns[ $key ] = ( (int) $value ) - 1; // Convert column number to 0-based column index.
			}
			// Remove duplicate entries and sort the array.
			$excluded_columns = array_unique( $excluded_columns, SORT_NUMERIC );
			sort( $excluded_columns );
			$excluded_columns = implode( ',', $excluded_columns );

			if ( '' !== $excluded_columns ) {
				$columnfilterwidgets_parameters['aiExclude'] = '"aiExclude":[ ' . $excluded_columns . ' ]';
			}
		}

		if ( '' !== $js_options['datatables_columnfilterwidgets_separator'] ) {
			$separator = wp_json_encode( $js_options['datatables_columnfilterwidgets_separator'], JSON_HEX_TAG | JSON_UNESCAPED_SLASHES );
			$columnfilterwidgets_parameters['sSeparator'] = '"sSeparator":' . $separator;
		}

		if ( '' !== $js_options['datatables_columnfilterwidgets_max_selections'] ) {
			$limit = absint( $js_options['datatables_columnfilterwidgets_max_selections'] );
			$columnfilterwidgets_parameters['iMaxSelections'] = '"iMaxSelections":' . $limit;
		}

		if ( false !== $js_options['datatables_columnfilterwidgets_group_terms'] ) {
			$columnfilterwidgets_parameters['bGroupTerms'] = '"bGroupTerms":true';
		}

		if ( ! empty( $columnfilterwidgets_parameters ) ) {
			$columnfilterwidgets_parameters = implode( ',', $columnfilterwidgets_parameters );
			$parameters['oColumnFilterWidgets'] = '"oColumnFilterWidgets":{ ' . $columnfilterwidgets_parameters . ' }';
		}

		return $parameters;
	}

	/**
	 * Enqueues CSS files for the DataTables ColumnFilterWidgets module.
	 *
	 * @since 2.0.0
	 */
	public static function enqueue_css_files(): void {
		/** This filter is documented in modules/controllers/datatables-alphabetsearch.php */
		if ( ! apply_filters( 'tablepress_module_enqueue_css_files', true, self::$module['slug'] ) ) {
			return;
		}

		$css_url = plugins_url( 'modules/css/build/datatables.columnfilterwidgets.css', TABLEPRESS__FILE__ );
		wp_enqueue_style( 'tablepress-datatables-columnfilterwidgets', $css_url, array( 'tablepress-default' ), TablePress::version );
	}

} // class TablePress_Module_DataTables_ColumnFilterWidgets
