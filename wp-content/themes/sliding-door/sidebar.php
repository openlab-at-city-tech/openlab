<?php /** * The Sidebar containing the primary and secondary widget areas. * 
 * @package Sliding_Door
*  @since Sliding Door 1.0 */ ?>

<div id="sidebar1" class="widget-area" role="complementary"> <ul class="xoxo">

<?php /* When we call the dynamic_sidebar() function, it'll spit out * the widgets for that widget area. If it instead returns false, * then the sidebar simply doesn't exist, so we'll hard-code in * some default sidebar stuff just in case. */ if ( ! dynamic_sidebar( 'primary-widget-area' ) ) : ?>

<li id="search" class="widget-container widget_search"> <?php get_search_form(); ?> </li>

<li id="archives" class="widget-container"> <h3 class="widget-title"><?php _e( 'Archives', 'slidingdoor' ); ?></h3>

<ul> <?php wp_get_archives( 'type=monthly' ); ?> </ul> </li>

<li id="meta" class="widget-container"> <h3 class="widget-title"><?php _e( 'Meta', 'slidingdoor' ); ?></h3> <ul> <?php wp_register(); ?> <li><?php wp_loginout(); ?></li> <?php wp_meta(); ?> </ul> </li>

<?php endif; // end primary widget area ?> </ul> </div><!-- #primary .widget-area -->



<div id="sidebar2" class="widget-area" role="complementary"> <ul class="xoxo"> <?php if ( ! dynamic_sidebar( 'secondary-widget-area' ) ) : ?>


<li class="widget-container"> <h3 class="widget-title"><?php _e( 'Categories', 'slidingdoor' ); ?></h3> <ul> <?php

wp_list_categories(array('title_li' => ''));

?>

</ul> </li>


<?php endif; ?> </ul> </div><!-- #secondary .widget-area -->
