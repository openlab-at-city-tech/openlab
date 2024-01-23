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

        if ( ! ( isset( $response['data']['POST']['query'], $response['data']['GET']['query'] ) && $response['data']['POST']['query'] === self::$query && $response['data']['GET']['query'] === self::$query ) ) {
            $this->addError( sprintf( '<b>%s</b><br/>%s', parse_url( Cloud\API::API_URL, PHP_URL_HOST ), __( 'For some reason, your server blocks Bookly Cloud requests. To fix the issue, please ask your hosting provider to whitelist the Bookly Cloud server.', 'bookly' ) ) );
        }

        return empty( $this->errors );
    }

    public function ajax()
    {
        wp_send_json_success( array( 'query' => self::$query ) );
    }
}