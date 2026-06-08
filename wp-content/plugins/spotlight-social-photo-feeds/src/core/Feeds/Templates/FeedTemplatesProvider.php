<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Feeds\Templates;

use RebelCode\Spotlight\Instagram\SaaS\SaasResourceFetcher;
use Throwable;

class FeedTemplatesProvider extends SaasResourceFetcher
{
    protected $templates = [];

    public function get(): array
    {
        if (!$this->templates) {
            try {
                $this->templates = parent::get();
            } catch (Throwable $t) {}
        }

        return $this->templates;
    }
}
