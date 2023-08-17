<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Config;
use Bookly\Frontend\Modules\Booking\Proxy;
use Bookly\Lib\Utils\Common;

?>
<?php echo Common::stripScripts( $progress_tracker ) ?>
<div class="bookly-box"><?php echo Common::html( $info_text ) ?></div>
<?php Proxy\Shared::renderWaitingListInfoText() ?>
<div class="bookly-box bookly-label-error"></div>
<?php if ( $has_slots ) : ?>
    <?php Proxy\Pro::renderTimeZoneSwitcher() ?>
<?php endif ?>
<?php if ( Config::showCalendar() ) : ?>
    <style type="text/css">
        .picker__holder {
            top: 0;
            left: 0;
        }

        .bookly-time-step {
            margin-left: 0;
            margin-right: 0;
        }
    </style>
    <div class="bookly-input-wrap bookly-slot-calendar bookly-js-slot-calendar">
        <input style="display: none" class="bookly-js-selected-date" type="text" value="" data-value="<?php echo esc_attr( $date ) ?>"/>
    </div>
<?php endif ?>
<?php if ( $has_slots ) : ?>
    <div class="bookly-time-step">
        <div class="bookly-columnizer-wrap">
            <div class="bookly-columnizer">
                <?php /* here _time_slots */ ?>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="bookly-not-time-screen<?php if ( ! Config::showCalendar() ) : ?> bookly-not-calendar<?php endif ?>">
        <?php esc_html_e( 'No time is available for selected criteria.', 'bookly' ) ?>
    </div>
<?php endif ?>
<div class="bookly-box bookly-nav-steps bookly-clear">
    <button class="bookly-back-step bookly-js-back-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
        <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_button_back' ) ?></span>
    </button>
    <?php if ( $show_cart_btn ) : ?>
        <?php Proxy\Cart::renderButton() ?>
    <?php endif ?>
    <?php if ( $has_slots ) : ?>
        <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
            <?php Proxy\Tasks::renderSkipButton( $userData ) ?>
            <button class="bookly-time-prev bookly-btn bookly-left ladda-button" data-style="zoom-in" style="display: none" data-spinner-size="40">
                <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_button_time_prev' ) ?></span>
            </button>
            <button class="bookly-time-next bookly-btn bookly-left ladda-button" data-style="zoom-in" data-spinner-size="40">
                <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_button_time_next' ) ?></span>
            </button>
        </div>
    <?php endif ?>
</div>
