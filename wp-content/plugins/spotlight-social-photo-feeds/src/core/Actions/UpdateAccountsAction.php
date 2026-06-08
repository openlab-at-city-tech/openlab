<?php

namespace RebelCode\Spotlight\Instagram\Actions;

use Exception;
use RebelCode\Spotlight\Instagram\ErrorLog;
use RebelCode\Spotlight\Instagram\IgApi\IgApiClient;
use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use WP_Post;

/**
 * The action that refreshes account information for all saved accounts.
 *
 * @since 0.3
 */
class UpdateAccountsAction
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
                // Fetch the account info
                $newAccount = $this->api->getAccountInfo($account);

                // Convert to a WP post data array
                $accountPost = AccountPostType::toWpPost($newAccount);

                // Save the account info
                $this->cpt->update($post->ID, $accountPost);
            } catch (Exception $exception) {
                ErrorLog::exception($exception);
                return;
            }
        });
    }
}
