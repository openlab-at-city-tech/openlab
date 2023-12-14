<?php

namespace PublishPress\Blocks;

/*
 * Methods used across all the classes
 */
if ( ! class_exists( '\\PublishPress\\Blocks\\Utilities' ) ) {
	class Utilities {
		/**
		 * Check if a setting is enabled
		 *
		 * @param string $setting The setting from advgb_settings option field
		 *
		 * @return boolean
		 * @since 3.1.0
		 */
		public static function settingIsEnabled( $setting ) {
			$saved_settings = get_option( 'advgb_settings' );

			if ( isset( $saved_settings[ $setting ] ) && ! $saved_settings[ $setting ] ) {
				return false;
			}

			if ( ! isset( $saved_settings[ $setting ] ) || $saved_settings[ $setting ] ) {
				return true;
			}

			return false;
		}
	}
}
