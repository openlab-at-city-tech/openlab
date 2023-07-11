<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Inputs as ControlsInputs;
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Settings\Inputs;
use Bookly\Backend\Components\Settings\Selects;
use Bookly\Backend\Modules\Settings\Proxy;
?>
<form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'customers' ) ) ?>">
    <div class="card-body">
        <?php
            Proxy\Pro::renderCreateWordPressUser();
            Proxy\Pro::renderNewClientAccountRole();
            Selects::renderSingle( 'bookly_cst_phone_default_country', __( 'Phone field default country', 'bookly' ), __( 'Select default country for the phone field in the \'Details\' step of booking. You can also let Bookly determine the country based on the IP address of the client.', 'bookly' ), array( array( 'disabled', __( 'Disabled', 'bookly' ) ), array( 'auto', __( 'Guess country by user\'s IP address', 'bookly' ) ) ) );
            Inputs::renderText( 'bookly_cst_default_country_code', __( 'Default country code', 'bookly' ), __( 'Your clients must have their phone numbers in international format in order to receive text messages. However you can specify a default country code that will be used as a prefix for all phone numbers that do not start with "+" or "00". E.g. if you enter "1" as the default country code and a client enters their phone as "(600) 555-2222" the resulting phone number to send the SMS to will be "+1600555222".', 'bookly' ) );

            Proxy\Pro::renderCustomersBirthday();
            Proxy\Pro::renderCustomersAddress();
            Proxy\Pro::renderCustomersAddressTemplate();

            Proxy\Pro::renderCustomersLimitStatuses();

            Selects::renderSingle( 'bookly_cst_remember_in_cookie', __( 'Remember personal information in cookies', 'bookly' ), __( 'If this setting is enabled then returning customers will have their personal information fields filled in at the Details step with the data previously saved in cookies.', 'bookly' ) );
            Selects::renderSingle( 'bookly_cst_allow_duplicates', __( 'Allow duplicate customers', 'bookly' ), __( 'If enabled, a new user will be created if any of the registration data during the booking is different.', 'bookly' ) );
            Selects::renderSingle( 'bookly_cst_show_update_details_dialog', __( 'Show confirmation dialog before updating customer\'s data', 'bookly' ), __( 'If this option is enabled and customer enters contact info different from the previous order, a warning message will appear asking to update the data.', 'bookly' ) );
            Selects::renderSingle( 'bookly_cst_verify_customer_details', __( 'Verify customer\'s contact information at Details step', 'bookly' ), __( 'Select when to send a notification with a verification code to the customer by SMS or email.', 'bookly' ), array( array( 0, __( 'Off', 'bookly' ) ), array( 'always_phone', __( 'Always verify phone', 'bookly' ) ), array( 'always_email', __( 'Always verify email', 'bookly' ) ), array( 'on_update', __( 'Only if data is different from the previous order', 'bookly' ) ) ) );
        ?>
    </div>

    <div class="card-footer bg-transparent d-flex justify-content-end">
        <?php ControlsInputs::renderCsrf() ?>
        <?php Buttons::renderSubmit() ?>
        <?php Buttons::renderReset( 'bookly-customer-reset', 'ml-2' ) ?>
    </div>
</form>