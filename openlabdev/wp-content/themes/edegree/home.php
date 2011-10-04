<?php 
global $shortname;

$number_posts = get_option('tbf2_number_posts');

if (!isset($number_posts)) {
	$number_posts = get_option('posts_per_page');
}

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

if (is_active_widget('widget_myFeature')) {
	$category = "showposts=$posts&cat=-".$options['category'];		
} else {
	$category = "showposts=".$posts;		
} 
query_posts($category."&paged=$paged&showposts=$number_posts");
		
get_header();
?>
	
	<?php if (have_posts()) : ?>
		<?php
        $i = 0;
        while (have_posts()) {
            the_post(); 
            include(dirname(__FILE__).'/post.php');
            if ($html = get_option($shortname.'_custom_html_'.$i)) {
                echo "<div class='customhtml'>$html</div>";
            }
        $i++;
        }
		?>
	<?php endif; ?>
    
    <?php if(isset($paged)):?>
    <div class="navigation">
        <p class="alignleft"><?php previous_posts_link('&laquo; Previous Page'); ?></p>
        <p class="alignright"><?php next_posts_link('Next Page &raquo;'); ?></p>
        <div class="recover"></div>
    </div>
    <?php endif; ?>

<?php get_footer(); ?>