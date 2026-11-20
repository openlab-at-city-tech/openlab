<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\RecommendedPlugins;

use RebelCode\Spotlight\Instagram\Config\ConfigEntry;

/**
 * Decides whether the Feeds screen shows a recommended plugin banner to the current user, and remembers its dismissal.
 */
class RecommendedPluginsBanner
{
    /** @var RecommendedPlugins */
    protected $plugins;

    /** @var string */
    protected $capability;

    /** @var string */
    protected $metaKey;

    /** @var int */
    protected $minAge;

    /** @var ConfigEntry */
    protected $dateStarted;

    /**
     * @param RecommendedPlugins $plugins     The recommended plugins.
     * @param string             $capability  The capability a user needs to see and dismiss the banner.
     * @param string             $metaKey     The user meta key that records the dismissal.
     * @param int                $minAge      How long Spotlight must have been in use before the banner shows, in seconds.
     * @param ConfigEntry        $dateStarted When Spotlight was first used, as a timestamp.
     */
    public function __construct(
        RecommendedPlugins $plugins,
        string $capability,
        string $metaKey,
        int $minAge,
        ConfigEntry $dateStarted
    ) {
        $this->plugins = $plugins;
        $this->capability = $capability;
        $this->metaKey = $metaKey;
        $this->minAge = $minAge;
        $this->dateStarted = $dateStarted;
    }

    /** The banner data for the current user, or null when no banner should show. */
    public function getData(): ?array
    {
        $started = (int) $this->dateStarted->getValue();

        if (
            !current_user_can($this->capability) ||
            $this->isDismissed() ||
            $started === 0 ||
            time() - $started < $this->minAge
        ) {
            return null;
        }

        foreach ($this->plugins->getAll() as $plugin) {
            if (empty($plugin['banner'])) {
                continue;
            }

            $status = $this->plugins->getStatus($plugin['slug']);

            if ($status === RecommendedPlugins::ACTIVE) {
                continue;
            }

            if ($status === RecommendedPlugins::INSTALLED) {
                $file = $this->plugins->getFile($plugin['slug']);

                if (!current_user_can('activate_plugin', $file)) {
                    continue;
                }

                $actionUrl = $this->plugins->getActivateUrl($file);
                $actionLabel = $plugin['banner']['activate'];
            } else {
                $actionUrl = $this->plugins->getInstallUrl($plugin['slug']);
                $actionLabel = $plugin['banner']['install'];
            }

            return [
                'slug' => $plugin['slug'],
                'name' => $plugin['name'],
                'icon' => $plugin['icon'],
                'message' => $plugin['banner']['message'],
                'actionLabel' => $actionLabel,
                'actionUrl' => $actionUrl,
            ];
        }

        return null;
    }

    public function isDismissed(): bool
    {
        return (bool) get_user_meta(get_current_user_id(), $this->metaKey, true);
    }

    public function dismiss(): void
    {
        update_user_meta(get_current_user_id(), $this->metaKey, 1);
    }
}
