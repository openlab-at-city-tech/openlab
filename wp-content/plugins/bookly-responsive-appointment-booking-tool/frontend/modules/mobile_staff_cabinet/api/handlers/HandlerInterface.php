<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api\Handlers;

use Bookly\Lib;
use Bookly\Frontend\Modules\MobileStaffCabinet\Api\Response;

interface HandlerInterface
{
    /**
     * @return float
     */
    public static function getVersion();

    /**
     * @param Lib\Base\Request $request
     * @return Response
     */
    public function __invoke( Lib\Base\Request $request );

    /**
     * @return string
     */
    public function getResolverMethodName();

    /**
     * @return string
     */
    public function getRole();
}