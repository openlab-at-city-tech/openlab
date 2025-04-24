<?php
/**
 * Construct and check lengths of status updates - XPoster
 *
 * @category Core
 * @package  XPoster
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
 * @param string $service Service to get values for.
 *
 * @return array of URL lengths and params.
 */
function wpt_max_length( $service = false ) {
	$values = array(
		'http_length'    => 23,
		'https_length'   => 23,
		'reserved_chars' => 24,
	);

	$values['base_length'] = intval( ( get_option( 'wpt_' . $service . '_length' ) ) ? get_option( 'wpt_' . $service . '_length' ) : 280 ) - 1;

	if ( ! $service ) {
		$values['x']        = absint( get_option( 'wpt_x_length', $values['base_length'] ) );
		$values['mastodon'] = absint( get_option( 'wpt_mastodon_length', $values['base_length'] ) );
		$values['bluesky']  = absint( get_option( 'wpt_bluesky_length', $values['base_length'] ) );
	}

	/**
	 * Filter the max length array used for calculating status update truncation.
	 *
	 * @hook wpt_max_length
	 *
	 * @param {array} $values Array with various values used for calculating how long your status update can be.
	 *
	 * @return {array}
	 */
	return apply_filters( 'wpt_max_length', $values, $service );
}

add_filter( 'wpt_tweet_sentence', 'wpt_filter_urls', 10, 2 );
/**
 * Filter the URLs in a status update and shorten them.
 *
 * @param string $update Status update text.
 * @param int    $post_ID Post ID.
 *
 * @return string New update text.
 */
function wpt_filter_urls( $update, $post_ID ) {
	preg_match_all( '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $update, $match );
	$title = get_the_title( $post_ID );

	if ( isset( $match[0] ) && ! empty( $match[0] ) ) {
		$urls = $match[0];
		foreach ( $urls as $url ) {
			if ( esc_url( $url ) ) {
				$short = wpt_shorten_url( $url, $title, $post_ID, false, false, false );
				if ( $short ) {
					$update = str_replace( $url, $short, $update );
				}
			}
		}
	}

	return $update;
}

/**
 * Parse the text of a status update to ensure included tags don't exceed length requirements.
 *
 * @param string  $update Status update text.
 * @param array   $post Post data.
 * @param int     $post_ID Post ID.
 * @param boolean $repost Is this a repost.
 * @param boolean $ref X.com author Reference.
 * @param string  $service Service being generated for.
 *
 * @return string New text.
 */
function wpt_truncate_status( $update, $post, $post_ID, $repost = false, $ref = false, $service = 'x' ) {
	if ( ! $post_ID ) {
		// If no Post ID, return the update exactly as passed.
		return $update;
	} else {
		if ( empty( $post ) ) {
			$post = wpt_post_info( $post_ID );
		}
		$variant = get_post_meta( $post_ID, '_wpt_post_template_' . $service, true );
		if ( '' !== $variant ) {
			$update = $variant;
		}
		// create full unconditional post update - prior to truncation.
		// order matters; arrays have to be ordered the same way.
		$tags   = array_map( 'wpt_make_tag', wpt_tags() );
		$values = wpt_create_values( $post, $post_ID, $ref, $service );

		// media file no longer needs accounting in shortening. 9/22/2016.
		$maxlength = wpt_max_length( $service );
		$length    = $maxlength['base_length'];
		/**
		 * Filter a template prior to parsing tags.
		 *
		 * @hook wpt_tweet_sentence
		 *
		 * @param {string} $update Template for this status update.
		 * @param {int}    $post_ID Post ID.
		 *
		 * @return {string}
		 */
		$update   = apply_filters( 'wpt_tweet_sentence', $update, $post_ID );
		$update   = trim( wpt_custom_shortcodes( $update, $post_ID ) );
		$update   = trim( wpt_user_meta_shortcodes( $update, $post['authId'] ) );
		$encoding = ( 'UTF-8' !== get_option( 'blog_charset' ) && '' !== get_option( 'blog_charset', '' ) ) ? get_option( 'blog_charset' ) : 'UTF-8';
		$diff     = 0;
		$prepend  = wp_unslash( get_option( 'jd_twit_prepend', '' ) );
		$append   = wp_unslash( get_option( 'jd_twit_append', '' ) );
		// Add custom append/prepend fields to status update text.
		if ( '' !== $prepend && '' !== $update && ( str_contains( $update, $prepend ) === false ) ) {
			$update = $prepend . ' ' . $update;
		}
		if ( '' !== $append && '' !== $update && ( str_contains( $update, $append ) === false ) ) {
			$update = $update . ' ' . $append;
		}

		// there are no tags in this update. Truncate and return.
		if ( ! wpt_has_tags( $update ) ) {
			$post_update = mb_substr( $update, 0, $length, $encoding );
			/**
			 * Filter an update template that does not contain any XPoster template tags.
			 *
			 * @hook wpt_custom_truncate
			 * @param {string} $post_status Text to status update truncated to maximum allowed length.
			 * @param {string} $update Original passed text.
			 * @param {int}    $post_ID Post ID.
			 * @param {bool}   $repost Boolean flag that indicates whether this is being reposted.
			 * @param {int}    $reference Pass reference (1).
			 *
			 * @return {string}
			 */
			return apply_filters( 'wpt_custom_truncate', $post_update, $update, $post_ID, $repost, 1 );
		}

		// Replace the template tags with their corresponding values.
		$post_update = str_ireplace( $tags, $values, $update );

		// check total length.
		$str_length = mb_strlen( urldecode( wpt_normalize( $post_update ) ), $encoding );

		// Check whether completed replacement is still within allowed length.
		if ( $str_length < $length + 1 ) {
			if ( mb_strlen( wpt_normalize( $post_update ) ) > $length + 1 ) {
				$post_update = mb_substr( $post_update, 0, $length, $encoding );
			}
			/**
			 * Filter an update template after tags have been parsed but prior to truncating for length.
			 *
			 * @hook wpt_custom_truncate
			 * @param {string} $post_update Text to Tweet truncated to maximum allowed length.
			 * @param {string} $update Original passed text.
			 * @param {int}    $post_ID Post ID.
			 * @param {bool}   $repost Boolean flag that indicates whether this is being reposted.
			 * @param {int}    $reference Pass reference (2).
			 *
			 * @return {string}
			 */
			return apply_filters( 'wpt_custom_truncate', $post_update, $update, $post_ID, $repost, 2 ); // return early if all is well.
		} else {
			$has_excerpt_tag = wpt_has( $update, '#post#' );
			$has_title_tag   = wpt_has( $update, '#title#' );
			$has_short_url   = wpt_has( $update, '#url#' );
			$has_long_url    = wpt_has( $update, '#longurl#' );

			$url_strlen     = mb_strlen( urldecode( wpt_normalize( $values['url'] ) ), $encoding );
			$longurl_strlen = mb_strlen( urldecode( wpt_normalize( $values['longurl'] ) ), $encoding );

			// Status update is too long, so we'll have to truncate that sucker.
			$length_array = wpt_length_array( $values, $encoding );

			// X.com's t.co shortener is mandatory. All URLS are max-character value set by X.com. Only true on X.
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
						$str_length = mb_strlen( urldecode( wpt_normalize( trim( $post_update ) ) ), $encoding );
						if ( $str_length > ( $length + 1 + $diff ) ) {
							$trim      = $str_length - ( $length + 1 + $diff );
							$old_value = $values[ $key ];
							// prevent URL from being modified.
							$post_update = str_ireplace( array( $values['url'], $values['longurl'] ), array( '#url#', '#longurl#' ), $post_update );

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
										$new_value = trim( mb_substr( $new_value, 0, mb_strrpos( $new_value, '#', 0, $encoding ) - 1 ) );
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

								/**
								 * Filter a template tag value after truncation. If a value like an excerpt or post content has been truncated, you can modify the output using this filter.
								 *
								 * @hook wpt_filter_truncated_value
								 * @param {string} $new_value Text truncated to maximum allowed length.
								 * @param {string} $key Template tag.
								 * @param {string} $old_value Text prior to truncation.
								 *
								 * @return {string}
								 */
								$new_value = apply_filters( 'wpt_filter_truncated_value', $new_value, $key, $old_value );
							}
							$post_update = str_ireplace( $old_value, $new_value, $post_update );
							// put URL back before checking length.
							$post_update = str_ireplace( array( '#url#', '#longurl#' ), array( $values['url'], $values['longurl'] ), $post_update );
						} else {
							if ( mb_strlen( wpt_normalize( $post_update ), $encoding ) > ( $length + 1 + $diff ) ) {
								$post_update = mb_substr( $post_update, 0, ( $length + $diff ), $encoding );
							}
						}
					}
				}
			}

			// this is needed in case an update needs to be truncated outright and the truncation values aren't in the above.
			// 1) removes URL 2) checks length of remainder 3) Replaces URL.
			if ( mb_strlen( wpt_normalize( $post_update ) ) > $length + 1 ) {
				$update = false;
				if ( $has_short_url ) {
					$url = $values['url'];
					$tag = '#url#';
				} elseif ( $has_long_url ) {
					$url = $values['longurl'];
					$tag = '#longurl#';
				} else {
					$post_update = mb_substr( $post_update, 0, ( $length + $diff ), $encoding );
					$update      = true;
				}

				if ( ! $update ) {
					$temp = str_ireplace( $url, $tag, $post_update );
					if ( mb_strlen( wpt_normalize( $temp ) ) > ( ( $length + 1 ) - ( $tco - strlen( $tag ) ) ) && $temp !== $post_update ) {
						if ( false === stripos( $temp, '#url#' ) && false === stripos( $temp, '#longurl#' ) ) {
							$post_update = trim( mb_substr( $temp, 0, $length, $encoding ) );
						} else {
							$post_update = trim( mb_substr( $temp, 0, ( $length - $tco - 1 ), $encoding ) );
						}
						// it's possible to trim off the #url# part in this process. If that happens, put it back.
						$sub_sentence = ( ! wpt_has( $post_update, $tag ) && ( $has_short_url || $has_long_url ) ) ? $post_update . ' ' . $tag : $post_update;
						$post_update  = str_ireplace( $tag, $url, $sub_sentence );
					}
				}
			}
		}
	}
	/**
	 * Filter a status update template after all content checks are completed.
	 *
	 * @hook wpt_custom_truncate
	 * @param {string} $post_update Text to status update truncated to maximum allowed length.
	 * @param {string} $update Original passed text.
	 * @param {int}    $post_ID Post ID.
	 * @param {bool}   $repost Boolean flag that indicates whether this is being reposted.
	 * @param {int}    $reference Pass reference (3).
	 *
	 * @return {string}
	 */
	return apply_filters( 'wpt_custom_truncate', $post_update, $update, $post_ID, $repost, 3 );
}

/**
 * Check whether a tag is within the string.
 *
 * @param string $text String. Probably a status update.
 * @param string $tag Template tag text.
 *
 * @return boolean.
 */
function wpt_has( $text, $tag ) {
	if ( strpos( $text, $tag ) === false ) {
		return false;
	}

	return true;
}

/**
 * Check whether any tags are present.
 *
 * @param string $text String. Probably a Tweet.
 *
 * @return boolean.
 */
function wpt_has_tags( $text ) {
	$tags = wpt_tags();
	foreach ( $tags as $tag ) {
		if ( wpt_has( $text, "#$tag#" ) ) {
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
	/**
	 * Add a new template tag placeholder.
	 *
	 * @hook wpt_tags
	 *
	 * @param {array} $tags Array of strings for each tag, e.g. 'blog' for #blog#.
	 *
	 * @return {array}
	 */
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
 * @since 5.0.0 Added $service parameter.
 *
 * @param array   $post Post array.
 * @param int     $post_ID Post ID.
 * @param boolean $ref Use referential author.
 * @param string  $service Social media service to send to.
 *
 * @return array of values.
 */
function wpt_create_values( $post, $post_ID, $ref, $service ) {
	/**
	 * Run filters that shorten links.
	 *
	 * @hook wptt_shorten_link
	 *
	 * @param {string} $permalink The post permalink.
	 * @param {string} $title The post title.
	 * @param {int}    $post_ID The post ID.
	 * @param {bool}   $test False because this is not a test cycle.
	 *
	 * @return {string}
	 */
	$shortlink = apply_filters( 'wptt_shorten_link', $post['postLink'], $post['postTitle'], $post_ID, false );
	$shrink    = ( '' !== $post['shortUrl'] && false !== $post['shortUrl'] ) ? $post['shortUrl'] : $shortlink;
	// generate template variable values.
	$auth        = $post['authId'];
	$title       = apply_filters( 'wpt_status', $post['postTitle'], $post_ID, 'title' );
	$title       = trim( ( ! $title ) ? get_the_title( $post_ID ) : $title );
	$encoding    = get_option( 'blog_charset', 'UTF-8' );
	$title       = html_entity_decode( $title, ENT_QUOTES, $encoding );
	$blogname    = trim( (string) $post['blogTitle'] );
	$excerpt     = trim( (string) apply_filters( 'wpt_status', $post['postExcerpt'], $post_ID, 'post' ) );
	$thisposturl = trim( (string) $shrink );
	$category    = trim( (string) $post['category'] );
	$categories  = trim( (string) $post['cats'] );
	$cat_desc    = trim( (string) $post['cat_desc'] );
	$tags        = wpt_generate_hash_tags( $post_ID );
	$date        = trim( (string) $post['postDate'] );
	$modified    = trim( (string) $post['postModified'] );
	switch ( $service ) {
		case 'x':
			$account_field = 'wtt_twitter_username';
			$user_setting  = 'wp-to-twitter-user-username';
			$user_field    = 'wtt_twitter_username';
			break;
		case 'bluesky':
			$account_field = 'wpt_bluesky_username';
			$user_setting  = 'wpt-bluesky-username';
			$user_field    = 'wpt_bluesky_username';
			break;
		case 'mastodon':
			$account_field = 'wpt_mastodon_username';
			$user_setting  = 'wpt-mastodon-username';
			$user_field    = 'wpt_mastodon_username';
			break;
	}
	$account = get_option( $account_field, '' );
	// The setting.
	$user_meta = get_user_meta( $auth, $user_setting, true );
	// If connected to service.
	$user_account = get_user_meta( $auth, $user_field, true );
	$user_account = ( $user_account ) ? $user_account : $user_meta;

	$account = ( '' !== $account ) ? "@$account" : ''; // value of #account#.
	$account = str_ireplace( '@@', '@', $account );

	$uaccount = ( '' !== $user_account ) ? "@$user_account" : "$account"; // value of #@#.
	$uaccount = str_ireplace( '@@', '@', $uaccount );

	$display_name = get_the_author_meta( 'display_name', $auth );
	$author       = ( '' !== $user_account ) ? "@$user_account" : $display_name; // value of #author#.
	$author       = str_ireplace( '@@', '@', $author );

	if ( function_exists( 'wpt_pro_exists' ) && true === wpt_pro_exists() ) {
		$reference = ( $ref ) ? $uaccount : '@' . get_option( $account_field );
	} else {
		$reference = '';
	}

	// If this order is changed, changes must also be replicated in `wpt_tags()`.
	$values = array(
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
	// If tags array has been changed by a filter, update the order here, as well.
	$tags   = wpt_tags();
	$return = array();
	foreach ( $tags as $key ) {
		// If this key doesn't exist in the default values array, this was added in `wpt_tags` filter.
		if ( ! isset( $values[ $key ] ) ) {
			/**
			 * Filter the value of a custom template tag.
			 *
			 * @hook wpt_custom_tag
			 *
			 * @param {string} $tag_value The output for a custom tag. Default empty.
			 * @param {int}     $post_ID The post ID.
			 *
			 * @return {string}
			 */
			$return[ $key ] = trim( apply_filters( 'wpt_custom_tag', '', $post_ID ) );
		} else {
			$return[ $key ] = trim( $values[ $key ] );
		}
	}

	return $return;
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
 * @param string  $sentence Status update template.
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
			$value     = wp_strip_all_tags( get_post_meta( $post_ID, $field, true ) );
			/**
			 * Filter the output of a custom field template tag. Custom field tags are marked with `[[$field]]`.
			 *
			 * @hook wpt_custom_shortcode
			 *
			 * @param {string} $value Returned singular value of a post meta field, tags stripped.
			 * @param {int}    $post_ID Post ID.
			 * @param {string} $field Post meta field name.
			 *
			 * @return {string}
			 */
			$custom   = apply_filters( 'wpt_custom_shortcode', $value, $post_ID, $field );
			$sentence = str_replace( $shortcode, $custom, $sentence );
		}
	}

	return $sentence;
}

/**
 * Parse user meta shortcodes
 *
 * @param string  $sentence Status update template.
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
			/**
			 * Filter custom user meta. User meta tags are marked with `{{$field}}`.
			 *
			 * @hook wpt_user_meta_shortcode
			 *
			 * @param {string} $value Returned singular value of a post meta field, tags stripped.
			 * @param {int}  $auth_id User ID.
			 * @param {string} $field Name of user meta field.
			 *
			 * @return {string}
			 */
			$custom   = apply_filters( 'wpt_user_meta_shortcode', wp_strip_all_tags( get_user_meta( $auth_id, $field, true ) ), $auth_id, $field );
			$sentence = str_replace( $shortcode, $custom, $sentence );
		}
	}

	return $sentence;
}
