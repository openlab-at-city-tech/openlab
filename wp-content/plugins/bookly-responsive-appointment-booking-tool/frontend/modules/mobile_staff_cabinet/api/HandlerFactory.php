<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api;

class HandlerFactory
{
    /**
     * @return HandlerLocator
     */
    public static function createLocator()
    {
        $locator = new HandlerLocator();

        $locator->register('Bookly\Frontend\Modules\MobileStaffCabinet\Api\Handlers\Handler1_0');
        $locator->register('Bookly\Frontend\Modules\MobileStaffCabinet\Api\Handlers\Handler1_1');

        return $locator;
    }
}