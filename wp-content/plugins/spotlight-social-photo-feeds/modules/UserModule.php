<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factory;
use LiteSpeed\Conf;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Config\ConfigEntry;
use RebelCode\Spotlight\Instagram\Config\WpOption;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Wp\PostType;

class UserModule extends Module
{
    public function run(ContainerInterface $c): void
    {
        add_action('spotlight/instagram/init', function () use ($c) {
            // If the start date is not saved and the user has feeds, set the start date
            if ($c->get('is_new_with_feeds')) {
                /** @var ConfigEntry $dateStarted */
                $dateStarted = $c->get('config/date_started');
                $dateStarted->setValue(time());
            }
        });
    }

    /** @inerhitDoc */
    public function getFactories(): array
    {
        return [
            'config/date_started' => new Factory([], function () {
                return new WpOption('sli_date_started', 0, true, WpOption::SANITIZE_INT);
            }),
            'is_new' => new Factory(['config/date_started', '@feeds/cpt'],
                function (ConfigEntry $dateStarted, PostType $feeds) {
                    return $dateStarted->getValue() === 0 && $feeds->getTotalNum() === 0;
                }
            ),
            'is_new_with_feeds' => new Factory(['config/date_started', '@feeds/cpt'],
                function (ConfigEntry $dateStarted, PostType $feeds) {
                    return $dateStarted->getValue() === 0 && $feeds->getTotalNum() > 0;
                }
            ),
        ];
    }

    /** @inerhitDoc */
    public function getExtensions(): array
    {
        return [
            'config/entries' => new ArrayExtension([
                'dateStarted' => 'config/date_started',
            ]),
        ];
    }
}
