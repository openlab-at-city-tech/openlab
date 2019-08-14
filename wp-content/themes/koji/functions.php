<?php

/* ---------------------------------------------------------------------------------------------
   THEME SETUP
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'koji_setup' ) ) :

	function koji_setup() {

		// Automatic feed
		add_theme_support( 'automatic-feed-links' );

		// Custom background color
		add_theme_support( 'custom-background' );

		// Set content-width
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 520;
		}

		// Post thumbnails
		add_theme_support( 'post-thumbnails' );

		// Set post thumbnail size
		set_post_thumbnail_size( 1870, 9999 );

		// Add image sizes
		add_image_size( 'koji_preview_image_low_resolution', 400, 9999, false );
		add_image_size( 'koji_preview_image_high_resolution', 800, 9999, false );

		// Custom logo
		add_theme_support( 'custom-logo', array(
			'height'      => 200,
			'width'       => 600,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'site-title', 'site-description' ),
		) );

		// Title tag
		add_theme_support( 'title-tag' );

		// Add nav menu
		register_nav_menu( 'primary-menu', __( 'Primary Menu', 'koji' ) );
		register_nav_menu( 'mobile-menu', __( 'Mobile Menu', 'koji' ) );
		register_nav_menu( 'social', __( 'Social Menu', 'koji' ) );

		// HTML5 semantic markup
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

		// Make the theme translation ready
		load_theme_textdomain( 'koji', get_template_directory() . '/languages' );

	}
	add_action( 'after_setup_theme', 'koji_setup' );

endif;


/* ---------------------------------------------------------------------------------------------
   ENQUEUE STYLES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_load_style' ) ) :

	function koji_load_style() {
		if ( ! is_admin() ) :

			wp_register_style( 'koji-fontawesome', get_template_directory_uri() . '/assets/font-awesome/css/font-awesome.css' );

			$dependencies = array( 'koji-fontawesome' );

			wp_enqueue_style( 'koji-style', get_template_directory_uri() . '/style.css', $dependencies, wp_get_theme()->get( 'Version' ) );
		endif;
	}
	add_action( 'wp_enqueue_scripts', 'koji_load_style' );

endif;


/* ---------------------------------------------------------------------------------------------
   ADD EDITOR STYLES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_add_editor_styles' ) ) :

	function koji_add_editor_styles() {
		add_editor_style( array(
			'koji-editor-styles.css',
		) );
	}
	add_action( 'init', 'koji_add_editor_styles' );

endif;


/* ---------------------------------------------------------------------------------------------
   ENQUEUE SCRIPTS
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_enqueue_scripts' ) ) :

	function koji_enqueue_scripts() {

		wp_enqueue_script( 'koji_construct', get_template_directory_uri() . '/assets/js/construct.js', array( 'jquery', 'imagesloaded', 'masonry' ), wp_get_theme()->get( 'Version' ), true );

		if ( ( ! is_admin() ) && is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		$ajax_url = admin_url( 'admin-ajax.php' );

		// AJAX Load More
		wp_localize_script( 'koji_construct', 'koji_ajax_load_more', array(
			'ajaxurl'   => esc_url( $ajax_url ),
		) );

	}
	add_action( 'wp_enqueue_scripts', 'koji_enqueue_scripts' );

endif;


/* ---------------------------------------------------------------------------------------------
   POST CLASSES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_post_classes' ) ) :

	function koji_post_classes( $classes ) {

		global $post;

		// Class indicating presence/lack of post thumbnail
		$classes[] = ( has_post_thumbnail() ? 'has-thumbnail' : 'missing-thumbnail' );

		return $classes;
	}
	add_action( 'post_class', 'koji_post_classes' );

endif;


/* ---------------------------------------------------------------------------------------------
   BODY CLASSES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_body_classes' ) ) :

	function koji_body_classes( $classes ) {

		global $post;

		// Determine type of infinite scroll
		$pagination_type = get_theme_mod( 'koji_pagination_type' ) ? get_theme_mod( 'koji_pagination_type' ) : 'button';
		switch ( $pagination_type ) {
			case 'button' :
				$classes[] = 'pagination-type-button';
				break;
			case 'scroll' :
				$classes[] = 'pagination-type-scroll';
				break;
			case 'links' :
				$classes[] = 'pagination-type-links';
				break;
		}

		// Check for post thumbnail
		if ( is_singular() && has_post_thumbnail() ) {
			$classes[] = 'has-post-thumbnail';
		} elseif ( is_singular() ) {
			$classes[] = 'missing-post-thumbnail';
		}

		// Check whether we're in the customizer preview
		if ( is_customize_preview() ) {
			$classes[] = 'customizer-preview';
		}

		// Slim page template class names (class = name - file suffix)
		if ( is_page_template() ) {
			$classes[] = preg_replace( '/\\.[^.\\s]{3,4}$/', '', get_page_template_slug( $post->ID ) );
		}

		return $classes;

	}
	add_action( 'body_class', 'koji_body_classes' );

endif;


/* ---------------------------------------------------------------------------------------------
   ADD HTML CLASS IF THERE'S JAVASCRIPT
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_has_js' ) ) :

	function koji_has_js() {
		?>
		<script>document.documentElement.className = document.documentElement.className.replace( 'no-js', 'js' );</script>
		<?php
	}
	add_action( 'wp_head', 'koji_has_js' );

endif;


/* ---------------------------------------------------------------------------------------------
   CUSTOM LOGO OUTPUT
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_custom_logo' ) ) :

	function koji_custom_logo() {

		$logo_id = get_theme_mod( 'custom_logo' );

		// Get the logo
		$logo = wp_get_attachment_image_src( $logo_id, 'full' );

		if ( $logo ) {

			// For clarity
			$logo_url = esc_url( $logo[0] );
			$logo_width = esc_attr( $logo[1] );
			$logo_height = esc_attr( $logo[2] );

			// If the retina logo setting is active, reduce the width/height by half
			if ( get_theme_mod( 'koji_retina_logo' ) ) {
				$logo_width = floor( $logo_width / 2 );
				$logo_height = floor( $logo_height / 2 );
			}

			// Get the alt text
			$logo_alt_text = get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) ? get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) : get_bloginfo( 'site-title' );

			?>

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>" class="custom-logo-link">
				<img src="<?php echo esc_url( $logo_url ); ?>" width="<?php echo esc_attr( $logo_width ); ?>" height="<?php echo esc_attr( $logo_height ); ?>" alt="<?php echo esc_attr( $logo_alt_text ); ?>" />
			</a>

			<?php
		}

	}

endif;


/* ---------------------------------------------------------------------------------------------
   REGISTER WIDGET AREAS
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_widget_areas' ) ) :

	function koji_widget_areas() {

		register_sidebar( array(
			'name' 			=> __( 'Sidebar', 'koji' ),
			'id' 			=> 'sidebar',
			'description' 	=> __( 'Widgets in this area will be shown below the main menu.', 'koji' ),
			'before_title' 	=> '<h2 class="widget-title">',
			'after_title' 	=> '</h2>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget' 	=> '</div><div class="clear"></div></div>',
		) );

		register_sidebar( array(
			'name' 			=> __( 'Footer #1', 'koji' ),
			'id' 			=> 'footer-one',
			'description' 	=> __( 'Widgets in this area will be shown in the first footer column.', 'koji' ),
			'before_title' 	=> '<h2 class="widget-title">',
			'after_title' 	=> '</h2>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget' 	=> '</div><div class="clear"></div></div>',
		) );

		register_sidebar( array(
			'name' 			=> __( 'Footer #2', 'koji' ),
			'id' 			=> 'footer-two',
			'description' 	=> __( 'Widgets in this area will be shown in the second footer column.', 'koji' ),
			'before_title' 	=> '<h2 class="widget-title">',
			'after_title' 	=> '</h2>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget' 	=> '</div><div class="clear"></div></div>',
		) );

		register_sidebar( array(
			'name' 			=> __( 'Footer #3', 'koji' ),
			'id' 			=> 'footer-three',
			'description' 	=> __( 'Widgets in this area will be shown in the third footer column.', 'koji' ),
			'before_title' 	=> '<h2 class="widget-title">',
			'after_title' 	=> '</h2>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget' 	=> '</div><div class="clear"></div></div>',
		) );

	}
	add_action( 'widgets_init', 'koji_widget_areas' );

endif;


/* ---------------------------------------------------------------------------------------------
   REMOVE ARCHIVE PREFIXES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_remove_archive_title_prefix' ) ) :

	function koji_remove_archive_title_prefix( $title ) {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif ( is_year() ) {
			$title = get_the_date( 'Y' );
		} elseif ( is_month() ) {
			$title = get_the_date( 'F Y' );
		} elseif ( is_day() ) {
			$title = get_the_date( get_option( 'date_format' ) );
		} elseif ( is_tax( 'post_format' ) ) {
			if ( is_tax( 'post_format', 'post-format-aside' ) ) {
				$title = _x( 'Asides', 'post format archive title', 'koji' );
			} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
				$title = _x( 'Galleries', 'post format archive title', 'koji' );
			} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
				$title = _x( 'Images', 'post format archive title', 'koji' );
			} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
				$title = _x( 'Videos', 'post format archive title', 'koji' );
			} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
				$title = _x( 'Quotes', 'post format archive title', 'koji' );
			} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
				$title = _x( 'Links', 'post format archive title', 'koji' );
			} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
				$title = _x( 'Statuses', 'post format archive title', 'koji' );
			} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
				$title = _x( 'Audio', 'post format archive title', 'koji' );
			} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
				$title = _x( 'Chats', 'post format archive title', 'koji' );
			}
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		} elseif ( is_search() ) {
			$title = '&ldquo;' . get_search_query() . '&rdquo;';
		} else {
			$title = __( 'Archives', 'koji' );
		} // End if().
		return $title;
	}
	add_filter( 'get_the_archive_title', 'koji_remove_archive_title_prefix' );

endif;


/* ---------------------------------------------------------------------------------------------
   GET ARCHIVE PREFIX
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_get_archive_title_prefix' ) ) :

	function koji_get_archive_title_prefix() {
		if ( is_category() ) {
			$title_prefix = __( 'Category', 'koji' );
		} elseif ( is_tag() ) {
			$title_prefix = __( 'Tag', 'koji' );
		} elseif ( is_author() ) {
			$title_prefix = __( 'Author', 'koji' );
		} elseif ( is_year() ) {
			$title_prefix = __( 'Year', 'koji' );
		} elseif ( is_month() ) {
			$title_prefix = __( 'Month', 'koji' );
		} elseif ( is_day() ) {
			$title_prefix = __( 'Day', 'koji' );
		} elseif ( is_tax() ) {
			$tax = get_taxonomy( get_queried_object()->taxonomy );
			$title_prefix = $tax->labels->singular_name;
		} elseif ( is_search() ) {
			$title_prefix = __( 'Search Results', 'koji' );
		} else {
			$title_prefix = __( 'Archives', 'koji' );
		}
		return $title_prefix;
	}

endif;


/* ---------------------------------------------------------------------------------------------
   GET FALLBACK IMAGE
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_get_fallback_image_url' ) ) :

	function koji_get_fallback_image_url() {

		$disable_fallback_image = get_theme_mod( 'koji_disable_fallback_image' );

		if ( $disable_fallback_image ) {
			return '';
		}

		$fallback_image_id = get_theme_mod( 'koji_fallback_image' );

		if ( $fallback_image_id ) {
			$fallback_image = wp_get_attachment_image_src( $fallback_image_id, 'full' );
		}

		$fallback_image_url = isset( $fallback_image ) ? esc_url( $fallback_image[0] ) : get_template_directory_uri() . '/assets/images/default-fallback-image.png';

		return $fallback_image_url;

	}

endif;


/* ---------------------------------------------------------------------------------------------
   OUTPUT FALLBACK IMAGE
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'koji_the_fallback_image' ) ) :

	function koji_the_fallback_image() {

		$fallback_image_url = koji_get_fallback_image_url();

		if ( ! $fallback_image_url ) {
			return;
		}

		echo '<img class="fallback-image" src="' . $fallback_image_url . '" alt="' . __( 'Fallback image', 'koji' ) . '" />';

	}

endif;


/* ---------------------------------------------------------------------------------------------
   GET THE IMAGE SIZE OF PREVIEWS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'koji_get_preview_image_size' ) ) :

	function koji_get_preview_image_size() {

		// Check if low-resolution images are activated in the customizer
		$low_res_images = get_theme_mod( 'koji_activate_low_resolution_images' );

		// If they are, we're using the low resolution image size
		if ( $low_res_images ) {
			return 'koji_preview_image_low_resolution';

		// If not, we're using the high resolution image size
		} else {
			return 'koji_preview_image_high_resolution';
		}

	}

endif;


/* ---------------------------------------------------------------------------------------------
   OUTPUT POST META
   If it's a single post, output the post meta values specified in the Customizer settings.

   @param	$post_id int		The ID of the post for which the post meta should be output
   @param	$location string	Which post meta location to output – single or preview
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_the_post_meta' ) ) :

	function koji_the_post_meta( $post_id = null, $location = 'single' ) {

		echo koji_get_post_meta( $post_id, $location );

	}

endif;


/* ---------------------------------------------------------------------------------------------
   GET THE POST META
   If the provided ID is for a single post, return the post meta values specified in the Customizer settings.

   @param	$post_id int		The ID of the post for which the post meta should be output
   @param	$location string	Which post meta location to output – single or preview
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_get_post_meta' ) ) :

	function koji_get_post_meta( $post_id = null, $location = 'single' ) {

		// Require post ID
		if ( ! $post_id ) {
			return;
		}

		// Check that the post type should be able to output post meta
		$allowed_post_types = apply_filters( 'koji_allowed_post_types_for_meta_output', array( 'post' ) );
		if ( ! in_array( get_post_type( $post_id ), $allowed_post_types ) ) {
			return;
		}

		$post_meta_wrapper_classes = '';
		$post_meta_classes = '';

		// Get the post meta settings for the location specified
		if ( 'preview' === $location ) {
			$post_meta = get_theme_mod( 'koji_post_meta_preview' );

			$post_meta_wrapper_classes = ' post-meta-preview';

			// Empty = use default
			if ( ! $post_meta ) {
				$post_meta = array(
					'post-date',
					'comments',
				);
			}
		} else {
			$post_meta = get_theme_mod( 'koji_post_meta_single' );

			$post_meta_wrapper_classes = ' post-meta-single';
			$post_meta_classes = ' stack-mobile';

			// Empty = use default
			if ( ! $post_meta ) {
				$post_meta = array(
					'post-date',
					'categories',
				);
			}
		}

		// If the post meta setting has the value 'empty', it's explicitly empty and the default post meta shouldn't be output
		if ( $post_meta && ! in_array( 'empty', $post_meta ) ) :

			ob_start();

			setup_postdata( $post_id );

			?>

			<div class="post-meta-wrapper<?php echo $post_meta_wrapper_classes; ?>">

				<ul class="post-meta<?php echo $post_meta_classes; ?>">

					<?php

					// Post date
					if ( in_array( 'post-date', $post_meta ) ) : ?>
						<li class="post-date">
							<a class="meta-wrapper" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
								<span class="screen-reader-text"><?php _e( 'Post date', 'koji' ); ?></span>
								<div class="meta-icon"><img aria-hidden="true" src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/calendar.svg" /></div>
								<span class="meta-content"><?php the_time( get_option( 'date_format' ) ); ?></span>
							</a>
						</li>
					<?php endif;

					// Author
					if ( in_array( 'author', $post_meta ) ) : ?>
						<li class="post-author">
							<span class="screen-reader-text"><?php _e( 'Posted by', 'koji' ); ?></span>
							<a class="meta-wrapper" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
								<div class="meta-icon"><img aria-hidden="true" src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/user.svg" /></div>
								<span class="meta-content"><?php the_author_meta( 'nickname' ); ?></span>
							</a>
						</li>
						<?php
					endif;

					// Categories
					if ( in_array( 'categories', $post_meta ) ) : ?>
						<li class="post-categories meta-wrapper">
							<div class="meta-icon"><img aria-hidden="true" src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/folder.svg" /></div>
							<span class="screen-reader-text"><?php _e( 'Posted in', 'koji' ); ?></span>
							<span class="meta-content"><?php the_category( ', ' ); ?></span>
						</li>
						<?php
					endif;

					// Tags
					if ( in_array( 'tags', $post_meta ) && has_tag() ) : ?>
						<li class="post-tags meta-wrapper">
							<div class="meta-icon"><img aria-hidden="true" src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/tag.svg" /></div>
							<span class="screen-reader-text"><?php _e( 'Tagged with', 'koji' ); ?></span>
							<span class="meta-content"><?php the_tags( '', ', ', '' ); ?></span>
						</li>
						<?php
					endif;

					// Comments link
					if ( in_array( 'comments', $post_meta ) && comments_open() ) : ?>
						<li class="post-comment-link">
							<a class="meta-wrapper" href="<?php echo esc_url( get_comments_link( $post_id ) ); ?>">
								<span class="screen-reader-text"><?php _e( 'Comments', 'koji' ); ?></span>
								<div class="meta-icon"><img aria-hidden="true" src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/comment.svg" /></div>
								<span class="meta-content"><?php echo get_comments_number(); ?></span>
							</a>
						</li>
						<?php
					endif;

					// Sticky
					if ( in_array( 'sticky', $post_meta ) && is_sticky() ) : ?>
						<li class="post-sticky meta-wrapper">
							<div class="meta-icon"><img aria-hidden="true" src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/bookmark.svg" /></div>
							<span class="meta-content"><?php _e( 'Sticky post', 'koji' ); ?></span>
						</li>
					<?php endif;

					// Edit link
					if ( in_array( 'edit-link', $post_meta ) && current_user_can( 'edit_post', get_the_ID() ) ) : ?>
						<li class="edit-post">
							
							<?php
							// Make sure we display something in the customizer, as edit_post_link() doesn't output anything there
							if ( is_customize_preview() ) { ?>
								<div class="meta-wrapper">
									<div class="meta-icon"><img aria-hidden="true" src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/edit.svg" /></div>
									<span class="meta-content"><?php _e( 'Edit', 'koji' ); ?></span>
								</div>
								<?php
							} else {
								echo '<a href="' . esc_url( get_edit_post_link() ) . '" class="meta-wrapper"><div class="meta-icon">';
								echo '<img aria-hidden="true" src="' . get_template_directory_uri() . '/assets/images/icons/edit.svg' . '" />';
								echo '</div>';
								echo '<span class="meta-content">' . __( 'Edit', 'koji' ) . '</span>';
								echo '</a>';
							}
							?>

						</li>
					<?php endif; ?>

				</ul><!-- .post-meta -->

			</div><!-- .post-meta-wrapper -->

			<?php

			// Get the contents of the buffer
			$post_meta_contents = ob_get_clean();

			wp_reset_postdata();

			// And return them
			return $post_meta_contents;

		endif;

		// If we've reached this point, there's nothing to return, so let's return nothing
		return;

	}

endif;


/* ---------------------------------------------------------------------------------------------
   	CUSTOM CUSTOMIZER CONTROLS
   --------------------------------------------------------------------------------------------- */


if ( class_exists( 'WP_Customize_Control' ) ) :

	if ( ! class_exists( 'Koji_Customize_Control_Checkbox_Multiple' ) ) :

		// Custom Customizer control that outputs a specified number of checkboxes
		// Based on a solution by Justin Tadlock: http://justintadlock.com/archives/2015/05/26/multiple-checkbox-customizer-control
		class Koji_Customize_Control_Checkbox_Multiple extends WP_Customize_Control {

			public $type = 'checkbox-multiple';

			public function render_content() {

				if ( empty( $this->choices ) ) :
					return;
				endif;

				if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;

				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif;

				$multi_values = ! is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value(); ?>

				<ul>
					<?php foreach ( $this->choices as $value => $label ) : ?>

						<li>
							<label>
								<input type="checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $multi_values ) ); ?> />
								<?php echo esc_html( $label ); ?>
							</label>
						</li>

					<?php endforeach; ?>
				</ul>

				<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $multi_values ) ); ?>" />
				<?php
			}
		}

	endif;

endif;


/* ---------------------------------------------------------------------------------------------
   FILTER COMMENT TEXT TO OUTPUT "BY POST AUTHOR" TEXT
------------------------------------------------------------------------------------------------ */


if ( ! function_exists( 'koji_loading_indicator' ) ) :

	function koji_filter_comment_text( $comment_text, $comment, $args ) {

		$comment_author_user_id = $comment->user_id;
		$post_author_user_id = get_post_field( 'post_author', $comment->comment_post_ID );

		if ( $comment_author_user_id === $post_author_user_id ) {
			$comment_text .= '<div class="by-post-author-wrapper">' . __( 'Post author', 'koji' ) . '</div>';
		}

		return $comment_text;

	}
	add_filter( 'comment_text', 'koji_filter_comment_text', 10, 3 );

endif;


/* ---------------------------------------------------------------------------------------------
   OUTPUT LOADING INDICATOR
------------------------------------------------------------------------------------------------ */


if ( ! function_exists( 'koji_loading_indicator' ) ) :

	function koji_loading_indicator() {

		echo '<div class="loader"></div>';

	}

endif;


/* ---------------------------------------------------------------------------------------------
	AJAX LOAD MORE
	Called in construct.js when the user has clicked the load more button
--------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_ajax_load_more' ) ) :

	function koji_ajax_load_more() {

		$query_args = json_decode( wp_unslash( $_POST['json_data'] ), true );

		$ajax_query = new WP_Query( $query_args );

		// Determine which preview to use based on the post_type
		$post_type = $ajax_query->get( 'post_type' );

		// Default to the "post" post type for previews
		if ( is_array( $post_type ) ) {
			$post_type = 'post';
		}

		if ( $ajax_query->have_posts() ) :

			while ( $ajax_query->have_posts() ) : $ajax_query->the_post();

				get_template_part( 'preview', $post_type );

			endwhile;

		endif;

		die();
	}
	add_action( 'wp_ajax_nopriv_koji_ajax_load_more', 'koji_ajax_load_more' );
	add_action( 'wp_ajax_koji_ajax_load_more', 'koji_ajax_load_more' );

endif;


/* ---------------------------------------------------------------------------------------------
   CUSTOMIZER SETTINGS
   --------------------------------------------------------------------------------------------- */


if ( ! class_exists( 'Koji_Customize' ) ) :

	class Koji_Customize {

		public static function koji_register( $wp_customize ) {

			/* 2X Header Logo ----------------------------- */

			$wp_customize->add_setting( 'koji_retina_logo', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'koji_sanitize_checkbox',
				'transport'			=> 'postMessage',
			) );

			$wp_customize->add_control( 'koji_retina_logo', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'title_tagline',
				'priority'		=> 10,
				'label' 		=> __( 'Retina logo', 'koji' ),
				'description' 	=> __( 'Scales the logo to half its uploaded size, making it sharp on high-res screens.', 'koji' ),
			) );

			/* ------------------------------------
			 * Fallback Image Options
			 * ------------------------------------ */

			$wp_customize->add_section( 'koji_image_options', array(
				'title' 		=> __( 'Images', 'koji' ),
				'priority' 		=> 40,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Settings for images in Koji.', 'koji' ),
			) );

			// Activate low-resolution images setting
			$wp_customize->add_setting( 'koji_activate_low_resolution_images', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'koji_sanitize_checkbox'
			) );

			$wp_customize->add_control( 'koji_activate_low_resolution_images', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'koji_image_options',
				'priority'		=> 5,
				'label' 		=> __( 'Use Low-Resolution Images', 'koji' ),
				'description'	=> __( 'Checking this will decrease load times, but also make images look less sharp on high-resolution screens.', 'koji' ),
			) );

			// Fallback image setting
			$wp_customize->add_setting( 'koji_fallback_image', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'absint'
			) );

			$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'koji_fallback_image', array(
				'label'			=> __( 'Fallback Image', 'koji' ),
				'description'	=> __( 'The selected image will be used when a post is missing a featured image. A default fallback image included in the theme will be used if no image is set.', 'koji' ),
				'priority'		=> 10,
				'mime_type'		=> 'image',
				'section' 		=> 'koji_image_options',
			) ) );

			// Disable fallback image setting
			$wp_customize->add_setting( 'koji_disable_fallback_image', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'koji_sanitize_checkbox'
			) );

			$wp_customize->add_control( 'koji_disable_fallback_image', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'koji_image_options',
				'priority'		=> 15,
				'label' 		=> __( 'Disable Fallback Image', 'koji' )
			) );

			/* ------------------------------------
			 * Post Meta Options
			 * ------------------------------------ */

			$wp_customize->add_section( 'koji_post_meta_options', array(
				'title' 		=> __( 'Post Meta', 'koji' ),
				'priority' 		=> 41,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Choose which meta information to display in Koji.', 'koji' ),
			) );

			/* Post Meta Setting ----------------------------- */

			$post_meta_choices = apply_filters( 'koji_post_meta_choices_in_the_customizer', array(
				'author'		=> __( 'Author', 'koji' ),
				'categories'	=> __( 'Categories', 'koji' ),
				'comments'		=> __( 'Comments', 'koji' ),
				'edit-link'		=> __( 'Edit link (for logged in users)', 'koji' ),
				'post-date'		=> __( 'Post date', 'koji' ),
				'sticky'		=> __( 'Sticky status', 'koji' ),
				'tags'			=> __( 'Tags', 'koji' ),
			) );

			// Post Meta Single Setting
			$wp_customize->add_setting( 'koji_post_meta_single', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => array( 'post-date', 'categories' ),
				'sanitize_callback' => 'koji_sanitize_multiple_checkboxes',
			) );

			$wp_customize->add_control( new Koji_Customize_Control_Checkbox_Multiple( $wp_customize, 'koji_post_meta_single', array(
				'section' 		=> 'koji_post_meta_options',
				'label'   		=> __( 'Post Meta On Single:', 'koji' ),
				'description'	=> __( 'Select the post meta values to show on single posts.', 'koji' ),
				'choices' 		=> $post_meta_choices,
			) ) );

			// Post Meta Preview Setting
			$wp_customize->add_setting( 'koji_post_meta_preview', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => array( 'post-date', 'comments' ),
				'sanitize_callback' => 'koji_sanitize_multiple_checkboxes',
			) );

			$wp_customize->add_control( new Koji_Customize_Control_Checkbox_Multiple( $wp_customize, 'koji_post_meta_preview', array(
				'section' 		=> 'koji_post_meta_options',
				'label'   		=> __( 'Post Meta In Previews:', 'koji' ),
				'description'	=> __( 'Select the post meta values to show in previews.', 'koji' ),
				'choices' 		=> $post_meta_choices,
			) ) );

			/* ------------------------------------
			 * Pagination Options
			 * ------------------------------------ */

			$wp_customize->add_section( 'koji_pagination_options', array(
				'title' 		=> __( 'Pagination', 'koji' ),
				'priority' 		=> 45,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Choose which type of pagination to display.', 'koji' ),
			) );

			/* Pagination Type Setting ----------------------------- */

			$wp_customize->add_setting( 'koji_pagination_type', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => 'button',
				'sanitize_callback' => 'koji_sanitize_radio',
			) );

			$wp_customize->add_control( 'koji_pagination_type', array(
				'type'			=> 'radio',
				'section' 		=> 'koji_pagination_options',
				'label'   		=> __( 'Pagination Type:', 'koji' ),
				'choices' 		=> array(
					'button'		=> __( 'Load more on button click', 'koji' ),
					'scroll'		=> __( 'Load more on scroll', 'koji' ),
					'links'			=> __( 'Previous and next page links', 'koji' ),
				),
			) );

			/* ------------------------------------
			 * Search Options
			 * ------------------------------------ */

			$wp_customize->add_section( 'koji_search_options', array(
				'title' 		=> __( 'Search', 'koji' ),
				'priority' 		=> 50,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> '',
			) );

			/* Disable Search Setting ----------------------------- */

			$wp_customize->add_setting( 'koji_disable_search', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'koji_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'koji_disable_search', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'koji_search_options',
				'priority'		=> 10,
				'label' 		=> __( 'Disable Search Toggle', 'koji' ),
				'description' 	=> __( 'Check to remove the search toggle from the row of icons.', 'koji' ),
			) );

			/* ------------------------------------
			 * Related Posts Options
			 * ------------------------------------ */

			$wp_customize->add_section( 'koji_related_posts_options', array(
				'title' 		=> __( 'Related Posts', 'koji' ),
				'priority' 		=> 60,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> '',
			) );

			/* Disable Related Posts Setting ----------------------------- */

			$wp_customize->add_setting( 'koji_disable_related_posts', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'koji_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'koji_disable_related_posts', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'koji_related_posts_options',
				'priority'		=> 10,
				'label' 		=> __( 'Disable Related Posts', 'koji' ),
				'description' 	=> __( 'Check to hide the related posts section on single posts.', 'koji' ),
			) );

			/* Sanitation functions ----------------------------- */

			// Sanitize boolean for checkbox
			function koji_sanitize_checkbox( $checked ) {
				return ( ( isset( $checked ) && true == $checked ) ? true : false );
			}

			// Sanitize booleans for multiple checkboxes
			function koji_sanitize_multiple_checkboxes( $values ) {
				$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;
				return ! empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : array();
			}

			function koji_sanitize_radio( $input, $setting ) {
				$input = sanitize_key( $input );
				$choices = $setting->manager->get_control( $setting->id )->choices;
				return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
			}

		}

		// Initiate the customize controls js
		public static function koji_customize_controls() {
			wp_enqueue_script( 'koji-customize-controls', get_template_directory_uri() . '/assets/js/customize-controls.js', array( 'jquery', 'customize-controls' ), '', true );
		}

	}

	// Setup the Theme Customizer settings and controls
	add_action( 'customize_register', array( 'Koji_Customize', 'koji_register' ) );

	// Enqueue customize controls javascript in Theme Customizer admin screen
	add_action( 'customize_controls_init', array( 'Koji_Customize', 'koji_customize_controls' ) );

endif;


/* ---------------------------------------------------------------------------------------------
   SPECIFY GUTENBERG SUPPORT
------------------------------------------------------------------------------------------------ */


if ( ! function_exists( 'koji_add_gutenberg_features' ) ) :

	function koji_add_gutenberg_features() {

		/* Gutenberg Feature Opt-Ins --------------------------------------- */

		add_theme_support( 'align-wide' );

		/* Gutenberg Palette --------------------------------------- */

		add_theme_support( 'editor-color-palette', array(
			array(
				'name' 	=> _x( 'Black', 'Name of the black color in the Gutenberg palette', 'koji' ),
				'slug' 	=> 'black',
				'color' => '#232D37',
			),
			array(
				'name' 	=> _x( 'Darkest gray', 'Name of the darkest gray color in the Gutenberg palette', 'koji' ),
				'slug' 	=> 'darkest-gray',
				'color' => '#4B555F',
			),
			array(
				'name' 	=> _x( 'Darker Gray', 'Name of the darker gray color in the Gutenberg palette', 'koji' ),
				'slug' 	=> 'darker-gray',
				'color' => '#69737D',
			),
			array(
				'name' 	=> _x( 'Gray', 'Name of the gray color in the Gutenberg palette', 'koji' ),
				'slug' 	=> 'gray',
				'color' => '#9BA5AF',
			),
			array(
				'name' 	=> _x( 'Light gray', 'Name of the light gray color in the Gutenberg palette', 'koji' ),
				'slug' 	=> 'light-gray',
				'color' => '#DCDFE2',
			),
			array(
				'name' 	=> _x( 'Lightest gray', 'Name of the lightest gray color in the Gutenberg palette', 'koji' ),
				'slug' 	=> 'lightest-gray',
				'color' => '#E6E9EC',
			),
			array(
				'name' 	=> _x( 'White', 'Name of the white color in the Gutenberg palette', 'koji' ),
				'slug' 	=> 'white',
				'color' => '#FFF',
			),
		) );

		/* Gutenberg Font Sizes --------------------------------------- */

		add_theme_support( 'editor-font-sizes', array(
			array(
				'name' 		=> _x( 'Small', 'Name of the small font size in Gutenberg', 'koji' ),
				'shortName' => _x( 'S', 'Short name of the small font size in the Gutenberg editor.', 'koji' ),
				'size' 		=> 16,
				'slug' 		=> 'small',
			),
			array(
				'name' 		=> _x( 'Regular', 'Name of the regular font size in Gutenberg', 'koji' ),
				'shortName' => _x( 'M', 'Short name of the regular font size in the Gutenberg editor.', 'koji' ),
				'size' 		=> 19,
				'slug' 		=> 'regular',
			),
			array(
				'name' 		=> _x( 'Large', 'Name of the large font size in Gutenberg', 'koji' ),
				'shortName' => _x( 'L', 'Short name of the large font size in the Gutenberg editor.', 'koji' ),
				'size' 		=> 24,
				'slug' 		=> 'large',
			),
			array(
				'name' 		=> _x( 'Larger', 'Name of the larger font size in Gutenberg', 'koji' ),
				'shortName' => _x( 'XL', 'Short name of the larger font size in the Gutenberg editor.', 'koji' ),
				'size' 		=> 32,
				'slug' 		=> 'larger',
			),
		) );

	}
	add_action( 'after_setup_theme', 'koji_add_gutenberg_features' );

endif;


/* ---------------------------------------------------------------------------------------------
   GUTENBERG EDITOR STYLES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'koji_block_editor_styles' ) ) :

	function koji_block_editor_styles() {
		wp_enqueue_style( 'koji-block-editor-styles', get_template_directory_uri() . '/koji-gutenberg-editor-style.css', array(), '1.0', 'all' );
	}
	add_action( 'enqueue_block_editor_assets', 'koji_block_editor_styles', 1 );

endif;