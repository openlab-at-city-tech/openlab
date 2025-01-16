<?php

use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Placeholder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Placeholders' ) ) {

	class Kenta_Placeholders {

		/**
		 * @var null
		 */
		protected static $_instances = [];

		/**
		 * Get instance
		 */
		public static function instance() {
			if ( ! isset( self::$_instances[ static::class ] ) ) {
				self::$_instances[ static::class ] = new static();
			}

			return self::$_instances[ static::class ];
		}

		/**
		 * keep constructor is protected
		 */
		protected function __construct() {

			if ( KENTA_CMP_PRO_ACTIVE ) {
				return;
			}

			add_filter( 'kenta_header_builder_controls', function ( $controls ) {
				return array_merge( $controls, [
					kenta_upsell_info_control( __( "More builder elements in %sPro Version%s", 'kenta' ), 'kenta_header_builder_upsell' )
				] );
			} );

			add_filter( 'kenta_footer_builder_controls', function ( $controls ) {
				return array_merge( $controls, [
					kenta_upsell_info_control( __( "More builder elements in %sPro Version%s", 'kenta' ), 'kenta_footer_builder_upsell' )
				] );
			} );

			add_filter( 'kenta_header_row_style_controls', function ( $controls ) {
				return array_merge( $controls, [
					kenta_upsell_info_control( __( "Header row overlay is available in %sPro Version%s", 'kenta' ), 'kenta_footer_builder_upsell' )
				] );
			}, 99, 3 );

			add_filter( 'kenta_button_style_controls', function ( $controls, $id, $defaults ) {

				return array_merge( $controls, [
					( new Placeholder( $id . 'text_color' ) )
						->addColor( 'initial', $defaults['text-initial'] )
						->addColor( 'hover', $defaults['text-hover'] )
					,
					( new Placeholder( $id . 'button_color' ) )
						->addColor( 'initial', $defaults['button-initial'] )
						->addColor( 'hover', $defaults['button-hover'] )
					,
					( new Placeholder( $id . 'border' ) )
						->setDefaultBorder( ...array_merge( $defaults['border'], [
							$defaults['border-initial'],
							$defaults['border-hover']
						] ) )
					,
					( new Placeholder( $id . 'shadow' ) )
						->setDefaultShadow( ...$defaults['shadow'] )
					,
					( new Placeholder( $id . 'shadow_active' ) )
						->setDefaultShadow( ...$defaults['shadow-active'] )
					,
					( new Placeholder( $id . 'typography' ) )
						->setDefaultValue( $defaults['typography'] )
					,
					( new Placeholder( $id . 'radius' ) )
						->setDefaultValue( $defaults['border-radius'] )
					,
					( new Placeholder( $id . 'padding' ) )
						->setDefaultValue( $defaults['padding'] )
					,
					kenta_upsell_info_control( __( 'Fully customize your button style in %sPro Version%s', 'kenta' ) )
				] );
			}, 10, 3 );

			add_filter( 'kenta_archive_header_controls', function ( $controls ) {
				return array_merge( $controls, [
					( new Placeholder( 'kenta_search_archive_header_prefix' ) )
						->setDefaultValue( __( 'Search Results for: ', 'kenta' ) )
					,
					( new Placeholder( 'kenta_archive_title_typography' ) )
						->setDefaultValue( [
							'family'        => 'inherit',
							'fontSize'      => [
								'desktop' => '1.45rem',
								'tablet'  => '1.25rem',
								'mobile'  => '1rem'
							],
							'variant'       => '600',
							'lineHeight'    => '2',
							'textTransform' => 'capitalize',
						] )
					,
					( new Placeholder( 'kenta_archive_description_typography' ) )
						->setDefaultValue( [
							'family'     => 'inherit',
							'fontSize'   => [
								'desktop' => '0.875rem',
								'tablet'  => '0.875rem',
								'mobile'  => '0.75em'
							],
							'variant'    => '400',
							'lineHeight' => '1.5',
						] )
					,
					kenta_upsell_info_control( __( 'Fully customize your archive header in %sPro Version%s', 'kenta' ) ),
				] );
			} );

			add_filter( 'kenta_title_element_controls', function ( $controls, $id, $link, $defaults ) {
				return array_merge( $controls, [
					( new Placeholder( 'kenta_' . $id . '_title_typography' ) )
						->setDefaultValue( $defaults['typography'] )
					,
					( new Placeholder( 'kenta_' . $id . '_title_color' ) )
						->addColor( 'initial', $defaults['initial'] )
						->addColor( 'hover', $defaults['hover'] )
					,
					kenta_upsell_info_control( __( 'More options in %sPro Version%s', 'kenta' ) )
				] );
			}, 10, 4 );

			add_filter( 'kenta_taxonomy_element_controls', function ( $controls, $id, $type, $defaults ) {
				return array_merge( $controls, [
					( new Placeholder( 'kenta_' . $id . '_tax_typography' . $type ) )
						->setDefaultValue( $defaults['typography'] )
					,
					( new Placeholder( 'kenta_' . $id . '_tax_default_color' . $type ) )
						->addColor( 'initial', $defaults['text-initial'] )
						->addColor( 'hover', $defaults['text-hover'] )
					,
					( new Placeholder( 'kenta_' . $id . '_tax_badge_text_color' . $type ) )
						->addColor( 'initial', $defaults['badge-text-initial'] )
						->addColor( 'hover', $defaults['badge-text-hover'] )
					,
					( new Placeholder( 'kenta_' . $id . '_tax_badge_bg_color' . $type ) )
						->addColor( 'initial', $defaults['badge-bg-initial'] )
						->addColor( 'hover', $defaults['badge-bg-hover'] )
					,
					kenta_upsell_info_control( __( 'More options in %sPro Version%s', 'kenta' ) )
				] );
			}, 10, 4 );

			add_filter( 'kenta_excerpt_element_controls', function ( $controls, $id, $defaults ) {
				return array_merge( $controls, [
					( new Placeholder( 'kenta_' . $id . '_excerpt_typography' ) )
						->setDefaultValue( [
							'family'     => 'inherit',
							'fontSize'   => '0.875rem',
							'variant'    => '400',
							'lineHeight' => '1.5'
						] )
					,
					( new Placeholder( 'kenta_' . $id . '_excerpt_color' ) )
						->addColor( 'initial', 'var(--kenta-accent-active)' )
					,
					kenta_upsell_info_control( __( 'More options in %sPro Version%s', 'kenta' ) )
				] );
			}, 10, 3 );

			add_filter( 'kenta_metas_element_controls', function ( $controls, $id, $defaults ) {
				return array_merge( $controls, [
					( new Placeholder( 'kenta_' . $id . '_meta_typography' ) )
						->setDefaultValue( $defaults['typography'] )
					,
					( new Placeholder( 'kenta_' . $id . '_meta_color' ) )
						->addColor( 'initial', $defaults['initial'] )
						->addColor( 'hover', $defaults['hover'] )
					,
					( new Placeholder( 'kenta_' . $id . '_meta_items_style' ) )
						->setDefaultValue( $defaults['style'] )
					,
					( new Placeholder( 'kenta_' . $id . '_meta_items_divider' ) )
						->setDefaultValue( $defaults['divider'] )
					,
					kenta_upsell_info_control( __( 'More options in %sPro Version%s', 'kenta' ) )
				] );
			}, 10, 3 );

			add_filter( 'kenta_widgets_style_controls', function ( $controls, $id, $defaults ) {

				return array_merge( $controls, [
					( new Placeholder( $id . 'title-typography' ) )
						->setDefaultValue( $defaults['title-typography'] )
					,
					( new Placeholder( $id . 'title-color' ) )
						->addColor( 'initial', $defaults['title-color'] )
						->addColor( 'indicator', $defaults['title-indicator'] )
					,
					( new Placeholder( $id . 'content-typography' ) )
						->setDefaultValue( $defaults['content-typography'] )
					,
					( new Placeholder( $id . 'content-color' ) )
						->addColor( 'text', $defaults['text-color'] )
						->addColor( 'initial', $defaults['link-initial'] )
						->addColor( 'hover', $defaults['link-hover'] )
					,
					( new Placeholder( $id . 'widgets-background' ) )
						->setDefaultValue( [
							'type'  => 'color',
							'color' => $defaults['widgets-background'],
						] )
					,
					( new Placeholder( $id . 'widgets-border' ) )
						->setDefaultBorder( ...$defaults['widgets-border'] )
					,
					( new Placeholder( $id . 'widgets-shadow' ) )
						->setDefaultShadow( ...array_merge( $defaults['widgets-shadow'], [ $defaults['widgets-shadow-enable'] ] ) )
					,
					( new Placeholder( $id . 'widgets-padding' ) )
						->setDefaultValue( $defaults['widgets-padding'] )
					,
					( new Placeholder( $id . 'widgets-radius' ) )
						->setDefaultValue( $defaults['widgets-radius'] )
					,
					kenta_upsell_info_control( __( 'Fully customize your sidebar in %sPro Version%s', 'kenta' ) )
				] );

			}, 10, 3 );

			add_filter( 'kenta_menu_top_level_controls', function ( $controls, $slug, $defaults ) {
				$id = $slug . '_';

				return array_merge( $controls, [
					( new Placeholder( $id . 'top_level_typography' ) )
						->setDefaultValue( $defaults['top-level-typography'] ?? [
							'family'        => 'inherit',
							'fontSize'      => '0.85rem',
							'variant'       => '500',
							'lineHeight'    => '1',
							'textTransform' => 'capitalize',
						] )
					,
					( new Placeholder( $id . 'top_level_text_color' ) )
						->addColor( 'initial', $defaults['top-level-text-initial'] ?? 'var(--kenta-accent-active)' )
						->addColor( 'hover', $defaults['top-level-text-hover'] ?? 'var(--kenta-primary-color)' )
						->addColor( 'active', $defaults['top-level-text-active'] ?? 'var(--kenta-primary-color)' )
					,
					( new Placeholder( $id . 'top_level_background_color' ) )
						->addColor( 'initial', $defaults['top-level-background-initial'] ?? 'var(--kenta-transparent)' )
						->addColor( 'hover', $defaults['top-level-background-hover'] ?? 'var(--kenta-transparent)' )
						->addColor( 'active', $defaults['top-level-background-active'] ?? 'var(--kenta-transparent)' )
					,
					( new Placeholder( $id . 'top_level_border_top' ) )
						->setDefaultBorder( 2, 'none', 'var(--kenta-transparent)' )
					,
					( new Placeholder( $id . 'top_level_border_top_active' ) )
						->setDefaultBorder( 2, 'none', 'var(--kenta-primary-color)' )
					,
					( new Placeholder( $id . 'top_level_border_bottom' ) )
						->setDefaultBorder( 2, 'none', 'var(--kenta-transparent)' )
					,
					( new Placeholder( $id . 'top_level_border_bottom_active' ) )
						->setDefaultBorder( 2, 'none', 'var(--kenta-primary-color)' )
					,
					( new Placeholder( $id . 'top_level_margin' ) )
						->setDefaultValue( $defaults['top-level-margin'] ?? [
							'top'    => '0px',
							'bottom' => '0px',
							'left'   => '0px',
							'right'  => '0px',
							'linked' => true,
						] )
					,
					( new Placeholder( $id . 'top_level_padding' ) )
						->setDefaultValue( $defaults['top-level-padding'] ?? [
							'top'    => '4px',
							'bottom' => '4px',
							'left'   => '8px',
							'right'  => '8px',
							'linked' => false,
						] )
					,
					( new Placeholder( $id . 'top_level_radius' ) )
						->setDefaultValue( $defaults['top-level-radius'] ?? [
							'top'    => '0',
							'bottom' => '0',
							'left'   => '0',
							'right'  => '0',
							'linked' => true,
						] )
					,

					kenta_upsell_info_control( __( "Fully customize your menu's top level items in %sPro Version%s", 'kenta' ) )
				] );
			}, 10, 3 );

			add_filter( 'kenta_copyright_element_controls', function ( $controls ) {
				return array_merge( $controls, [
					kenta_upsell_info_control( __( "Customize your copyright text in %sPro Version%s", 'kenta' ) )
				] );
			}, 10, 3 );

			add_filter( 'kenta_breadcrumbs_element_content_controls', function ( $controls ) {
				return array_merge( $controls, [
					kenta_upsell_info_control( __( "More breadcrumb options in %sPro Version%s", 'kenta' ) )
				] );
			}, 10, 3 );

			add_filter( 'kenta_breadcrumbs_element_style_controls', function ( $controls, $slug ) {
				$id = $slug . '_';

				return array_merge( $controls, [
					( new Placeholder( $id . 'typography' ) )
						->setDefaultValue( [
							'family'        => 'inherit',
							'fontSize'      => '0.8rem',
							'variant'       => '400',
							'lineHeight'    => '1.5',
							'textTransform' => 'capitalize',
						] )
					,
					( new Placeholder( $id . 'text_color' ) )
						->addColor( 'text', 'var(--kenta-accent-color)' )
						->addColor( 'initial', 'var(--kenta-accent-active)' )
						->addColor( 'hover', 'var(--kenta-primary-color)' )
					,
					kenta_upsell_info_control( __( "More breadcrumb style options in %sPro Version%s", 'kenta' ) )
				] );
			}, 10, 3 );

			add_filter( 'kenta_socials_element_content_controls', function ( $controls, $slug, $defaults ) {
				$id = $slug . '_';

				return array_merge( $controls, [
					( new Placeholder( $id . 'icons_shape' ) )
						->setDefaultValue( $defaults['icons-shape'] )
					,
					( new Placeholder( $id . 'shape_fill_type' ) )
						->setDefaultValue( $defaults['icons-fill-type'] )
					,
					kenta_upsell_info_control( __( 'More social icon options in our %sPro Version%s', 'kenta' ) )
				] );
			}, 10, 3 );

			add_filter( 'kenta_socials_element_style_controls', function ( $controls, $slug, $defaults ) {
				$id = $slug . '_';

				return array_merge( $controls, [
					( new Placeholder( $id . 'icons_bg_color' ) )
						->addColor( 'initial', $defaults['icons-bg-initial'] )
						->addColor( 'hover', $defaults['icons-bg-hover'] )
					,
					( new Placeholder( $id . 'icons_border_color' ) )
						->addColor( 'initial', $defaults['icons-border-initial'] )
						->addColor( 'hover', $defaults['icons-border-hover'] )
					,
					( new Placeholder( $id . 'padding' ) )
						->setDefaultValue( $defaults['icons-box-spacing'] )
					,
					kenta_upsell_info_control( __( 'Fully customize your social icons in our %sPro Version%s', 'kenta' ) )
				] );
			}, 10, 3 );

			add_filter( 'kenta_icon_button_style_controls', [ $this, 'more_options_upsell' ], 10, 3 );
			add_filter( 'kenta_menu_dropdown_style_controls', [ $this, 'more_options_upsell' ], 10, 3 );

			add_filter( 'kenta_archive_layout_controls', function ( $controls ) {
				$controls[] = kenta_upsell_info_control( __( 'More layout available in %sPro Version%s', 'kenta' ), 'kenta_archive_layout_upsell_info' );

				return $controls;
			} );

			add_filter( 'kenta_card_style_controls', function ( $controls ) {
				$controls[] = kenta_upsell_info_control( __( "Fully customize your posts card style in %sPro Version%s", 'kenta' ) );

				return $controls;
			}, 10, 2 );

			add_filter( 'kenta_pagination_general_controls', function ( $controls ) {
				$controls[] = ( new Condition() )
					->setCondition( [ 'kenta_pagination_type' => 'load-more|infinite-scroll' ] )
					->setControls( [
						kenta_upsell_info_control( __( 'Load More & Infinite Scroll is available in our %sPro Version%s', 'kenta' ) )
					] );

				return $controls;
			} );

			/**
			 * WooCommerce controls
			 */
			add_filter( 'kenta_store_card_style_controls', function ( $controls ) {
				$controls[] = kenta_upsell_info_control( __( "Fully customize your product card style in %sPro Version%s", 'kenta' ) );

				return $controls;
			} );
		}

		public function more_options_upsell( $controls ) {
			return array_merge( $controls, [
				kenta_upsell_info_control( __( "More options in %sPro Version%s", 'kenta' ) )
			] );
		}
	}
}