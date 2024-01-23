<?php
/**
 * Block component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.5.0
 */

namespace WebManDesign\Michelle\Content;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Assets;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Block implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.3.8
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'enqueue_block_editor_assets', __CLASS__ . '::enqueue_editor_mods' );

			// Filters

				// WP6.0+ fix:
				remove_filter( 'render_block', 'wp_render_layout_support_flag' );

				add_filter( 'register_post_type_args', __CLASS__ . '::register_reusable_blocks_args', 10, 2 );

				add_filter( 'render_block', __CLASS__ . '::render_block', 15, 2 );

	} // /init

	/**
	 * Block editor output modifications.
	 *
	 * @since    1.0.0
	 * @version  1.4.1
	 *
	 * @param  string $block_content  The pre-rendered content. Default null.
	 * @param  array  $block          The block being rendered.
	 *
	 * @return  void
	 */
	public static function render_block( string $block_content, array $block ): string {

		// Variables

			$attrs = $block['attrs'];

			/**
			 * Filter array of forced default block attribute values.
			 *
			 * Default block attribute values does not seem to be passed to `render_block` filter.
			 * @link  https://github.com/WordPress/gutenberg/issues/16365
			 *
			 * @since  1.0.0
			 *
			 * @param  array  $defaults       Array of default values for blocks.
			 * @param  string $block_content
			 * @param  array  $block
			 */
			$defaults = apply_filters( 'michelle/content/block/render_block/defaults', array(
				// Blocks with `wide` default alignment.
				'align:wide' => array(
					'core/media-text',
					'coblocks/media-card',
				),
			), $block_content, $block );

			if (
				in_array( $block['blockName'], $defaults['align:wide'] )
				&& ! isset( $attrs['align'] )
			) {
				$attrs['align'] = 'wide';
			}

			// Make sure the alignment attribute is set.
			if ( ! isset( $attrs['align'] ) ) {
				$attrs['align'] = null;
			}

			/**
			 * Compatibility with 3rd party block plugins.
			 * @link  https://wordpress.org/support/topic/align-attribute-name
			 */
			if ( null === $attrs['align'] && isset( $attrs['blockAlignment'] ) ) {
				$attrs['align'] = $attrs['blockAlignment'];
			}

			// Make sure the className attribute is set.
			if ( ! isset( $attrs['className'] ) ) {
				$attrs['className'] = '';
			}


		// Processing

			// WP6.0+ fix:
			// This must be first.
			if ( ! in_array( $block['blockName'], array(
				// See also `assets/js/editor-blocks.js`.
				'core/column',
				'core/columns',
			) ) ) {
				$block_content = wp_render_layout_support_flag( $block_content, $block );
			}

			// Wide align wrapper.
			if (
				'wide' == $attrs['align']
				|| false !== stripos( $attrs['className'], 'alignwide' )
			) {
				$atts = array(
					'class="alignwide-wrap"',
					'data-block="' . sanitize_title( str_replace( 'core/', '', $block['blockName'] ) ) . '"',
				);
				$block_content = '<div ' . implode( ' ', $atts ) . '>' . $block_content . '</div>';
			}

			// Image block left/right alignment.
			if (
				'core/image' === $block['blockName']
				&& in_array( $attrs['align'], array( 'left', 'right' ) )
			) {
				$block_content = str_replace(
					'wp-block-image',
					'wp-block-image align-horizontal-wrap',
					$block_content
				);
			}

			// Latest Posts block.
			if (
				'core/latest-posts' === $block['blockName']
				&& (
					! empty( $attrs['displayAuthor'] )
					|| ! empty( $attrs['displayPostDate'] )
				)
			) {
				$re = '/';

				if ( ! empty( $attrs['displayAuthor'] ) ) {
					$re .= '<div class="wp-block-latest-posts__post-author">';
				} else {
					$re .= '<time(.*?)>';
				}

				$re .= '(.*?)';

				if ( ! empty( $attrs['displayPostDate'] ) ) {
					$re .= '<\/time>';
				} else {
					$re .= '<\/div>';
				}

				$re .= '/s';

				$block_content = preg_replace( $re, '<div class="entry-meta">$0</div>', $block_content );
			}

			// Post Excerpt block.
			if ( 'core/post-excerpt' == $block['blockName'] ) {
				// Remove excerpt opening paragraph tag.
				$block_content = str_replace( '<p class="wp-block-post-excerpt__excerpt">', '', $block_content );
				// Remove excerpt closing paragraph tag (is `</p></div>`).
				$block_content = substr( $block_content, 0, -10 ) . '</div>';
				// Adding excerpt class back in.
				$block_content = str_replace( '"entry-summary', '"entry-summary wp-block-post-excerpt__excerpt', $block_content );
			}

			// Cover block.
			if (
				'core/cover' === $block['blockName']
				&& ! empty( $attrs['gradient'] )
			) {
				/**
				 * Modifying gradient CSS class applied onto the block container.
				 *
				 * This should not happen by default, but as we enable text color setup
				 * for Cover block, we get this weird erroneous behavior we need to fix.
				 * We invalidate the gradient CSS class applied on container only by
				 * appending `-overlay` to it.
				 */
				$re = '/wp-block-cover ([a-z0-9\-_\s]*?)-gradient-background/i';
				$block_content = preg_replace( $re, '$0-overlay', $block_content, 1 );
			}


		// Output

			return $block_content;

	} // /render_block

	/**
	 * Enqueues block editor assets for block modifications.
	 *
	 * @since    1.3.0
	 * @version  1.5.0
	 *
	 * @return  void
	 */
	public static function enqueue_editor_mods() {

		// Processing

			Assets\Factory::script_enqueue( array(
				'handle' => 'michelle-editor-blocks',
				'src'    => get_theme_file_uri( 'assets/js/editor-blocks.min.js' ),
				'deps'   => array( 'wp-blocks', 'wp-hooks', 'lodash' ),
			) );

	} // /enqueue_editor_mods

	/**
	 * Enable "Reusable blocks" in admin menu.
	 *
	 * @since  1.3.0
	 *
	 * @param  array  $args       Array of arguments for registering a post type.
	 * @param  string $post_type  Post type key.
	 *
	 * @return  array
	 */
	public static function register_reusable_blocks_args( array $args, string $post_type ): array {

		// Processing

			if ( 'wp_block' === $post_type ) {
				// Show under "Tools" menu item.
				$args['show_in_menu'] = 'tools.php';
			}


		// Output

			return $args;

	} // /register_reusable_blocks_args

}
