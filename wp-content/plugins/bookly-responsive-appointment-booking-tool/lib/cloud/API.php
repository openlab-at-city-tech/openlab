<?php
namespace Bookly\Lib\Cloud;

use Bookly\Backend\Modules;
use Bookly\Lib\Base\Cache;
use Bookly\Lib\Plugin;

/**
 * Class API
 *
 * @package Bookly\Lib\Cloud
 */
class API extends Cache
{
    const API_URL = 'https://cloud.bookly.pro';

    /** @var string */
    protected $token;
    /** @var array */
    protected $errors = array();
    /** @var array */
    protected $errorTranslators = array();
    /** @var array */
    protected $listeners = array();
    /** @var int */
    protected $timeout = 30;

    /** @var General */
    public $general;
    /** @var Account */
    public $account;
    /** @var SMS */
    public $sms;
    /** @var Stripe */
    public $stripe;
    /** @var Zapier */
    public $zapier;
    /** @var Cron */
    public $cron;
    /** @var Voice */
    public $voice;
    /** @var \BooklyPro\Lib\Cloud\Square */
    public $square;
    /** @var \BooklyPro\Lib\Cloud\Gift */
    public $gift;
    /** @var WhatsApp */
    public $whatsapp;

    /**
     * Constructor.
     */
    protected function __construct()
    {
        $this->token = get_option( 'bookly_cloud_token' );
        $this->general = new General( $this );
        $this->account = new Account( $this );
        $this->sms = new SMS( $this );
        $this->stripe = new Stripe( $this );
        $this->zapier = new Zapier( $this );
        $this->cron = new Cron( $this );
        $this->voice = new Voice( $this );
        $this->whatsapp = new WhatsApp( $this );

        Proxy\Shared::initApi( $this );
    }

    /**
     * Get instance
     *
     * @return static
     */
    public static function getInstance()
    {
        if ( ! self::hasInCache( __FUNCTION__ ) ) {
            self::putInCache( __FUNCTION__, new static() );
        }

        return self::getFromCache( __FUNCTION__ );
    }

    /**
     * Send GET request.
     *
     * @param string $path
     * @param array $data
     * @return array|false
     */
    public function sendGetRequest( $path, array $data = array() )
    {
        $url = $this->buildUrl( $path, $data );

        return $this->_handleResponse( $this->_sendRequest( 'GET', $url, $data ) );
    }

    /**
     * Send POST request.
     *
     * @param string $path
     * @param array $data
     * @return array|false
     */
    public function sendPostRequest( $path, array $data = array() )
    {
        $url = $this->buildUrl( $path, $data );

        return $this->_handleResponse( $this->_sendRequest( 'POST', $url, $data ) );
    }

    /**
     * Send PATCH request.
     *
     * @param string $path
     * @param array $data
     * @return array|false
     */
    public function sendPatchRequest( $path, array $data = array() )
    {
        $url = $this->buildUrl( $path, $data );

        return $this->_handleResponse( $this->_sendRequest( 'PATCH', $url, $data ) );
    }

    /**
     * Send DELETE request.
     *
     * @param string $path
     * @param array $data
     * @return array|false
     */
    public function sendDeleteRequest( $path, array $data = array() )
    {
        $url = $this->buildUrl( $path, $data );

        return $this->_handleResponse( $this->_sendRequest( 'DELETE', $url, $data ) );
    }

    /**
     * Build URL
     *
     * @param string $path
     * @param array &$data
     * @return string
     */
    public function buildUrl( $path, array &$data = array() )
    {
        $url = self::API_URL . str_replace( '%token%', $this->token, $path );

        foreach ( $data as $key => $value ) {
            if ( $key[0] == '%' ) {
                $url = str_replace( $key, $value, $url );
                unset ( $data[ $key ] );
            }
        }

        return $url;
    }

    /**
     * Add new listener
     *
     * @param string $event
     * @param callable $callable
     */
    public function listen( $event, $callable )
    {
        $this->listeners[ $event ][] = $callable;
    }

    /**
     * Dispatch event
     *
     * @param string $event
     */
    public function dispatch( $event )
    {
        if ( isset ( $this->listeners[ $event ] ) ) {
            $params = func_get_args();
            unset ( $params[0] );
            foreach ( $this->listeners[ $event ] as $listener ) {
                call_user_func_array( $listener, $params );
            }
        }
    }

    /**
     * Set new token
     *
     * @param string|null $token
     */
    public function setToken( $token )
    {
        $this->token = $token;
    }

    /**
     * Get token
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Add error
     *
     * @param string $error
     */
    public function addError( $error )
    {
        $this->errors[] = $error;
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Clear errors
     */
    public function clearErrors()
    {
        $this->errors = array();
    }

    /**
     * Add error translator
     *
     * @param callable $translator
     */
    public function addErrorTranslator( $translator )
    {
        $this->errorTranslators[] = $translator;
    }

    /**
     * Sets request timeout
     *
     * @param int $timeout
     * @return $this
     */
    public function setRequestTimeout( $timeout )
    {
        if ( ini_get( 'max_execution_time' ) < $timeout ) {
            set_time_limit( $timeout );
        }
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Send HTTP request
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @return string|null
     */
    private function _sendRequest( $method, $url, $data )
    {
        $args = array(
            'method' => $method,
            'sslverify' => false,
            'timeout' => $this->timeout,
        );

        if ( ! isset( $data['site_url'] ) ) {
            $data['site_url'] = site_url();
        }
        if ( ! isset( $data['bookly'] ) ) {
            $data['bookly'] = Plugin::getVersion();
        }

        if ( $method == 'GET' ) {
            // WP 4.4.11 doesn't take into account the $data for the GET request
            // Manually move data in query string
            $query_data = array();
            foreach ( $data as $key => $value ) {
                $query_data[ $key ] = urlencode( $value );
            }
            $url = add_query_arg( $query_data, $url );
        } else {
            $args['body'] = $data;
        }

        $response = wp_remote_request( $url, $args );
        if ( $response instanceof \WP_Error ) {
            $this->errors[] = $response->get_error_messages();

            return null;
        }

        return $response['body'];
    }

    /**
     * Check response for errors.
     *
     * @param mixed $response
     * @return array|false
     */
    private function _handleResponse( $response )
    {
        $response = json_decode( $response, true );

        if ( $response !== null && array_key_exists( 'success', $response ) ) {
            if ( $response['success'] ) {

                return $response;
            }
            if ( strncmp( $response['message'], 'ERROR_', 6 ) === 0 ) {
                $this->errors[ $response['message'] ] = $this->_translateError( $response['message'] );
            } else {
                $this->errors[] = $this->_translateError( $response['message'] );
            }
        } else {
            $this->errors[] = __( 'Error connecting to server.', 'bookly' );
        }

        return false;
    }

    /**
     * Translate error code into message.
     *
     * @param string $error_code
     * @return string
     */
    private function _translateError( $error_code )
    {
        foreach ( $this->errorTranslators as $translator ) {
            $msg = $translator( $error_code );
            if ( $msg !== null ) {
                return $msg;
            }
        }

        // Build message from error code
        if ( strncmp( $error_code, 'ERROR_', 6 ) === 0 ) {
            $error_code = substr( $error_code, 6 );
        }

        return __( ucfirst( strtolower( str_replace( '_', ' ', $error_code ) ) ), 'bookly' );
    }
}
