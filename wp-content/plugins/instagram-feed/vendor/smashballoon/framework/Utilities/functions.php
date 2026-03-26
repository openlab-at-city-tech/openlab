<?php

namespace InstagramFeed\Vendor\Smashballoon\Framework;

if (!function_exists('InstagramFeed\Vendor\Smashballoon\Framework\sb_doing_it_wrong')) {
    /**
     * Wrapper for _doing_it_wrong().
     *
     * @param string $function Function used.
     * @param string $message Message to log.
     * @param string $version Version the message was added in.
     *
     * @return void
     */
    function sb_doing_it_wrong($function, $message, $version)
    {
        // @codingStandardsIgnoreStart
        $message .= ' Backtrace: ' . wp_debug_backtrace_summary();
        if (wp_doing_ajax()) {
            do_action('doing_it_wrong_run', $function, $message, $version);
            error_log("{$function} was called incorrectly. {$message}. This message was added in version {$version}.");
        } else {
            _doing_it_wrong($function, $message, $version);
        }
        // @codingStandardsIgnoreEnd
    }
}
if (!function_exists('InstagramFeed\Vendor\Smashballoon\Framework\sb_locate_template')) {
    /**
     * Locate a template and return the path for inclusion.
     *
     * This is the load order:
     *
     * yourtheme/$template_path/$template_name
     * yourtheme/$template_name
     * $default_path/$template_name
     *
     * @param string $template_name Template name.
     * @param string $template_path Template path. (default: '').
     * @param string $default_path Default path. (default: '').
     *
     * @return string Template path.
     */
    function sb_locate_template($template_name, $template_path = '', $default_path = '')
    {
        if (!$template_path) {
            $template_path = apply_filters('sb_template_path', 'smashballoon/');
        }
        if (!$default_path) {
            $default_path = untrailingslashit(plugin_dir_path(__DIR__)) . '/Packages/';
            $default_path = apply_filters('sb_default_template_path', $default_path);
        }
        // Look within passed path within the theme - this is priority.
        $template = locate_template([trailingslashit($template_path) . $template_name, $template_name]);
        // Get default template.
        if (!$template) {
            $template = $default_path . $template_name;
        }
        // Return what we found.
        return apply_filters('sb_locate_template', $template, $template_name, $template_path);
    }
}
if (!function_exists('InstagramFeed\Vendor\Smashballoon\Framework\sb_get_template')) {
    /**
     * Get other templates passing attributes and including the file.
     *
     * @param string $template_name   Template name.
     * @param array  $args            Arguments. (default: array).
     * @param string $template_path   Template path. (default: '').
     * @param string $default_path    Default path. (default: '').
     *
     * @return void
     */
    function sb_get_template($template_name, $args = [], $template_path = '', $default_path = '')
    {
        $cache_key = sanitize_key(implode('-', ['template', $template_name, $template_path, $default_path]));
        $template = (string) wp_cache_get($cache_key, 'smashballoon');
        if (!$template) {
            $template = sb_locate_template($template_name, $template_path, $default_path);
            wp_cache_set($cache_key, $template, 'smashballoon');
        }
        // Allow 3rd party plugin filter template file from their plugin.
        $filter_template = apply_filters('sb_get_template', $template, $template_name, $args, $template_path, $default_path);
        if ($filter_template !== $template) {
            if (!file_exists($filter_template)) {
                // translators: %s template.
                sb_doing_it_wrong(__FUNCTION__, sprintf(__('%s does not exist.', 'sb-notices'), '<code>' . $template . '</code>'), '6.2.2');
                return;
            }
            $template = $filter_template;
        }
        $action_args = ['template_name' => $template_name, 'template_path' => $template_path, 'located' => $template, 'args' => $args];
        if (!empty($args) && is_array($args)) {
            if (isset($args['action_args'])) {
                sb_doing_it_wrong(__FUNCTION__, __('action_args should not be overwritten when calling sb_get_template.', 'sb-notices'), '1.0.0');
                unset($args['action_args']);
            }
            extract($args);
        }
        do_action('sb_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args']);
        include $action_args['located'];
        do_action('sb_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args']);
    }
}
if (!function_exists('InstagramFeed\Vendor\Smashballoon\Framework\sb_map_notice_hooks')) {
    /**
     * Map notices hooks as per plugin name.
     *
     * @param string $plugin_name Plugin name.
     *
     * @return string $plugin_hook Plugin hook.
     */
    function sb_map_notice_hooks($plugin_name)
    {
        $notice_hooks = ['instagram-feed' => 'sbi_admin_notices', 'instagram-feed-pro' => 'sbi_admin_notices', 'custom-facebook-feed' => 'cff_admin_notices', 'custom-facebook-feed-pro' => 'cff_admin_notices'];
        $plugin_hook = isset($notice_hooks[$plugin_name]) ? $notice_hooks[$plugin_name] : 'admin_notices';
        return $plugin_hook;
    }
}
if (!function_exists('InstagramFeed\Vendor\Smashballoon\Framework\sb_get_plugin_type')) {
    /**
     * Check if the plugin is free or pro.
     *
     * @param string $plugin_name Plugin name.
     *
     * @return string $plugin_type Plugin type.
     */
    function sb_get_plugin_type($plugin_name)
    {
        $plugins = ['instagram-feed' => 'free', 'instagram-feed-pro' => 'pro', 'custom-facebook-feed' => 'free', 'custom-facebook-feed-pro' => 'pro'];
        $plugin_type = isset($plugins[$plugin_name]) ? $plugins[$plugin_name] : 'free';
        return $plugin_type;
    }
}
if (!function_exists('InstagramFeed\Vendor\Smashballoon\Framework\flatten_array')) {
    /**
     * Flatten a multidimensional array.
     * 
     * @param array $array Array to flatten.
     * 
     * @return array $result Flattened array.
     */
    function flatten_array($array)
    {
        $result = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                $result = array_merge($result, flatten_array($value));
            } else {
                $result[] = $value;
            }
        }
        return $result;
    }
}
if (!function_exists('InstagramFeed\Vendor\Smashballoon\Framework\sb_get_active_plugins')) {
    /**
     * Get active plugins.
     *
     * @return array $active_plugins Active plugins.
     */
    function sb_get_active_plugins()
    {
        if (is_multisite()) {
            $active_plugins = array_keys((array) get_site_option('active_sitewide_plugins', array()));
        } else {
            $active_plugins = (array) get_option('active_plugins', array());
        }
        return $active_plugins;
    }
}
