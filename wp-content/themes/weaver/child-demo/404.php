<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * SIMPLE OVERRIDE EXAMPLE FOR CHILD
 *
 * If you wanted to create a child just to overrid the 404 page, you could do that. The 404.php file
 * from the child will replace the 404.php from the parent.
 */

get_header(); ?>

    <div id="container">
	<div id="content" role="main">
	    <div id="post-0" class="post error404 not-found">
		<h1 class="entry-title">Content Not Found on this Site</h1>
		<div class="entry-content">
		    <p>'Sorry, but the page you requested could not be found on this site. Be sure you entered the correct URL.</p>
		    <?php get_search_form(); ?>
		</div><!-- .entry-content -->
	    </div><!-- #post-0 -->

	</div><!-- #content -->
    </div><!-- #container -->
    <script type="text/javascript">
	// focus on search field after it has loaded
	document.getElementById('s') && document.getElementById('s').focus();
    </script>
<?php get_footer(); ?>
