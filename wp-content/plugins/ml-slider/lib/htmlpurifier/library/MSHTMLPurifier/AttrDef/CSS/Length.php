<?php

/**
 * Represents a Length as defined by CSS.
 */
class MSHTMLPurifier_AttrDef_CSS_Length extends MSHTMLPurifier_AttrDef
{

    /**
     * @type MSHTMLPurifier_Length|string
     */
    protected $min;

    /**
     * @type MSHTMLPurifier_Length|string
     */
    protected $max;

    /**
     * @param MSHTMLPurifier_Length|string $min Minimum length, or null for no bound. String is also acceptable.
     * @param MSHTMLPurifier_Length|string $max Maximum length, or null for no bound. String is also acceptable.
     */
    public function __construct($min = null, $max = null)
    {
        $this->min = $min !== null ? MSHTMLPurifier_Length::make($min) : null;
        $this->max = $max !== null ? MSHTMLPurifier_Length::make($max) : null;
    }

    /**
     * @param string $string
     * @param MSHTMLPurifier_Config $config
     * @param MSHTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);

        // Optimizations
        if ($string === '') {
            return false;
        }
        if ($string === '0') {
            return '0';
        }
        if (strlen($string) === 1) {
            return false;
        }

        $length = MSHTMLPurifier_Length::make($string);
        if (!$length->isValid()) {
            return false;
        }

        if ($this->min) {
            $c = $length->compareTo($this->min);
            if ($c === false) {
                return false;
            }
            if ($c < 0) {
                return false;
            }
        }
        if ($this->max) {
            $c = $length->compareTo($this->max);
            if ($c === false) {
                return false;
            }
            if ($c > 0) {
                return false;
            }
        }
        return $length->toString();
    }
}

// vim: et sw=4 sts=4
