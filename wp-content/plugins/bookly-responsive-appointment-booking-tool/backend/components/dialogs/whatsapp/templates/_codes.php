<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Entities\Notification;
$codes = new \Bookly\Backend\Modules\Notifications\Lib\Codes( $gateway );
?>
<div class="form-group bookly-js-codes-container">
    <a class="bookly-collapsed mb-2 d-inline-block" data-toggle="bookly-collapse" href="#bookly-notification-codes" role="button" aria-expanded="false" aria-controls="collapseExample">
        <?php esc_attr_e( 'Codes', 'bookly' ) ?>
    </a>
    <div class="bookly-collapse" id="bookly-notification-codes">
        <?php foreach ( Notification::getTypes() as $notification_type ) :
            $codes->render( $notification_type );
        endforeach ?>
    </div>
</div>