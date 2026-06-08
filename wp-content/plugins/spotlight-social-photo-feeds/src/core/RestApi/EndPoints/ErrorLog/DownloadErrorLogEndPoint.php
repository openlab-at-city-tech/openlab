<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\ErrorLog;

use RebelCode\Spotlight\Instagram\ErrorLog;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use WP_REST_Request;
use WP_REST_Response;

class DownloadErrorLogEndPoint extends AbstractEndpointHandler
{
    protected function handle(WP_REST_Request $request)
    {
        if (!is_user_logged_in()) {
            return new WP_REST_Response('Unauthorized', 401);
        }

        header('Content-Type', 'text/plain');
        header('Content-Disposition', 'attachment; filename="sli-error-log.txt"');
        header('Content-Length', ErrorLog::getSize());

        foreach (ErrorLog::readChunks(1024 * 10) as $chunk) {
            echo $chunk;
        }

        die;
    }
}
