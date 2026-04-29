<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Helper class for Display KB configuration menu and pages (Tools Tab)
 *
 * @copyright   Copyright (C) 2021, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Config_Tools_Page {

	/**
	 * Get Tools View Config
	 *
	 * @param $kb_config
	 * @return array
	 */
	public static function get_tools_view_config( $kb_config ) {

		$secondary_tabs = [];

		// SECONDARY VIEW: EXPORT
		$secondary_tabs[] = array(

			// Shared
			'list_key'   => 'export',
			'active'     => true,

			// Secondary Panel Item
			'label_text' => esc_html__( 'Export', 'echo-knowledge-base' ),

			// Secondary Boxes List
			'boxes_list' => self::get_export_boxes( $kb_config )
		);

		// SECONDARY VIEW: IMPORT
		$secondary_tabs[] = array(

			// Shared
			'list_key'   => 'import',

			// Secondary Panel Item
			'label_text' => esc_html__( 'Import', 'echo-knowledge-base' ),

			// Secondary Boxes List
			'boxes_list' => self::get_import_boxes( $kb_config )
		);

		// SECONDARY VIEW: CONVERT
		$secondary_tabs[] = array(

			// Shared
			'list_key'   => 'convert',

			// Secondary Panel Item
			'label_text' => esc_html__( 'Convert Articles', 'echo-knowledge-base' ),

			// Secondary Boxes List
			'boxes_list' => self::get_convert_boxes( $kb_config )
		);

		// SECONDARY VIEW: OTHER
		$secondary_tabs[] = array(

			// Shared
			'list_key'   => 'other',

			// Secondary Top Panel Item
			'label_text' => esc_html__( 'Other', 'echo-knowledge-base' ),

			// Secondary Boxes List
			'boxes_list' => self::get_other_boxes( $kb_config )
		);

		// SECONDARY VIEW: ADD-ONS (redirects to Add-ons page)
		$secondary_tabs[] = array(
			'list_key'   => 'add-ons',
			'label_text' => '<span style="color:#5cb85c;">' . esc_html__( 'Add-ons', 'echo-knowledge-base' ) . '</span>',
			'url'        => admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) . '&page=epkb-add-ons#add-ons' ),
		);

		// SECONDARY VIEW: MENU ACCESS CONTROL
		$secondary_tabs[] = array(

			// Shared
			'list_key'              => 'access-control',

			// Secondary Panel Item
			'label_text'            => esc_html__( 'Menu Access Control', 'echo-knowledge-base' ),

			// Secondary Boxes List
			'list_top_actions_html' => '<div class="epkb-admin__list-actions-row">' . EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Save Access Control Settings', 'echo-knowledge-base' ),
											 'epkb_save_access_control', 'epkb-admin__save-access-control-btn', '', true, true, 'epkb-success-btn' ) . '</div>',
			'boxes_list'            => EPKB_Admin_UI_Access::get_access_boxes( $kb_config ),
		);

		// SECONDARY VIEW: DEBUG
		$secondary_tabs[] = array(

			// Shared
			'list_key'   => 'debug',

			// Secondary Top Panel Item
			'label_text' => esc_html__( 'Debug', 'echo-knowledge-base' ),

			// Secondary Boxes List
			'boxes_list' => self::get_debug_boxes( $kb_config )
		);

		return array(

			// Shared
			'secondary_tab_access_override' => [
				'convert'   => EPKB_Admin_UI_Access::get_editor_capability(),
			],
			'list_key'   => 'tools',

			// Top Panel Item
			'label_text' => esc_html__( 'Tools', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-wrench',

			// Secondary Panel Items
			'secondary_tabs'  => $secondary_tabs,
		);
	}

	/**
	 * Get Import Box
	 *
	 * @param $kb_config
	 * @return false|string
	 */
	private static function get_import_box( $kb_config ) {

		// reset cache and get latest KB config
		epkb_get_instance()->kb_config_obj->reset_cache();

		ob_start(); ?>

		<!-- Import Config -->
		<div class="epkb-admin-info-box">
			<div class="epkb-admin-info-box__body">
				<p><?php esc_html_e( 'This import will overwrite the following KB settings:', 'echo-knowledge-base' ); ?></p>				<?php
				self::display_import_export_info(); ?>
				<form class="epkb-import-kbs"
					  action="<?php echo esc_url( add_query_arg( array( 'active_kb_tab' => $kb_config['id'], 'active_action_tab' => 'import' ) ) . '#tools__import' ); ?>"
					  method="post" enctype="multipart/form-data">
					<input type="hidden" name="_wpnonce_epkb_ajax_action"
						   value="<?php echo wp_create_nonce( "_wpnonce_epkb_ajax_action" ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>"/>
					<input type="hidden" name="action" value="epkb_import_knowledge_base"/>
					<input type="hidden" name="emkb_kb_id" value="<?php echo esc_attr( $kb_config['id'] ); ?>"/>
					<input class="epkb-form-label__input epkb-form-label__input--text" type="file" name="import_file"
						   required><br>
					<input type="button" class="epkb-kbnh-back-btn epkb-default-btn"
						   value="<?php echo esc_attr( '< ' . __( 'Back', 'echo-knowledge-base' ) ); ?>"/>
					<input type="submit" class="epkb-primary-btn"
						   value="<?php esc_attr_e( 'Import Configuration', 'echo-knowledge-base' ); ?>"/><br/>
				</form>
			</div>
		</div>  <?php

		return ob_get_clean();
	}

	private static function display_import_export_info() { ?>
		<ul>
			<li><?php esc_html_e( 'Configuration for all text, styles, features.', 'echo-knowledge-base' ); ?></li>
			<li><?php esc_html_e( 'Configuration for all add-ons.', 'echo-knowledge-base' ); ?></li>
		</ul>
		<p><?php esc_html_e( 'Instructions:', 'echo-knowledge-base' ); ?></p>
		<ul>
			<li><?php esc_html_e( 'Test import and export on your staging or test site before importing configuration in production.', 'echo-knowledge-base' ); ?></li>
			<li><?php esc_html_e( 'Always back up your database before starting the import.', 'echo-knowledge-base' ); ?></li>
			<li><?php esc_html_e( 'Preferably run import outside of business hours.', 'echo-knowledge-base' ); ?></li>
		</ul> <?php
	}

	/**
	 * Get boxes for Tools panel, export subpanel
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function get_export_boxes( $kb_config ) {
		$boxes = [];

		foreach ( self::get_export_boxes_config( $kb_config ) as $box ) {

			if ( $box['plugin'] == 'epie' ) {
				if ( EPKB_Utilities::is_export_import_enabled() ) {
					$box['active_status'] = true;
				} else {
					$box['upgrade_link'] = EPKB_Core_Utilities::get_plugin_sales_page( $box['plugin'] );
					$box['corner_label'] = esc_html__( '3 Sites License', 'echo-knowledge-base' );
				}
			} else {
				$box['active_status'] = true;
			}

			// add pro tag
			if ( empty( $box['active_status'] ) ) {
				$box['title_class'] .= '--pro';
			}

			// box with the button
			$boxes[] = [
				'class' => 'epkb-kbnh__feature-container',
				'html'  => EPKB_HTML_Forms::get_feature_box_html( $box )
			];
		}

		foreach ( self::get_export_boxes_config( $kb_config ) as $box ) {
			// panel that will be opened with the button
			$box_panel_class = 'epkb-kbnh__feature-panel-container ' . ( empty( $box['button_id'] ) ? '' : 'epkb-kbnh__feature-panel-container--' . $box['button_id'] );

			$boxes[] = [
				'title' => $box['title'],
				'class' => $box_panel_class,
				'html'  => apply_filters( 'epkb_config_page_export_import_panel_html', '', $kb_config, $box ),
			];
		}

		return $boxes;
	}

	/**
	 * Get boxes config for Tools panel, export subpanel
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function get_export_boxes_config( $kb_config ) {

		return [
			[
				'plugin'       => 'core',
				'icon'         => 'epkbfa epkbfa-upload',
				'title'        => esc_html__( 'Export KB Configuration', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Export core and add-ons configuration including colors, fonts, labels, and features settings.', 'echo-knowledge-base' ) . ' ' . esc_html__( 'Block settings will not be exported.', 'echo-knowledge-base' ),
				'custom_links' => self::get_export_button_html( $kb_config ),
				'button_id'    => 'epkb_core_export',
				'button_title' => esc_html__( 'Export Configuration', 'echo-knowledge-base' ),
			],
			[
				'plugin'       => 'epie',
				'icon'         => 'epkbfa epkbfa-upload',
				'title'        => esc_html__( 'Export Articles to CSV', 'echo-knowledge-base' ),
				'title_class'  => 'epkb-kbnh__feature-name',
				'desc'         => esc_html__( 'Export basic article information: title, content, categories, and tags.', 'echo-knowledge-base' ),
				'button_id'    => EPKB_Utilities::is_export_import_enabled() ? 'epie_export_data_csv' : '',
				'button_title' => esc_html__( 'Run Export', 'echo-knowledge-base' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/export-articles-as-csv/',
				'learn_more'   => EPKB_Utilities::is_export_import_enabled() ? '' : 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/'
			],
			[
				'plugin'       => 'epie',
				'icon'         => 'epkbfa epkbfa-upload',
				'title'        => esc_html__( 'Export Articles to XML', 'echo-knowledge-base' ),
				'title_class'  => 'epkb-kbnh__feature-name',
				'desc'         => esc_html__( 'Export articles, including content, comments, authors, categories, meta data, and references to attachments.', 'echo-knowledge-base' ),
				'button_id'    => EPKB_Utilities::is_export_import_enabled() ? 'epie_export_data_xml' : '',
				'button_title' => esc_html__( 'Run Export', 'echo-knowledge-base' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/export-articles-as-xml/',
				'learn_more'   => EPKB_Utilities::is_export_import_enabled() ? '' : 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/'
			],
		];
	}

	/**
	 * Get hidden block to make export working
	 *
	 * @param $kb_config
	 * @return string
	 */
	private static function get_export_button_html( $kb_config ) {

		ob_start(); ?>

		<form class="epkb-export-kbs" action="<?php echo esc_url( add_query_arg( array( 'active_kb_tab' => $kb_config['id'], 'active_action_tab' => 'export#tools__export' ) ) ); ?>" method="post">
			<input type="hidden" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_epkb_ajax_action" ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>"/>
			<input type="hidden" name="action" value="epkb_export_knowledge_base"/>
			<input type="hidden" name="emkb_kb_id" value="<?php echo esc_attr( $kb_config['id'] ); ?>"/>
			<input type="submit" class="epkb-primary-btn" value="<?php esc_attr_e( 'Export Configuration', 'echo-knowledge-base' ); ?>"/>
		</form><?php

		return ob_get_clean();
	}

	/**
	 * Get boxes for Tools panel, import subpanel
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function get_import_boxes( $kb_config ) {
		$boxes = [];

		foreach ( self::get_import_boxes_config() as $box ) {

			if ( $box['plugin'] == 'epie' ) {
				if ( EPKB_Utilities::is_export_import_enabled() ) {
					$box['active_status'] = true;
				} else {
					$box['upgrade_link'] = EPKB_Core_Utilities::get_plugin_sales_page( $box['plugin'] );
					$box['corner_label'] = esc_html__( '3 Sites License', 'echo-knowledge-base' );
				}
			} else {
				$box['active_status'] = true;
			}

			// add pro tag
			if ( empty( $box['active_status'] ) ) {
				$box['title_class'] .= '--pro';
			}

			$boxes[] = [
				'class' => 'epkb-kbnh__feature-container',
				'html'  => EPKB_HTML_Forms::get_feature_box_html( $box )
			];
		}

		foreach ( self::get_import_boxes_config() as $box ) {
			// panel that will be opened with the button
			$box_panel_class = 'epkb-kbnh__feature-panel-container ' . ( empty( $box['button_id'] ) ? '' : 'epkb-kbnh__feature-panel-container--' . $box['button_id'] );

			$panel_html = '';

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_core_import' ) {
				$panel_html = self::get_import_box( $kb_config );
			}

			$boxes[] = [
				'title' => $box['title'],
				'class' => $box_panel_class,
				'html'  => apply_filters( 'epkb_config_page_export_import_panel_html', $panel_html, $kb_config, $box ),
			];
		}

		return $boxes;
	}

	/**
	 * Get config for boxes for Tools panel, import subpanel
	 * @return array
	 */
	private static function get_import_boxes_config() {

		return [
			[
				'plugin'       => 'core',
				'icon'         => 'epkbfa epkbfa-download',
				'title'        => esc_html__( 'Import KB Configuration', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Import core and add-ons configuration including colors, fonts, labels, and features settings.', 'echo-knowledge-base' ) . ' ' . esc_html__( 'Block settings cannot be imported.', 'echo-knowledge-base' ),
				'button_id'    => 'epkb_core_import',
				'button_title' => esc_html__( 'Import Configuration', 'echo-knowledge-base' ),
			],
			[
				'plugin'       => 'epie',
				'icon'         => 'epkbfa epkbfa-download',
				'title'        => esc_html__( 'Import Articles from CSV', 'echo-knowledge-base' ),
				'title_class'  => 'epkb-kbnh__feature-name',
				'desc'         => esc_html__( 'Import basic article information: title, content, categories and tags.', 'echo-knowledge-base' ),
				'button_id'    => EPKB_Utilities::is_export_import_enabled() ? 'epie_import_data_csv' : '',
				'button_title' => esc_html__( 'Run Import', 'echo-knowledge-base' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/how-to-import-csv-file/',
				'learn_more'   => EPKB_Utilities::is_export_import_enabled() ? '' : 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/'
			],
			[
				'plugin'       => 'epie',
				'icon'         => 'epkbfa epkbfa-download',
				'title'        => esc_html__( 'Import Articles from XML', 'echo-knowledge-base' ),
				'title_class'  => 'epkb-kbnh__feature-name',
				'desc'         => esc_html__( 'Import articles including content, comments, authors, categories, meta data, attachments.', 'echo-knowledge-base' ),
				'button_id'    => EPKB_Utilities::is_export_import_enabled() ? 'epie_import_data_xml' : '',
				'button_title' => esc_html__( 'Run Import', 'echo-knowledge-base' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/how-to-import-xml-file/',
				'learn_more'   => EPKB_Utilities::is_export_import_enabled() ? '' : 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/'
			],
		];
	}

	/**
	 * Get boxes for Tools panel, convert subpanel
	 * @param $kb_config
	 * @return array
	 */
	private static function get_convert_boxes( $kb_config ) {
		$boxes = [];

		foreach ( self::get_convert_boxes_config() as $box ) {

			if ( $box['plugin'] == 'ai' . 'fp' ) {
				if ( EPKB_Utilities::is_ai_features_pro_enabled() ) {
					$box['active_status'] = true;
				} else {
					$box['upgrade_link'] = EPKB_Core_Utilities::get_plugin_sales_page( 'ai' . 'fp' );
					$box['corner_label'] = esc_html__( 'Add-on', 'echo-knowledge-base' );
				}
			}

			// add pro tag
			if ( ! empty( $box['title_class'] ) && empty( $box['active_status'] ) ) {
				$box['title_class'] .= '--pro';
			}

			$boxes[] = [
				'class' => 'epkb-kbnh__feature-container',
				'html'  => EPKB_HTML_Forms::get_feature_box_html( $box )
			];
		}

		foreach ( self::get_convert_boxes_config() as $box ) {
			// panel that will be opened with the button
			$box_panel_class = 'epkb-kbnh__feature-panel-container ' . ( empty( $box['button_id'] ) ? '' : 'epkb-kbnh__feature-panel-container--' . $box['button_id'] );

			$panel_html = '';

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_core_import' ) {
				$panel_html = self::get_import_box( $kb_config );
			}

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_convert_posts' ) {
				$panel_html = self::get_convert_posts_to_articles_box( $kb_config );
			}

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_convert_articles' ) {
				$panel_html = self::get_convert_articles_to_posts_box( $kb_config );
			}

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_convert_cpt' ) {
				$panel_html = self::get_convert_cpt_box( $kb_config );
			}

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_import_pdf' ) {
				$panel_html = self::get_import_pdf_panel( $kb_config );
			}

			$boxes[] = [
				'title' => $box['title'],
				'class' => $box_panel_class,
				'html'  => apply_filters( 'epkb_config_page_export_import_panel_html', $panel_html, $kb_config, $box ),
			];
		}

		return $boxes;
	}

	/**
	 * Get config for boxes for Tools panel, convert subpanel
	 * @return array
	 */
	private static function get_convert_boxes_config() {

		$is_ai_features_pro_enabled = EPKB_Utilities::is_ai_features_pro_enabled();

		return [
			[
				'plugin'        => 'core',
				'icon'          => 'epkbfa epkbfa-map-signs',
				'title'         => esc_html__( 'Convert Posts to KB Articles', 'echo-knowledge-base' ),
				'desc'          => esc_html__( 'Convert your blog or regular posts into Knowledge Base articles.', 'echo-knowledge-base' ),
				'button_id'     => 'epkb_convert_posts',
				'button_title'  => esc_html__( 'Convert Posts', 'echo-knowledge-base' ),
				'docs'          => 'https://www.echoknowledgebase.com/documentation/convert-posts-cpts-to-articles/',
				'active_status' => true
			],
			[
				'plugin'        => 'core',
				'icon'          => 'epkbfa epkbfa-download',
				'title'         => esc_html__( 'Import Articles from Other Knowledge Base Plugins', 'echo-knowledge-base' ),
				'desc'          => esc_html__( 'Convert your blog or custom post types into Knowledge Base articles.', 'echo-knowledge-base' ),
				'button_id'     => 'epkb_convert_cpt',
				'button_title'  => esc_html__( 'Convert From Another KB', 'echo-knowledge-base' ),
				'active_status' => true
			],
			[
				'plugin'        => 'core',
				'icon'          => 'epkbfa epkbfa-map-signs',
				'title'         => esc_html__( 'Convert KB Articles to Posts', 'echo-knowledge-base' ),
				'desc'          => esc_html__( 'Convert Knowledge Base articles to regular Posts.', 'echo-knowledge-base' ),
				'button_id'     => 'epkb_convert_articles',
				'button_title'  => esc_html__( 'Convert Articles', 'echo-knowledge-base' ),
				'active_status' => true
			],
			[
				'plugin'        => 'ai' . 'fp',
				'icon'          => 'epkbfa epkbfa-file-pdf-o',
				'title'         => esc_html__( 'Convert PDFs to Articles', 'echo-knowledge-base' ),
				'title_class'   => 'epkb-kbnh__feature-name',
				'desc'          => esc_html__( 'Upload PDF files and convert them into Knowledge Base articles.', 'echo-knowledge-base' ),
				'desc_escaped'  => self::get_pdf_import_features_list_html( ! $is_ai_features_pro_enabled ),
				'button_id'     => $is_ai_features_pro_enabled ? 'epkb_import_pdf' : '',
				'button_title'  => esc_html__( 'Import PDF', 'echo-knowledge-base' ),
				'learn_more'    => $is_ai_features_pro_enabled ? '' : self::get_ai_pro_features_admin_url(),
			],
		];
	}


	/*******         Convert Posts to Articles        *****************/

	/**
	 * Convert Posts to Articles.
	 * @param $kb_config
	 * @return false|string
	 */
	private static function get_convert_posts_to_articles_box( $kb_config ) {
		ob_start(); ?>

		<div class="epkb-form-wrap epkb-import-form epkb-convert-form epkb-convert-form--posts">    <?php
			self::show_convert_header_html(); ?>
			<div class="epkb-import-body">
				<div class="epkb-import-step epkb-import-step--1">  <?php
					self::show_convert_posts_step_1( $kb_config );  ?>
				</div>
				<div class="epkb-import-step epkb-import-step--2 epkb-hidden">  <?php
					self::show_convert_posts_step_2();  ?>
				</div>
				<div class="epkb-import-step epkb-import-step--3 epkb-hidden">  <?php
					self::show_convert_posts_step_3();  ?>
				</div>
				<div class="epkb-import-step epkb-import-step--4 epkb-hidden">  <?php
					self::show_convert_posts_step_4();  ?>
				</div>
			</div>  <?php
			self::show_convert_footer_html( $kb_config );   ?>
		</div>  <?php

		return ob_get_clean();
	}

	/**
	 * HTML for convert post step
	 *
	 * @param $kb_config
	 */
	private static function show_convert_posts_step_1( $kb_config ) { ?>
		<form class="convert-main-form">
			<div class="epkb-form-field-instruction-wrap">
				<div class="epkb-form-field-instruction-column">
					<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Features', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-form-field-instruction-item">
						<div class="epkb-form-field-instruction-icon">
							<i class="epkbfa epkbfa-check"></i>
						</div>
						<div class="epkb-form-field-instruction-text">
							<?php esc_html_e( 'Convert Posts', 'echo-knowledge-base' ); ?>
						</div>
					</div>
					<div class="epkb-form-field-instruction-item">
						<div class="epkb-form-field-instruction-icon">
							<i class="epkbfa epkbfa-check"></i>
						</div>
						<div class="epkb-form-field-instruction-text">
							<?php esc_html_e( 'Copy or Move Categories', 'echo-knowledge-base' ); ?>
						</div>
					</div>
				</div>

				<div class="epkb-form-field-instruction-column">
					<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Not Supported', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-form-field-instruction-item">
						<div class="epkb-form-field-instruction-icon">
							<i class="epkbfa epkbfa-close"></i>
						</div>
						<div class="epkb-form-field-instruction-text">
							<?php esc_html_e( 'Categories hierarchy', 'echo-knowledge-base' ); ?>
						</div>
					</div>
				</div>

				<div class="epkb-form-field-instructions">
					<p><?php esc_html_e( 'Instructions:', 'echo-knowledge-base' ); ?></p>
					<ul>
						<li><?php esc_html_e( 'Test conversion on your staging or test site before converting posts in production.', 'echo-knowledge-base' ); ?></li>
						<li><?php esc_html_e( 'Always back up your database before starting the conversion.', 'echo-knowledge-base' ); ?></li>
						<li><?php esc_html_e( 'Ensure that you are converting posts into the correct KB.', 'echo-knowledge-base' ); ?></li>
					</ul>
					<p><a href="https://www.echoknowledgebase.com/documentation/convert-posts-cpts-to-articles/"
					      class="epkb-form-field-instructions__link" target="_blank"><?php esc_html_e( 'Read complete instructions here', 'echo-knowledge-base' ); ?></a>
					</p>
				</div>

			</div>
			<input type="hidden" name="epkb_convert_post_type" value="post"><?php

			if ( EPKB_Utilities::is_multiple_kbs_enabled() ) { ?>
				<label class="epkb-form-label">
				<input class="epkb-form-label__input epkb-form-label__input--checkbox import-kb-name-checkbox"
				       type="checkbox" name="epkb_convert_post" required>
				<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I want to convert posts into this KB:', 'echo-knowledge-base' ); ?>
	                <span class="epkb-admin__distinct-box epkb-admin__distinct-box--middle"><?php echo esc_html( $kb_config['kb_name'] ); ?></span></span>
				</label><?php
			} ?>

			<label class="epkb-form-label">
				<input class="epkb-form-label__input epkb-form-label__input--checkbox import-backup-checkbox"
				       type="checkbox" name="epkb_convert_backup" required>
				<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I have backed up my database and read all import instructions above.', 'echo-knowledge-base' ); ?></span>
			</label>
		</form><?php
	}

	/**
	 * HTML for convert step
	 */
	private static function show_convert_posts_step_2() {
		self::progress_bar_html( esc_html__( 'Reading items', 'echo-knowledge-base' ) );
	}

	/**
	 * HTML for import step
	 */
	private static function show_convert_posts_step_3() {
		// Will be filled with AJAX
	}

	/**
	 * HTML for import step
	 */
	private static function show_convert_posts_step_4() {
		self::progress_bar_html( esc_html__( 'Convert Progress', 'echo-knowledge-base' ) ); ?>

		<div class="epkb-import-error-messages epkb-hidden"><?php
		$title = esc_html__( 'Errors during convert', 'echo-knowledge-base' );
		$description = '';
		$table_header = [
			__( 'Article Title', 'echo-knowledge-base' ),
			__( 'File Link', 'echo-knowledge-base' ),
			' ',
			' '
		];

		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo EPKB_Convert::display_import_table( $title, $description, $table_header, [], 'error', '' ); ?>
		</div><?php
	}


	/*******         Convert CPTs to Articles         *****************/

	/**
	 * @param $kb_config
	 * @return false|string
	 */
	private static function get_convert_cpt_box( $kb_config ) {
		ob_start(); ?>
		<div class="epkb-form-wrap epkb-import-form epkb-convert-form epkb-convert-form--posts">    <?php
		self::show_convert_header_html( 'cpt' );    ?>
		<div class="epkb-import-body">
			<div class="epkb-import-step epkb-import-step--1">  <?php
				self::show_convert_cpt_step_1( $kb_config );    ?>
			</div>
			<div class="epkb-import-step epkb-import-step--2 epkb-hidden">  <?php
				self::show_convert_posts_step_2();  ?>
			</div>
			<div class="epkb-import-step epkb-import-step--3 epkb-hidden">  <?php
				self::show_convert_posts_step_3();  ?>
			</div>
			<div class="epkb-import-step epkb-import-step--4 epkb-hidden">  <?php
				self::show_convert_posts_step_4();  ?>
			</div>
		</div>  <?php
		self::show_convert_footer_html( $kb_config );   ?>
		</div><?php

		return ob_get_clean();
	}

	/**
	 * HTML for convert post step
	 *
	 * @param $kb_config
	 */
	private static function show_convert_cpt_step_1( $kb_config ) {
		$custom_post_types = self::get_eligible_cpts(); ?>
		<form class="convert-main-form">
		<div class="epkb-form-field-instruction-wrap">
			<div class="epkb-form-field-instruction-column">
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Features', 'echo-knowledge-base' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">							<?php
						esc_html_e( 'Convert CPT', 'echo-knowledge-base' ); ?>
					</div>
				</div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">							<?php
						esc_html_e( 'Copy or Move Categories', 'echo-knowledge-base' ); ?>
					</div>
				</div>
			</div>

			<div class="epkb-form-field-instruction-column">
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Not Supported', 'echo-knowledge-base' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-close"></i>
					</div>
					<div class="epkb-form-field-instruction-text">							<?php
						esc_html_e( 'Categories hierarchy', 'echo-knowledge-base' ); ?>
					</div>
				</div>
			</div>

			<div class="epkb-form-field-instructions">
				<p><?php esc_html_e( 'Instructions:', 'echo-knowledge-base' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'Test conversion on your staging or test site before converting posts in production.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Always back up your database before starting the conversion.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Ensure that you are converting posts into the correct KB.', 'echo-knowledge-base' ); ?></li>
				</ul>
				<p><a href="https://www.echoknowledgebase.com/documentation/convert-posts-cpts-to-articles/"
				      class="epkb-form-field-instructions__link"><?php esc_html_e( 'Read complete instructions here', 'echo-knowledge-base' ); ?></a>
				</p>
			</div>

		</div>

		<label class="epkb-form-label">
			<span class="epkb-form-label__select"><?php esc_html_e( 'Convert CPT:', 'echo-knowledge-base' ); ?></span>
			<select name="epkb_convert_post_type">
				<option value="" selected><?php esc_html_e( 'Select Post Type', 'echo-knowledge-base' ); ?></option><?php
				foreach ( $custom_post_types as $post_type => $post_label ) { ?>
					<option value="<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( $post_label ); ?></option><?php
				} ?>
			</select>
		</label><?php

		if ( EPKB_Utilities::is_multiple_kbs_enabled() ) { ?>
			<label class="epkb-form-label">
			<input class="epkb-form-label__input epkb-form-label__input--checkbox import-kb-name-checkbox"
			       type="checkbox" name="epkb_convert_post" required>
			<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I want to convert articles into this KB:', 'echo-knowledge-base' ); ?> <strong
						class="epkb-admin__distinct-box epkb-admin__distinct-box--middle"><?php echo esc_html( $kb_config['kb_name'] ); ?></strong></span>
			</label><?php
		} ?>

		<label class="epkb-form-label">
			<input class="epkb-form-label__input epkb-form-label__input--checkbox import-backup-checkbox"
			       type="checkbox" name="epkb_convert_backup" required>
			<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I have backed up my database and read all import instructions above.', 'echo-knowledge-base' ); ?></span>
		</label>
		</form><?php
	}


	/*******         Convert Articles to Posts         *****************/

	/**
	 * Convert Articles to Posts.
	 * @param $kb_config
	 * @return false|string
	 */
	private static function get_convert_articles_to_posts_box( $kb_config ) {
		ob_start(); ?>

		<div class="epkb-form-wrap epkb-import-form epkb-convert-form epkb-convert-form--posts">    <?php
			self::show_convert_header_html( 'article' ); ?>
			<div class="epkb-import-body">
				<div class="epkb-import-step epkb-import-step--1">  <?php
					self::show_convert_articles_step_1( $kb_config );  ?>
				</div>
				<div class="epkb-import-step epkb-import-step--2 epkb-hidden">  <?php
					self::show_convert_articles_step_2();  ?>
				</div>
				<div class="epkb-import-step epkb-import-step--3 epkb-hidden">  <?php
					self::show_convert_articles_step_3();  ?>
				</div>
				<div class="epkb-import-step epkb-import-step--4 epkb-hidden">  <?php
					self::show_convert_articles_step_4();  ?>
				</div>
			</div>  <?php
			self::show_convert_footer_html( $kb_config );   ?>
		</div>  <?php

		return ob_get_clean();
	}

	/**
	 * HTML for convert post step
	 *
	 * @param $kb_config
	 */
	private static function show_convert_articles_step_1( $kb_config ) { ?>
		<form class="convert-main-form">
		<div class="epkb-form-field-instruction-wrap">
			<div class="epkb-form-field-instruction-column">
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Features', 'echo-knowledge-base' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Convert Articles', 'echo-knowledge-base' ); ?>
					</div>
				</div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Copy or Move Categories', 'echo-knowledge-base' ); ?>
					</div>
				</div>
			</div>

			<div class="epkb-form-field-instruction-column">
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Not Supported', 'echo-knowledge-base' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-close"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Categories hierarchy', 'echo-knowledge-base' ); ?>
					</div>
				</div>
			</div>

			<div class="epkb-form-field-instructions">
				<p><?php esc_html_e( 'Instructions:', 'echo-knowledge-base' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'Test conversion on your staging or test site before converting articles in production.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Always back up your database before starting the conversion.', 'echo-knowledge-base' ); ?></li>
				</ul>
			</div>
			<input type='hidden' name='epkb_convert_post_type' value='article'>

		</div>
		<label class="epkb-form-label">
			<input class="epkb-form-label__input epkb-form-label__input--checkbox import-backup-checkbox"
			       type="checkbox" name="epkb_convert_backup" required>
			<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I have backed up my database and read all import instructions above.', 'echo-knowledge-base' ); ?></span>
		</label>
		</form><?php
	}

	/**
	 * HTML for convert step
	 */
	private static function show_convert_articles_step_2() {
		self::progress_bar_html( esc_html__( 'Reading articles', 'echo-knowledge-base' ) );
	}

	/**
	 * HTML for import step
	 */
	private static function show_convert_articles_step_3() {
		// Will be filled with AJAX
	}

	/**
	 * HTML for import step
	 */
	private static function show_convert_articles_step_4() {
		self::progress_bar_html( esc_html__( 'Convert Progress', 'echo-knowledge-base' ) ); ?>

		<div class="epkb-import-error-messages epkb-hidden"><?php
			$title = esc_html__( 'Errors during convert', 'echo-knowledge-base' );
			$description = '';
			$table_header = [
				esc_html__( 'Post Title', 'echo-knowledge-base' ),
				esc_html__( 'File Link', 'echo-knowledge-base' ),
				' ',
				' '
			];

			//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo EPKB_Convert::display_import_table( $title, $description, $table_header, [], 'error', '' ); ?>
		</div><?php
	}


	/*******         Convert PDFs to Articles        *****************/

	/**
	 * Get features list HTML for the PDF import feature box.
	 *
	 * @param bool $show_discount_coupon
	 * @return string
	 */
	private static function get_pdf_import_features_list_html( $show_discount_coupon = false ) {
		$features = [
			esc_html__( 'PDF to Article', 'echo-knowledge-base' ),
			esc_html__( 'PDF to Article using AI', 'echo-knowledge-base' ),
			esc_html__( 'Bulk PDF to Articles', 'echo-knowledge-base' ),
		];
		$html = '<div class="epkb-pdf-import__box-features"><div class="epkb-form-field-instruction-title">' . esc_html__( 'Features', 'echo-knowledge-base' ) . '</div>';
		foreach ( $features as $feature ) {
			$html .= '<div class="epkb-form-field-instruction-item">' .
				'<div class="epkb-form-field-instruction-icon"><i class="epkbfa epkbfa-check"></i></div>' .
				'<div class="epkb-form-field-instruction-text">' . $feature . '</div></div>';
		}

		if ( $show_discount_coupon ) {
			$html .= self::get_pdf_import_discount_coupon_html();
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get the admin URL for the AI PRO Features tab.
	 *
	 * @return string
	 */
	private static function get_ai_pro_features_admin_url() {
		return admin_url( 'edit.php?post_type=epkb_post_type_1&page=epkb-kb-ai-features&active_tab=pro-features' );
	}

	/**
	 * Get discount coupon HTML for the gated PDF import feature box.
	 *
	 * @return string
	 */
	private static function get_pdf_import_discount_coupon_html() {
		$coupon = EPKB_AI_PRO_Features_Tab::get_discount_coupon();

		return EPKB_HTML_Forms::get_discount_coupon_box_html( $coupon, 'epkb-pdf-import__discount-coupon' );
	}

	/**
	 * Render the Import PDF panel.
	 *
	 * @param $kb_config
	 * @return false|string
	 */
	private static function get_import_pdf_panel( $kb_config ) {

		$categories = EPKB_Core_Utilities::get_kb_categories_visible( $kb_config['id'] );
		$categories = is_array( $categories ) ? $categories : [];

		ob_start(); ?>

		<div class="epkb-form-wrap epkb-import-form epkb-pdf-import-form">
			<div class="epkb-import-body">

				<!-- Step 1: Options -->
				<div class="epkb-pdf-import-step epkb-pdf-import-step--1">

					<!-- Conversion Mode -->
					<div class="epkb-pdf-import__option-group">
						<label class="epkb-pdf-import__option-label"><?php esc_html_e( 'Conversion Mode', 'echo-knowledge-base' ); ?></label>
						<label class="epkb-form-label">
							<input type="radio" name="epkb_pdf_conversion_mode" value="basic" checked>
							<span><?php esc_html_e( 'Basic formatting', 'echo-knowledge-base' ); ?></span><?php
							EPKB_HTML_Elements::display_tooltip( '', esc_html__( 'Extracts text from PDF and wraps it in paragraphs. Fast and preserves all content as-is.', 'echo-knowledge-base' ) ); ?>
						</label>
						<label class="epkb-form-label">
							<input type="radio" name="epkb_pdf_conversion_mode" value="ai">
							<span><?php esc_html_e( 'AI-structured', 'echo-knowledge-base' ); ?></span><?php
							EPKB_HTML_Elements::display_tooltip( '', esc_html__( 'Uses AI to organize the extracted text into headings, lists, and paragraphs. Cleans up PDF artifacts like broken words and page numbers.', 'echo-knowledge-base' ) ); ?>
						</label>
					</div>

					<!-- File Upload -->
					<div class="epkb-pdf-import__option-group">
						<label class="epkb-pdf-import__option-label"><?php esc_html_e( 'Select PDF Files', 'echo-knowledge-base' ); ?></label>
						<div class="epkb-pdf-import__dropzone" id="epkb-pdf-dropzone">
							<input type="file" id="epkb-pdf-file-input" class="epkb-pdf-import__file-input" accept=".pdf" multiple="multiple" style="display:none;">
							<i class="epkbfa epkbfa-file-pdf-o epkb-pdf-import__dropzone-icon"></i>
							<p class="epkb-pdf-import__dropzone-text"><?php esc_html_e( 'Drag and drop PDF files here, or click the buttons below', 'echo-knowledge-base' ); ?></p>
							<div style="display: flex; gap: 10px; justify-content: center;">
								<button type="button" class="epkb-pdf-import__dropzone-btn" id="epkb-pdf-select-btn">
									<i class="epkbfa epkbfa-upload"></i>
									<?php esc_html_e( 'Select PDFs', 'echo-knowledge-base' ); ?>
								</button>
								<button type="button" class="epkb-pdf-import__dropzone-btn epkb-pdf-import__dropzone-btn--secondary" id="epkb-pdf-media-library-btn">
									<i class="epkbfa epkbfa-image"></i>
									<?php esc_html_e( 'Media Library', 'echo-knowledge-base' ); ?>
								</button>
							</div>
						</div>
						<div class="epkb-pdf-import__file-list" id="epkb-pdf-file-list"></div>
					</div>

					<!-- Category -->
					<div class="epkb-pdf-import__option-group">
						<label class="epkb-pdf-import__option-label"><?php esc_html_e( 'Category', 'echo-knowledge-base' ); ?></label>
						<select id="epkb-pdf-category">
							<option value="0"><?php esc_html_e( '-- No Category --', 'echo-knowledge-base' ); ?></option><?php
							foreach ( $categories as $category ) { ?>
								<option value="<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></option><?php
							} ?>
						</select>
					</div>

					<!-- Status of Imported Articles -->
					<div class="epkb-pdf-import__option-group">
						<label class="epkb-pdf-import__option-label"><?php esc_html_e( 'Status of Imported Articles', 'echo-knowledge-base' ); ?></label>
						<label class="epkb-form-label">
							<input type="radio" name="epkb_pdf_post_status" value="draft" checked>
							<span><?php esc_html_e( 'Draft', 'echo-knowledge-base' ); ?></span>
						</label>
						<label class="epkb-form-label">
							<input type="radio" name="epkb_pdf_post_status" value="publish">
							<span><?php esc_html_e( 'Published', 'echo-knowledge-base' ); ?></span>
						</label>
					</div>

					<!-- Debug Mode -->
					<div class="epkb-pdf-import__option-group">
						<label class="epkb-form-label">
							<input type="checkbox" id="epkb-pdf-debug-toggle">
							<span><?php esc_html_e( 'Debug Mode', 'echo-knowledge-base' ); ?></span>
						</label>
					</div>

				</div>

				<!-- Step 2: Review -->
				<div class="epkb-pdf-import-step epkb-pdf-import-step--2 epkb-hidden">
					<h3><?php esc_html_e( 'Convert PDFs', 'echo-knowledge-base' ); ?></h3>
					<div class="epkb-pdf-import__batch-summary">
						<div class="epkb-pdf-import__batch-summary-status">
							<strong id="epkb-pdf-batch-status-label"><?php esc_html_e( 'Ready to convert', 'echo-knowledge-base' ); ?></strong>
							<span id="epkb-pdf-batch-status-detail"></span>
						</div>
						<div class="epkb-pdf-import__batch-summary-counts" id="epkb-pdf-batch-summary-counts"></div>
					</div>
					<div class="epkb-pdf-import__review-layout">
						<div class="epkb-pdf-import__review-sidebar" id="epkb-pdf-review-sidebar"></div>
						<div class="epkb-pdf-import__review-panel">
							<div class="epkb-pdf-import__review-header">
								<strong id="epkb-pdf-review-counter"></strong>
								<span id="epkb-pdf-review-file-name"></span>
							</div>
							<div class="epkb-pdf-import__review-status" id="epkb-pdf-review-status"></div>
							<div class="epkb-pdf-import__option-group">
								<label class="epkb-pdf-import__option-label" for="epkb-pdf-review-title"><?php esc_html_e( 'Article Title', 'echo-knowledge-base' ); ?></label>
								<input type="text" id="epkb-pdf-review-title" class="epkb-input epkb-pdf-import__title-input" />
							</div>
							<div class="epkb-pdf-import__option-group">
								<span class="epkb-pdf-import__option-label"><?php esc_html_e( 'HTML Preview', 'echo-knowledge-base' ); ?></span>
								<div class="epkb-pdf-import__preview" id="epkb-pdf-review-preview"></div>
							</div>
							<div class="epkb-pdf-import__review-actions">
								<button type="button" class="epkb-default-btn epkb-pdf-import__review-retry-btn epkb-hidden"><?php esc_html_e( 'Retry', 'echo-knowledge-base' ); ?></button>
							</div>
						</div>
					</div>
				</div>

				<!-- Step 3: Results -->
				<div class="epkb-pdf-import-step epkb-pdf-import-step--3 epkb-hidden">
					<h3><?php esc_html_e( 'Import Complete', 'echo-knowledge-base' ); ?></h3>
					<div class="epkb-pdf-import__results" id="epkb-pdf-results"></div>
				</div>

				<!-- Debug Output -->
				<div class="epkb-pdf-import__debug-wrap epkb-hidden" id="epkb-pdf-debug-wrap">
					<label class="epkb-pdf-import__option-label"><?php esc_html_e( 'Debug Log', 'echo-knowledge-base' ); ?></label>
					<textarea class="epkb-pdf-import__debug-log" id="epkb-pdf-debug-log" readonly></textarea>
				</div>

			</div>

			<div class="epkb-import-footer">
				<button type="button" class="epkb-default-btn epkb-pdf-import__back-btn"><?php echo '< ' . esc_html__( 'Back', 'echo-knowledge-base' ); ?></button>
				<button type="button" class="epkb-error-btn epkb-hidden epkb-pdf-import__cancel-btn"><?php esc_html_e( 'Cancel', 'echo-knowledge-base' ); ?></button>
				<button type="button" class="epkb-primary-btn epkb-hidden epkb-pdf-import__save-all-btn"><?php esc_html_e( 'Save All Selected', 'echo-knowledge-base' ); ?></button>
				<button type="button" class="epkb-primary-btn epkb-pdf-import__start-btn" data-kb_id="<?php echo esc_attr( $kb_config['id'] ); ?>">
					<?php esc_html_e( 'Start Import', 'echo-knowledge-base' ); ?>
				</button>
			</div>
		</div><?php

		return ob_get_clean();
	}


	/*******         OTHER         *****************/

	/**
	 * HTML for convert header
	 *
	 * @param string $type
	 */
	private static function show_convert_header_html( $type = 'post' ) {

		$step_4_text = $type == 'post' ? esc_html__( 'Choose Posts', 'echo-knowledge-base' ) : esc_html__( 'Choose Articles', 'echo-knowledge-base' );
		$step_4_title = $type == 'cpt' ? esc_html__( 'Convert CPT', 'echo-knowledge-base' ) : esc_html__( 'Convert Posts', 'echo-knowledge-base' );		?>

		<div class="epkb-import-header">
			<div class="epkb-import-step-label epkb-import-step--1 epkb-import-step--done" data-step="1">
				<i class="epkbfa epkbfa-check"></i>
				<span><?php esc_html_e( 'Begin', 'echo-knowledge-base' ); ?></span>
			</div>
			<div class="epkb-import-step-label epkb-import-step--2" data-step="2">
				<i class="epkbfa epkbfa-check"></i>
				<span><?php echo esc_html( $step_4_text ); ?></span>
			</div>
			<div class="epkb-import-step-label epkb-import-step--3" data-step="3">
				<i class="epkbfa epkbfa-check"></i>
				<span><?php esc_html_e( 'Choose Options', 'echo-knowledge-base' ); ?></span>
			</div>
			<div class="epkb-import-step-label epkb-import-step--4 " data-step="4">
				<i class="epkbfa epkbfa-check"></i>
				<span><?php echo esc_html( $step_4_title ); ?></span>
			</div>
		</div><?php

		self::maybe_show_wp_version_warning();
	}

	/**
	 * Show user warning if WordPress version less than 5.6
	 */
	private static function maybe_show_wp_version_warning() {
		global $wp_version;

		if ( version_compare( $wp_version, '5.6', '>=' ) ) {
			return;
		}

		EPKB_HTML_Forms::notification_box_middle( [
			'title'  => esc_html__( 'Old version of WordPress detected', 'echo-knowledge-base' ),
			'type'   => 'error',
			'static' => true,
			'desc'   => esc_html__( 'This website is using an old version of WordPress. Unpredictable behaviour and errors during conversion can occur for this old WordPress version. Please update to the latest version of WordPress. Support is very limited for old versions of WordPress.', 'echo-knowledge-base' ),
		] );
	}

	/**
	 * HTML for convert footer
	 * @param $kb_config
	 */
	private static function show_convert_footer_html( $kb_config ) { ?>
		<div class="epkb-import-footer">
			<button type="button" class="epkb-default-btn epkb-convert-button-back"><?php echo '< ' . esc_html__( 'Back', 'echo-knowledge-base' ); ?></button>
			<button type="button" class="epkb-default-btn epkb-hidden epkb-convert-button-exit">
				<?php esc_html_e( 'Exit', 'echo-knowledge-base' ); ?>
			</button>
			<button type="button" class="epkb-error-btn epkb-hidden epkb-convert-button-cancel">
				<?php esc_html_e( 'Stop', 'echo-knowledge-base' ); ?>
			</button>
			<button type="button" class="epkb-primary-btn epkb-convert-button-next"
					data-kb_id="<?php echo esc_attr( $kb_config['id'] ); ?>">
				<?php esc_html_e( 'Next Step >', 'echo-knowledge-base' ); ?>
			</button>
			<button type="button" class="epkb-primary-btn epkb-hidden epkb-convert-button-start_convert"
					data-kb_id="<?php echo esc_attr( $kb_config['id'] ); ?>">
				<?php esc_html_e( 'Start Converting', 'echo-knowledge-base' ); ?>
			</button>
		</div><?php
	}

	/**
	 * HTML for progress bar. Working with admin-ui.js bar function
	 * @param $title
	 */
	public static function progress_bar_html( $title ) { ?>
		<div class="epkb-progress">
			<h3><?php echo esc_html( $title ); ?> <span class="epkb-progress__percentage"></span></h3>
			<div class="epkb-progress__bar ">
				<div style="width:0;"></div>
			</div>
			<div class="epkb-progress__log"></div>
		</div>
		<div class="epkb-data-status-log"></div><?php
	}

	/**
	 * Return array of slug => name pairs eligible for CPT converting
	 */
	private static function get_eligible_cpts() {

		$disallowed_post_types = [ 'page', 'post', 'wp_template', 'attachment', 'elementor_library' ];

		$cpts = EPKB_Utilities::get_post_type_labels( $disallowed_post_types, [], true );

		// for epie
		return apply_filters( 'epkb_convert_post_types', $cpts );
	}

	/*
	 * Get boxes for Tools panel, Debug subpanel
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function get_debug_boxes( $kb_config ) {

		return array(

			// Box: KB Information required for support
			array(
				'title' => esc_html__( 'Information required for support', 'echo-knowledge-base' ),
				'description' => '',
				'html' => self::display_debug_info( $kb_config ),
			),

			// Box: Information required for support
			array(
				'title' => esc_html__( 'Debug logs', 'echo-knowledge-base' ),
				'description' => esc_html__( 'Enable logs when instructed by the support team.', 'echo-knowledge-base' ),
				'html' => self::display_error_info(),
			),

			// Box: Advanced Search Information required for support
			/* EPKB_Utilities::is_advanced_search_enabled() ? array(
				'title'       => esc_html__( 'Advanced Search', 'echo-knowledge-base' ),
				'description' => esc_html__( 'Enable debug when instructed by the support team.', 'echo-knowledge-base' ),
				'html'        => self::display_asea_debug_info( $kb_config ),
			) : '', */

			EPKB_Reset::get_reset_sequence_box_config(),
		);
	}

	/**
	 * Get boxes for Tools panel, Other subpanel
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function get_other_boxes( $kb_config ) {

		$delete_kb_handler = new EPKB_Delete_KB();
		$specification = EPKB_Core_Utilities::retrieve_all_kb_specs_with_labels( $kb_config['id'] );

		// KB Nickname
		$boxes_config[] = array(
			'title' => esc_html__( 'KB Nickname', 'echo-knowledge-base' ),
			'html'  => self::get_kb_nickname_html( $kb_config ),
		);

		// Sidebar Introduction Text - only show if using Sidebar Layout (shortcode or block)
		if ( EPKB_Utilities::is_elegant_layouts_enabled() ) {
			$boxes_config[] = array(
				'title' => esc_html__( 'Sidebar Layout - Introduction Text', 'echo-knowledge-base' ),
				'html'  => self::get_sidebar_intro_text_html( $kb_config ),
			);
		}

		// Translations
		if ( ! EPKB_Utilities::is_amag_on() ) {
			$boxes_config[] = array(
				'title' => esc_html__( 'Translations', 'echo-knowledge-base' ),
				'html'  => EPKB_HTML_Elements::checkbox_toggle( [
					'id'            => 'wpml_is_enabled',
					'name'          => 'wpml_is_enabled',
					'text'          => EPKB_KB_Config_Specs::get_field_label( 'wpml_is_enabled' ),
					'textLoc'       => 'left',
					'checked'       => ( ! empty( $kb_config['wpml_is_enabled'] ) && $kb_config['wpml_is_enabled'] == 'on' ),
					'topDesc'       => '<a class="epkb-admin__input-field-desc" href="https://www.echoknowledgebase.com/documentation/translate-text/" target="_blank">' . esc_html__( 'Follow Polyland and WPML setup instructions here.', 'echo-knowledge-base' ) . '</a>',
					'return_html'   => true,
				] ),
			);
		}

		// Archive or Delete KB
		$boxes_config[] = array(
			'class' => 'epkb-admin__boxes-list__box--row-style',
			'title' => esc_html__( 'Archive or Delete Selected KB', 'echo-knowledge-base' ),
			'html'  => $delete_kb_handler->get_archive_or_delete_kb_form( $kb_config ),
		);

		$boxes_config[] = array(
			'title' => esc_html__( 'Category Slug', 'echo-knowledge-base' ),
			'html'  => self::display_category_slug_html( $kb_config ),
		);

		$boxes_config[] = array(
			'title' => esc_html__( 'Tag Slug', 'echo-knowledge-base' ),
			'html'  => self::display_tag_slug_html( $kb_config ),
		);

		// Search Query Parameter
		if ( EPKB_Utilities::is_advanced_search_enabled() ) {
			$kb_config = apply_filters( 'eckb_kb_config', $kb_config );
			if ( ! empty( $kb_config['search_query_param'] ) ) {
				// Optimization
				$boxes_config[] = array(
					'title' => esc_html__( 'KB Search Query Parameter', 'echo-knowledge-base' ),
					'html'  => self::display_search_query_html( $kb_config ),
				);
			}
		}

		// Optimization
		$boxes_config[] = array(
			'title' => esc_html__( 'Optimization', 'echo-knowledge-base' ),
			'html'  => EPKB_HTML_Elements::checkbox_toggle( [
				'id'            => 'preload_fonts',
				'name'          => 'preload_fonts',
				'text'          => esc_html__( 'Preload Fonts', 'echo-knowledge-base' ),
				'textLoc'       => 'left',
				'checked'       => EPKB_Core_Utilities::is_kb_flag_set( 'preload_fonts' ),
				'topDesc'       => esc_html__( 'KB can preload its fonts for better performance. Some cache plugins preload fonts themselves, which can result in KB fonts being preloaded twice. Disable this option to avoid a conflict with your cache plugin.', 'echo-knowledge-base' ),
				'return_html'   => true,
			] ),
		);

		// Box: Delete All KBs Data
		$boxes_config[] = array(
			'title' => esc_html__( 'Delete All Plugin Data', 'echo-knowledge-base' ),
			'html'  => $delete_kb_handler->get_delete_all_kbs_data_form(),
		);

		return $boxes_config;
	}

	/**
	 * Display Debug page.
	 *
	 * @param $kb_config
	 * @return false|string
	 */
	private static function display_debug_info( $kb_config ) {

		$is_debug_on = get_transient( EPKB_Debug_Controller::EPKB_DEBUG );
		$button_text = $is_debug_on ? esc_html__('Disable Debug', 'echo-knowledge-base') : esc_html__( 'Enable Debug', 'echo-knowledge-base' );

		ob_start();     ?>

		<div id="epkb_debug_info_tab_page">

			<section class="save-settings">    <?php
				EPKB_HTML_Elements::submit_button_v2( $button_text, 'epkb_toggle_debug', 'epkb_toggle_debug','' ,true ,'' ,'epkb-primary-btn' ); ?>
			</section>  <?php

			if ( ! empty( $is_debug_on ) ) { ?>
				<section>
					<h3><?php esc_html_e( 'Debug Information:', 'echo-knowledge-base' ); ?></h3>
				</section>     <?php

				echo wp_kses_post( self::display_debug_data() ); ?>

				<form action="<?php echo esc_url( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . '1&page=epkb-add-ons' ) ); ?>" method="post" dir="ltr">
					<section style="padding-top: 20px;" class="save-settings checkbox-input"><?php
						EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Download System Information', 'echo-knowledge-base' ), 'epkb_download_debug_info', 'epkb_download_debug_info', '', true, '' , 'epkb-primary-btn' ); ?>
					</section>
					<input type="hidden" name="epkb_debug_box" value="main">
					<input type="hidden" name="kb_id" value="<?php echo esc_attr( $kb_config['id'] ); ?>">
				</form>

				<form action="<?php echo esc_url( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . '1&page=epkb-add-ons' ) ); ?>" method="post" dir="ltr">
					<section class="save-settings checkbox-input"><?php
						EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Export AI Configuration', 'echo-knowledge-base' ), 'epkb_download_ai_config', 'epkb_download_ai_config', '', true, '' , 'epkb-primary-btn' ); ?>
					</section>
					<input type="hidden" name="kb_id" value="<?php echo esc_attr( $kb_config['id'] ); ?>">
				</form>     <?php
			}   ?>

			<div id='epkb-ajax-in-progress-debug-switch' style="display:none;">
				<?php esc_html_e( 'Switching debug', 'echo-knowledge-base' ) . '... '; ?><img class="epkb-ajax waiting" style="height: 30px;"
				                                                                         src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif' ); ?>">
			</div>
		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Display Debug page.
	 * @return false|string
	 */
	private static function display_error_info() {

		$is_show_logs = get_transient( EPKB_Debug_Controller::EPKB_SHOW_LOGS );

		ob_start();     ?>

		<div id="epkb_error_info_tab_page">
			<section class="save-settings">    <?php

				if ( ! $is_show_logs ) {
					EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Show Logs', 'echo-knowledge-base' ), 'epkb_show_logs', 'epkb_show_logs', '', true, '', 'epkb-primary-btn' );
				}

				EPKB_HTML_Elements::submit_button_v2( esc_html__('Reset Logs', 'echo-knowledge-base'), 'epkb_reset_logs', 'epkb_reset_logs','' ,true ,'' ,'epkb-primary-btn' ); ?>
			</section><?php

			if (  $is_show_logs ) {
				echo wp_kses_post( self::display_debug_errors() );
			} ?>

		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Display debug errors
	 * @return string
	 */
	private static function display_debug_errors() {

		$output = '';

		$logs = EPKB_Logging::get_logs();

		if ( empty( $logs ) ) {
			return '<section class="epkb-debug-info-empty-logs"><h3>' . esc_html__( 'Logs are empty', 'echo-knowledge-base' ) . '</h3></section>';
		}
		$logs_by_plugin = [];

		foreach( $logs as $log ) {
			$plugin_name = empty ( $log['plugin'] ) ? 'default' : $log['plugin'];
			if ( empty ( $logs_by_plugin[$plugin_name] ) ) {
				$logs_by_plugin[$plugin_name] = [];
			}

			$logs_by_plugin[$plugin_name][] = $log;
		}

		foreach( $logs_by_plugin as $plugin_name => $sorted_logs ) {
			$output .= '<section><h3>';
			$output .= $plugin_name . ' ';
			$output .= esc_html__( 'Error Log:', 'echo-knowledge-base' );
			$output .= '</h3></section>';
			$output .= '<textarea rows="30" cols="150" style="overflow:scroll;">';

			foreach ( $sorted_logs as $log ) {
				$output .= empty( $log['kb'] ) ? '' : 'kb: ' . $log['kb'] . " ";
				$output .= empty( $log['date'] ) ? '' : $log['date'] . "\n";
				$output .= empty( $log['message'] ) ? '' : $log['message'] . "\n";
				$output .= empty( $log['trace'] ) ? '' : $log['trace'] . "\n\n";
			}

			$output .= '</textarea>';
		}

		// retrieve add-on data
		$add_on_output = apply_filters( 'eckb_add_on_error_data', '' );
		$output .= is_string( $add_on_output ) ? $add_on_output : '';

		// General simple log for all errors
		$output .= '<section><h3>';
		$output .= esc_html__( 'Simple Error Log', 'echo-knowledge-base' ) . ':';
		$output .= '</h3></section>';
		$output .= '<textarea rows="30" cols="150" style="overflow:scroll;">';
		foreach( $logs as $log ) {
			$output .= empty( $log['date'] ) ? '' : '[' . $log['date'] . ']: ';
			$output .= empty( $log['message'] ) ? '' : $log['message'] . "\n";
		}
		$output .= '</textarea>';

		return $output;
	}

	/**
	 * Display debug data
	 * @return string
	 */
	public static function display_debug_data() {
		/** @var $wpdb Wpdb */
		global $wpdb;

		// ensure user has correct permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			return EPKB_HTML_Forms::notification_box_middle( array(
				'type' => 'error',
				'desc' => esc_html__( 'No access', 'echo-knowledge-base' ),
			), true );
		}

		$epkb_version = EPKB_Utilities::get_wp_option( 'epkb_version', 'N/A' );

		$output = '<textarea rows="30" cols="150" style="overflow:scroll;">';

		// display KB configuration
		$output .= "KB Configurations:\n";
		$output .= "==================\n";

		$first_plugin_version = epkb_get_instance()->kb_config_obj->get_value( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'first_plugin_version' );
		$output .= "KB first version: " . ( empty( $first_plugin_version ) ? 'none' : $first_plugin_version ) . "\n";

		$output .= "KB version: " . $epkb_version . "\n";

		// display PHP and WP settings
		$output .= self::get_system_info();

		// retrieve AI debug data only if AI Chat or AI Search is enabled
		$output .= self::get_ai_debug_data();

		// retrieve KB config directly from the database
		$all_kb_ids = epkb_get_instance()->kb_config_obj->get_kb_ids();
		foreach ( $all_kb_ids as $kb_id ) {

			// retrieve specific KB configuration
			$kb_config = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s", EPKB_KB_Config_DB::KB_CONFIG_PREFIX . $kb_id ) );
			if ( ! empty( $kb_config ) ) {
				$kb_config = maybe_unserialize( $kb_config );
			}

			// with WPML we need to trigger hook to have configuration names translated
			if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
				$output .= "WPML Enabled---------- for KB ID " . $kb_id . "\n";
				$kb_config = get_option( EPKB_KB_Config_DB::KB_CONFIG_PREFIX . $kb_id );
			}

			// if KB configuration is missing then return error
			if ( empty( $kb_config ) || ! is_array( $kb_config ) ) {
				$output .= "Did not find KB configuration (DB231) for KB ID " . $kb_id . "\n\n";
				continue;
			}

			if ( count( $kb_config ) < 100 ) {
				$output .= "Found KB configuration is incomplete with only " . count( $kb_config ) . " items.\n";
			}

			$output .= 'KB Config ' . $kb_id . "\n\n";

			$output .= 'KB Main Page Has KB Blocks: ' . ( EPKB_Block_Utilities::kb_main_page_has_kb_blocks( $kb_config ) ? 'Yes' : 'No' ) . "\n";
			if ( EPKB_Block_Utilities::kb_main_page_has_kb_blocks( $kb_config ) ) {
				$kb_main_page = get_post( EPKB_KB_Handler::get_first_kb_main_page_id( $kb_config ) );
				$output .= 'KB Main Page Block Layout: ' . EPKB_Block_Utilities::get_kb_block_layout( $kb_main_page );
			}
			$output .= "\n\n";

			$output .= '- KB URL  = ' . EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config ) . "\n";
			$output .= "/* KB settings output disabled. */\n\n";

			// Add multilang debug info for this KB if WPML is enabled
			if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
				$output .= self::get_polylang_debug_info( $kb_id );
				$output .= "\n\n";
			}
		}

		// retrieve feature usage data
		$output .= self::get_feature_usage_debug_data();

		// retrieve Setup Steps debug data
		$output .= EPKB_Setup_Steps::get_debug_data( EPKB_KB_Config_DB::DEFAULT_KB_ID );

		// retrieve add-on data
		$add_on_output = apply_filters( 'eckb_add_on_debug_data', '' );
		$output .= is_string( $add_on_output ) ? $add_on_output : '';

		$output .= '</textarea>';

		return $output;
	}

	/**
	 * Get Glossary and Quizzes usage stats for debug output.
	 *
	 * @return string
	 */
	private static function get_feature_usage_debug_data() {
		global $wpdb;

		$all_kb_ids = epkb_get_instance()->kb_config_obj->get_kb_ids( true );
		$total_kbs = count( $all_kb_ids );
		$glossary_enabled_kb_ids = array();
		$quizzes_enabled_kb_ids = array();

		foreach ( $all_kb_ids as $kb_id ) {
			$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

			if ( ! empty( $kb_config['glossary_enable'] ) && $kb_config['glossary_enable'] === 'on' ) {
				$glossary_enabled_kb_ids[] = $kb_id;
			}

			if ( ! empty( $kb_config['quizzes_enable'] ) && $kb_config['quizzes_enable'] === 'on' ) {
				$quizzes_enabled_kb_ids[] = $kb_id;
			}
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Internal table names from $wpdb.
		$glossary_stats = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT COUNT(*) AS total_count,
						SUM( CASE WHEN COALESCE( glossary_terms.status_value, 'publish' ) = 'publish' THEN 1 ELSE 0 END ) AS published_count,
						SUM( CASE WHEN glossary_terms.status_value = 'draft' THEN 1 ELSE 0 END ) AS draft_count,
						SUM( CASE WHEN COALESCE( glossary_terms.sort_key_value, '' ) <> '' THEN 1 ELSE 0 END ) AS sort_key_count
				 FROM (
					SELECT tt.term_id,
						   MAX( CASE WHEN tm.meta_key = 'epkb_glossary_status' THEN tm.meta_value END ) AS status_value,
						   MAX( CASE WHEN tm.meta_key = 'epkb_glossary_sort_key' THEN tm.meta_value END ) AS sort_key_value
					FROM {$wpdb->term_taxonomy} tt
					LEFT JOIN {$wpdb->termmeta} tm ON tm.term_id = tt.term_id AND tm.meta_key IN ( 'epkb_glossary_status', 'epkb_glossary_sort_key' )
					WHERE tt.taxonomy = %s
					GROUP BY tt.term_id
				) glossary_terms",
				EPKB_Glossary_Taxonomy_Setup::GLOSSARY_TAXONOMY
			)
		);

		$quiz_count_obj = wp_count_posts( EPKB_Quizzes_CPT_Setup::QUIZ_POST_TYPE );
		$quizzes = EPKB_Quizzes_Utilities::get_quizzes();
		$quiz_source_article_ids = array();
		$quiz_total_questions = 0;
		$quiz_ai_generated_count = 0;
		$quiz_renderable_published_count = 0;

		foreach ( $quizzes as $quiz ) {
			$source_article_id = absint( get_post_meta( $quiz->ID, EPKB_Quizzes_Utilities::META_SOURCE_ARTICLE_ID, true ) );
			if ( ! empty( $source_article_id ) ) {
				$quiz_source_article_ids[ $source_article_id ] = $source_article_id;
			}

			$quiz_total_questions += count( EPKB_Quizzes_Utilities::get_quiz_questions( $quiz->ID ) );

			$generation_meta = get_post_meta( $quiz->ID, EPKB_Quizzes_Utilities::META_GENERATION_META, true );
			if ( ! empty( $generation_meta['generated_at'] ) ) {
				$quiz_ai_generated_count++;
			}

			if ( $quiz->post_status === 'publish' ) {
				$source_state = EPKB_Quizzes_Utilities::get_source_article_state( $source_article_id );
				if ( $source_state['is_renderable'] ) {
					$quiz_renderable_published_count++;
				}
			}
		}

		$output = "\n\nFeature Usage:\n";
		$output .= "==================\n";

		$output .= "\nGlossary Feature:\n";
		$output .= "------------------\n";
		$output .= "Glossary Terms Total: " . absint( $glossary_stats->total_count ) . "\n";
		$output .= "Glossary Terms Published: " . absint( $glossary_stats->published_count ) . "\n";
		$output .= "Glossary Terms Draft: " . absint( $glossary_stats->draft_count ) . "\n";
		$output .= "Glossary Terms With Sort Key: " . absint( $glossary_stats->sort_key_count ) . "\n";

		$output .= "\nQuizzes Feature:\n";
		$output .= "------------------\n";
		$output .= "Quizzes Total: " . count( $quizzes ) . "\n";
		$output .= "Quizzes Published: " . absint( isset( $quiz_count_obj->publish ) ? $quiz_count_obj->publish : 0 ) . "\n";
		$output .= "Quizzes Draft: " . absint( isset( $quiz_count_obj->draft ) ? $quiz_count_obj->draft : 0 ) . "\n";
		$output .= "Quizzes Trashed: " . absint( isset( $quiz_count_obj->trash ) ? $quiz_count_obj->trash : 0 ) . "\n";
		$output .= "Distinct Source Articles Used: " . count( $quiz_source_article_ids ) . "\n";
		$output .= "Published Quizzes Renderable on Frontend: " . $quiz_renderable_published_count . "\n";
		$output .= "Quiz Questions Total: " . $quiz_total_questions . "\n";
		$output .= "AI-Generated Quizzes: " . $quiz_ai_generated_count . "\n";

		return $output;
	}

	/**
	 * Get AI-related debug data if AI Chat or AI Search is enabled.
	 *
	 * @return string
	 */
	private static function get_ai_debug_data() {

		$ai_chat_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_chat_enabled' ) === 'on';
		$ai_search_enabled = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_enabled' ) === 'on';
		if ( ! $ai_chat_enabled && ! $ai_search_enabled ) {
			return '';
		}

		$output = '';

		// retrieve AI Configuration
		$output .= "\n\nAI Configuration:\n";
		$output .= "==================\n";

		// Get AI configuration
		$ai_config = EPKB_AI_Config_Specs::get_ai_config();
		$ai_specs = EPKB_AI_Config_Specs::get_config_fields_specifications();

		if ( ! empty( $ai_config ) ) {
			// Loop through all AI configuration fields
			foreach ( $ai_specs as $field_name => $field_spec ) {
				$value = isset( $ai_config[$field_name] ) ? $ai_config[$field_name] : $field_spec['default'];

				// Format the value based on field type and name
				if ( $field_name === 'ai_chatgpt_key' || $field_name === 'ai_gemini_key' ) {
					// Mask API key for security
					$formatted_value = ! empty( $value ) && $value !== '' ? 'Yes (configured)' : 'No (not configured)';
				} elseif ( $field_name === 'ai_organization_id' ) {
					// Partially mask organization ID
					$formatted_value = ! empty( $value ) ? substr( $value, 0, 10 ) . '...' : 'Not set';
				} elseif ( strpos( $field_name, '_instructions' ) !== false ) {
					// For instruction fields, show full content and length
					if ( ! empty( $value ) ) {
						$formatted_value = "\n" . str_repeat( ' ', 4 ) . $value . "\n" . str_repeat( ' ', 4 ) . '(' . strlen( $value ) . ' chars)';
					} else {
						$formatted_value = 'Not set (0 chars)';
					}
				} elseif ( $field_spec['type'] === EPKB_Input_Filter::CHECKBOX ) {
					// Format checkbox values
					$formatted_value = ( ! empty( $value ) && $value === 'on' ) ? 'Yes' : 'No';
				} elseif ( $field_spec['type'] === EPKB_Input_Filter::SELECTION && ! empty( $field_spec['options'] ) ) {
					// Show selection value with label if available
					$formatted_value = ! empty( $value ) ? $value : 'Not set';
					if ( isset( $field_spec['options'][$value] ) ) {
						$option_value = $field_spec['options'][$value];
						// Handle array option values (convert to string)
						$formatted_value = is_array( $option_value ) ? wp_json_encode( $option_value ) : $option_value;
					}
				} else {
					// Default formatting
					$formatted_value = ! empty( $value ) ? $value : ( isset( $field_spec['default'] ) ? $field_spec['default'] : 'Not set' );
				}

				// Ensure formatted_value is a string before concatenation
				if ( is_array( $formatted_value ) ) {
					$formatted_value = wp_json_encode( $formatted_value );
				} elseif ( ! is_string( $formatted_value ) && ! is_numeric( $formatted_value ) ) {
					$formatted_value = strval( $formatted_value );
				}

				$output .= '- ' . $field_name . ' = ' . $formatted_value . "\n";
			}
		} else {
			$output .= "No AI configuration found.\n";
		}

		// Vector Store Debug Info
		$output .= "\n\nVector Store Debug:\n";
		$output .= "==================\n";
		$output .= "Active Provider: " . EPKB_AI_Provider::get_provider_label() . " (" . EPKB_AI_Provider::get_active_provider() . ")\n";

		// Gather data from both sources
		$options_data = array();
		$db_data = array();
		$training_data_db = new EPKB_AI_Training_Data_DB( true );

		// Get collections from options table
		$options_collections = EPKB_AI_Training_Data_Config_Specs::get_training_data_collections( false, false );
		if ( ! is_wp_error( $options_collections ) && ! empty( $options_collections ) ) {
			foreach ( $options_collections as $collection_id => $config ) {
				$options_data[ $collection_id ] = array(
					'name' => ! empty( $config['ai_training_data_store_name'] ) ? $config['ai_training_data_store_name'] : EPKB_AI_Training_Data_Config_Specs::get_default_collection_name( $collection_id ),
					'provider' => isset( $config['ai_training_data_provider'] ) ? $config['ai_training_data_provider'] : 'unknown',
					'vector_store_id' => isset( $config['ai_training_data_store_id'] ) ? $config['ai_training_data_store_id'] : '',
				);
			}
		}

		// Get collections from training data DB table
		$db_collections = $training_data_db->get_all_collection_ids_from_db();
		if ( ! is_wp_error( $db_collections ) && ! empty( $db_collections ) ) {
			foreach ( $db_collections as $collection_id => $provider ) {
				$stats = $training_data_db->get_status_statistics( $collection_id );
				$db_data[ $collection_id ] = array(
					'provider' => $provider,
					'vector_store_id' => $training_data_db->get_store_id_by_collection( $collection_id, $provider ) ?: '',
					'record_count' => $stats['total'] ?? 0,
					'synced_count' => $stats['synced'] ?? 0,
					'pending_count' => $stats['pending'] ?? 0,
					'error_count' => $stats['error'] ?? 0,
				);
			}
		}

		// Merge and output collections
		$all_collection_ids = array_unique( array_merge( array_keys( $options_data ), array_keys( $db_data ) ) );
		sort( $all_collection_ids );

		if ( empty( $all_collection_ids ) ) {
			$output .= "\nNo collections found.\n";
		} else {
			$output .= "\nCollections:\n";
			foreach ( $all_collection_ids as $collection_id ) {
				$in_options = isset( $options_data[ $collection_id ] );
				$in_db = isset( $db_data[ $collection_id ] );
				$options_store_id = $in_options ? $options_data[ $collection_id ]['vector_store_id'] : '';
				$db_store_id = $in_db ? $db_data[ $collection_id ]['vector_store_id'] : '';

				// Determine source label
				if ( $in_options && $in_db && $options_store_id === $db_store_id ) {
					$source = 'Options & DB';
				} else if ( $in_options && $in_db ) {
					$source = 'MISMATCH';
				} else if ( $in_options ) {
					$source = 'Options only';
				} else {
					$source = 'DB only';
				}

				$provider = $in_options ? $options_data[ $collection_id ]['provider'] : $db_data[ $collection_id ]['provider'];
				$name = $in_options ? $options_data[ $collection_id ]['name'] : EPKB_AI_Training_Data_Config_Specs::get_default_collection_name( $collection_id );
				$store_id = $in_options ? $options_store_id : $db_store_id;

				$output .= "\n- Collection ID: " . $collection_id . " [" . $source . "]\n";
				$output .= "  Name: " . $name . "\n";
				$output .= "  Provider: " . $provider . "\n";
				$output .= "  Store ID: " . ( ! empty( $store_id ) ? $store_id : 'Not created' ) . "\n";

				// Show mismatch details
				if ( $source === 'MISMATCH' ) {
					$output .= "  Options Store ID: " . ( $options_store_id ?: 'Not set' ) . "\n";
					$output .= "  DB Store ID: " . ( $db_store_id ?: 'Not set' ) . "\n";
				}

				// Show DB stats if available
				if ( $in_db ) {
					$output .= "  Records - Total: " . $db_data[ $collection_id ]['record_count'];
					$output .= ", Synced: " . $db_data[ $collection_id ]['synced_count'];
					$output .= ", Pending: " . $db_data[ $collection_id ]['pending_count'];
					$output .= ", Error: " . $db_data[ $collection_id ]['error_count'] . "\n";
					$collection_last_sync = $training_data_db->get_last_sync_date( $collection_id );
					$output .= "  Last Sync: " . ( ! empty( $collection_last_sync ) ? $collection_last_sync : 'Never' ) . "\n";
				}
			}
		}

		// KB to Collection Mapping
		$output .= "\nKB-Collection Mapping:\n";
		$kb_config_db = new EPKB_KB_Config_DB();
		$all_kb_configs = $kb_config_db->get_kb_configs();
		foreach ( $all_kb_configs as $kb_id => $kb_config ) {
			$kb_collection_id = isset( $kb_config['kb_ai_collection_id'] ) ? absint( $kb_config['kb_ai_collection_id'] ) : 0;
			$kb_name = isset( $kb_config['kb_name'] ) ? $kb_config['kb_name'] : 'KB ' . $kb_id;
			$output .= "- KB " . $kb_id . " (" . $kb_name . "): Collection " . ( $kb_collection_id > 0 ? $kb_collection_id : 'Not set' ) . "\n";
		}

		// AI Activity Logs
		$output .= "\n\nAI Activity Logs (Last 50):\n";
		$output .= "==================\n";
		$ai_logs = EPKB_AI_Log::get_logs_for_display();
		if ( empty( $ai_logs ) ) {
			$output .= "No AI logs found.\n";
		} else {
			// Show last 50 logs instead of 100 for better readability
			$recent_logs = array_slice( $ai_logs, -50 );
			foreach ( $recent_logs as $log ) {
				$output .= "\n[" . ( isset( $log['timestamp'] ) ? $log['timestamp'] : 'N/A' ) . "] ";
				$output .= "[" . ( isset( $log['level'] ) ? strtoupper( $log['level'] ) : 'INFO' ) . "] ";
				$output .= isset( $log['message'] ) ? $log['message'] : 'No message';
				if ( ! empty( $log['context'] ) && is_array( $log['context'] ) ) {
					$output .= " | Context: " . wp_json_encode( $log['context'] );
				}
				$output .= "\n";
			}
		}

		// Error notification count
		$current_date = gmdate( 'Y-m-d' );
		$error_count = get_transient( 'epkb_ai_error_notification_count_' . $current_date );
		$output .= "Error Notifications Today: " . ( $error_count !== false ? $error_count : '0' ) . "\n";

		return $output;
	}

	/**
	 * Display Advanced Search Debug page.
	 *
	 * @param $kb_config
	 * @return false|string
	 */
	private static function display_asea_debug_info( $kb_config ) {

		$is_debug_on = get_transient( EPKB_Debug_Controller::EPKB_ADVANCED_SEARCH_DEBUG );
		$button_text = $is_debug_on ? esc_html__('Disable Advanced Search Debug', 'echo-knowledge-base') : esc_html__( 'Enable Advanced Search Debug', 'echo-knowledge-base' );

		ob_start();     ?>

		<div id="epkb_asea_debug_info_tab_page">

			<section class="save-settings">    <?php
				EPKB_HTML_Elements::submit_button_v2( $button_text, 'epkb_enable_advanced_search_debug', 'epkb_enable_advanced_search_debug','' ,true ,'' ,'epkb-primary-btn' );  ?>
			</section>  <?php

			if ( ! empty( $is_debug_on ) ) {   ?>
				<section>
					<h3><?php esc_html_e( 'Advanced Search Debug Information:', 'echo-knowledge-base' ); ?></h3>
				</section>  <?php

				echo wp_kses_post( self::display_asea_debug_data() ); ?>

				<form action="<?php echo esc_url( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . '1&page=epkb-add-ons' ) ); ?>" method="post" dir="ltr">
					<section style="padding-top: 20px;" class="save-settings checkbox-input"><?php
						EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Download Advanced Search Information', 'echo-knowledge-base' ), 'epkb_download_debug_info', 'epkb_download_debug_info', '', true, '', 'epkb-primary-btn' ); ?>
					</section>
					<input type="hidden" name="epkb_debug_box" value="asea">
					<input type="hidden" name="kb_id" value="<?php echo esc_attr( $kb_config['id'] ); ?>">
				</form> <?php
			}   ?>

			<div id='epkb-ajax-in-progress-debug-switch' style="display:none;">
				<?php esc_html_e( 'Switching debug', 'echo-knowledge-base' ) . '... '; ?><img class="epkb-ajax waiting" style="height: 30px;" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif' ); ?>">
			</div>
		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Display Advanced Search data for Debug subpanel
	 * @return string
	 */
	public static function display_asea_debug_data() {

		// ensure user has correct permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			return EPKB_HTML_Forms::notification_box_middle( array(
				'type' => 'error',
				'desc' => esc_html__( 'No access', 'echo-knowledge-base' ),
			), true );
		}

		$asea_search_audit = EPKB_Utilities::get_wp_option( 'asea_search_audit', '' );

		if ( empty( $asea_search_audit ) ) {
			return EPKB_HTML_Forms::notification_box_middle( array(
				'type' => 'info',
				'desc' => esc_html__( 'No data recorded yet.', 'echo-knowledge-base' ),
			), true );
		}

		$output = '<textarea rows="30" cols="150" style="overflow:scroll;">';

		// Display Advanced Search Audit
		$output .= $asea_search_audit;

		$output .= '</textarea>';

		return $output;
	}

	/**
	 * Based on EDD system-info.php file
	 * @return string
	 */
	private static function get_system_info() {

		/** @var $theme_data WP_Theme */
		$theme_data = wp_get_theme();
		/** @noinspection PhpUndefinedFieldInspection */
		$theme = $theme_data->Name . ' ' . $theme_data->Version;

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$first_KB_URL = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );

		$pages = self::epkb_get_kb_main_pages_templates();

		$kb_main_pages_templates_info = '';
		$counter = 0;
		foreach ( $pages as $info ) {
			$counter++;
			if ( $counter > 5 ) {
				break;
			}
			$kb_main_pages_templates_info .= sprintf(
				"\t\t\tKB # %d – “%s” – %s\n",
				//$info['kb_name'],
				$info['kb_id'],
				$info['page_title'],
				//$info['page_id'],
				$info['template_name']
			);
		}

		ob_start();     ?>

		PHP and WordPress Information:
		==============================

		Multisite:                <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

		SITE_URL:                 <?php echo esc_url( site_url() ) . "\n"; ?>
		HOME_URL:                 <?php echo esc_url( home_url() ) . "\n"; ?>
		KB URL:                   <?php echo esc_url( $first_KB_URL ) . "\n"; ?>

		Is New User:              <?php echo EPKB_Utilities::is_new_user( $kb_config, Echo_Knowledge_Base::$version ) . "\n"; ?>
		Active Theme:             <?php echo esc_html( $theme ) . "\n"; ?>
		Block Theme:              <?php echo EPKB_Block_Utilities::is_block_theme() ? 'Yes' . "\n" : 'No' . "\n"; ?>
		KB Block Template Available: <?php echo EPKB_Block_Utilities::is_kb_block_page_template_available() ? 'Yes' . "\n" : 'No' . "\n"; ?>
		Theme Supports Blocks:    <?php echo EPKB_Block_Utilities::current_theme_has_block_support() ? 'Yes' . "\n" : 'No' . "\n"; ?>
		Blocks Available:         <?php echo EPKB_Block_Utilities::is_blocks_available() ? 'Yes' . "\n" : 'No' . "\n"; ?>
		WordPress Version:        <?php echo esc_html( get_bloginfo( 'version' ) ) . "\n\n"; ?>
		KB Main Page Names and Templates:  <?php echo "\n" . $kb_main_pages_templates_info;

		$plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );

		$kb_plugins = array(
			'Knowledge Base for Documents and FAQs',
			'AI Features',
			'KB - Article Rating and Feedback',
			'KB - Links Editor','Articles Import and Export',
			'KB - Multiple Knowledge Bases','KB - Widgets',
			'KB - Elegant Layouts',
			'KB - Advanced Search',
			'Knowledge Base with Access Manager',
			'KB - Custom Roles',
			'KB Groups',
			'KB - Articles Import and Export',
			'Creative Addons for Elementor'
		);

		echo "\n\n";
		echo "KB PLUGINS:	         \n\n";

		foreach ( $plugins as $plugin_path => $plugin ) {
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) )
				continue;

			if ( in_array($plugin['Name'], $kb_plugins)) {
				echo "		" . esc_html( $plugin['Name'] . ': ' . $plugin['Version'] ) ."\n";
			}
		}

		echo "\n\n";

		$other_plugins = false;
		foreach ( $plugins as $plugin_path => $plugin ) {
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) )
				continue;

			if ( ! in_array($plugin['Name'], $kb_plugins)) {
				echo ( $other_plugins ? '' : "OTHER PLUGINS:	         \n\n" );
				echo "		" . esc_html( $plugin['Name'] . ': ' . $plugin['Version'] ) . "\n";
				$other_plugins = true;
			}
		}

		if ( is_multisite() ) {
			echo 'NETWORK ACTIVE PLUGINS:';
			echo "\n";

			$active_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );

			if ( ! empty( $active_plugins ) ) {
				$active_plugins = array_keys( $active_plugins );
			}

			foreach ( $active_plugins as $plugin_path ) {

				if ( validate_file( $plugin_path ) // 0 means valid
					 || '.php' !== substr( $plugin_path, -4 )
					 || ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_path )
				) {
					continue;
				}

				$plugin = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );

				echo "- " . esc_html( $plugin['Name'] . ': ' . $plugin['Version'] ) . "\n";
			}
		}

		echo "\n";
		echo "\n";

		return ob_get_clean();
	}

	/**
	 * Return a list of every KB Main Page together with the human-readable
	 * template name currently applied to that page.
	 *
	 * @return array[] {
	 *     @type int    $page_id         WordPress post-ID of the KB Main Page.
	 *     @type string $page_title      Page title.
	 *     @type int    $kb_id           ID of the Knowledge Base this page belongs to.
	 *     @type string $kb_name         Name of that Knowledge Base.
	 *     @type string $template_slug   Raw value stored in _wp_page_template ('' = default).
	 *     @type string $template_name   Friendly name resolved for display.
	 * }
	 */
	static private function epkb_get_kb_main_pages_templates() {

		$list        = [];
		$theme       = wp_get_theme();
		$is_block    = EPKB_Block_Utilities::is_block_theme();
		$classic_map = $is_block ? [] : $theme->get_page_templates( null, 'page' ); // [ 'Nice Name' => 'file.php' ]

		/* ----------------------------------------------------------
		 * 1. Loop through every KB configuration
		 * -------------------------------------------------------- */
		$kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs( true );
		foreach ( $kb_configs as $kb_config ) {

			$kb_id         = (int) $kb_config['id'];
			$kb_name       = $kb_config['kb_name'];
			$kb_main_pages = $kb_config['kb_main_pages'] ?? [];

			foreach ( $kb_main_pages as $page_id => $page_title ) {

				$template_slug = get_post_meta( $page_id, '_wp_page_template', true ); // '' means default
				$template_name = __( 'Default template', 'echo-knowledge-base' );

				/* --------------------------------------------------
				 * 2. Resolve a *name* for the slug found above
				 * -------------------------------------------------- */

				// 2a. Block-theme template?  (slug = "my-template" or "my-template.html")
				if ( $is_block && $template_slug ) {

					$slug_no_ext = preg_replace( '/\.html$/', '', $template_slug );
					$tpls        = get_block_templates(
						[ 'post_type' => 'page', 'slug__in' => [ $slug_no_ext ] ]
					);

					if ( ! empty( $tpls ) ) {
						$template_name = $tpls[0]->title; // first (and only) match
					} else {
						$template_name = $template_slug;  // fallback  –  unknown slug
					}

					// 2b. Classic-theme PHP template?
				} elseif ( ! $is_block && $template_slug ) {

					$pretty = array_search( $template_slug, $classic_map, true );
					$template_name = $pretty !== false ? $pretty : $template_slug;
				}

				$list[] = [
					'page_id'       => (int) $page_id,
					'page_title'    => $page_title,
					'kb_id'         => $kb_id,
					'kb_name'       => $kb_name,
					'template_slug' => $template_slug ?: 'default',
					'template_name' => $template_name,
				];
			}
		}

		return $list;
	}

	/**
	 * Display Category Slug parameter field
	 * @param $kb_config
	 * @return string
	 */
	private static function display_category_slug_html( $kb_config ) {

		ob_start(); ?>

		<form id="epkb-category-slug-parameter__form" class="epkb-category-slug-parameter__form epkb-admin__kb__form" method="POST">
			<p class="epkb-category-slug-parameter__form-title">    <?php
				esc_html_e( 'Do not change unless you need to define a different category slug for your category archive pages.', 'echo-knowledge-base' ); ?>
			</p>    <?php
			EPKB_HTML_Elements::text( array(
				'title'     => '',
				'label'     => esc_html__( 'Category Slug', 'echo-knowledge-base' ),
				'value' => $kb_config['category_slug'],
				'name'    => 'category_slug_param',
				'specs' => 'category_slug_param',
				'required' => false,
				'input_size' => 'large',
			) );
			EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Save', 'echo-knowledge-base' ), 'eckb_update_category_slug_parameter', '', '', false, '', 'epkb-error-btn' );  ?>
		</form><?php

		return ob_get_clean();
	}

	/**
	 * Display Tag Slug parameter field
	 * @param $kb_config
	 * @return string
	 */
	private static function display_tag_slug_html( $kb_config ) {

		ob_start(); ?>

		<form id="epkb-tag-slug-parameter__form" class="epkb-tag-slug-parameter__form epkb-admin__kb__form" method="POST">
		<p class="epkb-tag-slug-parameter__form-title">    <?php
			esc_html_e( 'Do not change unless you need to define a different tag slug for your tag archive pages.', 'echo-knowledge-base' ); ?>
		</p>    <?php
		EPKB_HTML_Elements::text( array(
			'title'     => '',
			'label'     => esc_html__( 'Tag Slug', 'echo-knowledge-base' ),
			'value' => $kb_config['tag_slug'],
			'name'    => 'tag_slug_param',
			'specs' => 'tag_slug_param',
			'required' => false,
			'input_size' => 'medium',
		) );
		EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Save', 'echo-knowledge-base' ), 'eckb_update_tag_slug_parameter', '', '', false, '', 'epkb-error-btn' );  ?>
		</form><?php

		return ob_get_clean();
	}

	/**
	 * Display search query parameter field
	 * @param $kb_config
	 * @return string
	 */
	private static function display_search_query_html( $kb_config ) {

		ob_start(); ?>

		<form id="epkb-search-query-parameter__form" class="epkb-search-query-parameter__form" method="POST">
			<p class="epkb-search-query-parameter__form-title">    <?php
				esc_html_e( 'Do not change unless you need to define a different query parameter for the search results page.', 'echo-knowledge-base' ); ?>
			</p>    <?php
			EPKB_HTML_Elements::text( array(
				'title'     => '',
				'value' => $kb_config['search_query_param'],
				'name'    => 'search_query_param',
				'specs' => 'search_query_param',
				'required' => true,
			) );
			EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Save', 'echo-knowledge-base' ), 'eckb_update_query_parameter', '', '', false, '', 'epkb-error-btn' );  ?>
		</form><?php

		return ob_get_clean();
	}

	/**
	 * Display KB Nickname field
	 * @param $kb_config
	 * @return string
	 */
	private static function get_kb_nickname_html( $kb_config ) {

		ob_start(); ?>

		<form id="epkb-kb-nickname__form" class="epkb-kb-nickname__form epkb-admin__kb__form" method="POST">
			<p class="epkb-kb-nickname__form-title">    <?php
				esc_html_e( 'Give your Knowledge Base a name. The name will show when we refer to it or when you see a list of post types.', 'echo-knowledge-base' ); ?>
			</p>    <?php
			EPKB_HTML_Elements::text( array(
				'title'     => '',
				'label'     => esc_html__( 'Knowledge Base Name', 'echo-knowledge-base' ),
				'value' => $kb_config['kb_name'],
				'name'    => 'kb_name',
				'specs' => 'kb_name',
				'required' => true,
				'input_size' => 'large',
			) );
			EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Save', 'echo-knowledge-base' ), 'epkb_save_kb_name', '', '', false, '', 'epkb-primary-btn' );  ?>
		</form><?php

		return ob_get_clean();
	}

	/**
	 * Display Sidebar Introduction Text editor
	 * @param $kb_config
	 * @return string
	 */
	private static function get_sidebar_intro_text_html( $kb_config ) {

		// Get full configuration including add-on settings (for Elegant Layouts)
		$add_ons_kb_config = apply_filters( 'elay_block_config', $kb_config, $kb_config['id'] );
		if ( is_wp_error( $add_ons_kb_config ) || empty( $add_ons_kb_config ) || ! is_array( $add_ons_kb_config ) || count( $add_ons_kb_config ) < 100 ) {
			$add_ons_kb_config = $kb_config;
		}

		$sidebar_intro_text = isset( $add_ons_kb_config['sidebar_main_page_intro_text'] ) ? $add_ons_kb_config['sidebar_main_page_intro_text'] : '<missing>';
		
		// Apply the same filter used in the frontend to get the actual value
		$sidebar_intro_text = apply_filters( 'eckb_main_page_sidebar_intro_text', $sidebar_intro_text, $kb_config['id'] );
		
		ob_start(); 
		
		// Ensure editor scripts are enqueued
		wp_enqueue_editor(); ?>

		<form id="epkb-sidebar-intro-text__form" class="epkb-sidebar-intro-text__form epkb-admin__kb__form" method="POST">
			<p class="epkb-sidebar-intro-text__form-title">    <?php
				esc_html_e( 'Enter the introduction text for the Sidebar Layout main page. This text appears at the top of the sidebar navigation.', 'echo-knowledge-base' ); ?>
			</p>
			<br>
			<!-- Hidden field to debug value -->
			<input type="hidden" id="epkb_sidebar_intro_text_debug" value="<?php echo esc_attr( $sidebar_intro_text ); ?>" />
			<div class="epkb-input-group epkb-admin__wp-editor-field">
				<label for="epkb_sidebar_main_page_intro_text"><?php echo esc_html__( 'Introduction Text', 'echo-knowledge-base' ); ?></label>
				<div class="input_container ekb-wp-editor"><?php
					// Use wp_editor with full editor capabilities
					wp_editor( $sidebar_intro_text, 'epkb_sidebar_main_page_intro_text', array( 
						'textarea_name' => 'sidebar_main_page_intro_text',
						'media_buttons' => false,  // Keep media buttons off for intro text
						'teeny' => false,          // Use full editor not teeny
						'textarea_rows' => 12,
						'quicktags' => true,
						'wpautop' => true,
						'tinymce' => true          // Let WordPress handle the full TinyMCE configuration
					) ); ?>
				</div>
			</div>    <?php
			EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Save', 'echo-knowledge-base' ), 'epkb_save_sidebar_intro_text', '', '', false, '', 'epkb-primary-btn' );  ?>
		</form><?php

		return ob_get_clean();
	}

	/**
	 * Get Polylang/WPML debug information for troubleshooting multilingual issues
	 *
	 * @return string
	 */
	private static function get_polylang_debug_info( $kb_id = 1 ) {
		
		// Get the KB configuration for the selected KB
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $kb_config ) ) {
			return '';
		}

		// Only output debug info if WPML is enabled for this KB
		if ( empty( $kb_config['wpml_is_enabled'] ) || $kb_config['wpml_is_enabled'] != 'on' ) {
			return '';
		}

		$output = "Polylang/WPML Debug Information:\n";
		$output .= "================================\n\n";

		// Check if Polylang is active
		if ( function_exists( 'pll_languages_list' ) ) {
			$output .= "Polylang Status: Active\n";
			$output .= "Polylang Version: " . ( defined( 'POLYLANG_VERSION' ) ? POLYLANG_VERSION : 'Unknown' ) . "\n\n";

			// Get all languages
			$languages = pll_languages_list( array( 'fields' => '' ) );
			$output .= "Configured Languages:\n";
			foreach ( $languages as $lang ) {
				$output .= "  - " . $lang->name . " (" . $lang->slug . ")";
				$output .= " [Locale: " . $lang->locale . "]";
				$output .= " [Home URL: " . $lang->home_url . "]";
				$output .= $lang->is_default ? " [DEFAULT]" : "";
				$output .= "\n";
			}
			$output .= "\n";

			// Get current language
			if ( function_exists( 'pll_current_language' ) ) {
				$current_lang = pll_current_language();
				$output .= "Current Language: " . ( $current_lang ? $current_lang : 'Not set' ) . "\n";
			}

			// Check URL modifications
			if ( function_exists( 'PLL' ) && isset( PLL()->options ) ) {
				$options = PLL()->options;
				$output .= "\nPolylang Settings:\n";
				$output .= "  - Hide default language in URL: " . ( ! empty( $options['hide_default'] ) ? 'Yes' : 'No' ) . "\n";
				$output .= "  - Force language in links: " . ( ! empty( $options['force_lang'] ) ? $options['force_lang'] : 'Not set' ) . "\n";
				$output .= "  - Rewrite rules: " . ( ! empty( $options['rewrite'] ) ? $options['rewrite'] : 'Not set' ) . "\n";
			}
		} elseif ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			$output .= "WPML Status: Active\n";
			$output .= "WPML Version: " . ICL_SITEPRESS_VERSION . "\n\n";
		} else {
			$output .= "Polylang/WPML Status: Not Active\n\n";
		}

		// Display KB-specific multilingual settings
		$output .= "\nKB " . $kb_id . " Multilingual Settings:\n";
		$output .= "  - wpml_is_enabled: Yes\n";
		$output .= "  - kb_articles_common_path: " . $kb_config['kb_articles_common_path'] . "\n";
		$output .= "  - category_slug: " . $kb_config['category_slug'] . "\n";
		$output .= "  - categories_in_url_enabled: " . $kb_config['categories_in_url_enabled'] . "\n\n";

		// Check taxonomies registration
		$category_taxonomy = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		$tag_taxonomy = EPKB_KB_Handler::get_tag_taxonomy_name( $kb_id );

		$output .= "Taxonomy Registration:\n";
		$output .= "  - Category taxonomy: " . $category_taxonomy . " - " . ( taxonomy_exists( $category_taxonomy ) ? "Registered" : "NOT REGISTERED" ) . "\n";
		$output .= "  - Tag taxonomy: " . $tag_taxonomy . " - " . ( taxonomy_exists( $tag_taxonomy ) ? "Registered" : "NOT REGISTERED" ) . "\n\n";

		// Check if taxonomies are translatable in Polylang
		if ( function_exists( 'pll_is_translated_taxonomy' ) ) {
			$output .= "Polylang Taxonomy Translation:\n";
			$output .= "  - Category taxonomy translatable: " . ( pll_is_translated_taxonomy( $category_taxonomy ) ? "Yes" : "No" ) . "\n";
			$output .= "  - Tag taxonomy translatable: " . ( pll_is_translated_taxonomy( $tag_taxonomy ) ? "Yes" : "No" ) . "\n\n";
		}

		// Get some example category URLs for each language
		if ( function_exists( 'pll_languages_list' ) && taxonomy_exists( $category_taxonomy ) ) {
			$categories = get_terms( array(
				'taxonomy' => $category_taxonomy,
				'hide_empty' => false,
				'number' => 3
			) );

			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				$output .= "Sample Category URLs by Language:\n";
				$first_category = $categories[0];
				$languages = pll_languages_list( array( 'fields' => '' ) );

				foreach ( $languages as $lang ) {
					// Get translated category
					$translated_cat_id = function_exists( 'pll_get_term' ) ? pll_get_term( $first_category->term_id, $lang->slug ) : $first_category->term_id;
					if ( $translated_cat_id ) {
						$cat_url = get_term_link( $translated_cat_id, $category_taxonomy );
						$output .= "  - " . $lang->name . ": " . ( is_wp_error( $cat_url ) ? "Error: " . $cat_url->get_error_message() : $cat_url ) . "\n";
					}
				}
				$output .= "\n";
			}
		}

		// Display current rewrite rules for KB categories
		global $wp_rewrite;
		$output .= "Rewrite Rules for KB " . $kb_id . " Categories:\n";
		$category_base = $kb_config['kb_articles_common_path'] . '/' . $kb_config['category_slug'];
		$found_rules = false;

		if ( ! empty( $wp_rewrite->rules ) ) {
			foreach ( $wp_rewrite->rules as $pattern => $rule ) {
				if ( strpos( $pattern, $category_base ) !== false || strpos( $rule, $category_taxonomy ) !== false ) {
					$output .= "  Pattern: " . $pattern . "\n";
					$output .= "  Rule: " . $rule . "\n\n";
					$found_rules = true;
				}
			}
		}

		if ( ! $found_rules ) {
			$output .= "  No rewrite rules found for category base: " . $category_base . "\n\n";
		}

		// Check if rewrite rules need flushing
		$output .= "\nRewrite Rules Status:\n";
		$output .= "  - Total rules count: " . ( ! empty( $wp_rewrite->rules ) ? count( $wp_rewrite->rules ) : 0 ) . "\n";
		$output .= "  - Permalink structure: " . get_option( 'permalink_structure' ) . "\n\n";

		// Debug current request if on a category archive
		if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
			$output .= "Current Request Debug:\n";
			$output .= "  - REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
			if ( ! empty( $GLOBALS['wp']->query_vars ) ) {
				$output .= "  - Query vars: " . print_r( $GLOBALS['wp']->query_vars, true ) . "\n";
			}
		}

		// Troubleshooting recommendations
		$output .= "\nTroubleshooting Recommendations for 404 Issues:\n";
		$output .= "================================================\n";
		$output .= "1. Flush Rewrite Rules:\n";
		$output .= "   - Go to Settings > Permalinks and click 'Save Changes' (without making changes)\n";
		$output .= "   - Or deactivate and reactivate the Echo Knowledge Base plugin\n\n";
		$output .= "2. Check Polylang Settings:\n";
		$output .= "   - Ensure KB categories are set as translatable in Languages > Settings > Custom post types and Taxonomies\n";
		$output .= "   - Check 'Hide URL language information for default language' setting\n\n";
		$output .= "3. Verify Category Translations:\n";
		$output .= "   - Make sure categories have translations for all languages\n";
		$output .= "   - Check that category slugs are unique for each language\n\n";
		$output .= "4. Common Issues with English (Default Language):\n";
		$output .= "   - If 'Hide default language in URL' is enabled, English URLs won't have /en/ prefix\n";
		$output .= "   - This can cause conflicts with rewrite rules expecting language prefixes\n";
		$output .= "   - Try disabling 'Hide default language in URL' temporarily to test\n\n";

		return $output;
	}
}
