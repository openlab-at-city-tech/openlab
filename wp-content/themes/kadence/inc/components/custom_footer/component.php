<?php
/**
 * Kadence\Custom_Footer\Component class
 *
 * @package kadence
 */

namespace Kadence\Custom_Footer;

use Kadence\Component_Interface;
use Kadence\Templating_Component_Interface;
use function add_action;
use function apply_filters;
use function Kadence\kadence;
use function get_template_part;

/**
 * Class for adding custom footer support.
 *
 * Exposes template tags:
 * * `kadence()->render_footer()`
 * * `kadence()->display_footer_row()`
 * * `kadence()->footer_column_item_count()`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'custom_footer';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
	}
	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `kadence()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return array(
			'render_footer'            => array( $this, 'render_footer' ),
			'display_footer_row'       => array( $this, 'display_footer_row' ),
			'footer_column_item_count' => array( $this, 'footer_column_item_count' ),
		);
	}
	/**
	 * Checks to see if the row has any content.
	 *
	 * @param string $row the name of the row.
	 * @return bool
	 */
	public function display_footer_row( $row = 'middle' ) {
		$display = false;
		foreach ( array( '1', '2', '3', '4', '5' ) as $column ) {
			$elements = kadence()->option( 'footer_items' );
			if ( isset( $elements ) && isset( $elements[ $row ] ) && isset( $elements[ $row ][ $row . '_' . $column ] ) && is_array( $elements[ $row ][ $row . '_' . $column ] ) && ! empty( $elements[ $row ][ $row . '_' . $column ] ) ) {
				$display = true;
				break;
			}
		}
		return $display;
	}
	/**
	 * Adds support to render footer columns.
	 *
	 * @param string $row the name of the row.
	 * @param string $column the name of the column.
	 */
	public function render_footer( $row = 'middle', $column = '1' ) {
		$elements = kadence()->option( 'footer_items' );
		if ( isset( $elements ) && isset( $elements[ $row ] ) && isset( $elements[ $row ][ $row . '_' . $column ] ) && is_array( $elements[ $row ][ $row . '_' . $column ] ) && ! empty( $elements[ $row ][ $row . '_' . $column ] ) ) {
			foreach ( $elements[ $row ][ $row . '_' . $column ] as $key => $item ) {
				$template = apply_filters( 'kadence_footer_elements_template_path', 'template-parts/footer/' . $item, $item, $row, $column );
				get_template_part( $template );
			}
		}
	}
	/**
	 * Adds support to get the footer item count for a specific column.
	 *
	 * @param string $row the name of the row.
	 * @param string $column the name of the column.
	 */
	public function footer_column_item_count( $row = 'middle', $column = '1' ) {
		$count = 0;
		$elements = kadence()->option( 'footer_items' );
		if ( isset( $elements ) && isset( $elements[ $row ] ) && isset( $elements[ $row ][ $row . '_' . $column ] ) && is_array( $elements[ $row ][ $row . '_' . $column ] ) && ! empty( $elements[ $row ][ $row . '_' . $column ] ) ) {
			$count = count( $elements[ $row ][ $row . '_' . $column ] );
		}
		return $count;
	}
}
