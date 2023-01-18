<?php

namespace Bookly\Backend\Modules\Diagnostics\Tools;

/**
 * Class DataManagement
 *
 * @package Bookly\Backend\Modules\Diagnostics\Tools
 */
class DataManagement extends Tool
{
    protected $slug = 'data-management';
    protected $hidden = true;

    public function __construct()
    {
        $this->title = 'Data management';
    }

    public function render()
    {
        return self::renderTemplate( '_data_management', array(), false );
    }
}