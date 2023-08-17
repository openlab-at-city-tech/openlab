<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Modules\Appearance\Codes;
use Bookly\Backend\Modules\Appearance\Proxy;
use Bookly\Backend\Components\Editable\Elements;
?>
<div class="bookly-form">
    <?php include '_progress_tracker.php' ?>
    <div class="bookly-box bookly-js-done-success">
        <?php Elements::renderText( 'bookly_l10n_info_complete_step', Codes::getJson( 8, true ) ) ?>
        <?php Proxy\Pro::renderQRCode() ?>
    </div>
    <div class="bookly-box bookly-js-done-limit-error bookly-collapse">
        <?php Elements::renderText( 'bookly_l10n_info_complete_step_limit_error', Codes::getJson( 8 ) ) ?>
    </div>
    <div class="bookly-box bookly-js-done-processing bookly-collapse">
        <?php Elements::renderText( 'bookly_l10n_info_complete_step_processing', Codes::getJson( 8, true ) ) ?>
        <?php Proxy\Pro::renderQRCode() ?>
    </div>
    <?php Proxy\CustomerGroups::renderStepCompleteInfo() ?>
    <div class="bookly-box bookly-nav-steps">
        <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'mr-2 bookly-left' : 'ml-2 bookly-right' ?>">
            <div class="bookly-next-step bookly-js-next-step bookly-btn">
                <?php Elements::renderString( array( 'bookly_l10n_step_done_button_start_over' ) ) ?>
            </div>
        </div>
        <div class="<?php echo get_option( 'bookly_app_align_buttons_left' ) ? 'mr-2 bookly-left' : 'ml-2 bookly-right' ?>">
            <div class="bookly-next-step bookly-js-next-step bookly-btn">
                <?php Elements::renderString( array( 'bookly_l10n_button_download_ics' ) ) ?>
            </div>
        </div>
        <?php Proxy\Invoices::renderDownloadInvoice() ?>
    </div>
</div>