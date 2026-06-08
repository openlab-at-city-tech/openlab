<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Media;

use RebelCode\Iris\Data\Source;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Server;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The handler for the endpoint that provides media objects.
 */
class GetMediaEndPoint extends AbstractEndpointHandler
{
    /** @var Server */
    protected $server;

    /** @var int */
    protected $expiry;

    /** Constructor */
    public function __construct(Server $server, int $expiry = 0)
    {
        $this->server = $server;
        $this->expiry = $expiry;
    }

    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        $from = $request->get_param('from') ?? 0;
        $num = $request->get_param('num') ?? null;

        if ($request->has_param('source')) {
            $srcName = $request->get_param('source');
            $srcType = $request->get_param('type');

            $source = new Source($srcName, $srcType);
            $result = $this->server->getSourceMedia($source, $from, $num);
        } else {
            $options = $request->get_param('options') ?? [];
            $result = $this->server->getFeedMedia($options, $from, $num);
        }

        $headers = [];
        if ($this->expiry > 0) {
            $headers['Expires'] = gmdate('D, d M Y H:i:s T', $this->expiry);
        }

        return new WP_REST_Response($result, 200, $headers);
    }
}
