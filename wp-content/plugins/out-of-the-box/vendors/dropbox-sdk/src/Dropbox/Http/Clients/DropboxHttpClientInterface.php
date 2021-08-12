<?php
namespace TheLion\OutoftheBox\API\Dropbox\Http\Clients;

/**
 * DropboxHttpClientInterface
 */
interface DropboxHttpClientInterface
{
    /**
     * Send request to the server and fetch the raw response
     *
     * @param  string $url     URL/Endpoint to send the request to
     * @param  string $method  Request Method
     * @param  string|resource|StreamInterface $body Request Body
     * @param  array  $headers Request Headers
     * @param  array  $options Additional Options
     *
     * @return \TheLion\OutoftheBox\API\Dropbox\Http\DropboxRawResponse Raw response from the server
     *
     * @throws \TheLion\OutoftheBox\API\Dropbox\Exceptions\DropboxClientException
     */
    public function send($url, $method, $body, $headers = [], $options = []);
}
