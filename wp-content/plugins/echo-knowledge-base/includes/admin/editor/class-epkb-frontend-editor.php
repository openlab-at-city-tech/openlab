<?php

/**
 * Visual Helper Editor
 * Handles the display and saving of settings for modules on the frontend
 * within the KB Main Page Visual Helper.
 */
class EPKB_Frontend_Editor {

	private static $modules = [ 'search', 'categories_articles', 'articles_list', 'faqs', 'resource_links' ];

    /**
     * Constructor
     * Initializes the class, sets up KB configuration, and adds AJAX handlers.
     */
    public function __construct() {

		add_action( 'wp_ajax_eckb_apply_fe_settings', array( $this, 'update_preview_and_settings') );
		add_action( 'wp_ajax_nopriv_eckb_apply_fe_settings', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

        add_action( 'wp_ajax_eckb_save_fe_settings', array( $this, 'save_main_page_settings' ) );
        add_action( 'wp_ajax_nopriv_eckb_save_fe_settings', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_eckb_save_fe_article_settings', array( $this, 'save_article_page_settings' ) );
		add_action( 'wp_ajax_nopriv_eckb_save_fe_article_settings', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_eckb_save_fe_archive_settings', array( $this, 'save_archive_page_settings' ) );
		add_action( 'wp_ajax_nopriv_eckb_save_fe_archive_settings', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_eckb_closed_fe_editor', array( $this, 'closed_fe_editor' ) );
		add_action( 'wp_ajax_nopriv_eckb_closed_fe_editor', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		// report uncaught AJAX error
	    add_action( 'wp_ajax_epkb_editor_error', array( 'EPKB_Controller', 'handle_report_admin_error' ) );
	    add_action( 'wp_ajax_nopriv_epkb_editor_error', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

        add_action( 'wp_footer', array( $this, 'generate_page_content' ), 1 );
    }

    /**
     * Display Frontend Editor
     */
    public function generate_page_content() {

		$kb_id = EPKB_Utilities::get_eckb_kb_id( '' );
		if ( empty( $kb_id ) ) {
			return;
		}

		// continue only if we are on one of the following page: KB main page, KB article page, KB archive page
		$kb_page_type = EPKB_Editor_Utilities::epkb_front_end_editor_type();
		if ( empty( $kb_page_type ) ) {
			return;
		}

		if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {
			return;
		}

		// get KB configuration	- do nothing on fail
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $kb_config ) ) {
			return;
		}

		$frontend_editor_state = $kb_config['frontend_editor_switch_visibility_toggle'];
		if ( $frontend_editor_state == 'off' ) {

			// when FE is disabled in settings, then it still can be opened by direct admin links, and admin bar link, and when it refreshes page on settings change via reloading the entire page
			$is_load_editor_action = EPKB_Utilities::post( 'action' ) == 'epkb_load_editor' || EPKB_Utilities::post( 'epkb_fe_reopen_feature', null ) !== null;
			if ( ! $is_load_editor_action ) {
				return;
			}
		}

		// do not enable FE for KB Main Page if modular is off
		if ( $kb_page_type == 'main-page' && $kb_config['modular_main_page_toggle'] != 'on' ) {
			return;
		}

		// do not enable FE on Archive Pages for old category archive page v2 or if KB template is current
		if ( $kb_page_type == 'archive-page' && ( $kb_config['archive_page_v3_toggle'] != 'on' || $kb_config['template_for_archive_page'] == 'current_theme_templates' ) ) {
			return;
		}

		// do not show if a page builder is opened
		/* done in JS if ( EPKB_Editor_Utilities::is_page_builder_enabled() ) {
		    return;
		} */

		// if FE is opened then in Settings UI do not show legacy settings UI
		$is_legacy_settings = EPKB_Core_Utilities::is_kb_flag_set( 'is_legacy_settings' );
		if ( $is_legacy_settings ) {
			EPKB_Core_Utilities::remove_kb_flag( 'is_legacy_settings' );
		}	

	    // when FE preview is updated via the entire page reload without saving settings (for some of the settings controls need to reload the entire page)
	    $kb_config = self::fe_preview_config( $kb_config );

		// render settings
		self::render_editor( $kb_config );

        wp_enqueue_style( 'epkb-frontend-editor' );
		wp_enqueue_script( 'epkb-admin-form-controls-scripts' );
		wp_enqueue_script( 'epkb-frontend-editor' );
    }

    /**
     * Renders the HTML content for the settings sidebar.
     * Retrieves configuration settings and generates the form fields.
     */
    private static function render_editor( $kb_config ) {
		global $post;

		$frontend_editor_type = EPKB_Editor_Utilities::epkb_front_end_editor_type();
		$display_frontend_editor_closed = EPKB_Core_Utilities::is_kb_flag_set( 'epkb_fe_editor_closed' ); ?>

		<!-- Frontend Editor Toggle -->
		<div id="epkb-fe__toggle" class="epkb-fe__toggle" style="display: none;">
			<div class="epkb-fe__toggle-wrapper">
				<div class="epkb-fe_toggle-icon-wrapper">
					<span class="epkbfa epkbfa-pencil"></span>
				</div>
				<div class="epkb-fe__toggle-title">
					<span class="epkb-fe__toggle-title__text"><?php esc_html_e( 'Open Frontend Editor', 'echo-knowledge-base' ); ?></span>
				</div>
			</div>
		</div>		<?php 

		$editor_class = $frontend_editor_type === 'block-main-page' ? 'epkb-fe__editor--block-main-page' : '';	?>
	    <!-- Frontend Editor Sidebar -->
		<div id="epkb-fe__editor" class="epkb-admin__form epkb-fe__editor--home <?php echo esc_attr( $editor_class ); ?>" data-kbid="<?php echo esc_attr( $kb_config['id'] ); ?>"
		                    data-post-id="<?php echo empty( $post ) ? 0 : esc_attr( $post->ID ); ?>" style="display: none;"
		 					data-display-frontend-editor-closed="<?php echo $display_frontend_editor_closed ? 'true' : 'false'; ?>">

			<!-- Frontend Editor Header -->
			<div id="epkb-fe__header-container">

				<!-- Shared Titles -->
				<h1 data-title="home" class="epkb-fe__header-title"><?php esc_html_e( 'Frontend Editor', 'echo-knowledge-base' ); ?></h1>
				<h1 data-title="help" class="epkb-fe__header-title"><?php esc_html_e( 'Help', 'echo-knowledge-base' ); ?></h1>	<?php

				switch ( $frontend_editor_type ) {

					case 'main-page':	?>
						<!-- Main Page Titles -->
						<h1 data-title="search" class="epkb-fe__header-title"><?php esc_html_e( 'Search Box', 'echo-knowledge-base' ); ?></h1>
						<h1 data-title="categories_articles" class="epkb-fe__header-title"><?php esc_html_e( 'Categories and Articles', 'echo-knowledge-base' ); ?></h1>
						<h1 data-title="articles_list" class="epkb-fe__header-title"><?php esc_html_e( 'Featured Articles', 'echo-knowledge-base' ); ?></h1>
						<h1 data-title="faqs" class="epkb-fe__header-title"><?php esc_html_e( 'FAQs', 'echo-knowledge-base' ); ?></h1>
						<h1 data-title="resource_links" class="epkb-fe__header-title"><?php esc_html_e( 'Resource Links', 'echo-knowledge-base' ); ?></h1>	<?php
						break;

					case 'article-page':	?>
						<!-- Article Page Titles -->
						<h1 data-title="article-page-settings" class="epkb-fe__header-title"><?php esc_html_e( 'Settings', 'echo-knowledge-base' ); ?></h1>
						<h1 data-title="article-page-search-box" class="epkb-fe__header-title"><?php esc_html_e( 'Search Box', 'echo-knowledge-base' ); ?></h1>
						<h1 data-title="article-page-sidebar" class="epkb-fe__header-title"><?php esc_html_e( 'Sidebar', 'echo-knowledge-base' ); ?></h1>
						<h1 data-title="article-page-toc" class="epkb-fe__header-title"><?php esc_html_e( 'Table of Contents', 'echo-knowledge-base' ); ?></h1>
						<h1 data-title="article-page-ratings" class="epkb-fe__header-title"><?php esc_html_e( 'Rating and Feedback', 'echo-knowledge-base' ); ?></h1>	<?php
						break;

					case 'archive-page':	?>
						<!-- Archive Page Titles -->
						<h1 data-title="archive-page-settings" class="epkb-fe__header-title"><?php esc_html_e( 'Settings', 'echo-knowledge-base' ); ?></h1>	<?php
						break;

					case 'block-main-page':	?>
						<!-- Block Main Page Titles -->
						<h1 data-title="block-main-page-settings" class="epkb-fe__header-title"><?php esc_html_e( 'Settings', 'echo-knowledge-base' ); ?></h1>	<?php
						break;

					default:
						break;
				}	?>

				<div class="epkb-fe__header-close-button">
					<span class="epkbfa epkbfa-times"></span>
				</div>
			</div>

			<!-- List of features -->
			<div class="epkb-fe__features-list">
			
				<div class="epkb-fe__actions" style="display: none;">
					<span id="epkb-fe__action-back" class="epkb-primary-btn epkb-fe__action-btn">
						<span class="epkb-fe__action-btn-icon epkbfa epkbfa-chevron-left"></span>
						<span class="epkb-fe__action-btn-text"><?php esc_html_e( 'Back', 'echo-knowledge-base' ); ?></span>
					</span>
					<span id="epkb-fe__action-save" class="epkb-success-btn epkb-fe__action-btn"><?php esc_html_e( 'Save', 'echo-knowledge-base' ); ?></span>
				</div>	<?php

				// display settings for each feature
				switch ( $frontend_editor_type ) {

					case 'main-page':
						// we need to retrieve settings for all modules - hardcode all modules assigned to rows in $settings_kb_config to have their settings rendered by EPKB_Config_Settings_Page(),
						// while store actually selected modules in $kb_config
						$settings_kb_config = $kb_config;

						$assigned_modules = array();
						for ( $row_number = 1; $row_number <= EPKB_Modular_Main_Page::MAX_ROWS; $row_number++ ) {
							$module_key = 'ml_row_' . $row_number . '_module';
							if ( isset( $settings_kb_config[ $module_key ] ) && $settings_kb_config[ $module_key ] != 'none' ) {
								$assigned_modules[] = $settings_kb_config[ $module_key ];
							}
						}

						$unassigned_modules = array_diff( self::$modules, $assigned_modules );
						for ( $row_number = 1; $row_number <= EPKB_Modular_Main_Page::MAX_ROWS; $row_number++ ) {
							$module_key = 'ml_row_' . $row_number . '_module';
							if ( isset( $settings_kb_config[ $module_key ] ) && $settings_kb_config[ $module_key ] == 'none' ) {
								if ( ! empty( $unassigned_modules ) ) {
									$settings_kb_config[ $module_key ] = array_shift( $unassigned_modules );
								}
							}
						}

						$config_page = new EPKB_Config_Settings_Page( $settings_kb_config, true );
						$features_config = $config_page->get_vertical_tabs_config( 'main-page' );
						self::display_main_page_feature_selection_buttons( array(
							'search' => __( 'Search', 'echo-knowledge-base' ),
							'categories_articles' => __( 'Categories & Articles', 'echo-knowledge-base' ),
							'articles_list' => __( 'Featured Articles', 'echo-knowledge-base' ),
							'faqs' => __( 'FAQs', 'echo-knowledge-base' ),
							'resource_links' => __( 'Resource Links', 'echo-knowledge-base' ),
						) );
						self::display_main_page_settings( $features_config, $kb_config );
						break;

					case 'article-page':
						$config_page = new EPKB_Config_Settings_Page( $kb_config, true );
						$features_config = $config_page->get_vertical_tabs_config( 'article-page' );
						self::display_article_page_settings( $features_config );
						break;

					case 'archive-page':
						$config_page = new EPKB_Config_Settings_Page( $kb_config, true );
						$features_config = $config_page->get_vertical_tabs_config( 'archive-page' );
						self::display_archive_page_settings( $features_config );
						break;

					case 'block-main-page':
						self::display_block_main_page_settings( $kb_config );
						break;

					default:
						break;
				}	?>
			</div>

			<!-- Help tab -->
			<div class='epkb-fe__help-container'>	<?php
				self::display_help_tab( $kb_config, $frontend_editor_type );	?>
			</div> 

			<!-- Frontend Editor Footer -->
			<div id="epkb-fe__footer-container">  
				<!-- text is available to screen readers but not visible on screen -->
				<span id="epkb-tab-instructions" class="epkb-sr-only"><?php esc_html_e( 'Use arrow keys to move between features', 'echo-knowledge-base' ); ?></span>

				<!-- FEATURES CONTAINER -->
				<div id="epkb-fe__tab-container" role="tablist" aria-label="Help Dialog Top Tabs" aria-describedby="epkb-tab-instructions">

					<div id="epkb-fe__help-tab" role="tab" aria-selected="true" tabindex="0" class="epkb-fe__tab epkb-fe__tab__help-btn epkb-fe__tab--active" data-epkb-target-tab="help">
						<span class="epkb-fe__tab__icon epkbfa epkbfa-book"></span>
						<span class="epkb-fe__tab__text"><?php esc_html_e( 'Help', 'echo-knowledge-base' ); ?></span>
					</div>  

					<a id="epkb-fe__contact-tab" href="<?php echo esc_url( 'https://www.echoknowledgebase.com/contact-us/' ); ?>" target="_blank" rel="noopener noreferrer" aria-selected="false" tabindex="-1" class="epkb-fe__tab epkb-fe__tab__contact-btn" data-epkb-target-tab="contact">
						<span class="epkb-fe__tab__icon epkbfa epkbfa-envelope-o"></span>
						<span class="epkb-fe__tab__text"><?php esc_html_e( 'Contact Us', 'echo-knowledge-base' ); ?></span>
					</a>  		

				</div>				
			</div>
		</div>

		<!-- Error Form -->
		<div id="epkb-fe__error-form-wrap" style="display: none !important;">	<?php
			EPKB_HTML_Admin::display_report_admin_error_form();	?>
		</div>	<?php
    }

	private static function display_help_tab( $kb_config, $frontend_editor_type ) {

		// TODO: it looks like for each FE type need to show dedicated Help content
		if ( $frontend_editor_type == 'block-main-page' ) {
			return;
		}

		// Is this page or search box too narrow? ------------------------/
		$search_row_width_key = '';
		$category_row_width_key = '';

		for ( $row_index = 1; $row_index <= EPKB_Modular_Main_Page::MAX_ROWS; $row_index++ ) {
			if ( $kb_config['ml_row_' . $row_index . '_module'] === 'categories_articles' ) {
				$category_row_width_key = 'ml_row_' . $row_index . '_desktop_width';
				continue;
			}
			if ( $kb_config['ml_row_' . $row_index . '_module'] === 'search' ) {
				$search_row_width_key = 'ml_row_' . $row_index . '_desktop_width';
			}
		}

		ob_start();	?>

		<h4><?php echo esc_html__( 'Page width', 'echo-knowledge-base' ) . ': '; ?><span class='js-epkb-mp-width'>-</span></h4> <?php

		if ( ! empty( $search_row_width_key ) ) {	?>

			<h5><?php echo esc_html__( 'Search Box', 'echo-knowledge-base' ); ?></h5>

			<ul>
				
				<li><?php echo esc_html__( 'Actual width', 'echo-knowledge-base' ) . ': '; ?><span class="js-epkb-mp-search-width">-</span></li>

				<li><?php echo esc_html__( 'KB setting for Search Width', 'echo-knowledge-base' ) . ': ' . esc_attr( $kb_config[ $search_row_width_key ] . $kb_config[ $search_row_width_key . '_units' ] ) .

					( $kb_config[ $search_row_width_key . '_units' ] == '%' ? ' ' . esc_html__( 'of the page.', 'echo-knowledge-base' ) : '' ); ?>

					<a href="#" class="epkb-fe__open-feature-setting-link" data-feature="search" data-section="module-settings"><?php echo esc_html__( 'Edit', 'echo-knowledge-base' ); ?></a>
				</li>
			</ul>	<?php
		}

		if ( ! empty( $category_row_width_key ) ) {	?>
			<h5><?php echo esc_html__( 'Categories and Articles', 'echo-knowledge-base' ); ?></h5>

			<ul>
				<li><?php echo esc_html__( 'Actual width', 'echo-knowledge-base' ) . ': '; ?><span class="js-epkb-mp-width-container">-</span></li>

				<li><?php echo esc_html__( 'KB setting for categories list width', 'echo-knowledge-base' ); echo ': ' . esc_attr( $kb_config[ $category_row_width_key ] . $kb_config[ $category_row_width_key . '_units' ] ) .
							( $kb_config[ $category_row_width_key . '_units' ] == '%' ? ' ' . esc_html__( 'of the total page width.', 'echo-knowledge-base' ) : '' ); ?>
						<a href="#" class="epkb-fe__open-feature-setting-link" data-feature="categories_articles" data-section="module-settings"><?php echo esc_html__( 'Edit', 'echo-knowledge-base' ); ?></a>
				</li>
			</ul>	<?php
		}	?>

		<h5><?php echo esc_html__( 'Troubleshooting', 'echo-knowledge-base' ); ?></h5>

		<p><?php echo esc_html__( 'If the value you set in the KB settings does not match the actual value, it may be because your theme or page builder is limiting the overall width. In such cases, the KB settings cannot exceed the maximum width allowed ' .
						'by your theme or page builder. Try the following', 'echo-knowledge-base' ) . ':'; ?>
		</p>

		<ul>
			<li><?php echo sprintf( esc_html__( 'If the KB Shortcode is inserted inside your page builder, then you will need to check the section width of that page builder. %s', 'echo-knowledge-base' ),
				'<a href="https://www.echoknowledgebase.com/documentation/main-page-width-and-page-builders/" target="_blank" rel="nofollow">' . esc_html__( 'Learn more', 'echo-knowledge-base' ) .' '. '<span class="epkbfa epkbfa-external-link"> </span></a> ' ); ?>
			</li><?php

			if ( $kb_config['templates_for_kb'] == 'current_theme_templates' ) { ?>
				<li><?php echo sprintf( esc_html__( 'You are currently using the Current Theme Template. Check your theme settings or switch to the KB template. %s', 'echo-knowledge-base' ),
					'<a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank" rel="nofollow">' . esc_html__( 'Learn more', 'echo-knowledge-base' ) .' '. '<span class="epkbfa epkbfa-external-link"></span></a> ' ); ?>
				</li>	<?php
			}	?>
		</ul>	<?php

		$content = ob_get_clean();

		self::display_section( __( 'Is this page or search box too narrow?', 'echo-knowledge-base' ), $content );

		ob_start(); ?>

		<p> <?php
		echo sprintf( esc_html__( 'The Knowledge Base offers two template options for both Main and Article Pages: %sKB Template%s and %sCurrent Theme Template%s.', 'echo-knowledge-base' ), '<strong>', '</strong>', '<strong>', '</strong>' ) . ' ' .
			'<a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank" rel="nofollow">' . esc_html__(  'Learn More', 'echo-knowledge-base' ) .' '. '<span class="epkbfa epkbfa-external-link"></span></a>';  ?>
		</p>

		<p><?php echo esc_html__( 'If you\'re experiencing layout issues or want to see a different look, try switching the template', 'echo-knowledge-base' ) . ':'; ?></p>
		<a href="#" class="epkb-fe__open-feature-setting-link" data-feature="categories_articles" data-setting="templates_for_kb"><?php esc_html_e( 'Click here to switch the template', 'echo-knowledge-base' ); ?></a> <?php

		$content = ob_get_clean();

		self::display_section( __( 'Issues with the page layout, header, or menu?', 'echo-knowledge-base' ), $content );
	}

	/**
	 * Display a collapsible section with a title and content.
	 * 
	 * @param string $title   The title text to display in the section header
	 * @param string $content The HTML content to display in the section body
	 */
	private static function display_section( $title, $content ) { ?>
		<div class="epkb-fe__settings-section">
			<div class="epkb-fe__settings-section-header">				<?php
				echo esc_html( $title ); ?>
				<i class="epkbfa epkbfa-chevron-down"></i>
				<i class="epkbfa epkbfa-chevron-up"></i>
			</div>

			<div class="epkb-fe__settings-section-body">				<?php
				echo wp_kses_post( $content ); ?>
			</div>
		</div>	<?php
	}

	private static function display_main_page_settings( $features_config, $kb_config ) {

		$is_elay_enabled = EPKB_Utilities::is_elegant_layouts_enabled();

		foreach ( $features_config['main-page']['sub_tabs'] as $row_index => $row_config ) {

			$is_resource_links_unavailable = $row_config['data']['selected-module'] == 'resource_links' && ! $is_elay_enabled;

			$module_position = $is_resource_links_unavailable ? 'none' : self::get_module_row_number( $row_config['data']['selected-module'], $kb_config );	?>

			<!-- Module settings -->
			<div class="epkb-fe__feature-settings" data-feature="<?php echo esc_attr( $row_config['data']['selected-module'] ); ?>" data-row-number="<?php echo esc_attr( $module_position ); ?>" data-kb-page-type="main-page">

				<!-- Module settings body -->
				<div class="epkb-fe__settings-list">	<?php
					if ( $is_resource_links_unavailable ) {
						EPKB_HTML_Admin::show_resource_links_ad();
					} else {
						echo self::get_module_position_field( $row_config['data']['selected-module'], $module_position );
						self::display_feature_settings( $row_config['contents'] );
					}	?>
				</div>
			</div>	<?php
		}
	}

	/**
	 * Display buttons HTML to select a feature for Main Page in desired sequence (since the features can change their sequence on the page, it is needed to keep their sequence in UI constant)
	 * @param $features_list
	 * @return void
	 */
	private static function display_main_page_feature_selection_buttons( $features_list ) {
		foreach ( $features_list as $feature_name => $feature_title ) {	?>
			<!-- Module icon -->
			<div class="epkb-fe__feature-select-button" data-feature="<?php echo esc_attr( $feature_name ); ?>">
				<i class="<?php echo self::get_features_icon_escaped( $feature_name ); ?> epkb-fe__feature-icon"></i>
				<span class="epkb-fe__feature-title"><?php echo esc_html( $feature_title ); ?></span>
			</div>	<?php
		}
	}

	private static function display_article_page_settings( $features_config ) {

		foreach ( $features_config['article-page']['sub_tabs'] as $feature_index => $feature_config ) {	?>

			<!-- Feature icon -->
			<div class="epkb-fe__feature-select-button" data-feature="<?php echo esc_attr( $feature_config['key'] ); ?>">
				<i class="<?php echo self::get_features_icon_escaped( $feature_config['key'] ); ?> epkb-fe__feature-icon"></i>
				<span class="epkb-fe__feature-title"><?php echo esc_html( $feature_config['title'] ); ?></span>
			</div>

			<!-- Feature settings -->
			<div class="epkb-fe__feature-settings" data-feature="<?php echo esc_attr( $feature_config['key'] ); ?>" data-kb-page-type="article-page">

				<!-- Sub-feature settings body -->
				<div class="epkb-fe__settings-list">	<?php
					self::display_feature_settings( $feature_config['contents'] );	?>
				</div>
			</div>	<?php
		}
	}

	private static function display_archive_page_settings( $features_config ) {	?>

		<!-- Feature icon -->
		<div class="epkb-fe__feature-select-button" data-feature="archive-page-settings">
			<i class="<?php echo self::get_features_icon_escaped( 'archive-page-settings' ); ?> epkb-fe__feature-icon"></i>
			<span class="epkb-fe__feature-title"><?php esc_html_e( 'Settings', 'echo-knowledge-base' ); ?></span>
		</div>

		<!-- Feature settings -->
		<div class="epkb-fe__feature-settings" data-feature="archive-page-settings" data-kb-page-type="archive-page">

			<!-- Settings body -->
			<div class="epkb-fe__settings-list">	<?php
				self::display_feature_settings( $features_config['archive-page']['contents'] );	?>
			</div>
		</div>	<?php
	}

	private static function display_block_main_page_settings( $kb_config ) {	?>
		<div class="epkb-fe__settings-list">
			<div class="epkb-fe__sub-content">	<?php
				echo wp_kses( EPKB_HTML_Admin::display_block_main_page( $kb_config, false ), EPKB_Utilities::get_admin_ui_extended_html_tags() );	?>
			</div>
		</div>	<?php
	}

	/**
	 * Display settings HTML for each feature
	 * @param $feature_config_contents
	 * @param $return_html
	 * @return string
	 */
	private static function display_feature_settings( $feature_config_contents, $return_html = false ) {

		if ( $return_html ) {
			ob_start();
		}

		foreach ( $feature_config_contents as $settings_section ) {

			$css_class = empty( $settings_section['css_class'] ) ? '' : ' ' . str_replace( 'epkb-admin__form-tab-content', 'epkb-fe__settings-section', $settings_section['css_class'] );
			$data_escaped = '';
			if ( isset( $settings_section['data'] ) ) {
				foreach ( $settings_section['data'] as $data_key => $data_value ) {
					$data_escaped .= 'data-' . esc_attr( $data_key ) . '="' . esc_attr( str_replace( 'epkb-admin__form-tab-content', 'epkb-fe__settings-section', $data_value ) ) . '" ';
				}
			}	?>

			<!-- Settings section -->
			<div class="epkb-fe__settings-section epkb-fe__is_opened<?php echo esc_attr( $css_class ); ?>" <?php echo $data_escaped; ?>>
				<div class="epkb-fe__settings-section-header"><?php echo esc_html( $settings_section['title'] ); ?><i class="epkbfa epkbfa-chevron-down"></i><i class="epkbfa epkbfa-chevron-up"></i></div>
				<div class="epkb-fe__settings-section-body">	<?php
					echo wp_kses( $settings_section['body_html'], EPKB_Utilities::get_admin_ui_extended_html_tags() );	?>
				</div>
			</div>	<?php
		}

		if ( $return_html ) {
			return ob_get_clean();
		}
	}

	/**************************************************************************************
	 *
	 *    AJAX PREVIEW HANDLERS
	 *
	 **************************************************************************************/

	/**
	 * AJAX preview changes without saving
	 */
	public function update_preview_and_settings() {
		global $epkb_frontend_editor_preview, $eckb_kb_id, $post;

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_frontend_editor_write' );

		$epkb_frontend_editor_preview = true;

		// if FE is opened then in Settings UI do not show legacy settings UI
		$is_legacy_settings = EPKB_Core_Utilities::is_kb_flag_set( 'is_legacy_settings' );
		if ( $is_legacy_settings ) {
			EPKB_Core_Utilities::remove_kb_flag( 'is_legacy_settings' );
		}

		$feature_name = EPKB_Utilities::post( 'feature_name' );
		$kb_page_type = EPKB_Utilities::post( 'kb_page_type' );
		$setting_name = EPKB_Utilities::post( 'setting_name' );
		$layout_name = EPKB_Utilities::post( 'layout_name' );
		$settings_row_number = EPKB_Utilities::post( 'settings_row_number' );

		// do not use the self::update_module_position() here because on preview the rows numbers in HTML remain original
		$config = self::merge_new_and_old_kb_config( false );
		$orig_config = $config['orig_config'];
		$new_config = $config['new_config'];
		$unmerged_new_config = $config['unmerged_new_config'];

		ob_start();
		$faqs_design_settings = array();
		$categories_articles_design_settings = array();
		$search_design_settings = array();
		$archive_design_settings = array();
		switch ( $feature_name ) {

			// Main Page 'Search' feature
			case 'search':
				global $eckb_is_kb_main_page;
				$eckb_is_kb_main_page = true;

				EPKB_Editor_Utilities::initialize_advanced_search_box();

				// after the design preset applied, it is reset to 'current' to avoid continuing applying and enable further settings change - use this to distinct request for design change and request for settings change after applying design preset
				$is_design_preset_change = $new_config['advanced_search_mp_presets'] != 'current';

				// design preset may change settings which are not present in the FE UI - apply full design settings + FE UI settings until user save settings
				$selected_search_preset = EPKB_Utilities::post( 'selected_search_preset', 'current' );

				// to apply preset add-on needs preset name in config
				$new_config['advanced_search_mp_presets'] = $selected_search_preset;

				// search preset settings
				$search_design_config = EPKB_Core_Utilities::advanced_search_presets( $new_config, $orig_config, 'mp' );
				$search_design_settings = EPKB_Utilities::diff_two_dimentional_arrays( $search_design_config, $new_config );
				if ( ! empty( $search_design_settings ) ) {
					if ( $is_design_preset_change ) {
						$new_config = $search_design_config;

					} else {
						// user changes have higher priority if the changes applying after design preset is already applied in one of previous requests
						$new_config = array_merge( $search_design_config, $unmerged_new_config );

						// do not change UI settings programmatically here as the preset is already applied and currently the user is changing settings on unsaved preset
						$search_design_settings = [];
					}
				}

				EPKB_Modular_Main_Page::search_module( $new_config );
				break;

			// Main Page 'Categories & Articles' feature
			case 'categories_articles':
				global $eckb_is_kb_main_page;
				$eckb_is_kb_main_page = true;

				// adjust settings on layout change

				// temporarily set the layout name to the one selected in the editor if user went from e.g. Basic -> Tabs -> Basic
				// original layout is the layout storing in DataBase, but for the purpose of layout change we want to capture Tabs -> Basic change
				if ( ! empty( $layout_name ) ) {
					$orig_config['kb_main_page_layout'] = $layout_name;
				}

				$new_config_result = EPKB_Core_Utilities::adjust_settings_on_layout_change( $orig_config, $new_config );
				$new_config = $new_config_result['new_config'];
				$seq_meta = $new_config_result['seq_meta'];

				// only get preset settings if user is modifying it
				if ( $setting_name == 'categories_articles_preset' && ! empty( $new_config['categories_articles_preset'] ) && $new_config['categories_articles_preset'] != 'current' ) {
					$categories_articles_design_settings = EPKB_KB_Wizard_Themes::get_theme( $new_config['categories_articles_preset'], $new_config );
					$new_config = array_merge( $new_config, $categories_articles_design_settings );
				}

				$handler = new EPKB_Modular_Main_Page();
				$handler->setup_layout_data( $new_config, $seq_meta );
				if ( $new_config['kb_main_page_layout'] == EPKB_Layout::SIDEBAR_LAYOUT ) {
					$intro_text = apply_filters( 'eckb_main_page_sidebar_intro_text', $new_config['sidebar_main_page_intro_text'], $new_config['id'] );
					$temp_article = new stdClass();
					$temp_article->ID = 0;
					$temp_article->post_title = esc_html__( 'Demo Article', 'echo-knowledge-base' );
					$temp_article->post_content = wp_kses( $intro_text, EPKB_Utilities::get_extended_html_tags( true ) );
					$temp_article = new WP_Post( $temp_article );

					$new_config['sidebar_welcome'] = 'on';
					$new_config['article_content_enable_back_navigation'] = 'off';
					$new_config['prev_next_navigation_enable'] = 'off';
					$new_config['article_content_enable_rows'] = 'off';
					$layout_output = EPKB_Articles_Setup::get_article_content_and_features( $temp_article, $temp_article->post_content, $new_config );
					$handler->set_sidebar_layout_content( $layout_output );
				}

				if ( $handler->has_kb_categories() ) {
					$handler->categories_articles_module( $new_config );
				} else {
					$handler->show_categories_missing_message();
				}
				break;

			// Main Page 'Featured Articles' feature
			case 'articles_list':   ?>
				<div id="epkb-ml__module-articles-list" class="epkb-ml__module">   <?php
					$articles_list_handler = new EPKB_ML_Articles_List( $new_config );
					$articles_list_handler->display_articles_list();	?>
				</div>  <?php
				break;

			// Main Page 'FAQs' feature
			case 'faqs':    	?>
				<div id="epkb-ml__module-faqs" class="epkb-ml__module">   <?php

					if ( ! empty( $new_config['faq_preset_name'] ) ) {
						$faqs_design_settings = EPKB_FAQs_Utilities::get_design_settings( $new_config['faq_preset_name'] );
						$new_config = array_merge( $new_config, $faqs_design_settings );
					}

					$faqs_handler = new EPKB_ML_FAQs( $new_config );
					$faqs_handler->display_faqs_module( true, true ); ?>
				</div>	<?php
				break;

			case 'resource_links':
				do_action( 'epkb_ml_resource_links_module', $new_config );
				// echo '<style>' . apply_filters( 'epkb_ml_resource_links_module_styles', '', $new_config ) . '</style>;
				break;

			// Article Page features update entire Article HTML
			case 'article-page-settings':
			case 'article-page-search-box':
			case 'article-page-sidebar':
			case 'article-page-toc':
			case 'article-page-ratings':

				$article_id = (int)EPKB_Utilities::post( 'kb_post_id' );

				// Initialize Advanced Search if needed
				EPKB_Editor_Utilities::initialize_advanced_search_box( false );

				// sync Article Page Search setting with Main Page Search settings
				$synced_new_config = EPKB_Core_Utilities::sync_article_page_search_with_main_page_search( $new_config, $orig_config );
				$search_design_settings = EPKB_Utilities::diff_two_dimentional_arrays( $synced_new_config, $new_config );
				$new_config = $synced_new_config;

				// apply design settings only if sync toggle is 'off'
				if ( empty( $new_config['article_search_sync_toggle'] ) || $new_config['article_search_sync_toggle'] == 'off' ) {

					// after the design preset applied, it is reset to 'current' to avoid continuing applying and enable further settings change - use this to distinct request for design change and request for settings change after applying design preset
					$is_design_preset_change = $new_config['advanced_search_ap_presets'] != 'current';

					// design preset may change settings which are not present in the FE UI - apply full design settings + FE UI settings until user save settings
					$selected_search_preset = EPKB_Utilities::post( 'selected_search_preset', 'current' );

					// to apply preset add-on needs preset name in config
					$new_config['advanced_search_ap_presets'] = $selected_search_preset;

					$search_design_config = EPKB_Core_Utilities::advanced_search_presets( $new_config, $orig_config, 'ap' );
					$search_preset_diff = EPKB_Utilities::diff_two_dimentional_arrays( $search_design_config, $new_config );
					if ( ! empty( $search_preset_diff ) ) {
						if ( $is_design_preset_change ) {
							$new_config = $search_design_config;
							$search_design_settings = array_merge( $search_design_settings, $search_preset_diff );

						} else {
							// user changes have higher priority if the changes applying after design preset is already applied in one of previous requests
							$new_config = array_merge( $search_design_config, $unmerged_new_config );
						}
					}

				}

				global $eckb_is_kb_main_page;
				$eckb_is_kb_main_page = false;

				// Sidebar
				// recalculate width
				$new_config = EPKB_Core_Utilities::reset_article_sidebar_widths( $new_config );

				// TOC
				// Process sidebar priority for TOC location
				$new_config['article_sidebar_component_priority'] = EPKB_KB_Config_Controller::convert_ui_data_to_article_sidebar_component_priority( $new_config );
				$new_config = EPKB_Core_Utilities::update_article_sidebar_priority( $orig_config, $new_config );

				// For general settings, we need to update the article content header which contains metadata like author, dates, etc.

				// Set up global $post for WordPress date functions to work properly
				$original_post = $GLOBALS['post'] ?? null;
				$demo_article = null;
				if ( empty( $original_post ) ) {
					if ( $article_id > 0 ) {
						$demo_article = get_post( $article_id );
					}
					if ( empty( $demo_article ) ) {
						$demo_article = new Stdclass();
						$demo_article->ID = 0;
						$demo_article->post_title = __( 'Demo Article Title', 'echo-knowledge-base' );
						$demo_article->post_author = get_current_user_id();
						$demo_article->post_date = current_time( 'mysql' );
						$demo_article->post_modified = current_time( 'mysql' );
						$demo_article->filter = 'raw';
						$demo_article = new WP_Post( $demo_article );
					}
				}
				$GLOBALS['post'] = $demo_article;
				$post = $demo_article;
				$eckb_kb_id = $new_config['id'];

				echo EPKB_Articles_Setup::get_article_content_and_features( $demo_article, $demo_article->post_content, $new_config );

				// Restore original global $post
				$GLOBALS['post'] = $original_post;

				// Also include any template wrapper updates
				$template_style_escaped = EPKB_Utilities::get_inline_style(
					' padding-top::       template_article_padding_top,
					padding-bottom::    template_article_padding_bottom,
					padding-left::      template_article_padding_left,
					padding-right::     template_article_padding_right,
					margin-top::        template_article_margin_top,
					margin-bottom::     template_article_margin_bottom,
					margin-left::       template_article_margin_left,
					margin-right::      template_article_margin_right,', $new_config );

				// CSS Article Reset / Defaults
				$article_class_escaped = '';
				if ( $new_config[ 'templates_for_kb_article_reset'] === 'on' ) {
					$article_class_escaped .= 'eckb-article-resets ';
				}
				if ( $new_config[ 'templates_for_kb_article_defaults'] === 'on' ) {
					$article_class_escaped .= 'eckb-article-defaults ';
				}

				// Include template data for JavaScript to update as well
				?><script type="application/json" id="eckb-template-update-data"><?php
				echo wp_json_encode( array(
					'classes' => $article_class_escaped,
					'style' => $template_style_escaped
				) );
				?></script><?php
				break;

			// Archive Page features update entire Archive HTML
			case 'archive-page-settings':
				// Initialize Advanced Search if needed
				EPKB_Editor_Utilities::initialize_advanced_search_box();
				$new_config = EPKB_Core_Utilities::advanced_search_presets( $new_config, $orig_config, 'cp' );

				if ( ! empty( $new_config['archive_content_sub_categories_display_mode'] ) ) {
					$archive_design_settings = EPKB_Core_Utilities::get_category_archive_page_design_settings( $new_config['archive_content_sub_categories_display_mode'] );
					$new_config = array_merge( $new_config, $archive_design_settings );
				}
				
				// Get taxonomy and term ID from the AJAX request
				$taxonomy = EPKB_Utilities::post( 'taxonomy' );
				$term_id = EPKB_Utilities::post( 'term_id', 0 );
				
				// Set up the query context for archive page
				if ( ! empty( $taxonomy ) && ! empty( $term_id ) ) {
					$GLOBALS['taxonomy'] = $taxonomy;
					
					// Get the term object
					$term = get_term( $term_id, $taxonomy );
					if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
						// Set up query vars that archive pages expect
						global $wp_query;
						if ( empty( $wp_query ) ) {
							$wp_query = new WP_Query();
						}
						$wp_query->is_archive = true;
						$wp_query->is_tax = true;
						$wp_query->queried_object = $term;
						$wp_query->queried_object_id = $term_id;
					}
				}
				
				// Get the category archive page HTML
				if ( EPKB_KB_Handler::is_kb_category_taxonomy( $GLOBALS['taxonomy'] ) ) {
					EPKB_Category_Archive_Setup::get_category_archive_page_v3( $new_config );
				} else if (  EPKB_KB_Handler::is_kb_tag_taxonomy( $GLOBALS['taxonomy'] ) ) {
					EPKB_Tag_Archive_Setup::get_tag_archive_page( $new_config );
				}
				break;

			default:
				break;
		}

		$preview_html = ob_get_clean();

		$updated_settings_html = self::get_updated_settings_html( $new_config, $kb_page_type, $setting_name, $feature_name, $settings_row_number );
		$inline_styles = self::get_inline_styles( $new_config, $kb_page_type );

		$response_data = $inline_styles + array(
			'preview_html' => $preview_html,
			'layout_settings_html' => $updated_settings_html['layout_settings_html'],
			'layout_settings_html_temp' => $updated_settings_html['layout_settings_html_temp'],
			'faqs_design_settings' => $faqs_design_settings,
			'categories_articles_design_settings' => $categories_articles_design_settings,
			'search_design_settings' => $search_design_settings,
			'archive_design_settings' => $archive_design_settings,
		);

		wp_send_json_success( $response_data );
	}

	/**
	 * For some settings need to reload entire page - populate the KB config with the changes which are not saved yet
	 * @param $kb_config
	 * @return array|mixed|null
	 */
	public static function fe_preview_config( $kb_config ) {

		// only for reloading if user changes KB Template option
		if ( EPKB_Utilities::post( 'epkb_fe_reload_mode' ) != 'on' ) {
			return $kb_config;
		}

		// use cache
		static $cached_kb_config = null;
		if ( ! empty( $cached_kb_config ) ) {
			return $cached_kb_config;
		}

		$config = self::merge_new_and_old_kb_config( true, true );
		$new_config = $config['new_config'];

		$cached_kb_config = $new_config;

		return $new_config;
	}

    /**
     * AJAX save all Main Pagesettings
     * Handles nonce verification, user permissions check, data retrieval, and configuration update.
     */
    public function save_main_page_settings() {

        EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_frontend_editor_write' );

	    // if FE is opened then in Settings UI do not show legacy settings UI
	    $is_legacy_settings = EPKB_Core_Utilities::is_kb_flag_set( 'is_legacy_settings' );
	    if ( $is_legacy_settings ) {
		    EPKB_Core_Utilities::remove_kb_flag( 'is_legacy_settings' );
	    }

	    $config = self::merge_new_and_old_kb_config();
	    $orig_config = $config['orig_config'];
	    $new_config = $config['new_config'];
		$unmerged_new_config = $config['unmerged_new_config'];
		$kb_id = $config['kb_id'];

		// at this point FE already applied all layout change adjustments - by syncing configs layout we ensure the adjustments will not be triggered again (and thus will not rewrite user changes) during the update
		$orig_config['kb_main_page_layout'] = $new_config['kb_main_page_layout'];

		// Check if the user has permission to save settings
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid Knowledge Base ID', 'echo-knowledge-base' ) ) );
		}

		// after the design preset applied, it is reset to 'current' to avoid continuing applying and enable further settings change - use this to distinct request for design change and request for settings change after applying design preset
		$is_design_preset_change = $new_config['advanced_search_mp_presets'] != 'current';

		// design preset may change settings which are not present in the FE UI - apply full design settings + FE UI settings until user save settings
		$selected_search_preset = EPKB_Utilities::post( 'selected_search_preset', 'current' );

		// to apply preset add-on needs preset name in config
		$new_config['advanced_search_mp_presets'] = $selected_search_preset;

		// search preset settings
		$search_design_config = EPKB_Core_Utilities::advanced_search_presets( $new_config, $orig_config, 'mp' );
		$search_design_settings = EPKB_Utilities::diff_two_dimentional_arrays( $search_design_config, $new_config );

		// user changes have higher priority if the changes applying after design preset is already applied in one of previous requests
		if ( ! empty( $search_design_settings ) ) {
			$new_config = $is_design_preset_change ? $search_design_config : array_merge( $search_design_config, $unmerged_new_config );
		}

		// Update the main page configuration
		self::update_module_position( $new_config );

		self::update_main_page( $kb_id, $orig_config, $new_config );

        // Send success response if update was successful
        wp_send_json_success( array( 'message' => esc_html__( 'Settings saved successfully', 'echo-knowledge-base' ) ) );
    }

	/**
	 * AJAX save all Article Page settings
	 * Handles nonce verification, user permissions check, data retrieval, and configuration update.
	 */
	public function save_article_page_settings() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_frontend_editor_write' );

		// if FE is opened then in Settings UI do not show legacy settings UI
		$is_legacy_settings = EPKB_Core_Utilities::is_kb_flag_set( 'is_legacy_settings' );
		if ( $is_legacy_settings ) {
			EPKB_Core_Utilities::remove_kb_flag( 'is_legacy_settings' );
		}

		$config = self::merge_new_and_old_kb_config( false );
		$orig_config = $config['orig_config'];
		$new_config = $config['new_config'];
		$unmerged_new_config = $config['unmerged_new_config'];
		$kb_id = $config['kb_id'];

		// Check if the user has permission to save settings
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid Knowledge Base ID', 'echo-knowledge-base' ) ) );
		}

		// after the design preset applied, it is reset to 'current' to avoid continuing applying and enable further settings change - use this to distinct request for design change and request for settings change after applying design preset
		$is_design_preset_change = $new_config['advanced_search_ap_presets'] != 'current';

		// design preset may change settings which are not present in the FE UI - apply full design settings + FE UI settings until user save settings
		$selected_search_preset = EPKB_Utilities::post( 'selected_search_preset', 'current' );

		// to apply preset add-on needs preset name in config
		$new_config['advanced_search_ap_presets'] = $selected_search_preset;

		// sync Article Page Search setting with Main Page Search settings
		$new_config = EPKB_Core_Utilities::sync_article_page_search_with_main_page_search( $new_config, $orig_config );

		// apply design settings only if sync toggle is 'off'
		if ( empty( $new_config['article_search_sync_toggle'] ) || $new_config['article_search_sync_toggle'] == 'off' ) {

			// search preset settings
			$search_design_config = EPKB_Core_Utilities::advanced_search_presets( $new_config, $orig_config, 'ap' );
			$search_design_diff = EPKB_Utilities::diff_two_dimentional_arrays( $search_design_config, $new_config );

			// user changes have higher priority if the changes applying after design preset is already applied in one of previous requests
			if ( ! empty( $search_design_diff ) ) {
				$new_config = $is_design_preset_change ? $search_design_config : array_merge( $search_design_config, $unmerged_new_config );
			}
		}

		// Update the article page configuration
		self::update_article_page( $kb_id, $orig_config, $new_config );

		// Send success response if update was successful
		wp_send_json_success( array( 'message' => esc_html__( 'Settings saved successfully', 'echo-knowledge-base' ) ) );
	}

	/**
	 * Save KB Article Page configuration
	 *
	 * @param $editor_kb_id
	 * @param $orig_config
	 * @param $new_config
	 */
	private static function update_article_page( $editor_kb_id, $orig_config, $new_config ) {

		// if user selected a theme preset for search then apply it
		if ( ! empty( $new_config['advanced_search_ap_presets'] ) && $new_config['advanced_search_ap_presets'] != 'current' ) {
			$new_config = EPKB_Core_Utilities::advanced_search_presets( $new_config, $orig_config, 'ap' );
		}

		// detect user changed kb template
		if ( $orig_config['templates_for_kb'] != $new_config['templates_for_kb'] ) {
			$new_config['article_content_enable_article_title'] = $new_config['templates_for_kb'] == 'current_theme_templates' ? 'off' : 'on';
		}

		// process sidebar priority settings
		$new_config['article_sidebar_component_priority'] = EPKB_KB_Config_Controller::convert_ui_data_to_article_sidebar_component_priority( $new_config );
		$new_config = EPKB_Core_Utilities::update_article_sidebar_priority( $orig_config, $new_config );

		EPKB_Core_Utilities::start_update_kb_configuration( $editor_kb_id, $new_config );
	}

	/**
	 * AJAX save all Archive Page settings
	 * Handles nonce verification, user permissions check, data retrieval, and configuration update.
	 */
	public function save_archive_page_settings() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_frontend_editor_write' );

		// if FE is opened then in Settings UI do not show legacy settings UI
		$is_legacy_settings = EPKB_Core_Utilities::is_kb_flag_set( 'is_legacy_settings' );
		if ( $is_legacy_settings ) {
			EPKB_Core_Utilities::remove_kb_flag( 'is_legacy_settings' );
		}

		$config = self::merge_new_and_old_kb_config( false );
		$orig_config = $config['orig_config'];
		$new_config = $config['new_config'];
		$kb_id = $config['kb_id'];

		// Check if the user has permission to save settings
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid Knowledge Base ID', 'echo-knowledge-base' ) ) );
		}

		// if user selected a theme preset for search then apply it
		if ( ! empty( $new_config['advanced_search_cp_presets'] ) && $new_config['advanced_search_cp_presets'] != 'current' ) {
			$new_config = EPKB_Core_Utilities::advanced_search_presets( $new_config, $orig_config, 'cp' );
		}

		// no need to detect kb template change for archive pages as they don't use templates

		EPKB_Core_Utilities::start_update_kb_configuration( $kb_id, $new_config );

		// Send success response if update was successful
		wp_send_json_success( array( 'message' => esc_html__( 'Settings saved successfully', 'echo-knowledge-base' ) ) );
	}

	public function closed_fe_editor() {
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( 'admin_eckb_access_frontend_editor_write' );
		EPKB_Core_Utilities::add_kb_flag( 'epkb_fe_editor_closed' );
		wp_send_json_success();
	}

	private static function merge_new_and_old_kb_config( $merge_module_position=true, $page_reload=false ) {

		// use cache
		static $cached_kb_config = null;
		if ( ! empty( $cached_kb_config ) ) {
			return $cached_kb_config;
		}

		$kb_id = EPKB_Utilities::post( 'kb_id', 0 );
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ){
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 801 ) );
		}

		$value_type = $page_reload ? 'db-config-json' : 'db-config';
		$new_config = EPKB_Utilities::post( 'new_kb_config', [], $value_type );

		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 8 ) );
		}
		$orig_config = EPKB_Core_Utilities::get_add_ons_config( $kb_id, $orig_config );
		if ( $orig_config === false ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 149 ) );
		}

		if ( $merge_module_position ) {
			$new_config = self::update_module_position( $new_config );
		}

		$unmerged_new_config = $new_config;

		$new_config = array_merge( $orig_config, $new_config );

		$cached_kb_config = [ 'kb_id' => $kb_id, 'orig_config' => $orig_config, 'new_config' => $new_config, 'unmerged_new_config' => $unmerged_new_config ];

		return $cached_kb_config;
	}

	/**
	 * Save KB Main Page configuration
	 *
	 * @param $editor_kb_id
	 * @param $orig_config
	 * @param $new_config
	 */
	private static function update_main_page( $editor_kb_id, $orig_config, $new_config ) {

		$chosen_preset = empty( $new_config['theme_presets'] ) || $new_config['theme_presets'] == 'current' ? '' : $new_config['theme_presets'];

		// if user selected a theme presets then Copy search setting from main to article and update icons
		if ( ! empty( $chosen_preset ) ) {
			$new_config['theme_name'] = $chosen_preset;
			$new_config = EPKB_KB_Wizard_Themes::copy_search_mp_to_ap( $new_config );
			EPKB_Core_Utilities::get_or_update_new_category_icons( $new_config, $chosen_preset, true );
		}

		// detect user changed kb template
		if ( $orig_config['templates_for_kb'] != $new_config['templates_for_kb'] ) {
			$new_config['article_content_enable_article_title'] = $new_config['templates_for_kb'] == 'current_theme_templates' ? 'off' : 'on';
		}

		EPKB_Core_Utilities::start_update_kb_configuration( $editor_kb_id, $new_config, false, $orig_config );
	}

	private static function update_module_position( $new_config ) {

		// ensure at least one module is set
		$module_counter = 0;
		foreach ( self::$modules as $module ) {

			// for unavailable module the module position and the rest of settings are missing
			if ( empty( $new_config[ $module . '_module_position' ] ) ) {
				continue;
			}

			if ( $new_config[ $module . '_module_position' ] == 'none' ) {
				$module_counter++;
			}
		}

		if ( $module_counter == 0 ) {
			return $new_config;
		}

		// reset original module positions
		for ( $row_index = 1; $row_index <= EPKB_Modular_Main_Page::MAX_ROWS; $row_index++ ) {
			$new_config[ 'ml_row_' . $row_index . '_module' ] = 'none';
		}

		// update new module positions
		foreach ( self::$modules as $module ) {

			// for unavailable module the module position and the rest of settings are missing
			if ( ! isset( $new_config[ $module . '_module_position' ] ) ) {
				continue;
			}

			$new_module_row_number = $new_config[ $module . '_module_position' ];
			if ( $new_module_row_number == 'none' ) {
				continue;
			}

			$new_config[ 'ml_row_' . $new_module_row_number . '_module' ] = $module;
		}

		return $new_config;
	}

	private static function get_updated_settings_html( $new_config, $kb_page_type, $setting_name, $feature_name, $settings_row_number ) {

		$prev_link_css_id = EPKB_Utilities::post( 'prev_link_css_id' );

		$layout_settings_html = '';
		$layout_settings_html_temp = '';

		$module_row_number = $settings_row_number == 'none' ? 1 : $settings_row_number;
		$all_main_page_features = self::$modules;
		if ( in_array( $feature_name, $all_main_page_features ) ) {
			$new_config['ml_row_' . $module_row_number . '_module'] = $feature_name;
		}

		$config_page = new EPKB_Config_Settings_Page( $new_config, true );
		$features_config = $config_page->get_vertical_tabs_config();

		if ( $kb_page_type == 'main-page' ) {
			$current_css_file_slug = self::get_current_css_slug( $new_config );

			// only on layout switch
			if ( $setting_name == 'kb_main_page_layout' && 'epkb-' . $current_css_file_slug . '-css' != $prev_link_css_id ) {
				// shared settings for all layouts are assigned to the first feature container (required by inherited logic from Settings UI)
				$layout_settings_html_temp = self::display_feature_settings( $features_config['main-page']['sub_tabs'][0]['contents'], true );

				$layout_settings_html = self::get_module_position_field( $feature_name, $new_config['categories_articles_module_position'] );
				$layout_settings_html .= self::display_feature_settings( $features_config['main-page']['sub_tabs'][ $module_row_number - 1 ]['contents'], true );
			}
		}

		return array(
			'layout_settings_html' => $layout_settings_html,
			'layout_settings_html_temp' => $layout_settings_html_temp,
		);
	}

	private static function get_inline_styles( $new_config, $kb_page_type ) {

		$prev_link_css_id = EPKB_Utilities::post( 'prev_link_css_id' );

		$link_css = '';
		$link_css_rtl = '';
		$elay_link_css = '';
		$current_css_file_slug = '';

		switch ( $kb_page_type ) {

			case 'main-page':

				$current_css_file_slug = self::get_current_css_slug( $new_config );

				// get CSS file accordingly to the current slug if layout change detected
				if ( 'epkb-' . $current_css_file_slug . '-css' != $prev_link_css_id ) {

					// apply the modules position here to have the inline CSS updated properly (since the inline CSS rendering relying on ml_row_{n}_module settings)
					$new_config = self::update_module_position( $new_config );

					$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

					$elay_link_css = EPKB_Core_Utilities::initialize_elegant_layouts( $new_config, $suffix );

					$link_css = '<link rel="stylesheet" id="epkb-' . $current_css_file_slug . '-css" href="' . Echo_Knowledge_Base::$plugin_url . 'css/' . $current_css_file_slug . $suffix . '.css?ver=' . Echo_Knowledge_Base::$version . '" media="all">';
					if ( is_rtl() ) {
						$link_css_rtl = '<link rel="stylesheet" id="epkb-' . $current_css_file_slug . '-rtl-css" href="' . Echo_Knowledge_Base::$plugin_url . 'css/' . $current_css_file_slug . '-rtl' . $suffix . '.css?ver=' . Echo_Knowledge_Base::$version . '" media="all">';
					}
				}
				break;

			case 'article-page':
				$current_css_file_slug = 'ap-frontend-layout';
				break;

			case 'archive-page':
				$current_css_file_slug = 'cp-frontend-layout';
				break;

			default:
				break;
		}

		// get updated inline styles
		$inline_styles = epkb_frontend_kb_theme_styles_now( $new_config, $current_css_file_slug );

		// For archive pages, also get the archive-specific inline styles
		if ( $kb_page_type === 'archive-page' ) {
			$inline_styles .= EPKB_Category_Archive_Setup::get_all_inline_styles( $new_config );
		}

		return array(
			'inline_styles'	=> EPKB_Utilities::minify_css( $inline_styles ),
			'link_css'		=> $link_css,
			'link_css_rtl'	=> $link_css_rtl,
			'elay_link_css'		=> empty( $elay_link_css['elay_link_css'] ) ? '' : $elay_link_css['elay_link_css'],
			'elay_link_css_rtl'	=> empty( $elay_link_css['elay_link_css_rtl'] ) ? '' : $elay_link_css['elay_link_css_rtl']
		);
	}

	/**
	 * Icons for the FE first page
	 * @param $feature_name
	 * @return string
	 */
	private static function get_features_icon_escaped( $feature_name ) {  
		switch ( $feature_name ) {
			case 'article-page-search-box':
			case 'search':
				return 'epkbfa epkbfa-search';
			case 'categories_articles':
				return 'epkbfa epkbfa-folder-open';
			case 'articles_list':
				return 'epkbfa epkbfa-list';
			case 'faqs':
				return 'epkbfa epkbfa-question-circle';
			case 'resource_links':
				return 'epkbfa epkbfa-link';
			case 'article-page-settings':
				return 'epkbfa epkbfa-cogs';
			case 'article-page-sidebar':
				return 'epkbfa epkbfa-th-list';
			case 'article-page-toc':
				return 'epkbfa epkbfa-list-ol';
			case 'article-page-ratings':
				return 'epkbfa epkbfa-star';
			case 'archive-page-settings':
				return 'epkbfa epkbfa-archive';
			default:
				return 'epkbfa epkbfa-circle-o';
		}
	}

	private static function get_module_row_number( $module_name, $kb_config ) {
		for ( $i = 1; $i <= EPKB_Modular_Main_Page::MAX_ROWS; $i++ ) {
			if ( $kb_config['ml_row_' . $i . '_module'] == $module_name ) {
				return $i;
			}
		}
		return 'none';
	}

	private static function get_module_position_field( $module_name, $module_position ) {

		$output = self::display_feature_settings( array( array(
			'title' => __( 'Enable Feature', 'echo-knowledge-base' ),
			'body_html' => EPKB_HTML_Elements::checkbox_toggle( array(
				'checked' => $module_position == 'none',
				'name' => $module_name,
				'input_group_class' => 'epkb-row-module-position epkb-row-module-position--' . $module_name,
				'return_html' => true,
				'group_data' => array(
					'module' => $module_name,
				),
			) ). EPKB_HTML_Elements::radio_buttons_horizontal( array(
				'name' => $module_name . '_module_position',
				'value' => '',
				'options' => [ 'move-up' => __( 'Move Up', 'echo-knowledge-base' ) . ' <span class="epkbfa epkbfa-arrow-up"></span>', 'move-down' => __( 'Move Down', 'echo-knowledge-base' ) . ' <span class="epkbfa epkbfa-arrow-down"></span>' ],
				'input_group_class' => 'epkb-row-module-position epkb-row-module-position--' . $module_name,
				'return_html' => true,
				'group_data' => array(
					'module' => $module_name,
				),
			) ),
			'css_class' => 'epkb-fe__settings-section--module-position' ),
		), true );

		return $output;
	}

	private static function get_current_css_slug( $kb_config ) {
		switch ( $kb_config['kb_main_page_layout'] ) {
			case 'Tabs': return 'mp-frontend-modular-tab-layout';
			case 'Categories': return 'mp-frontend-modular-category-layout';
			case 'Grid': return EPKB_Utilities::is_elegant_layouts_enabled() ? 'mp-frontend-modular-grid-layout' : 'mp-frontend-modular-basic-layout';
			case 'Sidebar': return EPKB_Utilities::is_elegant_layouts_enabled() ? 'mp-frontend-modular-sidebar-layout' : 'mp-frontend-modular-basic-layout';
			case 'Classic': return 'mp-frontend-modular-classic-layout';
			case 'Drill-Down': return 'mp-frontend-modular-drill-down-layout';
			case 'Basic':
			default: return 'mp-frontend-modular-basic-layout';
		}
	}
} 