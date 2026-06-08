<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Survey2023;

use RebelCode\Spotlight\Instagram\Config\WpOption;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_REST_Request;
use WP_REST_Response;

class Complete2023SurveyEndPoint extends AbstractEndpointHandler
{
    /** @var WpOption */
    protected $didSurvey;

    /**
     * Constructor.
     *
     * @param WpOption $didSurvey The option that stores whether the user has completed the survey.
     */
    public function __construct(WpOption $didSurvey)
    {
        $this->didSurvey = $didSurvey;
    }

    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        $this->didSurvey->setValue(true);

        return new WP_REST_Response([]);
    }
}
