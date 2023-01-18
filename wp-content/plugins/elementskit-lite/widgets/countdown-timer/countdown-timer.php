<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Countdown_Timer_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Countdown_Timer extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

	public $base;
    
    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->add_script_depends('final-countdown');
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

    public function get_help_url() {
        return 'https://wpmet.com/doc/countdown-timer/';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_tab', [
                'label' =>esc_html__( 'Presets', 'elementskit-lite' ),
            ]
        );


		$this->add_control(
            'ekit_countdown_timer_style',
            [
                'label' => esc_html__('Choose Style', 'elementskit-lite'),
                'type' => ElementsKit_Controls_Manager::IMAGECHOOSE,
                'default' => 'style1',
                'options' => [
					'style1' => [
						'title' => esc_html__( 'image style 1', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/1.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/1.png',
                        'width' => '100%',
					],
					'style2' => [
						'title' => esc_html__( 'image style 2', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/2.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/2.png',
                        'width' => '100%',
					],
					'style3' => [
						'title' => esc_html__( 'image style 3', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/3.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/3.png',
                        'width' => '100%',
					],
					'style4' => [
						'title' => esc_html__( 'image style 4', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/4.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/4.png',
                        'width' => '100%',
					],
					'style5' => [
						'title' => esc_html__( 'image style 5', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/5.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/5.png',
                        'width' => '100%',
					],
					'style6' => [
						'title' => esc_html__( 'image style 6', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/6.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/6.png',
                        'width' => '100%',
					],
				],
            ]
        );
        $this->end_controls_section();
        // Timer setting


        $this->start_controls_section(
            'ekit_countdown_timer_timer_setting', [
                'label' =>esc_html__( 'Timer Settings  ', 'elementskit-lite' ),
            ]
        );


		$this->add_control(
			'ekit_countdown_timer_due_time',
			[
				'label' => esc_html__( 'Countdown Due Date', 'elementskit-lite' ),
				'type' => Controls_Manager::DATE_TIME,
				'default' => date("Y-m-d", strtotime("+ 1 day")),
                'description' => esc_html__( 'Set the due date and time', 'elementskit-lite' ),
			]
		);
        $this->add_control(
            'ekit_countdown_timer_content_setting',
            [
                'label' => esc_html__( 'Custom Labels', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'ekit_countdown_timer_weeks_label',
			[
				'label' => esc_html__( 'Weeks', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Weeks', 'elementskit-lite' ),
                'condition' => ['ekit_countdown_timer_style' => 'style3'],
			]
		);


		$this->add_control(
			'ekit_countdown_timer_days_label',
			[
				'label' => esc_html__( 'Days', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'default' => esc_html__( 'Days', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_countdown_timer_hours_label',
			[
				'label' => esc_html__( 'Hours', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'default' => esc_html__( 'Hours', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_countdown_timer_minutes_hours_label',
			[
				'label' => esc_html__( 'Minutes', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'default' => esc_html__( 'Minutes', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_countdown_timer_seconds_hours_label',
			[
				'label' => esc_html__( 'Seconds', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'default' => esc_html__( 'Seconds', 'elementskit-lite' ),
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'ekit_countdown_timer_on_expire_settings',
			[
				'label' => esc_html__( 'Expire Action' , 'elementskit-lite' )
			]
		);

		$this->add_control(
			'ekit_countdown_timer_title',
			[
				'label'			=> esc_html__('On Expiry Title', 'elementskit-lite'),
				'type'			=> Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
                'default'		=> esc_html__('Countdown is finished!','elementskit-lite'),
			]
		);

		$this->add_control(
			'ekit_countdown_timer_expiry_content',
			[
				'label'			=> esc_html__('On Expiry Content', 'elementskit-lite'),
				'type'			=> Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
                'default'		=> esc_html__('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s','elementskit-lite'),
			]
		);

        $this->end_controls_section();

        // start style here........

		// content settings styles start
		 $this->start_controls_section(
            'ekit_countdown_timer_content_style', [
                'label'	 =>esc_html__( 'Content', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,

            ]
        );
		// set width for Days
        $this->add_responsive_control(
			'ekit_countdown_timer_days_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors'		 => [
                    '{{WRAPPER}} .ekit-countdown-inner'	=> 'width: {{SIZE}}{{UNIT}};',
                ],

			]
		);
		// set Height for Days
        $this->add_responsive_control(
            'ekit_countdown_timer_days_height', [
                'label'			 =>esc_html__( 'Height', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'range'			 => [
                    'px' => [
                        'min'	 => 0,
                        'max'	 => 500,
						'step' => 1,
                    ],
                ],
                'size_units'	 => ['px'],
                'selectors'		 => [
                    '{{WRAPPER}} .ekit-countdown-inner'	=> 'height: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

		// set Line Height for Days
        $this->add_responsive_control(
            'ekit_countdown_timer_days__line_height', [
                'label'			 =>esc_html__( 'Line Height', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'range'			 => [
                    'px' => [
                        'min'	 => 0,
                        'max'	 => 500,
						'step' => 1,
                    ],
                ],
                'size_units'	 => ['px'],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-content .elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-content .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box  .elementskit-timer-content,
					{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container .elementskit-inner-container,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-top,
					{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container '	=> 'line-height: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

        $this->add_responsive_control(
            'ekit_countdown_timer_content_margin_bottom', [
                'label'			 =>esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'range'			 => [
                    'px' => [
                        'min'	 => 0,
                        'step'	 => 1,
                    ],
                ],
                'desktop_default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 15,
					'unit' => 'px',
				],
                'size_units'	 => ['px'],
                'selectors'		 => [
                    '{{WRAPPER}} .ekit-countdown-inner'	=> 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		 $this->end_controls_section();

		 // end content settings

		//weeks Style Section
        $this->start_controls_section(
            'ekit_countdown_timer_weeks_style', [
                'label'	 =>esc_html__( 'Weeks', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
				 'condition'		=> [
					'ekit_countdown_timer_style' => 'style3'
				],
            ]
        );

		// Start Digits for weeks
        $this->add_control(
            'ekit_countdown_timer_weeks_heading_digits',
            [
                'label' => esc_html__( 'Digits', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
            ]
        );
		// Set Digits color for weeks
        $this->add_control(
            'ekit_countdown_timer_weeks_digits_color', [
                'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .elementskit-flip-clock  > .elementskit-wks  > .elementskit-count' => 'color: {{VALUE}};'
                ],
            ]
        );
		// Set Digits typeography for weeks
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_countdown_timer_weeks_digits_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-flip-clock  > .elementskit-wks  > .elementskit-count',
            ]
        );

		// Set Digits margin for weeks
        $this->add_responsive_control(
            'ekit_countdown_timer_weeks_digits_margin_bottom', [
                'label'			 =>esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                ],
                'range'			 => [
                    'px' => [
                        'min'	 => -30,
                        'step'	 => 1,
                    ],
                ],
                'size_units'	 => ['px'],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-flip-clock  > .elementskit-wks  > .elementskit-count'	=> 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_countdown_timer_weeks_label_title',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'ekit_countdown_timer_weeks_label_color', [
                'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .elementskit-flip-clock  > .elementskit-wks  > .elementskit-label' => 'color: {{VALUE}};'
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_countdown_timer_weeks_label_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-flip-clock  > .elementskit-wks  > .elementskit-label',
                'fields_options' => [
                    'font_weight' => [
                        'default' => '400',
                    ],
                    'font_family' => [
                        'default' => 'Lato',
                    ],
                    'font_size' => [ 'default' => [ 'unit' => 'px', 'size' => 14 ] ]
                ],
                'seperator' => 'before'
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_weeks_label_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-flip-clock  > .elementskit-wks  > .elementskit-label',
                'seperator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_countdown_timer_weeks_label_border_color',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-flip-clock  > .elementskit-wks  > .elementskit-label',
            ]
        );
        $this->add_responsive_control(
            'ekit_countdown_timer_weeks_label_border_radious_open',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-flip-clock  > .elementskit-wks  > .elementskit-label, ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_countdown_timer_weeks_label_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-flip-clock  > .elementskit-wks  > .elementskit-label',
            ]
        );


		$this->add_responsive_control(
            'ekit_countdown_timer_weeks_lebel_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],

                'selectors' => [
								'{{WRAPPER}} .elementskit-flip-clock > .elementskit-wks > .elementskit-label
					' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

// start genaral setting styles
        $this->add_control(
            'ekit_countdown_timer_weeks_heading_general',
            [
                'label' => esc_html__( 'General', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_weeks_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-flip-clock  > .elementskit-wks .elementskit-count',
                'seperator' => 'before'
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_countdown_timer_weeks_border_color_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock  > .elementskit-wks ',

            ]
        );
        $this->add_responsive_control(
            'ekit_countdown_timer_weeks_border_radious_open',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-flip-clock > .elementskit-wks ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_countdown_timer_weeks_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-flip-clock > .elementskit-wks ',

            ]
        );

        $this->end_controls_section();

// end digit section styles for Weeks


		//Days Style Section
        $this->start_controls_section(
            'ekit_countdown_timer_days_style', [
                'label'	 =>esc_html__( 'Days', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
            ]
        );

		// Start Digits for Days
        $this->add_control(
            'ekit_countdown_timer_days_heading_digits',
            [
                'label' => esc_html__( 'Digits', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
            ]
        );
		// Set Digits color for Days
        $this->add_control(
            'ekit_countdown_timer_days_digits_color', [
                'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-timer-content > span.elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-days .elementskit-count' => 'color: {{VALUE}};'
                ],
            ]
        );
		// Set Digits typeography for Days
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_countdown_timer_days_digits_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-timer-content > span.elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock .elementskit-days .elementskit-count',
            ]
        );

		// Set Digits margin for Days
        $this->add_responsive_control(
            'ekit_countdown_timer_days_digits_margin_bottom', [
                'label'			 =>esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'range'			 => [
                    'px' => [
                        'min'	 => -30,
                        'step'	 => 1,
                    ],
                ],
                'size_units'	 => ['px'],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-timer-content > span.elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-days .elementskit-count'	=> 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_countdown_timer_days_label_title',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'ekit_countdown_timer_days_label_color', [
                'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-timer-content > span.elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-days .elementskit-label' => 'color: {{VALUE}};'
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_countdown_timer_days_label_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-flip-clock .elementskit-days .elementskit-label,
								{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-title',
                'fields_options' => [
                    // Inner control name
                    'font_weight' => [
                        // Inner control settings
							'default' => '400',
                    ],
                    'font_family' => [
                        'default' => 'Lato',
                    ],
                    'font_size' => [ 'default' => [ 'unit' => 'px', 'size' => 14 ] ]
                ],
                'seperator' => 'before'
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_days_label_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-flip-clock .elementskit-days .elementskit-label,
								{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-title
								',
                'seperator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_countdown_timer_days_label_border_color',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => ' {{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-days .elementskit-label
								',
            ]
        );
        $this->add_responsive_control(
            'ekit_countdown_timer_days_label_border_radious_open',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
								'{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-days .elementskit-label
					' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_countdown_timer_days_label_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-days .elementskit-label
				',
            ]
        );

		$this->add_responsive_control(
            'ekit_countdown_timer_days_lebel_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],

                'selectors' => [
								'{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-days .elementskit-label
					' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
// start genaral settings
        $this->add_control(
            'ekit_countdown_timer_days_heading_general',
            [
                'label' => esc_html__( 'General', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );



        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_days_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-inner-container,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
								{{WRAPPER}} .elementskit-flip-clock  > .elementskit-days .elementskit-count ',
                'seperator' => 'before'
            ]
        );

		// overlay color

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_countdown_timer_days_border_color_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-inner-container,
				{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock .elementskit-days ',
            ]
        );
        $this->add_responsive_control(
            'ekit_countdown_timer_days_border_radious_open',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-inner-container,
					{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-days ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_countdown_timer_days_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-days .elementskit-inner-container,
				{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-days .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock .elementskit-days ',
            ]
        );

        $this->end_controls_section();

        // end digit section styles for Days


        //Hours Style Section start
        $this->start_controls_section(
            'ekit_countdown_timer_hours_style', [
                'label'	 =>esc_html__( 'Hours', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ekit_countdown_timer_hours_heading_digits',
            [
                'label' => esc_html__( 'Digits', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'ekit_countdown_timer_hours_digits_color', [
                'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [
								'{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-timer-content > span.elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-hrs .elementskit-count' => 'color: {{VALUE}};'
							],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_countdown_timer_hours_digits_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-timer-content > span.elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-hrs .elementskit-count',
            ]
        );
        $this->add_responsive_control(
            'ekit_countdown_timer_hours_digits_margin_bottom', [
                'label'			 =>esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                ],
                'range'			 => [
                    'px' => [
                        'min'	 => -30,
                        'step'	 => 1,
                    ],
                ],
                'size_units'	 => ['px'],

				'selectors'		 => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-timer-content > span.elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-hrs .elementskit-count'	=> 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'ekit_countdown_timer_hours_label_title',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'ekit_countdown_timer_hours_label_color', [
                'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,

				 'selectors'	 => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-timer-content > span.elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-hrs .elementskit-label' => 'color: {{VALUE}};'
                ],
            ]
        );

		 $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_countdown_timer_hours_label_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-timer-content > span.elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-flip-clock .elementskit-hrs .elementskit-label',
                'fields_options' => [
                    // Inner control name
                    'font_weight' => [
                        // Inner control settings
							'default' => '400',
                    ],
                    'font_family' => [
                        'default' => 'Lato',
                    ],
                    'font_size' => [ 'default' => [ 'unit' => 'px', 'size' => 14 ] ]
                ],
                'seperator' => 'before'
            ]
        );

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_hours_label_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-flip-clock .elementskit-hrs .elementskit-label,
                {{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-title
								',
                'seperator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_countdown_timer_hours_label_border_color',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => ' {{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-timer-content > span.elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
                {{WRAPPER}} .elementskit-flip-clock .elementskit-hrs .elementskit-label
                ',
            ]
        );
        $this->add_responsive_control(
            'ekit_countdown_timer_hours_label_border_radious_open',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
								'{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-hrs .elementskit-label
					' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_countdown_timer_hours_label_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-hrs .elementskit-label
				',
            ]
        );


		$this->add_responsive_control(
            'ekit_countdown_timer_hours_lebel_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],

                'selectors' => [
								'{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-hrs .elementskit-label
					' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

// start genaral styles
        $this->add_control(
            'ekit_countdown_timer_hours_heading_general',
            [
                'label' => esc_html__( 'General', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_hours_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],

				'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-inner-container,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
								{{WRAPPER}} .elementskit-flip-clock  > .elementskit-hrs .elementskit-count ',
            ]
        );

       $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_countdown_timer_hours_border_color_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-inner-container,
				{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock .elementskit-hrs ',
            ]
        );

		$this->add_responsive_control(
            'ekit_countdown_timer_hours_border_radious_open',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-inner-container,
					{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-hrs ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
       $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_countdown_timer_hours_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-hours .elementskit-inner-container,
				{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-hours .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock .elementskit-hrs ',
            ]
        );

        $this->end_controls_section();


        //Minutes Style Section

        $this->start_controls_section(
            'ekit_countdown_timer_minutes_style', [
                'label'	 =>esc_html__( 'Minutes', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
            ]
        );

		// Start Digits for Days
        $this->add_control(
            'ekit_countdown_timer_minutes_heading_digits',
            [
                'label' => esc_html__( 'Digits', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
            ]
        );
		// Set Digits color for Days
        $this->add_control(
            'ekit_countdown_timer_minutes_digits_color', [
                'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-timer-content > span.elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-mins .elementskit-count' => 'color: {{VALUE}};'
                ],
            ]
        );
		// Set Digits typeography for Days
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_countdown_timer_minutes_digits_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-timer-content > span.elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock .eins .eount, {{WRAPPER}} .elementskit-flip-clock .elementskit-mins .elementskit-count',
            ]
        );

		// Set Digits margin for Days
        $this->add_responsive_control(
            'ekit_countdown_timer_minutes_digits_margin_bottom', [
                'label'			 =>esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                ],
                'range'			 => [
                    'px' => [
                        'min'	 => -30,
                        'step'	 => 1,
                    ],
                ],
                'size_units'	 => ['px'],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-timer-content > span.elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-mins .elementskit-count'	=> 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_countdown_timer_minutes_label_title',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'ekit_countdown_timer_minutes_label_color', [
                'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-timer-content > span.elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-mins .elementskit-label' => 'color: {{VALUE}};'
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_countdown_timer_minutes_label_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-mins .elementskit-label',
                'fields_options' => [
                    // Inner control name
                    'font_weight' => [
                        // Inner control settings
							'default' => '400',
                    ],
                    'font_family' => [
                        'default' => 'Lato',
                    ],
                    'font_size' => [ 'default' => [ 'unit' => 'px', 'size' => 14 ] ]
                ],
                'seperator' => 'before'
            ]
        );

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_minutes_label_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-flip-clock .elementskit-mins .elementskit-label,
								{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title
								',
                'seperator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_countdown_timer_minutes_label_border_color',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => ' {{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-mins .elementskit-label
								',
            ]
        );
        $this->add_responsive_control(
            'ekit_countdown_timer_minutes_label_border_radious_open',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
								'{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-mins .elementskit-label
					' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_countdown_timer_minutes_label_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-mins .elementskit-label
				',
            ]
        );

		$this->add_responsive_control(
            'ekit_countdown_timer_minutes_lebel_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],

                'selectors' => [
								'{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-mins .elementskit-label
					' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


// start genaral styles
        $this->add_control(
            'ekit_countdown_timer_minutes_heading_general',
            [
                'label' => esc_html__( 'General', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_minutes_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],

				'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-inner-container,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
								{{WRAPPER}} .elementskit-flip-clock  > .elementskit-mins .elementskit-count ',
            ]
        );

       $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_countdown_timer_minutes_border_color_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-inner-container,
				{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock .elementskit-mins ',
            ]
        );

		$this->add_responsive_control(
            'ekit_countdown_timer_minutes_border_radious_open',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-inner-container,
					{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-mins ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
       $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_countdown_timer_minutes_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-minutes .elementskit-inner-container,
				{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-minutes .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock .elementskit-mins ',
            ]
        );


        $this->end_controls_section();

		// end minutes style section


        //Seconds Style Section

        $this->start_controls_section(
            'ekit_countdown_timer_seconds_style', [
                'label'	 =>esc_html__( 'Seconds', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
            ]
        );

		// Start Digits for Days
        $this->add_control(
            'ekit_countdown_timer_seconds_heading_digits',
            [
                'label' => esc_html__( 'Digits', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
            ]
        );
		// Set Digits color for Days
        $this->add_control(
            'ekit_countdown_timer_seconds_digits_color', [
                'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-timer-content > span.elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
                    {{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-secs .elementskit-count' => 'color: {{VALUE}};'
                ],
            ]
        );
		// Set Digits typeography for Days
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_countdown_timer_seconds_digits_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-timer-content > span.elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock .elementskit-secs .elementskit-count',
            ]
        );

		// Set Digits margin for Days
        $this->add_responsive_control(
            'ekit_countdown_timer_seconds_digits_margin_bottom', [
                'label'			 =>esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'default'		 => [
                    'size' => '',
                ],
                'range'			 => [
                    'px' => [
                        'min'	 => -30,
                        'step'	 => 1,
                    ],
                ],
                'size_units'	 => ['px'],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-timer-content > span.elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-secs .elementskit-count'	=> 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_countdown_timer_seconds_label_title',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'ekit_countdown_timer_seconds_label_color', [
                'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-timer-content > span.elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
                    {{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-secs .elementskit-label' => 'color: {{VALUE}};'
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_countdown_timer_seconds_label_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-secs .elementskit-label',
                'fields_options' => [
                    // Inner control name
                    'font_weight' => [
                        // Inner control settings
							'default' => '400',
                    ],
                    'font_family' => [
                        'default' => 'Lato',
                    ],
                    'font_size' => [ 'default' => [ 'unit' => 'px', 'size' => 14 ] ]
                ],
                'seperator' => 'before'
            ]
        );

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_seconds_label_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-flip-clock .elementskit-secs .elementskit-label,
								{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title
								',
                'seperator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_countdown_timer_seconds_label_border_color',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => ' {{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-secs .elementskit-label
								',
            ]
        );
        $this->add_responsive_control(
            'ekit_countdown_timer_seconds_label_border_radious_open',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
								'{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-secs .elementskit-label
					' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_countdown_timer_seconds_label_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-secs .elementskit-label
				',
            ]
        );

		$this->add_responsive_control(
            'ekit_countdown_timer_seconds_lebel_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],

                'selectors' => [
								'{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-timer-content > span.elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-title,
								{{WRAPPER}} .elementskit-flip-clock .elementskit-secs .elementskit-label
					' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

// start genaral styles
        $this->add_control(
            'ekit_countdown_timer_seconds_heading_general',
            [
                'label' => esc_html__( 'General', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_seconds_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],

				'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-inner-container,
								{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
								{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
								{{WRAPPER}} .elementskit-flip-clock  > .elementskit-secs .elementskit-count ',
            ]
        );

       $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_countdown_timer_seconds_border_color_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-inner-container,
				{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock .elementskit-secs ',
            ]
        );

		$this->add_responsive_control(
            'ekit_countdown_timer_seconds_border_radious_open',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-inner-container,
					{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
					{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
					{{WRAPPER}} .elementskit-flip-clock .elementskit-secs ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
       $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_countdown_timer_seconds_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-countdown-timer .elementskit-timer-container.elementskit-seconds .elementskit-inner-container,
				{{WRAPPER}} .elementskit-countdown-timer-2 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-3.elementskit-version-box .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-countdown-timer-4 .elementskit-timer-container.elementskit-seconds .elementskit-timer-count,
				{{WRAPPER}} .elementskit-flip-clock .elementskit-secs ',
            ]
        );
        $this->end_controls_section();
		// end seconds style section

        //Section Background

        $this->start_controls_section(
            'ekit_countdown_timer_bg_style', [
                'label'	 =>esc_html__( 'Background', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_countdown_timer_style' => 'style6'
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_countdown_timer_content_height', [
                'label'			 =>esc_html__( 'Height', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range'			 => [
                    'px' => [
                        'min'	 => 0,
                        'step'	 => 1,
                    ],
                ],
                'desktop_default' => [
					'size' => 120,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 100,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-countdown-container .elementskit-countdown-timer-4'	=> 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_countdown_timer_content_line_height', [
                'label'			 =>esc_html__( 'Line Height', 'elementskit-lite' ),
                'type'			 => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range'			 => [
                    'px' => [
                        'min'	 => 0,
                        'step'	 => 1,
                    ],
                ],
                'desktop_default' => [
					'size' => 120,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 100,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
                'selectors'		 => [
                    '{{WRAPPER}} .elementskit-countdown-container .elementskit-countdown-timer-4'	=> 'line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_countdown_timer_outer_section_bg_style',
            [
                'label' => esc_html__( 'Outer Part', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_outer_background_group',
                'label' => esc_html__( 'Outer Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-countdown-container .elementskit-countdown-timer-4',
            ]
        );
        $this->add_control(
            'ekit_countdown_timer_inner_section_bg_style',
            [
                'label' => esc_html__( 'Inner Part', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_countdown_timer_inner_background_group',
                'label' => esc_html__( 'Inner Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-countdown-container',
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

	   $data = '';
		if(isset($ekit_countdown_timer_weeks_label)){
			$data .= ' data-date-ekit-week="'.esc_attr($ekit_countdown_timer_weeks_label).'"';
		}
		if(isset($ekit_countdown_timer_days_label)){
			$data .= ' data-date-ekit-day="'.esc_attr($ekit_countdown_timer_days_label).'"';
		}
		if(isset($ekit_countdown_timer_hours_label)){
			$data .= ' data-date-ekit-hour="'.esc_attr($ekit_countdown_timer_hours_label).'"';
		}
		if(isset($ekit_countdown_timer_minutes_hours_label)){
			$data .= ' data-date-ekit-minute="'.esc_attr($ekit_countdown_timer_minutes_hours_label).'"';
		}
		if(isset($ekit_countdown_timer_seconds_hours_label)){
			$data .= ' data-date-ekit-second="'.esc_attr($ekit_countdown_timer_seconds_hours_label).'"';
		}
		if(isset($ekit_countdown_timer_due_time)){
			$data .= ' data-ekit-countdown="'.esc_attr($ekit_countdown_timer_due_time).'"';
        }

        $data .= ' data-finish-title="'.esc_attr($ekit_countdown_timer_title).'"';
        $data .= ' data-finish-content="'.esc_attr($ekit_countdown_timer_expiry_content).'"';

        switch ( $ekit_countdown_timer_style ) {
            case 'style1':
                ?><div class="elementskit-countdown-timer ekit-countdown text-center" <?php echo wp_kses($data, \ElementsKit_Lite\Utils::get_kses_array()); ?>></div><?php
                break;
            case 'style2':
                ?><div class="elementskit-countdown-timer-2 ekit-countdown text-center" <?php echo wp_kses($data, \ElementsKit_Lite\Utils::get_kses_array()); ?>></div><?php
                break;
            case 'style3':
                ?><div class="elementskit-flip-clock text-center" <?php echo wp_kses($data, \ElementsKit_Lite\Utils::get_kses_array()); ?>></div><?php
                break;
            case 'style4':
                ?><div class="elementskit-countdown-timer-3 ekit-countdown text-center" <?php echo wp_kses($data, \ElementsKit_Lite\Utils::get_kses_array()); ?>></div><?php
                break;
            case 'style5':
                ?><div class="elementskit-countdown-timer-3 ekit-countdown elementskit-version-box text-center align-items-end" <?php echo wp_kses($data, \ElementsKit_Lite\Utils::get_kses_array()); ?>></div>
                <?php
                break;
            case 'style6':
                ?><div class="elementskit-countdown-container text-center">
                    <div class="elementskit-countdown-timer-4 ekit-countdown" <?php echo wp_kses($data, \ElementsKit_Lite\Utils::get_kses_array()); ?>></div>
                </div><?php
                break;

        }
    }
}
