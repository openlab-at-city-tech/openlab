<?php
namespace Bookly\Lib\Cloud;

use Bookly\Lib\Utils;

/**
 * Class Voice
 *
 * @package Bookly\Lib\Cloud
 */
class Voice extends Product
{
    const ACTIVATE       = '/1.0/users/%token%/products/voice/activate';        //POST
    const DEACTIVATE_NOW = '/1.0/users/%token%/products/voice/deactivate/now';  //POST
    const SETTINGS       = '/1.0/users/%token%/products/voice/settings';        //POST
    const CALL           = '/1.0/users/%token%/products/voice/call';            //POST
    const CALLS          = '/1.0/users/%token%/products/voice/calls';           //GET
    const PRICES         = '/1.0/voice/prices';                                 //GET

    public $language;

    /**
     * @param string $language
     * @return bool
     */
    public function setSettings( $language )
    {
        return $this->api->sendPostRequest( self::SETTINGS, compact( 'language' ) );
    }

    /**
     * Make a call.
     *
     * @param string $phone_number
     * @param string $message
     * @param string $impersonal_message
     * @return bool
     */
    public function call( $phone_number, $message, $impersonal_message )
    {
        if ( $this->api->getToken() && $this->api->account->productActive( Account::PRODUCT_VOICE ) ) {
            $data = array(
                'message' => $message,
                'impersonal_message' => $impersonal_message,
                'phone' => \Bookly\Lib\Cloud\SMS::normalizePhoneNumber( $phone_number ),
            );
            if ( $data['phone'] != '' ) {
                $response = $this->api->sendPostRequest( self::CALL, $data );
                if ( $response ) {
                    return true;
                }
            } else {
                $this->api->addError( __( 'Phone number is empty.', 'bookly' ) );
            }
        }

        return false;
    }

    /**
     * Get calls list.
     *
     * @param null $start_date
     * @param null $end_date
     * @return array
     */
    public function getCallsList( $start_date = null, $end_date = null )
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendGetRequest(
                self::CALLS,
                compact( 'start_date', 'end_date' )
            );
            if ( $response ) {
                array_walk( $response['list'], function( &$item ) {
                    $date_time = Utils\DateTime::UTCToWPTimeZone( $item['datetime'] );
                    $item['date']    = Utils\DateTime::formatDate( $date_time );
                    $item['time']    = Utils\DateTime::formatTime( $date_time );
                    $item['message'] = nl2br( preg_replace( '/([^\s]{50})+/U', '$1 ', htmlspecialchars( $item['message'] ) ) );
                    $item['phone']   = '+' . $item['phone'];
                    switch ( $item['status'] ) {
                        case 'completed':
                            $item['charge'] = '$' . $item['charge'];
                            $item['duration'] = sprintf( __( '%d min', 'bookly' ), $item['duration'] );
                            break;
                    }
                } );

                return $response;
            }
        }

        return array( 'success' => false, 'list' => array() );
    }

    /**
     * Get Price list.
     *
     * @return array
     */
    public function getPriceList()
    {
        $response = $this->api->sendGetRequest( self::PRICES );
        if ( $response ) {
            return $response;
        }

        return array( 'success' => false, 'list' => array() );
    }

    /**
     * @inheritDoc
     */
    protected function setupListeners()
    {
        $voice = $this;

        $this->api->listen( Events::ACCOUNT_PROFILE_LOADED, function ( $response ) use ( $voice ) {
            if ( isset( $response['account'][ Account::PRODUCT_VOICE ] ) ) {
                $voice->language = $response['account'][ Account::PRODUCT_VOICE ]['language'];
            }
        } );
    }
}