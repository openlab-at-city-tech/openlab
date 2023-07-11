<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Lib\Utils\Common;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Notices;
use Bookly\Lib\Utils\Tables;
/** @var array $datatable */
?>
<input type="hidden" name="form-notifications">
    <div class="form-group">
        <label for="admin_phone">
            <?php esc_html_e( 'Administrator phone', 'bookly' ) ?>
        </label>
        <div class="form-row">
            <div class="col-auto">
                <input class="form-control w-100 mb-3 mb-md-0" id="admin_phone" name="bookly_sms_administrator_phone" type="text" value="<?php form_option( 'bookly_sms_administrator_phone' ) ?>">
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <button class="btn btn-success" id="test_call"><?php esc_html_e( 'Make a test call', 'bookly' ) ?></button>
                    <button type="button" class="btn btn-success bookly-dropdown-toggle" data-toggle="bookly-dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="bookly-dropdown-menu">
                        <a href="#" class="bookly-dropdown-item" data-action="save-administrator-phone"><?php esc_html_e( 'Save administrator phone', 'bookly' ) ?></a>
                    </div>
                </div>
            </div>
        </div>
        <small class="form-text text-muted"><?php esc_html_e( 'Enter a phone number in international format. E.g. for the United States a valid phone number would be +17327572923.', 'bookly' ) ?></small>
    </div>

<form method="post" action="<?php echo Common::escAdminUrl( $self::pageSlug() ) ?>">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <input class="form-control" type="text" id="bookly-filter" placeholder="<?php esc_attr_e( 'Quick search notifications', 'bookly' ) ?>"/>
            </div>
        </div>
        <div class="col-md-8 form-row justify-content-end pr-2">
            <?php Dialogs\Sms\Dialog::renderNewNotificationButton() ?>
            <?php Dialogs\TableSettings\Dialog::renderButton( Tables::VOICE_NOTIFICATIONS, 'BooklyL10n' ) ?>
        </div>
    </div>


    <table id="bookly-js-notification-list" class="table table-striped w-100">
        <thead>
        <tr>
            <?php foreach ( $datatable['settings']['columns'] as $column => $show ) : ?>
                <?php if ( $show ) : ?>
                    <?php if ( $column  === 'type' ) : ?>
                        <th width="1"></th>
                    <?php else : ?>
                        <th><?php echo esc_html( $datatable['titles'][ $column ] ) ?></th>
                    <?php endif ?>
                <?php endif ?>
            <?php endforeach ?>
            <th width="75"></th>
            <th width="16"><?php Inputs::renderCheckBox( null, null, null, array( 'id' => 'bookly-check-all' ) ) ?></th>
        </tr>
        </thead>
    </table>

    <div class="form-row my-3">
        <div class="col-auto">
            <?php Buttons::renderDefault( 'bookly-js-test-voice-notifications', null, __( 'Test voice notifications', 'bookly' ), array(), true ) ?>
        </div>
        <div class="ml-auto mr-1">
            <?php Buttons::renderDelete( 'bookly-js-delete-notifications' ) ?>
        </div>
    </div>

    <?php Notices\Cron\Notice::render() ?>
</form>
<?php Dialogs\Voice\Dialog::render() ?>