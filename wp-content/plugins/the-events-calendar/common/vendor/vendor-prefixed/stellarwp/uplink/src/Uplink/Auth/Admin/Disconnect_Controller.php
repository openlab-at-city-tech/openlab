<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Auth\Admin;

use TEC\Common\StellarWP\Uplink\API\V3\Auth\Token_Authorizer_Cache_Decorator;
use TEC\Common\StellarWP\Uplink\Auth\Authorizer;
use TEC\Common\StellarWP\Uplink\Auth\Token\Contracts\Token_Manager;
use TEC\Common\StellarWP\Uplink\Auth\Token\Disconnector;
use TEC\Common\StellarWP\Uplink\Notice\Notice_Handler;
use TEC\Common\StellarWP\Uplink\Notice\Notice;
use TEC\Common\StellarWP\Uplink\Resources\Resource;
use TEC\Common\StellarWP\Uplink\Config;

final class Disconnect_Controller {

	public const ARG       = 'uplink_disconnect';
	public const SLUG      = 'uplink_slug';
	public const CACHE_KEY = 'uplink_cache';

	/**
	 * @var Authorizer
	 */
	private $authorizer;

	/**
	 * @var Disconnector
	 */
	private $disconnect;

	/**
	 * @var Notice_Handler
	 */
	private $notice;

	/**
	 * @var Token_Manager
	 */
	private $token_manager;

	/**
	 * @var Token_Authorizer_Cache_Decorator
	 */
	private $cache;

	/**
	 * @param  Authorizer                        $authorizer     The authorizer.
	 * @param  Disconnector                      $disconnect     Disconnects a Token, if the user has the capability.
	 * @param  Token_Manager                     $token_manager  Manages token storage.
	 * @param  Notice_Handler                    $notice         Handles storing and displaying notices.
	 * @param  Token_Authorizer_Cache_Decorator  $cache          The token cache.
	 */
	public function __construct(
		Authorizer $authorizer,
		Disconnector $disconnect,
		Notice_Handler $notice,
		Token_Manager $token_manager,
		Token_Authorizer_Cache_Decorator $cache
	) {
		$this->authorizer    = $authorizer;
		$this->disconnect    = $disconnect;
		$this->notice        = $notice;
		$this->token_manager = $token_manager;
		$this->cache         = $cache;
	}

	/**
	 * Get the disconnect URL to render.
	 *
	 * @param  Resource  $plugin  The plugin/service.
	 *
	 * @return string
	 */
	public function get_url( Resource $plugin ): string {
		$token = $this->token_manager->get( $plugin );

		if ( ! $token ) {
			return '';
		}

		$cache_key = $this->cache->build_transient_no_prefix( [ $token ] );

		return wp_nonce_url( add_query_arg( [
			self::ARG       => true,
			self::SLUG      => $plugin->get_slug(),
			self::CACHE_KEY => $cache_key,
		], get_admin_url( get_current_blog_id() ) ), self::ARG );
	}

	/**
	 * Disconnect (delete) a token if the user is allowed to.
	 *
	 * @action stellarwp/uplink/{$prefix}/admin_action_{$slug}
	 *
	 * @throws \RuntimeException
	 *
	 * @return void
	 */
	public function maybe_disconnect(): void {
		if ( empty( $_GET[ self::ARG ] ) || empty( $_GET['_wpnonce'] ) || empty( $_GET[ self::SLUG ] ) || empty( $_GET[ self::CACHE_KEY ] ) ) {
			return;
		}

		if ( ! is_admin() || wp_doing_ajax() ) {
			return;
		}

		if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), self::ARG ) ) {
			if ( $this->authorizer->can_auth() && $this->disconnect->disconnect( $_GET[ self::SLUG ], $_GET[ self::CACHE_KEY ] ) ) {
				$this->notice->add(
					new Notice( Notice::SUCCESS,
						__( 'Token disconnected.', 'tribe-common' ),
						true
					)
				);

				/**
				 * Fires after a plugin has been disconnected.
				 *
				 * @since 2.2.2
				 *
				 * @param string $slug The plugin slug that was disconnected.
				 */
				do_action( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/' . $_GET[ self::SLUG ] . '/disconnected', $_GET[ self::SLUG ] );

				/**
				 * Fires after a plugin has been disconnected.
				 *
				 * @since 2.2.2
				 *
				 * @param string $slug The plugin slug that was disconnected.
				 */
				do_action( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/disconnected', $_GET[ self::SLUG ] );
			} else {
				$this->notice->add(
					new Notice( Notice::ERROR,
						__( 'Unable to disconnect token, ensure you have admin permissions.', 'tribe-common' ),
						true
					)
				);
			}
		} else {
			$this->notice->add(
				new Notice( Notice::ERROR,
					__( 'Unable to disconnect token: nonce verification failed.', 'tribe-common' ),
					true
				)
			);
		}

		$this->maybe_redirect_back();
	}

	/**
	 * Attempts to redirect the user back to their previous dashboard page while
	 * ensuring that any "Connect" token query variables are removed if they immediately
	 * attempt to Disconnect after Connecting. This prevents them from automatically
	 * getting connected again if the nonce is still valid.
	 *
	 * This will ensure the Notices set above are displayed.
	 *
	 * @return void
	 */
	private function maybe_redirect_back(): void {
		$referer = wp_get_referer();

		if ( ! $referer ) {
			return;
		}

		$referer = remove_query_arg(
			[
				Connect_Controller::TOKEN,
				Connect_Controller::LICENSE,
				Connect_Controller::SLUG,
				Connect_Controller::NONCE,
			],
			$referer
		);

		wp_safe_redirect( esc_url_raw( $referer ) );
		exit;
	}

}
