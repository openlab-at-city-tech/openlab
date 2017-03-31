<?php
/*
Plugin Name: Require Post Category
Plugin URI: http://www.warpconduit.net/wordpress-plugins/require-post-category/
Description: Require users to choose a post category before saving a draft or publishing.
Version: 1.0.7
Author: Josh Hartman
Author URI: http://www.warpconduit.net
License: GPL2
Text Domain: require-post-category
*/
/*
    Copyright 2013 Josh Hartman
    
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('plugins_loaded', 'rpc_load_translation_files');
add_action('admin_enqueue_scripts', 'rpc_admin_enqueue_scripts_func');

function rpc_load_translation_files(){
	load_plugin_textdomain( 'require-post-category', false, basename(dirname( __FILE__ )) . '/languages');
}

function rpc_admin_enqueue_scripts_func($hook){
	if(!in_array($hook, array('post.php', 'post-new.php'))) {
		return;
	}
	global $post_type;
	if($post_type=='post'){
		wp_enqueue_script('jquery-rpc', plugin_dir_url( __FILE__ ) . 'require-post-category.js', array('jquery'), false, true);
		$script_data = array(
			'message' => __('Please select a category before publishing this post.', 'require-post-category')
		);
		wp_localize_script('jquery-rpc', 'require_post_category', $script_data);
	}
}