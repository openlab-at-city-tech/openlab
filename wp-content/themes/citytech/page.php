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
			);?>
	<h2 class="sidebar-title">About</h2>
	<?php wp_nav_menu( $args );
}//add the help-page sidebar to just the help page and any child help page
  }else if ($postID == "43" || $parent == "43")
  {
    add_action('genesis_before_sidebar_widget_area', 'cuny_help_menu');
    function cuny_help_menu() { 
    $args = array(
				'theme_location' => 'helpmenu',
				'container' => 'div',
                'container_id' => 'help-menu',
				'menu_class' => 'sidbar-nav'
			);
?>
	<h2 class="sidebar-title">Help</h2>
	<?php
	wp_nav_menu( $args );
  }
 }

genesis();