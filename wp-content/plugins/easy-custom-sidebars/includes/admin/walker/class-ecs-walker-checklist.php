<?php 
/**
 * CLASS: ECS_Walker_Checklist
 * 
 * Create HTML list of sidebar input items. This is 
 * used to generate the markup used to output the 
 * checkbox items in the sidebar on the admin settings
 * page.
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 * @uses Walker_Nav_Menu
 * 
 */
if ( class_exists( 'Walker_Nav_Menu' ) ) {

	class ECS_Walker_Checklist extends Walker_Nav_Menu {
		
		function __construct( $fields = false ) {
			if ( $fields ) {
				$this->db_fields = $fields;
			}
		}

		/**
		 * Start Level 
		 * 
		 * @param  [type]  $output [description]
		 * @param  integer $depth  [description]
		 * @param  array   $args   [description]
		 * @return [type]          [description]
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent  = str_repeat( "\t", $depth );
			$output .= "\n$indent<ul class='children'>\n";
		}

		/**
		 * End Level
		 * 
		 * @param  [type]  $output [description]
		 * @param  integer $depth  [description]
		 * @param  array   $args   [description]
		 * @return [type]          [description]
		 *
		 * @since 1.0.1
		 * @version 1.0.9
		 * 
		 */
		function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent  = str_repeat( "\t", $depth );
			$output .= "\n$indent</ul>";
		}

		/**
		 * @see Walker::start_el()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item Menu item data object.
		 * @param int $depth Depth of menu item. Used for padding.
		 * @param object $args
		 */
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			global $_nav_menu_placeholder;

			$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval($_nav_menu_placeholder) - 1 : -1;
			$possible_object_id    = isset( $item->post_type ) && 'nav_menu_item' == $item->post_type ? $item->object_id : $_nav_menu_placeholder;
			$possible_db_id        = ( ! empty( $item->ID ) ) && ( 0 < $possible_object_id ) ? (int) $item->ID : 0;

			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			/**
			 * Check if a custom item type is defined and modify
			 * the $item->type property accordingly. This allows
			 * us to pass in custom data types in future updates
			 * when required.
			 * 
			 */
			if ( isset( $args->custom_item_type ) ) {
				$item->type = $args->custom_item_type;
			}

			$output .= $indent . '<li>';
			$output .= '<label class="menu-item-title">';
			$output .= '<input type="checkbox" class="menu-item-checkbox';
			
			if ( property_exists( $item, 'front_or_home' ) && $item->front_or_home ) {
				$title   = sprintf( _x( 'Home: %s', 'nav menu front page title', 'easy-custom-sidebars' ), $item->post_title );
				$output .= ' add-to-top';
			}
			
			$output .= '" name="menu-item[' . $possible_object_id . '][menu-item-object-id]" value="'. esc_attr( $item->object_id ) .'" /> ';
			$output .= isset( $title ) ? esc_html( $title ) : esc_html( $item->title );
			$output .= '</label>';

			// Menu item hidden fields		
			$output .= '<input type="hidden" class="menu-item-db-id"      name="menu-item[' . $possible_object_id . '][menu-item-db-id]"      value="' . $possible_db_id . '" />';
			$output .= '<input type="hidden" class="menu-item-object"     name="menu-item[' . $possible_object_id . '][menu-item-object]"     value="'. esc_attr( $item->object ) .'" />';
			$output .= '<input type="hidden" class="menu-item-parent-id"  name="menu-item[' . $possible_object_id . '][menu-item-parent-id]"  value="'. esc_attr( $item->menu_item_parent ) .'" />';
			$output .= '<input type="hidden" class="menu-item-type"       name="menu-item[' . $possible_object_id . '][menu-item-type]"       value="'. esc_attr( $item->type ) .'" />';
			$output .= '<input type="hidden" class="menu-item-title"      name="menu-item[' . $possible_object_id . '][menu-item-title]"      value="'. esc_attr( $item->title ) .'" />';
			$output .= '<input type="hidden" class="menu-item-url"        name="menu-item[' . $possible_object_id . '][menu-item-url]"        value="'. esc_attr( $item->url ) .'" />';
			$output .= '<input type="hidden" class="menu-item-target"     name="menu-item[' . $possible_object_id . '][menu-item-target]"     value="'. esc_attr( $item->target ) .'" />';
			$output .= '<input type="hidden" class="menu-item-attr_title" name="menu-item[' . $possible_object_id . '][menu-item-attr_title]" value="'. esc_attr( $item->attr_title ) .'" />';
			$output .= '<input type="hidden" class="menu-item-classes"    name="menu-item[' . $possible_object_id . '][menu-item-classes]"    value="'. esc_attr( implode( ' ', $item->classes ) ) .'" />';
			$output .= '<input type="hidden" class="menu-item-xfn"        name="menu-item[' . $possible_object_id . '][menu-item-xfn]"        value="'. esc_attr( $item->xfn ) .'" />';
		}
	} // END ECS_Walker_Checklist

} //endif
