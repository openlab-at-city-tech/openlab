<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Tools;

use RebelCode\Spotlight\Instagram\Actions\CleanUpMediaAction;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The endpoint for running the media clean up optimization.
 */
class CleanUpMediaEndpoint extends AbstractEndpointHandler
{
    /** @var CleanUpMediaAction */
    protected $action;

    /**
     * Constructor.
     *
     * @param CleanUpMediaAction $action The clean up action.
     */
    public function __construct(CleanUpMediaAction $action)
    {
        $this->action = $action;
    }

    /** @inerhitDoc */
    protected function handle(WP_REST_Request $request)
    {
        $ageLimit = $request->get_param('ageLimit') ?? null;
        $numCleaned = ($this->action)($ageLimit);

        return new WP_REST_Response(['success' => true, 'numCleaned' => $numCleaned]);
    }
}
