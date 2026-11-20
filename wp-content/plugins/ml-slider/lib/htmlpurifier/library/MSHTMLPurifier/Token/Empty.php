<?php

/**
 * Concrete empty token class.
 */
class MSHTMLPurifier_Token_Empty extends MSHTMLPurifier_Token_Tag
{
    public function toNode() {
        $n = parent::toNode();
        $n->empty = true;
        return $n;
    }
}

// vim: et sw=4 sts=4
