<?php
namespace Bookly\Backend\Components\Cloud\Account;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * Forgot password.
     */
    public static function forgotPassword()
    {
        $cloud    = Lib\Cloud\API::getInstance();
        $step     = self::parameter( 'step' );
        $code     = self::parameter( 'code' );
        $username = self::parameter( 'username' );
        $password = self::parameter( 'password' );
        $result   = $cloud->account->forgotPassword( $username, $step, $code, $password );
        if ( $result === false ) {
            $errors = $cloud->getErrors();
            wp_send_json_error( array( 'code' => key( $errors ), 'message' => current( $errors ) ) );
        } else {
            wp_send_json_success();
        }
    }

    /**
     * Login.
     */
    public static function cloudLogin()
    {
        $cloud  = Lib\Cloud\API::getInstance();
        $result = $cloud->account->login( self::parameter( 'username' ), self::parameter( 'password' ) );
        if ( $result ) {
            wp_send_json_success();
        }

        wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
    }

    /**
     * Registration.
     */
    public static function cloudRegister()
    {
        $cloud = Lib\Cloud\API::getInstance();

        if ( self::parameter( 'accept_tos', false ) ) {
            $response = $cloud->account->register(
                self::parameter( 'username' ),
                self::parameter( 'password' ),
                self::parameter( 'password_repeat' ),
                self::parameter( 'country' )
            );
            if ( $response ) {
                update_option( 'bookly_cloud_token', $response['token'] );

                wp_send_json_success();
            }
        } else {
            wp_send_json_error( array( 'message' => __( 'Please accept terms and conditions.', 'bookly' ) ) );
        }

        wp_send_json_error( array( 'message' => current( $cloud->getErrors() ) ) );
    }

    /**
     * Logout.
     */
    public static function cloudLogout()
    {
        Lib\Cloud\API::getInstance()->account->logout();

        wp_send_json_success();
    }

    /**
     * Apply confirmation code.
     */
    public static function applyConfirmationCode()
    {
        $code = self::parameter( 'code' );

        $result = Lib\Cloud\API::getInstance()->account->confirmEmail( $code );
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => current( Lib\Cloud\API::getInstance()->getErrors() ) ) );
        } else {
            wp_send_json( $result );
        }
    }

    /**
     * Resend confirmation code.
     */
    public static function resendConfirmationCode()
    {
        $result = Lib\Cloud\API::getInstance()->account->resendConfirmation();
        if ( $result === false ) {
            wp_send_json_error( array( 'message' => current( Lib\Cloud\API::getInstance()->getErrors() ) ) );
        } else {
            wp_send_json( $result );
        }
    }

    /**
     * Dismiss confirm email modal.
     */
    public static function dismissConfirmEmail()
    {
        update_user_meta( get_current_user_id(), 'bookly_dismiss_cloud_confirm_email', 1 );

        wp_send_json_success();
    }
}