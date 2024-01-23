<?php
namespace Bookly\Lib\Base;

class ValidationException extends \Exception
{
    /** @var string */
    protected $field;

    /**
     * ValidationException constructor.
     *
     * @param string $message
     * @param string $field
     * @param int $code
     */
    public function __construct( $message, $field, $code = 0 )
    {
        $this->field = $field;
        parent::__construct( $message, $code );
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }
}