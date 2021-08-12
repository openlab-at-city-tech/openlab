<?php
/**
 * Util class for Posts By Tag Plugin
 *
 * @since 3.1
 * @author Sudar
 * @package Posts_By_Tag
 * @subpackage Utils
 */
class Posts_By_Tag_Util {

	/**
	 * Helper function for @link get_posts_by_tag
	 *
	 * @link posts_by_tag for information about parameters
	 *
	 * @see get_posts_by_tag
	 * @see posts_by_tag
	 * @param unknown $giventags (optional)
	 * @param unknown $options   (optional)
	 * @return unknown
	 */
	public static function get_posts_by_tag( $giventags = '', $options = array() ) {
		global $wp_query;
		global $post;

		$current_post_id = $wp_query->post->ID;
		$output          = '';

		$options         = wp_parse_args( $options, array(
				'number'                     => 5,
				'tag_from_post'              => false,
				'tag_from_post_slug'         => false,
				'tag_from_post_custom_field' => false,
				'exclude'                    => false,
				'excerpt'                    => false,
				'thumbnail'                  => false,
				'thumbnail_size'             => 'thumbnail',
				'thumbnail_size_width'       => 100,
				'thumbnail_size_height'      => 100,
				'order_by'                   => 'date',
				'order'                      => 'desc',
				'author'                     => false,
				'date'                       => false,
				'content'                    => false,
				'content_filter'             => true,
				'exclude_current_post'       => false,
				'tag_links'                  => false,
				'link_target'                => '',
			)
		);

		extract( $options, EXTR_OVERWRITE );

		$tag_id_array = self::generate_tag_id_from_name( $giventags, $options );
		// append the tag ids to options
		$options['tag_ids'] = $tag_id_array;

		if ( count( $tag_id_array ) > 0 ) {
			// only if we have atleast one tag. get_posts has a bug. If empty array is passed, it returns all posts. That's why we need this condition
			$tag_arg = 'tag__in';
			if ( $exclude ) {
				$tag_arg = 'tag__not_in';
			}

			// saving the query
			$temp_query = clone $wp_query;
			$temp_post = $post;

			$tag_posts = get_posts( array( 'numberposts' => $number, $tag_arg => $tag_id_array, 'orderby' => $order_by, 'order' => $order ) );

			if ( count( $tag_posts ) > 0 ) {
				$output = '<ul class = "posts-by-tag-list">';
				foreach ( $tag_posts as $tag_post ) {
					if ( $exclude_current_post && $current_post_id == $tag_post->ID ) {
						// exclude currrent post/page
						continue;
					}

					setup_postdata( $tag_post );
					$tag_post_tags_array = wp_get_post_tags( $tag_post->ID );
					$tag_post_tags = array();

					foreach ( $tag_post_tags_array as $tag_post_tag ) {
						array_push( $tag_post_tags, $tag_post_tag->name );
					}

					$permalink = apply_filters( Posts_By_Tag::FILTER_PERMALINK, get_permalink( $tag_post->ID ), $options, $tag_post );
					$onclick = apply_filters( Posts_By_Tag::FILTER_ONCLICK, '', $options, $tag_post );

					if ( $onclick != '' ) {
						$onclick_attr = ' onclick = "' . $onclick . '" ';
					} else {
						$onclick_attr = '';
					}

					$output .= '<li class="posts-by-tag-item ' . implode( ' ', $tag_post_tags ) . '" id="posts-by-tag-item-' . $tag_post->ID . '">';

					if ( $thumbnail ) {
						if ( has_post_thumbnail( $tag_post->ID ) ) {
							if ( $thumbnail_size == 'custom' ) {
								$t_size = array( $thumbnail_size_width, $thumbnail_size_height );
							} else {
								$t_size = $thumbnail_size;
							}
							$output .=  '<a class="thumb" href="' . $permalink . '" title="' . get_the_title( $tag_post->ID ) . '" ' . $onclick_attr . ' >' .
								get_the_post_thumbnail( $tag_post->ID, $t_size ) .
								'</a>';
						} else {
							if ( get_post_meta( $tag_post->ID, 'post_thumbnail', true ) != '' ) {
								$output .=  '<a class="thumb" href="' . $permalink . '" title="' . get_the_title( $tag_post->ID ) . '" ' . $onclick_attr . '>' .
									'<img src="' . esc_url( get_post_meta( $tag_post->ID, 'post_thumbnail', true ) ) . '" alt="' . get_the_title( $tag_post->ID ) . '" >' .
									'</a>';
							}
						}
					}

					// add permalink
					$output .= '<a class = "posts-by-tag-item-title" href="' . $permalink . '"';

					if ( $link_target != '' ) {
						$output .= ' target = "' . $link_target . '"';
					}

					$output .= $onclick_attr;
					$output .= '>' . $tag_post->post_title . '</a>';

					if ( $content ) {
						$more_link_text = '(more...)'; $stripteaser = 0; $more_file = '';
						$post_content = get_the_content( $more_link_text, $stripteaser, $more_file );

						if ( $content_filter ) {
							// apply the content filters
							$post_content = apply_filters( 'the_content', $post_content );
						}

						$post_content = str_replace( ']]>', ']]&gt;', $post_content );

						$output .= $post_content;
					}

					if ( $author ) {
						$output .= ' <small>' . __( 'Posted by: ', 'posts-by-tag' );
						$output .=  get_the_author_meta( 'display_name', $tag_post->post_author ) . '</small>';
					}

					if ( $date ) {
						$output .= ' <small>' . __( 'Posted on: ', 'posts-by-tag' );
						$output .=  mysql2date( get_option( 'date_format' ), $tag_post->post_date ) . '</small>';
					}

					if ( $excerpt ) {
						$output .=  '<br />';
						if ( $tag_post->post_excerpt != NULL )
							if ( $excerpt_filter ) {
								$output .= apply_filters( 'the_excerpt', $tag_post->post_excerpt );
							} else {
							$output .= $tag_post->post_excerpt;
						}
						else
							$output .= get_the_excerpt();
					}
					$output .=  '</li>';
				}

				$output .=  '</ul>';
			}

			// restoring the query so it can be later used to display our posts
			$wp_query = clone $temp_query;
			$post = $temp_post;
			wp_reset_postdata();
		}

		// if there were no posts, then don't return any content
		if ( '<ul class = "posts-by-tag-list"></ul>' == $output ) {
			$output = '';
		}
		return $output;
	}

	/**
	 * Generate Tag id from Tag name
	 *
	 * @since 3.1
	 * @static
	 *
	 * @see posts_by_tag
	 * @param string  $tags    List of comma separated Tag names
	 * @param array   $options Options array. @link posts_by_tag for details about the different options
	 * @return array List of tag ids
	 */
	public static function generate_tag_id_from_name( $tags, $options ) {
		global $post;

		$tag_id_array = array();

		if ( ! ( $options['tag_from_post'] || $options['tag_from_post_slug'] || $options['tag_from_post_custom_field'] ) && '' == $tags ) {
			// if tags is empty and no options are set, then try to get tags from post tags
			$options['tag_from_post'] = true;
		}

		if ( $options['tag_from_post'] || $options['tag_from_post_slug'] || $options['tag_from_post_custom_field'] ) {

			if ( is_singular() && !is_attachment() ) {

				if ( $options['tag_from_post'] ) {
					$tag_array = wp_get_post_tags( $post->ID );
					foreach ( $tag_array as $tag ) {
						$tag_id_array[] = $tag->term_id;
					}
					return $tag_id_array;
				}

				if ( $options['tag_from_post_slug'] ) {
					if ( $post->ID > 0 ) {
						$tags = get_post( $post )->post_name;
					}
				}

				if ( $options['tag_from_post_custom_field'] ) {
					if ( isset( $post->ID ) && $post->ID > 0 ) {
						$post_id = $post->ID;
						Posts_By_Tag::update_postmeta_key( $post_id );
						$posts_by_tag_page_fields = get_post_meta( $post_id, Posts_By_Tag::CUSTOM_POST_FIELD, true );

						if ( isset( $posts_by_tag_page_fields ) && is_array( $posts_by_tag_page_fields ) ) {
							if ( $posts_by_tag_page_fields['widget_tags'] != '' ) {
								$tags = $posts_by_tag_page_fields['widget_tags'];
							}
						}
					}
				}
			}
		}

		if ( '' != $tags ) {
			$tag_array = explode( ",", $tags );

			foreach ( $tag_array as $tag ) {
				$tag_id_array[] = self::get_tag_ID( trim( $tag ) );
			}
		}

		return $tag_id_array;
	}

	/**
	 * Get tag more links for a bunch of tags
	 *
	 * @since 3.1
	 * @static
	 * @access public
	 *
	 * @param string|array $tags   List of tags
	 * @param string  $prefix (optional) Prefix that should be used
	 * @return string $output Tag more links HTML content
	 */
	public static function get_tag_more_links( $tags, $prefix = 'More posts: ' ) {
		global $wp_query;

		$tag_array = array();
		$output = '';

		if ( $tags == '' ) {
			// if tags is empty then take from current posts
			if ( is_single() ) {
				$tag_array = wp_get_post_tags( $wp_query->post->ID );
			}
		} else {
			$tag_array = explode( ",", $tags );
		}

		if ( count( $tag_array ) > 0 ) {
			$output = '<p>' . $prefix;

			foreach ( $tag_array as $tag ) {
				$tag_name = $tag;
				if ( is_object( $tag ) ) {
					$tag_name = $tag->name;
				}

				$output .= self::get_tag_more_link( $tag_name );
			}

			$output .= '</p>';
		}

		return $output;
	}

	/**
	 * Get tag more link for a single tag
	 *
	 * @since 3.1
	 * @static
	 * @access public
	 *
	 * @param unknown $tag
	 * @return string Tag more links HTML content
	 */
	public static function get_tag_more_link( $tag ) {
		return '<a href = "' . get_tag_link( self::get_tag_ID( $tag ) ) . '">' . $tag . '</a> ';
	}

	/**
	 * Get tag id from tag name or slug
	 *
	 * @since 3.1
	 * @static
	 * @access public
	 *
	 * @param string  $tag_name Tag name or slug
	 * @return int Term id. 0 if not found
	 */
	public static function get_tag_ID( $tag_name ) {
		// Try tag name first
		$tag = get_term_by( 'name', $tag_name, 'post_tag' );
		if ( $tag ) {
			return $tag->term_id;
		} else {
			// if Tag name is not found, try tag slug
			$tag = get_term_by( 'slug', $tag_name, 'post_tag' );
			if ( $tag ) {
				return $tag->term_id;
			}
			return 0;
		}
	}

	/**
	 * Helper function to validate boolean options
	 *
	 * @since 3.1
	 * @static
	 *
	 * @param array   $options Options to validate
	 * @param array   $fields  List of boolean fields
	 * @return array Validated Options
	 */
	public static function validate_boolean_options( $options, $fields ) {
		$validated_options = array();

		foreach ( $options as $key => $value ) {
			if ( in_array( $key, $fields ) ) {
				$validated_options[$key] = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
			} else {
				$validated_options[$key] = $value;
			}
		}

		return $validated_options;
	}
}
?>
