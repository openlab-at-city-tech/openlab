<?php get_header(); ?>
	<?php 
    while (have_posts()) : the_post(); 
    	include(dirname(__FILE__).'/post.php');
    endwhile;
    ?>
<?php get_footer(); ?>