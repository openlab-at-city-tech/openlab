<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display FAQs page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_FAQs_Page {

	/**
	 * Display FAQs page
	 */
	public function display_faqs_page() {

		$admin_page_views = self::get_regular_views_config();

		EPKB_HTML_Admin::admin_page_header();   ?>

		<!-- Admin Page Wrap -->
		<div id="ekb-admin-page-wrap">

			<div id="epkb-kb-faqs-page-container">   <?php

				// FAQs editor
				EPKB_FAQs_Page::display_single_faq_editor();

				/**
				 * ADMIN HEADER (KB logo and list of KBs dropdown)
				 */
				EPKB_HTML_Admin::admin_header( [], [], 'logo' );

				/**
				 * ADMIN TOOLBAR
				 */
				EPKB_HTML_Admin::admin_primary_tabs( $admin_page_views );

				/**
				 * SHORTCODE GENERATOR - appears only on Shortcode page, above the secondary tabs
				 */
				$page = EPKB_Utilities::post( 'page' );
				$page_tab = EPKB_Utilities::post( 'tab' );
				if ( $page == 'epkb-faqs' && ( $page_tab == 'epkb-faqs' || $page_tab == '' ) ) {
					self::display_shortcode_generator();
				}

				/**
				 * ADMIN SECONDARY TABS
				 */
				EPKB_HTML_Admin::admin_secondary_tabs( $admin_page_views );

				/**
				 * LIST OF SETTINGS IN TABS
				 */
				EPKB_HTML_Admin::admin_primary_tabs_content( $admin_page_views );				?>

			</div>

		</div>      <?php
	}

	/**
	 * Display shortcode generator above secondary tabs
	 */
	private static function display_shortcode_generator() {	?>

		<div class="epkb-faq-shortcode-preview-wrap" style="display:none;">
			<div class="epkb-faq-shortcode-preview">
				<div id="epkb-faq-shortcode-above-tabs" class="epkb-faq-shortcode-above-tabs-container">

					<!-- Preview Section - Matches width of sub-tabs -->
					<div id="epkb-faq-shortcode-preview-container">
						<div class="epkb-faq-shortcode-preview-head">
							<div class="epkb-faq-shortcode-preview-head__title"><?php esc_html_e( 'Live Preview', 'echo-knowledge-base' ); ?></div>
							<div class="epkb-faq-shortcode-preview-head__sub-title"><?php esc_html_e( 'This preview shows how your FAQs shortcode will appear.', 'echo-knowledge-base' ); ?></div>
						</div>
						<div class="epkb-faq-shortcode-preview-body">
							<div class="epkb-faq-preview-content">
							</div>
						</div>
						<div class="epkb-shortcode-actions">
							<button id="epkb-copy-shortcode" class="epkb-btn epkb-success-btn">
								<span class="epkbfa epkbfa-clipboard"></span>
								<span><?php esc_html_e( 'Copy generated FAQ shortcode', 'echo-knowledge-base' ); ?></span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>		<?php
	}

	/**
	 * Get configuration array for regular views
	 * @return array
	 */
	private static function get_regular_views_config() {

		$views_config = [];

		/**
		 * View: Overview
		 */
		$views_config[] = [

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_faqs_write'] ),
			'list_key' => 'faqs-overview',

			// Top Panel Item
			'label_text' => esc_html__( 'Overview', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-home',

			// Boxes List
			'boxes_list' => array(
				array(
					'html' => self::overview_tab(),
				)
			),
		];

		/**
		 * View: Questions
		 */
		$views_config[] = [

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_faqs_write'] ),
			'list_key' => 'faqs-questions',

			// Top Panel Item
			'label_text' => esc_html__( 'FAQs', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-question-circle-o',

			// Boxes List
			'boxes_list' => array(
				array(
					'html' => self::faqs_questions_tab(),
				)
			),
		];

		/**
		 * View: FAQ Groups
		 */
		$views_config[] = [

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_faqs_write'] ),
			'list_key' => 'faqs-groups',

			// Top Panel Item
			'label_text' => esc_html__( 'FAQ Groups', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-comments',

			// Boxes List
			'boxes_list' => array(
				array(
					'html' => self::faqs_groups_tab(),
				)
			),
		];

		/**
		 * View: FAQ Shortcodes
		 */
		$views_config[] = [

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_faqs_write'] ),
			'list_key'   => 'faq-shortcodes',

			// Top Panel Item
			'label_text' => esc_html__( 'FAQ Shortcodes', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-list-alt',

			// Secondary Panel Items
			'secondary_tabs' => array(
				array(
					'list_key'   => 'faq_groups',
					'label_text' => esc_html__('STEP 1: Choose FAQ Group', 'echo-knowledge-base'),
					'active'     => true,
					'boxes_list' => array(
						array(
							'html' => self::show_faq_shortcode_content('faq_groups'),
						)
					),
				),
				array(
					'list_key'   => 'design',
					'label_text' => esc_html__('STEP 2: Apply Design', 'echo-knowledge-base'),
					'boxes_list' => array(
						array(
							'html' => self::show_faq_shortcode_content('design'),
						)
					),
				),
				array(
					'list_key'   => 'settings',
					'label_text' => esc_html__('STEP 3: Adjust Settings', 'echo-knowledge-base'),
					'boxes_list' => array(
						array(
							'html' => self::show_faq_shortcode_content('settings'),
						)
					),
				),
			),
		];

		/**
		 * View: Settings
		 */
		/*$views_config[] = [

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_faqs_write'] ),
			'list_key'   => 'faq-setting',

			// Top Panel Item
			'label_text' => esc_html__( 'Settings', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-gears',

			// Boxes List
			'boxes_list' => array(),
		];*/

		return $views_config;
	}

	/**
	 * Show HTML content for Questions tab
	 * @return false|string
	 */
	private static function faqs_questions_tab() {

		$faqs_list = get_posts( [
			'post_type'         => EPKB_FAQs_CPT_Setup::FAQS_POST_TYPE,
			'posts_per_page'    => -1,
			'orderby'           => 'post_title',
			'order'             => 'ASC',
			'fields'            => 'id=>name',
		] );

		$col_length = ceil( count( $faqs_list ) / 2 );

		ob_start(); ?>

		<!-- Questions Info Box -->
		<div class="epkb-admin-info-box">
			<div class="epkb-admin-info-box__header">
				<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-info-circle"></div>
				<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'Manage Your Questions', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="epkb-admin-info-box__body">
				<p><?php esc_html_e( 'Create your own questions and answers, and organize them into one or more FAQ groups. ' .
						'Each FAQ group will have its own heading if multiple groups exist. Deleting a question will remove it from all groups.', 'echo-knowledge-base' ); ?></p>
			</div>
		</div>

		<!-- Buttons -->
		<div id='epkb-faq-top-buttons-container'>
			<button id="epkb-faq-create-question" class="epkb-btn epkb-success-btn">
				<span class="epkb-btn-icon epkbfa epkbfa-question-circle-o"></span>
				<span class="epkb-btn-text"><?php esc_html_e( 'Create Question', 'echo-knowledge-base' ); ?></span>
			</button>
		</div>

		<!-- All Questions -->
		<div id="epkb-all-faqs-container" class="epkb-faqs-modern-container">
			<div class="epkb-all-questions-body">

				<!-- Left Col -->
				<div class="epkb-body-col epkb-body-col--left">
					<div class="epkb-faq-questions-list-empty<?php echo empty( $faqs_list ) ? ' ' . 'epkb-faq-questions-list-empty--active' : ''; ?>"><?php esc_html_e( 'No available Questions.', 'echo-knowledge-base' ); ?></div>   <?php
					for ( $i = 0; $i < $col_length; $i++ ) {
						if ( isset( $faqs_list[$i] ) ) {
							self::display_question( array(
								'faq_id'        => $faqs_list[$i]->ID,
								'title'         => $faqs_list[$i]->post_title,
								'edit_icon'     => true,
							) );
						}
					}   ?>
				</div>

				<!-- Right Col -->
				<div class="epkb-body-col epkb-body-col--right">						<?php
					for ( $i = $col_length; $i < count( $faqs_list ); $i++ ) {
						if ( isset( $faqs_list[$i] ) ) {
							self::display_question( array(
								'faq_id'        => $faqs_list[$i]->ID,
								'title'         => $faqs_list[$i]->post_title,
								'edit_icon'     => true,
							) );
						}
					}   ?>
				</div>
			</div>
		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Show HTML content for Overview tab
	 * @return false|string
	 */
	private static function overview_tab() {
		ob_start();

		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );		?>

		<!-- Overview Info Box -->
		<div class="epkb-admin-info-box">
			<div class="epkb-admin-info-box__header">
				<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-info-circle"></div>
				<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'FAQs Overview', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="epkb-admin-info-box__body">
				<p><?php esc_html_e( 'Select an option below to add FAQs to a page. Then, create individual FAQs and assign them to one or more FAQ groups.', 'echo-knowledge-base' ); ?></p>
			</div>
		</div>

		<!-- Options Container -->
		<div class="epkb-overview-options-container">

			<!-- Option 1: FAQs Shortcode -->
			<div class="epkb-overview-option">
				<div class="epkb-overview-option__header">
					<div class="epkb-overview-option__icon epkbfa epkbfa-code"></div>
					<div class="epkb-overview-option__title"><?php esc_html_e( 'Option 1: Use FAQs Shortcode', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-overview-option__body">
					<p><?php esc_html_e( 'Use a shortcode to display FAQs anywhere on your site, including pages, posts, or widgets.', 'echo-knowledge-base' ); ?></p>
					<div class="epkb-overview-option__actions">
						<a href="https://www.echoknowledgebase.com/documentation/faqs-shortcode/" target="_blank" class="epkb-overview-option__link">
							<span class="epkbfa epkbfa-book"></span>
							<span><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?></span>
						</a>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . EPKB_KB_Handler::get_current_kb_id() . '&page=epkb-faqs#faq-shortcodes' ) ); ?>"
							id="epkb-faqs-shortcode-link" class="epkb-overview-option__button epkb-primary-btn">
							<span class="epkbfa epkbfa-arrow-circle-right"></span>
							<span><?php esc_html_e( 'Go to FAQ Shortcodes', 'echo-knowledge-base' ); ?></span>
						</a>
					</div>
				</div>
			</div>

			<!-- Option 2: FAQs Block -->
			<div class="epkb-overview-option">
				<div class="epkb-overview-option__header">
					<div class="epkb-overview-option__icon epkbfa epkbfa-th-large"></div>
					<div class="epkb-overview-option__title"><?php esc_html_e( 'Option 2: Use FAQs Block in Gutenberg Editor', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-overview-option__body">
					<p><?php esc_html_e( 'Use the Gutenberg block editor to insert FAQs into your content. Each FAQ block allows you to select one or more FAQ groups.', 'echo-knowledge-base' ); ?></p>
					<div class="epkb-overview-option__actions">
						<a href="https://www.echoknowledgebase.com/documentation/faqs/#How-to-use-the-shortcode/" target="_blank" class="epkb-overview-option__link">
							<span class="epkbfa epkbfa-book"></span>
							<span><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?></span>
						</a>						<?php

						$main_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $kb_config );
						$edit_link = empty( $main_page_id ) ? '' : get_edit_post_link( $main_page_id );

						if ( ! empty( $edit_link ) ) { ?>
							<div class="epkb-overview-option__buttons-container">
							<a href="<?php echo esc_url( $edit_link ); ?>" target="_blank" class="epkb-overview-option__button epkb-primary-btn">
								<span class="epkbfa epkbfa-edit"></span>
								<span><?php esc_html_e( 'Edit KB Main Page', 'echo-knowledge-base' ); ?></span>
							</a>
							<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>" target='_blank' class="epkb-overview-option__button epkb-primary-btn">
								<span class="epkbfa epkbfa-plus-circle"></span>
								<span><?php esc_html_e( 'Create New Page with Block', 'echo-knowledge-base' ); ?></span>
							</a>
							</div><?php } else { ?>
							<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>" target='_blank' class="epkb-overview-option__button epkb-primary-btn">
								<span class="epkbfa epkbfa-plus-circle"></span>
								<span><?php esc_html_e( 'Create New Page with Block', 'echo-knowledge-base' ); ?></span>
							</a>						<?php
						} ?>
					</div>
				</div>
			</div>

			<!-- Option 3: KB Main Page with FAQs Module -->			<?php

			// Check if using block main page
			$is_block_main_page = EPKB_Block_Utilities::kb_main_page_has_kb_blocks( $kb_config );

			if ( ! $is_block_main_page ) { ?>
				<div class="epkb-overview-option">
					<div class="epkb-overview-option__header">
						<div class="epkb-overview-option__icon epkbfa epkbfa-puzzle-piece"></div>
						<div class="epkb-overview-option__title"><?php esc_html_e( 'Option 3: Use KB Main Page with FAQs Module', 'echo-knowledge-base' ); ?></div>
					</div>
					<div class="epkb-overview-option__body">
						<p><?php esc_html_e( 'Add a FAQs module to your Knowledge Base Main Page.', 'echo-knowledge-base' ); ?></p>
						<div class="epkb-overview-option__actions">
							<a href="https://www.echoknowledgebase.com/documentation/faqs/#How-to-use-the-shortcode/" target="_blank" class="epkb-overview-option__link">
								<span class="epkbfa epkbfa-book"></span>
								<span><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?></span>
							</a>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=epkb_post_type_' . EPKB_KB_Handler::get_current_kb_id() . '&page=epkb-kb-configuration#settings__main-page__module--faqs' ) ); ?>" class="epkb-overview-option__button epkb-primary-btn">
								<span class="epkbfa epkbfa-cog"></span>
								<span><?php esc_html_e( 'Go to Main Page Settings', 'echo-knowledge-base' ); ?></span>
							</a>
						</div>
					</div>
				</div>			<?php
			} ?>
		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Show FAQs box
	 * @return false|string
	 */
	private static function faqs_groups_tab() {

		$faq_groups = EPKB_FAQs_Utilities::get_faq_groups();
		if ( is_wp_error( $faq_groups ) ) {
			EPKB_Logging::add_log( 'Error on retrieving FAQ Groups (750)', $faq_groups );
			return EPKB_HTML_Forms::notification_box_middle( array( 'type' => 'error-no-icon', 'desc' => EPKB_Utilities::report_generic_error( 750 ) ), true );
		}

		// append 'Draft' to FAQ groups name if they are not published
		/* foreach ( $faq_groups as $group_id => $group_name ) {
			$faq_groups[$group_id] = get_term_meta( $group_id, 'faq_group_status', true ) == 'publish' ? $group_name : $group_name . ' [Draft]';
		} */

		$group_ids = array_keys( $faq_groups );
		$col_length = ceil( count( $group_ids ) / 2 );

		$all_faqs_list = get_posts( [
			'post_type'         => EPKB_FAQs_CPT_Setup::FAQS_POST_TYPE,
			'posts_per_page'    => 1000,
			'orderby'           => 'post_title',
			'order'             => 'ASC',
		] );

		ob_start();     ?>

		<!-- FAQ Groups Info Box -->
		<div class="epkb-admin-info-box">
			<div class="epkb-admin-info-box__header">
				<div class="epkb-admin-info-box__header__icon epkbfa epkbfa-info-circle"></div>
				<div class="epkb-admin-info-box__header__title"><?php esc_html_e( 'FAQ Groups', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="epkb-admin-info-box__body">
				<p><?php esc_html_e( 'You can create FAQ groups and add individual FAQs to each group. ' .
						'These groups can be displayed on different pages or within the same FAQ shortcode or block.', 'echo-knowledge-base' ); ?></p>
			</div>
		</div>

		<!-- Buttons -->
		<div id="epkb-faq-top-buttons-container">
			<button id="epkb-faq-create-group" class="epkb-btn epkb-success-btn">
				<span class="epkb-btn-icon epkbfa epkbfa-comments"></span>
				<span class="epkb-btn-text"><?php esc_html_e( 'Create FAQs Group', 'echo-knowledge-base' ); ?></span>
			</button>
		</div>

		<!-- FAQ Groups -->
		<div id="epkb-faq-content-container">

			<!-- FAQ Group Form -->
			<div id="epkb-faq-group-form" data-faq-group-id="0" data-default-faq-group-id="0" data-default-faq-group-name="">
				<div class="epkb-faq-group-form-head">
					<div class="epkb-faq-group-form-head-left-col">
						<div class="epkb-faq-group-form-head__title"><?php esc_html_e( 'Group Name', 'echo-knowledge-base' ); ?></div>
					</div>
					<div class="epkb-faq-group-form-head-right-col">
						<div class="epkb-faq-group-form-head__save epkb-success-btn"><?php esc_html_e( 'Save', 'echo-knowledge-base' ); ?></div>
						<div class="epkb-faq-group-form-head__close epkb-primary-btn"><?php esc_html_e( 'Close', 'echo-knowledge-base' ); ?></div>
					</div>
				</div>
				<div class="epkb-faq-settings">					<?php
					EPKB_HTML_Elements::text( array(
						'name'          => 'faq-group-name',
						'label'         => 'Group Name',
						'placeholder'   => ''
					) );
					/* EPKB_HTML_Elements::checkbox_toggle( array(
						'name'          => 'faq-group-status',
						'toggleOnText'  => 'Publish',
						'toggleOffText' =>'Draft'
					) ); */ ?>
					<div class="epkb-delete-icon epkbfa epkbfa-trash"></div>
				</div>
				<div class="epkb-faq-group-form-body">
					<div class="epkb-faq-questions-list"></div>
					<div class="epkb-faq-questions-list-empty"><?php esc_html_e( 'No Questions assigned.', 'echo-knowledge-base' ); ?></div>
				</div>
			</div>

			<!-- Available Questions List -->
			<div id="epkb-available-questions-container">
				<div class="epkb-available-questions-head">
					<div class="epkb-available-questions-head__title"><?php esc_html_e( 'Questions not in your current group', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-available-questions-body">
					<div class="epkb-faq-questions-list-empty"><?php esc_html_e( 'No available Questions.', 'echo-knowledge-base' ); ?></div> <?php
					foreach ( $all_faqs_list as $faq ) {
						self::display_question( array(
							'faq_id'        => $faq->ID,
							'title'         => $faq->post_title,
							'add_icon'      => true,
							'order_icon'    => true,
							'include_icon'  => true,
							'edit_icon'     => true,
						) );
					}   ?>
				</div>
			</div>

			<!-- FAQ Groups List -->
			<div id="epkb-faq-groups-list">
				<div class="epkb-body-col epkb-body-col--left">					    <?php
					for ( $i = 0; $i < $col_length; $i++ ) {
						self::display_group_container( $group_ids[$i], $faq_groups[$group_ids[$i]] );
					}   ?>
				</div>
				<div class="epkb-body-col epkb-body-col--right">				    <?php
					for ( $i = $col_length; $i < count( $group_ids ); $i++ ) {
						self::display_group_container( $group_ids[$i], $faq_groups[$group_ids[$i]] );
					}   ?>
				</div>
			</div>

		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Question
	 * @param $args
	 * @return false|string|void
	 */
	public static function display_question( $args ) {

		if ( ! empty( $args['return_html'] ) ) {
			ob_start();
		}   ?>

		<div class="epkb-faq-question epkb-faq-question--<?php echo esc_attr( $args['faq_id'] ); ?> epkb-faq-question--modern" data-faq-id="<?php echo esc_attr( $args['faq_id'] ); ?>">			<?php
			if ( isset( $args['order_icon'] ) ) {  ?>
				<div class="epkb-faq-question__action-order">
					<div class="epkb-faq-question__order-icon epkbfa epkbfa-bars"></div>
				</div>			<?php
			}
			if ( isset( $args['include_icon'] ) ) {   ?>
				<div class="epkb-faq-question__action-include">
					<div class="epkb-faq-question__include-icon epkbfa epkbfa-plus-circle"></div>
				</div>  <?php
			}   ?>
			<div class="epkb-faq-question__action-edit">
				<div class="epkb-faq-question__title"><?php echo esc_html( $args['title'] ); ?></div>   <?php
				if ( isset( $args['edit_icon'] ) ) {   ?>
					<div class="epkb-faq-question__edit-icon epkbfa epkbfa-edit"></div> <?php
				}   ?>
			</div>
		</div>	<?php

		if ( ! empty( $args['return_html'] ) ) {
			return ob_get_clean();
		}
	}

	/**
	 * Group Container
	 * @param $faq_group_id
	 * @param $faq_group_name
	 * @param bool $return_html
	 * @return false|string|void
	 */
	public static function display_group_container( $faq_group_id, $faq_group_name, $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}   ?>

		<div class="epkb-faq-group-container epkb-faq-group-container--<?php echo esc_attr( $faq_group_id ); ?>" data-faq-group-id="<?php echo esc_attr( $faq_group_id ); ?>">
			<div class="epkb-faq-group-head">
				<div class="epkb-faq-group-head-left-col">
					<div class="epkb-faq-group-head__title"><?php echo esc_html( $faq_group_name ); ?></div>
				</div>
				<div class="epkb-faq-group-head-right-col">
					<div class="epkb-faq-group-head__edit epkb-primary-btn"><?php esc_html_e( 'Edit Group', 'echo-knowledge-base' ); ?></div>
				</div>
			</div>
			<div class="epkb-faq-questions-list">					<?php

				$faqs = EPKB_FAQs_Utilities::get_sorted_group_faqs( $faq_group_id );
				foreach ( $faqs as $one_faq ) {
					self::display_question( array(
						'faq_id'        => $one_faq->ID,
						'title'         => $one_faq->post_title,
					) );
				}

				if ( empty( $faqs ) ) { ?>
					<div class="epkb-faq-questions-list-empty"><?php esc_html_e( 'No Questions assigned.', 'echo-knowledge-base' ); ?></div>  <?php
				}   ?>

			</div>
		</div>  <?php

		if ( $return_html ) {
			return ob_get_clean();
		}
	}

	/**
	 * Single FAQ WP Editor
	 */
	private static function display_single_faq_editor() {    ?>

		<div id="epkb-faq-question-wp-editor-popup">

			<div class="epkb-faq-question-wp-editor__overlay"></div>

			<!-- WP Editor Form -->
			<form id="epkb-faq-question-wp--form">
				<input type="hidden" id="epkb-faq-editor-id" name="faq-id" value="">   <?php
				//EPKB_HTML_Elements::submit_button_v2( esc_html__( 'AI Help', 'echo-knowledge-base' ), '', 'epkb__wp_editor__open-ai-help-sidebar', '', '', '', 'epkb__wp_editor__ai-help-sidebar-btn-open' );   ?>
				<div class="epkb-faq-question-wp-editor__question">
					<h4><?php esc_html_e( 'Question', 'echo-knowledge-base' ); ?></h4>
					<div class="epkb-faq-question-wp-editor__question__input-container">
						<input type="text" id="epkb-faq-wp-editor__faq-title" name="faq-title" required maxlength="200">
						<div class="epkb-characters_left"><span class="epkb-characters_left-title"><?php esc_html_e( 'Character Limit', 'echo-knowledge-base' ); ?></span><span class="epkb-characters_left-counter">200</span>/<span>200</span></div>
					</div>
				</div>
				<div class="epkb-faq-question-wp-editor__answer">
					<h4><?php esc_html_e( 'Answer', 'echo-knowledge-base' ); ?></h4><?php
					wp_editor( '', 'epkb-faq-question-wp-editor', array( 'media_buttons' => false ) ); ?>
				</div>
				<div class="epkb-faq-question-wp-editor__buttons">				<?php
					EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Save', 'echo-knowledge-base' ), 'epkb_save_faq', 'epkb-faq-question-wp-editor__action__save', '', true, '', 'epkb-success-btn' );
					EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Cancel', 'echo-knowledge-base' ), '', 'epkb__help_editor__action__cancel', '', '', '', 'epkb-primary-btn' );
					EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Remove from Group', 'echo-knowledge-base' ), '', 'epkb__help_editor__action__remove-from-group', '', '', '', 'epkb-primary-btn' );
					EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Delete Permanently', 'echo-knowledge-base' ), 'epkb_delete_faq', 'epkb-faq-question-wp-editor__action__delete', '', true, '', 'epkb-error-btn' );    ?>
				</div>
			</form>

		</div><?php
	}

	/**
	 * Show HTML content for FAQ Shortcode tab
	 * @param string $type Tab type: 'faq_groups', 'design', or 'settings'
	 * @return string HTML content
	 */
	private static function show_faq_shortcode_content( $type = "faq_groups" ) {
		ob_start(); 
		
		// Displaying the general container ?>
		<div id="epkb-faq-shortcode-container">
			<div class="epkb-faq-shortcode-container-flex">
				<?php 
				// Get content depending on the type
				$content = '';
				switch ( $type ) {
					case 'faq_groups':
						$content = self::show_faq_groups_content();
						break;
					case 'design':
						$content = self::show_faq_design_content();
						break;
					case 'settings':
						$content = self::show_faq_settings_content();
						break;
					default:
						$content = self::show_faq_groups_content();
				}
				
				// Display the tab-specific content in left column
				echo '<div class="epkb-faq-shortcode-content-column">' . $content . '</div>';
				
				// Get and store FAQ groups for the shortcode generator
				$faq_groups = EPKB_FAQs_Utilities::get_faq_groups();
				$group_ids = is_wp_error($faq_groups) ? [] : array_keys($faq_groups);
				$GLOBALS['epkb_active_group_ids'] = $group_ids; // Store for shortcode generation				?>

				<div class="epkb-faq-shortcode-preview-column" style="display: none !important;">
					<!-- Generate Your FAQ Shortcode Section -->
					<div id="epkb-shortcode-generator-container">
						<div class="epkb-shortcode-example-head">
							<div class="epkb-shortcode-example-head__title"><?php esc_html_e( 'Generated FAQ Shortcode', 'echo-knowledge-base' ); ?></div>
						</div>
						<div class="epkb-shortcode-example-body">
							<div class="epkb-shortcode-generator-form">
								<div class="epkb-shortcode-display">		<?php
									EPKB_HTML_Elements::get_copy_to_clipboard_box(
										'[epkb-faqs group_ids="' . implode( ",", $group_ids ) . '"]',
										'Shortcode Preview',
										false
									); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>		<?php
		
		return ob_get_clean();
	}

	private static function show_faq_design_content() {

		$default_design = '1';

		ob_start(); ?>

		<!-- Choose a Design -->
		<div id="epkb-faq-shortcode-preset-container">
			<div class="epkb-faq-shortcode-preset-head">
				<div class="epkb-faq-shortcode-preset-head__title"><?php esc_html_e( 'Choose a Design', 'echo-knowledge-base' ); ?></div>
				<div class="epkb-faq-shortcode-preset-head__sub-title"><?php esc_html_e( 'Select a pre-made design for your FAQ display.', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="epkb-faq-shortcode-preset-body">
				<div class="epkb-radio-buttons-container">					<?php

					// Get all design options
					$design_names = EPKB_FAQs_Utilities::get_design_names();

					// Create a container for design settings JSON data
					echo '<div id="epkb-faq-design-settings-data" style="display:none;">';
					foreach ( $design_names as $design_id => $design_name ) {
						$design_settings = EPKB_FAQs_Utilities::get_design_settings( $design_id );
						echo '<div id="epkb-design-settings-' . esc_attr( $design_id ) . '" 
							data-design-id="' . esc_attr( $design_id ) . '" 
							data-design-settings=\'' . wp_json_encode( $design_settings ) . '\'></div>';
					}
					echo '</div>';

					EPKB_HTML_Elements::radio_buttons_horizontal( array(
						'input_group_class' => 'epkb-admin__input-field epkb-radio-buttons-horizontal',
						'label_class'       => 'epkb-main_label',
						'name'              => 'faq_shortcode_preset',
						'value'             => $default_design,
						'options'           => $design_names,
						'group_data'        => [ 
							'default-value' => $default_design
						],
					) );					?>
				</div>
			</div>
		</div>		<?php

		return ob_get_clean();
	}

	private static function show_faq_settings_content() {
		
		$faq_groups = EPKB_FAQs_Utilities::get_faq_groups();
		if ( is_wp_error( $faq_groups ) ) {
			EPKB_Logging::add_log( 'Error on retrieving FAQ Groups (751)', $faq_groups );
			return EPKB_HTML_Forms::notification_box_middle( array( 'type' => 'error-no-icon', 'desc' => EPKB_Utilities::report_generic_error( 751 ) ), true );
		}

		ob_start(); 		?>

		<!-- Settings -->
		<div id="ekb-admin-page-wrap" class="epkb-faq-settings-container">
			<div class="epkb-admin__form">
				<div class="epkb-admin__form__body">
					<div class="epkb-admin__form-tab-contents">
						<div id="epkb-faq-shortcode-settings-container">
							<div class="epkb-faq-shortcode-settings-head">
								<div class="epkb-faq-shortcode-settings-head__title"><?php esc_html_e( 'Settings', 'echo-knowledge-base' ); ?></div>
								<div class="epkb-faq-shortcode-settings-head__sub-title"><?php esc_html_e( 'Customize the appearance and behavior of your FAQs.', 'echo-knowledge-base' ); ?></div>
							</div>
							<div class="epkb-faq-shortcode-settings-body"> <?php
								// Add the wrappers seen in the sample HTML
								echo '<div class="epkb-admin__kb__form">';

								// Get default KB configuration specs using the static method
								// Use DEFAULT_KB_ID as we don't have a specific context here
								$kb_config_specs = EPKB_KB_Config_Specs::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID );

								// We don't have a specific KB config here, so we'll use defaults for now.
								// In a real scenario, these values should be fetched based on the context (e.g., shortcode attributes)
								$current_config_values = EPKB_KB_Config_Specs::get_default_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );

								// Settings list in the order matching the screenshot
								$settings_to_render = [
									'ml_faqs_title_text',
									'ml_faqs_title_location',
									'faq_nof_columns',
									'faq_icon_type',
									'faq_icon_location',
									'faq_border_mode',
									'faq_compact_mode',
									'faq_open_mode',
									'faq_question_background_color',
									'faq_answer_background_color',
									'faq_question_text_color',
									'faq_answer_text_color',
									'faq_icon_color',
									'faq_border_color'
								];

								// --- Loop through settings and render ---
								foreach ( $settings_to_render as $setting_name ) {
									// --- Prepare arguments for standard rendering ---
									if ( ! isset( $kb_config_specs[$setting_name] ) ) {
										echo "<p><i>Setting spec missing for: " . esc_html( $setting_name ) . "</i></p>";
										continue;
									}

									$field_spec = $kb_config_specs[$setting_name];
									$current_value = $current_config_values[$setting_name] ?? $field_spec['default'] ?? '';

									// Base arguments for EPKB_HTML_Elements functions
									$input_args = [
										'specs'             => $setting_name,
										'name'              => $setting_name,
										'id'                => $setting_name,
										'label'             => $field_spec['label'] ?? '',
										'value'             => $current_value,
										'options'           => $field_spec['options'] ?? [],
										'default'           => $field_spec['default'] ?? '',
										'desc'              => $field_spec['desc'] ?? '',
										'input_group_class' => 'epkb-shortcode-setting epkb-shortcode-setting--' . esc_attr( $setting_name ),
										// Add tooltip data if needed
									];

									// Add tooltip for Title Location
									if ( $setting_name === 'ml_faqs_title_location' ) {
										// Replicating tooltip structure - requires JS for toggle
										$input_args['label_suffix_html'] = '
											<div class="epkb__option-tooltip ">
												<span class="epkb__option-tooltip__button epkbfa epkbfa-info-circle"></span>
												<div class="epkb__option-tooltip__contents" style="display: none;">
													<div class="epkb__option-tooltip__body">'.
													// Need a way to get the description/link dynamically if possible
													sprintf( esc_html__( 'To change FAQ Title %sclick here%s', 'echo-knowledge-base' ), '<a href="#" class="epkb-admin__form-tab-content-desc__link">', '</a>' ).
													'</div>
												</div>
											</div>';
									}

									// --- Determine Render Function and Adjust Args ---
									$render_function = null;

									switch ( $field_spec['type'] ) {
										case EPKB_Input_Filter::COLOR_HEX:
											$render_function = 'EPKB_HTML_Elements::color';
											break;

										case EPKB_Input_Filter::SELECTION:
											if ( $setting_name === 'faq_open_mode' ) {
												$render_function = 'EPKB_HTML_Elements::radio_buttons_horizontal';
												// Add description text below dropdown
												$input_args['input_desc'] = esc_html__( 'Toggle - Show one Article at a time', 'echo-knowledge-base' );
											} elseif ( $setting_name === 'faq_icon_type' ) {
												$render_function = 'EPKB_HTML_Elements::radio_buttons_horizontal';
												$input_args = array(
													'label'       => esc_html__( 'Icon to Expand/Collapse FAQs', 'echo-knowledge-base' ),
													'name'        => 'faq_icon_type',
													'type'        => EPKB_Input_Filter::SELECTION,
													'options'     => array(
														'icon_plus_box'                             => _x( 'Plus Box', 'icon type', 'echo-knowledge-base' ),
														'icon_plus_circle'                          => _x( 'Plus circle', 'icon type', 'echo-knowledge-base' ),
														'icon_plus'                                 => _x( 'Plus Sign', 'icon type', 'echo-knowledge-base' ),
														'icon_arrow_caret'                          => _x( 'Arrow Down Caret', 'icon type', 'echo-knowledge-base' ),
														'icon_arrow_angle'                          => _x( 'Arrow Right Angle', 'icon type', 'echo-knowledge-base' ),
													),
													'default'     => 'icon_arrow_caret'
												);
											} else {
												$render_function = 'EPKB_HTML_Elements::radio_buttons_horizontal';
											}
											break;

										case EPKB_Input_Filter::CHECKBOX:
											$render_function = 'EPKB_HTML_Elements::checkbox_toggle';
											$input_args['checked'] = ( $current_value == 'on' );
											break;

										case EPKB_Input_Filter::NUMBER:
											$render_function = 'EPKB_HTML_Elements::text';
											$input_args['type'] = 'number';
											if ( isset( $field_spec['min'] ) ) { $input_args['min'] = $field_spec['min']; }
											if ( isset( $field_spec['max'] ) ) { $input_args['max'] = $field_spec['max']; }
											break;

										case EPKB_Input_Filter::TEXT:
										default:
											$render_function = 'EPKB_HTML_Elements::text';
											if ( isset( $field_spec['max'] ) ) { $input_args['maxlength'] = $field_spec['max']; }
											break;
									}

									// --- Call Render Function ---
									if ( $render_function && is_callable( $render_function ) ) {
										call_user_func( $render_function, $input_args );
									} else {
										echo "<p><i>Cannot render setting: " . esc_html( $setting_name ) . "</i></p>";
									}

								} // End foreach loop

								echo '</div>'; // Close epkb-admin__kb__form
							?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>		<?php

		return ob_get_clean();
	}

	private static function show_faq_groups_content() {
		
		$faq_groups = EPKB_FAQs_Utilities::get_faq_groups();
		if ( is_wp_error( $faq_groups ) ) {
			EPKB_Logging::add_log( 'Error on retrieving FAQ Groups (751)', $faq_groups );
			return EPKB_HTML_Forms::notification_box_middle( array( 'type' => 'error-no-icon', 'desc' => EPKB_Utilities::report_generic_error( 751 ) ), true );
		}

		$group_ids = array_keys( $faq_groups );
		$GLOBALS['epkb_active_group_ids'] = $group_ids; // Store for shortcode generation
		$groups_count = count( $group_ids );
		
		ob_start(); ?>
		<!-- Your FAQ Groups -->
		<div id="epkb-all-groups-container">
			<div class="epkb-faq-shortcode-groups-head">
				<div class="epkb-faq-shortcode-groups-head__title"><?php esc_html_e( 'Choose FAQ Groups', 'echo-knowledge-base' ); ?></div>
				<div class="epkb-faq-shortcode-groups-head__sub-title"><?php esc_html_e( 'Select which FAQ groups to include in your shortcode.', 'echo-knowledge-base' ); ?></div>
			</div>
			
			<!-- Select All Action Bar -->
			<div class="epkb-all-groups-actions">
				<div class="epkb-all-groups-select-all">
					<label>
						<input type="checkbox" id="epkb-select-all-groups" checked>
						<?php esc_html_e( 'Select All Groups', 'echo-knowledge-base' ); ?>
					</label>
				</div>
				<div class="epkb-all-groups-count">
					<?php echo esc_html( sprintf( _n( '%d group selected', '%d groups selected', $groups_count, 'echo-knowledge-base' ), $groups_count ) ); ?>
				</div>
			</div>
			
			<!-- Table Header -->
			<div class="epkb-all-groups-table-header">
				<div class="epkb-groups-header-cell epkb-groups-header-select"></div>
				<div class="epkb-groups-header-cell epkb-groups-header-name"><?php esc_html_e( 'Group Name', 'echo-knowledge-base' ); ?></div>
				<div class="epkb-groups-header-cell epkb-groups-header-id"><?php esc_html_e( 'ID', 'echo-knowledge-base' ); ?></div>
			</div>
			
			<div class="epkb-all-groups-table">
				<?php foreach ( $faq_groups as $group_id => $group_name ) : ?>
					<div class="epkb-groups-table-row" data-group-id="<?php echo esc_attr( $group_id ); ?>">
						<div class="epkb-groups-table-cell epkb-groups-table-select">
							<input type="checkbox" class="epkb-group-select" value="<?php echo esc_attr( $group_id ); ?>" checked>
						</div>
						<div class="epkb-groups-table-cell epkb-groups-table-name"><?php echo esc_html( $group_name ); ?></div>
						<div class="epkb-groups-table-cell epkb-groups-table-id"><?php echo esc_html( $group_id ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
			
			<?php if ( empty( $faq_groups ) ) : ?>
			<div class="epkb-all-groups-empty epkb-all-groups-empty--active">
				<?php esc_html_e( 'No FAQ groups found. Please create some groups first.', 'echo-knowledge-base' ); ?>
			</div>
			<?php endif; ?>
		</div>		<?php

		return ob_get_clean();
	}
}


