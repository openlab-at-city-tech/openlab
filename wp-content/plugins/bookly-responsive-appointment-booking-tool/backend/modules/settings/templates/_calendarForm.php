<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Settings;
use Bookly\Backend\Components\Ace;
use Bookly\Backend\Modules\Settings\Codes;
use Bookly\Lib\Entities\CustomerAppointment;

?>
<form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'calendar' ) ) ?>">
    <div class="card-body">
        <?php Settings\Selects::renderSingle( 'bookly_cal_show_only_business_days', __( 'Show only business days in the calendar', 'bookly' ), __( 'If this setting is enabled then only business days will be visible in the calendar according to the company\'s business hours settings', 'bookly' ) ) ?>
        <?php Settings\Selects::renderSingle( 'bookly_cal_show_only_business_hours', __( 'Show only business hours in the calendar', 'bookly' ), __( 'If this setting is enabled then the visible hours in the calendar will be limited to the company\'s business hours', 'bookly' ) ) ?>
        <?php Settings\Selects::renderSingle( 'bookly_cal_show_only_staff_with_appointments', __( 'Show only staff members with appointments in Day view', 'bookly' ), __( 'If this setting is enabled then only staff members who have associated appointments will be displayed in the Day view', 'bookly' ) ) ?>
        <?php Settings\Selects::renderSingle( 'bookly_cal_show_new_appointments_badge', __( 'Show new appointments notifications', 'bookly' ), __( 'If enabled, you will see an indicator near \'Calendar\' for newly created appointments', 'bookly' ) ) ?>
        <?php Settings\Selects::renderSingle( 'bookly_cal_scrollable_calendar', __( 'Scrollable calendar', 'bookly' ), __( 'If enabled, the backend calendar will occupy part of the screen and remain scrollable. If disabled, it will take up more space and scroll along with the entire page.', 'bookly' ), array(), array( 'data-expand' => '1' ) ) ?>
        <div class="border-left mt-3 ml-4 pl-3 bookly_cal_scrollable_calendar-expander"<?php if ( get_option( 'bookly_cal_scrollable_calendar', '1' ) !== '0' ) : ?> style="display:none;"<?php endif ?>>
            <?php Settings\Selects::renderSingle( 'bookly_cal_month_view_style', __( 'Month view style', 'bookly' ), __( 'Select the style for displaying appointments in Month view', 'bookly' ), array( array( 'classic', __( 'Classic', 'bookly' ) ), array( 'minimalistic', __( 'Minimalistic', 'bookly' ) ) ) ) ?>
        </div>
        <?php Settings\Selects::renderSingle( 'bookly_cal_coloring_mode', __( 'Coloring mode', 'bookly' ), __( 'If you select "By service", then the color will be taken from the service settings. If you select "By status", then the color will depend on the appointment status', 'bookly' ), array( array( 'service', __( 'By service', 'bookly' ) ), array( 'status', __( 'By status', 'bookly' ) ), array( 'staff', __( 'By staff', 'bookly' ) ) ) ) ?>
        <div class="border-left ml-4 pl-3 mb-3 bookly-js-colors-by bookly-js-colors-status form-row">
            <?php foreach ( $values['colors_status'] as $status => $color ): ?>
                <div class='col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-3'>
                    <div class='bookly-color-picker bookly-color-picker-sm'>
                        <input name='status[<?php echo esc_attr( $status ) ?>]' value="<?php echo esc_attr( $color ) ?>" class='bookly-js-color-picker' data-title="<?php echo esc_attr( CustomerAppointment::statusToString( $status ) ) ?>" type='text'/>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <div class="form-group">
            <?php if ( Bookly\Lib\Config::groupBookingActive() ) : ?>
                <label for="bookly_appointment_participants"><?php esc_html_e( 'Calendar', 'bookly' ) ?></label>
                <select class="form-control custom-select mb-3" id="bookly_appointment_participants">
                    <option value="bookly_cal_one_participant"><?php esc_html_e( 'Appointment with one participant', 'bookly' ) ?></option>
                    <option value="bookly_cal_many_participants"><?php esc_html_e( 'Appointment with many participants', 'bookly' ) ?></option>
                </select>
            <?php else : ?>
                <label for="bookly_appointment_participants"><?php esc_html_e( 'Calendar', 'bookly' ) ?></label>
                <input id="bookly_appointment_participants" type="hidden" name="bookly_appointment_participants" value="bookly_cal_one_participant"/>
            <?php endif ?>
            <div id="bookly_cal_one_participant">
                <?php Ace\Editor::render( 'bookly-calendar', 'bookly_cal_editor_one_participant', Codes::getJson( 'calendar_one_participant' ), get_option( 'bookly_cal_one_participant', '' ) ) ?>
                <input type="hidden" name="bookly_cal_one_participant" value="<?php echo esc_attr( get_option( 'bookly_cal_one_participant', '' ) ) ?>">
            </div>
            <div id="bookly_cal_many_participants">
                <?php Ace\Editor::render( 'bookly-calendar', 'bookly_cal_editor_many_participants', Codes::getJson( 'calendar_many_participants' ), get_option( 'bookly_cal_many_participants', '' ) ) ?>
                <input type="hidden" name="bookly_cal_many_participants" value="<?php echo esc_attr( get_option( 'bookly_cal_many_participants', '' ) ) ?>">
            </div>
        </div>
    </div>

    <div class="card-footer bg-transparent d-flex justify-content-end">
        <?php Inputs::renderCsrf() ?>
        <?php Buttons::renderSubmit() ?>
        <?php Buttons::renderReset( 'bookly-calendar-reset', 'ml-2' ) ?>
    </div>
</form>