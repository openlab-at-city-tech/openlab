<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib;
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components;
use Bookly\Backend\Modules\Appearance\Proxy;
use Bookly\Backend\Modules\Appearance;
use Bookly\Lib\Utils\Advertisement;

?>
<style type="text/css">
    :root {
        --bookly-main-color: <?php echo esc_attr( get_option( 'bookly_app_color', '#f4662f' ) ) ?>;
    }

    <?php if ( trim( $custom_css ) ) : ?>
    <?php echo Lib\Utils\Common::stripScripts( $custom_css ) ?>
    <?php endif ?>
</style>

<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Appearance', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <?php if ( Lib\Config::proActive() ) : ?>
        <div class="card mb-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col"><h5 class="mb-0"><?php esc_html_e( 'Bookly form', 'bookly' ) ?></h5></div>
                    <div class="col text-right"><a class="btn btn-default" href="<?php echo add_query_arg( array( 'page' => Appearance\Page::pageSlug() ), admin_url( 'admin.php' ) ) ?>"><?php esc_html_e( 'Back', 'bookly' ) ?></a></div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <?php Advertisement::render( 'appearance-top-bar' ) ?>
    <?php endif ?>
    <div class="card mb-2">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-3 col-xl-2 bookly-color-picker-wrapper mb-2 mb-lg-0">
                    <div class="bookly-color-picker">
                        <input name="color" value="<?php form_option( 'bookly_app_color' ) ?>" class="bookly-js-color-picker" data-selected="<?php form_option( 'bookly_app_color' ) ?>" type="text"/>
                    </div>
                </div>
                <div class="col-lg-9 col-xl-10">
                    <div class="row">
                        <div class="col-lg-4 col-xl-3 mb-2">
                            <?php Inputs::renderCheckBox( __( 'Show form progress tracker', 'bookly' ), null, get_option( 'bookly_app_show_progress_tracker' ), array( 'id' => 'bookly-show-progress-tracker' ) ) ?>
                        </div>
                        <div class="col-lg-4 col-xl-3 mb-2">
                            <?php Inputs::renderCheckBox( __( 'Align buttons to the left', 'bookly' ), null, get_option( 'bookly_app_align_buttons_left' ), array( 'id' => 'bookly-align-buttons-left' ) ) ?>
                        </div>
                        <?php Proxy\ServiceExtras::renderShowStep() ?>
                        <?php Proxy\Tasks::renderShowTimeStep() ?>
                        <?php Proxy\RecurringAppointments::renderShowStep() ?>
                        <?php Proxy\Cart::renderShowStep() ?>
                        <div class="col-lg-4 col-xl-3 mb-2">
                            <?php Inputs::renderCheckBox( __( 'Invert datepicker colors', 'bookly' ), null, get_option( 'bookly_app_datepicker_inverted' ), array( 'id' => 'bookly-invert-datepicker-colors' ) ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs bookly-js-appearance-steps flex-column flex-lg-row bookly-nav-tabs-md" role="tablist">
                <?php $i = 1 ?>
                <?php foreach ( $steps as $data ) : ?>
                    <li class="nav-item text-center" <?php if ( ! $data['show'] ) : ?>style="display: none;"<?php endif ?>>
                        <a class="nav-link<?php if ( $data['step'] == 1 ) : ?> active<?php endif ?>" href="#bookly-step-<?php echo esc_attr( $data['step'] ) ?>" data-toggle="bookly-tab"><span class="bookly-js-step-number"><?php echo esc_html( $data['show'] ? $i++ : $i ) ?></span>. <?php echo esc_html( $data['title'] ) ?>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="card-body">
            <div id="bookly-appearance">

                <?php if ( ! get_user_meta( get_current_user_id(), Lib\Plugin::getPrefix() . 'dismiss_appearance_notice', true ) ): ?>
                    <div class="alert alert-info alert-dismissible my-2" id="bookly-js-hint-alert" role="alert">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <p>
                            <?php esc_html_e( 'Click on the underlined text to edit.', 'bookly' ) ?>
                        </p>
                        <p class="mb-0"><?php esc_html_e( 'How to publish this form on your web site?', 'bookly' ) ?>
                            <br/>
                            <?php esc_html_e( 'Open the page where you want to add the booking form in page edit mode and click on the "Add Bookly booking form" button. Choose which fields you\'d like to keep or remove from the booking form. Click Insert, and the booking form will be added to the page.', 'bookly' ) ?>
                            <a href="<?php echo Bookly\Lib\Utils\Common::prepareUrlReferrers( 'https://support.booking-wp-plugin.com/hc/en-us/articles/212800185-Publish-Booking-Form', 'appearance' ) ?>" target="_blank"><?php esc_html_e( 'Read more', 'bookly' ) ?></a>
                        </p>
                    </div>
                <?php endif ?>

                <div id="bookly-step-settings">
                    <div class="bookly-js-service-settings">
                        <div class="row">
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Make selecting employee required', 'bookly' ), null, get_option( 'bookly_app_required_employee' ), array( 'id' => 'bookly-required-employee' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show service price next to employee name', 'bookly' ), null, get_option( 'bookly_app_staff_name_with_price' ), array( 'id' => 'bookly-staff-name-with-price' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show service duration next to service name', 'bookly' ), null, get_option( 'bookly_app_service_name_with_duration' ), array( 'id' => 'bookly-service-name-with-duration' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show category info', 'bookly' ), null, get_option( 'bookly_app_show_category_info' ), array( 'id' => 'bookly-show-category-info' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show service info', 'bookly' ), null, get_option( 'bookly_app_show_service_info' ), array( 'id' => 'bookly-show-service-info' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show staff info', 'bookly' ), null, get_option( 'bookly_app_show_staff_info' ), array( 'id' => 'bookly-show-staff-info' ) ) ?>
                            </div>
                            <?php Proxy\Shared::renderServiceStepSettings() ?>
                        </div>
                    </div>
                    <div class="bookly-js-time-settings bookly-collapse">
                        <div class="row">
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show calendar', 'bookly' ), null, get_option( 'bookly_app_show_calendar' ), array( 'id' => 'bookly-show-calendar' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show blocked timeslots', 'bookly' ), null, get_option( 'bookly_app_show_blocked_timeslots' ), array( 'id' => 'bookly-show-blocked-timeslots' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show each day in one column', 'bookly' ), null, get_option( 'bookly_app_show_day_one_column' ), array( 'id' => 'bookly-show-day-one-column' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <div id="bookly-show-single-slot-popover" data-container="#bookly-show-single-slot-popover" data-toggle="bookly-popover" data-placement="bottom" data-content="<?php esc_attr_e( 'Please note that "I\'m available on or after" picker will be hidden', 'bookly' ) ?>">
                                    <?php Inputs::renderCheckBox( __( 'Show only the nearest timeslot', 'bookly' ), null, Lib\Config::showSingleTimeSlot(), array( 'id' => 'bookly-show-single-slot' ) ) ?>
                                </div>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show only the first timeslot in each day', 'bookly' ), null, Lib\Config::showSingleTimeSlotPerDay(), array( 'id' => 'bookly-show-single-slot-per-day' ) ) ?>
                            </div>
                            <?php Proxy\Pro::renderTimeZoneSwitcherCheckbox() ?>
                            <?php Proxy\Shared::renderTimeStepSettings() ?>
                            <?php Proxy\SpecialHours::renderHighlightSpecialHours() ?>
                        </div>
                    </div>

                    <?php Proxy\RecurringAppointments::renderRepeatStepSettings() ?>
                    <?php Proxy\Cart::renderCartStepSettings() ?>
                    <?php Proxy\ServiceExtras::renderStepSettings() ?>

                    <div class="bookly-js-details-settings bookly-collapse">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <select id="bookly-cst-required-details" class="form-control custom-select" data-default="<?php echo ! array_diff( array( 'phone', 'email' ), get_option( 'bookly_cst_required_details', array() ) ) ? 'both' : current( get_option( 'bookly_cst_required_details', array() ) ) ?>">
                                        <option value="phone"<?php selected( in_array( 'phone', get_option( 'bookly_cst_required_details', array() ) ) && ! in_array( 'email', get_option( 'bookly_cst_required_details', array() ) ) ) ?><?php disabled( get_option( 'bookly_cst_create_account' ) ) ?>><?php esc_html_e( 'Phone field required', 'bookly' ) ?></option>
                                        <option value="email"<?php selected( in_array( 'email', get_option( 'bookly_cst_required_details', array() ) ) && ! in_array( 'phone', get_option( 'bookly_cst_required_details', array() ) ) ) ?>><?php esc_html_e( 'Email field required', 'bookly' ) ?></option>
                                        <option value="both"<?php selected( ! array_diff( array( 'phone', 'email' ), get_option( 'bookly_cst_required_details', array() ) ) ) ?>><?php esc_html_e( 'Both email and phone fields required', 'bookly' ) ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show Login button', 'bookly' ), null, get_option( 'bookly_app_show_login_button' ), array( 'id' => 'bookly-show-login-button' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <div id="bookly-cst-first-last-name-popover" data-container="#bookly-cst-first-last-name-popover" data-toggle="bookly-popover" data-trigger="focus" data-placement="bottom" data-content="<?php esc_attr_e( 'Do not forget to update your email and SMS codes for customer names', 'bookly' ) ?>">
                                    <?php Inputs::renderCheckBox( __( 'Use first and last name instead of full name', 'bookly' ), null, get_option( 'bookly_cst_first_last_name' ), array( 'id' => 'bookly-cst-first-last-name' ) ) ?>
                                </div>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Email confirmation field', 'bookly' ), null, get_option( 'bookly_app_show_email_confirm' ), array( 'id' => 'bookly-cst-show-email-confirm' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show notes field', 'bookly' ), null, get_option( 'bookly_app_show_notes' ), array( 'id' => 'bookly-show-notes' ) ) ?>
                            </div>
                            <?php Proxy\Pro::renderShowStepDetailsSettings() ?>
                            <?php Proxy\GoogleMapsAddress::renderShowGoogleMaps() ?>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show Terms & Conditions checkbox', 'bookly' ), null, get_option( 'bookly_app_show_terms' ), array( 'id' => 'bookly-show-terms' ) ) ?>
                            </div>
                        </div>
                    </div>
                    <div class="bookly-js-payment-settings bookly-collapse">
                        <div class="row">
                            <?php Proxy\Coupons::renderShowCoupons() ?>
                            <?php Proxy\Pro::renderShowGiftCards() ?>
                            <?php Proxy\Pro::renderShowTips() ?>
                        </div>
                        <?php Proxy\Pro::renderBookingStatesSelector() ?>
                    </div>

                    <div class="bookly-js-done-settings bookly-collapse">
                        <div class="row">
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show \'Start over\' button', 'bookly' ), null, get_option( 'bookly_app_show_start_over' ), array( 'id' => 'bookly-show-start-over' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show \'Download ICS\' button', 'bookly' ), null, get_option( 'bookly_app_show_download_ics' ), array( 'id' => 'bookly-show-download-ics' ) ) ?>
                            </div>
                            <div class="col-md-3 my-2">
                                <?php Inputs::renderCheckBox( __( 'Show \'Add to calendar\'', 'bookly' ), null, get_option( 'bookly_app_show_add_to_calendar' ), array( 'id' => 'bookly-show-add-to-calendar' ) ) ?>
                            </div>
                            <?php Proxy\Pro::renderShowQRCode() ?>
                            <?php Proxy\Invoices::renderShowDownloadInvoice() ?>
                        </div>
                        <div class="alert alert-info my-2">
                            <div class="d-flex">
                                <div class="mr-3"><i class="fas fa-info-circle fa-2x"></i></div>
                                <div class="flex-fill">
                                    <div>
                                        <?php esc_html_e( 'The booking form on this step may have different set or states of its elements. It depends on various conditions such as installed/activated add-ons, settings configuration or choices made on previous steps. Select option and click on the underlined text to edit.', 'bookly' ) ?>
                                    </div>
                                    <div class="mt-2">
                                        <select id="bookly-done-step-view" class="form-control custom-select">
                                            <option value="booking-success"><?php esc_html_e( 'Form view in case of successful booking', 'bookly' ) ?></option>
                                            <option value="booking-limit-error"><?php esc_html_e( 'Form view in case the number of bookings exceeds the limit', 'bookly' ) ?></option>
                                            <option value="booking-processing"><?php esc_html_e( 'Form view in case of payment has been accepted for processing', 'bookly' ) ?></option>
                                            <?php Proxy\CustomerGroups::renderStepCompleteOption(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card my-2">
                    <div class="card-body">
                        <div class="tab-content bookly-css-root">
                            <?php foreach ( $steps as $step => $step_name ) : ?>
                                <div id="bookly-step-<?php echo esc_attr( $step ) ?>" class="tab-pane <?php if ( $step == 1 ) : ?>active<?php endif ?>" data-target="<?php echo esc_attr( $step ) ?>">
                                    <?php // Render unique data per step
                                    switch ( $step ) :
                                        case 1:
                                            include '_1_service.php';
                                            break;
                                        case 2:
                                            Proxy\ServiceExtras::renderStep( $self::renderTemplate( '_progress_tracker', compact( 'step' ), false ) );
                                            break;
                                        case 3:
                                            include '_3_time.php';
                                            break;
                                        case 4:
                                            Proxy\RecurringAppointments::renderStep( $self::renderTemplate( '_progress_tracker', compact( 'step' ), false ) );
                                            break;
                                        case 5:
                                            Proxy\Cart::renderStep( $self::renderTemplate( '_progress_tracker', compact( 'step' ), false ) );
                                            break;
                                        case 6:
                                            include '_6_details.php';
                                            break;
                                        case 7:
                                            include '_7_payment.php';
                                            break;
                                        case 8:
                                            include '_8_complete.php';
                                            break;
                                    endswitch ?>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
                <div class="form-row m-0">
                    <?php $self::renderTemplate( '_custom_css', compact( 'custom_css' ) ) ?>
                    <?php Proxy\CustomJavaScript::renderEditor() ?>
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent text-right">
            <?php Buttons::renderSubmit( 'ajax-send-appearance' ) ?>
            <?php Buttons::renderReset() ?>
        </div>
    </div>
    <?php Components\Editable\Elements::renderModals( 'bookly-appearance' ) ?>
</div>