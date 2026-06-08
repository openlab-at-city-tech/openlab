<?php

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factory;
use RebelCode\Atlas\Atlas;
use RebelCode\Spotlight\Instagram\Module;

class AtlasModule extends Module
{
    public function getFactories(): array
    {
        return [
            'instance' => new Factory([], function () {
                return Atlas::createDefault();
            }),
        ];
    }
}
