<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Allows plugins to use their own update API.
 *
 * @author Easy Digital Downloads
 * @version 1.6.13
 */
if( !class_exists('TRP_EDD_SL_Plugin_Updater') ) {
    class TRP_EDD_SL_Plugin_Updater
    {

        private $api_url = '';
        private $api_data = array();
        private $name = '';
        private $slug = '';
        private $version = '';
        private $wp_override = false;
        private $cache_key = '';
        private $beta = '';

        /**
         * Class constructor.
         *
         * @uses plugin_basename()
         * @uses hook()
         *
         * @param string $_api_url The URL pointing to the custom API endpoint.
         * @param string $_plugin_file Path to the plugin file.
         * @param array $_api_data Optional data to send with API calls.
         */
        public function __construct($_api_url, $_plugin_file, $_api_data = null)
        {

            global $edd_plugin_data;

            $this->api_url = trailingslashit($_api_url);
            $this->api_data = $_api_data;
            $this->name = plugin_basename($_plugin_file);
            $this->slug = basename($_plugin_file, '.php');

            // IMPORTANT TranslatePress modification.
            if ( $this->slug === 'index') {
                // $this->slug is the add-on file name. For Deepl and Translator accounts the file name is 'index' causing a conflict.
                $this->slug = dirname( plugin_basename( $_plugin_file ) );
            }
            // end modification

            $this->version = $_api_data['version'];
            $this->wp_override = isset($_api_data['wp_override']) ? (bool)$_api_data['wp_override'] : false;
            $this->beta = !empty($this->api_data['beta']) ? true : false;
            $this->cache_key = md5(serialize($this->slug . $this->api_data['license'] . $this->beta));

            $edd_plugin_data[$this->slug] = $this->api_data;

            // Set up hooks.
            $this->init();

        }

        /**
         * Set up WordPress filters to hook into WP's update process.
         *
         * @uses add_filter()
         *
         * @return void
         */
        public function init()
        {

            add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
            add_filter('plugins_api', array($this, 'plugins_api_filter'), 10, 3);
            remove_action('after_plugin_row_' . $this->name, 'wp_plugin_update_row', 10);
            add_action('after_plugin_row_' . $this->name, array($this, 'show_update_notification'), 10, 2);
            add_action('admin_init', array($this, 'show_changelog'));

        }

        /**
         * Check for Updates at the defined API endpoint and modify the update array.
         *
         * This function dives into the update API just when WordPress creates its update array,
         * then adds a custom API call and injects the custom plugin data retrieved from the API.
         * It is reassembled from parts of the native WordPress plugin update code.
         * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
         *
         * @uses api_request()
         *
         * @param array $_transient_data Update array build by WordPress.
         * @return array Modified update array with custom plugin data.
         */
        public function check_update($_transient_data)
        {

            global $pagenow;

            if (!is_object($_transient_data)) {
                $_transient_data = new stdClass;
            }

            if ('plugins.php' == $pagenow && is_multisite()) {
                return $_transient_data;
            }

            if (!empty($_transient_data->response) && !empty($_transient_data->response[$this->name]) && false === $this->wp_override) {
                return $_transient_data;
            }

            $version_info = $this->get_cached_version_info();

            if (false === $version_info) {
                $version_info = $this->api_request('plugin_latest_version', array('slug' => $this->slug, 'beta' => $this->beta));

                $this->set_version_info_cache($version_info);

            }

            if (false !== $version_info && is_object($version_info) && isset($version_info->new_version)) {

                if (version_compare($this->version, $version_info->new_version, '<')) {

                    $_transient_data->response[$this->name] = $version_info;

                }

                $_transient_data->last_checked = current_time('timestamp');
                $_transient_data->checked[$this->name] = $this->version;

            }

            return $_transient_data;
        }

        /**
         * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
         *
         * @param string $file
         * @param array $plugin
         */
        public function show_update_notification($file, $plugin)
        {

            if (is_network_admin()) {
                return;
            }

            if (!current_user_can('update_plugins')) {
                return;
            }

            if (!is_multisite()) {
                return;
            }

            if ($this->name != $file) {
                return;
            }

            // Remove our filter on the site transient
            remove_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'), 10);

            $update_cache = get_site_transient('update_plugins');

            $update_cache = is_object($update_cache) ? $update_cache : new stdClass();

            if (empty($update_cache->response) || empty($update_cache->response[$this->name])) {

                $version_info = $this->get_cached_version_info();

                if (false === $version_info) {
                    $version_info = $this->api_request('plugin_latest_version', array('slug' => $this->slug, 'beta' => $this->beta));

                    $this->set_version_info_cache($version_info);
                }

                if (!is_object($version_info)) {
                    return;
                }

                if (version_compare($this->version, $version_info->new_version, '<')) {

                    $update_cache->response[$this->name] = $version_info;

                }

                $update_cache->last_checked = current_time('timestamp');
                $update_cache->checked[$this->name] = $this->version;

                set_site_transient('update_plugins', $update_cache);

            } else {

                $version_info = $update_cache->response[$this->name];

            }

            // Restore our filter
            add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));

            if (!empty($update_cache->response[$this->name]) && version_compare($this->version, $version_info->new_version, '<')) {

                // build a plugin list row, with update notification
                $wp_list_table = _get_list_table('WP_Plugins_List_Table');
                # <tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">
                echo '<tr class="plugin-update-tr" id="' . esc_attr( $this->slug ) . '-update" data-slug="' . esc_attr( $this->slug ) . '" data-plugin="' . esc_attr( $this->slug ) . '/' . esc_attr( $file ) . '">';
                echo '<td colspan="3" class="plugin-update colspanchange">';
                echo '<div class="update-message notice inline notice-warning notice-alt">';

                $changelog_link = self_admin_url('index.php?edd_sl_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&TB_iframe=true&width=772&height=911');

                if (empty($version_info->download_link)) {
                    printf(
                        __('There is a new version of %1$s available. %2$sView version %3$s details%4$s.', 'translatepress-multilingual'), //phpcs:ignore
                        esc_html($version_info->name),
                        '<a target="_blank" class="thickbox" href="' . esc_url($changelog_link) . '">',
                        esc_html($version_info->new_version),
                        '</a>'
                    );
                } else {
                    printf(
                        __('There is a new version of %1$s available. %2$sView version %3$s details%4$s or %5$supdate now%6$s.', 'translatepress-multilingual'), //phpcs:ignore
                        esc_html($version_info->name),
                        '<a target="_blank" class="thickbox" href="' . esc_url($changelog_link) . '">',
                        esc_html($version_info->new_version),
                        '</a>',
                        '<a href="' . esc_url(wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=') . $this->name, 'upgrade-plugin_' . $this->name)) . '">',
                        '</a>'
                    );
                }

                do_action("in_plugin_update_message-{$file}", $plugin, $version_info);

                echo '</div></td></tr>';
            }
        }

        /**
         * Updates information on the "View version x.x details" page with custom data.
         *
         * @uses api_request()
         *
         * @param mixed $_data
         * @param string $_action
         * @param object $_args
         * @return object $_data
         */
        public function plugins_api_filter($_data, $_action = '', $_args = null)
        {

            if ($_action != 'plugin_information') {

                return $_data;

            }

            if (!isset($_args->slug) || ($_args->slug != $this->slug)) {

                return $_data;

            }

            $to_send = array(
                'slug' => $this->slug,
                'is_ssl' => is_ssl(),
                'fields' => array(
                    'banners' => array(),
                    'reviews' => false
                )
            );

            $cache_key = 'edd_api_request_' . md5(serialize($this->slug . $this->api_data['license'] . $this->beta));

            // Get the transient where we store the api request for this plugin for 24 hours
            $edd_api_request_transient = $this->get_cached_version_info($cache_key);

            //If we have no transient-saved value, run the API, set a fresh transient with the API value, and return that value too right now.
            if (empty($edd_api_request_transient)) {

                $api_response = $this->api_request('plugin_information', $to_send);

                // Expires in 3 hours
                $this->set_version_info_cache($api_response, $cache_key);

                if (false !== $api_response) {
                    $_data = $api_response;
                }

            } else {
                $_data = $edd_api_request_transient;
            }

            // Convert sections into an associative array, since we're getting an object, but Core expects an array.
            if (isset($_data->sections) && !is_array($_data->sections)) {
                $new_sections = array();
                foreach ($_data->sections as $key => $value) {
                    $new_sections[$key] = $value;
                }

                $_data->sections = $new_sections;
            }

            // Convert banners into an associative array, since we're getting an object, but Core expects an array.
            if (isset($_data->banners) && !is_array($_data->banners)) {
                $new_banners = array();
                foreach ($_data->banners as $key => $value) {
                    $new_banners[$key] = $value;
                }

                $_data->banners = $new_banners;
            }

            return $_data;
        }

        /**
         * Disable SSL verification in order to prevent download update failures
         *
         * @param array $args
         * @param string $url
         * @return object $array
         */
        public function http_request_args($args, $url)
        {

            $verify_ssl = $this->verify_ssl();
            if (strpos($url, 'https://') !== false && strpos($url, 'edd_action=package_download')) {
                $args['sslverify'] = $verify_ssl;
            }
            return $args;

        }

        /**
         * Calls the API and, if successfull, returns the object delivered by the API.
         *
         * @uses get_bloginfo()
         * @uses wp_remote_post()
         * @uses is_wp_error()
         *
         * @param string $_action The requested action.
         * @param array $_data Parameters for the API action.
         * @return false|object
         */
        private function api_request($_action, $_data)
        {

            global $wp_version;

            $data = array_merge($this->api_data, $_data);

            if ($data['slug'] != $this->slug) {
                return;
            }

            if ($this->api_url == trailingslashit(home_url())) {
                return false; // Don't allow a plugin to ping itself
            }

            $api_params = array(
                'edd_action' => 'get_version',
                'license' => !empty($data['license']) ? $data['license'] : '',
                'item_name' => isset($data['item_name']) ? $data['item_name'] : false,
                'item_id' => isset($data['item_id']) ? $data['item_id'] : false,
                'version' => isset($data['version']) ? $data['version'] : false,
                'slug' => $data['slug'],
                'author' => $data['author'],
                'url' => home_url(),
                'beta' => !empty($data['beta']),
            );

            $verify_ssl = $this->verify_ssl();
            $request = wp_remote_post($this->api_url, array('timeout' => 15, 'sslverify' => $verify_ssl, 'body' => $api_params));

            if (!is_wp_error($request)) {
                $request = json_decode(wp_remote_retrieve_body($request));
            }

            if ($request && isset($request->sections)) {
                $request->sections = maybe_unserialize($request->sections);
            } else {
                $request = false;
            }

            if ($request && isset($request->banners)) {
                $request->banners = maybe_unserialize($request->banners);
            }

            if (!empty($request->sections)) {
                foreach ($request->sections as $key => $section) {
                    $request->$key = (array)$section;
                }
            }

            return $request;
        }

        public function show_changelog()
        {

            global $edd_plugin_data;

            if (empty($_REQUEST['edd_sl_action']) || 'view_plugin_changelog' != $_REQUEST['edd_sl_action']) {
                return;
            }

            if (empty($_REQUEST['plugin'])) {
                return;
            }

            if (empty($_REQUEST['slug'])) {
                return;
            }

            if (!current_user_can('update_plugins')) {
                wp_die( esc_html__('You do not have permission to install plugin updates', 'translatepress-multilingual'), esc_html__('Error', 'translatepress-multilingual'), array('response' => 403));
            }

            $data = $edd_plugin_data[sanitize_text_field( $_REQUEST['slug'] )];
            $beta = !empty($data['beta']) ? true : false;
            $cache_key = md5('edd_plugin_' . sanitize_key($_REQUEST['plugin']) . '_' . $beta . '_version_info');
            $version_info = $this->get_cached_version_info($cache_key);

            if (false === $version_info) {

                $api_params = array(
                    'edd_action' => 'get_version',
                    'item_name' => isset($data['item_name']) ? $data['item_name'] : false,
                    'item_id' => isset($data['item_id']) ? $data['item_id'] : false,
                    'slug' => sanitize_text_field( $_REQUEST['slug'] ),
                    'author' => $data['author'],
                    'url' => home_url(),
                    'beta' => !empty($data['beta'])
                );

                $verify_ssl = $this->verify_ssl();
                $request = wp_remote_post($this->api_url, array('timeout' => 15, 'sslverify' => $verify_ssl, 'body' => $api_params));

                if (!is_wp_error($request)) {
                    $version_info = json_decode(wp_remote_retrieve_body($request));
                }


                if (!empty($version_info) && isset($version_info->sections)) {
                    $version_info->sections = maybe_unserialize($version_info->sections);
                } else {
                    $version_info = false;
                }

                if (!empty($version_info)) {
                    foreach ($version_info->sections as $key => $section) {
                        $version_info->$key = (array)$section;
                    }
                }

                $this->set_version_info_cache($version_info, $cache_key);

            }

            if (!empty($version_info) && isset($version_info->sections['changelog'])) {
                echo '<div style="background:#fff;padding:10px;">' . wp_kses_post( $version_info->sections['changelog'] ) . '</div>';
            }

            exit;
        }

        public function get_cached_version_info($cache_key = '')
        {

            if (empty($cache_key)) {
                $cache_key = $this->cache_key;
            }

            $cache = get_option($cache_key);

            if (empty($cache['timeout']) || current_time('timestamp') > $cache['timeout']) {
                return false; // Cache is expired
            }

            return json_decode($cache['value']);

        }

        public function set_version_info_cache($value = '', $cache_key = '')
        {

            if (empty($cache_key)) {
                $cache_key = $this->cache_key;
            }

            $data = array(
                'timeout' => strtotime('+3 hours', current_time('timestamp')),
                'value' => json_encode($value)
            );

            update_option($cache_key, $data);

        }

        /**
         * Returns if the SSL of the store should be verified.
         *
         * @since  1.6.13
         * @return bool
         */
        private function verify_ssl()
        {
            return (bool)apply_filters('edd_sl_api_request_verify_ssl', true, $this);
        }

    }
}

if( !class_exists('TRP_LICENSE_PAGE') ) {
    class TRP_LICENSE_PAGE
    {
        public function __construct(){
        }

        public function license_menu()
        {
            add_submenu_page(
                'TRPHidden',
                'TranslatePress License',
                'TRPHidden',
                'manage_options',
                'trp_license_key',
                array($this, 'license_page')
            );
        }

        public function license_page()
        {
            $license = get_option('trp_license_key');
            // don't show the license in html
            $license = str_repeat("*", strlen($license));
            $status = get_option('trp_license_status');
            $details = get_option('trp_license_details');
            $action = 'options.php';
            ob_start();
            require TRP_PLUGIN_DIR . 'partials/license-settings-page.php';
            echo ob_get_clean();//phpcs:ignore
        }
    }
}

class TRP_Plugin_Updater{

    private $store_url = "https://translatepress.com";

    public function __construct(){
    }

    protected function get_option( $license_key_option ){
        return get_option( $license_key_option );
    }

    protected function delete_option( $license_key_option ){
        delete_option( $license_key_option );
    }

    protected function update_option( $license_key_option, $value ){
        update_option( $license_key_option, $value );
    }

    protected function license_page_url( ){
        return admin_url( 'admin.php?page=trp_license_key' );
    }

    public function edd_sanitize_license( $new ) {
        $new = sanitize_text_field($new);
        $old = $this->get_option( 'trp_license_key' );
        if( $old && $old != $new ) {
            $this->delete_option( 'trp_license_status' ); // new license has been entered, so must reactivate
        }
        return $new;
    }

    /**
     * This function is run when wordpress checks for updates ( twice a day I believe )
     * @param $transient_data
     * @return mixed
     */
    public function check_license( $transient_data ){

        if( empty( $transient_data->response ) )
            return $transient_data;

        if ( false === ( $trp_check_license = get_transient( 'trp_checked_licence' ) ) ) {

            $license = trim( $this->get_option( 'trp_license_key' ) );

            $license_information_for_all_addons = array();

            $trp = TRP_Translate_Press::get_trp_instance();

            if (!empty($trp->active_pro_addons)) {
                foreach ($trp->active_pro_addons as $active_pro_addon_name) {
                    // data to send in our API request
                    $api_params = array(
                        'edd_action' => 'activate_license',                  //as the license is already activated this does not do anything. We could use check_license action but it gives different results  so we can't use it consistently with the result we get from the moment we activate it
                        'license'    => $license,
                        'item_name'  => urlencode($active_pro_addon_name),   // the name of our product in EDD
                        'url'        => home_url()
                    );

                    if( !empty( $license ) || get_option( 'trp_plugin_optin' ) == 'yes' ){
                        $api_params['machine_translated_strings_data'] = json_encode( get_option( 'trp_machine_translated_characters', array() ), JSON_HEX_QUOT );
                    }

                    // Call the custom API.
                    $response = wp_remote_post($this->store_url, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

                    // make sure the response came back okay
                    if (!is_wp_error($response)) {
                        $license_data = json_decode(wp_remote_retrieve_body($response));
                        if (false === $license_data->success) {
                            $license_information_for_all_addons['invalid'][] = $license_data;
                            break;//we only need one failure
                        } else {
                            $license_information_for_all_addons['valid'][] = $license_data;
                        }
                    }
                }
            }

            //store the license reponse for each addon in the database
            $this->update_option('trp_license_details', $license_information_for_all_addons);

            if( !$license ){
                //we need to throw a notice if we have a pro addon active and no license entered
                $license_information_for_all_addons['invalid'][] = (object) array( 'error' => 'missing' );
                $this->update_option('trp_license_details', $license_information_for_all_addons);
            }

            set_transient( 'trp_checked_licence', 'yes', DAY_IN_SECONDS );
            
        }

        return $transient_data;
    }

    public function admin_activation_notices() {
        if ( isset( $_GET['trp_sl_activation'] ) && ! empty( $_GET['message'] ) ) {

            switch( $_GET['trp_sl_activation'] ) {
                case 'false':
                    $class ="error";
                    break;
                case 'true':
                default:
                    $class ="updated";
                    break;
            }

            ?>
            <div class="<?php echo esc_attr( $class ); ?>">
                <p><?php echo wp_kses_post( urldecode( $_GET['message'] ) );//phpcs:ignore ?></p>
            </div>
            <?php
        }
    }

    public function activate_license() {

        // listen for our activate button to be clicked
        if( isset( $_POST['trp_edd_license_activate'] ) ) {
            // run a quick security check
            if( ! check_admin_referer( 'trp_license_nonce', 'trp_license_nonce' ) )
                return; // get out if we didn't click the Activate button


            if ( isset( $_POST['trp_license_key'] ) && preg_match('/^[*]+$/', $_POST['trp_license_key']) && strlen( $_POST['trp_license_key'] ) > 5 ) { //phpcs:ignore
                // pressed submit without altering the existing license key (containing only * as outputted by default)
                // useful for Deactivating/Activating valid license back
                $license = get_option('trp_license_key', '');
            }else{
                // save the license
                $license = $this->edd_sanitize_license( trim( $_POST['trp_license_key'] ) );//phpcs:ignore
                $this->update_option( 'trp_license_key', $license );
            }
            $message = array();//we will check the license for each addon and we will sotre the messages in an array
            $license_information_for_all_addons = array();

            $trp = TRP_Translate_Press::get_trp_instance();
            if( !empty( $trp->active_pro_addons ) ){
                foreach ( $trp->active_pro_addons as $active_pro_addon_name ){
                    // data to send in our API request
                    $api_params = array(
                        'edd_action' => 'activate_license',
                        'license'    => $license,
                        'item_name'  => urlencode( $active_pro_addon_name ), // the name of our product in EDD
                        'url'        => home_url()
                    );

                    if( !empty( $license ) || get_option( 'trp_plugin_optin' ) == 'yes' ){
                        $api_params['machine_translated_strings_data'] = json_encode( get_option( 'trp_machine_translated_characters', array() ), JSON_HEX_QUOT );
                    }

                    // Call the custom API.
                    $response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

                    // make sure the response came back okay
                    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

                        $response_error_message = $response->get_error_message();
                        $message[] = ( is_wp_error( $response ) && ! empty( $response_error_message ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.', 'translatepress-multilingual' );

                    } else {

                        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

                        if ( false === $license_data->success ) {

                            switch( $license_data->error ) {
                                case 'expired' :
                                    $message[] = sprintf(
                                        __( 'Your license key expired on %s.', 'translatepress-multilingual' ),
                                        date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                                    );
                                    break;
                                case 'revoked' :
                                    $message[] = __( 'Your license key has been disabled.', 'translatepress-multilingual' );
                                    break;
                                case 'missing' :
                                    $message[] = __( 'Invalid license.', 'translatepress-multilingual' );
                                    break;
                                case 'invalid' :
                                case 'site_inactive' :
                                    $message[] = __( 'Your license is not active for this URL.', 'translatepress-multilingual' );
                                    break;
                                case 'item_name_mismatch' :
                                    $message[] = sprintf( __( 'This appears to be an invalid license key for %s.', 'translatepress-multilingual' ), $active_pro_addon_name );
                                    break;
                                case 'no_activations_left':
                                    $message[] = __( 'Your license key has reached its activation limit.', 'translatepress-multilingual' );
                                    break;
                                default :
                                    $message[] = __( 'An error occurred, please try again.', 'translatepress-multilingual' );
                                    break;
                            }

                            $license_information_for_all_addons['invalid'][] =  $license_data;

                        }
                        else{
                            $license_information_for_all_addons['valid'][] =  $license_data;
                            trp_mtapi_sync_license_call( $license );
                        }

                    }
                }
            }

            //store the license reponse for each addon in the database
            $this->update_option( 'trp_license_details', $license_information_for_all_addons );


            // Check if anything passed on a message constituting a failure
            if ( ! empty( $message ) ) {
                $message = implode( "<br/>", array_unique($message) );//if we got the same message for multiple addons show just one, and add a br in case we show multiple messages
                $redirect = add_query_arg( array( 'trp_sl_activation' => 'false', 'message' => urlencode( $message ) ), $this->license_page_url() );

                wp_redirect( $redirect );
                exit();
            }

            // $license_data->license will be either "valid" or "invalid"

            $this->update_option( 'trp_license_status', $license_data->license );

            wp_redirect( add_query_arg( array( 'trp_sl_activation' => 'true', 'message' => urlencode( __( 'You have successfully activated your license', 'translatepress-multilingual' ) ) ), $this->license_page_url() ) );
            exit();
        }
    }

    function deactivate_license() {

        // listen for our activate button to be clicked
        if( isset( $_POST['trp_edd_license_deactivate'] ) ) {

            // run a quick security check
            if( ! check_admin_referer( 'trp_license_nonce', 'trp_license_nonce' ) )
                return; // get out if we didn't click the Activate button

            // retrieve the license from the database
            $license = trim( $this->get_option( 'trp_license_key' ) );

            $trp = TRP_Translate_Press::get_trp_instance();
            if( !empty( $trp->active_pro_addons ) ){
                foreach ( $trp->active_pro_addons as $active_pro_addon_name ){//this loop will actually run just once, as we redirect at the end in all cases

                    // data to send in our API request
                    $api_params = array(
                        'edd_action' => 'deactivate_license',
                        'license'    => $license,
                        'item_name'  => urlencode( $active_pro_addon_name ), // the name of our product in EDD
                        'url'        => home_url()
                    );

                    // Call the custom API.
                    $response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

                    // make sure the response came back okay
                    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

                        if ( is_wp_error( $response ) ) {
                            $message = $response->get_error_message();
                        } else {
                            $message = __( 'An error occurred, please try again.', 'translatepress-multilingual' );
                        }

                        $redirect = add_query_arg( array( 'trp_sl_activation' => 'false', 'message' => urlencode( $message ) ), $this->license_page_url() );
                        wp_redirect( $redirect );
                        exit();
                    }

                    // decode the license data
                    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

                    // $license_data->license will be either "deactivated" or "failed"
                    // regardless, we delete the record in the client website. Otherwise, if he tries to add a new license, he can't.
                    if( $license_data->license == 'deactivated' || $license_data->license == 'failed') {
                        delete_option( 'trp_license_status' );
                    }

                    wp_redirect( $this->license_page_url() );
                    exit();
                    }
                }
        }
    }

}