<?php

namespace Elementor;

use \ElementsKit_Lite\Modules\Onepage_Scroll\Init;

class ElementsKit_Extend_Onepage_Scroll {
	public function __construct() {
		/**
		 * Page Controls
		 */
		add_action( 'elementor/documents/register_controls', array( $this, 'register_page_controls' ) );

		/**
		 * Section Controls
		 */
		add_action( 'elementor/element/section/section_advanced/after_section_end', array( $this, 'register_section_controls' ) );

		/**
		 * Navigation Markup
		 */
		add_action( 'wp_footer', array( $this, 'generate_navigation_markup' ) );
		add_action( 'wp_ajax_generate_navigation_markup', array( $this, 'generate_navigation_markup' ) );

		/**
		 * Pro Notice
		 */
		if ( \ElementsKit_Lite::package_type() === 'free' ) {
			add_action( 'elementor/element/wp-page/ekit_page_settings/before_section_end', array( $this, 'pro_panel_notice' ), 99 );
			add_action( 'elementor/element/section/ekit_onepagescroll_section/before_section_end', array( $this, 'pro_panel_notice' ), 99 );
		}
	}


	/**
	 * Pro Panel Notice
	 */
	public function pro_panel_notice( $element ) {
		$element->add_control(
			'ekit_control_get_pro',
			array(
				'label'       => esc_html__( 'Unlock more possibilities', 'elementskit-lite' ),
				'type'        => \Elementor\Controls_Manager::CHOOSE,
				'options'     => array(
					'1' => array(
						'icon' => 'fa fa-unlock-alt',
					),
				),
				'default'     => '1',
				'toggle'      => false,
				'separator'   => 'before',
				'description' => sprintf( __( '%1$s Get the %2$s Pro version %3$s for more awesome elements and powerful modules. %4$s', 'elementskit-lite' ), '<span class="ekit-widget-pro-feature">', '<a href="https://wpmet.com/elementskit-pricing" target="_blank">', '</a>', '</span>' ),
			)
		);
	}


	/**
	 * Page Controls
	 */
	public function register_page_controls( Controls_Stack $element ) {
		$element->start_controls_section(
			'ekit_page_settings',
			array(
				'label' => esc_html__( 'ElementsKit Settings', 'elementskit-lite' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);
			$element->add_control(
				'ekit_onepagescroll',
				array(
					'label'              => esc_html__( 'Enable Onepage Scroll', 'elementskit-lite' ),
					'type'               => Controls_Manager::SWITCHER,
					'return_value'       => 'block',
					'frontend_available' => true,
					'selectors'          => array(
						'div.onepage_scroll_nav' => 'display: {{VALUE}};',
					),
				)
			);
		$element->end_controls_section();
	}


	/**
	 * Section Controls
	 */
	public function register_section_controls( Controls_Stack $element ) {
		$element->start_controls_section(
			'ekit_onepagescroll_section',
			array(
				'label'         => esc_html__( 'ElementsKit Onepage Scroll', 'elementskit-lite' ),
				'tab'           => Controls_Manager::TAB_ADVANCED,
				'hide_in_inner' => true,
			)
		);
			$element->add_control(
				'ekit_has_onepagescroll',
				array(
					'label'              => esc_html__( 'Enable Section', 'elementskit-lite' ),
					'type'               => Controls_Manager::SWITCHER,
					'frontend_available' => true,
					'return_value'       => 'section',
					'prefix_class'       => 'ops-',
				)
			);
		$element->end_controls_section();
	}


	/**
	 * Navigation Markup
	 */
	public function generate_navigation_markup() {
		$is_active = Init::get_page_setting( 'ekit_onepagescroll' );
		$is_nav    = $nav_style = Init::get_page_setting( 'ekit_onepagescroll_nav' );
		$is_pro    = \ElementsKit_Lite::package_type() === 'pro';
		$is_editor = \Elementor\Plugin::$instance->preview->is_preview_mode();
		$nav_pos   = Init::get_page_setting( 'ekit_onepagescroll_nav_pos' );
		$nav_icon  = Init::get_page_setting( 'ekit_onepagescroll_nav_icon' );

		if ( ! ( $is_pro && $is_active && $is_nav ) ) {
			return;
		} elseif ( $is_editor ) {
			echo '<div id="onepage_scroll_nav_wrap">';
		}

		$classlist = array(
			'wrapper' => 'nav-style-' . $nav_style . ' met_d--none met_pos--fixed ',
			'ul'      => 'met_list--none met_m--0 met_p--0 met_lh--0 ',
			'li'      => 'met_not_last_mb--20 ',
			'link'    => '',
			'tooltip' => '',
			'arrow'   => '',
			'span'    => '',
		);

		switch ( $nav_pos ) {
			case 'top':
				$classlist['wrapper'] .= 'met-' . $nav_pos . ' met_top--0 met_left--50p met_translateLeft--m50p met_my--20 ';
				$classlist['ul']      .= 'met_d--flex ';
				$classlist['li']       = 'met_not_last_mr--20 ';

				$classlist['tooltip'] .= 'met_top--100p ';
				$classlist['arrow']   .= 'met_bdb_color--current met_top--100p ';
				break;
			
			case 'bottom':
				$classlist['wrapper'] .= 'met-' . $nav_pos . ' met_bottom--0 met_left--50p met_translateLeft--m50p met_my--20 ';
				$classlist['ul']      .= 'met_d--flex ';
				$classlist['li']       = 'met_not_last_mr--20 ';

				$classlist['tooltip'] .= 'met_bottom--100p ';
				$classlist['arrow']   .= 'met_bdt_color--current met_bottom--100p ';
				break;

			case 'left':
				$classlist['wrapper'] .= 'met-' . $nav_pos . ' met_top--50p met_left--0 met_translateTop--m50p met_mx--20 ';

				$classlist['tooltip'] .= 'met_left--100p ';
				$classlist['arrow']   .= 'met_bdr_color--current met_left--100p ';
				break;
			
			case 'right':
				$classlist['wrapper'] .= 'met-' . $nav_pos . ' met_top--50p met_right--0 met_translateTop--m50p met_mx--20 ';

				$classlist['tooltip'] .= 'met_right--100p ';
				$classlist['arrow']   .= 'met_bdl_color--current met_right--100p ';
				break;
		}

		include_once 'nav-styles/' . $nav_style . '.php';

		if ( $is_editor ) :
			echo '</div>';
		endif;
	}
}
