<?php

/**
 * Implements required attribute stipulation for <script>
 */
class MSHTMLPurifier_AttrTransform_ScriptRequired extends MSHTMLPurifier_AttrTransform
{
    /**
     * @param array $attr
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['type'])) {
            $attr['type'] = 'text/javascript';
        }
        return $attr;
    }
}

// vim: et sw=4 sts=4
