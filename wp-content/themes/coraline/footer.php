<?php
/**
 * @package Coraline
 * @since Coraline 1.0
 */
?>
	</div><!-- #content-box -->

	<div id="footer" role="contentinfo">
		<?php get_sidebar( 'footer' ); ?>

		<div id="colophon">
			<span class="generator-link"><a href="<?php echo esc_url( __( 'http://wordpress.org/', 'coraline' ) ); ?>" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'coraline' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s.', 'coraline' ), 'WordPress' ); ?></a></span>
			<?php printf( __( 'Theme: %1$s by %2$s.', 'coraline' ), 'Coraline', '<a href="https://wordpress.com/themes/" rel="designer">WordPress.com</a>' ); ?>
		</div><!-- #colophon -->
	</div><!-- #footer -->

</div><!-- #container -->

<?php wp_footer(); ?>
</body>
</html>