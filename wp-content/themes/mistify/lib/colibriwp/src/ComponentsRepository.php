<?php


namespace ColibriWP\Theme;

use ColibriWP\Theme\Core\ComponentInterface;
use ColibriWP\Theme\Core\Hooks;

class ComponentsRepository {

	private $entities = array();

	public function load() {
		$components = Hooks::prefixed_apply_filters( 'components', array() );

		foreach ( $components as $key => $class ) {

			$this->add( $key, $class );
		}
	}


	/**
	 * @param $id
	 *
	 * @return null|ComponentInterface
	 */
	private function getInstance( $id ) {

		if ( ! $this->entities[ $id ] ['instance'] ) {
			$class = $this->entities[ $id ]['class'];

			$this->entities[ $id ] = array(
				'class'    => $this->entities[ $id ]['class'],
				'instance' => new $class(),
			);
		}

		return $this->entities[ $id ]['instance'];
	}

	/**
	 * @param $id
	 *
	 * @return ComponentInterface|null
	 */
	public function getByName( $id ) {

		if ( array_key_exists( $id, $this->entities ) ) {
			return $this->getInstance( $id );
		}

		return null;
	}


	/**
	 * @return array
	 */
	public function getAllDefinitions() {
		$result = array();

		foreach ( $this->entities as $key => $entity ) {
			$result[ $key ] = $entity['class'];
		}

		return $result;
	}

	/**
	 * @param $component_name
	 * @param $class
	 */
	public function add( $component_name, $class ) {
		$this->entities[ $component_name ] = array(
			'class'    => $class,
			'instance' => null,
		);
	}
}
