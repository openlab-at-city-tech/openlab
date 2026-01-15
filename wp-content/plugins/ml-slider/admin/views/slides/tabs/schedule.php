<?php 
if (!defined('ABSPATH')) {
    die('No direct access.');
} 
$current_time = current_time( 'timestamp' );
?>
<div class="row schedule">
    <div class="ms-switch-button">
        <label>
            <input type="checkbox" class="schedule-slide mr-0" disabled> <span class="opacity-50"></span>
        </label>
    </div>
    <label class="schedule-slide">
        <?php 
        esc_html_e('Schedule this slide', 'ml-slider');
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo metaslider_upgrade_pro_small_btn(
            __( 'Schedule is available in MetaSlider Pro', 'ml-slider' )
        );
        ?> 
    </label>
    <span class="text-gray ml-4 float-right">
        <?php
        // Get the timezone setting from global settings
        $timezone_string = get_option('timezone_string');

        // Display timezone saved in settings; based on wp_timezone_string() function
        $timezone_string    = get_option( 'timezone_string' );
        $offset             = (float) get_option( 'gmt_offset' );
        if ( $timezone_string ) {
            $parts  = explode( '/', $timezone_string );
            $city   = end( $parts );
            $city   = str_replace( '_', ' ', $city );

            echo sprintf( esc_html__( '%s time', 'ml-slider' ), esc_html( $city ) );
        } elseif( $offset ) {
            $hours   = (int) $offset;
            $minutes = ( $offset - $hours );

            $sign      = ( $offset < 0 ) ? '-' : '+';
            $abs_hour  = abs( $hours );
            $abs_mins  = abs( $minutes * 60 );
            $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

            echo sprintf( esc_html__( 'UTC%s timezone', 'ml-slider' ), esc_html( $tz_offset ) );
        } else {
            echo sprintf( 
                esc_html__( '%sUses your settings timezone%s', 'ml-slider' ), 
                '<a href="' . esc_url( admin_url( 'options-general.php#timezone_string' ) ) . '" target="_blank" class="button-link">',
                '</a>'
            );
        }
        ?>
        <span class="tipsy-tooltip-top ms-time-helper text-gray float-right ml-1"
            data-time="<?php 
            esc_attr_e(gmdate('Y-m-d H:i:s', $current_time)); ?>" data-now-text="<?php
            esc_attr_e('Current server time', 'ml-slider') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="feather feather-clock">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </span>
    </span>
</div>