<?php
/**
 * Store notice section
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Background;
use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Section as CustomizerSection;
use LottaFramework\Facades\AsyncCss;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Store_Notice_Section' ) ) {

	class Kenta_Store_Notice_Section extends CustomizerSection {

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			return [
				( new Separator( 'kenta_store_notice_divider' ) ),
				( new ColorPicker( 'kenta_store_notice_colors' ) )
					->setLabel( __( 'Colors', 'kenta' ) )
					->asyncColors( '.woocommerce-store-notice, p.demo_store', [
						'text'            => 'color',
						'dismiss-initial' => '--kenta-link-initial-color',
						'dismiss-hover'   => '--kenta-link-hover-color',
					] )
					->addColor( 'text', __( 'Text', 'kenta' ), '#ffffff' )
					->addColor( 'dismiss-initial', __( 'Dismiss Initial', 'kenta' ), '#ffffff' )
					->addColor( 'dismiss-hover', __( 'Dismiss Hover', 'kenta' ), '#ffffff' )
				,
				( new Background( 'kenta_store_notice_background' ) )
					->setLabel( __( 'Background', 'kenta' ) )
					->asyncCss( '.woocommerce-store-notice, p.demo_store', AsyncCss::background() )
					->setDefaultValue( [
						'type'  => 'color',
						'color' => 'var(--kenta-primary-color)'
					] )
				,
			];
		}
	}
}
