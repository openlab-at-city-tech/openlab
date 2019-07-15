<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // don't access directly
};

/**
 * Load the Main Multipage Class 
 *
 * @since 0.6
 */
class Multipage {

	/** Option Overload *******************************************************/

	/**
	 * @var array Optional Overloads default options retrieved from get_option().
	 */
	public $options = array();

	/**
	 * Main Multipage Instance.
	 *
	 * @since 1.4
	 */
	public static function instance() {

		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new Multipage;
			$instance->constants();
			$instance->setup_globals();
			$instance->includes();
			$instance->frontend_init();
		}

		// Always return the instance
		return $instance;
	}

	private function __construct() { /* Do nothing */ }
	
	/**
	 * Bootstrap constants.
	 *
	 * @since 1.4
	 *
	 */
	private function constants() {

		// Path and URL
		if ( ! defined( 'MPP_PLUGIN_DIR' ) ) {
			define( 'MPP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		if ( ! defined( 'MPP_PLUGIN_URL' ) ) {
			define( 'MPP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
		
		// MPP pattern constant
		if ( ! defined( 'MPP_PATTERN' ) ) {
			define( 'MPP_PATTERN', '/\[nextpage[^\]]*\]/' );
		}
	}

	/**
	 * Component global variables.
	 *
	 * @since 1.4
	 *
	 */
	private function setup_globals() {

		/** Versions **********************************************************/

		$this->version    = '1.4.4';
		$this->db_version = 1000;
		
		/** Paths**************************************************************/

		$this->file           = constant( 'MPP_PLUGIN_DIR' ) . 'sgr-nextpage-titles.php';
		$this->basename       = basename( constant( 'MPP_PLUGIN_DIR' ) ) . '/sgr-nextpage-titles.php';
		$this->plugin_dir     = trailingslashit( constant( 'MPP_PLUGIN_DIR' ) );
		$this->plugin_url     = trailingslashit( constant( 'MPP_PLUGIN_URL' ) );
	}

	/**
	 * Include required files.
	 *
	 * @since 1.4
	 *
	 */
	private function includes() {
		// Setup the versions.
		$this->versions();
		
		// Load the admin.
		if ( is_admin() ) {
			add_action( 'init', 'mpp_admin' );
		}

		require( $this->plugin_dir . 'classes/class-mpp-admin.php'  );
		require( $this->plugin_dir . 'classes/class-mpp-shortcodes.php'  );
		//require( $this->plugin_dir . 'classes/class-mpp-table-of-contents-widget.php'  );	

		require( $this->plugin_dir . 'inc/mpp-admin.php'            );	
		require( $this->plugin_dir . 'inc/mpp-update.php'           );	
		require( $this->plugin_dir . 'inc/mpp-options.php'          );
		require( $this->plugin_dir . 'inc/mpp-functions.php'        );
		require( $this->plugin_dir . 'inc/mpp-shortcodes.php'       );
		//require( $this->plugin_dir . 'inc/mpp-widgets.php'        );
		require( $this->plugin_dir . 'inc/mpp-template.php'			);
		
		Multipage_Plugin_Shortcodes::init();
	}
	
	private function frontend_init() {
		add_action( 'wp', array( &$this, 'mpp_post' ) );
	}

	public function mpp_post() {
		global $post, $page, $subpages;
		
		// No need to process
		if ( is_feed() || is_404() || empty( $post ) )
			return;
		
		// Check if it's a Multipage Post
		$subpages = get_post_meta( $post->ID, '_mpp_data', true );
		if ( empty( $subpages ) )
			return;
		
		// Replace eventually existing variables on the first page.
		foreach ( $subpages as $link => $title ) {
			$subpages[ $link ] = str_replace( '%%intro%%', __( 'Intro', 'sgr-nextpage-titles' ), $title );
			break;
		}
		
		$_mpp_page_keys = array_keys( $subpages );
		$array_pos = $page > 1 ? $page -1 : 0;
		$current_index = $_mpp_page_keys[ $array_pos ];

		// Check wherever or not hide the standard WordPress pagination.
		if ( mpp_disable_standard_pagination() == true )
			add_filter( 'wp_link_pages_args', array( &$this, 'hide_standard_pagination' ) );

		// Check wherever or not hide comments.
		if ( $page != 0 && mpp_get_comments_on_page() == 'first-page' || $page != count( $_mpp_page_keys ) && mpp_get_comments_on_page() == 'last-page' )
			add_filter( 'comments_template', array( &$this, 'hide_comments' ) );

		// Initialize variables
		$content = $post->post_content;
		
		// Update $post Object with new data.
		$post->post_content = preg_replace( MPP_PATTERN, '<!--nextpage-->', $content );

		// Update Object with current post data.
		$this->page_title = $subpages[ $current_index ];
		$this->max_num_pages = ( substr_count( $post->post_content, '<!--nextpage-->' ) + 1 );
		$this->page = $page;
		
		// Change the document title only if it's not the first page.
		if ( $page > 1 ) {
			add_filter( 'wp_title',					array( &$this, 'mpp_the_title' ), mpp_get_rewrite_title_priority(), 1 );
			add_filter( 'pre_get_document_title',	array( &$this, 'mpp_the_title' ), mpp_get_rewrite_title_priority(), 1 );
			add_filter( 'document_title_parts',		array( &$this, 'mpp_document_title_parts' ), mpp_get_rewrite_title_priority(), 1 );
		}
		add_filter( 'the_content', 			array( &$this, 'mpp_the_content' ), mpp_get_rewrite_content_priority(), 1 );
		add_action( 'wp_enqueue_scripts',	array( &$this, 'enqueue_styles' ) );
	}
	
	/**
	 * Hide the standard pagination.
	 *
	 * @since 0.6
	 */
	public static function hide_standard_pagination( $args ) {
		$args['echo'] = 0;
		return $args;
	}
	
	/**
	 * Private method to align the active and database versions.
	 *
	 * @since 1.4
	 */
	private function versions() {
		// Get the possible DB versions.
		$versions               = array();
		$versions['1.3']		= null !== get_option( null, 'multipage' ) ? 999 : null; // If we found a multipage option then it's an update from the 1.3 version.
		
		// Remove empty array items
		$versions				= array_filter( $versions );
		$this->db_version_raw	= (int) ( !empty( $versions ) ) ? (int) max( $versions ) : 0;
	}

	/**
	 * Filter the document title for Multipage pages.
	 *
	 * @since 0.6
	 *
	 * @see wp_title()
	 *
	 * @param string $title       Original page title.
 	 * @return string the title modified by Multipage.
	 */
	public function mpp_the_title( $title ) {
		// Eventually, manipulate WordPress SEO by Yoast custom title.
		$title = str_replace( sprintf( __( 'Page %1$d of %2$d', 'wordpress-seo' ), $this->page, $this->max_num_pages ), $this->page_title, $title );
		
		// Manipulate Theme standard title (WP < 4.4).
		$title = str_replace( sprintf( __( 'Page %s', wp_get_theme()->get( 'TextDomain' ) ), $this->page ), $this->page_title, $title );

		return $title;
	}

	/**
	 * Filter the document title for Multipage pages.
	 *
	 * @since 1.4
	 *
	 * @param array $title The WordPress document title parts.
	 * @return array the title parts modified by Multipage.
	 */
	public function mpp_document_title_parts( $title ) {	
		// Change the page title.
		$title['page'] = $this->page_title;
		return $title;
	}

	/**
	 * Filter the WordPress post content for Multipage pages.
	 *
	 * @since 0.6
	 *
	 * @param string $content       Original page content.
 	 * @return string the content enhanced by Multipage.
	 */
	public function mpp_the_content( $content ) {
		// Table of contents should not be the only content in the post.
		if ( ! $content )
			return $content;
			
		// Only on single posts.
		if ( ! is_singular() )
			return $content;
		
		$page_title_template = apply_filters( 'mpp_page_title_template', '<h2>%s</h2>' );
		$page_title = mpp_hide_intro_title() == true && $this->page == 0 ? '' : sprintf( $page_title_template, $this->page_title );
		$toc_labels = mpp_get_toc_row_labels();

		switch ( $toc_labels ) {
			case 'page':
				$toc_row_separator = ': ';
				$toc_row_pagelink = __( 'Page %', 'sgr-nextpage-titles' );
				break;
			case 'hidden':
				$toc_row_separator = '';
				$toc_row_pagelink = '';
				break;
			default:
				$toc_row_separator = '. ';
				$toc_row_pagelink = '%';
				break;
		}
		
		if ( $continue_or_prev_next = mpp_get_continue_or_prev_next() !== 'hidden' ) {
			$navigation = mpp_link_pages( array(
				'before'				=> '<div class="mpp-page-link page-link">',
				'after'					=> '</div>',
				'continue_or_prev_next'	=> mpp_get_continue_or_prev_next()
			) );
		} else {
			$navigation = '';
		}
		
		// Get comments link
		if ( mpp_comments_toc_link() == true ) {
			switch ( mpp_get_comments_on_page() ) {
				case 'all':
					$comments_link = '<a href="#comments">';
					break;
				case 'first-page':
					$comments_link = _mpp_link_page( 1, 'comments' );
					break;
				case 'last-page':
					$comments_link = _mpp_link_page( $this->max_num_pages, 'comments' );
					break;
				default:
					$comments_link = '';
					break;
			}
		}

		$toc = mpp_toc( array(
			'hide_header'	=> mpp_hide_toc_header(),
			'comments'		=> isset( $comments_link ) ? $comments_link : '',
			'position'		=> mpp_get_toc_position(),
			'before'		=> '<nav class="mpp-toc toc"><ul>',
			'after'			=> '</ul></nav>',
			'separator'		=> $toc_row_separator,
			'pagelink'		=> $toc_row_pagelink,
		) );
		
		// Add the title
		$output = $page_title;
		
		// Add the table of content
		if ( mpp_get_toc_position() == 'bottom' ) {
			$output .= $content . $toc;
		} elseif ( mpp_get_toc_position() == 'hidden' || mpp_toc_only_on_the_first_page() && $this->page > 1 ) {
			$output .= $content;
		} else {
			$output .= $toc . $content;
		}
		
		// Add the page navigation
		$output .= $navigation;

		return $output;
	}
	
	/**
	 * Hide comments area.
	 *
	 * @since 1.0
	 */
	public function hide_comments() {
		// Return an empty file.
		return MPP_PLUGIN_DIR . '/index.php';
	}
	
	/**
	 * Styles applied to public-facing pages
	 *
	 * @since 0.6
	 * @uses enqueue_styles()
	 */
	public function enqueue_styles() {
		// LTR or RTL
		$file = is_rtl() ? 'css/multipage-rtl' : 'css/multipage';
		
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		
		// Add extension
		$file .= $suffix . '.css';
		
		// Check child theme
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
			$location = trailingslashit( get_stylesheet_directory_uri() );
			$handle   = get_stylesheet_directory() . '-multipage';

		// Check parent theme
		} elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
			$location = trailingslashit( get_template_directory_uri() );
			$handle   = get_template_directory() . '-multipage';

		// Multipage Plugin Default Style
		} else {
			$location = trailingslashit( MPP_PLUGIN_URL );
			$handle   = 'multipage';
		}

		// Enqueue the Multipage Plugin styling
		wp_enqueue_style( $handle, $location . $file, array(), $this->version, 'screen' );
	}
}