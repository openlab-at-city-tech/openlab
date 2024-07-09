<?php if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Controls\Buttons;
/**
 * @var Bookly\Lib\Cloud\SMS $sms
 * @var $datatables
 */
?>
<div class="alert alert-info"><?php esc_html_e( 'Please take into account that not all countries by law allow custom SMS sender ID. Please check if particular country supports custom sender ID in our price list. Also please note that prices for messages with custom sender ID are usually 20% - 25% higher than normal message price.', 'bookly' ) ?></div>

<div class="row justify-content-between">
    <div class="col-md-8">
        <label class="control-label" for="bookly-sender-id-input"><?php esc_html_e( 'Request Sender ID', 'bookly' ) ?> <?php if ( $sms->getSenderIdApprovalDate() ) : ?> <span><?php _e( 'or', 'bookly' ) ?> <a href="#" id="bookly-reset-sender_id"><?php esc_html_e( 'Reset to default', 'bookly' ) ?></a></span><?php endif ?></label>
        <form class="form-row">
            <div class="col-lg-2 col-md-4">
                <input id="bookly-sender-id-input" class="form-control" type="text" maxlength="11" required="required" minlength="1" value="" />
            </div>
            <div>
                <?php Buttons::render( 'bookly-request-sender_id', 'btn btn-success', __( 'Request', 'bookly' ) ) ?>
                <?php Buttons::render( 'bookly-cancel-sender_id', 'btn btn-danger', __( 'Cancel request', 'bookly' ) . '…', array( 'style' => 'display:none' ) ) ?>
            </div>
        </form>
        <small class="form-text text-muted"><?php esc_html_e( 'Can only contain letters or digits (up to 11 characters).', 'bookly' ) ?></small>
    </div>
    <div class="col-md-4 form-row justify-content-end mt-5">
        <?php Dialogs\TableSettings\Dialog::renderButton( 'sms_sender', 'BooklyL10n', esc_attr( add_query_arg( 'tab', 'sender_id' ) ) ) ?>
    </div>
</div>

<table id="bookly-sender-ids" class="table table-striped w-100">
    <thead>
    <tr>
        <?php foreach ( $datatables['sms_sender']['settings']['columns'] as $column => $show ) : ?>
            <?php if ( $show ) : ?>
                <th><?php echo esc_attr( $datatables['sms_sender']['titles'][ $column ] ) ?></th>
            <?php endif ?>
        <?php endforeach ?>
    </tr>
    </thead>
</table>