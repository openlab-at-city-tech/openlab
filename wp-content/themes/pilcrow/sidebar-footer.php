<?php
/**
 * The Footer widget areas.
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */

/* The footer widget area is triggered if any of the areas
 * have widgets. So let's check that first.
 *
 * If none of the sidebars have widgets, then let's bail early.
 */
if ( ! is_active_sidebar( 'sidebar-4' ) && ! is_active_sidebar( 'sidebar-5' ) )
	return;

// If we get this far, we have widgets. Let's do this.
?>

<div id="footer-widget-area" role="complementary">

	<?php if ( is_active_sidebar( 'sidebar-4' ) ) : ?>
	<div id="first" class="widget-area">
		<ul class="xoxo sidebar-list">
			<?php dynamic_sidebar( 'sidebar-4' ); ?>
		</ul>
	</div><!-- #first .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-5' ) ) : ?>
	<div id="second" class="widget-area">
		<ul class="xoxo sidebar-list">
			<?php dynamic_sidebar( 'sidebar-5' ); ?>
		</ul>
	</div><!-- #second .widget-area -->
	<?php endif; ?>

</div><!-- #footer-widget-area -->
