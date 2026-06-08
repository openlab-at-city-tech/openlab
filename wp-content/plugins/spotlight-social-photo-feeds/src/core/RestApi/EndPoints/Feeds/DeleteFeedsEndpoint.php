<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Feeds;

use Dhii\Transformer\TransformerInterface;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\Wp\RestRequest;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The handler for the REST API endpoint that deletes feeds.
 *
 * @since 0.1
 */
class DeleteFeedsEndpoint extends AbstractEndpointHandler
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
     * @param PostType             $cpt
     * @param TransformerInterface $transformer
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
        if (!RestRequest::has_param($request, 'id')) {
            return new WP_Error('sli_missing_id', 'Missing feed ID in request');
        }

        $id = $request->get_param('id');
        $this->cpt->delete($id);

        return new WP_REST_Response(array_map([$this->transformer, 'transform'], $this->cpt->query()));
    }
}
