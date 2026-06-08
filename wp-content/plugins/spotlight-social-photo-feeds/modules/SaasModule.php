<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\Value;
use RebelCode\Spotlight\Instagram\Module;

class SaasModule extends Module
{
    public function getFactories(): array
    {
        return [
            'server/base_url' => new Value('https://spotlightwp.com/wp-json/spotlight'),
        ];
    }
}
