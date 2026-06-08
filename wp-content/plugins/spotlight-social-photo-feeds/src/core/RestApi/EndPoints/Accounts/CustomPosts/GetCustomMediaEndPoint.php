<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts\CustomPosts;

use RebelCode\Iris\Data\Item;
use RebelCode\Spotlight\Instagram\PostTypes\CustomMedia;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Server;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class GetCustomMediaEndPoint extends AbstractEndpointHandler
{
    /** @var Server */
    protected $server;

    /** @var PostType */
    protected $cpt;

    /** Constructor. */
    public function __construct(Server $server, PostType $cpt)
    {
        $this->server = $server;
        $this->cpt = $cpt;
    }

    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        $accountId = $request->get_url_params()['id'] ?? null;
        if (empty($accountId)) {
            return new WP_Error('sli_missing_id', 'Missing account ID in request', ['status' => 400]);
        }

        $accountId = intval($accountId);
        $customPosts = CustomMedia::getForAccount($accountId);

        $customPosts = Arrays::map($customPosts, function (Item $item) {
            return $this->server->transform($item);
        });

        return new WP_REST_Response($customPosts);
    }
}
