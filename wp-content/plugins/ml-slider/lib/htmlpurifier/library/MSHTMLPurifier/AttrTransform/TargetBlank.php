<?php

// must be called POST validation

/**
 * Adds target="blank" to all outbound links.  This transform is
 * only attached if Attr.TargetBlank is TRUE.  This works regardless
 * of whether or not Attr.AllowedFrameTargets
 */
class MSHTMLPurifier_AttrTransform_TargetBlank extends MSHTMLPurifier_AttrTransform
{
    /**
     * @type MSHTMLPurifier_URIParser
     */
    private $parser;

    public function __construct()
    {
        $this->parser = new MSHTMLPurifier_URIParser();
    }

    /**
     * @param array $attr
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['href'])) {
            return $attr;
        }

        // XXX Kind of inefficient
        $url = $this->parser->parse($attr['href']);
        
        // Ignore invalid schemes (e.g. `javascript:`)
        if (!($scheme = $url->getSchemeObj($config, $context))) {
            return $attr;
        }

        if ($scheme->browsable && !$url->isBenign($config, $context)) {
            $attr['target'] = '_blank';
        }
        return $attr;
    }
}

// vim: et sw=4 sts=4
