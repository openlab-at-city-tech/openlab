<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\BlackFriday;

use RebelCode\Spotlight\Instagram\BlackFriday\BlackFridayCampaign;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_REST_Request;
use WP_REST_Response;

/** Hides the Black Friday banner for the current user. */
class DismissBannerEndPoint extends AbstractEndpointHandler
{
    /** @var BlackFridayCampaign */
    protected $campaign;

    public function __construct(BlackFridayCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    protected function handle(WP_REST_Request $request)
    {
        $this->campaign->dismiss();

        return new WP_REST_Response(['success' => true]);
    }
}
