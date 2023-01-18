<?php
/*
 * Plugin Name:Blog2Social: Social Media Auto Post & Scheduler
 * Plugin URI: https://www.blog2social.com
 * Description:Auto publish, schedule & share posts on social media: Facebook, Twitter, XING, LinkedIn, Instagram, ... crosspost to pages & groups
 * Author: Blog2Social, Adenion
 * Text Domain: blog2social
 * Domain Path: /languages
 * Version: 6.9.14
 * Author URI: https://www.blog2social.com
 * License: GPL2+
 */

define('B2S_PLUGIN_VERSION', '699');
define('B2S_PLUGIN_LANGUAGE', serialize(array('de_DE', 'en_US')));
define('B2S_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('B2S_PLUGIN_URL', plugin_dir_url(__FILE__));
define('B2S_PLUGIN_HOOK', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
define('B2S_PLUGIN_FILE', __FILE__);
define('B2S_PLUGIN_LANGUAGE_PATH', dirname(plugin_basename(__FILE__)) . '/languages/');
$language = (!in_array(get_locale(), unserialize(B2S_PLUGIN_LANGUAGE))) ? 'en_US' : get_locale();
define('B2S_LANGUAGE', $language);
define('B2S_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('B2S_PLUGIN_API_ENDPOINT', 'https://developer.blog2social.com/wp/v3/');
define('B2S_PLUGIN_API_ENDPOINT_AUTH', 'https://developer.blog2social.com/wp/v3/network/auth.php');
define('B2S_PLUGIN_API_ENDPOINT_AUTH_SHORTENER', 'https://developer.blog2social.com/wp/v3/network/shortener.php');
define('B2S_PLUGIN_PRG_API_ENDPOINT', 'http://developer.pr-gateway.de/wp/v3/');
define('B2S_PLUGIN_SERVER_URL', 'https://developer.blog2social.com');

//B2SLoader
require_once(B2S_PLUGIN_DIR . 'includes/Loader.php');
require_once(B2S_PLUGIN_DIR . 'includes/Tools.php');
require_once(B2S_PLUGIN_DIR . 'includes/System.php');
require_once(B2S_PLUGIN_DIR . 'includes/Options.php');

$b2sLoad = new B2S_Loader();
register_uninstall_hook(B2S_PLUGIN_FILE, 'uninstallPlugin');
register_activation_hook(B2S_PLUGIN_FILE, array($b2sLoad, 'activatePlugin'));
register_deactivation_hook(B2S_PLUGIN_FILE, array($b2sLoad, 'deactivatePlugin'));

$b2sCheck = new B2S_System();
if ($b2sCheck->check() === true) {
    add_action('init', array($b2sLoad, 'load'));
    add_filter('safe_style_css', function( $styles ) {
        $styles[] = 'display';
        return $styles;
    } );
} else {
    require_once(B2S_PLUGIN_DIR . 'includes/Notice.php');
    add_action('admin_notices', array('B2S_Notice', 'sytemNotice'));
}

function uninstallPlugin() {
    require_once(plugin_dir_path(__FILE__) . 'includes/Tools.php');
    require_once(plugin_dir_path(__FILE__) . 'includes/System.php');
    $b2sCheck = new B2S_System();
    if ($b2sCheck->check() === true) {
        global $wpdb;
        if (is_multisite()) {
            $sql = "SELECT blog_id FROM {$wpdb->base_prefix}blogs";
            $blog_ids = $wpdb->get_results($sql, ARRAY_A);
            if (is_array($blog_ids) && !empty($blog_ids)) {
                $union = "";
                foreach ($blog_ids as $key => $blog_data) {
                    $blog_prefix = $wpdb->get_blog_prefix($blog_data['blog_id']);
                    if (!empty($blog_prefix)) {
                        $existsTable = $wpdb->get_results('SHOW TABLES LIKE "' . $blog_prefix . 'b2s_user"');
                        if (is_array($existsTable) && !empty($existsTable)) {
                            if (!empty($union)) {
                                $union .= " UNION ALL ";
                            }
                            $union .= " SELECT  token,blog_user_id FROM {$blog_prefix}b2s_user ";
                        }
                    }
                }
                if (!empty($union)) {
                    $sql = "SELECT * FROM ( " . $union . " ) as all_tokens";
                    $data = $wpdb->get_results($sql, ARRAY_A);
                    if (!empty($data) && is_array($data)) {
                        require_once (plugin_dir_path(__FILE__) . 'includes/B2S/Api/Post.php');
                        B2S_Api_Post::post('https://developer.blog2social.com/wp/v3/', array('action' => 'uninstallPlugin', 'blog_url' => get_option('home'), 'data' => serialize($data), 'is_multisite' => true));
                    }
                }
            }
        } else {
            $sql = "SELECT token,blog_user_id FROM `{$wpdb->prefix}b2s_user`";
            $data = $wpdb->get_results($sql, ARRAY_A);
            if (!empty($data) && is_array($data)) {
                require_once (plugin_dir_path(__FILE__) . 'includes/B2S/Api/Post.php');
                B2S_Api_Post::post('https://developer.blog2social.com/wp/v3/', array('action' => 'uninstallPlugin', 'blog_url' => get_option('home'), 'data' => serialize($data), 'is_multisite' => false));
            }
        }
    }
    //global $wpdb;
    //update_option('b2s_plugin_version', '0');
    //$wpdb->query('DROP TABLE IF EXISTS `b2s_posts`');
    //$wpdb->query('DROP TABLE IF EXISTS `b2s_user`');
    //$wpdb->query('DROP TABLE IF EXISTS `b2s_filter`');
    //$wpdb->query('DROP TABLE IF EXISTS `b2s_posts_network_details`');
    //$wpdb->query('DROP TABLE IF EXISTS `b2s_posts_sched_details`');
    //$wpdb->query('DROP TABLE IF EXISTS `b2s_user_network_settings`');
    //$wpdb->query('DROP TABLE IF EXISTS `b2s_user_contact`');
}
