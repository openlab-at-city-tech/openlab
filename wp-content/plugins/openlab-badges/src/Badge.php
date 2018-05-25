<?php

namespace OpenLab\Badges;

class Badge implements Grantable {
	private $data = array(
		'id'    => null,
		'name'  => null,
		'image' => null,
		'link'  => null,
	);

	public function __construct( $badge_id = null ) {
		if ( $badge_id ) {
			$this->populate( $badge_id );
		}
	}

	private function populate( $badge_id ) {
		$term = get_term( $badge_id, 'openlab_badge' );
		if ( ! $term ) {
			return;
		}

		$this->set_id( $term->term_id );
		$this->set_name( $term->name );

		$image = get_term_meta( $term->term_id, 'image', true );
		if ( $image ) {
			$this->set_image( $image );
		}

		$link = get_term_meta( $term->term_id, 'link', true );
		if ( $link ) {
			$this->set_link( $link );
		}
	}

	public function save() {
		$term_id = $this->get_id();

		if ( ! $term_id ) {
			$term = wp_insert_term( $this->get_name(), 'openlab_badge' );
			if ( is_wp_error( $term ) ) {
				return $term;
			}

			$term_id = (int) $term['term_id'];
			$this->set_id( $term_id );
		} else {
			wp_update_term( $term_id, 'openlab_badge', array(
				'name' => $this->get_name(),
			) );
		}

		update_term_meta( $term_id, 'image', $this->get_image() );
		update_term_meta( $term_id, 'link', $this->get_link() );
	}

	public function set_id( $id ) {
		$this->data['id'] = (int) $id;
	}

	public function set_name( $name ) {
		$this->data['name'] = $name;
	}

	public function set_image( $image ) {
		$this->data['image'] = $image;
	}

	public function set_link( $link ) {
		$this->data['link'] = $link;
	}

	public function get_id() {
		return (int) $this->data['id'];
	}

	public function get_name() {
		return $this->data['name'];
	}

	public function get_image() {
		return $this->data['image'];
	}

	public function get_link() {
		return $this->data['link'];
	}

	/**
	 * @todo Tooltip. See http://accessibility.athena-ict.com/aria/examples/tooltip.shtml
	 */
	public function get_avatar_badge_html() {
		return sprintf(
			'<a href="%s"><img class="badge-image" src="%s" alt="%s" /></a>',
			esc_attr( $this->get_link() ),
			esc_attr( $this->get_image() ),
			esc_attr( $this->get_name() )
		);
	}
}
