<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs as ControlsInputs;
use Bookly\Backend\Components\Settings\Selects;
use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Config;
use Bookly\Backend\Components\Ace;
use Bookly\Backend\Modules\Settings\Codes;

$help = __( 'Select status for newly booked appointments.', 'bookly' );
if ( Config::customerGroupsActive() ) {
    $help .= ' ' . sprintf( __( 'Please note that this setting will be overridden by Customer Groups > General settings > <a href="%s" target="_blank">Default appointment status</a>.', 'bookly' ), Common::escAdminUrl( 'bookly-customer-groups' ) );
}
?>
<form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'appointments' ) ) ?>">
    <div class="card-body">
        <?php Selects::renderSingle( 'bookly_appointment_default_status', __( 'Default appointment status', 'bookly' ), $help, $statuses ) ?>
        <?php Proxy\Pro::renderAppointmentsSettings() ?>
        <div class="form-group">
            <label for="bookly-ics-customer-editor"><?php esc_html_e( 'ICS description for customers', 'bookly' ) ?></label>
            <?php Ace\Editor::render( 'bookly-placeholders', 'bookly-ics-customer-editor', Codes::getJson( 'ics_for_customer' ), get_option( 'bookly_l10n_ics_customer_template', '' ), 'bookly-ace-editor-h80' ) ?>
            <input type="hidden" name="bookly_l10n_ics_customer_template" value="<?php echo esc_attr( get_option( 'bookly_l10n_ics_customer_template', '' ) ) ?>"/>
        </div>
        <div class="form-group">
            <label for="bookly-ics-staff-editor"><?php esc_html_e( 'ICS description for staff', 'bookly' ) ?></label>
            <?php Ace\Editor::render( 'bookly-placeholders', 'bookly-ics-staff-editor', Codes::getJson( 'ics_for_staff' ), get_option( 'bookly_ics_staff_template', '' ), 'bookly-ace-editor-h80' ) ?>
            <input type="hidden" name="bookly_ics_staff_template" value="<?php echo esc_attr( get_option( 'bookly_ics_staff_template', '' ) ) ?>"/>
        </div>
    </div>

    <div class="card-footer bg-transparent d-flex justify-content-end">
        <?php ControlsInputs::renderCsrf() ?>
        <?php Buttons::renderSubmit() ?>
        <?php Buttons::renderReset( null, 'ml-2' ) ?>
    </div>
</form>