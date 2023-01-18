<?php
namespace Bookly\Backend\Components\Dialogs\Mailing\CreateList;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Dialogs\Mailing\CreateList
 */
class Ajax extends Lib\Base\Ajax
{
    /**
     * Create mailing list.
     */
    public static function createMailingList()
    {
        $mailing_list = new Lib\Entities\MailingList();
        $mailing_list->setName( self::parameter( 'name' ) )->save();
        wp_send_json_success( $mailing_list->getFields() );
    }
}