<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

class EMCS_Admin
{
    public static function clear_unwanted_notices()
    {
        if (isset($_REQUEST['page'])) {

            if ($_REQUEST['page'] == 'emcs-customizer' || $_REQUEST['page'] == 'emcs-event-types' || $_REQUEST['page'] == 'emcs-settings') {
                remove_all_actions('admin_notices');
                remove_all_actions('all_admin_notices');
            }
        }
    }

    public static function display_notices()
    {

        $activation_time = get_option('emcs_activation_time');
        $stop_review_reminder = get_option('emcs_stop_review_notice');
        $review_past_date = strtotime('-7 days');
        $stop_newsletter_reminder = get_option('emcs_stop_newsletter_notice');
        $newsletter_past_date = strtotime('-14 days');

        if ($review_past_date > $activation_time && !$stop_review_reminder) {
            add_action('admin_notices', 'EMCS_Admin::rating_admin_notice');
        }

        if ($newsletter_past_date > $activation_time && !$stop_newsletter_reminder) {
            add_action('admin_notices', 'EMCS_Admin::newsletter_admin_notice');
        }
    }

    public static  function newsletter_admin_notice()
    {
        global $pagenow;

        if ($pagenow == 'index.php') {
?>
            <div class="notice notice-info is-dismissible emcs-newsletter-notice">
                <div class="sc-wrapper">
                    <div class="sc-container">
                        <div class="row">
                            <div class="col-md-9">
                                <h3>More cool features coming to <span class="emcs-primary-color">Embed Calendly</span> soon!</h3>
                                <p>Be among the first to get notified.</p>
                                <link href="//cdn-images.mailchimp.com/embedcode/horizontal-slim-10_7.css" rel="stylesheet" type="text/css">
                                <div id="emcs_embed_signup">
                                    <form action="https://embedcalendly.us6.list-manage.com/subscribe/post?u=91af9e1caa59d5bcf7df9e9ba&amp;id=a81b8045ef" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                                        <div id="mc_embed_signup_scroll">
                                            <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="Email" required>
                                            <input type="submit" value="Get notified!" name="subscribe" id="mc-embedded-subscribe" class="emcs-subscribe-btn">
                                            <a href="?emcs_dismiss_notice=2" class="emcs-dismiss-btn">Dismiss</a>
                                            <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_91af9e1caa59d5bcf7df9e9ba_a81b8045ef" tabindex="-1" value=""></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-3 emcs-hide-col">
                                <img src="<?php echo esc_url(EMCS_URL . 'assets/img/emc.svg') ?>" alt="embed calendly logo" width="100px" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
    }

    public static  function rating_admin_notice()
    {
        global $pagenow;

        if ($pagenow == 'index.php') {
        ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    Did you find <strong>Embed Calendly</strong> useful? Kindly rate it
                    <span class="dashicons dashicons-star-filled emcs-dashicon emcs-dashicon-rating"></span>
                    <span class="dashicons dashicons-star-filled emcs-dashicon emcs-dashicon-rating"></span>
                    <span class="dashicons dashicons-star-filled emcs-dashicon emcs-dashicon-rating"></span>
                    <span class="dashicons dashicons-star-filled emcs-dashicon emcs-dashicon-rating"></span>
                    <span class="dashicons dashicons-star-filled emcs-dashicon emcs-dashicon-rating"></span>
                    on WordPress.org if you did!
                    <a href="https://wordpress.org/support/plugin/embed-calendly-scheduling/reviews/#new-post" target="_blank">Click here to submit review.</a>
                    Already done?
                    <a href="?emcs_dismiss_notice=1">Dismiss</a>
                </p>
            </div>
<?php
        }
    }

    public static function dismiss_notice_listener()
    {
        if (isset($_REQUEST['emcs_dismiss_notice'])) {
            if (!empty($_REQUEST['emcs_dismiss_notice'])) {

                if ($_REQUEST['emcs_dismiss_notice'] == 1) {
                    update_option('emcs_stop_review_notice', 1);
                } else {
                    update_option('emcs_stop_newsletter_notice', 1);
                }
            }
        }
    }

    public static function on_activation()
    {
        add_option('emcs_activation_time', strtotime('now'));
        add_option('emcs_stop_review_notice', 0);
        add_option('emcs_stop_newsletter_notice', 0);
        add_option('emcs_display_greeting', 1);
        add_option('emcs_encryption_key', bin2hex(openssl_random_pseudo_bytes(10)));

        require_once(EMCS_EVENT_TYPES . 'event-types.php');
        EMCS_Event_Types::create_emcs_event_types_table();
    }
}
