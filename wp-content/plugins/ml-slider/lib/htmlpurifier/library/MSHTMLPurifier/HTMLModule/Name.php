<?php

class MSHTMLPurifier_HTMLModule_Name extends MSHTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Name';

    /**
     * @param MSHTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $elements = array('a', 'applet', 'form', 'frame', 'iframe', 'img', 'map');
        foreach ($elements as $name) {
            $element = $this->addBlankElement($name);
            $element->attr['name'] = 'CDATA';
            if (!$config->get('HTML.Attr.Name.UseCDATA')) {
                $element->attr_transform_post[] = new MSHTMLPurifier_AttrTransform_NameSync();
            }
        }
    }
}

// vim: et sw=4 sts=4
