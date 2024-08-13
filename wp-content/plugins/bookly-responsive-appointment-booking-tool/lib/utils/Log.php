<?php
namespace Bookly\Lib\Utils;

use Bookly\Lib;

abstract class Log
{
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_ERROR = 'error';
    const ACTION_DEBUG = 'debug';

    const OPTION_OUTLOOK = 'bookly_temporary_logs_outlook';
    const OPTION_GOOGLE = 'bookly_temporary_logs_google';

    public static function getTypes()
    {
        return array( self::OPTION_OUTLOOK, self::OPTION_GOOGLE );
    }

    /**
     * @param string $action
     * @param string $target
     * @param string $target_id
     * @param string $details
     * @param string $ref
     * @param string $comment
     * @return void|bool
     */
    public static function common( $action = null, $target = null, $target_id = null, $details = null, $ref = null, $comment = null )
    {
        self::put( $action, $target, $target_id, $details, $ref, $comment );
    }

    /**
     * @param $entity
     * @param string $action
     * @param string $comment
     * @return void|bool
     */
    public static function fromBacktrace( $entity, $action = self::ACTION_UPDATE, $comment = '' )
    {
        try {
            if ( $entity->isLoggable() ) {
                $debug_backtrace = debug_backtrace();
                $key = 0;
                foreach ( $debug_backtrace as $key => $trace ) {
                    if ( isset( $trace['class'] ) && $trace['class'] === __CLASS__ ) {
                        break;
                    }
                }
                $ref = '';
                for ( $offset = 3; $offset < 6; $offset++ ) {
                    if ( isset( $debug_backtrace[ $key + $offset ] ) && ( $trace = $debug_backtrace[ $key + $offset ] ) && $trace['function'] !== 'call_user_func' ) {
                        $ref .= ( isset( $trace['class'] ) ? $trace['class'] : '' ) . ( isset( $trace['type'] ) ? $trace['type'] : ' ' ) . ( isset( $trace['function'] ) ? $trace['function'] : '' ) . "\n";
                    }
                }

                $modified = array();
                if ( $action === self::ACTION_UPDATE ) {
                    $fields = $entity->getFields();
                    foreach ( array_keys( $entity->getModified() ) as $key ) {
                        $modified[ $key ] = $fields[ $key ];
                    }
                } else {
                    $modified = $entity->getFields();
                }

                self::common( $action, $entity->getTableName(), $entity->getId(), json_encode( $modified ), $ref, $comment );
            }
        } catch ( \Exception $e ) {
        }
    }

    /**
     * @param string $message
     * @param string $file
     * @param string $line
     * @return void
     */
    public static function error( $message = null, $file = null, $line = null )
    {
        try {
            $target = $file . ':' . $line;
            if ( $entry = Lib\Entities\Log::query()->where( 'target', $target )->findOne() ) {
                /** @var Lib\Entities\Log $entry */
                $entry
                    ->setDetails( $message )
                    ->setComment( 'Last occurrence: ' . current_time( 'mysql' ) )
                    ->save();
            } else {
                $plugins_updated = true;
                $plugins = get_site_transient( 'update_plugins' );
                if ( $plugins && property_exists( $plugins, 'response' ) ) {
                    foreach ( $plugins->response as $plugin => $data ) {
                        if ( strpos( $plugin, 'bookly-' ) === 0 ) {
                            $plugins_updated = false;
                        }
                    }
                }
                self::put( self::ACTION_ERROR, $target, null, $message, null, $plugins_updated ? null : 'Bookly plugins has not updated to the latest versions' );
            }
        } catch ( \Exception $e ) {
        }
        if ( get_option( 'bookly_dev' ) ) {
            $message = sprintf( '<b>%s</b><p>%s:%s</p>', $message, substr( $file, strpos( $file, 'bookly-' ) ), $line );
            $wp_error = new \WP_Error( 'bookly_error', $message, array() );
            wp_die( $wp_error, 'Bookly', array( 'response' => 500, 'exit' => true, ) );
        }
    }

    /**
     * @param string $action
     * @param string $target
     * @param string $target_id
     * @param string $details
     * @param string $ref
     * @param string $comment
     * @return void
     */
    public static function put( $action = 'debug', $target = null, $target_id = null, $details = null, $ref = null, $comment = null )
    {
        $log = new Lib\Entities\Log();
        $log
            ->setAction( $action )
            ->setTarget( $target )
            ->setTargetId( $target_id )
            ->setAuthor( self::getAuthor() )
            ->setRef( $ref )
            ->setComment( $comment )
            ->setDetails( $details )
            ->setCreatedAt( current_time( 'mysql' ) )
            ->save();
    }

    public static function tempPut( $option, $target = null, $target_id = null, $details = null, $comment = null )
    {
        if ( get_option( $option ) ) {
            self::put( self::ACTION_DEBUG, $target, $target_id, $details, ucfirst( self::getLogOptionTitle( $option ) ), $comment );
        }
    }

    public static function getLogOptionTitle( $option )
    {
        return substr( $option, strrpos( $option, '_' ) + 1 );
    }

    /**
     * @return string
     */
    private static function getAuthor()
    {
        $author_id = get_current_user_id();

        return $author_id ? ( trim( get_user_meta( $author_id, 'first_name', true ) . ' ' . get_user_meta( $author_id, 'last_name', true ) ) ?: get_user_meta( $author_id, 'nickname', true ) ) : '';
    }
}