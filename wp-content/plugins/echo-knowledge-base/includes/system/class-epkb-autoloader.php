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
				'epkb_error_handler'                =>  'includes/system/class-epkb-error-handler.php',
				'epkb_typography'                   =>  'includes/system/class-epkb-typography.php',
				'epkb_admin_ui_access'              =>  'includes/system/class-epkb-admin-ui-access.php',
				'epkb_reset'                        =>  'includes/system/class-epkb-reset.php',
				'epkb_controller'                   =>  'includes/system/class-epkb-controller.php',

				// ACCESS MANAGER
				'epkb_access_manager'               =>  'includes/access-manager/class-epkb-access-manager.php',

				// ADMIN CORE
				'epkb_admin_notices'                =>  'includes/admin/class-epkb-admin-notices.php',
				'epkb_site_builders'                =>  'includes/admin/class-epkb-site-builders.php',
				'epkb_debug_controller'             =>  'includes/admin/settings/class-epkb-debug-controller.php',
				'epkb_visual_helper'                =>  'includes/admin/settings/class-epkb-visual-helper.php',
				'epkb_main_page_visual_helper'      =>  'includes/admin/settings/class-epkb-main-page-visual-helper.php',
				'epkb_article_page_visual_helper'   =>  'includes/admin/settings/class-epkb-article-page-visual-helper.php',
				'epkb_category_page_visual_helper'  =>  'includes/admin/settings/class-epkb-category-page-visual-helper.php',

				// ADMIN PAGES
				'epkb_config_page'                  =>  'includes/admin/pages/class-epkb-config-page.php',
				'epkb_config_tools_page'            =>  'includes/admin/pages/class-epkb-config-tools-page.php',
				'epkb_config_settings_page'         =>  'includes/admin/pages/class-epkb-config-settings-page.php',
				'epkb_need_help_page'               =>  'includes/admin/pages/class-epkb-need-help-page.php',
				'epkb_need_help_features'           =>  'includes/admin/pages/class-epkb-need-help-features.php',
				'epkb_need_help_contact_us'         =>  'includes/admin/pages/class-epkb-need-help-contact-us.php',
				'epkb_analytics_page'               =>  'includes/admin/pages/class-epkb-analytics-page.php',
				'epkb_add_ons_page'                 =>  'includes/admin/pages/class-epkb-add-ons-page.php',
				'epkb_add_ons_features'             =>  'includes/admin/pages/class-epkb-add-ons-features.php',

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

				// WIZARDS
				'epkb_kb_wizard_setup'              =>  'includes/admin/wizard/class-epkb-kb-wizard-setup.php',
				'epkb_kb_wizard_cntrl'              =>  'includes/admin/wizard/class-epkb-kb-wizard-cntrl.php',
				'epkb_kb_wizard_themes'             =>  'includes/admin/wizard/class-epkb-kb-wizard-themes.php',
				'epkb_kb_wizard_ordering'           =>  'includes/admin/wizard/class-epkb-kb-wizard-ordering.php',
				'epkb_kb_wizard_global'             =>  'includes/admin/wizard/class-epkb-kb-wizard-global.php',

				// FRONT END EDITOR
				'epkb_editor_controller'            =>  'includes/admin/editor/class-epkb-editor-controller.php',
				'epkb_editor_view'                  =>  'includes/admin/editor/class-epkb-editor-view.php',
				'epkb_editor_article_page_config'   =>  'includes/admin/editor/class-epkb-editor-article-page-config.php',
				'epkb_editor_archive_page_config'   =>  'includes/admin/editor/class-epkb-editor-archive-page-config.php',
				'epkb_editor_main_page_config'      =>  'includes/admin/editor/class-epkb-editor-main-page-config.php',
				'epkb_editor_search_page_config'    =>  'includes/admin/editor/class-epkb-editor-search-page-config.php',
				'epkb_editor_config_base'           =>  'includes/admin/editor/class-epkb-editor-config-base.php',
				'epkb_editor_kb_base_config'        =>  'includes/admin/editor/class-epkb-editor-kb-base-config.php',
				'epkb_editor_utilities'             =>  'includes/admin/editor/class-epkb-editor-utilities.php',
				'epkb_editor_sidebar_config'        =>  'includes/admin/editor/class-epkb-editor-sidebar-config.php',

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

				// FEATURES - ARTICLE VIEWS
				'epkb_article_count_cntrl'          =>  'includes/features/article-counter/class-epkb-article-count-cntrl.php',
				'epkb_article_count_handler'        =>  'includes/features/article-counter/class-epkb-article-count-handler.php',

				// TEMPLATES
				'epkb_templates_various'            =>  'templates/helpers/class-epkb-templates-various.php',

				// OpenAI
				'epkb_openai'                       =>  'includes/admin/openai/class-epkb-openai.php',
				'epkb_ai_help_sidebar'              =>  'includes/admin/openai/class-epkb-ai-help-sidebar.php',
				'epkb_ai_help_sidebar_ctrl'         =>  'includes/admin/openai/class-epkb-ai-help-sidebar-ctrl.php',

				// BLOCKS
				'epkb_abstract_block'               =>  'includes/admin/blocks/class-epkb-abstract-block.php',
				'epkb_search_block'                 =>  'includes/admin/blocks/search/class-epkb-search-block.php',
				'epkb_basic_layout_block'           =>  'includes/admin/blocks/basic-layout/class-epkb-basic-layout-block.php',
				'epkb_tabs_layout_block'            =>  'includes/admin/blocks/tabs-layout/class-epkb-tabs-layout-block.php',
				'epkb_categories_layout_block'      =>  'includes/admin/blocks/categories-layout/class-epkb-categories-layout-block.php',
				'epkb_classic_layout_block'         =>  'includes/admin/blocks/classic-layout/class-epkb-classic-layout-block.php',
				'epkb_drill_down_layout_block'      =>  'includes/admin/blocks/drill-down-layout/class-epkb-drill-down-layout-block.php',
				'epkb_block_utilities'              =>  'includes/class-epkb-block-utilities.php',
			);
		}

		$cn = strtolower( $class );
		if ( isset( $classes[ $cn ] ) ) {
			/** @noinspection PhpIncludeInspection */
			include_once( plugin_dir_path( Echo_Knowledge_Base::$plugin_file ) . $classes[ $cn ] );
		}
	}
}
