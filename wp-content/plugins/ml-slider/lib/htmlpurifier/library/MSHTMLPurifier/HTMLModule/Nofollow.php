<?php

/**
 * Module adds the nofollow attribute transformation to a tags.  It
 * is enabled by HTML.Nofollow
 */
class MSHTMLPurifier_HTMLModule_Nofollow extends MSHTMLPurifier_HTMLModule
{

    /**
     * @type string
     */
    public $name = 'Nofollow';

    /**
     * @param MSHTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $a = $this->addBlankElement('a');
        $a->attr_transform_post[] = new MSHTMLPurifier_AttrTransform_Nofollow();
    }
}

// vim: et sw=4 sts=4
