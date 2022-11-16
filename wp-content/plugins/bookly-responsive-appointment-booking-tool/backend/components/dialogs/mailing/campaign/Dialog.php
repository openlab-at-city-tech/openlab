<?php
namespace Bookly\Backend\Components\Dialogs\Mailing\Campaign;

use Bookly\Lib;
use Bookly\Backend\Components\Controls\Buttons;

/**
 * Class Dialog
 * @package Bookly\Backend\Components\Dialogs\Mailing\Campaign
 */
class Dialog extends Lib\Base\Component
{
    /**
     * Render campaign dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
        ) );

        self::enqueueScripts( array(
            'module' => array( 'js/campaign-dialog.js' => array( 'bookly-backend-globals' ), ),
        ) );


        wp_localize_script( 'bookly-campaign-dialog.js', 'BooklyL10nCampaignDialog', array(
            'datePicker' => Lib\Utils\DateTime::datePickerOptions(),
            'moment_format_date' => Lib\Utils\DateTime::convertFormat( 'date', Lib\Utils\DateTime::FORMAT_MOMENT_JS ),
            'moment_format_time' => Lib\Utils\DateTime::convertFormat( 'time', Lib\Utils\DateTime::FORMAT_MOMENT_JS ),
            'l10n' => array(
                'new_campaign' => __( 'New campaign', 'bookly' ),
                'edit_campaign' => __( 'Edit campaign', 'bookly' ),
                'save' => __( 'Save', 'bookly' ),
                'cancel' => __( 'Cancel', 'bookly' ),
                'close' => __( 'Close', 'bookly' ),
                'name' => __( 'Name', 'bookly' ),
                'start_campaign' => __( 'Start campaign', 'bookly' ),
                'immediately' => __( 'Immediately', 'bookly' ),
                'start_sending_at' => __( 'Start sending messages at', 'bookly' ),
                'start_sending_help' => __( 'Set the time when the mailing will start', 'bookly' ),
                'start_time' => __( 'Start time', 'bookly' ),
                'start' => __( 'Start', 'bookly' ),
                'recipients' => __( 'Recipients', 'bookly' ),
                'sms_text' => __( 'Sms text', 'bookly' ),
                'campaign' => __( 'Campaign', 'bookly' ),
                'cancel_campaign' => __( 'Cancel campaign', 'bookly' ) . '…',
                'are_you_sure' => __( 'Are you sure?', 'bookly' ),
            ),
        ) );

        print '<div id="bookly-campaign-dialog"></div>';
    }

    /**
     * Render button
     */
    public static function renderNewCampaignButton()
    {
        print '<div class="col-auto">';
        Buttons::renderAdd( 'bookly-js-new-campaign', 'btn-success', __( 'New campaign', 'bookly' ) );
        print '</div>';
    }
}