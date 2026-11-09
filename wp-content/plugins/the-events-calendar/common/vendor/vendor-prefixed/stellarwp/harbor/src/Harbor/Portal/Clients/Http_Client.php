<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal\Clients;

use TEC\Common\Psr\Http\Client\ClientExceptionInterface;
use TEC\Common\Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use TEC\Common\LiquidWeb\Harbor\Portal\Catalog_Collection;
use TEC\Common\LiquidWeb\Harbor\Portal\Error_Code;
use TEC\Common\LiquidWeb\Harbor\Portal\Results\Product_Catalog;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;
use WP_Error;

/**
 * PSR-18 HTTP implementation of the catalog API client.
 *
 * @since 1.0.0
 */
final class Http_Client implements Portal_Client {

	use With_Debugging;

	/**
	 * The PSR-18 HTTP client.
	 *
	 * @since 1.0.0
	 *
	 * @var ClientInterface
	 */
	protected ClientInterface $client;

	/**
	 * The PSR-17 request factory.
	 *
	 * @since 1.0.0
	 *
	 * @var RequestFactoryInterface
	 */
	protected RequestFactoryInterface $request_factory;

	/**
	 * The API base URL (no trailing slash).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $base_url;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param ClientInterface         $client          PSR-18 HTTP client.
	 * @param RequestFactoryInterface $request_factory PSR-17 request factory.
	 * @param string                  $base_url        API base URL (no trailing slash).
	 */
	public function __construct(
		ClientInterface $client,
		RequestFactoryInterface $request_factory,
		string $base_url
	) {
		$this->client          = $client;
		$this->request_factory = $request_factory;
		$this->base_url        = $base_url;
	}

	/**
	 * @inheritDoc
	 */
	public function get_catalog() {
		$url = $this->base_url . '/wp-json/slw/v1/catalog';

		self::debug_log(
			sprintf( 'Catalog HTTP request: GET %s', $url )
		);

		$request = $this->request_factory->createRequest( 'GET', $url );

		try {
			$response = $this->client->sendRequest( $request );
		} catch ( ClientExceptionInterface $e ) {
			self::debug_log(
				sprintf( 'Catalog HTTP exception: %s', $e->getMessage() )
			);

			return new WP_Error(
				Error_Code::INVALID_RESPONSE,
				$e->getMessage()
			);
		}

		$status_code = $response->getStatusCode();

		self::debug_log(
			sprintf( 'Catalog HTTP response: %d', $status_code )
		);

		if ( $status_code < 200 || $status_code >= 300 ) {
			return new WP_Error(
				Error_Code::INVALID_RESPONSE,
				sprintf( 'Catalog API returned HTTP %d.', $status_code ),
				[ 'status' => $status_code ]
			);
		}

		$data = json_decode( (string) $response->getBody(), true );

		if ( ! is_array( $data ) ) {
			self::debug_log( 'Catalog response body could not be decoded as JSON.' );

			return new WP_Error(
				Error_Code::INVALID_RESPONSE,
				'Catalog response could not be decoded.'
			);
		}

		$catalogs = new Catalog_Collection();

		foreach ( $data as $item ) {
			if ( ! is_array( $item ) || ! isset( $item['product_slug'] ) ) {
				return new WP_Error(
					Error_Code::INVALID_RESPONSE,
					'Catalog entry missing product_slug.'
				);
			}

			/** @var array<string, mixed> $item */
			$catalogs->add( Product_Catalog::from_array( $item ) );
		}

		if ( $catalogs->count() === 0 ) {
			return new WP_Error(
				Error_Code::INVALID_RESPONSE,
				'Catalog response is empty.'
			);
		}

		return $catalogs;
	}
}
