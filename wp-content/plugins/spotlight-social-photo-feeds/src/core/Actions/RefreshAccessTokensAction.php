<?php

namespace RebelCode\Spotlight\Instagram\Actions;

use Exception;
use RebelCode\Spotlight\Instagram\ErrorLog;
use RebelCode\Spotlight\Instagram\IgApi\IgAccount;
use RebelCode\Spotlight\Instagram\IgApi\IgApiClient;
use RebelCode\Spotlight\Instagram\IgApi\IgUser;
use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use WP_Post;

/**
 * The action that refreshes access tokens for all saved accounts.
 *
 * @since 0.3
 */
class RefreshAccessTokensAction
{
    /**
     * @since 0.3
     *
     * @var IgApiClient
     */
    protected $api;

    /**
     * @since 0.3
     *
     * @var PostType
     */
    protected $cpt;

    /**
     * Constructor.
     *
     * @since 0.3
     *
     * @param IgApiClient $api The Instagram API client.
     * @param PostType    $cpt The accounts post type.
     */
    public function __construct(IgApiClient $api, PostType $cpt)
    {
        $this->api = $api;
        $this->cpt = $cpt;
    }

    /**
     * @since 0.3
     */
    public function __invoke()
    {
        Arrays::each($this->cpt->query(), function (WP_Post $post) {
            $account = AccountPostType::fromWpPost($post);
            try {
                // Refresh the access token for personal accounts
                if ($account->user->type === IgUser::TYPE_PERSONAL) {
                    $accessToken = $this->api->getBasicApi()->refreshAccessToken($account->accessToken);
                    $account = new IgAccount($account->user, $accessToken);
                }

                // Save the account
                $this->cpt->update($post->ID, AccountPostType::toWpPost($account));
            } catch (Exception $exception) {
                ErrorLog::exception($exception);
                return;
            }
        });
    }
}
