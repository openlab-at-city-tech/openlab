<?php

namespace RebelCode\Spotlight\Instagram\Utils;

class FileLocation
{
    /** @var string */
    public $path;

    /** @var string */
    public $url;

    /** Constructor. */
    public function __construct(string $path, string $url)
    {
        $this->path = $path;
        $this->url = $url;
    }

    public function downloadFrom(string $url, bool $overwrite = true)
    {
        if ($overwrite || !file_exists($this->path)) {
            Files::download($url, $this->path);
        }
    }
}
