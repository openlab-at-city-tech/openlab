<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Support;
use Bookly\Backend\Components\Cloud;
/**
 * @var string $title
 * @var string $slug
 */
?>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php echo esc_html( $title ) ?></h4>
        <?php Support\Buttons::render( $slug ) ?>
    </div>
    <div id="bookly-login-required" class="card mb-4" style="min-height: 600px;">
        <div class="card-body">
            <div class="row pb-3">
                <div class="col">
                </div>
                <div class="col-auto">
                    <?php Cloud\Account\Panel::render() ?>
                </div>
            </div>
        </div>
    </div>
</div>