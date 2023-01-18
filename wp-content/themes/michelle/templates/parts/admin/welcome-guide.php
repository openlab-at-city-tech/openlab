<?php
/**
 * Admin "Welcome" page content component.
 *
 * Quickstart guide.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.7
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WebManDesign\Michelle\Welcome\Component' ) ) {
	return;
}

?>

<div class="welcome__section welcome__section--guide" id="welcome-guide">

	<h2><?php esc_html_e( 'Quickstart Guide', 'michelle' ); ?></h2>

	<div class="welcome__column welcome__guide--settings">
		<h3>
			<span class="welcome__icon dashicons dashicons-admin-settings"></span>
			<?php esc_html_e( 'Set up', 'michelle' ); ?>
		</h3>
		<p>
			<?php esc_html_e( 'Make sure to tweak "Settings" section of your site.', 'michelle' ); ?>
			<?php esc_html_e( '(Pay attention to image size setup under Settings &rarr; Media.)', 'michelle' ); ?>
		</p>
		<p><a class="button button-hero" href="<?php echo esc_url( admin_url( 'options-general.php' ) ); ?>"><?php esc_html_e( 'Settings', 'michelle' ); ?></a></p>
	</div>

	<div class="welcome__column welcome__guide--customize">
		<h3>
			<span class="welcome__icon dashicons dashicons-admin-customizer"></span>
			<?php esc_html_e( 'Customize', 'michelle' ); ?>
		</h3>
		<p>
			<?php esc_html_e( 'You can customize your website using a live-preview editor.', 'michelle' ); ?>
			<?php esc_html_e( 'Customization changes apply only after you publish them.', 'michelle' ); ?>
		</p>
		<p><a class="button button-hero" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Customize', 'michelle' ); ?></a></p>
	</div>

	<div class="welcome__column welcome__guide--wordpress">
		<h3>
			<span class="welcome__icon dashicons dashicons-wordpress-alt"></span>
			<?php esc_html_e( 'New to WordPress?', 'michelle' ); ?>
		</h3>
		<p><?php esc_html_e( 'If you are new to WordPress check out info in theme documentation.', 'michelle' ); ?></p>
		<p><a href="https://webmandesign.github.io/docs/michelle/#wordpress"><?php esc_html_e( 'Get to know WordPress &rarr;', 'michelle' ); ?></a></p>
	</div>

</div>
