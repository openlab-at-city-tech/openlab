<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Quizzes admin page.
 */
class EPKB_Quizzes_Page {

	/**
	 * Register quiz submenu via eckb_add_kb_submenu hook.
	 *
	 * @param string $parent_slug
	 */
	public static function add_menu_item( $parent_slug ) {

		if ( ! EPKB_Quizzes_Utilities::is_feature_enabled() ) {
			return;
		}

		add_submenu_page(
			$parent_slug,
			esc_html__( 'Quizzes - Echo Knowledge Base', 'echo-knowledge-base' ),
			esc_html__( 'Quizzes', 'echo-knowledge-base' ),
			EPKB_Admin_UI_Access::get_context_required_capability( array( 'admin_eckb_access_quizzes_write' ) ),
			'epkb-quizzes',
			array( new self(), 'display_quizzes_page' )
		);
	}

	/**
	 * Display quizzes page.
	 */
	public function display_quizzes_page() {

		$admin_page_views = self::get_views_config();

		EPKB_HTML_Admin::admin_page_header(); ?>

		<div id="ekb-admin-page-wrap">
			<div id="epkb-kb-quizzes-page-container"> <?php
				EPKB_HTML_Admin::admin_header( array(), array(), 'logo' );
				EPKB_HTML_Admin::admin_primary_tabs( $admin_page_views );
				EPKB_HTML_Admin::admin_primary_tabs_content( $admin_page_views );
				echo self::interest_modal(); ?>
			</div>
		</div> <?php
	}

	/**
	 * Get views config.
	 *
	 * @return array
	 */
	private static function get_views_config() {
		return array(
			array(
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( array( 'admin_eckb_access_quizzes_write' ) ),
				'list_key'                    => 'quizzes-overview',
				'label_text'                  => esc_html__( 'Overview', 'echo-knowledge-base' ),
				'icon_class'                  => 'epkbfa epkbfa-home',
				'boxes_list'                  => array(
					array(
						'html' => self::overview_tab(),
					),
				),
			),
			array(
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( array( 'admin_eckb_access_quizzes_write' ) ),
				'list_key'                    => 'quizzes-editor',
				'label_text'                  => esc_html__( 'Add and Edit Quiz', 'echo-knowledge-base' ),
				'icon_class'                  => 'epkbfa epkbfa-check-square-o',
				'boxes_list'                  => array(
					array(
						'html' => self::quizzes_tab(),
					),
				),
			),
			array(
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_admin_capability(),
				'list_key'                    => 'quizzes-settings',
				'label_text'                  => esc_html__( 'Settings', 'echo-knowledge-base' ),
				'icon_class'                  => 'epkbfa epkbfa-cog',
				'boxes_list'                  => array(
					array(
						'html' => self::settings_tab(),
					),
				),
			),
		);
	}

	/**
	 * Overview tab HTML.
	 *
	 * @return string
	 */
	private static function overview_tab() {

		$is_feature_enabled = EPKB_Quizzes_Utilities::is_feature_enabled();
		$quizzes = $is_feature_enabled ? EPKB_Quizzes_Utilities::get_quizzes() : array();

		ob_start(); ?>

		<div class="epkb-admin-info-box">
			<div class="epkb-admin-info-box__header">
				<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-map-o"></div>
				<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'Quiz Workflow', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="epkb-admin-info-box__body">
				<ul>
					<li><?php esc_html_e( 'Each quiz is linked to exactly one KB article.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Create a draft manually or generate one from an article with AI when AI features are available.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Review and edit the title, intro, questions, answers, and explanations before publishing.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Published quizzes appear only on their source article page, below the article content.', 'echo-knowledge-base' ); ?></li>
				</ul>
				<?php self::display_demo_quiz_cta(); ?>
				<p>
					<button type="button" class="epkb-btn epkb-secondary-btn epkb-quiz-feedback-trigger"><?php esc_html_e( 'Suggest a New Quiz Feature', 'echo-knowledge-base' ); ?></button>
				</p>
			</div>
		</div>

		<div class="epkb-quizzes-admin"> <?php
			if ( ! $is_feature_enabled ) { ?>
				<div class="epkb-admin-info-box">
					<div class="epkb-admin-info-box__header">
						<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-info-circle"></div>
						<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'Quizzes are Disabled', 'echo-knowledge-base' ); ?></div>
					</div>
					<div class="epkb-admin-info-box__body">
						<p><?php esc_html_e( 'Turn on the Quizzes feature in the Settings tab to view the quiz library and edit quizzes here.', 'echo-knowledge-base' ); ?></p>
					</div>
				</div> <?php
			} else {
				self::display_quiz_library( $quizzes );
			} ?>
		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Add and edit quiz tab HTML.
	 *
	 * @return string
	 */
	private static function quizzes_tab() {

		$articles = EPKB_Quizzes_Utilities::get_selectable_articles();
		$generation_state = EPKB_Quizzes_Utilities::get_generation_state();
		$show_upgrade_link = ! $generation_state['is_available'] && $generation_state['reason'] === 'upgrade' && ! empty( $generation_state['link_url'] ) && ! empty( $generation_state['link_label'] );
		$is_feature_enabled = EPKB_Quizzes_Utilities::is_feature_enabled();

		ob_start();

		if ( ! $is_feature_enabled ) { ?>

				<div class="epkb-quizzes-admin" data-interest-submitted="<?php echo esc_attr( EPKB_Quizzes_Utilities::should_show_interest_modal() ? '0' : '1' ); ?>">
				<div class="epkb-admin-info-box">
					<div class="epkb-admin-info-box__header">
						<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-info-circle"></div>
						<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'Quizzes are Disabled', 'echo-knowledge-base' ); ?></div>
					</div>
					<div class="epkb-admin-info-box__body">
						<p><?php esc_html_e( 'Enable the Quizzes feature in the Settings tab to manage quizzes here.', 'echo-knowledge-base' ); ?></p>
						<?php self::display_demo_quiz_cta(); ?>
					</div>
				</div>
			</div> <?php

			return ob_get_clean();
		} ?>

		<div class="epkb-quizzes-admin" data-interest-submitted="<?php echo esc_attr( EPKB_Quizzes_Utilities::should_show_interest_modal() ? '0' : '1' ); ?>">
			<div class="epkb-quizzes-admin__editor-tab">
				<div class="epkb-quizzes-admin__editor">
					<form id="epkb-quiz-editor-form">
						<input type="hidden" name="quiz_id" id="epkb-quiz-id" value="0">

						<div class="epkb-quizzes-admin__editor-head">
							<div>
								<h3 id="epkb-quiz-editor-title"><?php esc_html_e( 'New Quiz', 'echo-knowledge-base' ); ?></h3>
								<p><?php esc_html_e( 'Create a new quiz or update one from the Quiz Library on the Overview tab.', 'echo-knowledge-base' ); ?></p>
							</div>
							<div class="epkb-quizzes-admin__editor-head-actions">
								<button type="button" class="epkb-btn epkb-success-btn epkb-quiz-create-trigger" hidden>
									<span class="epkbfa epkbfa-plus-circle"></span>
									<span><?php esc_html_e( 'Add Quiz', 'echo-knowledge-base' ); ?></span>
								</button>
								<div class="epkb-quizzes-admin__status" id="epkb-quiz-status-badge"><?php esc_html_e( 'Draft', 'echo-knowledge-base' ); ?></div>
								<a href="#" id="epkb-quiz-view-link" class="epkb-quiz-view-link" target="_blank" rel="noopener noreferrer" hidden>
									<span class="epkbfa epkbfa-external-link"></span>
									<?php esc_html_e( 'View Quiz', 'echo-knowledge-base' ); ?>
								</a>
							</div>
						</div>

						<?php self::display_demo_quiz_cta(); ?>

						<div id="epkb-quiz-editor-notice" class="epkb-quizzes-admin__notice" hidden></div>
						<div id="epkb-quiz-editor-warning" class="epkb-quizzes-admin__warning" hidden></div>

						<div class="epkb-quizzes-admin__field">
							<label for="epkb-quiz-title"><?php esc_html_e( 'Quiz Title', 'echo-knowledge-base' ); ?></label>
							<input type="text" id="epkb-quiz-title" name="quiz_title" maxlength="200" placeholder="<?php esc_attr_e( 'Enter quiz title...', 'echo-knowledge-base' ); ?>">
						</div>

						<div class="epkb-quizzes-admin__field-grid">
							<div class="epkb-quizzes-admin__field">
								<label for="epkb-quiz-source-article"><?php esc_html_e( 'Article Selection', 'echo-knowledge-base' ); ?></label>
								<select id="epkb-quiz-source-article" name="source_article_id">
									<option value="0"><?php esc_html_e( 'Select a source article', 'echo-knowledge-base' ); ?></option>
									<?php foreach ( $articles as $article ) { ?>
										<option value="<?php echo esc_attr( $article['id'] ); ?>"><?php echo esc_html( $article['label'] ); ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="epkb-quizzes-admin__field">
								<label for="epkb-quiz-question-count"><?php esc_html_e( 'Multiple Choices per Question', 'echo-knowledge-base' ); ?></label>
								<select id="epkb-quiz-question-count" name="question_count_mode">
									<option value="auto"><?php esc_html_e( 'Auto', 'echo-knowledge-base' ); ?></option>
									<?php for ( $count = 3; $count <= 10; $count++ ) { ?>
										<option value="<?php echo esc_attr( $count ); ?>"><?php echo esc_html( $count ); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="epkb-quizzes-admin__field">
							<label for="epkb_quiz_intro"><?php esc_html_e( 'Intro / Instructions', 'echo-knowledge-base' ); ?></label>
							<div class="epkb-quizzes-admin__editor-wrap">
								<?php wp_editor( '', 'epkb_quiz_intro', array(
									'media_buttons' => false,
									'textarea_rows' => 6,
								) ); ?>
							</div>
						</div>

						<div class="epkb-quizzes-admin__generate-box">
							<div class="epkb-quizzes-admin__generate-copy">
								<h4><?php esc_html_e( 'Generate Quiz', 'echo-knowledge-base' ); ?></h4>
								<p><?php esc_html_e( 'Create or replace quiz questions from the selected source article.', 'echo-knowledge-base' ); ?></p>
							</div>
								<div class="epkb-quizzes-admin__generate-actions">
									<?php if ( $show_upgrade_link ) { ?>
										<a href="<?php echo esc_url( $generation_state['link_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $generation_state['link_label'] ); ?></a>
									<?php } else { ?>
										<button type="button" id="epkb-quiz-generate" class="epkb-btn epkb-primary-btn" <?php disabled( ! $generation_state['is_available'] ); ?>>
											<span class="epkbfa epkbfa-magic"></span>
										<span><?php esc_html_e( 'Generate Quiz', 'echo-knowledge-base' ); ?></span>
									</button>
								<?php } ?>
							</div>
						</div>

						<?php if ( ! $generation_state['is_available'] && ! $show_upgrade_link ) { ?>
							<div class="epkb-quizzes-admin__generate-state epkb-quizzes-admin__generate-state--<?php echo esc_attr( $generation_state['reason'] ); ?>">
								<div class="epkb-quizzes-admin__generate-state-copy"><?php echo esc_html( $generation_state['message'] ); ?></div>
								<?php if ( ! empty( $generation_state['link_url'] ) && ! empty( $generation_state['link_label'] ) ) { ?>
									<a href="<?php echo esc_url( $generation_state['link_url'] ); ?>" class="epkb-btn epkb-secondary-btn" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $generation_state['link_label'] ); ?></a>
								<?php } ?>
							</div>
						<?php } ?>

						<div class="epkb-quizzes-admin__questions-head">
							<h4><?php esc_html_e( 'Questions', 'echo-knowledge-base' ); ?></h4>
							<button type="button" class="epkb-btn epkb-secondary-btn" id="epkb-quiz-add-question">
								<span class="epkbfa epkbfa-plus-circle"></span>
								<span><?php esc_html_e( 'Add Question', 'echo-knowledge-base' ); ?></span>
							</button>
						</div>

						<div id="epkb-quiz-questions" class="epkb-quiz-questions"></div>
						<div id="epkb-quiz-questions-empty" class="epkb-quiz-questions__empty"><?php esc_html_e( 'No questions yet. Add one manually or generate a quiz from the source article.', 'echo-knowledge-base' ); ?></div>

						<div class="epkb-quizzes-admin__footer">
							<button type="button" class="epkb-btn epkb-primary-btn" id="epkb-quiz-save-draft"><?php esc_html_e( 'Save Draft', 'echo-knowledge-base' ); ?></button>
							<button type="button" class="epkb-btn epkb-success-btn" id="epkb-quiz-publish"><?php esc_html_e( 'Publish', 'echo-knowledge-base' ); ?></button>
							<button type="button" class="epkb-btn epkb-error-btn" id="epkb-quiz-delete" disabled><?php esc_html_e( 'Delete', 'echo-knowledge-base' ); ?></button>
						</div>
					</form>
				</div>
			</div>

		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Shared quiz feedback modal.
	 *
	 * @return string
	 */
	private static function interest_modal() {

		ob_start(); ?>

		<div id="epkb-quiz-interest-modal" class="epkb-quiz-modal" hidden>
			<div class="epkb-quiz-modal__backdrop"></div>
			<div class="epkb-quiz-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="epkb-quiz-interest-title">
				<button type="button" class="epkb-quiz-modal__close" id="epkb-quiz-interest-close" aria-label="<?php esc_attr_e( 'Close', 'echo-knowledge-base' ); ?>">
					<span class="epkbfa epkbfa-times"></span>
				</button>
				<h3 id="epkb-quiz-interest-title"><?php esc_html_e( 'Quick Quiz Feedback', 'echo-knowledge-base' ); ?></h3>
				<p><?php esc_html_e( 'You are one of the first admins using quizzes. Tell us what would make this feature more useful for your team.', 'echo-knowledge-base' ); ?></p>
				<div class="epkb-quizzes-admin__field-grid">
					<div class="epkb-quizzes-admin__field">
						<label for="epkb-quiz-interest-first-name">
							<?php esc_html_e( 'First Name', 'echo-knowledge-base' ); ?>
							<span class="epkb-quizzes-admin__label-tag"><?php esc_html_e( 'Optional', 'echo-knowledge-base' ); ?></span>
						</label>
						<input type="text" id="epkb-quiz-interest-first-name" maxlength="100">
					</div>
					<div class="epkb-quizzes-admin__field">
						<label for="epkb-quiz-interest-email">
							<?php esc_html_e( 'Email', 'echo-knowledge-base' ); ?>
							<span class="epkb-quizzes-admin__label-tag"><?php esc_html_e( 'Optional', 'echo-knowledge-base' ); ?></span>
						</label>
						<input type="email" id="epkb-quiz-interest-email" maxlength="190">
					</div>
				</div>
				<div class="epkb-quizzes-admin__field">
					<label for="epkb-quiz-interest-feedback"><?php esc_html_e( 'Feedback', 'echo-knowledge-base' ); ?></label>
					<textarea id="epkb-quiz-interest-feedback" rows="5" placeholder="<?php esc_attr_e( 'What kind of quiz workflows or learner features would help most?', 'echo-knowledge-base' ); ?>"></textarea>
				</div>
				<div id="epkb-quiz-interest-message" class="epkb-quizzes-admin__notice" hidden></div>
				<div class="epkb-quiz-modal__footer">
					<button type="button" class="epkb-btn epkb-primary-btn epkb-quiz-modal__skip" id="epkb-quiz-interest-skip"><?php esc_html_e( 'Skip', 'echo-knowledge-base' ); ?></button>
					<button type="button" class="epkb-btn epkb-success-btn" id="epkb-quiz-interest-submit"><?php esc_html_e( 'Send Feedback', 'echo-knowledge-base' ); ?></button>
				</div>
			</div>
		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Settings tab HTML.
	 *
	 * @return string
	 */
	private static function settings_tab() {

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$quiz_dependency_attrs = ' data-dependency-ids="quizzes_enable" data-enable-on-values="on"';
		$active_provider = EPKB_AI_Provider::get_active_provider();
		$active_provider_label = EPKB_AI_Provider::get_provider_label( $active_provider );
		ob_start(); ?>

		<input id="epkb-list-of-kbs" type="hidden" value="<?php echo esc_attr( EPKB_KB_Config_DB::DEFAULT_KB_ID ); ?>">

		<div class="epkb-admin__form">
			<div class="epkb-admin__form__save_button">
				<button class="epkb-success-btn epkb-admin__kb__form-save__button"><?php esc_html_e( 'Save Settings', 'echo-knowledge-base' ); ?></button>
			</div>
			<div class="epkb-admin-info-box">
				<div class="epkb-admin-info-box__header">
					<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-cog"></div>
					<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'Feature Settings', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-admin-info-box__body"> <?php

					EPKB_HTML_Elements::checkbox_toggle( array(
						'id'                => 'quizzes_enable',
						'name'              => 'quizzes_enable',
						'text'              => esc_html__( 'Quizzes Enabled', 'echo-knowledge-base' ),
						'checked'           => $kb_config['quizzes_enable'] === 'on',
						'input_group_class' => 'eckb-conditional-setting-input epkb-quizzes-settings-toggle ',
					) );

					self::display_demo_quiz_cta(); ?>
				</div>
			</div>

			<div class="epkb-admin-info-box eckb-condition-depend__quizzes_enable"<?php echo $quiz_dependency_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="epkb-admin-info-box__header">
					<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-magic"></div>
					<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'AI Generation', 'echo-knowledge-base' ); ?></div>
				</div>
					<div class="epkb-admin-info-box__body">
						<p>
							<?php
							echo esc_html(
								sprintf( __( 'Quiz drafts use your active AI provider: %s.', 'echo-knowledge-base' ), $active_provider_label )
							);
							?>
						</p>
					</div>
				</div>

			<div class="epkb-admin-info-box eckb-condition-depend__quizzes_enable"<?php echo $quiz_dependency_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="epkb-admin-info-box__header">
					<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-font"></div>
					<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'Frontend Text', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-admin-info-box__body epkb-quizzes-settings__text-fields"> <?php
					EPKB_HTML_Elements::text( array(
						'name'  => 'quizzes_eyebrow_text',
						'label' => esc_html__( 'Eyebrow Text', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_eyebrow_text'],
						'max'   => 80,
					) );

					EPKB_HTML_Elements::text( array(
						'name'  => 'quizzes_start_button_text',
						'label' => esc_html__( 'Start Button Text', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_start_button_text'],
						'max'   => 80,
					) );

					EPKB_HTML_Elements::text( array(
						'name'  => 'quizzes_question_label_text',
						'label' => esc_html__( 'Question Label', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_question_label_text'],
						'max'   => 40,
						'desc'  => esc_html__( 'Displayed before the number, for example: Question 1', 'echo-knowledge-base' ),
					) );

					EPKB_HTML_Elements::horizontal_text_inputs( array(
						'name'              => 'quizzes_true_false_text',
						'label'             => esc_html__( 'True / False Text', 'echo-knowledge-base' ),
						'input_group_class' => 'epkb-quizzes-settings__true-false-pair',
						'inputs'            => array(
							array(
								'name'  => 'quizzes_true_text',
								'label' => esc_html__( 'True', 'echo-knowledge-base' ),
								'value' => $kb_config['quizzes_true_text'],
								'max'   => 40,
							),
							array(
								'name'  => 'quizzes_false_text',
								'label' => esc_html__( 'False', 'echo-knowledge-base' ),
								'value' => $kb_config['quizzes_false_text'],
								'max'   => 40,
							),
						),
					) );

					EPKB_HTML_Elements::text( array(
						'name'  => 'quizzes_summary_title_text',
						'label' => esc_html__( 'Summary Title', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_summary_title_text'],
						'max'   => 80,
					) );

					EPKB_HTML_Elements::text( array(
						'name'  => 'quizzes_correct_text',
						'label' => esc_html__( 'Correct Text', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_correct_text'],
						'max'   => 40,
					) );

					EPKB_HTML_Elements::text( array(
						'name'  => 'quizzes_incorrect_text',
						'label' => esc_html__( 'Incorrect Text', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_incorrect_text'],
						'max'   => 40,
					) );

					EPKB_HTML_Elements::text( array(
						'name'  => 'quizzes_score_prefix_text',
						'label' => esc_html__( 'Score Prefix', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_score_prefix_text'],
						'max'   => 80,
					) ); ?>
				</div>
			</div>

			<div class="epkb-admin-info-box eckb-condition-depend__quizzes_enable"<?php echo $quiz_dependency_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="epkb-admin-info-box__header">
					<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-paint-brush"></div>
					<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'Frontend Colors', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-admin-info-box__body epkb-quizzes-settings__color-fields"> <?php
					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_accent_color',
						'label' => esc_html__( 'Accent', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_accent_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_button_text_color',
						'label' => esc_html__( 'Button Text', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_button_text_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_card_border_color',
						'label' => esc_html__( 'Card Border', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_card_border_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_card_background_color',
						'label' => esc_html__( 'Card Background', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_card_background_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_heading_text_color',
						'label' => esc_html__( 'Heading Text', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_heading_text_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_body_text_color',
						'label' => esc_html__( 'Body Text', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_body_text_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_intro_background_color',
						'label' => esc_html__( 'Intro Background', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_intro_background_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_correct_background_color',
						'label' => esc_html__( 'Correct Background', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_correct_background_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_correct_border_color',
						'label' => esc_html__( 'Correct Border', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_correct_border_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_incorrect_background_color',
						'label' => esc_html__( 'Incorrect Background', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_incorrect_background_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_incorrect_border_color',
						'label' => esc_html__( 'Incorrect Border', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_incorrect_border_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_summary_background_color',
						'label' => esc_html__( 'Summary Background', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_summary_background_color'],
					) );

					EPKB_HTML_Elements::color( array(
						'name'  => 'quizzes_summary_text_color',
						'label' => esc_html__( 'Summary Text', 'echo-knowledge-base' ),
						'value' => $kb_config['quizzes_summary_text_color'],
					) ); ?>
				</div>
			</div>
		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Display demo quiz CTA.
	 */
	private static function display_demo_quiz_cta() { ?>
		<div class="epkb-quizzes-admin__demo-cta">
			<div class="epkb-quizzes-admin__demo-cta-copy">
				<div class="epkb-quizzes-admin__demo-cta-label"><?php esc_html_e( 'See the Demo Quiz', 'echo-knowledge-base' ); ?></div>
				<p><?php esc_html_e( 'Open our live demo quiz to see how the quiz experience looks for your readers.', 'echo-knowledge-base' ); ?></p>
			</div>
			<a href="<?php echo esc_url( EPKB_Quizzes_Utilities::get_demo_quiz_url() ); ?>" class="epkb-btn epkb-primary-btn" target="_blank" rel="noopener noreferrer">
				<span class="epkbfa epkbfa-external-link"></span>
				<?php esc_html_e( 'See Demo Quiz', 'echo-knowledge-base' ); ?>
			</a>
		</div> <?php
	}

	/**
	 * Display quiz library panel.
	 *
	 * @param array $quizzes
	 */
	private static function display_quiz_library( $quizzes ) { ?>
		<div class="epkb-quizzes-admin__sidebar epkb-quizzes-admin__sidebar--overview">
			<div class="epkb-quizzes-admin__sidebar-head">
				<div>
					<h3><?php esc_html_e( 'Quiz Library', 'echo-knowledge-base' ); ?></h3>
					<p><?php esc_html_e( 'Open a quiz to edit it or start a new draft.', 'echo-knowledge-base' ); ?></p>
				</div>
				<button type="button" class="epkb-btn epkb-success-btn epkb-quiz-create-trigger">
					<span class="epkbfa epkbfa-plus-circle"></span>
					<span><?php esc_html_e( 'Create Quiz', 'echo-knowledge-base' ); ?></span>
				</button>
			</div>

			<div id="epkb-quizzes-list" class="epkb-quizzes-list">
				<?php if ( empty( $quizzes ) ) { ?>
					<div class="epkb-quizzes-list__empty"><?php esc_html_e( 'No quizzes yet.', 'echo-knowledge-base' ); ?></div>
				<?php } else { ?>
					<?php foreach ( $quizzes as $quiz ) { ?>
						<?php self::display_quiz_list_row( $quiz ); ?>
					<?php } ?>
				<?php } ?>
			</div>
		</div> <?php
	}

	/**
	 * Render a single quiz row.
	 *
	 * @param int|WP_Post $quiz
	 * @param bool $return_html
	 * @return string|void
	 */
	public static function display_quiz_list_row( $quiz, $return_html = false ) {

		$quiz = EPKB_Quizzes_Utilities::get_quiz( $quiz );
		if ( empty( $quiz ) ) {
			return $return_html ? '' : null;
		}

		$payload = EPKB_Quizzes_Utilities::get_quiz_payload( $quiz );
		if ( empty( $payload ) ) {
			return $return_html ? '' : null;
		}

		if ( $return_html ) {
			ob_start();
		}

		$status_label = $payload['status'] === 'publish' ? esc_html__( 'Published', 'echo-knowledge-base' ) : esc_html__( 'Draft', 'echo-knowledge-base' ); ?>

		<div class="epkb-quiz-list-row epkb-quiz-list-row--<?php echo esc_attr( $payload['status'] ); ?>" data-quiz-id="<?php echo esc_attr( $payload['quiz_id'] ); ?>" data-source-article-id="<?php echo esc_attr( $payload['source_article_id'] ); ?>">
			<div class="epkb-quiz-list-row__body">
				<div class="epkb-quiz-list-row__title">
					<span class="epkbfa epkbfa-check-square-o"></span>
					<span><?php echo esc_html( $payload['title'] ); ?></span>
				</div>
				<div class="epkb-quiz-list-row__meta">
					<span class="epkb-quiz-list-row__source">
						<span class="epkbfa epkbfa-file-text-o"></span>
						<span><?php echo esc_html( $payload['source_article_label'] ); ?></span>
					</span>
				</div>
				<?php if ( ! empty( $payload['source_warning_message'] ) ) { ?>
					<div class="epkb-quiz-list-row__warning">
						<span class="epkbfa epkbfa-exclamation-triangle"></span>
						<span><?php echo esc_html( $payload['source_warning_message'] ); ?></span>
					</div>
				<?php } ?>
			</div>
			<div class="epkb-quiz-list-row__actions">
				<div class="epkb-quiz-list-row__status-row">
					<button type="button" class="epkb-btn epkb-primary-btn epkb-quiz-list-row__edit"><?php esc_html_e( 'Edit', 'echo-knowledge-base' ); ?></button>
					<span class="epkb-quiz-list-row__status epkb-quiz-list-row__status--<?php echo esc_attr( $payload['status'] ); ?>"><?php echo esc_html( $status_label ); ?></span>
					<?php if ( ! empty( $payload['source_article_url'] ) ) {
						$quiz_url = $payload['source_article_url'] . '#epkb-article-quiz-' . $payload['quiz_id']; ?>
						<a href="<?php echo esc_url( $quiz_url ); ?>" class="epkb-quiz-list-row__view-link" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation();">
							<span class="epkbfa epkbfa-external-link"></span>
							<?php esc_html_e( 'View Quiz', 'echo-knowledge-base' ); ?>
						</a>
					<?php } ?>
				</div>
			</div>
		</div> <?php

		if ( $return_html ) {
			return ob_get_clean();
		}
	}
}
