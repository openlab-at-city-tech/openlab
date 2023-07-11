<?php
namespace Bookly\Backend\Components\Dialogs\Whatsapp;

use Bookly\Lib;

/**
 * Class Ajax
 * @package Bookly\Backend\Components\Dialogs\Whatsapp
 */
class Ajax extends Lib\Base\Ajax
{
    public static function getWhatsappTemplates()
    {
        $list = Lib\Cloud\API::getInstance()->whatsapp->getTemplates();
        if ( Lib\Cloud\API::getInstance()->getErrors() ) {
            wp_send_json_error( array( 'message' => current( Lib\Cloud\API::getInstance()->getErrors() ) ) );
        }

        wp_send_json_success( $list );
    }
}