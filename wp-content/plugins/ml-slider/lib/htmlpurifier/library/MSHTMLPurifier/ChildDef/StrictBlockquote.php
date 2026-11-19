<?php

/**
 * Takes the contents of blockquote when in strict and reformats for validation.
 */
class MSHTMLPurifier_ChildDef_StrictBlockquote extends MSHTMLPurifier_ChildDef_Required
{
    /**
     * @type array
     */
    protected $real_elements;

    /**
     * @type array
     */
    protected $fake_elements;

    /**
     * @type bool
     */
    public $allow_empty = true;

    /**
     * @type string
     */
    public $type = 'strictblockquote';

    /**
     * @type bool
     */
    protected $init = false;

    /**
     * @param MSHTMLPurifier_Config $config
     * @return array
     * @note We don't want MakeWellFormed to auto-close inline elements since
     *       they might be allowed.
     */
    public function getAllowedElements($config)
    {
        $this->init($config);
        return $this->fake_elements;
    }

    /**
     * @param array $children
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return array
     */
    public function validateChildren($children, $config, $context)
    {
        $this->init($config);

        // trick the parent class into thinking it allows more
        $this->elements = $this->fake_elements;
        $result = parent::validateChildren($children, $config, $context);
        $this->elements = $this->real_elements;

        if ($result === false) {
            return array();
        }
        if ($result === true) {
            $result = $children;
        }

        $def = $config->getHTMLDefinition();
        $block_wrap_name = $def->info_block_wrapper;
        $block_wrap = false;
        $ret = array();

        foreach ($result as $node) {
            if ($block_wrap === false) {
                if (($node instanceof MSHTMLPurifier_Node_Text && !$node->is_whitespace) ||
                    ($node instanceof MSHTMLPurifier_Node_Element && !isset($this->elements[$node->name]))) {
                        $block_wrap = new MSHTMLPurifier_Node_Element($def->info_block_wrapper);
                        $ret[] = $block_wrap;
                }
            } else {
                if ($node instanceof MSHTMLPurifier_Node_Element && isset($this->elements[$node->name])) {
                    $block_wrap = false;

                }
            }
            if ($block_wrap) {
                $block_wrap->children[] = $node;
            } else {
                $ret[] = $node;
            }
        }
        return $ret;
    }

    /**
     * @param MSHTMLPurifier_Config $config
     */
    private function init($config)
    {
        if (!$this->init) {
            $def = $config->getHTMLDefinition();
            // allow all inline elements
            $this->real_elements = $this->elements;
            $this->fake_elements = $def->info_content_sets['Flow'];
            $this->fake_elements['#PCDATA'] = true;
            $this->init = true;
        }
    }
}

// vim: et sw=4 sts=4
