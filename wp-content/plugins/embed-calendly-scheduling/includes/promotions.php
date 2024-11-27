<?php

defined('ABSPATH') || exit;

class EMCS_Promotions
{
    private const PROMOTION_OPTION = 'emcs_promotion';
    private const UI_REBRAND_NOTICE_OPTION = 'emcs_ui_rebrand_notice';
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
            add_option(self::UI_REBRAND_NOTICE_OPTION, 0);
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
                __('EMC Pro License', 'embed-calendly-scheduling'),
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
            <h1><?php esc_html_e('Unlock More Features With EMC Pro', 'embed-calendly-scheduling'); ?></h1>
            <ul>
                <li>
                    <?php printf(esc_html__('%1$sTrack your calendar conversion%2$s - Understand how visitors interact with your booking page & calendar.', 'embed-calendly-scheduling'), '<strong>', '</strong>'); ?>
                </li>
                <li>
                    <?php printf(esc_html__('%1$sReduce no show rate with reminders%2$s - Easily send both automated and manual reminders.', 'embed-calendly-scheduling'), '<strong>', '</strong>'); ?>
                </li>
                <li>
                    <?php printf(esc_html__('%1$sManage upcoming meetings in WordPress%2$s - View booked meetings and cancel them directly from WordPress.', 'embed-calendly-scheduling'), '<strong>', '</strong>'); ?>
                </li>
                <li>
                    <?php printf(esc_html__('%1$sView and backup your contacts%2$s - It\'s now easier than ever to export all your contacts!', 'embed-calendly-scheduling'), '<strong>', '</strong>'); ?>
                </li>
                <li>
                    <?php printf(esc_html__('%1$s24/7 Premium Support%2$s - Gain access to our priority customer support.', 'embed-calendly-scheduling'), '<strong>', '</strong>'); ?>
                </li>
            </ul>
            <h4><i><?php esc_html_e('- With 14 days money back guarantee. No questions asked.', 'embed-calendly-scheduling'); ?></i></h4>
            <a href="https://simpma.com/emc" class="button-primary" target="_blank"><?php esc_html_e('Get EMC Pro', 'embed-calendly-scheduling'); ?></a>
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

        if (isset($_REQUEST[self::UI_REBRAND_NOTICE_OPTION])) {

            if ($_REQUEST[self::UI_REBRAND_NOTICE_OPTION]) {
                self::disable_ui_rebrand_notice();
                wp_redirect(admin_url());
            }
        }
    }

    public static function disable_all_promotions()
    {
        update_option(self::STOP_PROMOTIONS_OPTION, 1);
    }

    public static function disable_ui_rebrand_notice()
    {
        update_option(self::UI_REBRAND_NOTICE_OPTION, 1);
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

    public static function ui_rebrand_notice()
    {
        global $pagenow;

        $base_date = '2024-11-05';
        $days_after = 30;
        $start_date = strtotime($base_date);
        $end_date = strtotime("+$days_after days", $start_date);
        $current_date = current_time('timestamp');

        if ($pagenow == 'index.php') {

            // Check if the notice is within the 30 days period and if it hasn't been dismissed
            if ($current_date >= $start_date && $current_date <= $end_date && !get_option(self::UI_REBRAND_NOTICE_OPTION, 0)) {
        ?>
                <div class="notice notice-warning is-dismissible emcs-rebrand-notice">
                    <p><strong>Embed Calendly</strong> <?php esc_html_e('rebranded! Check out the new look', 'embed-calendly-scheduling'); ?>! >> <a href="<?php echo esc_attr(admin_url('?page=emcs-event-types')); ?>"><?php esc_html_e('Go to plugin page', 'embed-calendly-scheduling') ?></a></p>
                    <a href="?<?php echo self::UI_REBRAND_NOTICE_OPTION; ?>=1" class=""><?php esc_html_e("Don't show again.", 'embed-calendly-scheduling'); ?></a>
                </div>
            <?php
            }
        }
    }

    private static function get_promotions()
    {
        add_action('admin_notices', 'EMCS_Promotions::get_current_promotion', 11);
        add_action('admin_notices', 'EMCS_Promotions::ui_rebrand_notice', 10);
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
            return self::email_list_promotion();
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
                        <h2><?php printf(esc_html__('Turn Your Website Into %1$sA Lead Generation Tool%2$s', 'embed-calendly-scheduling'), '<strong>', '</strong>'); ?></h2>
                        <h3><?php printf(esc_html__('Optimize your website to %1$sbook more calls%2$s and %1$sland more clients%2$s', 'embed-calendly-scheduling'), '<strong><u>', '</strong></u>'); ?></h3>
                        <div>
                            <a href="https://simpma.com/promotion" class="button-primary" target="_blank"><?php esc_html_e('See how >>', 'embed-calendly-scheduling'); ?></a>
                            <a href="?<?php echo self::STOP_PROMOTIONS_OPTION; ?>=1" class=""><?php esc_html_e("Don't show again.", 'embed-calendly-scheduling'); ?></a>
                        </div>
                    </div>
                    <div class="emcs-col emcs-hide-col">
                        <img src="<?php echo esc_url(EMCS_URL . 'assets/img/emc.svg') ?>" alt="<?php esc_attr_e('emc logo', 'embed-calendly-scheduling'); ?>" width="100px" />
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
                        <h3><?php printf(esc_html__('More cool features coming to %1$sEMC Scheduling Manager%2$s soon!', 'embed-calendly-scheduling'), '<span class="emcs-primary-color">', '</span>'); ?></h3>
                        <p><?php esc_html_e('Be among the first to get notified.', 'embed-calendly-scheduling'); ?></p>
                        <link href="//cdn-images.mailchimp.com/embedcode/horizontal-slim-10_7.css" rel="stylesheet" type="text/css">
                        <div id="emcs_embed_signup">
                            <form action="https://embedcalendly.us6.list-manage.com/subscribe/post?u=91af9e1caa59d5bcf7df9e9ba&amp;id=a81b8045ef" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                                <div id="mc_embed_signup_scroll">
                                    <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="<?php esc_html_e('Email', 'embed-calendly-scheduling'); ?>" required>
                                    <input type="submit" value="<?php esc_html_e('Get notified!', 'embed-calendly-scheduling'); ?>" name="subscribe" id="mc-embedded-subscribe" class="button-primary">
                                    <a href="?<?php echo self::STOP_PROMOTIONS_OPTION; ?>=1" class="emcs-dismiss-btn"><?php esc_html_e("Don't show again.", 'embed-calendly-scheduling'); ?></a>
                                    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_91af9e1caa59d5bcf7df9e9ba_a81b8045ef" tabindex="-1" value=""></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="emcs-col emcs-hide-col">
                        <img src="<?php echo esc_url(EMCS_URL . 'assets/img/emc.svg') ?>" alt="<?php esc_attr_e('emc logo', 'embed-calendly-scheduling'); ?>" width="100px" />
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
