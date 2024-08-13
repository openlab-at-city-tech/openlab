<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Dialogs;
?>
<div id="bookly-cloud-staff-cabinet-key-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="bookly-token"><?php esc_html_e( 'Associated staff member', 'bookly' ) ?></label>
                    <select id="bookly-token" class="form-control custom-select" name="staff_id">
                    </select>
                </div>
                <div class="form-group">
                    <?php Inputs::renderCheckBox( __( 'Send notifications', 'bookly' ), null, null, array( 'id' => 'bookly-send-notifications' ) ) ?>
                </div>
            </div>
            <div class="modal-footer">
                <?php Buttons::renderSubmit() ?>
                <?php Buttons::renderCancel() ?>
            </div>
        </div>
    </div>
</div>
<?php Dialogs\Queue\Dialog::render() ?>