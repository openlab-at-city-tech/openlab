<?php

/**
 * Shortcode - Lists KB articles like FAQ block with drop-down panels.
 *
 * @copyright   Copyright (c) 2022, Echo Plugins
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Faqs_Shortcode {

	public function __construct() {
		add_shortcode( 'epkb-faqs', array( 'EPKB_Faqs_Shortcode', 'output_shortcode' ) );
	}

	/**
	 * Outputs the shortcode content.
	 *
	 * @param array $attributes Shortcode attributes.
	 *      - 'design': Design style of the shortcode.
	 *      - 'title': Title of the shortcode.
	 *      - 'class': Additional CSS classes for user custom CSS
	 *      - 'group_ids': Group IDs
	 *      - 'category_ids': Category IDs. (OLD parameter)
	 *      - 'kb_id': Knowledge base ID. ( OLD parameter )
	 *      - 'preset': Preset configuration. ( OLD parameter )
	 */
	public static function output_shortcode( $attributes ) {
		global $output_kb_faq_shortcode;

		// we are inside nested shortcode
		if ( ! empty( $output_kb_faq_shortcode ) ) {
			return '';
		}

		wp_enqueue_style('epkb-shortcodes');
		wp_enqueue_script('epkb-faq-shortcode-scripts');

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );

		// show FAQs from KB categories only if category ids present
		$category_ids = empty( $attributes['category_ids'] ) ? [] : explode( ',', $attributes['category_ids'] );
		$category_ids = EPKB_Utilities::sanitize_array( $category_ids );
		$faq_group_ids = empty( $attributes['group_ids'] ) ? [] : explode( ',', $attributes['group_ids'] );
		$faq_group_ids = EPKB_Utilities::sanitize_array( $faq_group_ids );
		if ( empty( $faq_group_ids ) && ! empty( $category_ids ) ) {
			return self::display_faq_categories( $kb_config, $attributes );
		}

		return self::display_faq_groups( $kb_config, $faq_group_ids, $attributes );
	}

	private static function display_faq_groups( $kb_config, $faq_group_ids, $attributes ) {

		$design_name = empty( $attributes['design'] ) ? '' : sanitize_text_field( $attributes['design'] );
		$faq_title = empty( $attributes['title'] ) ? '' : esc_html( wp_strip_all_tags( trim( $attributes['title'] ) ) );

		// only get terms that are in $group_ids
		$faq_groups = EPKB_FAQs_Utilities::get_faq_groups( $faq_group_ids, 'include' );
		if ( is_wp_error( $faq_groups ) ) {
			return EPKB_FAQs_Utilities::display_error( $faq_groups->get_error_message() );
		}

		$faq_groups_questions = EPKB_FAQs_Utilities::get_faq_groups_questions( $faq_groups );

		$design_settings = EPKB_FAQs_Utilities::get_design_settings( $design_name );
		$kb_config = array_merge( $kb_config, $design_settings );

		// user can override the design with explicit settings
		$faq_title_alignment = empty( $attributes['title_alignment'] ) ? $kb_config['ml_faqs_title_location'] : esc_html( wp_strip_all_tags( trim( $attributes['title_alignment'] ) ) );
		$kb_config['ml_faqs_title_location'] = in_array( $faq_title_alignment, array( 'none', 'left', 'center', 'right' ) ) ? $faq_title_alignment : 'center';

		$faq_nof_columns = empty( $attributes['number_of_columns'] ) ? $kb_config['faq_nof_columns'] : esc_html( wp_strip_all_tags( trim( $attributes['number_of_columns'] ) ) );
		$kb_config['faq_nof_columns'] = in_array( $faq_nof_columns, array( '1', '2' ) ) ? $faq_nof_columns : '1';

		$faq_value = empty( $attributes['border_mode'] ) ? $kb_config['faq_border_mode'] : esc_html( wp_strip_all_tags( trim( $attributes['border_mode'] ) ) );
		$kb_config['faq_border_mode'] = in_array( $faq_value, array( 'none', 'all_around', 'separator' ) ) ? $faq_value : 'all_around';

		$faq_value = empty( $attributes['icon_type'] ) ? $kb_config['faq_icon_type'] : esc_html( wp_strip_all_tags( trim( $attributes['icon_type'] ) ) );
		$kb_config['faq_icon_type'] = in_array( $faq_value, array( 'icon_plus_box', 'icon_plus_circle', 'icon_plus', 'icon_arrow_caret', 'icon_arrow_angle' ) ) ? $faq_value : 'all_around';

		$faq_value = empty( $attributes['icon_location'] ) ? $kb_config['faq_icon_location'] : esc_html( wp_strip_all_tags( trim( $attributes['icon_location'] ) ) );
		$kb_config['faq_icon_location'] = in_array( $faq_value, array( 'no_icons', 'left', 'right' ) ) ? $faq_value : 'all_around';

		$faq_value = empty( $attributes['compact_mode'] ) ? $kb_config['faq_compact_mode'] : esc_html( wp_strip_all_tags( trim( $attributes['compact_mode'] ) ) );
		$kb_config['faq_compact_mode'] = in_array( $faq_value, array( 'compact_small', 'compact_medium' ) ) ? $faq_value : 'all_around';

		$faq_value = empty( $attributes['open_mode'] ) ? $kb_config['faq_open_mode'] : esc_html( wp_strip_all_tags( trim( $attributes['open_mode'] ) ) );
		$kb_config['faq_open_mode'] = in_array( $faq_value, array( 'accordion_mode', 'toggle_mode', 'show_all_mode' ) ) ? $faq_value : 'all_around';

		$faq_value = empty( $attributes['schema'] ) ? $kb_config['faq_schema_toggle'] : esc_html( wp_strip_all_tags( trim( $attributes['schema'] ) ) );
		$kb_config['faq_schema_toggle'] = in_array( $faq_value, array( 'on', 'off' ) ) ? $faq_value : 'off';

		// colors
		$faq_question_background_color = self::retrieve_shortcode_color( $kb_config['faq_question_background_color'], $attributes, 'question_background_color' );
		$kb_config['faq_question_background_color'] = empty( $faq_question_background_color ) ?: $faq_question_background_color;
		$faq_answer_background_color = self::retrieve_shortcode_color( $kb_config['faq_answer_background_color'], $attributes, 'answer_background_color' );
		$kb_config['faq_answer_background_color'] = empty( $faq_answer_background_color ) ?: $faq_answer_background_color;
		$faq_icon_color = self::retrieve_shortcode_color( $kb_config['faq_icon_color'], $attributes, 'icon_color' );
		$kb_config['faq_icon_color'] = empty( $faq_icon_color ) ?: $faq_icon_color;

		return EPKB_FAQs_Utilities::display_faqs( $kb_config, $faq_groups_questions, $faq_title, true );
	}

	private static function display_faq_categories( $kb_config, $attributes ) {
		global $output_kb_faq_shortcode;

		$kb_id = empty( $attributes['kb_id'] ) ? EPKB_Utilities::get_eckb_kb_id() : EPKB_Utilities::sanitize_int( $attributes['kb_id'] );
		$kb_id = EPKB_Core_Utilities::sanitize_kb_id( $kb_id );

		$kb_config_tmp = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );

		$design_name = empty( $attributes['design'] ) ? '1' : sanitize_text_field( $attributes['design'] );
		$old_preset_name = empty( $attributes['preset'] ) ? '' : sanitize_text_field( $attributes['preset'] );

		// convert old preset to new design if necessary
		switch ( $old_preset_name ) {
			case 'Boxed':
				$design_name = '4';
				break;
			case 'Grey Box':
				$design_name = '5';
				break;
			case 'Grey Box Dark':
				$design_name = '6';
				break;
			default:
				break;
		}

		$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
		$articles_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );

		// for WPML filter categories and articles given active language
		if ( EPKB_Utilities::is_wpml_enabled( $kb_config_tmp ) ) {
			$category_seq_data = EPKB_WPML::apply_category_language_filter( $category_seq_data );
			$articles_seq_data = EPKB_WPML::apply_article_language_filter( $articles_seq_data );
		}

		$stored_ids_obj = new EPKB_Categories_Array( $category_seq_data ); // normalizes the array as well
		$allowed_categories_ids = $stored_ids_obj->get_all_keys();

		// No categories found - message only for admins
		if ( empty( $allowed_categories_ids ) ) {
			return EPKB_FAQs_Utilities::display_error( esc_html__( 'No categories found.', 'echo-knowledge-base' ) );
		}

		// remove epkb filter
		remove_filter( 'the_content', array( 'EPKB_Layouts_Setup', 'get_kb_page_output_hook' ), 99999 );

		// for empty categories parameters show all
		$included_categories = empty( $attributes['category_ids'] ) ? [] : explode( ',', sanitize_text_field( $attributes['category_ids'] ) );
		if ( empty( $included_categories ) ) {
			$included_categories = array_keys( $allowed_categories_ids );
		}

		// get current post id to exclude it from articles to prevent display issues
		global $post;
		$current_post_id = empty( $post ) || empty( $post->ID ) ? 0 : $post->ID;

		// all nested faq shortcodes will be ignored
		$output_kb_faq_shortcode = true;
		$kb_config['$faq_container_class'] = empty( $attributes['class'] ) ? '' : ' ' . sanitize_text_field( $attributes['class'] );

		$faq_groups = [];
		foreach( $included_categories as $include_category_id ) {

			if ( empty( $articles_seq_data[$include_category_id] ) ) {
				continue;
			}

			if ( empty( $allowed_categories_ids[$include_category_id] ) ) {
				continue;
			}

			foreach ( $articles_seq_data[$include_category_id] as $article_id => $article_title) {

				// category title/description or current post
				if ($article_id == 0 || $article_id == 1 || $current_post_id == $article_id) {
					continue;
				}

				// exclude linked articles
				$article = get_post( $article_id );

				// disallow article that failed to retrieve
				if ( empty( $article ) || empty( $article->post_status ) ) {
					unset( $articles_seq_data[$include_category_id][$article_id] );
					continue;
				}

				if ( EPKB_Utilities::is_link_editor( $article ) ) {
					unset( $articles_seq_data[$include_category_id][$article_id] );
					continue;
				}

				// exclude not allowed for Access Manager articles
				if ( !EPKB_Utilities::is_article_allowed_for_current_user( $article_id ) ) {
					unset( $articles_seq_data[$include_category_id][$article_id] );
				}
			}

			// not empty term but with hidden articles for the user
			if ( empty( $articles_seq_data[$include_category_id] ) ) {
				continue;
			}

			$faqs = [];
			foreach( $articles_seq_data[$include_category_id] as $article_id => $article_title ) {

				if ( $article_id == 0 || $article_id == 1 || $current_post_id == $article_id ) {
					continue;
				}

				// second call is cached by wp core, will not create db query
				$article = get_post( $article_id );

				// disallow article that failed to retrieve
				if ( empty( $article ) || empty( $article->post_status ) ) {
					continue;
				}

				// ignore password-protected pages
				if ( ! empty( $article->post_password ) ) {
					continue;
				}

				if ( $kb_config_tmp['faq_shortcode_content_mode'] == 'excerpt' ) {
					$post_content = $article->post_excerpt;
				} else {
					$post_content = $article->post_content;
				}

				$article->post_content = $post_content;
				$article->post_title = get_the_title( $article );

				$faqs[] = $article;
			}

			$faq_groups[$include_category_id] = ['title' => $articles_seq_data[$include_category_id][0], 'faqs' => $faqs];
		}

		$design_settings = EPKB_FAQs_Utilities::get_design_settings( $design_name );
		$kb_config = array_merge( $kb_config, $design_settings );

		$output = EPKB_FAQs_Utilities::display_faqs( $kb_config, $faq_groups, '', true, true );

		$output_kb_faq_shortcode = false;

		// add epkb filter back
		add_filter( 'the_content', array( 'EPKB_Layouts_Setup', 'get_kb_page_output_hook' ), 99999 );

		return $output;
	}

	private static function retrieve_shortcode_color( $setting_value, $attributes, $name ) {

		$value = empty( $attributes[$name] ) ? $setting_value : esc_html( wp_strip_all_tags( trim( $attributes[$name] ) ) );

		// Check for a hex color string '#c1c2b4'
		if ( preg_match('/^#[a-f0-9]{6}$/i', $value) ) {
			return $value;
		}

		// Check for a hex color string without hash 'c1c2b4'
		else if ( preg_match('/^[a-f0-9]{6}$/i', $value) ) {
			return '#' . $value;
		}

		return '';
	}
}
