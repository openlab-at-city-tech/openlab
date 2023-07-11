<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<form id="bookly-js-recharge-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php esc_html_e( 'Account recharge', 'bookly' ) ?></h4>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="bookly-recharge-amounts">
                    <?php static::renderTemplate( '_amounts', compact( 'cloud' ) ) ?>
                </div>
                <div id="bookly-recharge-payment">
                    <?php static::renderTemplate( '_payment' ) ?>
                </div>
                <div id="bookly-recharge-accepted">
                    <?php static::renderTemplate( '_accepted' ) ?>
                </div>
                <div id="bookly-recharge-cancelled">
                    <?php static::renderTemplate( '_cancelled' ) ?>
                </div>
            </div>
        </div>
    </div>
</form>
<?php static::renderTemplate( '_confirm_modal' ) ?>