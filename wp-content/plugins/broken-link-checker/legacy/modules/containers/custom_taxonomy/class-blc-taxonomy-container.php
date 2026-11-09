<?php
/**
 * Container class that represents a single post for taxonomy-validity checking.
 */
require_once __DIR__ . '/trait-blc-taxonomy-container-ui.php';

/**
 *
 * container_id  = WP post ID
 * container_type = 'custom_taxonomy'
 *
 * Each taxonomy slug found in the post's term-taxonomy relationships becomes a
 * separate field (keyed by the slug) using the 'taxonomy_reference' format.  The
 * companion blcTaxonomyRefParser converts those slugs into taxonomy:// pseudo-URLs,
 * and blcTaxonomyChecker tests whether each taxonomy is still registered.
 *
 * @package Broken Link Checker
 * @access public
 */
class blcTaxonomyContainer extends blcContainer {

	use blcTaxonomyContainerUI;

	/**
	 * Retrieve the post object wrapped by this container.
	 *
	 * @param bool $ensure_consistency Whether to re-query the DB for the post even if we already have a cached object. Set this to true when we want to ensure that the post still exists and its data is up to date (e.g. after an edit or delete operation).
	 * @return WP_Post|null
	 */
	public function get_wrapped_object( $ensure_consistency = false ) {
		if ( $ensure_consistency || is_null( $this->wrapped_object ) ) {
			$this->wrapped_object = get_post( $this->container_id );
		}
		return $this->wrapped_object;
	}

	/**
	 * Not used — we do not modify the post itself from this container.
	 *
	 * @return void
	 */
	public function update_wrapped_object() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			trigger_error(
				'blcTaxonomyContainer::update_wrapped_object() is not supported.',
				E_USER_WARNING
			);
		}
	}

	/**
	 * Return the value of a field.
	 *
	 * For this container the field name IS the taxonomy slug, and the returned
	 * value is also the taxonomy slug (a plain string passed to the taxonomy_ref parser).
	 *
	 * @param string $field Taxonomy slug.
	 * @return string
	 */
	public function get_field( $field = '' ) {
		return $field;
	}

	/**
	 * Override synch() to query term-taxonomy relationships directly from the DB.
	 *
	 * We query the raw `wp_term_taxonomy` table so we also detect taxonomies that
	 * are no longer registered (which `get_object_taxonomies()` would silently skip).
	 *
	 * @return void
	 */
	public function synch() {
		global $wpdb;

		// Get the taxonomy_reference parser. It must be active for this to work.
		$parsers = blcParserHelper::get_parsers( 'taxonomy_reference', $this->container_type );
		if ( empty( $parsers ) ) {
			// No parser available, so we can't create any instances. Just mark as synched to avoid repeated attempts.
			$this->mark_as_synched();
			return;
		}

		// Remove any existing instances for this container.
		$this->delete_instances();

		// Built-in WordPress taxonomies are always registered and can never be
		// orphaned, so there is no value in checking them.
		$builtin_taxonomies = array(
			'category',
			'post_tag',
			'post_format',
			'nav_menu',
			'link_category',
		);

		$placeholders = implode( ', ', array_fill( 0, count( $builtin_taxonomies ), '%s' ) );

		// Retrieve all distinct taxonomy slugs assigned to this post,
		// including those belonging to unregistered taxonomies, but
		// excluding the built-in ones that are always present.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT tt.taxonomy,tt.term_id, t.name
				 FROM {$wpdb->term_relationships} AS tr
				 INNER JOIN {$wpdb->term_taxonomy} AS tt
				     ON tr.term_taxonomy_id = tt.term_taxonomy_id
				 INNER JOIN {$wpdb->terms} AS t
				     ON tt.term_id = t.term_id
				 WHERE tr.object_id = %d
				   AND tt.taxonomy NOT IN ({$placeholders})",
				array_merge( array( $this->container_id ), $builtin_taxonomies )
			)
		);

		if ( empty( $rows ) ) {
			$this->mark_as_synched();
			return;
		}

		$base_url = get_permalink( $this->container_id );

		foreach ( $rows as $row ) {
			$link_text = $row->name;

			if ( empty( $link_text ) ) {
				$link_text = $row->taxonomy;
			}
			foreach ( $parsers as $parser ) {
				$found = $parser->parse( $row->taxonomy, $base_url, $link_text );
				foreach ( $found as $instance ) {
					$instance->set_container( $this, $row->taxonomy );
					$instance->save();
				}
			}
		}

		$this->mark_as_synched();
	}

	/**
	 * "Editing" a taxonomy reference makes no sense in this context.
	 * Return the raw URL unchanged so BLC considers it a no-op.
	 *
	 * @param string      $field_name The taxonomy slug (stored as container_field).
	 * @param blcParser   $parser     The parser instance requesting the edit.
	 * @param string      $new_url    Ignored.
	 * @param string      $old_url    Ignored.
	 * @param string      $old_raw_url Ignored.
	 * @param string|null $new_text   Ignored.
	 * @return string The raw_url value (unchanged).
	 */
	public function edit_link( $field_name, $parser, $new_url, $old_url = '', $old_raw_url = '', $new_text = null ) {
		return $old_raw_url;
	}

	/**
	 * "Unlinking" removes the term-post relationship for the offending taxonomy terms.
	 *
	 * All terms belonging to $field_name (the taxonomy slug stored in container_field)
	 * are removed from this post.
	 *
	 * @param string    $field_name The taxonomy slug (stored as container_field).
	 * @param blcParser $parser     The parser instance requesting the unlink.
	 * @param string    $url        Ignored.
	 * @param string    $raw_url    The original taxonomy slug.
	 * @return bool|WP_Error
	 */
	public function unlink( $field_name, $parser, $url, $raw_url = '' ) {
		$taxonomy = '' !== $raw_url ? $raw_url : $field_name;

		// wp_set_object_terms() with an empty array removes all terms of that taxonomy.
		if ( taxonomy_exists( $taxonomy ) ) {
			$result = wp_set_object_terms( $this->container_id, array(), $taxonomy );
		} else {
			// The taxonomy is not registered; remove the raw term relationships from the DB.
			global $wpdb;
			$deleted = $wpdb->query(
				$wpdb->prepare(
					"DELETE tr FROM {$wpdb->term_relationships} AS tr
					 INNER JOIN {$wpdb->term_taxonomy} AS tt
					     ON tr.term_taxonomy_id = tt.term_taxonomy_id
					 WHERE tr.object_id = %d
					   AND tt.taxonomy  = %s",
					$this->container_id,
					$taxonomy
				)
			);

			$result = false !== $deleted;
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}

	/**
	 * Return the default "link text" for a field — just the taxonomy slug.
	 *
	 * @param string $field Taxonomy slug.
	 * @return string
	 */
	public function default_link_text( $field = '' ) {
		return $field;
	}

	/**
	 * Return the base URL (post permalink) for relative-URL normalisation.
	 *
	 * @return string
	 */
	public function base_url() {
		return get_permalink( $this->container_id );
	}

	/**
	 * Delete or trash the post.
	 *
	 * @return bool|WP_Error
	 */
	public function delete_wrapped_object() {
		if ( EMPTY_TRASH_DAYS ) {
			return $this->trash_wrapped_object();
		}

		if ( wp_delete_post( $this->container_id, true ) ) {
			return true;
		}

		return new WP_Error(
			'delete_failed',
			sprintf(
				// translators: %1$s: post title, %2$d: post ID.
				__( 'Failed to delete post "%1$s" (%2$d)', 'broken-link-checker' ),
				get_the_title( $this->container_id ),
				$this->container_id
			)
		);
	}

	/**
	 * Move the post to the Trash.
	 *
	 * @return bool|WP_Error
	 */
	public function trash_wrapped_object() {
		if ( ! EMPTY_TRASH_DAYS ) {
			return new WP_Error(
				'trash_disabled',
				sprintf(
					// translators: %1$s: post title, %2$d: post ID.
					__( 'Can\'t move post "%1$s" (%2$d) to the trash because the trash feature is disabled', 'broken-link-checker' ),
					get_the_title( $this->container_id ),
					$this->container_id
				)
			);
		}

		$post = get_post( $this->container_id );
		if ( $post && 'trash' === $post->post_status ) {
			return true;
		}

		if ( wp_trash_post( $this->container_id ) ) {
			return true;
		}

		return new WP_Error(
			'trash_failed',
			sprintf(
				// translators: %1$s: post title, %2$d: post ID.
				__( 'Failed to move post "%1$s" (%2$d) to the trash', 'broken-link-checker' ),
				get_the_title( $this->container_id ),
				$this->container_id
			)
		);
	}

	/**
	 * Check if the current user can delete the wrapped object.
	 *
	 * @return bool
	 */
	public function current_user_can_delete() {
		$post             = $this->get_wrapped_object();
		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object ) {
			return false;
		}

		return current_user_can( $post_type_object->cap->delete_post, $this->container_id );
	}

	/**
	 * Check if the current user can edit the wrapped post.
	 *
	 * @return bool
	 */
	public function current_user_can_edit() {
		$post = $this->get_wrapped_object();
		if ( ! $post ) {
			return false;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object ) {
			return false;
		}

		return current_user_can( $post_type_object->cap->edit_post, $this->container_id );
	}

	/**
	 * Check if the wrapped object can be moved to the trash.
	 *
	 * @return bool
	 */
	public function can_be_trashed() {
		return defined( 'EMPTY_TRASH_DAYS' ) && EMPTY_TRASH_DAYS;
	}
}
