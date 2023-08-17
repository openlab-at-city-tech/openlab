<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-delete-dialog" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Delete', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p><?php esc_html_e( 'You are going to delete appointment(s). Notifications will be sent in accordance with your settings.', 'bookly' ) ?></p>

                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" id="bookly-delete-notify" type="checkbox"/>
                    <label class="custom-control-label" for="bookly-delete-notify"><?php esc_html_e( 'Send notifications', 'bookly' ) ?>
                </div>

                <div class="form-group" style="display: none;" id="bookly-delete-reason-cover">
                    <input class="form-control" type="text" id="bookly-delete-reason" placeholder="<?php esc_attr_e( 'Cancellation reason (optional)', 'bookly' ) ?>" />
                </div>
            </div>
            <div class="modal-footer">
                <?php Buttons::render( 'bookly-delete', 'btn-danger', __( 'Delete', 'bookly' ), array(), '<i class="far fa-fw fa-trash-alt mr-1"></i>{caption}' ) ?>
                <?php Buttons::renderCancel() ?>
            </div>
        </div>
    </div>
</div>