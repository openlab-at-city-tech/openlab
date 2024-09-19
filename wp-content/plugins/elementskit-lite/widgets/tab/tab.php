<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Tab_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Tab extends Widget_Base {
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

    public function get_keywords() {
        return Handler::get_keywords();
    }

    public function get_help_url() {
        return 'https://wpmet.com/doc/tab/';
    }
    protected function is_dynamic_content(): bool {
        return false;
    }
    protected function register_controls() {
        $this->start_controls_section(
            'section_tab', [
                'label' =>esc_html__( 'Tab', 'elementskit-lite' ),
            ]
        );
        $this->add_control(
            'ekit_tab_style',
            [
                'label' =>esc_html__( 'Style', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' =>esc_html__( 'Horizontal', 'elementskit-lite' ),
                    'vertical' =>esc_html__( 'Vertical', 'elementskit-lite' ),
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_nav_width',
            [
                'label' => esc_html__( 'Vertical Nav Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', '' ],
                'default' => [
                    'size' => 30,
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-wraper.vertical .elementkit-tab-nav' => 'flex-basis: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_style' => 'vertical',
                ],
            ]
        );

        $this->add_control(
            'ekit_tab_caret_style_choose',
            [
                'label' => esc_html__( 'Show Caret', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
                'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'ekit_tab_caret_style',
            [
                'label' => esc_html__('Choose Style', 'elementskit-lite'),
                'type' => ElementsKit_Controls_Manager::IMAGECHOOSE,
                'default' => 'elementskit_tab_border_bottm',
                'options' => [
                    'elementskit_tab_border_bottm' => [
                        'title' => esc_html__( 'image style 1', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/tab-01.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/tab-01.png',
                        'width' => '33.333333333333333%',
                    ],
                    'elementskit_tooltip_style' => [
                        'title' => esc_html__( 'image style 2', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/tab-02.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/tab-02.png',
                        'width' => '33.333333333333333%',
                    ],
                    'elementskit_heartbit_style' => [
                        'title' => esc_html__( 'image style 3', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/tab-03.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/tab-03.png',
                        'width' => '33.333333333333333%',
                    ],
                    'elementskit_pregress_style' => [
                        'title' => esc_html__( 'image style 4', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/tab-04.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/tab-04.png',
                        'width' => '33.333333333333333%',
                    ],
                    'elementskit_ribbon_style' => [
                        'title' => esc_html__( 'image style 5', 'elementskit-lite' ),
                        'imagelarge' => Handler::get_url() . 'assets/imagechoose/tab-05.png',
                        'imagesmall' => Handler::get_url() . 'assets/imagechoose/tab-05.png',
                        'width' => '33.333333333333333%',
                    ],
                ],
                'condition' => [
                    'ekit_tab_caret_style_choose' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ekit_tab_fill_full_width',
            [
                'label' => esc_html__( 'Full Width Nav', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
                'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'ekit_tab_style' => 'horizontal'
                ]
            ]
        );

        $this->add_control(
            'ekit_tab_header_icon_pos_style',
            [
                'label' => esc_html__( 'Nav Icon Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'left-pos',
                'options' => [
                    'right-pos'  => esc_html__( 'Right', 'elementskit-lite' ),
                    'left-pos' => esc_html__( 'Left', 'elementskit-lite' ),
                    'top-pos' => esc_html__( 'Top', 'elementskit-lite' ),
                    'bottom-pos' => esc_html__( 'Bottom', 'elementskit-lite' ),
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_icon_margin_left',
            [
                'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-wraper .elementkit-nav-link.right-pos .elementskit-tab-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementkit-tab-wraper .elementkit-nav-link.right-pos .ekit-icon-image' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_header_icon_pos_style' => 'right-pos',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_icon_margin_right',
            [
                'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-wraper .elementkit-nav-link.left-pos .elementskit-tab-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementkit-tab-wraper .elementkit-nav-link.left-pos .ekit-icon-image' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_header_icon_pos_style' => 'left-pos',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_icon_margin_top',
            [
                'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'default' => [
                    'size' => 0,
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-wraper .elementkit-nav-link.bottom-pos .elementskit-tab-icon' => 'margin-top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementkit-tab-wraper .elementkit-nav-link.bottom-pos .ekit-icon-image' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_header_icon_pos_style' => 'bottom-pos',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_icon_margin_bottom',
            [
                'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'default' => [
                    'size' => 0,
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-wraper .elementkit-nav-link.top-pos .elementskit-tab-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementkit-tab-wraper .elementkit-nav-link.top-pos .ekit-icon-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_header_icon_pos_style' => 'top-pos',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_wraper_position',
            [
                'label' =>esc_html__( 'Nav Alignment', 'elementskit-lite' ),
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
                    '{{WRAPPER}} .elementkit-tab-wraper.elementskit-fitcontent-tab:not(.vertical)' => 'text-align: {{VALUE}};'
                ],
                'default' => 'left',
                'condition' => [
                    'ekit_tab_style' => 'horizontal',
                    'ekit_tab_fill_full_width!' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_content_position',
            [
                'label' =>esc_html__( 'Nav Item Alignment', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start'    => [
                        'title' =>esc_html__( 'Left', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' =>esc_html__( 'Center', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' =>esc_html__( 'Right', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-wraper .elementkit-nav-link' => 'justify-content: {{VALUE}};'
                ],
                'default' => 'center',
            ]
        );

        $this->add_control(
            'ekit_hash_change',
            [
                'label'                 => esc_html__( 'Enable URL Hash', 'elementskit-lite' ),
                'type'                  => Controls_Manager::SWITCHER,
                'return_value'          => '1',
                'frontend_available'    => true,
            ]
        );

        $this->add_control(
            'ekit_tab_trigger_type',
            [
                'label' => esc_html__( 'Toggle Type', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'click',
                'options' => [
                    'click' => esc_html__( 'Click', 'elementskit-lite' ),
                    'mouseenter' => esc_html__( 'Hover', 'elementskit-lite' ),
                ],

            ]
        );

        $repeater = new Repeater();
        $repeater->add_control(
            'ekit_tab_title', [
                'label' => esc_html__('Title', 'elementskit-lite'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'ekit_tab_title_is_active',
            [
                'label' => esc_html__('Keep this tab open? ', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
            ]
        );

        $repeater->add_control(
            'ekit_tab_title_icon_type', [
                'label'       => esc_html__( 'Icon Type', 'elementskit-lite' ),
                'type'        => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options'     => [
                    'none' => [
                        'title' => esc_html__( 'None', 'elementskit-lite' ),
                        'icon'  => 'fa fa-ban',
                    ],
                    'icon' => [
                        'title' => esc_html__( 'Icon', 'elementskit-lite' ),
                        'icon'  => 'fa fa-paint-brush',
                    ],
                    'image' => [
                        'title' => esc_html__( 'Image', 'elementskit-lite' ),
                        'icon'  => 'fa fa-image',
                    ],
                ],
                'default'       => 'icon',
            ]
        );
        $repeater->add_control(
            'ekit_tab_title_icons', [
                'label' => esc_html__('Title Icon', 'elementskit-lite'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'fa4compatibility' => 'ekit_tab_title_icon',
                'default' => [
                    'value' => 'icon icon-earth',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_tab_title_icon_type' => 'icon'
                ]
            ]
        );

        $repeater->add_control(
            'ekit_tab_title_image',
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
                    'ekit_tab_title_icon_type' => 'image'
                ],
            ]
        );
        $repeater->add_control(
            'ekit_tab_content', [
                'label' => esc_html__('Content', 'elementskit-lite'),
                'type' => Controls_Manager::WYSIWYG,
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'ekit_tab_hr1',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
        );
        $repeater->add_responsive_control(
            'ekit_tab_title_border_radius_group',
            [
                'label' => esc_html__( 'Title Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav {{CURRENT_ITEM}} .elementkit-nav-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'ekit_tab_items',
            [
                'label' => esc_html__('Tab content', 'elementskit-lite'),
                'type' => Controls_Manager::REPEATER,
                'separator' => 'before',
                'title_field' => '{{ ekit_tab_title }}',
                'default' => [
                    [
                        'ekit_tab_title' => ' WordPress',
                        'ekit_tab_content' => 'Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast',
                    ],
                    [
                        'ekit_tab_title' => ' Prestashop',
                        'ekit_tab_content' => 'Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast',
                    ],
                    [
                        'ekit_tab_title' => ' Joomla!',
                        'ekit_tab_content' => 'Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast',
                    ],
                ],
                'fields' => $repeater->get_controls(),
            ]
        );
        //tab schema
        $this->add_control(
            'ekit_tab_schema',
            [
                'label' => esc_html__( 'TAB Schema', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'separator' => 'before',
            ]
        );
        $this->end_controls_section();

        //  Wrapper Control

        $this->start_controls_section(
            'ekit_tab_section_wrapper_style', [
                'label'	 =>esc_html__( 'Wrapper', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_wrapper_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-wraper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'ekit_tab_wrapper_bg_group',
                'selector' => '{{WRAPPER}} .elementkit-tab-wraper',
            )
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_tab_wrapper_border_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementkit-tab-wraper',
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_wrapper_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-wraper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_tab_wrapper_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementkit-tab-wraper',
            ]
        );

        $this->end_controls_section();
        
        // Header setting
        $this->start_controls_section(
            'ekit_tab_header_section_setting', [
                'label' =>esc_html__( 'Nav Wrapper  ', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ekit_tab_nav_wrapper_width',
            [
                'label' => esc_html__( 'Make Fluid', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );


        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_tab_nav_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementkit-tab-nav',
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_nav_header_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default'    => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_nav_header_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default'    => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_tab_nav_border_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementkit-tab-nav',

            ]
        );
        $this->add_responsive_control(
            'ekit_tab_nav_border_radius_group',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_tab_nav_header_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementkit-tab-nav',
            ]
        );
        $this->end_controls_section();

        // Header Items
        $this->start_controls_section(
            'ekit_tab_nav_items_section_setting', [
                'label' =>esc_html__( 'Nav Items  ', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_tab_header_title_typography_group',
                'label' =>esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-item .elementkit-nav-link',
            ]
        );

        $this->add_responsive_control(
            'ekit_simple_tab_title_icon_size',
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
                    '{{WRAPPER}} .elementkit-tab-wraper .elementkit-nav-link .elementskit-tab-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementkit-tab-wraper .elementkit-nav-link .elementskit-tab-icon svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_spacing_right',
            [
                'label' => esc_html__( 'Margin Right', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-wraper:not(.vertical) .elementkit-nav-item:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '.rtl {{WRAPPER}} .elementkit-tab-wraper:not(.vertical) .elementkit-nav-item:not(:last-child)' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',
                    '{{WRAPPER}} .elementkit-tab-wraper.vertical .elementkit-tab-nav' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_header_spacing_bottom',
            [
                'label' => esc_html__( 'Margin Bottom', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-wraper.vertical .elementkit-nav-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementkit-tab-wraper:not(.vertical) .elementkit-tab-nav' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_nav_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default'    => [
                    'top' => '14',
                    'right' => '35',
                    'bottom' => '14',
                    'left' => '35',
                    'unit' => 'px',
                    'isLinked' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->start_controls_tabs(
            'ekit_tab_header_style_tabs_normal'
        );

        $this->start_controls_tab(
            'style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );
        $this->add_control(
            'ekit_tab_title_color',
            [
                'label' =>esc_html__( 'Title Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#2575fc',
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_tab_icon_color',
            [
                'label' =>esc_html__( 'Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link span.elementskit-tab-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link span.elementskit-tab-icon path'   => 'stroke: {{VALUE}}; fill: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_tab_title_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_tab_title_border_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link',
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width' => [
                        'default' => [
                            'top' => '1',
                            'right' => '1',
                            'bottom' => '1',
                            'left' => '1',
                            'isLinked' => false,
                        ],
                    ],
                    'color' => [
                        'default' => '#2575fc',
                    ],
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_tab_tab_title_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-item .elementkit-nav-link',
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_tab_header_style_tabs_active',
            [
                'label' => esc_html__( 'Active', 'elementskit-lite' ),
            ]
        );
        $this->add_control(
            'ekit_tab_active_title_color',
            [
                'label' =>esc_html__( 'Title Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000',
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link.active' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'ekit_tab_icon_color_active',
            [
                'label' =>esc_html__( 'Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link.active span.elementskit-tab-icon' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link.active span.elementskit-tab-icon path'    => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_tab_title_active_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link.active',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_tab_title_border_active_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link.active',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_tab_tab_title_box_shadow_active_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-link.active',
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'ekit_tab_nav_item_border_radious',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-item a.elementkit-nav-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        //  First Child design

        $this->add_responsive_control(
            'ekit_nav_item_first_child',
            [
                'label' => esc_html__( 'First and Last Child Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_nav_item_first_child_border_radious',
            [
                'label' => esc_html__( 'First Child Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-item:first-child a.elementkit-nav-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_nav_item_first_child_border',
            [
                'label' => esc_html__( 'First Child Border Width', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px'],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-item:first-child a.elementkit-nav-link' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_nav_item_last_child_border_radious',
            [
                'label' => esc_html__( 'Last Child Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-item:last-child a.elementkit-nav-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_nav_item_last_child_border',
            [
                'label' => esc_html__( 'Last Child Border Width', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px'],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav .elementkit-nav-item:last-child a.elementkit-nav-link' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );


        $this->end_controls_section();


        // Caret setting
        $this->start_controls_section(
            'ekit_tab_header_caret_section_setting', [
                'label' =>esc_html__( 'Caret  ', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_tab_caret_style_choose' => 'yes'
                ]
            ]
        );

        // elementskit_tab_border_bottm
        $this->add_responsive_control(
            'ekit_tab_header_caret_tab_border_bottm_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
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
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_tab_border_bottm .elementkit-nav-item .elementkit-nav-link::before' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_tab_border_bottm',]
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_tab_border_bottm_height',
            [
                'label' => esc_html__( 'Height', 'elementskit-lite' ),
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
                    'unit' => '%',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_tab_border_bottm .elementkit-nav-item .elementkit-nav-link::before' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_tab_border_bottm']
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_tab_header_caret_tab_border_bottm_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient', 'video' ],
                'selector' => '{{WRAPPER}} .elementkit-tab-nav.elementskit_tab_border_bottm .elementkit-nav-item .elementkit-nav-link::before',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_tab_border_bottm']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_tab_border_bottm_bottom',
            [
                'label' => esc_html__( 'Bottom Icon Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit_tab_border_bottm.elementkit-tab-nav .elementkit-nav-item .elementkit-nav-link::before' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_tab_border_bottm']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_tab_border_bottm_left',
            [
                'label' => esc_html__( 'Left', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_tab_border_bottm .elementkit-nav-item .elementkit-nav-link::before' => 'left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_tab_border_bottm']
                ]
            ]
        );

        // elementskit_tooltip_style
        $this->add_responsive_control(
            'ekit_tab_header_caret_tooltip_style_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
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
                    'size' => 24,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit_tooltip_style.elementkit-tab-nav .elementkit-nav-item .elementkit-nav-link::before' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_tooltip_style',]
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_tooltip_style_height',
            [
                'label' => esc_html__( 'Height', 'elementskit-lite' ),
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
                    'size' => 24,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit_tooltip_style.elementkit-tab-nav .elementkit-nav-item .elementkit-nav-link::before' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_tooltip_style']
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_tab_header_caret_tooltip_style_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit_tooltip_style.elementkit-tab-nav .elementkit-nav-item .elementkit-nav-link::before',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_tooltip_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_tooltip_style_bottom',
            [
                'label' => esc_html__( 'Bottom', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -12,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit_tooltip_style.elementkit-tab-nav .elementkit-nav-item .elementkit-nav-link::before' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_tooltip_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_tooltip_style_left',
            [
                'label' => esc_html__( 'Left', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_tooltip_style .elementkit-nav-item .elementkit-nav-link::before' => 'left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_tooltip_style']
                ]
            ]
        );

        // elementskit_heartbit_style
        $this->add_responsive_control(
            'ekit_tab_header_caret_heartbit_style_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 70,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_heartbit_style .elementkit-nav-item .elementkit-nav-link::before' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_heartbit_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_heartbit_style_height',
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
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_heartbit_style .elementkit-nav-item .elementkit-nav-link::before' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_heartbit_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_bottom_heartbit_style_line',
            [
                'label' => esc_html__( 'Bottom Line', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1
                    ],
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_heartbit_style .elementkit-nav-item .elementkit-nav-link::before' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_heartbit_style']
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_tab_header_caret_heartbit_style_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementkit-tab-nav.elementskit_heartbit_style .elementkit-nav-item .elementkit-nav-link::before',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_heartbit_style']
                ]
            ]
        );

        $this->add_control(
            'ekit_tab_header_caret_heartbit_style_hr',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_heartbit_style']
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_tab_header_caret_background_heartbit_style_heart_symbol',
                'label' => esc_html__( 'Hear Symbol Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementkit-tab-nav.elementskit_heartbit_style .elementkit-nav-item .elementkit-nav-link::after',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_heartbit_style',]
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_tab_header_caret_heartbit_style_border_heart_symbol',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementkit-tab-nav.elementskit_heartbit_style .elementkit-nav-item .elementkit-nav-link::after',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_heartbit_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_heartbit_style_heartbeat_width',
            [
                'label' => esc_html__( 'Caret Width', 'elementskit-lite' ),
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
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_heartbit_style .elementkit-nav-item .elementkit-nav-link::after' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_heartbit_style',]
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_heartbit_style_heartbeat_height',
            [
                'label' => esc_html__( 'Caret Height', 'elementskit-lite' ),
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
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_heartbit_style .elementkit-nav-item .elementkit-nav-link::after' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_heartbit_style',]
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_heartbit_style_bottom',
            [
                'label' => esc_html__( 'Bottom Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_heartbit_style .elementkit-nav-item .elementkit-nav-link::after' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_heartbit_style',]
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_heartbit_style_left',
            [
                'label' => esc_html__( 'Left Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_heartbit_style .elementkit-nav-item .elementkit-nav-link::after' => 'left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_heartbit_style',]
                ]
            ]
        );

        // elementskit_pregress_style
        $this->add_responsive_control(
            'ekit_tab_header_caret_pregress_style_line_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_pregress_style .elementkit-nav-item .elementkit-nav-link::before' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_pregress_style_line_height',
            [
                'label' => esc_html__( 'Height', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit_pregress_style.elementkit-tab-nav .elementkit-nav-item .elementkit-nav-link::before' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_pregress_style_bottom_line',
            [
                'label' => esc_html__( 'Bottom', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1
                    ],
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_pregress_style .elementkit-nav-item .elementkit-nav-link::before' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_pregress_style_left_line',
            [
                'label' => esc_html__( 'Left', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1
                    ],
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_pregress_style .elementkit-nav-item .elementkit-nav-link::before' => 'left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_tab_header_caret_pregress_style_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementkit-tab-nav.elementskit_pregress_style .elementkit-nav-item .elementkit-nav-link::before',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_control(
            'ekit_tab_header_caret_background_heart_pregress_style_heading',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_tab_header_caret_background_pregress_style_symbol',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'default' => '#ffffff',
                'selector' => '{{WRAPPER}} .elementkit-tab-nav.elementskit_pregress_style .elementkit-nav-item .elementkit-nav-link::after',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_tab_header_caret_border_pregress_style_symbol',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '.elementkit-tab-nav.elementskit_pregress_style .elementkit-nav-item .elementkit-nav-link::after',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_pregress_style_width',
            [
                'label' => esc_html__( 'Caret Width', 'elementskit-lite' ),
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
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_pregress_style .elementkit-nav-item .elementkit-nav-link::after' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_pregress_style_height',
            [
                'label' => esc_html__( 'Caret Height', 'elementskit-lite' ),
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
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_pregress_style .elementkit-nav-item .elementkit-nav-link::after' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_pregress_style_bottom',
            [
                'label' => esc_html__( 'Bottom Icon Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_pregress_style .elementkit-nav-item .elementkit-nav-link::after' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_pregress_style_left',
            [
                'label' => esc_html__( 'Left Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_pregress_style .elementkit-nav-item .elementkit-nav-link::after' => 'left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_border_pregress_style_radius',
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
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_pregress_style .elementkit-nav-item .elementkit-nav-link::after' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_pregress_style']
                ]
            ]
        );

        // elementskit_ribbon_style
        $this->add_responsive_control(
            'ekit_tab_header_caret_ribbon_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_ribbon_style .elementkit-nav-item .elementkit-nav-link::before' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_ribbon_style',]
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_ribbon_height',
            [
                'label' => esc_html__( 'Height', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_ribbon_style .elementkit-nav-item .elementkit-nav-link::before' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_ribbon_style']
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_tab_header_caret_ribbon_style_background',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementkit-tab-nav.elementskit_ribbon_style .elementkit-nav-item .elementkit-nav-link::before',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_ribbon_style']
                ]
            ]
        );

        $this->add_control(
            'ekit_tab_header_caret_background_ribbon_style_heading',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_ribbon_style']
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_tab_header_caret_background_heart_symbol',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementkit-tab-nav.elementskit_ribbon_style .elementkit-nav-item .elementkit-nav-link::after',
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_ribbon_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_ribbon_style_width',
            [
                'label' => esc_html__( 'Caret Width', 'elementskit-lite' ),
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
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_ribbon_style .elementkit-nav-item .elementkit-nav-link::after' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_ribbon_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_ribbon_style_height',
            [
                'label' => esc_html__( 'Caret Height', 'elementskit-lite' ),
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
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_ribbon_style .elementkit-nav-item .elementkit-nav-link::after' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_ribbon_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_ribbon_style_bottom',
            [
                'label' => esc_html__( 'Bottom Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_ribbon_style .elementkit-nav-item .elementkit-nav-link::after' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_ribbon_style']
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_tab_header_caret_ribbon_style_left',
            [
                'label' => esc_html__( 'Left Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementkit-tab-nav.elementskit_ribbon_style .elementkit-nav-item .elementkit-nav-link::after' => 'transform:translateX(-100%);left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tab_caret_style' => ['elementskit_ribbon_style']
                ]
            ]
        );

        $this->end_controls_section();

        //Body Style Section

        $this->start_controls_section(
            'ekit_tab_section_body_style', [
                'label'	 =>esc_html__( 'Body', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'ekit_tab_body_color',
            [
                'label' => esc_html__( 'Body Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#656565',
                'selectors' => [
                    '{{WRAPPER}} .tab-content .tab-pane' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_body_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default'    => [
                    'top' => '20',
                    'right' => '0',
                    'bottom' => '20',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .tab-content .tab-pane' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'ekit_tab_body_bg_group',
                'selector' => '{{WRAPPER}} .tab-content .tab-pane',
            )
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_tab_body_content_border_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .tab-content .tab-pane',
            ]
        );
        $this->add_responsive_control(
            'ekit_tab_body_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .tab-content .tab-pane' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_tab_body_box_shadow_group',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .tab-content .tab-pane',
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

        $nav_wrapper_class = 'nav nav-tabs elementkit-tab-nav ';

        if($ekit_tab_caret_style_choose == 'yes'){
            $nav_wrapper_class .= ' '.$ekit_tab_caret_style;
        }

        if($ekit_tab_caret_style_choose == 'yes'){
            $nav_wrapper_class .= ' '.$ekit_tab_caret_style;
        }

        if($ekit_tab_fill_full_width == 'yes') {
            $nav_wrapper_class .= ' elementskit-fullwidth-tab';
        }

        if($ekit_tab_nav_wrapper_width == 'yes') {
            $nav_wrapper_class .= ' tab-nav-fluid';
        }

        $tab_id = uniqid();

        $has_user_defined_active_tab = false;
        foreach($ekit_tab_items as $tab){
            if($tab['ekit_tab_title_is_active'] == 'yes'){
                $has_user_defined_active_tab = true;
            }
        }


        ?>
        <div class="elementkit-tab-wraper <?php echo esc_attr($ekit_tab_style == 'vertical' ? 'vertical' : ''); ?> <?php if ($ekit_tab_fill_full_width != 'yes') : ?> elementskit-fitcontent-tab <?php endif; ?>">
            <ul class="<?php echo esc_attr($nav_wrapper_class); ?>">
                <?php foreach ($ekit_tab_items as $i=>$tab) :
                    $is_active = ($tab['ekit_tab_title_is_active'] == 'yes') ? ' active show' : '';
                    $is_active = ($has_user_defined_active_tab == false && $i == 0) ? ' active show' : $is_active;

                    // new icon
                    $migrated = isset( $tab['__fa4_migrated']['ekit_tab_title_icons'] );
                    // Check if its a new widget without previously selected icon using the old Icon control
                    $is_new = empty( $tab['ekit_tab_title_icon'] );

                    if($is_new || $migrated){
                        ob_start();
                            Icons_Manager::render_icon( $tab['ekit_tab_title_icons'], [ 'aria-hidden' => 'true' ] );
                        $rendered_icon = ob_get_clean();

                        $icon_html = !empty($tab['ekit_tab_title_icons']) ? ($tab['ekit_tab_title_icons']['library'] === 'svg' ? '<span class="elementskit-tab-icon">'. $rendered_icon .'</span>' : '<span class="'.  $tab['ekit_tab_title_icons']['value'] .' elementskit-tab-icon"></span>') : '';
                    } else {
                        $icon_html = '<span class="'.  $tab['ekit_tab_title_icon'] .' elementskit-tab-icon"></span>';
                    }

                   

                    $img_html = isset($tab['ekit_tab_title_icon_type']) && ($tab['ekit_tab_title_icon_type'] == 'image' && ! empty( $tab['ekit_tab_title_image']['url'] )) ?
                        '<div class="ekit-icon-image">'. \Elementskit_Lite\Utils::get_attachment_image_html($tab, 'ekit_tab_title_image', 'full', [
                            'draggable' => 'false'
                        ]) .'</div>' : '';
                    
                    // URL Hash id
                    $handler_id = (($tab['ekit_tab_title']) != '' ? strtolower(preg_replace("![^a-z0-9]+!i", "-", $tab['ekit_tab_title'])) : ('tab-'.$tab['_id']));
                    ?>
                    <li class="elementkit-nav-item elementor-repeater-item-<?php echo esc_attr( $tab[ '_id' ] ); ?>">
                        <a class="elementkit-nav-link <?php echo esc_attr($is_active);?> <?php echo esc_attr($ekit_tab_header_icon_pos_style); ?>" id="content-<?php echo esc_attr($tab['_id'].$tab_id); ?>-tab" data-ekit-handler-id="<?php echo esc_html( $handler_id ); ?>" data-ekit-toggle="tab" data-target="#content-<?php echo esc_attr($tab['_id'].$tab_id); ?>" href="#Content-<?php echo esc_attr($tab['_id'].$tab_id); ?>"
                            data-ekit-toggle-trigger="<?php echo esc_attr( $ekit_tab_trigger_type ); ?>"
                            aria-describedby="Content-<?php echo esc_attr($tab['_id'].$tab_id); ?>">
                            <?php echo wp_kses($icon_html.$img_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
                            <span class="elementskit-tab-title"> <?php $this->print_unescaped_setting( 'ekit_tab_title', 'ekit_tab_items', $i );?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
            </ul>

            <div class="tab-content elementkit-tab-content">
                <?php foreach ($ekit_tab_items as $i=>$tab) :
                    $is_active = ($tab['ekit_tab_title_is_active'] == 'yes') ? ' active show' : '';
                    $is_active = ($has_user_defined_active_tab == false && $i == 0) ? ' active show' : $is_active;
                ?>
                    <div class="tab-pane elementkit-tab-pane elementor-repeater-item-<?php echo esc_attr( $tab[ '_id' ] ); ?> <?php echo esc_attr($is_active);?>" id="content-<?php echo esc_attr($tab['_id'].$tab_id); ?>" role="tabpanel"
                         aria-labelledby="content-<?php echo esc_attr($tab['_id'].$tab_id); ?>-tab">
                        <div class="animated fadeIn">
                            <?php $this->print_text_editor( $tab['ekit_tab_content'] ); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
            </div>
            <?php
                if ( isset( $settings['ekit_tab_schema'] ) && 'yes' === $settings['ekit_tab_schema'] ) {
                    $json = [
                        '@context' => 'https://schema.org',
                        '@type' => 'FAQPage',
                        'mainEntity' => [],
                    ];

                    foreach ( $settings['ekit_tab_items'] as $index => $item ) {
                        $faq_tab_text = !empty( $item['ekit_tab_content'] ) ? $item['ekit_tab_content'] : '';
                        $json['mainEntity'][] = [
                            '@type' => 'Question',
                            'name' => esc_html($item['ekit_tab_title']),
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => \ElementsKit_Lite\Utils::kses( $faq_tab_text ),
                            ],
                        ];
                    }
                    ?>
                    <script type="application/ld+json"><?php echo wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
                    <?php
                }
            ?>
        </div>
    <?php
    }
}
