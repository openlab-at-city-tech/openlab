<?php

namespace SimpleCalendar\plugin_deps\GuzzleHttp;

use SimpleCalendar\plugin_deps\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message) : ?string;
}
