<?php
/*
Plugin Name: wp-api
Plugin URI: http://wordpress.org/extend/plugins/wp-api/
Description: Wordpress api in JSON format
Author: Peyman Aslani
Version: 1.0.3
Author URI: http://www.myappsnippet.com/
*/
//includes
@include_once('admin/admin.php');
@include_once ('includes/get_author.php');
@include_once ('includes/get_tags.php');
@include_once ('includes/get_posts.php');
@include_once ('includes/search_api.php');
@include_once ('includes/comment_api.php');
@include_once ('includes/get_gravatar.php');
//hooks
add_action('admin_menu', 'admin_page_class::add_menu_item');
new get_author();
new get_tags();
new get_posts();
new search_api(); 
new comment_api();
new get_gravatar();
?>