<?php
/**
 * Blog Helper Functions
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Adds custom classes to the array of body classes.
 */
if ( ! function_exists( 'astra_blog_body_classes' ) ) {

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 1.0
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	function astra_blog_body_classes( $classes ) {

		// Adds a class of group-blog to blogs with more than 1 published author.
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}

		return $classes;
	}
}

add_filter( 'body_class', 'astra_blog_body_classes' );

/**
 * Adds custom classes to the array of post grid classes.
 */
if ( ! function_exists( 'astra_post_class_blog_grid' ) ) {

	/**
	 * Adds custom classes to the array of post grid classes.
	 *
	 * @since 1.0
	 * @param array $classes Classes for the post element.
	 * @return array
	 */
	function astra_post_class_blog_grid( $classes ) {

		if ( is_archive() || is_home() || is_search() ) {
			$classes[] = astra_attr( 'ast-blog-col' );
			$classes[] = 'ast-article-post';
		}

		return $classes;
	}
}

add_filter( 'post_class', 'astra_post_class_blog_grid' );

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
if ( ! function_exists( 'astra_blog_get_post_meta' ) ) {

	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @since 1.0
	 * @return mixed            Markup.
	 */
	function astra_blog_get_post_meta() {

		$enable_meta       = apply_filters( 'astra_blog_post_meta_enabled', '__return_true' );
		$post_meta         = astra_get_option( 'blog-meta' );
		$current_post_type = get_post_type();
		$post_type_array   = apply_filters( 'astra_blog_archive_post_type_meta', array( 'post' ) );

		if ( in_array( $current_post_type, $post_type_array ) && is_array( $post_meta ) && $enable_meta ) {

			$output_str = astra_get_post_meta( $post_meta );

			if ( ! empty( $output_str ) ) {
				echo apply_filters( 'astra_blog_post_meta', '<div class="entry-meta">' . $output_str . '</div>', $output_str ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}
}

/**
 * Featured post meta.
 */
if ( ! function_exists( 'astra_blog_post_get_featured_item' ) ) {

	/**
	 * To featured image / gallery / audio / video etc. As per the post format.
	 *
	 * @since 1.0
	 * @return mixed
	 */
	function astra_blog_post_get_featured_item() {

		$post_featured_data = '';
		$post_format        = get_post_format();

		if ( has_post_thumbnail() ) {

			$post_featured_data  = '<a href="' . esc_url( get_permalink() ) . '" >';
			$post_featured_data .= get_the_post_thumbnail();
			$post_featured_data .= '</a>';

		} else {

			switch ( $post_format ) {
				case 'image':
					break;

				case 'video':
					$post_featured_data = astra_get_video_from_post( get_the_ID() );
					break;

				case 'gallery':
					$post_featured_data = get_post_gallery( get_the_ID(), false );
					if ( isset( $post_featured_data['ids'] ) ) {
						$img_ids = explode( ',', $post_featured_data['ids'] );

						$image_alt = get_post_meta( $img_ids[0], '_wp_attachment_image_alt', true );
						$image_url = wp_get_attachment_url( $img_ids[0] );

						if ( isset( $img_ids[0] ) ) {
							$post_featured_data  = '<a href="' . esc_url( get_permalink() ) . '" >';
							$post_featured_data .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $image_alt ) . '" >';
							$post_featured_data .= '</a>';
						}
					}
					break;

				case 'audio':
					$post_featured_data = do_shortcode( astra_get_audios_from_post( get_the_ID() ) );
					break;
			}
		}

		echo $post_featured_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

add_action( 'astra_blog_post_featured_format', 'astra_blog_post_get_featured_item' );


/**
 * Blog Post Thumbnail / Title & Meta Order
 */
if ( ! function_exists( 'astra_blog_post_thumbnail_and_title_order' ) ) {

	/**
	 * Blog post Thubmnail, Title & Blog Meta order
	 *
	 * @since  1.0.8
	 */
	function astra_blog_post_thumbnail_and_title_order() {

		$blog_post_thumb_title_order = astra_get_option( 'blog-post-structure' );
		if ( is_singular() ) {
			return astra_banner_elements_order();
		}

		if ( is_array( $blog_post_thumb_title_order ) ) {
			// Append the custom class for second element for single post.
			foreach ( $blog_post_thumb_title_order as $post_thumb_title_order ) {

				switch ( $post_thumb_title_order ) {

					// Blog Post Featured Image.
					case 'image':
						do_action( 'astra_blog_archive_featured_image_before' );
						astra_get_blog_post_thumbnail( 'archive' );
						do_action( 'astra_blog_archive_featured_image_after' );
						break;

					// Blog Post Title and Blog Post Meta.
					case 'title-meta':
						do_action( 'astra_blog_archive_title_meta_before' );
						astra_get_blog_post_title_meta();
						do_action( 'astra_blog_archive_title_meta_after' );
						break;
				}
			}
		}
	}
}

/**
 * Blog / Single Post Thumbnail
 */
if ( ! function_exists( 'astra_get_blog_post_thumbnail' ) ) {

	/**
	 * Blog post Thumbnail
	 *
	 * @param string $type Type of post.
	 * @since  1.0.8
	 */
	function astra_get_blog_post_thumbnail( $type = 'archive' ) {

		if ( 'archive' === $type ) {
			// Blog Post Featured Image.
			astra_get_post_thumbnail( '<div class="ast-blog-featured-section post-thumb ' . astra_attr( 'ast-grid-blog-col' ) . '">', '</div>' );
		} elseif ( 'single' === $type ) {
			// Single Post Featured Image.
			astra_get_post_thumbnail();
		}
	}
}

/**
 * Blog Post Title & Meta Order
 */
if ( ! function_exists( 'astra_get_blog_post_title_meta' ) ) {

	/**
	 * Blog post Thumbnail
	 *
	 * @since  1.0.8
	 */
	function astra_get_blog_post_title_meta() {

		// Blog Post Title and Blog Post Meta.
		do_action( 'astra_archive_entry_header_before' );
		?>
		<header class="entry-header">
			<?php

				do_action( 'astra_archive_post_title_before' );

				/* translators: 1: Current post link, 2: Current post id */
				astra_the_post_title(
					sprintf(
						'<h2 class="entry-title" %2$s><a href="%1$s" rel="bookmark">',
						esc_url( get_permalink() ),
						astra_attr(
							'article-title-blog',
							array(
								'class' => '',
							)
						)
					),
					'</a></h2>',
					get_the_id()
				);

				do_action( 'astra_archive_post_title_after' );

			?>
			<?php

				do_action( 'astra_archive_post_meta_before' );

				astra_blog_get_post_meta();

				do_action( 'astra_archive_post_meta_after' );

			?>
		</header><!-- .entry-header -->
		<?php

		do_action( 'astra_archive_entry_header_after' );
	}
}

/**
 * Get audio files from post content
 */
if ( ! function_exists( 'astra_get_audios_from_post' ) ) {

	/**
	 * Get audio files from post content
	 *
	 * @param  number $post_id Post id.
	 * @return mixed          Iframe.
	 */
	function astra_get_audios_from_post( $post_id ) {

		// for audio post type - grab.
		$post    = get_post( $post_id );
		$content = do_shortcode( apply_filters( 'the_content', $post->post_content ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$embeds  = apply_filters( 'astra_get_post_audio', get_media_embedded_in_content( $content ) );

		if ( empty( $embeds ) ) {
			return '';
		}

		// check what is the first embed containg video tag, youtube or vimeo.
		foreach ( $embeds as $embed ) {
			if ( strpos( $embed, 'audio' ) ) {
				return '<span class="ast-post-audio-wrapper">' . $embed . '</span>';
			}
		}
	}
}

/**
 * Get first image from post content
 */
if ( ! function_exists( 'astra_get_video_from_post' ) ) {

	/**
	 * Get first image from post content
	 *
	 * @since 1.0
	 * @param  number $post_id Post id.
	 * @return mixed
	 */
	function astra_get_video_from_post( $post_id ) {

		$post    = get_post( $post_id );
		$content = do_shortcode( apply_filters( 'the_content', $post->post_content ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$embeds  = apply_filters( 'astra_get_post_audio', get_media_embedded_in_content( $content ) );

		if ( empty( $embeds ) ) {
			return '';
		}

		// check what is the first embed containg video tag, youtube or vimeo.
		foreach ( $embeds as $embed ) {
			if ( strpos( $embed, 'video' ) || strpos( $embed, 'youtube' ) || strpos( $embed, 'vimeo' ) ) {
				return $embed;
			}
		}
	}
}

/**
 * Get last word of string to get meta-key of custom post structure.
 *
 * @since 4.0.0
 * @param string $string from this get last word.
 * @return string $last_word result.
 */
function astra_get_last_meta_word( $string ) {
	$string    = explode( '-', $string );
	$last_word = array_pop( $string );
	return $last_word;
}

/**
 * Get the current archive description.
 *
 * @since 4.0.0
 * @param string $post_type post type.
 * @return string $description Description for archive.
 */
function astra_get_archive_description( $post_type ) {
	$description = '';

	if ( defined( 'SURECART_PLUGIN_FILE' ) && is_page() && get_the_ID() === absint( get_option( 'surecart_shop_page_id' ) ) ) {
		$description = astra_get_option( 'ast-dynamic-archive-sc_product-custom-description', '' );
		return $description;
	}

	if ( ! is_search() ) {

		$get_archive_description = get_the_archive_description();
		$get_author_meta         = trim( get_the_author_meta( 'description' ) );

		if ( ! empty( $get_archive_description ) ) {
			$description = get_the_archive_description();
		}
		if ( is_author() ) {
			if ( ! empty( $get_author_meta ) ) {
				$description = get_the_author_meta( 'description' );
			}
		}
		if ( empty( $description ) && ! have_posts() ) {
			$description = esc_html( astra_default_strings( 'string-content-nothing-found-message', false ) );
		}
	}
	if ( is_post_type_archive( $post_type ) ) {
		$description = astra_get_option( 'ast-dynamic-archive-' . $post_type . '-custom-description', '' );
	}
	if ( 'post' === $post_type && ( ( is_front_page() && is_home() ) || is_home() ) ) {
		$description = astra_get_option( 'ast-dynamic-archive-post-custom-description', '' );
	}
	return $description;
}

/**
 * Custom single post Title & Meta order display.
 *
 * @since 4.0.0
 * @param array $structure archive or single post structure.
 * @return mixed
 */
function astra_banner_elements_order( $structure = array() ) {

	if ( true === apply_filters( 'astra_remove_entry_header_content', false ) ) {
		return;
	}

	global $post;
	if ( is_null( $post ) ) {
		return;
	}

	// If Blog / Latest Post page is active then looping required structural order.
	if ( ( ! is_front_page() && is_home() ) && false === astra_get_option( 'ast-dynamic-archive-post-banner-on-blog', false ) ) {
		return astra_blog_post_thumbnail_and_title_order();
	}

	$post_type = strval( $post->post_type );

	$prefix      = 'archive';
	$structure   = astra_get_option( 'ast-dynamic-' . $prefix . '-' . $post_type . '-structure', array( 'ast-dynamic-' . $prefix . '-' . $post_type . '-title', 'ast-dynamic-' . $prefix . '-' . $post_type . '-description' ) );
	$layout_type = astra_get_option( 'ast-dynamic-' . $prefix . '-' . $post_type . '-layout', 'layout-1' );

	if ( is_singular() ) {
		$prefix    = 'single';
		$structure = astra_get_option( 'ast-dynamic-' . $prefix . '-' . $post_type . '-structure', array( 'ast-dynamic-' . $prefix . '-' . $post_type . '-title', 'ast-dynamic-' . $prefix . '-' . $post_type . '-meta' ) );
		if ( 'page' === $post_type ) {
			$structure = astra_get_option( 'ast-dynamic-single-page-structure', array( 'ast-dynamic-single-page-image', 'ast-dynamic-single-page-title' ) );
		}
		$layout_type = astra_get_option( 'ast-dynamic-' . $prefix . '-' . $post_type . '-layout', 'layout-1' );
	}

	do_action( 'astra_single_post_banner_before' );
	$post_type = apply_filters( 'astra_banner_elements_post_type', $post_type );
	$prefix    = apply_filters( 'astra_banner_elements_prefix', $prefix );

	foreach ( apply_filters( 'astra_banner_elements_structure', $structure ) as $metaval ) {
		$meta_key = $prefix . '-' . astra_get_last_meta_word( $metaval );
		switch ( $meta_key ) {
			case 'single-breadcrumb':
				do_action( 'astra_single_post_banner_breadcrumb_before' );
				echo astra_get_breadcrumb(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				do_action( 'astra_single_post_banner_breadcrumb_after' );
				break;

			case 'single-title':
				do_action( 'astra_single_post_banner_title_before' );
				if ( 'page' === $post_type ) {
					astra_the_title(
						'<h1 class="entry-title" ' . astra_attr(
							'article-title-content-page',
							array(
								'class' => '',
							)
						) . '>',
						'</h1>'
					);
				} else {
					astra_the_title(
						'<h1 class="entry-title" ' . astra_attr(
							'article-title-blog-single',
							array(
								'class' => '',
							)
						) . '>',
						'</h1>'
					);
				}
				do_action( 'astra_single_post_banner_title_after' );
				break;

			case 'single-excerpt':
				do_action( 'astra_single_post_banner_excerpt_before' );
				echo '<span>' . get_the_excerpt( $post->ID ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				do_action( 'astra_single_post_banner_excerpt_after' );
				break;

			case 'single-meta':
				do_action( 'astra_single_post_banner_meta_before' );
				$post_meta = astra_get_option( 'ast-dynamic-single-' . $post_type . '-metadata', array( 'comments', 'author', 'date' ) );
				$output    = '';
				if ( ! empty( $post_meta ) ) {
					$output_str = astra_get_post_meta( $post_meta );
					if ( ! empty( $output_str ) ) {
						$output = apply_filters( 'astra_single_post_meta', '<div class="entry-meta">' . $output_str . '</div>' ); // WPCS: XSS OK.
					}
				}
				echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				do_action( 'astra_single_post_banner_meta_after' );
				break;

			case 'single-image':
				$featured_background = astra_get_option( 'ast-dynamic-single-' . $post_type . '-featured-as-background', false );

				if ( 'layout-1' === $layout_type ) {
					$article_featured_image_position = astra_get_option( 'ast-dynamic-single-' . $post_type . '-article-featured-image-position-layout-1', 'behind' );
				} else {
					$article_featured_image_position = astra_get_option( 'ast-dynamic-single-' . $post_type . '-article-featured-image-position-layout-2', 'none' );
				}

				if ( 'none' !== $article_featured_image_position ) {
					break;
				}

				if ( ( 'layout-2' === $layout_type && false === $featured_background ) || 'layout-1' === $layout_type ) {
					do_action( 'astra_blog_single_featured_image_before' );
					astra_get_blog_post_thumbnail( 'single' );
					do_action( 'astra_blog_single_featured_image_after' );
				}
				break;

			case 'archive-title':
				do_action( 'astra_blog_archive_title_before' );
				add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
				if ( 'layout-1' === $layout_type ) {
					astra_the_post_title( '<h1 class="page-title ast-archive-title">', '</h1>', 0, true );
				} else {
					astra_the_post_title( '<h1>', '</h1>', 0, true );
				}
				remove_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
				do_action( 'astra_blog_archive_title_after' );
				break;

			case 'archive-breadcrumb':
				if ( ! is_author() ) {
					do_action( 'astra_blog_archive_breadcrumb_before' );
					echo astra_get_breadcrumb(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					do_action( 'astra_blog_archive_breadcrumb_after' );
				}
				break;

			case 'archive-description':
				do_action( 'astra_blog_archive_description_before' );
				echo wp_kses_post( wpautop( astra_get_archive_description( $post_type ) ) );
				do_action( 'astra_blog_archive_description_after' );
				break;
		}
	}

	do_action( 'astra_single_post_banner_after' );
}
