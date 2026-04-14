<?php

namespace InstagramFeed\Vendor\Smashballoon\Framework\Utilities\PlatformTracking\Platforms;

class Bluehost implements PlatformInterface
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
        if (defined('BLUEHOST_PLUGIN_VERSION')) {
            $platform = 'bluehost';
        }
        return $platform;
    }
}
