<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Review;

use RebelCode\Spotlight\Instagram\Config\ConfigEntry;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_REST_Request;
use WP_REST_Response;

class LeaveReviewEndPoint extends AbstractEndpointHandler
{
    /** @var ConfigEntry */
    protected $didReview;

    /** Constructor */
    public function __construct(ConfigEntry $didReview)
    {
        $this->didReview = $didReview;
    }

    protected function handle(WP_REST_Request $request)
    {
        $this->didReview->setValue(true);

        return new WP_REST_Response(['success' => true]);
    }
}
