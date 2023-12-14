<?php
namespace Bookly\Lib;

abstract class SessionDB extends Session
{
    protected static $affected = array();

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
        self::touch( $name );
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
        self::touch( $name );
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

        $form_var = self::formVar( $form_id );

        if ( isset ( $session[ $form_var ][ $name ] ) ) {
            $result = $session[ $form_var ][ $name ];
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
        $form_var = self::formVar( $form_id );
        self::touch( $form_var );
        $session = self::getSession();

        if ( ! isset( $session[ $form_var ] ) ) {
            $session[ $form_var ] = array();
        }

        $session[ $form_var ][ $name ] = $value;

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
        $form_var = self::formVar( $form_id );
        $session = self::getSession();

        return isset ( $session[ $form_var ][ $name ] );
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
        foreach ( $session as $key => $value ) {
            $data[ str_replace( 'form-', '', $key ) ] = $value;
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
        $form_var = self::formVar( $form_id );
        self::touch( $form_var );
        $session = self::getSession();
        if ( isset( $session[ $form_var ][ $name ] ) ) {
            unset( $session[ $form_var ][ $name ] );
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
        $form_var = self::formVar( $form_id );
        self::touch( $form_var );
        $session = self::getSession();
        if ( isset( $session[ $form_var ] ) ) {
            unset( $session[ $form_var ] );
            $key = 'bookly-db-session';
            self::putInCache( $key, $session );
        }
    }

    public static function getSession()
    {
        $key = 'bookly-db-session';

        if ( ! self::hasInCache( $key ) ) {
            $session = array();
            foreach ( Entities\Session::query()->where( 'token', self::sessionId() )->whereGte( 'expire', current_time( 'mysql' ) )->fetchArray() as $record ) {
                $session[ $record['name'] ] = json_decode( $record['value'], true );
            }

            self::putInCache( $key, $session );
        }

        return self::getFromCache( $key );
    }

    public static function save()
    {
        $session = self::getSession();

        foreach ( self::$affected as $name ) {
            $value = isset( $session[ $name ] ) ? $session[ $name ] : null;
            $db_session = new Entities\Session();
            $db_session->loadBy( array( 'token' => self::sessionId(), 'name' => $name ) );
            $db_session
                ->setToken( self::sessionId() )
                ->setName( $name )
                ->setValue( json_encode( $value ) )
                ->setExpire( date_create( current_time( 'mysql' ) )->modify( '+1 day' )->format( 'Y-m-d H:i:s' ) )
                ->save();
        }
    }

    private static function formVar( $form_id )
    {
        return 'form-' . $form_id;
    }

    private static function touch( $name )
    {
        if ( ! in_array( $name, self::$affected, true ) ) {
            self::$affected[] = $name;
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