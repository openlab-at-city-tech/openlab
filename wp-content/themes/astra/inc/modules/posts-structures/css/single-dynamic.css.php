<?php
/**
 * Post Structures - Dynamic CSS
 *
 * @package Astra
 * @since 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Post Structures
 */
add_filter( 'astra_dynamic_theme_css', 'astra_post_single_structure_dynamic_css' );

/**
 * Dynamic CSS
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @param  string $dynamic_css_filtered Astra Dynamic CSS Filters.
 * @return String Generated dynamic CSS for Post Structures.
 *
 * @since 4.0.0
 */
function astra_post_single_structure_dynamic_css( $dynamic_css, $dynamic_css_filtered = '' ) {

	$current_post_type    = strval( get_post_type() );
	$supported_post_types = Astra_Posts_Structure_Loader::get_supported_post_types();

	if ( ! is_singular( $current_post_type ) ) {
		return $dynamic_css;
	}
	if ( ! in_array( $current_post_type, $supported_post_types ) ) {
		return $dynamic_css;
	}
	if ( false === astra_get_option( 'ast-single-' . $current_post_type . '-title', ( class_exists( 'WooCommerce' ) && 'product' === $current_post_type ) ? false : true ) ) {
		return $dynamic_css;
	}

	$layout_type     = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-layout', 'layout-1' );
	$layout_2_active = ( 'layout-2' === $layout_type ) ? true : false;
	$exclude_attr    = astra_get_option( 'enable-related-posts', false ) ? ':not(.related-entry-header)' : '';

	if ( $layout_2_active ) {
		$selector = '.ast-single-entry-banner[data-post-type="' . $current_post_type . '"]';
	} else {
		$selector = 'header.entry-header' . $exclude_attr;
	}

	$site_content_width = astra_get_option( 'site-content-width', 1200 );
	$horz_alignment     = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-horizontal-alignment' );
	$desk_h_alignment   = ( isset( $horz_alignment['desktop'] ) ) ? $horz_alignment['desktop'] : '';
	$tab_h_alignment    = ( isset( $horz_alignment['tablet'] ) ) ? $horz_alignment['tablet'] : '';
	$mob_h_alignment    = ( isset( $horz_alignment['mobile'] ) ) ? $horz_alignment['mobile'] : '';

	$banner_padding = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-banner-padding', Astra_Posts_Structure_Loader::get_customizer_default( 'responsive-padding' ) );
	$banner_margin  = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-banner-margin' );

	$text_color       = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-banner-text-color' );
	$title_color      = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-banner-title-color' );
	$link_color       = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-banner-link-color' );
	$link_hover_color = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-banner-link-hover-color' );

	$elements_gap       = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-elements-gap', 10 );
	$banner_height      = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-banner-height' );
	$desk_banner_height = ( $layout_2_active && isset( $banner_height['desktop'] ) ) ? astra_get_css_value( $banner_height['desktop'], 'px' ) : '';
	$tab_banner_height  = ( $layout_2_active && isset( $banner_height['tablet'] ) ) ? astra_get_css_value( $banner_height['tablet'], 'px' ) : '';
	$mob_banner_height  = ( $layout_2_active && isset( $banner_height['mobile'] ) ) ? astra_get_css_value( $banner_height['mobile'], 'px' ) : '';

	$vert_alignment = ( $layout_2_active ) ? astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-vertical-alignment', 'center' ) : 'center';
	$width_type     = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-banner-width-type', 'fullwidth' );
	$custom_width   = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-banner-custom-width', 1200 );

	$single_structure = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-structure', 'page' === $current_post_type ? array( 'ast-dynamic-single-' . $current_post_type . '-image', 'ast-dynamic-single-' . $current_post_type . '-title' ) : array( 'ast-dynamic-single-' . $current_post_type . '-title', 'ast-dynamic-single-' . $current_post_type . '-meta' ) );

	// Banner Text typography dynamic stylings.
	$banner_text_font_size = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-text-font-size' );

	// Banner Title typography dynamic stylings.
	$banner_title_font_size = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-title-font-size', Astra_Posts_Structure_Loader::get_customizer_default( 'title-font-size' ) );

	// Banner Meta typography dynamic stylings.
	$banner_meta_font_size = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-meta-font-size' );

	$css_output_min_tablet  = array();
	$narrow_container_width = astra_get_option( 'narrow-container-max-width', apply_filters( 'astra_narrow_container_width', 750 ) );

	// Aspect ratio.
	$aspect_ratio_type   = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-article-featured-image-ratio-type', 'predefined' );
	$predefined_scale    = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-article-featured-image-ratio-pre-scale', '16/9' );
	$custom_scale_width  = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-article-featured-image-custom-scale-width', 16 );
	$custom_scale_height = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-article-featured-image-custom-scale-height', 9 );
	$aspect_ratio        = astra_get_dynamic_image_aspect_ratio( $aspect_ratio_type, $predefined_scale, $custom_scale_width, $custom_scale_height );

	// Few settings from banner section are also applicable to 'layout-1' so adding this condition & compatibility.
	if ( 'layout-1' === $layout_type ) {
		$image_wrap_alignment = ( false === astra_get_option( 'v4-4-0-backward-option', true ) ) ? '' : 'center';
		/**
		 * Desktop CSS.
		 */
		$css_output_desktop = array(
			$selector                               => array(
				'text-align' => $desk_h_alignment,
			),
			$selector . ' *'                        => astra_get_font_array_css( astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-text-font-family' ), astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-text-font-weight' ), $banner_text_font_size, 'ast-dynamic-single-' . $current_post_type . '-text-font-extras', $text_color ),
			$selector . ' .entry-title'             => astra_get_font_array_css( astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-title-font-family' ), astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-title-font-weight', Astra_Posts_Structure_Loader::get_customizer_default( 'title-font-weight' ) ), $banner_title_font_size, 'ast-dynamic-single-' . $current_post_type . '-title-font-extras', $title_color ),
			$selector . ' .entry-meta, ' . $selector . ' .entry-meta *' => astra_get_font_array_css( astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-meta-font-family' ), astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-meta-font-weight' ), $banner_meta_font_size, 'ast-dynamic-single-' . $current_post_type . '-meta-font-extras' ),
			$selector . ' a, ' . $selector . ' a *' => array(
				'color' => esc_attr( $link_color ),
			),
			$selector . ' a:hover, ' . $selector . ' a:hover *' => array(
				'color' => esc_attr( $link_hover_color ),
			),
			$selector . ' > *:not(:last-child)'     => array(
				'margin-bottom' => $elements_gap . 'px',
			),
			$selector . ' .post-thumb-img-content'  => array(
				'text-align' => $image_wrap_alignment,
			),
			$selector . ' .post-thumb img, .ast-single-post-featured-section.post-thumb img' => array(
				'aspect-ratio' => $aspect_ratio,
			),
		);
		/**
		 * Tablet CSS.
		 */
		$css_output_tablet = array(
			$selector                   => array(
				'text-align' => $tab_h_alignment,
			),
			$selector . ' .entry-title' => array(
				'font-size' => astra_responsive_font( $banner_title_font_size, 'tablet' ),
			),
			$selector . ' *'            => array(
				'font-size' => astra_responsive_font( $banner_text_font_size, 'tablet' ),
			),
			$selector . ' .entry-meta, ' . $selector . ' .entry-meta *' => array(
				'font-size' => astra_responsive_font( $banner_meta_font_size, 'tablet' ),
			),
		);

		/**
		 * Mobile CSS.
		 */
		$css_output_mobile = array(
			$selector                   => array(
				'text-align' => $mob_h_alignment,
			),
			$selector . ' .entry-title' => array(
				'font-size' => astra_responsive_font( $banner_title_font_size, 'mobile' ),
			),
			$selector . ' *'            => array(
				'font-size' => astra_responsive_font( $banner_text_font_size, 'mobile' ),
			),
			$selector . ' .entry-meta, ' . $selector . ' .entry-meta *' => array(
				'font-size' => astra_responsive_font( $banner_meta_font_size, 'mobile' ),
			),
		);
	} else {
		$entry_title_selector    = is_customize_preview() ? $selector . ' .ast-container .entry-title' : $selector . ' .entry-title';
		$image_position          = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-image-position', 'inside' );
		$use_featured_background = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-featured-as-background', false );
		$custom_background       = astra_get_option(
			'ast-dynamic-single-' . $current_post_type . '-banner-background',
			Astra_Posts_Structure_Loader::get_customizer_default( 'responsive-background' )
		);

		/**
		 * Desktop CSS.
		 */
		$css_output_desktop = array(
			$selector                                     => array(
				'text-align'      => $desk_h_alignment,
				'justify-content' => $vert_alignment,
				'min-height'      => $desk_banner_height,
				'margin-top'      => astra_responsive_spacing( $banner_margin, 'top', 'desktop' ),
				'margin-right'    => astra_responsive_spacing( $banner_margin, 'right', 'desktop' ),
				'margin-bottom'   => astra_responsive_spacing( $banner_margin, 'bottom', 'desktop' ),
				'margin-left'     => astra_responsive_spacing( $banner_margin, 'left', 'desktop' ),
			),
			$selector . ' .ast-container'                 => array(
				'width'          => '100%',
				'padding-top'    => astra_responsive_spacing( $banner_padding, 'top', 'desktop' ),
				'padding-right'  => astra_responsive_spacing( $banner_padding, 'right', 'desktop' ),
				'padding-bottom' => astra_responsive_spacing( $banner_padding, 'bottom', 'desktop' ),
				'padding-left'   => astra_responsive_spacing( $banner_padding, 'left', 'desktop' ),
			),
			$selector . '[data-banner-layout="layout-2"]' => astra_get_responsive_background_obj( $custom_background, 'desktop' ),
			$selector . ' .ast-container *'               => astra_get_font_array_css( astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-text-font-family' ), astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-text-font-weight' ), $banner_text_font_size, 'ast-dynamic-single-' . $current_post_type . '-text-font-extras', $text_color ),
			$selector . ' .ast-container > *:not(:last-child)' => array(
				'margin-bottom' => $elements_gap . 'px',
			),
			'.ast-page-builder-template ' . $selector . ' .ast-container' => array(
				'max-width' => '100%',
			),
			$entry_title_selector                         => astra_get_font_array_css( astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-title-font-family' ), astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-title-font-weight', Astra_Posts_Structure_Loader::get_customizer_default( 'title-font-weight' ) ), $banner_title_font_size, 'ast-dynamic-single-' . $current_post_type . '-title-font-extras', $title_color ),
			$selector . ' > .entry-title'                 => array(
				'margin-bottom' => '0',
			),
			$selector . ' .entry-meta, ' . $selector . ' .entry-meta *' => astra_get_font_array_css( astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-meta-font-family' ), astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-meta-font-weight' ), $banner_meta_font_size, 'ast-dynamic-single-' . $current_post_type . '-meta-font-extras' ),
			$selector . ' .ast-container a, ' . $selector . ' .ast-container a *' => array(
				'color' => esc_attr( $link_color ),
			),
			$selector . ' .ast-container a:hover, ' . $selector . ' .ast-container a:hover *' => array(
				'color' => esc_attr( $link_hover_color ),
			),
			$selector . ' .post-thumb img'                => array(
				'aspect-ratio' => $aspect_ratio,
			),
		);

		/**
		 * Min tablet width CSS.
		 */
		$css_output_min_tablet = array(
			'.ast-narrow-container ' . $selector . ' .ast-container' => array(
				'max-width'     => $narrow_container_width . 'px',
				'padding-left'  => '0',
				'padding-right' => '0',
			),
		);

		/**
		 * Tablet CSS.
		 */
		$css_output_tablet = array(
			$selector                                     => array(
				'text-align'     => $tab_h_alignment,
				'min-height'     => $tab_banner_height,
				'padding-top'    => astra_responsive_spacing( $banner_padding, 'top', 'tablet' ),
				'padding-right'  => astra_responsive_spacing( $banner_padding, 'right', 'tablet' ),
				'padding-bottom' => astra_responsive_spacing( $banner_padding, 'bottom', 'tablet' ),
				'padding-left'   => astra_responsive_spacing( $banner_padding, 'left', 'tablet' ),
				'margin-top'     => astra_responsive_spacing( $banner_margin, 'top', 'tablet' ),
				'margin-right'   => astra_responsive_spacing( $banner_margin, 'right', 'tablet' ),
				'margin-bottom'  => astra_responsive_spacing( $banner_margin, 'bottom', 'tablet' ),
				'margin-left'    => astra_responsive_spacing( $banner_margin, 'left', 'tablet' ),
			),
			$selector . '[data-banner-layout="layout-2"]' => astra_get_responsive_background_obj( $custom_background, 'tablet' ),
			$selector . ' .entry-title'                   => array(
				'font-size' => astra_responsive_font( $banner_title_font_size, 'tablet' ),
			),
			$selector . ' .ast-container'                 => array(
				'padding-left'  => '0',
				'padding-right' => '0',
			),
			$selector . ' *'                              => array(
				'font-size' => astra_responsive_font( $banner_text_font_size, 'tablet' ),
			),
			$selector . ' .entry-meta, ' . $selector . ' .entry-meta *' => array(
				'font-size' => astra_responsive_font( $banner_meta_font_size, 'tablet' ),
			),
		);

		/**
		 * Mobile CSS.
		 */
		$css_output_mobile = array(
			$selector                                     => array(
				'text-align'     => $mob_h_alignment,
				'min-height'     => $mob_banner_height,
				'padding-top'    => astra_responsive_spacing( $banner_padding, 'top', 'mobile' ),
				'padding-right'  => astra_responsive_spacing( $banner_padding, 'right', 'mobile' ),
				'padding-bottom' => astra_responsive_spacing( $banner_padding, 'bottom', 'mobile' ),
				'padding-left'   => astra_responsive_spacing( $banner_padding, 'left', 'mobile' ),
				'margin-top'     => astra_responsive_spacing( $banner_margin, 'top', 'mobile' ),
				'margin-right'   => astra_responsive_spacing( $banner_margin, 'right', 'mobile' ),
				'margin-bottom'  => astra_responsive_spacing( $banner_margin, 'bottom', 'mobile' ),
				'margin-left'    => astra_responsive_spacing( $banner_margin, 'left', 'mobile' ),
			),
			$selector . '[data-banner-layout="layout-2"]' => astra_get_responsive_background_obj( $custom_background, 'mobile' ),
			$selector . ' .entry-title'                   => array(
				'font-size' => astra_responsive_font( $banner_title_font_size, 'mobile' ),
			),
			$selector . ' *'                              => array(
				'font-size' => astra_responsive_font( $banner_text_font_size, 'mobile' ),
			),
			$selector . ' .entry-meta, ' . $selector . ' .entry-meta *' => array(
				'font-size' => astra_responsive_font( $banner_meta_font_size, 'mobile' ),
			),
		);

		if ( ( $layout_2_active && 'custom' === $width_type ) || is_customize_preview() ) {
			$css_output_desktop[ $selector . '[data-banner-width-type="custom"]' ]['max-width'] = $custom_width . 'px';
		}

		if ( 'outside' !== $image_position && in_array( 'ast-dynamic-single-' . $current_post_type . '-image', $single_structure ) && $use_featured_background ) {
			/** @psalm-suppress PossiblyFalseArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$feat_image_src = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
			/** @psalm-suppress PossiblyFalseArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( $feat_image_src ) {
				$css_output_desktop[ $selector . '[data-banner-background-type="featured"]' ] = array(
					'background'            => 'url( ' . esc_url( $feat_image_src ) . ' )',
					'background-repeat'     => 'no-repeat',
					'background-attachment' => 'scroll',
					'background-position'   => 'center center',
					'background-size'       => 'cover',
				);
				$overlay_color = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-banner-featured-overlay', '' );
				if ( '' !== $overlay_color && 'unset' !== $overlay_color ) {
					$css_output_desktop[ $selector . '[data-banner-background-type="featured"]' ]['background']            = 'url( ' . esc_url( $feat_image_src ) . ' ) ' . $overlay_color;
					$css_output_desktop[ $selector . '[data-banner-background-type="featured"]' ]['background-blend-mode'] = 'multiply';
				}
			}
		}

		if ( 'outside' === $image_position ) {
			$css_output_desktop['.single article .post-thumb'] = array(
				'margin-bottom' => '2em',
			);
		}
	}

	$dynamic_css .= '
		.ast-single-entry-banner {
			-js-display: flex;
			display: flex;
			flex-direction: column;
			justify-content: center;
			text-align: center;
			position: relative;
			background: #eeeeee;
		}
		.ast-single-entry-banner[data-banner-layout="layout-1"] {
			max-width: ' . astra_get_css_value( $site_content_width, 'px' ) . ';
			background: inherit;
			padding: 20px 0;
		}
		.ast-single-entry-banner[data-banner-width-type="custom"] {
			margin: 0 auto;
			width: 100%;
		}
		.ast-single-entry-banner + .site-content .entry-header {
			margin-bottom: 0;
		}
	';

	if ( is_customize_preview() ) {
		$dynamic_css .= '
			.site-header-focus-item .ast-container div.customize-partial-edit-shortcut,
			.site-header-focus-item .ast-container button.item-customizer-focus {
				font-size: inherit;
			}
		';
	}

	/* Parse CSS from array() */
	$dynamic_css .= astra_parse_css( $css_output_desktop );
	$dynamic_css .= astra_parse_css( $css_output_min_tablet, astra_get_tablet_breakpoint( '', 1 ) );
	$dynamic_css .= astra_parse_css( $css_output_tablet, '', astra_get_tablet_breakpoint() );
	$dynamic_css .= astra_parse_css( $css_output_mobile, '', astra_get_mobile_breakpoint() );

	return $dynamic_css;
}
