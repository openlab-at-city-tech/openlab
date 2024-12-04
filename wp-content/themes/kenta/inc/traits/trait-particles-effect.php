<?php
/**
 * Particles effect trait
 *
 * @package Kenta
 */

use LottaFramework\Customizer\Controls\ColorPicker;
use LottaFramework\Customizer\Controls\Condition;
use LottaFramework\Customizer\Controls\Number;
use LottaFramework\Customizer\Controls\Radio;
use LottaFramework\Customizer\Controls\Select;
use LottaFramework\Customizer\Controls\Toggle;
use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! trait_exists( 'Kenta_Particles_Effect' ) ) {

	trait Kenta_Particles_Effect {

		/**
		 * Particles effect presets
		 *
		 * @var array
		 */
		protected $particles_presets = [];

		/**
		 * Register particles.js script
		 */
		public function registerParticlesScript() {
			wp_register_script(
				'particles.js',
				get_template_directory_uri() . '/dist/vendor/particles/tsparticles.bundle.min.js',
//				get_template_directory_uri() . '/dist/vendor/particles/particles.min.js',
				[],
				KENTA_VERSION
			);
		}

		protected function getParticlesCss( $prefix, $selector = '' ) {
			if ( ! Utils::str_ends_with( $prefix, '_' ) ) {
				$prefix .= '_';
			}

			if ( ! CZ::checked( $prefix . 'enable_particles' ) ) {
				return [];
			}

			$selector = $selector === '' ? ".{$prefix}particles_canvas" : $selector;

			return [
				$selector => apply_filters( 'kenta_particles_effect_css', [
					'--kenta-particles-canvas-z-index' => CZ::get( $prefix . 'particle_canvas_z_index' ),
				], $prefix )
			];
		}

		/**
		 * Render particles canvas element
		 *
		 * @param $prefix
		 * @param string $id
		 */
		protected function renderParticlesCanvas( $prefix, $id = '' ) {
			if ( ! Utils::str_ends_with( $prefix, '_' ) ) {
				$prefix .= '_';
			}
			if ( ! CZ::checked( $prefix . 'enable_particles' ) ) {
				return;
			}

			$options = apply_filters(
				'kenta_particles_effect_options',
				$this->particles_presets[ CZ::get( $prefix . 'particle_preset' ) ] ?? '',
				$prefix
			);

			if ( empty( $options ) ) {
				return;
			}

			wp_enqueue_script( 'particles.js' );

			$attrs = apply_filters( 'kenta_particles_canvas_attrs', [
				'class'                          => Utils::clsx( [
					'kenta-particles-canvas'     => true,
					$prefix . 'particles_canvas' => true,
					$id                          => $id !== '',
				] ),
				'id'                             => $id === '' ? $prefix . 'particles' : $id,
				'data-kenta-particles'           => $options,
				// override global options
				'data-kenta-particle-detect-on'  => CZ::get( $prefix . 'particle_detect_on' ),
				'data-kenta-particle-color'      => CZ::get( $prefix . 'particle_color' )['particle'],
				'data-kenta-particle-line-color' => CZ::get( $prefix . 'particle_color' )['line'] ?? '',
			], $prefix );

			echo '<div ' . Utils::render_attribute_string( $attrs ) . '></div>';
		}

		/**
		 * Particles effect related controls
		 *
		 * @param $prefix
		 * @param array $exclude
		 *
		 * @return array
		 */
		protected function getParticleEffectControls( $prefix, $exclude = [] ) {

			if ( ! Utils::str_ends_with( $prefix, '_' ) ) {
				$prefix .= '_';
			}

			$controls = [];
			if ( ! in_array( 'enable', $exclude ) ) {
				$controls[] = ( new Toggle( $prefix . 'enable_particles' ) )
					->setLabel( __( 'Enable Particles Effect', 'kenta' ) )
					->closeByDefault();
			}

			// z-index & source
			$controls[] = ( new Condition() )
				->setCondition( [ $prefix . 'enable_particles' => 'yes' ] )
				->setControls( [
					( new Number( $prefix . 'particle_canvas_z_index' ) )
						->setLabel( __( 'Z Index', 'kenta' ) )
						->asyncCss( ".{$prefix}particles_canvas", [ '--kenta-particles-canvas-z-index' => 'value' ] )
						->setMin( - 99999 )
						->setMax( 99999 )
						->setDefaultUnit( false )
						->setDefaultValue( 1 )
					,
					( new Radio( $prefix . 'particle_source' ) )
						->setLabel( __( 'Particle Source', 'kenta' ) )
						->setDefaultValue( 'preset' )
						->buttonsGroupView()
						->setChoices( [
							'preset' => __( 'Preset', 'kenta' ),
							'custom' => __( 'Custom', 'kenta' ),
						] )
					,
				] );

			// Particles preset
			$controls[] = ( new Condition() )
				->setCondition( [
					$prefix . 'enable_particles' => 'yes',
					$prefix . 'particle_source'  => 'preset',
				] )
				->setControls( [
					( new Select( $prefix . 'particle_preset' ) )
						->setLabel( __( 'Choose Particle Preset', 'kenta' ) )
						->setDefaultValue( 'default' )
						->setChoices( apply_filters( 'kenta_particles_effect_presets_options', [
							'default'        => __( 'Default', 'kenta' ),
							'gather'         => __( 'Gather', 'kenta' ),
							'parallax'       => __( 'Parallax', 'kenta' ),
							'nasa'           => __( 'NASA', 'kenta' ),
							'polygon-bubble' => __( 'Polygon Bubble', 'kenta' ),
							'circle-bubble'  => __( 'Circle Bubble', 'kenta' ),
							'snow'           => __( 'Snow', 'kenta' ),
							'fire-spark'     => __( 'Fire Spark', 'kenta' ),
							'nyancat'        => __( 'Nyan Cat', 'kenta' ),
						] ) )
					,
				] );

			// Custom particles
			$controls[] = ( new Condition() )
				->setCondition( [
					$prefix . 'enable_particles' => 'yes',
					$prefix . 'particle_source'  => 'custom',
				] )
				->setControls( apply_filters( 'kenta_particles_custom_json_controls', [
					kenta_upsell_info_control(
						__( "You can customize your particles effect using the online editor in %sPro Version%s", 'kenta' ),
						$prefix . 'particles_custom_json_upsell'
					),
				], $prefix ) );

			// Particles override
			$controls[] = ( new Condition() )
				->setCondition( [ $prefix . 'enable_particles' => 'yes' ] )
				->setControls( apply_filters( 'kenta_particles_override_controls', [
					( new Radio( $prefix . 'particle_detect_on' ) )
						->setLabel( __( 'Interactivity Detect On', 'kenta' ) )
						->setDefaultValue( 'default' )
						->buttonsGroupView()
						->setChoices( [
							'default' => __( 'Default', 'kenta' ),
							'window'  => __( 'Window', 'kenta' ),
							'canvas'  => __( 'Canvas', 'kenta' ),
						] )
					,
					( new ColorPicker( $prefix . 'particle_color' ) )
						->setLabel( __( 'Particle Color', 'kenta' ) )
						->addColor( 'particle', __( 'Particle Color', 'kenta' ), 'var(--kenta-primary-color)' )
						->addColor( 'line', __( 'Line Color', 'kenta' ), 'var(--kenta-primary-color)' )
					,
					kenta_upsell_info_control(
						__( "More particle options available in %sPro Version%s", 'kenta' ),
						$prefix . 'particles_override_options_upsell'
					),
				], $prefix ) );

			return apply_filters( 'kenta_particles_effect_controls', $controls, $prefix, $exclude );
		}

	}

}
