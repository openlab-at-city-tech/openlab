<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;

/**
 * @var Bookly\Backend\Components\Schedule\Component $business_hours
 */
?>
<form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'business_hours' ) ) ?>" id="business-hours">
    <div class="card-body">
        <p>
            <?php esc_html_e( 'Please note, the business hours below work as a template for all new staff members. To render a list of available time slots the system takes into account only staff members\' schedule, not the company business hours. Be sure to check the schedule of your staff members if you have some unexpected behavior of the booking system.', 'bookly' ) ?>
        </p>
        <p>
            <?php esc_html_e( 'Please note that business hours you set here will be used as visible hours in Calendar for all staff members if you enable "Show only business hours in the calendar" in Settings > Calendar.', 'bookly' ) ?>
        </p>
        <div class="form-row">
            <div class="col-sm-12">
                <?php $business_hours->render() ?>
            </div>
        </div>
    </div>

    <div class="card-footer bg-transparent d-flex justify-content-end">
        <?php Inputs::renderCsrf() ?>
        <?php Buttons::renderSubmit() ?>
        <?php Buttons::renderReset( 'bookly-hours-reset', 'ml-2' ) ?>
    </div>
</form>