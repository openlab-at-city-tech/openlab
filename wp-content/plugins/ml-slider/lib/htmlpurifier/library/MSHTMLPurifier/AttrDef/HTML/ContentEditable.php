<?php

class MSHTMLPurifier_AttrDef_HTML_ContentEditable extends MSHTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        $allowed = array('false');
        if ($config->get('HTML.Trusted')) {
            $allowed = array('', 'true', 'false');
        }

        $enum = new MSHTMLPurifier_AttrDef_Enum($allowed);

        return $enum->validate($string, $config, $context);
    }
}
