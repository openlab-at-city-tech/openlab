<?php
namespace Bookly\Backend\Components\Dialogs\SmtpTest;

use Bookly\Lib;

/**
 * Class Dialog
 *
 * @package Bookly\Backend\Components\Dialogs\SmtpTest
 */
class Dialog extends Lib\Base\Component
{
    /**
     * Render create service dialog.
     */
    public static function render()
    {
        self::enqueueScripts( array(
            'module' => array( 'js/smtp-test-dialog.js' => array( 'bookly-backend-globals' ) ),
        ) );

        wp_localize_script( 'bookly-smtp-test-dialog.js', 'BooklySmtpTestDialogL10n', array(
            'success' => __( 'Success', 'bookly' ),
            'failed' => __( 'Failed', 'bookly' ),
        ) );

        self::renderTemplate( 'dialog' );
    }
}