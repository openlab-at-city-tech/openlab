<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Settings\Selects;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Modules\Notifications;
use Bookly\Lib\Utils;

foreach ( range( 1, 23 ) as $hours ) {
    $bookly_ntf_processing_interval_values[] = array( $hours, Utils\DateTime::secondsToInterval( $hours * HOUR_IN_SECONDS ) );
}
?>
<form id="bookly-email-settings-form">
    <?php self::renderTemplate( '_common_settings', array( 'tail' => '_gen' ) ) ?>
    <div class="row">
        <div class="col-md-12">
            <?php Selects::renderSingle( 'bookly_ntf_processing_interval', __( 'Scheduled notifications retry period', 'bookly' ), __( 'Set period of time when system will attempt to deliver notification to user. Notification will be discarded after period expiration.', 'bookly' ), $bookly_ntf_processing_interval_values ) ?>
        </div>
    </div>
    <?php Notifications\Proxy\Pro::renderLogsSettings() ?>
    <?php Inputs::renderCsrf() ?>
</form>