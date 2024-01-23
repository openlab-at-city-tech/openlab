<?php
namespace Bookly\Lib\Base;

use Bookly\Lib;
use BooklyPro\Lib\Base\Proxy as ProxyPro;

abstract class Proxy extends Component
{
    /**
     * Register proxy methods.
     */
    public static function init()
    {
        if ( Lib\Config::proActive() ) {
            ProxyPro::init( get_called_class(), static::reflection() );
        }
    }

    /**
     * Invoke proxy method.
     *
     * @param string $method
     * @param mixed $args
     * @return mixed
     */
    public static function __callStatic( $method, $args )
    {
        if ( Lib\Config::proActive() ) {
            if ( ProxyPro::canInvoke( get_called_class(), $method ) ) {
                return ProxyPro::invoke( get_called_class(), $method, $args );
            }
        }

        // Return null for void methods or methods with "get" and "find" prefixes.
        return empty ( $args ) || preg_match( '/^(?:get|find)/', $method )
            ? null
            : $args[0];
    }

    /**
     * @inheritDoc
     */
    protected static function directory()
    {
        return dirname( parent::directory() );
    }
}