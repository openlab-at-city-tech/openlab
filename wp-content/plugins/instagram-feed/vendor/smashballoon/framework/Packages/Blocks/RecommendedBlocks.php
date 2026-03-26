<?php

/**
 * Description: Recommended blocks for suggesting other Awesome Motive plugins.
 * Version:     1.0
 * Author:      Awesome Motive, Inc.
 * Author URI:  https://awesomemotive.com/
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
namespace InstagramFeed\Vendor\Smashballoon\Framework\Packages\Blocks;

require_once \ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
use Plugin_Installer_Skin;
use Plugin_Upgrader;
use WP_Error;
/**
 * Recommended Blocks class.
 */
class RecommendedBlocks
{
    /**
     * Setup.
     */
    public function setup()
    {
        add_action('wp_ajax_am_recommended_block_install', [$this, 'install_plugin']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_assets']);
    }
    /**
     * Enqueue the needed scripts.
     */
    public function enqueue_block_assets()
    {
        $asset_file = plugin_dir_path(__FILE__) . 'build/index.asset.php';
        $asset = file_exists($asset_file) ? require $asset_file : ['dependencies' => ['wp-i18n', 'wp-element', 'wp-components', 'wp-api-fetch'], 'version' => '1.0.0'];
        wp_enqueue_script('recommended-blocks', plugin_dir_url(__FILE__) . 'build/index.js', $asset['dependencies'], $asset['version'], \true);
        $active_plugins = (array) get_option('active_plugins', array());
        wp_localize_script('recommended-blocks', 'recommendedBlocksData', ['siteUrl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('am_recommended_block_install'), 'plugins' => $active_plugins]);
        wp_enqueue_style('recommended-blocks', plugin_dir_url(__FILE__) . 'build/index.css', [], '1.0.0');
    }
    /**
     * Install the plugin.
     */
    public function install_plugin()
    {
        include_once \ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once \ABSPATH . 'wp-admin/includes/plugin-install.php';
        if (!current_user_can('install_plugins')) {
            $error = new WP_Error('no_permission', 'You do not have permission to install plugins.');
            wp_send_json_error($error);
        }
        if (empty($_REQUEST['nonce']) || !wp_verify_nonce(sanitize_text_field($_REQUEST['nonce']), 'am_recommended_block_install')) {
            $error = new WP_Error('nonce_failure', 'The nonce was not valid.');
            wp_send_json_error($error);
        }
        if (empty($_REQUEST['plugin'])) {
            $error = new WP_Error('missing_file', 'The plugin file was not specified.');
            wp_send_json_error($error);
        }
        $plugin_file = sanitize_text_field($_REQUEST['plugin']);
        $slug = strtok($plugin_file, '/');
        $plugin_dir = \WP_PLUGIN_DIR . '/' . $slug;
        $plugin_path = \WP_PLUGIN_DIR . '/' . $plugin_file;
        if (!is_dir($plugin_dir)) {
            $api = plugins_api('plugin_information', ['slug' => $slug, 'fields' => ['short_description' => \false, 'sections' => \false, 'requires' => \false, 'rating' => \false, 'ratings' => \false, 'downloaded' => \false, 'last_updated' => \false, 'added' => \false, 'tags' => \false, 'compatibility' => \false, 'homepage' => \false, 'donate_link' => \false]]);
            $skin = new Plugin_Installer_Skin(['api' => $api]);
            $upgrader = new Plugin_Upgrader($skin);
            $install = $upgrader->install($api->download_link);
            if ($install !== \true) {
                $error = new WP_Error('failed_install', 'The plugin install failed.');
                wp_send_json_error($error);
            }
        }
        if (file_exists($plugin_path)) {
            activate_plugin($plugin_path);
            $this->disable_installed_plugins_redirect();
            wp_redirect(get_permalink());
        } else {
            $error = new WP_Error('failed_activation', 'The plugin activation failed.');
            wp_send_json_error($error);
        }
        wp_die();
    }
    /**
     * Disable the redirect to the 3rd party plugin's welcome page.
     *
     * @return void
     */
    public function disable_installed_plugins_redirect()
    {
        // Smash Balloon plugins.
        $this->disable_smash_balloon_redirect();
    }
    /**
     * Disable the redirect to Smash Balloon's plugin welcome page after activation.
     *
     * @return void
     */
    public function disable_smash_balloon_redirect()
    {
        $smash_list = ['facebook' => 'cff_plugin_do_activation_redirect', 'instagram' => 'sbi_plugin_do_activation_redirect', 'youtube' => 'sby_plugin_do_activation_redirect', 'twitter' => 'ctf_plugin_do_activation_redirect', 'reviews' => 'sbr_plugin_do_activation_redirect'];
        foreach ($smash_list as $plugin => $option) {
            delete_option($option);
        }
    }
}
