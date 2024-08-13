<?php
namespace Bookly\Backend\Modules\Diagnostics\Tests;

use Bookly\Lib\API;
use Bookly\Lib\Cloud;
use Bookly\Lib\Config;

class Connections extends Test
{
    protected $slug = 'check-external-connections';
    public static $query = 'query';
    public $ignore_csrf = array( 'ajax' );

    public function __construct()
    {
        $this->title = __( 'External connections', 'bookly' );
        $this->description = __( 'This test checks the ability to establish a connection with the Bookly Cloud external server.', 'bookly' );
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $port = 443;
        $timeout = 5;
        $hosts = array( parse_url( API::API_URL, PHP_URL_HOST ), parse_url( Cloud\API::API_URL, PHP_URL_HOST ) );
        if ( Config::mailchimpActive() ) {
            $hosts[] = 'mailchimp.com';
        }
        foreach ( $hosts as $host ) {
            $fp = fsockopen( $host, $port, $errno, $errstr, $timeout );
            if ( ! $fp ) {
                $this->addError( sprintf( '<b>%s</b><br/>%s', $host, $errstr ) );
            }
        }

        // Test cloud callback access.
        $cloud = Cloud\API::getInstance();
        $data = array(
            'feedback' => array(
                'test' => 'Connections',
                'ajax' => 'ajax',
            ),
            'endpoint' => add_query_arg( array( 'action' => 'bookly_diagnostics_ajax' ), admin_url( 'admin-ajax.php' ) ),
        );

        $response = $cloud->sendPostRequest( '/1.0/test/feedback-request', $data );
        if ( $cloud->getErrors() ) {
            foreach ( $cloud->getErrors() as $error ) {
                $this->addError( $error );
            }
        } else {
            $post = $get = false;
            if ( isset( $response['data'] ) ) {
                if ( isset( $response['data']['POST']['query'] ) && $response['data']['POST']['query'] === self::$query ) {
                    $post = true;
                }
                if ( isset( $response['data']['GET']['query'] ) && $response['data']['GET']['query'] === self::$query ) {
                    $get = true;
                }
                if ( ! $get && ! $post ) {
                    $this->addError( 'POST and GET requests from Bookly Cloud with Bookly are failed' );
                } elseif ( ! $post ) {
                    $this->addError( 'POST request from Bookly Cloud is failed' );
                } elseif ( ! $get ) {
                    $this->addError( 'GET request from Bookly Cloud is failed' );
                }
                if ( isset( $response['data']['raw'] ) && is_array( $response['data']['raw'] ) ) {
                    $this->addError( sprintf( '<b>%s</b><br/>%s<br/>', parse_url( Cloud\API::API_URL, PHP_URL_HOST ), __( 'For some reason, your server blocks Bookly Cloud requests. To fix the issue, please ask your hosting provider to whitelist the Bookly Cloud server.', 'bookly' ) ) );
                    foreach ( $response['data']['raw'] as $i => $error_data ) {
                        $title = '';
                        if ( isset( $error_data['method'] ) ) {
                            $title .= 'Method <b>' . $error_data['method'] . '</b> ';
                        }
                        if ( isset( $error_data['status'] ) ) {
                            $title .= 'Status <b>' . $error_data['status'] . '</b>';
                        }
                        $message = '';
                        if ( isset( $error_data['content'] ) ) {
                            $message .= '<span class="badge badge-primary">Content</span> ' . $error_data['content'] . '<br>';
                        }
                        if ( isset( $error_data['headers'] ) ) {
                            $message .= '<span class="badge badge-primary">Headers</span>';
                            if ( is_array( $error_data['headers'] ) ) {
                                $message .= '<pre>' . json_encode( $error_data['headers'], JSON_PRETTY_PRINT ) . '</pre>';
                            } else {
                                $message .= $error_data['headers'];
                            }
                        }
                        if ( $message ) {
                            $this->addError( sprintf( '<div class="card bookly-collapse-with-arrow bookly-js-tool">
                                <div class="card-header bg-light d-flex align-items-center bookly-cursor-pointer bookly-collapsed" href="#bookly-connections-%1$d" data-toggle="bookly-collapse" aria-expanded="false">
                                    <div class="d-flex w-100 align-items-center">
                                        <div class="flex-fill bookly-collapse-title bookly-js-test-title">%2$s</div>
                                    </div>
                                </div>
                                <div id="bookly-connections-%1$d" class="bookly-collapse" style="">
                                    <div class="card-body">
                                        <div id="accordion" class="accordion" role="tablist" aria-multiselectable="true">%3$s</div>
                                    </div>
                                </div>
                            </div>', $i, $title ?: __( 'Error', 'bookly' ), $message ) );
                        }
                    }
                }
            } else {
                $this->addError( 'Bookly Cloud response is failed' );
            }
        }

        return empty( $this->errors );
    }

    public function ajax()
    {
        wp_send_json_success( array( 'query' => self::$query ) );
    }
}