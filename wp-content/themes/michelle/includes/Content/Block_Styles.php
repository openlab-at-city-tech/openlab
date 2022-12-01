<?php
/**
 * Block styles component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.8
 */

namespace WebManDesign\Michelle\Content;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Block_Styles implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Requirements check

			if ( ! function_exists( 'register_block_style' ) ) {
				return;
			}


		// Processing

			// Actions

				add_action( 'after_setup_theme', __CLASS__ . '::register' );

	} // /init

	/**
	 * Register block styles.
	 *
	 * @link  https://developer.wordpress.org/reference/functions/register_block_style/
	 *
	 * @since    1.0.0
	 * @version  1.2.0
	 *
	 * @return  void
	 */
	public static function register() {

		// Variables

			$styles = self::get_styles();


		// Processing

			foreach ( $styles as $class => $args ) {

				if (
					empty( $args['blocks'] )
					|| empty( $args['label'] )
				) {
					continue;
				}

				foreach ( (array) $args['blocks'] as $block ) {
					register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
						$block,
						array(
							'name'  => sanitize_html_class( $class ),
							'label' => esc_html( $args['label'] ),
						)
					);
				}
			}

	} // /register

	/**
	 * Gets theme block styles setup array.
	 *
	 * @since    1.0.0
	 * @version  1.3.8
	 *
	 * @return  array
	 */
	public static function get_styles(): array {

		// Variables

			$styles = array(

				'alignleft' => array(
					'label'  => _x( 'Single column: align left', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/column',
					),
				),
				'alignright' => array(
					'label'  => _x( 'Single column: align right', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/column',
					),
				),

				'double-line' => array(
					'label'  => _x( 'Double-line', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/separator',
					),
				),

				'double-line-wide' => array(
					'label'  => _x( 'Double-line wide', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/separator',
					),
				),

				'drop-shadow' => array(
					'label'  => _x( 'Drop shadow', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/column',
						'core/columns',
						'core/cover',
						'core/group',
						'core/image',
						'core/media-text',
						'core/video',
					),
				),

				'has-padding' => array(
					'label'  => _x( 'Has padding', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/columns',
					),
				),

				'hidden-on-tablet' => array(
					'label'  => _x( 'Hide on tablet', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/column',
					),
				),

				'inner-shadow' => array(
					'label'  => _x( 'Inner shadow', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/column',
						'core/cover',
						'core/group',
					),
				),

				'media-on-top' => array(
					'label'  => _x( 'Media on top', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/media-text',
					),
				),

				'no-gaps' => array(
					'label'  => _x( 'No gaps', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/columns',
						'core/gallery',
						'core/group',
					),
				),

				'no-margin-vertical' => array(
					'label'  => _x( 'No vertical gap', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/column',
						'core/columns',
						'core/cover',
						'core/gallery',
						'core/group',
						'core/heading',
						'core/image',
						'core/media-text',
						'core/paragraph',
						'core/post-content',
						'core/post-date',
						'core/post-excerpt',
						'core/post-featured-image',
						'core/post-terms',
						'core/post-title',
						'core/query',
					),
				),

				'overlap-gradient' => array(
					'label'  => _x( 'Overlap gradient', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/media-text',
					),
				),

				'padding-left' => array(
					'label'  => _x( 'Padding left', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/image',
					),
				),
				'padding-right' => array(
					'label'  => _x( 'Padding right', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/image',
					),
				),

				'scale-image' => array(
					'label'  => _x( 'Scale image', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/cover',
					),
				),

				'screen-reader-text' => array(
					'label'  => _x( 'Accessibly hidden', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/heading',
					),
				),

				'simple' => array(
					'label'  => _x( 'Simple', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/pullquote',
					),
				),

				'stacked-on-tablet' => array(
					'label'  => _x( 'Stack on tablet', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/columns',
						'core/media-text',
					),
				),

				'text-shadow-dark' => array(
					'label'  => _x( 'Dark text shadow', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/heading',
						'core/paragraph',
					),
				),

				'text-shadow-light' => array(
					'label'  => _x( 'Light text shadow', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/heading',
						'core/paragraph',
					),
				),

				'uppercase' => array(
					'label'  => _x( 'Uppercase', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/heading',
						'core/paragraph',
					),
				),

				'variable' => array(
					'label'  => _x( 'Variable', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/gallery',
					),
				),

				'zigzag' => array(
					'label'  => _x( 'Zig-zag', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/separator',
					),
				),

				'zigzag-wide' => array(
					'label'  => _x( 'Zig-zag wide', 'Block style label.', 'michelle' ),
					'blocks' => array(
						'core/separator',
					),
				),

			);


		// Output

			/**
			 * Filters block styles setup array.
			 *
			 * @example
			 *   array(
			 *     'css-class' => array(
			 *       'label'  => (string) 'Block style label',
			 *       'blocks' => array( 'core/block-identifier' ),
			 *     ),
			 *   )
			 *
			 * @since  1.0.0
			 *
			 * @param  array $styles
			 */
			return (array) apply_filters( 'michelle/content/block_styles/get_styles', $styles );

	} // /get_styles

}
