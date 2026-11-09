<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Legacy\Notices;

use TEC\Common\LiquidWeb\Harbor\Admin\Feature_Manager_Page;
use TEC\Common\LiquidWeb\Harbor\Legacy\License_Repository;
use TEC\Common\LiquidWeb\Harbor\Notice\Notice;
use TEC\Common\LiquidWeb\Harbor\Notice\Notice_Controller;
use TEC\Common\LiquidWeb\Harbor\Harbor;
use TEC\Common\LiquidWeb\Harbor\Utils\Cast;
use TEC\Common\LiquidWeb\Harbor\Utils\Version;

/**
 * Displays consolidated admin notices for legacy licenses that are not
 * covered by a StellarWP v3 unified license.
 *
 * Only fires on the leader Harbor instance to prevent duplicate notices
 * when multiple plugins bundle Harbor.
 *
 * @since 1.0.0
 */
class License_Notice_Handler {

	/**
	 * User meta key that stores a map of notice ID => dismissed-until timestamp.
	 *
	 * @since 1.0.0
	 */
	public const DISMISSED_META_KEY = 'lw_harbor_dismissed_notices';

	/**
	 * How long a dismissal lasts in seconds (7 days).
	 *
	 * @since 1.0.0
	 */
	public const DISMISS_TTL = 7 * DAY_IN_SECONDS;

	/**
	 * @var License_Repository
	 */
	private $repository;

	/**
	 * @var Notice_Controller
	 */
	private $controller;

	/**
	 * @since 1.0.0
	 *
	 * @param License_Repository $repository The license repository.
	 * @param Notice_Controller  $controller The notice controller.
	 */
	public function __construct( License_Repository $repository, Notice_Controller $controller ) {
		$this->repository = $repository;
		$this->controller = $controller;
	}

	/**
	 * Display notices for inactive legacy licenses that are not covered by a StellarWP v3 unified license.
	 *
	 * @action admin_notices
	 *
	 * @return void
	 */
	public function display(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! Version::should_handle( 'legacy_license_notices' ) ) {
			return;
		}

		$licenses = $this->repository->all_inactive();

		if ( empty( $licenses ) ) {
			return;
		}

		// Group by product, skipping any slug already covered by StellarWP v3 or dismissed by the user.
		$by_product = [];

		foreach ( $licenses as $license ) {
			if ( lw_harbor_is_feature_available( $license->slug ) ) {
				continue;
			}

			$product = $license->product;
			$id      = 'legacy-' . $product;

			if ( $this->is_dismissed( $id ) ) {
				continue;
			}

			if ( ! isset( $by_product[ $product ] ) ) {
				$by_product[ $product ] = [
					'id'       => $id,
					'page_url' => $license->page_url,
					'count'    => 0,
				];
			}

			++$by_product[ $product ]['count'];
		}

		if ( empty( $by_product ) ) {
			return;
		}

		foreach ( $by_product as $product => $data ) {
			if ( $this->is_on_notice_page( $data['page_url'] ) ) {
				continue;
			}

			$this->render_notice( $product, $data );
		}

		$this->enqueue_dismiss_script();
	}

	/**
	 * Whether a notice is currently dismissed for the current user.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id The notice ID.
	 *
	 * @return bool
	 */
	private function is_dismissed( string $id ): bool {
		$dismissed = (array) get_user_meta( get_current_user_id(), self::DISMISSED_META_KEY, true );

		return isset( $dismissed[ $id ] ) && Cast::to_int( $dismissed[ $id ] ) > time();
	}

	/**
	 * Whether the current admin request is already on the given page URL.
	 *
	 * Compares the `page` query parameter from the notice URL against the
	 * current request so the notice is suppressed when the user is already
	 * on the page they would be directed to.
	 *
	 * @since 1.0.0
	 *
	 * @param string $page_url The product's license page URL.
	 *
	 * @return bool
	 */
	private function is_on_notice_page( string $page_url ): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- reading current page slug for display routing only; sanitize_key wraps the value.
		$current_page = sanitize_key( Cast::to_string( $_GET['page'] ?? '' ) );

		if ( $current_page === '' ) {
			return false;
		}

		if ( $current_page === Feature_Manager_Page::PAGE_SLUG ) {
			return true;
		}

		$parsed = wp_parse_url( $page_url );

		if ( empty( $parsed['query'] ) ) {
			return false;
		}

		$params = [];
		wp_parse_str( $parsed['query'], $params );

		return ! empty( $params['page'] ) && $current_page === $params['page'];
	}

	/**
	 * Render a single product's license notice.
	 *
	 * @since 1.0.0
	 *
	 * TODO: Decide on messaging for all products.
	 *
	 * @param string                                          $product The product name.
	 * @param array{id: string, page_url: string, count: int} $data The notice data.
	 *
	 * @return void
	 */
	private function render_notice( string $product, array $data ): void {
		$message = sprintf(
			/* translators: %1$s is the product name, %2$s is the page URL, %3$d is the number of inactive licenses. */
			_n(
				'You have %3$d inactive %1$s license. Please <a href="%2$s">activate it</a> to receive critical updates and new features.',
				'You have %3$d inactive %1$s licenses. Please <a href="%2$s">activate them</a> to receive critical updates and new features.',
				$data['count'],
				'tribe-common'
			),
			ucfirst( $product ),
			esc_url( $data['page_url'] ),
			$data['count']
		);

		$this->controller->render(
			( new Notice( Notice::ERROR, $message, true, false, false, $data['id'] ) )->to_array()
		);
	}

	/**
	 * Register and enqueue the notice dismiss script, passing config via wp_localize_script.
	 *
	 * @since 1.0.0
	 *
	 * @return void Enqueues the notice dismiss script.
	 */
	private function enqueue_dismiss_script(): void {
		$handle = 'lw-harbor-notice-dismiss';

		if ( ! wp_script_is( $handle, 'registered' ) ) {
			$assets_url = trailingslashit( plugin_dir_url( __DIR__ . '/index.php' ) );

			wp_register_script(
				$handle,
				$assets_url . 'assets/js/notice-dismiss.js',
				[ 'wp-api-fetch' ],
				Harbor::VERSION,
				[ 'in_footer' => true ]
			);

			wp_localize_script(
				$handle,
				'harborNoticeDismiss',
				[
					'ttl'     => self::DISMISS_TTL,
					'metaKey' => self::DISMISSED_META_KEY,
				]
			);
		}

		wp_enqueue_script( $handle );
	}
}
