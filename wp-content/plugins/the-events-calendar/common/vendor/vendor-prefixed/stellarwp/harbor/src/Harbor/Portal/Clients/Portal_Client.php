<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal\Clients;

use TEC\Common\LiquidWeb\Harbor\Portal\Catalog_Collection;
use WP_Error;

/**
 * Contract for the product catalog API client.
 *
 * @since 1.0.0
 */
interface Portal_Client {

	/**
	 * Fetch the full catalog for all products.
	 *
	 * @since 1.0.0
	 *
	 * @return Catalog_Collection|WP_Error
	 */
	public function get_catalog();
}
