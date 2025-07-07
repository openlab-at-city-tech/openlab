<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Auth\Admin;

use TEC\Common\StellarWP\Uplink\Auth\Authorizer;
use TEC\Common\StellarWP\Uplink\Auth\Nonce;
use TEC\Common\StellarWP\Uplink\Auth\Token\Connector;
use TEC\Common\StellarWP\Uplink\Auth\Token\Exceptions\InvalidTokenException;
use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Notice\Notice_Handler;
use TEC\Common\StellarWP\Uplink\Notice\Notice;
use TEC\Common\StellarWP\Uplink\Resources\Collection;
use TEC\Common\StellarWP\Uplink\Storage\Drivers\Transient_Storage;

/**
 * Handles storing token data after successfully redirecting
 * back from an Origin site that has authorized their license.
 */
final class Connect_Controller {

	public const TOKEN   = 'uplink_token';
	public const LICENSE = 'uplink_license';
	public const SLUG    = 'uplink_slug';
	public const NONCE   = '_uplink_nonce';

	/**
	 * @var Connector
	 */
	private $connector;

	/**
	 * @var Notice_Handler
	 */
	private $notice;

	/**
	 * @var Collection
	 */
	private $collection;

	/**
	 * @var Authorizer
	 */
	private $authorizer;

	/**
	 * @var Nonce
	 */
	private $nonce;

	public function __construct(
		Connector $connector,
		Notice_Handler $notice,
		Collection $collection,
		Authorizer $authorizer,
		Nonce $nonce
	) {
		$this->connector  = $connector;
		$this->notice     = $notice;
		$this->collection = $collection;
		$this->authorizer = $authorizer;
		$this->nonce      = $nonce;
	}

	/**
	 * Store the token data passed back from the Origin site.
	 *
	 * @action stellarwp/uplink/{$prefix}/admin_action_{$slug}
	 *
	 * @throws \RuntimeException
	 */
	public function maybe_store_token_data(): void {
		if ( ! is_admin() || wp_doing_ajax() ) {
			return;
		}

		$args = array_intersect_key( $_GET, [
			self::TOKEN   => true,
			self::NONCE   => true,
			self::LICENSE => true,
			self::SLUG    => true,
		] );

		if ( ! $args ) {
			return;
		}

		if ( ! $this->authorizer->can_auth() ) {
			$this->notice->add( new Notice( Notice::ERROR,
				__( 'Sorry, you do not have permission to connect this site.', 'tribe-common' ),
				true
			) );

			return;
		}


		if ( ! $this->nonce->verify( $args[ self::NONCE ] ?? '' ) ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				// @phpstan-ignore-next-line The file will exist in a WordPress installation.
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			// The Litespeed plugin allows completely disabling transients for some reason...
			if ( Config::get_storage_driver() === Transient_Storage::class && is_plugin_active( 'litespeed-cache/litespeed-cache.php' ) ) {
				$this->notice->add( new Notice( Notice::ERROR,
					sprintf(
						__( 'The Litespeed plugin was detected, ensure "Store Transients" is set to ON and try again. See the <a href="%s" target="_blank">Litespeed documentation</a> for more information.', 'tribe-common' ),
						esc_url( 'https://docs.litespeedtech.com/lscache/lscwp/cache/#store-transients' )
					),
					true
				) );
			}

			$this->notice->add( new Notice( Notice::ERROR,
				__( 'Unable to save token data: nonce verification failed.', 'tribe-common' ),
				true
			) );

			return;
		}

		$slug    = $args[ self::SLUG ] ?? '';
		$plugin  = $this->collection->offsetGet( $slug );

		if ( ! $plugin ) {
			$this->notice->add( new Notice( Notice::ERROR,
				__( 'Plugin or Service slug not found.', 'tribe-common' ),
				true
			) );

			return;
		}

		try {
			if ( ! $this->connector->connect( $args[ self::TOKEN ] ?? '', $plugin ) ) {
				$this->notice->add( new Notice( Notice::ERROR,
					__( 'Error storing token.', 'tribe-common' ),
					true
				) );

				return;
			}
		} catch ( InvalidTokenException $e ) {
			$this->notice->add( new Notice( Notice::ERROR,
				sprintf( '%s.', $e->getMessage() ),
				true
			) );

			return;
		}

		// Store or override an existing license.
		$license = $args[ self::LICENSE ] ?? '';

		if ( $license ) {
			if ( ! $plugin->set_license_key( $license, 'network' ) ) {
				$this->notice->add( new Notice( Notice::ERROR,
					__( 'Error storing license key.', 'tribe-common' ),
				true
				) );

				return;
			}
		}

		$this->notice->add(
			new Notice( Notice::SUCCESS,
				__( 'Connected successfully.', 'tribe-common' ),
				true
			)
		);

		/**
		 * Fires after a plugin has been connected.
		 *
		 * @since 2.2.1
		 *
		 * @param \TEC\Common\StellarWP\Uplink\Resources\Resource $plugin The plugin that was connected.
		 */
		do_action( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/' . $slug . '/connected', $plugin );

		/**
		 * Fires after a plugin has been connected.
		 *
		 * @since 2.2.2
		 *
		 * @param \TEC\Common\StellarWP\Uplink\Resources\Resource $plugin The plugin that was connected.
		 */
		do_action( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/connected', $plugin );
	}
}
