<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Dashboard admin page
 *
 */
class EPKB_Dashboard_Page {

	private $kb_config;

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dashboard_scripts' ) );
		add_action( 'wp_ajax_epkb_enable_glossary', array( $this, 'ajax_enable_glossary' ) );
		add_action( 'wp_ajax_epkb_enable_quizzes', array( $this, 'ajax_enable_quizzes' ) );
	}

	/**
	 * Display Dashboard page
	 */
	public function display_dashboard_page() {

		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		$kb_id = empty( $kb_id ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $kb_id;
		$kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
				
		$post_type = EPKB_KB_Handler::get_post_type( $kb_id );
		
		// Check if Setup Wizard should be shown (first 2 weeks)
		$show_setup_wizard = $this->should_show_setup_wizard();
		
		// Get statistics
		$article_count_obj = wp_count_posts( $post_type );
		$published_articles = isset( $article_count_obj->publish ) ? $article_count_obj->publish : 0;
		$draft_articles = isset( $article_count_obj->draft ) ? $article_count_obj->draft : 0;
		
		$faq_count_obj = wp_count_posts( EPKB_FAQs_CPT_Setup::FAQS_POST_TYPE );
		$published_faqs = isset( $faq_count_obj->publish ) ? $faq_count_obj->publish : 0;
		
		// Get category count
		$categories = get_terms( array(
			'taxonomy' => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ),
			'hide_empty' => false,
		) );
		if ( is_wp_error( $categories ) ) {
			$category_count = 0;
		} else {
			$category_count = is_array( $categories ) ? count( $categories ) : 0;
		}
		
		// Get views this month
		$views_this_month = 0;
		if ( $kb_config['article_views_counter_enable'] == 'on' ) {
			$year = wp_date( 'Y' );
			$month_weeks = $this->get_month_weeks();
			
			$args = array(
				'post_type' => $post_type,
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'fields' => 'ids',
			);
			$articles = get_posts( $args );
			
			foreach ( $articles as $article_id ) {
				$year_meta = EPKB_Utilities::get_postmeta( $article_id, 'epkb-article-views-' . $year, [] );
				if ( is_wp_error( $year_meta ) ) {
					EPKB_Logging::add_log( 'Failed to get article views meta', $year_meta );
					continue;
				}
				if ( is_array( $year_meta ) ) {
					foreach ( $month_weeks as $week ) {
						if ( isset( $year_meta[$week] ) && is_numeric( $year_meta[$week] ) ) {
							$views_this_month += (int) $year_meta[$week];
						}
					}
				}
			}
		}
		
		// Get searches this month
		$searches_this_month = 0;
		$searches_found = EPKB_Utilities::get_kb_option( $kb_id, 'epkb_hit_search_counter', 0 );
		if ( is_wp_error( $searches_found ) ) {
			EPKB_Logging::add_log( 'Failed to get hit search counter', $searches_found );
			$searches_found = 0;
		}
		$searches_not_found = EPKB_Utilities::get_kb_option( $kb_id, 'epkb_miss_search_counter', 0 );
		if ( is_wp_error( $searches_not_found ) ) {
			EPKB_Logging::add_log( 'Failed to get miss search counter', $searches_not_found );
			$searches_not_found = 0;
		}
		$searches_this_month = $searches_found + $searches_not_found;

		// Ensure WordPress admin environment is properly loaded
		if ( ! function_exists( 'wp_admin_bar_render' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/admin.php' );
		}
		
		EPKB_HTML_Admin::admin_page_header();
		EPKB_HTML_Admin::admin_header( $kb_config, [] );   ?>

		<div id="ekb-admin-page-wrap">
			<div id="epkb-dashboard-page-container">

				<!-- ================= KPI Actions ================= -->
				<div class="epkb-kpi-actions-container">
					<div class="epkb-kpi-actions-buttons">

						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . $post_type ) ); ?>" class="epkb-btn epkb-btn-add-article">
							<?php esc_html_e( '+ Add New Article', 'echo-knowledge-base' ); ?>
						</a>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-faqs#faqs-overview' ) ); ?>" class="epkb-btn epkb-btn-add-faq">
							<?php esc_html_e( '+ Add New FAQs', 'echo-knowledge-base' ); ?>
						</a>						<?php 
						
						$kb_main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
						if ( empty( $kb_main_page_url ) ) { ?>
							<a href="#" class="epkb-btn epkb-btn-frontend-editor epkb-btn-no-kb-main-page" data-setup-wizard-url="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-kb-configuration&setup-wizard-on=true' ) ); ?>">
								<?php esc_html_e( 'Frontend Editor', 'echo-knowledge-base' ); ?>
							</a>						<?php 
						} else { ?>
							<a href="<?php echo esc_url( $kb_main_page_url . '?action=epkb_load_editor&epkb_kb_id=' . $kb_config['id'] ); ?>" class="epkb-btn epkb-btn-frontend-editor" target="_blank">
								<?php esc_html_e( 'Frontend Editor', 'echo-knowledge-base' ); ?>
							</a>						<?php 
						} ?>						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-kb-configuration&setup-wizard-on=true' ) ); ?>" class="epkb-btn epkb-btn-setup-wizard">
							<?php esc_html_e( 'Setup Wizard', 'echo-knowledge-base' ); ?>
						</a>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-kb-configuration&ekb-main-page-loc=tools&ekb-secondary-page-loc=import#tools__import' ) ); ?>" class="epkb-btn epkb-btn-import-data">
							<?php esc_html_e( 'Import Data', 'echo-knowledge-base' ); ?>
						</a>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-kb-configuration&ekb-main-page-loc=tools&ekb-secondary-page-loc=convert#tools__convert' ) ); ?>" class="epkb-btn epkb-btn-convert-posts">
							<?php esc_html_e( 'Convert Posts', 'echo-knowledge-base' ); ?>
						</a>
						
					</div>
				</div>
				
				<!-- ================= Top KPI tiles ================ -->
				<section class="epkb-kpi-grid">

					<a href="#" class="epkb-kpi-card">
						<div class="epkb-kpi-icon-container epkb-kpi-articles">
							<span class="epkb-kpi-icon epkbfa epkbfa-file-text"></span>
						</div>
						<div>
							<h3 class="epkb-kpi-value"><?php echo esc_html( $published_articles ); ?></h3>
							<p class="epkb-kpi-label"><?php esc_html_e( 'Total Articles', 'echo-knowledge-base' ); ?></p>
						</div>
					</a>

					<a href="#" class="epkb-kpi-card">
						<div class="epkb-kpi-icon-container epkb-kpi-categories">
							<span class="epkb-kpi-icon epkbfa epkbfa-folder-open"></span>
						</div>
						<div>
							<h3 class="epkb-kpi-value"><?php echo esc_html( $category_count ); ?></h3>
							<p class="epkb-kpi-label"><?php esc_html_e( 'Total Categories', 'echo-knowledge-base' ); ?></p>
						</div>
					</a>

					<a href="#" class="epkb-kpi-card">
						<div class="epkb-kpi-icon-container epkb-kpi-faqs">
							<span class="epkb-kpi-icon epkbfa epkbfa-question-circle"></span>
						</div>
						<div>
							<h3 class="epkb-kpi-value"><?php echo esc_html( $published_faqs ); ?></h3>
							<p class="epkb-kpi-label"><?php esc_html_e( 'Total FAQs', 'echo-knowledge-base' ); ?></p>
						</div>
					</a>

					<a href="#" class="epkb-kpi-card">
						<div class="epkb-kpi-icon-container epkb-kpi-views">
							<span class="epkb-kpi-icon epkbfa epkbfa-eye"></span>
						</div>
						<div>
							<h3 class="epkb-kpi-value"><?php echo esc_html( $views_this_month ); ?></h3>
							<p class="epkb-kpi-label"><?php esc_html_e( 'Views this Month', 'echo-knowledge-base' ); ?></p>
						</div>
					</a>

					<a href="#" class="epkb-kpi-card">
						<div class="epkb-kpi-icon-container epkb-kpi-search">
							<span class="epkb-kpi-icon epkbfa epkbfa-search"></span>
						</div>
						<div>
							<h3 class="epkb-kpi-value"><?php echo esc_html( $searches_this_month ); ?></h3>
							<p class="epkb-kpi-label"><?php esc_html_e( 'Searches this Month', 'echo-knowledge-base' ); ?></p>
						</div>
					</a>
				</section>

				<!-- ================= Marketing row ================= -->
				<section class="epkb-marketing-row">
					
					<!-- Main Content (70%) -->
					<div class="epkb-main-content"><?php

						// Show KB Frontend setup section for KB #1 when Setup Wizard is available
						if ( $kb_id == 1 && $show_setup_wizard ) { ?>
							<!-- New KB Frontend Section -->
							<article class="epkb-card epkb-card--kb-frontend-setup" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-bottom: 20px;">
								<div class="epkb-kb-frontend-content" style="padding: 40px; text-align: center;">
									<div class="epkb-kb-frontend-text">
										<div class="epkb-kb-frontend-heading">
											<h2 style="color: white; font-size: 28px; margin-bottom: 20px;">
												<i class="epkbfa epkbfa-rocket" style="color: #FFD700; margin-right: 10px;"></i>
												<?php esc_html_e( 'View Your Knowledge Base Frontend', 'echo-knowledge-base' ); ?>
											</h2>
										</div>
										<div class="epkb-kb-frontend-description">
											<p style="color: rgba(255,255,255,0.95); font-size: 16px; line-height: 1.6; max-width: 600px; margin: 0 auto 30px;">
												<?php esc_html_e( 'Your Knowledge Base is ready! View it on the frontend using the button below or the link in the top right corner.', 'echo-knowledge-base' ); ?>
											</p>
										</div>
										<div class="epkb-kb-frontend-buttons" style="display: flex; justify-content: center; flex-wrap: wrap;">
											<?php
											$kb_main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
											if ( ! empty( $kb_main_page_url ) ) { ?>
												<a href="<?php echo esc_url( $kb_main_page_url ); ?>" target="_blank" class="epkb-btn" style="background: white; color: #667eea; padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;">
													<i class="epkbfa epkbfa-external-link"></i>
													<?php esc_html_e( 'View KB Frontend', 'echo-knowledge-base' ); ?>
												</a>
											<?php } else { ?>
												<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-kb-configuration&setup-wizard-on=true' ) ); ?>" class="epkb-btn" style="background: white; color: #667eea; padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s;">
													<i class="epkbfa epkbfa-play-circle"></i>
													<?php esc_html_e( 'Create KB Main Page', 'echo-knowledge-base' ); ?>
												</a>
											<?php } ?>
										</div>
									</div>
								</div>
							</article>						<?php
						} ?>

					<!-- Article Lists Container -->
					<div class="epkb-card-article-list-container">

						<!-- Most Viewed Articles -->
						<div class="epkb-card epkb-card--most-viewed">
							<div class="epkb-most-viewed-header">
								<h3><?php esc_html_e( 'Most Viewed Articles', 'echo-knowledge-base' ); ?></h3>
							</div>
							<div class="epkb-most-viewed-list">								<?php

								// Get most viewed articles
								$most_viewed_articles = array();
								if ( $kb_config['article_views_counter_enable'] == 'on' ) {
									$args = array(
										'post_type'      => $post_type,
										'post_status'    => 'publish',
										'posts_per_page' => 5,
										'orderby'        => 'meta_value_num',
										'meta_key'       => 'epkb-article-views',
										'order'          => 'DESC',
									);
									$most_viewed_articles = get_posts( $args );
								}

								if ( ! empty( $most_viewed_articles ) ) {
									$rank = 1;
									foreach ( $most_viewed_articles as $article ) {
										$views = EPKB_Utilities::get_postmeta( $article->ID, 'epkb-article-views', 0 );
										if ( is_wp_error( $views ) ) {
											EPKB_Logging::add_log( 'Failed to get article views', $views );
											$views = 0;
										}
										$article_url = get_permalink( $article->ID );										?>

										<div class="epkb-article-item">
											<div class="epkb-article-info">
												<span class="epkb-article-rank"><?php echo esc_html( $rank ); ?>.</span>
												<a href="<?php echo esc_url( $article_url ); ?>" class="epkb-article-title" target="_blank"><?php echo esc_html( $article->post_title ); ?></a>
											</div>
											<div class="epkb-article-views">
												<?php echo esc_html( number_format( $views ) ); ?> <?php esc_html_e( 'views', 'echo-knowledge-base' ); ?>
											</div>
										</div>
										<?php
										$rank++;
									}
								} else {
									?>
									<div class="epkb-article-item">
										<div class="epkb-article-info">
											<span class="epkb-article-title"><?php esc_html_e( 'Coming Soon', 'echo-knowledge-base' ); ?></span>
										</div>
									</div>
									<?php
								}								?>

							</div>
						</div>

						<!-- Recently Edited Articles -->
						<div class="epkb-card epkb-card--recently-edited">
							<div class="epkb-most-viewed-header">
								<h3><?php esc_html_e( 'Recently Edited Articles', 'echo-knowledge-base' ); ?></h3>
							</div>
							<div class="epkb-most-viewed-list">								<?php

								// Get recently edited articles
								$args = array(
									'post_type'      => $post_type,
									'post_status'    => 'publish',
									'posts_per_page' => 5,
									'orderby'        => 'modified',
									'order'          => 'DESC',
								);
								$recent_articles = get_posts( $args );

								if ( ! empty( $recent_articles ) ) {
									$rank = 1;
									foreach ( $recent_articles as $article ) {
										$article_url = get_permalink( $article->ID );
										$modified_date = get_the_modified_date( 'M j, Y', $article->ID );										?>
										<div class="epkb-article-item epkb-article-item--no-views">
											<div class="epkb-article-info">
												<span class="epkb-article-rank"><?php echo esc_html( $rank ); ?>.</span>
												<a href="<?php echo esc_url( $article_url ); ?>" class="epkb-article-title" target="_blank"><?php echo esc_html( $article->post_title ); ?></a>
											</div>
											<div class="epkb-article-date">
												<?php echo esc_html( $modified_date ); ?>
											</div>
										</div>										<?php
										$rank++;
									}
								} else {
									?>
									<div class="epkb-article-item epkb-article-item--no-views">
										<div class="epkb-article-info">
											<span class="epkb-article-title"><?php esc_html_e( 'Coming Soon', 'echo-knowledge-base' ); ?></span>
										</div>
									</div>
									<?php
								}								?>
							</div>
						</div>

					</div> <!-- End of Article Lists Container -->

						<!-- New Features Showcase -->
						<article class="epkb-card epkb-card--features-showcase">
							<div class="epkb-features-showcase-bg"></div>
							<div class="epkb-features-showcase-content">
								<div class="epkb-features-showcase-header">
									<h2><?php esc_html_e( 'Enhance Your Knowledge Base', 'echo-knowledge-base' ); ?></h2>
								</div>

								<!-- Features Carousel -->
								<div class="epkb-features-carousel-wrapper">
									<div class="epkb-features-carousel">
										<!-- Slide 1: AI Features -->
										<div class="epkb-feature-slide epkb-feature-slide--active" data-slide="0">
											<h4 class="epkb-feature-title" style="color: #7e5bef;"><?php esc_html_e( 'AI Chat and AI Search – Free Core Feature', 'echo-knowledge-base' ); ?></h4>
											<div class="epkb-feature-image-container">
												<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/AI-Pro-Features-List.jpg' ); ?>"
													 alt="<?php esc_attr_e( 'AI Chat and AI Search', 'echo-knowledge-base' ); ?>"
													 class="epkb-feature-image epkb-zoomable-image"
													 data-zoom-src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/AI-Pro-Features-List.jpg' ); ?>">
												<span class="epkb-zoom-icon"><i class="epkbfa epkbfa-search-plus"></i></span>
											</div>
											<p class="epkb-feature-description"><?php esc_html_e( 'Free AI Chat with instant answers and Smart Search with AI-generated responses. Includes training on KB articles.', 'echo-knowledge-base' ); ?> <em class="epkb-feature-pro-note"><?php esc_html_e( 'Pro: Train on posts, pages & custom content.', 'echo-knowledge-base' ); ?></em></p>
										</div>

										<!-- Slide 2: Unlimited Knowledge Bases -->
										<div class="epkb-feature-slide" data-slide="1">
											<h4 class="epkb-feature-title"><?php esc_html_e( 'Unlimited Knowledge Bases Add-on', 'echo-knowledge-base' ); ?></h4>
											<div class="epkb-feature-image-container">
												<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/featured-image-MKB-1.jpg' ); ?>"
													 alt="<?php esc_attr_e( 'Unlimited Knowledge Bases Add-on', 'echo-knowledge-base' ); ?>"
													 class="epkb-feature-image epkb-zoomable-image"
													 data-zoom-src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/featured-image-MKB-1.jpg' ); ?>">
												<span class="epkb-zoom-icon"><i class="epkbfa epkbfa-search-plus"></i></span>
											</div>
											<p class="epkb-feature-description"><?php esc_html_e( 'Create multiple fully independent knowledge bases to organize content for different needs with unlimited Knowledge Bases, divided by department or audience', 'echo-knowledge-base' ); ?></p>
										</div>

										<!-- Slide 3: Advanced Search -->
										<div class="epkb-feature-slide" data-slide="2">
											<h4 class="epkb-feature-title"><?php esc_html_e( 'Advanced Search Add-on', 'echo-knowledge-base' ); ?></h4>
											<div class="epkb-feature-image-container">
												<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/featured-image-ASEA-1.jpg' ); ?>"
													 alt="<?php esc_attr_e( 'Advanced Search Add-on', 'echo-knowledge-base' ); ?>"
													 class="epkb-feature-image epkb-zoomable-image"
													 data-zoom-src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/featured-image-ASEA-1.jpg' ); ?>">
												<span class="epkb-zoom-icon"><i class="epkbfa epkbfa-search-plus"></i></span>
											</div>
											<p class="epkb-feature-description"><?php esc_html_e( 'Enhance user search experience with search analytics, background images, color gradients, search filters and advanced search results page', 'echo-knowledge-base' ); ?></p>
										</div>

										<!-- Slide 4: Elegant Layouts -->
										<div class="epkb-feature-slide" data-slide="3">
											<h4 class="epkb-feature-title"><?php esc_html_e( 'Elegant Layouts Add-on', 'echo-knowledge-base' ); ?></h4>
											<div class="epkb-feature-image-container">
												<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/featured-image-ELAY-1.1.jpg' ); ?>"
													 alt="<?php esc_attr_e( 'Elegant Layouts Add-on', 'echo-knowledge-base' ); ?>"
													 class="epkb-feature-image epkb-zoomable-image"
													 data-zoom-src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/featured-image-ELAY-1.1.jpg' ); ?>">
												<span class="epkb-zoom-icon"><i class="epkbfa epkbfa-search-plus"></i></span>
											</div>
											<p class="epkb-feature-description"><?php esc_html_e( 'Use Grid Layout or Sidebar Layout for KB Main page or combine Basic, Tabs, Grid and Sidebar layouts in many cool ways', 'echo-knowledge-base' ); ?></p>
										</div>

										<!-- Slide 5: Access Manager -->
										<div class="epkb-feature-slide" data-slide="4">
											<h4 class="epkb-feature-title"><?php esc_html_e( 'Access Manager Add-on', 'echo-knowledge-base' ); ?></h4>
											<div class="epkb-feature-image-container">
												<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/featured-image-AMGR-1.jpg' ); ?>"
													 alt="<?php esc_attr_e( 'Access Manager Add-on', 'echo-knowledge-base' ); ?>"
													 class="epkb-feature-image epkb-zoomable-image"
													 data-zoom-src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/featured-image-AMGR-1.jpg' ); ?>">
												<span class="epkb-zoom-icon"><i class="epkbfa epkbfa-search-plus"></i></span>
											</div>
											<p class="epkb-feature-description"><?php esc_html_e( 'Restrict your Articles to certain Groups using KB Categories and assign users to specific KB Roles within Groups to protect your content', 'echo-knowledge-base' ); ?></p>
										</div>

										<!-- Slide 6: Import Export -->
										<div class="epkb-feature-slide" data-slide="5"
											 data-cta-url="https://www.echoknowledgebase.com/wordpress-plugin/kb-import-export/"
											 data-cta-text="<?php echo esc_attr__( 'Get Articles Import and Export', 'echo-knowledge-base' ); ?>">
											<h4 class="epkb-feature-title"><?php esc_html_e( 'Articles CVS/XML Import and EXPORT Add-on', 'echo-knowledge-base' ); ?></h4>
											<div class="epkb-feature-image-container">
												<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/KB-Import-Export-Banner-v2.jpg' ); ?>"
													 alt="<?php esc_attr_e( 'Articles CVS/XML Import and EXPORT Add-on', 'echo-knowledge-base' ); ?>"
													 class="epkb-feature-image epkb-zoomable-image"
													 data-zoom-src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/dashboard/KB-Import-Export-Banner-v2.jpg' ); ?>">
												<span class="epkb-zoom-icon"><i class="epkbfa epkbfa-search-plus"></i></span>
											</div>
											<p class="epkb-feature-description"><?php esc_html_e( 'Powerful import and export plugin to migrate, create and copy articles and images from your Knowledge Base', 'echo-knowledge-base' ); ?></p>
										</div>
									</div>

									<!-- Carousel Navigation -->
									<div class="epkb-carousel-dots">
										<button class="epkb-carousel-btn epkb-carousel-btn--prev" aria-label="<?php esc_attr_e( 'Previous', 'echo-knowledge-base' ); ?>">
											<i class="epkbfa epkbfa-chevron-left"></i>
										</button>
										<span class="epkb-carousel-dot epkb-carousel-dot--active" data-slide="0"></span>
										<span class="epkb-carousel-dot" data-slide="1"></span>
										<span class="epkb-carousel-dot" data-slide="2"></span>
										<span class="epkb-carousel-dot" data-slide="3"></span>
										<span class="epkb-carousel-dot" data-slide="4"></span>
										<span class="epkb-carousel-dot" data-slide="5"></span>
										<button class="epkb-carousel-btn epkb-carousel-btn--next" aria-label="<?php esc_attr_e( 'Next', 'echo-knowledge-base' ); ?>">
											<i class="epkbfa epkbfa-chevron-right"></i>
										</button>
									</div>
								</div>
								
							<div class="epkb-features-cta">
								<a href="https://www.echoknowledgebase.com/bundle-pricing/" target="_blank" class="epkb-btn epkb-btn-features-primary"
								   data-default-url="https://www.echoknowledgebase.com/bundle-pricing/"
								   data-default-text="<?php echo esc_attr__( 'Upgrade to PRO', 'echo-knowledge-base' ); ?>">
									<i class="epkbfa epkbfa-trophy"></i>
									<span class="epkb-features-cta-label"><?php esc_html_e( 'Upgrade to PRO', 'echo-knowledge-base' ); ?></span>
								</a>								<?php
								// Show Free Pro offer for new users only during first two weeks of December
								$is_new_user = $kb_config['first_plugin_version'] === Echo_Knowledge_Base::$version;
								$is_december_promo = (int) gmdate( 'n' ) === 12 && (int) gmdate( 'j' ) <= 14;
								if ( $is_new_user && $is_december_promo ) { ?>
									<a href="https://www.echoknowledgebase.com/pre-sale-question/" target="_blank" class="epkb-btn epkb-btn-free-pro">
										<i class="epkbfa epkbfa-gift"></i>
										<?php esc_html_e( 'Get Free Pro - Ask Us How!', 'echo-knowledge-base' ); ?>
									</a>								<?php 
								} ?>
							</div>
							</div>
						</article>

						<!-- Image Zoom Modal -->
						<div id="epkb-image-zoom-modal" class="epkb-image-zoom-modal">
							<span class="epkb-image-zoom-close">&times;</span>
							<img class="epkb-image-zoom-content" id="epkb-zoomed-image" alt="<?php esc_attr_e( 'Zoomed Image', 'echo-knowledge-base' ); ?>">
						</div>

						<!-- Welcome -->
						<div class="epkb-card epkb-card--welcome">
							<div class="epkb-welcome-content">
								<div class="epkb-welcome-text">
									<header>
										<h2><?php esc_html_e( 'Welcome To Echo Knowledge Base', 'echo-knowledge-base' ); ?></h2>
										<p><?php esc_html_e( 'Join', 'echo-knowledge-base' ); ?> <span class="epkb-highlight-text"><?php esc_html_e( '10,000+ professionals', 'echo-knowledge-base' ); ?></span> <?php esc_html_e( 'who use Echo Knowledge Base to build documentation for their businesses.', 'echo-knowledge-base' ); ?></p>
									</header>
								</div>
							</div>
							
							<div class="epkb-why-us-container">
								<div class="epkb-why-us-item">
									<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/why_us_icon1.png' ); ?>" alt="<?php esc_attr_e( 'Happy customers', 'echo-knowledge-base' ); ?>" class="epkb-why-us-icon">
									<div class="epkb-why-us-text">
										<span class="epkb-why-us-number">10,000+</span>
										<span class="epkb-why-us-description"><?php esc_html_e( 'Happy customers & counting', 'echo-knowledge-base' ); ?></span>
									</div>
								</div>
								
								<div class="epkb-why-us-item">
									<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/why_us_icon2.png' ); ?>" alt="<?php esc_attr_e( 'User reviews', 'echo-knowledge-base' ); ?>" class="epkb-why-us-icon">
									<div class="epkb-why-us-text">
										<span class="epkb-why-us-number">112</span>
										<span class="epkb-why-us-description"><?php esc_html_e( 'User reviews 5-stars rating', 'echo-knowledge-base' ); ?></span>
									</div>
								</div>
								
								<div class="epkb-why-us-item">
									<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/why_us_icon3.png' ); ?>" alt="<?php esc_attr_e( 'Free support', 'echo-knowledge-base' ); ?>" class="epkb-why-us-icon">
									<div class="epkb-why-us-text">
										<span class="epkb-why-us-number"><?php esc_html_e( 'Free Support', 'echo-knowledge-base' ); ?></span>
										<span class="epkb-why-us-description"><?php esc_html_e( '7 days/week', 'echo-knowledge-base' ); ?></span>
									</div>
								</div>
							</div>
						</div>

					<!-- AI Chatbot -->
					<article class="epkb-card epkb-card--chatbot">
						<div class="epkb-chatbot-content">
							<div class="epkb-chatbot-text">
								<div class="epkb-chatbot-heading">		<?php
									if ( EPKB_AI_Utilities::is_ai_configured() ) { ?>
									<h2><span class="epkb-magic-icon"><i class="epkbfa epkbfa-magic"></i></span> <span class="epkb-ai-addon-text" style="white-space:nowrap;"><?php esc_html_e( 'FREE AI Chat & Search', 'echo-knowledge-base' ); ?></span></h2>		<?php
									} else { ?>
									<h2><span class="epkb-magic-icon"><i class="epkbfa epkbfa-magic"></i></span> <span class="epkb-ai-addon-text" style="white-space:nowrap;"><?php esc_html_e( 'AI Features (Optional)', 'echo-knowledge-base' ); ?></span></h2>		<?php
									} ?>
								</div>
								<div class="epkb-chatbot-description">	<?php
									if ( EPKB_AI_Utilities::is_ai_configured() ) { ?>
									<p><?php esc_html_e( 'Transform your knowledge base with AI-powered chat and search. Our intelligent chatbot answers visitor questions timely, while AI Search delivers highly relevant results by understanding user intent. Reduce support tickets, improve user satisfaction, and let AI handle queries while your team focuses on complex issues.', 'echo-knowledge-base' ); ?></p>		<?php
									} else { ?>
									<p><?php esc_html_e( 'This Knowledge Base AI is not enabled. AI features are optional and turned off by default. AI will only work after you manually enable it and add your own API key.', 'echo-knowledge-base' ); ?></p>		<?php
									} ?>
								</div>
								<div class="epkb-chatbot-buttons">
									<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-kb-ai-features&active_tab=chat' ) ); ?>" class="epkb-btn epkb-btn-primary-outline">
										<?php esc_html_e( 'Get FREE AI Chat', 'echo-knowledge-base' ); ?>
									</a>
									<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-kb-ai-features&active_tab=ai-search' ) ); ?>" class="epkb-btn epkb-btn-primary-outline epkb-btn-ai-search">
										<?php esc_html_e( 'Get FREE AI Search', 'echo-knowledge-base' ); ?>
									</a>
								</div>
							</div>
							<div class="epkb-chatbot-image">
								<figure>
									<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/ai-chatbot-image-1.png' ); ?>" alt="<?php esc_attr_e( 'AI Chatbot screenshot', 'echo-knowledge-base' ); ?>">
								</figure>
							</div>
						</div>
					</article>
					
					</div> <!-- End of Main Content -->
					
					<!-- Sidebar (30%) -->
					<div class="epkb-sidebar">

					<!-- Quick Actions - Hidden for now -->
					<?php /* Temporarily hidden
					<aside class="epkb-card epkb-card--quick-actions">
						<div class="epkb-quick-actions-header">
							<h3><?php esc_html_e( 'Quick Actions', 'echo-knowledge-base' ); ?></h3>
						</div>
						<div class="epkb-quick-actions-list">
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=training-data' ) ); ?>" class="epkb-quick-action-item">
								<span class="epkb-quick-action-icon epkbfa epkbfa-sync"></span>
								<div class="epkb-quick-action-content">
									<h4><?php esc_html_e( 'Sync Training Data', 'echo-knowledge-base' ); ?></h4>
									<p><?php esc_html_e( 'Update your AI knowledge base with latest content', 'echo-knowledge-base' ); ?></p>
								</div>
							</a>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=general-settings' ) ); ?>" class="epkb-quick-action-item">
								<span class="epkb-quick-action-icon epkbfa epkbfa-cog"></span>
								<div class="epkb-quick-action-content">
									<h4><?php esc_html_e( 'Configure AI Settings', 'echo-knowledge-base' ); ?></h4>
									<p><?php esc_html_e( 'Adjust AI behavior and response settings', 'echo-knowledge-base' ); ?></p>
								</div>
							</a>
							<a href="https://www.echoknowledgebase.com/docs/ai-getting-started" target="_blank" class="epkb-quick-action-item">
								<span class="epkb-quick-action-icon epkbfa epkbfa-play-circle"></span>
								<div class="epkb-quick-action-content">
									<h4><?php esc_html_e( 'Getting Started Guide', 'echo-knowledge-base' ); ?></h4>
									<p><?php esc_html_e( 'Learn how to set up and configure AI features', 'echo-knowledge-base' ); ?></p>
								</div>
							</a>
							<a href="#" onclick="if(document.querySelector('.epkb-help-chat-button')) { document.querySelector('.epkb-help-chat-button').click(); } else { alert('AI Help is loading...'); } return false;" class="epkb-quick-action-item epkb-quick-action--ai">
								<span class="epkb-quick-action-icon epkbfa epkbfa-comments"></span>
								<div class="epkb-quick-action-content">
									<h4><?php esc_html_e( 'Get Instant AI Help', 'echo-knowledge-base' ); ?> <span style="color: #ff3333; font-size: 10px; font-weight: bold; margin-left: 5px;"><?php esc_html_e( 'NEW', 'echo-knowledge-base' ); ?></span></h4>
									<p><?php esc_html_e( 'Ask questions and get instant answers', 'echo-knowledge-base' ); ?></p>
								</div>
							</a>
						</div>
					</aside>
					*/ ?>

					<?php
					$shared_kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
					$quizzes_demo_url = EPKB_Quizzes_Utilities::get_demo_quiz_url();

					if ( $shared_kb_config['glossary_enable'] !== 'on' ) {
						$this->display_feature_promo_card( array(
							'feature_key'  => 'glossary',
							'feature_name' => esc_html__( 'Glossary', 'echo-knowledge-base' ),
							'icon_class'   => 'epkbfa epkbfa-book',
							'title'        => esc_html__( 'Glossary Feature', 'echo-knowledge-base' ),
							'features'     => array(
								esc_html__( 'Define terms with rich descriptions', 'echo-knowledge-base' ),
								esc_html__( 'Auto-highlight terms in articles with tooltips', 'echo-knowledge-base' ),
								esc_html__( 'Generate terms with AI', 'echo-knowledge-base' ),
								esc_html__( 'Glossary Index shortcode and block', 'echo-knowledge-base' ),
							),
							'button_label' => esc_html__( 'Enable Glossary', 'echo-knowledge-base' ),
							'dialog_title' => esc_html__( 'Glossary Enabled', 'echo-knowledge-base' ),
							'dialog_message' => esc_html__( 'You can find Glossary in the new admin menu on the left after the next page load, or use the button below to open it now.', 'echo-knowledge-base' ),
							'dialog_open_label' => esc_html__( 'Open Glossary', 'echo-knowledge-base' ),
							'dialog_cancel_label' => esc_html__( 'Stay on Dashboard', 'echo-knowledge-base' ),
							'dialog_open_url' => admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-glossary#glossary-terms' ),
						) );
					}

					if ( $shared_kb_config['quizzes_enable'] !== 'on' ) {
						$this->display_feature_promo_card( array(
							'feature_key'  => 'quizzes',
							'feature_name' => esc_html__( 'Quizzes', 'echo-knowledge-base' ),
							'icon_class'   => 'epkbfa epkbfa-check-square-o',
							'title'        => esc_html__( 'Quizzes Feature', 'echo-knowledge-base' ),
							'features'     => array(
								esc_html__( 'Create quizzes linked to KB articles', 'echo-knowledge-base' ),
								esc_html__( 'Add custom questions, answers, and explanations', 'echo-knowledge-base' ),
								esc_html__( 'Generate quiz drafts with AI when available', 'echo-knowledge-base' ),
								esc_html__( 'Show published quizzes below article content', 'echo-knowledge-base' ),
							),
							'button_label' => esc_html__( 'Enable Quizzes', 'echo-knowledge-base' ),
							'dialog_title' => esc_html__( 'Quizzes Enabled', 'echo-knowledge-base' ),
							'dialog_message' => esc_html__( 'You can find Quizzes in the new admin menu on the left after the next page load, or use the button below to open it now.', 'echo-knowledge-base' ),
							'dialog_open_label' => esc_html__( 'Open Quizzes', 'echo-knowledge-base' ),
							'dialog_cancel_label' => esc_html__( 'Stay on Dashboard', 'echo-knowledge-base' ),
							'dialog_open_url' => admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-quizzes#quizzes-editor' ),
							'secondary_link_label' => esc_html__( 'See Demo Quiz', 'echo-knowledge-base' ),
							'secondary_link_url' => $quizzes_demo_url,
						) );
					} ?>

					<?php
					$quizzes_dashboard_url          = admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-quizzes#quizzes-editor' );
					$glossary_dashboard_url         = admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-glossary#glossary-terms' );
					$pdf_to_articles_dashboard_url = admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-kb-configuration#tools__convert' );
					$ai_training_data_dashboard_url = admin_url( 'edit.php?post_type=' . $post_type . '&page=epkb-kb-ai-features&active_tab=training-data' );
					?>

					<!-- What's New -->
					<aside class="epkb-card epkb-card--whatsnew">
						<div class="epkb-whatsnew-header">
							<h3><?php esc_html_e( 'What\'s New', 'echo-knowledge-base' ); ?></h3>
						</div>
						<ul class="epkb-whatsnew-list">
							<li class="epkb-whatsnew-item epkb-whatsnew-item--new">
								<span class="epkb-whatsnew-badge"><?php esc_html_e( 'NEW', 'echo-knowledge-base' ); ?></span>
								<div class="epkb-whatsnew-content">
									<span class="epkb-whatsnew-date"><?php esc_html_e( 'March 29, 2026', 'echo-knowledge-base' ); ?></span>
									<strong><?php esc_html_e( 'Quizzes', 'echo-knowledge-base' ); ?></strong>
									<span><?php esc_html_e( 'Create quizzes linked to KB articles and generate quiz drafts with AI when available.', 'echo-knowledge-base' ); ?></span>
									<div class="epkb-whatsnew-links">
										<a class="epkb-whatsnew-link" href="<?php echo esc_url( $quizzes_dashboard_url ); ?>"><?php esc_html_e( 'Open Quizzes', 'echo-knowledge-base' ); ?></a>
										<a class="epkb-whatsnew-link" href="<?php echo esc_url( $quizzes_demo_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'See Demo Quiz', 'echo-knowledge-base' ); ?></a>
									</div>
								</div>
							</li>
							<li class="epkb-whatsnew-item epkb-whatsnew-item--new">
								<span class="epkb-whatsnew-badge"><?php esc_html_e( 'PRO', 'echo-knowledge-base' ); ?></span>
								<div class="epkb-whatsnew-content">
									<span class="epkb-whatsnew-date"><?php esc_html_e( 'March 9, 2026', 'echo-knowledge-base' ); ?></span>
									<strong><?php esc_html_e( 'PDF to Articles', 'echo-knowledge-base' ); ?></strong>
									<span><?php esc_html_e( 'Upload PDF files and convert them into KB articles with AI-powered formatting.', 'echo-knowledge-base' ); ?></span>
									<a class="epkb-whatsnew-link" href="<?php echo esc_url( $pdf_to_articles_dashboard_url ); ?>"><?php esc_html_e( 'Convert PDFs to Articles', 'echo-knowledge-base' ); ?></a>
								</div>
							</li>
							<li class="epkb-whatsnew-item epkb-whatsnew-item--new">
								<span class="epkb-whatsnew-badge"><?php esc_html_e( 'PRO', 'echo-knowledge-base' ); ?></span>
								<div class="epkb-whatsnew-content">
									<span class="epkb-whatsnew-date"><?php esc_html_e( 'March 9, 2026', 'echo-knowledge-base' ); ?></span>
									<strong><?php esc_html_e( 'PDF Uploads', 'echo-knowledge-base' ); ?></strong>
									<span><?php esc_html_e( 'Upload PDFs directly into AI Data Collections for AI Chat and Search.', 'echo-knowledge-base' ); ?></span>
									<a class="epkb-whatsnew-link" href="<?php echo esc_url( $ai_training_data_dashboard_url ); ?>"><?php esc_html_e( 'Open AI Training Data', 'echo-knowledge-base' ); ?></a>
								</div>
							</li>
							<li class="epkb-whatsnew-item epkb-whatsnew-item--new">
								<span class="epkb-whatsnew-badge"><?php esc_html_e( 'NEW', 'echo-knowledge-base' ); ?></span>
								<div class="epkb-whatsnew-content">
									<span class="epkb-whatsnew-date"><?php esc_html_e( 'February 21, 2026', 'echo-knowledge-base' ); ?></span>
									<strong><?php esc_html_e( 'Glossary', 'echo-knowledge-base' ); ?></strong>
									<span><?php esc_html_e( 'Add glossary terms with definitions that are automatically highlighted in your articles with interactive tooltips.', 'echo-knowledge-base' ); ?></span>
									<a class="epkb-whatsnew-link" href="<?php echo esc_url( $glossary_dashboard_url ); ?>"><?php esc_html_e( 'Open Glossary', 'echo-knowledge-base' ); ?></a>
								</div>
							</li>
							<li class="epkb-whatsnew-item epkb-whatsnew-item--new">
								<span class="epkb-whatsnew-badge"><?php esc_html_e( 'PRO', 'echo-knowledge-base' ); ?></span>
								<div class="epkb-whatsnew-content">
									<span class="epkb-whatsnew-date"><?php esc_html_e( 'February 21, 2026', 'echo-knowledge-base' ); ?></span>
									<strong><?php esc_html_e( 'PDF to Notes', 'echo-knowledge-base' ); ?></strong>
									<span><?php esc_html_e( 'Upload PDF files and convert them into AI training notes.', 'echo-knowledge-base' ); ?></span>
									<a class="epkb-whatsnew-link" href="<?php echo esc_url( $ai_training_data_dashboard_url ); ?>"><?php esc_html_e( 'Open AI Training Data', 'echo-knowledge-base' ); ?></a>
								</div>
							</li>

							<?php /* Temporarily hidden - backend help chat
							<li class="epkb-whatsnew-item">
								<div class="epkb-whatsnew-content">
									<strong><?php esc_html_e( 'Backend Help Chat', 'echo-knowledge-base' ); ?></strong>
									<span><?php esc_html_e( 'Instant AI-powered assistance', 'echo-knowledge-base' ); ?></span>
								</div>
							</li>
							*/ ?>
						</ul>
					</aside>
					
					</div> <!-- End of Sidebar -->

				</section>

				<!-- ================= Quick‑Links ================= -->
				<section class="epkb-quicklinks-row">

					<a href="https://www.echoknowledgebase.com/documentation/" target="_blank" class="epkb-ql-card epkb-ql-card--documentation">
						<div class="epkb-ql-icon-container">
							<span class="epkb-ql-icon epkbfa epkbfa-book"></span>
						</div>
						<h3><?php esc_html_e( 'Documentation', 'echo-knowledge-base' ); ?></h3>
						<p><?php esc_html_e( 'Get started by spending some time with the documentation and build an awesome Knowledge Base for your customers.', 'echo-knowledge-base' ); ?></p>
						<span class="epkb-action-text"><?php esc_html_e( 'Read Me', 'echo-knowledge-base' ); ?></span>
					</a>

					<div class="epkb-ql-card epkb-ql-card--help epkb-ql-card--split">
						<div class="epkb-ql-icon-container">
							<span class="epkb-ql-icon epkbfa epkbfa-comments"></span>
						</div>
						<h3><?php esc_html_e( 'Need Help?', 'echo-knowledge-base' ); ?></h3>
						<p><?php esc_html_e( 'Get instant answers or contact our support team.', 'echo-knowledge-base' ); ?></p>
						<div class="epkb-help-options">
							<?php /* Temporarily hidden - backend help chat
							<button onclick="if(document.querySelector('.epkb-help-chat-button')) { document.querySelector('.epkb-help-chat-button').click(); } else { alert('AI Help is loading...'); } return false;" class="epkb-btn epkb-btn-ai-help">
								<span class="dashicons dashicons-editor-help"></span>
								<?php esc_html_e( 'AI Help (Instant)', 'echo-knowledge-base' ); ?>
								<span class="epkb-beta-tag"><?php esc_html_e( 'BETA', 'echo-knowledge-base' ); ?></span>
							</button>
							*/ ?>
							<a href="https://www.echoknowledgebase.com/contact-us/" target="_blank" class="epkb-btn epkb-btn-human-support">
								<span class="dashicons dashicons-admin-users"></span>
								<?php esc_html_e( 'Human Support', 'echo-knowledge-base' ); ?>
							</a>
						</div>
					</div>

					<a href="https://wordpress.org/support/plugin/echo-knowledge-base/reviews/" target="_blank" class="epkb-ql-card epkb-ql-card--love">
						<div class="epkb-ql-icon-container">
							<span class="epkb-ql-icon epkbfa epkbfa-heart"></span>
						</div>
						<h3><?php esc_html_e( 'Show Your Love', 'echo-knowledge-base' ); ?></h3>
						<p><?php esc_html_e( 'We love to have you in Echo Knowledge Base family. Take your 2 minutes to review the plugin and spread the love!', 'echo-knowledge-base' ); ?></p>
						<span class="epkb-action-text"><?php esc_html_e( 'Review Now', 'echo-knowledge-base' ); ?></span>
					</a>

				</section>

			</div>
		</div>    <?php
	}


	/**
	 * Display feature promotion card.
	 *
	 * @param array $args
	 */
	private function display_feature_promo_card( $args ) {

		$feature_key = empty( $args['feature_key'] ) ? '' : $args['feature_key'];
		$feature_name = empty( $args['feature_name'] ) ? '' : $args['feature_name'];
		$icon_class = empty( $args['icon_class'] ) ? '' : $args['icon_class'];
		$title = empty( $args['title'] ) ? '' : $args['title'];
		$features = empty( $args['features'] ) || ! is_array( $args['features'] ) ? array() : $args['features'];
		$button_label = empty( $args['button_label'] ) ? '' : $args['button_label'];
		$dialog_title = empty( $args['dialog_title'] ) ? '' : $args['dialog_title'];
		$dialog_message = empty( $args['dialog_message'] ) ? '' : $args['dialog_message'];
		$dialog_open_label = empty( $args['dialog_open_label'] ) ? '' : $args['dialog_open_label'];
		$dialog_cancel_label = empty( $args['dialog_cancel_label'] ) ? '' : $args['dialog_cancel_label'];
		$dialog_open_url = empty( $args['dialog_open_url'] ) ? '' : $args['dialog_open_url'];
		$secondary_link_label = empty( $args['secondary_link_label'] ) ? '' : $args['secondary_link_label'];
		$secondary_link_url = empty( $args['secondary_link_url'] ) ? '' : $args['secondary_link_url'];

		if ( empty( $feature_key ) || empty( $feature_name ) || empty( $title ) || empty( $button_label ) ) {
			return;
		} ?>

		<aside id="epkb-card--feature-promo-<?php echo esc_attr( $feature_key ); ?>" class="epkb-card epkb-dashboard-feature-promo">
			<div class="epkb-dashboard-feature-promo__header">
				<span class="epkb-dashboard-feature-promo__icon <?php echo esc_attr( $icon_class ); ?>"></span>
				<h3><?php echo esc_html( $title ); ?></h3>
			</div>
			<div class="epkb-dashboard-feature-promo__body">
				<ul class="epkb-dashboard-feature-promo__features epkb-body__check-mark-list-container">
					<?php foreach ( $features as $feature ) { ?>
						<li class="epkb-check-mark-list__item">
							<span class="epkb-check-mark-list__item__icon epkbfa epkbfa-check"></span>
							<span class="epkb-check-mark-list__item__text"><?php echo esc_html( $feature ); ?></span>
						</li>
					<?php } ?>
				</ul>
				<button
					class="epkb-btn epkb-dashboard-feature-promo__button epkb-btn-dashboard-feature-enable"
					type="button"
					data-action="<?php echo esc_attr( 'epkb_enable_' . $feature_key ); ?>"
					data-feature-label="<?php echo esc_attr( $feature_name ); ?>"
					data-button-label="<?php echo esc_attr( $button_label ); ?>"
					data-loading-label="<?php echo esc_attr( sprintf( esc_html__( 'Enabling %s...', 'echo-knowledge-base' ), $feature_name ) ); ?>"
					data-dialog-title="<?php echo esc_attr( $dialog_title ); ?>"
					data-dialog-message="<?php echo esc_attr( $dialog_message ); ?>"
					data-dialog-open-label="<?php echo esc_attr( $dialog_open_label ); ?>"
					data-dialog-cancel-label="<?php echo esc_attr( $dialog_cancel_label ); ?>"
					data-dialog-open-url="<?php echo esc_url( $dialog_open_url ); ?>"
				><?php echo esc_html( $button_label ); ?></button>
				<?php if ( ! empty( $secondary_link_label ) && ! empty( $secondary_link_url ) ) { ?>
					<a href="<?php echo esc_url( $secondary_link_url ); ?>" class="epkb-btn-link epkb-dashboard-feature-promo__link" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( $secondary_link_label ); ?>
						<span class="epkbfa epkbfa-external-link"></span>
					</a>
				<?php } ?>
				<div class="epkb-dashboard-feature-promo__message" style="display: none;"></div>
			</div>
		</aside> <?php
	}

	/**
	 * Enqueue scripts for dashboard page
	 */
	public function enqueue_dashboard_scripts() {
		$screen = get_current_screen();
		if ( !$screen || $screen->id !== 'toplevel_page_epkb-dashboard' ) {
			return;
		}

		// Ensure WordPress admin scripts are loaded
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		// jquery-ui-dialog removed - vote form is now inline
		wp_enqueue_script( 'jquery-effects-core' );
		wp_enqueue_script( 'jquery-effects-bounce' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

		// Load plugin admin scripts
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'epkb-admin-plugin-pages-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-pages' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
		wp_enqueue_script( 'epkb-admin-plugin-pages-ui', Echo_Knowledge_Base::$plugin_url . 'js/admin-ui' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );

		// Localize script with epkb_vars to prevent JavaScript errors
		wp_localize_script( 'epkb-admin-plugin-pages-ui', 'epkb_vars', array(
			'msg_try_again' => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
			'error_occurred' => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (151)',
			'not_saved' => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (152)',
			'unknown_error' => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (1783)',
			'reload_try_again' => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
			'save_config' => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
			'input_required' => esc_html__( 'Input is required', 'echo-knowledge-base' ),
			'sending_feedback' => esc_html__( 'Sending feedback', 'echo-knowledge-base' ) . '...',
			'changing_debug' => esc_html__( 'Changing debug', 'echo-knowledge-base' ) . '...',
			'help_text_coming' => esc_html__( 'Help text is coming soon.', 'echo-knowledge-base' ),
			'nonce' => wp_create_nonce( '_wpnonce_epkb_ajax_action' ),
			'msg_reading_posts' => esc_html__( 'Reading items', 'echo-knowledge-base' ) . '...',
			'msg_confirm_kb' => esc_html__( 'Please confirm Knowledge Base to import into.', 'echo-knowledge-base' ),
			'msg_confirm_backup' => esc_html__( 'Please confirm you backed up your database or understand that import can potentially make undesirable changes.', 'echo-knowledge-base' ),
			'msg_empty_post_type' => esc_html__( 'Please select post type.', 'echo-knowledge-base' ),
			'msg_nothing_to_convert' => esc_html__( 'No posts to convert.', 'echo-knowledge-base' ),
			'msg_select_article' => esc_html__( 'Please select posts to convert.', 'echo-knowledge-base' ),
			'msg_articles_converted' => esc_html__( 'Items converted', 'echo-knowledge-base' ),
			'msg_converting' => esc_html__( 'Converting, please wait...', 'echo-knowledge-base' ),
			'on_kb_main_page_layout' => esc_html__( 'First, the selected layout will be saved. Then, the page will reload and you can see the layout change on the KB frontend.', 'echo-knowledge-base' ),
			'on_kb_templates' => esc_html__( 'First, the KB Base Template will be enabled. Then the page will reload after which you can see the layout change on the KB frontend.', 'echo-knowledge-base' ),
			'on_current_theme_templates' => esc_html__( 'First, the Current Theme Template will be enabled. Then the page will reload after which you can see the layout change on the KB frontend. If you have issues using the Current Theme Template, switch back to the KB Template or contact us for help.', 'echo-knowledge-base' ),
			'on_article_search_sync_toggle' => esc_html__( 'First, the current settings will be saved. Then, the page will reload.', 'echo-knowledge-base' ),
			'on_article_search_toggle' => esc_html__( 'First, the current settings will be saved. Then, the page will reload.', 'echo-knowledge-base' ),
			'on_asea_presets_selection' => esc_html__( 'First, the current settings will be saved. Then, the page will reload.', 'echo-knowledge-base' ),
			'on_faqs_presets_selection' => esc_html__( 'First, the current settings will be saved. Then, the page will reload.', 'echo-knowledge-base' ),
			'on_archive_page_v3_toggle' => esc_html__( 'First, the current settings will be saved. Then, the page will reload.', 'echo-knowledge-base' ),
			'preview_not_available' => esc_html__( 'Preview functionality will be implemented soon.', 'echo-knowledge-base' ),
			'msg_empty_input' => esc_html__( 'Missing input', 'echo-knowledge-base' ),
			'msg_no_key_admin' => esc_html__( 'You have no API key. Please add it here', 'echo-knowledge-base' ),
			'msg_no_key' => esc_html__( 'You have no API key.', 'echo-knowledge-base' ),
			'ai_help_button_title' => esc_html__( 'AI Help', 'echo-knowledge-base' ),
			'msg_ai_help_loading' => esc_html__( 'Processing...', 'echo-knowledge-base' ),
			'msg_ai_copied_to_clipboard' => esc_html__( 'Copied to clipboard', 'echo-knowledge-base' ),
			'copied_text' => esc_html__( 'Copied!', 'echo-knowledge-base' ),
			'group_selected_singular' => esc_html__( 'group selected', 'echo-knowledge-base' ),
			'group_selected_plural' => esc_html__( 'groups selected', 'echo-knowledge-base' ),
		) );
	}

	/**
	 * Check if Setup Wizard button should be shown (first 2 weeks after installation)
	 * @return bool
	 */
	private function should_show_setup_wizard() {

		// Get the installation date from KB config
		$kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
		$install_date = empty( $kb_config['plugin_install_date'] ) ? '' : $kb_config['plugin_install_date'];

		// If no install date set, this is a new installation
		if ( empty( $install_date ) ) {
			return true;
		}

		// Calculate if we're within 2 weeks (14 days) of installation
		$install_timestamp = strtotime( $install_date );
		if ( $install_timestamp === false ) {
			return false; // Invalid date
		}

		$two_weeks_in_seconds = 14 * 24 * 60 * 60;
		$time_since_install = current_time( 'timestamp' ) - $install_timestamp;

		return $time_since_install <= $two_weeks_in_seconds;
	}

	/**
	 * Get week numbers for the current month
	 * @return array
	 */
	private function get_month_weeks() {
		$current_month = wp_date( 'n' );
		$current_year = wp_date( 'Y' );
		$weeks = array();

		// Get first and last day of month
		$first_day = mktime( 0, 0, 0, $current_month, 1, $current_year );
		$last_day = mktime( 0, 0, 0, (int)$current_month + 1, 0, $current_year );

		// Get week numbers
		$first_week = wp_date( 'W', $first_day );
		$last_week = wp_date( 'W', $last_day );

		// Handle year transition
		if ( $last_week < $first_week ) {
			// December to January transition
			for ( $w = $first_week; $w <= 53; $w++ ) {
				$weeks[] = $w;
			}
			for ( $w = 1; $w <= $last_week; $w++ ) {
				$weeks[] = $w;
			}
		} else {
			for ( $w = $first_week; $w <= $last_week; $w++ ) {
				$weeks[] = $w;
			}
		}

		return $weeks;
	}
	
	/**
	 * Get add-ons carousel items HTML
	 *
	 * @return string
	 */
	private function get_addons_carousel_items() {
		
		$addons = array(
			array(
				'title'             => esc_html__( 'AI Features', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'Smart AI-powered support', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2025/08/AI-Pro-Features-List.jpg',
				// translators: %1$s, %2$s, %3$s, %4$s, %5$s, %6$s are HTML strong tags
				'desc'              => sprintf( esc_html__( '%1$sAI Chat%2$s with instant answers, %3$sSmart Search%4$s with AI-generated responses, and %5$sAdvanced Training%6$s on posts, pages & custom content.', 'echo-knowledge-base' ), '<strong>', '</strong>', '<strong>', '</strong>', '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/ai-features/?utm_source=plugin&utm_medium=dashboard&utm_content=carousel&utm_campaign=ai-features',
			),
			array(
				'title'          => esc_html__( 'Unlimited Knowledge Bases', 'echo-knowledge-base' ),
				'special_note'   => esc_html__( 'Expand your documentation', 'echo-knowledge-base' ),
				'img'            => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-MKB-1.jpg',
				'desc'           =>
					esc_html__( 'Create multiple fully independent knowledge bases to organize content for different needs. Key features include:', 'echo-knowledge-base' )
					. '<br>' . esc_html__( '1. One plugin, unlimited Knowledge Bases.', 'echo-knowledge-base' )
					. '<br>' . esc_html__( '2. Divide knowledge bases by department or audience.', 'echo-knowledge-base' )
					. '<br>' . esc_html__( '3. Search stays in its lane, results only from the selected KB.', 'echo-knowledge-base' )
					. '<br>' . esc_html__( "4. Switch KBs with one click.", 'echo-knowledge-base' ),
				'learn_more_url' => 'https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/?utm_source=plugin&utm_medium=dashboard&utm_content=carousel&utm_campaign=multiple-kbs',
			),	
			array(
				'title'          => esc_html__( 'Advanced Search', 'echo-knowledge-base' ),
				'special_note'   => esc_html__( 'Enhance and analyze user searches', 'echo-knowledge-base' ),
				'img'            => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-ASEA-1.jpg',
				'desc'           =>
					esc_html__( "Enhance users' search experience and view search analytics, including popular searches and no results searches. Key features include:", 'echo-knowledge-base' )
					. '<br>' . esc_html__( '1. Background images, color gradients', 'echo-knowledge-base' )
					. '<br>' . esc_html__( '2. Search Analytics', 'echo-knowledge-base' )
					. '<br>' . esc_html__( '3. Search filters', 'echo-knowledge-base' )
					. '<br>' . esc_html__( '4. Search results page', 'echo-knowledge-base' ),
				'learn_more_url' => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/?utm_source=plugin&utm_medium=dashboard&utm_content=carousel&utm_campaign=advanced-search',
			),
			array(
				'title'             => esc_html__( 'Elegant Layouts', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'More ways to design your KB', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-ELAY-1.1.jpg',
				// translators: %1$s, %2$s, %3$s, %4$s are HTML strong tags
				'desc'              => sprintf( esc_html__( 'Use %1$sGrid Layout%2$s or %3$sSidebar Layout%4$s for KB Main page or combine Basic, Tabs, Grid and Sidebar layouts in many cool ways.', 'echo-knowledge-base' ), '<strong>', '</strong>', '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/?utm_source=plugin&utm_medium=dashboard&utm_content=carousel&utm_campaign=elegant-layouts',
			),
			array(
				'title'             => esc_html__( 'Access Manager', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'Protect your KB content', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-AMGR-1.jpg',
				'desc'              => esc_html__( 'Restrict your Articles to certain Groups using KB Categories. Assign users to specific KB Roles within Groups.', 'echo-knowledge-base' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/access-manager/?utm_source=plugin&utm_medium=dashboard&utm_content=carousel&utm_campaign=access-manager'
			),
			array(
				'title'             => esc_html__( 'Migrate, Copy, Import and Export', 'echo-knowledge-base' ),
				'special_note'      => esc_html__( 'Import, export and copy Articles', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/edd/2022/01/KB-Import-Export-Banner-v2.jpg',
				'desc'              => esc_html__( "Powerful import and export plugin to migrate, create and copy articles and images from your Knowledge Base.", 'echo-knowledge-base' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/kb-import-export/?utm_source=plugin&utm_medium=dashboard&utm_content=carousel&utm_campaign=kb-import-export',
				'cta_text'          => esc_html__( 'Get Articles Import and Export', 'echo-knowledge-base' ),
				'cta_url'           => 'https://www.echoknowledgebase.com/wordpress-plugin/kb-import-export/',
			),
		);
		
		$discount_coupon = EPKB_AI_PRO_Features_Tab::get_discount_coupon();

		$html = '';
		foreach ( $addons as $addon ) {
			$addon_json = htmlspecialchars( json_encode( $addon ), ENT_QUOTES, 'UTF-8' );
			$html .= '<div class="epkb-carousel-item" data-addon=\'' . $addon_json . '\'>';
			$html .= '<img src="' . esc_url( $addon['img'] ) . '" alt="' . esc_attr( $addon['title'] ) . '">';

			// Show discount coupon for AI Features carousel item
			if ( $addon['title'] === esc_html__( 'AI Features', 'echo-knowledge-base' ) && ! empty( $discount_coupon['discount_percentage'] ) ) {
				$html .= '<div class="epkb-ad-discount-coupon">';
				$html .= '<span class="epkb-ad-discount-badge">' . esc_html( $discount_coupon['discount_percentage'] . '% ' . __( 'OFF', 'echo-knowledge-base' ) ) . '</span>';
				$html .= '<span class="epkb-ad-discount-text">' . esc_html__( 'Use code:', 'echo-knowledge-base' ) . ' <code>' . esc_html( $discount_coupon['coupon_code'] ) . '</code></span>';
				$html .= '<button type="button" class="epkb-ad-discount-copy-btn" data-code="' . esc_attr( $discount_coupon['coupon_code'] ) . '">' . esc_html__( 'Copy', 'echo-knowledge-base' ) . '</button>';
				$html .= '</div>';
			}

			$cta_url = empty( $addon['cta_url'] ) ? 'https://www.echoknowledgebase.com/bundle-pricing/' : $addon['cta_url'];
			$cta_text = empty( $addon['cta_text'] ) ? esc_html__( 'Upgrade to PRO', 'echo-knowledge-base' ) : $addon['cta_text'];

			$html .= '<a href="' . esc_url( $cta_url ) . '" target="_blank" class="epkb-btn epkb-btn-upgrade-pro">';
			$html .= '<span class="epkbfa epkbfa-trophy"></span>';
			$html .= esc_html( $cta_text );
			$html .= '</a>';
			$html .= '</div>';
		}

		return $html;
	}
	
	/**
	 * AJAX handler to enable the Glossary feature
	 */
	public function ajax_enable_glossary() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_epkb_ajax_action' );

		$this->enable_default_kb_feature(
			'glossary_enable',
			__( 'Failed to enable Glossary. Please try again.', 'echo-knowledge-base' ),
			__( 'Glossary has been enabled.', 'echo-knowledge-base' )
		);
	}

	/**
	 * AJAX handler to enable the Quizzes feature.
	 */
	public function ajax_enable_quizzes() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_epkb_ajax_action' );

		$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $kb_config ) ) {
			wp_send_json_error( __( 'Failed to enable Quizzes. Please try again.', 'echo-knowledge-base' ) );
		}

		$kb_config['quizzes_enable'] = 'on';

		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $kb_config );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( __( 'Failed to enable Quizzes. Please try again.', 'echo-knowledge-base' ) );
		}

		wp_send_json_success( array(
			'message'             => __( 'Quizzes have been enabled.', 'echo-knowledge-base' ),
			'show_interest_modal' => EPKB_Quizzes_Utilities::should_show_interest_modal(),
		) );
	}

	/**
	 * Enable a feature stored on the default KB configuration.
	 *
	 * @param string $feature_key
	 * @param string $error_message
	 * @param string $success_message
	 */
	private function enable_default_kb_feature( $feature_key, $error_message, $success_message ) {

		$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $kb_config ) ) {
			wp_send_json_error( $error_message );
		}

		$kb_config[ $feature_key ] = 'on';

		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $kb_config );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $error_message );
		}

		wp_send_json_success( array( 'message' => $success_message ) );
	}
}
