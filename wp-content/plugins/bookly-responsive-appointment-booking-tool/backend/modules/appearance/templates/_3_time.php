<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\DateTime;
use Bookly\Lib\Config;
use Bookly\Backend\Modules\Appearance\Codes;
use Bookly\Backend\Components\Editable\Elements;
use Bookly\Backend\Modules\Appearance\Proxy;

?>
    <div class="bookly-form">
        <?php include '_progress_tracker.php' ?>

        <div class="bookly-box">
            <?php Elements::renderText( 'bookly_l10n_info_time_step', Codes::getJson( 3 ) ) ?>
        </div>
        <?php Proxy\WaitingList::renderInfoText() ?>
        <div class="bookly-box bookly-label-error" style="padding-bottom:2px">
            <?php Elements::renderText( 'bookly_l10n_step_time_slot_not_available', null, 'bottom', __( 'Visible when the chosen time slot has been already booked', 'bookly' ) ) ?>
        </div>
        <?php Proxy\Pro::renderTimeZoneSwitcher() ?>

        <!-- timeslots -->
        <div class="bookly-time-step">
            <div class="bookly-columnizer-wrap">
                <div class="bookly-columnizer">
                    <div id="bookly-day-multi-columns" class="bookly-time-screen" style="display: <?php echo get_option( 'bookly_app_show_day_one_column' ) == 1 ? ' none' : 'block' ?>">
                        <div class="bookly-input-wrap bookly-slot-calendar bookly-js-slot-calendar">
                        <span class="bookly-date-wrap">
                            <?php include '_calendar.php' ?>
                        </span>
                        </div>
                        <div class="bookly-column col1">
                            <button class="bookly-day bookly-js-first-child"><?php echo date_i18n( 'D, M d', current_time( 'timestamp' ) ) ?></button>
                            <?php for ( $i = 28800; $i <= 57600; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i>
                                    <?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                        </div>
                        <div class="bookly-column col2">
                            <button class="bookly-hour ladda-button bookly-last-child">
                            <span class="ladda-label bookly-time-main">
                                <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( 61200 ) ?>
                            </span>
                                <span class="bookly-time-additional"></span>
                            </button>
                            <button class="bookly-day bookly-js-first-child" style="display: <?php echo get_option( 'bookly_app_show_calendar' ) == 1 ? ' none' : 'block' ?>"><?php echo date_i18n( 'D, M d', strtotime( '+1 day', current_time( 'timestamp' ) ) ) ?></button>
                            <?php for ( $i = 28800; $i <= 54000; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>" style="display: <?php echo get_option( 'bookly_app_show_calendar' ) == 1 ? ' none' : 'block' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                        </div>
                        <div class="bookly-column col3" style="display: <?php echo get_option( 'bookly_app_show_calendar' ) == 1 ? ' none' : 'inline-block' ?>">
                            <?php for ( $i = 57600; $i <= 61200; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                            <button class="bookly-day bookly-js-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+2 days', current_time( 'timestamp' ) ) ) ?></button>
                            <?php for ( $i = 28800; $i <= 50400; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                        </div>
                        <div class="bookly-column col4" style="display: <?php echo get_option( 'bookly_app_show_calendar' ) == 1 ? ' none' : 'inline-block' ?>">
                            <?php for ( $i = 54000; $i <= 61200; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                            <button class="bookly-day bookly-js-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+3 days', current_time( 'timestamp' ) ) ) ?></button>
                            <?php for ( $i = 28800; $i <= 46800; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                        </div>
                        <div class="bookly-column col5" style="display:<?php echo get_option( 'bookly_app_show_calendar' ) == 1 ? ' none' : ' inline-block' ?>">
                            <?php for ( $i = 50400; $i <= 61200; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                            <button class="bookly-day bookly-js-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+4 days', current_time( 'timestamp' ) ) ) ?></button>
                            <?php for ( $i = 28800; $i <= 43200; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                        </div>
                        <div class="bookly-column col6" style="display: <?php echo get_option( 'bookly_app_show_calendar' ) == 1 ? ' none' : 'inline-block' ?>">
                            <?php for ( $i = 46800; $i <= 61200; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                            <button class="bookly-day bookly-js-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+5 days', current_time( 'timestamp' ) ) ) ?></button>
                            <?php for ( $i = 28800; $i <= 39600; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                        </div>
                        <div class="bookly-column col7" style="display:<?php echo get_option( 'bookly_app_show_calendar' ) == 1 ? ' none' : ' inline-block' ?>">
                            <?php for ( $i = 43200; $i <= 61200; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                            <button class="bookly-day bookly-js-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+6 days', current_time( 'timestamp' ) ) ) ?></button>
                            <?php for ( $i = 28800; $i <= 36000; $i += 3600 ) : ?>
                                <?php $slot_type = mt_rand( 0, 2 ) ?>
                                <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                <span class="ladda-label bookly-time-main">
                                    <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $i ) ?>
                                </span>
                                    <span class="bookly-time-additional"></span>
                                </button>
                            <?php endfor ?>
                        </div>
                    </div>

                    <div id="bookly-day-one-column" class="bookly-time-screen" style="display: <?php echo get_option( 'bookly_app_show_day_one_column' ) == 1 ? ' block' : 'none' ?>">
                        <div class="bookly-input-wrap bookly-slot-calendar bookly-js-slot-calendar">
                        <span class="bookly-date-wrap">
                            <?php include '_calendar.php' ?>
                        </span>
                        </div>
                        <?php for ( $i = 1; $i <= 7; ++$i ) : ?>
                            <div class="bookly-column col<?php echo esc_attr( $i ) ?>">
                                <button class="bookly-day bookly-js-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+' . ( $i - 1 ) . ' days', current_time( 'timestamp' ) ) ) ?></button>
                                <?php for ( $j = 28800; $j <= 61200; $j += 3600 ) : ?>
                                    <?php $slot_type = mt_rand( 0, 2 ) ?>
                                    <button class="bookly-hour ladda-button<?php if ( $slot_type == 1 ) echo get_option( 'bookly_app_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked'; elseif ( $slot_type == 2 && Config::waitingListActive() ) echo ' no-waiting-list' ?>">
                                    <span class="ladda-label bookly-time-main">
                                        <i class="bookly-hour-icon"><span></span></i><?php echo DateTime::formatTime( $j ) ?>
                                    </span>
                                        <span class="bookly-time-additional"></span>
                                    </button>
                                <?php endfor ?>
                            </div>
                        <?php endfor ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="bookly-box bookly-nav-steps">
            <div class="bookly-back-step bookly-js-back-step bookly-btn">
                <?php Elements::renderString( array( 'bookly_l10n_button_back' ) ) ?>
            </div>
            <?php Proxy\Cart::renderButton() ?>
            <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
                <button class="bookly-time-next bookly-btn bookly-right ladda-button ml-2">
                    <?php Elements::renderString( array( 'bookly_l10n_button_time_next' ) ) ?>
                </button>
                <button class="bookly-time-prev bookly-btn bookly-right ladda-button ml-2">
                    <?php Elements::renderString( array( 'bookly_l10n_button_time_prev' ) ) ?>
                </button>
                <?php Proxy\Tasks::renderSkipButton() ?>
            </div>
        </div>
    </div>
<?php Proxy\CustomJavaScript::renderTimeSlotsJS() ?>