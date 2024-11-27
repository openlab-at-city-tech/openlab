<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace TEC\Common\StellarWP\AdminNotices\Traits;

trait HasNamespace
{
    /**
     * The namespace for the plugin.
     *
     * @var string
     */
    protected $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }
}
