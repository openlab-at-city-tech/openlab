<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Backend\Components;
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Settings', 'bookly' ) ?></h4>
        <?php Components\Support\Buttons::render( '' ) ?>
    </div>

    <div class="form-row">
        <div id="bookly-sidebar" class="col-12 col-sm-auto">
            <div class="nav flex-column nav-pills" role="tablist">
                <?php Components\Settings\Menu::renderItem( __( 'General', 'bookly' ), 'general' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'URL Settings', 'bookly' ), 'url' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Calendar', 'bookly' ), 'calendar' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Company', 'bookly' ), 'company' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Customers', 'bookly' ), 'customers' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Appointments', 'bookly' ), 'appointments' ) ?>
                <?php Proxy\Mailchimp::renderMenuItem() ?>
                <?php Proxy\Pro::renderGoogleCalendarMenuItem() ?>
                <?php Proxy\Shared::renderMenuItem() ?>
                <?php Proxy\Pro::renderOnlineMeetingsMenuItem() ?>
                <?php Proxy\Pro::renderUserPermissionsMenuItem() ?>
                <?php Proxy\WaitingList::renderWaitingListMenuItem(); ?>
                <?php Components\Settings\Menu::renderItem( __( 'Payments', 'bookly' ), 'payments' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Business Hours', 'bookly' ), 'business_hours' ) ?>
                <?php Components\Settings\Menu::renderItem( __( 'Holidays', 'bookly' ), 'holidays' ) ?>
                <?php Proxy\Pro::renderPurchaseCodeMenuItem() ?>
                <?php Components\Settings\Menu::renderItem( __( 'Logs', 'bookly' ), 'logs' ) ?>
            </div>
        </div>

        <div id="bookly_settings_controls" class="col">
            <div class="card">
                <div class="tab-content">
                    <div class="tab-pane active" id="bookly_settings_general">
                        <?php self::renderTemplate( '_generalForm', $values ) ?>
                    </div>
                    <div class="tab-pane" id="bookly_settings_url">
                        <?php include '_urlForm.php' ?>
                    </div>
                    <div class="tab-pane" id="bookly_settings_calendar">
                        <?php include '_calendarForm.php' ?>
                    </div>
                    <div class="tab-pane" id="bookly_settings_company">
                        <?php include '_companyForm.php' ?>
                    </div>
                    <div class="tab-pane" id="bookly_settings_customers">
                        <?php include '_customers.php' ?>
                    </div>
                    <div class='tab-pane' id='bookly_settings_appointments'>
                        <?php self::renderTemplate( '_appointmentsForm', array( 'statuses' => $values['statuses'] ) ) ?>
                    </div>
                    <?php Proxy\Mailchimp::renderTab() ?>
                    <?php Proxy\Pro::renderGoogleCalendarTab() ?>
                    <?php Proxy\Shared::renderTab() ?>
                    <?php Proxy\CustomStatuses::renderTab() ?>
                    <div class="tab-pane" id="bookly_settings_payments">
                        <?php include '_paymentsForm.php' ?>
                    </div>
                    <?php Proxy\Pro::renderOnlineMeetingsTab() ?>
                    <?php Proxy\Pro::renderUserPermissionsTab() ?>
                    <?php Proxy\WaitingList::renderWaitingListTab(); ?>
                    <div class="tab-pane" id="bookly_settings_business_hours">
                        <?php include '_hoursForm.php' ?>
                    </div>
                    <div class="tab-pane" id="bookly_settings_holidays">
                        <?php include '_holidaysForm.php' ?>
                    </div>
                    <?php Proxy\Pro::renderPurchaseCodeTab() ?>
                    <div class="tab-pane" id="bookly_settings_logs">
                        <?php include '_logsForm.php' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>