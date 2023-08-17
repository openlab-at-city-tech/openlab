<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/** @var Bookly\Lib\Cloud\API $cloud */
$balance = $cloud->account->getBalance();
if ( $balance <= 10 ) {
    $txt_class = 'text-danger';
} else {
    $txt_class = '';
}
?>
<div class="btn-group">
    <div class="border rounded-left pl-2 d-flex align-items-center" id="bookly-cloud-balance">
        <div class="col pl-0 pr-2 d-none d-md-inline">
            <h6 class="small text-muted m-0"><?php _e( 'current<br/>balance', 'bookly' ) ?></h6>
        </div>
        <div class="col pl-0 pr-2">
            <span class="lead <?php echo esc_attr( $txt_class ) ?>">$<?php echo number_format( $balance, 2 ) ?></span>
        </div>
    </div>
    <button type="button" class="btn btn-success text-nowrap bookly-js-recharge-dialog-activator">
        <i class="fas fa-coins"></i><span class="d-none d-md-inline ml-2"><?php esc_html_e( 'Recharge', 'bookly' ) ?></span>
    </button>
</div>
