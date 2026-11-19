<?php

class MSHTMLPurifier_URIFilter_DisableResources extends MSHTMLPurifier_URIFilter
{
    /**
     * @type string
     */
    public $name = 'DisableResources';

    /**
     * @param MSHTMLPurifier_URI $uri
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return bool
     */
    public function filter(&$uri, $config, $context)
    {
        return !$context->get('EmbeddedURI', true);
    }
}

// vim: et sw=4 sts=4
