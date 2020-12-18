<?php
/**
 * Jufeedback main file
 *
 * @package Joomunited\ADVGB\Jufeedback
 */

namespace Joomunited\ADVGB\Jufeedback;

/**
 * Class Jufeedback
 */
class Jufeedback
{
    /**
     * The main plugin php file
     *
     * @var string $main_plugin_file
     */
    public static $main_plugin_file;

    /**
     * The extension prefix
     *
     * @var string plugin_prefix
     */
    public static $plugin_prefix;

    /**
     * The extension slug
     *
     * @var string $plugin_slug
     */
    public static $plugin_slug;

    /**
     * The plugin name
     *
     * @var string $plugin_name
     */
    public static $plugin_name;

    /**
     * The plugin translation text
     *
     * @var string $text_domain
     */
    public static $text_domain;

    /**
     * Init list feedback params
     *
     * @var array $list_comments
     */
    public static $list_comments = array();

    /**
     * Define mailto
     *
     * @var string $mailto
     */
    public static $mailto = 'help@publishpress.com';

    /**
     * @todo check if Jutranslation is actually being called here
     * Initialize Jutranslation
     *
     * @param string $main_plugin_file Main plugin file
     * @param string $plugin_prefix    Extension prefix
     * @param string $plugin_slug      Extension slug
     * @param string $plugin_name      Extension name
     * @param string $text_domain      Language text domain
     *
     * @return void
     */
    public static function init($main_plugin_file, $plugin_prefix, $plugin_slug, $plugin_name, $text_domain)
    {
        //Only need on admin side
        if (!is_admin()) {
            return;
        }

        self::$main_plugin_file = $main_plugin_file;
        self::$plugin_prefix = $plugin_prefix;
        self::$plugin_slug = $plugin_slug;
        self::$plugin_name = $plugin_name;
        self::$text_domain = $text_domain;
        //phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain -- Already use text domain for extensions
        self::$list_comments = array(
            self::$plugin_prefix . '_feature_missing' => array(
                'explain' => __('There\'s a feature missing', self::$text_domain),
                'placeholder' => __('Let us know what\'s missing', self::$text_domain),
            ),
            self::$plugin_prefix . '_not_working' => array(
                'explain' => __('The plugin is not working great', self::$text_domain),
                'placeholder' => __('Please let us know what\'s the problem', self::$text_domain),
            ),
            self::$plugin_prefix . '_found_better_plugin' => array(
                'explain' => __('I found a better plugin', self::$text_domain),
                'placeholder' => __('Oh, which one is it?', self::$text_domain),
            ),
            self::$plugin_prefix . '_something_else' => array(
                'explain' => __('I was searching for something else', self::$text_domain),
                'placeholder' => __('Can you let us know what\'s you\'re searching for?', self::$text_domain),
            ),
            self::$plugin_prefix . '_other' => array(
                'explain' => __('Other, We\'d like to hear your opinion :)', self::$text_domain),
                'placeholder' => __('Write what\'s in your mind', self::$text_domain))
        );
        //phpcs:enable

        // Check if the current screen
        add_action('current_screen', array(__CLASS__, 'screenFeedback'));
        // Check if the current user
        add_action('admin_init', array(__CLASS__, 'jufeedbackCallAjax'));

        // ADD REVIEW NOTICE FOR PLUGIN
        //
        //
        register_activation_hook(self::$main_plugin_file, array(__CLASS__, 'jureviewActivation'));
        register_deactivation_hook(self::$main_plugin_file, array(__CLASS__, 'jureviewDeactivation'));

        $install_time = get_option(self::$plugin_prefix . '_jureview_installation_time');
        if (!empty($install_time) && $install_time !== 'unnecessary' && ($install_time + (30 * 24 * 60 * 60) < time())) {
            add_action('admin_notices', array(__CLASS__, 'jureviewNotice'));
        }
    }

    /**
     * Ajax method
     *
     * @return void
     */
    public static function jufeedbackCallAjax()
    {
        if (current_user_can('manage_options')) {
            add_action('wp_ajax_ju_send_feedback_deactive_' . self::$plugin_slug, array(__CLASS__, 'sendFeedbackDeactive'));
            add_action('wp_ajax_ju_disable_feedback_' . self::$plugin_slug, array(__CLASS__, 'disableFeedback'));
            add_action('wp_ajax_jureview_ajax_hide_review_' . self::$plugin_slug, array(__CLASS__, 'ajaxHideReview'));
            add_action('wp_ajax_ju_feedback_get_technical_data_' . self::$plugin_slug, array(__CLASS__, 'ajaxGetTechnicalData'));
        }

        self::juUpdatePlugin();
    }

    /**
     * Trigger in update plugin
     *
     * @return void
     */
    public static function juUpdatePlugin()
    {
        $ju_version_installed = get_option(self::$plugin_prefix . '_jufeedback_version', false);

        if (!$ju_version_installed) {
            update_option(self::$plugin_prefix . '_jureview_installation_time', time());
            // Update current version
            update_option(self::$plugin_prefix . '_jufeedback_version', '1.0.0');

            return;
        }
    }

    /**
     * Trigger in current_screen usage
     *
     * @param object $current_screen Current screen
     *
     * @return void
     */
    public static function screenFeedback($current_screen)
    {
        // Return if it is not plugin screen
        if (!in_array($current_screen->id, array('plugins', 'plugins-network'))) {
            return;
        }

        // Check once deactive
        $allow_feedback = get_option(self::$plugin_prefix . '_disallow_feedback', false);
        if (!$allow_feedback) {
            self::enqueueFeedbackScript();
        }
    }

    /**
     * Enqueue feedback script in plugin screen
     *
     * @return void
     */
    public static function enqueueFeedbackScript()
    {
        // Enqueue style
        wp_enqueue_style(
            'ju-feedback-style',
            plugin_dir_url(self::$main_plugin_file) . 'jufeedback/assets/css/jufeedback.css'
        );

        // Enqueue script
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script(
            'ju-feedback-tooltip',
            plugin_dir_url(self::$main_plugin_file) . 'jufeedback/assets/js/tooltip.js'
        );
        wp_enqueue_script(
            'ju-feedback-global',
            plugin_dir_url(self::$main_plugin_file) . 'jufeedback/assets/js/global.js'
        );
        wp_enqueue_script(
            'ju-feedback-velocity',
            plugin_dir_url(self::$main_plugin_file) . 'jufeedback/assets/js/velocity.min.js'
        );
        wp_enqueue_script(
            'ju-feedback',
            plugin_dir_url(self::$main_plugin_file) . 'jufeedback/assets/js/jufeedback.min.js'
        );

        wp_localize_script('ju-feedback', 'ju_feedback', array(
            'token' => wp_create_nonce('ju-feedback'),
            'ajaxurl' => admin_url('admin-ajax.php')
        ));

        add_action('admin_footer', array(__CLASS__, 'renderFeedbackModal'));
    }

    /**
     * Render feedback modal in plugin screen
     *
     * @return void
     */
    public static function renderFeedbackModal()
    {
        //phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain -- Already use text domain for extensions
        ?>
        <!--Dialog-->
        <div id="<?php echo esc_attr(self::$plugin_prefix) ?>_feedback_modal"
             class="ju-feedback-dialog <?php echo esc_attr(self::$plugin_slug) ?>" style="display: none">
            <div class="title"><?php esc_html_e('Very quick feedback', self::$text_domain) ?></div>
            <div class="feedback-content">
                <div class="content">
                    <div class="text-introduction">
                        <span><?php esc_html_e('Before leaving, would you have 30 seconds to give your anonymous opinion about why you\'re disabling the plugin?', self::$text_domain) ?></span>
                    </div>
                    <ul class="list-comments">
                        <?php foreach (self::$list_comments as $key => $comments) : ?>
                            <li>
                                <input id="<?php echo esc_html($key) ?>" type="checkbox"
                                       name="<?php echo esc_html($key) ?>"
                                       class="choose-reason reason-deactive" value="<?php echo esc_html($key) ?>"/>
                                <label for="<?php echo esc_html($key) ?>">
                                    <?php echo esc_html($comments['explain']) ?>
                                </label>
                                <textarea id="comment-<?php echo esc_html($key) ?>"
                                          placeholder="<?php echo esc_html($comments['placeholder']) ?>"
                                          name="comment-<?php echo esc_html($key) ?>" class="feedback-text" rows="2"
                                          value=""></textarea>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="technical-information">
                        <input id="<?php echo esc_attr(self::$plugin_prefix) ?>-allow-send-technical" type="checkbox"
                               checked="checked" name="allow_send_technical" class="" value=""/>
                        <label for="<?php echo esc_attr(self::$plugin_prefix) ?>-allow-send-technical">
                            <?php esc_html_e('Share anonymous technical informations', self::$text_domain) ?></label>
                        <i class="material-icons signaling tooltipped" data-position="top"
                           data-tooltip="<?php esc_html_e('We get 100% anonymously info for debug and statistics purpose only (theme name, server informations...), click the drop down icon to see what information is sent', self::$text_domain) ?>">chat</i>
                        <i class="material-icons more">arrow_drop_down</i>
                        <textarea name="technical" readonly="readonly" class="technical" rows="5" data-info=""></textarea>
                    </div>
                </div>
                <div class="feedback-result-notice"></div>
            </div>
            <div class="feedback-button">
                <a href="#" class="btn button disable-only"><?php esc_html_e('Disable now', self::$text_domain) ?></a>
                <a href="#"
                   class="btn button send-message"><?php esc_html_e('SEND & DISABLE, THANKS!', self::$text_domain) ?></a>
                <img class="ju-loading"
                     src="<?php echo esc_html(plugin_dir_url(self::$main_plugin_file) . 'jufeedback/assets/loading.gif') ?>"/>
                <div class="clear"></div>
            </div>
        </div>
        <?php
        //phpcs:enable
    }

    /**
     * Ajax send feedback to mail on deactive
     *
     * @return void
     */
    public static function sendFeedbackDeactive()
    {
        check_ajax_referer('ju-feedback', 'ajax_nonce');

        $temp_array = array();
        $output = '';

        if (isset($_POST['feedbackTechnical']) && !empty($_POST['feedbackTechnical'])) {
            $temp_array = json_decode(stripslashes($_POST['feedbackTechnical']), true);
        }

        $email = sanitize_email(self::$mailto);
        $wp_address = get_bloginfo('url');
        $wp_name = get_bloginfo('name');

        // First array with timestamp, reasons, other comment
        $first_array = array('Timestamp' => gmdate('Y-m-d h:i:s', current_time('timestamp')));

        // translators: %s: website url.
        $email_subject = sprintf(self::$plugin_name . ' - Feedback from %s', $wp_address);

        $email_body = sprintf(
            '<i>%1$s</i> plugin feedback from %2$s (%3$s).<br><br>',
            self::$plugin_name,
            $wp_name,
            $wp_address
        );

        if (isset($_POST['reasons']) && !empty($_POST['reasons'])) {
            $reasons = json_decode(stripslashes($_POST['reasons']), true);
            foreach ($reasons as $k => $v) {
                $reasons[$k]['reason'] = self::$list_comments[$v['reason']]['explain'];
            }
            $first_array['Reasons'] = $reasons;
        }

        $temp_array = array_merge($first_array, $temp_array);

        if (!empty($temp_array)) {
            //phpcs:disable PHPCompatibility.Constants.NewConstants.json_pretty_printFound -- We do not use for php <5.3
            $email_body .= sprintf(
                // translators: %s: The custom message that may be included with the email.
                '<b>Feedback Information</b> : <pre>%s</pre>',
                json_encode($temp_array, JSON_PRETTY_PRINT)
            );
            //phpcs:enable
        }

        $headers = "MIME-Version: 1.0\r\n";
        //Set the content-type to html
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $sendmail = wp_mail($email, $email_subject, $email_body, $headers);
        $status = false;
        $output .= '<p>';
        //phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain -- Already use text domain for extensions
        if (!empty($sendmail)) {
            $status = true;
            $output .= __('Thanks for your feedback! We will acknowledge and make it better in the future.', self::$text_domain);
            // Update status feedback
            update_option(self::$plugin_prefix . '_disallow_feedback', true);
        } else {
            $output .= __('Oops! It seems there was a problem sending the e-mail. Please try again!', self::$text_domain);
        }
        //phpcs:enable
        $output .= '</p>';

        $response = array(
            'send_status' => $status,
            'message' => $output,
        );

        wp_send_json($response);

        wp_die();
    }

    /**
     * Ajax get technical data
     *
     * @return void
     */
    public static function ajaxGetTechnicalData()
    {
        check_ajax_referer('ju-feedback', 'ajax_nonce');

        // Load JuCheckDebugData class
        if (!class_exists('JuCheckDebugData')) {
            require_once(plugin_dir_path(self::$main_plugin_file) . 'jufeedback/ju-check-debug-data.php');
        }

        // Get server information
        $technical_data = call_user_func('JuCheckDebugData::debugData');
        // Filter data
        $result_data = self::getTechnicalData($technical_data);

        if (!empty($result_data)) {
            wp_send_json(array('get_status' => true, 'data' => $result_data));
        }

        wp_send_json(array('get_status' => false, 'data' => false));
    }

    /**
     * Get technical data by json
     *
     * @param array $technical_data Data to filter
     *
     * @return array|boolean
     */
    public static function getTechnicalData($technical_data)
    {
        $output = array();
        $sub_output = array();

        if (!empty($technical_data)) {
            foreach ($technical_data as $key => $data) {
                if (!isset($data['fields']) || empty($data['fields'])) {
                    continue;
                }

                if ($key === 'wp-core') {
                    $output['Wordpress version'] = $data['fields']['version']['value'];
                    $output['Is https site'] = $data['fields']['https_status']['value'];
                    $output['Is multisite'] = $data['fields']['multisite']['value'];
                }

                if ($key === 'wp-active-theme') {
                    $output['Active theme']['Name'] = $data['fields']['name']['value'];
                    $output['Active theme']['Version'] = $data['fields']['version']['value'];
                    $output['Active theme']['Folder'] = self::setAnonymousPath($data['fields']['theme_path']['value']);
                }

                if ($key === 'wp-plugins-active') {
                    foreach ($data['fields'] as $data_name) {
                        $sub_output[] = array(
                            'Name' => $data_name['label'],
                            'Version' => $data_name['value']
                        );
                    }
                    $output['Active plugins'] = $sub_output;
                    unset($sub_output);
                }

                if ($key === 'wp-media') {
                    foreach ($data['fields'] as $data_name) {
                        $sub_output[$data_name['label']] = $data_name['value'];
                    }
                    $output['Media handling'] = $sub_output;
                    unset($sub_output);
                }

                if ($key === 'wp-server') {
                    foreach ($data['fields'] as $data_name) {
                        $sub_output[$data_name['label']] = $data_name['value'];
                    }
                    $output['Server'] = $sub_output;
                    unset($sub_output);
                }

                if ($key === 'wp-database') {
                    $output['Database']['Extension'] = $data['fields']['extension']['value'];
                    $output['Database']['Version'] = $data['fields']['server_version']['value'];
                    $output['Database']['Client'] = $data['fields']['client_version']['value'];
                }

                if ($key === 'wp-constants') {
                    foreach ($data['fields'] as $data_name) {
                        $data_name['value'] = self::setAnonymousPath($data_name['value']);
                        $sub_output[$data_name['label']] = $data_name['value'];
                    }
                    $output['Wordpress constants'] = $sub_output;
                    unset($sub_output);
                }

                if ($key === 'wp-filesystem') {
                    foreach ($data['fields'] as $data_name) {
                        $sub_output[$data_name['label']] = $data_name['value'];
                    }
                    if (file_exists(ABSPATH . 'wp-config.php') && is_writable(ABSPATH . 'wp-config.php')) {
                        /**
                         * The config file resides in ABSPATH
                         */
                        $config_is_writeable = true;
                    } elseif (file_exists(dirname(ABSPATH) . '/wp-config.php') && is_writable(dirname(ABSPATH) . '/wp-config.php')) {
                        /**
                         * The config file resides one level above ABSPATH but is not part of another installation
                         */
                        $config_is_writeable = true;
                    } else {
                        // A config file doesn't exist or isn't writeable
                        $config_is_writeable = false;
                    }
                    //phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain -- Already use text domain for extensions
                    if ($config_is_writeable) {
                        $sub_output['WordPress configuration file'] = __('Writable', self::$text_domain);
                    } else {
                        $sub_output['WordPress configuration file'] = __('Not writable', self::$text_domain);
                    }
                    //phpcs:enable
                    $output['Filesystem permissions'] = $sub_output;
                    unset($sub_output);
                }
            }
        }

        $extension_settings = self::getSettingsOfPlugin();
        if (!empty($extension_settings)) {
            $output['Extension Configuration'] = $extension_settings;
        }

        if (empty($output)) {
            return false;
        }

        return $output;
    }

    /**
     * Get settings of plugin to display information
     *
     * @return array
     */
    public static function getSettingsOfPlugin()
    {
        // Get configuration
        $config = array();

        return $config;
    }

    /**
     * Remove the base path
     *
     * @param string $base_path Base path
     *
     * @return string
     */
    public static function setAnonymousPath($base_path)
    {
        if (!empty($base_path) && is_dir($base_path)) {
            $base_path = str_replace(get_home_path(), '/ANONYMOUS_BASE_PATH/', $base_path);
        }
        return $base_path;
    }

    /**
     * Disable only in extension feedback
     *
     * @return void
     */
    public static function disableFeedback()
    {
        check_ajax_referer('ju-feedback', 'ajax_nonce');

        update_option(self::$plugin_prefix . '_disallow_feedback', true);

        wp_send_json(array('status' => true));

        wp_die();
    }

    /**
     * Hide review notice
     *
     * @return void
     */
    public static function ajaxHideReview()
    {
        check_ajax_referer('ju-review', 'ajaxnonce');
        update_option(self::$plugin_prefix . '_jureview_installation_time', 'unnecessary');
        wp_die();
    }

    /**
     * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
     *
     * @static
     * @return void
     */
    public static function jureviewActivation()
    {
        $install_time = get_option(self::$plugin_prefix . '_jureview_installation_time');

        if (empty($install_time)) {
            update_option(self::$plugin_prefix . '_jureview_installation_time', time());
        }
    }

    /**
     * Removes all connection options
     *
     * @static
     * @return void
     */
    public static function jureviewDeactivation()
    {
        $install_time = get_option(self::$plugin_prefix . '_jureview_installation_time');
        if (!empty($install_time) && $install_time !== 'unnecessary' && ($install_time + (7 * 24 * 60 * 60) > time())) {
            delete_option(self::$plugin_prefix . '_jureview_installation_time');
        }
    }

    /**
     * Show a message asking to make a review on the PD after a week after the installation
     *
     * @return void
     */
    public static function jureviewNotice()
    {
        wp_enqueue_script(
            'ju-review',
            plugin_dir_url(self::$main_plugin_file) . 'jufeedback/assets/js/jureview.min.js'
        );
        wp_localize_script('ju-review', 'ju_review', array(
            'token' => wp_create_nonce('ju-review'),
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
        //phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain -- Already use text domain for extensions
        echo '<div id="' . esc_attr(self::$plugin_prefix) . '-review-notice" class="updated jureview-notice" data-slug="' . esc_attr(self::$plugin_slug) . '">
	          <p>' . sprintf(
            esc_html__('Thanks for using %s, it’s been more than a month now! Would you consider leaving a review on the plugin directory? It helps us make the plugin & support better :)', self::$text_domain),
            esc_html(self::$plugin_name)
        ) . '</p>
	          <p class="submit">
	             <a href="' . esc_html('https://wordpress.org/support/plugin/' . self::$plugin_slug . '/reviews/?filter=5#new-post') . '" target="_blank" class="button-primary jureview-already-review">' . esc_html__('Sure I’d love to', self::$text_domain) . '</a>
	             <button class="button-secondary jureview-hide-review">' . esc_html__('No hide notification', self::$text_domain) . '</button>
	          </p>
	          </div>';
        //phpcs:enable
    }
}