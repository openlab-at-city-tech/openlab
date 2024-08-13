<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;

$color = get_option( 'bookly_app_color', '#f4662f' );
$custom_css = trim( get_option( 'bookly_app_custom_styles' ) );
?>
    <style type="text/css">
        :root {
            --bookly-main-color: <?php echo esc_attr( $color ) ?> !important;
        }

        .bookly-form .fill-bookly {
            fill: <?php echo esc_attr( $color ) ?>;
        }

        /* Color */
        .bookly-form .bookly-form-group > label,
        .bookly-form .bookly-label-error,
        .bookly-form .bookly-progress-tracker > .active,
        .bookly-form .bookly-columnizer .bookly-hour span.bookly-waiting-list,
        .bookly-form .hover\:text-bookly:hover,
        .bookly-form .text-bookly:not(:hover),
        .bookly-form .hover\:text-bookly:hover {
            color: <?php echo esc_attr( $color ) ?> !important;
        }

        /* Background */
        .bookly-form .bookly-progress-tracker > .active .step,
        .bookly-form .bookly-columnizer .bookly-hour:active .bookly-hour-icon span,
        .bookly-form .bookly-btn,
        .bookly-form .bookly-btn:active,
        .bookly-form .bookly-btn:focus,
        .bookly-form .bookly-btn:hover,
        .bookly-form .bookly-btn-submit,
        .bookly-form .bookly-round,
        .bookly-form .bookly-square,
        .bookly-form .bookly-pagination > li.active,
        .bookly-form .bg-bookly,
        .bookly-form .hover\:bg-bookly:hover,
        .bookly-form .bg-bookly-not-hover:not(:hover) {
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