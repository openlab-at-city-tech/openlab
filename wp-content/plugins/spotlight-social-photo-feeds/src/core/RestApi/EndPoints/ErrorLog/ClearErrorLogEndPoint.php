<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\ErrorLog;

use RebelCode\Spotlight\Instagram\ErrorLog;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_REST_Request;
use WP_REST_Response;

class ClearErrorLogEndPoint extends AbstractEndpointHandler
{
    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        return new WP_REST_Response(['success' => ErrorLog::delete()]);
    }
}
