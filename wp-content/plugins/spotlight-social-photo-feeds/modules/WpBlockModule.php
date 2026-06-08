<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Wp\Asset;
use WP_Block_Type;

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
        add_action('enqueue_block_editor_assets', function () use ($c) {
            // Register block assets
            Asset::register('sli-wp-block-js', $c->get('editor_script'));
            Asset::register('sli-wp-block-css', $c->get('editor_style'));

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
            'type' => new Constructor(WP_Block_Type::class, ['id', 'args']),
            'id' => new Value('spotlight/instagram'),
            'args' => new Factory(['render_fn'], function ($renderFn) {
                return [
                    'editor_script' => 'sli-wp-block-js',
                    'editor_style' => 'sli-wp-block-css',
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
            ]),
            'style_deps' => new Value([
                'sli-admin-common',
                'sli-editor',
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

    /**
     * @inheritDoc
     *
     * @since 0.3
     */
    public function getExtensions(): array
    {
        return [
            // Register the block type to WordPress
            'wp/block_types' => new ArrayExtension(['type']),
        ];
    }
}
