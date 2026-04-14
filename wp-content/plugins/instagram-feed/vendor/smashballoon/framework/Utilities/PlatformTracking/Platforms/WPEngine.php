<?php

namespace InstagramFeed\Vendor\Smashballoon\Framework\Utilities\PlatformTracking\Platforms;

class WPEngine implements PlatformInterface
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
        if (method_exists('InstagramFeed\Vendor\WpeCommon', 'get_wpe_auth_cookie_value') && !empty(\InstagramFeed\Vendor\WpeCommon::get_wpe_auth_cookie_value())) {
            $platform = 'wpengine';
        }
        return $platform;
    }
}
