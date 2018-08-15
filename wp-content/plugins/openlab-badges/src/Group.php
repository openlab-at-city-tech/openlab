<?php

namespace OpenLab\Badges;

class Group {
	private $group_id;

	public function __construct( $group_id = null ) {
		if ( $group_id ) {
			$this->set_group_id( $group_id );
		}
	}

	public function set_group_id( $group_id ) {
		$this->group_id = (int) $group_id;
	}

	public function get_group_id() {
		return (int) $this->group_id;
	}

	public function get_badges() {
		$terms  = wp_get_object_terms( $this->get_group_id(), 'openlab_badge' );
		$badges = array_map( function( $term ) {
			return new Badge( $term->term_id );
		}, $terms );
		return $badges;
	}

	public function grant( Grantable $badge ) {
		$set = wp_set_object_terms( $this->get_group_id(), $badge->get_id(), 'openlab_badge', true );
		return $set;
	}

	public function revoke( Grantable $badge ) {
		$removed = wp_remove_object_terms( $this->get_group_id(), $badge->get_id(), 'openlab_badge' );
		return $removed;
	}
}
