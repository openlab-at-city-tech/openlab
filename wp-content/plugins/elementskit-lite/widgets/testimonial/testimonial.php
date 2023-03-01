<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Testimonial_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Testimonial extends Widget_Base {
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
        return Handler::get_keywords();
    }

    public function get_help_url() {
        return 'https://wpmet.com/doc/how-to-create-testimonials-in-wordpress/';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'ekit_testimonial_layout_section_tab_style',
            [
                'label' => esc_html__('Layout', 'elementskit-lite'),
            ]
        );


        // Card style

		$this->add_control(
            'ekit_testimonial_style',
            [
                'label' => esc_html__('Choose Style', 'elementskit-lite'),
                'type' => ElementsKit_Controls_Manager::IMAGECHOOSE,
                'default' => 'style1',
                'options' => [
					'style1' => [
						'title' => esc_html__( 'Default', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/1.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/1.png',
                        'width' => '33.33%',
					],
					'style2' => [
						'title' => esc_html__( 'Grid Style without image', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/2.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/2.png',
                        'width' => '33.33%',
					],
					'style3' => [
						'title' => esc_html__( 'Image with Ratting', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/3.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/3.png',
                        'width' => '33.33%',
					],
					'style4' => [
						'title' => esc_html__( 'image style 4', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/4.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/4.png',
                        'width' => '33.33%',
					],
					'style5' => [
						'title' => esc_html__( 'image style 5', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/5.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/5.png',
                        'width' => '33.33%',
					],
					'style6' => [
						'title' => esc_html__( 'image style 6', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/6.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/6.png',
                        'width' => '33.33%',
					],
				],
            ]
        );

		$this->end_controls_section();
        $this->start_controls_section(
            'ekit_testimonial_section_tab_style',
            [
                'label' => esc_html__('Testimonial', 'elementskit-lite'),
            ]
        );

		// enable warter mark icon
		$this->add_control(
            'ekit_testimonial_wartermark_enable',
            [
                'label' => esc_html__( 'Enable Quote Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'ekit_testimonial_style' => ['style2', 'style4', 'style5', 'style6']
				]
            ]
		);

		$this->add_control(
            'ekit_testimonial_wartermarks',
            [
                'label' => esc_html__( 'Quote Icon', 'elementskit-lite' ),
                'label_block' => true,
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_testimonial_wartermark',
                'default' => [
                    'value' => 'icon icon-quote',
                    'library' => 'ekiticons',
                ],
                'condition' => [
					'ekit_testimonial_wartermark_enable' => 'yes',
					'ekit_testimonial_style' => ['style2', 'style4', 'style5', 'style6'],
				],
            ]
		);

		// water mark position
		$this->add_control(
			'ekit_testimonial_wartermark_position',
			[
				'label' => esc_html__( 'Quote Icon Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bottom',
				'separator'    => 'before',
				'options' => [
					'top'  => esc_html__( 'Top', 'elementskit-lite' ),
					'bottom' => esc_html__( 'Bottom', 'elementskit-lite' ),
				],
				'condition' => [
					'ekit_testimonial_wartermark_enable' => 'yes',
					'ekit_testimonial_style' => ['style5']
				]
			]
		);

		$this->add_control(
			'ekit_testimonial_wartermark_mask_show_badge',
			[
				'label' => esc_html__( 'Show Quote Icon Badge', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'separator'    => 'before',
				'condition' => [
					'ekit_testimonial_wartermark_enable' => 'yes',
					'ekit_testimonial_style' => ['style6']
				]
			]
		);

		$this->add_control(
			'ekit_testimonial_wartermark_custom_position',
			[
				'label' => esc_html__( 'Custom Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator'	=> 'before',
				'condition' => [
					'ekit_testimonial_wartermark_enable' => 'yes',
					'ekit_testimonial_style'			 => 'style2',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_wartermark_custom_position_offset_x',
			[
				'label' => esc_html__( 'Left', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_watermark_icon_custom_position' => 'left: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'ekit_testimonial_wartermark_enable'		  => 'yes',
					'ekit_testimonial_wartermark_custom_position' => 'yes',
					'ekit_testimonial_style'			 		  => 'style2',
				]
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_wartermark_custom_position_offset_y',
			[
				'label' => esc_html__( 'top', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_watermark_icon_custom_position' => 'top: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'ekit_testimonial_wartermark_enable' 		  => 'yes',
					'ekit_testimonial_wartermark_custom_position' => 'yes',
					'ekit_testimonial_style'			 		  => 'style2',
				]
			]
		);

		$this->add_control(
			'ekit_testimonial_before_rating',
			[
				'type' 		=> Controls_Manager::DIVIDER,
				'condition'	=> [
					'ekit_testimonial_style!' => ['style1', 'style3'],
				],
			]
		);

		// enable rating
		$this->add_control(
            'ekit_testimonial_rating_enable',
            [
                'label' => esc_html__( 'Enable Rating', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'ekit_testimonial_style' => ['style3', 'style4', 'style5', 'style6']
				]
            ]
		);

		// enable title separetor
		$this->add_control(
            'ekit_testimonial_title_separetor',
            [
                'label'     => esc_html__( 'Show Separator', 'elementskit-lite' ),
                'type'      => Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'default'   => 'yes',
				'condition' => [
					'ekit_testimonial_style' => ['style1', 'style2'],
				]
            ]
		);

		$repeater = new Repeater();

        $repeater->add_control(
            'client_name', [
                'label' => esc_html__('Client Name', 'elementskit-lite'),
				'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'default' => esc_html__('Testimonial #1', 'elementskit-lite'),
				'label_block' => true,
            ]
        );

        $repeater->add_control(
            'designation', [
                'label' => esc_html__('Designation', 'elementskit-lite'),
				'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
				'label_block' => true,
				'default' => esc_html__('Designation', 'elementskit-lite'),
            ]
        );

        $repeater->add_control(
            'review', [
				'label' => esc_html__('Testimonial Review', 'elementskit-lite'),
				'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
				'label_block' => true,
				'default' => esc_html__('Review Text', 'elementskit-lite'),
            ]
        );

        $repeater->add_control(
            'rating', [
				'label' => esc_html__('Testimonial Rating', 'elementskit-lite'),
				'type' => Controls_Manager::SELECT,
				'default' => '5',
				'options'   => [
					'5'     => esc_html__( '5', 'elementskit-lite' ),
					'4'     => esc_html__( '4', 'elementskit-lite' ),
					'3'     => esc_html__( '3', 'elementskit-lite' ),
					'2'     => esc_html__( '2', 'elementskit-lite' ),
					'1'     => esc_html__( '1', 'elementskit-lite' ),
				],
				'label_block' => true,
            ]
        );

		$repeater->add_control(
			'link',
			[
				'label'			=> esc_html__( 'Link', 'elementskit-lite' ),
				'type'			=> Controls_Manager::URL,
				'dynamic'		=> [
					'active' => true,
				],
				'placeholder'	=> esc_url( 'https://wpmet.com', 'elementskit-lite' ),
			]
		);

        $repeater->add_control(
            'client_photo', [
				'label' => esc_html__('Client Avatar', 'elementskit-lite'),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
					'id'    => -1
				],
				'separator'	=> 'before',
            ]
        );

        $repeater->add_control(
            'client_logo', [
				'label' => esc_html__('Logo', 'elementskit-lite'),
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
            'use_hover_logo', [
				'label' => esc_html__( 'Display different logo on hover?', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'default' => 'no',
				'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'client_logo_active', [
				'label' => esc_html__('Logo Active', 'elementskit-lite'),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
					'id'    => -1
				],
				'condition' => ['use_hover_logo' => 'yes'],
            ]
        );

		$repeater->add_control(
            'ekit_testimonial_active', [
				'label' => esc_html__( 'Active Testimonial?', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => '',
            ]
        );

		$repeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_testimonial_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic'],
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
            ]
        );

        $this->add_control(
            'ekit_testimonial_data',
            [
                'label' => esc_html__('Testimonial', 'elementskit-lite'),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [ 'client_name' => esc_html__('Testimonial #1', 'elementskit-lite') ],
                    [ 'client_name' => esc_html__('Testimonial #2', 'elementskit-lite') ],
                    [ 'client_name' => esc_html__('Testimonial #3', 'elementskit-lite') ],
                ],

                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ client_name }}}',
            ]
		);



		$this->end_controls_section();

		// setting section

        $this->start_controls_section(
            'ekit_testimonial_layout_settings',
            [
                'label' => esc_html__( 'Settings', 'elementskit-lite' ),
            ]
        );

		$this->add_responsive_control(
			'ekit_testimonial_left_right_spacing',
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
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-slide' => 'margin-right: {{SIZE}}{{UNIT}};margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-testimonial-slider' => '--ekit_testimonial_left_right_spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_top_bottom_spacing',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_testimonial_slidetoshow',
			[
				'label' => esc_html__( 'Slides To Show', 'elementskit-lite' ),
				'type' =>  Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 20,
				'step' => 1,
				'default' => 1,
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider' => '--ekit_testimonial_slidetoshow:  {{SIZE}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_testimonial_slidesToScroll',
			[
				'label' => esc_html__( 'Slides To Scroll', 'elementskit-lite' ),
				'type' =>  Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 20,
				'step' => 1,
				'default' => 1,
			]
		);

        $this->add_control(
			'ekit_testimonial_speed',
			[
				'label' => esc_html__( 'Speed', 'elementskit-lite' ),
				'type' =>  Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10000,
				'step' => 1,
				'default' => 1000,
			]
		);

		$this->add_control(
			'ekit_testimonial_autoplay',
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
			'ekit_testimonial_show_arrow',
			[
				'label' => esc_html__( 'Show Arrow', 'elementskit-lite' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => '',
			]
        );

        $this->add_control(
			'ekit_testimonial_show_dot',
			[
				'label' => esc_html__( 'Show Dots', 'elementskit-lite' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

        $this->add_control(
			'ekit_testimonial_left_arrows',
			[
				'label' => esc_html__( 'Left Arrow Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_testimonial_left_arrow',
                'default' => [
                    'value' => 'icon icon-left-arrow2',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                        'ekit_testimonial_show_arrow' => 'yes',
                ]
			]
        );

        $this->add_control(
			'ekit_testimonial_right_arrows',
			[
				'label' => esc_html__( 'Right Arrow Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_testimonial_right_arrow',
                'default' => [
                    'value' => 'icon icon-right-arrow2',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_testimonial_show_arrow' => 'yes',
                ]
			]
		);

		$this->add_control(
			'ekit_testimonial_loop',
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
            'ekit_testimonial_pause_on_hover',
            [
                'label' => esc_html__( 'Pause on Hover', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

		// layout controll style start
		 $this->start_controls_section(
		    'ekit_testimonial_section_layout', [
			    'label'	 => esc_html__( 'Layout', 'elementskit-lite' ),
			    'tab'	 => Controls_Manager::TAB_STYLE,
		    ]
	    );
			$this->add_responsive_control(
				'ekit_testimonial_layout_margin',
				[
					'label'         => esc_html__('Column Gap', 'elementskit-lite'),
					'type'          => Controls_Manager::SLIDER,
					'size_units'    => ['px', 'em'],
					'selectors' => [
						'{{WRAPPER}} .elementskit-tootltip-testimonial .elementskit-commentor-content, {{WRAPPER}} .elementskit-single-testimonial-slider, {{WRAPPER}}  .elementskit-testimonial_card' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);


			$this->add_responsive_control(
				'ekit_testimonial_layout_padding',
				[
					'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .elementskit-tootltip-testimonial .elementskit-commentor-content, {{WRAPPER}} .elementskit-single-testimonial-slider, {{WRAPPER}}  .elementskit-testimonial_card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'ekit_testimonial_client_parent_container_margin',
				[
					'label' => esc_html__( 'Margin', 'elementskit-lite' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .elementskit-testimonial-slider-block-style' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'ekit_testimonial_style' => ['style4']
					]
				]
			);

			$this->add_responsive_control(
				'ekit_testimonial_layout_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .elementskit-tootltip-testimonial .elementskit-commentor-content, {{WRAPPER}} .elementskit-single-testimonial-slider, {{WRAPPER}} .elementskit-testimonial_card' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'ekit_testimonial_wrapper_tabs' );
				$this->start_controls_tab(
					'ekit_testimonial_wrapper_tab_normal',
					[
						'label' => esc_html__( 'Normal', 'elementskit-lite' ),
					]
				);
					$this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' => 'ekit_testimonial_layout_background',
							'label' => esc_html__( 'Background', 'elementskit-lite' ),
							'types' => [ 'classic', 'gradient' ],
							'selector' => '{{WRAPPER}} .elementskit-tootltip-testimonial .elementskit-commentor-content, {{WRAPPER}} .elementskit-single-testimonial-slider, {{WRAPPER}} .elementskit-testimonial_card, {{WRAPPER}} .elementskit-tootltip-testimonial .elementskit-commentor-content::before',
						]
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name' => 'ekit_testimonial_layout_border',
							'label' => esc_html__( 'Border', 'elementskit-lite' ),
							'selector' => '{{WRAPPER}} .elementskit-single-testimonial-slider',
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(), [
							'name'      => 'ekit_testimonial_layout_shadow',
							'selector'  => '{{WRAPPER}} .elementskit-tootltip-testimonial .elementskit-commentor-content, {{WRAPPER}} .elementskit-single-testimonial-slider, {{WRAPPER}}  .elementskit-testimonial_card',
						]
					);
				$this->end_controls_tab();
				
				$this->start_controls_tab(
					'ekit_testimonial_wrapper_tab_hover',
					[
						'label' => esc_html__( 'Hover', 'elementskit-lite' ),
					]
				);
					$this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' => 'ekit_testimonial_layout_active_background',
							'label' => esc_html__( 'Background', 'elementskit-lite' ),
							'types' => [ 'classic', 'gradient' ],
							'selector' => '{{WRAPPER}} .elementskit-single-testimonial-slider:before',
						]
					);

					$this->add_control(
						'ekit_testimonial_layout_active_border_color',
						[
							'label' => esc_html__( 'Border Color', 'elementskit-lite' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .elementskit-single-testimonial-slider:hover' => 'border-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(), [
							'name'      => 'ekit_testimonial_layout_hover_shadow',
							'selector'  => '{{WRAPPER}} .elementskit-tootltip-testimonial .elementskit-commentor-content:hover, {{WRAPPER}} .elementskit-single-testimonial-slider:hover, {{WRAPPER}}  .elementskit-testimonial_card:hover',
						]
					);

					$this->add_control(
						'ekit_testimonial_hover_effect',
						[
							'label'		=> esc_html__( 'Hover Effect', 'elementskit-lite' ),
							'type'		=> Controls_Manager::SELECT,
							'options'	=> [
								'slide'		=> esc_html__( 'Slide', 'elementskit-lite' ),
								'fade' 		=> esc_html__( 'Fade', 'elementskit-lite' ),
							],
							'default'	=> 'slide',
							'prefix_class'	=> 'ekit-testimonial-',
							'condition'	=> [
								'ekit_testimonial_style!'								=> 'style3',
								'ekit_testimonial_layout_active_background_background!' => '',
							],
						]
					);
				$this->end_controls_tab();

				$this->start_controls_tab(
					'ekit_testimonial_wrapper_tab_active',
					[
						'label' => esc_html__( 'Active', 'elementskit-lite' ),
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name' => 'ekit_testimonial_active_layout_background',
						'label' => esc_html__( 'Background', 'elementskit-lite' ),
						'types' => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active',
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(), [
						'name'      => 'ekit_testimonial_active_layout_shadow',
						'selector'  => '{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active',
					]
				);

				$this->end_controls_tab();
			$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
		    'ekit_testimonial_section_wraper_style', [
			    'label'	 => esc_html__( 'Wrapper Content Style', 'elementskit-lite' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
		    ]
		);
		

		$this->add_responsive_control(
			'ekit_testimonial_section_wraper_vertical_alignment',
			[
				'label' =>esc_html__( 'Vertical Alignment', 'elementskit-lite' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start'    => [
						'title' =>esc_html__( 'Top', 'elementskit-lite' ),
						'icon' => 'eicon-sort-up',
					],
					'center' => [
						'title' =>esc_html__( 'Center', 'elementskit-lite' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' =>esc_html__( 'Bottom', 'elementskit-lite' ),
						'icon' => 'eicon-sort-down',
					],
				],
				'selectors' => [
                    '{{WRAPPER}} .elementkit-testimonial-col' => 'align-self: {{VALUE}};'
                ],
				'default' => 'center',
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_section_wraper_horizontal_alignment',
			[
				'label' =>esc_html__( 'Horizontal Alignment', 'elementskit-lite' ),
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
                    '{{WRAPPER}} .elementskit-commentor-content' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-testimonial_card' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-profile-info' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .ekit_testimonial_style_5 .elementskit-commentor-header' => 'text-align: {{VALUE}};',
                ]
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_section_wraper_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-commentor-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_testimonial_section_wraper_use_height',
			[
				'label' => esc_html__( 'Use Fixed Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_section_height',
			[
				'label' => esc_html__( 'Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 10,
						'max' => 1000,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 500,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-commentor-content' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_testimonial_section_wraper_use_height' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		// description
		$this->start_controls_section(
			'ekit_testimonial_content_description',
			[
				'label' => esc_html__( 'Description', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_testimonial_description_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-testimonial-slider  .elementskit-commentor-content > p' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementskit-testimonial_card .elementskit-commentor-coment' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_testimonial_description_active_color',
			[
				'label' => esc_html__( 'Hover & Active Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-testimonial-slider:hover  .elementskit-commentor-content > p' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active  .elementskit-commentor-content > p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_testimonial_description_typography',
				'label' => esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-single-testimonial-slider  .elementskit-commentor-content > p, {{WRAPPER}} .elementskit-testimonial_card .elementskit-commentor-coment',
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_description_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-testimonial-slider  .elementskit-commentor-content > p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-testimonial_card .elementskit-commentor-coment' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Testimonial Review Rating

	    $this->start_controls_section(
		    'ekit_testimonial_section_testimonial_ratting_style', [
			    'label'	 => esc_html__( 'Rating', 'elementskit-lite' ),
			    'tab'	 => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_testimonial_style' => ['style3', 'style4', 'style5', 'style6'],
					'ekit_testimonial_rating_enable' => 'yes'
				]
		    ]
	    );

	    // Testimonial Review ratting Color
	    $this->add_control(
		    'ekit_testimonial_review_ratting_color', [
			    'label'		 => esc_html__( 'Color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'default'	 => '#fec42d',
			    'selectors'	 => [
				    '{{WRAPPER}} .elementskit-stars > li > a, {{WRAPPER}} .elementskit-stars > li > span' => 'color: {{VALUE}};'
			    ],
		    ]
	    );

		$this->add_control(
			'ekit_testimonial_rating_hover_color',
			[
				'label'		=> esc_html__( 'Hover & Active Color', 'elementskit-lite' ),
				'type'		=> Controls_Manager::COLOR,
				'selectors'	=> [
					'{{WRAPPER}} .elementskit-single-testimonial-slider:hover .elementskit-stars > li > a, {{WRAPPER}} .elementskit-single-testimonial-slider:hover .elementskit-stars > li > span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active .elementskit-stars > li > a, {{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active .elementskit-stars > li > span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
            'ekit_testimonial_review_ratting_font_size',
            [
                'label'         => esc_html__('Font Size', 'elementskit-lite'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-stars > li > a, {{WRAPPER}} .elementskit-stars > li > span' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
		);

		$this->add_responsive_control(
			'ekit_testimonial_review_ratting_right_spacing',
			[
				'label' => esc_html__( 'Items Margin Right', 'elementskit-lite' ),
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
					'{{WRAPPER}} .elementskit-stars > li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_review_ratting_padding',
			[
				'label' => esc_html__( 'Review Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-stars' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_review_ratting_spacing',
			[
				'label' => esc_html__( 'Review Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-stars' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

	    $this->end_controls_section();

		$this->start_controls_section(
		    'ekit_testimonial_section_wathermark_style', [
			    'label'	 => esc_html__( 'Quote Icon', 'elementskit-lite' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_testimonial_wartermark_enable' => 'yes',
					'ekit_testimonial_style!'			 => ['style1', 'style3'],
				]
		    ]
		);

		$this->start_controls_tabs(
            'ekit_testimonial_client_watermark_color_tabs'
        );

        $this->start_controls_tab(
            'ekit_testimonial_client_watermark_normal_color_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
		);

		// Testimonial wathermark Color
	    $this->add_responsive_control(
		    'ekit_testimonial_section_wathermark_color', [
			    'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
			    'type'		 => Controls_Manager::COLOR,
			    'selectors'	 => [
				    '{{WRAPPER}} .elementskit-single-testimonial-slider .elementskit-watermark-icon > i' => 'color: {{VALUE}};',
				    '{{WRAPPER}} .elementskit-testimonial-slider-block-style .elementskit-commentor-content > i' => 'color: {{VALUE}};',
				    '{{WRAPPER}} .elementskit-testimonial-slider-block-style-two .elementskit-icon-content > i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-testimonial-slider-block-style-three .elementskit-icon-content > i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-watermark-icon svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};'
			    ],
		    ]
	    );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_testimonial_section_wathermark_icon_badge_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-commentor-content > i, {{WRAPPER}} .elementskit-icon-content > i,{{WRAPPER}} .elementskit-watermark-icon > i, {{WRAPPER}} .elementskit-watermark-icon svg',
				'condition' => [
					'ekit_testimonial_style!' => 'style6'
				]
			]
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_testimonial_client_watermark_active_color_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
		);

		$this->add_responsive_control(
            'ekit_testimonial_section_wathermark_active_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .elementskit-single-testimonial-slider:hover .elementskit-watermark-icon > i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-testimonial-slider-block-style:hover .elementskit-commentor-content > i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-testimonial-slider-block-style-two:hover .elementskit-icon-content > i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-testimonial-slider-block-style-three:hover .elementskit-icon-content > i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-single-testimonial-slider:hover .elementskit-watermark-icon svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active:hover .elementskit-watermark-icon > i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active:hover .elementskit-watermark-icon svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',


                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_testimonial_section_wathermark_icon_badge_hover_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}}.elementskit-single-testimonial-slider:hover .elementskit-commentor-content > i, {{WRAPPER}} .elementskit-single-testimonial-slider:hover .elementskit-icon-content > i,{{WRAPPER}} .elementskit-single-testimonial-slider:hover .elementskit-watermark-icon > i, {{WRAPPER}} .elementskit-single-testimonial-slider:hover .elementskit-watermark-icon svg,
				
				{{WRAPPER}}.elementskit-single-testimonial-slider.testimonial-active:hover .elementskit-commentor-content > i, {{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active:hover .elementskit-icon-content > i,{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active:hover .elementskit-watermark-icon > i, {{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active:hover .elementskit-watermark-icon svg
				',
				'condition' => [
					'ekit_testimonial_style!' => 'style6'
				]
			]
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_testimonial_client_watermark_hover_color_tab',
            [
                'label' => esc_html__( 'Active', 'elementskit-lite' ),
            ]
		);

		$this->add_responsive_control(
            'ekit_testimonial_section_wathermark_hover_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active .elementskit-watermark-icon > i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active .elementskit-watermark-icon svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_testimonial_section_wathermark_icon_badge_active_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active .elementskit-commentor-content > i, {{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active .elementskit-icon-content > i, {{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active .elementskit-watermark-icon > i, {{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active .elementskit-watermark-icon svg',
				'condition' => [
					'ekit_testimonial_style!' => 'style6'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'ekit_testimonial_client_watermark_color_tab_end',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

	    // Testimonial wathermark icon size
		$this->add_responsive_control(
			'ekit_testimonial_section_wathermark_typography',
			[
				'label' => esc_html__( 'Icon Size', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-watermark-icon > i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-watermark-icon > svg'	=> 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_section_wathermark_margin_bottom',
			[
				'label' => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider-block-style .elementskit-commentor-content > i' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-testimonial-slider-block-style-three .elementskit-icon-content > i' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-watermark-icon'	=> 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_section_wathermark_icon_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-commentor-content > i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-icon-content > i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-watermark-icon > i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-watermark-icon svg'	=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_section_wathermark_icon_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-commentor-content > i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-icon-content > i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-watermark-icon > i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-watermark-icon svg'	=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_testimonial_style!' => 'style6'
				],
			]
		);

		$this->add_control(
			'ekit_testimonial_section_wathermark_badge_devider',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => [
					'ekit_testimonial_wartermark_mask_show_badge' => 'yes'
				]
			]
		);

		// watermark badge
		$this->add_responsive_control(
			'ekit_testimonial_section_wathermark_badge_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider-block-style-three .elementskit-icon-content.commentor-badge::before' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_testimonial_wartermark_mask_show_badge' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_testimonial_section_wathermark_badge_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-testimonial-slider-block-style-three .elementskit-icon-content.commentor-badge::before',
				'condition' => [
					'ekit_testimonial_wartermark_mask_show_badge' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		// title separetor
		$this->start_controls_section(
			'ekit_testimonial_title_separetor_tab',
			[
				'label' => esc_html__( 'Title Separetor', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_testimonial_title_separetor' => 'yes',
					'ekit_testimonial_style' => ['style1', 'style2'],
				]
			]
		);

		$this->start_controls_tabs(
            'ekit_testimonial_client_title_separetor_color_tabs'
        );

        $this->start_controls_tab(
            'ekit_testimonial_client_title_separetor_normal_color_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
		);

		$this->add_control(
            'ekit_testimonial_title_separator_color',
            [
                'label'      => esc_html__( 'Separator Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .elementskit-single-testimonial-slider .elementskit-border-hr' => 'background-color: {{VALUE}};',
                ],
            ]
        );

		$this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_testimonial_client_title_separetor_active_color_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
		);

		$this->add_control(
            'ekit_testimonial_title_separator_active_color',
            [
                'label'      => esc_html__( 'Separator Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .elementskit-single-testimonial-slider:hover .elementskit-border-hr' => 'background-color: {{VALUE}};',
                ],
            ]
        );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'ekit_testimonial_client_title_separetor_color_tab_end',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

        $this->add_responsive_control(
			'ekit_testimonial_title_separator_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-testimonial-slider .elementskit-border-hr' => 'width: {{SIZE}}{{UNIT}};',
                ],
			]
        );

        $this->add_responsive_control(
			'ekit_testimonial_title_separator_height',
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
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-testimonial-slider .elementskit-border-hr' => 'height: {{SIZE}}{{UNIT}};',
                ],
			]
        );

        $this->add_responsive_control(
			'ekit_testimonial_title_separator_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-single-testimonial-slider .elementskit-border-hr' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
			]
		);

		$this->end_controls_section();

		// client style
		$this->start_controls_section(
			'ekit_testimonial_client_content_section',
			[
				'label' => esc_html__( 'Client', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		// client name heading
		$this->add_control(
			'ekit_testimonial_client_name_heading',
			[
				'label' => esc_html__( 'Client Name', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		// Client Name Color
		$this->add_control(
			'ekit_testimonial_client_name_normal_color', [
				'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .elementskit-profile-info .elementskit-author-name' => 'color: {{VALUE}};'
				],
			]
		);
		
		// Client Name Color
		$this->add_control(
			'ekit_testimonial_client_name_active_color', [
				'label'		 =>esc_html__( 'Hover & Active Color', 'elementskit-lite' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
					'{{WRAPPER}} .elementskit-single-testimonial-slider:hover .elementskit-author-name' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active .elementskit-author-name' => 'color: {{VALUE}};'
				],
			]
		);
		
		$this->add_group_control(
		    Group_Control_Typography::get_type(), [
			    'name'		 => 'ekit_testimonial_client_name_typography',
			    'selector'	 => '{{WRAPPER}} .elementskit-profile-info .elementskit-author-name',
		    ]
		);

		// client name margin bottom
		$this->add_responsive_control(
			'ekit_testimonial_client_name_spacing_bottom',
			[
				'label' => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
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
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-profile-info .elementskit-author-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// client designation heading
		$this->add_control(
			'ekit_testimonial_client_designation_heading',
			[
				'label' => esc_html__( 'Client Designation', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		// Designation Color
	    $this->add_control(
		    'ekit_testimonial_designation_normal_color', [
			    'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
			    'type'		 => Controls_Manager::COLOR,
			    'selectors'	 => [
				    '{{WRAPPER}} .elementskit-profile-info .elementskit-author-des' => 'color: {{VALUE}};'
			    ],
		    ]
	    );

		// Designation Hover Color
	    $this->add_control(
		    'ekit_testimonial_designation_active_color', [
			    'label'		 =>esc_html__( 'Hover & Active Color', 'elementskit-lite' ),
			    'type'		 => Controls_Manager::COLOR,
			    'selectors'	 => [
				    '{{WRAPPER}} .elementskit-single-testimonial-slider:hover .elementskit-author-des' => 'color: {{VALUE}};',
				    '{{WRAPPER}} .elementskit-single-testimonial-slider.testimonial-active .elementskit-author-des' => 'color: {{VALUE}};'
			    ],
		    ]
	    );

	    // Designation typography
	    $this->add_group_control(
		    Group_Control_Typography::get_type(), [
			    'name'		 => 'ekit_testimonial_designation_typography',
			    'selector'	 => '{{WRAPPER}} .elementskit-profile-info .elementskit-author-des',
		    ]
		);

		$this->add_responsive_control(
			'ekit_testimonial_client_spacing',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-commentor-bio' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// client logo heading
		$this->add_control(
			'ekit_testimonial_client_logo_heading',
			[
				'label' => esc_html__( 'Client Logo', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_testimonial_style' => ['style1', 'style2']
				]
			]
		);

		// client logo margin bottom
		$this->add_responsive_control(
			'ekit_testimonial_client_logo_margin_bottom',
			[
				'label' => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
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
					'size' => 32,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-commentor-content .elementskit-client_logo' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_testimonial_style' => ['style1', 'style2']
				]
			]
		);
		
		/**
		 * Heading: Client Image
		 */
		$this->add_control(
			'ekit_testimonial_client_image_heading',
			[
				'label' => esc_html__( 'Client Image', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_testimonial_style' => ['style1', 'style4', 'style5', 'style6']
				]
			]
		);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'ekit_testimonial_client_image_background',
					'label' => esc_html__( 'Background', 'elementskit-lite' ),
					'types' => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .elementskit-profile-image-card::before',
					'condition' => [
						'ekit_testimonial_style' => ['style1']
					]
				]
			);

			$this->add_responsive_control(
				'ekit_testimonial_client_img_pos',
				[
					'label'		=> esc_html__( 'Image Position', 'elementskit-lite' ),
					'type'		=> Controls_Manager::CHOOSE,
					'options'	=> [
						'left' => [
							'title'	=> esc_html__( 'Left', 'elementskit-lite' ),
							'icon'	=> 'eicon-caret-left',
						],
						'top' => [
							'title'	=> esc_html__( 'Top', 'elementskit-lite' ),
							'icon'	=> 'eicon-caret-up',
						],
						'bottom' => [
							'title'	=> esc_html__( 'Bottom', 'elementskit-lite' ),
							'icon'	=> 'eicon-caret-down',
						],
						'right' => [
							'title'	=> esc_html__( 'Right', 'elementskit-lite' ),
							'icon'	=> 'eicon-caret-right',
						],
					],
					'selectors_dictionary' => [
						'left'   => '-webkit-box-orient: horizontal; -webkit-box-direction: normal; -ms-flex-direction: row; flex-direction: row;',
						'top'    => '-webkit-box-orient: vertical; -webkit-box-direction: normal; -ms-flex-direction: column; flex-direction: column;',
						'bottom' => '-webkit-box-orient: vertical; -webkit-box-direction: reverse; -ms-flex-direction: column-reverse; flex-direction: column-reverse;',
						'right'  => '-webkit-box-orient: horizontal; -webkit-box-direction: reverse; -ms-flex-direction: row-reverse; flex-direction: row-reverse;',
					],
					'selectors'	=> [
						'{{WRAPPER}} .elementkit-commentor-details' => '{{VALUE}}',
					],
					'condition'	=> [
						'ekit_testimonial_style'	=> 'style5',
					],
				]
			);

			$this->add_control(
				'ekit_testimonial_client_area_alignment',
				[
					'label' => esc_html__( 'Alignment', 'elementskit-lite' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'client_left'    => [
							'title' => esc_html__( 'Left', 'elementskit-lite' ),
							'icon' => 'eicon-text-align-left',
						],
						'client_center' => [
							'title' => esc_html__( 'Center', 'elementskit-lite' ),
							'icon' => 'eicon-text-align-center',
						],
						'client_right' => [
							'title' => esc_html__( 'Right', 'elementskit-lite' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'condition' => [
						'ekit_testimonial_style' => ['style4', 'style5', 'style6']
					]
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'ekit_testimonial_client_image_border',
					'label' => esc_html__( 'Border', 'elementskit-lite' ),
					'selector' => '{{WRAPPER}} .elementskit-commentor-image > img',
					'condition' => [
						'ekit_testimonial_style' => ['style4', 'style5', 'style6']
					]
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ekit_testimonial_client_image_box_shadow',
					'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
					'selector' => '{{WRAPPER}} .elementskit-commentor-image > img',
					'condition' => [
						'ekit_testimonial_style' => ['style4', 'style5', 'style6']
					]
				]
			);

			$this->add_responsive_control(
				'ekit_testimonial_client_image_size',
				[
					'label'   => esc_html__('Image Size', 'elementskit-lite'),
					'type'    => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 300,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 70,
					],
					'selectors' => [
						'{{WRAPPER}} .elementskit-commentor-bio .elementskit-commentor-image > img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'ekit_testimonial_style' => ['style4', 'style5', 'style6']
					]
				]
			);

			$this->add_responsive_control(
				'ekit_testimonial_client_author_container_top',
				[
					'label' => esc_html__( 'Bottom', 'elementskit-lite' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => -200,
							'max' => 200,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => -98,
					],
					'selectors' => [
						'{{WRAPPER}} .elementskit-commentor-bio' => 'bottom: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'ekit_testimonial_style' => ['style4']
					]
				]
			);

			$this->add_responsive_control(
				'ekit_testimonial_client_image_margin_',
				[
					'label'			=> __( 'Margin', 'elementskit-lite' ),
					'type'			=> Controls_Manager::DIMENSIONS,
					'size_units'	=> [ 'px', '%', 'em' ],
					'selectors'		=> [
						'{{WRAPPER}} .ekit-testimonial--avatar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'		=> [
						'ekit_testimonial_style' => ['style4', 'style5', 'style6'],
					],
				]
			);
		$this->end_controls_section();

		// dot style
		$this->start_controls_section(
			'ekit_testimonial_client_dot_tab',
			[
				'label' => esc_html__( 'Dot', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_testimonial_show_dot' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_client_dot_bottom',
			[
				'label' => esc_html__( 'Dot Top Spacing', 'elementskit-lite' ),
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
					'size' => -50,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_client_dot_width',
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
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-dots li button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_client_dot_height',
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
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-dots li button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_testimonial_client_dot_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-testimonial-slider .slick-dots li button',
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_client_dot_border_radius',
			[
				'label' => esc_html__( 'Border radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-dots li button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_client_dot_spacing',
			[
				'label' => esc_html__( 'Margin right', 'elementskit-lite' ),
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
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-dots li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_testimonial_client_dot_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-testimonial-slider .slick-dots li button',
			]
		);

		$this->add_control(
			'ekit_testimonial_client_dot_active_heading',
			[
				'label' => esc_html__( 'Active', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_testimonial_client_dot_active_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-testimonial-slider .slick-dots li.slick-active button',
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_client_dot_active_width',
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
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-dots li.slick-active button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_client_dot_active_height',
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
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-dots li.slick-active button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_testimonial_client_dot_active_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-testimonial-slider .slick-dots li.slick-active button',
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_client_dot_active_scale',
			[
				'label' => esc_html__( 'Scale', 'elementskit-lite' ),
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
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-dots li.slick-active button' => 'transform: scale({{SIZE}});',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'ekit_testimonial_nav_style_tab',
			[
				'label' => esc_html__( 'Nav', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_testimonial_show_arrow' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_nav_font_size',
			[
				'label' => esc_html__( 'Font Size', 'elementskit-lite' ),
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
					'size' => 36,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-prev' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-next' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_nav_right_icon',
			[
				'label' => esc_html__( 'Prev', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_nav_left_icon',
			[
				'label' => esc_html__( 'Next', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_nav_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_nav_width',
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
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-prev' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-next' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_testimonial_nav_height',
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
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-prev' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-next' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        $this->add_responsive_control(
            'ekit_testimonial_nav_vertical_align',
            [
                'label' => esc_html__( 'vertical_align', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px','%' ],
                'range' => [
                    '%' => [
                        'min' => -500,
                        'max' => 500,
                    ],
                    'px' => [
                        'min' => -500,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-testimonial-slider .slick-arrow' => ' -webkit-transform: translateY({{SIZE}}{{UNIT}}); -ms-transform: translateY({{SIZE}}{{UNIT}}); transform: translateY({{SIZE}}{{UNIT}});',
                ],
            ]
        );


        $this->start_controls_tabs(
            'ekit_testimonial_nav_hover_normal_tabs'
        );

        $this->start_controls_tab(
            'ekit_testimonial_nav_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
		);

		$this->add_responsive_control(
			'ekit_testimonial_nav_font_color_normal',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-prev' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-next' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_testimonial_nav_background_normal',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-testimonial-slider .slick-prev, {{WRAPPER}} .elementskit-testimonial-slider .slick-next',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_testimonial_nav_box_shadow_normal',
				'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-testimonial-slider .slick-prev, {{WRAPPER}} .elementskit-testimonial-slider .slick-next',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_testimonial_nav_border_normal',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-testimonial-slider .slick-prev, {{WRAPPER}} .elementskit-testimonial-slider .slick-next',
			]
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_testimonial_nav_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
		);

		$this->add_responsive_control(
			'ekit_testimonial_nav_font_color_hover',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-prev:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementskit-testimonial-slider .slick-next:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_testimonial_nav_background_hover',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementskit-testimonial-slider .slick-prev:hover, {{WRAPPER}} .elementskit-testimonial-slider .slick-next:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_testimonial_nav_box_shadow_hover',
				'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-testimonial-slider .slick-prev:hover, {{WRAPPER}} .elementskit-testimonial-slider .slick-next:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_testimonial_nav_border_hover',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-testimonial-slider .slick-prev:hover, {{WRAPPER}} .elementskit-testimonial-slider .slick-next:hover',
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

		$testimonials = [];
		$settings = $this->get_settings_for_display();
		extract($settings);

		// Left Arrow Icon
		$migrated = isset( $settings['__fa4_migrated']['ekit_testimonial_left_arrows'] );
		// - Check if its a new widget without previously selected icon using the old Icon control
		$is_new = empty( $settings['ekit_testimonial_left_arrow'] );

		$prevArrowIcon = ($is_new || $migrated) ? (!empty($ekit_testimonial_left_arrows) && $settings['ekit_testimonial_left_arrows']['library'] != 'svg' ? $settings['ekit_testimonial_left_arrows']['value'] : '') : $settings['ekit_testimonial_left_arrow'];

		// Right Arrow Icon
		$migrated = isset( $settings['__fa4_migrated']['ekit_testimonial_right_arrows'] );
		// - Check if its a new widget without previously selected icon using the old Icon control
		$is_new = empty( $settings['ekit_testimonial_right_arrow'] );

		$nextArrowIcon = ($is_new || $migrated) ? !empty($ekit_testimonial_right_arrows) && $settings['ekit_testimonial_right_arrows']['library'] != 'svg' ? $settings['ekit_testimonial_right_arrows']['value'] : '' : $settings['ekit_testimonial_right_arrow'];

		$slides_to_show_count = $ekit_testimonial_slidetoshow ? $ekit_testimonial_slidetoshow : 1;
		$slides_to_scroll_count = $ekit_testimonial_slidesToScroll ? $ekit_testimonial_slidesToScroll : 1;

		// Config
		$config = [
			'rtl'				=> is_rtl(),
			'arrows'			=> $ekit_testimonial_show_arrow ? true : false,
			'dots'				=> $ekit_testimonial_show_dot ? true : false,
			'pauseOnHover'		=> $ekit_testimonial_pause_on_hover ? true : false,
			'autoplay'			=> $ekit_testimonial_autoplay ? true : false,
			'speed'				=> $ekit_testimonial_speed ? $ekit_testimonial_speed : 1000,
			'slidesPerGroup'	=> (int) $slides_to_scroll_count,
			'slidesPerView'		=> (int) $slides_to_show_count,
			'loop'				=> ( !empty($ekit_testimonial_loop) && $ekit_testimonial_loop == 'yes' ) ? true : false,
			'breakpoints'		=> [
                320 => [
                    'slidesPerView'      => !empty( $ekit_testimonial_slidetoshow_mobile ) ? $ekit_testimonial_slidetoshow_mobile : 1,
                    'slidesPerGroup'     => !empty( $ekit_testimonial_slidesToScroll_mobile ) ? $ekit_testimonial_slidesToScroll_mobile : 1
                ],
                768 => [
                    'slidesPerView'      => !empty( $ekit_testimonial_slidetoshow_tablet ) ? $ekit_testimonial_slidetoshow_tablet : 2,
                    'slidesPerGroup'     => !empty( $ekit_testimonial_slidesToScroll_tablet ) ? $ekit_testimonial_slidesToScroll_tablet : 1,
                ],
                1024 => [
                    'slidesPerView'      =>  $slides_to_show_count,
                    'slidesPerGroup'     =>  $slides_to_scroll_count,
                ]
            ],
		];

		// HTML Attribute
		$this->add_render_attribute(
			'wrapper',
			[
				'data-config'	=> wp_json_encode($config),
			]
		);
		
		$dir_common = Handler::get_dir() .'common/';

        $testimonials = isset($ekit_testimonial_data) ? $ekit_testimonial_data : [];
		$style = isset($ekit_testimonial_style) ? $ekit_testimonial_style : 'default';

		if (is_array($testimonials) && !empty($testimonials)):
			require Handler::get_dir() . 'style/'.$style.'.php';
	 	endif; // end if check testimonila array
    }
}
