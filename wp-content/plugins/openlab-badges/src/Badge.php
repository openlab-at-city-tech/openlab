<?php

namespace OpenLab\Badges;

class Badge implements Grantable {
	private $data = array(
		'id'    => null,
		'name'  => null,
		'slug'  => null,
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
		$this->set_slug( $term->slug );

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
			$term = wp_insert_term( $this->get_name(), 'openlab_badge', array(
				'slug' => $this->get_slug(),
			) );
			if ( is_wp_error( $term ) ) {
				return $term;
			}

			$term_id = (int) $term['term_id'];
			$this->set_id( $term_id );
		} else {
			wp_update_term( $term_id, 'openlab_badge', array(
				'name' => $this->get_name(),
				'slug' => $this->get_slug(),
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

	public function set_slug( $slug ) {
		$this->data['slug'] = $slug;
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

	public function get_slug() {
		return $this->data['slug'];
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
	public function get_avatar_badge_html( $group_id ) {
		$group = groups_get_group( $group_id );

		$tooltip_id = 'badge-tooltip-' . $group->slug . '-' . $this->get_slug();

		$html  = '<div class="avatar-badge">';
		$html .=   '<img class="badge-image" aria-describedby="' . esc_attr( $tooltip_id ) . '" src="' . esc_attr( $this->get_image() ) . '" alt="' . esc_attr( $this->get_name() ) . '" />';
		$html .=   '<div id="' . esc_attr( $tooltip_id ) . '" class="badge-tooltip" role="tooltip">';
		$html .=     esc_html( $this->get_name() ) . " &mdash; " . sprintf( '<a href="%s">%s</a>', esc_attr( $this->get_link() ), esc_html__( 'Learn More', 'openlab-badges' ) );
		$html .=   '</div>';
		$html .= '</div>';

		return $html;
	}
}
