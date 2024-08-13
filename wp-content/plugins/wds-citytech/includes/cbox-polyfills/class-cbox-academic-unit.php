<?php

class CBOX_Academic_Unit {
	protected $name;
	protected $parent;
	protected $slug;
	protected $type;

	public function __construct( $args ) {
		$string_keys = [ 'name', 'parent', 'slug', 'type' ];
		foreach ( $string_keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = (string) $args[ $key ];
			}
		}
	}

	public function get_name() {
		return $this->name;
	}

	public function get_parent() {
		return $this->parent;
	}

	public function get_slug() {
		return $this->slug;
	}

	public function get_type() {
		return $this->type;
	}

	public function get_wp_post_id() {
		return $this->get_slug();
	}
}
