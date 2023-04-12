<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Client_Logo_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Client_Logo extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;
    
    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		// $this->add_script_depends('ekit-slick'); // deprecated
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
        return Handler::get_categories();
    }

    public function get_help_url() {
        return 'https://wpmet.com/doc/client-logo/';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'ekit_client_logo_section_client',
            [
                'label' => esc_html__( 'Logo', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
			'ekit_client_logo_slide_style',
			[
				'label' => esc_html__( 'Slide Style ', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'simple_logo_image',
				'options' => [
					'simple_logo_image'  => esc_html__( 'Simple', 'elementskit-lite' ),
					'banner_logo_image' => esc_html__( 'Banner', 'elementskit-lite' ),
				],
			]
		);

        $repeater = new Repeater();

        $repeater->add_control(
            'ekit_client_logo_list_title', [
                'label' => esc_html__( 'Client Name', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'default' => esc_html__( 'List Title' , 'elementskit-lite' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'ekit_client_logo_image_normal',
            [
                'label' => esc_html__( 'Client Logo', 'elementskit-lite' ),
                'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                    'id'    => -1
                ],
            ]
        );

        $repeater->add_control(
            'ekit_client_logo_enable_hover_logo',
            [
                'label' => esc_html__( 'Enable Hover Logo', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $repeater->add_control(
            'ekit_client_logo_image_hover',
            [
                'label' => esc_html__( 'Hover Logo', 'elementskit-lite' ),
                'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                    'id'    => -1
                ],
                'condition' => [
                    'ekit_client_logo_enable_hover_logo' => 'yes'
                ]
            ]
        );

        $repeater->add_control(
            'ekit_client_logo_enable_link',
            [
                'label' => esc_html__( 'Enable Link', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => 'yes',
            ]
        );

        $repeater->add_control(
            'ekit_client_logo_website_link',
            [
                'label' => esc_html__( 'Link', 'elementskit-lite' ),
                'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
                'placeholder' => esc_html__( 'https://wpmet.com', 'elementskit-lite' ),
                'show_external' => true,
                'condition' => [
                    'ekit_client_logo_enable_link' => 'yes'
                ],
            ]
        );


        $this->add_control(
            'ekit_client_logo_repiter',
            [
                'label' => esc_html__( 'Repeater List', 'elementskit-lite' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'ekit_client_logo_list_title' => esc_html__( 'Title #1', 'elementskit-lite' ),
                    ],
                    [
                        'ekit_client_logo_list_title' => esc_html__( 'Title #2', 'elementskit-lite' ),
					],
					[
                        'ekit_client_logo_list_title' => esc_html__( 'Title #3', 'elementskit-lite' ),
                    ],
					[
                        'ekit_client_logo_list_title' => esc_html__( 'Title #4', 'elementskit-lite' ),
                    ],
					[
                        'ekit_client_logo_list_title' => esc_html__( 'Title #5', 'elementskit-lite' ),
                    ],
                ],
                'title_field' => '{{{ ekit_client_logo_list_title }}}',
            ]
        );

        $this->end_controls_section();

        // setting section

        $this->start_controls_section(
            'ekit_client_logo_slider_settings',
            [
                'label' => esc_html__( 'Settings', 'elementskit-lite' ),
            ]
        );

		$this->add_responsive_control(
			'ekit_client_logo_left_right_spacing',
			[
				'label' => esc_html__( 'Spacing Left Right', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 15,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'default' => [
					'size' => 15,
					'unit' => 'px',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-slide' => 'margin-right: {{SIZE}}{{UNIT}};margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-clients-slider' => '--ekit_client_logo_left_right_spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_client_logo_top_bottom_spacing',
			[
				'label' => esc_html__( 'Spacing Top Bottom', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 15,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'default' => [
					'size' => 15,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-slide > div' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-clients-slider .slick-list' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_client_logo_slidetosho',
			[
				'label' => esc_html__( 'Slides To Show', 'elementskit-lite' ),
                'type' =>  Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 4,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 2,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'default' => [
					'size' => 4,
					'unit' => 'px',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .ekit-price-card-slider' => '--ekit_client_logo_slidetosho:  {{SIZE}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_client_logo_slidesToScroll',
			[
				'label' => esc_html__( 'Slides To Scroll', 'elementskit-lite' ),
                'type' =>  Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'default' => [
					'size' => 1,
					'unit' => 'px',
				],
			]
		);



		$this->add_control(
			'ekit_client_logo_autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'elementskit-lite' ),
				'type' =>  Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
        $this->add_control(
            'ekit_client_logo_speed',
            [
                'label' => esc_html__( 'Speed (ms)', 'elementskit-lite' ),
                'type' =>  Controls_Manager::NUMBER,
                'min' => 1000,
                'max' => 15000,
                'step' => 100,
                'default' => 1000,
                'condition' => [
                    'ekit_client_logo_autoplay' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'ekit_client_logo_pause_on_hover',
            [
                'label' => esc_html__( 'Pause on Hover', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'ekit_client_logo_autoplay' => 'yes',
                ]
            ]
        );
        $this->add_control(
			'ekit_client_logo_show_arrow',
			[
				'label' => esc_html__( 'Show arrow', 'elementskit-lite' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => '',
			]
        );
        $this->add_control(
			'ekit_client_logo_loop',
			[
				'label' => esc_html__( 'Enable Loop?', 'elementskit-lite' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => '',
			]
        );
        $this->add_control(
            'ekit_client_logo_left_arrow_icon',
            [
                'label' => esc_html__( 'Left arrow Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_client_logo_left_arrow',
                'default' => [
                    'value' => 'icon icon-left-arrow2',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_client_logo_show_arrow' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'ekit_client_logo_right_arrow_icon',
            [
                'label' => esc_html__( 'Right arrow Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_client_logo_right_arrow',
                'default' => [
                    'value' => 'icon icon-right-arrow2',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_client_logo_show_arrow' => 'yes',
                ]
            ]
        );
        $this->add_control(
			'ekit_client_logo_show_dot',
			[
				'label' => esc_html__( 'Show dots', 'elementskit-lite' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => '',
			]
        );

        $this->add_control(
            'ekit_client_logo_additional_option_heading',
            [
                'label' => esc_html__( 'Additional Options', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'ekit_client_logo_rows',
			[
				'label' => esc_html__( 'Rows', 'elementskit-lite' ),
				'description' => esc_html__( 'Setting this to more than 1 initializes grid mode. Use slidesPerRow to set how many slides should be in each row.
				', 'elementskit-lite' ) ,
                'type' => Controls_Manager::SELECT,
				'default' => 1,
                'options' => [
                    '1'  => esc_html__( 'One row', 'elementskit-lite' ),
                    '2' => esc_html__( 'Two row', 'elementskit-lite' ),
                    '3' => esc_html__( 'Three row', 'elementskit-lite' ),
                ],
			]
		);
        $this->add_control(
            'ekit_client_logo_separator',
            [
                'label' => esc_html__( 'Show Separator', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
                'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
			'ekit_client_logo_container_style_tab',
			[
				'label' => esc_html__( 'Container', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_client_logo_container_bg_color',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-clients-slider .slick-list'
			]
		);

		$this->add_responsive_control(
			'ekit_client_logo_container_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_client_logo_container_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->add_responsive_control(
			'ekit_client_logo_container_min_height',
			[
				'label' => esc_html__( 'Min Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .single-client' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

        // style tab
        // Logo

        $this->start_controls_section(
            'ekit_client_logo_image_style',
            [
                'label' => esc_html__( 'Logo', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    // 'ekit_client_logo_slide_style' => 'simple_logo_image',
                ]
            ]
        );

        $this->start_controls_tabs('ekit_client_logo_image_style_tabs');

		$this->start_controls_tab(
			'ekit_client_logo_image_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit-lite' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_client_logo_client_logo_background_group',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-clients-slider .single-client',
			]
		);



		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_client_logo_image_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit-lite' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_client_logo_background_hover_group',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-clients-slider.banner_logo_image .single-client:before, {{WRAPPER}} .elementskit-clients-slider.hover-bg-gradient .single-client:before',
				'condition' => [
					'ekit_client_logo_slide_style' => 'banner_logo_image'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_client_logo_background_simple_hover_group',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .elementskit-clients-slider .single-client:hover',
				'condition' => [
					'ekit_client_logo_slide_style' => 'simple_logo_image'
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

        $this->add_responsive_control(
            'ekit_client_logo_image_style_border_radious',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-clients-slider .single-client' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_client_logo_hover_animation_driction',
            [
                'label' => esc_html__( 'Overlay Direction', 'elementskit-lite' ),
                'type' =>   Controls_Manager::CHOOSE,
                'options' => [
                    'hover_from_left' => [
                        'title' => esc_html__( 'From Left', 'elementskit-lite' ),
                        'icon' => 'fa fa-caret-right',
                    ],
                    'hover_from_top' => [
                        'title' => esc_html__( 'From Top', 'elementskit-lite' ),
                        'icon' => 'fa fa-caret-down',
                    ],
                    'hover_from_bottom' => [
                        'title' => esc_html__( 'From Bottom', 'elementskit-lite' ),
                        'icon' => 'fa fa-caret-up',
                    ],
                    'hover_from_right' => [
                        'title' => esc_html__( 'From Right', 'elementskit-lite' ),
                        'icon' => 'fa fa-caret-left',
                    ],

                ],
                'default' => 'hover_from_bottom',
                'toggle' => true,
                'condition'  => [
                    'ekit_client_logo_slide_style' => 'banner_logo_image'
                ]
            ]
        );


		$this->add_group_control(
        Group_Control_Background::get_type(),
            array(
                'name'     => 'ekit_client_logo_hover_animation_color',
				'label' => esc_html__( 'Hover Background', 'elementskit-lite' ),
                'default' => '',
                'selector' => '{{WRAPPER}} .elementskit-clients-slider.banner_logo_image .single-client:before',
				'condition'  => [
                    'ekit_client_logo_slide_style' => 'banner_logo_image'
                ]
			)
        );

        $this->add_responsive_control(
            'ekit_client_logo_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .single-client' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
				'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'ekit_client_logo_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .single-client' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->start_controls_tabs(
            'ekit_client_logo_border_control'
        );

        $this->start_controls_tab(
            'ekit_client_logo_border_style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_client_logo_image_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-clients-slider .single-client',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_client_logo_image_style_border_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-clients-slider .single-client',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_client_logo_border_style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_client_logo_image_box_shadow_hover_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-clients-slider.simple_logo_image .single-client:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_client_logo_image_style_hover_border_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-clients-slider .single-client:hover',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->start_controls_tabs('ekit_client_logo_normal_tab');

        $this->start_controls_tab(
            'ekit_client_logo_style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_client_logo_opacity',
            [
                'label' => esc_html__( 'Opacity', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ ''],
                'range' => [
                    '' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => .1,
                    ],
                ],
                'default' => [
                    'unit' => '',
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .simple_logo_image .single-client .content-image .main-image' => 'opacity: {{SIZE}};filter: alpha(opacity={{SIZE}})',
                    '{{WRAPPER}} .elementskit-clients-slider .single-client img' => 'opacity: {{SIZE}};filter: alpha(opacity={{SIZE}})',
                ],
            ]
        );


        $this->end_controls_tab();

        //  hover tab

        $this->start_controls_tab(
            'ekit_client_logo_style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_client_logo_opacity_hover',
            [
                'label' => esc_html__( 'Opacity', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ ''],
                'range' => [
                    '' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => .1,
                    ],
                ],
                'default' => [
                    'unit' => '',
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .simple_logo_image .single-client:hover .content-image img' => 'opacity: {{SIZE}};filter: alpha(opacity={{SIZE}})',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_client_logo_hover_opacity',
            [
                'label' => esc_html__( 'Opacity Hover', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ ''],
                'range' => [
                    '' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => .1,
                    ],
                ],
                'default' => [
                    'unit' => '',
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .simple_logo_image .single-client:hover .content-image .main-image' => 'opacity: {{SIZE}};filter: alpha(opacity={{SIZE}})',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

        //  Navigation section

        $this->start_controls_section(
			'ekit_client_logo_section_navigation',
			[
				'label' => esc_html__( 'Arrows', 'elementskit-lite' ),
				'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                        'ekit_client_logo_show_arrow' => 'yes'
                ]
			]
        );

        $this->add_control(
			'ekit_client_logo_arrow_pos',
			[
				'label' => esc_html__( 'Position', 'elementskit-lite' ),
				'type' =>   Controls_Manager::SELECT,
				'default' => 'arrow_inside',
				'options' => [
					'arrow_outside'  => esc_html__( 'Outside', 'elementskit-lite' ),
					'arrow_inside' => esc_html__( 'Inside', 'elementskit-lite' ),
				],
			]
		);

        $this->add_responsive_control(
			'ekit_client_logo_arrow_size',
			[
				'label' => esc_html__( 'Size', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-arrow' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_client_logo_arrow_icon_typography',
                'label' => esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-clients-slider .slick-arrow i',
            ]
        );

        $this->add_responsive_control(
			'ekit_client_logo_arrow_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_client_logo_arrow_border_group',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-clients-slider .slick-arrow',
			]
        );

        $this->add_responsive_control(
			'ekit_client_logo_arrow_border_radious',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'ekit_client_logo_arrow_shadow',
                'selector'  => '{{WRAPPER}} .elementskit-clients-slider .slick-arrow',
            ]
		);
        		
        $this->add_control(
			'ekit_client_logo_position_popover_toggle',
			[
				'label' => esc_html__( 'Arrow Position', 'elementskit-lite' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => esc_html__( 'Default', 'elementskit-lite' ),
				'label_on' => esc_html__( 'Custom', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->start_popover();

        $this->add_control(
			'ekit_client_logo_arrow_pos_head',
			[
				'label' => esc_html__( 'Left Arrow Position', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING
			]
		);
        
        $this->add_responsive_control(
			'ekit_client_logo_arrow_left_pos',
			[
				'label' => esc_html__( 'Left Arrow Position (X)', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -1000,
						'max' => 1000,
					],
				],

				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-arrow.slick-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_client_logo_arrow_left_vertical_pos',
			[
				'label' => esc_html__( 'Left Arrow Position (Y)', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -1000,
						'max' => 1000,
					],
				],

				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-arrow.slick-prev' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_control(
			'ekit_client_logo_arrow_right_pos_head',
			[
				'label' => esc_html__( 'Right Arrow Position', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_responsive_control(
			'ekit_client_logo_arrow_right_pos',
			[
				'label' => esc_html__( 'Right Arrow Position (X)', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -1000,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-arrow.slick-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_client_logo_arrow_right_vertical_pos',
			[
				'label' => esc_html__( 'Right Arrow Position (Y)', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -1000,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-arrow.slick-next' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->end_popover();
        // Arrow Normal

		$this->start_controls_tabs('ekit_logo_style_tabs');

        $this->start_controls_tab(
			'ekit_logo_arrow_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit-lite' ),
			]
		);

        $this->add_control(
			'ekit_client_logo_arrow_color',
			[
				'label' => esc_html__( 'Arrow Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-arrow' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_control(
			'ekit_client_logo_arrow_background',
			[
				'label' => esc_html__( 'Arrow Background', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-arrow' => 'background: {{VALUE}}',
				],
			]
        );

        $this->end_controls_tab();

        //  Arrow hover tab

        $this->start_controls_tab(
			'ekit_client_logo_arrow_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit-lite' ),
			]
        );

        $this->add_control(
			'ekit_client_logo_arrow_hv_color',
			[
				'label' => esc_html__( 'Arrow Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-arrow:hover' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_control(
			'ekit_client_logo_arrow_hover_background',
			[
				'label' => esc_html__( 'Arrow Background', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-arrow:hover' => 'background: {{VALUE}}',
				],
			]
        );
        $this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		// Dots

        $this->start_controls_section(
            'ekit_client_logo_navigation_dot',
            [
                'label' => esc_html__( 'Dots', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                        'ekit_client_logo_show_dot' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ekit_client_logo_client_logo_dot_style',
            [
                'label' => esc_html__( 'Dot Style', 'elementskit-lite' ),
                'type' =>  Controls_Manager::SELECT,
                'default' => 'dot_dotted',
                'options' => [
                    'dot_default'  => esc_html__( 'Default', 'elementskit-lite' ),
                    'dot_dashed' => esc_html__( 'Dashed', 'elementskit-lite' ),
                    'dot_dotted' => esc_html__( 'Dotted', 'elementskit-lite' ),
                    'dot_paginated' => esc_html__( 'Paginate', 'elementskit-lite' ),
                ],
            ]
		);

		$this->add_responsive_control(
			'ekit_client_logo_dots_left_right_spacing',
			[
				'label' => esc_html__( 'Spacing Left Right', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default' => [
					'size' => 8,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'default' => [
					'size' => 8,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-dots li' => 'margin-right: {{SIZE}}{{UNIT}};margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'ekit_client_logo_dots_top_to_bottom',
            [
                'label' => esc_html__( 'Spacing Top To Bottom', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px'],
                'range' => [
                    'px' => [
                        'min' => -120,
                        'max' => 120,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],

                'selectors' => [
                    '{{WRAPPER}} .elementskit-clients-slider ul.slick-dots' => ' -webkit-transform:translateY( {{SIZE}}{{UNIT}});transform: translateY( {{SIZE}}{{UNIT}});',
                ],
            ]
        );

		$this->add_control(
            'ekit_client_logo_dot_color',
            [
                'label' => esc_html__( 'Dot Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-clients-slider.dot_paginated .slick-dots li' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_client_logo_client_logo_dot_style' => 'dot_paginated'
			    ]
            ]
        );

		$this->add_responsive_control(
			'ekit_client_logo_dot_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-dots li button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_client_logo_dot_height',
			[
				'label' => esc_html__( 'Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-dots li button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
        );

		$this->add_responsive_control(
			'ekit_client_logo_dot_border_radius',
			[
				'label' => esc_html__( 'Border radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-dots li button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_client_logo_dot_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-clients-slider .slick-dots li button',
			]
		);

		$this->add_control(
			'ekit_client_logo_dot_active_heading',
			[
				'label' => esc_html__( 'Active', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_client_logo_dot_active_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-clients-slider .slick-dots li.slick-active button',
			]
		);

		$this->add_responsive_control(
			'ekit_client_logo_dot_active_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-dots li.slick-active button' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_client_logo_client_logo_dot_style' => 'dot_dashed'
                ],
			]
		);

		$this->add_responsive_control(
			'ekit_client_logo_dot_active_scale',
			[
				'label' => esc_html__( 'Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => .5,
						'max' => 3,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1.2,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-clients-slider .slick-dots li.slick-active button' => 'transform: scale({{SIZE}});',
                ],
                'condition' => [
                    'ekit_client_logo_client_logo_dot_style' => 'dot_dotted'
                ],
			]
		);

        $this->end_controls_section();

        //  Separator
        $this->start_controls_section(
            'ekit_client_logo_separator_section',
            [
                'label' => esc_html__( 'Separator', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_client_logo_separator' => 'yes'
                ]
            ]
        );
        $this->add_responsive_control(
            'ekit_client_logo_separator_height',
            [
                'label' => esc_html__( 'Hight', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 100,
                        'step' => 1,
                    ],

                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-clients-slider .elementskit-client-slider-item.log-separator:after' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_client_logo_separator_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                        'step' => 1,
                    ],

                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-clients-slider .elementskit-client-slider-item.log-separator:after' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_client_logo_separator_top_bottom_position',
            [
                'label' => esc_html__( 'Top Bottom Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => -10,
                        'max' => 110,
                        'step' => 1,
                    ],

                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-clients-slider .elementskit-client-slider-item.log-separator:after' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_client_logo_separator_left_right_position',
            [
                'label' => esc_html__( 'Left Right Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => -5,
                        'max' => 120,
                        'step' => 1,
                    ],

                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-clients-slider .elementskit-client-slider-item.log-separator:after' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('ekit_client_logo_seperator_color_tabs');

        $this->start_controls_tab(
            'ekit_client_logo_seperator_color_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_client_logo_seperator_bg_color',
                'label' => esc_html__( 'Separator Color', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-clients-slider .elementskit-client-slider-item.log-separator:after',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_client_logo_seperator_color_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_client_logo_seperator_bg_color_hover',
                'label' => esc_html__( 'Separator Color', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-clients-slider:hover .elementskit-client-slider-item.log-separator:after',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

        $this->insert_pro_message();
    }

    protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {

        $settings = $this->get_settings_for_display();
        extract($settings);
        $logos = $settings['ekit_client_logo_repiter'];
        
		// Left Arrow Icon
        $migrated = isset( $settings['__fa4_migrated']['ekit_client_logo_left_arrow_icon'] );
        // - Check if its a new widget without previously selected icon using the old Icon control
        $is_new = empty( $settings['ekit_client_logo_left_arrow'] );
        $prevArrowIcon = ($is_new || $migrated) ? (!empty($ekit_client_logo_left_arrow_icon) && $ekit_client_logo_left_arrow_icon['library'] != 'svg' ? $ekit_client_logo_left_arrow_icon['value'] : '') : $ekit_client_logo_left_arrow;

		// Right Arrow Icon
        $migrated = isset( $settings['__fa4_migrated']['ekit_client_logo_right_arrow_icon'] );
        // - Check if its a new widget without previously selected icon using the old Icon control
        $is_new = empty( $settings['ekit_client_logo_right_arrow'] );
        $nextArrowIcon = ($is_new || $migrated) ? (!empty($ekit_client_logo_right_arrow_icon) && $ekit_client_logo_right_arrow_icon['library'] != 'svg' ? $ekit_client_logo_right_arrow_icon['value'] : '') : $ekit_client_logo_right_arrow;

        // Config
        $config = [
            'rtl'				=> is_rtl(),
            'arrows'			=> !empty($settings['ekit_client_logo_show_arrow']),
            'dots'				=> !empty($settings['ekit_client_logo_show_dot']),
            'pauseOnHover'		=> !empty($settings['ekit_client_logo_pause_on_hover']),
            'autoplay'			=> !empty($settings['ekit_client_logo_autoplay']),
            'speed'		=> !empty($settings['ekit_client_logo_speed']) ? $settings['ekit_client_logo_speed'] : 1000,
            'slidesPerView'		=> !empty($settings['ekit_client_logo_slidetosho']['size']) ? $settings['ekit_client_logo_slidetosho']['size'] : 4,
            'slidesPerGroup'	=> !empty($settings['ekit_client_logo_slidesToScroll']['size']) ? $settings['ekit_client_logo_slidesToScroll']['size'] : 1,
            'pauseOnHover'	    => !empty($settings['ekit_client_logo_pause_on_hover']),
            'slidesPerColumn'	            => !empty($settings['ekit_client_logo_rows']) ? (int) $settings['ekit_client_logo_rows'] : 1,
            'loop'  => ( !empty($ekit_client_logo_loop) && $ekit_client_logo_loop == 'yes' && !empty($ekit_client_logo_rows) && $ekit_client_logo_rows == 1 ) ? true : false,
            'breakpoints'		=> [
                320 => [
                    'slidesPerView'      => !empty($settings['ekit_client_logo_slidetosho_mobile']['size']) ? $settings['ekit_client_logo_slidetosho_mobile']['size'] : 1,
                    'slidesPerGroup'    => !empty($settings['ekit_client_logo_slidesToScroll_mobile']['size']) ? $settings['ekit_client_logo_slidesToScroll_mobile']['size'] : 1
                ],
                768 => [
                    'slidesPerView'      => !empty($settings['ekit_client_logo_slidetosho_tablet']['size']) ? $settings['ekit_client_logo_slidetosho_tablet']['size'] : 2,
                    'slidesPerGroup'    => !empty($settings['ekit_client_logo_slidesToScroll_tablet']['size']) ? $settings['ekit_client_logo_slidesToScroll_tablet']['size'] : 1,
                ],
                1024 => [
                    'slidesPerView'      => !empty($settings['ekit_client_logo_slidetosho']['size']) ? $settings['ekit_client_logo_slidetosho']['size'] : 2,
                    'slidesPerGroup'    => !empty($settings['ekit_client_logo_slidesToScroll']['size']) ? $settings['ekit_client_logo_slidesToScroll']['size'] : 1,
                ]
            ],
        ];

        $this->add_render_attribute( 'wrapper', 'class', 'elementskit-clients-slider slick-slider');
        $this->add_render_attribute( 'wrapper', 'class', !empty($settings['ekit_client_logo_show_dot']) ? 'slick-dotted' : '');
        $this->add_render_attribute( 'wrapper', 'class', $settings['ekit_client_logo_arrow_pos']);
        $this->add_render_attribute( 'wrapper', 'class', $settings['ekit_client_logo_client_logo_dot_style']);
		$this->add_render_attribute( 'wrapper', 'class', $settings['ekit_client_logo_hover_animation_driction']);
		$this->add_render_attribute( 'wrapper', 'class', $settings['ekit_client_logo_slide_style']);

        $this->add_render_attribute( 'wrapper', 'data-config', wp_json_encode($config) );

		$this->add_render_attribute( 'wrapper', 'data-direction', $settings['ekit_client_logo_hover_animation_driction']);

        $seperotor_enable = $settings['ekit_client_logo_separator'] == 'yes' ? 'log-separator' : '';
        ?>
        <div <?php echo $this->get_render_attribute_string( 'wrapper' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
            <div class="swiper-container">
                <div class="slick-list swiper-wrapper">
                    <?php

                        $count = 1;

                        foreach ($logos as $logo) :
                        if ( ! empty( $logo['ekit_client_logo_website_link']['url'] ) ) {
                            $this->add_link_attributes( 'button-' . $count, $logo['ekit_client_logo_website_link'] );
                        }
                        ?>
                        <div class="elementskit-client-slider-item swiper-slide <?php echo esc_attr($seperotor_enable);?>">
                            <div class="slick-slide">
                                <div class="single-client image-switcher" title="<?php echo esc_attr( $logo['ekit_client_logo_list_title'] ); ?>">
                                    <?php if($logo['ekit_client_logo_enable_link'] == 'yes') :  ?>


                                        <a <?php echo $this->get_render_attribute_string( 'button-' . esc_attr($count) ); ?> <?php echo $this->get_render_attribute_string( 'link_' . esc_attr($count) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
                                            <span class="content-image">
												<?php
												echo wp_kses(
													\Elementskit_Lite\Utils::get_attachment_image_html($logo, 'ekit_client_logo_image_normal', null, [
														'class' => $logo['ekit_client_logo_enable_hover_logo'] == 'yes' ? 'main-image' :  ''
													]),
													\ElementsKit_Lite\Utils::get_kses_array()
												);

												if(!empty($logo['ekit_client_logo_enable_hover_logo'])) {
													echo wp_kses(
														\Elementskit_Lite\Utils::get_attachment_image_html($logo, 'ekit_client_logo_image_hover', 'full', [
															'class' => 'hover-image'
														]),
														\ElementsKit_Lite\Utils::get_kses_array()
													);
												}
												?>
                                            </span>
                                        </a>

                                    <?php else:  ?>

                                        <div class="content-image">

											<?php
											echo wp_kses(
												\Elementskit_Lite\Utils::get_attachment_image_html($logo, 'ekit_client_logo_image_normal', 'full', [
													'class' => $logo['ekit_client_logo_enable_hover_logo'] == 'yes' ? 'main-image' :  ''
												]),
												\ElementsKit_Lite\Utils::get_kses_array()
											);

											if($logo['ekit_client_logo_enable_hover_logo']) {
												echo wp_kses(
													\Elementskit_Lite\Utils::get_attachment_image_html($logo, 'ekit_client_logo_image_hover', 'full', [
														'class' => 'hover-image'
													]),
													\ElementsKit_Lite\Utils::get_kses_array()
												);
											}
                                            ?>
                                        </div>

                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>

                    <?php  $count++; endforeach; ?>
                </div>

            </div><!-- .elementskit-clients-slider END -->
           
            <ul class="slick-dots swiper-pagination swiper-pagination-clickable swiper-pagination-bullets"></ul>
            <?php if(!empty($settings['ekit_client_logo_show_arrow'])) : ?>
                <button type="button" class="slick-prev slick-arrow"><i class="<?php echo esc_attr($prevArrowIcon); ?>"></i></button>
                <button type="button" class="slick-next slick-arrow"><i class="<?php echo esc_attr($nextArrowIcon); ?>"></i></button>
            <?php endif; ?>
        </div>

    <?php
    }
}
