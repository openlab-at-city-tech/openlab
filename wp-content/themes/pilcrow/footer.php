<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package Pilcrow
 * @since Pilcrow 1.0
 */
?>

		</div><!-- #content-box -->

		<div id="footer" role="contentinfo">
			<div id="colophon">

				<?php
					/* A sidebar in the footer? Yep. You can can customize
					 * your footer with two columns of widgets.
					 */
					get_sidebar( 'footer' );
				?>

				<div id="site-info">
					<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a> &middot; <?php bloginfo( 'description' ); ?>
				</div><!-- #site-info -->

				<div id="site-generator">
					<a href="http://wordpress.org/" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'pilcrow' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'pilcrow' ), 'WordPress' ); ?></a>
					&middot;
					<?php printf( __( 'Theme: %1$s by %2$s.', 'pilcrow' ), 'Pilcrow', '<a href="http://automattic.com/" rel="designer">Automattic</a>' ); ?>
				</div><!-- #site-generator -->

			</div><!-- #colophon -->
		</div><!-- #footer -->
	</div><!-- #page .blog -->
</div><!-- #container -->

<?php
do_action( 'pilcrow_after' );

/* Always have wp_footer() just before the closing </body>
 * tag of your theme, or you will break many plugins, which
 * generally use this hook to reference JavaScript files.
 */
wp_footer();
?>
</body>
</html>
