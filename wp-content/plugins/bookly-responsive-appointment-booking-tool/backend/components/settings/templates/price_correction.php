<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Settings;
use Bookly\Lib\Entities\Payment;

$option_increase = 'bookly_' . $gateway . '_increase';
$option_addition = 'bookly_' . $gateway . '_addition';
?>
<div class="form-group">
    <div class="form-row">
        <div class="col-6">
            <label for="<?php echo esc_attr( $option_increase ) ?>"><?php esc_html_e( 'Price correction', 'bookly' ) ?> <span class="text-muted"><?php esc_html_e( 'Increase/Discount (%)', 'bookly' ) ?></span></label>
            <input type="number" id="<?php echo esc_attr( $option_increase ) ?>" class="form-control" name="<?php echo esc_attr( $option_increase ) ?>" value="<?php echo esc_attr( get_option( $option_increase ) ) ?>" min="-100" max="100" step="any"/>
        </div>
        <div class="col-6">
            <label for="<?php echo esc_attr( $option_addition ) ?>"><span class="text-muted"><?php esc_html_e( 'Addition/Deduction', 'bookly' ) ?></span></label>
            <input type="number" id="<?php echo esc_attr( $option_addition ) ?>" class="form-control" name="<?php echo esc_attr( $option_addition ) ?>" value="<?php echo esc_attr( get_option( $option_addition ) ) ?>" step="any"/>
        </div>
    </div>
    <?php if ( ! in_array( $gateway, array( Payment::TYPE_MOLLIE, Payment::TYPE_PAYSON, Payment::TYPE_STRIPE, Payment::TYPE_CLOUD_STRIPE, Payment::TYPE_PAYUBIZ ) ) ) : ?>
        <?php if ( \Bookly\Lib\Config::taxesActive() ) :
            Settings\Proxy\Taxes::renderHelpMessage();
        else: ?>
            <small class="form-text text-muted"><?php esc_html_e( 'This setting affects the cost of the booking according to the payment gateway used. Specify a percentage or fixed amount. Use minus ("-") sign for decrease/discount.', 'bookly' ) ?></small>
        <?php endif ?>
    <?php else: ?>
        <small class="form-text text-muted"><?php esc_html_e( 'This setting affects the cost of the booking according to the payment gateway used. Specify a percentage or fixed amount. Use minus ("-") sign for decrease/discount.', 'bookly' ) ?></small>
    <?php endif ?>
</div>
