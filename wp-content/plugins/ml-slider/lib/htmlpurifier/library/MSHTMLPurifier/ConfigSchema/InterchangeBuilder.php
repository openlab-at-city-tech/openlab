<?php

class MSHTMLPurifier_ConfigSchema_InterchangeBuilder
{

    /**
     * Used for processing DEFAULT, nothing else.
     * @type MSHTMLPurifier_VarParser
     */
    protected $varParser;

    /**
     * @param MSHTMLPurifier_VarParser $varParser
     */
    public function __construct($varParser = null)
    {
        $this->varParser = $varParser ? $varParser : new MSHTMLPurifier_VarParser_Native();
    }

    /**
     * @param string $dir
     * @return MSHTMLPurifier_ConfigSchema_Interchange
     */
    public static function buildFromDirectory($dir = null)
    {
        $builder = new MSHTMLPurifier_ConfigSchema_InterchangeBuilder();
        $interchange = new MSHTMLPurifier_ConfigSchema_Interchange();
        return $builder->buildDir($interchange, $dir);
    }

    /**
     * @param MSHTMLPurifier_ConfigSchema_Interchange $interchange
     * @param string $dir
     * @return MSHTMLPurifier_ConfigSchema_Interchange
     */
    public function buildDir($interchange, $dir = null)
    {
        if (!$dir) {
            $dir = MSHTMLPURIFIER_PREFIX . '/MSHTMLPurifier/ConfigSchema/schema';
        }
        if (file_exists($dir . '/info.ini')) {
            $info = parse_ini_file($dir . '/info.ini');
            $interchange->name = $info['name'];
        }

        $files = array();
        $dh = opendir($dir);
        while (false !== ($file = readdir($dh))) {
            if (!$file || $file[0] == '.' || strrchr($file, '.') !== '.txt') {
                continue;
            }
            $files[] = $file;
        }
        closedir($dh);

        sort($files);
        foreach ($files as $file) {
            $this->buildFile($interchange, $dir . '/' . $file);
        }
        return $interchange;
    }

    /**
     * @param MSHTMLPurifier_ConfigSchema_Interchange $interchange
     * @param string $file
     */
    public function buildFile($interchange, $file)
    {
        $parser = new MSHTMLPurifier_StringHashParser();
        $this->build(
            $interchange,
            new MSHTMLPurifier_StringHash($parser->parseFile($file))
        );
    }

    /**
     * Builds an interchange object based on a hash.
     * @param MSHTMLPurifier_ConfigSchema_Interchange $interchange MSHTMLPurifier_ConfigSchema_Interchange object to build
     * @param MSHTMLPurifier_StringHash $hash source data
     * @throws MSHTMLPurifier_ConfigSchema_Exception
     */
    public function build($interchange, $hash)
    {
        if (!$hash instanceof MSHTMLPurifier_StringHash) {
            $hash = new MSHTMLPurifier_StringHash($hash);
        }
        if (!isset($hash['ID'])) {
            throw new MSHTMLPurifier_ConfigSchema_Exception('Hash does not have any ID');
        }
        if (strpos($hash['ID'], '.') === false) {
            if (count($hash) == 2 && isset($hash['DESCRIPTION'])) {
                $hash->offsetGet('DESCRIPTION'); // prevent complaining
            } else {
                throw new MSHTMLPurifier_ConfigSchema_Exception('All directives must have a namespace');
            }
        } else {
            $this->buildDirective($interchange, $hash);
        }
        $this->_findUnused($hash);
    }

    /**
     * @param MSHTMLPurifier_ConfigSchema_Interchange $interchange
     * @param MSHTMLPurifier_StringHash $hash
     * @throws MSHTMLPurifier_ConfigSchema_Exception
     */
    public function buildDirective($interchange, $hash)
    {
        $directive = new MSHTMLPurifier_ConfigSchema_Interchange_Directive();

        // These are required elements:
        $directive->id = $this->id($hash->offsetGet('ID'));
        $id = $directive->id->toString(); // convenience

        if (isset($hash['TYPE'])) {
            $type = explode('/', $hash->offsetGet('TYPE'));
            if (isset($type[1])) {
                $directive->typeAllowsNull = true;
            }
            $directive->type = $type[0];
        } else {
            throw new MSHTMLPurifier_ConfigSchema_Exception("TYPE in directive hash '$id' not defined");
        }

        if (isset($hash['DEFAULT'])) {
            try {
                $directive->default = $this->varParser->parse(
                    $hash->offsetGet('DEFAULT'),
                    $directive->type,
                    $directive->typeAllowsNull
                );
            } catch (MSHTMLPurifier_VarParserException $e) {
                throw new MSHTMLPurifier_ConfigSchema_Exception($e->getMessage() . " in DEFAULT in directive hash '$id'");
            }
        }

        if (isset($hash['DESCRIPTION'])) {
            $directive->description = $hash->offsetGet('DESCRIPTION');
        }

        if (isset($hash['ALLOWED'])) {
            $directive->allowed = $this->lookup($this->parseArrayLiteral($hash->offsetGet('ALLOWED')));
        }

        if (isset($hash['VALUE-ALIASES'])) {
            $directive->valueAliases = $this->parseArrayLiteral($hash->offsetGet('VALUE-ALIASES'));
        }

        if (isset($hash['ALIASES'])) {
            $raw_aliases = trim($hash->offsetGet('ALIASES'));
            $aliases = preg_split('/\s*,\s*/', $raw_aliases);
            foreach ($aliases as $alias) {
                $directive->aliases[] = $this->id($alias);
            }
        }

        if (isset($hash['VERSION'])) {
            $directive->version = $hash->offsetGet('VERSION');
        }

        if (isset($hash['DEPRECATED-USE'])) {
            $directive->deprecatedUse = $this->id($hash->offsetGet('DEPRECATED-USE'));
        }

        if (isset($hash['DEPRECATED-VERSION'])) {
            $directive->deprecatedVersion = $hash->offsetGet('DEPRECATED-VERSION');
        }

        if (isset($hash['EXTERNAL'])) {
            $directive->external = preg_split('/\s*,\s*/', trim($hash->offsetGet('EXTERNAL')));
        }

        $interchange->addDirective($directive);
    }

    /**
     * Parses an array PHP literal string without array() wrapper
     * @param string $contents
     * @return array
     * @throws MSHTMLPurifier_ConfigSchema_Exception
     */
    protected function parseArrayLiteral($contents)
    {
        try {
            $array = MSHTMLPurifier_VarParser_Native::parseLiteral('array(' . $contents . ')');
        } catch (MSHTMLPurifier_VarParserException $e) {
            throw new MSHTMLPurifier_ConfigSchema_Exception($e->getMessage());
        }
        if (!is_array($array)) {
            throw new MSHTMLPurifier_ConfigSchema_Exception('Expected array literal');
        }
        return $array;
    }

    /**
     * Converts an array list into a lookup array.
     * @param array $array
     * @return array
     */
    protected function lookup($array)
    {
        $ret = array();
        foreach ($array as $val) {
            $ret[$val] = true;
        }
        return $ret;
    }

    /**
     * Convenience function that creates an MSHTMLPurifier_ConfigSchema_Interchange_Id
     * object based on a string Id.
     * @param string $id
     * @return MSHTMLPurifier_ConfigSchema_Interchange_Id
     */
    protected function id($id)
    {
        return MSHTMLPurifier_ConfigSchema_Interchange_Id::make($id);
    }

    /**
     * Triggers errors for any unused keys passed in the hash; such keys
     * may indicate typos, missing values, etc.
     * @param MSHTMLPurifier_StringHash $hash Hash to check.
     */
    protected function _findUnused($hash)
    {
        $accessed = $hash->getAccessed();
        foreach ($hash as $k => $v) {
            if (!isset($accessed[$k])) {
                trigger_error("String hash key '$k' not used by builder", E_USER_NOTICE);
            }
        }
    }
}

// vim: et sw=4 sts=4
