<?php

/**
 * Validates http (HyperText Transfer Protocol) as defined by RFC 2616
 */
class MSHTMLPurifier_URIScheme_http extends MSHTMLPurifier_URIScheme
{
    /**
     * @type int
     */
    public $default_port = 80;

    /**
     * @type bool
     */
    public $browsable = true;

    /**
     * @type bool
     */
    public $hierarchical = true;

    /**
     * @param MSHTMLPurifier_URI $uri
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return bool
     */
    public function doValidate(&$uri, $config, $context)
    {
        $uri->userinfo = null;
        return true;
    }
}

// vim: et sw=4 sts=4
