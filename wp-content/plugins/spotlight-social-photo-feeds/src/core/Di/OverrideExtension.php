<?php

namespace RebelCode\Spotlight\Instagram\Di;

use Dhii\Services\ResolveKeysCapableTrait;
use Dhii\Services\Service;
use Psr\Container\ContainerInterface;

/**
 * A service helper for extensions that completely override an existing service.
 *
 * @since 0.1
 */
class OverrideExtension extends Service
{
    use ResolveKeysCapableTrait;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string $replacement The key of the service that will be used to replace the original when extended.
     */
    public function __construct(string $replacement)
    {
        parent::__construct([$replacement]);
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function __invoke(ContainerInterface $c)
    {
        return $this->resolveKeys($c, $this->dependencies)[0];
    }
}
