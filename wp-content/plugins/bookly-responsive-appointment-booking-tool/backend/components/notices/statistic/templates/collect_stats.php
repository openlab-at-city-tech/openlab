<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-tbs" class="wrap">
    <div id="bookly-collect-stats-notice" class="alert alert-info" data-action="bookly_dismiss_collect<?php if ( $enabled ): ?>ing<?php endif ?>_stats_notice">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <div class="form-row">
            <div class="mr-3"><i class="fas fa-info-circle fa-2x"></i></div>
            <div class="col">
                <?php if ( $enabled ): ?>
                    <?php esc_html_e( 'To help us improve Bookly, the plugin anonymously collects usage information. You can opt out of sharing the information in Settings > General.', 'bookly' ) ?>
                    <div class="mt-2">
                        <button type="button" class="btn btn-default" data-dismiss="alert"><?php esc_html_e( 'Close', 'bookly' ) ?></button>
                    </div>
                <?php else: ?>
                    <?php esc_html_e( 'Let the plugin anonymously collect usage information to help Bookly team improve the product.', 'bookly' ) ?>
                    <div class="mt-2">
                        <?php Buttons::render( 'bookly-enable-collecting-stats-btn', 'btn-success', __( 'Agree', 'bookly' ) ) ?>
                        <?php Buttons::render( null, 'btn-default', __( 'Disagree', 'bookly' ), array( 'data-dismiss' => 'alert' ) ) ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>