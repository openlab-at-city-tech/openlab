<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts;

use WP_REST_Response;
use WP_REST_Request;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Spotlight\Instagram\IgApi\IgApiClient;
use RebelCode\Spotlight\Instagram\IgApi\IgAccount;
use RebelCode\Spotlight\Instagram\IgApi\AccessToken;
use Exception;

class ConnectAccountEndPoint extends AbstractEndpointHandler
{
    /**
     * @since 0.1
     *
     * @var IgApiClient
     */
    protected $client;

    /**
     * @since 0.1
     *
     * @var callable
     */
    protected $cpt;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param IgApiClient $client The API client.
     * @param PostType    $cpt    The accounts post type.
     */
    public function __construct(IgApiClient $client, PostType $cpt)
    {
        $this->client = $client;
        $this->cpt = $cpt;
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     *
     * @throws Exception
     */
    protected function handle(WP_REST_Request $request)
    {
        // Get the access token code from the request
        $tokenCode = isset($request['accessToken']) ? sanitize_text_field($request['accessToken']) : '';
        if (empty($tokenCode)) {
            return new WP_REST_Response(['error' => "The access token is required."], 400);
        }

        $isBusinessToken = stripos($tokenCode, 'EA') === 0 && strlen($tokenCode) > 145;

        $userId = isset($request['userId']) ? sanitize_text_field($request['userId']): '';

        try {
            // Construct the access token object
            $accessToken = new AccessToken($tokenCode, 0);

            // Attempt to connect via a filter first
            $account = apply_filters('spotlight/instagram/api/connect_access_token', null, $accessToken, $userId);

            // Connect via Instagram
            if ($account === null) {
                // FOR BUSINESS ACCOUNT ACCESS TOKENS
                if ($isBusinessToken) {
                    if (empty($userId)) {
                        return new WP_REST_Response(['error' => 'The user ID is required for business accounts.'], 400);
                    }

                    $account = $this->client->getGraphApi()->getAccountForUser($userId, $accessToken);
                } else {
                    // FOR PERSONAL ACCOUNT ACCESS TOKENS
                    $user = $this->client->getBasicApi()->getTokenUser($accessToken);
                    $account = new IgAccount($user, $accessToken);
                }
            }

            // Insert the account into the database (or update existing account)
            $accountId = AccountPostType::insertOrUpdate($this->cpt, $account);

            return new WP_REST_Response([
                'success' => true,
                'accountId' => $accountId,
            ]);
        } catch (Exception $e) {
            $exMsg = $e->getMessage();
            $didMatch = preg_match('/Error\s+#([0-9]+)\s+\[/', $exMsg, $matches) === 1;
            $matches = ($matches ?? []) + [0 => '', 1 => ''];

            if ($didMatch &&
                (
                    ($matches[1] ?? '') === '190' ||
                    ($matches[1] ?? '') === '100' ||
                    ($matches[1] ?? '') === '12' ||
                    ($matches[1] ?? '') === '10' ||
                    ($matches[1] ?? '') === '0'
                )
            ) {
                $message = $isBusinessToken
                    ? 'Failed to connect your account. Please make sure that your access token and user ID are correct.'
                    : 'Failed to connect your account. Please make sure that the access token is correct.';

                return new WP_REST_Response(['error' => $message], 400);
            } else {
                throw $e;
            }
        }
    }
}
