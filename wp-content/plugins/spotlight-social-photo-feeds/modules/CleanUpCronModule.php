<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\ServiceList;
use Dhii\Services\Factories\Value;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Actions\CleanUpMediaAction;
use RebelCode\Spotlight\Instagram\Config\WpOption;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Di\ConfigService;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Wp\CronJob;

/**
 * Provides a corn job for cleaning up old media.
 *
 * @since 0.1
 */
class CleanUpCronModule extends Module
{
    /**
     * Config key for the cleaner cron interval.
     *
     * @since 0.1
     */
    const CFG_CRON_INTERVAL = 'cleanerInterval';

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function getFactories() : array
    {
        return [
            //==========================================================================
            // CRON JOB
            //==========================================================================

            // The hook for the cron
            'hook' => new Value('spotlight/instagram/clean_up_media'),

            // The args to pass to the cron's handlers
            'args' => new Value([]),

            // The repetition for the cron, retrieved from config
            'repeat' => new ConfigService('@config/set', static::CFG_CRON_INTERVAL),

            // The cleanup action - also the main handler for the cron
            'action' => new Constructor(CleanUpMediaAction::class, [
                '@engine/instance',
                '@media/cpt',
                '@config/set',
            ]),

            // The list of handlers for the cron
            'handlers' => new ServiceList([
                'action',
            ]),

            // The cron job instance.
            'job' => new Constructor(CronJob::class, [
                'hook',
                'args',
                'repeat',
                'handlers',
            ]),

            //==========================================================================
            // CONFIG ENTRIES
            //==========================================================================

            // The config entry that stores the truncation age limit
            'config/age_limit' => new Value(new WpOption('sli_media_age_limit', '7 days')),

            // The config entry that stores the repetition interval for the cron
            'config/interval' => new Value(new WpOption('sli_clean_up_interval', 'daily')),
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
            // Register the cron job
            'wp/cron_jobs' => new ArrayExtension([
                'job',
            ]),
            // Register the config entries
            'config/entries' => new ArrayExtension([
                static::CFG_CRON_INTERVAL => 'config/interval',
                CleanUpMediaAction::CFG_AGE_LIMIT => 'config/age_limit',
            ]),
        ];
    }
}
