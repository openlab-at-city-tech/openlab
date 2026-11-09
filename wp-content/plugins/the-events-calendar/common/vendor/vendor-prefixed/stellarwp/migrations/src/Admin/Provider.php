<?php

/**
 * Admin Service Provider.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Admin
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\Admin;

use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Utilities\Log_Download_Handler;
/**
 * Admin Service Provider.
 *
 * Registers hidden admin pages for the migrations UI.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Admin
 */
class Provider
{
    /**
     * Whether the provider has been registered.
     *
     * @since 0.0.1
     *
     * @var bool
     */
    private static bool $registered = false;
    /**
     * The parent page slug for sidebar highlighting.
     *
     * When set, the admin sidebar will highlight this menu item
     * while viewing the single migration page.
     *
     * @since 0.0.1
     *
     * @var ?string
     */
    protected static ?string $parent_page = null;
    /**
     * Stores the original parent file during hijacking.
     *
     * @since 0.0.1
     *
     * @var ?string
     */
    protected static ?string $stored_parent_file = null;
    /**
     * The URL of the migrations list page.
     *
     * When set, the single migration page will display a back link.
     *
     * @since 0.0.1
     *
     * @var ?string
     */
    protected static ?string $list_url = null;
    /**
     * Register the admin provider.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function register(): void
    {
        if (self::$registered) {
            return;
        }
        self::$registered = true;
        add_action('admin_menu', [$this, 'register_hidden_page']);
        add_filter('set-screen-option', [$this, 'save_screen_option'], 10, 3);
        add_filter('submenu_file', [$this, 'hijack_current_parent_file']);
        add_action('adminmenu', [$this, 'restore_current_parent_file']);
        add_action('admin_post_' . Log_Download_Handler::get_action_name(), [Log_Download_Handler::class, 'download']);
    }
    /**
     * Register hidden admin pages.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function register_hidden_page(): void
    {
        $single_page_hook = add_submenu_page('', __('Migration Details', 'stellarwp-migrations'), __('Migration Details', 'stellarwp-migrations'), 'manage_options', self::get_single_page_slug(), [$this, 'render_single_page']);
        if ($single_page_hook && is_string($single_page_hook)) {
            add_action('load-' . $single_page_hook, [$this, 'add_screen_options']);
            add_action('load-' . $single_page_hook, [$this, 'set_page_title']);
        }
    }
    /**
     * Add screen options for the single migration page.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function add_screen_options(): void
    {
        add_screen_option('per_page', ['label' => __('Logs per page', 'stellarwp-migrations'), 'default' => 10, 'option' => Assets::get_logs_per_page_option()]);
    }
    /**
     * Set the page title for the single migration page.
     *
     * Prevents WordPress from passing null to strip_tags() when rendering
     * the admin header for hidden submenu pages.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function set_page_title(): void
    {
        global $title;
        if (empty($title)) {
            $title = __('Migration Details', 'stellarwp-migrations');
            // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- It's intended to override the global title.
        }
    }
    /**
     * Save the screen option value.
     *
     * @since 0.0.1
     *
     * @param mixed  $status The current status (false by default).
     * @param string $option The option name.
     * @param mixed  $value  The option value.
     *
     * @return mixed The value to save, or false to not save.
     */
    public function save_screen_option($status, string $option, $value)
    {
        if (Assets::get_logs_per_page_option() === $option && is_numeric($value)) {
            return max(1, (int) $value);
        }
        return $status;
    }
    /**
     * Render the single migration page.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function render_single_page(): void
    {
        $raw_migration_id = filter_input(INPUT_GET, 'migration_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $migration_id = sanitize_key(is_string($raw_migration_id) ? $raw_migration_id : '');
        if (empty($migration_id)) {
            Config::get_template_engine()->template('single-not-found', ['migration_id' => '']);
            return;
        }
        Config::get_container()->get(UI::class)->render_single($migration_id);
    }
    /**
     * Get the single page slug.
     *
     * @since 0.0.1
     *
     * @return string The page slug.
     */
    public static function get_single_page_slug(): string
    {
        return 'stellarwp-migrations-' . Config::get_hook_prefix() . '-single';
    }
    /**
     * Get the URL for a single migration page.
     *
     * @since 0.0.1
     *
     * @param string $migration_id The migration ID.
     *
     * @return string The URL to the single migration page.
     */
    public static function get_single_url(string $migration_id): string
    {
        return admin_url(add_query_arg(['migration_id' => rawurlencode($migration_id)], 'admin.php?page=' . self::get_single_page_slug()));
    }
    /**
     * Set the parent page slug for sidebar highlighting.
     *
     * When set, viewing the single migration page will highlight
     * the specified menu item in the admin sidebar.
     *
     * @since 0.0.1
     *
     * @param string $parent_page The parent menu slug (e.g. 'edit.php?post_type=page').
     *
     * @return void
     */
    public static function set_parent_page(string $parent_page): void
    {
        self::$parent_page = $parent_page;
    }
    /**
     * Set the URL for the migrations list page.
     *
     * When set, the single migration page will display a link
     * back to the list page.
     *
     * @since 0.0.1
     *
     * @param string $url The URL of the migrations list page.
     *
     * @return void
     */
    public static function set_list_url(string $url): void
    {
        self::$list_url = $url;
    }
    /**
     * Get the URL for the migrations list page.
     *
     * @since 0.0.1
     *
     * @return string|null The list page URL, or null if not set.
     */
    public static function get_list_url(): ?string
    {
        return self::$list_url;
    }
    /**
     * Hijack the current parent file for sidebar highlighting.
     *
     * When a parent page is configured and we are on the single
     * migration page, this overrides `$plugin_page` so the correct
     * admin menu item is highlighted in the sidebar.
     *
     * @since 0.0.1
     *
     * @param string $submenu_file The submenu file.
     *
     * @return string The submenu file (unchanged).
     */
    public function hijack_current_parent_file($submenu_file)
    {
        if (!self::$parent_page) {
            return $submenu_file;
        }
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $current_page = isset($_GET['page']) && is_string($_GET['page']) ? wp_unslash(sanitize_text_field($_GET['page'])) : '';
        if ($current_page !== self::get_single_page_slug()) {
            return $submenu_file;
        }
        global $plugin_page;
        self::$stored_parent_file = is_string($plugin_page) ? $plugin_page : null;
        $plugin_page = self::$parent_page;
        // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        return $submenu_file;
    }
    /**
     * Restore the original parent file after the admin menu renders.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function restore_current_parent_file(): void
    {
        if (!isset(self::$stored_parent_file)) {
            return;
        }
        global $plugin_page;
        $plugin_page = self::$stored_parent_file;
        // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
    }
    /**
     * Reset registration status. Primarily for testing.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$registered = false;
        self::$parent_page = null;
        self::$stored_parent_file = null;
        self::$list_url = null;
    }
}