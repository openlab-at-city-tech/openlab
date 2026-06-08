<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Extension;
use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\ServiceList;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use Psr\Container\ContainerInterface;
use RebelCode\Iris\Importer;
use RebelCode\Spotlight\Instagram\Actions\BatchHandler;
use RebelCode\Spotlight\Instagram\Actions\UpdateAccountsAction;
use RebelCode\Spotlight\Instagram\Actions\UpdateFeedsAction;
use RebelCode\Spotlight\Instagram\Config\ConfigEntry;
use RebelCode\Spotlight\Instagram\Config\WpOption;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Di\ConfigService;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Wp\CronJob;

class UpdateCronModule extends Module
{
    /** The config key for the update interval setting. */
    const CONFIG_UPDATE_INTERVAL = 'importerInterval';

    public function run(ContainerInterface $c): void
    {
        add_action('init', function () {
            // Unschedule old crons
            wp_unschedule_hook('spotlight/instagram/import_media');
            wp_unschedule_hook('spotlight/instagram/import');
        });

        add_action('admin_init', function () use ($c) {
            $schedules = wp_get_schedules();
            $repeat = $c->get('main/repeat');

            if (!array_key_exists($repeat, $schedules)) {
                add_action('admin_notices', function () {
                    $url = admin_url('admin.php?page=spotlight-instagram&screen=settings&tab=config');
                    $linkText = __('Instagram feeds » Settings » Configuration', 'sl-insta');
                    $link = sprintf('<b><a href="%s">%s</a></b>', $url, $linkText);

                    $line1 = __(
                        'Your Instagram feeds cannot be auto-updated due to a missing cron schedule.',
                        'sl-insta'
                    );

                    $line2 = sprintf(
                        __(
                            'Please go to the %s page and set a new interval for how often Spotlight checks for new posts.',
                            'sl-insta'
                        ),
                        $link
                    );

                    printf(
                        '<div class="notice notice-error sli-notice sli-update-interval-notice"><p>%s</p><p>%s</p></div>',
                        $line1,
                        $line2
                    );
                });
            }
        });

        // Register the batch handler
        // We don't use the WpModule's API for this because the cron is not auto-scheduled, but scheduled on-demand
        add_action($c->get('engine/importer/scheduler/cron/hook'), $c->get('batch/handler'));
    }

    public function getFactories(): array
    {
        return [
            //==========================================================================
            // BATCH IMPORT CRON
            //==========================================================================

            'batch/handler' => new Constructor(BatchHandler::class, ['@engine/importer']),

            //==========================================================================
            // MAIN UPDATE CRON
            //==========================================================================

            'main/hook' => new Value('spotlight/instagram/update'),
            'main/args' => new Value([]),
            'main/repeat' => new ConfigService('@config/set', static::CONFIG_UPDATE_INTERVAL),

            // The cron handler for updating account info
            'main/handlers/accounts' => new Constructor(UpdateAccountsAction::class, [
                '@ig/api/client',
                '@accounts/cpt',
            ]),

            // The cron handler for fetching media for feeds
            'main/handlers/feeds' => new Constructor(UpdateFeedsAction::class, [
                '@engine/importer',
                '@feeds/manager'
            ]),

            // The list of handlers for the cron
            'main/handlers' => new ServiceList([
                'main/handlers/accounts',
                'main/handlers/feeds',
            ]),

            // The cron job instance
            'main/job' => new Constructor(CronJob::class, [
                'main/hook',
                'main/args',
                'main/repeat',
                'main/handlers',
            ]),

            //==========================================================================
            // CONFIG ENTRIES
            //==========================================================================

            // The config entry that stores the cron's repetition interval
            'config/interval' => new Value(new WpOption('sli_importer_interval', 'hourly')),
        ];
    }

    public function getExtensions(): array
    {
        return [
            // Register the cron job
            'wp/cron_jobs' => new ArrayExtension([
                'main/job',
            ]),
            // Register the config entries
            'config/entries' => new ArrayExtension([
                static::CONFIG_UPDATE_INTERVAL => 'config/interval',
            ]),
            // Override the API cache with the value of the import cron interval option
            'ig/cache/ttl' => new Extension(['config/interval'], function ($ttl, ConfigEntry $interval) {
                return $interval->getValue();
            }),
        ];
    }
}
