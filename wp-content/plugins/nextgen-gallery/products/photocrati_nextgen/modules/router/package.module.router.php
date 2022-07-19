<?php
/**
 * Class A_Routing_App_Factory
 * @mixin C_Component_Factory
 * @adapts I_Component_Factory
 */
class A_Routing_App_Factory extends Mixin
{
    function routing_app($context = FALSE, $router = FALSE)
    {
        return new C_Routing_App($context, $router);
    }
}
/**
 * Class C_Http_Response_Controller
 * @mixin Mixin_Http_Response_Actions
 * @implements I_Http_Response
 */
class C_Http_Response_Controller extends C_Component
{
    /** @var C_Http_Response_Controller */
    static $_instance = NULL;
    /**
     * @param bool|string $context
     * @return C_Http_Response_Controller
     */
    static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instance)) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
    function define($context = FALSE)
    {
        $this->add_mixin('Mixin_Http_Response_Actions');
        $this->implement('I_Http_Response');
    }
}
class Mixin_Http_Response_Actions extends Mixin
{
    function http_301_action()
    {
        header('HTTP/1.1 301 Permanent Redirect');
        header("Location: {$this->object->get_routed_url()}");
    }
    function http_302_action()
    {
        header('HTTP/1.1 302 Temporary Redirect');
        header("Location: {$this->object->get_routed_url()}");
    }
    function http_501_action()
    {
        header('HTTP/1.1 501 Not Implemented');
    }
    function http_404_action()
    {
        header('HTTP/1.1 404 Not found');
    }
    function http_202_action()
    {
        header('HTTP/1.1 202 Accepted');
    }
}
class Mixin_Router extends Mixin
{
    function set_routed_app($app)
    {
        $this->object->_routed_app = $app;
    }
    function &get_routed_app()
    {
        $retval = $this->object->_routed_app ? $this->object->_routed_app : $this->object->get_default_app();
        return $retval;
    }
    function &get_default_app()
    {
        if (is_null($this->object->_default_app)) {
            $this->object->_default_app = $this->object->create_app();
        }
        $retval = $this->object->_default_app;
        return $retval;
    }
    function route($patterns, $handler = FALSE)
    {
        $this->object->get_default_app()->route($patterns, $handler);
    }
    function rewrite($src, $dst, $redirect = FALSE)
    {
        $this->object->get_default_app()->rewrite($src, $dst, $redirect);
    }
    function get_parameter($key, $prefix = NULL, $default = NULL)
    {
        return $this->object->get_routed_app()->get_parameter($key, $prefix, $default);
    }
    function param($key, $prefix = NULL, $default = NULL)
    {
        return $this->object->get_parameter($key, $prefix, $default);
    }
    function has_parameter_segments()
    {
        return $this->object->get_routed_app()->has_parameter_segments();
    }
    function passthru()
    {
        return $this->object->get_default_app()->passthru();
    }
    /**
     * Gets url for the router
     * @param string $uri (optional) Default = /
     * @param bool $with_qs (optional) Default = true
     * @param bool $site_url
     * @return string
     */
    function get_url($uri = '/', $with_qs = TRUE, $site_url = FALSE)
    {
        $retval = $this->object->join_paths($this->object->get_base_url($site_url), $uri);
        if ($with_qs) {
            $parts = parse_url($retval);
            if (!isset($parts['query'])) {
                $parts['query'] = $this->object->get_querystring();
            } else {
                $parts['query'] = $this->object->join_querystrings($parts['query'], $this->object->get_querystring());
            }
            $retval = $this->object->construct_url_from_parts($parts);
        }
        return str_replace("\\", "/", $retval);
    }
    /**
     * Currents the relative url
     * @param string $uri
     * @param boolean $with_qs
     * @return string
     */
    function get_relative_url($uri = '/', $with_qs = TRUE)
    {
        $url = $this->object->get_url($uri, $with_qs = TRUE);
        if ($url !== '/') {
            $retval = str_replace($this->object->get_base_url(), '', $url);
        }
        return '/' . lrtim($retval, '/');
    }
    /**
     * Returns a static url
     * @param string $path
     * @param string|false $module (optional)
     * @return string
     */
    function get_static_url($path, $module = FALSE)
    {
        return M_Static_Assets::get_static_url($path, $module);
    }
    /**
     * Gets the routed url
     * @return string
     */
    function get_routed_url()
    {
        $retval = $this->object->get_url($this->object->get_request_uri());
        if ($app = $this->object->get_routed_app()) {
            $retval = $this->object->get_url($app->get_app_uri());
        }
        return $retval;
    }
    /**
     * Gets the base url for the router
     *
     * @param bool $site_url Unused
     * @return string
     */
    function get_base_url($site_url = FALSE)
    {
        $protocol = $this->object->is_https() ? 'https://' : 'http://';
        $retval = "{$protocol}{$_SERVER['SERVER_NAME']}{$this->object->context}";
        return str_replace("\\", "/", rtrim($retval, "/\\"));
    }
    /**
     * Determines if the current request is over HTTPs or not
     */
    function is_https()
    {
        return !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' || !empty($_SERVER['HTTP_USESSL']) && strtolower($_SERVER['HTTP_USESSL']) !== 'off' || !empty($_SERVER['REDIRECT_HTTPS']) && strtolower($_SERVER['REDIRECT_HTTPS']) !== 'off' || !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443;
    }
    /**
     * Serve request using defined Routing Apps
     */
    function serve_request()
    {
        $served = FALSE;
        // iterate over all apps, and serve the route
        foreach ($this->object->get_apps() as $app) {
            if ($served = $app->serve_request($this->object->context)) {
                break;
            }
        }
        return $served;
    }
    /**
     * Gets the querystring of the current request
     * @return null|bool
     */
    function get_querystring()
    {
        return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
    }
    function set_querystring($value)
    {
        $_SERVER['QUERY_STRING'] = $value;
    }
    /**
     * Gets the request for the router
     * @param bool $with_params (optional) Default = true
     * @return string
     */
    function get_request_uri($with_params = TRUE)
    {
        if (!empty($_SERVER['NGG_ORIG_REQUEST_URI'])) {
            $retval = $_SERVER['NGG_ORIG_REQUEST_URI'];
        } elseif (!empty($_SERVER['PATH_INFO'])) {
            $retval = $_SERVER['PATH_INFO'];
        } else {
            $retval = $_SERVER['REQUEST_URI'];
        }
        // Remove the querystring
        if (($index = strpos($retval, '?')) !== FALSE) {
            $retval = substr($retval, 0, $index);
        }
        // Remove the router's context
        $retval = preg_replace('#^' . preg_quote($this->object->context, '#') . '#', '', $retval);
        // Remove the params
        if (!$with_params) {
            $retval = $this->object->strip_param_segments($retval);
        }
        // Ensure that request uri starts with a slash
        if (strpos($retval, '/') !== 0) {
            $retval = "/{$retval}";
        }
        return $retval;
    }
    /**
     * Gets the method of the HTTP request
     * @return string
     */
    function get_request_method()
    {
        return $this->object->_request_method;
    }
    function &create_app($name = '/')
    {
        $factory = C_Component_Factory::get_instance();
        $app = $factory->create('routing_app', $name);
        $this->object->_apps[] = $app;
        return $app;
    }
    /**
     * Gets a list of apps registered for the router
     *
     * @return array
     */
    function get_apps()
    {
        usort($this->object->_apps, array(&$this, '_sort_apps'));
        return array_reverse($this->object->_apps);
    }
    /**
     * Sorts apps.This is needed because we want the most specific app to be
     * executed first
     * @param C_Routing_App $a
     * @param C_Routing_App $b
     * @return int
     */
    function _sort_apps($a, $b)
    {
        return strnatcmp($a->context, $b->context);
    }
}
/**
 * A router is configured to match patterns against a url and route the request to a particular controller and action
 * @mixin Mixin_Url_Manipulation
 * @mixin Mixin_Router
 * @implements I_Router
 */
class C_Router extends C_Component
{
    static $_instances = array();
    var $_apps = array();
    var $_default_app = NULL;
    function define($context = FALSE)
    {
        if (!$context or $context == 'all') {
            $context = '/';
        }
        parent::define($context);
        $this->add_mixin('Mixin_Url_Manipulation');
        $this->add_mixin('Mixin_Router');
        $this->implement('I_Router');
    }
    function initialize()
    {
        parent::initialize();
        $this->_request_method = !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
    }
    /**
     * @param string|false $context (optional)
     * @return self
     */
    static function &get_instance($context = False)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
}
class Mixin_Routing_App extends Mixin
{
    /**
     * Creates a new route endpoint with the assigned handler
     *
     * @param array $routes URL to route, eg /page/{page}/
     * @param array $handler Formatted array
     */
    function route($routes, $handler)
    {
        // ensure that the routing patterns array exists
        if (!is_array($this->object->_routing_patterns)) {
            $this->object->_routing_patterns = array();
        }
        if (!is_array($routes)) {
            $routes = array($routes);
        }
        // fetch all routing patterns
        $patterns = $this->object->_routing_patterns;
        foreach ($routes as $route) {
            // add the routing pattern
            $patterns[$this->object->_route_to_regex($route)] = $handler;
        }
        // update routing patterns
        $this->object->_routing_patterns = $patterns;
    }
    /**
     * Handles internal url rewriting with optional HTTP redirection,
     *
     * @param string $src Original URL
     * @param string $dst Destination URL
     * @param bool $redirect FALSE for internal handling, otherwise the HTTP code to send
     * @param bool $stop
     */
    function rewrite($src, $dst, $redirect = FALSE, $stop = FALSE)
    {
        // ensure that rewrite patterns array exists
        if (!is_array($this->object->_rewrite_patterns)) {
            $this->object->_rewrite_patterns = array();
        }
        // fetch all rewrite patterns
        $patterns = $this->object->_rewrite_patterns;
        // Assign rewrite definition
        $definition = array('dst' => $dst, 'redirect' => $redirect, 'stop' => $stop);
        // We treat wildcards much differently than normal rewrites
        if (preg_match("/\\{[\\.\\\\*]/", $src)) {
            $pattern = str_replace('{*}', '(.*?)', $src);
            $pattern = str_replace('{.*}', '(.*?)', $pattern);
            $pattern = str_replace('{\\w}', '([^/]*)', $pattern);
            $pattern = str_replace('{\\d}', '(\\d*)', $pattern);
            $src = '#' . (strpos($src, '/') === 0 ? '^' : '') . $pattern . '/?$#';
            $definition['wildcards'] = TRUE;
        } else {
            $src = $this->object->_route_to_regex($src);
        }
        // add the rewrite pattern
        $patterns[$src] = $definition;
        // update rewrite patterns;
        $this->object->_rewrite_patterns = $patterns;
    }
    /**
     * Gets an instance of the router
     * @return C_Router
     */
    function get_router()
    {
        return C_Router::get_instance();
    }
    function get_app_url($request_uri = FALSE, $with_qs = FALSE)
    {
        return $this->object->get_router()->get_url($this->object->get_app_uri($request_uri), $with_qs);
    }
    function get_routed_url($with_qs = TRUE)
    {
        return $this->object->get_app_url(FALSE, $with_qs);
    }
    function get_app_uri($request_uri = FALSE)
    {
        if (!$request_uri) {
            $request_uri = $this->object->get_app_request_uri();
        }
        return $this->object->join_paths($this->object->context, $request_uri);
    }
    function get_app_request_uri()
    {
        $retval = FALSE;
        if ($this->object->_request_uri) {
            $retval = $this->object->_request_uri;
        } else {
            if ($retval = $this->object->does_app_serve_request()) {
                if (strpos($retval, '/') !== 0) {
                    $retval = '/' . $retval;
                }
                $this->object->set_app_request_uri($retval);
            }
        }
        return $retval;
    }
    /**
     * Sets the application request uri
     * @param string $uri
     */
    function set_app_request_uri($uri)
    {
        $this->object->_request_uri = $uri;
    }
    /**
     * Gets the application's routing regex pattern
     * @return string
     */
    function get_app_routing_pattern()
    {
        return $this->object->_route_to_regex($this->object->context);
    }
    /**
     * Determines whether this app serves the request
     * @return boolean|string
     */
    function does_app_serve_request()
    {
        $retval = FALSE;
        $request_uri = $this->object->get_router()->get_request_uri(TRUE);
        // Is the context present in the uri?
        if (($index = strpos($request_uri, $this->object->context)) !== FALSE) {
            $starts_with_slash = strpos($this->object->context, '/') === 0;
            if ($starts_with_slash && $index === 0 or !$starts_with_slash) {
                $regex = implode('', array('#', $starts_with_slash ? '^' : '', preg_quote($this->object->context, '#'), '#'));
                $retval = preg_replace($regex, '', $request_uri);
                if (!$retval) {
                    $retval = '/';
                }
                if (strpos($retval, '/') !== 0) {
                    $retval = '/' . $retval;
                }
                if (substr($retval, -1) != '/') {
                    $retval = $retval . '/';
                }
            }
        }
        return $retval;
    }
    /**
     * Performs the url rewriting routines. Returns the HTTP status code used to
     * redirect, if we're to do so. Otherwise FALSE
     * @return int|bool
     */
    function do_rewrites($request_uri = FALSE)
    {
        $redirect = FALSE;
        static $stop_processing = FALSE;
        // Get the request uri if not provided, if provided decode it
        if (!$request_uri) {
            $request_uri = $this->object->get_app_request_uri();
        } else {
            $request_uri = urldecode($request_uri);
        }
        // ensure that rewrite patterns array exists
        if (!is_array($this->object->_rewrite_patterns)) {
            $this->object->_rewrite_patterns = array();
        }
        // Process each rewrite rule
        // start rewriting urls
        if (!$stop_processing) {
            foreach ($this->object->_rewrite_patterns as $pattern => $details) {
                // Remove this pattern from future processing for this request
                unset($this->object->_rewrite_patterns[$pattern]);
                // Wildcards are processed much differently
                if (isset($details['wildcards']) && $details['wildcards']) {
                    if (preg_match($pattern, $request_uri, $matches)) {
                        foreach ($matches as $index => $match) {
                            if ($index == 0) {
                                $request_uri = str_replace($match, $details['dst'], $request_uri);
                            }
                            if ($index > 0) {
                                $request_uri = str_replace("{{$index}}", $match, $request_uri);
                            }
                        }
                        // Set the redirect flag if we're to do so
                        if (isset($details['redirect']) && $details['redirect']) {
                            $redirect = $details['redirect'] === TRUE ? 302 : intval($details['redirect']);
                            break;
                        }
                        // Stop processing rewrite patterns?
                        if ($details['stop']) {
                            $stop_processing = TRUE;
                        }
                    }
                } elseif (preg_match_all($pattern, $request_uri, $matches, PREG_SET_ORDER)) {
                    // Assign new request URI
                    $request_uri = $details['dst'];
                    // Substitute placeholders
                    foreach ($matches as $match) {
                        if ($redirect) {
                            break;
                        }
                        foreach ($match as $key => $val) {
                            // If we have a placeholder that needs swapped, swap
                            // it now
                            if (is_numeric($key)) {
                                continue;
                            }
                            $request_uri = str_replace("{{$key}}", $val, $request_uri);
                        }
                        // Set the redirect flag if we're to do so
                        if (isset($details['redirect']) && $details['redirect']) {
                            $redirect = $details['redirect'] === TRUE ? 302 : intval($details['redirect']);
                            break;
                        }
                    }
                }
                if ($stop_processing) {
                    break;
                }
            }
        }
        // Cache all known data about the application request
        $this->object->set_app_request_uri($request_uri);
        $this->object->get_router()->set_routed_app($this->object);
        return $redirect;
    }
    /**
     * Determines if the current routing app meets our requirements and serves them
     *
     * @return bool
     */
    function serve_request()
    {
        $served = FALSE;
        // ensure that the routing patterns array exists
        if (!is_array($this->object->_routing_patterns)) {
            $this->object->_routing_patterns = array();
        }
        // if the application root matches, then we'll try to route the request
        if ($request_uri = $this->object->get_app_request_uri()) {
            // Perform URL rewrites
            $redirect = $this->object->do_rewrites($request_uri);
            // Are we to perform a redirect?
            if ($redirect) {
                $this->object->execute_route_handler($this->object->parse_route_handler($redirect));
            } else {
                foreach ($this->object->_routing_patterns as $pattern => $handler) {
                    if (preg_match($pattern, $this->object->get_app_request_uri(), $matches)) {
                        $served = TRUE;
                        // Add placeholder parameters
                        foreach ($matches as $key => $value) {
                            if (is_numeric($key)) {
                                continue;
                            }
                            $this->object->set_parameter_value($key, $value, NULL);
                        }
                        // If a handler is attached to the route, execute it. A
                        // handler can be
                        // - FALSE, meaning don't do any post-processing to the route
                        // - A string, such as controller#action
                        // - An array: array(
                        //   'controller' => 'I_Test_Controller',
                        //   'action'	  => 'index',
                        //   'context'	  => 'all', (optional)
                        //   'method'	  => array('GET') (optional)
                        // )
                        if ($handler && ($handler = $this->object->parse_route_handler($handler))) {
                            // Is this handler for the current HTTP request method?
                            if (isset($handler['method'])) {
                                if (!is_array($handler['method'])) {
                                    $handler['$method'] = array($handler['method']);
                                }
                                if (in_array($this->object->get_router()->get_request_method(), $handler['method'])) {
                                    $this->object->execute_route_handler($handler);
                                }
                            } else {
                                $this->object->execute_route_handler($handler);
                            }
                        } else {
                            if (!$handler) {
                                $this->object->passthru();
                            }
                        }
                    }
                }
            }
        }
        return $served;
    }
    /**
     * Executes an action of a particular controller
     * @param array $handler
     */
    function execute_route_handler($handler)
    {
        // Get action
        $action = $handler['action'];
        // Get controller
        $controller = $this->object->get_registry()->get_utility($handler['controller'], $handler['context']);
        // Call action
        $controller->{$action}();
        // Clean Exit (fastcgi safe)
        C_NextGEN_Bootstrap::shutdown();
    }
    /**
     * Parses the route handler
     * @param mixed $handler
     * @return array
     */
    function parse_route_handler($handler)
    {
        if (is_string($handler)) {
            $handler = array_combine(array('controller', 'action'), explode('#', $handler));
        } elseif (is_numeric($handler)) {
            $handler = array('controller' => 'I_Http_Response', 'action' => 'http_' . $handler);
        }
        if (!isset($handler['context'])) {
            $handler['context'] = FALSE;
        }
        if (strpos($handler['action'], '_action') === FALSE) {
            $handler['action'] .= '_action';
        }
        return $handler;
    }
    function add_placeholder_params_from_matches($matches)
    {
        // Add the placeholder parameter values to the _params array
        foreach ($matches as $key => $value) {
            if (is_numeric($key)) {
                continue;
            }
            $this->object->add_placeholder_param($key, $value, $matches[0]);
        }
    }
    /**
     * Used to pass execution to PHP and perhaps an above framework
     */
    function passthru()
    {
    }
    /**
     * Adds a placeholder parameter
     * @param string $name
     * @param string $value
     * @param string $source
     */
    function add_placeholder_param($name, $value, $source = NULL)
    {
        if (!is_array($this->object->_parameters)) {
            $this->object->_parameters = array('global');
        }
        if (!isset($this->object->_parameters['global'])) {
            $this->object->_parameters['global'] = array();
        }
        $this->object->_parameters['global'][] = array('id' => '', 'name' => $name, 'value' => $value, 'source' => $source);
    }
    /**
     * Converts the route to the regex
     *
     * @param string $route
     * @return string
     */
    function _route_to_regex($route)
    {
        // Get the settings manager
        $settings = $this->object->_settings;
        $param_slug = $settings->router_param_slug;
        // convert route to RegEx pattern
        $route_regex = preg_quote(str_replace(array('{', '}'), array('~', '~'), $route), '#');
        // Wrap the route
        $route_regex = '(' . $route_regex . ')';
        // If the route starts with a slash, then it must appear at the beginning
        // of a request uri
        if (strpos($route, '/') === 0) {
            $route_regex = '^' . $route_regex;
        }
        // If the route is not /, and perhaps /foo, then we need to optionally
        // look for a trailing slash as well
        if ($route != '/') {
            $route_regex .= '/?';
        }
        // If parameters come after a slug, it might appear as well
        if ($param_slug) {
            $route_regex .= "(" . preg_quote($param_slug, '#') . '/)?';
        }
        // Parameter might follow the request uri
        $route_regex .= "(/?([^/]+\\-\\-)?[^/]+\\-\\-[^/]+/?){0,}";
        // Create the regex
        $route_regex = '#' . $route_regex . '/?$#i';
        // convert placeholders to regex as well
        return preg_replace('/~([^~]+)~/i', ($param_slug ? '(' . preg_quote($param_slug, '#') . '\\K)?' : '') . '(?P<\\1>[^/]+)/?', $route_regex);
    }
    /**
     * Gets a request parameter from either the request uri or querystring
     * This method takes into consideration the values of the router_param_prefix
     * and router_param_separator settings when searching for the parameter
     *
     * Parameter can take on the following forms:
     * /key--value
     * /[MVC_PARAM_PREFIX]key--value
     * /[MVC_PARAM_PREFIX]-key--value
     * /[MVC_PARAM_PREFIX]_key--value
     * /id--key--value
     * /id--[MVC_PARAM_PREFIX]key--value
     * /id--[MVC_PARAM_PREFIX]-key--value
     * /id--[MVC_PARAM_PREFIX]_key--value
     *
     * @param string $key
     * @param mixed $id
     * @param mixed $default
     * @return mixed
     */
    function get_parameter($key, $id = NULL, $default = NULL, $segment = FALSE, $url = FALSE)
    {
        $retval = $default;
        $settings = $this->object->_settings;
        $quoted_key = preg_quote($key, '#');
        $id = $id ? preg_quote($id, '#') : "[^/]+";
        $param_prefix = preg_quote($settings->router_param_prefix, '#');
        $param_sep = preg_quote($settings->router_param_separator, '#');
        $param_regex = "#/((?P<id>{$id}){$param_sep})?({$param_prefix}[-_]?)?{$quoted_key}{$param_sep}(?P<value>[^/\\?]+)/?#i";
        $found = FALSE;
        $sources = $url ? array('custom' => $url) : $this->object->get_parameter_sources();
        foreach ($sources as $source_name => $source) {
            if (preg_match($param_regex, $source, $matches)) {
                if ($segment) {
                    $retval = array('segment' => $matches[0], 'source' => $source_name);
                } else {
                    $retval = $this->object->recursive_stripslashes($matches['value']);
                }
                $found = TRUE;
                break;
            }
        }
        // Lastly, check the $_REQUEST
        if (!$found && !$url && isset($_REQUEST[$key])) {
            $found = TRUE;
            $retval = $this->object->recursive_stripslashes($_REQUEST[$key]);
        }
        if (!$found && isset($_SERVER['REQUEST_URI'])) {
            $params = [];
            $parsed = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
            if (is_string($parsed)) {
                parse_str($parsed, $params);
            }
            if (isset($params[$key])) {
                $retval = $this->object->recursive_stripslashes($params[$key]);
            }
        }
        return $retval;
    }
    /**
     * Checks and cleans a URL. This function is from WordPress.
     *
     * A number of characters are removed from the URL. If the URL is for displaying
     * (the default behaviour) ampersands are also replaced. The 'clean_url' filter
     * is applied to the returned cleaned URL.
     *
     * @since 2.8.0
     * @uses wp_kses_bad_protocol() To only permit protocols in the URL set
     *		via $protocols or the common ones set in the function.
     *
     * @param string $url The URL to be cleaned.
     * @param array $protocols Optional. An array of acceptable protocols.
     *		Defaults to 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn' if not set.
     * @param string $_context Private. Use esc_url_raw() for database usage.
     * @return string The cleaned $url after the 'clean_url' filter is applied.
     */
    function esc_url($url, $protocols = null, $_context = 'display')
    {
        $original_url = $url;
        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\\|*\'()\\x80-\\xff]|i', '', $url);
        $strip = array('%0d', '%0a', '%0D', '%0A');
        $url = _deep_replace($strip, $url);
        $url = str_replace(';//', '://', $url);
        // Replace ampersands and single quotes only when displaying.
        if ('display' == $_context) {
            $url = wp_kses_normalize_entities($url);
            $url = str_replace('&amp;', '&#038;', $url);
            $url = str_replace("'", '&#039;', $url);
        }
        if (!empty($url[0]) && '/' === $url[0]) {
            $good_protocol_url = $url;
        } else {
            if (!is_array($protocols)) {
                $protocols = wp_allowed_protocols();
            }
            $good_protocol_url = wp_kses_bad_protocol($url, $protocols);
            if (strtolower($good_protocol_url) != strtolower($url)) {
                return '';
            }
        }
        /**
         * Filter a string cleaned and escaped for output as a URL.
         *
         * @since 2.3.0
         *
         * @param string $good_protocol_url The cleaned URL to be returned.
         * @param string $original_url      The URL prior to cleaning.
         * @param string $_context          If 'display', replace ampersands and single quotes only.
         */
        return apply_filters('clean_url', $good_protocol_url, $original_url, $_context);
    }
    /**
     * Sets the value of a particular parameter
     * @param string $key
     * @param mixed $value
     * @param mixed $id (optional)
     * @param bool $use_prefix (optional)
     * @param bool|string $url (optional)
     * @return string
     */
    function set_parameter_value($key, $value, $id = NULL, $use_prefix = FALSE, $url = FALSE)
    {
        // Remove the parameter from both the querystring and request uri
        $retval = $this->object->remove_parameter($key, $id, $url);
        // Get the settings manager
        $settings = $this->object->_settings;
        $param_slug = $settings->router_param_slug;
        // We're modifying a url passed in
        if ($url) {
            $parts = parse_url($retval);
            if (!isset($parts['path'])) {
                $parts['path'] = '';
            }
            $parts['path'] = $this->object->join_paths($parts['path'], $param_slug && strpos($parts['path'], $param_slug) === FALSE ? $param_slug : '', $this->object->create_parameter_segment($key, $value, $id, $use_prefix));
            $parts['path'] = str_replace('//', '/', $parts['path']);
            $retval = $this->object->construct_url_from_parts($parts);
        } else {
            // This parameter is being appended to the current request uri
            $this->object->add_parameter_to_app_request_uri($key, $value, $id, $use_prefix);
            // Return the new full url
            $retval = $this->object->get_routed_url();
        }
        return (is_null($retval) or is_numeric($retval) or is_array($retval)) ? $retval : $this->esc_url($retval);
    }
    /**
     * Alias for remove_parameter()
     * @param string $key
     * @param mixed $id
     * @return string
     */
    function remove_param($key, $id = NULL, $url = FALSE)
    {
        return $this->object->remove_parameter($key, $id, $url);
    }
    /**
     * Removes a parameter from the querystring and application request URI
     * and returns the full application URL
     * @param string $key
     * @param mixed $id
     * @return string|array|float|int
     */
    function remove_parameter($key, $id = NULL, $url = FALSE)
    {
        $retval = $url;
        $settings = $this->object->_settings;
        $param_sep = $settings->router_param_separator;
        $param_prefix = $settings->router_param_prefix ? preg_quote($settings->router_param_prefix, '#') : '';
        $param_slug = $settings->router_param_slug ? preg_quote($settings->router_param_slug, '#') : FALSE;
        // Is the parameter already part of the request? If so, modify that
        // parameter
        if (($segment = $this->object->get_parameter_segment($key, $id, $url)) && is_array($segment)) {
            extract($segment);
            if ($source == 'querystring') {
                $preg_id = $id ? '\\d+' : preg_quote($id, '#');
                $preg_key = preg_quote($key, '#');
                $regex = implode('', array('#', $id ? "{$preg_id}{$param_sep}" : '', "(({$param_prefix}{$param_sep})?)?{$preg_key}({$param_sep}|=)[^\\/&]+&?#i"));
                $qs = preg_replace($regex, '', $this->get_router()->get_querystring());
                $this->object->get_router()->set_querystring($qs);
                $retval = $this->object->get_routed_url();
            } elseif ($source == 'request_uri') {
                $uri = $this->object->get_app_request_uri();
                $uri = $this->object->join_paths(explode($segment, $uri));
                if ($settings->router_param_slug && preg_match("#{$param_slug}/?\$#i", $uri, $match)) {
                    $retval = $this->object->remove_url_segment($match[0], $retval);
                }
                $this->object->set_app_request_uri($uri);
                $retval = $this->object->get_routed_url();
            } else {
                $retval = $this->object->join_paths(explode($segment, $url));
                if ($settings->router_param_slug && preg_match("#/{$param_slug}\$#i", $retval, $match)) {
                    $retval = $this->object->remove_url_segment($match[0], $retval);
                }
            }
        }
        if (is_string($retval)) {
            $retval = rtrim($retval, ' ?&');
        }
        return (is_null($retval) or is_numeric($retval) or is_array($retval)) ? $retval : $this->esc_url($retval);
    }
    /**
     * Adds a parameter to the application's request URI
     * @param string $key
     * @param mixed $value
     * @param mixed $id (optional)
     * @param bool|string $use_prefix (optional)
     * @return string
     */
    function add_parameter_to_app_request_uri($key, $value, $id = NULL, $use_prefix = FALSE)
    {
        $settings = $this->object->_settings;
        $param_slug = $settings->router_param_slug;
        $uri = $this->object->get_app_request_uri();
        $parts = array($uri);
        if ($param_slug && strpos($uri, $param_slug) === FALSE) {
            $parts[] = $param_slug;
        }
        $parts[] = $this->object->create_parameter_segment($key, $value, $id, $use_prefix);
        $this->object->set_app_request_uri($this->object->join_paths($parts));
        return $this->object->get_app_request_uri();
    }
    /**
     * Creates a parameter segment
     * @param string $key
     * @param mixed $value
     * @param mixed $id
     * @return string
     */
    function create_parameter_segment($key, $value, $id = NULL, $use_prefix = FALSE)
    {
        $settings = $this->object->_settings;
        if ($use_prefix) {
            $key = $settings->router_param_prefix . $key;
        }
        if ($value === TRUE) {
            $value = 1;
        } elseif ($value == FALSE) {
            $value = 0;
        }
        // null and false values
        $retval = $key . $settings->router_param_separator . $value;
        if ($id) {
            $retval = $id . $settings->router_param_separator . $retval;
        }
        return $retval;
    }
    /**
     * Alias for set_parameter_value
     * @param string $key
     * @param mixed $value
     * @param mixed $id (optional)
     * @param bool $use_prefix (optional)
     * @param bool|string $url (optional)
     * @return string
     */
    function set_parameter($key, $value, $id = NULL, $use_prefix = FALSE, $url = FALSE)
    {
        return $this->object->set_parameter_value($key, $value, $id, $use_prefix, $url);
    }
    /**
     * Alias for set_parameter_value
     * @param string $key
     * @param mixed $value
     * @param mixed $id (optional)
     * @param bool $use_prefix (optional)
     * @param bool|string $url (optional)
     * @return string
     */
    function set_param($key, $value, $id = NULL, $use_prefix = FALSE, $url = FALSE)
    {
        return $this->object->set_parameter_value($key, $value, $id, $use_prefix = FALSE, $url);
    }
    /**
     * Gets a parameter's value
     * @param string $key
     * @param mixed $id
     * @param mixed $default
     * @return mixed
     */
    function get_parameter_value($key, $id = NULL, $default = NULL, $url = FALSE)
    {
        return $this->object->get_parameter($key, $id, $default, FALSE, $url);
    }
    /**
     * Gets a parameter's matching URI segment
     * @param string $key
     * @param mixed $id
     * @param mixed $url
     * @return mixed
     */
    function get_parameter_segment($key, $id = NULL, $url = FALSE)
    {
        return $this->object->get_parameter($key, $id, NULL, TRUE, $url);
    }
    /**
     * Gets sources used for parsing and extracting parameters
     * @return array
     */
    function get_parameter_sources()
    {
        return array('querystring' => $this->object->get_formatted_querystring(), 'request_uri' => $this->object->get_app_request_uri());
    }
    function get_postdata()
    {
        $retval = '/' . urldecode(file_get_contents("php://input"));
        $settings = $this->object->_settings;
        $retval = str_replace(array('&', '='), array('/', $settings->router_param_separator), $retval);
        return $retval;
    }
    function get_formatted_querystring()
    {
        $retval = '/' . $this->object->get_router()->get_querystring();
        $settings = $this->object->_settings;
        $retval = str_replace(array('&', '='), array('/', $settings->router_param_separator), $retval);
        return $retval;
    }
    function has_parameter_segments()
    {
        $retval = FALSE;
        $settings = $this->object->_settings;
        $request_uri = $this->object->get_app_request_uri();
        $sep = preg_quote($settings->router_param_separator, '#');
        // If we detect the MVC_PARAM_SLUG, then we assume that we have parameters
        if ($settings->router_param_slug && strpos($request_uri, '/' . $settings->router_param_slug . '/') !== FALSE) {
            $retval = TRUE;
        }
        // If the above didn't pass, then we try finding parameters in our
        // desired format
        if (!$retval) {
            $regex = implode('', array('#', $settings->router_param_slug ? '/' . preg_quote($settings->router_param_slug, '#') . '/?' : '', "(/?([^/]+{$sep})?[^/]+{$sep}[^/]+/?){0,}", '$#'));
            $retval = preg_match($regex, $request_uri);
        }
        return $retval;
    }
    /**
     * Recursively calls stripslashes() on strings, arrays, and objects
     *
     * @param mixed $value Value to be processed
     * @return mixed Resulting value
     */
    function recursive_stripslashes($value)
    {
        if (is_string($value)) {
            $value = stripslashes($value);
        } elseif (is_array($value)) {
            foreach ($value as &$tmp) {
                $tmp = $this->object->recursive_stripslashes($tmp);
            }
        } elseif (is_object($value)) {
            foreach (get_object_vars($value) as $key => $data) {
                $value->{$key} = recursive_stripslashes($data);
            }
        }
        return $value;
    }
}
/**
 * Class C_Routing_App
 * @mixin Mixin_Url_Manipulation
 * @mixin Mixin_Routing_App
 * @implements I_Routing_App
 */
class C_Routing_App extends C_Component
{
    static $_instances = array();
    var $_request_uri = FALSE;
    var $_settings = null;
    function define($context = FALSE)
    {
        parent::define($context);
        $this->add_mixin('Mixin_Url_Manipulation');
        $this->add_mixin('Mixin_Routing_App');
        $this->implement('I_Routing_App');
    }
    function initialize()
    {
        parent::initialize();
        $this->_settings = $this->object->get_routing_settings();
    }
    function get_routing_settings()
    {
        $settings = C_NextGen_Settings::get_instance();
        $object = new stdClass();
        $object->router_param_separator = $settings->get('router_param_separator', '--');
        $object->router_param_slug = $settings->get('router_param_slug', 'nggallery');
        $object->router_param_prefix = $settings->get('router_param_prefix', '');
        return $object;
    }
    static function &get_instance($context = False)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Routing_App($context);
        }
        return self::$_instances[$context];
    }
}
class Mixin_Url_Manipulation extends Mixin
{
    function join_paths()
    {
        $args = func_get_args();
        $parts = $this->_flatten_array($args);
        foreach ($parts as &$part) {
            $part = trim(str_replace("\\", '/', $part), "/");
        }
        return implode('/', $parts);
    }
    /**
     * Removes a segment from a url
     * @param string $segment
     * @param string $url
     * @return string
     */
    function remove_url_segment($segment, $url)
    {
        $retval = $url;
        $parts = parse_url($url);
        // If the url has a path, then we can remove a segment
        if (isset($parts['path']) && $segment != '/') {
            if (substr($segment, -1) == '/') {
                $segment = substr($segment, -1);
            }
            $segment = preg_quote($segment, '#');
            if (preg_match("#{$segment}#", $parts['path'], $matches)) {
                $parts['path'] = str_replace('//', '/', str_replace($matches[0], '', $parts['path']));
                $retval = $this->object->construct_url_from_parts($parts);
            }
        }
        return $retval;
    }
    /**
     * Flattens an array of arrays to a single array
     * @param array $array
     * @param array $parent (optional)
     * @param bool $exclude_duplicates (optional - defaults to TRUE)
     * @return array
     */
    function _flatten_array($array, $parent = NULL, $exclude_duplicates = TRUE)
    {
        if (is_array($array)) {
            // We're to add each element to the parent array
            if ($parent) {
                foreach ($array as $index => $element) {
                    foreach ($this->_flatten_array($array) as $sub_element) {
                        if ($exclude_duplicates) {
                            if (!in_array($sub_element, $parent)) {
                                $parent[] = $sub_element;
                            }
                        } else {
                            $parent[] = $sub_element;
                        }
                    }
                }
                $array = $parent;
            } else {
                $index = 0;
                while (isset($array[$index])) {
                    $element = $array[$index];
                    if (is_array($element)) {
                        $array = $this->_flatten_array($element, $array);
                        unset($array[$index]);
                    }
                    $index += 1;
                }
                $array = array_values($array);
            }
        } else {
            $array = array($array);
        }
        return $array;
    }
    function join_querystrings()
    {
        $parts = array();
        $retval = array();
        $params = func_get_args();
        $parts = $this->_flatten_array($params);
        foreach ($parts as $part) {
            $part = explode("&", $part);
            foreach ($part as $segment) {
                $segment = explode("=", $segment);
                $key = $segment[0];
                $value = isset($segment[1]) ? $segment[1] : '';
                $retval[$key] = $value;
            }
        }
        return $this->object->assoc_array_to_querystring($retval);
    }
    function assoc_array_to_querystring($arr)
    {
        $retval = array();
        foreach ($arr as $key => $val) {
            if (strlen($key)) {
                $retval[] = strlen($val) ? "{$key}={$val}" : $key;
            }
        }
        return implode("&", $retval);
    }
    /**
     * Constructs a url from individual parts, created by parse_url
     * @param array $parts
     * @return string
     */
    function construct_url_from_parts($parts)
    {
        // let relative paths be relative, and full paths full
        $prefix = '';
        if (!empty($parts['scheme']) && !empty($parts['host'])) {
            $prefix = $parts['scheme'] . '://' . $parts['host'];
            if (!empty($parts['port'])) {
                $prefix .= ':' . $parts['port'];
            }
        }
        $retval = $this->object->join_paths($prefix, isset($parts['path']) ? str_replace('//', '/', trailingslashit($parts['path'])) : '');
        if (isset($parts['query']) && $parts['query']) {
            $retval .= untrailingslashit("?{$parts['query']}");
        }
        return $retval;
    }
    function get_parameter_segments($request_uri)
    {
        return str_replace($this->strip_param_segments($request_uri), '', $request_uri);
    }
    /**
     * Returns the request uri with the parameter segments stripped
     * @param string $request_uri
     * @return string
     */
    function strip_param_segments($request_uri, $remove_slug = TRUE)
    {
        $retval = $request_uri ? $request_uri : '/';
        $settings = C_NextGen_Settings::get_instance();
        $sep = preg_quote($settings->get('router_param_separator', '--'), '#');
        $param_regex = "#((?P<id>\\w+){$sep})?(?<key>\\w+){$sep}(?P<value>.+)/?\$#";
        $slug = $settings->get('router_param_slug', 'nggallery') && $remove_slug ? '/' . preg_quote($settings->get('router_param_slug', 'nggallery'), '#') : '';
        $slug_regex = '#' . $slug . '/?$#';
        // Remove all parameters
        while (@preg_match($param_regex, $retval, $matches)) {
            $match_regex = '#' . preg_quote(array_shift($matches), '#') . '$#';
            $retval = preg_replace($match_regex, '', $retval);
        }
        // Remove the slug or trailing slash
        if (@preg_match($slug_regex, $retval, $matches)) {
            $match_regex = '#' . preg_quote(array_shift($matches), '#') . '$#';
            $retval = preg_replace($match_regex, '', $retval);
        }
        // If there's a slug, we can assume everything after is a parameter,
        // even if it's not in our desired format.
        $retval = preg_replace('#' . $slug . '.*$#', '', $retval);
        if (!$retval) {
            $retval = '/';
        }
        return $retval;
    }
}