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
	private $is_main_page_missing;
	private $main_page_has_blocks;
	private $main_page_has_shortcode;

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

		$existing_main_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $kb_config );
		$this->is_main_page_missing = empty( $existing_main_page_id );

		$this->main_page_has_blocks = EPKB_Block_Utilities::kb_main_page_has_kb_blocks( $kb_config );
		$this->main_page_has_shortcode = ! $this->main_page_has_blocks;
	}

	/**
	 * Show KB Setup page
	 */
	public function display_kb_setup_wizard() {

		// Step: URL
		$step_number = 1;
		$setup_steps_config[] = [
			'label'     => esc_html__( 'URL', 'echo-knowledge-base' ),
			'header_args'    => array(
				'title_html'            => esc_html__( 'Setup Your Knowledge Base', 'echo-knowledge-base' ),
				'info_title'            => esc_html__( 'Set your Knowledge Base nickname, create a slug, and add it to the menu.', 'echo-knowledge-base' ),
			),
			'content_escaped'   => $this->wizard_step_title_url_content( $step_number ),
			'step_number_label'	=> 1,
		];

		// Step: Main Page - choice between using KB blocks and KB shortcode (only when blocks are available)
		if ( $this->is_main_page_missing && EPKB_Block_Utilities::is_blocks_available() ) {

			$step_number++;
			$setup_steps_config[] = [
				'label' => esc_html__( 'Main Page', 'echo-knowledge-base' ),
				'header_args' => array(
					'title_html' => esc_html__( 'Two Ways to Display Your Knowledge Base', 'echo-knowledge-base' ),
					'info_title' => '',
					'info_description' => '',
				),
				'content_escaped' => $this->wizard_step_blocks_or_shortcode( $step_number ),
				'step_number_label' => 2,
				'steps_bar_css_class' => 'epkb-setup-wizard-step-part-1-of-2',
				'header_css_class' => 'epkb-wc-step-header--mp-type',
			];
		}

		// Step: Choose KB Layout
		$step_number++;
		$setup_steps_config[] = [
			'label'     => esc_html__( 'KB Layout', 'echo-knowledge-base' ),
			'header_args'    => array(
					'title_html'        => esc_html__( 'Choose Your KB Layout', 'echo-knowledge-base' ),
					'info_title'        => esc_html__( 'Select a layout that best organizes your knowledge base content.', 'echo-knowledge-base' ),
				),
			'content_escaped'   => $this->wizard_step_modular_layout_content( $step_number ),
			'step_number_label'	=> 3,
			'header_css_class'	=> 'epkb-wc-step-header--layout',
		];

		// Step: Choose Design
		$step_number++;
		$setup_steps_config[] = [
			'label'     => esc_html__( 'KB Style and Colors', 'echo-knowledge-base' ),
			'header_args'    => array(
				'title_html'        => esc_html__( 'Choose Style and Colors for Your KB', 'echo-knowledge-base' ),
				'info_title'        => esc_html__( 'Select a design preset or keep your current style.', 'echo-knowledge-base' ),
			),
			'content_escaped'   => $this->wizard_step_designs_content( $step_number ),
			'step_number_label'	=> 4,
			'header_css_class'	=> 'epkb-wc-step-header--design',
		];

		// Step: Article Page
		$step_number++;
		$setup_steps_config[] = [
			'label'     => esc_html__( 'Article Page', 'echo-knowledge-base' ),
			'header_args'    => array(
				'title_html'        => esc_html__( 'Setup Your Article Page', 'echo-knowledge-base' ),
				'info_title'        => esc_html__( 'Article pages can have navigation links in the left sidebar or in the right sidebar.', 'echo-knowledge-base' ),
			),
			'content_escaped'   => $this->wizard_step_modular_navigation_content( $step_number ),
			'step_number_label'	=> $this->is_main_page_missing || $this->main_page_has_shortcode ? 5 : 3,
			'header_css_class'	=> 'epkb-wc-step-header--article-page',
		];  ?>

		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-wizard-container">
			<div class="<?php echo 'epkb-config-setup-wizard-modular'; echo $this->is_setup_run_first_time ? ' ' . 'epkb-config-setup-wizard-modular--first-setup' : ''; ?>" id="epkb-config-wizard-content">

			<!------- Wizard Steps Banner ------------>  <?php
				$total_steps = count( $setup_steps_config );   ?>
			<div class="epkb-setup-wizard-steps-banner" data-total-steps="<?php echo esc_attr( $total_steps ); ?>">
				<div class="epkb-setup-wizard-steps-banner__line"></div>
				<div class="epkb-setup-wizard-steps-banner__highlights">   <?php
						foreach ( $setup_steps_config as $step_index => $step_config ) {   ?>
							<div data-step="<?php echo esc_attr( $step_index + 1 ); ?>" class="epkb-setup-wizard-step-highlight epkb-setup-wizard-step-highlight--<?php echo esc_attr( $step_index + 1 ); ?><?php echo $step_index == 0 ? ' epkb-setup-wizard-step-highlight--active' : ''; ?>" data-total-steps="<?php echo esc_attr( $total_steps ); ?>">
								<div class="epkb-setup-wizard-step-highlight__number"><?php echo esc_html( $step_index + 1 ); ?></div>
								<div class="epkbfa epkbfa-check epkb-setup-wizard-step-highlight__check"></div>
							</div>  <?php
						}   ?>
					</div>
				</div>

				<div class="epkb-config-wizard-inner">

					<!------- Wizard Header ------------>
					<div class="epkb-wizard-header">    <?php
						$total_steps = count( $setup_steps_config );
						foreach ( $setup_steps_config as $step_index => $step_config ) {
							$class = ( $step_index + 1 ) . ( $step_index == 0 ? ' ' . 'epkb-wc-step-header--active' : '' );
							$header_args = isset( $step_config['header_args'] ) ? $step_config['header_args'] : array();
							$header_args['step_number'] = $step_index + 1;
							$header_args['total_steps'] = $total_steps;    ?>
							<div class="epkb-wc-step-header epkb-wc-step-header--<?php echo esc_attr( $class ); echo empty( $step_config['header_css_class'] ) ? '' : ' ' . $step_config['header_css_class']; ?>"> <?php
								//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo $this->wizard_step_header( $header_args );   ?>
							</div>  <?php
						}   ?>
					</div>

					<!------- Wizard Content ---------->
					<div class="epkb-wizard-content">   <?php

						foreach ( $setup_steps_config as $step_index => $step_config ) {
							//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $step_config['content_escaped'];
						}		  ?>

					</div>

					<input type="hidden" id="_wpnonce_epkb_ajax_action" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_epkb_ajax_action" ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
					<input type="hidden" id="epkb_wizard_kb_id" name="epkb_wizard_kb_id" value="<?php echo esc_attr( $this->kb_config['id'] ); ?>"/>

					<div class="eckb-bottom-notice-message"></div>

				</div>
			</div>

		</div>		<?php

		// Success message
		$success_body = '<div class="epkb-wizard-success-content">' .
			'<div class="epkb-wizard-success-content__message">' .
				'<span class="epkb-wizard-success-content__icon epkbfa epkbfa-check-circle"></span>' .
				'<span class="epkb-wizard-success-content__text">' . esc_html__( 'Your Knowledge Base is ready.', 'echo-knowledge-base' ) . '</span>' .
			'</div>' .
			'<a href="#" class="epkb-wizard-success-content__open-kb" target="_blank">' .
				'<span class="epkbfa epkbfa-external-link"></span> ' . esc_html__( 'Open Knowledge Base', 'echo-knowledge-base' ) .
			'</a>' .
		'</div>';
		EPKB_HTML_Forms::dialog_confirm_action( [
			'id'           => 'epkb-wizard-success-message',
			'title'        => '',
			'body'         => $success_body,
			'accept_label' => esc_html__( 'Continue', 'echo-knowledge-base' ),
			'accept_type'  => 'success'
		] );

		// PRO layout upgrade dialog - shown when user clicks Next with Grid or Sidebar layout selected
		if ( ! $this->elay_enabled ) {
			EPKB_HTML_Forms::dialog_pro_feature_ad( [
				'id'              => 'epkb-wizard-pro-layout-dialog',
				'title'           => esc_html__( 'Unlock Grid and Sidebar Layouts', 'echo-knowledge-base' ),
				'list'            => [
					esc_html__( 'Grid Layout: Visual category cards with article counts', 'echo-knowledge-base' ),
					esc_html__( 'Sidebar Layout: Always-visible navigation on all pages', 'echo-knowledge-base' ),
					esc_html__( 'Professional designs for your knowledge base', 'echo-knowledge-base' ),
				],
				'btn_text'        => esc_html__( 'Get Elegant Layouts Add-on', 'echo-knowledge-base' ),
				'btn_url'         => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/',
				'cancel_btn_text' => esc_html__( 'Later', 'echo-knowledge-base' ),
			] );
		}
	}

	/**
	 * Setup Wizard: Step 1 - Title & URL
	 *
	 * @param $step_number
	 * @return false|string
	 */
	private function wizard_step_title_url_content( $step_number ) {

		ob_start();

		$kb_id = $this->kb_config['id'];

		$kb_path = $this->kb_config['kb_articles_common_path'];
		if ( $kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID && substr( $kb_path, -strlen( '-' . $kb_id ) ) !== '-' . $kb_id ) {
			$kb_path .= '-' . $kb_id;
		}

		$kb_name = $this->kb_config['kb_name']; ?>

		<div id="epkb-wsb-step-<?php echo esc_attr( $step_number ); ?>-panel" class="epkb-wc-step-panel eckb-wizard-step-url eckb-wizard-step-<?php echo esc_attr( $step_number ); ?> epkb-wc-step-panel--active epkb-wizard-theme-step-<?php echo esc_attr( $step_number ); ?>">  <?php

			// KB Name
		    EPKB_HTML_Elements::text(
				array(
					'label'             => esc_html__('Knowledge Base Nickname', 'echo-knowledge-base'),
					'placeholder'       => esc_html__('Knowledge Base', 'echo-knowledge-base'),
					'main_tag'          => 'div',
					'input_group_class' => 'epkb-wizard-row-form-input epkb-wizard-name',
					'value'             => $kb_name
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
						'value'             => $kb_path,
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
	 *  Setup Wizard: Step - Choose KB Layout with sidebar and live preview
	 *
	 * @param $step_number
	 * @return false|string
	 */
	private function wizard_step_modular_layout_content( $step_number ) {

		$layouts_config = $this->get_layouts_config();

		// Determine current layout - for block-based KBs, detect from block
		$current_layout = $this->kb_config['kb_main_page_layout'];
		if ( $this->main_page_has_blocks ) {
			$main_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $this->kb_config );
			if ( ! empty( $main_page_id ) ) {
				$block_layout = EPKB_Block_Utilities::get_kb_block_layout( get_post( $main_page_id ), $current_layout );
				if ( ! empty( $block_layout ) ) {
					$current_layout = $block_layout;
				}
			}
		}

		// Add PRO badge to Grid and Sidebar if Elegant Layouts not enabled
		if ( ! $this->elay_enabled ) {
			$layouts_config['Grid']['is_pro'] = true;
			$layouts_config['Sidebar']['is_pro'] = true;
		}

		ob_start();  ?>

		<div id="epkb-wsb-step-<?php echo esc_attr( $step_number ); ?>-panel" class="epkb-wc-step-panel eckb-wizard-step-layout eckb-wizard-step-<?php echo esc_attr( $step_number ); ?>">

			<?php // Info banner for block-based KBs
			if ( $this->main_page_has_blocks && ! $this->is_setup_run_first_time ) { ?>
				<div class="epkb-setup-wizard-block-info-banner">
					<span class="epkbfa epkbfa-info-circle"></span>
					<?php esc_html_e( 'Your KB uses blocks. Layout changes will be applied automatically when you complete the wizard.', 'echo-knowledge-base' ); ?>
				</div>
			<?php } ?>

			<div class="epkb-setup-wizard-step-container epkb-setup-wizard-step-container--layout-preview">
				<input type="hidden" id="_wpnonce_epkb_ajax_action" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_epkb_ajax_action" ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"/>

				<!-- Layout Sidebar -->
				<div class="epkb-setup-wizard-layout-sidebar">   <?php

					foreach ( $layouts_config as $layout_name => $layout_config ) {
						$is_active = ( $layout_name === $current_layout );
						$is_pro = ! empty( $layout_config['is_pro'] );
						$pro_screenshot_url = ! empty( $layout_config['pro_screenshot'] ) ? Echo_Knowledge_Base::$plugin_url . 'img/' . $layout_config['pro_screenshot'] : '';  ?>

						<div class="epkb-setup-wizard-layout-option<?php echo $is_active ? ' epkb-setup-wizard-layout-option--active' : ''; ?><?php echo $is_pro ? ' epkb-setup-wizard-layout-option--pro' : ''; ?>" data-layout="<?php echo esc_attr( $layout_name ); ?>"<?php echo $pro_screenshot_url ? ' data-pro-screenshot="' . esc_url( $pro_screenshot_url ) . '"' : ''; ?>>

							<div class="epkb-setup-wizard-layout-option__header">
								<input type="radio" name="epkb-layout" value="<?php echo esc_attr( $layout_name ); ?>"<?php checked( $is_active ); ?> />
								<span class="epkb-setup-wizard-layout-option__title"><?php echo esc_html( $layout_config['layout_title'] ); ?></span>
								<?php if ( $is_pro ) { ?>
									<span class="epkb-setup-wizard-layout-option__pro-badge"><?php esc_html_e( 'PRO', 'echo-knowledge-base' ); ?></span>
								<?php } ?>
							</div>

							<ul class="epkb-setup-wizard-layout-option__features">   <?php
								foreach ( $layout_config['layout_features'] as $feature ) { ?>
									<li><?php echo esc_html( $feature ); ?></li>   <?php
								} ?>
							</ul>

							<?php if ( isset( $layout_config['demo_link'] ) ) { ?>
								<a href="<?php echo esc_url( $layout_config['demo_link']['url'] ); ?>" target="_blank" class="epkb-setup-wizard-layout-option__demo">
									<?php esc_html_e( 'View Demo', 'echo-knowledge-base' ); ?>
									<span class="epkbfa epkbfa-external-link"></span>
								</a>
							<?php } ?>

						</div>   <?php
					} ?>

				</div>

				<!-- Live Preview Area -->
				<div class="epkb-setup-wizard-layout-preview">
					<div class="epkb-setup-wizard-layout-preview__loading">
						<div class="epkb-setup-wizard-layout-preview__loading-spinner"></div>
						<div class="epkb-setup-wizard-layout-preview__loading-text"><?php esc_html_e( 'Loading Preview...', 'echo-knowledge-base' ); ?></div>
					</div>
					<div class="epkb-setup-wizard-layout-preview__content epkb-setup-wizard-module-preset--current"></div>
				</div>

				

			</div>

			<?php /* DEBUG INFO - hidden for now
			$categories_icons_data = EPKB_KB_Config_Category::get_category_data_option( $this->kb_config['id'] );
			$font_count = 0;
			$image_count = 0;
			foreach ( $categories_icons_data as $cat_icon ) {
				if ( ! empty( $cat_icon['type'] ) && $cat_icon['type'] === 'image' ) {
					$image_count++;
				} else {
					$font_count++;
				}
			}
			$icon_type_summary = empty( $categories_icons_data ) ? 'Font (default)' : sprintf( 'Font: %d, Image: %d', $font_count, $image_count );
			?>
			<div style="margin-top: 20px; padding: 15px; background: #f0f0f0; border: 1px solid #ccc; font-size: 12px;">
				<strong>DEBUG INFO:</strong><br>
				<strong>Saved Layout Name:</strong> <?php echo esc_html( $this->kb_config['kb_main_page_layout'] ); ?><br>
				<strong>Category Icons - Type:</strong> <?php echo esc_html( $icon_type_summary ); ?><br>
				<strong>Category Icons - Location:</strong> <?php echo esc_html( $this->kb_config['section_head_category_icon_location'] ); ?><br>
				<strong>Category Icons - Size:</strong> <?php echo esc_html( $this->kb_config['section_head_category_icon_size'] ); ?>px<br>
				<strong>KB Main Page Type:</strong> <?php echo $this->main_page_has_blocks ? 'Block' : ( $this->main_page_has_shortcode ? 'Shortcode' : 'Unknown' ); ?>
			</div>
			<?php */ ?>

		</div>   <?php

		return ob_get_clean();
	}

	/**
	 * Get layouts configuration for the layout step
	 * @return array
	 */
	private function get_layouts_config() {
		return [
			'Basic' => [
				'layout_title'    => esc_html__( 'Basic Layout', 'echo-knowledge-base' ),
				'layout_features' => [
					esc_html__( 'Two levels of categories displayed', 'echo-knowledge-base' ),
					esc_html__( 'Articles from top categories listed', 'echo-knowledge-base' ),
					esc_html__( 'Expandable article lists', 'echo-knowledge-base' ),
				],
				'demo_link' => [ 'url' => 'https://www.echoknowledgebase.com/demo-1-knowledge-base-basic-layout/' ],
			],
			'Classic' => [
				'layout_title'    => esc_html__( 'Classic Layout', 'echo-knowledge-base' ),
				'layout_features' => [
					esc_html__( 'Compact view of top categories', 'echo-knowledge-base' ),
					esc_html__( 'Click to expand articles', 'echo-knowledge-base' ),
					esc_html__( 'Space-efficient design', 'echo-knowledge-base' ),
				],
				'demo_link' => [ 'url' => 'https://www.echoknowledgebase.com/demo-12-knowledge-base-image-layout/' ],
			],
			'Drill-Down' => [
				'layout_title'    => esc_html__( 'Drill Down Layout', 'echo-knowledge-base' ),
				'layout_features' => [
					esc_html__( 'Progressive category reveal', 'echo-knowledge-base' ),
					esc_html__( 'Great for large KBs', 'echo-knowledge-base' ),
					esc_html__( 'Interactive navigation', 'echo-knowledge-base' ),
				],
				'demo_link' => [ 'url' => 'https://www.echoknowledgebase.com/demo-4-knowledge-base-tabs-layout/' ],
			],
			'Tabs' => [
				'layout_title'    => esc_html__( 'Tabs Layout', 'echo-knowledge-base' ),
				'layout_features' => [
					esc_html__( 'Top categories as tabs', 'echo-knowledge-base' ),
					esc_html__( 'Subject-specific browsing', 'echo-knowledge-base' ),
					esc_html__( 'Clear organization', 'echo-knowledge-base' ),
				],
				'demo_link' => [ 'url' => 'https://www.echoknowledgebase.com/demo-3-knowledge-base-tabs-layout/' ],
			],
			'Categories' => [
				'layout_title'    => esc_html__( 'Category Focused Layout', 'echo-knowledge-base' ),
				'layout_features' => [
					esc_html__( 'Article count per category', 'echo-knowledge-base' ),
					esc_html__( 'Links to category archives', 'echo-knowledge-base' ),
					esc_html__( 'Two levels of categories', 'echo-knowledge-base' ),
				],
				'demo_link' => [ 'url' => 'https://www.echoknowledgebase.com/demo-14-category-layout/' ],
			],
			'Grid' => [
				'layout_title'    => esc_html__( 'Grid Layout', 'echo-knowledge-base' ),
				'layout_features' => [
					esc_html__( 'Visual category cards', 'echo-knowledge-base' ),
					esc_html__( 'Article count display', 'echo-knowledge-base' ),
					esc_html__( 'Modern grid design', 'echo-knowledge-base' ),
				],
				'demo_link' => [ 'url' => 'https://www.echoknowledgebase.com/demo-5-knowledge-base-grid-layout/' ],
				'pro_screenshot' => 'setup-wizard/step-2/Grid-Layout-Standard-no-search.jpg',
			],
			'Sidebar' => [
				'layout_title'    => esc_html__( 'Sidebar Layout', 'echo-knowledge-base' ),
				'layout_features' => [
					esc_html__( 'Always-visible navigation', 'echo-knowledge-base' ),
					esc_html__( 'Sidebar on all pages', 'echo-knowledge-base' ),
					esc_html__( 'Introductory text support', 'echo-knowledge-base' ),
				],
				'demo_link' => [ 'url' => 'https://www.echoknowledgebase.com/demo-7-knowledge-base-sidebar-layout/' ],
				'pro_screenshot' => 'setup-wizard/step-2/Sidebar-Layout-Standard-no-search.jpg',
			],
		];
	}

	/**
	 * Return HTML for Step Header based on args
	 *
	 * @param $args
	 * @return false|string
	 */
	private static function wizard_step_header( $args ) {
		
		$step_number = isset( $args['step_number'] ) ? $args['step_number'] : 1;
		$total_steps = isset( $args['total_steps'] ) ? $args['total_steps'] : 1;
		$title_html = isset( $args['title_html'] ) ? $args['title_html'] : '';
		
		ob_start();     ?>
		<div class="epkb-wizard-header__info">
			<div class="epkb-wizard-header__info-wrapper">
				<h1 class="epkb-wizard-header__info__title"><?php echo wp_kses( $title_html, EPKB_Utilities::get_admin_ui_extended_html_tags() ); ?></h1>
				
				<div class="epkb-wizard-top-navigation">  <?php
					
					// Previous Button (for all steps except first)
					if ( $step_number > 1 ) { ?>
						<button value="<?php echo esc_attr( $step_number - 1 ); ?>" class="epkb-wizard-button epkb-setup-wizard-button-prev epkb-wizard-top-nav-btn epkb-wizard-top-nav-btn--prev">
							<span class="epkb-wizard-button-next__icon epkbfa epkbfa-caret-left"></span>
							<span class="epkb-setup-wizard-button-prev__text"><?php esc_html_e( 'Previous Step', 'echo-knowledge-base' ); ?></span>
						</button>  <?php
					}
					
					// Next/Apply Button (for all steps)
					if ( $step_number < $total_steps ) { ?>
						<button value="<?php echo esc_attr( $step_number + 1 ); ?>" class="epkb-wizard-button epkb-setup-wizard-button-next epkb-wizard-top-nav-btn epkb-wizard-top-nav-btn--next">
							<span class="epkb-setup-wizard-button-next__text"><?php esc_html_e( 'Next Step', 'echo-knowledge-base' ); ?></span>
							<span class="epkb-wizard-button-next__icon epkbfa epkbfa-caret-right"></span>
						</button>  <?php
					} else { ?>
						<button value="apply" class="epkb-wizard-button epkb-setup-wizard-button-apply epkb-wizard-top-nav-btn epkb-wizard-top-nav-btn--next" data-wizard-type="setup"><?php esc_html_e( 'Finish Set Up', 'echo-knowledge-base' ); ?></button>  <?php
					}   ?>
				</div>
			</div>
		</div>
		<div class="epkb-setup-wizard-theme-header">
			
				 <?php
			if ( isset( $args['info_description'] ) ) { ?>
				<h2 class="epkb-setup-wizard-theme-header__info__description">	<?php
					if ( isset ( $args[ 'info_description_icon'] ) ) { ?>
						<span class="epkb-setup-wizard-theme-header__info__description__icon epkbfa epkbfa-<?php echo esc_attr( $args['info_description_icon'] ); ?>"></span>
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

		// Offer Use Blocks option as first preselected option if not KB #1 and KB #1 is using blocks otherwise offer Use Shortcode option as first preselected option
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$kb_id = $this->kb_config['id'];
		$main_page_has_blocks = EPKB_Block_Utilities::kb_main_page_has_kb_blocks( $kb_config );

		// Check if a page builder is installed
		$has_page_builder = EPKB_Site_Builders::has_page_builder_enabled();
		$builder_name = $this->get_detected_builder_name();

	ob_start();	?>
<div id="epkb-wsb-step-<?php echo esc_attr( $step_number ); ?>-panel" class="epkb-wc-step-panel eckb-wizard-step-<?php echo esc_attr( $step_number ); ?>">
		<div class="epkb-setup-wizard-features-choices-list">

			<?php

		// Preselect shortcode option if page builder is detected
		if ( $has_page_builder ) {
			$this->get_shortcode_option( true, 1, $builder_name, true );
			$this->get_blocks_option( false, 2, false );
		} elseif ( $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID || $main_page_has_blocks ) {
			$this->get_blocks_option( true, 1, true );
			$this->get_shortcode_option( false, 2, $builder_name, false );
		} else {
			$this->get_shortcode_option( true, 1, $builder_name, false );
			$this->get_blocks_option( false, 2, false );
		} ?>

			</div>
		</div>	<?php

		return ob_get_clean();
	}

	private function get_blocks_option( $is_preselected, $option_number = 1, $is_recommended = false ) {	?>
		<!-- Use Blocks -->
		<div class="epkb-setup-wizard-features-choice <?php echo $is_preselected ? 'epkb-setup-wizard-features-choice--active' : ''; ?>">
				<?php // translators: %d is the option number ?>
				<div class="epkb-setup-wizard-features-choice__header"><span><?php echo esc_html( sprintf( __( 'Option %d - Use WordPress Blocks', 'echo-knowledge-base' ), $option_number ) ); ?></span><?php if ( $is_recommended ) { ?><span class="epkb-setup-wizard-features-choice__header-label"><?php esc_html_e( 'Recommended', 'echo-knowledge-base' ); ?></span><?php } ?></div>
				<div class="epkb-setup-wizard-features-choice__body">
					<img alt="Blocks" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-2/blocks-choice.jpg' ); ?>">
				</div>
				<p class="epkb-setup-wizard-features-choice__footer"><?php echo esc_html__( 'Choose this option if you use WordPress blocks for your pages.', 'echo-knowledge-base' ); ?></p>
				<p><a class="epkb-setup-wizard-features-choice__learn-more" href="https://www.echoknowledgebase.com/documentation/kb-blocks/#Introduction-to-WordPress-Blocks" target="_blank"><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?></a></p>
				<label class="epkb-setup-wizard-features-choice__option__label">
					<input type="radio" name="epkb-main-page-type" value="kb-blocks"<?php checked( $is_preselected ); ?>>
					<span><?php esc_html_e( 'Select Blocks', 'echo-knowledge-base' ); ?></span>
				</label>
		</div>	<?php

	}
	
	private function get_shortcode_option( $is_preselected, $option_number = 1, $builder_name = '', $is_recommended = false ) {
		// Generate description based on whether a specific builder is detected
		if ( ! empty( $builder_name ) ) {
			// translators: %s is the page builder name (e.g., "Elementor")
			$description = sprintf( esc_html__( 'Because %s is installed, the KB Shortcode option is recommended.', 'echo-knowledge-base' ), '<strong>' . $builder_name . '</strong>' );
		} else {
			$description = esc_html__( 'Choose this option if you use page builders such as Elementor or Divi.', 'echo-knowledge-base' );
		}
		?>
		<!-- Use Shortcode -->
		<div class="epkb-setup-wizard-features-choice <?php echo $is_preselected ? 'epkb-setup-wizard-features-choice--active' : ''; ?>">
			<?php // translators: %d is the option number ?>
			<div class="epkb-setup-wizard-features-choice__header"><span><?php echo esc_html( sprintf( __( 'Option %d - Use Shortcodes', 'echo-knowledge-base' ), $option_number ) ); ?></span><?php if ( $is_recommended ) { ?><span class="epkb-setup-wizard-features-choice__header-label"><?php esc_html_e( 'Recommended', 'echo-knowledge-base' ); ?></span><?php } ?></div>
			<div class="epkb-setup-wizard-features-choice__body">
				<img alt="Shortcode" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-2/shortcode-choice.jpg' ); ?>">
			</div>
			<p class="epkb-setup-wizard-features-choice__footer"><?php echo wp_kses_post( $description ); ?></p>
			<p><a class="epkb-setup-wizard-features-choice__learn-more" href="https://www.echoknowledgebase.com/documentation/knowledge-base-shortcode/#crel-597" target="_blank"><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?></a></p>
			<label class="epkb-setup-wizard-features-choice__option__label">
				<input type="radio" name="epkb-main-page-type" value="kb-shortcode"<?php checked( $is_preselected ); ?>>
				<span><?php esc_html_e( 'Select Shortcode', 'echo-knowledge-base' ); ?></span>
			</label>
		</div>	<?php
	}

	/**
	 * Get the name of the detected page builder
	 *
	 * @return string
	 */
	private function get_detected_builder_name() {
		if ( EPKB_Site_Builders::is_elementor_enabled() ) {
			return esc_html__( 'Elementor', 'echo-knowledge-base' );
		}
		if ( EPKB_Site_Builders::is_divi_enabled() ) {
			return esc_html__( 'Divi', 'echo-knowledge-base' );
		}
		if ( EPKB_Site_Builders::is_wpb_enabled() ) {
			return esc_html__( 'WPBakery Page Builder', 'echo-knowledge-base' );
		}
		if ( EPKB_Site_Builders::is_vc_enabled() ) {
			return esc_html__( 'Visual Composer', 'echo-knowledge-base' );
		}
		if ( EPKB_Site_Builders::is_beaver_enabled() ) {
			return esc_html__( 'Beaver Builder', 'echo-knowledge-base' );
		}
		if ( EPKB_Site_Builders::is_so_enabled() ) {
			return esc_html__( 'SiteOrigin Builder', 'echo-knowledge-base' );
		}
		return '';
	}

	/**
	 * Setup Wizard: Modular Step - Choose Presets for selected Modules
	 *
	 * @param $step_number
	 * @return false|string
	 */
	private function wizard_step_designs_content( $step_number ) {

		$modules_presets_config = self::get_modules_presets_config( $this->kb_config['kb_main_page_layout'] );
		$show_keep_current = ! $this->is_setup_run_first_time;

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

									// Keep Current Style preview for returning users - displayed first in preview area
									if ( $show_keep_current ) { ?>
										<div class="epkb-setup-wizard-module-preset epkb-setup-wizard-module-preset--active epkb-setup-wizard-module-preset--current epkb-setup-wizard-module-preset--loaded" data-layout="<?php echo esc_attr( $layout_name ); ?>" data-preset="current">
											<div class="epkb-setup-wizard-module-preset__preview">
												<div class="epkb-setup-wizard-module-preset__keep-current">
													<span class="epkbfa epkbfa-check-circle"></span>
													<span class="epkb-setup-wizard-module-preset__keep-current-text"><?php esc_html_e( 'Keep Current Style', 'echo-knowledge-base' ); ?></span>
													<span class="epkb-setup-wizard-module-preset__keep-current-desc"><?php esc_html_e( 'No changes will be made to your current design', 'echo-knowledge-base' ); ?></span>
												</div>
											</div>
										</div>  <?php
									}

									foreach ( $layout_config['presets'] as $preset_name => $preset_config ) {
										// For returning users, no preset is pre-selected (current is selected instead)
										$is_preset_active = $show_keep_current ? false : ! empty( $preset_config['preselected'] );  ?>
										<!-- Preset -->
										<div class="epkb-setup-wizard-module-preset<?php echo $is_preset_active ? ' epkb-setup-wizard-module-preset--active' : ''; ?> epkb-setup-wizard-module-preset--<?php echo esc_attr( $preset_name ); ?>" data-layout="<?php echo esc_attr( $layout_name ); ?>" data-preset="<?php echo esc_attr( $preset_name ); ?>">
											<div class="epkb-setup-wizard-module-preset__preview">
												<div class="epkb-setup-wizard-module-preset__preview-loading">
													<div class="epkb-setup-wizard-module-preset__preview-loading__spinner"></div>
													<div class="epkb-setup-wizard-module-preset__preview-loading__text"><?php esc_html_e( 'Loading Preview...', 'echo-knowledge-base' ); ?></div>
												</div>
											</div>
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
								$preselected_preset = $show_keep_current ? 'current' : '';

								// Build preset options (without "Keep Current" - it's separate)
								$presets_titles = [];
								foreach ( $layout_config['presets'] as $preset_name => $preset_config ) {
									$presets_titles[$preset_name] = $preset_config['title'];
									if ( ! $show_keep_current && isset( $preset_config['preselected'] ) ) {
										$preselected_preset = $preset_name;
									}
								}   ?>
								<!-- Settings Row -->
								<div class="epkb-setup-wizard-module-settings-row<?php echo empty( $layout_config['preselected'] ) ? '' : ' ' . 'epkb-setup-wizard-module-settings-row--active'; ?> epkb-setup-wizard-module-settings-row--layout epkb-setup-wizard-module-settings-row--<?php echo esc_attr( $layout_name ); ?>">    <?php

									// Keep Current Style - separate large button for returning users
									if ( $show_keep_current ) {
										$input_name = 'categories_articles_' . strtolower( $layout_name ) . '_preset';  ?>
										<div class="epkb-setup-wizard-keep-current-button-container">
											<label class="epkb-setup-wizard-keep-current-button<?php echo $preselected_preset === 'current' ? ' epkb-setup-wizard-keep-current-button--active' : ''; ?>">
												<input type="radio" name="<?php echo esc_attr( $input_name ); ?>" value="current" <?php checked( $preselected_preset, 'current' ); ?>>
												<span class="epkbfa epkbfa-check-circle"></span>
												<span class="epkb-setup-wizard-keep-current-button__text"><?php esc_html_e( 'Keep Current Style', 'echo-knowledge-base' ); ?></span>
											</label>
										</div>

										<div class="epkb-setup-wizard-presets-label"><?php esc_html_e( 'Or choose a new preset:', 'echo-knowledge-base' ); ?></div>  <?php
									}

									EPKB_HTML_Elements::radio_buttons_horizontal( [
										'name' => 'categories_articles_' . strtolower( $layout_name ) . '_preset',
										'options' => $presets_titles,
										'value' => $show_keep_current ? '' : $preselected_preset,
										'input_group_class' => 'epkb-setup-wizard-module-preset-selector',
									] );    ?>
								</div>  <?php
							}   ?>
						</div>

						<?php
						$colors_edit_url = self::get_kb_main_page_edit_url( $this->kb_config );
						if ( empty( $colors_edit_url ) ) {
							$colors_note_text = esc_html__( 'You can change colors later in the Frontend Editor or the Main Page block.', 'echo-knowledge-base' );
						} else {
							$colors_link_label = EPKB_Block_Utilities::kb_main_page_has_kb_blocks( $this->kb_config )
								? esc_html__( 'Main Page block', 'echo-knowledge-base' )
								: esc_html__( 'Frontend Editor', 'echo-knowledge-base' );
							$colors_note_text = sprintf(
								/* translators: %s: link to the Frontend Editor or the Main Page block */
								__( 'You can change colors later in the %s.', 'echo-knowledge-base' ),
								'<a href="' . esc_url( $colors_edit_url ) . '" target="_blank" rel="noopener">' . $colors_link_label . '</a>'
							);
						}   ?>
						<div class="epkb-setup-wizard-preset-colors-note">
							<span class="epkbfa epkbfa-info-circle"></span>
							<span><?php echo wp_kses( $colors_note_text, [ 'a' => [ 'href' => [], 'target' => [], 'rel' => [] ] ] ); ?></span>
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
	 * Return the URL where the user can edit the KB Main Page's design/colors.
	 * Block-based Main Page -> block editor; shortcode-based Main Page -> Frontend Editor.
	 * Empty string when no Main Page exists yet (first-time setup).
	 */
	private static function get_kb_main_page_edit_url( $kb_config ) {

		if ( ! is_array( $kb_config ) || empty( $kb_config['kb_main_pages'] ) || ! is_array( $kb_config['kb_main_pages'] ) ) {
			return '';
		}

		$main_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $kb_config );
		if ( empty( $main_page_id ) ) {
			return '';
		}

		if ( EPKB_Block_Utilities::kb_main_page_has_kb_blocks( $kb_config ) ) {
			$edit_url = get_edit_post_link( $main_page_id, 'raw' );
			return empty( $edit_url ) ? '' : $edit_url;
		}

		$main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
		if ( empty( $main_page_url ) || empty( $kb_config['id'] ) ) {
			return '';
		}

		return add_query_arg( [
			'action' => 'epkb_load_editor',
			'epkb_kb_id' => $kb_config['id'],
		], $main_page_url );
	}

	/**
	 * Return array of Presets for each Module
	 *
	 * @return array
	 */
	public static function get_modules_presets_config( $main_page_layout ) {

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

		$basic_categories_articles_presets = [
			'horizon' => [
				'preselected'   => true,
				'title'         => esc_html__( 'Horizon', 'echo-knowledge-base' ),
			],
			'bloom' => [
				'title'         => esc_html__( 'Bloom', 'echo-knowledge-base' ),
			],
			'ember' => [
				'title'         => esc_html__( 'Ember', 'echo-knowledge-base' ),
			],
			'organized' => [
				'title'         => esc_html__( 'Organized', 'echo-knowledge-base' ),
			],
			'airy' => [
				'title'         => esc_html__( 'Airy', 'echo-knowledge-base' ),
			],
			'office' => [
				'title'         => esc_html__( 'Office', 'echo-knowledge-base' ),
			],
			'creative' => [
				'title'         => esc_html__( 'Creative', 'echo-knowledge-base' ),
			],
			'image' => [
				'title'         => esc_html__( 'Image', 'echo-knowledge-base' ),
			],
			'informative' => [
				'title'         => esc_html__( 'Informative', 'echo-knowledge-base' ),
			],
			'canvas' => [
				'title'         => esc_html__( 'One Column', 'echo-knowledge-base' ),
			],
			'formal' => [
				'title'         => esc_html__( 'Formal', 'echo-knowledge-base' ),
			],
			'elegant' => [
				'title'         => esc_html__( 'Elegant', 'echo-knowledge-base' ),
			],
			'icon_focused' => [
				'title'         => esc_html__( 'No Article Icons', 'echo-knowledge-base' ),
			],
			'bright' => [
				'title'         => esc_html__( 'Bright', 'echo-knowledge-base' ),
			],
			'compact' => [
				'title'         => esc_html__( 'Compact', 'echo-knowledge-base' ),
			],
			'sharp' => [
				'title'         => esc_html__( 'Sharp', 'echo-knowledge-base' ),
			],
			'simple' => [
				'title'         => esc_html__( 'Simple', 'echo-knowledge-base' ),
			],
			'modern' => [
				'title'         => esc_html__( 'Modern', 'echo-knowledge-base' ),
			],
			'teal' => [
				'title'         => esc_html__( 'Teal', 'echo-knowledge-base' ),
			],
		];

		// Categories & Articles Module Presets: Basic
		$modules_presets_config['categories_articles']['Basic'] = [
			'preselected' => $main_page_layout == 'Basic',
			'presets' => $basic_categories_articles_presets,
		];

		$tabs_categories_articles_presets = [];
		foreach ( $basic_categories_articles_presets as $preset_name => $preset_config ) {
			$tabs_categories_articles_presets[ $preset_name . '_tabs' ] = [
				'title' => $preset_config['title'],
			];
			if ( ! empty( $preset_config['preselected'] ) ) {
				$tabs_categories_articles_presets[ $preset_name . '_tabs' ]['preselected'] = true;
			}
		}

		$drill_down_categories_articles_presets = [];
		foreach ( $basic_categories_articles_presets as $preset_name => $preset_config ) {
			$drill_down_categories_articles_presets[ $preset_name . '_drill_down' ] = [
				'title' => $preset_config['title'],
			];
			if ( ! empty( $preset_config['preselected'] ) ) {
				$drill_down_categories_articles_presets[ $preset_name . '_drill_down' ]['preselected'] = true;
			}
		}

		$categories_categories_articles_presets = [];
		foreach ( $basic_categories_articles_presets as $preset_name => $preset_config ) {
			$categories_categories_articles_presets[ $preset_name . '_categories' ] = [
				'title' => $preset_config['title'],
			];
			if ( ! empty( $preset_config['preselected'] ) ) {
				$categories_categories_articles_presets[ $preset_name . '_categories' ]['preselected'] = true;
			}
		}

		$classic_categories_articles_presets = [];
		foreach ( $basic_categories_articles_presets as $preset_name => $preset_config ) {
			$classic_categories_articles_presets[ $preset_name . '_classic' ] = [
				'title' => $preset_config['title'],
			];
			if ( ! empty( $preset_config['preselected'] ) ) {
				$classic_categories_articles_presets[ $preset_name . '_classic' ]['preselected'] = true;
			}
		}

		// Categories & Articles Module Presets: Tabs
		$modules_presets_config['categories_articles']['Tabs'] = [
			'preselected' => $main_page_layout == 'Tabs',
			'presets' => $tabs_categories_articles_presets,
		];

		// Categories & Articles Module Presets: Categories
		$modules_presets_config['categories_articles']['Categories'] = [
			'preselected' => $main_page_layout == 'Categories',
			'presets' => $categories_categories_articles_presets,
		];

		// Categories & Articles Module Presets: Classic
		$modules_presets_config['categories_articles']['Classic'] = [
			'preselected' => $main_page_layout == 'Classic',
			'presets' => $classic_categories_articles_presets,
		];

		// Categories & Articles Module Presets: Drill-Down
		$modules_presets_config['categories_articles']['Drill-Down'] = [
			'preselected' => $main_page_layout == 'Drill-Down',
			'presets' => $drill_down_categories_articles_presets,
		];

		// Categories & Articles Module Add-ons Presets
		if ( EPKB_Utilities::is_elegant_layouts_enabled() ) {

			// Categories & Articles Module Presets: Grid
			$modules_presets_config['categories_articles']['Grid'] = [
				'preselected' => $main_page_layout == 'Grid',
				'presets' => [
					'grid_basic' => [
						'preselected'   => true,
						'title'         => esc_html__( 'Basic', 'echo-knowledge-base' ),
					],
					'grid_demo_5' => [
						'title'         => esc_html__( 'Informative', 'echo-knowledge-base' ),
					],
					'grid_demo_6' => [
						'title'         => esc_html__( 'Simple', 'echo-knowledge-base' ),
					],
					'grid_demo_8' => [
						'title'         => esc_html__( 'Simple 2', 'echo-knowledge-base' ),
					],
					'grid_demo_7' => [
						'title'         => esc_html__( 'Left Icon Style', 'echo-knowledge-base' ),
					],
					'grid_demo_9' => [
						'title'         => esc_html__( 'Icon Squares', 'echo-knowledge-base' ),
					],
					'grid_nebula' => [
						'title'         => esc_html__( 'Nebula', 'echo-knowledge-base' ),
					],
					'grid_oasis' => [
						'title'         => esc_html__( 'Oasis', 'echo-knowledge-base' ),
					],
				],
			];

			// Categories & Articles Module Presets: Sidebar
			$modules_presets_config['categories_articles']['Sidebar'] = [
				'preselected' => $main_page_layout == 'Sidebar',
				'presets' => [
					'sidebar_basic' => [
						'preselected'   => true,
						'title'         => esc_html__( 'Basic', 'echo-knowledge-base' ),
					],
					'sidebar_colapsed' => [
						'title'         => esc_html__( 'Collapsed', 'echo-knowledge-base' ),
					],
					'sidebar_formal' => [
						'title'         => esc_html__( 'Formal', 'echo-knowledge-base' ),
					],
					'sidebar_compact' => [
						'title'         => esc_html__( 'Compact', 'echo-knowledge-base' ),
					],
					'sidebar_plain' => [
						'title'         => esc_html__( 'Plain', 'echo-knowledge-base' ),
					],
				],
			];
		}

		// Featured Articles Module Presets
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
				'learn_more_url' => 'https://www.echoknowledgebase.com/demo-14-category-layout/demo-article-2/',
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
