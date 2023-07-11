<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ePortfolio
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('twp-gallery-post'); ?>>
	
	<?php if (has_post_thumbnail()) {
	    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'medium_large' );
	    $url = $thumb['0'];
	    } else {
	        $url = '';
	}
	?>
	<a href="<?php the_permalink(); ?>" class="post-thumbnail data-bg" data-background="<?php echo esc_url($url); ?>">
	</a>
	<div class="twp-desc">
		<div class="twp-categories">
			<?php if (is_archive()) {
				eportfolio_post_categories();
			} ?>

		</div>
		<?php the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' ); ?>
		<?php if (is_archive()) {
			echo "<div class='twp-meta'>";
				eportfolio_post_date();
			echo "</div>";

		} else { 
			echo "<div class='twp-categories'>";
				eportfolio_post_categories();
			echo "</div>";
		}?>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->

