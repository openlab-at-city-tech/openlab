<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DLM Pro versions before 1.1.0 don't have the centralized licensing/
 * extensions system (`DLM_Pro\Extensions\Licensing`/`Extensions`) — that
 * was introduced in 1.1.0. A site running an older Pro still defines
 * DLM_PRO_VERSION, so `defined( 'DLM_PRO_VERSION' )` alone can't tell an
 * outdated install apart from a current one. Without this check, Lite's
 * license/upsell UI would treat an old Pro the same as no Pro at all,
 * confusing users who already paid and installed it.
 */
class DLM_Pro_Compat {

	const MIN_VERSION = '1.1.0';

	/**
	 * @return bool
	 */
	public static function is_pro_outdated() {
		return defined( 'DLM_PRO_VERSION' ) && version_compare( DLM_PRO_VERSION, self::MIN_VERSION, '<' );
	}
}
