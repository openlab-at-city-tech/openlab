<?php

class MSHTMLPurifier_HTMLModule_XMLCommonAttributes extends MSHTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'XMLCommonAttributes';

    /**
     * @type array
     */
    public $attr_collections = array(
        'Lang' => array(
            'xml:lang' => 'LanguageCode',
        )
    );
}

// vim: et sw=4 sts=4
