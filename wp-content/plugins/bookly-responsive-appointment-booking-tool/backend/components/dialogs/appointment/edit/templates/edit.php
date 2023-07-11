<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Dialogs\Appointment\AttachPayment\Proxy as AttachPaymentProxy;
use Bookly\Backend\Components\Dialogs;
/** @var bool $show_wp_users */
?>
<div id="bookly-appointment-dialog"></div>
<?php AttachPaymentProxy\Pro::renderAttachPaymentDialog() ?>
<?php Dialogs\Payment\Dialog::render() ?>
<?php Dialogs\Appointment\CustomerDetails\Dialog::render() ?>
<?php Dialogs\Customer\Edit\Dialog::render( $show_wp_users ) ?>