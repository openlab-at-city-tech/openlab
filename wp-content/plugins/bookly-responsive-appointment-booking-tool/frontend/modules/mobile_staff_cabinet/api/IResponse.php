<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api;

interface IResponse
{
    /**
     * @return $this
     */
    public function setData( $data );

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return $this
     */
    public function setHttpStatus( $http_status );

    /**
     * @return $this
     */
    public function addHeader( $name, $value );

    /**
     * @return void
     */
    public function render();
}
