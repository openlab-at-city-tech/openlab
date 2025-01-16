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
	 * Show FAQs box
	 * @return false|string
	 */
	private static function show_faqs_content() {

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
			'posts_per_page'    => 100,
			'orderby'           => 'post_title',
			'order'             => 'ASC',
		] );

		ob_start();     ?>

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
			<div id="epkb-faq-group-form" data-faq-group-id="0" data-default-faq-group-id="0" data-default-faq-group-name="<?php esc_attr_e( 'Group Name', 'echo-knowledge-base' ); ?>">
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
						'label'         => 'Name',
						'placeholder'   => esc_html__( 'Group Name', 'echo-knowledge-base' )
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
	 * Show HTML content for Questions tab
	 * @return false|string
	 */
	private static function show_questions_content() {

		$faqs_list = get_posts( [
			'post_type'         => EPKB_FAQs_CPT_Setup::FAQS_POST_TYPE,
			'posts_per_page'    => -1,
			'orderby'           => 'post_title',
			'order'             => 'ASC',
			'fields'            => 'id=>name',
		] );

		$col_length = ceil( count( $faqs_list ) / 2 );

		ob_start(); ?>

		<!-- Buttons -->
		<div id='epkb-faq-top-buttons-container'>
			<button id="epkb-faq-create-question" class="epkb-btn epkb-success-btn">
				<span class="epkb-btn-icon epkbfa epkbfa-question-circle-o"></span>
				<span class="epkb-btn-text"><?php esc_html_e( 'Create Question', 'echo-knowledge-base' ); ?></span>
			</button>
		</div>

		<!-- All Questions -->
		<div id="epkb-all-faqs-container">
			<div class="epkb-all-faqs-head">
				<div class="epkb-all-faqs-head__title"><?php esc_html_e( 'Manage Your Questions', 'echo-knowledge-base' ); ?></div>
				<div class="epkb-all-faqs-head__sub-title"><?php esc_html_e( 'Edit your questions. Deleting a question will remove it from all groups.', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="epkb-all-questions-body">

				<!-- Left Col -->
				<div class="epkb-body-col epkb-body-col--left">
					<div class="epkb-faq-questions-list-empty<?php echo empty( $faqs_list ) ? ' ' . 'epkb-faq-questions-list-empty--active' : ''; ?>"><?php esc_html_e( 'No available Questions.', 'echo-knowledge-base' ); ?></div>   <?php
					for ( $i = 0; $i < $col_length; $i++ ) {
						self::display_question( array(
							'faq_id'        => $faqs_list[$i]->ID,
							'title'         => $faqs_list[$i]->post_title,
							'edit_icon'     => true,
						) );
					}   ?>
				</div>

				<!-- Right Col -->
				<div class="epkb-body-col epkb-body-col--right">						<?php
					for ( $i = $col_length; $i < count( $faqs_list ); $i++ ) {
						self::display_question( array(
							'faq_id'        => $faqs_list[$i]->ID,
							'title'         => $faqs_list[$i]->post_title,
							'edit_icon'     => true,
						) );
					}   ?>
				</div>
			</div>

		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Show HTML content for FAQ Shortcode tab
	 * @return false|string
	 */
	private static function show_faq_shortcode_content() {

		$faq_groups = EPKB_FAQs_Utilities::get_faq_groups();
		if ( is_wp_error( $faq_groups ) ) {
			EPKB_Logging::add_log( 'Error on retrieving FAQ Groups (751)', $faq_groups );
			return EPKB_HTML_Forms::notification_box_middle( array( 'type' => 'error-no-icon', 'desc' => EPKB_Utilities::report_generic_error( 751 ) ), true );
		}

		$group_ids = array_keys( $faq_groups );
		$col_length = ceil( count( $group_ids ) / 2 );
		$default_design = '1';

		ob_start(); ?>

		<div id="epkb-faq-shortcode-container">

			<div id="epkb-shortcode-example-container">
				<div class="epkb-shortcode-example-head">
					<div class="epkb-shortcode-example-head__title"><?php esc_html_e( 'Shortcode Example', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-shortcode-example-head__sub-title"><?php esc_html_e( 'Copy the code below and update the group_ids parameter with the IDs of the groups you wish to use.' .
																						'Arrange them in the order you want them to be displayed.', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-shortcode-example-body">					<?php
					EPKB_HTML_Elements::get_copy_to_clipboard_box( '[epkb-faqs group_ids="' . implode( ",", $group_ids ) . '"]', '', false );					?>
				</div>
				<div class="epkb-shortcode-example-container__link">
					<a href="https://www.echoknowledgebase.com/documentation/faqs-shortcode/#How-to-use-the-shortcode" target="_blank" rel="nofollow"><?php esc_html_e( 'For additional FAQ shortcode parameters, click here.', 'echo-knowledge-base' ); ?></a><span class="epkbfa epkbfa-external-link"></span>
				</div>
			</div>

			<div id="epkb-faq-shortcode-preset-container">
				<div class="epkb-faq-shortcode-preset-head">
					<div class="epkb-faq-shortcode-preset-head__title"><?php esc_html_e( 'Design', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-faq-shortcode-preset-head__sub-title"><?php esc_html_e( 'Select pre-made Design.', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-faq-shortcode-preset-body">   <?php
					EPKB_HTML_Elements::radio_buttons_horizontal( array(
					'input_group_class' => 'epkb-admin__input-field',
					'label_class'       => 'epkb-main_label',
					'name'              => 'faq_shortcode_preset',
					'value'             => $default_design,
					'options'           => EPKB_FAQs_Utilities::get_design_names(),
					'group_data'        => [ 'default-value' => $default_design ],
					) );    ?>
				</div>
			</div>

			<div id="epkb-all-groups-container">
				<div class="epkb-all-groups-head">
					<div class="epkb-all-groups-head__title"><?php esc_html_e( 'List of Available Groups', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-all-groups-head__sub-title"><?php esc_html_e( 'Your FAQ Groups are listed here. Each group has a group ID that is used in the FAQ shortcode as shown in the example above.', 'echo-knowledge-base' ); ?></div>
				</div>
				<div class="epkb-all-groups-body">
					<div class="epkb-body-col epkb-body-col--left">						<?php
						for ( $i = 0; $i < $col_length; $i++ ) {
							self::display_shortcode_group( $group_ids[$i], $faq_groups[$group_ids[$i]] );
						}   ?>
					</div>
					<div class="epkb-body-col epkb-body-col--right">				    <?php
						for ( $i = $col_length; $i < count( $group_ids ); $i++ ) {
							self::display_shortcode_group( $group_ids[$i], $faq_groups[$group_ids[$i]] );
						}   ?>
					</div>
				</div>
			</div>

		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Get configuration array for regular views
	 * @return array
	 */
	private static function get_regular_views_config() {

		$views_config = [];

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
					'html' => self::show_faqs_content(),
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
			'label_text' => esc_html__( 'Questions', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-question-circle-o',

			// Boxes List
			'boxes_list' => array(
				array(
					'html' => self::show_questions_content(),
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

			// Boxes List
			'boxes_list' => array(
				array(
					'html' => self::show_faq_shortcode_content(),
				)
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
	 * Question
	 * @param $args
	 * @return false|string|void
	 */
	public static function display_question( $args ) {

		if ( ! empty( $args['return_html'] ) ) {
			ob_start();
		}   ?>

		<div class="epkb-faq-question epkb-faq-question--<?php echo esc_attr( $args['faq_id'] ); ?>" data-faq-id="<?php echo esc_attr( $args['faq_id'] ); ?>">			<?php
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
		     <!-- data-faq-group-status="<?php //echo get_term_meta( $faq_group_id, 'faq_group_status', true ) == 'publish' ? 'publish' : 'draft'; ?>"> -->
			<div class="epkb-faq-group-head">
				<div class="epkb-faq-group-head-left-col">
					<div class="epkb-faq-group-head__title"><?php echo esc_html( $faq_group_name ); ?></div>
				</div>
				<div class="epkb-faq-group-head-right-col">
					<div class="epkb-faq-group-head__edit epkb-primary-btn"><?php esc_html_e( 'Edit', 'echo-knowledge-base' ); ?></div>
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
			</form> <?php

			// AI Help Sidebar
			//	EPKB_AI_Help_Sidebar::display_ai_help_sidebar();    ?>

		</div><?php
	}

	/**
	 * HTML for FAQ Group in FAQ Shortcodes tab
	 * @param $group_id
	 * @param $group_name
	 * @param bool $return_html
	 * @return false|string|void
	 */
	public static function display_shortcode_group( $group_id, $group_name, $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}   ?>

		<div class="epkb-faq-group epkb-faq-group--<?php echo esc_attr( $group_id ); ?>">
			<div class="epkb-faq-group__title"><?php echo esc_html( $group_name ); ?></div>
			<div class="epkb-faq-group__id"><?php echo esc_html__( 'ID', 'echo-knowledge-base' ) . ': ' . esc_html( $group_id ); ?></div>
		</div>  <?php

		if ( $return_html ) {
			return ob_get_clean();
		}
	}
}
