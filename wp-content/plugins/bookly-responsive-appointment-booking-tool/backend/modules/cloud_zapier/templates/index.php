<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Cloud;
use Bookly\Backend\Components\Settings\Inputs;
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0">Zapier</h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row pb-3">
                <div class="col">
                </div>
                <div class="col-auto">
                    <?php Cloud\Account\Panel::render() ?>
                </div>
            </div>
            <div class="form-group">
                <h4><?php esc_html_e( 'Instructions', 'bookly' ) ?></h4>
                <p></p>
                <ol>
                    <li><?php printf( __( 'If you have not already done so, <a href="%s" target="_blank">sign up for Zapier</a>', 'bookly' ), 'https://zapier.com/sign-up/' ) ?></li>
                    <li><?php printf( __( '<a href="%s" target="_blank">Sign in to Zapier</a> and click <a href="%s" target="_blank"><b>Make a Zap</b></a>', 'bookly' ), 'https://zapier.com/login/', 'https://zapier.com/app/editor' ) ?></li>
                    <li><?php _e( 'In the <b>Choose App & Event</b> step search for the <b>Bookly</b> app and select it', 'bookly' ) ?></li>
                    <li><?php _e( 'In the <b>Choose Trigger Event</b> dropdown choose a trigger and click <b>Continue</b>', 'bookly' ) ?></li>
                    <li><?php _e( 'In the <b>Choose Account</b> step click <b>Sign in to Bookly</b>', 'bookly' ) ?></li>
                    <li><?php _e( 'In the popup window enter the API Key found below on this page, and click <b>Yes, Continue</b>', 'bookly' ) ?></li>
                    <li><?php _e( 'Click <b>Continue</b>, then <b>Test trigger</b> and <b>Continue</b>', 'bookly' ) ?></li>
                    <li><?php esc_html_e( 'Continue creating your Zap by selecting the options you\'d like', 'bookly' ) ?></li>
                    <li><?php _e( 'Finally, click <b>Finish</b> to create your Zap', 'bookly' ) ?></li>
                    <li><?php esc_html_e( 'Once your Zap is created, make sure to toggle your Zap "on". It\'s now ready to go and will run automatically', 'bookly' ) ?></li>
                </ol>
            </div>
            <div class="form-row">
                <div class="col-lg-6 col-xs-12">
                    <?php Inputs::renderOptionCopy( 'bookly_cloud_zapier_api_key', __( 'API Key', 'bookly' ) ) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="col-lg-6 col-xs-12">
                    <?php Buttons::renderDefault( 'bookly-zapier-generate-new-api-key', null, __( 'Generate new API Key', 'bookly' ), array(), true ) ?>
                </div>
            </div>
        </div>
    </div>
</div>