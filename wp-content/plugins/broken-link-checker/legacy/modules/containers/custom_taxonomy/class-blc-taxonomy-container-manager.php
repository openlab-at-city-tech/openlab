<?php

/**
 * Manager for the custom_taxonomy container type.
 *
 * One synch record is maintained per published post. When any term relationship
 * is changed (term added, removed, or the post saved) the record is marked as
 * unsynched so BLC will re-examine the post.
 *
 * @package Broken Link Checker
 * @access public
 */
class blcTaxonomyContainerManager extends blcContainerManager {

	/**
	 * The name of the container class this manager handles.
	 *
	 * @var string
	 */
	public $container_class_name = 'blcTaxonomyContainer';

	/**
	 * Built-in WordPress taxonomies that are always registered and therefore
	 * can never be orphaned. Posts that only carry these are excluded from
	 * the synch records created by this manager.
	 *
	 * @var string[]
	 */
	protected $builtin_taxonomies = array(
		'category',
		'post_tag',
		'post_format',
		'nav_menu',
		'link_category',
	);

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function init() {
		parent::init();

		// Mark the post as unsynched whenever its terms change.
		add_action( 'set_object_terms', array( $this, 'on_set_object_terms' ), 10, 4 );
		add_action( 'added_term_relationship', array( $this, 'on_term_relationship_changed' ), 10, 2 );
		add_action( 'deleted_term_relationships', array( $this, 'on_term_relationships_deleted' ), 10, 2 );

		// Keep synch records in step with post lifecycle.
		add_action( 'save_post', array( $this, 'on_post_saved' ) );
		add_action( 'delete_post', array( $this, 'on_post_deleted' ) );
		add_action( 'trash_post', array( $this, 'on_post_deleted' ) );
		add_action( 'untrash_post', array( $this, 'on_post_saved' ) );
	}

	// -------------------------------------------------------------------------
	// Hook callbacks
	// -------------------------------------------------------------------------

	/**
	 * Fired by 'set_object_terms' after all terms have been updated for an object.
	 *
	 * @param int    $object_id Post ID.
	 * @param array  $terms     Term IDs or slugs.
	 * @param array  $tt_ids    Term-taxonomy IDs.
	 * @param string $taxonomy  Taxonomy slug.
	 * @return void
	 */
	public function on_set_object_terms( $object_id, $terms, $tt_ids, $taxonomy ) {
		$this->mark_post_unsynched( $object_id );
	}

	/**
	 * Fired by 'added_term_relationship'.
	 *
	 * @param int $object_id Post ID.
	 * @param int $tt_id     Term-taxonomy ID.
	 * @return void
	 */
	public function on_term_relationship_changed( $object_id, $tt_id ) {
		$this->mark_post_unsynched( $object_id );
	}

	/**
	 * Fired by 'deleted_term_relationships'.
	 *
	 * @param int   $object_id Post ID.
	 * @param array $tt_ids    Array of term-taxonomy IDs.
	 * @return void
	 */
	public function on_term_relationships_deleted( $object_id, $tt_ids ) {
		$this->mark_post_unsynched( $object_id );
	}

	/**
	 * When a published post is saved, mark it as unsynched.
	 *
	 * @param int $post_id ID of the saved post.
	 * @return void
	 */
	public function on_post_saved( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'publish' !== $post->post_status ) {
			return;
		}
		$this->mark_post_unsynched( $post_id );
	}

	/**
	 * When a post is deleted or trashed, remove its synch record and instances.
	 *
	 * @param int $post_id ID of the deleted or trashed post.
	 * @return void
	 */
	public function on_post_deleted( $post_id ) {
		$container = blcContainerHelper::get_container( array( $this->container_type, intval( $post_id ) ) );
		if ( $container ) {
			$container->delete();
			blc_cleanup_links();
		}
	}

	/**
	 * Mark a post's taxonomy container as unsynched.
	 *
	 * @param int $post_id ID of the post.
	 * @return void
	 */
	public function mark_post_unsynched( $post_id ) {
		$container = blcContainerHelper::get_container( array( $this->container_type, intval( $post_id ) ) );
		if ( $container ) {
			$container->mark_as_unsynched();
		}
	}

	// -------------------------------------------------------------------------
	// Container instantiation
	// -------------------------------------------------------------------------

	/**
	 * Instantiate multiple containers, optionally pre-loading their wrapped posts.
	 *
	 * @param array  $containers           Array of container identifiers (container_type, container_id).
	 * @param string $purpose              Optional context for which the containers will be loaded; may be used to determine whether to preload the wrapped posts.
	 * @param bool   $load_wrapped_objects Whether to preload the wrapped posts.
	 * @return blcTaxonomyContainer[]
	 */
	public function get_containers( $containers, $purpose = '', $load_wrapped_objects = false ) {
		$containers = $this->make_containers( $containers );

		$preload = $load_wrapped_objects || in_array( $purpose, array( BLC_FOR_DISPLAY ) );
		if ( $preload ) {
			$post_ids = array();
			foreach ( $containers as $container ) {
				$post_ids[] = $container->container_id;
			}
			if ( ! empty( $post_ids ) ) {
				get_posts( array( 'include' => implode( ',', $post_ids ) ) );
			}
		}

		return $containers;
	}

	// -------------------------------------------------------------------------
	// Resynch
	// -------------------------------------------------------------------------

	/**
	 * Create or update synchronisation records for all published posts that have
	 * at least one term-taxonomy relationship.
	 *
	 * @param bool $forced When true, recreate all records from scratch.
	 * @return void
	 */
	public function resynch( $forced = false ) {
		if ( $forced ) {
			$this->force_create_all_synch_records();
		} else {
			$this->delete_stale_synch_records();
			$this->mark_modified_posts_unsynched();
			$this->create_missing_synch_records();
		}
	}

	/**
	 * Insert synch records for every published post that has terms, preserving
	 * existing rows via ON DUPLICATE KEY UPDATE.
	 *
	 * @return void
	 */
	private function force_create_all_synch_records() {
		global $wpdb;
		global $blclog;

		$blclog->log( '...... Creating taxonomy synch records for all published posts with custom taxonomy terms' );
		$start        = microtime( true );
		$placeholders = implode( ', ', array_fill( 0, count( $this->builtin_taxonomies ), '%s' ) );

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}blc_synch (container_id, container_type, synched)
				SELECT DISTINCT tr.object_id, %s, 0
				FROM {$wpdb->term_relationships} AS tr
				INNER JOIN {$wpdb->posts} AS p ON p.ID = tr.object_id AND p.post_status = 'publish'
				INNER JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
					AND tt.taxonomy NOT IN ({$placeholders})
				ON DUPLICATE KEY UPDATE synched = synched",
				array_merge( array( $this->container_type ), $this->builtin_taxonomies )
			)
		);
		$blclog->log( sprintf( '...... %d rows inserted/updated in %.3f seconds', $wpdb->rows_affected, microtime( true ) - $start ) );
	}

	/**
	 * Delete synch records for posts that no longer exist or are no longer published.
	 *
	 * @return void
	 */
	private function delete_stale_synch_records() {
		global $wpdb;
		global $blclog;

		$placeholders = implode( ', ', array_fill( 0, count( $this->builtin_taxonomies ), '%s' ) );

		// Remove records for posts that no longer exist or are no longer published.
		$blclog->log( '...... Deleting taxonomy synch records for removed/unpublished posts' );
		$start = microtime( true );

		$wpdb->query(
			$wpdb->prepare(
				"DELETE synch.*
				FROM {$wpdb->prefix}blc_synch AS synch
				LEFT JOIN {$wpdb->posts} AS p ON p.ID = synch.container_id AND p.post_status = 'publish'
				WHERE synch.container_type = %s
				  AND p.ID IS NULL",
				$this->container_type
			)
		);
		$blclog->log( sprintf( '...... %d rows deleted in %.3f seconds', $wpdb->rows_affected, microtime( true ) - $start ) );

		// Also remove records for posts that have lost all their custom taxonomy
		// terms (only built-in terms remain, which this module does not check).
		$blclog->log( '...... Deleting taxonomy synch records for posts with no custom taxonomy terms' );
		$start = microtime( true );

		$wpdb->query(
			$wpdb->prepare(
				"DELETE synch.*
				FROM {$wpdb->prefix}blc_synch AS synch
				WHERE synch.container_type = %s
				  AND NOT EXISTS (
					SELECT 1
					FROM {$wpdb->term_relationships} AS tr
					INNER JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
						AND tt.taxonomy NOT IN ({$placeholders})
					WHERE tr.object_id = synch.container_id
				  )",
				array_merge( array( $this->container_type ), $this->builtin_taxonomies )
			)
		);
		$blclog->log( sprintf( '...... %d rows deleted in %.3f seconds', $wpdb->rows_affected, microtime( true ) - $start ) );
	}

	/**
	 * Mark synch records as unsynched when the post has been modified since the last synch.
	 *
	 * @return void
	 */
	private function mark_modified_posts_unsynched() {
		global $wpdb;
		global $blclog;

		$blclog->log( '...... Marking modified posts as unsynched (taxonomy container)' );
		$start = microtime( true );

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}blc_synch AS synch
		      JOIN {$wpdb->posts} AS p ON p.ID = synch.container_id
		      SET synch.synched = 0
		      WHERE synch.container_type = %s
		        AND synch.last_synch < p.post_modified",
				$this->container_type
			)
		);
		$blclog->log( sprintf( '...... %d rows updated in %.3f seconds', $wpdb->rows_affected, microtime( true ) - $start ) );
	}

	/**
	 * Insert synch records for published posts that have terms but no existing record.
	 *
	 * @return void
	 */
	private function create_missing_synch_records() {
		global $wpdb;
		global $blclog;

		$blclog->log( '...... Creating taxonomy synch records for new published posts with custom taxonomy terms' );
		$start        = microtime( true );
		$placeholders = implode( ', ', array_fill( 0, count( $this->builtin_taxonomies ), '%s' ) );

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}blc_synch (container_id, container_type, synched)
				SELECT DISTINCT tr.object_id, %s, 0
				FROM {$wpdb->term_relationships} AS tr
				INNER JOIN {$wpdb->posts} AS p ON p.ID = tr.object_id AND p.post_status = 'publish'
				INNER JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
					AND tt.taxonomy NOT IN ({$placeholders})
				LEFT JOIN {$wpdb->prefix}blc_synch AS synch
					ON synch.container_id = tr.object_id AND synch.container_type = %s
				WHERE synch.container_id IS NULL",
				array_merge( array( $this->container_type ), $this->builtin_taxonomies, array( $this->container_type ) )
			)
		);
		$blclog->log( sprintf( '...... %d rows inserted in %.3f seconds', $wpdb->rows_affected, microtime( true ) - $start ) );
	}

	// -------------------------------------------------------------------------
	// UI helpers
	// -------------------------------------------------------------------------

	/**
	 * Get the bulk delete message for this container type.
	 *
	 * @param int $amount_of_posts Total amount of items to delete.
	 * @return string
	 */
	public function ui_bulk_delete_message( $amount_of_posts ) {
		return blcAnyPostContainerManager::build_delete_action_message(
			$amount_of_posts,
			'post',
			null
		);
	}

	/**
	 * Get the bulk trash message for this container type.
	 *
	 * @param int $amount_of_posts Total amount of items to trash.
	 * @return string
	 */
	public function ui_bulk_trash_message( $amount_of_posts ) {
		return blcAnyPostContainerManager::build_trash_action_message(
			$amount_of_posts,
			'post',
			null
		);
	}
}
