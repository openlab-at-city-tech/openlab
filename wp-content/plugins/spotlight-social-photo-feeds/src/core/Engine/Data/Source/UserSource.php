<?php

namespace RebelCode\Spotlight\Instagram\Engine\Data\Source;

use RebelCode\Iris\Data\Source;
use RebelCode\Spotlight\Instagram\IgApi\IgUser;

/**
 * Source for posts posted by a user.
 */
class UserSource
{
    const TYPE_PERSONAL = 'PERSONAL_ACCOUNT';
    const TYPE_BUSINESS = 'BUSINESS_ACCOUNT';

    /**
     * Creates a media source for a user.
     *
     * @param string $username The username.
     * @param string $userType The user type.
     *
     * @return Source The created source instance.
     */
    public static function create(string $username, string $userType) : Source
    {
        if ($userType === IgUser::TYPE_PERSONAL) {
            $type = static::TYPE_PERSONAL;
        } elseif ($userType === IgUser::TYPE_BUSINESS) {
            $type = static::TYPE_BUSINESS;
        } else {
            $type = $userType;
        }

        return new Source($username, $type);
    }
}
