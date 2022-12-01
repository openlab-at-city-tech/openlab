<?php
/**
 * Admin "Welcome" page content component.
 *
 * Features.
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

<div class="welcome__section welcome__section--features" id="welcome-features">

	<h2><?php esc_html_e( 'Theme Features', 'michelle' ); ?></h2>

	<div class="welcome__column">
		<span class="welcome__icon dashicons dashicons-block-default"></span>
		<h3><?php esc_html_e( 'Block Patterns', 'michelle' ); ?></h3>
		<p><?php esc_html_e( 'Create your content faster with integrated pre-designed patterns library.', 'michelle' ); ?></p>
		<p><a href="https://vimeo.com/webmandesigneu/block-patterns" target="_blank" rel="noopener noreferrer"><small><em><?php esc_html_e( 'Watch instructions in new window &rarr;', 'michelle' ); ?></em></small></a></p>
	</div>

	<div class="welcome__column">
		<span class="welcome__icon dashicons dashicons-admin-appearance"></span>
		<h3><?php esc_html_e( 'Block Styles', 'michelle' ); ?></h3>
		<p><?php esc_html_e( 'Change the style of blocks immediately and easily without coding.', 'michelle' ); ?></p>
		<p><a href="https://vimeo.com/webmandesigneu/block-styles" target="_blank" rel="noopener noreferrer"><small><em><?php esc_html_e( 'Watch instructions in new window &rarr;', 'michelle' ); ?></em></small></a></p>
	</div>

	<div class="welcome__column">
		<span class="welcome__icon dashicons dashicons-admin-page"></span>
		<h3><?php esc_html_e( 'Templates', 'michelle' ); ?></h3>
		<p><?php esc_html_e( 'Modify a front-end page or post layout swiftly using templates.', 'michelle' ); ?></p>
		<p><a href="https://vimeo.com/webmandesigneu/templates" target="_blank" rel="noopener noreferrer"><small><em><?php esc_html_e( 'Watch instructions in new window &rarr;', 'michelle' ); ?></em></small></a></p>
	</div>

	<div class="welcome__column">
		<span class="welcome__icon dashicons dashicons-star-filled"></span>
		<h3><?php esc_html_e( 'Featured Posts', 'michelle' ); ?></h3>
		<p><?php esc_html_e( 'Display important news immediately on top of your blog page.', 'michelle' ); ?></p>
		<p>
			<a href="https://vimeo.com/webmandesigneu/featured-posts" target="_blank" rel="noopener noreferrer"><small><em><?php esc_html_e( 'Watch instructions in new window &rarr;', 'michelle' ); ?></em></small></a>
			<br>
			<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[control]=featured_posts_tag' ) ); ?>"><small><em><?php esc_html_e( 'Set featured posts tag now &rarr;', 'michelle' ); ?></em></small></a>
		</p>
	</div>

</div>
