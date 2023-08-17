<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Config;
use Bookly\Backend\Components\Editable\Elements;

$i = 1;
?>
<div class="bookly-progress-tracker bookly-table">
    <div class="active">
        <span class="bookly-js-step-number"><?php echo esc_html( $i ++ ) ?></span>. <?php Elements::renderString( array( 'bookly_l10n_step_service' ) ) ?>
        <div class="step"></div>
    </div>
    <?php if ( Config::serviceExtrasActive() && get_option( 'bookly_service_extras_after_step_time' ) == '0' ) : ?>
        <div <?php if ( $step >= 2 ) : ?>class="active"<?php endif ?> data-step="bookly-step-2" <?php if ( ! get_option( 'bookly_service_extras_enabled' ) ) : ?>style="display: none;"<?php endif ?>>
            <span class="bookly-js-step-number"><?php echo get_option( 'bookly_service_extras_enabled' ) ? $i ++ : $i ?></span>. <?php Elements::renderString( array( 'bookly_l10n_step_extras' ) ) ?>
            <div class="step"></div>
        </div>
    <?php endif ?>
    <div <?php if ( $step >= 3 - (int) ( Config::serviceExtrasActive() && get_option( 'bookly_service_extras_after_step_time' ) == '1' ) ) : ?>class="active"<?php endif ?>>
        <span class="bookly-js-step-number"><?php echo esc_html( $i ++ ) ?></span>. <?php Elements::renderString( array( 'bookly_l10n_step_time' ) ) ?>
        <div class="step"></div>
    </div>
    <?php if ( Config::serviceExtrasActive() && get_option( 'bookly_service_extras_after_step_time' ) == '1' ) : ?>
        <div <?php if ( $step > 3 || $step == 2 ) : ?>class="active"<?php endif ?> data-step="bookly-step-2" <?php if ( ! get_option( 'bookly_service_extras_enabled' ) ) : ?>style="display: none;"<?php endif ?>>
            <span class="bookly-js-step-number"><?php echo get_option( 'bookly_service_extras_enabled' ) ? $i ++ : $i ?></span>. <?php Elements::renderString( array( 'bookly_l10n_step_extras' ) ) ?>
            <div class="step"></div>
        </div>
    <?php endif ?>
    <?php if ( Config::recurringAppointmentsActive() ) : ?>
        <div <?php if ( $step >= 4 ) : ?>class="active"<?php endif ?> data-step="bookly-step-4" <?php if ( ! get_option( 'bookly_recurring_appointments_enabled' ) ) : ?>style="display: none;"<?php endif ?>>
            <span class="bookly-js-step-number"><?php echo get_option( 'bookly_recurring_appointments_enabled' ) ? $i ++ : $i ?></span>. <?php Elements::renderString( array( 'bookly_l10n_step_repeat' ) ) ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <?php if ( Config::cartActive() ) : ?>
        <div <?php if ( $step >= 5 ) : ?>class="active"<?php endif ?> data-step="bookly-step-5" <?php if ( ! get_option( 'bookly_cart_enabled' ) ) : ?>style="display: none;"<?php endif ?>>
            <span class="bookly-js-step-number"><?php echo get_option( 'bookly_cart_enabled' ) ? $i ++ : $i ?></span>. <?php Elements::renderString( array( 'bookly_l10n_step_cart' ) ) ?>
            <div class="step"></div>
        </div>
    <?php endif ?>
    <div <?php if ( $step >= 6 ) : ?>class="active"<?php endif ?>>
        <span class="bookly-js-step-number"><?php echo esc_html( $i ++ ) ?></span>. <?php Elements::renderString( array( 'bookly_l10n_step_details' ) ) ?>
        <div class="step"></div>
    </div>
    <div <?php if ( $step >= 7 ) : ?>class="active"<?php endif ?>>
        <span class="bookly-js-step-number"><?php echo esc_html( $i ++ ) ?></span>. <?php Elements::renderString( array( 'bookly_l10n_step_payment' ) ) ?>
        <div class="step"></div>
    </div>
    <div <?php if ( $step >= 8 ) : ?>class="active"<?php endif ?>>
        <span class="bookly-js-step-number"><?php echo esc_html( $i ++ ) ?></span>. <?php Elements::renderString( array( 'bookly_l10n_step_done' ) ) ?>
        <div class="step"></div>
    </div>
</div>
