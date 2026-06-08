<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts;

use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\Wp\RestRequest;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The handler for the REST API endpoint that updates account information.
 *
 * @since 0.1
 */
class UpdateAccountEndPoint extends AbstractEndpointHandler
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
     * @param PostType $cpt The accounts post type.
     */
    public function __construct(PostType $cpt)
    {
        $this->cpt = $cpt;
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    protected function handle(WP_REST_Request $request)
    {
        if (!RestRequest::has_param($request, 'id')) {
            return new WP_Error('sli_missing_id', 'Missing account ID in request', [
                'status' => 400,
            ]);
        }

        $id = $request->get_param('id');
        $post = $this->cpt->get($id);

        if ($post === null) {
            return new WP_Error('sli_missing_id', 'Missing account ID in request', [
                'status' => 404,
            ]);
        }

        $array = AccountPostType::fromWpPostToArray($post);

        if (RestRequest::has_param($request, 'customProfilePicUrl')) {
            $array['meta_input'][AccountPostType::CUSTOM_PROFILE_PIC] = $request->get_param('customProfilePicUrl');
        }

        if (RestRequest::has_param($request, 'customBio')) {
            $array['meta_input'][AccountPostType::CUSTOM_BIO] = $request->get_param('customBio');
        }

        $this->cpt->update($id, $array);

        return new WP_REST_Response([], 200);
    }
}
