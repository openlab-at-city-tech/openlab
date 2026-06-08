<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factory;
use RebelCode\Spotlight\Instagram\Config\WpOption;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Di\EndPointService;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\Survey2023\Complete2023SurveyEndPoint;

class Survey2023Module extends Module
{
    public function getFactories(): array
    {
        return [
            'option' => new Factory([], function () {
                return new WpOption('sli_did_survey', false, true, WpOption::SANITIZE_BOOL);
            }),
            'endpoint' => new EndPointService(
                '/survey-2023',
                ['POST'],
                Complete2023SurveyEndPoint::class,
                ['option'],
                '@rest_api/auth/user'
            ),
        ];
    }

    public function getExtensions(): array
    {
        return [
            'config/entries' => new ArrayExtension([
                'did2023Survey' => 'option',
            ]),
            'rest_api/endpoints' => new ArrayExtension([
                'endpoint',
            ]),
        ];
    }
}
