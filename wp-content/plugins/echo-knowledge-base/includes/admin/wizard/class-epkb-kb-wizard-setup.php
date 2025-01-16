<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Show setup wizard when plugin is installed
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Setup {

	private $kb_config;
	private $is_setup_run_first_time;
	private $elay_enabled;
	private $is_old_elay;   // TODO: remove in December 2024
	private $is_main_page_missing;

	private static $sidebar_images = array(
		0 => 'setup-wizard/step-5/Article-Setup-No-sidebar.jpg',
		1 => 'setup-wizard/step-5/Article-Setup-Left-Sidebar-Category-and-Article.jpg',
		2 => 'setup-wizard/step-5/Article-Setup-Right-Sidebar-Category-and-Article.jpg',
		3 => 'setup-wizard/step-5/Article-Setup-Left-Sidebar-Top-Category-Navigation.jpg',
		4 => 'setup-wizard/step-5/Article-Setup-Right-Sidebar-Top-Category-Navigation.jpg',
		5 => 'setup-wizard/step-5/Article-Setup-Left-Sidebar-current-category-articles.jpg',    
		6 => 'setup-wizard/step-5/Article-Setup-Right-Sidebar-current-category-articles.jpg',
		7 => 'setup-wizard/step-5/Article-Setup-No-Sidebar.jpg',
	);

	function __construct( $kb_config=array() ) {
		$this->kb_config = $kb_config;
		$this->is_setup_run_first_time = EPKB_Core_Utilities::run_setup_wizard_first_time() || EPKB_Utilities::post( 'emkb_admin_notice' ) == 'kb_add_success';

		$this->elay_enabled = EPKB_Utilities::is_elegant_layouts_enabled();
		$this->is_old_elay = $this->elay_enabled && class_exists( 'Echo_Elegant_Layouts' ) && version_compare( Echo_Elegant_Layouts::$version, '2.14.1', '<=' );

		$existing_main_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $this->kb_config );
		$this->is_main_page_missing = empty( $existing_main_page_id );
	}

	/**
	 * Show KB Setup page
	 */
	public function display_kb_setup_wizard() {

		// Step: URL
		$step_number = 1;
		$setup_steps_config[] = [
			'label'     => esc_html__( 'URL', 'echo-knowledge-base' ),
			'header_escaped'    => $this->wizard_step_header( array(
				'title_html'            => esc_html__( 'Setup Your Knowledge Base', 'echo-knowledge-base' ),
				'info_title'            => esc_html__( 'Set your Knowledge Base nickname, create a slug, and add it to the menu.', 'echo-knowledge-base' ),
			) ),
			'content_escaped'   => $this->wizard_step_title_url_content( $step_number ),
			'step_number_label'	=> 1,
		];

		// Step: Modules - Part 1 of 2
		if ( EPKB_Block_Utilities::is_block_enabled() && $this->is_main_page_missing ) {
			$step_number++;
			$setup_steps_config[] = [
				'label'     => esc_html__( 'Features Part 1 of 2', 'echo-knowledge-base' ),
				'sub_label'	=> esc_html__( 'Main Page', 'echo-knowledge-base' ),
				'header_escaped'    => $this->wizard_step_header( array(
					'title_html'        => '',
					'info_title'        => '',
					'info_description'  => '',
				) ),
				'content_escaped'   => $this->wizard_step_blocks_or_shortcode( $step_number ),
				'step_number_label'	=> 2,
				'steps_bar_css_class' => 'epkb-setup-wizard-step-part-1-of-2',
				'header_css_class'	=> 'epkb-wc-step-header--mp-type',
			];
		}

		// Step: Modules Part 2 of 2
		$step_number++;
		$setup_steps_config[] = [
			'label'     => EPKB_Block_Utilities::is_block_enabled() && $this->is_main_page_missing ? esc_html__( 'Features Part 2 of 2', 'echo-knowledge-base' ) : esc_html__( 'Features', 'echo-knowledge-base' ),
			'sub_label' => esc_html__( 'Main Page', 'echo-knowledge-base' ),
			'header_escaped'    => $this->wizard_step_header( array(
				'title_html'        => esc_html__( 'Customize KB Main Page', 'echo-knowledge-base' ),
				'info_title'        => sprintf( esc_html__( 'The page is divided into rows. Simply select which features, called %s, you want to display in each row.', 'echo-knowledge-base' ),
											'<span class="epkb-setup-wizard-step__topic">' . esc_html__( 'Modules', 'echo-knowledge-base' ) . '</span>' ),
				'info_description'  => esc_html__( 'Feel free to experiment with different arrangements. You can make additional changes at any time, either on this page or in the Knowledge Base Settings.', 'echo-knowledge-base' ),
			) ),
			'content_escaped'   => $this->wizard_step_modules_content( $step_number ),
			'step_number_label'	=> 2,
			'steps_bar_css_class' => EPKB_Block_Utilities::is_block_enabled() && $this->is_main_page_missing ? 'epkb-setup-wizard-step-part-2-of-2' : '',
		];

		// Step: Layout
		$step_number++;
		$setup_steps_config[] = [
			'label'     => esc_html__( 'Layout', 'echo-knowledge-base' ),
			'sub_label' => esc_html__( 'Main Page', 'echo-knowledge-base' ),
			'header_escaped'    => $this->wizard_step_header( array(
					'title_html'        => esc_html__( 'Choose Layout Matching Your Needs', 'echo-knowledge-base' ),
					'info_title'        => esc_html__( 'Each layout offers a different way to show categories and articles. Layout features are explained below.', 'echo-knowledge-base' ),
					'info_description'  => esc_html__( 'Don\'t hesitate to try out various layouts. You can change your KB Layout at any time.', 'echo-knowledge-base' ),
					'info_html'         => $this->is_old_elay
						? EPKB_HTML_Forms::notification_box_middle( array(
							'type' => 'error',
							'desc' => '<p>' . esc_html__( 'Modular Main Page feature is supported for Sidebar and Grid layouts in the "KB - Elegant Layouts" add-on version higher than 2.14.1.', 'echo-knowledge-base' ) .
								'<br>' . sprintf( esc_html__( 'Please %supgrade%s the add-on to use Modular Main Page feature for the Sidebar and Grid layouts.', 'echo-knowledge-base' ), '<a href="https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/" target="_blank">', '</a>' ) . '</p>',
						), true )
						: '',
				) ),
			'content_escaped'   => $this->wizard_step_modular_layout_content( $step_number ),
			'step_number_label'	=> 3,
		];

		// Step: Designs
		$step_number++;
		$setup_steps_config[] = [
			'label'     => esc_html__( 'Designs', 'echo-knowledge-base' ),
			'sub_label' => esc_html__( 'Main Page', 'echo-knowledge-base' ),
			'header_escaped'    => $this->wizard_step_header( array(
				'title_html'        => esc_html__( 'Select a Design that best matches your requirements (Optional Step)', 'echo-knowledge-base' ),
				'info_title'        => '', // esc_html__( 'Select a Design that best matches your site theme or requirements.', 'echo-knowledge-base' ),
				'info_description_icon' => 'paint-brush',
				'info_description'  => esc_html__( 'You can easily fine-tune colors and other elements later on the Settings page.', 'echo-knowledge-base' ),
				'content_show_option'  => array(
					'current_layout' => $this->kb_config['kb_main_page_layout'],
					'text'          => esc_html__( 'Do you want to change the style and colors of the KB Main Page using one of our designs?', 'echo-knowledge-base' ),
				)
			) ),
			'content_escaped'   => $this->wizard_step_designs_content( $step_number ),
			'step_number_label'	=> 4,
			'header_css_class'	=> 'epkb-wc-step-header--design',
		];

		// Step: Article Page
		$step_number++;
		$setup_steps_config[] = [
			'label'     => esc_html__( 'Article Page', 'echo-knowledge-base' ),
			'header_escaped'    => $this->wizard_step_header( array(
				'title_html'        => esc_html__( 'Setup Your Article Page', 'echo-knowledge-base' ),
				'info_title'        => esc_html__( 'Article pages can have navigation links in the left sidebar or in the right sidebar.', 'echo-knowledge-base' ),
			) ),
			'content_escaped'   => $this->wizard_step_modular_navigation_content( $step_number ),
			'step_number_label'	=> 5,
			'header_css_class'	=> 'epkb-wc-step-header--article-page',
		];  ?>

		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-wizard-container">
			<div class="<?php echo 'epkb-config-setup-wizard-modular'; echo $this->is_setup_run_first_time ? ' ' . 'epkb-config-setup-wizard-modular--first-setup' : ''; ?>" id="epkb-config-wizard-content">

				<!------- Wizard Steps Bar ------------>
				<div class="epkb-setup-wizard-steps-bar">   <?php
					foreach ( $setup_steps_config as $step_index => $step_config ) {   ?>
						<div data-step="<?php echo esc_attr( $step_index + 1 ); ?>" class="epkb-setup-wizard-step-tab epkb-setup-wizard-step-tab--<?php echo esc_attr( $step_index + 1 ); echo $step_index == 0 ? ' ' . 'epkb-setup-wizard-step-tab--active' : ''; echo empty( $step_config['steps_bar_css_class'] ) ? '' : ' ' . esc_attr( $step_config['steps_bar_css_class'] ); ?>">
							<div class="epkbfa epkbfa-check-circle epkb-setup-wizard-step-tab__icon"></div>
							<div class="epkb-setup-wizard-step-tab__number"><?php echo esc_html( $step_config['step_number_label'] ); ?></div>
							<div class="epkb-setup-wizard-step-tab__label"><?php
								if ( ! empty( $step_config['sub_label'] ) ) {   ?>
									<span class="epkb-setup-wizard-step-tab__sub-label"><?php echo esc_html( $step_config['sub_label'] ); ?></span><?php
								}
								echo esc_html( $step_config['label'] ); ?>
							</div>
						</div>  <?php
						if ( ( $step_index + 1 ) < count( $setup_steps_config ) ) {    ?>
							<div class="epkb-setup-wizard-step-tab-divider">
								<i class="epkbfa epkbfa-chevron-right"></i>
								<i class="epkbfa epkbfa-chevron-right"></i>
							</div>  <?php
						}
					}   ?>
				</div>

				<div class="epkb-config-wizard-inner">

					<!------- Wizard Header ------------>
					<div class="epkb-wizard-header">    <?php
						foreach ( $setup_steps_config as $step_index => $step_config ) {
							$class = ( $step_index + 1 ) . ( $step_index == 0 ? ' ' . 'epkb-wc-step-header--active' : '' ); ?>
							<div class="epkb-wc-step-header epkb-wc-step-header--<?php echo esc_attr( $class ); echo empty( $step_config['header_css_class'] ) ? '' : ' ' . $step_config['header_css_class']; ?>"> <?php
								//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo $step_config['header_escaped'];   ?>
							</div>  <?php
						}   ?>
					</div>

					<!------- Wizard Content ---------->
					<div class="epkb-wizard-content">   <?php

						if ( $this->kb_config['modular_main_page_toggle'] == 'off' ) {
							$notification_escaped = EPKB_HTML_Forms::notification_box_middle( array(
								'type' => 'error',
								'desc' => esc_html__( 'Please switch to Modules in Settings UI before proceeding with Setup Wizard', 'echo-knowledge-base' ),
							), true );
							echo $notification_escaped;
						} else {
							foreach ( $setup_steps_config as $step_index => $step_config ) {
								//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo $step_config['content_escaped'];
							}
						}		  ?>

					</div>

					<!------- Wizard Footer ---------->
					<div class="epkb-wizard-footer">

						<!----First Step Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-1-panel-button epkb-wc-step-panel-button epkb-wc-step-panel-button--active">
							<div class="epkb-wizard-button-container__inner">
								<button value="2" class="epkb-wizard-button epkb-setup-wizard-button-next">
									<span class="epkb-setup-wizard-button-next__text"><?php esc_html_e( 'Next Step', 'echo-knowledge-base' ); ?>&nbsp;&gt;</span>
								</button>
							</div>
						</div>

						<!----Middle Steps Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-2-panel-button epkb-wc-step-panel-button">
							<div class="epkb-wizard-button-container__inner">
								<button value="1" class="epkb-wizard-button epkb-setup-wizard-button-prev">
									<span class="epkb-setup-wizard-button-prev__text">&lt;&nbsp;<?php esc_html_e( 'Previous Step', 'echo-knowledge-base' ); ?></span>
								</button>
								<button value="3" class="epkb-wizard-button epkb-setup-wizard-button-next">
									<span class="epkb-setup-wizard-button-next__text"><?php esc_html_e( 'Next Step', 'echo-knowledge-base' ); ?>&nbsp;&gt;</span>
								</button>
							</div>
						</div>

						<!----Last Step Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-3-panel-button epkb-wc-step-panel-button">
							<div class="epkb-wizard-button-container__inner">
								<button value="<?php echo esc_attr( count( $setup_steps_config ) - 1 ); ?>" class="epkb-wizard-button epkb-setup-wizard-button-prev">
									<span class="epkb-setup-wizard-button-prev__text">&lt;&nbsp;<?php esc_html_e( 'Previous Step', 'echo-knowledge-base' ); ?></span>
								</button>
								<button value="apply" class="epkb-wizard-button epkb-setup-wizard-button-apply" data-wizard-type="setup"><?php esc_html_e( 'Finish Set Up', 'echo-knowledge-base' ); ?></button>

								<input type="hidden" id="_wpnonce_epkb_ajax_action" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_epkb_ajax_action" ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
							</div>
						</div>

					</div>

					<input type="hidden" id="epkb_wizard_kb_id" name="epkb_wizard_kb_id" value="<?php echo esc_attr( $this->kb_config['id'] ); ?>"/>

					<div class="eckb-bottom-notice-message"></div>

				</div>
			</div>

		</div>		<?php

		// Report error form
		EPKB_HTML_Admin::display_report_admin_error_form();

		// Success message
		EPKB_HTML_Forms::dialog_confirm_action( [
			'id'           => 'epkb-wizard-success-message',
			'title'        => esc_html__( 'Success', 'echo-knowledge-base' ),
			'body'         => esc_html__( 'Wizard Completed Successfully.', 'echo-knowledge-base' ),
			'accept_label' => esc_html__( 'Ok', 'echo-knowledge-base' ),
			'accept_type'  => 'success'
		] );
	}

	/**
	 * Setup Wizard: Step 1 - Title & URL
	 *
	 * @param $step_number
	 * @return false|string
	 */
	private function wizard_step_title_url_content( $step_number ) {

		ob_start();     ?>

		<div id="epkb-wsb-step-<?php echo esc_attr( $step_number ); ?>-panel" class="epkb-wc-step-panel eckb-wizard-step-url eckb-wizard-step-<?php echo esc_attr( $step_number ); ?> epkb-wc-step-panel--active epkb-wizard-theme-step-<?php echo esc_attr( $step_number ); ?>">  <?php

			// KB Name
		    EPKB_HTML_Elements::text(
				array(
					'label'             => esc_html__('Knowledge Base Nickname', 'echo-knowledge-base'),
					'placeholder'       => esc_html__('Knowledge Base', 'echo-knowledge-base'),
					'main_tag'          => 'div',
					'input_group_class' => 'epkb-wizard-row-form-input epkb-wizard-name',
					'value'             => $this->kb_config['kb_name']
				)
			);      ?>
			<div class="epkb-wizard-row-form-input">
				<div class="epkb-wizard-col2">
					<p class="epkb-wizard-input-desc"><?php
						echo esc_html__( 'Give your Knowledge Base a name. The name will show when we refer to it or when you see a list of post types.', 'echo-knowledge-base' ) .
						     '</br>' . esc_html__( 'Examples: Knowledge Base, Help, Support', 'echo-knowledge-base' );							?>
					</p>
				</div>
			</div>			<?php

			// KB Slug - if Setup Wizard is run first time or no KB Main Pages exist, then show input field
			$main_pages = EPKB_KB_Handler::get_kb_main_pages( $this->kb_config );
			if ( $this->is_setup_run_first_time || empty( $main_pages ) ) {
				EPKB_HTML_Elements::text(
					array(
						'label'             => esc_html__( 'Knowledge Base Slug', 'echo-knowledge-base' ),
						'placeholder'       => 'knowledge-base',
						'main_tag'          => 'div',
						'readonly'          => ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ),
						'input_group_class' => 'epkb-wizard-row-form-input epkb-wizard-slug',
						'value'             => $this->kb_config['kb_articles_common_path'],
					)
				);      ?>
				<div class="epkb-wizard-row-form-input">
					<div class="epkb-wizard-col2">
						<p id="epkb-wizard-slug-error"><?php esc_html_e( 'The slug should not contain full KB URL.', 'echo-knowledge-base' ); ?></p>
						<p class="epkb-wizard-input-desc"><?php esc_html_e( 'This KB slug is part of your full knowledge base URL:', 'echo-knowledge-base' ); ?></p>
						<p class="epkb-wizard-input-desc"><span><?php echo esc_url( site_url() ); ?></span> / <span id="epkb-wizard-slug-target"><?php echo esc_html( $this->kb_config['kb_articles_common_path'] ); ?></span></p>
					</div>
				</div>				<?php

			// KB Slug - if user re-run Setup Wizard, then only show slug with Link to change it (KB URL)
			} else {
				$main_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $this->kb_config );
				$main_page_slug = EPKB_Core_Utilities::get_main_page_slug( $main_page_id );
				$main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config );
				EPKB_HTML_Elements::text(
					array(
						'label'             => esc_html__( 'Knowledge Base Slug', 'echo-knowledge-base' ),
						'placeholder'       => 'knowledge-base',
						'main_tag'          => 'div',
						'readonly'          => ! ( EPKB_Utilities::get_wp_option( 'epkb_not_completed_setup_wizard_' . $this->kb_config['id'], false ) && EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ),
						'input_group_class' => 'epkb-wizard-row-form-input epkb-wizard-slug',
						'value'             => $main_page_slug,
					)
				);      ?>
				<div class="epkb-wizard-row-form-input">
					<div class="epkb-wizard-col2">
						<p class="epkb-wizard-input-desc"><?php esc_html_e( 'This is KB slug that is part of your full knowledge base URL:', 'echo-knowledge-base' ); ?></p>
						<a class="epkb-wizard-input-desc" href="<?php echo esc_url( $main_page_url ); ?>" target="_blank"><?php echo esc_html( $main_page_url ); ?></a><?php
						if ( current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) ) {   ?>
							<p class="epkb-wizard-input-desc">
								<a href="https://www.echoknowledgebase.com/documentation/changing-permalinks-urls-and-slugs/" target="_blank"><?php esc_html_e( 'Need to change KB URL?', 'echo-knowledge-base' ); ?>
								<span class="ep_font_icon_external_link"></span>
								</a>
							</p>    <?php
						}   ?>
					</div>
				</div>				<?php
			}

			// if we have menus and menus without link
			$menus = $this->kb_menus_without_item();
			if ( is_array( $menus ) && ! empty( $menus ) ) {      ?>

				<div class="input_group epkb-wizard-row-form-input epkb-wizard-menus" >
					<label><?php esc_html_e( 'Add KB to Website Menu', 'echo-knowledge-base' ); ?></label>
					<ul>	<?php
						foreach ($menus as $menu_id => $menu_title) {
							EPKB_HTML_Elements::checkbox( array(
								'name'              => 'epkb_menu_' . $menu_id,
								'label'             => $menu_title,
								'input_group_class' => 'epkb-menu-checkbox',
								'value'             => 'off'
							) );
						}           ?>
					</ul>
				</div>
				<div class="epkb-wizard-row-form-input">
				<div class="epkb-wizard-col2">
					<p class="epkb-wizard-input-desc"><?php esc_html_e( 'Choose the website menu(s) where users will access the Knowledge Base. You can change it at any time in WordPress -> Appearance -> Menus.', 'echo-knowledge-base' ); ?></p>
				</div>
				</div><?php

			}       ?>
		</div>	<?php

		return ob_get_clean();
	}

	/**
	 * Find menu items with a link to KB
	 *
	 * @return array|bool - true on ERROR,
	 *                      false if found a menu with KB link
	 *                      empty array if no menu exists
	 *                      non-empty array for existing menus.
	 */
	private function kb_menus_without_item() {

		$menus = wp_get_nav_menus();
		if ( empty( $menus ) || ! is_array( $menus ) ) {
			return array();
		}

		$kb_main_pages_info = EPKB_KB_Handler::get_kb_main_pages( $this->kb_config );

		// check if we have any menu item with KB page
		$menu_without_kb_links = array();
		foreach ( $menus as $menu ) {

			// does menu have any menu items?
			$menu_items = wp_get_nav_menu_items( $menu );
			if ( empty( $menu_items ) && ! is_array( $menu_items ) )  {
				continue;
			}

			foreach ( $menu_items as $item ) {

				// true if we already have KB link in menu
				if ( $item->object == 'page' && isset( $kb_main_pages_info[$item->object_id] ) ) {
					return false; // use this string to show menus without KB link only if ALL menus have no KB links
				}
			}

			$menu_without_kb_links[$menu->term_id] = $menu->name;
		}

		return $menu_without_kb_links;
	}

	/**
	 * Determine what sidebar set up the user has and return corresponding selection id.
	 *
	 * @param $kb_config
	 * @return int
	 */
	public static function get_current_sidebar_selection( $kb_config ) {

		if ( $kb_config['article-left-sidebar-toggle'] == 'on' && isset( $kb_config['article_sidebar_component_priority']['nav_sidebar_left'] ) && (int)$kb_config['article_sidebar_component_priority']['nav_sidebar_left'] ) {

			// Articles and Categories Navigation: Left Side
			if ( $kb_config['article_nav_sidebar_type_left'] == 'eckb-nav-sidebar-v1' ) {
				return 1;
			}

			// Top Categories Navigation: Left Side
			if ( $kb_config['article_nav_sidebar_type_left'] == 'eckb-nav-sidebar-categories' ) {
				return 3;
			}

			// Current Category and Articles: Left Side
			if ( $kb_config['article_nav_sidebar_type_left'] == 'eckb-nav-sidebar-current-category' ) {
				return 5;
			}
		}

		if ( $kb_config['article-right-sidebar-toggle'] == 'on' && isset( $kb_config['article_sidebar_component_priority']['nav_sidebar_right'] ) && (int)$kb_config['article_sidebar_component_priority']['nav_sidebar_right'] ) {

			// Articles and Categories Navigation: Right Side
			if ( $kb_config['article_nav_sidebar_type_right'] == 'eckb-nav-sidebar-v1' ) {
				return 2;
			}

			// Top Categories Navigation: Right Side
			if ( $kb_config['article_nav_sidebar_type_right'] == 'eckb-nav-sidebar-categories' ) {
				return 4;
			}

			// Current Category and Articles: Right Side
			if ( $kb_config['article_nav_sidebar_type_right'] == 'eckb-nav-sidebar-current-category' ) {
				return 6;
			}
		}

		// No Navigation/Default
		return 7;
	}

	/**
	 *  Setup Wizard: Modular Step - Choose Layout
	 *
	 * @param $step_number
	 * @return false|string
	 */
	private function wizard_step_modular_layout_content( $step_number ) {

		$layouts_config = [];

		$layouts_config['Basic'] = [
			'layout_title'          => esc_html__( 'Basic Layout', 'echo-knowledge-base' ),
			'layout_description'    => esc_html__( 'The Basic Layout offers a user-friendly grid format for viewing categories, subcategories, and articles. Expand and collapse article lists for easy navigation.', 'echo-knowledge-base' ),
			'layout_image'          => [
				'title' => esc_html__( 'Basic', 'echo-knowledge-base' ),
				'url'   => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-3/Basic-Layout-Standard.jpg',
			],
			'layout_features'       => [
				esc_html__( 'Two levels of categories are initially displayed.', 'echo-knowledge-base' ),
				esc_html__( 'Articles from the top categories are also listed.', 'echo-knowledge-base' ),
			],
			/* 'youtube_link'          => [
				'title' => esc_html__( 'Watch our Video', 'echo-knowledge-base' ),
				'url'   => '#',
			], */
			'demo_link'             => [
				'title' => esc_html__( 'Try out our Demo', 'echo-knowledge-base' ),
				'url'   => 'https://www.echoknowledgebase.com/demo-1-knowledge-base-basic-layout/',
			],
		];
		$layouts_config['Classic'] = [
			'layout_title'          => esc_html__( 'Classic Layout', 'echo-knowledge-base' ),
			'layout_description'    => esc_html__( 'The Classic Layout offers a simple, compressed view of top-level categories. Click to expand each category and see its associated articles and subcategories.', 'echo-knowledge-base' ),
			'layout_image'          => [
				'title' => esc_html__( 'Classic', 'echo-knowledge-base' ),
				'url'   => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-3/Classic-Layout-Standard.jpg',
			],
			'layout_features'       => [
				esc_html__( 'Initially, only top categories are listed.', 'echo-knowledge-base' ),
				esc_html__( 'Users can click to expand and view articles and sub-categories in a compact format.', 'echo-knowledge-base' ),
			],
			/* 'youtube_link'          => [
				'title' => esc_html__( 'Watch our Video', 'echo-knowledge-base' ),
				'url'   => '#',
			], */
			'demo_link'             => [
				'title' => esc_html__( 'Try out our Demo', 'echo-knowledge-base' ),
				'url'   => 'https://www.echoknowledgebase.com/demo-12-knowledge-base-image-layout/',
			],
		];
		$layouts_config['Drill-Down'] = [
			'layout_title'          => esc_html__( 'Drill Down Layout', 'echo-knowledge-base' ),
			'layout_description'    => esc_html__( 'The Drill Down Layout helps you navigate large knowledge bases easily. Click top categories to progressively reveal articles and subcategories.', 'echo-knowledge-base' ),
			'layout_video'          => [
				'title' => esc_html__( 'Drill Down', 'echo-knowledge-base' ),
				'url'   => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-3/drill-down-example-2.webm',
			],
			'layout_features'       => [
				esc_html__( 'Only top categories are initially listed.', 'echo-knowledge-base' ),
				esc_html__( 'Users can click to reveal articles and sub-categories in an extensive format.', 'echo-knowledge-base' ),
			],
			/* 'youtube_link'          => [
				'title' => esc_html__( 'Watch our Video', 'echo-knowledge-base' ),
				'url'   => '#',
			], */
			'demo_link'             => [
				'title' => esc_html__( 'Try out our Demo', 'echo-knowledge-base' ),
				'url'   => 'https://www.echoknowledgebase.com/demo-4-knowledge-base-tabs-layout/',
			],
		];
		$layouts_config['Tabs'] = [
			'layout_title'          => esc_html__( 'Tabs Layout', 'echo-knowledge-base' ),
			'layout_description'    => esc_html__( 'The Tab Layout clearly organizes top categories for subject-specific browsing. Within each tab, find related articles and sub-categories.', 'echo-knowledge-base' ),
			'layout_image'          => [
				'title' => esc_html__( 'Tabs', 'echo-knowledge-base' ),
				'url'   => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-3/Tab-Layout-Standard.jpg',
			],
			'layout_features'       => [
				esc_html__( 'Top categories are presented as tabs.', 'echo-knowledge-base' ),
				esc_html__( 'Each tab page follows a structure similar to the Basic layout.', 'echo-knowledge-base' ),
			],
			/* 'youtube_link'          => [
				'title' => esc_html__( 'Watch our Video', 'echo-knowledge-base' ),
				'url'   => '#',
			], */
			'demo_link'             => [
				'title' => esc_html__( 'Try out our Demo', 'echo-knowledge-base' ),
				'url'   => 'https://www.echoknowledgebase.com/demo-3-knowledge-base-tabs-layout/',
			],
		];
		$layouts_config['Categories'] = [
			'layout_title'          => esc_html__( 'Category Focused Layout', 'echo-knowledge-base' ),
			'layout_description'    => esc_html__( 'The Categories layout resembles the Basic layout but includes the number of articles beside each category name.', 'echo-knowledge-base' ),
			'layout_image'          => [
				'title' => esc_html__( 'Category Focused', 'echo-knowledge-base' ),
				'url'   => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-3/Category-Layout-Standard.jpg',
			],
			'layout_features'       => [
				esc_html__( 'Lists two levels of categories. Sub categories link to their Category Archive page.', 'echo-knowledge-base' ),
				esc_html__( 'Displays the number of articles in each category.', 'echo-knowledge-base' ),
			],
			/* 'youtube_link'          => [
				'title' => esc_html__( 'Watch our Video', 'echo-knowledge-base' ),
				'url'   => '#',
			], */
			'demo_link'             => [
				'title' => esc_html__( 'Try out our Demo', 'echo-knowledge-base' ),
				'url'   => 'https://www.echoknowledgebase.com/demo-14-category-layout/',
			],
		];
		$layouts_config['Grid'] = [
			'layout_title'          => esc_html__( 'Grid Layout', 'echo-knowledge-base' ),
			'layout_description'    => esc_html__( 'Grid layout presents top categories with the count of articles in each. Clicking on a category navigates the user to either an article page or a category archive page.', 'echo-knowledge-base' ),
			'layout_image'          => [
				'title' => esc_html__( 'Grid', 'echo-knowledge-base' ),
				'url'   => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-3/Grid-Layout-Standard.jpg',
			],
			'layout_features'       => [
				esc_html__( 'Initially displays only top categories.', 'echo-knowledge-base' ),
				esc_html__( 'Clicking on a category leads to the first article or the category archive page.', 'echo-knowledge-base' ),
			],
			/* 'youtube_link'          => [
				'title' => esc_html__( 'Watch our Video', 'echo-knowledge-base' ),
				'url'   => '#',
			], */
			'demo_link'             => [
				'title' => esc_html__( 'Try out our Demo', 'echo-knowledge-base' ),
				'url'   => 'https://www.echoknowledgebase.com/demo-5-knowledge-base-grid-layout/',
			],
		];
		$layouts_config['Sidebar'] = [
			'layout_title'          => esc_html__( 'Sidebar Layout', 'echo-knowledge-base' ),
			'layout_description'    => esc_html__( 'The Sidebar layout features a navigation sidebar alongside articles on both the Knowledge Base (KB) Main Page and KB Article Pages.', 'echo-knowledge-base' ),
			'layout_image'          => [
				'title' => esc_html__( 'Sidebar', 'echo-knowledge-base' ),
				'url'   => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-3/Sidebar-Layout-Standard.jpg',
			],
			'layout_features'       => [
				esc_html__( 'The article navigation sidebar is always visible.', 'echo-knowledge-base' ),
				esc_html__( 'The KB Main Page includes introductory text.', 'echo-knowledge-base' ),
			],
			/* 'youtube_link'          => [
				'title' => esc_html__( 'Watch our Video', 'echo-knowledge-base' ),
				'url'   => '#',
			], */
			'demo_link'             => [
				'title' => esc_html__( 'Try out our Demo', 'echo-knowledge-base' ),
				'url'   => 'https://www.echoknowledgebase.com/demo-7-knowledge-base-sidebar-layout/',
			],
		];

		// move the current layout to the top so the user can see it. Simply move the active layout to the top of the Layout step page
		$current_layout = $this->kb_config['kb_main_page_layout'];
		$active_layout = $layouts_config[ $current_layout ];
		unset( $layouts_config[ $current_layout ] );
		$layouts_config = array_merge( [ $current_layout => $active_layout ], $layouts_config );

		// add the get pro link to the layouts if the user does not have the pro version
		if ( ! $this->elay_enabled ) {
			$layouts_config['Grid']['get_pro_link'] = [
				'url'   => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/',
			];
			$layouts_config['Sidebar']['get_pro_link'] = [
				'url'   => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/',
			];
		}

		ob_start();  ?>

		<div id="epkb-wsb-step-<?php echo esc_attr( $step_number ); ?>-panel" class="epkb-setup-wizard-theme epkb-wc-step-panel eckb-wizard-step-layout eckb-wizard-step-<?php echo esc_attr( $step_number ); ?>">

			<div class="epkb-setup-wizard-no-categories-articles-message"><?php esc_html_e( 'Categories & Articles module was not selected in previous step.', 'echo-knowledge-base' ); ?></div>

			<div class="epkb-setup-wizard-step-container epkb-setup-wizard-step-container--layout">
				<input type="hidden" id="_wpnonce_epkb_ajax_action" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_epkb_ajax_action" ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"/>    <?php

				foreach ( $layouts_config as $layout_name => $layout_config ) { ?>

					<!-- Layout -->
					<div class="epkb-setup-wizard-step__item">

						<!-- Title -->
						<div class="epkb-setup-wizard-step__item-title"><span><?php echo esc_html( $layout_config['layout_title'] ); ?></span></div>

						<!-- Content -->
						<div class="epkb-setup-wizard-step__item-content">

							<!-- Layout Selection -->
							<div class="epkb-setup-wizard-step__item-selection">
								<div class="epkb-setup-option-container<?php echo $layout_name == $this->kb_config['kb_main_page_layout'] ? ' ' . 'epkb-setup-option-container--active' : ''; ?>">
									<div class="epkb-setup-option__inner">
										<div class="epkb-setup-option__selection">				<?php 
										
											if ( isset( $layout_config['layout_image'] ) ) { 		?>
												<div class="epkb-setup-option__option-container">
													<label class="epkb-setup-option__option__label">
														<span><?php echo esc_html( $layout_config['layout_image']['title'] ); ?></span>
													</label>
												</div>
												<div class="epkb-setup-option__featured-img-container">
													<img class="epkb-setup-option__featured-img" src="<?php echo esc_url( $layout_config['layout_image']['url'] ); ?>" title="<?php echo esc_attr( $layout_config['layout_image']['title'] ); ?>" alt="<?php echo esc_attr( $layout_config['layout_image']['title'] ); ?>" />
												</div>									<?php 
											} elseif ( isset( $layout_config['layout_video'] ) ) { 			?>
												<div class="epkb-setup-option__option-container">
													<label class="epkb-setup-option__option__label">
														<span><?php echo esc_html( $layout_config['layout_video']['title'] ); ?></span>
													</label>
												</div>
												<div class="epkb-setup-option__featured-vid-container">
													<video class="epkb-setup-option__featured-vid" autoplay loop muted>
														<source src="<?php echo esc_url( $layout_config['layout_video']['url'] ); ?>" type="video/webm">
													</video>
												</div>											<?php 
											} ?>

										</div>
									</div>
								</div>
							</div>

							<!-- Layout Description -->
							<div class="epkb-setup-wizard-step__item-description">

								<!-- Description Text -->
								<div class="epkb-setup-wizard-step__item-description-text"><?php echo esc_html( $layout_config['layout_description'] ); ?></div>  <?php

								// Choose/Selected Button
								if ( isset( $layout_config['get_pro_link'] ) ) {    ?>
									<button class="epkb-success-btn epkb-setup-wizard-step__item-description__button-pro" data-target="<?php echo esc_attr( 'epkb-dialog-pro-feature-ad-' . strtolower( $layout_name ) ); ?>"><?php esc_html_e( 'Choose', 'echo-knowledge-base'); ?></button> <?php
								} else {    ?>
									<label class="epkb-setup-wizard-step__item-description__option__label">
										<input type="radio" name="epkb-layout" value="<?php echo esc_attr( $layout_name ); ?>"<?php checked( $layout_name, $this->kb_config['kb_main_page_layout'] ); ?>>
									</label> <?php
								} ?>

								<!-- Key Features Title -->
								<div class="epkb-setup-wizard-step__item-description-features-title"><?php esc_html_e( 'Key Features', 'echo-knowledge-base'); ?></div>

								<!-- Features -->
								<ul class="epkb-setup-wizard-step__item-description-features">   <?php
									foreach ( $layout_config['layout_features'] as $index => $feature ) {  ?>
										<li data-feature="<?php echo esc_attr( $index + 1 ); ?>"><?php echo wp_kses( $feature, EPKB_Utilities::get_admin_ui_extended_html_tags() ); ?></li><?php
									}   ?>
								</ul>   <?php

								if ( isset( $layout_config['youtube_link'] ) || isset( $layout_config['demo_link'] ) ) {    ?>
									<!-- Links -->
									<div class="epkb-setup-wizard-step__item-description-links">   <?php

										if ( isset( $layout_config['youtube_link'] ) ) {   ?>
											<!-- Youtube Link -->
											<div class="epkb-setup-wizard-step__item-description-link epkb-setup-wizard-step__item-youtube-link">
												<a href="<?php echo esc_url( $layout_config['youtube_link']['url'] ); ?>" target="_blank"><?php echo esc_html( $layout_config['youtube_link']['title'] ); ?></a>
											</div>  <?php
										}

										if ( isset( $layout_config['demo_link'] ) ) {  ?>
											<!-- Demo Link -->
											<div class="epkb-setup-wizard-step__item-description-link epkb-setup-wizard-step__item-demo-link">
												<a href="<?php echo esc_url( $layout_config['demo_link']['url'] ); ?>" target="_blank"><?php echo esc_html( $layout_config['demo_link']['title'] ); ?></a>
											</div>  <?php
										}   ?>

									</div>  <?php
								}   ?>
							</div>
						</div>
					</div>  <?php
					if ( isset( $layout_config['get_pro_link'] ) ) {
						EPKB_HTML_Forms::dialog_pro_feature_ad( array(
							'id' => 'epkb-dialog-pro-feature-ad-' . strtolower( $layout_name ),
							'title' => sprintf(__("Unlock %s" . $layout_config['layout_title'] . " Feature%s By Upgrading to PRO ", 'echo-knowledge-base'), '<strong>', '</strong>'),
							'list' => array( esc_html__( 'Grid Layout for the Main Page', 'echo-knowledge-base'), esc_html__( 'Sidebar Layout for the Main Page', 'echo-knowledge-base'),
											__( 'Resource Links feature for the Main Page', 'echo-knowledge-base')),
							'btn_text' => esc_html__('Upgrade Now', 'echo-knowledge-base'),
							'btn_url' => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/',
							'show_close_btn' => 'yes',
							'return_html' => true,
						));
					}
				}   ?>

			</div>

		</div>	<?php

		return ob_get_clean();
	}

	/**
	 * Return HTML for Step Header based on args
	 *
	 * @param $args
	 * @return false|string
	 */
	private static function wizard_step_header( $args ) {
		ob_start();     ?>
		<div class="epkb-wizard-header__info">
			<h1 class="epkb-wizard-header__info__title"><?php echo wp_kses( $args['title_html'], EPKB_Utilities::get_admin_ui_extended_html_tags() ); ?></h1>
		</div>
		<div class="epkb-setup-wizard-theme-header">
			<h2 class="epkb-setup-wizard-theme-header__info__title"><?php echo wp_kses( $args['info_title'], EPKB_Utilities::get_admin_ui_extended_html_tags() ); ?></h2>  <?php
			if ( isset( $args['info_description'] ) ) { ?>
				<h2 class="epkb-setup-wizard-theme-header__info__description">	<?php
					if ( isset ( $args[ 'info_description_icon'] ) ) { ?>
						<span class="epkb-setup-wizard-theme-header__info__description__icon epkbfa epkbfa-<?php esc_attr_e( $args[ 'info_description_icon'] ); ?>"></span>
					<?php }
					echo esc_html( $args['info_description'] ); ?></h2>
				<?php
			}
			if ( isset( $args['info_html'] ) ) {
				echo wp_kses( $args['info_html'], EPKB_Utilities::get_admin_ui_extended_html_tags() );
			}   ?>
		</div>  <?php
		$first_time = EPKB_Core_Utilities::run_setup_wizard_first_time() || EPKB_Utilities::post( 'emkb_admin_notice' ) == 'kb_add_success';
		if ( ! $first_time && isset( $args['content_show_option'] ) ) { ?>
			<div class="epkb-setup-wizard-theme-content-show-option" data-current-layout="<?php echo esc_attr( $args['content_show_option']['current_layout'] ); ?>">
				<h5 class="epkb-setup-wizard-theme-content-show-option__text"><?php echo esc_html( $args['content_show_option']['text'] ); ?></h5> <?php
				EPKB_HTML_Elements::checkbox_toggle( [
					'name' => 'epkb-setup-wizard-theme-content-show-option__toggle',
					'toggleOnText'  => esc_html__( 'yes', 'echo-knowledge-base' ),
					'toggleOffText'  => esc_html__( 'no', 'echo-knowledge-base' ),
				] ); ?>
			</div> <?php
		}
		return ob_get_clean();
	}

	/**
	 * Setup Wizard: Modular Step - Choose blocks or shortcode to use for KB Main Page
	 *
	 * @param $step_number
	 * @return false|string
	 */
	private function wizard_step_blocks_or_shortcode( $step_number ) {

		ob_start();	?>
		<div id="epkb-wsb-step-<?php echo esc_attr( $step_number ); ?>-panel" class="epkb-wc-step-panel eckb-wizard-step-<?php echo esc_attr( $step_number ); ?>">
			<div class="epkb-setup-wizard-features-choices-list">

				<!-- Need Help -->
				<div class="epkb-setup-wizard-features-choice-info">
					<p><?php esc_html_e( 'KB Main Page displays categories and articles. Please decide how to display it, using either blocks or a shortcode.', 'echo-knowledge-base' ); ?></p>
					<p><?php esc_html_e( 'Need help deciding between Blocks and Shortcodes?', 'echo-knowledge-base' ); ?></p>
					<p><a class="epkb-setup-wizard-features-choice-info-link" href="#" target="_blank"><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); /* TODO: update URL */ ?></a></p>
				</div>


				<!-- Use Blocks -->
				<div class="epkb-setup-wizard-features-choice epkb-setup-wizard-features-choice--active">
					<div class="epkb-setup-wizard-features-choice__header"><span><?php esc_html_e( 'Use Blocks', 'echo-knowledge-base' ); ?></span><span class="epkb-setup-wizard-features-choice__header-label"><?php esc_html_e( 'Recommended', 'echo-knowledge-base' ); ?></span></div>
					<div class="epkb-setup-wizard-features-choice__body">
						<img alt="Blocks" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-2/blocks-choice.png' ); ?>">
					</div>
					<p class="epkb-setup-wizard-features-choice__footer"><?php esc_html_e( 'Create and customize your main page directly in the editor using drag-and-drop blocks.', 'echo-knowledge-base' ); ?></p>
					<label class="epkb-setup-wizard-features-choice__option__label">
						<input type="radio" name="epkb-main-page-type" value="kb-blocks"<?php checked( true ); ?>>
					</label>
				</div>

				<!-- Use Shortcode -->
				<div class="epkb-setup-wizard-features-choice">
					<div class="epkb-setup-wizard-features-choice__header"><span><?php esc_html_e( 'Use Shortcode', 'echo-knowledge-base' ); ?></span></div>
					<div class="epkb-setup-wizard-features-choice__body">
						<img alt="Shortcode" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-2/shortcode-choice.png' ); ?>">
					</div>
					<p class="epkb-setup-wizard-features-choice__footer"><?php esc_html_e( 'Insert a shortcode into your page and manage settings from the admin panel.', 'echo-knowledge-base' ); ?></p>
					<label class="epkb-setup-wizard-features-choice__option__label">
						<input type="radio" name="epkb-main-page-type" value="kb-shortcode"<?php checked( false ); ?>>
					</label>
				</div>

			</div>
		</div>	<?php

		return ob_get_clean();
	}

	/**
	 * Setup Wizard: Modular Step - Choose which Modules on which Row to display
	 *
	 * @return false|string
	 */
	private function wizard_step_modules_content( $step_number ) {

		$modules_rows_config = $this->get_modules_rows_config();
		$modules_presets_config = $this->get_modules_presets_config();

		$row_number = 1;
		$selected_modules_flag = true;
		$modules_total = count( $modules_rows_config );

		$sidebar_location_value = 'none';
		if ( $this->kb_config['ml_categories_articles_sidebar_toggle'] == 'on' ) {
			$sidebar_location_value = $this->kb_config['ml_categories_articles_sidebar_location'];
		}

		ob_start();  ?>

		<div id="epkb-wsb-step-<?php echo esc_attr( $step_number ); ?>-panel" class="epkb-wc-step-panel eckb-wizard-step-features eckb-wizard-step-<?php echo esc_attr( $step_number ); ?>">

			<div class="epkb-setup-wizard-step-container epkb-setup-wizard-step-container--modules">
				<input type="hidden" id="_wpnonce_epkb_ajax_action" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_epkb_ajax_action" );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>"/>

				<!-- Modules Rows List -->
				<div class="epkb-setup-wizard-module-rows-list">    <?php

					foreach ( $modules_rows_config as $module_name => $module_row_config ) {

						// Show Inactive Rows title before the first unselected module
						if ( $module_row_config['toggle_value'] == 'none' && $selected_modules_flag ) { ?>
							<div class="epkb-setup-wizard-hidden-rows-title epkb-setup-wizard-hidden-rows-title--active"><?php esc_html_e( 'Inactive Features', 'echo-knowledge-base' ); ?><span class="epkb-setup-wizard-hidden-rows-title__line"></span></div>    <?php
							$selected_modules_flag = false;
						}

						$elay_modules_disabled = in_array( $module_name, ['resource_links'] ) && ( ! $this->elay_enabled || $this->is_old_elay );         ?>

						<!-- Module Row -->
						<div class="epkb-setup-wizard-module-row<?php echo $module_row_config['toggle_value'] == 'none' ? '' : ' ' . 'epkb-setup-wizard-module-row--active';
								echo $elay_modules_disabled ? ' ' . 'epkb-setup-wizard-module-row--resource-link--disabled' : ''; ?>" data-row-module="<?php echo esc_attr( $module_name ); ?>">

							<!-- Module Row Left Settings -->
							<div class="epkb-setup-wizard-module-row-left-settings">
								<div class="epkb-setup-wizard-module-settings-title"> <?php
									echo esc_html( $module_row_config['label'] );
									EPKB_HTML_Elements::display_tooltip( '', '', array(), $module_row_config['tooltip_external_links'] ); ?>
								</div> <?php
								if ( $module_name == 'categories_articles' ) {  ?>
									<!-- Settings Row -->
									<div class="epkb-setup-wizard-module-settings-row epkb-setup-wizard-module-settings-row--sidebar">  <?php
										EPKB_HTML_Elements::radio_buttons_horizontal( [
											'name' => 'categories_articles_sidebar_location',
											'options' => [
												'none' => esc_html__( 'None', 'echo-knowledge-base' ),
												'left' => esc_html__( 'Left', 'echo-knowledge-base' ),
												'right' => esc_html__( 'Right', 'echo-knowledge-base' ),
											],
											'value' => $sidebar_location_value,
											'label' => esc_html__( 'Sidebar Visibility', 'echo-knowledge-base' ),
											'input_group_class' => 'epkb-setup-wizard-module-sidebar-selector',
										] );    ?>
									</div>  <?php
								}   ?>
							</div>

							<!-- Module Row Preview -->
							<div class="epkb-setup-wizard-module-row-preview">  <?php

								if ( $module_name == 'categories_articles' ) {   ?>
									<!-- Sidebar Left -->
									<div class="epkb-setup-wizard-module-sidebar epkb-setup-wizard-module-sidebar--left<?php echo $sidebar_location_value == 'left' ? ' ' . 'epkb-setup-wizard-module-sidebar--active' : ''; ?>">
										<img alt="" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-2/module-sidebar.jpg' ); ?>">
									</div>  <?php
								}

								if ( isset( $modules_presets_config[$module_name] ) ) {    ?>
									<!-- Module -->
									<div class="epkb-setup-wizard-module epkb-setup-wizard-module--<?php echo esc_attr( $module_name ); ?>">   <?php

										foreach ( $modules_presets_config[$module_name] as $layout_name => $layout_config ) {  ?>

											<!-- Layout -->
											<div class="epkb-setup-wizard-module-layout<?php echo empty( $layout_config['preselected'] ) ? '' : ' ' . 'epkb-setup-wizard-module-layout--active'; ?> epkb-setup-wizard-module-layout--<?php echo esc_attr( $layout_name ); ?>">  <?php
												foreach ( $layout_config['presets'] as $preset_name => $preset_config ) {    ?>
													<!-- Preset -->
													<div class="epkb-setup-wizard-module-preset<?php echo empty( $preset_config['preselected'] ) ? '' : ' ' . 'epkb-setup-wizard-module-preset--active'; ?> epkb-setup-wizard-module-preset--<?php echo esc_attr( $preset_name ); ?>">  <?php

														if ( $module_name == 'categories_articles' ) {
															$layouts = [
																'Basic'         => 'Basic-Layout-Standard-no-search.jpg',
																'Tabs'          => 'Tab-Layout-Standard-no-search.jpg',
																'Categories'    => 'Category-Layout-Standard-no-search.jpg',
																'Classic'       => 'Classic-Layout-Standard-no-search.jpg',
																'Drill-Down'    => 'Drill-Down-Layout-Standard-no-search.jpg',
																'Sidebar'       => 'Sidebar-Layout-Standard-no-search.jpg',
																'Grid'          => 'Grid-Layout-Standard-no-search.jpg'
															];

															$module_url = isset( $layouts[$layout_name] ) ? Echo_Knowledge_Base::$plugin_url . 'img/setup-wizard/step-2/' . $layouts[$layout_name] : '';

															echo '<img src="' . esc_url( $module_url ) . '">';

														} else {
															echo '<img src="' . esc_url( $preset_config['image_url'] ) . '">';
														}       ?>
													</div>  <?php
												}   ?>
											</div>  <?php

										}   ?>
									</div>  <?php
								}

								if ( $module_name == 'categories_articles' ) {   ?>
									<!-- Sidebar Right -->
									<div class="epkb-setup-wizard-module-sidebar epkb-setup-wizard-module-sidebar--right<?php echo $sidebar_location_value == 'right' ? ' ' . 'epkb-setup-wizard-module-sidebar--active' : ''; ?>">
										<img alt="" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-2/module-sidebar.jpg' ); ?>">
									</div>  <?php
								}   ?>

							</div>

							<!-- Module Row Right Settings -->
							<div class="epkb-setup-wizard-module-row-right-settings">

								<!-- Module -->
								<div class="epkb-setup-wizard-module">

									<!-- Settings Row -->
									<div class="epkb-setup-wizard-module-settings-row epkb-setup-wizard-module-settings-row--active epkb-setup-wizard-module-settings-row--allowed-modules"> <?php
										if ( $elay_modules_disabled ) {     	?>
											<button class="epkb-success-btn epkb-setup-wizard-module-row--resource-links-activate">
												<?php esc_html_e( 'Activate', 'echo-knowledge-base' ); ?>
											</button> <?php
										} else { ?>
											<span class="epkbfa epkbfa-chevron-down epkb-setup-wizard-module-row-sequence epkb-setup-wizard-module-row-sequence--down"
											      data-tooltip="<?php esc_html_e( 'Move Down', 'echo-knowledge-base' ); ?>"></span>
											<span class="epkbfa epkbfa-chevron-up epkb-setup-wizard-module-row-sequence epkb-setup-wizard-module-row-sequence--up"
											      data-tooltip="<?php esc_html_e( 'Move Up', 'echo-knowledge-base' ); ?>"></span> <?php
											EPKB_HTML_Elements::radio_buttons_horizontal( [
												'name' => 'module_row_toggle_' . $row_number,
												'options' => $module_row_config['toggle_options'],
												'value' => $module_row_config['toggle_value'],
												'input_group_class' => 'epkb-setup-wizard-module-row-toggle',
											] );
										} ?>
									</div>

								</div>
							</div>
						</div>  <?php

						// If all modules are selected, then render hidden Inactive Rows title at the end of modules list
						if ( $row_number == $modules_total && $selected_modules_flag ) { ?>
							<div class="epkb-setup-wizard-hidden-rows-title"><?php esc_html_e( 'Inactive Rows', 'echo-knowledge-base' ); ?><span class="epkb-setup-wizard-hidden-rows-title__line"></span></div>    <?php
							$selected_modules_flag = false;
						}

						$row_number++;
					}   ?>

				</div>
			</div> <?php

			EPKB_HTML_Forms::dialog_pro_feature_ad( array(
				'id'                => 'epkb-dialog-pro-feature-ad-resource-links',
				'title'             => sprintf( esc_html__( "Unlock %sResource Links Feature%s", 'echo-knowledge-base' ), '<strong>', '</strong>' ),
				'list'              => array( esc_html__( 'Grid Layout for the Main Page', 'echo-knowledge-base' ), esc_html__( 'Sidebar Layout for the Main Page', 'echo-knowledge-base' ), esc_html__( 'Resource Links feature for the Main Page', 'echo-knowledge-base' ) ),
				'btn_text'          => esc_html__( 'Upgrade Now', 'echo-knowledge-base' ),
				'btn_url'           => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/',
				'show_close_btn'    => 'yes',
				'return_html'       => true,
			) );    ?>

		</div>	<?php

		return ob_get_clean();
	}

	/**
	 * Setup Wizard: Modular Step - Choose Presets for selected Modules
	 *
	 * @param $step_number
	 * @return false|string
	 */
	private function wizard_step_designs_content( $step_number ) {

		$modules_presets_config = $this->get_modules_presets_config();

		ob_start();  ?>

		<div id="epkb-wsb-step-<?php echo esc_attr( $step_number ); ?>-panel" class="epkb-wc-step-panel eckb-wizard-step-design eckb-wizard-step-<?php echo esc_attr( $step_number ); ?>">

			<div class="epkb-setup-wizard-no-categories-articles-message"><?php esc_html_e( 'Categories & Articles module was not selected in previous step.', 'echo-knowledge-base' ); ?></div>

			<div class="epkb-setup-wizard-step-container epkb-setup-wizard-step-container--presets">
				<input type="hidden" id="_wpnonce_epkb_ajax_action" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_epkb_ajax_action" ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"/>

				<!-- Module Row -->
				<div class="epkb-setup-wizard-module-row">

					<!-- Module Preset Previews -->
					<div class="epkb-setup-wizard-module-preset-previews">

						<!-- Module -->
						<div class="epkb-setup-wizard-module epkb-setup-wizard-module--categories_articles">   <?php

							foreach ( $modules_presets_config['categories_articles'] as $layout_name => $layout_config ) {  ?>

								<!-- Layout -->
								<div class="epkb-setup-wizard-module-layout<?php echo empty( $layout_config['preselected'] ) ? '' : ' ' . 'epkb-setup-wizard-module-layout--active'; ?> epkb-setup-wizard-module-layout--<?php echo esc_attr( $layout_name ); ?>">  <?php

									foreach ( $layout_config['presets'] as $preset_name => $preset_config ) {    ?>
										<!-- Preset -->
										<div class="epkb-setup-wizard-module-preset<?php echo empty( $preset_config['preselected'] ) ? '' : ' ' . 'epkb-setup-wizard-module-preset--active'; ?> epkb-setup-wizard-module-preset--<?php echo esc_attr( $preset_name ); ?>">
											<img src="<?php echo esc_url( $preset_config['image_url'] ); ?>" alt="">
										</div>  <?php
									}   ?>
								</div>  <?php

							}   ?>
						</div>

					</div>

					<!-- Module Preset Settings -->
					<div class="epkb-setup-wizard-module-preset-settings">

						<!-- Module -->
						<div class="epkb-setup-wizard-module epkb-setup-wizard-module--categories_articles">   <?php

							foreach ( $modules_presets_config['categories_articles'] as $layout_name => $layout_config ) {
								$presets_titles = [];
								$preselected_preset = '';
								foreach ( $layout_config['presets'] as $preset_name => $preset_config ) {
									$presets_titles[$preset_name] = $preset_config['title'];
									if ( isset( $preset_config['preselected'] ) ) {
										$preselected_preset = $preset_name;
									}
								}   ?>
								<!-- Settings Row -->
								<div class="epkb-setup-wizard-module-settings-row<?php echo empty( $layout_config['preselected'] ) ? '' : ' ' . 'epkb-setup-wizard-module-settings-row--active'; ?> epkb-setup-wizard-module-settings-row--layout epkb-setup-wizard-module-settings-row--<?php echo esc_attr( $layout_name ); ?>">    <?php
									EPKB_HTML_Elements::radio_buttons_horizontal( [
										'name' => 'categories_articles_' . strtolower( $layout_name ) . '_preset',
										'options' => $presets_titles,
										'value' => $preselected_preset,
										'input_group_class' => 'epkb-setup-wizard-module-preset-selector',
									] );    ?>
								</div>  <?php
							}   ?>
						</div>

					</div>

				</div>

			</div>
		</div>	<?php

		return ob_get_clean();
	}

	/**
	 * Setup Wizard: Modular Step - Choose article page navigation on left or right sidebar
	 *
	 * @param $step_number
	 * @return false|string
	 */
	private function wizard_step_modular_navigation_content( $step_number ) {

		$groups = $this->get_sidebar_groups();

		$selected_id = $this->is_setup_run_first_time ? 1 : self::get_current_sidebar_selection( $this->kb_config );

		ob_start(); ?>

		<div id="epkb-wsb-step-<?php echo esc_attr( $step_number ); ?>-panel" class="epkb-setup-wizard-sidebar epkb-wc-step-panel eckb-wizard-step-article-page eckb-wizard-step-<?php echo esc_attr( $step_number ); ?>">
			<div class="epkb-setup-wizard-theme-preview">
				<div class="epkb-wizard-theme-tab-container">
					<div class="epkb-setup-wizard-article__container">
						<div class="epkb-setup-wizard-article-image__container">
							<div class="epkb-setup-wizard-article-image__list"><?php
								foreach ( $groups as $group ) {
									foreach ( $group['options'] as $id => $option_title ) {
										$image_id = $id ? $id : self::get_current_sidebar_selection( $this->kb_config );
										$image_url = Echo_Knowledge_Base::$plugin_url . 'img/' . self::$sidebar_images[ $image_id ]; ?>
										<div class="epkb-setup-wizard__featured-img-container <?php echo $selected_id === $image_id ? 'epkb-setup-wizard__featured-img-container--active' : ''; ?>" data-value="<?php echo esc_attr( $image_id ); ?>">
											<img alt="" class="epkb-setup-wizard__featured-img" src="<?php echo esc_url( $image_url ); ?>" title="<?php echo esc_attr( $option_title ); ?>"/>
										</div> <?php
									}
								} ?>
							</div>
						</div>
						<div class="epkb-setup-wizard-option__container">
							<div class="epkb-setup-wizard-option__title"><?php esc_html_e( 'Navigation', 'echo-knowledge-base'); ?></div> <?php
							$article_navigation = 'none';
							$article_location = 'left';
							if ( $selected_id === 1 || $selected_id === 2 ) {
								$article_navigation = 'categories_articles';
							}
							if ( $selected_id === 3 || $selected_id === 4 ) {
								$article_navigation = 'top_categories';
							}
							if ( $selected_id === 5 || $selected_id === 6 ) {
								$article_navigation = 'current_category_articles';
							}
							if ( $selected_id === 2 || $selected_id === 4 || $selected_id === 6 ) {
								$article_location = 'right';
							}
							EPKB_HTML_Elements::radio_buttons_horizontal( [
								'name' => 'article_navigation',
								'options' => [
									'categories_articles' => esc_html__( 'All Categories and Articles', 'echo-knowledge-base' ),
									'top_categories' => esc_html__( 'Top Categories', 'echo-knowledge-base' ),
									'current_category_articles' => esc_html__( 'Current Category and Articles', 'echo-knowledge-base' ),
									'none' => esc_html__( 'None', 'echo-knowledge-base' ),
								],
								'value' => $article_navigation,
								'input_group_class' => 'epkb-setup-wizard-option__navigation-selector',
								'group_data' => [ 'current-value' => $article_navigation, 'hide-none-on-layout' => EPKB_Layout::SIDEBAR_LAYOUT ],
							] ); ?>
							<div class="epkb-setup-wizard-option__title"><?php esc_html_e( 'Location', 'echo-knowledge-base'); ?></div> <?php
							EPKB_HTML_Elements::radio_buttons_horizontal( [
								'name' => 'article_location',
								'options' => [
									'left' => esc_html__( 'Left', 'echo-knowledge-base' ),
									'right' => esc_html__( 'Right', 'echo-knowledge-base' ),
								],
								'value' => $article_location,
								'input_group_class' => 'epkb-setup-wizard-option__location-selector',
							] ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>  <?php

		return ob_get_clean();
	}

	/**
	 * Return array of Presets for each Module
	 *
	 * @return array
	 */
	private function get_modules_presets_config() {

		$modules_presets_config = [
			'search' => [],
			'categories_articles' => [],
			'articles_list' => [],
			'faqs' => [],
			'resource_links' => [],
		];

		// Search Module Presets
		$modules_presets_config['search']['layout_1'] = [
			'preselected' => true,
			'presets' => [
				'preset_1' => [
					'preselected'   => true,
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-2/module-search.jpg',
					'title'         => esc_html__( 'Basic', 'echo-knowledge-base' ),
				],
			],
		];

		// Categories & Articles Module Presets: Basic
		$modules_presets_config['categories_articles']['Basic'] = [
			'preselected' => $this->kb_config['kb_main_page_layout'] == 'Basic',
			'presets' => [
				'office' => [
					'preselected'   => true,
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-office.jpg',
					'title'         => esc_html__( 'Office', 'echo-knowledge-base' ),
				],
				'organized_basic' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-organized.jpg',
					'title'         => esc_html__( 'Organized', 'echo-knowledge-base' ),
				],
				'creative' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-creative.jpg',
					'title'         => esc_html__( 'Creative', 'echo-knowledge-base' ),
				],
				'image' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-image.jpg',
					'title'         => esc_html__( 'Image', 'echo-knowledge-base' ),
				],
				'informative' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-informative.jpg',
					'title'         => esc_html__( 'Informative', 'echo-knowledge-base' ),
				],
				'formal' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-formal.jpg',
					'title'         => esc_html__( 'Formal', 'echo-knowledge-base' ),
				],
				'elegant' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-elegant.jpg',
					'title'         => esc_html__( 'Elegant', 'echo-knowledge-base' ),
				],
				'icon_focused' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-organized-no-icons.jpg',
					'title'         => esc_html__( 'No Article Icons', 'echo-knowledge-base' ),
				],
				'bright' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-bright.jpg',
					'title'         => esc_html__( 'Bright', 'echo-knowledge-base' ),
				],
				'compact' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-compact.jpg',
					'title'         => esc_html__( 'Compact', 'echo-knowledge-base' ),
				],
				'sharp' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-sharp.jpg',
					'title'         => esc_html__( 'Sharp', 'echo-knowledge-base' ),
				],
				'simple' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-simple.jpg',
					'title'         => esc_html__( 'Simple', 'echo-knowledge-base' ),
				],
				'modern' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-modern.jpg',
					'title'         => esc_html__( 'Modern', 'echo-knowledge-base' ),
				],
				'gray' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-gray.jpg',
					'title'         => esc_html__( 'Gray', 'echo-knowledge-base' ),
				],
			],
		];

		// Categories & Articles Module Presets: Tabs
		$modules_presets_config['categories_articles']['Tabs'] = [
			'preselected' => $this->kb_config['kb_main_page_layout'] == 'Tabs',
			'presets' => [

				'office_tabs' => [
					'preselected'   => true,
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-office.jpg',
					'title'         => esc_html__( 'Office', 'echo-knowledge-base' ),
				],
				'organized_tabs' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-organized.jpg',
					'title'         => esc_html__( 'Organized', 'echo-knowledge-base' ),
				],
				'modern_tabs' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-modern.jpg',
					'title'         => esc_html__( 'Modern', 'echo-knowledge-base' ),
				],
				'image_tabs' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-image.jpg',
					'title'         => esc_html__( 'Image', 'echo-knowledge-base' ),
				],
				'informative_tabs' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-informative.jpg',
					'title'         => esc_html__( 'Informative', 'echo-knowledge-base' ),
				],
				'creative_tabs' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-creative.jpg',
					'title'         => esc_html__( 'Creative', 'echo-knowledge-base' ),
				],
				'formal_tabs' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-formal.jpg',
					'title'         => esc_html__( 'Formal', 'echo-knowledge-base' ),
				],
				'compact_tabs' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-compact.jpg',
					'title'         => esc_html__( 'Compact', 'echo-knowledge-base' ),
				],
				'sharp_tabs' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-sharp.jpg',
					'title'         => esc_html__( 'Sharp', 'echo-knowledge-base' ),
				],
				'elegant_tabs' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-elegant.jpg',
					'title'         => esc_html__( 'Elegant', 'echo-knowledge-base' ),
				],
				'icon_focused_tabs' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-organized-no-icons.jpg',
					'title'         => esc_html__( 'No Article Icons', 'echo-knowledge-base' ),
				],
				'simple_tabs' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-simple.jpg',
					'title'         => esc_html__( 'Simple', 'echo-knowledge-base' ),
				],
				'clean' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-tabs-clean.jpg',
					'title'         => esc_html__( 'Clean', 'echo-knowledge-base' ),
				],
			],
		];

		// Categories & Articles Module Presets: Categories
		$modules_presets_config['categories_articles']['Categories'] = [
			'preselected' => $this->kb_config['kb_main_page_layout'] == 'Categories',
			'presets' => [
				'office_categories' => [
					'preselected'   => true,
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-category-focused-office.jpg',
					'title'         => esc_html__( 'Office', 'echo-knowledge-base' ),
				],
				'corporate' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-category-focused-corporate.jpg',
					'title'         => esc_html__( 'Corporate', 'echo-knowledge-base' ),
				],
				'creative_categories' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-category-creative.jpg',
					'title'         => esc_html__( 'Creative', 'echo-knowledge-base' ),
				],
				'business' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-category-focused-business.jpg',
					'title'         => esc_html__( 'Business', 'echo-knowledge-base' ),
				],
				'minimalistic' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-category-focused-minimalistic.jpg',
					'title'         => esc_html__( 'Minimalistic', 'echo-knowledge-base' ),
				],
				'sharp_categories' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-category-focused-sharp.jpg',
					'title'         => esc_html__( 'Sharp', 'echo-knowledge-base' ),
				],
				'icon_focused_categories' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-category-focused-no-icons.jpg',
					'title'         => esc_html__( 'No Article Icons', 'echo-knowledge-base' ),
				],
				'compact_categories' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-category-focused-compact.jpg',
					'title'         => esc_html__( 'Compact', 'echo-knowledge-base' ),
				],
				'formal_categories' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-category-formal.jpg',
					'title'         => esc_html__( 'Formal', 'echo-knowledge-base' ),
				],
				'simple_categories' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-category-focused-simple.jpg',
					'title'         => esc_html__( 'Simple', 'echo-knowledge-base' ),
				],
			],
		];

		// Categories & Articles Module Presets: Classic
		$modules_presets_config['categories_articles']['Classic'] = [
			'preselected' => $this->kb_config['kb_main_page_layout'] == 'Classic',
			'presets' => [
				'standard_classic' => [
					'preselected'   => true,
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-classic-standard.jpg',
					'title'         => esc_html__( 'Standard', 'echo-knowledge-base' ),
				],
				'sharp_classic' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-classic-sharp.jpg',
					'title'         => esc_html__( 'Sharp', 'echo-knowledge-base' ),
				],
				'organized_classic' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-classic-organized.jpg',
					'title'         => esc_html__( 'Organized', 'echo-knowledge-base' ),
				],
				'creative_classic' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-classic-creative.jpg',
					'title'         => esc_html__( 'Creative', 'echo-knowledge-base' ),
				],
				'simple_classic' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-classic-simple.jpg',
					'title'         => esc_html__( 'Simple', 'echo-knowledge-base' ),
				],
				'icon_focused_classic' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-classic-no-icons.jpg',
					'title'         => esc_html__( 'No Article Icons', 'echo-knowledge-base' ),
				],
			],
		];

		// Categories & Articles Module Presets: Drill-Down
		$modules_presets_config['categories_articles']['Drill-Down'] = [
			'preselected' => $this->kb_config['kb_main_page_layout'] == 'Drill-Down',
			'presets' => [
				'standard_drill_down' => [
					'preselected'   => true,
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-drill-down-standard.jpg',
					'title'         => esc_html__( 'Standard', 'echo-knowledge-base' ),
				],
				'sharp_drill_down' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-drill-down-sharp.jpg',
					'title'         => esc_html__( 'Sharp', 'echo-knowledge-base' ),
				],
				'organized_drill_down' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-drill-down-organized.jpg',
					'title'         => esc_html__( 'Organized', 'echo-knowledge-base' ),
				],
				'creative_drill_down' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-drill-down-creative.jpg',
					'title'         => esc_html__( 'Creative', 'echo-knowledge-base' ),
				],
				'simple_drill_down' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-drill-down-simple.jpg',
					'title'         => esc_html__( 'Simple', 'echo-knowledge-base' ),
				],
				'icon_focused_drill_down' => [
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-drill-down-no-icons.jpg',
					'title'         => esc_html__( 'No Article Icons', 'echo-knowledge-base' ),
				],
			],
		];

		// Categories & Articles Module Add-ons Presets
		if ( $this->elay_enabled ) {

			// Categories & Articles Module Presets: Grid
			$modules_presets_config['categories_articles']['Grid'] = [
				'preselected' => $this->kb_config['kb_main_page_layout'] == 'Grid',
				'presets' => [
					'grid_basic' => [
						'preselected'   => true,
						'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-grid-basic.jpg',
						'title'         => esc_html__( 'Basic', 'echo-knowledge-base' ),
					],
					'grid_demo_5' => [
						'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-grid-informative.jpg',
						'title'         => esc_html__( 'Informative', 'echo-knowledge-base' ),
					],
					'grid_demo_6' => [
						'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-grid-simple.jpg',
						'title'         => esc_html__( 'Simple', 'echo-knowledge-base' ),
					],
					'grid_demo_7' => [
						'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-grid-left-icon.jpg',
						'title'         => esc_html__( 'Left Icon Style', 'echo-knowledge-base' ),
					],
					'grid_demo_8' => [
						'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-grid-simple-2.jpg',
						'title'         => esc_html__( 'Simple 2', 'echo-knowledge-base' ),
					],
					'grid_demo_9' => [
						'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-grid-icon-squares.jpg',
						'title'         => esc_html__( 'Icon Squares', 'echo-knowledge-base' ),
					],
				],
			];

			// Categories & Articles Module Presets: Sidebar
			$modules_presets_config['categories_articles']['Sidebar'] = [
				'preselected' => $this->kb_config['kb_main_page_layout'] == 'Sidebar',
				'presets' => [
					'sidebar_basic' => [
						'preselected'   => true,
						'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-sidebar-basic.jpg',
						'title'         => esc_html__( 'Basic', 'echo-knowledge-base' ),
					],
					'sidebar_colapsed' => [
						'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-sidebar-collapsed.jpg',
						'title'         => esc_html__( 'Collapsed', 'echo-knowledge-base' ),
					],
					'sidebar_formal' => [
						'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-sidebar-formal.jpg',
						'title'         => esc_html__( 'Formal', 'echo-knowledge-base' ),
					],
					'sidebar_compact' => [
						'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-sidebar-compact.jpg',
						'title'         => esc_html__( 'Compact', 'echo-knowledge-base' ),
					],
					'sidebar_plain' => [
						'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-sidebar-plain.jpg',
						'title'         => esc_html__( 'Plain', 'echo-knowledge-base' ),
					],
				],
			];
		}

		// Articles List Module Presets
		$modules_presets_config['articles_list']['layout_1'] = [
			'preselected' => true,
			'presets' => [
				'preset_1' => [
					'preselected'   => true,
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-2/module-articles-list.jpg',
					'title'         => esc_html__( 'Basic', 'echo-knowledge-base' ),
				],
			],
		];

		// FAQs Module Presets
		$modules_presets_config['faqs']['layout_1'] = [
			'preselected' => true,
			'presets' => [
				'preset_1' => [
					'preselected'   => true,
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-2/module-faqs.jpg',
					'title'         => esc_html__( 'Basic', 'echo-knowledge-base' ),
				],
			],
		];

		// Resource Links Module Presets
		$modules_presets_config['resource_links']['layout_1'] = [
			'preselected' => true,
			'presets' => [
				'preset_1' => [
					'preselected'   => true,
					'image_url'     => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-2/module-resource-links.jpg',
					'title'         => esc_html__( 'Basic', 'echo-knowledge-base' ),
				],
			],
		];

		return $modules_presets_config;
	}

	/**
	 * Return configuration for each Module Row
	 *
	 * @return array
	 */
	private function get_modules_rows_config() {

		$modules_config = [
			'search'                => [
				'label' => esc_html__( 'Search', 'echo-knowledge-base' ),
				'toggle_value' => $this->is_setup_run_first_time ? 'search' : 'none',
				'toggle_options' => [
					'search'  => '<i class="epkbfa epkbfa-plus epkb-setup-wizard-module-row-toggle--on"></i>',
					'none'  => '<i class="epkbfa epkbfa-minus epkb-setup-wizard-module-row-toggle--off"></i>',
				],
				'tooltip_external_links' => [ [ 'link_text' => esc_html__( 'Learn More', 'echo-knowledge-base' ), 'link_url' => 'https://www.echoknowledgebase.com/documentation/search/' ] ]
			],
			'categories_articles'   => [
				'label' => esc_html__( 'Categories & Articles', 'echo-knowledge-base' ),
				'toggle_value' => $this->is_setup_run_first_time ? 'categories_articles' : 'none',
				'toggle_options' => [
					'categories_articles'  => '<i class="epkbfa epkbfa-plus epkb-setup-wizard-module-row-toggle--on"></i>',
					'none'  => '<i class="epkbfa epkbfa-minus epkb-setup-wizard-module-row-toggle--off"></i>',
				],
				'tooltip_external_links' => [ [ 'link_text' => esc_html__( 'Learn More', 'echo-knowledge-base' ), 'link_url' => 'https://www.echoknowledgebase.com/documentation/categories-and-articles/' ] ]
			],
			'articles_list'         => [
				'label' => esc_html__( 'Featured Articles', 'echo-knowledge-base' ),
				'toggle_value' => $this->is_setup_run_first_time ? 'articles_list' : 'none',
				'toggle_options' => [
					'articles_list'  => '<i class="epkbfa epkbfa-plus epkb-setup-wizard-module-row-toggle--on"></i>',
					'none'  => '<i class="epkbfa epkbfa-minus epkb-setup-wizard-module-row-toggle--off"></i>',
				],
				'tooltip_external_links' => [ [ 'link_text' => esc_html__( 'Learn More', 'echo-knowledge-base' ), 'link_url' => 'https://www.echoknowledgebase.com/documentation/articles-list/' ] ]
			],
			'faqs'                  => [
				'label' => esc_html__( 'FAQs', 'echo-knowledge-base' ),
				'toggle_value' => $this->is_setup_run_first_time ? 'faqs' : 'none',
				'toggle_options' => [
					'faqs'  => '<i class="epkbfa epkbfa-plus epkb-setup-wizard-module-row-toggle--on"></i>',
					'none'  => '<i class="epkbfa epkbfa-minus epkb-setup-wizard-module-row-toggle--off"></i>',
				],
				'tooltip_external_links' => [ [ 'link_text' => esc_html__( 'Learn More', 'echo-knowledge-base' ), 'link_url' => 'https://www.echoknowledgebase.com/documentation/faqs/' ] ]
			],
			'resource_links'        => [
				'label' => esc_html__( 'Resource Links', 'echo-knowledge-base' ),
				'toggle_value' => $this->is_setup_run_first_time && $this->elay_enabled ? 'resource_links' : 'none',
				'toggle_options' => [
					'resource_links'  => '<i class="epkbfa epkbfa-plus epkb-setup-wizard-module-row-toggle--on"></i>',
					'none'  => '<i class="epkbfa epkbfa-minus epkb-setup-wizard-module-row-toggle--off"></i>',
				],
				'tooltip_external_links' => [ [ 'link_text' => esc_html__( 'Learn More', 'echo-knowledge-base' ), 'link_url' => 'https://www.echoknowledgebase.com/documentation/resource-links/' ] ]
			],
		];

		// Do not check which modules are selected in KB Configuration for the first run of Setup Wizard
		if ( $this->is_setup_run_first_time ) {
			return $modules_config;
		}

		$selected_modules_config = [];

		// assign selected modules
		for ( $row_number = 1; $row_number <= 5; $row_number++ ) {

			$selected_module = $this->kb_config['ml_row_' . $row_number . '_module'];

			if ( empty( $modules_config[ $selected_module ] ) ) {
				continue;
			}

			if ( $selected_module == 'resource_links' && ! $this->elay_enabled ) {
				continue;
			}

			$selected_modules_config[ $selected_module ] = $modules_config[ $selected_module ];
			$selected_modules_config[ $selected_module ]['toggle_value'] = $selected_module;
			unset( $modules_config[ $selected_module ] );
		}

		// append unselected modules and return the full modules list
		return array_merge_recursive( $selected_modules_config, $modules_config );
	}

	/**
	 * Get names of the sidebar presets
	 * @return array
	 */
	private function get_sidebar_groups() {
		return [
			[
				'title' => esc_html__( 'Articles and Categories Navigation', 'echo-knowledge-base' ),
				'class' => '',
				'description' => esc_html__( 'This navigation sidebar displays a list of links to all categories and their articles. Users can navigate your KB using the links in the navigation sidebar.', 'echo-knowledge-base' ),
				'learn_more_url' => 'https://www.echoknowledgebase.com/demo-1-knowledge-base-basic-layout/administration/demo-article-1/',
				'options' => [
					1 => esc_html__( 'Left Side', 'echo-knowledge-base' ),
					2 => esc_html__( 'Right Side', 'echo-knowledge-base' )
				]
			],
			[
				'title' => esc_html__( 'Top Categories Navigation', 'echo-knowledge-base' ),
				'class' => '',
				'description' => esc_html__( 'This navigation sidebar displays only top-level categories. Each category displays a counter of articles within the category.', 'echo-knowledge-base' ),
				'learn_more_url' => 'https://www.echoknowledgebase.com/demo-14-category-layout/demo-article-2/',
				'options' => [
					3 => esc_html__( 'Left Side', 'echo-knowledge-base' ),
					4 => esc_html__( 'Right Side', 'echo-knowledge-base' )
				]
			],
			[
				'title' => esc_html__( 'Current Category and Articles', 'echo-knowledge-base' ),
				'class' => '',
				'description' => esc_html__( 'This navigation sidebar displays only the current category, its subcategories, and articles.', 'echo-knowledge-base' ),
				'learn_more_url' => 'https://www.echoknowledgebase.com/demo-14-category-layout/demo-article-2/',    // TODO: update URL
				'options' => [
					5 => esc_html__( 'Left Side', 'echo-knowledge-base' ),
					6 => esc_html__( 'Right Side', 'echo-knowledge-base' )
				]
			],
			[
				'title' => esc_html__( 'No Navigation', 'echo-knowledge-base' ),
				'class' => '',
				'description' => esc_html__( 'Articles do not show any navigation links. The table of content and KB widgets sidebar can still be displayed.', 'echo-knowledge-base' ),
				'learn_more_url' => 'https://www.echoknowledgebase.com/demo-12-knowledge-base-image-layout/demo-article-3/',
				'options' => [
					7 => esc_html__( 'No Navigation', 'echo-knowledge-base' ),
				]
			],
		];
	}
}