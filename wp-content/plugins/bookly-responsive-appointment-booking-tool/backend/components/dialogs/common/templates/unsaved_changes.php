<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div class="bookly-modal bookly-fade bookly-js-unsaved-changes" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title h5"><?php esc_html_e( 'Are you sure?', 'bookly' ) ?></div>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p><?php esc_html_e( 'All unsaved changes will be lost.', 'bookly' ) ?></p>
            </div>
            <div class="modal-footer">
                <?php Buttons::renderSubmit( null, 'bookly-js-save-changes' ) ?>
                <?php Buttons::render( null, 'btn-danger bookly-js-ignore-changes', __( 'Don\'t save', 'bookly' ) ) ?>
                <?php Buttons::renderCancel() ?>
            </div>
        </div>
    </div>
</div>