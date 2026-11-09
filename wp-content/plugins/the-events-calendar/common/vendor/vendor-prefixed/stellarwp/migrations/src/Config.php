<?php

/**
 * Migrations Config
 *
 * @package \StellarWP\Migrations
 */
declare (strict_types=1);
namespace TEC\Common\StellarWP\Migrations;

use RuntimeException;
use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Migrations\Contracts\Template_Engine;
use TEC\Common\StellarWP\Migrations\Utilities\Default_Template_Engine;
/**
 * Migrations Config
 *
 * @since 0.0.1
 *
 * @package \StellarWP\Migrations
 */
class Config
{
    /**
     * The assets URL.
     *
     * @since 0.0.1
     *
     * @var ?string
     */
    protected static ?string $assets_url = null;
    /**
     * Container object.
     *
     * @since 0.0.1
     *
     * @var ?ContainerInterface
     */
    protected static ?ContainerInterface $container = null;
    /**
     * The hook prefix.
     *
     * @since 0.0.1
     *
     * @var string
     */
    protected static string $hook_prefix;
    /**
     * Template engine instance.
     *
     * @since 0.0.1
     *
     * @var ?Template_Engine
     */
    protected static ?Template_Engine $template_engine = null;
    /**
     * Get the container.
     *
     * @since 0.0.1
     *
     * @throws RuntimeException If the container is not set.
     *
     * @return ContainerInterface
     */
    public static function get_container(): ContainerInterface
    {
        if (self::$container === null) {
            throw new RuntimeException('You must provide a container via StellarWP\Migrations\Config::set_container() before attempting to fetch it.');
        }
        return self::$container;
    }
    /**
     * Gets the assets URL.
     *
     * @since 0.0.1
     *
     * @return string|null The assets URL, or null if not set.
     */
    public static function get_assets_url(): ?string
    {
        return self::$assets_url;
    }
    /**
     * Gets the hook prefix.
     *
     * @since 0.0.1
     *
     * @throws RuntimeException If the hook prefix is not set.
     *
     * @return string
     */
    public static function get_hook_prefix(): string
    {
        if (!static::$hook_prefix) {
            $class = self::class;
            throw new RuntimeException("You must specify a hook prefix for your project with {$class}::set_hook_prefix()");
        }
        return static::$hook_prefix;
    }
    /**
     * Set the container object.
     *
     * @since 0.0.1
     *
     * @param ContainerInterface $container Container object.
     *
     * @return void
     */
    public static function set_container(ContainerInterface $container)
    {
        self::$container = $container;
    }
    /**
     * Sets the assets URL.
     *
     * The url should point to the assets directory of this library.
     *
     * @since 0.0.1
     *
     * @param string $assets_url The assets URL.
     *
     * @return void
     */
    public static function set_assets_url(string $assets_url): void
    {
        self::$assets_url = $assets_url;
    }
    /**
     * Sets the hook prefix.
     *
     * @since 0.0.1
     *
     * @param string $prefix The prefix to add to hooks.
     *
     * @throws RuntimeException If the hook prefix is empty.
     *
     * @return void
     */
    public static function set_hook_prefix(string $prefix): void
    {
        if (!$prefix) {
            throw new RuntimeException('The hook prefix cannot be empty.');
        }
        static::$hook_prefix = $prefix;
    }
    /**
     * Sets the template engine.
     *
     * @since 0.0.1
     *
     * @param Template_Engine $engine Template engine instance.
     *
     * @return void
     */
    public static function set_template_engine(Template_Engine $engine): void
    {
        static::$template_engine = $engine;
    }
    /**
     * Get the template engine.
     *
     * Returns the configured template engine, or creates a default one if not set.
     *
     * @since 0.0.1
     *
     * @return Template_Engine
     */
    public static function get_template_engine(): Template_Engine
    {
        if (static::$template_engine === null) {
            static::$template_engine = new Default_Template_Engine();
        }
        return static::$template_engine;
    }
    /**
     * Resets the config.
     *
     * @since 0.0.1
     *
     * @return void
     */
    public static function reset(): void
    {
        static::$assets_url = null;
        static::$container = null;
        static::$hook_prefix = '';
        static::$template_engine = null;
    }
}