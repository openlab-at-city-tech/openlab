<?php
/**
 * Abstract class for admin pages view.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  Panos Lyrakis <panos.lyrakis@incsub.com>
 * @package WPMUDEV_BLC\Core\Interfaces
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Views;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Interfaces\Admin_View;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Utils\Utilities;

/**
 * Class that other admin pages can use/extend.
 */
abstract class Admin_Page extends Base implements Admin_View {
	/**
	 * The unique id that can be used by react. Sent over from Controller.
	 *
	 * @var int $unique_id
	 *
	 * @since 2.0.0
	 */
	public static $unique_id = null;

	/**
	 * The page slug.
	 *
	 * @var string $slug
	 *
	 * @since 2.0.0
	 */
	public static $slug = null;

	/**
	 * Render the output.
	 *
	 * @since 2.0.0
	 * @param array $params .
	 *
	 * @return void Render the output.
	 */
	abstract public function render( $params = array() );

	/**
	 * Prints some header content.
	 */
	public function render_header() {
		?>
		<div class="sui-header">
			<h1 class="sui-header-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<div class="sui-actions-right">
				<?php do_action( 'blc_sui_header_sui_actions_right' ); ?>
				<?php if ( ! apply_filters( 'wpmudev_branding_hide_doc_link', false ) ) : ?>
					<a href="documentation_url" target="_blank" class="sui-button sui-button-ghost">
						<span class="sui-icon-academy" aria-hidden="true"></span>
						<?php esc_html_e( 'View Documentation', 'broken-link-checker' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<div class="sui-floating-notices">
			<div role="alert" id="blc-ajax-update-notice" class="sui-notice" aria-live="assertive"></div>
			<?php do_action( 'blc_sui_floating_notices' ); ?>
		</div>
		<?php
	}

	/**
	 * Renders the page body content.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function render_body() {}

	/**
	 * Render footer.
	 */
	public function render_footer() {
		$hide_footer = false;
		$footer_text = sprintf(
		/* translators: %s - icon */
			esc_html__( 'Made with %s by WPMU DEV', 'broken-link-checker' ),
			'<span aria-hidden="true" class="sui-icon-heart"></span>'
		);

		if ( Utilities::is_site_connected() ) {
			$hide_footer = apply_filters( 'wpmudev_branding_change_footer', false );
			$footer_text = apply_filters( 'wpmudev_branding_footer_text', $footer_text );
		}
		?>
		<div class="sui-footer">
			<?php
			// @codingStandardsIgnoreStart
			echo $footer_text;
			// @codingStandardsIgnoreEnd
			?>
		</div>

		<?php if ( Utilities::is_site_connected() ) : ?>

			<?php if ( ! $hide_footer ) : ?>
				<ul class="sui-footer-nav">
					<li><a href="https://wpmudev.com/hub2/" target="_blank"><?php esc_html_e( 'The Hub', 'broken-link-checker' ); ?></a></li>
					<li><a href="https://wpmudev.com/projects/category/plugins/" target="_blank"><?php esc_html_e( 'Plugins', 'broken-link-checker' ); ?></a></li>
					<li><a href="https://wpmudev.com/roadmap/" target="_blank"><?php esc_html_e( 'Roadmap', 'broken-link-checker' ); ?></a></li>
					<li><a href="https://wpmudev.com/hub2/support/" target="_blank"><?php esc_html_e( 'Support', 'broken-link-checker' ); ?></a></li>
					<li><a href="https://wpmudev.com/docs/wpmu-dev-plugins/broken-link-checker" target="_blank"><?php esc_html_e( 'Docs', 'broken-link-checker' ); ?></a></li>
					<li><a href="https://wpmudev.com/hub2/community/" target="_blank"><?php esc_html_e( 'Community', 'broken-link-checker' ); ?></a></li>
					<li><a href="https://wpmudev.com/terms-of-service/" target="_blank"><?php esc_html_e( 'Terms of Service', 'broken-link-checker' ); ?></a></li>
					<li><a href="https://incsub.com/privacy-policy/" target="_blank"><?php esc_html_e( 'Privacy Policy', 'broken-link-checker' ); ?></a></li>
				</ul>
			<?php endif; ?>

		<?php else : ?>

			<ul class="sui-footer-nav">
				<li><a href="https://profiles.wordpress.org/wpmudev#content-plugins" target="_blank"><?php esc_html_e( 'Free Plugins', 'broken-link-checker' ); ?></a></li>
				<li><a href="https://wpmudev.com/features/" target="_blank"><?php esc_html_e( 'Membership', 'broken-link-checker' ); ?></a></li>
				<li><a href="https://wpmudev.com/roadmap/" target="_blank"><?php esc_html_e( 'Roadmap', 'broken-link-checker' ); ?></a></li>
				<li><a href="https://wpmudev.com/hub2/support/" target="_blank"><?php esc_html_e( 'Support', 'broken-link-checker' ); ?></a></li>
				<li><a href="https://wpmudev.com/docs/wpmu-dev-plugins/broken-link-checker" target="_blank"><?php esc_html_e( 'Docs', 'broken-link-checker' ); ?></a></li>
				<li><a href="https://wpmudev.com/hub-welcome/" target="_blank"><?php esc_html_e( 'The Hub', 'broken-link-checker' ); ?></a></li>
				<li><a href="https://wpmudev.com/terms-of-service/" target="_blank"><?php esc_html_e( 'Terms of Service', 'broken-link-checker' ); ?></a></li>
				<li><a href="https://incsub.com/privacy-policy/" target="_blank"><?php esc_html_e( 'Privacy Policy', 'broken-link-checker' ); ?></a></li>
			</ul>

		<?php endif; ?>

		<?php if ( ! $hide_footer ) : ?>
			<ul class="sui-footer-social">
				<li><a href="https://www.facebook.com/wpmudev" target="_blank">
						<span class="sui-icon-social-facebook" aria-hidden="true"></span>
						<span class="sui-screen-reader-text">Facebook</span>
					</a></li>
				<li><a href="https://twitter.com/wpmudev" target="_blank">
						<span class="sui-icon-social-twitter" aria-hidden="true"></span>
						<span class="sui-screen-reader-text">Twitter</span>
					</a></li>
				<li><a href="https://www.instagram.com/wpmu_dev/" target="_blank">
						<span class="sui-icon-instagram" aria-hidden="true"></span>
						<span class="sui-screen-reader-text">Instagram</span>
					</a></li>
			</ul>
		<?php endif; ?>
		<?php
	}
}
