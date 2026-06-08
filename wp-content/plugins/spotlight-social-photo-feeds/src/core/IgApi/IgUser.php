<?php

namespace RebelCode\Spotlight\Instagram\IgApi;

/**
 * Represents an Instagram user as retrieved from the Graph API.
 *
 * @since 0.1
 */
class IgUser
{
    const TYPE_PERSONAL = 'PERSONAL';
    const TYPE_BUSINESS = 'BUSINESS';

    /**
     * @since 0.1
     *
     * @var string
     */
    public $id;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $username;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $type;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $name;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $bio;

    /**
     * @since 0.1
     *
     * @var int
     */
    public $mediaCount;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $profilePicUrl;

    /**
     * @since 0.1
     *
     * @var int
     */
    public $followersCount;

    /**
     * @since 0.1
     *
     * @var int
     */
    public $followsCount;

    /**
     * @since 0.1
     *
     * @var string
     */
    public $website;

    /**
     * @since 0.1
     *
     * @param array $data
     *
     * @return IgUser
     */
    public static function create(array $data)
    {
        $data = array_merge([
            'id' => '',
            'username' => '',
            'name' => '',
            'biography' => '',
            'account_type' => '',
            'media_count' => 0,
            'profile_picture_url' => '',
            'followers_count' => 0,
            'follows_count' => 0,
            'website' => '',
        ], $data);

        $user = new static();
        $user->id = $data['id'];
        $user->username = $data['username'];
        $user->name = $data['name'];
        $user->bio = $data['biography'];
        $user->type = $data['account_type'];
        $user->mediaCount = $data['media_count'];
        $user->profilePicUrl = $data['profile_picture_url'];
        $user->followersCount = $data['followers_count'];
        $user->followsCount = $data['follows_count'];
        $user->website = $data['website'];

        return $user;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getBio() : string
    {
        return $this->bio;
    }

    /**
     * @since 0.1
     *
     * @return int
     */
    public function getMediaCount() : int
    {
        return $this->mediaCount;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getProfilePicUrl() : string
    {
        return $this->profilePicUrl;
    }

    /**
     * @since 0.1
     *
     * @return int
     */
    public function getFollowersCount() : int
    {
        return $this->followersCount;
    }

    /**
     * @since 0.1
     *
     * @return int
     */
    public function getFollowsCount() : int
    {
        return $this->followsCount;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getWebsite() : string
    {
        return $this->website;
    }
}
