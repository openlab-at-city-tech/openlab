<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules;

use DateTimeImmutable;
use Dhii\Services\Extension;
use Dhii\Services\Factories\Constructor;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use RebelCode\Spotlight\Instagram\BlackFriday\BlackFridayCampaign;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Di\EndPointService;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\BlackFriday\DismissBannerEndPoint;

/**
 * The Black Friday 2026 campaign: tells the admin UI whether the campaign is running and whether the current user
 * dismissed its banner, and provides the endpoint that saves the dismissal.
 */
class BlackFridayModule extends Module
{
    public function getFactories(): array
    {
        return [
            // 17 November 2026 00:00:00 to 1 December 2026 23:59:59, both GMT-07:00 and both inclusive. The issue gives the
            // end as "2026-12-01 11:59:59"; I read it as 11:59:59 PM, the same end that WP RSS Aggregator uses.
            'start' => new Factory([], function () {
                return (new DateTimeImmutable('2026-11-17T00:00:00-07:00'))->getTimestamp();
            }),
            'end' => new Factory([], function () {
                return (new DateTimeImmutable('2026-12-01T23:59:59-07:00'))->getTimestamp();
            }),

            // The year is part of the key, so that a later campaign starts with the banner shown again
            'meta_key' => new Value('sli_bf26_banner_dismissed'),

            'campaign' => new Constructor(BlackFridayCampaign::class, ['start', 'end', 'meta_key']),

            // Same capability as the Spotlight admin pages
            'endpoint' => new EndPointService(
                '/black_friday/dismiss',
                ['POST'],
                DismissBannerEndPoint::class,
                ['campaign'],
                '@rest_api/auth/user'
            ),
        ];
    }

    public function getExtensions(): array
    {
        return [
            'ui/l10n/admin-common' => new Extension(['campaign'], function ($l10n, BlackFridayCampaign $campaign) {
                $l10n['blackFriday'] = is_admin()
                    ? $campaign->getData()
                    : ['isActive' => false, 'isDismissed' => false];

                return $l10n;
            }),
            'rest_api/endpoints' => new ArrayExtension(['endpoint']),
        ];
    }
}
