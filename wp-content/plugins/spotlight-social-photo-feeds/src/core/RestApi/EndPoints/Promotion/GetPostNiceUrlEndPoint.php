<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Promotion;

use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_REST_Request;
use WP_REST_Response;

class GetPostNiceUrlEndPoint extends AbstractEndpointHandler
{
    protected function handle(WP_REST_Request $request)
    {
        $id = $request->get_param('id');

        if (empty($id)) {
            return new WP_Error('sli_missing_id', __('Missing post ID in request', 'sl-insta'), [
                'status' => 400,
            ]);
        }

        return new WP_REST_Response(['niceUrl' => get_permalink($id)]);
    }
}
