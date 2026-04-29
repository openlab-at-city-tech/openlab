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
                __('Booking Growth Tools', 'embed-calendly-scheduling'),
                __('Booking Growth Tools', 'embed-calendly-scheduling'),
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
            <h1><?php esc_html_e('Unlock Booking Growth Tools with EMC Pro', 'embed-calendly-scheduling'); ?></h1>
            <h4>
                <i><?php esc_html_e('EMC Pro gives you tools to optimize your booking flow and understand what works.', 'embed-calendly-scheduling'); ?></i>
            </h4>
            <ul>
                <li>
                    <?php
                    printf(
                        /* translators: %1$s opens a strong tag, %2$s closes a strong tag, %3$s add line breaks */
                        esc_html__('%1$sIncrease bookings with smarter scheduling pages%2$s%3$s Show limited availability indicator (e.g. “Only 2 slots left”) to create urgency and encourage visitors to book sooner.', 'embed-calendly-scheduling'),
                        '<strong>',
                        '</strong>',
                        '<br><br>'
                    );
                    ?>
                </li>


                <li>
                    <?php
                    printf(
                        /* translators: %1$s opens a strong tag, %2$s closes a strong tag, %3$s add line breaks */
                        esc_html__('%1$sTrack what actually leads to booked calls%2$s%3$s The built-in analytics dashboard shows which pages and scheduling widgets generate the most bookings, helping you improve what works.', 'embed-calendly-scheduling'),
                        '<strong>',
                        '</strong>',
                        '<br><br>'
                    );
                    ?>
                </li>

                <li>
                    <?php
                    printf(
                        /* translators: %1$s opens a strong tag, %2$s closes a strong tag, %3$s add line breaks */
                        esc_html__('%1$sGuide clients after they book%2$s%3$s Automatically redirect users after scheduling to thank-you pages, onboarding steps, upsells, or additional offers.', 'embed-calendly-scheduling'),
                        '<strong>',
                        '</strong>',
                        '<br><br>'
                    );
                    ?>
                </li>
                <li>
                    <?php
                    printf(
                        /* translators: %1$s opens a strong tag, %2$s closes a strong tag, %3$s add line breaks */
                        esc_html__('%1$sTurn purchases into scheduled calls%2$s%3$s With WooCommerce integration, customers can book their meeting immediately after purchasing a product or service.', 'embed-calendly-scheduling'),
                        '<strong>',
                        '</strong>',
                        '<br><br>'
                    );
                    ?>
                </li>
                <li>
                    <?php
                    printf(
                        /* translators: %1$s opens a strong tag, %2$s closes a strong tag, %3$s add line breaks */
                        esc_html__('%1$sTrack marketing campaigns%2$s%3$s Pass common UTM parameters like utm_source, utm_medium, etc to Calendly & Integrate booking data with your CRM.', 'embed-calendly-scheduling'),
                        '<strong>',
                        '</strong>',
                        '<br><br>'
                    );
                    ?>
                </li>
                <li>
                    <?php
                    printf(
                        /* translators: %1$s opens a strong tag, %2$s closes a strong tag, %3$s add line breaks */
                        esc_html__('%1$sPriority support when it matters%2$s%3$s Get fast assistance when your scheduling system is part of your business workflow.', 'embed-calendly-scheduling'),
                        '<strong>',
                        '</strong>',
                        '<br><br>'
                    );
                    ?>
                </li>
            </ul>
            <a href="https://simpma.com/emc/grow/" class="button-primary" target="_blank"><?php esc_html_e('See How It Works', 'embed-calendly-scheduling'); ?></a>
            <br>
            <h3>
                Users often upgrade after realizing their scheduling page can do more than collect bookings — <br><i>It can help optimize how visitors become clients.</i>
            </h3>
        </div>
        <?php
    }

    /**
     * Handles the dismiss promotion button
     */
    private static function promotion_actions_listener()
    {

        if (isset($_GET[self::STOP_PROMOTIONS_OPTION])) {

            if (
                isset($_GET['_wpnonce']) &&
                wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'emcs_dismiss_notice')
            ) {

                self::disable_all_promotions();

                wp_safe_redirect(admin_url());
                exit;
            }
        }

        if (isset($_GET[self::UI_REBRAND_NOTICE_OPTION])) {

            if (
                isset($_GET['_wpnonce']) &&
                wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'emcs_dismiss_rebrand_notice')
            ) {

                self::disable_ui_rebrand_notice();

                wp_safe_redirect(admin_url());
                exit;
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

                $dimiss_notice_url = wp_nonce_url(admin_url('?' . self::UI_REBRAND_NOTICE_OPTION . '=1'), 'emcs_dismiss_rebrand_notice');
        ?>
                <div class="notice notice-warning is-dismissible emcs-rebrand-notice">
                    <p><strong>EMC Scheduling Manager</strong> <?php esc_html_e('rebranded! Check out the new look', 'embed-calendly-scheduling'); ?>! >> <a href="<?php echo esc_attr(admin_url('?page=emcs-event-types')); ?>"><?php esc_html_e('Go to plugin page', 'embed-calendly-scheduling') ?></a></p>
                    <a href="<?php echo esc_url($dimiss_notice_url) ?>" class=""><?php esc_html_e("Don't show again.", 'embed-calendly-scheduling'); ?></a>
                </div>
            <?php
            }
        }
    }

    private static function get_promotions()
    {
        add_action('admin_notices', 'EMCS_Promotions::get_current_promotion', 11);
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
            return self::pro_version_promotion();
        }
    }

    public static function pro_version_promotion()
    {

        global $pagenow;

        if ($pagenow == 'index.php') {

            $dimiss_notice_url = wp_nonce_url(admin_url('?' . self::STOP_PROMOTIONS_OPTION . '=1'), 'emcs_dismiss_notice');

            ?>
            <div class="notice notice-info is-dismissible emcs-promotion-notice">
                <div class="emcs-row">
                    <div class="emcs-col">
                        <h2>
                            <strong>
                                <?php echo esc_html__('Make Your Booking Page Work Harder for Your Business', 'embed-calendly-scheduling'); ?>
                            </strong>
                        </h2>
                        <h3>
                            <?php
                            printf(
                                /* translators: %1$s opens a strong tag, %2$s closes a strong tag */
                                esc_html__('Improve booking completion with limited availability indicator, scheduling insights, 
                                %1$sand smarter booking flows designed to increase conversion rate.', 'embed-calendly-scheduling'),
                                '<br>'
                            );
                            ?>
                        </h3>
                        <div>
                            <a href="https://simpma.com/emc/grow/" class="button-primary" target="_blank"><?php esc_html_e('View Pro Features', 'embed-calendly-scheduling'); ?></a>
                            <a href="<?php echo esc_url($dimiss_notice_url); ?>" class=""><?php esc_html_e("Don't show again.", 'embed-calendly-scheduling'); ?></a>
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

            $dimiss_notice_url = wp_nonce_url(admin_url('?' . self::STOP_PROMOTIONS_OPTION . '=1'), 'emcs_dismiss_notice');
        ?>
            <div class="notice notice-warning is-dismissible emcs-rebrand-notice">
                <div class="emcs-row">
                    <div class="emcs-col">
                        <p>
                            <strong><?php esc_html_e('Your Booking Page Could Be Generating More Clients', 'embed-calendly-scheduling'); ?></strong><br>
                        </p>
                        <p>
                            <?php
                            /* translators: %1$s adds a new line tag */
                            printf(esc_html__('Uncover opportunities to increase completed bookings and upsell%1$s additional services directly from your scheduling flow.', 'embed-calendly-scheduling'), '<br>'); ?>
                            <br><br>
                            <a href="https://simpma.com/emc/grow/" target="_blank"><?php esc_html_e('Discover How >>', 'embed-calendly-scheduling') ?></a>
                        </p>
                    </div>
                    <div class="emcs-col emcs-hide-col">
                        <img src="<?php echo esc_url(EMCS_URL . 'assets/img/emc.svg') ?>" alt="<?php esc_attr_e('emc logo', 'embed-calendly-scheduling'); ?>" width="100px" /><br>
                        <a href="<?php echo esc_url($dimiss_notice_url); ?>" class="emcs-dismiss-btn"><?php esc_html_e("Don't show again.", 'embed-calendly-scheduling'); ?></a>
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
