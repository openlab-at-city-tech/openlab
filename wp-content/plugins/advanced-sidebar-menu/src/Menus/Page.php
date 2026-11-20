<?php

namespace Advanced_Sidebar_Menu\Menus;

use Advanced_Sidebar_Menu\Core;
use Advanced_Sidebar_Menu\List_Pages;
use Advanced_Sidebar_Menu\Widget\Page as PageWidget;
use Advanced_Sidebar_Menu\Widget\Widget;

/**
 * Page menu.
 *
 * @author OnPoint Plugins
 *
 * @phpstan-import-type WIDGET_ARGS from Widget
 *
 * @phpstan-type PAGE_SETTINGS array{
 *      exclude: string,
 *      order_by: 'menu_order'|'post_title'|'post_date',
 *      title?: string,
 *      display_all?: ''|'checked',
 *      include_childless_parent?: ''|'checked',
 *      include_parent?: ''|'checked',
 *      levels?: numeric-string|int,
 *      post_type?: string,
 * }
 *
 * @extends Menu_Abstract<PAGE_SETTINGS>
 * @implements Menu<PAGE_SETTINGS, Page>
 */
class Page extends Menu_Abstract implements Menu {
	public const WIDGET = 'page';

	public const ORDER_ASC  = 'ASC';
	public const ORDER_DESC = 'DESC';

	/**
	 * Store current menu instance.
	 *
	 * @var ?Page
	 */
	protected static $current_menu;

	/**
	 * Top parent of the tree being rendered.
	 *
	 * @var ?int
	 */
	public ?int $forced_top_parent_id = null;

	/**
	 * The current post
	 *
	 * @var null|\WP_Post
	 */
	protected $post;

	/**
	 * Has the title printed during the current render?
	 *
	 * @var bool
	 */
	protected bool $is_title_printed = false;

	/**
	 * Filtered excluded ids for this request.
	 *
	 * @var ?array<int>
	 */
	protected ?array $excluded_ids = null;


	/**
	 * Used for setting the current post during unit testing
	 * or special extending.
	 *
	 * @param \WP_Post $post - New current post.
	 *
	 * @return void
	 */
	public function set_current_post( \WP_Post $post ): void {
		$this->post = $post;
		$this->excluded_ids = null;
	}


	/**
	 * Gets the current queried post unless it
	 * has been set explicitly.
	 *
	 * @return null|\WP_Post
	 */
	public function get_current_post() {
		if ( null !== $this->post ) {
			return $this->post;
		}
		if ( is_page() || is_singular() ) {
			$post = get_queried_object();
			if ( $post instanceof \WP_Post ) {
				$this->post = $post;
			}
		}

		return $this->post;
	}


	/**
	 * Get the key to order the menu items by.
	 *
	 * @notice Must be a string because it is also used by `wp_list_pages`.
	 *
	 * @return string
	 */
	public function get_order_by(): string {
		return (string) apply_filters( 'advanced-sidebar-menu/menus/page/order-by', $this->instance[ static::ORDER_BY ] ?? PageWidget::ORDER_BY_MENU_ORDER, $this->get_current_post(), $this->args, $this->instance, $this );
	}


	/**
	 * Get order of the menu (ASC|DESC).
	 *
	 * @return string
	 */
	public function get_order(): string {
		return (string) apply_filters( 'advanced-sidebar-menu/menus/page/order', self::ORDER_ASC, $this->get_current_post(), $this->args, $this->instance, $this );
	}


	/**
	 * Get the top-level parent page id of
	 * the page we are currently on.
	 *
	 * Returns -1 if we don't have one.
	 *
	 * @return int
	 */
	public function get_top_parent_id(): int {
		if ( \is_int( $this->forced_top_parent_id ) ) {
			return $this->forced_top_parent_id;
		}

		$top_id = - 1;
		$post = $this->get_current_post();
		if ( null !== $post ) {
			$ancestors = get_post_ancestors( $post );
			if ( \count( $ancestors ) > 0 ) {
				$top_id = \end( $ancestors );
			} elseif ( null !== $this->get_current_post() ) {
				$top_id = $this->get_current_post()->ID;
			}
		}

		return (int) apply_filters( 'advanced-sidebar-menu/menus/page/top-parent', $top_id, $this->args, $this->instance, $this );
	}


	/**
	 * Get the top-level page ids, which each render a tree.
	 *
	 * Filtered ids must be real page ids.
	 *
	 * @since 9.9.0
	 *
	 * @return list<int>
	 */
	public function get_top_level_page_ids(): array {
		$default_parents = [ $this->get_top_parent_id() ];
		$page_ids = (array) apply_filters( 'advanced-sidebar-menu/menus/page/top-level-page-ids', $default_parents, $this->args, $this->instance, $this );

		$page_ids = \array_values( \array_unique( \array_map( '\intval', $page_ids ) ) );
		if ( $default_parents === $page_ids ) {
			return $page_ids;
		}

		return \array_values( \array_filter( $page_ids, function( int $page_id ): bool {
			return $page_id > 0;
		} ) );
	}


	/**
	 * Is this menu displayed?
	 *
	 * @return bool
	 */
	public function is_displayed(): bool {
		$display = false;
		$post_type = $this->get_post_type();
		$current_post_type = $this->get_current_post()->post_type ?? 'invalid';
		if ( is_page() || ( is_singular() && $post_type === $current_post_type ) ) {
			// If we are on the correct post type.
			if ( get_post_type( $this->get_top_parent_id() ) === $post_type ) {
				$display = $this->is_tree_displayed();
			}
		}

		return (bool) apply_filters( 'advanced-sidebar-menu/menus/page/is-displayed', $display, $this->args, $this->instance, $this );
	}


	/**
	 * Does the tree of the current top parent have anything to show?
	 *
	 * @since 9.9.0
	 *
	 * @return bool
	 */
	public function is_tree_displayed(): bool {
		// If we have children.
		if ( $this->has_pages() ) {
			return true;
		}

		// No children + not excluded + include parent +include childless parent.
		return $this->checked( static::INCLUDE_CHILDLESS_PARENT ) && $this->checked( static::INCLUDE_PARENT ) && ! $this->is_excluded( $this->get_top_parent_id() );
	}


	/**
	 * Get the top-level page ids whose trees have something to display.
	 *
	 * @since 9.9.0
	 *
	 * @param array<int> $page_ids - Top-level page ids.
	 *
	 * @return list<int>
	 */
	public function get_displayed_tree_ids( array $page_ids ): array {
		List_Pages::factory( $this )->get_query()->prime_child_ids( $page_ids );

		$original_top_parent_id = $this->forced_top_parent_id;
		try {
			$displayed = \array_filter( $page_ids, function( int $page_id ): bool {
				$this->forced_top_parent_id = $page_id;
				return $this->is_tree_displayed();
			} );
			return \array_values( $displayed );
		} finally {
			$this->forced_top_parent_id = $original_top_parent_id;
		}
	}


	/**
	 * Is this page excluded from this menu?
	 *
	 * @param int|string $id ID of the object.
	 *
	 * @return bool
	 */
	public function is_excluded( $id ): bool {
		if ( 0 === (int) $id ) {
			return false;
		}
		$excluded = \in_array( (int) $id, $this->get_excluded_ids(), true );

		return (bool) apply_filters( 'advanced-sidebar-menu/menus/page/is-excluded', $excluded, $id, $this->get_widget_args(), $this->get_widget_instance(), $this );
	}


	/**
	 * Gets the number of levels to display when doing 'Always display'
	 *
	 * @return int
	 */
	public function get_levels_to_display(): int {
		$levels = 100;
		if ( $this->display_all() ) {
			// Subtract 1 level to account for the first level children.
			$levels = ( (int) $this->instance[ static::LEVELS ] ) - 1;
		}
		return (int) apply_filters( 'advanced-sidebar-menu/menus/page/levels', $levels, $this->args, $this->instance, $this );
	}


	/**
	 * Get ids of any pages excluded via widget settings.
	 *
	 * Memoized until `reset_excluded_ids()` is called.
	 *
	 * @return array<int>
	 */
	public function get_excluded_ids(): array {
		if ( \is_array( $this->excluded_ids ) ) {
			return $this->excluded_ids;
		}
		$this->excluded_ids = \array_map( '\intval', (array) apply_filters( 'advanced-sidebar-menu/menus/page/excluded', parent::get_excluded_ids(), $this->get_current_post(), $this->args, $this->instance, $this ) );
		return $this->excluded_ids;
	}


	/**
	 * Echos the title once per render.
	 *
	 * @return void
	 */
	public function title(): void {
		if ( $this->is_title_printed ) {
			return;
		}

		parent::title();
	}


	/**
	 * Render the widget
	 *
	 * One tree per top-level page id inside a single widget.
	 */
	public function render(): void {
		if ( ! $this->is_displayed() ) {
			return;
		}

		$page_ids = $this->get_top_level_page_ids();
		$has_single_top_parent = [ $this->get_top_parent_id() ] === $page_ids;
		$is_open = false;
		$output = '';
		// A lone top parent tree is governed by `is_displayed` alone.
		if ( false === $has_single_top_parent ) {
			$page_ids = $this->get_displayed_tree_ids( $page_ids );
		}

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		foreach ( $page_ids as $page_id ) {
			if ( false === $has_single_top_parent ) {
				$this->forced_top_parent_id = $page_id;
			}
			if ( false === $is_open ) {
				echo $this->args['before_widget'];

				do_action( 'advanced-sidebar-menu/menus/page/render', $this );

				$is_open = true;
			}

			$output .= $this->get_tree_output();
			$this->is_title_printed = true;
		}

		$this->forced_top_parent_id = null;
		$this->is_title_printed = false;

		if ( $is_open ) {
			echo apply_filters( 'advanced-sidebar-menu/menus/page/output', $output, $this->get_current_post(), $this->args, $this->instance, $this );

			do_action( 'advanced-sidebar-menu/menus/page/render/after', $this );

			echo $this->args['after_widget'];
		}
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	/**
	 * Get the output of the template for the current top parent.
	 *
	 * Own scope, so a template's variables can't touch the render loop.
	 *
	 * @since        9.9.0
	 *
	 * @noinspection UsingInclusionReturnValueInspection
	 *
	 * @return string
	 */
	protected function get_tree_output(): string {
		$crumb = '';
		$output = require Core::instance()->get_template_part( 'page_list.php', $crumb );

		return $crumb . $output;
	}


	/**
	 * Do we have child pages at all on this menu?
	 *
	 * Return false if all we have is the top parent page
	 * Return true if we have at least a second level
	 *
	 * @return bool
	 */
	public function has_pages(): bool {
		$list_pages = List_Pages::factory( $this );
		$children = $list_pages->get_child_pages( $this->get_top_parent_id(), true );

		return \count( $children ) > 0;
	}


	/**
	 * Get the post type of this menu.
	 *
	 * @return string
	 */
	public function get_post_type(): string {
		return (string) apply_filters( 'advanced-sidebar-menu/menus/page/post-type', 'page', $this->args, $this->instance, $this );
	}


	/**
	 * Get current menu instance.
	 *
	 * @return Page|null
	 */
	public static function get_current() {
		return static::$current_menu;
	}


	/**
	 * Constructs a new instance of this class.
	 *
	 * @phpstan-param PAGE_SETTINGS $widget_instance
	 * @phpstan-param WIDGET_ARGS   $widget_args
	 *
	 * @param array                 $widget_instance - Widget settings.
	 * @param array                 $widget_args     - Widget registration args.
	 *
	 * @return Page
	 */
	public static function factory( array $widget_instance, array $widget_args ): Page {
		$menu = new static( $widget_instance, $widget_args );
		static::$current_menu = $menu;

		return $menu;
	}
}
