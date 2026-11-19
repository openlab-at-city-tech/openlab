<?php

class MSHTMLPurifier_AttrTransform_SafeEmbed extends MSHTMLPurifier_AttrTransform
{
    /**
     * @type string
     */
    public $name = "SafeEmbed";

    /**
     * @param array $attr
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        $attr['allowscriptaccess'] = 'never';
        $attr['allownetworking'] = 'internal';
        $attr['type'] = 'application/x-shockwave-flash';
        return $attr;
    }
}

// vim: et sw=4 sts=4
