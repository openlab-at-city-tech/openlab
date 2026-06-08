<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\CustomPosts;

use RebelCode\Spotlight\Instagram\PostTypes\CustomMedia;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class UpdateCustomMediaEndPoint extends AbstractEndpointHandler
{
    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        $params = $request->get_url_params();
        $accountId = $params['account_id'] ?? null;
        $mediaId = $params['media_id'] ?? null;

        if (empty($accountId)) {
            return new WP_Error('sli_missing_account_id', 'Missing account ID in request', ['status' => 400]);
        }

        if (empty($mediaId)) {
            return new WP_Error('sli_missing_media_id', 'Missing media ID in request', ['status' => 400]);
        }

        $data = $request->get_json_params();
        CustomMedia::updateCustomMedia((int) $accountId, $mediaId, $data);

        return new WP_REST_Response(['success' => true]);
    }
}
