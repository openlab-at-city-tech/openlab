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
			'label' => 'Unknown',
			'value' => 'u',
			'url'   => '',
		],
		[
			'label' => 'Standard Copyright',
			'value' => 'c',
			'url'   => '',
		],
		[
			'label' => 'Public Domain',
			'value' => 'pd',
			'url'   => '',
		],
		[
			'label' => 'Fair Use',
			'value' => 'fu',
			'url'   => '',
		],
		[
			'label' => 'CC BY',
			'value' => 'cc-by',
			'url'   => 'https://creativecommons.org/licenses/by/4.0',
		],
		[
			'label' => 'CC BY-SA',
			'value' => 'cc-by-sa',
			'url'   => 'https://creativecommons.org/licenses/by-sa/4.0',
		],
		[
			'label' => 'CC BY-ND',
			'value' => 'cc-by-nd',
			'url'   => 'https://creativecommons.org/licenses/by-nd/4.0',
		],
		[
			'label' => 'CC BY-NC',
			'value' => 'cc-by-nc',
			'url'   => 'https://creativecommons.org/licenses/by-nc/4.0',
		],
		[
			'label' => 'CC BY-NC-SA',
			'value' => 'cc-by-nc-sa',
			'url'   => 'https://creativecommons.org/licenses/by-nc-sa/4.0',
		],
		[
			'label' => 'CC BY-NC-ND',
			'value' => 'cc-by-nc-nd',
			'url'   => 'https://creativecommons.org/licenses/by-nc-nd/4.0',
		],
	];

	return $licenses;
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
 * Get the license object by ID/value.
 *
 * @param string $id
 * @return array
 */
function get_the_license( $id ) {
	$licenses = get_licenses();
	$license  = array_filter( $licenses, function( $item ) use ( $id ) {
		return $item['value'] === $id;
	} );

	return end( $license );
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

	if ( ! empty( $item['authorName'] ) ) {
		$parts[] = format( $item['authorName'], $item['authorUrl'] );
	}

	if ( ! empty( $item['datePublished'] ) ) {
		$parts[] = sprintf( '(%s)', $item['datePublished'] );
	}

	if ( ! empty( $item['title'] ) ) {
		$parts[] = format( $item['title'], $item['titleUrl'] );
	}

	if ( ! empty( $item['derivative'] ) ) {
		$parts[] = sprintf(
			'Retrieved from %s',
			format( $item['derivative'], $item['derivative'] )
		);
	}

	if ( ! empty( $item['publisher'] ) ) {
		$parts[] = format( $item['publisherUrl'], $item['publisherUrl'] );
	}

	if ( ! empty( $item['project'] ) ) {
		$parts[] = format( $item['project'], $item['projectUrl'] );
	}

	if ( ! empty( $item['license'] ) ) {
		$license = get_the_license( $item['license'] );
		$parts[] = sprintf(
			'Licensed under %s',
			format( $license['label'], $license['url'] )
		);
	}

	return implode( '. ', $parts );
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
