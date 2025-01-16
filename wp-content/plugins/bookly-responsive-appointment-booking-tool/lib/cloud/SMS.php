<?php
namespace Bookly\Lib\Cloud;

use Bookly\Lib\Entities\SmsLog;
use Bookly\Lib\Utils;

class SMS extends Base
{
    const CANCEL_SENDER_ID    = '/1.0/users/%token%/sender-ids/cancel'; //GET
    const CHANGE_SMS_STATUS   = '/1.0/users/%token%/sms';               //PATCH
    const GET_PRICES          = '/1.0/prices';                          //GET
    const GET_SENDER_IDS_LIST = '/1.0/users/%token%/sender-ids';        //GET
    const GET_SMS_LIST        = '/1.1/users/%token%/sms-list';          //POST
    const REQUEST_SENDER_ID   = '/1.0/users/%token%/sender-ids';        //POST
    const RESET_SENDER_ID     = '/1.0/users/%token%/sender-ids/reset';  //GET
    const SEND_SMS            = '/1.1/users/%token%/sms';               //POST

    /** @var array */
    protected $sender_id;

    /**
     * Send SMS.
     *
     * @param string $phone_number
     * @param string $message
     * @param string $impersonal_message
     * @param int    $type_id
     * @return bool
     */
    public function sendSms( $phone_number, $message, $impersonal_message, $type_id = null )
    {
        if ( $this->api->getToken() ) {
            $data = array(
                'message' => $message,
                'impersonal_message' => $impersonal_message,
                'phone' => self::normalizePhoneNumber( $phone_number ),
                'type' => $type_id,
            );
            if ( $data['phone'] != '' ) {
                $response = $this->api->sendPostRequest( self::SEND_SMS, $data );

                $sl = new SmsLog();
                $sl->setPhone( $data['phone'] )
                    ->setMessage( $message )
                    ->setImpersonalMessage( $impersonal_message )
                    ->setRefId( $response ? $response['ref_id'] : null )
                    ->setTypeId( $type_id )
                    ->save();

                if ( $response ) {
                    if ( array_key_exists( 'notify_low_balance', $response ) && $response['notify_low_balance'] ) {
                        $this->api->dispatch( Events::ACCOUNT_LOW_BALANCE );
                    }
                    if ( array_key_exists( 'gateway_status' , $response ) ) {
                        if ( in_array( $response['gateway_status'], array( 1, 10, 11, 12, 13 ) ) ) {  /* @see SMS::getSmsList */

                            return true;
                        } elseif ( $response['gateway_status'] == 3 ) {
                            $this->api->addError( __( 'You don\'t have enough Bookly Cloud credits to send this message. Please add funds to your balance and try again.', 'bookly' ) );
                        } else {
                            $this->api->addError( __( 'Failed to send SMS.', 'bookly' ) );
                        }
                    }
                }
            } else {
                $this->api->addError( __( 'Phone number is empty.', 'bookly' ) );
            }
        }

        return false;
    }

    /**
     * Change SMS product status.
     *
     * @param bool $status
     * @return bool
     */
    public function changeSmsStatus( $status )
    {
        $data = array(
            'status' => $status
        );

        $response = $this->api->sendPatchRequest( self::CHANGE_SMS_STATUS, $data );
        if ( $response ) {
            update_option( 'bookly_cloud_account_products', $response['products'] );

            return true;
        }
        return false;
    }

    /**
     * Return phone_number in international format without +
     *
     * @param $phone_number
     * @return string
     */
    public static function normalizePhoneNumber( $phone_number )
    {
        // Remove everything except numbers and "+".
        $phone_number = preg_replace( '/[^\d\+]/', '', $phone_number );

        if ( strpos( $phone_number, '+' ) === 0 ) {
            // ok.
        } elseif ( strpos( $phone_number, '00' ) === 0 ) {
            $phone_number = ltrim( $phone_number, '0' );
        } else {
            // Default country code can contain not permitted characters. Remove everything except numbers.
            $phone_number = ltrim( preg_replace( '/\D/', '', get_option( 'bookly_cst_default_country_code', '' ) ), '0' )  . ltrim( $phone_number, '0' );
        }

        // Finally remove "+" if there were any among digits.
        return str_replace( '+', '', $phone_number );
    }

    /**
     * Get SMS list.
     *
     * @param int $start
     * @param int $length
     * @param array $filter
     * @return array
     */
    public function getSmsList( $start, $length, array $filter )
    {
        $data = array();
        $filtered = 0;
        if ( $this->api->getToken() ) {
            $response = $this->api->sendPostRequest(
                self::GET_SMS_LIST,
                compact( 'start', 'length', 'filter' )
            );
            if ( $response ) {
                $refs = SmsLog::query()->whereIn( 'ref_id', $response['refs'] ?: array() )->fetchCol( 'ref_id' );

                array_walk( $response['list'], function( &$item ) use ( $refs ) {
                    $date_time = Utils\DateTime::UTCToWPTimeZone( $item['datetime'] );
                    $item['date'] = Utils\DateTime::formatDate( $date_time );
                    $item['time'] = Utils\DateTime::formatTime( $date_time );
                    $item['message'] = nl2br( preg_replace( '/([^\s]{50})+/U', '$1 ', htmlspecialchars( $item['message'] ) ) );
                    $item['phone']   = '+' . $item['phone'];
                    $item['charge'] = $item['charge'] === null ? '' : rtrim( $item['charge'], '0' );
                    $item['info'] = $item['info'] === null ? '' : nl2br( htmlspecialchars( $item['info'] ) );
                    switch ( $item['status'] ) {
                        case 1:
                        case 10:
                            $item['status'] = __( 'Queued', 'bookly' );
                            $item['charge'] = '$' . $item['charge'];
                            break;
                        case 2:
                        case 16:
                            $item['status'] = __( 'Error', 'bookly' );
                            $item['charge'] = '';
                            break;
                        case 3:
                            $item['status'] = __( 'Out of credit', 'bookly' );
                            $item['charge'] = '';
                            break;
                        case 4:
                            $item['status'] = __( 'Country out of service', 'bookly' );
                            $item['charge'] = '';
                            break;
                        case 5:
                            $item['status'] = __( 'Blocked', 'bookly' );
                            $item['charge'] = '';
                            break;
                        case 11:
                            $item['status'] = __( 'Sending', 'bookly' );
                            $item['charge'] = '$' . $item['charge'];
                            break;
                        case 12:
                            $item['status'] = __( 'Sent', 'bookly' );
                            $item['charge'] = '$' . $item['charge'];
                            break;
                        case 13:
                            $item['status'] = __( 'Delivered', 'bookly' );
                            $item['charge'] = '$' . $item['charge'];
                            break;
                        case 14:
                            $item['status'] = __( 'Failed', 'bookly' );
                            if ( $item['charge'] != '' ) {
                                $item['charge'] = '$' . $item['charge'];
                            }
                            break;
                        case 15:
                            $item['status'] = __( 'Undelivered', 'bookly' );
                            $item['charge'] = '$' . $item['charge'];
                            break;
                        default:
                            $item['status'] = __( 'Error', 'bookly' );
                            $item['charge'] = '';
                    }
                    $item['resend'] = in_array( $item['id'], $refs );
                } );

                $this->setUndeliveredSmsCount( 0 );

                $data = $response['list'];
                $filtered = $response['filtered'];
            }
        }

        return array(
            'data' => $data,
            'recordsFiltered' => $filtered,
        );
    }

    /**
     * Get Price list.
     *
     * @return array
     */
    public function getPriceList()
    {
        $response = $this->api->sendGetRequest( self::GET_PRICES );
        if ( $response ) {
            return $response;
        }

        return array( 'success' => false, 'list' => array() );
    }

    /**
     * Get list of all requests for SENDER IDs.
     *
     * @return array
     */
    public function getSenderIdsList()
    {
        $response = $this->api->sendGetRequest( self::GET_SENDER_IDS_LIST );
        if ( $response ) {
            $response['pending'] = null;
            foreach ( $response['list'] as &$item ) {
                $item['date'] = Utils\DateTime::formatDate( Utils\DateTime::UTCToWPTimeZone( $item['date'] ) );
                $item['status_date'] = $item['status_date'] ? Utils\DateTime::formatDate( Utils\DateTime::UTCToWPTimeZone( $item['status_date'] ) ) : '';
                switch ( $item['status'] ) {
                    case 0:
                        $item['status'] = __( 'Pending', 'bookly' );
                        $response['pending'] = $item['name'];
                        break;
                    case 1:
                        $item['status'] = __( 'Approved', 'bookly' );
                        break;
                    case 2:
                        $item['status'] = __( 'Declined', 'bookly' );
                        break;
                    case 3:
                        $item['status'] = __( 'Cancelled', 'bookly' );
                        break;
                }
            }

            return $response;
        }

        return array( 'success' => false, 'list' => array(), 'pending' => null );
    }

    /**
     * Request new SENDER ID.
     *
     * @param string $sender_id
     * @return array|false
     */
    public function requestSenderId( $sender_id )
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendPostRequest( self::REQUEST_SENDER_ID, array( 'name' => $sender_id ) );
            if ( $response ) {

                return $response;
            }
        }

        return false;
    }

    /**
     * Cancel request for SENDER ID.
     *
     * @return bool
     */
    public function cancelSenderId()
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendGetRequest( self::CANCEL_SENDER_ID );
            if ( $response ) {

                return true;
            }
        }

        return false;
    }

    /**
     * Reset SENDER ID to default (Bookly).
     *
     * @return bool
     */
    public function resetSenderId()
    {
        if ( $this->api->getToken() ) {
            $response = $this->api->sendGetRequest( self::RESET_SENDER_ID );
            if ( $response ) {

                return true;
            }
        }

        return false;
    }

    /**
     * Get sender ID.
     *
     * @return string
     */
    public function getSenderId()
    {
        return $this->sender_id['value'];
    }

    /**
     * Get sender ID approval date.
     *
     * @return string
     */
    public function getSenderIdApprovalDate()
    {
        return $this->sender_id['approved_at'];
    }

    /**
     * Set number of undelivered sms.
     *
     * @param int $count
     */
    public function setUndeliveredSmsCount( $count )
    {
        update_option( 'bookly_sms_undelivered_count', (int) $count );
    }

    /**
     * Get number of undelivered sms.
     *
     * @return int
     */
    public static function getUndeliveredSmsCount()
    {
        return (int) get_option( 'bookly_sms_undelivered_count', 0 );
    }

    /**
     * @inheritDoc
     */
    public function translateError( $error_code )
    {
        switch ( $error_code ) {
            case 'ERROR_INVALID_SENDER_ID': return __( 'Incorrect sender ID', 'bookly' );
            default: return null;
        }
    }

    /**
     * @inheritDoc
     */
    protected function setupListeners()
    {
        $sms = $this;

        $this->api->listen( Events::ACCOUNT_PROFILE_LOADED, function ( $response ) use ( $sms ) {
            if ( isset( $response['account'][ Account::PRODUCT_SMS_NOTIFICATIONS ] ) ) {
                $sms->sender_id = $response['account'][ Account::PRODUCT_SMS_NOTIFICATIONS ]['sender_id'];
                $sms->setUndeliveredSmsCount( $response['account'][ Account::PRODUCT_SMS_NOTIFICATIONS ]['undelivered_count'] );
            } else {
                $sms->setUndeliveredSmsCount( 0 );
            }
        } );

        $this->api->listen( Events::ACCOUNT_PROFILE_NOT_LOADED, function () use ( $sms ) {
            $sms->setUndeliveredSmsCount( 0 );
        } );

        $this->api->listen( Events::ACCOUNT_LOGGED_OUT, function () use ( $sms ) {
            $sms->setUndeliveredSmsCount( 0 );
        } );
    }
}