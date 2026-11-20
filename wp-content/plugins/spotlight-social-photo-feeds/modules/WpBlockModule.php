<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Wp\Asset;

/**
 * The module that adds the Spotlight block type to the WordPress block editor.
 *
 * @since 0.3
 */
class WpBlockModule extends Module
{
    /**
     * @inheritDoc
     *
     * @since 0.3
     */
    public function run(ContainerInterface $c): void
    {
        add_action('init', function () use ($c) {
            // Make editor styles available before WordPress collects iframe assets.
            Asset::register('sli-wp-block-js', $c->get('editor_script'));
            Asset::register('sli-wp-block-css', $c->get('editor_style'));

            register_block_type($c->get('metadata_path'), $c->get('args'));
        });

        add_action('enqueue_block_editor_assets', function () use ($c) {
            // Makes sure script config is localized
            do_action('spotlight/instagram/localize_config');

            // Triggers action to allow extension
            do_action('spotlight/wp_block/register_assets');
        });
    }

    /**
     * @inheritDoc
     *
     * @since 0.3
     */
    public function getFactories(): array
    {
        return [
            'metadata_path' => new Factory(['@plugin/dir'], function ($dir) {
                return $dir . '/ui/block.json';
            }),
            'args' => new Factory(['render_fn'], function ($renderFn) {
                return [
                    'render_callback' => $renderFn,
                ];
            }),
            'editor_script' => new Factory(
                ['@ui/scripts_url', '@ui/assets_ver', 'script_deps'],
                function ($url, $ver, $deps) {
                    return Asset::script("{$url}/wp-block.js", $ver, $deps);
                }
            ),
            'editor_style' => new Factory(
                ['@ui/scripts_url', '@ui/assets_ver', 'style_deps'],
                function ($url, $ver, $deps) {
                    return Asset::style("{$url}/styles/wp-block.css", $ver, $deps);
                }
            ),
            'script_deps' => new Value([
                'sli-admin-common',
                'sli-editor',
                'wp-blocks',
                'wp-block-editor',
                'wp-components',
                'wp-element',
                'wp-i18n',
            ]),
            'style_deps' => new Value([
                'sli-admin-common',
                'sli-editor',
                'wp-components',
            ]),
            'render_fn' => new Factory(['@shortcode/callback'], function ($shortcode) {
                return function ($attrs) use ($shortcode) {
                    $feedId = $attrs['feedId'] ?? 0;
                    $className = $attrs['className'] ?? '';

                    return (is_numeric($feedId) && $feedId > 0)
                        ? call_user_func($shortcode, ['feed' => $feedId, 'class-name' => $className])
                        : '';
                };
            }),
        ];
    }
}
