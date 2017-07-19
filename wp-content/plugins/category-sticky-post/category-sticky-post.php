<?php
/**
 * Category Sticky Post
 *
 * Mark a post to be placed at the top of a specified category archive. It's sticky posts specifically for categories.
 *
 * @package   Category_Sticky_post
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 * @license   GPL-2.0+
 * @link      http://tommcfarlin.com/category-sticky-post/
 * @copyright 2013 - 2016 Tom McFarlin
 *
 * @wordpress-plugin
 * Plugin Name: Category Sticky Post
 * Plugin URI: 	https://tommcfarlin.com/category-sticky-post/
 * Description: Mark a post to be placed at the top of a specified category archive. It's sticky posts specifically for categories.
 * Version:     2.10.1
 * Author:      Tom McFarlin
 * Author URI:  https://tommcfarlin.com
 * Text Domain: category-sticky-post
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-category-sticky-post.php' );
add_action( 'plugins_loaded', array( 'Category_Sticky_Post', 'get_instance' ) );
