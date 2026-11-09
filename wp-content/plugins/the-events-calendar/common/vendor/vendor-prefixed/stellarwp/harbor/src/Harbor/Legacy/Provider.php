<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Legacy;

use TEC\Common\LiquidWeb\Harbor\Contracts\Abstract_Provider;
use TEC\Common\LiquidWeb\Harbor\Legacy\Notices\License_Notice_Handler;

/**
 * Registers services for legacy license discovery.
 *
 * @since 1.0.0
 */
class Provider extends Abstract_Provider {

	/**
	 * @inheritDoc
	 */
	public function register(): void {
		$this->container->singleton( License_Repository::class );

		$this->container->singleton( License_Notice_Handler::class );

		$this->register_dismissed_notices_meta();

		add_action( 'admin_notices', [ $this->container->get( License_Notice_Handler::class ), 'display' ], 10, 0 );
	}

	/**
	 * Register the user meta field that tracks dismissed notice IDs and their
	 * expiry timestamps, exposed via the REST API for JS to read and update.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_dismissed_notices_meta(): void {
		register_meta(
			'user',
			License_Notice_Handler::DISMISSED_META_KEY,
			[
				'type'          => 'object',
				'single'        => true,
				'default'       => [],
				'show_in_rest'  => [
					'schema' => [
						'type'                 => 'object',
						'additionalProperties' => [
							'type' => 'integer',
						],
					],
				],
				'auth_callback' => static function (): bool {
					return is_user_logged_in();
				},
			]
		);
	}
}
