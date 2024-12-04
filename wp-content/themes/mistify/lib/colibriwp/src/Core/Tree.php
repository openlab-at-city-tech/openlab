<?php


namespace ColibriWP\Theme\Core;

class Tree {

	const SEPARATOR = '.';

	private $data;

	public function __construct( $data = array() ) {
		$this->data = $data;
	}

	public function walkFirstLevel( $callback ) {
		$this->walkElementsAt( '', $callback );
	}

	public function walkElementsAt( $path, $callback ) {

		$data = $this->getData();

		if ( ! empty( $path ) ) {
			$data = $this->findAt( $path, array() );
		}

		if ( is_array( $data ) ) {
			foreach ( $data as $key => $item ) {
				call_user_func( $callback, $key, $item );
			}
		}
	}

	/**
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param array $data
	 *
	 * @return Tree
	 */
	public function setData( $data ) {
		$this->data = $data;

		return $this;
	}

	public function findAt( $path, $default = null ) {
		$path_parts = explode( self::SEPARATOR, $path );
		$result     = $this->data;

		if ( $path === '' ) {
			return $result;
		}

		while ( $path_parts ) {
			$part = array_shift( $path_parts );

			if ( $this->entityHasKey( $result, $part ) ) {
				if ( is_array( $result ) ) {
					$result = $result[ $part ];
				} else {
					$result = $result->$part;
				}
			} else {
				$result = $default;
				break;
			}
		}

		return $result;
	}

	/**
	 * @param $entity
	 * @param $key
	 *
	 * @return bool
	 */
	private function entityHasKey( $entity, $key ) {
		if ( is_array( $entity ) && array_key_exists( $key, $entity ) ) {
			return true;
		}

		if ( is_object( $entity ) && property_exists( $entity, $key ) ) {
			return true;
		}

		return false;
	}

}
