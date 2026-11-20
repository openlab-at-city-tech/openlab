<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\RecommendedPlugins;

use RebelCode\Spotlight\Instagram\RecommendedPlugins\RecommendedPluginsBanner;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_REST_Request;
use WP_REST_Response;

class DismissBannerEndPoint extends AbstractEndpointHandler
{
    /** @var RecommendedPluginsBanner */
    protected $banner;

    public function __construct(RecommendedPluginsBanner $banner)
    {
        $this->banner = $banner;
    }

    protected function handle(WP_REST_Request $request)
    {
        $this->banner->dismiss();

        return new WP_REST_Response(['success' => true]);
    }
}
