<?php
/**
 * TablePress Row Filtering.
 *
 * @package TablePress
 * @subpackage Row Filtering.
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the Row Filtering feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_Row_Filtering {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Helper string that contains the name of the function that is used for the content matching.
	 *
	 * @since 2.0.0
	 *
	 * @var callable
	 */
	protected static $filter_compare_function;

	/**
	 * Registers necessary plugin filter hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'tablepress_table_template', array( __CLASS__, 'add_option_to_table_template' ) );
		add_filter( 'tablepress_shortcode_table_default_shortcode_atts', array( __CLASS__, 'add_shortcode_parameters' ) );
		add_filter( 'tablepress_table_render_options', array( __CLASS__, 'process_table_render_options' ), 10, 2 );
		add_filter( 'tablepress_datatables_serverside_processing_render_options', array( __CLASS__, 'add_serverside_processing_render_options' ), 10, 3 );
		add_filter( 'tablepress_table_raw_render_data', array( __CLASS__, 'filter_rows' ), 10, 2 );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_block_editor_js' ) );
	}

	/**
	 * Adds options related to Row Filtering to the table template.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['filter'] = '';
		$table['options']['filter_full_cell_match'] = false;
		$table['options']['filter_case_sensitive'] = false;
		$table['options']['filter_columns'] = ''; // '' equates to 'all'.
		$table['options']['filter_inverse'] = false;
		$table['options']['filter_url_parameter'] = '';
		return $table;
	}

	/**
	 * Adds parameters for the Row Filtering feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $default_atts Default Shortcode attributes.
	 * @return array<string, mixed> Extended Shortcode attributes.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['filter'] = null;
		$default_atts['filter_full_cell_match'] = null;
		$default_atts['filter_case_sensitive'] = null;
		$default_atts['filter_columns'] = null;
		$default_atts['filter_inverse'] = null;
		$default_atts['filter_url_parameter'] = null;
		return $default_atts;
	}

	/**
	 * Replaces the static filter term with the one from the configured URL parameter, if set.
	 *
	 * This needs to be done in this filter hook, so that the changed `filter` parameter can be used for retrieving/setting the correct table output cache.
	 *
	 * @since 2.1.4
	 *
	 * @param array<string, mixed> $render_options Render Options.
	 * @param array<string, mixed> $table          Table.
	 * @return array<string, mixed> Modified Render Options.
	 */
	public static function process_table_render_options( array $render_options, array $table ): array {
		// If given, use the URL filter parameter.
		if ( ! empty( $render_options['filter_url_parameter'] ) ) {
			// Only allow characters a-z, A-Z, 0-9, _, and - in the URL parameter name. The filter term can be anything.
			$render_options['filter_url_parameter'] = (string) preg_replace( '#[^a-zA-Z0-9_-]#', '', $render_options['filter_url_parameter'] );
			if ( ! empty( $_GET[ $render_options['filter_url_parameter'] ] ) ) {
				$render_options['filter'] = $_GET[ $render_options['filter_url_parameter'] ];
			}
		}

		return $render_options;
	}

	/**
	 * Adds parameters for the Row Filtering feature to the DataTables Server-side Processing render options.
	 *
	 * @since 2.1.0
	 *
	 * @param array<string, mixed> $render_options_ssp Render Options list for Server-side Processing.
	 * @param string               $table_id           Table ID.
	 * @param array<string, mixed> $render_options     Render Options.
	 * @return string[] Modified Render Options list for Server-side Processing.
	 */
	public static function add_serverside_processing_render_options( array $render_options_ssp, string $table_id, array $render_options ): array {
		$render_options_ssp[] = 'filter';
		$render_options_ssp[] = 'filter_full_cell_match';
		$render_options_ssp[] = 'filter_case_sensitive';
		$render_options_ssp[] = 'filter_columns';
		$render_options_ssp[] = 'filter_inverse';
		// $render_options_ssp[] = 'filter_url_parameter'; // The URL parameter is not passed in the SSP URL.
		return $render_options_ssp;
	}

	/**
	 * Registers the module's JS script for the block editor.
	 *
	 * @since 2.0.0
	 */
	public static function enqueue_block_editor_js(): void {
		TablePress_Modules_Helper::enqueue_script( 'row-filtering-block' );
	}

	/**
	 * Helper function for exact matching (strcmp() and strcasecmp() return 0 in case of exact match).
	 *
	 * @since 2.0.0
	 *
	 * @param string $a Cell content.
	 * @param string $b Search term.
	 * @return bool Whether string $a and $ are equal (thus the filter matches).
	 */
	public static function _filter_full_cell_match( string $a, string $b ): bool {
		return ( 0 === call_user_func( self::$filter_compare_function, $a, $b ) );
	}

	/**
	 * Helper function for partial matching (strpos() and stripos() return false in case of no match).
	 *
	 * @since 2.0.0
	 *
	 * @param string $a Cell content.
	 * @param string $b Search term.
	 * @return bool Whether string $b can be found somewhere in $a (thus the filter matches).
	 */
	public static function _filter_cell_partial_match( string $a, string $b ): bool {
		return ( false !== call_user_func( self::$filter_compare_function, $a, $b ) );
	}

	/**
	 * Removes all rows from the table that do not fulfil the filter criterion.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table          The table.
	 * @param array<string, mixed> $render_options Render options for the table.
	 * @return array<string, mixed> Possibly filtered table.
	 */
	public static function filter_rows( array $table, array $render_options ): array {
		// Exit early, if no "filter" parameter is given, either from the URL parameter or the fallback -- except when doing full cell matching, to enable filtering for empty cells.
		if ( empty( $render_options['filter'] ) && ! $render_options['filter_full_cell_match'] ) {
			return $table;
		}

		$filter = $render_options['filter'];
		$filter_columns = $render_options['filter_columns'];
		$filter_inverse = ( true === $render_options['filter_inverse'] );

		$filter_data = self::_remove_not_filtered_columns( $table['data'], $filter_columns );

		// Bail, if no columns are left to be filtered.
		if ( 0 === count( $filter_data[0] ) ) {
			return $table;
		}

		// Determine which function should be used for matching, depending on parameters.
		if ( $render_options['filter_full_cell_match'] ) {
			// The entire cell content has to match the search term.
			$filter_match_function = '_filter_full_cell_match';
			if ( $render_options['filter_case_sensitive'] ) {
				self::$filter_compare_function = 'strcmp';
			} else {
				self::$filter_compare_function = 'strcasecmp';
			}
		} else {
			// The search term can be anywhere in the cell content.
			$filter_match_function = '_filter_cell_partial_match';
			if ( $render_options['filter_case_sensitive'] ) {
				self::$filter_compare_function = 'strpos';
			} else {
				self::$filter_compare_function = 'stripos';
			}
		}

		// && will be passed as &#038;&#038; or &amp;&amp;, depending on the used editor.
		$filter = str_replace( array( '&#038;&#038;', '&amp;&amp;' ), '&&', $filter );

		// Evaluate logic expressions in filter term.
		if ( str_contains( $filter, '&&' ) ) {
			$compare = 'and';
			$filter_terms = explode( '&&', $filter );
		} elseif ( str_contains( $filter, '||' ) ) {
			$compare = 'or';
			$filter_terms = explode( '||', $filter );
		} else {
			$compare = 'none'; // Single filter word.
			$filter_terms = array( $filter );
		}

		$filter_terms = array_unique( $filter_terms );

		// Remove HTML entities and turn them into characters, escape/slash other characters.
		foreach ( $filter_terms as $key => $filter_term ) {
			$filter_terms[ $key ] = wp_specialchars_decode( $filter_term, ENT_QUOTES );
		}

		// Rows that do not match the filter, will be hidden via "hide_rows" Shortcode attribute.
		$hidden_rows = array();

		// Check for every row if it matches the filter.
		$last_row_idx = count( $filter_data ) - 1;
		foreach ( $filter_data as $row_idx => $row ) {
			// Always show the header/footer rows, if enabled.
			if ( 0 === $row_idx && $render_options['table_head'] ) {
				continue;
			}
			if ( $last_row_idx === $row_idx && $render_options['table_foot'] ) {
				continue;
			}

			$found = array();
			foreach ( $filter_terms as $filter_term ) {
				$found[ $filter_term ] = false; // @phpstan-ignore-line
				foreach ( $row as $col_idx => $cell_content ) {
					if ( call_user_func( array( __CLASS__, $filter_match_function ), $cell_content, $filter_term ) ) {
						$found[ $filter_term ] = true; // @phpstan-ignore-line
						break;
					}
				}
			}

			// Evaluate logic expressions.
			switch ( $compare ) {
				case 'none':
				case 'or':
					// At least one word was found / only filter word was found.
					if ( ! in_array( true, $found, true ) ^ $filter_inverse ) {
						$hidden_rows[] = $row_idx;
					}
					break;
				case 'and':
					// If not (at least one word was *not* found) == all words were found.
					if ( in_array( false, $found, true ) ^ $filter_inverse ) {
						$hidden_rows[] = $row_idx;
					}
					break;
			}
		}

		// Remove the rows that shall be hidden from table data and table visibility.
		foreach ( $hidden_rows as $row_idx ) {
			unset( $table['data'][ $row_idx ] );
			unset( $table['visibility']['rows'][ $row_idx ] );
		}
		// Reset array keys.
		$table['data'] = array_merge( $table['data'] );
		$table['visibility']['rows'] = array_merge( $table['visibility']['rows'] );

		return $table;
	}

	/**
	 * Removes columns that shall not be filtered from the data set.
	 *
	 * @since 2.0.0
	 *
	 * @param array<int, array<int, string>> $table_data     Full table data for the table to be filtered.
	 * @param string                         $filter_columns List of columns that shall be searched by the filter.
	 * @return array<int, array<int, string>> Reduced table data, that only contains the columns that shall be searched.
	 */
	protected static function _remove_not_filtered_columns( array $table_data, string $filter_columns ): array {
		// Add a range with all columns to the list if "" or "all" is set for the columns parameter.
		if ( 'all' === $filter_columns || '' === $filter_columns ) {
			return $table_data;
		}

		// We have a list of columns (possibly with ranges in it).
		$filter_columns = explode( ',', $filter_columns );
		// Support for ranges like 3-6 or A-BA.
		$range_cells = array();
		foreach ( $filter_columns as $key => $value ) {
			$range_dash = strpos( $value, '-' );
			if ( false !== $range_dash ) {
				unset( $filter_columns[ $key ] );
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
		$filter_columns = array_merge( $filter_columns, $range_cells );
		// Parse single letters.
		foreach ( $filter_columns as $key => $value ) {
			$value = trim( $value );
			if ( ! is_numeric( $value ) ) {
				$value = TablePress::letter_to_number( $value );
			}
			$filter_columns[ $key ] = (int) $value;
		}
		// Remove duplicate entries and sort the array.
		$filter_columns = array_unique( $filter_columns, SORT_NUMERIC );
		// Remove columns that shall not be filtered from the data.
		$dont_filter_columns = array_diff( range( 1, count( $table_data[0] ) ), $filter_columns );
		foreach ( $table_data as $row_idx => $row ) {
			foreach ( $dont_filter_columns as $col_idx ) {
				unset( $row[ $col_idx - 1 ] ); // -1 due to zero based indexing
			}
			$table_data[ $row_idx ] = array_merge( $row );
		}

		return $table_data;
	}

} // class TablePress_Module_Row_Filtering
