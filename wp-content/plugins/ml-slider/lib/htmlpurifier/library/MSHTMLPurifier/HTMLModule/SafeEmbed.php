<?php

/**
 * A "safe" embed module. See SafeObject. This is a proprietary element.
 */
class MSHTMLPurifier_HTMLModule_SafeEmbed extends MSHTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'SafeEmbed';

    /**
     * @param MSHTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $max = $config->get('HTML.MaxImgLength');
        $embed = $this->addElement(
            'embed',
            'Inline',
            'Empty',
            'Common',
            array(
                'src*' => 'URI#embedded',
                'type' => 'Enum#application/x-shockwave-flash',
                'width' => 'Pixels#' . $max,
                'height' => 'Pixels#' . $max,
                'allowscriptaccess' => 'Enum#never',
                'allownetworking' => 'Enum#internal',
                'flashvars' => 'Text',
                'wmode' => 'Enum#window,transparent,opaque',
                'name' => 'ID',
            )
        );
        $embed->attr_transform_post[] = new MSHTMLPurifier_AttrTransform_SafeEmbed();
    }
}

// vim: et sw=4 sts=4
