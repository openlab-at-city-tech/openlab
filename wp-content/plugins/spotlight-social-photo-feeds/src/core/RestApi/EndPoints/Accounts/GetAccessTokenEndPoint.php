<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts;

use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use WP_Post;
use WP_REST_Request;
use WP_REST_Response;
use WP_User;

/**
 * The handler for the endpoint that provides account access tokens.
 *
 * @since 0.1
 */
class GetAccessTokenEndPoint extends AbstractEndpointHandler
{
    /**
     * @since 0.1
     *
     * @var PostType
     */
    protected $cpt;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param PostType $cpt The account post type.
     */
    public function __construct(PostType $cpt)
    {
        $this->cpt = $cpt;
    }

    protected function handle(WP_REST_Request $request)
    {
        $pass = $request->get_param('pass');
        $accountId = $request->get_param('id');

        $wpUser = wp_get_current_user();
        if (!($wpUser instanceof WP_User)) {
            return new WP_REST_Response([
                'error' => 'not_logged_in',
                'message' => __('You must be logged in to view this access token', 'sl-insta'),
            ], 400);
        }

        if (!wp_check_password($pass, $wpUser->user_pass, $wpUser->ID)) {
            return new WP_REST_Response([
                'error' => 'incorrect_password',
                'message' => __('The password you entered is incorrect', 'sl-insta'),
            ], 400);
        }

        $accountPost = $this->cpt->get($accountId);
        if (!($accountPost instanceof WP_Post)) {
            return new WP_REST_Response([
                'error' => 'invalid_account_id',
                'message' => __('The Instagram account does not exist', 'sl-insta'),
            ], 404);
        }

        return new WP_REST_Response(['accessToken' => AccountPostType::getAccessToken($accountPost->ID)]);
    }
}
