<?php

namespace LottaFramework\Customizer\Controls;

use LottaFramework\Customizer\ContainerControl;

class Repeater extends ContainerControl {

	/**
	 * @param $id
	 */
	public function __construct( $id ) {
		parent::__construct( $id );

		$this->setDefaultValue( [] );
		$this->showLabel();
		$this->preventEmpty();
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return 'lotta-repeater';
	}

	/**
	 * @return string[]
	 */
	public function getSanitize() {
		return [ $this, 'sanitizeCallback' ];
	}

	/**
	 * Sanitize callback for repeater control
	 *
	 * @param $input
	 * @param $args
	 *
	 * @return array
	 */
	public function sanitizeCallback( $input, $args ) {

		$options = $args['options'] ?? [];
		$limit   = absint( $options['limit'] ?? 0 );
		$input   = is_array( $input ) ? $input : [];
		$result  = [];

		foreach ( $input as $item ) {
			$result[] = [
				'visible'  => (bool) ( $item['visible'] ?? false ),
				'settings' => $this->sanitizeSettings( $this, $item['settings'] ?? [] )
			];
		}

		if ( $limit > 0 ) {
			$result = array_slice( $result, 0, $limit );
		}

		return $result;
	}

	/**
	 * @param false $empty
	 *
	 * @return Repeater
	 */
	public function preventEmpty( $prevent = true ) {
		return $this->setOption( 'empty', ! $prevent );
	}

	/**
	 * @param $limit
	 * @param string $label
	 *
	 * @return Repeater
	 */
	public function setLimit( $limit, $label = '' ) {
		$this->setOption( 'limitLabel', $label );

		return $this->setOption( 'limit', $limit );
	}

	/**
	 * @param string $field
	 *
	 * @return Repeater
	 */
	public function setTitleField( $field ) {
		return $this->setOption( 'title_field', $field );
	}

	/**
	 * @param $controls
	 *
	 * @return Repeater
	 */
	public function setControls( $controls ) {
		$this->setOption( 'controls', $this->parseControls( $controls, true ) );

		return $this->setOption( 'defaultSettings', $this->getDefaults() );
	}
}