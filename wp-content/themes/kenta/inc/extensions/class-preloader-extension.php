<?php

use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Facades\CZ;

if ( ! class_exists( 'Kenta_Preloader_Extension' ) ) {

	/**
	 * Class for preloader
	 *
	 * @package Kenta
	 */
	class Kenta_Preloader_Extension {

		public function __construct() {
			// inject scroll top customize controls
			add_filter( 'kenta_global_section_controls', [ $this, 'injectControls' ] );
		}

		/**
		 * @param $controls
		 *
		 * @return mixed
		 */
		public function injectControls( $controls ) {

			$controls[] = ( new Section( 'kenta_global_preloader' ) )
				->setLabel( __( 'Preloader', 'kenta' ) )
				->enableSwitch()
				->setControls( $this->getPreloaderControls() );

			return $controls;
		}

		/**
		 * Preloader
		 *
		 * @return array
		 */
		protected function getPreloaderControls() {
			return [
				( new Select( 'kenta_preloader_preset' ) )
					->setLabel( __( 'Preloader Preset', 'kenta' ) )
					->setDefaultValue( 'preset-1' )
					->bindSelectiveRefresh( 'kenta-preloader-selective-css' )
					->selectiveRefresh( '.kenta-preloader-wrap', function () {
						echo wp_kses_post( kenta_get_preloader( CZ::get( 'kenta_preloader_preset' ) )['html'] );
					} )
					->setChoices( [
						'preset-1'  => __( 'Preset 1', 'kenta' ),
						'preset-2'  => __( 'Preset 2', 'kenta' ),
						'preset-3'  => __( 'Preset 3', 'kenta' ),
						'preset-4'  => __( 'Preset 4', 'kenta' ),
						'preset-5'  => __( 'Preset 5', 'kenta' ),
						'preset-6'  => __( 'Preset 6', 'kenta' ),
						'preset-7'  => __( 'Preset 7', 'kenta' ),
						'preset-8'  => __( 'Preset 8', 'kenta' ),
						'preset-9'  => __( 'Preset 9', 'kenta' ),
						'preset-10' => __( 'Preset 10', 'kenta' ),
					] )
				,
				( new Separator() ),
				( new ColorPicker( 'kenta_preloader_colors' ) )
					->setLabel( __( 'Colors', 'kenta' ) )
					->asyncColors( '.kenta-preloader-wrap', [
						'background' => '--kenta-preloader-background',
						'accent'     => '--kenta-preloader-accent',
						'primary'    => '--kenta-preloader-primary',
					] )
					->addColor( 'background', __( 'Background', 'kenta' ), 'var(--kenta-accent-color)' )
					->addColor( 'accent', __( 'Accent', 'kenta' ), 'var(--kenta-base-color)' )
					->addColor( 'primary', __( 'Primary', 'kenta' ), 'var(--kenta-primary-color)' )
				,
			];
		}
	}

}

new Kenta_Preloader_Extension();
