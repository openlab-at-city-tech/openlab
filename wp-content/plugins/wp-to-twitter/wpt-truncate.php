<?php
/**
 * Construct and check lengths of Tweets - WP to Twitter
 *
 * @category Core
 * @package  WP to Twitter
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check the current allowed max lengths.
 *
 * @return array of URL lengths and params.
 */
function wpt_max_length() {
	$config        = get_transient( 'wpt_twitter_config' );
	$set_transient = false;
	if ( ! $config ) {
		$set_transient = true;
		$connection    = wpt_oauth_connection();
		if ( $connection ) {
			$config = $connection->get( 'https://api.twitter.com/1.1/help/configuration.json' );
		} else {
			$config = json_encode(
				array(
					'http_length'    => 23,
					'https_length'   => 23,
					'reserved_chars' => 24,
				)
			);
		}
	}
	$decoded = ( is_string( $config ) ) ? json_decode( $config ) : $config;

	if ( is_object( $decoded ) && isset( $decoded->short_url_length ) ) {
		$short_url_length = $decoded->short_url_length;
		$short_url_https  = $decoded->short_url_length_https;
		$reserved_char    = $decoded->characters_reserved_per_media;
		$values           = array(
			'http_length'    => $short_url_length,
			'https_length'   => $short_url_https,
			'reserved_chars' => $reserved_char,
		);

	} else {
		// if config query is invalid, use default values; these may become invalid.
		$values = array(
			'http_length'    => 23,
			'https_length'   => 23,
			'reserved_chars' => 24,
		);
	}
	if ( $set_transient ) {
		// Only set the transient after confirming valid values.
		set_transient( 'wpt_twitter_config', $values, 60 * 60 * 24 );
	}

	$values['base_length'] = intval( ( get_option( 'wpt_tweet_length' ) ) ? get_option( 'wpt_tweet_length' ) : 140 ) - 1;

	return apply_filters( 'wpt_max_length', $values );
}

add_filter( 'wpt_tweet_sentence', 'wpt_filter_urls', 10, 2 );
/**
 * Filter the URLs in a tweet and shorten them.
 *
 * @param string $tweet Tweet.
 * @param int    $post_ID Post ID.
 *
 * @return string New tweet text.
 */
function wpt_filter_urls( $tweet, $post_ID ) {
	preg_match_all( '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $tweet, $match );
	$title = get_the_title( $post_ID );

	if ( isset( $match[0] ) && ! empty( $match[0] ) ) {
		$urls = $match[0];
		foreach ( $urls as $url ) {
			if ( esc_url( $url ) ) {
				$short = wpt_shorten_url( $url, $title, $post_ID, false, false );
				if ( $short ) {
					$tweet = str_replace( $url, $short, $tweet );
				}
			}
		}
	}

	return $tweet;
}

/**
 * Parse the text of a Tweet to ensure included tags don't exceed length requirements.
 *
 * @param string  $tweet Tweet text.
 * @param array   $post Post data.
 * @param int     $post_ID Post ID.
 * @param boolean $retweet Is this a retweet.
 * @param boolean $ref Reference.
 *
 * @return string New text.
 */
function jd_truncate_tweet( $tweet, $post, $post_ID, $retweet = false, $ref = false ) {
	// media file no longer needs accounting in shortening. 9/22/2016.
	$maxlength = wpt_max_length();
	$length    = $maxlength['base_length'];
	$tweet     = apply_filters( 'wpt_tweet_sentence', $tweet, $post_ID );
	$tweet     = trim( wpt_custom_shortcodes( $tweet, $post_ID ) );
	$tweet     = trim( wpt_user_meta_shortcodes( $tweet, $post['authId'] ) );
	$encoding  = ( 'UTF-8' !== get_option( 'blog_charset' ) && '' !== get_option( 'blog_charset', '' ) ) ? get_option( 'blog_charset' ) : 'UTF-8';
	$diff      = 0;

	// Add custom append/prepend fields to Tweet text.
	if ( '' !== get_option( 'jd_twit_prepend', '' ) && '' !== $tweet ) {
		$tweet = stripslashes( get_option( 'jd_twit_prepend' ) ) . ' ' . $tweet;
	}
	if ( '' !== get_option( 'jd_twit_append', '' ) && '' !== $tweet ) {
		$tweet = $tweet . ' ' . stripslashes( get_option( 'jd_twit_append' ) );
	}

	// there are no tags in this Tweet. Truncate and return.
	if ( ! wpt_has_tags( $tweet ) ) {
		$post_tweet = mb_substr( $tweet, 0, $length, $encoding );
		return apply_filters( 'wpt_custom_truncate', $post_tweet, $tweet, $post_ID, $retweet, 1 );
	}

	// create full unconditional post tweet - prior to truncation.
	// order matters; arrays have to be ordered the same way.
	$tags   = array_map( 'wpt_make_tag', wpt_tags() );
	$values = wpt_create_values( $post, $post_ID, $ref );

	$post_tweet = str_ireplace( $tags, $values, $tweet );
	// check total length.
	$str_length = mb_strlen( urldecode( wpt_normalize( $post_tweet ) ), $encoding );

	// Check whether completed replacement is still within allowed length.
	if ( $str_length < $length + 1 ) {
		if ( mb_strlen( wpt_normalize( $post_tweet ) ) > $length + 1 ) {
			$post_tweet = mb_substr( $post_tweet, 0, $length, $encoding );
		}

		return apply_filters( 'wpt_custom_truncate', $post_tweet, $tweet, $post_ID, $retweet, 2 ); // return early if all is well.
	} else {
		$has_excerpt_tag = wpt_has( $tweet, '#post#' );
		$has_title_tag   = wpt_has( $tweet, '#title#' );
		$has_short_url   = wpt_has( $tweet, '#url#' );
		$has_long_url    = wpt_has( $tweet, '#longurl#' );

		$url_strlen     = mb_strlen( urldecode( wpt_normalize( $values['url'] ) ), $encoding );
		$longurl_strlen = mb_strlen( urldecode( wpt_normalize( $values['longurl'] ) ), $encoding );

		// Tweet is too long, so we'll have to truncate that sucker.
		$length_array = wpt_length_array( $values, $encoding );

		// Twitter's t.co shortener is mandatory. All URLS are max-character value set by Twitter.
		$tco   = ( wpt_is_ssl( $values['url'] ) ) ? $maxlength['https_length'] : $maxlength['http_length'];
		$order = get_option( 'wpt_truncation_order' );
		if ( is_array( $order ) ) {
			asort( $order );
			$preferred = array();
			foreach ( $order as $k => $v ) {
				if ( 'excerpt' === $k ) {
					$k     = 'post';
					$value = $length_array['post'];
				} elseif ( 'blogname' === $k ) {
					$k     = 'blog';
					$value = $length_array['blog'];
				} else {
					$value = $length_array[ $k ];
				}

				$preferred[ $k ] = $value;
			}
		} else {
			$preferred = $length_array;
		}
		if ( $has_short_url ) {
			$diff = ( ( $url_strlen - $tco ) > 0 ) ? $url_strlen - $tco : 0;
		} elseif ( $has_long_url ) {
			$diff = ( ( $longurl_strlen - $tco ) > 0 ) ? $longurl_strlen - $tco : 0;
		}
		if ( $str_length > ( $length + 1 + $diff ) ) {
			foreach ( $preferred as $key => $value ) {
				// don't truncate content of post excerpt or title if those tags not in use.
				if ( ! ( 'excerpt' === $key && ! $has_excerpt_tag ) && ! ( 'title' === $key && ! $has_title_tag ) ) {
					$str_length = mb_strlen( urldecode( wpt_normalize( trim( $post_tweet ) ) ), $encoding );
					if ( $str_length > ( $length + 1 + $diff ) ) {
						$trim      = $str_length - ( $length + 1 + $diff );
						$old_value = $values[ $key ];
						// prevent URL from being modified.
						$post_tweet = str_ireplace( array( $values['url'], $values['longurl'] ), array( '#url#', '#longurl#' ), $post_tweet );

						// These tag fields should be removed completely, rather than truncated.
						if ( wpt_remove_tag( $key ) ) {
							$new_value = '';
							// These tag fields should have stray characters removed on word boundaries.
						} elseif ( 'tags' === $key ) {
							// remove any stray hash characters due to string truncation.
							if ( mb_strlen( $old_value ) - $trim <= 2 ) {
								$new_value = '';
							} else {
								$new_value = $old_value;
								while ( ( mb_strlen( $old_value ) - $trim ) < mb_strlen( $new_value ) ) {
									$new_value = trim( mb_substr( $new_value, 0, mb_strrpos( $new_value, '#', $encoding ) - 1 ) );
								}
							}
							// Just flat out truncate everything else cold.
						} else {
							// trim letters.
							$new_value = mb_substr( $old_value, 0, - ( $trim ), $encoding );
							// trim rest of last word.
							$last_space = strrpos( $new_value, ' ' );
							$new_value  = mb_substr( $new_value, 0, $last_space, $encoding );
							// If you want to add something like an ellipsis after truncation, use this filter.
							$new_value = apply_filters( 'wpt_filter_truncated_value', $new_value, $key, $old_value );
						}
						$post_tweet = str_ireplace( $old_value, $new_value, $post_tweet );
						// put URL back before checking length.
						$post_tweet = str_ireplace( array( '#url#', '#longurl#' ), array( $values['url'], $values['longurl'] ), $post_tweet );
					} else {
						if ( mb_strlen( wpt_normalize( $post_tweet ), $encoding ) > ( $length + 1 + $diff ) ) {
							$post_tweet = mb_substr( $post_tweet, 0, ( $length + $diff ), $encoding );
						}
					}
				}
			}
		}

		// this is needed in case a tweet needs to be truncated outright and the truncation values aren't in the above.
		// 1) removes URL 2) checks length of remainder 3) Replaces URL.
		if ( mb_strlen( wpt_normalize( $post_tweet ) ) > $length + 1 ) {
			$tweet = false;
			if ( $has_short_url ) {
				$url = $values['url'];
				$tag = '#url#';
			} elseif ( $has_long_url ) {
				$url = $values['longurl'];
				$tag = '#longurl#';
			} else {
				$post_tweet = mb_substr( $post_tweet, 0, ( $length + $diff ), $encoding );
				$tweet      = true;
			}

			if ( ! $tweet ) {
				$temp = str_ireplace( $url, $tag, $post_tweet );
				if ( mb_strlen( wpt_normalize( $temp ) ) > ( ( $length + 1 ) - ( $tco - strlen( $tag ) ) ) && $temp !== $post_tweet ) {
					if ( false === stripos( $temp, '#url#' ) && false === stripos( $temp, '#longurl#' ) ) {
						$post_tweet = trim( mb_substr( $temp, 0, $length, $encoding ) );
					} else {
						$post_tweet = trim( mb_substr( $temp, 0, ( $length - $tco - 1 ), $encoding ) );
					}
					// it's possible to trim off the #url# part in this process. If that happens, put it back.
					$sub_sentence = ( ! wpt_has( $post_tweet, $tag ) && ( $has_short_url || $has_long_url ) ) ? $post_tweet . ' ' . $tag : $post_tweet;
					$post_tweet   = str_ireplace( $tag, $url, $sub_sentence );
				}
			}
		}
	}

	return apply_filters( 'wpt_custom_truncate', $post_tweet, $tweet, $post_ID, $retweet, 3 );
}

/**
 * Check whether a tag is within the string.
 *
 * @param string $string String. Probably a Tweet.
 * @param string $tag Template tag text.
 *
 * @return boolean.
 */
function wpt_has( $string, $tag ) {
	if ( strpos( $string, $tag ) === false ) {
		return false;
	}

	return true;
}

/**
 * Check whether any tags are present.
 *
 * @param string $string String. Probably a Tweet.
 *
 * @return boolean.
 */
function wpt_has_tags( $string ) {
	$tags = wpt_tags();
	foreach ( $tags as $tag ) {
		if ( wpt_has( $string, "#$tag#" ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Get a tag to remove.
 *
 * @param string $key Template tag.
 *
 * @return boolean.
 */
function wpt_remove_tag( $key ) {
	switch ( $key ) {
		case 'account':
		case 'author':
		case 'category':
		case 'categories':
		case 'date':
		case 'modified':
		case 'reference':
		case '@':
			$return = true;
			break;
		default:
			$return = false;
	}

	return $return;
}

/**
 * Get all valid template tags.
 *
 * @return array tags.
 */
function wpt_tags() {
	return apply_filters( 'wpt_tags', array( 'url', 'title', 'blog', 'post', 'category', 'categories', 'date', 'author', 'displayname', 'tags', 'modified', 'reference', 'account', '@', 'cat_desc', 'longurl' ) );
}

/**
 * Adjust a tag string into its ## usage.
 *
 * @param string $value Any text.
 *
 * @return string wrapped.
 */
function wpt_make_tag( $value ) {
	return '#' . $value . '#';
}

/**
 * Create values. Get the value of tags.
 *
 * @param array   $post Post array.
 * @param int     $post_ID Post ID.
 * @param boolean $ref Use referential author.
 *
 * @return array of values.
 */
function wpt_create_values( $post, $post_ID, $ref ) {
	$shrink = ( '' !== $post['shortUrl'] && false !== $post['shortUrl'] ) ? $post['shortUrl'] : apply_filters( 'wptt_shorten_link', $post['postLink'], $post['postTitle'], $post_ID, false );
	// generate template variable values.
	$auth         = $post['authId'];
	$title        = trim( apply_filters( 'wpt_status', $post['postTitle'], $post_ID, 'title' ) );
	$blogname     = trim( $post['blogTitle'] );
	$excerpt      = trim( apply_filters( 'wpt_status', $post['postExcerpt'], $post_ID, 'post' ) );
	$thisposturl  = trim( $shrink );
	$category     = trim( $post['category'] );
	$categories   = trim( $post['cats'] );
	$cat_desc     = trim( $post['cat_desc'] );
	$tags         = wpt_generate_hash_tags( $post_ID );
	$date         = trim( $post['postDate'] );
	$modified     = trim( $post['postModified'] );
	$account      = get_option( 'wtt_twitter_username', '' );
	$user_meta    = get_user_meta( $auth, 'wp-to-twitter-user-username', true );
	$user_account = get_user_meta( $auth, 'wtt_twitter_username', true );
	$user_account = ( $user_account ) ? $user_account : $user_meta;
	if ( '1' === get_option( 'jd_individual_twitter_users' ) ) {
		if ( 'mainAtTwitter' === get_user_meta( $auth, 'wp-to-twitter-enable-user', true ) ) {
			$account = $user_account;
		} elseif ( 'mainAtTwitterPlus' === get_user_meta( $auth, 'wp-to-twitter-enable-user', true ) ) {
			$account = stripcslashes( $user_account . ' @' . get_option( 'wtt_twitter_username' ) );
		} else {
			$account = ( $user_account ) ? $user_account : $account;
		}
	}
	$account = ( '' !== $account ) ? "@$account" : ''; // value of #account#.
	$account = str_ireplace( '@@', '@', $account );

	$uaccount = ( '' !== $user_account ) ? "@$user_account" : "$account"; // value of #@#.
	$uaccount = str_ireplace( '@@', '@', $uaccount );

	$display_name = get_the_author_meta( 'display_name', $auth );
	$author       = ( '' !== $user_account ) ? "@$user_account" : $display_name; // value of #author#.
	$author       = str_ireplace( '@@', '@', $author );

	if ( 'on' === get_user_meta( $auth, 'wpt-remove', true ) ) {
		$account = '';
	}

	if ( function_exists( 'wpt_pro_exists' ) && true === wpt_pro_exists() ) {
		$reference = ( $ref ) ? $uaccount : '@' . get_option( 'wtt_twitter_username' );
	} else {
		$reference = '';
	}

	return array(
		'url'         => $thisposturl,
		'title'       => $title,
		'blog'        => $blogname,
		'post'        => $excerpt,
		'category'    => $category,
		'categories'  => $categories,
		'date'        => $date,
		'author'      => $author,
		'displayname' => $display_name,
		'tags'        => $tags,
		'modified'    => $modified,
		'reference'   => $reference,
		'account'     => $account,
		'@'           => $uaccount,
		'cat_desc'    => $cat_desc,
		'longurl'     => $post['postLink'],
	);
}

/**
 * Generate array of length values of every value.
 *
 * @param array  $values All values.
 * @param string $encoding Current encoding.
 *
 * @return array.
 */
function wpt_length_array( $values, $encoding ) {
	foreach ( $values as $key => $value ) {
		$array[ $key ] = mb_strlen( wpt_normalize( $value ), $encoding );
	}

	return $array;
}

/**
 * Parse custom shortcodes
 *
 * @param string  $sentence Tweet template.
 * @param integer $post_ID Post ID.
 *
 * @return string $sentence with any custom shortcodes replaced with their appropriate content.
 */
function wpt_custom_shortcodes( $sentence, $post_ID ) {
	$pattern = '/([([\[\]?)([A-Za-z0-9-_])*(\]\]]?)+/';
	$params  = array(
		0 => '[[',
		1 => ']]',
	);
	preg_match_all( $pattern, $sentence, $matches );
	if ( $matches && is_array( $matches[0] ) ) {
		foreach ( $matches[0] as $value ) {
			$shortcode = "$value";
			$field     = str_replace( $params, '', $shortcode );
			$custom    = apply_filters( 'wpt_custom_shortcode', strip_tags( get_post_meta( $post_ID, $field, true ) ), $post_ID, $field );
			$sentence  = str_replace( $shortcode, $custom, $sentence );
		}
	}

	return $sentence;
}

/**
 * Parse user meta shortcodes
 *
 * @param string  $sentence Tweet template.
 * @param integer $auth_id Post Author ID.
 *
 * @return string $sentence with any custom shortcodes replaced with their appropriate content.
 */
function wpt_user_meta_shortcodes( $sentence, $auth_id ) {
	$pattern = '/([({\{\}?)([A-Za-z0-9-_])*(\}\}}?)+/';
	$params  = array(
		0 => '{{',
		1 => '}}',
	);
	preg_match_all( $pattern, $sentence, $matches );
	if ( $matches && is_array( $matches[0] ) ) {
		foreach ( $matches[0] as $value ) {
			$shortcode = "$value";
			$field     = str_replace( $params, '', $shortcode );
			$custom    = apply_filters( 'wpt_user_meta_shortcode', strip_tags( get_user_meta( $auth_id, $field, true ) ), $auth_id, $field );
			$sentence  = str_replace( $shortcode, $custom, $sentence );
		}
	}

	return $sentence;
}
