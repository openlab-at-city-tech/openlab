<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet;

class ParameterException extends \Exception
{
    /** @var string */
    protected $parameter;

    protected $value;

    public function __construct( $parameter, $value, $code = 400 )
    {
        $this->parameter = $parameter;
        $this->value = $value;
        parent::__construct( '', $code );
    }

    /**
     * @return string
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

}