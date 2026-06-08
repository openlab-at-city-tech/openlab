<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Analytics;

use Psr\Http\Client\ClientInterface;
use RebelCode\Psr7\Request;
use RebelCode\Spotlight\Instagram\Config\ConfigEntry;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use Throwable;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class SubmitSurveyEndPoint extends AbstractEndpointHandler
{
    const URL = "https://hooks.zapier.com/hooks/catch/305784/bi9c1uh/";

    /** @var ClientInterface */
    protected $client;

    /** @var ConfigEntry */
    protected $didComplete;

    /**
     * Constructor.
     */
    public function __construct(ClientInterface $client, ConfigEntry $didComplete)
    {
        $this->client = $client;
        $this->didComplete = $didComplete;
    }

    /** @inheritDoc */
    protected function handle(WP_REST_Request $request)
    {
        $this->didComplete->setValue(true);

        $body = $request->get_body() ?? '';

        $request = new Request('POST', static::URL, ['Content-type' => 'application/json'], $body);

        try {
            $response = $this->client->sendRequest($request);
            $status = $response->getStatusCode();

            if ($status === 200) {
                return new WP_REST_Response(['success' => true]);
            } else {
                return new WP_Error('sli_analytics_survey_failed', "Received status code $status from Zapier", [
                    'status' => 500
                ]);
            }
        } catch (Throwable $e) {
            return new WP_Error('sli_analytics_survey_failed', $e->getMessage(), ['status' => 500]);
        }
    }
}
