<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Modules\Settings\Page as SettingsPage;
use Bookly\Lib;
?>
<div id="bookly-tbs" class="wrap">
    <div id="bookly-zoom-jwt-notice" class="alert alert-warning" data-action="bookly_dismiss_zoom_jwt_notice">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <div class="form-row">
            <div class="mr-3"><i class="fas fa-info-circle fa-2x"></i></div>
            <div class="col">
                <b><?php esc_html_e( 'Migrate your Zoom JWT app to the new app type by June 1, 2023.', 'bookly' ) ?></b><br/>
                <?php esc_html_e( 'Zoom JWT app type will be deprecated June 1, 2023. After this date, you will no longer be able to use your JWT apps.', 'bookly' ) ?><br/>
                <?php esc_html_e( 'We recommend that you create an OAuth app to replace the functionality of a JWT app in your account.', 'bookly' ) ?><br/>
                <?php echo Lib\Utils\Common::html( __( 'To learn more, please visit the <a href="https://marketplace.zoom.us/docs/guides/build/jwt-app/jwt-faq/" target="_blank">official Zoom page</a> about this change', 'bookly' ) ) ?>
                <div class="mt-2">
                    <a href="<?php echo add_query_arg( array( 'page' => SettingsPage::pageSlug(), 'tab' => 'online_meetings' ), admin_url( 'admin.php' ) ) ?>" class="btn btn-success ladda-button" data-spinner-size="40" data-style="zoom-in" data-action="settings"><?php esc_html_e( 'Go to Settings', 'bookly' ) ?></a>
                </div>
            </div>
        </div>
    </div>
</div>