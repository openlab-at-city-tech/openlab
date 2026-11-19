<?php

/**
 * Module adds the target=blank attribute transformation to a tags.  It
 * is enabled by HTML.TargetBlank
 */
class MSHTMLPurifier_HTMLModule_TargetBlank extends MSHTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'TargetBlank';

    /**
     * @param MSHTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $a = $this->addBlankElement('a');
        $a->attr_transform_post[] = new MSHTMLPurifier_AttrTransform_TargetBlank();
    }
}

// vim: et sw=4 sts=4
