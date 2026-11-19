<?php

// this MUST be placed in post, as it assumes that any value in dir is valid

/**
 * Post-transform that ensures that bdo tags have the dir attribute set.
 */
class MSHTMLPurifier_AttrTransform_BdoDir extends MSHTMLPurifier_AttrTransform
{

    /**
     * @param array $attr
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (isset($attr['dir'])) {
            return $attr;
        }
        $attr['dir'] = $config->get('Attr.DefaultTextDir');
        return $attr;
    }
}

// vim: et sw=4 sts=4
