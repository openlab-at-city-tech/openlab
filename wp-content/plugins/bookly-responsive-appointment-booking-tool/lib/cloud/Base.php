<?php
namespace Bookly\Lib\Cloud;

use Bookly\Backend\Modules\Diagnostics\Tests;

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

    /**
     * @param array $data
     * @return array
     */
    protected function addTestCanIUse( array $data )
    {
        $data['test'] = array(
            'data' => array(
                'test' => 'Connections',
                'ajax' => 'ajax',
            ),
            'expected' => Tests\Connections::$query,
            'endpoint' => add_query_arg( array( 'action' => 'bookly_diagnostics_ajax' ), admin_url( 'admin-ajax.php' ) ),
        );

        return $data;
    }
}