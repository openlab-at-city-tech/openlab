<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

get_header();
?>

<article class="bulb-archive-container">

	<h1><strong>Lesson Title:</strong> <?php echo single_term_title(); ?></h1>
	<h3><?php single_term_title(); ?> Lesson Pages:</h3>
	<?php

	$bulb_tax_args = array(
		'paged'          => $paged,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'posts_per_page' => -1,
		'tax_query'      => array( // WPCS: slow query ok.
			array(
				'taxonomy' => 'bulb-courses',
				'field'    => 'slug',
				'terms'    => get_queried_object(),
			),
		),
	);

	$bulb_tax_query = new WP_Query( $bulb_tax_args );

	if ( $bulb_tax_query->have_posts() ) :
		while ( $bulb_tax_query->have_posts() ) :
			$bulb_tax_query->the_post();
			?>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</br>
			</br>
			<?php
		endwhile;
	endif;
	?>
</article>
<div class="sidebar"></div>
<?php

get_footer();
