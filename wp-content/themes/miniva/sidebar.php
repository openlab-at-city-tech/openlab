<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Miniva
 */

$miniva_sidebar = apply_filters( 'miniva_sidebar', 'sidebar-1' );
if ( empty( $miniva_sidebar ) ) {
	$miniva_sidebar = 'sidebar-1';
}
if ( ! is_active_sidebar( $miniva_sidebar ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( $miniva_sidebar ); ?>
</aside><!-- #secondary -->
