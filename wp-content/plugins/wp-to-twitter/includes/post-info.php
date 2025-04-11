<?php
/**
 * Fetch status information for a post.
 *
 * @category Status Updates
 * @package  XPoster
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-to-twitter/
 */

/**
 * Builds array of post info for use in status update functions.
 *
 * @param integer $post_ID Post ID.
 *
 * @return array Post data used in status update functions.
 */
function wpt_post_info( $post_ID ) {
	$encoding       = get_option( 'blog_charset', 'UTF-8' );
	$excerpt_length = get_option( 'jd_post_excerpt' );
	$dateformat     = ( '' === get_option( 'jd_date_format', '' ) ) ? get_option( 'date_format' ) : get_option( 'jd_date_format' );
	$post           = get_post( $post_ID );
	$category_ids   = array();
	$values         = array();
	$values['id']   = $post_ID;

	$values['postinfo']      = $post;
	$values['postContent']   = $post->post_content;
	$values['authId']        = $post->post_author;
	$values['_postDate']     = mysql2date( 'Y-m-d H:i:s', $post->post_date );
	$values['postDate']      = mysql2date( $dateformat, $post->post_date );
	$values['_postModified'] = mysql2date( 'Y-m-d H:i:s', $post->post_modified );
	$values['postModified']  = mysql2date( $dateformat, $post->post_modified );
	// get first category.
	$category   = '';
	$cat_desc   = '';
	$categories = get_the_category( $post_ID );
	$cats       = array();
	$cat_descs  = array();
	if ( is_array( $categories ) ) {
		if ( count( $categories ) > 0 ) {
			$category = $categories[0]->cat_name;
			$cat_desc = $categories[0]->description;
		}
		foreach ( $categories as $cat ) {
			$category_ids[] = $cat->term_id;
			$cats[]         = $cat->cat_name;
			$cat_descs[]    = $cat->description;
		}
		/**
		 * Filter the space separated list of category names in #cats#.
		 *
		 * @hook wpt_twitter_category_names
		 *
		 * @param {array} $cats Array of category names attached to this status update.
		 *
		 * @return {array}
		 */
		$cat_names = implode( ' ', apply_filters( 'wpt_twitter_category_names', $cats ) );
		/**
		 * Filter the space separated list of category descriptions in #cat_descs#.
		 *
		 * @hook wpt_twitter_category_descs
		 *
		 * @param {array} $cats Array of category descriptions attached to this status update.
		 *
		 * @return {array}
		 */
		$category_descriptions = implode( ' ', apply_filters( 'wpt_twitter_category_descs', $cat_descs ) );
	} else {
		$category     = '';
		$cat_desc     = '';
		$category_ids = array();
	}
	$values['cats']        = $cat_names;
	$values['cat_descs']   = $category_descriptions;
	$values['categoryIds'] = $category_ids;
	$values['category']    = ( $category ) ? html_entity_decode( $category, ENT_COMPAT, $encoding ) : '';
	$values['cat_desc']    = ( $cat_desc ) ? html_entity_decode( $cat_desc, ENT_COMPAT, $encoding ) : '';
	$post_excerpt          = ( '' === trim( $post->post_excerpt ) ) ? mb_substr( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 0, $excerpt_length ) : mb_substr( wp_strip_all_tags( strip_shortcodes( $post->post_excerpt ) ), 0, $excerpt_length );
	$values['postExcerpt'] = ( $post_excerpt ) ? html_entity_decode( $post_excerpt, ENT_COMPAT, $encoding ) : '';
	$thisposttitle         = $post->post_title;
	if ( '' === $thisposttitle && isset( $_POST['title'] ) ) {
		$thisposttitle = wp_kses_post( wp_unslash( $_POST['title'] ) );
	}
	$thisposttitle = wp_strip_all_tags( apply_filters( 'the_title', stripcslashes( $thisposttitle ), $post_ID ) );
	// These are common sequences that may not be fixed by html_entity_decode due to double encoding.
	$search               = array( '&apos;', '&#039;', '&quot;', '&#034;', '&amp;', '&#038;' );
	$replace              = array( "'", "'", '"', '"', '&', '&' );
	$thisposttitle        = str_replace( $search, $replace, $thisposttitle );
	$values['postTitle']  = html_entity_decode( $thisposttitle, ENT_QUOTES, $encoding );
	$values['postLink']   = wpt_link( $post_ID );
	$values['blogTitle']  = get_bloginfo( 'name' );
	$values['shortUrl']   = wpt_short_url( $post_ID );
	$values['postStatus'] = $post->post_status;
	$values['postType']   = $post->post_type;
	/**
	 * Filters post array to insert custom data that can be used in status update process.
	 *
	 * @hook wpt_post_info
	 *
	 * @param {array}   $values Existing values.
	 * @param {integer} $post_ID Post ID.
	 *
	 * @return {array}  $values
	 */
	$values = apply_filters( 'wpt_post_info', $values, $post_ID );

	return $values;
}

/**
 * Retrieve stored short URL.
 *
 * @param int $post_id Post ID.
 *
 * @return string|bool False if no stored URL.
 */
function wpt_short_url( $post_id ) {
	global $post_ID;
	if ( ! $post_id ) {
		$post_id = $post_ID;
	}
	$use_urls = ( get_option( 'wpt_use_stored_urls' ) === 'false' ) ? false : true;
	$short    = ( $use_urls ) ? get_post_meta( $post_id, '_wpt_short_url', true ) : false;
	$short    = ( '' === $short ) ? false : $short;

	return $short;
}

/**
 * Function checks for an alternate URL to be updated. Contribution by Bill Berry.
 *
 * @param int $post_ID Post ID.
 *
 * @return Link to use for this URL.
 */
function wpt_link( $post_ID ) {
	$ex_link       = false;
	$external_link = get_option( 'jd_twit_custom_url', '' );
	$permalink     = get_permalink( $post_ID );
	if ( '' !== $external_link ) {
		$ex_link = get_post_meta( $post_ID, $external_link, true );
	}

	return ( $ex_link ) ? $ex_link : $permalink;
}

/**
 * Generate hash tags from tags set on post.
 *
 * @param int $post_ID Post ID.
 *
 * @return string $hashtags Hashtags in format needed for status updates.
 */
function wpt_generate_hash_tags( $post_ID ) {
	$hashtags       = '';
	$term_meta      = false;
	$t_id           = false;
	$max_tags       = get_option( 'jd_max_tags', '3' );
	$max_characters = get_option( 'jd_max_characters', '20' );
	$max_characters = ( '0' === $max_characters || '' === $max_characters ) ? 100 : $max_characters + 1;
	if ( '0' === $max_tags || '' === $max_tags ) {
		$max_tags = 100;
	}
	$use_cats = ( '1' === get_option( 'wpt_use_cats' ) ) ? true : false;
	$tags     = ( true === $use_cats ) ? wp_get_post_categories( $post_ID, array( 'fields' => 'all' ) ) : get_the_tags( $post_ID );
	/**
	 * Change the taxonomy used by default to generate post tags. Array of terms attached to post.
	 *
	 * @hook wpt_hash_source
	 *
	 * @param {array} $tags Array of post terms.
	 * @param {int}   $post_ID Post ID.
	 *
	 * @return {array}
	 */
	$tags = apply_filters( 'wpt_hash_source', $tags, $post_ID );
	if ( $tags && count( $tags ) > 0 ) {
		$i = 1;
		foreach ( $tags as $value ) {
			if ( function_exists( 'wpt_pro_exists' ) ) {
				$t_id      = $value->term_id;
				$term_meta = get_option( "wpt_taxonomy_$t_id" );
			}
			$source = get_option( 'wpt_tag_source' );
			if ( 'slug' === $source ) {
				// If the tag has an '@' symbol as the first character, assume it is a mention unless set.
				if ( 0 === stripos( $value->name, '@' ) && ! $term_meta ) {
					$term_meta = 5;
				}
				$tag = $value->slug;
			} else {
				$tag = $value->name;
				// If the tag has an '@' symbol as the first character, assume it is a mention unless set.
				if ( 0 === stripos( $value->name, '@' ) && ! $term_meta ) {
					$term_meta = 4;
				}
			}
			$strip   = get_option( 'jd_strip_nonan' );
			$search  = '/[^\p{L}\p{N}\s]/u';
			$replace = get_option( 'jd_replace_character' );
			$replace = ( '[ ]' === $replace || '' === $replace ) ? '' : $replace;
			if ( false !== strpos( $tag, ' ' ) ) {
				// If multiple words, camelcase tag.
				$tag = ucwords( $tag );
			}
			$tag = str_ireplace( ' ', $replace, trim( $tag ) );
			$tag = preg_replace( '/[\/]/', $replace, $tag ); // remove forward slashes.
			$tag = ( '1' === $strip ) ? preg_replace( $search, $replace, $tag ) : $tag;

			switch ( $term_meta ) {
				case 1:
					$newtag = "#$tag";
					break;
				case 2:
					$newtag = "$$tag";
					break;
				case 3:
					$newtag = '';
					break;
				case 4:
					$newtag = $tag;
					break;
				case 5:
					$newtag = "@$tag";
					break;
				default:
					/**
					 * Change the default tag character. Default '#'.
					 *
					 * @hook wpt_tag_default
					 *
					 * @param {string} $char Character used to convert tags into hashtags.
					 * @param {int}    $t_id Term ID.
					 *
					 * @return {string}
					 */
					$newtag = apply_filters( 'wpt_tag_default', '#', $t_id ) . $tag;
			}
			if ( mb_strlen( $newtag ) > 2 && ( mb_strlen( $newtag ) <= $max_characters ) && ( $i <= $max_tags ) ) {
				$hashtags .= "$newtag ";
				++$i;
			}
		}
	}
	$hashtags = trim( $hashtags );
	if ( mb_strlen( $hashtags ) <= 1 ) {
		$hashtags = '';
	}

	return $hashtags;
}
