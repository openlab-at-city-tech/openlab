<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\Value;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Actions\RenderShortcode;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Wp\Shortcode;

/**
 * The module that adds the shortcode (to display feeds) to the plugin.
 *
 * @since 0.1
 */
class ShortcodeModule extends Module
{
    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getFactories() : array
    {
        return [
            'tag' => new Value('instagram'),
            'callback' => new Constructor(RenderShortcode::class, [
                '@feeds/cpt',
                '@accounts/cpt',
                '@feeds/template',
                '@server/instance',
                '@config/set',
            ]),
            'instance' => new Constructor(Shortcode::class, ['tag', 'callback']),
        ];
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getExtensions() : array
    {
        return [
            'wp/shortcodes' => new ArrayExtension(['instance']),
        ];
    }
}
