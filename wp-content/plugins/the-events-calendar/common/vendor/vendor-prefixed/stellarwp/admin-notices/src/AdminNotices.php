<?php

declare(strict_types=1);

namespace TEC\Common\StellarWP\AdminNotices;

use TEC\Common\Psr\Container\ContainerInterface;
use RuntimeException;
use TEC\Common\StellarWP\AdminNotices\Actions\DisplayNoticesInAdmin;
use TEC\Common\StellarWP\AdminNotices\Contracts\NotificationsRegistrarInterface;

class AdminNotices
{
    /**
     * @var ContainerInterface|null
     */
    protected static $container;

    /**
     * @var NotificationsRegistrarInterface|null
     */
    protected static $registrar;

    /**
     * @var string used in actions, filters, and data storage
     */
    protected static $namespace;

    /**
     * @var string the URL to the package, used for enqueuing scripts
     */
    protected static $packageUrl;

    /**
     * Registers a notice to be conditionally displayed in the admin
     *
     * @since 1.0.0
     * @since 1.1.0 no longer include namespace in AdminNotice id
     *
     * @param string|callable $render
     */
    public static function show(string $notificationId, $render): AdminNotice
    {
        $notice = new AdminNotice($notificationId, $render);

        self::getRegistrar()->registerNotice($notice);

        return $notice;
    }

    /**
     * Immediately renders a notice, useful when wanting to display a notice in an ad hoc context
     *
     * @since 1.0.0
     *
     * @param bool $echo whether to echo or return the notice
     *
     * @return string|null
     */
    public static function render(AdminNotice $notice, bool $echo = true): ?string
    {
        ob_start();
        (new DisplayNoticesInAdmin(self::$namespace))($notice);
        $output = ob_get_clean();

        if ($echo) {
            echo $output;

            return null;
        } else {
            return $output;
        }
    }

    /**
     * Removes a registered notice so it will no longer be shown
     *
     * @since 1.0.0
     */
    public static function removeNotice(string $notificationId): void
    {
        self::getRegistrar()->unregisterNotice($notificationId);
    }

    /**
     * Sets the container with the register stored to be used for storing notices
     *
     * @since 1.0.0
     */
    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
        self::$registrar = null;
    }

    /**
     * Removes the container so the register will be stored locally
     *
     * @since 1.0.0
     */
    public static function removeContainer(): void
    {
        self::$container = null;
        self::$registrar = null;
    }

    /**
     * Initializes the package. Required to be called to display the notices.
     *
     * This should be called at the beginning of the plugin file along with other configuration.
     *
     * @since 1.1.0 added namespace validation
     * @since 1.0.0
     */
    public static function initialize(string $namespace, string $pluginUrl): void
    {
        if (empty($namespace)) {
            throw new RuntimeException('Namespace must be provided');
        } elseif (preg_match('/[^a-zA-Z0-9_-]/', $namespace)) {
            throw new RuntimeException('Namespace must only contain letters, numbers, hyphens, and underscores');
        }

        self::$packageUrl = $pluginUrl;
        self::$namespace = $namespace;

        add_action('admin_notices', [self::class, 'setUpNotices']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueScripts']);
    }

    /**
     * Returns the notices stored in the register
     *
     * @since 1.0.0
     *
     * @return AdminNotice[]
     */
    public static function getNotices(): array
    {
        return self::getRegistrar()->getNotices();
    }

    /**
     * Rests a dismissed notice for a given user so the notice will be shown again
     *
     * @since 1.1.0 uses namespacing
     * @since 1.0.0
     */
    public static function resetNoticeForUser(string $notificationId, int $userId): void
    {
        global $wpdb;

        $preferencesKey = $wpdb->get_blog_prefix() . 'persisted_preferences';
        $preferences = get_user_meta($userId, $preferencesKey, true);
        $packageKey = 'stellarwp/admin-notices/' . self::$namespace;

        if (isset($preferences[$packageKey][$notificationId])) {
            unset($preferences[$packageKey][$notificationId]);
            update_user_meta($userId, $preferencesKey, $preferences);
        }
    }

    /**
     * Resets all dismissed notices for a given user so all notices will be shown again
     *
     * @since 1.1.0 uses namespacing and simplified the method
     * @since 1.0.0
     */
    public static function resetAllNoticesForUser(int $userId): void
    {
        global $wpdb;

        $preferencesKey = $wpdb->get_blog_prefix() . 'persisted_preferences';
        $preferences = get_user_meta($userId, $preferencesKey, true);
        $packageKey = 'stellarwp/admin-notices/' . self::$namespace;

        if (isset($preferences[$packageKey])) {
            unset($preferences[$packageKey]);
            update_user_meta($userId, $preferencesKey, $preferences);
        }
    }

    /**
     * Hook action to display the notices in the admin
     *
     * @since 1.1.0 passes the namespace to the display notices class
     * @since 1.0.0
     */
    public static function setUpNotices(): void
    {
        (new DisplayNoticesInAdmin(self::$namespace))(...self::getNotices());
    }

    /**
     * Hook action to enqueue the scripts needed for dismissing notices
     *
     * @since 1.1.0 added the namespacing attribute to the script tag
     * @since 1.0.2 use filetime for versioning, which will bust the cache when the library is updated
     * @since 1.0.0
     */
    public static function enqueueScripts(): void
    {
        $namespace = self::$namespace;
        $handle = "$namespace-admin-notices";
        $version = filemtime(__DIR__ . '/resources/admin-notices.js');

        add_filter('script_loader_tag', static function ($tag, $tagHandle) use ($handle, $namespace) {
            if ($handle !== $tagHandle) {
                return $tag;
            }

            $tag = str_replace(' src', ' defer src', $tag);

            $replacement = "<script data-stellarwp-namespace='$namespace'";

            return str_replace('<script', $replacement, $tag);
        }, 10, 2);

        wp_enqueue_script(
            $handle,
            self::$packageUrl . '/src/resources/admin-notices.js',
            ['jquery', 'wp-data', 'wp-preferences'],
            $version,
            true
        );
    }

    /**
     * Returns the registrar instance, from the container if available, otherwise a locally stored instance
     *
     * @since 1.0.0
     */
    private static function getRegistrar(): NotificationsRegistrarInterface
    {
        if (self::$registrar !== null) {
            return self::$registrar;
        }

        if (self::$container && !self::$container->has(NotificationsRegistrarInterface::class)) {
            throw new RuntimeException('NotificationsRegistrarInterface not found in container');
        }

        if (self::$container) {
            self::$registrar = self::$container->get(NotificationsRegistrarInterface::class);
        } else {
            self::$registrar = new NotificationsRegistrar();
        }

        return self::$registrar;
    }
}
