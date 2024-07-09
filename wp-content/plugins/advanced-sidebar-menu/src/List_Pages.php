<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Menus\Page;
use Advanced_Sidebar_Menu\Walkers\Page_Walker;

/**
 * Parse and build the child and grandchild menus
 * Create the opening and closing <ul class="child-sidebar-menu">
 * in the view and this will fill in the guts.
 *
 * Send the args (like wp_list_pages) to the constructor and then
 * display by calling list_pages()
 *
 * @author  OnPoint Plugins <support@onpointplugins.com>
 *
 * @since   5.0.0
 */
class List_Pages {

	/**
	 * The page list
	 *
	 * @var string
	 */
	public $output = '';

	/**
	 * Used when walking the list
	 *
	 * @var null|\WP_Post
	 */
	protected $current_page;

	/**
	 * The top-level parent id according to the menu class
	 *
	 * @var int
	 */
	protected $top_parent_id;

	/**
	 * Passed during construct given to walker and used for queries
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 * Menu class
	 *
	 * @var Page
	 */
	protected $menu;

	/**
	 * Used exclusively to differentiate the cache based on changes
	 * to internal properties.
	 *
	 * @var array{excluded: bool, parent: int}
	 */
	protected $cache = [
		'excluded' => false,
		'parent'   => 0,
	];


	/**
	 * Constructor
	 *
	 * @param Page $menu - The menu class.
	 */
	final protected function __construct( Page $menu ) {
		$this->menu = $menu;
		$this->top_parent_id = $menu->get_top_parent_id();
		$this->current_page = $menu->get_current_post();
		$this->args = $this->parse_args();
		$this->hook();
	}


	/**
	 * Filters
	 *
	 * @return void
	 */
	protected function hook() {
		add_filter( 'page_css_class', [ $this, 'add_list_item_classes' ], 2, 2 );
	}


	/**
	 * Add hierarchical level classes to menu items.
	 *
	 * Follow existing WP core pattern for `wp_list_pages`.
	 *
	 * WP core handles pages, but it doesn't handle custom post types.
	 * We cover pages as well to handle edge cases where a site's theme
	 * changes/removes default classes.
	 *
	 * @param array    $classes - Provided classes for item.
	 * @param \WP_Post $post    - The item.
	 *
	 * @return array
	 */
	public function add_list_item_classes( $classes, \WP_Post $post ) {
		$classes[] = 'menu-item';
		$children = $this->get_child_pages( $post->ID, $post->ID === $this->top_parent_id );
		if ( ! empty( $children ) ) {
			$classes[] = 'has_children';
		}
		if ( ! empty( $this->get_current_page_id() ) ) {
			if ( $this->get_current_page_id() === $post->ID ) {
				$classes[] = 'current_page_item';
				$classes[] = 'current-menu-item';
			} elseif ( null !== $this->current_page && $this->current_page->post_parent === $post->ID ) {
				$classes[] = 'current_page_parent';
				$classes[] = 'current_page_ancestor';
				$classes[] = 'current-menu-parent';
				$classes[] = 'current-menu-ancestor';
			} else {
				$ancestors = get_post_ancestors( $this->get_current_page_id() );
				if ( ! empty( $ancestors ) && \in_array( $post->ID, $ancestors, true ) ) {
					$classes[] = 'current_page_ancestor';
					$classes[] = 'current-menu-ancestor';
				}
			}
		}

		return \array_unique( $classes );
	}


	/**
	 * Return the list of args that have been populated by this class
	 * For use with wp_list_pages()
	 *
	 * @param string $level - Level of menu to retrieve arguments for.
	 *
	 * @return array
	 */
	public function get_args( $level = null ) {
		$args = $this->args;
		switch ( $level ) {
			case Page::LEVEL_PARENT:
				$args['include'] = $this->menu->get_top_parent_id();
				break;
			case Page::LEVEL_DISPLAY_ALL:
				$args['child_of'] = $this->menu->get_top_parent_id();
				$args['depth'] = $this->menu->get_levels_to_display();
				$args['sort_column'] = $this->menu->get_order_by();
				break;
		}

		return apply_filters( 'advanced-sidebar-menu/list-pages/get-args', $args, $level, $this );
	}


	/**
	 * Return menu which was passed to this class
	 *
	 * @return Page
	 */
	public function get_menu() {
		return $this->menu;
	}


	/**
	 * Magic method to allow using a simple echo to get output.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->output;
	}


	/**
	 * Generate the arguments shared by `walk_page_tree` and `get_posts`.
	 *
	 * @return array{
	 *    echo: int,
	 *    exclude: string,
	 *    item_spacing: 'preserve'|'discard',
	 *    levels: int,
	 *    order: 'ASC'|'DESC',
	 *    orderby: string,
	 *    post_type: string,
	 *    posts_per_page: int,
	 *    suppress_filters: bool,
	 *    title_li: string,
	 *    walker: Page_Walker
	 * }
	 */
	protected function parse_args(): array {
		$args = [
			'echo'             => 0,
			'exclude'          => $this->menu->get_excluded_ids(),
			'item_spacing'     => 'preserve',
			'levels'           => $this->menu->get_levels_to_display(),
			'order'            => $this->menu->get_order(),
			'orderby'          => $this->menu->get_order_by(),
			'post_type'        => $this->menu->get_post_type(),
			// phpcs:ignore WordPress.WP.PostsPerPage -- Several cases of menu items higher than 100.
			'posts_per_page'   => 200,
			'suppress_filters' => false,
			'title_li'         => '',
			'walker'           => new Page_Walker(),
		];

		//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core filter for `wp_list_pages` compatibility.
		$args['exclude'] = apply_filters( 'wp_list_pages_excludes', wp_parse_id_list( $args['exclude'] ) );
		// Must be string when used with `wp_list_pages`.
		$args['exclude'] = \implode( ',', $args['exclude'] );

		return apply_filters( 'advanced-sidebar-menu/list-pages/parse-args', $args, $this );
	}


	/**
	 * Return the ID of the current page if we are on a page.
	 * Helper method to prevent a bunch of conditionals throughout.
	 *
	 * @since 7.7.2
	 */
	public function get_current_page_id() {
		if ( null !== $this->current_page ) {
			return $this->current_page->ID;
		}
		return 0;
	}


	/**
	 * List the pages like wp_list_pages.
	 *
	 * @return string
	 */
	public function list_pages() {
		$pages = $this->get_child_pages( $this->top_parent_id, true );
		foreach ( $pages as $page ) {
			$this->output .= walk_page_tree( [ $page ], 1, $this->get_current_page_id(), $this->args );
			$this->output .= $this->list_grandchild_pages( $page->ID, 0 );
			$this->output .= '</li>' . "\n";
		}

		//phpcs:ignore WordPress.NamingConventions -- WP core filter for `wp_list_pages` compatibility.
		return apply_filters( 'wp_list_pages', $this->output, $this->args, $pages );
	}


	/**
	 * List all levels of grandchild pages up to the limit set in the widget.
	 * All grandchild pages will be rendered inside `grandchild-sidebar-menu` uls.
	 *
	 * @param int $parent_page_id - ID of the page we are getting the grandchildren of.
	 * @param int $level          - Level of grandchild pages we are displaying.
	 *
	 * @return string
	 */
	protected function list_grandchild_pages( $parent_page_id, $level ) {
		if ( $level >= (int) $this->args['levels'] ) {
			return '';
		}
		if ( ! $this->menu->display_all() && ! $this->is_current_page_ancestor( $parent_page_id ) ) {
			return '';
		}
		$pages = $this->get_child_pages( $parent_page_id );
		if ( empty( $pages ) ) {
			return '';
		}

		$content = sprintf( '<ul class="grandchild-sidebar-menu level-%s children" data-level="%s">', $level, $level + 2 );

		$inside = '';
		foreach ( $pages as $page ) {
			$inside .= walk_page_tree( [ $page ], 1, $this->get_current_page_id(), $this->args );
			$inside .= $this->list_grandchild_pages( $page->ID, $level + 1 );
			$inside .= "</li>\n";
		}

		return $content . $inside . "</ul>\n";
	}


	/**
	 * Retrieve the child pages of specific page_id
	 *
	 * @param int  $parent_page_id - Page id we are getting children of.
	 * @param bool $is_first_level - Is this the first level of child pages?.
	 *
	 * @return \WP_Post[]
	 */
	public function get_child_pages( $parent_page_id, $is_first_level = false ): array {
		$excluded = $this->menu->is_excluded( $parent_page_id ) || $this->menu->is_excluded( $this->top_parent_id );
		$this->cache = [
			'parent'   => $parent_page_id,
			'excluded' => $excluded,
		];
		$child_pages = Cache::instance()->get_child_pages( $this );
		if ( false === $child_pages ) {
			if ( $excluded ) {
				$child_pages = [];
			} else {
				$args = $this->args;
				$args['post_parent'] = $parent_page_id;
				$args['fields'] = 'ids';
				$args['suppress_filters'] = false;
				$child_pages = get_posts( $args );
			}
			Cache::instance()->add_child_pages( $this, $child_pages );
		}

		$child_pages = \array_map( 'get_post', \array_filter( $child_pages, function( $post_id ) {
			return ! $this->menu->is_excluded( $post_id );
		} ) );

		if ( $is_first_level ) {
			return (array) apply_filters( 'advanced-sidebar-menu/list-pages/first-level-child-pages', $child_pages, $this, $this->menu );
		}

		return (array) apply_filters( 'advanced-sidebar-menu/list-pages/grandchild-pages', $child_pages, $this, $this->menu );
	}


	/**
	 * Is the specified page an ancestor of the current page?
	 *
	 * @param int|string $page_id - Post id to check against.
	 *
	 * @return bool
	 */
	public function is_current_page_ancestor( $page_id ): bool {
		$return = false;
		$current_page_id = $this->get_current_page_id();
		if ( ! empty( $current_page_id ) ) {
			if ( (int) $page_id === $current_page_id ) {
				$return = true;
			} elseif ( null !== $this->current_page && $this->current_page->post_parent === (int) $page_id ) {
				$return = true;
			} elseif ( null !== $this->current_page ) {
				$ancestors = get_post_ancestors( $this->current_page );
				if ( ! empty( $ancestors ) && \in_array( (int) $page_id, $ancestors, true ) ) {
					$return = true;
				}
			}
		}

		return (bool) apply_filters( 'advanced-sidebar-menu/list-pages/is-current-page-ancestor', $return, $current_page_id, $this );
	}


	/**
	 * List Pages Factory
	 *
	 * @param Page $menu - Menu class.
	 *
	 * @return List_Pages
	 */
	public static function factory( Page $menu ): List_Pages {
		return new static( $menu );
	}
}
