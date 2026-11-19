<?php

/**
 * Dummy AttrDef that mimics another AttrDef, BUT it generates clones
 * with make.
 */
class MSHTMLPurifier_AttrDef_Clone extends MSHTMLPurifier_AttrDef
{
    /**
     * What we're cloning.
     * @type MSHTMLPurifier_AttrDef
     */
    protected $clone;

    /**
     * @param MSHTMLPurifier_AttrDef $clone
     */
    public function __construct($clone)
    {
        $this->clone = $clone;
    }

    /**
     * @param string $v
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($v, $config, $context)
    {
        return $this->clone->validate($v, $config, $context);
    }

    /**
     * @param string $string
     * @return MSHTMLPurifier_AttrDef
     */
    public function make($string)
    {
        return clone $this->clone;
    }
}

// vim: et sw=4 sts=4
