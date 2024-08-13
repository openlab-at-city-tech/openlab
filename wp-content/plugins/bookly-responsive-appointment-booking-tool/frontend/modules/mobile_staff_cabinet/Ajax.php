<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /** @var Lib\Entities\Staff */
    protected static $staff;

    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'anonymous' );
    }

    /**
     * Get resources
     */
    public static function mobileStaffCabinet()
    {
        $json = file_get_contents( 'php://input' );
        $data = json_decode( $json, true ) ?: array();
        $params = isset( $data['params'] ) ? $data['params'] : array();
        $response = new Response10( self::$staff, $params );
        $action = array_key_exists( 'action', $data ) ? $data['action'] : null;
        if ( self::$staff ) {
            try {
                switch ( $data['resource'] ) {
                    case 'init':
                        $response->init();
                        break;
                    case 'customers':
                        $response->customers();
                        break;
                    case 'customer':
                        if ( $action == 'save' ) {
                            $response->saveCustomer();
                        }
                        break;
                    case 'appointments':
                        $response->appointments();
                        break;
                    case 'appointment':
                        if ( $action == 'save' ) {
                            $response->saveAppointment();
                        } else {
                            $response->appointment();
                        }
                        break;
                    case 'check-appointment-time':
                        $response->checkAppointmentTime();
                        break;
                    case 'slots':
                        $response->slots();
                        break;
                    case 'services':
                        $response->services();
                        break;
                    default:
                        $response->setError( '400', 'UNKNOWN_REQUEST', 400 );
                }
            } catch ( ParameterException $e ) {
                $response->setError( '400', 'INVALID_PARAMETER', 400, array( $e->getParameter() => $e->getValue() ) );
            } catch ( \Exception $e ) {
                $response->setError( '400', 'ERROR' );
            }
        } else {
            $response->setError( '401', 'UNAUTHORIZED_REQUEST', 401 );
        }

        $response->render();
    }

    /**
     * @inheritDoc
     */
    protected static function hasAccess( $action )
    {
        $access_key = self::parameter( 'access_key' );
        if ( $access_key ) {
            self::$staff = Lib\Entities\Staff::query()->where( 'cloud_msc_token', $access_key )->findOne();
        }

        return true;
    }

    /**
     * Override parent method to exclude actions from CSRF token verification.
     *
     * @param string $action
     * @return bool
     */
    protected static function csrfTokenValid( $action = null )
    {
        return true;
    }
}