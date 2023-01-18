<?php

namespace Elementor;

use \ElementsKit_Lite\Modules\Onepage_Scroll\Init;

class ElementsKit_Pro_Extend_Onepage_Scroll {
	public function __construct() {
		/**
		 * Pro Page Controls
		 */
		add_action( 'elementor/element/wp-page/ekit_page_settings/before_section_end', array( $this, 'pro_page_controls' ) );

		/**
		 * Pro Section Controls
		 */
		add_action( 'elementor/element/section/ekit_onepagescroll_section/before_section_end', array( $this, 'pro_section_controls' ) );
	}


	/**
	 * Pro Page Controls
	 */
	public function pro_page_controls( Controls_Stack $element ) {
		$element->add_control(
			'ekit_onepagescroll_heading',
			array(
				'label'     => __( 'Onepage Scroll Settings', 'elementskit-lite' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'ekit_onepagescroll' => 'block',
				),
			)
		);

		$element->add_control(
			'ekit_onepagescroll_nav',
			array(
				'label'              => esc_html__( 'Navigation Style', 'elementskit-lite' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '0',
				'groups'             => array(
					'0'    => esc_html__( 'None', 'elementskit-lite' ),
					array(
						'label'   => esc_html__( 'Circle', 'elementskit-lite' ),
						'options' => array(
							'circle-scale-up'      => esc_html__( 'Scale Up', 'elementskit-lite' ),
							'circle-fill-in'       => esc_html__( 'Fill In', 'elementskit-lite' ),
							'circle-fill-out'      => esc_html__( 'Fill Out', 'elementskit-lite' ),
							'circle-stroke'        => esc_html__( 'Stroke', 'elementskit-lite' ),
							'circle-stroke-dot'    => esc_html__( 'Stroke Dot', 'elementskit-lite' ),
							'circle-stroke-simple' => esc_html__( 'Stroke Simple', 'elementskit-lite' ),
							'circle-dot-move'      => esc_html__( 'Dot Move', 'elementskit-lite' ),
							'circle-timeline'      => esc_html__( 'Timeline', 'elementskit-lite' ),
						),
					),
					array(
						'label'   => esc_html__( 'Square', 'elementskit-lite' ),
						'options' => array(
							'square-scale-up' => esc_html__( 'Scale Up', 'elementskit-lite' ),
						),
					),
					array(
						'label'   => esc_html__( 'Line', 'elementskit-lite' ),
						'options' => array(
							'line-grow'   => esc_html__( 'Line Grow', 'elementskit-lite' ),
							'line-shrink' => esc_html__( 'Line Shrink', 'elementskit-lite' ),
							'line-fill'   => esc_html__( 'Line Fill', 'elementskit-lite' ),
							'line-move'   => esc_html__( 'Line Move', 'elementskit-lite' ),
						),
					),
					'icon' => esc_html__( 'Custom Icon', 'elementskit-lite' ),
				),
				'frontend_available' => true,
				'condition'          => array(
					'ekit_onepagescroll' => 'block',
				),
			)
		);

		$element->add_control(
			'ekit_onepagescroll_nav_icon',
			array(
				'label'              => esc_html__( 'Navigation Icon', 'elementskit-lite' ),
				'type'               => Controls_Manager::ICONS,
				'default'            => array(
					'value'   => 'fas fa-circle',
					'library' => 'solid',
				),
				'frontend_available' => true,
				'condition'          => array(
					'ekit_onepagescroll'     => 'block',
					'ekit_onepagescroll_nav' => 'icon',
				),
			)
		);

		$element->add_control(
			'ekit_onepagescroll_nav_pos',
			array(
				'label'              => esc_html__( 'Navigation Position', 'elementskit-lite' ),
				'type'               => Controls_Manager::CHOOSE,
				'default'            => 'right',
				'options'            => array(
					'top' => array(
						'title' => esc_html__( 'Top', 'elementskit-lite' ),
						'icon'  => 'fa fa-angle-up',
					),
					'bottom' => array(
						'title' => esc_html__( 'Bottom', 'elementskit-lite' ),
						'icon'  => 'fa fa-angle-down',
					),
					'left' => array(
						'title' => esc_html__( 'Left', 'elementskit-lite' ),
						'icon'  => 'fa fa-angle-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'elementskit-lite' ),
						'icon'  => 'fa fa-angle-right',
					),
				),
				'toggle'             => false,
				'frontend_available' => true,
				'condition'          => array(
					'ekit_onepagescroll'      => 'block',
					'ekit_onepagescroll_nav!' => '0',
				),
			)
		);

		$element->add_control(
			'ekit_onepagescroll_nav_pos_offset',
			array(
				'label'      => esc_html__( 'Navigation Position Offset', 'elementskit-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 10,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => '50',
				),
				'condition'  => array(
					'ekit_onepagescroll'      => 'block',
					'ekit_onepagescroll_nav!' => '0',
				),
				'selectors'  => array(
					'.onepage_scroll_nav.met-top, .onepage_scroll_nav.met-bottom'   => 'left: {{SIZE}}{{UNIT}};',
					'.onepage_scroll_nav.met-left, .onepage_scroll_nav.met-right'   => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_control(
			'ekit_onepagescroll_nav_spacing',
			array(
				'label'      => esc_html__( 'Navigation Spacing', 'elementskit-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => '20',
				),
				'condition'  => array(
					'ekit_onepagescroll'      => 'block',
					'ekit_onepagescroll_nav!' => '0',
				),
				'selectors'  => array(
					'.onepage_scroll_nav:not(.met-top):not(.met-bottom) li:not(:last-child)'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'.onepage_scroll_nav:not(.met-left):not(.met-right) li:not(:last-child)'  => 'margin-right: {{SIZE}}{{UNIT}};',

					'.onepage_scroll_nav.nav-style-circle-timeline:not(.met-top):not(.met-bottom) li:before'  => 'height: calc({{SIZE}}{{UNIT}} - 1px)',
					'.onepage_scroll_nav.nav-style-circle-timeline:not(.met-left):not(.met-right) li:before'  => 'width: calc({{SIZE}}{{UNIT}} - 1px)',
				),
			)
		);

		$element->add_control(
			'ekit_onepagescroll_nav_color',
			array(
				'label'     => esc_html__( 'Navigation Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111',
				'condition' => array(
					'ekit_onepagescroll'      => 'block',
					'ekit_onepagescroll_nav!' => '0',
				),
				'selectors' => array(
					/**
					 * .editor:met_color
					 */
					'.onepage_scroll_nav .editor\:met_color' => 'color: {{VALUE}};',

					/**
					 * .editor:met_bgc
					 * .editor:before:met_bgc
					 */
					'.onepage_scroll_nav .editor\:met_bgc, .onepage_scroll_nav .editor\:before\:met_bgc:before' => 'background-color: {{VALUE}};',

					/**
					 * .editor:met_bdc
					 * .editor:active:met_bdc
					 */
					'.onepage_scroll_nav .editor\:met_bdc, .onepage_scroll_nav .active > .editor\:active\:met_bdc' => 'border-color: {{VALUE}};',
				),
			)
		);

		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'ekit_onepagescroll_nav_tooltip_font',
				'label'     => esc_html__( 'Tooltip Typography', 'elementskit-lite' ),
				'selector'  => '.onepage_scroll_nav .nav_tooltip',
				'condition' => array(
					'ekit_onepagescroll'      => 'block',
					'ekit_onepagescroll_nav!' => '0',
				),
			)
		);
	}


	/**
	 * Pro Section Controls
	 */
	public function pro_section_controls( $element ) {
		$element->add_control(
			'ekit_has_onepagescroll_dot',
			array(
				'label'              => esc_html__( 'Enable Dot', 'elementskit-lite' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'default'            => 'yes',
				'render_type'        => 'none',
			)
		);
		$element->add_control(
			'ekit_onepagescroll_tooltip_text',
			array(
				'label'              => esc_html__( 'Tooltip Text', 'elementskit-lite' ),
				'type'               => Controls_Manager::TEXT,
				'frontend_available' => true,
				'render_type'        => 'none',
			)
		);
	}
}
