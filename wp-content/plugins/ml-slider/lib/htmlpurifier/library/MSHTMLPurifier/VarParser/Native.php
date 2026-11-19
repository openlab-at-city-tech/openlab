<?php

/**
 * This variable parser accepts the PHP literal subset used by the config
 * schema without executing arbitrary PHP code.
 */
class MSHTMLPurifier_VarParser_Native extends MSHTMLPurifier_VarParser
{

    /**
     * @param mixed $var
     * @param int $type
     * @param bool $allow_null
     * @return null|string
     */
    protected function parseImplementation($var, $type, $allow_null)
    {
        return $this->parseExpression($var);
    }

    /**
     * @param string $expr
     * @return mixed
     * @throws MSHTMLPurifier_VarParserException
     */
    protected function parseExpression($expr)
    {
        return self::parseLiteral($expr);
    }

    /**
     * Parses the PHP literal subset used by HTML Purifier's config schema.
     *
     * This intentionally accepts only scalar values and arrays, avoiding PHP
     * execution while preserving the native parser's schema-building behavior.
     *
     * @param string $expr
     * @return mixed
     * @throws MSHTMLPurifier_VarParserException
     */
    public static function parseLiteral($expr)
    {
        $parser = new MSHTMLPurifier_VarParser_Native_LiteralParser($expr);
        return $parser->parse();
    }
}

/**
 * Token parser for the PHP literal subset used by schema defaults.
 */
class MSHTMLPurifier_VarParser_Native_LiteralParser
{
    /**
     * @type array
     */
    protected $tokens = array();

    /**
     * @type int
     */
    protected $pos = 0;

    /**
     * @param string $expr
     */
    public function __construct($expr)
    {
        $raw_tokens = token_get_all('<?php ' . $expr . ';');
        foreach ($raw_tokens as $token) {
            if (is_array($token)) {
                if ($token[0] === T_OPEN_TAG || $token[0] === T_WHITESPACE) {
                    continue;
                }
                $this->tokens[] = $token;
            } else {
                $this->tokens[] = $token;
            }
        }
    }

    /**
     * @return mixed
     * @throws MSHTMLPurifier_VarParserException
     */
    public function parse()
    {
        $value = $this->parseValue();
        $token = $this->peek();
        if ($token !== ';' && $token !== null) {
            $this->error('Unexpected token after literal');
        }
        return $value;
    }

    /**
     * @return mixed
     * @throws MSHTMLPurifier_VarParserException
     */
    protected function parseValue()
    {
        $token = $this->next();
        if ($token === null) {
            $this->error('Unexpected end of literal');
        }

        if ($token === '-') {
            $value = $this->parseValue();
            if (is_int($value) || is_float($value)) {
                return -$value;
            }
            $this->error('Unexpected negative literal');
        }

        if ($this->isToken($token, T_ARRAY)) {
            $this->expect('(');
            return $this->parseArray(')');
        }

        if ($token === '[') {
            return $this->parseArray(']');
        }

        if ($this->isToken($token, T_CONSTANT_ENCAPSED_STRING)) {
            return stripcslashes(substr($token[1], 1, -1));
        }

        if ($this->isToken($token, T_LNUMBER)) {
            return intval($token[1], 0);
        }

        if ($this->isToken($token, T_DNUMBER)) {
            return (float) $token[1];
        }

        if ($this->isToken($token, T_STRING)) {
            $value = strtolower($token[1]);
            if ($value === 'true') {
                return true;
            }
            if ($value === 'false') {
                return false;
            }
            if ($value === 'null') {
                return null;
            }
        }

        $this->error('Unsupported literal value');
    }

    /**
     * @param string $end
     * @return array
     * @throws MSHTMLPurifier_VarParserException
     */
    protected function parseArray($end)
    {
        $array = array();
        $next_index = 0;

        while (($token = $this->peek()) !== null) {
            if ($token === $end) {
                $this->next();
                return $array;
            }

            $key_or_value = $this->parseValue();
            if ($this->isToken($this->peek(), T_DOUBLE_ARROW)) {
                $this->next();
                $array[$key_or_value] = $this->parseValue();
            } else {
                $array[$next_index++] = $key_or_value;
            }

            $token = $this->peek();
            if ($token === ',') {
                $this->next();
                continue;
            }
            if ($token === $end) {
                continue;
            }
            $this->error('Expected comma or array end');
        }

        $this->error('Unclosed array literal');
    }

    /**
     * @return mixed|null
     */
    protected function peek()
    {
        return isset($this->tokens[$this->pos]) ? $this->tokens[$this->pos] : null;
    }

    /**
     * @return mixed|null
     */
    protected function next()
    {
        $token = $this->peek();
        $this->pos++;
        return $token;
    }

    /**
     * @param string $expected
     * @throws MSHTMLPurifier_VarParserException
     */
    protected function expect($expected)
    {
        if ($this->next() !== $expected) {
            $this->error("Expected '$expected'");
        }
    }

    /**
     * @param mixed $token
     * @param int $type
     * @return bool
     */
    protected function isToken($token, $type)
    {
        return is_array($token) && $token[0] === $type;
    }

    /**
     * @param string $message
     * @throws MSHTMLPurifier_VarParserException
     */
    protected function error($message)
    {
        throw new MSHTMLPurifier_VarParserException($message);
    }
}

// vim: et sw=4 sts=4
