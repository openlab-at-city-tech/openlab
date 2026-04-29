<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Quiz feature setup.
 */
class EPKB_Quizzes_Setup {

	public function __construct() {
		add_action( 'eckb_add_kb_submenu', array( 'EPKB_Quizzes_Page', 'add_menu_item' ) );
		add_action( 'eckb-article-content-footer', array( $this, 'render_article_quiz' ), 5 );
	}

	/**
	 * Render published quiz on article pages.
	 *
	 * @param array $args
	 */
	public function render_article_quiz( $args ) {

		$article = empty( $args['article'] ) ? null : $args['article'];
		if ( empty( $article ) || empty( $article->ID ) ) {
			return;
		}

		$quiz = EPKB_Quizzes_Utilities::get_frontend_quiz_payload( $article->ID );
		if ( empty( $quiz ) ) {
			return;
		}

		$frontend_settings = EPKB_Quizzes_Utilities::get_frontend_settings();
		wp_enqueue_script( 'epkb-quizzes-frontend' );

		$intro_content = empty( $quiz['intro'] ) ? '' : $quiz['intro'];
		$toggle_id = 'epkb-article-quiz-toggle-' . $quiz['quiz_id'];
		$panel_id = 'epkb-article-quiz-panel-' . $quiz['quiz_id']; ?>

		<div class="epkb-article-quiz" id="epkb-article-quiz-<?php echo esc_attr( $quiz['quiz_id'] ); ?>" data-quiz-id="<?php echo esc_attr( $quiz['quiz_id'] ); ?>">
			<div class="epkb-article-quiz__card">
				<div class="epkb-article-quiz__header">
					<div class="epkb-article-quiz__eyebrow"><?php echo esc_html( $frontend_settings['eyebrow_text'] ); ?></div>
					<h2 class="epkb-article-quiz__title"><?php echo esc_html( $quiz['title'] ); ?></h2>
					<button type="button" id="<?php echo esc_attr( $toggle_id ); ?>" class="epkb-article-quiz__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
						<span class="epkb-article-quiz__toggle-text"><?php echo esc_html( $frontend_settings['start_button_text'] ); ?></span>
						<span class="epkb-article-quiz__toggle-icon epkbfa epkbfa-angle-down" aria-hidden="true"></span>
					</button>
				</div>

				<div id="<?php echo esc_attr( $panel_id ); ?>" class="epkb-article-quiz__panel" hidden>
					<?php if ( ! empty( $intro_content ) ) { ?>
						<div class="epkb-article-quiz__intro"><?php echo $intro_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php } ?>

					<div class="epkb-article-quiz__questions"> <?php
						foreach ( $quiz['questions'] as $index => $question ) {
							$question_number = $index + 1;
							$question_name = 'epkb-quiz-question-' . $quiz['quiz_id'] . '-' . $question['id']; ?>
							<div class="epkb-article-quiz__question" data-question-id="<?php echo esc_attr( $question['id'] ); ?>" data-correct-choice="<?php echo esc_attr( $question['correct_choice'] ); ?>">
								<div class="epkb-article-quiz__question-number"><?php echo esc_html( trim( $frontend_settings['question_label_text'] . ' ' . $question_number ) ); ?></div>
								<div class="epkb-article-quiz__question-text"><?php echo esc_html( $question['question'] ); ?></div>
								<div class="epkb-article-quiz__choices">
									<?php foreach ( $question['choices'] as $choice_index => $choice ) { ?>
										<label class="epkb-article-quiz__choice">
											<input type="radio" name="<?php echo esc_attr( $question_name ); ?>" value="<?php echo esc_attr( $choice_index ); ?>">
											<span class="epkb-article-quiz__choice-label"><?php echo esc_html( $choice ); ?></span>
										</label>
									<?php } ?>
								</div>
								<div class="epkb-article-quiz__feedback" hidden>
									<div class="epkb-article-quiz__feedback-state"></div>
									<?php if ( ! empty( $question['explanation'] ) ) { ?>
										<div class="epkb-article-quiz__feedback-explanation"><?php echo nl2br( esc_html( $question['explanation'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
									<?php } ?>
								</div>
							</div> <?php
						} ?>
					</div>

					<div class="epkb-article-quiz__summary" hidden>
						<div class="epkb-article-quiz__summary-title"><?php echo esc_html( $frontend_settings['summary_title_text'] ); ?></div>
						<div class="epkb-article-quiz__summary-score"></div>
					</div>
				</div>
			</div>
		</div> <?php
	}
}
