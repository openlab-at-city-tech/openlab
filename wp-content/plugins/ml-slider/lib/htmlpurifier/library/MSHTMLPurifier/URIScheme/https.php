<?php

/**
 * Validates https (Secure HTTP) according to http scheme.
 */
class MSHTMLPurifier_URIScheme_https extends MSHTMLPurifier_URIScheme_http
{
    /**
     * @type int
     */
    public $default_port = 443;
    /**
     * @type bool
     */
    public $secure = true;
}

// vim: et sw=4 sts=4
