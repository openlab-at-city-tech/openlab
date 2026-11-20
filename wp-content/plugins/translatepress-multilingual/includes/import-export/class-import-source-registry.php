<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// The available import sources, keyed by slug; add-ons add theirs through the trp_import_sources filter.
class TRP_Import_Source_Registry {

    protected static $sources = null;

    public static function get_sources() {
        if ( self::$sources !== null ) {
            return self::$sources;
        }

        $sources = array( 'translatepress' => new TRP_Import_Source_TranslatePress() );
        $sources = apply_filters( 'trp_import_sources', $sources );

        $valid = array();
        if ( is_array( $sources ) ) {
            foreach ( $sources as $slug => $source ) {
                if ( $source instanceof TRP_Import_Source_Interface ) {
                    $valid[ (string) $slug ] = $source;
                }
            }
        }

        self::$sources = $valid;
        return self::$sources;
    }

    public static function get_source( $slug ) {
        $sources = self::get_sources();
        return isset( $sources[ $slug ] ) ? $sources[ $slug ] : null;
    }
}
