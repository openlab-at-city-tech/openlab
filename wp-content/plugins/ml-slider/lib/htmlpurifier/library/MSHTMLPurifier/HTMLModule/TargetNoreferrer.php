<?php

/**
 * Module adds the target-based noreferrer attribute transformation to a tags.  It
 * is enabled by HTML.TargetNoreferrer
 */
class MSHTMLPurifier_HTMLModule_TargetNoreferrer extends MSHTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'TargetNoreferrer';

    /**
     * @param MSHTMLPurifier_Config $config
     */
    public function setup($config) {
        $a = $this->addBlankElement('a');
        $a->attr_transform_post[] = new MSHTMLPurifier_AttrTransform_TargetNoreferrer();
    }
}
