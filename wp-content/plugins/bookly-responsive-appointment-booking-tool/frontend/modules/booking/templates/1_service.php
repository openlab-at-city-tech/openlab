<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Frontend\Modules\Booking\Proxy;
use Bookly\Lib\Utils\Common;
/** @var Bookly\Lib\UserBookingData $userData */
echo Common::stripScripts( $progress_tracker );
$checkbox_prefix = 'bookly-week-day-' . $userData->getFormId() . '-';
?>
<div class="bookly-service-step">
    <div class="bookly-box bookly-bold"><?php echo Common::html( $info_text ) ?></div>
    <div class="bookly-mobile-step-1 bookly-js-mobile-step-1">
        <div class="bookly-js-chain"></div>
        <div class="bookly-nav-steps bookly-box">
            <?php if ( $show_cart_btn ) : ?>
                <?php Proxy\Cart::renderButton() ?>
            <?php endif ?>
            <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
                <button class="bookly-right bookly-mobile-next-step bookly-js-mobile-next-step bookly-btn bookly-none ladda-button" data-style="zoom-in" data-spinner-size="40">
                    <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_step_service_mobile_button_next' ) ?></span>
                </button>
            </div>
        </div>
    </div>
    <div class="bookly-mobile-step-2 bookly-js-mobile-step-2">
        <div class="bookly-box" style="display: table;">
            <div class="bookly-left bookly-mobile-float-none">
                <div class="bookly-available-date bookly-js-available-date bookly-left bookly-mobile-float-none">
                    <div class="bookly-form-group">
                        <span class="bookly-bold"><?php echo Common::getTranslatedOption( 'bookly_l10n_label_select_date' ) ?></span>
                        <div>
                           <input class="bookly-date-from bookly-js-date-from" type="text" value="" data-value="<?php echo esc_attr( $userData->getDateFrom() ) ?>" />
                        </div>
                    </div>
                </div>
                <?php if ( ! empty ( $days ) ) : ?>
                    <div class="bookly-week-days bookly-js-week-days bookly-table bookly-left bookly-mobile-float-none">
                        <?php foreach ( $days as $key => $day ) : ?>
                            <div>
                                <div class="bookly-bold"><?php echo esc_html( $day ) ?></div>
                                <input id="<?php echo esc_attr( $checkbox_prefix . $key ) ?>" value="<?php echo esc_attr( $key ) ?>" <?php checked( in_array( $key, $days_checked ) ) ?> type="checkbox"/>
                                <label for="<?php echo esc_attr( $checkbox_prefix . $key ) ?>"></label>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
            <?php if ( ! empty ( $times ) ) : ?>
                <div class="bookly-time-range bookly-js-time-range bookly-left bookly-mobile-float-none">
                    <?php if ( is_rtl() ) : ?>
                        <?php self::renderTemplate( '1_service_time', array( 'type' => 'to', 'times' => $times, 'selected' => $userData->getTimeTo() ) ) ?>
                        <?php self::renderTemplate( '1_service_time', array( 'type' => 'from', 'times' => $times, 'selected' => $userData->getTimeFrom() ) ) ?>
                    <?php else: ?>
                        <?php self::renderTemplate( '1_service_time', array( 'type' => 'from', 'times' => $times, 'selected' => $userData->getTimeFrom() ) ) ?>
                        <?php self::renderTemplate( '1_service_time', array( 'type' => 'to', 'times' => $times, 'selected' => $userData->getTimeTo() ) ) ?>
                    <?php endif?>
                </div>
            <?php endif ?>
        </div>
        <div class="bookly-box bookly-nav-steps">
            <button class="bookly-left bookly-mobile-prev-step bookly-js-mobile-prev-step bookly-btn bookly-none ladda-button" data-style="zoom-in" data-spinner-size="40">
                <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_button_back' ) ?></span>
            </button>
            <?php if ( $show_cart_btn ) : ?>
                <?php Proxy\Cart::renderButton() ?>
            <?php endif ?>
            <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
                <button class="bookly-next-step bookly-js-next-step bookly-btn ladda-button" data-style="zoom-in" data-spinner-size="40">
                    <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_step_service_button_next' ) ?></span>
                </button>
            </div>
        </div>
    </div>
</div>