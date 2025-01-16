<?php

/**
 * AI Help Sidebar
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_AI_Help_Sidebar {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', 'epkb_load_admin_ai_help_sidebar_resources' );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_button' ), 1000  );
		add_action( 'admin_footer', array( $this, 'display_ai_help_sidebar' ) );
		add_action( 'add_meta_boxes', array( $this, 'show_kb_ai_help_meta_box' ) );
	}

	/**
	 * Add AI Help Sidebar button in the WordPress admin bar
	 * Fired by `admin_bar_menu` filter
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public static function add_admin_bar_button( WP_Admin_Bar $wp_admin_bar ) {
		global $pagenow;

		// render AI Help Sidebar only if the current user has access to it
		$required_capability = EPKB_Admin_UI_Access::get_contributor_capability();
		if ( ! current_user_can( $required_capability ) ) {
			return;
		}

		// detect kb article page
		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			return;
		}

		if ( $pagenow != 'post-new.php' && $pagenow != 'post.php' ) {
			return;
		}

		if ( EPKB_Core_Utilities::is_kb_flag_set( 'disable_openai' ) ) {
			return;
		}

		$wp_admin_bar->add_menu( array( 'id' => 'epkb-ai-help-sidebar-button', 'title' => esc_html__( 'AI Help', 'echo-knowledge-base' ), 'href' => '#' ) );
	}

	/**
	 * Display AI Help Sidebar
	 */
	public static function display_ai_help_sidebar() {

		// render AI Help Sidebar only if the current user has access to it
		$required_capability = EPKB_Admin_UI_Access::get_contributor_capability();
		if ( ! current_user_can( $required_capability ) ) {
			return;
		}

		if ( EPKB_Core_Utilities::is_kb_flag_set( 'disable_openai' ) ) {
			return;
		}

		$openai_api_key = EPKB_OpenAI::get_openai_api_key();
		$openai_settings_capability = EPKB_Admin_UI_Access::get_admin_capability(); ?>

		<!-- AI Help Sidebar -->
		<div class="epkb-ai-help-sidebar" data-active-tab="helper-functions" data-back-btn="hide" data-apikey-state="<?php echo empty( $openai_api_key ) ? 'off' : 'on'; ?>">
			<div class="epkb-ai-help-sidebar__wrap">

				<!-- Header -->
				<div class="epkb-ai-help-sidebar__header">
					<div class="epkb-ai-help-sidebar__header-title">
						<span><?php esc_html_e( 'AI Help', 'echo-knowledge-base' ); ?></span>
						<span class="epkb__feature-experimental-tag"><?php esc_html_e( 'Beta', 'echo-knowledge-base' ); ?></span>
					</div>
					<div class="epkb-close-notice epkbfa epkbfa-window-close epkb-ai-help-sidebar-btn-close"></div>
				</div>

				<!-- Navigation -->
				<div class="epkb-ai-help-sidebar__nav">
					<div class="epkb-ai-help-sidebar__nav-back-btn">
						<div class="epkb-ai-help-sidebar__nav-back-btn__icon epkbfa epkbfa-arrow-left"></div>
						<div class="epkb-ai-help-sidebar__nav-back-btn__text"><?php esc_html_e( 'Back', 'echo-knowledge-base' ); ?></div>
					</div>
					<div class="epkb-ai-help-sidebar__nav-link epkb-ai-help-sidebar__nav-link--active" data-target="helper-functions"><?php esc_html_e( 'Helper Functions', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-ai-help-sidebar__nav-link" data-target="ai"><?php esc_html_e( 'AI', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-ai-help-sidebar__nav-link" data-target="resources"><?php esc_html_e( 'Resources', 'echo-knowledge-base' ); ?></div>    <?php

					// limit access to AI settings tab
					if ( current_user_can( $openai_settings_capability ) ) {   ?>
						<div class="epkb-ai-help-sidebar__nav-link" data-target="settings"><?php esc_html_e( 'Settings', 'echo-knowledge-base' ); ?></div>  <?php
					}   ?>
				</div>

				<!-- Body: Helper Functions -->
				<div class="epkb-ai-help-sidebar__body epkb-ai-help-sidebar__body-helper-functions epkb-ai-help-sidebar__body--active">    <?php

					self::show_helper_functions_main_screen();
					self::show_fix_spelling_and_grammar_screen();
					self::show_generate_article_outline_screen(); ?>

				</div>

				<!-- Body: AI -->
				<div class="epkb-ai-help-sidebar__body epkb-ai-help-sidebar__body-ai">    <?php
					self::show_ai_screen();    ?>
				</div>

				<!-- Body: Resources -->
				<div class="epkb-ai-help-sidebar__body epkb-ai-help-sidebar__body-resources">    <?php
					self::show_resources_screen();   ?>
				</div>  <?php

				// limit access to AI settings tab
				if ( current_user_can( $openai_settings_capability ) ) { ?>
					<!-- Body: Settings -->
					<div class="epkb-ai-help-sidebar__body epkb-ai-help-sidebar__body-settings">    <?php
						self::show_settings_screen();    ?>
					</div>  <?php
				}   ?>

				<!-- Body: Settings -->
				<div class="epkb-ai-help-sidebar__body epkb-ai-help-sidebar__body-feedback">    <?php
					self::show_feedback_screen();   ?>
				</div>

				<!-- Footer -->
				<div class="epkb-ai-help-sidebar__footer">
					<a href="#" target="_blank" class="epkb-ai__open-feedback-btn" data-target="feedback"><?php esc_html_e( 'Send us Feedback', 'echo-knowledge-base' ); ?></a>
					<span><a href="<?php echo esc_url( 'https://www.echoknowledgebase.com/' ); ?>" target="_blank"> <?php esc_html_e( 'Part of Echo Knowledge base', 'echo-knowledge-base' ); ?></a></span>
					<a href="<?php echo esc_url( 'https://www.echoknowledgebase.com/contact-us/' ); ?>" target="_blank"><?php esc_html_e( 'Need Help?', 'echo-knowledge-base' ); ?></a>
				</div>

				<!-- Bottom Message Container -->
				<div class="epkb-ai-help-sidebar__bottom-notice-message-container"></div>

			</div>
		</div>  <?php
	}

	/**
	 * Helper Functions: main screen
	 */
	private static function show_helper_functions_main_screen() { ?>

		<!-- Helper Functions Main Screen -->
		<div class="epkb-ai-help-sidebar__main">    <?php
			self::display_main_intro(); ?>
			<div class="epkb-ai-help-sidebar__actions">
				<div class="epkb-ai-help-sidebar__actions-left-col">
					<div class="epkb-ai-help-sidebar__actions-title"><?php esc_html_e( 'Content Helper', 'echo-knowledge-base' ); ?><p><?php esc_html_e( 'How can AI help you to write your content?', 'echo-knowledge-base' ); ?></p></div>   <?php
					EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Improve Text', 'echo-knowledge-base' ), 'epkb_fix_spelling_and_grammar', 'epkb-ai-help-sidebar__action-wrap', '', false, false, 'epkb-ai__fix-improve-text-btn' );
					EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Generate Article Outline', 'echo-knowledge-base' ), 'epkb_ai_generate_article_outline', 'epkb-ai-help-sidebar__action-wrap', '', false, false, 'epkb-ai__generate-article-outline-btn' );							?>
				</div>
			</div>
		</div><?php
	}

	/**
	 * Helper Functions: Improve Text screen
	 */
	private static function show_fix_spelling_and_grammar_screen() {    ?>

		<!-- Screen for Improve Text -->
		<div class="epkb-ai-help-sidebar__improve-text">
			<div class="epkb-ai-help-sidebar-select-text-title"><?php esc_html_e( 'Select Text', 'echo-knowledge-base' ); ?></div>
			<div class="epkb-ai-help-sidebar__screen-title">
				<div class="epkb-ai-help-sidebar__screen-title-text"><?php esc_html_e( 'Improve Text', 'echo-knowledge-base' ); ?></div>
				<div class="epkb-ai-help-sidebar__screen-title-subtext"><?php esc_html_e( 'To change text, select the desired portion. To change all text, don\'t select anything.', 'echo-knowledge-base' ); ?></div>  <?php
				self::display_tokens_used_html();   ?>
			</div>

			<div class="epkb-ai-help-sidebar__improve-text-input">

				<div class="epkb-ai-help-sidebar__improve-text-selected-text-container">
					<div class="epkb-ai-help-sidebar__improve-text-toolbar">    <?php
						EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Improve Readability', 'echo-knowledge-base' ), 'epkb_ai_improve_readability', 'epkbfa epkbfa-eye epkb-ai-help-sidebar__improve-readability-wrap', '', false );
						EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Re-phrase', 'echo-knowledge-base' ), 'epkb_airephrase', 'epkbfa epkbfa-refresh epkb-ai-help-sidebar__re-phrase-wrap', '', false );
						EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Fix Spelling / Grammar', 'echo-knowledge-base' ), 'epkb_ai_fix_spelling_and_grammar', 'epkbfa epkbfa-scissors epkb-ai-help-sidebar__fix-spelling-wrap', '', false );
						EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Copy', 'echo-knowledge-base' ), 'epkb_ai_copy', 'epkbfa epkbfa-file epkb-ai-help-sidebar__copy-wrap', '', false );  ?>
					</div>
					<div class="epkb-ai-help-sidebar__improve-text-input__textarea-wrap">
						<div class="epkb-ai-help-sidebar__improve-text-input__textarea" contenteditable="true" placeholder="<?php esc_html_e( 'No text selected', 'echo-knowledge-base' ); ?>"></div>
					</div>
				</div>

			</div>

		</div>  <?php
	}

	/**
	 * Helper Functions: Generate Article Outline
	 */
	private static function show_generate_article_outline_screen() {    ?>

		<!-- Screen for generate article outline -->
		<div class="epkb-ai-help-sidebar__article-outline">
			<div class="epkb-ai-help-sidebar__screen-title">
				<div class="epkb-ai-help-sidebar__screen-title-text"><?php esc_html_e( 'Generate Article Outline', 'echo-knowledge-base' ); ?></div>    <?php
				self::display_tokens_used_html();   ?>
			</div>
			<div class="epkb-ai-help-sidebar__article-outline-input-container">
				<div class="epkb-ai-help-sidebar__settings-form">
					<div class="epkb-ai-help-sidebar__settings-group">  <?php
						EPKB_HTML_Elements::text( [
							'label'         => esc_html__( 'Article title', 'echo-knowledge-base' ),
							'name'          => 'epkb_ai_article_title',
							'max'           => '100',
							'min'           => '0',
							'default'       => '',
							'value'         => '',
							'tooltip_body'  => 'Enter article title.',
							'tooltip_args'  => [ 'open-icon' => 'info' ],
						] );    ?>
					</div>
				</div>
				<div class="epkb-ai-help-sidebar__article-outline-generate"><?php
					EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Generate Outline', 'echo-knowledge-base' ), 'epkb_ai_generate_article_outline_button', 'epkb-ai-help-sidebar__generate-outline-wrap', '', false ); ?>
				</div>
			</div>
			<div class="epkb-ai-help-sidebar__article-outline-results-container">

				<div class="epkb-ai-help-sidebar__article-outline-selected-text-container">
					<div class="epkb-ai-help-sidebar__article-outline-result__textarea-wrap">
						<div class="epkb-ai-help-sidebar__article-outline-result__textarea" contenteditable="true" placeholder="<?php esc_html_e( 'Enter the title here', 'echo-knowledge-base' ); ?>"></div>
					</div>
				</div>
			</div>
		</div><?php
	}

	/**
	 * Chat AI
	 */
	private static function show_ai_screen() {    ?>

		<!-- Screen for Chat AI -->
		<div class="epkb-ai-help-sidebar__ai">
			<div class="epkb-ai-help-sidebar__screen-title">
				<div class="epkb-ai-help-sidebar__screen-title-text"><?php esc_html_e( 'Chat AI', 'echo-knowledge-base' ); ?></div>
			</div>

			<div class="epkb-ai-help-sidebar__ai-response-wrap">
				<div class="epkb-ai-help-sidebar__ai-response-title">
					<div class="epkb-ai-help-sidebar__ai-response-title-text"><?php esc_html_e( 'AI Response', 'echo-knowledge-base' ); ?></div>   <?php
					self::display_tokens_used_html();   ?>
				</div>
				<div class="epkb-ai-help-sidebar__ai-response-container"></div>
			</div>

			<div class="epkb-ai-help-sidebar__ai-input-wrap">
				<div class="epkb-ai-help-sidebar__ai-input-title">
					<div class="epkb-ai-help-sidebar__ai-input-title-text"><?php esc_html_e( 'Enter your request to Chat AI', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-ai-help-sidebar__ai-input-container">
					<input class="epkb-ai-help-sidebar__ai-input" type="text" value="" placeholder="">
				</div>
			</div>
		</div>  <?php
	}

	/**
	 * Resources Tab
	 */
	private static function show_settings_screen() {    ?>

        <!-- Screen for Settings -->
        <div class="epkb-ai-help-sidebar__settings">
            <div class="epkb-ai-help-sidebar__screen-title">
                <div class="epkb-ai-help-sidebar__screen-title-text"><?php esc_html_e( 'Settings', 'echo-knowledge-base' ); ?></div>
            </div>

            <div class="epkb-ai-help-sidebar__settings-form">

                <!-- OpenAI API Key -->
                <div class="epkb-ai-help-sidebar__settings-group">  <?php
	                $api_key = EPKB_OpenAI::get_openai_api_key();
					EPKB_HTML_Elements::text( [
						'label'         => esc_html__( 'OpenAI API Key', 'echo-knowledge-base' ),
						'name'          => 'openai_api_key',
						'max'           => '500',
						'min'           => '0',
						'default'       => '',
						'value'         =>  empty( $api_key ) ? '' : substr( $api_key, 0, 2 ) . '...' . substr( $api_key, -4 ),
						'tooltip_body'  => esc_html__( 'Enter your OpenAI API key.', 'echo-knowledge-base' ) . ' <a href="https://beta.openai.com/account/api-keys" target="_blank" rel="noopener">' . esc_html__( 'Get OpenAI API Key', 'echo-knowledge-base' ) . '</a>',
						'tooltip_args'  => [ 'open-icon' => 'info' ],
					] );    ?>
                </div>

                <!-- Access to AI Help -->
                <div class="epkb-ai-help-sidebar__settings-group"> <?php
					EPKB_HTML_Elements::checkbox_toggle( [
						'id'            => 'disable_openai',
						'name'          => 'disable_openai',
						'text'          => esc_html__( 'Disable AI Help', 'echo-knowledge-base' ),
						'textLoc'       => 'left',
						'checked'       => EPKB_Core_Utilities::is_kb_flag_set( 'disable_openai' ),
					] ) ?>
                </div>

                <!-- Locations -->
                <div class="epkb-ai-help-sidebar__settings-group">

                </div>

            </div>

            <!-- Save Settings -->
            <div class="epkb-ai-help-sidebar__settings-save">
                <button class="epkb-ai-help-sidebar__settings-save-btn"><?php esc_html_e( 'Save', 'echo-knowledge-base' ); ?></button>
            </div>

        </div>  <?php
	}
	/**
	 * Settings
	 */
	private static function show_resources_screen() {    ?>

		<!-- Screen for Settings -->
		<div class="epkb-ai-help-sidebar__resources">

			<div class="epkb-ai-help-sidebar__screen-title">
				<div class="epkb-ai-help-sidebar__screen-title-text"><?php esc_html_e( 'Resources', 'echo-knowledge-base' ); ?></div>
			</div>

            <div class="epkb-ai-help-sidebar__resources-container">

                <div class="epkb-ai-help-sidebar__resources-full-row">
                    <p><?php esc_html_e( 'This is a compilation of materials that can assist you in understanding OpenAI, including setup procedures and essential knowledge you need to acquire. ' .
                                         'The utilization of artificial intelligence will revolutionize your writing practices.', 'echo-knowledge-base' ); ?></p>
                </div>

            </div>

			<div class="epkb-ai-help-sidebar__resources-container">

                <div class="epkb-ai-help-sidebar__resources-left-col">
                    <h3><?php esc_html_e( 'Knowledge Base Documentation', 'echo-knowledge-base' ); ?></h3>
                    <ul>
                        <li><a href="<?php echo esc_url('https://www.echoknowledgebase.com/documentation/ai-help-sidebar/'); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'Overview', 'echo-knowledge-base' ); ?></a></li>
                        <li><a href="<?php echo esc_url('https://www.echoknowledgebase.com/documentation/how-to-get-an-open-ai-key/'); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'How to get an OpenAI Key?', 'echo-knowledge-base' ); ?></a></li>
                        <li><a href="<?php echo esc_url('https://www.echoknowledgebase.com/documentation/improve-text'); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'Improve Text Function', 'echo-knowledge-base' ); ?></a></li>
                        <li><a href="<?php echo esc_url('https://www.echoknowledgebase.com/documentation/generate-article-outline/'); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'Generate Article Outline Function', 'echo-knowledge-base' ); ?></a></li>
                    </ul>
                </div>

                <div class="epkb-ai-help-sidebar__resources-right-col">
                    <h3><?php esc_html_e( 'OpenAI Information', 'echo-knowledge-base' ); ?></h3>
                    <ul>
                        <li><a href="<?php echo esc_url('https://platform.openai.com/docs/introduction'); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'Introduction', 'echo-knowledge-base' ); ?></a></li>
                        <li><a href="<?php echo esc_url('https://openai.com/safety'); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'Artificial general intelligence Safety', 'echo-knowledge-base' ); ?></a></li>
                        <li><a href="<?php echo esc_url('https://openai.com/blog'); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'OpenAI Blog', 'echo-knowledge-base' ); ?></a></li>
                    </ul>
                </div>

			</div>

            <div class="epkb-ai-help-sidebar__resources-container">

                <div class="epkb-ai-help-sidebar__resources-left-col">
                    <h3><?php esc_html_e( 'OpenAI Policies', 'echo-knowledge-base' ); ?></h3>
                    <ul>
                        <li><a href="<?php echo esc_url('https://openai.com/policies/usage-policies'); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'OpenAI Usage policies', 'echo-knowledge-base' ); ?></a></li>
                        <li><a href="<?php echo esc_url('https://openai.com/policies/sharing-publication-policy'); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'Sharing & publication policy', 'echo-knowledge-base' ); ?></a></li>
                        <li><a href="<?php echo esc_url('https://platform.openai.com/docs/guides/safety-best-practices'); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'Safety best practices', 'echo-knowledge-base' ); ?></a></li>
                        <li><a href="<?php echo esc_url('https://openai.com/policies/privacy-policy'); ?>" target="_blank" rel="nofollow"><?php esc_html_e( 'Privacy policy', 'echo-knowledge-base' ); ?></a></li>
                    </ul>
                </div>
            </div>

		</div>  <?php
	}

	/**
	 * Chat AI
	 */
	private static function show_feedback_screen() {    ?>

		<!-- Screen for Chat AI -->
		<div class="epkb-ai-help-sidebar__feedback">
		<div class="epkb-ai-help-sidebar__screen-title">
			<div class="epkb-ai-help-sidebar__screen-title-text"><?php esc_html_e( 'Send us Feedback', 'echo-knowledge-base' ); ?></div>
		</div>

		<form class="epkb-ai-help-sidebar__feedback-form">
			<div class="epkb-ai-help-sidebar__feedback-input-wrap">
				<div class="epkb-ai-help-sidebar__feedback-input-title">
					<div class="epkb-ai-help-sidebar__feedback-input-title-text"><?php esc_html_e( 'Name', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-ai-help-sidebar__feedback-input-container">
					<input class="epkb-ai-help-sidebar__feedback-input" type="text" value="" placeholder="" name="feedback_name">
				</div>
			</div>
			<div class="epkb-ai-help-sidebar__feedback-input__textarea-wrap">
				<div class="epkb-ai-help-sidebar__feedback-input-title">
					<div class="epkb-ai-help-sidebar__feedback-input-title-text"><?php esc_html_e( 'Feedback', 'echo-knowledge-base' ); ?>
						<span class="epkb-ai-help-sidebar__input-required">*</span>
					</div>
				</div>
				<div class="epkb-ai-help-sidebar__feedback-input-container">
					<textarea class="epkb-ai-help-sidebar__feedback-input__textarea" name="feedback_text" required></textarea>
				</div>
			</div>
			<div class="epkb-ai-help-sidebar__feedback-input-wrap">
				<div class="epkb-ai-help-sidebar__feedback-input-title">
					<div class="epkb-ai-help-sidebar__feedback-input-title-text">
                        <?php esc_html_e( 'Email', 'echo-knowledge-base' ); ?>
                        <i><?php esc_html_e( '( Share your email to discuss your feedback. )', 'echo-knowledge-base' ); ?></i>
                    </div>
				</div>
				<div class="epkb-ai-help-sidebar__feedback-input-container">
					<input class="epkb-ai-help-sidebar__feedback-input" type="email" value="" placeholder="" name="feedback_email">
				</div>
			</div><?php
			EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Submit', 'echo-knowledge-base' ), 'epkb_ai_feedback', 'epkb-ai-help-sidebar__action-wrap', '', false ); ?>
		</form>
		</div><?php
	}

	/**
	 * Display KB AI Help meta box
	 */
	public function show_kb_ai_help_meta_box() {
		global $post, $pagenow;

		if ( empty( $post ) || ! $post instanceof WP_Post ) {
			return;
		}

		// ignore non-KB posts
		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( empty( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		if ( $pagenow != 'post-new.php' && $pagenow != 'post.php' ) {
			return;
		}

		if ( EPKB_Core_Utilities::is_kb_flag_set( 'disable_openai' ) ) {
			return;
		}

		add_meta_box( 'epkb_ai_help_meta_box', esc_html__( 'KB AI Help', 'echo-knowledge-base' ), array( $this, 'display_kb_ai_help_meta_box'), EPKB_KB_Handler::get_post_type( $kb_id ), 'side', 'high' );
	}

	/**
	 * Display HTML for KB AI Help meta box
	 */
	public function display_kb_ai_help_meta_box() {
		echo '<input type="button" id="epkb-ai-help-meta-box-button" value="' . esc_html__( 'AI Help', 'echo-knowledge-base' ) . '">';
	}

	/**
	 * Display HTML for Tokens Used
	 */
	private static function display_tokens_used_html() {    ?>
		<div class="epkb-ai-help-sidebar__screen-usage" style="display: none;">
			<div class="epkb-ai-help-sidebar__screen-usage-tokens"><?php esc_html_e( 'Tokens Used', 'echo-knowledge-base' ); ?>: <span></span></div>
		</div>  <?php
	}

	/**
	 * Display main intro
	 */
	private static function display_main_intro() {

		if ( EPKB_Core_Utilities::is_kb_flag_set( 'ai_dismiss_main_intro' ) ) {
			return;
		}

		EPKB_HTML_Forms::notification_box_middle(
		    array(
				'type' => 'warning',
		        'title' => esc_html__( 'AI Disclaimer and Warnings', 'echo-knowledge-base' ),
		        'desc' => sprintf(
		            wp_kses(
		                __( 'Please read the <a href="%s" target="_blank">AI Disclaimer and Warnings</a> before using the AI features.', 'echo-knowledge-base' ),
		                array(
		                    'a' => array(
		                        'href' => array(),
		                        'target' => array(),
		                    ),
		                )
		            ),
		            esc_url( 'https://www.echoknowledgebase.com/ai-disclaimer-and-warnings/' )
		        ),
		    )
		);		?>

		<div class="epkb-ai-help-sidebar__main-intro">
			<div class="epkb-ai-help-sidebar__main-intro-left">
				<?php esc_html_e( 'Welcome to AI-powered writing assistance for your content. Whether you need to generate new text or improve existing ones, ' .
                                        'we\'ve got you covered with the tools below.', 'echo-knowledge-base' ); ?>
				<br>
				<br>
                <div>
                    <?php echo wp_kses( 'We are currently in the <strong>beta development stage</strong>, with a plethora of additional features in the pipeline. ' .
                                'Your valuable feedback is highly appreciated and will enable us to incorporate your suggestions in our development roadmap. '.
                                'Kindly <a href="" id="ai-help-feedback-link" data-target="feedback"> click here</a> to share your insights and help us enhance our product.', array(
                        'p' => array(),
                        'strong' => array(),
                        'a' => array(
	                        'href' => array(),
	                        'title' => array(),
	                        'target' => array(),
							'id' => array(),
							'data-target' => array(),
                        ),
                    )); ?>
                </div>
				<div class="epkb-ai-help-sidebar__main-intro__links-title"><?php esc_html_e( 'Learn About Using AI (Based on OpenAI Technology)', 'echo-knowledge-base') . ':'; ?></div>
				<ul class="epkb-ai-help-sidebar__main-intro__links">
					<li><a href="https://platform.openai.com/docs/guides/production-best-practices" target="_blank" class="epkb-ai-help-sidebar__main-link"><?php
                            esc_html_e( 'AI Production Setup and Best Practices', 'echo-knowledge-base' ); ?></a></li>
					<li><a href="https://openai.com/api/pricing/" target="_blank" class="epkb-ai-help-sidebar__main-link"><?php
                            esc_html_e( 'Important: Using AI assistance will occur cost based on "Davinci" model and subject to your specific usage.', 'echo-knowledge-base' ); ?></a></li>
				</ul>
			</div>
			<div class="epkb-ai-help-sidebar__main-intro-right">
				<div class="epkb-ai-help-sidebar__main-intro-icon"></div>
			</div>
			<input type="button" class="epkb-ai-help-sidebar__main-intro__dismiss-btn" value="<?php esc_attr_e( 'Dismiss', 'echo-knowledge-base' ); ?>">
		</div>  <?php
	}
}