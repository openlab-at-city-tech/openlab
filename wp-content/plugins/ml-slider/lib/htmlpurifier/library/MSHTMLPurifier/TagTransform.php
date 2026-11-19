<?php

/**
 * Defines a mutation of an obsolete tag into a valid tag.
 */
abstract class MSHTMLPurifier_TagTransform
{

    /**
     * Tag name to transform the tag to.
     * @type string
     */
    public $transform_to;

    /**
     * Transforms the obsolete tag into the valid tag.
     * @param MSHTMLPurifier_Token_Tag $tag Tag to be transformed.
     * @param MSHTMLPurifier_Config $config Mandatory MSHTMLPurifier_Config object
     * @param MSHTMLPurifier_Context $context Mandatory MSHTMLPurifier_Context object
     */
    abstract public function transform($tag, $config, $context);

    /**
     * Prepends CSS properties to the style attribute, creating the
     * attribute if it doesn't exist.
     * @warning Copied over from AttrTransform, be sure to keep in sync
     * @param array $attr Attribute array to process (passed by reference)
     * @param string $css CSS to prepend
     */
    protected function prependCSS(&$attr, $css)
    {
        $attr['style'] = isset($attr['style']) ? $attr['style'] : '';
        $attr['style'] = $css . $attr['style'];
    }
}

// vim: et sw=4 sts=4
