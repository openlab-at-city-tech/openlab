<?php

/**
 *  Outputs the Search module for Modular Main Page.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_ML_Search {

	private $kb_config;
	private $setting_prefix;
	private $is_kb_block;

	function __construct( $kb_config, $is_kb_block = false ) {
		$this->kb_config = $kb_config;
		$this->setting_prefix = EPKB_Core_Utilities::is_main_page_search( $kb_config ) || $is_kb_block ? '' : 'article_';
		$this->is_kb_block = $is_kb_block;
	}

	/**
	 * Display Search box - Classic Layout
	 */
	public function display_classic_search_layout() {	?>

		<!-- Classic Search Layout -->
		<div id="epkb-ml-search-classic-layout">    <?php
			$this->display_search_title();  ?>
			<form id="epkb-ml-search-form" class="epkb-ml-search-input-height--<?php echo esc_attr( $this->kb_config['search_box_input_height'] ); ?>" method="get" action="/"<?php echo $this->is_kb_block ? ' ' . 'data-kb-block-post-id="' . (int)get_the_ID() . '"' : ''; ?>>
				<input type="hidden" id="epkb_kb_id" value="<?php echo esc_attr( $this->kb_config['id'] ); ?>" >

				<!-- Search Input Box -->
				<div id="epkb-ml-search-box">
					<input class="epkb-ml-search-box__input" type="text" name="s" value="" aria-label="<?php echo esc_attr( $this->kb_config[$this->setting_prefix . 'search_box_hint'] ); ?>"
					        placeholder="<?php echo esc_attr( $this->kb_config[$this->setting_prefix . 'search_box_hint'] ); ?>" aria-controls="epkb-ml-search-results" >
					<button class="epkb-ml-search-box__btn" type="submit">
                        <span class="epkb-ml-search-box__text"> <?php echo esc_html( $this->kb_config[$this->setting_prefix . 'search_button_name'] ); ?></span>
                        <span class="epkbfa epkbfa-spinner epkbfa-ml-loading-icon"></span>
                    </button>
				</div>

				<!-- Search Results -->
				<div id="epkb-ml-search-results" aria-live="polite"></div>
			</form>
		</div>  <?php
	}

	/**
	 * Display Search box - Modern Layout
	 */
	public function display_modern_search_layout() {	?>

		<!-- Modern Search Layout -->
		<div id="epkb-ml-search-modern-layout">    <?php
			$this->display_search_title();  ?>
			<form id="epkb-ml-search-form" class="epkb-ml-search-input-height--<?php echo esc_attr( $this->kb_config['search_box_input_height'] ); ?>" method="get" action="/"<?php echo $this->is_kb_block ? ' ' . 'data-kb-block-post-id="' . (int)get_the_ID() . '"' : ''; ?>>
				<input type="hidden" id="epkb_kb_id" value="<?php echo esc_attr( $this->kb_config['id'] ); ?>" >

				<!-- Search Input Box -->
				<div id="epkb-ml-search-box">
					<input class="epkb-ml-search-box__input" type="text" name="s" value="" aria-label="<?php echo esc_attr( $this->kb_config[$this->setting_prefix . 'search_box_hint'] ); ?>" placeholder="<?php echo esc_attr( $this->kb_config[$this->setting_prefix . 'search_box_hint'] ); ?>" aria-controls="epkb-ml-search-results" >
					<button class="epkb-ml-search-box__btn" type="submit">
                        <span class="epkbfa epkbfa-search epkbfa-ml-search-icon"></span>
                        <span class="epkbfa epkbfa-spinner epkbfa-ml-loading-icon"></span>
                    </button>
				</div>

				<!-- Search Results -->
				<div id="epkb-ml-search-results" aria-live="polite"></div>
			</form>
		</div>  <?php
    }

	/**
	 * Display HTML for Search Title
	 */
	private function display_search_title() {
		if ( empty( $this->kb_config['search_title'] ) ) {
			return;
		}

		$search_title_tag_escaped = EPKB_Utilities::sanitize_html_tag( $this->kb_config[$this->setting_prefix . 'search_title_html_tag'] ); ?>
		<<?php echo $search_title_tag_escaped; ?> class="epkb-ml-search-title"><?php echo esc_html( $this->kb_config[$this->setting_prefix . 'search_title'] ); ?></<?php echo $search_title_tag_escaped; ?>>   <?php
	}

	/**
	 * Returns inline styles for Search Module
	 *
	 * @param $kb_config
	 * @param bool $is_article
	 * @param $is_block
	 * @return string
	 */
	public static function get_inline_styles( $kb_config, $is_article = false, $is_block = false ) {

		$output = '
		/* CSS for Search Module
		-----------------------------------------------------------------------*/';

		$output .= $is_block ? '' : '
			#epkb-ml__module-search .epkb-ml-search-title,
			#epkb-ml__module-search .epkb-ml-search-box__input,
			#epkb-ml__module-search .epkb-ml-search-box__text {
				font-family: ' . ( ! empty( $kb_config['general_typography']['font-family'] ) ? $kb_config['general_typography']['font-family'] .'!important' : 'inherit !important' ) . ';
			}';

		// adjust for Article page
		if ( $is_article ) {

			// still check prefix because Sidebar layout uses Main Page search for Article Page
			$prefix = EPKB_Core_Utilities::is_main_page_search( $kb_config ) ? '' : 'article_';

			$output .= '
				#eckb-article-header #epkb-ml__module-search {
					margin-bottom: ' . $kb_config[$prefix . 'search_box_margin_bottom'] . 'px;
					padding-top: ' . $kb_config[$prefix . 'search_box_padding_top'] . 'px;
					padding-bottom: ' . $kb_config[$prefix . 'search_box_padding_bottom'] . 'px;
					background-color: ' . $kb_config[$prefix . 'search_background_color'] . ';
				}
				#epkb-ml__module-search .epkb-ml-search-title {
					color: ' . $kb_config[$prefix . 'search_title_font_color'] . ';
				}';

			// Classic Search
			$output .= '
				#epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form {
					max-width: ' . $kb_config[$prefix . 'search_box_input_width'] . '% !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout .epkb-ml-search-box__input {
					background-color: ' . $kb_config[$prefix . 'search_text_input_background_color'] . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-box {
					background-color: ' . $kb_config[$prefix . 'search_text_input_border_color'] . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout .epkb-ml-search-box__btn {
					background-color: ' . $kb_config[$prefix . 'search_btn_background_color'] . ' !important;
				}';
			// Modern Search
			$output .= '
				#epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form {
					max-width: ' . $kb_config[$prefix . 'search_box_input_width'] . '% !important;
				}
				#epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-box {
					background-color: ' . $kb_config[$prefix . 'search_btn_background_color'] . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-modern-layout .epkb-ml-search-box__input {
					background-color: ' . $kb_config[$prefix . 'search_text_input_background_color'] . ' !important;
				}';

		} else if ( is_archive() ) {
			$output .= '
				#eckb-archive-page-container #eckb-archive-header #epkb-ml__module-search {
					margin-bottom: ' . $kb_config['search_box_margin_bottom'] . 'px;
					padding-top: ' . $kb_config['search_box_padding_top'] . 'px;
					padding-bottom: ' . $kb_config['search_box_padding_bottom'] . 'px;
					background-color: ' . $kb_config['search_background_color'] . ';
				}
				#epkb-ml__module-search .epkb-ml-search-title {
					color: ' . $kb_config['search_title_font_color'] . ';
				}';
			// Classic Search
			$output .= '
				#epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form {
					max-width: ' . $kb_config['search_box_input_width'] . '% !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout .epkb-ml-search-box__input {
					background-color: ' . $kb_config['search_text_input_background_color'] . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-box {
					background-color: ' . $kb_config['search_text_input_border_color'] . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout .epkb-ml-search-box__btn {
					background-color: ' . $kb_config['search_btn_background_color'] . ' !important;
				}';
			// Modern Search
			$output .= '
				#epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form {
					max-width: ' . $kb_config['search_box_input_width'] . '% !important;
				}
				#epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-box {
					background-color: ' . $kb_config['search_btn_background_color'] . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-modern-layout .epkb-ml-search-box__input {
					background-color: ' . $kb_config['search_text_input_background_color'] . ' !important;
				}';

		} else {    // KB Main Page
			if ( ! $is_block ) {
				$output .= '
					#epkb-modular-main-page-container #epkb-ml__module-search {
						padding-top: ' . $kb_config['search_box_padding_top'] . 'px;
						padding-bottom: ' . $kb_config['search_box_padding_bottom'] . 'px;
						background-color: ' . $kb_config['search_background_color'] . ';
					}';
			}
			$output .= '
				#epkb-ml__module-search .epkb-ml-search-title {
					color: ' . sanitize_hex_color( $kb_config['search_title_font_color'] ) . ';
				}';
			// Classic Search
			$output .= '
				#epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form {
					max-width: ' . intval( $kb_config['search_box_input_width'] ) . '% !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout .epkb-ml-search-box__input {
					background-color: ' . sanitize_hex_color( $kb_config['search_text_input_background_color'] ) . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-box {
					background-color: ' . sanitize_hex_color( $kb_config['search_text_input_border_color'] ) . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout .epkb-ml-search-box__btn {
					background-color: ' . sanitize_hex_color( $kb_config['search_btn_background_color'] ) . ' !important;
				}';
			// Modern Search
			$output .= '
				#epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form {
					max-width: ' . intval( $kb_config['search_box_input_width'] ) . '% !important;
				}
				#epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-box {
					background-color: ' . sanitize_hex_color( $kb_config['search_btn_background_color'] ) . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-modern-layout .epkb-ml-search-box__input {
					background-color: ' . sanitize_hex_color( $kb_config['search_text_input_background_color'] ) . ' !important;
				}';
		}

		return $output;
	}

	/**
	 * Returns HTML for given search results
	 *
	 * @param $search_results
	 * @param $kb_config
	 * @return string
	 */
	public static function display_search_results_html( $search_results, $kb_config ) {

		if ( $kb_config['article_search_sync_toggle'] == 'on' || EPKB_Core_Utilities::is_main_page_search( $kb_config ) ) {
			$show_article_excerpt = $kb_config['search_result_mode'] == 'title_excerpt';
		} else {
			$show_article_excerpt = $kb_config['article_search_result_mode'] == 'title_excerpt';
		}

		$title_style_escaped = '';
		$icon_style_escaped  = '';
		if ( $kb_config['search_box_results_style'] == 'on' && EPKB_Core_Utilities::is_main_page_search( $kb_config ) ) {
			$setting_names = EPKB_Core_Utilities::get_style_setting_name( $kb_config['kb_main_page_layout'] );
			$title_style_escaped = EPKB_Utilities::get_inline_style( 'color:: ' . $setting_names['article_font_color'] , $kb_config );
			$icon_style_escaped = EPKB_Utilities::get_inline_style( 'color:: ' . $setting_names['article_icon_color'] , $kb_config );
		}

		ob_start(); ?>

		<ul>    <?php
			foreach ( $search_results as $article ) {

				$article_url = get_permalink( $article->ID );
				if ( empty( $article_url ) || is_wp_error( $article_url ) ) {
					continue;
				}

				// linked articles have their own icon
				$article_title_icon = 'ep_font_icon_document';
				if ( has_filter( 'eckb_single_article_filter' ) ) {
					$article_title_icon = apply_filters( 'eckb_article_icon_filter', $article_title_icon, $article->ID );
					$article_title_icon = empty( $article_title_icon ) ? 'epkbfa-file-text-o' : $article_title_icon;
				}

				// linked articles have open in new tab option
				$new_tab_escaped = '';
				if ( EPKB_Utilities::is_link_editor_enabled() ) {
					$link_editor_config = EPKB_Utilities::get_postmeta( $article->ID, 'kblk-link-editor-data', [], true );
					$new_tab_escaped = empty( $link_editor_config['open-new-tab'] ) ? '' : 'target="_blank"';
				}    ?>

				<li>
					<a href="<?php echo esc_url( $article_url ); ?>" <?php echo $new_tab_escaped; ?> class="epkb-ml-article-container" data-kb-article-id="<?php echo esc_attr( $article->ID ); ?>" <?php echo empty( $new_tab_escaped ) ? '' : 'rel="noopener noreferrer"'; ?>>
						<span class="epkb-article-inner" <?php echo $title_style_escaped; ?>>
							<span class="epkb-article__icon epkbfa <?php echo esc_attr( $article_title_icon ); ?>" aria-hidden="true" <?php echo $icon_style_escaped; ?>></span>
							<span class="epkb-article__text"><?php echo esc_html( $article->post_title ); ?>    <?php
								if ( $show_article_excerpt && ! empty( $article->post_excerpt ) ) {	?>
									<span class="epkb-article__excerpt"><?php echo esc_html( $article->post_excerpt ); ?></span>                            <?php
								}   ?>
							</span>
						</span>
					</a>
				</li>   <?php
			}   ?>
		</ul>   <?php

		return ob_get_clean();
	}
}