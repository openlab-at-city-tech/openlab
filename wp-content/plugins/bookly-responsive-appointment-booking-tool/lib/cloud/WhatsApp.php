<?php
namespace Bookly\Lib\Cloud;

use Bookly\Lib\Utils;

/**
 * Class WhatsApp
 *
 * @package Bookly\Lib\Cloud
 */
class WhatsApp extends Product
{
    const ACTIVATE                = '/1.0/users/%token%/products/whatsapp/activate';        //POST
    const DEACTIVATE_NOW          = '/1.0/users/%token%/products/whatsapp/deactivate/now';  //POST
    const DEACTIVATE_NEXT_RENEWAL = '/1.0/users/%token%/products/whatsapp/deactivate/next-renewal';   //POST
    const REVERT_CANCEL           = '/1.0/users/%token%/products/whatsapp/revert-cancel';   //POST
    const SETTINGS                = '/1.0/users/%token%/products/whatsapp/settings';        //POST
    const TEMPLATES               = '/1.0/users/%token%/products/whatsapp/templates';       //GET
    const MESSAGES                = '/1.0/users/%token%/products/whatsapp/messages';        //GET
    const MESSAGE                 = '/1.0/users/%token%/products/whatsapp/message';         //POST

    /** @var string */
    public $access_token;
    /** @var string */
    public $phone_id;
    /** @var string */
    public $business_account_id;

    /**
     * @param string $access_token
     * @param string $phone_id
     * @param string $business_account_id
     * @return array|false
     */
    public function setSettings( $access_token = null, $phone_id = null, $business_account_id = null )
    {
        return $this->api->sendPostRequest( self::SETTINGS, compact( 'access_token', 'phone_id', 'business_account_id' ) );
    }

    /**
     * @inheritDoc
     */
    protected function setupListeners()
    {
        $whatsapp = $this;

        $this->api->listen( Events::ACCOUNT_PROFILE_LOADED, function ( $response ) use ( $whatsapp ) {
            if ( isset( $response['account'][ Account::PRODUCT_WHATSAPP ] ) ) {
                $whatsapp->access_token = $response['account'][ Account::PRODUCT_WHATSAPP ]['access_token'];
                $whatsapp->phone_id = $response['account'][ Account::PRODUCT_WHATSAPP ]['phone_id'];
                $whatsapp->business_account_id = $response['account'][ Account::PRODUCT_WHATSAPP ]['business_account_id'];
            }
        } );
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        return $this->api->sendGetRequest( self::TEMPLATES );
    }

    /**
     * Send message
     *
     * @param string $phone_number
     * @param array $message
     * @return false
     */
    public function send( $phone_number, $message )
    {
        if ( $this->api->getToken() && $this->api->account->productActive( Account::PRODUCT_WHATSAPP ) ) {
            $data = array(
                'template' => $message,
                'phone' => \Bookly\Lib\Cloud\SMS::normalizePhoneNumber( $phone_number ),
            );
            if ( $data['phone'] != '' ) {
                $response = $this->api->sendPostRequest( self::MESSAGE, $data );
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
     * Get messages list.
     *
     * @param null $start_date
     * @param null $end_date
     * @return array
     */
    public function getMessagesList( $start_date = null, $end_date = null )
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendGetRequest(
                self::MESSAGES,
                compact( 'start_date', 'end_date' )
            );
            if ( $response ) {
                array_walk( $response['list'], function( &$item ) {
                    $date_time = Utils\DateTime::UTCToWPTimeZone( $item['datetime'] );
                    $item['date'] = Utils\DateTime::formatDate( $date_time );
                    $item['time'] = Utils\DateTime::formatTime( $date_time );
                    $item['phone'] = '+' . $item['phone'];
                    switch ( $item['status'] ) {
                        case 'completed':
                            $item['charge'] = '$' . $item['charge'];
                            break;
                    }
                } );

                return $response;
            }
        }

        return array( 'success' => false, 'list' => array() );
    }
}