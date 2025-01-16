<?php

/**
 *  Outputs the Articles List module for Modular Main Page.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_ML_Articles_List {

	private $kb_config;

	function __construct( $kb_config ) {
		$this->kb_config = $kb_config;
	}

	/**
	 * Display Articles ( Recent, Newest, Popular etc. )
	 *
	 */
	public function display_articles_list() { ?>

		<div id="epkb-ml-article-list-<?php echo esc_attr( strtolower( $this->kb_config['kb_main_page_layout'] ) ); ?>-layout" class="epkb-ml-article-list-container">   <?php

			if ( $this->kb_config['ml_articles_list_title_location'] != 'none' ) { ?>
				<h2 class="epkb-ml-articles-list__title">
					<span><?php echo esc_html( $this->kb_config['ml_articles_list_title_text'] ); ?></span>
				</h2>   <?php
			}   ?>

			<div class="epkb-ml-articles-list__row"> <?php

				// Articles list Position 1
				switch ( $this->kb_config['ml_articles_list_column_1'] ) {

					case 'popular_articles':
						$this->display_popular_articles_list();
						break;

					case 'newest_articles':
						$this->display_newest_articles_list();
						break;

					case 'recent_articles':
						$this->display_recent_articles_list();
						break;

					default: break;
				}

				// Articles list Position 2
				switch ( $this->kb_config['ml_articles_list_column_2'] ) {

					case 'popular_articles':
						$this->display_popular_articles_list();
						break;

					case 'newest_articles':
						$this->display_newest_articles_list();
						break;

					case 'recent_articles':
						$this->display_recent_articles_list();
						break;

					default: break;
				}

				// Articles list Position 3
				switch ( $this->kb_config['ml_articles_list_column_3'] ) {

					case 'popular_articles':
						$this->display_popular_articles_list();
						break;

					case 'newest_articles':
						$this->display_newest_articles_list();
						break;

					case 'recent_articles':
						$this->display_recent_articles_list();
						break;

					default: break;
				}   ?>

			</div>

		</div>        <?php
	}

	/**
	 * Display Popular Articles list
	 */
	private function display_popular_articles_list() {

		$popular_articles = $this->execute_search( 'meta_value_num', 'epkb-article-views' );	?>

		<!-- Popular Articles -->
		<section id="epkb-ml-popular-articles" class="epkb-ml-article-section">
			<div class="epkb-ml-article-section__head"><?php echo esc_html( $this->kb_config['ml_articles_list_popular_articles_msg'] ); ?></div>
			<div class="epkb-ml-article-section__body">
				<ul class="epkb-ml-articles-list">    <?php
					if ( empty( $popular_articles ) ) {
						$popular_articles = $this->execute_search( 'date', '', 'ASC' );
					}
					if ( empty( $popular_articles ) ) {  ?>
						<li class="epkb-ml-articles-coming-soon"><?php echo esc_html( $this->kb_config['category_empty_msg'] ); ?></li> <?php
					}
					foreach ( $popular_articles as $article ) {  ?>
						<li><?php EPKB_Utilities::get_single_article_link( $this->kb_config, $article->post_title, $article->ID, 'Module' );  ?></li><?php
					}   ?>
				</ul>
			</div>
		</section>  <?php
	}

	/**
	 * Display Newest Articles list
	 */
	private function display_newest_articles_list() {

		$newest_articles = $this->execute_search( 'date' ); ?>

		<!-- Newest Articles -->
		<section id="epkb-ml-newest-articles" class="epkb-ml-article-section">
			<div class="epkb-ml-article-section__head"><?php echo esc_html( $this->kb_config['ml_articles_list_newest_articles_msg'] ); ?></div>
			<div class="epkb-ml-article-section__body">
				<ul class="epkb-ml-articles-list">    <?php
					if ( empty( $newest_articles) ) {   ?>
						<li class="epkb-ml-articles-coming-soon"><?php echo esc_html( $this->kb_config['category_empty_msg'] ); ?></li> <?php
					}
					foreach ( $newest_articles as $article ) {  ?>
						<li><?php EPKB_Utilities::get_single_article_link( $this->kb_config, $article->post_title, $article->ID, 'Module' ); ?></li><?php
					}   ?>
				</ul>
			</div>
		</section>  <?php
	}

	/**
	 * Display Recent Articles list
	 */
	private function display_recent_articles_list() {

		$recent_articles = $this->execute_search( 'modified' ); ?>

		<!-- Recent Articles -->
		<section id="epkb-ml-recent-articles" class="epkb-ml-article-section">
			<div class="epkb-ml-article-section__head"><?php echo esc_html( $this->kb_config['ml_articles_list_recent_articles_msg'] ); ?></div>
			<div class="epkb-ml-article-section__body">
				<ul class="epkb-ml-articles-list">    <?php
					if ( empty( $recent_articles) ) {   ?>
						<li class="epkb-ml-articles-coming-soon"><?php echo esc_html( $this->kb_config['category_empty_msg'] ); ?></li> <?php
					}
					foreach ( $recent_articles as $article ) {  ?>
						<li><?php EPKB_Utilities::get_single_article_link( $this->kb_config, $article->post_title, $article->ID, 'Module' ); ?></li><?php
					}   ?>
				</ul>
			</div>
		</section>  <?php
	}

	/**
	 * Call WP query to get matching terms (any term OR match)
	 * @param $order_by string - creation date, modified date or meta_value ('date' or 'modified' or 'meta_value_num')
	 * @param $meta_key string - meta key for custom sorting
	 * @return array - or empty if no results
	 */
	public function execute_search( $order_by, $meta_key='', $order_type='DESC' ) {

		$result = array();
		$search_params = array(
			'post_type'             => EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ),
			'ignore_sticky_posts'   => true,    // sticky posts will not show at the top
			'posts_per_page'        => EPKB_Utilities::is_amag_on() ? 200 : $this->kb_config['ml_articles_list_nof_articles_displayed'],  // limit search results
			'no_found_rows'         => true,    // query only posts_per_page rather than finding total nof posts for pagination etc.
			'orderby'               => $order_by,
			'order'                 => $order_type
		);

		// add meta_key for custom sorting by meta value
		if ( $order_by == 'meta_value_num' && ! empty( $meta_key ) ) {
			$search_params['meta_key'] = $meta_key;
		}

		// OLD installation or Access Manager
		$search_params['post_status'] = array( 'publish' );
		if ( EPKB_Utilities::is_amag_on() || is_user_logged_in() ) {
			$search_params['post_status'] = array( 'publish', 'private' );
		}

		$found_posts_obj = new WP_Query( $search_params );
		if ( ! empty( $found_posts_obj->posts ) ) {
			$result = $found_posts_obj->posts;
			wp_reset_postdata();
		}

		// limit the number of articles by config settings
		if ( EPKB_Utilities::is_amag_on() && count( $result ) > $this->kb_config['ml_articles_list_nof_articles_displayed'] ) {
			$result = array_splice( $result, 0, $this->kb_config['ml_articles_list_nof_articles_displayed'] );
		}

		return $result;
	}

	/**
	 * Returns inline styles for Articles List Module
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_inline_styles( $kb_config ) {

		/*
		 * Legacy Layouts that have specific settings
		 */
		$legacy_layouts = [
			EPKB_Layout::BASIC_LAYOUT,
			EPKB_Layout::TABS_LAYOUT,
			EPKB_Layout::CATEGORIES_LAYOUT,
			EPKB_Layout::SIDEBAR_LAYOUT,
			EPKB_Layout::GRID_LAYOUT,
		];

		// Use CSS Settings from Layout selected to match the styling.
		$setting_names = EPKB_Core_Utilities::get_style_setting_name( $kb_config['kb_main_page_layout'] );

		$shadow_setting_name = $setting_names['shadow'];
		$background_color_setting_name = $setting_names['background_color'];
		$head_typography_setting_name = $setting_names['head_typography'];
		$article_typography_setting_name = $setting_names['article_typography'];
		$border_setting_prefix = $setting_names['border_prefix'];
		$head_font_color_setting_name = $setting_names['head_font_color'];
		$article_font_color_setting_name = $setting_names['article_font_color'];
		$article_icon_color_setting_name = $setting_names['article_icon_color'];

		// Container -----------------------------------------/
		$output = '';
		$container_shadow = '';
		$container_background = '';
		if ( in_array( $kb_config['kb_main_page_layout'], $legacy_layouts ) ) {

			switch ( $kb_config[$shadow_setting_name] ) {
				case 'section_light_shadow':
					$container_shadow = 'box-shadow: 0px 3px 20px -10px rgba(0, 0, 0, 0.75);';
					break;
				case 'section_medium_shadow':
					$container_shadow = 'box-shadow: 0px 3px 20px -4px rgba(0, 0, 0, 0.75);';
					break;
				case 'section_bottom_shadow':
					$container_shadow = 'box-shadow: 0 2px 0 0 #E1E1E1;';
					break;
				default:
					break;
			}

			$container_background = 'background-color: ' . $kb_config[$background_color_setting_name] . ';';

			$output .= '
			#epkb-ml__module-articles-list .epkb-ml-articles-list__title {' . EPKB_Utilities::get_font_css( $kb_config, 'general_typography', 'font-family' ) . EPKB_Utilities::get_font_css( $kb_config, $head_typography_setting_name, 'font-size', 5 ) . '}';

			// Headings Typography -----------------------------------------/
			if ( in_array( $kb_config['kb_main_page_layout'], $legacy_layouts ) ) {
				$output .= '
				#epkb-ml__module-articles-list .epkb-ml-article-section__head {' . EPKB_Utilities::get_font_css( $kb_config, $head_typography_setting_name, 'font-size' ) . EPKB_Utilities::get_font_css( $kb_config, $head_typography_setting_name, 'font-weight' ) . '}';
			}

			// Articles  -----------------------------------------/
			if ( in_array( $kb_config['kb_main_page_layout'], $legacy_layouts ) ) {
				$output .= '
				#epkb-ml__module-articles-list .epkb-article-inner {' . EPKB_Utilities::get_font_css( $kb_config, $article_typography_setting_name, 'font-size' ) . EPKB_Utilities::get_font_css( $kb_config, $article_typography_setting_name, 'font-weight' ) . '}';
			}
		}

		$output .= '
		#epkb-ml__module-articles-list .epkb-ml-article-list-container .epkb-ml-article-section {
			border-color: ' . $kb_config[$border_setting_prefix . '_color'] . ' !important;
			border-width: ' . $kb_config[$border_setting_prefix . '_width'] . 'px !important;
			border-radius: ' . $kb_config[$border_setting_prefix . '_radius'] . 'px !important;
			border-style: solid !important;' .
			$container_shadow .
			$container_background .
		'}';

		// Headings Typography -----------------------------------------/
		if ( in_array( $kb_config['kb_main_page_layout'], $legacy_layouts ) ) {
			if ( ! empty( $kb_config[$head_typography_setting_name]['font-size'] ) || ! empty( $kb_config[$head_typography_setting_name]['font-weight'] ) ) {
				$output .= '
				#epkb-ml__module-articles-list .epkb-ml-article-section__head {
				    ' . ( empty( $kb_config[$head_typography_setting_name]['font-size'] ) ? '' : 'font-size:' . $kb_config[$head_typography_setting_name]['font-size'] . 'px !important;' ) . '
				    ' . ( empty( $kb_config[$head_typography_setting_name]['font-weight'] ) ? '' : 'font-weight:' . $kb_config[$head_typography_setting_name]['font-weight'] . '!important;' ) . '
			    }';
			}
		}
		$output .= '
		#epkb-ml__module-articles-list .epkb-ml-article-section__head {
			color: ' . $kb_config[$head_font_color_setting_name] . ' !important;
		}';

		// Articles  -----------------------------------------/
		if ( in_array( $kb_config['kb_main_page_layout'], $legacy_layouts ) ) {
			if ( ! empty( $kb_config[$article_typography_setting_name]['font-size'] ) || ! empty( $kb_config[$article_typography_setting_name]['font-weight'] ) ) {
				$output .= '
				#epkb-ml__module-articles-list .epkb-article-inner {
				    ' . ( empty( $kb_config[$article_typography_setting_name]['font-size'] ) ? '' : 'font-size:' . $kb_config[$article_typography_setting_name]['font-size']. 'px !important;' ) . '
				    ' . ( empty( $kb_config[$article_typography_setting_name]['font-weight'] ) ? '' : 'font-weight:' . $kb_config[$article_typography_setting_name]['font-weight']. '!important;' ) . '
			    }';
			}

		}
		$output .= '
		    #epkb-ml__module-articles-list .epkb-ml-articles-list li {
			    padding-top: ' . $kb_config['article_list_spacing'] . 'px !important;
			    padding-bottom: ' . $kb_config['article_list_spacing'] . 'px !important;
		        line-height: 1 !important;
		    }';

		$output .= '
		#epkb-ml__module-articles-list .epkb-article-inner .epkb-article__text {
		    color: ' . $kb_config[$article_font_color_setting_name] . ';
		}
		#epkb-ml__module-articles-list .epkb-article-inner .epkb-article__icon {
		    color: ' . $kb_config[$article_icon_color_setting_name] . ';
	    }';

		if ( $kb_config['ml_articles_list_title_location'] != 'none' ) {
			$output .= '#epkb-ml__module-articles-list .epkb-ml-articles-list__title { text-align: ' . esc_attr( $kb_config['ml_articles_list_title_location'] ) . '!important; }';
		}

		return $output;
	}
}