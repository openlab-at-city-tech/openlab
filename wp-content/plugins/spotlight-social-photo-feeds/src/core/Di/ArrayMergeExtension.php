<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Di;

use Dhii\Services\Service;
use Psr\Container\ContainerInterface;

class ArrayMergeExtension extends Service
{
    /**
     * Constructor.
     *
     * @param string $dependency The key of the service whose value to merge into the original.
     */
    public function __construct(string $dependency)
    {
        parent::__construct([$dependency]);
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function __invoke(ContainerInterface $c, $prev = [])
    {
        return array_merge($prev, $c->get($this->dependencies[0]));
    }
}
