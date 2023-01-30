<?php
/**
 * The template for displaying learning module archives
 *
 * @package BU Learning Blocks
 *
 * @since 0.0.2
 */

get_header();

$bulb_terms = get_terms(
	'bulb-courses',
	array(
		'orderby'    => 'name',
		'order'      => 'ASC',
		'hide_empty' => 1,
	)
); ?>

<article class="bulb-archive-container">
<h1 class="bulb-page-title">Lesson Index:</h1>

<?php

foreach ( $bulb_terms as $term_item ) {
	// Define the query.
	$args  = array(
		'paged'          => $paged,
		'posts_per_page' => -1,
		'post_type'      => 'bulb-learning-module',
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'bulb-courses'   => $term_item->slug,
	);
	$query = new WP_Query( $args );
	$name  = $term_item->name;

	echo '<h2 class="bulb-term-heading">' . esc_html( $name ) . '</h2>';

	echo '<ul class="bulb-list">';
	while ( $query->have_posts() ) :
		$query->the_post();
		?>
	<li class="bulb-list-item" id="post-<?php the_ID(); ?>">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</li>
		<?php
	endwhile;

	echo '</ul>';
	echo '<hr>';
	wp_reset_postdata();
}
?>
</article>
<div class="sidebar"></div>
<?php
get_footer();
