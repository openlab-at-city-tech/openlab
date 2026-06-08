<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Promotion;

use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use WP_Error;
use WP_Post;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The endpoint for searching for WordPress posts, used by the "Promote" feature.
 *
 * @since 0.3
 */
class SearchPostsEndpoint extends AbstractEndpointHandler
{
    /**
     * @inheritDoc
     *
     * @since 0.3
     */
    protected function handle(WP_REST_Request $request)
    {
        if (!$request->has_param('type')) {
            return new WP_Error('sli_missing_post_type', __('Missing post type in request', 'sl-insta'), [
                'status' => 400,
            ]);
        }

        $postType = $request->get_param('type');
        $search = $request->has_param('search')
            ? $request->get_param('search')
            : '';

        if (empty($search)) {
            $query = [
                'post_type' => $postType,
                'posts_per_page' => 5,
                'post_status' => ['publish', 'draft'],
                'orderby' => 'date',
                'order' => 'DESC',
            ];
        } else {
            $query = [
                'post_type' => $postType,
                'posts_per_page' => -1,
                'post_status' => ['publish', 'draft'],
                's' => $search,
            ];
        }

        $posts = get_posts($query);

        return new WP_REST_Response(Arrays::map($posts, function (WP_Post $post) {
            return [
                'id' => $post->ID,
                'title' => $post->post_title,
                'permalink' => get_post_permalink($post->ID),
                'niceUrl' => get_permalink($post->ID),
            ];
        }));
    }
}
