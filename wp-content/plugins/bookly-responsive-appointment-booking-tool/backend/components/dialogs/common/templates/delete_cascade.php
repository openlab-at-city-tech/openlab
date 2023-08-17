<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div class="bookly-modal bookly-fade bookly-js-delete-cascade-confirm" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Are you sure?', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p><?php esc_html_e( 'You are going to delete an item that might be involved in existing appointments. All related appointments will be deleted. Please double-check and edit appointments before this item deletion if needed.', 'bookly' ) ?></p>
            </div>
            <div class="modal-footer">
                <?php Buttons::render( null, 'btn-danger bookly-js-delete', __( 'Delete', 'bookly' ) ) ?>
                <?php Buttons::render( null, 'btn-success bookly-js-edit', __( 'Edit appointments', 'bookly' ) ) ?>
                <?php Buttons::renderCancel() ?>
            </div>
        </div>
    </div>
</div>