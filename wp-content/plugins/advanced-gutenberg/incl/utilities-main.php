<?php
namespace PublishPress\Blocks;

use Exception;

/*
 * Methods used across all the classes
 */
if( ! class_exists( '\\PublishPress\\Blocks\\Utilities' ) ) {
    class Utilities
    {
        /**
         * Check if a setting is enabled
         *
         * @param string $setting The setting from advgb_settings option field
         *
         * @since 3.1.0
         * @return boolean
         */
        public static function settingIsEnabled( $setting ) {
            $saved_settings = get_option( 'advgb_settings' );
            if( ! isset( $saved_settings[$setting] ) || $saved_settings[$setting] ) {
                return true;
            } else {
                return false;
            }
        }
    }
}
