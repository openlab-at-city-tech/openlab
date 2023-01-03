<?php
namespace Bookly\Lib;

use Bookly\Lib\Base\Cache;

/**
 * Class Session
 *
 * @package Bookly\Lib
 * @method static mixed get( $name, $default = null )
 * @method static void  set( $name, $value )
 * @method static bool  has( $name )
 * @method static void  destroy( $name )
 * @method static mixed getFormVar( $form_id, $name, $default = null )
 * @method static void  setFormVar( $form_id, $name, $value )
 * @method static bool  hasFormVar( $form_id, $name )
 * @method static void  destroyFormVar( $form_id, $name )
 * @method static void  destroyFormData( $form_id )
 * @method static array getAllFormsData()
 * @method static void  save()
 */
abstract class Session extends Cache
{
    protected static $session_class;

    public static function init( $session_type = 'db' )
    {
        $class = $session_type === 'db' ? '\Bookly\Lib\SessionDB' : '\Bookly\Lib\SessionPHP';

        self::$session_class = $class;

        $class::initSession();
    }

    /**
     * Call magic functions.
     *
     * @param $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic( $name, array $arguments )
    {
        $class = self::$session_class;

        return call_user_func_array( array( $class, $name ), $arguments );
    }
}