<?php

namespace InstagramFeed\Vendor\Smashballoon\Framework\Utilities\PlatformTracking\Platforms;

class Kinsta implements PlatformInterface
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        add_filter('sb_hosting_platform', [$this, 'filter_sb_hosting_platform']);
    }
    /**
     * @inheritDoc
     */
    public function filter_sb_hosting_platform($platform)
    {
        if (!empty(getenv('KINSTA_CACHE_ZONE'))) {
            $platform = 'kinsta';
        }
        return $platform;
    }
}
