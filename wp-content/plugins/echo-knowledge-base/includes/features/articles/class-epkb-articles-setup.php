<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle article front-end setup
 *
 */
class EPKB_Articles_Setup {

	private $cached_comments_flag;
	static $styles = '';

	public function __construct() {
		add_action( 'wp_ajax_epkb_update_the_content_flag', array( $this, 'epkb_update_the_content_flag' ) );
		add_filter( 'comments_open', array( $this, 'setup_comments'), 1, 2 );
	}

    /**
     * Return single article content surrounded by features like breadcrumb and tags.
     *
     * NOTE: Assumes shortcodes already ran.
     *
     * @param $article
     * @param $content
     * @param $kb_config - front end or back end temporary KB config
     * @return string
     */
	public static function get_article_content_and_features( $article, $content, $kb_config ) {

		global $epkb_password_checked, $eckb_is_kb_main_page;

		$is_modular = $kb_config['modular_main_page_toggle'] == 'on';

		if ( empty( $epkb_password_checked ) && post_password_required() ) {
			return get_the_password_form();
		}

		// if global post is empty initialize it
		if ( empty( $GLOBALS['post'] ) ) {
		   $GLOBALS['post'] = $article;
		}

		// if necessary get KB configuration
		if ( empty( $kb_config ) ) {
		   $kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $article->post_type );
		   if ( is_wp_error($kb_id) ) {
		       $kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		   }

		   // initialize KB config to be accessible to templates
		   $kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		}

		// setup article structure
		self::setup_article_hooks( $kb_config );

		// custom hooks if needed
		$article_page_container_classes = apply_filters( 'eckb-article-page-container-classes', array(), $kb_config['id'], $kb_config );  // used for old Widgets KB Sidebar
		$article_page_container_classes = isset( $article_page_container_classes ) && is_array( $article_page_container_classes ) ? $article_page_container_classes : array();

		$article_page_container_classes[] = 'eckb-article-page-content-counter';

		if ( $kb_config['article-left-sidebar-match'] == 'on' ) {
			$article_page_container_classes[] = 'eckb-article-page--L-sidebar-to-content';
		}
		
		if ( $kb_config['article-right-sidebar-match'] == 'on' ) {
			$article_page_container_classes[] = 'eckb-article-page--R-sidebar-to-content';
		}

		$v2_suffix = $kb_config['article_content_enable_rows'] == 'on' ? '-v2' : '';

		ob_start();

		if ( ! empty( $kb_config['theme_name'] ) ) {
			$article_page_container_classes[] = 'eckb-theme-' . $kb_config['theme_name'];
		}

		// add theme name to Div for specific targeting
		$activeWPTheme = EPKB_Utilities::get_active_theme_classes( 'ap' );
		
		$article_seq_no = empty( $_REQUEST['seq_no'] ) ? '' : EPKB_Utilities::sanitize_int( wp_unslash( sanitize_key( $_REQUEST['seq_no'] ) ) );
		$article_seq_no = empty( $article_seq_no ) ? '' : ' data-kb_article_seq_no=' . $article_seq_no;

		$mobile_breakpoint = $kb_config['article-mobile-break-point-v2'];
		if ( is_numeric( $mobile_breakpoint ) && ! empty( $_REQUEST['epkb-editor-page-loaded'] ) ) {
			$mobile_breakpoint -= 400;
		}

		/**
		 * For the Sidebar Layout we use article page HTML structure on the Main Page as well. The Sidebar Layout has width controls related to article page.
		 * For Modular page, the Article Page width is not required since sidebar layout is nested inside the module row.
		 */
		$article_container_structure_version = 'eckb-article-page-container-v2';
		if ( $is_modular && $eckb_is_kb_main_page ) {
			$article_container_structure_version = 'epkb-ml-sidebar-layout-inner';
		} ?>

		<div id="<?php echo esc_attr( $article_container_structure_version ); ?>" class="<?php echo esc_attr( implode(" ", $article_page_container_classes) . ' ' . $activeWPTheme ); ?> " data-mobile_breakpoint="<?php echo esc_attr( $mobile_breakpoint ); ?>">    <?php

		   self::article_section( 'eckb-article-header', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) ); ?>

			<div id="eckb-article-body">  <?php

		        self::article_section( 'eckb-article-left-sidebar', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) ); ?>

		        <article id="eckb-article-content" data-article-id="<?php echo esc_attr( $article->ID ); ?>" <?php echo esc_attr( $article_seq_no ); ?>>                        <?php

					self::article_section( 'eckb-article-content-header' . $v2_suffix, array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) );
					self::article_section( 'eckb-article-content-body', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article, 'content' => $content ) );
					self::article_section( 'eckb-article-content-footer', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) );                        ?>

		        </article><!-- /#eckb-article-content -->     <?php

		        self::article_section( 'eckb-article-right-sidebar', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) ); ?>

			</div><!-- /#eckb-article-body -->              <?php

			self::article_section( 'eckb-article-footer', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) ); ?>

		</div><!-- /#eckb-article-page-container-v2 -->

		<style id="eckb-article-styles" type="text/css"><?php echo EPKB_Utilities::minify_css( self::$styles ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?></style>   <?php

		$article_content = ob_get_clean();

		return str_replace( ']]>', ']]&gt;', $article_content );
	}

	/**
	 * REGISTER all article hooks we need
	 *
	 * @param $kb_config
	 */
	private static function setup_article_hooks( $kb_config ) {

		// A. ARTICLE PAGE HEADER
		add_action( 'eckb-article-header', array( 'EPKB_Articles_Setup', 'search_box' ) );


		// B. ARTICLE CONTENT - old meta OR header rows
		if ( $kb_config['article_content_enable_rows'] == 'on' ) {
			add_action( 'eckb-article-content-header-v2', array('EPKB_Articles_Setup', 'article_content_header'), 9 );
		} else {
			add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'article_title'), 9 );
			add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'meta_data_header'), 9 );
			add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'breadcrumb'), 9 );
			add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'back_navigation'), 9 );
		}


		// C. SIDEBARS + ARTICLE CONTENT BODY
		add_action( 'eckb-article-content-body', array('EPKB_Articles_Setup', 'article_content_body' ), 10 );

		$sidebar_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $kb_config['article_sidebar_component_priority'] );

		// Widgets KB sidebar
		if ( $sidebar_priority['kb_sidebar_left'] ) {
			add_action( 'eckb-article-left-sidebar', array('EPKB_Articles_Setup', 'display_kb_widgets_sidebar'), 10 * $sidebar_priority['kb_sidebar_left'] );
		}

		if ( $sidebar_priority['kb_sidebar_right'] ) {
			add_action( 'eckb-article-right-sidebar', array('EPKB_Articles_Setup', 'display_kb_widgets_sidebar'), 10 * $sidebar_priority['kb_sidebar_right'] );
		}

		// Article Sidebar (KB/Elegant Layouts)
		if ( EPKB_Core_Utilities::get_nav_sidebar_type( $kb_config, 'left' ) != 'eckb-nav-sidebar-none' ) {
			add_action( 'eckb-article-left-sidebar', array('EPKB_Articles_Setup', 'display_nav_sidebar_left'), 10 * EPKB_Core_Utilities::get_nav_sidebar_priority( $kb_config, 'left' ) );
		} else if ( EPKB_Core_Utilities::get_nav_sidebar_type( $kb_config, 'right' ) != 'eckb-nav-sidebar-none' ) {
			add_action( 'eckb-article-right-sidebar', array('EPKB_Articles_Setup', 'display_nav_sidebar_right'), 10 * EPKB_Core_Utilities::get_nav_sidebar_priority( $kb_config, 'right' ) );
		}

		// We can place the TOC into their appropriate HTML container ( Left Sidebar , Main Content , Right Sidebar )

		// check TOC
		if ( $sidebar_priority['toc_left'] ) {
			add_action( 'eckb-article-left-sidebar', array('EPKB_Articles_Setup', 'table_of_content'), 10 * $sidebar_priority['toc_left'] );
		}

		$v2_suffix = $kb_config['article_content_enable_rows'] == 'on' ? '-v2' : '';
		if ( $sidebar_priority['toc_content'] ) {
			add_action( 'eckb-article-content-header' . $v2_suffix, array('EPKB_Articles_Setup', 'table_of_content'), 10 * $sidebar_priority['toc_content'] );
		}

		if ( $sidebar_priority['toc_right'] ) {
			add_action( 'eckb-article-right-sidebar', array('EPKB_Articles_Setup', 'table_of_content'), 10 * $sidebar_priority['toc_right'] );
		}


		// D. ARTICLE CONTENT FOOTER
		add_action( 'eckb-article-content-footer', array('EPKB_Articles_Setup', 'meta_data_footer'), 10 );
		add_action( 'eckb-article-content-footer', array('EPKB_Articles_Setup', 'tags'), 99 );
		add_action( 'eckb-article-content-footer', array('EPKB_Articles_Setup', 'prev_next_navigation'), 99 );
		add_action( 'eckb-article-content-footer', array('EPKB_Articles_Setup', 'comments'), 99 );
	}


	/***********************   A. ARTICLE PAGE HEADER   *********************/

	/**
	 * Search Box for Article Page or Sidebar Main Page (non/modular)
	 *
	 * @param $args
	 */
	public static function search_box( $args ) {

		$is_modular_page = $args['config']['modular_main_page_toggle'] == 'on';
		$is_sidebar_layout = $args['config']['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT;
		$is_kb_main_page_search_off = $is_modular_page ? ! EPKB_Core_Utilities::is_module_present( $args['config'], 'search' ) : $args['config']['search_layout'] == 'epkb-search-form-0';

		// SEARCH BOX OFF: no search box if Article Page search is off except Sidebar Layout that behaves like the Main Page
		$is_article_search_off = $is_sidebar_layout ? $is_kb_main_page_search_off : $args['config']['article_search_toggle'] == 'off';
		if ( $is_article_search_off ) {
			return;
		}

		// The Sidebar layout on Modular KB Main Page will not output search box as Modular Search will be shown instead
		if ( EPKB_Utilities::is_kb_main_page() && $is_sidebar_layout && $is_modular_page ) {
			return;
		}

		EPKB_KB_Search::get_search_form_output( $args['config'] );
	}


	/***********************   B. ARTICLE CONTENT HEADER  *********************/

	public static function article_content_header( $args ) {

		$kb_config = $args['config'];

		$rows_setup = [ [], [], [], [], [] ];

		// article title
		if ( $kb_config['article_content_enable_article_title'] == 'on' ) {
			$rows_setup[$kb_config['article_title_row']][$kb_config['article_title_alignment']][$kb_config['article_title_sequence']*10] = 'title';
		}

		// back navigation
		if ( $kb_config['article_content_enable_back_navigation'] == 'on' ) {
		   $rows_setup[$kb_config['back_navigation_row']][$kb_config['back_navigation_alignment']][$kb_config['back_navigation_sequence']*10+1] = 'navigation';
		}

		// author
		if ( $kb_config['article_content_enable_author'] == 'on' ) {
			$rows_setup[$kb_config['author_row']][$kb_config['author_alignment']][$kb_config['author_sequence']*10+2] = 'author';
		}

		// created date
		if ( $kb_config['article_content_enable_created_date'] == 'on' ) {
			$rows_setup[$kb_config['created_date_row']][$kb_config['created_date_alignment']][$kb_config['created_date_sequence']*10+3] = 'created_date';
		}

		// last updated date
		if ( $kb_config['article_content_enable_last_updated_date'] == 'on' ) {
			$rows_setup[$kb_config['last_updated_date_row']][$kb_config['last_updated_date_alignment']][$kb_config['last_updated_date_sequence']*10+4] = 'last_updated_date';
		}

		// breadcrumb
		if ( $kb_config['breadcrumb_enable'] == 'on' && $kb_config['breadcrumb_row'] != 'page_bar' ) {
			$rows_setup[$kb_config['breadcrumb_row']][$kb_config['breadcrumb_alignment']][$kb_config['breadcrumb_sequence']*10+5] = 'breadcrumb';
		}

		// article views counter
		if ( $kb_config['article_content_enable_views_counter'] == 'on' ) {
			$rows_setup[$kb_config['article_views_counter_row']][$kb_config['article_views_counter_alignment']][$kb_config['article_views_counter_sequence']*10+6] = 'article_views_counter';
		}

		// print button
		if ( $kb_config['print_button_enable'] == 'on' ) {
		   $rows_setup[$kb_config['article_content_toolbar_row']][$kb_config['article_content_toolbar_alignment']][$kb_config['article_content_toolbar_sequence']*10+7] = 'print_button';
		}

		// add config from add-ons if needed
		if ( EPKB_Utilities::is_article_rating_enabled() ) {
			$add_ons_config = apply_filters( 'eckb_kb_config', $kb_config );
			if ( empty($add_ons_config) || is_wp_error($add_ons_config) ) {
				$add_ons_config = [];
			}
		}

		// article rating element
		if ( ! empty($add_ons_config) && isset($add_ons_config['rating_element_row']) && isset($add_ons_config['article_content_enable_rating_element']) &&
		   $add_ons_config['article_content_enable_rating_element'] == 'on' ) {
			$rows_setup[$add_ons_config['rating_element_row']][$add_ons_config['rating_element_alignment']][$add_ons_config['rating_element_sequence']*10+8] = 'rating-element';
		}

		// article rating statistics
		if ( ! empty($add_ons_config) && isset($add_ons_config['rating_statistics_row']) && isset($add_ons_config['article_content_enable_rating_stats']) &&
		    $add_ons_config['article_content_enable_rating_stats'] == 'on' ) {
			$rows_setup[$add_ons_config['rating_statistics_row']][$add_ons_config['rating_statistics_alignment']][$add_ons_config['rating_statistics_sequence']*10+9] = 'rating-statistics';
		}

		$mobile_breakpoint = $kb_config['article-mobile-break-point-v2'];
		if ( is_numeric( $mobile_breakpoint ) && ! empty( $_REQUEST['epkb-editor-page-loaded'] ) ) {
			$mobile_breakpoint -= 400;
		}

		// When the page gets to the size of the user defined Mobile breakpoint set the rows to columns to allows space.
		self::$styles .= '@media only screen and (min-width: ' . $mobile_breakpoint . 'px){
					        #eckb-article-page-container-v2 #eckb-article-body #eckb-article-content #eckb-article-content-header-v2 #eckb-article-content-header-row-1,
					        #eckb-article-page-container-v2 #eckb-article-body #eckb-article-content #eckb-article-content-header-v2 #eckb-article-content-header-row-2,
					        #eckb-article-page-container-v2 #eckb-article-body #eckb-article-content #eckb-article-content-header-v2 #eckb-article-content-header-row-3,
					        #eckb-article-page-container-v2 #eckb-article-body #eckb-article-content #eckb-article-content-header-v2 #eckb-article-content-header-row-4,
					        #eckb-article-page-container-v2 #eckb-article-body #eckb-article-content #eckb-article-content-header-v2 #eckb-article-content-header-row-5 {
					            flex-direction:row;
					        }
						}';

		// display up to 5 rows
		for ( $row = 1; $row < 6; $row++ ) {

			if ( empty( $rows_setup[$row] ) ) {
				continue;
			}

			self::$styles .= '#eckb-article-content-header-row-'.$row.'{
					        margin-bottom: ' . $kb_config['article_content_enable_rows_' . $row . '_gap'].'px;
					      }
				
					      #eckb-article-content-header-row-'.$row.' .eckb-article-content-header-row-left-group, 
					      #eckb-article-content-header-row-'.$row.' .eckb-article-content-header-row-right-group {
					        align-items: ' . $kb_config['article_content_enable_rows_' . $row . '_alignment'].'; 
					      }';

			echo '<div id="eckb-article-content-header-row-' . esc_attr( $row ) . '">';

			// LEFT alignment
			$sequences = isset($rows_setup[$row]['left']) ? $rows_setup[$row]['left'] : [];
			ksort( $sequences );
			if ( ! empty($sequences) ) {
				echo '<div class="eckb-article-content-header-row-left-group">';
				foreach ( $sequences as $sequence => $value ) {
					self::article_content_header_feature( $args, $rows_setup[$row]['left'][$sequence] );
				}
				echo '</div>';
			}

			// help user to see the row in the Editor
			if ( EPKB_Utilities::get( 'epkb-editor-page-loaded' ) == '1' ) {
				$alignment = self::is_left_sidebar_on( $kb_config ) ? 'eckb-editor-row--left' : ( self::is_right_sidebar_on( $kb_config ) ? 'eckb-editor-row--right' : 'eckb-editor-row--center' );
				echo '<span class="eckb-editor-row-tag ' . esc_attr( $alignment ) . '">Row #' . esc_attr( $row ) . '</span>';
			}

			// RIGHT alignment
			$sequences = isset($rows_setup[$row]['right']) ? $rows_setup[$row]['right'] : [];
			ksort ($sequences);
			if ( ! empty($sequences) ) {
				echo '<div class="eckb-article-content-header-row-right-group">';
				foreach ( $sequences as $sequence => $value ) {
					self::article_content_header_feature( $args, $rows_setup[$row]['right'][$sequence] );
				}
				echo '</div>';
			}

			echo '</div>';
		}
	}

	private static function article_content_header_feature( $args, $feature ) {

		switch( $feature ) {
			case 'title':
				self::article_title( $args );
				break;
			case 'navigation':
				self::back_navigation_new( $args );
				break;
			case 'author':
				$post_author = get_the_author_meta( 'display_name', $args['article']->post_author );
				self::meta_data_header_feature( $args, $feature, 'epkbfa-user', 'author_icon_on', 'author_text', $post_author );
				break;
			case 'created_date':
				$date = sprintf(
					'<time class="entry-date" datetime="%1$s">%2$s</time>',
					esc_attr( get_the_date( DATE_W3C ) ),
					esc_html( get_the_date() )
				);
				self::meta_data_header_feature( $args, $feature, 'epkbfa-calendar', 'created_date_icon_on', 'created_on_text', $date );
				break;
			case 'last_updated_date':
				 $date = sprintf(
					 '<time class="entry-date" datetime="%1$s">%2$s</time>',
					 esc_attr( get_the_modified_date( DATE_W3C ) ),
					 esc_html( get_the_modified_date() )
				 );
				 self::meta_data_header_feature( $args, $feature, 'epkbfa-pencil-square-o', 'last_updated_date_icon_on', 'last_updated_on_text', $date );
				break;
			case 'breadcrumb':
				self::breadcrumb_new( $args, 'eckb-article-content-breadcrumb-container' );
				break;
			case 'article_views_counter':
				if ( $args['config']['article_views_counter_enable'] == 'on' && $args['config']['article_content_enable_views_counter'] == 'on' ) {
					$views = EPKB_Article_Count_Handler::get_article_views_counter_frontend();
					self::meta_data_header_feature( $args, 'article-views-counter', 'epkbfa-signal', 'article_views_counter_icon_on', 'article_views_counter_text', $views );
				}
				break;
			case 'print_button':
			   self::toolbar( $args, 'article_content' );
				break;
			case 'rating-element':
				do_action( 'eckb-article-content-header-rating-element', $args );
				break;
		    case 'rating-statistics':
				$args['output_location'] = 'top';
				do_action( 'eckb-article-content-header-rating-statistics', $args );
			  break;
			default:
				break;
		}
	}

	// META DATA FEATURE
	private static function meta_data_header_feature( $args, $feature, $icon, $config_icon, $config_text, $value ) {
		$feature = str_replace('_', '-', $feature);     ?>

		<div class="eckb-article-content-<?php echo esc_attr( $feature ); ?>-container">		<?php
			if ( 'on' == $args['config'][$config_icon] ) {
				echo '<span class="eckb-meta-data-feature-icon epkbfa ' . esc_attr( $icon ) . '"></span>';
			}

			if ( $args['config'][$config_text] ) {
				echo '<span class="eckb-meta-data-feature-text">' . esc_html( $args['config'][$config_text] ) . '</span>';
			}

			echo '<span class="eckb-meta-data-feature-value">' . wp_kses_post( $value ) . '</span>';		   ?>
		</div> <?php
	}

	// ARTICLE TITLE
	public static function article_title( $args ) {
		global $eckb_is_kb_main_page;

		// Sidebar layout on Main Page should not have metadata
		if ( $args['config']['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT && $eckb_is_kb_main_page ) {
			return;
		}

		if ( $args['config']['article_content_enable_article_title'] != 'on' ) {
			return;
		}

		$article_title_typography_styles = EPKB_Utilities::get_typography_config( $args['config']['article_title_typography'] );
		
		// Both Versions
		self::$styles .= '#eckb-article-content .eckb-article-title {
						' . $article_title_typography_styles . '
						}';

		echo $args['config']['article_content_enable_rows'] == 'off' ? '' : '<div id="eckb-article-content-title-container">';
		echo '<h1 class="eckb-article-title">' . esc_html( get_the_title( $args['article'] ) ) . '</h1>';
		echo $args['config']['article_content_enable_rows'] == 'off' ? '' : '</div>';
	}

	// BACK NAVIGATION - for article content header and page bar
	private static function back_navigation_new( $args ) {
	   echo '<div id="eckb-article-back-navigation-container">';
	   EPKB_Templates::get_template_part( 'feature', 'navigation-back', $args['config'], $args['article'] );
	   echo '</div>';
   }

	// BACK NAVIGATION - for old article content header
	public static function back_navigation( $args ) {
		if ( $args['config'][ 'article_content_enable_back_navigation'] == 'on' ) {
			EPKB_Templates::get_template_part( 'feature', 'navigation-back', $args['config'], $args['article'] );
		}
	}

	// BREADCRUMB - for page bar and article content header
	private static function breadcrumb_new( $args, $location='' ) {
		global $eckb_is_kb_main_page;

		// Sidebar layout on the Main Page should not have breadcrumb
		if ( $args['config']['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT && $eckb_is_kb_main_page) {
			return;
		}

	   $args['config']['use_old_margin_bottom'] = false;

		echo '<div id="' . esc_attr( $location ) . '">';
		EPKB_Templates::get_template_part( 'feature', 'breadcrumb', $args['config'], $args['article'] );
		echo '</div>';
	}
	
	// BREADCRUMB - old breadcrumb
	public static function breadcrumb( $args ) {
		global $eckb_is_kb_main_page;

		// Sidebar layout on the Main Page should not have breadcrumb
		if ( $args['config']['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT && $eckb_is_kb_main_page ) {
			return;
		}

		$args['config']['use_old_margin_bottom'] = true;

		if ( $args['config'][ 'breadcrumb_enable'] == 'on' ) {
			EPKB_Templates::get_template_part( 'feature', 'breadcrumb', $args['config'], $args['article'] );
		}
	}

	// PAGE BAR and ARTICLE CONTENT TOOLBARs
	private static function toolbar( $args, $location ) {
		$kb_config = $args['config'];

		$parent_container = 'eckb-' . str_replace('_', '-', $location) . '-toolbar-button-container';
		self::$styles .= '
			.' . $parent_container . ' {
					background-color: ' . $kb_config[$location . '_toolbar_button_background'] . ';
					padding: '
	             . $kb_config[$location . '_toolbar_button_padding_top'] . 'px '
	             . $kb_config[$location . '_toolbar_button_padding_right'] . 'px '
	             . $kb_config[$location . '_toolbar_button_padding_bottom'] . 'px '
	             . $kb_config[$location . '_toolbar_button_padding_left'] . 'px;
					margin: '
	             . $kb_config[$location . '_toolbar_button_margin_top'] . 'px '
	             . $kb_config[$location . '_toolbar_button_margin_right'] . 'px '
	             . $kb_config[$location . '_toolbar_button_margin_bottom'] . 'px '
	             . $kb_config[$location . '_toolbar_button_margin_left'] . 'px;
					border-radius: ' . $kb_config[$location . '_toolbar_border_radius'] . 'px;
					border-width: ' . $kb_config[$location . '_toolbar_border_width'] . 'px;	
					border-color: ' . $kb_config[$location . '_toolbar_border_color'] . ';	
					border-style: solid;
			}
			.' . $parent_container . ' .eckb-toolbar-button-text{
					color: ' . $kb_config[$location . '_toolbar_text_color'] . ';
					font-size: ' . $kb_config[$location . '_toolbar_text_size'] . 'px;	
			}
			.' . $parent_container . ' .eckb-toolbar-button-icon{
					color: ' . $kb_config[$location . '_toolbar_icon_color'] . ';
					font-size: ' . $kb_config[$location . '_toolbar_icon_size'] . 'px;	
			}
			.' . $parent_container . ':hover {
					background-color: ' . $kb_config[$location . '_toolbar_button_background_hover'] . ';
			}
			.' . $parent_container . ':hover .eckb-toolbar-button-text{
					color: ' . $kb_config[$location . '_toolbar_text_hover_color'] . ';
			}
			.' . $parent_container . ':hover .eckb-toolbar-button-icon{
					color: ' . $kb_config[$location . '_toolbar_icon_hover_color'] . ';
			}';

		echo '<div id="eckb-' . esc_attr( str_replace('_', '-', $location) ) . '-toolbar-container">';
			if ( $args['config']['print_button_enable'] == 'on' ) {
				self::toolbar_button( $args, $location, 'print', 'epkbfa-print' );
			}
		echo '</div>';
	}

	// TOOLBAR BUTTON
	private static function toolbar_button( $args, $location, $feature, $icon ) {
		$kb_config = $args['config'];

		$format = $kb_config[$location . '_toolbar_button_format'];   ?>

		<div class="eckb-<?php echo esc_attr( str_replace('_', '-', $location) ); ?>-toolbar-button-container">
			<span class="eckb-<?php echo esc_attr( $feature ); ?>-button-container">			<?php
				echo $format == 'icon' || $format == 'icon_text' ? '<span class="eckb-toolbar-button-icon epkbfa ' . esc_attr( $icon ) . '"></span>' : '';
				echo $format == 'text' || $format == 'text_icon' ? '<span class="eckb-toolbar-button-text">' . esc_html( $kb_config[$feature . '_button_text'] ) . '</span>' : '';
			   echo $format == 'icon_text' ? '<span class="eckb-toolbar-button-text">' . esc_html( $kb_config[$feature . '_button_text'] ) . '</span>' : '';
			   echo $format == 'text_icon' ? '<span class="eckb-toolbar-button-icon epkbfa ' . esc_attr( $icon ) . '"></span>' : '';				?>
			</span>
		</div> <?php
	}


	/***********************   C. ARTICLE CONTENT BODY   *********************/

	public static function article_content_body( $args ) {
		global $multipage;

		do_action( 'eckb-article-before-content', $args );
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['content'];
		do_action( 'eckb-article-after-content', $args );

		// support for paged articles (with page break)
		if ( $multipage ) {
			wp_link_pages( array(
				'before'           => '<div class="eckb-article-paged-navigation">',
				'after'            => '</div>',
				'separator'        => ' | ',
				'nextpagelink'     => esc_html__( 'Next Section', 'echo-knowledge-base' ),
				'previouspagelink' => esc_html__( 'Previous Section', 'echo-knowledge-base' ),
				'next_or_number'   => 'next'
			) );
		}
	}


	/***********************   D. ARTICLE CONTENT FOOTER   *********************/

	// TAGS
	public static function tags( $args ) {
		EPKB_Templates::get_template_part( 'feature', 'tags', $args['config'], $args['article'] );
	}

	/**
	 * OUTPUT PREV/NEXT buttons
	 * @param $args
	 */
	public static function prev_next_navigation( $args ) {
		global $eckb_kb_id, $post;

		if ( empty( $post ) or ! isset( $post->ID ) or empty( $eckb_kb_id ) ) {
			return;
		}

		$post_id = $post->ID;
		$kb_id = $eckb_kb_id;
		$kb_config = $args['config'];

		if ( empty( $kb_config['prev_next_navigation_enable'] ) || $kb_config['prev_next_navigation_enable'] != 'on' ) {
			return;
		}

		$styles = '
			#eckb-article-content-footer .epkb-article-navigation-container a {
				background-color:   ' . $kb_config['prev_next_navigation_bg_color'] . ';
				color:              ' . $kb_config['prev_next_navigation_text_color'] . ';
			}
			#eckb-article-content-footer .epkb-article-navigation-container a:hover {
				background-color:   ' . $kb_config['prev_next_navigation_hover_bg_color'] . ';
				color:              ' . $kb_config['prev_next_navigation_hover_text_color'] . ';
			}
		';

		$prev_navigation_text = empty( $kb_config['prev_navigation_text'] ) ? esc_html__( 'Previous', 'echo-knowledge-base' ) : $kb_config['prev_navigation_text'];
		$next_navigation_text = empty( $kb_config['next_navigation_text'] ) ? esc_html__( 'Next', 'echo-knowledge-base' ) : $kb_config['next_navigation_text'];

		$demo_prev_link =
            '<a href="#" rel="prev">
                <span class="epkb-article-navigation__label">
                    <span class="epkb-article-navigation__label__previous__icon epkbfa epkbfa-caret-left"></span>
                    ' . esc_html( $prev_navigation_text ) . '
                </span>
                <span class="epkb-article-navigation-article__title">
                    <span class="epkb-article-navigation__previous__icon ep_font_icon_document"></span>
                    ' . esc_html__( 'Previous Article', 'echo-knowledge-base' ) . '
                </span>
            </a>';
		$demo_next_link =
            '<a href="#" rel="next">
                <span class="epkb-article-navigation__label">
                    ' . esc_html( $next_navigation_text ) . '
                    <span class="epkb-article-navigation__label__next__icon  epkbfa epkbfa-caret-right"></span>
                </span>
                <span class="epkb-article-navigation-article__title">
                    <span class="epkb-article-navigation__next__icon ep_font_icon_document"></span>
                    ' . esc_html__( 'Next Article', 'echo-knowledge-base' ) . '
                </span>
            </a>';

		// Condition To set Demo for admin wizards
		if ( self::is_configuring_article() ) {
			self::prev_next_navigation_html( $styles, $demo_prev_link, $demo_next_link );
			return;
		}

		// get last category id
		$breadcrumb_tree = EPKB_Templates_Various::get_article_breadcrumb( $kb_config, $post_id );
		if ( empty( $breadcrumb_tree ) ) {
			return;
		}

		end( $breadcrumb_tree );
		$category_id = key( $breadcrumb_tree );
		if ( empty( $category_id ) ) {
			return;
		}

		/* Fetch all article Ids in sequence */

		// category and article sequence
		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );
		$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );

		if ( empty( $category_seq_data ) or empty( $articles_seq_data ) ) {
			return;
		}

		// for WPML filter categories and articles given active language
		if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
			$category_seq_data = EPKB_WPML::apply_category_language_filter( $category_seq_data );
			$articles_seq_data = EPKB_WPML::apply_article_language_filter( $articles_seq_data );
		}

		// articles ids considering category and article sequences
		$articles_general_seq_ids = self::get_article_general_seq_ids( $category_seq_data, $articles_seq_data, $kb_config );

		/* Fetch all article Ids in sequence End*/

		$repeat_current_post_keys = array_keys( $articles_general_seq_ids, $post_id );
		if ( empty( $repeat_current_post_keys ) ) {
			return;
		}
		$current_post_seq_no = (int)EPKB_Utilities::get( 'seq_no', 0 );
		$current_post_seq_index = $current_post_seq_no > 0 ? $current_post_seq_no - 1 : $current_post_seq_no;
		$current_post_key = $repeat_current_post_keys[ $current_post_seq_index ];

		// search previous post id depends on user access
		$index = $current_post_key - 1;
		$prev_post_id = 0;
		while ( $index >= 0 ) {
			if ( ! empty( $articles_general_seq_ids[$index] ) && EPKB_Utilities::is_article_allowed_for_current_user( $articles_general_seq_ids[$index] ) ) {
				$prev_post_id = $articles_general_seq_ids[$index];
				break;
			}
			$index--;
		}

		// search next post id depends on user access
		$index = $current_post_key + 1;
		$next_post_id = 0;
		while ( $index < count( $articles_general_seq_ids ) ) {
			if ( ! empty( $articles_general_seq_ids[$index] ) && EPKB_Utilities::is_article_allowed_for_current_user( $articles_general_seq_ids[$index] ) ) {
				$next_post_id = $articles_general_seq_ids[$index];
				break;
			}
			$index++;
		}

		/*** Code to get sequence no **/
		$category_seq_array = self::epkb_get_array_keys_multiarray( $category_seq_data, $kb_config );

		$repeat_cat_id = array(); // Array of articles id with seq_no
		if ( ! empty( $category_seq_array ) ) {
			$repeat_id = array();

			foreach( $category_seq_array as $cat_seq_id ) {

				if ( ! empty( $articles_seq_data[$cat_seq_id] ) ) {
					foreach( $articles_seq_data[$cat_seq_id] as $key => $value ) {
						if ( $key > 1 ) {
							$repeat_id[$key] = isset( $repeat_id[$key] ) ? $repeat_id[$key] + 1 : 1;
							$repeat_cat_id[$key][$cat_seq_id] = $repeat_id[$key];
						}
					}
				}
			}
		}
		/*** Code to get sequence no END **/

		// output the PREV/NEXT buttons

		$prev_link = '';
		if ( ! empty( $prev_post_id ) ) {

			$prev_seq_no = isset( $repeat_cat_id[$prev_post_id][$category_id] ) ? $repeat_cat_id[$prev_post_id][$category_id] : 1;
			$prev_link = get_permalink( $prev_post_id );
			$prev_link = empty( $prev_seq_no ) || $prev_seq_no < 2 ? $prev_link : add_query_arg( 'seq_no', $prev_seq_no, $prev_link );

			// linked articles have their own icon
			$article_title_icon = 'ep_font_icon_document';
			if ( has_filter( 'eckb_article_icon_filter' ) ) {
				$article_title_icon = apply_filters( 'eckb_article_icon_filter', $article_title_icon, $prev_post_id );
				$article_title_icon = empty( $article_title_icon ) ? 'ep_font_icon_document' : $article_title_icon;
			}

			$new_tab = '';
			if ( has_filter( 'eckb_link_newtab_filter' ) ) {
				$new_tab = apply_filters( 'eckb_link_newtab_filter', $prev_post_id );
			}
			$new_tab = ! empty( $new_tab );

			// external links have rel="noopener noreferrer", while internal links have rel="prev"
			$prev_rel_escaped = EPKB_Utilities::is_internal_url( $prev_link ) ? 'prev' : 'noopener noreferrer';

			$prev_link =
				'<a href="' . esc_url( $prev_link ) . '" ' . ( $new_tab ? "target='_blank' " : '' ) . 'rel="' . $prev_rel_escaped . '">
					<span class="epkb-article-navigation__label">
					    <span class="epkb-article-navigation__label__previous__icon epkbfa epkbfa-caret-left"></span>
					    ' . esc_html( $prev_navigation_text ) . '
					</span>
					<span title="' . get_the_title( $prev_post_id ) . '" class="epkb-article-navigation-article__title">
						<span class="epkb-article-navigation__previous__icon epkbfa ' . esc_attr( $article_title_icon ) . '"></span>
						' . get_the_title( $prev_post_id ) . '
					</span>
				</a>';
		}

		$next_link = '';
		if ( ! empty( $next_post_id ) ) {

			$next_seq_no = isset( $repeat_cat_id[$next_post_id][$category_id] ) ? $repeat_cat_id[$next_post_id][$category_id] : 1;
			$next_link = get_permalink( $next_post_id );
			$next_link = empty( $next_seq_no ) || $next_seq_no < 2 ? $next_link : add_query_arg( 'seq_no', $next_seq_no, $next_link );

			// linked articles have their own icon
			$article_title_icon = 'ep_font_icon_document';
			if ( has_filter('eckb_article_icon_filter' ) ) {
				$article_title_icon = apply_filters( 'eckb_article_icon_filter', $article_title_icon, $next_post_id );
				$article_title_icon = empty( $article_title_icon ) ? 'ep_font_icon_document' : $article_title_icon;
			}

			$new_tab = '';
			if ( has_filter('eckb_link_newtab_filter' ) ) {
				$new_tab = apply_filters( 'eckb_link_newtab_filter', $next_post_id );
			}
			$new_tab = ! empty( $new_tab );

			// external links have rel="noopener noreferrer", while internal links have rel="next"
			$next_rel_escaped = EPKB_Utilities::is_internal_url( $next_link ) ? 'next' : 'noopener noreferrer';

			$next_link =
				'<a href="' . esc_url( $next_link ) . '" ' . ( $new_tab ? "target='_blank' " : '' ) . 'rel="' . $next_rel_escaped . '">
					<span class="epkb-article-navigation__label">
					    ' . esc_html( $next_navigation_text ) . '
					    <span class="epkb-article-navigation__label__next__icon epkbfa epkbfa-caret-right"></span>
                    </span>
					<span title="' . get_the_title( $next_post_id ) . '" class="epkb-article-navigation-article__title">
						' . get_the_title( $next_post_id ) . '
						<span class="epkb-article-navigation__next__icon epkbfa ' . esc_attr( $article_title_icon ) . '"></span>
					</span>
				</a>
			   ';
		}

		self::prev_next_navigation_html( $styles, $prev_link, $next_link );
	}

	/**
	 * OUTPUT PREV/NEXT buttons HTML
	 * @param $styles
	 * @param $prev_link
	 * @param $next_link
	 */
	private static function prev_next_navigation_html( $styles, $prev_link, $next_link ) {

		self::$styles .= $styles;

		$next_link_on_right = '';
		// If no Previous link available assign class to move Next link to far right.
		if ( empty( $prev_link ) ) {
			$next_link_on_right = 'epkb-article-navigation--next-link-right';
		}	   ?>

		<div class="epkb-article-navigation-container <?php echo esc_attr( $next_link_on_right ); ?>">            <?php

			if ( ! empty( $prev_link ) ) {  ?>
				<div class="epkb-article-navigation__previous"> <?php
					echo wp_kses_post( $prev_link );    ?>
				</div>  <?php
			}

			if ( ! empty( $next_link ) ) {                ?>
				<div class="epkb-article-navigation__next"><?php
					echo wp_kses_post( $next_link );    ?>
				</div>  <?php
			}  ?>

		</div>        <?php
	}

	// COMMENTS
	public static function comments( $args ) {
		// only show if using our KB template as theme templates display comments
		if ( $args['config'][ 'templates_for_kb' ] == 'kb_templates' && ! self::is_demo_article( $args['article'] ) ) {
			EPKB_Templates::get_template_part( 'feature', 'comments', array(), $args['article'] );
		}
	}


	/***********************   META DATA HEADER/FOOTER CONTAINER   *********************/

	// OLD META DATA HEADER
	public static function meta_data_header( $args ) {
		self::meta_container( $args, 'header' );
	}

	public static function meta_data_footer( $args ) {
		self::meta_container( $args, 'footer' );
	}

	/**
	 * DISPLAY METADATA CONTAINER such as author, modification and creation date. Used for:
	 * a) OLD META-DATA HEADER
	 * b) CURRENT META-DATA FOOTER
	 * @param $args
	 * @param $location
	 */
	private static function meta_container( $args, $location ) {
		global $eckb_is_kb_main_page;

		$kb_config = $args['config'];

		$args['output_location'] = $location == 'header' ? 'top' : 'bottom';
		$args['is_meta_container_on'] = false;

		// Sidebar layout on Main Page should not have metadata
		if ( $args['config']['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT && $eckb_is_kb_main_page ) {
			return;
		}

		// is meta data container enabled? for meta-data-header-toggle and meta-data-footer-toggle
		if ( isset( $kb_config['meta-data-' . $location . '-toggle'] ) && $kb_config['meta-data-' . $location . '-toggle'] == 'off' ) {
			return;
		}

		/** below is class eckb-article-content-header__article-meta and eckb-article-content-footer__article-meta */
		echo '<div class="' . 'eckb-article-content-' . esc_attr( $location ) . '__article-meta' . '">';

		$config_name = $location == 'header' ? 'article_content_enable_created_date' : 'created_on_' . $location . '_toggle';
		if ( isset( $kb_config[$config_name] ) && $kb_config[$config_name] != 'off' ) {
			self::created_on( $args );
		}

		$config_name = $location == 'header' ? 'article_content_enable_last_updated_date' : 'last_updated_on_' . $location . '_toggle';
		if ( isset( $kb_config[$config_name] ) && $kb_config[$config_name] != 'off' ) {
			self::last_updated_on( $args );
		}

		$config_name = $location == 'header' ? 'article_content_enable_author' : 'author_' . $location . '_toggle';
		if ( isset( $kb_config[$config_name] ) && $kb_config[$config_name] != 'off' ) {
			self::author( $args );
		}

		$config_name = $location == 'header' ? 'article_content_enable_views_counter' : 'article_views_counter_' . $location . '_toggle';
		if ( isset( $kb_config['article_views_counter_enable'] ) && $kb_config['article_views_counter_enable'] != 'off' && isset( $kb_config[$config_name] ) && $kb_config[$config_name] != 'off' ) {
			self::article_views_counter( $args );
		}

		if ( $location == 'header' && $args['config']['print_button_enable'] != 'off' ) {
			self::print_button( $args );
		}

		// output other metadata like Article Rating
		do_action( 'eckb-article-meta-container-end', $args );

		echo '</div>';
	}

	// CREATED ON
	private static function created_on( $args ) {
		echo '<div class="eckb-ach__article-meta__date-created">';
		if ( 'on' == $args['config']['article_meta_icon_on'] ) {
			echo '<span class="eckb-ach__article-meta__date-created__date-icon epkbfa epkbfa-calendar"></span>';
		}
		if ( $args['config']['created_on_text'] && ! empty($args['article']->post_date) ) {
			echo '<span class="eckb-ach__article-meta__date-created__text">' . esc_html( $args['config']['created_on_text'] ) . '</span>';
		}

		echo '<span class="eckb-ach__article-meta__date-created__date">';
		printf(
			'<time class="entry-date" datetime="%1$s">%2$s</time>',
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() )
		);
		echo '</span>';
		echo '</div>';
	}

	// LAST UPDATED ON
	private static function last_updated_on( $args ) {
		echo '<div class="eckb-ach__article-meta__date-updated">';
		if ( 'on' == $args['config']['article_meta_icon_on'] ) {
			echo '<span class="eckb-ach__article-meta__date-updated__date-icon epkbfa epkbfa-pencil-square-o"></span>';
		}
		if ( $args['config']['last_updated_on_text'] && ! empty($args['article']->post_modified) ) {
			echo '<span class="eckb-ach__article-meta__date-updated__text">' . esc_html( $args['config']['last_updated_on_text'] ) . '</span>';
		}

		echo '<span class="eckb-ach__article-meta__date-updated__date">';
		printf(
			'<time class="entry-date" datetime="%1$s">%2$s</time>',
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);
		echo '</span>';
		echo '</div>';
	}

	// AUTHOR
	public static function author( $args ) {

		$post_author = get_the_author_meta( 'display_name', $args['article']->post_author );
		$post_author = empty($post_author) ? esc_html__( 'Demo', 'echo-knowledge-base' ) : $post_author;

		echo '<div class="eckb-ach__article-meta__author">';
		if ( 'on' == $args['config']['article_meta_icon_on'] ) {
			echo '<span class="eckb-ach__article-meta__author__author-icon epkbfa epkbfa-user"></span>';
		}
		if ( $args['config']['author_text'] && ! empty($post_author) ) {
			echo '<span class="eckb-ach__article-meta__author__text">' . esc_html( $args['config']['author_text'] ) . '</span>';
		}
		echo '<span class="eckb-ach__article-meta__author__name">' . esc_html( $post_author ) . '</span>';
		echo '</div>';
	}

	// ARTICLE VIEWS COUNTER
	private static function article_views_counter( $args ) {

		$views = EPKB_Article_Count_Handler::get_article_views_counter_frontend();

		echo '<div class="eckb-ach__article-meta__views_counter">';
		if ( 'on' == $args['config']['article_meta_icon_on'] ) {
			echo '<span class="eckb-ach__article-meta__views_counter-icon epkbfa epkbfa-signal"></span>';
		}
		if ( $args['config']['article_views_counter_text'] ) {
			echo '<span class="eckb-ach__article-meta__views_counter__text">' . esc_html( $args['config']['article_views_counter_text'] ) . '</span>';
		}
		echo '<span class="eckb-ach__article-meta__views_counter__name">' . esc_html( $views ) . '</span>';
		echo '</div>';
	}

	// PRINT BUTTON - old article content header - beside meta data
	private static function print_button( $args ) {    ?>
		<span class="eckb-print-button-meta-container">			<?php
			if ( 'on' == $args['config']['article_meta_icon_on'] ) {
				echo '<span class="eckb-print-button-meta-icon epkbfa epkbfa-print"></span>';
			}

			if ( ! empty($args['config']['print_button_text']) ) { ?>
				<span class="eckb-print-button-meta-text"><?php echo esc_html( $args['config']['print_button_text'] );  ?></span>				<?php
			}  ?>
		 </span> <?php
	}


	/******************************************************************************
	 *
	 *  SIDEBARS
	 *
	 ******************************************************************************/

	/**
	 * Display Article KB Sidebar
	 * @param $args
	 */
	public static function display_kb_widgets_sidebar( $args ) {

		$widget_id = $args['config']['id'] == 1 ? 'eckb_articles_sidebar' : 'eckb_articles_sidebar_' . $args['config']['id'];
		if ( $args['config']['template_widget_sidebar_defaults'] == 'on' ) {
			$article_widget_sidebar_default_styles = 'eckb-article-widget-sidebar--default-styles';
		} else {
			$article_widget_sidebar_default_styles = '';
		} ?>

		<div id="eckb-article-widget-sidebar-container" class="<?php echo esc_attr( $article_widget_sidebar_default_styles );?>">
			<div class="eckb-article-widget-sidebar-body">				<?php
				self::wizard_widget_demo_data( $widget_id );
				dynamic_sidebar( $widget_id );				?>
			</div>
		</div>    <?php
	}

	/**
	 * Display LEFT navigation Sidebar
	 * @param $args
	 */
	public static function display_nav_sidebar_left( $args ) {

		$nav_sidebar_type = EPKB_Core_Utilities::get_nav_sidebar_type( $args['config'], 'left' );

		// a) Top Categories navigation ( used by Categories Focused Layout as well )
		if ( $nav_sidebar_type == 'eckb-nav-sidebar-categories' ) {
			self::display_top_categories_sidebar( $args );

		// b) Elegant Layouts navigation V2 Sidebar (v1 setting)
		} else if ( $nav_sidebar_type == 'eckb-nav-sidebar-v1' && EPKB_Utilities::is_elegant_layouts_enabled() ) {
			do_action( 'eckb-article-v2-elay_sidebar', $args );

		// c) Categories and Articles (Core) Navigation
		} else if ( $nav_sidebar_type == 'eckb-nav-sidebar-v1' ) {
			$core_nav_sidebar = new EPKB_Layout_Article_Sidebar();
			$core_nav_sidebar->display_article_sidebar( $args['config'] );

		} else if ( $nav_sidebar_type == 'eckb-nav-sidebar-current-category' ) {
			$core_nav_sidebar = new EPKB_Layout_Article_Sidebar();
			$breadcrumb_tree = EPKB_Templates_Various::get_article_breadcrumb( $args['config'], $args['article']->ID );
			end( $breadcrumb_tree );
			$current_category_id = key( $breadcrumb_tree );
			$core_nav_sidebar->display_article_sidebar( $args['config'], true, $current_category_id );
		}
	}

	/**
	 * Display RIGHT navigation Sidebar
	 * @param $args
	 */
	public static function display_nav_sidebar_right( $args ) {

		$nav_sidebar_type = EPKB_Core_Utilities::get_nav_sidebar_type( $args['config'], 'right' );

		// a) Top Categories navigation ( used by Categories Focused Layout as well )
		if ( $nav_sidebar_type == 'eckb-nav-sidebar-categories' ) {
			self::display_top_categories_sidebar( $args );

		// b) Elegant Layouts navigation V2 Sidebar (v1 setting)
		} else if ( $nav_sidebar_type == 'eckb-nav-sidebar-v1' && EPKB_Utilities::is_elegant_layouts_enabled() ) {
			do_action( 'eckb-article-v2-elay_sidebar', $args );

		// c) Categories and Articles (Core) Navigation
		} else if ( $nav_sidebar_type == 'eckb-nav-sidebar-v1' ) {
			$core_nav_sidebar = new EPKB_Layout_Article_Sidebar();
			$core_nav_sidebar->display_article_sidebar( $args['config'] );

		} else if ( $nav_sidebar_type == 'eckb-nav-sidebar-current-category' ) {
			$core_nav_sidebar = new EPKB_Layout_Article_Sidebar();
			$breadcrumb_tree = EPKB_Templates_Various::get_article_breadcrumb( $args['config'], $args['article']->ID );
			end( $breadcrumb_tree );
			$current_category_id = key( $breadcrumb_tree );
			$core_nav_sidebar->display_article_sidebar( $args['config'], true, $current_category_id );
		}
	}

	/**
	 * For Category Focused Layout show top level or sibling categories in the Navigation Sidebar
	 *
	 * @param $args
	 */
	private static function display_top_categories_sidebar( $args ) {

		$parent_category_id = 0;
		$active_id = 0;
		$breadcrumb_tree = EPKB_Templates_Various::get_article_breadcrumb( $args['config'], $args['article']->ID );
		$breadcrumb_tree = array_keys( $breadcrumb_tree );

		if ( $args['config']['categories_layout_list_mode'] == 'list_top_categories' ) {
			if ( isset( $breadcrumb_tree[0] ) ) {
				$active_id = $breadcrumb_tree[0];
			}
		} else {
			$tree_count = count( $breadcrumb_tree );
			if ( $tree_count > 1 ) {
				$parent_category_id = $breadcrumb_tree[$tree_count - 2];
				$active_id = $breadcrumb_tree[$tree_count - 1];
			}

			if ( $tree_count == 1 ) {
				$active_id = $breadcrumb_tree[0];
			}
		}

		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo EPKB_Layout_Category_Sidebar::get_layout_categories_list( $args['config']['id'], $args['config'], $parent_category_id, $active_id );
	}

	/**
	 * Output Table of Content
	 *
	 * @param $args
	 */
	public static function table_of_content( $args ) {

		// show TOC only on the article page
		if ( EPKB_Utilities::is_kb_main_page() ) {
			return;
		}

		$article_toc_typography_styles = EPKB_Utilities::get_typography_config( $args['config']['article_toc_typography'] );
		$article_toc_header_typography_styles = EPKB_Utilities::get_typography_config( $args['config']['article_toc_header_typography'] );

		// Both Versions
		self::$styles .= '
			#eckb-article-body .eckb-article-toc ul a.active {
				background-color:   ' . $args['config']['article_toc_active_bg_color'] . ';
				color:              ' . $args['config']['article_toc_active_text_color'] . ';
			}
			#eckb-article-body .eckb-article-toc ul a:hover {
				background-color:   ' . $args['config']['article_toc_cursor_hover_bg_color'] . ';
				color:              ' . $args['config']['article_toc_cursor_hover_text_color'] . ';
			}
			#eckb-article-body .eckb-article-toc__inner {
				border-color: ' . $args['config']['article_toc_border_color'] . ';
				' . $article_toc_typography_styles . '
				background-color:   ' . $args['config']['article_toc_background_color'] . ';
			}
			#eckb-article-body .eckb-article-toc__inner a {
				color:              ' . $args['config']['article_toc_text_color'] . ';
				' . $article_toc_typography_styles . '
			}
			#eckb-article-body .eckb-article-toc__title {
				color:              ' . $args['config']['article_toc_title_color'] . ';
				' . $article_toc_header_typography_styles . '
			}
		';

		$classes = ' eckb-article-toc--bmode-' . $args['config']['article_toc_border_mode'];

		echo '
			<div class="eckb-article-toc ' . esc_attr( $classes ) . ' eckb-article-toc-reset "				
				data-offset="' . esc_attr( $args['config']['article_toc_scroll_offset'] ) . '"
				data-min="' . esc_attr( $args['config']['article_toc_hx_level'] ) . '"
				data-max="' . esc_attr( $args['config']['article_toc_hy_level'] ) . '"
				data-speed="' . esc_attr( $args['config']['article_toc_scroll_speed'] ) . '"
				data-exclude_class="' . esc_attr( $args['config']['article_toc_exclude_class'] ) . '"
				>' .
					( empty( $args['config']['article_toc_title'] ) ? '' : '<div class="eckb-article-toc__title">' . esc_html( $args['config']['article_toc_title'] ) . '</div>' ) .
			'</div>
			';
	}

	public static function is_left_sidebar_on( $kb_config ) {
		return $kb_config['article-left-sidebar-toggle'] != 'off';
	}

	public static function is_right_sidebar_on( $kb_config ) {
		return $kb_config['article-right-sidebar-toggle'] != 'off';
	}


	/******************************************************************************
	 *
	 *  OTHER UTILITIES
	 *
	 ******************************************************************************/

	/**
	 * Disable comments.
	 * Enable comments, but it is up to WP, article and theme settings whether comments are actually displayed.
	 *
	 * @param $open
	 * @param $post_id
	 *
	 * @return bool
	 * @noinspection PhpUnusedParameterInspection*/
	public function setup_comments( $open, $post_id ) {
      global $eckb_kb_id;

		// verify it is a KB article
		$post = get_post();
		if ( empty( $post ) || ! $post instanceof WP_Post || ( ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) && empty( $eckb_kb_id ) ) ) {
			return $open;
		}

		$kb_id = empty($eckb_kb_id) ? EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type ) : $eckb_kb_id;
		if ( is_wp_error($kb_id) ) {
			return $open;
		}

		if ( empty($this->cached_comments_flag) ) {
			$this->cached_comments_flag = epkb_get_instance()->kb_config_obj->get_value( $kb_id, 'articles_comments_global', 'off' );
		}

		if ( $this->cached_comments_flag == 'article' ) {
			$open_comment = isset( $post->comment_status ) && $post->comment_status == 'open';
		} else {
			$open_comment = 'on' == $this->cached_comments_flag;
		}

		return $open_comment;
	}

	/**
	 * Output section container + trigger hook to output the section content.
	 *
	 * @param $hook - both hook name and div id
	 * @param $args
	 */
	public static function article_section( $hook, $args ) {

	   echo '<div id="' . esc_attr( $hook ) . '">';

		if ( self::is_hook_enabled( $args['config'], $hook ) ) {
			do_action( $hook, $args );
		}

		echo '</div>';
	}

	/**
	 * Hooks in Sidebar belong to either left or right sidebar. If sidebar is disabled then it is not invoked.
	 *
	 * @param $kb_config
	 * @param $hook
	 * @return bool
	 */
	private static function is_hook_enabled( $kb_config, $hook ) {
		// do not output left and/or right sidebar if not configured
		if ( $hook == 'eckb-article-left-sidebar' && ! self::is_left_sidebar_on( $kb_config ) ) {
			return false;
		}
		if ( $hook == 'eckb-article-right-sidebar' && ! self::is_right_sidebar_on( $kb_config ) ) {
			return false;
		}

		return true;
	}

	private static function is_demo_article( $article ) {
        return empty($article->ID) || empty($GLOBALS['post']) || empty($GLOBALS['post']->ID);
    }

	/**
	 * Generate new article style from configuration
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function generate_article_structure_css_v2( $kb_config ) {

		// Left Sidebar Settings
		$article_left_sidebar_padding_top          = $kb_config['article-left-sidebar-padding-v2_top'];
		$article_left_sidebar_padding_right        = $kb_config['article-left-sidebar-padding-v2_right'];
		$article_left_sidebar_padding_bottom       = $kb_config['article-left-sidebar-padding-v2_bottom'];
		$article_left_sidebar_padding_left         = $kb_config['article-left-sidebar-padding-v2_left'];

		$article_left_sidebar_bgColor                  = $kb_config['article-left-sidebar-background-color-v2'];
		$article_left_sidebar_starting_position        = $kb_config['article-left-sidebar-starting-position'];
		$article_left_sidebar_starting_position_mobile = $kb_config['article-left-sidebar-starting-position-mobile'];

		// Content Settings
		$article_content_padding                    = $kb_config['article-content-padding-v2'];
		$article_content_bgColor                    = $kb_config['article-content-background-color-v2'];
		$article_meta_color                         = $kb_config['article-meta-color'];
		$article_meta_typography                    = $kb_config['article-meta-typography'];

		// Right Sidebar Settings
		$article_right_sidebar_padding_top          = $kb_config['article-right-sidebar-padding-v2_top'];
		$article_right_sidebar_padding_right        = $kb_config['article-right-sidebar-padding-v2_right'];
		$article_right_sidebar_padding_bottom       = $kb_config['article-right-sidebar-padding-v2_bottom'];
		$article_right_sidebar_padding_left         = $kb_config['article-right-sidebar-padding-v2_left'];


		$article_right_sidebar_bgColor                 = $kb_config['article-right-sidebar-background-color-v2'];
		$article_right_sidebar_starting_position       = $kb_config['article-right-sidebar-starting-position'];
		$article_right_sidebar_starting_position_mobile = $kb_config['article-right-sidebar-starting-position-mobile'];


		// Desktop Settings
		$article_container_desktop_width        = $kb_config['article-container-desktop-width-v2'];
		$article_container_desktop_width_units  = $kb_config['article-container-desktop-width-units-v2'];

		$article_body_desktop_width             = $kb_config['article-body-desktop-width-v2'];
		$article_body_desktop_width_units       = $kb_config['article-body-desktop-width-units-v2'];

		$article_left_sidebar_desktop_width     = $kb_config['article-left-sidebar-desktop-width-v2'];
		$article_right_sidebar_desktop_width    = $kb_config['article-right-sidebar-desktop-width-v2'];


		// Tablet Settings
		$tablet_breakpoint                      = $kb_config['article-tablet-break-point-v2'];
		$article_container_tablet_width         = $kb_config['article-container-tablet-width-v2'];
		$article_container_tablet_width_units   = $kb_config['article-container-tablet-width-units-v2'];

		$article_body_tablet_width              = $kb_config['article-body-tablet-width-v2'];
		$article_body_tablet_width_units        = $kb_config['article-body-tablet-width-units-v2'];

		$article_left_sidebar_tablet_width      = $kb_config['article-left-sidebar-tablet-width-v2'];
		$article_right_sidebar_tablet_width     = $kb_config['article-right-sidebar-tablet-width-v2'];


		// Mobile Settings
		$mobile_breakpoint                      = $kb_config['article-mobile-break-point-v2'];
		if ( is_numeric( $mobile_breakpoint ) && ! empty( $_REQUEST['epkb-editor-page-loaded'] ) ) {
			$mobile_breakpoint -= 400;
			$tablet_breakpoint -= 400;
		}
		
		// auto-determine whether we need sidebar or let user override it to be displayed
		$is_left_sidebar_on = self::is_left_sidebar_on( $kb_config );
		$is_right_sidebar_on = self::is_right_sidebar_on( $kb_config );

		// Backend Editor special values
		if ( EPKB_Core_Utilities::is_backend_editor_iframe() ) {
			$article_container_desktop_width = '100';
			$article_container_desktop_width_units = '%';
			$article_body_desktop_width = '100';
			$article_body_desktop_width_units = '%';
			$article_container_tablet_width_units = '%';
			$article_container_tablet_width = '100';
			$article_body_tablet_width = '100';
			$article_body_tablet_width_units = '%';
		}

		/**
		 *  Grid Columns start at lines.
		 *
		 *  Left Sidebar Grid Start:    1 - 2;
		 *  Content Grid Start:         2 - 3;
		 *  Right Sidebar Grid Start:    3 - 4;
		 *
		 *  LEFT   Content  Right
		 *  1 - 2   2 - 3   3 - 4
		 */

		$output = self::article_media_structure( array(
			'is_left_sidebar_on'            => $is_left_sidebar_on,
			'is_right_sidebar_on'           => $is_right_sidebar_on,
			'article_container_width'       => $article_container_desktop_width,
			'article_container_width_units' => $article_container_desktop_width_units,
			'article_body_width'            => $article_body_desktop_width,
			'article_body_width_units'      => $article_body_desktop_width_units,
			'article_left_sidebar_width'    => $article_left_sidebar_desktop_width,
			'article_content_width'         => 100 - $kb_config['article-left-sidebar-desktop-width-v2'] - $kb_config['article-right-sidebar-desktop-width-v2'],
			'article_right_sidebar_width'   => $article_right_sidebar_desktop_width,
			'breakpoint' => 'desktop',
			'type' => 'DESKTOP',
		) );

		$output .= self::article_media_structure( array(
			'is_left_sidebar_on'            => $is_left_sidebar_on,
			'is_right_sidebar_on'           => $is_right_sidebar_on,
			'article_container_width'       => $article_container_tablet_width,
			'article_container_width_units' => $article_container_tablet_width_units,
			'article_body_width'            => $article_body_tablet_width,
			'article_body_width_units'      => $article_body_tablet_width_units,
			'article_left_sidebar_width'    => $article_left_sidebar_tablet_width,
			'article_content_width'         => 100 - $kb_config['article-left-sidebar-tablet-width-v2'] - $kb_config['article-right-sidebar-tablet-width-v2'],
			'article_right_sidebar_width'   => $article_right_sidebar_tablet_width,
			'breakpoint'                    => $tablet_breakpoint,
			'type'                          => 'TABLET',
		) );

		/* SHARED */

		$output .= '
				#eckb-article-page-container-v2 #eckb-article-header, 
				#eckb-article-page-container-v2 #eckb-article-content-header-v2, 
				#eckb-article-page-container-v2 #eckb-article-left-sidebar, 
				#eckb-article-page-container-v2 #eckb-article-right-sidebar,
				#eckb-article-page-container-v2 #epkb-sidebar-container-v2 .epkb-sidebar__heading__inner__cat-name,
				#eckb-article-page-container-v2 #epkb-sidebar-container-v2 .epkb-category-level-2-3__cat-name,
				#eckb-article-page-container-v2 #epkb-sidebar-container-v2 .eckb-article-title__text,
				#eckb-article-page-container-v2 #elay-sidebar-container-v2 .elay-sidebar__heading__inner__cat-name,
				#eckb-article-page-container-v2 #elay-sidebar-container-v2 .elay-category-level-2-3__cat-name,
				#eckb-article-page-container-v2 #elay-sidebar-container-v2 .elay-article-title__text,
				#eckb-article-page-container-v2 .eckb-acll__title,
				#eckb-article-page-container-v2 .eckb-acll__cat-item__name,
				#eckb-article-page-container-v2 #eckb-article-content-header,
				#eckb-article-page-container-v2 .eckb-article-toc .eckb-article-toc__title,
				#eckb-article-page-container-v2 .eckb-article-toc .eckb-article-toc__level a,
				#eckb-article-page-container-v2 .eckb-breadcrumb-nav,
				#eckb-article-page-container-v2 #eckb-article-content-footer { 
				    	font-family: ' . ( ! empty( $kb_config['general_typography']['font-family'] ) ? $kb_config['general_typography']['font-family'] .'!important' : 'inherit !important' ) . ';
				}';

		$output .= '
			#eckb-article-page-container-v2 #eckb-article-left-sidebar {
				padding: ' . $article_left_sidebar_padding_top . 'px ' . $article_left_sidebar_padding_right . 'px ' . $article_left_sidebar_padding_bottom . 'px ' . $article_left_sidebar_padding_left . 'px;
				background-color: ' . $article_left_sidebar_bgColor .';
				margin-top: ' . $article_left_sidebar_starting_position .'px;
			}
			#eckb-article-page-container-v2 #eckb-article-content {
				padding: ' . $article_content_padding .'px;
				background-color: ' . $article_content_bgColor . ';
			}
			.eckb-article-content-created-date-container, .eckb-article-content-last-updated-date-container, .eckb-article-content-author-container,
			.eckb-article-content-article-views-counter-container, .eckb-ach__article-meta__date-created, .eckb-ach__article-meta__author,.eckb-ach__article-meta__views_counter, .eckb-ach__article-meta__date-updated {
				color: ' . $article_meta_color . ';' .
				EPKB_Utilities::get_typography_config( $article_meta_typography ). '
			}
			#eckb-article-page-container-v2 #eckb-article-right-sidebar {
				padding: ' . $article_right_sidebar_padding_top . 'px ' . $article_right_sidebar_padding_right . 'px ' . $article_right_sidebar_padding_bottom . 'px ' . $article_right_sidebar_padding_left . 'px;
				background-color: ' . $article_right_sidebar_bgColor . ';
				margin-top: ' . $article_right_sidebar_starting_position . 'px;
			} ';

		/* MOBILE - Set all columns to full width. */
		$output .= '
			@media only screen and ( max-width: ' . $mobile_breakpoint . 'px ) {
	
				#eckb-article-page-container-v2 {
					width:100%;
				}
				#eckb-article-page-container-v2 #eckb-article-content {
					grid-column-start: 1;
					grid-column-end: 4;
				}
				#eckb-article-page-container-v2 #eckb-article-left-sidebar {
					grid-column-start: 1;
					grid-column-end: 4;
				}
				#eckb-article-page-container-v2 #eckb-article-right-sidebar {
					grid-column-start: 1;
					grid-column-end: 4;
				}
				#eckb-article-page-container-v2 .eckb-article-toc {
					position: relative;
					float: left;
					width: 100%;
					height: auto;
					top: 0;
				}
				#eckb-article-page-container-v2 #eckb-article-body {
					display: flex;
					flex-direction: column;
				}
				#eckb-article-page-container-v2 #eckb-article-left-sidebar {
					order: 3;
					margin-top: ' . $article_left_sidebar_starting_position_mobile . 'px!important;
				}
				#eckb-article-page-container-v2 #eckb-article-content { order: 2; }
				#eckb-article-page-container-v2 #eckb-article-right-sidebar {
					order: 1;
					margin-top: ' . $article_right_sidebar_starting_position_mobile . 'px!important;
				}
			}
		';

		/* PRINT */
		$output .=
			'@media print {
				@page {
					margin: ' . $kb_config['print_button_doc_padding_top'] . 'px ' . $kb_config['print_button_doc_padding_right'] . 'px ' . $kb_config['print_button_doc_padding_bottom'] . 'px ' . $kb_config['print_button_doc_padding_left'] . 'px!important;
				}
			}';

		return $output;
	}

	/**
	 * Output style for either desktop or tablet
	 * @param array $settings
	 */
	public static function article_media_structure( $settings = array() ) {

		$defaults = array(
			'is_left_sidebar_on'            => '',
			'is_right_sidebar_on'           => '',
			'article_container_width'       => '',
			'article_container_width_units' => '',
			'article_body_width'            => '',
			'article_body_width_units'      => '',
			'article_left_sidebar_width'    => '',
			'article_content_width'         => '',
			'article_right_sidebar_width'   => '',
			'breakpoint'                    => '',
			'type'                          => '',
		);
		$args = array_merge( $defaults, $settings );


		$article_length = ' /* ' . $args[ 'type' ] . ' */ ' ;
		
		if ( $args[ 'breakpoint' ]  != 'desktop' ) {
			$article_length .= '@media only screen and ( max-width: '.$args[ 'breakpoint' ].'px ) {';
		}

		$article_length .=
			'#eckb-article-page-container-v2  {
				width: '.$args[ 'article_container_width' ] . $args[ 'article_container_width_units'].' }';
		$article_length .=
			'#eckb-article-page-container-v2 #eckb-article-body {
				width: '.$args[ 'article_body_width' ] . $args[ 'article_body_width_units'].' }';

		/**
		 * If No Left Sidebar
		 *  - Expend the Article Content 1 - 3
		 *  - Make Layout 2 Columns only and use the Two remaining values
		 */
		if ( ! $args[ 'is_left_sidebar_on' ]  ) {
			$article_length .= '
		        /* NO LEFT SIDEBAR */
				#eckb-article-page-container-v2 #eckb-article-body {
				      grid-template-columns:  0 ' . $args[ 'article_content_width' ] . '% '.$args[ 'article_right_sidebar_width' ].'%;
				}
				#eckb-article-page-container-v2 #eckb-article-left-sidebar {
						display:none;
				}
				#eckb-article-page-container-v2 #eckb-article-content {
						grid-column-start: 1;
						grid-column-end: 3;
					}
				';
		}

		/**
		 * If No Right Sidebar
		 *  - Expend the Article Content 2 - 4
		 *  - Make Layout 2 Columns only and use the Two remaining values
		 */
		if ( ! $args[ 'is_right_sidebar_on' ] ) {
			$article_length .= '
				/* NO RIGHT SIDEBAR */
				#eckb-article-page-container-v2 #eckb-article-body {
				      grid-template-columns: '.$args[ 'article_left_sidebar_width' ].'% ' . $args[ 'article_content_width' ] . '% 0 ;
				}
				
				#eckb-article-page-container-v2 #eckb-article-right-sidebar {
						display:none;
				}
				#eckb-article-page-container-v2 #eckb-article-content {
						grid-column-start: 2;
						grid-column-end: 4;
					}
				';
		}

		// If No Sidebars Expand the Article Content 1 - 4
		if ( ! $args[ 'is_left_sidebar_on'] && ! $args[ 'is_right_sidebar_on' ] ) {
			$article_length .= '
				#eckb-article-page-container-v2 #eckb-article-body {
				      grid-template-columns: 0 ' . $args[ 'article_content_width' ] . '% 0;
				}
				#eckb-article-page-container-v2 #eckb-article-left-sidebar {
						display:none;
				}
				#eckb-article-page-container-v2 #eckb-article-right-sidebar {
						display:none;
				}
				#eckb-article-page-container-v2 #eckb-article-content {
						grid-column-start: 1;
						grid-column-end: 4;
					}
				';
		}

		/**
		 * If Both Sidebars are active
		 *  - Make Layout 3 Columns and divide their sizes according to the user settings
		 */
		if ( $args[ 'is_left_sidebar_on' ] && $args[ 'is_right_sidebar_on' ] ) {
			$article_length .= '
					#eckb-article-page-container-v2 #eckb-article-body {
					      grid-template-columns: ' . $args[ 'article_left_sidebar_width' ] . '% ' . $args[ 'article_content_width' ] . '% ' . $args[ 'article_right_sidebar_width' ] . '%;
					}
					';
		}

		if ( $args[ 'breakpoint' ]  !== 'desktop' ) {
			$article_length .= '}';
		}

		return $article_length;
	}

	/**
	 * Display a message in the Widget Container, Indicating that there are no Widgets assigned to this element.
	 * @param $widget_id
	 */
	private static function wizard_widget_demo_data( $widget_id ) {
		if ( self::is_configuring_article() && ! is_active_sidebar( $widget_id ) ) { ?>
			  <div class="eckb-no-widget">
			   <?php esc_html_e( 'No widgets', 'echo-widgets' ); ?><br>
				  <a class="eckb-redirect-link" href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>" target="_blank"><?php esc_html_e( 'Add your widgets here', 'echo-widgets' ); ?></a>
			  </div> <?php
		}
	}

	/**
	 * Function to flatten array
	 * @param array $category_array
	 * @param $kb_config
	 * @return array
	 */
	public static function epkb_get_array_keys_multiarray( array $category_array, $kb_config ) {
		$keys = array();

		foreach ( $category_array as $key => $value ) {
			if ( $kb_config['show_articles_before_categories'] != 'off' ) {
				$keys[] = $key;
			}

			if ( is_array( $value ) ) {
				$keys = array_merge( $keys, self::epkb_get_array_keys_multiarray( $value, $kb_config ) );
			}

			if ( $kb_config['show_articles_before_categories'] == 'off' ) {
				$keys[] = $key;
			}
		}

		return $keys;
	}

	private static function is_configuring_article() {
		return EPKB_Utilities::get( 'epkb-editor-page-loaded' ) == '1';
	}

	/**
	 * If frontend JS detects multiple articles content on the page, set flag
	 */
	public static function epkb_update_the_content_flag() {

		// check capability author, editor, admin
		if ( ! EPKB_Admin_UI_Access::is_user_admin_editor_author() ) {
			EPKB_Utilities::ajax_show_error_die( '' );
		}

		$wp_nonce = EPKB_Utilities::post( '_wpnonce_epkb_ajax_action' );
		if ( empty( $wp_nonce ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $wp_nonce ) ), '_wpnonce_epkb_ajax_action' ) ) {
			EPKB_Utilities::ajax_show_error_die( '' );
		}

		EPKB_Core_Utilities::add_kb_flag( 'epkb_the_content_fix' );

		// we are done here
		EPKB_Utilities::ajax_show_info_die();
	}

	/**
	 * Return an array of article ids considering category and article sequences
	 *
	 * @param $category_seq_data
	 * @param $articles_seq_data
	 * @param $kb_config
	 * @return array
	 */
	private static function get_article_general_seq_ids( $category_seq_data, $articles_seq_data, $kb_config ) {

		$categories_general_seq_ids = array();
		$show_articles_before_categories = $kb_config['show_articles_before_categories'] == 'on';

		// collect category ids lvl 1
		foreach ( $category_seq_data as $cat_lvl1_id => $cat_lvl2_data ) {

			if ( $show_articles_before_categories ) {
				$categories_general_seq_ids[] = $cat_lvl1_id;
			}

			// collect category ids lvl 2
			foreach ( $cat_lvl2_data as $one_cat_lvl2_id => $cat_lvl3_data ) {

				if ( $show_articles_before_categories ) {
					$categories_general_seq_ids[] = $one_cat_lvl2_id;
				}

				// collect category ids lvl 3
				foreach ( $cat_lvl3_data as $one_cat_lvl3_id => $cat_lvl4_data ) {

					if ( $show_articles_before_categories ) {
						$categories_general_seq_ids[] = $one_cat_lvl3_id;
					}

					// collect category ids lvl 4
					foreach ( $cat_lvl4_data as $one_cat_lvl4_id => $cat_lvl5_data ) {

						if ( $show_articles_before_categories ) {
							$categories_general_seq_ids[] = $one_cat_lvl4_id;
						}

						// collect category ids lvl 5
						foreach ( $cat_lvl5_data as $one_cat_lvl5_id => $cat_lvl6_data ) {
							$categories_general_seq_ids[] = $one_cat_lvl5_id;
						}

						if ( ! $show_articles_before_categories ) {
							$categories_general_seq_ids[] = $one_cat_lvl4_id;
						}
					}

					if ( ! $show_articles_before_categories ) {
						$categories_general_seq_ids[] = $one_cat_lvl3_id;
					}
				}

				if ( ! $show_articles_before_categories ) {
					$categories_general_seq_ids[] = $one_cat_lvl2_id;
				}
			}

			if ( ! $show_articles_before_categories ) {
				$categories_general_seq_ids[] = $cat_lvl1_id;
			}
		}

		$articles_general_seq_ids = array();

		foreach ( $categories_general_seq_ids as $one_cat_id ) {
			if ( ! empty( $articles_seq_data[$one_cat_id] ) ) {
				foreach ( $articles_seq_data[$one_cat_id] as $article_id => $article_title ) {
					if ( $article_id > 1 ) {
						$articles_general_seq_ids[] = $article_id;
					}
				}
			}
		}

		return $articles_general_seq_ids;
	}
}
