<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api;

class Response implements IResponse
{
    protected $http_status = 200;
    protected $data = '';
    protected $headers = array();
    protected $contentType = 'application/json';

    public function render()
    {
        $body = $this->getBody();
        if ( $body === null ) {
            $this->setHttpStatus( 204 );
        }

        $this->sendHeaders();

        echo $body;
        exit;
    }

    public function setData( $data )
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set HTTP status code
     *
     * @param int $http_status
     * @return void
     */
    public function setHttpStatus( $http_status )
    {
        $this->http_status = $http_status;
    }

    public function addHeader( $name, $value )
    {
        $this->headers[$name] = $value;

        return $this;
    }

    protected function getBody()
    {
        $data = $this->getData();
        if ( $data === null ) {
            return null;
        }

        if ( str_contains( $this->contentType, 'application/json' ) ) {
            return json_encode( $data );
        }

        return $data;
    }

    protected function sendHeaders()
    {
        header( 'Content-Type: ' . $this->contentType );
        http_response_code( $this->http_status );
        foreach ( $this->headers as $name => $value ) {
            header( $name . ': ' . $value );
        }
    }

    protected function buildData()
    {

    }

    public function getData()
    {
        $this->buildData();

        return $this->data;
    }
}
