<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
?>
<div class="bookly-box bookly-list">
    <label>
        <input type="radio" class="bookly-js-payment" name="payment-method-<?php echo esc_attr( $form_id ) ?>" value="local"/>
        <span><?php echo Common::getTranslatedOption( 'bookly_l10n_label_pay_locally' ) ?></span>
    </label>
</div>
