<?php

namespace com\cminds\package\free\v1_1_4;

if (!defined(__NAMESPACE__ . '\PLATFORM_VERSION')) {
    define(__NAMESPACE__ . '\PLATFORM_VERSION', '1_1_4');
}
if (!class_exists(__NAMESPACE__ . '\CmindsFreePackage')) {

    class CmindsFreePackage {

        private $config = array();

        /**
         * LicensingAPI object
         * @var CmindsLicensingAPI
         */
        public $licensingApi = null;
        private $pluginMenu = null;
        private $pluginMenuPage = null;

        const SHAREBOX_FLAT = 0;
        const SHAREBOX_SQUARE = 1;
        const ADS_REFRESH_INTERVAL = 7776000; // 3600 * 24 * 90 = 3 months

        public function __construct($config) {
            $this->config = $config;

            $this->pluginMenu = $this->getOption('plugin-menu-item');
            $this->pluginMenuPage = $this->getPageUrl('licensing');

            add_action('activated_plugin', array($this, 'redirectAfterInstall'), 10, 2);
            add_action('admin_init', array($this, 'updatePoweredByOption'));
            add_action('admin_init', array($this, 'registerAdminActions'));
            add_action('admin_menu', array($this, 'updateMenu'), 21);
            add_action('admin_enqueue_scripts', array($this, 'enqueueAdminStyles'));
            add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));

            $aboutPageKey = $this->getPageSlug();
            add_action('cminds-' . $aboutPageKey . '-content-1', array($this, 'displayUserGuideTab'));
            add_action('cminds-' . $aboutPageKey . '-content-10', array($this, 'displayCreativeMindsTab'));
            add_action('cminds-' . $aboutPageKey . '-content-30', array($this, 'displayMembershipTab'));
            add_action('cminds-' . $aboutPageKey . '-content-40', array($this, 'displayCMProductTab'));
            add_action('cminds-' . $aboutPageKey . '-content-50', array($this, 'displayCMAddOnsTab'));
            add_action('cminds-' . $aboutPageKey . '-content-60', array($this, 'displayVideoGuidesTab'));
            add_action('cminds-' . $aboutPageKey . '-content-99', array($this, 'displaySupportTab'));
            add_action('cminds-' . $aboutPageKey . '-content-199', array($this, 'displayServerInformationTab'));

            add_action('cminds_download_sysinfo', array($this, 'cminds_generate_sysinfo_download'));

            add_action('init', array($this, 'cminds_get_actions'));
            add_action('init', array($this, 'cminds_post_actions'));

            add_shortcode('cminds_free_author', array($this, 'showAuthor'));
            add_shortcode('cminds_free_registration', array($this, 'showRegistration'));
            add_shortcode('cminds_free_guide', array($this, 'showGuide'));
            add_shortcode('cminds_upgrade_box', array($this, 'showUpgrade'));
            add_shortcode('cminds_free_activation', array($this, 'showActivation'));

            add_filter('plugin_row_meta', array($this, 'add_plugin_meta_links'), 10, 2);
            add_filter('plugin_action_links_' . $this->getOption('plugin-basename'), array($this, 'add_plugin_action_links'));

            add_action('wp_dashboard_setup', array($this, 'addDashboardWidget'));
        }

        public function addDashboardWidget() {
            if ($this->isRegistered()) {
                wp_add_dashboard_widget('cminds_package_dashboard_widget', __('CreativeMinds WordPress News', 'cminds-package'), array($this, 'showCMDashboardWidget'));
            }
        }

        public function showCMDashboardWidget() {
            $feeds = array(
                array(
                    'url'          => 'https://www.cminds.com/category/wordpress/feed/',
                    'items'        => 6,
                    'show_summary' => 0,
                    'show_author'  => 0,
                    'show_date'    => 1,
                ),
            );

            wp_dashboard_primary_output('cminds_package_dashboard_widget', $feeds);
            ?>
            <br><hr>
            <p>
                <?php
                printf(
                        '<a href="%1$s" target="_blank">%2$s <span class="screen-reader-text">%3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>', 'https://www.cminds.com/', __('cminds.com'), __('(opens in a new window)')
                );
                ?>

                |

                <?php
                printf(
                        '<a href="%1$s" target="_blank">%2$s <span class="screen-reader-text">%3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>', 'https://www.cminds.com/wordpress-plugins/', __('CM WordPress Plugins'), __('(opens in a new window)')
                );
                ?>

                |

                <?php
                printf(
                        '<a href="%1$s" target="_blank">%2$s <span class="screen-reader-text">%3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>', esc_url(_x('https://www.cminds.com/wordpress-plugins-library/cm-wordpress-plugins-yearly-membership/', '99+ WordPress Plugins Bundle')), __('99+ WordPress Plugins Bundle'), __('(opens in a new window)')
                );
                ?>
            </p>
            <?php
        }

        public function redirectAfterInstall($plugin, $network_activation = false) {
            global $cmindsPluginPackage;
            $the_package = null;

            foreach ($cmindsPluginPackage as $package) {
                $basename = $package->getOption('plugin-basename');
                if ($basename == $plugin) {
                    $the_package = $package;
                    break;
                }
            }

            if ($the_package && $the_package->getOption('plugin-redirect-after-install')) {
                $url = $the_package->getOption('plugin-redirect-after-install');
                $isBulkActivate = array();
                $isBulkActivate[] = filter_input(INPUT_POST, 'action2');
                $isBulkActivate[] = filter_input(INPUT_POST, 'action');
                if (!empty($url) && !in_array('activate-selected', $isBulkActivate)) {
                    wp_redirect($url);
                    exit();
                }
            }
        }

        /**
         * Hooks Cminds actions, when present in the $_GET superglobal. Every Cminds_action
         * present in $_GET is called using WordPress's do_action function. These
         * functions are called on init.
         *
         * @since 1.0
         * @return void
         */
        public function registerAdminActions() {
            if (is_admin()) {
                // If user is paying or in trial and have the free version installed,
                // assume that the deactivation is for the upgrade process.
                add_action('wp_ajax_cm-submit-uninstall-reason', array($this, 'submitUninstallReason'));
                add_action('wp_ajax_cm-submit-registration-email', array($this, 'submitRegistrationEmail'));
                add_action('wp_ajax_cm-submit-deregistration', array($this, 'submitDeregistration'));
                add_action('wp_ajax_cm-submit-registration-skip', array($this, 'submitRegistrationSkip'));

                global $pagenow;
                if ('plugins.php' === $pagenow) {
                    // Add action link to settings page.
                    add_filter('plugin_action_links_' . $this->getOption('plugin-basename'), array($this, 'modifyPluginActionLinks'), 10, 2);
                    add_filter('network_admin_plugin_action_links_' . $this->getOption('plugin-basename'), array($this, 'modifyPluginActionLinks'), 10, 2);
                    add_action('admin_footer', array($this, 'showDeactivationFeedbackDialog'), 11);
                }
            }
        }

        /**
         * Modify plugin's page action links collection.
         *
         * @author Vova Feldman (@svovaf)
         * @since  1.0.0
         *
         * @param array $links
         * @param       $file
         *
         * @return array
         */
        function modifyPluginActionLinks($links, $file) {
            /*
             * This HTML element is used to identify the correct plugin when attaching an event to its Deactivate link.
             */
            if (isset($links['deactivate'])) {
                $links['deactivate'] .= '<i class="cm-slug" data-slug="' . $this->getOption('plugin-abbrev') . '"></i>';
            }

            return $links;
        }

        /**
         * Displays a confirmation and feedback dialog box when the user clicks on the "Deactivate" link on the plugins
         * page.
         *
         */
        function showDeactivationFeedbackDialog() {
            $content = '';
            ob_start();
            include_once 'views/deactivation_feedback_modal.php';
            $content .= ob_get_clean();
            echo $content;
        }

        function filterDeactivationReason($reason) {

            if (strlen($reason) < 20) {
                return '';
            }
            if (str_word_count($reason) < 4) {
                return '';
            }

            $dirty_words = array('shit', 'fuck', 'wtf');
            foreach ($dirty_words as $a) {
                if (stripos($reason, $a) !== false) {
                    return '';
                }
            }

            return $reason;
        }

        /**
         * Called after the user has submitted his reason for deactivating the plugin.
         * @since  1.1.2
         */
        function submitRegistrationEmail() {
            $atts = array();

            if (empty($_POST['email'])) {
                exit;
            }

            $this->registerUser($atts);
        }

        /**
         * Called after the user has submitted his reason for deactivating the plugin.
         * @since  1.1.2
         */
        function submitDeregistration() {
            $atts = array();

            $this->deregisterUser($atts);
        }

        /**
         * Called after the user has submitted his reason for deactivating the plugin.
         * @since  1.1.2
         */
        function submitRegistrationSkip() {
            if (empty($_POST)) {
                exit;
            }
            $atts = array();
            if (!empty($_POST['id'])) {
                $atts['id'] = sanitize_text_field($_POST['id']);
            }
            $this->skipRegistration($atts);
        }

        /**
         * Called after the user has submitted his reason for deactivating the plugin.
         * @since  1.1.2
         */
        function submitUninstallReason() {
            if (empty($_POST['plugin_slug']) || empty($_POST['deactivation_reason'])) {
                exit;
            }

            $reason = isset($_REQUEST['deactivation_reason']) ? trim(esc_html(stripslashes(strip_tags($_REQUEST['deactivation_reason'])))) : '';
            $filteredReason = $this->filterDeactivationReason($reason);
            $postedEmail = isset($_POST['contact_email']) && is_email(trim($_POST['contact_email'])) ? sanitize_text_field($_POST['contact_email']) : null;

            /*
             * Only send the e-mail if the filtered reason is not empty
             */
            if (!empty($filteredReason)) {
                global $cmindsPluginPackage;
                $package = $cmindsPluginPackage[$_POST['plugin_slug']];

                $fields = array(
                    'product_name' => $package->getOption('plugin-name'),
                    'remote_url'   => get_bloginfo('wpurl'),
                    'email'        => null !== $postedEmail ? $postedEmail : get_bloginfo('admin_email'),
                    'want_contact' => isset($_POST['want_contact']) ? 'true' === $_POST['want_contact'] : false,
                    'data_agree'   => isset($_POST['data_agree']) ? 'true' === $_POST['data_agree'] : true,
                );

                $registered = $this->isRegistered($_POST['plugin_slug']) ? ' (registered)' : '';
                $message = '<p>The ' . $fields['product_name'] . ' has been deactivated on ' . $fields['remote_url'] . ' by ' . $fields['email'] . $registered . '.</p> <p>The reason was:</p> <p><strong>' . $filteredReason . '</strong></p>';
                $message .= '<br/><hr/>';

                $mail = 'support@cminds.com';

                if ($fields['want_contact']) {
                    $message .= '<p>This user is interested in contact from our side.</p>';
                    $message .= '<br/><hr/>';
                    $mail = 'support@cminds.com';
                }

                if ($fields['data_agree']) {
                    $message .= '<p>SYSTEM INFORMATION BELOW:</p>';
                    ob_start();
                    echo '<pre>';
                    echo $this->cminds_system_info_content();
                    echo '</pre>';
                    $message .= ob_get_clean();
                }

                $message .= '<p>END OF SYSTEM INFORMATION</p>';

                add_filter('wp_mail_content_type', array(__CLASS__, 'cminds_set_content_type'));
                $result = wp_mail($mail, 'CM Free Plugin Deactivation Feedback', $message);
                remove_filter('wp_mail_content_type', array(__CLASS__, 'cminds_set_content_type'));
            }


            // Print '1' for successful operation.
            echo 1;
            exit;
        }

        /**
         * Hooks Cminds actions, when present in the $_GET superglobal. Every Cminds_action
         * present in $_GET is called using WordPress's do_action function. These
         * functions are called on init.
         *
         * @since 1.0
         * @return void
         */
        public function cminds_get_actions() {
            if (isset($_GET['cminds_action'])) {
                do_action('cminds_' . $_GET['cminds_action'], $_GET);
            }
        }

        /**
         * Hooks Cminds actions, when present in the $_POST superglobal. Every Cminds_action
         * present in $_POST is called using WordPress's do_action function. These
         * functions are called on init.
         *
         * @since 1.0
         * @return void
         */
        public function cminds_post_actions() {
            if (isset($_POST['cminds_action'])) {
                do_action('cminds_' . $_POST['cminds_action'], $_POST);
            }
        }

        public function updatePoweredByOption() {
            $optionValue = filter_input(INPUT_POST, $this->getPoweredByOption());
            $submitValue = filter_input(INPUT_POST, 'cminds_poweredby_change');

            if (null !== $optionValue || null !== $submitValue) {
				if(is_admin() && is_user_logged_in() && current_user_can('manage_options')) {
					update_option($this->getPoweredByOption(), $optionValue);
				}
            }
        }

        public function getPoweredByOption() {
            $optionName = $this->getOption('plugin-abbrev') . '-powered-by-enabled';
            return $optionName;
        }

        /**
         * Default is 0 (disabled)
         * @return type
         */
        public function isPoweredByEnabled() {
            $result = get_option($this->getPoweredByOption(), 0);
            return $result;
        }

        /**
         * Returns the author Url (for free version only)
         */
        public function showAuthor($atts = array()) {
            $authorUrl = '';
            global $cmindsPluginPackage;

            $atts = shortcode_atts(array('id' => null), $atts);
            $currentPlugin = !empty($atts['id']) ? $cmindsPluginPackage[$atts['id']] : $this;

            if (!$currentPlugin->isPoweredByEnabled()) {
                return;
            }
            ob_start();
            ?>
            <style>
                .cminds_poweredby {clear:both;float:none;font-size:11px;line-height:1.5;display: inline-block;padding: 3px;margin-top:20px;color:#bbb;text-decoration:none;font-weight:bold}
                .cminds_poweredby:before {content:'Powered by ';}
                .cminds_poweredbylink:link{color:#bbb;text-decoration:none;}
                .cminds_poweredbylink:visited{color:#bbb;text-decoration:none;}
                .cminds_poweredbylink:hover{color:#bbb;text-decoration:none;}
            </style>

            <?php
            $authorUrl .= ob_get_clean();
            /*
             * By leaving following snippet in the code, you're expressing your gratitude to creators of this plugin. Thank You!
             */
            $authorUrl .= '<div style="display:block;clear:both;"></div><span class="cminds_poweredby">';
            $authorUrl .= '<a href="' . $currentPlugin->addAffiliateCode('https://www.cminds.com/') . '" target="_blank" class="cminds_poweredbylink">CreativeMinds</a> ';
            $authorUrl .= ' <a href="' . $currentPlugin->addAffiliateCode('https://www.cminds.com/wordpress-plugins/') . '" target="_blank" class="cminds_poweredbylink">WordPress Plugin</a>';
            $authorUrl .= ' <a href="' . $currentPlugin->addAffiliateCode($currentPlugin->getOption('plugin-store-url')) . '" target="_blank" class="cminds_poweredbylink">' . $currentPlugin->getOption('plugin-name') . '</a>';
            $authorUrl .= '</span><div style="display:block;clear:both;"></div>';

            return $authorUrl;
        }

        /*
         * Licensing Page Tabs
         */

        public function displayUpgradeTutorialTab() {
            $content = '';
            ob_start();
            ?>
            <div>
                <a href="https://www.cminds.com/guest-account/" class="btn button button-primary" target="_blank">Customer Dashboard</a>
            </div>
            <br/>
            <div>
                <?php
                echo do_shortcode('[video src="https://player.vimeo.com/video/134692135?title=0&byline=0&portrait=0" width="500" height="280"]');
                ?>
            </div>
            <?php
            $content .= ob_get_clean();
            return $content;
        }

        public function displayManageProductsTab() {
            $content = '';
            ob_start();
            ?>
            <div>
                <a href="https://www.cminds.com/guest-account/" class="btn button button-primary" target="_blank">Open CreativeMinds Customer Dashboard</a>
            </div>
            <br/>
            <div>
                <?php
                echo do_shortcode('[video src="https://player.vimeo.com/video/134490629?title=0&byline=0&portrait=0" width="640" height="360"]');
                ?>
            </div>
            <?php
            $content .= ob_get_clean();
            return $content;
        }

        public function displayServerInformationTab() {
            global $wp_version;
            $content = '';

            ob_start();
            echo $this->cminds_compatibility_check();
            echo $this->cminds_system_info();
            $content .= ob_get_clean();
            return $content;
        }

        /*
         * About/User Guide Page Tabs
         */

        public function displayUserGuideTab() {
            $content = '';
            ob_start();
            ?>
            <div>
                <a href="<?php echo $this->getUserguideUrl(); ?>" class="btn button button-primary" target="_blank">Open User Guide in a new tab</a>
            </div>
            <?php
            $content .= ob_get_clean();
            return $content;
        }

        public function displayUpgradeToProTab() {
            $content = '';
            $upgradeToProHtml = $this->getOption('plugin-compare-table');
            $pluginUrl = $this->getOption('plugin-store-url');
            wp_enqueue_script('jquery-ui-tooltip');
            ob_start();
            ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery(".cminds-package-show-tooltip").tooltip();
                });
            </script>
            <style type="text/css">
                .pricing-table ul{
                    border-width: 1px;
                    border-style: solid;
                    border-color: #CCCCCC;
                    border-radius: 3px;
                    margin: 4px;
                    text-align: center;
                    font-family: 'Arial';
                    list-style: none;
                    float: left;
                    padding: 4px;
                    background-color: #FFFFFF;
                    max-width: 24%;
                }
                .pricing-table ul li{
                    padding: 7px;
                    background-color: #EFEFEF;
                    border-width: 2px;
                    border-color: #B83737;
                    border-radius: 0px;
                    border-bottom-width: 2px;
                    font-size: 13px;
                }
                .pricing-table li:nth-child(odd){
                    background-color: transparent;
                }
                .pricing-table ul .heading{
                    color: #FFFFFF;
                    background-color: #659b2e;
                    font-size: 19px;
                    text-align: center;
                }
                .pricing-table ul .price{
                    font-size: 25px;
                }
                .pricing-table ul .action{
                    font-size: 13px;
                    color: #FFFFFF;
                }
                .pricing-table .action a{
                    border-color: #B83737;
                    border-width: 0px;
                    background-color: #659b2e;
                    padding-top: 7px;
                    padding-bottom: 7px;
                    padding-left: 15px;
                    padding-right: 15px;
                    border-style: solid;
                    color: #FFFFFF;
                }
                .pricing-table ul .noaction{
                    font-size: 13px;
                    color: #FFFFFF;
                }
                .pricing-table .noaction span{
                    border-color: #B83737;
                    border-width: 0px;
                    background-color: #b2b2b2;
                    padding-top: 7px;
                    padding-bottom: 7px;
                    padding-left: 15px;
                    padding-right: 15px;
                    border-style: solid;
                    color: #FFFFFF;
                }

            </style>
            <br>
            <div>
                <?php echo $upgradeToProHtml; ?>
            </div>
            <?php
            $content .= ob_get_clean();
            return $content;
        }

        public function displayCreativeMindsTab() {
            $content = '';
            ob_start();
            ?>
            <div>
                <a href="https://www.cminds.com/about/" class="btn button button-primary" target="_blank">Open About Us page in a new tab</a>
            </div>
            <?php
            $content .= ob_get_clean();
            return $content;
        }

        public function displayMembershipTab() {
            $content = '';
            ob_start();
            ?>
            <div>
                <a href="https://www.cminds.com/wordpress-plugins-library/cm-wordpress-plugins-yearly-membership/" class="btn button button-primary" target="_blank">Open Membership Package page in a new tab</a>
            </div>
            <?php
            $content .= ob_get_clean();
            return $content;
        }

        public function displayCMProductTab() {
            $content = '';
            ob_start();

            $currentIframeFilter = filter_input(INPUT_POST, 'cminds_iframe_filter');
            if (empty($currentIframeFilter)) {
                $currentIframeFilter = 'Plugin';
            }
            $url = remove_query_arg('cminds_iframe_filter', $_SERVER['REQUEST_URI']);
            ?>
            <div>
                <a href="https://www.cminds.com/wordpress-plugins-library/?cat=Plugin" class="btn button button-primary" target="_blank">Open Product Catalog in a new tab</a>
                <form method="post" class="cminds_iframe_filter_form">
                    <span> | Filter: </span>
                    <button type="submit" name="cminds_iframe_filter" value="Plugin" class="btn button button-primary">Plugins</button>
                    <button type="submit" name="cminds_iframe_filter" value="Add-On" class="btn button button-primary">AddOns</button>
                    <button type="submit" name="cminds_iframe_filter" value="Bundle" class="btn button button-primary">Bundles</button>
                    <button type="submit" name="cminds_iframe_filter" value="Service" class="btn button button-primary">Services</button>
                </form>
            </div>
            <?php
            $content .= ob_get_clean();
            return $content;
        }

        public function displayCMAddOnsTab() {
            $content = '';
            ob_start();
            ?>
            <div>
                <a href="https://www.cminds.com/wordpress-plugins-library/?showfilter=No&tags=Tooltip" class="btn button button-primary" target="_blank">Open Add-Ons page in a new tab</a>
            </div>
            <?php
            $content .= ob_get_clean();
            return $content;
        }

        public function displayVideoGuidesTab() {
            $content = '';
            ob_start();
            ?>
            <div>
                <a href="https://www.cminds.com/cm-plugins-video-library/" class="btn button button-primary" target="_blank">Open Video Guides page in a new tab</a>
            </div>
            <?php
            $content .= ob_get_clean();
            return $content;
        }

        public function displaySupportTab() {
            $content = '';
            $supportForumLink = $this->getOption('plugin-support-url');
            ob_start();
            ?>
            <div>
                <p>WordPress supprot forum can only be viewed on WordPress.org site. Please click on the buttom to visit the forum and to submit your support questions</p>
                <a href="<?php echo $supportForumLink; ?>" class="btn button button-primary" target="_blank">Open WordPress Support Formum</a>
            </div>
            <?php
            $content .= ob_get_clean();
            return $content;
        }

        public function add_plugin_meta_links($meta, $file) {
            if ($file == $this->getOption('plugin-basename')) {

                foreach ($meta as $key => $value) {
                    if (strpos($value, 'Visit plugin site') !== FALSE) {
                        unset($meta[$key]);
                        $meta[] = sprintf('<a href="%s">%s</a>', esc_url($this->getOption('plugin-store-url')), __('Visit plugin site', 'cminds-package')
                        );
                        break;
                    }
                }

                foreach ($meta as $key => $value) {
                    if (strpos($value, '<a href="') !== FALSE) {
                        $meta[$key] = str_replace('<a href="', '<a target="_blank" href="', $value);
                    }
                }
            }
            return $meta;
        }

        public function add_plugin_action_links($links) {
            $settingsUrl = $this->getOption('plugin-redirect-after-install');
            if (!empty($settingsUrl)) {
                $links[] = '<a href="' . esc_url($settingsUrl) . '">Settings</a>';
            }
            if (!$this->getOption('plugin-free-only')) {
                $links[] = '<a href="' . esc_url(admin_url('admin.php?page=' . $this->getProSlug())) . '">Upgrade to Pro</a>';
            }
            return $links;
        }

        public function updateMenu() {
            add_submenu_page($this->getOption('plugin-menu-item'), __('User Guide', 'cminds-package'), __('User Guide', 'cminds-package'), 'manage_options', $this->getPageSlug(), array($this, 'displayPage'));
            if (!$this->getOption('plugin-free-only')) {
                add_submenu_page($this->getOption('plugin-menu-item'), __('Upgrade&nbsp;➤', 'cminds-package'), __('Upgrade&nbsp;➤', 'cminds-package'), 'manage_options', $this->getProSlug(), array($this, 'displayPage'));
            }

            $tag = 'cminds-' . $this->getOption('plugin-short-slug') . '-license-page';
            $condition = has_action($tag);
        }

        public function getProSlug() {
            $slug = $this->getOption('plugin-abbrev') . '_pro';
            return $slug;
        }

        public function getPageSlug() {
            $slug = $this->getOption('plugin-abbrev') . '_about';
            return $slug;
        }

        public function getLicensingSlug() {
            $slug = $this->getOption('plugin-abbrev') . '_licensing';
            return $slug;
        }

        public function isOwnScreen() {
            $screen = get_current_screen();
            return (strpos($screen->base, $this->getPageSlug()) !== false || strpos($screen->base, $this->getLicensingSlug()) !== false || strpos($screen->base, $this->getProSlug()) !== false);
        }

        /**
         * Load plugin styles for admin area
         *
         * @access public
         * @since 1.0
         */
        public function enqueueAdminStyles() {

            $screen = get_current_screen();
            if (!isset($screen->id) || !$this->isOwnScreen()) {
                return;
            }
            wp_enqueue_style('jquery-ui-tabs-css', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array());
        }

        /**
         * Load plugin scripts for admin area
         *
         * @access public
         * @since 1.0
         */
        public function enqueueAdminScripts() {

            $screen = get_current_screen();
            if (!isset($screen->id) || !$this->isOwnScreen()) {
                return;
            }
            wp_enqueue_script('jquery-ui-tabs');
        }

        public function getUserguideUrl() {
            $url = '';
            if ($this->getOption('plugin-userguide-key')) {
                $url .= 'https://creativeminds.helpscoutdocs.com/article/' . $this->getOption('plugin-userguide-key');
            } else {
                $url .= 'https://creativeminds.helpscoutdocs.com/collection/3-wordpress-plugins';
            }
            return $url;
        }

        public function displayPage() {
            global $plugin_page;
            $content = '';

            global $cmindsPluginPackage;

            if (!empty($cmindsPluginPackage)) {
                foreach ($cmindsPluginPackage as $key => $package) {
                    $isown = $package->isOwnScreen();
                    if ($isown) {
                        $currentPlugin = $package;
                    }
                }
            }
            ?>
            <div class="wrap">
                <style type="text/css">
                    .subsubsub li+li:before {content:'| ';}
                    .cminds_system_info_area{
                        width: 99.5%;
                        height: 290px;
                    }
                    .cminds_update_table td,
                    .cminds_update_table tr {
                        width: 140px;
                        text-align: left;
                    }
                    .cminds_iframe_filter_form{
                        display: inline-block;
                    }
                    .cminds_iframe_filter_form > span{
                        line-height: 28px;
                    }
                </style>
                <h2>
                    <div id="icon-<?php echo $currentPlugin->getOption('plugin-icon') ?>" class="icon32">
                        <br />
                    </div>
                    <?php echo $currentPlugin->getOption('plugin-name') ?>&nbsp;
                    <?php
                    $pluginAbbrev = $currentPlugin->getOption('plugin-abbrev');
                    if (empty($_GET['page']) || $_GET['page'] != $pluginAbbrev . '_pro') :
                        ?>
                        <a href="<?php echo esc_url(get_admin_url('', 'admin.php?page=' . $pluginAbbrev . '_pro')); ?>" class="button button-primary" title="Click to Buy PRO">Upgrade to Pro</a>
                        <?php
                    endif;
                    ?>
                </h2>
                <?php
                echo $currentPlugin->showNav();

                wp_enqueue_style('cminds_package_userguide', plugin_dir_url(__FILE__) . 'css/main.css');
                wp_enqueue_style('cminds_package_userguide_font', 'https://fonts.googleapis.com/css?family=Open+Sans:400,700,300,600');

                switch ($plugin_page) {
                    default:
                    case $currentPlugin->getPageSlug(): {
                            $title = __('About', 'cminds-package');
                            wp_enqueue_script('cmdm-mailerlite', plugin_dir_url(__FILE__) . 'js/mailerlite.js', array(), $this->getOption('plugin-version'), true);
                            ob_start();
                            $base_path = $currentPlugin->getOption('plugin-dir-path');
                            include $base_path . 'package/views/userguide_free.php';
                            $content .= ob_get_clean();
                            break;
                        }
                    case $currentPlugin->getProSlug(): {
                            $title = __('Upgrade to Pro', 'cminds-package');

                            ob_start();
                            echo $currentPlugin->displayUpgradeToProTab();
                            $content .= ob_get_clean();
                            break;
                        }
                    case $currentPlugin->getLicensingSlug(): {
                            $title = __('User Guide', 'cminds-package');

                            ob_start();
                            $content .= $this->showTabs($plugin_page);
                            $content .= ob_get_clean();
                            break;
                        }
                }

                echo apply_filters('cminds-about-page-content', $content, $currentPlugin);
                ?>
            </div>
            <?php
        }

        protected function showTabs($key) {
            $class = $id = 'cminds_' . $key . '_tabs';
            ?>
            <div id="<?php echo $id; ?>" class="<?php echo $class; ?>">
                <script>
                    jQuery(document).ready(function ($) {
                        $('#<?php echo $id; ?>').tabs({
                            activate: function (event, ui) {
                                window.location.hash = ui.newPanel.attr('id').replace(/-/g, '_');
                            },
                            create: function (event, ui) {
                                var tab = location.hash.replace(/\_/g, '-');
                                if (tab.length)
                                {
                                    var tabContainer = $(ui.panel.context).find('a[href="' + tab + '"]');
                                    if (typeof tabContainer !== 'undefined' && tabContainer.length)
                                    {
                                        var index = tabContainer.parent().index();
                                        $(ui.panel.context).tabs('option', 'active', index);
                                    }
                                }
                            }
                        });
                    });
                </script>
                <?php
                $this->renderSettingsTabsControls($key);
                $this->renderSettingsTabs($key);
                ?>
            </div>
            <?php
        }

        protected function getTabsArray($key) {

            switch ($key) {
                default:
                case $this->getPageSlug():
                    $settingsTabsArrayBase = array(
                        '1'   => 'User Guide',
//						'5'		 => 'Upgrade To Pro',
                        '10'  => 'About',
                        '30'  => 'Membership',
                        '40'  => 'CM Catalog',
                        '60'  => 'Video Guides',
                        '99'  => 'WordPress Support Forum',
                        '199' => 'System Information',
                    );
                    if ($this->getOption('plugin-has-addons')) {
                        $settingsTabsArrayBase['50'] = 'Plugin Add-ons';
                    }
                    break;
                case $this->getLicensingSlug():
                    $settingsTabsArrayBase = array(
                        '10' => 'License Activation',
                        '20' => 'Check Version',
//						'30' => 'Upgrade Tutorial',
                        '40' => 'Manage Your CM Products',
                    );
                    break;
            }

            $settingsTabsArray = apply_filters('cminds-' . $key . '-tabs-array', $settingsTabsArrayBase);

            ksort($settingsTabsArray);
            return $settingsTabsArray;
        }

        /**
         * Function renders (default) or returns the setttings tabs
         *
         * @param type $return
         * @return string
         */
        protected function renderSettingsTabs($key, $return = false) {
            $content = '';
            $settingsTabsArray = $this->getTabsArray($key);

            if ($settingsTabsArray) {
                foreach ($settingsTabsArray as $tabKey => $tabLabel) {
                    $filterName = 'cminds-' . $key . '-content-' . $tabKey;

                    $content .= '<div id="tabs-' . $tabKey . '">';
                    $tabContent = apply_filters($filterName, '');
                    $content .= $tabContent;
                    $content .= '</div>';
                }
            }

            if ($return) {
                return $content;
            }
            echo $content;
        }

        /**
         * Function renders (default) or returns the setttings tabs
         *
         * @param type $return
         * @return string
         */
        protected function renderSettingsTabsControls($key, $return = false) {
            $content = '';
            $settingsTabsArray = $this->getTabsArray($key);

            if ($settingsTabsArray) {
                $content .= '<ul>';
                foreach ($settingsTabsArray as $tabKey => $tabLabel) {
                    $content .= '<li><a href="#tabs-' . $tabKey . '">' . $tabLabel . '</a></li>';
                }
                $content .= '</ul>';
            }

            if ($return) {
                return $content;
            }
            echo $content;
        }

        protected function showSharebox($mode = self::SHAREBOX_FLAT) {

            $class = (self::SHAREBOX_SQUARE === $mode) ? 'square' : 'flat';

            $pluginReviewLink = (string) $this->getOption('plugin-review-url');
            $pluginFullName = (string) $this->getOption('plugin-name');
            $pluginUrl = (string) $this->getOption('plugin-store-url');

            $twitterTweet = rawurlencode('Checkout the ' . $pluginFullName . ' ( ' . $pluginUrl . ' ) #WordPress #Plugin by @CMPLUGINS');

            ob_start();
            ?>

            <style type="text/css">

                div.cminds_call_to_action_wrapper:after{
                    content: '';
                    display: table;
                    clear: both;
                }

                #mc_embed_signup{clear:left; width:400px;}
                .fb_iframe_widget{vertical-align: top;line-height: 2em;}

                div.cminds_call_to_action.flat{
                    display: flex;
                    text-align: left;
                    float: left;
                    margin-bottom: 10px;
                }

                div.cminds_call_to_action.flat .inner_box{
                    border: 1px solid #999;
                    padding: 0 0.5em 0.5em;
                    min-width: 80px;
                    border-radius : 4px;
                    margin: 5px 0 5px 0 ;
                }

                div.cminds_call_to_action.square{
                    display: flex;
                    text-align: center;
                }

                div.cminds_call_to_action.square .inner_box{
                    border: 1px solid #999;
                    padding: 0 0.5em 0.5em;
                    margin: 0 auto;
                    min-width: 290px;
                }

                div.cminds_call_to_action.square .inner_box td{
                    display: block;
                    padding: 0;
                }

                div.cminds_call_to_action a{
                    text-decoration: none;
                }
            </style>

            <div class="cminds_call_to_action_wrapper">
                <div class="cminds_call_to_action <?php echo $class; ?>">
                    <div id="fb-root"></div>
                    <div class="inner_box">
                        <table cellpadding="10"><tr>
                                <td valign="top">
                                    <h3><?php $this->_e('Share your Appreciation'); ?></h3>
                                    <a target="_blank" href="<?php echo esc_attr($pluginReviewLink); ?>">
                                        <div class="btn button">
                                            <div class="dashicons dashicons-share-alt2"></div><span><?php $this->_e('Submit a review'); ?></span>
                                        </div>
                                    </a>
                                    <a target="_blank"  href="http://twitter.com/home/?status=<?php echo esc_attr(urlencode($twitterTweet)) ?>">
                                        <div class="btn button">
                                            <div class="dashicons dashicons-twitter"></div><span><?php $this->_e('Tweet'); ?></span>
                                        </div>
                                    </a>
                                </td><td valign="top">
                                    <h3><?php $this->_e('Stay Up-to-Date'); ?></h3>
                                    <a href="https://twitter.com/CMPLUGINS" class="twitter-follow-button" data-show-count="false" data-size="large" data-dnt="true"><?php $this->_e('Follow @CMPLUGINS'); ?></a>
                                    <script>!function (d, s, id) {
                                            var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                            if (!d.getElementById(id)) {
                                                js = d.createElement(s);
                                                js.id = id;
                                                js.src = p + '://platform.twitter.com/widgets.js';
                                                fjs.parentNode.insertBefore(js, fjs);
                                            }
                                        }(document, 'script', 'twitter-wjs');
                                    </script>

                                    <div class="g-follow" data-annotation="none" data-height="24" data-href="https://plus.google.com/108513627228464018583" data-rel="publisher"></div>

                                    <script type="text/javascript">
                                        (function () {
                                            var po = document.createElement('script');
                                            po.type = 'text/javascript';
                                            po.async = true;
                                            po.src = 'https://apis.google.com/js/platform.js';
                                            var s = document.getElementsByTagName('script')[0];
                                            s.parentNode.insertBefore(po, s);
                                        })();
                                    </script>

                                    <script>(function (d, s, id) {
                                            var js, fjs = d.getElementsByTagName(s)[0];
                                            if (d.getElementById(id))
                                                return;
                                            js = d.createElement(s);
                                            js.id = id;
                                            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
                                            fjs.parentNode.insertBefore(js, fjs);
                                        }(document, 'script', 'facebook-jssdk'));
                                    </script>

                                    <div class="fb-like" data-href="https://www.facebook.com/cmplugins" data-width="100" data-layout="button" data-action="like" data-show-faces="true" data-share="true"></div>

                                    <br/>
                                </td>
                                <td valign="top">
                                    <!-- Begin MailChimp Signup Form -->
                                    <div id="mc_embed_signup">
                                        <form action="//cminds.us3.list-manage.com/subscribe/post?u=f48254f757fafba2669ae5918&amp;id=142732cbf9" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                                            <div id="mc_embed_signup_scroll">
                                                <h3 for="mce-EMAIL"><?php $this->_e('CM Newsletter - coupons, deals, news'); ?></h3>
                                                <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
                                                <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="btn button">
                                                <span style="display:inline-block; position: relative"><div class="cmtt_field_message" title="We only send newsletters a couple of times a year. They include great deals, promo codes and information about our new plugins!"></div></span>
                                                <!-- real people should not fill this in and expect good things - do not remove this or risk fsorm bot signups-->
                                                <div style="position: absolute; left: -5000px;"><input type="text" name="b_f48254f757fafba2669ae5918_142732cbf9" tabindex="-1" value=""></div>
                                                <div class="clear"></div>
                                            </div>
                                        </form>
                                    </div>
                                    <!--End mc_embed_signup-->
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            ob_end_flush();
        }

        /**
         * Function returns the ads for the users who volunteered to register (optional step)
         * It communicates with our own server to download the current promotions
         * 
         * @return boolean
         */
        protected function getAds() {
            $connectionProblem = false;
            $ads_arr = get_transient('cminds_free_ads');
            if (empty($ads_arr) && !is_array($ads_arr)) {
                $args = array(
                    'body' => array()
                );
                $href = 'https://www.cminds.com/wp-admin/admin-ajax.php?action=get_ads&cminds_json_api=get_ads';
                $response = wp_remote_post($href, $args);
                if (!is_wp_error($response)) {
                    $ads = json_decode(wp_remote_retrieve_body($response), true);
                } else {
                    $args['sslverify'] = false;
                    $href = 'https://www.cminds.com/wp-admin/admin-ajax.php?action=get_ads&cminds_json_api=get_ads';
                    $response = wp_remote_post($href, $args);
                    if (!is_wp_error($response)) {
                        $ads = json_decode(wp_remote_retrieve_body($response), true);
                    } else {
                        $ads = array();
                        $connectionProblem = true;
                    }
                }
                $ads_arr = array(
                    'ads'          => $ads,
                    'refresh_time' => time(),
                    'connection'   => !$connectionProblem,
                );
                set_transient('cminds_free_ads', $ads_arr, self::ADS_REFRESH_INTERVAL);
            }
            /*
             * Update from old version
             */
            if (!isset($ads_arr['ads']) && !isset($ads_arr['refresh_time'])) {
                $temp_ads_arr = $ads_arr;
                $ads_arr = array(
                    'ads'          => $temp_ads_arr,
                    'refresh_time' => strtotime('-10 DAYS'),
                    'connection'   => TRUE,
                );
            }
            /*
             * Update from old version
             */
            if (!isset($ads_arr['connection'])) {
                $ads_arr['connection'] = TRUE;
            }
            return $ads_arr;
        }

        public function getStoreUrl($args = array()) {
            $category = isset($args['category']) ? $args['category'] : 'All';
            $storeUrl = $this->addAffiliateCode($this->getCategoryLink('Wordpress', 'category', $category));
            return esc_url($storeUrl);
        }

        public function getCategoryLink($group, $type, $name) {
            $categoryLinks = array(
                'Wordpress' => array(
                    'category' => array(
                        'All'     => '/wordpress-plugins-library/',
                        'Plugin'  => '/wordpress-plugins/',
                        'Service' => '/wordpress-maintenance-services/',
                        'Add-On'  => '/wordpress-add-ons/',
                        'Bundle'  => '/wordpress-plugins-bundles/'),
                    'tags'     => array(
                        'SEO'        => '/wordpress-seo-content-plugins/',
                        'Business'   => '/plugins-for-wordpress-business-websites/',
                        'Publishing' => '/wordpress-plugins-for-content-publishing/',
                        'Free'       => '/free-wordpress-plugins/',
                        'eCommerce'  => '/wordpress-e-commerce-plugins/',
                        'Marketing'  => '/wordpress-plugins-for-marketers/',
                        'Admin'      => '/admin-wordpress-plugins/',
                        'Community'  => '/wordpress-community-plugins/',
                        'eLearning'  => '/wordpress-e-learning-and-lms-plugins/',
                    )
                ),
                'Magento'   => array(
                    'category' => array(
                        'All'       => '/magento-extensions-and-modules/',
                        'Extension' => '/ecommerce-extensions-store/',
                        'Service'   => '/support-and-maintenance-services-for-magento/',
                        'Bundle'    => '/extensions-bundles-magento/'),
                    'tags'     => array(
                        'Customer'     => '/magento-customer-care-support/',
                        'Integrations' => '/magento-third-party-integration-extensions/',
                        'Marketing'    => '/magento-marketing-extensions/',
                        'Marketplace'  => '/magento-marketplace-extensions/',
                        'Marketplaces' => '/magento-marketplace-extensions/',
                        'Utilities'    => '/magento-utilities-extensions/',
                        'Magento-2'    => '/magento-2-extensions/',
                        '2.0'          => '/magento-2-extensions/'
                    )
                )
            );

            $link = null;
            $types = array('category', 'tags');
            if (in_array($type, $types)) {
                $link = isset($categoryLinks[$group][$type][$name]) ? $categoryLinks[$group][$type][$name] : null;
            }

            if (!is_string($link)) {
                $link = ('Wordpress' === $group) ? '/wordpress-plugins-library/?' . $type . '=' . $name : '/magento-extensions-and-modules/?' . $type . '=' . $name;
            }

            $link = 'https://www.cminds.com' . $link;
            return $link;
        }

        public function addAffiliateCode($link) {
            if ($this->getOption('plugin-affiliate')) {
                $link = add_query_arg(array('af' => $this->getOption('plugin-affiliate')), $link);
            }
            return esc_url($link);
        }

        public function showGuide($atts = array()) {
            global $cmindsPluginPackage;

            $atts = shortcode_atts(array('id' => null), $atts);
            $currentPlugin = !empty($atts['id']) ? $cmindsPluginPackage[$atts['id']] : $this;

            $optionName = 'cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-guide-hidden';
            $guideHide = filter_input(INPUT_GET, 'cminds_guide_hide');
            if ($guideHide) {
                update_option($optionName, 1);
            }
            $guideShow = filter_input(INPUT_GET, 'cminds_guide_show');
            if ($guideShow) {
                update_option($optionName, 0);
            }
            $guideHidden = get_option($optionName, 0);

            $showGuide = $currentPlugin->getOption('plugin-show-guide');
            if ($showGuide) :
                ob_start();
                ?><style type="text/css">

                    div.cminds_guide_wrapper {
                        display: inline-block;
                        padding: 0em 0em 1em 1em;
                        background: #FFF;
                        margin: 0em 0em 0 0;
                        vertical-align: top;
                    }

                    div.cminds_guide_wrapper * {
                        vertical-align: top;
                    }

                    .cminds_guide{
                        display: inline-block;
                        margin: 1em;
                        padding: 1em;
                    }

                    .cminds_guide_text{
                        display: inline-block;
                        margin: 1em;
                    }

                    .cminds_guide_text > span{
                        text-align: left;
                        display: block;
                    }

                    .clear, .clearfix{
                        clear: both;
                    }

                    .cmclearfix{
                        clear: both !important;
                    }

                    .cminds_guide .guide_header{
                        font-size: 14pt;
                        font-weight: bold;
                    }

                    .cminds_guide .guide_text{
                        display: inline-block;
                        width: 509px;
                        max-width: 100%;
                        margin-right: 40px;
                    }

                    .cminds_guide .guide_videos{
                        display: inline-block;
                        max-width: 100%;
                        overflow: hidden;
                    }

                    .cminds_guide .guide_videos .guide_videos_inner{
                    }

                    .cminds_guide .guide_videos > div{
                        display: inline-block;
                    }
                    .cminds_guide .guide_videos > div.guide_videos_after{
                        display: block;
                        margin: 10px 0 0 52px;
                    }
                    .prev_video,
                    .next_video {
                        margin-top: 85px;
                    }

                    .guide_video_title {
                        font-size: 13pt;
                        font-weight: bold;
                        margin: 0 0 10px 0px;
                    }

                    .cminds_link.blue {
                        color: #fff;
                        border-color: #33ace7;
                        background: #66c1ed;
                        -webkit-box-shadow: 0 1px 0 #ccc;
                        box-shadow: 0 1px 0 #ccc;
                        display: inline-block;
                        text-decoration: none;
                        line-height: 26px;
                        height: 28px;
                        padding: 0 10px 1px;
                        cursor: pointer;
                        border-width: 1px;
                        border-style: solid;
                        -webkit-border-radius: 3px;
                        border-radius: 3px;
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                    }

                    .cminds_guide .guide_videos .guide_video{
                        display: none;
                    }
                    .cminds_guide .guide_videos .guide_video.active{
                        display: inline-block;
                    }

                    .cminds_guide .guide_video_content > a{
                        display: block;
                    }
                    .install-guide-video {max-height: 160px;}
                    .upgrage-guide-video {max-height: 255px;}
                </style>
                <?php
                if (!$guideHidden) :
                    ?>
                    <script>
                        jQuery(document).ready(function () {

                            jQuery.fn.visible = function () {
                                return this.css('visibility', 'visible');
                            };

                            jQuery.fn.invisible = function () {
                                return this.css('visibility', 'hidden');
                            };

                            jQuery.fn.visibilityToggle = function () {
                                return this.css('visibility', function (i, visibility) {
                                    return (visibility === 'visible') ? 'hidden' : 'visible';
                                });
                            };
                            var cminds_video_prev_next_toggle = function () {
                                var prevVisible = jQuery('.guide_videos .guide_video.active').prev('.guide_video').length;
                                var nextVisible = jQuery('.guide_videos .guide_video.active').next('.guide_video').length;

                                if (prevVisible) {
                                    jQuery('.guide_videos .prev_video').visible();
                                } else {
                                    jQuery('.guide_videos .prev_video').invisible();
                                }

                                if (nextVisible) {
                                    jQuery('.guide_videos .next_video').visible();
                                } else {
                                    jQuery('.guide_videos .next_video').invisible();
                                }
                            };

                            cminds_video_prev_next_toggle();

                            jQuery('.guide_videos .prev_video').on('click', function () {
                                var prevVideo = jQuery('.guide_videos .guide_video.active').prev('.guide_video');
                                if (prevVideo.length)
                                {
                                    jQuery('.guide_videos .guide_video.active').removeClass('active');
                                    prevVideo.addClass('active');
                                }
                                cminds_video_prev_next_toggle();
                            });

                            jQuery('.guide_videos .next_video').on('click', function () {
                                var nextVideo = jQuery('.guide_videos .guide_video.active').next('.guide_video');
                                if (nextVideo.length)
                                {
                                    jQuery('.guide_videos .guide_video.active').removeClass('active');
                                    nextVideo.addClass('active');
                                }
                                cminds_video_prev_next_toggle();
                            });
                        });
                    </script>
                    <div class="clear clearfix cmclearfix"></div>
                    <div class="cminds_guide_wrapper">
                        <div class="cminds_guide">

                            <?php
                            $guideText = $currentPlugin->getOption('plugin-guide-text');
                            if (!empty($guideText)) :
                                ?>
                                <div class="guide_text">
                                    <div class="guide_header">
                                        Initial Installation Guide
                                    </div>
                                    <?php echo $guideText; ?>
                                </div>
                            <?php endif; ?>

                            <?php
                            $videos = $currentPlugin->getOption('plugin-guide-videos');
                            if (!empty($videos) && is_array($videos)) :
                                ?>
                                <div class="guide_videos">
                                    <div class="prev_video cminds_link blue" style="visibility: hidden">Prev</div>
                                    <div class="guide_videos_inner">
                                        <?php foreach ($videos as $key => $video) : ?>
                                            <div class="guide_video <?php echo!$key ? 'active' : ''; ?>">
                                                <div class="guide_video_title"><?php echo $video['title']; ?></div>
                                                <?php
                                                // echo do_shortcode('[video src="https://player.vimeo.com/video/' . $video['video_id'] . '?portrait=0&responsive=0&maxwidth=290&maxheight=160&player_id=player_2" width="290" height="160" id="palyer_2"][/video]');
                                                echo $GLOBALS['wp_embed']->run_shortcode('[embed width="290" height="255"]https://player.vimeo.com/video/' . $video['video_id'] . '[/embed]');
//														echo wp_video_shortcode(array('src' =>"https://player.vimeo.com/video/" . $video['video_id'] . "?portrait=0&responsive=1&maxheight=160&player_id=player_2", 'width' => 463, 'height' => 255, 'class' => "install-guide-video wp-video-shortcode", 'id' => "palyer_2"));
                                                ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="next_video cminds_link blue" style="visibility: hidden">Next</div>
                                    <div class="guide_videos_after">
                                        <a href="<?php echo $currentPlugin->getPageUrl('about'); ?>" class="cminds_link blue" target="_blank">Open Plugin User Guide</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="clear clearfix cmclearfix"></div>
                <?php else : ?>
                    <div class="cminds_guide_wrapper">
                        <div>
                            <a class="cminds-guide-hide-button button" href="<?php echo add_query_arg(array('cminds_guide_show' => 1), remove_query_arg('cminds_guide_hide')); ?>">Show Installation Guide Box</a>
                        </div>
                    </div>
                    <div class="clear clearfix cmclearfix"></div>
                <?php
                endif;
                ?>

                <?php
                $content = ob_get_clean();
                return $content;
            endif;
        }

        public function showActivation($atts = array()) {
            global $cmindsPluginPackage;

            $atts = shortcode_atts(array('id' => null), $atts);
            $currentPlugin = !empty($atts['id']) ? $cmindsPluginPackage[$atts['id']] : $this;

            ob_start();
            $isRegistered = $currentPlugin->isRegistered();
            $isSkipped = $currentPlugin->isSkipped();
            $isRegistered = false;
            if (!$isRegistered) :
                include_once 'views/registration.php';
            else:
                ?>
                <br/>
                <br/>
            <?php
            endif;
            $content = ob_get_clean();
            return $content;
        }

        public function time_to_decimal($time) {
            $timeArr = explode(':', $time);
            $decTime = ($timeArr[0] * 60) + ($timeArr[1]);
            if (isset($timeArr[2])) {
                $decTime += $timeArr[2] / 60;
            }
            return $decTime;
        }

        public function showUpgrade($atts = array()) {
            global $cmindsPluginPackage;

            $atts = shortcode_atts(array('id' => null), $atts);
            $currentPlugin = !empty($atts['id']) ? $cmindsPluginPackage[$atts['id']] : $this;

            $optionName = 'cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-upgrade-hidden';
            $upgradeHide = filter_input(INPUT_GET, 'cminds_upgrade_hide');
            if ($upgradeHide) {
                update_option($optionName, 1);
            }
            $upgradeShow = filter_input(INPUT_GET, 'cminds_upgrade_show');
            if ($upgradeShow) {
                update_option($optionName, 0);
            }
            $upgradeHidden = get_option($optionName, 0);
            $isRegistered = $currentPlugin->isRegistered();

            $showUpgrade = $currentPlugin->getOption('plugin-show-upgrade', TRUE);
            if ($showUpgrade) :
                ob_start();
                ?><style type="text/css">

                    div.cminds_upgrade_wrapper {
                        display: block;
                        padding: 0em 0em 1em 1em;
                        background: #FFF;
                        margin: 0em 1em 0 0;
                        vertical-align: top;
                    }

                    div.cminds_upgrade_wrapper * {
                        vertical-align: top;
                    }

                    .cminds_upgrade{
                        display: inline-block;
                        margin: 1em;
                        padding: 1em;
                        width: 100%;
                    }

                    .cminds_upgrade_text{
                        display: inline-block;
                        margin: 1em;
                    }

                    .cminds_upgrade_text > span{
                        text-align: left;
                        display: block;
                    }

                    .clear, .clearfix{
                        clear: both;
                    }

                    .cmclearfix{
                        clear: both !important;
                    }

                    .cminds_upgrade .upgrade_header{
                        font-size: 14pt;
                        font-weight: bold;
                    }

                    .cminds_upgrade .upgrade_text{
                        display: inline-block;
                        max-width: 45%;
                        margin-right: 40px;
                    }

                    .cminds_upgrade .upgrade_videos{
                        display: inline-flex;
                        max-width: 50%;
                        min-width: 520px;
                        overflow: hidden;
                    }

                    .cminds_upgrade .upgrade_videos .upgrade_videos_inner{
                    }

                    .cminds_upgrade .upgrade_videos > div{
                        display: inline-block;
                    }
                    .cminds_upgrade .upgrade_videos > div.upgrade_videos_after{
                        display: block;
                        margin: 10px 0 0 0px;
                    }
                    .prev_video,
                    .next_video {
                        margin-top: 15px;
                    }

                    .upgrade_video_title {
                        font-size: 13pt;
                        font-weight: bold;
                        margin: 0 0 10px 0px;
                    }

                    .cminds_link.blue {
                        color: #fff;
                        border-color: #33ace7;
                        background: #66c1ed;
                        -webkit-box-shadow: 0 1px 0 #ccc;
                        box-shadow: 0 1px 0 #ccc;
                        display: inline-block;
                        text-decoration: none;
                        line-height: 26px;
                        height: 28px;
                        padding: 0 10px 1px;
                        cursor: pointer;
                        border-width: 1px;
                        border-style: solid;
                        -webkit-border-radius: 3px;
                        border-radius: 3px;
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                    }

                    .cminds_upgrade .upgrade_videos .upgrade_video{
                        display: none;
                    }
                    .cminds_upgrade .upgrade_videos .upgrade_video.active{
                        display: inline-block;
                    }

                    .cminds_upgrade .upgrade_video_content > a{
                        display: block;
                    }
                </style>
                <?php
                if (!$upgradeHidden) :
                    ?>
                    <script>
                        jQuery(document).ready(function () {

                            jQuery.fn.visible = function () {
                                return this.css('visibility', 'visible');
                            };

                            jQuery.fn.invisible = function () {
                                return this.css('visibility', 'hidden');
                            };

                            jQuery.fn.visibilityToggle = function () {
                                return this.css('visibility', function (i, visibility) {
                                    return (visibility === 'visible') ? 'hidden' : 'visible';
                                });
                            };
                            var cminds_video_prev_next_toggle = function () {
                                var prevVisible = jQuery('.upgrade_videos .upgrade_video.active').prev('.upgrade_video').length;
                                var nextVisible = jQuery('.upgrade_videos .upgrade_video.active').next('.upgrade_video').length;

                                if (prevVisible) {
                                    jQuery('.upgrade_videos .prev_video').visible();
                                } else {
                                    jQuery('.upgrade_videos .prev_video').invisible();
                                }

                                if (nextVisible) {
                                    jQuery('.upgrade_videos .next_video').visible();
                                } else {
                                    jQuery('.upgrade_videos .next_video').invisible();
                                }
                            };

                            cminds_video_prev_next_toggle();

                            jQuery('.upgrade_videos .prev_video').on('click', function () {
                                var prevVideo = jQuery('.upgrade_videos .upgrade_video.active').prev('.upgrade_video');
                                if (prevVideo.length)
                                {
                                    jQuery('.upgrade_videos .upgrade_video.active').removeClass('active');
                                    prevVideo.addClass('active');
                                }
                                cminds_video_prev_next_toggle();
                            });

                            jQuery('.upgrade_videos .next_video').on('click', function () {
                                var nextVideo = jQuery('.upgrade_videos .upgrade_video.active').next('.upgrade_video');
                                if (nextVideo.length)
                                {
                                    jQuery('.upgrade_videos .upgrade_video.active').removeClass('active');
                                    nextVideo.addClass('active');
                                }
                                cminds_video_prev_next_toggle();
                            });
                        });</script>
                    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__); ?>js/froogaloop.js"></script>
                    <script>
                        jQuery(document).ready(function ( ) {
                            // Message displayed when link is playing
                            var links = jQuery('.cm-video-bookmark-link');
                            var playNotice = ' - <strong><i><spam style="color:darkgreen;">Now playing</span></i></strong>';
                            // Function to hide all status messages
                            hidePlayNoticeAll = function ( ) {
                                jQuery('.cm-video-bookmark-link .link_status').html("");
                            }

                    //                             Load Vimeo API for the embedded video
                            var iframe_player = jQuery('.upgrade_video_content iframe')[0];
                            var frogaloopPlayer = $f(iframe_player);
                            // Option listeners for pause, finish, and playProgress
                            //
                            // Note: If you want to use this, you must define the functions or links wont work
                            frogaloopPlayer.addEvent('ready', function ( ) {
                                frogaloopPlayer.addEvent('pause', onPause);
                                frogaloopPlayer.addEvent('finish', onFinish);
                                frogaloopPlayer.addEvent('playProgress', onPlayProgress);
                            });

                            // Functions for each listener event
                            function onPause(id) { // When the video is paused, do this.
                                jQuery(".main_status").html('Paused');
                            }

                            function onFinish(id) { // When the video is finished, do this.
                                jQuery(".main_status").html('Finished');
                            }

                            function onPlayProgress(data, id) { // While the video is playing, do this.
                                jQuery(".main_status").html('Playing - ' + data.seconds + 's played'); // data.percent can also be used.
                            }

                            // Function to control what happens when each lesson link is clicked
                            function setupLinks( ) {

                                links.on('click', function ( ) {
                                    var track = jQuery(this).data('timestamp'); //Timestamp stored as a data attribute

                                    hidePlayNoticeAll( ); // Hide all status messages before displaying (to prevent them from sticking)
                                    if (true === jQuery(this).data('active')) {
                                        frogaloopPlayer.api('pause'); //Play the video
                                        jQuery(this).data('active', false);
                                    } else {
                                        frogaloopPlayer.api('play'); //Play the video
                                        frogaloopPlayer.api('seekTo', track); //Seek to the number of seconds in the variable link_1_track
                                        jQuery(this).find('.link_status').html(playNotice); //Display status message (playNotice) within span with class link_1_status
                                        jQuery(this).data('active', true);
                                    }
                                });
                            }
                            setupLinks( );
                        });</script>
                    <div class="clear clearfix cmclearfix"></div>
                    <div class="cminds_upgrade_wrapper">
                        <div class="cminds_upgrade">

                            <?php
                            $upgradeText = $currentPlugin->getOption('plugin-upgrade-text');
                            if (!empty($upgradeText)) :
                                ?>
                                <div class="upgrade_text">
                                    <div class="upgrade_header">
                                        Why you Should Upgrade
                                    </div>
                                    <div style="display:block">
                                        <p><?php echo $upgradeText; ?></p>
                                        <div class="links">

                                            <?php
                                            $upgradel = $currentPlugin->getOption('plugin-upgrade-text-list');
                                            if (!empty($upgradel) && is_array($upgradel)) :
                                                foreach ($upgradel as $key => $listitem) :
                                                    $linknum = $key + 1;
                                                    echo $listitem['video_time'];
                                                    if ('More' !== $listitem['video_time']) :
                                                        ?>
                                                        - <a class="cm-video-bookmark-link link_<?php echo $linknum; ?>" data-timestamp="<?php echo $this->time_to_decimal($listitem['video_time']); ?>" href="javascript:void(0);" target="_self"><?php echo $listitem['title']; ?><span class="link_status"></span></a>
                                                    <?php else: ?>
                                                        - <span class="cm-video-bookmark bookmark_<?php echo $linknum; ?>" style="color:green;"><?php echo $listitem['title']; ?></span>
                                                    <?php endif; ?>
                                                    <br/>
                                                <?php endforeach; ?>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php
                            $videos = $currentPlugin->getOption('plugin-upgrade-videos');
                            if (!empty($videos) && is_array($videos)) :
                                ?>
                                <div class="upgrade_videos">
                                    <div class="prev_video cminds_link blue" style="visibility: hidden">Prev</div>
                                    <div class="upgrade_videos_inner">
                                        <?php
                                        foreach ($videos as $key => $video) :
                                            if (!empty($video)) :
                                                ?>

                                                <div class="upgrade_video <?php echo!$key ? 'active' : ''; ?>">
                                                    <div class="upgrade_video_title"><?php echo $video['title']; ?></div>
                                                    <div class="upgrade_video_content">
                                                        <?php
                                                        echo $GLOBALS['wp_embed']->run_shortcode('[embed width="390" height="255"]https://player.vimeo.com/video/' . $video['video_id'] . '[/embed]');
                                                        ?>
                                                        <br /><br /><a href="<?php echo esc_url(get_admin_url('', 'admin.php?page=' . $currentPlugin->getOption('plugin-abbrev') . '_pro')); ?>" class="button button-primary" style="background-color:green; width:220px;" title="Click to Buy PRO">Show me Upgrade Options >></a>

                                                    </div>
                                                </div>
                                                <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>                    </div>
                    <?php
                    echo $this->showAds($atts);
                    ?>
                    <div class="clear clearfix cmclearfix"></div>
                <?php else : ?>
                    <div class="cminds_upgrade_wrapper">
                        <div>
                            <a class="cminds-upgrade-hide-button button" href="<?php echo add_query_arg(array('cminds_upgrade_show' => 1), remove_query_arg('cminds_upgrade_hide')); ?>">Why you shoud have the Premium Edition>></a>
                        </div>
                    </div>
                    <div class="clear clearfix cmclearfix"></div>
                <?php
                endif;
                ?>

                <?php
                $content = ob_get_clean();
                return $content;
            endif;
        }

        protected function getDaysSinceLastRefresh($ads_arr) {
            if (!is_array($ads_arr) && empty($ads_arr['refresh_time'])) {
                return null;
            }

            $now = time();
            $your_date = intval($ads_arr['refresh_time']);
            $datediff = $now - $your_date;
            $days = abs(floor($datediff / (60 * 60 * 24)));
            return $days;
        }

        public function showAds($atts = array()) {
            global $cmindsPluginPackage;

            $atts = shortcode_atts(array(
                'flat' => false,
                'id'   => null
                    ), $atts);

            $currentPlugin = !empty($atts['id']) ? $cmindsPluginPackage[$atts['id']] : $this;

            $optionName = 'cminds-' . $this->getOption('plugin-short-slug') . '-ads-hidden';
            $adsHide = filter_input(INPUT_GET, 'cminds_ad_hide');
            if ($adsHide) {
                update_option($optionName, 1);
            }
            $adsShow = filter_input(INPUT_GET, 'cminds_ad_show');
            if ($adsShow) {
                update_option($optionName, 0);
            }
            $adsHidden = get_option($optionName, 1);
            $adsRefreshed = filter_input(INPUT_GET, 'cminds_ad_refresh');
            if ($adsRefreshed) {
                delete_transient('cminds_free_ads');
            }
            $ads_arr = $this->getAds();
            $ads = isset($ads_arr['ads']) ? $ads_arr['ads'] : array();
            $days_since_last_refresh = $this->getDaysSinceLastRefresh($ads_arr);

            $isRegistered = $currentPlugin->isRegistered();
            /*
             * Don't display if there's no server connection
             */
            if (!$ads_arr['connection']) {
                return;
            }
            ob_start();
            ?>

            <style type="text/css">

                div.cminds_ads_wrapper {
                    display: inline-block;
                    padding: 1em;
                    background: #FFF;
                    border: solid 1px #E0E0E0;
                    margin: 1em 1em 0 0;
                    vertical-align: top;
                }

                .cminds_ads_internal_wrapper {
                    display: inline-block;
                    overflow: auto;
                    max-height: 175px;
                }

                .cminds_ad{
                    display: block;
                    margin: 0.5em;
                    vertical-align: top;
                }

                span.ad_code {
                    font-weight: bold;
                }

                .cminds_ad.hidden{
                    display: none;
                }
                .cminds_ads_wrapper.hidden{
                    display: none;
                }

                .cminds_ad_text{
                    display: block;
                    margin-bottom: 1em;
                }
                .cminds_ad_link{
                    text-align: center;
                }

                span.ads_refreshed{
                    color: #259602;
                    font-weight: bold;
                }

                .cminds_ad_text > a:before {
                    content:"\A"; white-space:pre;
                }

                .cminds_ad_text > *:after {
                    content:"\A"; white-space:pre;
                }

                .cminds_no_ads{
                    margin: 1em 0;
                    font-size: 12pt;
                    font-weight: bold;
                }

                .cminds_links{
                    display: inline-block;
                    margin: 1em;
                    padding: 0;
                    max-width: 150px;
                    min-height: 90px;
                    vertical-align: top;
                    text-align: left;
                }

                .cminds_links .cminds_link{
                    margin: 5px 0;
                }

                .cminds_links .cminds_link:first-child{
                    margin-top: 0;
                }

                .cminds_link{
                    color: #555;
                    border-color: rgba(204,204,204,0.8);
                    background: #f7f7f7;
                    -webkit-box-shadow: 0 1px 0 #ccc;
                    box-shadow: 0 1px 0 #ccc;
                    display: inline-block;
                    text-decoration: none;
                    line-height: 26px;
                    height: 28px;
                    padding: 0 10px 1px;
                    cursor: pointer;
                    border-width: 1px;
                    border-style: solid;
                    -webkit-border-radius: 3px;
                    border-radius: 3px;
                    -webkit-box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    box-sizing: border-box;
                }

                .cminds_link:active {
                    background: #eee;
                    border-color: #999;
                    -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    -webkit-transform: translateY(1px);
                    -ms-transform: translateY(1px);
                    transform: translateY(1px);
                }

                .cminds_link:hover {
                    background: #eee;
                    border-color: #999;
                    -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    -webkit-transform: translateY(1px);
                    -ms-transform: translateY(1px);
                    transform: translateY(1px);
                }

                .cminds_link.blue{
                    color: #fff;
                    border-color: #33ace7;
                    background: #66c1ed;
                    -webkit-box-shadow: 0 1px 0 #ccc;
                    box-shadow: 0 1px 0 #ccc;
                    display: inline-block;
                    text-decoration: none;
                    line-height: 26px;
                    height: 28px;
                    padding: 0 10px 1px;
                    cursor: pointer;
                    border-width: 1px;
                    border-style: solid;
                    -webkit-border-radius: 3px;
                    border-radius: 3px;
                    -webkit-box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    box-sizing: border-box;

                }

                .cminds_link.blue:active {
                    background: #0198e1;
                    border-color: #66c1ed;
                    -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    -webkit-transform: translateY(1px);
                    -ms-transform: translateY(1px);
                    transform: translateY(1px);
                }

                .cminds_link.blue:hover {
                    background: #005b87;
                    border-color: #0198e1;
                    -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    -webkit-transform: translateY(1px);
                    -ms-transform: translateY(1px);
                    transform: translateY(1px);
                }

                .cminds_link.orange{
                    color: #fff;
                    border-color: #ffb752;
                    background: #ffb752;
                    -webkit-box-shadow: 0 1px 0 #ccc;
                    box-shadow: 0 1px 0 #ccc;
                    display: inline-block;
                    text-decoration: none;
                    line-height: 26px;
                    height: 28px;
                    padding: 0 10px 1px;
                    cursor: pointer;
                    border-width: 1px;
                    border-style: solid;
                    -webkit-border-radius: 3px;
                    border-radius: 3px;
                    -webkit-box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    box-sizing: border-box;

                }

                .cminds_link.orange:active {
                    background: #e5a449;
                    border-color: #ffc574;
                    -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    -webkit-transform: translateY(1px);
                    -ms-transform: translateY(1px);
                    transform: translateY(1px);
                }

                .cminds_link.orange:hover {
                    background: #e5a449;
                    border-color: #ffc574;
                    -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                    -webkit-transform: translateY(1px);
                    -ms-transform: translateY(1px);
                    transform: translateY(1px);
                }

                .cminds_more{
                    display: inline-block;
                    margin: 1em;
                    vertical-align: top;
                    font-size: 12pt;
                    line-height: 123px;
                }

                .cminds_more > *{
                    cursor: pointer;
                }

                .cminds_link.cminds_more_ads {
                    -moz-border-radius: 11px;
                    -webkit-border-radius: 11px;
                    border-radius: 11px;
                    border: 1px solid #66c1ed;
                    font-size: 23px;
                    padding: 8px 14px;
                    text-decoration: none;
                    line-height: 23px;
                    height: auto;
                }

                .clear, .clearfix{
                    clear:both;
                }

                .cmclearfix{
                    clear: both !important;
                }
            </style>

            <?php if ($isRegistered) : ?>

                <?php if (!$atts['flat']) : ?>

                    <script>
                        jQuery(document).ready(function ( ) {
                            jQuery('.cminds-ads-show-button').on('click', function (e) {
                                e.preventDefault( );
                                jQuery('#cminds_offers_wrapper').toggleClass('hidden');
                                jQuery('#cminds_links_wrapper').toggleClass('hidden');
                            });
                        });</script>

                    <h3>Special offers from CreativeMinds</h3>

                    <div class="cminds_ads_wrapper_white">
                        <a href="https://www.cminds.com/wordpress-plugins-library/seo-keyword-hound-wordpress/" target="_blank"><img src="<?php echo plugin_dir_url(__FILE__); ?>views/keywordhoundbanner.jpg"></a>
                        <span style="margin-left:20px;">
                            <a class="" href="https://www.cminds.com/wordpress-plugins-library/cm-wordpress-plugins-yearly-membership/" target="_blank"><img src="<?php echo plugin_dir_url(__FILE__); ?>views/99suite.png"></a>
                        </span>
                    </div>


                    <?php if (!empty($ads)) : ?>
                        <div class="cminds_ads_internal_wrapper">
                            <?php foreach ($ads as $index => $ad) : ?>
                                <div class="cminds_ad">
                                    <?php
                                    $dateUntil = date('jS F, Y', strtotime($ad['ad_valid_date']));
                                    printf('Receive %s discount for <a target="_blank" href="%s">%s</a> with discount code <span class="ad_code">"%s"</span> valid until %s', $ad['ad_discount'], $ad['ad_product_url'], $ad['ad_title'], $ad['ad_code'], $dateUntil);
                                    ?>
                                    <span class="cminds_ad_link">
                                        <a class="cminds_link blue" target="_blank" href="<?php echo $this->addAffiliateCode($ad['ad_url']); ?>">Redeem Offer</a>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="cminds_no_ads">
                            <span class="cminds_no_ads_text">
                                Currently we have no special discounts.
                            </span>
                        </div>
                    <?php endif; ?>

                    <div>
                        <?php if ($adsRefreshed) :
                            ?>
                            <span class="ads_refreshed">
                                Discounts have been refreshed.
                            </span>
                        <?php else : ?>
                            You haven't refreshed the discounts for <?php echo $days_since_last_refresh . ' ' . _n('day', 'days', $days_since_last_refresh); ?>. <a class="cminds-ads-refresh-button" href="<?php echo add_query_arg(array('cminds_ad_refresh' => 1)); ?>"><strong>Refresh now!</strong></a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <ul class="cminds_ads_list_wrapper">
                        <?php foreach ($ads as $index => $ad) : ?>
                            <li class="cminds_ads_list_item">								<?php
                                $dateUntil = date('Y-m-d', strtotime($ad['ad_valid_date']));
                                printf('Receive %s discount for <a target="_blank" href="%s">%s</a> with discount code "%s" valid until %s', $ad['ad_discount'], $ad['ad_product_url'], $ad['ad_title'], $ad['ad_code'], $dateUntil);
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <h3>Special Offers from CreativeMinds</h3>

                <p>
                    <a href="javascript:void(0)" id="cminds-unskip">Complete <?php echo esc_attr($currentPlugin->getOption('plugin-name')); ?> registration</a> and receive special offers
                </p>
            <?php endif; ?>
            <div class="clear clearfix cmclearfix"></div>
            <?php
            $content = ob_get_clean();
            return $content;
        }

        public function getRegistrationFields() {
            $fields = array(
                'product_name'   => $this->getOption('plugin-name'),
                'remote_url'     => get_bloginfo('wpurl'),
                'remote_ip'      => $_SERVER['SERVER_ADDR'],
                'remote_country' => '',
                'remote_city'    => '',
                'email'          => get_bloginfo('admin_email'),
                'hostname'       => get_bloginfo('wpurl'),
                'username'       => '',
            );

            $output = '';

            foreach ($fields as $key => $value) {
                $output .= sprintf('<input type="hidden" name="%s" value="%s" />', $key, $value);
            }

            return $output;
        }

        public function isSkipped($id = null) {
            global $cmindsPluginPackage;
            $currentPlugin = !empty($id) ? $cmindsPluginPackage[$id] : $this;
            $isSkipped = get_option('cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-registration-skipped');
            return $isSkipped;
        }

        public function isRegistered($id = null) {
            global $cmindsPluginPackage;
            $currentPlugin = !empty($id) ? $cmindsPluginPackage[$id] : $this;
            $isRegistered = get_option('cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-registered');
            return $isRegistered;
        }

        public function wasRegistered($id = null) {
            global $cmindsPluginPackage;
            $currentPlugin = !empty($id) ? $cmindsPluginPackage[$id] : $this;
            $wasRegistered = get_option('cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-was-registered');
            return $wasRegistered;
        }

        public function setWasRegistered($id = null) {
            global $cmindsPluginPackage;
            $currentPlugin = !empty($id) ? $cmindsPluginPackage[$id] : $this;
            $optionName = 'cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-was-registered';
            $result = update_option($optionName, 1);
            return $result;
        }

        /**
         * Function comminicates with our server to remove the user from our database (right to be forgotten)
         * @global array $cmindsPluginPackage
         * @param type $atts
         * @return type
         */
        public function deregisterUser($atts = array()) {
            global $cmindsPluginPackage;

            $atts = shortcode_atts(array('id' => null), $atts);
            $currentPlugin = !empty($atts['id']) ? $cmindsPluginPackage[$atts['id']] : $this;

            $optionName = 'cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-registered';
            $post = filter_input_array(INPUT_POST);
            if (!empty($post) && !empty($post['cminds_nonce'])) {

                $nonceCheck = wp_verify_nonce($post['cminds_nonce'], 'cminds_register_free');
                if ($nonceCheck) {

                    if ($post['product_name'] != $currentPlugin->getOption('plugin-name')) {
                        return;
                    }

                    unset($post['cminds_nonce']);
                    $jsonData = wp_json_encode(array($post));

                    $args = array(
                        'body' => array(
                            'jsonData' => $jsonData
                        )
                    );
                    $href = 'https://www.cminds.com/wp-admin/admin-ajax.php?action=remove_user&cminds_json_api=remove_user';
                    $response = wp_remote_post($href, $args);
                    if (!is_wp_error($response)) {
                        $result = json_decode(wp_remote_retrieve_body($response), true);
                        if ($result && 1 === $result['result']) {
                            update_option($optionName, 0);
                        }
                    } else {
                        $args['sslverify'] = false;
                        $href = 'http://www.cminds.com/wp-admin/admin-ajax.php?action=remove_user&cminds_json_api=remove_user';
                        $response = wp_remote_post($href, $args);
                        if (!is_wp_error($response)) {
                            $result = json_decode(wp_remote_retrieve_body($response), true);
                            if ($result && 1 === $result['result']) {
                                update_option($optionName, 0);
                            }
                        } else {
                            $message = 'User has requested the deregistration: <br/><table>';
                            foreach ($post as $key => $value) {
                                if (!in_array($key, array('product_name', 'email', 'hostname'))) {
                                    continue;
                                }
                                $message .= '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
                            }
                            $message .= '</table>';

                            add_filter('wp_mail_content_type', array(__CLASS__, 'cminds_set_content_type'));
                            wp_mail('marketing@cminds.com', 'CM Free Product Registration Canceled', $message);
                            remove_filter('wp_mail_content_type', array(__CLASS__, 'cminds_set_content_type'));
                        }
                    }
                    update_option($optionName, 0);
                }
            }
        }

        public function skipRegistration($atts = array()) {
            global $cmindsPluginPackage;

            $atts = shortcode_atts(array('id' => null), $atts);
            $currentPlugin = !empty($atts['id']) ? $cmindsPluginPackage[$atts['id']] : $this;

            $optionName = 'cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-registration-skipped';
            update_option($optionName, 1);
        }

        public function unskipRegistration($atts = array()) {
            global $cmindsPluginPackage;

            $atts = shortcode_atts(array('id' => null), $atts);
            $currentPlugin = !empty($atts['id']) ? $cmindsPluginPackage[$atts['id']] : $this;

            $optionName = 'cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-registration-skipped';
            update_option($optionName, 0);
        }

        /**
         * Function communicates with our server to register user's e-mail in our database.
         * It's an optional step, user can easily skip it. We only collect e-mail and the url of the site.
         * User is being informed about the data being collected and the reason why it's collected.
         * @global array $cmindsPluginPackage
         * @param type $atts
         * @return type
         */
        public function registerUser($atts = array()) {
            global $cmindsPluginPackage;

            $atts = shortcode_atts(array('id' => null), $atts);
            $currentPlugin = !empty($atts['id']) ? $cmindsPluginPackage[$atts['id']] : $this;

            $optionName = 'cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-registered';
            $post = filter_input_array(INPUT_POST);
            if (!empty($post) && !empty($post['cminds_nonce'])) {

                $nonceCheck = wp_verify_nonce($post['cminds_nonce'], 'cminds_register_free');
                if ($nonceCheck) {

                    if ($post['product_name'] != $currentPlugin->getOption('plugin-name')) {
                        return;
                    }

                    unset($post['cminds_nonce']);
                    $jsonData = wp_json_encode(array($post));

                    $args = array(
                        'body' => array(
                            'jsonData' => $jsonData
                        )
                    );
                    $href = 'https://www.cminds.com/wp-admin/admin-ajax.php?action=add_user&cminds_json_api=add_user';
                    $response = wp_remote_post($href, $args);
                    if (!is_wp_error($response)) {
                        $result = json_decode(wp_remote_retrieve_body($response), true);
                        if ($result && 1 === $result['result']) {
                            update_option($optionName, 1);
                            $currentPlugin->setWasRegistered();
                        }
                    } else {
                        $args['sslverify'] = false;
                        $href = 'http://www.cminds.com/wp-admin/admin-ajax.php?action=add_user&cminds_json_api=add_user';
                        $response = wp_remote_post($href, $args);
                        if (!is_wp_error($response)) {
                            $result = json_decode(wp_remote_retrieve_body($response), true);
                            if ($result && 1 === $result['result']) {
                                update_option($optionName, 1);
                                $currentPlugin->setWasRegistered();
                            }
                        } else {
                            $message = 'Registered fields: <br/><table>';
                            foreach ($post as $key => $value) {
                                if (!in_array($key, array('product_name', 'email', 'hostname'))) {
                                    continue;
                                }
                                $message .= '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
                            }
                            $message .= '</table>';

                            add_filter('wp_mail_content_type', array(__CLASS__, 'cminds_set_content_type'));
                            wp_mail('marketing@cminds.com', 'CM Free Product Registration', $message);
                            remove_filter('wp_mail_content_type', array(__CLASS__, 'cminds_set_content_type'));
                        }
                    }
                }
            }
        }

        public function showRegistration($atts = array()) {
            global $cmindsPluginPackage;

            $atts = shortcode_atts(array('id' => null), $atts);
            $currentPlugin = !empty($atts['id']) ? $cmindsPluginPackage[$atts['id']] : $this;

            $optionName = 'cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-registration-hidden';
            $registrationHide = filter_input(INPUT_GET, 'cminds_registration_hide');
            if ($registrationHide) {
                update_option($optionName, 1);
            }
            $registrationShow = filter_input(INPUT_GET, 'cminds_registration_show');
            if ($registrationShow) {
                delete_option($optionName);
            }
            $registrationHidden = get_option($optionName);

            $this->registerUser($atts);

            $optionName = 'cminds-' . $currentPlugin->getOption('plugin-short-slug') . '-registered';
            $isRegistered = $this->isRegistered();

            if ($isRegistered) :
                return $this->showAds($atts);
            else :
                ob_start();
                ?>
                <style type="text/css">

                    div.cminds_registration_wrapper {
                        display: inline-block;
                        padding: 1em;
                        background: #FFF;
                        margin: 1em 1em 0 0;
                        width: 1004px;
                    }

                    div.cminds_registration_wrapper * {
                        vertical-align: top;
                    }

                    .cminds_registration{
                        display: inline-block;
                        margin: 1em;
                        padding: 1em 58px;
                    }

                    .cminds_registration form{
                        display: inline-block;
                        margin: 1em;
                    }
                    .cminds_registration form .no-registration{
                        margin: 5px 0 0;
                        text-align: center;
                    }

                    .cminds_registration form input[type="submit"]{
                        font-size: 16pt;
                        padding: 7px;
                        height: auto;
                    }

                    .cminds_registration_text{
                        display: inline-block;
                        margin: 1em;
                    }

                    .cminds_registration_text > span{
                        text-align: left;
                        display: block;
                    }

                    .cminds_registration_text .row_1{
                        font-size: 12pt;
                        padding-bottom: 5px;
                    }
                    .cminds_registration_text .row_2{
                        font-size: 10pt;
                    }

                    .cminds_link{
                        color: #555;
                        border-color: rgba(204,204,204,0.8);
                        background: #f7f7f7;
                        -webkit-box-shadow: 0 1px 0 #ccc;
                        box-shadow: 0 1px 0 #ccc;
                        display: inline-block;
                        text-decoration: none;
                        line-height: 26px;
                        height: 28px;
                        padding: 0 10px 1px;
                        cursor: pointer;
                        border-width: 1px;
                        border-style: solid;
                        -webkit-border-radius: 3px;
                        border-radius: 3px;
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                    }

                    .cminds_link:active {
                        background: #eee;
                        border-color: #999;
                        -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        -webkit-transform: translateY(1px);
                        -ms-transform: translateY(1px);
                        transform: translateY(1px);
                    }

                    .cminds_link:hover {
                        background: #eee;
                        border-color: #999;
                        -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        -webkit-transform: translateY(1px);
                        -ms-transform: translateY(1px);
                        transform: translateY(1px);
                    }

                    .cminds_link.blue{
                        color: #fff;
                        border-color: #33ace7;
                        background: #66c1ed;
                        -webkit-box-shadow: 0 1px 0 #ccc;
                        box-shadow: 0 1px 0 #ccc;
                        display: inline-block;
                        text-decoration: none;
                        line-height: 26px;
                        height: 28px;
                        padding: 0 10px 1px;
                        cursor: pointer;
                        border-width: 1px;
                        border-style: solid;
                        -webkit-border-radius: 3px;
                        border-radius: 3px;
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;

                    }

                    .cminds_link.blue:active {
                        background: #0198e1;
                        border-color: #66c1ed;
                        -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        -webkit-transform: translateY(1px);
                        -ms-transform: translateY(1px);
                        transform: translateY(1px);
                    }

                    .cminds_link.blue:hover {
                        background: #005b87;
                        border-color: #0198e1;
                        -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        -webkit-transform: translateY(1px);
                        -ms-transform: translateY(1px);
                        transform: translateY(1px);
                    }

                    .cminds_link.orange{
                        color: #fff;
                        border-color: #ffb752;
                        background: #ffb752;
                        -webkit-box-shadow: 0 1px 0 #ccc;
                        box-shadow: 0 1px 0 #ccc;
                        display: inline-block;
                        text-decoration: none;
                        line-height: 26px;
                        height: 28px;
                        padding: 0 10px 1px;
                        cursor: pointer;
                        border-width: 1px;
                        border-style: solid;
                        -webkit-border-radius: 3px;
                        border-radius: 3px;
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;

                    }

                    .cminds_link.orange:active {
                        background: #e5a449;
                        border-color: #ffc574;
                        -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        -webkit-transform: translateY(1px);
                        -ms-transform: translateY(1px);
                        transform: translateY(1px);
                    }

                    .cminds_link.orange:hover {
                        background: #e5a449;
                        border-color: #ffc574;
                        -webkit-box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);
                        -webkit-transform: translateY(1px);
                        -ms-transform: translateY(1px);
                        transform: translateY(1px);
                    }

                    .clear, .clearfix{
                        clear:both;
                    }
                    .cmclearfix{
                        clear:both !important;
                    }

                </style>

                <?php if (!$registrationHidden) : ?>

                    <div class="cminds_registration_wrapper">
                        <div class="cminds_registration">
                            <table><tr>
                                    <td>
                                        <form method="post" action="">
                                            <?php
                                            wp_nonce_field('cminds_register_free', 'cminds_nonce');
                                            echo $currentPlugin->getRegistrationFields();
                                            ?>
                                            <input class="button button-primary" style="background-color:green;" type="submit" value="Register Your Copy" />
                                            <div class="no-registration">
                                                <a class="cminds-registration-hide-button button" href="<?php echo add_query_arg(array('cminds_registration_hide' => 1), remove_query_arg('cminds_registration_show')); ?>">I don't want to register now</a>
                                            </div>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="cminds_registration_text">
                                            <span class="row_1">
                                                Once registered, you will receive updates and special offers.
                                            </span>
                                            <span class="row_2">
                                                <p>We will only send once, your administrator's e-mail and site URL to CreativeMinds server.<br /> No additional information will be ever collected or sent.</p>
                                            </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    </div>
                <?php else : ?>
                    <div class="cminds_registration_wrapper">
                        <div>
                            <a class="cminds-registration-hide-button button" href="<?php echo add_query_arg(array('cminds_registration_show' => 1), remove_query_arg('cminds_registration_hide')); ?>">Show registration box</a>
                            <a href="<?php echo $this->getStoreUrl(); ?>" class="cminds_link orange" target="_blank">View all CreativeMidns Plugins</a>
                            <a href="<?php echo $this->getStoreUrl(array('category' => 'Bundle')); ?>" class="cminds_link blue" target="_blank">View Bundles</a>
                            <a href="<?php echo $this->getStoreUrl(array('category' => 'Add-On')); ?>" class="cminds_link blue" target="_blank">View Add-Ons</a>
                            <a href="<?php echo $this->getStoreUrl(array('category' => 'Service')); ?>" class="cminds_link blue" target="_blank">View Services</a>
                        </div>
                    </div>
                <?php
                endif;
                echo $this->showUpgrade($atts);
                ?>
                <div class="clear clearfix cmclearfix"></div>
                <?php
                $content = ob_get_clean();
                return $content;
            endif;
        }

        /**
         * Displays the horizontal navigation bar
         * @global string $submenu
         * @global type $plugin_page
         */
        protected function showNav() {

            global $self, $plugin_page, $typenow, $submenu;

            $submenus = array();
            $menuItem = $this->getOption('plugin-menu-item');
            $pluginAbbrev = $this->getOption('plugin-abbrev');

            ob_start();

            if (isset($submenu[$menuItem])) {

                $thisMenu = $submenu[$menuItem];

                foreach ($thisMenu as $sub_item) {

                    $slug = $sub_item[2];

                    // Handle current for post_type=post|page|foo pages, which won't match $self.
                    $self_type = !empty($typenow) ? $self . '?post_type=' . $typenow : 'nothing';

                    $isCurrent = FALSE;

                    $subpageUrl = get_admin_url('', 'admin.php?page=' . $slug);

                    if ((!isset($plugin_page) && $self == $slug) || ( isset($plugin_page) && $plugin_page == $slug && ( $menuItem == $self_type || $menuItem == $self || file_exists($menuItem) === false ))) {
                        $isCurrent = TRUE;
                    }

                    $url = ( strpos($slug, '.php') !== false || strpos($slug, 'http://') !== false || strpos($slug, 'https://') !== false ) ? $slug : $subpageUrl;

                    $isExternal = ( $slug === $url ) ? TRUE : FALSE;

                    /*
                     * Don't show on the Upgrade page itself
                     */
                    if (!empty($_GET['page']) && $_GET['page'] == $pluginAbbrev . '_pro' && $slug == $pluginAbbrev . '_pro') {
                        continue;
                    }

                    $submenus[] = array(
                        'link'     => $url,
                        'title'    => $sub_item[0],
                        'current'  => $isCurrent,
                        'external' => $isExternal
                    );
                }
                ?>
                <div id="cmhandfsl_admin_nav">
                    <ul class="subsubsub">
                        <?php foreach ($submenus as $menu): ?>

                            <li><a href="<?php echo esc_attr($menu['link']); ?>" <?php echo ( $menu['current'] ) ? 'class="current"' : ''; ?> <?php
                                if ($menu['external']) {
                                    echo 'target="_blank"';
                                }
                                ?>><?php echo esc_html($menu['title']); ?>
                                </a></li>
                        <?php endforeach; ?>
                    </ul>
                    <br class="clear" />
                </div>
                <?php
            }

            $nav = ob_get_contents();
            ob_end_clean();
            return $nav;
        }

        public function cminds_compatibility_check($plaintext = false) {
            global $wp_version;
            $upload_max = ini_get('upload_max_filesize') ? ini_get('upload_max_filesize') : 'N/A';
            $post_max = ini_get('post_max_size') ? ini_get('post_max_size') : 'N/A';
            $memory_limit = ini_get('memory_limit') ? ini_get('memory_limit') : 'N/A';
            $allow_url_fopen = ini_get('allow_url_fopen') ? ini_get('allow_url_fopen') : 'N/A';
            $max_execution_time = ini_get('max_execution_time') !== FALSE ? ini_get('max_execution_time') : 'N/A';
            $cURL = function_exists('curl_version') ? 'On' : 'Off';
            $mb_support = function_exists('mb_strtolower') ? 'On' : 'Off';
            $intl_support = extension_loaded('intl') ? 'On' : 'Off';
            $iconv_support = extension_loaded('iconv') ? 'On' : 'Off';

            $permalink_structure = get_option('permalink_structure');
            $permalinksurl = self_admin_url('options-permalink.php');

            $content = '';
            $namespace = explode('\\', __NAMESPACE__);
            ob_start();
            if (!$plaintext):
                ?>
                <span class="description" style="">
                    The information in this table is useful to check if the plugin might have some incompabilities with you server.
                </span>
                <table class="form-table server-info-table">
                    <tr>
                        <td>Package Version</td>
                        <td><?php echo str_replace(array('v', '_'), array('', '.'), end($namespace)); ?></td>
                        <td><span>OK</span></td>
                    </tr>
                    <tr>
                        <td>WordPress Version</td>
                        <td><?php echo $wp_version; ?></td>
                        <td><?php if (version_compare($wp_version, '3.3.0', '<')): ?><strong>The minimum supported version of WordPress is 3.3</strong><?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>Permalinks enabled</td>
                        <td><?php echo ($permalink_structure) ? 'Yes' : 'No' ?></td>
                        <td><?php if (empty($permalink_structure)): ?><strong>Please enable the permalinks. Go to <a href="<?php echo $permalinksurl; ?>">Settings</a></strong><?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP Version</td>
                        <td><?php echo phpversion(); ?></td>
                        <td><?php if (version_compare(phpversion(), '5.3.0', '<')): ?><strong>Recommended 5.3 or higher</strong><?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>mbstring support</td>
                        <td><?php echo $mb_support; ?></td>
                        <td><?php if ($mb_support == 'Off'): ?>
                                <strong>"mbstring" library is required for plugin to work.</strong>
                            <?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>intl support</td>
                        <td><?php echo $intl_support; ?></td>
                        <td><?php if ($intl_support == 'Off'): ?>
                                <strong>"intl" library is required for proper sorting of accented (non-ASCII) characters.</strong>
                            <?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>iconv support</td>
                        <td><?php echo $iconv_support; ?></td>
                        <td><?php if ($iconv_support == 'Off'): ?>
                                <strong>"iconv" library is required for converting non-UTF-8 characters.</strong>
                            <?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP Memory Limit</td>
                        <td><?php echo $memory_limit; ?></td>
                        <td><?php if (self::cminds_units2bytes($memory_limit) < 1024 * 1024 * 128): ?>
                                <strong>This value can be too low for a site with big glossary.</strong>
                            <?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP Max Upload Size</td>
                        <td><?php echo $upload_max; ?></td>
                        <td><?php if (self::cminds_units2bytes($upload_max) < 1024 * 1024 * 5): ?>
                                <strong>This value can be too low to import large files.</strong>
                            <?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP Max Post Size</td>
                        <td><?php echo $post_max; ?></td>
                        <td><?php if (self::cminds_units2bytes($post_max) < 1024 * 1024 * 5): ?>
                                <strong>This value can be too low to import large files.</strong>
                            <?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP Max Execution Time </td>
                        <td><?php echo $max_execution_time; ?></td>
                        <td><?php if ($max_execution_time != 0 && $max_execution_time < 300): ?>
                                <strong>This value can be too low for lengthy operations. We strongly suggest setting this value to at least 300 or 0 which is no limit.</strong>
                            <?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP cURL</td>
                        <td><?php echo $cURL; ?></td>
                        <td><?php if ($cURL == 'Off'): ?>
                                <strong>cURL library is required to check if remote audio file exists.</strong>
                            <?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td>PHP allow_url_fopen</td>
                        <td><?php echo $allow_url_fopen; ?></td>
                        <td><?php if ($allow_url_fopen == '0'): ?>
                                <strong>allow_url_fopen is required to connect to the Merriam-Webster and Wikipedia API.</strong>
                            <?php else: ?><span>OK</span><?php endif; ?></td>
                    </tr>
                </table>
                <?php
            else:
                ?>
                Package Version		<?php echo str_replace(array('v', '_'), array('', '.'), end($namespace)); ?>    OK

                WordPress Version		<?php echo $wp_version; ?>
                <?php if (version_compare($wp_version, '3.3.0', '<')): ?>The minimum supported version of WordPress is 3.3<?php else: ?>OK<?php endif; ?>

                Permalinks enabled		<?php echo ($permalink_structure) ? 'Yes' : 'No' ?>
                <?php if (empty($permalink_structure)): ?>Please enable the permalinks. Go to <a href="<?php echo $permalinksurl; ?>">Settings</a><?php else: ?>OK<?php endif; ?>

                PHP Version		<?php echo phpversion(); ?>
                <?php if (version_compare(phpversion(), '5.3.0', '<')): ?>Recommended 5.3 or higher<?php else: ?>OK<?php endif; ?>

                mbstring support		<?php echo $mb_support; ?>
                <?php if ($mb_support == 'Off'): ?>
                    "mbstring" library is required for plugin to work.
                <?php else: ?>OK<?php endif; ?>

                intl support		<?php echo $intl_support; ?>
                <?php if ($intl_support == 'Off'): ?>
                    "intl" library is required for proper sorting of accented characters on Glossary Index page.
                <?php else: ?>OK<?php endif; ?>

                PHP Memory Limit		<?php echo $memory_limit; ?>
                <?php if (self::cminds_units2bytes($memory_limit) < 1024 * 1024 * 128): ?>
                    This value can be too low for a site with big glossary.
                <?php else: ?>OK<?php endif; ?>

                PHP Max Upload Size		<?php echo $upload_max; ?>
                <?php if (self::cminds_units2bytes($upload_max) < 1024 * 1024 * 5): ?>
                    This value can be too low to import large files.
                <?php else: ?>OK<?php endif; ?>

                PHP Max Post Size		<?php echo $post_max; ?>
                <?php if (self::cminds_units2bytes($post_max) < 1024 * 1024 * 5): ?>
                    This value can be too low to import large files.
                <?php else: ?>OK<?php endif; ?>

                PHP Max Execution Time		<?php echo $max_execution_time; ?>
                <?php if ($max_execution_time != 0 && $max_execution_time < 300): ?>
                    This value can be too low for lengthy operations. We strongly suggest setting this value to at least 300 or 0 which is no limit.
                <?php else: ?>OK<?php endif; ?>

                PHP cURL		<?php echo $cURL; ?>
                <?php if ($cURL == 'Off'): ?>
                    cURL library is required to check if remote audio file exists.
                <?php else: ?>OK<?php endif; ?>

                PHP allow_url_fopen		<?php echo $allow_url_fopen; ?>
                <?php if ($allow_url_fopen == '0'): ?>
                    allow_url_fopen is required to connect to the Merriam-Webster and Wikipedia API.
                <?php else: ?>OK<?php endif; ?>

            <?php
            endif;
            $content .= ob_get_clean();
            return $content;
        }

        /**
         * System info
         *
         * Shows the system info panel which contains version data and debug info.
         * The data for the system info is generated by the Browser class.
         *
         * @since 1.4
         * @global $wpdb
         * @global object $wpdb Used to query the database using the WordPress
         *   Database API
         * @return void
         */
        public function cminds_system_info() {
            ?>
            <div class="">
                <h2><?php __('System Information', 'cminds-package'); ?></h2><br/>
                <form action="<?php echo esc_url(admin_url('admin.php?page=cmtt_licensing')); ?>" method="post" dir="ltr">
                    <p class="submit">
                        <input type="hidden" name="cminds_action" value="download_sysinfo" />
                        <?php submit_button('Download system information file', 'primary', 'cminds-download-sysinfo', false); ?>
                    </p>
                    <textarea class="cminds_system_info_area" readonly="readonly" onclick="this.focus( );
                                        this.select( )" id="system-info-textarea" name="cminds-sysinfo" title="<?php _e('To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'edd'); ?>">
                        <?php $this->cminds_system_info_content(); ?>
                    </textarea>
                </form>
            </div>
            <?php
        }

        /**
         * Generates the System Info Download File
         *
         * @since 1.4
         * @return void
         */
        public function cminds_system_info_content() {
            global $wpdb;

            if (get_bloginfo('version') < '3.4') {
                $theme_data = get_theme_data(get_stylesheet_directory() . '/style.css');
                $theme = $theme_data['Name'] . ' ' . $theme_data['Version'];
            } else {
                $theme_data = wp_get_theme();
                $theme = $theme_data->Name . ' ' . $theme_data->Version;
            }

            // Try to identifty the hosting provider
            $host = false;
            if (defined('WPE_APIKEY')) {
                $host = 'WP Engine';
            } elseif (defined('PAGELYBIN')) {
                $host = 'Pagely';
            }
            $namespace = explode('\\', __NAMESPACE__);
            ?>
            ### Begin System Info ###

            ## Please include this information when posting support requests ##

            <?php do_action('cminds_system_info_before'); ?>

            Package Version:                <?php echo str_replace(array('v', '_'), array('', '.'), end($namespace)); ?>

            Multisite:                <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

            SITE_URL:                 <?php echo site_url() . "\n"; ?>
            HOME_URL:                 <?php echo home_url() . "\n"; ?>

            WordPress Version:        <?php echo get_bloginfo('version') . "\n"; ?>
            Permalink Structure:      <?php echo get_option('permalink_structure') . "\n"; ?>
            Active Theme:             <?php echo $theme . "\n"; ?>
            <?php if ($host) : ?>
                Host:                     <?php echo $host . "\n"; ?>
            <?php endif; ?>

            Registered Post Stati:    <?php echo implode(', ', get_post_stati()) . "\n\n"; ?>

            PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
            MySQL Version:            <?php echo (function_exists('mysqli_get_server_info')) ? mysqli_get_server_info($wpdb->dbh) . "\n" : 'N/A' . "\n"; ?>
            Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

            WordPress Memory Limit:   <?php echo WP_MEMORY_LIMIT ?><?php echo "\n"; ?>
            PHP Memory Limit:         <?php echo ini_get('memory_limit') . "\n"; ?>
            PHP Upload Max Size:      <?php echo ini_get('upload_max_filesize') . "\n"; ?>
            PHP Post Max Size:        <?php echo ini_get('post_max_size') . "\n"; ?>
            PHP Upload Max Filesize:  <?php echo ini_get('upload_max_filesize') . "\n"; ?>
            PHP Time Limit:           <?php echo ini_get('max_execution_time') . "\n"; ?>
            PHP Max Input Vars:       <?php echo ini_get('max_input_vars') . "\n"; ?>
            PHP Arg Separator:        <?php echo ini_get('arg_separator.output') . "\n"; ?>
            PHP Allow URL File Open:  <?php echo ini_get('allow_url_fopen') ? "Yes" : "No\n"; ?>

            WP_DEBUG:                 <?php echo defined('WP_DEBUG') ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>

            WP Table Prefix:          <?php
            echo "Length: " . strlen($wpdb->prefix);
            echo " Status:";
            if (strlen($wpdb->prefix) > 16) {
                echo " ERROR: Too Long";
            } else {
                echo " Acceptable";
            } echo "\n";
            ?>

            Show On Front:            <?php echo get_option('show_on_front') . "\n" ?>
            Page On Front:            <?php
            $id = get_option('page_on_front');
            echo get_the_title($id) . ' (#' . $id . ')' . "\n"
            ?>
            Page For Posts:           <?php
            $id = get_option('page_for_posts');
            echo get_the_title($id) . ' (#' . $id . ')' . "\n"
            ?>

            <?php
            $request['cmd'] = '_notify-validate';

            $params = array(
                'sslverify'  => false,
                'timeout'    => 60,
                'user-agent' => 'Cminds/' . $this->getOption('plugin-version'),
                'body'       => $request
            );

            $response = wp_remote_post('https://www.paypal.com/cgi-bin/webscr', $params);

            if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300) {
                $WP_REMOTE_POST = 'wp_remote_post() works' . "\n";
            } else {
                $WP_REMOTE_POST = 'wp_remote_post() does not work' . "\n";
            }
            ?>
            WP Remote Post:           <?php echo $WP_REMOTE_POST; ?>

            Session:                  <?php echo isset($_SESSION) ? 'Enabled' : 'Disabled'; ?><?php echo "\n"; ?>
            Session Name:             <?php echo esc_html(ini_get('session.name')); ?><?php echo "\n"; ?>
            Cookie Path:              <?php echo esc_html(ini_get('session.cookie_path')); ?><?php echo "\n"; ?>
            Save Path:                <?php echo esc_html(ini_get('session.save_path')); ?><?php echo "\n"; ?>
            Use Cookies:              <?php echo ini_get('session.use_cookies') ? 'On' : 'Off'; ?><?php echo "\n"; ?>
            Use Only Cookies:         <?php echo ini_get('session.use_only_cookies') ? 'On' : 'Off'; ?><?php echo "\n"; ?>

            DISPLAY ERRORS:           <?php echo ( ini_get('display_errors') ) ? 'On (' . ini_get('display_errors') . ')' : 'N/A'; ?><?php echo "\n"; ?>
            FSOCKOPEN:                <?php echo ( function_exists('fsockopen') ) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.'; ?><?php echo "\n"; ?>
            cURL:                     <?php echo ( function_exists('curl_init') ) ? 'Your server supports cURL.' : 'Your server does not support cURL.'; ?><?php echo "\n"; ?>
            SOAP Client:              <?php echo ( class_exists('SoapClient') ) ? 'Your server has the SOAP Client enabled.' : 'Your server does not have the SOAP Client enabled.'; ?><?php echo "\n"; ?>
            SUHOSIN:                  <?php echo ( extension_loaded('suhosin') ) ? 'Your server has SUHOSIN installed.' : 'Your server does not have SUHOSIN installed.'; ?><?php echo "\n"; ?>

            ##COMPATIBILITY

            <?php echo $this->cminds_compatibility_check(true); ?>

            ##ACTIVE PLUGINS:

            <?php
            $plugins = get_plugins();
            $active_plugins = get_option('active_plugins', array());

            foreach ($plugins as $plugin_path => $plugin) {
                // If the plugin isn't active, don't show it.
                if (!in_array($plugin_path, $active_plugins))
                    continue;

                echo $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
            }

            if (is_multisite()) :
                ?>

                NETWORK ACTIVE PLUGINS:

                <?php
                $plugins = wp_get_active_network_plugins();
                $active_plugins = get_site_option('active_sitewide_plugins', array());

                foreach ($plugins as $plugin_path) {
                    $plugin_base = plugin_basename($plugin_path);

                    // If the plugin isn't active, don't show it.
                    if (!array_key_exists($plugin_base, $active_plugins))
                        continue;

                    $plugin = get_plugin_data($plugin_path);

                    echo $plugin['Name'] . ' :' . $plugin['Version'] . "\n";
                }

            endif;
            do_action('cminds_system_info_after');
            ?>
            ### End System Info ###
            <?php
        }

        /**
         * Generates the System Info Download File
         *
         * @since 1.4
         * @return void
         */
        public function cminds_generate_sysinfo_download() {
            nocache_headers();

            header("Content-type: text/plain");
            header('Content-Disposition: attachment; filename="cminds-system-info.txt"');

            echo wp_strip_all_tags($_POST['cminds-sysinfo']);
            die();
        }

        public function getOption($key, $default = NULL) {
            $value = isset($this->config[$key]) ? $this->config[$key] : $default;
            return $value;
        }

        public function getPageUrl($pageBase = 'licensing') {
            $pluginMenuPageBase = $this->pluginMenu;
            $abbrev = $this->getOption('plugin-is-addon') ? $this->getOption('plugin-parent-abbrev') : $this->getOption('plugin-abbrev');
            if (empty($abbrev)) {
                /*
                 * Abbrev not set for the plugin try to guess
                 */
                $abbrev = substr($this->getOption('plugin-abbrev'), 0, -1);
            }
            $page = $abbrev . '_' . $pageBase;
            if (FALSE === strpos($pluginMenuPageBase, '.php')) {
                $pageUrl = esc_attr(add_query_arg(urlencode_deep(array('page' => $page)), admin_url('admin.php')));
            } else {
                $pageUrl = esc_attr(add_query_arg(urlencode_deep(array('page' => $page)), admin_url($pluginMenuPageBase)));
            }
            return $pageUrl;
        }

        /**
         * Converts the Apache memory values to number of bytes ini_get('upload_max_filesize') or ini_get('post_max_size')
         * @param type $str
         * @return type
         */
        public static function cminds_units2bytes($str) {
            $units = array('B', 'K', 'M', 'G', 'T');
            $unit = preg_replace('/[0-9]/', '', $str);
            $unitFactor = array_search(strtoupper($unit), $units);
            if ($unitFactor !== false) {
                return preg_replace('/[a-z]/i', '', $str) * pow(2, 10 * $unitFactor);
            }
        }

        public static function cminds_set_content_type() {
            return "text/html";
        }

    }

}

/*
 * Load the config
 */
global $cmindsPluginPackage;
include('cminds-plugin-config.php');
$cmindsPluginPackage[$cminds_plugin_config['plugin-abbrev']] = new CmindsFreePackage($cminds_plugin_config);
