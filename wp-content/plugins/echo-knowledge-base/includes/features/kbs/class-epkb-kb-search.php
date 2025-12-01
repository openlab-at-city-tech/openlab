<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Search Knowledge Base
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Search {

	public function __construct() {
		add_action( 'wp_ajax_epkb-search-kb', array( $this, 'search_kb' ) );
		add_action( 'wp_ajax_nopriv_epkb-search-kb', array( $this, 'search_kb' ) );  // users not logged-in should be able to search as well
	}

	/**
	 * Process AJAX search request
	 */
	public function search_kb() {

		// we don't need nonce and permission check here

		$kb_id = empty( $_GET['epkb_kb_id'] ) ? '' : EPKB_Utilities::sanitize_get_id( sanitize_text_field( wp_unslash( $_GET['epkb_kb_id'] ) ) );
		if ( empty( $kb_id) || is_wp_error( $kb_id ) ) {
			wp_die( wp_json_encode( array( 'status' => 'success', 'search_result' => EPKB_Utilities::report_generic_error( 5 ) ) ) );
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		// search block uses its own settings (for kb_search_block_config filter see EP.KB_Abstract_Block::filter_block_config_if_exists())
		$kb_block_post_id = (int)EPKB_Utilities::get( 'kb_block_post_id', 0 );
		if ( $kb_block_post_id && has_filter( 'kb_search_block_config' ) ) {
			$kb_config = apply_filters( 'kb_search_block_config', $kb_config, $kb_block_post_id );
		}

		// search for given keyword(s)
		$search_terms = EPKB_Utilities::get( 'search_words' );
		$result = self::execute_search( $kb_id, $search_terms );
		if ( is_wp_error( $result ) ) {
			wp_die( wp_json_encode( array( 'status' => 'success', 'search_result' => esc_html( $kb_config['min_search_word_size_msg'] ) ) ) );
		}
		if ( empty( $result ) ) {

			$search_result = '
                    <div class="epkb-ml-search-results__no-results">
                        <span class="epkb-ml-search-results__no-results__icon epkbfa epkbfa-exclamation-circle"></span>
                        <span class="epkb-ml-search-results__no-results__text">' . $kb_config['no_results_found'] . '</span>
                    </div>';
                    
			// Add AI search section if enabled and in simple_search mode
			if ( EPKB_AI_Utilities::is_ai_search_simple_enabled() ) {
				ob_start();
				self::display_ai_search_section( $kb_config, 'below' );
				$search_result .= ob_get_clean();
			}

			$not_found_count = EPKB_Utilities::get_kb_option( $kb_id, 'epkb_miss_search_counter', 0 );
			EPKB_Utilities::save_kb_option( $kb_id, 'epkb_miss_search_counter', $not_found_count + 1 );

			wp_die( wp_json_encode( array(	'status' => 'success','search_result' => $search_result	) ) );
		}

		// ensure that links have https if the current schema is https
		set_current_screen('front');

		// wrap search results into HTML
		$search_result = self::display_search_results_html( $result, $kb_config );

		$search_count = EPKB_Utilities::get_kb_option( $kb_id, 'epkb_hit_search_counter', 0 );
		EPKB_Utilities::save_kb_option( $kb_id, 'epkb_hit_search_counter', $search_count + 1 );

		wp_die( wp_json_encode( array( 'status' => 'success', 'search_result' => $search_result ) ) );
	}

	/**
	 * Call WP query to get matching terms (any term OR match)
	 *
	 * @param $kb_id
	 * @param $search_terms
	 * @param int $posts_per_page Number of results to return (default 20)
	 * @return array|WP_Error
	 */
	public static function execute_search( $kb_id, $search_terms, $posts_per_page = 200 ) {	// 200 for AMGR

		$search_terms = stripslashes( $search_terms );
		$search_terms = str_replace('?', '', $search_terms);
		$search_terms = str_replace( array( "\r", "\n" ), '', $search_terms );

		// Normalise typographic quotes/apostrophes to straight ASCII Mobile keyboards often insert U+2018 – U+201D which break MySQL full-text search. Keep it server-side so ALL callers  (widgets, REST, future apps) benefit.
		$search_terms = str_replace(
			array( "\u{2018}", "\u{2019}", "\u{201C}", "\u{201D}" ),
			array( '\'', '\'', '"', '"' ),
			$search_terms
		);

		// require minimum size of search word(s)
		if ( empty( $search_terms ) ) {
			return new WP_Error( 'empty_search_terms', esc_html__( 'Please enter search terms.', 'echo-knowledge-base' ) );
		}

		// add-ons can adjust the search
		if ( has_filter( 'eckb_execute_search_filter' ) ) {
			$result = apply_filters('eckb_execute_search_filter', '', $kb_id, $search_terms );
			if ( is_array( $result ) ) {
				return $result;
			}
		}

		$result = array();
		$search_params = array(
				's' => $search_terms,
				'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
				'ignore_sticky_posts' => true,  // sticky posts will not show at the top
				'posts_per_page' => $posts_per_page,         // limit search results
				'no_found_rows' => true,        // query only posts_per_page rather than finding total nof posts for pagination etc.
				'cache_results' => false,       // don't need that for mostly unique searches
				'orderby' => 'relevance',
				'perm'  => 'readable'           // only show posts that are readable by the current user
		);

		// OLD installation or Access Manager
		$search_params['post_status'] = array( 'publish' );
		if ( EPKB_Utilities::is_amag_on() ) {
			$search_params['post_status'] = array( 'publish', 'private' );
		} else if ( is_user_logged_in() ) {
			$search_params['post_status'] = array( 'publish', 'private' );
		}

		$found_posts_obj = new WP_Query( $search_params );
		if ( ! empty($found_posts_obj->posts) ) {
			$result = $found_posts_obj->posts;
			wp_reset_postdata();
		}

		return $result;
	}

	/**
	 * Returns HTML for given search results
	 *
	 * @param $search_results
	 * @param $kb_config
	 * @return string
	 */
	public static function display_search_results_html( $search_results, $kb_config ) {

		if ( EPKB_Utilities::is_article_search_synced( $kb_config ) || EPKB_Core_Utilities::is_main_page_search( $kb_config ) ) {
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

		$ai_search_enabled = EPKB_AI_Utilities::is_ai_search_simple_enabled();

		// Limit results to 6 if AI is shown below results
		$results_to_show = $search_results;
		if ( $ai_search_enabled ) {
			$results_to_show = array_slice( $search_results, 0, 6 );
		}

		ob_start(); ?>

		<ul class="epkb-ml-search-results-list">    <?php
			foreach ( $results_to_show as $article ) {

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

		// Display AI search below results if configured
		if ( $ai_search_enabled ) {
			self::display_ai_search_section( $kb_config, 'below' );
		}

		return ob_get_clean();
	}

	/**
	 * Display AI Search Section
	 *
	 * @param array $kb_config
	 * @param string $position
	 */
	private static function display_ai_search_section( $kb_config, $position ) {
		// Get AI config to check immediate query setting
		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		$immediate_query = $ai_config['ai_search_immediate_query'] === 'on';
		$display_mode = $immediate_query ? 'auto' : 'button';
		$button_text = empty( $ai_config['ai_search_ask_button_text'] ) ?  __( 'Ask AI?', 'echo-knowledge-base' ) : $ai_config['ai_search_ask_button_text'];

		$section_class = 'epkb-ml-ai-search-section epkb-ml-ai-search-section--' . esc_attr( $position ); ?>

		<div class="<?php echo esc_attr( $section_class ); ?>" data-display-mode="<?php echo esc_attr( $display_mode ); ?>"
		     data-kb-id="<?php echo esc_attr( $kb_config['id'] ); ?>" data-is-admin="<?php echo esc_attr( current_user_can( 'manage_options' ) ? 'true' : 'false' ); ?>">			<?php
			if ( $immediate_query ) { ?>
				<div class='epkb-ml-ai-search-answer'>
					<div class='epkb-ml-ai-search-answer__loading'><?php esc_html_e( 'Retrieving AI answer...', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-ml-ai-search-answer__content" style="display:none;"></div>
					<div class="epkb-ml-ai-search-answer__error" style="display:none;"></div>
				</div>            <?php
			} else { ?>
				<button type='button' class='epkb-ml-ai-search-button'>
					<span class='epkb-ml-ai-search-button__icon epkbfa epkbfa-comments-o' aria-hidden='true'></span>
					<span class='epkb-ml-ai-search-button__text'><?php echo esc_html( $button_text ); ?></span>
				</button>			<?php
			} ?>
		</div>		<?php
	}

	public static function get_search_form_output( $kb_config ) {

		// handle Advanced Search
		if ( EPKB_Utilities::is_advanced_search_enabled( $kb_config ) ) {
			do_action( 'eckb_advanced_search_box', $kb_config );
			return;
		}

		// handle Modular search
		EPKB_Modular_Main_Page::search_module( $kb_config );
	}

	/**
	 * Output inline CSS style based on configuration.
	 *
	 * @param $kb_config
	 * @param string $styles A list of Configuration Setting styles
	 *
	 * @return string
	 */
	public static function get_inline_style( $kb_config, $styles ) {
		return EPKB_Utilities::get_inline_style( $styles, $kb_config );
	}

	/**
	 * Output CSS classes based on configuration.
	 *
	 * @param $kb_config
	 * @param $classes
	 *
	 * @return string
	 */
	public static function get_css_class( $kb_config, $classes ) {
		return EPKB_Utilities::get_css_class( $classes, $kb_config );
	}

	/**
	 * Display a search form for non-modular KB Main Page: core layouts + called by Elegant Layouts
	 *
	 * @param $kb_config
	 */
	public static function get_search_form( $kb_config ) {	// TODO REMOVE 2026

		// SEARCH BOX OFF
		if ( $kb_config['search_layout'] == 'epkb-search-form-0' ) {
			return;
		}

		self::get_search_form_output( $kb_config );
	}
}
