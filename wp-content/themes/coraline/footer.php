<?php
/**
 * @package WordPress
 * @subpackage Coraline
 * @since Coraline 1.0
 */
?>
	</div><!-- #content-box -->

	<div id="footer" role="contentinfo">
		<?php get_sidebar( 'footer' ); ?>

		<div id="colophon">
			<?php printf( __( 'Theme: %1$s by %2$s', 'coraline' ), 'Coraline', '<a href="http://automattic.com/" rel="designer">Automattic</a>.' ); ?> <span class="generator-link"><a href="<?php echo esc_url( __( 'http://wordpress.org/', 'coraline' ) ); ?>" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'coraline' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s.', 'coraline' ), 'WordPress' ); ?></a></span>
		</div><!-- #colophon -->
	</div><!-- #footer -->

</div><!-- #container -->

<?php wp_footer(); ?>
</body>
</html>