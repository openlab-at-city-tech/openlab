<?php
/**
 * The template for displaying Taxonomy Archive pages.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.1
 * @author Jeffikus
 * @website http://www.jeffikus.com
 */

get_header(); ?>
<?php 
// Global query variable
global $wp_query; 
// Get taxonomy query object
$taxonomy_archive_query_obj = $wp_query->get_queried_object();
// Taxonomy term name
$taxonomy_term_nice_name = $taxonomy_archive_query_obj->name;
// Taxonomy term id
$term_id = $taxonomy_archive_query_obj->term_taxonomy_id;
// Get taxonomy object
$taxonomy_short_name = $taxonomy_archive_query_obj->taxonomy;
$taxonomy_raw_obj = get_taxonomy($taxonomy_short_name);
// You can alternate between these labels: name, singular_name
$taxonomy_full_name = $taxonomy_raw_obj->labels->name;
?>
		<div id="container">
			<div id="content" role="main">

				<h1 class="page-title"><?php
					printf( __( $taxonomy_full_name.' Archives: %s', 'twentyten' ), '<span>' . $taxonomy_term_nice_name . '</span>' );
				?></h1>
				<?php
					$term = &get_term( $term_id, $taxonomy_short_name );
					$taxonomy_description = $term->description;
					if ( ! empty( $taxonomy_description ) )
						echo '<div class="archive-meta">' . $taxonomy_description . '</div>';

				/* Run the loop for the category page to output the posts.
				 * If you want to overload this in a child theme then include a file
				 * called loop-category.php and that will be used instead.
				 */
				get_template_part( 'loop', 'category' );
				?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
