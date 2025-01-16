<?php
/**
 * Article trait
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Background;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Filters;
use LottaFramework\Customizer\Controls\ImageRadio;
use LottaFramework\Customizer\Controls\ImageUploader;
use LottaFramework\Customizer\Controls\Layers;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Spacing;
use LottaFramework\Facades\AsyncCss;
use LottaFramework\Facades\Css;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! trait_exists( 'Kenta_Article_Controls' ) ) {

	/**
	 * Post structure functions
	 */
	trait Kenta_Article_Controls {

		use Kenta_Post_Structure;

		/**
		 * @param $type
		 * @param array $defaults
		 *
		 * @return array
		 */
		protected function getContainerControls( $type, $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'style'  => 'boxed',
				'layout' => 'normal',
				'preset' => 'ghost'
			] );

			$article_type = $type === 'pages' ? 'page' : 'post';

			$controls = [
				kenta_docs_control( __( '%sLearn how it works%s', 'kenta' ), 'https://kentatheme.com/docs/kenta-theme/article-content-options/article-container/' ),
				( new Radio( 'kenta_' . $type . '_container_style' ) )
					->setLabel( __( 'Container Style', 'kenta' ) )
					->setDefaultValue( $defaults['style'] )
					->buttonsGroupView()
					->setChoices( [
						'boxed' => __( 'Boxed', 'kenta' ),
						'fluid' => __( 'Fluid', 'kenta' )
					] )
				,
				( new ImageRadio( 'kenta_' . $type . '_container_layout' ) )
					->setLabel( __( 'Content Width', 'kenta' ) )
					->setDefaultValue( $defaults['layout'] )
					->asyncCss( '.kenta-site-wrap .kenta-container', [
						'--wp--style--global--content-size' => AsyncCss::unescape( AsyncCss::valueMapper( [
							'normal' => 'var(--wp--style--global--wide-size)',
							'narrow' => '720px',
						] ) )
					] )
					->setChoices( [
						'narrow' => [
							'title' => __( 'Narrow', 'kenta' ),
							'src'   => kenta_image_url( 'narrow.png' ),
						],
						'normal' => [
							'title' => __( 'Normal', 'kenta' ),
							'src'   => kenta_image_url( 'normal.png' ),
						],
					] )
				,
				( new Condition() )
					->setCondition( [ 'kenta_' . $type . '_container_layout' => 'narrow' ] )
					->setControls( [
						( new Slider( 'kenta_' . $type . '_container_max_width' ) )
							->setLabel( __( 'Content Max Width', 'kenta' ) )
							->asyncCss( '.kenta-site-wrap .kenta-container', [
								'--wp--style--global--content-size' => 'value'
							] )
							->setUnits( [
								[ 'unit' => 'px', 'min' => 500, 'max' => 1140 ],
								[ 'unit' => '%', 'min' => 50, 'max' => 100 ],
							] )
							->setDefaultValue( '720px' )
					] )
				,
				( new Separator() ),
				( new Background( 'kenta_' . $type . '_site_background' ) )
					->setLabel( __( 'Site Background', 'kenta' ) )
					->asyncCss( ".kenta-{$type} .kenta-site-wrap", AsyncCss::background() )
					->enableResponsive()
					->setDefaultValue( [
						'type'  => 'color',
						'color' => Css::INITIAL_VALUE,
					] )
				,
				( new Separator() ),
				( new Select( 'kenta_' . $article_type . '_content_style_preset' ) )
					->setLabel( __( 'Container Style Preset', 'kenta' ) )
					->setDefaultValue( $defaults['preset'] )
					->bindSelectiveRefresh( 'kenta-global-selective-css' )
					->setChoices( kenta_card_style_preset_options() )
				,
			];

			return apply_filters( "kenta_{$article_type}_container_controls", $controls, $article_type, $defaults );
		}

		/**
		 * @return array
		 */
		protected function getHeaderControls( $type, $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'selective-refresh' => [ null ],
				'selector'          => '',
				'elements'          => [
					[ 'id' => 'categories', 'visible' => true ],
					[ 'id' => 'title', 'visible' => true ],
					[ 'id' => 'metas', 'visible' => true ],
					[ 'id' => 'tags', 'visible' => false ],
				],
				'metas'             => [],
			] );

			return [
				kenta_docs_control( __( '%sLearn how it works%s', 'kenta' ), 'https://kentatheme.com/docs/kenta-theme/article-content-options/article-header/' ),
				( new Layers( 'kenta_' . $type . '_header_elements' ) )
					->setLabel( __( 'Header Elements', 'kenta' ) )
					->selectiveRefresh( ...$defaults['selective-refresh'] )
					->setDefaultValue( $defaults['elements'] )
					->addLayer( 'title', __( 'Title', 'kenta' ), $this->getTitleLayerControls( $type, false, [
						'selective-refresh' => $defaults['selective-refresh'],
						'selector'          => $defaults['selector'],
						'tag'               => 'h1',
						'typography'        => [
							'family'     => 'inherit',
							'fontSize'   => [ 'desktop' => '3rem', 'tablet' => '2rem', 'mobile' => '1.875em' ],
							'variant'    => '700',
							'lineHeight' => '1.25'
						]
					] ) )
					->addLayer( 'metas', __( 'Metas', 'kenta' ), $this->getMetasControls( $type, array_merge( $defaults['metas'], [
						'selective-refresh' => $defaults['selective-refresh'],
						'selector'          => $defaults['selector'],
					] ) ) )
					->addLayer( 'categories', __( 'Categories', 'kenta' ), $this->getTaxonomyControls( $type, '_cats', [
						'selective-refresh' => $defaults['selective-refresh'],
						'selector'          => $defaults['selector'],
						'style'             => 'badge',
					] ) )
					->addLayer( 'tags', __( 'Tags', 'kenta' ), $this->getTaxonomyControls( $type, '_tags', [
						'selective-refresh' => $defaults['selective-refresh'],
						'selector'          => $defaults['selector'],
					] ) )
				,
				( new ImageRadio( 'kenta_' . $type . '_header_alignment' ) )
					->setLabel( __( 'Content Alignment', 'kenta' ) )
					->asyncCss( $defaults['selector'], [ 'text-align' => 'value' ] )
					->setDefaultValue( 'center' )
					->inlineChoices()
					->setChoices( [
						'left'   => [
							'src'   => kenta_image( 'text-left' ),
							'title' => __( 'Left', 'kenta' ),
						],
						'center' => [
							'src'   => kenta_image( 'text-center' ),
							'title' => __( 'Center', 'kenta' ),
						],
						'right'  => [
							'src'   => kenta_image( 'text-right' ),
							'title' => __( 'Right', 'kenta' ),
						]
					] )
				,
				( new Separator() ),
				( new Spacing( 'kenta_' . $type . '_header_spacing' ) )
					->setLabel( __( 'Spacing', 'kenta' ) )
					->asyncCss( $defaults['selector'], AsyncCss::dimensions( 'padding' ) )
					->setDisabled( [ 'left', 'right' ] )
					->setDefaultValue( [
						'top'    => '48px',
						'right'  => '0px',
						'bottom' => '48px',
						'left'   => '0px',
						'linked' => true,
					] )
				,
			];
		}

		/**
		 * @return array
		 */
		protected function getFeaturedImageControls( $type, $defaults = [] ) {
			$defaults = wp_parse_args( $defaults, [
				'selective-refresh' => [ null ],
				'selector'          => '',
				'image-position'    => 'below',
			] );

			$behind_controls = [
				( new ColorPicker( 'kenta_' . $type . '_featured_image_elements_override' ) )
					->setLabel( __( 'Header Color Override', 'kenta' ) )
					->asyncColors( '.kenta-article-header-background', [
						'override' => '--kenta-article-header-override',
					] )
					->addColor( 'override', __( 'Override', 'kenta' ), Css::INITIAL_VALUE )
				,
				( new Separator() ),
				( new Slider( 'kenta_' . $type . '_featured_image_background_overlay_opacity' ) )
					->setLabel( __( 'Overlay Opacity', 'kenta' ) )
					->asyncCss( '.kenta-article-header-background::after', [ 'opacity' => 'value' ] )
					->setMin( 0 )
					->setMax( 1 )
					->setDecimals( 2 )
					->setDefaultUnit( false )
					->setDefaultValue( 0.6 )
				,
				( new Background( 'kenta_' . $type . '_featured_image_background_overlay' ) )
					->setLabel( __( 'Header Overlay', 'kenta' ) )
					->asyncCss( '.kenta-article-header-background::after', AsyncCss::background() )
					->setDefaultValue( [
						'type'     => 'gradient',
						'gradient' => 'linear-gradient(-225deg, rgb(227, 253, 245) 0%, rgb(255, 230, 250) 100%)',
						'color'    => 'rgba(24,31,41,0.45)',
					] )
				,
				( new Separator() ),
				( new Spacing( 'kenta_' . $type . '_featured_image_background_spacing' ) )
					->setLabel( __( 'Spacing', 'kenta' ) )
					->asyncCss( '.kenta-article-header-background', AsyncCss::dimensions( 'padding' ) )
					->enableResponsive()
					->setDefaultValue( [
						'top'    => '68px',
						'right'  => '0px',
						'bottom' => '68px',
						'left'   => '0px',
						'linked' => false,
					] )
				,
			];

			$non_behind_controls = [
				( new Radio( 'kenta_' . $type . '_featured_image_width' ) )
					->setLabel( __( 'Image Width', 'kenta' ) )
					->buttonsGroupView()
					->setDefaultValue( 'wide' )
					->selectiveRefresh( ...$defaults['selective-refresh'] )
					->setChoices( [
						'default' => __( 'Default', 'kenta' ),
						'wide'    => __( 'Wide', 'kenta' ),
						'full'    => __( 'Full', 'kenta' ),
					] )
				,
				( new Slider( 'kenta_' . $type . '_featured_image_height' ) )
					->setLabel( __( 'Image Height', 'kenta' ) )
					->asyncCss( $defaults['selector'] . ' img', array( 'height' => 'value' ) )
					->setUnits( [
						[ 'unit' => 'px', 'min' => 100, 'max' => 1000 ],
						[ 'unit' => '%', 'min' => 10, 'max' => 100 ],
					] )
					->setDefaultValue( '100%' )
				,
				( new Separator() ),
				( new Spacing( 'kenta_' . $type . '_featured_image_spacing' ) )
					->setLabel( __( 'Spacing', 'kenta' ) )
					->enableResponsive()
					->asyncCss( $defaults['selector'], AsyncCss::dimensions( 'padding' ) )
					->setDisabled( [ 'left', 'right' ] )
					->setDefaultValue( [
						'top'    => '12px',
						'right'  => '0px',
						'bottom' => '12px',
						'left'   => '0px',
						'linked' => true,
					] )
				,
			];

			$controls = [
				kenta_docs_control( __( '%sLearn how it works%s', 'kenta' ), 'https://kentatheme.com/docs/kenta-theme/article-content-options/article-header/' ),
				( new ImageUploader( 'kenta_' . $type . '_featured_image_fallback' ) )
					->setLabel( __( 'Image Fallback', 'kenta' ) )
					->setDescription( __( 'If the current post does not have a featured image, then this image will be displayed.', 'kenta' ) )
					->setDefaultValue( '' )
				,
				( new Select( 'kenta_' . $type . '_featured_image_size' ) )
					->setLabel( __( 'Image Size', 'kenta' ) )
					->setDefaultValue( 'full' )
					->selectiveRefresh( ...$defaults['selective-refresh'] )
					->setChoices( kenta_image_size_options( false ) )
				,
				( new Separator() ),
				( new ImageRadio( 'kenta_' . $type . '_featured_image_position' ) )
					->setLabel( __( 'Image Position', 'kenta' ) )
					->setColumns( 3 )
					->setDefaultValue( $defaults['image-position'] )
					->setChoices( [
						'above'  => [
							'src' => kenta_image_url( 'above-header.png' ),
						],
						'below'  => [
							'src' => kenta_image_url( 'below-header.png' ),
						],
						'behind' => [
							'src' => kenta_image_url( 'behind-header.png' ),
						],
					] )
				,
				( new Condition() )
					->setCondition( [ 'kenta_' . $type . '_featured_image_position' => 'behind' ] )
					->setControls( apply_filters( "kenta_{$type}_behind_featured_image_controls", $behind_controls ) )
					->setReverseControls( apply_filters( "kenta_{$type}_non_behind_featured_image_controls", $non_behind_controls ) )
				,
				( new Filters( 'kenta_' . $type . '_featured_image_filter' ) )
					->setLabel( __( 'Css Filter', 'kenta' ) )
					->asyncCss( [
						$defaults['selector'] . ' img',
						'.kenta-article-header-background img'
					], AsyncCss::filters() )
				,
			];

			return apply_filters( "kenta_{$type}_featured_image_controls", $controls );
		}
	}

}