<?php

/**
 * Visual Helper - add a Visual Helper
 *
 */
class EPKB_Visual_Helper {

	public function __construct() {
		add_action( 'wp_ajax_epkb_visual_helper_update_switch_settings',  array( $this, 'update_switch_settings_handler' ) );
		add_action( 'wp_ajax_nopriv_epkb_visual_helper_update_switch_settings', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_visual_helper_switch_template',  array( $this, 'switch_template_handler' ) );
		add_action( 'wp_ajax_nopriv_epkb_visual_helper_switch_template', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Add Visual Helper Elements to KB Main Page
	 */
	public function epkb_generate_page_content( $settings_info_icons, $kb_config, $settings_side_menu = array() ) {

		wp_enqueue_style( 'epkb-frontend-visual-helper' );
		wp_enqueue_script( 'epkb-frontend-visual-helper' );

		ob_start(); ?>

		<div class="epkb-vshelp__wrapper">  <?php
			$this->epkb_visual_helper_toggle( $kb_config );
            if ( count( $settings_side_menu ) ) {
	            $this->epkb_visual_helper_side_menu( $kb_config, $settings_side_menu );
            } ?>
		</div>  <?php

		$this->generate_info_components( $settings_info_icons );

		echo ob_get_clean();
	}

	/**
	 * Generate Visual Helper Toggle HTML element
	 */
	private function epkb_visual_helper_toggle( $kb_config ) {

		$kb_id = EPKB_Utilities::get_eckb_kb_id();
        $active_state = $kb_config['visual_helper_switch_show_state'] ?? 'off';

		ob_start(); ?>

		<div class="epkb-vshelp-toggle-wrapper">
			<div class="epkb-vshelp-icon-wrapper">
				<span class="ep_font_icon_info"></span>
			</div>
			<div class="epkb-vshelp-title">
				<span class="epkb-vshelp-title__text"><?php esc_html_e( 'Toggle Visual Helper', 'echo-knowledge-base' ); ?></span>
			</div>
			<div class="epkb-settings-control">
				<label class="epkb-settings-control-toggle">
					<input type="checkbox" class="epkb-settings-control__input__toggle js-epkb-side-menu-toggle" value="<?php esc_attr( $active_state ) ?>" name="visual_helper_switch_show_state" <?php checked( $active_state, 'on' ); ?> data-kbid="<?php echo esc_attr( $kb_id ); ?>">
					<span class="epkb-settings-control__input__label" data-on="<?php esc_html_e( 'On', 'echo-knowledge-base' ); ?>" data-off="<?php esc_html_e( 'Off', 'echo-knowledge-base' ); ?>"></span>
					<span class="epkb-settings-control__input__handle"></span>
				</label>
			</div>
			<div class="epkb-vshelp-hide-switcher<?php echo ( $active_state === 'on' ) ? ' epkb-vshelp-hide-switcher--hidden' : ''; ?>" data-kbid="<?php echo esc_attr( $kb_id ); ?>">
				<span class="epkb-vshelp-hide-switcher__icon epkbfa epkbfa-times-circle"></span>
			</div>
		</div>		<?php

		echo ob_get_clean();
	}

	/**
	 * Generate Visual Helper Side Menu HTML element
	 */
	private function epkb_visual_helper_side_menu( $kb_config, $settings_side_menu ) {

		$keys_to_check = ['ml_row_1_module', 'ml_row_2_module', 'ml_row_3_module', 'ml_row_4_module', 'ml_row_5_module'];

        $switch_toggle_active = $kb_config['visual_helper_switch_show_state'] ?? 'off';

		foreach ( $keys_to_check as $key ) {
			if ( isset( $kb_config[$key] ) ) {
				if ( $kb_config[$key] === 'categories_articles' ) {
					$category_row = substr($key, 0,-7 );
					preg_match( '/\d+/', $category_row, $matches );

				} elseif ( $kb_config[$key] === 'search' ) {
					$search_row = substr($key, 0,-7 );
					preg_match( '/\d+/', $search_row, $matches );
				}
			}
		}

		ob_start();

        $side_menu_styles =  $switch_toggle_active === 'off' ? 'style="display: none;"' : '';        ?>
		<div class="epkb-vshelp-side-menu-wrapper <?php echo esc_attr( EPKB_Utilities::get_active_theme_classes() ); ?>" <?php echo $side_menu_styles != '' ? $side_menu_styles : ''; ?>>
			<div class="epkb-vshelp-side-menu-header">
				<div class="epkb-vshelp-icon-wrapper">
					<span class="ep_font_icon_info"></span>
				</div>
				<div class="epkb-vshelp-title">
					<span><?php esc_html_e( 'Page Information', 'echo-knowledge-base' ); ?></span>
				</div>
			</div>
			<div class="epkb-vshelp-side-menu-body">    <?php
                foreach (  $settings_side_menu as $section ) {

					$button_wrapper_id  = $section['details_button_id'] ?? '';
                    $section_title = $section['box_title'] ?? '';
                    $section_content_escaped = $section['box_content'] ?? '';

                    if ( ! empty( $section_content_escaped ) ): ?>
                        <div class="epkb-vshelp-accordion-wrapper">
                            <div class="epkb-vshelp-accordion-header">                                <?php
	                            if ( ! empty( $section_title ) ): ?>
                                    <span><?php echo esc_html( $section_title ); ?></span>                                <?php
	                            endif; ?>
                                <button class="epkb-vshelp-accordion-header__button js-epkb-accordion-toggle" <?php echo ( ! empty( $button_wrapper_id ) ? 'id="' . esc_attr( $button_wrapper_id ) . '"' : '') ?>><?php
	                                esc_html_e( 'Details', 'echo-knowledge-base' ); ?>
                                </button>
                            </div>
                            <div class="epkb-vshelp-accordion-body" style="display: none">                                <?php
	                            echo $section_content_escaped; ?>
                            </div>
                        </div>                    <?php
                    endif;
                } ?>
			</div>
		</div>        <?php

		echo ob_get_clean();
	}

	/**
	 * Generate info components for the visual helper
	 */
	private function generate_info_components( $settings_info_icons ) {
		ob_start();
		foreach ( $settings_info_icons as $key => $info_icon ) {
			$output_escaped = $this->display_info_modal( $key, $info_icon );
			echo $output_escaped;
		}
		echo ob_get_clean();
	}

	/**
	 * Render info modal windows
	 * @param $section_id
	 * @param $info_icon
	 * @return string
	 */
	private function display_info_modal( $section_id, $info_icon ) {
		ob_start(); ?>

		<div class="epkb-vshelp-info-modal epkb-vshelp-info-modal--<?php echo esc_attr( $section_id ); ?>" data-section-id="<?php echo esc_attr( $section_id ); ?>" data-selectors="<?php echo esc_attr( $info_icon['connected_selectors'] ?? '' ); ?>">
			<div class="epkb-vshelp-info-modal__content">   <?php
				if ( $modalTitle = ( $info_icon['modalTitle'] ?? false ) ) { ?>
					<h3 class="epkb-vshelp-info-modal__title">  <?php
						echo esc_html( $modalTitle ); ?>
					</h3>                <?php
				}

				if ( $modalSections = ( $info_icon['modalSections'] ?? false ) ) { ?>
					<div class="epkb-vshelp-info-modal__sections">  <?php

						foreach ( $modalSections as $section ) {
							$section_title = $section['title'] ?? false;
							$section_location = $section['location'] ?? false;
							$section_content_escaped = $section['content'] ?? false;
							$section_link = $section['link'] ?? array(); ?>

							<div class="epkb-vshelp-info-modal__section">   <?php

								if ( $section_title ) { ?>
									<h4 class="epkb-vshelp-info-modal__section-title">										<?php
										echo esc_html( $section_title ); ?>
									</h4>   <?php
								}
								if ( $section_location ) { ?>
									<div class="epkb-vshelp-info-modal__section-location">										<?php
										echo esc_html( $section_location ); ?>
									</div>   <?php
								}

								if ( $section_content_escaped ) { ?>

									<p class="epkb-vshelp-info-modal__section-content">     <?php
										echo $section_content_escaped; ?>
									</p>    <?php

									if ( ! empty( $section_link ) ) { ?>
										<div class="epkb-vshelp-info-modal__section-link-wrapper">
											<a class="epkb-vshelp-info-modal__section-link "
											   href="<?php echo esc_url( $section_link ); ?>"
											   target="_blank"> <?php
												echo esc_html__( 'Configure Here', 'echo-knowledge-base' ); ?>
											</a>
											<span class="ep_font_icon_external_link epkb-vshelp-info-modal__section-link-icon"></span>
										</div>			<?php
									}
								} ?>
							</div>  <?php
						} ?>
					</div>  <?php
				} ?>
			</div>
		</div><?php

		return ob_get_clean();
	}

	/**
	 * Change switcher state for visual helper - AJAX handler
	 */
	public function update_switch_settings_handler() {

        $setting_name = EPKB_Utilities::post( 'setting_name', 'visual_helper_switch_visibility_toggle' );
		$kb_id = EPKB_Utilities::post( 'kb_id', EPKB_KB_Config_DB::DEFAULT_KB_ID );
        $current_state = epkb_get_instance()->kb_config_obj->get_value( $kb_id, $setting_name );

		epkb_get_instance()->kb_config_obj->set_value( $kb_id, $setting_name, $current_state === 'on' ? 'off' : 'on' );

		wp_send_json_success( esc_html__( 'Settings saved', 'echo-knowledge-base' ) );
	}

    /**
     * Switch KB page template
     */
    public function switch_template_handler() {

        $kb_id     = EPKB_Utilities::post( 'kb_id', EPKB_KB_Config_DB::DEFAULT_KB_ID );
        $template  = EPKB_Utilities::post( 'current_template', 'kb_templates' );
        $prop_name = EPKB_Utilities::post( 'prop_name', 'templates_for_kb' );

        epkb_get_instance()->kb_config_obj->set_value( $kb_id, $prop_name, $template );

        wp_send_json_success( esc_html__( 'Settings saved', 'echo-knowledge-base' ) );
    }
}