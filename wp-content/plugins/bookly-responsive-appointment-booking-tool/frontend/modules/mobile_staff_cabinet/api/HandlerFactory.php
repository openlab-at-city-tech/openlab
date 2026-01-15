<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api;

use Bookly\Lib;

class HandlerFactory
{
    /**
     * Creates appropriate handler object based on API version
     *
     * @param Lib\Entities\Auth|null $auth Authentication entity
     * @param Lib\Base\Request $request Request object
     * @return ApiHandler
     */
    public static function create( $auth, Lib\Base\Request $request )
    {
        list( $role, $staff_or_wp_user ) = self::determineUserRole( $auth );

        if ( $staff_or_wp_user === null ) {
            throw new Exceptions\ApiException( 'Unauthorized', 401, array(), $request );
        }

        // Create handler based on protocol version and requested method
        $handler = self::findCompatibleHandler( $request, $role );

        // Set user data based on role
        if ( $role === ApiHandler::ROLE_SUPERVISOR && method_exists( $handler, 'setWpUser' ) ) {
            $handler->setWpUser( $staff_or_wp_user );
        } elseif ( $role === ApiHandler::ROLE_STAFF && method_exists( $handler, 'setStaff' ) ) {
            $handler->setStaff( $staff_or_wp_user );
        }

        return $handler;
    }

    /**
     * Determines user role based on authentication data
     *
     * @param Lib\Entities\Auth|null $auth Authentication entity
     * @return array
     */
    private static function determineUserRole( $auth )
    {
        $role = null;

        if ( $auth === null ) {
            return array( $role, null );
        }

        // Check staff access
        if ( $auth->getStaffId() ) {
            $staff = Lib\Entities\Staff::find( $auth->getStaffId() );
            if ( $staff ) {
                return array( ApiHandler::ROLE_STAFF, $staff );
            }
        } // Check admin/supervisor access
        elseif ( $auth->getWpUserId() ) {
            $wp_user = get_user_by( 'id', $auth->getWpUserId() );
            $user_id = $auth->getWpUserId();

            if ( user_can( $user_id, 'manage_bookly' ) ||
                user_can( $user_id, 'manage_options' ) ||
                user_can( $user_id, 'manage_bookly_appointments' ) ) {
                return array( ApiHandler::ROLE_SUPERVISOR, $wp_user );
            }
        }

        return array( $role, null );
    }

    /**
     * Creates handler object for specific API version with method support check
     *
     * @param Lib\Base\Request $request
     * @param string|null $role User role
     * @return ApiHandler
     */
    private static function findCompatibleHandler( Lib\Base\Request $request, $role )
    {
        // Get API version from headers (default: 1.0)
        $version = $request->getHeaders()->getGreedy( 'X-Bookly-Api-Version', '1.0' );

        $compatible_classes = self::findCompatibleHandlerClasses( $version );

        if ( empty( $compatible_classes ) ) {
            throw new Exceptions\HandleException( 'UNKNOWN_REQUEST', $request, null, 'No compatible response classes found' );
        }

        $method = self::buildMethodNameFromRequest( $request );

        foreach ( $compatible_classes as $class_name ) {
            if ( method_exists( $class_name, $method ) ) {
                try {
                    /** @var ApiHandler $handler */
                    $handler = new $class_name( $role, $request, new Response() );
                    $handler->setProcessMethod( $method );

                    return $handler;
                } catch ( \Error $e ) {
                    throw new Exceptions\HandleException( 'UNKNOWN_REQUEST', $request, $class_name, 'Method ' . $method . ' has error ' . $e->getMessage() );
                } catch ( \Exception $e ) {
                    throw new Exceptions\HandleException( 'UNKNOWN_REQUEST', $request, $class_name, 'Method ' . $method . ' has exception ' . $e->getMessage() );
                }
            }
        }

        throw new Exceptions\HandleException( 'UNKNOWN_REQUEST', $request, null, 'Method ' . $method . ' — not found' );
    }

    /**
     * Finds all handler classes compatible with requested version
     *
     * @param string $version Requested API version
     * @return string[] Array of compatible handler class names
     */
    private static function findCompatibleHandlerClasses( $version )
    {
        $version = trim( $version );
        $fs = Lib\Utils\Common::getFilesystem();
        $base_dir = __DIR__;
        $dirs = $fs->dirlist( $base_dir, false );
        $classes = array();

        foreach ( $dirs as $dir ) {
            if ( $dir['type'] === 'd' && preg_match( '/^v(\d+_\d+)$/', $dir['name'], $matches ) ) {
                $folder_version = str_replace( '_', '.', $matches[1] );
                if ( version_compare( $folder_version, $version, '>' ) ) {
                    continue;
                }
                $handler_path = $base_dir . '/' . $dir['name'] . '/Handler.php';
                if ( file_exists( $handler_path ) ) {
                    $class_name = __NAMESPACE__ . '\\' . strtoupper( $dir['name'] ) . '\\Handler';
                    if ( class_exists( $class_name ) ) {
                        $classes[ $folder_version ] = $class_name;
                    }
                }
            }
        }

        if ( count( $classes ) > 1 ) {
            uksort( $classes, function( $a, $b ) {
                return version_compare( $b, $a );
            } );
        }

        return $classes;
    }

    /**
     * Builds method name from request parameters
     * Converts kebab-case resource names to camelCase method names
     * Example: 'resource-name' with action 'save' => 'saveResourceName'
     *
     * @param Lib\Base\Request $request
     * @return string|null
     */
    private static function buildMethodNameFromRequest( Lib\Base\Request $request )
    {
        $resource = $request->get( 'resource' );
        $action = $request->get( 'action' );

        if ( empty( $resource ) ) {
            return null;
        }

        // Convert kebab-case to camelCase
        $parts = explode( '-', $resource );
        $method = $parts[0];

        // Convert remaining parts to PascalCase and append
        $parts_count = count( $parts );
        for ( $i = 1; $i < $parts_count; $i++ ) {
            $method .= ucfirst( $parts[ $i ] );
        }

        // Append action if present
        if ( ! empty( $action ) ) {
            $method = $action . ucfirst( $method );
        }

        return $method;
    }
}
