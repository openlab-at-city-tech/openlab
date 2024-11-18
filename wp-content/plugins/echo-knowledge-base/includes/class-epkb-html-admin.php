<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * HTML Elements for admin pages excluding boxes
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_HTML_Admin {

	/********************************************************************************
	 *
	 *                             ADMIN HEADER
	 *
	 ********************************************************************************/

	/**
	 * Show Admin Header
	 *
	 * @param $kb_config
	 * @param $permissions
	 * @param string $content_type
	 * @param string $position
	 */
	public static function admin_header( $kb_config, $permissions, $content_type='header', $position = '' ) {  ?>

		<!-- Admin Header -->
		<div class="epkb-admin__header">
			<div class="epkb-admin__section-wrap <?php echo empty( $position ) ? '' : 'epkb-admin__section-wrap--' . esc_attr( $position ); ?> epkb-admin__section-wrap__header">   <?php

				switch ( $content_type ) {
					case 'header':
					default:
						//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo self::admin_header_content( $kb_config, $permissions ) ;
						break;
					case 'logo':
						//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo self::admin_header_logo();
						break;
				}  ?>

			</div>
		</div>  <?php
	}

	/**
	 * Content for Admin Header - KB Logo, List of KBs
	 *
	 * @param $kb_config
	 * @param array $contexts
	 * @return string
	 */
	public static function admin_header_content( $kb_config, $contexts=[] ) {

		ob_start();

		if ( ! empty( $kb_config ) ) {
			$link_output = EPKB_Core_Utilities::get_current_kb_main_page_link( $kb_config, esc_html__( 'View KB', 'echo-knowledge-base' ), 'epkb-admin__header__view-kb__link' );
			if ( empty( $link_output ) && $kb_config['modular_main_page_toggle'] == 'on' && EPKB_Admin_UI_Access::is_user_access_to_context_allowed('admin_eckb_access_frontend_editor_write')) {
				$link_output = '<a href="' . esc_url( admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) . '&page=epkb-kb-configuration&setup-wizard-on' ) ) .
					'" class="epkb-admin__header__view-kb__link" target="_blank">' . esc_html__( "Setup KB", "echo-knowledge-base" ) . '</a>';
			}
		}

		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo self::admin_header_logo();

		if ( ! empty( $kb_config ) ) {    ?>
			<div class="epkb-admin__header__controls-wrap">

				<!-- KBs List -->
				<p class="epkb-admin__header__label"><?php esc_html__( 'Select KB', 'echo-knowledge-base' ); ?></p>
				<div class="epkb-admin__header__dropdown">      <?php
					EPKB_Core_Utilities::admin_list_of_kbs( $kb_config, $contexts ); 			?>
				</div>

				<!-- Link to KB View -->
				<div class="epkb-admin__header__view-kb">					<?php
					echo wp_kses_post( $link_output ); ?>
					<span class="epkb-admin__header__view-kb__icon epkbfa epkbfa-external-link"></span>
				</div>  <?php    ?>
			</div>      <?php
		}

		$result = ob_get_clean();

		return empty( $result ) ? '' : $result;
	}

	/**
	 * Get logo container for the admin header
	 *
	 * @return string
	 */
	public static function admin_header_logo() {

		ob_start();     ?>

		<!-- Echo Logo -->
		<div class="epkb-admin__header__logo-wrap">
			<img class="epkb-admin__header__logo-mobile" alt="<?php esc_html_e( 'Echo Knowledge Base Logo', 'echo-knowledge-base' ); ?>" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/kb-icon.png' ); ?>">
			<img class="epkb-admin__header__logo-desktop" alt="<?php esc_html_e( 'Echo Knowledge Base Logo', 'echo-knowledge-base' ); ?>" src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/echo-kb-logo' . ( is_rtl() ? '-rtl' : '' ) . '.png' ); ?>">
		</div>  <?php

		$result = ob_get_clean();

		return empty( $result ) ? '' : $result;
	}


	/********************************************************************************
	 *
	 *                             ADMIN TABS
	 *
	 ********************************************************************************/

	/**
	 * Show Admin Toolbar
	 *
	 * @param $admin_page_views
	 */
	public static function admin_primary_tabs( $admin_page_views ) {     ?>

		<!-- Admin Top Panel -->
		<div class="epkb-admin__top-panel">
			<div class="epkb-admin__section-wrap epkb-admin__section-wrap__top-panel">      <?php

				foreach( $admin_page_views as $page_view ) {

					// Optionally we can have null in $page_view, make sure we handle it correctly
					if ( empty( $page_view ) || ! is_array( $page_view ) ) {
						continue;
					}

					// Fill missing fields in admin page view configuration array with default values
					$page_view = self::admin_page_view_fill_missing_with_default( $page_view );

					// Do not render toolbar tab if the user does not have permission
					if ( ! current_user_can( $page_view['minimum_required_capability'] ) ) {
						continue;
					}   ?>

					<div class="epkb-admin__top-panel__item epkb-admin__top-panel__item--<?php echo esc_attr( $page_view['list_key'] );
					echo empty( $page_view['secondary_tabs'] ) ? '' : ' epkb-admin__top-panel__item--parent ';
					echo esc_attr( $page_view['main_class'] ); ?>"
						<?php echo empty( $page_view['list_id'] ) ? '' : ' id="' . esc_attr( $page_view['list_id'] ) . '"'; ?> data-target="<?php echo esc_attr( $page_view['list_key'] ); ?>">
						<div class="epkb-admin__top-panel__icon epkb-admin__top-panel__icon--<?php echo esc_attr( $page_view['list_key'] ); ?> <?php echo esc_attr( $page_view['icon_class'] ); ?>"></div>
						<p class="epkb-admin__top-panel__label epkb-admin__boxes-list__label--<?php echo esc_attr( $page_view['list_key'] ); ?>"><?php echo wp_kses_post( $page_view['label_text'] ); ?></p>
					</div> <?php
				}       ?>

			</div>
		</div>  <?php
	}

	/**
	 * Display admin second-level tabs below toolbar
	 *
	 * @param $admin_page_views
	 */
	public static function admin_secondary_tabs( $admin_page_views ) {  ?>

		<!-- Admin Secondary Panels List -->
		<div class="epkb-admin__secondary-panels-list">
			<div class="epkb-admin__section-wrap epkb-admin__section-wrap__secondary-panel">  <?php

				foreach ( $admin_page_views as $page_view ) {

					// Optionally we can have null in $page_view, make sure we handle it correctly
					if ( empty( $page_view ) || ! is_array( $page_view ) ) {
						continue;
					}

					// Optionally we can have empty in $page_view['secondary_tabs'], make sure we handle it correctly
					if ( empty( $page_view['secondary_tabs'] ) || ! is_array( $page_view['secondary_tabs'] ) ) {
						continue;
					}

					// Fill missing fields in admin page view configuration array with default values
					$page_view = self::admin_page_view_fill_missing_with_default( $page_view );

					// Do not render toolbar tab if the user does not have permission
					if ( ! current_user_can( $page_view['minimum_required_capability'] ) ) {
						continue;
					}   ?>

					<!-- Admin Secondary Panel -->
					<div id="epkb-admin__secondary-panel__<?php echo esc_attr( $page_view['list_key'] ); ?>" class="epkb-admin__secondary-panel">  <?php

						foreach ( $page_view['secondary_tabs'] as $secondary ) {

							// Optionally we can have empty in $secondary, make sure we handle it correctly
							if ( empty( $secondary ) || ! is_array( $secondary ) ) {
								continue;
							}

							// Do not render toolbar tab if the user does not have permission
							if ( ! current_user_can( $secondary['minimum_required_capability'] ) ) {
								continue;
							}   ?>

							<div class="epkb-admin__secondary-panel__item epkb-admin__secondary-panel__<?php echo esc_attr( $secondary['list_key'] ); ?> <?php
							echo ( $secondary['active'] ? 'epkb-admin__secondary-panel__item--active' : '' );
							echo esc_attr( $secondary['main_class'] ); ?>" data-target="<?php echo esc_attr( $page_view['list_key'] ) . '__' .esc_attr( $secondary['list_key'] ); ?>">     <?php

								// Optional icon for secondary panel item
								if ( ! empty( $secondary['icon_class'] ) ) {        ?>
									<span class="epkb-admin__secondary-panel__icon <?php echo esc_attr( $secondary['icon_class'] ); ?>"></span>     <?php
								}       ?>

								<p class="epkb-admin__secondary-panel__label epkb-admin__secondary-panel__<?php echo esc_attr( $secondary['list_key'] ); ?>__label"><?php echo wp_kses_post( $secondary['label_text'] ); ?></p>
							</div>  <?php

						}   ?>
					</div>  <?php

				}   ?>

			</div>
		</div>  <?php
	}

	/**
	 * Show content (such as settings and features) for each primary tab
	 *
	 * @param $admin_page_views
	 */
	public static function admin_primary_tabs_content( $admin_page_views ) {    ?>

		<!-- Admin Content -->
		<div class="epkb-admin__content"> <?php

		echo '<div class="epkb-admin__boxes-list-container">';
		foreach ( $admin_page_views as $page_view ) {

			// Optionally we can have null in $page_view, make sure we handle it correctly
			if ( empty( $page_view ) || ! is_array( $page_view ) ) {
				continue;
			}

			// Fill missing fields in admin page view configuration array with default values
			$page_view = self::admin_page_view_fill_missing_with_default( $page_view );

			// Do not render view if the user does not have permission
			if ( ! current_user_can( $page_view['minimum_required_capability'] ) ) {
				continue;
			}   ?>

			<!-- Admin Boxes List -->
			<div id="epkb-admin__boxes-list__<?php echo esc_attr( $page_view['list_key'] ); ?>" class="epkb-admin__boxes-list">     <?php

			// List body
			self::admin_single_primary_tab_content( $page_view );

			// Optional list footer
			if ( ! empty( $page_view['list_footer_html'] ) ) {   ?>
				<div class="epkb-admin__section-wrap epkb-admin__section-wrap__<?php echo esc_attr( $page_view['list_key'] ); ?>">
					<div class="epkb-admin__boxes-list__footer"><?php echo wp_kses_post( $page_view['list_footer_html'] ); ?></div>
				</div>      <?php
			}   ?>

			</div><?php
		}
		echo '</div>'; ?>
		</div><?php
	}

	/**
	 * Show single List of Settings Boxes for Admin Page
	 *
	 * @param $page_view
	 */
	private static function admin_single_primary_tab_content( $page_view ) {

		// CASE: secondary tabs
		if ( ! empty( $page_view['secondary_tabs'] ) && is_array( $page_view['secondary_tabs'] ) ) {

			// Secondary tabs
			foreach ( $page_view['secondary_tabs'] as $secondary_tab ) {

				// Make sure we can handle empty boxes list correctly
				if ( empty( $secondary_tab['boxes_list'] ) || ! is_array( $secondary_tab['boxes_list'] ) ) {
					continue;
				}

				// Do not render toolbar tab if the user does not have permission
				if ( ! current_user_can( $secondary_tab['minimum_required_capability'] ) ) {
					continue;
				}   ?>

				<!-- Admin Section Wrap -->
				<div class="epkb-setting-box-container epkb-setting-box-container-type-<?php echo esc_attr( $page_view['list_key'] ); ?>">

					<!-- Secondary Boxes List -->
					<div id="epkb-setting-box__list-<?php echo esc_attr( $page_view['list_key'] ) . '__' . esc_attr( $secondary_tab['list_key'] ); ?>"
					     class="epkb-setting-box__list <?php echo ( $secondary_tab['active'] ? 'epkb-setting-box__list--active' : '' ); ?>">   <?php

						self::admin_tab_content_boxes_list( $secondary_tab );   ?>

					</div>

				</div>  <?php
			}
			return;
		}

		// CASE: vertical (secondary) tabs
		if ( ! empty( $page_view['vertical_tabs'] ) && is_array( $page_view['vertical_tabs'] ) ) {      ?>

			<!-- Admin Form -->
			<div class="epkb-admin__form">
				<div class="epkb-admin__form__save_button">
					<button class="epkb-success-btn epkb-admin__kb__form-save__button"><?php esc_html_e( 'Save Settings', 'echo-knowledge-base' ); ?></button>
				</div>
				<div class="epkb-admin__form__body"><?php
					self::display_admin_vertical_tabs( $page_view['vertical_tabs'] );   ?>
				</div>
			</div>  <?php

			return;
		}

		// CASE: Boxes List for view without secondary tabs - make sure we can handle empty boxes list correctly
		if ( ! empty( $page_view['boxes_list'] ) && is_array( $page_view['boxes_list'] ) ) {    ?>

			<!-- Admin Section Wrap -->
			<div class="epkb-admin__section-wrap epkb-admin__section-wrap__<?php echo esc_attr( $page_view['list_key'] ); ?>">  <?php

				self::admin_tab_content_boxes_list( $page_view );   ?>

			</div>      <?php
			return;
		}
	}

	/**
	 * Display boxes list for admin settings
	 *
	 * @param $page_view
	 */
	private static function admin_tab_content_boxes_list( $page_view ) {

		// Optional buttons row displayed at the top of the boxes list
		if ( ! empty( $page_view['list_top_actions_html'] ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $page_view['list_top_actions_html'];
		}

		// Admin Boxes with configuration
		foreach ( $page_view['boxes_list'] as $box_options ) {

			// Do not render empty or not valid array
			if ( empty( $box_options ) || ! is_array( $box_options ) ) {
				continue;
			}

			EPKB_HTML_Forms::admin_settings_box( $box_options );
		}

		// Optional buttons row displayed at the bottom of the boxes list
		if ( ! empty( $page_view['list_bottom_actions_html'] ) ) {
			echo wp_kses_post( $page_view['list_bottom_actions_html'] );
		}
	}

	/**
	 * Display vertical tabs
	 *
	 * @param $vertical_tabs
	 */
	private static function display_admin_vertical_tabs( $vertical_tabs ) { ?>

		<!-- TABS -->
		<div class="epkb-admin__form-tabs">    <?php
			foreach ( $vertical_tabs as $tab ) {

				$data_escaped = '';
				if ( ! empty( $tab['data'] ) ) {
					foreach ( $tab['data'] as $key => $value ) {
						$data_escaped .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
					}
				}   ?>

				<div class="epkb-admin__form-tab<?php echo $tab['active'] ? ' epkb-admin__form-tab--active' : ''; ?>" data-target="<?php echo esc_attr( $tab['key'] ); ?>" <?php echo $data_escaped; ?>>
					<i class="<?php echo esc_attr( $tab['icon'] ); ?> epkb-admin__form-tab-icon"></i>
					<span class="epkb-admin__form-tab-title"><?php echo esc_html( $tab['title'] ); ?></span>
				</div>  <?php

				if ( ! empty( $tab['sub_tabs'] ) ) {    ?>
					<div class="epkb-admin__form-sub-tabs epkb-admin__form-sub-tabs--<?php echo esc_attr( $tab['key'] ); ?>" data-tab-key="<?php echo esc_attr( $tab['key'] ); ?>">    <?php
						foreach ( $tab['sub_tabs'] as $sub_tab ) {
							$sub_data_escaped = '';
							if ( ! empty( $sub_tab['data'] ) ) {
								foreach ( $sub_tab['data'] as $key => $value ) {
									$sub_data_escaped .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
								}
							}   ?>
							<div class="epkb-admin__form-sub-tab<?php echo $sub_tab['active'] ? ' epkb-admin__form-sub-tab--active' : ''; echo ' ' . esc_attr( $sub_tab['class'] ); ?>" data-target="<?php echo esc_attr( $sub_tab['key'] ); ?>" <?php echo $sub_data_escaped; ?>>
								<span class="<?php echo esc_attr( $sub_tab['icon'] ); ?> epkb-admin__form-sub-tab-icon"><?php echo esc_html( $sub_tab['icon_text'] ); ?></span>
								<span class="epkb-admin__form-sub-tab-title"><?php echo esc_html( $sub_tab['title'] ); ?></span>
							</div><?php
						}   ?>
					</div>  <?php
				}
			}   ?>
		</div>

		<!-- TAB CONTENTS -->
		<div class="epkb-admin__form-tab-contents"> <?php
			foreach ( $vertical_tabs as $tab ) {    ?>

				<div class="epkb-admin__form-tab-wrap epkb-admin__form-tab-wrap--<?php echo esc_attr( $tab['key'] ); echo $tab['active'] ? ' epkb-admin__form-tab-wrap--active' : ''; ?>">  <?php

					foreach ( $tab['contents'] as $content ) {

						$data_escaped = '';
						if ( ! empty( $content['data'] ) ) {
							foreach ( $content['data'] as $key => $value ) {
								$data_escaped .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
							}
						}   ?>

						<div class="epkb-admin__form-tab-content <?php echo empty( $content['css_class'] ) ? '' : esc_attr( $content['css_class'] ); ?>" <?php echo $data_escaped; ?>>

							<div class="epkb-admin__form-tab-content-title">    <?php
								echo empty( $content['title_before_icon'] ) ? '' : esc_html( $content['title'] );
								if ( ! empty( $content['icon'] ) || ! empty( $content['icon_text'] ) ) {    ?>
									<span class="<?php echo esc_html( $content['icon'] ); ?> epkb-admin__form-tab-content-icon"><?php echo esc_html( $content['icon_text'] ); ?></span>   <?php
								}
								echo empty( $content['title_before_icon'] ) ? esc_html( $content['title'] ) : '';
						        if ( ! empty( $content['help_links_html'] ) ) {
                                    echo wp_kses( $content['help_links_html'], EPKB_Utilities::get_admin_ui_extended_html_tags() );
                                }   ?>
                            </div>  <?php

							if ( ! empty( $content['desc'] ) ) {   ?>
								<div class="epkb-admin__form-tab-content-desc">
									<span class="epkb-admin__form-tab-content-desc__text"><?php echo esc_html( $content['desc'] ); ?></span>    <?php
									if ( ! empty( $content['read_more_url'] ) ) {   ?>
										<a class="epkb-admin__form-tab-content-desc__link" href="<?php echo esc_url( $content['read_more_url'] ); ?>" target="_blank"><?php echo esc_html( $content['read_more_text'] ); ?></a> <?php
									}   ?>
								</div>   <?php
							}   ?>

							<div class="epkb-admin__form-tab-content-body">     <?php
								echo wp_kses( $content['body_html'], EPKB_Utilities::get_admin_ui_extended_html_tags() );   ?>
							</div>
						</div>  <?php
					}

					foreach ( $tab['sub_tabs'] as $sub_tab ) {  ?>

						<div class="epkb-admin__form-sub-tab-wrap epkb-admin__form-sub-tab-wrap--<?php echo esc_attr( $sub_tab['key'] ); echo $sub_tab['active'] ? ' epkb-admin__form-sub-tab-wrap--active' : ''; ?>">  <?php

							foreach ( $sub_tab['contents'] as $content ) {
								$data_escaped = '';
								if ( ! empty( $content['data'] ) ) {
									foreach ( $content['data'] as $key => $value ) {
										$data_escaped .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
									}
								}   ?>

								<div class="epkb-admin__form-tab-content <?php echo empty( $content['css_class'] ) ? '' : esc_attr( $content['css_class'] ); ?>" <?php echo $data_escaped; ?>>

									<div class="epkb-admin__form-tab-content-title">    <?php
										if ( ! empty( $content['icon'] ) || ! empty( $content['icon_text'] ) ) {    ?>
											<span class="<?php echo esc_html( $content['icon'] ); ?> epkb-admin__form-tab-content-icon"><?php echo esc_html( $content['icon_text'] ); ?></span>   <?php
										}
										echo esc_html( $content['title'] );
										if ( ! empty( $content['tooltip_external_links'] ) ) {
											EPKB_HTML_Elements::display_tooltip( '', '', array(), $content['tooltip_external_links'] );
										}
										if ( ! empty( $content['help_links_html'] ) ) {
											echo wp_kses( $content['help_links_html'], EPKB_Utilities::get_admin_ui_extended_html_tags() );
										}   ?>
									</div>  <?php

									if ( ! empty( $content['desc'] ) ) {   ?>
										<div class="epkb-admin__form-tab-content-desc">
											<span class="epkb-admin__form-tab-content-desc__text"><?php echo esc_html( $content['desc'] ); ?></span>    <?php
											if ( ! empty( $content['read_more_url'] ) ) {   ?>
												<a class="epkb-admin__form-tab-content-desc__link" href="<?php echo esc_url( $content['read_more_url'] ); ?>" target="_blank"><?php echo esc_html( $content['read_more_text'] ); ?></a> <?php
											}   ?>
										</div>   <?php
									}   ?>

									<div class="epkb-admin__form-tab-content-body">     <?php
										echo wp_kses( $content['body_html'], EPKB_Utilities::get_admin_ui_extended_html_tags() );   ?>
									</div>
								</div>  <?php
							}

							// Optional link to Labels tab
							if ( $sub_tab['bottom_labels_link'] ) {
								self::display_bottom_labels_link();
							}   ?>
						</div>  <?php
					}

					// Optional link to Labels tab
					if ( $tab['bottom_labels_link'] ) {
						self::display_bottom_labels_link();
					}	?>
				</div>  <?php
			}   ?>
		</div>  <?php
	}


	/********************************************************************************
	 *
	 *                                   VARIOUS
	 *
	 ********************************************************************************/

	/**
	 * Fill missing fields in single admin page view configuration array with default values
	 *
	 * @param $page_view
	 * @return array
	 */
	private static function admin_page_view_fill_missing_with_default( $page_view ) {

		// Do not fill empty or not valid array
		if ( empty( $page_view ) || ! is_array( $page_view ) ) {
			return $page_view;
		}

		// Default page view
		$default_page_view = array(

			// Shared
			'minimum_required_capability'   => EPKB_Admin_UI_Access::get_admin_capability(),
			'secondary_tab_access_override' => [],
			'active'                        => false,
			'list_id'                       => '',
			'list_key'                      => '',
			'kb_config_id'				    => '',

			// Top Panel Item
			'label_text'                    => '',
			'main_class'                    => '',
			'label_class'                   => '',
			'icon_class'                    => '',

			// Secondary Panel Items
			'secondary_tabs'                => array(),

			// Boxes List
			'list_top_actions_html'         => '',
			'list_bottom_actions_html'      => '',
			'boxes_list'                    => array(),
			'vertical_tabs'                 => array(),

			// List footer HTML
			'list_footer_html'              => '',
		);

		// Default secondary view
		$default_secondary = array(

			// Shared
			'active'                    => false,
			'list_key'                  => '',

			// Secondary Panel Item
			'label_text'                => '',
			'main_class'                => '',
			'label_class'               => '',
			'icon_class'                => '',

			// Secondary Boxes List
			'list_top_actions_html'     => '',
			'list_bottom_actions_html'  => '',
			'boxes_list'                => array(),
		);

		// Default box
		$default_box = array(
			'icon_class'    => '',
			'class'         => '',
			'title'         => '',
			'description'   => '',
			'html'          => '',
			'return_html'   => false,
			'extra_tags'    => [],
		);

		// Default admin form tab in vertical_tabs
		$default_admin_form_tab = array(
			'title'     		=> '',
			'icon'      		=> '',
			'title_before_icon'	=> true,
			'key'       		=> '',
			'active'    		=> false,
			'contents'  		=> [],
			'sub_tabs'  		=> [],
			'bottom_labels_link' => false,
		);

		// Default content for admin form tab
		$default_admin_form_tab_content = array(
			'title'             => '',
			'icon'              => '',
			'icon_text'         => '',
			'desc'              => '',
			'body_html'         => '',
			'read_more_url'     => '',
			'read_more_text'    => '',
		);

		// Default admin form sub-tab in vertical_tabs
		$default_admin_form_sub_tab = array(
			'title'         => '',
			'icon'          => '',
			'icon_text'     => '',
			'key'           => '',
			'active'        => false,
			'contents'      => [],
			'class'         => '',
			'bottom_labels_link' => false,
		);

		// Set default view
		$page_view = array_merge( $default_page_view, $page_view );

		// Set default boxes
		foreach ( $page_view['boxes_list'] as $box_index => $box_content ) {

			// Do not fill empty or not valid array
			if ( empty( $page_view['boxes_list'][$box_index] ) || ! is_array( $page_view['boxes_list'][$box_index] ) ) {
				continue;
			}

			$page_view['boxes_list'][$box_index] = array_merge( $default_box, $box_content );
		}

		// Set default secondary views
		foreach ( $page_view['secondary_tabs'] as $secondary_index => $secondary_content ) {

			// Do not fill empty or not valid array
			if ( empty( $page_view['secondary_tabs'][$secondary_index] ) || ! is_array( $page_view['secondary_tabs'][$secondary_index] ) ) {
				continue;
			}

			// if minimum required capability is missed, then inherit it from upper level
			$secondary_content['minimum_required_capability'] = in_array( $secondary_content['list_key'], array_keys( $page_view['secondary_tab_access_override'] ) )
				? $page_view['secondary_tab_access_override'][$secondary_content['list_key']]
				: $page_view['minimum_required_capability'];

			$page_view['secondary_tabs'][$secondary_index] = array_merge( $default_secondary, $secondary_content );

			// Set default boxes
			foreach ( $page_view['secondary_tabs'][$secondary_index]['boxes_list'] as $box_index => $box_content ) {

				// Do not fill empty or not valid array
				if ( empty(  $page_view['secondary_tabs'][$secondary_index]['boxes_list'][$box_index] ) || ! is_array(  $page_view['secondary_tabs'][$secondary_index]['boxes_list'][$box_index] ) ) {
					continue;
				}

				$page_view['secondary_tabs'][$secondary_index]['boxes_list'][$box_index] = array_merge( $default_box, $box_content );
			}
		}

		if ( ! empty( $page_view['secondary_tab_access_override'] ) ) {
			$page_view['minimum_required_capability'] = reset( $page_view['secondary_tab_access_override'] );
		}

		// Set default tabs in vertical_tabs
		foreach ( $page_view['vertical_tabs'] as $tab_key => $admin_form_tab ) {
			$page_view['vertical_tabs'][$tab_key] = array_merge( $default_admin_form_tab, $admin_form_tab );

			// Set default contents in tabs
			foreach ( $page_view['vertical_tabs'][$tab_key]['contents'] as $content_index => $admin_form_tab_content ) {
				$page_view['vertical_tabs'][$tab_key]['contents'][$content_index] = array_merge( $default_admin_form_tab_content, $admin_form_tab_content );
			}

			// Set default sub-tabs
			foreach ( $page_view['vertical_tabs'][$tab_key]['sub_tabs'] as $sub_tab_index => $admin_form_sub_tab ) {
				$page_view['vertical_tabs'][$tab_key]['sub_tabs'][$sub_tab_index] = array_merge( $default_admin_form_sub_tab, $admin_form_sub_tab );

				// Set default contents in sub-tabs
				foreach ( $admin_form_sub_tab['contents'] as $context_index => $admin_form_sub_tab_content ) {
					$page_view['vertical_tabs'][$tab_key]['sub_tabs'][$sub_tab_index]['contents'][$context_index] = array_merge( $default_admin_form_tab_content, $admin_form_sub_tab_content );
				}
			}
		}

		return $page_view;
	}

	/**
	 * We need to add this HTML to admin page to catch JS from third party plugins and show missing CSS message if needed
	 */
	public static function admin_page_header() {  ?>

		<!-- This is to catch 3rd party plugins JS output -->
		<div class="wrap epkb-wp-admin">
			<h1></h1>
		</div>
		<div class=""></div>  <?php

		EPKB_Core_Utilities::display_missing_css_message();
	}

	/**
	 * Display modal form in admin area for user to submit an error to support. For example Setup Wizard/Editor encounters error.
	 */
	public static function display_report_admin_error_form() {     ?>

		<!-- Submit Error Form -->
		<div class="epkb-admin__error-form__container" style="display:none!important;">
			<div class="epkb-admin__error-form__wrap">
				<div class="epkb-admin__scroll-container">
					<div class="epkb-admin__white-box">

						<h4 class="epkb-admin__error-form__title"></h4>
						<div class="epkb-admin__error-form__desc"></div>

						<form id="epkb-admin__error-form" method="post">				<?php

							EPKB_HTML_Admin::nonce();				?>

							<input type="hidden" name="action" value="epkb_report_admin_error" >
							<div class="epkb-admin__error-form__body">

								<label for="epkb-admin__error-form__message"><?php esc_html_e( 'Error Details', 'echo-knowledge-base' ); ?>*</label>
								<textarea name="admin_error" class="admin_error" required id="epkb-admin__error-form__message"></textarea>

								<div class="epkb-admin__error-form__btn-wrap">
									<input type="submit" name="submit_error" value="<?php esc_attr_e( 'Submit', 'echo-knowledge-base' ); ?>" class="epkb-admin__error-form__btn epkb-admin__error-form__btn-submit">
									<span class="epkb-admin__error-form__btn epkb-admin__error-form__btn-cancel"><?php esc_html_e( 'Cancel', 'echo-knowledge-base' ); ?></span>
								</div>

								<div class="epkb-admin__error-form__response"></div>
							</div>
						</form>

						<div class="epkb-close-notice epkbfa epkbfa-window-close"></div>

					</div>
				</div>
			</div>
		</div>      <?php
	}

	/**
	 * Display or return HTML input for wpnonce
	 *
	 * @param false $return_html
	 *
	 * @return false|string|void
	 */
	public static function nonce( $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}   ?>

		<input type="hidden" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( '_wpnonce_epkb_ajax_action' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>">	<?php

		if ( $return_html ) {
			return ob_get_clean();
		}
	}

	/**
	 * Display content box with link to Labels tab
	 * @return void
	 */
	private static function display_bottom_labels_link() {	?>
		<div class="epkb-admin__form-tab-content epkb-admin__form-tab-content--bottom-labels-link">
			<div class="epkb-admin__form-tab-content-title">    <?php
				esc_html_e( 'Labels', 'echo-knowledge-base' );	?>
			</div>
			<div class="epkb-admin__form-tab-content-desc">
				<span><?php esc_html_e( 'Labels are the text elements displayed on your website\'s front end, such as button names and custom headings. ' .
											'Customize these labels to match your website\'s language and tone.', 'echo-knowledge-base' ); ?></span>
				<a class="epkb-admin__form-tab-content-desc__link" href="#" target="_blank"><?php esc_html_e( 'click here to customize the labels', 'echo-knowledge-base' ); ?></a>
			</div>
			<div class="epkb-admin__form-tab-content-body"></div>
		</div>	<?php
	}

	/**
	 * Display warning about missing main page
	 *
	 * @param $kb_config
	 * @param bool $return_html
	 *
	 * @return string|void
	 */
	public static function display_no_main_page_warning( $kb_config, $return_html=false ) {

		$notification_escaped = EPKB_HTML_Forms::notification_box_middle( array(
			'type'  => 'error',
			'title' => esc_html__( 'We did not detect any Main Page for your knowledge base', 'echo-knowledge-base' ) . ': ' . esc_html( $kb_config['kb_name'] ) . ' . ' . esc_html__( 'You can do the following:', 'echo-knowledge-base' ),
			'desc'  => '<ul>
							<li>' . esc_html__( 'If you have a KB Main Page, please re-save it and then come back', 'echo-knowledge-base' ) . '</li>
                            <li>' . __( 'Run Setup Wizard to create a new KB Main Page', 'echo-knowledge-base' )
				. ' ' . '<a href="'.esc_url( admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) . '&page=epkb-kb-configuration&setup-wizard-on' ) ) . '" target="_blank">' . __( 'Run Setup Wizard', 'echo-knowledge-base' ) . '</a></li>
							<li>' . __( 'Create one manually as described here:', 'echo-knowledge-base' )
				. ' ' . '<a href="https://www.echoknowledgebase.com/documentation/knowledge-base-shortcode/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a></li>
                        </ul>'
		), $return_html  );

		if ( $return_html ) {
			return $notification_escaped;
		} else {
			//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $notification_escaped;
		}
	}
}
