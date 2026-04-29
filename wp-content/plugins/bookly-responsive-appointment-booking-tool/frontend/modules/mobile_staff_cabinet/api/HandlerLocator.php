<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api;

use Bookly\Lib;

final class HandlerLocator
{
    /** @var array<string, Handlers\HandlerInterface> */
    private $handlers = array();

    /**
     * @param string $class_name
     */
    public function register( $class_name )
    {
        /** @var Handlers\HandlerInterface $class_name */
        $this->handlers[ $class_name::getVersion() ] = $class_name;
    }

    /**
     * @param \WP_User | Lib\Entities\Staff $staff_or_wp_user
     * @param Lib\Base\Request $request
     *
     * @return Handlers\HandlerInterface
     * @throws Exceptions\HandleException
     */
    public function getHandler( $staff_or_wp_user, Lib\Base\Request $request )
    {
        $version = $request->getHeaders()->getGreedy( 'X-Bookly-Api-Version', '1.0' );

        if ( ! array_key_exists( $version, $this->handlers ) ) {
            throw new Exceptions\HandleException( 'UNKNOWN_REQUEST', $request, null, 'API ' . $version . ' — not found' );
        }
        $compatible_classes = $this->findCompatibleHandlerClasses( $version );
        $method = $this->buildMethodNameFromRequest( $request );

        foreach ( $compatible_classes as $class_name ) {
            if ( method_exists( $class_name, $method ) ) {
                try {
                    /** @var Handlers\HandlerInterface $class_name */
                    return new $class_name( $staff_or_wp_user, $method );
                } catch ( \Error $e ) {
                    throw new Exceptions\HandleException( 'UNKNOWN_REQUEST', $request, $class_name, 'Method ' . $method . ' has error ' . $e->getMessage() );
                } catch ( \Exception $e ) {
                    throw new Exceptions\HandleException( 'UNKNOWN_REQUEST', $request, $class_name, 'Method ' . $method . ' has exception ' . $e->getMessage() );
                }
            }
        }
        throw new Exceptions\HandleException( 'UNKNOWN_REQUEST', $request, null, 'Method ' . $method . ' — not found' );
    }

    private function findCompatibleHandlerClasses( $version )
    {
        $compatible_handlers = array();
        foreach ( $this->handlers as $handler_version => $handler ) {
            if ( version_compare( $handler_version, $version, '<=' ) ) {
                $compatible_handlers[ $handler_version ] = $handler;
            }
        }

        // Sort handlers by version descending
        uksort( $compatible_handlers, static function ( $a, $b ) {
            return version_compare( $b, $a );
        } );

        return $compatible_handlers;
    }

    private function buildMethodNameFromRequest( Lib\Base\Request $request )
    {
        $resource = $request->get( 'resource' );
        $action = $request->get( 'action' );

        if ( empty( $resource ) ) {
            throw new Exceptions\HandleException( 'UNKNOWN_REQUEST', $request, null, 'Resouce — is empty' );
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