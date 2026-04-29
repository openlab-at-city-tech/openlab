<?php if ( ! defined( 'ABSPATH' ) ) exit;

class EPKB_Help_Resources_Page {

	public function __construct() {
		// Register AJAX handlers if needed
	}

	/**
	 * Display the Help Resources page
	 */
	public function display_page() {

		// Enqueue necessary assets
		$this->enqueue_assets();

		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $kb_config ) || empty( $kb_config ) || ! is_array( $kb_config ) ) {
			$kb_config = EPKB_KB_Config_Specs::get_default_kb_config( $kb_id );
		}		?>

		<div class="wrap epkb-help-resources-page-wrap">
			<h1></h1>

			<!-- Admin Header -->
			<?php EPKB_HTML_Admin::admin_header( $kb_config, array(), 'header' ); ?>

			<!-- Page Content -->
			<div class="epkb-help-resources-container">

				<!-- Page Title -->
				<div class="epkb-help-resources__title-section">
					<h1 class="epkb-help-resources__title"><?php esc_html_e( 'Setup Guide', 'echo-knowledge-base' ); ?></h1>
					<?php /* Hidden for now - re-enable when ready ?>
					<p class="epkb-help-resources__subtitle"><?php esc_html_e( 'Get instant help with AI-powered search and explore our video guides', 'echo-knowledge-base' ); ?></p>
					<?php */ ?>
				</div>

				<!-- KB Setup Steps -->
				<?php echo EPKB_Setup_Steps::render_steps_section( $kb_config ); ?>

				<?php /* Hidden for now - re-enable when AI Live Help and Video Guides are ready ?>
				<!-- Two Column Layout -->
				<div class="epkb-help-resources__columns">

					<!-- Left Column: AI Search (60%) -->
					<div class="epkb-help-resources__column epkb-help-resources__column--ai-search">
						<div class="epkb-help-resources__column-header">
							<span class="epkb-help-resources__column-icon epkbfa epkbfa-search"></span>
							<h2><?php esc_html_e( 'AI Live Help', 'echo-knowledge-base' ); ?></h2>
						</div>
						<div class="epkb-help-resources__column-content">
							<?php echo $this->render_ai_search( $kb_id ); ?>
						</div>
					</div>

					<!-- Right Column: Video Guides (40%) -->
					<div class="epkb-help-resources__column epkb-help-resources__column--videos">
						<div class="epkb-help-resources__column-header">
							<span class="epkb-help-resources__column-icon epkbfa epkbfa-youtube-play"></span>
							<h2><?php esc_html_e( 'Video Guides', 'echo-knowledge-base' ); ?></h2>
						</div>
						<div class="epkb-help-resources__column-content">
							<?php echo $this->render_video_guides(); ?>
						</div>
					</div>

				</div>
				<?php */ ?>

				<!-- Documentation Link -->
				<div class="epkb-help-resources__footer">
					<a href="https://www.echoknowledgebase.com/documentation/" target="_blank" class="epkb-help-resources__doc-link">
						<span class="epkbfa epkbfa-book"></span>
						<?php esc_html_e( 'View Full Documentation', 'echo-knowledge-base' ); ?>
						<span class="epkbfa epkbfa-external-link"></span>
					</a>
				</div>

			</div>

		</div>		<?php
	}

	/**
	 * Render AI Smart Search
	 */
	private function render_ai_search( $kb_id ) {

		// Ensure kb_id is valid
		if ( empty( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		// Check if AI search is enabled
		if ( ! EPKB_AI_Utilities::is_ai_search_smart_enabled() ) {
			return $this->render_ai_search_placeholder();
		}

		// Output the AI Smart Search shortcode
		ob_start();

		echo do_shortcode( '[ai-smart-search kb_id="' . absint( $kb_id ) . '"]' );

		return ob_get_clean();
	}

	/**
	 * Render placeholder when AI search is not enabled
	 */
	private function render_ai_search_placeholder() {
		ob_start(); ?>
		<div class="epkb-help-resources__placeholder">
			<div class="epkb-help-resources__placeholder-icon">
				<span class="epkbfa epkbfa-lightbulb-o"></span>
			</div>
			<h3><?php esc_html_e( 'AI Search Not Enabled', 'echo-knowledge-base' ); ?></h3>
			<p><?php esc_html_e( 'Enable AI Search in the AI settings to get instant answers to your questions.', 'echo-knowledge-base' ); ?></p>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=search' ) ); ?>" class="epkb-btn epkb-btn--primary">
				<?php esc_html_e( 'Configure AI Search', 'echo-knowledge-base' ); ?>
			</a>
		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Render Video Guides (placeholders for now)
	 */
	private function render_video_guides() {
		ob_start();

		$video_guides = array(
			array(
				'title'       => __( 'Getting Started with Knowledge Base', 'echo-knowledge-base' ),
				'video_id'    => 'placeholder1',
				'description' => __( 'Learn the basics of setting up your first knowledge base', 'echo-knowledge-base' ),
			),
			array(
				'title'       => __( 'Creating Articles and Categories', 'echo-knowledge-base' ),
				'video_id'    => 'placeholder2',
				'description' => __( 'Master article creation and organize content effectively', 'echo-knowledge-base' ),
			),
			array(
				'title'       => __( 'Configuring AI Features', 'echo-knowledge-base' ),
				'video_id'    => 'placeholder3',
				'description' => __( 'Set up AI-powered search and chat for better user experience', 'echo-knowledge-base' ),
			),
			array(
				'title'       => __( 'Customizing Your KB Design', 'echo-knowledge-base' ),
				'video_id'    => 'placeholder4',
				'description' => __( 'Use the frontend editor to customize colors, layouts, and more', 'echo-knowledge-base' ),
			),
		);	?>

		<div class="epkb-help-resources__videos-grid">
			<?php foreach ( $video_guides as $video ) : ?>
				<div class="epkb-help-resources__video-item">
					<div class="epkb-help-resources__video-thumbnail">
						<!-- Placeholder for video thumbnail -->
						<div class="epkb-help-resources__video-placeholder">
							<span class="epkbfa epkbfa-play-circle"></span>
						</div>
					</div>
					<div class="epkb-help-resources__video-info">
						<h4 class="epkb-help-resources__video-title"><?php echo esc_html( $video['title'] ); ?></h4>
						<p class="epkb-help-resources__video-description"><?php echo esc_html( $video['description'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Enqueue necessary CSS and JS
	 */
	private function enqueue_assets() {
		// AI Smart Search assets will be enqueued by the shortcode itself
		wp_enqueue_style( 'epkb-admin-plugin-pages' );

		// WP Pointer for inline setup step pointers
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

	/**
	 * Get the button HTML to add to admin headers
	 */
	public static function get_help_button_html() {
		$page_url = admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-help-resources' );

		ob_start(); ?>
		<a href="<?php echo esc_url( $page_url ); ?>" class="epkb-help-resources__header-button" target="_blank">
			<span class="epkb-help-resources__button-icon epkbfa epkbfa-life-ring"></span>
			<span class="epkb-help-resources__button-text"><?php esc_html_e( 'AI Live Help + Video Guides', 'echo-knowledge-base' ); ?></span>
		</a>		<?php

		return ob_get_clean();
	}
}
