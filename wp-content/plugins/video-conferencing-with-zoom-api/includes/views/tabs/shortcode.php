<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="zvc-row">
    <div class="zvc-position-floater-left">
        <section class="zoom-api-example-section">
            <h1>Go <a href="https://zoom.codemanas.com/shortcode/" target="_blank">here</a> for more information about shortcodes.</h1>
            <h3><?php _e( 'Using Shortcode Example', 'video-conferencing-with-zoom-api' ); ?></h3>
            <p><?php _e( 'Below are few examples of how you can add shortcodes manually into your posts.', 'video-conferencing-with-zoom-api' ); ?></p>

            <div class="zoom-api-basic-usage">
                <h4>1. <?php _e( 'Basic Usage', 'video-conferencing-with-zoom-api' ); ?>:</h4>
                <code>[zoom_api_link meeting_id="123456789" link_only="no"]</code>
            </div>
            <div class="zoom-api-basic-usage" style="margin-top: 20px;border-top:1px solid #ccc;">
                <h4>2. <?php _e( 'Listing Zoom Meetings', 'video-conferencing-with-zoom-api' ); ?>:</h4>
                <code>[zoom_list_meetings per_page="5" category="test,test2,test3" order="ASC" type="upcoming" filter="no"]</code>
            </div>
            <div class="zoom-api-basic-usage" style="margin-top: 20px;border-top:1px solid #ccc;">
                <h4>2. <?php _e( 'Listing Zoom Webinars', 'video-conferencing-with-zoom-api' ); ?>:</h4>
                <code>[zoom_list_webinars per_page="5" category="test,test2,test3" order="ASC" type="upcoming" filter="no"]</code>
            </div>
            <div class="zoom-api-basic-usage" style="margin-top: 20px;border-top:1px solid #ccc;">
                <h4>3. <?php _e( 'List Host ID', 'video-conferencing-with-zoom-api' ); ?>:</h4>
                <code>[zoom_list_host_meetings host="YOUR_HOST_ID"]</code>
            </div>
            <div class="zoom-api-basic-usage" style="margin-top: 20px;border-top:1px solid #ccc;">
                <h4>4. <?php _e( 'Embed Zoom Meeting in your Browser', 'video-conferencing-with-zoom-api' ); ?>:</h4>
                <code>[zoom_join_via_browser meeting_id="YOUR_MEETING_ID" login_required="no" help="yes" title="Test" height="500px" disable_countdown="yes" passcode="12345" webinar="no"]</code>
            </div>
            <div class="zoom-api-basic-usage" style="margin-top: 20px;border-top:1px solid #ccc;">
                <h4>5. <?php _e( 'Show webinars based on HOST ID in frontend.', 'video-conferencing-with-zoom-api' ); ?>:</h4>
                <code>[zoom_list_host_webinars host="YOUR_HOST_ID"]</code>
            </div>
            <div class="zoom-api-basic-usage" style="margin-top: 20px;border-top:1px solid #ccc;">
                <h4>6. <?php _e( 'Show Webinar based meeting ID.', 'video-conferencing-with-zoom-api' ); ?>:</h4>
                <code>[zoom_api_webinar webinar_id="YOUR_WEBINAR_ID" link_only="no"]</code>
            </div>
            <div class="zoom-api-basic-usage" style="margin-top: 20px;border-top:1px solid #ccc;">
                <h4>7. <?php _e( 'Show recordings based on HOST ID.', 'video-conferencing-with-zoom-api' ); ?>:</h4>
                <code>[zoom_recordings host_id="YOUR_HOST_ID" downloadable="yes"]</code>
            </div>
            <div class="zoom-api-basic-usage" style="margin-top: 20px;border-top:1px solid #ccc;">
                <h4>8. <?php _e( 'Show Recordings based on Meeting ID', 'video-conferencing-with-zoom-api' ); ?>:</h4>
                <code>[zoom_recordings_by_meeting meeting_id="YOUR_MEETING_ID" downloadable="no"]</code>
            </div>
        </section>
    </div>
</div>
