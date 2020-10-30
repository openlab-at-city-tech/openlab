<?php
namespace Ari\Utils;

class Object_Helper {
    public static function extract_name( $obj ) {
        $class = get_class( $obj );
        $class = explode( '\\', $class );

        return array_pop( $class );
    }

    public static function get_properties( $obj ) {
        $vars = get_object_vars( $obj );

        return $vars;
    }

    public static function get_default_properties( $obj ) {
        return get_class_vars( get_class( $obj ) );
    }

    public static function get_path( $obj ) {
        $rc = new \ReflectionClass( get_class( $obj ) );
        return dirname( $rc->getFileName() );
    }

    public static function get_namespace( $obj ) {
        return ( new \ReflectionObject( $obj ) )->getNamespaceName();
    }
}
