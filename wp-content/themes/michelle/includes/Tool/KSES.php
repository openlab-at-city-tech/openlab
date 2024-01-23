<?php
/**
 * KSES component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Tool;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class KSES implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Filters

				add_filter( 'wp_kses_allowed_html', __CLASS__ . '::tags', 10, 2 );

	} // /init

	/**
	 * HTML tags allowed specific custom theme context.
	 *
	 * You can then use `wp_kses( $html, 'context' );`
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  array  $data
	 * @param  string $context
	 *
	 * @return  array
	 */
	public static function tags( array $tags, string $context ): array {

		// Output

			switch ( $context ) {

				case 'inline':
					return array(
						'br'     => array(),
						'code'   => array(),
						'em'     => array(),
						'mark'   => array(),
						'strong' => array(),

						'a' => array(
							'href'   => array(),
							'class'  => array(),
							'rel'    => array(),
							'title'  => array(),
							'target' => array(),
						),

						'span' => array(
							'class' => array(),
							'style' => array(),
						),
					);

				case 'option_description':
					return array(
						'big'  => array(),
						'br'   => array(),
						'code' => array(),
						'em'   => array(),
						'mark' => array(),

						'a' => array(
							'href'   => array(),
							'class'  => array(),
							'rel'    => array(),
							'title'  => array(),
							'target' => array(),
						),

						'h2' => array(
							'class' => array(),
							'id'    => array(),
						),
						'h3' => array(
							'class' => array(),
							'id'    => array(),
						),
						'h4' => array(
							'class' => array(),
							'id'    => array(),
						),
						'p' => array(
							'class' => array(),
							'id'    => array(),
						),

						'span' => array(
							'class' => array(),
							'style' => array(),
						),

						'strong' => array(
							'class' => array(),
							'style' => array(),
						),
					);

				default:
					return $tags;

			}

	} // /tags

}
