<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Extension;
use Dhii\Services\Factory;
use RebelCode\Spotlight\Instagram\Config\ConfigEntry;
use RebelCode\Spotlight\Instagram\Config\WpOption;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Di\EndPointService;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Analytics\SubmitSurveyEndPoint;
use RebelCode\WordPress\Http\WpClient;

class AnalyticsSurveyModule extends Module
{
    public function getFactories(): array
    {
        return [
            'config/did_survey' => new Factory([], function () {
                return new WpOption('sli_did_analytics_survey', false, false, WpOption::SANITIZE_BOOL);
            }),
            'endpoint' => new EndPointService(
                '/analytics/survey/?',
                ['POST'],
                SubmitSurveyEndPoint::class,
                ['client', 'config/did_survey'],
                '@rest_api/auth/user'
            ),
            'client' => new Factory([], function () {
                return WpClient::createDefault(null, ['timeout' => 10]);
            }),
        ];
    }

    public function getExtensions(): array
    {
        return [
            'ui/l10n/admin-common' => new Extension(['config/did_survey'], function ($l10n, ConfigEntry $didSurvey) {
                $l10n['didAnalyticsSurvey'] = $didSurvey->getValue();
                return $l10n;
            }),
            'rest_api/endpoints' => new ArrayExtension([
                'endpoint'
            ]),
        ];
    }
}
