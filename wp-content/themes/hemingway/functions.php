<?php


/* ---------------------------------------------------------------------------------------------
   THEME SETUP
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_setup' ) ) {
	function hemingway_setup() {
		
		// Automatic feed
		add_theme_support( 'automatic-feed-links' );
		
		// Custom background
		add_theme_support( 'custom-background' );
			
		// Post thumbnails
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'post-image', 676, 9999 );

		// Post formats
		add_theme_support( 'post-formats', array( 'video', 'aside', 'quote' ) );

		// Custom header
		add_theme_support( 'custom-header', array(
			'width'         => 1280,
			'height'        => 416,
			'default-image' => get_template_directory_uri() . '/assets/images/header.jpg',
			'uploads'       => true,
			'header-text'  	=> false
		) );

		// Custom logo
		add_theme_support( 'custom-logo', array(
			'height'      => 240,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'site-title', 'site-description' ),
		) );
		
		// Title tag
		add_theme_support( 'title-tag' );

		// Set content width
		global $content_width;
		$content_width = 676;

		// HTML5 semantic markup for search forms.
		add_theme_support( 'html5', array( 'search-form' ) );
		
		// Add nav menu
		register_nav_menu( 'primary', __( 'Primary Menu', 'hemingway' ) );
		
		// Make the theme translation ready
		load_theme_textdomain( 'hemingway', get_template_directory() . '/languages' );
		
	}
	add_action( 'after_setup_theme', 'hemingway_setup' );
}


/*	-----------------------------------------------------------------------------------------------
	REQUIRED FILES
	Include required files
--------------------------------------------------------------------------------------------------- */

// Include the Customizer class.
require get_template_directory() . '/inc/classes/class-hemingway-customize.php';


/* ---------------------------------------------------------------------------------------------
   ENQUEUE SCRIPTS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_load_javascript_files' ) ) :
	function hemingway_load_javascript_files() {

		wp_enqueue_script( 'hemingway_global', get_template_directory_uri() . '/assets/js/global.js', array( 'jquery' ), wp_get_theme( 'hemingway' )->get( 'Version' ), true );

		if ( is_singular() ) wp_enqueue_script( 'comment-reply' );

	}
	add_action( 'wp_enqueue_scripts', 'hemingway_load_javascript_files' );
endif;


/* ---------------------------------------------------------------------------------------------
   ENQUEUE STYLES
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_load_style' ) ) :
	function hemingway_load_style() {

		if ( is_admin() ) return;

		$theme_version = wp_get_theme( 'hemingway' )->get( 'Version' );

		$dependencies = array();

		$google_fonts_url = hemingway_get_google_fonts_url();

		if ( $google_fonts_url ) {
			wp_register_style( 'hemingway_googleFonts', $google_fonts_url, array(), null );
			$dependencies[] = 'hemingway_googleFonts';
		}

		wp_enqueue_style( 'hemingway_style', get_template_directory_uri() . '/style.css', $dependencies, $theme_version );

	}
	add_action( 'wp_print_styles', 'hemingway_load_style' );
endif;


/* ---------------------------------------------------------------------------------------------
   FILTER POST CLASS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_post_class' ) ) :
	function hemingway_post_class( $classes ) {

		// If we're not on singular, add a post preview class 
		if ( ! is_singular() ) {
			$classes[] = 'post-preview';
		}

		return $classes;

	}
	add_filter( 'post_class', 'hemingway_post_class' );
endif;


/* ---------------------------------------------------------------------------------------------
   ADD EDITOR STYLES
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_add_editor_styles' ) ) :
	function hemingway_add_editor_styles() {

		add_editor_style( '/assets/css/hemingway-classic-editor-style.css' );

		$google_fonts_url = hemingway_get_google_fonts_url();

		if ( $google_fonts_url ) {
			add_editor_style( str_replace( ',', '%2C', $google_fonts_url ) );
		}

	}
	add_action( 'init', 'hemingway_add_editor_styles' );
endif;


/* ---------------------------------------------------------------------------------------------
   GET GOOGLE FONTS URL
   Helper function for returning the Google Fonts URL.
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_get_google_fonts_url' ) ) :
	function hemingway_get_google_fonts_url() {

		/**
		 * Translators: If there are characters in your language that are not
		 * supported by the theme fonts, translate this to 'off'. Do not translate
		 * into your own language.
		 */
		$google_fonts = _x( 'on', 'Google Fonts: on or off', 'hemingway' );

		if ( 'off' === $google_fonts ) return;

		$font_families = apply_filters( 'hemingway_google_fonts_families', array( 'Lato:400,700,400italic,700italic|Raleway:400,700' ) );

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) )
		);

		$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );

		return $fonts_url;

	}
endif;


/* ---------------------------------------------------------------------------------------------
   REGISTER WIDGET AREAS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_sidebar_registration' ) ) :
	function hemingway_sidebar_registration() {

		$shared_args = array(
			'before_title' 	=> '<h3 class="widget-title">',
			'after_title' 	=> '</h3>',
			'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
			'after_widget' 	=> '</div></div>'
		);

		register_sidebar( array_merge( $shared_args, array(
			'name' 			=> __( 'Footer A', 'hemingway' ),
			'id' 			=> 'footer-a',
			'description' 	=> __( 'Widgets in this area will be shown in the left column in the footer.', 'hemingway' ),
		) ) );

		register_sidebar( array_merge( $shared_args, array(
			'name' 			=> __( 'Footer B', 'hemingway' ),
			'id' 			=> 'footer-b',
			'description' 	=> __( 'Widgets in this area will be shown in the middle column in the footer.', 'hemingway' ),
		) ) );

		register_sidebar( array_merge( $shared_args, array(
			'name' 			=> __( 'Footer C', 'hemingway' ),
			'id' 			=> 'footer-c',
			'description' 	=> __( 'Widgets in this area will be shown in the right column in the footer.', 'hemingway' ),
		) ) );

		register_sidebar( array_merge( $shared_args, array(
			'name' 			=> __( 'Sidebar', 'hemingway' ),
			'id' 			=> 'sidebar',
			'description'	=> __( 'Widgets in this area will be shown in the sidebar.', 'hemingway' ),
		) ) );

	}
	add_action( 'widgets_init', 'hemingway_sidebar_registration' ); 
endif;


/* ---------------------------------------------------------------------------------------------
   ADD CLASSES TO PAGINATION LINKS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_next_posts_link_class' ) ) :
	function hemingway_next_posts_link_class() {

		return 'class="post-nav-older"';

	}
	add_filter( 'next_posts_link_attributes', 'hemingway_next_posts_link_class' );
endif;

if ( ! function_exists( 'hemingway_prev_posts_link_class' ) ) :
	function hemingway_prev_posts_link_class() {

		return 'class="post-nav-newer"';

	}
	add_filter( 'previous_posts_link_attributes', 'hemingway_prev_posts_link_class' );
endif;


/* ---------------------------------------------------------------------------------------------
   CUSTOM MORE LINK TEXT
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_custom_more_link' ) ) :
	function hemingway_custom_more_link( $more_link, $more_link_text ) {

		return str_replace( $more_link_text, __( 'Continue reading', 'hemingway' ), $more_link );

	}
	add_filter( 'the_content_more_link', 'hemingway_custom_more_link', 10, 2 );
endif;


/*	-----------------------------------------------------------------------------------------------
	IS COMMENT BY POST AUTHOR?
	Check if the specified comment is written by the author of the post commented on.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_is_comment_by_post_author' ) ) :
	function hemingway_is_comment_by_post_author( $comment = null ) {

		if ( is_object( $comment ) && $comment->user_id > 0 ) {
			$user = get_userdata( $comment->user_id );
			$post = get_post( $comment->comment_post_ID );
			if ( ! empty( $user ) && ! empty( $post ) ) {
				return $comment->user_id === $post->post_author;
			}
		}
		return false;

	}
endif;


/* ---------------------------------------------------------------------------------------------
   HEMINGWAY COMMENT FUNCTION
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_comment' ) ) :
	function hemingway_comment( $comment, $args, $depth ) {

		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>
		
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		
			<?php __( 'Pingback:', 'hemingway' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'hemingway' ), '<span class="edit-link">(', ')</span>' ); ?>
			
		</li>
		<?php
				break;
			default :
			global $post;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		
			<div id="comment-<?php comment_ID(); ?>" class="comment">
			
				<div class="comment-meta comment-author vcard">
								
					<?php echo get_avatar( $comment, 120 ); ?>

					<div class="comment-meta-content">
												
						<?php printf( '<cite class="fn">%1$s %2$s</cite>',
							get_comment_author_link(),
							( hemingway_is_comment_by_post_author( $comment ) ) ? '<span class="post-author"> ' . __( '(Post author)', 'hemingway' ) . '</span>' : ''
						); ?>
						
						<p>
							<?php
							/* Translators: 1 = comment date, 2 = comment time */
							$comment_timestamp = sprintf( __( '%1$s at %2$s', 'hemingway' ), get_comment_date( '', $comment ), get_comment_time() );
							?>
							<time datetime="<?php comment_time( 'c' ); ?>" title="<?php echo $comment_timestamp; ?>">
								<?php echo $comment_timestamp; ?>
							</time>
						</p>
						
					</div><!-- .comment-meta-content -->
					
				</div><!-- .comment-meta -->

				<div class="comment-content post-content">
				
					<?php if ( '0' == $comment->comment_approved ) : ?>
					
						<p class="comment-awaiting-moderation"><?php _e( 'Awaiting moderation', 'hemingway' ); ?></p>
						
					<?php endif; ?>
				
					<?php comment_text(); ?>
					
					<div class="comment-actions group">
					
						<?php edit_comment_link( __( 'Edit', 'hemingway' ), '', '' ); ?>
						
						<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'hemingway' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
											
					</div><!-- .comment-actions -->
					
				</div><!-- .comment-content -->

			</div><!-- .comment-## -->
		<?php
			break;
		endswitch;
		
	}
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER ARCHIVE TITLE

	@param	$title string		The initial title
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_filter_archive_title' ) ) :
	function hemingway_filter_archive_title( $title ) {

		// On home, show no title
		if ( is_home() ) {
			$title = '';
		}

		// On search, show the search query.
		elseif ( is_search() ) {
			$title = sprintf( _x( 'Search: %s', '%s = The search query', 'hemingway' ), '&ldquo;' . get_search_query() . '&rdquo;' );
		}

		return $title;

	}
	add_filter( 'get_the_archive_title', 'hemingway_filter_archive_title' );
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER ARCHIVE DESCRIPTION

	@param	$description string		The initial description
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_filter_archive_description' ) ) :
	function hemingway_filter_archive_description( $description ) {
		
		// On search, show a string describing the results of the search.
		if ( is_search() ) {
			global $wp_query;
			if ( $wp_query->found_posts ) {
				/* Translators: %s = Number of results */
				$description = sprintf( _nx( 'We found %s result for your search.', 'We found %s results for your search.',  $wp_query->found_posts, '%s = Number of results', 'hemingway' ), $wp_query->found_posts );
			} else {
				$description = __( 'We could not find any results for your search. You can give it another try through the search form below.', 'hemingway' );
			}
		}

		return $description;

	}
	add_filter( 'get_the_archive_description', 'hemingway_filter_archive_description' );
endif;


/*	-----------------------------------------------------------------------------------------------
	CHECK IF POST TYPE SHOULD DISPLAY POST META
	Check whether the specified post type should display the post meta in the post header.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_post_type_has_post_meta_output' ) ) :
	function hemingway_post_type_has_post_meta_output( $post_type ) {

		$post_types_with_meta = apply_filters( 'hemingway_post_types_with_post_meta_output', array( 'post' ) );

		return in_array( $post_type, $post_types_with_meta );

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	OUTPUT FEATURED MEDIA
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_the_featured_media' ) ) :
	function hemingway_the_featured_media( $post ) {

		setup_postdata( $post );

		// Output nothing on password protected posts and pages.
		if ( post_password_required() ) return;

		// Legacy output
		$video_url = get_post_meta( $post->ID, 'videourl', true ); 
		
		?>

		<?php if ( has_post_thumbnail() || $video_url ) : ?>
						
			<figure class="featured-media">

				<?php 
				if ( is_sticky() ) : 
					?>

					<span class="sticky-post"><?php _e( 'Sticky post', 'hemingway' ); ?></span>

					<?php 
				endif;
				
				// Legacy output of video
				if ( $video_url ) :
					if ( strpos( $videourl, '.mp4' ) !== false ) : ?>
						<video controls>
							<source src="<?php echo esc_url( $videourl ); ?>" type="video/mp4">
						</video>			
						<?php 
					else : 
						echo wp_oembed_get( $videourl ); 
					endif; 

				// Output of post thumbnail
				elseif ( has_post_thumbnail() ) : 

					?>
			
					<a href="<?php the_permalink(); ?>" rel="bookmark">
						<?php the_post_thumbnail( 'post-image' ); ?>
					</a>

					<?php 

					$image_caption = get_the_post_thumbnail_caption();
					
					if ( $image_caption ) : ?>
									
						<div class="media-caption-container">
							<figcaption class="media-caption"><?php echo $image_caption; ?></figcaption>
						</div>
						
					<?php endif; ?>

				<?php endif; ?>
						
			</figure><!-- .featured-media -->
				
			<?php 
		endif;

		wp_reset_postdata();

	}
endif;


/* ---------------------------------------------------------------------------------------------
   SPECIFY BLOCK EDITOR SUPPORT
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_block_editor_features' ) ) :
	function hemingway_block_editor_features() {

		/* Block Editor Feature Opt-Ins ------ */

		add_theme_support( 'align-wide' );

		/* Block Editor Color Palette -------- */

		$accent_color = get_theme_mod( 'accent_color' ) ? get_theme_mod( 'accent_color' ) : '#1abc9c';

		add_theme_support( 'editor-color-palette', array(
			array(
				'name' 	=> _x( 'Accent', 'Name of the accent color in the Gutenberg palette', 'hemingway' ),
				'slug' 	=> 'accent',
				'color' => $accent_color,
			),
			array(
				'name' 	=> _x( 'Dark Gray', 'Name of the dark gray color in the Gutenberg palette', 'hemingway' ),
				'slug' 	=> 'dark-gray',
				'color' => '#444',
			),
			array(
				'name' 	=> _x( 'Medium Gray', 'Name of the medium gray color in the Gutenberg palette', 'hemingway' ),
				'slug' 	=> 'medium-gray',
				'color' => '#666',
			),
			array(
				'name' 	=> _x( 'Light Gray', 'Name of the light gray color in the Gutenberg palette', 'hemingway' ),
				'slug' 	=> 'light-gray',
				'color' => '#888',
			),
			array(
				'name' 	=> _x( 'White', 'Name of the white color in the Gutenberg palette', 'hemingway' ),
				'slug' 	=> 'white',
				'color' => '#fff',
			),
		) );

		/* Block Editor Font Sizes ----------- */

		add_theme_support( 'editor-font-sizes', array(
			array(
				'name' 		=> _x( 'Small', 'Name of the small font size in Gutenberg', 'hemingway' ),
				'shortName' => _x( 'S', 'Short name of the small font size in the Gutenberg editor.', 'hemingway' ),
				'size' 		=> 16,
				'slug' 		=> 'small'
			),
			array(
				'name' 		=> _x( 'Regular', 'Name of the regular font size in Gutenberg', 'hemingway' ),
				'shortName' => _x( 'M', 'Short name of the regular font size in the Gutenberg editor.', 'hemingway' ),
				'size' 		=> 19,
				'slug' 		=> 'regular'
			),
			array(
				'name' 		=> _x( 'Large', 'Name of the large font size in Gutenberg', 'hemingway' ),
				'shortName' => _x( 'L', 'Short name of the large font size in the Gutenberg editor.', 'hemingway' ),
				'size' 		=> 24,
				'slug' 		=> 'large'
			),
			array(
				'name' 		=> _x( 'Larger', 'Name of the larger font size in Gutenberg', 'hemingway' ),
				'shortName' => _x( 'XL', 'Short name of the larger font size in the Gutenberg editor.', 'hemingway' ),
				'size' 		=> 32,
				'slug' 		=> 'larger'
			)
		) );
		
	}
	add_action( 'after_setup_theme', 'hemingway_block_editor_features' );
endif;


/* ---------------------------------------------------------------------------------------------
   BLOCK EDITOR STYLES
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'hemingway_block_editor_styles' ) ) :
	function hemingway_block_editor_styles() {

		$dependencies = array();

		$google_fonts_url = hemingway_get_google_fonts_url();

		if ( $google_fonts_url ) {
			wp_register_style( 'hemingway-block-editor-styles-font', $google_fonts_url, false, 1.0, 'all' );
			$dependencies[] = 'hemingway-block-editor-styles-font';
		}

		// Enqueue the editor styles
		wp_enqueue_style( 'hemingway-block-editor-styles', get_theme_file_uri( '/assets/css/hemingway-block-editor-style.css' ), $dependencies, '1.0', 'all' );

	}
	add_action( 'enqueue_block_editor_assets', 'hemingway_block_editor_styles' );
endif;
