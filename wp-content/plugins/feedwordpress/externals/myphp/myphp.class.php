<?php
/**
 * MyPHP: handy general utility functions, for things that PHP should do, but
 * doesn't do (yet).
 *
 * @package MyPHP
 * @version 2014.0805
 */
if ( ! class_exists( 'MyPHP' ) ) :
	class MyPHP {
		/**
		 * MyPHP::param: For dealing with HTTP GET/POST parameters sent
		 * to us as input.
		 *
		 * @param string $key The name of the GET/POST parameter
		 * @param mixed $default Default to return if parameter is unset
		 * @param string $type 'GET', 'POST' or 'REQUEST' (=both GET and POST)
		 * @return mixed The value of the named parameter, or the fallback
		 *    in $default if there is no value set for that param name.
		 */
		static public function param( $key, $default = NULL, $type = 'REQUEST' ) {
			// PHP 5.4 introduces "just-in-time" initialization of
			// $GLOBALS. Which seems to me largely to defeat the
			// purpose of the $GLOBALS array. But whatever. Force
			// $_GET, $_POST, and $_REQUEST into the array.
			global $_GET, $_POST, $_REQUEST;

			$where = '_' . strtoupper( $type );
			$ret = $default;
			if ( isset( $GLOBALS[ $where ] ) and is_array( $GLOBALS[ $where ] ) ) :
				if ( isset( $GLOBALS[ $where ][ $key ] ) ) :
					$ret = $GLOBALS[ $where ][ $key ];

					// Magic quotes were just like the stupidest
					// thing ever. (<http://php.net/manual/en/security.magicquotes.php>)
					// Happily they were DEPRECATED as of PHP 5.3.0,
					// and REMOVED as of PHP 5.4.0, so we don't need
					// to worry about them anymore.
				endif;
			endif;

			return $ret;
		} /* MyPHP::param() */

		/**
		 * MyPHP::post: For dealing with HTTP POST parameters sent as input
		 *
		 * @param string $key The name of the POST parameter
		 * @param mixed $default Default to return if parameter is unset
		 * @return mixed The value of the named parameter, or the fallback
		 *    in $default if there is no value set for that param name.
		 */
		static function post( $key, $default = NULL ) {
			return self::param( $key, $default, 'POST' );
		} /* MyPHP::post() */

		/**
		 * MyPHP::post: For dealing with HTTP GET parameters sent as input
		 *
		 * @param string $key The name of the GET parameter
		 * @param mixed $default Default to return if parameter is unset
		 * @return mixed The value of the named parameter, or the fallback
		 *    in $default if there is no value set for that param name.
		 */
		static function get( $key, $default = NULL ) {
			return self::param( $key, $default, "GET" );
		} /* MyPHP::get() */

		/**
		 * MyPHP::request: For dealing with HTTP GET/POST parameters
		 * sent as input. This method checks both GET parameters and POST
		 * parameters and will return values from either.
		 *
		 * @param string $key The name of the GET/POST parameter
		 * @param mixed $default Default to return if parameter is unset
		 * @return mixed The value of the named parameter, or the fallback
		 *    in $default if there is no value set for that param name.
		 */
		static function request( $key, $default = NULL ) {
			return self::param( $key, $default, "REQUEST" );
		} /* MyPHP::request () */

		/**
		 * MyPHP::request: Makes sure that we strip all slashes and double slashes
		 * we happen to encounter on our strings.
		 *
		 * The function is recursive and gets as deep as it must.
		 *
		 * @param  array|object|string $value Item to de-slash.
		 *
		 * @return array|object|string De-slashed item.
		 */
		static function stripslashes_deep( $value ) {
			if ( is_array( $value ) ) {
				$value = array_map( array( __CLASS__, 'stripslashes_deep' ), $value );
			} elseif ( is_object( $value ) ) {
				$vars = get_object_vars( $value );
				foreach ( $vars as $key => $data ) {
					$value->{$key} = stripslashes_deep( $data );
				}
			} else {
				$value = stripslashes( $value );
			}
			return $value;
		} /* MyPHP::stripslashes_deep () */

		/**
		 * Constructs an URL with the list of passed parameters.
		 *
		 * @param  string $url    Base URL.
		 * @param  Array  $params Variable parameters.
		 *
		 * @return string
		 */
		static function url( $url, $params = array() ) {
			$sep = '?';
			if ( strpos( $url, '?' ) !== false ) :
				$sep = '&';
			endif;
			$url .= $sep . self::to_http_post( $params );

			return $url;
		} /* MyPHP::url () */

		/**
		 * MyPHP::to_http_post () -- convert a hash table or object into
		 * an application/x-www-form-urlencoded query string
		 *
		 * @param  mixed $params Hash table/object to convert.
		 *
		 * @return string The application/x-www-form-urlencoded query string.
		 */
		static function to_http_post( $params ) {
			$getQuery = array();
			foreach ( $params as $key => $value ) :
				if ( is_scalar( $value ) ) :
					$getQuery[] = urlencode( $key ) . '=' . urlencode( $value );
				else :
					// Make some attempt to deal with array and
					// object members. Really we should descend
					// recursively to get the whole thing....
					foreach ((array) $value as $k => $v ) :
						$getQuery[] = urlencode( $key . '[' . $k . ']' ) . '=' . urlencode( $v );
					endforeach;
				endif;
			endforeach;
			return implode( "&", $getQuery );
		} /* MyPHP::to_http_post () */

		/**
		 * MyPHP::val(): Captures the output of var_dump() to a string,
		 * with some optional filtering removing newlines and replacing
		 * them with spaces) applied.
		 *
		 * @note As far as _I_ understand regexes, this replaces _multiple_
		 * consecutive whitespace elements with a single space — which is
		 * not quite what the description says! However, since the result of
		 * var_dump() has a reasonably predictable output, so this _may_ work
		 * as intended...(gwyneth 20230916)
		 *
		 * @param  mixed  $v           Value to dump to a string representation.
		 * @param  bool   $no_newlines Whether to filter out newline chars.
		 *
		 * @return string Captured & cleaned-up output of var_dump().
		 */
		static function val( $v, $no_newlines = false ) {
			ob_start();
			var_dump( $v );
			$out = ob_get_contents(); ob_end_clean();
			if ( $no_newlines ) :
				$out = preg_replace( '/\s+/', " ", $out );
			endif;
			return $out;
		} /* MyPHP::val () */
	} /* class MyPHP */
endif;
