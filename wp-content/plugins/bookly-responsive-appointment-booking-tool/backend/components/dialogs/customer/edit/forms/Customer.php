<?php
namespace Bookly\Backend\Components\Dialogs\Customer\Edit\Forms;

use Bookly\Lib;

/**
 * Class Customer
 * @package Bookly\Backend\Components\Dialogs\Customer\Forms
 */
class Customer extends Lib\Base\Form
{
    protected static $entity_class = 'Customer';

    public function configure()
    {
        $this->setFields( array(
            'wp_user_id',
            'group_id',
            'full_name',
            'first_name',
            'last_name',
            'phone',
            'email',
            'country',
            'state',
            'postcode',
            'city',
            'street',
            'street_number',
            'additional_address',
            'notes',
            'birthday',
            'info_fields',
        ) );
    }
}