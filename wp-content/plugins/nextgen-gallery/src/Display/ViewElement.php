<?php

namespace Imagely\NGG\Display;

class ViewElement {

	protected $id;
	protected $type;
	protected $list;
	protected $context;

	public function __construct( $id, $type = null ) {
		$this->id      = $id;
		$this->type    = $type;
		$this->list    = [];
		$this->context = [];
	}

	public function get_id() {
		return $this->id;
	}

	public function append( $child ) {
		$this->list[] = $child;
	}

	public function insert( $child, $position = 0 ) {
		array_splice( $this->list, $position, 0, $child );
	}

	public function delete( $child ) {
		$index = array_search( $child, $this->list );

		if ( $index !== false ) {
			array_splice( $this->list, $index, 1 );
		}
	}

	public function find( $id, $recurse = false ) {
		$list = [];

		$this->_find( $list, $id, $recurse );

		return $list;
	}

	public function _find( array &$list, $id, $recurse = false ) {
		foreach ( $this->list as $index => $element ) {
			if ( $element instanceof ViewElement ) {
				if ( $element->get_id() == $id ) {
					$list[] = $element;
				}
				if ( $recurse ) {
					$element->_find( $list, $id, $recurse );
				}
			}
		}
	}

	public function get_context( $name ) {
		if ( isset( $this->context[ $name ] ) ) {
			return $this->context[ $name ];
		}

		return null;
	}

	public function set_context( $name, $value ) {
		$this->context[ $name ] = $value;
	}

	public function get_object() {
		return $this->get_context( 'object' );
	}

	public function rasterize() {
		$ret = null;

		foreach ( $this->list as $index => $element ) {
			if ( $element instanceof ViewElement ) {
				$ret .= $element->rasterize();
			} else {
				$ret .= (string) $element;
			}
		}

		return $ret;
	}
}
