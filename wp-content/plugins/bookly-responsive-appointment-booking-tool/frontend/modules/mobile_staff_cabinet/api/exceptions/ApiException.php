<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api\Exceptions;

use Bookly\Lib\Base\Request;

class ApiException extends \Exception
{
    /** @var int */
    protected $http_status = 400;
    /** @var Request|null */
    protected $request;
    /** @var array */
    protected $error_data = array();

    public function __construct( $message, $http_status = 400, $error_data = array(), $request = null )
    {
        $this->error_data = $error_data;
        $this->request = $request;
        $this->http_status = $http_status;
        parent::__construct( $message, $http_status );
    }

    public function getHttpStatus()
    {
        return $this->http_status;
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function getErrorData()
    {
        return $this->error_data;
    }
}