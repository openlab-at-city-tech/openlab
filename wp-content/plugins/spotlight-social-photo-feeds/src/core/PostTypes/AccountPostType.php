<?php

namespace RebelCode\Spotlight\Instagram\PostTypes;

use WP_Post;
use WP_Error;
use RuntimeException;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use RebelCode\Spotlight\Instagram\IgApi\IgUser;
use RebelCode\Spotlight\Instagram\IgApi\IgAccount;
use RebelCode\Spotlight\Instagram\IgApi\AccessToken;
use RebelCode\Spotlight\Instagram\Engine\Data\Source\UserSource;
use RebelCode\Iris\Store;
use Exception;
use DateTime;

/**
 * The post type for accounts.
 *
 * This class extends the {@link PostType} class only as a formality. The primary purpose of this class is to house
 * the meta key constants and functionality for dealing with posts of the account custom post type.
 *
 * @since 0.1
 */
class AccountPostType extends PostType
{
    const USER_ID = '_sli_user_id';
    const USERNAME = '_sli_username';
    const NAME = '_sli_name';
    const BIO = '_sli_bio';
    const TYPE = '_sli_account_type';
    const MEDIA_COUNT = '_sli_media_count';
    const PROFILE_PIC_URL = '_sli_profile_pic_url';
    const FOLLOWERS_COUNT = '_sli_followers_count';
    const FOLLOWS_COUNT = '_sli_follows_count';
    const WEBSITE = '_sli_website';
    const CUSTOM_PROFILE_PIC = '_sli_custom_profile_pic';
    const CUSTOM_BIO = '_sli_custom_bio';
    const ACCESS_TOKEN = '_sli_access_token';
    const ACCESS_EXPIRY = '_sli_access_expires';
    const ACCESS_IV = '_sli_access_token_iv';
    // Access token encryption algorithm
    const ENCRYPT_ALGO = 'AES-256-CBC';
    // Custom account-specific posts
    const CUSTOM_MEDIA = '_sli_custom_media';

    /**
     * @var Store 
     */
    protected $store;

    /**
     * Constructor 
     */
    public function __construct(string $slug, array $args, array $fields, Store $store)
    {
        parent::__construct($slug, $args, $fields);
        $this->store = $store;
    }

    /**
     * @inheritDoc 
     */
    public function delete($id)
    {
        // Make sure the account exists
        $post = $this->get($id);
        if ($post === null) {
            return false;
        }

        // Get the source for the account's user
        $account = static::fromWpPost($post);
        $source = UserSource::create($account->user->username, $account->user->type);

        // Delete the account
        $result = parent::delete($id);

        // If successful, delete the associated media
        if ($result) {
            $this->store->deleteForSources([$source]);
        }

        return $result;
    }

    /**
     * Converts a WordPress post into an IG account instance.
     *
     * @since 0.1
     *
     * @param WP_Post $post
     *
     * @return IgAccount
     */
    public static function fromWpPost(WP_Post $post) : IgAccount
    {
        $accessToken = new AccessToken(
            static::decryptAccessToken($post->{static::ACCESS_TOKEN}, $post->{static::ACCESS_IV}),
            (int) $post->{static::ACCESS_EXPIRY}
        );

        $user = IgUser::create(
            [
            'id' => $post->{static::USER_ID},
            'username' => $post->{static::USERNAME},
            'name' => $post->{static::NAME},
            'biography' => $post->{static::BIO},
            'account_type' => $post->{static::TYPE},
            'media_count' => $post->{static::MEDIA_COUNT},
            'profile_picture_url' => $post->{static::PROFILE_PIC_URL},
            'followers_count' => $post->{static::FOLLOWERS_COUNT},
            'follows_count' => $post->{static::FOLLOWS_COUNT},
            'website' => $post->{static::WEBSITE},
            ]
        );

        return new IgAccount($user, $accessToken);
    }

    /**
     * Converts an IG account instance into a WordPress post.
     *
     * @since 0.1
     *
     * @param IgAccount $account
     *
     * @return array
     */
    public static function toWpPost(IgAccount $account) : array
    {
        $user = $account->user;
        $accessToken = $account->accessToken;
        $iv = static::generateEncryptionIv();

        return [
            'post_title' => $user->username,
            'post_status' => 'publish',
            'meta_input' => [
                static::USER_ID => $user->id,
                static::USERNAME => $user->username,
                static::NAME => $user->name,
                static::BIO => $user->bio,
                static::TYPE => $user->type,
                static::MEDIA_COUNT => $user->mediaCount,
                static::PROFILE_PIC_URL => $user->profilePicUrl,
                static::FOLLOWERS_COUNT => $user->followersCount,
                static::FOLLOWS_COUNT => $user->followsCount,
                static::WEBSITE => $user->website,
                static::ACCESS_EXPIRY => $accessToken->expires,
                static::ACCESS_TOKEN => static::encryptAccessToken($accessToken->code, $iv),
                static::ACCESS_IV => $iv,
            ],
        ];
    }

    /**
     * Converts a WordPress post into a post array for an account.
     *
     * @since 0.1
     *
     * @param WP_Post $post The post.
     *
     * @return array The post array.
     */
    public static function fromWpPostToArray(WP_Post $post) : array
    {
        $array = static::toWpPost(static::fromWpPost($post));

        $array['meta_input'][static::CUSTOM_PROFILE_PIC] = $post->{static::CUSTOM_PROFILE_PIC};
        $array['meta_input'][static::CUSTOM_BIO] = $post->{static::CUSTOM_BIO};

        return $array;
    }

    /**
     * Finds and retrieves a business account.
     *
     * @since 0.1
     *
     * @param PostType $cpt The post type instance.
     *
     * @return IgAccount|null The found business account, or null if none could be found.
     */
    public static function findBusinessAccount(PostType $cpt)
    {
        $accounts = $cpt->query(
            [
            'meta_query' => [
                [
                    'key' => AccountPostType::TYPE,
                    'value' => IgUser::TYPE_BUSINESS,
                ],
            ],
            ]
        );

        return count($accounts) > 0
            ? AccountPostType::fromWpPost($accounts[0])
            : null;
    }

    /**
     * Finds an account with a specific username.
     *
     * @since 0.5
     *
     * @param PostType $cpt      The post type instance.
     * @param string   $username The username of the account to search for.
     *
     * @return IgAccount|null The found account with the given username or null if no matching account was found.
     */
    public static function getByUsername(PostType $cpt, string $username)
    {
        $posts = $cpt->query(
            [
            'meta_query' => [
                [
                    'key' => static::USERNAME,
                    'value' => $username,
                ],
            ],
            ]
        );

        return count($posts) > 0
            ? AccountPostType::fromWpPost($posts[0])
            : null;
    }

    /**
     * Inserts an account into the database, or updates an existing account if it already exists in the database.
     *
     * @since 0.1
     *
     * @param PostType  $cpt     The post type.
     * @param IgAccount $account The account instance.
     *
     * @return int The inserted ID.
     *
     * @throws RuntimeException If the insertion or update failed.
     */
    public static function insertOrUpdate(PostType $cpt, IgAccount $account)
    {
        $existing = $cpt->query(
            [
            'meta_query' => [
                [
                    'key' => static::USER_ID,
                    'value' => $account->user->id,
                ],
            ],
            ]
        );

        $data = static::toWpPost($account);

        $result = (count($existing) > 0)
            ? $cpt->update($existing[0]->ID, $data)
            : $cpt->insert($data);

        if ($result instanceof WP_Error) {
            throw new RuntimeException($result->get_error_message());
        }

        return $result;
    }

    /**
     * Retrieves the access token for an account.
     *
     * @param int $accountId The ID of the account.
     *
     * @return string The (unencrypted) access token string.
     */
    public static function getAccessToken(int $accountId): string
    {
        $iv = get_post_meta($accountId, static::ACCESS_IV, true);
        $encrypted = get_post_meta($accountId, static::ACCESS_TOKEN, true);

        // If the IV is empty, the access token is probably not encrypted. So we just return it.
        return empty($iv)
            ? $encrypted
            : static::decryptAccessToken($encrypted, $iv);
    }

    /**
     * Encrypts an access token.
     *
     * @param string $accessToken The unencrypted access token.
     * @param string $initVector  The initialization vector for the cryptographic algorithm.
     *
     * @return string The encrypted access token string.
     */
    public static function encryptAccessToken(string $accessToken, string $initVector): string
    {
        if (!extension_loaded('openssl')) {
            return $accessToken;
        }

        return openssl_encrypt($accessToken, static::ENCRYPT_ALGO, wp_salt(), 0, $initVector);
    }

    /**
     * Encrypts an access token.
     *
     * @param string $encryptedOrRaw The encrypted access token, or the unencrypted access token.
     * @param string $initVector     The initialization vector for the cryptographic algorithm.
     *
     * @return string The unencrypted access token string. If the first argument is already unencrypted, it's value
     *                will be returned without any further decrypting.
     */
    public static function decryptAccessToken(string $encryptedOrRaw, string $initVector): string
    {
        if (!extension_loaded('openssl')) {
            return $encryptedOrRaw;
        }

        $decrypted = openssl_decrypt($encryptedOrRaw, static::ENCRYPT_ALGO, wp_salt(), 0, $initVector);

        if (!is_string($decrypted)) {
            return $encryptedOrRaw;
        }

        $prefix = strtoupper(substr($decrypted, 0, 2));
        if ($prefix === 'EA' || $prefix === 'IG') {
            return $decrypted;
        }

        return $encryptedOrRaw;
    }

    /**
     * Generates a random initialization vector, for use when encrypting an access token.
     *
     * @return string The generated initialization vector, as a string of digits.
     */
    public static function generateEncryptionIv(): string
    {
        if (extension_loaded('openssl')) {
            $length = openssl_cipher_iv_length(static::ENCRYPT_ALGO);
        } else {
            $length = 16;
        }

        $iv = '';
        for ($i = 0; $i < $length; ++$i) {
            try {
                $iv .= random_int(0, 9);
            } catch (Exception $e) {
                $iv .= '0';
            }
        }

        return $iv;
    }

    /**
     * Finds and retrieves a personal account.
     *
     * @since 1.6.13
     *
     * @param PostType $cpt The post type instance.
     *
     * @return IgAccount|false The found personal account, or false if none could be found.
     */
    public static function findPersonalAccount(PostType $cpt)
    {
        $accounts = $cpt->query(
            [
            'meta_query' => [
                [
                    'key' => AccountPostType::TYPE,
                    'value' => IgUser::TYPE_PERSONAL,
                ],
            ],
            ]
        );

        $comparisonDate = new DateTime("2024-12-04T00:00:00Z");

        foreach ($accounts as $k => $post) {
            try {
                $postDate = new DateTime($post->post_date_gmt ?? $post->post_date);
                if ($postDate >= $comparisonDate) {
                    unset($accounts[$k]);
                }
            } catch (Exception $e) {
                unset($accounts[$k]);
            }
        }

        $accounts = array_values($accounts);

        return count($accounts) > 0
            ? AccountPostType::fromWpPost($accounts[0])
            : false;
    }
}
