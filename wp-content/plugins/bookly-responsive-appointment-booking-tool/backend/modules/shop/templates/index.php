<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Support;
use Bookly\Lib;
?>
<style>
    .bookly-css-root {
        --bookly-color: <?php echo esc_attr( get_option( 'bookly_app_color', '#f4662f' ) ) ?>;
    }
</style>
<div id="bookly-tbs" class="wrap">
    <div class="form-row align-items-center mb-3">
        <h4 class="col m-0"><?php esc_html_e( 'Add-ons', 'bookly' ) ?></h4>
        <?php Support\Buttons::render( $self::pageSlug() ) ?>
    </div>
    <div id="bookly-addons-form"></div>
    <?php Lib\Proxy\Pro::renderLicenseDialog() ?>
</div>