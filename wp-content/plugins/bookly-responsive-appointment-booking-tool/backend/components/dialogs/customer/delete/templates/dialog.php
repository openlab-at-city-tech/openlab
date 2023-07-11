<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
?>
<form id="bookly-delete-dialog" class="bookly-modal bookly-fade" tabindex=-1>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Delete customers', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="bookly-js-delete-with-events"><?php esc_html_e( 'You are going to delete customers with existing bookings. Notifications will not be sent to them.', 'bookly' ) ?></p>
                <p class="bookly-js-delete-without-events"><?php esc_html_e( 'You are going to delete customers, are you sure?', 'bookly' ) ?></p>
                <div class="bookly-js-delete-with-events bookly-collapse">
                    <?php Inputs::renderCheckBox( __( 'Delete customers with existing bookings', 'bookly' ), null, null, array( 'id' => 'bookly-js-delete-with-events-checkbox' ) ) ?>
                </div>
                <div>
                    <?php Inputs::renderCheckBox( __( 'Delete customers\' WordPress accounts if there are any', 'bookly' ), null, null, array( 'id' => 'bookly-js-delete-with-wp-user-checkbox' ) ) ?>
                    <?php Inputs::renderCheckBox( __( 'Remember my choice', 'bookly' ), null, null, array( 'id' => 'bookly-js-remember-choice-checkbox' ) ) ?>
                </div>
            </div>
            <div class="modal-footer">
                <?php Buttons::renderDelete( null, 'bookly-js-delete', null, array(), false ) ?>
                <?php Buttons::renderCancel() ?>
            </div>
        </div>
    </div>
</form>