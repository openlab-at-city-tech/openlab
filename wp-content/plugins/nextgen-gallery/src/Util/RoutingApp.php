<?php

namespace Imagely\NGG\Util;

use Imagely\NGG\Settings\Settings;

class RoutingApp {

	public static $_instances = [];
	public $_request_uri      = false;
	public $_settings         = null;

	protected $_rewrite_patterns = [];
	protected $_routing_patterns = [];

	public $context = false;

	public function __construct( $context ) {
		$this->_settings = $this->get_routing_settings();
		$this->context   = $context;
	}

	public function get_routing_settings() {
		$settings                       = Settings::get_instance();
		$object                         = new \stdClass();
		$object->router_param_separator = $settings->get( 'router_param_separator', '--' );
		$object->router_param_slug      = $settings->get( 'router_param_slug', 'nggallery' );
		$object->router_param_prefix    = $settings->get( 'router_param_prefix', '' );

		return $object;
	}

	public static function get_instance( $context = false ) {
		if ( ! isset( self::$_instances[ $context ] ) ) {
			self::$_instances[ $context ] = new RoutingApp( $context );
		}
		return self::$_instances[ $context ];
	}

	/**
	 * Creates a new route endpoint with the assigned handler
	 *
	 * @param string[] $routes URL to route, eg /page/{page}/
	 * @param array    $handler Formatted array
	 */
	public function route( $routes, $handler ) {
		// ensure that the routing patterns array exists.
		if ( ! is_array( $this->_routing_patterns ) ) {
			$this->_routing_patterns = [];
		}

		if ( ! is_array( $routes ) ) {
			$routes = [ $routes ];
		}

		// fetch all routing patterns.
		$patterns = $this->_routing_patterns;

		foreach ( $routes as $route ) {
			// add the routing pattern.
			$patterns[ $this->_route_to_regex( $route ) ] = $handler;
		}

		// update routing patterns.
		$this->_routing_patterns = $patterns;
	}

	/**
	 * Handles internal url rewriting with optional HTTP redirection,
	 *
	 * @param string $src Original URL
	 * @param string $dst Destination URL
	 * @param bool   $redirect FALSE for internal handling, otherwise the HTTP code to send
	 * @param bool   $stop
	 */
	public function rewrite( $src, $dst, $redirect = false, $stop = false ) {
		// ensure that rewrite patterns array exists.
		if ( ! is_array( $this->_rewrite_patterns ) ) {
			$this->_rewrite_patterns = [];
		}

		// fetch all rewrite patterns.
		$patterns = $this->_rewrite_patterns;

		// Assign rewrite definition.
		$definition = [
			'dst'      => $dst,
			'redirect' => $redirect,
			'stop'     => $stop,
		];

		// We treat wildcards much differently than normal rewrites.
		if ( preg_match( '/\\{[\\.\\\\*]/', $src ) ) {
			$pattern                 = str_replace( '{*}', '(.*?)', $src );
			$pattern                 = str_replace( '{.*}', '(.*?)', $pattern );
			$pattern                 = str_replace( '{\\w}', '([^/]*)', $pattern );
			$pattern                 = str_replace( '{\\d}', '(\\d*)', $pattern );
			$src                     = '#' . ( strpos( $src, '/' ) === 0 ? '^' : '' ) . $pattern . '/?$#';
			$definition['wildcards'] = true;
		} else {
			// Normal rewrite.
			$src = $this->_route_to_regex( $src );
		}

		// add the rewrite pattern.
		$patterns[ $src ] = $definition;

		// update rewrite patterns.
		$this->_rewrite_patterns = $patterns;
	}

	/**
	 * Gets an instance of the router
	 *
	 * @return Router
	 */
	public function get_router() {
		return Router::get_instance();
	}

	public function get_app_url( $request_uri = false, $with_qs = false ) {
		return $this->get_router()->get_url( $this->get_app_uri( $request_uri ), $with_qs );
	}

	public function get_routed_url( $with_qs = true ) {
		return $this->get_app_url( false, $with_qs );
	}

	public function get_app_uri( $request_uri = false ) {
		if ( ! $request_uri ) {
			$request_uri = $this->get_app_request_uri();
		}
		return $this->join_paths(
			$this->context,
			$request_uri
		);
	}

	public function get_app_request_uri() {
		$retval = false;

		if ( $this->_request_uri ) {
			$retval = $this->_request_uri;
		} elseif ( ( $retval = $this->does_app_serve_request() ) ) {
			if ( strpos( $retval, '/' ) !== 0 ) {
				$retval = '/' . $retval;
			}
			$this->set_app_request_uri( $retval );
		}

		return $retval;
	}

	/**
	 * Sets the application request uri
	 *
	 * @param string $uri
	 */
	public function set_app_request_uri( $uri ) {
		$this->_request_uri = $uri;
	}

	/**
	 * Gets the application's routing regex pattern
	 *
	 * @return string
	 */
	public function get_app_routing_pattern() {
		return $this->_route_to_regex( $this->context );
	}

	/**
	 * Determines whether this app serves the request
	 *
	 * @return boolean|string
	 */
	public function does_app_serve_request() {
		$retval = false;

		$request_uri = $this->get_router()->get_request_uri( true );

		// Is the context present in the uri?
		if ( ( $index = strpos( $request_uri, $this->context ) ) !== false ) {
			$starts_with_slash = strpos( $this->context, '/' ) === 0;
			if ( ( $starts_with_slash && $index === 0 ) or ( ! $starts_with_slash ) ) {
				$regex  = implode(
					'',
					[
						'#',
						( $starts_with_slash ? '^' : '' ),
						preg_quote( $this->context, '#' ),
						'#',
					]
				);
				$retval = preg_replace( $regex, '', $request_uri );
				if ( ! $retval ) {
					$retval = '/';
				}
				if ( strpos( $retval, '/' ) !== 0 ) {
					$retval = '/' . $retval;
				}
				if ( substr( $retval, -1 ) != '/' ) {
					$retval = $retval . '/';
				}
			}
		}

		return $retval;
	}

	/**
	 * Performs the url rewriting routines. Returns the HTTP status code used to
	 * redirect, if we're to do so. Otherwise FALSE
	 *
	 * @return int|bool
	 */
	public function do_rewrites( $request_uri = false ) {
		$redirect               = false;
		static $stop_processing = false;

		// Get the request uri if not provided, if provided decode it.
		if ( ! $request_uri ) {
			$request_uri = $this->get_app_request_uri();
		} else {
			$request_uri = urldecode( $request_uri );
		}

		// ensure that rewrite patterns array exists.
		if ( ! is_array( $this->_rewrite_patterns ) ) {
			$this->_rewrite_patterns = [];
		}

		// Process each rewrite rule
		// start rewriting urls.
		if ( ! $stop_processing ) {
			foreach ( $this->_rewrite_patterns as $pattern => $details ) {

				// Remove this pattern from future processing for this request.
				unset( $this->_rewrite_patterns[ $pattern ] );

				// Wildcards are processed much differently.
				if ( isset( $details['wildcards'] ) && $details['wildcards'] ) {
					if ( preg_match( $pattern, $request_uri, $matches ) ) {
						foreach ( $matches as $index => $match ) {
							if ( $index == 0 ) {
								$request_uri = str_replace( $match, $details['dst'], $request_uri );
							}
							if ( $index > 0 ) {
								$request_uri = str_replace(
									"{{$index}}",
									$match,
									$request_uri
								);
							}
						}

						// Set the redirect flag if we're to do so.
						if ( isset( $details['redirect'] ) && $details['redirect'] ) {
							$redirect = $details['redirect'] === true ?
							302 : intval( $details['redirect'] );
							break;
						}

						// Stop processing rewrite patterns?
						if ( $details['stop'] ) {
							$stop_processing = true;

						}
					}
				}

				// Normal rewrite pattern.
				elseif ( preg_match_all( $pattern, $request_uri, $matches, PREG_SET_ORDER ) ) {
					// Assign new request URI.
					$request_uri = $details['dst'];

					// Substitute placeholders.
					foreach ( $matches as $match ) {
						if ( $redirect ) {
							break;
						}
						foreach ( $match as $key => $val ) {

							// If we have a placeholder that needs swapped, swap
							// it now.
							if ( is_numeric( $key ) ) {
								continue;
							}
							$request_uri = str_replace( "{{$key}}", $val, $request_uri );
						}
						// Set the redirect flag if we're to do so.
						if ( isset( $details['redirect'] ) && $details['redirect'] ) {
							$redirect = $details['redirect'] === true ?
							302 : intval( $details['redirect'] );
							break;
						}
					}
				}

				if ( $stop_processing ) {
					break;
				}
			}
		}

		// Cache all known data about the application request.
		$this->set_app_request_uri( $request_uri );
		$this->get_router()->set_routed_app( $this );

		return $redirect;
	}

	/**
	 * Determines if the current routing app meets our requirements and serves them
	 *
	 * @return bool
	 */
	public function serve_request() {
		$served = false;

		// ensure that the routing patterns array exists.
		if ( ! is_array( $this->_routing_patterns ) ) {
			$this->_routing_patterns = [];
		}

		// if the application root matches, then we'll try to route the request.
		if ( ( $request_uri = $this->get_app_request_uri() ) ) {
			// Perform URL rewrites.
			$redirect = $this->do_rewrites( $request_uri );

			// Are we to perform a redirect?
			if ( $redirect ) {
				$this->execute_route_handler( $this->parse_route_handler( $redirect ) );
			} else {
				// Handle routed endpoints.
				foreach ( $this->_routing_patterns as $pattern => $handler ) {
					if ( preg_match( $pattern, $this->get_app_request_uri(), $matches ) ) {
						$served = true;

						// Add placeholder parameters.
						foreach ( $matches as $key => $value ) {
							if ( is_numeric( $key ) ) {
								continue;
							}
							$this->set_parameter_value( $key, $value, null );
						}

						// If a handler is attached to the route, execute it. A
						// handler can be
						// - FALSE, meaning don't do any post-processing to the route
						// - A string, such as controller#action
						// - An array: array(
						// 'controller' => 'I_Test_Controller',
						// 'action'     => 'index',
						// 'context'    => 'all', (optional)
						// 'method'     => array('GET') (optional)
						// ).

						if ( $handler && $handler = $this->parse_route_handler( $handler ) ) {
							// Is this handler for the current HTTP request method?
							if ( isset( $handler['method'] ) ) {
								if ( ! is_array( $handler['method'] ) ) {
									$handler['$method'] = [ $handler['method'] ];
								}
								if ( in_array( $this->get_router()->get_request_method(), $handler['method'] ) ) {
									$this->execute_route_handler( $handler );
								}
							}

							// This handler is for all request methods.
							else {
								$this->execute_route_handler( $handler );
							}
						} elseif ( ! $handler ) {
							$this->passthru();
						}
					}
				}
			}
		}

		return $served;
	}

	/**
	 * Executes an action of a particular controller
	 *
	 * @param array $handler
	 */
	public function execute_route_handler( $handler ) {
		// qTranslate requires we disable "Hide Untranslated Content" during routed app requests like
		// photocrati-ajax, when uploading new images, or retrieving dynamically altered (watermarked) images.
		if ( ! empty( $GLOBALS['q_config'] ) && defined( 'QTRANS_INIT' ) ) {
			global $q_config;
			$q_config['hide_untranslated'] = 0;
		}

		// Get action.
		$action = $handler['action'];

		if ( class_exists( $handler['controller'] ) ) {
			$controller = new $handler['controller']();
		}

		// TODO: Remove when Pro's minimum supported version supports v1 of the POPE removal compat.
		elseif ( class_exists( '\C_Component_Registry' ) ) {
			$controller = \C_Component_Registry::get_instance()->get_utility( $handler['controller'], $handler['context'] );
		}

		// Call action.
		$controller->$action();

		exit();
	}

	/**
	 * Parses the route handler
	 *
	 * @param mixed $handler
	 * @return array
	 */
	public function parse_route_handler( $handler ) {
		if ( is_string( $handler ) ) {
			$handler = array_combine( [ 'controller', 'action' ], explode( '#', $handler ) );
		}

		if ( ! isset( $handler['context'] ) ) {
			$handler['context'] = false;
		}
		if ( strpos( $handler['action'], '_action' ) === false ) {
			$handler['action'] .= '_action';
		}

		return $handler;
	}

	/**
	 * Converts the route to the regex
	 *
	 * @param string $route
	 * @return string
	 */
	public function _route_to_regex( $route ) {
		// Get the settings manager.
		$settings   = $this->_settings;
		$param_slug = $settings->router_param_slug;

		// convert route to RegEx pattern.
		$route_regex = preg_quote(
			str_replace(
				[ '{', '}' ],
				[ '~', '~' ],
				$route
			),
			'#'
		);

		// Wrap the route.
		$route_regex = '(' . $route_regex . ')';

		// If the route starts with a slash, then it must appear at the beginning
		// of a request uri.
		if ( strpos( $route, '/' ) === 0 ) {
			$route_regex = '^' . $route_regex;
		}

		// If the route is not /, and perhaps /foo, then we need to optionally
		// look for a trailing slash as well.
		if ( $route != '/' ) {
			$route_regex .= '/?';
		}

		// If parameters come after a slug, it might appear as well.
		if ( $param_slug ) {
			$route_regex .= '(' . preg_quote( $param_slug, '#' ) . '/)?';
		}

		// Parameter might follow the request uri.
		$route_regex .= '(/?([^/]+\-\-)?[^/]+\-\-[^/]+/?){0,}';

		// Create the regex.
		$route_regex = '#' . $route_regex . '/?$#i';

		// convert placeholders to regex as well.
		return preg_replace( '/~([^~]+)~/i', ( $param_slug ? '(' . preg_quote( $param_slug, '#' ) . '\K)?' : '' ) . '(?P<\1>[^/]+)/?', $route_regex );
	}

	/**
	 * Gets a request parameter from either the request uri or querystring.
	 *
	 * This method takes into consideration the values of the router_param_prefix and router_param_separator settings
	 * when searching for the parameter.
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
	 * @param mixed  $id
	 * @param mixed  $default
	 * @return mixed
	 */
	public function get_parameter( $key, $id = null, $default = null, $segment = false, $url = false ) {
		$retval       = $default;
		$settings     = $this->_settings;
		$quoted_key   = preg_quote( $key, '#' );
		$id           = $id ? preg_quote( $id, '#' ) : '[^/]+';
		$param_prefix = preg_quote( $settings->router_param_prefix, '#' );
		$param_sep    = preg_quote( $settings->router_param_separator, '#' );
		$param_regex  = "#/((?P<id>{$id}){$param_sep})?({$param_prefix}[-_]?)?{$quoted_key}{$param_sep}(?P<value>[^/\?]+)/?#i";
		$found        = false;
		$sources      = $url ? [ 'custom' => $url ] : $this->get_parameter_sources();

		foreach ( $sources as $source_name => $source ) {
			if ( preg_match( $param_regex, $source, $matches ) ) {
				if ( $segment ) {
					$retval = [
						'segment' => $matches[0],
						'source'  => $source_name,
					];
				} else {
					$retval = $this->recursive_stripslashes( $matches['value'] );
				}
				$found = true;
				break;
			}
		}

		// Lastly, check the $_REQUEST.
		if ( ! $found && ! $url && isset( $_REQUEST[ $key ] ) ) {
			$found  = true;
			$retval = $this->recursive_stripslashes( $_REQUEST[ $key ] );
		}

		if ( ! $found && isset( $_SERVER['REQUEST_URI'] ) ) {
			$params = [];
			$parsed = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY );
			if ( is_string( $parsed ) ) {
				parse_str( $parsed, $params );
			}

			if ( isset( $params[ $key ] ) ) {
				$retval = $this->recursive_stripslashes( $params[ $key ] );
			}
		}

		return $retval;
	}

	/**
	 * Alias for remove_parameter()
	 *
	 * @param string $key
	 * @param mixed  $id
	 * @return string
	 */
	public function remove_param( $key, $id = null, $url = false ) {
		return $this->remove_parameter( $key, $id, $url );
	}

	/**
	 * Adds a parameter to the application's request URI
	 *
	 * @param string      $key
	 * @param mixed       $value
	 * @param mixed       $id (optional)
	 * @param bool|string $use_prefix (optional)
	 * @return string
	 */
	public function add_parameter_to_app_request_uri( $key, $value, $id = null, $use_prefix = false ) {
		$settings   = $this->_settings;
		$param_slug = $settings->router_param_slug;

		$uri   = $this->get_app_request_uri();
		$parts = [ $uri ];
		if ( $param_slug && strpos( $uri, $param_slug ) === false ) {
			$parts[] = $param_slug;
		}
		$parts[] = $this->create_parameter_segment( $key, $value, $id, $use_prefix );
		$this->set_app_request_uri( $this->join_paths( $parts ) );

		return $this->get_app_request_uri();
	}

	/**
	 * Alias for set_parameter_value
	 *
	 * @param string      $key
	 * @param mixed       $value
	 * @param mixed       $id (optional)
	 * @param bool        $use_prefix (optional)
	 * @param bool|string $url (optional)
	 * @return string
	 */
	public function set_parameter( $key, $value, $id = null, $use_prefix = false, $url = false ) {
		return $this->set_parameter_value( $key, $value, $id, $use_prefix, $url );
	}

	/**
	 * Alias for set_parameter_value
	 *
	 * @param string      $key
	 * @param mixed       $value
	 * @param mixed       $id (optional)
	 * @param bool        $use_prefix (optional)
	 * @param bool|string $url (optional)
	 * @return string
	 */
	public function set_param( $key, $value, $id = null, $use_prefix = false, $url = false ) {
		return $this->set_parameter_value( $key, $value, $id, $use_prefix, $url );
	}

	/**
	 * Gets a parameter's matching URI segment
	 *
	 * @param string $key
	 * @param mixed  $id
	 * @param mixed  $url
	 * @return mixed
	 */
	public function get_parameter_segment( $key, $id = null, $url = false ) {
		return $this->get_parameter( $key, $id, null, true, $url );
	}

	/**
	 * Gets sources used for parsing and extracting parameters
	 *
	 * @return array
	 */
	public function get_parameter_sources() {
		return [
			'querystring' => $this->get_formatted_querystring(),
			'request_uri' => $this->get_app_request_uri(),
		];
	}

	public function get_formatted_querystring() {
		$retval   = '/' . $this->get_router()->get_querystring();
		$settings = $this->_settings;
		$retval   = str_replace(
			[ '&', '=' ],
			[ '/', $settings->router_param_separator ],
			$retval
		);

		return $retval;
	}

	public function has_parameter_segments() {
		$retval      = false;
		$settings    = $this->_settings;
		$request_uri = $this->get_app_request_uri();
		$sep         = preg_quote( $settings->router_param_separator, '#' );

		// If we detect the MVC_PARAM_SLUG, then we assume that we have parameters.
		if ( $settings->router_param_slug && strpos( $request_uri, '/' . $settings->router_param_slug . '/' ) !== false ) {
			$retval = true;
		}

		// If the above didn't pass, then we try finding parameters in our
		// desired format.
		if ( ! $retval ) {
			$regex  = implode(
				'',
				[
					'#',
					$settings->router_param_slug ? '/' . preg_quote( $settings->router_param_slug, '#' ) . '/?' : '',
					"(/?([^/]+{$sep})?[^/]+{$sep}[^/]+/?){0,}",
					'$#',
				]
			);
			$retval = preg_match( $regex, $request_uri );
		}

		return $retval;
	}

	/**
	 * Recursively calls stripslashes() on strings, arrays, and objects
	 *
	 * @param mixed $value Value to be processed
	 * @return mixed Resulting value
	 */
	public function recursive_stripslashes( $value ) {
		if ( is_string( $value ) ) {
			$value = stripslashes( $value );
		} elseif ( is_array( $value ) ) {
			foreach ( $value as &$tmp ) {
				$tmp = $this->recursive_stripslashes( $tmp );
			}
		} elseif ( is_object( $value ) ) {
			foreach ( get_object_vars( $value ) as $key => $data ) {
				$value->{$key} = $this->recursive_stripslashes( $data );
			}
		}

		return $value;
	}

	public function passthru() {
		$router = Router::get_instance();

		$_SERVER['NGG_ORIG_REQUEST_URI'] = $_SERVER['REQUEST_URI'];
		$base_parts                      = parse_url( $router->get_base_url( 'root' ) );
		$new_request_uri                 = $router->join_paths(
			( ! empty( $base_parts['path'] ) ? $base_parts['path'] : '' ),
			$this->strip_param_segments( $router->get_request_uri() )
		);

		$new_request_uri = str_replace( 'index.php/index.php', 'index.php', $new_request_uri );

		// Handle possible incompatibility with 3rd party plugins manipulating the query as well: WPML in particular
		// can lead to our $new_request_uri here becoming index.php/en/index.php: remove this double index.php.
		$uri_array = explode( '/', $new_request_uri );
		if ( ! empty( $uri_array ) && count( $uri_array ) >= 2 && reset( $uri_array ) == 'index.php' && end( $uri_array ) == 'index.php' ) {
			array_shift( $uri_array );
			$new_request_uri = implode( '/', $uri_array );
		}

		$_SERVER['UNENCODED_URL'] = $_SERVER['HTTP_X_ORIGINAL_URL'] = $_SERVER['REQUEST_URI'] = '/' . trailingslashit( $new_request_uri );
		if ( isset( $_SERVER['PATH_INFO'] ) ) {
			$_SERVER['ORIG_PATH_INFO'] = $_SERVER['PATH_INFO'];
			unset( $_SERVER['PATH_INFO'] );
		}
	}

	public function parse_url( $url ) {
		$parts = parse_url( $url );
		if ( ! isset( $parts['path'] ) ) {
			$parts['path'] = '/';
		}
		if ( ! isset( $parts['query'] ) ) {
			$parts['query'] = '';
		}

		return $parts;
	}

	/**
	 * Adds the post permalink to the url, if it isn't already present.
	 *
	 * The generated_url could look like:
	 * http://localhost/dir/nggallery/show/slideshow
	 *
	 * @param $generated_url
	 * @return mixed
	 */
	public function add_post_permalink_to_url( $generated_url ) {
		if ( ! apply_filters( 'ngg_wprouting_add_post_permalink', true ) ) {
			return $generated_url;
		}

		global $multipage, $page;

		$base_url = $this->get_router()->get_base_url( 'home' );
		$settings = Settings::get_instance();
		if ( strlen( $generated_url ) < 2 ) {
			$generated_url = $base_url;
		}

		$original_url    = $generated_url;
		$generated_parts = explode( $settings->get( 'router_param_slug', 'nggallery' ), $generated_url );
		$generated_url   = $generated_parts[0];
		$ngg_parameters  = '/';
		if ( isset( $generated_parts[1] ) ) {
			$parts          = explode( '?', $generated_parts[1] );
			$ngg_parameters = array_shift( $parts );
		}
		$post_permalink = get_permalink( isset( $_REQUEST['p'] ) ? $_REQUEST['p'] : 0 );
		if ( $post_permalink == '/' ) {
			$post_permalink = $base_url;
		}

		// Trailing slash all of the urls.
		$original_url   = trailingslashit( $original_url );
		$post_permalink = trailingslashit( $post_permalink );
		$generated_url  = trailingslashit( $generated_url );

		// Ensure that /page/2/ links to /page/2/nggallery/page/4 rather than /nggallery/page/4/ when our paginated
		// galleries are displayed on posts paginated through the page break block.
		if ( $multipage && $page >= 2 ) {
			$post_permalink = $post_permalink . $page;
		}

		// We need to determine if the generated url and the post permalink TRULY differ. If they
		// differ, then we'll return post_permalink + nggallery parameters appended. Otherwise, we'll
		// just return the generated url.
		$generated_url           = str_replace( $base_url, home_url(), $generated_url );
		$generated_parts         = $this->parse_url( $generated_url );
		$post_parts              = $this->parse_url( $post_permalink );
		$generated_parts['path'] = trailingslashit( $generated_parts['path'] );
		if ( isset( $generated_parts['query'] ) ) {
			$generated_parts['query'] = untrailingslashit( $generated_parts['query'] );
		}
		$post_parts['path'] = trailingslashit( $post_parts['path'] );
		if ( isset( $post_parts['query'] ) ) {
			$post_parts['query'] = untrailingslashit( $post_parts['query'] );
		}

		$generated_url  = $this->construct_url_from_parts( $generated_parts );
		$post_permalink = $this->construct_url_from_parts( $post_parts );

		// No change required...
		if ( $generated_url == $post_permalink ) {
			$generated_url = $original_url;

			// Ensure that the generated url has the real base url for default permalinks.
			if ( strpos( $generated_url, home_url() ) !== false && strpos( $generated_url, $base_url ) === false ) {
				$generated_url = str_replace( home_url(), $base_url, $generated_url );
			}
		} else {
			// The post permalink differs from the generated url.
			$post_permalink     = str_replace( home_url(), $base_url, $post_permalink );
			$post_parts         = $this->parse_url( $post_permalink );
			$post_parts['path'] = $this->join_paths( $post_parts['path'], $settings->get( 'router_param_slug', 'nggallery' ), $ngg_parameters );
			$post_parts['path'] = str_replace( 'index.php/index.php', 'index.php', $post_parts['path'] ); // incase permalink_structure contains index.php.
			if ( ! empty( $generated_parts['query'] ) && empty( $post_parts['query'] ) ) {
				$post_parts['query'] = $generated_parts['query'];
			}
			$generated_url = $this->construct_url_from_parts( $post_parts );
		}

		return $generated_url;
	}

	public function join_paths() {
		$args  = func_get_args();
		$parts = $this->_flatten_array( $args );
		foreach ( $parts as &$part ) {
			$part = trim( str_replace( '\\', '/', $part ), '/' );
		}
		return implode( '/', $parts );
	}

	/**
	 * Removes a segment from a url
	 *
	 * @param string $segment
	 * @param string $url
	 * @return string
	 */
	public function remove_url_segment( $segment, $url ) {
		$retval = $url;
		$parts  = parse_url( $url );

		// If the url has a path, then we can remove a segment.
		if ( isset( $parts['path'] ) && $segment != '/' ) {
			if ( substr( $segment, -1 ) == '/' ) {
				$segment = substr( $segment, -1 );
			}
			$segment = preg_quote( $segment, '#' );
			if ( preg_match( "#{$segment}#", $parts['path'], $matches ) ) {
				$parts['path'] = str_replace(
					'//',
					'/',
					str_replace( $matches[0], '', $parts['path'] )
				);
				$retval        = $this->construct_url_from_parts( $parts );
			}
		}
		return $retval;
	}

	/**
	 * Flattens an array of arrays to a single array
	 *
	 * @param array $array
	 * @param array $parent (optional)
	 * @param bool  $exclude_duplicates (optional - defaults to TRUE)
	 * @return array
	 */
	public function _flatten_array( $array, $parent = null, $exclude_duplicates = true ) {
		if ( is_array( $array ) ) {

			// We're to add each element to the parent array.
			if ( $parent ) {
				foreach ( $array as $index => $element ) {
					foreach ( $this->_flatten_array( $array ) as $sub_element ) {
						if ( $exclude_duplicates ) {
							if ( ! in_array( $sub_element, $parent ) ) {
								$parent[] = $sub_element;
							}
						} else {
							$parent[] = $sub_element;
						}
					}
				}
				$array = $parent;
			}

			// We're starting the process..
			else {
				$index = 0;
				while ( isset( $array[ $index ] ) ) {
					$element = $array[ $index ];
					if ( is_array( $element ) ) {
						$array = $this->_flatten_array( $element, $array );
						unset( $array[ $index ] );
					}
					$index += 1;
				}
				$array = array_values( $array );
			}
		} else {
			$array = [ $array ];
		}

		return $array;
	}

	/**
	 * Constructs a url from individual parts, created by parse_url
	 *
	 * @param array $parts
	 * @return string
	 */
	public function construct_url_from_parts( $parts ) {
		// let relative paths be relative, and full paths full.
		$prefix = '';
		if ( ! empty( $parts['scheme'] ) && ! empty( $parts['host'] ) ) {
			$prefix = $parts['scheme'] . '://' . $parts['host'];
			if ( ! empty( $parts['port'] ) ) {
				$prefix .= ':' . $parts['port'];
			}
		}

		$retval = $this->join_paths(
			$prefix,
			isset( $parts['path'] ) ? str_replace( '//', '/', trailingslashit( $parts['path'] ) ) : ''
		);

		if ( isset( $parts['query'] ) && $parts['query'] ) {
			$retval .= untrailingslashit( "?{$parts['query']}" );
		}

		return $retval;
	}

	/**
	 * Returns the request uri with the parameter segments stripped
	 *
	 * @param string $request_uri
	 * @return string
	 */
	public function strip_param_segments( $request_uri, $remove_slug = true ) {
		$retval      = $request_uri ? $request_uri : '/';
		$settings    = Settings::get_instance();
		$sep         = preg_quote( $settings->get( 'router_param_separator', '--' ), '#' );
		$param_regex = "#((?P<id>\w+){$sep})?(?<key>\w+){$sep}(?P<value>.+)/?$#";
		$slug        = $settings->get( 'router_param_slug', 'nggallery' ) && $remove_slug ? '/' . preg_quote( $settings->get( 'router_param_slug', 'nggallery' ), '#' ) : '';
		$slug_regex  = '#' . $slug . '/?$#';

		// Remove all parameters.
		while ( @preg_match( $param_regex, $retval, $matches ) ) {
			$match_regex = '#' . preg_quote( array_shift( $matches ), '#' ) . '$#';
			$retval      = preg_replace( $match_regex, '', $retval );
		}

		// Remove the slug or trailing slash.
		if ( @preg_match( $slug_regex, $retval, $matches ) ) {
			$match_regex = '#' . preg_quote( array_shift( $matches ), '#' ) . '$#';
			$retval      = preg_replace( $match_regex, '', $retval );
		}

		// If there's a slug, we can assume everything after is a parameter,
		// even if it's not in our desired format.
		$retval = preg_replace( '#' . $slug . '.*$#', '', $retval );

		if ( ! $retval ) {
			$retval = '/';
		}

		return $retval;
	}

	/**
	 * Creates a parameter segment
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @param mixed  $id
	 * @return string
	 */
	public function create_parameter_segment( $key, $value, $id = null, $use_prefix = false ) {
		if ( $key === 'nggpage' ) {
			return 'page/' . $value;
		} elseif ( $key === 'album' ) {
			return $value;
		} elseif ( $key === 'gallery' ) {
			return $value;
		} elseif ( $key === 'pid' ) {
			return "image/{$value}";
		} elseif ( $key === 'gallerytag' ) {
			return 'tags/' . $value;
		}

		if ( $key == 'show' ) {
			if ( $value === NGG_BASIC_SLIDESHOW ) {
				$value = 'slideshow';
			} elseif ( $value == NGG_BASIC_THUMBNAILS ) {
				$value = 'thumbnails';
			} elseif ( $value == NGG_BASIC_IMAGEBROWSER ) {
				$value = 'imagebrowser';
			}
			return $value;
		}

		$settings = $this->_settings;
		if ( $use_prefix ) {
			$key = $settings->router_param_prefix . $key;
		}
		if ( $value === true ) {
			$value = 1;
		} elseif ( $value === false ) {
			$value = 0; // null and false values.
		}
		$retval = $key . $settings->router_param_separator . $value;
		if ( $id ) {
			$retval = $id . $settings->router_param_separator . $retval;
		}
		return $retval;
	}

	/**
	 * Removes a parameter from the querystring and application request URI and returns the full application URL
	 *
	 * @param string $key
	 * @param mixed  $id
	 * @return string|array|float|int
	 */
	public function remove_parameter( $key, $id = null, $url = false ) {
		$retval       = $url;
		$settings     = $this->_settings;
		$param_sep    = $settings->router_param_separator;
		$param_prefix = $settings->router_param_prefix ? preg_quote( $settings->router_param_prefix, '#' ) : '';
		$param_slug   = $settings->router_param_slug ? preg_quote( $settings->router_param_slug, '#' ) : false;

		// Is the parameter already part of the request? If so, modify that parameter.
		if ( ( $segment = $this->get_parameter_segment( $key, $id, $url ) ) && is_array( $segment ) ) {
			extract( $segment );

			if ( $source == 'querystring' ) {
				$preg_id  = $id ? '\d+' : preg_quote( $id, '#' );
				$preg_key = preg_quote( $key, '#' );
				$regex    = implode(
					'',
					[
						'#',
						$id ? "{$preg_id}{$param_sep}" : '',
						"(({$param_prefix}{$param_sep})?)?{$preg_key}({$param_sep}|=)[^\/&]+&?#i",
					]
				);
				$qs       = preg_replace( $regex, '', $this->get_router()->get_querystring() );
				$this->get_router()->set_querystring( $qs );
				$retval = $this->get_routed_url();
			} elseif ( $source == 'request_uri' ) {
				$uri = $this->get_app_request_uri();
				$uri = $this->join_paths( explode( $segment, $uri ) );
				if ( $settings->router_param_slug && preg_match( "#{$param_slug}/?$#i", $uri, $match ) ) {
					$retval = $this->remove_url_segment( $match[0], $retval );
				}
				$this->set_app_request_uri( $uri );
				$retval = $this->get_routed_url();
			} else {
				$retval = $this->join_paths( explode( $segment, $url ) );
				if ( $settings->router_param_slug && preg_match( "#/{$param_slug}$#i", $retval, $match ) ) {
					$retval = $this->remove_url_segment( $match[0], $retval );
				}
			}
		}

		if ( is_string( $retval ) ) {
			$retval = rtrim( $retval, ' ?&' );
		}

		$retval = ( is_null( $retval ) or is_numeric( $retval ) or is_array( $retval ) ) ? $retval : Router::esc_url( $retval );

		$retval = $this->add_post_permalink_to_url( $retval );
		$retval = $this->_set_tag_cloud_parameters( $retval, $key, $id );

		if ( preg_match( "#(/{$param_slug}/.*)album--#", $retval, $matches ) ) {
			$retval = str_replace( $matches[0], $matches[1], $retval );
		}

		if ( preg_match( "#(/{$param_slug}/.*)gallery--#", $retval, $matches ) ) {
			$retval = str_replace( $matches[0], $matches[1], $retval );
		}

		$retval = $this->_set_ngglegacy_page_parameter( $retval, $key );

		// For some reason, we're not removing our parameters the way we should. Our routing system seems to be
		// a bit broken and so I'm adding an exception here.
		// TODO: Our parameter manipulations need to be flawless. Look into route cause.
		if ( $key === 'show' ) {
			$regex = '#/' . $param_slug . '.*(/?(slideshow|thumbnails|imagebrowser)/?)#';
			if ( preg_match( $regex, $retval, $matches ) ) {
				$retval = str_replace( $matches[1], '', $retval );
			}
		}

		return $retval;
	}

	public function _set_tag_cloud_parameters( $retval, $key, $id = null ) {
		// Get the settings manager.
		$settings = Settings::get_instance();

		// Create the regex pattern.
		$sep = preg_quote( $settings->get( 'router_param_separator', '--' ), '#' );
		if ( $id ) {
			$id = preg_quote( $id, '#' ) . $sep;
		}
		$prefix = preg_quote( $settings->get( 'router_param_prefix', '' ), '#' );
		$regex  = implode(
			'',
			[
				'#//?',
				$id ? "({$id})?" : "(\w+{$sep})?",
				"($prefix)?gallerytag{$sep}([\w\-_]+)/?#",
			]
		);

		// Replace any page parameters with the ngglegacy equivalent.
		if ( preg_match( $regex, $retval, $matches ) ) {
			$retval = rtrim( str_replace( $matches[0], "/tags/{$matches[3]}/", $retval ), '/' );
		}

		return $retval;
	}

	public function _set_ngglegacy_page_parameter( $retval, $key, $value = null, $id = null, $use_prefix = null ) {
		// Get the settings manager.
		$settings = Settings::get_instance();

		// Create regex pattern.
		$param_slug = preg_quote( $settings->get( 'router_param_slug', 'nggallery' ), '#' );

		if ( $key == 'nggpage' ) {
			$regex = "#(/{$param_slug}/.*)(/?page/\\d+/?)(.*)#";
			if ( preg_match( $regex, $retval, $matches ) ) {
				$new_segment = $value ? "/page/{$value}" : '';
				$retval      = rtrim(
					str_replace(
						$matches[0],
						rtrim( $matches[1], '/' ) . $new_segment . ltrim( $matches[3], '/' ),
						$retval
					),
					'/'
				);
			}
		}

		// Convert the nggpage parameter to a slug.
		if ( preg_match( "#(/{$param_slug}/.*)nggpage--(.*)#", $retval, $matches ) ) {
			$retval = rtrim( str_replace( $matches[0], rtrim( $matches[1], '/' ) . '/page/' . ltrim( $matches[2], '/' ), $retval ), '/' );
		}

		// Convert the show parameter to a slug.
		if ( preg_match( "#(/{$param_slug}/.*)show--(.*)#", $retval, $matches ) ) {
			$retval = rtrim( str_replace( $matches[0], rtrim( $matches[1], '/' ) . '/' . $matches[2], $retval ), '/' );
			$retval = str_replace( NGG_BASIC_SLIDESHOW, 'slideshow', $retval );
			$retval = str_replace( NGG_BASIC_THUMBNAILS, 'thumbnails', $retval );
			$retval = str_replace( NGG_BASIC_IMAGEBROWSER, 'imagebrowser', $retval );
		}

		return $retval;
	}

	public function _set_search_page_parameter( $retval, $key, $value = null, $id = null, $use_prefix = null ) {
		$settings   = Settings::get_instance();
		$param_slug = preg_quote( $settings->router_param_slug, '#' );

		// Convert the nggsearch parameter to a slug.
		if ( preg_match( "#(/{$param_slug}/.*)nggsearch--(.*)#", $retval, $matches ) ) {
			$retval = rtrim(
				str_replace( $matches[0], rtrim( $matches[1], '/' ) . '/search/' . ltrim( $matches[2], '/' ), $retval ),
				'/'
			);
		}

		if ( preg_match( "#(/{$param_slug}/.*)tagfilter--(.*)#", $retval, $matches ) ) {
			$retval = rtrim(
				str_replace( $matches[0], rtrim( $matches[1], '/' ) . '/tagfilter/' . ltrim( $matches[2], '/' ), $retval ),
				'/'
			);
		}

		return $retval;
	}

	/**
	 * Sets the value of a particular parameter
	 *
	 * @param string      $key
	 * @param mixed       $value
	 * @param mixed       $id (optional)
	 * @param bool        $use_prefix (optional)
	 * @param bool|string $url (optional)
	 * @return string
	 */
	public function set_parameter_value( $key, $value, $id = null, $use_prefix = false, $url = false ) {
		// Get the settings manager.
		$settings   = $this->_settings;
		$param_slug = $settings->router_param_slug;

		// it's difficult to make NextGEN's router work with spaces in parameter names without just encoding them
		// directly first; replace nggsearch's parameter's spaces with %20.
		$url = preg_replace_callback(
			"#(/{$param_slug}/.*)nggsearch--(.*)#",
			function ( $matches ) {
				return str_replace( ' ', '%20', $matches[0] );
			},
			$url
		);

		// Remove the parameter from both the querystring and request uri.
		$retval = $this->remove_parameter( $key, $id, $url );

		// We're modifying a url passed in.
		if ( $url ) {
			$parts = parse_url( $retval );
			if ( ! isset( $parts['path'] ) ) {
				$parts['path'] = '';
			}
			$parts['path'] = $this->join_paths(
				$parts['path'],
				$param_slug && strpos( $parts['path'], $param_slug ) === false ? $param_slug : '',
				$this->create_parameter_segment( $key, $value, $id, $use_prefix )
			);
			$parts['path'] = str_replace( '//', '/', $parts['path'] );
			$retval        = $this->construct_url_from_parts( $parts );
		}

		// We're modifying the current request.
		else {
			// This parameter is being appended to the current request uri.
			$this->add_parameter_to_app_request_uri( $key, $value, $id, $use_prefix );

			// Return the new full url.
			$retval = $this->get_routed_url();
		}

		$retval = ( is_null( $retval ) || is_numeric( $retval ) || is_array( $retval ) ) ? $retval : Router::esc_url( $retval );
		$retval = $this->_set_tag_cloud_parameters( $retval, $key, $id );
		$retval = $this->_set_ngglegacy_page_parameter( $retval, $key, $value, $id, $use_prefix );
		$retval = $this->_set_search_page_parameter( $retval, $key, $value, $id, $use_prefix );

		return $retval;
	}
}
