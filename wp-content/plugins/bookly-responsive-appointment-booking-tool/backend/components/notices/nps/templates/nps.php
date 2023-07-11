<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-tbs" class="wrap bookly-js-nps-notice">
    <div id="bookly-nps-notice" class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <div class="form-row">
            <div class="mr-3"><i class="fas fa-info-circle fa-2x"></i></div>
            <div class="col">
                <div id="bookly-nps-quiz" class="my-2">
                    <label><?php esc_html_e( 'How likely is it that you would recommend Bookly to a friend or colleague?', 'bookly' ) ?></label>
                    <div>
                        <?php for ( $i = 1; $i <= 10; ++ $i ): ?><i class="bookly-js-star bookly-cursor-pointer far fa-star fa-lg text-muted"></i><?php endfor ?>
                    </div>
                </div>
                <div id="bookly-nps-form" class="mt-4 bookly-collapse" style="max-width:400px;">
                    <div class="form-group">
                        <label for="bookly-nps-msg" class="control-label"><?php esc_html_e( 'What do you think should be improved?', 'bookly' ) ?></label>
                        <textarea id="bookly-nps-msg" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="bookly-nps-email" class="control-label"><?php esc_html_e( 'Please enter your email (optional)', 'bookly' ) ?></label>
                        <input type="text" id="bookly-nps-email" class="form-control" value="<?php echo esc_attr( $current_user->user_email ) ?>"/>
                    </div>
                    <?php Buttons::render( 'bookly-nps-btn', 'btn-success', __( 'Send', 'bookly' ) ) ?>
                </div>
            </div>
        </div>
    </div>
</div>