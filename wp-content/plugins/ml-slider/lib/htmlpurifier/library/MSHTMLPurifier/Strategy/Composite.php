<?php

/**
 * Composite strategy that runs multiple strategies on tokens.
 */
abstract class MSHTMLPurifier_Strategy_Composite extends MSHTMLPurifier_Strategy
{

    /**
     * List of strategies to run tokens through.
     * @type MSHTMLPurifier_Strategy[]
     */
    protected $strategies = array();

    /**
     * @param MSHTMLPurifier_Token[] $tokens
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return MSHTMLPurifier_Token[]
     */
    public function execute($tokens, $config, $context)
    {
        foreach ($this->strategies as $strategy) {
            $tokens = $strategy->execute($tokens, $config, $context);
        }
        return $tokens;
    }
}

// vim: et sw=4 sts=4
