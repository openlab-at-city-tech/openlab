<?php

class CBOX_Member_Type {
	protected $slug;
	protected $name;
	protected $can_create_courses;

	public function __construct( $args ) {
		$string_keys = [ 'slug', 'name' ];
		foreach ( $string_keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = (string) $args[ $key ];
			}
		}

		$bool_keys = [ 'can_create_courses' ];
		foreach ( $bool_keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = (bool) $args[ $key ];
			}
		}
	}

	public function get_slug() {
		return $this->slug;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_label() {
		return $this->name;
	}

	public function get_can_create_courses() {
		return $this->can_create_courses;
	}
}
