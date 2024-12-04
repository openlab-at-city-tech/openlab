<?php
/**
 * Footer action
 * @package Koyel
 */

function koyel_footer_style_1(){ ?>
<footer class="footer-area">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="copyright">
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
					<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'koyel' ) ); ?>">
						<?php
						/* translators: %s: CMS name, i.e. WordPress. */
						printf( esc_html__( 'Proudly powered by %s', 'koyel' ), 'WordPress' );
						?>
					</a>
					<p> &copy;
						<?php
						/* translators: 1: Theme name, 2: Theme author. */
						printf( esc_html__( ' %1$s by %2$s.', 'koyel' ), '<a href="'. esc_url ('https://ashathemes.com/index.php/product/koyel-personal-blog-wordpress-theme/').'">Koyel</a>', 'ashathemes' );
						?>
					</p>
				</div>
			</div>
		</div>
	</div>
</footer>
<?php }
add_action('koyel_footer_style','koyel_footer_style_1');