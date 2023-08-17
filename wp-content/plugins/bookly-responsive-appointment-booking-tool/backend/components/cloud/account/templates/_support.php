<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/**
 * @var Bookly\Lib\Cloud\API $cloud
 */
$days = $cloud->account->getCloudSupportDays();
if ( $days <= 3 ) {
    $color_class = 'alert-danger';
} else {
    $color_class = 'alert-info';
}
?>
<span class="badge rounded-pill <?php echo esc_attr( $color_class ) ?> font-weight-normal p-2" id="bookly-cloud-support">
    <i class="fas fa-headset fa-sm"></i>
    <?php if ( $days >= 0 ): ?>
        <?php printf( _n( '%s day', '%s days', $days, 'bookly' ), $days ) ?>
    <?php endif ?>
</span>
