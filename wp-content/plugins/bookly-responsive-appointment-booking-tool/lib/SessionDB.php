<?php
namespace Bookly\Lib;

use Bookly\Lib\Base\Cache;
use Bookly\Lib\Entities;

/**
 * Class SessionDB
 *
 * @package Bookly\Lib
 */
abstract class SessionDB extends Session
{
    public static function initSession()
    {
        static $initialized;
        if ( ! $initialized ) {
            if ( ! array_key_exists( self::cookieName(), $_COOKIE ) ) {
                @setcookie( self::cookieName(), self::sessionId(), 0, '/', '', false, true );
            }

            add_action( 'shutdown', array( __CLASS__, 'save' ), 20 );
            $initialized = true;
        }
    }

    /**
     * Get value from session.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get( $name, $default = null )
    {
        $session = self::getSession();

        return isset( $session[ $name ] ) ? $session[ $name ] : $default;
    }

    /**
     * Set value to session.
     *
     * @param string $name
     * @param mixed $value
     */
    public static function set( $name, $value )
    {
        $session = self::getSession();
        $session[ $name ] = $value;

        $key = 'bookly-db-session';
        self::putInCache( $key, $session );
    }

    /**
     * Check if a named value exists in session.
     *
     * @param string $name
     * @return bool
     */
    public static function has( $name )
    {
        $session = self::getSession();

        return isset( $session[ $name ] );
    }

    /**
     * Destroy value in session.
     *
     * @param string $name
     */
    public static function destroy( $name )
    {
        $session = self::getSession();
        unset( $session[ $name ] );

        $key = 'bookly-db-session';
        self::putInCache( $key, $session );
    }

    /**
     * Get named variable of a frontend booking form.
     *
     * @param string $form_id
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function getFormVar( $form_id, $name, $default = null )
    {
        $result = $default;

        $session = self::getSession();

        if ( isset ( $session['forms'][ $form_id ][ $name ] ) ) {
            $result = $session['forms'][ $form_id ][ $name ];
        }

        return $result;
    }

    /**
     * Set named variable for a frontend booking form.
     *
     * @param string $form_id
     * @param string $name
     * @param mixed $value
     */
    public static function setFormVar( $form_id, $name, $value )
    {
        $session = self::getSession();
        if ( ! isset( $session['forms'] ) ) {
            $session['forms'] = array();
        }
        if ( ! isset( $session['forms'][ $form_id ] ) ) {
            $session['forms'][ $form_id ] = array();
        }

        $session['forms'][ $form_id ][ $name ] = $value;

        $key = 'bookly-db-session';
        self::putInCache( $key, $session );
    }

    /**
     * Check if a named variable exists for a frontend booking form.
     *
     * @param string $form_id
     * @param string $name
     * @return bool
     */
    public static function hasFormVar( $form_id, $name )
    {
        $session = self::getSession();

        return isset ( $session['forms'][ $form_id ][ $name ] );
    }

    /**
     * Get data of all booking forms.
     *
     * @return array
     */
    public static function getAllFormsData()
    {
        $data = array();
        $session = self::getSession();
        if ( isset ( $session['forms'] ) ) {
            $data = $session['forms'];
        }

        return $data;
    }

    /**
     * Destroy named variable in booking form data.
     *
     * @param string $form_id
     * @param string $name
     */
    public static function destroyFormVar( $form_id, $name )
    {
        $session = self::getSession();
        if ( isset( $session['forms'][ $form_id ][ $name ] ) ) {
            unset( $session['forms'][ $form_id ][ $name ] );
            $key = 'bookly-db-session';
            self::putInCache( $key, $session );
        }
    }

    /**
     * Destroy all data of a booking form.
     *
     * @param string $form_id
     */
    public static function destroyFormData( $form_id )
    {
        $session = self::getSession();
        if ( isset( $session['forms'][ $form_id ] ) ) {
            unset( $session['forms'][ $form_id ] );
            $key = 'bookly-db-session';
            self::putInCache( $key, $session );
        }
    }

    public static function getSession()
    {
        $key = 'bookly-db-session';

        if ( ! self::hasInCache( $key ) ) {
            $result = Entities\Session::query()->where( 'token', self::sessionId() )->whereGte( 'expire', current_time( 'mysql' ) )->fetchVar( 'value' );

            self::putInCache( $key, $result ? json_decode( $result, true ) : array() );
        }

        return self::getFromCache( $key );
    }

    public static function save()
    {
        $session = new Entities\Session();
        $session->loadBy( array( 'token' => self::sessionId() ) );
        $value = self::getSession();
        if ( $value || $session->isLoaded() ) {
            $session
                ->setToken( self::sessionId() )
                ->setValue( json_encode( self::getSession() ) )
                ->setExpire( date_create( current_time( 'mysql' ) )->modify( '+1 day' )->format( 'Y-m-d H:i:s' ) )
                ->save();
        }
    }

    private static function sessionId()
    {
        $key = 'bookly-db-session-id';

        if ( ! self::hasInCache( $key ) ) {
            $cookie_name = self::cookieName();
            $cookie_value = isset( $_COOKIE[ $cookie_name ] ) ? $_COOKIE[ $cookie_name ] : false;

            self::putInCache( $key, $cookie_value ?: substr( md5( uniqid( time(), true ) ), 0, 32 ) );
        }

        return self::getFromCache( $key );
    }

    private static function cookieName()
    {
        return 'bookly-session-' . COOKIEHASH;
    }
}