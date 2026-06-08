<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\CustomPosts;

use RebelCode\Spotlight\Instagram\PostTypes\CustomMedia;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class AddCustomMediaEndPoint extends AbstractEndpointHandler
{
    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        $accountId = $request->get_url_params()['id'] ?? null;
        if (empty($accountId)) {
            return new WP_Error('sli_missing_id', 'Missing account ID in request', ['status' => 400]);
        }

        $data = $request->get_json_params();
        $success = CustomMedia::addCustomMedia((int) $accountId, $data);

        if (!$success) {
            return new WP_Error('sli_update_error', 'Failed to add custom post to account', ['status' => 500]);
        }

        return new WP_REST_Response(['success' => true]);
    }
}
