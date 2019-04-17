<?php get_header(); ?>

<div class="page-content">
	<div id="portfolio" class="portfolio-area">
		<?php
		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$tax_query = new WP_Query( array( 'post_type' => 'project', 'posts_per_page' => -1, 'project-category' => $term->slug ) );
		while ( $tax_query->have_posts() ) : $tax_query->the_post();
			get_template_part( 'content-portfolio' );
		endwhile;
		wp_reset_postdata();
		?>
    </div>
</div>

<?php get_footer(); ?>