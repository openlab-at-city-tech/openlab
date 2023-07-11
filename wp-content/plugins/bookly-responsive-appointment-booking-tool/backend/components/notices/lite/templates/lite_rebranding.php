<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div id="bookly-tbs" class="wrap">
    <div id="bookly-lite-rebranding-notice" class="alert alert-info" data-action="bookly_dismiss_lite_rebranding_notice">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <div class="form-row">
            <div class="mr-3"><i class="fas fa-info-circle fa-2x"></i></div>
            <div class="col">
                <?php printf( __( '<b>Bookly Lite rebrands into Bookly with more features available.</b><br/><br/>We have changed the architecture of Bookly Lite and Bookly to optimize the development of both plugin versions and add more features to the new free Bookly. To learn more about the major Bookly update, check our <a href="%s" target="_blank">blog post</a>.', 'bookly' ), 'https://www.booking-wp-plugin.com/bookly-major-update/?utm_source=bookly_admin&utm_medium=pro_not_active&utm_campaign=notification' ) ?>
            </div>
        </div>
    </div>
</div>