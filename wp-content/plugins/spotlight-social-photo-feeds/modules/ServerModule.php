<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Constructor;
use Psr\Container\ContainerInterface;
use RebelCode\Spotlight\Instagram\Module;
use RebelCode\Spotlight\Instagram\Server;

class ServerModule extends Module
{
    public function getFactories(): array
    {
        return [
            'instance' => new Constructor(Server::class, [
                '@engine/instance',
                '@engine/importer',
                '@feeds/manager',
                '@engine/importer/lock'
            ]),
        ];
    }
}
