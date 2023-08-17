<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls;
?>
<div id="bookly-product-update-required-modal" class="bookly-modal bookly-fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php esc_html_e( 'Bookly update required', 'bookly' ) ?></h4>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div><?php esc_html_e( 'This product is not supported by your version of Bookly plugin. Please update Bookly to the latest version.', 'bookly' ) ?></div>
            </div>
            <div class="modal-footer">
                <?php Controls\Buttons::renderCancel( __( 'Ok', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</div>