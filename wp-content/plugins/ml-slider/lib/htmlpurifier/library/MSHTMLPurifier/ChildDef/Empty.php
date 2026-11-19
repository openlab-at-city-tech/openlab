<?php

/**
 * Definition that disallows all elements.
 * @warning validateChildren() in this class is actually never called, because
 *          empty elements are corrected in MSHTMLPurifier_Strategy_MakeWellFormed
 *          before child definitions are parsed in earnest by
 *          MSHTMLPurifier_Strategy_FixNesting.
 */
class MSHTMLPurifier_ChildDef_Empty extends MSHTMLPurifier_ChildDef
{
    /**
     * @type bool
     */
    public $allow_empty = true;

    /**
     * @type string
     */
    public $type = 'empty';

    public function __construct()
    {
    }

    /**
     * @param MSHTMLPurifier_Node[] $children
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return array
     */
    public function validateChildren($children, $config, $context)
    {
        return array();
    }
}

// vim: et sw=4 sts=4
