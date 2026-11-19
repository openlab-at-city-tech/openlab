<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Menus\Page;

/**
 * Query and cache the child page ids of a page list.
 *
 * @author OnPoint Plugins
 * @since  9.9.0
 */
class Child_Pages_Query {
	/**
	 * Query var marking the query grouped by parent.
	 */
	protected const GROUP_BY_PARENT = 'advanced-sidebar-menu/child-pages-query/group-by-parent';

	/**
	 * Arguments shared by every query of the list.
	 *
	 * @var array<string, mixed>
	 */
	protected array $args;

	/**
	 * Menu the list belongs to.
	 *
	 * @var Page
	 */
	protected Page $menu;


	/**
	 * Constructor
	 *
	 * @param array<string, mixed> $args - Arguments of the page list.
	 * @param Page                 $menu - Menu the list belongs to.
	 */
	final protected function __construct( array $args, Page $menu ) {
		// WP_Query serializes its query vars into its cache key.
		unset( $args[ List_Pages::ARGS_KEY ] );
		$this->args = $args;
		$this->menu = $menu;
	}


	/**
	 * Retrieve the ids of a page's children from the cache or query.
	 *
	 * @param int $parent_page_id - Page id we are getting children of.
	 *
	 * @return array<int>
	 */
	public function get_child_ids( int $parent_page_id ): array {
		$args = $this->get_child_args( $parent_page_id );
		$args['suppress_filters'] = false;

		$children = Cache::instance()->get_child_pages( $args );
		if ( \is_array( $children ) ) {
			return $children;
		}
		$children = get_posts( $args );

		$children = Utils::instance()->clean_post_ids( $children );
		Cache::instance()->store_child_pages( $args, $children );
		return $children;
	}


	/**
	 * Cache the childless pages as empty using one grouped query, so their child lookups skip the query.
	 *
	 * @param array<int> $page_ids - Page ids to prime.
	 *
	 * @return void
	 */
	public function prime_child_ids( array $page_ids ): void {
		$page_ids = Utils::instance()->clean_post_ids( $page_ids );
		$page_ids = \array_values( \array_filter( $page_ids, function( int $parent_id ): bool {
			return ! $this->menu->is_excluded( $parent_id );
		} ) );
		if ( [] === $page_ids ) {
			return;
		}

		$with_children = $this->get_parents_with_children( $page_ids );
		$childless_parents = \array_values( \array_diff( $page_ids, $with_children ) );
		if ( [] === $childless_parents ) {
			return;
		}

		// Entries may be evicted while the grouped result stays cached.
		Cache::instance()->store_childless_pages( \array_map( function( int $parent_id ): array {
			return $this->get_child_args( $parent_id );
		}, $childless_parents ) );
	}


	/**
	 * Get the `get_posts` arguments for a page's child ids.
	 *
	 * @param int $parent_page_id - Page id we are getting children of.
	 *
	 * @return array<string, mixed>
	 */
	protected function get_child_args( int $parent_page_id ): array {
		$args = $this->args;
		$args['post_parent'] = $parent_page_id;
		$args['fields'] = 'ids';
		$args['suppress_filters'] = false;

		return $args;
	}


	/**
	 * Retrieve the parents with children from the cache or query.
	 *
	 * @phpstan-param list<int> $page_ids
	 *
	 * @param array             $page_ids - Page ids to check for children.
	 *
	 * @return list<int>
	 */
	protected function get_parents_with_children( array $page_ids ): array {
		$args = $this->args;
		$args['post_parent__in'] = $page_ids;
		$args['fields'] = 'id=>parent';
		$args['posts_per_page'] = \count( $page_ids );
		$args['orderby'] = 'none';
		$args['no_found_rows'] = true;
		$args['update_post_meta_cache'] = false;
		$args['update_post_term_cache'] = false;

		$parents = Cache::instance()->get_pages_with_children( $args );
		if ( \is_array( $parents ) ) {
			return $parents;
		}
		$parents = $this->query_parent_ids( $args );

		Cache::instance()->store_pages_with_children( $args, $parents );

		return $parents;
	}


	/**
	 * Query the parents with children, one row per parent.
	 *
	 * @param array<string, mixed> $args - Arguments for `get_posts` using `post_parent__in`.
	 *
	 * @return list<int>
	 */
	protected function query_parent_ids( array $args ): array {
		$group_by_parent = function( string $groupby, \WP_Query $query ): string {
			global $wpdb;
			if ( ! $wpdb instanceof \wpdb || true !== $query->get( self::GROUP_BY_PARENT ) ) {
				return $groupby;
			}

			return $wpdb->posts . '.post_parent';
		};

		// The group by filter only runs with filters.
		$args['suppress_filters'] = false;
		// Marks this query, so nested queries are not grouped.
		$args[ self::GROUP_BY_PARENT ] = true;

		add_filter( 'posts_groupby', $group_by_parent, 10, 2 );

		try {
			$posts = get_posts( $args );
		} finally {
			remove_filter( 'posts_groupby', $group_by_parent );
		}

		return Utils::instance()->clean_post_ids( $posts );
	}


	/**
	 * Child pages query factory.
	 *
	 * @param array<string, mixed> $args - Arguments of the page list.
	 * @param Page                 $menu - Menu the list belongs to.
	 *
	 * @return Child_Pages_Query
	 */
	public static function factory( array $args, Page $menu ): Child_Pages_Query {
		return new static( $args, $menu );
	}
}
