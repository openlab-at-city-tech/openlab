<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\FuncService;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Config\ConfigSet;
use RebelCode\Spotlight\Instagram\Config\NullConfigEntry;
use RebelCode\Spotlight\Instagram\Config\WpOption;
use RebelCode\Spotlight\Instagram\Module;

class ConfigModule extends Module
{
    /* Config key for the media preload option. */
    const PRELOAD_MEDIA = 'preloadMedia';

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getFactories(): array
    {
        return [
            // The config set
            'set' => new Factory(['entries', 'default'], function ($entries, $default) {
                return new ConfigSet($entries, $default);
            }),

            // Entries for the config
            'entries' => new Value([
                static::PRELOAD_MEDIA => new WpOption('sli_preload_media', false, false, WpOption::SANITIZE_BOOL),
            ]),

            // The callback used by the config set to create options that do not exist
            'default' => new FuncService([], function () {
                return new NullConfigEntry();
            }),
        ];
    }
}
