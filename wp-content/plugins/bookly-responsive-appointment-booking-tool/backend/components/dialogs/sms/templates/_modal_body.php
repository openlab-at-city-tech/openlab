<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Container;
use Bookly\Backend\Components\Controls\Inputs;
/** @var string $gateway */
?>
<div class="bookly-js-loading" style="height: 200px;"></div>
<div class="bookly-js-loading">
    <?php Container::renderHeader( __( 'Notification settings', 'bookly' ), 'bookly-js-settings-container' ) ?>
    <input type="hidden" name="notification[id]" value="0">
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="form-group">
                <label for="notification_name"><?php esc_attr_e( 'Name', 'bookly' ) ?></label>
                <input type="text" class="form-control" id="notification_name" name="notification[name]" value=""/>
                <small class="form-text text-muted"><?php esc_html_e( 'Enter notification name which will be displayed in the list.', 'bookly' ) ?></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php Inputs::renderRadioGroup( __( 'State', 'bookly' ), __( 'Choose whether notification is enabled and sending messages or it is disabled and no messages are sent until you activate the notification.', 'bookly' ), array(), 1, array( 'name' => 'notification[active]' ) ) ?>
        </div>
    </div>

    <?php $self::renderTemplate( '_types' ) ?>
    <?php static::renderTemplate( '_settings' ) ?>

    <div class="row bookly-js-recipient-container">
        <div class="col-md-12">
            <div class="form-group">
                <label><?php esc_attr_e( 'Recipients', 'bookly' ) ?></label>
                <?php Inputs::renderCheckBox( __( 'Client', 'bookly' ), 1, null, array( 'name' => 'notification[to_customer]' ) ) ?>
                <?php Inputs::renderCheckBox( __( 'Staff', 'bookly' ), 1, null, array( 'name' => 'notification[to_staff]' ) ) ?>
                <?php Inputs::renderCheckBox( __( 'Administrators', 'bookly' ), 1, null, array( 'name' => 'notification[to_admin]' ) ) ?>
                <?php Inputs::renderCheckBox( __( 'Custom', 'bookly' ), 1, null, array( 'name' => 'notification[to_custom]' ) ) ?>
                <div class="bookly-js-custom-recipients">
                    <textarea name="notification[custom_recipients]" rows="2" class="form-control"></textarea>
                    <?php if ( $gateway == 'email' ) : ?>
                        <small class="form-text text-muted"><?php esc_html_e( 'You can enter multiple email addresses (one per line)', 'bookly' ) ?></small>
                    <?php else: ?>
                        <small class="form-text text-muted"><?php esc_html_e( 'You can enter multiple phone numbers (one per line)', 'bookly' ) ?></small>
                    <?php endif ?>
                </div>
                <small class="form-text text-muted"><?php esc_html_e( 'Choose who will receive this notification.', 'bookly' ) ?></small>
            </div>
        </div>
    </div>

    <?php Container::renderFooter() ?>
    <?php Container::renderHeader( '', 'bookly-js-message-container' ) ?>

    <?php $self::renderTemplate( '_subject' ) ?>
    <?php $self::renderTemplate( '_editor' ) ?>
    <?php if ( $gateway == 'email' || $gateway == 'whatsapp' ) : ?>
        <?php $self::renderTemplate( '_codes', compact( 'gateway' ) ) ?>
    <?php endif ?>
    <?php Container::renderFooter() ?>
</div>