<?php
namespace Bookly\Backend\Modules\Diagnostics\Tests;

use Bookly\Lib;

class Headers extends Connections
{
    protected $slug = 'check-external-connections-without-headers';
    protected $hidden = true;

    public function __construct()
    {
        $this->title = 'External connections without some headers';
        $this->description = 'This test checks the ability to establish a connection with the Bookly from external server.';
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        // Test cloud callback access.
        $cloud = Lib\Cloud\API::getInstance();
        $data = array(
            'feedback' => array(
                'test' => 'Connections',
                'ajax' => 'ajax',
            ),
            'endpoint' => add_query_arg( array( 'action' => 'bookly_diagnostics_ajax' ), admin_url( 'admin-ajax.php' ) ),
        );

        $response = $cloud->sendPostRequest( '/1.0/test/feedback-request/check-headers', $data );
        $methods = array( 'POST', 'GET' );
        if ( isset( $response['headers'] ) && count( $response['headers'] ) > 0 ) {
            foreach ( $response['headers'] as $header => $data ) {
                foreach ( $methods as $method ) {
                    if ( ! ( isset( $data[ $method ]['query'] ) && $data[ $method ]['query'] === self::$query ) ) {
                        $this->addError( sprintf( 'Request method <b>%s</b> without header <b>%s</b> failed', $method, $header ) );
                    }
                }
            }
        } else {
            $this->addError( 'Failed' );
        }

        return empty( $this->errors );
    }
}