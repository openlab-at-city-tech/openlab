<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;

$color = get_option( 'bookly_app_color', '#f4662f' );
$custom_css = trim( get_option( 'bookly_app_custom_styles' ) );
?>
<style type="text/css">
    :root {
        --bookly-main-color: <?php echo esc_attr( $color ) ?> !important;
    }

    <?php if ( $custom_css != '' ) : ?>
    <?php echo Common::css( $custom_css ) ?>
    <?php endif ?>
</style>