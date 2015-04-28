<?php
/**
 * This is a static utility class that provides helper functions for the
 * CAC Featured Content Widget Class. Any external functionality needed to keep
 * the main CAC Featured Content Widget class 'DRY', belongs in here.
 *
 * @author Dominic Giglio
 */

class CAC_Featured_Content_Helper {

	/**
	 * Somewhat surprisingly, this function doesn't (or at least, this
	 * functionality) doesn't seem to exist in the core.
	 *
	 * @param string $domain - The URL of the blog you're looking for info on
	 * @return object - An object containing information about the blog.
	 */
	public static function get_blog_by_domain($domain) {
		global $wpdb;

		// if no scheme, add one
		if ( false === strpos( $domain, '://' ) ) {
			$domain = "http://{$domain}";
		}

		// parse the URL
		$url_parts = parse_url( trailingslashit( $domain ) );

		if ( is_subdomain_install() ) {
			$blog_id = get_blog_id_from_url( $url_parts['host'] );

		// subdirectory install
		} else {
			$blog_id = get_blog_id_from_url( $url_parts['host'], $url_parts['path'] );
		}

		$blog_data = get_blog_details( $blog_id );

		// update the blog domain on MS installs
		// this makes domain mapping plugin work
		if ( is_multisite() && $blog_data ) {
			$blog_data->siteurl = get_home_url( $blog_id, '/' );
		}

		return $blog_data;
	}

	/**
	 * Given a post slug and a blog id, this function retrieves a post object.
	 *
	 * @param string $slug - The post url slug
	 * @param int $blog_id - ID of the blog you're trying to get a post from
	 * @return object
	 */
	public static function get_post_by_slug( $slug, $blog_id = '' ) {
		$single_post = false;

		// setup $posts var
		if ( is_multisite() ) {
			switch_to_blog($blog_id);
			$posts = new WP_Query( array( 'name' => $slug, 'post_type' => array( 'post', 'page' ) ) );
			restore_current_blog();
		} else {
			$posts = new WP_Query( array( 'name' => $slug, 'post_type' => array( 'post', 'page' ) ) );
		}

		if ( $posts->have_posts() ) :
			while ( $posts->have_posts() ) :
				$posts->the_post();
				if ( $posts->post->post_name == $slug ) {
					$single_post = $posts->post;
					break;
				}
			endwhile;
		endif;

		// update the post guid on MS installs
		// this makes domain mapping plugin work
		if ( is_multisite() ) {
			$single_post->guid = get_blog_permalink( $blog_id, $single_post->ID );
		}

		return $single_post;
	}

	/**
	 * This method attempts to retrieve an image from the blog_id passed to it
	 *
	 * @param int $blog_id - ID of the blog you're trying to get an image from
	 * @param int $width - The desired width of the returned image
	 * @param int $height - The desired height of the returned image
	 * @return str - The content with images stripped and replaced with a single thumb.
	 */
	public static function get_image_from_blog( $blog_id, $width, $height ) {
		global $post;

		$image = false;

		switch_to_blog($blog_id);

		$posts = new WP_Query( array( 'post_type' => array( 'post', 'page' ) ) );

		if ( $posts->have_posts() ) :
			while ( $posts->have_posts() ) :
				$posts->the_post();
					$image = self::get_image_from_post( get_the_content(), $width, $height );

					if ( ! empty( $image ) )
						break;
			endwhile;
		endif;

		restore_current_blog();

		return $image;
	}

	/**
	*
	* This is pretty much taken directly from buddy press. Given some post content
	* this method will return an html <img> element. The element's src attribute
	* will be set to the path of the first image found in the post. The img's width
	* and height attributes will be set to those passed as arguments.
	*
	* @param $content str - The content to work with
	* @param int $width - The width used in the <img> element
	* @param int $height - The height used in the <img> element
	* @return str - The content with images stripped and replaced with a single thumb.
	**/
	public static function get_image_from_post( $content, $width = 50, $height = 50 ) {
		preg_match_all( '/<img[^>]*>/Ui', $content, $matches );
		$content = preg_replace('/<img[^>]*>/Ui', '', $content );

		if ( ! empty( $matches[0][0] ) ) {
			/* Get the SRC value */
			preg_match( '/<img.*?(src\=[\'|"]{0,1}.*?[\'|"]{0,1})[\s|>]{1}/i', $matches[0][0], $src );

			if ( ! empty( $src ) ) {
				$src = substr( substr( str_replace( 'src=', '', $src[1] ), 0, -1 ), 1 );

				$content = '<img class="avatar" src="' . esc_attr( $src) . '" width="' . $width .
				  '" height="' . $height . '" alt="' . __( 'Thumbnail', 'buddypress' ) .
				  '" class="align-left thumbnail" />';
			}
		} else {
			$content = false;
		}

		return $content;
	}

	/**
	 * Widget Error
	 *
	 * This function can be used to render an error message on the front end of your site.
	 * It matches the HTML structure of the rest of the widget views.
	 *
	 * @param array $widget
	 * @param array $params
	 * @param array $sidebar
	 */
	public static function error( $msg = '' ) {
	?>

		<h3><?php _e( 'Error', 'cac-featured-content' ) ?></h3>
		<div class="cfcw-content">
			<p><?php echo $msg ?></p>
			<p><?php _e( 'Please correct and reload.', 'cac-featured-content' ) ?></p>
		</div>

	<?php
	}

} // end CAC_Featured_Content_Helper class

/**
 * Get a numeric user ID from either an email address or a login.
 *
 * @param string $string
 * @return int
 */
function cacfc_get_user_id_from_string( $string ) {
	$user_id = 0;

	if ( is_email( $string ) ) {
		$user = get_user_by( 'email', $string );
	} else {
		$user = get_user_by( 'login', $string );
	}

	if ( $user ) {
		$user_id = $user->ID;
	}

	return $user_id;
}
