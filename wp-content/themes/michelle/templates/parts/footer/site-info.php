<?php
/**
 * Site info / footer credits area.
 *
 * SVG icon from Genericons Neue.
 * @link  https://github.com/Automattic/genericons-neue/blob/master/svg/collapse.svg
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.8
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div class="site-info-section site-footer-section">
	<div class="site-info-content site-footer-content">
		<?php

		/**
		 * Fires before actual site info container opening tag.
		 *
		 * @since  1.0.0
		 */
		do_action( 'michelle/site_info/before' );

		?>

		<div class="site-info">
			<span class="site-info-item">
				<a href="#top" class="back-to-top">
					<svg class="svg-icon" width="3em" aria-hidden="true" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><polygon points="8,4.6 1.3,11.3 2.7,12.7 8,7.4 13.3,12.7 14.7,11.3 "/></svg>
					<span class="screen-reader-text"><?php esc_html_e( 'Back to top of the page', 'michelle' ); ?></span>
				</a>
			</span>

			<span class="site-info-item">
				&copy; <?php echo date_i18n( 'Y' ); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><strong><?php bloginfo( 'name' ); ?></strong></a>
				<?php the_privacy_policy_link( '<span class="sep"> &bull; </span>' . PHP_EOL ); ?>
				<span class="sep"> &bull; </span>
				<?php

				printf(
					/* translators: 1: linked CMS name (WordPress), 2: theme name. */
					esc_html__( 'Powered by %1$s and %2$s.', 'michelle' ),
					'<a rel="nofollow" href="' . esc_url( __( 'https://wordpress.org/', 'michelle' ) ) . '">WordPress</a>',
					'<a rel="nofollow" href="' . esc_url( wp_get_theme( 'michelle' )->get( 'ThemeURI' ) ) . '">' . wp_get_theme( 'michelle' )->display( 'Name' ) . '</a>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);

				?>
			</span>
		</div>

		<?php

		/**
		 * Fires after actual site info container closing tag.
		 *
		 * @since  1.0.0
		 */
		do_action( 'michelle/site_info/after' );

		?>
	</div>
</div>
