<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Heading_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

defined('ABSPATH') || exit;


class ElementsKit_Widget_Heading extends Widget_Base {
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
        return 'https://wpmet.com/doc/widget-documentation/';
    }

    protected function register_controls() {


		$this->start_controls_section(
			'ekit_heading_section_title',
			array(
				'label' => esc_html__( 'Title', 'elementskit-lite' ),
			)
		);

		$this->add_control(
			'ekit_heading_title', [
				'label'			 => esc_html__( 'Heading Title', 'elementskit-lite' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
				'description'	 => esc_html__( '"Focused Title" Settings will be worked, If you use this {{something}} format', 'elementskit-lite' ),
				'label_block'	 => true,
				'placeholder'	 => esc_html__( 'Grow your {{report}}', 'elementskit-lite' ),
				'default'		 => esc_html__( 'Grow your {{report}}', 'elementskit-lite' ),

			]
		);

		$this->add_control( 'ekit_heading_link', [
			'label'			 => esc_html__( 'Link', 'elementskit-lite' ),
			'type'			 => Controls_Manager::URL,
			'dynamic'		 => [
				'active' => true,
			],
			'label_block' => true,
			'placeholder' => esc_html__( 'Paste URL or type', 'elementskit-lite' ),
			'autocomplete' => false,
			'options' => [ 'is_external', 'nofollow', 'custom_attributes' ],
        ]);

		$this->add_control(
			'ekit_heading_title_tag',
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
				'default' => 'h2',
			]
		);

		
		$this->add_control( 'show_title_border', [
			'label' => esc_html__( 'Show Border', 'elementskit-lite' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'no',
        ]);
        
        $this->add_control(
			'title_border_position',
			[
				'label' => esc_html__( 'Border Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'start',
				'options' => [
					'start' => esc_html__( 'Start', 'elementskit-lite' ),
					'end' => esc_html__( 'End', 'elementskit-lite' ),
				],
				'condition' => [
					'show_title_border' => 'yes'
				]
			]
		);

		$this->add_responsive_control( 'title_float_left', [
			'label' => esc_html__( 'Float Left', 'elementskit-lite' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'no',
		]);

		$this->add_responsive_control( 'title_float_left_width', [
			'label' => __( 'Title Width', 'elementskit-lite' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ '%' ],
			'default' => [ 'unit' => '%', 'size' => '40' ],
			'range' => [
				'%' => [
					'min' => 0,
					'max' => 200,
					'step' => 1,
				]
			],
			'selectors' => [
				'{{WRAPPER}} .ekit-heading__title-wrapper' => 
					'width: {{SIZE}}{{UNIT}};'
			],
			'condition' => [
				'title_float_left' => 'yes'	
			]
		]);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_heading_section_subtitle',
			array(
				'label' => esc_html__( 'Subtitle', 'elementskit-lite' ),
			)
		);

		$this->add_control(
			'ekit_heading_sub_title_show',
			[
				'label' => esc_html__( 'Show Sub Title', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);
		$this->add_control(
			'ekit_heading_sub_title_border',
			[
				'label' => esc_html__( 'Border Sub Title', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
                    'ekit_heading_sub_title_show' => 'yes',
                    //'ekit_heading_sub_title_outline' => '!yes'
				]
			]
		);

		$this->add_control(
			'ekit_heading_sub_title_outline',
			[
				'label' => esc_html__( 'Show Outline', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'ekit_heading_sub_title_show' => 'yes',
					'ekit_heading_sub_title_border!' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_heading_sub_title', [
				'label'			 =>esc_html__( 'Heading Sub Title', 'elementskit-lite' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
				'label_block'	 => true,
				'placeholder'	 =>esc_html__( 'Time has changed', 'elementskit-lite' ),
				'default'		 =>esc_html__( 'Time has changed', 'elementskit-lite' ),
				'condition' => [
					'ekit_heading_sub_title_show' => 'yes'
				],

			]
		);
		$this->add_control(
			'ekit_heading_sub_title_position',
			[
				'label' => esc_html__( 'Sub Title Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after_title',
				'options' => [
					'before_title' => esc_html__( 'Before Title', 'elementskit-lite' ),
					'after_title' => esc_html__( 'After Title', 'elementskit-lite' ),
				],
				'condition' => [
					'ekit_heading_sub_title_show' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_heading_sub_title_tag',
			[
				'label' => esc_html__( 'Sub Title HTML Tag', 'elementskit-lite' ),
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
				'condition' => [
					'ekit_heading_sub_title_show' => 'yes'
				]
			]
		);
		$this->end_controls_section();

		//Title Description
		$this->start_controls_section(
			'ekit_heading_section_extra_title',
			array(
				'label' => esc_html__( 'Title Description', 'elementskit-lite' ),
			)
		);

		$this->add_control(
			'ekit_heading_section_extra_title_show',
			[
				'label' => esc_html__( 'Show Description', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'ekit_heading_extra_title',
			[
				'label' => esc_html__( 'Heading Description', 'elementskit-lite' ),
				'type' => Controls_Manager::WYSIWYG,
				'dynamic' => [
					'active' => true,
				],
				'rows' => 10,
				'label_block'	 => true,
				'default'	 =>esc_html__( 'A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradise ', 'elementskit-lite' ),
				'placeholder'	 =>esc_html__( 'Title Description', 'elementskit-lite' ),
				'condition' => [
					'ekit_heading_section_extra_title_show' => 'yes'
				],

			]
		);

		$this->add_responsive_control( 'desciption_width', [
			'label' => __( 'Maximum Width', 'elementskit-lite' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors' => [
				'{{WRAPPER}} .ekit-heading__description' => 'max-width: {{SIZE}}{{UNIT}};'
			],
			'condition' => [
				'ekit_heading_section_extra_title_show' => 'yes'
			]
		]);

		$this->end_controls_section();

		/** Start Heading shadow text setion */
		$this->start_controls_section( 'shadow_text_section', [
			'label' => esc_html__( 'Shadow Text', 'elementskit-lite' )
		]);

		$this->add_control( 'show_shadow_text', [
			'label' => esc_html__( 'Show Shadow Text', 'elementskit-lite' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'no',
		]);

		$this->add_control( 'shadow_text_content', [
			'label'			 => esc_html__( 'Content', 'elementskit-lite' ),
			'label_block'	 => true,
			'type'			 => Controls_Manager::TEXT,
			'dynamic'		 => [
				'active' => true,
			],
			'default'		 => esc_html__( 'bussiness', 'elementskit-lite' ),
			'condition' => [
				'show_shadow_text' => 'yes'
			],

		]);

		$this->end_controls_section();
		/** End Heading shadow text setion */

		$this->start_controls_section(
			'ekit_heading_section_seperator',
			array(
				'label' => esc_html__( 'Separator', 'elementskit-lite' ),
			)
		);


		$this->add_control(
			'ekit_heading_show_seperator', [
				'label'			 =>esc_html__( 'Show Separator', 'elementskit-lite' ),
				'type'			 => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
			]

		);
		$this->add_control(
			'ekit_heading_seperator_style',
			[
				'label' => esc_html__( 'Separator Style', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'elementskit-border-divider ekit-dotted' => esc_html__( 'Dotted', 'elementskit-lite' ),
					'elementskit-border-divider elementskit-style-long' => esc_html__( 'Solid', 'elementskit-lite' ),
					'elementskit-border-star' => esc_html__( 'Solid with star', 'elementskit-lite' ),
					'elementskit-border-star elementskit-bullet' => esc_html__( 'Solid with bullet', 'elementskit-lite' ),
					'ekit_border_custom' => esc_html__( 'Custom', 'elementskit-lite' ),
				],
				'default' => 'elementskit-border-divider',
				'condition' => [
					'ekit_heading_show_seperator' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekit_heading_seperator_position',
			[
				'label' => esc_html__( 'Separator Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'top' => esc_html__( 'Top', 'elementskit-lite' ),
					'before' => esc_html__( 'Before Title', 'elementskit-lite' ),
					'after' => esc_html__( 'After Title', 'elementskit-lite' ),
					'bottom' => esc_html__( 'Bottom', 'elementskit-lite' ),
				],
				'default' => 'after',
				'condition' => [
					'ekit_heading_show_seperator' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekit_heading_seperator_image',
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
					'ekit_heading_show_seperator' => 'yes',
					'ekit_heading_seperator_style' => 'ekit_border_custom',
				],

			]
		);

		$this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'ekit_heading_seperator_image_size',
				'default' => 'large',
				'condition' => [
					'ekit_heading_show_seperator' => 'yes',
					'ekit_heading_seperator_style' => 'ekit_border_custom',
				],
            ]
        );

		$this->end_controls_section();
		
		$this->start_controls_section(
			'ekit_heading_section_general',
			array(
				'label' => esc_html__( 'General', 'elementskit-lite' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'ekit_heading_title_align', [
				'label'			 =>esc_html__( 'Alignment', 'elementskit-lite' ),
				'type'			 => Controls_Manager::CHOOSE,
				'options'		 => [
					'text_left'		 => [
						'title'	 =>esc_html__( 'Left', 'elementskit-lite' ),
						'icon'	 => 'eicon-text-align-left',
					],
					'text_center'	 => [
						'title'	 =>esc_html__( 'Center', 'elementskit-lite' ),
						'icon'	 => 'eicon-text-align-center',
					],
					'text_right'		 => [
						'title'	 =>esc_html__( 'Right', 'elementskit-lite' ),
						'icon'	 => 'eicon-text-align-right',
					],
				],
				'default'		 => 'text_left',
			]
		);

		$this->end_controls_section();


		//Title Style Section
		$this->start_controls_section(
			'ekit_heading_section_title_style', [
				'label'	 => esc_html__( 'Title', 'elementskit-lite' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_heading_title_color', [
				'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_heading_title_color_hover', [
				'label'		 =>esc_html__( 'Hover Color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title:hover' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_heading_title_shadow',
                'selector' => '{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title',

            ]
        );
		$this->add_responsive_control(
			'ekit_heading_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
			'name'		 => 'ekit_heading_title_typography',
			'selector'	 => '{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title',
			]
		);

		$this->add_control( 'title_left_border_heading', [
			'label' => esc_html__( 'Border', 'elementskit-lite' ),
			'type' => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
				'show_title_border' => 'yes'	
			]
		]);

		$this->add_control( 'title_left_border_width', [
			'label' => __( 'Border Width', 'elementskit-lite' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 32,
					'step' => 1,
				]
			],
			'default' => [ 'unit' => 'px', 'size' => 5 ],
			'selectors' => [
                '{{WRAPPER}} .ekit-heading__title-has-border::before' => 
                    'width: {{SIZE}}{{UNIT}};'
			],
			'condition' => [
				'show_title_border' => 'yes'	
			]
		]);

		$this->add_control( 'title_left_border_height', [
			'label' => __( 'Border Height', 'elementskit-lite' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ '%', 'px', 'em' ],
			'default' => [ 'unit' => '%', 'size' => 100 ],
			'selectors' => [
				'{{WRAPPER}} .ekit-heading__title-has-border::before' => 
					'height: {{SIZE}}{{UNIT}};'
			],
			'condition' => [
				'show_title_border' => 'yes'	
			]
        ]);

		$this->add_control( 'title_border_vertical_position', [
			'label' => __( 'Vertical Position', 'elementskit-lite' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ '%', 'px', 'em' ],
			'default' => [ 'unit' => 'px', 'size' => 0 ],
			'selectors' => [
				'{{WRAPPER}} .ekit-heading__title-has-border::before' => 
					'top: {{SIZE}}{{UNIT}};'
			],
			'condition' => [
				'show_title_border' => 'yes'	
			]
		]);

		$this->add_control( 'title_left_border_gap', [
			'label' => __( 'Right Gap', 'elementskit-lite' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 128,
					'step' => 1,
				]
			],
			'default' => [ 'unit' => 'px', 'size' => 30 ],
			'selectors' => [
				'{{WRAPPER}} .ekit-heading__title-has-border' => 
					'padding-left: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .ekit-heading__title-has-border ~ *' => 
                    'padding-left: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .ekit-heading__subtitle-has-border' => 
                    'margin-left: {{SIZE}}{{UNIT}};'
			],
			'condition' => [
                'show_title_border' => 'yes',
				'title_border_position' => 'start',
                'ekit_heading_title_align!' => 'text_center'
			]
        ]);

		$this->add_control( 'title_left_border_gap2', [
			'label' => __( 'Left Gap', 'elementskit-lite' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 128,
					'step' => 1,
				]
			],
			'default' => [ 'unit' => 'px', 'size' => 30 ],
			'selectors' => [
				'{{WRAPPER}} .ekit-heading__title-has-border' => 
					'padding-right: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .ekit-heading__title-has-border ~ *' => 
					'padding-right: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .ekit-heading__subtitle-has-border' => 
					'margin-right: {{SIZE}}{{UNIT}};'
			],
			'condition' => [
				'show_title_border' => 'yes',
                'title_border_position' => 'end',
                'ekit_heading_title_align!' => 'text_center'
			]
		]);

		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name' => 'title_left_border_color',
				'label' => __( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .ekit-heading__title-has-border::before',
				'types' => ['gradient'],
				'condition' => [
					'show_title_border' => 'yes'	
				]
			]
		);

		$this->end_controls_section();

		//Focused Title Style Section
		$this->start_controls_section(
			'ekit_heading_section_focused_title_style', [
				'label'	 => esc_html__( 'Focused Title', 'elementskit-lite' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_heading_focused_title_color', [
				'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors'	 => [
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title > span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_heading_focused_title_color_hover', [
				'label'		 =>esc_html__( 'Hover Color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors'	 => [
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title:hover > span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
			'name'		 => 'ekit_heading_focused_title_typography',
			'selector'	 => '{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title > span',
			]
		);

		$this->add_responsive_control(
			'ekit_heading_title_text_decoration_color', [
				'label'		 =>esc_html__( 'Text decoration color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title > span' => 'text-decoration-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_heading_focus_title_shadow',
                'selector' => '{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title > span',

            ]
        );

		$this->add_responsive_control(
			'ekit_heading_focused_title_secondary_spacing',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_heading_use_focused_title_bg', [
				'label'			 =>esc_html__( 'Use background color on text', 'elementskit-lite' ),
				'type'			 => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
				'condition' => [
					'ekit_heading_use_title_text_fill!' => 'yes'
				],
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_heading_focused_title_secondary_bg',
				'label'		 => esc_html__( 'Focused Title Secondary BG', 'elementskit-lite' ),
                'default' => '',
				'selector' => '{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title > span',
				'condition' => [
					'ekit_heading_use_focused_title_bg' => 'yes',
					'ekit_heading_use_title_text_fill!' => 'yes'
				],
            )
		);

		$this->add_control(
			'ekit_heading_focused_title_secondary_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title > span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_heading_use_focused_title_bg' => 'yes',
					'ekit_heading_use_title_text_fill!' => 'yes'
				],
			]
		);

		$this->add_control(
			'ekit_heading_use_title_text_fill', [
				'label'			 =>esc_html__( 'Use text fill', 'elementskit-lite' ),
				'type'			 => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
				'separator' => 'before',
				'condition' => [
					'ekit_heading_use_focused_title_bg!' => 'yes'
				]
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_heading_title_secondary_bg',
				'label'		 => esc_html__( 'Focused Title Secondary BG', 'elementskit-lite' ),
                'default' => '',
				'selector' => '{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-title.text_fill > span',
				'condition' => [
					'ekit_heading_use_title_text_fill' => 'yes',
					'ekit_heading_use_focused_title_bg!' => 'yes'
				],
            )
        );

		$this->end_controls_section();

		//Sub title Style Section
		$this->start_controls_section(
			'ekit_heading_section_sub_title_style', [
				'label'	 => esc_html__( 'Subtitle', 'elementskit-lite' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_heading_sub_title_show' => 'yes',
					'ekit_heading_sub_title!' => ''
				]
			]
		);

		$this->add_responsive_control(
			'ekit_heading_sub_title_color', [
				'label'		 => esc_html__( 'Color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
			'name'		 => 'ekit_heading_sub_title_typography',
			'selector'	 => '{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-subtitle',
			]
		);

		$this->add_responsive_control(
			'ekit_heading_sub_title_margn',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'rem', '%' ],
				'selectors' => [
                    '{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-subtitle' => 
                        'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control( 'subheading_padding', [
			'label' => esc_html__( 'Padding', 'elementskit-lite' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em'],
			'default' => [
				'top' 		=> '8',		'right'	=> '32',
				'bottom' 	=> '8',		'left'	=> '32',
				'unit' => 'px', 'isLinked' => false
			],
			'selectors' => [
				'{{WRAPPER}} .ekit-heading__subtitle-has-border' => 
					'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition' => [
				'ekit_heading_sub_title_border!' => 'yes',	
				'ekit_heading_sub_title_outline' => 'yes'	
			]
		]);


		$this->add_control(
			'ekit_heading_use_sub_title_text_fill', [
				'label'			 =>esc_html__( 'Use text fill', 'elementskit-lite' ),
				'type'			 => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' =>esc_html__( 'No', 'elementskit-lite' ),

			]
		);
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_heading_sub_title_secondary_bg',
				'label'		 => esc_html__( 'Sub Title', 'elementskit-lite' ),
                'default' => '',
				'selector' => '{{WRAPPER}} .elementskit-section-title-wraper .elementskit-section-subtitle',
				'condition' => [
					'ekit_heading_use_sub_title_text_fill' => 'yes',
				],
            )
		);

		$this->add_control(
			'ekit_heading_sub_title_border_hr',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => [
					'ekit_heading_sub_title_border' => 'yes',
				],
			]
		);

        $this->add_control(
            'ekit_heading_sub_title_border_heading_title_left',
            [
                'label' => esc_html__( 'Subtitle Border Left', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'ekit_heading_sub_title_border' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_heading_sub_title_border_color_left',
				'label'		 => esc_html__( 'Sub Title', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-section-subtitle.elementskit-style-border::before',
				'condition' => [
					'ekit_heading_sub_title_border' => 'yes',
				],
            )
		);

		
		$this->add_responsive_control(
			'ekit_heading_sub_title_border_left_width',
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
					'{{WRAPPER}} .elementskit-section-subtitle.elementskit-style-border::before' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_heading_sub_title_border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_heading_sub_title_border_heading_title_right_margin',
			[
				'label' => __( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-section-subtitle.elementskit-style-border::before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_heading_sub_title_border' => 'yes',
				],
			]
		);

        $this->add_control(
            'ekit_heading_sub_title_border_heading_title_right',
            [
                'label' => esc_html__( 'Subtitle Border Right color', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'ekit_heading_sub_title_border' => 'yes',
                ],
            ]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'ekit_heading_sub_title_border_color_right',
                'label'		 => esc_html__( 'Sub Title', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-section-subtitle.elementskit-style-border::after',
                'condition' => [
                    'ekit_heading_sub_title_border' => 'yes',
                ],
            )
        );

		$this->add_responsive_control(
			'ekit_heading_sub_title_border_right_width',
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
					'{{WRAPPER}} .elementskit-section-subtitle.elementskit-style-border::after' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_heading_sub_title_border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_heading_sub_title_border_heading_title_left_margin',
			[
				'label' => __( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-section-subtitle.elementskit-style-border::after' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_heading_sub_title_border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_heading_sub_title_border_height',
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
					'size' => 3,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-section-subtitle.elementskit-style-border::before, {{WRAPPER}} .elementskit-section-subtitle.elementskit-style-border::after' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_heading_sub_title_border' => 'yes',
				],
			]
		);

        $this->add_responsive_control(
            'ekit_heading_sub_title_vertical_alignment',
            [
                'label' => esc_html__( 'Vertical Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => -20,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-section-subtitle.elementskit-style-border::before, {{WRAPPER}} .elementskit-section-subtitle.elementskit-style-border::after' => 'transform: translateY({{SIZE}}{{UNIT}}); -webkit-transform: translateY({{SIZE}}{{UNIT}}); -ms-transform: translateY({{SIZE}}{{UNIT}})',
                ],
                'condition' => [
                    'ekit_heading_sub_title_border' => 'yes',
                ],
            ]
		);
		
		$this->add_control( 'subheading_outline_heading', [
			'label' => esc_html__( 'Outline', 'elementskit-lite' ),
			'type' => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
				'ekit_heading_sub_title_outline' => 'yes'	
			]
		]);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'subheading_outline',
				'label' => __( 'Outline', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-heading__subtitle-has-border',
				'condition' => [
					'ekit_heading_sub_title_outline' => 'yes'	
				]
			]
		);

		$this->add_responsive_control( 'subheading_outline_radius', [
			'label' => esc_html__( 'Outline Radius', 'elementskit-lite' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em'],
			'default' => [
				'top' => '2',
				'right' => '2',
				'bottom' => '2',
				'left' => '2',
				'unit' => 'em',
				'isLinked' => true
			],
			'selectors' => [
				'{{WRAPPER}} .ekit-heading__subtitle-has-border' => 
					'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition' => [
				'ekit_heading_sub_title_outline' => 'yes'	
			]
		]);

		$this->end_controls_section();

		//Extra Title Style Section
		$this->start_controls_section(
			'ekit_heading_section_extra_title_style', [
				'label'	 => esc_html__( 'Title Description', 'elementskit-lite' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_heading_section_extra_title_show' => 'yes',
					'ekit_heading_extra_title!' => ''
				]
			]
		);

		$this->add_responsive_control(
			'ekit_heading_extra_title_color', [
				'label'		 =>esc_html__( 'Title Description color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .elementskit-section-title-wraper p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
			'name'		 => 'ekit_heading_extra_title_typography',
			'selector'	 => '{{WRAPPER}} .elementskit-section-title-wraper p',
			]
		);

		$this->add_responsive_control(
			'ekit_heading_extra_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-section-title-wraper p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		//Separator Style Section
		$this->start_controls_section(
			'ekit_heading_section_seperator_style', [
				'label'	 => esc_html__( 'Separator', 'elementskit-lite' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_heading_show_seperator' => 'yes'
				]
			]
		);
		$this->add_responsive_control(
			'ekit_heading_seperator_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-border-divider' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-border-divider.elementskit-style-long' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-border-star' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'		=> [
					'ekit_heading_seperator_style!' => 'ekit_border_custom'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_heading_seperator_height',
			[
				'label' => esc_html__( 'Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-border-divider, {{WRAPPER}} .elementskit-border-divider::before' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-border-divider.elementskit-style-long' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-border-star' => 'height: {{SIZE}}{{UNIT}};',
					
				],
				'condition'		=> [
					'ekit_heading_seperator_style!' => 'ekit_border_custom'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_heading_seperator_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-section-title-wraper .ekit_heading_separetor_wraper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_heading_seperator_color', [
				'label'		 =>esc_html__( 'Separator color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-border-divider' => 'background: linear-gradient(90deg, {{VALUE}} 0%, {{VALUE}} 100%);',
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-border-divider:before' => 'background-color: {{VALUE}};box-shadow: 9px 0px 0px 0px {{VALUE}}, 18px 0px 0px 0px {{VALUE}};',
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-border-divider.elementskit-style-long' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-border-star' => 'background: linear-gradient(90deg, {{VALUE}} 0%, {{VALUE}} 38%, rgba(255, 255, 255, 0) 38%, rgba(255, 255, 255, 0) 62%, {{VALUE}} 62%, {{VALUE}} 100%);',
					'{{WRAPPER}} .elementskit-section-title-wraper .elementskit-border-star:after' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'ekit_heading_seperator_style!' => 'ekit_border_custom'
				]
			]
		);

		$this->end_controls_section();

		/** Start Heading shadow text style setion */
		$this->start_controls_section( 'shadow_text_style_section', [
			'label' => esc_html__( 'Shadow Text', 'elementskit-lite' ),
			'tab'	=> Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_shadow_text' => 'yes'
			]
		]);

		$this->add_responsive_control( 'shadow_text_position', [
			'label' => esc_html__( 'Position', 'elementskit-lite' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em', 'rem', 'vw' ],
			'allowed_dimensions' => [ 'top', 'left' ],
			'default' => [
				'top' => '-45',
				'left' => '18',
				'unit' => '%',
				'isLinked' => false
			],
			'selectors' => [
				'{{WRAPPER}} .ekit-heading__shadow-text' => 
					'top:{{TOP}}{{UNIT}};left:{{LEFT}}{{UNIT}};',
			],
		]);
		
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'		 => 'shadow_text_typography',
			'selector'	 => '{{WRAPPER}} .ekit-heading__shadow-text',
		]);

		$this->add_responsive_control( 'shadow_text_color', [
			'label'		 =>esc_html__( 'Text color', 'elementskit-lite' ),
			'type'		 => Controls_Manager::COLOR,
			'selectors'	 => [
				'{{WRAPPER}} .ekit-heading__shadow-text' => 
					'-webkit-text-fill-color: {{VALUE}};',
			],
		]);

		$this->add_control( 'shadow_text_border_heading', [
			'label' => esc_html__( 'Border', 'elementskit-lite' ),
			'type' => Controls_Manager::HEADING,
		]);

		$this->add_control( 'shadow_text_border_width', [
			'label' => __( 'Border Width', 'elementskit-lite' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 64,
					'step' => 1,
				]
			],
			'default' => [ 'unit' => 'px', 'size' => 1 ],
			'selectors' => [
				'{{WRAPPER}} .ekit-heading__shadow-text' => 
					'-webkit-text-stroke-width: {{SIZE}}{{UNIT}};'
			],
		]);

		$this->add_responsive_control( 'shadow_text_border_color', [
			'label'		 =>esc_html__( 'Border Color', 'elementskit-lite' ),
			'type'		 => Controls_Manager::COLOR,
			'selectors'	 => [
				'{{WRAPPER}} .ekit-heading__shadow-text' => 
					'-webkit-text-stroke-color: {{VALUE}};',
			],
		]);

		$this->end_controls_section();
		/** End Heading shadow text style setion */

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

		// Sanitize Title & Sub-Title Tags
		$options_ekit_heading_title_tag = array_keys([
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
		$title_tag = \ElementsKit_Lite\Utils::esc_options($ekit_heading_title_tag, $options_ekit_heading_title_tag, 'h2');

		// Sanitize Sub Title Tag
		$options_ekit_heading_sub_title_tag = array_keys([
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
		$sub_title_tag = \ElementsKit_Lite\Utils::esc_options($ekit_heading_sub_title_tag, $options_ekit_heading_sub_title_tag, 'h3');

		// Image sectionn
        $image_html = '';
        if (!empty($settings['ekit_heading_seperator_image']['url'])) {

            $this->add_render_attribute('image', 'src', $settings['ekit_heading_seperator_image']['url']);
            $this->add_render_attribute('image', 'alt', Control_Media::get_image_alt($settings['ekit_heading_seperator_image']));
            $this->add_render_attribute('image', 'title', Control_Media::get_image_title($settings['ekit_heading_seperator_image']));

            $image_html = Group_Control_Image_Size::get_attachment_image_html($settings, 'ekit_heading_seperator_image_size', 'ekit_heading_seperator_image');

        }

		$seperator = '';
		if ($ekit_heading_seperator_style != 'ekit_border_custom') {
			$seperator = ($ekit_heading_show_seperator == 'yes') ? '<div class="ekit_heading_separetor_wraper ekit_heading_'. esc_attr($ekit_heading_seperator_style) .'"><div class="'. esc_attr($ekit_heading_seperator_style) .'"></div></div>' : '';
		} else {
			$seperator = ($ekit_heading_show_seperator == 'yes') ? '<div class="ekit_heading_separetor_wraper ekit_heading_'. esc_attr($ekit_heading_seperator_style) .'"><div class="'. esc_attr($ekit_heading_seperator_style) .'">'.$image_html.'</div></div>' : '';
		}


		$title_text_fill = ($ekit_heading_use_title_text_fill == 'yes') ? 'text_fill' : '';

		$sub_title_text_fill =	 ($settings['ekit_heading_use_sub_title_text_fill'] == 'yes') ? 'elementskit-gradient-title' : '';

		$sub_title_border =	 ($settings['ekit_heading_sub_title_border'] == 'yes') ? 'elementskit-style-border' : '';

		$title_border = (isset($show_title_border) && $show_title_border == 'yes') ? ' ekit-heading__title-has-border '. esc_attr($title_border_position) : '';
		$subheading_outline = (isset($ekit_heading_sub_title_outline) && $ekit_heading_sub_title_outline == 'yes') ? ' ekit-heading__subtitle-has-border' : '';
		$title_in_left = (isset($title_float_left) && $title_float_left == 'yes' ) ? ' ekit-heading__title-in-left' : '';

		$ekit_heading_align_tablet = isset($settings['ekit_heading_title_align_tablet']) ? $ekit_heading_title_align_tablet : '';
		$ekit_heading_align_mobile = isset($settings['ekit_heading_title_align_mobile']) ? $ekit_heading_title_align_mobile : '';

		echo '<div class="ekit-heading elementskit-section-title-wraper '.esc_attr($ekit_heading_title_align).'   ekit_heading_tablet-'. esc_attr($ekit_heading_align_tablet) .'   ekit_heading_mobile-'. esc_attr($ekit_heading_align_mobile) .''.esc_attr($title_in_left).'">';

			if(!empty($shadow_text_content) && $show_shadow_text == 'yes' ): ?>
				<span class='ekit-heading__shadow-text'>
					<?php echo wp_kses(\ElementsKit_Lite\Utils::kspan($shadow_text_content), \ElementsKit_Lite\Utils::get_kses_array()); ?>
				</span>
			<?php endif;

			if($title_float_left == 'yes'):?>
				<div class='ekit-heading__title-wrapper'>
			<?php endif;

			echo (($ekit_heading_seperator_position) == 'top') ? wp_kses($seperator, \ElementsKit_Lite\Utils::get_kses_array()): '';
			if($ekit_heading_sub_title_position == 'before_title' && $title_float_left != 'yes'){
				if((!empty($ekit_heading_sub_title) && ($settings['ekit_heading_sub_title_show'] == 'yes'))):
					echo '<'. esc_attr($sub_title_tag).' class="elementskit-section-subtitle '.esc_attr($sub_title_text_fill).' '.esc_attr($sub_title_border).''.esc_attr($subheading_outline).'">
						'.esc_html( $ekit_heading_sub_title ).'
					</'.esc_attr($sub_title_tag).'>';
				endif;
			}

			$ekit_title = \ElementsKit_Lite\Utils::kspan($ekit_heading_title);

			echo (($ekit_heading_seperator_position) == 'before') ? wp_kses($seperator, \ElementsKit_Lite\Utils::get_kses_array()) : '';
			if(!empty($ekit_heading_title)):
				if ( ! empty( $ekit_heading_link['url'] ) ) {
					$this->add_link_attributes( 'ekit_heading_link', $ekit_heading_link );

					echo('<a '.$this->get_render_attribute_string( 'ekit_heading_link' ).'> '. '<'.esc_attr($title_tag).' class="ekit-heading--title elementskit-section-title '.esc_attr($title_text_fill.''.$title_border).'">
					'.wp_kses($ekit_title, \ElementsKit_Lite\Utils::get_kses_array()).'
					</'.esc_attr($title_tag).'>' .'</a>');
				}else {
					echo ('<'.esc_attr($title_tag).' class="ekit-heading--title elementskit-section-title '.esc_attr($title_text_fill.''.$title_border).'">
					'.wp_kses($ekit_title, \ElementsKit_Lite\Utils::get_kses_array()).'
					</'.esc_attr($title_tag).'>');
				}
			endif;

			echo (
				$ekit_heading_seperator_position == 'after' || (
					$ekit_heading_seperator_position == 'bottom' && 
					$title_float_left == 'yes'
				)
			) ? wp_kses($seperator, \ElementsKit_Lite\Utils::get_kses_array()) : '';

			// End Title wrapper
			if($title_float_left == 'yes'): ?>
				</div>
				<div class='ekit-heading__content-wrapper'>
			<?php endif;

			if($ekit_heading_sub_title_position == 'after_title' || ($ekit_heading_sub_title_position == 'before_title' && $title_float_left == 'yes')){
				if(!empty($ekit_heading_sub_title) && ($settings['ekit_heading_sub_title_show'] == 'yes')):
					echo '<'.esc_html($sub_title_tag).' class="ekit-heading--subtitle elementskit-section-subtitle '.esc_attr($sub_title_text_fill).' '.esc_attr($sub_title_border).''.esc_attr($subheading_outline).'">
						'.esc_html( $ekit_heading_sub_title ).'
					</'.esc_html($sub_title_tag).'>';
				endif;
			}

			if((!empty($ekit_heading_extra_title)) && ($settings['ekit_heading_section_extra_title_show'] == 'yes')): ?>
				<div class='ekit-heading__description'>
					<?php echo wp_kses(wpautop($ekit_heading_extra_title), \ElementsKit_Lite\Utils::get_kses_array()); ?>
				</div>
			<?php endif;

			if($title_float_left == 'yes'): ?>
				</div>
			<?php endif;

			echo ($ekit_heading_seperator_position == 'bottom' && $title_float_left != 'yes') ? wp_kses($seperator, \ElementsKit_Lite\Utils::get_kses_array()) : '';

		echo '</div>';
    }
}
