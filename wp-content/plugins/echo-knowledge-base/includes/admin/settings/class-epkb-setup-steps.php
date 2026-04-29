<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * KB Setup Steps - Progressive onboarding checklist for Help Resources page
 */
class EPKB_Setup_Steps {

	const USER_META_KEY = 'epkb_setup_steps_done';
	const STEPS_PER_PAGE = 6;
	const LATER_DELAY_DAYS = 14;

	/**
	 * Step configurations (URLs, conditions, selectors). Translatable text is in get_step_texts().
	 */
	// Step categories: 'setup', 'basic', 'advanced'
	const CATEGORY_SETUP = 'setup';
	const CATEGORY_BASIC = 'basic';
	const CATEGORY_ADVANCED = 'advanced';

	private static $steps = array(

		// ========== SETUP CATEGORY ==========
		'add_articles' => array(
			'category'       => 'setup',
			'show_me'        => array(
				array(
					'target_page'    => 'inline_pointer',
					'target_element' => '#menu-posts-epkb_post_type_1 .wp-submenu a[href*="post-new.php?post_type=epkb_post_type_1"], #menu-posts-epkb_post_type_1 a[href*="post-new.php?post_type=epkb_post_type_1"], #menu-posts-epkb_post_type_1 > a.menu-top',
				),
			),
			'learn_more_url' => '',
			'keywords'       => array( 'article', 'content', 'create', 'add', 'write', 'new' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 10,
		),
		'import_articles' => array(
			'category'       => 'setup',
			'show_me'        => array(
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-configuration&active_tab=tools&active_sub_tab=convert#tools__import',
					'target_element' => '#epie_import_data_csv',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/scenarios-overview/',
			'keywords'       => array( 'import', 'convert', 'migrate', 'posts', 'content' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 20,
			'optional'       => true,
		),
		'add_categories' => array(
			'category'       => 'setup',
			'show_me'        => array(
				array(
					'target_page'    => 'edit-tags.php?taxonomy=epkb_post_type_1_category&post_type=epkb_post_type_1',
					'target_element' => '#tag-name',
				),
				array(
					'target_element' => 'input[name="epkb_category_is_draft"] + span, label input[name="epkb_category_is_draft"]',
				),
				array(
					'target_element' => '.epkb-categories-icons__tabs-header',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/categories/',
			'keywords'       => array( 'category', 'organize', 'structure', 'taxonomy', 'order', 'icon', 'image', 'draft' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 30,
		),
		'update_kb_url' => array(
			'category'       => 'setup',
			'show_me'        => array(
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-configuration&active_tab=urls#kb-url',
					'target_element' => '#epkb-kb-location-box, #epkb-admin__boxes-list__kb-url, #kb-url, .epkb-admin__boxes-list__box__kb-location',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/changing-permalinks-urls-and-slugs/',
			'keywords'       => array( 'url', 'slug', 'permalink', 'path', 'link' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 40,
		),
		'switch_theme_template' => array(
			'category'       => 'setup',
			'show_me'        => array(
				array(
					'target_page'      => 'frontend_editor',
					'target_element'   => '#templates_for_kb_group',
					'frontend_feature' => 'main-page-settings',
					'frontend_section' => 'theme-compatibility-mode',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/',
			'keywords'       => array( 'template', 'theme', 'kb template', 'current theme', 'switch', 'layout' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 45,
		),
		'run_setup_wizard' => array(
			'category'       => 'setup',
			'show_me'        => array(
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-configuration&setup-wizard-on',
					'target_element' => '.epkb-setup-wizard-dialog',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/setup-wizard/',
			'keywords'       => array( 'wizard', 'setup', 'layout', 'design', 'colors', 'theme', 'style' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 50,
			'optional'       => true,
		),

		// ========== BASIC FEATURES CATEGORY ==========
		'fine_tune_design' => array(
			'category'       => 'basic',
			'show_me'        => array(
				array(
					'target_page'    => 'frontend_editor',
					'target_element' => '.epkb-fe__features-list',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/frontend-editor/',
			'keywords'       => array( 'design', 'customize', 'editor', 'frontend', 'visual', 'colors', 'fonts' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 60,
		),
		'customize_category_page' => array(
			'category'       => 'basic',
			'show_me'        => array(
				array(
					'target_page'    => 'category_archive_page',
					'target_element' => '.epkb-category-archive-container, .eckb-categories-archive',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/category-archive-page/',
			'keywords'       => array( 'archive', 'category page', 'layout', 'header', 'description' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 70,
		),
		'configure_article_sidebar' => array(
			'category'       => 'basic',
			'show_me'        => array(
				array(
					'target_page'      => 'frontend_editor_article',
					'target_element'   => '#categories_layout_list_mode_group',
					'frontend_feature' => 'article-page-sidebar',
					'frontend_section' => 'article_sidebar_categories_and_articles_navigation',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/article-sidebars/',
			'keywords'       => array( 'sidebar', 'article', 'navigation', 'toc', 'table of contents' ),
			'condition'      => array( 'type' => 'articles_min', 'count' => 1 ),
			'priority'       => 80,
		),
		'setup_faqs' => array(
			'category'       => 'basic',
			'show_me'        => array(
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-faqs',
					'target_element' => '.epkb-admin__top-panel__item[data-target="faqs-overview"]',
				),
				array(
					'target_element' => '.epkb-admin__top-panel__item[data-target="faqs-questions"]',
				),
				array(
					'target_element' => '.epkb-admin__top-panel__item[data-target="faqs-groups"]',
				),
				array(
					'target_element' => '.epkb-admin__top-panel__item[data-target="faq-shortcodes"]',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/faqs/',
			'keywords'       => array( 'faq', 'questions', 'answers', 'frequently' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 90,
		),
		'setup_glossary' => array(
			'category'       => 'basic',
			'show_me'        => array(
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-glossary',
					'target_element' => '.epkb-admin__top-panel__item[data-target="glossary-introduction"]',
				),
				array(
					'target_element' => '.epkb-admin__top-panel__item[data-target="glossary-terms"]',
				),
				array(
					'target_element' => '.epkb-admin__top-panel__item[data-target="glossary-settings"]',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/glossary/',
			'keywords'       => array( 'glossary', 'terms', 'definitions', 'tooltips', 'highlight' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 95,
		),
		'configure_article_views' => array(
			'category'       => 'basic',
			'show_me'        => array(
				array(
					'target_page'      => 'frontend_editor_article',
					'target_element'   => '#article_views_counter_enable_group',
					'frontend_feature' => 'article-page-settings',
					'frontend_section' => 'article_views_counter',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/article-views-counter/',
			'keywords'       => array( 'views', 'counter', 'popularity', 'tracking', 'analytics' ),
			'condition'      => array( 'type' => 'articles_min', 'count' => 1 ),
			'priority'       => 100,
		),
		'setup_ai_chat' => array(
			'category'       => 'basic',
			'show_me'        => array(
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=chat&active_sub_tab=chat-settings',
					'target_element' => '.epkb-ai-tabs-nav .epkbfa-cogs, .epkb-ai-tabs-nav .epkb-ai-tab-button .epkbfa-cogs',
				),
				array(
					'target_element' => '.epkb-ai-tabs-nav .epkbfa-database, .epkb-ai-tabs-nav .epkb-ai-tab-button .epkbfa-database',
				),
				array(
					'target_element' => '.epkb-ai-sub-tab-button.epkb-ai-sub-tab-chat-settings, #epkb-ai-admin-react-root .epkb-ai-sub-tab-button.epkb-ai-sub-tab-chat-settings',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/setup-ai-provider-and-key/',
			'keywords'       => array( 'ai', 'chat', 'chatbot', 'assistant', 'conversation' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 110,
		),
		'setup_ai_search' => array(
			'category'       => 'basic',
			'show_me'        => array(
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=search&active_sub_tab=search-settings',
					'target_element' => '.epkb-ai-tabs-nav .epkbfa-cogs, .epkb-ai-tabs-nav .epkb-ai-tab-button .epkbfa-cogs',
				),
				array(
					'target_element' => '.epkb-ai-tabs-nav .epkbfa-database, .epkb-ai-tabs-nav .epkb-ai-tab-button .epkbfa-database',
				),
				array(
					'target_element' => '.epkb-ai-sub-tab-button.epkb-ai-sub-tab-search-settings, #epkb-ai-admin-react-root .epkb-ai-sub-tab-button.epkb-ai-sub-tab-search-settings',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/setup-ai-provider-and-key/',
			'keywords'       => array( 'ai', 'search', 'smart', 'intelligent', 'find' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 120,
		),
		'order_categories_articles' => array(
			'category'       => 'basic',
			'show_me'        => array(
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-configuration&active_tab=tools&active_sub_tab=ordering#ordering',
					'target_element' => '.epkb-wizard-ordering-selection-container, #eckb-wizard-ordering__page',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/order-articles-and-categories/',
			'keywords'       => array( 'order', 'ordering', 'arrange', 'sort', 'sequence', 'position' ),
			'condition'      => array( 'type' => 'core' ),
			'priority'       => 130,
		),

		// ========== ADVANCED FEATURES CATEGORY ==========
		'setup_featured_articles' => array(
			'category'       => 'advanced',
			'show_me'        => array(
				array(
					'target_page'      => 'frontend_editor',
					'target_element'   => '#ml_articles_list_title_text_group',
					'frontend_feature' => 'articles_list',
					'frontend_section' => 'module-settings',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/articles-list/',
			'keywords'       => array( 'featured', 'popular', 'important', 'highlight', 'articles list' ),
			'condition'      => array( 'type' => 'articles_min', 'count' => 3 ),
			'priority'       => 140,
		),
		'setup_access_restrictions' => array(
			'category'       => 'advanced',
			'show_me'        => array(
				array(
					'target_page'    => 'https://www.echoknowledgebase.com/wordpress-plugin/access-manager/',
					'target_element' => '',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/wordpress-plugin/access-manager/',
			'keywords'       => array( 'access', 'restrictions', 'permissions', 'roles', 'private' ),
			'condition'      => array( 'type' => 'addon_active', 'addon' => 'access-manager' ),
			'priority'       => 150,
			'optional'       => true,
		),
		'run_content_analysis' => array(
			'category'       => 'advanced',
			'show_me'        => array(
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=content-analysis',
					'target_element' => '.epkb-ai-tabs-action-group .epkb-ai-tab-button:first-child, .epkb-ai-tabs-nav .epkb-ai-tab-button:first-child',
				),
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=content-analysis',
					'target_element' => '.epkb-ai-tabs-action-group .epkb-ai-tab-button:nth-child(2), .epkb-ai-tabs-nav .epkb-ai-tab-button:nth-child(2)',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/content-analysis/',
			'keywords'       => array( 'analysis', 'content', 'readability', 'seo', 'quality' ),
			'condition'      => array( 'type' => 'articles_min', 'count' => 3 ),
			'priority'       => 160,
		),
		'setup_multilanguage' => array(
			'category'       => 'advanced',
			'show_me'        => array(
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-configuration#tools__other',
					'target_element' => 'label[for="wpml_is_enabled"], #wpml_is_enabled',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/translate-text/',
			'keywords'       => array( 'language', 'translation', 'wpml', 'polylang', 'multilingual' ),
			'condition'      => array( 'type' => 'plugin_active', 'plugin' => 'wpml_or_polylang' ),
			'priority'       => 170,
			'optional'       => true,
		),
		'setup_article_rating_feedback' => array(
			'category'       => 'advanced',
			'show_me'        => array(
				array(
					'target_page'    => 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/',
					'target_element' => '',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/article-rating-feedback-overview/',
			'keywords'       => array( 'rating', 'feedback', 'like', 'stars', 'analytics' ),
			'condition'      => array( 'type' => 'addon_active', 'addon' => 'article-rating' ),
			'priority'       => 180,
		),
		'csv_import_export' => array(
			'category'       => 'advanced',
			'show_me'        => array(
				array(
					'target_page'    => 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-configuration&active_tab=tools&active_sub_tab=export#tools__export',
					'target_element' => '#tools__export, #epie_export_data_csv, #epie_import_data_csv',
				),
			),
			'learn_more_url' => '',
			'keywords'       => array( 'csv', 'import', 'export', 'backup', 'migrate' ),
			'condition'      => array( 'type' => 'addon_active', 'addon' => 'import-export' ),
			'priority'       => 190,
			'optional'       => true,
		),
		'setup_article_links' => array(
			'category'       => 'advanced',
			'show_me'        => array(
				array(
					'target_page'    => 'https://www.echoknowledgebase.com/demo-10-knowledge-base-add-ons/',
					'target_element' => '',
				),
			),
			'learn_more_url' => 'https://www.echoknowledgebase.com/documentation/links-editor-overview/',
			'keywords'       => array( 'links', 'pdf', 'external', 'resource', 'button' ),
			'condition'      => array( 'type' => 'addon_active', 'addon' => 'links-editor' ),
			'priority'       => 200,
		),
	);

	public function __construct() {
		add_action( 'wp_ajax_epkb_mark_setup_step_done', array( $this, 'ajax_mark_step_done' ) );
		add_action( 'wp_ajax_epkb_restore_setup_step', array( $this, 'ajax_restore_setup_step' ) );
		add_action( 'wp_ajax_epkb_reset_setup_steps', array( $this, 'ajax_reset_setup_steps' ) );
	}

	/**
	 * Get all step definitions
	 */
	public static function get_all_steps() {
		return self::$steps;
	}

	/**
	 * Get category order for progressive disclosure
	 */
	public static function get_category_order() {
		return array( self::CATEGORY_SETUP, self::CATEGORY_BASIC, self::CATEGORY_ADVANCED );
	}

	/**
	 * Get translated category label
	 */
	public static function get_category_label( $category ) {
		$labels = array(
			self::CATEGORY_SETUP    => __( 'Setup', 'echo-knowledge-base' ),
			self::CATEGORY_BASIC    => __( 'Basic Features', 'echo-knowledge-base' ),
			self::CATEGORY_ADVANCED => __( 'Advanced Features', 'echo-knowledge-base' ),
		);
		return isset( $labels[ $category ] ) ? $labels[ $category ] : $category;
	}

	/**
	 * Get steps filtered by category
	 */
	public static function get_steps_by_category( $category ) {
		$steps = array();
		foreach ( self::$steps as $key => $step ) {
			if ( isset( $step['category'] ) && $step['category'] === $category ) {
				$steps[ $key ] = $step;
			}
		}
		return $steps;
	}

	/**
	 * Get eligible steps for a category (excluding transition steps)
	 */
	public static function get_eligible_steps_for_category( $category, $kb_id ) {
		$steps = self::get_steps_by_category( $category );
		$eligible = array();
		foreach ( $steps as $key => $step ) {
			// Skip transition steps in count
			if ( ! empty( $step['is_transition'] ) ) {
				continue;
			}
			if ( self::check_condition( $step['condition'], $kb_id ) ) {
				$eligible[ $key ] = $step;
			}
		}
		return $eligible;
	}

	/**
	 * Check if all steps in a category are completed
	 */
	public static function is_category_complete( $category, $kb_id ) {
		$eligible_steps = self::get_eligible_steps_for_category( $category, $kb_id );
		$completed_keys = self::get_completed_step_keys();

		foreach ( $eligible_steps as $key => $step ) {
			if ( ! in_array( $key, $completed_keys ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Get the current active category based on completion status
	 * Returns the first category that has visible steps
	 */
	public static function get_current_category( $kb_id ) {
		$categories = self::get_category_order();

		foreach ( $categories as $category ) {
			if ( ! empty( self::get_visible_steps_for_category( $category, $kb_id ) ) ) {
				return $category;
			}
		}

		foreach ( $categories as $category ) {
			if ( ! self::is_category_complete( $category, $kb_id ) ) {
				return $category;
			}
		}
		// All categories complete, return the last one
		return end( $categories );
	}

	/**
	 * Get visible steps for a specific category
	 */
	private static function get_visible_steps_for_category( $category, $kb_id ) {
		$completed_keys = self::get_completed_step_keys();
		$visible = array();

		// Get steps for current category
		$category_steps = self::get_steps_by_category( $category );

		// Sort by priority
		uasort( $category_steps, function( $a, $b ) {
			return $a['priority'] - $b['priority'];
		} );

		foreach ( $category_steps as $key => $step ) {
			// Check condition is met
			if ( ! self::check_condition( $step['condition'], $kb_id ) ) {
				continue;
			}

			// Skip completed steps
			if ( in_array( $key, $completed_keys ) ) {
				continue;
			}

			$visible[ $key ] = $step;
		}

		return $visible;
	}

	/**
	 * Get the next category after the given one
	 */
	public static function get_next_category( $current_category ) {
		$categories = self::get_category_order();
		$current_index = array_search( $current_category, $categories );
		if ( $current_index !== false && isset( $categories[ $current_index + 1 ] ) ) {
			return $categories[ $current_index + 1 ];
		}
		return null;
	}

	/**
	 * Check if all categories are complete
	 */
	public static function are_all_categories_complete( $kb_id ) {
		$categories = self::get_category_order();
		foreach ( $categories as $category ) {
			if ( ! self::is_category_complete( $category, $kb_id ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Check if all categories are completed with "done" steps only ("later" does not count)
	 */
	private static function are_all_categories_done( $kb_id ) {
		$categories = self::get_category_order();
		$done_keys = self::get_done_step_keys();

		foreach ( $categories as $category ) {
			$eligible_steps = self::get_eligible_steps_for_category( $category, $kb_id );
			foreach ( $eligible_steps as $key => $step ) {
				if ( ! in_array( $key, $done_keys, true ) ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Get progress data for a specific category
	 */
	public static function get_category_progress( $category, $kb_id ) {
		$eligible_steps = self::get_eligible_steps_for_category( $category, $kb_id );
		$completed_keys = self::get_completed_step_keys();

		$total = count( $eligible_steps );
		$completed = 0;
		foreach ( $eligible_steps as $key => $step ) {
			if ( in_array( $key, $completed_keys ) ) {
				$completed++;
			}
		}

		return array(
			'total'     => $total,
			'completed' => $completed,
			'percent'   => $total > 0 ? round( ( $completed / $total ) * 100 ) : 0,
		);
	}

	/**
	 * Get visible steps for current category (progressive disclosure)
	 * Shows incomplete steps from current category only
	 */
	public static function get_visible_steps_by_category( $kb_id ) {
		$current_category = self::get_current_category( $kb_id );
		return self::get_visible_steps_for_category( $current_category, $kb_id );
	}

	/**
	 * Get completed step keys for current user (returns array of step_key => mode pairs or just keys for backward compatibility)
	 * @param bool $with_mode If true, returns array with step_key => mode format
	 * @return array
	 */
	public static function get_completed_step_keys( $with_mode = false ) {
		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return array();
		}

		$completed = get_user_meta( $user_id, self::USER_META_KEY, true );
		if ( ! is_array( $completed ) ) {
			return array();
		}

		// Handle backward compatibility: old format was simple array of keys, new format is key => mode
		$is_new_format = self::is_new_storage_format( $completed );
		$needs_save = false;

		// Convert legacy format to new associative array with mode/defer structure
		if ( ! $is_new_format ) {
			$converted = array();
			foreach ( $completed as $step_key ) {
				$converted[ $step_key ] = array(
					'mode'        => 'done',
					'defer_until' => 0,
				);
			}
			$completed = $converted;
			$needs_save = true;
		}

		list( $normalized_completed, $normalized_changed ) = self::normalize_completed_entries( $completed );

		if ( $needs_save || $normalized_changed ) {
			update_user_meta( $user_id, self::USER_META_KEY, $normalized_completed );
		}

		if ( $with_mode ) {
			return $normalized_completed;
		}

		// Return just the keys (for backward compatibility)
		return array_keys( $normalized_completed );
	}

	/**
	 * Get completed step keys with "done" mode only
	 *
	 * @return array
	 */
	private static function get_done_step_keys() {
		$completed = self::get_completed_step_keys( true );
		$done_keys = array();

		foreach ( $completed as $step_key => $entry ) {
			if ( isset( $entry['mode'] ) && $entry['mode'] === 'done' ) {
				$done_keys[] = $step_key;
			}
		}

		return $done_keys;
	}

	/**
	 * Check if the storage format is the new key => mode format
	 */
	private static function is_new_storage_format( $completed ) {
		if ( empty( $completed ) ) {
			return false;
		}
		// New format has string keys (step_key) and string values (mode)
		// Old format has numeric keys and string values (step_key)
		$first_key = array_key_first( $completed );
		return ! is_numeric( $first_key );
	}

	/**
	 * Normalize completed steps array and remove expired "later" entries
	 *
	 * @param array $completed
	 *
	 * @return array[] Array with normalized data and flag indicating whether update is needed
	 */
	private static function normalize_completed_entries( $completed ) {
		$normalized = array();
		$has_changes = false;
		$now = time();

		if ( empty( $completed ) || ! is_array( $completed ) ) {
			return array( $normalized, false );
		}

		foreach ( $completed as $step_key => $value ) {
			$entry = self::normalize_completed_entry( $value );

			// Skip expired "later" entries so they can reappear
			if ( $entry['mode'] === 'later' && ! empty( $entry['defer_until'] ) && $entry['defer_until'] <= $now ) {
				$has_changes = true;
				continue;
			}

			if ( ! is_array( $value ) || $value !== $entry ) {
				$has_changes = true;
			}

			$normalized[ $step_key ] = $entry;
		}

		return array( $normalized, $has_changes );
	}

	/**
	 * Normalize a single completed entry value to unified array format
	 *
	 * @param mixed $value
	 *
	 * @return array
	 */
	private static function normalize_completed_entry( $value ) {
		$entry = array(
			'mode'        => 'done',
			'defer_until' => 0,
		);

		if ( is_array( $value ) ) {
			if ( isset( $value['mode'] ) ) {
				$entry['mode'] = $value['mode'];
			}
			if ( isset( $value['defer_until'] ) ) {
				$entry['defer_until'] = (int) $value['defer_until'];
			}
		} else {
			$entry['mode'] = $value;
		}

		$entry['mode'] = in_array( $entry['mode'], array( 'done', 'later' ), true ) ? $entry['mode'] : 'done';
		$entry['defer_until'] = max( 0, (int) $entry['defer_until'] );

		return $entry;
	}

	/**
	 * Get completed steps with full configuration
	 */
	public static function get_completed_steps() {
		$completed_keys = self::get_completed_step_keys( true );
		$completed_steps = array();

		foreach ( $completed_keys as $step_key => $mode_info ) {
			if ( isset( self::$steps[ $step_key ] ) ) {
				// Skip transition steps - they shouldn't appear in completed list
				if ( ! empty( self::$steps[ $step_key ]['is_transition'] ) ) {
					continue;
				}
				$completed_steps[ $step_key ] = self::$steps[ $step_key ];
				$completed_steps[ $step_key ]['completed_mode'] = $mode_info['mode'];
			}
		}

		return $completed_steps;
	}

	/**
	 * Check if a step's condition is met
	 */
	public static function check_condition( $condition, $kb_id ) {

		if ( empty( $condition ) || ! is_array( $condition ) ) {
			return true;
		}

		$type = isset( $condition['type'] ) ? $condition['type'] : 'core';

		switch ( $type ) {
			case 'core':
				return true;

			case 'wizard_not_completed':
				return ! self::is_wizard_completed( $kb_id );

			case 'articles_min':
				$required_count = isset( $condition['count'] ) ? (int) $condition['count'] : 1;
				$article_count = EPKB_Articles_DB::get_count_of_all_kb_articles( $kb_id );
				return $article_count >= $required_count;

			case 'addon_active':
				$addon = isset( $condition['addon'] ) ? $condition['addon'] : '';
				switch ( $addon ) {
					case 'access-manager':
						return EPKB_Utilities::is_amag_on();
					case 'elegant-layouts':
						return EPKB_Utilities::is_elegant_layouts_enabled();
					case 'article-rating':
						return EPKB_Utilities::is_article_rating_enabled();
					case 'import-export':
						return EPKB_Utilities::is_export_import_enabled();
					case 'links-editor':
						return EPKB_Utilities::is_link_editor_enabled();
					default:
						return false;
				}

			case 'plugin_active':
				$plugin = isset( $condition['plugin'] ) ? $condition['plugin'] : '';
				switch ( $plugin ) {
					case 'wpml':
						return EPKB_Utilities::is_wpml_plugin_active();
					case 'wpml_or_polylang':
						return EPKB_Utilities::is_wpml_plugin_active() || function_exists( 'pll_current_language' );
					default:
						return false;
				}

			default:
				return true;
		}
	}

	/**
	 * Get visible steps (next 8 eligible incomplete steps)
	 */
	public static function get_visible_steps( $kb_id ) {
		$completed_keys = self::get_completed_step_keys();
		$visible_steps = array();

		// Sort steps by priority
		$sorted_steps = self::$steps;
		uasort( $sorted_steps, function( $a, $b ) {
			return $a['priority'] - $b['priority'];
		} );

		foreach ( $sorted_steps as $step_key => $step ) {
			// Skip completed steps
			if ( in_array( $step_key, $completed_keys ) ) {
				continue;
			}

			// Skip steps where condition is not met
			if ( ! self::check_condition( $step['condition'], $kb_id ) ) {
				continue;
			}

			$visible_steps[ $step_key ] = $step;

			// Limit to STEPS_PER_PAGE
			if ( count( $visible_steps ) >= self::STEPS_PER_PAGE ) {
				break;
			}
		}

		return $visible_steps;
	}

	/**
	 * Get total eligible steps count (for progress display)
	 */
	public static function get_total_eligible_steps( $kb_id ) {
		$count = 0;

		foreach ( self::$steps as $step_key => $step ) {
			if ( self::check_condition( $step['condition'], $kb_id ) ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Get completed eligible steps count
	 */
	public static function get_completed_eligible_count( $kb_id ) {
		$completed_keys = self::get_done_step_keys();
		$count = 0;

		foreach ( self::$steps as $step_key => $step ) {
			if ( in_array( $step_key, $completed_keys ) && self::check_condition( $step['condition'], $kb_id ) ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Mark a step as done or later
	 * @param string $step_key
	 * @param string $mode 'done' or 'later'
	 * @param int    $defer_until Timestamp for when a "later" step can reappear
	 */
	public static function mark_step_done( $step_key, $mode = 'done', $defer_until = 0 ) {
		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return false;
		}

		if ( ! isset( self::$steps[ $step_key ] ) ) {
			return false;
		}

		// Validate mode
		$mode = in_array( $mode, array( 'done', 'later' ) ) ? $mode : 'done';

		// Get completed steps with mode
		$completed = self::get_completed_step_keys( true );

		// Add or update the step with its mode
		$completed[ $step_key ] = array(
			'mode'        => $mode,
			'defer_until' => $mode === 'later' ? max( 0, (int) $defer_until ) : 0,
		);
		update_user_meta( $user_id, self::USER_META_KEY, $completed );

		return true;
	}

	/**
	 * AJAX handler to mark step as done or later
	 */
	public function ajax_mark_step_done() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$step_key = EPKB_Utilities::post( 'step_key' );
		if ( empty( $step_key ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid step', 'echo-knowledge-base' ) );
		}

		// Get completion mode (done or later)
		$mode = EPKB_Utilities::post( 'mode', 'done' );
		$mode = in_array( $mode, array( 'done', 'later' ) ) ? $mode : 'done';

		// Get KB ID
		$kb_id = EPKB_Utilities::post( 'kb_id', EPKB_KB_Config_DB::DEFAULT_KB_ID );

		// Get category BEFORE marking done (to detect category change)
		$category_before = self::get_current_category( $kb_id );

		// Get visible steps BEFORE marking done (to find the new step that will appear)
		$visible_steps_before = array_keys( self::get_visible_steps_by_category( $kb_id ) );

		$result = self::mark_step_done( $step_key, $mode );
		if ( ! $result ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not mark step', 'echo-knowledge-base' ) );
		}

		// Get category AFTER marking done
		$category_after = self::get_current_category( $kb_id );

		// Check if category changed (completed current category and moved to next)
		$category_changed = ( $category_before !== $category_after );

		// Get updated data for response (use category-based progress)
		$category_progress = self::get_category_progress( $category_after, $kb_id );
		$completed_count = $category_progress['completed'];
		$total_count = $category_progress['total'];
		$all_complete = self::are_all_categories_done( $kb_id );
		$visible_steps = self::get_visible_steps_by_category( $kb_id );
		$visible_steps_keys = array_keys( $visible_steps );
		$defer_until = 0;

		// If user clicked Later, set a future date for re-showing
		if ( $mode === 'later' ) {
			$defer_until = time() + self::get_later_delay_seconds();
			self::update_step_defer_until( $step_key, $defer_until );
		}

		// Find the new step that appeared (if any) - it's a step in new list but not in old list
		$new_step_html = '';
		if ( ! $category_changed ) {
			$new_steps = array_diff( $visible_steps_keys, $visible_steps_before );
			if ( ! empty( $new_steps ) ) {
				$new_step_key = reset( $new_steps );
				if ( isset( $visible_steps[$new_step_key] ) ) {
					ob_start();
					self::render_step_item( $new_step_key, $visible_steps[$new_step_key], $kb_id, count( $visible_steps_keys ), false );
					$new_step_html = ob_get_clean();
				}
			}
		}

		wp_send_json_success( array(
			'message'          => $mode === 'later' ? __( 'Step marked for later', 'echo-knowledge-base' ) : __( 'Step marked as done', 'echo-knowledge-base' ),
			'completed_count'  => $completed_count,
			'total_count'      => $total_count,
			'all_complete'     => $all_complete,
			'visible_steps'    => $visible_steps_keys,
			'mode'             => $mode,
			'new_step_html'    => $new_step_html,
			'defer_until'      => $defer_until,
			'category_changed' => $category_changed,
		) );
	}

	/**
	 * AJAX handler to restore a step (unmark as completed)
	 */
	public function ajax_restore_setup_step() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$step_key = EPKB_Utilities::post( 'step_key' );
		if ( empty( $step_key ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid step', 'echo-knowledge-base' ) );
		}

		$result = self::restore_step( $step_key );
		if ( ! $result ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not restore step', 'echo-knowledge-base' ) );
		}

		wp_send_json_success( array( 'message' => __( 'Step restored', 'echo-knowledge-base' ) ) );
	}

	/**
	 * Restore a step (remove from completed list)
	 */
	public static function restore_step( $step_key ) {
		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return false;
		}

		if ( ! isset( self::$steps[ $step_key ] ) ) {
			return false;
		}

		$completed = self::get_completed_step_keys( true );

		// Remove the step from completed list
		if ( isset( $completed[$step_key] ) ) {
			unset( $completed[$step_key] );
			update_user_meta( $user_id, self::USER_META_KEY, $completed );
		}

		return true;
	}

	/**
	 * AJAX handler to reset all completed steps
	 */
	public function ajax_reset_setup_steps() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'User not found', 'echo-knowledge-base' ) );
		}

		delete_user_meta( $user_id, self::USER_META_KEY );

		wp_send_json_success( array( 'message' => __( 'Setup steps reset successfully', 'echo-knowledge-base' ) ) );
	}

	/**
	 * Get debug data for setup steps (for debug file output)
	 */
	public static function get_debug_data( $kb_id ) {
		$all_steps = self::$steps;
		$completed_with_mode = self::get_completed_step_keys( true );

		// Sort by priority
		uasort( $all_steps, function( $a, $b ) {
			return $a['priority'] - $b['priority'];
		} );

		$output = "\n\nSetup Steps Status:\n";
		$output .= "==================\n";

		$step_num = 1;
		foreach ( $all_steps as $step_key => $step ) {
			$is_eligible = self::check_condition( $step['condition'], $kb_id );
			$texts = self::get_step_texts( $step_key );
			$title = $texts['title'];
			$is_optional = ! empty( $step['optional'] );

			// Determine status and mode
			$status = 'Pending';
			$mode = '';
			if ( isset( $completed_with_mode[$step_key] ) ) {
				$mode = is_array( $completed_with_mode[ $step_key ] ) ? $completed_with_mode[ $step_key ]['mode'] : $completed_with_mode[ $step_key ];
				$status = 'Completed (' . $mode . ')';
			}

			$optional_text = $is_optional ? ' [Optional]' : '';

			$output .= sprintf( "%2d. %s%s\n", $step_num, $title, $optional_text );
			$output .= "    - Status: " . $status . "\n";
			// Only show eligibility if it's 'No'
			if ( ! $is_eligible ) {
				$output .= "    - Eligible: No\n";
			}

			$step_num++;
		}

		// Summary
		$completed_count = self::get_completed_eligible_count( $kb_id );
		$total_count = self::get_total_eligible_steps( $kb_id );
		$output .= "\nSummary: " . $completed_count . "/" . $total_count . " eligible steps completed\n";

		return $output;
	}

	/**
	 * Replace static placeholders with KB specific values
	 *
	 * @param string $value
	 * @param int    $kb_id
	 *
	 * @return string
	 */
	public static function format_target_value( $value, $kb_id ) {

		if ( empty( $value ) ) {
			return $value;
		}

		$kb_id = EPKB_Utilities::is_positive_int( $kb_id ) ? $kb_id : EPKB_KB_Config_DB::DEFAULT_KB_ID;
		$post_type = EPKB_KB_Handler::get_post_type( $kb_id );

		return str_replace( 'epkb_post_type_1', $post_type, $value );
	}

	/**
	 * Get the "Show Me" URL for a step
	 */
	public static function get_show_me_url( $step_key, $kb_id ) {

		if ( ! isset( self::$steps[ $step_key ] ) ) {
			return '';
		}

		$step = self::$steps[ $step_key ];
		if ( empty( $step['show_me'][0]['target_page'] ) ) {
			return '';
		}

		$kb_id = EPKB_Utilities::is_positive_int( $kb_id ) ? $kb_id : EPKB_KB_Config_DB::DEFAULT_KB_ID;
		$target_page = self::format_target_value( $step['show_me'][0]['target_page'], $kb_id );

		// Absolute links (external docs/demos)
		if ( preg_match( '#^https?://#i', $target_page ) ) {
			return $target_page;
		}

		// Handle special cases
		if ( $target_page === 'frontend_editor_article' ) {
			$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
			$article_page_url = EPKB_KB_Handler::get_first_kb_article_url( $kb_config );
			$main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
			$target_url = $article_page_url ? $article_page_url : $main_page_url;
			return $target_url ? add_query_arg(
				array(
					'action'            => 'epkb_load_editor',
					'epkb_show_pointer' => $step_key,
					'epkb_kb_id'        => $kb_id,
				),
				$target_url
			) : '';
		}

		if ( $target_page === 'frontend_editor' ) {
			$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
			$main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
			return $main_page_url ? add_query_arg(
				array(
					'action'           => 'epkb_load_editor',
					'epkb_show_pointer' => $step_key,
					'epkb_kb_id'        => $kb_id,
				),
				$main_page_url
			) : '';
		}

		// Inline pointer - no URL, pointer shows on current page via JavaScript
		if ( $target_page === 'inline_pointer' ) {
			return '';
		}

		// Show frontend category archive page
		if ( $target_page === 'category_archive_page' ) {
			$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
			$category_url = EPKB_KB_Handler::get_kb_category_with_most_articles_url( $kb_config );
			return $category_url ? add_query_arg( 'epkb_show_pointer', $step_key, $category_url ) : '';
		}

		// Regular admin page
		$url = admin_url( $target_page );
		return add_query_arg( 'epkb_show_pointer', $step_key, $url );
	}

	/**
	 * Get action URL for Learn More dialog based on action_page key
	 */
	private static function get_learn_more_action_url( $action_page, $kb_id ) {
		$kb_id = EPKB_Utilities::is_positive_int( $kb_id ) ? $kb_id : EPKB_KB_Config_DB::DEFAULT_KB_ID;

		switch ( $action_page ) {
			case 'add_article':
				return admin_url( 'post-new.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) );
			default:
				return '';
		}
	}

	/**
	 * Check if Setup Wizard was already completed for this KB
	 */
	private static function is_wizard_completed( $kb_id ) {
		return EPKB_Core_Utilities::is_kb_flag_set( 'completed_setup_wizard_' . $kb_id );
	}

	/**
	 * Get the display title for a step (handles dynamic titles and translations)
	 */
	private static function get_step_title( $step_key, $step, $kb_id ) {
		$texts = self::get_step_texts( $step_key );
		$title = $texts['title'];

		// Add '(If Applicable)' suffix for optional steps
		if ( ! empty( self::$steps[$step_key]['optional'] ) ) {
			$title .= ' (' . __( 'If Applicable', 'echo-knowledge-base' ) . ')';
		}

		return $title;
	}

	/**
	 * Get the description for a step
	 */
	private static function get_step_description( $step_key ) {
		$texts = self::get_step_texts( $step_key );
		return $texts['description'];
	}

	/**
	 * Get the delay (in seconds) for later steps to reappear
	 *
	 * @return int
	 */
	private static function get_later_delay_seconds() {
		return (int) ( DAY_IN_SECONDS * self::LATER_DELAY_DAYS );
	}

	/**
	 * Update defer until timestamp for a specific step
	 *
	 * @param string $step_key
	 * @param int    $defer_until
	 */
	private static function update_step_defer_until( $step_key, $defer_until ) {
		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return;
		}

		$completed = self::get_completed_step_keys( true );
		if ( empty( $completed[ $step_key ] ) ) {
			return;
		}

		$entry = self::normalize_completed_entry( $completed[ $step_key ] );
		$entry['mode'] = 'later';
		$entry['defer_until'] = max( 0, (int) $defer_until );

		$completed[ $step_key ] = $entry;
		update_user_meta( $user_id, self::USER_META_KEY, $completed );
	}

	/**
	 * Build bullet-style descriptions while keeping bullet markers outside translation strings.
	 *
	 * @param string $intro   Introductory sentence (can include punctuation).
	 * @param array  $items   Bullet point strings.
	 * @param string $closing Optional closing sentence appended after bullets.
	 *
	 * @return string
	 */
	private static function format_bullet_description( $intro, $items, $closing = '' ) {
		$items = array_filter( array_map( 'trim', (array) $items ) );
		$intro = trim( $intro );
		$closing = trim( $closing );

		$description = $intro;

		if ( ! empty( $items ) ) {
			// Always prefix with • so JS treats first item as bullet too
			$bullet_prefix = ( $description === '' ) ? '• ' : ' • ';
			$description .= $bullet_prefix . implode( ' • ', $items );
		}

		if ( $closing !== '' ) {
			$description .= ( $description !== '' ? ' ' : '' ) . $closing;
		}

		return trim( $description );
	}

	/**
	 * Get translated texts for a step - literal strings for translation extraction
	 */
	public static function get_step_texts( $step_key ) {
		$texts = array(
			'run_setup_wizard' => array(
				'title'       => __( 'Re-run Setup Wizard', 'echo-knowledge-base' ),
				'description' => __( 'Quickly configure your KB look and feel: choose from multiple layouts (Basic, Classic, Tabs, etc.), pick a color theme, and run anytime to redesign.', 'echo-knowledge-base' ),
				'learn_more'  => array(
					'title'       => __( 'Setup Wizard Overview', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						'',
						array(
							__( 'The Setup Wizard guides you through initial configuration', 'echo-knowledge-base' ),
							__( 'Choose a layout (Basic, Classic, Tabs, Grid, Sidebar, Drill-Down)', 'echo-knowledge-base' ),
							__( 'Select a color scheme or customize colors', 'echo-knowledge-base' ),
							__( 'Configure basic display settings', 'echo-knowledge-base' ),
							__( 'Preview changes before applying', 'echo-knowledge-base' ),
							__( 'Run it anytime to redesign your KB without losing content', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'add_articles' => array(
				'title'       => __( 'Add Article', 'echo-knowledge-base' ),
				'description' => __( 'Create your first article: go to KB Articles → Add New, write your content, assign a category, and click Publish.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'Add New Article', 'echo-knowledge-base' ),
						'content' => __( 'Click here to create a new article for your Knowledge Base.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'Adding Articles to Your Knowledge Base', 'echo-knowledge-base' ),
					'description' => __( 'Articles are the foundation of your Knowledge Base. To add an article: 1) Go to KB Articles → Add New 2) Write your content using the editor 3) Assign one or more categories 4) Add tags for better searchability 5) Click Publish when ready. Each article helps visitors find answers to their questions.', 'echo-knowledge-base' ),
					'action_text' => __( 'Add New Article', 'echo-knowledge-base' ),
					'action_page' => 'add_article',
				),
			),
			'import_articles' => array(
				'title'       => __( 'Import Articles', 'echo-knowledge-base' ),
				'description' => __( 'Already have content elsewhere? Convert WordPress posts/pages to KB articles, import from CSV files, or migrate from other documentation plugins.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'Import Content', 'echo-knowledge-base' ),
						'content' => __( 'Use this section to import posts, pages, or other content types into your Knowledge Base.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'Importing Content into Your KB', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Save time by importing existing content:', 'echo-knowledge-base' ),
						array(
							__( 'Convert WordPress posts or pages to KB articles', 'echo-knowledge-base' ),
							__( 'Import custom post types', 'echo-knowledge-base' ),
							__( 'Migrate from other documentation plugins', 'echo-knowledge-base' ),
							__( 'Preserve categories and tags during import', 'echo-knowledge-base' ),
							__( 'Bulk import multiple items at once.', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'csv_import_export' => array(
				'title'       => __( 'Import / Export Articles via CSV', 'echo-knowledge-base' ),
				'description' => __( 'Import and export articles as CSV or XML.', 'echo-knowledge-base' ),
				'learn_more'  => array(
					'title'       => __( 'CSV Import and Export', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Manage articles via files.', 'echo-knowledge-base' ),
						array(
							__( 'Import articles via CSV or XML', 'echo-knowledge-base' ),
							__( 'Export articles via CSV or XML', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'add_categories' => array(
				'title'       => __( 'Add Categories', 'echo-knowledge-base' ),
				'description' => __( 'Organize your articles into categories. Create top-level and sub-categories, add icons or images, and mark categories as Draft to hide them until ready.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'Create a Category', 'echo-knowledge-base' ),
						'content' => __( 'Categories organize your Knowledge Base articles into logical groups. Create top-level categories and subcategories to help visitors find information quickly.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'Draft Status', 'echo-knowledge-base' ),
						'content' => __( 'Check this to hide the category from visitors while you prepare content. The category will not appear on the KB Main Page until you uncheck this.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'Category Icon', 'echo-knowledge-base' ),
						'content' => __( 'Add a visual icon to your category. Choose Font Icon for built-in icons or Image Icon to upload your own custom image.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'Organizing with Categories', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Categories help visitors navigate your Knowledge Base:', 'echo-knowledge-base' ),
						array(
							__( 'Create top-level categories and subcategories', 'echo-knowledge-base' ),
							__( 'Add font icons or custom images', 'echo-knowledge-base' ),
							__( 'Use Draft status to hide while preparing content', 'echo-knowledge-base' ),
							__( 'Set category descriptions', 'echo-knowledge-base' ),
							__( 'Drag and drop to reorder on the Ordering page.', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'order_categories_articles' => array(
				'title'       => __( 'Order Categories and Articles', 'echo-knowledge-base' ),
				'description' => __( 'Arrange your categories and articles in the order you want them to appear.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'Ordering', 'echo-knowledge-base' ),
						'content' => __( 'Drag and drop categories and articles to set their display order.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'Ordering Categories and Articles', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Control the display order of your content:', 'echo-knowledge-base' ),
						array(
							__( 'Drag and drop categories on the Main Page', 'echo-knowledge-base' ),
							__( 'Reorder articles within each category', 'echo-knowledge-base' ),
							__( 'Choose ordering mode: alphabetical, date-based, or custom', 'echo-knowledge-base' ),
							__( 'Show most important content first', 'echo-knowledge-base' ),
							__( 'Changes apply immediately after saving.', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'customize_category_page' => array(
				'title'       => __( 'Customize Category Page', 'echo-knowledge-base' ),
				'description' => __( 'Configure how category pages look with custom headers, descriptions, and layouts.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'Category Page', 'echo-knowledge-base' ),
						'content' => __( 'This is how your category pages appear to visitors. Customize them in KB settings.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'Category Archive Pages', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Category pages show all articles within a category:', 'echo-knowledge-base' ),
						array(
							__( 'Customize the page header and title', 'echo-knowledge-base' ),
							__( 'Add category descriptions', 'echo-knowledge-base' ),
							__( 'Choose article list style', 'echo-knowledge-base' ),
							__( 'Configure sidebar options', 'echo-knowledge-base' ),
							__( 'Help visitors browse content by topic.', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'fine_tune_design' => array(
				'title'       => __( 'Fine-tune Your Design', 'echo-knowledge-base' ),
				'description' => __( 'Use the visual Frontend Editor to make detailed design adjustments to your KB.', 'echo-knowledge-base' ),
				'learn_more'  => array(
					'title'       => __( 'Using the Frontend Editor', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'The Frontend Editor lets you customize your KB visually:', 'echo-knowledge-base' ),
						array(
							__( 'Change colors, fonts, and spacing', 'echo-knowledge-base' ),
							__( 'See live previews as you edit', 'echo-knowledge-base' ),
							__( 'Configure Main Page, Article Page, and Category Archive designs', 'echo-knowledge-base' ),
							__( 'Access from admin bar -> "Edit KB Design"', 'echo-knowledge-base' ),
							__( 'No coding required.', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'update_kb_url' => array(
				'title'       => __( 'Update KB URL', 'echo-knowledge-base' ),
				'description' => __( 'Customize your KB URL structure: change the default /knowledge-base/ slug, modify article URL paths, and configure permalinks for SEO.', 'echo-knowledge-base' ),
				'learn_more'  => array(
					'title'       => __( 'Configuring KB URLs', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Customize your KB URL structure:', 'echo-knowledge-base' ),
						array(
							__( 'Change the main slug (e.g., /help/ or /docs/)', 'echo-knowledge-base' ),
							__( 'Include or exclude category in article URLs', 'echo-knowledge-base' ),
							__( 'Configure for SEO-friendly permalinks', 'echo-knowledge-base' ),
							__( 'Permalinks refresh automatically after changes.', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'switch_theme_template' => array(
				'title'       => __( 'Switch Between KB and Current Theme Template', 'echo-knowledge-base' ),
				'description' => __( 'Choose between KB Template (full control over styling) or Current Theme Template (inherits your theme\'s design).', 'echo-knowledge-base' ),
				'learn_more'  => array(
					'title'       => __( 'KB Template vs Current Theme Template', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Choose the right template for your needs:', 'echo-knowledge-base' ),
						array(
							__( 'KB Template provides full control over layout and styling', 'echo-knowledge-base' ),
							__( 'Current Theme Template inherits your theme\'s header, footer, and sidebar', 'echo-knowledge-base' ),
							__( 'Switch anytime from the Frontend Editor or Settings page', 'echo-knowledge-base' ),
							__( 'If your page looks too narrow, try the KB Template', 'echo-knowledge-base' ),
							__( 'Use Current Theme for seamless integration with your site design.', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'setup_ai_chat' => array(
				'title'       => __( 'Set Up AI Chat', 'echo-knowledge-base' ),
				'description' => __( 'Enable AI-powered chat to help visitors find answers through conversation.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'General Settings Required', 'echo-knowledge-base' ),
						'content' => __( 'Set your API key and accept the data privacy terms in General Settings so AI Chat can run.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'Training Data', 'echo-knowledge-base' ),
						'content' => __( 'Choose what to sync so AI has articles to answer with before enabling Chat.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'AI Chat Settings', 'echo-knowledge-base' ),
						'content' => __( 'Open the Settings sub-tab to enable AI Chat, fine-tune its behavior, and choose where the widget appears.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'AI Chat Setup Guide', 'echo-knowledge-base' ),
					'description' => __( 'Set up AI Chat in 3 steps: 1) Add your API key in General Settings 2) Sync articles as Training Data 3) Enable AI Chat and configure widget placement. The AI uses your KB content to generate helpful, conversational responses to visitor questions.', 'echo-knowledge-base' ),
				),
			),
			'setup_ai_search' => array(
				'title'       => __( 'Set Up AI Search', 'echo-knowledge-base' ),
				'description' => __( 'Configure AI-powered search for smarter, more relevant search results.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'General Settings Required', 'echo-knowledge-base' ),
						'content' => __( 'Set your API key and accept the data privacy terms in General Settings so AI Search can run.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'Training Data', 'echo-knowledge-base' ),
						'content' => __( 'Sync your articles so AI Search has indexed content to answer with.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'AI Search Settings', 'echo-knowledge-base' ),
						'content' => __( 'Open the Search Settings sub-tab to enable AI Search, choose modes, and control where results appear.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'AI Search Setup Guide', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Set up AI Search in 3 steps.', 'echo-knowledge-base' ),
						array(
							__( 'Add your API key in General Settings', 'echo-knowledge-base' ),
							__( 'Sync articles as Training Data', 'echo-knowledge-base' ),
							__( 'Enable AI Search and choose search mode', 'echo-knowledge-base' ),
							__( 'Understands natural language queries', 'echo-knowledge-base' ),
							__( 'Finds articles even without exact keyword matches', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'run_content_analysis' => array(
				'title'       => __( 'Run Content Analysis', 'echo-knowledge-base' ),
				'description' => __( 'Analyze your articles for readability, SEO, and quality improvements.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'Analyze Content', 'echo-knowledge-base' ),
						'content' => __( 'Choose articles here and start AI analysis to get fresh scores and insights.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'Improve Content', 'echo-knowledge-base' ),
						'content' => __( 'After analysis finishes, review result of each article analysis and improvements in this tab.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'Content Analysis Overview', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'AI-powered Content Analysis helps improve your articles.', 'echo-knowledge-base' ),
						array(
							__( 'Generate article tags (PRO)', 'echo-knowledge-base' ),
							__( 'Get readability and SEO scores', 'echo-knowledge-base' ),
							__( 'Receive specific improvement suggestions', 'echo-knowledge-base' ),
							__( 'Run analysis on selected articles or entire KB', 'echo-knowledge-base' ),
							__( 'Review detailed results for each article', 'echo-knowledge-base' ),
							__( 'Implement changes to boost content quality', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'setup_featured_articles' => array(
				'title'       => __( 'Set Up Featured Articles', 'echo-knowledge-base' ),
				'description' => __( 'Highlight your most important articles on the KB Main Page to guide visitors.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'Featured Articles Settings', 'echo-knowledge-base' ),
						'content' => __( 'Configure which article lists to display: Popular, Newest, or Recently Updated. Adjust titles, colors, and the number of articles shown.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'Featured Articles Module', 'echo-knowledge-base' ),
					'description' => trim( implode( ' ', array(
						self::format_bullet_description(
							__( 'Display curated article lists on your KB Main Page:', 'echo-knowledge-base' ),
							array(
								__( 'Popular Articles - most viewed', 'echo-knowledge-base' ),
								__( 'Newest Articles - recently published', 'echo-knowledge-base' ),
								__( 'Recently Updated Articles.', 'echo-knowledge-base' ),
							)
						),
						self::format_bullet_description(
							__( 'Customize:', 'echo-knowledge-base' ),
							array(
								__( 'List titles and styling', 'echo-knowledge-base' ),
								__( 'Number of articles shown', 'echo-knowledge-base' ),
								__( 'Layout and colors.', 'echo-knowledge-base' ),
							)
						),
					) ) ),
				),
			),
			'configure_article_views' => array(
				'title'       => __( 'Enable Article Views Counter', 'echo-knowledge-base' ),
				'description' => __( 'Turn on the views counter to track and display article popularity.', 'echo-knowledge-base' ),
				'learn_more'  => array(
					'title'       => __( 'Article Views Counter', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Track article popularity with the views counter:', 'echo-knowledge-base' ),
						array(
							__( 'Count how many times each article is viewed', 'echo-knowledge-base' ),
							__( 'Display view counts to visitors', 'echo-knowledge-base' ),
							__( 'Identify your most valuable content', 'echo-knowledge-base' ),
							__( 'Power the Popular Articles list', 'echo-knowledge-base' ),
							__( 'Enable/disable in Article Page settings.', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'setup_article_rating_feedback' => array(
				'title'       => __( 'Enable Article Rating & Feedback', 'echo-knowledge-base' ),
				'description' => __( 'Collect thumbs up/down or star ratings and ask for feedback to improve articles.', 'echo-knowledge-base' ),
				'learn_more'  => array(
					'title'       => __( 'Article Rating and Feedback', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						'',
						array(
							__( 'Collect visitor feedback on articles', 'echo-knowledge-base' ),
							__( 'Thumbs up/down ratings', 'echo-knowledge-base' ),
							__( 'Star ratings', 'echo-knowledge-base' ),
							__( 'Optional written feedback', 'echo-knowledge-base' ),
							__( 'Analytics dashboard to track ratings', 'echo-knowledge-base' ),
							__( 'Identify articles needing improvement', 'echo-knowledge-base' ),
						),
						__( 'Requires the Article Rating & Feedback add-on.', 'echo-knowledge-base' )
					),
				),
			),
			'setup_faqs' => array(
				'title'       => __( 'Set Up FAQs', 'echo-knowledge-base' ),
				'description' => __( 'Create frequently asked questions to provide quick answers to common queries.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'FAQs Overview', 'echo-knowledge-base' ),
						'content' => __( 'See options for placing FAQs on your site and jump into the right setup area.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'Questions', 'echo-knowledge-base' ),
						'content' => __( 'Create and edit the individual questions and answers in your FAQ lists.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'FAQ Groups', 'echo-knowledge-base' ),
						'content' => __( 'Organize questions into groups to control how FAQs are grouped on pages and blocks.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'FAQ Shortcodes', 'echo-knowledge-base' ),
						'content' => __( 'Generate the shortcode, choose the design, and adjust settings for embedding FAQs anywhere.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'FAQs Feature Overview', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Create FAQ sections for quick answers:', 'echo-knowledge-base' ),
						array(
							__( 'Add questions and answers', 'echo-knowledge-base' ),
							__( 'Organize into groups by topic', 'echo-knowledge-base' ),
							__( 'Display options: shortcodes, Gutenberg blocks, KB Main Page', 'echo-knowledge-base' ),
							__( 'Multiple designs including accordion style', 'echo-knowledge-base' ),
							__( 'Help visitors find answers without reading full articles.', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'setup_glossary' => array(
				'title'       => __( 'Set Up Glossary', 'echo-knowledge-base' ),
				'description' => __( 'Create glossary terms that automatically highlight in your articles and show tooltip definitions on hover.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'Introduction', 'echo-knowledge-base' ),
						'content' => __( 'Learn how the Glossary feature works and see examples of highlighted terms with tooltips.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'Glossary Terms', 'echo-knowledge-base' ),
						'content' => __( 'Add, edit, and manage your glossary terms and their definitions here.', 'echo-knowledge-base' ),
					),
					array(
						'title'   => __( 'Settings', 'echo-knowledge-base' ),
						'content' => __( 'Configure tooltip styles, highlighting behavior, and other glossary options.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'Glossary Feature Overview', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Add a glossary to your Knowledge Base:', 'echo-knowledge-base' ),
						array(
							__( 'Glossary terms are shared across all Knowledge Bases', 'echo-knowledge-base' ),
							__( 'Terms auto-highlight in articles with tooltip definitions on hover', 'echo-knowledge-base' ),
							__( 'Customize tooltip styles and highlighting colors', 'echo-knowledge-base' ),
							__( 'Use the Glossary Index shortcode or Gutenberg block to display all terms on a page', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'setup_article_links' => array(
				'title'       => __( 'Add Resource Links (PDFs / External)', 'echo-knowledge-base' ),
				'description' => __( 'Link articles to PDFs, pages, or external resources so readers can jump to the right place.', 'echo-knowledge-base' ),
				'learn_more'  => array(
					'title'       => __( 'Resource Links Add-on', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Turn KB articles into links to external resources:', 'echo-knowledge-base' ),
						array(
							__( 'Link to PDFs and documents', 'echo-knowledge-base' ),
							__( 'Link to external websites', 'echo-knowledge-base' ),
							__( 'Link to other pages on your site', 'echo-knowledge-base' ),
							__( 'Article appears in KB navigation', 'echo-knowledge-base' ),
							__( 'Clicking opens the linked resource.', 'echo-knowledge-base' ),
						),
						__( 'Requires the Links Editor add-on.', 'echo-knowledge-base' )
					),
				),
			),
			'configure_article_sidebar' => array(
				'title'       => __( 'Configure Article Sidebar', 'echo-knowledge-base' ),
				'description' => __( 'Customize what appears in your article sidebars for better navigation.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'Article Sidebar Settings', 'echo-knowledge-base' ),
						'content' => __( 'Configure sidebar navigation, choose which elements to display, and customize the appearance of your article sidebars.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'Article Sidebar Configuration', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Configure the article sidebar for better navigation:', 'echo-knowledge-base' ),
						array(
							__( 'Show category navigation tree', 'echo-knowledge-base' ),
							__( 'Display table of contents', 'echo-knowledge-base' ),
							__( 'Choose left or right placement', 'echo-knowledge-base' ),
							__( 'Customize styling and colors', 'echo-knowledge-base' ),
							__( 'Configure in Frontend Editor -> Article Page settings.', 'echo-knowledge-base' ),
						)
					),
				),
			),
			'setup_access_restrictions' => array(
				'title'       => __( 'Set Up Access Restrictions', 'echo-knowledge-base' ),
				'description' => __( 'Control who can view your KB content with user roles and groups.', 'echo-knowledge-base' ),
				'learn_more'  => array(
					'title'       => __( 'Access Manager Add-on', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Restrict KB content to specific users:', 'echo-knowledge-base' ),
						array(
							__( 'Create private KBs for employees, members, or customers', 'echo-knowledge-base' ),
							__( 'Control access at KB, category, or article level', 'echo-knowledge-base' ),
							__( 'Integrate with membership plugins', 'echo-knowledge-base' ),
							__( 'Show different content to different user roles.', 'echo-knowledge-base' ),
						),
						__( 'Requires the Access Manager add-on.', 'echo-knowledge-base' )
					),
				),
			),
			'setup_multilanguage' => array(
				'title'       => __( 'Multi-language Setup', 'echo-knowledge-base' ),
				'description' => __( 'Configure your KB for multiple languages with WPML or Polylang.', 'echo-knowledge-base' ),
				'pointers'    => array(
					array(
						'title'   => __( 'Polylang and WPML', 'echo-knowledge-base' ),
						'content' => __( 'Toggle multilingual support so your KB works with Polylang and WPML.', 'echo-knowledge-base' ),
					),
				),
				'learn_more'  => array(
					'title'       => __( 'Multi-language Support', 'echo-knowledge-base' ),
					'description' => self::format_bullet_description(
						__( 'Create KB content in multiple languages.', 'echo-knowledge-base' ),
						array(
							__( 'WPML translations', 'echo-knowledge-base' ),
							__( 'Polylang translations', 'echo-knowledge-base' ),
						)
					),
				),
			),
		);

		return isset( $texts[ $step_key ] ) ? $texts[ $step_key ] : array( 'title' => '', 'description' => '' );
	}

	/**
	 * Render the setup steps section for Help Resources page
	 */
	public static function render_steps_section( $kb_config ) {

		$kb_id = isset( $kb_config['id'] ) ? $kb_config['id'] : EPKB_KB_Config_DB::DEFAULT_KB_ID;

		// Get current category and its progress (progressive disclosure)
		$current_category = self::get_current_category( $kb_id );
		$category_progress = self::get_category_progress( $current_category, $kb_id );
		$visible_steps = self::get_visible_steps_by_category( $kb_id );
		$completed_steps = self::get_completed_steps();
		$all_complete = self::are_all_categories_done( $kb_id );
		$next_category = self::get_next_category( $current_category );
		$next_category_label = $next_category ? self::get_category_label( $next_category ) : '';

		// For data attributes, use category-specific counts
		$completed_count = $category_progress['completed'];
		$total_count = $category_progress['total'];
		$category_order = self::get_category_order();
		$category_count = count( $category_order );

		ob_start(); ?>

		<div class="epkb-setup-steps" data-kb-id="<?php echo esc_attr( $kb_id ); ?>" data-category="<?php echo esc_attr( $current_category ); ?>" data-total-count="<?php echo esc_attr( $total_count ); ?>" data-completed-count="<?php echo esc_attr( $completed_count ); ?>">

			<!-- Header -->
			<div class="epkb-setup-steps__header">
				<div class="epkb-setup-steps__header-left">
					<div class="epkb-setup-steps__header-text">
						<div class="epkb-setup-steps__category-trail">
							<?php foreach ( $category_order as $index => $category ) {
								$is_active = ( $category === $current_category );
								$category_class = $is_active ? ' epkb-setup-steps__category--active' : ' epkb-setup-steps__category--inactive';
								?>
								<span class="epkb-setup-steps__category<?php echo esc_attr( $category_class ); ?>"><?php echo esc_html( self::get_category_label( $category ) ); ?></span>
								<?php if ( $index < $category_count - 1 ) { ?>
									<span class="epkb-setup-steps__category-sep" aria-hidden="true">&gt;</span>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="epkb-setup-steps__header-right">
					<div class="epkb-setup-steps__progress">
						<div class="epkb-setup-steps__progress-text">
							<span class="epkb-setup-steps__progress-count"><?php echo esc_html( $completed_count ); ?></span>
							<span class="epkb-setup-steps__progress-separator">/</span>
							<span class="epkb-setup-steps__progress-total"><?php echo esc_html( $total_count ); ?></span>
							<span class="epkb-setup-steps__progress-label"><?php esc_html_e( 'steps completed', 'echo-knowledge-base' ); ?></span>
						</div>
						<div class="epkb-setup-steps__progress-bar">
							<div class="epkb-setup-steps__progress-fill" style="width: <?php echo esc_attr( $category_progress['percent'] ); ?>%;"></div>
						</div>
					</div>
				</div>
			</div>

			<!-- Steps List (Two Columns) -->
			<div class="epkb-setup-steps__list">
				<?php
				$step_number = 1;
				foreach ( $visible_steps as $step_key => $step ) {
					self::render_step_item( $step_key, $step, $kb_id, $step_number );
					$step_number++;
				}

				// Show completion state or motivational message
				if ( $all_complete ) {
					// All categories complete - big celebration! ?>
					<div class="epkb-setup-steps__celebration">
						<div class="epkb-setup-steps__celebration-content">
							<span class="epkb-setup-steps__celebration-icon epkbfa epkbfa-trophy"></span>
							<h3><?php esc_html_e( 'Congratulations!', 'echo-knowledge-base' ); ?></h3>
							<p><?php esc_html_e( 'You\'ve explored all Knowledge Base features. Your KB is fully configured and ready to help your users!', 'echo-knowledge-base' ); ?></p>
						</div>
					</div>
				<?php } elseif ( empty( $visible_steps ) ) {
					// Current category empty but not all complete - shouldn't happen normally ?>
					<div class="epkb-setup-steps__empty">
						<span class="epkbfa epkbfa-check-circle"></span>
						<p><?php esc_html_e( 'All steps in this category completed!', 'echo-knowledge-base' ); ?></p>
					</div>
				<?php } elseif ( count( $visible_steps ) <= 3 && ! empty( $next_category ) && ! self::is_category_complete( $next_category, $kb_id ) ) {
					// 3 or fewer steps remaining and there's a next category that's not yet complete - show teaser ?>
					<div class="epkb-setup-steps__teaser">
						<span class="epkbfa epkbfa-gift"></span>
						<?php // translators: %s is the name of the next category to unlock ?>
						<p><?php printf( esc_html__( 'Complete these steps to unlock %s!', 'echo-knowledge-base' ), '<strong>' . esc_html( $next_category_label ) . '</strong>' ); ?></p>
					</div>
				<?php } ?>
			</div>

			<!-- Show Completed Steps Button -->
			<?php if ( ! empty( $completed_steps ) ) { ?>
				<div class="epkb-setup-steps__completed-toggle">
					<button type="button" class="epkb-setup-steps__toggle-btn">
						<span class="epkbfa epkbfa-chevron-down"></span>
						<?php // translators: %d is the number of completed steps ?>
						<?php printf( esc_html__( 'Show Completed Steps (%d)', 'echo-knowledge-base' ), count( $completed_steps ) ); ?>
					</button>
				</div>

				<!-- Completed Steps (hidden by default) -->
				<div class="epkb-setup-steps__completed-list" style="display: none;">
					<?php foreach ( $completed_steps as $step_key => $step ) {
						self::render_step_item( $step_key, $step, $kb_id, 0, true );
					} ?>
					<div class="epkb-setup-steps__reset-wrap">
						<button type="button" class="epkb-setup-steps__btn epkb-setup-steps__btn--reset">
							<span class="epkbfa epkbfa-refresh"></span>
							<?php esc_html_e( 'Reset', 'echo-knowledge-base' ); ?>
							<svg class="epkb-setup-steps__btn-spinner" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"></path></svg>
						</button>
					</div>
				</div>
			<?php } ?>

		</div>

		<?php // TEMPORARY DEBUG TABLE - All Steps Review
		// echo self::render_debug_steps_table( $kb_id ); ?>

		<!-- Learn More Dialog -->
		<div class="epkb-setup-steps-dialog-overlay"></div>
		<div class="epkb-setup-steps-dialog">
			<div class="epkb-setup-steps-dialog__header">
				<h3 class="epkb-setup-steps-dialog__title"></h3>
				<button type="button" class="epkb-setup-steps-dialog__close" aria-label="<?php esc_attr_e( 'Close', 'echo-knowledge-base' ); ?>">
					<span class="epkbfa epkbfa-times"></span>
				</button>
			</div>
			<div class="epkb-setup-steps-dialog__body">
				<p class="epkb-setup-steps-dialog__description"></p>
			</div>
			<div class="epkb-setup-steps-dialog__footer">
				<a href="#" class="epkb-setup-steps-dialog__btn epkb-setup-steps-dialog__btn--action" target="_blank">
					<span class="epkbfa epkbfa-external-link"></span>
					<span class="epkb-setup-steps-dialog__btn-text"></span>
				</a>
				<a href="#" class="epkb-setup-steps-dialog__btn epkb-setup-steps-dialog__btn--doc" target="_blank">
					<span class="epkbfa epkbfa-book"></span>
					<?php esc_html_e( 'Documentation', 'echo-knowledge-base' ); ?>
				</a>
				<button type="button" class="epkb-setup-steps-dialog__btn epkb-setup-steps-dialog__btn--ask-ai">
					<span class="epkbfa epkbfa-comment"></span>
					<?php esc_html_e( 'Ask AI', 'echo-knowledge-base' ); ?>
				</button>
				<a href="#" class="epkb-setup-steps-dialog__btn epkb-setup-steps-dialog__btn--video" target="_blank">
					<span class="epkbfa epkbfa-play-circle"></span>
					<?php esc_html_e( 'Tutorial Video', 'echo-knowledge-base' ); ?>
				</a>
			</div>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * TEMPORARY: Render debug table showing all steps with details
	 */
	private static function render_debug_steps_table( $kb_id ) {
		$all_steps = self::$steps;
		$completed_keys = self::get_completed_step_keys();

		// Sort by priority
		uasort( $all_steps, function( $a, $b ) {
			return $a['priority'] - $b['priority'];
		} );

		ob_start(); ?>
		<div style="margin-top: 30px; background: #fff; border: 2px dashed #ff6b6b; border-radius: 8px; padding: 20px;">
			<h3 style="margin: 0 0 15px; color: #ff6b6b;">⚠️ DEBUG: All Steps Review (<?php echo count( $all_steps ); ?> total)</h3>
			<table style="width: 100%; border-collapse: collapse; font-size: 13px;">
				<thead>
					<tr style="background: #f8f9fa;">
						<th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">#</th>
						<th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Title</th>
						<th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Description</th>
						<th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Condition</th>
						<th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">Category</th>
						<th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">Eligible</th>
						<th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">Learn More</th>
						<th style="padding: 10px; border: 1px solid #dee2e6; text-align: center;">Show Me</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$row_num = 1;
					foreach ( $all_steps as $step_key => $step ) {
						$is_eligible = self::check_condition( $step['condition'], $kb_id );
						$is_completed = in_array( $step_key, $completed_keys );
						$condition_text = $step['condition']['type'];
						if ( $step['condition']['type'] === 'articles_min' ) {
							$condition_text .= ' (' . $step['condition']['count'] . ')';
						} elseif ( $step['condition']['type'] === 'addon_active' ) {
							$condition_text .= ' (' . $step['condition']['addon'] . ')';
						} elseif ( $step['condition']['type'] === 'plugin_active' ) {
							$condition_text .= ' (' . $step['condition']['plugin'] . ')';
						}
						$show_me_url = self::get_show_me_url( $step_key, $kb_id );
						$row_bg = $is_eligible ? '#fff' : '#f5f5f5';
						?>
						<tr style="background: <?php echo esc_attr( $row_bg ); ?>;">
							<td style="padding: 8px; border: 1px solid #dee2e6;"><?php echo esc_html( $row_num ); ?></td>
							<td style="padding: 8px; border: 1px solid #dee2e6; font-weight: 600;"><?php echo esc_html( self::get_step_title( $step_key, $step, $kb_id ) ); ?></td>
							<td style="padding: 8px; border: 1px solid #dee2e6; max-width: 300px;"><?php echo esc_html( self::get_step_description( $step_key ) ); ?></td>
							<td style="padding: 8px; border: 1px solid #dee2e6; font-size: 11px;"><?php echo esc_html( $condition_text ); ?></td>
							<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center; font-size: 11px;">
								<?php
								$category = isset( $step['category'] ) ? $step['category'] : 'N/A';
								$cat_colors = array( 'setup' => '#3b82f6', 'basic' => '#22c55e', 'advanced' => '#f59e0b' );
								$cat_color = isset( $cat_colors[ $category ] ) ? $cat_colors[ $category ] : '#666';
								?>
								<span style="display: inline-block; padding: 2px 8px; background: <?php echo esc_attr( $cat_color ); ?>; color: #fff; border-radius: 10px; font-weight: 500;">
									<?php echo esc_html( ucfirst( $category ) ); ?>
								</span>
							</td>
							<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">
								<?php echo $is_eligible ? '<span style="color: #28a745;">✓</span>' : '<span style="color: #dc3545;">✗</span>'; ?>
							</td>
							<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">
								<?php
								$step_texts = self::get_step_texts( $step_key );
								$learn_more = ! empty( $step_texts['learn_more'] ) ? $step_texts['learn_more'] : array();
								if ( ! empty( $learn_more['title'] ) && ! empty( $learn_more['description'] ) ) :
									$learn_more_url = ! empty( $step['learn_more_url'] ) ? $step['learn_more_url'] : '';
									// Build action URL if action_page is set
									$debug_action_url = '';
									if ( ! empty( $learn_more['action_page'] ) ) {
										$debug_action_url = self::get_learn_more_action_url( $learn_more['action_page'], $kb_id );
									}
								?>
									<button type="button" class="epkb-setup-steps__btn epkb-setup-steps__btn--learn-more"
									        style="display: inline-block; padding: 4px 10px; background: #9c27b0; color: #fff; text-decoration: none; border-radius: 4px; font-size: 11px; white-space: nowrap; border: none; cursor: pointer;"
									        data-title="<?php echo esc_attr( $learn_more['title'] ); ?>"
									        data-description="<?php echo esc_attr( $learn_more['description'] ); ?>"
									        <?php echo ! empty( $learn_more_url ) ? 'data-doc-url="' . esc_attr( $learn_more_url ) . '"' : ''; ?>
									        <?php echo ! empty( $debug_action_url ) ? 'data-action-url="' . esc_attr( $debug_action_url ) . '"' : ''; ?>
									        <?php echo ! empty( $learn_more['action_text'] ) ? 'data-action-text="' . esc_attr( $learn_more['action_text'] ) . '"' : ''; ?>>
										Learn More
									</button>
								<?php else : ?>
									<span style="color: #999;">-</span>
								<?php endif; ?>
							</td>
							<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">
								<?php if ( ! empty( $show_me_url ) ) : ?>
									<a href="<?php echo esc_url( $show_me_url ); ?>" target="_blank" style="display: inline-block; padding: 4px 10px; background: #0073aa; color: #fff; text-decoration: none; border-radius: 4px; font-size: 11px; white-space: nowrap;">
										Show Me →
									</a>
								<?php else : ?>
									<span style="color: #999;">-</span>
								<?php endif; ?>
							</td>
						</tr>
						<?php
						$row_num++;
					}
					?>
				</tbody>
			</table>
			<p style="margin: 15px 0 0; color: #6c757d; font-size: 12px;">
				<strong>Legend:</strong>
				<span style="background: #fff; padding: 2px 8px; border: 1px solid #dee2e6;">Eligible</span> |
				<span style="background: #f5f5f5; padding: 2px 8px;">Not Eligible (condition not met)</span> |
				<span style="background: #9c27b0; color: #fff; padding: 2px 8px; border-radius: 4px;">Learn More</span> opens dialog
			</p>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a single step item with two-column layout and buttons below description
	 */
	private static function render_step_item( $step_key, $step, $kb_id, $step_number = 0, $is_completed = false ) {
		$show_me_url = self::get_show_me_url( $step_key, $kb_id );
		$completed_class = $is_completed ? ' epkb-setup-steps__item--completed' : '';
		$step_title = self::get_step_title( $step_key, $step, $kb_id );
		$step_description = self::get_step_description( $step_key );

		// Determine if this is an inline pointer (no navigation, show pointer on current page)
		$is_inline_pointer = ! empty( $step['show_me'][0]['target_page'] ) && $step['show_me'][0]['target_page'] === 'inline_pointer';
		$target_element = ! empty( $step['show_me'][0]['target_element'] ) ? self::format_target_value( $step['show_me'][0]['target_element'], $kb_id ) : '';
		$step_texts = self::get_step_texts( $step_key );
		$pointer_title = ! empty( $step_texts['pointers'][0]['title'] ) ? $step_texts['pointers'][0]['title'] : $step_texts['title'];
		$pointer_content = ! empty( $step_texts['pointers'][0]['content'] ) ? $step_texts['pointers'][0]['content'] : $step_texts['description'];
		$category = isset( $step['category'] ) ? $step['category'] : 'setup';
		?>
		<div class="epkb-setup-steps__item<?php echo esc_attr( $completed_class ); ?>" data-step-key="<?php echo esc_attr( $step_key ); ?>" data-category="<?php echo esc_attr( $category ); ?>">

			<?php if ( $is_completed ) { ?>
				<?php if ( ! empty( $step['completed_mode'] ) && $step['completed_mode'] === 'later' ) { ?>
					<span class="epkb-setup-steps__item-question epkbfa epkbfa-question-circle"></span>
				<?php } else { ?>
					<span class="epkb-setup-steps__item-check epkbfa epkbfa-check-circle"></span>
				<?php } ?>
			<?php } elseif ( $step_number > 0 ) { ?>
				<span class="epkb-setup-steps__item-number"><?php echo esc_html( $step_number ); ?></span>
			<?php } ?>

			<div class="epkb-setup-steps__item-content">
				<h4 class="epkb-setup-steps__item-title">
					<?php echo esc_html( $step_title ); ?>
					<?php if ( $is_completed && ! empty( $step['completed_mode'] ) && $step['completed_mode'] === 'later' ) { ?>
						<span class="epkb-setup-steps__later-tag">
							<span class="epkbfa epkbfa-clock-o"></span>
							<?php esc_html_e( 'Later', 'echo-knowledge-base' ); ?>
						</span>
					<?php } ?>
				</h4>
				<p class="epkb-setup-steps__item-description"><?php echo esc_html( $step_description ); ?></p>

				<div class="epkb-setup-steps__item-actions">
					<?php if ( $is_completed ) { ?>
						<?php if ( ! empty( $step['completed_mode'] ) && $step['completed_mode'] === 'later' ) { ?>
							<button type="button" class="epkb-setup-steps__btn epkb-setup-steps__btn--complete-later" data-step-key="<?php echo esc_attr( $step_key ); ?>">
								<span class="epkbfa epkbfa-check"></span>
								<?php esc_html_e( 'Complete Now', 'echo-knowledge-base' ); ?>
								<svg class="epkb-setup-steps__btn-spinner" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"></path></svg>
							</button>
						<?php } ?>
						<button type="button" class="epkb-setup-steps__btn epkb-setup-steps__btn--restore" data-step-key="<?php echo esc_attr( $step_key ); ?>">
							<span class="epkbfa epkbfa-undo"></span>
							<?php esc_html_e( 'Restore', 'echo-knowledge-base' ); ?>
							<svg class="epkb-setup-steps__btn-spinner" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"></path></svg>
						</button>
					<?php } else { ?>
						<?php if ( $is_inline_pointer && ! empty( $target_element ) ) { ?>
							<button type="button" class="epkb-setup-steps__btn epkb-setup-steps__btn--show-me epkb-setup-steps__btn--inline-pointer"
							        data-target-element="<?php echo esc_attr( $target_element ); ?>"
							        data-pointer-title="<?php echo esc_attr( $pointer_title ); ?>"
							        data-pointer-content="<?php echo esc_attr( $pointer_content ); ?>">
								<span class="epkbfa epkbfa-hand-pointer-o"></span>
								<?php esc_html_e( 'Show Me', 'echo-knowledge-base' ); ?>
							</button>
						<?php } elseif ( ! empty( $show_me_url ) ) { ?>
							<a href="<?php echo esc_url( $show_me_url ); ?>" class="epkb-setup-steps__btn epkb-setup-steps__btn--show-me" target="_blank">
								<span class="epkbfa epkbfa-hand-pointer-o"></span>
								<?php esc_html_e( 'Show Me', 'echo-knowledge-base' ); ?>
							</a>
						<?php } ?>

						<?php
						$learn_more = ! empty( $step_texts['learn_more'] ) ? $step_texts['learn_more'] : array();
						if ( ! empty( $learn_more['title'] ) && ! empty( $learn_more['description'] ) ) {
							// Build action URL if action_page is set
							$action_url = '';
							if ( ! empty( $learn_more['action_page'] ) ) {
								$action_url = self::get_learn_more_action_url( $learn_more['action_page'], $kb_id );
							}
							?>
							<button type="button" class="epkb-setup-steps__btn epkb-setup-steps__btn--learn-more"
							        data-title="<?php echo esc_attr( $learn_more['title'] ); ?>"
							        data-description="<?php echo esc_attr( $learn_more['description'] ); ?>"
							        <?php echo ! empty( $step['learn_more_url'] ) ? 'data-doc-url="' . esc_attr( $step['learn_more_url'] ) . '"' : ''; ?>
							        <?php echo ! empty( $learn_more['video_url'] ) ? 'data-video-url="' . esc_attr( $learn_more['video_url'] ) . '"' : ''; ?>
							        <?php echo ! empty( $learn_more['ask_ai'] ) ? 'data-ask-ai="1"' : ''; ?>
							        <?php echo ! empty( $action_url ) ? 'data-action-url="' . esc_attr( $action_url ) . '"' : ''; ?>
							        <?php echo ! empty( $learn_more['action_text'] ) ? 'data-action-text="' . esc_attr( $learn_more['action_text'] ) . '"' : ''; ?>>
								<span class="epkbfa epkbfa-book"></span>
								<?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?>
							</button>
						<?php } elseif ( ! empty( $step['learn_more_url'] ) ) { ?>
							<a href="<?php echo esc_url( $step['learn_more_url'] ); ?>" class="epkb-setup-steps__btn epkb-setup-steps__btn--learn-more" target="_blank">
								<span class="epkbfa epkbfa-book"></span>
								<?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?>
							</a>
						<?php } ?>

						<button type="button" class="epkb-setup-steps__btn epkb-setup-steps__btn--done" data-step-key="<?php echo esc_attr( $step_key ); ?>">
							<span class="epkbfa epkbfa-check"></span>
							<?php esc_html_e( 'Done', 'echo-knowledge-base' ); ?>
							<svg class="epkb-setup-steps__btn-spinner" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"></path></svg>
						</button>
					<?php } ?>
				</div>
			</div>

		</div>
		<?php
	}
}
