<?php

namespace SimpleCalendar\plugin_deps\GuzzleHttp;

use SimpleCalendar\plugin_deps\Psr\Http\Message\MessageInterface;
final class BodySummarizer implements \SimpleCalendar\plugin_deps\GuzzleHttp\BodySummarizerInterface
{
    /**
     * @var int|null
     */
    private $truncateAt;
    public function __construct(int $truncateAt = null)
    {
        $this->truncateAt = $truncateAt;
    }
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message) : ?string
    {
        return $this->truncateAt === null ? \SimpleCalendar\plugin_deps\GuzzleHttp\Psr7\Message::bodySummary($message) : \SimpleCalendar\plugin_deps\GuzzleHttp\Psr7\Message::bodySummary($message, $this->truncateAt);
    }
}
