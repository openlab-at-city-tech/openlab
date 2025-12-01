<?php  if ( ! defined( 'ABSPATH' ) ) exit;

spl_autoload_register( array('EPKB_Autoloader', 'autoload') );

/**
 * A class which contains the autoload function, that the spl_autoload_register
 * will use to autoload PHP classes.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Autoloader {

	public static function autoload( $class ) {
		static $classes = null;

		if ( $classes === null ) {
			$classes = array(

				// CORE
				'epkb_utilities'                    =>  'includes/class-epkb-utilities.php',
				'epkb_core_utilities'               =>  'includes/class-epkb-core-utilities.php',
				'epkb_html_elements'                =>  'includes/class-epkb-html-elements.php',
				'epkb_html_admin'                   =>  'includes/class-epkb-html-admin.php',
				'epkb_html_forms'    				=>  'includes/class-epkb-html-forms.php',
				'epkb_icons'                        =>  'includes/class-epkb-icons.php',
				'epkb_input_filter'                 =>  'includes/class-epkb-input-filter.php',

				// SYSTEM
				'epkb_logging'                      =>  'includes/system/class-epkb-logging.php',
				'epkb_templates'                    =>  'includes/system/class-epkb-templates.php',
				'epkb_upgrades'                     =>  'includes/system/class-epkb-upgrades.php',
				'epkb_wpml'                         =>  'includes/system/class-epkb-wpml.php',
				'epkb_pll'                          =>  'includes/system/class-epkb-pll.php',
				'epkb_delete_kb'                	=>  'includes/system/class-epkb-delete-kb.php',
				'epkb_deactivate_feedback'          =>  'includes/system/class-epkb-deactivate-feedback.php',
				'epkb_typography'                   =>  'includes/system/class-epkb-typography.php',
				'epkb_admin_ui_access'              =>  'includes/system/class-epkb-admin-ui-access.php',
				'epkb_reset'                        =>  'includes/system/class-epkb-reset.php',
				'epkb_controller'                   =>  'includes/system/class-epkb-controller.php',
				'epkb_db'                           =>  'includes/system/class-epkb-db.php',
				'epkb_language_utilities'           =>  'includes/system/class-epkb-language-utilities.php',
				'epkb_ui_table'                     =>  'includes/system/class-epkb-ui-table.php',

				// ACCESS MANAGER
				'epkb_access_manager'               =>  'includes/access-manager/class-epkb-access-manager.php',

				// ADMIN CORE
				'epkb_admin_notices'                =>  'includes/admin/class-epkb-admin-notices.php',
				'epkb_site_builders'                =>  'includes/admin/class-epkb-site-builders.php',
				'epkb_debug_controller'             =>  'includes/admin/settings/class-epkb-debug-controller.php',

				// ADMIN PAGES
				'epkb_config_page'                  =>  'includes/admin/pages/class-epkb-config-page.php',
				'epkb_config_tools_page'            =>  'includes/admin/pages/class-epkb-config-tools-page.php',
				'epkb_config_settings_page'         =>  'includes/admin/pages/class-epkb-config-settings-page.php',
				'epkb_analytics_page'               =>  'includes/admin/pages/class-epkb-analytics-page.php',
				'epkb_add_ons_page'                 =>  'includes/admin/pages/class-epkb-add-ons-page.php',
				'epkb_add_ons_features'             =>  'includes/admin/pages/class-epkb-add-ons-features.php',
				'epkb_dashboard_page'               =>  'includes/admin/pages/class-epkb-dashboard-page.php',

				// CONVERT
				'epkb_convert'                      =>  'includes/admin/convert/class-epkb-convert.php',
				'epkb_convert_ctrl'                 =>  'includes/admin/convert/class-epkb-convert-ctrl.php',

				// KB CONFIGURATION
				'epkb_kb_config_specs'              =>  'includes/admin/kb-configuration/class-epkb-kb-config-specs.php',
				'epkb_kb_config_db'                 =>  'includes/admin/kb-configuration/class-epkb-kb-config-db.php',
				'epkb_kb_config_layout_modular'     =>  'includes/admin/kb-configuration/class-epkb-kb-config-layout-modular.php',
				'epkb_kb_config_layout_basic'       =>  'includes/admin/kb-configuration/class-epkb-kb-config-layout-basic.php',
				'epkb_kb_config_layout_tabs'        =>  'includes/admin/kb-configuration/class-epkb-kb-config-layout-tabs.php',
				'epkb_kb_config_layout_categories'  =>  'includes/admin/kb-configuration/class-epkb-kb-config-layout-categories.php',
				'epkb_kb_config_layout_classic'     =>  'includes/admin/kb-configuration/class-epkb-kb-config-layout-classic.php',
				'epkb_kb_config_layout_drill_down'  =>  'includes/admin/kb-configuration/class-epkb-kb-config-layout-drill-down.php',
				'epkb_kb_config_sequence'           =>  'includes/admin/kb-configuration/class-epkb-kb-config-sequence.php',
				'epkb_kb_config_category'           =>  'includes/admin/kb-configuration/class-epkb-kb-config-category.php',
				'epkb_kb_config_controller'         =>  'includes/admin/kb-configuration/class-epkb-kb-config-controller.php',
				'epkb_export_import'                =>  'includes/admin/kb-configuration/class-epkb-export-import.php',

				// FAQs
				'epkb_faqs_ctrl'                    =>  'includes/admin/faqs/class-epkb-faqs-ctrl.php',
				'epkb_faqs_page'                    =>  'includes/admin/faqs/class-epkb-faqs-page.php',
				'epkb_faqs_utilities'               =>  'includes/admin/faqs/class-epkb-faqs-utilities.php',
				'epkb_faqs_ajax'               		=>  'includes/admin/faqs/class-epkb-faqs-ajax.php',

				// WIZARDS
				'epkb_kb_wizard_setup'              =>  'includes/admin/wizard/class-epkb-kb-wizard-setup.php',
				'epkb_kb_wizard_cntrl'              =>  'includes/admin/wizard/class-epkb-kb-wizard-cntrl.php',
				'epkb_kb_wizard_themes'             =>  'includes/admin/wizard/class-epkb-kb-wizard-themes.php',
				'epkb_kb_wizard_ordering'           =>  'includes/admin/wizard/class-epkb-kb-wizard-ordering.php',
				'epkb_kb_wizard_global'             =>  'includes/admin/wizard/class-epkb-kb-wizard-global.php',

				// FRONT END EDITOR
				'epkb_frontend_editor' 				=>  'includes/admin/editor/class-epkb-frontend-editor.php',
				'epkb_editor_utilities'             =>  'includes/admin/editor/class-epkb-editor-utilities.php',

				// FEATURES - LAYOUT
				'epkb_layout'                       =>  'includes/features/layouts/class-epkb-layout.php',
				'epkb_layout_basic'                 =>  'includes/features/layouts/class-epkb-layout-basic.php',
				'epkb_layout_tabs'                  =>  'includes/features/layouts/class-epkb-layout-tabs.php',
				'epkb_layout_categories'            =>  'includes/features/layouts/class-epkb-layout-categories.php',
				'epkb_layout_classic'               =>  'includes/features/layouts/class-epkb-layout-classic.php',
				'epkb_layout_drill_down'            =>  'includes/features/layouts/class-epkb-layout-drill-down.php',
				'epkb_layout_article_sidebar'       =>  'includes/features/layouts/class-epkb-layout-article-sidebar.php',
				'epkb_layout_category_sidebar'      =>  'includes/features/layouts/class-epkb-layout-category-sidebar.php',
				'epkb_layouts_setup'                =>  'includes/features/layouts/class-epkb-layouts-setup.php',

				// MODULAR MAIN PAGE
				'epkb_modular_main_page'            =>  'includes/features/layouts/modules/class-epkb-modular-main-page.php',
				'epkb_ml_search'                    =>  'includes/features/layouts/modules/class-epkb-ml-search.php',
				'epkb_ml_articles_list'             =>  'includes/features/layouts/modules/class-epkb-ml-articles-list.php',
				'epkb_ml_faqs'                      =>  'includes/features/layouts/modules/class-epkb-ml-faqs.php',

				// FEATURES - KB
				'epkb_kb_handler'                   =>  'includes/features/kbs/class-epkb-kb-handler.php',
				'epkb_kb_demo_data'                 =>  'includes/features/kbs/class-epkb-kb-demo-data.php',
				'epkb_kb_search'                    =>  'includes/features/kbs/class-epkb-kb-search.php',
				'epkb_template_sync'                =>  'includes/features/kbs/class-epkb-template-sync.php',

				// FEATURES - CATEGORIES
				'epkb_categories_db'                =>  'includes/features/categories/class-epkb-categories-db.php',
				'epkb_categories_admin'             =>  'includes/features/categories/class-epkb-categories-admin.php',
				'epkb_categories_array'             =>  'includes/features/categories/class-epkb-categories-array.php',
				'epkb_category_archive_setup'       =>  'includes/features/categories/class-epkb-category-archive-setup.php',

				// FEATURES - Tags
				'epkb_tag_archive_setup'            =>  'includes/features/categories/class-epkb-tag-archive-setup.php',

				// FEATURES - ARTICLES
				'epkb_articles_cpt_setup'           =>  'includes/features/articles/class-epkb-articles-cpt-setup.php',
				'epkb_articles_db'                  =>  'includes/features/articles/class-epkb-articles-db.php',
				'epkb_articles_admin'               =>  'includes/features/articles/class-epkb-articles-admin.php',
				'epkb_articles_array'               =>  'includes/features/articles/class-epkb-articles-array.php',
				'epkb_articles_setup'               =>  'includes/features/articles/class-epkb-articles-setup.php',

				// FEATURES - FAQS
				'epkb_faqs_cpt_setup'               =>  'includes/features/faqs/class-epkb-faqs-cpt-setup.php',

				// FEATURES - SHORTCODES
				'epkb_shortcodes'                   =>  'includes/features/shortcodes/class-epkb-shortcodes.php',
				'epkb_articles_index_shortcode'     =>  'includes/features/shortcodes/class-epkb-articles-index-shortcode.php',
				'epkb_faqs_shortcode'               =>  'includes/features/shortcodes/class-epkb-faqs-shortcode.php',
				'epkb_ai_advanced_search_shortcode' =>  'includes/features/shortcodes/class-epkb-ai-advanced-search-shortcode.php',

				// FEATURES - ARTICLE VIEWS
				'epkb_article_count_cntrl'          =>  'includes/features/article-counter/class-epkb-article-count-cntrl.php',
				'epkb_article_count_handler'        =>  'includes/features/article-counter/class-epkb-article-count-handler.php',

				// FEATURES - CHAT
				'epkb_ai_chat_frontend'             =>  'includes/features/chat/class-epkb-ai-chat-frontend.php',

				// TEMPLATES
				'epkb_templates_various'            =>  'templates/helpers/class-epkb-templates-various.php',

				// AI Admin
				'epkb_ai_admin_page'          		=>  'includes/ai/admin/class-epkb-ai-admin-page.php',
				'epkb_ai_content_analysis_page'		=>  'includes/ai/admin/class-epkb-ai-content-analysis-page.php',
				'epkb_ai_chat_tab'         			=>  'includes/ai/admin/class-epkb-ai-chat-tab.php',
				'epkb_ai_dashboard_tab'        		=>  'includes/ai/admin/class-epkb-ai-dashboard-tab.php',
				'epkb_ai_general_settings_tab'     	=>  'includes/ai/admin/class-epkb-ai-general-settings-tab.php',
				'epkb_ai_search_tab'          		=>  'includes/ai/admin/class-epkb-ai-search-tab.php',
				'epkb_ai_training_data_tab'      	=>  'includes/ai/admin/class-epkb-ai-training-data-tab.php',
				'epkb_ai_tools_tab'      			=>  'includes/ai/admin/class-epkb-ai-tools-tab.php',
				'epkb_ai_tools_debug_tab'           =>  'includes/ai/admin/class-epkb-ai-tools-debug-tab.php',
				'epkb_ai_pro_features_tab'		    =>  'includes/ai/admin/class-epkb-ai-pro-features-tab.php',

				// AI Config
				'epkb_ai_config_base'				=>  'includes/ai/config/class-epkb-ai-config-base.php',
				'epkb_ai_config_specs'              =>  'includes/ai/config/class-epkb-ai-config-specs.php',
				'epkb_ai_chat_widget_config_specs'  =>  'includes/ai/config/class-epkb-ai-chat-widget-config-specs.php',
				'epkb_ai_training_data_config_specs'=>  'includes/ai/config/class-epkb-ai-training-data-config-specs.php',

				// AI DB
				'epkb_ai_conversation_model'        =>  'includes/ai/db/class-epkb-ai-conversation-model.php',
				'epkb_ai_messages_db'               =>  'includes/ai/db/class-epkb-ai-messages-db.php',
				'epkb_ai_training_data_db'          =>  'includes/ai/db/class-epkb-ai-training-data-db.php',
				'epkb_ai_content_analysis_db'       =>  'includes/ai/db/class-epkb-ai-content-analysis-db.php',

				// AI OpenAI
				'epkb_openai_client'                =>  'includes/ai/openai/class-epkb-openai-client.php',
				'epkb_ai_openai_handler'            =>  'includes/ai/openai/class-epkb-ai-openai-handler.php',
				'epkb_ai_openai_vector_store'	    =>  'includes/ai/openai/class-epkb-ai-openai-vector-store.php',

				// AI REST
				'epkb_ai_rest_admin_controller'         =>  'includes/ai/rest/class-epkb-ai-rest-admin-controller.php',
				'epkb_ai_rest_base_controller'          =>  'includes/ai/rest/class-epkb-ai-rest-base-controller.php',
				'epkb_ai_rest_chat_controller'          =>  'includes/ai/rest/class-epkb-ai-rest-chat-controller.php',
				'epkb_ai_rest_content_analysis_controller' =>  'includes/ai/rest/class-epkb-ai-rest-content-analysis-controller.php',
				'epkb_ai_rest_search_controller'        =>  'includes/ai/rest/class-epkb-ai-rest-search-controller.php',
				'epkb_ai_rest_training_data_controller' =>  'includes/ai/rest/class-epkb-ai-rest-training-data-controller.php',
				'epkb_ai_rest_sync_controller'          =>  'includes/ai/rest/class-epkb-ai-rest-sync-controller.php',
				'epkb_ai_rest_support_controller'       =>  'includes/ai/rest/class-epkb-ai-rest-support-controller.php',
				'epkb_ai_rest_search_results_controller' => 'includes/ai/rest/class-epkb-ai-rest-search-results-controller.php',

				// AI Services
				'epkb_ai_base_handler'              	=>  'includes/ai/services/class-epkb-ai-base-handler.php',
				'epkb_ai_chat_handler'              	=>  'includes/ai/services/class-epkb-ai-chat-handler.php',
				'epkb_ai_search_handler'            	=>  'includes/ai/services/class-epkb-ai-search-handler.php',

				// AI Support
				'epkb_ai_content_processor'             =>  'includes/ai/support/class-epkb-ai-content-processor.php',
				'epkb_ai_utilities' 				    =>  'includes/ai/support/class-epkb-ai-utilities.php',
				'epkb_ai_validation'                    =>  'includes/ai/support/class-epkb-ai-validation.php',
				'epkb_ai_security'                      =>  'includes/ai/support/class-epkb-ai-security.php',
				'epkb_ai_table_operations'              =>  'includes/ai/support/class-epkb-ai-table-operations.php',
				'epkb_ai_log'                           =>  'includes/ai/support/class-epkb-ai-log.php',

				// AI Sync
				'epkb_ai_sync_manager'                  =>  'includes/ai/sync/class-epkb-ai-sync-manager.php',
				'epkb_ai_sync_hooks'                    =>  'includes/ai/sync/class-epkb-ai-sync-hooks.php',
				'epkb_ai_sync_job_manager'              =>  'includes/ai/sync/class-epkb-ai-sync-job-manager.php',
				'epkb_ai_sync_cron_handler'             =>  'includes/ai/sync/class-epkb-ai-sync-cron-handler.php',

				// AI Analysis
				'epkb_ai_tags_usage'                    =>  'includes/ai/analysis/class-epkb-ai-tags-usage.php',
				'epkb_ai_readability'                   =>  'includes/ai/analysis/class-epkb-ai-readability.php',
				'epkb_ai_content_analysis_utilities'    =>  'includes/ai/analysis/class-epkb-ai-content-analysis-utilities.php',
				'epkb_ai_content_analysis_job_manager'  =>  'includes/ai/analysis/class-epkb-ai-content-analysis-job-manager.php',

				// AI Search Results
				'epkb_ai_search_results_display'        =>  'includes/ai/search-results/class-epkb-ai-search-results-display.php',
				'epkb_ai_search_results_handler'        =>  'includes/ai/search-results/class-epkb-ai-search-results-handler.php',

				// BLOCKS
				'epkb_block_utilities'              =>  'includes/class-epkb-block-utilities.php',
				'epkb_blocks_setup'               	=>  'includes/admin/blocks/class-epkb-blocks-setup.php',
				'epkb_blocks_settings'             	=>  'includes/admin/blocks/class-epkb-blocks-settings.php',
				'epkb_abstract_block'               =>  'includes/admin/blocks/class-epkb-abstract-block.php',
				'epkb_search_block'                 =>  'includes/admin/blocks/class-epkb-search-block.php',
				'epkb_basic_layout_block'           =>  'includes/admin/blocks/class-epkb-basic-layout-block.php',
				'epkb_tabs_layout_block'            =>  'includes/admin/blocks/class-epkb-tabs-layout-block.php',
				'epkb_categories_layout_block'      =>  'includes/admin/blocks/class-epkb-categories-layout-block.php',
				'epkb_classic_layout_block'         =>  'includes/admin/blocks/class-epkb-classic-layout-block.php',
				'epkb_drill_down_layout_block'      =>  'includes/admin/blocks/class-epkb-drill-down-layout-block.php',
				'epkb_faqs_block'                   =>  'includes/admin/blocks/class-epkb-faqs-block.php',
				'epkb_featured_articles_block'      =>  'includes/admin/blocks/class-epkb-featured-articles-block.php',
				'epkb_grid_layout_block'            =>  'includes/admin/blocks/class-epkb-grid-layout-block.php',
				'epkb_sidebar_layout_block'         =>  'includes/admin/blocks/class-epkb-sidebar-layout-block.php',
				'epkb_advanced_search_block'        =>  'includes/admin/blocks/class-epkb-advanced-search-block.php',
				'epkb_ai_advanced_search_block'     =>  'includes/admin/blocks/class-epkb-ai-advanced-search-block.php',
			);
		}

		$cn = strtolower( $class );
		if ( isset( $classes[ $cn ] ) ) {
			/** @noinspection PhpIncludeInspection */
			include_once( plugin_dir_path( Echo_Knowledge_Base::$plugin_file ) . $classes[ $cn ] );
		}
	}
}
