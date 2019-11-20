<?php
/**
 * Left sidebar containing widget area, only shown when using Three Columns page template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package gillian
 */

if ( ! is_active_sidebar( 'sidebar-2' ) ) {
	return;
}
?>

<aside id="three-columns-sidebar" class="widget-area sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Three columns sidebar', 'gillian' ); ?>">
	<?php dynamic_sidebar( 'sidebar-2' ); ?>
</aside><!-- #three-columns-sidebar -->
