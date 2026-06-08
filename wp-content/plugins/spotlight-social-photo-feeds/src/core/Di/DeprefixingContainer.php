<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Di;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * A container implementation that wraps around an inner container to automatically add prefixes to keys during
 * fetching and look up, allowing consumers to omit them.
 */
class DeprefixingContainer implements ContainerInterface
{
    /** @var ContainerInterface */
    protected $inner;
    /** @var string */
    protected $prefix;
    /** @var bool */
    protected $strict;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The container whose keys to de-prefix.
     * @param string $prefix The prefix to remove from the container's keys.
     * @param bool $strict Whether to fall back to prefixed keys if an un-prefixed key does not exist.
     */
    public function __construct(ContainerInterface $container, string $prefix, bool $strict = true)
    {
        $this->inner = $container;
        $this->prefix = $prefix;
        $this->strict = $strict;
    }

    /** @inheritdoc */
    public function get($id)
    {
        try {
            return $this->inner->get($this->prefix . $id);
        } catch (NotFoundExceptionInterface $nfException) {
            if ($this->strict || !$this->inner->has($id)) {
                throw $nfException;
            }
        }

        return $this->inner->get($id);
    }

    /** @inheritdoc */
    public function has($id): bool
    {
        return $this->inner->has($this->prefix . $id) || (!$this->strict && $this->inner->has($id));
    }
}
