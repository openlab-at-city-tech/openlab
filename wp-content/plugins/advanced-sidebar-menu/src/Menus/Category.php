<?php

namespace Advanced_Sidebar_Menu\Menus;

use Advanced_Sidebar_Menu\Core;
use Advanced_Sidebar_Menu\Traits\Memoize;
use Advanced_Sidebar_Menu\Walkers\Category_Walker;
use Advanced_Sidebar_Menu\Widget\Widget;

/**
 * Category menu.
 *
 * @author OnPoint Plugins
 *
 * @phpstan-import-type WIDGET_ARGS from Widget
 *
 * @phpstan-type CATEGORY_SETTINGS array{
 *     'display-posts'?: string,
 *     display_all?: ''|'checked',
 *     exclude: string,
 *     include_childless_parent?: ''|'checked',
 *     include_parent?: ''|'checked',
 *     levels: numeric-string|int,
 *     new_widget: 'widget'|'list',
 *     single: ''|'checked',
 *     taxonomy?: string,
 *     title?: string
 * }
 *
 * @extends Menu_Abstract<CATEGORY_SETTINGS>
 * @implements Menu<CATEGORY_SETTINGS, Category>
 */
class Category extends Menu_Abstract implements Menu {
	use Memoize;

	public const WIDGET = 'category';

	public const DISPLAY_ON_SINGLE     = 'single';
	public const EACH_CATEGORY_DISPLAY = 'new_widget';

	public const EACH_LIST   = 'list';
	public const EACH_WIDGET = 'widget';

	/**
	 * Store current menu instance.
	 *
	 * @var ?Category
	 */
	protected static $current_menu;

	/**
	 * Top_level_term.
	 *
	 * @var ?\WP_Term
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
	public function set_current_top_level_term( \WP_Term $term ): void {
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
			'walker'           => new Category_Walker(),
		];

		if ( null === $level ) {
			return $args;
		}

		switch ( $level ) {
			case static::LEVEL_PARENT:
				$args['hide_empty'] = 0;
				$args['include'] = trim( (string) $this->get_top_parent_id() );
				break;
			case static::LEVEL_DISPLAY_ALL:
				$args['child_of'] = $this->get_top_parent_id();
				$args['depth'] = $this->get_levels_to_display();
				break;
			case static::LEVEL_CHILD:
				if ( null !== $term ) {
					$args['include'] = $term->term_id;
					$args['depth'] = 1;
				}
				break;
			case static::LEVEL_GRANDCHILD:
				if ( null !== $term ) {
					$args['child_of'] = $term->term_id;
					$args['depth'] = $this->get_levels_to_display();
				}
				break;
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/get-list-categories-args', $args, $level, $this );
	}


	/**
	 * Get all ancestor of the current term or terms assigned
	 * to the current post.
	 *
	 * @since 8.8.0
	 *
	 * @return int[]
	 */
	public function get_current_ancestors(): array {
		return $this->once( function() {
			$included = $this->get_included_term_ids();
			$ancestors = [];
			foreach ( $included as $term_id ) {
				$term_ancestors = \array_reverse( get_ancestors( $term_id, $this->get_taxonomy(), 'taxonomy' ) );
				// All the post's assigned categories are considered ancestors.
				if ( ! $this->is_tax() ) {
					$term_ancestors[] = $term_id;
				}
				$ancestors[] = $term_ancestors;
			}

			if ( [] === $ancestors ) {
				return [];
			}

			return \array_merge( ...$ancestors );
		}, __METHOD__, [] );
	}


	/**
	 * Get the current item if available.
	 *
	 * @since 8.8.0
	 *
	 * @return ?\WP_Term
	 */
	public function get_current_term() {
		if ( $this->is_tax() ) {
			$term = get_queried_object();
			if ( $term instanceof \WP_Term ) {
				return $term;
			}
		}
		return null;
	}


	/**
	 * Get the first level child terms.
	 *
	 * @return \WP_Term[]
	 */
	public function get_child_terms(): array {
		$terms = [];
		if ( null !== $this->get_top_parent_id() && ! $this->is_excluded( $this->get_top_parent_id() ) ) {
			$terms = get_terms( \array_filter( [
				'taxonomy' => $this->get_taxonomy(),
				'parent'   => $this->get_top_parent_id(),
				'orderby'  => $this->get_order_by(),
				'order'    => $this->get_order(),
			] ) );
			if ( is_wp_error( $terms ) ) {
				$terms = [];
			}
		}

		return (array) apply_filters( 'advanced-sidebar-menu/menus/category/get-child-terms', \array_filter( $terms ), $this );
	}


	/**
	 * Gets the number of levels to display when using "Always display child categories".
	 *
	 * @return int
	 */
	public function get_levels_to_display() {
		$depth = 3;
		if ( $this->display_all() ) {
			$depth = $this->instance[ static::LEVELS ];
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/levels', $depth, $this->args, $this->instance, $this );
	}


	/**
	 * Get id of the highest level parent item.
	 *
	 * @return ?int
	 */
	public function get_top_parent_id(): ?int {
		if ( null === $this->top_level_term || $this->top_level_term->term_id < 1 ) {
			return null;
		}

		return $this->top_level_term->term_id;
	}


	/**
	 * Get key to order the menu items by.
	 *
	 * 'term_id'|'name'|'count'|'slug'
	 *
	 * @return string
	 */
	public function get_order_by(): string {
		return (string) apply_filters( 'advanced-sidebar-menu/menus/category/order-by', 'name', $this->args, $this->instance, $this );
	}


	/**
	 * Get order of the menu (ASC|DESC).
	 *
	 * @return string
	 */
	public function get_order(): string {
		return (string) apply_filters( 'advanced-sidebar-menu/menus/category/order', 'ASC', $this->args, $this->instance, $this );
	}


	/**
	 * Is this menu displayed?
	 *
	 * @return bool
	 */
	public function is_displayed() {
		$display = false;
		if ( is_singular() ) {
			if ( $this->checked( static::DISPLAY_ON_SINGLE ) ) {
				$display = true;
			}
		} elseif ( $this->is_tax() ) {
			$display = true;
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/is-displayed', $display, $this->args, $this->instance, $this );
	}


	/**
	 * Is this term excluded from this menu?
	 *
	 * @param int|string $id ID of the object.
	 *
	 * @return bool
	 */
	public function is_excluded( $id ): bool {
		$excluded = \in_array( (int) $id, $this->get_excluded_ids(), true );

		return (bool) apply_filters( 'advanced-sidebar-menu/menus/category/is-excluded', $excluded, $id, $this->get_widget_args(), $this->get_widget_instance(), $this );
	}


	/**
	 * Get list of excluded ids from widget settings.
	 *
	 * @return array<int>
	 */
	public function get_excluded_ids(): array {
		return \array_map( '\intval', (array) apply_filters( 'advanced-sidebar-menu/menus/category/excluded', parent::get_excluded_ids(), $this->args, $this->instance, $this ) );
	}


	/**
	 * Render the widget output.
	 *
	 * @example Display singe post categories in a new widget.
	 *          ```html
	 *          <div>
	 *              <ul />
	 *          </div>
	 *          <div>
	 *             <ul />
	 *          </div>
	 *          ```
	 *
	 * @example Display singe post categories in another list.
	 *          ```html
	 *          <div>
	 *              <ul />
	 *              <ul />
	 *          </div>
	 *          ```
	 *
	 * @return void
	 */
	public function render() {
		if ( ! $this->is_displayed() ) {
			return;
		}

		add_filter( 'category_css_class', [ $this, 'add_list_item_classes' ], 11, 2 );

		$menu_open = false;
		$close_menu = false;
		$output = '';

		foreach ( $this->get_top_level_terms() as $i => $_cat ) {
			$this->set_current_top_level_term( $_cat );
			if ( ! $this->is_term_displayed( $_cat ) ) {
				continue;
			}

			if ( ! $menu_open || self::EACH_WIDGET === $this->instance[ self::EACH_CATEGORY_DISPLAY ] ) {
				if ( $i > 0 && isset( $this->args['widget_id'] ) ) {
					$this->args['id_increment'] = '-' . ( $i + 1 );
					echo \str_replace( "id=\"{$this->args['widget_id']}\"", "id=\"{$this->args['widget_id']}{$this->args['id_increment']}\"", $this->args['before_widget'] ); //phpcs:ignore WordPress.Security
				} else {
					echo $this->args['before_widget']; //phpcs:ignore WordPress.Security
				}

				do_action( 'advanced-sidebar-menu/menus/category/render', $this );

				if ( ! $menu_open ) {
					// Must remain in the loop vs the template.
					$this->title();

					$menu_open = true;
					$close_menu = true;
					if ( self::EACH_LIST === $this->instance[ self::EACH_CATEGORY_DISPLAY ] ) {
						$close_menu = false;
					}
				}
			}

			$view = require Core::instance()->get_template_part( 'category_list.php' );

			$output .= apply_filters( 'advanced-sidebar-menu/menus/category/output', $view, $this->args, $this->instance, $this );

			if ( $close_menu ) {
				$this->close_menu( $output );
				$output = '';
			}
		}

		if ( ! $close_menu && $menu_open ) {
			$this->close_menu( $output );
		}
	}


	/**
	 * Get the top-level terms for the current page.
	 * Could be multiple if on a single.
	 * This will be one if on an archive.
	 *
	 * @return \WP_Term[]
	 */
	public function get_top_level_terms() {
		$top_level_term_ids = \array_filter( \array_map( function( $term_id ) {
			$top = $this->get_highest_parent( $term_id );
			return $this->is_excluded( $top ) ? null : $top;
		}, $this->get_included_term_ids() ) );

		$top_level_term_ids = apply_filters( 'advanced-sidebar-menu/menus/category/top-level-term-ids', $top_level_term_ids, $this->args, $this->instance, $this );

		$terms = [];
		if ( ! empty( $top_level_term_ids ) ) {
			$terms = get_terms(
				[
					'include'    => \array_unique( \array_filter( $top_level_term_ids ) ),
					'hide_empty' => false,
					'orderby'    => $this->get_order_by(),
					'order'      => $this->get_order(),
				]
			);

			if ( is_wp_error( $terms ) ) {
				return [];
			}
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
		if ( is_singular() ) {
			$id = get_the_ID();
			if ( false !== $id ) {
				$term_ids = wp_get_post_terms( $id, $this->get_taxonomy(), [ 'fields' => 'ids' ] );
			}
		} elseif ( $this->is_tax() ) {
			$term = get_queried_object();
			if ( $term instanceof \WP_Term ) {
				$term_ids[] = $term->term_id;
			}
		}

		return (array) apply_filters( 'advanced-sidebar-menu/menus/category/included-term-ids', $term_ids, $this->args, $this->instance, $this );
	}


	/**
	 * Get this menu's taxonomy.
	 * Defaults to 'category'.
	 *
	 * @return string
	 */
	public function get_taxonomy() {
		return apply_filters( 'advanced-sidebar-menu/menus/category/taxonomy', 'category', $this->args, $this->instance, $this );
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
			if ( ! $this->checked( static::INCLUDE_PARENT ) || ! $this->checked( static::INCLUDE_CHILDLESS_PARENT ) ) {
				return false;
			}
			$top = $this->get_top_parent_id() ?? - 1;
			if ( $this->is_excluded( $top ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Simplified way to verify if we are on a taxonomy archive.
	 *
	 * @return bool
	 */
	public function is_tax() {
		$taxonomy = $this->get_taxonomy();
		if ( 'category' === $taxonomy ) {
			return is_category();
		}

		return is_tax( $taxonomy );
	}


	/**
	 * Is a term our current top level term?
	 *
	 * @since 8.8.0
	 *
	 * @param \WP_Term $term - Term to check against.
	 *
	 * @return bool
	 */
	public function is_current_top_level_term( \WP_Term $term ): bool {
		if ( null === $this->top_level_term ) {
			return false;
		}
		return $term->term_id === $this->top_level_term->term_id;
	}


	/**
	 * Removes the closing </li> tag from a list item to allow for child menus inside of it
	 *
	 * @param string|false $item - An <li></li> item.
	 *
	 * @return string|false
	 */
	public function openListItem( $item = false ) {
		if ( false === $item ) {
			return false;
		}

		return \substr( \trim( $item ), 0, - 5 );
	}


	/**
	 * Retrieve the highest level term_id based on the given
	 * term's ancestors.
	 *
	 * Track the latest call's ancestors on the class for use
	 * on the single term's archive.
	 *
	 * @param int $term_id - Provided term's id.
	 *
	 * @return int
	 */
	public function get_highest_parent( $term_id ) {
		$ancestors = \array_reverse( get_ancestors( $term_id, $this->get_taxonomy(), 'taxonomy' ) );
		// Use current term if no ancestors available.
		$ancestors[] = $term_id;

		return reset( $ancestors );
	}


	/**
	 * Add various classes to category item to define it among levels
	 * as well as current item state.
	 *
	 * @param string[] $classes  - List of classes added to category list item.
	 * @param \WP_Term $category - Current category.
	 *
	 * @filter category_css_class 11 2
	 *
	 * @return string[]
	 */
	public function add_list_item_classes( array $classes, \WP_Term $category ): array {
		$classes[] = 'menu-item';
		if ( $this->has_children( $category ) ) {
			$classes[] = 'has_children';
		}

		if ( $this->is_current_term( $category ) ) {
			$classes[] = 'current-menu-item';
		} else {
			$current = $this->get_current_term();
			if ( null !== $current ) {
				if ( $current->parent === $category->term_id ) {
					$classes[] = 'current-menu-parent';
					$classes[] = 'current-menu-ancestor';
				} elseif ( $this->is_current_term_ancestor( $category ) ) {
					$classes[] = 'current-menu-ancestor';
				}
			}
		}

		return \array_unique( $classes );
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
	public function is_first_level_term( \WP_Term $term ): bool {
		$return = false;
		if ( ! $this->is_excluded( $term->term_id ) && $term->parent === $this->get_top_parent_id() ) {
			$return = true;
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/is-first-level-term', $return, $term, $this );
	}


	/**
	 * Is a term the currently viewed term?
	 *
	 * @since 8.8.0
	 *
	 * @param \WP_Term $term - Term to check against.
	 *
	 * @return bool
	 */
	public function is_current_term( \WP_Term $term ) {
		if ( ! $this->is_tax() ) {
			return false;
		}
		return get_queried_object_id() === $term->term_id;
	}


	/**
	 * Both are required to be considered an ancestor.
	 * - Is this term an ancestor of the current term?
	 * - Does this term have children?
	 *
	 * @param \WP_Term $term - Category or term.
	 *
	 * @return mixed
	 */
	public function is_current_term_ancestor( \WP_Term $term ) {
		$is_ancestor = false;

		if ( $this->is_current_top_level_term( $term ) || \in_array( $term->term_id, $this->get_current_ancestors(), true ) ) {
			$children = get_term_children( $term->term_id, $this->get_taxonomy() );
			if ( ! is_wp_error( $children ) && \count( $children ) > 0 ) {
				$is_ancestor = true;
			}
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/is-current-term-ancestor', $is_ancestor, $term, $this );
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
		if ( ! is_wp_error( $children ) && \count( $children ) > 0 ) {
			$return = true;
		}

		return apply_filters( 'advanced-sidebar-menu/menus/category/has-children', $return, $term, $this );
	}


	/**
	 * Close the menu after applying final filters.
	 *
	 * Either we wrap each list when display each category in a
	 * new widget, or we wrap all lists when displaying each
	 * category in another list.
	 *
	 * The `advanced-sidebar-menu/menus/category/close-menu` filter lets
	 * us target the inner content of each `<div>`.
	 *
	 * @since   9.0.0
	 *
	 * @param string $output - Contents of the widget `<div>`.
	 *
	 * @return void
	 */
	protected function close_menu( $output ) {
		//phpcs:disable WordPress.Security.EscapeOutput
		echo apply_filters( 'advanced-sidebar-menu/menus/category/close-menu', $output, $this->args, $this->instance, $this );
		do_action( 'advanced-sidebar-menu/menus/category/render/after', $this );
		echo $this->args['after_widget'];
		//phpcs:enable WordPress.Security.EscapeOutput
	}


	/**
	 * Get current menu instance.
	 *
	 * @return Category|null
	 */
	public static function get_current(): ?Category {
		return static::$current_menu;
	}


	/**
	 * Constructs a new instance of this class.
	 *
	 * @phpstan-param CATEGORY_SETTINGS $widget_instance
	 * @phpstan-param WIDGET_ARGS       $widget_args
	 *
	 * @param array                     $widget_instance - Widget settings.
	 * @param array                     $widget_args     - Widget registration args.
	 *
	 * @return Category
	 */
	public static function factory( array $widget_instance, array $widget_args ): Category {
		$menu = new static( $widget_instance, $widget_args );
		static::$current_menu = $menu;

		return $menu;
	}
}
