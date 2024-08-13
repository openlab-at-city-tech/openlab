<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Controls;
use Bookly\Backend\Components\Cloud;
use Bookly\Lib;

/**
 * @var Bookly\Backend\Modules\CloudProducts\Page $self
 * @var Lib\Cloud\API $cloud
 * @var array $products
 */
$update_required_modal = false;
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Bookly Cloud', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row pb-3">
                <div class="col">
                </div>
                <div class="col-auto">
                    <?php Cloud\Account\Panel::render() ?>
                </div>
            </div>
            <?php foreach ( $products as $product ) : ?>
                <div class="card bg-light p-3 mb-3 bookly-js-cloud-product" data-product="<?php echo esc_attr( $product['id'] ) ?>">
                    <div class="form-row">
                        <div class="col-xl-9 col-md-8 col-xs-12">
                            <div class="d-flex">
                                <div class="mr-4 mb-4">
                                    <img src="<?php echo esc_attr( $product['icon_url'] ) ?>" alt="<?php echo esc_attr( $product['texts']['title'] ) ?>"/>
                                </div>
                                <div class="flex-fill">
                                    <div class="h4 mb-2"><?php echo Lib\Utils\Common::stripScripts( $product['texts']['title'] ) ?></div>
                                    <?php echo Lib\Utils\Common::stripScripts( $product['texts']['description'] ) ?>
                                    <?php if ( $product['button'] ) : ?>
                                        <div>
                                            <?php Controls\Buttons::render( null, 'btn-white border text-nowrap bookly-js-product-info-button mt-2', $product['texts']['info-button'] ); ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-4 col-xs-12 mt-2 mt-md-0">
                            <div class="bookly-js-product-price-selector text-center" data-pp-id="" data-product="<?php echo esc_attr( $product['id'] ) ?>">
                                <?php include '_price.php' ?>
                                <?php if ( $product['version'] > Lib\Plugin::getVersion() ) : ?>
                                    <?php $update_required_modal = true ?>
                                    <?php Controls\Buttons::render( null, 'btn-default bookly-js-bookly-update-required', $product['texts']['action-on'], array( 'data-version' => $product['version'] ) ) ?>
                                    <div class="mt-2 text-danger"><strong><?php printf( esc_html__( 'Bookly %s required', 'bookly' ), esc_html( $product['version'] ) ) ?></strong></div>
                                <?php else : ?>
                                    <?php static::renderTemplate( '_action_btn', compact( 'product', 'cloud' ) ) ?>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
    <?php include '_info.php' ?>
    <?php include '_activation_modal.php' ?>
    <?php include '_unsubscribe_modal.php' ?>
    <?php if ( $update_required_modal ) : ?>
        <?php include '_update_required.php' ?>
    <?php endif ?>
</div>