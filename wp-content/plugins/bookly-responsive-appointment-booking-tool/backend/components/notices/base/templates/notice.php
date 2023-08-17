<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-tbs" class="wrap">
    <div id="<?php echo esc_attr( $id ) ?>" class="alert alert-success alert-dismissible" role="alert" <?php if ( $hidden ) : ?>style="display: none" <?php endif ?>>
        <div class="row text-center text-md-left">
            <div class="col-12 col-md-auto pr-md-0">
                <img src="<?php echo plugins_url( 'bookly-responsive-appointment-booking-tool/backend/components/notices/base/images/photo.png' ) ?>" alt="Daniel Williams, PO at Bookly" width="72" style="width: 72px"/>
            </div>
            <div class="col">
                <div class="row">
                    <div class="col-12">
                        <div><b class="bookly-js-alert-title"><?php echo esc_html( $title ) ?></b> <?php echo esc_html( $sub_title ) ?></div>
                        <div class="font-weight-bold mt-1"><?php echo nl2br( esc_html( $message ) ) ?></div>
                        <small class="text-muted">Daniel Williams, PO at Bookly</small>
                    </div>
                    <div class="col-12 mt-2">
                        <?php foreach ( $buttons as $button ) : ?>
                            <?php Buttons::render( null, $button['class'] . ' mr-1 mb-1', $button['caption'] ) ?>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="close <?php echo esc_attr( $dismiss_js_class ) ?>" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>