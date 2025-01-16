<?php
/**
 * Footer customizer section
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Customizer\Section as CustomizerSection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Footer_Section' ) ) {

	class Kenta_Footer_Section extends CustomizerSection {

		use Kenta_Global_Color_Controls;

		/**
		 * {@inheritDoc}
		 */
		public function getControls() {
			$controls = [
				kenta_docs_control(
					__( '%sLearn how to use footer builder%s', 'kenta' ),
					'https://kentatheme.com/docs/kenta-theme/header-footer-builder/',
					'kenta_footer_builder_doc'
				),
				Kenta_Footer_Builder::instance()->builder()->setPreviewLocation( $this->id ),

				( new Section( 'kenta_footer_colors_override' ) )
					->setLabel( __( 'Override Global Colors', 'kenta' ) )
					->keepMarginBelow()
					->setControls( $this->getGlobalColorControls( 'kenta_footer_', '.kenta-footer-area' ) )
				,
			];

			return apply_filters( 'kenta_footer_builder_controls', $controls );
		}
	}
}