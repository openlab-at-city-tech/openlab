<?php

/**
 * Adds custom embeds
 */

/**
 * Adds [openprocessing] shortcode
 */
function openlab_openprocessing_shortcode( $atts ) {
	$html = '';

	$errors = array();
	foreach ( array( 'id', 'height', 'width' ) as $key ) {
		$v = isset( $atts[ $key ] ) ? (int) $atts[ $key ] : 0;
		if ( ! $v ) {
			switch ( $key ) {
				case 'id':
					$errors[] = 'You must provide the numeric id of the openprocessing.org sketch. In a URL like "http://openprocessing.org/sketch/12345", the id is <strong>12345</strong>';
					break;

				default:
					$errors[] = sprintf( 'You didn&#8217;t provide the following necessary attribute: <strong>%s</strong>', $key );
					break;
			}
		}
	}

	if ( empty( $errors ) ) {
		$height = (int) $atts['height'];
		$width  = (int) $atts['width'];
		$html   = sprintf(
			'<iframe width="%s" height="%s" scrolling="no" frameborder="0" src="http://www.openprocessing.org/sketch/%s/embed/?width=%s&height=%s&border=true"></iframe>',
			$width + 28, // iframe needs padding
			$height + 50,
			(int) $atts['id'],
			$width,
			$height
		);
	} else {
		$estring = '';
		foreach ( $errors as $e ) {
			$estring .= '<li>' . $e . '</li>';
		}

		$html = sprintf( '<em>Your openprocessing.org sketch could not be displayed, because of the following errors: <ul>%s</ul></em>', $estring );
	}

	return $html;
}
add_shortcode( 'openprocessing', 'openlab_openprocessing_shortcode' );

/**
 * Register auto-embed handlers.
 */
function openlab_register_embed_handlers() {
	wp_embed_register_handler( 'screencast', '#https?://([^\.]+)\.screencast\.com/([^/?]+)#i', 'openlab_embed_handler_screencast' );

	wp_embed_register_handler(
		'pinterest',
		'#'
		. 'https?://'
		. '(?:www\.)?'
		. '(?:[a-z]{2}\.)?'
		. 'pinterest\.[a-z.]+/'
		. '([^/]+)'
		. '(/[^/]+)?'
		. '#',
		'openlab_pinterest_embed_handler'
	);

	wp_embed_register_handler( 'miro', '#https?://(?:[a-z]{2}\.)?miro\.com/#i', 'openlab_embed_handler_miro' );
	wp_embed_register_handler( 'desmos', '#https?://([^\.]+)\.desmos\.com/#i', 'openlab_embed_handler_desmos' );
	wp_embed_register_handler( 'geogebra', '#https?://([^\.]+)\.geogebra\.org/#i', 'openlab_embed_handler_geogebra' );
	wp_embed_register_handler( 'yuja', '#https?://([^\.]+)\.yuja\.com/#i', 'openlab_embed_handler_yuja' );
	wp_embed_register_handler( 'mathdot', '#https?://mathdev\.citytech\.cuny\.edu/DOT/([^/]*)/?#i', 'openlab_embed_handler_mathdot' );
	wp_embed_register_handler( 'circuitverse', '#https?://circuitverse\.org/#i', 'openlab_embed_handler_circuitverse' );

	$network_home_url = network_home_url(); // Network home URL
	wp_embed_register_handler( 'openlab', "#{$network_home_url}#i", 'openlab_embed_local_uploaded_images' );

	// Register oEmbed provider for Yuja
	wp_oembed_add_provider( 'https://citytech.yuja.com/V/*', 'https://citytech.yuja.com/services/oembed', false );
}
add_action( 'init', 'openlab_register_embed_handlers' );

/**
 * screencast.com embed callback.
 */
function openlab_embed_handler_screencast( $matches, $attr, $url, $rawattr ) {
	if ( 'app' === $matches[1] ) {
		// "Modern" Screencast.
		$embed_url = $matches[0] . '/e';
	} else {
		// "Classic" Screencast.
		$cached = wp_cache_get( 'screencast_embed_url_v2_' . $url );
		if ( false === $cached ) {
			// This is the worst thing in the whole world.
			$r = wp_remote_get( $url );
			$b = wp_remote_retrieve_body( $r );
			$b = htmlspecialchars_decode( $b );

			$embed_url = '';
			if ( preg_match( '|<iframe[^>]+src="([^"]+screencast\.com[^"]+)"|', $b, $url_matches ) ) {
				$embed_url = str_replace( '/tsc_player.swf', '', $url_matches[1] );
			}

			wp_cache_set( 'screencast_embed_url_v2_' . $url, $embed_url );
		} else {
			$embed_url = $cached;
		}
	}

	// Get height/width from URL params, if available.
	$height = 450;
	$width  = 800;
	$query  = parse_url( $url, PHP_URL_QUERY );
	if ( $query ) {
		parse_str( $query, $parts );

		if ( $parts['height'] ) {
			$height = intval( $parts['height'] );
		}

		if ( $parts['width'] ) {
			$width = intval( $parts['width'] );
		}
	}

	$template = '<iframe class="tscplayer_inline embeddedObject" name="tsc_player" scrolling="no" frameborder="0" type="text/html" style="overflow:hidden;" src="%s" height="%s" width="%s" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

	$html = sprintf( $template, set_url_scheme( $embed_url ), $height, $width );
	return $html;
}

/**
 * Stolen from Jetpack.
 */
function openlab_pinterest_embed_handler( $matches, $attr, $url ) {
	// Pinterest's JS handles making the embed
	$script_src = '//assets.pinterest.com/js/pinit.js';
	wp_enqueue_script( 'pinterest-embed', $script_src, array(), false, true );

	$path = parse_url( $url, PHP_URL_PATH );
	if ( 0 === strpos( $path, '/pin/' ) ) {
		$embed_type = 'embedPin';
	} elseif ( 0 === strpos( $path, '/topics/' ) ) {
		// Pinterest oEmbed doesn't support topics.
		return $url;
	} elseif ( preg_match( '#^/([^/]+)/?$#', $path ) ) {
		$embed_type = 'embedUser';
	} elseif ( preg_match( '#^/([^/]+)/([^/]+)/?$#', $path ) ) {
		$embed_type = 'embedBoard';
	} else {
		if ( current_user_can( 'edit_posts' ) ) {
			return __( 'Sorry, that Pinterest URL was not recognized.', 'jetpack' );
		}
		return;
	}

	$return = sprintf( '<a data-pin-do="%s" href="%s"></a>', esc_attr( $embed_type ), esc_url( $url ) );

	// If we're generating an embed view for the WordPress Admin via ajax...
	if ( doing_action( 'wp_ajax_parse-embed' ) ) {
		$return .= sprintf( '<script src="%s"></script>', esc_url( $script_src ) );
	}

	return $return;
}

/**
 * Miro.com embed callback
 */
function openlab_embed_handler_miro( $matches, $attr, $url ) {
	$path = parse_url( $url, PHP_URL_PATH );

	// Create array and remove empty values
	$path = array_filter( explode( '/', $path ) );

	// ID is always last in the array
	$id = end($path);

	$embed = sprintf(
		'<div class="miro-iframe-container" style="position: relative; width: 100%%; height: 0; padding-bottom: 56.25%%;">
		<iframe
			src="https://miro.com/app/live-embed/%s?embedAutoplay=true"
			style="position: absolute; top: 0; left: 0; width:100%%; height:100%%; border: 0;"
			frameborder=0
			scrolling="no"
			allowFullScreen>
		</iframe>
		</div>',
		esc_attr( $id )
	);

	return $embed;
}

/**
 * Desmos.com embed callback
 */
function openlab_embed_handler_desmos( $matches, $attr, $url, $rawattr ) {
	$path = parse_url( $url, PHP_URL_PATH );

	// Create array and remove empty values
	$path = array_filter( explode( '/', $path ) );

	// ID is always last in the array
	$id = end($path);

	$embed = sprintf(
		'<div class="desmos-iframe-container" style="position: relative; width: 100%%; height: 0; padding-bottom: 56.25%%;">
		<iframe
			src="https://www.desmos.com/calculator/%s?embed"
			style="position: absolute; top: 0; left: 0; width:100%%; height:100%%; border: 0;" frameborder=0>
		</iframe>
		</div>',
		esc_attr( $id )
	);

	return $embed;
}

/**
 * Geogebra.org embed callback
 */
function openlab_embed_handler_geogebra( $matches, $attr, $url ) {
	$path = parse_url( $url, PHP_URL_PATH );

	// Create array and remove empty values
	$path = array_filter( explode( '/', $path ) );

	// ID is always last in the array
	$id = end($path);

	$embed = sprintf(
		'<div class="geogebra-iframe-container" style="position: relative; width: 100%%; height: 0; padding-bottom: 56.25%%;">
		<iframe
			scrolling="no"
			src="https://www.geogebra.org/material/iframe/id/%s/rc/true/ai/true/sdz/true/smb/true/stb/true/stbh/true/ld/false/sri/false/sfsb/true/szb/true"
			style="position: absolute; top: 0; left: 0; width:100%%; height:100%%; border: 0;" allowfullscreen>
		</iframe>
		</div>',
		esc_attr( $id )
	);

	return $embed;
}

/**
 * Yuja.com embed callback
 */
function openlab_embed_handler_yuja( $matches, $attr, $url ) {
	$url = str_replace( 'Watch?', 'Video?', $url);

	return wp_oembed_get($url);
}

/**
 * Callback for math DOT embed game.
 *
 * @param array $matches Matches from embed URL regex.
 * @param array $attr    Attributes from embed URL regex.
 * @param string $url    URL.
 * @return string
 */
function openlab_embed_handler_mathdot( $matches, $attr, $url ) {
	$embed = sprintf(
		'<div class="mathdot-iframe-container" style="position: relative; width: 100%%; height: 0; padding-bottom: 56.25%%;">
		<iframe
			scrolling="no"
			src="%s"
			style="position: absolute; top: 0; left: 0; width:100%%; height:100%%; border: 0;" allowfullscreen>
		</iframe>
		</div>',
		esc_attr( $url )
	);

	return $embed;
}

/**
 * Locally uploaded images embed callback.
 */
function openlab_embed_local_uploaded_images( $matches, $attr, $url ) {
	// Supported image extensions
	$supported_images = array(
		'jpg',
		'jpeg',
		'png',
		'gif'
	);

	// Get extension from the URL
	$ext = strtolower( pathinfo( $url, PATHINFO_EXTENSION ) );

	// If URL doesn't end with supported image extension, return the URL
	if( ! in_array( $ext, $supported_images ) ) {
		return $url;
	}

	$embed = sprintf('<img src="%s" />', esc_url( $url ) );

	return $embed;
}

/**
 * Padlet embed shortcode.
 */
function openlab_padlet_shortcode( $attr = [] ) {
    global $content_width;

    $r = shortcode_atts(
        [
            'key'    => '',
            'height' => 608,

            // not used at the moment
            'width'  => ! empty( $content_width ) ? (int) $content_width : 500,
        ],
        $attr
    );

    if ( empty( $r['key'] ) ) {
        return '';
    }

    if ( empty( $r['height'] ) || ! is_numeric( $r['height'] ) ) {
        $r['height'] = 608;
    }

    return sprintf(
        '<div class="padlet-embed" style="border:1px solid rgba(0,0,0,0.1);border-radius:2px;box-sizing:border-box;overflow:hidden;position:relative;width:100%%;background:#F4F4F4"><iframe src="https://padlet.com/embed/%1$s" frameborder="0" allow="camera;microphone;geolocation" style="width:100%%;height:%2$dpx;display:block;padding:0;margin:0"></iframe></div>',
        sanitize_title( $r['key'] ),
        (int) $r['height']
    );
}
add_shortcode( 'padlet', 'openlab_padlet_shortcode' );

/**
 * circuitverse shortcode.
 */
function openlab_circuitverse_shortcode( $attr = [] ) {
	static $counter = 0;

	$r = shortcode_atts(
		[
			'url'           => '',
			'height'        => 500,
			'width'         => 500,
			'theme'         => 'default',
			'clock_time'    => '1',
			'zoom_in_out'   => '1',
			'fullscreen'    => '1',
			'display_title' => '1',
		],
		$attr
	);

	if ( empty( $r['url'] ) ) {
		return '';
	}

	$domain = parse_url( $r['url'], PHP_URL_HOST );
	if ( 'circuitverse.org' !== $domain ) {
		return '';
	}

	// Try to get the project ID from the URL.
	$project_id = '';

	$path = parse_url( $r['url'], PHP_URL_PATH );

	$user_project_regex = '/\/users\/(\d+)\/projects\/([a-z0-9-]+)/';
	if ( preg_match( $user_project_regex, $path, $matches ) ) {
		$project_id = $matches[2];
	}

	if ( ! $project_id ) {
		return '';
	}

	$boolean_attributes = [ 'clock_time', 'display_title', 'fullscreen', 'zoom_in_out' ];
	foreach ( $boolean_attributes as $attr ) {
		$r[ $attr ] = filter_var( $r[ $attr ], FILTER_VALIDATE_BOOLEAN ) ? 'true' : 'false';
	}

	$embed_src = sprintf(
		'https://circuitverse.org/simulator/embed/%s?theme=%s&display_title=%s&clock_time=%s&fullscreen=%s&zoom_in_out=%s',
		$project_id,
		$r['theme'],
		$r['display_title'],
		$r['clock_time'],
		$r['fullscreen'],
		$r['zoom_in_out']
	);

	++$counter;

	$embed = sprintf(
		'<iframe id="circuitverse-embed-%s-%s" src="%s" style="border-width:; border-style: solid; border-color:;" name="myiframe" id="projectPreview" scrolling="no" frameborder="1" marginheight="0px" marginwidth="0px" height="%d" width="%d" allowFullScreen></iframe>',
		esc_attr( $project_id ),
		(int) $counter,
		esc_url( $embed_src ),
		(int) $r['height'],
		(int) $r['width']
	);

	return $embed;
}
add_shortcode( 'circuitverse', 'openlab_circuitverse_shortcode' );

/**
 * Circuitverse embed callback.
 *
 * @param array $matches Matches from embed URL regex.
 * @param array $attr    Attributes from embed URL regex.
 * @param string $url    URL.
 * @return string
 */
function openlab_embed_handler_circuitverse( $matches, $attr, $url ) {
	return openlab_circuitverse_shortcode( [ 'url' => $url ] );
}
