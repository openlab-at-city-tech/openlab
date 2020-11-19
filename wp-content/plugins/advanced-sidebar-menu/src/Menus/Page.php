<?php

namespace Advanced_Sidebar_Menu\Menus;

use Advanced_Sidebar_Menu\Core;
use Advanced_Sidebar_Menu\List_Pages;

/**
 * Page menu.
 *
 * @author OnPoint Plugins
 * @since  7.0.0
 */
class Page extends Menu_Abstract {
	const WIDGET = 'page';

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
		if ( null === $this->post ) {
			if ( is_page() || is_singular() ) {
				$this->post = get_queried_object();
			}
		}

		return $this->post;
	}


	/**
	 * Get key to order the menu items by.
	 *
	 * @return string
	 */
	public function get_order_by() {
		return apply_filters( 'advanced-sidebar-menu/menus/page/order-by', $this->instance[ self::ORDER_BY ], $this->get_current_post(), $this->args, $this->instance, $this );
	}


	/**
	 * Get order of the menu (ASC|DESC).
	 *
	 * @return string
	 */
	public function get_order() {
		return apply_filters( 'advanced-sidebar-menu/menus/page/order', 'ASC', $this->get_current_post(), $this->args, $this->instance, $this );
	}


	/**
	 * Get the id of page which is the top-level parent of
	 * the page we are currently on.
	 *
	 * Returns -1 if we don't have one.
	 *
	 * @return int
	 */
	public function get_top_parent_id() {
		$top_id = - 1;
		$ancestors = get_post_ancestors( $this->get_current_post() );
		if ( ! empty( $ancestors ) ) {
			$top_id = end( $ancestors );
		} elseif ( null !== $this->get_current_post() ) {
			$top_id = $this->get_current_post()->ID;
		}

		return apply_filters( 'advanced-sidebar-menu/menus/page/top-parent', $top_id, $this->args, $this->instance, $this );
	}


	/**
	 * Is this menu displayed?
	 *
	 * @return bool
	 */
	public function is_displayed() {
		$display = false;
		$post_type = $this->get_post_type();
		if ( is_page() || ( is_single() && $post_type === $this->get_current_post()->post_type ) ) {
			// If we are on the correct post type.
			if ( get_post_type( $this->get_top_parent_id() ) === $post_type ) {
				// If we have children.
				if ( $this->has_pages() ) {
					$display = true;
					// No children + not excluded + include parent +include childless parent.
				} elseif ( $this->checked( self::INCLUDE_CHILDLESS_PARENT ) && $this->checked( self::INCLUDE_PARENT ) && ! $this->is_excluded( $this->get_top_parent_id() ) ) {
					$display = true;
				}
			}
		}

		return apply_filters( 'advanced-sidebar-menu/menus/page/is-displayed', $display, $this->args, $this->instance, $this );
	}


	/**
	 * Do we have child pages at all on this menu?
	 *
	 * Return false if all we have is the top parent page
	 * Return true if we have at least a second level
	 *
	 * @return bool
	 */
	public function has_pages() {
		$list_pages = List_Pages::factory( $this );
		$children = $list_pages->get_child_pages( $this->get_top_parent_id(), true );

		return ! empty( $children );
	}


	/**
	 * Gets the number of levels ot display when doing 'Always display'
	 *
	 * @return int
	 */
	public function get_levels_to_display() {
		$levels = 100;
		if ( $this->display_all() ) {
			// Subtract 1 level to account for the first level children.
			$levels = $this->instance[ self::LEVELS ] - 1;
		}
		return apply_filters( 'advanced-sidebar-menu/menus/page/levels', $levels, $this->args, $this->instance, $this );
	}


	/**
	 * Get the post type of this menu.
	 *
	 * @return string
	 */
	public function get_post_type() {
		return apply_filters( 'advanced-sidebar-menu/menus/page/post-type', 'page', $this->args, $this->instance, $this );
	}


	/**
	 * Get ids of any pages excluded via widget settings.
	 *
	 * @return array|mixed
	 */
	public function get_excluded_ids() {
		return apply_filters( 'advanced-sidebar-menu/menus/page/excluded', parent::get_excluded_ids(), $this->get_current_post(), $this->args, $this->instance, $this );
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

		$output = require Core::instance()->get_template_part( 'page_list.php' );
		echo apply_filters( 'advanced-sidebar-menu/menus/page/output', $output, $this->get_current_post(), $this->args, $this->instance, $this );

		do_action( 'advanced-sidebar-menu/menus/page/render/after', $this );

		echo $this->args['after_widget'];

		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
