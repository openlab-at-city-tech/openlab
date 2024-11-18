<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Features tab on the Need Help? screen
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Need_Help_Features {

	const FEATURES_TAB_VISITED_ACTION = 'epkb_features_tab_visited';

	public function __construct() {
		add_action( 'wp_ajax_' . self::FEATURES_TAB_VISITED_ACTION, array( $this, 'features_tab_visited' ) );
		add_action( 'wp_ajax_nopriv_' . self::FEATURES_TAB_VISITED_ACTION, array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Get configuration array for Features page view
	 *
	 * @return array
	 */
	public static function get_page_view_config() {
		global $first_main_page_url, $first_article_page_url;

		$kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
		$first_main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
		$first_article_page_url = EPKB_KB_Handler::get_first_kb_article_url( $kb_config );

		return array(

			// Shared
			'active' => true,
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_need_help_read', 'admin_eckb_access_frontend_editor_write'] ),
			'list_key' => 'features',

			// Top Panel Item
			'label_text' => esc_html__( 'Features', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-puzzle-piece',
			'main_class' => EPKB_Core_Utilities::is_kb_flag_set( 'features_tab_visited') ? 'epkb-admin__flag--visited' : '',

			// Secondary Views
			'secondary_tabs' => self::features_tab(),

			// List footer HTML
			'list_footer_html' => self::features_tab_footer(),
		);
	}

	/**
	 * Get HTML for single feature box
	 *
	 * @param $feature
	 * @return false|string
	 */
	private static function get_feature_box( $feature ) {

		switch( $feature['category'] )  {
			case 'design':
				$icon = 'epkbfa epkbfa-paint-brush';
				break;
			case 'article-features':
				$icon = 'epkbfa epkbfa-newspaper-o';
				break;
			case 'search':
				$icon = 'epkbfa epkbfa-search';
				break;
			case 'shortcodes':
				$icon = 'epkbfa epkbfa-list-alt';
				break;
			case 'widgets':
				$icon = 'epkbfa epkbfa-list-alt';
				break;
			case 'compatibility':
				$icon = 'epkbfa epkbfa-handshake-o';
				break;
			case 'help-dialog':
				$icon = 'epkbfa epkbfa-comments-o';
				break;
			case 'advanced':
				$icon = 'epkbfa epkbfa-rocket';
				break;
			default:
				$icon = 'epkbfa epkbfa-clock-o';
		}

		ob_start();
		$class_name = $feature['plugin'] != 'core' && $feature['plugin'] != 'crel' && $feature['plugin'] != 'ep'.'hd' && empty( $feature['active_status'] ) ? ' epkb-kbnh__feature-name--pro' : ''; ?>

		<div class="epkb-kbnh__feature-container__col epkb-kbnh__feature__icon-col"><span class="<?php echo empty( $feature['icon'] ) ? esc_attr( $icon ) : esc_attr( $feature['icon'] ); ?>"></span></div>

		<div class="epkb-kbnh__feature-container__col epkb-kbnh__feature__content-col">
			<h3 class="epkb-kbnh__feature-name<?php echo esc_attr( $class_name ); ?>"><?php echo esc_html( $feature['name'] ); ?> <?php

			// Optional experimental label
			if ( ! empty( $feature['experimental'] ) ) {   ?>
				<div class="epkb-kbnh__feature-experimental"><?php
					esc_html_e( 'Experimental', 'echo-knowledge-base' );
					EPKB_HTML_Elements::display_tooltip( '', $feature['experimental'], ['link_text' => '', ]); ?>
				</div><?php
			} ?>

			</h3><?php


			// Optional description
			if ( ! empty( $feature['desc'] ) ) {   ?>
				<div class="epkb-kbnh__feature-desc"><?php echo wp_kses_post( $feature['desc'] ); ?></div><?php
			}

			// Links    ?>
			<div class="epkb-kbnh__feature-links">  <?php

				if ( ! empty( $feature['custom'] ) && ( current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) || ( ! empty( $feature['min_capability'] ) && current_user_can( $feature['min_capability'] ) ) ) ) {
					echo wp_kses_post( $feature['custom'] );
				}

				// Link to Configure ( only if dedicated plugin is active and initial KB installation is completed )
				if ( ! empty( $feature['config'] ) && EPKB_Utilities::is_plugin_enabled( $feature['plugin'] ) && ! EPKB_Core_Utilities::run_setup_wizard_first_time() && ( current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) || ( ! empty( $feature['min_capability'] ) && current_user_can( $feature['min_capability'] ) ) ) ) {   ?>
					<a class="epkb-kbnh__feature-link" href="<?php echo esc_url( $feature['config'] ); ?>" target="_blank"><span><?php esc_html_e( 'Configure', 'echo-knowledge-base' ); ?></span></a>    <?php
				}

				if ( ! empty( $feature['docs'] ) ) {    ?>
					<a class="epkb-kbnh__feature-link" href="<?php echo esc_url( $feature['docs'] ); ?>" target="_blank"><span><?php esc_html_e( 'Documentation', 'echo-knowledge-base' ); ?></span></a>    <?php
				}

				// Link to Video Tutorial
				if ( ! empty( $feature['video'] ) ) {  ?>
					<a class="epkb-kbnh__feature-link" href="<?php echo esc_url( $feature['video'] ); ?>" target="_blank"><span><?php esc_html_e( 'Video Tutorial', 'echo-knowledge-base' ); ?></span></a>    <?php
				}

				// Link to Demo
				if ( ! empty( $feature['demo'] ) ) {  ?>
                    <a class="epkb-kbnh__feature-link" href="<?php echo esc_url( $feature['demo'] ); ?>" target="_blank"><span><?php esc_html_e( 'Demo', 'echo-knowledge-base' ); ?></span></a>    <?php
				}

				// if plugin is not enabled, then show Learn More
				/* if ( ! EPKB_Utilities::is_plugin_enabled( $feature['plugin'] ) ) {  ?>
					<a class="epkb-kbnh__feature-link" href="<?php echo EPKB_Core_Utilities::get_plugin_sales_page( $feature['plugin'] ); ?>" target="_blank"><span><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?></span></a>    <?php
				}*/				?>

			</div>

		</div>

		<div class="epkb-kbnh__feature-container__col epkb-kbnh__feature__status-col">    <?php

			// Plugin is enabled
			if ( $feature['active_status'] ) {    ?>
				<span class="epkb-kbnh__feature-status epkb-kbnh__feature--installed">
                    <span class="epkbfa epkbfa-check"></span>
                </span> <?php
			// Plugin is not enabled
			} else {
				if ( $feature['plugin'] == 'crel' ) {
					echo '<a class="epkb-kbnh__feature-status epkb-kbnh__feature--disabled epkb-success-btn" href="https://wordpress.org/plugins/creative-addons-for-elementor/" target="_blank"><span>' . esc_html__( 'Install Now', 'echo-knowledge-base' ) . '</span></a>';
				} else if ( $feature['plugin'] == 'ep'.'hd' && empty( $feature['hide_install_btn'] ) ) {
					echo '<a class="epkb-kbnh__feature-status epkb-kbnh__feature--disabled epkb-success-btn" href="https://wordpress.org/plugins/help-dialog/" target="_blank"><span>' . esc_html__( 'Install Now', 'echo-knowledge-base' ) . '</span></a>';
				} else if ( empty( $feature['hide_install_btn'] ) ) {
					echo '<a class="epkb-kbnh__feature-status epkb-kbnh__feature--disabled epkb-success-btn" href="' . esc_url( EPKB_Core_Utilities::get_plugin_sales_page( $feature['plugin'] ) ) . '" target="_blank"><span>' . esc_html__( 'Upgrade', 'echo-knowledge-base' ) . '</span></a>';
				}
			}   ?>

		</div>  <?php

		return ob_get_clean();
	}

	/**
	 * Get configuration array for all features
	 *
	 * Installed - if core OR ( if 'PRO' + add-on active )
	 * Upgrade - if 'PRO' + add-on inactive
	 * On/Off - if 'switch' available AND ( if core OR if 'PRO' + add-on active )
	 * 'PRO' if not core
	 *
	 * @return array[]
	 */
	private static function get_features_config() {

		$kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
		$kb_id = EPKB_KB_Handler::get_current_kb_id();

		$faq_groups = EPKB_FAQs_Utilities::get_faq_groups();
		$faq_groups = is_wp_error( $faq_groups ) ? [] : $faq_groups;
		$group_ids = array_keys( $faq_groups );

		return [
			[
				'plugin'    => 'core',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Ordering of Articles and Categories', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Order articles and categories by date or name, or use drag and drop for custom ordering.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'ordering' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/order-articles-and-categories/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_order_articles_write' ),
			],
			/* [
				'plugin'    => 'core',
				'category'  => 'basic',
				'icon'      => '',
				'name'      => esc_html__( 'Five Levels of Documentation Hierarchy', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Use five levels of categories and sub-categories with either unfolded or collapsed articles.', 'echo-knowledge-base' ),
				'config'    => '/edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) . '&post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/categories-overview/',
				'video'     => '',
				'min_capability'   => '',
			], */
			[
				'plugin'    => 'core',
				'category'  => 'design',
				'icon'      => '',
				'name'      => esc_html__( 'Layouts: Basic, Tabs, Categories, Classic and Drill Down', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Basic layout shows categories and articles in groups. Tabs layout has top-level tabs. Categories layout shows main categories and count of articles it contains. Drill Down layout has greater flexibility.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'main-page', 'module--categories_articles', 'main_page_layout' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/knowledge-base-layouts/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
				'hide_term' => 'month'
			],
			[
				'plugin'    => 'elay',
				'category'  => 'design',
				'icon'      => '',
				'name'      => esc_html__( 'Layouts: Grid and Sidebar', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'The Grid Layout displays top-level categories in rows and columns. The Sidebar Layout shows categories in a sidebar.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'main-page', 'module--categories_articles', 'main_page_layout' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/elegant-layouts-overview/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'design',
				'icon'      => '',
				'name'      => esc_html__( 'Categories: Font and Image Icons', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Categories can have custom images or any of the 500 font icons available.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) . '&post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/how-do-you-change-icons-for-the-categories/',
				'video'     => '',
				'min_capability' => EPKB_Admin_UI_Access::get_editor_capability(),
			],
			[
				'plugin'    => 'core',
				'category'  => 'design',
				'icon'      => '',
				'name'      => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Customize the font family, size and weight for article and category names, TOC, breadcrumbs, and more.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/typography-font-family-size-weight/',
				'video'     => '',
			],
			[
				'plugin'    => 'core',
				'category'  => 'design',
				'icon'      => '',
				'name'      => esc_html__( 'Theme Compatibility Mode', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Display KB Main Page, Article Pages and Category Archive Pages using either your current theme template or KB template.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'general' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/',
				'video'     => 'https://youtu.be/gPYwgZ8Ama8',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],

			[
				'plugin'    => 'core',
				'category'  => 'compatibility',
				'icon'      => '',
				'name'      => esc_html__( 'Elementor Compatible', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Works with Elementor widgets, Elementor templates, and more.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/elementor-plugin-setup/',
				'video'     => '',
			],
			[
				'plugin'    => 'core',
				'category'  => 'compatibility',
				'icon'      => '',
				'name'      => esc_html__( 'Page Builders', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Works with Elementor and Templates, Beaver Builder, Divi, Visual Composer, and others.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/using-page-builders-articles/',
				'video'     => '',
			],
			[
				'plugin'    => 'core',
				'category'  => 'compatibility',
				'icon'      => '',
				'name'      => esc_html__( 'RTL (Right-To-Left) Styling', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'This Knowledge Base fully supports RTL CSS files for both admin screens and frontend pages.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/more-features-rtl-accessibility/',
				'video'     => '',
			],
			[
				'plugin'    => 'core',
				'category'  => 'compatibility',
				'icon'      => '',
				'name'      => esc_html__( 'Multisite Compatible', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Echo Knowledge Base works with the WordPress multi-site feature.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/more-features-rtl-accessibility/#multisite',
				'video'     => '',
			],
			[
				'plugin'    => 'core',
				'category'  => 'compatibility',
				'icon'      => '',
				'name'      => esc_html__( 'WPML and Polylang Compatible', 'echo-knowledge-base' ),
				'switch'    => 'wpml_is_enabled',
				'desc'      => 'Supports use of WPML and Polylang plugins for multi-language sites.',
				'config'    => self::get_settings_link( $kb_id, 'tools', 'other' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/translate-text/',
				'video'     => '',
			],
			[
				'plugin'    => 'core',
				'category'  => 'compatibility',
				'icon'      => '',
				'name'      => esc_html__( 'Multi-language Support', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Change or translate any text label on the front-end using any of 12 translated languages.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/set-multilingual-bilingual-site/',
				'video'     => '',
			],
			[
				'plugin'    => 'core',
				'category'  => 'compatibility',
				'icon'      => '',
				'name'      => esc_html__( 'WCAG Accessibility', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Complies with basic WCAG accessibility for people with disabilities, including blindness.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/more-features-rtl-accessibility/',
				'video'     => '',
			],
			[
				'plugin'    => 'core',
				'category'  => 'compatibility',
				'icon'      => '',
				'name'      => esc_html__( 'Developer hooks', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Control the article page with WordPress hooks.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/adding-custom-section-to-articles-using-hooks/',
				'video'     => '',
			],
			[
				'plugin'    => 'core',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Table of Contents (TOC)', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Generate a TOC based on article headings and let it float, or stick, beside the article.', 'echo-knowledge-base' ),
				'switch'    => 'article_toc_enable',
				'config'    => self::get_settings_link( $kb_id, 'settings', 'article-page', 'article-page-toc' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/table-of-content/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Article Sidebars', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Choose to show left, right, or both sidebars containing navigation, TOC, and widgets.', 'echo-knowledge-base' ),
				'custom'    => '<a class="epkb-kbnh__feature-link" href="' . esc_url( self::get_settings_link( $kb_id, 'settings', 'article-page', 'article-page-sidebar', ( is_rtl() ? 'right_sidebar' : 'left_sidebar' ) ) ) . '" target="_blank"><span>' . esc_html__( 'Left Sidebar', 'echo-knowledge-base' ) . '</span></a>' .
				               '<a class="epkb-kbnh__feature-link" href="' . esc_url( self::get_settings_link( $kb_id, 'settings', 'article-page', 'article-page-sidebar', ( is_rtl() ? 'left_sidebar' : 'right_sidebar' ) ) ) . '" target="_blank"><span>' . esc_html__( 'Right Sidebar', 'echo-knowledge-base' ) . '</span></a>',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/article-sidebars/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Breadcrumbs', 'echo-knowledge-base' ),
				'desc'      => 'Show breadcrumbs on article pages.',
				'switch'    => 'breadcrumb_enable',
				'config'    => self::get_settings_link( $kb_id, 'settings', 'article-page', 'article-page-settings', 'breadcrumb' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/article-breadcrumb/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Navigation Links Sidebar', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Article pages can have navigation links in the left sidebar or in the right sidebar.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'article-page', 'article-page-sidebar' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/1-setup-knowledge-base/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Print Button', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Users can print an article without a redundant site header and footer.', 'echo-knowledge-base' ),
				'switch'    => 'print_button_enable',
				'config'    => self::get_settings_link( $kb_id, 'settings', 'labels', '', 'print_button' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/print-button/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Article Views Counter', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Track the number of times articles are viewed and display a view counter on article pages and in analytics. Show visitors the most popular articles using widgets and shortcodes (Widgets add-on required).', 'echo-knowledge-base' ),
				'switch'    => 'article_views_counter_enable',
				'config'    => self::get_settings_link( $kb_id, 'settings', 'article-page', 'article-page-settings', 'article_views_counter' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/article-views-counter/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Creation and Last Update Date, and Article Author', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Show the creation and modification dates and article author above or below each article.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'article-page' ),
				'custom'    => '<a class="epkb-kbnh__feature-link" href="' . esc_url( self::get_settings_link( $kb_id, 'settings', 'labels', '', 'created_date' ) ) . '" target="_blank"><span>' . esc_html__( 'Creation', 'echo-knowledge-base' ) . '</span></a>' .
				               '<a class="epkb-kbnh__feature-link" href="' . esc_url( self::get_settings_link( $kb_id, 'settings', 'labels', '', 'updated_date' ) ) . '" target="_blank"><span>' . esc_html__( 'Last Update', 'echo-knowledge-base' ) . '</span></a>' .
				               '<a class="epkb-kbnh__feature-link" href="' . esc_url( self::get_settings_link( $kb_id, 'settings', 'labels', '', 'author' ) ) . '" target="_blank"><span>' . esc_html__( 'Author', 'echo-knowledge-base' ) . '</span></a>',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/created-on-updated-on-author-meta/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Article Comments', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Choose to show or hide article comments.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'article-page' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/wordpress-article-comments/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Previous/Next Navigation', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Users can navigate to the next article or previous articles using the previous/next buttons.', 'echo-knowledge-base' ),
				'switch'    => 'prev_next_navigation_enable',
				'config'    => self::get_settings_link( $kb_id, 'settings', 'article-page', 'article-page-settings', 'prev_next_navigation' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/previous-next-page-navigation/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Back Navigation', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Show back navigation above each article to bring the user back to the KB Main Page.', 'echo-knowledge-base' ),
				'switch'    => 'article_content_enable_back_navigation',
				'config'    => self::get_settings_link( $kb_id, 'settings', 'article-page', 'article-page-settings', 'back_navigation' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/display-structure-overview/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'kblk',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Links to PDF Files, Docs, Images, and Web Pages', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Replace articles with links to PDFs, documents, images, videos, pages, and more.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/post-new.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&linked-editor=yes' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/links-editor-overview/',
				'video'     => '',
			],
			[
				'plugin'      => 'eprf',
				'category'    => 'article-features',
				'box-heading' => esc_html__( 'Article Ratings and Feedback Add-on', 'echo-knowledge-base' ),
			],
			[
				'plugin'    => 'eprf',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Article Feedback Form', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Readers can submit insightful feedback about your articles to help you improve them.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'article-page', 'article-page-ratings' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/article-rating-feedback-overview/#feedback-form/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'eprf',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Article Rating', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Users can rate articles.', 'echo-knowledge-base' ),
				'switch'    => 'article_content_enable_rating_element',
				'config'    => self::get_settings_link( $kb_id, 'settings', 'article-page', 'article-page-ratings' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/article-rating-feedback-overview/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'eprf',
				'category'  => 'article-features',
				'icon'      => '',
				'name'      => esc_html__( 'Article Rating Analytics', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Learn about the most and least rated articles.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&page=epkb-plugin-analytics#rating-data' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/article-rating-feedback-overview/#configure-stats/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_search_analytics_read' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'search',
				'icon'      => '',
				'name'      => esc_html__( 'Search Box on KB Main Page', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Fast search bar on KB Main Page with listed results.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'main-page', 'module--search', 'advanced_search_mp' ),
				'docs'      => '',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'search',
				'icon'      => '',
				'name'      => esc_html__( 'Search Box on KB Article Pages', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Fast search bar on KB Article Pages with listed results.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'article-page', 'article-page-search-box', 'search-options-ap--search-style-ap' ),
				'docs'      => '',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			/*[
				'plugin'    => 'core',
				'category'  => 'search',
				'icon'      => '',
				'name'      => esc_html__( 'Basic Analytics for Search', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'This shows the basic search count for articles found and those with no results.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&page=epkb-plugin-analytics#search-data' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/advanced-search-analytics/',
				'video'     => '',
			],*/
			[
				'plugin'      => 'asea',
				'category'    => 'search',
				'box-heading' => esc_html__( 'Advanced Search Add-on', 'echo-knowledge-base' ),
			],
			[
				'plugin'    => 'asea',
				'category'  => 'search',
				'icon'      => '',
				'name'      => esc_html__( 'Pre-Made Search Box Designs', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Choose from five pre-made designs to show different looks and styles for the search box.', 'echo-knowledge-base' ),
				'config'    => add_query_arg( array( 'action' => 'epkb_load_editor', 'preopen_zone' => 'search_box_zone' ), EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config ) ),
				'docs'      => '',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'asea',
				'category'  => 'search',
				'icon'      => '',
				'name'      => esc_html__( 'Search', 'echo-knowledge-base' ) . ' ' . esc_html__( 'Shortcode', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Add a KB search to any page such as Home and Contact Us. Users can search across multiple KBs.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/advanced-search-shortcode/',
				'video'     => '',
			],
			[
				'plugin'    => 'asea',
				'category'  => 'search',
				'icon'      => '',
				'name'      => esc_html__( 'Category Search Filter', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Users can narrow their results by searching within certain categories.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'main-page', 'module--search', 'advanced_search_mp' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/search-category-filters/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'asea',
				'category'  => 'search',
				'icon'      => '',
				'name'      => esc_html__( 'Search Analytics for No Results Searches', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Analytics will show searched-for keywords with no articles found. Add missing articles.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&page=epkb-plugin-analytics#search-data' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/advanced-search-analytics/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_search_analytics_read' ),
			],
			[
				'plugin'    => 'asea',
				'category'  => 'search',
				'icon'      => '',
				'name'      => esc_html__( 'Search by Tags', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Enable a search to match specific search keywords and article tags to find relevant articles.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_tag_taxonomy_name( $kb_id ) . '&post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/advanced-search-overview/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_editor_capability(),
			],
			[
				'plugin'    => 'asea',
				'category'  => 'search',
				'icon'      => '',
				'name'      => esc_html__( 'Search Analytics for The Most Popular Searches', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Analytics will show the most popular articles to help the editor make improvements.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&page=epkb-plugin-analytics#search-data' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/advanced-search-analytics/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_search_analytics_read' ),
			],
			[
				'plugin'    => 'asea',
				'category'  => 'search',
				'icon'      => '',
				'name'      => esc_html__( 'Search Results Pages', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Users can browse pages with search results if a large number of matching articles are found.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'main-page', 'module--search', 'advanced_search_results_page' ),
				'docs'      => '',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'asea',
				'category'  => 'search',
				'icon'      => '',
				'name'      => esc_html__( 'Search Box with Image and Links', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Add a background image, sub-title, and links to pages like the support form and more.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'main-page', 'module--search' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/advanced-search-overview/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'       => 'core',
				'category'     => 'shortcodes',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'Knowledge Base Shortcode', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Display Echo Knowledge Base on a page.', 'echo-knowledge-base' ) .
									EPKB_Shortcodes::get_copy_custom_box( EPKB_KB_Handler::KB_MAIN_PAGE_SHORTCODE_NAME, [ 'id' => $kb_id ], esc_html__( 'Shortcode:', 'echo-knowledge-base' ), false ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/knowledge-base-shortcode/',
			],
			[
				'plugin'       => 'core',
				'category'     => 'shortcodes',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'FAQs', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Show Frequently Asked Questions.', 'echo-knowledge-base' ) .
				                  EPKB_Shortcodes::get_copy_custom_box( 'epkb-faqs', [ 'group_ids' => implode( ',', $group_ids ) ], esc_html__( 'Shortcode example:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/faqs-shortcode/',
			],
			[
				'plugin'       => 'core',
				'category'     => 'shortcodes',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'Articles Index Directory', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Show alphabetical list of articles grouped by letter in a three-column format.', 'echo-knowledge-base' ) .
				                  EPKB_Shortcodes::get_copy_box( 'epkb-articles-index-directory', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/shortcode-articles-index-directory/',
			],
			[
				'plugin'       => 'asea',
				'category'     => 'shortcodes',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'Search One or More KBs', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Search one or more Knowledge Bases on any page.', 'echo-knowledge-base' ) .
									EPKB_Shortcodes::get_copy_box( 'eckb-advanced-search', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/advanced-search-shortcode/',
			],
			[
			   'plugin'      => 'widg',
			   'category'    => 'shortcodes',
			   'box-heading' => esc_html__( 'Widgets Add-on', 'echo-knowledge-base' ),
			],
			[
				'plugin'       => 'widg',
				'category'     => 'shortcodes',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'Recent Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Show either recently created or recently modified KB Articles.', 'echo-knowledge-base' ) .
				                  EPKB_Shortcodes::get_copy_box( 'widg-recent-articles', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/recent-articles-shortcode/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'shortcodes',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'Popular Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Show a list of the most popular articles based on article views.', 'echo-knowledge-base' ) .
				                  EPKB_Shortcodes::get_copy_box( 'widg-popular-articles', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/popular-articles-widget/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'shortcodes',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'KB Search', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Add a search box on your Home page, Contact Us page, and others.', 'echo-knowledge-base' ) .
				                  EPKB_Shortcodes::get_copy_box( 'widg-search-articles', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/search-shortcode/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'shortcodes',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'KB Categories', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'List your KB Categories for easy reference, which are typically displayed in sidebars.', 'echo-knowledge-base' ) .
				                  EPKB_Shortcodes::get_copy_box( 'widg-categories-list', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/categories-list-shortcode/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'shortcodes',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'List of Category Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Display a list of articles for a given category.', 'echo-knowledge-base' ) .
				                  EPKB_Shortcodes::get_copy_box( 'widg-category-articles', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/category-articles-shortcode/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'shortcodes',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'KB Tags', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Display current KB tags ordered alphabetically.', 'echo-knowledge-base' ) .
				                  EPKB_Shortcodes::get_copy_box( 'widg-tags-list', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/tags-list-shortcode/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'shortcodes',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'List of Tagged Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Display a list of articles that have a given tag.', 'echo-knowledge-base' ) .
				                  EPKB_Shortcodes::get_copy_box( 'widg-tag-articles', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/tagged-articles-shortcode/',
				'video'        => '',
			],
			[
				'plugin'       => 'core',
				'category'     => 'widgets',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'Widgets for Elementor', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Our Elementor widgets are designed for writers. We make it easy to write great instructions, step-by-step guides, manuals and detailed documentation.', 'echo-knowledge-base' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/elementor-widgets-for-documentation/',
			],
			[
				'plugin'      => 'widg',
				'category'    => 'widgets',
				'box-heading' => esc_html__( 'Widgets Add-on', 'echo-knowledge-base' ),
			],
			[
				'plugin'       => 'widg',
				'category'     => 'widgets',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'Recent Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Show either recently created or recently modified KB Articles.', 'echo-knowledge-base' ),
				'config'       => admin_url( '/widgets.php' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/recent-articles-widget/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'widgets',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'Popular Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Show a list of the most popular articles based on article views.', 'echo-knowledge-base' ),
				'config'       => admin_url( '/widgets.php' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/popular-articles-widget/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'widgets',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'KB Sidebar', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'A dedicated KB Sidebar will be shown only on the left side or right side of your KB articles.', 'echo-knowledge-base' ),
				'config'       => admin_url( '/widgets.php' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/kb-sidebar/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'widgets',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'KB Search', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Add a search box on your Home page, Contact Us page, and others.', 'echo-knowledge-base' ),
				'config'       => admin_url( '/widgets.php' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/search-widget/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'widgets',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'KB Categories', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'List your KB Categories for easy reference, which are typically displayed in sidebars.', 'echo-knowledge-base' ),
				'config'       => admin_url( '/widgets.php' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/categories-list-widget/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'widgets',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'List of Category Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Display a list of articles for a given category.', 'echo-knowledge-base' ),
				'config'       => admin_url( '/widgets.php' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/category-articles-widget/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'widgets',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'KB Tags', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Display current KB tags ordered alphabetically.', 'echo-knowledge-base' ),
				'config'       => admin_url( '/widgets.php' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/tags-list-widget/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'category'     => 'widgets',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'List of Tagged Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Display a list of articles that have a given tag.', 'echo-knowledge-base' ),
				'config'       => admin_url( '/widgets.php' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/tagged-articles-widget/',
				'video'        => '',
			],
			[
				'plugin'    => 'core',
				'category'  => 'advanced',
				'icon'      => '',
				'name'      => esc_html__( 'Convert Posts and CPTs into Articles', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Convert your blog and other posts as well as Custom Post Types into KB Articles.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'tools', 'convert' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/convert-posts-cpts-to-articles/',
				'video'     => '',
			],
			[
				'plugin'    => 'core',
				'category'  => 'advanced',
				'icon'      => '',
				'name'      => esc_html__( 'Category Archive Pages', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Select from five pre-made designs for the Category Archive Page with more options coming soon.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'settings', 'archive-page' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/category-archive-page/',
				'video'     => '',
				'min_capability'   => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'advanced',
				'icon'      => '',
				'name'      => esc_html__( 'Knowledge Base URL', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Include or exclude category in articles URL and customize your Knowledge Base URL.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'kb-url' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/changing-permalinks-urls-and-slugs/',
				'video'     => '',
				'hide_term' => 'week'
			],
			[
				'plugin'    => 'core',
                'category'  => 'blocks',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'name'      => esc_html__( 'KB Search', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Fast search bar on KB Main Page with listed results.', 'echo-knowledge-base' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/elementor-widgets-for-documentation/',
				'video'     => '',
				'hide_term' => 'week'
			],
			[
				'plugin'    => 'core',
                'category'  => 'blocks',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'name'      => esc_html__( 'KB Basic Layout', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'The Basic Layout offers a user-friendly grid format for viewing categories, subcategories, and articles. Expand and collapse article lists for easy navigation.', 'echo-knowledge-base' ),
				'demo'      => 'https://www.echoknowledgebase.com/demo-1-knowledge-base-basic-layout/',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/basic-layout/',
				'video'     => '',
				'hide_term' => 'week'
			],
			[
				'plugin'    => 'core',
                'category'  => 'blocks',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'name'      => esc_html__( 'KB Tabs Layout', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'The Tabs Layout clearly organizes top categories for subject-specific browsing. Within each tab, find related articles and sub-categories.', 'echo-knowledge-base' ),
				'demo'      => 'https://www.echoknowledgebase.com/demo-3-knowledge-base-tabs-layout/',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/using-tabs-layout/',
				'video'     => '',
				'hide_term' => 'week'
			],
			[
				'plugin'    => 'core',
                'category'  => 'blocks',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'name'      => esc_html__( 'KB Classic Layout', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'The Classic Layout offers a simple, compressed view of top-level categories. Click to expand each category and see its associated articles and subcategories.', 'echo-knowledge-base' ),
				'demo'      => 'https://www.echoknowledgebase.com/demo-12-knowledge-base-image-layout/',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/classic-layout/',
				'video'     => '',
				'hide_term' => 'week'
			],
			[
				'plugin'    => 'core',
                'category'  => 'blocks',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'name'      => esc_html__( 'KB Drill Down Layout', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'The Drill Down Layout helps you navigate large knowledge bases easily. Click top categories to progressively reveal articles and subcategories.', 'echo-knowledge-base' ),
				'demo'      => 'https://www.echoknowledgebase.com/demo-4-knowledge-base-tabs-layout/',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/drill-down-layout/',
				'video'     => '',
				'hide_term' => 'week'
			],
			[
				'plugin'    => 'core',
                'category'  => 'blocks',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'name'      => esc_html__( 'KB Categories Layout', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'The Categories layout resembles the Basic layout but includes the number of articles beside each category name.', 'echo-knowledge-base' ),
				'demo'      => 'https://www.echoknowledgebase.com/demo-14-category-layout/',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/categories-focused-layout/',
				'video'     => '',
				'hide_term' => 'week'
			],
			[
				'plugin'    => 'emkb',
				'category'  => 'advanced',
				'icon'      => '',
				'name'      => esc_html__( 'Unlimited Knowledge Bases', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Each KB has separate articles and URLs to help organize docs based on your topics, products, services, and more.', 'echo-knowledge-base' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/multiple-kbs-overview/',
				'video'     => '',
			],
			[
				'plugin'      => 'crel',
				'category'    => 'advanced',
				'box-heading' => esc_html__( 'Creative Add-ons Plugin ', 'echo-knowledge-base' ),
			],
			[
				'plugin'    => 'core',
				'category'  => 'advanced',
				'icon'      => '',
				'name'      => esc_html__( 'Creative Addons for Elementor', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Creative Addons makes writing professional documents and articles easy.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.creative-addons.com/',
				'video'     => '',
			],
			[
				'plugin'      => 'ep'.'hd',
				'category'    => 'advanced',
				'box-heading' => esc_html__( 'Help Dialog Chat Plugin', 'echo-knowledge-base' ),
			],
			[
				'plugin'       => 'ep'.'hd',
				'category'     => 'advanced',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'name'         => esc_html__( 'Help Dialog Chat', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Help Dialog Chat is a frontend dialog where users can easily search for answers, browse FAQs and submit contact form.', 'echo-knowledge-base' ),
				'docs'         => 'https://www.helpdialog.com/documentation/',
				'video'        => '',
			],
			[
				'plugin'      => 'amgr',
				'category'    => 'advanced',
				'box-heading' => esc_html__( 'Access Control Add-ons', 'echo-knowledge-base' ),
			],
			[
				'plugin'    => 'amgr',
				'category'  => 'advanced',
				'icon'      => '',
				'name'      => esc_html__( 'Restrict Access to Articles and Categories', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Control access to private a Knowledge Base utilizing WordPress user accounts.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/restrict-access-permission-privacy-scenarios-use-cases/',
				'custom'    => '<a class="epkb-kbnh__feature-link" href="https://www.echoknowledgebase.com/documentation/access-manager-and-add-ons/" target="_blank"><strong>' .
								esc_html__( 'Try Access Manager Demo', 'echo-knowledge-base' ) . '</strong></a>',
				'video'     => '',
			],
			[
				'plugin'    => 'amgp',
				'category'  => 'advanced',
				'icon'      => '',
				'name'      => esc_html__( 'Access Control Groups', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Organize your users into KB Groups, separating their access based on the level of access each group needs.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/groups-initial-setup/',
				'video'     => '',
				'hide_install_btn' => true,
			],
			[
				'plugin'    => 'amcr',
				'category'  => 'advanced',
				'icon'      => '',
				'name'      => esc_html__( 'Custom Roles', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Map any custom WP Role to KB Roles such as KB Subscriber, Author, Editor, and Manager.', 'echo-knowledge-base' ),
				'config'    => '',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/overview-custom-roles-add-on/',
				'video'     => '',
				'hide_install_btn' => true,
			],
			[
				'plugin'      => 'epie',
				'category'    => 'advanced',
				'box-heading' => esc_html__( 'Import and Export Add-on', 'echo-knowledge-base' ),
			],
			[
				'plugin'    => 'epie',
				'category'  => 'advanced',
				'icon'      => '',
				'name'      => esc_html__( 'Articles Migration, Copy, and Export', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Export configuration, articles, categories, and tags from your Knowledge Base.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'tools', 'export' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/import-export-overview/',
				'video'     => '',
			],
			[
				'plugin'    => 'epie',
				'category'  => 'advanced',
				'icon'      => '',
				'name'      => esc_html__( 'Articles CSV and XML Import', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Import configuration, articles, categories, and tags into your Knowledge Base using a CSV or XML file.', 'echo-knowledge-base' ),
				'config'    => self::get_settings_link( $kb_id, 'tools', 'import' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/import-export-overview/',
				'video'     => '',
			],
		];
	}

	/**
	 * CallBack function to use in usort function, sorting features by name
	 *
	 * @param $feature_a
	 * @param $feature_b
	 * @return bool
	 */
	private static function sort_features_by_name( $feature_a, $feature_b ) {
		return $feature_a['name'] > $feature_b['name'];
	}

	/**
	 * Get list of Most Popular feature names
	 *
	 * @return array
	 */
	private static function get_most_popular_feature_names() {
		return [
			__( 'Table of Contents (TOC)', 'echo-knowledge-base' ),
			__( 'Article Sidebars', 'echo-knowledge-base' ),
			__( 'Ordering of Articles and Categories', 'echo-knowledge-base' ),
			__( 'Category Font and Image Icons', 'echo-knowledge-base' ),
			__( 'Initial Layouts', 'echo-knowledge-base' ),
			__( 'Knowledge Base URL', 'echo-knowledge-base' ),
		];
	}

	/**
	 * Get configuration for feature categories
	 *
	 * @return array[]
	 */
	private static function get_categories_config() {
		return [
			/* [
				'name'  => 'basic',
				'title' => esc_html__( 'Basic', 'echo-knowledge-base' ),
				'icon'  => '',
			], */
			[
				'name'  => 'design',
				'title' => esc_html__( 'KB Design', 'echo-knowledge-base' ),
				'icon'  => 'epkbfa epkbfa-paint-brush',
			],
			[
				'name'  => 'article-features',
				'title' => esc_html__( 'Articles', 'echo-knowledge-base' ),
				'icon'  => 'epkbfa epkbfa-newspaper-o',
			],
			[
				'name'  => 'search',
				'title' => esc_html__( 'Search', 'echo-knowledge-base' ),
				'icon'  => 'epkbfa epkbfa-search',
			],
			[
				'name'  => 'blocks',
				'title' => esc_html__( 'Blocks', 'echo-knowledge-base' ),
				'icon'  => 'epkbfa epkbfa-list-alt',
			],
			[
				'name'  => 'shortcodes',
				'title' => esc_html__( 'Shortcodes' ),
				'icon'  => 'epkbfa epkbfa-list-alt',
			],
			[
				'name'  => 'widgets',
				'title' => esc_html__( 'Widgets', 'echo-knowledge-base' ),
				'icon'  => 'epkbfa epkbfa-list-alt',
			],
			[
				'name'  => 'compatibility',
				'title' => esc_html__( 'Compatibility', 'echo-knowledge-base' ),
				'icon'  => 'epkbfa epkbfa-handshake-o',
			],
			[
				'name'  => 'advanced',
				'title' => esc_html__( 'Advanced', 'echo-knowledge-base' ),
				'icon'  => 'epkbfa epkbfa-rocket',
			],
		];
	}

	/**
	 * Get configuration array for Features tab
	 *
	 * @return array
	 */
	private static function features_tab() {

		$features_tab = array();

		$features_list = self::get_features_config();

		// All Features - secondary tab
		/* $features_tab[] = array(

			// Shared
			'list_key' => 'all',
			'active' => true,

			// Secondary Panel Item
			'label_text' => esc_html__( 'All Features', 'echo-knowledge-base' ),

			// Secondary Boxes List
			'boxes_list' => self::features_tab_all( $features_list ),
		); */

		// Most Popular - secondary tab
		/* $features_tab[] = array(

			// Shared
			'list_key' => 'most-popular',

			// Secondary Panel Item
			'label_text' => esc_html__( 'Most Popular', 'echo-knowledge-base' ),

			// Secondary Boxes List
			'boxes_list' => self::features_tab_most_popular( $features_list ),
		); */

		// List categories - secondary tabs
		$first_tab = true;
		$categories_list = self::get_categories_config();
		foreach ( $categories_list as $category ) {

			$features_tab[] = array(

				'active' => $first_tab,

				// Shared
				'list_key' => strtolower( $category['name'] ),

				// Secondary Panel Item
				'label_text' => $category['title'],
				'icon_class' => $category['icon'],

				// Secondary Boxes List
				'boxes_list' => self::features_category_boxes_list( $features_list, $category['name'] ),
			);

			$first_tab = false;
		}

		return $features_tab;
	}

	/**
	 * Get configuration array of sorted by name features for Features -> All Features tab
	 *
	 * @param $features_list
	 * @return array
	 */
	private static function features_tab_all( $features_list ) {

		$features = array();

		usort( $features_list, array( 'EPKB_Need_Help_Features', 'sort_features_by_name' ) );

		foreach ( $features_list as $feature ) {

			$features[] = array(
				'class' => 'epkb-kbnh__feature-container',
				'html'  => self::get_feature_box( $feature ),
			);
		}

		return $features;
	}

	/**
	 * Get configuration for Features -> Most Popular tab
	 *
	 * @param $features_list
	 * @return array
	 */
	private static function features_tab_most_popular( $features_list ) {

		$features = array();

		$most_popular_names = self::get_most_popular_feature_names();

		foreach ( $features_list as $feature ) {

			// Filter features by name
			if ( ! in_array( $feature['name'], $most_popular_names ) ) {
				continue;
			}

			// If the current feature should not be shown in Most Popular list after a certain time of installation
			if ( ! empty( $feature['hide_term'] ) && ! get_transient( '_epkb_' . $feature['hide_term'] . '_after_installation' ) ) {
				continue;
			}

			$features[] = array(
				'class' => 'epkb-kbnh__feature-container',
				'html'  => self::get_feature_box( $feature ),
			);
		}

		return $features;
	}

	/**
	 * Get configuration for boxes list in Features category tab
	 *
	 * @param $features_list
	 * @param $category_name
	 *
	 * @return array
	 */
	private static function features_category_boxes_list( $features_list, $category_name ) {

		$features = array();

		$widgets_with_grouped_features = array();

		foreach ( $features_list as $feature ) {

            $feature['active_status'] = EPKB_Utilities::is_plugin_enabled( $feature['plugin'] );

			// Filter features by category
			if ( $feature['category'] != $category_name ) {
				continue;
			}

			// Add box separator heading
			if ( isset( $feature['box-heading'] ) ) {
				$widgets_with_grouped_features[] = $feature['plugin'];
				$class = 'epkb-kbnh__feature-heading ' . ( empty( $feature['class'] ) ? '' : $feature['class'] );
				$features[] = [
					'class' => $class,
					'html'  => self::get_box_heading_html( $feature ),
				];
                continue;
			}

			// Hide Install/Upgrade button for grouped features
			if ( in_array( $feature['plugin'], $widgets_with_grouped_features ) ) {
				$feature['hide_install_btn'] = true;
			}

			$features[] = array(
				'class' => 'epkb-kbnh__feature-container',
				'html'  => self::get_feature_box( $feature ),
			);
		}

		return $features;
	}

	/**
	 * Get box separator heading html
	 *
	 * @param $box
	 *
	 * @return string
	 */
	public static function get_box_heading_html( $box ) {

		ob_start(); ?>

        <h1 class="epkb-kbnh__feature-heading-title"><?php echo esc_html( $box['box-heading'] ); ?></h1> <?php

		// Plugin is enabled
		if ( ! empty( $box['active_status'] ) ) {   ?>
            <span class="epkb-kbnh__feature-status epkb-kbnh__feature--installed">
                <span class="epkbfa epkbfa-check"></span>
            </span>    <?php
			// Plugin is not enabled
		} else if ( $box['plugin'] == 'ep'.'hd' ) { ?>
            <a class="epkb-kbnh__feature-status epkb-kbnh__feature--disabled epkb-success-btn" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-add-ons#our-free-plugins' ) ); ?>" target="_blank"><span><?php esc_html_e( 'Free Install', 'echo-knowledge-base' ) ?></span></a>   <?php
		} else {    ?>
            <a class="epkb-kbnh__feature-status epkb-kbnh__feature--disabled epkb-success-btn" href="<?php echo esc_url( EPKB_Core_Utilities::get_plugin_sales_page( $box['plugin'] ) ); ?>" target="_blank"><span><?php echo esc_html__( 'Upgrade', 'echo-knowledge-base' ); ?></span></a> <?php
		}

		return ob_get_clean();
	}

	/**
	 * Get footer HTML for Features tab
	 *
	 * @return false|string
	 */
	private static function features_tab_footer() {
		ob_start();     ?>

		<span><?php esc_html_e( 'Cannot find a feature?', 'echo-knowledge-base' ); ?></span>
		<a href="https://www.echoknowledgebase.com/feature-request/" class="epkb-kb__wizard-link" target="_blank"><?php esc_html_e( 'Contact us', 'echo-knowledge-base' ); ?></a>   <?php

		return ob_get_clean();
	}

	public static function get_settings_link( $kb_id, $config_page='', $config_tab='', $config_sub_tab='', $config_box='' ) {
        $url = '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&page=epkb-kb-configuration#' . $config_page;
		$url .= empty( $config_tab ) ? '__' : '__' . $config_tab;
		$url .= empty( $config_sub_tab ) ? '__' : '__' . $config_sub_tab;
        $url .= empty( $config_box ) ? '' : '__' . $config_box;
		return admin_url( $url );
	}

	/**
	 * Add flag that indicates the Features tab in Need Help page was visited by the user at least once
	 */
	public static function features_tab_visited() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_need_help_read' );

		EPKB_Core_Utilities::add_kb_flag( 'features_tab_visited' );

		EPKB_Utilities::ajax_show_info_die();
	}
}
