<?php
/**
 * Store catalog section
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\ImageRadio;
use LottaFramework\Customizer\Controls\Number;
use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Slider;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Section as CustomizerSection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Store_Catalog_Section' ) ) {

	class Kenta_Store_Catalog_Section extends CustomizerSection {

		use Kenta_Post_Card;

		public function getControls() {
			return [
				( new Separator( 'kenta_store_catalog_default_divider' ) ),
				( new Slider( 'kenta_store_catalog_columns' ) )
					->setLabel( __( 'Shop Columns', 'kenta' ) )
					->bindSelectiveRefresh( 'kenta-woo-selective-css' )
					->setDefaultUnit( false )
					->setMin( 1 )
					->setMax( 6 )
					->enableResponsive()
					->setDefaultValue( [
						'desktop' => 4,
						'tablet'  => 2,
						'mobile'  => 1,
					] )
				,
				( new Slider( 'kenta_store_catalog_gap' ) )
					->setLabel( __( 'Shop Gap', 'kenta' ) )
					->asyncCss( '.kenta-products', [ '--card-gap' => 'value' ] )
					->enableResponsive()
					->setDefaultUnit( 'px' )
					->setDefaultValue( '24px' )
				,
				( new Number( 'kenta_store_catalog_per_page' ) )
					->setLabel( __( 'Products Per Page', 'kenta' ) )
					->setDefaultValue( 12 )
					->setMin( 1 )
					->setMax( 99999 )
					->setDefaultUnit( false )
				,
				( new Section( 'kenta_store_product_card_section' ) )
					->setLabel( __( 'Store Product Card', 'kenta' ) )
					->setControls( $this->getCardControls() )
				,

				( new Section( 'kenta_store_sidebar_section' ) )
					->setLabel( __( 'Sidebar', 'kenta' ) )
					->enableSwitch( false )
					->setControls( [
						( new ImageRadio( 'kenta_store_sidebar_layout' ) )
							->setLabel( __( 'Sidebar Layout', 'kenta' ) )
							->setDefaultValue( 'right-sidebar' )
							->setChoices( [
								'left-sidebar'  => [
									'title' => __( 'Left Sidebar', 'kenta' ),
									'src'   => kenta_image_url( 'left-sidebar.png' ),
								],
								'right-sidebar' => [
									'title' => __( 'Right Sidebar', 'kenta' ),
									'src'   => kenta_image_url( 'left-sidebar.png' ),
								],
							] )
						,
					] )
			];
		}

		protected function getCardControls() {
			$selector = '.woocommerce .kenta-products li.product .kenta-product-wrapper';

			$content_controls = $this->getCardContentControls( 'kenta_store_', [
				'selector'          => $selector,
				'content-spacing'   => '0px',
				'thumbnail-spacing' => '24px',
			] );

			$style_controls = $this->getCardStyleControls( 'kenta_store_', [
				'preset'    => 'ghost',
				'selective' => 'kenta-woo-selective-css',
			] );

			return [
				( new Tabs() )
					->setActiveTab( 'content' )
					->addTab( 'content', __( 'Content', 'kenta' ), apply_filters(
						'kenta_store_card_content_controls', $content_controls
					) )
					->addTab( 'style', __( 'Style', 'kenta' ), apply_filters(
						'kenta_store_card_style_controls', $style_controls, [
							'selector' => $selector,
						]
					) )
			];
		}
	}
}
