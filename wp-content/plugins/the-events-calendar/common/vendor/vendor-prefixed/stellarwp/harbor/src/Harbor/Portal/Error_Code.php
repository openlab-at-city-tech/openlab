<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal;

/**
 * WP_Error codes for the Catalog system.
 *
 * @since 1.0.0
 */
final class Error_Code {

	/**
	 * The requested product slug was not found in the catalog.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const PRODUCT_NOT_FOUND = 'lw-harbor-catalog-product-not-found';

	/**
	 * The catalog response could not be decoded.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const INVALID_RESPONSE = 'lw-harbor-catalog-invalid-response';
}
