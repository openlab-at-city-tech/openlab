<?php
/**
 * Handle Header Controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Header_Controls
 */
class Hestia_Header_Controls extends Hestia_Register_Customizer_Controls {

	/**
	 * Add the customizer controls.
	 */
	public function add_controls() {
		$this->register_type( 'Hestia_Customize_Control_Radio_Image', 'control' );
		$this->add_sections();

		$this->add_panel(
			new Hestia_Customizer_Panel(
				'hestia_header_options',
				array(
					'priority' => 35,
					'title'    => esc_html__( 'Header Options', 'hestia' ),
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_header_image_sitewide',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Enable Header Image Sitewide', 'hestia' ),
					'section'  => 'header_image',
					'priority' => 25,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_top_bar_hide',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => true,
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Disable section', 'hestia' ),
					'section'  => 'hestia_top_bar',
					'priority' => 1,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_link_to_top_menu',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'priority'     => 25,
					'section'      => 'hestia_top_bar',
					'button_text'  => esc_html__( 'Very Top Bar', 'hestia' ) . ' ' . esc_html__( 'Menu', 'hestia' ),
					'button_class' => 'hestia-link-to-top-menu',
					'icon_class'   => 'fa-bars',
				),
				'Hestia_Button'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_top_menu_hidden',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'priority' => 25,
					'type'     => 'hidden',
					'section'  => 'menu_locations',
				),
				null,
				array(
					'selector'        => '.top-bar-nav',
					'settings'        => 'hestia_top_menu_hidden',
					'render_callback' => array( $this, 'top_bar_callback' ),
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_search_in_menu',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Enable Search in Menu', 'hestia' ),
					'section'  => 'hestia_navigation',
					'priority' => 5,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_header_alignment',
				array(
					'default'           => apply_filters( 'hestia_header_alignment_default', 'left' ),
					'sanitize_callback' => 'hestia_sanitize_alignment_options',
				),
				array(
					'label'    => esc_html__( 'Layout', 'hestia' ),
					'priority' => 25,
					'section'  => 'hestia_navigation',
					'choices'  => apply_filters(
						'hestia_nav_layout_choices',
						array(
							'left'   => array(
								'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAADFBMVEX////V1dUAhbo+yP8aB7nsAAAARklEQVRYhWNgGEGA/z8EHKC+MqZVq1ah4xGtjOLgHQWjYBSMgmENREOxgoDhp2xkASIDYxgoGwUUACJDdlTZKBgFo2DAAAAZAflVkm2NtwAAAABJRU5ErkJggg==',
							),
							'center' => array(
								'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAADFBMVEX////V1dUAhbo+yP8aB7nsAAAARElEQVRYR2NgGKngPwT8GVU2BJQNb6C1atUCfHjEKRsFo2BEANFQrCBg+CkbWYDIwBgGykYBBYDIkB1VNgpGwSgYMAAAzIYr4wYj4sEAAAAASUVORK5CYII=',
							),
							'right'  => array(
								'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAD1BMVEX////V1dXF7v8Ahbo+yP+vNfjRAAAAWUlEQVR42u3TsQ2AMAxFQRAswAaIFViB/WeKlI4KgUJMwl3j7lXfA3+xXVvfas3HmZaWVtw/1mrRjmnPnl6tDlsAEcblFq2PtuhLyS1oxbWgjpIL1dICgEYlsKfbvyzuWeMAAAAASUVORK5CYII=',
							),
						)
					),
				),
				'Hestia_Customize_Control_Radio_Image'
			)
		);

		/**
		 * Customize control for header layout.
		 */
		$sidebar_choices = apply_filters(
			'hestia_header_layout_choices',
			array(
				'default'      => array(
					'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAD1BMVEU+yP/////Y9P/G7//V1dUbjhlcAAAAW0lEQVR4Ae3SAQmAYAyE0V9NMDCBCQxh/0wKGGCAIJ7vC3DA28ZvkjRVo49vzVujoeYFbF15i32pu4CtlCTVc+Vu2VqPRi9ssWfPnj179uzZs2fPnj179uwzt07LZ+4ImOW7JwAAAABJRU5ErkJggg==',
				),
				'no-content'   => array(
					'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAElBMVEU+yP////88SFhjbXl1fonV1dUUDrn8AAAAXElEQVR4Ae3SMQ2AYAyEUSwAYOC3gAJE4N8KCztNKEPT9wm44eUmSZL0b3NeXbeWEaj41noEet/yCVs+cW7jqfjW12ztV6D8Lfbs2bNnz549e/bs2bNnz559060bqAJ8azq5sAYAAAAASUVORK5CYII=',
				),
				'classic-blog' => array(
					'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAElBMVEX///88SFhjbXl1fok+yP/V1dWks4cUAAAAXElEQVR4Ae3SMQ2AQBBE0QNAwFlAASKwgH8rNNSwCdfs5j0BU/xMo6ypByTfmveAxmd7Wz5xLP2Rf4tf1jPAli1btl7YsmWL7QoYuoX22lelvfbaa6892mufifbcjgr1IbRYbwEAAAAASUVORK5CYII=',
				),
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_header_layout',
				array(
					'sanitize_callback' => 'sanitize_key',
					'default'           => 'default',
				),
				array(
					'label'    => esc_html__( 'Layout', 'hestia' ),
					'section'  => 'header_image',
					'priority' => 10,
					'choices'  => $sidebar_choices,
				),
				'Hestia_Customize_Control_Radio_Image'
			)
		);

		$this->add_selective_refresh_to_header_items();
	}

	/**
	 * Change customizer controls.
	 */
	public function change_controls() {
		$this->move_header_image_section();
		$this->move_top_bar_controls();
		$this->move_controls_to_navigation_sidebar();
	}

	/**
	 * Move controls to nav sidebar.
	 */
	private function move_controls_to_navigation_sidebar() {
		$navigation_sidebar = $this->get_customizer_object( 'section', 'sidebar-widgets-header-sidebar' );
		if ( empty( $navigation_sidebar ) ) {
			return;
		}
		$navigation_sidebar->panel = 'hestia_header_options';
		$hestia_header_alignment   = $this->get_customizer_object( 'control', 'hestia_header_alignment' );
		if ( ! empty( $hestia_header_alignment ) ) {

			$hestia_header_alignment->section  = 'sidebar-widgets-header-sidebar';
			$hestia_header_alignment->priority = - 1;
		}
		$hestia_search_in_menu = $this->get_customizer_object( 'control', 'hestia_search_in_menu' );
		if ( ! empty( $hestia_search_in_menu ) ) {
			$hestia_search_in_menu->section  = 'sidebar-widgets-header-sidebar';
			$hestia_search_in_menu->priority = - 1;
		}
	}

	/**
	 * Add selective refresh to header logo and site name.
	 */
	private function add_selective_refresh_to_header_items() {
		$this->get_customizer_object( 'setting', 'blogname' )->transport = 'postMessage';

		$this->add_partial(
			new Hestia_Customizer_Partial(
				'blogname',
				array(
					'selector'        => '.navbar .navbar-brand p',
					'settings'        => array( 'blogname' ),
					'render_callback' => array( $this, 'blog_name_callback' ),
				)
			)
		);

		$this->add_partial(
			new Hestia_Customizer_Partial(
				'custom_logo',
				array(
					'selector'        => '.navbar-brand',
					'settings'        => 'custom_logo',
					'render_callback' => array( $this, 'logo_callback' ),
				)
			)
		);
	}

	/**
	 * Move header image controls.
	 */
	private function move_header_image_section() {

		$this->get_customizer_object( 'setting', 'custom_logo' )->transport = 'postMessage';

		$header_image_section = $this->get_customizer_object( 'section', 'header_image' );

		if ( ! empty( $header_image_section ) ) {
			$header_image_section->title    = esc_html__( 'Header Settings', 'hestia' );
			$header_image_section->panel    = 'hestia_header_options';
			$header_image_section->priority = 20;
		}

		$header_image_control = $this->get_customizer_object( 'control', 'header_image' );
		if ( ! empty( $header_image_control ) ) {
			$header_image_control->priority = 15;
		}

		$header_image_data_control = $this->get_customizer_object( 'control', 'header_image_data' );
		if ( ! empty( $header_image_data_control ) ) {
			$header_image_data_control->priority = 20;
		}
	}

	/**
	 * Move top bar controls.
	 */
	private function move_top_bar_controls() {
		$top_bar_sidebar = $this->get_customizer_object( 'section', 'sidebar-widgets-sidebar-top-bar' );
		if ( ! empty( $top_bar_sidebar ) ) {
			$top_bar_sidebar->panel = 'hestia_header_options';
			$controls_to_move       = array(
				'hestia_top_bar_hide',
				'hestia_link_to_top_menu',
			);
			foreach ( $controls_to_move as $control ) {
				$hestia_control = $this->get_customizer_object( 'control', $control );
				if ( ! empty( $hestia_control ) ) {
					$hestia_control->section  = 'sidebar-widgets-sidebar-top-bar';
					$hestia_control->priority = - 2;
				}
			}
		}
	}

	/**
	 * Add sections.
	 */
	private function add_sections() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_navigation',
				array(
					'title'    => esc_html__( 'Navigation', 'hestia' ),
					'panel'    => 'hestia_header_options',
					'priority' => 15,
				)
			)
		);

		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_top_bar',
				array(
					'title'    => esc_html__( 'Very Top Bar', 'hestia' ),
					'panel'    => 'hestia_header_options',
					'priority' => 10,
				)
			)
		);
	}

	/**
	 * Blog name callback function
	 *
	 * @return void
	 */
	public function blog_name_callback() {
		bloginfo( 'name' );
	}

	/**
	 * Custom logo callback function.
	 *
	 * @return string
	 */
	public function logo_callback() {
		if ( get_theme_mod( 'custom_logo' ) ) {
			$logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
			$logo = '<img src="' . esc_url( $logo[0] ) . '">';
		} else {
			$logo = '<p>' . get_bloginfo( 'name' ) . '</p>';
		}

		return $logo;
	}
}
