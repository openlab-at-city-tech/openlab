<?php
namespace Bookly\Backend\Modules\Services\Forms;

use Bookly\Lib;

/**
 * Class Category
 * @method Lib\Entities\Category save()
 *
 * @package Bookly\Backend\Modules\Services\Forms
 */
class Category extends Lib\Base\Form
{
    protected static $entity_class = 'Category';

    /**
     * Configure the form.
     */
    public function configure()
    {
        $this->setFields( array( 'name' ) );
    }

}
