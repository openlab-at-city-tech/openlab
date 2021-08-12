<?php
/**
 * WP to Twitter Twitter Feed Class
 *
 * @category Widgets
 * @package  WP to Twitter
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'class-wpt-twitteroauth.php' );

/**
 * Based on Version 2.0.3, Twitter Feed for Developers by Storm Consultancy (Liam Gladdy)
 * The base class for the storm twitter feed for developers.
 */
class WPT_TwitterFeed {

	/**
	 * Default feed settings.
	 *
	 * @var $defaults.
	 */
	private $defaults = array(
		'directory'    => '',
		'key'          => '',
		'secret'       => '',
		'token'        => '',
		'token_secret' => '',
		'screenname'   => false,
		'cache_expire' => 1800,
	);

	/**
	 * Last error, if any.
	 *
	 * @var $st_last_error
	 */
	public $st_last_error = false;

	/**
	 * Constructor.
	 *
	 * @param array $args Arguments; merged with defaults.
	 */
	function __construct( $args = array() ) {
		$this->defaults = array_merge( $this->defaults, $args );
	}

	/**
	 * Convert arguments into a string.
	 *
	 * @return print_r of arguments.
	 */
	function __toString() {
		return print_r( $this->defaults, true );
	}

	/**
	 * Get Tweets for a given screen name.
	 *
	 * @param int    $count Number of Tweets to fetch.
	 * @param string $screenname Twitter account feed to fetch.
	 * @param array  $options Options to apply for display of feed.
	 *
	 * @return Tweets or error message.
	 */
	function get_tweets( $count = 20, $screenname = false, $options = false ) {
		if ( $count > 20 ) {
			/**
			 * Filters the max feed count. Default is 20, but you can change it.
			 *
			 * @param integer 20 - Default value
			 * @param integer $count - Widget variable
			 * @return integer
			 */
			$count = apply_filters( 'wpt_feed_max_count', 20, $count );
		}
		if ( $count < 1 ) {
			$count = 1;
		}

		$default_options = array(
			'trim_user'       => true,
			'exclude_replies' => true,
			'include_rts'     => false,
		);

		if ( false === $options || ! is_array( $options ) ) {
			$options = $default_options;
		} else {
			$options = array_merge( $default_options, $options );
		}

		if ( false === $screenname ) {
			$screenname = get_option( 'wtt_twitter_username' );
		}

		$result = $this->check_valid_cache( $screenname, $options );
		if ( false !== $result ) {
			return $this->crop_tweets( $result, $count );
		}

		// If we're here, we need to load.
		$result = $this->oauth_get_tweets( $screenname, $options );

		if ( is_object( $result ) && isset( $result->error ) ) {
			$last_error = $result->error;

			return array( 'error' => 'Twitter said: ' . $last_error );
		} else {
			return $this->crop_tweets( $result, $count );
		}

	}

	/**
	 * Crop list of Tweets to display correct number of items.
	 *
	 * @param array $result Full query result.
	 * @param int   $count Tweets to show.
	 *
	 * @return array
	 */
	private function crop_tweets( $result, $count ) {
		if ( is_array( $result ) ) {
			return array_slice( $result, 0, $count );
		} else {
			return array();
		}
	}

	/**
	 * Locate cache.
	 */
	private function get_cache_location() {
		return $this->defaults['directory'] . '.tweetcache';
	}

	/**
	 * Hash options so cache is unique.
	 *
	 * @param array $options Display options.
	 *
	 * @return md5 hash.
	 */
	private function get_options_hash( $options ) {
		$hash = md5( serialize( $options ) );

		return $hash;
	}

	/**
	 * Save cache to file.
	 *
	 * @param string $file Cache file location.
	 * @param string $cache Data to save.
	 */
	private function save_cache( $file, $cache ) {
		$is_writable = wpt_is_writable( $file );
		if ( $is_writable ) {
			file_put_contents( $file, $cache );
		} else {
			set_transient( 'wpt_cache', $cache, $this->defaults['cache_expire'] );
		}
	}

	/**
	 * Delete cache.
	 *
	 * @param string $file File name.
	 */
	private function delete_cache( $file ) {
		$is_writable = wpt_is_writable( $file );
		if ( $is_writable ) {
			unlink( $file );
		} else {
			delete_transient( 'wpt_cache' );
		}
	}

	/**
	 * Fetch and verify cache.
	 *
	 * @param string $screenname Name to get cache for.
	 * @param array  $options Options for cache being fetched.
	 *
	 * @return boolean or cache contents.
	 */
	private function check_valid_cache( $screenname, $options ) {
		$delete_cache = get_option( 'wpt_delete_cache' );
		$file         = $this->get_cache_location();

		if ( 'true' === $delete_cache ) {
			update_option( 'wpt_delete_cache', 'false' );
			$this->delete_cache( $file );
		}

		if ( is_file( $file ) ) {
			$cache = file_get_contents( $file );
			$cache = json_decode( $cache, true );
			if ( ! isset( $cache ) ) {
				unlink( $file );

				return false;
			}
		} else {
			$cache = get_transient( 'wpt_cache' );
			$cache = json_decode( $cache, true );
			if ( ! isset( $cache ) ) {
				return false;
			}
		}
		$cachename = $screenname . '-' . $this->get_options_hash( $options );

		// Check if we have a cache for the user.
		if ( ! isset( $cache[ $cachename ] ) ) {
			return false;
		}

		if ( ! isset( $cache[ $cachename ]['time'] ) || ! isset( $cache[ $cachename ]['tweets'] ) ) {
			unset( $cache[ $cachename ] );
			$this->save_cache( $file, json_encode( $cache ) );

			return false;
		}

		if ( $cache[ $cachename ]['time'] < ( time() - $this->defaults['cache_expire'] ) ) {
			$result = $this->oauth_get_tweets( $screenname, $options );
			if ( ! isset( $result->error ) ) {
				return $result;
			}
		}

		return $cache[ $cachename ]['tweets'];
	}

	/**
	 * Fetch Tweets from Twitter.
	 *
	 * @param string $screenname Username.
	 * @param array  $options Array of display options.
	 *
	 * @return Tweets.
	 */
	private function oauth_get_tweets( $screenname, $options ) {
		$key          = $this->defaults['key'];
		$secret       = $this->defaults['secret'];
		$token        = $this->defaults['token'];
		$token_secret = $this->defaults['token_secret'];
		$cachename    = $screenname . '-' . $this->get_options_hash( $options );
		$options      = array_merge(
			$options,
			array(
				'screen_name' => $screenname,
				'count'       => 20,
			)
		);

		if ( empty( $key ) ) {
			return array( 'error' => __( 'Missing Consumer Key - Check settings', 'wp-to-twitter' ) );
		}
		if ( empty( $secret ) ) {
			return array( 'error' => __( 'Missing Consumer Secret - Check settings', 'wp-to-twitter' ) );
		}
		if ( empty( $token ) ) {
			return array( 'error' => __( 'Missing Access Token - Check settings', 'wp-to-twitter' ) );
		}
		if ( empty( $token_secret ) ) {
			return array( 'error' => __( 'Missing Access Token Secret - Check settings', 'wp-to-twitter' ) );
		}
		if ( empty( $screenname ) ) {
			return array( 'error' => __( 'Missing Twitter Feed Screen Name - Check settings', 'wp-to-twitter' ) );
		}

		$connection = new wpt_TwitterOAuth( $key, $secret, $token, $token_secret );

		if ( isset( $options['search'] ) ) {
			$args = array(
				'q'           => urlencode( $options['search'] ),
				'result_type' => urlencode( $options['result_type'] ),
			);
			if ( '' !== $options['geocode'] ) {
				$args['geocode'] = urlencode( $options['geocode'] );
			}
			$url    = add_query_arg( $args, 'https://api.twitter.com/1.1/search/tweets.json' );
			$result = $connection->get( $url, $options );
		} else {
			$result = $connection->get( 'https://api.twitter.com/1.1/statuses/user_timeline.json', $options );
		}
		$result = json_decode( $result );
		if ( isset( $options['search'] ) ) {
			if ( ! method_exists( $result, 'errors' ) ) {
				$result = ( is_object( $result ) ) ? $result->statuses : '';
			} else {
				$errors = $result->errors;
				$return = '';
				foreach ( $errors as $error ) {
					$return .= "<li>$error->message</li>";
				}
				echo '<ul>' . $return . '</ul>';
				return;
			}
		}
		if ( is_file( $this->get_cache_location() ) ) {
			$cache = json_decode( file_get_contents( $this->get_cache_location() ), true );
		}

		if ( ! isset( $result->error ) ) {
			$cache[ $cachename ]['time']   = time();
			$cache[ $cachename ]['tweets'] = $result;
			$file                          = $this->get_cache_location();
			$this->save_cache( $file, json_encode( $cache ) );
		} else {
			if ( is_array( $result ) && isset( $result['errors'][0] ) && isset( $result['errors'][0]['message'] ) ) {
				// Translators: Error message.
				$last_error          = '[' . gmdate( 'r' ) . '] ' . sprintf( __( 'Twitter error: %s', 'wp-to-twitter' ), $result['errors'][0]['message'] );
				$this->st_last_error = $last_error;
			} else {
				$last_error          = '[' . gmdate( 'r' ) . ']' . __( 'Twitter returned an invalid response. It is probably down.', 'wp-to-twitter' );
				$this->st_last_error = $last_error;
			}
		}
		// Run an action on the results output from the Twitter widget query.
		do_action( 'wpt_process_tweets', $result, $screenname, $options );

		return $result;
	}
}
