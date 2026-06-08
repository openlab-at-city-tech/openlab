<?php

declare(strict_types=1);

namespace RebelCode\Iris\Exception;

use RebelCode\Iris\Data\Source;
use RebelCode\Iris\Fetcher;
use Throwable;

/** @psalm-immutable */
class FetchException extends IrisException
{
    /** @var Fetcher|null */
    public $fetcher;

    /** @var Source|null */
    public $source;

    /** @var ?string */
    public $cursor;

    /**
     * @inheritDoc
     *
     * @param Fetcher|null $fetcher
     * @param Source|null $source
     * @param string|null $cursor
     */
    public function __construct(
        string $message = "",
        ?Fetcher $fetcher = null,
        ?Source $source = null,
        ?string $cursor = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $previous);
        $this->fetcher = $fetcher;
        $this->source = $source;
        $this->cursor = $cursor;
    }
}
