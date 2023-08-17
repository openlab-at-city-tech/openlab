<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
use Bookly\Backend\Components\Cloud\Recharge;
/**
 * @var Bookly\Lib\Cloud\API $cloud
 */
?>
<div class="form-row" id="bookly-cloud-panel">
    <?php if ( $cloud->account->autoRechargeEnabled() ) : ?>
        <div class="col d-flex align-items-center">
            <?php include '_auto_recharge.php' ?>
        </div>
    <?php endif ?>
    <div class="col d-flex align-items-center">
        <?php include '_support.php' ?>
    </div>
    <div class="col">
        <?php include '_balance.php' ?>
    </div>
    <div class="col">
        <div class="bookly-dropdown bookly-show">
            <a id="bookly-open-account-settings" class="btn <?php echo esc_attr( $cloud->account->getEmailConfirmed() ? 'btn-default' : 'btn-danger' ) ?> text-truncate bookly-dropdown-toggle ladda-button" href="#" data-toggle="bookly-dropdown" aria-haspopup="true" aria-expanded="false" data-spinner-color="#666666" data-style="zoom-in" data-spinner-size="40">
                <span class="ladda-label"><i class="fas <?php echo esc_attr( $cloud->account->getEmailConfirmed() ? 'fa-user' : 'fa-user-slash' ) ?>"></i><span class="d-none d-md-inline ml-2"><?php echo esc_html( $cloud->account->getUserName() ) ?></span></span>
            </a>
            <div class="bookly-dropdown-menu bookly-dropdown-menu-compact bookly-dropdown-menu-right" aria-labelledby="bookly-open-account-settings">
                <?php if ( ! $cloud->account->getEmailConfirmed() ) : ?>
                    <a id="bookly-open-email-confirm" class="bookly-dropdown-item text-danger" href="#">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php esc_html_e( 'Confirm email', 'bookly' ) ?>
                    </a>
                <?php endif ?>
                <a class="bookly-dropdown-item bookly-js-ladda" href="<?php echo Common::escAdminUrl( Bookly\Backend\Modules\CloudSettings\Page::pageSlug() ) ?>">
                    <i class="fas fa-cog mr-2"></i><?php esc_html_e( 'Settings', 'bookly' ) ?>
                </a>
                <a id="bookly-logout" class="bookly-dropdown-item bookly-js-ladda" href="#">
                    <i class="fas fa-sign-out-alt mr-2"></i><?php esc_html_e( 'Log out', 'bookly' ) ?>
                </a>
            </div>
        </div>
    </div>
</div>
<?php Recharge\Dialog::render() ?>

<?php if ( ! $cloud->account->getCountry() ): ?>
    <?php include '_setup_country.php' ?>
<?php endif ?>
<?php if ( ! $cloud->account->getEmailConfirmed() ): ?>
    <?php include "_confirm_email.php" ?>
<?php endif ?>
