<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

class DataManagement extends Tool
{
    protected $slug = 'data-management';
    protected $hidden = true;
    public $position = 10;

    public function __construct()
    {
        $this->title = 'Data management';
    }

    public function render()
    {
        return self::renderTemplate( '_data_management', array(), false );
    }
}