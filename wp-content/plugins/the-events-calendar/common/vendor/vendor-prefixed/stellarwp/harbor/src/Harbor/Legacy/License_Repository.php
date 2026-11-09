<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Legacy;

/**
 * Provides access to legacy licenses reported by all Harbor
 * instances through the cross-instance filter.
 *
 * @since 1.0.0
 */
class License_Repository {

	/**
	 * In-memory cache for the current request cycle.
	 *
	 * @var Legacy_License[]|null
	 */
	private ?array $cache = null;

	/**
	 * Whether `apply_filters( 'lw-harbor/legacy_licenses' )` is currently dispatching.
	 *
	 * Used by `all()` to detect re-entry and short-circuit before `apply_filters`
	 * is called a second time on the same hook within the same call stack.
	 * WordPress does not prevent that on its own: `WP_Hook` would re-fire every
	 * registered callback at a new nesting level until the PHP call stack is
	 * exhausted.
	 *
	 * @var bool
	 */
	private bool $applying_filter = false;

	/**
	 * Get all legacy licenses reported across all Harbor instances.
	 *
	 * @since 1.0.0
	 *
	 * @return Legacy_License[]
	 */
	public function all(): array {
		if ( $this->cache !== null ) {
			return $this->cache;
		}

		/*
		 * Break recursion on `apply_filters( 'lw-harbor/legacy_licenses' )`.
		 *
		 * WordPress does not guard against a hook re-dispatching itself: if a
		 * registered callback (directly or through any function it calls) ends
		 * up triggering the same hook again, `WP_Hook::apply_filters()` happily
		 * re-fires every callback at a new nesting level, blowing the PHP call
		 * stack.
		 *
		 * Returning an empty array on re-entry short-circuits that recursion at
		 * the source. Re-entrant callers are forced to answer from Unified
		 * data alone, which is the only consistent source while dispatch is in
		 * progress.
		 */
		if ( $this->applying_filter ) {
			return [];
		}

		$this->applying_filter = true;

		try {
			$filtered_licenses = (array) apply_filters( 'lw-harbor/legacy_licenses', [] );
		} finally {
			$this->applying_filter = false;
		}

		$licenses = [];

		foreach ( $filtered_licenses as $license ) {
			if ( ! is_array( $license ) ) {
				continue;
			}

			/** @var array<string, mixed> $license */
			$candidate = Legacy_License::from_data( $license );

			// Reject malformed entries that violate the integration contract.
			// Both `key` and `slug` are documented as required; entries missing
			// either are dropped here so they never reach UI, notices, or any
			// downstream consumer.
			if ( ! ( $candidate->key && $candidate->slug ) ) {
				continue;
			}

			$licenses[] = $candidate;
		}

		$this->cache = $licenses;

		return $licenses;
	}

	/**
	 * Get all legacy licenses that are currently active.
	 *
	 * @since 1.0.0
	 *
	 * @return Legacy_License[]
	 */
	public function all_active(): array {
		return array_values(
			array_filter(
				$this->all(),
				static function ( Legacy_License $l ): bool {
					return $l->is_active;
				}
			)
		);
	}

	/**
	 * Get all legacy licenses that are not currently active.
	 *
	 * @since 1.0.0
	 *
	 * @return Legacy_License[]
	 */
	public function all_inactive(): array {
		return array_values(
			array_filter(
				$this->all(),
				static function ( Legacy_License $l ): bool {
					return ! $l->is_active;
				}
			)
		);
	}

	/**
	 * Get a legacy license by resource slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The resource slug.
	 *
	 * @return Legacy_License|null
	 */
	public function find( string $slug ): ?Legacy_License {
		foreach ( $this->all() as $license ) {
			if ( $license->slug === $slug ) {
				return $license;
			}
		}

		return null;
	}

	/**
	 * Whether any legacy licenses exist across all instances.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_any(): bool {
		return count( $this->all() ) > 0;
	}

	/**
	 * Whether any reported legacy license has opted into Harbor's update pipeline.
	 *
	 * Used as the cheap pre-check for update handlers: if no reported entry has
	 * `use_for_updates = true`, there is no work to do regardless of how many
	 * informational legacy entries exist.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function any_used_for_updates(): bool {
		foreach ( $this->all() as $license ) {
			if ( $license->use_for_updates ) {
				return true;
			}
		}

		return false;
	}
}
