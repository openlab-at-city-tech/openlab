<?php

class MSHTMLPurifier_HTMLModule_Tidy_Strict extends MSHTMLPurifier_HTMLModule_Tidy_XHTMLAndHTML4
{
    /**
     * @type string
     */
    public $name = 'Tidy_Strict';

    /**
     * @type string
     */
    public $defaultLevel = 'light';

    /**
     * @return array
     */
    public function makeFixes()
    {
        $r = parent::makeFixes();
        $r['blockquote#content_model_type'] = 'strictblockquote';
        return $r;
    }

    /**
     * @type bool
     */
    public $defines_child_def = true;

    /**
     * @param MSHTMLPurifier_ElementDef $def
     * @return MSHTMLPurifier_ChildDef_StrictBlockquote
     */
    public function getChildDef($def)
    {
        if ($def->content_model_type != 'strictblockquote') {
            return parent::getChildDef($def);
        }
        return new MSHTMLPurifier_ChildDef_StrictBlockquote($def->content_model);
    }
}

// vim: et sw=4 sts=4
