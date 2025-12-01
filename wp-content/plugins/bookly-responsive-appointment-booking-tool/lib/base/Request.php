<?php
namespace Bookly\Lib\Base;

use Bookly\Lib\Utils\Collection;

class Request extends Collection
{
    /** @var Collection */
    protected $headers;
    /** @var string */
    protected $method;

    public function __construct()
    {
        $headers = array();
        if ( function_exists( 'getallheaders' ) ) {
            $headers = getallheaders();
        } else {
            foreach ( $_SERVER as $name => $value ) {
                if ( substr( $name, 0, 5 ) == 'HTTP_' ) {
                    $header_name = str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $name, 5 ) ) ) ) );
                    $headers[ $header_name ] = $value;
                } else {
                    $header_name = str_replace( '_', '-', ucwords( strtolower( $name ) ) );
                    $headers[ $header_name ] = $value;
                }
            }
        }

        $this->headers = new Collection( $headers );
        $this->method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET';

        if ( str_contains( $this->headers->get( 'Content-Type', '' ), 'json' ) ) {
            parent::__construct( json_decode( file_get_contents( 'php://input' ), true ) ?: array() );

            return;
        }

        if ( in_array( $this->method, array( 'DELETE', 'PUT', 'PATCH' ) ) ) {
            $input = file_get_contents( 'php://input' );
            if ( ! empty( $input ) ) {
                parse_str( $input, $data );
                parent::__construct( $data );

                return;
            }
        }

        parent::__construct( $_REQUEST );
    }

    /**
     * @return Collection
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}