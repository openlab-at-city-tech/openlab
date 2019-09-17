<?php
/**
 * Conditional collection.
 *
 * @since 3.0.0
 */

class Fixedtoc_Conditions {
	/**
	 * Has data to TOC.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function has_toc() {
		global $FTOC_HAS_DATA;
		if ( isset( $FTOC_HAS_DATA ) && $FTOC_HAS_DATA ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Is TOC to display in the page.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function toc_page() {
		// Check if is_singular()
		$post_types = fixedtoc_get_val( 'general_post_types' );
		
		if ( empty( $post_types ) ) {
			return false;
		}
		
		if ( ! is_singular( $post_types ) ) {
			return false;
		}
		
		// Check if turn on/off TOC
		if ( ! fixedtoc_get_val( 'general_enable' ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Convert title to id
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function convert_title_to_id() {
		return (bool) fixedtoc_get_val( 'general_title_to_id' );
	}
	
	/**
	 * Determine if show in the post.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function in_post() {
		if ( self::in_widget() ) {
			return false;
		}
		
		return (bool) fixedtoc_get_val( 'contents_display_in_post' );
	}
	
	/**
	 * Float in the post.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function float_in_post() {
		if ( ! self::in_post() ) {
			return false;
		}
		
		if ( 'none' != fixedtoc_get_val( 'contents_float_in_post' ) ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Display the Fixed TOC in widget
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function in_widget() {
		return fixedtoc_get_val( 'general_in_widget' ) && is_active_widget( false, false, 'fixedtoc' );
	}
	
	/**
	 * Fixed widget
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function fixed_widget() {
		if ( ! self::in_widget() ) {
			return false;
		}

		return (bool) fixedtoc_get_val( 'widget_fixed' );
	}

	/**
	 * Nested list
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function nested_list() {
		return (bool) fixedtoc_get_val( 'contents_list_nested' );
	}
	
	/**
	 * Strong first level list item
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function strong_first_list() {
		if ( ! self::nested_list() ) {
			return false;
		}
		
		return (bool) fixedtoc_get_val( 'contents_list_strong_1st' );
	}
	
	/**
	 * Expand the 1st level list
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function expand_1st_list() {
		if ( self::accordion_list() || ! self::show_colexp_icon() ) {
			return false;
		}
		return (bool) ( 'expand_1st' == fixedtoc_get_val( 'contents_list_colexp_init_state' ) );
	}
	
	/**
	 * Collapse/expand sub list
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function colexp_list() {
		if ( ! self::nested_list() ) {
			return false;
		}
		
		return (bool) fixedtoc_get_val( 'contents_list_colexp' );
	}
	
	/**
	 * Showing collapse/expand icons.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function show_colexp_icon() {
		if ( ! self::colexp_list() ) {
			return false;
		}		
		
		return (bool) fixedtoc_get_val( 'contents_list_sub_icon' );
	}
	
	/**
	 * Accordion list
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function accordion_list() {
		if ( ! self::colexp_list() ) {
			return false;
		}
		
		if ( ! self::show_colexp_icon() ) {
			return true;
		}
		
		return (bool) fixedtoc_get_val( 'contents_list_accordion' );
	}

	/**
	 * Clicking anywhere expect the container to minimize.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function quick_min() {
		return in_array( 'quick', (array) fixedtoc_get_val( 'general_shortcut' ) );
	}
	
	/**
	 * Pressing the 'esc' keyboard to minimize.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function esc_min() {
		return in_array( 'esc', (array) fixedtoc_get_val( 'general_shortcut' ) );
	}
	
	/**
	 * Pressing the 'enter' keyboard to maximize.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public static function enter_max() {
		return in_array( 'enter', (array) fixedtoc_get_val( 'general_shortcut' ) );
	}
	
	/**
	 * Collapsing contents in initiation.
	 *
	 * @since 3.1.4
	 * @access public
	 *
	 * @return bool
	 */
	public static function contents_collapse_init() {
		return (bool) fixedtoc_get_val( 'contents_col_exp_init' );
	}
	
}


/** ------------------------------------------------------------------------------------------------------------
 * Determine if it is true by $tag.
 *
 * @since 3.0.0
 *
 * @param string $tag
 * @param mixed $args
 * @return bool
 */
function fixedtoc_is_true( $tag, $args = false ) {
	if ( ! method_exists( 'Fixedtoc_Conditions', $tag ) ) {
		wp_die( "Error! Not $tag method!" );
	}
	
	if ( method_exists( 'Fixedtoc_Conditions', $tag ) ) {
		if ( $args ) {
			return (bool) Fixedtoc_Conditions::$tag( $args );
		} else {
			return (bool) Fixedtoc_Conditions::$tag();
		} 
	}
	
	return false;
}