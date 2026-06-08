<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules\Dev;

use RebelCode\Spotlight\Instagram\IgApi\AccessToken;
use RebelCode\Spotlight\Instagram\IgApi\IgAccount;
use RebelCode\Spotlight\Instagram\IgApi\IgUser;

class DevAccessTokenHandler
{
    public const CODE_PREFIX = 'developer';

    public static function handle(?IgAccount $account, AccessToken $token): ?IgAccount
    {
        if (!DevModule::isDeveloper() || stripos($token->code, static::CODE_PREFIX) !== 0) {
            return $account;
        }

        $mediaCount = substr($token->code, strlen(static::CODE_PREFIX));
        $mediaCount = (int) $mediaCount;
        $mediaCount = (!is_int($mediaCount) || $mediaCount <= 0) ? DevCatalog::DEFAULT_SIZE : $mediaCount;

        $igUser = new IgUser();
        $igUser->name = 'Developer';
        $igUser->type = 'DEVELOPER';
        $igUser->username = "developer_{$mediaCount}";
        $igUser->bio = "Spotlight developer account with {$mediaCount} posts";
        $igUser->followersCount = 0;
        $igUser->followsCount = 0;
        $igUser->mediaCount = $mediaCount;
        $igUser->profilePicUrl = '';
        $igUser->website = '';

        return new IgAccount($igUser, $token);
    }
}
