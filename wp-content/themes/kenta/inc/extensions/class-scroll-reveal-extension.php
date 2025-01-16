<?php

use LottaFramework\Customizer\Controls\Number;
use LottaFramework\Customizer\Controls\Placeholder;
use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Facades\CZ;

if ( ! class_exists( 'Kenta_Scroll_Reveal_Extension' ) ) {
	/**
	 * Class for scroll reveal extension
	 *
	 * @package Kena
	 */
	class Kenta_Scroll_Reveal_Extension {

		public function __construct() {
			add_filter( 'kenta_global_section_controls', [ $this, 'injectControls' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		}

		public function enqueue_scripts() {
			if ( CZ::checked( 'kenta_global_scroll_reveal' ) ) {

				if ( ! is_customize_preview() || CZ::checked( 'kenta_customize_preview_scroll_reveal' ) ) {

					wp_enqueue_script(
						'scrollreveal',
						get_template_directory_uri() . '/dist/vendor/scrollreveal/scrollreveal.min.js',
						[],
						KENTA_VERSION
					);

				}
			}
		}

		public function injectControls( $controls ) {
			$controls[] = ( new Section( 'kenta_global_scroll_reveal' ) )
				->setLabel( __( 'Scroll Reveal', 'kenta' ) )
				->enableSwitch()
				->setControls( $this->getScrollRevealControls() );

			return $controls;
		}

		protected function getScrollRevealControls() {
			$controls = [
				( new Toggle( 'kenta_customize_preview_scroll_reveal' ) )
					->setLabel( __( 'Enable On Customize Preview', 'kenta' ) )
					->openByDefault()
				,
				( new Separator() ),
				( new Number( 'kenta_scroll_reveal_delay' ) )
					->setLabel( __( 'Delay', 'kenta' ) )
					->setMin( 0 )
					->setMax( 500 )
					->setDefaultValue( 200 )
				,
				( new Number( 'kenta_scroll_reveal_duration' ) )
					->setLabel( __( 'Duration', 'kenta' ) )
					->setMin( 100 )
					->setMax( 1000 )
					->setDefaultValue( 600 )
				,
			];

			if ( ! KENTA_CMP_PRO_ACTIVE ) {
				$controls = array_merge( $controls, [
					( new Placeholder( 'kenta_scroll_reveal_interval' ) )
						->setDefaultValue( 200 )
					,
					( new Placeholder( 'kenta_scroll_reveal_opacity' ) )
						->setDefaultValue( 0 )
					,
					( new Placeholder( 'kenta_scroll_reveal_scale' ) )
						->setDefaultValue( 1 )
					,
					( new Placeholder( 'kenta_scroll_reveal_origin' ) )
						->setDefaultValue( 'bottom' )
					,
					( new Placeholder( 'kenta_scroll_reveal_distance' ) )
						->setDefaultValue( '200px' )
					,
					kenta_upsell_info_control( __( 'More scroll reveal options in %sPro Version%s', 'kenta' ) )
						->showBackground()
				] );
			}

			return apply_filters( 'kenta_scroll_reveal_controls', $controls );
		}
	}
}
new Kenta_Scroll_Reveal_Extension();
