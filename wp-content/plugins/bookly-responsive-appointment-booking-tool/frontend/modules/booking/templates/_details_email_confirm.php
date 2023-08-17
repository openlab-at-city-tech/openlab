<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
/** @var \Bookly\Lib\UserBookingData $userData */
?>
<div class="bookly-form-group">
    <label><?php echo Common::getTranslatedOption( 'bookly_l10n_label_email_confirm' ) ?></label>
    <div>
        <input class="bookly-js-user-email-confirm" maxlength="255" type="text" value="<?php esc_attr_e( $userData->getEmailConfirm() ) ?>"/>
    </div>
    <div class="bookly-js-user-email-confirm-error bookly-label-error"></div>
</div>