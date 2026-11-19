<?php

/**
 * XHTML 1.1 Hypertext Module, defines hypertext links. Core Module.
 */
class MSHTMLPurifier_HTMLModule_Hypertext extends MSHTMLPurifier_HTMLModule
{

    /**
     * @type string
     */
    public $name = 'Hypertext';

    /**
     * @param MSHTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $a = $this->addElement(
            'a',
            'Inline',
            'Inline',
            'Common',
            array(
                // 'accesskey' => 'Character',
                // 'charset' => 'Charset',
                'href' => 'URI',
                // 'hreflang' => 'LanguageCode',
                'rel' => new MSHTMLPurifier_AttrDef_HTML_LinkTypes('rel'),
                'rev' => new MSHTMLPurifier_AttrDef_HTML_LinkTypes('rev'),
                // 'tabindex' => 'Number',
                // 'type' => 'ContentType',
            )
        );
        $a->formatting = true;
        $a->excludes = array('a' => true);
    }
}

// vim: et sw=4 sts=4
