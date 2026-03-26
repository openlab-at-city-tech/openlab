<?php

namespace InstagramFeed\Vendor\Smashballoon\Framework\Utilities\PlatformTracking\Platforms;

interface PlatformInterface
{
    /**
     * Register the platform hooks.
     *
     * @return void
     */
    public function register();
    /**
     * Filter the hosting platform.
     *
     * @param string $platform
     *
     * @return string
     */
    public function filter_sb_hosting_platform($platform);
}
