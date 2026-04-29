<?php

namespace Imagely\NGG\Display;

/**
 * View element for building display structures.
 */
class ViewElement {

	/**
	 * Element ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Element type.
	 *
	 * @var string|null
	 */
	protected $type;

	/**
	 * List of child elements.
	 *
	 * @var array
	 */
	protected $list;

	/**
	 * Element context data.
	 *
	 * @var array
	 */
	protected $context;

	/**
	 * Constructor.
	 *
	 * @param string      $id The element ID.
	 * @param string|null $type The element type.
	 */
	public function __construct( $id, $type = null ) {
		$this->id      = $id;
		$this->type    = $type;
		$this->list    = [];
		$this->context = [];
	}

	/**
	 * Get the element ID.
	 *
	 * @return string The element ID.
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Append a child element.
	 *
	 * @param mixed $child The child element to append.
	 */
	public function append( $child ) {
		$this->list[] = $child;
	}

	/**
	 * Insert a child element at a specific position.
	 *
	 * @param mixed $child The child element to insert.
	 * @param int   $position The position to insert at.
	 */
	public function insert( $child, $position = 0 ) {
		array_splice( $this->list, $position, 0, $child );
	}

	/**
	 * Delete a child element.
	 *
	 * @param mixed $child The child element to delete.
	 */
	public function delete( $child ) {
		$index = array_search( $child, $this->list, true );

		if ( false !== $index ) {
			array_splice( $this->list, $index, 1 );
		}
	}

	/**
	 * Find elements by ID.
	 *
	 * @param string $id The ID to find.
	 * @param bool   $recurse Whether to search recursively.
	 * @return array Found elements.
	 */
	public function find( $id, $recurse = false ) {
		$list = [];

		$this->_find( $list, $id, $recurse );

		return $list;
	}

	/**
	 * Internal find helper method.
	 *
	 * @param array  $found_list Reference to list to populate with found elements.
	 * @param string $id The ID to find.
	 * @param bool   $recurse Whether to search recursively.
	 */
	public function _find( array &$found_list, $id, $recurse = false ) {
		foreach ( $this->list as $index => $element ) {
			if ( $element instanceof ViewElement ) {
				if ( $element->get_id() == $id ) {
					$found_list[] = $element;
				}
				if ( $recurse ) {
					$element->_find( $found_list, $id, $recurse );
				}
			}
		}
	}

	/**
	 * Get a context value by name.
	 *
	 * @param string $name The context name.
	 * @return mixed|null The context value or null if not found.
	 */
	public function get_context( $name ) {
		if ( isset( $this->context[ $name ] ) ) {
			return $this->context[ $name ];
		}

		return null;
	}

	/**
	 * Set a context value.
	 *
	 * @param string $name The context name.
	 * @param mixed  $value The context value.
	 */
	public function set_context( $name, $value ) {
		$this->context[ $name ] = $value;
	}

	/**
	 * Get the object from context.
	 *
	 * @return mixed|null The object or null if not found.
	 */
	public function get_object() {
		return $this->get_context( 'object' );
	}

	/**
	 * Rasterize the element tree to a string.
	 *
	 * @return string|null The rasterized output.
	 */
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
