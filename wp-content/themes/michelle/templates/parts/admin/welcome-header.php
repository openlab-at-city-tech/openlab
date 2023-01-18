<?php
/**
 * Admin "Welcome" page content component.
 *
 * Header.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.7
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WebManDesign\Michelle\Welcome\Component' ) ) {
	return;
}

?>

<div class="welcome__section welcome__header">

	<h1>
		<?php echo wp_get_theme( 'michelle' )->display( 'Name' ); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
		<small><?php echo MICHELLE_THEME_VERSION; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?></small>
	</h1>

	<p class="welcome__intro">
		<?php

		printf(
			/* translators: 1: theme name, 2: theme developer link. */
			esc_html__( 'Congratulations and thank you for choosing %1$s theme by %2$s!', 'michelle' ),
			'<strong>' . wp_get_theme( 'michelle' )->display( 'Name' ) . '</strong>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<a href="' . esc_url( wp_get_theme( 'michelle' )->get( 'AuthorURI' ) ) . '"><strong>WebMan Design</strong></a>'
		);

		?>
		<?php esc_html_e( 'Information on this page introduces the theme and provides useful tips.', 'michelle' ); ?>
	</p>

	<nav class="welcome__nav">
		<ul>
			<li><a href="#welcome-features"><?php esc_html_e( 'Features', 'michelle' ); ?></a></li>
			<li><a href="#welcome-a11y"><?php esc_html_e( 'Accessibility', 'michelle' ); ?></a></li>
			<li><a href="#welcome-guide"><?php esc_html_e( 'Quickstart', 'michelle' ); ?></a></li>
			<li><a href="#welcome-demo"><?php esc_html_e( 'Demo content', 'michelle' ); ?></a></li>
			<li><a href="#welcome-promo"><?php esc_html_e( 'Upgrade', 'michelle' ); ?></a></li>
		</ul>
	</nav>

	<p>
		<a href="https://webmandesign.github.io/docs/michelle/" class="button button-hero button-primary"><?php esc_html_e( 'Documentation &rarr;', 'michelle' ); ?></a>
		<a href="https://support.webmandesign.eu/forums/forum/michelle/" class="button button-hero button-primary"><?php esc_html_e( 'Support Forum &rarr;', 'michelle' ); ?></a>
	</p>

	<p class="welcome__alert welcome__alert--tip">
		<strong class="welcome__badge"><?php echo esc_html_x( 'Tip:', 'Notice, hint.', 'michelle' ); ?></strong>
		<?php echo Welcome\Component::get_info_like(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
	</p>

</div>
