<?php

namespace RebelCode\Spotlight\Instagram\Actions;

use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;

class OEmbedHandler
{
    /** @var string */
    protected $baseUrl;

    /** @var ClientInterface */
    protected $client;

    /** @var PostType */
    protected $accounts;

    /** Constructor. */
    public function __construct(string $baseUrl, ClientInterface $client, PostType $accounts)
    {
        $this->client = $client;
        $this->accounts = $accounts;
        $this->baseUrl = $baseUrl;
    }

    /** Retrieves the oEmbed HTML */
    public function __invoke($matches, $attr, $url, $other): string
    {
        return '<div class="sli-oembed">' . $this->fetchHtml($url) . '</div>';
    }

    /** Fetches the HTML for the embed from Instagram's API. */
    protected function fetchHtml(string $url): string
    {
        $account = AccountPostType::findBusinessAccount($this->accounts);

        // User must have a business account connected
        if ($account === null) {
            if (is_user_logged_in() && current_user_can('edit_posts')) {
                $message = sprintf(
                    'An %1$s is required to embed Instagram posts. This requires a %2$s or %3$s account connected to a Facebook Page. %4$s (This message is only visible to logged-in users.)',
                    '<b>Advanced connection</b>',
                    '<a href="https://docs.spotlightwp.com/article/886-what-is-an-instagram-creator-account" target="_blank">Creator</a>',
                    '<a href="https://docs.spotlightwp.com/article/885-what-is-an-instagram-business-account" target="_blank">Business</a>',
                    '<a href="https://docs.spotlightwp.com/article/905-what-is-an-advanced-account" target="_blank">Learn more →</a>'
                );

                return "<p><b>Spotlight</b>: $message</p>";
            } else {
                return '';
            }
        }

        // Remove GET params from IG post URL
        if (strpos($url, '?') !== false) {
            $parts = explode('?', $url);
            if (isset($parts[1])) {
                $url = str_replace('?' . $parts[1], '', $url);
            }
        }

        $fullUrl = $this->baseUrl . '?url=' . urlencode($url) . '&access_token=' . $account->accessToken->code;

        try {
            $response = $this->client->sendRequest(new Request('GET', $fullUrl));
            $body = json_decode($response->getBody()->getContents());

            return $body->html;
        } catch (ClientExceptionInterface $e) {
            return '<p>' . esc_html($e->getMessage()) . '</p>';
        }
    }
}
