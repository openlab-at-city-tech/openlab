<?php

namespace RebelCode\Spotlight\Instagram\RestApi\Transformers;

use DateTime;
use Dhii\Transformer\TransformerInterface;
use RebelCode\Spotlight\Instagram\IgApi\IgComment;
use RebelCode\Spotlight\Instagram\IgApi\IgMedia;
use RebelCode\Spotlight\Instagram\MediaStore\IgCachedMedia;

/**
 * Transforms {@link IgMedia} instances into REST API response format.
 *
 * @since 0.1
 */
class MediaTransformer implements TransformerInterface
{
    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function transform($source)
    {
        if (!($source instanceof IgMedia)) {
            return $source;
        }

        $media = IgCachedMedia::from($source);

        $children = $media->children;
        foreach ($children as $idx => $child) {
            $children[$idx] = [
                'id' => $child->id,
                'type' => $child->type,
                'url' => $child->url,
                'permalink' => $child->permalink,
            ];
        }

        return [
            'id' => $media->id,
            'username' => $media->username,
            'caption' => $media->caption,
            'timestamp' => $media->timestamp ? $media->timestamp->format(DateTime::ISO8601) : null,
            'type' => $media->type,
            'url' => $media->url,
            'permalink' => $media->permalink,
            'thumbnail' => $media->thumbnail,
            'thumbnails' => $media->thumbnails,
            'likesCount' => $media->likesCount,
            'commentsCount' => $media->commentsCount,
            'comments' => array_map([$this, 'transformComment'], $media->comments),
            'children' => $children,
            'source' => $media->source->toArray(),
        ];
    }

    /**
     * Transforms a single media comment.
     *
     * @since 0.1
     *
     * @param IgComment $comment The comment instance.
     *
     * @return array The transformation result.
     */
    public function transformComment(IgComment $comment)
    {
        return [
            'id' => $comment->id,
            'username' => $comment->username,
            'text' => $comment->text,
            'timestamp' => $comment->timestamp ? $comment->timestamp->format(DateTime::ISO8601) : null,
            'likeCount' => $comment->likeCount,
        ];
    }
}
