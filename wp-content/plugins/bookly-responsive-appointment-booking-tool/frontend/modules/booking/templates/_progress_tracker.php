<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib;
$i = 1;
?>
<div class="bookly-progress-tracker bookly-table">
    <?php if ( ! $skip_steps['service'] ) : ?>
        <div <?php if ( $step >= 1 ) : ?>class="active"<?php endif ?>>
            <?php echo esc_html( $i ++ . '. ' . Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_step_service' ) ) ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <?php if ( ! $skip_steps['extras'] && get_option( 'bookly_service_extras_after_step_time' ) == '0' ) : ?>
        <div <?php if ( $step >= 2 ) : ?>class="active"<?php endif ?>>
            <?php echo esc_html( $i ++ . '. ' . Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_step_extras' ) ) ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <?php if ( ! $skip_steps['time'] ) : ?>
        <div <?php if ( $step >= 3 - (int) ( ! $skip_steps['extras'] && get_option( 'bookly_service_extras_after_step_time' ) == '1' ) ) : ?>class="active"<?php endif ?>>
            <?php echo esc_html( $i ++ . '. ' . Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_step_time' ) ) ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <?php if ( ! $skip_steps['extras'] && get_option( 'bookly_service_extras_after_step_time' ) == '1' ) : ?>
        <div <?php if ( $step_extras_active ) : ?>class="active"<?php endif ?>>
            <?php echo esc_html( $i ++ . '. ' . Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_step_extras' ) ) ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <?php if ( ! $skip_steps['repeat'] ) : ?>
        <div <?php if ( $step >= 4 ) : ?>class="active"<?php endif ?>>
            <?php echo esc_html( $i ++ . '. ' . Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_step_repeat' ) ) ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <?php if ( ! $skip_steps['cart'] ) : ?>
        <div <?php if ( $step >= 5 ) : ?>class="active"<?php endif ?>>
            <?php echo esc_html( $i ++ . '. ' . Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_step_cart' ) ) ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <div <?php if ( $step >= 6 ) : ?>class="active"<?php endif ?>>
        <?php echo esc_html( $i ++ . '. ' . Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_step_details' ) ) ?>
        <div class=step></div>
    </div>
    <?php if ( ! $skip_steps['payment'] ) : ?>
        <div <?php if ( $step >= 7 ) : ?>class="active"<?php endif ?>>
            <?php echo esc_html( $i ++ . '. ' . Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_step_payment' ) ) ?>
            <div class=step></div>
        </div>
    <?php endif ?>
    <div <?php if ( $step >= 8 ) : ?>class="active"<?php endif ?>>
        <?php echo esc_html( $i ++ . '. ' . Lib\Utils\Common::getTranslatedOption( 'bookly_l10n_step_done' ) ) ?>
        <div class=step></div>
    </div>
</div>