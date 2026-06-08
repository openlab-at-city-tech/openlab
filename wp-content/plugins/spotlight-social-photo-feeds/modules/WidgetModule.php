<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factory;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Feeds\FeedWidget;
use RebelCode\Spotlight\Instagram\Module;

/**
 * The module that adds the widget (to display feeds) to the plugin.
 *
 * @since 0.1
 */
class WidgetModule extends Module
{
    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getFactories() : array
    {
        return [
            // The WP widget class - renders using the shortcode
            'class' => new Factory(['@feeds/cpt', '@shortcode/callback'], function ($cpt, $shortcode) {
                FeedWidget::$cpt = $cpt;
                FeedWidget::$shortcode = $shortcode;

                return FeedWidget::class;
            }),
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
            'wp/widgets' => new ArrayExtension([
                'class',
            ]),
        ];
    }
}
