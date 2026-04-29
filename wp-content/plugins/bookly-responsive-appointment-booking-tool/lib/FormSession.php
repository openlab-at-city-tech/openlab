<?php
namespace Bookly\Lib;

abstract class FormSession
{
    /**
     * @return string
     */
    public static function createSession()
    {
        $form_session = new Entities\FormSession();
        $form_session->save();

        return $form_session->getToken();
    }

    /**
     * @param $token
     * @return array|mixed
     */
    public static function loadSession( $token )
    {
        $result = Entities\FormSession::query()
            ->where( 'token', $token )
            ->fetchVar( 'value' );

        return $result ? json_decode( $result, true ) : array();
    }

    /**
     * @param $token
     * @param $value
     * @param int $expire Expire in minutes
     * @return void
     */
    public static function saveSession( $token, $value, $expire = 30 )
    {
        try {
            Entities\FormSession::query()
                ->update()
                ->set( 'value', json_encode( $value ) )
                ->set( 'expire', date_create( current_time( 'mysql' ) )->modify( '+' . $expire . ' minutes' )->format( 'Y-m-d H:i:s' ) )
                ->where( 'token', $token )
                ->execute();
        } catch ( \Exception $e ) {
        }
    }

    /**
     * @param $token
     * @return void
     */
    public static function clearSession( $token )
    {
        try {
            Entities\FormSession::query()
                ->delete()
                ->where( 'token', $token )
                ->execute();
        } catch ( \Exception $e ) {
        }
    }

    /**
     * @return void
     */
    public static function clearExpiredSessions()
    {
        Entities\FormSession::query()
            ->delete()
            ->whereLt( 'expire', current_time( 'mysql' ) )
            ->execute();
    }
}