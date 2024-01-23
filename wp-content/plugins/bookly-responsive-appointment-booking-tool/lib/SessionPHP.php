<?php
namespace Bookly\Lib;

abstract class SessionPHP extends Session
{
    public static function initSession()
    {
        static $initialized;
        if ( ! $initialized ) {
            if ( get_option( 'bookly_gen_prevent_session_locking' ) ) {
                self::writeClose();
                // For PHP 7.2+ we need to re-implement session cookies to avoid errors and warnings
                @ini_set( 'session.use_only_cookies', false );
                @ini_set( 'session.use_cookies', false );
                @ini_set( 'session.use_trans_sid', false );
                @ini_set( 'session.cache_limiter', null );

                if ( array_key_exists( session_name(), $_COOKIE ) ) {
                    session_id( $_COOKIE[ session_name() ] );
                } else {
                    self::start();
                    @setcookie( session_name(), session_id(), 0, '/', '', false, true );
                    self::writeClose();
                }
            } elseif ( ! session_id() ) {
                // fix loopback request failure
                if ( // WP 4.9+ plugin or theme editor
                    ! isset( $_GET['wp_scrape_key'] )
                    &&  // WP Site Health
                    ! ( isset( $_POST['action'] ) && strncmp( $_POST['action'], 'health-check-', 13 ) === 0 )
                ) {
                    // Start session.
                    @ini_set( 'session.cookie_httponly', 1 );
                    self::start();
                }
            }
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
        $result = $default;
        self::start();
        if ( isset ( $_SESSION['bookly'][ $name ] ) ) {
            $result = $_SESSION['bookly'][ $name ];
        }
        self::writeClose();

        return $result;
    }

    /**
     * Set value to session.
     *
     * @param string $name
     * @param mixed $value
     */
    public static function set( $name, $value )
    {
        self::start();
        $_SESSION['bookly'][ $name ] = $value;
        self::writeClose();
    }

    /**
     * Check if a named value exists in session.
     *
     * @param string $name
     * @return bool
     */
    public static function has( $name )
    {
        self::start();
        $has = isset ( $_SESSION['bookly'][ $name ] );
        self::writeClose();

        return $has;
    }

    /**
     * Destroy value in session.
     *
     * @param string $name
     */
    public static function destroy( $name )
    {
        self::start();
        unset ( $_SESSION['bookly'][ $name ] );
        self::writeClose();
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
        self::start();
        if ( isset ( $_SESSION['bookly']['forms'][ $form_id ][ $name ] ) ) {
            $result = $_SESSION['bookly']['forms'][ $form_id ][ $name ];
        }
        self::writeClose();

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
        self::start();
        $_SESSION['bookly']['forms'][ $form_id ][ $name ] = $value;
        self::writeClose();
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
        self::start();
        $has = isset ( $_SESSION['bookly']['forms'][ $form_id ][ $name ] );
        self::writeClose();

        return $has;
    }

    /**
     * Get data of all booking forms.
     *
     * @return array
     */
    public static function getAllFormsData()
    {
        $data = array();
        self::start();
        if ( isset ( $_SESSION['bookly']['forms'] ) ) {
            $data = $_SESSION['bookly']['forms'];
        }
        self::writeClose();

        return $data;
    }

    public static function save()
    {
        self::start();
        self::writeClose();
    }

    /**
     * Destroy named variable in booking form data.
     *
     * @param string $form_id
     * @param string $name
     */
    public static function destroyFormVar( $form_id, $name )
    {
        self::start();
        unset ( $_SESSION['bookly']['forms'][ $form_id ][ $name ] );
        self::writeClose();
    }

    /**
     * Destroy all data of a booking form.
     *
     * @param string $form_id
     */
    public static function destroyFormData( $form_id )
    {
        self::start();
        unset ( $_SESSION['bookly']['forms'][ $form_id ] );
        self::writeClose();
    }

    /**
     * Start session
     */
    private static function start()
    {
        @session_start();
    }

    /**
     * Write session data and end session if it is set to do so
     */
    private static function writeClose()
    {
        if ( get_option( 'bookly_gen_prevent_session_locking' ) ) {
            session_write_close();
        }
    }
}