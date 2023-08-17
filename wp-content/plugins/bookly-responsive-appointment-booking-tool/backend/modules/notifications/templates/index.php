<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Modules\Notifications;
use Bookly\Backend\Components\Dialogs;
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Email notifications', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs bookly-js-notifications-tabs flex-column flex-lg-row bookly-nav-tabs-md" role="tablist">
                <li class="nav-item text-center">
                    <a class="nav-link<?php if ( $tab === 'notifications' ) : ?> active<?php endif ?>" href="<?php echo add_query_arg( array( 'page' => Notifications\Page::pageSlug() ), admin_url( 'admin.php' ) ) ?>" data-toggle="bookly-tab" data-tab="notifications"><?php esc_html_e( 'Notifications', 'bookly' ) ?></a>
                </li>
                <?php Notifications\Proxy\Pro::renderLogsTab( $tab ) ?>
                <li class="nav-item text-center">
                    <a class="nav-link<?php if ( $tab === 'settings' ) : ?> active<?php endif ?>" href="<?php echo add_query_arg( array( 'page' => Notifications\Page::pageSlug(), 'tab' => 'settings' ), admin_url( 'admin.php' ) ) ?>" data-toggle="bookly-tab" data-tab="settings"><?php esc_html_e( 'Settings', 'bookly' ) ?></a>
                </li>
            </ul>
        </div>
        <div class="card-body bookly-js-notifications-wrap">
        </div>
        <div class="card-footer bg-transparent text-right bookly-js-notifications-footer" style="display: none;">
            <?php Buttons::renderSubmit( null, 'bookly-js-save', __( 'Save', 'bookly' ) ) ?>
        </div>
    </div>
    <?php Dialogs\Notifications\Dialog::render() ?>
    <?php Dialogs\TableSettings\Dialog::render() ?>
</div>