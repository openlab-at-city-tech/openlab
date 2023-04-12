<?php

namespace WeBWorK\Server\Util;

/**
 * WordPress post utilities.
 *
 * CRUD utils shared between objects that store content via CPTs.
 *
 * @since 1.0.0
 */
class WPPost {
	/**
	 * Composing object, passed by reference.
	 */
	protected $c;

	protected $post_type;
	protected $post_data = array();
	protected $pf;

	/**
	 * @param \WeBWorK\Server\Util\SaveableAsWPPost
	 */
	public function __construct( SaveableAsWPPost $c ) {
		$this->c  = $c;
		$this->pf = new ProblemFormatter();
	}

	public function set( $field, $value ) {
		$this->post_data[ $field ] = $value;
	}

	public function set_post_type( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * @return int|bool Integer on success, false on failure.
	 */
	public function save() {
		$args = array(
			'post_type'   => $this->post_type,
			'post_status' => 'publish',
		);

		if ( $this->c->exists() ) {
			$args['ID'] = $this->c->get_id();
		}

		// WP will gobble up LaTeX escape characters.
		$content              = $this->c->get_content();
		$content              = $this->pf->convert_delims( $content );
		$content              = $this->pf->swap_latex_escape_characters( $content );
		$args['post_content'] = $content;

		$args['post_author'] = $this->c->get_author_id();

		if ( null !== $this->c->get_post_date() ) {
			// todo gmt
			$args['post_date'] = $this->c->get_post_date();
		}

		if ( $this->c->exists() ) {
			$saved = wp_update_post( $args );
		} else {
			$saved = wp_insert_post( $args );

			if ( $saved && ! is_wp_error( $saved ) ) {
				$this->c->set_id( $saved );
			}
		}

		return $saved;
	}

	public function delete() {
		if ( ! $this->c->exists() ) {
			return false;
		}

		$deleted = wp_trash_post( $this->c->get_id() );

		if ( $deleted ) {
			$this->c->set_id( 0 );
		}

		return (bool) $deleted;
	}

	public function populate( $post = null ) {
		if ( ! $post ) {
			$post = get_post( $this->c->get_id() );
		}

		if ( ! $post || 'publish' !== $post->post_status ) {
			$this->c->set_id( 0 );
			return false;
		}

		// WP post properties.
		$this->c->set_id( $post->ID );
		$this->c->set_author_id( $post->post_author );
		$this->c->set_content( $post->post_content );
		$this->c->set_post_date( $post->post_date );

		return true;
	}

	public function get_author_avatar() {
		return get_avatar_url(
			$this->c->get_author_id(),
			array(
				'size' => 80,
			)
		);
	}

	public function get_author_name() {
		$userdata = get_userdata( $this->c->get_author_id() );
		return $userdata->display_name;
	}

	/**
	 * Get vote count.
	 *
	 * @param int $force_query Whether to skip the metadata cache.
	 * @return int
	 */
	public function get_vote_count( $force_query = false ) {
		$item_id = $this->c->get_id();

		$vote_count = get_post_meta( $item_id, 'webwork_vote_count', true );
		if ( $force_query || '' === $vote_count ) {
			$vote_query = new \WeBWorK\Server\Vote\Query(
				array(
					'item_id' => $item_id,
				)
			);

			$vote_count = $vote_query->get( 'count' );
			update_post_meta( $item_id, 'webwork_vote_count', $vote_count );
		}

		return intval( $vote_count );
	}
}
