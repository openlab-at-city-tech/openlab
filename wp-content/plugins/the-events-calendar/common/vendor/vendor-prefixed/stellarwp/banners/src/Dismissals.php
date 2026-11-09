<?php declare(strict_types=1);

namespace TEC\Common\Nexcess\Banners;

/**
 * Per-user banner dismissals, shared by every plugin that bundles a copy of this package.
 *
 * Consumers Strauss-prefix their own copy, so the state is carried entirely by string keys, which
 * prefixing does not rewrite. Do not prefix them per plugin.
 */
final class Dismissals {

	/**
	 * User meta holding when the user dismissed each banner, as [ banner ID => Unix timestamp ].
	 */
	private const META_KEY = 'nx_banners_dismissed';

	/**
	 * The query argument carrying the dismissed banner ID, and the prefix of its nonce action.
	 */
	private const QUERY_ARG = 'nx-banners-dismiss';

	/**
	 * The query argument carrying the nonce. Named so it cannot replace a _wpnonce already in the
	 * URL of the page the banner renders on.
	 */
	private const NONCE_ARG = 'nx-banners-nonce';

	/**
	 * Store the dismissal the current request carries, when it is for this banner and signed.
	 *
	 * Runs from render() rather than admin_init: a client may only call render() mid-page, which
	 * leaves no earlier hook to handle it on, so the link returns to the page and nothing redirects.
	 */
	public static function handleRequest( string $id ): void {
		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( ( $_GET[ self::QUERY_ARG ] ?? null ) !== $id ) {
			return;
		}

		$nonce = $_GET[ self::NONCE_ARG ] ?? null;

		if ( ! is_string( $nonce ) || ! wp_verify_nonce( $nonce, self::action( $id ) ) ) {
			return;
		}

		$dismissed = self::dismissed();

		if ( isset( $dismissed[ $id ] ) ) {
			return;
		}

		$dismissed[ $id ] = time();
		update_user_meta( get_current_user_id(), self::META_KEY, $dismissed );
	}

	public static function isDismissed( string $id ): bool {
		return isset( self::dismissed()[ $id ] );
	}

	/**
	 * The nonce-protected link that dismisses this banner for the current user.
	 */
	public static function url( string $id ): string {
		return (string) wp_nonce_url( add_query_arg( self::QUERY_ARG, $id ), self::action( $id ), self::NONCE_ARG );
	}

	/**
	 * @return array<string, int>
	 */
	private static function dismissed(): array {
		$dismissed = get_user_meta( get_current_user_id(), self::META_KEY, true );

		return is_array( $dismissed ) ? $dismissed : [];
	}

	private static function action( string $id ): string {
		return self::QUERY_ARG . ':' . $id;
	}

}
