<?php

namespace RebelCode\Spotlight\Instagram\Wp;

/**
 * Represents a page that is shown in the WordPress Admin.
 *
 * @since 0.1
 */
class AdminPage
{
    /**
     * @since 0.1
     *
     * @var string
     */
    protected $title;

    /**
     * @since 0.1
     *
     * @var callable
     */
    protected $renderFn;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string   $title    The title of the page, shown in the browser's tab.
     * @param callable $renderFn The function that returns the rendered contents of the page
     */
    public function __construct(string $title, callable $renderFn)
    {
        $this->title = $title;
        $this->renderFn = $renderFn;
    }

    /**
     * Retrieves the title for the page.
     *
     * @since 0.1
     *
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * Retrieves the render function for the page.
     *
     * @since 0.1
     *
     * @return callable
     */
    public function getRenderFn() : callable
    {
        $returnFn = $this->renderFn;

        // The first argument is passed by WordPress and is unused
        return function ($unused, ...$args) use ($returnFn) {
            echo $returnFn(...$args);
        };
    }
}
