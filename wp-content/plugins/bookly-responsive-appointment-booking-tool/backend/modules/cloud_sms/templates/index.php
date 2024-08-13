<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Dialogs\TableSettings;
use Bookly\Backend\Components\Cloud;
use Bookly\Lib\Utils\DateTime;
/**
 * @var Bookly\Lib\Cloud\SMS $sms
 * @var int $undelivered_count
 */
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'SMS Notifications', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row pb-3">
                <div class="col">
                    <p class="h6 m-0 p-0"><strong><?php esc_html_e( 'Sender ID', 'bookly' ) ?>:</strong> <a href="#" id="bookly-open-tab-sender-id"><?php echo esc_html( $sms->getSenderId() ) ?>
                            <i class="fas fa-pencil-alt ml-1"></i></a></p>
                    <?php if ( $sms->getSenderIdApprovalDate() ) : ?>
                        <p class="h6 small m-0 p-0 mb-1 mr-1 text-muted text-form bookly-js-sender-id-approval-date"><?php esc_html_e( 'Approved at', 'bookly' ) ?>:
                            <strong><?php echo DateTime::formatDate( $sms->getSenderIdApprovalDate() ) ?></strong></p>
                    <?php else: ?>
                        <p class="h6 small m-0 p-0 mb-1 mr-1 text-muted"><?php esc_html_e( 'Change the sender\'s name to your phone number or any other name', 'bookly' ) ?></p>
                    <?php endif ?>
                </div>
                <div class="col-auto">
                    <?php Cloud\Account\Panel::render() ?>
                </div>
            </div>
            <ul class="nav nav-tabs mb-3" id="sms_tabs">
                <li class="nav-item"><a class="nav-link active" data-toggle="bookly-tab" href="#notifications"><?php esc_html_e( 'Notifications', 'bookly' ) ?></a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="bookly-tab" href="#campaigns"><?php esc_html_e( 'Campaigns', 'bookly' ) ?></a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="bookly-tab" href="#mailing"><?php esc_html_e( 'Mailing lists', 'bookly' ) ?></a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="bookly-tab" href="#sms_details"><?php esc_html_e( 'SMS Details', 'bookly' ); if ( $undelivered_count ) : ?> <span class="badge bg-danger"><?php echo esc_html( $undelivered_count ) ?></span><?php endif ?></a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="bookly-tab" href="#price_list"><?php esc_html_e( 'Price list', 'bookly' ) ?></a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="bookly-tab" href="#sender_id"><?php esc_html_e( 'Sender ID', 'bookly' ) ?></a></li>
            </ul>
            <div class="tab-content mt-3">
                <div class="tab-pane active" id="notifications"><?php include '_notifications.php' ?></div>
                <div class="tab-pane" id="campaigns"><?php include '_campaigns.php' ?></div>
                <div class="tab-pane" id="mailing"><?php include '_mailing.php' ?></div>
                <div class="tab-pane" id="sms_details"><?php include '_sms_details.php' ?></div>
                <div class="tab-pane" id="price_list"><?php include '_price.php' ?></div>
                <div class="tab-pane" id="sender_id"><?php include '_sender_id.php' ?></div>
            </div>
        </div>
    </div>

    <?php TableSettings\Dialog::render() ?>
</div>