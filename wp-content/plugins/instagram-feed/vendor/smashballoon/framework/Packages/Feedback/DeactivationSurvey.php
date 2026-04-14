<?php

/**
 * Deactivation Survey - Renders modal and handles form submission.
 *
 * @package Feedback
 */
namespace InstagramFeed\Vendor\Smashballoon\Framework\Packages\Feedback;

if (!defined('ABSPATH')) {
    exit;
}
/**
 * Handles the deactivation survey modal for all registered plugins.
 */
class DeactivationSurvey
{
    /**
     * All plugin configurations.
     *
     * @var array
     */
    private $configs;
    /**
     * Constructor.
     *
     * @param array $configs All plugin configurations from FeedbackManager.
     */
    public function __construct(array $configs)
    {
        $this->configs = $configs;
    }
    /**
     * Enqueue modal assets.
     *
     * CSS is not enqueued — it's inlined inside the Shadow DOM template
     * rendered by render_modal() for complete style isolation.
     *
     * @return void
     */
    public function enqueue_assets()
    {
        $asset_url = $this->get_asset_url();
        wp_enqueue_script('sb-deactivation-modal', $asset_url . 'deactivation-modal.js', [], $this->get_version(), \true);
        // Use inline script to merge plugin data into a shared global.
        // wp_localize_script overwrites on duplicate calls across scoped
        // plugin instances — this approach accumulates instead.
        $inline = sprintf('window.sbFeedbackData = window.sbFeedbackData || %s;' . 'Object.assign( window.sbFeedbackData.plugins, %s );', wp_json_encode(['ajaxUrl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('sb_deactivation_feedback'), 'plugins' => new \stdClass()]), wp_json_encode($this->get_registered_plugins()));
        wp_add_inline_script('sb-deactivation-modal', $inline, 'before');
    }
    /**
     * Get the URL to the assets directory.
     *
     * @return string
     */
    private function get_asset_url()
    {
        $asset_dir = dirname(__FILE__) . '/assets/';
        // Try to resolve URL from wp-content.
        $content_dir = wp_normalize_path(\WP_CONTENT_DIR);
        $asset_path = wp_normalize_path($asset_dir);
        if (strpos($asset_path, $content_dir) === 0) {
            $relative = substr($asset_path, strlen($content_dir));
            return content_url($relative);
        }
        // Fallback: use first registered plugin to resolve URL.
        $first_config = reset($this->configs);
        if ($first_config && !empty($first_config['plugin_file'])) {
            $plugin_dir = wp_normalize_path(dirname($first_config['plugin_file']));
            $relative = str_replace($plugin_dir, '', $asset_path);
            return plugins_url($relative, $first_config['plugin_file']);
        }
        return '';
    }
    /**
     * Get version string from first registered plugin.
     *
     * @return string
     */
    private function get_version()
    {
        $first = reset($this->configs);
        return $first ? $first['plugin_version'] : '1.0.0';
    }
    /**
     * Get all registered plugin data for the JS.
     *
     * @return array
     */
    private function get_registered_plugins()
    {
        $plugins = [];
        foreach ($this->configs as $slug => $config) {
            $plugin_basename = plugin_basename($config['plugin_file']);
            $plugins[$plugin_basename] = ['slug' => $slug, 'name' => $config['plugin_name'], 'version' => $config['plugin_version'], 'supportUrl' => $config['support_url'], 'reasons' => self::get_reasons($config['plugin_name'])];
        }
        return $plugins;
    }
    /**
     * Get deactivation reasons with context-sensitive messaging.
     *
     * @param string $plugin_name Plugin display name.
     * @return array
     */
    public static function get_reasons($plugin_name)
    {
        return [['id' => 'no-longer-needed', 'label' => sprintf(
            /* translators: %s: Plugin name */
            __('I no longer need %s', 'sb-common'),
            $plugin_name
        ), 'heading' => __('Glad we could help!', 'sb-common'), 'description' => sprintf(
            /* translators: %s: Plugin name */
            __('We would love to know about your experience and improve %s!', 'sb-common'),
            $plugin_name
        ), 'placeholder' => __('Tell us more about your experience (Optional)', 'sb-common')], ['id' => 'did-not-work', 'label' => __("It didn't work as expected", 'sb-common'), 'heading' => __('We would love to help', 'sb-common'), 'description' => __('Our support team can often resolve technical issues quickly. Consider reaching out before you go.', 'sb-common'), 'placeholder' => __('Tell us more about your experience (Optional)', 'sb-common')], ['id' => 'caused-errors', 'label' => __('It caused issues/errors on my site', 'sb-common'), 'heading' => __('We would love to help', 'sb-common'), 'description' => __('Our support team can often resolve technical issues quickly. Consider reaching out before you go.', 'sb-common'), 'placeholder' => __('Tell us more about your experience (Optional)', 'sb-common')], ['id' => 'switching-plugin', 'label' => __('I am switching to another plugin', 'sb-common'), 'heading' => __('Help us improve!', 'sb-common'), 'description' => __("What features does the alternative have that we're missing?", 'sb-common'), 'placeholder' => __('Tell us more about your experience (Optional)', 'sb-common')], ['id' => 'too-complicated', 'label' => __("It's too complicated to use", 'sb-common'), 'heading' => __('Did you know?', 'sb-common'), 'description' => __('We have video tutorials and documentation that might help. What specific feature was confusing?', 'sb-common'), 'placeholder' => __('Tell us more about your experience (Optional)', 'sb-common')], ['id' => 'other', 'label' => __('Other', 'sb-common'), 'heading' => __('Help us improve!', 'sb-common'), 'description' => sprintf(
            /* translators: %s: Plugin name */
            __('Your feedback will help us make %s better for everyone', 'sb-common'),
            $plugin_name
        ), 'placeholder' => __('Tell us more about your experience (Optional)', 'sb-common')]];
    }
    /**
     * Render the Shadow DOM host and template in the admin footer.
     *
     * The modal HTML and CSS live inside a <template> that the JS
     * clones into a Shadow DOM root, giving us complete CSS isolation
     * from WP admin styles — no !important, no scoping hacks.
     *
     * @return void
     */
    public function render_modal()
    {
        // Guard against duplicate output when multiple scoped plugin
        // instances each call render_modal() on admin_footer.
        if (!empty($GLOBALS['sb_feedback_modal_rendered'])) {
            return;
        }
        $GLOBALS['sb_feedback_modal_rendered'] = \true;
        $css_file = dirname(__FILE__) . '/assets/deactivation-modal.css';
        $css = file_exists($css_file) ? file_get_contents($css_file) : '';
        ?>
		<div id="sb-deactivation-host"></div>
		<template id="sb-deactivation-template">
			<style><?php 
        echo $css;
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS file, not user input. 
        ?></style>
			<div class="sb-deactivation-overlay" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="sb-deactivation-title">
				<div class="sb-deactivation-modal">
					<div class="sb-deactivation-header">
						<div>
							<h2 id="sb-deactivation-title" class="sb-deactivation-heading">
								<?php 
        esc_html_e('Reason for Cancellation', 'sb-common');
        ?>
							</h2>
							<p class="sb-deactivation-subtitle">
								<?php 
        esc_html_e('We are sorry to see you go. Help us make ', 'sb-common');
        ?>
								<span class="sb-deactivation-plugin-name"></span>
								<?php 
        esc_html_e(' better for other users!', 'sb-common');
        ?>
							</p>
						</div>
						<button type="button" class="sb-deactivation-close" aria-label="<?php 
        esc_attr_e('Close', 'sb-common');
        ?>">
							<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M13 1L1 13M1 1L13 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</button>
					</div>

					<div class="sb-deactivation-reasons">
						<!-- Populated by JavaScript -->
					</div>

					<div class="sb-deactivation-context">
						<h3 class="sb-deactivation-context-heading"></h3>
						<p class="sb-deactivation-context-description"></p>
						<textarea
							class="sb-deactivation-textarea"
							rows="4"
							placeholder=""
						></textarea>
					</div>

					<div class="sb-deactivation-footer">
						<a href="#" class="sb-deactivation-btn sb-deactivation-btn--support" target="_blank" rel="noopener noreferrer">
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M8 14.5C11.5899 14.5 14.5 11.5899 14.5 8C14.5 4.41015 11.5899 1.5 8 1.5C4.41015 1.5 1.5 4.41015 1.5 8C1.5 11.5899 4.41015 14.5 8 14.5Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M6.09375 6.00043C6.24921 5.55487 6.55771 5.17817 6.96306 4.93985C7.36842 4.70153 7.84436 4.61676 8.30593 4.69996C8.7675 4.78316 9.18463 5.02919 9.48113 5.39298C9.77763 5.75677 9.93476 6.21479 9.92375 6.68543C9.92375 8.00043 7.92375 8.65793 7.92375 8.65793" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
								<circle cx="8" cy="11.25" r="0.75" fill="currentColor"/>
							</svg>
							<?php 
        esc_html_e('Get Support', 'sb-common');
        ?>
						</a>
						<div class="sb-deactivation-footer-right">
							<button type="button" class="sb-deactivation-btn sb-deactivation-btn--cancel">
								<?php 
        esc_html_e('Cancel', 'sb-common');
        ?>
							</button>
							<button type="button" class="sb-deactivation-btn sb-deactivation-btn--submit">
								<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M11.6663 3.5L5.24967 9.91667L2.33301 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
								<?php 
        esc_html_e('Deactivate Plugin', 'sb-common');
        ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</template>
		<?php 
    }
    /**
     * Handle AJAX feedback submission.
     *
     * @return void
     */
    public static function handle_ajax()
    {
        check_ajax_referer('sb_deactivation_feedback', 'nonce');
        if (!current_user_can('activate_plugins')) {
            wp_send_json_error('Unauthorized', 403);
        }
        $slug = isset($_POST['plugin_slug']) ? sanitize_key($_POST['plugin_slug']) : '';
        $reason_id = isset($_POST['reason_id']) ? sanitize_key($_POST['reason_id']) : '';
        $details = isset($_POST['details']) ? sanitize_textarea_field($_POST['details']) : '';
        $config = FeedbackManager::get_config($slug);
        if (!$config) {
            wp_send_json_error('Unknown plugin', 400);
        }
        // Data for the sb-feedback-api REST endpoint.
        // Field mapping: reason_id -> reason, details -> comment.
        $api_data = ['plugin_slug' => $slug, 'plugin_name' => $config['plugin_name'], 'plugin_version' => $config['plugin_version'], 'reason' => $reason_id, 'comment' => $details, 'wp_version' => get_bloginfo('version'), 'php_version' => phpversion(), 'site_url' => home_url()];
        // Determine API endpoint: use config override, or auto-detect from slug.
        $endpoint = !empty($config['api_endpoint']) ? $config['api_endpoint'] : ApiClient::get_endpoint_for_slug($slug);
        // Send to API.
        ApiClient::send($endpoint, $api_data);
        // Extended data for the action hook (includes additional metadata).
        $hook_data = array_merge($api_data, ['locale' => get_locale(), 'multisite' => is_multisite() ? 'yes' : 'no', 'timestamp' => current_time('mysql', \true)]);
        /**
         * Fires after deactivation feedback is collected.
         *
         * @param array  $data   Feedback data.
         * @param array  $config Plugin configuration.
         */
        do_action('sb_feedback_deactivation_submitted', $hook_data, $config);
        // Always succeed — deactivation should never be blocked.
        wp_send_json_success();
    }
}
