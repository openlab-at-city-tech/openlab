<?php
/**
 * Throttle the Freemius "You are just one step away — Complete X
 * Activation Now" opt-in nag to once-per-login.
 *
 * The Freemius SDK re-adds that notice on every admin page load while
 * the plugin is registered but not yet opted in / skipped, and it
 * renders without a dismiss control (it's the non-sticky `update-nag`
 * branch in `Freemius::_admin_init_action()`). The Drupal counterpart
 * shows its equivalent once after each login; this class brings the WP
 * build into parity.
 *
 * Mechanism:
 *
 *   - `wp_login` action sets a user-meta flag for the just-authenticated
 *     user. The flag means "show the next opt-in nag once."
 *   - Freemius's `show_admin_notice` filter (SDK 2.2.0+) consults that
 *     flag for the activation nag specifically. If set, the filter
 *     returns true AND deletes the flag, so the next admin page load
 *     suppresses again until the user logs out and back in.
 *
 * Identification of the activation nag is by triple match —
 * `type='update-nag'`, empty title, non-sticky — which uniquely
 * corresponds to the activation message in the current SDK. The two
 * other `update-nag` notices are either sticky with an explicit id
 * (`connect_account`) or carry a 'Heads up' title (addons page), so
 * neither collides.
 *
 * @package Editoria11y
 */

namespace Editoria11y;

defined( 'ABSPATH' ) || exit;

/**
 * Login-scoped throttle for the Freemius opt-in nag.
 */
final class FreemiusOptInNag {

	/**
	 * User-meta key holding the "show the nag on next admin load" flag.
	 *
	 * Value is the login timestamp (UNIX seconds) when set; absence of
	 * the key means "suppress."
	 */
	const META_KEY = 'ed11y_fs_optin_nag_pending';

	/**
	 * Wire the login hook + the Freemius filter against the live SDK
	 * instance.
	 *
	 * @param \Freemius $ed11ycsa Live SDK instance returned by fs_dynamic_init().
	 */
	public static function apply( \Freemius $ed11ycsa ): void {
		add_action( 'wp_login', array( __CLASS__, 'mark_pending_on_login' ), 10, 2 );
		$ed11ycsa->add_filter( 'show_admin_notice', array( __CLASS__, 'filter_show_admin_notice' ), 10, 2 );
	}

	/**
	 * `wp_login` callback: set the pending flag for the user that just
	 * authenticated.
	 *
	 * @param string   $user_login Login name (ignored — kept to match the WP hook signature).
	 * @param \WP_User $user       Authenticated user.
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter) WP hook signature dictates $user_login first; only $user is used.
	 */
	public static function mark_pending_on_login( $user_login, $user ): void {
		if ( ! $user instanceof \WP_User || ! $user->ID ) {
			return;
		}
		update_user_meta( $user->ID, self::META_KEY, time() );
	}

	/**
	 * Freemius `show_admin_notice` filter.
	 *
	 * Lets every notice through unchanged except the activation nag.
	 * For that one, returns true once per login (consuming the flag),
	 * false otherwise.
	 *
	 * @param bool                $show Whether the SDK would otherwise render this notice.
	 * @param array<string,mixed> $msg  Notice descriptor (see FS_Admin_Notice_Manager).
	 * @return bool
	 */
	public static function filter_show_admin_notice( $show, $msg ) {
		if ( ! self::is_activation_nag( $msg ) ) {
			return $show;
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}

		$pending = get_user_meta( $user_id, self::META_KEY, true );
		if ( empty( $pending ) ) {
			return false;
		}

		delete_user_meta( $user_id, self::META_KEY );
		return $show;
	}

	/**
	 * Identify the "You are just one step away" activation nag.
	 *
	 * The SDK adds this with type='update-nag', empty title, and
	 * is_sticky=false (see class-freemius.php around the
	 * `you-are-step-away` string). Other update-nag notices have either
	 * a non-empty title or sticky=true.
	 *
	 * @param array<string,mixed> $msg Notice descriptor.
	 */
	private static function is_activation_nag( array $msg ): bool {
		return 'update-nag' === ( $msg['type'] ?? '' )
			&& '' === (string) ( $msg['title'] ?? '' )
			&& empty( $msg['sticky'] );
	}
}
