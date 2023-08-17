<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
/** @var \Bookly\Lib\UserBookingData $userData */
?>
<div class="bookly-form-group">
    <label><?php echo Common::getTranslatedOption( 'bookly_l10n_label_email' ) ?></label>
    <div>
        <input class="bookly-js-user-email" maxlength="255" type="text" value="<?php esc_attr_e( $userData->getEmail() ) ?>"/>
    </div>
    <div class="bookly-js-user-email-error bookly-label-error"></div>
</div>