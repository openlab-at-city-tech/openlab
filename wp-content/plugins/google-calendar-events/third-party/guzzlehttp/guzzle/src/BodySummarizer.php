<?php

namespace SimpleCalendar\plugin_deps\GuzzleHttp;

use SimpleCalendar\plugin_deps\Psr\Http\Message\MessageInterface;
/** @internal */
final class BodySummarizer implements BodySummarizerInterface
{
    /**
     * @var int|null
     */
    private $truncateAt;
    public function __construct(?int $truncateAt = null)
    {
        $this->truncateAt = $truncateAt;
    }
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message) : ?string
    {
        return $this->truncateAt === null ? Psr7\Message::bodySummary($message) : Psr7\Message::bodySummary($message, $this->truncateAt);
    }
}
