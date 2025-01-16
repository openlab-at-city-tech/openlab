<?php

namespace LottaFramework\Customizer;

use LottaFramework\Application;
use LottaFramework\Customizer\Traits\Settings;
use LottaFramework\Facades\Css;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Typography\Fonts;

class Customizer {

	use Settings;

	/**
	 * Global application instance
	 *
	 * @var Application
	 */
	protected $app;

	/**
	 * Store type: option/theme_mod
	 *
	 * @var string
	 */
	protected $store = 'theme_mod';

	/**
	 * Global partials
	 *
	 * @var array
	 */
	protected $partials = [];

	/**
	 * Global async scripts
	 *
	 * @var array
	 */
	protected $asyncScripts = [];

	/**
	 * @param Application $app
	 */
	public function __construct( Application $app ) {
		$this->app = $app;

		// register scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'registerScripts' ] );
		add_action( 'customize_controls_print_footer_scripts', [ $this, 'registerScripts' ] );

		if ( class_exists( '_WP_Editors' ) ) {
			// enqueue editor scripts
			add_action( 'customize_controls_print_footer_scripts', array(
				'_WP_Editors',
				'force_uncompressed_tinymce'
			), 1 );
			add_action( 'customize_controls_print_footer_scripts', array(
				'_WP_Editors',
				'print_default_editor_scripts'
			), 45 );
		}

		add_action( 'customize_controls_enqueue_scripts', [ $this, 'enqueueControlsScripts' ] );
		add_action( 'customize_preview_init', [ $this, 'enqueuePreviewScripts' ] );
		add_action( 'customize_register', [ $this, 'registerPartials' ] );
	}

	/**
	 * Reset all customizer options
	 */
	public function reset() {
		if ( $this->store === 'option' ) {
			foreach ( array_keys( $this->_settings ) as $key ) {
				delete_option( $key );
			}
		}

		remove_theme_mods();
	}

	/**
	 * Enqueue frontend scripts
	 */
	public function registerScripts() {
		wp_register_style(
			'lotta-fontawesome',
			$this->app->uri() . 'dist/vendor/fontawesome/css/all.min.css',
			[], WP_DEBUG ? time() : Application::VERSION
		);
	}

	/**
	 * Customizer control scripts
	 */
	public function enqueueControlsScripts() {
		$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

		wp_enqueue_style( 'lotta-fontawesome' );

		wp_enqueue_script(
			'lotta-customizer-script',
			$this->app->uri() . 'dist/js/customizer' . $suffix . '.js',
			[
				'customize-controls',
				'wp-element',
				'wp-components',
				'wp-color-picker'
			],
			Application::VERSION,
			true
		);

		wp_enqueue_style(
			'lotta-customizer-style',
			$this->app->uri() . 'dist/css/customizer' . $suffix . '.css',
			[ 'wp-components', 'wp-color-picker' ],
			Application::VERSION
		);

		$this->enqueueLocalize( 'lotta-customizer-script' );
	}

	/**
	 * Customizer preview scripts
	 */
	public function enqueuePreviewScripts() {
		$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

		wp_enqueue_script(
			'lotta-customizer-preview-script',
			$this->app->uri() . 'dist/js/customizer-preview' . $suffix . '.js',
			[ 'customize-preview', 'customize-selective-refresh' ],
			Application::VERSION
		);

		wp_enqueue_style(
			'lotta-customizer-preview-style',
			$this->app->uri() . 'dist/css/customizer-preview' . $suffix . '.css',
			[],
			Application::VERSION
		);

		$this->enqueueLocalize( 'lotta-customizer-preview-script' );

		wp_register_script( 'lotta-async-scripts', false );
		wp_enqueue_script( 'lotta-async-scripts' );
		wp_add_inline_script( 'lotta-async-scripts', $this->generateAsyncScripts() );
	}

	/**
	 * Register partials
	 *
	 * @param $wp_customize
	 */
	public function registerPartials( $wp_customize ) {
		if ( isset( $wp_customize->selective_refresh ) ) {
			foreach ( $this->partials as $partial => $args ) {
				$wp_customize->selective_refresh->add_partial( $partial, $args );
			}
		}
	}

	/**
	 * Bind selective refresh
	 *
	 * @param $partial
	 * @param $setting
	 *
	 * @return $this
	 */
	public function bindSelectiveRefresh( $partial, $setting ) {
		if ( isset( $this->partials[ $partial ] ) ) {
			$args               = $this->partials[ $partial ];
			$args['settings'][] = $setting;

			$this->partials[ $partial ] = $args;
		}

		return $this;
	}

	/**
	 * Add selective refresh partial
	 *
	 * @param $id
	 * @param $selector
	 * @param $render_callback
	 *
	 * @return $this
	 */
	public function addPartial( $id, $selector, $render_callback ) {
		$this->partials[ $id ] = [
			'selector'        => $selector,
			'settings'        => [],
			'render_callback' => $render_callback,
		];

		return $this;
	}

	/**
	 * Save async scripts
	 *
	 * @param $id
	 * @param $script
	 *
	 * @return $this
	 */
	public function addAsync( $id, $script ) {
		$this->asyncScripts[ $id ] = $script;

		return $this;
	}

	/**
	 * Change default store type
	 *
	 * @param string $type
	 */
	public function storeAs( string $type ) {
		$this->store = $type;
	}

	/**
	 * Get setting
	 *
	 * @param $id
	 *
	 * @return mixed|void|null
	 */
	public function get( $id, array $settings = [] ) {

		if ( isset( $settings[ $id ] ) ) {
			return $settings[ $id ];
		}

		$settings = $this->_settings;
		if ( ! isset( $settings[ $id ] ) ) {
			return null;
		}

		$default = $settings[ $id ]['default'] ?? null;
		$value   = $this->getSetting( $id, $default );

		return apply_filters( $this->app->uniqid( $id ), $value );
	}

	/**
	 * Get theme_mod or option
	 *
	 * @param $id
	 * @param mixed $default
	 *
	 * @return false|mixed|void
	 */
	protected function getSetting( $id, $default = false ) {
		if ( $this->store === 'option' ) {
			return get_option( $id, $default );
		}

		return get_theme_mod( $id, $default );
	}

	/**
	 * Add a section with controls
	 *
	 * @param \WP_Customize_Manager|null $wp_customize
	 * @param $id
	 * @param array $args
	 * @param array $controls
	 *
	 * @return string
	 */
	public function addSection( $wp_customize, $id, $args = [], $controls = [] ) {
		if ( $id instanceof Section ) {
			$args     = $id->getSectionArgs();
			$controls = $id->getControls();
			$id       = $id->getId();
		}

		if ( $wp_customize ) {
			$wp_customize->add_section( $id, $args );
		}

		foreach ( $controls as $control ) {
			if ( $control instanceof Control ) {
				$control->setSection( $id );
			} else if ( is_array( $control ) ) {
				$control['section'] = $id;
			}
			$this->addControl( $wp_customize, $control );
		}

		return $id;
	}

	/**
	 * Add control
	 *
	 * @param \WP_Customize_Manager|null $wp_customize
	 * @param $args
	 * @param bool $has_control
	 */
	public function addControl( $wp_customize, $args, bool $has_control = true ) {
		if ( $args instanceof Control ) {
			$args = $args->toArray();
		}

		$this->register( $args );

		$this->app->do_action( 'before_register_' . $args['id'] );

		// this is a valid control with setting
		if ( isset( $args['default'] ) && $wp_customize ) {
			$wp_customize->add_setting( $args['id'], array_merge( [
				'type'              => $this->store,
				'default'           => $args['default'] ?? null,
				'sanitize_callback' => $args['sanitize_callback'] ?? '',
				'transport'         => $args['transport'] ?? 'refresh',
			], $args['setting'] ?? [] ) );

			if ( isset( $wp_customize->selective_refresh ) && isset( $args['selective_refresh'] ) ) {
				$wp_customize->selective_refresh->add_partial( $args['id'], $args['selective_refresh'] );
			}
		}

		// Register control
		if ( $has_control && $wp_customize ) {
			$instance = new \WP_Customize_Control(
				$wp_customize,
				$args['id'],
				$args
			);

			$options = $args['options'] ?? [];
			if ( isset( $args['default'] ) ) {
				$options['default'] = $args['default'];
			}

			if ( isset( $args['choices'] ) ) {
				$instance->json['choices'] = $args['choices'];
			}

			if ( isset( $options['condition'] ) ) {
				$instance->json['condition'] = $options['condition'];
			}

			$instance->json['options'] = $options;

			$wp_customize->add_control( $instance );
		}

		// It's a container and has sub controls
		foreach ( $this->getSubControls( $args ) as $control ) {
			$this->addControl( $wp_customize, $control, false );
		}

		$this->app->do_action( 'after_register_' . $args['id'] );
	}

	/**
	 * Change existing customize object
	 *
	 * @param \WP_Customize_Manager $wp_customize
	 * @param $type
	 * @param $id
	 * @param $property
	 * @param $value
	 */
	public function changeObject( $wp_customize, $type, $id, $property, $value ) {
		$accepted_types = array( 'setting', 'control', 'section', 'panel' );
		if ( ! in_array( $type, $accepted_types, true ) ) {
			return;
		}
		$object = call_user_func_array( array( $wp_customize, 'get_' . $type ), array( $id ) );

		if ( empty( $object ) ) {
			return;
		}

		$object->$property = $value;
	}

	/**
	 * Enqueue localize script
	 *
	 * @param $handle
	 */
	protected function enqueueLocalize( $handle ) {
		$localize               = [
			'breakpoints' => [
				'desktop' => Css::desktop(),
				'tablet'  => Css::tablet(),
				'mobile'  => Css::mobile(),
			],
		];
		$localize['customizer'] = [
			'gradientPicker' => [
				'swatches' => [
					[
						'name'     => 'Vivid cyan blue to vivid purple',
						'gradient' => 'linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%)',
						'slug'     => 'vivid-cyan-blue-to-vivid-purple',
					],
					[
						'name'     => 'Light green cyan to vivid green cyan',
						'gradient' => 'linear-gradient(135deg,rgb(122,220,180) 0%,rgb(0,208,130) 100%)',
						'slug'     => 'light-green-cyan-to-vivid-green-cyan',
					],
					[
						'name'     => 'Luminous vivid amber to luminous vivid orange',
						'gradient' => 'linear-gradient(135deg,rgba(252,185,0,1) 0%,rgba(255,105,0,1) 100%)',
						'slug'     => 'luminous-vivid-amber-to-luminous-vivid-orange',
					],
					[
						'name'     => 'Luminous vivid orange to vivid red',
						'gradient' => 'linear-gradient(135deg,rgba(255,105,0,1) 0%,rgb(207,46,46) 100%)',
						'slug'     => 'luminous-vivid-orange-to-vivid-red',
					],
					[
						'name'     => 'Cool to warm spectrum',
						'gradient' => 'linear-gradient(135deg,rgb(74,234,220) 0%,rgb(151,120,209) 20%,rgb(207,42,186) 40%,rgb(238,44,130) 60%,rgb(251,105,98) 80%,rgb(254,248,76) 100%)',
						'slug'     => 'cool-to-warm-spectrum',
					],
					[
						'name'     => 'Blush light purple',
						'gradient' => 'linear-gradient(135deg,rgb(255,206,236) 0%,rgb(152,150,240) 100%)',
						'slug'     => 'blush-light-purple',
					],
					[
						'name'     => 'Blush bordeaux',
						'gradient' => 'linear-gradient(135deg,rgb(254,205,165) 0%,rgb(254,45,45) 50%,rgb(107,0,62) 100%)',
						'slug'     => 'blush-bordeaux',
					],
					[
						'name'     => 'Luminous dusk',
						'gradient' => 'linear-gradient(135deg,rgb(255,203,112) 0%,rgb(199,81,192) 50%,rgb(65,88,208) 100%)',
						'slug'     => 'luminous-dusk',
					],
					[
						'name'     => 'Pale ocean',
						'gradient' => 'linear-gradient(135deg,rgb(255,245,203) 0%,rgb(182,227,212) 50%,rgb(51,167,181) 100%)',
						'slug'     => 'pale-ocean',
					],
					[
						'name'     => 'Electric grass',
						'gradient' => 'linear-gradient(135deg,rgb(202,248,128) 0%,rgb(113,206,126) 100%)',
						'slug'     => 'electric-grass',
					],
					[
						'name'     => 'Midnight',
						'gradient' => 'linear-gradient(135deg,rgb(2,3,129) 0%,rgb(40,116,252) 100%)',
						'slug'     => 'midnight',
					],
					[
						'name'     => 'Juicy Peach',
						'gradient' => 'linear-gradient(to right, #ffecd2 0%, #fcb69f 100%)',
						'slug'     => 'juicy-peach',
					],
					[
						'name'     => 'Young Passion',
						'gradient' => 'linear-gradient(to right, #ff8177 0%, #ff867a 0%, #ff8c7f 21%, #f99185 52%, #cf556c 78%, #b12a5b 100%)',
						'slug'     => 'young-passion',
					],
					[
						'name'     => 'True Sunset',
						'gradient' => 'linear-gradient(to right, #fa709a 0%, #fee140 100%)',
						'slug'     => 'true-sunset',
					],
					[
						'name'     => 'Morpheus Den',
						'gradient' => 'linear-gradient(to top, #30cfd0 0%, #330867 100%)',
						'slug'     => 'morpheus-den',
					],
					[
						'name'     => 'Plum Plate',
						'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
						'slug'     => 'plum-plate',
					],
					[
						'name'     => 'Aqua Splash',
						'gradient' => 'linear-gradient(15deg, #13547a 0%, #80d0c7 100%)',
						'slug'     => 'aqua-splash',
					],
					[
						'name'     => 'Love Kiss',
						'gradient' => 'linear-gradient(to top, #ff0844 0%, #ffb199 100%)',
						'slug'     => 'love-kiss',
					],
					[
						'name'     => 'New Retrowave',
						'gradient' => 'linear-gradient(to top, #3b41c5 0%, #a981bb 49%, #ffc8a9 100%)',
						'slug'     => 'new-retrowave',
					],
					[
						'name'     => 'Plum Bath',
						'gradient' => 'linear-gradient(to top, #cc208e 0%, #6713d2 100%)',
						'slug'     => 'plum-bath',
					],
					[
						'name'     => 'High Flight',
						'gradient' => 'linear-gradient(to right, #0acffe 0%, #495aff 100%)',
						'slug'     => 'high-flight',
					],
					[
						'name'     => 'Teen Party',
						'gradient' => 'linear-gradient(-225deg, #FF057C 0%, #8D0B93 50%, #321575 100%)',
						'slug'     => 'teen-party',
					],
					[
						'name'     => 'Fabled Sunset',
						'gradient' => 'linear-gradient(-225deg, #231557 0%, #44107A 29%, #FF1361 67%, #FFF800 100%)',
						'slug'     => 'fabled-sunset',
					],
					[
						'name'     => 'Arielle Smile',
						'gradient' => 'radial-gradient(circle 248px at center, #16d9e3 0%, #30c7ec 47%, #46aef7 100%)',
						'slug'     => 'arielle-smile',
					],
					[
						'name'     => 'Itmeo Branding',
						'gradient' => 'linear-gradient(180deg, #2af598 0%, #009efd 100%)',
						'slug'     => 'itmeo-branding',
					],
					[
						'name'     => 'Deep Blue',
						'gradient' => 'linear-gradient(to right, #6a11cb 0%, #2575fc 100%)',
						'slug'     => 'deep-blue',
					],
					[
						'name'     => 'Strong Bliss',
						'gradient' => 'linear-gradient(to right, #f78ca0 0%, #f9748f 19%, #fd868c 60%, #fe9a8b 100%)',
						'slug'     => 'strong-bliss',
					],
					[
						'name'     => 'Sweet Period',
						'gradient' => 'linear-gradient(to top, #3f51b1 0%, #5a55ae 13%, #7b5fac 25%, #8f6aae 38%, #a86aa4 50%, #cc6b8e 62%, #f18271 75%, #f3a469 87%, #f7c978 100%)',
						'slug'     => 'sweet-period',
					],
					[
						'name'     => 'Purple Division',
						'gradient' => 'linear-gradient(to top, #7028e4 0%, #e5b2ca 100%)',
						'slug'     => 'purple-division',
					],
					[
						'name'     => 'Cold Evening',
						'gradient' => 'linear-gradient(to top, #0c3483 0%, #a2b6df 100%, #6b8cce 100%, #a2b6df 100%)',
						'slug'     => 'cold-evening',
					],
					[
						'name'     => 'Mountain Rock',
						'gradient' => 'linear-gradient(to right, #868f96 0%, #596164 100%)',
						'slug'     => 'mountain-rock',
					],
					[
						'name'     => 'Desert Hump',
						'gradient' => 'linear-gradient(to top, #c79081 0%, #dfa579 100%)',
						'slug'     => 'desert-hump',
					],
					[
						'name'     => 'Eternal Constance',
						'gradient' => 'linear-gradient(to top, #09203f 0%, #537895 100%)',
						'slug'     => 'ethernal-constance',
					],
					[
						'name'     => 'Happy Memories',
						'gradient' => 'linear-gradient(-60deg, #ff5858 0%, #f09819 100%)',
						'slug'     => 'happy-memories',
					],
					[
						'name'     => 'Grown Early',
						'gradient' => 'linear-gradient(to top, #0ba360 0%, #3cba92 100%)',
						'slug'     => 'grown-early',
					],
					[
						'name'     => 'Morning Salad',
						'gradient' => 'linear-gradient(-225deg, #B7F8DB 0%, #50A7C2 100%)',
						'slug'     => 'morning-salad',
					],
					[
						'name'     => 'Night Call',
						'gradient' => 'linear-gradient(-225deg, #AC32E4 0%, #7918F2 48%, #4801FF 100%)',
						'slug'     => 'night-call',
					],
					[
						'name'     => 'Mind Crawl',
						'gradient' => 'linear-gradient(-225deg, #473B7B 0%, #3584A7 51%, #30D2BE 100%)',
						'slug'     => 'mind-crawl',
					],
					[
						'name'     => 'Angel Care',
						'gradient' => 'linear-gradient(-225deg, #FFE29F 0%, #FFA99F 48%, #FF719A 100%)',
						'slug'     => 'angel-care',
					],
					[
						'name'     => 'Juicy Cake',
						'gradient' => 'linear-gradient(to top, #e14fad 0%, #f9d423 100%)',
						'slug'     => 'juicy-cake',
					],
					[
						'name'     => 'Rich Metal',
						'gradient' => 'linear-gradient(to right, #d7d2cc 0%, #304352 100%)',
						'slug'     => 'rich-metal',
					],
					[
						'name'     => 'Mole Hall',
						'gradient' => 'linear-gradient(-20deg, #616161 0%, #9bc5c3 100%)',
						'slug'     => 'mole-hall',
					],
					[
						'name'     => 'Cloudy Knoxville',
						'gradient' => 'linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%)',
						'slug'     => 'cloudy-knoxville',
					],
					[
						'name'     => 'Very light gray to cyan bluish gray',
						'gradient' => 'linear-gradient(135deg,rgb(238,238,238) 0%,rgb(169,184,195) 100%)',
						'slug'     => 'very-light-gray-to-cyan-bluish-gray',
					],
					[
						'name'     => 'Soft Grass',
						'gradient' => 'linear-gradient(to top, #c1dfc4 0%, #deecdd 100%)',
						'slug'     => 'soft-grass',
					],
					[
						'name'     => 'Saint Petersburg',
						'gradient' => 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
						'slug'     => 'saint-petersburg',
					],
					[
						'name'     => 'Everlasting Sky',
						'gradient' => 'linear-gradient(135deg, #fdfcfb 0%, #e2d1c3 100%)',
						'slug'     => 'everlasting-sky',
					],
					[
						'name'     => 'Kind Steel',
						'gradient' => 'linear-gradient(-20deg, #e9defa 0%, #fbfcdb 100%)',
						'slug'     => 'kind-steel',
					],
					[
						'name'     => 'Over Sun',
						'gradient' => 'linear-gradient(60deg, #abecd6 0%, #fbed96 100%)',
						'slug'     => 'over-sun',
					],
					[
						'name'     => 'Premium White',
						'gradient' => 'linear-gradient(to top, #d5d4d0 0%, #d5d4d0 1%, #eeeeec 31%, #efeeec 75%, #e9e9e7 100%)',
						'slug'     => 'premium-white',
					],
					[
						'name'     => 'Clean Mirror',
						'gradient' => 'linear-gradient(45deg, #93a5cf 0%, #e4efe9 100%)',
						'slug'     => 'clean-mirror',
					],
					[
						'name'     => 'Wild Apple',
						'gradient' => 'linear-gradient(to top, #d299c2 0%, #fef9d7 100%)',
						'slug'     => 'wild-apple',
					],
					[
						'name'     => 'Snow Again',
						'gradient' => 'linear-gradient(to top, #e6e9f0 0%, #eef1f5 100%)',
						'slug'     => 'snow-again',
					],
					[
						'name'     => 'Confident Cloud',
						'gradient' => 'linear-gradient(to top, #dad4ec 0%, #dad4ec 1%, #f3e7e9 100%)',
						'slug'     => 'confident-cloud',
					],
					[
						'name'     => 'Glass Water',
						'gradient' => 'linear-gradient(to top, #dfe9f3 0%, white 100%)',
						'slug'     => 'glass-water',
					],
					[
						'name'     => 'Perfect White',
						'gradient' => 'linear-gradient(-225deg, #E3FDF5 0%, #FFE6FA 100%)',
						'slug'     => 'perfect-white',
					],
				],
			],
			'colorPicker'    => [
				'swatches' => [],
			],
			'settings'       => array(
				'system_fonts' => Fonts::system_fonts(),
				'custom_fonts' => Fonts::custom_fonts(),
				'google_fonts' => Fonts::google_fonts(),
			),
			'iconsLibrary'   => IconsManager::allLibraries(),
		];

		wp_localize_script(
			$handle,
			'Lotta',
			apply_filters( 'lotta_filter_customizer_js_localize', $localize )
		);
	}

	/**
	 * Generate async scripts
	 *
	 * @return string
	 */
	protected function generateAsyncScripts() {
		$output = '(function ($) {';

		foreach ( $this->asyncScripts as $id => $script ) {
			$output .= "wp.customize('$id', function (setting) {";
			$output .= "setting.bind(function (value) {";
			$output .= $script;
			$output .= "});";
			$output .= "});";
		}

		return $output . '}(jQuery));';
	}
}
