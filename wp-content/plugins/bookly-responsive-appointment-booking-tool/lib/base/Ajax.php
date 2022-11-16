<?php
namespace Bookly\Lib\Base;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Lib\Base
 */
abstract class Ajax extends Component
{
    /**
     * Register WP Ajax actions.
     */
    public static function init()
    {
        if ( defined( 'DOING_AJAX' ) ) {
            /** @var static $called_class */
            $called_class  = get_called_class();
            $plugin_prefix = call_user_func( array( Lib\Base\Plugin::getPluginFor( $called_class ), 'getPrefix' ) );
            $anonymous     = in_array( 'anonymous', $called_class::permissions() );

            foreach ( static::reflection()->getMethods( \ReflectionMethod::IS_PUBLIC ) as $method ) {
                if ( $method->class !== $called_class ) {
                    // Stop if parent class reached.
                    break;
                }
                // Register Ajax action.
                $action   = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1_$2', $method->name ) );
                $function = function () use ( $called_class, $method ) {
                    $called_class::forward( $method->name, true, true );
                };
                add_action( sprintf( 'wp_ajax_%s%s', $plugin_prefix, $action ), $function );
                if ( $anonymous ) {
                    add_action( sprintf( 'wp_ajax_nopriv_%s%s', $plugin_prefix, $action ), $function );
                }
            }
        }
    }

    /**
     * Execute given action (if the current user has appropriate permissions).
     *
     * @param string $action
     * @param bool   $check_csrf
     * @param bool   $check_access
     */
    public static function forward( $action, $check_csrf = true, $check_access = true )
    {
        if ( ( ! $check_csrf || static::csrfTokenValid( $action ) ) && ( ! $check_access || static::hasAccess( $action ) ) ) {
            $original_timezone = date_default_timezone_get();
            // @codingStandardsIgnoreStart
            date_default_timezone_set( 'UTC' );
            try {
                call_user_func( array( get_called_class(), $action ) );
            } catch ( \Error $e ) {
                Lib\Utils\Log::error( $e->getMessage(), $e->getFile(), $e->getLine() );
            } catch ( \Exception $e ) {
                Lib\Utils\Log::error( $e->getMessage(), $e->getFile(), $e->getLine() );
            }
            date_default_timezone_set( $original_timezone );
            // @codingStandardsIgnoreEnd
        } else {
            wp_die( 'Bookly: ' . __( 'You do not have sufficient permissions to access this page.' ) );
        }
    }

    /**
     * Check if the current user has access to the action.
     *
     * Default access (if is not set in permissions()) is "admin"
     * Access type:
     *  "admin"     - check if the current user is admin
     *  "user"      - check if the current user is authenticated
     *  "anonymous" - anonymous user
     *
     * @param string $action
     * @return bool
     */
    protected static function hasAccess( $action )
    {
        $permissions = static::permissions();
        $security    = isset ( $permissions[ $action ] ) ? $permissions[ $action ] : null;

        if ( is_null( $security ) ) {
            // Check if default permission is set.
            $security = isset ( $permissions['_default'] ) ? $permissions['_default'] : array( 'admin' );
        }

        $permitted = false;
        foreach ( (array) $security as $access_type ) {
            switch ( $access_type ) {
                case 'admin':      $permitted = Lib\Utils\Common::isCurrentUserAdmin(); break;
                case 'supervisor': $permitted = Lib\Utils\Common::isCurrentUserSupervisor(); break;
                case 'staff':      $permitted = Lib\Utils\Common::isCurrentUserStaff(); break;
                case 'customer':   $permitted = Lib\Utils\Common::isCurrentUserCustomer(); break;
                case 'user':       $permitted = is_user_logged_in(); break;
                case 'anonymous':  $permitted = true; break;
            }
            if ( $permitted ) {
                return true;
            }
        }

        return $permitted;
    }

    /**
     * Get access permissions for child controller methods.
     * Array structure:
     *  [ action_name => array|string access_type ]
     * where:
     *   action_name => action's name or _default;
     *                  _default - for all actions which have no explicit value of access_type indicated
     *
     * access_type => array or string, for array it is enough that at least one of the access_type is available
     *
     * @return array
     */
    protected static function permissions()
    {
        return array();
    }
}