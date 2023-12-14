<?php
namespace ElementsKit_Lite;

use \ElementsKit_Lite\Modules\Megamenu\Init;
use \ElementsKit_Lite\Libs\Framework\Attr;

class ElementsKit_Menu_Walker extends \Walker_Nav_Menu {

	public $menu_Settings;

	// custom methods
	public function get_item_meta( $menu_item_id ) {
		$meta_key = Init::$menuitem_settings_key;
		$data     = get_post_meta( $menu_item_id, $meta_key, true );
		$data     = (array) json_decode( $data );

		$default = array(
			'menu_id'                         => null,
			'menu_has_child'                  => '',
			'menu_enable'                     => 0,
			'menu_icon'                       => '',
			'menu_icon_color'                 => '',
			'menu_badge_text'                 => '',
			'menu_badge_color'                => '',
			'menu_badge_background'           => '',
			'mobile_submenu_content_type'     => 'builder_content',
			'vertical_megamenu_position_type' => 'relative_position',
			'vertical_menu_width'             => '',
			'megamenu_width_type'             => 'default_width',
			'megamenu_ajax_load'              => 'no',
		);
		return array_merge( $default, $data );
	}

	public function is_megamenu( $menu_slug ) {
		$menu_obj = wp_get_nav_menu_object($menu_slug);
		$menu_slug = ( ( ( gettype( $menu_obj ) == 'object' ) && ( isset( $menu_obj->slug ) ) ) ? $menu_obj->slug : $menu_slug );

		$cache_key = 'elementskit_megamenu_data_' . $menu_slug;
		$cached    = wp_cache_get( $cache_key );
		if ( false !== $cached ) {
			return $cached;
		}

		$return = 0;

		$modules_all    = \ElementsKit_Lite\Config\Module_List::instance()->get_list();
		$modules_active = \ElementsKit_Lite\Libs\Framework\Classes\Utils::instance()->get_option( 'module_list', $modules_all );
		$modules_active = ( ! isset( $modules_active[0] ) ? array_keys( $modules_active ) : $modules_active );

		$settings = Attr::instance()->utils->get_option( Init::$megamenu_settings_key, array() );
		$term     = get_term_by( 'slug', $menu_slug, 'nav_menu' );

		if ( in_array( 'megamenu', $modules_active )
			&& isset( $term->term_id )
			&& isset( $settings[ 'menu_location_' . $term->term_id ] )
			&& $settings[ 'menu_location_' . $term->term_id ]['is_enabled'] == '1' ) {

			$return = 1;
		}

		wp_cache_set( $cache_key, $return );
		return $return;
	}

	public function is_megamenu_item( $item_meta, $menu ) {
		if ( $this->is_megamenu( $menu ) == 1 && $item_meta['menu_enable'] == 1 && class_exists( 'Elementor\Plugin' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class=\"elementskit-dropdown elementskit-submenu-panel\">\n";
	}
	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}
	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent    = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filter the CSS class(es) applied to a menu item's list item element.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		// New
		$class_names     .= ' nav-item';
		$item_meta        = $this->get_item_meta( $item->ID );
		$is_megamenu_item = $this->is_megamenu_item( $item_meta, $args->menu );

		if ( in_array( 'menu-item-has-children', $classes ) || $is_megamenu_item == true ) {
			$class_names .= ' elementskit-dropdown-has ' . $item_meta['vertical_megamenu_position_type'] . ' elementskit-dropdown-menu-' . $item_meta['megamenu_width_type'] . '';
		}

		if ( $is_megamenu_item == true ) {
			$class_names .= ' elementskit-megamenu-has';
		}

		if ( $item_meta['mobile_submenu_content_type'] == 'builder_content' ) {
			$class_names .= ' elementskit-mobile-builder-content';
		}

		if ( in_array( 'current-menu-item', $classes ) ) {
			$class_names .= ' active';
		}

		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
		// New
		$data_attr = '';
		switch ( $item_meta['megamenu_width_type'] ) {
			case 'default_width':
				$data_attr = esc_attr( ' data-vertical-menu=750px' );
				break;

			case 'full_width':
				$data_attr = ' data-vertical-menu=""';
				break;

			case 'custom_width':
				$data_attr = $item_meta['vertical_menu_width'] === '' ? esc_attr( ' data-vertical-menu=750px' ) : esc_attr( ' data-vertical-menu=' . $item_meta['vertical_menu_width'] . '' );
				break;

			default:
				$data_attr = esc_attr( ' data-vertical-menu=750px' );
				break;
		}
		//
		$output        .= $indent . '<li' . $id . $class_names . $data_attr . '>';
		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		$submenu_indicator = '';

		// New
		if ( $depth === 0 ) {
			$atts['class'] = 'ekit-menu-nav-link';
		}
		if ( $depth === 0 && in_array( 'menu-item-has-children', $classes ) ) {
			$atts['class'] .= ' ekit-menu-dropdown-toggle';
		}
		if ( in_array( 'menu-item-has-children', $classes ) || $is_megamenu_item == true ) {
			// Use an if statement to conditionally display the submenu indicator icon
			if(!empty($args->submenu_indicator_icon)) {
				$submenu_indicator .= $args->submenu_indicator_icon;
			} else {
				$submenu_indicator .= '<i class="icon icon-down-arrow1 elementskit-submenu-indicator"></i>';
			}
		}
		if ( $depth > 0 ) {
			$manual_class   = array_values( $classes )[0] . ' ' . 'dropdown-item';
			$atts ['class'] = $manual_class;
		}
		if ( in_array( 'current-menu-item', $item->classes ) ) {
			$atts['class'] .= ' active';
		}

		//
		/**
		 * Filter the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$atts       = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		$item_output = $args->before;
		// New

		//
		$item_output .= '<a' . $attributes . '>';

		if ( $this->is_megamenu( $args->menu ) == 1 ) {
			// add badge text
			if ( $item_meta['menu_badge_text'] != '' ) {
				$badge_style        = 'background:' . $item_meta['menu_badge_background'] . '; color:' . $item_meta['menu_badge_color'];
				$badge_carret_style = 'border-top-color:' . $item_meta['menu_badge_background'];
				$item_output       .= '<span style="' . $badge_style . '" class="ekit-menu-badge">' . $item_meta['menu_badge_text'] . '<i style="' . $badge_carret_style . '" class="ekit-menu-badge-arrow"></i></span>';
			}

			// add menu icon & style
			if ( $item_meta['menu_icon'] != '' ) {
				$icon_style   = 'color:' . $item_meta['menu_icon_color'];
				$item_output .= '<i class="ekit-menu-icon ' . $item_meta['menu_icon'] . '" style="' . $icon_style . '" ></i>';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= $submenu_indicator . '</a>';
		$item_output .= $args->after;
		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of {@see wp_nav_menu()} arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Page data object. Not used.
	 * @param int    $depth  Depth of page. Not Used.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		if ( $depth === 0 ) {
			if ( $this->is_megamenu( $args->menu ) == 1 ) {
				$item_meta = $this->get_item_meta( $item->ID );
				if ( $item_meta['menu_enable'] == 1 && class_exists( 'Elementor\Plugin' ) ) {
					$builder_post_title = 'dynamic-content-megamenu-menuitem' . $item->ID;
					$builder_post       = Utils::get_page_by_title( $builder_post_title, 'elementskit_content' );
					$output            .= '<div class="elementskit-megamenu-panel">';
					if ( $builder_post != null ) {
						$elementor = \Elementor\Plugin::instance();
						$mega_menu_output = $elementor->frontend->get_builder_content_for_display( $builder_post->ID );

						// if ajax load is enable and not elementor editor mode
						if(!empty($item_meta['megamenu_ajax_load']) && $item_meta['megamenu_ajax_load'] == 'yes') {
							$mega_menu_output = sprintf('<div class="megamenu-ajax-load" data-id="%1$s"></div>', $builder_post->ID);
						}

						$output .= $mega_menu_output;
					} else {
						$output .= esc_html__( 'No content found', 'elementskit-lite' );
					}

					$output .= '</div>';
				}
			}
			$output .= "</li>\n";
		}
	}
}
