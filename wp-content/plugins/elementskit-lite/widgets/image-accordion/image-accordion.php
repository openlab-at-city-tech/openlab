<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Image_Accordion_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Image_Accordion extends Widget_Base {
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
        return 'https://wpmet.com/doc/image-accordion/';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'ekit_img_accordion_content_tab',
            [
                'label' => esc_html__('Content', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
            $repeater = new \Elementor\Repeater();

            $repeater->add_control(
                'ekit_img_accordion_active',
                [
                    'label'     => esc_html__('Active ? ', 'elementskit-lite'),
                    'type'      => Controls_Manager::SWITCHER,
                    'default'   => 'no',
                    'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
                    'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_bg',
                [
                    'label'     => esc_html__( 'Background Image', 'elementskit-lite' ),
                    'type'      => Controls_Manager::MEDIA,
					'dynamic'	=> [
						'active' => true,
					],
                    'default'   => [
                        'url' => Utils::get_placeholder_image_src(),
                        'id'    => -1
                    ],
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_title',
                [
                    'label'         => esc_html__('Title', 'elementskit-lite'),
                    'type'          => Controls_Manager::TEXT,
					'dynamic'		=> [
						'active' => true,
					],
                    'label_block'   => true,
                    'default'       => esc_html__('Image accordion Title', 'elementskit-lite'),
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_enable_icon',
                [
                    'label'         => esc_html__( 'Enable Icon', 'elementskit-lite' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => esc_html__( 'Yes', 'elementskit-lite' ),
                    'label_off'     => esc_html__( 'No', 'elementskit-lite' ),
                    'return_value'  => 'yes',
                    'default'       => '',
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_title_icons',
                [
                    'label'             => esc_html__('Icon for title', 'elementskit-lite'),
                    'type'              => Controls_Manager::ICONS,
                    'fa4compatibility'  => 'ekit_img_accordion_title_icon',
                    'default'           => [
                        'value' => '',
                    ],
                    'condition'         => [
                        'ekit_img_accordion_enable_icon' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_title_icon_position',
                [
                    'label'     => esc_html__( 'Icon Position', 'elementskit-lite' ),
                    'type'      => Controls_Manager::SELECT,
                    'default'   => 'left',
                    'options'   => [
                        'left'      => esc_html__( 'Before', 'elementskit-lite' ),
                        'right'     => esc_html__( 'After', 'elementskit-lite' ),
                    ],
                    'condition' => [
                        'ekit_img_accordion_title_icons!' => '',
                        'ekit_img_accordion_enable_icon' => 'yes',
                    ],
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_enable_wrap_link',
                [
                    'label'         => esc_html__( 'Enable Wrap Link', 'elementskit-lite' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => esc_html__( 'Yes', 'elementskit-lite' ),
                    'label_off'     => esc_html__( 'No', 'elementskit-lite' ),
                    'return_value'  => 'yes',
                    'default'       => 'no',
                    'separator'     => 'before',
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_wrap_link_url',
                [
                    'label'     => esc_html__('Wrap URL', 'elementskit-lite'),
                    'type'      => Controls_Manager::URL,
					'dynamic'	=> [
						'active' => true,
					],
                    'condition' => [
                        'ekit_img_accordion_enable_wrap_link' => 'yes',
                    ],
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_enable_button',
                [
                    'label'         => esc_html__( 'Enable Button', 'elementskit-lite' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => esc_html__( 'Yes', 'elementskit-lite' ),
                    'label_off'     => esc_html__( 'No', 'elementskit-lite' ),
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                    'separator'     => 'before',
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_button_label',
                [
                    'label'         => esc_html__('Button Label', 'elementskit-lite'),
                    'type'          => Controls_Manager::TEXT,
					'dynamic'		=> [
						'active' => true,
					],
                    'label_block'   => true,
                    'default'       => esc_html__('Read More','elementskit-lite'),
                    'condition'     => [
                        'ekit_img_accordion_enable_button' => 'yes',
                    ],
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_button_url',
                [
                    'label'     => esc_html__('Button URL', 'elementskit-lite'),
                    'type'      => Controls_Manager::URL,
					'dynamic'	=> [
						'active' => true,
					],
                    'condition' => [
                        'ekit_img_accordion_enable_button' => 'yes',
                    ],
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_enable_pupup',
                [
                    'label'         => esc_html__( 'Enable Popup', 'elementskit-lite' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => esc_html__( 'Yes', 'elementskit-lite' ),
                    'label_off'     => esc_html__( 'No', 'elementskit-lite' ),
                    'return_value'  => 'yes',
                    'default'       => '',
                    'separator'     => 'before',
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_pup_up_icons',
                [
                    'label'             => esc_html__('Pupup Icon', 'elementskit-lite'),
                    'type'              => Controls_Manager::ICONS,
                    'fa4compatibility'  => 'ekit_img_accordion_pup_up_icon',
                    'default'           => [
                        'value'     => 'icon icon-plus',
                        'library'   => 'ekiticons'
                    ],
                    'label_block'       => true,
                    'condition'         => [
                        'ekit_img_accordion_enable_pupup' => 'yes'
                    ]
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_enable_project_link',
                [
                    'label'         => esc_html__( 'Enable Project Link', 'elementskit-lite' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => esc_html__( 'Yes', 'elementskit-lite' ),
                    'label_off'     => esc_html__( 'No', 'elementskit-lite' ),
                    'return_value'  => 'yes',
                    'separator'     => 'before',
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_project_link',
                [
                    'label'         => esc_html__( 'Project Link', 'elementskit-lite' ),
                    'type'          => Controls_Manager::URL,
					'dynamic'		=> [
						'active' => true,
					],
                    'placeholder'   => esc_html__( 'https://wpmet.com', 'elementskit-lite' ),
                    'condition'     => [
                        'ekit_img_accordion_enable_project_link' => 'yes'
                    ],
                ]
            );

            $repeater->add_control(
                'ekit_img_accordion_project_link_icons',
                [
                    'label'             => esc_html__('Project Link Icon', 'elementskit-lite'),
                    'type'              => Controls_Manager::ICONS,
                    'fa4compatibility'  => 'ekit_img_accordion_project_link_icon',
                    'default'           => [
                        'value' => 'icon icon icon-link',
                        'library'   => 'ekiticons'
                    ],
                    'label_block'       => true,
                    'condition'         => [
                        'ekit_img_accordion_enable_project_link' => 'yes'
                    ],
                ]
            );

            $this->add_control(
                'ekit_img_accordion_items',
                [
                    'label' => esc_html__('Accordion Items', 'elementskit-lite'),
                    'type' => Controls_Manager::REPEATER,
                    'default' => [
                        [ 'ekit_img_accordion_title' => esc_html__('This is title','elementskit-lite') ],
                        [ 'ekit_img_accordion_icon' => esc_attr('icon icon-minus') ],
                        [ 'ekit_img_accordion_link' => esc_url('#') ],
                        [ 'ekit_img_accordion_button_label' => esc_html__('Read More','elementskit-lite') ],
                    ],
                    'fields' => $repeater->get_controls(),
                    'title_field' => '{{ ekit_img_accordion_title }}',
                ]
            );

            $this->add_responsive_control(
                'items_style',
                [
                    'label'         => esc_html__('Style', 'elementskit-lite'),
                    'type'          => Controls_Manager::SELECT,
                    'options'       => [
                        ''              => esc_html__('Default', 'elementskit-lite'),
                        'horizontal'    => esc_html__('Horizontal', 'elementskit-lite'),
                        'vertical'      => esc_html__('Vertical', 'elementskit-lite'),
                    ],
                    'default'       => 'horizontal',
                    'prefix_class'  => 'ekit-image-accordion%s-',
                ]
            );

            $this->add_control(
                'active_behavior',
                [
                    'label'         => esc_html__('Active Behaivor', 'elementskit-lite'),
                    'type'          => Controls_Manager::SELECT,
                    'options'       => [
                        'click' => esc_html__('Click', 'elementskit-lite'),
                        'hover' => esc_html__('Hover', 'elementskit-lite'),
                    ],
                    'default'       => 'click',
                    'prefix_class'  => 'ekit-image-accordion-',
                ]
            );
        $this->end_controls_section();

        /** Tab Style (Image accordion General Style) */
      $this->start_controls_section(
        'ekit_img_accordion_general_settings',
        [
          'label' => esc_html__( 'General', 'elementskit-lite' ),
          'tab' => Controls_Manager::TAB_STYLE
        ]
      );

      $this->add_responsive_control(
        'ekit_img_accordion_min_height',
        [
            'label' => esc_html__( 'Min Height', 'elementskit-lite' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],

            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 460,
            ],
            'selectors' => [
                '{{WRAPPER}} .elementskit-single-image-accordion' => 'min-height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .elementskit-image-accordion-wraper' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]
    );


      $this->add_responsive_control(
        'ekit_img_accordion_gutter',
        [
          'label' => esc_html__( 'Gutter', 'elementskit-lite' ),
          'type' => Controls_Manager::SLIDER,
          'range' => [
            'px' => [
                'min' => 0,
                'max' => 100,
            ],
          ],
          'selectors' => [
              '{{WRAPPER}} .elementskit-image-accordion-wraper .elementskit-single-image-accordion' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
              '{{WRAPPER}} .elementskit-image-accordion-wraper' => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
          ],
        ]
      );
      
	   $this->add_control(
        'ekit_img_accordion_active_background_text',
        [
          'label' => esc_html__( 'Active Item Background', 'elementskit-lite' ),
          'type' => Controls_Manager::HEADING,
          'separator' => 'before'
        ]
      );

      $this->add_group_control(
        Group_Control_Background::get_type(),
            array(
                'name'     => 'ekit_img_accordion_bg_active_color',
                'selector' => '{{WRAPPER}} .elementskit-single-image-accordion:before',
			)
        );
      $this->add_responsive_control(
        'ekit_img_accordion_container_padding',
        [
          'label' => esc_html__( 'Padding', 'elementskit-lite' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', 'em', '%' ],
		  'separator' => 'before',
          'selectors' => [
              '{{WRAPPER}} .elementskit-image-accordion-wraper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
        ]
      );

      $this->add_responsive_control(
        'ekit_img_accordion_container_margin',
        [
          'label' => esc_html__( 'Margin', 'elementskit-lite' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', 'em', '%' ],
          'selectors' => [
              '{{WRAPPER}} .elementskit-image-accordion-wraper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
        ]
      );
      $this->add_group_control(
        Group_Control_Border::get_type(),
        [
          'name' => 'ekit_img_accordion_border_group',
          'label' => esc_html__( 'Border', 'elementskit-lite' ),
          'selector' => '{{WRAPPER}} .elementskit-image-accordion-wraper',
        ]
      );

      $this->add_control(
        'ekit_img_accordion_border_radius',
        [
          'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
          'type' => Controls_Manager::SLIDER,
          'range' => [
            'px' => [
              'max' => 500,
            ],
          ],
          'selectors' => [
            '{{WRAPPER}} .elementskit-image-accordion-wraper' => 'border-radius: {{SIZE}}px;',
          ],
        ]
      );
      $this->add_group_control(
        Group_Control_Box_Shadow::get_type(),
        [
          'name' => 'ekit_img_accordion_shadow',
          'selector' => '{{WRAPPER}} .elementskit-image-accordion-wraper',
        ]
      );

      $this->end_controls_section();


        /** Tab Style (Image accordion Content Style) */
        $this->start_controls_section(
            'ekit_img_accordion_section_img_accordion_title_settings',
            [
            'label' => esc_html__( 'Title', 'elementskit-lite' ),
            'tab' => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_control(
            'ekit_img_accordion_section_img_accordion_icon_title',
            [
                'label' => esc_html_x( 'Margin', 'Border Control', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
				'default' => [
					'top' => '0',
					'bottom' => '20',
					'left' => '0',
					'right' => '0',
					'unit' => 'px',
				],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-image-accordion .elementskit-accordion-title-wraper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'ekit_img_accordion_section_img_accordion_title_icon_spacing',
            [
                'label' => esc_html_x( 'Title Icon Spacing', 'Border Control', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-image-accordion .elementskit-accordion-title-wraper .icon-title > i, {{WRAPPER}} .elementskit-single-image-accordion .elementskit-accordion-title-wraper .icon-title > svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_control(
			'ekit_img_accordion_title_color',
			[
			  'label' => esc_html__( 'Color', 'elementskit-lite' ),
			  'type' => Controls_Manager::COLOR,
			  'default' => '#fff',
			  'selectors' => [
                '{{WRAPPER}} .elementskit-image-accordion-wraper .elementskit-accordion-title-wraper .elementskit-accordion-title ' => 'color: {{VALUE}};',
                '{{WRAPPER}} .elementskit-image-accordion-wraper .elementskit-accordion-title-wraper .elementskit-accordion-title svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
			  ],
			]
          );
          
          $this->add_responsive_control(
            'ekit_img_accordion_title_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-image-accordion-wraper .elementskit-accordion-title-wraper .elementskit-accordion-title i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementskit-image-accordion-wraper .elementskit-accordion-title-wraper .elementskit-accordion-title svg' => 'max-width: {{SIZE}}{{UNIT}}; height: auto',
                ],
            ]
        );

		  $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			  'name' => 'ekit_img_accordion_title_typography_group',
			  'selector' => '{{WRAPPER}} .elementskit-image-accordion-wraper .elementskit-accordion-title-wraper .elementskit-accordion-title',
			]
		  );

      $this->end_controls_section();

        /** Tab Style (Image accordion Content Style) */
        $this->start_controls_section(
            'ekit_img_accordion_section_img_accordion_content_settings',
            [
            'label' => esc_html__( 'Content', 'elementskit-lite' ),
            'tab' => Controls_Manager::TAB_STYLE
            ]
        );
        $this->add_responsive_control(
            'ekit_img_accordion_section_img_accordion_content_align',
            [
                'label' =>esc_html__( 'Alignment', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left'    => [
                        'title' =>esc_html__( 'Left', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' =>esc_html__( 'Center', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' =>esc_html__( 'Right', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-image-accordion .elementskit-accordion-content' => 'text-align: {{VALUE}};'
                ],
                'default' => 'center',
            ]
        );
        $this->add_responsive_control(
            'ekit_img_accordion_section_img_accordion_content_padding',
            [
                'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-image-accordion .elementskit-accordion-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_img_accordion_section_img_accordion_content_position',
            [
                'label' => esc_html__( 'Vertical Position', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__( 'Top', 'elementskit-lite' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'elementskit-lite' ),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => esc_html__( 'Bottom', 'elementskit-lite' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-image-accordion-wraper .elementskit-single-image-accordion' => 'align-items: {{VALUE}}',
                ],
            ]
        );


      $this->end_controls_section();

        // Button
        $this->start_controls_section(
            'ekit_img_accordion_button_style_settings',
            [
                'label' => esc_html__( 'Button', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'ekit_img_accordion_text_padding',
            [
                'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'top' => 15,
                    'right' => 20,
                    'bottom' => 15,
                    'left' => 20,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-accordion-content .elementskit-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_img_accordion_btn_typography',
                'label' =>esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-accordion-content .elementskit-btn',
            ]
        );

        $this->start_controls_tabs( 'ekit_img_accordion_tabs_button_style' );

        $this->start_controls_tab(
            'ekit_img_accordion_tab_button_normal',
            [
                'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_img_accordion_btn_text_color',
            [
                'label' =>esc_html__( 'Text Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-accordion-content .elementskit-btn' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'ekit_img_accordion_btn_bg_color_group',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-accordion-content .elementskit-btn',
				'fields_options' => [
                    'background' => [
						'color' => [
								'default' => '#fff'
							],
                    ],

				],

            )
        );

		$this->add_control(
            'ekit_img_accordion_btn_border_color',
            [
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',

            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_img_accordion_btn_border_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-accordion-content .elementskit-btn',
				'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width' => [
                        'default' => [
                            'top' => '1',
                            'right' => '1',
                            'bottom' => '1',
                            'left' => '1',
                            'unit' => 'px'
                        ],
                    ],
                    'color' => [
                        'default' => '#ffffff',
                    ],
                ],
            ]
        );
        $this->add_control(
            'ekit_img_accordion_btn_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
				'default' => ['top' => '5', 'bottom' => '5', 'left' => '5', 'right' => '5', 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-accordion-content .elementskit-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_img_accordion_btn_tab_button_hover',
            [
                'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_img_accordion_btn_hover_color',
            [
                'label' =>esc_html__( 'Text Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-accordion-content .elementskit-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'ekit_img_accordion_btn_bg_hover_color_group',
                'selector' => '{{WRAPPER}} .elementskit-accordion-content .elementskit-btn:hover',
            )
        );
        $this->add_control(
            'ekit_img_accordion_btn_border_color_hover',
            [
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_img_accordion_btn_border_hover_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-accordion-content .elementskit-btn:hover',
            ]
        );
        $this->add_control(
            'btn_border_radius_hover',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-accordion-content .elementskit-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
        // PopUp

        $this->start_controls_section(
            'ekit_img_accordion_style_section',
            [
                'label' => esc_html__( 'Action Icon', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'actions_width',
            [
                'label'     => esc_html__( 'Width', 'elementskit-lite' ),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .ekit-image-accordion-actions > a' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'ekit_img_accordion_section_img_accordion_icon_left_spacing',
            [
                'label' => esc_html__( 'Icon Left Spacing', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-image-accordion .elementskit-icon-wraper > a:not(:last-child)' => 'margin-right: {{SIZE}}px',
                ],
            ]
        );

        $this->add_control(
            'ekit_img_accordion_section_img_accordion_icon_spacing',
            [
                'label' => esc_html_x( 'Icon Container Spacing', 'Border Control', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-image-accordion .elementskit-icon-wraper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'actions_border_width',
            [
                'label'         => esc_html__( 'Border Width', 'elementskit-lite' ),
                'type'          => Controls_Manager::NUMBER,
                'placeholder'   => '1',
                'selectors'     => [
                    '{{WRAPPER}} .ekit-image-accordion-actions > a' => 'border-width: {{VALUE}}px;',
                ],
            ]
        );

        $this->start_controls_tabs('ekit_img_accordion_pup_up_style_tabs');

        $this->start_controls_tab(
            'ekit_img_accordion_pupup_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );
        $this->add_control(
            'ekit_img_accordion_pup_up_icon_color',
            [
                'label' => esc_html__( 'Popup Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-icon-wraper a:first-child' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-icon-wraper a:first-child svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_img_accordion_pup_up_project_color',
            [
                'label' => esc_html__( 'Link Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-icon-wraper a:last-child' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-icon-wraper a:last-child svg path'   => 'fill: {{VALUE}};',
                ],
            ]
        );

            $this->add_control(
                'action_btn_bg',
                [
                    'label'     => esc_html__( 'Background Color', 'elementskit-lite' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-image-accordion-actions > a' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                    ],
                ]
            );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_img_accordion_pup_up_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_img_accordion_pup_up_icon_color_hover',
            [
                'label' => esc_html__( 'Popup Icon color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-icon-wraper a:first-child:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .elementskit-icon-wraper a:first-child:hover svg path'   => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_img_accordion_pup_up_project_color_hover',
            [
                'label' => esc_html__( 'Link Icon color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-icon-wraper a:last-child:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-icon-wraper a:last-child:hover svg path'   => 'fill: {{VALUE}};',
                ],
            ]
        );

            $this->add_control(
                'action_btn_bg_hover',
                [
                    'label'     => esc_html__( 'Background Color (Hover)', 'elementskit-lite' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-image-accordion-actions > a:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                    ],
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
        ?>
        <div class="ekit-image-accordion elementskit-image-accordion-wraper">
            <?php foreach ( $ekit_img_accordion_items as $key => $item ) :
                // enabling wrap link
                if(!Plugin::$instance->editor->is_edit_mode() && $item['ekit_img_accordion_enable_wrap_link'] == 'yes') {
                    $this->add_render_attribute( 'wrap-link-' . $key, 'data-link', json_encode($item['ekit_img_accordion_wrap_link_url']) );
                    $this->add_render_attribute( 'wrap-link-' . $key, 'data-behavior', $active_behavior );
                    $this->add_render_attribute( 'wrap-link-' . $key, 'data-active', $item['ekit_img_accordion_active'] );
                }
                ?>
                <input type="radio" name="ekit_ia_<?php echo esc_attr($this->get_id()); ?>" id="ekit_ia_<?php echo esc_attr($this->get_id()) .'_'. esc_attr($key); ?>" class="elementskit-single-image-accordion--input" <?php echo (($item['ekit_img_accordion_active'] == 'yes') ? 'checked' : '') ; ?> hidden>
                <label for="ekit_ia_<?php echo esc_attr($this->get_id()) .'_'. esc_attr($key); ?>" class="elementskit-single-image-accordion ekit-image-accordion-item" style="background-image: url(<?php echo esc_url($item['ekit_img_accordion_bg']['url']); ?>)" <?php $this->print_render_attribute_string( 'wrap-link-' . $key ); ?>>
                    <span class="elementskit-accordion-content">
                    <?php if($item['ekit_img_accordion_enable_pupup'] == 'yes' || $item['ekit_img_accordion_enable_project_link'] == 'yes') {


                        if (!empty($item['ekit_img_accordion_project_link']['url'])) {

                            $this->add_render_attribute('projectlink', 'href', $item['ekit_img_accordion_project_link']['url']);

                            if ($item['ekit_img_accordion_project_link']['is_external']) {
                                $this->add_render_attribute('projectlink', 'target', '_blank');
                            }

                            if (!empty($item['ekit_img_accordion_project_link']['nofollow'])) {
                                $this->add_render_attribute('projectlink', 'rel', 'nofollow');
                            }
                        }

                        ?>
                        <span class="elementskit-icon-wraper ekit-image-accordion-actions">
                        <?php if($item['ekit_img_accordion_enable_pupup'] == 'yes') { ?>
                                <a href="<?php echo esc_url($item['ekit_img_accordion_bg']['url']); ?>" class="icon-outline circle" data-elementor-open-lightbox="yes" aria-label="pupup-button">
                                <?php

                                    $migrated = isset( $item['__fa4_migrated']['ekit_img_accordion_pup_up_icons'] );
                                    // Check if its a new widget without previously selected icon using the old Icon control
                                    $is_new = empty( $item['ekit_img_accordion_pup_up_icon'] );
                                    if ( $is_new || $migrated ) {

                                        // new icon
                                        Icons_Manager::render_icon( $item['ekit_img_accordion_pup_up_icons'], [ 'aria-hidden' => 'true'] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($item['ekit_img_accordion_pup_up_icon']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }
                                ?>
                                </a>
                        <?php } ?>
                        <?php if($item['ekit_img_accordion_enable_project_link'] == 'yes') {
                                if ( ! empty( $item['ekit_img_accordion_project_link']['url'] ) ) {
                                    $this->add_link_attributes( 'button-2' . $key, $item['ekit_img_accordion_project_link'] );
									$this->add_render_attribute( 'button-2' . $key, ['role' => "link", 'aria-label' => "button-link"] );
                                }
                            ?>
                                <a <?php echo $this->get_render_attribute_string( 'button-2' . esc_attr($key) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?> class="icon-outline circle">
                                <?php
                                    $migrated = isset( $item['__fa4_migrated']['ekit_img_accordion_project_link_icons'] );
                                    // Check if its a new widget without previously selected icon using the old Icon control
                                    $is_new = empty( $item['ekit_img_accordion_project_link_icon'] );
                                    if ( $is_new || $migrated ) {

                                        // new icon
                                        Icons_Manager::render_icon( $item['ekit_img_accordion_project_link_icons'], [ 'aria-hidden' => 'true'] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($item['ekit_img_accordion_project_link_icon']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }
                                ?>
                                </a>
                            <?php } ?>
                        </span>
                        <?php } ?>
                        <span class="elementskit-accordion-title-wraper">
                            <span class="elementskit-accordion-title <?php echo esc_attr($item['ekit_img_accordion_title_icons'] != '') ? 'icon-title' : ''?>">
                            <?php if($item['ekit_img_accordion_enable_icon']  == 'yes'): ?>
                            <?php if($item['ekit_img_accordion_title_icon_position'] == 'left'): ?>
                                <!-- same-1 -->
                                <?php

                                    $migrated = isset( $item['__fa4_migrated']['ekit_img_accordion_title_icons'] );
                                    // Check if its a new widget without previously selected icon using the old Icon control
                                    $is_new = empty( $item['ekit_img_accordion_title_icon'] );
                                    if ( $is_new || $migrated ) {

                                        // new icon
                                        Icons_Manager::render_icon( $item['ekit_img_accordion_title_icons'], [ 'aria-hidden' => 'true'] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($item['ekit_img_accordion_title_icon']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }
                                ?>
                            <?php endif; ?>
                            <?php endif; ?>

                            <?php echo esc_html($item['ekit_img_accordion_title']); ?>

                            <?php if($item['ekit_img_accordion_enable_icon']  == 'yes'): ?>
                            <?php if($item['ekit_img_accordion_title_icon_position'] == 'right'): ?>
                                <!-- same-1 -->
                                <?php

                                    $migrated = isset( $item['__fa4_migrated']['ekit_img_accordion_title_icons'] );
                                    // Check if its a new widget without previously selected icon using the old Icon control
                                    $is_new = empty( $item['ekit_img_accordion_title_icon'] );
                                    if ( $is_new || $migrated ) {

                                        // new icon
                                        Icons_Manager::render_icon( $item['ekit_img_accordion_title_icons'], [ 'aria-hidden' => 'true'] );
                                    } else {
                                        ?>
                                        <i class="<?php echo esc_attr($item['ekit_img_accordion_title_icon']); ?>" aria-hidden="true"></i>
                                        <?php
                                    }
                                ?>
                            <?php endif; ?>
                            <?php endif; ?>
                            </span>
                        </span>
                        <?php if($item['ekit_img_accordion_enable_button'] == 'yes'):
                        
                            if ( ! empty( $item['ekit_img_accordion_button_url']['url'] ) ) {
                                $this->add_link_attributes( 'button-' . $key, $item['ekit_img_accordion_button_url'] );
                            }    
                        ?>
                            <span class="elementskit-btn-wraper">
                                <a class="ekit-image-accordion--btn elementskit-btn whitespace--normal" <?php echo $this->get_render_attribute_string( 'button-' . esc_attr($key) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
                                    <?php echo esc_html($item['ekit_img_accordion_button_label']); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
    <?php }
}
