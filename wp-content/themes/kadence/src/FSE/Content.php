<?php
/**
 * Kadence\FSE\Content class
 *
 * @package kadence
 */

namespace Kadence\FSE;

use WP_HTML_Tag_Processor;
use function Kadence\kadence;

/**
 * Applies the per-page settings the block templates can't express in their markup.
 *
 * The templates carry the classic class names, so the blocks these settings apply
 * to are found by class.
 *
 * @since 1.6.0
 */
final class Content {

	/**
	 * Drops the title area or featured image when the page's settings hide them.
	 *
	 * Kadence decides this per request from Customizer options and post meta.
	 *
	 * @since 1.6.0
	 *
	 * @param string $block_content Rendered block.
	 * @param array  $block         Parsed block.
	 * @return string
	 */
	public function hide_title_and_feature( $block_content, $block ) {
		$classes = $this->get_classes( $block );

		if ( 'core/post-featured-image' === $block['blockName'] ) {
			return in_array( 'article-post-thumbnail', $classes, true ) && ! kadence()->show_feature() ? '' : $block_content;
		}

		if ( $this->is_title_area( $classes ) && 'hide' === kadence()->get_title_layout() ) {
			return '';
		}

		return $block_content;
	}

	/**
	 * Adds the class Kadence's post list styles target to each post in a Kadence post list.
	 *
	 * @since 1.6.0
	 *
	 * @param string $block_content Rendered block.
	 * @param array  $block         Parsed block.
	 * @return string
	 */
	public function add_list_item_class( $block_content, $block ) {
		if ( ! in_array( 'kadence-posts-list', $this->get_classes( $block ), true ) ) {
			return $block_content;
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );

		while ( $processor->next_tag(
			[
				'tag_name'   => 'li',
				'class_name' => 'wp-block-post',
			]
		) ) {
			$processor->add_class( 'entry-list-item' );
		}

		return $processor->get_updated_html();
	}

	/**
	 * Keeps the comment styles for a template that renders comments.
	 *
	 * The block templates always render the comments block when a post takes comments,
	 * while Kadence otherwise loads those styles from its own Customizer setting.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $enabled Whether the comment styles load.
	 * @return bool
	 */
	public function enable_comment_styles( $enabled ) {
		return comments_open() || get_comments_number() ? true : $enabled;
	}

	/**
	 * Turns the sidebar off, since the block templates have no sidebar area.
	 *
	 * @since 1.6.0
	 *
	 * @param array $layout The page's layout settings.
	 * @return array
	 */
	public function disable_sidebar( $layout ) {
		$layout['sidebar'] = 'disable';

		if ( isset( $layout['layout'] ) && in_array( $layout['layout'], [ 'left', 'right' ], true ) ) {
			$layout['layout'] = 'normal';
		}

		return $layout;
	}

	/**
	 * Whether a block is the title area: the hero, or an entry header for a page title.
	 *
	 * A post's header inside a post list has no title class, so it is never hidden.
	 *
	 * @since 1.6.0
	 *
	 * @param string[] $classes The block's classes.
	 * @return bool
	 */
	private function is_title_area( array $classes ): bool {
		if ( in_array( 'entry-hero', $classes, true ) ) {
			return true;
		}

		if ( ! in_array( 'entry-header', $classes, true ) ) {
			return false;
		}

		foreach ( $classes as $class ) {
			if ( '-title' === substr( $class, -6 ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets the custom classes on a block.
	 *
	 * @since 1.6.0
	 *
	 * @param array $block Parsed block.
	 * @return string[]
	 */
	private function get_classes( $block ): array {
		$class_name = $block['attrs']['className'] ?? '';

		return is_string( $class_name ) ? array_filter( explode( ' ', $class_name ) ) : [];
	}
}
