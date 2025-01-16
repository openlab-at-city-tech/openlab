<?php

use LottaFramework\Customizer\Controls\MultiSelect;
use LottaFramework\Customizer\Controls\Section;
use LottaFramework\Customizer\Controls\Separator;
use LottaFramework\Facades\CZ;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Kenta_Particles_Extension' ) ) {

	class Kenta_Particles_Extension {

		use Kenta_Particles_Effect;

		public function __construct() {
			// Register particles.js script
			add_action( 'wp_enqueue_scripts', [ $this, 'registerParticlesScript' ] );

			// Add dynamic css for particles effect
			add_filter( 'kenta_header_row_css', [ $this, 'header_row_particles_css' ], 10, 2 );
			add_filter( 'kenta_filter_dynamic_css', [ $this, 'site_background_particles_css' ] );
			add_filter( 'kenta_filter_dynamic_css', [ $this, 'featured_image_particles_css' ] );

			// Inject particle effect controls
			add_filter( 'kenta_header_row_style_controls', [ $this, 'header_row_particle_effect_controls' ], 11, 3 );
			add_filter( 'kenta_background_controls', [ $this, 'background_particle_effect_controls' ] );
			add_filter( 'kenta_post_behind_featured_image_controls', [
				$this,
				'post_featured_image_particle_effect_controls'
			] );
			add_filter( 'kenta_page_behind_featured_image_controls', [
				$this,
				'page_featured_image_particle_effect_controls'
			] );

			// Render particles canvas
			add_action( 'kenta_start_header_row', [ $this, 'render_header_row_particles_canvas' ], 11 );
			add_action( 'kenta_action_before', [ $this, 'render_background_particles_canvas' ] );
			add_action( 'wp_body_open', [ $this, 'render_body_particles_canvas' ] );
			add_action( 'kenta_before_render_featured_image', [ $this, 'render_featured_image_particles_canvas' ] );

			// Preset effects
			$this->particles_presets = apply_filters( 'kenta_particles_effect_presets', [
				'default'        => '{"particles":{"number":{"value":120,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":3,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":true,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":2,"direction":"none","random":true,"straight":false,"out_mode":"bounce","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"repulse"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
				'gather'         => '{"particles":{"number":{"value":120,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":3,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":true,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":2,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"grab"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":150,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
				'parallax'       => '{"autoPlay":true,"background":{},"backgroundMask":{"composite":"destination-out","cover":{"color":{"value":"#fff"},"opacity":1},"enable":false},"defaultThemes":{},"delay":0,"fullScreen":{"enable":false,"zIndex":1},"detectRetina":true,"duration":0,"fpsLimit":120,"interactivity":{"detectsOn":"window","events":{"onClick":{"enable":true,"mode":"push"},"onDiv":{"selectors":[],"enable":false,"mode":[],"type":"circle"},"onHover":{"enable":true,"mode":"grab","parallax":{"enable":true,"force":60,"smooth":10}},"resize":{"delay":0.5,"enable":true}},"modes":{"trail":{"delay":1,"pauseOnStop":false,"quantity":1},"attract":{"distance":200,"duration":0.4,"easing":"ease-out-quad","factor":1,"maxSpeed":50,"speed":1},"bounce":{"distance":200},"bubble":{"distance":400,"duration":2,"mix":false,"opacity":0.8,"size":40,"divs":{"distance":200,"duration":0.4,"mix":false,"selectors":[]}},"connect":{"distance":80,"links":{"opacity":0.5},"radius":60},"grab":{"distance":400,"links":{"blink":false,"consent":false,"opacity":1}},"push":{"default":true,"groups":[],"quantity":4},"remove":{"quantity":2},"repulse":{"distance":200,"duration":0.4,"factor":100,"speed":1,"maxSpeed":50,"easing":"ease-out-quad","divs":{"distance":200,"duration":0.4,"factor":100,"speed":1,"maxSpeed":50,"easing":"ease-out-quad","selectors":[]}},"slow":{"factor":3,"radius":200},"light":{"area":{"gradient":{"start":{"value":"#ffffff"},"stop":{"value":"#000000"}},"radius":1000},"shadow":{"color":{"value":"#000000"},"length":2000}}}},"manualParticles":[],"particles":{"bounce":{"horizontal":{"random":{"enable":false,"minimumValue":0.1},"value":1},"vertical":{"random":{"enable":false,"minimumValue":0.1},"value":1}},"collisions":{"absorb":{"speed":2},"bounce":{"horizontal":{"random":{"enable":false,"minimumValue":0.1},"value":1},"vertical":{"random":{"enable":false,"minimumValue":0.1},"value":1}},"enable":false,"maxSpeed":50,"mode":"bounce","overlap":{"enable":true,"retries":0}},"color":{"value":"#ffffff","animation":{"h":{"count":0,"enable":false,"offset":0,"speed":1,"delay":0,"decay":0,"sync":true},"s":{"count":0,"enable":false,"offset":0,"speed":1,"delay":0,"decay":0,"sync":true},"l":{"count":0,"enable":false,"offset":0,"speed":1,"delay":0,"decay":0,"sync":true}}},"groups":{},"move":{"angle":{"offset":0,"value":90},"attract":{"distance":200,"enable":false,"rotate":{"x":600,"y":1200}},"center":{"x":50,"y":50,"mode":"percent","radius":0},"decay":0,"distance":{},"direction":"none","drift":0,"enable":true,"gravity":{"acceleration":9.81,"enable":false,"inverse":false,"maxSpeed":50},"path":{"clamp":true,"delay":{"random":{"enable":false,"minimumValue":0},"value":0},"enable":false,"options":{}},"outModes":{"default":"out","bottom":"out","left":"out","right":"out","top":"out"},"random":false,"size":false,"speed":2,"spin":{"acceleration":0,"enable":false},"straight":false,"trail":{"enable":false,"length":10,"fill":{}},"vibrate":false,"warp":false},"number":{"density":{"enable":true,"width":1920,"height":1080},"limit":0,"value":160},"opacity":{"random":{"enable":true,"minimumValue":0.1},"value":{"min":0.1,"max":0.5},"animation":{"count":0,"enable":true,"speed":3,"decay":0,"delay":0,"sync":false,"mode":"auto","startValue":"random","destroy":"none","minimumValue":0.1}},"reduceDuplicates":false,"shadow":{"blur":0,"color":{"value":"#000"},"enable":false,"offset":{"x":0,"y":0}},"shape":{"close":true,"fill":true,"options":{},"type":"circle"},"size":{"random":{"enable":true,"minimumValue":1},"value":{"min":0.1,"max":4},"animation":{"count":0,"enable":true,"speed":20,"decay":0,"delay":0,"sync":false,"mode":"auto","startValue":"random","destroy":"none","minimumValue":0.1}},"stroke":{"width":0},"zIndex":{"random":{"enable":false,"minimumValue":0},"value":0,"opacityRate":1,"sizeRate":1,"velocityRate":1},"destroy":{"bounds":{},"mode":"none","split":{"count":1,"factor":{"random":{"enable":false,"minimumValue":0},"value":3},"rate":{"random":{"enable":false,"minimumValue":0},"value":{"min":4,"max":9}},"sizeOffset":true,"particles":{}}},"roll":{"darken":{"enable":false,"value":0},"enable":false,"enlighten":{"enable":false,"value":0},"mode":"vertical","speed":25},"tilt":{"random":{"enable":false,"minimumValue":0},"value":0,"animation":{"enable":false,"speed":0,"decay":0,"sync":false},"direction":"clockwise","enable":false},"twinkle":{"lines":{"enable":false,"frequency":0.05,"opacity":1},"particles":{"enable":false,"frequency":0.05,"opacity":1}},"wobble":{"distance":5,"enable":false,"speed":{"angle":50,"move":10}},"life":{"count":0,"delay":{"random":{"enable":false,"minimumValue":0},"value":0,"sync":false},"duration":{"random":{"enable":false,"minimumValue":0.0001},"value":0,"sync":false}},"rotate":{"random":{"enable":false,"minimumValue":0},"value":0,"animation":{"enable":false,"speed":0,"decay":0,"sync":false},"direction":"clockwise","path":false},"orbit":{"animation":{"count":0,"enable":false,"speed":1,"decay":0,"delay":0,"sync":false},"enable":false,"opacity":1,"rotation":{"random":{"enable":false,"minimumValue":0},"value":45},"width":1},"links":{"blink":false,"color":{"value":"#ffffff"},"consent":false,"distance":150,"enable":true,"frequency":1,"opacity":0.4,"shadow":{"blur":5,"color":{"value":"#000"},"enable":false},"triangles":{"enable":false,"frequency":1},"width":1,"warp":false},"repulse":{"random":{"enable":false,"minimumValue":0},"value":0,"enabled":false,"distance":1,"duration":1,"factor":1,"speed":1}},"pauseOnBlur":true,"pauseOnOutsideViewport":true,"responsive":[],"smooth":false,"style":{},"themes":[],"zLayers":100,"motion":{"disable":false,"reduce":{"factor":4,"value":true}}}',
				'nasa'           => '{"particles":{"number":{"value":160,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":1,"random":true,"anim":{"enable":true,"speed":1,"opacity_min":0,"sync":false}},"size":{"value":3,"random":true,"anim":{"enable":false,"speed":4,"size_min":0.3,"sync":false}},"line_linked":{"enable":false,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":1,"direction":"none","random":true,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":600}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"bubble"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":250,"size":0,"duration":2,"opacity":0,"speed":3},"repulse":{"distance":400,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
				'polygon-bubble' => '{"particles":{"number":{"value":6,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"polygon","stroke":{"width":0,"color":"#000"},"polygon":{"nb_sides":6},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.3,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":160,"random":false,"anim":{"enable":true,"speed":10,"size_min":40,"sync":false}},"line_linked":{"enable":false,"distance":200,"color":"#ffffff","opacity":1,"width":2},"move":{"enable":true,"speed":8,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"grab"},"onclick":{"enable":false,"mode":"push"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
				'circle-bubble'  => '{"particles":{"number":{"value":6,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000"},"polygon":{"nb_sides":6},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.3,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":160,"random":false,"anim":{"enable":true,"speed":10,"size_min":40,"sync":false}},"line_linked":{"enable":false,"distance":200,"color":"#ffffff","opacity":1,"width":2},"move":{"enable":true,"speed":8,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"grab"},"onclick":{"enable":false,"mode":"push"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
				'snow'           => '{"particles":{"number":{"value":400,"density":{"enable":true,"value_area":800}},"color":{"value":"#fff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.5,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":3,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":false,"distance":500,"color":"#ffffff","opacity":0.4,"width":2},"move":{"enable":true,"speed":4,"direction":"bottom","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"repulse"},"onclick":{"enable":false,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":0.5}},"bubble":{"distance":400,"size":4,"duration":0.3,"opacity":1,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
				'fire-spark'     => '{"particles":{"number":{"value":400,"density":{"enable":true,"value_area":3000}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":3},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.5,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":4,"random":true,"anim":{"enable":true,"speed":5,"size_min":0,"sync":false}},"line_linked":{"enable":false,"distance":500,"color":"#ffffff","opacity":0.4,"width":2},"move":{"enable":true,"speed":7.8914764163227265,"direction":"top","random":true,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"bubble"},"onclick":{"enable":false,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":0.5}},"bubble":{"distance":400,"size":4,"duration":0.3,"opacity":1,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
				'nyancat'        => '{"particles":{"number":{"value":100,"density":{"enable":false,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"star","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"http://wiki.lexisnexis.com/academic/images/f/fb/Itunes_podcast_icon_300.jpg","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":4,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":false,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":14,"direction":"left","random":false,"straight":true,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"grab"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":200,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
			] );
		}

		public function header_row_particles_css( $css, $id ) {
			return array_merge( $css, $this->getParticlesCss( 'kenta_header_' . $id . '_row_' ) );
		}

		public function site_background_particles_css( $css ) {
			return array_merge( $css, $this->getParticlesCss(
				'kenta_site_background',
				'.kenta_site_background_particles_canvas,.kenta_site_body_particles'
			) );
		}

		public function featured_image_particles_css( $css ) {
			if ( is_single() || is_page() ) {
				$article_type = is_page() ? 'page' : 'post';
				$prefix       = 'kenta_' . $article_type;

				if ( CZ::checked( "{$prefix}_featured_image_enable_particles" )
				     && CZ::get( "{$prefix}_featured_image_position" ) === 'behind' ) {
					return array_merge( $css, $this->getParticlesCss(
						"{$prefix}_featured_image"
					) );
				}
			}

			return $css;
		}

		public function render_header_row_particles_canvas( $id ) {
			$this->renderParticlesCanvas( 'kenta_header_' . $id . '_row_' );
		}

		public function render_background_particles_canvas() {
			if ( ! CZ::checked( 'kenta_enable_site_wrap' ) ) {
				$this->renderParticlesCanvas( 'kenta_site_background' );

				return;
			}

			$scope = CZ::get( 'kenta_site_background_particles_scope' );
			if ( isset( $scope['site-content'] ) && $scope['site-content'] === 'yes' ) {
				$this->renderParticlesCanvas( 'kenta_site_background' );
			}
		}

		public function render_body_particles_canvas() {
			if ( ! CZ::checked( 'kenta_enable_site_wrap' ) ) {
				return;
			}

			$scope = CZ::get( 'kenta_site_background_particles_scope' );
			if ( isset( $scope['site-body'] ) && $scope['site-body'] === 'yes' ) {
				$this->renderParticlesCanvas( 'kenta_site_background', 'kenta_site_body_particles' );
			}
		}

		public function render_featured_image_particles_canvas( $prefix ) {
			if ( CZ::get( "{$prefix}_featured_image_position" ) !== 'behind' ) {
				return;
			}

			$this->renderParticlesCanvas( "{$prefix}_featured_image" );
		}

		/**
		 * @param $controls
		 * @param $key
		 * @param $id
		 *
		 * @return array
		 */
		public function header_row_particle_effect_controls( $controls, $key, $id ) {
			return array_merge(
				$controls,
				[ ( new Separator() ) ],
				$this->getParticleEffectControls( $key ),
				[ ( new Separator() ) ]
			);
		}

		/**
		 * @param $controls
		 * @param $key
		 * @param $id
		 *
		 * @return array
		 */
		public function background_particle_effect_controls( $controls ) {
			return array_merge( $controls, [
				( new Section( 'kenta_site_background_enable_particles' ) )
					->setLabel( __( 'Background Particles Effect', 'kenta' ) )
					->enableSwitch( false )
					->setControls( array_merge(
						[
							( new \LottaFramework\Customizer\Controls\Condition() )
								->setCondition( [
									'kenta_enable_site_wrap'                 => 'yes',
									'kenta_site_background_enable_particles' => 'yes',
								] )
								->setControls( [
									( new MultiSelect( 'kenta_site_background_particles_scope' ) )
										->setLabel( __( 'Scope', 'kenta' ) )
										->buttonsGroupView()
										->setChoices( [
											'site-body'    => __( 'Site Body', 'kenta' ),
											'site-content' => __( 'Site Content', 'kenta' ),
										] )
										->setDefaultValue( [
											'site-body'    => 'yes',
											'site-content' => 'no',
										] )
									,
								] )
							,
						],
						$this->getParticleEffectControls( 'kenta_site_background', [ 'enable' ] )
					) )
				,
			] );
		}

		/**
		 * @param $controls
		 *
		 * @return array
		 */
		public function post_featured_image_particle_effect_controls( $controls ) {
			return array_merge(
				$controls,
				[ ( new Separator() ) ],
				$this->getParticleEffectControls( "kenta_post_featured_image" ),
				[ ( new Separator() ) ]
			);
		}

		/**
		 * @param $controls
		 *
		 * @return array
		 */
		public function page_featured_image_particle_effect_controls( $controls ) {
			return array_merge(
				$controls,
				[ ( new Separator() ) ],
				$this->getParticleEffectControls( "kenta_page_featured_image" ),
				[ ( new Separator() ) ]
			);
		}
	}
}

new Kenta_Particles_Extension();
