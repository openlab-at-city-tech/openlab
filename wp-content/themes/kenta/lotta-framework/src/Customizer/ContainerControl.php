<?php

namespace LottaFramework\Customizer;

abstract class ContainerControl extends Control {

	use Traits\ContainerControl;

	/**
	 * @param $id
	 */
	public function __construct( $id ) {
		parent::__construct( $id );

		$this->hideLabel();
		$this->setDefaultValue( '__LOTTA_CONTAINER_CONTROL__' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSanitize() {
		return '__return_false';
	}

	/**
	 * @param $options
	 * @param $settings
	 *
	 * @return array
	 */
	protected function sanitizeSettings( $options, $settings ) {
		$result = [];
		foreach ( $settings as $id => $value ) {
			$option = $options->getSettingArgs( $id );
			if ( empty( $option ) ) {
				continue;
			}
			if ( isset( $option['sanitize_callback'] ) ) {
				$value = call_user_func( $option['sanitize_callback'], $value, $option );
			}
			$result[ $id ] = $value;
		}

		return $result;
	}
}
