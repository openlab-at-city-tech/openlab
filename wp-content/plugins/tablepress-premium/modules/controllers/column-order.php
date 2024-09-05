<?php
/**
 * TablePress Column Order.
 *
 * @package TablePress
 * @subpackage Column Order.
 * @author Tobias Bäthge
 * @since 2.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class that contains the logic for the Column Order feature for TablePress.
 *
 * @author Tobias Bäthge
 * @since 2.0.0
 */
class TablePress_Module_Column_Order {
	use TablePress_Module; // Use properties and methods from trait.

	/**
	 * Registers necessary plugin filter hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'tablepress_table_template', array( __CLASS__, 'add_option_to_table_template' ) );
		add_filter( 'tablepress_shortcode_table_default_shortcode_atts', array( __CLASS__, 'add_shortcode_parameters' ) );
		add_filter( 'tablepress_datatables_serverside_processing_render_options', array( __CLASS__, 'add_serverside_processing_render_options' ), 10, 3 );
		add_filter( 'tablepress_table_render_options', array( __CLASS__, 'turn_off_caching' ), 10, 2 );
		add_filter( 'tablepress_table_render_data', array( __CLASS__, 'after_render_processing' ), 10, 3 );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_block_editor_js' ) );
	}

	/**
	 * Adds options related to Column Order to the table template.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table Current table template.
	 * @return array<string, mixed> Extended table template.
	 */
	public static function add_option_to_table_template( array $table ): array {
		$table['options']['column_order'] = 'default';
		$table['options']['column_order_manual_order'] = '';
		return $table;
	}

	/**
	 * Adds parameters for the Column Order feature to the [table /] Shortcode.
	 *
	 * By using null as the default value, the table options's value will be used (if set).
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $default_atts Default Shortcode attributes.
	 * @return array<string, mixed> Extended Shortcode attributes.
	 */
	public static function add_shortcode_parameters( array $default_atts ): array {
		$default_atts['column_order'] = null;
		$default_atts['column_order_manual_order'] = null;
		return $default_atts;
	}

	/**
	 * Adds parameters for the Column Order feature to the DataTables Server-side Processing render options.
	 *
	 * @since 2.1.0
	 *
	 * @param array<string, mixed> $render_options_ssp Render Options list for Server-side Processing.
	 * @param string               $table_id           Table ID.
	 * @param array<string, mixed> $render_options     Render Options.
	 * @return string[] Modified Render Options list for Server-side Processing.
	 */
	public static function add_serverside_processing_render_options( array $render_options_ssp, string $table_id, array $render_options ): array {
		$render_options_ssp[] = 'column_order';
		$render_options_ssp[] = 'column_order_manual_order';
		return $render_options_ssp;
	}

	/**
	 * Registers the module's JS script for the block editor.
	 *
	 * @since 2.0.0
	 */
	public static function enqueue_block_editor_js(): void {
		TablePress_Modules_Helper::enqueue_script( 'column-order-block' );
	}

	/**
	 * Deactivates Table Output caching, if the "random" column order is used.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $render_options Render Options.
	 * @param array<string, mixed> $table          Table.
	 * @return array<string, mixed> Modified Render Options.
	 */
	public static function turn_off_caching( array $render_options, array $table ): array {
		if ( 'random' === $render_options['column_order'] ) {
			$render_options['cache_table_output'] = false;
		}

		return $render_options;
	}

	/**
	 * Change the order of the columns, for "random", "reverse", and "manual" order.
	 *
	 * @since 2.0.0
	 *
	 * @param array<string, mixed> $table          Table.
	 * @param array<string, mixed> $orig_table     Original table.
	 * @param array<string, mixed> $render_options Render Options.
	 * @return array<string, mixed> Modified table.
	 */
	public static function after_render_processing( array $table, array $orig_table, array $render_options ): array {
		if ( 'default' === $render_options['column_order'] ) {
			return $table;
		}

		// Exit early if there's no actual table data (e.g. after using the Row Filter module).
		if ( 0 === count( $table['data'] ) ) {
			return $table;
		}

		$num_columns = count( $table['data'][0] );

		switch ( $render_options['column_order'] ) {
			case 'random':
				if ( $render_options['first_column_th'] ) {
					$column_order = array( 1 );
					$range = range( 2, $num_columns );
					shuffle( $range );
					$column_order = array_merge( $column_order, $range );
				} else {
					$column_order = range( 1, $num_columns );
					shuffle( $column_order );
				}
				break;

			case 'reverse':
				if ( $render_options['first_column_th'] ) {
					$column_order = array( 1 );
					$range = range( $num_columns, 2 ); // Reverse order.
					$column_order = array_merge( $column_order, $range );
				} else {
					$column_order = range( $num_columns, 1 ); // Reverse order.
				}
				break;

			case 'manual':
				$original_column_order = $render_options['column_order_manual_order'];

				if ( '' === $original_column_order ) {
					return $table;
					// break; // unreachable.
				}

				// We have a list of rows (possibly with ranges in it).
				$original_column_order = explode( ',', $original_column_order );
				$column_order = array();

				foreach ( $original_column_order as $key => $value ) {
					$value = trim( $value );

					$num_columns = (string) $num_columns;

					// Convert keywords to corresponding row numbers or ranges.
					if ( 'all' === $value ) {
						$value = '1-' . $num_columns;
					} elseif ( 'reverse' === $value ) {
						$value = $num_columns . '-1';
					} elseif ( 'last' === $value ) {
						$value = $num_columns;
					}

					// Possibly expand ranges.
					$range_dash = strpos( $value, '-' );
					if ( false !== $range_dash ) {
						// Range.
						$start = trim( substr( $value, 0, $range_dash ) );
						if ( ! is_numeric( $start ) ) {
							$start = TablePress::letter_to_number( $start );
						}
						$end = trim( substr( $value, $range_dash + 1 ) );
						if ( ! is_numeric( $end ) ) {
							$end = TablePress::letter_to_number( $end );
						}
						$value = range( $start, $end );
					} else {
						// No range.
						if ( ! is_numeric( $value ) ) {
							$value = TablePress::letter_to_number( $value );
						}
						$value = array( $value );
					}

					$column_order = array_merge( $column_order, $value );
				}
				break;

			default:
				return $table;
				// break; // unreachable.
		}

		// Convert numbers to indices.
		foreach ( $column_order as $idx => $column_number ) {
			$column_order[ $idx ] = absint( $column_number ) - 1;
		}

		// Build new table.
		foreach ( $table['data'] as $row_idx => $row ) {
			$new_row = array();
			foreach ( $column_order as $idx => $column_number ) {
				if ( isset( $row[ $column_number ] ) ) {
					$new_row[] = $row[ $column_number ];
				}
			}
			if ( ! empty( $new_row ) ) {
				$table['data'][ $row_idx ] = $new_row;
			}
		}

		return $table;
	}

} // class TablePress_Module_Column_Order
