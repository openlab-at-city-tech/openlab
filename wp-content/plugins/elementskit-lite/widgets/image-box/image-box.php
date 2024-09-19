<?php

namespace Elementor;

use \Elementor\ElementsKit_Widget_Image_Box_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Image_Box extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;

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
        return 'https://wpmet.com/doc/image-box-2/';
    }
    protected function is_dynamic_content(): bool {
        return false;
    }

    protected function register_controls() {

        // start content section for set Image
        $this->start_controls_section(
            'ekit_image_box_section_infoboxwithimage',
            [
                'label' => esc_html__( 'Image', 'elementskit-lite' ),
            ]
        );

        // Image insert
        $this->add_control(
            'ekit_image_box_image',
            [
                'label' => esc_html__( 'Choose Image', 'elementskit-lite' ),
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

		$this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'ekit_image_box_thumbnail',
                'default' => 'full',
                'separator' => 'none',
            ]
        );

        //  simple  style

        $this->add_control(
            'ekit_image_box_style_simple',
            [
                'label' => esc_html__( 'Content Area', 'elementskit-lite' ),
                'type' =>  Controls_Manager::SELECT,
                'default' => 'simple-card',
                'options' => [
                    'simple-card'  => esc_html__( 'Simple', 'elementskit-lite' ),
                    'style-modern' => esc_html__( 'Classic Curves', 'elementskit-lite' ),
                    'floating-style' => esc_html__( 'Floating box', 'elementskit-lite' ),
                    'hover-border-bottom' => esc_html__( 'Hover Border', 'elementskit-lite' ),
                    'style-sideline' => esc_html__( 'Side Line', 'elementskit-lite' ),
                    'shadow-line' => esc_html__( 'Shadow line', 'elementskit-lite' ),
                ],
            ]
        );

        $this->add_control(
            'enable_equal_height',
            [
                'label'     => esc_html__( 'Equal Height?', 'elementskit-lite' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'enable' => esc_html__( 'Enable', 'elementskit-lite' ),
                    'disable' => esc_html__( 'Disable', 'elementskit-lite' ),
                ],
                'default'   => 'disable',
                'prefix_class'  => 'ekit-equal-height-',
                'selectors' => [
					'{{WRAPPER}}.ekit-equal-height-enable, {{WRAPPER}}.ekit-equal-height-enable .elementor-widget-container, {{WRAPPER}}.ekit-equal-height-enable .ekit-wid-con, {{WRAPPER}}.ekit-equal-height-enable .ekit-wid-con .elementskit-info-image-box' => 'height: 100%;',
                ],
                'condition' => [
                    'ekit_image_box_style_simple!'   => 'floating-style'
                ]
            ]
        );

        $this->add_control(
            'ekit_image_box_enable_link',
            [
                'label' => esc_html__( 'Enable Link', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'ekit_image_box_website_link',
            [
                'label' => esc_html__( 'Link', 'elementskit-lite' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://wpmet.com', 'elementskit-lite' ),
                'show_external' => true,
                'condition' => [
                    'ekit_image_box_enable_link' => 'yes'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

         // end content section for set Image
        $this->end_controls_section();


        // start content section for image title and sub title
        $this->start_controls_section(
            'ekit_image_box_section_for_image_title',
            [
                'label' => esc_html__( 'Body', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_image_box_title_text',
            [
                'label' => esc_html__( 'Title ', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'This is the heading', 'elementskit-lite' ),
                'placeholder' => esc_html__( 'Enter your title', 'elementskit-lite' ),
                'label_block' => true,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'ekit_image_box_front_title_icons__switch',
            [
                'label' => esc_html__('Add icon? ', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
                'condition' => [
                    'ekit_image_box_style_simple' => 'floating-style',
                ]
            ]
		);

        $this->add_control(
            'ekit_image_box_front_title_icons',
            [
                'label' => esc_html__( 'Title Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_image_box_front_title_icon',
                'default' => [
                    'value' => 'icon icon-review',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_image_box_style_simple' => 'floating-style',
                    'ekit_image_box_front_title_icons__switch'  => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ekit_image_box_front_title_icon_position',
            [
                'label' => esc_html__( 'Title Icon Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' =>esc_html__( 'Before', 'elementskit-lite' ),
                    'right' =>esc_html__( 'After', 'elementskit-lite' ),
                ],
                'condition' => [
                    'ekit_image_box_front_title_icons__switch'  => 'yes',
                    'ekit_image_box_style_simple' => 'floating-style',
                ]
            ]
        );

        // title tag
        $this->add_control(
            'ekit_image_box_title_size',
            [
                'label' => esc_html__( 'Title HTML Tag', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'ekit_image_box_description_text',
            [
                'label' => esc_html__( 'Description', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'Click edit  to change this text. Lorem ipsum dolor sit amet, cctetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementskit-lite' ),
                'placeholder' => esc_html__( 'Enter your description', 'elementskit-lite' ),
                'separator' => 'none',
                'rows' => 10,
                'show_label' => false,
            ]
        );

        // Text aliment

        $this->add_control(
            'ekit_image_box_content_text_align',
            [
                'label' => esc_html__( 'Alignment', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
            ]
        );

         // end content section for image title and sub title
        $this->end_controls_section();

         // start content section for button
         //  Section Button

        $this->start_controls_section(
            'ekit_image_box_section_button',
            [
                'label' => esc_html__( 'Button', 'elementskit-lite' ),
            ]
        );
        $this->add_control(
			'ekit_image_box_enable_btn',
			[
				'label' => esc_html__( 'Enable Button', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);
        $this->add_control(
			'ekit_image_box_btn_text',
			[
				'label' =>esc_html__( 'Label', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'default' =>esc_html__( 'Learn more ', 'elementskit-lite' ),
				'placeholder' =>esc_html__( 'Learn more ', 'elementskit-lite' ),
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'ekit_image_box_enable_btn' => 'yes',
                ]
			]
		);


		$this->add_control(
			'ekit_image_box_btn_url',
			[
				'label' =>esc_html__( 'URL', 'elementskit-lite' ),
				'type' => Controls_Manager::URL,
				'placeholder' =>esc_url('https://wpmet.com'),
				'default' => [
					'url' => '#',
				],
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'ekit_image_box_enable_btn' => 'yes',
                ]
			]
        );
        $this->add_control(
            'ekit_image_box_icons__switch',
            [
                'label' => esc_html__('Add icon? ', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
                'condition' => [
                    'ekit_image_box_enable_btn' => 'yes',
                ]
            ]
		);
        $this->add_control(
			'ekit_image_box_icons',
			[
				'label' =>esc_html__( 'Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_image_box_icon',
                'default' => [
                    'value' => '',
                ],
				'label_block' => true,
                'condition' => [
                    'ekit_image_box_enable_btn' => 'yes',
                    'ekit_image_box_icons__switch' => 'yes'
                ]
			]
		);
		$this->add_control(
			'ekit_image_box_icon_align',
			[
				'label' =>esc_html__( 'Icon Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' =>esc_html__( 'Before', 'elementskit-lite' ),
					'right' =>esc_html__( 'After', 'elementskit-lite' ),
				],
				'condition' => [
                    'ekit_image_box_icons__switch' => 'yes',
                    'ekit_image_box_enable_btn' => 'yes',
				],
			]
		);
        // end content section for button
        $this->end_controls_section();

        // start style section here


        // start floating box style
        $this->start_controls_section(
			'ekit_image_box_image_floating_box',
			[
				'label' => esc_html__( 'Floating Style', 'elementskit-lite' ),
                'tab' =>  Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_image_box_style_simple' => 'floating-style',
                ]
			]
        );

        $this->start_controls_tabs(
            'ekit_image_box_image_floating_box_heights'
        );

        $this->start_controls_tab(
            'ekit_image_box_image_floating_box_normal_height_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
			'ekit_image_box_image_floating_box_height',
			[
				'label' => esc_html__( 'Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 90,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
            'ekit_image_box_image_floating_box_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body .elementskit-info-box-title > i ' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body .elementskit-info-box-title > svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_image_box_image_floating_box_hover_height_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
			'ekit_image_box_image_floating_box_hover_height',
			[
				'label' => esc_html__( 'Hover Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 185,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box.floating-style:hover .elementskit-box-body' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
            'ekit_image_box_image_floating_box_icon_color_hover',
            [
                'label' => esc_html__( 'Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box.floating-style:hover .elementskit-box-body .elementskit-info-box-title > i ' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-info-image-box.floating-style:hover .elementskit-box-body .elementskit-info-box-title > svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
			'ekit_image_box_image_floating_box_tab_separetor',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
        );
        
        $this->add_responsive_control(
			'ekit_image_box_image_floating_box_icon_font_size',
			[
				'label' => esc_html__( 'Icon Font Size', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 26,
				],
				'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body .elementskit-info-box-title > i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body .elementskit-info-box-title > svg'    => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);


        $this->add_responsive_control(
			'ekit_image_box_image_floating_box_margin_top',
			[
				'label' => esc_html__( 'Margin Top', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => -40,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_image_box_image_floating_box_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 90,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_image_box_image_floating_box_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body, {{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body::before, {{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body::after',
			]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_image_box_image_floating_box_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body, {{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body::before, {{WRAPPER}} .elementskit-info-image-box.floating-style .elementskit-box-body::after',
            ]
        );

        $this->end_controls_section();

         // start classic curves style
        $this->start_controls_section(
			'ekit_image_box_image_classic_curves',
			[
				'label' => esc_html__( 'Classic Curves', 'elementskit-lite' ),
                'tab' =>  Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_image_box_style_simple' => 'style-modern',
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_image_box_image_classic_curves_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box.style-modern .elementskit-box-body' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_image_box_image_classic_curves_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => -20,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box.style-modern .elementskit-box-body' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->end_controls_section();

        // start border bottom hover style
        $this->start_controls_section(
			'ekit_image_box_border_bottom_hover',
			[
				'label' => esc_html__( 'Hover Border Bottom', 'elementskit-lite' ),
                'tab' =>  Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_image_box_style_simple' => 'hover-border-bottom',
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_image_box_border_hover_height',
			[
				'label' => esc_html__( 'Border Bottom Height', 'elementskit-lite' ),
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
					'size' => 3,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box.hover-border-bottom .elementskit-box-body::before' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_image_box_style_simple' => 'hover-border-bottom',
                ]
			]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_image_box_border_hover_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-info-image-box.hover-border-bottom .elementskit-box-body::before',
                'condition' => [
                    'ekit_image_box_style_simple' => 'hover-border-bottom',
                ]
			]
		);

        $this->add_control(
			'ekit_image_box_border_hover_background_direction',
			[
				'label' => esc_html__( 'Hover Direction', 'elementskit-lite' ),
				'type' =>   Controls_Manager::CHOOSE,
				'options' => [
					'hover_from_left' => [
						'title' => esc_html__( 'From Left', 'elementskit-lite' ),
						'icon' => 'fa fa-caret-right',
                    ],
                    'hover_from_center' => [
						'title' => esc_html__( 'From Center', 'elementskit-lite' ),
						'icon' => 'fa fa-align-center',
					],
					'hover_from_right' => [
						'title' => esc_html__( 'From Right', 'elementskit-lite' ),
						'icon' => 'fa fa-caret-left',
					],
				],
				'default' => 'hover_from_right',
				'toggle' => true,
				'condition'  => [
					'ekit_image_box_style_simple' => 'hover-border-bottom',
				]
			]
        );

        $this->end_controls_section();

         // start side line style
        $this->start_controls_section(
			'ekit_image_box_image_side_line',
			[
				'label' => esc_html__( 'Side Line', 'elementskit-lite' ),
                'tab' =>  Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_image_box_style_simple' => 'style-sideline',
                ]
			]
        );

		$this->add_responsive_control(
            'ekit_image_box_image_side_line_border_width',
            [
                'label' => esc_html__( 'Border Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 3,
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-image-box-body-inner' => 'border-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

		$this->add_responsive_control(
            'ekit_image_box_image_side_line_border_type',
            [
                'label' => esc_html__( 'Border Type', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
				'default' => 'solid',
                'options' => [
                    'none' =>esc_html__( 'None', 'elementskit-lite' ),
                    'solid' =>esc_html__( 'Solid', 'elementskit-lite' ),
                    'double' =>esc_html__( 'Double', 'elementskit-lite' ),
                    'dotted' =>esc_html__( 'Dotted', 'elementskit-lite' ),
                    'dashed' =>esc_html__( 'Dashed', 'elementskit-lite' ),

                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-image-box-body-inner' => 'border-style: {{VALUE}}',
                ],
            ]
        );

        $this->start_controls_tabs(
            'side_line_tabs'
        );
            $this->start_controls_tab(
                'side_line_normal',
                [
                    'label' => esc_html__( 'Normal', 'elementskit-lite' ),
                ]
            );
                $this->add_responsive_control(
                    'ekit_image_box_image_side_line_border',
                    [
                        'label'     => esc_html__( 'Border Color', 'elementskit-lite' ),
                        'type'      => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .ekit-image-box-body-inner' => 'border-color: {{VALUE}};',
                        ],
                    ]
                );
            $this->end_controls_tab();

            $this->start_controls_tab(
                'side_line_hover',
                [
                    'label' => esc_html__( 'Hover', 'elementskit-lite' ),
                ]
            );
                $this->add_responsive_control(
                    'side_line_hover_color',
                    [
                        'label'     => esc_html__( 'Border Color', 'elementskit-lite' ),
                        'type'      => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}}:hover .ekit-image-box-body-inner' => 'border-color: {{VALUE}};',
                        ],
                    ]
                );
            $this->end_controls_tab();
        $this->end_controls_tabs();

		$this->end_controls_section();

        // start line shadow style
        $this->start_controls_section(
			'ekit_image_box_image_shadow_line',
			[
				'label' => esc_html__( 'Shadow Line', 'elementskit-lite' ),
                'tab' =>  Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_image_box_style_simple' => 'shadow-line',
                ]
			]
        );

        $this->start_controls_tabs(
            'ekit_image_box_image_shadow_line_tabs'
        );

        $this->start_controls_tab(
            'ekit_image_box_image_shadow_line_left_tab',
            [
                'label' => esc_html__( 'Left Line', 'elementskit-lite' ),
            ]
        );

		$this->add_responsive_control(
			'ekit_image_box_image_shadow_left_line_width',
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
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box.shadow-line .elementskit-box-body::before' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_image_box_image_shadow_left_line_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-info-image-box.shadow-line .elementskit-box-body::before',
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_image_box_image_shadow_left_line_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-info-image-box.shadow-line .elementskit-box-body::before',
			]
        );

        $this->end_controls_tab();

        // right line
        $this->start_controls_tab(
            'ekit_image_box_image_shadow_line_right_tab',
            [
                'label' => esc_html__( 'Right Line', 'elementskit-lite' ),
            ]
        );

		$this->add_responsive_control(
			'ekit_image_box_image_shadow_right_line_width',
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
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box.shadow-line .elementskit-box-body::after' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_image_box_image_shadow_right_line_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-info-image-box.shadow-line .elementskit-box-body::after',
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_image_box_image_shadow_right_line_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-info-image-box.shadow-line .elementskit-box-body::after',
			]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


        // start image section style
        $this->start_controls_section(
            'ekit_image_box_image_section',
            [
                'label' => esc_html__( 'Image', 'elementskit-lite' ),
                'tab' =>  Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
			'ekit_image_box_border_radius',
			[
				'label' => esc_html__( 'Border radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
                    '{{WRAPPER}} .elementskit-box-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-box-header img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_image_box_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-box-header img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->start_controls_tabs(
            'ekit_image_box_style_tabs_image'
        );

        $this->start_controls_tab(
            'ekit_image_box_style_normal_tab_image',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_image_box_image_opacity',
            [
                'label' => esc_html__( 'Image opacity', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => .01,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box  .elementskit-box-header img' => 'opacity: {{SIZE}};',
                    '{{WRAPPER}} .elementskit-info-image-box.elementskit-thumb-card >  img' => 'opacity: {{SIZE}};',
                ],
            ]
        );


        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_image_box_style_hover_tab_image',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_image_box_image_opacity_hover',
            [
                'label' => esc_html__( 'Image opacity', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => .01,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box:hover  .elementskit-box-header img' => 'opacity: {{SIZE}};',
                    '{{WRAPPER}} .elementskit-info-image-box.elementskit-thumb-card:hover >  img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_image_box_image_scale_on_hover',
            [
                'label' => esc_html__( 'Image Scale on Hover', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 2,
                        'step' => .1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1.1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box:hover  .elementskit-box-header img' => 'transform: scale({{SIZE}});',
                    '{{WRAPPER}} .elementskit-info-image-box.elementskit-thumb-card:hover >  img' => 'transform: scale({{SIZE}});',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
        //end image section style

        // start body style section
        $this->start_controls_section(
            'ekit_image_box_style_body_section',
            [
                'label' => esc_html__( 'Body', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
			'ekit_imagebox_genaral_border_heading_title',
			[
				'label' => esc_html__( 'General', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
			]
		);
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_imagebox_container_border_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-box-body',
            ]
        );

        $this->add_responsive_control(
            'body_radius',
            [
                'label'         => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => [ 'px', '%', 'em' ],
                'selectors'     => [
                    '{{WRAPPER}} .ekit-image-box-body, {{WRAPPER}} .ekit-image-box-body:before, {{WRAPPER}} .ekit-image-box-body:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_imagebox_container_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-box-body',
            ]
        );

        $this->add_responsive_control(
			'ekit_imagebox_container_spacing',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box .elementskit-box-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_image_box_shadow_group',
                'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-box-body',
            ]
        );

		// title
		$this->add_control(
			'ekit_imagebox_title_border_heading_title',
			[
				'label' => esc_html__( 'Title', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
			]
        );

        $this->add_responsive_control(
            'ekit_image_box_title_bottom_space',
			[
                'label' => esc_html__( 'Spacing', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
                'default' => [  
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '20',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => 'true',
                ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box .elementskit-info-box-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_image_box_title_typography',
                'label' => esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-box-content :is(.elementskit-info-box-title, .elementskit-info-box-title a )',
            ]
        );
        
        $this->start_controls_tabs('ekit_image_box_style_heading_tabs');

        $this->start_controls_tab(
            'ekit_image_box_style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_image_box_heading_color',
            [
                'label' => esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box .elementskit-info-box-title ' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-info-image-box .elementskit-info-box-title a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-info-image-box .elementskit-info-box-title svg path'    => 'stroke: {{VALUE}}; fill: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_image_box_style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_image_box_heading_color_hover',
            [
                'label' => esc_html__( 'Color (Hover)', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box:hover .elementskit-info-box-title ' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-info-image-box:hover .elementskit-info-box-title a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-info-image-box:hover .elementskit-info-box-title svg path'    => 'stroke: {{VALUE}}; fill: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();

		$this->end_controls_tabs();

		// sub Description
		$this->add_control(
			'ekit_imagebox_description_border_heading_title',
			[
				'label' => esc_html__( 'Description', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->add_responsive_control(
            'ekit_image_box_title_bottom_space_description',
			[
                'label' => esc_html__( 'Spacing', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
                'default' => [  
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '14',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => 'true',
                ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box .elementskit-box-style-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_image_box_title_typography_description',
                'label' => esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-box-style-content',
            ]
        );
        
        $this->start_controls_tabs('ekit_image_box_style_description_tabs');

        $this->start_controls_tab(
            'ekit_image_box_style_normal_tab_description',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_image_box_heading_color_description',
            [
                'label' => esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box .elementskit-box-style-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_image_box_style_hover_tab_description',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_image_box_heading_color_hover_description',
            [
                'label' => esc_html__( 'Color (Hover)', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box:hover .elementskit-box-style-content ' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // start style csetion for button
        // Button

        $this->start_controls_section(
            'ekit_image_box_section_style',
            [
                'label' => esc_html__( 'Button', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_image_box_enable_btn' => 'yes',
                ]
            ]
        );
        $this->add_responsive_control(
			'ekit_image_box_text_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box .elementskit-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_image_box_typography_group',
				'label' =>esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn',
			]
		);
        $this->add_responsive_control(
			'ekit_image_box_btn_icon_font_size',
			array(
				'label'      => esc_html__( 'Icon Font Size', 'elementskit-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'rem',
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'  => array(
                    '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn svg'  => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);
        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'ekit_image_box_tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_image_box_button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};', 
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_image_box_btn_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_image_box_button_border_color_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn',
            ]
        );
        $this->add_responsive_control(
			'ekit_image_box_btn_border_radius',
			[
				'label' =>esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '' ,
					'left' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box .elementskit-btn' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_image_box_button_box_shadow',
                'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_image_box_tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_image_box_btn_hover_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn:hover svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};', 
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_image_box_btn_background_hover_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_image_box_button_border_hv_color_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn:hover',
            ]
        );
        $this->add_responsive_control(
			'ekit_image_box_btn_hover_border_radius',
			[
				'label' =>esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '' ,
					'left' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-info-image-box .elementskit-btn:hover' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_image_box_button_box_shadow_hover_group',
                'selector' => '{{WRAPPER}} .elementskit-info-image-box .elementskit-btn:hover',
            ]
        );


        $this->end_controls_tab();

        $this->end_controls_tabs();


        $this->end_controls_section();

        // end style section for buttun

        $this->insert_pro_message();
    }

    protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {

        $settings = $this->get_settings_for_display();

        // Data Sanitization/Escaping
        $options_ekit_image_box_title_size = array_keys([
			'h1'   => 'H1',
			'h2'   => 'H2',
			'h3'   => 'H3',
			'h4'   => 'H4',
			'h5'   => 'H5',
			'h6'   => 'H6',
			'div'  => 'div',
			'span' => 'span',
			'p'    => 'p',
		]);

        $ekit_image_box_content_text_align_value_escape = \ElementsKit_Lite\Utils::esc_options($settings['ekit_image_box_content_text_align'], ['left', 'center', 'right'], 'center');

        // Wrapper settion

        $this->add_render_attribute('wrapper', 'class', 'elementskit-info-image-box ekit-image-box');
        $this->add_render_attribute('wrapper', 'class', 'text-' . $ekit_image_box_content_text_align_value_escape);


        if ($settings['ekit_image_box_style_simple'] == 'hover-border-bottom') {

            $this->add_render_attribute('wrapper', 'class', $settings['ekit_image_box_border_hover_background_direction']);
        }
        $this->add_render_attribute('wrapper', 'class', $settings['ekit_image_box_style_simple']);



        // Image section
		$image_html = '';
        if (!empty($settings['ekit_image_box_image']['url'])) {

            $this->add_render_attribute('image', 'src', $settings['ekit_image_box_image']['url']);
            $this->add_render_attribute('image', 'alt', Control_Media::get_image_alt($settings['ekit_image_box_image']));
            $this->add_render_attribute('image', 'title', Control_Media::get_image_title($settings['ekit_image_box_image']));

			$image_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'ekit_image_box_thumbnail', 'ekit_image_box_image' );
        }

        // Button
        $btn_text = $settings['ekit_image_box_btn_text'];

        
        if ( ! empty( $settings['ekit_image_box_btn_url']['url'] ) ) {
            $this->add_link_attributes( 'button-2', $settings['ekit_image_box_btn_url'] );
        }

        $image_pos = 'image-box-img-' . $ekit_image_box_content_text_align_value_escape;
?>

            <div <?php echo ($this->get_render_attribute_string('wrapper')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?> >

                <?php if($settings['ekit_image_box_enable_link'] == 'yes' && isset($settings['ekit_image_box_website_link']['url'])) {
                    $this->add_link_attributes( 'button', $settings['ekit_image_box_website_link'] );

                    echo "<a ". $this->get_render_attribute_string( 'button' )  .">"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor
                }
                ?>

                <div class="elementskit-box-header <?php echo esc_attr($image_pos); ?>">

                    <?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>

                </div>
                <?php if($settings['ekit_image_box_enable_link'] == 'yes' && isset($settings['ekit_image_box_website_link']['url'])) {
                    echo "</a>";
                } ?>

                <div class="elementskit-box-body ekit-image-box-body">
                    <div class="elementskit-box-content ekit-image-box-body-inner">
                        <?php
                        if ($settings['ekit_image_box_title_text'] != '') :
                        ?>
                        <<?php echo in_array($settings['ekit_image_box_title_size'], $options_ekit_image_box_title_size) ? esc_attr($settings['ekit_image_box_title_size']) : 'h3'; ?> class="elementskit-info-box-title">

                        <?php if(($settings['ekit_image_box_front_title_icons'] != '') && ($settings['ekit_image_box_front_title_icon_position'] == 'left') && ($settings['ekit_image_box_style_simple'] == 'floating-style')) : ?>

                            <?php
                                // new icon
                                $migrated = isset( $settings['__fa4_migrated']['ekit_image_box_front_title_icons'] );
                                // Check if its a new widget without previously selected icon using the old Icon control
                                $is_new = empty( $settings['ekit_image_box_front_title_icon'] );
                                if ( $is_new || $migrated ) {
                                    // new icon
                                    Icons_Manager::render_icon( $settings['ekit_image_box_front_title_icons'], [ 'aria-hidden' => 'true' ] );
                                } else {
                                    ?>
                                    <i class="<?php echo esc_attr($settings['ekit_image_box_front_title_icon']); ?>" aria-hidden="true"></i>
                                    <?php
                                }
                            ?>

                        <?php endif; 
                            echo wp_kses($settings['ekit_image_box_title_text'], \ElementsKit_Lite\Utils::get_kses_array());
                        ?>

                        <?php if(($settings['ekit_image_box_front_title_icons'] != '') && ($settings['ekit_image_box_front_title_icon_position'] == 'right') && ($settings['ekit_image_box_style_simple'] == 'floating-style')) : ?>
                                
                            <?php
                                // new icon
                                $migrated = isset( $settings['__fa4_migrated']['ekit_image_box_front_title_icons'] );
                                // Check if its a new widget without previously selected icon using the old Icon control
                                $is_new = empty( $settings['ekit_image_box_front_title_icon'] );
                                if ( $is_new || $migrated ) {
                                    // new icon
                                    Icons_Manager::render_icon( $settings['ekit_image_box_front_title_icons'], [ 'aria-hidden' => 'true' ] );
                                } else {
                                    ?>
                                    <i class="<?php echo esc_attr($settings['ekit_image_box_front_title_icon']); ?>" aria-hidden="true"></i>
                                    <?php
                                }
                            ?>

                        <?php endif; ?>

                    </<?php echo in_array($settings['ekit_image_box_title_size'], $options_ekit_image_box_title_size) ? esc_attr($settings['ekit_image_box_title_size']) : 'h3'; ?>>
                    <?php

                        endif;
                    ?>
                    <?php if ($settings['ekit_image_box_description_text'] != '') { ?>
                    <div class="elementskit-box-style-content">
                        <?php
                        echo wp_kses($settings['ekit_image_box_description_text'], \ElementsKit_Lite\Utils::get_kses_array())
                        ?>
                    </div>
                    <?php }; ?>
                </div>

                <?php if($settings['ekit_image_box_enable_btn'] == 'yes') :  ?>
                <div class="elementskit-box-footer">
                    <div class="box-footer">
                        <div class="btn-wraper">
                            <?php if($settings['ekit_image_box_icon_align'] == 'right'): ?>
                                <a <?php echo $this->get_render_attribute_string( 'button-2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?> class="elementskit-btn whitespace--normal">
                                    <?php echo esc_html( $btn_text ); ?>

                                    <?php
                                        // new icon
                                        $migrated = isset( $settings['__fa4_migrated']['ekit_image_box_icons'] );
                                        // Check if its a new widget without previously selected icon using the old Icon control
                                        $is_new = empty( $settings['ekit_image_box_icon'] );
                                        if ( $is_new || $migrated ) {
                                            // new icon
                                            Icons_Manager::render_icon( $settings['ekit_image_box_icons'], [ 'aria-hidden' => 'true' ] );
                                        } else {
                                            ?>
                                            <i class="<?php echo esc_attr($settings['ekit_image_box_icon']); ?>" aria-hidden="true"></i>
                                            <?php
                                        }
                                    ?>

                                </a>
                                <?php elseif ($settings['ekit_image_box_icon_align'] == 'left') : ?>
                                <a <?php echo $this->get_render_attribute_string( 'button-2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor?> class="elementskit-btn whitespace--normal">
                                    
                                    <?php
                                        // new icon
                                        $migrated = isset( $settings['__fa4_migrated']['ekit_image_box_icons'] );
                                        // Check if its a new widget without previously selected icon using the old Icon control
                                        $is_new = empty( $settings['ekit_image_box_icon'] );
                                        if ( $is_new || $migrated ) {
                                            // new icon
                                            Icons_Manager::render_icon( $settings['ekit_image_box_icons'], [ 'aria-hidden' => 'true' ] );
                                        } else {
                                            ?>
                                            <i class="<?php echo esc_attr($settings['ekit_image_box_icon']); ?>" aria-hidden="true"></i>
                                            <?php
                                        }
                                    ?>

                                    <?php echo esc_html( $btn_text ); ?>
                                </a>
                                <?php else : ?>
                                <a <?php echo $this->get_render_attribute_string( 'button-2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?> class="elementskit-btn whitespace--normal">
                                    <?php echo esc_html( $btn_text ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            </div>
    <?php
    }
}
