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
		$args = array(
			'width'         => 1280,
			'height'        => 416,
			'default-image' => get_template_directory_uri() . '/images/header.jpg',
			'uploads'       => true,
			'header-text'  	=> false
			
		);
		add_theme_support( 'custom-header', $args );
		
		// Title tag
		add_theme_support( 'title-tag' );

		// Set content width
		global $content_width;
		if ( ! isset( $content_width ) ) $content_width = 676;
		
		// Add nav menu
		register_nav_menu( 'primary', 'Primary Menu' );
		
		// Make the theme translation ready
		load_theme_textdomain( 'hemingway', get_template_directory() . '/languages' );
		
	}
	add_action( 'after_setup_theme', 'hemingway_setup' );

}


/* ---------------------------------------------------------------------------------------------
   ENQUEUE SCRIPTS
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'hemingway_load_javascript_files' ) ) {

	function hemingway_load_javascript_files() {
		if ( ! is_admin() ) {
			wp_enqueue_script( 'hemingway_global', get_template_directory_uri() . '/js/global.js', array( 'jquery' ), '', true );
			if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'hemingway_load_javascript_files' );

}


/* ---------------------------------------------------------------------------------------------
   ENQUEUE STYLES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'hemingway_load_style' ) ) {

	function hemingway_load_style() {
		if ( ! is_admin() ) {
			wp_enqueue_style( 'hemingway_googleFonts', '//fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic|Raleway:700,400' );
			wp_enqueue_style( 'hemingway_style', get_template_directory_uri() . '/style.css' );
		}
	}
	add_action( 'wp_print_styles', 'hemingway_load_style' );

}


/* ---------------------------------------------------------------------------------------------
   ADD EDITOR STYLES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'hemingway_add_editor_styles' ) ) {

	function hemingway_add_editor_styles() {
		add_editor_style( 'hemingway-editor-style.css' );
		$font_url = '//fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic|Raleway:700,400';
		add_editor_style( str_replace( ',', '%2C', $font_url ) );
	}
	add_action( 'init', 'hemingway_add_editor_styles' );

}


/* ---------------------------------------------------------------------------------------------
   ADD WIDGET AREAS
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'hemingway_sidebar_registration' ) ) {

	function hemingway_sidebar_registration() {

		register_sidebar( array(
			'name' 			=> __( 'Footer A', 'hemingway' ),
			'id' 			=> 'footer-a',
			'description' 	=> __( 'Widgets in this area will be shown in the left column in the footer.', 'hemingway' ),
			'before_title' 	=> '<h3 class="widget-title">',
			'after_title' 	=> '</h3>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget' 	=> '</div><div class="clear"></div></div>'
		) );

		register_sidebar( array(
			'name' 			=> __( 'Footer B', 'hemingway' ),
			'id' 			=> 'footer-b',
			'description' 	=> __( 'Widgets in this area will be shown in the middle column in the footer.', 'hemingway' ),
			'before_title' 	=> '<h3 class="widget-title">',
			'after_title' 	=> '</h3>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget' 	=> '</div><div class="clear"></div></div>'
		) );

		register_sidebar( array(
			'name' 			=> __( 'Footer C', 'hemingway' ),
			'id' 			=> 'footer-c',
			'description' 	=> __( 'Widgets in this area will be shown in the right column in the footer.', 'hemingway' ),
			'before_title' 	=> '<h3 class="widget-title">',
			'after_title' 	=> '</h3>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget' 	=> '</div><div class="clear"></div></div>'
		) );

		register_sidebar( array(
			'name' 			=> __( 'Sidebar', 'hemingway' ),
			'id' 			=> 'sidebar',
			'description'	=> __( 'Widgets in this area will be shown in the sidebar.', 'hemingway' ),
			'before_title' 	=> '<h3 class="widget-title">',
			'after_title' 	=> '</h3>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget' 	=> '</div><div class="clear"></div></div>'
		) );

	}
	add_action( 'widgets_init', 'hemingway_sidebar_registration' ); 

}
	

/* ---------------------------------------------------------------------------------------------
   ADD THEME WIDGETS
   --------------------------------------------------------------------------------------------- */


require_once( get_template_directory() . '/widgets/dribbble-widget.php' );
require_once( get_template_directory() . '/widgets/flickr-widget.php' );


/* ---------------------------------------------------------------------------------------------
   ADD CLASSES TO PAGINATION LINKS
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'hemingway_next_posts_link_class' ) ) {

	function hemingway_next_posts_link_class() {
		return 'class="post-nav-older"';
	}
	add_filter( 'next_posts_link_attributes', 'hemingway_next_posts_link_class' );

}

if ( ! function_exists( 'hemingway_prev_posts_link_class' ) ) {

	function hemingway_prev_posts_link_class() {
		return 'class="post-nav-newer"';
	}
	add_filter( 'previous_posts_link_attributes', 'hemingway_prev_posts_link_class' );

}


/* ---------------------------------------------------------------------------------------------
   CUSTOM MENU WALKER WITH HAS-CHILDREN CLASS
   --------------------------------------------------------------------------------------------- */


class hemingway_nav_walker extends Walker_Nav_Menu {
    function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
        $id_field = $this->db_fields['id'];
        if ( ! empty( $children_elements[ $element->$id_field ] ) ) {
            $element->classes[] = 'has-children';
        }
        Walker_Nav_Menu::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }
}


/* ---------------------------------------------------------------------------------------------
   BODY CLASSES
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'hemingway_body_classes' ) ) {

	function hemingway_body_classes( $classes ) {

		// If there's a post thumbnail
		if ( has_post_thumbnail() ) {
			$classes[] = 'has-featured-image';
		}

		return $classes;
	}
	add_action( 'body_class', 'hemingway_body_classes' );

}


/* ---------------------------------------------------------------------------------------------
   CUSTOM MORE LINK TEXT
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'hemingway_custom_more_link' ) ) {

	function hemingway_custom_more_link( $more_link, $more_link_text ) {
		return str_replace( $more_link_text, __( 'Continue reading', 'hemingway' ), $more_link );
	}
	add_filter( 'the_content_more_link', 'hemingway_custom_more_link', 10, 2 );

}


/* ---------------------------------------------------------------------------------------------
   STYLE THE ADMIN AREA
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'hemingway_admin_css' ) ) {

	function hemingway_admin_css() { ?>
	<style type="text/css">
			#postimagediv #set-post-thumbnail img {
				max-width: 100%;
				height: auto;
			}
		</style>
		<?php 
	}
	add_action( 'admin_head', 'hemingway_admin_css' );

}


/* ---------------------------------------------------------------------------------------------
   HEMINGWAY COMMENT FUNCTION
   --------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'hemingway_comment' ) ) {
	function hemingway_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>
		
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		
			<?php __( 'Pingback:', 'hemingway' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'hemingway' ), '<span class="edit-link">', '</span>' ); ?>
			
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
							( $comment->user_id === $post->post_author ) ? '<span class="post-author"> ' . __( '(Post author)', 'hemingway' ) . '</span>' : ''
						); ?>
						
						<p><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>"><?php echo get_comment_date() . ' at ' . get_comment_time() ?></a></p>
						
					</div><!-- .comment-meta-content -->
					
				</div><!-- .comment-meta -->

				<div class="comment-content post-content">
				
					<?php if ( '0' == $comment->comment_approved ) : ?>
					
						<p class="comment-awaiting-moderation"><?php _e( 'Awaiting moderation', 'hemingway' ); ?></p>
						
					<?php endif; ?>
				
					<?php comment_text(); ?>
					
					<div class="comment-actions">
					
						<?php edit_comment_link( __( 'Edit', 'hemingway' ), '', '' ); ?>
						
						<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'hemingway' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
						
						<div class="clear"></div>
					
					</div><!-- .comment-actions -->
					
				</div><!-- .comment-content -->

			</div><!-- .comment-## -->
		<?php
			break;
		endswitch;
	}
}


/* ---------------------------------------------------------------------------------------------
   CUSTOMIZER SETTINGS
   --------------------------------------------------------------------------------------------- */


class Hemingway_Customize {

   public static function register ( $wp_customize ) {
   
      //1. Define a new section (if desired) to the Theme Customizer
      $wp_customize->add_section( 'hemingway_options', 
         array(
            'title' 		=> __( 'Hemingway Options', 'hemingway' ), //Visible title of section
            'priority'		=> 35, //Determines what order this appears in
            'capability' 	=> 'edit_theme_options', //Capability needed to tweak
            'description' 	=> __('Allows you to customize some settings for Hemingway.', 'hemingway'), //Descriptive tooltip
         ) 
      );
      
      $wp_customize->add_section( 'hemingway_logo_section' , array(
		    'title'       => __( 'Logo', 'hemingway' ),
		    'priority'    => 40,
		    'description' => __('Upload a logo to replace the default site name and description in the header','hemingway'),
		) );
      
      //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'accent_color', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' 			=> '#1abc9c', //Default setting/value to save
            'type' 				=> 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' 		=> 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' 		=> 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
            'sanitize_callback' => 'sanitize_hex_color'
         ) 
      );
      
      
      // Add logo setting and sanitize it
      $wp_customize->add_setting( 'hemingway_logo', 
      	array( 
      		'sanitize_callback' => 'esc_url_raw'
      	) 
      );

                  
      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'hemingway_accent_color', //Set a unique ID for the control
         array(
            'label' 		=> __( 'Accent Color', 'hemingway' ), //Admin-visible name of the control
            'section' 		=> 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' 		=> 'accent_color', //Which setting to load and manipulate (serialized is okay)
            'priority' 		=> 10, //Determines the order this control appears in for the specified section
         ) 
      ) );
      
      $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hemingway_logo', array(
		    'label'    => __( 'Logo', 'hemingway' ),
		    'section'  => 'hemingway_logo_section',
		    'settings' => 'hemingway_logo',
		) ) );
		
      
      //4. We can also change built-in settings by modifying properties. For instance, let's make some stuff use live preview JS...
      $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
      $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
   }

   public static function header_output() {
      ?>
      
	      <!--Customizer CSS--> 
	      
	      <style type="text/css">
	           <?php self::generate_css('body::selection', 'background', 'accent_color'); ?>
	           <?php self::generate_css('body a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('body a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.blog-title a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.blog-menu a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.blog-search #searchsubmit', 'background-color', 'accent_color'); ?>
	           <?php self::generate_css('.blog-search #searchsubmit', 'border-color', 'accent_color'); ?>
	           <?php self::generate_css('.blog-search #searchsubmit:hover', 'background-color', 'accent_color'); ?>
	           <?php self::generate_css('.blog-search #searchsubmit:hover', 'border-color', 'accent_color'); ?>
	           <?php self::generate_css('.featured-media .sticky-post', 'background-color', 'accent_color'); ?>
	           <?php self::generate_css('.post-title a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.post-meta a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.post-content a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.post-content a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.blog .format-quote blockquote cite a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.post-content a.more-link:hover', 'background-color', 'accent_color'); ?>
	           <?php self::generate_css('.post-content input[type="submit"]:hover', 'background-color', 'accent_color'); ?>
	           <?php self::generate_css('.post-content input[type="reset"]:hover', 'background-color', 'accent_color'); ?>
	           <?php self::generate_css('.post-content input[type="button"]:hover', 'background-color', 'accent_color'); ?>
	           <?php self::generate_css('.post-content fieldset legend', 'background-color', 'accent_color'); ?>
	           <?php self::generate_css('.post-content .searchform #searchsubmit', 'background', 'accent_color'); ?>
	           <?php self::generate_css('.post-content .searchform #searchsubmit', 'border-color', 'accent_color'); ?>
	           <?php self::generate_css('.post-content .searchform #searchsubmit:hover', 'background', 'accent_color'); ?>
	           <?php self::generate_css('.post-content .searchform #searchsubmit:hover', 'border-color', 'accent_color'); ?>
	           <?php self::generate_css('.post-categories a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.post-categories a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.post-tags a:hover', 'background', 'accent_color'); ?>
	           <?php self::generate_css('.post-tags a:hover:after', 'border-right-color', 'accent_color'); ?>
	           <?php self::generate_css('.post-nav a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.archive-nav a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.logged-in-as a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.logged-in-as a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.content #respond input[type="submit"]:hover', 'background-color', 'accent_color'); ?>
	           <?php self::generate_css('.comment-meta-content cite a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.comment-meta-content p a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.comment-actions a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('#cancel-comment-reply-link', 'color', 'accent_color'); ?>
	           <?php self::generate_css('#cancel-comment-reply-link:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.comment-nav-below a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget-title a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget-title a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_text a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_text a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_rss a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_rss a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_archive a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_archive a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_meta a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_meta a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_recent_comments a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_recent_comments a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_pages a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_pages a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_links a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_links a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_recent_entries a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_recent_entries a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_categories a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_categories a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_search #searchsubmit', 'background', 'accent_color'); ?>
	           <?php self::generate_css('.widget_search #searchsubmit', 'border-color', 'accent_color'); ?>
	           <?php self::generate_css('.widget_search #searchsubmit:hover', 'background', 'accent_color'); ?>
	           <?php self::generate_css('.widget_search #searchsubmit:hover', 'border-color', 'accent_color'); ?>
	           <?php self::generate_css('#wp-calendar a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('#wp-calendar a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('#wp-calendar tfoot a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.dribbble-shot:hover', 'background', 'accent_color'); ?>
	           <?php self::generate_css('.widgetmore a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.widgetmore a:hover', 'color', 'accent_color'); ?>
	           <?php self::generate_css('.flickr_badge_image a:hover img', 'background', 'accent_color'); ?>
	           <?php self::generate_css('.footer .flickr_badge_image a:hover img', 'background', 'accent_color'); ?>
	           <?php self::generate_css('.footer .dribbble-shot:hover img', 'background', 'accent_color'); ?>
	           <?php self::generate_css('.sidebar .tagcloud a:hover', 'background', 'accent_color'); ?>
	           <?php self::generate_css('.footer .tagcloud a:hover', 'background', 'accent_color'); ?>
	           <?php self::generate_css('.credits a:hover', 'color', 'accent_color'); ?>
	           
	           <?php self::generate_css('body#tinymce.wp-editor a', 'color', 'accent_color'); ?>
	           <?php self::generate_css('body#tinymce.wp-editor a:hover', 'color', 'accent_color'); ?>
	      </style> 
	      
	      <!--/Customizer CSS-->
	      
      <?php
   }
   
   public static function live_preview() {
      wp_enqueue_script( 
           'hemingway-themecustomizer', // Give the script a unique ID
           get_template_directory_uri() . '/js/theme-customizer.js', // Define the path to the JS file
           array(  'jquery', 'customize-preview' ), // Define dependencies
           '', // Define a version (optional) 
           true // Specify whether to put in footer (leave this true)
      );
   }

   public static function generate_css( $selector, $style, $mod_name, $prefix='', $postfix='', $echo=true ) {
      $return = '';
      $mod = get_theme_mod($mod_name);
      if ( ! empty( $mod ) ) {
         $return = sprintf('%s { %s:%s; }',
            $selector,
            $style,
            $prefix.$mod.$postfix
         );
         if ( $echo ) echo $return;
      }
      return $return;
    }
}

// Setup the Theme Customizer settings and controls...
add_action( 'customize_register' , array( 'Hemingway_Customize' , 'register' ) );

// Output custom CSS to live site
add_action( 'wp_head' , array( 'Hemingway_Customize' , 'header_output' ) );

// Enqueue live preview javascript in Theme Customizer admin screen
add_action( 'customize_preview_init' , array( 'Hemingway_Customize' , 'live_preview' ) );

?>