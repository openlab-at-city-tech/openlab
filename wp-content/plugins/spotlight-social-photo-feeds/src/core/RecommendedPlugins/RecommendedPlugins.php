<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\RecommendedPlugins;

/**
 * The bundled list of RebelCode plugins that Spotlight recommends, and their install state on this site.
 *
 * Nothing here talks to a remote server. Install and activation go through WordPress core.
 */
class RecommendedPlugins
{
    const NOT_INSTALLED = 'not_installed';
    const INSTALLED = 'installed';
    const ACTIVE = 'active';

    /** @var array[] */
    protected $plugins;

    /**
     * @param array[] $plugins Each entry has a "slug", "name", "description" and "icon".
     */
    public function __construct(array $plugins)
    {
        $this->plugins = array_values($plugins);
    }

    public function getAll(): array
    {
        return $this->plugins;
    }

    /** The main file of an installed plugin, relative to the plugins directory, or null if not installed. */
    public function getFile(string $slug): ?string
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $files = array_keys(get_plugins('/' . $slug));

        return empty($files) ? null : $slug . '/' . $files[0];
    }

    public function getStatus(string $slug): string
    {
        $file = $this->getFile($slug);

        if ($file === null) {
            return static::NOT_INSTALLED;
        }

        return is_plugin_active($file) ? static::ACTIVE : static::INSTALLED;
    }

    // Unescaped, unlike wp_nonce_url(), because the React app also uses these URLs. Escape them on output.
    public function getInstallUrl(string $slug): string
    {
        return add_query_arg(
            '_wpnonce',
            wp_create_nonce('install-plugin_' . $slug),
            self_admin_url('update.php?action=install-plugin&plugin=' . rawurlencode($slug))
        );
    }

    public function getActivateUrl(string $file): string
    {
        return add_query_arg(
            '_wpnonce',
            wp_create_nonce('activate-plugin_' . $file),
            self_admin_url('plugins.php?action=activate&plugin=' . rawurlencode($file))
        );
    }

    public function getDetailsUrl(string $slug): string
    {
        return self_admin_url(
            'plugin-install.php?tab=plugin-information&plugin=' . rawurlencode($slug) . '&TB_iframe=true&width=772&height=550'
        );
    }
}
