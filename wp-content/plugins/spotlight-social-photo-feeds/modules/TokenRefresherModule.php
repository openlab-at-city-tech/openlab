<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\ServiceList;
use Dhii\Services\Factories\Value;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Actions\RefreshAccessTokensAction;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Wp\CronJob;

/**
 * The module that provides access token refreshing functionality.
 *
 * @since 0.3
 */
class TokenRefresherModule extends Module
{
    /**
     * The repetition interval for the cron job.
     *
     * @since 0.3
     */
    const CFG_CRON_INTERVAL = 'weekly';

    /**
     * @inheritDoc
     *
     * @since 0.3
     */
    public function getFactories(): array
    {
        return [
            //==========================================================================
            // CRON JOB
            //==========================================================================

            // The hook for the cron
            'hook' => new Value('spotlight/instagram/refresh_access_tokens'),

            // The args to pass to the cron's handlers
            'args' => new Value([]),

            // The repetition for the cron
            'repeat' => new Value(static::CFG_CRON_INTERVAL),

            // The main handler for the cron
            'main_handler' => new Constructor(RefreshAccessTokensAction::class, [
                '@ig/api/client',
                '@accounts/cpt',
            ]),

            // The list of handlers for the cron
            'handlers' => new ServiceList([
                'main_handler',
            ]),

            // The cron job instance
            'job' => new Constructor(CronJob::class, [
                'hook',
                'args',
                'repeat',
                'handlers',
            ]),
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
            // Register the cron job
            'wp/cron_jobs' => new ArrayExtension([
                'job',
            ]),
        ];
    }
}
