<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Modules\Appearance\Proxy;
use Bookly\Backend\Components\Editable\Elements;
use Bookly\Backend\Modules\Appearance\Codes;
use Bookly\Lib\Config;
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Utils\Price;

/** @var WP_Locale $wp_locale */
global $wp_locale;
?>
<div class="bookly-form">
    <?php include '_progress_tracker.php' ?>

    <div class="bookly-service-step">
        <div class="bookly-box">
            <span class="bookly-bold bookly-desc">
                <?php Elements::renderText( 'bookly_l10n_info_service_step' ) ?>
            </span>
        </div>
        <div class="bookly-mobile-step-1 bookly-js-mobile-step-1 bookly-box">
            <div class="bookly-js-chain-item bookly-table bookly-box">
                <?php Proxy\Locations::renderLocation() ?>
                <div class="bookly-form-group">
                    <?php Elements::renderLabel( array( 'bookly_l10n_label_category', 'bookly_l10n_option_category', ) ) ?>
                    <div>
                        <select class="bookly-select-mobile bookly-js-select-category">
                            <option value="" data-option="bookly_l10n_option_category"><?php echo esc_html( get_option( 'bookly_l10n_option_category' ) ) ?></option>
                            <option value="1">Cosmetic Dentistry</option>
                            <option value="2">Invisalign</option>
                            <option value="3">Orthodontics</option>
                            <option value="4">Dentures</option>
                        </select>
                    </div>
                </div>
                <div class="bookly-form-group">
                    <?php Elements::renderLabel( array(
                        'bookly_l10n_label_service',
                        'bookly_l10n_option_service',
                        'bookly_l10n_required_service',
                    ) ) ?>
                    <div>
                        <select class="bookly-select-mobile bookly-js-select-service bookly-animate">
                            <option value="0" data-option="bookly_l10n_option_service"><?php echo esc_html( get_option( 'bookly_l10n_option_service' ) ) ?></option>
                            <option value="1" class="service-name-duration">Crown and Bridge (<?php echo DateTime::secondsToInterval( 3600 ) ?>)</option>
                            <option value="-1" class="service-name">Crown and Bridge</option>
                            <option value="2" class="service-name-duration">Teeth Whitening (<?php echo DateTime::secondsToInterval( 3600 * 2 ) ?>)</option>
                            <option value="-2" class="service-name">Teeth Whitening</option>
                            <option value="3" class="service-name-duration">Veneers (<?php echo DateTime::secondsToInterval( 3600 * 12 ) ?>)</option>
                            <option value="-3" class="service-name">Veneers</option>
                            <option value="4" class="service-name-duration">Invisalign (invisable braces) (<?php echo DateTime::secondsToInterval( 3600 * 24 ) ?>)</option>
                            <option value="-4" class="service-name">Invisalign (invisable braces)</option>
                            <option value="5" class="service-name-duration">Orthodontics (braces) (<?php echo DateTime::secondsToInterval( 3600 * 8 ) ?>)</option>
                            <option value="-5" class="service-name">Orthodontics (braces)</option>
                            <option value="6" class="service-name-duration">Wisdom tooth Removal (<?php echo DateTime::secondsToInterval( 3600 * 6 ) ?>)</option>
                            <option value="-6" class="service-name">Wisdom tooth Removal</option>
                            <option value="7" class="service-name-duration">Root Canal Treatment (<?php echo DateTime::secondsToInterval( 3600 * 16 ) ?>)</option>
                            <option value="-7" class="service-name">Root Canal Treatment</option>
                            <option value="8" class="service-name-duration">Dentures (<?php echo DateTime::secondsToInterval( 3600 * 48 ) ?>)</option>
                            <option value="-8" class="service-name">Dentures</option>
                        </select>
                    </div>
                </div>
                <div class="bookly-form-group">
                    <?php Elements::renderLabel( array(
                        'bookly_l10n_label_employee',
                        'bookly_l10n_option_employee',
                        'bookly_l10n_required_employee',
                    ) ) ?>
                    <div>
                        <select class="bookly-select-mobile bookly-js-select-employee bookly-animate">
                            <option value="0" data-option="bookly_l10n_option_employee"><?php echo esc_html( get_option( 'bookly_l10n_option_employee' ) ) ?></option>
                            <option value="1" class="employee-name-price">Nick Knight (<?php echo Price::format( 350 ) ?>)</option>
                            <option value="-1" class="employee-name">Nick Knight</option>
                            <option value="2" class="employee-name-price">Jane Howard (<?php echo Price::format( 375 ) ?>)</option>
                            <option value="-2" class="employee-name">Jane Howard</option>
                            <option value="3" class="employee-name-price">Ashley Stamp (<?php echo Price::format( 300 ) ?>)</option>
                            <option value="-3" class="employee-name">Ashley Stamp</option>
                            <option value="4" class="employee-name-price">Bradley Tannen (<?php echo Price::format( 400 ) ?>)</option>
                            <option value="-4" class="employee-name">Bradley Tannen</option>
                            <option value="5" class="employee-name-price">Wayne Turner (<?php echo Price::format( 350 ) ?>)</option>
                            <option value="-5" class="employee-name">Wayne Turner</option>
                            <option value="6" class="employee-name-price">Emily Taylor (<?php echo Price::format( 350 ) ?>)</option>
                            <option value="-6" class="employee-name">Emily Taylor</option>
                            <option value="7" class="employee-name-price">Hugh Canberg (<?php echo Price::format( 380 ) ?>)</option>
                            <option value="-7" class="employee-name">Hugh Canberg</option>
                            <option value="8" class="employee-name-price">Jim Gonzalez (<?php echo Price::format( 390 ) ?>)</option>
                            <option value="-8" class="employee-name">Jim Gonzalez</option>
                            <option value="9" class="employee-name-price">Nancy Stinson (<?php echo Price::format( 360 ) ?>)</option>
                            <option value="-9" class="employee-name">Nancy Stinson</option>
                            <option value="10" class="employee-name-price">Marry Murphy (<?php echo Price::format( 350 ) ?>)</option>
                            <option value="-10" class="employee-name">Marry Murphy</option>
                        </select>
                    </div>
                </div>
                <?php Proxy\CustomDuration::renderServiceDuration() ?>
                <?php Proxy\GroupBooking::renderNOP() ?>
                <?php Proxy\MultiplyAppointments::renderQuantity() ?>
                <?php Proxy\ChainAppointments::renderChainTip() ?>
            </div>
            <div id="bookly-category-info" class="bookly-box bookly-category-info">
                <?php Elements::renderText( 'bookly_l10n_step_service_category_info', Codes::getCategoryCodes() ) ?>
            </div>
            <div id="bookly-service-info" class="bookly-box bookly-service-info">
                <?php Elements::renderText( 'bookly_l10n_step_service_service_info', Codes::getServiceCodes() ) ?>
            </div>
            <div id="bookly-staff-info" class="bookly-box bookly-staff-info">
                <?php Elements::renderText( 'bookly_l10n_step_service_staff_info', Codes::getStaffCodes() ) ?>
            </div>

            <?php Proxy\ChainAppointments::renderBookMore() ?>

            <div class="bookly-right bookly-mobile-next-step bookly-js-mobile-next-step bookly-btn bookly-none">
                <?php Elements::renderString( array( 'bookly_l10n_step_service_mobile_button_next' ) ) ?>
            </div>
        </div>
        <div class="bookly-mobile-step-2 bookly-js-mobile-step-2">
            <div class="bookly-box">
                <div class="bookly-left">
                    <div class="bookly-available-date bookly-js-available-date bookly-left">
                        <div class="bookly-form-group">
                            <?php Elements::renderLabel( array( 'bookly_l10n_label_select_date', ) ) ?>
                            <div>
                                <input class="bookly-date-from bookly-js-date-from" style="background-color: #fff;" type="text" data-value="<?php echo date( 'Y-m-d' ) ?>" value="<?php echo DateTime::formatDate( date_create()->format( 'Y-m-d' ) ) ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="bookly-week-days bookly-js-week-days bookly-table bookly-left">
                        <?php foreach ( $wp_locale->weekday_abbrev as $day ) : ?>
                            <div class="bookly-form-group">
                                <label for="bookly-week-day-<?php echo esc_attr( $day ) ?>"><?php echo esc_html( $day ) ?></label>
                                <input id="bookly-week-day-<?php echo esc_attr( $day ) ?>" checked="checked" type="checkbox">
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
                <div class="bookly-time-range bookly-js-time-range bookly-left">
                    <div class="bookly-form-group bookly-left">
                        <?php Elements::renderLabel( array( is_rtl() ? 'bookly_l10n_label_finish_by' : 'bookly_l10n_label_start_from' ) ) ?>
                        <div>
                            <select>
                                <?php for ( $i = 28800; $i <= 64800; $i += 3600 ) : ?>
                                    <option <?php is_rtl() ? selected( $i == 64800 ) : '' ?>><?php echo DateTime::formatTime( $i ) ?></option>
                                <?php endfor ?>
                            </select>
                        </div>
                    </div>
                    <div class="bookly-form-group bookly-left">
                        <?php Elements::renderLabel( array( is_rtl() ? 'bookly_l10n_label_start_from' : 'bookly_l10n_label_finish_by', ) ) ?>
                        <div>
                            <select>
                                <?php for ( $i = 28800; $i <= 64800; $i += 3600 ) : ?>
                                    <option<?php is_rtl() ? '' : selected( $i == 64800 ) ?>><?php echo DateTime::formatTime( $i ) ?></option>
                                <?php endfor ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bookly-box bookly-nav-steps">
                <div class="bookly-right bookly-mobile-prev-step bookly-js-mobile-prev-step bookly-btn bookly-none">
                    <?php Elements::renderString( array( 'bookly_l10n_button_back' ) ) ?>
                </div>
                <?php Proxy\Cart::renderButton() ?>
                <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
                    <div class="bookly-next-step bookly-js-next-step bookly-btn">
                        <?php if ( Config::customJavaScriptActive() ): ?>
                            <?php Proxy\CustomJavaScript::renderNextButton( 'service' ) ?>
                        <?php else: ?>
                            <?php Elements::renderString( array( 'bookly_l10n_step_service_button_next' ) ) ?>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="display: none">
    <?php foreach ( array( 'bookly_l10n_required_service', 'bookly_l10n_required_name', 'bookly_l10n_required_phone', 'bookly_l10n_required_email', 'bookly_l10n_required_employee', 'bookly_l10n_required_location' ) as $validator ) : ?>
        <div data-option="<?php echo esc_attr( $validator ) ?>"><?php echo esc_html( get_option( $validator ) ) ?></div>
    <?php endforeach ?>
</div>