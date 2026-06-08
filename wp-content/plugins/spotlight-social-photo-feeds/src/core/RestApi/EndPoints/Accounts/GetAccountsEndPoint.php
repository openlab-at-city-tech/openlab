<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts;

use WP_REST_Response;
use WP_REST_Request;
use WP_Post;
use WP_Error;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\PostTypes\CustomMedia;
use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use Dhii\Transformer\TransformerInterface;

/**
 * The handler for the endpoint that provides account information.
 *
 * @since 0.1
 */
class GetAccountsEndPoint extends AbstractEndpointHandler
{
    /**
     * @since 0.1
     *
     * @var PostType
     */
    protected $cpt;

    /**
     * @since 0.1
     *
     * @var TransformerInterface
     */
    protected $transformer;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param PostType             $cpt         The account post type.
     * @param TransformerInterface $transformer The transformer to use for formatting accounts into responses.
     */
    public function __construct(PostType $cpt, TransformerInterface $transformer)
    {
        $this->cpt = $cpt;
        $this->transformer = $transformer;
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    protected function handle(WP_REST_Request $request)
    {
        $id = isset($request['id']) ? sanitize_text_field($request['id']) : '';
        if (empty($id)) {
            $data = Arrays::map($this->cpt->query(), function ($account) use ($request) {
                return $this->transformAccount($account);
            });

            return new WP_REST_Response($data);
        }

        $account = $this->cpt->get($id);

        if ($account === null) {
            return new WP_Error('not_found', "Account \"{$id}\" was not found", ['status' => 404]);
        }

        return new WP_REST_Response($this->transformAccount($account));
    }

    /**
     * Transforms an account into response format, including the access token if the request is authorized accordingly.
     *
     * @since 0.1
     *
     * @param WP_Post         $account The account to transform.
     *
     * @return array The account data for the response.
     */
    protected function transformAccount(WP_Post $account)
    {
        $data = $this->transformer->transform($account);

        // If the request came from a logged in user, access token expiry date
        if (is_user_logged_in()) {
            $data['accessExpiry'] = (int) $account->{AccountPostType::ACCESS_EXPIRY};
            $data['numCustomMedia'] = count(CustomMedia::getForAccount($account->ID));
        }

        return $data;
    }
}
