<?php
/**
 * 404 template
 *
 */
 
  get_header(); ?>
  
  <div id="content" class="hfeed">
  			<div <?php post_class(); ?>>
            	<?php cuny_404(); ?>
            </div><!--hentry-->
  </div><!--#content-->

 <?php get_footer();

function cuny_404() { ?>

	<div class="post hentry">

		<h1 class="entry-title">Page Not Found</h1>
		<div id="openlab-main-content" class="entry-content">
			<p>The page you requested could not be found. Please use the menu above to find the page you need.</p>

		</div><!-- end .entry-content -->

	</div><!-- end .postclass -->

<?php
}