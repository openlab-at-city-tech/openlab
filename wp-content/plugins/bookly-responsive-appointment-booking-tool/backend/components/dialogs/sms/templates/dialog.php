<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<form id="bookly-js-notification-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <?php static::renderTemplate( '_modal_body', compact( 'self', 'gateway' ) ) ?>
            </div>
            <div class="modal-footer">
                <?php Buttons::render( null, 'bookly-js-save btn-success', __( 'Save notification', 'bookly' ) ) ?>
                <?php Buttons::renderCancel() ?>
            </div>
        </div>
    </div>
</form>