<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Dual_Button_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Dual_Button extends Widget_Base {
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
        return 'https://wpmet.com/doc/dual-button/';
    }
    protected function is_dynamic_content(): bool {
        return false;
    }

    protected function register_controls() {

        $this->start_controls_section(
            'dualbutton_content',
            [
                'label' => esc_html__( 'Double Button', 'elementskit-lite' ),
            ]
        );

            $this->add_control(
                'ekit_show_button_middle_text',
                [
                    'label' => esc_html__( 'Middle Text', 'elementskit-lite' ),
                    'type'  => Controls_Manager::SWITCHER,
                ]
            );

            $this->add_control(
                'ekit_button_middle_text',
                [
                    'label' => esc_html__( 'Text', 'elementskit-lite' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__( 'Or', 'elementskit-lite' ),
                    'condition'   => [
                        'ekit_show_button_middle_text' => 'yes',
                    ],
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_responsive_control(
                'ekit_double_button_align',
                [
                    'label' => esc_html__( 'Alignment', 'elementskit-lite' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'start' => [
                            'title' => esc_html__( 'Left', 'elementskit-lite' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__( 'Center', 'elementskit-lite' ),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'end' => [
                            'title' => esc_html__( 'Right', 'elementskit-lite' ),
                            'icon' => 'eicon-text-align-right',
                        ],
                    ],
                    'prefix_class' => 'elementor-widget-elementskit-dual-button%s-',
                ]
            );

            $this->add_responsive_control(
                'ekit_dual_button_width',
                [
                    'label' => esc_html__( 'Button Width', 'elementskit-lite' ),
                    'type'  => Controls_Manager::SLIDER,
                    'range' => [
                        '%' => [
                            'max' => 100,
                            'min' => 20,
                        ],
                        'px' => [
                            'max' => 1200,
                            'min' => 300,
                        ],
                    ],
                    'size_units' => ['%', 'px'],
                    'default' => [
                        'size' => 40,
                        'unit' => '%',
                    ],
                    'tablet_default' => [
                        'size' => 80,
                        'unit' => '%',
                    ],
                    'mobile_default' => [
                        'size' => 100,
                        'unit' => '%',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit_double_button'  => 'width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'ekit_dual_button_gap',
                [
                    'label'   => esc_html__( 'Button Gap', 'elementskit-lite' ),
                    'type'    => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 5,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors'  => [
                        '{{WRAPPER}} .ekit-double-btn:not(:last-child)' => 'margin-right: {{SIZE}}px;',
                    ],
                    'condition' => [
                        'ekit_show_button_middle_text!' => 'yes',
                    ],
                ]
            );

        $this->end_controls_section();

        // Button One
        $this->start_controls_section(
            'ekit_button_one_content',
            [
                'label' => esc_html__( 'Button One', 'elementskit-lite' ),
            ]
        );
            $this->add_control(
                'ekit_button_one_text',
                [
                    'label' => esc_html__( 'Text', 'elementskit-lite' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__( 'Button', 'elementskit-lite' ),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'ekit_button_one_link',
                [
                    'label' => esc_html__( 'Link', 'elementskit-lite' ),
                    'type' => Controls_Manager::URL,
                    'placeholder' => esc_html( 'https://wpmet.com'),
                    'show_external' => true,
                    'default' => [
                        'url' => '#',
                        'is_external' => false,
                        'nofollow' => false,
                    ],
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'ekit_button_one_icons__switch',
                [
                    'label' => esc_html__('Add icon? ', 'elementskit-lite'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
                    'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
                ]
            );

            $this->add_control(
                'ekit_button_one_icons',
                [
                    'label' => __( 'Icon', 'elementskit-lite' ),
                    'type' => Controls_Manager::ICONS,
                    'fa4compatibility' => 'ekit_button_one_icon',
                    'default' => [
                        'value' => '',
                    ],
                    'condition' => [
                        'ekit_button_one_icons__switch' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'ekit_double_button_one_icon_position',
                [
                    'label' => esc_html__( 'Icon Position', 'elementskit-lite' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'before',
                    'options' => [
                        'before'  => esc_html__( 'Before', 'elementskit-lite' ),
                        'after' => esc_html__( 'After', 'elementskit-lite' ),
                    ],
                    'condition' => [
                        'ekit_button_one_icons__switch' => 'yes'
                    ]
                ]
            );

            $this->add_responsive_control(
                'ekit_double_button_one_icon_before_specing',
                [
                    'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
                    'type'  => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'max' => 150,
                        ],
                    ],
                    'default' => [
                        'size' => 8,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one > i'  => 'padding-right: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one > svg'  => 'margin-right: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'ekit_button_one_icons__switch' => 'yes',
                        'ekit_double_button_one_icon_position' => 'before',
                    ]
                ]
            );

            $this->add_responsive_control(
                'ekit_double_button_one_icon_after_specing',
                [
                    'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
                    'type'  => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'max' => 150,
                        ],
                    ],
                    'default' => [
                        'size' => 8,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one > i'  => 'padding-left: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one > svg'  => 'margin-left: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'ekit_button_one_icons__switch' => 'yes',
                        'ekit_double_button_one_icon_position' => 'after',
                    ]
                ]
            );

        $this->end_controls_section(); // Button One End

        // Button Two
        $this->start_controls_section(
            'ekit_button_two_content',
            [
                'label' => esc_html__( 'Button Two', 'elementskit-lite' ),
            ]
        );
            $this->add_control(
                'ekit_button_two_text',
                [
                    'label' => esc_html__( 'Text', 'elementskit-lite' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__( 'Button', 'elementskit-lite' ),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'ekit_button_two_link',
                [
                    'label' => esc_html__( 'Link', 'elementskit-lite' ),
                    'type' => Controls_Manager::URL,
                    'placeholder' => esc_html__( 'https://wpmet.com', 'elementskit-lite' ),
                    'show_external' => true,
                    'default' => [
                        'url' => '#',
                        'is_external' => false,
                        'nofollow' => false,
                    ],
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'ekit_button_two_icons__switch',
                [
                    'label' => esc_html__('Add icon? ', 'elementskit-lite'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
                    'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
                ]
            );

            $this->add_control(
                'ekit_button_two_icons',
                [
                    'label' => __( 'Icon', 'elementskit-lite' ),
                    'type' => Controls_Manager::ICONS,
                    'fa4compatibility' => 'ekit_button_two_icon',
                    'default' => [
                        'value' => '',
                    ],
                    'condition' => [
                        'ekit_button_two_icons__switch' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'ekit_double_button_two_icon_position',
                [
                    'label' => esc_html__( 'Icon Position', 'elementskit-lite' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'before',
                    'options' => [
                        'before'  => esc_html__( 'Before', 'elementskit-lite' ),
                        'after' => esc_html__( 'After', 'elementskit-lite' ),
                    ],
                    'condition' => [
                        'ekit_button_two_icons__switch' => 'yes'
                    ]
                ]
            );

            $this->add_responsive_control(
                'ekit_double_button_two_icon_before_specing',
                [
                    'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
                    'type'  => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'max' => 150,
                        ],
                    ],
                    'default' => [
                        'size' => 8,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two > i'  => 'padding-right: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two > svg'  => 'margin-right: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'ekit_button_two_icons__switch' => 'yes',
                        'ekit_double_button_two_icon_position' => 'before',
                    ]
                ]
            );

            $this->add_responsive_control(
                'ekit_double_button_two_icon_after_specing',
                [
                    'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
                    'type'  => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'max' => 150,
                        ],
                    ],
                    'default' => [
                        'size' => 8,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two > i'  => 'padding-left: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two > svg'  => 'margin-left: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'ekit_button_two_icons__switch' => 'yes',
                        'ekit_double_button_two_icon_position' => 'after',
                    ]
                ]
            );

        $this->end_controls_section(); // Button Two End


        // Button One Style tab Start
        $this->start_controls_section(
            'ekit_double_button_one_style_section',
            [
                'label' => esc_html__( 'Button One', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->start_controls_tabs('ekit_double_button_one_style_tabs');

                // Button Default Normal style start
                $this->start_controls_tab(
                    'ekit_double_button_one_style_normal_tab',
                    [
                        'label' => esc_html__( 'Normal', 'elementskit-lite' ),
                    ]
                );

                    $this->add_control(
                        'ekit_double_button_one_color',
                        [
                            'label'     => esc_html__( 'Color', 'elementskit-lite' ),
                            'type'      => Controls_Manager::COLOR,
                            'default'   =>'#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Typography::get_type(),
                        [
                            'name' => 'ekit_double_button_one_typography',
                            'label' => esc_html__( 'Typography', 'elementskit-lite' ),
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one',
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_one_icon_font_size',
                        [
                            'label' => esc_html__( 'Icon font size', 'elementskit-lite' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px' ],
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one > i' => 'font-size: {{SIZE}}{{UNIT}};',
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one > svg' => 'max-width: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'ekit_double_button_one_border',
                            'label' => esc_html__( 'Border', 'elementskit-lite' ),
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one',
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_one_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'ekit_double_button_one_background',
                            'label' => esc_html__( 'Background', 'elementskit-lite' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one',
                            'separator' => 'before',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Box_Shadow::get_type(),
                        [
                            'name' => 'ekit_double_button_one_box_shadow',
                            'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one',
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_one_padding',
                        [
                            'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                            'separator' => 'before',
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_one_margin',
                        [
                            'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_one_align',
                        [
                            'label' => esc_html__( 'Alignment', 'elementskit-lite' ),
                            'type' => Controls_Manager::CHOOSE,
                            'options' => [
                                'start' => [
                                    'title' => esc_html__( 'Left', 'elementskit-lite' ),
                                    'icon' => 'eicon-text-align-left',
                                ],
                                'center' => [
                                    'title' => esc_html__( 'Center', 'elementskit-lite' ),
                                    'icon' => 'eicon-text-align-center',
                                ],
                                'end' => [
                                    'title' => esc_html__( 'Right', 'elementskit-lite' ),
                                    'icon' => 'eicon-text-align-right',
                                ],
                                'justify' => [
                                    'title' => esc_html__( 'Justified', 'elementskit-lite' ),
                                    'icon' => 'eicon-text-align-justify',
                                ],
                            ],
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one' => 'text-align: {{VALUE}};',
                            ],
                        ]
                    );

                $this->end_controls_tab(); // Button One Normal style End

                // Button One Hover style start
                $this->start_controls_tab(
                    'ekit_double_button_one_style_hover_tab',
                    [
                        'label' => esc_html__( 'Hover', 'elementskit-lite' ),
                    ]
                );

                    $this->add_control(
                        'ekit_double_button_one_hover_color',
                        [
                            'label'     => esc_html__( 'Color', 'elementskit-lite' ),
                            'type'      => Controls_Manager::COLOR,
                            'default'   =>'#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one:hover' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one:hover svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'ekit_double_button_one_hover_border',
                            'label' => esc_html__( 'Border', 'elementskit-lite' ),
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one:hover',
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_one_hover_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'ekit_double_button_one_hover_background',
                            'label' => esc_html__( 'Background', 'elementskit-lite' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one:hover',
                            'separator' => 'before',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Box_Shadow::get_type(),
                        [
                            'name' => 'ekit_double_button_one_hover_box_shadow',
                            'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-one:hover',
                        ]
                    );

                $this->end_controls_tab(); // Button one Hover style End

            $this->end_controls_tabs();

        $this->end_controls_section(); // Button One Style tab end

        // Button two Style tab Start
        $this->start_controls_section(
            'ekit_double_button_two_style_section',
            [
                'label' => esc_html__( 'Button Two', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->start_controls_tabs('ekit_double_button_two_style_tabs');

                // Button Two Normal style start
                $this->start_controls_tab(
                    'ekit_double_button_two_style_normal_tab',
                    [
                        'label' => esc_html__( 'Normal', 'elementskit-lite' ),
                    ]
                );

                    $this->add_control(
                        'ekit_double_button_two_color',
                        [
                            'label'     => esc_html__( 'Color', 'elementskit-lite' ),
                            'type'      => Controls_Manager::COLOR,
                            'default'   =>'#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Typography::get_type(),
                        [
                            'name' => 'ekit_double_button_two_typography',
                            'label' => esc_html__( 'Typography', 'elementskit-lite' ),
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two',
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_two_icon_font_size',
                        [
                            'label' => esc_html__( 'Icon font size', 'elementskit-lite' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px' ],
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two > i' => 'font-size: {{SIZE}}{{UNIT}};',
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two > svg' => 'max-width: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'ekit_double_button_two_border',
                            'label' => esc_html__( 'Border', 'elementskit-lite' ),
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two',
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_two_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'ekit_double_button_two_background',
                            'label' => esc_html__( 'Background', 'elementskit-lite' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two',
                            'separator' => 'before',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Box_Shadow::get_type(),
                        [
                            'name' => 'ekit_double_button_two_box_shadow',
                            'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two',
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_two_padding',
                        [
                            'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                            'separator' => 'before',
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_two_margin',
                        [
                            'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_two_align',
                        [
                            'label' => esc_html__( 'Alignment', 'elementskit-lite' ),
                            'type' => Controls_Manager::CHOOSE,
                            'options' => [
                                'start' => [
                                    'title' => esc_html__( 'Left', 'elementskit-lite' ),
                                    'icon' => 'eicon-text-align-left',
                                ],
                                'center' => [
                                    'title' => esc_html__( 'Center', 'elementskit-lite' ),
                                    'icon' => 'eicon-text-align-center',
                                ],
                                'end' => [
                                    'title' => esc_html__( 'Right', 'elementskit-lite' ),
                                    'icon' => 'eicon-text-align-right',
                                ],
                                'justify' => [
                                    'title' => esc_html__( 'Justified', 'elementskit-lite' ),
                                    'icon' => 'eicon-text-align-justify',
                                ],
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two' => 'text-align: {{VALUE}};',
                            ],
                        ]
                    );

                $this->end_controls_tab(); // Button two Normal style End

                // Button Two Hover style start
                $this->start_controls_tab(
                    'ekit_double_button_two_style_hover_tab',
                    [
                        'label' => esc_html__( 'Hover', 'elementskit-lite' ),
                    ]
                );
                    $this->add_control(
                        'ekit_double_button_two_hover_color',
                        [
                            'label'     => esc_html__( 'Color', 'elementskit-lite' ),
                            'type'      => Controls_Manager::COLOR,
                            'default'   =>'#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two:hover' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two:hover svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'ekit_double_button_two_hover_border',
                            'label' => esc_html__( 'Border', 'elementskit-lite' ),
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two:hover',
                        ]
                    );

                    $this->add_responsive_control(
                        'ekit_double_button_two_hover_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'ekit_double_button_two_hover_background',
                            'label' => esc_html__( 'Background', 'elementskit-lite' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two:hover',
                            'separator' => 'before',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Box_Shadow::get_type(),
                        [
                            'name' => 'ekit_double_button_two_hover_box_shadow',
                            'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                            'selector' => '{{WRAPPER}} .ekit-double-btn.ekit-double-btn-two:hover',
                        ]
                    );

                $this->end_controls_tab(); // Button two Hover style End

            $this->end_controls_tabs();

        $this->end_controls_section(); // Button two Style tab end

        // Button Middle Text style start
        $this->start_controls_section(
            'ekit_double_button_middletext_style_section',
            [
                'label' => esc_html__( 'Middle Text', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'ekit_show_button_middle_text'=>'yes',
                    'ekit_button_middle_text!'=>'',
                ]
            ]
        );
            $this->add_control(
                'ekit_double_button_middletext_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit-lite' ),
                    'type'      => Controls_Manager::COLOR,
                    'default'   =>'#000000',
                    'selectors' => [
                        '{{WRAPPER}} .ekit-wid-con .ekit_button_middle_text' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'ekit_double_button_middletext_typography',
                    'label' => esc_html__( 'Typography', 'elementskit-lite' ),
                    'selector' => '{{WRAPPER}} .ekit-wid-con .ekit_button_middle_text',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'ekit_double_button_middletext_border',
                    'label' => esc_html__( 'Border', 'elementskit-lite' ),
                    'selector' => '{{WRAPPER}} .ekit-wid-con .ekit_button_middle_text',
                ]
            );

            $this->add_responsive_control(
                'ekit_double_button_middletext_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit-wid-con .ekit_button_middle_text' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'ekit_double_button_middletext_background',
                    'label' => esc_html__( 'Background', 'elementskit-lite' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .ekit-wid-con .ekit_button_middle_text',
                    'separator' => 'before',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'ekit_double_button_middletext_box_shadow',
                    'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                    'selector' => '{{WRAPPER}} .ekit-wid-con .ekit_button_middle_text',
                ]
            );

            $this->add_responsive_control(
                'ekit_double_button_middletext_width',
                [
                    'label' => esc_html__( 'Width', 'elementskit-lite' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 10,
                            'max' => 140,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 40,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit-wid-con .ekit_button_middle_text' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'ekit_double_button_middletext_height',
                [
                    'label' => esc_html__( 'Height', 'elementskit-lite' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 10,
                            'max' => 140,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 40,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit-wid-con .ekit_button_middle_text' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section(); //Button Middle Text Style tab end

        $this->insert_pro_message();
    }

    protected function render( ) {
        echo '<div class="ekit-wid-con" >
                <div class="ekit-element-align-wrapper">
                    <div class="ekit_double_button">';
            $this->render_raw();
        echo '      </div>
                </div>
            </div>';
    }

    protected function render_raw( ) {
		$settings = $this->get_settings_for_display();

        // fw_print($settings['ekit_button_one_icons']);
        // fw_print($settings['ekit_button_one_link']);

        // Button One
        if ( ! empty( $settings['ekit_button_one_link']['url'] ) ) {

            // new icon
            $migrated = isset( $settings['__fa4_migrated']['ekit_button_one_icons'] );
            // Check if its a new widget without previously selected icon using the old Icon control
            $is_new = empty( $settings['ekit_button_one_icon'] );


            if ( ! empty( $settings['ekit_button_one_link']['url'] ) ) {
                $this->add_link_attributes( 'button-1', $settings['ekit_button_one_link'] );
            }

            ?>
                <a class="ekit-double-btn ekit-double-btn-one" <?php echo $this->get_render_attribute_string( 'button-1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
                    <?php 
                        if ($settings['ekit_double_button_one_icon_position'] == 'before') {
                            if ($is_new || $migrated) {
                                Icons_Manager::render_icon( $settings['ekit_button_one_icons'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                echo '<i class="'. esc_attr($settings['ekit_button_one_icon']) .'" aria-hidden="true"></i>';
                            }
                        }
                        echo esc_html($settings['ekit_button_one_text']); 
                        if ($settings['ekit_double_button_one_icon_position'] == 'after') {
                            if ($is_new || $migrated) {
                                Icons_Manager::render_icon( $settings['ekit_button_one_icons'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                echo '<i class="'. esc_attr($settings['ekit_button_one_icon']) .'" aria-hidden="true"></i>';
                            }
                        }
                    ?>
                </a>
            <?php
        }


        if( $settings['ekit_show_button_middle_text'] == 'yes' && !empty( $settings['ekit_button_middle_text'] ) ){
            echo "<span class='ekit_button_middle_text'>". esc_html($settings['ekit_button_middle_text']) ."</span>";
        }

        // Button Two
        // new icon
        $migrated = isset( $settings['__fa4_migrated']['ekit_button_two_icons'] );
        // Check if its a new widget without previously selected icon using the old Icon control
        $is_new = empty( $settings['ekit_button_two_icon'] );

        if ( ! empty( $settings['ekit_button_two_link']['url'] ) ) {

            if ( ! empty( $settings['ekit_button_two_link']['url'] ) ) {
                $this->add_link_attributes( 'button-2', $settings['ekit_button_two_link'] );
            }
            ?>

            <a class="ekit-double-btn ekit-double-btn-two" <?php echo $this->get_render_attribute_string( 'button-2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
                    <?php 
                        if ($settings['ekit_double_button_two_icon_position'] == 'before') {
                            if ($is_new || $migrated) {
                                Icons_Manager::render_icon( $settings['ekit_button_two_icons'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                echo '<i class="'. esc_attr($settings['ekit_button_two_icon']) .'" aria-hidden="true"></i>';
                            }
                        }
                        echo esc_html($settings['ekit_button_two_text']); 
                        if ($settings['ekit_double_button_two_icon_position'] == 'after') {
                            if ($is_new || $migrated) {
                                Icons_Manager::render_icon( $settings['ekit_button_two_icons'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                echo '<i class="'. esc_attr($settings['ekit_button_two_icon']) .'" aria-hidden="true"></i>';
                            }
                        }
                    ?>
                </a>
            <?php
        }
    }
}
