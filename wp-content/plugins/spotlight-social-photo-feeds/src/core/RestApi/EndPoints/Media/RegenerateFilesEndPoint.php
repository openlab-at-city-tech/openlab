<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Media;

use RebelCode\Spotlight\Instagram\Engine\IgPostStore;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_REST_Request;
use WP_REST_Response;

class RegenerateFilesEndPoint extends AbstractEndpointHandler
{
    /** @var IgPostStore */
    protected $store;

    /** Constructor.*/
    public function __construct(IgPostStore $store)
    {
        $this->store = $store;
    }

    /** @inerhitDoc */
    protected function handle(WP_REST_Request $request): WP_REST_Response
    {
        $this->store->regenerateFiles();

        return new WP_REST_Response([], 200);
    }
}
