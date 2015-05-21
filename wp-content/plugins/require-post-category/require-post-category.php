<?php
/*
Plugin Name: Require Post Category
Plugin URI: http://www.warpconduit.net/wordpress-plugins/require-post-category/
Description: Require users to choose a post category before saving a draft or publishing.
Version: 1.0.4
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
add_action('admin_footer-post.php', 'rpc_admin_footer_post_func');
add_action('admin_footer-post-new.php', 'rpc_admin_footer_post_func');

function rpc_load_translation_files(){
	load_plugin_textdomain( 'require-post-category', false, basename(dirname( __FILE__ )) . '/languages');
}

function rpc_admin_footer_post_func(){
	global $post_type;
	if($post_type=='post'){
		echo "<script>
jQuery(function($){
	$('#publish, #save-post').click(function(e){
		if($('#taxonomy-category input:checked').length==0){
			alert('" . __('Please select a category before publishing this post.', 'require-post-category') . "');
			e.stopImmediatePropagation();
			return false;
		}else{
			return true;
		}
	});
	var publish_click_events = $('#publish').data('events').click;
	if(publish_click_events){
		if(publish_click_events.length>1){
			publish_click_events.unshift(publish_click_events.pop());
		}
	}
	if($('#save-post').data('events') != null){
		var save_click_events = $('#save-post').data('events').click;
		if(save_click_events){
		  if(save_click_events.length>1){
			  save_click_events.unshift(save_click_events.pop());
		  }
		}
	}
});
</script>";
	}
}