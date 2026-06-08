<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Extension;
use Dhii\Services\Factories\Value;
use Dhii\Services\Factory;
use RebelCode\Spotlight\Instagram\Config\ConfigEntry;
use RebelCode\Spotlight\Instagram\Config\WpOption;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Di\EndPointService;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Review\LeaveReviewEndPoint;

class LeaveReviewModule extends Module
{
    /** @inerhitDoc */
    public function getFactories(): array
    {
        return [
            'enabled' => new Value(false),
            'show_after' => new Value(14 * DAY_IN_SECONDS),
            'show_banner' => new Factory(
                ['enabled', 'config/did_review', '@user/config/date_started', 'show_after'],
                function (bool $enabled, ConfigEntry $didReview, ConfigEntry $startDate, int $threshold) {
                    if (!$enabled || $didReview->getValue()) {
                        return false;
                    }

                    $timeSinceStart = time() - $startDate->getValue();

                    return $timeSinceStart >= $threshold;
                }
            ),
            'config/did_review' => new Factory([], function () {
                return new WpOption('sli_did_review', false, false, WpOption::SANITIZE_BOOL);
            }),
            'endpoint' => new EndPointService(
                '/leave_review/?',
                ['POST'],
                LeaveReviewEndPoint::class,
                ['config/did_review'],
                '@rest_api/auth/user'
            ),
        ];
    }

    /** @inerhitDoc */
    public function getExtensions(): array
    {
        return [
            'ui/l10n/admin-common' => new Extension(['show_banner'], function ($l10n, bool $showBanner) {
                $l10n['showReviewBanner'] = $showBanner;
                return $l10n;
            }),
            'rest_api/endpoints' => new ArrayExtension([
                'endpoint'
            ])
        ];
    }
}
