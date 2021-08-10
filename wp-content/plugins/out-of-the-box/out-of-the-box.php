<?php

namespace TheLion\OutoftheBox;

/*
 * Plugin Name: WP Cloud Plugin Out-of-the-Box (Dropbox)
 * Plugin URI: https://www.wpcloudplugins.com/plugins/out-of-the-box-wordpress-plugin-for-dropbox/
 * Description: Say hello to the most popular WordPress Dropbox plugin! Start using the Cloud even more efficiently by integrating it on your website.
 * Version: 1.19.10
 * Author: WP Cloud Plugins
 * Author URI: https://www.wpcloudplugins.com
 * Text Domain: wpcloudplugins
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.0
 */

// SYSTEM SETTINGS
define('OUTOFTHEBOX_VERSION', '1.19.10');
define('OUTOFTHEBOX_ROOTPATH', plugins_url('', __FILE__));
define('OUTOFTHEBOX_ROOTDIR', __DIR__);

define('OUTOFTHEBOX_SLUG', dirname(plugin_basename(__FILE__)).'/out-of-the-box.php');
define('OUTOFTHEBOX_ADMIN_URL', admin_url('admin-ajax.php'));

if (!defined('OUTOFTHEBOX_CACHE_SITE_FOLDERS')) {
    define('OUTOFTHEBOX_CACHE_SITE_FOLDERS', false);
}

define('OUTOFTHEBOX_CACHEDIR', WP_CONTENT_DIR.'/out-of-the-box-cache/'.(OUTOFTHEBOX_CACHE_SITE_FOLDERS ? get_current_blog_id().'/' : ''));
define('OUTOFTHEBOX_CACHEURL', content_url().'/out-of-the-box-cache/'.(OUTOFTHEBOX_CACHE_SITE_FOLDERS ? get_current_blog_id().'/' : ''));

require_once 'includes/Autoload.php';

require_once OUTOFTHEBOX_ROOTDIR.'/vendors/dropbox-sdk/vendor/autoload.php';

class Main
{
    public $settings = false;
    public $_events;
    private $_accounts;

    /**
     * Construct the plugin object.
     */
    public function __construct()
    {
        $this->load_default_values();

        add_action('init', [$this, 'init']);

        if (is_admin() && (!defined('DOING_AJAX')
                || (isset($_REQUEST['action']) && ('update-plugin' === $_REQUEST['action'])))) {
            $admin = new \TheLion\OutoftheBox\Admin($this);
        }

        // Shortcodes
        add_shortcode('outofthebox', [$this, 'create_template']);

        // After the Shortcode hook to make sure that the raw shortcode will not become visible when plugin isn't meeting the requirements
        if (false === $this->can_run_plugin()) {
            return false;
        }

        $priority = add_filter('out-of-the-box_enqueue_priority', 10);
        add_action('wp_enqueue_scripts', [$this, 'load_scripts'], $priority);
        add_action('wp_enqueue_scripts', [$this, 'load_styles']);

        // add TinyMCE button
        // Depends on the theme were to load....
        add_action('init', [$this, 'load_shortcode_buttons']);
        add_action('admin_head', [$this, 'load_shortcode_buttons']);
        add_filter('mce_css', [$this, 'enqueue_tinymce_css_frontend']);

        add_action('plugins_loaded', [$this, 'load_integrations'], 9);

        // Hook to send notification emails when authorization is lost
        add_action('outofthebox_lost_authorisation_notification', [$this, 'send_lost_authorisation_notification'], 10, 1);

        // Add user folder if needed
        if (isset($this->settings['userfolder_oncreation']) && 'Yes' === $this->settings['userfolder_oncreation']) {
            add_action('user_register', [$this, 'user_folder_create']);
        }
        if (isset($this->settings['userfolder_update']) && 'Yes' === $this->settings['userfolder_update']) {
            add_action('profile_update', [$this, 'user_folder_update'], 100, 2);
        }
        if (isset($this->settings['userfolder_remove']) && 'Yes' === $this->settings['userfolder_remove']) {
            add_action('delete_user', [$this, 'user_folder_delete']);
        }

        // Ajax calls
        add_action('wp_ajax_nopriv_outofthebox-get-filelist', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-get-filelist', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-search', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-search', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-get-gallery', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-get-gallery', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-upload-file', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-upload-file', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-delete-entries', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-delete-entries', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-rename-entry', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-rename-entry', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-move-entries', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-move-entries', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-copy-entry', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-copy-entry', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-create-entry', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-create-entry', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-get-playlist', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-get-playlist', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-create-zip', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-create-zip', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-thumbnail', [$this, 'create_thumbnail']);
        add_action('wp_ajax_outofthebox-thumbnail', [$this, 'create_thumbnail']);

        add_action('wp_ajax_nopriv_outofthebox-check-recaptcha', [$this, 'check_recaptcha']);
        add_action('wp_ajax_outofthebox-check-recaptcha', [$this, 'check_recaptcha']);

        add_action('wp_ajax_nopriv_outofthebox-create-link', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-create-link', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-embedded', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-embedded', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-shorten-url', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-shorten-url', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-download', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-download', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-stream', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-stream', [$this, 'start_process']);

        add_action('wp_ajax_nopriv_outofthebox-preview', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-preview', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-previewshortcode', [$this, 'preview_shortcode']);

        add_action('wp_ajax_nopriv_outofthebox-getads', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-getads', [$this, 'start_process']);

        add_action('wp_ajax_outofthebox-reset-cache', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-factory-reset', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-reset-statistics', [$this, 'start_process']);
        add_action('wp_ajax_outofthebox-revoke', [$this, 'start_process']);

        add_action('wp_ajax_outofthebox-getpopup', [$this, 'get_popup']);

        add_action('wp_ajax_nopriv_outofthebox-embed-entry', [$this, 'embed_entry']);
        add_action('wp_ajax_outofthebox-embed-entry', [$this, 'embed_entry']);

        add_action('wp_ajax_outofthebox-linkusertofolder', [$this, 'user_folder_link']);
        add_action('wp_ajax_outofthebox-unlinkusertofolder', [$this, 'user_folder_unlink']);
        add_action('wp_ajax_outofthebox-rating-asked', [$this, 'rating_asked']);

        // add settings link on plugin page
        add_filter('plugin_row_meta', [$this, 'add_settings_link'], 99, 2);

        if (isset($this->settings['log_events']) && 'Yes' === $this->settings['log_events']) {
            $this->_events = new \TheLion\OutoftheBox\Events($this);
        }

        define('OUTOFTHEBOX_ICON_SET', $this->settings['icon_set']);
    }

    public function init()
    {
        // Localize

        $i18n_dir = dirname(plugin_basename(__FILE__)).'/languages/';
        load_plugin_textdomain('wpcloudplugins', false, $i18n_dir);
    }

    public function can_run_plugin()
    {
        if ((version_compare(PHP_VERSION, '7.0') < 0) || (!function_exists('curl_reset')) || (!extension_loaded('mbstring'))) {
            return false;
        }

        // Check Cache Folder
        if (!file_exists(OUTOFTHEBOX_CACHEDIR)) {
            @mkdir(OUTOFTHEBOX_CACHEDIR, 0755);
        }

        if (!is_writable(OUTOFTHEBOX_CACHEDIR)) {
            @chmod(OUTOFTHEBOX_CACHEDIR, 0755);

            if (!is_writable(OUTOFTHEBOX_CACHEDIR)) {
                return false;
            }
        }

        if (!file_exists(OUTOFTHEBOX_CACHEDIR.'.htaccess')) {
            return copy(OUTOFTHEBOX_ROOTDIR.'/cache/.htaccess', OUTOFTHEBOX_CACHEDIR.'.htaccess');
        }

        return true;
    }

    public function load_default_values()
    {
        $this->settings = get_option('out_of_the_box_settings', [
            'accounts' => [],
            'purcasecode' => '',
            'dropbox_app_key' => '',
            'dropbox_app_secret' => '',
            'lostauthorization_notification' => '',
            'google_analytics' => 'No',
            'loadimages' => 'thumbnail',
            'lightbox_skin' => 'metro-black',
            'lightbox_path' => 'horizontal',
            'lightbox_rightclick' => 'No',
            'lightbox_showcaption' => 'always',
            'lightbox_showheader' => 'always',
            'mediaplayer_skin' => 'Default_Skin',
            'mediaplayer_load_native_mediaelement' => 'No',
            'mediaplayer_ads_tagurl' => '',
            'mediaplayer_ads_skipable' => 'Yes',
            'mediaplayer_ads_skipable_after' => '5',
            'userfolder_name' => '%user_login% (%user_email%)',
            'userfolder_oncreation' => 'Yes',
            'userfolder_onfirstvisit' => 'No',
            'userfolder_update' => 'Yes',
            'userfolder_remove' => 'Yes',
            'userfolder_backend' => 'No',
            'userfolder_backend_auto_root' => '',
            'userfolder_noaccess' => '',
            'download_template_subject' => '',
            'download_template_subject_zip' => '',
            'download_template' => '',
            'upload_template_subject' => '',
            'upload_template' => '',
            'delete_template_subject' => '',
            'delete_template' => '',
            'filelist_template' => '',
            'permissions_edit_settings' => ['administrator'],
            'permissions_link_users' => ['administrator', 'editor'],
            'permissions_see_dashboard' => ['administrator', 'editor'],
            'permissions_see_filebrowser' => ['administrator'],
            'permissions_add_shortcodes' => ['administrator', 'editor', 'author', 'contributor'],
            'permissions_add_links' => ['administrator', 'editor', 'author', 'contributor'],
            'permissions_add_embedded' => ['administrator', 'editor', 'author', 'contributor'],
            'custom_css' => '',
            'loaders' => [],
            'colors' => [],
            'download_method' => 'redirect',
            'gzipcompression' => '',
            'request_cache_max_age' => '',
            'always_load_scripts' => 'No',
            'nonce_validation' => 'Yes',
            'share_buttons' => [],
            'shortlinks' => 'None',
            'bitly_login' => '',
            'bitly_apikey' => '',
            'shortest_apikey' => '',
            'rebrandly_apikey' => '',
            'rebrandly_domain' => '',
            'rebrandly_workspace' => '',
            'log_events' => 'Yes',
            'icon_set' => '',
            'use_team_folders' => 'Yes',
            'use_app_folder' => 'No',
            'recaptcha_sitekey' => '',
            'recaptcha_secret' => '',
            'fontawesomev4_shim' => 'No',
            'event_summary' => 'No',
            'event_summary_period' => 'daily',
            'event_summary_recipients' => get_site_option('admin_email'),
            'uninstall_reset' => 'Yes',
        ]);

        if (false === $this->settings) {
            return;
        }
        // Remove 'advancedsettings' option of versions before 1.6.2
        $advancedsettings = get_option('out_of_the_box_advancedsettings');
        if (false !== $advancedsettings && false !== $this->settings) {
            $this->settings = array_merge($this->settings, $advancedsettings);
            delete_option('out_of_the_box_advancedsettings');
            $this->settings = get_option('out_of_the_box_settings');
        }

        $updated = false;
        // Set default values
        if (empty($this->settings['google_analytics'])) {
            $this->settings['google_analytics'] = 'No';
            $updated = true;
        }

        if (empty($this->settings['download_template_subject'])) {
            $this->settings['download_template_subject'] = '%site_name% | %user_name% downloaded %file_name%';
            $updated = true;
        }

        if (empty($this->settings['download_template_subject_zip'])) {
            $this->settings['download_template_subject_zip'] = '%site_name% | %user_name% downloaded %number_of_files% file(s) from %folder_name%';
            $updated = true;
        }

        if (empty($this->settings['download_template'])) {
            $this->settings['download_template'] = '<h2>Hi there!</h2>

<p>%user_name% has downloaded the following files via %site_name%:</p>

<table cellpadding="0" cellspacing="0" width="100%" border="0" style="cellspacing:0;color:#000000;font-family:"Helvetica Neue", Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;table-layout:auto;width:100%;">

%filelist%

</table>';
            $updated = true;
        }

        if (empty($this->settings['upload_template_subject'])) {
            $this->settings['upload_template_subject'] = '%site_name% | %user_name% uploaded (%number_of_files%) file(s) to %folder_name%';
            $updated = true;
        }

        if (empty($this->settings['upload_template'])) {
            $this->settings['upload_template'] = '<h2>Hi there!</h2>

<p>%user_name% has uploaded the following file(s) via %site_name%:</p>

<table cellpadding="0" cellspacing="0" width="100%" border="0" style="cellspacing:0;color:#000000;font-family:"Helvetica Neue", Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;table-layout:auto;width:100%;">

%filelist%

</table>';
            $updated = true;
        }

        if (empty($this->settings['delete_template_subject'])) {
            $this->settings['delete_template_subject'] = '%site_name% | %user_name% deleted (%number_of_files%) file(s) from %folder_name%';
            $updated = true;
        }

        if (empty($this->settings['delete_template'])) {
            $this->settings['delete_template'] = '<h2>Hi there!</h2>

<p>%user_name% has deleted the following file(s) via %site_name%:</p>

<table cellpadding="0" cellspacing="0" width="100%" border="0" style="cellspacing:0;color:#000000;font-family:"Helvetica Neue", Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;table-layout:auto;width:100%;">

%filelist%

</table>';
            $updated = true;
        }

        if (empty($this->settings['filelist_template'])) {
            $this->settings['filelist_template'] = '<tr style="height: 50px;">
  <td style="width:20px;padding-right:10px;padding-top: 5px;padding-left: 5px;">
    <img alt="" height="16" src="%file_icon%" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;" width="16">
  </td>
  <td style="line-height:25px;padding-left:5px;">
    <a href="%file_cloud_preview_url%" target="_blank">%file_name%</a>
    <br/>
    <div style="font-size:12px;line-height:18px;color:#a6a6a6;outline:none;text-decoration:none;">%folder_absolute_path%</div>
  </td>
  <td style="font-weight: bold;">%file_size%</td>
</tr>';
            $updated = true;
        }

        if (empty($this->settings['mediaplayer_skin'])) {
            $this->settings['mediaplayer_skin'] = 'Default_Skin';
            $updated = true;
        }

        if (empty($this->settings['lightbox_skin'])) {
            $this->settings['lightbox_skin'] = 'metro-black';
            $updated = true;
        }
        if (empty($this->settings['lightbox_path'])) {
            $this->settings['lightbox_path'] = 'horizontal';
            $updated = true;
        }

        if (empty($this->settings['permissions_edit_settings'])) {
            $this->settings['permissions_edit_settings'] = ['administrator'];
            $updated = true;
        }
        if (empty($this->settings['permissions_link_users'])) {
            $this->settings['permissions_link_users'] = ['administrator', 'editor'];
            $updated = true;
        }
        if (empty($this->settings['permissions_see_filebrowser'])) {
            $this->settings['permissions_see_filebrowser'] = ['administrator'];
            $updated = true;
        }
        if (empty($this->settings['permissions_add_shortcodes'])) {
            $this->settings['permissions_add_shortcodes'] = ['administrator', 'editor', 'author', 'contributor'];
            $updated = true;
        }
        if (empty($this->settings['permissions_add_links'])) {
            $this->settings['permissions_add_links'] = ['administrator', 'editor', 'author', 'contributor'];
            $updated = true;
        }
        if (empty($this->settings['permissions_add_embedded'])) {
            $this->settings['permissions_add_embedded'] = ['administrator', 'editor', 'author', 'contributor'];
            $updated = true;
        }

        if (empty($this->settings['gzipcompression'])) {
            $this->settings['gzipcompression'] = 'No';
            $updated = true;
        }

        if (empty($this->settings['request_cache_max_age'])) {
            $this->settings['request_cache_max_age'] = 30; // in minutes
            $updated = true;
        }

        if (empty($this->settings['userfolder_backend'])) {
            $this->settings['userfolder_backend'] = 'No';
            $updated = true;
        }

        if (!isset($this->settings['userfolder_backend_auto_root'])) {
            $this->settings['userfolder_backend_auto_root'] = '';
            $updated = true;
        }

        if (empty($this->settings['colors'])) {
            $this->settings['colors'] = [
                'style' => 'light',
                'background' => '#f2f2f2',
                'accent' => '#522058',
                'black' => '#222',
                'dark1' => '#666',
                'dark2' => '#999',
                'white' => '#fff',
                'light1' => '#fcfcfc',
                'light2' => '#e8e8e8',
            ];
            $updated = true;
        }

        if (empty($this->settings['loaders'])) {
            $this->settings['loaders'] = [
                'style' => 'spinner',
                'loading' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_loading.gif',
                'no_results' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_no_results.png',
                'error' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_error.png',
                'upload' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_upload.gif',
                'protected' => OUTOFTHEBOX_ROOTPATH.'/css/images/loader_protected.png',
            ];
            $updated = true;
        }

        if (empty($this->settings['loaders']['iframe'])) {
            $this->settings['loaders']['iframe'] = OUTOFTHEBOX_ROOTPATH.'/css/images/wpcp-loader.svg';
            $updated = true;
        }

        if (empty($this->settings['lightbox_rightclick'])) {
            $this->settings['lightbox_rightclick'] = 'No';
            $updated = true;
        }

        if (empty($this->settings['lightbox_showcaption'])) {
            $this->settings['lightbox_showcaption'] = 'always';
            $updated = true;
        }
        if (empty($this->settings['lightbox_showheader'])) {
            $this->settings['lightbox_showheader'] = 'always';
            $updated = true;
        }

        if (empty($this->settings['always_load_scripts'])) {
            $this->settings['always_load_scripts'] = 'No';
            $updated = true;
        }

        if (empty($this->settings['nonce_validation'])) {
            $this->settings['nonce_validation'] = 'Yes';
            $updated = true;
        }

        if (empty($this->settings['shortlinks'])) {
            $this->settings['shortlinks'] = 'None';
            $this->settings['bitly_login'] = '';
            $this->settings['bitly_apikey'] = '';
            $this->settings['shortest_apikey'] = '';
            $this->settings['rebrandly_apikey'] = '';
            $this->settings['rebrandly_domain'] = '';
            $this->settings['rebrandly_workspace'] = '';
            $updated = true;
        }

        if (!isset($this->settings['rebrandly_workspace'])) {
            $this->settings['rebrandly_workspace'] = '';
            $updated = true;
        }

        if (empty($this->settings['permissions_see_dashboard'])) {
            $this->settings['permissions_see_dashboard'] = ['administrator', 'editor'];
            $updated = true;
        }

        if (empty($this->settings['log_events'])) {
            $this->settings['log_events'] = 'Yes';
            $updated = true;
        }

        if (empty($this->settings['icon_set']) || '/' === $this->settings['icon_set']) {
            $this->settings['icon_set'] = OUTOFTHEBOX_ROOTPATH.'/css/icons/';
            $updated = true;
        }

        if (empty($this->settings['download_method'])) {
            $this->settings['download_method'] = 'redirect';
            $updated = true;
        }

        if (empty($this->settings['lostauthorization_notification'])) {
            $this->settings['lostauthorization_notification'] = get_site_option('admin_email');
            $updated = true;
        }

        // Users updating the plugin will have not have enabled this by default
        if (empty($this->settings['use_team_folders'])) {
            $this->settings['use_team_folders'] = 'No';
            $updated = true;
        }

        if (!isset($this->settings['recaptcha_sitekey'])) {
            $this->settings['recaptcha_sitekey'] = '';
            $this->settings['recaptcha_secret'] = '';
            $updated = true;
        }

        // disable_fontawesome is replace with fontawesomev4_shim
        if (isset($this->settings['disable_fontawesome'])) {
            $this->settings['fontawesomev4_shim'] = $this->settings['disable_fontawesome'];
            unset($this->settings['disable_fontawesome']);
            $updated = true;
        }

        if (empty($this->settings['fontawesomev4_shim'])) {
            $this->settings['fontawesomev4_shim'] = 'No';
            $updated = true;
        }

        if ('default' === $this->settings['mediaplayer_skin']) {
            $this->settings['mediaplayer_skin'] = 'Default_Skin';
            $updated = true;
        }

        if (empty($this->settings['mediaplayer_load_native_mediaelement'])) {
            $this->settings['mediaplayer_load_native_mediaelement'] = 'No';
            $updated = true;
        }

        if (!isset($this->settings['mediaplayer_ads_tagurl'])) {
            $this->settings['mediaplayer_ads_tagurl'] = '';
            $this->settings['mediaplayer_ads_skipable'] = 'Yes';
            $this->settings['mediaplayer_ads_skipable_after'] = '5';
            $updated = true;
        }

        if (!isset($this->settings['event_summary'])) {
            $this->settings['event_summary'] = 'No';
            $this->settings['event_summary_period'] = 'daily';
            $this->settings['event_summary_recipients'] = get_site_option('admin_email');
            $updated = true;
        }

        if (empty($this->settings['loadimages'])) {
            $this->settings['loadimages'] = 'thumbnail';
            $updated = true;
        }

        if (empty($this->settings['userfolder_noaccess'])) {
            $this->settings['userfolder_noaccess'] = "<h2>No Access</h2>

<p>Your account isn't (yet) configured to access this content. Please contact the administrator of the site if you would like to have access. The administrator can link your account to the right content.</p>";
            $updated = true;
        }

        if (!isset($this->settings['uninstall_reset'])) {
            $this->settings['uninstall_reset'] = 'Yes';
            $updated = true;
        }

        if (empty($this->settings['use_app_folder'])) {
            $this->settings['use_app_folder'] = 'No';
            $updated = true;
        }

        if ('Dropbox' === $this->settings['shortlinks']) {
            $this->settings['shortlinks'] = 'None';
            $updated = true;
        }

        if (isset($this->settings['auth_key']) && false === get_site_option('wpcp-outofthebox-auth_key')) {
            add_site_option('wpcp-outofthebox-auth_key', $this->settings['auth_key']);
            unset($this->settings['auth_key']);
            $updated = true;
        }

        if (empty($this->settings['share_buttons'])) {
            $this->settings['share_buttons'] = [
                'clipboard' => 'enabled',
                'email' => 'enabled',
                'facebook' => 'enabled',
                'linkedin' => 'enabled',
                'mastodon' => 'disabled',
                'messenger' => 'enabled',
                'odnoklassniki' => 'disabled',
                'pinterest' => 'enabled',
                'pocket' => 'disabled',
                'reddit' => 'disabled',
                'telegram' => 'enabled',
                'twitter' => 'enabled',
                'viber' => 'disabled',
                'vkontakte' => 'disabled',
                'whatsapp' => 'enabled',
            ];
            $updated = true;
        }

        $auth_key = get_site_option('wpcp-outofthebox-auth_key');
        if (false === $auth_key) {
            require_once ABSPATH.'wp-includes/pluggable.php';
            $auth_key = wp_generate_password(32);
            add_site_option('wpcp-outofthebox-auth_key', $auth_key);
        }

        define('OUTOFTHEBOX_AUTH_KEY', $auth_key);

        if ($updated) {
            update_option('out_of_the_box_settings', $this->settings);
        }

        $version = get_option('out_of_the_box_version');

        if (version_compare($version, '1.13') < 0) {
            // Install Event Database
            $this->get_events()->install_database();
        }

        if (false !== $version) {
            if (version_compare($version, '1.13.11') < 0) {
                // Remove old DB lists
                delete_option('out_of_the_box_lists');
            }

            if (version_compare($version, '1.14') < 0) {
                // Remove old skin
                $this->settings['mediaplayer_skin'] = 'Default_Skin';
                update_option('out_of_the_box_settings', $this->settings);
            }

            if (version_compare($version, '1.16') < 0) {
                // Multi account support requires changes in account and access_token storage
                if (!isset($this->settings['accounts'])) {
                    $this->settings['accounts'] = [];
                }

                update_option('out_of_the_box_settings', $this->settings);
                $this->get_accounts()->upgrade_from_single();
                $this->settings = get_option('out_of_the_box_settings');
            }
        }

        // Update Version number
        if (OUTOFTHEBOX_VERSION !== $version) {
            // Clear Cache
            $this->get_processor()->reset_complete_cache();

            // Clear WordPress Cache
            Helpers::purge_cache_others();

            update_option('out_of_the_box_version', OUTOFTHEBOX_VERSION);
        }
    }

    public function add_settings_link($links, $file)
    {
        $plugin = plugin_basename(__FILE__);

        // create link
        if ($file == $plugin && !is_network_admin()) {
            return array_merge(
                $links,
                [sprintf('<a href="options-general.php?page=%s">%s</a>', 'OutoftheBox_settings', esc_html__('Settings', 'wpcloudplugins'))],
                [sprintf('<a href="'.plugins_url('_documentation/index.html', __FILE__).'" target="_blank">%s</a>', esc_html__('Docs', 'wpcloudplugins'))],
                [sprintf('<a href="https://florisdeleeuwnl.zendesk.com/hc/en-us" target="_blank">%s</a>', esc_html__('Support', 'wpcloudplugins'))]
            );
        }

        return $links;
    }

    public function load_scripts()
    {
        if ('' !== $this->settings['recaptcha_sitekey']) {
            $url = add_query_arg(
                [
                    'render' => $this->settings['recaptcha_sitekey'],
                ],
                'https://www.google.com/recaptcha/api.js'
            );

            wp_register_script('google-recaptcha', $url, [], '3.0', true);
        }

        wp_register_script('WPCloudPlugins.Polyfill', 'https://cdn.polyfill.io/v3/polyfill.min.js?features=es6,html5-elements,NodeList.prototype.forEach,Element.prototype.classList,CustomEvent,Object.entries,Object.assign,document.querySelector,URL&flags=gated');

        // load in footer

        wp_register_script('jQuery.iframe-transport', plugins_url('vendors/jquery-file-upload/js/jquery.iframe-transport.js', __FILE__), ['jquery', 'jquery-ui-widget'], false, true);
        wp_register_script('jQuery.fileupload-oftb', plugins_url('vendors/jquery-file-upload/js/jquery.fileupload.js', __FILE__), ['jquery', 'jquery-ui-widget'], false, true);
        wp_register_script('jQuery.fileupload-process', plugins_url('vendors/jquery-file-upload/js/jquery.fileupload-process.js', __FILE__), ['jquery', 'jquery-ui-widget'], false, true);
        wp_register_script('OutoftheBox.UploadBox', plugins_url('includes/js/UploadBox.min.js', __FILE__), ['jQuery.iframe-transport', 'jQuery.fileupload-oftb', 'jQuery.fileupload-process', 'jquery', 'jquery-ui-widget', 'WPCloudplugin.Libraries'], OUTOFTHEBOX_VERSION, true);

        wp_register_script('WPCloudplugin.Libraries', plugins_url('vendors/library.min.js', __FILE__), ['WPCloudPlugins.Polyfill', 'jquery'], OUTOFTHEBOX_VERSION, true);
        wp_register_script('Tagify', plugins_url('vendors/tagify/tagify.min.js', __FILE__), ['WPCloudPlugins.Polyfill'], OUTOFTHEBOX_VERSION, true);
        wp_register_script('OutoftheBox', plugins_url('includes/js/Main.min.js', __FILE__), ['jquery', 'jquery-ui-widget', 'WPCloudplugin.Libraries'], OUTOFTHEBOX_VERSION, true);

        wp_register_script('OutoftheBox.DocumentEmbedder', plugins_url('includes/js/DocumentEmbedder.min.js', __FILE__), ['jquery'], OUTOFTHEBOX_VERSION, true);
        wp_register_script('OutoftheBox.DocumentLinker', plugins_url('includes/js/DocumentLinker.min.js', __FILE__), ['jquery'], OUTOFTHEBOX_VERSION, true);
        wp_register_script('OutoftheBox.ShortcodeBuilder', plugins_url('includes/js/ShortcodeBuilder.min.js', __FILE__), ['Tagify', 'jquery-ui-accordion', 'jquery'], OUTOFTHEBOX_VERSION, true);

        // Scripts for the Admin Dashboard
        wp_register_script('OutoftheBox.Admin', plugins_url('includes/js/Admin.min.js', __FILE__), ['jquery'], OUTOFTHEBOX_VERSION, true);
        wp_register_script('OutoftheBox.Datatables', plugins_url('vendors/datatables/datatables.min.js', __FILE__), ['jquery'], OUTOFTHEBOX_VERSION, true);
        wp_register_script('OutoftheBox.ChartJs', plugins_url('vendors/chartjs/chartjs.min.js', __FILE__), ['jquery', 'jquery-ui-datepicker'], OUTOFTHEBOX_VERSION, true);
        wp_register_script('OutoftheBox.Dashboard', plugins_url('includes/js/Dashboard.min.js', __FILE__), ['OutoftheBox.Datatables', 'OutoftheBox.ChartJs', 'jquery-ui-widget', 'WPCloudplugin.Libraries'], OUTOFTHEBOX_VERSION, true);

        $post_max_size_bytes = min(\TheLion\OutoftheBox\Helpers::return_bytes(ini_get('post_max_size')), \TheLion\OutoftheBox\Helpers::return_bytes(ini_get('upload_max_filesize')));

        $localize = [
            'plugin_ver' => OUTOFTHEBOX_VERSION,
            'plugin_url' => plugins_url('', __FILE__),
            'ajax_url' => OUTOFTHEBOX_ADMIN_URL,
            'cookie_path' => COOKIEPATH,
            'cookie_domain' => COOKIE_DOMAIN,
            'is_mobile' => wp_is_mobile(),
            'recaptcha' => is_admin() ? '' : $this->settings['recaptcha_sitekey'],
            'shortlinks' => 'None' === $this->settings['shortlinks'] ? false : $this->settings['shortlinks'],
            'content_skin' => $this->settings['colors']['style'],
            'icons_set' => $this->settings['icon_set'],
            'lightbox_skin' => $this->settings['lightbox_skin'],
            'lightbox_path' => $this->settings['lightbox_path'],
            'lightbox_rightclick' => $this->settings['lightbox_rightclick'],
            'lightbox_showheader' => $this->settings['lightbox_showheader'],
            'lightbox_showcaption' => $this->settings['lightbox_showcaption'],
            'post_max_size' => $post_max_size_bytes,
            'google_analytics' => (('Yes' === $this->settings['google_analytics']) ? 1 : 0),
            'log_events' => (('Yes' === $this->settings['log_events']) ? 1 : 0),
            'share_buttons' => array_keys(array_filter($this->settings['share_buttons'], function ($value) {return 'enabled' === $value; })),
            'refresh_nonce' => wp_create_nonce('outofthebox-get-filelist'),
            'gallery_nonce' => wp_create_nonce('outofthebox-get-gallery'),
            'getplaylist_nonce' => wp_create_nonce('outofthebox-get-playlist'),
            'upload_nonce' => wp_create_nonce('outofthebox-upload-file'),
            'delete_nonce' => wp_create_nonce('outofthebox-delete-entries'),
            'rename_nonce' => wp_create_nonce('outofthebox-rename-entry'),
            'copy_nonce' => wp_create_nonce('outofthebox-copy-entry'),
            'move_nonce' => wp_create_nonce('outofthebox-move-entries'),
            'log_nonce' => wp_create_nonce('outofthebox-event-log'),
            'createentry_nonce' => wp_create_nonce('outofthebox-create-entry'),
            'getplaylist_nonce' => wp_create_nonce('outofthebox-get-playlist'),
            'shortenurl_nonce' => wp_create_nonce('outofthebox-shorten-url'),
            'createzip_nonce' => wp_create_nonce('outofthebox-create-zip'),
            'createlink_nonce' => wp_create_nonce('outofthebox-create-link'),
            'recaptcha_nonce' => wp_create_nonce('outofthebox-check-recaptcha'),
            'str_loading' => esc_html__('Hang on. Waiting for the files...', 'wpcloudplugins'),
            'str_processing' => esc_html__('Processing...', 'wpcloudplugins'),
            'str_success' => esc_html__('Success', 'wpcloudplugins'),
            'str_error' => esc_html__('Error', 'wpcloudplugins'),
            'str_inqueue' => esc_html__('Waiting', 'wpcloudplugins'),
            'str_uploading_start' => esc_html__('Start upload', 'wpcloudplugins'),
            'str_uploading_no_limit' => esc_html__('Unlimited', 'wpcloudplugins'),
            'str_uploading' => esc_html__('Uploading...', 'wpcloudplugins'),
            'str_uploading_failed' => esc_html__('File not uploaded successfully', 'wpcloudplugins'),
            'str_uploading_failed_msg' => esc_html__('The following file(s) are not uploaded succesfully:', 'wpcloudplugins'),
            'str_uploading_failed_in_form' => esc_html__('The form cannot be submitted. Please remove all files that are not successfully attached.', 'wpcloudplugins'),
            'str_uploading_cancelled' => esc_html__('Upload is cancelled', 'wpcloudplugins'),
            'str_uploading_convert' => esc_html__('Converting', 'wpcloudplugins'),
            'str_uploading_convert_failed' => esc_html__('Converting failed', 'wpcloudplugins'),
            'str_uploading_required_data' => esc_html__('Please first fill the required fields', 'wpcloudplugins'),
            'str_error_title' => esc_html__('Error', 'wpcloudplugins'),
            'str_close_title' => esc_html__('Close', 'wpcloudplugins'),
            'str_start_title' => esc_html__('Start', 'wpcloudplugins'),
            'str_cancel_title' => esc_html__('Cancel', 'wpcloudplugins'),
            'str_delete_title' => esc_html__('Delete', 'wpcloudplugins'),
            'str_move_title' => esc_html__('Move', 'wpcloudplugins'),
            'str_copy_title' => esc_html__('Copy', 'wpcloudplugins'),
            'str_copy' => esc_html__('Name of the copy:', 'wpcloudplugins'),
            'str_zip_title' => esc_html__('Create zip file', 'wpcloudplugins'),
            'str_account_title' => esc_html__('Select account', 'wpcloudplugins'),
            'str_copy_to_clipboard_title' => esc_html__('Copy to clipboard', 'wpcloudplugins'),
            'str_delete' => esc_html__('Do you really want to delete:', 'wpcloudplugins'),
            'str_delete_multiple' => esc_html__('Do you really want to delete these files?', 'wpcloudplugins'),
            'str_rename_failed' => esc_html__("That doesn't work. Are there any illegal characters (<>:\"/\\|?*) in the filename?", 'wpcloudplugins'),
            'str_rename_title' => esc_html__('Rename', 'wpcloudplugins'),
            'str_rename' => esc_html__('Rename to:', 'wpcloudplugins'),
            'str_add_description' => esc_html__('Add a description...', 'wpcloudplugins'),
            'str_module_error_title' => esc_html__('Configuration problem', 'wpcloudplugins'),
            'str_missing_location' => esc_html__('This module is currently linked to a cloud account and/or folder which is no longer accessible by the plugin. To resolve this, please relink the module again to the correct folder.', 'wpcloudplugins'),
            'str_no_filelist' => esc_html__('Oops! The content cannot be loaded at this moment. Please try again...', 'wpcloudplugins'),
            'str_recaptcha_failed' => esc_html__("Oops! We couldn't verify that you're not a robot :(. Please try refreshing the page.", 'wpcloudplugins'),
            'str_addnew_title' => esc_html__('Create', 'wpcloudplugins'),
            'str_addnew_name' => esc_html__('Enter name', 'wpcloudplugins'),
            'str_addnew' => esc_html__('Add to folder', 'wpcloudplugins'),
            'str_zip_nofiles' => esc_html__('No files found or selected', 'wpcloudplugins'),
            'str_zip_createzip' => esc_html__('Creating zip file', 'wpcloudplugins'),
            'str_share_link' => esc_html__('Share file', 'wpcloudplugins'),
            'str_shareon' => esc_html__('Share on', 'wpcloudplugins'),
            'str_create_shared_link' => esc_html__('Creating shared link...', 'wpcloudplugins'),
            'str_previous_title' => esc_html__('Previous', 'wpcloudplugins'),
            'str_next_title' => esc_html__('Next', 'wpcloudplugins'),
            'str_xhrError_title' => esc_html__('This content failed to load', 'wpcloudplugins'),
            'str_imgError_title' => esc_html__('This image failed to load', 'wpcloudplugins'),
            'str_startslideshow' => esc_html__('Start slideshow', 'wpcloudplugins'),
            'str_stopslideshow' => esc_html__('Stop slideshow', 'wpcloudplugins'),
            'str_nolink' => esc_html__('Not yet linked to a folder', 'wpcloudplugins'),
            'str_files_limit' => esc_html__('Maximum number of files exceeded', 'wpcloudplugins'),
            'str_filetype_not_allowed' => esc_html__('File type not allowed', 'wpcloudplugins'),
            'str_item' => esc_html__('Item', 'wpcloudplugins'),
            'str_items' => esc_html__('Items', 'wpcloudplugins'),
            'str_max_file_size' => esc_html__('File is too large', 'wpcloudplugins'),
            'str_min_file_size' => esc_html__('File is too small', 'wpcloudplugins'),
            'str_iframe_loggedin' => "<div class='empty_iframe'><h1>".esc_html__('Still Waiting?', 'wpcloudplugins').'</h1><span>'.esc_html__("If the document doesn't open, you are probably trying to access a protected file which requires a login.", 'wpcloudplugins')." <strong><a href='#' target='_blank' class='empty_iframe_link'>".esc_html__('Try to open the file in a new window.', 'wpcloudplugins').'</a></strong></span></div>',
        ];

        $localize_dashboard = [
            'ajax_url' => OUTOFTHEBOX_ADMIN_URL,
            'admin_nonce' => wp_create_nonce('outofthebox-admin-action'),
            'str_close_title' => esc_html__('Close', 'wpcloudplugins'),
            'str_details_title' => esc_html__('Details', 'wpcloudplugins'),
            'content_skin' => $this->settings['colors']['style'],
        ];

        $page = isset($_GET['page']) ? '?page='.$_GET['page'] : '';
        $location = get_admin_url(null, 'admin.php'.$page);

        $localize_admin = [
            'ajax_url' => OUTOFTHEBOX_ADMIN_URL,
            'update_url' => admin_url('update-core.php'),
            'documentation_url' => OUTOFTHEBOX_ROOTPATH.'/_documentation/index.html',
            'activate_url' => 'https://www.wpcloudplugins.com/updates/activate.php?init=1&client_url='.strtr(base64_encode($location), ' + /=', '-_~').'&plugin_id=5529125',
            'admin_nonce' => wp_create_nonce('outofthebox-admin-action'),
        ];

        wp_localize_script('OutoftheBox', 'OutoftheBox_vars', $localize);
        wp_localize_script('OutoftheBox.Dashboard', 'OutoftheBox_Dashboard_vars', $localize_dashboard);
        wp_localize_script('OutoftheBox.Admin', 'OutoftheBox_Admin_vars', $localize_admin);

        if ('Yes' === $this->settings['always_load_scripts']) {
            $mediaplayer = $this->get_processor()->load_mediaplayer($this->settings['mediaplayer_skin']);

            if (!empty($mediaplayer)) {
                $mediaplayer->load_scripts();
                $mediaplayer->load_styles();
            }

            wp_enqueue_script('jquery-ui-droppable');
            wp_enqueue_script('jquery-ui-button');
            wp_enqueue_script('jquery-ui-progressbar');
            wp_enqueue_script('jQuery.iframe-transport');
            wp_enqueue_script('jQuery.fileupload-oftb');
            wp_enqueue_script('jQuery.fileupload-process');
            wp_enqueue_script('jquery-effects-core');
            wp_enqueue_script('jquery-effects-fade');
            wp_enqueue_script('jquery-ui-droppable');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('OutoftheBox.UploadBox');
            wp_enqueue_script('OutoftheBox');
        }
    }

    public function load_styles()
    {
        $is_rtl_css = (is_rtl() ? '-rtl' : '');

        $skin = $this->settings['lightbox_skin'];
        wp_register_style('ilightbox', plugins_url('vendors/iLightBox/css/ilightbox.css', __FILE__), false);
        wp_register_style('ilightbox-skin-outofthebox', plugins_url('vendors/iLightBox/'.$skin.'-skin/skin.css', __FILE__), false);

        wp_register_style('Awesome-Font-5', plugins_url('vendors/font-awesome/css/all.min.css', __FILE__), false, OUTOFTHEBOX_VERSION);
        wp_register_style('Awesome-Font-4-shim', plugins_url('vendors/font-awesome/css/v4-shims.min.css', __FILE__), false, OUTOFTHEBOX_VERSION);

        $custom_css = new CSS($this->settings);
        $custom_css->register_style();

        wp_register_style('OutoftheBox', plugins_url("css/main.min{$is_rtl_css}.css", __FILE__), [], OUTOFTHEBOX_VERSION);
        wp_register_style('OutoftheBox.ShortcodeBuilder', plugins_url("css/tinymce.min{$is_rtl_css}.css", __FILE__), null, OUTOFTHEBOX_VERSION);

        // Scripts for the Admin Dashboard
        wp_register_style('OutoftheBox.Datatables', plugins_url('vendors/datatables/datatables.min.css', __FILE__), null, OUTOFTHEBOX_VERSION);

        if ('Yes' === $this->settings['always_load_scripts']) {
            wp_enqueue_style('ilightbox');
            wp_enqueue_style('ilightbox-skin-outofthebox');

            if (false === defined('WPCP_DISABLE_FONTAWESOME')) {
                wp_enqueue_style('Awesome-Font-5');
                if ('Yes' === $this->settings['fontawesomev4_shim']) {
                    wp_enqueue_style('Awesome-Font-4-shim');
                }
            }

            wp_enqueue_style('OutoftheBox.CustomStyle');
        }
    }

    public function load_integrations()
    {
        require_once 'includes/integrations/load.php';

        new \TheLion\OutoftheBox\Integrations\Integrations($this);
    }

    public function start_process()
    {
        if (!isset($_REQUEST['action'])) {
            return false;
        }

        switch ($_REQUEST['action']) {
            case 'outofthebox-get-filelist':
            case 'outofthebox-download':
            case 'outofthebox-stream':
            case 'outofthebox-preview':
            case 'outofthebox-create-zip':
            case 'outofthebox-create-link':
            case 'outofthebox-embedded':
            case 'outofthebox-reset-cache':
            case 'outofthebox-factory-reset':
            case 'outofthebox-reset-statistics':
            case 'outofthebox-revoke':
            case 'outofthebox-get-gallery':
            case 'outofthebox-upload-file':
            case 'outofthebox-delete-entries':
            case 'outofthebox-rename-entry':
            case 'outofthebox-copy-entry':
            case 'outofthebox-move-entries':
            case 'outofthebox-create-entry':
            case 'outofthebox-get-playlist':
            case 'outofthebox-shorten-url':
            case 'outofthebox-getads':
                require_once ABSPATH.'wp-includes/pluggable.php';
                $this->get_processor()->start_process();

                break;
        }
    }

    public function check_recaptcha()
    {
        if (!isset($_REQUEST['action']) || !isset($_REQUEST['response'])) {
            echo json_encode(['verified' => false]);

            exit();
        }

        check_ajax_referer($_REQUEST['action']);

        require_once OUTOFTHEBOX_ROOTDIR.'/vendors/reCAPTCHA/autoload.php';

        $secret = $this->settings['recaptcha_secret'];
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);

        $resp = $recaptcha->setExpectedAction('wpcloudplugins')
            ->setScoreThreshold(0.5)
            ->verify($_REQUEST['response'], Helpers::get_user_ip())
        ;

        if ($resp->isSuccess()) {
            echo json_encode(['verified' => true]);
        } else {
            echo json_encode(['verified' => false, 'msg' => $resp->getErrorCodes()]);
        }

        exit();
    }

    public function create_template($atts = [])
    {
        if (is_feed()) {
            return esc_html__('Please browse to the page to see this content', 'wpcloudplugins').'.';
        }

        if (false === $this->can_run_plugin()) {
            return '&#9888; <strong>'.esc_html__('This content is not available at this moment unfortunately. Contact the administrators of this site so they can check the plugin involved.', 'wpcloudplugins').'</strong>';
        }

        return $this->get_processor()->create_from_shortcode($atts);
    }

    public function create_thumbnail()
    {
        if (!isset($_REQUEST['account_id'])) {
            // Fallback for old embed urls without account info
            if (empty($account)) {
                $primary_account = $this->get_accounts()->get_primary_account();
                if (false === $primary_account) {
                    exit('-1');
                }
                $account_id = $primary_account->get_id();
            }
        } else {
            $account_id = $_REQUEST['account_id'];
        }

        $this->get_processor()->set_current_account($this->get_accounts()->get_account_by_id($account_id));

        return $this->get_processor()->create_thumbnail();
    }

    public function get_popup()
    {
        switch ($_REQUEST['type']) {
            case 'shortcodebuilder':
                include OUTOFTHEBOX_ROOTDIR.'/templates/admin/shortcode_builder.php';

                break;

            case 'links':
                include OUTOFTHEBOX_ROOTDIR.'/templates/admin/documents_linker.php';

                break;

            case 'embedded':
                include OUTOFTHEBOX_ROOTDIR.'/templates/admin/documents_embedder.php';

                break;
        }

        exit();
    }

    public function preview_shortcode()
    {
        check_ajax_referer('wpcp-outofthebox-block');

        include OUTOFTHEBOX_ROOTDIR.'/templates/admin/shortcode_previewer.php';

        exit();
    }

    public function embed_entry()
    {
        $entryid = isset($_REQUEST['OutoftheBoxpath']) ? $_REQUEST['OutoftheBoxpath'] : null;

        if (empty($entryid)) {
            exit('-1');
        }

        if (!isset($_REQUEST['account_id'])) {
            // Fallback for old embed urls without account info
            if (empty($account)) {
                $primary_account = $this->get_accounts()->get_primary_account();
                if (false === $primary_account) {
                    exit('-1');
                }
                $account_id = $primary_account->get_id();
            }
        } else {
            $account_id = $_REQUEST['account_id'];
        }

        $this->get_processor()->set_current_account($this->get_accounts()->get_account_by_id($account_id));
        $this->get_processor()->embed_entry($entryid);

        exit();
    }

    public function send_lost_authorisation_notification($account_id = null)
    {
        $account = $this->get_accounts()->get_account_by_id($account_id);

        // If account isn't longer present in the account list, remove it from the CRON job
        if (empty($account)) {
            if (false !== ($timestamp = wp_next_scheduled('outofthebox_lost_authorisation_notification', ['account_id' => $account_id]))) {
                wp_unschedule_event($timestamp, 'outofthebox_lost_authorisation_notification', ['account_id' => $account_id]);
            }

            return false;
        }

        $subject = get_bloginfo().' | '.sprintf(esc_html__('ACTION REQUIRED: WP Cloud Plugin lost authorization to %s account', 'wpcloudplugins'), 'Dropbox').':'.(!empty($account) ? $account->get_email() : '');
        $colors = $this->get_processor()->get_setting('colors');

        $template = apply_filters('outofthebox_set_lost_authorization_template', OUTOFTHEBOX_ROOTDIR.'/templates/notifications/lost_authorization.php', $this);

        ob_start();

        include_once $template;
        $htmlmessage = Helpers::compress_html(ob_get_clean());

        // Send mail
        try {
            $headers = ['Content-Type: text/html; charset=UTF-8'];
            $recipients = array_unique(array_map('trim', explode(',', $this->settings['lostauthorization_notification'])));

            foreach ($recipients as $recipient) {
                $result = wp_mail($recipient, $subject, $htmlmessage, $headers);
            }
        } catch (\Exception $ex) {
            error_log('[WP Cloud Plugin message]: '.esc_html__('Could not send email').': '.$ex->getMessage());
        }
    }

    public function ask_for_review($force = false)
    {
        $rating_asked = get_option('out_of_the_box_rating_asked', false);
        if (true == $rating_asked) {
            return;
        }
        $counter = get_option('out_of_the_box_shortcode_opened', 0);
        if ($counter < 10) {
            return;
        }

        include_once OUTOFTHEBOX_ROOTDIR.'/templates/admin/ask_review.php';
    }

    public function rating_asked()
    {
        update_option('out_of_the_box_rating_asked', true);
    }

    public function user_folder_link()
    {
        check_ajax_referer('outofthebox-create-link');

        $userfolders = new UserFolders($this->get_processor());

        $linkedto = [
            'folderid' => rawurldecode($_REQUEST['id']),
            'foldertext' => rawurldecode($_REQUEST['id']),
            'accountid' => rawurldecode($_REQUEST['account_id']),
        ];

        $userid = $_REQUEST['userid'];

        if (\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_link_users'])) {
            $userfolders->manually_link_folder($userid, $linkedto);
        }
    }

    public function user_folder_unlink()
    {
        check_ajax_referer('outofthebox-create-link');

        $userfolders = new UserFolders($this->get_processor());

        $userid = $_REQUEST['userid'];

        if (\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_link_users'])) {
            $userfolders->manually_unlink_folder($userid);
        }
    }

    public function user_folder_create($user_id)
    {
        $userfolders = new UserFolders($this->get_processor());

        foreach ($this->get_accounts()->list_accounts() as $account_id => $account) {
            if (false === $account->get_authorization()->has_access_token()) {
                continue;
            }

            $this->get_processor()->set_current_account($account);
            $userfolders->create_user_folders_for_shortcodes($user_id);
        }
    }

    public function user_folder_update($user_id, $old_user_data = false)
    {
        $userfolders = new UserFolders($this->get_processor());

        foreach ($this->get_accounts()->list_accounts() as $account_id => $account) {
            if (false === $account->get_authorization()->has_access_token()) {
                continue;
            }

            $this->get_processor()->set_current_account($account);
            $userfolders->update_user_folder($user_id, $old_user_data);
        }
    }

    public function user_folder_delete($user_id)
    {
        $userfolders = new UserFolders($this->get_processor());

        foreach ($this->get_accounts()->list_accounts() as $account_id => $account) {
            if (false === $account->get_authorization()->has_access_token()) {
                continue;
            }

            $this->get_processor()->set_current_account($account);
            $userfolders->remove_user_folder($user_id);
        }
    }

    /**
     * Reset plugin to factory settings.
     */
    public static function do_factory_reset()
    {
        // Remove Database settings
        delete_option('out_of_the_box_settings');
        delete_site_option('outofthebox_network_settings');
        delete_site_option('out_of_the_box_guestlinkedto');
        delete_option('out_of_the_box_uniqueID');

        delete_site_option('outofthebox_purchaseid');
        delete_option('out_of_the_box_activated');
        delete_transient('outofthebox_activation_validated');
        delete_site_transient('outofthebox_activation_validated');

        delete_option('out_of_the_box_version');

        // Remove Event Log
        \TheLion\OutoftheBox\Events::uninstall();

        // Remove Cache Files
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(OUTOFTHEBOX_CACHEDIR, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isFile() ? @unlink($path->getPathname()) : @rmdir($path->getPathname());
        }

        @rmdir(OUTOFTHEBOX_CACHEDIR);
    }

    // Add MCE buttons and script

    public function load_shortcode_buttons()
    {
        // Abort early if the user will never see TinyMCE
        if (
                !(\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_add_shortcodes']))
                && !(\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_add_links']))
                && !(\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_add_embedded']))
        ) {
            return;
        }

        if ('true' !== get_user_option('rich_editing')) {
            return;
        }
        // Add a callback to regiser our tinymce plugin
        add_filter('mce_external_plugins', [$this, 'register_tinymce_plugin'], 999);

        // Add a callback to add our button to the TinyMCE toolbar
        add_filter('mce_buttons', [$this, 'register_tinymce_plugin_buttons'], 999);

        // Add custom CSs for placeholders
        add_editor_style(OUTOFTHEBOX_ROOTPATH.'/css/tinymce_editor.css');
    }

    //This callback registers our plug-in
    public function register_tinymce_plugin($plugin_array)
    {
        $plugin_array['outofthebox'] = OUTOFTHEBOX_ROOTPATH.'/includes/js/ShortcodeBuilder_Tinymce.js';

        return $plugin_array;
    }

    //This callback adds our button to the toolbar
    public function register_tinymce_plugin_buttons($buttons)
    {
        //Add the button ID to the $button array

        if (\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_add_shortcodes'])) {
            $buttons[] = 'outofthebox';
        }
        if (\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_add_links'])) {
            $buttons[] = 'outofthebox_links';
        }
        if (\TheLion\OutoftheBox\Helpers::check_user_role($this->settings['permissions_add_embedded'])) {
            $buttons[] = 'outofthebox_embedded';
        }

        return $buttons;
    }

    public function enqueue_tinymce_css_frontend($mce_css)
    {
        if (!empty($mce_css)) {
            $mce_css .= ',';
        }

        $mce_css .= OUTOFTHEBOX_ROOTPATH.'/css/tinymce_editor.css';

        return $mce_css;
    }

    /**
     * @return \TheLion\OutoftheBox\Events
     */
    public function get_events()
    {
        if (empty($this->_events)) {
            $this->_events = new \TheLion\OutoftheBox\Events($this);
        }

        return $this->_events;
    }

    /**
     * @return \TheLion\OutoftheBox\Accounts
     */
    public function get_accounts()
    {
        if (empty($this->_accounts)) {
            $this->_accounts = new \TheLion\OutoftheBox\Accounts($this);
        }

        return $this->_accounts;
    }

    /**
     * @return \TheLion\OutoftheBox\Processor
     */
    public function get_processor()
    {
        if (empty($this->_processor)) {
            $this->_processor = new \TheLion\OutoftheBox\Processor($this);
        }

        return $this->_processor;
    }

    /**
     * @return \TheLion\OutoftheBox\App
     */
    public function get_app()
    {
        if (empty($this->_app)) {
            $this->_app = new \TheLion\OutoftheBox\App($this->get_processor());
            $this->_app->start_client();
        }

        return $this->_app;
    }
}

// Installation and uninstallation hooks
register_activation_hook(__FILE__, __NAMESPACE__.'\outofthebox_network_activate');
register_deactivation_hook(__FILE__, __NAMESPACE__.'\outofthebox_network_deactivate');
register_uninstall_hook(__FILE__, __NAMESPACE__.'\outofthebox_network_uninstall');

$OutoftheBox = new \TheLion\OutoftheBox\Main();

/**
 * Activate the plugin on network.
 *
 * @param mixed $network_wide
 */
function outofthebox_network_activate($network_wide)
{
    if (is_multisite() && $network_wide) { // See if being activated on the entire network or one blog
        global $wpdb;

        // Get this so we can switch back to it later
        $current_blog = $wpdb->blogid;
        // For storing the list of activated blogs
        $activated = [];

        // Get all blogs in the network and activate plugin on each one
        $sql = 'SELECT blog_id FROM %d';
        $blog_ids = $wpdb->get_col($wpdb->prepare($sql, $wpdb->blogs));
        foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            outofthebox_activate(); // The normal activation function
            $activated[] = $blog_id;
        }

        // Switch back to the current blog
        switch_to_blog($current_blog);

        // Store the array for a later function
        update_site_option('out_of_the_box_activated', $activated);
    } else { // Running on a single blog
        outofthebox_activate(); // The normal activation function
    }
}

/**
 * Activate the plugin.
 */
function outofthebox_activate()
{
    add_option(
        'out_of_the_box_settings',
        [
            'accounts' => [],
            'dropbox_app_key' => '',
            'dropbox_app_secret' => '',
            'purcasecode' => '',
            'lostauthorization_notification' => get_site_option('admin_email'),
            'custom_css' => '',
            'google_analytics' => 'No',
            'loadimages' => 'thumbnail',
            'lightbox_skin' => 'metro-black',
            'lightbox_path' => 'horizontal',
            'mediaplayer_skin' => 'Default_Skin',
            'mediaplayer_load_native_mediaelement' => 'No',
            'mediaplayer_ads_tagurl' => '',
            'mediaplayer_ads_skipable' => 'Yes',
            'mediaplayer_ads_skipable_after' => '5',
            'userfolder_name' => '%user_login% (%user_email%)',
            'userfolder_oncreation' => 'Yes',
            'userfolder_onfirstvisit' => 'No',
            'userfolder_update' => 'Yes',
            'userfolder_remove' => 'Yes',
            'userfolder_backend' => 'No',
            'userfolder_backend_auto_root' => '',
            'userfolder_noaccess' => '',
            'download_template_subject' => '',
            'download_template_subject_zip' => '',
            'download_template' => '',
            'upload_template_subject' => '',
            'upload_template' => '',
            'delete_template_subject' => '',
            'delete_template' => '',
            'filelist_template' => '',
            'download_method' => 'redirect',
            'gzipcompression' => '',
            'request_cache_max_age' => '',
            'always_load_scripts' => 'No',
            'nonce_validation' => 'Yes',
            'share_buttons' => [],
            'shortlinks' => 'None',
            'bitly_login' => '',
            'bitly_apikey' => '',
            'shortest_apikey' => '',
            'rebrandly_apikey' => '',
            'rebrandly_domain' => '',
            'rebrandly_workspace' => '',
            'log_events' => 'Yes',
            'icon_set' => '',
            'disable_fontawesome' => 'No',
            'use_team_folders' => 'Yes',
            'use_app_folder', 'No',
            'recaptcha_sitekey' => '',
            'recaptcha_secret' => '',
            'fontawesomev4_shim' => 'No',
            'event_summary' => 'No',
            'event_summary_period' => 'daily',
            'event_summary_recipients' => get_site_option('admin_email'),
            'uninstall_reset' => 'Yes',
        ]
    );

    @unlink(OUTOFTHEBOX_CACHEDIR.'/index');

    update_option('out_of_the_box_version', OUTOFTHEBOX_VERSION);

    // Install Event Log
    Events::install_database();
}

/**
 * Deactivate the plugin on network.
 *
 * @param mixed $network_wide
 */
function outofthebox_network_deactivate($network_wide)
{
    if (is_multisite() && $network_wide) { // See if being activated on the entire network or one blog
        global $wpdb;

        // Get this so we can switch back to it later
        $current_blog = $wpdb->blogid;

        // If the option does not exist, plugin was not set to be network active
        if (false === get_site_option('out_of_the_box_activated')) {
            return false;
        }

        // Get all blogs in the network
        $activated = get_site_option('out_of_the_box_activated');

        $sql = 'SELECT blog_id FROM %d';
        $blog_ids = $wpdb->get_col($wpdb->prepare($sql, $wpdb->blogs));
        foreach ($blog_ids as $blog_id) {
            if (!in_array($blog_id, $activated)) { // Plugin is not activated on that blog
                switch_to_blog($blog_id);
                outofthebox_deactivate();
            }
        }

        // Switch back to the current blog
        switch_to_blog($current_blog);

        // Store the array for a later function
        update_site_option('out_of_the_box_activated', $activated);
    } else { // Running on a single blog
        outofthebox_deactivate();
    }
}

/**
 * Deactivate the plugin.
 */
function outofthebox_deactivate()
{
    foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(OUTOFTHEBOX_CACHEDIR, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
        if ('.htaccess' === $path->getFilename()) {
            continue;
        }

        if ('access_token' === $path->getExtension()) {
            continue;
        }
    }

    global $OutoftheBox;

    if (!empty($OutoftheBox)) {
        foreach ($OutoftheBox->get_accounts()->list_accounts() as $account_id => $account) {
            if (false !== ($timestamp = wp_next_scheduled('outofthebox_lost_authorisation_notification', ['account_id' => $account_id]))) {
                wp_unschedule_event($timestamp, 'outofthebox_lost_authorisation_notification', ['account_id' => $account_id]);
            }
        }
    }

    if (false !== ($timestamp = wp_next_scheduled('outofthebox_lost_authorisation_notification'))) {
        wp_unschedule_event($timestamp, 'outofthebox_lost_authorisation_notification');
    }
}

/**
 * Deactivate the plugin on network.
 *
 * @param mixed $network_wide
 */
function outofthebox_network_uninstall($network_wide)
{
    if (is_multisite() && $network_wide) { // See if being activated on the entire network or one blog
        global $wpdb;

        // Get this so we can switch back to it later
        $current_blog = $wpdb->blogid;

        // If the option does not exist, plugin was not set to be network active
        if (false === get_site_option('out_of_the_box_activated')) {
            return false;
        }

        // Get all blogs in the network
        $activated = get_site_option('out_of_the_box_activated');

        $sql = 'SELECT blog_id FROM %d';
        $blog_ids = $wpdb->get_col($wpdb->prepare($sql, $wpdb->blogs));
        foreach ($blog_ids as $blog_id) {
            if (!in_array($blog_id, $activated)) { // Plugin is not activated on that blog
                switch_to_blog($blog_id);
                outofthebox_uninstall();
            }
        }

        // Switch back to the current blog
        switch_to_blog($current_blog);

        // Store the array for a later function
        update_site_option('out_of_the_box_activated', $activated);
        delete_site_option('outofthebox_network_settings');
    } else { // Running on a single blog
        outofthebox_uninstall();
    }
}

/**
 * Deactivate the plugin.
 */
function outofthebox_uninstall()
{
    $settings = get_option('out_of_the_box_settings', []);

    if (isset($settings['uninstall_reset']) && 'Yes' === $settings['uninstall_reset']) {
        \TheLion\OutoftheBox\Main::do_factory_reset();
    }

    // Remove pending notifications
    global $OutoftheBox;

    if (!empty($OutoftheBox)) {
        foreach ($OutoftheBox->get_accounts()->list_accounts() as $account_id => $account) {
            if (false !== ($timestamp = wp_next_scheduled('outofthebox_lost_authorisation_notification', ['account_id' => $account_id]))) {
                wp_unschedule_event($timestamp, 'outofthebox_lost_authorisation_notification', ['account_id' => $account_id]);
            }
        }
    }
    if (false !== ($timestamp = wp_next_scheduled('outofthebox_lost_authorisation_notification'))) {
        wp_unschedule_event($timestamp, 'outofthebox_lost_authorisation_notification');
    }
}
