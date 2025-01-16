<?php  if ( ! defined( 'ABSPATH' ) ) exit;

class EPKB_Layouts_Setup {

	public function __construct() {

		// work only on theme-based template on article page; must be high priority
		add_filter( 'the_content', array( 'EPKB_Layouts_Setup', 'get_kb_page_output_hook' ), 99999 );

		add_shortcode( EPKB_KB_Handler::KB_MAIN_PAGE_SHORTCODE_NAME, array( 'EPKB_Layouts_Setup', 'output_kb_page_shortcode' ) );
	}

	/**
	 * ARTICLE PAGE: Current Theme / KB template  ==>  the_content()  ==> get article (this method)
	 *
	 * @param $content
	 * @param bool $the_content_filter_call
	 *
	 * @return string
	 */
	public static function get_kb_page_output_hook( $content, $the_content_filter_call = true ) {
		global $eckb_our_block_template;

		// for KB article, ignore if not post, is archive or current theme with any layout
		$post = empty( $GLOBALS['post'] ) ? '' : $GLOBALS['post'];
		if ( empty( $post ) || ! $post instanceof WP_Post || empty( $post->post_type ) || is_archive() || ! is_main_query() ) {
			return $content;
		}

		// continue if NOT KB Article URL
		if ( ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
			return $content;
		}

		// we have KB Article
		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		if ( is_wp_error( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		// initialize KB config to be accessible to templates
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		$is_kb_template = ! empty( $kb_config['templates_for_kb'] ) && $kb_config['templates_for_kb'] == 'kb_templates';

		// ignore the_content hook for our KB template as we call this directly
		// non-block Theme (KB or Current Theme) or block theme (KB template or custom template and single or custom block template)
		if ( $is_kb_template && $the_content_filter_call && ( ! EPKB_Utilities::is_block_theme() || isset( $eckb_our_block_template ) ) ) {
			return $content;
		}

		// only direct call from theme will output the KB article
		if ( ! self::is_right_content() ) {
			return $content;
		}

        // count article view for php method
        if ( $kb_config['article_views_counter_enable'] == 'on' && $kb_config['article_views_counter_method'] == 'php' ) {
            EPKB_Article_Count_Handler::maybe_increase_article_count( $post->ID );
        }

		// retrieve article content and features
		$content = EPKB_Articles_Setup::get_article_content_and_features( $post, $content, $kb_config );

		return $content;
	}

	/**
	 * MAIN PAGE: Output layout based on KB Shortcode.
	 *
	 * @param array $shortcode_attributes are shortcode attributes that the user added with the shortcode
	 * @return string of HTML output replacing the shortcode itself
	 */
	public static function output_kb_page_shortcode( $shortcode_attributes ) {

        $kb_config = self::get_kb_configuration( $shortcode_attributes );
		
		do_action( 'epkb_enqueue_scripts', $kb_config['id'] );

		global $eckb_kb_id, $post;

        // add page with KB shortcode to KB Main Pages if missing
        if ( empty( $eckb_kb_id ) ) {

            $kb_main_pages = $kb_config['kb_main_pages'];
	        $query_post = empty( $GLOBALS['wp_the_query'] ) ? null : $GLOBALS['wp_the_query']->get_queried_object();
			$post = empty( $query_post ) && ! empty( $post ) && $post instanceof WP_Post ? $post : $query_post;

	        // add missing post to main pages
	        if ( ! empty( $post->post_type ) && $post->post_type == 'page' && ! empty( $post->ID ) &&
		        is_array( $kb_main_pages ) && ! in_array( $post->ID, array_keys( $kb_main_pages ) ) && ! in_array( $post->post_status, array( 'inherit', 'trash', 'auto-draft' ) ) ) {
		        $post_id = $post->ID;
		        $kb_main_pages[$post_id] = empty( $post->post_title ) ? '[KB Main Page]' : $post->post_title;
		        epkb_get_instance()->kb_config_obj->set_value( $kb_config['id'], 'kb_main_pages', $kb_main_pages );
	        }
        }

		return self::output_main_page( $kb_config );
	}

	/**
	 * Show KB Main page i.e. knowledge-base/ url or KB Article Page in case of SBL.
	 *
	 * @param bool $is_ordering_wizard_on
	 * @param null $kb_config
	 * @param array $article_seq
	 * @param array $categories_seq
	 *
	 * @return string
	 */
	public static function output_main_page( $kb_config, $is_ordering_wizard_on=false, $article_seq=array(), $categories_seq=array() ) {

		// do not display Main Page of Archived KB
		if ( $kb_config['id'] !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_archived( $kb_config['status'] ) ) {
			return esc_html__( 'This knowledge base was archived.', 'echo-knowledge-base' );
		}

		// let layout class display the KB main page
		$layout = empty( $kb_config['kb_main_page_layout'] ) ? EPKB_Layout::BASIC_LAYOUT : $kb_config['kb_main_page_layout'];
		$layout =  self::is_elay_layout( $layout ) && ! EPKB_Utilities::is_elegant_layouts_enabled() ? EPKB_Layout::BASIC_LAYOUT : $layout;
		$is_modular = $kb_config['modular_main_page_toggle'] == 'on';

		// select core layout or default
		$handler = new EPKB_Layout_Basic();
		switch ( $layout ) {
			case EPKB_Layout::BASIC_LAYOUT:
			default:
				$handler = new EPKB_Layout_Basic();
				$layout = EPKB_Layout::BASIC_LAYOUT;    // default
				break;
			case EPKB_Layout::TABS_LAYOUT:
				$handler = new EPKB_Layout_Tabs();
				break;
			case EPKB_Layout::CATEGORIES_LAYOUT:
				$handler = new EPKB_Layout_Categories();
				break;
			case EPKB_Layout::CLASSIC_LAYOUT:           // the Modular-only layout cannot handle page itself - use default if Modular is 'off'
			case EPKB_Layout::DRILL_DOWN_LAYOUT:        // the Modular-only layout cannot handle page itself - use default if Modular is 'off'
				break;
			case EPKB_Layout::GRID_LAYOUT:              // Elegant Layouts layout - use default if the add-on is 'off'
			case EPKB_Layout::SIDEBAR_LAYOUT:           // Elegant Layouts layout - use default if the add-on is 'off'
				break;
		}

		// generate layout
		$layout_output = '';
		$handler = $is_modular ? new EPKB_Modular_Main_Page() : $handler;

		// handle Elegant layouts
		if ( self::is_elay_layout( $layout ) ) {

			// display only introduction text for Sidebar layout
			if ( $layout == EPKB_Layout::SIDEBAR_LAYOUT ) {

				$intro_text = apply_filters( 'eckb_main_page_sidebar_intro_text', '', $kb_config['id'] );
				$temp_article = new stdClass();
				$temp_article->ID = 0;
				$temp_article->post_title = esc_html__( 'Demo Article', 'echo-knowledge-base' );
				// Use 'post' for the filter as it is the same content as in the usual page/post
				$temp_article->post_content = wp_kses( $intro_text, EPKB_Utilities::get_extended_html_tags( true ) );
				$temp_article = new WP_Post( $temp_article );
				$kb_config['sidebar_welcome'] = 'on';
				$kb_config['article_content_enable_back_navigation'] = 'off';
                $kb_config['prev_next_navigation_enable'] = 'off';
                $kb_config['article_content_enable_rows'] = 'off';
				$layout_output = EPKB_Articles_Setup::get_article_content_and_features( $temp_article, $temp_article->post_content, $kb_config );

				if ( $handler instanceof EPKB_Modular_Main_Page ) {
					$handler->set_sidebar_layout_content( $layout_output );
					$layout_output = '';
				}

			} else if ( ! $is_modular ) {  // non-modular non-Sidebar Layout e.g. Grid Layout

				ob_start();
				apply_filters( 'epkb_' . strtolower( $layout ) . '_layout_output', $kb_config, $is_ordering_wizard_on, $article_seq, $categories_seq );
				$layout_output = ob_get_clean();
			}
		}

		// handle all non/modular core layouts and modular Grid/Sidebar layouts; excludes non-modular Grid and Sidebar layouts
		if ( empty( $layout_output ) ) {
			ob_start();
			$handler->display_non_modular_kb_main_page( $kb_config, $is_ordering_wizard_on, $article_seq, $categories_seq );
			$layout_output = ob_get_clean();
		}

		// a hook for user modifications
		if ( has_filter( 'epkb_output_main_page' ) ) {
			$layout_output = apply_filters( 'epkb_output_main_page', $layout_output, $kb_config, $article_seq, $categories_seq );
		}

		return $layout_output;
	}

	public static function is_elay_layout( $layout ) {
		return $layout == EPKB_Layout::GRID_LAYOUT || $layout == EPKB_Layout::SIDEBAR_LAYOUT;
	}

	/**
	 * Check that the layout exists and is properly configured
	 *
	 * @param array $shortcode_attributes
	 *
	 * @return array return the KB configuration
	 */
	private static function get_kb_configuration( $shortcode_attributes ) {

		$kb_id = empty( $shortcode_attributes['id'] ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $shortcode_attributes['id'] ;
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		//retrieve KB config
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		return $kb_config;
	}

	/**
	 * Is this content hook invocation from the theme to output the article?
	 * @return bool
	 */
	private static function is_right_content() {

		if ( ! EPKB_Core_Utilities::is_kb_flag_set( 'epkb_the_content_fix' ) ) {
			return true;
		}

		// check backtrace
		$traces = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
		unset( $traces[0], $traces[1] ); // We don't need the last 2 calls: this function + call_user_func_array (or apply_filters on PHP7+)

		$theme_root = get_template_directory();
		$parent_theme_root = get_parent_theme_file_path();

		$blacklist = [
			$theme_root . '/footer.php',
			$theme_root . '/header.php',
			$parent_theme_root . '/footer.php',
			$parent_theme_root . '/header.php',
			'js_composer'
		];

		// check if the backtrace contains the theme header/footer files that we want to ignore
		foreach ( $traces as $trace ) {
			foreach ( $blacklist as $v ) {
				$file = ( false === strpos( $v, '\\' ) ) ? $v : str_replace( '/', '\\', $v );

				if ( ( isset( $trace['file'] ) && false !== strpos( $trace['file'], $file ) ) ) {
					return false;
				}
			}
		}

		return true;
	}
}
