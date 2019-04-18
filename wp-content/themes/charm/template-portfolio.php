<?php /* Template Name: Portfolio */ ?>

<?php get_header(); ?>

<div class="page-content">
	<?php
	while ( have_posts() ) : the_post();
		get_template_part( 'content-page' );
	endwhile;

	$cats = get_post_meta( $post->ID, 'rain_exclude_categories', false );
	$projects_per_page = get_theme_mod( 'rain_projects_per_page', '9' );
	$paged = 1;
	if ( get_query_var( 'paged' ) ) $paged = get_query_var( 'paged' );
	if ( get_query_var( 'page' ) ) $paged = get_query_var( 'page' );

	$terms = get_terms( 'project-category', array( 'exclude' => $cats ) );

	if ( ! get_post_meta( $post->ID, 'rain_hide_filter', true ) ) {
		if ( $terms ) {
			echo '<div class="filter-area">';
				echo '<span class="active" data-filter="*">' . __( 'All', 'themerain' ) . '</span>';
				foreach ( $terms as $term ) {
					echo '<span data-filter=".project-category-' . $term->slug . '">' . $term->name . '</span>';
				}
			echo '</div>';
		}
	}

	$portfolio_query_args = array(
		'post_type' => 'project',
		'paged' => $paged,
		'tax_query' => array(
			array(
				'taxonomy' => 'project-category',
				'field' => 'id',
				'terms' => $cats,
				'operator' => 'NOT IN'
			)
		),
		'posts_per_page' => $projects_per_page
	);

	$portfolio_query = new WP_Query( $portfolio_query_args );

	if ( $portfolio_query->have_posts() ) :
		echo '<div id="portfolio" class="portfolio-area">';
			while ( $portfolio_query->have_posts() ) : $portfolio_query->the_post();
				get_template_part( 'content-portfolio' );
			endwhile;
		echo '</div>';
		echo '<div id="next-projects">'; next_posts_link( '', $portfolio_query->max_num_pages ); echo '</div>';
		wp_reset_postdata();
	else :
		get_template_part( 'content-none' );
	endif;
	?>
</div>

<?php get_footer(); ?>