<?php
/**
 * Network-wide two-factor policy.
 */

/**
 * Restricts two-factor authentication to the authenticator app.
 *
 * @param array $providers Provider file paths indexed by provider class name.
 * @return array
 */
function openlab_two_factor_providers( $providers ) {
	return array_intersect_key( $providers, [ 'Two_Factor_Totp' => true ] );
}
add_filter( 'two_factor_providers', 'openlab_two_factor_providers', 9999 );

/**
 * Hides the primary-method picker on the two-factor settings form.
 *
 * With one provider there is nothing to choose between. Two_Factor_Core renders the picker
 * with no hook to skip it, so this hides it wherever the form appears: the profile screens
 * in wp-admin on every site, and the BuddyPress settings page on the main site.
 *
 * @param string $extra_css Additional rules for the current screen.
 */
function openlab_two_factor_settings_styles( $extra_css = '' ) {
	$css = '
		#two-factor-options > hr,
		.two-factor-primary-method-table {
			display: none;
		}
	' . $extra_css;

	wp_register_style( 'openlab-two-factor', false, [], false );
	wp_enqueue_style( 'openlab-two-factor' );
	wp_add_inline_style( 'openlab-two-factor', $css );
}

/**
 * Enqueues the settings styles on the wp-admin profile screens.
 *
 * The plugin's heading and intro text stay; nothing else on that screen introduces the form.
 *
 * @param string $hook_suffix Current admin page.
 */
function openlab_two_factor_admin_styles( $hook_suffix ) {
	if ( in_array( $hook_suffix, [ 'profile.php', 'user-edit.php' ], true ) ) {
		openlab_two_factor_settings_styles();
	}
}
add_action( 'admin_enqueue_scripts', 'openlab_two_factor_admin_styles' );

/**
 * Enqueues the settings styles on the BuddyPress general settings page.
 *
 * The theme's template already introduces the form, so the plugin's "Two-Factor Options"
 * heading and intro text are hidden too. The heading sits outside the fieldset, so it is
 * matched by adjacency; the plugin can put one notice (the revalidate-session warning)
 * between them.
 */
function openlab_two_factor_bp_styles() {
	if ( ! function_exists( 'bp_is_settings_component' ) || ! bp_is_settings_component() || ! bp_is_current_action( 'general' ) ) {
		return;
	}

	openlab_two_factor_settings_styles(
		'
		h2:has(+ #two-factor-options),
		h2:has(+ .notice + #two-factor-options),
		#two-factor-options > p {
			display: none;
		}
		'
	);
}
add_action( 'wp_enqueue_scripts', 'openlab_two_factor_bp_styles' );
