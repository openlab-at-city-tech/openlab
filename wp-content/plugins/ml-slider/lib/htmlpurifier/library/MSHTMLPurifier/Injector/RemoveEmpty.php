<?php

class MSHTMLPurifier_Injector_RemoveEmpty extends MSHTMLPurifier_Injector
{
    /**
     * @type MSHTMLPurifier_Context
     */
    private $context;

    /**
     * @type MSHTMLPurifier_Config
     */
    private $config;

    /**
     * @type MSHTMLPurifier_AttrValidator
     */
    private $attrValidator;

    /**
     * @type bool
     */
    private $removeNbsp;

    /**
     * @type bool
     */
    private $removeNbspExceptions;

    /**
     * Cached contents of %AutoFormat.RemoveEmpty.Predicate
     * @type array
     */
    private $exclude;

    /**
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return void
     */
    public function prepare($config, $context)
    {
        parent::prepare($config, $context);
        $this->config = $config;
        $this->context = $context;
        $this->removeNbsp = $config->get('AutoFormat.RemoveEmpty.RemoveNbsp');
        $this->removeNbspExceptions = $config->get('AutoFormat.RemoveEmpty.RemoveNbsp.Exceptions');
        $this->exclude = $config->get('AutoFormat.RemoveEmpty.Predicate');
        foreach ($this->exclude as $key => $attrs) {
            if (!is_array($attrs)) {
                // HACK, see MSHTMLPurifier/Printer/ConfigForm.php
                $this->exclude[$key] = explode(';', $attrs);
            }
        }
        $this->attrValidator = new MSHTMLPurifier_AttrValidator();
    }

    /**
     * @param MSHTMLPurifier_Token $token
     */
    public function handleElement(&$token)
    {
        if (!$token instanceof MSHTMLPurifier_Token_Start) {
            return;
        }
        $next = false;
        $deleted = 1; // the current tag
        for ($i = count($this->inputZipper->back) - 1; $i >= 0; $i--, $deleted++) {
            $next = $this->inputZipper->back[$i];
            if ($next instanceof MSHTMLPurifier_Token_Text) {
                if ($next->is_whitespace) {
                    continue;
                }
                if ($this->removeNbsp && !isset($this->removeNbspExceptions[$token->name])) {
                    $plain = str_replace("\xC2\xA0", "", $next->data);
                    $isWsOrNbsp = $plain === '' || ctype_space($plain);
                    if ($isWsOrNbsp) {
                        continue;
                    }
                }
            }
            break;
        }
        if (!$next || ($next instanceof MSHTMLPurifier_Token_End && $next->name == $token->name)) {
            $this->attrValidator->validateToken($token, $this->config, $this->context);
            $token->armor['ValidateAttributes'] = true;
            if (isset($this->exclude[$token->name])) {
                $r = true;
                foreach ($this->exclude[$token->name] as $elem) {
                    if (!isset($token->attr[$elem])) $r = false;
                }
                if ($r) return;
            }
            if (isset($token->attr['id']) || isset($token->attr['name'])) {
                return;
            }
            $token = $deleted + 1;
            for ($b = 0, $c = count($this->inputZipper->front); $b < $c; $b++) {
                $prev = $this->inputZipper->front[$b];
                if ($prev instanceof MSHTMLPurifier_Token_Text && $prev->is_whitespace) {
                    continue;
                }
                break;
            }
            // This is safe because we removed the token that triggered this.
            $this->rewindOffset($b+$deleted);
            return;
        }
    }
}

// vim: et sw=4 sts=4
