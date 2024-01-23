<?php

namespace WpToTwitter_Vendor\GuzzleHttp;

use WpToTwitter_Vendor\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message) : ?string;
}
