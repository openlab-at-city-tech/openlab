<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Piechart_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Piechart extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;
    
    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->add_script_depends('easypiechart');
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
        return 'https://wpmet.com/doc/pie-chart/';
    }

    protected function register_controls() {


        // Content section

        $this->start_controls_section(
            'ekit_piechart_content_section',
            [
                'label' => esc_html__( 'Content', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'ekit_piechart_style',
            [
                'label' => esc_html__( 'Pie Chart Style', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'simple',
                'options' => [
                    'simple'  => esc_html__( 'Simple', 'elementskit-lite' ),
                    'withcontent' => esc_html__( 'With Content', 'elementskit-lite' ),
                ],
            ]
        );

        $this->add_control(
            'ekit_piechart_content',
            [
                'label' => esc_html__( 'Chart Content', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'ekit_piechart_percentage',
                'options' => [
                    'ekit_piechart_percentage'  => esc_html__( 'Percentage', 'elementskit-lite' ),
                    'icon' => esc_html__( 'Icon', 'elementskit-lite' ),
                ],
            ]
        );

        $this->add_control(
            'ekit_piechart_percentage',
            [
                'label' => esc_html__( 'Percentage', 'elementskit-lite' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'default' => 40,
            ]
        );

        $this->add_control(
            'ekit_piechart_icon_type',
            [
                'label' => esc_html__( 'Icon type', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'icon',
                'options' => [
                    'icon'  => esc_html__( 'Icon', 'elementskit-lite' ),
                    'image' => esc_html__( 'Image', 'elementskit-lite' ),
                ],
                'condition' => [
                    'ekit_piechart_content' => 'icon'
                ]
            ]
        );

        $this->add_control(
            'ekit_piechart_icons',
            [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_piechart_icon',
                'default' => [
                    'value' => 'icon icon-apartment',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_piechart_icon_type' => 'icon',
                    'ekit_piechart_content' => 'icon'
                ]
            ]
        );

        $this->add_control(
            'ekit_piechart_icon_image',
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
                    'ekit_piechart_icon_type' => 'image',
                    'ekit_piechart_content' => 'icon'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'ekit_piechart_icon_image_size_group',
                'default' => 'thumbnail',
                'condition' => [
                    'ekit_piechart_icon_type' => 'image',
                    'ekit_piechart_content' => 'icon'
                ]
            ]
        );

        $this->add_control(
            'ekit_piechart_title',
            [
                'label' => esc_html__( 'Title', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'default' => esc_html__( 'Default title', 'elementskit-lite' ),
                'placeholder' => esc_html__( 'Type your title here', 'elementskit-lite' ),
                'label_block' => true,
                'condition' => [
                    'ekit_piechart_style' => 'withcontent'
                ]
            ]
        );

        $this->add_control(
            'ekit_piechart_item_description',
            [
                'label' => esc_html__( 'Description', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 10,
				'dynamic' => [
					'active' => true,
				],
                'default' => esc_html__( 'Default description', 'elementskit-lite' ),
                'placeholder' => esc_html__( 'Type your description here', 'elementskit-lite' ),
                'label_block' => true,
                'condition' => [
                    'ekit_piechart_style' => 'withcontent'
                ]
            ]
        );

        $this->add_control(
            'ekit_piechart_content_type',
            [
                'label' => esc_html__( 'Content type', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'simple',
                'options' => [
                    'simple'  => esc_html__( 'Static', 'elementskit-lite' ),
                    'flip-card' => esc_html__( 'Flip Card', 'elementskit-lite' ),
                ],
                'condition' => [
                    'ekit_piechart_style' => 'withcontent'
                ]
            ]
        );



        $this->end_controls_section();

        //  Style

        $this->start_controls_section(
            'ekit_piechart_section_content',
            [
                'label' => esc_html__( 'Title ', 'elementskit-lite' ),
                'tab' => controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_piechart_style' => 'withcontent'
                ]
            ]
        );

        $this->add_control(
            'ekit_piechart_title_color',
            [
                'label' => esc_html__( 'Title Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-piechart-title' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'ekit_piechart_style' => 'withcontent'
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_piechart_title_typography_group',
                'label' => esc_html__( 'Title Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .ekit-piechart-title',
                'condition' => [
                    'ekit_piechart_style' => 'withcontent'
                ],
            ]
        );
        $this->add_responsive_control(
			'ekit_piechart_title_margin',
			[
				'label' =>esc_html__( 'Title margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => 	[
					'top' => '0',
					'right' => '0',
					'bottom' => '20',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-piechart-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

                // Content

        $this->start_controls_section(
            'ekit_piechart_general_settings',
            [
                'label' => esc_html__( 'Content', 'elementskit-lite' ),
                'tab' => controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_piechart_style' => 'withcontent'
                ],
            ]
        );

        $this->add_control(
            'ekit_piechart_content_color',
            [
                'label' => esc_html__( 'Content Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-single-piechart p' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'ekit_piechart_style' => 'withcontent'
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_piechart_content_typography_group',
                'label' => esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .withcontent p',
                'condition' => [
                    'ekit_piechart_style' => 'withcontent'
                ],
            ]
        );

        $this->add_control(
			'ekit_piechart_content_margin',
			[
				'label' => __( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-single-piechart p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'ekit_piechart_content_align',
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
                    '{{WRAPPER}} .ekit-single-piechart' => 'text-align: {{VALUE}};',
                ],
                'default' => 'center',
            ]
        );

        $this->end_controls_section();

        //  Flip card

        $this->start_controls_section(
            'ekit_piechart_section_flip_card',
            [
                'label' => esc_html__( 'Flip Card ', 'elementskit-lite' ),
                'tab' => controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_piechart_style' => 'withcontent',
                    'ekit_piechart_content_type'   => 'flip-card',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_piechart_flip_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => ['gradient'],
                'selector' => '{{WRAPPER}} .flip-card .back',
            ]
        );

        $this->add_responsive_control(
			'ekit_piechart_flip_back_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-single-piechart.flip-card .back' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


        $this->end_controls_section();

        //  Chart style

        $this->start_controls_section(
            'ekit_piechart_section_piechart',
            [
                'label' => esc_html__( 'Chart', 'elementskit-lite' ),
                'tab' => controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'ekit_piechart_size',
            [
                'label' => esc_html__( 'Piechart Size', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
				'render_type' => 'template',
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 250,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 150,
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-single-piechart > .piechart canvas' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
            ]
        );
        $this->add_control(
            'ekit_piechart_border_size',
            [
                'label' => esc_html__( 'Border Size', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
                ],
            ]
        );

        $this->add_control(
            'ekit_piechart_color_style',
            [
                'label' => esc_html__( 'Color Type', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => [
                    'normal'  => esc_html__( 'Normal', 'elementskit-lite' ),
                    'gradient' => esc_html__( 'Gradient', 'elementskit-lite' ),
                ],
            ]
        );

        $this->add_control(
            'ekit_piechart_line_color',
            [
                'label' => esc_html__( 'Bar Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
				'render_type' => 'template',
                'condition' => [
                    'ekit_piechart_color_style' => 'normal'
				],
            ]
        );

        $this->add_control(
            'ekit_piechart_bar_color_bg',
            [
                'label' => esc_html__( 'Bar Background Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
				'render_type' => 'template',
            ]
        );

        $this->add_control(
            'ekit_piechart_gradientColor1',
            [
                'label' => esc_html__( 'Gradient Color1', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
				'render_type' => 'template',
                'condition' => [
                    'ekit_piechart_color_style' => 'gradient'
				],
            ]
        );

        $this->add_control(
            'ekit_piechart_gradientColor2',
            [
                'label' => esc_html__( 'Gradient Color2', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
				'render_type' => 'template',
                'condition' => [
                    'ekit_piechart_color_style' => 'gradient'
				],
            ]
        );

        $this->add_control(
            'ekit_piechart_iocn_color',
            [
                'label'     => esc_html__( ' Icon Color', 'elementskit-lite' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#333',
                'selectors' => [
                    '{{WRAPPER}} .ekit-chart-content i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-chart-content svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_piechart_icon_type!' => 'image',
                    'ekit_piechart_content' => 'icon',
                ]
            ]
        );

        $this->add_control(
            'ekit_piechart_content_color_number',
            [
                'label' => esc_html__( ' Number Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ekit-single-piechart span.ekit-chart-content' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'ekit_piechart_content' => 'ekit_piechart_percentage',
                ]
            ]
        );

        $this->end_controls_section();
        //  Background

        $this->start_controls_section(
            'ekit_piechart_background',
            [
                'label' => esc_html__( 'Background ', 'elementskit-lite' ),
                'tab' => controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'ekit_piechart_wrapper_padding',
            [
                'label' =>esc_html__( 'Wrapper Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default' => [
                    'top' => '60',
                    'right' => '0',
                    'bottom' => '60',
                    'left' => '0',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-single-piechart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_piechart_wrapper_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .ekit-single-piechart',
                'separator' => 'before',
                'description' => esc_html__('Eg: 0px 28px 40px 0px rgba(0, 0, 0, .1)', 'elementskit-lite'),
            ]
        );

        $this->start_controls_tabs('ekit_piechart_style_tabs');

        $this->start_controls_tab(
            'ekit_piechart_wrapper_bg_style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );


        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_piechart_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient', 'video' ],
                'selector' => '{{WRAPPER}} .ekit-single-piechart',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_piechart_bg_style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_piechart_background_hover_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient', 'video' ],
                'selector' => '{{WRAPPER}} .ekit-single-piechart:hover',
            ]
        );

        $this->add_control(
            'ekit_piechart_bg_hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'elementskit-lite' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
                'prefix_class' => 'elementor-animation-',
            ]
        );


        $this->end_controls_tab();
        $this->end_controls_tabs();

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

		$colors = $this->get_globals_colors($settings);

        if($settings['ekit_piechart_bg_hover_animation'] != '') {
            $this->add_render_attribute( 'pieechart', 'class', $settings['hover_animation'] );
        }

        $this->add_render_attribute( 'pieechart', 'class', 'ekit-single-piechart' );
        if($settings['ekit_piechart_style'] == 'simple'){
            $this->add_render_attribute( 'pieechart', 'class', 'text-center' );
        }


        if($settings['ekit_piechart_content_type'] == 'flip-card'){
            $this->add_render_attribute( 'pieechart', 'class', $settings['ekit_piechart_content_type'] );
        }

        if($settings['ekit_piechart_flip_background_group_background'] == 'gradient'){
            $this->add_render_attribute( 'pieechart', 'class', $settings['ekit_piechart_content_type'].' '.'flip-gradient-color' );
        }

		$this->add_render_attribute( 'pieechart', 'class', $settings['ekit_piechart_style'] );

		$this->add_render_attribute( 'pieechartscreen', [
			'class'	=> 'colorful-chart piechart',
			'data-pie_color_style'	=> $settings['ekit_piechart_color_style'],
			'data-gradientcolor1'	=> $colors['ekit_piechart_gradientColor1'],
			'data-gradientcolor2'	=> $colors['ekit_piechart_gradientColor2'],
		] );

		if($colors['ekit_piechart_line_color'] != '') {
			$this->add_render_attribute( 'pieechartscreen', 'data-color', $colors['ekit_piechart_line_color'] );
		}

		if($colors['ekit_piechart_bar_color_bg'] != '') {
			$this->add_render_attribute( 'pieechartscreen', 'data-barbg', $colors['ekit_piechart_bar_color_bg'] );
		}

		$piechart_size = $settings['ekit_piechart_size']['size'] != '' ? $settings['ekit_piechart_size']['size'] : 150;
		$this->add_render_attribute( 'pieechartscreen', 'data-size', $piechart_size );

		$line_size = $settings['ekit_piechart_border_size']['size'] != '' ? $settings['ekit_piechart_border_size']['size'] : 5;
		$this->add_render_attribute( 'pieechartscreen', 'data-linewidth', $line_size );


		if($settings['ekit_piechart_percentage'] != '') {
			$this->add_render_attribute( 'pieechartscreen', 'data-percent', $settings['ekit_piechart_percentage'] );
		}

		if (!empty($settings['ekit_piechart_icon_image']['url'])) {
			$this->add_render_attribute('image', 'src', $settings['ekit_piechart_icon_image']['url']);
			$this->add_render_attribute('image', 'alt', Control_Media::get_image_alt($settings['ekit_piechart_icon_image']));
			$this->add_render_attribute('image', 'title', Control_Media::get_image_title($settings['ekit_piechart_icon_image']));

			$image_html = Group_Control_Image_Size::get_attachment_image_html($settings, 'ekit_piechart_icon_image_size_group', 'ekit_piechart_icon_image');
		}

        $flip_front_start = '';
        $flip_front_end = '';
        $flip_back_start = '';
        $flip_back_end = '';

        if($settings['ekit_piechart_style'] == 'withcontent' && $settings['ekit_piechart_content_type'] == 'flip-card'){
            $flip_front_start .= '<div class="front"><div class="ekit-single-piechart_in">';
            $flip_front_end .= '</div></div>';
            $flip_back_start = '<div class="back">';
            $flip_back_end = '</div>';
        }

        ?>
        <div <?php echo $this->get_render_attribute_string( 'pieechart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>

            <?php echo wp_kses($flip_front_start, \ElementsKit_Lite\Utils::get_kses_array()); ?>

            <div <?php echo $this->get_render_attribute_string( 'pieechartscreen' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>

                <?php if($settings['ekit_piechart_percentage'] != '' && $settings['ekit_piechart_content'] == 'ekit_piechart_percentage') { ?>

                    <span class="ekit-chart-content"><?php echo esc_html($settings['ekit_piechart_percentage']); ?>&#37;</span>

                <?php } ?>

                <?php if($settings['ekit_piechart_content'] == 'icon' && $settings['ekit_piechart_icon_type'] == 'image') { ?>

                    <span class="ekit-chart-content">
                        <?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
                    </span>

                <?php } ?>

                <?php if($settings['ekit_piechart_content'] == 'icon' && $settings['ekit_piechart_icon_type'] == 'icon') { ?>

                    <span class="ekit-chart-content">
                        <?php
                            // new icon
                            $migrated = isset( $settings['__fa4_migrated']['ekit_piechart_icons'] );
                            // Check if its a new widget without previously selected icon using the old Icon control
                            $is_new = empty( $settings['ekit_piechart_icon'] );
                            if ( $is_new || $migrated ) {
                                // new icon
                                Icons_Manager::render_icon( $settings['ekit_piechart_icons'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($settings['ekit_piechart_icon']); ?>" aria-hidden="true"></i>
                                <?php
                            }
                        ?>
                    </span>

                <?php } ?>



            </div>
            <?php

		   echo wp_kses($flip_front_end.$flip_back_start, \ElementsKit_Lite\Utils::get_kses_array());


             if($settings['ekit_piechart_style'] == 'withcontent' && $settings['ekit_piechart_title'] != '') {  ?>
             <h2 class="ekit-piechart-title"><?php echo esc_html($settings['ekit_piechart_title']); ?></h2>
            <?php }

            if($settings['ekit_piechart_style'] == 'withcontent' && $settings['ekit_piechart_item_description'] != '') { ?>

            <p><?php echo wp_kses($settings['ekit_piechart_item_description'], \ElementsKit_Lite\Utils::get_kses_array()); ?></p>

            <?php }

            echo wp_kses($flip_back_end, \ElementsKit_Lite\Utils::get_kses_array());

            ?>
        </div>
    <?php
    }

	protected function get_globals_colors($settings) {
		$global_colors = [];
		$kit_items = $this->get_kit_items();
		$globals_vars = !empty($settings['__globals__']) ? array_filter($settings['__globals__']) : [];
		if($globals_vars) {
			foreach($globals_vars as $key => $globals_var) {
				parse_str(wp_parse_url($globals_var, PHP_URL_QUERY), $queryParams);
				if (isset($queryParams['id']) && isset($kit_items[$queryParams['id']]['value'])) {
					$global_colors[$key] = $kit_items[$queryParams['id']]['value'];
				}
			}
		}

		$color_controls = [
			'ekit_piechart_line_color',
			'ekit_piechart_bar_color_bg',
			'ekit_piechart_gradientColor1',
			'ekit_piechart_gradientColor2'
		];

		foreach($color_controls as $color_control) {
			if(isset($global_colors[$color_control])) {
				continue;
			}

			$global_colors[$color_control] = isset($settings[$color_control]) ? $settings[$color_control] : '';
		}

		return $global_colors;
	}

	protected function get_kit_items() {
		$result = [];
		$kit = Plugin::$instance->kits_manager->get_active_kit_for_frontend();

		$system_items = $kit->get_settings_for_display( 'system_colors' );
		$custom_items = $kit->get_settings_for_display( 'custom_colors' );

		if ( ! $system_items ) {
			$system_items = [];
		}

		if ( ! $custom_items ) {
			$custom_items = [];
		}

		$items = array_merge( $system_items, $custom_items );

		foreach ( $items as $index => $item ) {
			$id = $item['_id'];
			$result[ $id ] = [
				'id' => $id,
				'title' => $item['title'],
				'value' => $item['color'],
			];
		}

		return $result;
	}
}
