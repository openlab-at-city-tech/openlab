<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api\Exceptions;

use Bookly\Lib\Base\Request;

class HandleException extends \Exception
{
    /** @var Request|null */
    protected $request;
    /** @var string */
    protected $class_name;
    /** @var string */
    protected $info;

    public function __construct( $message, Request $request, $class_name, $info )
    {
        $this->request = $request;
        $this->class_name = $class_name;
        $this->info = $info;
        parent::__construct( $message );
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

}