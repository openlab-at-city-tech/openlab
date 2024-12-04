<?php


namespace ColibriWP\Theme\Customizer;

use ColibriWP\Theme\ActiveCallback;
use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Core\PartialComponentInterface;
use ColibriWP\Theme\Core\Utils;
use ColibriWP\Theme\Customizer\Controls\AlignButtonGroupControl;
use ColibriWP\Theme\Customizer\Controls\ButtonControl;
use ColibriWP\Theme\Customizer\Controls\ButtonGroupControl;
use ColibriWP\Theme\Customizer\Controls\ColibriControl;
use ColibriWP\Theme\Customizer\Controls\ColorControl;
use ColibriWP\Theme\Customizer\Controls\ComposedControl;
use ColibriWP\Theme\Customizer\Controls\ControlsGroupControl;
use ColibriWP\Theme\Customizer\Controls\CroppedImageControl;
use ColibriWP\Theme\Customizer\Controls\GradientControl;
use ColibriWP\Theme\Customizer\Controls\IconControl;
use ColibriWP\Theme\Customizer\Controls\ImageControl;
use ColibriWP\Theme\Customizer\Controls\InputControl;
use ColibriWP\Theme\Customizer\Controls\LinkedSelectControl;
use ColibriWP\Theme\Customizer\Controls\MediaControl;
use ColibriWP\Theme\Customizer\Controls\PenControl;
use ColibriWP\Theme\Customizer\Controls\PluginMessageControl;
use ColibriWP\Theme\Customizer\Controls\RepeaterControl;
use ColibriWP\Theme\Customizer\Controls\SelectControl;
use ColibriWP\Theme\Customizer\Controls\SelectIconControl;
use ColibriWP\Theme\Customizer\Controls\SeparatorControl;
use ColibriWP\Theme\Customizer\Controls\SliderControl;
use ColibriWP\Theme\Customizer\Controls\SpacingControl;
use ColibriWP\Theme\Customizer\Controls\SwitchControl;
use ColibriWP\Theme\Customizer\Controls\UploadControl;
use ColibriWP\Theme\Customizer\Controls\VideoControl;

class ControlFactory {


	private static $controls
		= array(
			// defaults
			'image'              => ImageControl::class,
			'cropped_image'      => CroppedImageControl::class,
			'upload'             => UploadControl::class,
			'media'              => MediaControl::class,
			// colibri
			'color'              => ColorControl::class,
			'switch'             => SwitchControl::class,
			'select'             => SelectControl::class,
			'linked-select'      => LinkedSelectControl::class,
			'select-icon'        => SelectIconControl::class,
			'button-group'       => ButtonGroupControl::class,
			'align-button-group' => AlignButtonGroupControl::class,
			'button'             => ButtonControl::class,
			'gradient'           => GradientControl::class,
			'repeater'           => RepeaterControl::class,
			'composed'           => ComposedControl::class,
			'slider'             => SliderControl::class,
			'video'              => VideoControl::class,
			'input'              => InputControl::class,
			'plugin-message'     => PluginMessageControl::class,
			'separator'          => SeparatorControl::class,
			'group'              => ControlsGroupControl::class,
			'spacing'            => SpacingControl::class,
			'icon'               => IconControl::class,
			'pen'                => PenControl::class,
		);

	private static $decoration_controls = array( 'separator', 'plugin-message' );

	private static $wp_controls = array();


	private static $partial_refreshes   = array();
	private static $css_output_controls = array();
	private static $js_output_controls  = array();
	private static $active_rules        = array();


	private static $registered = false;

	/**
	 * @return array
	 */
	public static function getCssOutputControls() {
		return self::$css_output_controls;
	}

	public static function getJsOutputControls() {
		return self::$js_output_controls;
	}

	public static function make( $setting_id, $data ) {

		if ( array_key_exists( 'active_rules', $data ) ) {
			static::addActiveCallbackRules( $setting_id, $data );
		}

		$data = array_merge(
			array(
				'type'            => 'hidden',
				'transport'       => 'colibri_selective_refresh',
				'settings'        => $data['settingless'] ? array() : $setting_id,
				'active_callback' => function ( $control ) use ( $setting_id, $data ) {

					$rendered = Hooks::prefixed_apply_filters( "control_{$setting_id}_rendered", false, $control );
					$active   = false;
					if ( $rendered ) {
						$active = Hooks::prefixed_apply_filters( "control_{$setting_id}_active", true, $control );
					}

					return $active;

				},
			),
			$data
		);

		if ( $data['settingless'] ) {
			$data['capability'] = 'edit_theme_options';
		}

		if ( isset( $data['focus_alias'] ) ) {
			Hooks::prefixed_add_filter(
				'customizer_autofocus_aliases',
				function ( $aliases ) use ( $data, $setting_id ) {
					$aliases[ $data['focus_alias'] ] = $setting_id;

					return $aliases;
				}
			);
		}

		$class = static::getClassByType( $data['type'] );

		global $wp_customize;

		if ( ! in_array( $data['type'], static::$decoration_controls ) ) {
			if ( $data['transport'] === 'colibri_selective_refresh' || $data['apply_selective_refresh'] ) {
				$data = static::preparePartialRefreshControl( $setting_id, $data );
			}

			if ( $data['transport'] === 'css_output' ) {
				$data = static::prepareCSSOutputControl( $setting_id, $data );
			}

			if ( $data['transport'] === 'js_output' ) {
				$data = static::prepareJSOutputControl( $setting_id, $data );
			}

			if ( $data['transport'] === 'js_and_css_output' ) {
				$data = static::prepareCSSOutputControl( $setting_id, $data );
				$data = static::prepareJSOutputControl( $setting_id, $data );
			}
		}

		if ( $class !== ColibriControl::class ) {
			unset( $data['type'] );
		}

		$control = new $class( $wp_customize, Utils::slugify( $setting_id ), $data );
		$wp_customize->add_control( $control );

		return $control;
	}

	private static function addActiveCallbackRules( $setting_id, $data ) {
		$rules = $data['active_rules'];

		$active_callback = new ActiveCallback();
		$active_callback->setRules( $rules );

		static::$active_rules[ $setting_id ] = $rules;

		Hooks::prefixed_add_filter( "control_{$setting_id}_active", array( $active_callback, 'applyRules' ), 10 );
	}

	private static function getClassByType( $type ) {

		static::register();

		$controls = array_merge( static::$wp_controls, static::$controls );

		$class = isset( $controls [ $type ] ) ? $controls [ $type ] : ColibriControl::class;

		return $class;
	}

	private static function register() {
		if ( ! static::$registered ) {

			foreach ( static::$controls as $key => $control ) {
				global $wp_customize;
				$wp_customize->register_control_type( $control );

			}

			static::$registered = true;
		}
	}

	private static function preparePartialRefreshControl( $setting_id, $data ) {
		global $wp_customize;

		if ( ! isset( $wp_customize->selective_refresh ) ) {

			$wp_customize->get_setting( $setting_id )->transport = 'refresh';
			if ( array_key_exists( 'colibri_selective_refresh_selector', $data ) ) {
				unset( $data['colibri_selective_refresh_selector'] );
			}

			if ( array_key_exists( 'colibri_selective_refresh_class', $data ) ) {
				unset( $data['colibri_selective_refresh_class'] );
			}
		} else {
			$wp_customize->get_setting( $setting_id )->transport = 'postMessage';

			$selector = ( isset( $data['colibri_selective_refresh_selector'] ) ) ? $data['colibri_selective_refresh_selector'] : '';
			$id       = Utils::slugify( $selector );

			if ( isset( static::$partial_refreshes[ $id ] ) ) {
				static::$partial_refreshes[ $id ]['settings'][] = $setting_id;
			} else {
				static::$partial_refreshes[ $id ] = array(
					'selector'            => $selector,
					'settings'            => array( $setting_id ),
					'container_inclusive' => true, // $data['selective_refresh_container_inclusive'],
					'render_callback'     => function () use ( $data ) {
						$class = $data['colibri_selective_refresh_class'];

						/** @var PartialComponentInterface $item */
						$item = new $class();

						if ( isset( $data['colibri_selective_refresh_function'] ) ) {
							$fn = $data['colibri_selective_refresh_function'];
							if ( is_string( $fn ) && ! function_exists( $fn ) ) {
								$fn = array( $item, $fn );
							}
							call_user_func( $fn );
						} else {
							$item->renderContent();
						}

					},
				);
			}
		}

		return $data;
	}

	private static function prepareCSSOutputControl( $setting_id, $data ) {
		global $wp_customize;

		$setting = $wp_customize->get_setting( $setting_id );
		if ( ! $setting ) {
			return array();
		}

		$setting->transport = 'postMessage';

		static::$css_output_controls[ $setting_id ] = $data['css_output'];

		return $data;
	}

	private static function prepareJSOutputControl( $setting_id, $data ) {
		global $wp_customize;

		$setting = $wp_customize->get_setting( $setting_id );
		if ( ! $setting ) {
			return array();
		}

		$setting->transport                        = 'postMessage';
		static::$js_output_controls[ $setting_id ] = $data['js_output'];

		return $data;
	}

	/**
	 * @return array
	 */
	public static function getPartialRefreshes() {
		return self::$partial_refreshes;
	}

	/**
	 * @return array
	 */
	public static function getActiveRules() {
		return Hooks::prefixed_apply_filters( 'controls_active_rules', self::$active_rules );
	}

	public static function getConstructor( $type ) {
		return Utils::pathGet( static::$controls, $type, null );
	}
}
