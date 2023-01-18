<?php
namespace Bookly\Lib\Base;

/**
 * Class Controller
 * @deprecated
 * @package Bookly\Lib\Base
 */
abstract class Controller
{
    public static function getInstance()
    {
        $class = get_called_class();
        return new $class();
    }
}