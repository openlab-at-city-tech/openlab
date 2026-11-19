<?php

class MSHTMLPurifier_HTMLModule_Tidy_Proprietary extends MSHTMLPurifier_HTMLModule_Tidy
{

    /**
     * @type string
     */
    public $name = 'Tidy_Proprietary';

    /**
     * @type string
     */
    public $defaultLevel = 'light';

    /**
     * @return array
     */
    public function makeFixes()
    {
        $r = array();
        $r['table@background'] = new MSHTMLPurifier_AttrTransform_Background();
        $r['td@background']    = new MSHTMLPurifier_AttrTransform_Background();
        $r['th@background']    = new MSHTMLPurifier_AttrTransform_Background();
        $r['tr@background']    = new MSHTMLPurifier_AttrTransform_Background();
        $r['thead@background'] = new MSHTMLPurifier_AttrTransform_Background();
        $r['tfoot@background'] = new MSHTMLPurifier_AttrTransform_Background();
        $r['tbody@background'] = new MSHTMLPurifier_AttrTransform_Background();
        $r['table@height']     = new MSHTMLPurifier_AttrTransform_Length('height');
        return $r;
    }
}

// vim: et sw=4 sts=4
