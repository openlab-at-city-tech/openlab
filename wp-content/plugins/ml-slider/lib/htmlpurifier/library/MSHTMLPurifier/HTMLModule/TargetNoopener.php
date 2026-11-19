<?php

/**
 * Module adds the target-based noopener attribute transformation to a tags.  It
 * is enabled by HTML.TargetNoopener
 */
class MSHTMLPurifier_HTMLModule_TargetNoopener extends MSHTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'TargetNoopener';

    /**
     * @param MSHTMLPurifier_Config $config
     */
    public function setup($config) {
        $a = $this->addBlankElement('a');
        $a->attr_transform_post[] = new MSHTMLPurifier_AttrTransform_TargetNoopener();
    }
}
