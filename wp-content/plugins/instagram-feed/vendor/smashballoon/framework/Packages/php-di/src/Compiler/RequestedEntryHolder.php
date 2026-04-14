<?php


namespace InstagramFeed\Vendor\DI\Compiler;

use InstagramFeed\Vendor\DI\Factory\RequestedEntry;
/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class RequestedEntryHolder implements RequestedEntry
{
    /**
     * @var string
     */
    private $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    public function getName(): string
    {
        return $this->name;
    }
}
