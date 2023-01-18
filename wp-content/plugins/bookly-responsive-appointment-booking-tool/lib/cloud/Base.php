<?php
namespace Bookly\Lib\Cloud;

/**
 * Class Base
 * @package Bookly\Lib\Cloud
 */
abstract class Base
{
    /** @var API */
    protected $api;

    /**
     * Constructor.
     *
     * @param API $api
     */
    public function __construct( API $api )
    {
        $this->api = $api;

        $api->addErrorTranslator( array( $this, 'translateError' ) );

        $this->setup();

        $this->setupListeners();
    }

    /**
     * Translate error code into message
     *
     * @param $error_code
     * @return string|null
     */
    public function translateError( $error_code )
    {
        return null;
    }

    /**
     * Setup object
     */
    protected function setup()
    {

    }

    /**
     * Setup listeners
     */
    protected function setupListeners()
    {

    }
}