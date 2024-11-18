<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display KB configuration menu and pages
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Config_Page {

	private $message = array(); // error/warning/success messages
	private $kb_config;
	private $kb_main_pages;

	// Show error/success messages
	function __construct() {
		$this->message = EPKB_KB_Config_Controller::handle_form_actions();
	}

	/**
	 * Displays the KB Config page with top panel + sidebar + preview panel
	 */
	public function display_kb_config_page() {

		// ensure that KB plugin is not activated in network-wide mode
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active_for_network( plugin_basename( Echo_Knowledge_Base::$plugin_file ) ) ) {
				$message = esc_html__( 'The Knowledge Base plugin cannot be activated network-wide. Please activate it on individual sites.', 'echo-knowledge-base' );
				EPKB_Core_Utilities::display_config_error_page( $message );
				return;
			}
		}

		// ensure KB config exists
		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		$this->kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );
		if ( is_wp_error( $this->kb_config ) || empty( $this->kb_config ) || ! is_array( $this->kb_config ) || count( $this->kb_config ) < 100 ) {
			EPKB_Logging::add_log( 'Could not retrieve KB configuration (715)', $this->kb_config );

			EPKB_Delete_KB::reset_config_button_handler( $kb_id );

			$error_message = esc_html__( 'Could not retrieve KB configuration. Please try again.', 'echo-knowledge-base' );
			$error_html    = EPKB_Utilities::is_user_admin()
				? esc_html__( 'Do you want to reset the Knowledge Base settings? All settings will revert to default.', 'echo-knowledge-base' ) . '<br /><br />' .
				EPKB_Delete_KB::get_reset_config_button() . '<br /></br />' . EPKB_Utilities::contact_us_for_support()
				: EPKB_Utilities::contact_us_for_support();

			EPKB_Core_Utilities::display_config_error_page( $error_message, $error_html );
			return;
		}

		EPKB_HTML_Admin::admin_page_header();

		// regenerate KB sequence for Categories and Articles if missing
		EPKB_KB_Handler::get_refreshed_kb_categories( $kb_id );

		//-------------------------------- SETUP WIZARD --------------------------------

		// should we display Setup Wizard or KB Configuration?
		if ( isset( $_GET['setup-wizard-on'] ) && $this->kb_config['modular_main_page_toggle'] == 'on' && EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {
			$add_ons_kb_config = $this->get_add_ons_config( $kb_id );
			if ( ! empty( $add_ons_kb_config) ) {
				$handler = new EPKB_KB_Wizard_Setup( $add_ons_kb_config );
				$handler->display_kb_setup_wizard();
			}
			return;
		}


		//---------------------- GENERAL CONFIGURATION PAGE -----------------------

		// retrieve KB Main Pages
		$this->kb_main_pages = EPKB_KB_Handler::get_kb_main_pages( $this->kb_config );

		/**
		 * Views of the Configuration Admin Page - show limited content for users that did not complete Setup Wizard
		 */
		if ( isset( $_GET['archived-kbs'] ) ) {
			$admin_page_views = self::get_archived_kbs_views_config();

		} else if ( EPKB_Core_Utilities::run_setup_wizard_first_time() && $this->kb_config['modular_main_page_toggle'] == 'on' ) {
			$admin_page_views = self::get_run_setup_first_views_config();

		} else {
			$add_ons_kb_config = $this->get_add_ons_config( $kb_id );
			if ( empty( $add_ons_kb_config) ) {
				return;
			}
			$admin_page_views = $this->get_regular_views_config( $add_ons_kb_config );
		}   ?>

		<!-- Admin Page Wrap -->
		<div id="ekb-admin-page-wrap">

			<div class="epkb-kb-config-page-container">    <?php

				/**
				 * ADMIN HEADER (KB logo and list of KBs dropdown)
				 */
				EPKB_HTML_Admin::admin_header( $this->kb_config, ['admin_eckb_access_order_articles_write', 'admin_eckb_access_frontend_editor_write'] );

				/**
				 * ADMIN TOOLBAR
				 */
				EPKB_HTML_Admin::admin_primary_tabs( $admin_page_views );

				/**
				 * ADMIN SECONDARY TABS
				 */
				EPKB_HTML_Admin::admin_secondary_tabs( $admin_page_views );

				/**
				 * LIST OF SETTINGS IN TABS
				 */
				EPKB_HTML_Admin::admin_primary_tabs_content( $admin_page_views );

				// generic confirmation box to reload page
				EPKB_HTML_Forms::dialog_confirm_action( array(
					'id'                => 'epkb-admin-page-reload-confirmation',
					'title'             => esc_html__( 'Changing Core Setting', 'echo-knowledge-base' ),
					'accept_label'      => esc_html__( 'Ok', 'echo-knowledge-base' ),
					'accept_type'       => 'primary',
					'show_cancel_btn'   => 'yes',
					'show_close_btn'    => 'no',
				) );    ?>

			</div>

		</div>  <?php

		/**
		 * Show any notifications
		 */
		foreach ( $this->message as $class => $message ) {
			//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo EPKB_HTML_Forms::notification_box_bottom( $message, '', $class );
		}
	}

	private function get_add_ons_config( $kb_id ) {
		// get current add-ons configuration
		$add_ons_kb_config = $this->kb_config;
		$add_ons_kb_config = apply_filters( 'epkb_all_wizards_get_current_config', $add_ons_kb_config, $kb_id );
		if ( is_wp_error( $add_ons_kb_config ) || empty( $add_ons_kb_config ) || ! is_array( $add_ons_kb_config ) || count( $add_ons_kb_config ) < 100 ) {
			EPKB_Core_Utilities::display_config_error_page();
			return [];
		}

		return $add_ons_kb_config;
	}

	/**
	 * Get configuration array for regular views of the KB Configuration page
	 *
	 * @param $wizard_kb_config
	 * @return array[]
	 */
	private function get_regular_views_config( $wizard_kb_config ) {

		/**
		 * PRIMARY TAB: Settings
		 */
		$settings_tab_handler = new EPKB_Config_Settings_Page( $this->kb_config );
		$settings_view_config = array(

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			'list_key' => 'settings',

			// Top Panel Item
			'label_text' => esc_html__( 'Settings', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-cogs',
			'vertical_tabs' => $settings_tab_handler->get_vertical_tabs_config()
		);

		/**
		 * PRIMARY TAB: Ordering
		 */
		$wizard_ordering = new EPKB_KB_Wizard_Ordering();
		$ordering_view_config = array(

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_order_articles_write' ),
			'list_key' => 'ordering',
			'kb_config_id' => $this->kb_config['id'],

			// Top Panel Item
			'label_text' => esc_html__( 'Order Articles and Categories', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-cubes',

			// Boxes List
			'boxes_list' => array(

				array(
					'class' => 'epkb-admin__boxes-list__box__ordering',
					'title' => esc_html__( 'Ordering Settings', 'echo-knowledge-base' ),
					'description' => '',
					'html' => $wizard_ordering->show_article_ordering( $wizard_kb_config ),
				),
			),
		);

		/**
		 * PRIMARY TAB: KB URLs
		 */
		$kb_url_view_config = array(

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_admin_capability(),
			'list_key' => 'kb-url',
			'kb_config_id' => $this->kb_config['id'],

			// Top Panel Item
			'label_text' => esc_html__( 'KB URLs', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-link',

			// Boxes List
			'boxes_list' => self::get_kb_urls_config( $wizard_kb_config )
		);

		/**
		 * PRIMARY TAB: Widgets / Shortcode
		 */
		$kb_widgets_view_config = array(

			// Shared
			'active' => false,
			'list_key' => 'widgets',
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_editor_capability(),
			'kb_config_id' => $this->kb_config['id'],

			// Top Panel Item
			'label_text' => esc_html__( 'Blocks' ) . ' / ' . esc_html__( 'Shortcodes' ) . ' / ' . esc_html__( 'Widgets', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-list-alt',

			// Secondary Panel Items
			'secondary_tabs'  => array(

				// SECONDARY VIEW: Blocks
				/* array(    // TODO is_block_enabled()

					// Shared
					'list_key'   => 'blocks',

					// Secondary Panel Item
					'label_text' => esc_html__( 'Blocks', 'echo-knowledge-base' ),

					// Secondary Boxes List
					'boxes_list' => self::get_widgets_boxes( self::get_blocks_boxes_config() )
				), */

				// SECONDARY VIEW: Shortcodes
				array(

					// Shared
					'list_key'   => 'shortcodes',
					'active'     => true,

					// Secondary Panel Item
					'label_text' => esc_html__( 'Shortcodes', 'echo-knowledge-base' ),

					// Secondary Boxes List
					'boxes_list' => self::get_widgets_boxes( $this->get_shortcodes_boxes_config() )
				),

				// SECONDARY VIEW: Widgets
				array(

					// Shared
					'list_key'   => 'widgets',

					// Secondary Panel Item
					'label_text' => esc_html__( 'Widgets', 'echo-knowledge-base' ),

					// Secondary Boxes List
					'boxes_list' => self::get_widgets_boxes( self::get_widgets_boxes_config() )
				)
			),
		);

		/**
		 * PRIMARY TAB: TOOLS
		 */
		$tools_view_config = EPKB_Config_Tools_Page::get_tools_view_config( $this->kb_config );


		/**
		 * OUTPUT PRIMARY TABS
		 */

		// compose views
		$core_views = [];

		$errors_tab_config = $this->get_errors_view_config();
		if ( ! empty( $errors_tab_config ) ) {
			$core_views[] = $errors_tab_config;
		}

		// Limited config for archived KBs
		if ( ! EPKB_Core_Utilities::is_kb_archived( $this->kb_config['status'] ) ) {
			$core_views[] = $settings_view_config;
			$core_views[] = $ordering_view_config;
			$core_views[] = $kb_url_view_config;
			$core_views[] = $kb_widgets_view_config;
			$core_views[] = $tools_view_config;
		}

		/**
		 * Add-on views for KB Configuration page
		 */
		$add_on_views = apply_filters( 'eckb_admin_config_page_views', [], $this->kb_config );
		if ( empty( $add_on_views ) || ! is_array( $add_on_views ) ) {
			$add_on_views = [];
		}

		$all_views = array_merge( $core_views, $add_on_views );

		// Full config for published KBs
		return $all_views;
	}

	/**
	 * Display KB URLs page
	 *
	 * @param $wizard_kb_config
	 * @return array
	 */
	private function get_kb_urls_config( $wizard_kb_config ) {
		$kb_url_boxes = [];

		// Box: Help box with Docs link for URL changing
		$kb_url_boxes[] = array(
			'class' => 'epkb-admin__boxes-list__box--kb-url-learn-more',
			'html' => EPKB_HTML_Forms::notification_box_middle( array(
					'type'  => 'info',
					'title' => esc_html__( 'Need to change KB URLs?', 'echo-knowledge-base' ),
					'desc'  => sprintf( '<a href="%s" target="_blank">%s <span class="ep_font_icon_external_link"></span></a>',
						'https://www.echoknowledgebase.com/documentation/changing-permalinks-urls-and-slugs/', esc_html__( 'Learn More', 'echo-knowledge-base' ) )
				), true  ),
		);

		if ( empty( $this->kb_main_pages ) ) {
			$kb_url_boxes[] = array(
				'title' => esc_html__( 'Your Knowledge Base URL', 'echo-knowledge-base' ),
				'html' => EPKB_HTML_Admin::display_no_main_page_warning( $this->kb_config, true ),
				'class' => 'epkb-admin__warning-box',
				'icon_class' => 'epkb-kbc__boxes-list__box__header--icon epkbfa-exclamation-circle'
			);

		} else {

			// Box: KB Location
			$kb_url_boxes[] = array(
				'class' => 'epkb-admin__boxes-list__box__kb-location',
				'title' => esc_html__( 'Knowledge Base Location', 'echo-knowledge-base' ),
				'description' => '',
				'html' => $this->get_kb_location_box(),
			);

			$wizard_global = new EPKB_KB_Wizard_Global( $wizard_kb_config );

			// Box: Category Name in KB URL
			$kb_url_boxes[] = array(
				'title' => esc_html__( 'Category Name in KB URL', 'echo-knowledge-base' ),
				'html' => $wizard_global->show_category_slug_toggle(),
				'class' => 'epkb-admin__toggle-box',
			);

			// Box: Knowledge Base URL
			$kb_url_boxes[] = array(
				'title' => esc_html__( 'Knowledge Base URL', 'echo-knowledge-base' ),
				'html' => $wizard_global->show_kb_urls(),
				'class' => 'epkb-admin__wizard-box',
			);

			// Apply button
			$kb_url_boxes[] = array(
				'html' => $wizard_global->show_apply_button(),
				'class' => 'epkb-admin__wizard-apply-btn-box',
			);
		}

		return $kb_url_boxes;
	}

	/**
	 * Get KB Location settings box
	 *
	 * @return false|string
	 */
	private function get_kb_location_box() {

		$HTML = new EPKB_HTML_Forms();
		ob_start();

		// If no Main Pages were detected for the current KB
		if ( empty( $this->kb_main_pages ) ) {
			EPKB_HTML_Admin::display_no_main_page_warning( $this->kb_config );

			// If at least one KB Main Page exists for the current KB
		} else {
			$kb_main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config );
			$kb_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $this->kb_config );     ?>

			<table class="epkb-admin__chapter__wrap">
				<tbody>
				<tr class="epkb-admin__chapter__content">
					<td><span><?php echo esc_html__( 'KB Page Title', 'echo-knowledge-base' ) . ': '; ?></span></td>
					<td><span><?php echo esc_html( $this->kb_config['kb_main_pages'][$kb_page_id] ); ?></span></td>
					<td><a class="epkb-primary-btn" href="<?php echo esc_url( get_edit_post_link( $kb_page_id ) ); ?>" target="_blank"><?php esc_html_e( 'Change Title or URL', 'echo-knowledge-base' ); ?></a></td>
				</tr>
				<tr class="epkb-admin__chapter__content">
					<td><span><?php echo esc_html__( 'KB Page URL', 'echo-knowledge-base' ) . ': '; ?></span></td>
					<td><a href="<?php echo esc_url( $kb_main_page_url ); ?>" target="_blank"><?php echo esc_html(  $kb_main_page_url ); ?><i class="ep_font_icon_external_link"></i></a></td>
					<td></td>
				</tr>
				<tr class="epkb-admin__chapter__content"><td colspan="3"></td></tr>
				<tr class="epkb-admin__chapter__content">
					<td colspan="3"><b><?php esc_html_e( 'Need to change KB URLs?', 'echo-knowledge-base' ); ?></b>
						<a href="https://www.echoknowledgebase.com/documentation/changing-permalinks-urls-and-slugs/" target="_blank"><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?> <i class="ep_font_icon_external_link"></i></a>
					</td>
				</tr>
				</tbody>
			</table>      <?php

			// If user has multiple pages with KB Shortcode or KB layout block then let them know this is normal for WPML users
			if ( count( $this->kb_main_pages ) > 1 && ! EPKB_Utilities::is_wpml_enabled( $this->kb_config ) ) {        ?>
				<div class="epkb-admin__chapter"><?php echo sprintf( esc_html__( 'Note: You have other pages with KB shortcode or KB layout block that are currently %snot used%s: ', 'echo-knowledge-base' ), '<strong>', '</strong>' ); ?></div>
				<ul class="epkb-admin__items-list">    <?php

					foreach ( $this->kb_main_pages as $page_id => $page_info ) {

						// Do not show relevant KB Main Page in the extra Main Pages list
						if ( $page_id == $kb_page_id ) {
							continue;
						}   ?>

						<li><span><?php echo esc_html( $page_info['post_title'] ); ?></span> <a href="<?php echo esc_url( get_edit_post_link( $page_id ) ); ?>" target="_blank"><?php esc_html_e( 'Edit page', 'echo-knowledge-base' ); ?></a></li><?php
					}   ?>

				</ul>                <?php
				$HTML::notification_box_middle( array(
					'type' => 'error-no-icon',
					'desc' => esc_html__( "It's best to remove KB shortcode and KB layout block from these pages unless you have a very specific reason for having them.", 'echo-knowledge-base' ),
					'' => '',
				));
			}
		}

		return ob_get_clean();
	}

	/**
	 * Get boxes for Widgets / Shortcode panel
	 *
	 * @param $boxes_content
	 * @return array
	 */
	private static function get_widgets_boxes( $boxes_content ) {

		$boxes = [];
		foreach ( $boxes_content as $box ) {

            // Hide install button for all Widgets / Shortcode boxes
			$box['hide_install_btn'] = true;

			$box['active_status'] = EPKB_Utilities::is_plugin_enabled( $box['plugin'] );

            // Add box separator heading
            if ( isset( $box['box-heading'] ) ) {
	            $boxes[] = [
		            'class' => 'epkb-kbnh__feature-heading',
		            'html'  => self::get_box_heading_html( $box ),
	            ];
                continue;
            }

			$boxes[] = [
				'class' => 'epkb-kbnh__feature-container',
				'html'  => EPKB_HTML_Forms::get_feature_box_html( $box ),
			];
		}

        return $boxes;
	}

	/**
	 * Get boxes config for Blocks
	 *
	 * @return array
	 */
	private static function get_blocks_boxes_config() {

		return [
			[
				'plugin'    => 'core',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'KB Search', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Fast search bar on KB Main Page with listed results.', 'echo-knowledge-base' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/elementor-widgets-for-documentation/',
			],
			[
				'plugin'    => 'core',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'KB Basic Layout', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'The Basic Layout offers a user-friendly grid format for viewing categories, subcategories, and articles. Expand and collapse article lists for easy navigation.', 'echo-knowledge-base' ),
				'demo'      => 'https://www.echoknowledgebase.com/demo-1-knowledge-base-basic-layout/',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/basic-layout/',
			],
			[
				'plugin'    => 'core',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'KB Tabs Layout', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'The Tabs Layout clearly organizes top categories for subject-specific browsing. Within each tab, find related articles and sub-categories.', 'echo-knowledge-base' ),
				'demo'      => 'https://www.echoknowledgebase.com/demo-3-knowledge-base-tabs-layout/',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/using-tabs-layout/',
			],
			[
				'plugin'    => 'core',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'KB Classic Layout', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'The Classic Layout offers a simple, compressed view of top-level categories. Click to expand each category and see its associated articles and subcategories.', 'echo-knowledge-base' ),
				'demo'      => 'https://www.echoknowledgebase.com/demo-12-knowledge-base-image-layout/',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/classic-layout/',
			],
			[
				'plugin'    => 'core',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'KB Drill Down Layout', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'The Drill Down Layout helps you navigate large knowledge bases easily. Click top categories to progressively reveal articles and subcategories.', 'echo-knowledge-base' ),
				'demo'      => 'https://www.echoknowledgebase.com/demo-4-knowledge-base-tabs-layout/',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/drill-down-layout/',
			],
			[
				'plugin'    => 'core',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'KB Categories Layout', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'The Categories layout resembles the Basic layout but includes the number of articles beside each category name.', 'echo-knowledge-base' ),
				'demo'      => 'https://www.echoknowledgebase.com/demo-14-category-layout/',
				'docs'      => 'https://www.echoknowledgebase.com/documentation/categories-focused-layout/',
			],
		];
	}

	/**
	 * Get boxes config for Widgets
	 *
	 * @return array
	 */
	private static function get_widgets_boxes_config() {

		return [
			[
				'plugin'    => 'core',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'Widgets for Elementor', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Our Elementor widgets are designed for writers. We make it easy to write great instructions, step-by-step guides, manuals and detailed documentation.', 'echo-knowledge-base' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/elementor-widgets-for-documentation/',
			],
			[
				'plugin'      => 'ep'.'hd',
				'box-heading' => esc_html__( 'Help Dialog Plugin', 'echo-knowledge-base' ),
			],
			[
				'plugin'    => 'ep'.'hd',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'Help Dialog', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Help Dialog is a frontend dialog where users can easily search for answers, browse FAQs and submit contact form.', 'echo-knowledge-base' ),
				'docs'      => 'https://www.helpdialog.com/documentation/',
				'video'     => '',
			],
			[
				'plugin'      => 'widg',
				'box-heading' => esc_html__( 'Widgets Add-on', 'echo-knowledge-base' ),
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'Recent Articles', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Show either recently created or recently modified KB Articles.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/recent-articles-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'Popular Articles', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Show a list of the most popular articles based on article views.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/popular-articles-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'KB Sidebar', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'A dedicated KB Sidebar will be shown only on the left side or right side of your KB articles.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/kb-sidebar/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'KB Search', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Add a search box on your Home page, Contact Us page, and others.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/search-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'KB Categories', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'List your KB Categories for easy reference, which are typically displayed in sidebars.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/categories-list-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'List of Category Articles', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Display a list of articles for a given category.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/category-articles-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'KB Tags', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Display current KB tags ordered alphabetically.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/tags-list-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => esc_html__( 'List of Tagged Articles', 'echo-knowledge-base' ),
				'desc'      => esc_html__( 'Display a list of articles that have a given tag.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/tagged-articles-widget/',
				'video'     => '',
			],
		];
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
		    <a class="epkb-kbnh__feature-status epkb-kbnh__feature--disabled epkb-success-btn" href="https://wordpress.org/plugins/help-dialog/" target="_blank">
			    <span><?php esc_html_e( 'Upgrade', 'echo-knowledge-base' ); ?></span></a>   <?php
	    } else {    ?>
		    <a class="epkb-kbnh__feature-status epkb-kbnh__feature--disabled epkb-success-btn" href="<?php echo esc_url( EPKB_Core_Utilities::get_plugin_sales_page( $box['plugin'] ) ); ?>" target="_blank">
			    <span><?php echo esc_html__( 'Upgrade', 'echo-knowledge-base' ); ?></span></a> <?php
	    }

	    return ob_get_clean();
    }

	/**
	 * Get boxes config for Shortcodes
	 *
	 * @return array
	 */
	private function get_shortcodes_boxes_config() {

		$kb_id = $this->kb_config['id'];
		$faq_groups = EPKB_FAQs_Utilities::get_faq_groups();
		$faq_groups = is_wp_error( $faq_groups ) ? [] : $faq_groups;
		$group_ids = array_keys( $faq_groups );

		return [
			[
				'plugin'       => 'core',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => esc_html__( 'Knowledge Base Shortcode', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Display Echo Knowledge Base on a page.', 'echo-knowledge-base' ),
				'desc_escaped' => EPKB_Shortcodes::get_copy_custom_box( EPKB_KB_Handler::KB_MAIN_PAGE_SHORTCODE_NAME, [ 'id' => $kb_id ], __( 'Shortcode:', 'echo-knowledge-base' ), false ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/knowledge-base-shortcode/',
			],
			[
				'plugin'       => 'core',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => esc_html__( 'FAQs', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Show Frequently Asked Questions.', 'echo-knowledge-base' ),
				'desc_escaped' => EPKB_Shortcodes::get_copy_custom_box( 'epkb-faqs', [ 'group_ids' => implode( ',', $group_ids ) ],
																		__( 'Shortcode example:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/faqs-shortcode/',
			],
			[
				'plugin'       => 'core',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => esc_html__( 'Articles Index Directory', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Show alphabetical list of articles grouped by letter in a three-column format.', 'echo-knowledge-base' ),
				'desc_escaped' => EPKB_Shortcodes::get_copy_box( 'epkb-articles-index-directory', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/shortcode-articles-index-directory/',
			],
			[
				'plugin'       => 'asea',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => esc_html__( 'Search One or More KBs', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Search one or more Knowledge Bases on any page.', 'echo-knowledge-base' ),
				'desc_escaped' => EPKB_Shortcodes::get_copy_box( 'eckb-advanced-search', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/advanced-search-shortcode/',
			],
            [
	            'plugin'      => 'widg',
	            'box-heading' => esc_html__( 'Widgets Add-on', 'echo-knowledge-base' ),
            ],
			[
				'plugin'       => 'widg',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => esc_html__( 'Recent Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Show either recently created or recently modified KB Articles.', 'echo-knowledge-base' ),
				'desc_escaped' => EPKB_Shortcodes::get_copy_box( 'widg-recent-articles', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/recent-articles-shortcode/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => esc_html__( 'Popular Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Show a list of the most popular articles based on article views.', 'echo-knowledge-base' ) ,
				'desc_escaped' => EPKB_Shortcodes::get_copy_box( 'widg-popular-articles', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/popular-articles-widget/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => esc_html__( 'KB Categories', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'List your KB Categories for easy reference, which are typically displayed in sidebars.', 'echo-knowledge-base' ),
				'desc_escaped' => EPKB_Shortcodes::get_copy_box( 'widg-categories-list', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/categories-list-shortcode/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => esc_html__( 'List of Category Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Display a list of articles for a given category.', 'echo-knowledge-base' ),
				'desc_escaped' => EPKB_Shortcodes::get_copy_box( 'widg-category-articles', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/category-articles-shortcode/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => esc_html__( 'KB Tags', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Display current KB tags ordered alphabetically.', 'echo-knowledge-base' ),
				'desc_escaped' => EPKB_Shortcodes::get_copy_box( 'widg-tags-list', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/tags-list-shortcode/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => esc_html__( 'List of Tagged Articles', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Display a list of articles that have a given tag.', 'echo-knowledge-base' ),
				'desc_escaped' => EPKB_Shortcodes::get_copy_box( 'widg-tag-articles', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/tagged-articles-shortcode/',
				'video'        => '',
			],
			[
				'plugin'       => 'widg',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => esc_html__( 'KB Search', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Add a search box on your Home page, Contact Us page, and others.', 'echo-knowledge-base' ),
				'desc_escaped' => EPKB_Shortcodes::get_copy_box( 'widg-search-articles', $kb_id, esc_html__( 'Shortcode:', 'echo-knowledge-base' ) ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/search-shortcode/',
				'video'        => '',
			],
		];
	}

	/**
	 * Get configuration array for views of KB Configuration page before the first KB setup
	 *
	 * @return array[]
	 */
	private static function get_run_setup_first_views_config() {

		return array(

			// VIEW: SETUP WIZARD
			array(

				// Shared
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_frontend_editor_write'] ),
				'list_key' => 'setup-wizard',

				// Top Panel Item
				'label_text' => esc_html__( 'Setup Wizard', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-cogs',

				'boxes_list' => array(

					// Box: Setup Wizard Message
					array(
						'html' => self::get_setup_wizard_message(),
						'class' => 'epkb-admin__notice'
					),
				),
			),
		);
	}

	/**
	 * Return message to complete Setup Wizard
	 *
	 * @return false|string
	 */
	private static function get_setup_wizard_message() {

		ob_start();     ?>

		<div class="epkb-admin__setup-wizard-warning">     <?php

			$thanks_message = EPKB_Core_Utilities::is_kb_flag_set( 'epkb_run_setup' ) ? esc_html__( 'Thank you for installing our Knowledge Base.', 'echo-knowledge-base' ) : esc_html__( 'Knowledge Base Shortcode is missing.', 'echo-knowledge-base' );

				EPKB_HTML_Forms::notification_box_popup( array(
				'type'  => 'success',
				'title' => $thanks_message . ' ' . esc_html__( 'Get started by running our Setup Wizard.', 'echo-knowledge-base' ),
				'desc'  => '<span>' . EPKB_Core_Utilities::get_kb_admin_page_link( 'page=epkb-kb-configuration&setup-wizard-on', esc_html__( 'Start the Setup Wizard', 'echo-knowledge-base' ), false,'epkb-success-btn' ) . '</span>',
			) );   ?>

		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Get configuration array for Errors view of KB Configuration page
	 *
	 * @return array
	 */
	private function get_errors_view_config() {

		$error_boxes = array();

		// KB missing main page error message
		if ( empty( $this->kb_main_pages ) ) {
			$error_boxes[] = array(
				'icon_class' => 'epkbfa-exclamation-circle',
				'title' => esc_html__( 'Missing Main Page', 'echo-knowledge-base' ),
				'html' => EPKB_HTML_Admin::display_no_main_page_warning( $this->kb_config, true ),
				'class' => 'epkb-admin__warning-box',
			);
		}

		// License issue messages from add-ons
		$add_on_messages = apply_filters( 'epkb_add_on_license_message', array() );
		if ( ( ! empty( $add_on_messages ) && is_array( $add_on_messages ) ) || did_action( 'kb_overview_add_on_errors' ) ) {

			$licenses_tab_url = admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-add-ons#licenses' );
			$licenses_tab_button = '<a href="' . esc_url( $licenses_tab_url ) . '" class="epkb-primary-btn"> ' . esc_html__( 'Fix the Issue', 'echo-knowledge-base' ) . '</a>';

			foreach ( $add_on_messages as $add_on_name => $add_on_message ) {

                // Add 'See Your License' button html
				$add_on_message .= $licenses_tab_button;

				$add_on_name = str_replace( array( '2', '3', '4' ), '', $add_on_name );

				$error_boxes[] = array(
					'icon_class' => 'epkbfa-exclamation-circle',
					'class' => 'epkb-admin__boxes-list__box__addons-license',
					'title' => $add_on_name . ': ' . esc_html__('License issue', 'echo-knowledge-base'),
					'description' => '',
					'html' => $add_on_message,
				);
			}
		}

		return empty( $error_boxes )
			? array()
			: array(

				// Shared
				'active' => true,
				'list_key' => 'errors',

				// Top Panel Item
				'label_text' => esc_html__( 'Errors', 'echo-knowledge-base' ),
				'icon_class' => 'page-icon overview-icon epkbfa epkbfa-exclamation-triangle',

				// Boxes List
				'boxes_list' => $error_boxes,
			);
	}

	/**
	 * Get configuration array for archived KBs
	 *
	 * @return array
	 */
	private static function get_archived_kbs_views_config() {

		$views_config = array(

			// View: Archived KBs
			array(

				// Shared
				'active' => true,
				'list_key' => 'archived-kbs',

				// Top Panel Item
				'label_text' => esc_html__( 'Archived KBs', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-cubes',

				// Boxes List
				'boxes_list' => array(

				),
			),
		);

		$archived_kbs = EPKB_Core_Utilities::get_archived_kbs();
		foreach ( $archived_kbs as $one_kb_config ) {

			$views_config[0]['boxes_list'][] = array(
				'class' => '',
				'title' => $one_kb_config['kb_name'],
				'description' => '',
				'html' => self::get_archived_kb_box_html( $one_kb_config ),
			);
		}

		return $views_config;
	}

	/**
	 * Get HTML for one archived KB box
	 *
	 * @param $kb_config
	 *
	 * @return false|string
	 */
	private static function get_archived_kb_box_html( $kb_config ) {

		ob_start();

		if ( ! EPKB_Utilities::is_multiple_kbs_enabled() ) {    ?>
			<div><?php esc_html_e( 'To manage non-default KBs you need Unlimited KBs add-on to be activated.', 'echo-knowledge-base' ); ?></div><?php
		}

		do_action( 'eckb_admin_config_page_kb_status', $kb_config );

		return ob_get_clean();
	}
}