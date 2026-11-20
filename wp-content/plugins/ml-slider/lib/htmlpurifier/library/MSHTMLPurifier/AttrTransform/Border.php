<?php

/**
 * Pre-transform that changes deprecated border attribute to CSS.
 */
class MSHTMLPurifier_AttrTransform_Border extends MSHTMLPurifier_AttrTransform
{
    /**
     * @param array $attr
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['border'])) {
            return $attr;
        }
        $border_width = $this->confiscateAttr($attr, 'border');
        // some validation should happen here
        $this->prependCSS($attr, "border:{$border_width}px solid;");
        return $attr;
    }
}

// vim: et sw=4 sts=4
