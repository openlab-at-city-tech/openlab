<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Lib\Config;
/**
 * @var array $product
 * @var Bookly\Lib\Cloud\API $cloud
 */
$pro_required = $product['pro_required'] && ! Config::proActive();
?>
<div>
    <?php if ( $cloud->account->loadProfile() ) : ?>
        <?php if ( isset( $product['cancel_on_renewal'] ) && $product['cancel_on_renewal'] === true ) : ?>
            <?php Buttons::render( null, $pro_required ? 'bookly-js-required-bookly-pro btn-success' : 'bookly-js-product-revert-cancel btn-success', $product['texts']['action-revert-cancel'] ) ?>
        <?php else : ?>
            <?php if ( $pro_required ) : ?>
                <?php if ( isset( $product['next_billing_date'] ) ) : ?>
                    <?php Buttons::render( null, 'bookly-js-required-bookly-pro btn-danger', $product['texts']['action-off'] ) ?>
                <?php else : ?>
                    <?php Buttons::render( null, 'bookly-js-required-bookly-pro btn-success', $product['texts']['action-on'] ) ?>
                <?php endif ?>
            <?php else : ?>
                <?php Buttons::render( null, 'bookly-js-product-enable btn-success bookly-collapse', $product['texts']['action-on'] ) ?>
                <?php Buttons::render( null, 'bookly-js-product-disable btn-danger bookly-collapse', $product['texts']['action-off'] ) ?>
            <?php endif ?>
        <?php endif ?>
    <?php else : ?>
        <?php Buttons::render( null, 'bookly-js-product-login-button btn-success', $product['texts']['action-on'] ) ?>
    <?php endif ?>
</div>
