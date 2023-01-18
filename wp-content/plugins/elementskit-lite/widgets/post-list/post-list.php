<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Post_List_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Post_List extends Widget_Base {
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

	public function get_keywords() {
        return Handler::get_keywords();
    }

    public function get_categories() {
        return Handler::get_categories();
    }

    public function get_help_url() {
        return 'https://wpmet.com/doc/post-list/';
    }

	protected function register_controls() {

		$this->start_controls_section(
			'section_icon',
			[
				'label' => esc_html__( 'List', 'elementskit-lite' ),
			]
		);

		$this->add_control(
            'section_layout_options',
            [
                'label' => esc_html__( 'Show post by:', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'selected',
				'options' => [
					'recent'           => esc_html__( 'Recent Post', 'elementskit-lite' ),
					'popular'          => esc_html__( 'Popular Post', 'elementskit-lite' ),
					'selected'         => esc_html__( 'Selected Post', 'elementskit-lite' ),
				],

            ]
		);
		
		$this->add_control(
			'section_recent_post_limit',
			[
				'label'   => esc_html__( 'Product Limit', 'elementskit-lite' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5,
				'condition'	=> [
					'section_layout_options'	=> ['recent', 'popular']
				]
			]
		);


		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			[
				'label' => esc_html__( 'Text', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'placeholder' => esc_html__( 'List Title', 'elementskit-lite' ),
			]
		);

		$repeater->add_control(
			'link',
			[
                'label' =>esc_html__('Select Post', 'elementskit-lite'),
                'type'      => ElementsKit_Controls_Manager::AJAXSELECT2,
                'options'   =>'ajaxselect2/post_list',
                'label_block' => true,
                'multiple'  => false,
			]
		);

		$this->add_control(
			'icon_list',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{ text }}',
				'condition'	=> [
					'section_layout_options'	=> 'selected'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_post_list_settings_tab',
			[
				'label' => esc_html__( 'Settings', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'view',
			[
				'label' => esc_html__( 'Layout', 'elementskit-lite' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'traditional',
				'options' => [
					'traditional' => [
						'title' => esc_html__( 'Vertical', 'elementskit-lite' ),
						'icon' => 'eicon-editor-list-ul',
					],
					'inline' => [
						'title' => esc_html__( 'Horizontal', 'elementskit-lite' ),
						'icon' => 'eicon-ellipsis-h',
					],
				],
				'render_type' => 'template',
				'classes' => 'elementor-control-start-end',
				'label_block' => false,
				'style_transfer' => true,
			]
		);

		$this->add_responsive_control(
            'post_grid',
            [
                'label' => esc_html__( 'Columns Grid', 'elementskit-lite' ),
                'type' =>  Controls_Manager::SELECT,
                'options' => [
                    '12'  => esc_html__( '1 Columns', 'elementskit-lite' ),
                    '6'  => esc_html__( '2 Columns', 'elementskit-lite' ),
                    '4' => esc_html__( '3 Columns', 'elementskit-lite' ),
                    '3' => esc_html__( '4 Columns', 'elementskit-lite' ),
                    '2' => esc_html__( '6 Columns', 'elementskit-lite' ),
                ],
				'condition' => ['view' => 'inline'],
            ]
		);
		
		$this->add_responsive_control(
            'grid_gap',
            [
                'label' => esc_html__( 'Grid Gap', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon-list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => ['view' => 'inline', 'post_grid!' => ''],
            ]
        );

		$this->add_control(
			'show_feature_image',
			[
				'label' => esc_html__( 'Show Featured Image', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'show_bg_feature_image',
			[
				'label' => esc_html__( 'Background Featured Image', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
       

        /**
        * Control: Featured Image Size
        */
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'              => 'show_feature_img_size',
                'fields_options'    => [
                    'size'  => [
                        'label' => esc_html__( 'Featured Image Size', 'elementskit-lite' ),
                    ],
                ],
                'default'           => 'large',
                'conditions'        => [
					'relation'	=> 'or',
					'terms'		=> [
						[
							'name'		=> 'show_feature_image',
							'operator'	=> '==',
							'value'		=> 'yes',
						],
						[
							'name'		=> 'show_bg_feature_image',
							'operator'	=> '==',
							'value'		=> 'yes',
						],
					],
                ],
            ]
        );


        /**
        * Control: Divider After Featured Image Size
        */
		$this->add_control(
			'ekit_post_list_divider_after_featured',
			[
				'type'	=> Controls_Manager::DIVIDER,
			]
		);


		$this->add_control(
			'show_post_icon',
			[
				'label' => esc_html__( 'Show Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition'	=> [
					'show_feature_image!'	=> 'yes'
				]
			]
		);

		$this->add_control(
			'icons',
			[
				'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => true,
				'fa4compatibility' => 'icon',
				'default'	=> [
					'value'	=> 'fas fa-circle',
					'library'	=> 'regular'
				],
				'condition'	=> [
					'show_post_icon'		=> 'yes',
					'show_feature_image!'	=> 'yes'
				]
			]
		);

		$this->add_control(
			'show_post_meta',
			[
				'label' => esc_html__( 'Show Meta', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'show_date_meta',
			[
				'label' => esc_html__( 'Show Date Meta', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'show_post_meta' => 'yes',
				]
			]
		);

		$this->add_control(
			'date_meta__icons',
			[
				'label' => __( 'Date Meta Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'date_meta__icon',
                'default' => [
                    'value' => 'icon icon-calendar-page-empty',
                    'library' => 'ekiticons',
                ],
				'condition' => [
					'show_post_meta' => 'yes',
					'show_date_meta' => 'yes',
				]
			]
		);
		
		$this->add_control(
			'show_category_meta',
			[
				'label' => esc_html__( 'Show Category Meta', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'show_post_meta' => 'yes',
				]
			]
		);

		$this->add_control(
			'category_meta__icons',
			[
				'label' => __( 'Category Meta Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'category_meta__icon',
                'default' => [
                    'value' => 'icon icon-folder',
                    'library' => 'ekiticons',
                ],
				'condition' => [
					'show_post_meta' => 'yes',
					'show_category_meta' => 'yes',
				]
			]
		);

		$this->add_control(
			'post_meta_position',
			[
				'label' => esc_html__( 'Meta Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'top_position',
				'options' => [
					'top_position'  => esc_html__( 'Top', 'elementskit-lite' ),
					'bottom_position' => esc_html__( 'Bottom', 'elementskit-lite' ),
				],
				'condition' => [
					'show_post_meta' => 'yes',
				]
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_list',
			[
				'label' => esc_html__( 'List', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'space_between',
			[
				'label' => esc_html__( 'Space Between', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:first-child)' => 'margin-top: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item' => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-icon-list-items.elementor-inline-items' => 'margin-right: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/2)',
					'body.rtl {{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item:after' => 'left: calc(-{{SIZE}}{{UNIT}}/2)',
					'body:not(.rtl) {{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item:after' => 'right: calc(-{{SIZE}}{{UNIT}}/2)',
				],
				'condition'	=> [
					'post_grid'	=> ''
				]
			]
		);

		$this->add_responsive_control(
            'list_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon-list-item a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
		);
		
		$this->start_controls_tabs( 'tabs_list_style' );

        $this->start_controls_tab(
            'list_tab_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'list_bg',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementor-icon-list-item a',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'list_border',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementor-icon-list-item a',
            ]
        );
        $this->add_responsive_control(
            'list_border_radius',
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
                    '{{WRAPPER}} .elementor-icon-list-item a' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'list_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-icon-list-item a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'list_tab_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'list_bg_hover',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementor-icon-list-item a:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'list_border_hover',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementor-icon-list-item a:hover',
            ]
        );
        $this->add_responsive_control(
            'list_border_radius_hover',
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
                    '{{WRAPPER}} .elementor-icon-list-item a:hover' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'list_box_shadow_hover',
                'selector' => '{{WRAPPER}} .elementor-icon-list-item a:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_align',
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
				'prefix_class' => 'elementor%s-align-',
			]
		);
		
		$this->add_control(
            'overlay_heading',
            [
                'label'         => __( 'Image Overlay:', 'elementskit-lite' ),
                'type'          => \Elementor\Controls_Manager::HEADING,
                'separator'     => 'before',
                'condition'	=> [
					'show_bg_feature_image'	=> 'yes'
				]
            ]
        );
		$this->start_controls_tabs( 'tabs_overlay' );

        $this->start_controls_tab(
            'overlay_tab_normal',
            [
				'label' => esc_html__( 'Normal', 'elementskit-lite' ),
				'condition'	=> [
					'show_bg_feature_image'	=> 'yes'
				]
            ]
		);
		$this->add_control(
            'list_overlay_color',
            [
                'label' => esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-enabled-bg-img .elementor-icon-list-item a:after' => 'background-color: {{VALUE}};',
				],
				'condition'	=> [
					'show_bg_feature_image'	=> 'yes'
				]
            ]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
            'overlay_tab_hover',
            [
				'label' => esc_html__( 'Hover', 'elementskit-lite' ),
				'condition'	=> [
					'show_bg_feature_image'	=> 'yes'
				]
            ]
		);
		$this->add_control(
            'list_overlay_hover_color',
            [
                'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-enabled-bg-img .elementor-icon-list-item a:hover:after' => 'background-color: {{VALUE}};',
				],
				'condition'	=> [
					'show_bg_feature_image'	=> 'yes'
				]
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		

		$this->add_control(
			'divider',
			[
				'label' => esc_html__( 'Divider', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'elementskit-lite' ),
				'label_on' => esc_html__( 'On', 'elementskit-lite' ),
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'content: "";',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label' => esc_html__( 'Style', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid' => esc_html__( 'Solid', 'elementskit-lite' ),
					'dotted' => esc_html__( 'Dotted', 'elementskit-lite' ),
					'dashed' => esc_html__( 'Dashed', 'elementskit-lite' ),
				],
				'default' => 'solid',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:last-child):after' => 'border-top-style: {{VALUE}}',
					'{{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item:not(:last-child):after' => 'border-left-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label' => esc_html__( 'Weight', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:last-child):after' => 'border-top-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-inline-items .elementor-icon-list-item:not(:last-child):after' => 'border-left-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'condition' => [
					'divider' => 'yes',
					'view!' => 'inline',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_height',
			[
				'label' => esc_html__( 'Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'divider' => 'yes',
					'view' => 'inline',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ddd',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'show_feature_image!'	=> 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'icon_vertical_alignment',
			[
				'label' =>esc_html__( 'Vertical Alignment', 'elementskit-lite' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start'    => [
						'title' =>esc_html__( 'Top', 'elementskit-lite' ),
						'icon' => 'fa fa-caret-up',
					],
					'center' => [
						'title' =>esc_html__( 'Center', 'elementskit-lite' ),
						'icon' => 'fa fa-align-center',
					],
					'flex-end' => [
						'title' =>esc_html__( 'Bottom', 'elementskit-lite' ),
						'icon' => 'fa fa-caret-down',
					],
				],
				'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .elementor-icon-list-icon' => 'align-self: {{VALUE}};'
                ]
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'icon_bg',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .ekit-wid-con .elementor-icon-list-icon',
            ]
		);
		
		$this->add_responsive_control(
            'icon_height',
            [
                'label' => esc_html__( 'Height', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .elementor-icon-list-icon' => 'height: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

        $this->add_responsive_control(
            'icon_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .elementor-icon-list-icon' => 'width: {{SIZE}}{{UNIT}};',
                ],


            ]
        );

        $this->add_responsive_control(
            'icon_line_height',
            [
                'label' => esc_html__( 'Line Height', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .elementor-icon-list-icon' => 'line-height: {{SIZE}}{{UNIT}};',
                ],

            ]
		);
		
		$this->add_responsive_control(
            'icon_border_radius',
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
                    '{{WRAPPER}} .ekit-wid-con .elementor-icon-list-icon' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon-list-icon svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:hover .elementor-icon-list-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon-list-item:hover .elementor-icon-list-icon svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Size', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'min' => 6,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-icon' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-icon-list-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-icon-list-icon svg'	=> 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			[
				'label' => esc_html__( 'Text', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:hover .elementor-icon-list-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_indent',
			[
				'label' => esc_html__( 'Padding Left', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'default'	=> [
					'size'	=> 10
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-text' => is_rtl() ? 'padding-right: {{SIZE}}{{UNIT}};' : 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'icon_typography',
				'selector' => '{{WRAPPER}} .elementor-icon-list-item',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'feature_image_style',
			[
				'label' => esc_html__( 'Feature Image', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_feature_image' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
            'feature_image_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon-list-item a > img' => 'width: {{SIZE}}{{UNIT}};',
                ],


            ]
		);

		$this->add_responsive_control(
            'feature_image_border_radius',
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
                    '{{WRAPPER}} .elementor-icon-list-item a > img' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		
		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_post_list_meta_style_tab',
			[
				'label' => esc_html__( 'Meta', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_post_meta' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_post_list_meta_content_typography',
				'label' => esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementor-icon-list-item .meta-lists > span',
			]
		);

		$this->add_responsive_control(
            'ekit_post_list_meta_content_icon_size',
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
                    '{{WRAPPER}} .elementor-icon-list-item .meta-lists > span i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-icon-list-item .meta-lists > span svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
		);
		
		$this->add_responsive_control(
			'meta_icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
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
                    'size' => 5,
                ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .meta-lists > span i, {{WRAPPER}} .elementor-icon-list-item .meta-lists > span svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_post_list_meta_content_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .meta-lists > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_post_list_meta_content_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
                    'top' => '',
                    'right' => '',
                    'bottom' => '',
                    'left' => '10',
                    'unit' => 'px',
                ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .meta-lists > span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->start_controls_tabs( 'ekit_post_list_normal_and_hover_tabs' );

		$this->start_controls_tab(
			'ekit_post_list_normal_tab',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
			]
		);

		$this->add_responsive_control(
			'ekit_post_list_meta_content_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#7f8595',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .meta-lists > span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon-list-item .meta-lists svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'ekit_post_list_meta_content_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .meta-lists > span' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_post_list_meta_content_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .meta-lists > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_post_list_hover_tab',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
			]
		);

		$this->add_responsive_control(
			'ekit_post_list_meta_content_color_hover',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .meta-lists > span:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-icon-list-item .meta-lists > span:hover svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};'
				],
			]
		);

		$this->add_responsive_control(
			'ekit_post_list_meta_content_bg_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .meta-lists > span:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_post_list_meta_content_border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .meta-lists > span:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
	
	private function post_list($post, $item = null) {
		$settings = $this->get_settings_for_display();
		$categories = get_the_category($post->ID);
		$text = empty($item['text']) ? $post->post_title : $item['text'];

		$grid_d = empty($settings['post_grid']) ? '' : 'col-lg-'.$settings['post_grid'];
		$grid_t = empty($settings['post_grid_tablet']) ? '' : 'col-md-'.$settings['post_grid_tablet'];
		$grid_m = empty($settings['post_grid_mobile']) ? '' : 'col-xs-'.$settings['post_grid_mobile'];
		

		ob_start();
		$feature_bg_url = get_the_post_thumbnail_url($post, $settings['show_feature_img_size_size']);
		?>
			<li class="elementor-icon-list-item <?php echo esc_attr($grid_d); ?> <?php echo esc_attr($grid_t); ?> <?php echo esc_attr($grid_m); ?>">
				<a href="<?php echo esc_url(get_the_permalink($post->ID)); ?>" <?php if(isset($settings['show_bg_feature_image']) && $settings['show_bg_feature_image'] == 'yes' && !empty($feature_bg_url)) : ?>style="background-image: url('<?php echo esc_attr( $feature_bg_url ); ?>')" <?php endif; ?>>
					<?php 
						if ($settings['show_feature_image'] == 'yes') {
							echo get_the_post_thumbnail($post->ID, $settings['show_feature_img_size_size']);
						} else {
							if ( $settings['show_post_icon'] === 'yes' ) { ?>
								<span class="elementor-icon-list-icon">
									<?php
										// new icon
										$migrated = isset( $settings['__fa4_migrated']['icons'] );
										// Check if its a new widget without previously selected icon using the old Icon control
										$is_new = empty( $settings['icon'] );
										if ( $is_new || $migrated ) {
											// new icon
											Icons_Manager::render_icon( $settings['icons'], [ 'aria-hidden' => 'true' ] );
										} else {
											?>
											<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
											<?php
										}
									?>
								</span>
							<?php }
						}
					?>
					<div class="ekit_post_list_content_wraper">
						<?php if ($settings['show_post_meta'] == 'yes') { 
							if ($settings['post_meta_position'] == 'top_position') {
						?>
						<?php if ($settings['show_date_meta'] == 'yes' || $settings['show_category_meta'] == 'yes') { ?>
						<div class="meta-lists">
							<?php if ($settings['show_date_meta'] == 'yes') { ?>
							<span class="meta-date">

								<?php
									// new icon
									$migrated = isset( $settings['__fa4_migrated']['date_meta__icons'] );
									// Check if its a new widget without previously selected icon using the old Icon control
									$is_new = empty( $settings['date_meta__icon'] );
									if ( $is_new || $migrated ) {
										// new icon
										Icons_Manager::render_icon( $settings['date_meta__icons'], [ 'aria-hidden' => 'true' ] );
									} else {
										?>
										<i class="<?php echo esc_attr($settings['date_meta__icon']); ?>" aria-hidden="true"></i>
										<?php
									}
								?>

								<?php echo get_the_date("d M Y", $post->ID); ?>
							</span>
							<?php }; ?>

							<?php 
								if ($settings['show_category_meta'] == 'yes') {
									$counter = 0;
									?>
									<span class="meta-category">
									<?php if (!empty($settings['category_meta__icons'])) { ?>

										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['category_meta__icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['category_meta__icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['category_meta__icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['category_meta__icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>

									<?php }
										echo (isset($categories[0])) ? esc_html( $categories[0]->name ) : 0;
									?>
									</span>
									<?php
								}
							?>
						</div>
						<?php 
						};
							}; 
						};
						?>

						<span class="elementor-icon-list-text"><?php echo esc_html($text); ?></span>

						<?php if ($settings['show_post_meta'] == 'yes') { 
							if ($settings['post_meta_position'] == 'bottom_position') {
						?>
						<?php if ($settings['show_date_meta'] == 'yes' || $settings['show_category_meta'] == 'yes') { ?>
						<div class="meta-lists">
							<?php if ($settings['show_date_meta'] == 'yes') { ?>
							<span class="meta-date">
								<?php
									// new icon
									$migrated = isset( $settings['__fa4_migrated']['date_meta__icons'] );
									// Check if its a new widget without previously selected icon using the old Icon control
									$is_new = empty( $settings['date_meta__icon'] );
									if ( $is_new || $migrated ) {
										// new icon
										Icons_Manager::render_icon( $settings['date_meta__icons'], [ 'aria-hidden' => 'true' ] );
									} else {
										?>
										<i class="<?php echo esc_attr($settings['date_meta__icon']); ?>" aria-hidden="true"></i>
										<?php
									}
								?>	

								<?php echo get_the_date("d M Y", $post->ID); ?>
							</span>
							<?php }; ?>

							<?php 
								if ($settings['show_category_meta'] == 'yes') {
									$counter = 0;
									?>
									<span class="meta-category">
									<?php if (!empty($settings['category_meta__icons'])) { ?>
										<?php
											// new icon
											$migrated = isset( $settings['__fa4_migrated']['category_meta__icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['category_meta__icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['category_meta__icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['category_meta__icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>
									<?php }
									echo esc_html( $categories[0]->name ); ?>
									</span>
									<?php
								}
							?>
						</div>
						<?php 
						};
							}; 
						};
						?>
					</div>
				</a>
			</li>
		<?php
		return ob_get_clean();
	}

    protected function render_raw( ) {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'icon_list', 'class', 'elementor-icon-list-items ekit-post-list-wrapper' );
		$this->add_render_attribute( 'icon_list', 'class', (isset($settings['show_bg_feature_image']) && $settings['show_bg_feature_image'] == 'yes' ? 'ekit-enabled-bg-img': '') );

		$this->add_render_attribute( 'list_item', 'class', 'elementor-icon-list-item' );

		if ( 'inline' === $settings['view'] ) {
			$this->add_render_attribute( 'icon_list', 'class', 'elementor-inline-items' );
			$this->add_render_attribute( 'list_item', 'class', 'elementor-inline-item' );
		}

		
		?>
		<ul <?php echo $this->get_render_attribute_string( 'icon_list' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
			<?php
			$post_args = array(
				'post_type'			=> 'post',
				'posts_per_page'	=> esc_html($settings['section_recent_post_limit'])
			);
			if($settings['section_layout_options'] === 'popular'){
				$post_args['meta_key']	= 'ekit_post_views_count';
				$post_args['orderby'] 	= 'meta_value_num';
				$post_args['order']		= 'DESC';
			}
			$posts = get_posts($post_args);
			
			if($settings['section_layout_options'] === 'recent' || $settings['section_layout_options'] === 'popular'){
				if( is_countable($posts) && count($posts) > 0){
					foreach($posts as $post){
						echo $this->post_list($post); // phpcs:ignore WordPress.Security.EscapeOutput -- Buffering output line number 1383 
					}
				} else {
					esc_html_e('Opps, No posts were found.', 'elementskit-lite');
				}
			} else {
				foreach ( $settings['icon_list'] as $index => $item ) {
					$post = !empty( $item['link'] ) ? get_post($item['link']) : 0;
					if($post != null){ echo $this->post_list($post, $item);  }; // phpcs:ignore WordPress.Security.EscapeOutput -- Buffering output line number 1383
				};
			}
			
			?>
		</ul>
		<?php
	}
}
