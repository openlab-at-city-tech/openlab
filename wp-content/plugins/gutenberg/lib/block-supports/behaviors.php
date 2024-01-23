<?php
/**
 * Behaviors block support flag.
 *
 * This file will NOT be backported to Core. It exists to provide a
 * migration path for theme.json files that used the deprecated "behaviors".
 * This file will be removed from Gutenberg in version 17.0.0.
 *
 * @package gutenberg
 */

/**
 * Registers the behaviors block attribute for block types that support it.
 * And add the needed hooks to add those behaviors.
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function gutenberg_register_behaviors_support( $block_type ) {
	$has_behaviors_support = block_has_support( $block_type, array( 'behaviors' ), false );
	if ( ! $has_behaviors_support ) {
		return;
	}

	if ( ! $block_type->attributes ) {
		$block_type->attributes = array();
	}

	$block_type->attributes['behaviors'] = array(
		'type' => 'object',
	);

	// If it supports the lightbox behavior, add the hook to that block.
	// In the future, this should be a loop with all the behaviors.
	$has_lightbox_support = block_has_support( $block_type, array( 'behaviors', 'lightbox' ), false );
	if ( $has_lightbox_support ) {
		// Use priority 15 to run this hook after other hooks/plugins.
		// They could use the `render_block_{$this->name}` filter to modify the markup.
		add_filter( 'render_block_' . $block_type->name, 'gutenberg_render_behaviors_support_lightbox', 15, 2 );
	}
}

/**
 * Add the directives and layout needed for the lightbox behavior.
 *
 * @param  string $block_content Rendered block content.
 * @param  array  $block         Block object.
 * @return string                Filtered block content.
 */
function gutenberg_render_behaviors_support_lightbox( $block_content, $block ) {

	// We've deprecated the lightbox implementation via behaviors.
	// While we may continue to explore behaviors in the future, the lightbox
	// logic seems very specific to the image and will likely never be a part
	// of behaviors, even in the future. With that in mind, we've rewritten the lightbox
	// to be a feature of the image block and will also soon remove the block_supports.
	// *Note: This logic for generating the lightbox markup has been duplicated and moved
	// to the image block's index.php.*
	// See https://github.com/WordPress/gutenberg/issues/53403.
	_deprecated_function( 'gutenberg_render_behaviors_support_lightbox', 'Gutenberg 17.0.0', '' );

	$link_destination = isset( $block['attrs']['linkDestination'] ) ? $block['attrs']['linkDestination'] : 'none';
	// Get the lightbox setting from the block attributes.
	if ( isset( $block['attrs']['behaviors']['lightbox'] ) ) {
		$lightbox_settings = $block['attrs']['behaviors']['lightbox'];
		// If the lightbox setting is not set in the block attributes, get it from the theme.json file.
	} else {
		$theme_data = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data()->get_data();
		if ( isset( $theme_data['behaviors']['blocks'][ $block['blockName'] ]['lightbox'] ) ) {
			$lightbox_settings = $theme_data['behaviors']['blocks'][ $block['blockName'] ]['lightbox'];
		} else {
			$lightbox_settings = null;
		}
	}

	if ( isset( $lightbox_settings['enabled'] ) && false === $lightbox_settings['enabled'] ) {
		return $block_content;
	}

	if ( ! $lightbox_settings || 'none' !== $link_destination ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );

	$aria_label = __( 'Enlarge image', 'gutenberg' );

	$processor->next_tag( 'img' );
	$alt_attribute = $processor->get_attribute( 'alt' );

	// An empty alt attribute `alt=""` is valid for decorative images.
	if ( is_string( $alt_attribute ) ) {
		$alt_attribute = trim( $alt_attribute );
	}

	// It only makes sense to append the alt text to the button aria-label when the alt text is non-empty.
	if ( $alt_attribute ) {
		/* translators: %s: Image alt text. */
		$aria_label = sprintf( __( 'Enlarge image: %s', 'gutenberg' ), $alt_attribute );
	}

	// If we don't set a default, it won't work if Lightbox is set to enabled by default.
	$lightbox_animation = 'zoom';
	if ( isset( $lightbox_settings['animation'] ) && '' !== $lightbox_settings['animation'] ) {
		$lightbox_animation = $lightbox_settings['animation'];
	}

	// Note: We want to store the `src` in the context so we
	// can set it dynamically when the lightbox is opened.
	if ( isset( $block['attrs']['id'] ) ) {
		$img_uploaded_src = wp_get_attachment_url( $block['attrs']['id'] );
		$img_metadata     = wp_get_attachment_metadata( $block['attrs']['id'] );
		$img_width        = $img_metadata['width'] ?? 'none';
		$img_height       = $img_metadata['height'] ?? 'none';
	} else {
		$img_uploaded_src = $processor->get_attribute( 'src' );
		$img_width        = 'none';
		$img_height       = 'none';
	}

	if ( isset( $block['attrs']['scale'] ) ) {
		$scale_attr = $block['attrs']['scale'];
	} else {
		$scale_attr = false;
	}

	$w = new WP_HTML_Tag_Processor( $block_content );
	$w->next_tag( 'figure' );
	$w->add_class( 'wp-lightbox-container' );
	$w->set_attribute( 'data-wp-interactive', true );

	$w->set_attribute(
		'data-wp-context',
		sprintf(
			'{ "core":
				{ "image":
					{   "imageLoaded": false,
						"initialized": false,
						"lightboxEnabled": false,
						"hideAnimationEnabled": false,
						"preloadInitialized": false,
						"lightboxAnimation": "%s",
						"imageUploadedSrc": "%s",
						"imageCurrentSrc": "",
						"targetWidth": "%s",
						"targetHeight": "%s",
						"scaleAttr": "%s"
					}
				}
			}',
			$lightbox_animation,
			$img_uploaded_src,
			$img_width,
			$img_height,
			$scale_attr
		)
	);
	$w->next_tag( 'img' );
	$w->set_attribute( 'data-wp-init', 'effects.core.image.setCurrentSrc' );
	$w->set_attribute( 'data-wp-on--load', 'actions.core.image.handleLoad' );
	$w->set_attribute( 'data-wp-effect', 'effects.core.image.setButtonStyles' );
	$body_content = $w->get_updated_html();

	// Wrap the image in the body content with a button.
	$img = null;
	preg_match( '/<img[^>]+>/', $body_content, $img );

	$button =
		$img[0]
		. '<button
			type="button"
			aria-haspopup="dialog"
			aria-label="' . esc_attr( $aria_label ) . '"
			data-wp-on--click="actions.core.image.showLightbox"
			data-wp-style--width="context.core.image.imageButtonWidth"
			data-wp-style--height="context.core.image.imageButtonHeight"
			data-wp-style--left="context.core.image.imageButtonLeft"
			data-wp-style--top="context.core.image.imageButtonTop"
		></button>';

	$body_content = preg_replace( '/<img[^>]+>/', $button, $body_content );

	// We need both a responsive image and an enlarged image to animate
	// the zoom seamlessly on slow internet connections; the responsive
	// image is a copy of the one in the body, which animates immediately
	// as the lightbox is opened, while the enlarged one is a full-sized
	// version that will likely still be loading as the animation begins.
	$m = new WP_HTML_Tag_Processor( $block_content );
	$m->next_tag( 'figure' );
	$m->add_class( 'responsive-image' );
	$m->next_tag( 'img' );
	// We want to set the 'src' attribute to an empty string in the responsive image
	// because otherwise, as of this writing, the wp_filter_content_tags() function in
	// WordPress will automatically add a 'srcset' attribute to the image, which will at
	// times cause the incorrectly sized image to be loaded in the lightbox on Firefox.
	// Because of this, we bind the 'src' attribute explicitly the current src to reliably
	// use the exact same image as in the content when the lightbox is first opened while
	// we wait for the larger image to load.
	$m->set_attribute( 'src', '' );
	$m->set_attribute( 'data-wp-bind--src', 'context.core.image.imageCurrentSrc' );
	$m->set_attribute( 'data-wp-style--object-fit', 'selectors.core.image.lightboxObjectFit' );
	$initial_image_content = $m->get_updated_html();

	$q = new WP_HTML_Tag_Processor( $block_content );
	$q->next_tag( 'figure' );
	$q->add_class( 'enlarged-image' );
	$q->next_tag( 'img' );

	// We set the 'src' attribute to an empty string to prevent the browser from loading the image
	// on initial page load, then bind the attribute to a selector that returns the full-sized image src when
	// the lightbox is opened. We could use 'loading=lazy' in combination with the 'hidden' attribute to
	// accomplish the same behavior, but that approach breaks progressive loading of the image in Safari
	// and Chrome (see https://github.com/WordPress/gutenberg/pull/52765#issuecomment-1674008151). Until that
	// is resolved, manually setting the 'src' seems to be the best solution to load the large image on demand.
	$q->set_attribute( 'src', '' );
	$q->set_attribute( 'data-wp-bind--src', 'selectors.core.image.enlargedImgSrc' );
	$q->set_attribute( 'data-wp-style--object-fit', 'selectors.core.image.lightboxObjectFit' );
	$enlarged_image_content = $q->get_updated_html();

	$background_color = esc_attr( wp_get_global_styles( array( 'color', 'background' ) ) );

	$close_button_icon  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" aria-hidden="true" focusable="false"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg>';
	$close_button_color = esc_attr( wp_get_global_styles( array( 'color', 'text' ) ) );
	$dialog_label       = esc_attr__( 'Enlarged image', 'gutenberg' );
	$close_button_label = esc_attr__( 'Close', 'gutenberg' );

	$lightbox_html = <<<HTML
        <div data-wp-body="" class="wp-lightbox-overlay $lightbox_animation"
            data-wp-bind--role="selectors.core.image.roleAttribute"
            aria-label="$dialog_label"
            data-wp-class--initialized="context.core.image.initialized"
            data-wp-class--active="context.core.image.lightboxEnabled"
			data-wp-class--hideAnimationEnabled="context.core.image.hideAnimationEnabled"
            data-wp-bind--aria-hidden="!context.core.image.lightboxEnabled"
            data-wp-bind--aria-modal="context.core.image.lightboxEnabled"
            data-wp-effect="effects.core.image.initLightbox"
            data-wp-on--keydown="actions.core.image.handleKeydown"
            data-wp-on--touchstart="actions.core.image.handleTouchStart"
            data-wp-on--touchmove="actions.core.image.handleTouchMove"
            data-wp-on--touchend="actions.core.image.handleTouchEnd"
            data-wp-on--click="actions.core.image.hideLightbox"
            >
                <button type="button" aria-label="$close_button_label" style="fill: $close_button_color" class="close-button" data-wp-on--click="actions.core.image.hideLightbox">
                    $close_button_icon
                </button>
                <div class="lightbox-image-container">$initial_image_content</div>
				<div class="lightbox-image-container">$enlarged_image_content</div>
                <div class="scrim" style="background-color: $background_color"></div>
        </div>
HTML;

	return str_replace( '</figure>', $lightbox_html . '</figure>', $body_content );
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'behaviors',
	array(
		'register_attribute' => 'gutenberg_register_behaviors_support',
	)
);
