<?php

/**
 * Core strategy composed of the big four strategies.
 */
class MSHTMLPurifier_Strategy_Core extends MSHTMLPurifier_Strategy_Composite
{
    public function __construct()
    {
        $this->strategies[] = new MSHTMLPurifier_Strategy_RemoveForeignElements();
        $this->strategies[] = new MSHTMLPurifier_Strategy_MakeWellFormed();
        $this->strategies[] = new MSHTMLPurifier_Strategy_FixNesting();
        $this->strategies[] = new MSHTMLPurifier_Strategy_ValidateAttributes();
    }
}

// vim: et sw=4 sts=4
