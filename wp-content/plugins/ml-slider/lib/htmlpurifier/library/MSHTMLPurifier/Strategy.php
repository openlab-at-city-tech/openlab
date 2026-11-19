<?php

/**
 * Supertype for classes that define a strategy for modifying/purifying tokens.
 *
 * While MSHTMLPurifier's core purpose is fixing HTML into something proper,
 * strategies provide plug points for extra configuration or even extra
 * features, such as custom tags, custom parsing of text, etc.
 */


abstract class MSHTMLPurifier_Strategy
{

    /**
     * Executes the strategy on the tokens.
     *
     * @param MSHTMLPurifier_Token[] $tokens Array of MSHTMLPurifier_Token objects to be operated on.
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return MSHTMLPurifier_Token[] Processed array of token objects.
     */
    abstract public function execute($tokens, $config, $context);
}

// vim: et sw=4 sts=4
