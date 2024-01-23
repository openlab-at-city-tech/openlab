<?php
/**
 * Admin "Welcome" page content component.
 *
 * Accessibility tips.
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

<div class="welcome__section welcome__section--a11y" id="welcome-a11y">

	<h2>
		<span class="welcome__icon dashicons dashicons-universal-access-alt"></span>
		<?php esc_html_e( 'Create an Accessible Website', 'michelle' ); ?>
	</h2>

	<p>
		<?php esc_html_e( 'Make your website inclusive and accessible to anyone.', 'michelle' ); ?>
		<?php esc_html_e( 'This theme will help you create a disabilities friendly and barrier-less user experience.', 'michelle' ); ?>
		<?php esc_html_e( 'When building your content, just follow web accessibility best practices.', 'michelle' ); ?>
	</p>

	<div class="welcome__column">
		<h3><?php esc_html_e( 'Simplicity', 'michelle' ); ?></h3>
		<p><?php esc_html_e( 'Keep your text simple. Paragraphs with more than four lines are more difficult to read. Use lists and spacing in your content.', 'michelle' ); ?></p>
	</div>

	<div class="welcome__column">
		<h3><?php esc_html_e( 'Hierarchy', 'michelle' ); ?></h3>
		<p><?php esc_html_e( 'Keep proper headings hierarchy, do not skip heading levels: H2 should be followed by H3 and so on.', 'michelle' ); ?></p>
	</div>

	<div class="welcome__column">
		<h3><?php esc_html_e( 'Images', 'michelle' ); ?></h3>
		<p><?php esc_html_e( 'Adding images to posts and pages makes your content easier to remember.', 'michelle' ); ?></p>
	</div>

	<div class="welcome__column">
		<h3><?php esc_html_e( 'Alternatives', 'michelle' ); ?></h3>
		<p>
			<?php esc_html_e( 'Set alt text to images (do this in the media library or directly in post editor).', 'michelle' ); ?>
			<?php esc_html_e( 'Provide text alternatives for videos and sound.', 'michelle' ); ?>
		</p>
	</div>

	<hr>

	<p><a href="https://webmandesign.github.io/docs/michelle/#accessibility"><?php esc_html_e( 'More info in theme documentation &rarr;', 'michelle' ); ?></a></p>

</div>
