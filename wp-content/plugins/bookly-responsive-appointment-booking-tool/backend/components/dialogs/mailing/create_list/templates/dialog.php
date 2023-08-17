<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<form id="bookly-create-mailing-list-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'New mailing list', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class=form-group>
                    <label for='bookly-mailing-list-name'><?php esc_html_e( 'Name', 'bookly' ) ?></label>
                    <input type="text" id="bookly-mailing-list-name" class="form-control" name="title"/>
                </div>
            </div>
            <div class="modal-footer">
                <?php Buttons::renderSubmit() ?>
                <?php Buttons::renderCancel() ?>
            </div>
        </div>
    </div>
</form>