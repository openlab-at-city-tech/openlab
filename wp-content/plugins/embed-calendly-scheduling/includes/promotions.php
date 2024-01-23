<?php

defined('ABSPATH') || exit;

class EMCS_Promotions
{
    private const PROMOTION_OPTION = 'emcs_promotion';
    private const STOP_PROMOTIONS_OPTION = 'emcs_stop_promotions';
    private const PROMOTION_DELAY_OPTION = 'emcs_promotion_delay';
    private const LAST_DISPLAYED_PROMOTION = 'emcs_promotion_last_displayed';
    private static $show_promotions;

    public static function init()
    {
        self::$show_promotions = apply_filters('emcs_promotions', true);

        if (!get_option(self::PROMOTION_OPTION)) {

            add_option(self::PROMOTION_OPTION, strtotime('now'));
            add_option(self::STOP_PROMOTIONS_OPTION, 0);
            add_option(self::PROMOTION_DELAY_OPTION, strtotime('now'));
            add_option(self::LAST_DISPLAYED_PROMOTION, 0);
        }

        wp_enqueue_style('emcs_calendly_css');
        wp_enqueue_script('emcs_calendly_js');

        self::display_promotions();
        self::promotion_actions_listener();
    }

    public static function init_menu()
    {
        self::$show_promotions = apply_filters('emcs_promotions', true);

        if (self::$show_promotions) {

            add_submenu_page(
                'emcs-event-types',
                __('Embed Calendly Pro License', 'embed-calendly-scheduling'),
                __('Premium', 'embed-calendly-scheduling'),
                'manage_options',
                'emcs-licenses',
                'EMCS_Promotions::pro_license_page'
            );
        }
    }

    public static function pro_license_page()
    {
?>
        <div class="emcs-pro-promotion-page">
            <h1>Unlock More Features With Embed Calendly Pro</h1>
            <ul>
                <li>
                    <strong>Track your calendar conversion</strong> - Understand how visitors interact with your booking page & calendar.
                </li>
                <li>
                    <strong>View all upcoming events in WordPress</strong> - See all booked events without leaving your WordPress website.
                </li>
                <li>
                    <strong>24/7 Premium Support</strong> - Gain access to our priority customer support.
                </li>
            </ul>
            <h4><i>- With 14 days money back guarantee. No questions asked.</i></h4>
            <a href="https://embedcalendly.com/pricing" class="button-primary" target="_blank">Get Embed Calendly Pro</a>
        </div>
        <?php
    }

    /**
     * Handles the dismiss promotion button
     */
    private static function promotion_actions_listener()
    {
        if (isset($_REQUEST[self::STOP_PROMOTIONS_OPTION])) {

            if ($_REQUEST[self::STOP_PROMOTIONS_OPTION]) {
                self::disable_all_promotions();
                wp_redirect(admin_url());
            }
        }
    }

    public static function disable_all_promotions()
    {
        update_option(self::STOP_PROMOTIONS_OPTION, 1);
    }

    private static function display_promotions()
    {
        wp_enqueue_style('emcs_style');

        $promotions_activation = get_option(self::PROMOTION_OPTION);
        $promotions_disabled = get_option(self::STOP_PROMOTIONS_OPTION);

        // never display any promotions at all if user has opted out before
        if (
            self::$show_promotions && !$promotions_disabled && !self::past_promotion_disabled()
            && self::is_more_than_3days_ago($promotions_activation) // or activation time
        ) {

            self::get_promotions();
        }
    }

    private static function get_promotions()
    {
        add_action('admin_notices', 'EMCS_Promotions::get_current_promotion');
    }

    /**
     * Get promotion notice UI based on current promotion ID
     */
    public static function get_current_promotion()
    {
        $current_promotion_id = self::get_current_promotion_id();

        if ($current_promotion_id == 2) {
            return self::email_list_promotion();
        } else {
            return self::optimization_promotion();
        }
    }

    public static function optimization_promotion()
    {

        global $pagenow;

        if ($pagenow == 'index.php') {
        ?>
            <div class="notice notice-info is-dismissible emcs-promotion-notice">
                <div class="emcs-row">
                    <div class="emcs-col">
                        <h2>Turn Your Website Into <strong>A Lead Generation Tool</strong></h2>
                        <h3>Optimize your website to <strong><u>book more calls</u></strong> and <strong><u>land more clients</u></strong></h3>
                        <div>
                            <a href="https://embedcalendly.com/promotion" class="button-primary" target="_blank">See how >></a>
                            <a href="?<?php echo self::STOP_PROMOTIONS_OPTION; ?>=1" class="">Don't show again.</a>
                        </div>
                    </div>
                    <div class="emcs-col emcs-hide-col">
                        <img src="<?php echo esc_url(EMCS_URL . 'assets/img/emc.svg') ?>" alt="embed calendly logo" width="100px" />
                    </div>
                </div>
            </div>

        <?php
        }
    }

    public static function email_list_promotion()
    {
        global $pagenow;

        if ($pagenow == 'index.php') {
        ?>
            <div class="notice notice-info is-dismissible emcs-newsletter-notice">
                <div class="emcs-row">
                    <div class="emcs-col">
                        <h3>More cool features coming to <span class="emcs-primary-color">Embed Calendly</span> soon!</h3>
                        <p>Be among the first to get notified.</p>
                        <link href="//cdn-images.mailchimp.com/embedcode/horizontal-slim-10_7.css" rel="stylesheet" type="text/css">
                        <div id="emcs_embed_signup">
                            <form action="https://embedcalendly.us6.list-manage.com/subscribe/post?u=91af9e1caa59d5bcf7df9e9ba&amp;id=a81b8045ef" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                                <div id="mc_embed_signup_scroll">
                                    <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="Email" required>
                                    <input type="submit" value="Get notified!" name="subscribe" id="mc-embedded-subscribe" class="button-primary">
                                    <a href="?<?php echo self::STOP_PROMOTIONS_OPTION; ?>=1" class="emcs-dismiss-btn">Don't show again.</a>
                                    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_91af9e1caa59d5bcf7df9e9ba_a81b8045ef" tabindex="-1" value=""></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="emcs-col emcs-hide-col">
                        <img src="<?php echo esc_url(EMCS_URL . 'assets/img/emc.svg') ?>" alt="embed calendly logo" width="100px" />
                    </div>
                </div>
            </div>
<?php
        }
    }

    /**
     * Get the ID of the current promotion notice to display
     */
    private static function get_current_promotion_id()
    {
        $promotion_id = 1;
        $last_promotion_id = get_option(self::LAST_DISPLAYED_PROMOTION);
        $current_promotion_delay = get_option(self::PROMOTION_DELAY_OPTION);

        if ($last_promotion_id || $last_promotion_id == 0) {

            if (self::is_more_than_3days_ago($current_promotion_delay)) {

                if ($last_promotion_id < 2) {

                    $promotion_id = $last_promotion_id + 1;
                    self::update_promotion_delay($promotion_id);;
                } else {
                    self::update_promotion_delay(1);
                }
            } else {

                return $last_promotion_id;
            }
        }

        return $promotion_id;
    }

    private static function update_promotion_delay($promotion_id)
    {
        update_option(self::LAST_DISPLAYED_PROMOTION, $promotion_id);
        update_option(self::PROMOTION_DELAY_OPTION, strtotime('now'));
    }

    /**
     * Checks if a timestamp is from 3 days ago
     */
    private static function is_more_than_3days_ago($timestamp)
    {
        $current_time = time();
        $three_days_in_seconds = 3 * 24 * 60 * 60; // 3 days * 24 hours * 60 minutes * 60 seconds
        return ($current_time - $timestamp) > $three_days_in_seconds;
    }

    /**
     * Checks if user has previously opted out of any
     * promotion notice before
     */
    private static function past_promotion_disabled()
    {

        $past_promotion_options = [
            'emcs_stop_review_notice',
            'emcs_stop_newsletter_notice',
            'emcs_stop_promotion_one',
            'emcs_stop_promotion_two'
        ];

        foreach ($past_promotion_options as $promotion_option) {

            $option = get_option($promotion_option);

            if ($option) {
                return true;
            }
        }

        return false;
    }
}
