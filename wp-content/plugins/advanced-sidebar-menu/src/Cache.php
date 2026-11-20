<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Traits\Singleton;

/**
 * Menu Cache
 *
 * @author OnPoint Plugins
 */
class Cache {
	use Singleton;

	public const CACHE_GROUP     = 'advanced-sidebar-menu';
	public const CHILD_PAGES_KEY = 'child-pages';

	/**
	 * @since 9.9.0
	 */
	public const PARENTS_WITH_CHILDREN_KEY = 'advanced-sidebar-menu/cache/parents-with-children';


	/**
	 * Actions.
	 */
	protected function hook(): void {
		add_action( 'save_post', [ $this, 'clear_on_post_save' ], 10, 2 );
		add_action( 'deleted_post', [ $this, 'clear_on_post_save' ], 10, 2 );
	}


	/**
	 * Clear all items in this cache group when a supported post type is saved or deleted.
	 *
	 * The basic version only supports the `page` post type, however filters could
	 * be used to add additional post types, so we flush any hierarchical post type.
	 *
	 * @param int      $post_id - ID of the updated posts.
	 * @param \WP_Post $post    - Full post object, so we can use the post_type.
	 *
	 * @return void
	 */
	public function clear_on_post_save( $post_id, \WP_Post $post ): void {
		$types = \apply_filters( 'advanced-sidebar-menu/utils/supported-post-types', false, [ 'page' ] );
		if ( false === $types ) {
			$types = get_post_types( [
				'hierarchical' => true,
				'public'       => true,
			] );
		}
		if ( \array_key_exists( $post->post_type, $types ) ) {
			$this->clear_cache_group();
		}
	}


	/**
	 * Clear all items in this cache group
	 *
	 * @return void
	 */
	public function clear_cache_group(): void {
		wp_cache_set( 'last_changed', microtime(), static::CACHE_GROUP . ':' . ADVANCED_SIDEBAR_MENU_BASIC_VERSION );
	}


	/**
	 * Get the group holding the cache entries.
	 *
	 * @return string
	 */
	public function get_cache_group(): string {
		$group = static::CACHE_GROUP . ':' . ADVANCED_SIDEBAR_MENU_BASIC_VERSION;
		if ( \function_exists( 'wp_cache_get_multiple_salted' ) ) {
			// Salted entries overwrite in place.
			return $group;
		}
		return $group . ':' . $this->get_last_changed();
	}


	/**
	 * Retrieve a post's child pages from the cache.
	 * If no exist in the cache will return false.
	 *
	 * @noinspection ProperNullCoalescingOperatorUsageInspection
	 *
	 * @param array<string, mixed> $args - Arguments passed to `get_posts`.
	 *
	 * @return list<int>|bool
	 *
	 */
	public function get_child_pages( array $args ) {
		return $this->get_ids( static::CHILD_PAGES_KEY, $args ) ?? false;
	}


	/**
	 * Add a post and its children to the cache.
	 *
	 * @param array<string, mixed> $args        - Arguments passed to `get_posts`.
	 * @param array<int>           $child_pages - List of child page ids to store.
	 *
	 * @return void
	 */
	public function store_child_pages( array $args, array $child_pages ): void {
		$this->store_ids( static::CHILD_PAGES_KEY, $args, $child_pages );
	}


	/**
	 * Store each query not yet stored as having no child pages.
	 *
	 * @since 9.9.0
	 *
	 * @param array<array<string, mixed>> $args_list - Arguments passed to `get_posts` for each parent.
	 *
	 * @return void
	 */
	public function store_childless_pages( array $args_list ): void {
		$entries = [];
		foreach ( $args_list as $args ) {
			$entries[ $this->get_key( static::CHILD_PAGES_KEY, $args ) ] = [];
		}
		$stored = \array_filter( $this->get_entries( \array_keys( $entries ) ), '\is_array' );

		$this->store_entries( \array_diff_key( $entries, $stored ) );
	}


	/**
	 * Retrieve the parents with children from the cache.
	 *
	 * @since 9.9.0
	 *
	 * @param array<string, mixed> $query_args - Arguments passed to `get_posts`.
	 *
	 * @return ?list<int>
	 */
	public function get_pages_with_children( array $query_args ): ?array {
		return $this->get_ids( static::PARENTS_WITH_CHILDREN_KEY, $query_args );
	}


	/**
	 * Add the parents with children to the cache.
	 *
	 * @since 9.9.0
	 *
	 * @param array<string, mixed> $query_args - Arguments passed to `get_posts`.
	 * @param array<int>           $parent_ids - Ids of the parents which have children.
	 *
	 * @return void
	 */
	public function store_pages_with_children( array $query_args, array $parent_ids ): void {
		$this->store_ids( static::PARENTS_WITH_CHILDREN_KEY, $query_args, $parent_ids );
	}


	/**
	 * Retrieve the list of ids stored for the query arguments.
	 *
	 * @param string               $prefix     - Prefix of the entry keys.
	 * @param array<string, mixed> $query_args - Arguments passed to `get_posts`.
	 *
	 * @return ?list<int>
	 */
	protected function get_ids( string $prefix, array $query_args ): ?array {
		$key = $this->get_key( $prefix, $query_args );
		$ids = $this->get_entries( [ $key ] )[ $key ] ?? null;
		if ( \is_array( $ids ) ) {
			return Utils::instance()->clean_post_ids( $ids );
		}
		return null;
	}


	/**
	 * Store the list of ids for the query arguments.
	 *
	 * @param string               $prefix     - Prefix of the entry keys.
	 * @param array<string, mixed> $query_args - Arguments passed to `get_posts`.
	 * @param array<int>           $ids        - Ids to store.
	 *
	 * @return void
	 */
	protected function store_ids( string $prefix, array $query_args, array $ids ): void {
		$this->store_entries( [ $this->get_key( $prefix, $query_args ) => $ids ] );
	}


	/**
	 * Retrieve the entries stored under the keys, salted when supported.
	 *
	 * @param array<string> $keys - Entry keys.
	 *
	 * @return array<mixed>
	 */
	protected function get_entries( array $keys ): array {
		if ( \function_exists( 'wp_cache_get_multiple_salted' ) ) {
			return wp_cache_get_multiple_salted( $keys, $this->get_cache_group(), $this->get_last_changed() );
		}
		return wp_cache_get_multiple( $keys, $this->get_cache_group() );
	}


	/**
	 * Store the entries under their keys, salted when supported.
	 *
	 * @param array<string, array<int>> $entries - Ids keyed by entry key.
	 *
	 * @return void
	 */
	protected function store_entries( array $entries ): void {
		if ( \function_exists( 'wp_cache_set_multiple_salted' ) ) {
			wp_cache_set_multiple_salted( $entries, $this->get_cache_group(), $this->get_last_changed() );
			return;
		}
		wp_cache_set_multiple( $entries, $this->get_cache_group() );
	}


	/**
	 * Get the time this group was last cleared.
	 *
	 * @return string
	 */
	protected function get_last_changed(): string {
		return wp_cache_get_last_changed( static::CACHE_GROUP . ':' . ADVANCED_SIDEBAR_MENU_BASIC_VERSION );
	}


	/**
	 * Unique cache key for the query arguments.
	 *
	 * @param string               $prefix     - Prefix of the entry keys.
	 * @param array<string, mixed> $query_args - Arguments passed to `get_posts`.
	 *
	 * @return string
	 */
	protected function get_key( string $prefix, array $query_args ): string {
		unset( $query_args['walker'], $query_args[ List_Pages::ARGS_KEY ] );
		return $prefix . ':' . \md5( maybe_serialize( $query_args ) );
	}
}
