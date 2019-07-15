<?php
/**
 * Multipage Templates.
 *
 * @package Multipage
 * @subpackage Templates
 * @since 1.5
 */
 
/**
 * The formatted output of a list of pages.
 *
 * Displays page links for paginated posts (i.e. includes the <!--nextpage-->
 * Quicktag one or more times). This tag must be within The Loop.
 *
 * @since 1.2.0
 *
 * @global int $page
 * @global int $numpages
 * @global int $multipage
 *
 * @param string|array $args {
 *     Optional. Array or string of default arguments.
 *
 *     @type string       $before           		HTML or text to prepend to each link. Default is `<p> Pages:`.
 *     @type string       $after           			HTML or text to append to each link. Default is `</p>`.
 *     @type string       $link_before      		HTML or text to prepend to each link, inside the `<a>` tag.
 *                                          		Also prepended to the current item, which is not linked. Default empty.
 *     @type string       $link_after       		HTML or text to append to each Pages link inside the `<a>` tag.
 *                                          		Also appended to the current item, which is not linked. Default empty.
 *     @type string       $continue_or_prev_next    Indicates whether continue should be used. Valid values are continue,
 *                                          		next-previous and hidden. Default is 'continue'.
 *     @type string       $separator        		Text between pagination links. Default is ' '.
 *     @type string       $previouspagelink 		Link text for the previous page link, if available. Default is 'Previous Page'.
 *     @type int|bool     $echo             		Whether to echo or not. Accepts 1|true or 0|false. Default 1|true.
 * }
 * @return string Formatted output in HTML.
 */
function mpp_link_pages( $args = '' ) {
	global $page, $numpages, $multipage, $subpages;

	$defaults = array(
		'before'				=> '<div>',
		'after'					=> '</div>',
		'link_before'			=> '',
		'link_after'			=> '',
		'continue_or_prev_next'	=> 'continue',
		'separator'				=> ' ',
		'nextpagelink'			=> __( 'Next page' ),
		'previouspagelink'		=> __( 'Previous page' ),
		'echo'					=> 0
	);

	$params = wp_parse_args( $args, $defaults );

	/**
	 * Filters the arguments used in retrieving page links for paginated posts.
	 *
	 * @since 3.0.0
	 *
	 * @param array $params An array of arguments for page links for paginated posts.
	 */
	$r = apply_filters( 'mpp_link_pages_args', $params );

	$output = '';
	if ( $multipage ) {
		if ( 'continue' == $r['continue_or_prev_next'] ) {
			$output .= $r['before'];
			$subpage_keys = array_keys( $subpages );
			$next = $page + 1;
			if ( $next <= $numpages ) {
				$link = _mpp_link_page( $next ) . $r['link_before'] . $subpages[ $subpage_keys[ $page ] ] . $r['link_after'] . '</a>';
				$text = sprintf( __( 'Continue: %s', 'sgr-nextpage-titles' ), $link );
			} else {
				$link = _mpp_link_page( 1 ) . $r['link_before'] . $subpages[ $subpage_keys[ 0 ] ] . $r['link_after'] . '</a>';
				$text = sprintf( __( 'Back to: %s', 'sgr-nextpage-titles' ), $link );
			}
			
			$output .= apply_filters( 'mpp_link_pages_link', $text, $link );
				
			$output .= $r['after'];
		} elseif ( 'next-previous' == $r['continue_or_prev_next'] ) {
			$output .= $r['before'];
			$prev = $page - 1;
			if ( $prev > 0 ) {
				$link = _mpp_link_page( $prev ) . $r['link_before'] . $r['previouspagelink'] . $r['link_after'] . '</a>';

				/** This filter is documented in wp-includes/post-template.php */
				$output .= apply_filters( 'mpp_link_pages_link', $link, $prev );
			}
			$next = $page + 1;
			if ( $next <= $numpages ) {
				if ( $prev ) {
					$output .= $r['separator'];
				}
				$link = _mpp_link_page( $next ) . $r['link_before'] . $r['nextpagelink'] . $r['link_after'] . '</a>';

				/** This filter is documented in wp-includes/post-template.php */
				$output .= apply_filters( 'mpp_link_pages_link', $link, $next );
			}
			$output .= $r['after'];
		}
	}

	/**
	 * Filters the HTML output of page links for paginated posts.
	 *
	 * @since 1.4
	 *
	 * @param string $output HTML output of paginated posts' page links.
	 * @param array  $args   An array of arguments.
	 */
	$html = apply_filters( 'mpp_link_pages', $output, $args );

	if ( $r['echo'] ) {
		echo $html;
	}
	return $html;
}

/**
 * The formatted output of a list of pages.
 *
 * Displays page links for paginated posts (i.e. includes the <!--nextpage-->.
 * Quicktag one or more times). This tag must be within The Loop.
 *
 * @since 1.4
 *
 * @global int $page
 * @global int $numpages
 * @global int $multipage
 * @global int $more
 *
 * @param string|array $args {
 *     Optional. Array or string of default arguments.
 *
 *	   @type int|bool	  $hide_header		Whether to add the table of contents header or not.
 *	   @type string		  $position			Where to display the table of contents?
 *	   @type int|bool	  $comments			Whether to add the comments link or not.
 *     @type string       $before           HTML or text to prepend.
 *     @type string       $after            HTML or text to append.
 *     @type string       $row_before       HTML or text to prepend to each row.
 *     @type string       $row_after        HTML or text to append to each row.
 *     @type string       $link_before      HTML or text to prepend to each link, inside the `<a>` tag.
 *                                          Also prepended to the current item, which is not linked. Default empty.
 *     @type string       $link_after       HTML or text to append to each Pages link inside the `<a>` tag.
 *                                          Also appended to the current item, which is not linked. Default empty.
 *     @type string       $separator        Text between pagination links. Default is ''.
 *     @type string       $pagelink			Link text for the previous page link, if available. Default is 'Previous Page'.
 *     @type int|bool     $echo             Whether to echo or not. Accepts 1|true or 0|false. Default 1|true.
 * }
 * @return string Formatted output in HTML.
 */
function mpp_toc( $args = '' ) {
	global $page, $numpages, $multipage, $subpages, $more;

	$defaults = array(
		'hide_header'	=> 0,
		'position'		=> 'top_right',
		'comments'		=> 0,
		'before'		=> '<nav><ul>',
		'after'			=> '</ul></nav>',
		'row_before'	=> '<li>',
		'row_after'		=> '</li>',
		'link_before'	=> '',
		'link_after'	=> '',
		'separator'     => '',
		'pagelink'      => '',
		'echo'			=> 0
	);

	$params = wp_parse_args( $args, $defaults );

	/**
	 * Filters the arguments used in retrieving page links for paginated posts.
	 *
	 * @since 1.4
	 *
	 * @param array $params An array of arguments for page links for paginated posts.
	 */
	$r = apply_filters( 'mpp_toc_args', $params );

	$i = 1;
	$output = '';
	if ( $multipage ) {
		$output .= $r['before'];
		foreach( $subpages as $subpage => $title ) {
			// Aggiungere class a row per current e per comments
			$row = $r['row_before'] . str_replace( '%', $i, $r['pagelink'] ) . $r['separator'];
			if ( $i != $page || ! $more && 1 == $page ) {
				$link = _mpp_link_page( $i ) . $title . '</a>';
				$row .= $r['link_before'] . $link . $r['link_after'];
			} else {
				$row .= '<span class="current">' . $title . '</span>';
			}
			$row .= $r['row_after'];

			/**
			 * Filters the HTML output of individual page number links.
			 *
			 * @since 1.4
			 *
			 * @param string $link The page number HTML output.
			 * @param int    $i    Page number for paginated posts' page links.
			 */
			$row = apply_filters( 'mpp_toc_pages_row', $row, $title, $i );

			$output .= $row;
			$i++;
		}
		
		// Add a link for comments.
		if ( $r['comments'] ) {
			$link = $r['comments'] . __( 'Comments' ) . '</a>';
			$row = $r['link_before'] . $link . $r['link_after'];
			$output .= $row;
		}

		$output .= $r['after'];
	}

	$container_class = isset( $r['position'] ) && $r['position'] != '' ? ' ' . $r['position'] : '';
	$template = '
	<div class="mpp-toc-container' . $container_class . '">';

	// Add header.
	if ( ! $r['hide_header'] ) {
		$template .= '<div class="mpp-toc-title"><h2>%1$s</h2></div>';
	}
	
	$template .= '%2$s</div>';
	
	$output = sprintf( $template, __( 'Contents', 'sgr-nextpage-titles' ), $output );
	
	/**
	 * Filters the HTML output of page links for paginated posts.
	 *
	 * @since 1.4
	 *
	 * @param string $output HTML output of paginated posts' page links.
	 * @param array  $args   An array of arguments.
	 */
	$html = apply_filters( 'mpp_toc', $output, $args );

	if ( $r['echo'] ) {
		echo $html;
	}
	return $html;
}

/**
 * Helper function for mpp_link_pages().
 *
 * @since 1.4
 * @access private
 *
 * @global WP_Rewrite $wp_rewrite
 *
 * @param int $i Page number.
 * @param string $p Paragraph id.
 * @return string Link.
 */
function _mpp_link_page( $i, $p = '' ) {
	global $wp_rewrite;
	$post = get_post();
	$query_args = array();

	if ( 1 == $i ) {
		$url = get_permalink();
	} else {
		if ( '' == get_option( 'permalink_structure' ) || in_array( $post->post_status, array( 'draft', 'pending', 'future', 'private' ) ) )
			$url = add_query_arg( 'page', $i, get_permalink() );
		elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )
			$url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
		else
			$url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
	}

	if ( is_preview() ) {

		if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
			$query_args['preview_id'] = wp_unslash( $_GET['preview_id'] );
			$query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] );
		}

		$url = get_preview_post_link( $post, $query_args, $url );
	}
	
	if ( $p )
		$url .= '#' . $p;

	return '<a href="' . esc_url( $url ) . '">';
}