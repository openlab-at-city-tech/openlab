<?php 
/**
 * Theme Font Generator Admin Page Output
 *
 * This file is responsible for generating the admin 
 * page output for the google fonts settings page. It
 * should only be included from within a function.
 * 
 * @package   Easy_Google_Fonts
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.4
 * 
 */

/**
 * Check User Permissions and Theme Support
 * 
 * Checks if the user has the required privileges. It will 
 * die if these conditions are not met.
 *
 * @link http://codex.wordpress.org/Function_Reference/current_user_can 			current_user_can()
 * @link http://codex.wordpress.org/Function_Reference/current_theme_supports		current_theme_supports()
 * @link http://codex.wordpress.org/Function_Reference/wp_die 				    	wp_die()
 *
 * @since 1.0
 * @version  1.0
 * 
 */
	if ( ! current_user_can('edit_theme_options') )
		wp_die( __( 'Cheatin&#8217; uh?' ) );

?>
<div class="wrap">
