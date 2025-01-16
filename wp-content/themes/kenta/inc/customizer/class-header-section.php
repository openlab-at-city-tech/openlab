<?php
/**
 * Header customizer section
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Background;
use LottaFramework\Customizer\Controls\Border;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\ImageUploader;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Tabs;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\Section as CustomizerSection;
use LottaFramework\Facades\AsyncCss;
use LottaFramework\Facades\Css;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Header_Section' ) ) {

	class Kenta_Header_Section extends CustomizerSection {

		use Kenta_Global_Color_Controls;

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			$controls = [
				kenta_docs_control(
					__( '%sLearn how to use header builder%s', 'kenta' ),
					'https://kentatheme.com/docs/kenta-theme/header-footer-builder/',
					'kenta_header_builder_doc'
				),
				Kenta_Header_Builder::instance()->builder()->setPreviewLocation( $this->id ),

				( new Section( 'kenta_header_colors_override' ) )
					->setLabel( __( 'Override Global Colors', 'kenta' ) )
					->setControls( $this->getGlobalColorControls( 'kenta_header_', '.kenta-site-header' ) )
				,

				( new Section( 'kenta_transparent_header' ) )
					->setLabel( __( 'Transparent Header', 'kenta' ) )
					->setControls( $this->transparentHeaderControls() )
				,
			];

			return apply_filters( 'kenta_header_builder_controls', $controls );
		}

		protected function transparentHeaderControls() {
			return [
				kenta_docs_control( __( '%sRead Documentation%s', 'kenta' ), 'https://kentatheme.com/docs/kenta-theme/header-footer-builder/transparent-header/' ),
				( new Tabs() )
					->setActiveTab( 'general' )
					->addTab( 'general', __( 'General', 'kenta' ), [
						( new Toggle( 'kenta_enable_transparent_header' ) )
							->setLabel( __( 'Enable Transparent Header', 'kenta' ) )
							->closeByDefault()
						,
						( new Condition() )
							->setCondition( [ 'kenta_enable_transparent_header' => 'yes' ] )
							->setControls( [
								( new Toggle( 'kenta_disable_archive_transparent_header' ) )
									->setLabel( __( 'Disable on Search & Archives', 'kenta' ) )
									->openByDefault()
								,
								( new Toggle( 'kenta_disable_posts_transparent_header' ) )
									->setLabel( __( 'Disable on Posts', 'kenta' ) )
									->closeByDefault()
								,
								( new Toggle( 'kenta_disable_pages_transparent_header' ) )
									->setLabel( __( 'Disable on Pages', 'kenta' ) )
									->closeByDefault()
								,
							] )
						,
						( new Separator() ),
						( new Radio( 'kenta_enable_transparent_header_device' ) )
							->setLabel( __( 'Enable Device', 'kenta' ) )
							->setDefaultValue( 'desktop' )
							->buttonsGroupView()
							->setChoices( [
								'all'     => __( 'All', 'kenta' ),
								'desktop' => __( 'Desktop', 'kenta' ),
								'mobile'  => __( 'Mobile', 'kenta' ),
							] )
						,
						( new Separator() ),
						( new Toggle( 'kenta_enable_transparent_header_logo' ) )
							->setLabel( __( 'Logo for Transparent Header', 'kenta' ) )
							->selectiveRefresh( '.kenta-site-header', 'kenta_header_render' )
							->closeByDefault()
						,
						( new Condition() )
							->setCondition( [
								'kenta_enable_transparent_header_logo' => 'yes'
							] )
							->setControls( [
								( new ImageUploader( 'kenta_transparent_header_logo' ) )
									->setLabel( __( 'Logo', 'kenta' ) )
									->setDefaultValue( '' )
									->selectiveRefresh( '.kenta-site-header', 'kenta_header_render' )
								,
							] )
						,
					] )
					->addTab( 'style', __( 'Style', 'kenta' ), [
						( new ColorPicker( 'kenta_trans_header_site_title_color' ) )
							->setLabel( __( 'Site Title Colors', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-transparent-selective-css' )
							->addColor( 'initial', __( 'Initial', 'kenta' ), Css::INITIAL_VALUE )
							->addColor( 'hover', __( 'Hover', 'kenta' ), Css::INITIAL_VALUE )
						,
						( new Separator() ),
						( new ColorPicker( 'kenta_trans_header_raw_text_color' ) )
							->setLabel( __( 'Raw Text Colors', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-transparent-selective-css' )
							->addColor( 'text', __( 'Text', 'kenta' ), Css::INITIAL_VALUE )
							->addColor( 'initial', __( 'Link Initial', 'kenta' ), Css::INITIAL_VALUE )
							->addColor( 'hover', __( 'Link Hover', 'kenta' ), Css::INITIAL_VALUE )
						,
						( new Separator() ),
						( new ColorPicker( 'kenta_trans_header_menu_color' ) )
							->setLabel( __( 'Menu Colors', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-transparent-selective-css' )
							->addColor( 'initial', __( 'Initial', 'kenta' ), Css::INITIAL_VALUE )
							->addColor( 'hover', __( 'Hover', 'kenta' ), Css::INITIAL_VALUE )
							->addColor( 'active', __( 'Active', 'kenta' ), Css::INITIAL_VALUE )
						,
						( new ColorPicker( 'kenta_trans_header_menu_border_color' ) )
							->setLabel( __( 'Menu Border Colors', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-transparent-selective-css' )
							->addColor( 'initial', __( 'Initial', 'kenta' ), Css::INITIAL_VALUE )
							->addColor( 'active', __( 'Active', 'kenta' ), Css::INITIAL_VALUE )
						,
						( new ColorPicker( 'kenta_trans_header_menu_bg_color' ) )
							->setLabel( __( 'Menu Background Colors', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-transparent-selective-css' )
							->addColor( 'initial', __( 'Initial', 'kenta' ), Css::INITIAL_VALUE )
							->addColor( 'hover', __( 'Hover', 'kenta' ), Css::INITIAL_VALUE )
							->addColor( 'active', __( 'Active', 'kenta' ), Css::INITIAL_VALUE )
						,
						( new Separator() ),
						( new ColorPicker( 'kenta_trans_header_button_color' ) )
							->setLabel( __( 'Button/Icon Colors', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-transparent-selective-css' )
							->addColor( 'initial', __( 'Initial', 'kenta' ), Css::INITIAL_VALUE )
							->addColor( 'hover', __( 'Hover', 'kenta' ), Css::INITIAL_VALUE )
						,
						( new ColorPicker( 'kenta_trans_header_button_border_color' ) )
							->setLabel( __( 'Button Border Colors', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-transparent-selective-css' )
							->addColor( 'initial', __( 'Initial', 'kenta' ), Css::INITIAL_VALUE )
							->addColor( 'hover', __( 'Hover', 'kenta' ), Css::INITIAL_VALUE )
						,
						( new ColorPicker( 'kenta_trans_header_button_bg_color' ) )
							->setLabel( __( 'Button Background Colors', 'kenta' ) )
							->bindSelectiveRefresh( 'kenta-transparent-selective-css' )
							->addColor( 'initial', __( 'Initial', 'kenta' ), Css::INITIAL_VALUE )
							->addColor( 'hover', __( 'Hover', 'kenta' ), Css::INITIAL_VALUE )
						,
						( new Separator() ),
						( new Border( 'kenta_trans_header_border_top' ) )
							->setLabel( __( 'Top Border', 'kenta' ) )
							->asyncCss( '.kenta-transparent-header .kenta-header-row', AsyncCss::border( 'border-top' ) )
							->enableResponsive()
							->displayBlock()
							->setDefaultBorder(
								1,
								'none',
								'var(--kenta-base-300)',
								'',
								true
							)
						,
						( new Border( 'kenta_trans_header_border_bottom' ) )
							->setLabel( __( 'Bottom Border', 'kenta' ) )
							->asyncCss( '.kenta-transparent-header .kenta-header-row', AsyncCss::border( 'border-bottom' ) )
							->enableResponsive()
							->displayBlock()
							->setDefaultBorder(
								1,
								'none',
								'var(--kenta-base-300)',
								'',
								true
							)
						,
						( new Background( 'kenta_trans_header_bg' ) )
							->setLabel( __( 'Header Background', 'kenta' ) )
							->asyncCss( '.kenta-transparent-header .kenta-header-row', AsyncCss::background() )
							->enableResponsive()
							->setDefaultValue( [
								'type'  => 'color',
								'color' => 'var(--kenta-transparent)',
							] )
						,
					] )
			];
		}
	}
}