<?php /* Template Name: Blog Full Width */ ?>

<?php get_header(); ?>

<div class="page-content">
	<?php
	while ( have_posts() ) : the_post();
		get_template_part( 'content-page' );
	endwhile;

	$posts_per_page = get_option( 'posts_per_page' );
	$paged = 1;
	if ( get_query_var( 'paged' ) ) $paged = get_query_var( 'paged' );
	if ( get_query_var( 'page' ) ) $paged = get_query_var( 'page' );

	$blog_query_args = array(
		'post_type' => 'post',
		'paged' => $paged,
		'posts_per_page' => $posts_per_page
	);

	$blog_query = new WP_Query( $blog_query_args );

	if ( $blog_query->have_posts() ) :
		echo '<div id="blog" class="blog-area">';
			while ( $blog_query->have_posts() ) : $blog_query->the_post();
				get_template_part( 'content' );
			endwhile;
		echo '</div>';

		$links = paginate_links( array(
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'total' => $blog_query->max_num_pages
		) );

		if ( $links ) {
			echo '<nav class="posts-pagination" role="navigation">';
				echo $links;
			echo '</nav>';
		}

		wp_reset_postdata();
	else :
		get_template_part( 'content-none' );
	endif;
	?>
</div>

<?php get_footer(); ?>