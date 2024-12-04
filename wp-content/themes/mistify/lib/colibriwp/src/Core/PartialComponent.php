<?php


namespace ColibriWP\Theme\Core;

use ColibriWP\Theme\Theme;
use function get_theme_mod;

abstract class PartialComponent implements PartialComponentInterface {


	public function mod( $name ) {

		return ComponentBase::mabyDeserializeModValue( get_theme_mod( $name, $this->settingDefault( $name ) ) );
	}

	public function settingDefault( $name ) {
		$options = (array) $this->getOptions();
		$default = null;

		if ( isset( $options['settings'] ) && isset( $options['settings'][ $name ] ) ) {
			if ( array_key_exists( 'default', $options['settings'][ $name ] ) ) {
				$default = $options['settings'][ $name ]['default'];
			}
		}

		return $default;
	}

	abstract public function getOptions();

	public function render() {
		$that = $this;

		Theme::getInstance()->getCustomizer()->inPreview(
			function () use ( $that ) {
				$that->addControlsFilter();
				$that->whenCustomizerPreview();
			}
		);

		$this->renderContent();
	}

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
				}
			);
		}

	}

	public function whenCustomizerPreview() {

	}

	abstract public function renderContent( $parameters = array() );

	protected function addFrontendJSData( $key, $value, $in_preview = false ) {

		if ( $in_preview ) {
			$self = $this;
			Theme::getInstance()->getCustomizer()->inPreview(
				function () use ( $self, $key, $value ) {
					$self->addJSData( $key, $value );
				}
			);
		} else {
			$this->addJSData( $key, $value );
		}

	}

	private function addJSData( $key, $value ) {
		Hooks::add_filter(
			'frontend_js_data',
			function ( $current_data ) use ( $key, $value ) {
				$current_data[ $key ] = $value;

				return $current_data;
			}
		);
	}


}
