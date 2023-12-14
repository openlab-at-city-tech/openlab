<?php
namespace Elementor;

use Elementor\Modules\DynamicTags\Module as TagsModule;
use \Elementor\ElementsKit_Widget_Video_Handler as Handler;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Video extends Widget_Base {
	use \ElementsKit_Lite\Widgets\Widget_Notice;

	public $base;

	public function get_style_depends() {
		return ['wp-mediaelement'];
	}

	public function get_script_depends() {
		return ['wp-mediaelement'];
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
        return 'https://wpmet.com/doc/video/';
    }

	protected function register_controls() {

		$this->start_controls_section(
			'ekit_video_popup_content_section',
			[
				'label' => esc_html__( 'Video', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_video_popup_button_style',
			[
				'label' => esc_html__( 'Button Style', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'text'  => esc_html__( 'Text', 'elementskit-lite' ),
					'icon' => esc_html__( 'Icon', 'elementskit-lite' ),
					'both' => esc_html__( 'Both', 'elementskit-lite' ),
				],
			]
		);

		 $this->add_control(
            'ekit_video_popup_button_title',
            [
                'label' =>esc_html__( 'Button Title', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' =>esc_html__( 'Play Video', 'elementskit-lite' ),
				'default' =>esc_html__( 'Play Video', 'elementskit-lite' ),
				'condition' => [
					'ekit_video_popup_button_style' => ['text', 'both'],
				],
				'dynamic' => [
					'active' => true,
				],
            ]
		 );

		 $this->add_control(
            'ekit_video_popup_button_icons__switch',
            [
                'label' => esc_html__('Add icon? ', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'condition' => [
					'ekit_video_popup_button_style' 		=> ['icon', 'both'],
				]
            ]
		);

		 $this->add_control(
            'ekit_video_popup_button_icons',
            [
                'label' =>esc_html__( 'Button Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_video_popup_button_icon',
                'default' => [
                    'value' => 'icon icon-play',
                    'library' => 'ekiticons',
                ],
				'label_block' => true,
				'condition' => [
					'ekit_video_popup_button_style' 		=> ['icon', 'both'],
					'ekit_video_popup_button_icons__switch'	=> 'yes',
				]
            ]
		 );
		 $this->add_control(
			'ekit_video_popup_icon_align',
			[
				'label' =>esc_html__( 'Icon Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before' =>esc_html__( 'Before', 'elementskit-lite' ),
					'after' =>esc_html__( 'After', 'elementskit-lite' ),
				],
				'condition' => [
					'ekit_video_popup_button_style' => 'both',
					'ekit_video_popup_button_icons__switch'	=> 'yes',
				]
			]
		);

		 $this->add_control(
            'ekit_video_popup_video_glow',
            [
                'label' =>esc_html__( 'Active Glow', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
            ]
		 );


		 $this->add_control(
            'ekit_video_popup_video_type',
            [
                'label'     => esc_html__( 'Video Type', 'elementskit-lite' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'youtube',
                'options'   => [
					'youtube'=> esc_html__( 'Youtube', 'elementskit-lite' ),
					'vimeo'=> esc_html__( 'Vimeo', 'elementskit-lite' ),
					'self'=> esc_html__( 'Self Hosted', 'elementskit-lite' ),
                ]
            ]
        );

		$this->add_control(
			'ekit_video_popup_url',
			[
				'label' => esc_html__( 'URL to Embed', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'url',
				'placeholder' => esc_url( 'https://www.youtube.com/watch?v=VhBl3dHT5SY' ),
				'default' => esc_url( 'https://www.youtube.com/watch?v=VhBl3dHT5SY' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ekit_video_popup_video_type!' => 'self',
				],
			]
		);

		//video option
		$this->add_control(
			'ekit_video_popup_start_time',
			[
				'label' => esc_html__( 'Start Time', 'elementskit-lite' ),
				'type' => Controls_Manager::NUMBER,
				'dynamic' => [
					'active' => true,
				],
				'input_type' => 'number',
				'placeholder' =>  '',
				'default' => '0',
				'condition' => ['ekit_video_popup_video_type' => 'youtube' ]
			]
		);

		$this->add_control(
			'ekit_video_popup_end_time',
			[
				'label' => esc_html__( 'End Time', 'elementskit-lite' ),
				'type' => Controls_Manager::NUMBER,
				'dynamic' => [
					'active' => true,
				],
				'input_type' => 'number',
				'placeholder' => '',
				'default' => '',
				'condition' => ['ekit_video_popup_video_type' => 'youtube']
			]
		);

		// video Options
		$this->add_control(
			'ekit_video_player_options_heading',
			[
				'label' => esc_html__('video Options', 'elementskit-lite'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		// Self hosted
		$this->add_control(
			'ekit_video_self_url',
			[
				'label' => esc_html__('Custom Url', 'elementskit-lite'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit-lite'),
				'label_off' => esc_html__('No', 'elementskit-lite'),
				'condition' => [
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		$this->add_control(
			'ekit_video_self_external_url',
			[
				'label' => esc_html__('URL', 'elementskit-lite'),
				'label_block' => true,
				'placeholder' => esc_html__('Enter video URL', 'elementskit-lite'),
				'description' => esc_html__('Input a valid video url', 'elementskit-lite'),
				'type'  => Controls_Manager::TEXT,
				'default' => 'https://wpmet.com/plugin/elementskit/wp-content/uploads/2022/11/selfhosted_video.mp4',
				'condition' => [
					'ekit_video_self_url' => 'yes',
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		$this->add_control(
			'ekit_video_player_self_hosted',
			[
				'label' => esc_html__( 'Choose Video', 'elementskit-lite' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::MEDIA_CATEGORY,
					],
				],
				'media_type' => 'video',
				'condition' => [
					'ekit_video_self_url!' => 'yes',
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		$this->add_control(
			'ekit_video_popup_auto_play',
			[
				'label' => esc_html__( 'Auto Play', 'elementskit-lite' ),
				'description' => esc_html__( 'Unmuted videos will not auto play in some browsers.', 'elementskit-lite'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'default' => 'no',
				'return_value' => '1',
			]
		);

		$this->add_control(
			'ekit_video_popup_video_mute',
			[
				'label' => esc_html__( 'Mute', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => '1',
				'default' => 'no',
			]
		);

		$this->add_control(
			'ekit_video_popup_video_loop',
			[
				'label' => esc_html__( 'Loop', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => '1',
				'default' => 'no',
			]
		);

		$this->add_control(
			'ekit_video_popup_video_player_control',
			[
				'label' => esc_html__( 'Player Control', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => '1',
				'default' => 'no',
				'condition' => ['ekit_video_popup_video_type!' => 'self']
			]
		);

	   $this->add_control(
			'ekit_video_popup_video_intro_title',
			[
				'label' => esc_html__( 'Intro Title', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => '1',
				'default' => 'no',
				'condition' => ['ekit_video_popup_video_type' => 'vimeo']
			]
		);

		$this->add_control(
			'ekit_video_popup_video_intro_portrait',
			[
				'label' => esc_html__( 'Intro Portrait', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => '1',
				'default' => 'no',
				'condition' => ['ekit_video_popup_video_type' => 'vimeo']
			]
		);

        $this->add_control(
			'ekit_video_popup_video_intro_byline',
			[
				'label' => esc_html__( 'Intro Byline', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'return_value' => '1',
				'default' => 'no',
				'condition' => ['ekit_video_popup_video_type' => 'vimeo']
			]
		);
		//video option
		$this->add_control(
			'self_poster_image',
			[
				'label' => esc_html__( 'Poster Image', 'elementskit-lite' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'separator' => 'before',
				'condition' => [
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		// Control Options
		$this->add_control(
			'ekit_video_player_control_options_heading',
			[
				'label' => esc_html__('Control Options', 'elementskit-lite'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		$this->add_control(
			'ekit_video_player_playpause',
			[
				'label' => esc_html__('Play Pause', 'elementskit-lite'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit-lite'),
				'label_off' => esc_html__('No', 'elementskit-lite'),
				'condition' => [
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		$this->add_control(
			'ekit_video_player_progress',
			[
				'label' => esc_html__('Progress Bar', 'elementskit-lite'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit-lite'),
				'label_off' => esc_html__('No', 'elementskit-lite'),
				'condition' => [
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		$this->add_control(
			'ekit_video_player_current',
			[
				'label' => esc_html__('Current Time', 'elementskit-lite'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit-lite'),
				'label_off' => esc_html__('No', 'elementskit-lite'),
				'condition' => [
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		$this->add_control(
			'ekit_video_player_duration',
			[
				'label' => esc_html__('Total Duration', 'elementskit-lite'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit-lite'),
				'label_off' => esc_html__('No', 'elementskit-lite'),
				'condition' => [
					'ekit_video_player_current' => 'yes',
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		$this->add_control(
			'ekit_video_player_volume',
			[
				'label' => esc_html__('Volume Bar', 'elementskit-lite'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit-lite'),
				'label_off' => esc_html__('No', 'elementskit-lite'),
				'condition' => [
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		$this->add_control(
			'ekit_video_player_volume_slider_layout',
			[
				'label' => esc_html__('Volume Slider Layout', 'elementskit-lite'),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'vertical' => esc_html__('Vertical', 'elementskit-lite'),
					'horizontal' => esc_html__('Horizontal', 'elementskit-lite'),
				],
				'condition' => [
					'ekit_video_player_volume' => ['yes'],
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);

		$this->add_control(
			'ekit_video_player_start_volume',
			[
				'label' => esc_html__('Start Volume', 'elementskit-lite'),
				'description' => esc_html__('Initial volume when the player starts.', 'elementskit-lite'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 0.8,
				],
				'condition' => [
					'ekit_video_popup_video_type' => 'self',
				],
			]
		);		

        $this->end_controls_section();

        $this->start_controls_section(
			'ekit_video_popup_style_section',
			[
				'label' => esc_html__( 'Wrapper Style', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_video_popup_title_align', [
				'label'			 =>esc_html__( 'Alignment', 'elementskit-lite' ),
				'type'			 => Controls_Manager::CHOOSE,
				'options'		 => [

					'left'		 => [
						'title'	 =>esc_html__( 'Left', 'elementskit-lite' ),
						'icon'	 => 'eicon-text-align-left',
					],
					'center'	 => [
						'title'	 =>esc_html__( 'Center', 'elementskit-lite' ),
						'icon'	 => 'eicon-text-align-center',
					],
					'right'		 => [
						'title'	 =>esc_html__( 'Right', 'elementskit-lite' ),
						'icon'	 => 'eicon-text-align-right',
					],
					'justify'	 => [
						'title'	 =>esc_html__( 'Justified', 'elementskit-lite' ),
						'icon'	 => 'eicon-text-align-justify',
					],
				],
				'default'		 => 'center',
                'selectors' => [
                    '{{WRAPPER}} .video-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
            'ekit_video_wrap_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .video-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_video_wrap_border',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .video-content',
            ]
        );

        $this->add_control(
            'ekit_video_wrap_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .video-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_video_popup_section_style',
			[
				'label' =>esc_html__( 'Button Style', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_video_popup_text_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
            'ekit_video_popup_icon_size',
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
                    '{{WRAPPER}} .ekit-video-popup-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-video-popup-btn svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_video_popup_btn_typography',
				'label' =>esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-video-popup-btn',
			]
		);


		$this->add_control(
			'ekit_video_popup_btn_use_height_and_width',
			[
				'label' => esc_html__( 'Use height width', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_responsive_control(
			'ekit_video_popup_btn_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 60,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_video_popup_btn_use_height_and_width' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_video_popup_btn_height',
			[
				'label' => esc_html__( 'Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 60,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_video_popup_btn_use_height_and_width' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_video_popup_btn_line_height',
			[
				'label' => esc_html__( 'Line height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn' => 'line-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_video_popup_btn_use_height_and_width' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_video_popup_btn_glow_color',
			[
				'label' => esc_html__( 'Glow Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn.glow-btn:before' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-video-popup-btn.glow-btn:after' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-video-popup-btn.glow-btn > i:after' => 'color: {{VALUE}}',
				],
				'default' => '#255cff',
				'separator' => 'before',
				'condition' => [
					'ekit_video_popup_video_glow' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_video_popup_btn_glow_size',
			[
				'label' => esc_html__( 'Glow Size (px)', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn' => '--glow-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_video_popup_video_glow' => 'yes'
				]
			]
		);

		$this->start_controls_tabs( 'ekit_video_popup_button_style_tabs' );

		$this->start_controls_tab(
			'ekit_video_popup_button_normal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_video_popup_btn_text_color',
			[
				'label' =>esc_html__( 'Text Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-video-popup-btn svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_video_popup_btn_bg_color',
				'selector' => '{{WRAPPER}} .ekit-video-popup-btn',
            )
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_video_popup_btn_tab_button_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_video_popup_btn_hover_color',
			[
				'label' =>esc_html__( 'Text Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-video-popup-btn:hover svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

	    $this->add_group_control(
		    Group_Control_Background::get_type(),
		    array(
			    'name'     => 'ekit_video_popup_btn_bg_hover_color',
			    'selector' => '{{WRAPPER}} .ekit-video-popup-btn:hover',
		    )
	    );

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

        $this->start_controls_section(
			'ekit_video_popup_border_style',
			[
				'label' =>esc_html__( 'Border Style', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_video_popup_btn_border_style',
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
					'{{WRAPPER}} .ekit-video-popup-btn' => 'border-style: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ekit_video_popup_btn_border_dimensions',
			[
				'label' => esc_html_x( 'Width', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'ekit_video_popup__button_border_style' );
		$this->start_controls_tab(
			'ekit_video_popup__button_border_normal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_video_popup_btn_border_color',
			[
				'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_video_popup_btn_tab_button_border_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
			]
		);
		$this->add_control(
			'ekit_video_popup_btn_hover_border_color',
			[
				'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn:hover' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'ekit_video_popup_btn_border_radius',
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
					'{{WRAPPER}} .ekit-video-popup-btn, {{WRAPPER}} .ekit-video-popup-btn:before' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

        $this->start_controls_section(
			'ekit_video_popup_box_shadow_style',
			[
				'label' =>esc_html__( 'Shadow Style', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_video_popup_btn_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-video-popup-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'ekit_video_popup_btn_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-video-popup-btn',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_video_popup_icon_style',
			[
				'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_video_popup_button_icons__switch'	=> 'yes',
					'ekit_video_popup_button_style' => ['both']
				]
			]
		);

		$this->add_responsive_control(
			'ekit_video_popup_icon_padding_right',
			[
				'label' => esc_html__( 'Padding Right', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
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
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn > i' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_video_popup_button_style' => 'both',
					'ekit_video_popup_icon_align' => 'before'
				]
			]
		);

		$this->add_responsive_control(
			'ekit_video_popup_icon_padding_left',
			[
				'label' => esc_html__( 'Padding Left', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
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
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-video-popup-btn > i' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_video_popup_button_style' => 'both',
					'ekit_video_popup_icon_align' => 'after'
				]
			]
		);

		$this->end_controls_section();

		$this->insert_pro_message();
	}

	/**
	 * Video Icon
	 */
	private function video_icon() {
		$settings = $this->get_settings_for_display();

		// new icon
		$migrated = isset( $settings['__fa4_migrated']['ekit_video_popup_button_icons'] );
		// Check if its a new widget without previously selected icon using the old Icon control
		$is_new = empty( $settings['ekit_video_popup_button_icon'] );
		if ( $is_new || $migrated ) {
			// new icon
			Icons_Manager::render_icon( $settings['ekit_video_popup_button_icons'], [ 'aria-hidden' => 'true' ] );
		} else {
			?>
			<i class="<?php echo esc_attr($settings['ekit_video_popup_button_icons']); ?>" aria-hidden="true"></i>
			<?php
		}
	}

	protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
	}

    protected function render_raw( ) {
		$settings = $this->get_settings_for_display();
		extract($settings);

		$player_control = isset( $ekit_video_popup_video_player_control )  && $ekit_video_popup_video_player_control == '1'  ? 1 : 0;

		// Fallback Video URL for YouTube
		if ( empty($ekit_video_popup_url) ) {
			$ekit_video_popup_url = 'https://www.youtube.com/watch?v=VhBl3dHT5SY';
		}

		$ekit_video_popup_url = Embed::get_embed_url( $ekit_video_popup_url ); // Support for short links like: https://youtu.be/VhBl3dHT5SY
		$video_properties = Embed::get_video_properties( $ekit_video_popup_url ); // Get only the video id.

		$video_id = '';
		if( !empty($video_properties['video_id']) ) {
			$video_id = $video_properties['video_id'];
		}
		
		$is_autoplay = (int) $ekit_video_popup_auto_play;
		$is_muted = (int) $ekit_video_popup_video_mute;

		if($ekit_video_popup_video_type == "vimeo"){
			$url = explode('#', $ekit_video_popup_url, 2);
			$ekit_video_popup_url = $url[0];
			$ekit_video_popup_url = $ekit_video_popup_url."?playlist={$video_id}&muted={$is_muted}&autoplay={$is_autoplay}&loop={$ekit_video_popup_video_loop}&controls={$player_control}&start={$ekit_video_popup_start_time}&end={$ekit_video_popup_end_time}";
		}
		else{
			$ekit_video_popup_url = $ekit_video_popup_url."?playlist={$video_id}&mute={$is_muted}&autoplay={$is_autoplay}&loop={$ekit_video_popup_video_loop}&controls={$player_control}&start={$ekit_video_popup_start_time}&end={$ekit_video_popup_end_time}";
		};

		// set player features playpause, current, progress, duration, volume
		$features = [];
		($ekit_video_player_playpause === 'yes') && array_push($features, 'playpause');
		($ekit_video_player_current === 'yes') && array_push($features, 'current');
		($ekit_video_player_progress === 'yes') && array_push($features, 'progress');
		($ekit_video_player_duration === 'yes') && array_push($features, 'duration');
		($ekit_video_player_volume === 'yes') && array_push($features, 'volume');


		// set settings data attributes
		$video_settings['videoVolume'] = (!empty($ekit_video_player_volume_slider_layout)) ? $ekit_video_player_volume_slider_layout: 'horizontal';
		$video_settings['startVolume'] = (!empty($ekit_video_player_start_volume['size'])) ? $ekit_video_player_start_volume['size']: 0.8;
		$video_settings['videoType'] = (!empty($ekit_video_popup_video_type === 'vimeo' || $ekit_video_popup_video_type === 'youtube')) ? 'iframe': 'inline';
		$video_settings['videoClass'] = (!empty($ekit_video_popup_video_type === 'vimeo' || $ekit_video_popup_video_type === 'youtube')) ? 'mfp-fade': 'ekit_self_video_wrap_content';
		$poster_image =  !empty($self_poster_image['url']) ? $self_poster_image['url'] : '';

		//generate id
		$generate_id = "test-popup-link".$this->get_id();

		// registering video player default attributes.
		$this->add_render_attribute(
			'player',
			[
				'preload' => 'none',
				'controls' => '',
				'poster' => $poster_image,
			]
		);

		// video options
		if (!empty($ekit_video_popup_auto_play) && $ekit_video_popup_auto_play === '1') {
			$this->add_render_attribute('player', 'autoplay', '');
		}

		if (!empty($ekit_video_popup_video_loop) && $ekit_video_popup_video_loop === '1') {
			$this->add_render_attribute('player', 'loop', '');
		}

		if (!empty($ekit_video_popup_video_mute) && $ekit_video_popup_video_mute === '1') {
			$this->add_render_attribute('player', 'muted', '');
		}
		?>
		<div class="video-content" data-video-player="<?php echo esc_attr(wp_json_encode($features)); ?>" data-video-setting="<?php echo esc_attr(wp_json_encode($video_settings)); ?>">
			<?php if($ekit_video_popup_video_type === 'vimeo' || $ekit_video_popup_video_type === 'youtube') :
				include Handler::get_dir() . 'parts/video-button.php';  ?>
			<?php else : 
				include Handler::get_dir() . 'parts/video-button.php'; ?>	
				<div id="<?php echo esc_attr($generate_id); ?>" class="mfp-hide ekit_self_video_wrap">
					<video class="video_class" <?php $this->print_render_attribute_string('player'); ?> >
						<source type="video/mp4" src="<?php echo esc_url($ekit_video_self_url == 'yes' ? $ekit_video_self_external_url : $ekit_video_player_self_hosted['url'] ); ?>" />
					</video>
				</div>
			<?php endif;?>
		</div>
		<?php
	}
}
