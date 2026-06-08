<?php

namespace RebelCode\Spotlight\Instagram\RestApi\Transformers;

use WP_Post;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\PostTypes\FeedPostType;
use RebelCode\Spotlight\Instagram\PostTypes\AccountPostType;
use RebelCode\Spotlight\Instagram\IgApi\IgAccount;
use Dhii\Transformer\TransformerInterface;

/**
 * Transforms {@link IgAccount} instances into REST API response format.
 *
 * @since 0.1
 */
class AccountTransformer implements TransformerInterface
{
    /**
     * @since 0.1
     *
     * @var PostType
     */
    protected $feedsCpt;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param PostType $feedsCpt The feeds post type.
     */
    public function __construct(PostType $feedsCpt)
    {
        $this->feedsCpt = $feedsCpt;
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function transform($source)
    {
        if (!($source instanceof WP_Post)) {
            return $source;
        }

        return static::toArray($source, $this->feedsCpt);
    }

    /**
     * Transforms an account post into an array.
     *
     * @since 0.4
     *
     * @param WP_Post       $post  The account post.
     * @param PostType|null $feeds Optional feeds CPT to calculate usages.
     *
     * @return array
     */
    public static function toArray(WP_Post $post, ?PostType $feeds = null)
    {
        $user = AccountPostType::fromWpPost($post)->user;

        $usages = [];
        if ($feeds !== null) {
            foreach ($feeds->query() as $feedPost) {
                $options = $feedPost->{FeedPostType::OPTIONS};

                $usedAccounts = $options['accounts'] ?? [];
                $usedTagged = $options['tagged'] ?? [];

                $used = array_search($post->ID, $usedAccounts) !== false ||
                        array_search($post->ID, $usedTagged) !== false;

                if ($used) {
                    $usages[] = $feedPost->ID;
                }
            }
        }

        return [
            'id' => $post->ID,
            'type' => $user->type,
            'userId' => $user->id,
            'username' => $user->username,
            'bio' => $user->bio,
            'customBio' => $post->{AccountPostType::CUSTOM_BIO},
            'profilePicUrl' => $user->profilePicUrl,
            'customProfilePicUrl' => $post->{AccountPostType::CUSTOM_PROFILE_PIC},
            'mediaCount' => $user->mediaCount,
            'followersCount' => $user->followersCount,
            'usages' => $usages,
            'creationDate' => $post->post_date_gmt ?? $post->post_date
        ];
    }
}
