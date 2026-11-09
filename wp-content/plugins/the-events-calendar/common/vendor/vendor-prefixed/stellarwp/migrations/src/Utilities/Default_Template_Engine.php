<?php

/**
 * Default Template Engine.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Utilities
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations\Utilities;

use TEC\Common\StellarWP\Migrations\Config;
use TEC\Common\StellarWP\Migrations\Contracts\Template_Engine;
/**
 * Default Template Engine.
 *
 * A simple PHP-based template engine that loads template files from the views directory.
 * Consumers can use this implementation or provide their own that implements the
 * Template_Engine interface.
 *
 * @since 0.0.1
 *
 * @package \TEC\Common\StellarWP\Migrations\Utilities
 */
class Default_Template_Engine implements Template_Engine
{
    /**
     * Render a template.
     *
     * @since 0.0.1
     *
     * @param string              $name    Template name (e.g., 'list', 'components/progress-bar').
     * @param array<string,mixed> $context Variables to pass to the template.
     * @param bool                $output  Whether to echo or return the output.
     *
     * @return string|void The rendered template if $echo is false, void otherwise.
     */
    public function template(string $name, array $context = [], bool $output = true)
    {
        $file = $this->get_template_path($name);
        if (!file_exists($file)) {
            if ($output) {
                return;
            }
            return '';
        }
        // Extract context variables into the local scope.
        // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Intentional for template variables.
        extract($context);
        if ($output) {
            include $file;
            return;
        }
        ob_start();
        include $file;
        return ob_get_clean() ?: '';
    }
    /**
     * Get the full path to a template file.
     *
     * @since 0.0.1
     *
     * @param string $name Template name.
     *
     * @return string Full path to the template file.
     */
    protected function get_template_path(string $name): string
    {
        $path = dirname(__DIR__) . '/views/' . $name . '.php';
        $prefix = Config::get_hook_prefix();
        /**
         * Filters the template path.
         *
         * Allows customization of where template files are loaded from.
         *
         * @since 0.0.1
         *
         * @param string $path The full path to the template file.
         * @param string $name The template name.
         *
         * @return string The full path to the template file.
         */
        return (string) apply_filters("stellarwp_migrations_{$prefix}_template_path", $path, $name);
    }
}