<?php

/**
 * Writes default type for all objects. Currently only supports flash.
 */
class MSHTMLPurifier_AttrTransform_SafeObject extends MSHTMLPurifier_AttrTransform
{
    /**
     * @type string
     */
    public $name = "SafeObject";

    /**
     * @param array $attr
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['type'])) {
            $attr['type'] = 'application/x-shockwave-flash';
        }
        return $attr;
    }
}

// vim: et sw=4 sts=4
