<?php
/**
 * UI trait for blcTaxonomyContainer.
 *
 * @package Broken Link Checker
 */

/**
 *
 * Provides the display-facing methods used by the broken-links admin table and
 * email notifications. Kept separate from the data/synch logic so each concern
 * lives in its own file.
 *
 * @package Broken Link Checker
 * @access public
 */
trait blcTaxonomyContainerUI {

	/**
	 * Returns the HTML for the "Source" column shown in the broken-links list.
	 *
	 * @param string $container_field The taxonomy slug stored as container_field.
	 * @param string $context         'display' or 'email'.
	 * @return string HTML.
	 */
	public function ui_get_source( $container_field = '', $context = 'display' ) {
		$post = $this->get_wrapped_object();
		if ( ! $post ) {
			return '';
		}

		if ( 'email' === $context ) {
			return sprintf(
				'<a href="%s">%s</a>',
				esc_url( get_permalink( $this->container_id ) ),
				esc_html( get_the_title( $this->container_id ) )
			);
		}

		return sprintf(
			'<a class="row-title" href="%s" title="%s">%s</a>',
			esc_url( $this->get_edit_url() ),
			esc_attr( __( 'Edit this post', 'broken-link-checker' ) ),
			esc_html( get_the_title( $this->container_id ) )
		);
	}

	/**
	 * Returns inline action links for this container.
	 *
	 * @param string $container_field The taxonomy slug stored as container_field.
	 * @return array
	 */
	public function ui_get_action_links( $container_field ) {
		$actions = array();
		$post    = $this->get_wrapped_object();

		if ( ! $post ) {
			return $actions;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object ) {
			return $actions;
		}

		if ( current_user_can( $post_type_object->cap->edit_post, $this->container_id ) ) {
			// translators: %s: post title.
			$actions['edit'] = sprintf(
				'<span class="edit"><a href="%s" title="%s">%s</a></span>',
				esc_url( $this->get_edit_url() ),
				esc_attr( $post_type_object->labels->edit_item ),
				__( 'Edit', 'broken-link-checker' )
			);
		}

		//Note: core's get_delete_post_link() has no special-case handling for
		//Site Editor post types (unlike get_edit_post_link()), so calling it for
		//them throws an ArgumentCountError. They're deleted via the Site Editor,
		//not this legacy link, so we simply omit the action for them.
		if ( current_user_can( $post_type_object->cap->delete_post, $this->container_id )
			&& ! $this->is_site_editor_post_type( $post->post_type )
		) {
			$actions['trash'] = sprintf(
				'<span class="trash"><a href="%s" title="%s">%s</a></span>',
				esc_url( get_delete_post_link( $this->container_id, '', false ) ),
				// translators: %s: post title.
				esc_attr( sprintf( __( 'Move &#8220;%s&#8221; to the Trash', 'broken-link-checker' ), get_the_title( $this->container_id ) ) ),
				__( 'Trash', 'broken-link-checker' )
			);
		}

		$actions['view'] = sprintf(
			'<span class="view"><a href="%s" title="%s" rel="permalink">%s</a></span>',
			esc_url( get_permalink( $this->container_id ) ),
			// translators: %s: post title.
			esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'broken-link-checker' ), get_the_title( $this->container_id ) ) ),
			__( 'View', 'broken-link-checker' )
		);

		return $actions;
	}

	/**
	 * Returns the admin edit URL for the wrapped post.
	 *
	 * @return string
	 */
	public function get_edit_url() {
		$post = $this->get_wrapped_object();
		if ( ! $post ) {
			return '';
		}

		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object ) {
			return '';
		}

		$link = '';
		if ( $post_type_object->_edit_link ) {
			if ( 'wp_template' === $post->post_type || 'wp_template_part' === $post->post_type ) {
				$slug = urlencode( get_stylesheet() . '//' . $post->post_name );
				$link = admin_url( sprintf( $post_type_object->_edit_link, $post->post_type, $slug ) );
			} elseif ( 'wp_navigation' === $post->post_type ) {
				$link = admin_url( sprintf( $post_type_object->_edit_link, (string) $post->ID ) );
			} else {
				$link = admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $post->ID ) );
			}
		}

		return apply_filters( 'get_edit_post_link', $link, $post->ID, 'display' );
	}
}
