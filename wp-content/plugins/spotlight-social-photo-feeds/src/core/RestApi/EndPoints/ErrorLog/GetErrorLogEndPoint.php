<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\ErrorLog;

use WP_REST_Response;
use WP_REST_Request;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\ErrorLog;

class GetErrorLogEndPoint extends AbstractEndpointHandler
{
    protected function handle(WP_REST_Request $request): WP_REST_Response
    {
        $limit = $request->has_param('limit')
            ? (int) $request->get_param('limit')
            : 50;

        $offset = $request->has_param('offset')
            ? (int) $request->get_param('offset')
            : 0;

        $entries = [];
        $reachedLimit = false;
        foreach (ErrorLog::readEntries() as $i => $entry) {
            if ($i < $offset) {
                continue;
            }

            if (count($entries) >= $limit) {
                $reachedLimit = true;
                break;
            } else {
                $entries[] = $entry;
            }
        }

        return new WP_REST_Response([
            'entries' => $entries,
            'fileSize' => ErrorLog::getSize(),
            'lastModified' => ErrorLog::getLastModified(),
            'path' => ErrorLog::getDebugLogPath(),
            'isEnd' => !$reachedLimit,
        ]);
    }
}
