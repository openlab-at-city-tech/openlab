<?php

namespace RebelCode\Spotlight\Instagram\Di;

use Dhii\Services\Factory;

/**
 * A factory for services that read a local JSON file and return the parsed result.
 */
class JsonFileService extends Factory
{
    public function __construct(string $fileService, $defaultService = null)
    {
        parent::__construct(
            array_filter([$fileService, $defaultService]),
            function ($file, $default = null) {
                if (!is_readable($file)) {
                    return $default;
                }

                $json = @file_get_contents($file);
                if (!is_string($json)) {
                    return $default;
                }

                $data = @json_decode($json);

                return $data === null ? $default : $data;
            }
        );
    }
}
