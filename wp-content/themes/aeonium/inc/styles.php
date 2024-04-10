<?php
/**
 *
 * Adds custom Block Styles to the editor.
 *
 * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-styles/
 *
 * @package Aeonium
 */

/**
 * Array of block styles.
 */
if ( ! function_exists( 'aeonium_block_styles' ) ) {
	function aeonium_block_styles() {
		return array(
			'posts-equal-height' => array(
				'label' => __( 'Posts equal height', 'aeonium' ),
				'blocks' => 'query'
			),
			'hover-zoom' => array(
				'label' => __( 'Zoom on hover', 'aeonium' ),
				'blocks' => 'post-featured-image,image,cover'
			),
			'hover-zoom-img' => array(
				'label' => __( 'Zoom on image hover', 'aeonium' ),
				'blocks' => 'media-text'
			),
			'hover-zoom-block' => array(
				'label' => __( 'Zoom on block hover', 'aeonium' ),
				'blocks' => 'media-text'
			),
			'no-overflow' => array(
				'label' => __( 'No overflow', 'aeonium' ),
				'blocks' => 'group'
			),
			'hover-effect-light' => array(
				'label' => __( 'Hover effect (light)', 'aeonium' ),
				'blocks' => 'button'
			),
			'hover-effect-dark' => array(
				'label' => __( 'Hover effect (dark)', 'aeonium' ),
				'blocks' => 'button'
			),
			'links-plain' => array(
				'label' => __( 'Links - plain', 'aeonium' ),
				'blocks' => 'paragraph,heading,site-title,site-tagline,post-title,query-title,post-author,post-terms,query-pagination-previous,query-pagination-next,query-pagination-numbers'
			),
			'links-underline-on-hover' => array(
				'label' => __( 'Links - underline on hover', 'aeonium' ),
				'blocks' => 'paragraph,heading,site-title,site-tagline,post-title,query-title,post-author,post-terms,query-pagination-previous,query-pagination-next,query-pagination-numbers'
			),
			'zero-top-margin' => array(
				'label' => __( 'Zero top margin', 'aeonium' ),
				'blocks' => 'paragraph,image,media-text'
			),
			'partial-border-small' => array(
				'label' => __( 'Partial border (small)', 'aeonium' ),
				'blocks' => 'group'
			),
			'partial-border-medium' => array(
				'label' => __( 'Partial border (medium)', 'aeonium' ),
				'blocks' => 'group'
			),
			'partial-border-large' => array(
				'label' => __( 'Partial border (large)', 'aeonium' ),
				'blocks' => 'group'
			),
			'separators' => array(
				'label' => __( 'Separators', 'aeonium' ),
				'blocks' => 'navigation'
			),
			'separators-accent' => array(
				'label' => __( 'Separators (accent color)', 'aeonium' ),
				'blocks' => 'navigation'
			),
			'separators-accent-2' => array(
				'label' => __( 'Separators (accent 2 color)', 'aeonium' ),
				'blocks' => 'navigation'
			),
			'separators-accent-3' => array(
				'label' => __( 'Separators (accent 3 color)', 'aeonium' ),
				'blocks' => 'navigation'
			),
			'no-separator' => array(
				'label' => __( 'No separator', 'aeonium' ),
				'blocks' => 'navigation-link,navigation-submenu'
			),
			'circle' => array(
				'label' => __( 'Circle', 'aeonium' ),
				'blocks' => 'list'
			),
			'disc' => array(
				'label' => __( 'Disc', 'aeonium' ),
				'blocks' => 'list'
			),
			'square' => array(
				'label' => __( 'Square', 'aeonium' ),
				'blocks' => 'list'
			),
			'line' => array(
				'label' => __( 'Line', 'aeonium' ),
				'blocks' => 'list'
			),
			'check' => array(
				'label' => __( 'Check', 'aeonium' ),
				'blocks' => 'list'
			),
			'cross' => array(
				'label' => __( 'Cross', 'aeonium' ),
				'blocks' => 'list'
			),
			'star' => array(
				'label' => __( 'Star', 'aeonium' ),
				'blocks' => 'list'
			),
			'star-outline' => array(
				'label' => __( 'Star Outline', 'aeonium' ),
				'blocks' => 'list'
			),
			'heart' => array(
				'label' => __( 'Heart', 'aeonium' ),
				'blocks' => 'list'
			),
			'arrow' => array(
				'label' => __( 'Arrow', 'aeonium' ),
				'blocks' => 'list'
			),
			'chevron' => array(
				'label' => __( 'Chevron', 'aeonium' ),
				'blocks' => 'list'
			),
			'asterisk' => array(
				'label' => __( 'Asterisk', 'aeonium' ),
				'blocks' => 'list'
			),
			'none' => array(
				'label' => __( 'No Style', 'aeonium' ),
				'blocks' => 'list'
			),
		);
	}
}


/**
 * Register the block styles.
 */
function aeonium_register_block_styles() {
	$block_styles = aeonium_block_styles();
	foreach ( $block_styles as $block_style => $attrs ) {
		if ( isset($attrs['label']) && $attrs['label'] !== '' ) {
			$label = $attrs['label'];
		} else {
			$label = $block_style;
		}
		if ( isset($attrs['handle']) && $attrs['handle'] !== '' ) {
			$handle = $attrs['handle'];
		} else {
			$handle = 'aeonium-style';
		}
		if ( isset($attrs['style']) && $attrs['style'] !== '' ) {
			$style = $attrs['style'];
		} else {
			$style = '';
		}
		$blocks = explode( ',', $attrs['blocks'] );
		$block_count = 0;
		foreach ( $blocks as $block ) {
			$block_count++;
			if ( strpos( $block, '/' ) !== false ) {
				$block = $block;
			} else {
				$block = 'core/' . $block;
			}
			if ( $block_count > 1 ) {
				$style = '';
			}
			register_block_style(
				$block,
				array(
					'name' => $block_style,
					'label'	=> $label,
					'style_handle' => $handle,
					'inline_style' => $style
				)
			);
		}
	}
}
add_action( 'init', 'aeonium_register_block_styles' );
