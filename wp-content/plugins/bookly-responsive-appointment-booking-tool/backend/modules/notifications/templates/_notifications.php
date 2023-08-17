<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Notices;
use Bookly\Backend\Modules\Notifications;
use Bookly\Lib\Config;

/** @var array $datatables */
?>

<div class="d-block d-lg-flex">
    <div>
        <div class="form-group">
            <input class="form-control" type="text" id="bookly-filter" placeholder="<?php esc_attr_e( 'Quick search notifications', 'bookly' ) ?>"/>
        </div>
    </div>
    <div class="flex-fill justify-content-end form-row">
        <?php Dialogs\Notifications\Dialog::renderNewNotificationButton() ?>
        <?php Dialogs\TableSettings\Dialog::renderButton( 'email_notifications', 'BooklyL10n', esc_attr( add_query_arg( array( 'page' => Notifications\Page::pageSlug() ), admin_url( 'admin.php' ) ) ) ) ?>
    </div>
</div>
<div class="row">
    <div class="col">
        <table id="bookly-js-notification-list" class="table table-striped w-100">
            <thead>
            <tr>
                <?php foreach ( $datatables['email_notifications']['settings']['columns'] as $column => $show ) : ?>
                    <?php if ( $show ) : ?>
                        <?php if ( $column == 'type' ) : ?>
                            <th width="1"></th>
                        <?php else : ?>
                            <th><?php echo esc_html( $datatables['email_notifications']['titles'][ $column ] ) ?></th>
                        <?php endif ?>
                    <?php endif ?>
                <?php endforeach ?>
                <th width="75"></th>
                <th width="16"><?php Inputs::renderCheckBox( null, null, null, array( 'id' => 'bookly-check-all' ) ) ?></th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<div class="form-row mb-3">
    <div class="col-auto">
        <?php Inputs::renderCsrf() ?>
        <?php Buttons::renderDefault( 'bookly-js-test-email-notifications', null, __( 'Test email notifications', 'bookly' ), array(), true ) ?>
    </div>
    <div class="ml-auto mr-1">
        <?php Buttons::renderDelete( 'bookly-js-delete-notifications' ) ?>
    </div>
</div>
<?php Config::proActive() && Notices\Cron\Notice::render() ?>
<?php $self::renderTemplate( '_test_email_modal' ) ?>

