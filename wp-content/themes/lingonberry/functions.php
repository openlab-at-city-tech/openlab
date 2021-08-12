<?php


/*	-----------------------------------------------------------------------------------------------
	THEME SETUP
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_setup' ) ) :
	function lingonberry_setup() {
		
		// Automatic feed
		add_theme_support( 'automatic-feed-links' );
		
		// Custom background
		add_theme_support( 'custom-background' );
		
		// Post formats
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
		
		// Post thumbnails
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 766, 9999 );
		
		// Title tag
		add_theme_support( 'title-tag' );

		// Set content width
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 766;
		} 

		// Custom header (logo)
		add_theme_support( 'custom-header', array( 
			'width' 		=> 200, 
			'height' 		=> 200, 
			'header-text' 	=> false 
		) );

		// HTML5 semantic markup for search forms.
		add_theme_support( 'html5', array( 'search-form' ) );
		
		// Add nav menu
		register_nav_menu( 'primary', 'Primary Menu' );
		
		// Make the theme translation ready
		load_theme_textdomain( 'lingonberry', get_template_directory() . '/languages' );
		
	}
	add_action( 'after_setup_theme', 'lingonberry_setup' );
endif;


/*	-----------------------------------------------------------------------------------------------
	REQUIRED FILES
	Include required files
--------------------------------------------------------------------------------------------------- */

// Handle Customizer settings.
require get_template_directory() . '/inc/classes/class-lingonberry-customize.php';


/*	-----------------------------------------------------------------------------------------------
	ENQUEUE SCRIPTS
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_load_javascript_files' ) ) :
	function lingonberry_load_javascript_files() {

		$theme_version = wp_get_theme( 'lingonberry' )->get( 'Version' );

		wp_register_script( 'lingonberry_flexslider', get_template_directory_uri().'/assets/js/flexslider.min.js' );
		wp_enqueue_script( 'lingonberry_global', get_template_directory_uri().'/assets/js/global.js', array( 'jquery', 'lingonberry_flexslider' ), $theme_version, true );

		if ( is_singular() ) wp_enqueue_script( 'comment-reply' );

	}
	add_action( 'wp_enqueue_scripts', 'lingonberry_load_javascript_files' );
endif;


/*	-----------------------------------------------------------------------------------------------
	ENQUEUE STYLES
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_load_style' ) ) :
	function lingonberry_load_style() {

		if ( is_admin() ) return;

		$theme_version = wp_get_theme( 'lingonberry' )->get( 'Version' );
		$dependencies = array();

		/**
		 * Translators: If there are characters in your language that are not
		 * supported by the theme fonts, translate this to 'off'. Do not translate
		 * into your own language.
		 */
		$google_fonts = _x( 'on', 'Google Fonts: on or off', 'lingonberry' );

		if ( 'off' !== $google_fonts ) {
			wp_register_style( 'lingonberry_google_fonts', '//fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic|Raleway:600,500,400' );
			$dependencies[] = 'lingonberry_google_fonts';
		}

		wp_enqueue_style( 'lingonberry_style', get_stylesheet_uri(), $dependencies, $theme_version );

	}
	add_action( 'wp_print_styles', 'lingonberry_load_style' );
endif;


/*	-----------------------------------------------------------------------------------------------
	EDITOR STYLES
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_add_editor_styles' ) ) :
	function lingonberry_add_editor_styles() {

		add_editor_style( 'assets/css/classic-editor-styles.css' );

		/**
		 * Translators: If there are characters in your language that are not
		 * supported by the theme fonts, translate this to 'off'. Do not translate
		 * into your own language.
		 */
		$google_fonts = _x( 'on', 'Google Fonts: on or off', 'lingonberry' );

		if ( 'off' !== $google_fonts ) {
			$font_url = '//fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic|Raleway:600,500,400';
			add_editor_style( str_replace( ',', '%2C', $font_url ) );
		}

	}
	add_action( 'init', 'lingonberry_add_editor_styles' );
endif;


/*	-----------------------------------------------------------------------------------------------
	REGISTER WIDGET AREAS
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_sidebar_registration' ) ) :
	function lingonberry_sidebar_registration() {

		$shared_args = array(
			'after_title' 	=> '</h3>',
			'after_widget' 	=> '</div><div class="clear"></div></div>',
			'before_title' 	=> '<h3 class="widget-title">',
			'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content">',
		);

		register_sidebar( array_merge( $shared_args, array(
			'description' 	=> __( 'Widgets in this area will be shown in the first column in the footer.', 'lingonberry' ),
			'id' 			=> 'footer-a',
			'name' 			=> __( 'Footer A', 'lingonberry' ),
		) ) );

		register_sidebar( array_merge( $shared_args, array(
			'description' 	=> __( 'Widgets in this area will be shown in the second column in the footer.', 'lingonberry' ),
			'id' 			=> 'footer-b',
			'name' 			=> __( 'Footer B', 'lingonberry' ),
		) ) );

		register_sidebar( array_merge( $shared_args, array(
			'description' 	=> __( 'Widgets in this area will be shown in the third column in the footer.', 'lingonberry' ),
			'id' 			=> 'footer-c',
			'name' 			=> __( 'Footer C', 'lingonberry' ),
		) ) );

	}
	add_action( 'widgets_init', 'lingonberry_sidebar_registration' ); 
endif;


/*	-----------------------------------------------------------------------------------------------
	NO JS CLASS
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_html_js_class' ) ) :
	function lingonberry_html_js_class() {

		echo '<script>document.documentElement.className = document.documentElement.className.replace("no-js","js");</script>'. "\n";

	}
	add_action( 'wp_head', 'lingonberry_html_js_class', 1 );
endif;


/*	-----------------------------------------------------------------------------------------------
	ADD CLASSES TO PAGINATION
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_posts_link_attributes_1' ) ) :
	function lingonberry_posts_link_attributes_1() {

		return 'class="post-nav-older"';

	}
	add_filter( 'next_posts_link_attributes', 'lingonberry_posts_link_attributes_1' );
endif;

if ( ! function_exists( 'lingonberry_posts_link_attributes_2' ) ) :
	function lingonberry_posts_link_attributes_2() {

		return 'class="post-nav-newer"';

	}
	add_filter( 'previous_posts_link_attributes', 'lingonberry_posts_link_attributes_2' );
endif;


/*	-----------------------------------------------------------------------------------------------
	CUSTOM MORE LINK TEXT
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_custom_more_link' ) ) :
	function lingonberry_custom_more_link( $more_link, $more_link_text ) {

		return str_replace( $more_link_text, __( 'Continue reading', 'lingonberry' ), $more_link );

	}
	add_filter( 'the_content_more_link', 'lingonberry_custom_more_link', 10, 2 );
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER ARCHIVE TITLE

	@param	$title string		The initial title
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_filter_archive_title' ) ) :
	function lingonberry_filter_archive_title( $title ) {

		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1; 

		// On home, show no title
		if ( is_home() ) {
			if ( $paged == 1 ) {
				$title = '';
			} else {
				global $wp_query;
				$title = sprintf( __( 'Page %1$s of %2$s', 'lingonberry' ), $paged, $wp_query->max_num_pages );
			}
		}

		// On search, show the search query.
		elseif ( is_search() ) {
			$title = sprintf( _x( 'Search: %s', '%s = The search query', 'lingonberry' ), '&ldquo;' . get_search_query() . '&rdquo;' );
		} 

		return $title;

	}
	add_filter( 'get_the_archive_title', 'lingonberry_filter_archive_title' );
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER ARCHIVE DESCRIPTION

	@param	$description string		The initial description
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_filter_archive_description' ) ) :
	function lingonberry_filter_archive_description( $description ) {
		
		// On search, show a string describing the results of the search.
		if ( is_search() ) {
			global $wp_query;
			if ( $wp_query->found_posts ) {
				/* Translators: %s = Number of results */
				$description = sprintf( _nx( 'We found %s result for your search.', 'We found %s results for your search.',  $wp_query->found_posts, '%s = Number of results', 'lingonberry' ), $wp_query->found_posts );
			}
		}

		return $description;

	}
	add_filter( 'get_the_archive_description', 'lingonberry_filter_archive_description' );
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER BODY CLASS

	@param	$classes array		Body classes
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingoberry_filter_body_class' ) ) :
	function lingoberry_filter_body_class( $classes ) {

		// Slim page template class names (class = name - file suffix).
		if ( is_page_template() ) {
			$classes[] = basename( get_page_template_slug(), '.php' );
		}
		
		// Add a shared class for styling archive pages.
		if ( is_search() || is_archive() || is_home() ) {
			$classes[] = 'archive-template';
		}

		return $classes;

	}
	add_filter( 'body_class', 'lingoberry_filter_body_class' );
endif;



/* ---------------------------------------------------------------------------------------------
   FILTER THE_CONTENT
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_the_content' ) ) :
	function lingonberry_the_content( $content ) {

		// On the archives template, append the content for that template.
		if ( is_page_template( 'template-archives.php' ) ) {
			ob_start();
			include( locate_template( 'inc/archives-template-content.php' ) );
			$content .= ob_get_clean();
		}
		
		return $content;

	}	
	add_filter( 'the_content', 'lingonberry_the_content' );
endif;


/*	-----------------------------------------------------------------------------------------------
	FLEXSLIDER OUTPUT
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_flexslider' ) ) :
	function lingonberry_flexslider( $size = 'post-thumbnail' ) {

		$attachment_parent = is_page() ? $post->ID : get_the_ID();

		$images = get_posts( array(
			'orderby'        	=> 'menu_order',
			'order'          	=> 'ASC',
			'post_mime_type' 	=> 'image',
			'post_parent'    	=> $attachment_parent,
			'post_status'    	=> null,
			'post_type'      	=> 'attachment',
			'posts_per_page'    => -1,
		) );

		if ( ! $images ) return;
		
		?>
		
		<div class="flexslider">
		
			<ul class="slides">
	
				<?php 
				foreach ( $images as $image ) :

					$attimg = wp_get_attachment_image( $image->ID, $size ); 
					
					?>
					
					<li>
						<?php echo $attimg; ?>
						<?php if ( ! empty( $image->post_excerpt ) ) : ?>
							<div class="media-caption-container">
								<p class="media-caption"><?php echo $image->post_excerpt; ?></p>
							</div>
						<?php endif; ?>
					</li>
					
					<?php 
				endforeach;
				?>
		
			</ul>
			
		</div><!-- .flexslider -->
			
		<?php

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	META FUNCTION
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_meta' ) ) :
	function lingonberry_meta() { 
		
		?>
		
		<div class="post-meta">
		
			<span class="post-date"><a href="<?php the_permalink(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></span>
			
			<span class="date-sep"> / </span>
				
			<span class="post-author"><?php the_author_posts_link(); ?></span>
			
			<?php if ( comments_open() ) : ?>
			
				<span class="date-sep"> / </span>
				
				<?php comments_popup_link( '<span class="comment">' . __( '0 Comments', 'lingonberry' ) . '</span>', __( '1 Comment', 'lingonberry' ), __( '% Comments', 'lingonberry' ) ); ?>
			
			<?php endif; ?>
			
			<?php if ( is_sticky() && ! has_post_thumbnail() ) : ?> 
			
				<span class="date-sep"> / </span>
			
				<?php _e( 'Sticky', 'lingonberry' ); ?>
			
			<?php endif; ?>
			
			<?php edit_post_link(__( 'Edit', 'lingonberry' ), '<span class="date-sep"> / </span>' ); ?>
									
		</div><!-- .post-meta -->

		<?php	
	}
endif;


/*	-----------------------------------------------------------------------------------------------
	COMMENT FUNCTION
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_comment' ) ) :
	function lingonberry_comment( $comment, $args, $depth ) {

		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>
		
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		
			<?php __( 'Pingback:', 'lingonberry' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'lingonberry' ), '<span class="edit-link">', '</span>' ); ?>
			
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
							( $comment->user_id === $post->post_author ) ? '<span class="post-author"> ' . __( '(Post author)', 'lingonberry' ) . '</span>' : ''
						); ?>
						
						<p><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>"><?php echo get_comment_date() . ' &mdash; ' . get_comment_time() ?></a></p>
						
					</div><!-- .comment-meta-content -->
					
					<div class="comment-actions">
					
						<?php edit_comment_link( __( 'Edit', 'lingonberry' ), '', '' ); ?>
						
						<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'lingonberry' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
										
					</div><!-- .comment-actions -->
					
					<div class="clear"></div>
					
				</div><!-- .comment-meta -->

				<div class="comment-content post-content">
				
					<?php if ( ! $comment->comment_approved ) : ?>
					
						<p class="comment-awaiting-moderation"><?php __( 'Your comment is awaiting moderation.', 'lingonberry' ); ?></p>
						
					<?php endif; ?>
				
					<?php comment_text(); ?>
					
					<div class="comment-actions">
					
						<?php edit_comment_link( __( 'Edit', 'lingonberry' ), '', '' ); ?>
						
						<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'lingonberry' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
						
						<div class="clear"></div>
					
					</div><!-- .comment-actions -->
					
				</div><!-- .comment-content -->

			</div><!-- .comment-## -->
		<?php
			break;
		endswitch;

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	BLOCK EDITOR SUPPORT
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_add_gutenberg_features' ) ) :
	function lingonberry_add_gutenberg_features() {

		/* Gutenberg Palette --------------------------------------- */

		$accent_color = get_theme_mod( 'accent_color', '#ff706c' );

		add_theme_support( 'editor-color-palette', array(
			array(
				'name' 	=> _x( 'Accent', 'Name of the accent color in the Gutenberg palette', 'lingonberry' ),
				'slug' 	=> 'accent',
				'color' => $accent_color,
			),
			array(
				'name' 	=> _x( 'Black', 'Name of the black color in the Gutenberg palette', 'lingonberry' ),
				'slug' 	=> 'black',
				'color' => '#111',
			),
			array(
				'name' 	=> _x( 'Darkest gray', 'Name of the darkest gray color in the Gutenberg palette', 'lingonberry' ),
				'slug' 	=> 'darkest-gray',
				'color' => '#444',
			),
			array(
				'name' 	=> _x( 'Dark gray', 'Name of the dark gray color in the Gutenberg palette', 'lingonberry' ),
				'slug' 	=> 'dark-gray',
				'color' => '#555',
			),
			array(
				'name' 	=> _x( 'Gray', 'Name of the gray color in the Gutenberg palette', 'lingonberry' ),
				'slug' 	=> 'gray',
				'color' => '#666',
			),
			array(
				'name' 	=> _x( 'Light gray', 'Name of the light gray color in the Gutenberg palette', 'lingonberry' ),
				'slug' 	=> 'light-gray',
				'color' => '#eee',
			),
			array(
				'name' 	=> _x( 'Lightest gray', 'Name of the lightest gray color in the Gutenberg palette', 'lingonberry' ),
				'slug' 	=> 'lightest-gray',
				'color' => '#f1f1f1',
			),
			array(
				'name' 	=> _x( 'White', 'Name of the white color in the Gutenberg palette', 'lingonberry' ),
				'slug' 	=> 'white',
				'color' => '#fff',
			),
		) );

		/* Gutenberg Font Sizes --------------------------------------- */

		add_theme_support( 'editor-font-sizes', array(
			array(
				'name' 		=> _x( 'Small', 'Name of the small font size in Gutenberg', 'lingonberry' ),
				'shortName' => _x( 'S', 'Short name of the small font size in the Gutenberg editor.', 'lingonberry' ),
				'size' 		=> 16,
				'slug' 		=> 'small',
			),
			array(
				'name' 		=> _x( 'Regular', 'Name of the regular font size in Gutenberg', 'lingonberry' ),
				'shortName' => _x( 'M', 'Short name of the regular font size in the Gutenberg editor.', 'lingonberry' ),
				'size' 		=> 19,
				'slug' 		=> 'regular',
			),
			array(
				'name' 		=> _x( 'Large', 'Name of the large font size in Gutenberg', 'lingonberry' ),
				'shortName' => _x( 'L', 'Short name of the large font size in the Gutenberg editor.', 'lingonberry' ),
				'size' 		=> 23,
				'slug' 		=> 'large',
			),
			array(
				'name' 		=> _x( 'Larger', 'Name of the larger font size in Gutenberg', 'lingonberry' ),
				'shortName' => _x( 'XL', 'Short name of the larger font size in the Gutenberg editor.', 'lingonberry' ),
				'size' 		=> 30,
				'slug' 		=> 'larger',
			),
		) );

	}
	add_action( 'after_setup_theme', 'lingonberry_add_gutenberg_features' );
endif;


/*	-----------------------------------------------------------------------------------------------
	BLOCK EDITOR STYLES
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'lingonberry_block_editor_styles' ) ) :
	function lingonberry_block_editor_styles() {

		$theme_version = wp_get_theme( 'lingonberry' )->get( 'Version' );
		$dependencies = array();

		/**
		 * Translators: If there are characters in your language that are not
		 * supported by the theme fonts, translate this to 'off'. Do not translate
		 * into your own language.
		 */
		$google_fonts = _x( 'on', 'Google Fonts: on or off', 'lingonberry' );

		if ( 'off' !== $google_fonts ) {
			wp_register_style( 'lingonberry-block-editor-styles-font', '//fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic|Raleway:600,500,400' );
			$dependencies[] = 'lingonberry-block-editor-styles-font';
		}

		// Enqueue the editor styles
		wp_enqueue_style( 'lingonberry-block-editor-styles', get_theme_file_uri( '/assets/css/block-editor-styles.css' ), $dependencies, $theme_version, 'all' );

	}
	add_action( 'enqueue_block_editor_assets', 'lingonberry_block_editor_styles', 1 );
endif;
