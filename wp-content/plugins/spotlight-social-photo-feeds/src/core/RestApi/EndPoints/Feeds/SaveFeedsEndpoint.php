<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Feeds;

use Dhii\Transformer\TransformerInterface;
use RebelCode\Spotlight\Instagram\Feeds\Feed;
use RebelCode\Spotlight\Instagram\PostTypes\FeedPostType;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\Wp\RestRequest;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The handler for the REST API endpoint that saves feeds.
 *
 * @since 0.1
 */
class SaveFeedsEndpoint extends AbstractEndpointHandler
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
        if (!RestRequest::has_param($request, 'feed')) {
            return new WP_Error('sli_missing_feed', 'Missing feed data in request');
        }

        $id = RestRequest::has_param($request, 'id')
            ? $request->get_param('id')
            : null;
        $feedData = $request->get_param('feed');

        // Remove usages
        unset($feedData['usages']);
        // Make sure the accounts and tagged do not contain duplicates
        $feedData['options'] = $feedData['options'] ?? [];
        $feedData['options']['accounts'] = array_unique($feedData['options']['accounts'] ?? []);
        $feedData['options']['tagged'] = array_unique($feedData['options']['tagged'] ?? []);

        $feed = Feed::fromArray($feedData);
        $post = FeedPostType::toWpPost($feed);

        $result = $this->cpt->update($id, $post);

        if ($result instanceof WP_Error) {
            return new WP_Error('sli_feed_save_error', $result->get_error_message(), ['status' => 500]);
        } else {
            $feed = new Feed($result, $feed->getName(), $feed->getOptions());
        }

        return new WP_REST_Response(['feed' => $this->transformer->transform($feed)]);
    }
}
