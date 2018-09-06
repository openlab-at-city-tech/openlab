<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package gillian
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
	
		<aside id="footer-sidebar" class="widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Footer widgets', 'gillian' ); ?>">
			<?php dynamic_sidebar( 'sidebar-3' ); ?>
		</aside>
	
		<div class="site-info">
			<p>&copy;
			<?php echo date_i18n(__('Y','gillian')) ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			
			<span class="sep"></span>
			
			<?php
			if ( function_exists( 'the_privacy_policy_link' ) ) {
				the_privacy_policy_link( '', '
				<span class="sep"></span>' );
			}
			?>
			
			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'gillian' ) ); ?>"><?php printf( esc_html__( 'Powered by %s', 'gillian' ), 'WordPress' ); ?></a>
			
			<span class="sep"></span>
			
			<?php printf( esc_html__( 'Theme: %1$s', 'gillian' ), '<a href="http://wordpress.org/themes/gillian">Gillian</a>' ); ?>
		</div><!-- .site-info -->
		
		<div class="back-to-top">
			<a href="#"><i class="fa fa-chevron-up" aria-hidden="true"><span class="screen-reader-text"><?php esc_html_e('Back to top','gillian'); ?></span></i></a>
		</div>
		
		<div class="clearer"></div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
