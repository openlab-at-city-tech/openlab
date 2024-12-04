<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Koyel
 */
$fb_url = get_theme_mod('fb_url');
$tw_url = get_theme_mod('tw_url');
$link_url = get_theme_mod('link_url');
$instagram_url = get_theme_mod('instagram_url');
?>
<footer class="footer-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="copyright">
                    <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'koyel-blog' ) ); ?>">
                        <?php
                        /* translators: %s: CMS name, i.e. WordPress. */
                        printf( esc_html__( 'Proudly powered by %s', 'koyel-blog' ), 'WordPress' );
                        ?>
                    </a>
                    <p><?php
                        /* translators: 1: Theme name, 2: Theme author. */
                        printf( esc_html__( 'Theme: %1$s by %2$s.', 'koyel-blog' ), 'koyel blog', 'ashathemes' );
                        ?>    
                     </p>
                </div>
            </div>
            <div class="col-lg-6">
                <ul class="social">
                    <li><?php esc_html_e('Follow Us','koyel'); ?></li>
                    <li><a href="<?php echo esc_url($fb_url); ?>"><i class="fa fa-facebook-f"></i></a></li>
                    <li><a href="<?php echo esc_url($tw_url); ?>"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="<?php echo esc_url($link_url); ?>"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="<?php echo esc_url($instagram_url); ?>"><i class="fa fa-instagram"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
