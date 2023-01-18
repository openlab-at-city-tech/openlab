<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Entities\Notification;
use Bookly\Lib\Config;
use Bookly\Lib\Cloud;
?>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="notification_type"><?php esc_attr_e( 'Type', 'bookly' ) ?></label>
            <select class="form-control custom-select" name="notification[type]" id="notification_type">
                <optgroup label="<?php esc_attr_e( 'Instant notifications', 'bookly' ) ?>">
                    <option value="<?php echo Notification::TYPE_NEW_BOOKING ?>"
                            data-set="instantly"
                            data-recipients='["customer","staff","admin","custom"]'
                            data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_NEW_BOOKING ) ) ?>'
                            data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_NEW_BOOKING ) ) ?></option>
                    <?php if ( Config::recurringAppointmentsActive() ): ?>
                        <option value="<?php echo Notification::TYPE_NEW_BOOKING_RECURRING ?>"
                                data-set="instantly"
                                data-recipients='["customer","staff","admin","custom"]'
                                data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_NEW_BOOKING_RECURRING ) ) ?>'
                                data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_NEW_BOOKING_RECURRING ) ) ?></option>
                    <?php endif ?>
                    <?php if ( Config::proActive() ): ?>
                        <option value="<?php echo Notification::TYPE_NEW_BOOKING_COMBINED ?>"
                                data-set="instantly"
                                data-recipients='["customer","custom"]'
                                data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_NEW_BOOKING_COMBINED ) ) ?>'
                                data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_NEW_BOOKING_COMBINED ) ) ?></option>
                    <?php endif ?>
                    <option value="<?php echo Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED ?>"
                            data-set="instantly"
                            data-recipients='["customer","staff","admin","custom"]'
                            data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED ) ) ?>'
                            data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED ) ) ?></option>
                    <?php if ( Config::recurringAppointmentsActive() ): ?>
                        <option value="<?php echo Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING ?>"
                                data-set="instantly"
                                data-recipients='["customer","staff","admin","custom"]'
                                data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING ) ) ?>'
                                data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_CUSTOMER_APPOINTMENT_STATUS_CHANGED_RECURRING ) ) ?></option>
                    <?php endif ?>
                    <?php if ( Config::packagesActive() ) : ?>
                        <option value="<?php echo Notification::TYPE_NEW_PACKAGE ?>"
                                data-set="instantly"
                                data-recipients='["customer","staff","admin","custom"]'
                                data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_NEW_PACKAGE ) ) ?>'
                                data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_NEW_PACKAGE ) ) ?></option>
                    <?php endif ?>
                    <?php if ( Config::packagesActive() ) : ?>
                        <option value="<?php echo Notification::TYPE_PACKAGE_DELETED ?>"
                                data-set="instantly"
                                data-recipients='["customer","staff","admin","custom"]'
                                data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_PACKAGE_DELETED ) ) ?>'
                                data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_PACKAGE_DELETED ) ) ?></option>
                    <?php endif ?>
                    <?php if ( Config::proActive() ) : ?>
                        <option value="<?php echo Notification::TYPE_CUSTOMER_NEW_WP_USER ?>"
                                data-set="instantly"
                                data-recipients='["customer"]'
                                data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_CUSTOMER_NEW_WP_USER ) ) ?>'
                                data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_CUSTOMER_NEW_WP_USER ) ) ?></option>
                        <?php if ( get_option( 'bookly_cloud_token' ) != '' && Cloud\API::getInstance()->account->productActive( Cloud\Account::PRODUCT_GIFT ) ) : ?>
                            <option value="<?php echo Notification::TYPE_NEW_GIFT_CARD ?>"
                                    data-set="instantly"
                                    data-recipients='["customer","staff","admin","custom"]'
                                    data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_NEW_GIFT_CARD ) ) ?>'
                                    data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_NEW_GIFT_CARD ) ) ?></option>
                        <?php endif ?>
                    <?php endif ?>
                    <?php if ( Config::waitingListActive() ) : ?>
                        <option value="<?php echo Notification::TYPE_STAFF_WAITING_LIST ?>"
                                data-set="instantly"
                                data-recipients='["staff","admin","custom"]'
                                data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_STAFF_WAITING_LIST ) ) ?>'
                                data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_STAFF_WAITING_LIST ) ) ?></option>
                        <option value="<?php echo Notification::TYPE_FREE_PLACE_WAITING_LIST ?>"
                                data-set="instantly"
                                data-recipients='["staff","admin","custom","customer"]'
                                data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_FREE_PLACE_WAITING_LIST ) ) ?>'
                                data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_FREE_PLACE_WAITING_LIST ) ) ?></option>
                    <?php endif ?>
                        <option value="<?php echo Notification::TYPE_VERIFY_PHONE ?>"
                                data-set="instantly"
                                data-recipients='["customer"]'
                                data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_VERIFY_PHONE ) ) ?>'
                                data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_VERIFY_PHONE ) ) ?></option>
                </optgroup>
                <optgroup label="<?php esc_attr_e( 'Scheduled notifications (require cron setup)', 'bookly' ) ?>">
                    <option value="<?php echo Notification::TYPE_APPOINTMENT_REMINDER ?>"
                            data-set="bidirectional full"
                            data-recipients='["customer","staff","admin","custom"]'
                            data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_APPOINTMENT_REMINDER ) ) ?>'
                            data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_APPOINTMENT_REMINDER ) ) ?></option>
                    <option value="<?php echo Notification::TYPE_LAST_CUSTOMER_APPOINTMENT ?>"
                            data-set="bidirectional full"
                            data-recipients='["customer","staff","admin","custom"]'
                            data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_LAST_CUSTOMER_APPOINTMENT ) ) ?>'
                            data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_LAST_CUSTOMER_APPOINTMENT ) ) ?></option>
                    <?php if ( Config::proActive() ) : ?>
                        <option value="<?php echo Notification::TYPE_CUSTOMER_BIRTHDAY ?>"
                                data-set="bidirectional at-time"
                                data-recipients='["customer","custom"]'
                                data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_CUSTOMER_BIRTHDAY ) ) ?>'
                                data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_CUSTOMER_BIRTHDAY ) ) ?></option>
                    <?php endif ?>
                    <option value="<?php echo Notification::TYPE_STAFF_DAY_AGENDA ?>"
                            data-set="before"
                            data-recipients='["staff","admin","custom"]'
                            data-icon='<?php echo esc_attr( Notification::getIcon( Notification::TYPE_STAFF_DAY_AGENDA ) ) ?>'
                            data-attach='[]'><?php echo esc_attr( Notification::getTitle( Notification::TYPE_STAFF_DAY_AGENDA ) ) ?></option>
                </optgroup>
            </select>
            <small class="form-text text-muted"><?php esc_html_e( 'Select the type of event at which the notification is sent.', 'bookly' ) ?></small>
            <small class="form-text text-muted bookly-js-help-block mt-2 <?php echo Notification::TYPE_NEW_BOOKING_COMBINED ?>"><?php esc_html_e( 'This notification is sent once for a booking made by a customer and includes all cart items.', 'bookly' ) ?></small>
        </div>
    </div>
</div>