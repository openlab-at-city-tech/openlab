<?php 
if ( ! class_exists( 'ElementsKit' ) ) {
	class ElementsKit {
		// for backward compatibility

		const VERSION = '1.5.9';

		const PACKAGE_TYPE = 'pro';

		const PRODUCT_ID = '9';

		const MINIMUM_ELEMENTOR_VERSION = '2.4.0';

		const MINIMUM_PHP_VERSION = '5.6';

		static function api_url() {
			return 'https://api.wpmet.com/public/';
		}

		static function plugin_url() {
			return trailingslashit( plugin_dir_url( __FILE__ ) );
		}

		static function plugin_dir() {
			return trailingslashit( plugin_dir_path( __FILE__ ) );
		}

		static function widget_dir() {
			return self::plugin_dir() . 'widgets/';
		}

		static function widget_url() {
			return self::plugin_url() . 'widgets/';
		}

		static function module_dir() {
			return self::plugin_dir() . 'modules/';
		}

		static function module_url() {
			return self::plugin_url() . 'modules/';
		}

		static function lib_dir() {
			return self::plugin_dir() . 'libs/';
		}

		static function lib_url() {
			return self::plugin_url() . 'libs/';
		}

	}
}
