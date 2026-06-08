<?php

namespace RebelCode\Spotlight\Instagram\Wp;

/**
 * Represents a WordPress admin submenu that appears under a top-level menu.
 *
 * @since 0.1
 */
class SubMenu
{
    /**
     * @since 0.1
     *
     * @var string
     */
    protected $slug;

    /**
     * @since 0.1
     *
     * @var string
     */
    protected $label;

    /**
     * @since 0.1
     *
     * @var string
     */
    protected $capability;

    /**
     * @since 0.1
     *
     * @var int|null
     */
    protected $position;

    /**
     * @since 0.1
     *
     * @var AdminPage|null
     */
    protected $page;

    /**
     * @since 0.1
     *
     * @var string|null
     */
    protected $url;

    /**
     * Creates a sub-menu instance that refers to a page.
     *
     * @since 0.1
     *
     * @param AdminPage $page       The page that the submenu refers to.
     * @param string    $slug       The slug name for the submenu.
     * @param string    $label      The label to show for this submenu in the WP admin menu bar.
     * @param string    $capability The user capability required to show the menu and access the page.
     * @param int|null  $position   The position of the submenu in its parent menu's container.
     *
     * @return static The created submenu instance.
     */
    public static function page(AdminPage $page, string $slug, string $label, string $capability, ?int $position = null)
    {
        $submenu = new static();
        $submenu->slug = $slug;
        $submenu->label = $label;
        $submenu->capability = $capability;
        $submenu->position = $position;
        $submenu->page = $page;
        $submenu->url = null;

        return $submenu;
    }

    /**
     * Creates a sub-menu instance that points to a URL.
     *
     * @since 0.1
     *
     * @param string   $url        The absolute URL that the submenu points to.
     * @param string   $label      The label to show for this submenu in the WP admin menu bar.
     * @param string   $capability The user capability required to show the menu and access the page.
     * @param int|null $position   The position of the submenu in its parent menu's container.
     *
     * @return static The created submenu instance.
     */
    public static function url(string $url, string $label, string $capability, ?int $position = null)
    {
        $submenu = new static();
        $submenu->slug = '';
        $submenu->label = $label;
        $submenu->capability = $capability;
        $submenu->position = $position;
        $submenu->url = $url;
        $submenu->page = null;

        return $submenu;
    }

    /**
     * Registers the submenu to a parent menu.
     *
     * @since 0.1
     *
     * @param string  $parentSlug The slug name of the parent menu.
     * @param SubMenu $instance   The submenu instance.
     */
    public static function registerFor(string $parentSlug, SubMenu $instance)
    {
        if (!current_user_can($instance->capability)) {
            return;
        }

        if ($instance->page instanceof AdminPage && $instance->url === null) {
            add_submenu_page(
                $parentSlug,
                $instance->page->getTitle(),
                $instance->label,
                $instance->capability,
                $instance->slug,
                $instance->page->getRenderFn(),
                $instance->position
            );

            return;
        }

        if (is_string($instance->url) && strlen($instance->url) > 0 && $instance->page === null) {
            global $submenu;

            // Add to the menu
            $submenu[$parentSlug][] = [
                $instance->label,
                $instance->capability,
                $instance->url,
                $instance->label,
            ];

            return;
        }
    }
}
