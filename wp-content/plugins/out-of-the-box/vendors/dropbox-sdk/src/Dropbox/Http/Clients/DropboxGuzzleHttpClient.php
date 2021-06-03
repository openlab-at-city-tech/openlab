<?php

namespace TheLion\OutoftheBox\API\Dropbox\Http\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\RingException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TheLion\OutoftheBox\API\Dropbox\Exceptions\DropboxClientException;
use TheLion\OutoftheBox\API\Dropbox\Http\DropboxRawResponse;

/**
 * DropboxGuzzleHttpClient.
 */
class DropboxGuzzleHttpClient implements DropboxHttpClientInterface
{
    /**
     * GuzzleHttp client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Create a new DropboxGuzzleHttpClient instance.
     *
     * @param Client $client GuzzleHttp Client
     */
    public function __construct(Client $client = null)
    {
        //Set the client
        $this->client = $client ?: new Client(['verify' => OUTOFTHEBOX_ROOTDIR.'/vendors/dropbox-sdk/vendor/guzzlehttp/guzzle/src/Handler/cacerts.pem']);
    }

    /**
     * Send request to the server and fetch the raw response.
     *
     * @param string                          $url     URL/Endpoint to send the request to
     * @param string                          $method  Request Method
     * @param resource|StreamInterface|string $body    Request Body
     * @param array                           $headers Request Headers
     * @param array                           $options Additional Options
     * @param mixed                           $wait
     *
     * @throws \TheLion\OutoftheBox\API\Dropbox\Exceptions\DropboxClientException
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Http\DropboxRawResponse Raw response from the server
     */
    public function send($url, $method, $body, $headers = [], $options = [], $wait = 0)
    {
        //Create a new Request Object
        $request = new Request($method, $url, $headers, $body);

        // Wait if required
        usleep(1000000 * $wait);

        try {
            //Send the Request
            $rawResponse = $this->client->send($request, $options);
        } catch (RequestException $e) {
            $rawResponse = $e->getResponse();

            if ($e->getPrevious() instanceof RingException || !$rawResponse instanceof ResponseInterface) {
                throw new DropboxClientException($e->getMessage(), $e->getCode());
            }
        }

        // Hit request limit
        if (429 == $rawResponse->getStatusCode() && $wait < 30) {
            return $this->send($url, $method, $body, $headers, $options, $wait + 10);
        }

        //Something went wrong
        if ($rawResponse->getStatusCode() >= 400) {
            throw new DropboxClientException($rawResponse->getBody());
        }

        //Get the Response Body
        $body = $this->getResponseBody($rawResponse);

        $rawHeaders = $rawResponse->getHeaders();
        $httpStatusCode = $rawResponse->getStatusCode();

        //Create and return a DropboxRawResponse object
        return new DropboxRawResponse($rawHeaders, $body, $httpStatusCode);
    }

    /**
     * Get the Response Body.
     *
     * @param \Psr\Http\Message\ResponseInterface|string $response Response object
     *
     * @return string
     */
    protected function getResponseBody($response)
    {
        //Response must be string
        $body = $response;

        if ($response instanceof ResponseInterface) {
            //Fetch the body
            $body = $response->getBody();
        }

        if ($body instanceof StreamInterface) {
            $body = $body->getContents();
        }

        return $body;
    }
}
