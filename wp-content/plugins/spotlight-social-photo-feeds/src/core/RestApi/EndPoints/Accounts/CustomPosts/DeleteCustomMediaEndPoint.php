<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\CustomPosts;

use RebelCode\Spotlight\Instagram\PostTypes\CustomMedia;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class DeleteCustomMediaEndPoint extends AbstractEndpointHandler
{
    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        $accountId = $request->get_param('account_id');
        $mediaId = $request->get_param('media_id');

        if (empty($accountId)) {
            return new WP_Error('sli_missing_account_id', 'Missing account ID in request', ['status' => 400]);
        }

        if (empty($mediaId)) {
            return new WP_Error('sli_missing_media_id', 'Missing media ID in request', ['status' => 400]);
        }

        $success = CustomMedia::deleteCustomMedia((int) $accountId, $mediaId);

        if (!$success) {
            return new WP_Error('sli_delete_error', 'Failed to delete custom post from account', ['status' => 500]);
        }

        return new WP_REST_Response(['success' => true]);
    }
}
