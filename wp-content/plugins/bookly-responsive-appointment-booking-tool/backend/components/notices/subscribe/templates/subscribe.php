<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-tbs" class="wrap">
    <div id="bookly-subscribe-notice" class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <div class="form-row">
            <div class="mr-3"><i class="fas fa-info-circle fa-2x"></i></div>
            <div class="col">
                <label for="bookly-subscribe-email"><?php esc_html_e( 'Subscribe to monthly emails about Bookly improvements and new releases.', 'bookly' ) ?></label>
                <div class="input-group" style="max-width: 400px;">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-fw fa-envelope"></i></div>
                    </div>
                    <input type="text" id="bookly-subscribe-email" class="form-control"/>
                    <div class="input-group-append">
                        <?php Buttons::render( 'bookly-subscribe-btn', 'btn-info', __( 'Send', 'bookly' ) ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>