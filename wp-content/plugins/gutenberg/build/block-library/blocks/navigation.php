<?php
/**
 * Server-side rendering of the `core/navigation` block.
 *
 * @package gutenberg
 */

/**
 * Build an array with CSS classes and inline styles defining the colors
 * which will be applied to the navigation markup in the front-end.
 *
 * @param  array $attributes Navigation block attributes.
 * @return array Colors CSS classes and inline styles.
 */
function gutenberg_block_core_navigation_build_css_colors( $attributes ) {
	$colors = array(
		'css_classes'   => array(),
		'inline_styles' => '',
	);

	// Text color.
	$has_named_text_color  = array_key_exists( 'textColor', $attributes );
	$has_custom_text_color = array_key_exists( 'customTextColor', $attributes );

	// If has text color.
	if ( $has_custom_text_color || $has_named_text_color ) {
		// Add has-text-color class.
		$colors['css_classes'][] = 'has-text-color';
	}

	if ( $has_named_text_color ) {
		// Add the color class.
		$colors['css_classes'][] = sprintf( 'has-%s-color', $attributes['textColor'] );
	} elseif ( $has_custom_text_color ) {
		// Add the custom color inline style.
		$colors['inline_styles'] .= sprintf( 'color: %s;', $attributes['customTextColor'] );
	}

	// Background color.
	$has_named_background_color  = array_key_exists( 'backgroundColor', $attributes );
	$has_custom_background_color = array_key_exists( 'customBackgroundColor', $attributes );

	// If has background color.
	if ( $has_custom_background_color || $has_named_background_color ) {
		// Add has-background class.
		$colors['css_classes'][] = 'has-background';
	}

	if ( $has_named_background_color ) {
		// Add the background-color class.
		$colors['css_classes'][] = sprintf( 'has-%s-background-color', $attributes['backgroundColor'] );
	} elseif ( $has_custom_background_color ) {
		// Add the custom background-color inline style.
		$colors['inline_styles'] .= sprintf( 'background-color: %s;', $attributes['customBackgroundColor'] );
	}

	return $colors;
}

/**
 * Build an array with CSS classes and inline styles defining the font sizes
 * which will be applied to the navigation markup in the front-end.
 *
 * @param  array $attributes Navigation block attributes.
 * @return array Font size CSS classes and inline styles.
 */
function gutenberg_block_core_navigation_build_css_font_sizes( $attributes ) {
	// CSS classes.
	$font_sizes = array(
		'css_classes'   => array(),
		'inline_styles' => '',
	);

	$has_named_font_size  = array_key_exists( 'fontSize', $attributes );
	$has_custom_font_size = array_key_exists( 'customFontSize', $attributes );

	if ( $has_named_font_size ) {
		// Add the font size class.
		$font_sizes['css_classes'][] = sprintf( 'has-%s-font-size', $attributes['fontSize'] );
	} elseif ( $has_custom_font_size ) {
		// Add the custom font size inline style.
		$font_sizes['inline_styles'] = sprintf( 'font-size: %spx;', $attributes['customFontSize'] );
	}

	return $font_sizes;
}

/**
 * Returns the top-level submenu SVG chevron icon.
 *
 * @return string
 */
function gutenberg_block_core_navigation_render_submenu_icon() {
	return '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" role="img" aria-hidden="true" focusable="false"><path d="M1.50002 4L6.00002 8L10.5 4" stroke-width="1.5"></path></svg>';
}

/**
 * Renders the `core/navigation` block on server.
 *
 * @param array $attributes The block attributes.
 * @param array $content The saved content.
 * @param array $block The parsed block.
 *
 * @return string Returns the post content with the legacy widget added.
 */
function gutenberg_render_block_core_navigation( $attributes, $content, $block ) {
	/**
	 * Deprecated:
	 * The rgbTextColor and rgbBackgroundColor attributes
	 * have been deprecated in favor of
	 * customTextColor and customBackgroundColor ones.
	 * Move the values from old attrs to the new ones.
	 */
	if ( isset( $attributes['rgbTextColor'] ) && empty( $attributes['textColor'] ) ) {
		$attributes['customTextColor'] = $attributes['rgbTextColor'];
	}

	if ( isset( $attributes['rgbBackgroundColor'] ) && empty( $attributes['backgroundColor'] ) ) {
		$attributes['customBackgroundColor'] = $attributes['rgbBackgroundColor'];
	}

	unset( $attributes['rgbTextColor'], $attributes['rgbBackgroundColor'] );
	$should_load_frontend_script = $attributes['isResponsive'] && ! wp_script_is( 'core_block_navigation_load_frontend_scripts' );

	if ( $should_load_frontend_script ) {
		wp_enqueue_script(
			'core_block_navigation_load_frontend_scripts',
			plugins_url( 'frontend.js', __DIR__ . '/navigation/frontend.js' ),
			array(),
			false,
			true
		);
	}

	if ( empty( $block->inner_blocks ) ) {
		return '';
	}

	$colors     = gutenberg_block_core_navigation_build_css_colors( $attributes );
	$font_sizes = gutenberg_block_core_navigation_build_css_font_sizes( $attributes );
	$classes    = array_merge(
		$colors['css_classes'],
		$font_sizes['css_classes'],
		( isset( $attributes['orientation'] ) && 'vertical' === $attributes['orientation'] ) ? array( 'is-vertical' ) : array(),
		isset( $attributes['itemsJustification'] ) ? array( 'items-justified-' . $attributes['itemsJustification'] ) : array(),
		isset( $attributes['isResponsive'] ) && true === $attributes['isResponsive'] ? array( 'is-responsive' ) : array()
	);

	$inner_blocks_html = '';
	foreach ( $block->inner_blocks as $inner_block ) {
		$inner_blocks_html .= $inner_block->render();
	}

	$block_styles = isset( $attributes['styles'] ) ? $attributes['styles'] : '';

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => implode( ' ', $classes ),
			'style' => $block_styles . $colors['inline_styles'] . $font_sizes['inline_styles'],
		)
	);

	$modal_unique_id = uniqid();

	// Determine whether or not navigation elements should be wrapped in the markup required to make it responsive,
	// return early if they don't.
	if ( ! isset( $attributes['isResponsive'] ) || false === $attributes['isResponsive'] ) {
		return sprintf(
			'<nav %1$s><ul class="wp-block-navigation__container">%2$s</ul></nav>',
			$wrapper_attributes,
			$inner_blocks_html
		);
	}

	$responsive_container_markup = sprintf(
		'<button aria-expanded="false" aria-haspopup="true" aria-label="%3$s" class="wp-block-navigation__responsive-container-open" data-micromodal-trigger="modal-%1$s"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false"><rect x="4" y="7.5" width="16" height="1.5" /><rect x="4" y="15" width="16" height="1.5" /></svg></button>
			<div class="wp-block-navigation__responsive-container" id="modal-%1$s" aria-hidden="true">
				<div class="wp-block-navigation__responsive-close" tabindex="-1" data-micromodal-close>
					<div class="wp-block-navigation__responsive-dialog" role="dialog" aria-modal="true" aria-labelledby="modal-%1$s-title" >
							<button aria-label="%4$s" data-micromodal-close class="wp-block-navigation__responsive-container-close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" role="img" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg></button>
						<div class="wp-block-navigation__responsive-container-content" id="modal-%1$s-content">
							<ul class="wp-block-navigation__container">%2$s</ul>
						</div>
					</div>
				</div>
			</div>',
		$modal_unique_id,
		$inner_blocks_html,
		__( 'Open menu' ), // Open button label.
		__( 'Close menu' ) // Close button label.
	);

	return sprintf(
		'<nav %1$s>%2$s</nav>',
		$wrapper_attributes,
		$responsive_container_markup
	);
}

/**
 * Register the navigation block.
 *
 * @uses gutenberg_render_block_core_navigation()
 * @throws WP_Error An WP_Error exception parsing the block definition.
 */
function gutenberg_register_block_core_navigation() {
	register_block_type_from_metadata(
		__DIR__ . '/navigation',
		array(
			'render_callback' => 'gutenberg_render_block_core_navigation',
		)
	);
}

add_action( 'init', 'gutenberg_register_block_core_navigation', 20 );

/**
 * Filter that changes the parsed attribute values of navigation blocks contain typographic presets to contain the values directly.
 *
 * @param array $parsed_block The block being rendered.
 * @return array The block being rendered without typographic presets.
 */
function gutenberg_block_core_navigation_typographic_presets_backcompatibility( $parsed_block ) {
	if ( 'core/navigation' === $parsed_block['blockName'] ) {
		$attribute_to_prefix_map = array(
			'fontStyle'      => 'var:preset|font-style|',
			'fontWeight'     => 'var:preset|font-weight|',
			'textDecoration' => 'var:preset|text-decoration|',
			'textTransform'  => 'var:preset|text-transform|',
		);
		foreach ( $attribute_to_prefix_map as $style_attribute => $prefix ) {
			if ( ! empty( $parsed_block['attrs']['style']['typography'][ $style_attribute ] ) ) {
				$prefix_len      = strlen( $prefix );
				$attribute_value = &$parsed_block['attrs']['style']['typography'][ $style_attribute ];
				if ( 0 === strncmp( $attribute_value, $prefix, $prefix_len ) ) {
					$attribute_value = substr( $attribute_value, $prefix_len );
				}
				if ( 'textDecoration' === $style_attribute && 'strikethrough' === $attribute_value ) {
					$attribute_value = 'line-through';
				}
			}
		}
	}
	return $parsed_block;
}

add_filter( 'render_block_data', 'gutenberg_block_core_navigation_typographic_presets_backcompatibility' );
