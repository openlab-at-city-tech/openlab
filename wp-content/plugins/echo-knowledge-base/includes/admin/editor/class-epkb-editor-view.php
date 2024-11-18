<?php

/**
 * Output panels for the front-end editor for KB page configuration as a template
 */
class EPKB_Editor_View {
	
	public function __construct() {

		// Show frontend editor if error on backend and user tries frontend
		// we can't check nonce on this moment, so use value but don't save it in db
		if ( EPKB_Utilities::get( 'enable_editor_frontend_mode' ) ) {
			$editor_backend_mode = false;
			// update after we can check the nonce
			add_action( 'plugins_loaded', [ $this, 'enable_frontend_editor' ] );
		} else {
			$editor_backend_mode = EPKB_Core_Utilities::is_kb_flag_set( 'editor_backend_mode' );
		}

		// for testing header("X-Frame-Options: DENY");
		$is_csp_option_on = EPKB_Utilities::get_wp_option( 'epkb_editor_csp', 0 );
		$use_csp_header = ! empty( $_GET['epkb_csp_on'] );
		
		if ( $use_csp_header && ! $is_csp_option_on ) {
			EPKB_Utilities::save_wp_option( 'epkb_editor_csp', 1 );
		}
		
		// if user will move the site or change hosting settings then have option switch off CSP
		if ( $use_csp_header && $_GET['epkb_csp_on'] == 'stop' ) {
			EPKB_Utilities::save_wp_option( 'epkb_editor_csp', 0 );
			unset( $_GET['epkb_csp_on'] );
			$use_csp_header = false;
			$is_csp_option_on = false;
		}

		if ( $editor_backend_mode || $use_csp_header || $is_csp_option_on ) {
			header( "X-Frame-Options: SAMEORIGIN" );
			header( "Content-Security-Policy: frame-ancestors 'self'" );

			add_action( 'send_headers', function () {
				header( "X-Frame-Options: SAMEORIGIN" );
				header( "Content-Security-Policy: frame-ancestors 'self'" );
			}, 999 );
		}

		// true if we are loading visual Editor portion of the page otherwise load the KB Main page BACKEND MODE
		if ( ! empty( $_REQUEST['epkb-editor-backend-mode'] ) ) {
			define( 'WP_ADMIN', true );
			add_action( 'template_include', [ $this, 'backend_mode_template' ] );
			return;
		}

		// true if we are loading visual Editor portion of the page otherwise load the KB Main page
		if ( ! empty( $_REQUEST['epkb-editor-page-loaded'] ) ) {
			add_action( 'wp_enqueue_scripts', 'epkb_load_front_end_editor', 999999 );
			return;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			require_once ABSPATH . '/wp-admin/includes/screen.php';
		}

		add_action( 'template_include', [ $this, 'init' ] );
	}
	
	public function init( $template ) {

		// do not load FE Editor for blocks
		if ( EPKB_Block_Utilities::current_post_has_kb_layout_blocks() ) {
			return $template;
		}
		
		if ( ! function_exists('wp_get_current_user')) {
			include(ABSPATH . "wp-includes/pluggable.php");
		}
		
		$page_type = EPKB_Editor_Utilities::epkb_front_end_editor_type();
		if ( ! in_array( $page_type, EPKB_Editor_Config_Base::EDITOR_PAGE_TYPES ) ) {
			epkb_load_admin_plugin_pages_resources();
			add_action( 'wp_footer', [ $this, 'error_can_not_load' ] );
			return $template;
		}

		if ( EPKB_Utilities::get_current_user() == null ) {
			epkb_load_admin_plugin_pages_resources();
			add_action( 'wp_footer', [ $this, 'error_user_not_logged_in' ] );
			return $template;
		}

		// get KB ID except on Category Archive Page without any article - we need KB ID here to have the Access Control working
		global $eckb_kb_id;

		if ( empty( $eckb_kb_id ) ) {
			$kb_id = EPKB_KB_Handler::get_kb_id_from_kb_main_page();
			$kb_id = empty( $kb_id ) ? EPKB_Core_Utilities::get_kb_id() : $kb_id;
			$eckb_kb_id = empty( $kb_id ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $kb_id;
		}

		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {
			epkb_load_admin_plugin_pages_resources();
			add_action( 'wp_footer', [ $this, 'error_no_permissions' ] );
			return $template;
		}

		add_filter( 'show_admin_bar', '__return_false' );

		// Remove all WordPress actions
		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_print_scripts' );
		remove_all_actions( 'wp_footer' );
		remove_all_actions( 'script_loader_tag' );

		// Handle `wp_enqueue_scripts`
		remove_all_actions( 'wp_enqueue_scripts' );

		add_action( 'epkb_editor_enqueue_scripts', 'epkb_load_editor_styles' );

		// Send MIME Type header like WP admin-header.
		@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
		
		self::frontend_editor_page();

		// do not load the actual page
		die();
	}
	 
	/**
	 * Various HTML pieces for the Editor
	 * - TinyMCE settings
	 * - error handling messages
	 */
	public static function get_editor_html() {        ?>

		<div class="epkb-editor-popup" style="display: none;">
			<div class="epkb-editor-popup__header"></div>
			<div class="epkb-editor-popup__body">
				<div class="epkb-editor-popup__body_desc"><strong><?php esc_html_e( 'Use your favorite WYSIWYG HTML Editor to compose the introduction content and then paste the content in this box.', 'echo-knowledge-base' ) ?></strong></div>
				<br/>
				<textarea class="epkb-editor-area" rows="20" autocomplete="off" cols="40" name="epkbeditormce" id="epkbeditormce" ></textarea>
			</div>
			<div class="epkb-editor-popup__footer">
				<button id="epkb-editor-popup__button-update"><?php esc_html_e( 'Update', 'echo-knowledge-base' ); ?></button>
				<button id="epkb-editor-popup__button-cancel"><?php esc_html_e( 'Cancel', 'echo-knowledge-base' ); ?></button>
			</div>
		</div>

		<div class="epkb-frontend-loader" style="display: none;">
			<div class="epkb-frontend-loader-icon epkbfa epkbfa-hourglass-half"></div>
		</div>

		<div class="epkb-editor-error-message" id="epkb-editor-error-message-timeout-1" style="display:none!important;">
			<div class="eckb-bottom-notice-message eckb-bottom-notice-message--center-loader-bottom">
				<div class="contents">
					<span class="error">
						<h4><?php echo esc_html( EPKB_Error_Handler::timeout1_error() ); ?></h4>
					</span>
				</div>
				<div class="epkb-close-notice epkbfa epkbfa-window-close"></div>
			</div>
		</div>

		<div class="epkb-editor-error-message" id="epkb-editor-error-message-timeout-2" style="display:none!important;">
			<div class="eckb-bottom-notice-message eckb-bottom-notice-message--center-aligned">
				<div class="contents">
					<span class="error white-box">
						<h4><?php esc_html_e( 'The visual Editor is not loading.', 'echo-knowledge-base' ); ?></h4>
						<?php self::get_error_form_html(); ?>
					</span>
					<div class="epkb-close-notice epkbfa epkbfa-window-close"></div>
				</div>
			</div>
		</div>

		<div id="epkb-editor-error-no-js-message" style="display: none;">
			<div class="eckb-bottom-notice-message eckb-bottom-notice-message--center-aligned">
				<div class="contents">
					<span class="error white-box"> <?php
						EPKB_Error_Handler::js_not_loaded();	?>
					</span>
				</div>
			</div>
		</div>

		<div id="epkb-editor-error-no-config-message" style="display: none;">
			<div class="eckb-bottom-notice-message eckb-bottom-notice-message--center-aligned">
				<div class="contents">
					<span class="error white-box">
						<h4><?php echo EPKB_Utilities::report_generic_error( 1105 ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?></h4>
					</span>
				</div>
			</div>
		</div><?php

		EPKB_HTML_Forms::dialog_confirm_action( array(
			'id'                => 'epkb-editor-layout-warning',
			'title'             => esc_html__( 'Changing Template', 'echo-knowledge-base' ),
			'body'              => esc_html__( 'If you have issues using the Current Theme Template, switch back to the KB Template or contact us for help.', 'echo-knowledge-base' ),
			'accept_label'      => esc_html__( 'Ok', 'echo-knowledge-base' ),
			'accept_type'       => 'warning',
			'show_cancel_btn' 	=> 'yes',
			'hidden'			=> true
		) );
	}

	/***
	 * Error submit Form for editor
	 */
	public static function get_error_form_html() {

		$user = EPKB_Utilities::get_current_user();

		$user_first_name = empty($user) ? '' : $user->display_name;
		$user_email = empty($user) ? '' : $user->user_email;  ?>

		<div class="epkb-editor-error--form-wrap">
			<div class="epkb-editor-error--form-message-1"></div>
			<div class="epkb-editor-error--form-message-2"><?php esc_html_e( 'We have detected errors on your website caused by your other plugins or website configuration. ' .
		                                                            'These errors could be causing other issues on your website. Let us help you fix your website errors.', 'echo-knowledge-base' ); ?></div>
			<div class="epkb-editor-error--form-message-3"><?php echo '*' . esc_html__( 'If you have a popup blocker, please disable it on this page and reload the page.', 'echo-knowledge-base' ); ?></div>
			<form id="epkb-editor-error--form" method="post">				<?php
				EPKB_HTML_Admin::nonce();				                    ?>
				<input type="hidden" name="action" value="epkb_editor_error" />
				<input type="hidden" name="editor_type" value="<?php echo EPKB_Core_Utilities::is_kb_flag_set( 'editor_backend_mode' ) ? 'backend' : 'frontend'; ?>">
				<div id="epkb-editor-error--form-body">

					<label for="epkb-editor-error--form-first_name"><?php esc_html_e( 'Name', 'echo-knowledge-base' ); ?>*</label>
					<input name="first_name" type="text" value="<?php echo esc_attr( $user_first_name ); ?>" required  id="epkb-editor-error--form-first_name">

					<label for="epkb-editor-error--form-email"><?php esc_html_e( 'Email', 'echo-knowledge-base' ); ?>*</label>
					<input name="email" type="email" value="<?php echo esc_attr( $user_email ); ?>" required id="epkb-editor-error--form-email">

					<label for="epkb-editor-error--form-editor_error"><?php esc_html_e( 'Error Details', 'echo-knowledge-base' ); ?>*</label>
					<textarea name="admin_error" class="editor_error" required id="epkb-editor-error--form-editor_error"></textarea>

					<div class="epkb-editor-error--form-btn-wrap">
						<input type="submit" name="submit_error" value="<?php esc_attr_e( 'Submit', 'echo-knowledge-base' ); ?>" class="epkb-editor-error--form-btn">
						<span class="epkb-close-notice epkb-editor-error--form-btn epkb-editor-error--form-btn-cancel"><?php esc_html_e( 'Cancel', 'echo-knowledge-base' ); ?></span><?php
						if ( EPKB_Core_Utilities::is_kb_flag_set( 'editor_backend_mode' ) ) {
							//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped     ?>
							<a href="<?php echo add_query_arg([ 'enable_editor_frontend_mode' => 1, '_wpnonce_epkb_ajax_action' => wp_create_nonce( '_wpnonce_epkb_ajax_action' ) ]); ?>"
							   class="epkb-editor-error--form-btn epkb-editor-error--form-btn-cancel" target="_blank"><?php esc_html_e( 'Try front-end Editor', 'echo-knowledge-base' ); ?></a> <?php
						} else {
							$url = admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) .
										'&page=epkb-kb-configuration&action=enable_editor_backend_mode&_wpnonce_epkb_ajax_action=' . wp_create_nonce( '_wpnonce_epkb_ajax_action' ) . '#settings__editor' ); ?>
							<a href="<?php echo esc_url( $url ); ?>" class="epkb-editor-error--form-btn epkb-editor-error--form-btn-cancel"><?php esc_html_e( 'Try backend Editor', 'echo-knowledge-base' ); ?></a><?php
						}       ?>
					</div>

					<div class="epkb-editor-error--form-response"></div>
				</div>
			</form>
		</div> <?php
	}

	/**
	 * EDITOR - Current vs KB Template and Layouts
	 *
	 * @return false|string
	 */
	public static function get_editor_settings_html() {
		ob_start(); ?>

		<div class="epkb-editor-settings-panel-container" id="epkb-editor-settings-templates">
			<div class="epkb-editor-settings-accordeon-item__title"><?php esc_html_e( 'Before you start editing', 'echo-knowledge-base' ); ?></div>
			<div class="epkb-editor-settings-accordeon-item__subtitle"><?php esc_html_e( 'Choose how KB is displayed with your theme:', 'echo-knowledge-base' ); ?></div>
			<div class="epkb-editor-settings-control-container epkb-editor-settings-control-type-image-select epkb-editor-settings-control-type-image-select--themes">

				<label class="epkb-editor-settings-control-image-select" data-name="templates_for_kb">
					<input type="radio" name="templates_for_kb" value="kb_templates">

					<div class="epkb-editor-settings-control-image-select--label">
                        <div class="epkb-editor-settings-control-image-select-recommended">Recommended</div>
						<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/editor/kb-template-option.jpg' ); ?>">
						<span class="epkb-editor-settings-control-image-select-name"><?php echo esc_html__( 'KB Template', 'echo-knowledge-base' ); ?></span>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-times"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'Blog Sidebar On article page', 'echo-knowlegde-base' ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line epkb-editor-settings-accordeon-item__description-line--margin">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-check"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'Full Width Page', 'echo-knowlegde-base' ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-check"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'KB Styled Category Archive page', 'echo-knowlegde-base' ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-check"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e ('Padding / Margin options', 'echo-knowlegde-base' ); ?></div>
						</div>
					</div>
				</label>
				<label class="epkb-editor-settings-control-image-select" data-name="templates_for_kb">
					<input type="radio" name="templates_for_kb" value="current_theme_templates">

					<div class="epkb-editor-settings-control-image-select--label">
						<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/editor/current-theme-option.jpg' ); ?>">
						<span class="epkb-editor-settings-control-image-select-name"><?php echo esc_html__( 'Current Theme Template', 'echo-knowledge-base' ); ?></span>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-check"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'Blog Sidebar On article page', 'echo-knowlegde-base' ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-question"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php printf( esc_html__( 'Full Width Page %s ( If theme allows )', 'echo-knowlegde-base' ), '<br>' ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-check"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'Category Archive displayed by theme', 'echo-knowlegde-base' ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-question"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'Padding / Margin options', 'echo-knowlegde-base' ); ?></div>
						</div>
					</div>
				</label>
			</div>

			<div class="epkb-editor-settings-accordeon-item__description">

                <div class="epkb-editor-settings-accordeon-item__description-learn-more">
                    <a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank"><?php esc_html_e( 'Learn more', 'echo-knowledge-base') ; ?></a>
					<p><?php esc_html_e( 'You can change this setting at any time.', 'echo-knowledge-base') ; ?></p>
                </div>
                <div class="epkb-editor-settings-accordeon-item__description-button">
                    <button id="epkb-editor-templates-apply" class="epkb-editor-btn epkb-editor-templates-apply"><?php esc_html_e( 'Apply', 'echo-knowledge-base') ; ?></button>
                </div>
			</div>
		</div>

		<div class="epkb-editor-settings-panel-container"  id="epkb-editor-settings-layouts">
			<div class="epkb-editor-settings-accordeon-item__title"><?php esc_html_e( 'Choose a Layout and save it', 'echo-knowledge-base' ); ?></div>
			<div class="epkb-editor-settings-control-container epkb-editor-settings-control-type-image-select">
				<label class="epkb-editor-settings-control-image-select" data-name="kb_main_page_layout">
					<input type="radio" name="kb_main_page_layout" value="Basic">

					<div class="epkb-editor-settings-control-image-select--label">
						<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/editor/basic-layout-dark.jpg' ); ?>">
						<span><?php esc_html_e( 'Basic', 'echo-knowledge-base' ); ?></span>
					</div>
				</label>

				<label class="epkb-editor-settings-control-image-select" data-name="kb_main_page_layout">
					<input type="radio" name="kb_main_page_layout" value="Tabs">

					<div class="epkb-editor-settings-control-image-select--label">
						<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/editor/tabs-layout.jpg' ); ?>">
						<span><?php esc_html_e( 'Tabs', 'echo-knowledge-base' ); ?></span>
					</div>
				</label>

				<label class="epkb-editor-settings-control-image-select" data-name="kb_main_page_layout">
					<input type="radio" name="kb_main_page_layout" value="Categories">

					<div class="epkb-editor-settings-control-image-select--label">
						<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/editor/category-focused-layout.jpg' ); ?>">
						<span><?php esc_html_e( 'Category Focused', 'echo-knowledge-base' ); ?></span>
					</div>
				</label>

				<label class="epkb-editor-settings-control-image-select" data-name="kb_main_page_layout">
					<input type="radio" name="kb_main_page_layout" value="Classic">

					<div class="epkb-editor-settings-control-image-select--label">
						<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/setting-icons/classic-layout.jpg' ); ?>">
						<span><?php esc_html_e( 'Classic', 'echo-knowledge-base' ); ?></span>
					</div>
				</label>

				<label class="epkb-editor-settings-control-image-select" data-name="kb_main_page_layout">
					<input type="radio" name="kb_main_page_layout" value="Drill-Down">

					<div class="epkb-editor-settings-control-image-select--label">
						<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/setting-icons/drill-down-layout.jpg' ); ?>">
						<span><?php esc_html_e( 'Drill Down', 'echo-knowledge-base' ); ?></span>
					</div>
				</label>    <?php

				if ( EPKB_Utilities::is_elegant_layouts_enabled() ) { ?>

					<label class="epkb-editor-settings-control-image-select" data-name="kb_main_page_layout">
						<input type="radio" name="kb_main_page_layout" value="Grid">

						<div class="epkb-editor-settings-control-image-select--label">
							<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/editor/grid-layout.jpg' ); ?>">
							<span><?php esc_html_e( 'Grid', 'echo-knowledge-base' ); ?></span>
						</div>
					</label>

					<label class="epkb-editor-settings-control-image-select" data-name="kb_main_page_layout">
						<input type="radio" name="kb_main_page_layout" value="Sidebar">

						<div class="epkb-editor-settings-control-image-select--label">
							<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/editor/sidebar-layout.jpg' ); ?>">
							<span><?php esc_html_e( 'Sidebar', 'echo-knowledge-base' ); ?></span>
						</div>
					</label><?php

				}  ?>

			</div>
		</div> <?php

		do_action( 'epkb_editor_settings_html' );

		return ob_get_clean();
	}

	/**
	 * Editor links in help menu
	 * @param $page_type
	 * @param $kb_config
	 * @return false|string
	 */
	public static function get_editor_modal_menu_links( $page_type, $kb_config ) {

		$editor_urls = EPKB_Editor_Utilities::get_editor_urls( $kb_config, '', '', '', false );

		$pages_data = array();

		foreach ( $editor_urls as $url_slug => $url ) {

			switch ( $url_slug ) {
				case 'main_page_url':

					if ( $kb_config['modular_main_page_toggle'] == 'on' ) {
						break;
					}

					$pages_data['main-page'] = array(
						'title' => esc_html__( 'Main Page Editor', 'echo-knowledge-base' ),
						'url' => $url
					);
					break;

				case 'article_page_url':
					$pages_data['article-page'] = array(
						'title' => esc_html__( 'Article Page Editor', 'echo-knowledge-base' ),
						'url' => $url
					);
					break;

				case 'archive_url':

					if ( $kb_config['archive_page_v3_toggle'] == 'on' ) {
						return '';
					}

					$pages_data['archive-page'] = array(
						'title' => esc_html__( 'Archive Page Editor', 'echo-knowledge-base' ),
						'url' => $url
					);
					break;
			}
		}

		if ( EPKB_Utilities::is_advanced_search_enabled() && ! empty($editor_urls['search_page_url']) ) {
			$pages_data['search-page'] = array(
				'title' => esc_html__( 'Search Results Editor', 'echo-knowledge-base' ),
				'url' => $editor_urls['search_page_url']
			);
		}

		// remove current link
		if ( ! empty ( $pages_data[$page_type] ) ) {
			unset( $pages_data[$page_type] );
		}

		ob_start();	?>

		<div class="epkb-editor-settings-menu-container">
			<div class="epkb-editor-settings-menu__inner">
				<div class="epkb-editor-settings-menu__group-container">
					<div class="epkb-editor-settings-menu__group__title"><?php esc_html_e( 'Other Pages', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-editor-settings-menu__group-items-container"><?php
						foreach ( $pages_data as $page ) { ?>
							<a href="<?php echo esc_url( $page['url'] ); ?>" class="epkb-editor-settings-menu__group-item-container" target="<?php echo EPKB_Core_Utilities::is_kb_flag_set( 'editor_backend_mode' ) ? '_self' : '_blank'; ?>">
								<div class="epkb-editor-settings-menu__group-item__icon epkbfa epkbfa-file-text-o"></div>
								<div class="epkb-editor-settings-menu__group-item__title"><?php echo esc_html( $page['title'] ); ?></div>
							</a><?php
						} ?>
					</div>
					<div class="epkb-editor-settings-menu__group__title"><?php esc_html_e( 'Help', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-editor-settings-menu__group-items-container">
						<a href="https://www.echoknowledgebase.com/documentation/" class="epkb-editor-settings-menu__group-item-container" target="_blank">
							<div class="epkb-editor-settings-menu__group-item__icon epkbfa epkbfa-graduation-cap"></div>
							<div class="epkb-editor-settings-menu__group-item__title"><?php esc_html_e( 'KB Documentation', 'echo-knowledge-base' ); ?></div>
						</a>
						<a href="https://www.echoknowledgebase.com/technical-support/" class="epkb-editor-settings-menu__group-item-container" target="_blank">
							<div class="epkb-editor-settings-menu__group-item__icon epkbfa epkbfa-life-ring"></div>
							<div class="epkb-editor-settings-menu__group-item__title"><?php esc_html_e( 'Support', 'echo-knowledge-base' ); ?></div>
						</a>
					</div>
				</div>
			</div>
		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Display page template used by the visual Editor to display its sidebar.
	 */
	private static function frontend_editor_page( ) {
		global $eckb_kb_id;

		$page_type = EPKB_Editor_Utilities::epkb_front_end_editor_type();

		// do not load editor if this is not KB page or the Editor configuration bar is being loaded
		if ( empty( $eckb_kb_id ) || ! empty($_REQUEST['epkb-editor-page-loaded']) ) {
			return;
		}

		// retrieve KB configuration
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $eckb_kb_id );

		// retrieve add-ons configuration
		$kb_config = apply_filters( 'eckb_kb_config', $kb_config );
		if ( empty( $kb_config ) || is_wp_error( $kb_config ) ) {
			return;
		}

		$body_class = EPKB_Core_Utilities::is_kb_flag_set( 'editor_backend_mode' ) ? 'epkb-editor-backend-mode' : '';  ?>
		
		<!doctype html>
		<html <?php language_attributes(); ?>>

			<head>
				<meta charset="<?php bloginfo( 'charset' ); ?>">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title><?php esc_html_e( 'Echo KB Editor', 'echo-knowledge-base' ); ?></title>
			</head>

			<body <?php body_class( ['epkb-editor-' . $page_type, $body_class ] ); ?>><?php
			
				self::get_editor_html();
				
				do_action( 'epkb_editor_enqueue_scripts' );
			    EPKB_Error_Handler::add_error_popup();
				EPKB_Error_Handler::no_js_inline_script();
				EPKB_Error_Handler::no_js_inline_styles(); ?>
			</body>

		</html><?php
	}
	
	public static function error_can_not_load() {
		EPKB_HTML_Forms::notification_box_popup( [
                'type' => 'error',
                'title' => esc_html__( 'Cannot open the visual Editor', 'echo-knowledge-base' ),
                'desc' =>  esc_html__( 'Cannot load the visual Editor on this page.', 'echo-knowledge-base' ) . ' ' . esc_html__( 'Are you logged in?', 'echo-knowledge-base' ) . ' ' .  esc_html__( 'Is this Knowledge Base URL?', 'echo-knowledge-base' ) ] );
	}

	public static function error_user_not_logged_in() {
		$link = sprintf( '<a href="%s">%s</a>', wp_login_url( empty( $_REQUEST['current_url'] ) ? '' : esc_url_raw( wp_unslash( $_REQUEST['current_url'] ) ) ), esc_html__( 'Login here', 'echo-knowledge-base' ) );
		EPKB_HTML_Forms::notification_box_popup( [
                'type' => 'error',
                'title' => esc_html__( 'Cannot open the visual Editor', 'echo-knowledge-base' ),
                'desc' => esc_html__( 'Your login has expired.', 'echo-knowledge-base' ) . ' ' . $link ]);
	}

	public static function error_no_permissions() {
		EPKB_HTML_Forms::notification_box_popup( [
                'type' => 'error',
                'title' => esc_html__( 'Cannot open the visual Editor', 'echo-knowledge-base' ),
                'desc' =>  esc_html__( 'You do not have permission to edit this knowledge base.', 'echo-knowledge-base' ) ] );
	}

	/**
	 * Use for backend mode iframe template instead theme one
	 * @param $template
	 * @return mixed|void
	 */
	function backend_mode_template( $template ) {

		if ( ! function_exists('wp_get_current_user')) {
			include(ABSPATH . "wp-includes/pluggable.php");
		}

		$page_type = EPKB_Editor_Utilities::epkb_front_end_editor_type();
		if ( ! in_array( $page_type, EPKB_Editor_Config_Base::EDITOR_PAGE_TYPES ) ) {
			epkb_load_admin_plugin_pages_resources();
			add_action( 'wp_footer', [ $this, 'error_can_not_load' ] );
			return $template;
		}

		if ( EPKB_Utilities::get_current_user() == null ) {
			epkb_load_admin_plugin_pages_resources();
			add_action( 'wp_footer', [ $this, 'error_user_not_logged_in' ] );
			return $template;
		}

		// get KB ID except on Category Archive Page without any article - we need KB ID here to have the Access Control working
		global $eckb_kb_id;
		$eckb_kb_id = empty( $eckb_kb_id ) ? EPKB_KB_Handler::get_kb_id_from_kb_main_page() : $eckb_kb_id;
		$eckb_kb_id = empty( $eckb_kb_id ) ? EPKB_Core_Utilities::get_kb_id() : $eckb_kb_id;

		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {
			epkb_load_admin_plugin_pages_resources();
			add_action( 'wp_footer', [ $this, 'error_no_permissions' ] );
			return $template;
		}

		add_filter( 'show_admin_bar', '__return_false' );

		// Remove all WordPress actions
		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_print_scripts' );
		remove_all_actions( 'wp_footer' );
		remove_all_actions( 'script_loader_tag' );

		// Handle `wp_enqueue_scripts`
		remove_all_actions( 'wp_enqueue_scripts' );

		add_action( 'wp_enqueue_scripts', 'epkb_load_front_end_editor', 999999 );
		add_action( 'wp_enqueue_scripts', 'epkb_load_editor_styles' );

		// Send MIME Type header like WP admin-header.
		@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );

		$body_class = EPKB_Core_Utilities::is_kb_flag_set( 'editor_backend_mode' ) ? 'epkb-editor-backend-mode' : '';  ?>

		<!doctype html>
		<html <?php language_attributes(); ?>>

			<head>
				<meta charset="<?php bloginfo( 'charset' ); ?>">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title><?php esc_html_e( 'Echo KB Editor', 'echo-knowledge-base' ); ?></title>
			</head>

			<body <?php body_class( ['epkb-editor-preview', 'epkb-edit-mode--' . $page_type, $body_class ] ); ?>><?php

			switch ( $page_type ) {
				case 'main-page':
					$this->backend_main_page_content();
					break;
				case 'article-page':
					$this->backend_article_page_content();
					break;
				case 'archive-page':
					$this->backend_archive_page_content();
					break;
				case 'search-page':
					$this->backend_search_page_content();
					break;
			}

			epkb_load_editor_backend_mode_styles_inline();
			EPKB_Error_Handler::add_error_popup();
			EPKB_Error_Handler::no_js_inline_script();
			EPKB_Error_Handler::no_js_inline_styles(); ?>
			</body>

		</html><?php

		// do not load the actual page
		die();
	}

	private function backend_main_page_content() {
		global $eckb_is_kb_main_page;

		$eckb_is_kb_main_page = true;
		$kb_id = EPKB_Utilities::get_eckb_kb_id();
		echo do_shortcode( '[' . EPKB_KB_Handler::KB_MAIN_PAGE_SHORTCODE_NAME . ' id=' . $kb_id . ']');
	}

	private function backend_article_page_content() {
		global $post;

		$post->post_title = esc_html__( 'Demo Article', 'echo-knowledge-base' );
		$post->ID = 0;
		$post->is_demo = true;
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo EPKB_Layouts_Setup::get_kb_page_output_hook( EPKB_KB_Demo_Data::get_sample_post_content(), false );
	}

	private function backend_archive_page_content() {
		$hide_header_footer = true;
		include_once( EPKB_Templates::locate_template( 'archive-categories.php' ) );
	}

	private function backend_search_page_content() {
		do_action( 'epkb_editor_backend_mode_search_page_content' );
	}

	public function enable_frontend_editor() {
		if ( EPKB_Utilities::get( 'enable_editor_frontend_mode' ) && ! empty( $_REQUEST['_wpnonce_epkb_ajax_action'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash(   $_REQUEST['_wpnonce_epkb_ajax_action'] ) ), '_wpnonce_epkb_ajax_action' ) ) {
			EPKB_Core_Utilities::remove_kb_flag( 'editor_backend_mode' );
		}
	}
}