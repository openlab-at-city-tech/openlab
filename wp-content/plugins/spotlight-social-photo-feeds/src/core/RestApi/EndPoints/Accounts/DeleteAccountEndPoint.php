<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Accounts;

use Dhii\Transformer\TransformerInterface;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\Wp\RestRequest;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Endpoint handler for deleting accounts.
 *
 * @since 0.1
 */
class DeleteAccountEndPoint extends AbstractEndpointHandler
{
    /**
     * @since 0.1
     *
     * @var PostType
     */
    protected $accountsCpt;

    /**
     * @since 0.1
     *
     * @var PostType
     */
    protected $mediaCpt;

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
     * @param PostType             $accountsCpt The accounts post type.
     * @param PostType             $mediaCpt    The media post type.
     * @param TransformerInterface $transformer
     */
    public function __construct(PostType $accountsCpt, PostType $mediaCpt, TransformerInterface $transformer)
    {
        $this->accountsCpt = $accountsCpt;
        $this->mediaCpt = $mediaCpt;
        $this->transformer = $transformer;
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    protected function handle(WP_REST_Request $request)
    {
        if (!RestRequest::has_param($request, 'id')) {
            return new WP_Error('sli_missing_id', 'Missing account ID in request');
        }

        $id = $request->get_param('id');

        $result = $this->accountsCpt->delete($id);

        if (!$result) {
            return new WP_Error('sli_account_delete_failed', 'Failed to delete the account', [
                'status' => 500,
            ]);
        }

        return new WP_REST_Response(array_map([$this->transformer, 'transform'], $this->accountsCpt->query()));
    }
}
