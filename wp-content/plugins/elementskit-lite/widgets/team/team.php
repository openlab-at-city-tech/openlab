<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Team_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Team extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;
    
    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->add_script_depends('magnific-popup');
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
        return 'https://wpmet.com/doc/team-2/';
    }

    protected function register_controls() {

        // Team Content
        $this->start_controls_section(
            'ekit_team_content', [
                'label' => esc_html__( 'Team Member Content', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_team_style',
            [
                'label' =>esc_html__( 'Style', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => esc_html__( 'Default', 'elementskit-lite' ),
                    'overlay' => esc_html__( 'Overlay', 'elementskit-lite' ),
                    'centered_style' => esc_html__( 'Centered ', 'elementskit-lite' ),
                    'hover_info' => esc_html__( 'Hover on social', 'elementskit-lite' ),
                    'overlay_details' => esc_html__( 'Overlay with details', 'elementskit-lite' ),
                    'centered_style_details' => esc_html__( 'Centered with details ', 'elementskit-lite' ),
                    'long_height_hover' => esc_html__( 'Long height with hover ', 'elementskit-lite' ),
                    'long_height_details' => esc_html__( 'Long height with details ', 'elementskit-lite' ),
                    'long_height_details_hover' => esc_html__( 'Long height with details & hover', 'elementskit-lite' ),
                    'overlay_circle' => esc_html__( 'Overlay with circle shape', 'elementskit-lite' ),
                    'overlay_circle_hover' => esc_html__( 'Overlay with circle shape & hover', 'elementskit-lite' ),
                ],
            ]
        );

        $this->add_control(
            'ekit_team_image',
            [
                'label' => esc_html__( 'Choose Member Image', 'elementskit-lite' ),
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
                'name' => 'ekit_team_thumbnail',
                'default' => 'large',
            ]
        );

        $this->add_control(
            'ekit_team_name',
            [
                'label' => esc_html__( 'Member Name', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'Jane Doe', 'elementskit-lite' ),
                'placeholder' => esc_html__( 'Member Name', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_team_position',
            [
                'label' => esc_html__( 'Member Position', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'Designer', 'elementskit-lite' ),
                'placeholder' => esc_html__( 'Member Position', 'elementskit-lite' ),

            ]
        );

        // Show Icon
        $this->add_control(
			'ekit_team_toggle_icon',
			[
				'label' => esc_html__( 'Show Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
                'condition' => [
                    'ekit_team_style' => 'default',
                ],
			]
        );
        $this->add_control(
            'ekit_team_top_icons',
            [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_team_top_icon',
                'default' => [
                    'value' => 'icon icon-team1',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_team_style' => 'default',
                    'ekit_team_toggle_icon' => 'yes',
                ],
            ]
        );
        
        // Show Description
        $this->add_control(
			'ekit_team_show_short_description',
			[
				'label' => esc_html__( 'Show Description', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
        $this->add_control(
            'ekit_team_short_description',
            [
                'label' => esc_html__( 'About Member', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'A small river named Duden flows by their place and supplies it with the necessary', 'elementskit-lite' ),
                'placeholder' => esc_html__( 'About Member', 'elementskit-lite' ),
                'condition' => [
                    'ekit_team_show_short_description' => 'yes'
                ],

            ]
        );

        $this->end_controls_section();


        // Team Social section

	   $this->start_controls_section(
            'ekit_team_section_social', [
                'label' => esc_html__( 'Social  Profiles', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_team_socail_enable',
            [
                'label' => esc_html__( 'Display Social Profiles?', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
                'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $social = new Repeater();

        $social->add_control(
            'ekit_team_icons',
            [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
                'label_block' => true,
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_team_icon',
                'default' => [
                    'value' => 'icon icon-facebook',
                    'library' => 'ekiticons',
                ],
            ]
        );

        $social->add_control(
            'ekit_team_label',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => 'Facebook',
            ]
        );

        $social->add_control(
            'ekit_team_link',
            [
                'label' => esc_html__( 'Link', 'elementskit-lite' ),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => 'https://facebook.com',
                ],
            ]
        );
        // start tab for content
        $social->start_controls_tabs(
            'ekit_team_socialmedia_tabs'
        );

        // start normal tab
        $social->start_controls_tab(
            'ekit_team_socialmedia_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        // set social icon color
        $social->add_control(
            'ekit_team_socialmedia_icon_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} > a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} > a svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};'
                ],
            ]
        );

        // set social icon background color
        $social->add_control(
            'ekit_team_socialmedia_icon_bg_color',
            [
                'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#a1a1a1',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} > a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $social->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_socialmedia_border',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a',
            ]
        );

        $social->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_team_socialmedia_icon_normal_text_shadow',
                'label' => esc_html__( 'Text Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a',
            ]
        );

        $social->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'       => 'ekit_team_socialmedai_list_box_shadow',
                'selector'   => '{{WRAPPER}} {{CURRENT_ITEM}} > a',
            ]
        );

        $social->end_controls_tab();
        // end normal tab

        //start hover tab
        $social->start_controls_tab(
            'ekit_team_socialmedia_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        // set social icon color
        $social->add_control(
            'ekit_team_socialmedia_icon_hover_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover svg path'   => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        // set social icon background color
        $social->add_control(
            'ekit_team_socialmedia_icon_hover_bg_color',
            [
                'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#3b5998',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $social->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_socialmedia_border_hover',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover',
            ]
        );

        $social->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_team_socialmedia_icon_hover_text_shadow',
                'label' => esc_html__( 'Text Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover',
            ]
        );

        $social->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'       => 'ekit_team_socialmedai_list_box_shadow_hover',
                'selector'   => '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover',
            ]
        );

        $social->end_controls_tab();
        //end hover tab

        $social->end_controls_tabs();

        $this->add_control(
            'ekit_team_social_icons',
            [
                'label' => esc_html__('Add Icon', 'elementskit-lite'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $social->get_controls(),
                'default' => [
                    [
                        'ekit_team_label' => esc_html__('Facebook', 'elementskit-lite'),
                        'ekit_team_icons' => [
							'value'     => 'icon icon-facebook',
							'library'   => 'ekiticons',
                        ],
                        'ekit_team_socialmedia_icon_hover_bg_color' => '#3b5998',
                    ],
                    [
                        'ekit_team_label' => esc_html__('Twitter', 'elementskit-lite'),
                        'ekit_team_icons' => [
							'value'     => 'icon icon-twitter',
							'library'   => 'ekiticons',
						],
                        'ekit_team_socialmedia_icon_hover_bg_color' => '#1da1f2',
                    ],
                    [
                        'ekit_team_label' => esc_html__('Pinterest', 'elementskit-lite'),
                        'ekit_team_icons' => [
							'value'     => 'icon icon-pinterest',
							'library'   => 'ekiticons',
						],
                        'ekit_team_socialmedia_icon_hover_bg_color' => '#e60023',
                    ],
                ],
                'title_field' => '{{{ ekit_team_label }}}',
                'condition' => [
                    'ekit_team_socail_enable' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
			'ekit_team_popup_details',
			[
				'label' => esc_html__( 'Pop Up Details', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_CONTENT,
			]
        );

        $this->add_control(
			'ekit_team_chose_popup',
			[
				'label' => esc_html__( 'Show Popup', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

        $this->add_control(
            'ekit_team_description',
            [
                'label' => esc_html__( 'About Member', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'A small river named Duden flows by their place and supplies it with the necessary', 'elementskit-lite' ),
                'placeholder' => esc_html__( 'About Member', 'elementskit-lite' ),
                'condition' => [
                    'ekit_team_chose_popup' => 'yes'
                ],

            ]
        );
        $this->add_control(
            'ekit_team_phone',
            [
                'label' => esc_html__( 'Phone', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '+1 (859) 254-6589',
                'placeholder' => esc_html__( 'Phone Number', 'elementskit-lite' ),
                'condition' => [
                    'ekit_team_chose_popup' => 'yes'
                ],

            ]
        );
        $this->add_control(
            'ekit_team_email',
            [
                'label' => esc_html__( 'Email', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => 'info@example.com',
                'placeholder' => esc_html__( 'Email Address', 'elementskit-lite' ),
                'condition' => [
                    'ekit_team_chose_popup' => 'yes'
                ],

            ]
        );

        // Close icon change option
        $this->add_control(
            'ekit_team_close_icon_changes',
            [
                'label' => esc_html__( 'Close Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_team_close_icon_change',
                'default' => [
                    'value' => 'fas fa-times',
                    'library' => 'fa-solid',
                ],
                'label_block' => true,
                'condition' => [
                    'ekit_team_chose_popup' => 'yes'
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'ekit_team_close_icon_alignment',
            [
                'label' => esc_html__( 'Close Icon Alignment', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => '{{VALUE}}: 10px;',
                ],
                'default' => 'right',
                'condition' => [
                    'ekit_team_chose_popup' => 'yes'
                ],
            ]
        );

		$this->end_controls_section();

        // start style section here

        // Team content section style start
        $this->start_controls_section(
            'ekit_team_content_style', [
                'label' => esc_html__( 'Content', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );



		$this->start_controls_tabs(
            'ekit_team_background_tabs'
        );
		// start normal tab
        $this->start_controls_tab(
            'ekit_team_content_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_team_background_content_normal',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .profile-card, {{WRAPPER}} .profile-image-card',
			]
		);
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'ekit_team_content_box_shadow',
                'selector'  => '{{WRAPPER}} .profile-card, {{WRAPPER}} .profile-image-card',
            ]
        );
		$this->end_controls_tab();

		$this->start_controls_tab(
            'ekit_team_content_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );


        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_team_background_content_hover',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .profile-card:hover, {{WRAPPER}} .profile-image-card:hover, {{WRAPPER}} .profile-card::before, {{WRAPPER}} .profile-image-card::before, {{WRAPPER}} div .profile-card .profile-body::before, {{WRAPPER}} .image-card-v3 .profile-image-card:after',
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'ekit_team_content_box_shadow_hover_group',
                'selector'  => '{{WRAPPER}} .profile-card:hover, {{WRAPPER}} .profile-image-card:hover',
            ]
        );
        
            $this->add_control(
                'team_hover_animation',
                [
                    'label'         => esc_html__( 'Hover Animation', 'elementskit-lite' ),
                    'type'          => Controls_Manager::HOVER_ANIMATION,
                ]
            );
        
            $this->add_responsive_control(
                'overlay_height',
                [
                    'label'         => esc_html__('Overlay Height', 'elementskit-lite'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['%', 'px'],
                    'range'         => [
                        '%'     => [
                            'min'   => 0,
                            'max'   => 100
                        ],
                        'px'    => [
                            'min'   => 0,
                            'max'   => 500,
                            'step'  => 5
                        ]
                    ],
                    'default'       => [
                        'unit'  => '%',
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .ekit-team-style-long_height_hover:after'  => 'height: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'ekit_team_style'   => 'long_height_hover',
                    ],
                ]
            );

		$this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->add_control(
            'content_tabs_after',
            [
                'type'  => Controls_Manager::DIVIDER,
            ]
        );

		// contentmax height
        $this->add_responsive_control(
			'ekit_team_content_max_weight',
			[
				'label' => esc_html__( 'Max Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 380,
				],
				'selectors' => [
					'{{WRAPPER}} .profile-square-v .profile-card' => 'max-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_style' => 'hover_info'
                ]
			]
		);

        // Text aliment

        $this->add_control(
            'ekit_team_content_text_align',
            [
                'label' => esc_html__( 'Alignment', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'text-left' => [
                        'title' => esc_html__( 'Left', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'text-center' => [
                        'title' => esc_html__( 'Center', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'text-right' => [
                        'title' => esc_html__( 'Right', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'text-center',
                'toggle' => true,
            ]
        );

        $this->add_responsive_control(
			'ekit_team_content_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .profile-card, {{WRAPPER}} .profile-image-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'ekit_team_content_inner_padding',
            [
                'label' =>esc_html__( 'Content Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .profile-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-wid-con .profile-square-v .profile-card .profile-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_content_border_color_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .profile-card, {{WRAPPER}} .profile-image-card',
            ]
        );

        $this->add_responsive_control(
			'ekit_team_content_border_radius',
			[
				'label' =>esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '' ,
					'left' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .profile-card, {{WRAPPER}} .profile-image-card' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


        $this->add_control(
            'ekit_team_content_overly_color_heading',
            [
                'label' => esc_html__( 'Hover Overy Color', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'ekit_team_style' => 'overlay_details'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_content_overly_color',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'gradient'],
                'selector' => '{{WRAPPER}} .image-card-v2 .profile-image-card::before',
                'condition' => [
                       'ekit_team_style' => 'overlay_details'
                ]
            ]
        );

        $this->add_control(
            'ekit_team_remove_gutters',
            [
                'label' => esc_html__( 'Remove Gutter?', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'Yes', 'elementskit-lite' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );



        $this->end_controls_section();
        // team content section style end

        // Image Styles section
        $this->start_controls_section(
            'ekit_team_image_style', [
                'label' => esc_html__( 'Image', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
            'ekit_team_image_weight',
            [
                'label' => esc_html__( 'Image Size', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em' ],
                'range'  => [
                    'px' => [
                        'min'   => 10,
                        'max'   => 300,
                    ],
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .profile-square-v.square-v4 .profile-card .profile-header' => 'padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .profile-header > img, {{WRAPPER}} .profile-image-card img, {{WRAPPER}} .profile-image-card, {{WRAPPER}} .profile-header ' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};'
				],
				'default' => [
					'unit' => '%'
				]
            ]
        );

        $this->add_responsive_control(
            'ekit_team_image_height',
            [
                'label'         => esc_html__('Height', 'elementskit-lite'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'em'],
                'range'  => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 500,
                    ],
                ],
                'condition' => [
                    'team_style!' => 'overlay',
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-card .profile-header' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_image_height_margin_bottom',
            [
                'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .profile-card .profile-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );



        $this->add_responsive_control(
            'ekit_team_image_width',
            [
                'label'         => esc_html__('Width', 'elementskit-lite'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'em', '%'],
                'range'  => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 500,
                    ],
                ],
                'condition' => [
                    'team_style!' => 'overlay',
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-card .profile-header' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'ekit_team_image_shadow',
                'selector'  => '{{WRAPPER}} .profile-card .profile-header',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'      => 'modal_img_shadow',
                'label'     => esc_html__('Box Shadow (Popup)', 'elementskit-lite'),
                'selector'  => '{{WRAPPER}} .ekit-team-modal-img > img',
                'condition' => [
                    'ekit_team_chose_popup' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_image_border',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .profile-card .profile-header',
            ]
        );

        $this->add_responsive_control(
            'ekit_team_image_radius',
            [
                'label' => esc_html__( 'Border radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-img.profile-header > img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default' => [
					'top' => '50',
					'right' => '50',
					'left' => '50',
					'bottom' => '50',
					'unit' => '%',
				]
            ]
        );

        $this->add_responsive_control(
            'ekit_team_image_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'condition' => [
                    'team_style!' => 'overlay',
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-card .profile-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_image_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .profile-card .profile-header',
            ]
        );

		$this->add_control(
			'ekit_team_default_img_overlay_h',
			[
				'label' => esc_html__( 'Overlay', 'elementskit-lite' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
                'condition' => [
                    'ekit_team_style' => 'default',
                ],
			]
		);
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_default_img_overlay',
                'label' => esc_html__( 'Overlay', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .profile-header:before',
                'condition' => [
                    'ekit_team_style' => 'default',
                ],
            ]
        );

        $this->end_controls_section();


        // Icon Styles
        $this->start_controls_section(
            'ekit_team_top_icon_style',
            [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_team_style' => 'default',
                    'ekit_team_toggle_icon' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_top_icon_align',
            [
                'label' => esc_html__( 'Alignment', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => esc_html__( 'Left', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Right', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'end' => [
                        'title' => esc_html__( 'Right', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'toggle' => true,
            ]
        );

        $this->add_responsive_control(
			'ekit_team_top_icon_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->add_responsive_control(
			'ekit_team_top_icon_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'ekit_team_top_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'   => [
                    'top'   => '50',
                    'left'  => '50',
                    'right' => '50',
                    'bottom'=> '50',
                    'unit' => '%'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_team_top_icon_shadow',
                'selector' => '{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg',
            ]
        );
        
		$this->add_responsive_control(
            'ekit_team_top_icon_fsize',
            [
                'label' => esc_html__( 'Font Size', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'default' => [
                    'size' => 22,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-icon > i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .profile-icon > svg'   => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_team_top_icon_hw',
			[
                'label' => esc_html__( 'Use Height Width', 'elementskit-lite' ),
                'description'   => esc_html__('For svg icon, We don\'t need this. We will use font size and padding for adjusting size.', 'elementskit-lite'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
        
		$this->add_responsive_control(
            'ekit_team_top_icon_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => 60,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-icon > i' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_top_icon_hw' => 'yes'
                ],
            ]
        );
        
		$this->add_responsive_control(
            'ekit_team_top_icon_height',
            [
                'label' => esc_html__( 'Height', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => 60,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-icon > i' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_top_icon_hw' => 'yes'
                ],
            ]
        );
        
		$this->add_responsive_control(
            'ekit_team_top_icon_lheight',
            [
                'label' => esc_html__( 'Line Height', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => 60,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .profile-icon > i' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_top_icon_hw' => 'yes'
                ],
            ]
        );

        $this->start_controls_tabs( 'top_icon_colors' );
            $this->start_controls_tab(
                'ekit_team_top_icon_colors_normal',
                [
                    'label' => esc_html__( 'Normal', 'elementskit-lite' ),
                ]
            );
            $this->add_control(
                'ekit_team_top_icon_n_color',
                [
                    'label' => esc_html__( 'Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#fff',
                    'selectors' => [
                        '{{WRAPPER}} .profile-icon > i' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .profile-icon > svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                    ],
                ]
            );
            $this->add_control(
                'ekit_team_top_icon_n_bgcolor',
                [
                    'label' => esc_html__( 'Background Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#fc0467',
                    'selectors' => [
                        '{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'ekit_team_top_icon_n_border',
                    'label' => esc_html__( 'Border', 'elementskit-lite' ),
                    'selector' => '{{WRAPPER}} .profile-icon > i, {{WRAPPER}} .profile-icon > svg',
                ]
            );
            $this->end_controls_tab();
            
            $this->start_controls_tab(
                'ekit_team_top_icon_colors_hover',
                [
                    'label' => esc_html__( 'Hover', 'elementskit-lite' ),
                ]
            );
                $this->add_control(
                    'ekit_team_top_icon_h_color',
                    [
                        'label' => esc_html__( 'Color', 'elementskit-lite' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .profile-icon > i:hover' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .profile-icon > svg:hover path'    => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                        ],
                    ]
                );
                $this->add_control(
                    'ekit_team_top_icon_h_bgcolor',
                    [
                        'label' => esc_html__( 'Background Color', 'elementskit-lite' ),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .profile-icon > i:hover, {{WRAPPER}} .profile-icon > svg:hover' => 'background-color: {{VALUE}};',
                        ],
                    ]
                );
                $this->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'ekit_team_top_icon_h_border',
                        'label' => esc_html__( 'Border', 'elementskit-lite' ),
                        'selector' => '{{WRAPPER}} .profile-icon > i:hover, {{WRAPPER}} .profile-icon > svg:hover',
                    ]
                );
            $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();


        // Name Styles
        $this->start_controls_section(
            'ekit_team_name_style', [
                'label' => esc_html__( 'Name', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_name_typography',
                'selector'   => '{{WRAPPER}} .profile-body .profile-title',
            ]
        );

        $this->start_controls_tabs(
            'ekit_team_name_tabs'
        );

        $this->start_controls_tab(
            'ekit_team_name_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_team_name_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .profile-body .profile-title' => 'color: {{VALUE}};'
                ],
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_team_name_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_team_name_hover_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .profile-body:hover .profile-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .profile-card:hover .profile-title' => 'color: {{VALUE}} !important',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'ekit_team_name_margin',
            [
                'label'         => esc_html__('Margin Bottom', 'elementskit-lite'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'em'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .profile-body .profile-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        // Position Styles
        $this->start_controls_section(
            'ekit_team_position_style', [
                'label' => esc_html__( 'Position', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_position_typography',
                'selector'   => '{{WRAPPER}} .profile-body .profile-designation',
            ]
        );

        $this->start_controls_tabs(
            'ekit_team_position_tabs'
        );

        $this->start_controls_tab(
            'ekit_team_position_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_team_position_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .profile-body .profile-designation' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_team_position_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_team_position_hover_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .profile-card:hover .profile-body .profile-designation,
                    {{WRAPPER}} .profile-body .profile-designation:hover' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(), [
                'name'       => 'ekit_team_position_hover_shadow',
                'selector'   => '{{WRAPPER}} .profile-card:hover .profile-body .profile-designation,
                    {{WRAPPER}} .profile-body .profile-designation:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'ekit_team_position_margin_bottom',
            [
                'label' => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],

                'selectors' => [
                    '{{WRAPPER}} .profile-body .profile-designation' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        // Position Styles
        $this->start_controls_section(
            'ekit_team_text_content_style_tab', [
                'label' => esc_html__( 'Description', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_text_content_typography',
                'selector'   => '{{WRAPPER}} .profile-body .profile-content',
            ]
        );

        $this->start_controls_tabs(
            'ekit_team_text_content_tabs'
        );

        $this->start_controls_tab(
            'ekit_team_text_content_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_team_text_content_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .profile-body .profile-content' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_team_text_content_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_team_text_content_hover_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .profile-card:hover .profile-body .profile-content' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .profile-image-card:hover .profile-body .profile-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
			'ekit_team_text_content_margin_bottom',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .profile-body .profile-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
			]
		);


        $this->end_controls_section();


        // Social Styles
        $this->start_controls_section(
            'ekit_team_social_style', [
                'label' => esc_html__( 'Social  Profiles', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_team_socail_enable' => 'yes'
                ]
            ]
        );

        // Alignment
        $this->add_responsive_control(
            'ekit_socialmedai_list_item_align',
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
				'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li > a' => 'text-align: {{VALUE}};',
                ],
            ]
        );

		// Display design
		 $this->add_responsive_control(
            'ekit_socialmedai_list_display',
            [
                'label' => esc_html__( 'Display', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'inline-block',
                'options' => [
                    'inline-block' => esc_html__( 'Inline Block', 'elementskit-lite' ),
                    'block' => esc_html__( 'Block', 'elementskit-lite' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li' => 'display: {{VALUE}};',
                ],
            ]
        );

		// text decoration
		 $this->add_responsive_control(
            'ekit_socialmedai_list_decoration_box',
            [
                'label' => esc_html__( 'Decoration', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
				'default' => 'none',
                'options' => [
                    'none' => esc_html__( 'None', 'elementskit-lite' ),
                    'underline' => esc_html__( 'Underline', 'elementskit-lite' ),
                    'overline' => esc_html__( 'Overline', 'elementskit-lite' ),
                    'line-through' => esc_html__( 'Line Through', 'elementskit-lite' ),

                ],
                'selectors' => ['{{WRAPPER}} .ekit-team-social-list > li > a' => 'text-decoration: {{VALUE}};'],
            ]
        );


		// border radius
		 $this->add_responsive_control(
            'ekit_socialmedai_list_border_radius',
            [
                'label' => esc_html__( 'Border radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '50',
					'right' => '50',
					'bottom' => '50' ,
					'left' => '50',
					'unit' => '%',
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		// Padding style

		 $this->add_responsive_control(
            'ekit_socialmedai_list_padding',
            [
                'label'         => esc_html__('Padding', 'elementskit-lite'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		// margin style

		$this->add_responsive_control(
            'ekit_socialmedai_list_margin',
            [
                'label'         => esc_html__('Margin', 'elementskit-lite'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-social-list > li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_socialmedai_list_icon_size',
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
                    '{{WRAPPER}} .ekit-team-social-list > li > a i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-team-social-list > li > a svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_socialmedai_list_typography',
				'label' => esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-team-social-list > li > a',
			]
		);

        $this->add_control(
			'ekit_socialmedai_list_style_use_height_and_width',
			[
                'label' => esc_html__( 'Use Height Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedai_list_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
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
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-social-list > li > a' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedai_list_style_use_height_and_width' => 'yes'
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedai_list_height',
			[
				'label' => esc_html__( 'Height', 'elementskit-lite' ),
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
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-social-list > li > a' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedai_list_style_use_height_and_width' => 'yes'
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedai_list_line_height',
			[
				'label' => esc_html__( 'Line Height', 'elementskit-lite' ),
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
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-team-social-list > li > a' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedai_list_style_use_height_and_width' => 'yes'
                ]
			]
		);

        $this->end_controls_section();


        // Overlay Styles
        $this->start_controls_section(
            'ekit_team_overlay_style', [
                'label' => esc_html__( 'Overlay', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'team_style' => 'overlay',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_background_overlay',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'gradient' ],
                'selector' => '{{WRAPPER}} .profile-image-card:before',
            ]
        );

        $this->end_controls_section();


        // Modal Styles start here
        $this->start_controls_section(
            'ekit_team_modal_style', [
                'label' => esc_html__( 'Modal Controls', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_team_chose_popup' => 'yes'
                ]
            ]
        );


        $this->add_control(
			'ekit_team_modal_heading',
			[
				'label' => esc_html__( 'Modal', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_team_modal_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-team-popup .modal-content',
            ]
        );

        $this->add_control(
			'ekit_team_modal_name_heading',
			[
				'label' => esc_html__( 'Name', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
            'ekit_team_modal_name_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .ekit-team-modal-title' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_modal_name_typography',
                'selector'   => '{{WRAPPER}} .ekit-team-modal-title',
            ]
        );

        $this->add_responsive_control(
            'ekit_team_modal_name_margin_bottom',
            [
                'label' => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_team_modal_position_heading',
			[
				'label' => esc_html__( 'Position', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
            'ekit_team_modal_position_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .ekit-team-modal-position' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_modal_position_typography',
                'selector'   => '{{WRAPPER}} .ekit-team-modal-position',
            ]
        );

        $this->add_responsive_control(
            'ekit_team_modal_position_margin_bottom',
            [
                'label' => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-position' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

            // Modal Description
            $this->add_control(
                'modal_desc',
                [
                    'label'     => esc_html__('Description', 'elementskit-lite'),
                    'type'      => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            // Modal Description - Color
            $this->add_control(
                'modal_desc_color',
                [
                    'label'     => esc_html__('Color', 'elementskit-lite'),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-team-modal-content'  => 'color: {{VALUE}};',
                    ]
                ]
            );

            // Modal Description - Typography
            $this->add_group_control(
                Group_Control_Typography::get_type(), [
                    'name'       => 'modal_desc_font',
                    'selector'   => '{{WRAPPER}} .ekit-team-modal-content',
                ]
            );
    
            // Modal Description - Margin Bottom
            $this->add_responsive_control(
                'modal_desc_margin_bottom',
                [
                    'label'         => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => [ 'px', '%' ],
                    'range'         => [
                        'px' => [
                            'min'   => 0,
                            'max'   => 150,
                            'step'  => 1,
                        ],
                        '%'  => [
                            'min'   => 0,
                            'max'   => 100,
                        ],
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .ekit-team-modal-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

        $this->add_control(
			'more_options',
			[
				'label' => esc_html__( 'Phone and Email', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);


        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_team_info_typography',
                'selector'   => '{{WRAPPER}} .ekit-team-modal-list',
            ]
        );

        $this->add_control(
            'ekit_team_info_color',
            [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .ekit-team-modal-list' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_control(
            'ekit_team_info_hover_color',
            [
                'label'      => esc_html__( 'Color Hover', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .ekit-team-modal-list a:hover' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ekit_team_close_icon',
            [
                'label' => esc_html__( 'Close Icon', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_team_chose_popup' => 'yes'
                ]
            ]
        );

        $this->start_controls_tabs( 'ekit_icon_box_icon_colors' );

        $this->start_controls_tab(
            'ekit_team_icon_colors_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_team_icon_primary_color',
            [
                'label' => esc_html__( 'Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#656565',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-team-modal-close svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_team_icon_secondary_color_normal',
            [
                'label' => esc_html__( 'Icon BG Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_border',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .ekit-team-modal-close',
            ]
        );



        $this->add_responsive_control(
            'ekit_team_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_icon_icon_box_shadow_normal_group',
                'selector' => '{{WRAPPER}} .ekit-team-modal-close',
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_team_icon_colors_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_team_hover_primary_color',
            [
                'label' => esc_html__( 'Icon Color (Hover)', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-team-modal-close:hover svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_team_hover_background_color',
            [
                'label' => esc_html__( 'Icon BG Color (Hover)', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_team_border_icon_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .ekit-team-modal-close:hover',
            ]
        );

        $this->add_responsive_control(
            'ekit_icon_box_icons_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_team_shadow_group',
                'selector' => '{{WRAPPER}} .ekit-team-modal-close:hover',
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->add_responsive_control(
            'ekit_team_close_icon_size',
            [
                'label' => esc_html__( 'Font Size', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-team-modal-close svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-team-modal-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_enable_height_width',
            [
                'label' => esc_html__( 'Use Height Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_width',
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
                    '{{WRAPPER}} .ekit-team-modal-close' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                  'ekit_team_close_icon_enable_height_width' => 'yes',
              ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_height',
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
                    '{{WRAPPER}} .ekit-team-modal-close' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_close_icon_enable_height_width' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_line_height',
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
                    '{{WRAPPER}} .ekit-team-modal-close' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_team_close_icon_enable_height_width' => 'yes',
                ],

            ]
        );

        $this->add_responsive_control(
            'ekit_team_close_icon_vertical_align',
            [
                'label' => esc_html__( 'Vertical Position ', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-infobox .elementskit-box-header .elementskit-info-box-icon' => ' -webkit-transform: translateY({{SIZE}}{{UNIT}}); -ms-transform: translateY({{SIZE}}{{UNIT}}); transform: translateY({{SIZE}}{{UNIT}});',
                ],
                'condition' => [
                    'ekit_icon_box_icon_position!' => 'top'
                ]

            ]
        );

        $this->end_controls_section();

        $this->insert_pro_message();
    }

    protected function render( ) {
        echo '<div class="ekit-wid-con">';
            $this->render_raw();
        echo '</div>';
    }

	protected function render_raw( ) {
		$settings = $this->get_settings_for_display();
        extract($settings);

		// Image sectionn
		$image_html = '';
		if ( !empty($ekit_team_image['url']) ) {
			$this->add_render_attribute('image', 'src', $ekit_team_image['url']);
			$this->add_render_attribute('image', 'alt', Control_Media::get_image_alt($ekit_team_image));
			$this->add_render_attribute('image', 'title', Control_Media::get_image_title($ekit_team_image));

			$image_html = Group_Control_Image_Size::get_attachment_image_html($settings, 'ekit_team_thumbnail', 'ekit_team_image');
		}

		$this->add_render_attribute(
			'profile_card',
			[
				'class' => 'profile-card elementor-animation-'. $team_hover_animation .' ' . $ekit_team_content_text_align . ' ekit-team-style-'.$ekit_team_style,
			]
		);

		// Social List
        if ( $ekit_team_socail_enable === 'yes' ) {
            foreach ($ekit_team_social_icons as $icon) {
                // List Item
                $this->add_render_attribute( 'social_item_' . $icon['_id'], 'class', 'elementor-repeater-item-' . $icon[ '_id' ] );
    
                // Link
                $this->add_link_attributes( 'social_link_' . $icon['_id'], $icon['ekit_team_link'] );
            }
        }
		
		if ( in_array($ekit_team_style, array('default', 'centered_style', 'centered_style_details', 'long_height_details', 'long_height_details_hover')) ):
		?>
		<?php if($ekit_team_style == 'centered_style'): ?> <div class="profile-square-v"> <?php endif; ?>
		<?php if($ekit_team_style == 'centered_style_details'): ?> <div class="profile-square-v square-v5 no_gutters"> <?php endif; ?>
		<?php if($ekit_team_style == 'long_height_details'): ?> <div class="profile-square-v square-v6 no_gutters"> <?php endif; ?>
		<?php if($ekit_team_style == 'long_height_details_hover'): ?> <div class="profile-square-v square-v6 square-v6-v2 no_gutters"><?php endif; ?>

		<div <?php echo $this->get_render_attribute_string('profile_card'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
			<?php if ($settings['ekit_team_chose_popup'] == 'yes') : ?>
				<a href="javascript:void(0)" data-mfp-src="#ekit_team_modal_<?php echo esc_attr($this->get_id()); ?>" class="ekit-team-popup">
			<?php endif; ?>
			
				<div class="profile-header ekit-team-img <?php echo esc_attr($ekit_team_style == 'default' ? 'ekit-img-overlay ekit-team-img-block' : ''); ?>" <?php if ( (isset($settings['ekit_team_chose_popup']) ? $ekit_team_chose_popup : 'no')  == 'yes') :?> data-toggle="modal" data-target="ekit_team_modal_#<?php echo esc_attr($this->get_id()); ?>" <?php endif; ?>>
					<?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
				</div><!-- .profile-header END -->
			<?php if ($settings['ekit_team_chose_popup'] == 'yes') : ?>
				</a>
			<?php endif; ?>
			

				<div class="profile-body">
					<?php if ( 'default' == $ekit_team_style && 'yes' == $ekit_team_toggle_icon && !empty( $ekit_team_top_icons ) ): ?>
					<div class="profile-icon<?php echo esc_attr($ekit_team_top_icon_align) ? ' icon-align-'.esc_attr($ekit_team_top_icon_align) : ''; ?>">

					<?php
						// new icon
						$migrated = isset( $settings['__fa4_migrated']['ekit_team_top_icons'] );
						// Check if its a new widget without previously selected icon using the old Icon control
						$is_new = empty( $settings['ekit_team_top_icon'] );
						if ( $is_new || $migrated ) {
							// new icon
							Icons_Manager::render_icon( $settings['ekit_team_top_icons'], [ 'aria-hidden' => 'true' ] );
						} else {
							?>
							<i class="<?php echo esc_attr($settings['ekit_team_top_icon']); ?>" aria-hidden="true"></i>
							<?php
						}
					?>
					</div>
					<?php endif; ?>

					<h2 class="profile-title">
					<?php if ($settings['ekit_team_chose_popup'] == 'yes') : ?>
						<a  href="javascript:void(0)" data-mfp-src="#ekit_team_modal_<?php echo esc_attr($this->get_id()); ?>" class="ekit-team-popup">
						<?php echo esc_html( $ekit_team_name ); ?>
						</a>
						<?php else: ?>
						<?php echo esc_html( $ekit_team_name ); ?>
					<?php endif; ?>
					</h2>
					<p class="profile-designation"><?php echo esc_html( $ekit_team_position ); ?></p>
					<?php if($ekit_team_show_short_description == 'yes' && $ekit_team_short_description != ''): ?>
					<p class="profile-content"><?php echo wp_kses($ekit_team_short_description, \ElementsKit_Lite\Utils::get_kses_array()); ?></p>
					<?php endif;?>
				</div><!-- .profile-body END -->

				<?php if(isset($ekit_team_socail_enable) && $ekit_team_socail_enable == 'yes'){?>
					<div class="profile-footer">
						<?php require Handler::get_dir() . 'parts/social-list.php'; ?>
					</div>
					<?php
					}
					?>
				</div>
				<?php if(in_array($ekit_team_style, array('centered_style', 'centered_style_details', 'long_height_details', 'long_height_details_hover')) ): ?> </div> <?php endif; ?>
			<?php endif; ?>

			<?php if ( in_array($ekit_team_style, array('overlay', 'overlay_details', 'long_height_hover', 'overlay_circle', 'overlay_circle_hover')) ): ?>
				<?php if($ekit_team_style == 'overlay_details'): ?> <div class="image-card-v2"> <?php endif; ?>
				<?php if($ekit_team_style == 'long_height_hover'): ?> <div class="<?php echo esc_attr($settings['ekit_team_remove_gutters'] == 'yes' ? '' : 'small-gutters'); ?> image-card-v3"> <?php endif; ?>
				<?php if($ekit_team_style == 'overlay_circle'): ?> <div class="style-circle ekit-team-img-fit"> <?php endif; ?>
				<?php if($ekit_team_style == 'overlay_circle_hover'): ?> <div class="image-card-v2 style-circle"> <?php endif; ?>
					<div class="profile-image-card elementor-animation-<?php echo esc_attr($team_hover_animation) ?> ekit-team-img ekit-team-style-<?php echo esc_attr($ekit_team_style); ?> <?php if(isset($ekit_team_content_text_align)) { echo esc_attr($ekit_team_content_text_align);} ?>">

						<?php if($ekit_team_style == 'long_height_hover'){ ?>
							<?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
						<?php
							$modalClass = 'team-sidebar_'.$ekit_team_style.'';
						}else{
							$modalClass = 'team-modal_'.$ekit_team_style.'';
						?>
							<?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
						<?php }?>
						<div class="hover-area">
							<div class="profile-body">
								<h2 class="profile-title">
								<?php if ($settings['ekit_team_chose_popup'] == 'yes') : ?>
									<a  href="javascript:void(0)" data-mfp-src="#ekit_team_modal_<?php echo esc_attr($this->get_id()); ?>" class="ekit-team-popup">
									<?php echo esc_html( $ekit_team_name ); ?>
									</a>
									<?php else: ?>
									<?php echo esc_html( $ekit_team_name ); ?>
								<?php endif; ?>
								</h2>
								<p class="profile-designation"><?php echo esc_html( $ekit_team_position ); ?></p>
								<?php if($ekit_team_show_short_description == 'yes' && $ekit_team_short_description != ''): ?>
								<p class="profile-content"><?php echo wp_kses($ekit_team_short_description, \ElementsKit_Lite\Utils::get_kses_array()); ?></p>
								<?php endif;?>
							</div>
							<?php if(isset($ekit_team_socail_enable) && $ekit_team_socail_enable == 'yes'){?>
								<div class="profile-footer">
									<?php require Handler::get_dir() . 'parts/social-list.php'; ?>
								</div>
							<?php
							}
							?>
						</div>
					</div>
					<?php if(in_array($ekit_team_style, array('overlay_details', 'long_height_hover' , 'overlay_circle', 'overlay_circle_hover')) ): ?> </div> <?php endif; ?>

				<?php
				endif;
				if ( 'hover_info' == $ekit_team_style ):
				?>
				
				<div class="profile-square-v square-v4 elementor-animation-<?php echo esc_attr($team_hover_animation) ?> ekit-team-style-<?php echo esc_attr($ekit_team_style); ?>">
					<div class="profile-card <?php if(isset($ekit_team_content_text_align)) { echo esc_attr($ekit_team_content_text_align);} ?>">
						<div class="profile-header ekit-team-img" <?php if ($settings['ekit_team_chose_popup'] == 'yes') :?> data-toggle="modal" data-target="#ekit_team_modal_<?php echo esc_attr($this->get_id()); ?>" <?php endif; ?>>
							<?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
						</div><!-- .profile-header END -->
						<div class="profile-body">
							<h2 class="profile-title">
							<?php if ($settings['ekit_team_chose_popup'] == 'yes') : ?>
								<a href="javascript:void(0)" data-mfp-src="#ekit_team_modal_<?php echo esc_attr($this->get_id()); ?>" class="ekit-team-popup">
								<?php echo esc_html( $ekit_team_name ); ?>
								</a>
								<?php else: ?>
								<?php echo esc_html( $ekit_team_name ); ?>
							<?php endif; ?>
							</h2>
							<p class="profile-designation"><?php echo esc_html( $ekit_team_position ); ?></p>
							<?php if($ekit_team_show_short_description == 'yes' && $ekit_team_short_description != ''): ?>
							<p class="profile-content"><?php echo wp_kses($ekit_team_short_description, \ElementsKit_Lite\Utils::get_kses_array()); ?></p>
							<?php endif;?>
							<?php
								if ( isset($ekit_team_socail_enable) && $ekit_team_socail_enable == 'yes' ) {
									require Handler::get_dir() . 'parts/social-list.php';
								}
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

		<?php if ( $ekit_team_chose_popup == 'yes' ): ?>
			<div class="zoom-anim-dialog mfp-hide elementskit-team-popup" id="ekit_team_modal_<?php echo esc_attr($this->get_id()); ?>" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<button type="button" class="ekit-team-modal-close">
							<?php Icons_Manager::render_icon( $ekit_team_close_icon_changes, ['aria-hidden' => 'true'] ); ?>
						</button>

						<div class="modal-body">
							<?php if ( !empty($image_html) ) { ?>
								<div class="ekit-team-modal-img">
									<?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
								</div>
							<?php } ?>

							<div class="ekit-team-modal-info<?php echo !empty($image_html) ? ' has-img' : ''; ?>">
								<h2 class="ekit-team-modal-title"><?php echo esc_html( $ekit_team_name ); ?></h2>
								<p class="ekit-team-modal-position"><?php echo esc_html( $ekit_team_position ); ?></p>
								
								<div class="ekit-team-modal-content">
									<?php echo wp_kses($ekit_team_description, \ElementsKit_Lite\Utils::get_kses_array()); ?>
								</div>

								<?php if ( $ekit_team_phone || $ekit_team_email ) { ?>
									<ul class="ekit-team-modal-list">
										<?php if ( $ekit_team_phone ): ?>
											<li><strong><?php esc_html_e( 'Phone', 'elementskit-lite' ); ?>:</strong><a href="tel:<?php echo esc_attr( $ekit_team_phone ); ?>"><?php echo esc_html( $ekit_team_phone ); ?></a></li>
										<?php endif; ?>

										<?php if ( $ekit_team_email ): ?>
											<li><strong><?php esc_html_e( 'Email', 'elementskit-lite' ); ?>:</strong><a href="mailto:<?php echo esc_attr( $ekit_team_email ); ?>"><?php echo esc_html( $ekit_team_email ); ?></a></li>
										<?php endif; ?>
									</ul>
								<?php } ?>
								
								<?php
									if ( isset($ekit_team_socail_enable) && $ekit_team_socail_enable == 'yes' ) {
										require Handler::get_dir() . 'parts/social-list.php';
									}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php
	}
}
