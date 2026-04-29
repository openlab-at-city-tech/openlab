<?php

if (!defined('ABSPATH')) {
    die('No direct access.');
}

if (!class_exists('Updraft_Notices_1_0')) {
    require_once(METASLIDER_PATH . 'admin/lib/Updraft_Notices.php');
}

/**
 * Meta Slider Notices
 */
class MetaSlider_Notices extends Updraft_Notices_1_0
{
    /**
     * All Ads
     *
     * @var object $ads
     */
    protected $ads;

    /**
     * Notices content
     *
     * @var object $notices_content
     */
    protected $notices_content;

    /**
     * Populates ad content and loads assets
     */
    public function __construct()
    {
        /*
         * There are three options you can use to force ads to show.
         * The second two require the first to be set to true
         *
         * define('METASLIDER_FORCE_NOTICES', true);
         *
         * Be sure not to set both of these at the same time
         * define('METASLIDER_FORCE_LITE_NOTICES', true);
         * define('METASLIDER_FORCE_PRO_NOTICES', true);
         *
         */

        $this->ads = $this->lite_notices();

        // To avoid showing the user ads off the start, lets wait
        $this->notices_content = ($this->ad_delay_has_finished()) ? $this->ads : array();

        add_action('admin_enqueue_scripts', array($this, 'add_notice_assets'));
        add_action('wp_ajax_notice_handler', array($this, 'ajax_notice_handler'));
        // Notices in admin pages except in MetaSlider admin pages - See MetaSliderPlugin->filter_admin_notices()
        add_action('admin_notices', array($this, 'show_dashboard_notices'));
        // @since 3.90.1 - Notices in MetaSlider admin pages
        add_action('metaslider_admin_notices', array($this, 'show_dashboard_notices'));
        // @since 3.106 - Ads in quickstart page
        add_action('metaslider_quickstart_ads', array($this, 'quickstart_ads'));
        add_action('wp_ajax_quickstart_ads', array($this, 'ajax_quickstart_ads'));
    }

    /**
     * Handles assets for the notices
     */
    public function add_notice_assets()
    {
        wp_enqueue_style('ml-slider-notices-css', METASLIDER_ADMIN_URL . 'assets/css/notices.css', false, METASLIDER_ASSETS_VERSION);
        wp_register_script('metaslider-notices-extra-js', '');
        wp_enqueue_script('metaslider-notices-extra-js');
        $nonce = wp_create_nonce('metaslider_handle_notices_nonce');
        $this->wp_add_inline_script(
            'metaslider-notices-extra-js',
            "window.metaslider_notices_handle_notices_nonce = '{$nonce}'"
        );
    }

    /**
     * Deprecated for MetaSlider for now
     */
    public function notices_init()
    {
        return;
    }

    /**
     * Returns notices that free/lite users should see. dismiss_time should match the key
     * hide_time is in weeks. Use a string to hide for 9999 weeks.
     *
     * @return array returns an array of notices
     */
    protected function lite_notices()
    {
        if (defined('METASLIDER_FORCE_PRO_NOTICES') && METASLIDER_FORCE_PRO_NOTICES) {
            // Override to force pro, but make sure both overrides arent set
            return (!defined('METASLIDER_FORCE_LITE_NOTICES')) ? $this->pro_notices() : array();
        }

        $ads = [
            'rate_plugin' => [
                'title' => _x('Like MetaSlider? Please help us by giving a positive review at WordPress.org', 'Keep the plugin name "MetaSlider" when possible', 'ml-slider'),
                'text' => '',
                'image' => 'notices/metaslider_logo.png',
                'button_link' => 'metaslider_rate',
                'button_meta' => 'review',
                'dismiss_time' => 'rate_plugin',
                'hide_time' => 12,
                'supported_positions' => ['header'],
            ],
            'pro_layers' => [
                'title' => __('Spice up your site with animated layers and video slides with MetaSlider Pro', 'ml-slider'),
                'text' => '',
                'image' => 'notices/metaslider_logo.png',
                'button_link' => 'metaslider',
                'button_meta' => 'buy-w-discount',
                'dismiss_time' => 'pro_layers',
                'hide_time' => 12,
                'supported_positions' => ['header'],
                'validity_function' => 'metaslider_pro_is_not_installed',
            ],
            'pro_features' => [
                'title' => __('Increase your revenue and conversion with video slides and many more MetaSlider Pro features', 'ml-slider'),
                'text' => '',
                'image' => 'notices/metaslider_logo.png',
                'button_link' => 'metaslider',
                'button_meta' => 'buy-w-discount',
                'dismiss_time' => 'pro_features',
                'hide_time' => 12,
                'supported_positions' => ['header'],
                'validity_function' => 'metaslider_pro_is_not_installed',
            ],
            'translation' => [
                'title' => __('Can you translate? Want to improve MetaSlider for speakers of your language?', 'ml-slider'),
                'text' => '',
                'image' => 'notices/metaslider_logo.png',
                'button_link' => 'metaslider_translate',
                'button_meta' => 'lets_start',
                'dismiss_time' => 'translation',
                'hide_time' => 12,
                'supported_positions' => ['header'],
                'validity_function' => 'translation_needed',
            ],
        ];

        if ( metaslider_plugin_is_installed( 'ml-slider-lightbox' ) === false ) {
            $ads = array_merge( $ads, [
                'install_lightbox' => [
                    'title' => __('Do you want to display your slideshow media inside a lightbox?', 'ml-slider'),
                    'text' => '',
                    'image' => 'notices/metaslider_logo.png',
                    'button_link' => 'metaslider_lightbox',
                    'button_meta' => 'install-lightbox',
                    'dismiss_time' => 'install_lightbox',
                    'hide_time' => 12,
                    'supported_positions' => ['header'],
                ],
            ] );
        }

        return $ads;
    }

    /**
     * Premium user notices, if any.
     *
     * @return array
     */
    protected function pro_notices()
    {
        if (defined('METASLIDER_FORCE_LITE_NOTICES') && METASLIDER_FORCE_LITE_NOTICES) {
            // Override to force pro, but make sure both overrides arent set
            return (!defined('METASLIDER_FORCE_PRO_NOTICES')) ? $this->lite_notices() : [];
        }

        return [];
    }

    /**
     * Add fields needed for an notice to show
     *
     * @param string $notice - the name of the notice
     * @return array
     */
    public function prepare_notice_fields($notice)
    {
        if (!isset($notice['dismiss_time']) && isset($notice['discount_code'])) {
            $notice['dismiss_time'] = $notice['discount_code'];
        }
        return $notice;
    }

    /**
     * Checks if MetaSlider Pro is NOT installed
     *
     * @return bool
     */
    protected function metaslider_pro_is_not_installed()
    {
        return ! metaslider_pro_is_installed();
    }

    /**
     * Checks if the user agent isn't set as en_GB or en_US, and if the language file doesn't exist
     *
     * @param  string $plugin_base_dir The plguin base directory
     * @param  string $product_name    Product name
     * @return bool
     */
    protected function translation_needed($plugin_base_dir = '', $product_name = '')
    {
        return parent::translation_needed(METASLIDER_PATH, 'ml-slider');
    }

    /**
     * This method checks to see if the ad has been dismissed
     *
     * @param string $ad_identifier - identifier for the ad
     * @return bool returns true when we dont want to show the ad
     */
    protected function check_notice_dismissed($ad_identifier)
    {
        if ($this->force_ads()) {
            return false;
        }
        return (time() < get_option("ms_hide_{$ad_identifier}_ads_until"));
    }

    /**
     * Checks whether this is an ad page - hard-coded
     *
     * @return bool
     */
    protected function is_page_with_ads()
    {
        global $pagenow;
        $page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';

        // I'm thinking to limit the check to the actual settings page for now
        // This way, if they activate the plugin but don't start using it until
        // a few weeks after, it won't bother them with ads.
        // return ('index.php' === $pagenow) || ($page === 'metaslider');
        return ($page === 'metaslider');
    }

    /**
     * This method checks to see if the ad waiting period is over (2 weeks)
     * If not, it will set a two week time
     *
     * @return bool returns true when we dont want to show the ad
     */
    protected function ad_delay_has_finished()
    {
        // The delay could be empty, ~2 weeks (initial delay) or ~12 weeks
        $delay = get_option("ms_hide_all_ads_until");

        if ($this->force_ads()) {
            // If there's an override, return true
            return true;
        }

        if (!$this->is_page_with_ads() && !$delay) {
            // Only start the timer if they see a page that can serve ads
            return false;
        }

        if (!$delay) {
            // Set the delay for when they will first see an ad, 2 weeks; returns false
            return !update_option("ms_hide_all_ads_until", time() + 2 * 7 * 86400);
        } elseif ((time() > $delay) && !get_option("ms_ads_first_seen_on")) {
            // Serve ads now, and note the time they first saw ads
            update_option("ms_ads_first_seen_on", time());

            // Now that they can see ads, make sure the rate_plugin is shown first.
            // Since this shows after 2 weeks, it's better timing.
            $notices = $this->lite_notices();
            $this->ads = array('rate_plugin' => $notices['rate_plugin']);
            return true;
        } elseif (time() < $delay) {
            // This means an ad was dismissed and there's a delay
            return false;
        } elseif (get_option("ms_ads_first_seen_on")) {
            // This means the initial delay has elapsed,
            // and the dismissed period expired
            return true;
        }

        if (metaslider_pro_is_installed()) {
            // If they are pro don't check anything but show the pro ad.
            return true;
        }

        // Default to not show an ad, in case there's some error
        return false;
    }

    /**
     * Method to handle dashboard notices
     */
    public function show_dashboard_notices()
    {
        $current_page = get_current_screen();
        if ('dashboard' === $current_page->base && metaslider_user_is_ready_for_notices()) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->do_notice(false, 'dashboard', true);
        }
    }

    /**
     * Selects the template and returns or displays the notice
     *
     * @param array  $notice_information     - variable names/values to pass through to the template
     * @param bool   $return_instead_of_echo - whether to
     * @param string $position               - where the notice is being displayed
     * @return null|string - depending on the value of $return_instead_of_echo
     */
    protected function render_specified_notice($notice_information, $return_instead_of_echo = false, $position = 'header')
    {
        $views = array(
            'header' => 'header-notice.php',
        );
        $view = isset($views[$position]) ? $views[$position] : 'header-notice.php';
        return $this->include_template($view, $return_instead_of_echo, $notice_information);
    }

    /**
     * Displays or returns the template
     *
     * @param string $path                   file name of the template
     * @param bool   $return_instead_of_echo Return the template instead of printing
     * @param array  $args                   template arguments
     * @return null|string
     */
    public function include_template($path, $return_instead_of_echo = false, $args = array())
    {
        if ( $return_instead_of_echo ) {
            ob_start();
        }

        if ( ! empty( $args['hide_time']) && is_int($args['hide_time'])) {
            $hide_time = $args['hide_time'] . ' ' . __('weeks', 'ml-slider');
        }

        include METASLIDER_PATH . 'admin/views/notices/' . $path;

        if ($return_instead_of_echo) {
            return ob_get_clean();
        }
    }

    /**
     * Builds a link based on the type of notice being requested
     *
     * @param string $link - the URL to link to
     * @param string $type - which notice is being displayed
     * @return string - the resulting HTML
     */
    public function get_button_link($link, $type)
    {
        $messages = array(
            'lets_start' => __('Let\'s Start &rarr;', 'ml-slider'),
            'review' => _x('Review MetaSlider &rarr;', 'Keep the plugin name "MetaSlider" when possible', 'ml-slider'),
            'ml-slider' => __('Find out more &rarr;', 'ml-slider'),
            'buy-w-discount' => __('Get MetaSlider Pro &rarr;', 'ml-slider'),
            'signup' => __('Sign up &rarr;', 'ml-slider'),
            'go_there' => __('Go there &rarr;', 'ml-slider')
        );

        if ( metaslider_plugin_is_installed( 'ml-slider-lightbox' ) === false ) {
            $messages['install-lightbox'] = __('Click to install the MetaSlider Lightbox plugin &rarr;', 'ml-slider');
        }

        $message = isset($messages[$type]) ? $messages[$type] : __('Read more', 'ml-slider');

        return '<a class="updraft_notice_link ml-discount-ad-button" target="_blank" href="' . esc_url($this->get_notice_url($link)) . '">' . esc_html($message) . '</a>';
    }

    /**
     * Handles any notice related ajax calls
     *
     * @return void
     */
    public function ajax_notice_handler()
    {
        if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'metaslider_handle_notices_nonce')) {
            wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                [
                    'message' => __('Access denied. Sorry, you do not have permission to complete this task.', 'ml-slider')
                ],
                403
            );
        }

        if (! isset($_POST['ad_identifier'])) {
            wp_send_json_error(array(
                'message' => __('Bad request', 'ml-slider')
            ), 400);
        }

        $ad_data = $this->ad_exists(sanitize_key($_POST['ad_identifier']));

        if (is_wp_error($ad_data)) {
            wp_send_json_error(array(
                'message' => __('This item does not exist. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        $result = $this->dismiss_ad($ad_data['dismiss_time'], $ad_data['hide_time']);

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message()
            ), 409);
        }

        wp_send_json_success(array(
            'message' => __('The option was successfully updated', 'ml-slider'),
        ), 200);
    }

    /**
     * Returns the available ads that havent been dismissed by the user
     *
     * @param string|array $location     the location for the ad
     * @param boolean      $bypass_delay Bypass the ad delay
     * @return array the identifier for the ad
     */
    public function active_ads($location = 'header', $bypass_delay = false)
    {
        $dismissed_ads = array();

        $ads = ($bypass_delay) ? $this->ads : $this->notices_content;

        // Filter through all site options (cached)
        foreach (wp_load_alloptions() as $key => $value) {
            if (strpos($key, 'ms_hide_') && strpos($key, '_ads_until')) {
                $key = str_replace(array('ms_hide_', '_ads_until'), '', $key);
                $dismissed_ads[$key] = $value;
            }
        }

        // Filter out if the dismiss time has expired, then compare to the database
        $valid_ads = array();
        foreach ($ads as $ad_identifier => $values) {
            $is_valid = isset($values['validity_function'])
                ? (bool)call_user_func([$this, $values['validity_function']]) : true;
            $not_dismissed = !$this->check_notice_dismissed($ad_identifier);
            $is_supported = in_array($location, $values['supported_positions']);

            if ($is_valid && $not_dismissed && $is_supported) {
                $valid_ads[$ad_identifier] = $values;
            }
        }

        return array_diff_key($valid_ads, $dismissed_ads);
    }

    /**
     * Returns all possible ads or the specified identifier
     *
     * @param string|null $ad_identifier Ad Identifier
     * @return string|null the data of the ad
     */
    public function get_ad($ad_identifier = null)
    {
        $all_notices = array_merge($this->pro_notices(), $this->lite_notices());
        return is_null($ad_identifier) ? $all_notices : $all_notices['ad_identifier'];
    }

    /**
     * Checks if the ad identifier exists in any of the ads above
     *
     * @param string $ad_identifier Ad Identifier
     * @return bool the data of the ad
     */
    public function ad_exists($ad_identifier)
    {
        $all_notices = array_merge($this->pro_notices(), $this->lite_notices());
        if (isset($all_notices[$ad_identifier])) {
            return $all_notices[$ad_identifier];
        }

        return new WP_Error('bad_call', __('The requested data does not exist.', 'ml-slider'), array('status' => 401));
    }

    /**
     * Updates the stored value for how long to hide the ads
     *
     * @param string     $ad_identifier Ad Identifier
     * @param int|string $weeks         time in weeks or a string to show
     * @return bool|WP_Error whether the update was a success
     */
    public function dismiss_ad($ad_identifier, $weeks)
    {

        // If the time isn't specified it will hide "forever" (9999 weeks)
        // Update 12/18/2017 - will set this an extra week, so that this individual ad will hide, for example, 13 weeks, while ALL ads will hide for 12 weeks. This ensures that the user doesn't see the same ad twice. Minor detail.
        $weeks = is_int($weeks) ? $weeks + 1 : 9999;

        $result = update_option("ms_hide_{$ad_identifier}_ads_until", time() + $weeks * 7 * 86400);

        // Update 12/18/2017 - Hide all ads for 12 weeks (this used to be 24 hours)
        // This skips over the scenario when a user has seen a seasonal ad within the 2 week grace period. That way we can still show them the "rate plugin" ad after 2 weeks.
        if (get_option("ms_ads_first_seen_on")) {
            update_option("ms_hide_all_ads_until", time() + 12 * 7 * 86400);
        }

        return $result ? $result : new WP_Error('update_failed', __('The attempt to update the option failed.', 'ml-slider'), array('status' => 409));
    }

    /**
     * Returns the url for a notice link
     *
     * @param string $link_id the link to get the url
     * @return string the url for the link id
     */
    public function get_notice_url($link_id)
    {
        $urls = array(
            'metaslider' => apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade'),
            'metaslider_rate' => 'https://wordpress.org/support/plugin/ml-slider/reviews?rate=5#new-post',
            'metaslider_survey' => 'https://www.metaslider.com/survey',
            'metaslider_survey_pro' => 'https://www.metaslider.com/survey-pro',
            'metaslider_translate' => 'https://translate.wordpress.org/projects/wp-plugins/ml-slider',
        );

        if ( metaslider_plugin_is_installed( 'ml-slider-lightbox' ) === false ) {
            $urls['metaslider_lightbox'] = wp_nonce_url(
                self_admin_url(
                    'update.php?action=install-plugin&plugin=ml-slider-lightbox'
                ),
                'install-plugin_ml-slider-lightbox'
            ); 
        }

        // Return the website url if the ID was not set
        if (!isset($urls[$link_id])) {
            return 'https://www.metaslider.com';
        }

        // Return if analytics code is already set
        if (strpos($urls[$link_id], 'utm_source')) {
            return esc_url($urls[$link_id]);
        }

        // Add our analytics code
        return esc_url(add_query_arg(array(
            'utm_source' => 'metaslider-plugin-page',
            'utm_medium' => 'banner'
        ), $urls[$link_id]));
    }

    /**
     * Forces ads to show when any override is set
     */
    private function force_ads()
    {
        return (defined('METASLIDER_FORCE_NOTICES') && METASLIDER_FORCE_NOTICES) ||
            (defined('METASLIDER_FORCE_PRO_NOTICES') && METASLIDER_FORCE_PRO_NOTICES) ||
            (defined('METASLIDER_FORCE_LITE_NOTICES') && METASLIDER_FORCE_LITE_NOTICES);
    }

    /**
     * Polyfill to handle the wp_add_inline_script() function.
     *
     * @param  string $handle   The script identifier
     * @param  string $data     The script to add, without <script> tags
     * @param  string $position Whether to output before or after
     *
     * @return object|bool
     */
    public function wp_add_inline_script($handle, $data, $position = 'after')
    {
        if (function_exists('wp_add_inline_script')) {
            return wp_add_inline_script($handle, $data, $position);
        }
        global $wp_scripts;
        if (!$data) {
            return false;
        }

        // First fetch any existing scripts
        $script = $wp_scripts->get_data($handle, 'data');

        // Append to the end
        $script .= $data;

        return $wp_scripts->add_data($handle, 'data', $script);
    }

    /** 
     * Show thank you ad in quickstart page 
     * 
     * @since 3.106
     */
    public function quickstart_ads()
    {
        // Thank you ad
        $stored_status = get_option( 'metaslider_quickstart_ad_thankyou' );

        if ( ! $stored_status || $stored_status != 'hide' ) {
            ?>
            <div class="ms-quickstart-heading border-orange border rounded-lg mb-8 leading-normal">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512" style="margin: 0 auto; width: 150px; height: auto; transform: translate3d(0px, 0px, 0px); content-visibility: visible;" preserveAspectRatio="xMidYMid meet"><defs><clipPath id="__lottie_element_2"><rect width="512" height="512" x="0" y="0"></rect></clipPath><clipPath id="__lottie_element_4"><path d="M0,0 L512,0 L512,512 L0,512z"></path></clipPath><linearGradient id="__lottie_element_10" spreadMethod="pad" gradientUnits="userSpaceOnUse" x1="-10.78499984741211" y1="43.922000885009766" x2="91.35199737548828" y2="-277.2149963378906"><stop offset="0%" stop-color="rgb(1,202,195)"></stop><stop offset="50%" stop-color="rgb(188,226,167)"></stop><stop offset="100%" stop-color="rgb(169,216,155)"></stop></linearGradient><mask id="__lottie_element_12"><path fill="url(#__lottie_element_11)" d=" M25.812999725341797,-44.79399871826172 C22.854000091552734,-48.834999084472656 20.22599983215332,-52.86199951171875 17.97100067138672,-56.8120002746582 C17.054000854492188,-58.35300064086914 16.090999603271484,-60.12200164794922 14.715999603271484,-62.66699981689453 C14.715999603271484,-62.66699981689453 -25.812999725341797,62.66699981689453 -25.812999725341797,62.66699981689453 C-25.812999725341797,62.66699981689453 3.378999948501587,52.74300003051758 3.378999948501587,52.74300003051758 C3.378999948501587,52.74300003051758 -4.177000045776367,49.909000396728516 -4.177000045776367,49.909000396728516 C-4.177000045776367,49.909000396728516 25.812999725341797,-44.79399871826172 25.812999725341797,-44.79399871826172z"></path></mask><linearGradient id="__lottie_element_11" spreadMethod="pad" gradientUnits="userSpaceOnUse" x1="-10.78499984741211" y1="43.922000885009766" x2="91.35199737548828" y2="-277.2149963378906"><stop stop-color="rgb(255,255,255)" offset="300%" stop-opacity="undefined"></stop></linearGradient></defs><g clip-path="url(#__lottie_element_2)"><g clip-path="url(#__lottie_element_4)" style="display: block;" transform="matrix(1,0,0,1,-2,3.79998779296875)" opacity="1"><g style="display: block;" transform="matrix(0.9499993324279785,0,0,0.9499993324279785,155.30006408691406,184.10006713867188)" opacity="1"><g opacity="1" transform="matrix(1,0,0,1,91.74700164794922,106.25299835205078)"><path stroke-linecap="round" stroke-linejoin="miter" fill-opacity="0" stroke-miterlimit="4" stroke="rgb(41,55,91)" stroke-opacity="1" stroke-width="8" d=" M-48.83000183105469,-29.738000869750977 C-63.42300033569336,15.701000213623047 -81.73999786376953,72.73400115966797 -81.73999786376953,72.73400115966797 C-83.74700164794922,78.95600128173828 -77.84700012207031,84.8270034790039 -71.62799835205078,82.7959976196289 C-71.62799835205078,82.7959976196289 83.74700164794922,30.34000015258789 83.74700164794922,30.34000015258789 M-31.136999130249023,-84.8270034790039 C-31.136999130249023,-84.8270034790039 -39.16699981689453,-59.82400131225586 -48.83000183105469,-29.738000869750977"></path></g><g opacity="0.999999724325497" transform="matrix(1,0,0,1,118.87899780273438,78.18699645996094)"><path fill="rgb(221,105,35)" fill-opacity="1" d=" M59.65299987792969,56.19499969482422 C49.119998931884766,66.72899627685547 14.647000312805176,49.334999084472656 -17.343000411987305,17.3439998626709 C-49.33399963378906,-14.647000312805176 -66.72899627685547,-49.11899948120117 -56.19499969482422,-59.65299987792969 C-45.6609992980957,-70.18699645996094 -11.189000129699707,-52.79199981689453 20.801000595092773,-20.802000045776367 C52.792999267578125,11.189000129699707 70.18699645996094,45.6619987487793 59.65299987792969,56.19499969482422z"></path></g><g opacity="1" transform="matrix(1,0,0,1,120.60800170898438,76.45800018310547)"><path stroke-linecap="round" stroke-linejoin="miter" fill-opacity="0" stroke-miterlimit="4" stroke="rgb(41,55,91)" stroke-opacity="1" stroke-width="8" d=" M-60.99399948120117,-48.56700134277344 C-61.08399963378906,-52.551998138427734 -60.0989990234375,-55.749000549316406 -57.92399978637695,-57.92399978637695 C-47.38999938964844,-68.45800018310547 -12.918000221252441,-51.0629997253418 19.07200050354004,-19.072999954223633 C51.06399917602539,12.918000221252441 68.45800018310547,47.39099884033203 57.92399978637695,57.92399978637695 C57.92399978637695,57.92399978637695 57.92399978637695,57.92399978637695 57.92399978637695,57.92399978637695 C47.39099884033203,68.45800018310547 12.918000221252441,51.06399917602539 -19.07200050354004,19.072999954223633 C-44.45600128173828,-6.310999870300293 -60.6510009765625,-33.25699996948242 -60.99399948120117,-48.56700134277344"></path></g><g opacity="1" transform="matrix(1,0,0,1,47.395999908447266,113.95099639892578)"><path fill="rgb(35,115,168)" fill-opacity="0.9999999963409957" d=" M25.812999725341797,-44.79399871826172 C22.854000091552734,-48.834999084472656 20.22599983215332,-52.86199951171875 17.97100067138672,-56.8120002746582 C17.054000854492188,-58.35300064086914 16.090999603271484,-60.12200164794922 14.715999603271484,-62.66699981689453 C14.715999603271484,-62.66699981689453 -25.812999725341797,62.66699981689453 -25.812999725341797,62.66699981689453 C-25.812999725341797,62.66699981689453 3.378999948501587,52.74300003051758 3.378999948501587,52.74300003051758 C3.378999948501587,52.74300003051758 -4.177000045776367,49.909000396728516 -4.177000045776367,49.909000396728516 C-4.177000045776367,49.909000396728516 25.812999725341797,-44.79399871826172 25.812999725341797,-44.79399871826172z"></path><path fill="url(#__lottie_element_10)" mask="url(#__lottie_element_12)" fill-opacity="0.9999999677852462" d="M0 0 M25.812999725341797,-44.79399871826172 C22.854000091552734,-48.834999084472656 20.22599983215332,-52.86199951171875 17.97100067138672,-56.8120002746582 C17.054000854492188,-58.35300064086914 16.090999603271484,-60.12200164794922 14.715999603271484,-62.66699981689453 C14.715999603271484,-62.66699981689453 -25.812999725341797,62.66699981689453 -25.812999725341797,62.66699981689453 C-25.812999725341797,62.66699981689453 3.378999948501587,52.74300003051758 3.378999948501587,52.74300003051758 C3.378999948501587,52.74300003051758 -4.177000045776367,49.909000396728516 -4.177000045776367,49.909000396728516 C-4.177000045776367,49.909000396728516 25.812999725341797,-44.79399871826172 25.812999725341797,-44.79399871826172z"></path></g></g><g style="display: block;" transform="matrix(1,0,0,1,399.0332946777344,470.33343505859375)" opacity="0.9999999936877157"><g opacity="1" transform="matrix(1,0,0,1,-41.95199966430664,-163.45199584960938)"><path fill="rgb(0,111,185)" fill-opacity="1" d=" M0,-7 C3.863300085067749,-7 7,-3.863300085067749 7,0 C7,3.863300085067749 3.863300085067749,7 0,7 C-3.863300085067749,7 -7,3.863300085067749 -7,0 C-7,-3.863300085067749 -3.863300085067749,-7 0,-7z"></path></g></g><g style="display: none;" transform="matrix(1,0,0,1,439.23236083984375,434.5927429199219)" opacity="0.0006650611908483484"><g opacity="1" transform="matrix(1,0,0,1,-41.95199966430664,-163.45199584960938)"><path fill="rgb(35,115,168)" fill-opacity="1" d=" M0,-7 C3.863300085067749,-7 7,-3.863300085067749 7,0 C7,3.863300085067749 3.863300085067749,7 0,7 C-3.863300085067749,7 -7,3.863300085067749 -7,0 C-7,-3.863300085067749 -3.863300085067749,-7 0,-7z"></path></g></g><g style="display: block;" transform="matrix(0.9999999403953552,0,0,0.9999999403953552,355.4018249511719,374.9208984375)" opacity="0.9999998634084015"><g opacity="1" transform="matrix(1,0,0,1,-41.95199966430664,-163.45199584960938)"><path fill="rgb(35,115,168)" fill-opacity="1" d=" M0,-7 C3.863300085067749,-7 7,-3.863300085067749 7,0 C7,3.863300085067749 3.863300085067749,7 0,7 C-3.863300085067749,7 -7,3.863300085067749 -7,0 C-7,-3.863300085067749 -3.863300085067749,-7 0,-7z"></path></g></g><g style="display: block;" transform="matrix(0.9999999403953552,0,0,0.9999999403953552,389.5693054199219,355.46551513671875)" opacity="0.9999999611139395"><g opacity="1" transform="matrix(1,0,0,1,-41.95199966430664,-163.45199584960938)"><path fill="rgb(0,111,185)" fill-opacity="1" d=" M0,-7 C3.863300085067749,-7 7,-3.863300085067749 7,0 C7,3.863300085067749 3.863300085067749,7 0,7 C-3.863300085067749,7 -7,3.863300085067749 -7,0 C-7,-3.863300085067749 -3.863300085067749,-7 0,-7z"></path></g></g><g style="display: block;" transform="matrix(1,0,0,1,368.8140563964844,309.27423095703125)" opacity="1"><g opacity="1" transform="matrix(1,0,0,1,-41.95199966430664,-163.45199584960938)"><path fill="rgb(35,115,168)" fill-opacity="1" d=" M0,-7 C3.863300085067749,-7 7,-3.863300085067749 7,0 C7,3.863300085067749 3.863300085067749,7 0,7 C-3.863300085067749,7 -7,3.863300085067749 -7,0 C-7,-3.863300085067749 -3.863300085067749,-7 0,-7z"></path></g></g><g style="display: block;" transform="matrix(1,0,0,1,442.9024963378906,367.199462890625)" opacity="2.7274055156567556e-8"><g opacity="1" transform="matrix(1,0,0,1,-41.95199966430664,-163.45199584960938)"><path fill="rgb(35,115,168)" fill-opacity="1" d=" M0,-4 C2.2076001167297363,-4 4,-2.2076001167297363 4,0 C4,2.2076001167297363 2.2076001167297363,4 0,4 C-2.2076001167297363,4 -4,2.2076001167297363 -4,0 C-4,-2.2076001167297363 -2.2076001167297363,-4 0,-4z"></path></g></g><g style="display: block;" transform="matrix(0.9999997019767761,0,0,0.9999997019767761,315.1037902832031,331.70697021484375)" opacity="0.010000241162083512"><g opacity="1" transform="matrix(1,0,0,1,-41.95199966430664,-163.45199584960938)"><path fill="rgb(0,111,185)" fill-opacity="1" d=" M0,-4 C2.2076001167297363,-4 4,-2.2076001167297363 4,0 C4,2.2076001167297363 2.2076001167297363,4 0,4 C-2.2076001167297363,4 -4,2.2076001167297363 -4,0 C-4,-2.2076001167297363 -2.2076001167297363,-4 0,-4z"></path></g></g><g style="display: block;" transform="matrix(1,0,0,1,239.33380126953125,322.2608642578125)" opacity="1"><g opacity="1" transform="matrix(1,0,0,1,-41.95199966430664,-163.45199584960938)"><path fill="rgb(0,111,185)" fill-opacity="1" d=" M0,-7 C3.863300085067749,-7 7,-3.863300085067749 7,0 C7,3.863300085067749 3.863300085067749,7 0,7 C-3.863300085067749,7 -7,3.863300085067749 -7,0 C-7,-3.863300085067749 -3.863300085067749,-7 0,-7z"></path></g></g><g style="display: block;" transform="matrix(1.0000008344650269,0,0,1.0000008344650269,222.49993896484375,156.99993896484375)" opacity="1"><g opacity="1" transform="matrix(1,0,0,1,19.389999389648438,29.381000518798828)"><path stroke-linecap="round" stroke-linejoin="round" fill-opacity="0" stroke="rgb(35,115,168)" stroke-opacity="1" stroke-width="12" d=" M2.9739999771118164,-2.9709999561309814 C-1.3819999694824219,-12.48900032043457 -7.390999794006348,-17.381000518798828 -7.390999794006348,-17.381000518798828"></path></g><g opacity="0.9999999466524446" transform="matrix(1,0,0,1,63.875999450683594,45.119998931884766)"><path stroke-linecap="round" stroke-linejoin="round" fill-opacity="0" stroke="rgb(0,111,185)" stroke-opacity="1" stroke-width="12" d=" M1.6160000562667847,12.756999969482422 C6.089000225067139,0.5529999732971191 5.415999889373779,-14.14799976348877 0.257999986410141,-27.524999618530273"></path></g><g opacity="0.9999994550201149" transform="matrix(1,0,0,1,96.00800323486328,94.99500274658203)"><path stroke-linecap="round" stroke-linejoin="round" fill-opacity="0" stroke="rgb(35,115,168)" stroke-opacity="1" stroke-width="12" d=" M-27.39900016784668,6.208000183105469 C-13.786999702453613,-2.4509999752044678 9.588000297546387,-11.538999557495117 41.13399887084961,-2.563999891281128"></path></g><g opacity="0.9999999446787189" transform="matrix(1,0,0,1,114.78700256347656,122.33100128173828)"><path stroke-linecap="round" stroke-linejoin="round" fill-opacity="0" stroke="rgb(0,111,185)" stroke-opacity="1" stroke-width="12" d=" M8.951000213623047,1.309000015258789 C11.711999893188477,1.843000054359436 14.612000465393066,2.6470000743865967 17.608999252319336,3.802000045776367"></path></g></g><g style="display: none;" transform="matrix(1,0,0,1,245.99998474121094,306)" opacity="1"><g opacity="1" transform="matrix(1,0,0,1,23.576000213623047,-172.4239959716797)"><path stroke-linecap="round" stroke-linejoin="miter" fill-opacity="0" stroke-miterlimit="4" stroke="rgb(0,111,185)" stroke-opacity="2.912178057101755e-7" stroke-width="6" d=" M0,0 C0,0 0,0 0,0 C0,0 0,0 0,0 C0,0 0,0 0,0 C0,0 0,0 0,0z"></path></g></g><g style="display: block;" transform="matrix(1,0,0,1,356,392)" opacity="1"><g opacity="1" transform="matrix(1,0,0,1,23.576000213623047,-172.4239959716797)"><path stroke-linecap="round" stroke-linejoin="miter" fill-opacity="0" stroke-miterlimit="4" stroke-dasharray=" 0" stroke-dashoffset="0" stroke="rgb(35,115,168)" stroke-opacity="1.2031095764086785e-7" stroke-width="8" d=" M0,0 C0,0 0,0 0,0 C0,0 0,0 0,0 C0,0 0,0 0,0 C0,0 0,0 0,0z"></path></g></g><g style="display: block;" transform="matrix(1,0,0,1,296,442)" opacity="1"><g opacity="1" transform="matrix(1,0,0,1,23.576000213623047,-172.4239959716797)"><path stroke-linecap="round" stroke-linejoin="miter" fill-opacity="0" stroke-miterlimit="4" stroke-dasharray=" 0" stroke-dashoffset="0" stroke="rgb(0,111,185)" stroke-opacity="2.3586487259308343e-7" stroke-width="6" d=" M0,0 C0,0 0,0 0,0 C0,0 0,0 0,0 C0,0 0,0 0,0 C0,0 0,0 0,0z"></path></g></g></g></g></svg>
                </div>
                <div>
                    <h2 class="text-2xl mt-0 mb-0 font-bold">
                        <?php esc_html_e( 'Thanks for using MetaSlider, the WordPress slideshow plugin', 'ml-slider' ) ?>
                    </h2>
                    <a class="underline text-blue-dark" href="#" onclick="jQuery('.ms-quickstart-heading').slideUp(); jQuery.post(ajaxurl, {action: 'quickstart_ads', ad_identifier: 'thankyou', _wpnonce: metaslider_notices_handle_notices_nonce });">
                        <?php esc_html_e( 'Dismiss', 'ml-slider' )  ?>
                    </a>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Ajax handler for quickstart dismiss action
     * 
     * @since 3.106
     */
    public function ajax_quickstart_ads()
    {
        if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'metaslider_handle_notices_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'The security check failed. Please refresh the page and try again.', 'ml-slider' )
            ), 401 );
        }

        $capability = apply_filters( 'metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES );
        if ( ! current_user_can( $capability ) ) {
            wp_send_json_error( array(
                'message' => __( 'Access denied. Sorry, you do not have permission to complete this task.', 'ml-slider' )
            ),
            403 );
        }

        if ( ! isset( $_POST['ad_identifier'] ) ) {
            wp_send_json_error( array(
                'message' => __( 'Bad request', 'ml-slider' )
            ), 400 );
        }

        $ad_identifier = sanitize_key( $_POST['ad_identifier'] );

        // Check valid ad_identifiers
        $valid_ad_identifiers = array(
            'thankyou'
        );

        if ( ! in_array( $ad_identifier, $valid_ad_identifiers ) ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid ad identifier', 'ml-slider' )
            ), 400 );
        }

        $result = update_option( "metaslider_quickstart_ad_{$ad_identifier}", 'hide', false );

        if ( ! $result ) {
            wp_send_json_error( array(
                'message' => __( 'There was an error dimissing the quickstart ad', 'ml-slider' )
            ), 400 );
        }

        wp_send_json_success(array(
            'message' => __( 'The quickstart ad was successfully hidden!', 'ml-slider' ),
        ), 200);
    }
}
