<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
?>
<div id="bookly-endpoint" class="accordion" role="tablist" aria-multiselectable="true">
    <?php if ( $troubles ) : ?>
        <?php foreach ( $troubles as $product => $endpoint ) : ?>
            <div class="card bookly-collapse-with-arrow mb-0">
                <div class="card-header d-flex align-items-center bookly-js-table py-0 px-2" role="tab">
                    <button role="button" class="btn btn-link btn-block text-left text-danger py-3 shadow-none bookly-collapsed" data-toggle="bookly-collapse" href="#endpoint-<?php echo esc_attr( $product ) ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $product ) ?>">
                        <div class="bookly-collapse-title">
                            <?php echo esc_html( $products[ $product ] ) ?>
                        </div>
                    </button>
                    <div class="text-right">
                        <?php Buttons::render( '', 'btn-success', 'Fix', array( 'data-ajax' => 'updateEndPoint', 'data-tool' => 'Endpoints', 'data-params' => '{"product":"' . $product . '"}', 'data-hide-on-success' => '.card' ) ) ?>
                    </div>
                </div>
                <div class="card-body bookly-collapse pb-1" id="endpoint-<?php echo esc_attr( $product ) ?>">
                    <ul class="list-group mb-3 text-monospace">
                        <li class="list-group-item d-flex align-items-center">
                            <span class="text-muted"><?php echo esc_html( $endpoint['current'] ) ?></span>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <?php echo esc_html( $endpoint['expected'] ) ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        End points is ok
    <?php endif ?>
</div>