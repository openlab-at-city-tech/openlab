<?php

namespace Advanced_Sidebar_Menu\Menus;

use Advanced_Sidebar_Menu\Core;

/**
 * Category menu.
 *
 * @author OnPoint Plugins
 * @since  7.0.0
 */
class Category extends Menu_Abstract {
	const WIDGET = 'category';

	const DISPLAY_ON_SINGLE     = 'single';
	const EACH_CATEGORY_DISPLAY = 'new_widget';

	/**
	 * Parents and grandparents fo current term.
	 *
	 * @var array
	 */
	public $ancestors = [];

	/**
	 * Top_level_term.
	 *
	 * @var \WP_Term
	 */
	public $top_level_term;


	/**
	 * If we are on a post we could potentially have more than one
	 * top-level term, so we end up calling this more than once.
	 *
	 * @param \WP_Term $term - The highest level term.
	 *
	 * @return void
	 */
	public function set_current_top_level_term( \WP_Term $term ) {
		$this->top_level_term = $term;
	}


	/**
	 * Return the list of args for wp_list_categories()
	 *
	 * @param string   $level - level of menu, so we have full control of updates.
	 * @param \WP_Term $term  - Term to use for this level.
	 *
	 * @return array
	 */
	public function get_list_categories_args( $level = null, $term = null ) {
		$args = [
			'echo'             => 0,
			'exclude'          => $this->get_excluded_ids(),
			'order'            => $this->get_order(),
			'orderby'          => $this->get_order_by(),
			'show_option_none' => false,
			'taxonomy'         => $this->get_taxonomy(),
			'title_li'         => '',
		];

		if ( null === $level ) {
			return $args;
		}

		switch ( $level ) {
			case self::LEVEL_PARENT:
				$args['hide_empty'] = 0;
				$args['include'] = trim( $this->get_top_parent_id() );
				break;
			case self::LEVEL_DISPLAY_ALL:
				$args['child_of'] = $this->get_top_parent_id();
				$args['depth'] = $this->get_levels_to_display();
				break;
			case self::LEVEL_CHILD:
				$args['include'] = $term->term_id;
				$args['depth'] = 1;
				break;
			case self::LEVEL_GRANDCHILD:
				$args['child_of'] = $term->term_id;
				$args['depth'] = $this->get_levels_to_display();
				break;
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/get-list-categories-args', $args, $level, $this );
	}


	/**
	 * Get the first level child terms.
	 *
	 * $this->set_current_top_level_term() most likely should be called
	 * before this.
	 *
	 * @see \Advanced_Sidebar_Menu\Menus\Category::set_current_top_level_term()
	 *
	 * @return \WP_Term[]
	 */
	public function get_child_terms() {
		$terms = array_filter(
			get_terms(
				[
					'taxonomy' => $this->get_taxonomy(),
					'parent'   => $this->get_top_parent_id(),
					'orderby'  => $this->get_order_by(),
					'order'    => $this->get_order(),
				]
			)
		);
		return apply_filters( 'advanced-sidebar-menu/menus/category/get-child-terms', $terms, $this );
	}


	/**
	 * Gets the number of levels ot display when doing 'Always display'
	 *
	 * @return int
	 */
	public function get_levels_to_display() {
		$depth = 3;
		if ( $this->display_all() ) {
			$depth = $this->instance[ self::LEVELS ];
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/levels', $depth, $this->args, $this->instance, $this );
	}


	/**
	 * Get the top-level terms for the current page.
	 * If on a single this could be multiple.
	 * If on an archive this will be one.
	 *
	 * @return array
	 */
	public function get_top_level_terms() {
		$child_term_ids = $this->get_included_term_ids();
		$top_level_term_ids = [];
		foreach ( $child_term_ids as $_term_id ) {
			$top_level_term_ids[] = $this->get_highest_parent( $_term_id );
		}
		$terms = [];
		if ( ! empty( $top_level_term_ids ) ) {
			$terms = get_terms(
				[
					'include'    => array_unique( array_filter( $top_level_term_ids ) ),
					'hide_empty' => false,
					'orderby'    => $this->get_order_by(),
					'order'      => $this->get_order(),
				]
			);
		}
		if ( is_wp_error( $terms ) ) {
			return [];
		}

		return $terms;
	}


	/**
	 * Get the term ids for either the current term archive,
	 * or the terms attached to the current post
	 *
	 * @return array
	 */
	public function get_included_term_ids() {
		$term_ids = [];
		if ( is_single() ) {
			$term_ids = wp_get_object_terms( get_the_ID(), $this->get_taxonomy(), [ 'fields' => 'ids' ] );
		} elseif ( $this->is_tax() ) {
			$term_ids[] = get_queried_object()->term_id;
		}

		return (array) apply_filters( 'advanced-sidebar-menu/menus/category/included-term-ids', $term_ids, $this->args, $this->instance, $this );
	}


	/**
	 * Get this menu's taxonomy.
	 * Defaults to 'category'.
	 *
	 * @return int
	 */
	public function get_taxonomy() {
		return apply_filters( 'advanced-sidebar-menu/menus/category/taxonomy', 'category', $this->args, $this->instance, $this );
	}


	/**
	 * Get id of the highest level parent item.
	 *
	 * @return int
	 */
	public function get_top_parent_id() {
		if ( empty( $this->top_level_term->term_id ) ) {
			return null;
		}

		return $this->top_level_term->term_id;
	}


	/**
	 * Get key to order the menu items by.
	 *
	 * @return string
	 */
	public function get_order_by() {
		return apply_filters( 'advanced-sidebar-menu/menus/category/order-by', 'name', $this->args, $this->instance, $this );
	}


	/**
	 * Get order of the menu (ASC|DESC).
	 *
	 * @return string
	 */
	public function get_order() {
		return apply_filters( 'advanced-sidebar-menu/menus/category/order', 'ASC', $this->args, $this->instance, $this );
	}


	/**
	 * Is this menu displayed?
	 *
	 * @return bool
	 */
	public function is_displayed() {
		$display = false;
		if ( is_single() ) {
			if ( $this->checked( self::DISPLAY_ON_SINGLE ) ) {
				$display = true;
			}
		} elseif ( $this->is_tax() ) {
			$display = true;
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/is-displayed', $display, $this->args, $this->instance, $this );
	}


	/**
	 * Is this term, and it's children displayed
	 *
	 * 1. If children not empty we always display (or at least let the view handle it).
	 * 2. If children empty and not include a parent we don't display.
	 * 3. If children empty and not include a childless parent we don't display.
	 * 4. If children empty, and the top parent is excluded we don't display.
	 *
	 * @param \WP_Term $term - Current top level term.
	 *
	 * @return bool
	 */
	public function is_term_displayed( $term ) {
		if ( ! $this->has_children( $term ) ) {
			if ( ! $this->checked( self::INCLUDE_PARENT ) || ! $this->checked( self::INCLUDE_CHILDLESS_PARENT ) ) {
				return false;
			}
			if ( $this->is_excluded( $this->get_top_parent_id() ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Simplified way to verify if we are on a taxonomy
	 * archive
	 *
	 * @return bool
	 */
	protected function is_tax() {
		$taxonomy = $this->get_taxonomy();
		if ( 'category' === $taxonomy ) {
			if ( is_category() ) {
				return true;
			}
		} elseif ( is_tax( $taxonomy ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Get list of excluded ids from widget settings.
	 *
	 * @return array
	 */
	public function get_excluded_ids() {
		return apply_filters( 'advanced-sidebar-menu/menus/category/excluded', parent::get_excluded_ids(), $this->args, $this->instance, $this );
	}


	/**
	 * Removes the closing </li> tag from a list item to allow for child menus inside of it
	 *
	 * @param string|bool $item - An <li></li> item.
	 *
	 * @return string|bool
	 */
	public function openListItem( $item = false ) {
		if ( ! $item ) {
			return false;
		}

		return substr( trim( $item ), 0, - 5 );
	}


	/**
	 * Retrieve the highest level term_id based on the given
	 * term's ancestors
	 *
	 * @param int $term_id - Provided term's id.
	 *
	 * @return int
	 */
	public function get_highest_parent( $term_id ) {
		$cat_ancestors = [];
		$cat_ancestors[] = $term_id;

		do {
			$term = get_term( $term_id, $this->get_taxonomy() );
			if ( is_a( $term, \WP_Term::class ) ) {
				$term_id = $term->parent;
				$cat_ancestors[] = $term_id;
			} else {
				$term = false;
			}
		} while ( $term );

		// We only track the last calls ancestors because we only care about these when on a single term archive.
		$this->ancestors = array_reverse( $cat_ancestors );
		return $this->ancestors[1];
	}


	/**
	 * If a category has children add the 'has_children' class
	 * to the list item.
	 *
	 * @param  []       $classes - List of classes added to category list item.
	 * @param \WP_Term $category - Current category.
	 *
	 * @filter category_css_class 11 2
	 *
	 * @return array
	 */
	public function add_has_children_category_class( $classes, $category ) {
		if ( $this->has_children( $category ) ) {
			$classes[] = 'has_children';
		}

		return array_unique( $classes );
	}


	/**
	 * 1. Is this term's parent our top_parent_id?
	 * 2. Is this term not excluded?
	 * 3. Does the filter allow this term?
	 *
	 * When looping through the view via the terms from $this->get_child_terms()
	 * the term->parent conditions will most likely always be true.
	 *
	 * @param \WP_Term $term - Category or term.
	 *
	 * @return bool
	 */
	public function is_first_level_term( \WP_Term $term ) {
		$return = false;
		if ( ! $this->is_excluded( $term->term_id ) && (int) $term->parent === (int) $this->get_top_parent_id() ) {
			$return = true;
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/is-first-level-term', $return, $term, $this );
	}


	/**
	 * Is this term an ancestor of the current term?
	 * Does this term have children?
	 *
	 * @param \WP_Term $term - Category or term.
	 *
	 * @return mixed
	 */
	public function is_current_term_ancestor( \WP_Term $term ) {
		$return = false;
		if ( (int) $term->term_id === (int) $this->top_level_term->term_id || in_array( $term->term_id, $this->ancestors, false ) ) { //phpcs:ignore WordPress.PHP.StrictInArray.FoundNonStrictFalse
			$children = get_term_children( $term->term_id, $this->get_taxonomy() );
			if ( ! empty( $children ) ) {
				$return = true;
			}
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/is-current-term-ancestor', $return, $term, $this );
	}


	/**
	 * Does this term have children?
	 *
	 * @param \WP_Term $term - Current category or term.
	 *
	 * @return mixed
	 */
	public function has_children( \WP_Term $term ) {
		$return = false;
		$children = get_term_children( $term->term_id, $this->get_taxonomy() );
		if ( ! empty( $children ) ) {
			$return = true;
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/has-children', $return, $term, $this );
	}


	/**
	 * Render the widget output
	 *
	 * @return void
	 */
	public function render() {
		if ( ! $this->is_displayed() ) {
			return;
		}

		add_filter( 'category_css_class', [ $this, 'add_has_children_category_class' ], 11, 2 );

		$menu_open = false;
		$close_menu = false;

		foreach ( $this->get_top_level_terms() as $_cat ) {
			$this->set_current_top_level_term( $_cat );
			if ( ! $this->is_term_displayed( $_cat ) ) {
				continue;
			}

			if ( ! $menu_open || ( 'widget' === $this->instance[ self::EACH_CATEGORY_DISPLAY ] ) ) {
				//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $this->args['before_widget'];

				do_action( 'advanced-sidebar-menu/menus/category/render', $this );

				if ( ! $menu_open ) {
					// Must remain in the loop vs the template.
					$this->title();

					$menu_open = true;
					$close_menu = true;
					if ( 'list' === $this->instance[ self::EACH_CATEGORY_DISPLAY ] ) {
						$close_menu = false;
					}
				}
			}

			$output = require Core::instance()->get_template_part( 'category_list.php' );

			echo apply_filters( 'advanced-sidebar-menu/menus/category/output', $output, $this->args, $this->instance, $this );

			if ( $close_menu ) {
				do_action( 'advanced-sidebar-menu/menus/category/render/after', $this );
				echo $this->args['after_widget'];
			}
		}

		if ( ! $close_menu && $menu_open ) {
			do_action( 'advanced-sidebar-menu/menus/category/render/after', $this );
			echo $this->args['after_widget'];
		}
		//phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}

}
