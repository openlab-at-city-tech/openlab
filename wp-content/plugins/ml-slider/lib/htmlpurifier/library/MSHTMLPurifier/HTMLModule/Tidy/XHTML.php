<?php

class MSHTMLPurifier_HTMLModule_Tidy_XHTML extends MSHTMLPurifier_HTMLModule_Tidy
{
    /**
     * @type string
     */
    public $name = 'Tidy_XHTML';

    /**
     * @type string
     */
    public $defaultLevel = 'medium';

    /**
     * @return array
     */
    public function makeFixes()
    {
        $r = array();
        $r['@lang'] = new MSHTMLPurifier_AttrTransform_Lang();
        return $r;
    }
}

// vim: et sw=4 sts=4
