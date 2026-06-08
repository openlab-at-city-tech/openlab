<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Feeds;

use WP_REST_Response;
use WP_REST_Request;
use WP_Error;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use Dhii\Transformer\TransformerInterface;

/**
 * The handler for the REST API endpoint that exposes feeds.
 *
 * @since 0.1
 */
class GetFeedsEndpoint extends AbstractEndpointHandler
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
        $id = isset($request['id']) ? sanitize_text_field($request['id']) : '';

        if (empty($id)) {
            return new WP_REST_Response(array_map([$this->transformer, 'transform'], $this->cpt->query([
                'order' => 'ASC',
                'order_by' => 'ID'
            ])));
        }

        $feed = $this->cpt->get($id);

        if ($feed === null) {
            return new WP_Error('not_found', "FeedCard \"{$id}\" was not found", ['status' => 404]);
        }

        return new WP_REST_Response($this->transformer->transform($feed));
    }
}
