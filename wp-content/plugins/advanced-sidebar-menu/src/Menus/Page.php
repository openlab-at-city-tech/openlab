<?php

namespace Advanced_Sidebar_Menu\Menus;

use Advanced_Sidebar_Menu\Core;
use Advanced_Sidebar_Menu\List_Pages;
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

	/**
	 * Store current menu instance.
	 *
	 * @var ?Page
	 */
	protected static $current_menu;

	/**
	 * The current post
	 *
	 * @var null|\WP_Post
	 */
	protected $post;


	/**
	 * Used for setting the current post during unit testing
	 * or special extending.
	 *
	 * @param \WP_Post $post - New current post.
	 *
	 * @return void
	 */
	public function set_current_post( \WP_Post $post ) {
		$this->post = $post;
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
	 * Get key to order the menu items by.
	 *
	 * @notice Must be a string because it is also used by `wp_list_pages`.
	 *
	 * @return string
	 */
	public function get_order_by(): string {
		return (string) apply_filters( 'advanced-sidebar-menu/menus/page/order-by', $this->instance[ static::ORDER_BY ], $this->get_current_post(), $this->args, $this->instance, $this );
	}


	/**
	 * Get order of the menu (ASC|DESC).
	 *
	 * @return string
	 */
	public function get_order(): string {
		return (string) apply_filters( 'advanced-sidebar-menu/menus/page/order', 'ASC', $this->get_current_post(), $this->args, $this->instance, $this );
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
	 * Is this menu displayed?
	 *
	 * @return bool
	 */
	public function is_displayed() {
		$display = false;
		$post_type = $this->get_post_type();
		$current_post_type = $this->get_current_post()->post_type ?? 'invalid';
		if ( is_page() || ( is_singular() && $post_type === $current_post_type ) ) {
			// If we are on the correct post type.
			if ( get_post_type( $this->get_top_parent_id() ) === $post_type ) {
				// If we have children.
				if ( $this->has_pages() ) {
					$display = true;
					// No children + not excluded + include parent +include childless parent.
				} elseif ( $this->checked( static::INCLUDE_CHILDLESS_PARENT ) && $this->checked( static::INCLUDE_PARENT ) && ! $this->is_excluded( $this->get_top_parent_id() ) ) {
					$display = true;
				}
			}
		}

		return apply_filters( 'advanced-sidebar-menu/menus/page/is-displayed', $display, $this->args, $this->instance, $this );
	}


	/**
	 * Is this page excluded from this menu?
	 *
	 * @param int|string $id ID of the object.
	 *
	 * @return bool
	 */
	public function is_excluded( $id ): bool {
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
	 * @return array<int>
	 */
	public function get_excluded_ids(): array {
		return \array_map( '\intval', (array) apply_filters( 'advanced-sidebar-menu/menus/page/excluded', parent::get_excluded_ids(), $this->get_current_post(), $this->args, $this->instance, $this ) );
	}


	/**
	 * Render the widget
	 *
	 * @return void
	 */
	public function render() {
		if ( ! $this->is_displayed() ) {
			return;
		}

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->args['before_widget'];

		do_action( 'advanced-sidebar-menu/menus/page/render', $this );

		$crumb = '';
		$output = require Core::instance()->get_template_part( 'page_list.php', $crumb );
		echo apply_filters( 'advanced-sidebar-menu/menus/page/output', $crumb . $output, $this->get_current_post(), $this->args, $this->instance, $this );

		do_action( 'advanced-sidebar-menu/menus/page/render/after', $this );

		echo $this->args['after_widget'];
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
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
