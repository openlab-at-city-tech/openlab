<?php

namespace RebelCode\Spotlight\Instagram\RestApi\EndPoints\Embed;

use WP_REST_Response;
use WP_REST_Request;
use WP_Error;
use RebelCode\Spotlight\Instagram\RestApi\EndPoints\AbstractEndpointHandler;

class CreatePostEndPoint extends AbstractEndpointHandler
{
    /** @inerhitDoc */
    protected function handle(WP_REST_Request $request)
    {
        $feedId = $request->get_param('feedId');
        $feedId = filter_var($feedId, FILTER_VALIDATE_INT);

        if (!is_int($feedId) || $feedId < 1) {
            return new WP_Error('sli_invalid_feed_id', __('Invalid feed ID', 'sli'), ['status' => 400]);
        }

        $useCase = $request->get_param('useCase');
        $useCase = sanitize_text_field($useCase);

        $postType = $request->get_param('postType');
        $postType = sanitize_text_field($postType);

        $args = [
            'post_type' => $postType,
            'post_title' => $this->getPostTitle($useCase),
            'post_content' => $this->getFeedEmbedCode($feedId, $postType, $useCase),
        ];

        $id = wp_insert_post($args);

        if (is_wp_error($id)) {
            return new WP_Error('sli_create_post_failed', $id->get_error_message(), ['status' => 500]);
        }

        return new WP_Rest_Response(['success' => true, 'postId' => $id, 'postTitle' => $args['post_title']]);
    }

    protected function getPostArgs(string $feedId, string $postType, string $useCase): array
    {
        return [
            'post_type' => $postType,
            'post_title' => $this->getPostTitle($useCase),
            'post_content' => $this->getFeedEmbedCode($feedId, $postType, $useCase),
        ];
    }

    protected function getPostTitle(string $useCase): string
    {
        switch ($useCase) {
            case 'linkInBio':
                return 'Link In Bio';
            case 'shoppable':
                return 'Shop My Instagram';
            default:
                return 'My Instagram Feed';
        }
    }

    protected function getFeedEmbedCode(string $feedId, string $postType, string $useCase): string
    {
        if (!function_exists('use_block_editor_for_post_type')) {
            require ABSPATH . 'wp-admin/includes/post.php';
        }

        if (use_block_editor_for_post_type($postType)) {
            $code = '<!-- wp:spotlight/instagram {"feedId":' . $feedId . '} /-->';

            if ($useCase === "linkInBio") {
                $code = $this->getLinkInBioButton('Link 1') .
                        $this->getLinkInBioButton('Link 2') .
                        $this->getLinkInBioButton('Link 3') .
                        $code;
            }

            return $code;
        } else {
            return '[instagram feed="' . $feedId . '"]';
        }
    }

    protected function getLinkInBioButton(string $text): string
    {
        return <<<BLOCK
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} -->
            <div class="wp-block-buttons">
                <!-- wp:button -->
                <div class="wp-block-button">
                    <a class="wp-block-button__link">$text</a>
                </div>
                <!-- /wp:button -->
            </div>
        <!-- /wp:buttons -->
BLOCK;
    }
}
