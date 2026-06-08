<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Media;

use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Server;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The endpoint for importing media.
 */
class ImportMediaEndPoint extends AbstractEndpointHandler
{
    /** @var Server */
    protected $server;

    /** Constructor */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        $options = $request->get_param('options') ?? [];
        $result = $this->server->import($options);

        return new WP_REST_Response($result);
    }
}
