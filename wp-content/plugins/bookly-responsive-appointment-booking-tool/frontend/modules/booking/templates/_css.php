<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;

$color = get_option( 'bookly_app_color', '#f4662f' );
$checkbox_img = plugins_url( 'frontend/resources/images/checkbox.png', \Bookly\Lib\Plugin::getMainFile() );
$custom_css = trim( get_option( 'bookly_app_custom_styles' ) );
?>
    <style type="text/css">
        /* Color */
        .bookly-form .bookly-form-group > label,
        .bookly-form .bookly-label-error,
        .bookly-form .bookly-progress-tracker > .active,
        .bookly-form .picker__nav--next,
        .bookly-form .pickadate__nav--prev,
        .bookly-form .picker__day:hover,
        .bookly-form .picker__day--selected:hover,
        .bookly-form .picker--opened .picker__day--selected,
        .bookly-form .picker__button--clear,
        .bookly-form .picker__button--today,
        .bookly-form .bookly-columnizer .bookly-hour span.bookly-waiting-list {
            color: <?php echo esc_attr( $color ) ?> !important;
        }

        /* Background */
        .bookly-form .bookly-progress-tracker > .active .step,
        .bookly-form .picker__frame,
        .bookly-form .bookly-service-step .bookly-week-days label,
        .bookly-form .bookly-repeat-step .bookly-week-days label,
        .bookly-form .bookly-columnizer .bookly-hour:active .bookly-hour-icon span,
        .bookly-form .bookly-btn,
        .bookly-form .bookly-btn:active,
        .bookly-form .bookly-btn:focus,
        .bookly-form .bookly-btn:hover,
        .bookly-form .bookly-btn-submit,
        .bookly-form .bookly-round,
        .bookly-form .bookly-square,
        .bookly-form .bookly-pagination > li.active {
            background-color: <?php echo esc_attr( $color ) ?> !important;
        }

        .bookly-form .bookly-triangle {
            border-bottom-color: <?php echo esc_attr( $color ) ?> !important;
        }

        /* Border */
        .bookly-form input[type="text"].bookly-error,
        .bookly-form input[type="password"].bookly-error,
        .bookly-form select.bookly-error,
        .bookly-form textarea.bookly-error,
        .bookly-form .bookly-week-days.bookly-error,
        .bookly-extra-step div.bookly-extras-thumb.bookly-extras-selected {
            border: 2px solid <?php echo esc_attr( $color ) ?> !important;
        }

        /* Other */
        .bookly-form .picker__header {
            border-bottom: 1px solid <?php echo esc_attr( $color ) ?> !important;
        }

        .bookly-form .picker__nav--next:before {
            border-left: 6px solid <?php echo esc_attr( $color ) ?> !important;
        }

        .bookly-form .picker__nav--prev:before {
            border-right: 6px solid <?php echo esc_attr( $color ) ?> !important;
        }

        .bookly-form .bookly-service-step .bookly-week-days label.active, .bookly-form .bookly-repeat-step .bookly-week-days label.active {
            background: <?php echo esc_attr( $color ) ?> url(<?php echo esc_attr( $checkbox_img ) ?>) 0 0 no-repeat !important;
        }

        .bookly-form .bookly-columnizer .bookly-day, .bookly-form .bookly-schedule-date {
            background: <?php echo esc_attr( $color ) ?> !important;
            border: 1px solid <?php echo esc_attr( $color ) ?> !important;
        }

        .bookly-form .bookly-pagination > li.active a {
            border: 1px solid <?php echo esc_attr( $color ) ?> !important;
        }

        .bookly-form .bookly-columnizer .bookly-hour:active {
            border: 2px solid <?php echo esc_attr( $color ) ?> !important;
            color: <?php echo esc_attr( $color ) ?> !important;
        }

        .bookly-form .bookly-columnizer .bookly-hour:active .bookly-hour-icon {
            background: none;
            border: 2px solid <?php echo esc_attr( $color ) ?> !important;
            color: <?php echo esc_attr( $color ) ?> !important;
            width: auto;
            height: auto;
            padding: 3px;
            border-radius: 25px;
            margin-right: 3px;
        }

        .bookly-form .bookly-columnizer .bookly-hour:active .bookly-hour-icon span {
            background-color: <?php echo esc_attr( $color ) ?> !important;
            width: 8px;
            height: 8px;
            border-radius: 10px;
            display: block;
        }

        @media (hover) {
            .bookly-form .bookly-columnizer .bookly-hour:hover {
                border: 2px solid <?php echo esc_attr( $color ) ?> !important;
                color: <?php echo esc_attr( $color ) ?> !important;
            }

            .bookly-form .bookly-columnizer .bookly-hour:hover .bookly-hour-icon {
                background: none;
                border: 2px solid <?php echo esc_attr( $color ) ?> !important;
                color: <?php echo esc_attr( $color ) ?> !important;
            }

            .bookly-form .bookly-columnizer .bookly-hour:hover .bookly-hour-icon span {
                background-color: <?php echo esc_attr( $color ) ?> !important;
            }
        }
    </style>

<?php if ( $custom_css != '' ) : ?>
    <style type="text/css">
        <?php echo Common::css( $custom_css ) ?>
    </style>
<?php endif ?>