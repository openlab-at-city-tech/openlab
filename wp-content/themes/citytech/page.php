<?php
/**
 * Page template
 *
 */
 
  get_header(); ?>
  
  <div id="content" class="hfeed">
  
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  
  			<div <?php post_class(); ?>>
            	<h1 class="entry-title"><?php the_title(); ?></h1>
                <div class="entry-content"><?php the_content(); ?></div>
            </div><!--hentry-->
  
  <?php endwhile;
  		endif; ?>
  
  </div><!--#content-->
  
  <?php global $wp_query;
  $post = $wp_query->post;
  $postID = $post->ID;
  $parent = $post->post_parent;
  
  //add the about-page sidebar to just the about page and any child about page
  if ($postID == "49" || $parent == "49")
  {
	  echo '<div id="sidebar" class="sidebar widget-area">';
	   
		$args = array(
					  'theme_location' => 'aboutmenu',
					  'container' => 'div',
					  'container_id' => 'about-menu',
					  'menu_class' => 'sidbar-nav'
				  );
	    echo '<h2 class="sidebar-title">About</h2>';
		wp_nav_menu( $args );
	  echo '</div>';
  }
  
  get_footer();