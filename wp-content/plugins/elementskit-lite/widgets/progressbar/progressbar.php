<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Progressbar_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Progressbar extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;
    
    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->add_script_depends('elementor-waypoints');
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
        return 'https://wpmet.com/doc/progress-bar/';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'ekit_progressbar_content', [
                'label' => esc_html__( 'Progress Bar', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_progressbar_style',
            [
                'label' =>esc_html__( 'Style', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__( 'Default', 'elementskit-lite' ),
                    'inner-content skill-big' => esc_html__( 'Inner Content', 'elementskit-lite' ),
                    'skilltrack-style2' => esc_html__( 'Bar Shadow', 'elementskit-lite' ),
                    'tooltip-style3' => esc_html__( 'Tooltip', 'elementskit-lite' ),
                    'tooltip-style2' => esc_html__( 'Tooltip Box', 'elementskit-lite' ),
                    'tooltip-style' => esc_html__( 'Tooltip Rounded', 'elementskit-lite' ),
                    'pin-style' => esc_html__( 'Tooltip Circle', 'elementskit-lite' ),
                    'style-switch' => esc_html__( 'Switch', 'elementskit-lite' ),
                    'style-ribbon' => esc_html__( 'Ribbon', 'elementskit-lite' ),
                    'style-stripe skill-medium tooltip-style' => esc_html__( 'Stripe', 'elementskit-lite' ),
                ],
            ]
        );

        $this->add_control(
            'ekit_progressbar_icons',
            [
                'label'         => esc_html__('Add Icon', 'elementskit-lite'),
                'label_block'   => true,
                'type'          => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_progressbar_icon',
                'default' => [
                    'value' => 'icon icon-arrow-right',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_progressbar_style' => ['inner-content skill-big'],
                ],
            ]
        );


        $this->add_control(
            'ekit_progressbar_title',
            [
                'label'         => esc_html__('Title', 'elementskit-lite'),
                'label_block'   => true,
                'type'          => Controls_Manager::TEXT,
                'dynamic' 		=> [
                    'active' => true,
                ],
                'default'       => 'WordPress',
            ]
        );

        $this->add_control(
            'ekit_progressbar_percentage',
            [
                'label'     => esc_html__('Percentage', 'elementskit-lite'),
                'type'      => Controls_Manager::NUMBER,
                'dynamic' 	=> [
                    'active' => true,
                ],
                'min'       => 1,
                'max'       => 100,
                'step'      => 1,
                'default'   => 90,
            ]
        );

        $this->add_control(
            'ekit_progressbar_percentage_show',
            [
                'label' => esc_html__('Hide Percentage Number? ', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'return_value' => 'none',
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'selectors' => [
                    '{{WRAPPER}} .skillbar-group .number-percentage-wraper' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_data_duration',
            [
                'label'     => esc_html__('Animation Duration', 'elementskit-lite'),
                'type'      => Controls_Manager::SLIDER,
                'dynamic' 	=> [
                    'active' => true,
                ],
                'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 10000,
						'step' => 5,
					],

				],
				'default' => [
					'size' => 3500,
				],

            ]
        );

        $this->end_controls_section();


        // Bar Styles
        $this->start_controls_section(
            'ekit_progressbar_bar_style', [
                'label' =>esc_html__( 'Bar', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'ekit_progressbar_background',
                'label'     => esc_html__( 'Background', 'elementskit-lite' ),
                'types'     => [ 'classic', 'gradient' ],
                'selector'  => '{{WRAPPER}} .skillbar-group .skill-bar',
                'default'   => '#f5f5f5'
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_bar_height',
            [
                'label'         => esc_html__('Height', 'elementskit-lite'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'em'],
                'range'  => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 200,
                    ],
                ],
                'separator'  => 'before',
                'condition' => [
                    'ekit_progressbar_style!' => ['style-switch'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .skillbar-group .skill-bar' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_progressbar_bar_shadow',
                'label' => esc_html__( 'Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .skillbar-group .skill-bar',
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_bar_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .skillbar-group .skill-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_bar_padding',
            [
                'label'      => esc_html__( 'Padding', 'elementskit-lite' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'condition' => [
                    'ekit_progressbar_style!' => ['style-switch'],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .skillbar-group .skill-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_bar_margin',
            [
                'label'      => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .skillbar-group .skill-bar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

        $this->end_controls_section();


        // Track Styles
        $this->start_controls_section(
            'ekit_progressbar_track_style', [
                'label'  =>esc_html__( 'Track', 'elementskit-lite' ),
                'tab'    => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'ekit_progressbar_track_color',
                'label'     => esc_html__( 'Track Color', 'elementskit-lite' ),
                'types'     => [ 'classic', 'gradient' ],

                'condition' => [
                    'ekit_progressbar_style!' => ['style-stripe skill-medium tooltip-style'],
                ],
                'selector'  => '{{WRAPPER}} .skillbar-group .skill-track',
            ]
        );
        //ekit_progressbar_style style-stripe skill-medium tooltip-style
        $this->add_responsive_control(
            'ekit_progressbar_strip_color', [
                'label'      => esc_html__( 'Stripe Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'condition' => [
                    'ekit_progressbar_style' => ['style-stripe skill-medium tooltip-style'],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .style-stripe .single-skill-bar .skill-track' => 'background: repeating-linear-gradient(to right, {{VALUE}}, {{VALUE}} 4px, #FFFFFF 4px, #FFFFFF 8px);',
                ],

            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_switch_color', [
                'label'      => esc_html__( 'Switch Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'condition' => [
                    'ekit_progressbar_style' => ['style-switch'],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .skillbar-group .single-skill-bar .skill-track:before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .skillbar-group .single-skill-bar .skill-track:after' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_progressbar_track_shadow',
                'label' => esc_html__( 'Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .skillbar-group .skill-track',
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_track_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .skillbar-group .skill-track' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        // Title Styles
        $this->start_controls_section(
            'ekit_progressbar_title_style', [
                'label' =>esc_html__( 'Title', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_title_color', [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .skillbar-group .skill-title' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_progressbar_title_typography',
                'selector'   => '{{WRAPPER}} .skillbar-group .skill-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(), [
                'name'       => 'ekit_progressbar_title_shadow',
                'selector'   => '{{WRAPPER}} .skillbar-group .skill-title',
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_margin_bottom',
            [
                'type'          => Controls_Manager::SLIDER,
                'label'         => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'size_units'    => ['px'],
                'range'  => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .skill-bar-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        // Percent Styles
        $this->start_controls_section(
            'ekit_progressbar_percent_style', [
                'label' =>esc_html__( 'Percent', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_percent_color', [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .skillbar-group .number-percentage-wraper' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'       => 'ekit_progressbar_percent_typography',
                'selector'   => '{{WRAPPER}} .skillbar-group .number-percentage-wraper',
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_percent_tooltip_bg', [
                'label'      => esc_html__( 'Background Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'condition' => [
                    'ekit_progressbar_style' => ['tooltip-style', 'style-stripe skill-medium tooltip-style'],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .skillbar-group .single-skill-bar .svg-content > svg' => 'fill: {{VALUE}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_percent_pin_bg', [
                'label'      => esc_html__( 'Background Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'condition' => [
                    'ekit_progressbar_style' => ['style-ribbon', 'pin-style', 'tooltip-style2', 'tooltip-style3'],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .skillbar-group .single-skill-bar .number-percentage-wraper,
                    {{WRAPPER}} .skillbar-group.pin-style .single-skill-bar .number-percentage-wraper:before' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .skillbar-group .single-skill-bar .number-percentage-wraper:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(), [
                'name'       => 'ekit_progressbar_percent_shadow',
                'selector'   => '{{WRAPPER}} .skillbar-group .number-percentage-wraper',
            ]
        );

        $this->end_controls_section();

         // Icon Styles
         $this->start_controls_section(
            'ekit_progressbar_icon_style', [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_progressbar_style!' => '',
                    'ekit_progressbar_style' => 'inner-content skill-big'
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_progressbar_icon_color', [
                'label'      => esc_html__( 'Color', 'elementskit-lite' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .skillbar-group .skill-track > span i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .skillbar-group .skill-track > span svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_progressbar_icon_typography',
            [
                'type'          => Controls_Manager::SLIDER,
                'label'         => esc_html__( 'Icon Size', 'elementskit-lite' ),
                'size_units'    => ['px', 'em'],
                'range'  => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 200,
                    ],
                ],
                'default' => ['unit' => 'px', 'size' => '15'],
                'selectors' => [
                    '{{WRAPPER}} .skillbar-group .skill-track > span i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .skillbar-group .skill-track > span svg'   => 'max-width: {{SIZE}}{{UNIT}};',
                ],
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

        ?>
        <div class="waypoint-tigger">
            <div class="skillbar-group <?php echo esc_attr( $ekit_progressbar_style ); ?>" data-progress-bar="">
                <div class="single-skill-bar">
                    <?php if ( 'style-switch' != $ekit_progressbar_style ): ?>
                        <div class="skill-bar-content">
                            <span class="skill-title"><?php echo esc_html( $ekit_progressbar_title ); ?></span>
                        </div><!-- .skill-bar-content END -->
                        <div class="skill-bar">
                            <div class="skill-track">
                                <?php if ( 'inner-content skill-big' == $ekit_progressbar_style ):
                                    
                                    // new icon
                                    $migrated = isset( $settings['__fa4_migrated']['ekit_progressbar_icons'] );
                                    // Check if its a new widget without previously selected icon using the old Icon control
                                    $is_new = empty( $settings['ekit_progressbar_icon'] );
                                    ?>
                                    
                                    <span class="skill-track-icon" >
                                        <?php
                                            // new icon
											$migrated = isset( $settings['__fa4_migrated']['ekit_progressbar_icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $settings['ekit_progressbar_icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $settings['ekit_progressbar_icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($settings['ekit_progressbar_icon']); ?>" aria-hidden="true"></i>
												<?php
											}
                                        ?>
                                    </span>
                                <?php endif; ?>

                                <div class="number-percentage-wraper">
                                    <span class="number-percentage" data-value="<?php echo esc_attr( $ekit_progressbar_percentage ); ?>" data-animation-duration="<?php echo esc_attr( $ekit_progressbar_data_duration['size'] ); ?>">0</span>%

                                    <?php if ( 'tooltip-style' == $ekit_progressbar_style ): ?>
                                        <div class="svg-content">
                                            <svg version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink" preserveAspectRatio="none" viewBox="0 0 116 79.6"> <g> <path d="M0,18.3v21.3C0,49.8,8.2,58,18.3,58h5.9c7.8,0,15.3,3.1,20.8,8.6l13,13l13-13c5.5-5.5,13-8.6,20.8-8.6h5.9 c10.1,0,18.3-8.2,18.3-18.3V18.3C116,8.2,107.8,0,97.7,0H18.3C8.2,0,0,8.2,0,18.3z"/></g></svg>
                                        </div>
                                    <?php elseif( 'style-stripe skill-medium tooltip-style' == $ekit_progressbar_style ): ?>
                                        <div class="svg-content">
                                            <svg version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink" preserveAspectRatio="none" viewBox="0 0 116 79.6"> <g> <path d="M0,18.3v21.3C0,49.8,8.2,58,18.3,58h5.9c7.8,0,15.3,3.1,20.8,8.6l13,13l13-13c5.5-5.5,13-8.6,20.8-8.6h5.9 c10.1,0,18.3-8.2,18.3-18.3V18.3C116,8.2,107.8,0,97.7,0H18.3C8.2,0,0,8.2,0,18.3z"/></g></svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div><!-- .skill-bar END -->
                    <?php else: ?>
                        <div class="content-group">
                            <div class="skill-bar-content">
                                <span class="skill-title"><?php echo esc_html( $ekit_progressbar_title ); ?></span>
                            </div><!-- .skill-bar-content END -->
                            <div class="skill-bar">
                                <div class="skill-track"></div>
                            </div><!-- .skill-bar END -->
                        </div>
                        <span class="number-percentage-wraper">
                            <span class="number-percentage" data-value="<?php echo esc_attr( $ekit_progressbar_percentage ); ?>" data-animation-duration="<?php echo esc_attr( $ekit_progressbar_data_duration['size'] ); ?>">0</span>%
                        </span>
                    <?php endif; ?>
                </div><!-- .single-skill-bar END -->
            </div><!-- .skillbar-group END -->
        </div>
        <?php
    }
}