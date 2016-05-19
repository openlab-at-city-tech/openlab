<?php
/**
 * Template for displaying embedded group events.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?><title><?php wp_title( '|', true, 'right' ); ?></title><?php endif; ?>
	<?php wp_head(); ?>
	<base target="_parent" />

	<style type="text/css">
	body {font-family:sans-serif;}
	h3 {display:none;}
	a {color:inherit;}
	ul.bpeo-upcoming-events {list-style-type:disc; padding-left:10px;}
	#bpeo-ical-download h3 {display:block;}
	#bpeo-ical-download ul {padding-left:0;}
	</style>
</head>

<body <?php body_class(); ?>>

<div id="page">
<?php
// Start the loop.
while ( have_posts() ) : the_post();
?>

	<article <?php post_class(); ?>>

		<div class="entry-content">
			<?php do_action( 'bp_template_content' ); ?>
		</div><!-- .entry-content -->

	</article>

<?php
// End the loop.
endwhile;
?>
</div>

<?php wp_footer(); ?>
</body>
</html>