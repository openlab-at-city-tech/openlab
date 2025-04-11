<?php
namespace TranslatePress;

if ( !defined('ABSPATH' ) )
    exit();

class Uri
{
    const SCHEMES_WITH_AUTHORITY = ';http;https;ftp';
    /** @var string */
    private $scheme;
    /** @var string */
    private $host;
    /** @var string */
    private $user;
    /** @var string */
    private $pass;
    /** @var string */
    private $path;
    /** @var string */
    private $query;
    /** @var string */
    private $fragment;
    /** @var int */
    private $port;
    /** @var bool */
    private $absolute = true;

    /**
     * If $uri is set, we'll hydrate this object with it
     *
     * @param string $uri {optional}
     */
    public function __construct($uri = null)
    {
        if ($uri !== null) {
            $this->fromString($uri);
        }
    }

    /**
     * Alias for getUri.
     * @return string
     */
    public function __toString()
    {
        return $this->getUri();
    }

    /**
     * Hydrate this object with values from a string
     * @param $uri
     * @return self
     */
    public function fromString($uri)
    {
        if (is_numeric($uri)) { //Could be a valid url
            $uri = '' . $uri;
        }
        if (!is_string($uri)) {
            $uri = '';
        }
        $this->setRelative();
        if (0 === strpos($uri, '//')) {
            $this->setAbsolute();
        }
        $parsed_url = parse_url($uri);
        if (!$parsed_url) {
            return $this;
        }
        if (array_key_exists('scheme', $parsed_url)) {
            $this->setAbsolute();
        }
        foreach ($parsed_url as $urlPart => $value) {
            $method = 'set' . ucfirst($urlPart);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Get the URI from the set parameters
     * @return string
     */
    public function getUri()
    {
        $userPart = '';
        if ($this->getUser() !== null && $this->getPass() !== null) {
            $userPart = $this->getUser() . ':' . $this->getPass()  . '@';
        } else if ($this->getUser() !== null) {
            $userPart = $this->getUser() . '@';
        }
        $schemePart = ($this->getScheme() ? $this->getScheme() . '://' : '//');
        if (!in_array($this->getScheme(), self::getSchemesWithAuthority())) {
            $schemePart = $this->getScheme() . ':';
        }
        $portPart = ($this->getPort() ? ':' . $this->getPort() : '');
        $queryPart = ($this->getQuery() ? '?' . $this->getQuery() : '');
        $fragmentPart = ($this->getFragment() ? '#' . $this->getFragment() : '');
        if ($this->isRelative()) {
            return $this->getPath() .
                $queryPart .
                $fragmentPart;
        }
        $path = $this->getPath();
        if (0 !== strlen($path) && '/' !== $path[0]) {
            $path = '/' . $path;
        }
        return $schemePart .
            $userPart .
            $this->getHost() .
            $portPart .
            $path .
            $queryPart .
            $fragmentPart;
    }

    /**
     * @param string $fragment
     * @return self
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
        return $this;
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param string $host
     * @return self
     */
    public function setHost($host)
    {
        $this->host = $host;
        $this->setAbsolute();
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $pass
     * @return self
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param string $path
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the query. Must be a string, and the prepending "?" will be trimmed.
     * Example: ?a=b&c[]=123 -> "a=b&c[]=123"
     * @see Sensimity_Helper_UriTest::provideSetQuery
     *
     * @param string $query
     * @return self
     */
    public function setQuery($query)
    {
        $this->query = null;
        if (is_string($query)) {
            $this->query = ltrim($query, '?');
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the scheme. If its empty, it will be set to null.
     *
     * Must be a string. Can only contain "a-z A-Z 0-9 . : -".
     * Will be forced to lowercase.
     * Appended : or // will be removed.
     * @see Sensimity_Helper_UriTest::provideSetScheme
     *
     * @param string $scheme
     * @return self
     */
    public function setScheme($scheme)
    {
        $this->scheme = null;
        if (empty($scheme) || null === $scheme) {
            return $this;
        }
        $scheme = preg_replace('/[^a-zA-Z0-9\.\:\-]/', '', $scheme);
        $scheme = strtolower($scheme);
        $scheme = rtrim($scheme, ':/');
        $scheme = trim($scheme, ':/');
        $scheme = str_replace('::', ':', $scheme);
        if (strlen($scheme) != 0) {
            if ($this->isRelative()) {
                /* Explained: */
                /* @see Sensimity_Helper_UriTest::testRelativeAbsoluteUrls */
                $exp = explode('/', ltrim($this->getPath(), '/'));
                $this->setHost($exp[0]);
                unset($exp[0]);
                $this->setPath(null);
                $path = implode('/', $exp);
                if (strlen($path) > 0) {
                    //Only create the "/" if theres a path
                    $this->setPath('/' . $path);
                }
                $this->setAbsolute();
            }
            $this->scheme = $scheme;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param string $user
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Port must be a valid number. Otherwise it will be set to NULL. (default scheme port)
     * @see Sensimity_Helper_UriTest::provideSetPort
     *
     * @param int|string $port
     * @return self
     */
    public function setPort($port)
    {
        $this->port = null;
        if ((is_string($port) || is_numeric($port)) && ctype_digit(strval($port))) {
            $this->port = (int) $port;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return bool
     */
    public function isRelative()
    {
        return (!$this->absolute);
    }

    /**
     * @return bool
     */
    public function isAbsolute()
    {
        return ($this->absolute);
    }

    /**
     * @return $this
     */
    public function setAbsolute()
    {
        $this->absolute = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function setRelative()
    {
        $this->absolute = false;
        return $this;
    }

    /** Some helpful static functions */

    /**
     * @param $uri
     * @param null $scheme
     * @return string
     */
    public static function changeScheme($uri, $scheme = null)
    {
        if ($scheme == null) { //null for scheme = just no change at all - only in this static function, for BC!
            return $uri;
        }
        $class = get_called_class();
        $uri = new $class($uri);
        $uri->setScheme($scheme);
        return $uri->getUri();
    }

    /**
     * @see http://tools.ietf.org/html/rfc3986#section-3
     * @return array
     */
    public static function getSchemesWithAuthority()
    {
        return explode(';', self::SCHEMES_WITH_AUTHORITY);
    }

    /**
     * @return bool
     */
    public function isSchemeless()
    {
        $scheme = $this->getScheme();
        return (bool) ($this->isRelative() || ($this->isAbsolute() && empty($scheme)));
    }

    public function hasAnchor(){
        return (bool) isset( $this->fragment );
    }

    public function hasQueryParam(){
        return (bool) isset( $this->query );
    }
}
