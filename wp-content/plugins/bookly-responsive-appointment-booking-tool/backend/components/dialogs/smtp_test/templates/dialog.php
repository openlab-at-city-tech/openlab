<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-smtp-test-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title h5"><?php esc_html_e( 'Test email', 'bookly' ) ?></div>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="bookly-smtp-to"><?php esc_html_e( 'Recipient email', 'bookly' ) ?></label>
                    <input class="form-control" id="bookly-smtp-to" type="text" value=""/>
                </div>
                <div id="bookly-smtp-status" class="font-weight-bold my-3" style="display: none;">
                    <?php esc_html_e( 'Status', 'bookly' ) ?>: <span id="bookly-smtp-status-text"></span>
                </div>
                <div id="bookly-smtp-log" class="text-muted" style="display: none;">
                </div>
            </div>
            <div class="modal-footer">
                <?php Buttons::render( 'bookly-send-smtp-test', 'btn-success', __( 'Send', 'bookly' ) ) ?>
                <?php Buttons::renderCancel() ?>
            </div>
        </div>
    </div>
</div>