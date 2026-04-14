<?php

namespace InstagramFeed\Vendor\Smashballoon\Framework\Utilities\PlatformTracking\Platforms;

class SiteGround implements PlatformInterface
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
        if (defined('WP_CONTENT_URL') && \false !== strpos(\WP_CONTENT_URL, 'sg-host.com')) {
            $platform = 'siteground';
        }
        return $platform;
    }
}
