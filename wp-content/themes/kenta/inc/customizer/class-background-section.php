<?php
/**
 * Background customizer section
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Background;
use LottaFramework\Customizer\Controls\BoxShadow;
use LottaFramework\Customizer\Controls\ColorPalettes;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Filters;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Customizer\Section;
use LottaFramework\Facades\AsyncCss;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Background_Section' ) ) {

	class Kenta_Background_Section extends \LottaFramework\Customizer\Section {

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			$controls = [
				( new Toggle( 'kenta_enable_site_wrap' ) )
					->setLabel( __( 'Site Wrap', 'kenta' ) )
					->setDescription( __( 'Enable boundaries for your site on large screens (>1600px)', 'kenta' ) )
					->asyncCss( '.kenta-site-wrap', [
						'max-width'               => AsyncCss::unescape( AsyncCss::valueMapper( [
							'yes' => '1600px',
							'no'  => 'inherit',
						] ) ),
						'--kenta-site-wrap-width' => AsyncCss::unescape( AsyncCss::valueMapper( [
							'yes' => '1600px',
							'no'  => '100vw',
						] ) ),
					] )
					->closeByDefault()
				,
				( new Background( 'kenta_site_background' ) )
					->setLabel( __( 'Site Background', 'kenta' ) )
					->asyncCss( '.kenta-site-wrap', AsyncCss::background() )
					->enableResponsive()
					->setDefaultValue( [
						'type'  => 'color',
						'color' => 'var(--kenta-base-100)',
					] )
				,
				( new Condition( 'kenta_site_wrap_condition' ) )
					->setCondition( [ 'kenta_enable_site_wrap' => 'yes' ] )
					->setControls( [
						( new Background( 'kenta_site_body_background' ) )
							->setLabel( __( 'Body Background', 'kenta' ) )
							->asyncCss( '.kenta-body', AsyncCss::background() )
							->enableResponsive()
							->setDefaultValue( [
								'type'  => 'color',
								'color' => 'var(--kenta-base-200)',
							] )
						,
						( new BoxShadow( 'kenta_site_wrap_shadow' ) )
							->setLabel( __( 'Site Box Shadow', 'kenta' ) )
							->asyncCss( '.kenta-site-wrap', AsyncCss::shadow() )
							->setDefaultShadow(
								'rgba(44, 62, 80, 0.06)',
								'0px',
								'0px',
								'24px',
								'0px',
								true
							)
						,
					] )
				,
				kenta_docs_control(
					__( '%sLearn how to use site wrap & site background%s', 'kenta' ),
					'https://kentatheme.com/docs/kenta-theme/general-theme-options/site-wrap-site-background/',
					'kenta_site_wrap_doc'
				)->hideBackground(),
				( new Filters( 'kenta_site_filters' ) )
					->setLabel( __( 'Site Css Filters', 'kenta' ) )
					->asyncCss( ':root', AsyncCss::filters() )
				,
				kenta_docs_control(
					__( '%sLearn how to use site filters', 'kenta' ),
					'https://kentatheme.com/docs/kenta-theme/general-theme-options/site-css-filters/',
					'kenta_site_filters_doc'
				)->hideBackground(),
			];

			return apply_filters( 'kenta_background_controls', $controls );
		}
	}
}
