<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ePortfolio
 */

?>
<?php 
$eportfolio_thumb = '';
if (!has_post_thumbnail()) {
    $eportfolio_thumb = 'twp-only-title';
}
$eportfolio_archive_classes = array(
    'twp-gallery-post',
    'twp-overlay-image-hover',
    $eportfolio_thumb,
);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($eportfolio_archive_classes); ?>>
	<?php if (has_post_thumbnail()) {
		$url = '';
	    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'medium_large' );
	    if (!empty($thumb['0'])) {
	    	$url = $thumb['0'];
	    }
	     ?>
	        <a class="post-thumbnail data-bg" href="<?php the_permalink(); ?>"  data-background="<?php echo esc_url($url); ?>">
        		<?php the_post_thumbnail('full'); ?>
        		<span class="twp-post-format-white">
					<?php echo esc_attr(eportfolio_post_format(get_the_ID())); ?>
				</span>
	        </a>
	<?php } ?>

	<div class="twp-desc">
		<div class="twp-categories">
			<?php eportfolio_post_categories(); ?>
		</div>
		<?php the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' ); ?>
		<div class='twp-meta'>
			<?php eportfolio_post_date(); ?>
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
