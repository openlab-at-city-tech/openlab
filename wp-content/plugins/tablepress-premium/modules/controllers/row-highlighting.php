<?php
/**
 * TablePress Row Highlighting.
 *
 * @package TablePress
 * @subpackage Row Highlighting.
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the Row Highlighting feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_Row_Highlighting {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Helper string that contains the name of the function that is used for the content comparison.
	 *
	 * @since 2.0.0
	 *
	 * @var callable
	 */
	protected static $highlight_compare_function;

	/**
	 * Helper string that contains the name of the function that is used for the content matching.
	 *
	 * @since 2.0.0
	 *
	 * @var callable
	 */
	protected static $highlight_match_function;

	/**
	 * Helper array that contains the highlight terms.
	 *
	 * @since 2.0.0
	 *
	 * @var string[]
	 */
	protected static $highlight_terms = array();

	/**
	 * Helper array that contains the columns in which highlighting should be performed.
	 *
	 * @since 2.0.0
	 *
	 * @var int[]
	 */
	protected static $highlight_columns = array();

	/**
	 * Helper array that contains the rows in which highlighting should be performed.
	 *
	 * @since 2.0.0
	 *
	 * @var int[]
	 */

	protected static $highlight_rows = array();
	/**
	 * Helper boolean defines whether full cell matching should be done.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	protected static $full_cell_match = false;

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
		add_filter( 'tablepress_table_render_data', array( __CLASS__, 'process_parameters' ), 10, 3 );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_block_editor_js' ) );
	}

	/**
	 * Adds options related to Row Highlighting to the table template.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['row_highlight'] = '';
		$table['options']['row_highlight_full_cell_match'] = true;
		$table['options']['row_highlight_case_sensitive'] = false;
		$table['options']['row_highlight_columns'] = ''; // '' equates to 'all'.
		$table['options']['row_highlight_rows'] = ''; // '' equates to 'all'.
		return $table;
	}

	/**
	 * Registers "Edit" screen elements for the Row Highlighting feature.
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
			add_meta_box( 'tablepress_edit-row-highlighting', __( 'Highlight certain rows based on their content', 'tablepress' ), array( __CLASS__, 'postbox_row_highlighting' ), null, 'normal', 'low' );

			TablePress_Modules_Helper::enqueue_script( 'row-highlighting' );
			add_filter( 'tablepress_admin_page_script_dependencies', array( __CLASS__, 'add_script_dependencies' ), 10, 2 );
		}
		return $data;
	}

	/**
	 * Adds Row Highlighting script as a dependency for the "Edit" script, so that hooks are added before they are executed.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $dependencies List of the dependencies that the $name script relies on.
	 * @param string   $name         Name of the JS script, without extension.
	 * @return string[] Modified list of the dependencies that the $name script relies on.
	 */
	public static function add_script_dependencies( array $dependencies, string $name ): array {
		if ( 'edit' === $name ) {
			$dependencies[] = 'tablepress-row-highlighting';
		}
		return $dependencies;
	}

	/**
	 * Prints the content of the "Row Highlighting" post meta box.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $data Data for this screen.
	 * @param array<string, mixed> $box  Information about the meta box.
	 */
	public static function postbox_row_highlighting( array $data, array $box ): void {
		$help_box_content = '';
		self::print_help_box_markup( $help_box_content );
		?>
		<p id="notice-row-highlighting-conflict-datatables-serverside-processing"><em><?php printf( __( 'This feature is only available when the “%1$s” feature is turned off.', 'tablepress' ), __( 'Server-side Processing', 'tablepress' ) ); ?></em></p>
		<table class="tablepress-postbox-table fixed">
			<tr>
				<th class="column-1 top-align" scope="row"><label for="option-row_highlight"><?php _e( 'Row Highlight term', 'tablepress' ); ?>:</label></th>
				<td class="column-2">
					<input type="text" name="row_highlight" id="option-row_highlight" class="large-text"><p class="description"><?php _e( 'Rows that contain this term will be highlighted.', 'tablepress' ); ?> <?php _e( 'You can combine multiple highlight terms with an OR operator, e.g. “term1||term2”.', 'tablepress' ); ?></p>
				</td>
			</tr>
		</table>
		<details id="tablepress-row_highlight-advanced-settings">
			<summary><?php _e( 'Advanced settings', 'tablepress' ); ?></summary>
			<div>
				<table class="tablepress-postbox-table fixed">
					<tr>
						<th class="column-1" scope="row"><?php _e( 'Full cell matching', 'tablepress' ); ?>:</th>
						<td class="column-2"><label for="option-row_highlight_full_cell_match"><input type="checkbox" name="row_highlight_full_cell_match" id="option-row_highlight_full_cell_match"> <?php _e( 'The full cell content has to match the highlight term.', 'tablepress' ); ?></label></td>
					</tr>
					<tr>
						<th class="column-1" scope="row"><?php _e( 'Case sensitivity', 'tablepress' ); ?>:</th>
						<td class="column-2"><label for="option-row_highlight_case_sensitive"><input type="checkbox" name="row_highlight_case_sensitive" id="option-row_highlight_case_sensitive"> <?php _e( 'The case sensitivity of the highlight term has to match the content in the cell.', 'tablepress' ); ?></label></td>
					</tr>
					<tr class="top-border">
						<th class="column-1 top-align" scope="row"><label for="option-row_highlight_columns"><?php _e( 'Highlight Columns', 'tablepress' ); ?>:</label></th>
						<td class="column-2">
							<input type="text" name="row_highlight_columns" id="option-row_highlight_columns" class="large-text" title="<?php esc_attr_e( 'This field can only contain letters, numbers, commas, spaces, and hyphens (-).', 'tablepress' ); ?>" pattern="[0-9A-Z, -]*"><p class="description"><?php _e( 'Enter a comma-separated list of the columns which shall be searched for the highlight terms, e.g. “1,3-5,7”.', 'tablepress' ); ?></p>
						</td>
					</tr>
					<tr>
						<th class="column-1 top-align" scope="row"><label for="option-row_highlight_rows"><?php _e( 'Highlight Rows', 'tablepress' ); ?>:</label></th>
						<td class="column-2">
							<input type="text" name="row_highlight_rows" id="option-row_highlight_rows" class="large-text" title="<?php esc_attr_e( 'This field can only contain numbers, commas, spaces, and hyphens (-).', 'tablepress' ); ?>" pattern="[0-9, -]*"><p class="description"><?php _e( 'Enter a comma-separated list of the rows which shall be searched for the highlight terms, e.g. “1,3-5,7”.', 'tablepress' ); ?></p>
						</td>
					</tr>
				</table>
			</div>
		</details>
		<?php
	}

	/**
	 * Adds parameters for the Row Highlighting feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $default_atts Default Shortcode attributes.
	 * @return array<string, mixed> Extended Shortcode attributes.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['row_highlight'] = null;
		$default_atts['row_highlight_full_cell_match'] = null;
		$default_atts['row_highlight_case_sensitive'] = null;
		$default_atts['row_highlight_columns'] = null;
		$default_atts['row_highlight_rows'] = null;
		return $default_atts;
	}

	/**
	 * Registers the module's JS script for the block editor.
	 *
	 * @since 2.0.0
	 */
	public static function enqueue_block_editor_js(): void {
		TablePress_Modules_Helper::enqueue_script( 'row-highlighting-block' );
	}

	/**
	 * Helper function for exact matching (strcmp() and strcasecmp() return 0 in case of exact match).
	 *
	 * @since 2.0.0
	 *
	 * @param string $a Row content.
	 * @param string $b Search term.
	 * @return bool Whether string $a and $ are equal (thus the highlighting matches).
	 */
	public static function _full_cell_match( string $a, string $b ): bool {
		return ( 0 === call_user_func( self::$highlight_compare_function, $a, $b ) );
	}

	/**
	 * Helper function for part matching (strpos() and stripos() return false in case of no match).
	 *
	 * @since 2.0.0
	 *
	 * @param string $a Row content.
	 * @param string $b Search term.
	 * @return bool Whether string $b can be found somewhere in $a (thus the highlighting matches).
	 */
	public static function _cell_partial_match( string $a, string $b ): bool {
		return ( false !== call_user_func( self::$highlight_compare_function, $a, $b ) );
	}

	/**
	 * Extracts Row Highlighting parameters and save them locally, because they are not available in the row class filter hook.
	 *
	 * The function is used as a filter hook handler, but the passed parameter `$table` is not changed.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table          The table.
	 * @param array<string, mixed> $orig_table     The previous state of the table, including hidden rows/columns.
	 * @param array<string, mixed> $render_options Render options for the table.
	 * @return array<string, mixed> Unmodified table.
	 */
	public static function process_parameters( array $table, array $orig_table, array $render_options ): array {
		// Exit early, if no or an empty "row_highlight" parameter is given.
		if ( empty( $render_options['row_highlight'] ) ) {
			return $table;
		}

		// Row Highlighting is not supported with Server-side Processing.
		if ( isset( $render_options['datatables_serverside_processing'] ) && $render_options['datatables_serverside_processing'] ) {
			return $table;
		}

		// Exit early if there's no actual table data (e.g. after using the Row Filter module).
		if ( 0 === count( $table['data'] ) ) {
			return $table;
		}

		// The Highlight values.
		self::$highlight_terms = explode( '||', $render_options['row_highlight'] );

		// The columns that shall be searched for the Highlight values.
		$highlight_columns = $render_options['row_highlight_columns'];
		// Add a range with all columns to the list if "" or "all" is set for the columns parameter.
		if ( '' === $highlight_columns || 'all' === $highlight_columns ) {
			$highlight_columns = '1-' . count( $table['data'][0] );
		}
		// We have a list of columns (possibly with ranges in it).
		$highlight_columns = explode( ',', $highlight_columns );
		// Support for ranges like 3-6 or A-BA.
		$range_cells = array();
		foreach ( $highlight_columns as $key => $value ) {
			$range_dash = strpos( $value, '-' );
			if ( false !== $range_dash ) {
				unset( $highlight_columns[ $key ] );
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
		$highlight_columns = array_merge( $highlight_columns, $range_cells );
		// Parse single letters.
		foreach ( $highlight_columns as $key => $value ) {
			$value = trim( $value );
			if ( ! is_numeric( $value ) ) {
				$value = TablePress::letter_to_number( $value );
			}
			$highlight_columns[ $key ] = (int) $value;
		}
		// Remove duplicate entries and sort the array.
		$highlight_columns = array_unique( $highlight_columns, SORT_NUMERIC );
		self::$highlight_columns = $highlight_columns;

		// The rows that shall be searched for the Highlight values.
		$highlight_rows = $render_options['row_highlight_rows'];
		// Add a range with all rows to the list if "" or "all" is set for the rows parameter.
		if ( '' === $highlight_rows || 'all' === $highlight_rows ) {
			$highlight_rows = '1-' . count( $table['data'] );
		}
		// We have a list of rows (possibly with ranges in it).
		$highlight_rows = explode( ',', $highlight_rows );
		// Support for ranges like 3-6.
		$range_cells = array();
		foreach ( $highlight_rows as $key => $value ) {
			$range_dash = strpos( $value, '-' );
			if ( false !== $range_dash ) {
				unset( $highlight_rows[ $key ] );
				$start = trim( substr( $value, 0, $range_dash ) );
				$end = trim( substr( $value, $range_dash + 1 ) );
				$current_range = range( $start, $end );
				$range_cells = array_merge( $range_cells, $current_range );
			}
		}
		$highlight_rows = array_merge( $highlight_rows, $range_cells );
		$highlight_rows = array_map( 'absint', $highlight_rows );
		$highlight_rows = array_unique( $highlight_rows, SORT_NUMERIC );
		self::$highlight_rows = $highlight_rows;

		// Determine which functions should be used for matching, depending on parameters.
		self::$full_cell_match = $render_options['row_highlight_full_cell_match'];
		if ( self::$full_cell_match ) {
			// The entire cell content has to match the search term.
			self::$highlight_match_function = array( __CLASS__, '_full_cell_match' );
			if ( $render_options['row_highlight_case_sensitive'] ) {
				self::$highlight_compare_function = 'strcmp';
			} else {
				self::$highlight_compare_function = 'strcasecmp';
			}
		} else {
			// The search term can be anywhere in the cell content.
			self::$highlight_match_function = array( __CLASS__, '_cell_partial_match' );
			if ( $render_options['row_highlight_case_sensitive'] ) {
				self::$highlight_compare_function = 'strpos';
			} else {
				self::$highlight_compare_function = 'stripos';
			}
		}

		// Register actual filter and cleanup filter.
		add_filter( 'tablepress_row_css_class', array( __CLASS__, 'highlight_rows' ), 10, 5 );
		add_filter( 'tablepress_table_output', array( __CLASS__, 'remove_row_css_class_filter' ), 10, 3 );

		return $table;
	}

	/**
	 * Searches current row for highlight terms, and add another CSS class on find.
	 *
	 * @since 2.0.0
	 *
	 * @param string   $row_class  Current CSS classes of the row.
	 * @param string   $table_id   Table ID.
	 * @param string[] $row_cells  HTML code for the cells.
	 * @param int      $row_number Row number.
	 * @param string[] $row_data   Row data array.
	 * @return string Row's new CSS classes.
	 */
	public static function highlight_rows( string $row_class, string $table_id, array $row_cells, int $row_number, array $row_data ): string {
		if ( empty( $row_data ) ) {
			return $row_class;
		}

		if ( ! in_array( $row_number, self::$highlight_rows, true ) ) {
			return $row_class;
		}

		foreach ( self::$highlight_terms as $highlight_term ) {
			foreach ( $row_data as $column_idx => $cell_content ) {
				$column_number = $column_idx + 1;
				if ( ! in_array( $column_number, self::$highlight_columns, true ) ) {
					continue;
				}

				if ( call_user_func( self::$highlight_match_function, $cell_content, $highlight_term ) ) {
					$row_class .= ' row-highlight-' . strtolower( sanitize_title_with_dashes( $highlight_term ) );
					break; // No need to check remaining cells in the row after a match.
				}
			}
		}

		return $row_class;
	}

	/**
	 * Removes filter on CSS class again, to allow for the class to be used again on the same page.
	 *
	 * The function is used as a filter hook handler, but the passed parameter `$output` is not changed.
	 *
	 * @since 2.0.0
	 *
	 * @param string               $output         Table output.
	 * @param array<string, mixed> $table          Table.
	 * @param array<string, mixed> $render_options Render options.
	 * @return string Table output.
	 */
	public static function remove_row_css_class_filter( string $output, array $table, array $render_options ): string {
		remove_filter( 'tablepress_row_css_class', array( __CLASS__, 'highlight_rows' ), 10 );
		return $output;
	}

} // class TablePress_Module_Row_Highlighting
