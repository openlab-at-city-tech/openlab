<?php
/**
 * Helper functions.
 */

namespace OpenLab\Attributions\Helpers;

/**
 * Get list of licenses.
 *
 * @return array $licenses
 */
function get_licenses() {
	$licenses = [
		[
			'label' => __( 'Unknown', 'openlab-attributions' ),
			'value' => 'u',
			'url'   => '',
		],
		[
			'label' => __( 'Standard Copyright', 'openlab-attributions' ),
			'value' => 'c',
			'url'   => '',
		],
		[
			'label' => __( 'Public Domain', 'openlab-attributions' ),
			'value' => 'pd',
			'url'   => '',
		],
		[
			'label' => __( 'Fair Use', 'openlab-attributions' ),
			'value' => 'fu',
			'url'   => '',
		],
		[
			'label' => __( 'CC BY', 'openlab-attributions' ),
			'value' => 'cc-by',
			'url'   => 'https://creativecommons.org/licenses/by/4.0',
		],
		[
			'label' => __( 'CC BY-SA', 'openlab-attributions' ),
			'value' => 'cc-by-sa',
			'url'   => 'https://creativecommons.org/licenses/by-sa/4.0',
		],
		[
			'label' => __( 'CC BY-ND', 'openlab-attributions' ),
			'value' => 'cc-by-nd',
			'url'   => 'https://creativecommons.org/licenses/by-nd/4.0',
		],
		[
			'label' => __( 'CC BY-NC', 'openlab-attributions' ),
			'value' => 'cc-by-nc',
			'url'   => 'https://creativecommons.org/licenses/by-nc/4.0',
		],
		[
			'label' => __( 'CC BY-NC-SA', 'openlab-attributions' ),
			'value' => 'cc-by-nc-sa',
			'url'   => 'https://creativecommons.org/licenses/by-nc-sa/4.0',
		],
		[
			'label' => __( 'CC BY-NC-ND', 'openlab-attributions' ),
			'value' => 'cc-by-nc-nd',
			'url'   => 'https://creativecommons.org/licenses/by-nc-nd/4.0',
		],
	];

	return $licenses;
}

/**
 * Get the license object by ID/value.
 *
 * @param string $value License value.
 * @return array
 */
function get_the_license( $value ) {
	$licenses = get_licenses();
	$license  = array_filter(
		$licenses,
		function( $item ) use ( $value ) {
			return $item['value'] === $value;
		}
	);

	return end( $license );
}

/**
 * Returns attribution name or link.
 *
 * @param string $title
 * @param string $url
 * @return string
 */
function format( $title, $url ) {
	if ( empty( $url ) ) {
		return $title;
	}

	return sprintf( '<a href="%1$s">%2$s</a>', esc_url( $url ), esc_html( $title ) );
}

/**
 * Format attribution license.
 *
 * @param string $value License value.
 * @return string $text
 */
function format_license( $value ) {
	$license = get_the_license( $value );
	$text    = '';

	switch ( $value ) {
		case 'pd':
		case 'fu':
			$text = sprintf( '%s.', $license['label'] );
			break;

		case 'u':
			$text = __( 'License unknown.', 'openlab-attributions' );
			break;

		default:
			// translators: Link to license.
			$text = sprintf( esc_html__( 'Licensed under %s.', 'openlab-attributions' ), format( $license['label'], $license['url'] ) );
			break;
	}

	return $text;
}

/**
 * Format "Adapted From" data into a string.
 *
 * @param array $item Attribution data.
 * @return string     Formatted "Adapted From" string.
 */
function format_adapted_from( $item ) {
	$is_legacy = empty( $item['adaptedTitle'] ) &&
				empty( $item['adaptedAuthor'] ) &&
				empty( $item['adaptedLicense'] ) &&
				! empty( $item['derivative'] );

	if ( $is_legacy ) {
		return sprintf(
			// translators: URL of the original work
			__( 'Adapted from the <a href="%s">original work</a>', 'openlab-attributions' ),
			$item['derivative']
		);
	}

	$license = get_the_license( $item['adaptedLicense'] );

	if ( empty( $item['adaptedAuthor'] ) ) {
		return sprintf(
			// translators: 1. Link to original work; 2. Link to license
			__( 'Adapted from %1$s, licensed under %2$s.', 'openlab-attributions' ),
			format( $item['adaptedTitle'], $item['derivative'] ),
			format( $license['label'], $license['url'] )
		);
	}

	return sprintf(
		// translators: 1. Link to original work; 2. Author name; 3. Link to license
		__( 'Adapted from %1$s by %2$s, licensed under %3$s.', 'openlab-attributions' ),
		format( $item['adaptedTitle'], $item['derivative'] ),
		format( $item['adaptedAuthor'], null ),
		format( $license['label'], $license['url'] )
	);
}

/**
 * Generate attribution markup from the data.
 *
 * Template:
 * `{author last, author first - linked if there's a URL}. ({date published}).
 * {item title - linked if there's a URL}. Retrieved from {derivative url}.
 * {organization - linked if URL}. {project - linked if URL}. Licensed under {license}`
 *
 * @param array $item Attribution data.
 * @return string     Formatted attribution HTML.
 */
function get_the_attribution( $item ) {
	$parts = [];

	if ( ! empty( $item['title'] ) ) {
		$parts[] = format( $item['title'], $item['titleUrl'] );
	}

	if ( ! empty( $item['authorName'] ) ) {
		$parts[] = format( $item['authorName'], $item['authorUrl'] );
	}

	if ( ! empty( $item['publisher'] ) ) {
		$parts[] = format( $item['publisher'], $item['publisherUrl'] );
	}

	if ( ! empty( $item['project'] ) ) {
		$parts[] = format( $item['project'], $item['projectUrl'] );
	}

	if ( ! empty( $item['datePublished'] ) ) {
		$parts[] = $item['datePublished'];
	}

	if ( ! empty( $item['license'] ) ) {
		$parts[] = format_license( $item['license'] );
	}

	$attribution = implode( '. ', $parts );

	if ( ! empty( $item['adaptedTitle'] ) || ! empty( $item['derivative'] ) ) {
		$attribution .= sprintf(
			'%1$s%2$s',
			empty( $parts ) ? '' : ' ',
			format_adapted_from( $item )
		);
	}

	// Append "." at the end of the sentence if there is none.
	if( substr($attribution, -1) != '.' ) {
		$attribution .= '.';
	}

	return $attribution;
}

/**
 * Array of post types supporting attributions.
 *
 * @return array $post_types
 */
function get_supported_post_types() {
	$post_types = [ 'post', 'page' ];

	return apply_filters( 'ol_image_attribution_supported_post_types', $post_types );
}

/**
 * Get attribution marker IDs from content.
 *
 * @param string $content
 * @return array
 */
function get_attribution_marker_ids( $content ) {
	if ( ! preg_match_all( '/anchor-(?P<id>[\w-]+)/i', $content, $markers ) ) {
		return [];
	}

	return $markers['id'];
}

/**
 * Sort the array using the given callback.
 *
 * This function is a copy of Laravel collect sortBy method.
 * @link https://laravel.com/docs/5.8/collections#method-sortby
 *
 * @param  callable|string  $callback
 * @param  int  $options
 * @param  bool  $descending
 * @return array $results
 */
function sort_by( array $items, $callback, $options = SORT_REGULAR, $descending = false ) {
	$results = [];

	// First we will loop through the items and get the comparator from a callback
	// function which we were given. Then, we will sort the returned values and
	// and grab the corresponding values for the sorted keys from this array.
	foreach ( $items as $key => $value ) {
		$results[ $key ] = $callback( $value, $key );
	}

	$descending ? arsort( $results, $options )
		: asort( $results, $options );

	// Once we have sorted all of the keys in the array, we will loop through them
	// and grab the corresponding value so we can set the underlying items list
	// to the sorted version.
	foreach ( array_keys( $results ) as $key ) {
		$results[ $key ] = $items[ $key ];
	}

	return $results;
}
