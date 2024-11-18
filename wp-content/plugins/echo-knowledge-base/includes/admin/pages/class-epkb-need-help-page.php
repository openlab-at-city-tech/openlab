<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Need Help? admin page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Need_Help_Page {

	private $kb_config;

	public function __construct() {
		$this->kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
	}

	/**
	 * Display Need Help page
	 */
	public function display_need_help_page() {

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

		$admin_page_views = $this->get_regular_views_config();

		EPKB_HTML_Admin::admin_page_header();   ?>

		<div id="ekb-admin-page-wrap">

			<div class="epkb-kb-need-help-page-container">  <?php

				/**
				 * ADMIN HEADER (KB logo and list of KBs dropdown)
				 */
				EPKB_HTML_Admin::admin_header( $this->kb_config, ['admin_eckb_access_need_help_read', 'admin_eckb_access_frontend_editor_write'] );

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
				EPKB_HTML_Admin::admin_primary_tabs_content( $admin_page_views );    ?>

				<div class="eckb-bottom-notice-message"></div>

			</div>

		</div>	    <?php
	}

	/**
	 * Get configuration for regular views
	 *
	 * @return array
	 */
	private function get_regular_views_config() {

		// Notification after successful completion of Setup Wizard
		if ( isset( $_GET['epkb_after_kb_setup'] ) && ! EPKB_Core_Utilities::is_kb_flag_set( 'epkb_after_kb_setup_notice_shown' ) ) {
			$setup_complete_box = array(
                'class'   => 'epkb-admin__boxes-list__box-notification',
                'html' => EPKB_HTML_Forms::notification_box_middle( array(
	                'type'           => 'success',
	                'id'             => 'kb_setup-congrats',
	                'title'          => esc_html__( 'Congratulations!', 'echo-knowledge-base' ),
	                'desc'           => esc_html__( 'Your initial Knowledge Base is ready. You can view it now or work on your articles and KB design below.', 'echo-knowledge-base' ) .
                                        EPKB_Core_Utilities::get_current_kb_main_page_link( $this->kb_config, esc_html__( 'View My Knowledge Base', 'echo-knowledge-base' ), 'epkb-success-btn' ),
	                'close_target'   => '.epkb-kb__need-help__after-setup-wizard-dialog',
                ), true ),
            );

			EPKB_Core_Utilities::add_kb_flag( 'epkb_after_kb_setup_notice_shown' );
		}

		return array(

			// VIEW: Get Started
			array(

				// Shared
				'active' => true,
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_need_help_read', 'admin_eckb_access_frontend_editor_write'] ),
				'list_key' => 'getting-started',

				// Top Panel Item
				'label_text' => esc_html__( 'Get Started', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-graduation-cap',

				// Boxes List
				'boxes_list' => array(

					// Box: Notification after successful completion of Setup Wizard
					isset( $setup_complete_box ) ? $setup_complete_box : '',

					// Box: Get Started
					array(
						'html' => $this->getting_started_tab(),
					),
				),
			),

			// VIEW: Features
			EPKB_Need_Help_Features::get_page_view_config(),

			// VIEW: Contact Us
			EPKB_Need_Help_Contact_Us::get_page_view_config(),
		);
	}

	/**
	 * Get content for Get Started tab
	 *
	 * @return false|string
	 */
	private function getting_started_tab() {

		$steps_list = [];
		$step_number = 1;
		$kb_id = $this->kb_config['id'];

		// Setup Wizard
		if ( $this->kb_config['modular_main_page_toggle'] != 'off' && EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {
			$steps_list[] = array(
				'content_icon_class' => $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_flag_set( 'completed_setup_wizard_' . $kb_id ) ? 'epkbfa epkbfa-check-circle' : '',
				'icon_class' => '',
				'icon_img_url' => 'img/need-help/rocket-2.jpg',
				'title' => $step_number++ . '. ' . esc_html__( 'Setup Wizard (Run Anytime)', 'echo-knowledge-base' ),
				'desc' => esc_html__( 'Set up your Knowledge Base name, url, and design in just two steps. You can run the wizard again at any time to make changes.', 'echo-knowledge-base' ),
				'html' => EPKB_Core_Utilities::get_kb_admin_page_link( 'page=epkb-kb-configuration&setup-wizard-on', esc_html__( 'Launch Setup Wizard', 'echo-knowledge-base' ) ,'','epkb-kb__wizard-link'),
			);
		}

		if ( ! EPKB_Core_Utilities::run_setup_wizard_first_time() ) {

			// KB Articles and KB Categories
			$steps_list[] = array(
                'id' => 'epkb-kbnh__cta-box__add-content',
				'icon_class' => '',
				'content_icon_class' => $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_flag_set( 'edit_articles_categories_visited' ) ? 'epkbfa epkbfa-check-circle' : '',
				'icon_img_url' => 'img/need-help/notepad-pencil.jpg',
				'title' => $step_number++ . '. ' . esc_html__( 'Add Articles and Categories', 'echo-knowledge-base' ),
				'desc' => esc_html__( 'Populate your Knowledge Base with articles and categories.', 'echo-knowledge-base' ),
				'html' => self::steps_list_2_html( $kb_id )
			);

			// Main Page and Article settings
			$steps_list[] = array(
				'icon_class' => '',
				'content_icon_class' => EPKB_Core_Utilities::is_kb_flag_set( 'settings_tab_visited' ) ? 'epkbfa epkbfa-check-circle' : '',
				'icon_img_url' => 'img/need-help/palette.jpg',
				'title' => $step_number++ . '. ' . esc_html__( 'Customize Colors, Labels, and Fonts', 'echo-knowledge-base' ),
				'desc' => esc_html__( 'Easily change the style and look of KB pages.', 'echo-knowledge-base' ),
				'html' => '<a class="epkb-kb__wizard-link" href="' . admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&page=epkb-kb-configuration#settings__main-page' ) . '" target="_blank">' . esc_html__( 'Customize KB Main Page', 'echo-knowledge-base' ) . '</a>' . ' ' .
				          '<a class="epkb-kb__wizard-link" href="' . admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&page=epkb-kb-configuration#settings__article-page' ) . '" target="_blank">' . esc_html__( 'Customize KB Article Page', 'echo-knowledge-base' ) . '</a>'
			);

			// Features link
			$steps_list[] = array(
				'id' => 'epkb-admin__step-cta-box__features',
				'content_icon_class' => $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_flag_set( 'features_tab_visited' ) ? 'epkbfa epkbfa-check-circle' : '',
				'icon_class' => '',
				'icon_img_url' => 'img/need-help/mountain-flag.jpg',
				'title' => $step_number . '. ' . esc_html__( 'Explore Features and Add-ons', 'echo-knowledge-base' ),
				'desc' => esc_html__( 'Get familiar with features and how they function.', 'echo-knowledge-base' ),
				'html' => '<a class="epkb-kb__wizard-link epkb-admin__step-cta-box__link epkb-admin__link-scroll-top" data-target="features" href="#features">' . esc_html__( 'Explore Features', 'echo-knowledge-base' ) . '</a>' . '<br>' .
							'<a class="epkb-kb__wizard-link epkb-admin__step-cta-box__link" data-target="add-ons" href="' .
								admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . '1&page=epkb-add-ons' ) . '" target="_blank">' . esc_html__( 'Explore Add-ons', 'echo-knowledge-base' ) . '<span class="epkb-kb__wizard-link-icon epkbfa epkbfa-external-link"></span></a>' );
		}

		ob_start();     ?>

		<div class="epkb-kbnh__getting-started-container">

			<!-- Get Started - header container  -->
			<div class="epkb-kbnh__gs__header-container">
				<div class="epkb-kbnh__header__img">
					<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/guy-on-laptop.jpg' ); ?>">
				</div>
				<div class="epkb-kbnh__header__text">
					<h2 class="epkb-kbnh__header__title"><?php esc_html_e( 'Welcome to Echo Knowledge Base!', 'echo-knowledge-base' ); ?></h2>
					<p class="epkb-kbnh__header__desc"><?php esc_html_e( 'Thank you for choosing Echo KB, the most powerful WordPress Knowledge Base plugin.', 'echo-knowledge-base' ); ?></p>
					<ul>
						<li><?php esc_html_e( 'Easy to set up and use.', 'echo-knowledge-base' ); ?></li>
						<li><?php esc_html_e( 'Features focused on effective documentation for your users.', 'echo-knowledge-base' ); ?></li>
						<li><?php esc_html_e( 'Friendly and timely support from our team.', 'echo-knowledge-base' ); ?></li>
					</ul>
					<p class="epkb-kbnh__header__desc"><?php esc_html_e( 'Thanks for using our Knowledge Base!', 'echo-knowledge-base' ); ?></p>    <?php

					// Show this block only if user completed Setup Wizard
					if ( ! EPKB_Core_Utilities::run_setup_wizard_first_time() ) {
						//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo EPKB_Core_Utilities::get_current_kb_main_page_link( $this->kb_config, esc_html__( 'View My Knowledge Base', 'echo-knowledge-base' ), 'epkb-success-btn' );
					}   ?>
					<div class="epkb-kbnh__header__link-container">
						<span class="epkb-kbnh__link__text"><a href="https://www.echoknowledgebase.com/documentation/" target="_blank"><?php esc_html_e( 'View Online Documentation', 'echo-knowledge-base' ); ?></a></span>
						<span class="epkb-kbnh__link__icon epkbfa epkbfa-external-link"></span>
					</div>

				</div>
			</div>

			<!-- Getting Started - content container -->
			<div class="epkb-nh__gs__body-container">   <?php

				foreach ( $steps_list as $step ) {
					EPKB_HTML_Forms::display_step_cta_box( $step );
				}   ?>

			</div>

		</div>		<?php

		return ob_get_clean();
	}

	private function video_tutorials_tab() {
		ob_start();     ?>

		<div class="epkb-kbnh__video-tutorials-container">

			<div class="epkb-kbnh__section-container">
				<div class="epkb-kbnh__common-videos-container">

					<div class="epkb-kbnh__cv__list">						<?php
						EPKB_HTML_Forms::video_info_box( array(
							'title'     => 'Setup Knowledge Base Layouts and Colors',
							'video_src' => 'https://www.youtube.com/embed/WTihgYwSM6A',
							'desc'      => 'In this video we will show you how to choose a Layout and colors for your Knowledge base Main page and article pages.',
							'keywords'  => array('setup','layout','colors','article page')
						));
						EPKB_HTML_Forms::video_info_box( array(
							'title'     => 'How to Control Access for Groups and Teams',
							'video_src' => 'https://www.youtube.com/embed/0uObJZwgO_g',
							'desc'      => 'KB Groups add-on helps you organize your users into KB Groups. Use it to separate users based on the category access each group needs.',
							'keywords'  => array('access','groups','teams','restrict')

						));
						EPKB_HTML_Forms::video_info_box( array(
							'title'     => 'Changing your Knowledge Base Category Icons',
							'video_src' => 'https://www.youtube.com/embed/gbi-seMLLgo',
							'desc'      => 'We will show you how to choose custom Icons for your Knowledge Base Categories.',
							'keywords'  => array('layout','categories','category','icon')

						));						?>
					</div>

				</div>
			</div>

		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Return HTML links for second step on Get started page
	 */
	private static function steps_list_2_html( $kb_id ) {
		ob_start(); ?>

		<div class="epkb-admin__step-cta-box__column"><?php
			//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo EPKB_Core_Utilities::get_kb_admin_page_link( '', esc_html__( 'Edit KB Articles', 'echo-knowledge-base' ),'','epkb-kb__wizard-link' );

			if ( current_user_can( 'manage_categories' ) ) {
				$url = admin_url( '/edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) . '&post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) );	?>
				<a class="epkb-kb__wizard-link" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Edit KB Categories', 'echo-knowledge-base' ); ?></a><?php
			} ?>
		</div>
		<div class="epkb-admin__step-cta-box__column"><?php
			$import_url = admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&page=epkb-kb-configuration#tools__import' );
			$convert_url = admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) . '&page=epkb-kb-configuration#tools__convert' ); ?>
			<a class="epkb-kb__wizard-link" href="<?php echo esc_url( $import_url ); ?>"><?php esc_html_e( 'Import CSV as Articles', 'echo-knowledge-base' ); ?></a>
			<a class="epkb-kb__wizard-link" href="<?php echo esc_url( $convert_url ); ?>"><?php esc_html_e( 'Convert Posts to Articles', 'echo-knowledge-base' ); ?></a>
		</div><?php

		return ob_get_clean();
	}
}