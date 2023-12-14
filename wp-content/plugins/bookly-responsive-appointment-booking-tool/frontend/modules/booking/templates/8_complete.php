<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
use Bookly\Frontend\Modules\Booking\Proxy;
/**
 * @var string $state
 * @var string $progress_tracker
 * @var string $info_text
 * @var string $add_calendar_info
 **/

$success = ( $state === 'completed' || $state === 'processing' );
$download_invoice = $success
    ? Proxy\Invoices::getDownloadButton()
    : null;

echo Common::stripScripts( $progress_tracker );
?>
<div class="bookly-box"><?php echo Common::html( $info_text ) ?></div>
<?php if ( $success && get_option( 'bookly_app_show_add_to_calendar' ) ) : ?>
    <div class="bookly-box bookly-text-center">
        <div>
            <?php echo Common::html( $add_calendar_info ) ?>
        </div>
        <br>
        <button class="bookly-btn bookly-inline-block bookly-js-add-to-calendar ladda-button" data-style="zoom-in" data-spinner-size="40" data-calendar="google">
            <span class="ladda-label"><i class="bookly-fa-svg bookly-google"></i> Google</span>
        </button>
        <button class="bookly-btn bookly-inline-block bookly-js-add-to-calendar ladda-button" data-style="zoom-in" data-spinner-size="40" data-calendar="ics">
            <i class="bookly-fa-svg bookly-apple"></i> Apple
        </button>
        <button class="bookly-btn bookly-inline-block bookly-js-add-to-calendar ladda-button" data-style="zoom-in" data-spinner-size="40" data-calendar="outlook">
            <i class="bookly-fa-svg bookly-microsoft"></i> Outlook
        </button>
        <button class="bookly-btn bookly-inline-block bookly-js-add-to-calendar ladda-button" data-style="zoom-in" data-spinner-size="40" data-calendar="yahoo">
            <i class="bookly-fa-svg bookly-yahoo"></i> Yahoo
        </button>
    </div>
<?php endif ?>
<?php if ( get_option( 'bookly_app_show_start_over' ) || get_option( 'bookly_app_show_download_ics' ) || $download_invoice ) : ?>
    <div class="bookly-box bookly-nav-steps">
        <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'bookly-left' : 'bookly-right' ?>">
            <?php echo Common::stripScripts( $download_invoice ?: '' ) ?>
            <?php if ( get_option( 'bookly_app_show_download_ics' ) ): ?>
                <button class="bookly-nav-btn bookly-js-download-ics bookly-btn ladda-button bookly-left" data-style="zoom-in" data-spinner-size="40">
                    <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_button_download_ics' ) ?></span>
                </button>
            <?php endif ?>
            <?php if ( get_option( 'bookly_app_show_start_over' ) ): ?>
                <button class="bookly-nav-btn bookly-js-start-over bookly-btn ladda-button bookly-left" data-style="zoom-in" data-spinner-size="40">
                    <span class="ladda-label"><?php echo Common::getTranslatedOption( 'bookly_l10n_step_done_button_start_over' ) ?></span>
                </button>
            <?php endif ?>
        </div>
    </div>
<?php endif ?>