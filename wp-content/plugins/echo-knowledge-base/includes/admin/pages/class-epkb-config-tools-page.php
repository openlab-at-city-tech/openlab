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

		// SECONDARY VIEW: MENU ACCESS CONTROL
		$secondary_tabs[] = array(

			// Shared
			'list_key'              => 'access-control',

			// Secondary Panel Item
			'label_text'            => esc_html__( 'Menu Access Control', 'echo-knowledge-base' ),

			// Secondary Boxes List
			'list_top_actions_html' => '<div class="epkb-admin__list-actions-row">' . EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Save Access Control Settings', 'echo-knowledge-base' ), 'epkb_save_access_control', 'epkb-admin__save-access-control-btn', '', true, true, 'epkb-success-btn' ) . '</div>',
			'boxes_list'            => EPKB_Admin_UI_Access::get_access_boxes( $kb_config ),
		);

		// SECONDARY VIEW: EXPORT
		$secondary_tabs[] = array(

			// Shared
			'list_key'   => 'export',
			'active'     => true,

			// Secondary Panel Item
			'label_text' => esc_html__( 'Export KB', 'echo-knowledge-base' ),

			// Secondary Boxes List
			'boxes_list' => self::get_export_boxes( $kb_config )
		);

		// SECONDARY VIEW: IMPORT
		$secondary_tabs[] = array(

			// Shared
			'list_key'   => 'import',

			// Secondary Panel Item
			'label_text' => esc_html__( 'Import KB', 'echo-knowledge-base' ),

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
						   value="<?php esc_attr_e( 'Back', 'echo-knowledge-base' ); ?>"/>
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
				'title'        => esc_html__( 'Export as CSV', 'echo-knowledge-base' ),
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
				'title'        => esc_html__( 'Export as XML', 'echo-knowledge-base' ),
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
				'title'        => esc_html__( 'Import as CSV', 'echo-knowledge-base' ),
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
				'title'        => esc_html__( 'Import as XML', 'echo-knowledge-base' ),
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
				$panel_html = self::get_convert_posts_box( $kb_config );
			}

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_convert_articles' ) {
				$panel_html = self::get_convert_articles_box( $kb_config );
			}

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_convert_cpt' ) {
				$panel_html = self::get_convert_cpt_box( $kb_config );
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
				'title'         => esc_html__( 'Convert From Other Documentation KB to Echo KB', 'echo-knowledge-base' ),
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
		];
	}

	/**
	 * Convert Posts to Articles.
	 * @param $kb_config
	 * @return false|string
	 */
	private static function get_convert_posts_box( $kb_config ) {
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
	 * Convert Articles to Posts.
	 * @param $kb_config
	 * @return false|string
	 */
	private static function get_convert_articles_box( $kb_config ) {
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
				<span><?php esc_html_e( $step_4_text ); ?></span>
			</div>
			<div class="epkb-import-step-label epkb-import-step--3" data-step="3">
				<i class="epkbfa epkbfa-check"></i>
				<span><?php esc_html_e( 'Choose Options', 'echo-knowledge-base' ); ?></span>
			</div>
			<div class="epkb-import-step-label epkb-import-step--4 " data-step="4">
				<i class="epkbfa epkbfa-check"></i>
				<span><?php esc_html_e( $step_4_title ); ?></span>
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
			'desc'   => esc_html__( 'This website is using an old version of WordPress. Unpredictable behaviour and errors during conversion can occur for this old WordPress version. Please update to the latest version of WordPress. ' .
							'Support is very limited for old versions of WordPress.', 'echo-knowledge-base' ),
		] );
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
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Features', 'echo-kb-import-export' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Convert Posts', 'echo-kb-import-export' ); ?>
					</div>
				</div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Copy or Move Categories', 'echo-kb-import-export' ); ?>
					</div>
				</div>
			</div>

			<div class="epkb-form-field-instruction-column">
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Not Supported', 'echo-kb-import-export' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-close"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Categories hierarchy', 'echo-kb-import-export' ); ?>
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
			<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I want to convert posts into this KB:', 'echo-kb-import-export' ); ?>
                <span class="epkb-admin__distinct-box epkb-admin__distinct-box--middle"><?php echo esc_html( $kb_config['kb_name'] ); ?></span></span>
			</label><?php
		} ?>

		<label class="epkb-form-label">
			<input class="epkb-form-label__input epkb-form-label__input--checkbox import-backup-checkbox"
				   type="checkbox" name="epkb_convert_backup" required>
			<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I have backed up my database and read all import instructions above.', 'echo-kb-import-export' ); ?></span>
		</label>
		</form><?php
	}

	/**
	 * HTML for convert step
	 */
	private static function show_convert_posts_step_2() {
		self::progress_bar_html( esc_html__( 'Reading posts', 'echo-knowledge-base' ) );
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

	/**
	 * HTML for convert post step
	 *
	 * @param $kb_config
	 */
	private static function show_convert_articles_step_1( $kb_config ) { ?>
		<form class="convert-main-form">
		<div class="epkb-form-field-instruction-wrap">
			<div class="epkb-form-field-instruction-column">
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Features', 'echo-kb-import-export' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Convert Articles', 'echo-kb-import-export' ); ?>
					</div>
				</div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Copy or Move Categories', 'echo-kb-import-export' ); ?>
					</div>
				</div>
			</div>

			<div class="epkb-form-field-instruction-column">
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Not Supported', 'echo-kb-import-export' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-close"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Categories hierarchy', 'echo-kb-import-export' ); ?>
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
			<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I have backed up my database and read all import instructions above.', 'echo-kb-import-export' ); ?></span>
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

	/**
	 * HTML for convert footer
	 * @param $kb_config
	 */
	private static function show_convert_footer_html( $kb_config ) { ?>
		<div class="epkb-import-footer">
			<button type="button" class="epkb-default-btn epkb-convert-button-back">
				<?php esc_html_e( '< Back', 'echo-knowledge-base' ); ?>
			</button>
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
					<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Features', 'echo-kb-import-export' ); ?></div>
					<div class="epkb-form-field-instruction-item">
						<div class="epkb-form-field-instruction-icon">
							<i class="epkbfa epkbfa-check"></i>
						</div>
						<div class="epkb-form-field-instruction-text">
							<?php esc_html_e( 'Convert CPT', 'echo-kb-import-export' ); ?>
						</div>
					</div>
					<div class="epkb-form-field-instruction-item">
						<div class="epkb-form-field-instruction-icon">
							<i class="epkbfa epkbfa-check"></i>
						</div>
						<div class="epkb-form-field-instruction-text">
							<?php esc_html_e( 'Copy or Move Categories', 'echo-kb-import-export' ); ?>
						</div>
					</div>
				</div>

				<div class="epkb-form-field-instruction-column">
					<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Not Supported', 'echo-kb-import-export' ); ?></div>
					<div class="epkb-form-field-instruction-item">
						<div class="epkb-form-field-instruction-icon">
							<i class="epkbfa epkbfa-close"></i>
						</div>
						<div class="epkb-form-field-instruction-text">
							<?php esc_html_e( 'Categories hierarchy', 'echo-kb-import-export' ); ?>
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
				<span class="epkb-form-label__select"><?php esc_html_e( 'Convert CPT:', 'echo-kb-import-export' ); ?></span>
				<select name="epkb_convert_post_type">
					<option value="" selected><?php esc_html_e( 'Select Post Type', 'echo-kb-import-export' ); ?></option><?php
					foreach ( $custom_post_types as $post_type => $post_label ) { ?>
						<option value="<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( $post_label ); ?></option><?php
					} ?>
				</select>
			</label><?php

			if ( EPKB_Utilities::is_multiple_kbs_enabled() ) { ?>
				<label class="epkb-form-label">
					<input class="epkb-form-label__input epkb-form-label__input--checkbox import-kb-name-checkbox"
						   type="checkbox" name="epkb_convert_post" required>
					<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I want to convert articles into this KB:', 'echo-kb-import-export' ); ?> <strong
							class="epkb-admin__distinct-box epkb-admin__distinct-box--middle"><?php echo esc_html( $kb_config['kb_name'] ); ?></strong></span>
				</label><?php
			} ?>

			<label class="epkb-form-label">
				<input class="epkb-form-label__input epkb-form-label__input--checkbox import-backup-checkbox"
					   type="checkbox" name="epkb_convert_backup" required>
				<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I have backed up my database and read all import instructions above.', 'echo-kb-import-export' ); ?></span>
			</label>
		</form><?php
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

	/**
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
			EPKB_Utilities::is_advanced_search_enabled() ? array(
				'title'       => esc_html__( 'Advanced Search', 'echo-knowledge-base' ),
				'description' => esc_html__( 'Enable debug when instructed by the support team.', 'echo-knowledge-base' ),
				'html'        => self::display_asea_debug_info( $kb_config ),
			) : '',

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
		$specification = EPKB_Core_Utilities::retrieve_all_kb_specs( $kb_config['id'] );

		// Translations
		if ( ! EPKB_Utilities::is_amag_on() ) {
			$boxes_config[] = array(
				'title' => esc_html__( 'Translations', 'echo-knowledge-base' ),
				'html'  => EPKB_HTML_Elements::checkbox_toggle( [
					'id'            => 'wpml_is_enabled',
					'name'          => 'wpml_is_enabled',
					'text'          => $specification['wpml_is_enabled']['label'],
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
				'topDesc'       => esc_html__( 'KB can preload its fonts for better performance. Some cache plugins preload fonts themselves, which can result in KB fonts being preloaded twice. ' .
										'Disable this option to avoid a conflict with your cache plugin.', 'echo-knowledge-base' ),
				'return_html'   => true,
			] ),
		);

		// AI Help Settings
		$boxes_config[] = array(
			'title' => esc_html__( 'AI Help Settings', 'echo-knowledge-base' ),
			'html'  => EPKB_HTML_Elements::checkbox_toggle( [
				'id'            => 'disable_openai',
				'name'          => 'disable_openai',
				'text'          => esc_html__( 'Disable AI Help', 'echo-knowledge-base' ),
				'textLoc'       => 'left',
				'checked'       => EPKB_Core_Utilities::is_kb_flag_set( 'disable_openai' ),
				'topDesc'       => esc_html__( 'Enable or disable the AI Help feature.', 'echo-knowledge-base' ),
				'return_html'   => true,
			] ),
		);

		// Box: Delete All KBs Data
		$boxes_config[] = array(
			'title' => esc_html__( 'Delete All KBs Data', 'echo-knowledge-base' ),
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

				<form action="<?php echo esc_url( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . '1&page=epkb-add-ons' ) ); ?>" method="post" dir="ltr"> <?php
					EPKB_HTML_Elements::checkbox( [
						'name'  => 'epkb_show_full_debug',
						'label' => esc_html__( 'Output full debug information (after instructed by support staff)', 'echo-knowledge-base' ),
						'input_class' => 'epkb-checkbox-input',
						'input_group_class' => 'epkb-input-group',
					] ); ?>

					<section style="padding-top: 20px;" class="save-settings checkbox-input"><?php
						EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Download System Information', 'echo-knowledge-base' ), 'epkb_download_debug_info', 'epkb_download_debug_info', '', true, '' , 'epkb-primary-btn' ); ?>
					</section>
					<input type="hidden" name="epkb_debug_box" value="main">
					<input type="hidden" name="kb_id" value="<?php echo esc_attr( $kb_config['id'] ); ?>">
				</form>     <?php
			}   ?>

			<div id='epkb-ajax-in-progress-debug-switch' style="display:none;">
				<?php esc_html_e( 'Switching debug... ', 'echo-knowledge-base' ); ?><img class="epkb-ajax waiting" style="height: 30px;"
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
		$output .= esc_html__( 'Simple Error Log:', 'echo-knowledge-base' );
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
				$output .= "Did not find KB configuration (DB231) for KB ID " . $kb_id . "\n";
				continue;
			}

			if ( count( $kb_config ) < 100 ) {
				$output .= "Found KB configuration is incomplete with only " . count( $kb_config ) . " items.\n";
			}

			$output .= 'KB Config ' . $kb_id . "\n\n";
			$specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
			$output .= '- KB URL  = ' . EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config ) . "\n";
			foreach( $kb_config as $name => $value ) {

				//phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( ! isset( $_POST['epkb_show_full_debug'] ) && ! in_array( $name, array('id','kb_main_pages','kb_name','kb_articles_common_path','categories_in_url_enabled',
						'templates_for_kb', 'wpml_is_enabled', 'kb_main_page_layout' ) ) ) {
					continue;
				}

				if ( is_array( $value ) ) {
					$value = EPKB_Utilities::get_variable_string( $value );
					$value = str_replace( "=>", "=", $value );
				}
				$label = empty($specs[$name]['label']) ? 'unknown' : $specs[$name]['label'];
				$output .= '- ' . $label . ' [' . $name . ']' . ' = ' . $value . "\n";
			}

			$output .= "\n\n";
		}

		// retrieve add-on data
		$add_on_output = apply_filters( 'eckb_add_on_debug_data', '' );
		$output .= is_string( $add_on_output ) ? $add_on_output : '';

		$output .= '</textarea>';

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
				<?php esc_html_e( 'Switching debug... ', 'echo-knowledge-base' ); ?><img class="epkb-ajax waiting" style="height: 30px;" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif' ); ?>">
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

		ob_start();     ?>

		PHP and WordPress Information:
		==============================

		Multisite:                <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

		SITE_URL:                 <?php echo esc_url( site_url() ) . "\n"; ?>
		HOME_URL:                 <?php echo esc_url( home_url() ) . "\n"; ?>
		KB URL:                   <?php echo esc_url( $first_KB_URL ) . "\n"; ?>

		Active Theme:             <?php echo esc_html( $theme ) . "\n";

		$plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );

		$kb_plugins = array(
			'KB - Article Rating and Feedback',
			'KB - Links Editor','Articles Import and Export',
			'KB - Multiple Knowledge Bases','KB - Widgets',
			'Knowledge Base for Documents and FAQs',
			'KB - Elegant Layouts',
			'KB - Advanced Search',
			'Knowledge Base with Access Manager',
			'KB - Custom Roles',
			'KB Groups',
			'KB - Articles Import and Export',
			'Blocks for Documents, Articles and FAQs',
			'Creative Addons for Elementor' );

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
		echo "OTHER PLUGINS:	         \n\n";

		foreach ( $plugins as $plugin_path => $plugin ) {
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) )
				continue;

			if ( ! in_array($plugin['Name'], $kb_plugins)) {
				echo "		" . esc_html( $plugin['Name'] . ': ' . $plugin['Version'] ) . "\n";
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
}
