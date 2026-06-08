<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Modules;

use Dhii\Services\Factories\StringService;
use Dhii\Services\Factories\Value;
use RebelCode\Spotlight\Instagram\Di\ArrayExtension;
use RebelCode\Spotlight\Instagram\Di\JsonFileService;
use RebelCode\Spotlight\Instagram\Module;

class TemplatesModule extends Module
{
    /** @inheritDoc */
    public function getFactories(): array
    {
        return [
            // The JSON file and the default value to use in case the file cannot be read
            'file' => new StringService("{0}/data/templates.json", ['@plugin/dir']),
            'default' => new Value([]),

            // The data parsed from the JSON file
            'data' => new JsonFileService('file', 'default'),
        ];
    }

    /** @inheritDoc */
    public function getExtensions(): array
    {
        return [
            // Add templates to admin-common l10n
            'ui/l10n/admin-common' => new ArrayExtension(['templates' => 'data']),
        ];
    }
}
