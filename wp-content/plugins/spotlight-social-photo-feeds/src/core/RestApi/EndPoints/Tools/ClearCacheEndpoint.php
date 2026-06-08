<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Tools;

use Exception;
use RebelCode\Spotlight\Instagram\Vendor\Psr\SimpleCache\CacheInterface;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The handler for the REST API endpoint that clears the cache.
 */
class ClearCacheEndpoint extends AbstractEndpointHandler
{
    /** @var CacheInterface */
    protected $apiCache;

    /** @var callable */
    protected $deleteAction;

    /** Constructor */
    public function __construct(CacheInterface $cache, callable $deleteAction)
    {
        $this->apiCache = $cache;
        $this->deleteAction = $deleteAction;
    }

    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        try {
            $this->apiCache->clear();

            $count = ($this->deleteAction)();

            if ($count === false) {
                throw new Exception('Failed to clear the media cache. Please try again later.');
            }

            do_action('spotlight/instagram/rest_api/clear_cache');

            return new WP_REST_Response(['success' => true]);
        } catch (Exception $exc) {
            return new WP_REST_Response(['success' => false, 'error' => $exc->getMessage()], 500);
        }
    }
}
