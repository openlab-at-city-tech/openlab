<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Config;
use Bookly\Lib\Cloud;
/** @var \Bookly\Backend\Components\Dialogs\Sms\Dialog $self */
?>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="notification_type"><?php esc_attr_e( 'Type', 'bookly' ) ?></label>
            <select class="form-control custom-select" name="notification[type]" id="notification_type">
                <optgroup label="<?php esc_attr_e( 'Instant notifications', 'bookly' ) ?>">
                    <?php $self::renderOption( Notification::TYPE_NEW_BOOKING, array( 'customer', 'staff', 'admin', 'custom' ), 'instantly', array( 'ics', 'invoice' ) ) ?>
                    <?php Config::recurringAppointmentsActive() && $self::renderOption( Notification::TYPE_NEW_BOOKING_RECURRING, array( 'customer', 'staff', 'admin', 'custom' ), 'instantly', array( 'ics', 'invoice' ) ) ?>
                    <?php Config::proActive() && $self::renderOption( Notification::TYPE_NEW_BOOKING_COMBINED, array( 'customer', 'custom' ), 'instantly', array( 'ics', 'invoice' ) ) ?>
                    <?php $self::renderOption( Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED, array( 'customer', 'staff', 'admin', 'custom' ), 'instantly', array( 'ics', 'invoice' ) ) ?>
                    <?php Config::recurringAppointmentsActive() && $self::renderOption( Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING, array( 'customer', 'staff', 'admin', 'custom' ), 'instantly', array( 'ics', 'invoice' ) ) ?>
                    <?php Config::packagesActive() && $self::renderOption( Notification::TYPE_NEW_PACKAGE, array( 'customer', 'staff', 'admin', 'custom' ) ) ?>
                    <?php Config::packagesActive() && $self::renderOption( Notification::TYPE_PACKAGE_DELETED, array( 'customer', 'staff', 'admin', 'custom' ) ) ?>
                    <?php Config::proActive() && $self::renderOption( Notification::TYPE_CUSTOMER_NEW_WP_USER, array( 'customer' ) ) ?>
                    <?php Config::proActive() && $self::renderOption( Notification::TYPE_STAFF_NEW_WP_USER, array( 'staff' ) ) ?>
                    <?php Config::proActive() && get_option( 'bookly_cloud_token' ) != '' && Cloud\API::getInstance()->account->productActive( Cloud\Account::PRODUCT_GIFT ) && $self::renderOption( Notification::TYPE_NEW_GIFT_CARD, array( 'customer', 'staff', 'admin', 'custom' ) ) ?>
                    <?php Config::waitingListActive() && $self::renderOption( Notification::TYPE_STAFF_WAITING_LIST, array( 'staff', 'admin', 'custom' ) ) ?>
                    <?php Config::waitingListActive() && $self::renderOption( Notification::TYPE_FREE_PLACE_WAITING_LIST, array( 'customer', 'staff', 'admin', 'custom' ) ) ?>
                    <?php $self::renderOption( Notification::TYPE_VERIFY_EMAIL, array( 'customer' ) ) ?>
                </optgroup>
                <?php if ( Config::proActive() ) : ?>
                    <optgroup label="<?php esc_attr_e( 'Scheduled notifications (require cron setup)', 'bookly' ) ?>">
                        <?php $self::renderOption( Notification::TYPE_APPOINTMENT_REMINDER, array( 'customer', 'staff', 'admin', 'custom' ), 'bidirectional full', array( 'ics', 'invoice' ) ) ?>
                        <?php $self::renderOption( Notification::TYPE_LAST_CUSTOMER_APPOINTMENT, array( 'customer', 'staff', 'admin', 'custom' ), 'bidirectional full', array( 'ics' ) ) ?>
                        <?php $self::renderOption( Notification::TYPE_CUSTOMER_BIRTHDAY, array( 'customer', 'custom' ), 'bidirectional at-time' ) ?>
                        <?php $self::renderOption( Notification::TYPE_STAFF_DAY_AGENDA, array( 'staff', 'admin', 'custom' ), 'before' ) ?>
                    </optgroup>
                <?php endif ?>
            </select>
            <small class="form-text text-muted"><?php esc_html_e( 'Select the type of event at which the notification is sent.', 'bookly' ) ?></small>
            <small class="form-text text-muted bookly-js-help-block <?php echo Notification::TYPE_NEW_BOOKING_COMBINED ?>"><?php esc_html_e( 'This notification is sent once for a booking made by a customer and includes all cart items.', 'bookly' ) ?></small>
        </div>
    </div>
</div>