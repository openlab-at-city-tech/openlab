<?php
/**
 * WARNING: This file is part of the core Genesis framework. DO NOT edit
 * this file under any circumstances. Please do all modifications
 * in the form of a child theme.
 *
 * This file handles pages, but only exists for the sake of
 * child theme forward cchoompatibility.
 *
 * @package Genesis
 */
  //add the about-page sidebar to just the about page and any child about page
  global $wp_query;
  $post = $wp_query->post;
  $postID = $post->ID;
  $parent = $post->post_parent;
  if ($postID == "49" || $parent == "49")
  {
add_action('genesis_before_sidebar_widget_area', 'cuny_about_menu');
function cuny_about_menu() { 
  $args = array(
				'theme_location' => 'aboutmenu',
				'container' => 'div',
                'container_id' => 'about-menu',
				'menu_class' => 'sidbar-nav'
			);

	wp_nav_menu( $args );
}
  }

genesis();