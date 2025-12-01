<?php defined( 'ABSPATH' ) || exit();

/**
 * Display standalone Content Analysis admin page
 */
class EPKB_AI_Content_Analysis_Page {

	/**
	 * Display the Content Analysis page
	 */
	public function display_content_analysis_page() {

		EPKB_Core_Utilities::display_missing_css_message();

		// Get tab configuration
		$tab_config = $this->get_tab_config();

		// Get current KB ID and post type
		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}
		$post_type = EPKB_KB_Handler::get_post_type( $kb_id );

		// Create React data for the standalone page
		$react_data = array(
			'page_type' => 'standalone',
			'tab_config' => $tab_config,
			'ai_enabled' => EPKB_AI_Utilities::is_ai_configured(),
			'admin_url' => admin_url(),
			'post_type' => $post_type,
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'rest_url' => esc_url_raw( rest_url() ),
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce( '_wpnonce_epkb_ajax_action' ),
			'i18n' => $this->get_i18n_strings(),
		);

		// Start the page output
		echo '<div class="wrap" id="epkb-admin-content-analysis-page-wrap">'; ?>

		<h1></h1> <!-- This is here for WP admin consistency -->

		<div class="epkb-wrap">
			<div class="epkb-content-analysis-layout">
				<div id="epkb-content-analysis-react-root"
					 class="epkb-ai-config-page-react epkb-content-analysis-standalone"
					 data-epkb-settings='<?php echo esc_attr( wp_json_encode( $react_data ) ); ?>'>
					<!-- Initial loading spinner - will be replaced when React mounts -->
					<div class="epkb-ai-loading-container" id="epkb-ai-initial-loader">
						<div class="epkb-loading-spinner"></div>
						<div class="epkb-ai-loading"><?php echo esc_html__( 'Loading Content Analysis...', 'echo-knowledge-base' ); ?></div>
					</div>
				</div>
			</div>
		</div>		<?php

		echo '</div>';
	}

	/**
	 * Get the configuration for the Content Analysis tab
	 * @return array
	 */
	public function get_tab_config() {

		if ( ! EPKB_AI_Utilities::is_ai_configured() ) {
			return array(
				'error' => __( 'AI features are not configured. Please add your OpenAI API key and accept the terms to access Content Analysis.', 'echo-knowledge-base' )
			);
		}

		$ai_config = EPKB_AI_Config_Specs::get_ai_config();

		// Build sub_tabs array
		$sub_tabs = array();

		// Add Analyze as the first sub-tab
		$sub_tabs['overview'] = array(
			'id' => 'overview',
			'title' => __( 'Analyze Content', 'echo-knowledge-base' ),
			'icon' => 'epkbfa epkbfa-bar-chart'
		);

		// Add Improve as the second sub-tab
		$sub_tabs['improve'] = array(
			'id' => 'improve',
			'title' => __( 'Improve Content', 'echo-knowledge-base' ),
			'icon' => 'epkbfa epkbfa-magic'
		);

		// Get preloaded content analysis data for initial display
		$preloaded_data = $this->get_preloaded_content_analysis_data();

		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		 /** @disregard P1011 */
		$config = array(
			'tab_id' => 'content-analysis',
			'title' => __( 'Content Analysis', 'echo-knowledge-base' ),
			'sub_tabs' => $sub_tabs,
			'ai_config' => $ai_config,
			'kb_id' => $kb_id,  // Pass KB ID to frontend
			'is_ai_features_pro_enabled' => EPKB_Utilities::is_ai_features_pro_enabled(),
			'is_access_manager_active' => EPKB_Utilities::is_amag_on(),
			'preloaded_data' => $preloaded_data
		);

		return $config;
	}

	/**
	 * Get pre-loaded content analysis data for initial page load
	 * @return array
	 */
	private function get_preloaded_content_analysis_data() {

		$preloaded = array();

		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		// Status tabs to pre-load (all, to_analyse, to_improve, recent)
		$statuses = array( 'all', 'to_analyse', 'to_improve', 'recent' );

		// Get the post type for this KB
		$post_type = EPKB_KB_Handler::get_post_type( $kb_id );

		foreach ( $statuses as $status ) {

			// Query KB articles directly
			$args = array(
				'post_type' => $post_type,
				'post_status' => 'publish',
				'posts_per_page' => 20,
				'paged' => 1,
				'orderby' => 'modified',
				'order' => 'DESC'
			);

			// Transform data for content analysis display
			$query = new WP_Query( $args );
			$transformed_data = array();
			foreach ( $query->posts as $post ) {

				// Check if this is a demo article
				$is_demo_article = EPKB_KB_Demo_Data::is_demo_article( $post->ID );

				// Get analysis data if available
				// For demo articles, this will return demo data automatically
				$analysis_data = EPKB_AI_Content_Analysis_Utilities::get_article_analysis_data( $post->ID );
				$scores = $analysis_data['scores'];
				$dates = $analysis_data['dates'];

				// Get the post type object to get the label
				$post_type_obj = get_post_type_object( $post->post_type );
				$type_name = 'Article';
				if ( $post_type_obj && isset( $post_type_obj->labels->singular_name ) ) {
					$type_name = $post_type_obj->labels->singular_name;
				}

				// Add content analysis specific fields
				$transformed_item = new stdClass();
				$transformed_item->id = $post->ID;
				$transformed_item->item_id = $post->ID;
				$transformed_item->title = $post->post_title;

				// For demo articles, use demo scores
				if ( $is_demo_article ) {
					$demo_tags_data = EPKB_KB_Demo_Data::get_demo_tags_usage_data();
					$transformed_item->score = $demo_tags_data['score'];
					$transformed_item->scoreComponents = array(
						array( 'name' => 'Tags Usage', 'value' => $demo_tags_data['score'] ),
						array( 'name' => 'Gap Analysis', 'value' => '-' ),
						array( 'name' => 'Readability', 'value' => '-' )
					);
					$transformed_item->importance = 'N/A';
					$transformed_item->is_demo = true;
				} else {
					// Get score from analysis data or default
					$transformed_item->score = $scores && isset( $scores['overall'] ) ? $scores['overall'] : '-';

					// Get score components from analysis data
					if ( $scores && isset( $scores['components'] ) ) {
						$transformed_item->scoreComponents = EPKB_AI_Content_Analysis_Utilities::format_score_components( $scores['components'] );
					} else {
						$transformed_item->scoreComponents = array(
							array( 'name' => 'Tags Usage', 'value' => '-' ),
							array( 'name' => 'Gap Analysis', 'value' => '-' ),
							array( 'name' => 'Readability', 'value' => '-' )
						);
					}

					// Importance from analysis data
					$transformed_item->importance = EPKB_AI_Content_Analysis_Utilities::calculate_article_importance( $post->ID ); ;
					$transformed_item->is_demo = false;
				}

				$transformed_item->last_analyzed = $dates['analyzed'] ? $dates['analyzed'] : 'Not analyzed';
				$transformed_item->updated = $post->post_modified;
				$transformed_item->type = $post->post_type;
				$transformed_item->type_name = $type_name;
				$transformed_item->status = $analysis_data['status'];
				$transformed_item->display_status = EPKB_AI_Content_Analysis_Utilities::get_article_display_status( $post->ID );

				$transformed_data[] = $transformed_item;
			}

			// Get total count for pagination
			$total = $query->found_posts;
			$total_pages = $query->max_num_pages;

			$preloaded[$status] = array(
				'data' => $transformed_data,
				'pagination' => array(
					'page' => 1,
					'per_page' => 20,
					'total' => $total,
					'total_pages' => $total_pages
				)
			);
		}

		// Calculate status statistics from actual KB articles
		// Get all articles to count properly
		$count_args = array(
			'post_type' => $post_type,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'fields' => 'ids'
		);
		$all_posts = get_posts( $count_args );

		$to_analyze_count = 0;
		$to_improve_count = 0;

		foreach ( $all_posts as $post_id ) {
			$display_status = EPKB_AI_Content_Analysis_Utilities::get_article_display_status( $post_id );
			if ( $display_status === 'To Analyze' ) {
				$to_analyze_count++;
			} elseif ( $display_status === 'To Improve' ) {
				$to_improve_count++;
			}
		}

		$stats = array(
			'all' => count( $all_posts ),
			'to_analyse' => $to_analyze_count,
			'to_improve' => $to_improve_count,
			'recent' => count( $all_posts ),
		);

		return array(
			'data' => $preloaded,
			'stats' => $stats
		);
	}

	/**
	 * Get internationalization strings for React
	 *
	 * @return array
	 */
	private function get_i18n_strings() {
		return array(
			'save' => __( 'Save', 'echo-knowledge-base' ),
			'saving' => __( 'Saving...', 'echo-knowledge-base' ),
			'saved' => __( 'Saved!', 'echo-knowledge-base' ),
			'error' => __( 'Error', 'echo-knowledge-base' ),
			'success' => __( 'Success', 'echo-knowledge-base' ),
			'loading' => __( 'Loading...', 'echo-knowledge-base' ),
			'confirm' => __( 'Are you sure?', 'echo-knowledge-base' ),
			'yes' => __( 'Yes', 'echo-knowledge-base' ),
			'no' => __( 'No', 'echo-knowledge-base' ),
			'cancel' => __( 'Cancel', 'echo-knowledge-base' ),
			'ok' => __( 'OK', 'echo-knowledge-base' ),
			'content_analysis' => __( 'Content Analysis', 'echo-knowledge-base' ),
			'analyze' => __( 'Analyze', 'echo-knowledge-base' ),
			'improve' => __( 'Improve', 'echo-knowledge-base' ),
			'analyzing' => __( 'Analyzing...', 'echo-knowledge-base' ),
			'analysis_complete' => __( 'Analysis complete!', 'echo-knowledge-base' ),
			'analysis_failed' => __( 'Analysis failed. Please try again.', 'echo-knowledge-base' ),
			'ai_disabled_message' => __( 'AI features are not configured. Please add your OpenAI API key and accept the terms to use Content Analysis.', 'echo-knowledge-base' ),
			'go_to_ai_settings' => __( 'Go to AI Settings', 'echo-knowledge-base' ),
			'demo_analytics_badge' => __( 'Demo', 'echo-knowledge-base' ),
			'demo_analytics_message' => __( 'This is demo analytics data for demonstration purposes.', 'echo-knowledge-base' ),
			'demo_analytics_notice' => __( 'Demo Analytics', 'echo-knowledge-base' )
		);
	}
}



