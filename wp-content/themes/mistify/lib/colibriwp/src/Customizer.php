<?php


namespace ColibriWP\Theme;

use ColibriWP\Theme\Core\ConfigurableInterface;
use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Core\Tree;
use ColibriWP\Theme\Core\Utils;
use ColibriWP\Theme\Customizer\ControlFactory;
use ColibriWP\Theme\Customizer\Controls\ColibriControl;
use ColibriWP\Theme\Customizer\CustomizerApi;
use ColibriWP\Theme\Customizer\PanelFactory;
use ColibriWP\Theme\Customizer\SectionFactory;
use ColibriWP\Theme\Core\EnableKubioInCustomizerPanel;
use WP_Customize_Manager;
use function is_customize_preview;

class Customizer {

	const TYPE_CONTROL = 'control';
	const TYPE_SECTION = 'section';
	const TYPE_PANEL   = 'panel';

	private $theme = null;
	private $options;
	private $sections = array();
	private $panels   = array();
	private $settings = array();


	public function __construct( Theme $theme ) {

		new CustomizerApi();
		$this->theme   = $theme;
		$this->options = new Tree();

	}

	public static function sanitize( $value, $data = array() ) {

		if ( is_bool( $value ) ) {
			return $value;
		}

		$control_type = Utils::pathGet( $data, 'control.type', false );

		if ( $control_type ) {
			$constructor = ControlFactory::getConstructor( $control_type );

			if ( $constructor && method_exists( $constructor, 'sanitize' ) ) {
				return call_user_func(
					array( $constructor, 'sanitize' ),
					$value,
					Utils::pathGet( $data, 'control', array() ),
					Utils::pathGet( $data, 'default', '' )
				);
			}
		}

		return (string) $value;
	}

	public function boot() {

		if ( Hooks::prefixed_apply_filters( 'customizer_skip_boot', false ) ) {
			return;
		}

		add_action( 'customize_register', array( $this, 'prepareOptions' ), 0, 0 );
		add_action( 'customize_register', array( $this, 'prepareTypes' ), 0, 1 );

		// register customizer structure
		add_action( 'customize_register', array( $this, 'add_enable_kubio_plugin' ), 1, 1 );
		add_action( 'customize_register', array( $this, 'registerPanels' ), 2, 1 );
		add_action( 'customize_register', array( $this, 'registerSections' ), 3, 1 );

		// register customizer components
		add_action( 'customize_register', array( $this, 'registerSettings' ), 4, 1 );
		add_action( 'customize_register', array( $this, 'registerControls' ), 5, 1 );

		// additional elements
		add_action( 'customize_register', array( $this, 'registerPartialRefresh' ), 6, 1 );

		$self = $this;
		$this->inPreview(
			function () use ( $self ) {
				add_action( 'wp_print_footer_scripts', array( $self, 'printPreviewOptions' ), PHP_INT_MAX );
			}
		);

		// rearrange customizer components
		add_action( 'customize_register', array( $this, 'rearrangeComponents' ), PHP_INT_MAX, 1 );

		// add customizer js / css
		add_action( 'customize_controls_print_scripts', array( $this, 'registerAssets' ), PHP_INT_MAX, 1 );

		$this->onPreviewInit( array( $this, 'previewInit' ) );

	}

	public function inPreview( $callback ) {
		if ( is_customize_preview() && is_callable( $callback ) ) {
			call_user_func( $callback );
		}
	}

	public function onPreviewInit( $callback, $priorty = 10 ) {

		add_action( 'customize_preview_init', $callback, $priorty );
	}

	public function printPreviewOptions() {
		?>
		<script data-name="colibri-preview-options">
			var colibri_CSS_OUTPUT_CONTROLS = <?php echo wp_json_encode( ControlFactory::getCssOutputControls() ); ?>;
			var colibri_JS_OUTPUT_CONTROLS = <?php echo wp_json_encode( ControlFactory::getJsOutputControls() ); ?>;
			var colibri_CONTROLS_ACTIVE_RULES = <?php echo wp_json_encode( ControlFactory::getActiveRules() ); ?>;
			var colibri_ADDITIONAL_JS_DATA =
				<?php
				echo wp_json_encode(
					(object) Hooks::prefixed_apply_filters(
						'customizer_additional_js_data',
						array()
					)
				);
				?>
			;
		</script>
		<?php
	}

	public function getSettingQuickLink( $value ) {
		return add_query_arg( 'colibri_autofocus', $value, admin_url( '/customize.php' ) );
	}

	public function prepareOptions() {

		new HeaderPresets();

		$components = $this->theme->getRepository()->getAllDefinitions();
		$options    = array(
			'settings' => array(),
			'sections' => array(),
			'panels'   => array(),
		);

		foreach ( $components as $key => $component ) {
			$interfaces = class_implements( $component );

			if ( array_key_exists( ConfigurableInterface::class, $interfaces ) ) {

				/** @var ConfigurableInterface $component */
				$opts = (array) $component::options();

				foreach ( $options as $opt_key => $value ) {

					if ( array_key_exists( $opt_key, $opts ) && is_array( $opts[ $opt_key ] ) ) {

						$options[ $opt_key ] = array_merge( $options[ $opt_key ], $opts[ $opt_key ] );

					}
				}
			}
		}

		$options = Hooks::prefixed_apply_filters( 'customizer_options', $options );

		// set initial section > tabs to empty = true
		$tabs     = array(
			'content' => true,
			'style'   => true,
			'layout'  => true,
		);
		$sections = array_flip( array_keys( $options['sections'] ) );
		array_walk(
			$sections,
			function ( &$value, $key ) use ( $tabs ) {
				$value = array( 'tabs' => $tabs );
			}
		);

		// set section > tabs that have controls empty = false
		foreach ( $options['settings'] as $setting => $value ) {
			$section                              = $value['control']['section'];
			$tab                                  = Utils::pathGet( $value, 'control.colibri_tab', 'content' );
			$sections[ $section ]['tabs'][ $tab ] = false;
		}

		foreach ( $sections as $section => $values ) {
			foreach ( $values['tabs'] as $tab => $tab_empty ) {
				if ( $tab_empty ) {
					// var_dump($section);
					$key                         = "{$section}-{$tab}-plugin-message";
					$options['settings'][ $key ] = array(
						'control' => array(
							'type'        => 'plugin-message',
							'section'     => $section,
							'colibri_tab' => $tab,
						),
					);
				}
			}
		}

		if ( isset( $_REQUEST['colibriwp_export_default_options'] ) && is_admin() ) {
			$defaults = array();

			foreach ( $options['settings'] as $key => $value ) {

				if ( str_ends_with( $key, '.pen' ) ) {
					continue;
				}

				$value            = Utils::pathGet( $value, 'default', '' );
				$defaults[ $key ] = $this->urlToPlaceholder( $value );
			}

			wp_send_json_success( $defaults );
		}

		$this->options->setData( $options );
	}

	/**
	 * @param WP_Customize_Manager $wp_customize
	 */
	public function prepareTypes( $wp_customize ) {
		$types = Hooks::prefixed_apply_filters( 'customizer_types', array() );
		foreach ( $types as $class => $type ) {
			switch ( $type ) {
				case self::TYPE_CONTROL:
					$wp_customize->register_control_type( $class );
					break;

				case self::TYPE_SECTION:
					$wp_customize->register_section_type( $class );
					break;

				case self::TYPE_PANEL:
					$wp_customize->register_panel_type( $class );
					break;
			}
		}

	}

	public function add_enable_kubio_plugin( $wp_customize ) {
		$wp_customize->add_panel(
			new EnableKubioInCustomizerPanel(
				$wp_customize,
				'enable-kubio-section',
				array(
					'capability' => 'manage_options',
					'priority'   => 0,
					'type'       => 'colibri-panel',
				)
			)
		);
	}

	public function registerPanels() {
		$this->panels = new Tree( $this->options->findAt( 'panels' ) );

		$this->panels->walkFirstLevel(
			function ( $id, $data ) {
				PanelFactory::make( $id, $data );
			}
		);
	}

	public function registerSections() {
		$this->sections = new Tree( $this->options->findAt( 'sections' ) );

		$this->sections->walkFirstLevel(
			function ( $id, $data ) {
				SectionFactory::make( $id, $data );
			}
		);
	}

	/**
	 * @param WP_Customize_Manager $wp_customize
	 */
	public function registerSettings( $wp_customize ) {
		$this->settings    = new Tree( $this->options->findAt( 'settings' ) );
		$sanitize_callback = array( __CLASS__, 'sanitize' );

		$this->settings->walkFirstLevel(
			function ( $id, $data ) use ( $wp_customize, $sanitize_callback ) {

				$data = array_merge(
					array(
						'transport' => 'colibri_selective_refresh',
						'default'   => '',
					),
					$data
				);

				if ( isset( $data['setting'] ) ) {
					$id = $data['setting'];
				}

				if ( ! ( isset( $data['settingless'] ) && $data['settingless'] ) ) {

					if ( ! $wp_customize->get_setting( $id ) ) {
						$wp_customize->add_setting(
							$id,
							array(
								'transport'         => $data['transport'],
								'default'           => $data['default'],
								'sanitize_callback' => function ( $value ) use ( $sanitize_callback, $data ) {
									return call_user_func( $sanitize_callback, $value, $data );
								},
							)
						);
					}
				}

				if ( isset( $data['control'] ) ) {

					$control = array_merge(
						array(
							'default'                 => $data['default'],
							'transport'               => $data['transport'],
							'apply_selective_refresh' => false,

						),
						$data['control']
					);

					if ( $control['transport'] === 'selective_refresh' ) {
						$control['apply_selective_refresh'] = true;
					}

					if ( array_key_exists( 'css_output', $data ) ) {
						$control['transport']  = 'css_output';
						$control['css_output'] = $data['css_output'];
					}
					if ( array_key_exists( 'js_output', $data ) ) {
						$control['transport'] = 'js_output';
						$control['js_output'] = $data['js_output'];
					}

					if ( array_key_exists( 'js_output', $data ) && array_key_exists( 'css_output', $data ) ) {
						$control['transport'] = 'js_and_css_output';
					}

					if ( array_key_exists( 'active_rules', $data ) ) {
						$control['active_rules'] = $data['active_rules'];
					}

					if ( array_key_exists( 'active_callback', $data ) ) {
						$control['active_callback'] = $data['active_callback'];
					}

					$control['settingless'] = ( isset( $data['settingless'] ) && $data['settingless'] );

					ControlFactory::make( $id, $control );
				}

			}
		);

	}

	/**
	 * @param WP_Customize_Manager $wp_customize
	 */
	public function registerControls( $wp_customize ) {

	}

	/**
	 * @param WP_Customize_Manager $wp_customize
	 */
	public function registerPartialRefresh( $wp_customize ) {
		$partials = ControlFactory::getPartialRefreshes();

		Hooks::prefixed_add_filter(
			'customizer_additional_js_data',
			function ( $value ) use ( $partials ) {
				$value['selective_refresh_settings'] = array();

				foreach ( $partials as $partial ) {
					$value['selective_refresh_settings'] = array_merge(
						$value['selective_refresh_settings'],
						$partial['settings']
					);
				}

				return $value;
			}
		);

		foreach ( $partials as $key => $args ) {
			$wp_customize->selective_refresh->add_partial( $key, $args );
		}
	}

	/**
	 * @param WP_Customize_Manager $wp_customize
	 */
	public function rearrangeComponents( $wp_customize ) {
		Hooks::prefixed_do_action( 'rearrange_customizer_components', $wp_customize );
	}

	public function registerAssets() {
		$base_url          = $this->theme->getAssetsManager()->getBaseURL();
		$customizer_handle = Theme::prefix( 'customizer' );

		wp_register_script(
			$customizer_handle,
			$base_url . '/customizer/customizer.js',
			array( 'jquery' ),
			$this->theme->getVersion(),
			true
		);

		wp_localize_script(
			$customizer_handle,
			'colibri_Customizer_Data',
			Hooks::prefixed_apply_filters(
				'customizer_js_data',
				array(
					'theme_prefix'                    => Theme::prefix( '', false ),
					'translations'                    => Translations::all(),
					'section_default_tab'             => ColibriControl::DEFAULT_COLIBRI_TAB,
					'style_tab'                       => ColibriControl::STYLE_COLIBRI_TAB,
					'kubio_disable_big_notice_nonce'  => wp_create_nonce( 'kubio_disable_big_notice_nonce' ),
					'kubio_front_set_predesign_nonce' => wp_create_nonce( 'kubio_front_set_predesign_nonce' ),
					'kubio_onboarding_disable_notice_nonce' => wp_create_nonce( 'kubio_onboarding_disable_notice_nonce' ),
					'colibri_autofocus'               => Utils::pathGet( $_REQUEST, 'colibri_autofocus' ),
					'colibri_autofocus_aliases'       => (object) Hooks::prefixed_apply_filters(
						'customizer_autofocus_aliases',
						array()
					),
					'getStartedData'                  => array(
						'plugin_installed_and_active' => Translations::escHtml( 'plugin_installed_and_active' ),
						'activate'                    => Translations::escHtml( 'activate' ),
						'activating'                  => Translations::get( 'activating', 'Kubio Page Builder' ),
						'install_recommended'         => isset( $_GET['install_recommended'] ) ? $_GET['install_recommended'] : '',
						'theme_prefix'                => Theme::prefix( '', false ),
					),
					'builderStatusData'               => array(
						'status'       => mistify_theme()->getPluginsManager()->getPluginState( mistify_get_builder_plugin_slug() ),
						'install_url'  => mistify_theme()->getPluginsManager()->getInstallLink( mistify_get_builder_plugin_slug() ),
						'activate_url' => mistify_theme()->getPluginsManager()->getActivationLink( mistify_get_builder_plugin_slug() ),
						'slug'         => mistify_get_builder_plugin_slug(),
						'messages'     => array(
							'installing' => Translations::get( 'installing', 'Kubio Page Builder' ),
							'activating' => Translations::get( 'activating', 'Kubio Page Builder' ),
							'preparing'  => Translations::get( 'preparing_front_page_installation' ),
						),
					),
				)
			)
		);

		wp_register_style(
			$customizer_handle,
			$base_url . '/customizer/customizer.css',
			array( 'customize-controls' ),
			$this->theme->getVersion()
		);

		wp_enqueue_style( $customizer_handle );
		wp_enqueue_script( $customizer_handle );
	}

	public function isInPreview() {
		return is_customize_preview();
	}

	public function isCustomizer( $callback ) {
		if ( is_customize_preview() && is_callable( $callback ) ) {
			call_user_func( $callback );
		}
	}

	public function previewInit() {

		$base_url = $this->theme->getAssetsManager()->getBaseURL();

		$customizer_prevew_handle = Theme::prefix( 'customizer_preview' );

		wp_enqueue_style(
			$customizer_prevew_handle,
			$base_url . '/customizer/preview.css',
			Theme::getInstance()->getVersion()
		);

		wp_enqueue_script(
			$customizer_prevew_handle,
			$base_url . '/customizer/preview.js',
			array(
				'customize-preview',
				'customize-selective-refresh',
			),
			Theme::getInstance()->getVersion(),
			true
		);

		AssetsManager::addInlineScriptCallback(
			$customizer_prevew_handle,
			function () {
				?>
				<script type="text/javascript">
					(function () {
						function ready(callback) {
							if (document.readyState !== 'loading') {
								callback();
							} else {
								if (document.addEventListener) {
									document.addEventListener('DOMContentLoaded', callback);

								} else {
									document.attachEvent('onreadystatechange', function () {
										if (document.readyState === 'complete') callback();
									});
								}
							}
						}

						ready(function () {
							setTimeout(function () {
								parent.wp.customize.trigger('colibri_preview_ready');
							}, 500);
						})
					})();
				</script>
				<?php
			}
		);
	}

	/**
	 * @return array
	 */
	public function getSettings() {
		return $this->settings;
	}

	public function urlToPlaceholder( $array ) {

		if ( ! is_array( $array ) ) {
			if ( is_string( $array ) ) {
				return str_replace(
					get_template_directory_uri() . '/resources/',
					'%s/../',
					$array
				);
			}

			return $array;
		}

		foreach ( $array as $index => $value ) {
			$array[ $index ] = $this->urlToPlaceholder( $value );
		}

		return $array;
	}
}
