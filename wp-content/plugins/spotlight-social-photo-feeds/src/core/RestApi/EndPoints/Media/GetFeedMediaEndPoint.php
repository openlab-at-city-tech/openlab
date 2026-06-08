<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Media;

use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Server;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The handler for the endpoint that fetches media from Instagram.
 *
 * @since 0.1
 */
class GetFeedMediaEndPoint extends AbstractEndpointHandler
{
    /** @var Server */
    protected $server;

    /** @var int */
    protected $expiry;

    /**
     * Constructor.
     *
     * @param Server $server The server instance.
     * @param int    $expiry The value of the expiry header to send to the browser.
     */
    public function __construct(Server $server, int $expiry = 0)
    {
        $this->server = $server;
        $this->expiry = $expiry;
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    protected function handle(WP_REST_Request $request)
    {
        $options = $request->get_param('options') ?? [];
        $from = $request->get_param('from') ?? 0;
        $num = $request->get_param('num') ?? null;

        $result = $this->server->getFeedMedia($options, $from, $num);
        $headers = [];

        if ($this->expiry > 0) {
            $headers['Expires'] = gmdate('D, d M Y H:i:s T', $this->expiry);
        }

        return new WP_REST_Response($result, 200, $headers);
    }
}
