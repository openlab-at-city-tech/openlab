<?php
/**
 * Blog Config File
 * Common Functions for Blog and Single Blog
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Prepare markup for taxonomies.
 *
 * @param string $control_tax Taxonomy subcontrol name.
 * @param int    $loop_count Meta loop counter to decide separator appearance.
 * @param string $separator Separator.
 *
 * @return string $output Taxonomy output.
 */
function astra_get_dynamic_taxonomy( $control_tax, $loop_count, $separator ) {

	$tax_type = astra_get_option( $control_tax );
	$post_id  = get_the_ID();

	if ( ! $post_id ) {
		return '';
	}

	$terms = get_the_terms( $post_id, $tax_type );
	/** @psalm-suppress RedundantCondition */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	if ( $terms && ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		/** @psalm-suppress RedundantCondition */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

		$term_links = array();

		/** @psalm-suppress PossibleRawObjectIteration */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		foreach ( $terms as $term ) {
			/** @psalm-suppress PossibleRawObjectIteration */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			$term_link = get_term_link( $term->slug, $tax_type );

			// If there was an error, continue to the next term.
			if ( is_wp_error( $term_link ) ) {
				continue;
			}

			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$term_links[] = '<a href="' . esc_url( $term_link ) . '">' . esc_html( $term->name ) . '</a>';
			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		}

		$all_terms  = join( ', ', $term_links );
		$output_str = '<span class="ast-terms-link">' . $all_terms . '</span>';

		return ( 1 != $loop_count ) ? ' ' . $separator . ' ' . $output_str : $output_str;
	}

	return '';
}

/**
 * Common Functions for Blog and Single Blog
 *
 * @return  post meta
 */
if ( ! function_exists( 'astra_get_post_meta' ) ) {

	/**
	 * Post meta
	 *
	 * @param  array  $post_meta Post meta.
	 * @param  string $separator Separator.
	 * @return string            post meta markup.
	 */
	function astra_get_post_meta( $post_meta, $separator = '/' ) {

		$output_str = '';
		$loop_count = 1;

		$separator = apply_filters( 'astra_post_meta_separator', $separator );

		foreach ( $post_meta as $meta_value ) {

			switch ( $meta_value ) {

				case 'author':
					$output_str .= ( 1 != $loop_count && '' != $output_str ) ? ' ' . $separator . ' ' : '';
					$output_str .= esc_html( astra_default_strings( 'string-blog-meta-author-by', false ) ) . astra_post_author();
					break;

				case 'date':
					$output_str .= ( 1 != $loop_count && '' != $output_str ) ? ' ' . $separator . ' ' : '';
					$output_str .= astra_post_date();
					break;

				case 'category':
					$category = astra_post_categories();
					if ( '' != $category ) {
						$output_str .= ( 1 != $loop_count && '' != $output_str ) ? ' ' . $separator . ' ' : '';
						$output_str .= $category;
					}
					break;

				case 'tag':
					$tags = astra_post_tags();
					if ( '' != $tags ) {
						$output_str .= ( 1 != $loop_count && '' != $output_str ) ? ' ' . $separator . ' ' : '';
						$output_str .= $tags;
					}
					break;

				case 'comments':
					$comment = astra_post_comments();
					if ( '' != $comment ) {
						$output_str .= ( 1 != $loop_count && '' != $output_str ) ? ' ' . $separator . ' ' : '';
						$output_str .= $comment;
					}
					break;
				default:
					$output_str = apply_filters( 'astra_meta_case_' . $meta_value, $output_str, $loop_count, $separator );

			}

			if ( strpos( $meta_value, '-taxonomy' ) !== false ) {
				$output_str .= astra_get_dynamic_taxonomy( $meta_value, $loop_count, $separator );
			}

			$loop_count ++;
		}

		return $output_str;
	}
}

/**
 * Get post format as per new configurations set in customizer.
 *
 * @return string HTML markup for date span.
 * @since 4.1.0
 */
function astra_get_dynamic_post_format() {
	$post_type          = strval( get_post_type() );
	$is_singular        = is_singular() ? true : false;
	$date_format_option = $is_singular ? astra_get_option( 'ast-dynamic-single-' . esc_attr( $post_type ) . '-date-format', '' ) : astra_get_option( 'blog-meta-date-format', '' );
	$date_type          = $is_singular ? astra_get_option( 'ast-dynamic-single-' . esc_attr( $post_type ) . '-meta-date-type', 'published' ) : astra_get_option( 'blog-meta-date-type', 'published' );
	$date_format        = apply_filters( 'astra_post_date_format', ( '' === $date_format_option ) ? get_option( 'date_format' ) : $date_format_option );

	$published_date = strval( get_the_date( $date_format ) );
	$modified_date  = strval( get_the_modified_date( $date_format ) );

	if ( 'updated' === $date_type ) {
		$class    = 'updated';
		$itemprop = 'dateModified';
		$date     = sprintf(
			esc_html( '%s' ),
			$modified_date
		);
	} else {
		$class    = 'published';
		$itemprop = 'datePublished';
		$date     = sprintf(
			esc_html( '%s' ),
			$published_date
		);
	}

	return sprintf( '<span class="%1$s" itemprop="%2$s"> %3$s </span>', $class, $itemprop, $date );
}

/**
 * Function to get Date of Post
 *
 * @since 1.0.0
 * @return html
 */
if ( ! function_exists( 'astra_post_date' ) ) {

	/**
	 * Function to get Date of Post
	 *
	 * @return html                Markup.
	 */
	function astra_post_date() {
		$output  = '';
		$output .= '<span class="posted-on">';
		$output .= astra_get_dynamic_post_format();
		$output .= '</span>';
		return apply_filters( 'astra_post_date', $output );
	}
}

/**
 * Function to get Author name.
 *
 * @return null|string $author_name Author name.
 * @since 4.0.0
 */
function astra_post_author_name() {
	$author_name    = '';
	$get_the_author = get_the_author();
	if ( empty( $get_the_author ) ) {
		/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		global $post;
		/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( is_object( $post ) && isset( $post->post_author ) ) {
			$user_id = $post->post_author;
			/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			global $authordata;
				/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$author_data = '';
			if ( ! $authordata ) {
				$author_data = get_userdata( $user_id );
			}

			$author_name = esc_attr( ! empty( $author_data ) ? $author_data->display_name : '' );
		}
	} else {
		$author_name = get_the_author();
	}

	return $author_name;
}

/**
 * Function to get Author of Post
 *
 * @since 1.0.0
 * @return html
 */
if ( ! function_exists( 'astra_post_author' ) ) {

	/**
	 * Function to get Author of Post
	 *
	 * @param  string $output_filter Filter string.
	 * @return html                Markup.
	 */
	function astra_post_author( $output_filter = '' ) {

		global $post;
		if ( isset( $post->post_author ) ) {
			$author_id = $post->post_author;
		} elseif ( is_callable( 'get_the_author_meta' ) ) {
			$author_id = get_the_author_meta( 'ID' );
		} else {
			$author_id = 1;
		}

		ob_start();

		echo '<span ';
			echo astra_attr(
				'post-meta-author',
				array(
					'class' => 'posted-by vcard author',
				)
			);
		echo '>';
			// Translators: Author Name. ?>
			<a title="<?php printf( esc_attr__( 'View all posts by %1$s', 'astra' ), get_the_author() ); ?>"
				href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" rel="author"
				<?php
					echo astra_attr(
						'author-url',
						array(
							'class' => 'url fn n',
						)
					);
				?>
				>
				<span
				<?php
					echo astra_attr(
						'author-name',
						array(
							'class' => 'author-name',
						)
					);
				?>
				>
				<?php
					/** @psalm-suppress PossiblyNullArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					echo wp_kses_post( astra_post_author_name() );
				?>
			</span>
			</a>
		</span>

		<?php

		$output = ob_get_clean();

		return apply_filters( 'astra_post_author', $output, $output_filter );
	}
}

/**
 * Function to get Read More Link of Post
 *
 * @since 1.0.0
 * @return html
 */
if ( ! function_exists( 'astra_post_link' ) ) {

	/**
	 * Function to get Read More Link of Post
	 *
	 * @param  string $output_filter Filter string.
	 * @return html                Markup.
	 */
	function astra_post_link( $output_filter = '' ) {

		$enabled = apply_filters( 'astra_post_link_enabled', '__return_true' );
		if ( ( is_admin() && ! wp_doing_ajax() ) || ! $enabled ) {
			return $output_filter;
		}

		$read_more_text    = apply_filters( 'astra_post_read_more', __( 'Read More &raquo;', 'astra' ) );
		$read_more_classes = apply_filters( 'astra_post_read_more_class', array() );

		$post_link = sprintf(
			esc_html( '%s' ),
			'<a class="' . esc_attr( implode( ' ', $read_more_classes ) ) . '" href="' . esc_url( get_permalink() ) . '"> ' . the_title( '<span class="screen-reader-text">', '</span>', false ) . ' ' . $read_more_text . '</a>'
		);

		$output = ' &hellip;<p class="read-more"> ' . $post_link . '</p>';

		return apply_filters( 'astra_post_link', $output, $output_filter );
	}
}
add_filter( 'excerpt_more', 'astra_post_link', 20 );

/**
 * Function to get Number of Comments of Post
 *
 * @since 1.0.0
 * @return html
 */
if ( ! function_exists( 'astra_post_comments' ) ) {

	/**
	 * Function to get Number of Comments of Post
	 *
	 * @param  string $output_filter Output filter.
	 * @return html                Markup.
	 */
	function astra_post_comments( $output_filter = '' ) {

		$output = '';

		ob_start();
		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			?>
			<span class="comments-link">
				<?php
				/**
				 * Get Comment Link
				 *
				 * @see astra_default_strings()
				 */
				comments_popup_link( astra_default_strings( 'string-blog-meta-leave-a-comment', false ), astra_default_strings( 'string-blog-meta-one-comment', false ), astra_default_strings( 'string-blog-meta-multiple-comment', false ) );
				?>
			</span>

			<?php
		}

		$output = ob_get_clean();

		return apply_filters( 'astra_post_comments', $output, $output_filter );
	}
}

/**
 * Function to get Tags applied of Post
 *
 * @since 1.0.0
 * @return html
 */
if ( ! function_exists( 'astra_post_tags' ) ) {

	/**
	 * Function to get Tags applied of Post
	 *
	 * @param  string $output_filter Output filter.
	 * @return html                Markup.
	 */
	function astra_post_tags( $output_filter = '' ) {

		$output = '';

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( ', ', 'astra' ) );

		if ( $tags_list ) {
			$output .= '<span class="tags-links">' . $tags_list . '</span>';
		}

		return apply_filters( 'astra_post_tags', $output, $output_filter );
	}
}

/**
 * Function to get Categories of Post
 *
 * @since 1.0.0
 * @return html
 */
if ( ! function_exists( 'astra_post_categories' ) ) {

	/**
	 * Function to get Categories applied of Post
	 *
	 * @param  string $output_filter Output filter.
	 * @return html                Markup.
	 */
	function astra_post_categories( $output_filter = '' ) {

		$output = '';

		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', 'astra' ) );

		if ( $categories_list ) {
			$output .= '<span class="cat-links">' . $categories_list . '</span>';
		}

		return apply_filters( 'astra_post_categories', $output, $output_filter );
	}
}

/**
 * Display classes for primary div
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'astra_blog_layout_class' ) ) {

	/**
	 * Layout class
	 *
	 * @param  string $class Class.
	 */
	function astra_blog_layout_class( $class = '' ) {

		// Separates classes with a single space, collates classes for body element.
		echo 'class="' . esc_attr( join( ' ', astra_get_blog_layout_class( $class ) ) ) . '"';
	}
}

/**
 * Retrieve the classes for the body element as an array.
 *
 * @since 1.0.0
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
if ( ! function_exists( 'astra_get_blog_layout_class' ) ) {

	/**
	 * Retrieve the classes for the body element as an array.
	 *
	 * @param string $class Class.
	 */
	function astra_get_blog_layout_class( $class = '' ) {

		// array of class names.
		$classes = array();

		$post_format = get_post_format();
		if ( $post_format ) {
			$post_format = 'standard';
		}

		$classes[] = 'ast-post-format-' . $post_format;

		if ( ! has_post_thumbnail() || ! wp_get_attachment_image_src( get_post_thumbnail_id() ) ) {
			switch ( $post_format ) {

				case 'aside':
								$classes[] = 'ast-no-thumb';
					break;

				case 'image':
								$has_image = astra_get_first_image_from_post();
					if ( empty( $has_image ) || is_single() ) {
						$classes[] = 'ast-no-thumb';
					}
					break;

				case 'video':
								$post_featured_data = astra_get_video_from_post( get_the_ID() );
					if ( empty( $post_featured_data ) ) {
						$classes[] = 'ast-no-thumb';
					}
					break;

				case 'quote':
								$classes[] = 'ast-no-thumb';
					break;

				case 'link':
								$classes[] = 'ast-no-thumb';
					break;

				case 'gallery':
								$post_featured_data = get_post_gallery();
					if ( empty( $post_featured_data ) || is_single() ) {
						$classes[] = 'ast-no-thumb';
					}
					break;

				case 'audio':
								$has_audio = astra_get_audios_from_post( get_the_ID() );
					if ( empty( $has_audio ) || is_single() ) {
						$classes[] = 'ast-no-thumb';
					} else {
						$classes[] = 'ast-embeded-audio';
					}
					break;

				case 'standard':
				default:
					if ( ! has_post_thumbnail() || ! wp_get_attachment_image_src( get_post_thumbnail_id() ) ) {
						$classes[] = 'ast-no-thumb';
					}
					break;
			}
		}

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		/**
		 * Filter primary div class names
		 */
		$classes = apply_filters( 'astra_blog_layout_class', $classes, $class );

		$classes = array_map( 'sanitize_html_class', $classes );

		return array_unique( $classes );
	}
}

/**
 * Function to get Content Read More Link of Post
 *
 * @since 1.2.7
 * @return html
 */
if ( ! function_exists( 'astra_the_content_more_link' ) ) {

	/**
	 * Filters the Read More link text.
	 *
	 * @param  string $more_link_element Read More link element.
	 * @param  string $more_link_text Read More text.
	 * @return html                Markup.
	 */
	function astra_the_content_more_link( $more_link_element = '', $more_link_text = '' ) {

		$enabled = apply_filters( 'astra_the_content_more_link_enabled', '__return_true' );
		if ( ( is_admin() && ! wp_doing_ajax() ) || ! $enabled ) {
			return $more_link_element;
		}

		$more_link_text    = apply_filters( 'astra_the_content_more_string', __( 'Read More &raquo;', 'astra' ) );
		$read_more_classes = apply_filters( 'astra_the_content_more_link_class', array() );

		$post_link = sprintf(
			esc_html( '%s' ),
			'<a class="' . esc_attr( implode( ' ', $read_more_classes ) ) . '" href="' . esc_url( get_permalink() ) . '"> ' . the_title( '<span class="screen-reader-text">', '</span>', false ) . $more_link_text . '</a>'
		);

		$more_link_element = ' &hellip;<p class="ast-the-content-more-link"> ' . $post_link . '</p>';

		return apply_filters( 'astra_the_content_more_link', $more_link_element, $more_link_text );
	}
}
add_filter( 'the_content_more_link', 'astra_the_content_more_link', 10, 2 );
