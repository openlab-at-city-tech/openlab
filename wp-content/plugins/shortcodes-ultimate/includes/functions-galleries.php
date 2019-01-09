<?php

/**
 * Get slides data.
 *
 * @since  5.0.5
 * @param array   $args Query args.
 * @return array        Slides collection.
 */
function su_get_slides( $args ) {

	$args = wp_parse_args( $args, array(
			'source'  => 'none',
			'limit'   => 20,
			'gallery' => null,
			'type'    => '',
			'link'    => 'none'
		) );

	if (
		$args['gallery'] !== null ||
		( $args['source'] === 'none' && get_option( 'su_option_galleries-432' ) )
	) {
		return su_get_slides_432( $args );
	}

	$slides = array();

	foreach ( array( 'media', 'posts', 'category', 'taxonomy' ) as $type ) {

		if ( strpos( trim( $args['source'] ), $type . ':' ) === 0 ) {
			$args['source'] = array(
				'type' => $type,
				'val'  => (string) trim( str_replace( array( $type . ':', ' ' ), '', $args['source'] ), ',' )
			);
			break;
		}

	}

	if ( ! is_array( $args['source'] ) ) {
		return $slides;
	}

	$query = array( 'posts_per_page' => $args['limit'] );

	if ( 'media' === $args['source']['type'] ) {

		$query['post_type'] = 'attachment';
		$query['post_status'] = 'any';
		$query['post__in'] = (array) explode( ',', $args['source']['val'] );
		$query['orderby'] = 'post__in';

	}

	// Source: posts
	if ( 'posts' === $args['source']['type'] ) {

		if ( 'recent' !== $args['source']['val'] ) {

			$query['post__in'] = (array) explode( ',', $args['source']['val'] );
			$query['orderby'] = 'post__in';
			$query['post_type'] = 'any';

		}

	}

	elseif ( 'category' === $args['source']['type'] ) {
		$query['category__in'] = (array) explode( ',', $args['source']['val'] );
	}

	elseif ( 'taxonomy' === $args['source']['type'] ) {

		$args['source']['val'] = explode( '/', $args['source']['val'] );

		if (
			! is_array( $args['source']['val'] ) ||
			count( $args['source']['val'] ) !== 2
		) {
			return $slides;
		}

		$query['tax_query'] = array(
			array(
				'taxonomy' => $args['source']['val'][0],
				'field' => 'id',
				'terms' => (array) explode( ',', $args['source']['val'][1] )
			)
		);
		$query['post_type'] = 'any';

	}

	$query = apply_filters( 'su/slides_query', $query, $args );
	$query = new WP_Query( $query );

	if ( is_array( $query->posts ) ) {

		foreach ( $query->posts as $post ) {

			$thumb = $args['source']['type'] === 'media' || $post->post_type === 'attachment'
				? $post->ID
				: get_post_thumbnail_id( $post->ID );

			if ( ! is_numeric( $thumb ) ) {
				continue;
			}

			$slide = array(
				'image' => wp_get_attachment_url( $thumb ),
				'link'  => '',
				'title' => get_the_title( $post->ID )
			);

			if ( 'image' === $args['link'] || 'lightbox' === $args['link'] ) {
				$slide['link'] = $slide['image'];
			}
			elseif ( 'custom' === $args['link'] ) {
				$slide['link'] = get_post_meta( $post->ID, 'su_slide_link', true );
			}
			elseif ( 'post' === $args['link'] ) {
				$slide['link'] = get_permalink( $post->ID );
			}
			elseif ( 'attachment' === $args['link'] ) {
				$slide['link'] = get_attachment_link( $thumb );
			}

			$slides[] = $slide;

		}

	}

	return $slides;

}

/**
 * Get slides data.
 *
 * Deprecated since 4.3.2.
 *
 * @since  5.0.5
 * @param array   $args Query args.
 * @return array       Slides collection.
 */
function su_get_slides_432( $args ) {

	$args = wp_parse_args( $args, array(
			'gallery' => 1
		) );

	$slides = array();

	$args['gallery'] = $args['gallery'] === null
		? 0
		: $args['gallery'] - 1;

	$galleries = get_option( 'su_option_galleries-432' );

	if ( ! is_array( $galleries ) ) {
		return $slides;
	}

	if ( isset( $galleries[ $args['gallery'] ] ) ) {
		$slides = $galleries[ $args['gallery'] ]['items'];
	}

	return $slides;

}
