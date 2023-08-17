<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/**
 * @var Bookly\Lib\Cloud\API $cloud
 */
?>
<span class="badge rounded-pill alert-info font-weight-normal p-2" id="bookly-cloud-auto-recharge">
    <i class="fas fa-sync fa-sm"></i>
    $<?php echo esc_html( $cloud->account->getAutoRechargeAmount() ) ?>
</span>
