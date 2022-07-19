<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Header_Offcanvas_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;
use \ElementsKit_Lite\Modules\Controls\Widget_Area_Utils as Widget_Area_Utils;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Header_Offcanvas extends Widget_Base
{
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
        return '';
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'ekit_header_search',
            [
                'label' => esc_html__('Header Offcanvas', 'elementskit-lite'),
            ]
        );

        $this->add_control(
            'ekit_offcanvas_content', [
                'label' => esc_html__('Content', 'elementskit-lite'),
                'type' => ElementsKit_Controls_Manager::WIDGETAREA,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'ekit__offcanvas_seacrh_overlay_bg_color',
            [
                'label' =>esc_html__( 'Overlay color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-bg-black' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Hamburger and Close tabs
        $this->start_controls_tabs('ekit_offcanvas_hamburber_close_tabs');
            // Hamburger tab
            $this->start_controls_tab(
				'ekit_offcanvas_hamburger_tab',
				[
					'label' => esc_html__( 'Hamburger', 'elementskit-lite' ),
				]
			);

            $this->add_control(
                'ekit_offcanvas_menu_type',
                [
                    'label' => esc_html__('Menu Type:', 'elementskit-lite'),
                    'type' => Controls_Manager::SELECT,
                    'default'   => 'icon',
                    'options'   => [
                        'icon'  => esc_html__('Icon', 'elementskit-lite'),
                        'text'  => esc_html__('Text', 'elementskit-lite'),
                        'icon_with_text'  => esc_html__('Icon with Text', 'elementskit-lite'),
                    ],    
                ]
            );

            $this->add_control(
                'ekit_offcanvas_menu_icons',
                [
                    'label' => esc_html__('Icon', 'elementskit-lite'),
                    'label_block' => true,
                    'type' => Controls_Manager::ICONS,
                    'fa4compatibility' => 'ekit_offcanvas_menu_icon',
                    'default' => [
                        'value' => 'icon icon-burger-menu',
                        'library' => 'ekiticons',
                    ],    
                    'condition' => [
                        'ekit_offcanvas_menu_type' => [ 'icon', 'icon_with_text' ],
                    ]
                ]
            );

            $this->add_control(
                'ekit_offcanvas_menu_icons_position',
                [
                    'label' => esc_html__('Icon Positioin', 'elementskit-lite'),
                    'type' => Controls_Manager::SELECT,
                    'default'   => 'before',
                    'options'   => [
                        'before'  => esc_html__('Before', 'elementskit-lite'),
                        'after'  => esc_html__('After', 'elementskit-lite'),
                    ],  
                    'condition' => [
                        'ekit_offcanvas_menu_type' => 'icon_with_text',
                    ]
                ]
                
            );

            $this->add_control(
                'ekit_offcanvas_menu_text',
                [
                    'label' => esc_html__('Text', 'elementskit-lite'),
                    'label_block' => true,
                    'type' => Controls_Manager::TEXT,
                    'condition' => [
                        'ekit_offcanvas_menu_type' => [ 'text', 'icon_with_text' ],
                    ] 
                ]
            );

            $this->end_controls_tab();


            // Close
            $this->start_controls_tab(
                'ekit_offcanvas_close_tab',
                [
                    'label' => esc_html__( 'Closed', 'elementskit-lite' )
                ]
            );

            $this->add_control(
                'ekit_offcanvas_menu_close_icons',
                [
                    'label' => esc_html__('Close Icon', 'elementskit-lite'),
                    'label_block' => true,
                    'type' => Controls_Manager::ICONS,
                    'fa4compatibility' => 'ekit_offcanvas_menu_close_icon',
                    'default' => [
                        'value' => 'fas fa-times',
                        'library' => 'fa-solid',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tabs

        $this->end_controls_section();


        $this->start_controls_section(
            'ekit_header_offcanvas_section_tab_style',
            [
                'label' => esc_html__('Offcanvas', 'elementskit-lite'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('ekit_header_offcanvas_style_tabs');
            $this->start_controls_tab(
                'ekit_header_offcanvas_style_hamburger_tab',
                [
                    'label' => esc_html__( 'Hamburger', 'elementskit-lite' )
                ]
            );

            $this->add_responsive_control(
                'ekit_offcanvas_icon_color',
                [
                    'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#333',
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navSidebar-button' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .ekit_navSidebar-button svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                    ],
                ]
            );
    
            $this->add_responsive_control(
                'ekit_offcanvas_icon_bg_color',
                [
                    'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navSidebar-button' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ekit_offcanvas_icon_hover_title',
                [
                    'label' => __( 'Hover', 'elementskit-lite' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'ekit_offcanvas_icon_color_hover',
                [
                    'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navSidebar-button:hover' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .ekit_navSidebar-button:hover svg path'  => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                    ],
                ]
            );
    
            $this->add_responsive_control(
                'ekit_offcanvas_icon_bg_color_hover',
                [
                    'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navSidebar-button:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'ekit_offcanvas_icon_border_color_hover',
                [
                    'label' => esc_html__( 'Border Color', 'elementskit-lite' ),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navSidebar-button:hover' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ekit_offcanvas_text_font_hr',
                [
                    'type'  => Controls_Manager::DIVIDER,
                ]
            );    
    
            $this->add_responsive_control(
                'ekit_offcanvas_icon_font_size',
                [
                    'label'         => esc_html__('Icon Size', 'elementskit-lite'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', 'em'],
                    'default' => [
                        'unit' => 'px',
                        'size' => '20',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navSidebar-button i' => 'font-size: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .ekit_navSidebar-button svg'   => 'max-width: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'ekit_offcanvas_menu_type' => [ 'text', 'icon_with_text' ],
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'     => 'ekit_offcanvas_text_typo',
                    'label'    => esc_html__('Text Typography', 'elementskit-lite'),
                    'selector' => '{{WRAPPER}} .ekit_navSidebar-button',
    
                    'fields_options' => [
                        'typography'  => [
                            'default' => 'custom',
                        ],
                        'font_weight' => [
                            'default' => '400',
                        ],
                        'font_size'     => [
                            'label'     => esc_html__('Font Size (px)', 'elementskit-lite'),
                            'responsive' => false,
                            'size_units' => ['px']
                        ],
                    ],
                    'condition' => [
                        'ekit_offcanvas_menu_type' => [ 'text', 'icon_with_text' ],
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'ekit_border',
                    'selector' => '{{WRAPPER}} .ekit_navSidebar-button',
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'ekit_offcanvas_humburger_text_align',
                [
                    'label' => __( 'Alignment', 'elementskit-lite' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __( 'Left', 'elementskit-lite' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => __( 'Center', 'elementskit-lite' ),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => __( 'Right', 'elementskit-lite' ),
                            'icon' => 'eicon-text-align-right',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit-offcanvas-toggle-wraper' => 'text-align: {{VALUE}}',
                    ],
                    'toggle' => true,
                ]
            );

            // box shadow
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
                    'name'       => 'ekit_header_search',
                    'selector'   => '{{WRAPPER}} .ekit_navSidebar-button',

                ]
            );
            // border radius
            $this->add_control(
                'ekit_offcanvas_border_radius',
                [
                    'label' => esc_html__( 'Border radius', 'elementskit-lite' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'default' => [
                        'top' => '',
                        'right' => '',
                        'bottom' => '' ,
                        'left' => '',
                        'unit' => '%',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navSidebar-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'ekit_offcanvas_padding',
                [
                    'label'         => esc_html__('Padding', 'elementskit-lite'),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => ['px', 'em'],
                    'default' => [
                        'top' => '4',
                        'right' => '7',
                        'bottom' => '5' ,
                        'left' => '7',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navSidebar-button, {{WRAPPER}} .ekit_social_media ul > li:last-child' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'ekit_offcanvas_margin',
                [
                    'label'         => esc_html__('Margin', 'elementskit-lite'),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => ['px', 'em'],
                    'default' => [
                        'top' => '',
                        'right' => '',
                        'bottom' => '' ,
                        'left' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navSidebar-button, {{WRAPPER}} .ekit_social_media ul > li:last-child' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
            
            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_header_offcanvas_style_close_tab',
                [
                    'label' => esc_html__( 'Closed', 'elementskit-lite' )
                ]
            );

            $this->add_responsive_control(
                'ekit_offcanvas_close_icon_color',
                [
                    'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#333',
                    'selectors' => [
                        '{{WRAPPER}} .ekit_close-side-widget' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .ekit_close-side-widget svg path'  => 'stroke: {{VALUE}}; fill:{{VALUE}};',
                    ],
                ]
            );
    
            $this->add_responsive_control(
                'ekit_offcanvas_close_icon_bg_color',
                [
                    'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_close-side-widget' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ekit_offcanvas_close_icon_hover_title',
                [
                    'label' => __( 'Hover', 'elementskit-lite' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'ekit_offcanvas_close_icon_color_hover',
                [
                    'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_close-side-widget:hover' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .ekit_close-side-widget:hover svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};'
                    ],
                ]
            );
    
            $this->add_responsive_control(
                'ekit_offcanvas_close_icon_bg_color_hover',
                [
                    'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_close-side-widget:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'ekit_offcanvas_close_icon_border_color_hover',
                [
                    'label' =>esc_html__( 'Border Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_close-side-widget:hover' => 'border-color: {{VALUE}};',
                    ],
                ]
            );
    
    
            $this->add_responsive_control(
                'ekit_offcanvas_close_icon_font_size',
                [
                    'label'         => esc_html__('Icon Size', 'elementskit-lite'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', 'em'],
                    'separator' => 'before',
                    'default' => [
                        'unit' => 'px',
                        'size' => '20',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit_close-side-widget' => 'font-size: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .ekit_close-side-widget svg'   => 'max-width: {{SIZE}}{{UNIT}};',
                    ],
    
                ]
            );

            $this->add_responsive_control(
                'close_btn_size',
                [
                    'label' => esc_html__('Box Size (px)', 'elementskit-lite'),
                    'type'  => Controls_Manager::SLIDER,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_close-side-widget' => 'width: {{SIZE}}px; height: {{SIZE}}px; line-height: calc({{SIZE}}px - 4px);',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'ekit_close_border',
                    'selector' => '{{WRAPPER}} .ekit_close-side-widget',
                    'separator' => 'before',
                ]
            );

            // box shadow
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
                    'name'       => 'ekit_header_search_close',
                    'selector'   => '{{WRAPPER}} .ekit_close-side-widget',

                ]
            );
            // border radius
            $this->add_control(
                'ekit_offcanvas_close_border_radius',
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
                        '{{WRAPPER}} .ekit_close-side-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'ekit_offcanvas_close_padding',
                [
                    'label'         => esc_html__('Padding', 'elementskit-lite'),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => ['px', 'em'],
                    'default' => [
                        'top' => '4',
                        'right' => '7',
                        'bottom' => '5' ,
                        'left' => '7',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit_close-side-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'ekit_offcanvas_close_margin',
                [
                    'label'         => esc_html__('Margin', 'elementskit-lite'),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => ['px', 'em'],
                    'default' => [
                        'top' => '',
                        'right' => '',
                        'bottom' => '' ,
                        'left' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit_close-side-widget' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_tab();
        $this->end_controls_tabs();

        
        
        $this->end_controls_section();

        $this->start_controls_section(
			'ekit_offcanvas_panel_style_tab',
			[
				'label' => __( 'Offcanvas Panel', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'eit_offcanvas_width',
			[
				'label' => __( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-sidebar-widget' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_offcanvas_background',
				'label' => __( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-sidebar-widget',
			]
		);

        // Position
        $this->add_control(
            'ekit_offcanvas_position',
            [
                'label'         => esc_html__( 'Position', 'elementskit-lite' ),
                'type'          => Controls_Manager::CHOOSE,
                'options'       => [
                    'left'  => [
                        'title' => esc_html__( 'Left', 'elementskit-lite' ),
                        'icon'  => 'eicon-chevron-left',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'elementskit-lite' ),
                        'icon'  => 'eicon-chevron-right',
                    ],
                ],
                'default'       => 'right',
                'toggle'        => false,
                'prefix_class'  => 'ekit-off-canvas-position-',
            ]
        );

        // Padding
        $this->add_control(
            'ekit_offcanvas_panel_padding',
            [
                'label'         => esc_html__('Padding', 'elementskit-lite'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px', 'em'],
                'default' => [
                    'top' => '',
                    'right' => '',
                    'bottom' => '' ,
                    'left' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit_sidebar-textwidget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        $settings = $this->get_settings();


        ?>
        <div class="ekit-offcanvas-toggle-wraper">
            <a href="#" class="ekit_navSidebar-button ekit_offcanvas-sidebar">
                <?php
                    if($settings['ekit_offcanvas_menu_type'] == 'icon') {
                        // new icon
                        $migrated = isset( $settings['__fa4_migrated']['ekit_offcanvas_menu_icons'] );
                        // Check if its a new widget without previously selected icon using the old Icon control
                        $is_new = empty( $settings['ekit_offcanvas_menu_icon'] );
                        if ( $is_new || $migrated ) {
                            // new icon
                            Icons_Manager::render_icon( $settings['ekit_offcanvas_menu_icons'], [ 'aria-hidden' => 'true' ] );
                        } else {
                            ?>
                            <i class="<?php echo esc_attr($settings['ekit_offcanvas_menu_icon']); ?>" aria-hidden="true"></i>
                            <?php
                        }
                    } elseif($settings['ekit_offcanvas_menu_type'] == 'icon_with_text') {
                         // new icon
                         $migrated = isset( $settings['__fa4_migrated']['ekit_offcanvas_menu_icons'] );
                         // Check if its a new widget without previously selected icon using the old Icon control
                         $is_new = empty( $settings['ekit_offcanvas_menu_icon'] );
                         if($settings['ekit_offcanvas_menu_icons_position'] == 'before') {
                            if ( $is_new || $migrated ) {
                                // new icon
                                Icons_Manager::render_icon( $settings['ekit_offcanvas_menu_icons'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($settings['ekit_offcanvas_menu_icon']); ?>" aria-hidden="true"></i>
                                <?php
                            }
                            echo ' '.esc_html($settings['ekit_offcanvas_menu_text']);
                         } else {
                            echo esc_html($settings['ekit_offcanvas_menu_text']).' ';
                            if ( $is_new || $migrated ) {
                                // new icon
                                Icons_Manager::render_icon( $settings['ekit_offcanvas_menu_icons'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($settings['ekit_offcanvas_menu_icon']); ?>" aria-hidden="true"></i>
                                <?php
                            }
                         }
                         
                    } elseif($settings['ekit_offcanvas_menu_type'] == 'text') {
                        echo esc_html($settings['ekit_offcanvas_menu_text']);
                    }
                ?>
            </a>
        </div>
        <!-- offset cart strart -->
        <!-- sidebar cart item -->
        <div class="ekit-sidebar-group info-group">
            <div class="ekit-overlay ekit-bg-black"></div>
            <div class="ekit-sidebar-widget">
                <div class="ekit_sidebar-widget-container">
                    <div class="ekit_widget-heading">
                        <a href="#" class="ekit_close-side-widget">

                            <?php
                                // new icon
                                $migrated = isset( $settings['__fa4_migrated']['ekit_offcanvas_menu_close_icons'] );
                                // Check if its a new widget without previously selected icon using the old Icon control
                                $is_new = empty( $settings['ekit_offcanvas_menu_close_icon'] );
                                if ( $is_new || $migrated ) {
                                    // new icon
                                    Icons_Manager::render_icon( $settings['ekit_offcanvas_menu_close_icons'], [ 'aria-hidden' => 'true' ] );
                                } else {
                                    ?>
                                    <i class="<?php echo esc_attr($settings['ekit_offcanvas_menu_close_icon']); ?>" aria-hidden="true"></i>
                                    <?php
                                }
                            ?>

                        </a>
                    </div>
                    <div class="ekit_sidebar-textwidget">
                        <?php echo Widget_Area_Utils::parse( $settings['ekit_offcanvas_content'], $this->get_id(), 99 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Displaying with Elementor content rendering ?> 
                    </div>
                </div>
            </div>
        </div> <!-- END sidebar widget item -->
        <!-- END offset cart strart -->
        <?php
    }
}
