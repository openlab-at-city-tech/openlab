<?php
namespace Bookly\Backend\Components\Dialogs\Mailing\Campaign;

use Bookly\Lib;
use Bookly\Backend\Components\Controls\Buttons;

class Dialog extends Lib\Base\Component
{
    /**
     * Render campaign dialog.
     */
    public static function render()
    {
        self::enqueueStyles( array(
            'backend' => array( 'css/fontawesome-all.min.css' => array( 'bookly-backend-globals' ), ),
            'bookly' => array( 'backend/components/ace/resources/css/ace.css', ),
        ) );

        self::enqueueScripts( array(
            'bookly' => array(
                'backend/components/ace/resources/js/ace.js' => array(),
                'backend/components/ace/resources/js/ext-language_tools.js' => array(),
                'backend/components/ace/resources/js/mode-bookly.js' => array(),
                'backend/components/ace/resources/js/editor.js' => array( 'bookly-campaign-dialog.js' ),
            ),
            'module' => array( 'js/campaign-dialog.js' => array( 'bookly-backend-globals' ), ),
        ) );


        wp_localize_script( 'bookly-campaign-dialog.js', 'BooklyL10nCampaignDialog', array(
            'datePicker' => Lib\Utils\DateTime::datePickerOptions(),
            'moment_format_date' => Lib\Utils\DateTime::convertFormat( 'date', Lib\Utils\DateTime::FORMAT_MOMENT_JS ),
            'moment_format_time' => Lib\Utils\DateTime::convertFormat( 'time', Lib\Utils\DateTime::FORMAT_MOMENT_JS ),
            'codes' => json_encode( array(
                'client_name' => array( 'description' => __( 'Full name of client', 'bookly' ), 'if' => true ),
                'client_first_name' => array( 'description' => __( 'First name of client', 'bookly' ), 'if' => true ),
                'client_last_name' => array( 'description' => __( 'Last name of client', 'bookly' ), 'if' => true ),
                'client_phone' => array( 'description' => __( 'Phone of client', 'bookly' ), 'if' => true ),
                'company_address' => array( 'description' => __( 'Address of company', 'bookly' ), 'if' => true ),
                'company_name' => array( 'description' => __( 'Name of company', 'bookly' ), 'if' => true ),
                'company_phone' => array( 'description' => __( 'Company phone', 'bookly' ), 'if' => true ),
                'company_website' => array( 'description' => __( 'Company web-site address', 'bookly' ), 'if' => true ),
            ) ),
            'l10n' => array(
                'new_campaign' => __( 'New campaign', 'bookly' ),
                'edit_campaign' => __( 'Edit campaign', 'bookly' ),
                'save' => __( 'Save', 'bookly' ),
                'cancel' => __( 'Cancel', 'bookly' ),
                'close' => __( 'Close', 'bookly' ),
                'name' => __( 'Name', 'bookly' ),
                'start_campaign' => __( 'Start campaign', 'bookly' ),
                'manual' => __( 'Manual', 'bookly' ),
                'start_sending_at' => __( 'Start sending messages at', 'bookly' ),
                'start_sending_help' => __( 'Set the time when the mailing will start', 'bookly' ),
                'start_time' => __( 'Start time', 'bookly' ),
                'recipients' => __( 'Recipients', 'bookly' ),
                'sms_text' => __( 'Sms text', 'bookly' ),
                'campaign' => __( 'Campaign', 'bookly' ),
                'cancel_campaign' => __( 'Cancel campaign', 'bookly' ) . '…',
                'are_you_sure' => __( 'Are you sure?', 'bookly' ),
                'start_now_text' => __( 'You\'re about to send an SMS Campaign. If you\'re sure about the setup, click \'Start Now\'. Otherwise, please take a moment to review the details.', 'bookly' ),
                'run' => __( 'Start Now', 'bookly' ),
                'doc_hint' => sprintf( __( 'Start typing "{" to see the available codes. For more information, see the <a href="%s" target="_blank">documentation</a> page', 'bookly' ), 'https://api.booking-wp-plugin.com/go/bookly-sms-campaigns' ),
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