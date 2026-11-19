<?php

/**
 * Definition that uses different definitions depending on context.
 *
 * The del and ins tags are notable because they allow different types of
 * elements depending on whether or not they're in a block or inline context.
 * Chameleon allows this behavior to happen by using two different
 * definitions depending on context.  While this somewhat generalized,
 * it is specifically intended for those two tags.
 */
class MSHTMLPurifier_ChildDef_Chameleon extends MSHTMLPurifier_ChildDef
{

    /**
     * Instance of the definition object to use when inline. Usually stricter.
     * @type MSHTMLPurifier_ChildDef_Optional
     */
    public $inline;

    /**
     * Instance of the definition object to use when block.
     * @type MSHTMLPurifier_ChildDef_Optional
     */
    public $block;

    /**
     * @type string
     */
    public $type = 'chameleon';

    /**
     * @param array $inline List of elements to allow when inline.
     * @param array $block List of elements to allow when block.
     */
    public function __construct($inline, $block)
    {
        $this->inline = new MSHTMLPurifier_ChildDef_Optional($inline);
        $this->block = new MSHTMLPurifier_ChildDef_Optional($block);
        $this->elements = $this->block->elements;
    }

    /**
     * @param MSHTMLPurifier_Node[] $children
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return bool
     */
    public function validateChildren($children, $config, $context)
    {
        if ($context->get('IsInline') === false) {
            return $this->block->validateChildren(
                $children,
                $config,
                $context
            );
        } else {
            return $this->inline->validateChildren(
                $children,
                $config,
                $context
            );
        }
    }
}

// vim: et sw=4 sts=4
