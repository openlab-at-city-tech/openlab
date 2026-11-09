<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Strategy;

use TEC\Common\LiquidWeb\Harbor\Features\Error_Code;
use WP_Error;

/**
 * Service Strategy — rejects all enable, disable, and update operations.
 *
 * Service features (e.g. Promoter) are managed exclusively through the
 * Commerce Portal. There is no WordPress-side activation state to read or write.
 * Any attempt to enable, disable, or update a service returns a WP_Error
 * directing the user to the Portal.
 *
 * @since 1.0.0
 */
class Service_Strategy extends Abstract_Strategy {

	/**
	 * @inheritDoc
	 */
	public function enable() {
		return new WP_Error(
			Error_Code::FEATURE_NOT_MODIFIABLE,
			sprintf(
				/* translators: %s: feature name */
				__( '"%s" is a service feature and cannot be enabled here. To manage this feature, visit the Commerce Portal.', 'tribe-common' ),
				$this->feature->get_name()
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function disable() {
		return new WP_Error(
			Error_Code::FEATURE_NOT_MODIFIABLE,
			sprintf(
				/* translators: %s: feature name */
				__( '"%s" is a service feature and cannot be disabled here. To manage this feature, visit the Commerce Portal.', 'tribe-common' ),
				$this->feature->get_name()
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function update() {
		return new WP_Error(
			Error_Code::FEATURE_NOT_MODIFIABLE,
			sprintf(
				/* translators: %s: feature name */
				__( '"%s" is a service feature and does not have updates. To manage this feature, visit the Commerce Portal.', 'tribe-common' ),
				$this->feature->get_name()
			)
		);
	}

	/**
	 * A service feature is active when it is available to the current site.
	 *
	 * @inheritDoc
	 */
	public function is_active(): bool {
		return $this->feature->is_available();
	}
}
