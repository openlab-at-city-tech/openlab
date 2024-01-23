<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
$i = 1;
$steps_counter = $stepper_add_step ? 2 : 1;
$step_active = 1;
?>
<div class="bookly-progress-tracker bookly-table">
    <?php if ( ! $skip_steps['service'] ) : ?>
        <div <?php if ( $step >= 1 ) : ?>class="active"<?php endif ?>>
            <?php echo esc_html( $i ++ . '. ' . Common::getTranslatedOption( 'bookly_l10n_step_service' ) ); $steps_counter++ ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <?php if ( ! $skip_steps['extras'] && get_option( 'bookly_service_extras_after_step_time' ) == '0' ) : ?>
        <div <?php if ( $step >= 2 ) : ?>class="active"<?php endif ?>>
            <?php if ( $step >= 2 ) $step_active = $stepper_add_step ? ( $i + 1 ) : $i ?>
            <?php echo esc_html( $i ++ . '. ' . Common::getTranslatedOption( 'bookly_l10n_step_extras' ) ); $steps_counter++ ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <?php if ( ! $skip_steps['time'] ) : ?>
        <div <?php if ( $step >= 3 - (int) ( ! $skip_steps['extras'] && get_option( 'bookly_service_extras_after_step_time' ) == '1' ) ) : ?>class="active"<?php endif ?>>
            <?php if ( $step >= 3 - (int) ( ! $skip_steps['extras'] && get_option( 'bookly_service_extras_after_step_time' ) == '1' ) ) $step_active = $stepper_add_step ? ( $i + 1 ) : $i ?>
            <?php echo esc_html( $i ++ . '. ' . Common::getTranslatedOption( 'bookly_l10n_step_time' ) ); $steps_counter++ ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <?php if ( ! $skip_steps['extras'] && get_option( 'bookly_service_extras_after_step_time' ) == '1' ) : ?>
        <div <?php if ( $step_extras_active ) : ?>class="active"<?php endif ?>>
            <?php if ( $step_extras_active ) $step_active = $stepper_add_step ? ( $i + 1 ) : $i ?>
            <?php echo esc_html( $i ++ . '. ' . Common::getTranslatedOption( 'bookly_l10n_step_extras' ) ); $steps_counter++ ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <?php if ( ! $skip_steps['repeat'] ) : ?>
        <div <?php if ( $step >= 4 ) : ?>class="active"<?php endif ?>>
            <?php if ( $step >= 4 ) $step_active = $stepper_add_step ? ( $i + 1 ) : $i ?>
            <?php echo esc_html( $i ++ . '. ' . Common::getTranslatedOption( 'bookly_l10n_step_repeat' ) ); $steps_counter++ ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <?php if ( ! $skip_steps['cart'] ) : ?>
        <div <?php if ( $step >= 5 ) : ?>class="active"<?php endif ?>>
            <?php if ( $step >= 5 ) $step_active = $stepper_add_step ? ( $i + 1 ) : $i ?>
            <?php echo esc_html( $i ++ . '. ' . Common::getTranslatedOption( 'bookly_l10n_step_cart' ) ); $steps_counter++ ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <div <?php if ( $step >= 6 ) : ?>class="active"<?php endif ?>>
        <?php if ( $step >= 6 ) $step_active = $stepper_add_step ? ( $i + 1 ) : $i ?>
        <?php echo esc_html( $i ++ . '. ' . Common::getTranslatedOption( 'bookly_l10n_step_details' ) ); $steps_counter++ ?>
        <div class=step></div>
    </div>
    <?php if ( ! $skip_steps['payment'] ) : ?>
        <div <?php if ( $step >= 7 ) : ?>class="active"<?php endif ?>>
            <?php if ( $step >= 7 ) $step_active = $stepper_add_step ? ( $i + 1 ) : $i ?>
            <?php echo esc_html( $i ++ . '. ' . Common::getTranslatedOption( 'bookly_l10n_step_payment' ) ); $steps_counter++ ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <div <?php if ( $step >= 8 ) : ?>class="active"<?php endif ?>>
        <?php if ( $step >= 8 ) $step_active = $stepper_add_step ? ( $i + 1 ) : $i ?>
        <?php echo esc_html( $i ++ . '. ' . Common::getTranslatedOption( 'bookly_l10n_step_done' ) ); $steps_counter++ ?>
        <div class=step></div>
    </div>
</div>
<ol class="bookly-stepper">
    <?php for ( $i = 1; $i < $steps_counter; $i++ ): ?>
        <li<?php if ( $i == $step_active ) : ?> class="bookly-step-active"<?php endif ?>></li>
    <?php endfor ?>
</ol>