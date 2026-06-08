<?php

namespace RebelCode\Spotlight\Instagram\MediaStore;

use RebelCode\Spotlight\Instagram\IgApi\IgUser;

/**
 * A media source is a simple struct-like object that records information about from where, or why, a specific media
 * object was fetched.
 *
 * @since 0.1
 */
class MediaSource
{
    const PERSONAL_ACCOUNT = 'PERSONAL_ACCOUNT';
    const BUSINESS_ACCOUNT = 'BUSINESS_ACCOUNT';

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
    public $type;

    /**
     * @since 0.1
     *
     * @param array|MediaSource $data
     *
     * @return static
     */
    public static function create($data)
    {
        if ($data instanceof static) {
            return $data;
        }

        $instance = new static();
        $instance->name = $data['name'] ?? '';
        $instance->type = $data['type'] ?? '';

        return $instance;
    }

    /**
     * Creates a media source for a user.
     *
     * @since 0.1
     *
     * @param IgUser $user The user instance.
     *
     * @return static The created media source instance.
     */
    public static function forUser(IgUser $user)
    {
        return static::create([
            'name' => $user->username,
            'type' => ($user->type === IgUser::TYPE_PERSONAL)
                ? static::PERSONAL_ACCOUNT
                : static::BUSINESS_ACCOUNT,
        ]);
    }

    /**
     * @since 0.1
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
        ];
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
    public function getType() : string
    {
        return $this->type;
    }
}
