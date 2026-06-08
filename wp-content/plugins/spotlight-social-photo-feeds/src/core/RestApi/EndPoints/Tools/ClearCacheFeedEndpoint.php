<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Tools;

use RebelCode\Iris\Engine;
use RebelCode\Spotlight\Instagram\Feeds\FeedManager;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Handler for the REST API endpoint that clears the cache for a specific feed.
 */
class ClearCacheFeedEndpoint extends AbstractEndpointHandler
{
    /** @var Engine */
    protected $engine;

    /** @var FeedManager */
    protected $feedManager;

    /** Constructor */
    public function __construct(Engine $engine, FeedManager $feedManager)
    {
        $this->engine = $engine;
        $this->feedManager = $feedManager;
    }

    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        $options = $request->get_param('options');
        $feed = $this->feedManager->createFeed($options);

        set_time_limit(60 * 5);
        $this->engine->getStore()->deleteForSources($feed->sources);

        return new WP_REST_Response(['success' => true]);
    }
}
