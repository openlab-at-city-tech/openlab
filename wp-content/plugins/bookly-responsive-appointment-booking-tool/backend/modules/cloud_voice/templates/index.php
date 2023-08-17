<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Cloud;
use Bookly\Backend\Components\Dialogs\TableSettings;
use Bookly\Lib\Utils\Tables;
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Voice Notifications', 'bookly' ) ?></h4>
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
            <ul class="nav nav-tabs mb-3" id="sms_tabs">
                <li class="nav-item"><a class="nav-link active" data-toggle="bookly-tab" href="#notifications"><?php esc_html_e( 'Notifications', 'bookly' ) ?></a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="bookly-tab" href="#details"><?php esc_html_e( 'Details', 'bookly' ) ?></a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="bookly-tab" href="#price_list"><?php esc_html_e( 'Price list', 'bookly' ) ?></a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="bookly-tab" href="#settings"><?php esc_html_e( 'Settings', 'bookly' ) ?></a></li>
            </ul>
            <div class="tab-content mt-3">
                <div class="tab-pane active" id="notifications"><?php self::renderTemplate( '_notifications', array( 'datatable' => $datatables[ Tables::VOICE_NOTIFICATIONS ] ) ) ?></div>
                <div class="tab-pane" id="details"><?php self::renderTemplate( '_calls_details', array( 'datatable' => $datatables[ Tables::VOICE_DETAILS ] ) ) ?></div>
                <div class="tab-pane" id="price_list"><?php self::renderTemplate( '_price', array( 'datatable' => $datatables[ Tables::VOICE_PRICES ] ) ) ?></div>
                <div class="tab-pane" id="settings"><?php self::renderTemplate( '_settings' ) ?></div>
            </div>
        </div>
    </div>

    <?php TableSettings\Dialog::render() ?>
    <?php Dialogs\VoiceTest\Dialog::render() ?>
</div>