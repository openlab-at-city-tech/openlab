<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Lottie_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Lottie extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;

    public function get_script_depends() {
        return ['lottie', 'lottie-init'];
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
        return 'https://wpmet.com/doc/lottie-animation/';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'ekit_lottie',
            [
                'label' => esc_html__( 'Lottie', 'elementskit-lite' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
            $this->add_control(
                'ekit_lottie_type',
                [
                    'label'         => esc_html__( 'Select JSON', 'elementskit-lite' ),
                    'type'          => \Elementor\Controls_Manager::CHOOSE,
                    'default'       => 'file',
                    'options'       => [
                        'file'  => [
                            'title' => esc_html__( 'JSON File', 'elementskit-lite' ),
                            'icon' => 'far fa-file',
                        ],
                        'url'   => [
                            'title' => esc_html__( 'JSON URL', 'elementskit-lite' ),
                            'icon' => 'fas fa-link',
                        ],
                    ]
                ]
            );

            $this->add_control(
                'ekit_lottie_json',
                [
                    'show_label'    => false,
                    'description'   => sprintf(
                        __('Discover thousands of %sLottie animations%s ready to use.', 'elementskit-lite'),
                        '<a href="https://lottiefiles.com/featured" target="_blank">',
                        '</a>'
                    ),
                    'type'          => \Elementor\Controls_Manager::MEDIA,
                    'media_type'    => 'application/json',
                    'condition'    => [
                        'ekit_lottie_type'  => 'file',
                    ],
                ]
            );

            $this->add_control(
                'ekit_lottie_url',
                [
                    'show_label'    => false,
                    'label_block'   => true,
                    'description'   => sprintf(
                        __('Discover thousands of %sLottie animations%s ready to use.', 'elementskit-lite'),
                        '<a href="https://lottiefiles.com/featured" target="_blank">',
                        '</a>'
                    ),
                    'type'          => \Elementor\Controls_Manager::TEXT,
					'dynamic'       => [
						'active' => true,
					],
                    'placeholder'   => esc_html__( 'https://example.com/file.json', 'elementskit-lite' ),
                    'show_external' => false,
                    'condition'     => [
                        'ekit_lottie_type'  => 'url'
                    ],
                ]
            );

            $this->add_control(
                'ekit_lottie_link_check',
                [
                    'label'         => esc_html__( 'Link', 'elementskit-lite' ),
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                ]
            );

            $this->add_control(
                'ekit_lottie_link',
                [
                    'show_label'    => false,
                    'type'          => \Elementor\Controls_Manager::URL,
					'dynamic'       => [
						'active' => true,
					],
                    'condition'     => [
                        'ekit_lottie_link_check'    => 'yes'
                    ],
                ]
            );

            $this->add_control(
                'ekit_lottie_options',
                [
                    'label'         => esc_html__( 'Animation Options', 'elementskit-lite' ),
                    'type'          => \Elementor\Controls_Manager::HEADING,
                    'separator'     => 'before',
                ]
            );

            $this->add_control(
                'ekit_lottie_reverse',
                [
                    'label'         => esc_html__( 'Reverse', 'elementskit-lite' ),
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                ]
            );

            $this->add_control(
                'ekit_lottie_autoplay',
                [
                    'label'         => esc_html__( 'Autoplay', 'elementskit-lite' ),
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'return_value'  => 'true',
                    'default'       => 'true',
                ]
            );

            $this->add_control(
                'ekit_lottie_on_scroll',
                [
                    'label'         => esc_html__( 'Start when visible', 'elementskit-lite' ),
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'condition'     => [
                        'ekit_lottie_autoplay'  => ''
                    ],
                ]
            );

            $this->add_control(
                'ekit_lottie_loop',
                [
                    'label'         => esc_html__( 'Loop', 'elementskit-lite' ),
                    'type'          => \Elementor\Controls_Manager::SWITCHER,
                    'return_value'  => 'true',
                    'default'       => 'true',
                ]
            );

            $this->add_control(
                'ekit_lottie_loop_count',
                [
                    'label'         => esc_html__( 'Loop Count', 'elementskit-lite' ),
                    'type'          => \Elementor\Controls_Manager::SLIDER,
                    'range'         => [
                        'px'    => [
                            'max'   => 10,
                        ]
                    ],
                    'condition'     => [
                        'ekit_lottie_loop'  => 'true'
                    ],
                ]
            );

            $this->add_control(
                'ekit_lottie_speed',
                [
                    'label'         => esc_html__( 'Speed', 'elementskit-lite' ),
                    'type'          => \Elementor\Controls_Manager::SLIDER,
                    'range'         => [
                        'px'    => [
                            'max'   => 10,
                            'step'  => 0.2,
                        ]
                    ],
                    'default'       => [
                        'size'  => 1,
                    ],
                ]
            );

            $this->add_control(
                'ekit_lottie_renderer',
                [
                    'label'         => esc_html__( 'Render Type', 'elementskit-lite' ),
                    'type'          => \Elementor\Controls_Manager::CHOOSE,
                    'default'       => 'svg',
                    'options'       => [
                        'svg'           => [
                            'title' => esc_html__( 'SVG', 'elementskit-lite' ),
                            'icon'  => 'fa fa-magic',
                        ],
                        'canvas'        => [
                            'title' => esc_html__( 'Canvas', 'elementskit-lite' ),
                            'icon'  => 'fa fa-chalkboard',
                        ],
                    ],
                ]
            );

            $this->add_control(
                'ekit_lottie_action',
                [
                    'label'         => esc_html__( 'On Hover', 'elementskit-lite' ),
                    'type'          => \Elementor\Controls_Manager::SELECT,
                    'options'       => [
                        ''          => esc_html__( 'None', 'elementskit-lite' ),
                        'play'      => esc_html__( 'Play', 'elementskit-lite' ),
                        'pause'     => esc_html__( 'Pause', 'elementskit-lite' ),
                        'reverse'   => esc_html__( 'Reverse', 'elementskit-lite' ),
                    ],
                ]
            );
        $this->end_controls_section();

        $this->start_controls_section(
            'ekit_lottie_styles',
            [
                'label' => esc_html__( 'Lottie', 'elementskit-lite' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
            $this->start_controls_tabs(
                'ekit_lottie_state'
            );
                $this->start_controls_tab(
                    'ekit_lottie_normal',
                    [
                        'label' => esc_html__( 'Normal', 'elementskit-lite' ),
                    ]
                );
                    $this->add_control(
                        'ekit_lottie_opacity',
                        [
                            'label'         => esc_html__( 'Opacity', 'elementskit-lite' ),
                            'type'          => \Elementor\Controls_Manager::SLIDER,
                            'range'         => [
                                'px'    => [
                                    'min'   => 0,
                                    'max'   => 1,
                                    'step'  => 0.1,
                                ]
                            ],
                            'selectors'     => [
                                '{{WRAPPER}}'   => 'opacity: {{SIZE}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        \Elementor\Group_Control_Css_Filter::get_type(),
                        [
                            'name'      => 'ekit_lottie_filter',
                            'selector'  => '{{WRAPPER}}',
                        ]
                    );
                $this->end_controls_tab();

                $this->start_controls_tab(
                    'ekit_lottie_hover',
                    [
                        'label' => esc_html__( 'Hover', 'elementskit-lite' ),
                    ]
                );
                    $this->add_control(
                        'ekit_lottie_opacity_hover',
                        [
                            'label'         => esc_html__( 'Opacity', 'elementskit-lite' ),
                            'type'          => \Elementor\Controls_Manager::SLIDER,
                            'range'         => [
                                'px'    => [
                                    'min'   => 0,
                                    'max'   => 1,
                                    'step'  => 0.1,
                                ]
                            ],
                            'selectors'     => [
                                '{{WRAPPER}}:hover'   => 'opacity: {{SIZE}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        \Elementor\Group_Control_Css_Filter::get_type(),
                        [
                            'name'      => 'ekit_lottie_filter_hover',
                            'selector'  => '{{WRAPPER}}',
                        ]
                    );

                    $this->add_control(
                        'ekit_lottie_transition',
                        [
                            'label' => esc_html__( 'Transition', 'elementskit-lite' ),
                            'type'  => \Elementor\Controls_Manager::SLIDER,
                            'range' => [
                                'px'    => [
                                    'max'   => 10,
                                    'step'  => 0.1,
                                ],
                            ],
                            'selectors' => [
                                '{{WRAPPER}}'   => 'transition: all {{SIZE}}s ease;',
                            ],
                        ]
                    );
                $this->end_controls_tab();
            $this->end_controls_tabs();
        $this->end_controls_section();

        $this->insert_pro_message();
    }

    protected function render() {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute(
            'wrapper',
            [
                'id'                    => 'ekit_lottie_' . $this->get_id(),
                'class'                 => 'ekit_lottie',
                'data-autoplay'         => $settings['ekit_lottie_autoplay'],
                'data-on-scroll'        => $settings['ekit_lottie_on_scroll'],
                'data-speed'            => $settings['ekit_lottie_speed']['size'],
                'data-direction'        => $settings['ekit_lottie_reverse'],
                'data-action'           => $settings['ekit_lottie_action'],
                'data-renderer'         => $settings['ekit_lottie_renderer'],
            ]
        );


        if ( !empty($settings['ekit_lottie_json']['url']) ):
            $this->add_render_attribute( 'wrapper', 'data-path', $settings['ekit_lottie_json']['url'] );
        else:
            $this->add_render_attribute( 'wrapper', 'data-path', $settings['ekit_lottie_url'] );
        endif;


        if ( $settings['ekit_lottie_loop_count']['size'] ):
            $this->add_render_attribute( 'wrapper', 'data-loop', ($settings['ekit_lottie_loop_count']['size'] - 1) );
        else:
            $this->add_render_attribute( 'wrapper', 'data-loop', $settings['ekit_lottie_loop'] );
        endif;


        if ( !empty($settings['ekit_lottie_link']['url']) && $settings['ekit_lottie_link']['url'] ):
            $this->add_render_attribute( 'wrapper', 'class', 'met_d--block' );
            $this->add_link_attributes( 'link', $settings['ekit_lottie_link'] );
        
            echo '<a '. $this->get_render_attribute_string( 'link' ) .' '. $this->get_render_attribute_string( 'wrapper' ) .'>&nbsp;</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_render_attribute_string Already escaped by elementor 
        else:
            echo '<div '. $this->get_render_attribute_string( 'wrapper' ) .'>&nbsp;</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_render_attribute_string Already escaped by elementor
        endif;
    }
}
