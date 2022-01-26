<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package ePortfolio
 */

if ( ! function_exists( 'eportfolio_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function eportfolio_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( 'Posted on %s', 'post date', 'eportfolio' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( 'eportfolio_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function eportfolio_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'eportfolio' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( 'eportfolio_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function eportfolio_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ' ', 'list item separator', 'eportfolio' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged: %1$s', 'eportfolio' ) . '</span>', $tags_list ); // WPCS: XSS OK.
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'eportfolio' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'eportfolio' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<span class="edit-link">',
			'<i class="fa fa-pencil"></i></span>'
		);
	}
endif;

if ( ! function_exists( 'eportfolio_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function eportfolio_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

		<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			the_post_thumbnail( 'post-thumbnail', array(
				'alt' => the_title_attribute( array(
					'echo' => false,
				) ),
			) );
			?>
		</a>

		<?php
		endif; // End is_singular().
	}
endif;


if (!function_exists('eportfolio_post_date')) :
    function eportfolio_post_date()
    {


        // Hide category and tag text for pages.
        if ('post' === get_post_type()) { ?>

        	    <span class="twp-post-date">
					<i class="fa fa-clock-o"></i>
        	        <?php
        	        $site_date_layout_option = esc_attr(eportfolio_get_option('site_date_layout_option'));
        	        if ($site_date_layout_option == 'normal-format') {
        	          the_time(get_option('date_format'));
        	        } else {
        	          	echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ' . __('ago', 'eportfolio');
        	        }

        	        ?>
        	</span>

            <?php }
    }
endif;



if (!function_exists('eportfolio_post_author')) :

    function eportfolio_post_author()
    {
        global $post;
        if ('post' == get_post_type($post->ID)):
            $author_id = $post->post_author;
            ?>

			<a href="<?php echo esc_url(get_author_posts_url($author_id)) ?>">
				<span class="twp-author-image"><img src="<?php echo esc_url(get_avatar_url($author_id, array('size' => 150))); ?>"></span>
				<span class="twp-author-caption"><?php echo esc_html(get_the_author_meta('display_name', $author_id)); ?></span>
			</a>
        <?php
        endif;

    }
endif;

if (!function_exists('eportfolio_post_categories')) :
    function eportfolio_post_categories($separator = '&nbsp')
    {


        // Hide category and tag text for pages.
        if ('post' === get_post_type()) {

            global $post;
			
            $post_categories = get_the_category($post->ID);
            if ($post_categories) {
                $output = '<ul class="cat-links">';
                foreach ($post_categories as $post_category) {
                    $output .= '<li>
                             <a  href="' . esc_url(get_category_link($post_category)) . '" alt="' . esc_attr(sprintf(__('View all posts in %s', 'eportfolio'), $post_category->name)) . '"> 
                                 ' . esc_html($post_category->name) . '
                             </a>
                        </li>';
                }
                $output .= '</ul>';
                echo wp_kses_post($output);

            }
        }
    }
endif;

/**
 * Returns post format.
 *
 * @since  eportfolio 1.0.0
 */
if (!function_exists('eportfolio_post_format')):
    function eportfolio_post_format($post_id)
    {
        $post_format = get_post_format($post_id);
        switch ($post_format) {
            case "image":
                $post_format = "<span class='twp-post-format-icon'><i class='fa fa-image'></i></span>";
                break;
            case "video":
                $post_format = "<span class='twp-post-format-icon'><i class='fa fa-video-camera'></i></span>";
                break;
            case "gallery":
                $post_format = "<span class='twp-post-format-icon'><i class='fa fa-image'></i></span>";
                break;
            case "quote":
                $post_format = "<span class='twp-post-format-icon'><i class='fa fa-quote-right'></i></span>";
                break; 
           case "audio":
                $post_format = "<span class='twp-post-format-icon'><i class='fa fa-volume-up'></i></span>";
                break;
            default:
                $post_format = "";
        }

        echo wp_kses_post($post_format);
    }

endif;
