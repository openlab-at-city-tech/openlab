<?php

namespace Imagely\NGG\Util;

use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\Transient;

class Router {

	public static $_instances = [];
	public static $_lookups   = [];

	public $context;
	public $_request_method = '';
	public $_routed_app;

	public $_apps        = [];
	public $_default_app = null;

	public static $use_canonical_redirect = true;
	public static $use_old_slugs          = true;

	public function __construct( $context = false ) {
		if ( ! $context || $context === 'all' ) {
			$this->context = '/';
		}

		$this->_request_method = ! empty( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : null;

		self::$_lookups = Transient::fetch( $this->_get_cache_key(), [] );

		// TODO: only register this once.
		register_shutdown_function( [ $this, 'cache_lookups' ] );
	}

	/**
	 * @param string|false $context (optional)
	 * @return Router
	 */
	public static function get_instance( $context = false ) {
		if ( ! isset( self::$_instances[ $context ] ) ) {
			self::$_instances[ $context ] = new Router( $context );
		}
		return self::$_instances[ $context ];
	}

	public function register_hooks() {
		\add_action( 'template_redirect', [ $this, 'restore_request_uri' ], 1 );

		// These two things cause conflicts in NGG. So we temporarily disable them and then reactivate them, if they
		// were used, in the restore_request_uri() method.
		if ( \has_action( 'template_redirect', 'wp_old_slug_redirect' ) ) {
			\remove_action( 'template_redirect', 'wp_old_slug_redirect' );
		}
		if ( \has_action( 'template_redirect', 'redirect_canonical' ) ) {
			\remove_action( 'template_redirect', 'redirect_canonical' );
		}

		\add_action( 'the_post', [ $this, 'fix_page_parameter' ] );
	}

	/**
	 * When WordPress sees a url like http://foobar.com/nggallery/page/2/, it thinks that it is an
	 * invalid url. Therefore, we modify the request uri before WordPress parses the request, and then
	 * restore the request uri afterwards
	 */
	public function restore_request_uri() {
		if ( isset( $_SERVER['NGG_ORIG_REQUEST_URI'] ) ) {
			$request_uri              = $_SERVER['NGG_ORIG_REQUEST_URI'];
			$_SERVER['UNENCODED_URL'] = $_SERVER['HTTP_X_ORIGINAL_URL'] = $_SERVER['REQUEST_URI'] = $request_uri;
			if ( isset( $_SERVER['ORIG_PATH_INFO'] ) ) {
				$_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];
			}
		} else {
			// This is the proper behavior, but it causes problems with WPML.
			if ( self::$use_old_slugs ) {
				\wp_old_slug_redirect();
			}
			if ( self::$use_canonical_redirect ) {
				\redirect_canonical();
			}
		}
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
				$parts['path'] = str_replace( '//', '/', str_replace( $matches[0], '', $parts['path'] ) );
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
			} else {
				// We're starting the process..
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

	public function join_querystrings() {
		$retval = [];
		$params = func_get_args();
		$parts  = $this->_flatten_array( $params );
		foreach ( $parts as $part ) {
			$part = explode( '&', $part );
			foreach ( $part as $segment ) {
				$segment        = explode( '=', $segment );
				$key            = $segment[0];
				$value          = isset( $segment[1] ) ? $segment[1] : '';
				$retval[ $key ] = $value;

			}
		}
		return $this->assoc_array_to_querystring( $retval );
	}

	public function assoc_array_to_querystring( $arr ) {
		$retval = [];
		foreach ( $arr as $key => $val ) {
			if ( strlen( $key ) ) {
				$retval[] = strlen( $val ) ? "{$key}={$val}" : $key;
			}
		}
		return implode( '&', $retval );
	}

	/**
	 * Constructs an url from individual parts, created by parse_url
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

	public function set_routed_app( $app ) {
		$this->_routed_app = $app;
	}

	/**
	 * @return RoutingApp
	 */
	public function get_routed_app() {
		return $this->_routed_app ? $this->_routed_app : $this->get_default_app();
	}

	/**
	 * @return RoutingApp
	 */
	public function get_default_app() {
		if ( is_null( $this->_default_app ) ) {
			$this->_default_app = $this->create_app();
		}

		return $this->_default_app;
	}

	public function route( $patterns, $handler = false ) {
		$this->get_default_app()->route( $patterns, $handler );
	}

	public function rewrite( $src, $dst, $redirect = false ) {
		$this->get_default_app()->rewrite( $src, $dst, $redirect );
	}

	public function get_parameter( $key, $prefix = null, $default = null ) {
		return $this->get_routed_app()->get_parameter( $key, $prefix, $default );
	}

	public function param( $key, $prefix = null, $default = null ) {
		return $this->get_parameter( $key, $prefix, $default );
	}

	public function has_parameter_segments() {
		return $this->get_routed_app()->has_parameter_segments();
	}

	public function passthru() {
		$this->get_default_app()->passthru();
	}

	public function get_url( $uri = '/', $with_qs = true, $site_url = false ) {
		static $cache = [];

		$key = implode( '|', [ $uri, $with_qs, $site_url ] );

		if ( isset( $cache[ $key ] ) ) {
			return $cache[ $key ];
		} else {
			$retval = $this->join_paths( $this->get_base_url( $site_url ), $uri );
			if ( $with_qs ) {
				$parts = parse_url( $retval );
				if ( ! isset( $parts['query'] ) ) {
					$parts['query'] = $this->get_querystring();
				} else {
					$parts['query'] = $this->join_querystrings( $parts['query'], $this->get_querystring() );
				}

				$retval = $this->construct_url_from_parts( $parts );

			}

			$retval = str_replace( '\\', '/', $retval );

			// Determine whether the url is a directory or file on the filesystem
			// If so, then we do NOT need /index.php as part of the url.
			$base_url = $this->get_base_url();
			$filename = str_replace(
				$base_url,
				\Imagely\NGG\Util\Filesystem::get_instance()->get_document_root(),
				$retval
			);

			if ( $retval && $retval != $base_url && @file_exists( $filename ) ) {
				// Remove index.php from the url.
				$retval = $this->remove_url_segment( '/index.php', $retval );

				// Static urls don't end with a slash.
				$retval = untrailingslashit( $retval );
			}

			$cache[ $key ] = $retval;
			return $retval;
		}
	}

	/**
	 * Returns a static url
	 *
	 * @param string       $path
	 * @param string|false $module (optional)
	 * @return string
	 */
	public function get_static_url( $path, $module = false ) {
		return \Imagely\NGG\Display\StaticPopeAssets::get_url( $path, $module );
	}

	/**
	 * Gets the routed url
	 *
	 * @return string
	 */
	public function get_routed_url() {
		$retval = $this->get_url( $this->get_request_uri() );

		if ( ( $app = $this->get_routed_app() ) ) {
			$retval = $this->get_url( $app->get_app_uri() );
		}

		return $retval;
	}

	/**
	 * Gets the base url for the router
	 *
	 * @param bool $type
	 * @return string
	 */
	public function get_base_url( $type = false ) {
		if ( $this->has_cached_base_url( $type ) ) {
			return $this->get_cached_base_url( $type );
		}

		return $this->get_computed_base_url( $type );
	}

	/**
	 * Determines if the current request is over HTTPs or not
	 */
	public function is_https() {
		return (
			( ! empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' ) ||
			( ! empty( $_SERVER['HTTP_USESSL'] ) && strtolower( $_SERVER['HTTP_USESSL'] ) !== 'off' ) ||
			( ! empty( $_SERVER['REDIRECT_HTTPS'] ) && strtolower( $_SERVER['REDIRECT_HTTPS'] ) !== 'off' ) ||
			( ! empty( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] == 443 )
		);
	}

	/**
	 * Serve request using defined Routing Apps
	 */
	public function serve_request() {
		$served = false;

		// iterate over all apps, and serve the route.
		/** @var \Imagely\NGG\Util\RoutingApp $app */
		foreach ( $this->get_apps() as $app ) {
			if ( ( $served = $app->serve_request( $this->context ) ) ) {
				break;
			}
		}

		return $served;
	}

	/**
	 * Gets the querystring of the current request
	 *
	 * @return null|bool
	 */
	public function get_querystring() {
		return isset( $_SERVER['QUERY_STRING'] ) ? $_SERVER['QUERY_STRING'] : null;
	}

	public function set_querystring( $value ) {
		$_SERVER['QUERY_STRING'] = $value;
	}

	/**
	 * Gets the request for the router
	 *
	 * @param bool $with_params (optional) Default = true
	 * @return string
	 */
	public function get_request_uri( $with_params = true ) {
		if ( ! empty( $_SERVER['NGG_ORIG_REQUEST_URI'] ) ) {
			$retval = $_SERVER['NGG_ORIG_REQUEST_URI'];
		} elseif ( ! empty( $_SERVER['PATH_INFO'] ) ) {
			$retval = $_SERVER['PATH_INFO'];
		} else {
			$retval = $_SERVER['REQUEST_URI'];
		}

		// Remove the querystring.
		if ( ( $index = strpos( $retval, '?' ) ) !== false ) {
			$retval = substr( $retval, 0, $index );
		}

		// Remove the router's context.
		$retval = preg_replace( '#^' . preg_quote( $this->context, '#' ) . '#', '', $retval );

		// Remove the params.
		if ( ! $with_params ) {
			$retval = $this->strip_param_segments( $retval );
		}

		// Ensure that request uri starts with a slash.
		if ( strpos( $retval, '/' ) !== 0 ) {
			$retval = "/{$retval}";
		}

		return $retval;
	}

	/**
	 * Gets the method of the HTTP request
	 *
	 * @return string
	 */
	public function get_request_method() {
		return $this->_request_method;
	}

	/**
	 * @param string $name
	 * @return RoutingApp
	 */
	public function create_app( $name = '/' ) {
		$app           = new RoutingApp( $name );
		$this->_apps[] = $app;
		return $app;
	}

	/**
	 * Gets a list of apps registered for the router
	 *
	 * @return array
	 */
	public function get_apps() {
		usort( $this->_apps, [ &$this, '_sort_apps' ] );
		return array_reverse( $this->_apps );
	}

	/**
	 * Sorts apps.This is needed because we want the most specific app to be executed first
	 *
	 * @return int
	 */
	public function _sort_apps( RoutingApp $a, RoutingApp $b ) {
		return strnatcmp( $a->context, $b->context );
	}

	public function _get_cache_key() {
		return Transient::create_key( 'WordPress-Router', 'get_base_url' );
	}

	public function cache_lookups() {
		Transient::update( $this->_get_cache_key(), self::$_lookups );
	}

	public function has_cached_base_url( $type = false ) {
		return isset( self::$_lookups[ $type ] );
	}

	public function get_cached_base_url( $type = false ) {
		return self::$_lookups[ $type ];
	}

	public function get_computed_base_url( $site_url = false ) {
		$retval            = null;
		$add_index_dot_php = true;

		if ( in_array( $site_url, [ true, 'site' ], true ) ) {
			$retval = site_url();
		} elseif ( in_array( $site_url, [ false, 'home' ], true ) ) {
			$retval = home_url();
		} elseif ( in_array( $site_url, [ 'plugins', 'plugin' ], true ) ) {
			$retval            = plugins_url();
			$add_index_dot_php = false;
		} elseif ( in_array( $site_url, [ 'plugins_mu', 'plugin_mu' ], true ) ) {
			$retval            = WPMU_PLUGIN_URL;
			$retval            = set_url_scheme( $retval );
			$retval            = apply_filters( 'plugins_url', $retval, '', '' );
			$add_index_dot_php = false;
		} elseif ( in_array( $site_url, [ 'templates', 'template', 'themes', 'theme' ], true ) ) {
			$retval            = get_template_directory_uri();
			$add_index_dot_php = false;
		} elseif ( in_array( $site_url, [ 'styles', 'style', 'stylesheets', 'stylesheet' ], true ) ) {
			$retval            = get_stylesheet_directory_uri();
			$add_index_dot_php = false;
		} elseif ( in_array( $site_url, [ 'content' ], true ) ) {
			$retval            = content_url();
			$add_index_dot_php = false;
		} elseif ( in_array( $site_url, [ 'root' ], true ) ) {
			$retval = get_option( 'home' );
			if ( is_ssl() ) {
				$scheme = 'https';
			} else {
				$scheme = parse_url( $retval, PHP_URL_SCHEME );
			}
			$retval = set_url_scheme( $retval, $scheme );
		} elseif ( in_array( $site_url, [ 'gallery', 'galleries' ], true ) ) {
			$root_type         = NGG_GALLERY_ROOT_TYPE;
			$add_index_dot_php = false;
			if ( $root_type === 'content' ) {
				$retval = content_url();
			} else {
				$retval = site_url();
			}
		} else {
			$retval = site_url();
		}

		if ( $add_index_dot_php ) {
			$retval = $this->_add_index_dot_php_to_url( $retval );
		}

		if ( $this->is_https() ) {
			$retval = preg_replace( '/^http:\\/\\//i', 'https://', $retval, 1 );
		}

		return $retval;
	}

	public function _add_index_dot_php_to_url( $url ) {
		if ( strpos( $url, '/index.php' ) === false ) {
			$pattern = get_option( 'permalink_structure' );
			if ( ! $pattern or strpos( $pattern, '/index.php' ) !== false ) {
				$url = $this->join_paths( $url, '/index.php' );
			}
		}

		return $url;
	}

	/**
	 * This code was originally added to correct a bug in Pro 1.0.10 and was meant to be temporary. However now the
	 * albums' pagination relies on this to function correctly, and fixing it properly would require more time than
	 * it is worth.
	 */
	public function fix_page_parameter() {
		global $post;

		if ( $post
			&& is_object( $post )
			&& is_string( $post->content )
			&& ( strpos( $post->content, '<!--nextpage-->' ) === false )
			&& ( strpos( $_SERVER['REQUEST_URI'], '/page/' ) !== false )
			&& preg_match( '#/page/(\\d+)#', $_SERVER['REQUEST_URI'], $match ) ) {
			$_REQUEST['page'] = $match[1];
		}
	}

	/**
	 * Checks and cleans a URL. This function is forked from WordPress.
	 *
	 * A number of characters are removed from the URL. If the URL is for displaying (the default behaviour) ampersands
	 * are also replaced. The 'clean_url' filter is applied to the returned cleaned URL.
	 *
	 * @param string $url The URL to be cleaned.
	 * @param array  $protocols Optional. An array of acceptable protocols.
	 * @param string $context Use esc_url_raw() for database usage.
	 * @return string The cleaned $url after the 'clean_url' filter is applied.
	 */
	public static function esc_url( $url, $protocols = null, $context = 'display' ) {
		$original_url = $url;

		if ( '' == $url ) {
			return $url;
		}
		$url   = preg_replace( '|[^a-z0-9 \\-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url );
		$strip = [ '%0d', '%0a', '%0D', '%0A' ];
		$url   = \_deep_replace( $strip, $url );
		$url   = str_replace( ';//', '://', $url );

		// If the URL doesn't appear to contain a scheme, we presume it needs https:// prepended (unless a relative
		// link starting with /, # or ? or a php file).
		if ( strpos( $url, ':' ) === false && ! in_array( $url[0], [ '/', '#', '?' ] ) && ! preg_match( '/^[a-z0-9-]+?\.php/i', $url ) ) {
			$url = \is_ssl() ? 'https://' : 'http://' . $url;
		}

		// Replace ampersands and single quotes only when displaying.
		if ( 'display' == $context ) {
			$url = \wp_kses_normalize_entities( $url );
			$url = str_replace( '&amp;', '&#038;', $url );
			$url = str_replace( "'", '&#039;', $url );
			$url = str_replace( ' ', '%20', $url );
		}

		if ( '/' === $url[0] ) {
			$good_protocol_url = $url;
		} else {
			if ( ! is_array( $protocols ) ) {
				$protocols = \wp_allowed_protocols();
			}
			$good_protocol_url = \wp_kses_bad_protocol( $url, $protocols );
			if ( strtolower( $good_protocol_url ) != strtolower( $url ) ) {
				return '';
			}
		}

		return \apply_filters( 'clean_url', $good_protocol_url, $original_url, $context );
	}
}
