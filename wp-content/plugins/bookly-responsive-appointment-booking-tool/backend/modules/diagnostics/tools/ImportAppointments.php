<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

use Bookly\Lib;

class ImportAppointments extends Tool
{
    protected $slug = 'import-appointments';
    protected $hidden = false;
    protected $list;

    public $position = 40;

    public function __construct()
    {
        $this->title = 'Import appointments';
    }

    public function render()
    {
        return '<div id="bookly-import-appointments-form"></div>';
    }
}