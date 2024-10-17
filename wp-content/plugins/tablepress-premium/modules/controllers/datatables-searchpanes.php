<?php
/**
 * TablePress DataTables SearchPanes.
 *
 * @package TablePress
 * @subpackage DataTables
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the DataTables SearchPanes feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_DataTables_SearchPanes {
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
	 * Adds options related to DataTables SearchPanes to the table template.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['datatables_searchpanes'] = false;
		$table['options']['datatables_searchpanes_columns'] = '';
		return $table;
	}

	/**
	 * Registers "Edit" screen elements for the "DataTables SearchPanes" feature.
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
			add_meta_box( 'tablepress_edit-datatables-searchpanes', __( 'Search Panes', 'tablepress' ), array( __CLASS__, 'postbox_datatables_searchpanes' ), null, 'normal', 'low' );

			TablePress_Modules_Helper::enqueue_script( 'datatables-searchpanes' );
			add_filter( 'tablepress_admin_page_script_dependencies', array( __CLASS__, 'add_script_dependencies' ), 10, 2 );
		}
		return $data;
	}

	/**
	 * Adds DataTables SearchPanes script as a dependency for the "Edit" script, so that hooks are added before they are executed.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $dependencies List of the dependencies that the $name script relies on.
	 * @param string   $name         Name of the JS script, without extension.
	 * @return string[] Modified list of the dependencies that the $name script relies on.
	 */
	public static function add_script_dependencies( array $dependencies, string $name ): array {
		if ( 'edit' === $name ) {
			$dependencies[] = 'tablepress-datatables-searchpanes';
		}
		return $dependencies;
	}

	/**
	 * Prints the content of the "DataTables SearchPanes" post meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public static function postbox_datatables_searchpanes( array $data, array $box ): void {
		$help_box_content = '';
		self::print_help_box_markup( $help_box_content );
		?>
		<p id="notice-datatables-searchpanes-requirements"><em><?php printf( __( 'This feature is only available when the “%1$s”, “%2$s”, and “%3$s” checkboxes in the “%4$s” and “%5$s” sections are checked.', 'tablepress' ), __( 'Table Head Row', 'tablepress' ), __( 'Enable Visitor Features', 'tablepress' ), __( 'Search/Filtering', 'tablepress' ), __( 'Table Options', 'tablepress' ), __( 'Table Features for Site Visitors', 'tablepress' ) ); ?></em></p>
		<p id="notice-datatables-searchpanes-conflict-datatables-serverside-processing"><em><?php printf( __( 'This feature is only available when the “%1$s” feature is turned off.', 'tablepress' ), __( 'Server-side Processing', 'tablepress' ) ); ?></em></p>
		<p><label for="option-datatables_searchpanes"><input type="checkbox" name="datatables_searchpanes" id="option-datatables_searchpanes"> <?php _e( 'Show panes for filtering the columns.', 'tablepress' ); ?></label></p>
		<details id="tablepress-datatables_searchpanes-advanced-settings">
			<summary><?php _e( 'Advanced settings', 'tablepress' ); ?></summary>
			<div>
				<table class="tablepress-postbox-table fixed">
					<tr class="top-border">
						<th class="column-1 top-align" scope="row"><label for="option-datatables_searchpanes_columns"><?php _e( 'Columns', 'tablepress' ); ?>:</label></th>
						<td class="column-2">
							<input type="text" name="datatables_searchpanes_columns" id="option-datatables_searchpanes_columns" class="large-text" title="<?php esc_attr_e( 'This field can only contain letters, numbers, commas, spaces, and hyphens (-).', 'tablepress' ); ?>" pattern="[0-9A-Z, -]*"><p class="description"><?php _e( 'Enter a comma-separated list of the columns for which a search pane should be shown, in the desired order, e.g. “3-5,1,7”. By default, the visible searches panes will be determined automatically.', 'tablepress' ); ?></p>
						</td>
					</tr>
				</table>
			</div>
		</details>
		<?php
	}

	/**
	 * Adds parameters for the DataTables SearchPanes feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $default_atts Default attributes for the TablePress [table /] Shortcode.
	 * @return array<string, mixed> Extended attributes for the Shortcode.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['datatables_searchpanes'] = null;
		$default_atts['datatables_searchpanes_columns'] = null;
		return $default_atts;
	}

	/**
	 * Passes the DataTables SearchPanes configuration from Shortcode parameters to JavaScript arguments.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $js_options     Current JS options.
	 * @param string               $table_id       Table ID.
	 * @param array<string, mixed> $render_options Render Options.
	 * @return array<string, mixed> Modified JS options.
	 */
	public static function pass_render_options_to_js_options( array $js_options, string $table_id, array $render_options ): array {
		$js_options['datatables_searchpanes'] = $render_options['datatables_searchpanes'];
		$js_options['datatables_searchpanes_columns'] = $render_options['datatables_searchpanes_columns'];

		// Search Panes is not supported with Server-side Processing.
		if ( isset( $render_options['datatables_serverside_processing'] ) && $render_options['datatables_serverside_processing'] ) {
			$js_options['datatables_searchpanes'] = false;
		}

		if ( false !== $js_options['datatables_searchpanes'] ) {
			$js_url = plugins_url( 'modules/js/datatables.select.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-select', $js_url, array( 'tablepress-datatables' ), TablePress::version, true );
			$js_url = plugins_url( 'modules/js/datatables.searchpanes.min.js', TABLEPRESS__FILE__ );
			wp_enqueue_script( 'tablepress-datatables-searchpanes', $js_url, array( 'tablepress-datatables', 'tablepress-datatables-select' ), TablePress::version, true );
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
		if ( ! $js_options['datatables_searchpanes'] ) {
			return $parameters;
		}

		// Prepend "P" to the "dom" value, if one is already set, otherwise use the default.
		if ( isset( $parameters['dom'] ) ) {
			$parameters['dom'] = str_replace( ':"', ':"P', $parameters['dom'] );
		} else {
			$parameters['dom'] = '"dom":"Plfrtip"';
		}

		$columns = trim( $js_options['datatables_searchpanes_columns'] );
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
				$parameters['searchPanes'] = '"searchPanes":{"columns":[' . $columns . ']}';

				// Prepend a columnDefs definition to the "columnDefs" value, if one is already set, otherwise use the default.
				$column_defs = '{"searchPanes":{"show":true},"targets":[' . $columns . ']}';
				if ( isset( $parameters['columnDefs'] ) ) {
					$parameters['columnDefs'] = str_replace( '"columnDefs":[', "\"columnDefs\":[{$column_defs},", $parameters['columnDefs'] );
				} else {
					$parameters['columnDefs'] = "\"columnDefs\":[{$column_defs}]";
				}
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
			'searchPanes' => array(
				'clearMessage'    => _x( 'Clear All', 'SearchPanes module', 'tablepress' ),
				'clearPane'       => _x( '&times;', 'SearchPanes module', 'tablepress' ),
				'collapse'        => array(
					'0' => _x( 'SearchPanes', 'SearchPanes module', 'tablepress' ),
					'_' => _x( 'SearchPanes (%d)', 'SearchPanes module', 'tablepress' ),
				),
				'collapseMessage' => _x( 'Collapse All', 'SearchPanes module', 'tablepress' ),
				'count'           => _x( '{total}', 'SearchPanes module', 'tablepress' ),
				'emptyMessage'    => '<em>' . _x( 'No data', 'SearchPanes module', 'tablepress' ) . '</em>',
				'emptyPanes'      => _x( 'No SearchPanes', 'SearchPanes module', 'tablepress' ),
				'loadMessage'     => _x( 'Loading Search Panes...', 'SearchPanes module', 'tablepress' ),
				'showMessage'     => _x( 'Show All', 'SearchPanes module', 'tablepress' ),
				'title'           => _x( 'Filters Active - %d', 'SearchPanes module', 'tablepress' ),
			),
		);
		// Merge existing strings into the new strings, so that existing translations are not lost.
		$datatables_strings = array_replace_recursive( $new_strings, $datatables_strings );

		return $datatables_strings;
	}

	/**
	 * Enqueues CSS files for the DataTables SearchPanes module.
	 *
	 * @since 2.0.0
	 */
	public static function enqueue_css_files(): void {
		/** This filter is documented in modules/controllers/datatables-alphabetsearch.php */
		if ( ! apply_filters( 'tablepress_module_enqueue_css_files', true, self::$module['slug'] ) ) {
			return;
		}

		$css_url = plugins_url( 'modules/css/build/datatables.select.css', TABLEPRESS__FILE__ );
		wp_enqueue_style( 'tablepress-datatables-select', $css_url, array( 'tablepress-default' ), TablePress::version );
		$css_url = plugins_url( 'modules/css/build/datatables.searchpanes.css', TABLEPRESS__FILE__ );
		wp_enqueue_style( 'tablepress-datatables-searchpanes', $css_url, array( 'tablepress-datatables-select' ), TablePress::version );
	}

} // class TablePress_Module_DataTables_SearchPanes
