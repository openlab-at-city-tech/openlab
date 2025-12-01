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

		// Mark that search box is rendered for AI search results dialog output
		if ( EPKB_AI_Utilities::is_ai_search_advanced_enabled() ) {
			EPKB_AI_Search_Results_Display::mark_search_box_rendered();
		}
	}

	/**
	 * Display Search box - Classic Layout
	 */
	public function display_classic_search_layout() {	?>

		<!-- Classic Search Layout -->
		<div id="epkb-ml-search-classic-layout">    <?php
			$this->display_search_title();
			$collection_id_attr = ' data-collection-id="' . $this->kb_config['kb_ai_collection_id'] . '"';  ?>
			<form id="epkb-ml-search-form" class="epkb-ml-search-input-height--<?php echo esc_attr( $this->kb_config['search_box_input_height'] ); ?>" method="get" onsubmit="return false;"<?php echo $this->is_kb_block ? ' ' . 'data-kb-block-post-id="' . (int)get_the_ID() . '"' : ''; ?><?php echo EPKB_AI_Utilities::is_ai_search_advanced_enabled() ? ' data-ai-search-results="1"' : ''; ?><?php echo $collection_id_attr; ?>>
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
			$this->display_search_title();
			$collection_id_attr = ' data-collection-id="' . $this->kb_config['kb_ai_collection_id'] . '"';  ?>
			<form id="epkb-ml-search-form" class="epkb-ml-search-input-height--<?php echo esc_attr( $this->kb_config['search_box_input_height'] ); ?>" method="get" onsubmit="return false;"<?php echo $this->is_kb_block ? ' ' . 'data-kb-block-post-id="' . (int)get_the_ID() . '"' : ''; ?><?php echo EPKB_AI_Utilities::is_ai_search_advanced_enabled() ? ' data-ai-search-results="1"' : ''; ?><?php echo $collection_id_attr; ?>>
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

		// adjust for Article page or Archive page that uses Article page search settings
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

			$prefix = EPKB_Core_Utilities::is_main_page_search( $kb_config ) ? '' : 'article_';

			$output .= '
				#eckb-archive-page-container #eckb-archive-header #epkb-ml__module-search {
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

		} else {    // KB Main Page

			$output .= '
				#epkb-ml__module-search {
					padding-top: ' . intval( $kb_config['search_box_padding_top'] ) . 'px !important;
					padding-bottom: ' . intval( $kb_config['search_box_padding_bottom'] ) . 'px !important;
					background-color: ' . EPKB_Utilities::sanitize_hex_color( $kb_config['search_background_color'] ) . ' !important;
				}';

			$output .= '
				#epkb-ml__module-search .epkb-ml-search-title {
					color: ' . EPKB_Utilities::sanitize_hex_color( $kb_config['search_title_font_color'] ) . ';
				}';
			// Classic Search
			$output .= '
				#epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form {
					max-width: ' . intval( $kb_config['search_box_input_width'] ) . '% !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout .epkb-ml-search-box__input {
					background-color: ' . EPKB_Utilities::sanitize_hex_color( $kb_config['search_text_input_background_color'] ) . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout #epkb-ml-search-form #epkb-ml-search-box {
					background-color: ' . EPKB_Utilities::sanitize_hex_color( $kb_config['search_text_input_border_color'] ) . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-classic-layout .epkb-ml-search-box__btn {
					background-color: ' . EPKB_Utilities::sanitize_hex_color( $kb_config['search_btn_background_color'] ) . ' !important;
				}';
			// Modern Search
			$output .= '
				#epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form {
					max-width: ' . intval( $kb_config['search_box_input_width'] ) . '% !important;
				}
				#epkb-ml__module-search #epkb-ml-search-modern-layout #epkb-ml-search-form #epkb-ml-search-box {
					background-color: ' . EPKB_Utilities::sanitize_hex_color( $kb_config['search_btn_background_color'] ) . ' !important;
				}
				#epkb-ml__module-search #epkb-ml-search-modern-layout .epkb-ml-search-box__input {
					background-color: ' . EPKB_Utilities::sanitize_hex_color( $kb_config['search_text_input_background_color'] ) . ' !important;
				}';
		}

		return $output;
	}
}