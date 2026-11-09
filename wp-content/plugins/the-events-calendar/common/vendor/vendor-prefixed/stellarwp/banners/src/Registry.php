<?php declare(strict_types=1);

namespace TEC\Common\Nexcess\Banners;

use OutOfBoundsException;

/**
 * The newest registered version of every banner on the site.
 *
 * Every plugin bundles its own prefixed copy of this package, and all of them share one registry in
 * a global. A registered banner can therefore be another copy's Banner: the same methods under a
 * different class name. Call its methods, and never check its type.
 */
final class Registry {

	/**
	 * $GLOBALS key of the registry: [ banner ID => banner ].
	 */
	private const GLOBAL_KEY = 'nx_banners';

	/**
	 * A banner replaces a registered one with the same ID only when its version is newer. Call on
	 * init, before anything renders.
	 */
	public function register( Banner ...$banners ): void {
		foreach ( $banners as $banner ) {
			$registered = $GLOBALS[ self::GLOBAL_KEY ][ $banner->id() ] ?? null;

			if ( $registered !== null && ! version_compare( $banner->version(), $registered->version(), '>' ) ) {
				continue;
			}

			$GLOBALS[ self::GLOBAL_KEY ][ $banner->id() ] = $banner;
		}
	}

	public function has( string $id ): bool {
		return isset( $GLOBALS[ self::GLOBAL_KEY ][ $id ] );
	}

	/**
	 * A copy of the newest registered version, so field edits stay with the caller.
	 *
	 * No native return type: PHP would reject a banner registered by another copy.
	 *
	 * @return Banner
	 *
	 * @throws OutOfBoundsException When no banner has this ID. Check has() first, or catch it.
	 */
	public function get( string $id ) { // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
		if ( ! $this->has( $id ) ) {
			throw new OutOfBoundsException( sprintf( 'Unknown banner "%s". Register it on init.', esc_html( $id ) ) );
		}

		/** @var Banner $registered Typed for analysis only: it may be another copy's Banner. */
		$registered = $GLOBALS[ self::GLOBAL_KEY ][ $id ];

		// Clone so that one plugin's edits won't leak into another codebase.
		return clone $registered;
	}

}
