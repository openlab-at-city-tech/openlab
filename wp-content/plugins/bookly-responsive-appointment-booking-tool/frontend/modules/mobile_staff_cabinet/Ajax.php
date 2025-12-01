<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
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
        try {
            $auth = Lib\Entities\Auth::query()->where( 'token', self::parameter( 'access_key' ) )->findOne();
            $request = new Lib\Base\Request();
            $handler = Api\HandlerFactory::create( $auth, $request );
            $response = $handler->process();

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
     * @param Api\ApiHandler $handler
     * @param Lib\Base\Request $request
     * @param Api\IResponse $response
     * @return void
     */
    protected static function logDebug( Api\ApiHandler $handler, Lib\Base\Request $request, Api\IResponse $response )
    {
        try {
            $class = get_class( $handler );

            Lib\Utils\Log::tempPut( Lib\Utils\Log::OPTION_MOBILE_STAFF_CABINET, $class . '::' . $handler->getProcessMethod(), null, '<pre>' . json_encode( array(
                    'API' => $request->getHeaders()->getGreedy( 'X-Bookly-Api-Version' ),
                    'role' => $handler->getRole(),
                    'request' => $request->getAll(),
                    'request.headers' => $request->getHeaders()->getAll(),
                    'response' => $response->getData(),
                ), 128 ) . '</pre>' );
        } catch ( \Exception $e ) {
        }
    }

    /**
     * @param \Exception $e
     * @return void
     */
    protected static function logException( \Exception $e )
    {
        if ( $e instanceof Api\Exceptions\HandleException ) {
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
     * @return Api\IResponse
     */
    protected static function getThrowableResponse( $throwable )
    {
        $response = new Api\Response();
        $response->setHttpStatus( 400 );

        $data = array(
            'error' => array(
                'code' => 400,
                'message' => $throwable->getMessage(),
            ),
        );
        if ( $throwable instanceof Api\Exceptions\ApiException ) {
            $response->setHttpStatus( $throwable->getHttpStatus() );
            if ( $throwable->getErrorData() ) {
                $data['error']['data'] = $throwable->getErrorData();
            }
        } elseif ( $throwable instanceof Api\Exceptions\ParameterException ) {
            $data['error']['data'] = $throwable->getParameter();
        } elseif ( ( $throwable instanceof Api\Exceptions\BooklyException ) || ( $throwable instanceof Api\Exceptions\HandleException ) ) {
            $data['error']['message'] = $throwable->getMessage();
        } else {
            $data['error']['message'] = 'ERROR';
        }
        $response->setData( $data );

        return $response;
    }
}