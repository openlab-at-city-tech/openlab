<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet;

use Bookly\Lib;
use Bookly\Frontend\Modules\MobileStaffCabinet\Api\Exceptions;

class Ajax extends Lib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'anonymous' );
    }

    public static function mobileStaffCabinet()
    {
        try {
            $auth = Lib\Entities\Auth::query()->where( 'token', self::parameter( 'access_key' ) )->findOne();

            $staff_or_wp_user = self::findUserByAuth( $auth );

            $request = new Lib\Base\Request();
            $handler = Api\HandlerFactory::createLocator()
                ->getHandler( $staff_or_wp_user, $request );
            
            $response = $handler( $request );

            get_option( Lib\Utils\Log::OPTION_MOBILE_STAFF_CABINET ) && self::logDebug( $handler, $request, $response );
        } catch ( \Error $e ) {
            $response = self::getThrowableResponse( $e );
            self::logException( $e );
        } catch ( \Exception $e ) {
            $response = self::getThrowableResponse( $e );
            self::logException( $e );
        }

        $response->render();
    }

    /**
     * @inheritDoc
     */
    protected static function hasAccess( $action )
    {
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

    /**
     * @param Api\Handlers\HandlerInterface $handler
     * @param Lib\Base\Request $request
     * @param Api\Response $response
     * @return void
     */
    protected static function logDebug( Api\Handlers\HandlerInterface $handler, Lib\Base\Request $request, Api\Response $response )
    {
        try {
            $class = get_class( $handler );

            Lib\Utils\Log::tempPut( Lib\Utils\Log::OPTION_MOBILE_STAFF_CABINET, $class . '::' . $handler->getResolverMethodName(), null, '<pre>' . json_encode( array(
                    'API' => $request->getHeaders()->getGreedy( 'X-Bookly-Api-Version' ),
                    'role' => $handler->getRole(),
                    'request.body' => $request->getAll(),
                    'request.headers' => $request->getHeaders()->getAll(),
                    'response.body' => $response->getData(),
                ), 128 ) . '</pre>' );
        } catch ( \Exception $e ) {
        }
    }

    /**
     * @param \Exception $e
     * @return void
     */
    protected static function logException( $e )
    {
        if ( $e instanceof Exceptions\HandleException ) {
            try {
                Lib\Utils\Log::put( Lib\Utils\Log::ACTION_ERROR,
                    $e->getClassName() ?: 'Mobile Staff Cabinet API',
                    null,
                    '<pre>' . json_encode( $e->getRequest()->getAll(), 128 ) . '</pre>',
                    'Client API: ' . $e->getRequest()->getHeaders()->getGreedy( 'X-Bookly-Api-Version', 'missing' ),
                    $e->getInfo()
                );
            } catch ( \Exception $e ) {
            }
        }
    }

    /**
     * @param \Throwable $throwable
     * @return Api\Response
     */
    protected static function getThrowableResponse( $throwable )
    {
        $response = new Api\Response( null );
        $response->setHttpStatus( 400 );

        $data = array(
            'error' => array(
                'code' => 400,
                'message' => $throwable->getMessage(),
            ),
        );
        if ( $throwable instanceof Exceptions\ApiException ) {
            $response->setHttpStatus( $throwable->getHttpStatus() );
            if ( $throwable->getErrorData() ) {
                $data['error']['data'] = $throwable->getErrorData();
            }
        } elseif ( $throwable instanceof Exceptions\ParameterException ) {
            $data['error']['data'] = $throwable->getParameter();
        } elseif ( ( $throwable instanceof Exceptions\BooklyException ) || ( $throwable instanceof Exceptions\HandleException ) ) {
            $data['error']['message'] = $throwable->getMessage();
        } else {
            $data['error']['message'] = 'ERROR';
        }
        $response->setData( $data );

        return $response;
    }

    /**
     * @param Lib\Entities\Auth|null $auth
     * @return \WP_User|Lib\Entities\Staff
     */
    protected static function findUserByAuth( $auth )
    {
        if ( $auth === null ) {
            throw new Exceptions\ApiException( 'Unauthorized', 401 );
        }

        // Check staff access
        if ( $auth->getStaffId() ) {
            $staff = Lib\Entities\Staff::find( $auth->getStaffId() );
            if ( $staff ) {
                return $staff;
            }
        } // Check admin/supervisor access
        elseif ( $auth->getWpUserId() ) {
            $wp_user = get_user_by( 'id', $auth->getWpUserId() );
            $user_id = $auth->getWpUserId();

            if ( user_can( $user_id, 'manage_bookly' ) ||
                user_can( $user_id, 'manage_options' ) ||
                user_can( $user_id, 'manage_bookly_appointments' ) ) {
                return $wp_user;
            }
        }

        throw new Exceptions\ApiException( 'Unauthorized', 401 );
    }
}