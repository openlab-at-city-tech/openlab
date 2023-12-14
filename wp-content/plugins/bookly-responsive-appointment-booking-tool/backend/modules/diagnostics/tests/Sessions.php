<?php
namespace Bookly\Backend\Modules\Diagnostics\Tests;

use Bookly\Lib;

class Sessions extends Test
{
    protected $slug = 'check-sessions';
    protected $hidden = true;

    protected $session_value1 = '0123456789';
    protected $session_value2 = '9876543210';

    public function __construct()
    {
        $this->title = __( 'PHP Sessions', 'bookly' );
        $this->description = sprintf( __( 'This test checks if PHP sessions are enabled. Bookly needs PHP sessions to work correctly. For more information about PHP sessions, please check the official PHP documentation %s.', 'bookly' ), '<a href="https://www.php.net/manual/en/intro.session.php">php.net/manual/en/intro.session.php</a>' );
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        Lib\Session::set( 'test-session-value', $this->session_value1 );

        return true;
    }

    public function ajax1()
    {
        if ( Lib\Session::get( 'test-session-value' ) === $this->session_value1 ) {
            Lib\Session::set( 'test-session-value', $this->session_value2 );
            wp_send_json_success();
        }
        $error = 'To enable PHP sessions, please check the official PHP documentation';
        wp_send_json_error( array( 'errors' => array( $error ) ) );
    }

    public function ajax2()
    {
        if ( Lib\Session::get( 'test-session-value' ) === $this->session_value2 ) {
            Lib\Session::destroy( 'test-session-value' );
            wp_send_json_success();
        }

        $error = 'To enable PHP sessions, please check the official PHP documentation';
        wp_send_json_error( array( 'errors' => array( $error ) ) );
    }
}