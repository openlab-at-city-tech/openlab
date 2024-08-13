<?php

class CBOX_Academic_Unit_Type {
	protected $slug;
	protected $name;
	protected $parent;
	protected $group_types = [];

	public function __construct( $args ) {
		$string_keys = [ 'slug', 'name', 'parent' ];
		foreach ( $string_keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = (string) $args[ $key ];
			}
		}

		if ( isset( $args['group_types'] ) ) {
			$this->group_types = (array) $args['group_types'];
		}
	}

	public function get_slug() {
		return $this->slug;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_parent() {
		return $this->parent;
	}

	public function get_group_types() {
		return $this->group_types;
	}
}
