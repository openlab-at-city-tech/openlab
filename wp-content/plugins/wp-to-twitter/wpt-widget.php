<?php
/**
 * WP to Twitter Widgets
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

require_once( dirname( __FILE__ ) . '/classes/class-wpt-latest-tweets-widget.php' );
require_once( dirname( __FILE__ ) . '/classes/class-wpt-search-tweets-widget.php' );

/**
 * Adds links to the contents of a tweet.
 * Forked from genesis_tweet_linkify, removed target = _blank
 *
 * Takes the content of a tweet, detects @replies, #hashtags, and
 * http:// links, and links them appropriately.
 *
 * @since 0.1
 *
 * @link http://www.snipe.net/2009/09/php-twitter-clickable-links/
 *
 * @param string $text A string representing the content of a tweet.
 * @param array  $opts Array of options.
 * @param array  $tweet Array of Tweet information.
 *
 * @return string Linkified tweet content
 */
function wpt_tweet_linkify( $text, $opts, $tweet ) {
	if ( true === (bool) $opts['show_images'] ) {
		$media = isset( $tweet['entities']['media'] ) ? $tweet['entities']['media'] : false;
		if ( $media ) {
			$media_urls = array();
			if ( ! empty( $media ) ) {
				foreach ( $media as $key => $image ) {
					$media_urls[] = $image['url'];
					$alt          = isset( $tweet['extended_entities']['media'][ $key ]['ext_alt_text'] ) ? $tweet['extended_entities']['media'][ $key ]['ext_alt_text'] : '';
					$text        .= "<img src='" . esc_url( $image['media_url_https'] ) . "' alt='" . esc_attr( $alt ) . "' class='wpt-twitter-image' />";

				}
			}
			if ( ! empty( $media_urls ) ) {
				foreach ( $media_urls as $media_url ) {
					$text = str_replace( $media_url, '', $text );
				}
			}
		}
	}
	$restore = false;
	if ( false !== strpos( $text, '…' ) ) {
		$text    = str_replace( '…', ' ______ ', $text );
		$restore = true;
	}
	$text = ( true === (bool) $opts['links'] ) ? preg_replace( '#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#', '\\1<a href="\\2" rel="nofollow">\\2</a>', $text ) : $text;
	$text = ( true === (bool) $opts['links'] ) ? preg_replace( '#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#', '\\1<a href="http://\\2" rel="nofollow">\\2</a>', $text ) : $text;
	$text = ( true === (bool) $opts['mentions'] ) ? preg_replace( '/@(\w+)/', '<a href="https://twitter.com/\\1" rel="nofollow">@\\1</a>', $text ) : $text;
	$text = ( true === (bool) $opts['hashtags'] ) ? preg_replace( '/#(\w+)/', '<a href="https://twitter.com/search?q=%23\\1" rel="nofollow">#\\1</a>', $text ) : $text;
	$urls = $tweet['entities']['urls'];
	if ( is_array( $urls ) ) {
		foreach ( $urls as $url ) {
			$text = str_replace( ">$url[url]<", ">$url[display_url]<", $text );
		}
	}
	if ( true === $restore ) {
		$text = str_replace( ' ______ ', '…', $text );
	}

	return $text;
}

/**
 * Implement get_tweets
 *
 * @param int    $count How many Tweets.
 * @param string $username Username passed.
 * @param array  $options Widget options.
 *
 * @return Tweets.
 */
function wpt_get_tweets( $count = 20, $username = false, $options = false ) {

	$config['key']          = get_option( 'app_consumer_key' );
	$config['secret']       = get_option( 'app_consumer_secret' );
	$config['token']        = get_option( 'oauth_token' );
	$config['token_secret'] = get_option( 'oauth_token_secret' );
	$config['screenname']   = get_option( 'wtt_twitter_username' );
	$config['cache_expire'] = intval( apply_filters( 'wpt_cache_expire', 1800 ) );
	if ( $config['cache_expire'] < 1 ) {
		$config['cache_expire'] = 1800;
	}
	$config['directory'] = plugin_dir_path( __FILE__ );

	$obj = new WPT_TwitterFeed( $config );
	$res = $obj->get_tweets( $count, $username, $options );
	update_option( 'wpt_tdf_last_error', $obj->st_last_error );

	return $res;

}

/**
 * Generate relevant classes for a Tweet.
 *
 * @param array $tweet Tweet info.
 *
 * @return array classes.
 */
function wpt_generate_classes( $tweet ) {
	// take Tweet array and parse selected options into classes.
	$classes[] = ( $tweet['favorited'] ) ? 'favorited' : '';
	$clasees[] = ( $tweet['retweeted'] ) ? 'retweeted' : '';
	$classes[] = ( isset( $tweet['possibly_sensitive'] ) && $tweet['possibly_sensitive'] ) ? 'sensitive' : '';
	$classes[] = 'lang-' . $tweet['lang'];
	$class     = trim( implode( ' ', $classes ) );

	return $class;
}

/**
 * Get information about user.
 *
 * @param string $twitter_id Twitter screen name.
 *
 * @return array
 */
function wpt_get_user( $twitter_id = false ) {
	if ( ! $twitter_id ) {
		return;
	}
	$options      = array( 'screen_name' => $twitter_id );
	$key          = get_option( 'app_consumer_key' );
	$secret       = get_option( 'app_consumer_secret' );
	$token        = get_option( 'oauth_token' );
	$token_secret = get_option( 'oauth_token_secret' );
	if ( $key && $secret && $token && $token_secret ) {
		$connection = new Wpt_TwitterOAuth( $key, $secret, $token, $token_secret );
		$result     = $connection->get( "https://api.twitter.com/1.1/users/show.json?screen_name=$twitter_id", $options );

		return json_decode( $result );
	} else {
		return array();
	}
}

add_shortcode( 'get_tweets', 'wpt_get_twitter_feed' );
/**
 * Get a Twitter Feed.
 *
 * @param array  $atts Display attributes.
 * @param string $content Fallback content.
 *
 * @return Twitter feed.
 */
function wpt_get_twitter_feed( $atts, $content ) {
	$atts     = shortcode_atts(
		array(
			'id'          => false,
			'num'         => 10,
			'duration'    => 1800,
			'replies'     => 0,
			'rts'         => 1,
			'links'       => 1,
			'mentions'    => 1,
			'hashtags'    => 0,
			'intents'     => 1,
			'source'      => 0,
			'show_images' => 1,
			'hide_header' => 0,
		),
		$atts,
		'get_tweets'
	);
	$instance = array(
		'twitter_id'           => $atts['id'],
		'twitter_num'          => $atts['num'],
		'twitter_duration'     => $atts['duration'],
		'twitter_hide_replies' => $atts['replies'],
		'twitter_include_rts'  => $atts['rts'],
		'link_links'           => $atts['links'],
		'link_mentions'        => $atts['mentions'],
		'link_hashtags'        => $atts['hashtags'],
		'intents'              => $atts['intents'],
		'source'               => $atts['source'],
		'show_images'          => $atts['show_images'],
		'hide_header'          => $atts['hide_header'],
	);

	return wpt_twitter_feed( $instance );
}

/**
 * Get the twitter feed data.
 *
 * @param array $instance Config for this instance.
 *
 * @return string.
 */
function wpt_twitter_feed( $instance ) {
	$header = '';
	if ( ! isset( $instance['search'] ) ) {
		$twitter_id = ( isset( $instance['twitter_id'] ) && '' !== $instance['twitter_id'] ) ? $instance['twitter_id'] : get_option( 'wtt_twitter_username' );
		$user       = wpt_get_user( $twitter_id );
		if ( empty( $user ) ) {
			return __( 'Error: You are not connected to Twitter.', 'wp-to-twitter' );
		}
		if ( isset( $user->errors ) && $user->errors[0]->message ) {
			return __( 'Error:', 'wp-to-twitter' ) . ' ' . $user->errors[0]->message;
		}
		$avatar           = $user->profile_image_url_https;
		$name             = $user->name;
		$verified         = sanitize_title( $user->verified );
		$img_alignment    = ( is_rtl() ) ? 'wpt-right' : 'wpt-left';
		$follow_alignment = ( is_rtl() ) ? 'wpt-left' : 'wpt-right';
		$follow_url       = esc_url( 'https://twitter.com/' . $twitter_id );
		$follow_button    = apply_filters( 'wpt_follow_button', "<a href='$follow_url' class='twitter-follow-button $follow_alignment' data-width='30px' data-show-screen-name='false' data-size='large' data-show-count='false' data-lang='en'>Follow @" . esc_html( $twitter_id ) . '</a>' );
		$header          .= '<div class="wpt-header">';
		$header          .= "<div class='wpt-follow-button'>$follow_button</div>
		<p>
			<img src='$avatar' alt='' class='wpt-twitter-avatar $img_alignment $verified' />
			<span class='wpt-twitter-name'>$name</span><br />
			<span class='wpt-twitter-id'><a href='$follow_url'>@" . esc_html( str_replace( '@', '', $twitter_id ) ) . '</a></span>
		</p>';
		$header          .= '</div>';
	} else {
		$twitter_id = false;
	}

	$hide_header = ( isset( $instance['hide_header'] ) && 1 === (int) $instance['hide_header'] ) ? true : false;

	if ( ! isset( $instance['search'] ) ) {
		$options['exclude_replies'] = ( isset( $instance['twitter_hide_replies'] ) ) ? $instance['twitter_hide_replies'] : false;
		$options['include_rts']     = $instance['twitter_include_rts'];
	} else {
		$options['search']      = $instance['search'];
		$options['geocode']     = $instance['geocode'];
		$options['result_type'] = $instance['result_type'];
	}

	if ( $hide_header ) {
		$header = '';
	}

	$return = $header . '<ul>' . "\n";

	$opts['links']       = $instance['link_links'];
	$opts['mentions']    = $instance['link_mentions'];
	$opts['hashtags']    = $instance['link_hashtags'];
	$opts['show_images'] = isset( $instance['show_images'] ) ? $instance['show_images'] : false;
	$rawtweets           = wpt_get_tweets( $instance['twitter_num'], $twitter_id, $options );

	if ( isset( $rawtweets['error'] ) ) {
		$return .= '<li>' . $rawtweets['error'] . '</li>';
	} else {
		// Build the tweets array.
		$tweets = array();
		foreach ( $rawtweets as $tweet ) {

			if ( is_object( $tweet ) ) {
				$tweet = json_decode( json_encode( $tweet ), true );
			}

			if ( isset( $tweet['retweeted_status']['user']['id_str'] ) ) {
				$posted_by = $tweet['retweeted_status']['user']['id_str'];
			} elseif ( isset( $tweet['in_reply_to_screen_name'] ) ) {
				$posted_by = $tweet['in_reply_to_screen_name'];
			} elseif ( isset( $tweet['user']['id_str'] ) ) {
				$posted_by = $tweet['user']['id_str'];
			} else {
				$posted_by = $twitter_id;
			}

			if ( $instance['source'] ) {
				$source = $tweet['source'];
				if ( '' !== $source ) {
					// Translators: 1 - time string, 2 - name of Tweet app, 3 - Link to Tweet.
					$timetweet = sprintf( __( '<a href="%3$s">about %1$s ago</a> via %2$s', 'wp-to-twitter' ), human_time_diff( strtotime( $tweet['created_at'] ) ), $source, 'http://twitter.com/' . $posted_by . "/status/$tweet[id_str]" );
				} else {
					// Translators: 1 - time string, 2 - Link to Tweet.
					$timetweet = sprintf( __( '<a href="%2$s">about %1$s ago</a>', 'wp-to-twitter' ), human_time_diff( strtotime( $tweet['created_at'] ) ), $source, 'http://twitter.com/' . $posted_by . "/status/$tweet[id_str]" );
				}
			} else {
				// Translators: 1 - time string; 2 - link to Tweet.
				$timetweet = sprintf( __( '<a href="%2$s">about %1$s ago</a>', 'wp-to-twitter' ), human_time_diff( strtotime( $tweet['created_at'] ) ), "http://twitter.com/$posted_by/status/$tweet[id_str]" );
			}
			$tweet_classes = wpt_generate_classes( $tweet );

			$intents = ( $instance['intents'] ) ? "<div class='wpt-intents-border'></div><div class='wpt-intents'><a class='wpt-reply' href='https://twitter.com/intent/tweet?in_reply_to=$tweet[id_str]'><span></span><span class='intent-text reply-text'>Reply</span></a> <a class='wpt-retweet' href='https://twitter.com/intent/retweet?tweet_id=$tweet[id_str]'><span></span><span class='intent-text retweet-text'>Retweet</span></a> <a class='wpt-favorite' href='https://twitter.com/intent/favorite?tweet_id=$tweet[id_str]'><span></span><span class='intent-text favorite-text'>Favorite</span></a></div>" : '';
			// Add tweet to array.
			$before_tweet = apply_filters( 'wpt_before_tweet', '', $tweet );
			$after_tweet  = apply_filters( 'wpt_after_tweet', '', $tweet );
			$tweets[]     = '<li class="' . $tweet_classes . '">' . $before_tweet . wpt_tweet_linkify( $tweet['text'], $opts, $tweet ) . "<br /><span class='wpt-tweet-time'>$timetweet</span> $intents " . $after_tweet . "</li>\n";
		}
	}
	if ( is_array( $tweets ) ) {
		foreach ( $tweets as $tweet ) {
			$return .= $tweet;
		}
	}
	$return .= '</ul>' . "\n";

	return $return;
}
