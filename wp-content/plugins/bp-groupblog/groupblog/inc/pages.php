<?php $current = $post->ID; ?>

<?php
/**
 * Filters the arguments for the page 'button' query on the Blog tab.
 *
 * @since 1.9.2
 *
 * @param array $nav_query_args WP_Query args.
 */
$nav_query_args = apply_filters(
	'bp_groupblog_page_tabs_query_args',
	array(
		'post_type' => 'page',
		'showposts' => -1,
		'order'     => 'ASC',
	)
);

$nav_query = new WP_Query( $nav_query_args );
?>

<?php if ( $nav_query->have_posts() ) :?>
	
	<div class="page" id="blog-pages">
	
	    <div id="groupblog-pages">
	      	
				<ul id="groupblog-page-list">	
				
				<?php while ( $nav_query->have_posts() ) : $nav_query->the_post(); ?>
				
		    	<li><a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
		    	
				<?php endwhile; ?>
				
				</ul>
		  
		  </div>
		  
	</div>
	
<?php endif; ?>