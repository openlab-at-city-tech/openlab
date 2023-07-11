<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="alert alert-info">
    <div class="row">
        <div class="col-md-12">
            <i class="fas fa-info-circle mr-2 text-primary"></i>
            <?php printf( __( 'To allow scheduled actions, please activate <a href="%s" target="_blank">Bookly Cloud Cron</a> or follow <a href="%s" target="_blank">the instructions</a> about cron setup', 'bookly' ),
                \Bookly\Lib\Utils\Common::escAdminUrl( \Bookly\Backend\Modules\CloudProducts\Page::pageSlug() ),
                'https://support.booking-wp-plugin.com/hc/en-us/articles/360015017400-How-can-I-configure-CRON-to-send-the-Bookly-reminders' ) ?>
        </div>
    </div>
</div>