<?php


namespace ColibriWP\Theme\Core;

use ColibriWP\Theme\Components\CSSOutput;
use ColibriWP\Theme\Theme;
use function get_theme_mod;

abstract class ComponentBase implements ConfigurableInterface, PartialComponentInterface {

	const PEN_ON_LEFT  = 'left';
	const PEN_ON_RIGHT = 'right';

	protected static $selective_refresh_container_inclusive = true;

	public static function selectiveRefreshKey() {
		return Utils::slugify( static::class );
	}

	public static function options() {

		if ( apply_filters( 'kubio_is_enabled', false ) ) {
			return array();
		}

		$options = array_replace(
			array(
				'settings' => array(),
				'sections' => array(),
				'panels'   => array(),
			),
			(array) static::getOptions()
		);

		foreach ( array_keys( $options['settings'] ) as $id ) {

			if ( ! array_key_exists( 'css_output', $options['settings'][ $id ] ) ) {
				continue;
			}

			if ( is_array( $options['settings'][ $id ]['css_output'] ) ) {

				foreach ( $options['settings'][ $id ]['css_output'] as $index => $css_output ) {
					$options['settings'][ $id ]['css_output'][ $index ] = CSSOutput::normalizeOutput( $css_output );
				}
			}
		}

		$__class__ = static::class;

		Theme::getInstance()->getCustomizer()->isCustomizer(
			function () use ( &$options, $__class__ ) {
				foreach ( array_keys( $options['settings'] ) as $id ) {

					$has_control = isset( $options['settings'][ $id ]['control'] ) ? true : false;
					$has_control = $has_control && is_array( $options['settings'][ $id ]['control'] );

					if ( $has_control ) {

						if ( array_key_exists( 'selective_refresh', $options['settings'][ $id ]['control'] ) ) {
							$selective_refresh = $options['settings'][ $id ]['control']['selective_refresh'];

							if ( $selective_refresh === false ) {
								continue;
							}

							unset( $options['settings'][ $id ]['control']['selective_refresh'] );

							if ( array_key_exists( 'selector', $selective_refresh ) ) {
								$options['settings'][ $id ]['control']['colibri_selective_refresh_selector']
									= $selective_refresh['selector'];
							}

							if ( array_key_exists( 'function', $selective_refresh ) ) {
								$options['settings'][ $id ]['control']['colibri_selective_refresh_function']
									= $selective_refresh['function'];
							}
						} else {
							$options['settings'][ $id ]['control']['colibri_selective_refresh_selector']
								= static::selectiveRefreshSelector();

						}

						$options['settings'][ $id ]['control']['colibri_selective_refresh_class']       = $__class__;
						$options['settings'][ $id ]['control']['selective_refresh_container_inclusive'] = static::$selective_refresh_container_inclusive;
					}
				}
			}
		);

		return $options;
	}

	abstract protected static function getOptions();

	public static function selectiveRefreshSelector() {
		return false;
	}

	public function mod_e_esc_attr( $name ) {
		echo esc_attr( $this->mod( $name ) );
	}

	/**
	 * @param      $name
	 * @param null $fallback
	 *
	 * @return mixed
	 */
	public function mod( $name, $fallback = null ) {
		return static::mabyDeserializeModValue( get_theme_mod( $name, static::settingDefault( $name, $fallback ) ) );
	}

	public static function mabyDeserializeModValue( $value ) {

		if ( is_string( $value ) ) {

			if ( empty( trim( $value ) ) ) {
				return $value;
			}

			$new_value = json_decode( urldecode( $value ), true );

			if ( json_last_error() === JSON_ERROR_NONE ) {
				$value = $new_value;
			}
		}

		return $value;
	}

	/**
	 * @param      $name
	 * @param null $fallback
	 *
	 * @return null
	 */
	public static function settingDefault( $name, $fallback = null ) {
		$options = (array) static::getOptions();

		$default = $fallback;

		if ( isset( $options['settings'] ) && isset( $options['settings'][ $name ] ) ) {
			if ( array_key_exists( 'default', $options['settings'][ $name ] ) ) {
				$default = $options['settings'][ $name ]['default'];
			}
		}

		return $default;
	}

	/**
	 * @return array
	 */
	public function mods() {
		$result   = array();
		$settings = array_key_exists( 'settings', static::getOptions() ) ? static::getOptions()['settings'] : array();

		foreach ( array( $settings ) as $key => $value ) {
			$result[ $key ] = $this->mod( $key );
		}

		return $result;
	}

	public function render( $parameters = array() ) {

		$that = $this;

		Theme::getInstance()->getCustomizer()->inPreview(
			function () use ( $that ) {
				$that->addControlsFilter();
				$that->whenCustomizerPreview();
				$that->printPenPosition();
			}
		);

		$this->renderContent( $parameters );
	}

	/**
	 * @return array();
	 */
	/** @noinspection PhpAbstractStaticMethodInspection */
	private function addControlsFilter() {

		$options = (array) static::getOptions();

		if ( isset( $options['settings'] ) ) {
			$options = array_keys( $options['settings'] );

		} else {
			$options = array();
		}

		foreach ( $options as $option ) {

			Hooks::prefixed_add_filter(
				"control_{$option}_rendered",
				function ( $value ) {
					return true;
				},
				0,
				1
			);
		}

	}

	public function whenCustomizerPreview() {

	}

	private function printPenPosition() {
		if ( $this->getPenPosition() === static::PEN_ON_RIGHT ) {
			$class = static::class;
			add_action(
				'wp_footer',
				function () use ( $class ) {
					$selector = call_user_func( array( $class, 'selectiveRefreshSelector' ) );
					if ( $selector ) {
						?>
						<style>
							@media (min-width: 768px) {
								<?php echo $selector; ?> > .customize-partial-edit-shortcut {
									right: -30px;
								}
							}
						</style>
						<?php
					}
				}
			);
		}
	}

	public function getPenPosition() {
		return static::PEN_ON_LEFT;
	}

	abstract public function renderContent( $parameters );

	protected function addFrontendJSData( $key, $value ) {
		Hooks::add_filter(
			'frontend_js_data',
			function ( $current_data ) use ( $key, $value ) {
				$current_data[ $key ] = $value;

				return $current_data;
			}
		);

	}

}
