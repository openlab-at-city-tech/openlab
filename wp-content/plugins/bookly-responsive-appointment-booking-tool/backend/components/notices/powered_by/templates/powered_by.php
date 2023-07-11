<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-tbs" class="wrap">
    <div id="bookly-powered-by" class="alert alert-info" data-action="bookly_dismiss_powered_by_notice">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <div class="form-row">
            <div class="mr-3"><i class="fas fa-info-circle fa-2x"></i></div>
            <div class="col">
                <?php esc_html_e( 'Allow the plugin to set a Powered by Bookly notice on the booking widget to spread information about the plugin. This will allow the team to improve the product and enhance its functionality.', 'bookly' ) ?>
                <div class="mt-2">
                    <?php Buttons::render( 'bookly-show-powered-by', 'btn-success', __( 'Agree', 'bookly' ) ) ?>
                    <?php Buttons::render( null, 'btn-default', __( 'Disagree', 'bookly' ), array( 'data-dismiss' => 'alert' ) ) ?>
                </div>
            </div>
        </div>
    </div>
</div>