<?php

namespace Elementor;
use \Elementor\ElementsKit_Widget_Nav_Menu_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Nav_Menu extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;

    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );
        $this->add_script_depends('ekit-nav-menu');
    }

    public function get_name() {
        return Handler::get_name();
    }

    public function get_title() {
        return Handler::get_title();
    }

    public function get_icon() {
        return Handler::get_icon();
    }

    public function get_categories() {
        return Handler::get_categories();
    }

    public function get_keywords() {
        return Handler::get_keywords();
    }

    public function get_help_url() {
        return 'https://wpmet.com/doc/nav-menu/';
    }

    public function get_menus(){
        $list = [];
        $menus = wp_get_nav_menus();
        foreach($menus as $menu){
            $list[$menu->slug] = $menu->name;
        }

        return $list;
    }

    protected function register_controls() {

        $this->start_controls_section(
            'elementskit_content_tab',
            [
                'label' => esc_html__('Menu Settings', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'elementskit_nav_menu',
            [
                'label'     => esc_html__( 'Select menu', 'elementskit-lite' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => $this->get_menus(),
            ]
        );

        $this->add_responsive_control(
            'elementskit_main_menu_position',
            [
                'label' => esc_html__( 'Horizontal menu position', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'elementskit-menu-po-left',
                'options' => [
                    'elementskit-menu-po-left'  => esc_html__( 'Left', 'elementskit-lite' ),
                    'elementskit-menu-po-center' => esc_html__( 'Center', 'elementskit-lite' ),
                    'elementskit-menu-po-right' => esc_html__( 'Right', 'elementskit-lite' ),
                    'elementskit-menu-po-justified'  => esc_html__( 'Justified', 'elementskit-lite' ),
                ],
            ]
        );

        $this->add_control(
            'elementskit_nav_dropdown_as',
            [
                'label' => esc_html__( 'Dropdown open as', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'ekit-nav-dropdown-hover',
                'options' => [
                    'ekit-nav-dropdown-hover'  => esc_html__( 'Hover', 'elementskit-lite' ),
                    'ekit-nav-dropdown-click' => esc_html__( 'Click', 'elementskit-lite' ),
                ],
            ]
        );

		if(\ElementsKit_Lite::license_status() === 'valid') {
			$this->add_control(
				'elementskit_submenu_indicator_icon',
				[
					'label' => esc_html__( 'Dropdown Indicator Icon', 'elementskit-lite' ),
					'type' => Controls_Manager::ICONS,
					'skin' => 'inline',
					'exclude_inline_options' => ['svg'],
					'skin_settings' => [
						'inline' => [
							'none' => [
								'label' => esc_html__( 'Default', 'elementskit-lite' ),
								'icon' => 'icon icon-down-arrow1',
							],
							'icon' => [
								'label' => esc_html__( 'Icon Library', 'elementskit-lite' ),
								'icon' => 'fas fa-external-link-alt',
							],
						],
					],
					'recommended' => [
						'ekiticons' => [
							'down-arrow1',
							'arrow-point-to-down',
							'plus',
							'link',
						],
						'fa-solid' => [
							'plus',
							'external-link-alt',
							'link',
							'angle-down',
						],
					],
					'label_block' => false,
				]
			);
		} else {
			$this->add_control(
				'elementskit_style_tab_submenu_item_arrow',
				[
					'label' => esc_html__( 'Submenu Indicator', 'elementskit-lite' ),
					'type'  => Controls_Manager::SELECT,
					'default' => 'elementskit_line_arrow',
					'options' => [
						'elementskit_line_arrow'    => esc_html__( 'Line Arrow', 'elementskit-lite' ),
						'elementskit_plus_icon'     => esc_html__( 'Plus', 'elementskit-lite' ),
						'elementskit_fill_arrow'    => esc_html__( 'Fill Arrow', 'elementskit-lite' ),
						'elementskit_none'          => esc_html__( 'None', 'elementskit-lite' ),
					],
				]
			);
		}

        $this->add_control(
            'elementskit_one_page_enable',
            [
                'label' => esc_html__('Enable one page? ', 'elementskit-lite'),
                'description'	=> esc_html__('This works in the current page.', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'elementskit_responsive_breakpoint',
            [
                'label' => __( 'Responsive Breakpoint', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'ekit_menu_responsive_tablet',
                'options' => [
                    'ekit_menu_responsive_tablet'  => __( 'Tablet', 'elementskit-lite' ),
                    'ekit_menu_responsive_mobile' => __( 'Mobile', 'elementskit-lite' ),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'elementskit_mobile_menu',
            [
                'label' => esc_html__('Mobile Menu Settings', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'elementskit_nav_menu_logo',
            [
                'label' => esc_html__( 'Mobile Menu Logo', 'elementskit-lite' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => '', //Utils::get_placeholder_image_src() -- removed for conflict with jetpack
                    'id'    => -1
                ],
            ]
        );

        $this->add_control(
            'elementskit_nav_menu_logo_link_to',
            [
                'label' => esc_html__( 'Menu link', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'home',
                'options' => [
                    'home' => esc_html__( 'Default(Home)', 'elementskit-lite' ),
                    'custom' => esc_html__( 'Custom URL', 'elementskit-lite' ),
                ],
            ]
        );

        $this->add_control(
            'elementskit_nav_menu_logo_link',
            [
                'label' => esc_html__( ' Custom Link', 'elementskit-lite' ),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => 'https://wpmet.com',
                'condition' => [
                    'elementskit_nav_menu_logo_link_to' => 'custom',
                ],
                'show_label' => false,

            ]
        );

        $this->add_control(
            'elementskit_hamburger_icon',
            [
                'label' => __( 'Hamburger Icon (Optional)', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'submenu_click_area',
            [
                'label'         => esc_html__('Submenu Click Area', 'elementskit-lite'),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__('Icon', 'elementskit-lite'),
                'label_off'     => esc_html__('Text', 'elementskit-lite'),
                'return_value'  => 'icon',
                'default'       => 'icon',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'elementskit_menu_style_tab',
            [
                'label' => esc_html__('Menu Wrapper', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'elementskit_menubar_height',
            [
                'label' => esc_html__( 'Menu Height', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 30,
                        'max' => 300,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop' ],
                'desktop_default' => [
                    'size' => 80,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-container' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'elementskit_menu_wrap_h',
            [
                'label' => esc_html__( 'Menu wrapper background', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'elementskit_menubar_background',
                'label' => esc_html__( 'Menu Panel Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'devices' => [ 'desktop' ],
                'selector' => '{{WRAPPER}} .elementskit-menu-container',
            ]
        );

        $this->add_responsive_control(
            'wrapper_color_mobile',
            [
                'label'     => esc_html__( 'Mobile Wrapper Background', 'elementskit-lite' ),
                'type'      => Controls_Manager::COLOR,
                'devices'   => ['desktop', 'tablet', 'mobile'],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-container'   => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_mobile_menu_panel_spacing',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'tablet_default' => [
                    'top' => '10',
                    'right' => '0',
                    'bottom' => '10',
                    'left' => '0',
                    'unit' => 'px',
                ],
                'devices' => ['desktop', 'tablet'],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-nav-identity-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_mobile_menu_panel_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'range' => [
                    'px' => [
                        'min' => 350,
                        'max' => 700,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'tablet_default' => [
                    'size' => 350,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-container' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_border_radius',
            [
                'label' => esc_html__( 'Menu border radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
                'separator' => [ 'before' ],
                'desktop_default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_menu_item_icon_spacing',
            [
                'label' => esc_html__( 'Menu Icon Spacing', 'elementskit-lite' ),
                'description' => esc_html__( 'This is only work with Mega menu icon option', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav li a .ekit-menu-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();


        $this->start_controls_section(
            'elementskit_style_tab_menuitem',
            [
                'label' => esc_html__('Menu item style', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );



        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'elementskit_content_typography',
                'label' => esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-navbar-nav > li > a',
            ]
        );



        $this->add_control(
            'elementskit_menu_item_h',
            [
                'label' => esc_html__( 'Menu Item Style', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->start_controls_tabs(
            'elementskit_nav_menu_tabs'
        );
        // Normal
        $this->start_controls_tab(
            'elementskit_nav_menu_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'elementskit_item_background',
                'label' => esc_html__( 'Item background', 'elementskit-lite' ),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .elementskit-navbar-nav > li > a',
            ]
        );

        $this->add_responsive_control(
            'elementskit_menu_text_color',
            [
                'label' => esc_html__( 'Item text color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'desktop_default' => '#000000',
                'tablet_default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav > li > a' => 'color: {{VALUE}}',
                ],
            ]
        );
	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'  => 'elementskit_menu_text_border',
				'selector'  => '{{WRAPPER}} .elementskit-navbar-nav > li > a',
				'size_units'  => ['px'],
			]
		);

		$this->add_control(
			'elementskit_menu_text_border_radius',
			[
				'label'      => esc_html__('Border Radius (px)', 'elementskit-lite'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .elementskit-navbar-nav > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab(
            'elementskit_nav_menu_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'elementskit_item_background_hover',
                'label' => esc_html__( 'Item background', 'elementskit-lite' ),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .elementskit-navbar-nav > li > a:hover, {{WRAPPER}} .elementskit-navbar-nav > li > a:focus, {{WRAPPER}} .elementskit-navbar-nav > li > a:active, {{WRAPPER}} .elementskit-navbar-nav > li:hover > a',
            ]
        );

        $this->add_responsive_control(
            'elementskit_item_color_hover',
            [
                'label' => esc_html__( 'Item text color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav > li > a:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav > li > a:focus' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav > li > a:active' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav > li:hover > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav > li:hover > a .elementskit-submenu-indicator' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav > li > a:hover .elementskit-submenu-indicator' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav > li > a:focus .elementskit-submenu-indicator' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav > li > a:active .elementskit-submenu-indicator' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'  => 'elementskit_menu_text_border_hover',
				'selector'  => '{{WRAPPER}} .elementskit-navbar-nav > li:hover > a',
				'size_units'  => ['px'],
			]
		);

		$this->add_control(
			'elementskit_menu_text_border_radius_hover',
			[
				'label'      => esc_html__('Border Radius (px)', 'elementskit-lite'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .elementskit-navbar-nav > li:hover > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        // active
        $this->start_controls_tab(
            'elementskit_nav_menu_active_tab',
            [
                'label' => esc_html__( 'Active', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'		=> 'elementskit_nav_menu_active_bg_color',
                'label' 	=> esc_html__( 'Item background', 'elementskit-lite' ),
                'types'		=> ['classic', 'gradient'],
                'selector'	=> '{{WRAPPER}} .elementskit-navbar-nav > li.current-menu-item > a,{{WRAPPER}} .elementskit-navbar-nav > li.current-menu-ancestor > a'
            ]
        );

        $this->add_responsive_control(
            'elementskit_nav_menu_active_text_color',
            [
                'label' => esc_html__( 'Item text color (Active)', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav > li.current-menu-item > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav > li.current-menu-ancestor > a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav > li.current-menu-ancestor > a .elementskit-submenu-indicator' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'  => 'elementskit_menu_text_border_active',
				'selector'  => '{{WRAPPER}} .elementskit-navbar-nav > li.current-menu-item > a',
				'size_units'  => ['px'],
			]
		);

		$this->add_control(
			'elementskit_menu_text_border_radius_active',
			[
				'label'      => esc_html__('Border Radius (px)', 'elementskit-lite'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .elementskit-navbar-nav > li.current-menu-item > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'elementskit_menu_item_spacing',
            [
                'label' => esc_html__( 'Item Spacing', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'separator' => [ 'before' ],
                'desktop_default' => [
                    'top' => 0,
                    'right' => 15,
                    'bottom' => 0,
                    'left' => 15,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'top' => 10,
                    'right' => 15,
                    'bottom' => 10,
                    'left' => 15,
                    'unit' => 'px',
                ],
                'size_units' => [ 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_menu_item_margin',
            [
                'label' => esc_html__( 'Item Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav > li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

		$this->start_controls_section(
			'elementskit_style_tab_submenu_indicator',
			[
				'label' => esc_html__('Submenu indicator style', 'elementskit-lite'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_submenu_indicator_font_size',
			[
				'label' => esc_html__( 'Font Size', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-navbar-nav > li > a .elementskit-submenu-indicator' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-navbar-nav > li > a .ekit-submenu-indicator-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'elementskit_style_tab_submenu_indicator_color',
			[
				'label' => esc_html__( 'Indicator color', 'elementskit-lite' ),
				'type'  => Controls_Manager::COLOR,
				'default'   =>  '#101010',
				'alpha'     => false,
				'selectors' => [
					'{{WRAPPER}} .elementskit-navbar-nav > li > a .elementskit-submenu-indicator' => 'color: {{VALUE}}; fill: {{VALUE}}',
					'{{WRAPPER}} .elementskit-navbar-nav > li > a .ekit-submenu-indicator-icon' => 'color: {{VALUE}}; fill: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_submenu_indicator_spacing',
			[
				'label' => esc_html__( 'Indicator Margin (px)', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .elementskit-navbar-nav-default .elementskit-dropdown-has>a .elementskit-submenu-indicator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-navbar-nav-default .elementskit-dropdown-has>a .ekit-submenu-indicator-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'elementskit_style_tab_submenu_item',
            [
                'label' => esc_html__('Submenu item style', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'elementskit_menu_item_typography',
                'label' => esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li > a',
            ]
        );

        $this->add_responsive_control(
            'elementskit_submenu_item_spacing',
            [
                'label' => esc_html__( 'Spacing', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'devices' => ['desktop', 'tablet'],
                'desktop_default' => [
                    'top' => 15,
                    'right' => 15,
                    'bottom' => 15,
                    'left' => 15,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'top' => 15,
                    'right' => 15,
                    'bottom' => 15,
                    'left' => 15,
                    'unit' => 'px',
                ],
                'size_units' => [ 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'elementskit_submenu_active_hover_tabs'
        );
        $this->start_controls_tab(
            'elementskit_submenu_normal_tab',
            [
                'label'	=> esc_html__('Normal', 'elementskit-lite')
            ]
        );

        $this->add_responsive_control(
            'elementskit_submenu_item_color',
            [
                'label' => esc_html__( 'Item text color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li > a' => 'color: {{VALUE}}',
                ],

            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'elementskit_menu_item_background',
                'label' => esc_html__( 'Item background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li > a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'elementskit_submenu_hover_tab',
            [
                'label'	=> esc_html__('Hover', 'elementskit-lite')
            ]
        );

        $this->add_responsive_control(
            'elementskit_item_text_color_hover',
            [
                'label' => esc_html__( 'Item text color (hover)', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li > a:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li > a:focus' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li > a:active' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li:hover > a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'elementskit_menu_item_background_hover',
                'label' => esc_html__( 'Item background (hover)', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '
					{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li > a:hover,
					{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li > a:focus,
					{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li > a:active,
					{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li:hover > a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'elementskit_submenu_active_tab',
            [
                'label'	=> esc_html__('Active', 'elementskit-lite')
            ]
        );

        $this->add_responsive_control(
            'elementskit_nav_sub_menu_active_text_color',
            [
                'label' => esc_html__( 'Item text color (Active)', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#707070',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li.current-menu-item > a' => 'color: {{VALUE}} !important'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'		=> 'elementskit_nav_sub_menu_active_bg_color',
                'label' 	=> esc_html__( 'Item background (Active)', 'elementskit-lite' ),
                'types'		=> ['classic', 'gradient'],
                'selector'	=> '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li.current-menu-item > a',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'elementskit_menu_item_border_heading',
            [
                'label' => esc_html__( 'Sub Menu Items Border', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'elementskit_menu_item_border',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li > a',
            ]
        );

        $this->add_control(
            'elementskit_menu_item_border_last_child_heading',
            [
                'label' => esc_html__( 'Border Last Child', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'elementskit_menu_item_border_last_child',
                'label' => esc_html__( 'Border last Child', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li:last-child > a',
            ]
        );

        $this->add_control(
            'elementskit_menu_item_border_first_child_heading',
            [
                'label' => esc_html__( 'Border First Child', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'elementskit_menu_item_border_first_child',
                'label' => esc_html__( 'Border First Child', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel > li:first-child > a',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'elementskit_style_tab_submenu_panel',
            [
                'label' => esc_html__('Submenu panel style', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
			'sub_panel_padding',
			[
				'label'         => esc_html__('Padding', 'elementskit-lite'),
                'type'          => Controls_Manager::DIMENSIONS,
                'default'       => [
                    'top'       => '15',
                    'bottom'    => '15',
                    'left'      => '0',
                    'right'     => '0',
                    'isLinked'  => false,
                ],
				'selectors'     => [
					'{{WRAPPER}} .elementskit-submenu-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'elementskit_panel_submenu_border',
                'label' => esc_html__( 'Panel Menu Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'elementskit_submenu_container_background',
                'label' => esc_html__( 'Container background', 'elementskit-lite' ),
                'types' => [ 'classic','gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel',
            ]
        );

        $this->add_responsive_control(
            'elementskit_submenu_panel_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'desktop_default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'size_units' => [ 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_submenu_container_width',
            [
                'label' => esc_html__( 'Conatiner width', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'devices' => [ 'desktop' ],
                'desktop_default' => '220px',
                'tablet_default' => '200px',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel' => 'min-width: {{VALUE}};',
                ]
            ]
        );


        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'elementskit_panel_box_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-navbar-nav .elementskit-submenu-panel',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'elementskit_menu_toggle_style_tab',
            [
                'label' => esc_html__( 'Hamburger Style', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'elementskit_menu_toggle_style_title',
            [
                'label' => esc_html__( 'Hamburger Toggle', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'elementskit_menu_toggle_icon_position',
            [
                'label' => esc_html__( 'Position', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Top', 'elementskit-lite' ),
                        'icon' => 'fa fa-angle-left',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Middle', 'elementskit-lite' ),
                        'icon' => 'fa fa-angle-right',
                    ],
                ],
                'default' => 'right',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-hamburger' => 'float: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_menu_toggle_spacing',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'top' => '8',
                    'right' => '8',
                    'bottom' => '8',
                    'left' => '8',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-hamburger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_menu_toggle_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 45,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 45,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-hamburger' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_menu_toggle_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-hamburger' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_menu_open_typography',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 15,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-hamburger > .ekit-menu-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'elementskit_hamburger_icon[value]!'    => '',
                ],
            ]
        );

        $this->start_controls_tabs(
            'elementskit_menu_toggle_normal_and_hover_tabs'
        );

        $this->start_controls_tab(
            'elementskit_menu_toggle_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'elementskit_menu_toggle_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic' ],
                'selector' => '{{WRAPPER}} .elementskit-menu-hamburger',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'elementskit_menu_toggle_border',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .elementskit-menu-hamburger',
            ]
        );

        $this->add_control(
            'elementskit_menu_toggle_icon_color',
            [
                'label' => esc_html__( 'Hamburger Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.5)',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-hamburger .elementskit-menu-hamburger-icon' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-menu-hamburger > .ekit-menu-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'elementskit_menu_toggle_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'elementskit_menu_toggle_background_hover',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic' ],
                'selector' => '{{WRAPPER}} .elementskit-menu-hamburger:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'elementskit_menu_toggle_border_hover',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .elementskit-menu-hamburger:hover',
            ]
        );

        $this->add_control(
            'elementskit_menu_toggle_icon_color_hover',
            [
                'label' => esc_html__( 'Hamburger Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.5)',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-hamburger:hover .elementskit-menu-hamburger-icon' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-menu-hamburger:hover > .ekit-menu-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();


        $this->add_control(
            'elementskit_menu_close_style_title',
            [
                'label' => esc_html__( 'Close Toggle', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'elementskit_menu_close_typography',
                'label' => esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-menu-close',
            ]
        );

        $this->add_responsive_control(
            'elementskit_menu_close_spacing',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'top' => '8',
                    'right' => '8',
                    'bottom' => '8',
                    'left' => '8',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_menu_close_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', ],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'top' => '12',
                    'right' => '12',
                    'bottom' => '12',
                    'left' => '12',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_menu_close_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 45,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 45,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-close' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_menu_close_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => ['desktop', 'tablet'],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-close' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
            'elementskit_menu_close_normal_and_hover_tabs'
        );

        $this->start_controls_tab(
            'elementskit_menu_close_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'elementskit_menu_close_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic' ],
                'selector' => '{{WRAPPER}} .elementskit-menu-close',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'elementskit_menu_close_border',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .elementskit-menu-close',
            ]
        );

        $this->add_control(
            'elementskit_menu_close_icon_color',
            [
                'label' => esc_html__( 'Hamburger Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(51, 51, 51, 1)',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-close' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'elementskit_menu_close_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'elementskit_menu_close_background_hover',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic' ],
                'selector' => '{{WRAPPER}} .elementskit-menu-close:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'elementskit_menu_close_border_hover',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .elementskit-menu-close:hover',
            ]
        );

        $this->add_control(
            'elementskit_menu_close_icon_color_hover',
            [
                'label' => esc_html__( 'Hamburger Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.5)',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-menu-close:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'elementskit_mobile_menu_logo_style_tab',
            [
                'label' => esc_html__( 'Mobile Menu Logo', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'elementskit_mobile_menu_logo_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 5,
                    ],
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 160,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 120,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-nav-logo > img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_mobile_menu_logo_height',
            [
                'label' => esc_html__( 'Height', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 1,
                    ],
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 60,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-nav-logo > img' => 'max-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_mobile_menu_logo_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'tablet_default' => [
                    'top' => '5',
                    'right' => '0',
                    'bottom' => '5',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => 'false',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-nav-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'elementskit_mobile_menu_logo_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'tablet_default' => [
                    'top' => '5',
                    'right' => '5',
                    'bottom' => '5',
                    'left' => '5',
                    'unit' => 'px',
                    'isLinked' => 'true',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-nav-logo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->insert_pro_message();
    }

    protected function render( ) {
        $settings = $this->get_settings_for_display();

        // Return if menu not selected
        if(empty($settings['elementskit_nav_menu'])) {
            return;
        }

        $hamburger_icon_value = '';
        $hamburger_icon_type = '';
        if ($settings['elementskit_hamburger_icon'] != '' && $settings['elementskit_hamburger_icon']) {
            if ($settings['elementskit_hamburger_icon']['library'] !== 'svg') {
                $hamburger_icon_value = esc_attr($settings['elementskit_hamburger_icon']['value']);
                $hamburger_icon_type = esc_attr('icon');
            } else {
                $hamburger_icon_value = esc_url($settings['elementskit_hamburger_icon']['value']['url']);
                $hamburger_icon_type = esc_attr('url');
            }
        }

        // Responsive menu breakpoint
        $responsive_menu_breakpoint = '';
        if ($settings['elementskit_responsive_breakpoint'] === 'ekit_menu_responsive_tablet') {
            $responsive_menu_breakpoint = "1024";
        } else {
            $responsive_menu_breakpoint = "767";
        }

        echo '<div class="ekit-wid-con '.esc_attr($settings['elementskit_responsive_breakpoint']).'" data-hamburger-icon="'.esc_attr($hamburger_icon_value).'" data-hamburger-icon-type="'.esc_attr($hamburger_icon_type).'" data-responsive-breakpoint="'.esc_attr($responsive_menu_breakpoint).'">';
        $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {
        $settings = $this->get_settings_for_display();

        if($settings['elementskit_nav_menu'] != '' && wp_get_nav_menu_items($settings['elementskit_nav_menu']) !== false && count(wp_get_nav_menu_items($settings['elementskit_nav_menu'])) > 0){
            /**
             * Hamburger Toggler Button
             */
            ?>
            <button class="elementskit-menu-hamburger elementskit-menu-toggler"  type="button" aria-label="hamburger-icon">
                <?php
                /**
                 * Show Default Icon
                 */
                if ( $settings['elementskit_hamburger_icon']['value'] === '' ):
                ?>
                    <span class="elementskit-menu-hamburger-icon"></span><span class="elementskit-menu-hamburger-icon"></span><span class="elementskit-menu-hamburger-icon"></span>
                <?php
                endif;
                
                /**
                 * Show Icon or, SVG
                 */
                Icons_Manager::render_icon( $settings['elementskit_hamburger_icon'], [ 'aria-hidden' => 'true', 'class' => 'ekit-menu-icon' ] );
                ?>
            </button>
            <?php

            /**
             * Main Menu Container
             */
            $link = $target = $nofollow = '';

            if (isset($settings['elementskit_nav_menu_logo_link_to']) && $settings['elementskit_nav_menu_logo_link_to'] == 'home') {
                $link = get_home_url();
            }elseif(isset($settings['elementskit_nav_menu_logo_link'])){
                $link = $settings['elementskit_nav_menu_logo_link']['url'];
                $target = ($settings['elementskit_nav_menu_logo_link']['is_external'] != "on" ? "" : "_blank");
                $nofollow = ($settings['elementskit_nav_menu_logo_link']['nofollow'] != "on" ? "" : "nofollow");
            }

            $metadata = \ElementsKit_Lite\Utils::img_meta(esc_attr($settings['elementskit_nav_menu_logo']['id']));
			$markup = '<div class="elementskit-nav-identity-panel">';
			// Use an if statement to conditionally display the site logo
			if (!empty($settings['elementskit_nav_menu_logo']['id'])) : 
				$markup .= '
				<div class="elementskit-site-title">
					<a class="elementskit-nav-logo" href="'.esc_url($link).'" target="'.(!empty($target) ? esc_attr($target) : '_self').'" rel="'.esc_attr($nofollow).'">
						'. \Elementskit_Lite\Utils::get_attachment_image_html($settings, 'elementskit_nav_menu_logo', 'full') .'
					</a> 
				</div>';
			endif;
			$markup .= '<button class="elementskit-menu-close elementskit-menu-toggler" type="button">X</button></div>';
		

			$container_classes = [
				'elementskit-menu-container elementskit-menu-offcanvas-elements elementskit-navbar-nav-default',
				'ekit-nav-menu-one-page-' . $settings['elementskit_one_page_enable'],
				!empty($settings['elementskit_nav_dropdown_as']) ? $settings['elementskit_nav_dropdown_as'] : 'ekit-nav-dropdown-hover',
			];

			$args = [
				'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>' . $markup,
				'container'       => 'div',
				'container_id'    => 'ekit-megamenu-' . $settings['elementskit_nav_menu'],
				'container_class' => join(' ', $container_classes),
				'menu'         	  => $settings['elementskit_nav_menu'],
				'menu_class'      => 'elementskit-navbar-nav ' . $settings['elementskit_main_menu_position'] .' submenu-click-on-'. $settings['submenu_click_area'],
				'depth'           => 4,
				'echo'            => true,
				'fallback_cb'     => 'wp_page_menu',
				'walker'          => (class_exists('\ElementsKit_Lite\ElementsKit_Menu_Walker') ? new \ElementsKit_Lite\ElementsKit_Menu_Walker() : '' )
			];

			// set submenu indicator icon
			$args['submenu_indicator_icon'] = $this->get_indicator_icon($settings);
			
			// WP 6.1 submenu issue
			if(version_compare(get_bloginfo('version'), '6.1', '>=')){
				unset($args['depth']);
			}

			wp_nav_menu($args);

			/**
			 * Mobile Menu Overlay
			 */
			?>
			
			<div class="elementskit-menu-overlay elementskit-menu-offcanvas-elements elementskit-menu-toggler ekit-nav-menu--overlay"></div><?php


			/**
			 * Editor: Widget Empty Fallback on Responsive View
			 */
			if ( Plugin::$instance->editor->is_edit_mode() ) : ?>
				<span class="ekit-nav-menu--empty-fallback">&nbsp;</span>
			<?php endif;
		}
	}

	protected function get_indicator_icon($settings) {
		extract($settings);

		$icon_html = '';
		$indicator_class = 'elementskit-submenu-indicator';

		// if ElementsKit Pro activate and licenced is activated
		if (\ElementsKit_Lite::license_status() === 'valid') {
			if(!empty($elementskit_submenu_indicator_icon['value'])) {
				return Icons_Manager::try_get_icon_html($settings['elementskit_submenu_indicator_icon'], ['class' => $indicator_class, 'aria-hidden' => 'true']);
			}
		} elseif(!empty($elementskit_style_tab_submenu_item_arrow)) {
			$icon_class_map = [
				'elementskit_line_arrow' => 'icon-down-arrow1',
				'elementskit_plus_icon' => 'icon-plus',
				'elementskit_fill_arrow' => 'icon-arrow-point-to-down',
				'elementskit_none' => ''
			];

			$selected_arrow = $elementskit_style_tab_submenu_item_arrow;

			if (isset($icon_class_map[$selected_arrow])) {
				return sprintf('<i aria-hidden="true" class="icon %1$s %2$s"></i>', $icon_class_map[$selected_arrow], $indicator_class);
			}
		}

		return $icon_html;
	}
}
