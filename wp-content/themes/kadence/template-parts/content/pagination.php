<?php
/**
 * Template part for displaying a pagination
 *
 * @package kadence
 */

namespace Kadence;

the_posts_pagination(
	apply_filters(
		'kadence_pagination_args',
		array(
			'mid_size'           => 2,
			'prev_text'          => '<span class="screen-reader-text">' . __( 'Previous Page', 'kadence' ) . '</span>' . kadence()->get_icon( 'arrow-left', _x( 'Previous', 'previous set of archive results', 'kadence' ) ),
			'next_text'          => '<span class="screen-reader-text">' . __( 'Next Page', 'kadence' ) . '</span>' . kadence()->get_icon( 'arrow-right', _x( 'Next', 'next set of archive results', 'kadence' ) ),
			'screen_reader_text' => __( 'Page navigation', 'kadence' ),
		)
	)
);
