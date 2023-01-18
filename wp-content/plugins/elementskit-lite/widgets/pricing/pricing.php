<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Pricing_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Pricing extends Widget_Base {
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

    public function get_help_url() {
        return 'https://wpmet.com/doc/pricing-table/';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'ekit_pricing_pricing_plan',
            [
                'label' => esc_html__('Header', 'elementskit-lite'),
            ]
        );


        $this->add_control(
			'ekit_pricing_table_title', [
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'label' => esc_html__('Table Title', 'elementskit-lite'),
                'default'   =>  esc_html__('Starter','elementskit-lite'),
				'label_block' => true,
			]
		);
		$this->add_control(
            'ekit_pricing_title_size',
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
                'separator' => 'after',
            ]
        );
        $this->add_control(
			'ekit_pricing_table_subtitle', [
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'label' => esc_html__('Table Subtitle', 'elementskit-lite'),
                'default'   =>  esc_html__('A small river named Duden flows by their place and supplies','elementskit-lite'),
				'label_block' => true,
			]
		);
		$this->add_control(
            'ekit_pricing_icon_type',
            [
                'label' => esc_html__( 'Header Icon or Image? ', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'none' => [
                        'title' => esc_html__( 'None', 'elementskit-lite' ),
                        'icon' => 'fa fa-stop-circle',
					],
					'icon' => [
                        'title' => esc_html__( 'Icon', 'elementskit-lite' ),
                        'icon' => 'fa fa-star',
                    ],
                    'image' => [
                        'title' => esc_html__( 'Image', 'elementskit-lite' ),
                        'icon' => 'fa fa-image',
                    ],
                ],
				'default' => 'none',
				'separator' => 'before',
				'toggle' => true,
            ]
		);
		
		$this->add_control(
            'ekit_pricing_icons__switch',
            [
                'label' => esc_html__('Add icon? ', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
				'condition' => [
                    'ekit_pricing_icon_type' => 'icon',
                ]
            ]
		);

        $this->add_control(
            'ekit_pricing_icons',
            [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_pricing_icon',
                'default' => [
                    'value' => 'fab fa-amazon',
                    'library' => 'brands',
                ],
                'condition' => [
					'ekit_pricing_icon_type' => 'icon',
					'ekit_pricing_icons__switch'	=> 'yes'
                ]
            ]
        );

        $this->add_control(
            'ekit_pricing_image',
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
                'condition' => [
                    'ekit_pricing_icon_type' => 'image',
				],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'ekit_pricing_thumbnail',
                'default' => 'thumbnail',
                'separator' => 'none',
                'condition' => [
                    'ekit_pricing_icon_type' => 'image',
                ]
            ]
        );
		$this->end_controls_section();
        $this->start_controls_section(
            'ekit_pricing_pricing_tag',
            [
                'label' => esc_html__('Price Tag', 'elementskit-lite'),
            ]
        );

	    $this->add_control(
			'ekit_pricing_currency_icon', [
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'label' => esc_html__('Currency', 'elementskit-lite'),
				'default'   => '$',
			]
		);
        $this->add_control(
			'ekit_pricing_table_price', [
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'label' => esc_html__('Price', 'elementskit-lite'),
				'default'   => esc_html__('5.99', 'elementskit-lite'),
			]
		);
        $this->add_control(
			'ekit_pricing_table_duration', [
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'label' => esc_html__('Duration', 'elementskit-lite'),
				'default'   => esc_html__('Month', 'elementskit-lite'),
			]
		);
		$this->end_controls_section();
        $this->start_controls_section(
            'ekit_pricing_features_tab',
            [
                'label' =>esc_html__('Features', 'elementskit-lite'),
            ]
        );
        $this->add_control(
            'ekit_pricing_content_style',
            [
                'label' => esc_html__( 'Features style', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'paragraph',
                'options' => [
                    'paragraph'  => esc_html__( 'Paragraph', 'elementskit-lite' ),
                    'list' => esc_html__( 'List', 'elementskit-lite' ),
                ],
            ]
        );

        $this->add_control(
			'ekit_pricing_table_content', [
                'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
                'label' => esc_html__('Table Content', 'elementskit-lite'),
				'label_block' => true,
				'default' => esc_html__('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam','elementskit-lite'),
                'condition' => [
                    'ekit_pricing_content_style' => 'paragraph',
				],
			]
        );
		$repeater = new Repeater();

        $repeater->add_control(
            'ekit_pricing_list', [
                'label' => esc_html__('List text', 'elementskit-lite'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( '15 Email Account' , 'elementskit-lite' ),
				'label_block' => true,
            ]
        );

        $repeater->add_control(
            'ekit_pricing_check_icons', [
				'label' =>esc_html__( 'Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => '',
                ],
				'label_block' => true,
            ]
        );

        $repeater->add_control(
            'ekit_pricing_list_icon_color', [
				'label' =>esc_html__( 'Icon Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists {{CURRENT_ITEM}} i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists {{CURRENT_ITEM}} svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};'
				],
            ]
		);
		
		$repeater->add_responsive_control(
            'ekit_pricing_list_content_typography_group',
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
                    '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists {{CURRENT_ITEM}} i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists {{CURRENT_ITEM}} svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$repeater->add_control(
			'ekit_pricing_list_info',
			[
				'label'	=> esc_html__( 'Info Text', 'elementskit-lite' ),
				'type'	=> Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);

        $this->add_control(
            'ekit_pricing_table_content_repeater',
            [
                'label' => esc_html__( 'Pricing Content List', 'elementskit-lite' ),
                'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{ekit_pricing_list}}',
                'default' => [
                    [
                        'item' => esc_html__( '15 Email Account', 'elementskit-lite' ),
                        'check_icon' => 'icon icon-tick',
                    ],
                    [
						'item' => esc_html__( '100 GB Space', 'elementskit-lite' ),
						'check_icon' => 'icon icon-tick',
                    ],
                    [
						'item' => esc_html__( '1 Domain Name', 'elementskit-lite' ),
						'check_icon' => 'icon icon-tick',
                    ],
                ],
                'title_field' => '{{{ ekit_pricing_list }}}',
                'condition' => [
                    'ekit_pricing_content_style' => 'list',
				],
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'ekit_pricing_button_style_tab',
            [
                'label' =>esc_html__('Button', 'elementskit-lite'),
            ]
        );
        $this->add_control(
			'ekit_pricing_btn_text',
			[
				'label' =>esc_html__( 'Label', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' =>esc_html__( 'Learn more ', 'elementskit-lite' ),
				'placeholder' =>esc_html__( 'Learn more ', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_pricing_btn_link',
			[
				'label' =>esc_html__( 'Link', 'elementskit-lite' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' =>esc_url('https://wpmet.com'),
				'default' => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
            'ekit_pricing_btn_icons__switch',
            [
                'label' => esc_html__('Add icon? ', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
            ]
		);

		$this->add_control(
			'ekit_pricing_btn_icons',
			[
				'label' =>esc_html__( 'Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_pricing_btn_icon',
                'default' => [
                    'value' => '',
                ],
				'label_block' => true,
				'condition'		=> [
					'ekit_pricing_btn_icons__switch'	=> 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_pricing_icon_align',
			[
				'label' =>esc_html__( 'Icon Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' =>esc_html__( 'Before', 'elementskit-lite' ),
					'right' =>esc_html__( 'After', 'elementskit-lite' ),
				],
				'condition' => [
					'ekit_pricing_btn_icons__switch'	=> 'yes'
				],
			]
		);

		$this->add_responsive_control(
            'ekit_pricing_icon_spacing',
            [
                'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-pricing a.ekit-pricing-btn-icon-pos-left i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-single-pricing a.ekit-pricing-btn-icon-pos-right i' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-single-pricing a.ekit-pricing-btn-icon-pos-left svg' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementskit-single-pricing a.ekit-pricing-btn-icon-pos-right svg' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_pricing_btn_icons__switch'	=> 'yes'
                ],
            ]
        );

	    $this->add_control(
		    'ekit_pricing_button_class',
		    [
			    'label' => esc_html__( 'Class', 'elementskit-lite' ),
			    'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			    'placeholder' => esc_html__( 'Class Name', 'elementskit-lite' ),
		    ]
	    );

	    $this->add_control(
		    'ekit_pricing_button_id',
		    [
			    'label' => esc_html__( 'id', 'elementskit-lite' ),
			    'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			    'placeholder' => esc_html__( 'ID', 'elementskit-lite' ),
		    ]
	    );


        $this->end_controls_section();


        //Body style start
        $this->start_controls_section(
			'ekit_pricing_section_body_style',
			[
				'label' =>esc_html__( 'Pricing Body', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_pricing_pricing_body_bg_sp', [
				'type' => Controls_Manager::COLOR,
                'label' => esc_html__('Background Color', 'elementskit-lite'),
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_pricing_pricing_content_align',
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
                    '{{WRAPPER}} .elementskit-single-pricing' => 'text-align: {{VALUE}};'
                ],
				'default' => 'center',
			]
		);

		$this->end_controls_section();



        //Price Title style start
        $this->start_controls_section(
			'sekit_pricing_ection_title_style',
			[
				'label' =>esc_html__( 'Table Title', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'ekit_pricing_title_align',
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
                    '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-title' => 'text-align: {{VALUE}};'
                ],
				'default' => '',
			]
		);
        $this->start_controls_tabs( 'ekit_pricing_tabs_title_style' );

        $this->start_controls_tab(
            'ekit_pricing_tab_title_normal',
            [
                'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );
        $this->add_control(
            'ekit_pricing_title_text_color',
            [
                'label' =>esc_html__( 'Title Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'ekit_pricing_tab_title_hover',
            [
                'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );
        $this->add_control(
            'ekit_pricing_title_hover_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}:hover .elementskit-pricing-header .elementskit-pricing-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_pricing_title_typography_group',
                'label' =>esc_html__( 'Title Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-title',
            ]
        );
		$this->add_responsive_control(
			'ekit_pricing_title_text_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_pricing_title_wraper_margin',
			[
				'label' =>esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->add_control(
			'ekit_pricing_titlehr12',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

        $this->add_responsive_control(
			'ekit_pricing_title_border_style',
			[
				'label' => esc_html_x( 'Border Type', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'elementskit-lite' ),
					'solid' => esc_html_x( 'Solid', 'Border Control', 'elementskit-lite' ),
					'double' => esc_html_x( 'Double', 'Border Control', 'elementskit-lite' ),
					'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementskit-lite' ),
					'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementskit-lite' ),
					'groove' => esc_html_x( 'Groove', 'Border Control', 'elementskit-lite' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-title' => 'border-style: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_pricing_title_border_dimensions',
			[
				'label' => esc_html_x( 'Border Width', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-title' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_pricing_title_border_style!' => '',
                ],

            ]
		);
		$this->start_controls_tabs( 'ekit_pricing_tabs_title_border_style' );
		$this->start_controls_tab(
			'ekit_pricing_title_border_normal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
                'condition' => [
                    'ekit_pricing_title_border_style!' => '',
                ],

            ]
		);
		$this->add_control(
			'ekit_pricing_title_border_color',
			[
				'label' => esc_html_x( 'Border Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-title' => 'border-color: {{VALUE}};',
				],
                'condition' => [
                    'ekit_pricing_title_border_style!' => '',
                ],

            ]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_pricing_title_tab_border_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
                'condition' => [
                    'ekit_pricing_title_border_style!' => '',
                ],

            ]
		);
		$this->add_control(
			'ekit_pricing_title_hover_border_color',
			[
				'label' => esc_html_x( 'Border Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}:hover .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-title' => 'border-color: {{VALUE}};',
				],
                'condition' => [
                    'ekit_pricing_title_border_style!' => '',
                ],
            ]
		);
		$this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
			'ekit_pricing_title_border_radius',
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
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-title' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_pricing_title_box_shadow_group',
                'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-title',
            ]
        );
		$this->end_controls_section();

        //Price Subtitle style start
        $this->start_controls_section(
			'ekit_pricing_section_subtitle_style',
			[
				'label' =>esc_html__( 'Table Subtitle', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_pricing_table_subtitle!' => '',
                ]
			]
		);
		$this->add_responsive_control(
			'ekit_pricing_subtitle_align',
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
                    '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-subtitle' => 'text-align: {{VALUE}};'
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'ekit_pricing_table_subtitle',
                            'operator' => '!in',
                            'value' => [''],
                        ],
                    ],
                ],
				'default' => '',
			]
		);
        $this->start_controls_tabs( 'ekit_pricing_tabs_subtitle_style' );

        $this->start_controls_tab(
            'ekit_pricing_tab_subtitle_normal',
            [
                'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );
        $this->add_control(
            'ekit_pricing_subtitle_text_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'ekit_pricing_tab_subtitle_hover',
            [
                'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );
        $this->add_control(
            'ekit_pricing_subtitle_hover_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}:hover .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_pricing_subtitle_typography_group',
                'label' =>esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-subtitle',
            ]
        );
		$this->add_responsive_control(
			'ekit_pricing_subtitle_text_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


        $this->add_responsive_control(
			'ekit_pricing_subtitlehr12',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

        $this->add_responsive_control(
			'ekit_pricing_subtitle_border_style',
			[
				'label' => esc_html_x( 'Border Type', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'elementskit-lite' ),
					'solid' => esc_html_x( 'Solid', 'Border Control', 'elementskit-lite' ),
					'double' => esc_html_x( 'Double', 'Border Control', 'elementskit-lite' ),
					'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementskit-lite' ),
					'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementskit-lite' ),
					'groove' => esc_html_x( 'Groove', 'Border Control', 'elementskit-lite' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-subtitle' => 'border-style: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_pricing_subtitle_border_dimensions',
			[
				'label' => esc_html_x( 'Width', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-subtitle' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_pricing_subtitle_border_style!' => '',
                ],
			]
		);
		$this->start_controls_tabs( 'ekit_pricing_tabs_subtitle_border_style' );
		$this->start_controls_tab(
			'ekit_pricing_subtitle_border_normal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
                'condition' => [
                    'ekit_pricing_subtitle_border_style!' => '',
                ],
			]
		);

		$this->add_control(
			'ekit_pricing_subtitle_border_color',
			[
				'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-subtitle' => 'border-color: {{VALUE}};',
				],
                'condition' => [
                    'ekit_pricing_subtitle_border_style!' => '',
                ],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_pricing_subtitle_tab_border_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
                'condition' => [
                    'ekit_pricing_subtitle_border_style!' => '',
                ],
			]
		);
		$this->add_control(
			'ekit_pricing_subtitle_hover_border_color',
			[
				'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}:hover .elementskit-pricing-header .elementskit-pricing-subtitle' => 'border-color: {{VALUE}};',
				],
                'condition' => [
                    'ekit_pricing_subtitle_border_style!' => '',
                ],
			]
		);
		$this->end_controls_tab();
        $this->end_controls_tabs();



        $this->add_control(
			'ekit_pricing_subtitlehr13',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);
        $this->add_responsive_control(
			'ekit_pricing_subtitle_border_radius',
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
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-subtitle' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_pricing_subtitle_box_shadow_group',
                'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-header .elementskit-pricing-subtitle',
            ]
        );
		$this->end_controls_section();

        //Image Style Start
        $this->start_controls_section(
            'ekit_pricing_style_image',
            [
                'label' => esc_html__( 'Header Image', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_pricing_icon_type' => 'image',

                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_pricing_image_space',
            [
                'label' => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-pricing .elementor-pricing-img img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->start_controls_tabs(
            'ekit_pricing_style_tabs_image'
        );

        $this->start_controls_tab(
            'ekit_pricing_style_img_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_pricing_imge_border_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementor-pricing-img img',
            ]
        );
        $this->add_responsive_control(
            'ekit_pricing_image_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-pricing .elementor-pricing-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_pricing_iamge_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementor-pricing-img img',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_pricing_style_img_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_pricing_imge_border_hover_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}}:hover .elementor-pricing-img img',
            ]
        );
        $this->add_responsive_control(
            'ekit_pricing_image_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-pricing .elementor-pricing-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_pricing_iamge_box_shadow_hv_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}}:hover .elementor-pricing-img img',
            ]
        );

        $this->add_control(
            'ekit_pricing_image_hover_animation',
            [
                'label' => esc_html__( 'Animation', 'elementskit-lite' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );


        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        //Icon Style Start
        $this->start_controls_section(
            'ekit_pricing_section_style_icon',
            [
                'label' => esc_html__( 'Header Icon', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
					'ekit_pricing_icons__switch'	=> 'yes',
                    'ekit_pricing_icon_type' => 'icon',

                ],
            ]
        );

        $this->start_controls_tabs( 'icon_colors' );

        $this->start_controls_tab(
            'ekit_pricing_icon_colors_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_pricing_icon_primary_color',
            [
                'label' => esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementkit-pricing-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-pricing-header svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_pricing_icon_secondary_color_normal',
            [
                'label' => esc_html__( 'BG Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-pricing .elementkit-pricing-icon, {{WRAPPER}} .elementskit-pricing-header svg' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_pricing_border_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementkit-pricing-icon, {{WRAPPER}} .elementskit-pricing-header svg',
            ]
        );

        $this->add_responsive_control(
            'ekit_pricing_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-pricing .elementkit-pricing-icon, {{WRAPPER}} .elementskit-pricing-header svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_pricing_icon_colors_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_pricing_hover_primary_color',
            [
                'label' => esc_html__( 'Primary Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}}:hover .elementkit-pricing-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}}:hover .elementskit-pricing-header svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_pricing_hover_secondary_color',
            [
                'label' => esc_html__( 'Secondary Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}}:hover .elementkit-pricing-icon, {{WRAPPER}}:hover .elementskit-pricing-header svg' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_pricing_border_icon_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}}:hover .elementkit-pricing-icon, {{WRAPPER}}:hover .elementskit-pricing-header svg',
                'condition' => [
                    'view!' => 'Stacked',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_pricing_icon_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}}:hover .elementkit-pricing-icon, {{WRAPPER}}:hover .elementskit-pricing-header svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'ekit_pricing_icons_hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'elementskit-lite' ),
                'type' =>   Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->add_responsive_control(
            'ekit_pricing_icon_size',
            [
                'label' => esc_html__( 'Size', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'default' => [
                    'size' => 40,
                    'unit' => 'px',
                ],
                'selectors' => [
					'{{WRAPPER}} .elementkit-pricing-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-pricing-header svg'	=> 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'ekit_pricing_icon_space',
            [
                'label' => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -20,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 15,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-pricing-icon, {{WRAPPER}} .elementskit-pricing-header svg' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_pricing_icon_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 15,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-pricing-icon, {{WRAPPER}} .elementskit-pricing-header svg' => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_pricing_rotate',
            [
                'label' => esc_html__( 'Rotate', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => 'deg',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-pricing-icon, {{WRAPPER}} .elementskit-pricing-header svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_pricing_icon_box_shadow_group',
                'selector' => '{{WRAPPER}} .elementkit-pricing-icon, {{WRAPPER}} .elementskit-pricing-header svg',
            ]
        );

        $this->end_controls_section();

        //Price Tag style start
        $this->start_controls_section(
			'ekit_pricing_section_tag_style',
			[
				'label' =>esc_html__( 'Price Tag', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_pricing_tag_right',
			[
				'label' => esc_html__( 'Right', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				// 'default' => [
				// 	'unit' => 'px',
				// 	'size' => 0,
				// ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-pricing-tag' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_pricing_tag_width_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-pricing-tag' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_pricing_tag_text_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => 	[
					'top' => '8',
					'right' => '0',
					'bottom' => '8',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_pricing_tag_text_margin',
			[
				'label' =>esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => 	[
					'top' => '0',
					'right' => '0',
					'bottom' => '50',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_pricing_price_typography_group',
				'label' =>esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-price span',
			]
		);
		$this->add_control(
			'ekit_pricing_heading_period_style',
			[
				'label' => esc_html__( 'Duration', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'ekit_pricing_period_text_color',
			[
				'label' =>esc_html__( 'Text Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-price .period' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ekit_pricing_period_text_color_hover',
			[
				'label' =>esc_html__( 'Text Hover Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}:hover .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-price .period' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_pricing_period_typography_group',
                'label' =>esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-price sub.period',
            ]
        );
		$this->add_responsive_control(
			'ekit_pricing_period_vertical_position',
			[
				'label' => esc_html__( 'Vertical Position', 'elementskit-lite' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'top',
				'selectors_dictionary' => [
					'top' => 'super',
					'middle' => 'baseline',
					'bottom' => 'sub',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-price sub.period' => 'vertical-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_pricing_heading_currency_style',
			[
				'label' => esc_html__( 'Currency Symbol', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_pricing_currency_size',
                'label' =>esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-price sup.currency',
            ]
        );

		$this->add_control(
			'ekit_pricing_currency_position',
			[
				'label' => esc_html__( 'Position', 'elementskit-lite' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'before',
				'options' => [
					'before' => [
						'title' => esc_html__( 'Before', 'elementskit-lite' ),
						'icon' => 'eicon-h-align-left',
					],
					'after' => [
						'title' => esc_html__( 'After', 'elementskit-lite' ),
						'icon' => 'eicon-h-align-right',
					],
				],
			]
		);

		$this->add_responsive_control(
			'ekit_pricing_currency_vertical_position',
			[
				'label' => esc_html__( 'Vertical Position', 'elementskit-lite' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'top',
				'selectors_dictionary' => [
					'top' => 'super',
					'middle' => 'baseline',
					'bottom' => 'sub',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-price sup.currency' => 'vertical-align: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'ekit_pricing_taghr1',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);
		$this->start_controls_tabs( 'ekit_pricing_tabs_price_style' );

		$this->start_controls_tab(
			'ekit_pricing_tab_tag_normal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_pricing_tag_text_color',
			[
				'label' =>esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-price' => 'color: {{VALUE}};',
				],
			]
		);
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_pricing_tag_bg_color',
				'default' => '',
				'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-tag',
            )
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_pricing_tag_tab_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_pricing_tag_hover_color',
			[
				'label' =>esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:hover .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-price' => 'color: {{VALUE}};',
				],
			]
		);

	    $this->add_group_control(
		    Group_Control_Background::get_type(),
		    array(
			    'name'     => 'ekit_pricing_tag_bg_hover_color_group',
			    'default' => '',
			    'selector' => '{{WRAPPER}}:hover .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-tag',
		    )
	    );

		$this->end_controls_tab();
        $this->end_controls_tabs();


        $this->add_control(
			'ekit_pricing_taghr2',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

        $this->add_responsive_control(
			'ekit_pricing_tag_border_style',
			[
				'label' => esc_html_x( 'Border Type', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'elementskit-lite' ),
					'solid' => esc_html_x( 'Solid', 'Border Control', 'elementskit-lite' ),
					'double' => esc_html_x( 'Double', 'Border Control', 'elementskit-lite' ),
					'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementskit-lite' ),
					'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementskit-lite' ),
					'groove' => esc_html_x( 'Groove', 'Border Control', 'elementskit-lite' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-tag' => 'border-style: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_pricing_tag_border_dimensions',
			[
				'label' => esc_html_x( 'Width', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-tag' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'ekit_pricing_tabs_tag_border_style' );
		$this->start_controls_tab(
			'ekit_pricing_tag_border_normal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_pricing_tag_border_color',
			[
				'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-tag' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_pricing_tag_tab_border_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
			]
		);
		$this->add_control(
			'ekit_pricing_tag_hover_border_color',
			[
				'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}:hover .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-tag' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
        $this->end_controls_tabs();



        $this->add_control(
			'ekit_pricing_taghr3',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);
        $this->add_responsive_control(
			'ekit_pricing_tag_border_radius',
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
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-tag' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_pricing_tag_box_shadow_group',
                'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-price-wraper.has-tag .elementskit-pricing-tag',
            ]
        );

		$this->end_controls_section();




        //Price Features style start
        $this->start_controls_section(
			'ekit_pricing_section_content_style',
			[
				'label' =>esc_html__( 'Features', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_pricing_btn_align',
			[
				'label' =>esc_html__( 'Content Alignment', 'elementskit-lite' ),
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
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-content' => 'text-align: {{VALUE}};',
				],
				'default' => '',

			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_pricing_content_typography_group',
				'label' =>esc_html__( 'List Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-content p,  {{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists > li',
			]
		);

        $this->add_control(
            'ekit_pricing_content_li_type',
            [
                'label'     => esc_html__( 'List Type', 'elementskit-lite' ),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'none',
                'options'   => [
                    'none'          => esc_html__( 'None', 'elementskit-lite' ),
                    'disc'          => esc_html__( 'Disc', 'elementskit-lite' ),
                    'decimal'       => esc_html__( 'Number', 'elementskit-lite' ),
                    'lower-alpha'   => esc_html__( 'Alphabet', 'elementskit-lite' ),
                    'lower-roman'   => esc_html__( 'Roman', 'elementskit-lite' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists > li' => 'list-style: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_pricing_content_style' => 'list',
                ],
            ]
        );

        $this->add_control(
			'ekit_pricing_fhr1',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);
		$this->start_controls_tabs( 'ekit_pricing_tabs_content_style' );

		$this->start_controls_tab(
			'ekit_pricing_content_tab',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
			]
		);
			$this->add_control(
				'ekit_pricing_content_text_color',
				[
					'label' =>esc_html__( 'Color', 'elementskit-lite' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-content p' => 'color: {{VALUE}};',
						'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists > li' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'ekit_pricing_features_n_bd',
					'selector'	=> '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-content',
				]
			);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'ekit_pricing_content_tab_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
			]
		);
        $this->add_control(
            'ekit_pricing_content_hover_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}:hover .elementskit-pricing-content p' => 'color: {{VALUE}};',
                    '{{WRAPPER}}:hover .elementskit-pricing-lists li' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' 		=> 'ekit_pricing_features_h_bd',
                'selector'	=> '{{WRAPPER}}:hover .elementskit-single-pricing .elementskit-pricing-content',
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->add_control(
			'ekit_pricing_list_divider',
			[
				'label' => esc_html__( 'Divider', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
                    'ekit_pricing_content_style' => 'list',
                ]
			]
		);

		$this->add_responsive_control(
			'ekit_pricing_divider_style',
			[
				'label' => esc_html__( 'Style', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid' => esc_html__( 'Solid', 'elementskit-lite' ),
					'double' => esc_html__( 'Double', 'elementskit-lite' ),
					'dotted' => esc_html__( 'Dotted', 'elementskit-lite' ),
					'dashed' => esc_html__( 'Dashed', 'elementskit-lite' ),
				],
				'default' => 'solid',
				'condition' => [
					'list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists li' => 'border-top-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_pricing_divider_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ddd',
				'condition' => [
					'ekit_pricing_list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists li' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_pricing_divider_weight',
			[
				'label' => esc_html__( 'Weight', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 2,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'condition' => [
					'ekit_pricing_list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists li' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_pricing_divider_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'ekit_pricing_list_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists li:before' => 'margin-left: calc((100% - {{SIZE}}%)/2); margin-right: calc((100% - {{SIZE}}%)/2)',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_pricing_divider_gap',
			[
				'label' => esc_html__( 'List Gap', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-lists li:before' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}}',
				],
                'condition' => [
                    'ekit_pricing_content_style' => 'list',
                ]
			]
		);

        $this->add_control(
			'ekit_pricing_fhr5',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);
		$this->add_responsive_control(
			'ekit_pricing_features_body_margin',
			[
				'label' =>esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => 0,
					'left' => 0,
					'right' => 0,
					'bottom' => 50,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_pricing_features_body_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'features_icon_heading',
			[
				'label'		=> esc_html__( 'Icon', 'elementskit-lite' ),
				'type'		=> Controls_Manager::HEADING,
				'separator'	=> 'before',
			]
		);

		$this->add_responsive_control(
			'features_icon_spacing',
			[
				'label'		=> esc_html__( 'Spacing', 'elementskit-lite' ),
				'type'		=> Controls_Manager::SLIDER,
				'selectors'	=> [
					'{{WRAPPER}} .elementskit-pricing-lists > li > i' => 'padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-pricing-lists > li > svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'features_icon_align',
			[
				'label'		=> esc_html__( 'Vertical Align', 'elementskit-lite' ),
				'type'		=> Controls_Manager::NUMBER,
				'selectors'	=> [
					'{{WRAPPER}} .elementskit-pricing-lists > li > i, {{WRAPPER}} .elementskit-pricing-lists > li > svg' => 'vertical-align: {{SIZE}}px;',
				]
			]
		);

		$this->end_controls_section();


        //Button style start
        $this->start_controls_section(
			'ekit_pricing_section_btn_style',
			[
				'label' =>esc_html__( 'Button', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_pricing_btn_typography_group',
                'label' =>esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-single-pricing a.elementskit-pricing-btn',
            ]
		);
		$this->add_responsive_control(
            'ekit_pricing_btn_icon_size',
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
                    '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-btn svg path' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
		);

		$this->add_responsive_control(
			'pricing_btn_width',
			[
				'label'		=> __( 'Width (%)', 'elementskit-lite' ),
				'type'		=> Controls_Manager::SLIDER,
				'selectors'	=> [
					'{{WRAPPER}} .elementskit-pricing-btn' => 'width: {{SIZE}}%;',
				],
			]
		);

		$this->add_responsive_control(
			'pricing_btn_align',
			[
				'label'		=> __( 'Alignment', 'elementskit-lite' ),
				'type'		=> Controls_Manager::CHOOSE,
				'options'	=> [
					'left' => [
						'title' => __( 'Left', 'elementskit-lite' ),
						'icon'	=> 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementskit-lite' ),
						'icon'	=> 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementskit-lite' ),
						'icon'	=> 'eicon-text-align-right',
					],
				],
				'selectors'	=> [
					'{{WRAPPER}} .elementskit-pricing-btn-wraper' => 'text-align: {{VALUE}};',
				],
			]
		);
		
        $this->add_control(
            'ekit_pricing_hr1',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );
        $this->start_controls_tabs( 'ekit_pricing_tabs_button_style' );

        $this->start_controls_tab(
            'ekit_pricing_tab_button_normal',
            [
                'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_pricing_btn_text_color',
            [
                'label' =>esc_html__( 'Text Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing a.elementskit-pricing-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-single-pricing a.elementskit-pricing-btn svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'ekit_pricing_btn_bg_color_group',
                'selector' => '{{WRAPPER}} .elementskit-single-pricing a.elementskit-pricing-btn',
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_pricing_btn_tab_button_hover',
            [
                'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_pricing_btn_hover_color',
            [
                'label' =>esc_html__( 'Text Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
					'{{WRAPPER}}:hover a.elementskit-pricing-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}}:hover a.elementskit-pricing-btn svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'ekit_pricing_btn_bg_hover_color_group',
                'selector' => '{{WRAPPER}}:hover a.elementskit-pricing-btn',
            )
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_pricing_text_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing a.elementskit-pricing-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'ekit_pricing_hr2',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

        $this->add_responsive_control(
			'ekit_pricing_btn_border_style',
			[
				'label' => esc_html_x( 'Border Type', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'elementskit-lite' ),
					'solid' => esc_html_x( 'Solid', 'Border Control', 'elementskit-lite' ),
					'double' => esc_html_x( 'Double', 'Border Control', 'elementskit-lite' ),
					'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementskit-lite' ),
					'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementskit-lite' ),
					'groove' => esc_html_x( 'Groove', 'Border Control', 'elementskit-lite' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing a.elementskit-pricing-btn' => 'border-style: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_pricing_btn_border_dimensions',
			[
				'label' => esc_html_x( 'Width', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing a.elementskit-pricing-btn' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => [
                    'ekit_pricing_btn_border_style!' => '',
                ]
			]
		);
		$this->start_controls_tabs( 'ekit_pricing_tabs_button_border_style' );
		$this->start_controls_tab(
			'ekit_pricing_tab_button_border_normal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
                'condition' => [
                    'ekit_pricing_btn_border_style!' => '',
                ]
			]
		);

		$this->add_control(
			'ekit_pricing_btn_border_color',
			[
				'label' => esc_html_x( 'Border Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-pricing a.elementskit-pricing-btn' => 'border-color: {{VALUE}};',
				],
                'condition' => [
                    'ekit_pricing_btn_border_style!' => '',
                ]
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_pricing_btn_tab_button_border_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
                'condition' => [
                    'ekit_pricing_btn_border_style!' => '',
                ]
			]
		);
		$this->add_control(
			'ekit_pricing_btn_hover_border_color',
			[
				'label' => esc_html_x( 'Border Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}:hover a.elementskit-pricing-btn' => 'border-color: {{VALUE}};',
				],
                'condition' => [
                    'ekit_pricing_btn_border_style!' => '',
                ]
			]
		);
		$this->end_controls_tab();
        $this->end_controls_tabs();



        $this->add_control(
			'ekit_pricing_hr3',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);
        $this->add_responsive_control(
			'ekit_pricing_btn_border_radius',
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
					'{{WRAPPER}} .elementskit-single-pricing a.elementskit-pricing-btn' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
        );

		$this->start_controls_tabs( 'ekit_pricing_tabs_button_box_shadow_style' );

		$this->start_controls_tab(
			'ekit_pricing_tab_button_box_shadow_normal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),

			]
		);
		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_pricing_button_box_shadow_group',
                'selector' => '{{WRAPPER}} .elementskit-single-pricing .elementskit-pricing-btn',

            ]
        );
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_pricing_tab_button_box_shadow_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),

			]
		);
		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_pricing_button_box_shadow_hover_group',
                'selector' => '{{WRAPPER}}:hover .elementskit-single-pricing .elementskit-pricing-btn',

            ]
        );
		$this->end_controls_tab();
        $this->end_controls_tabs();

		$this->end_controls_section();

		// Custom Order Style Start
		$this->start_controls_section(
			'ekit_pricing_order',
			[
				'label' =>esc_html__( 'Custom Ordering', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_control(
				'ekit_pricing_order_enable',
				[
					'label' 		=> esc_html__( 'Enable Ordering', 'elementskit-lite' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'label_block'	=> false,
					'return_value' 	=> 'yes',
					'default' 		=> 'no',
				]
			);

			$this->add_control(
				'ekit_pricing_order_header',
				[
					'label' 		=> esc_html__( 'Header', 'elementskit-lite' ),
					'type' 			=> Controls_Manager::SLIDER,
					'condition'		=> [
						'ekit_pricing_order_enable'	=> 'yes',
					]
				]
			);

			$this->add_control(
				'ekit_pricing_order_price',
				[
					'label' 		=> esc_html__( 'Price Tag', 'elementskit-lite' ),
					'type' 			=> Controls_Manager::SLIDER,
					'condition'		=> [
						'ekit_pricing_order_enable'	=> 'yes',
					]
				]
			);

			$this->add_control(
				'ekit_pricing_order_features',
				[
					'label' 		=> esc_html__( 'Features', 'elementskit-lite' ),
					'type' 			=> Controls_Manager::SLIDER,
					'condition'		=> [
						'ekit_pricing_order_enable'	=> 'yes',
					]
				]
			);

			$this->add_control(
				'ekit_pricing_order_button',
				[
					'label' 		=> esc_html__( 'Button', 'elementskit-lite' ),
					'type' 			=> Controls_Manager::SLIDER,
					'condition'		=> [
						'ekit_pricing_order_enable'	=> 'yes',
					]
				]
			);
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


		$options_ekit_pricing_title_size = array_keys([
			'h1' => 'H1',
			'h2' => 'H2',
			'h3' => 'H3',
			'h4' => 'H4',
			'h5' => 'H5',
			'h6' => 'H6',
			'div' => 'div',
			'span' => 'span',
			'p' => 'p',
		]);

		$ekit_pricing_title_size_validate = \ElementsKit_Lite\Utils::esc_options( $ekit_pricing_title_size, $options_ekit_pricing_title_size, 'h3');

        $table_title = $settings[ 'ekit_pricing_table_title' ];
        $table_subtitle = $settings[ 'ekit_pricing_table_subtitle' ];
		$table_content = $settings[ 'ekit_pricing_table_content' ];
		$currency_icon = $settings[ 'ekit_pricing_currency_icon' ];
		$table_price = $settings[ 'ekit_pricing_table_price' ];
		$table_duration = $settings[ 'ekit_pricing_table_duration' ];
		$table_content_repeater = $settings[ 'ekit_pricing_table_content_repeater' ];
        $content_style = $settings[ 'ekit_pricing_content_style' ];

        //For button
        $btn_text = $settings['ekit_pricing_btn_text'];
        $btn_class = ($settings['ekit_pricing_button_class'] != '') ? $settings['ekit_pricing_button_class'] : '';
        $btn_id = ($settings['ekit_pricing_button_id'] != '') ? $settings['ekit_pricing_button_id'] : '';
        $icon_align = $settings['ekit_pricing_icon_align'];

		if ( ! empty( $settings['ekit_pricing_btn_link']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['ekit_pricing_btn_link'] );
		}


		// $tag_align = $settings['ekit_pricing_tag_align'];
		$currency_position = $settings['ekit_pricing_currency_position'];
		$this->add_render_attribute( 'icon-align', 'class', 'xs-button-icon xs-align-icon-' . $settings['ekit_pricing_icon_align'] );

		$image = '';
        if ( ! empty( $settings['ekit_pricing_image']['url'] ) ) {
            $this->add_render_attribute( 'image', 'src', $settings['ekit_pricing_image']['url'] );
            $this->add_render_attribute( 'image', 'alt', Control_Media::get_image_alt( $settings['ekit_pricing_image'] ) );

            $image_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'ekit_pricing_image' );


            $image = '<figure class="elementor-pricing-img">' . $image_html . '</figure>';
        }


		// Custom Orders
		$header_order = !empty($ekit_pricing_order_header) ? $ekit_pricing_order_header['size'] : '';
		$price_order = !empty($ekit_pricing_order_price) ? $ekit_pricing_order_price['size']: '';
		$features_order = !empty($ekit_pricing_order_features) ? $ekit_pricing_order_features['size'] : '';
		$button_order = !empty($ekit_pricing_order_button) ? $ekit_pricing_order_button['size'] : '';
		?>


        <div class="elementskit-single-pricing <?php echo esc_attr($settings['ekit_pricing_order_enable'] == 'yes' ? 'd-flex flex-column' : ''); ?>" >
            <div class="elementskit-pricing-header <?php echo esc_attr($header_order ? 'order-'. $header_order : ''); ?>">
				<?php if($settings['ekit_pricing_icon_type'] == 'image') : ?>
                    <?php echo wp_kses($image, \ElementsKit_Lite\Utils::get_kses_array());?>
                <?php endif; ?>
				<?php if($settings['ekit_pricing_icon_type'] == 'icon') : ?>					
					<?php
						// new icon
						$migrated = isset( $settings['__fa4_migrated']['ekit_pricing_icons'] );
						// Check if its a new widget without previously selected icon using the old Icon control
						$is_new = empty( $settings['ekit_pricing_icon'] );
						if ( $is_new || $migrated ) {
							// new icon
							Icons_Manager::render_icon( $settings['ekit_pricing_icons'], [ 'aria-hidden' => 'true', 'class'    => [
								'elementkit-pricing-icon',
								'elementor-animation-'. esc_attr($settings['ekit_pricing_icons_hover_animation'])
							] ] );
						} else {
							?>
							<i class="<?php echo esc_attr($settings['ekit_pricing_icon']); ?> elementkit-pricing-icon <?php echo 'elementor-animation-'. esc_attr($settings['ekit_pricing_icons_hover_animation']); ?>" aria-hidden="true"></i>
							<?php
						}
					?>
									
				<?php endif; ?>

				<?php if($table_title != ''): ?>
                	<<?php echo wp_kses($ekit_pricing_title_size_validate, \ElementsKit_Lite\Utils::get_kses_array());?>
					class=" elementskit-pricing-title"><?php echo esc_html($table_title); ?>
					</<?php echo wp_kses($ekit_pricing_title_size_validate, \ElementsKit_Lite\Utils::get_kses_array()); ?>>
				<?php endif; ?>
				<?php if($table_subtitle != ''): ?>
                	<p class=" elementskit-pricing-subtitle"><?php echo esc_html($table_subtitle); ?></p>
				<?php endif; ?>
            </div>
			<?php if ($currency_icon != '' && $table_price !== '') { ?>
            <div class=" elementskit-pricing-price-wraper has-tag <?php echo esc_attr($price_order ? 'order-'. $price_order : ''); ?>">
                <div class="elementskit-pricing-tag"></div>
                <span class="elementskit-pricing-price">
					<?php if($currency_position == 'before'): ?>
						<sup class="currency"><?php echo esc_html($currency_icon); ?></sup>
					<?php endif; ?>
					<span><?php echo esc_html($table_price); ?></span>
					<?php if($currency_position == 'after'): ?>
						<sup class="currency"><?php echo esc_html($currency_icon); ?></sup>
					<?php endif; ?>

					<?php if ( $table_duration !== '' ): ?>
					<sub class="period"><?php echo esc_html($table_duration); ?></sub>
					<?php endif; ?>
				</span>
            </div>
			<?php } ?>
            <div class="elementskit-pricing-content <?php echo esc_attr($features_order ? 'order-'. $features_order : ''); ?>">

                <?php if($content_style == 'paragraph'){ ?>
                    <p> <?php echo wp_kses($table_content, \ElementsKit_Lite\Utils::get_kses_array()); ?></p>
                <?php } ?>
                <?php if($content_style == 'list'){ ?>
                    <ul class="elementskit-pricing-lists">
                        <?php foreach($table_content_repeater as $repeat){  ?>
							<li class="elementor-repeater-item-<?php echo esc_attr( $repeat[ '_id' ] ); ?>">
								<?php Icons_Manager::render_icon( $repeat['ekit_pricing_check_icons'], [ 'aria-hidden' => 'true' ] ); ?>
								<?php // echo esc_html($repeat['ekit_pricing_list']); ?>

								<?php echo esc_html($repeat['ekit_pricing_list']); ?>
								
								<?php if ( !empty( $repeat[ 'ekit_pricing_list_info' ] ) ): ?>
									<div class="ekit-pricing-list-info eicon-info-circle-o" data-info-tip="true">
										<span></span>
										<p class="ekit-pricing-list-info-content ekit-pricing-<?php echo esc_attr( $this->get_ID() ); ?> ekit-pricing-list-info-<?php echo esc_attr( $repeat[ '_id' ] ); ?>" data-info-tip-content="true"><?php echo esc_attr( $repeat[ 'ekit_pricing_list_info' ] ); ?></p>
									</div>
								<?php endif; ?>
							</li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
            <div class="elementskit-pricing-btn-wraper <?php echo esc_attr($button_order ? 'order-'. $button_order : ''); ?>">
				<a <?php echo $this->get_render_attribute_string( 'button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?> class="elementskit-pricing-btn <?php echo esc_attr( $btn_class ); ?> ekit-pricing-btn-icon-pos-<?php echo esc_attr($icon_align); ?>" <?php if($settings['ekit_pricing_button_id'] != '') { ?> id="<?php echo esc_attr( $btn_id ); ?>" <?php } ?>>
					<?php
					if ( $settings['ekit_pricing_btn_icons'] != '' && $icon_align == 'left' ):
						// new icon
						$migrated = isset( $settings['__fa4_migrated']['ekit_pricing_btn_icons'] );
						// Check if its a new widget without previously selected icon using the old Icon control
						$is_new = empty( $settings['ekit_pricing_btn_icon'] );
						if ( $is_new || $migrated ) {
							// new icon
							Icons_Manager::render_icon( $settings['ekit_pricing_btn_icons'], [ 'aria-hidden' => 'true' ] );
						} else {
							?>
							<i class="<?php echo esc_attr($settings['ekit_pricing_btn_icon']); ?>" aria-hidden="true"></i>
							<?php
						}
					endif;

					echo esc_html( $btn_text );
					
					if ( $settings['ekit_pricing_btn_icons'] != '' && $icon_align == 'right' ):
						// new icon
						$migrated = isset( $settings['__fa4_migrated']['ekit_pricing_btn_icons'] );
						// Check if its a new widget without previously selected icon using the old Icon control
						$is_new = empty( $settings['ekit_pricing_btn_icon'] );
						if ( $is_new || $migrated ) {
							// new icon
							Icons_Manager::render_icon( $settings['ekit_pricing_btn_icons'], [ 'aria-hidden' => 'true' ] );
						} else {
							?>
							<i class="<?php echo esc_attr($settings['ekit_pricing_btn_icon']); ?>" aria-hidden="true"></i>
							<?php
						}
					endif;
					?>
				</a>
            </div>
        </div>

    <?php
    }
}
