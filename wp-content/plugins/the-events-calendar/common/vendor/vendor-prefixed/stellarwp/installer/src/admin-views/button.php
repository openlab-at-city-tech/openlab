<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
/**
 * View: Install button.
 *
 * @since 1.0.0
 *
 * @var string $slug             The resource slug for the install/activation button.
 * @var string $action           The AJAX action to pass to admin-ajax.php.
 * @var string $hook_prefix      The hook prefix for triggered actions.
 * @var array  $button_classes   The button classes.
 * @var string $button_id        The button ID.
 * @var string $button_label     The button label.
 * @var string $ajax_nonce       The AJAX nonce.
 * @var string $redirect_url     The redirect_url for the action after install.
 * @var string $request_action   The button action (`install` or `activate`).
 * @var string $installing_label The `Installing` label.
 * @var string $installed_label  The `Installed` label.
 * @var string $activating_label The `Activating` label.
 * @var string $activated_label  The `Activated` label.
 */
// We include the following use line to ensure that Strauss copies this file.
use TEC\Common\StellarWP\Installer\Installer;
?>
<button
	<?php if ( ! empty( $button_id ) ): ?>id="<?php echo esc_attr( $button_id ); ?>"<?php endif; ?>
	class="<?php echo esc_attr( implode( ' ', $button_classes ) ); ?>"
	data-slug="<?php echo esc_attr( $slug ); ?>"
	data-hook-prefix="<?php echo esc_attr( $hook_prefix ); ?>"
	data-nonce="<?php echo esc_attr( $ajax_nonce ); ?>"
	data-action="<?php echo esc_attr( $action ); ?>"
	data-request-action="<?php echo esc_attr( $request_action ); ?>"
	data-redirect-url="<?php echo esc_attr( $redirect_url ); ?>"
	data-installing-label="<?php echo esc_attr( $installing_label ); ?>"
	data-installed-label="<?php echo esc_attr( $installed_label ); ?>"
	data-activating-label="<?php echo esc_attr( $activating_label ); ?>"
	data-activated-label="<?php echo esc_attr( $activated_label ); ?>"
><?php echo esc_html( $button_label ); ?></button>
