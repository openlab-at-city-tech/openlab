<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Value;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Module;

class AdminModule extends Module
{
    public function run(ContainerInterface $c): void
    {
        add_filter('plugin_action_links', function ($actions, $plugin) use ($c) {
            if (slInstaPluginInfo($plugin) !== null) {
                $idx = array_search('network_active', array_keys($actions));
                $newActions = $c->get('admin/plugin_row_actions');

                if ($idx === false) {
                    $actions = array_merge($newActions, $actions);
                } else {
                    array_splice($actions, $idx + 1, 0, $newActions);
                }
            }

            return $actions;
        }, 100, 2);

        add_filter('plugin_row_meta', function ($links, $plugin) use ($c) {
            if (slInstaPluginInfo($plugin) !== null) {
                $links = array_merge($links, $c->get('admin/plugin_meta_links'));
            }

            return $links;
        }, 100, 2);
    }

    public function getFactories(): array
    {
        return [
            'admin/plugin_row_actions' => new Value([
                'feeds' => sprintf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    admin_url('admin.php?page=spotlight-instagram'),
                    esc_attr(__('Feeds', 'sl-insta')),
                    __('Feeds', 'sl-insta')
                ),
            ]),
            'admin/plugin_meta_links' => new Value([
                'docs' => sprintf(
                    '<a href="%s" aria-label="%s" target="_blank">%s</a>',
                    'https://docs.spotlightwp.com',
                    esc_attr(__('Docs & FAQs', 'sl-insta')),
                    __('Docs & FAQs', 'sl-insta')
                ),
            ]),
        ];
    }
}
