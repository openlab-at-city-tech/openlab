<?php

/**
 * Validates arbitrary text according to the HTML spec.
 */
class MSHTMLPurifier_AttrDef_Text extends MSHTMLPurifier_AttrDef
{

    /**
     * @param string $string
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        return $this->parseCDATA($string);
    }
}

// vim: et sw=4 sts=4
