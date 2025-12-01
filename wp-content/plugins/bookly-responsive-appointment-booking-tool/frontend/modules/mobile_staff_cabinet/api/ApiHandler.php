<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api;

use Bookly\Lib;

/**
 * Abstract class for API
 */
abstract class ApiHandler
{
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_STAFF = 'staff';

    /** @var Lib\Entities\Staff */
    protected $staff;
    /** @var \WP_User */
    protected $wp_user;
    /** @var array */
    protected $result = array();

    /** @var string */
    protected $role;
    /** @var Lib\Base\Request */
    protected $request;
    /** @var Lib\Utils\Collection */
    protected $params;
    /** @var callable */
    protected $processMethod;
    /** @var IResponse */
    protected $response;

    /**
     * Sets resource method to execute
     *
     * @param string $method
     * @return void
     */
    public function setProcessMethod( $method )
    {
        $this->processMethod = $method;
    }

    /**
     * @return string|null
     */
    public function getProcessMethod()
    {
        return $this->processMethod;
    }

    /**
     * @return string|null
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Handles the API request by executing the appropriate resource method
     *
     * @return IResponse
     */
    public function process()
    {
        if ( method_exists( $this, $this->processMethod ) ) {
            $this->{$this->processMethod}();
        }

        $data = $this->getResponseData();

        $this->response
            ->setData( $data )
            ->addHeader( 'X-Bookly-V', Lib\Plugin::getVersion() );

        return $this->response;
    }

    /**
     * Sets request parameters
     *
     * @param mixed $params
     * @return void
     */
    protected function setParams( $params )
    {
        $this->params = new Lib\Utils\Collection( is_array( $params ) ? $params : array() );
    }

    /**
     * Gets parameter value by name
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    protected function param( $name, $default = null )
    {
        return $this->params->has( $name )
            ? stripslashes_deep( $this->params->get( $name ) )
            : $default;
    }

    /**
     * Gets response data
     *
     * @return array
     */
    protected function getResponseData()
    {
        return array( 'result' => $this->result );
    }
}
