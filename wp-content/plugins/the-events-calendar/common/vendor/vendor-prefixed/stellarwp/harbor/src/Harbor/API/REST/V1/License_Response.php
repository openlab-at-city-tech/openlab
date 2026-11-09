<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\API\REST\V1;

use TEC\Common\LiquidWeb\Harbor\Licensing\Product_Collection;
use WP_Error;

/**
 * Builds the standard {key, products, error} response shape.
 *
 * @since 1.0.0
 */
final class License_Response {

	/**
	 * Builds the standard license response array.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null        $key      The license key.
	 * @param Product_Collection $products The product collection.
	 * @param WP_Error|null      $error    An optional error to include in the response.
	 *
	 * @return array{key: string|null, products: array<int, array<string, mixed>>, error: array{code: string, message: string}|null}
	 */
	public static function make( ?string $key, Product_Collection $products, ?WP_Error $error = null ): array {
		return [
			'key'      => $key,
			'products' => $products->to_array(),
			'error'    => $error !== null ? [
				'code'    => (string) $error->get_error_code(),
				'message' => $error->get_error_message(),
			] : null,
		];
	}
}
