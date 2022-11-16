<?php
namespace Elementor;
use \Elementor\ElementsKit_Widget_Fluent_Forms_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Fluent_Forms extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;
    
    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
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
        return 'https://wpmet.com/doc/fluent-forms/';
    }
    
    public static function getForms()
    {
        if(!function_exists('wpFluent')){ return [esc_html__("Please install the 'Fluent Forms' plugin.", 'elementskit-lite')]; }
        $ff_list = wpFluent()->table('fluentform_forms')
            ->select(['id', 'title'])
            ->orderBy('id', 'DESC')
            ->get();


        $forms = array();

        if ($ff_list) {
            $forms[0] = esc_html__('Select a Fluent Form', 'elementskit-lite');
            foreach ($ff_list as $form) {
                $forms[$form->id] = $form->title .' ('.$form->id.')';
            }
        } else {
            $forms[0] = esc_html__('Create a Form First', 'elementskit-lite');
        }

        return $forms;
    }

    protected function register_controls() {

        // General Controls
        $this->start_controls_section(
            'section_fluent_form',
            [
                'label' => __('Fluent Form', 'elementskit-lite'),
            ]
        );


        $this->add_control(
            'form_list',
            [
                'label' => esc_html__('Fluent Form', 'elementskit-lite'),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => self::getForms(),
                'default' => '0',
            ]
        );

        $this->add_control(
            'custom_title_description',
            [
                'label' => __('Custom Title & Description', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'elementskit-lite'),
                'label_off' => __('No', 'elementskit-lite'),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'form_title_custom',
            [
                'label' => esc_html__('Title', 'elementskit-lite'),
                'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'label_block' => true,
                'default' => '',
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'form_description_custom',
            [
                'label' => esc_html__('Description', 'elementskit-lite'),
                'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
                'default' => '',
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'labels_switch',
            [
                'label' => __('Labels', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Show', 'elementskit-lite'),
                'label_off' => __('Hide', 'elementskit-lite'),
                'return_value' => 'yes'
            ]
        );

        $this->add_control(
            'placeholder_switch',
            [
                'label' => __('Placeholder', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Show', 'elementskit-lite'),
                'label_off' => __('Hide', 'elementskit-lite'),
                'return_value' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Error Controls
        $this->start_controls_section(
            'section_errors',
            [
                'label' => __('Errors', 'elementskit-lite'),
            ]
        );

        $this->add_control(
            'error_messages',
            [
                'label' => __('Error Messages', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Show', 'elementskit-lite'),
                'label_off' => __('Hide', 'elementskit-lite'),
                'return_value' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Title & Description style
        $this->start_controls_section(
            'section_form_title_style',
            [
                'label' => __('Title & Description', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'heading_alignment',
            [
                'label' => __('Alignment', 'elementskit-lite'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'elementskit-lite'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'elementskit-lite'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'elementskit-lite'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-fluentform-widget-title' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .ekit-fluentform-widget-description' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'heading_title',
            [
                'label' => __('Title', 'elementskit-lite'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'form_title_text_color',
            [
                'label' => __('Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-fluentform-widget-title' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'form_title_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .ekit-fluentform-widget-title',
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_title_margin',
            [
                'label' => __('Margin', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'allowed_dimensions' => 'vertical',
                'placeholder' => [
                    'top' => '',
                    'right' => 'auto',
                    'bottom' => '',
                    'left' => 'auto',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-fluentform-widget-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_title_padding',
            [
                'label' => esc_html__('Padding', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-fluentform-widget-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'heading_description',
            [
                'label' => __('Description', 'elementskit-lite'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'heading_description_text_color',
            [
                'label' => __('Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-fluentform-widget-description' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_description_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .ekit-fluentform-widget-description',
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );


        $this->add_responsive_control(
            'heading_description_margin',
            [
                'label' => __('Margin', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'allowed_dimensions' => 'vertical',
                'placeholder' => [
                    'top' => '',
                    'right' => 'auto',
                    'bottom' => '',
                    'left' => 'auto',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-fluentform-widget-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'custom_title_description' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'heading_description_padding',
            [
                'label' => esc_html__('Padding', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ekit-fluentform-widget-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Form Container style
        $this->start_controls_section(
            'section_form_container_style',
            [
                'label' => __('Form Container', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'form_container_background',
                'label' => __( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper',
            ]
        );


        $this->add_control(
            'form_container_link_color',
            [
                'label' => __('Link Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_container_max_width',
            [
                'label' => esc_html__('Max Width', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 1500,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper' => 'width: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'form_container_alignment',
            [
                'label' => esc_html__('Alignment', 'elementskit-lite'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => true,
                'options' => [
                    'default' => [
                        'title' => __('Default', 'elementskit-lite'),
                        'icon' => 'fa fa-ban',
                    ],
                    'left' => [
                        'title' => esc_html__('Left', 'elementskit-lite'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'elementskit-lite'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'elementskit-lite'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'default',
            ]
        );

        $this->add_responsive_control(
            'form_container_margin',
            [
                'label' => esc_html__('Margin', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_container_padding',
            [
                'label' => esc_html__('Padding', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_container_border',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper',
            ]
        );

        $this->add_control(
            'form_container_border_radius',
            [
                'label' => esc_html__('Border Radius', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'separator' => 'before',
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'form_container_box_shadow',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper',
            ]
        );

        $this->end_controls_section();

        // Label Style
        $this->start_controls_section(
            'section_form_label_style',
            [
                'label' => __('Labels', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_label_text_color',
            [
                'label' => __('Text Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-input--label label' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'form_label_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-input--label label',
            ]
        );

        $this->end_controls_section();

        // Input Textarea style
        $this->start_controls_section(
            'section_form_fields_style',
            [
                'label' => __('Input & Textarea', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'input_alignment',
            [
                'label' => __('Alignment', 'elementskit-lite'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'elementskit-lite'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'elementskit-lite'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'elementskit-lite'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group select' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_form_fields_style');

        $this->start_controls_tab(
            'tab_form_fields_normal',
            [
                'label' => __('Normal', 'elementskit-lite'),
            ]
        );

        $this->add_control(
            'form_field_bg_color',
            [
                'label' => __('Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):not(.select2-search__field), {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group select, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .select2-container--default .select2-selection--multiple' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'form_field_text_color',
            [
                'label' => __('Text Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group select' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_field_border',
                'label' => __('Border', 'elementskit-lite'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):not(.select2-search__field), {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group select,  {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .select2-container--default .select2-selection--multiple',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'form_field_radius',
            [
                'label' => __('Border Radius', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group select,  {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .select2-container--default .select2-selection--multiple' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_field_text_indent',
            [
                'label' => __('Text Indent', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group select' => 'text-indent: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'form_input_width',
            [
                'label' => __('Input Width', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group select, {{WRAPPER}} .frm-fluent-form .choices' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_input_height',
            [
                'label' => __('Input Height', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group select' => 'height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_textarea_width',
            [
                'label' => __('Textarea Width', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_textarea_height',
            [
                'label' => __('Textarea Height', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 400,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea' => 'height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_field_padding',
            [
                'label' => __('Padding', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_field_spacing',
            [
                'label' => __('Spacing', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'form_field_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group select',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'form_field_box_shadow',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group select',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_form_fields_focus',
            [
                'label' => __('Focus', 'elementskit-lite'),
            ]
        );

        $this->add_control(
            'form_field_bg_color_focus',
            [
                'label' => __('Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_input_focus_border',
                'label' => __('Border', 'elementskit-lite'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea:focus',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'form_input_focus_box_shadow',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea:focus',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // Placeholder Style
        $this->start_controls_section(
            'section_placeholder_style',
            [
                'label' => __('Placeholder', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'placeholder_switch' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'form_placeholder_text_color',
            [
                'label' => __('Text Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group input::-webkit-input-placeholder, {{WRAPPER}} .fluentform-widget-wrapper .ff-el-group textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'placeholder_switch' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();


        // Radio & Checkbox Styles
        $this->start_controls_section(
            'section_form_radio_checkbox_style',
            [
                'label' => __('Radio & Checkbox', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'radio_checkbox_label_color',
            [
                'label' => __('Label Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ff-el-form-check-label' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'radio_checkbox_label_typo',
                'label' => __('Label Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .ff-el-form-check-label',
                'separator' => 'after',
                'fields_options'    => [
                    'typography'  => [
                        'label' => __('Label Typography', 'elementskit-lite'),
                    ],
                ]
            ]
        );

        $this->add_responsive_control(
            'form_radio_checkbox_text_indent',
            [
                'label' => __('Text Indent', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default'   => [
                    'unit'  => 'px',
                    'size'  => 3
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ff-el-input--content input[type="checkbox"], {{WRAPPER}} .ff-el-input--content input[type="radio"]' => 'margin-right: {{SIZE}}{{UNIT}}',
                ]
            ]
        );

        $this->add_control(
            'form_custom_radio_checkbox',
            [
                'label' => __('Custom Styles', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'elementskit-lite'),
                'label_off' => __('No', 'elementskit-lite'),
                'return_value' => 'yes'
            ]
        );

        $this->add_responsive_control(
            'form_radio_checkbox_size',
            [
                'label' => __('Size', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '15',
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="radio"]' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_radio_checkbox_style');

        $this->start_controls_tab(
            'form_radio_checkbox_normal',
            [
                'label' => __('Normal', 'elementskit-lite'),
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'form_radio_checkbox_bg_color',
            [
                'label' => __('Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="checkbox"]:after, {{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="radio"]:after' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_checkbox_border_width',
            [
                'label' => __('Border Width', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 15,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="checkbox"]:after, {{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="radio"]:after' => 'border-width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'form_checkbox_border_color',
            [
                'label' => __('Border Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="checkbox"]:after, {{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="radio"]:after' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'form_checkbox_heading',
            [
                'label' => __('Checkbox', 'elementskit-lite'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'form_checkbox_border_radius',
            [
                'label' => __('Border Radius', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="checkbox"]:after, {{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="radio"]:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'form_radio_heading',
            [
                'label' => __('Radio Buttons', 'elementskit-lite'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'form_radio_border_radius',
            [
                'label' => __('Border Radius', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="radio"]:after, {{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="radio"]:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'form_radio_checkbox_checked',
            [
                'label' => __('Checked', 'elementskit-lite'),
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'form_radio_checkbox_bg_color_checked',
            [
                'label' => __('Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="checkbox"]:checked:after, {{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="radio"]:checked:after' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'form_radio_checkbox_border_checked',
            [
                'label' => __('Border Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="checkbox"]:checked:after, {{WRAPPER}} .fluentform-widget-custom-radio-checkbox input[type="radio"]:checked:after' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'form_custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // Section Break Style
        $this->start_controls_section(
            'form_section_break_style',
            [
                'label' => __('Section Break', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_section_break_label',
            [
                'label' => __('Label', 'elementskit-lite'),
                'type' => Controls_Manager::HEADING
            ]
        );

        $this->add_control(
            'form_section_break_label_color',
            [
                'label' => __('Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-section-break .ff-el-section-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'form_section_break_label_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '.fluentform-widget-wrapper .ff-el-section-break .ff-el-section-title',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'form_section_break_label_padding',
            [
                'label' => __('Padding', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-section-break .ff-el-section-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_section_break_label_margin',
            [
                'label' => __('Margin', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-section-break .ff-el-section-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'form_section_break_description',
            [
                'label' => __('Description', 'elementskit-lite'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'form_section_break_description_color',
            [
                'label' => __('Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-section-break .ff-section_break_desk' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'form_section_break_description_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-section-break div',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'form_section_break_description_padding',
            [
                'label' => __('Padding', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-section-break .ff-section_break_desk' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_section_break_description_margin',
            [
                'label' => __('Margin', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-section-break .ff-section_break_desk' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_section_break_alignment',
            [
                'label' => __('Alignment', 'elementskit-lite'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'elementskit-lite'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'elementskit-lite'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'elementskit-lite'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'prefix_class' => 'fluentform-widget-section-break-content-'
            ]
        );

        $this->add_control(
            'form_section_break_separator_color',
            [
                'label' => __('Separator Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ff-el-section-break hr' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        // Checkbox Grid Style
        $this->start_controls_section(
            'section_form_checkbox_grid',
            [
                'label' => __('Checkbox Grid', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'section_form_checkbox_grid_head',
            [
                'label' => __('Grid Table Head', 'elementskit-lite'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'form_checkbox_grid_table_head_text_color',
            [
                'label' => __('Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-table thead th' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'form_checkbox_grid_table_head_color',
            [
                'label' => __('Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-table thead th' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'form_checkbox_grid_table_head_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff-table thead th',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'form_checkbox_grid_table_head_height',
            [
                'label' => __('Height', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-table thead th' => 'height: {{SIZE}}{{UNIT}}',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_checkbox_grid_table_head_border',
                'label' => __('Border', 'elementskit-lite'),
                'default' => '',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff-table thead tr',
            ]
        );

        $this->add_responsive_control(
            'form_checkbox_grid_table_head_padding',
            [
                'label' => __('Padding', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-table thead th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'form_checkbox_grid_table_item',
            [
                'label' => __('Grid Table Item', 'elementskit-lite'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'form_checkbox_grid_table_item_color',
            [
                'label' => __('Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-table tbody tr td' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'form_checkbox_grid_table_item_bg_color',
            [
                'label' => __('Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-table tbody tr td' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'form_checkbox_grid_table_item_odd_bg_color',
            [
                'label' => __('Odd Item Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper tbody>tr:nth-child(2n)>td' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'form_checkbox_grid_table_item_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff-table tbody tr td',
            ]
        );

        $this->add_responsive_control(
            'form_checkbox_grid_table_item_height',
            [
                'label' => __('Height', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-table tbody tr td' => 'height: {{SIZE}}{{UNIT}}',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_checkbox_grid_table_item_border',
                'label' => __('Border', 'elementskit-lite'),
                'default' => '',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff-table tbody tr',
            ]
        );

        $this->add_responsive_control(
            'form_checkbox_grid_table_item_padding',
            [
                'label' => __('Padding', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-table tbody tr td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Address Line Styles
        $this->start_controls_section(
            'section_form_address_line_style',
            [
                'label' => __('Address Line', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'address_line_label_color',
            [
                'label' => __('Label Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .fluent-address label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'address_line_label_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .fluent-address label',
            ]
        );

        $this->end_controls_section();

        // Image Upload Style
        $this->start_controls_section(
            'section_form_image_upload_style',
            [
                'label' => __('Image Upload', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_form_image_upload_button_style');

        $this->start_controls_tab(
            'tab_form_image_upload_button_normal',
            [
                'label' => __('Normal', 'elementskit-lite'),
            ]
        );

        $this->add_control(
            'form_image_upload_bg_color',
            [
                'label' => __('Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff_upload_btn.ff-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_image_upload_button_border_normal',
                'label' => __('Border', 'elementskit-lite'),
                'default' => '',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff_upload_btn.ff-btn',
            ]
        );

        $this->add_control(
            'form_image_upload_button_border_radius',
            [
                'label' => __('Border Radius', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff_upload_btn.ff-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_image_upload_button_padding',
            [
                'label' => __('Padding', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff_upload_btn.ff-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'form_image_upload_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff_upload_btn.ff-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'form_image_upload_button_box_shadow',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff_upload_btn.ff-btn',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_form_image_upload_button_hover',
            [
                'label' => __('Hover', 'elementskit-lite'),
            ]
        );

        $this->add_control(
            'form_image_upload_button_bg_color_hover',
            [
                'label' => __('Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff_upload_btn.ff-btn:hover' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'form_image_upload_button_text_color_hover',
            [
                'label' => __('Text Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff_upload_btn.ff-btn:hover' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'form_image_upload_button_border_color_hover',
            [
                'label' => __('Border Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff_upload_btn.ff-btn:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();

       

        // Pagination Style
        if( defined("FLUENTFORMPRO") ) {

             // Range Slider
            $this->start_controls_section(
                'range_slider_section',
                [
                    'label' => __('Range Slider', 'elementskit-lite'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'range_slider_counter_color',
                [
                    'label' => __('Counter Color', 'elementskit-lite'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ff_range_value' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'range_slider_counter_typo',
                    'label' => __('Typography', 'elementskit-lite'),
                    'selector' => '{{WRAPPER}} .ff_range_value',
                    'separator' => 'before',
                    'fields_options'    => [
                        'typography'  => [
                            'label' => __( 'Counter Typography', 'elementskit-lite' )
                        ],
                    ]
                ]
            );

            $this->start_controls_tabs('range_slider_tabs');

            $this->start_controls_tab(
                'range_slider_normal',
                [
                    'label' => __('Normal', 'elementskit-lite'),
                ]
            );
            $this->add_control(
                'range_slider_normal_color',
                [
                    'label' => __('Color', 'elementskit-lite'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rangeslider' => 'background: {{VALUE}}',
                    ],
                ]
            );
            $this->end_controls_tab();

            $this->start_controls_tab(
                'range_slider_active',
                [
                    'label' => __('Active', 'elementskit-lite'),
                ]
            );
            $this->add_control(
                'range_slider_active_color',
                [
                    'label' => __('Color', 'elementskit-lite'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rangeslider' => 'background: {{VALUE}}',
                    ],
                ]
            );
            $this->end_controls_tab();

            $this->end_controls_tabs();


            $this->end_controls_section();
            // End Range Slider

            // Net Promoter Score
            $this->start_controls_section(
                'pro_score_section',
                [
                    'label' => __('Net Promoter Score', 'elementskit-lite'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'pro_score_label',
                [
                    'label' => esc_html__( 'Label:', 'elementskit-lite' ),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->add_control(
                'pro_score_label_color',
                [
                    'label' => __('Color', 'elementskit-lite'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .ff_not-likely, {{WRAPPER}} .ff_extremely-likely' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'pro_score_label_typo',
                    'label' => __('Typography', 'elementskit-lite'),
                    'selector' => '{{WRAPPER}} .ff_not-likely, {{WRAPPER}} .ff_extremely-likely',
                    'separator' => 'after',
                ]
            );

            $this->add_control(
                'pro_score_input',
                [
                    'label' => esc_html__( 'Input:', 'elementskit-lite' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'pro_score_input_color',
                [
                    'label' => __('Color', 'elementskit-lite'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .ff_net_table tbody tr td label' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'pro_score_input_typo',
                    'label' => __('Typography', 'elementskit-lite'),
                    'selector' => '{{WRAPPER}} .ff_net_table tbody tr td label',
                    'separator' => 'after',
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'pro_score_input_bg',
                    'label' => __( 'Background', 'elementskit-lite' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .ff_net_table tbody tr td label'
                ]
            );

            $this->add_control(
                'pro_score_input_border_color',
                [
                    'label' => __('Border Color', 'elementskit-lite'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .ff_net_table tbody tr td, {{WRAPPER}} .ff_net_table tbody tr td:first-of-type' => 'border-color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_control(
                'pro_score_input_hover_border_color',
                [
                    'label' => __('Hover Border Color', 'elementskit-lite'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .ff_net_table tbody tr td label:hover:after' => 'border-color: {{VALUE}}',
                    ],
                ]
            );


            $this->end_controls_section();
            // End Net Promoter Score

            // Rating
            $this->start_controls_section(
                'rating_section',
                [
                    'label' => __('Rating', 'elementskit-lite'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control(
                'rating_font_size',
                [
                    'label' => esc_html__( 'Font Size', 'elementskit-lite' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .fluentform .ff-el-ratings svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'rating_gap',
                [
                    'label' => esc_html__( 'Gap', 'elementskit-lite' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .fluentform .ff-el-ratings svg' => 'margin: 0 {{SIZE}}{{UNIT}}',
                    ],
                ]
            );

            $this->start_controls_tabs('rating_tabs');

            $this->start_controls_tab(
                'rating_normal',
                [
                    'label' => __('Normal', 'elementskit-lite'),
                ]
            );
            $this->add_control(
                'rating_normal_color',
                [
                    'label' => __('Color', 'elementskit-lite'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .fluentform .ff-el-ratings label svg' => 'fill: {{VALUE}}',
                    ],
                ]
            );
            $this->end_controls_tab();

            $this->start_controls_tab(
                'rating_active',
                [
                    'label' => __('Active', 'elementskit-lite'),
                ]
            );
            $this->add_control(
                'rating_active_color',
                [
                    'label' => __('Color', 'elementskit-lite'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .fluentform .ff-el-ratings label.active svg' => 'fill: {{VALUE}}',
                    ],
                ]
            );
            $this->end_controls_tab();

            $this->end_controls_tabs();


            $this->end_controls_section();
            // End Rating

            $this->start_controls_section(
                'section_form_pagination_style',
                [
                    'label' => __('Pagination', 'elementskit-lite'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'form_pagination_progressbar_label',
                [
                    'label' => __('Progressbar Label', 'elementskit-lite'),
                    'type' => Controls_Manager::HEADING
                ]
            );

            $this->add_control(
                'show_label',
                [
                    'label'     => __( 'Show Label', 'elementskit-lite' ),
                    'type'      => Controls_Manager::SWITCHER,
                    'label_on'  => __( 'Show', 'elementskit-lite' ),
                    'label_off' => __( 'Hide', 'elementskit-lite' ),
                    'return_value' => 'yes',
                    'default'   => 'yes',
                    'render_type'   => 'template',
                    'prefix_class'  => 'ekit-fluent-form-widget-step-header-'
                ]
            );

            $this->add_control(
                'form_progressbar_label_color',
                [
                    'label'     => __( 'Label Color', 'elementskit-lite' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ff-el-progress-status' => 'color: {{VALUE}}',
                    ],
                    'condition' => [
                        'show_label'    => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'form_progressbar_label_typography',
                    'label' => __( 'Typography', 'elementskit-lite' ),
                    'selector' => '{{WRAPPER}} .ff-el-progress-status',
                    'condition' => [
                        'show_label'    => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'form_progressbar_label_space',
                [
                    'label' => __( 'Spacing', 'elementskit-lite' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .ff-el-progress-status' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => [
                        'show_label'    => 'yes'
                    ],
                    'separator' => 'after'
                ]
            );

            $this->add_control(
                'form_pagination_progressbar',
                [
                    'label' => __('Progressbar', 'elementskit-lite'),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->add_control(
                'show_form_progressbar',
                [
                    'label'     => __( 'Show Progressbar', 'elementskit-lite' ),
                    'type'      => Controls_Manager::SWITCHER,
                    'label_on'  => __( 'Show', 'elementskit-lite' ),
                    'label_off' => __( 'Hide', 'elementskit-lite' ),
                    'return_value' => 'yes',
                    'default'   => 'yes',
                    'prefix_class'  => 'ekit-fluent-form-widget-step-progressbar-'
                ]
            );

            $this->start_controls_tabs('form_progressbar_style_tabs');

            $this->start_controls_tab(
                'form_progressbar_normal',
                [
                    'label' => __('Normal', 'elementskit-lite'),
                    'condition' => [
                        'show_form_progressbar'  => 'yes'
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'form_progressbar_bg',
                    'label' => __( 'Background', 'elementskit-lite' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .ff-el-progress',
                    'condition' => [
                        'show_form_progressbar'  => 'yes'
                    ],
                    'exclude'    => [
                        'image'
                    ]
                ]
            );

            $this->add_control(
                'form_progressbar_color',
                [
                    'label' => __( 'Text Color', 'elementskit-lite' ),
                    'type'  =>   Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ff-el-progress-bar span' => 'color: {{VALUE}};',
                    ],
                    'condition' => [
                        'show_form_progressbar'  => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'form_progressbar_height',
                [
                    'label' => __( 'Height', 'elementskit-lite' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ff-el-progress' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'show_form_progressbar'  => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'form_progressbar_border',
                    'label' => __( 'Border', 'elementskit-lite' ),
                    'selector' => '{{WRAPPER}} .ff-el-progress',
                    'condition' => [
                        'show_form_progressbar'  => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'form_progressbar_border_radius',
                [
                    'label' => __( 'Border Radius', 'elementskit-lite' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .ff-el-progress' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => [
                        'show_form_progressbar'  => 'yes'
                    ]
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'form_progressbar_filled',
                [
                    'label' => __('Filled', 'elementskit-lite'),
                    'condition' => [
                        'show_form_progressbar'  => 'yes'
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'form_progressbar_bg_filled',
                    'label' => __( 'Background', 'elementskit-lite' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .ff-el-progress-bar',
                    'condition' => [
                        'show_form_progressbar'  => 'yes'
                    ],
                    'exclude'    => [
                        'image'
                    ]
                ]
            );


            $this->end_controls_tab();

            $this->end_controls_tabs();



            $this->add_control(
                'form_pagination_button_style',
                [
                    'label' => __('Button', 'elementskit-lite'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before'
                ]
            );

            $this->start_controls_tabs(
                'form_pagination_button_style_tabs'
            );


            $this->start_controls_tab(
                'form_pagination_button',
                [
                    'label' => __('Normal', 'elementskit-lite'),
                ]
            );


            $this->add_control(
                'form_pagination_button_color',
                [
                    'label' => __( 'Color', 'elementskit-lite' ),
                    'type'  =>   Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .step-nav button' => 'color: {{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'form_pagination_button_typography',
                    'label' => __( 'Typography', 'elementskit-lite' ),
                    'selector' => '{{WRAPPER}} .step-nav button',
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'form_pagination_button_bg',
                    'label' => __( 'Background', 'elementskit-lite' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .step-nav button',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'form_pagination_button_border',
                    'label' => __( 'Border', 'elementskit-lite' ),
                    'selector' => '{{WRAPPER}} .step-nav button',
                ]
            );

            $this->add_control(
                'form_pagination_button_border_radius',
                [
                    'label' => __( 'Border Radius', 'elementskit-lite' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .step-nav button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'form_pagination_button_padding',
                [
                    'label' => __( 'Padding', 'elementskit-lite' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .step-nav button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'form_pagination_button_hover',
                [
                    'label' => __('Hover', 'elementskit-lite'),
                ]
            );

            $this->add_control(
                'form_pagination_button_hover_color',
                [
                    'label' => __( 'Color', 'elementskit-lite' ),
                    'type'  =>   Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .step-nav button:hover' => 'color: {{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'form_pagination_button_hover_bg',
                    'label' => __( 'Background', 'elementskit-lite' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .step-nav button:hover',
                ]
            );

            $this->add_control(
                'form_pagination_button_border_hover_color',
                [
                    'label' => __( 'Border Color', 'elementskit-lite' ),
                    'type'  =>   Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .step-nav button:hover' => 'border-color: {{VALUE}};',
                    ]
                ]
            );

            $this->add_control(
                'form_pagination_button_border_hover_radius',
                [
                    'label' => __( 'Border Radius', 'elementskit-lite' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .step-nav button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();


            $this->end_controls_section();
        }

        // Submit Button Styles
        $this->start_controls_section(
            'section_form_submit_button_style',
            [
                'label' => __('Submit Button', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'form_submit_button_align',
            [
                'label' => __('Alignment', 'elementskit-lite'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'elementskit-lite'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'elementskit-lite'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'elementskit-lite'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => '',
                'prefix_class' => 'fluentform-widget-submit-button-',
                'condition' => [
                    'form_submit_button_width_type' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'form_submit_button_width_type',
            [
                'label' => __('Width', 'elementskit-lite'),
                'type' => Controls_Manager::SELECT,
                'default' => 'custom',
                'options' => [
                    'full-width' => __('Full Width', 'elementskit-lite'),
                    'custom' => __('Custom', 'elementskit-lite'),
                ],
                'prefix_class' => 'fluentform-widget-submit-button-',
            ]
        );

        $this->add_responsive_control(
            'form_submit_button_width',
            [
                'label' => __('Width', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'form_submit_button_width_type' => 'custom',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_submit_button_style');

        $this->start_controls_tab(
            'tab_submit_button_normal',
            [
                'label' => __('Normal', 'elementskit-lite'),
            ]
        );

        $this->add_control(
            'form_submit_button_bg_color_normal',
            [
                'label' => __('Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '#409EFF',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'form_submit_button_text_color_normal',
            [
                'label' => __('Text Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_submit_button_border_normal',
                'label' => __('Border', 'elementskit-lite'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit',
            ]
        );

        $this->add_control(
            'form_submit_button_border_radius',
            [
                'label' => __('Border Radius', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_submit_button_padding',
            [
                'label' => __('Padding', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_submit_button_margin',
            [
                'label' => __('Margin Top', 'elementskit-lite'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'form_submit_button_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'form_submit_button_box_shadow',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_submit_button_hover',
            [
                'label' => __('Hover', 'elementskit-lite'),
            ]
        );

        $this->add_control(
            'form_submit_button_bg_color_hover',
            [
                'label' => __('Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit:hover' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'form_submit_button_text_color_hover',
            [
                'label' => __('Text Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit:hover' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'form_submit_button_border_color_hover',
            [
                'label' => __('Border Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-el-group .ff-btn-submit:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // Submit Button Styles
        $this->start_controls_section(
            'section_form_success_message_style',
            [
                'label' => __('Success Message', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_success_message_bg_color',
            [
                'label' => __('Background Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-message-success' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'form_success_message_text_color',
            [
                'label' => __('Text Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .ff-message-success' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_success_message_border',
                'label' => __('Border', 'elementskit-lite'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff-message-success',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'form_success_message_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .ff-message-success',
            ]
        );

        $this->end_controls_section();

        // Error Message styles
        $this->start_controls_section(
            'section_form_error_style',
            [
                'label' => __('Error Message', 'elementskit-lite'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_error_message_text_color',
            [
                'label' => __('Color', 'elementskit-lite'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .error.text-danger' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'form_error_message_typography',
                'label' => __('Typography', 'elementskit-lite'),
                'selector' => '{{WRAPPER}} .fluentform-widget-wrapper .error.text-danger',
            ]
        );

        $this->add_responsive_control(
            'form_error_message_padding',
            [
                'label' => __('Padding', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .error.text-danger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_error_message_margin',
            [
                'label' => __('Margin', 'elementskit-lite'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fluentform-widget-wrapper .error.text-danger' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
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

        $form_list_id_sanitize = isset($form_list) ? intval($form_list) : 0;

        $this->add_render_attribute(
            'ekit_fluent_forms_widget_wrapper',
            [
                'class' => [
                    'fluentform-widget-wrapper',
                ]
            ]
        );


        if ( $placeholder_switch != 'yes' ) {
            $this->add_render_attribute( 'ekit_fluent_forms_widget_wrapper', 'class', 'hide-placeholder' );
        }

        if( $labels_switch != 'yes' ) {
            $this->add_render_attribute( 'ekit_fluent_forms_widget_wrapper', 'class', 'hide-fluent-form-labels' );
        }

        if( $error_messages != 'yes' ) {
            $this->add_render_attribute( 'ekit_fluent_forms_widget_wrapper', 'class', 'hide-error-message' );
        }

        if ( $form_custom_radio_checkbox == 'yes' ) {
            $this->add_render_attribute( 'ekit_fluent_forms_widget_wrapper', 'class', 'fluentform-widget-custom-radio-checkbox' );
        }

        if ( $form_container_alignment ) {
            $this->add_render_attribute( 'ekit_fluent_forms_widget_wrapper', 'class', 'fluentform-widget-align-'.$form_container_alignment.'' );
        }

        if ( ! empty( $form_list_id_sanitize ) ) { ?>

            <div <?php echo $this->get_render_attribute_string('ekit_fluent_forms_widget_wrapper'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>

                <?php if ($custom_title_description == 'yes') { ?>
                    <div class="ekit-fluentform-widget-heading">
                        <?php if ($form_title_custom != '') { ?>
                            <h3 class="ekit-fluentform-widget-title">
                                <?php echo esc_attr($form_title_custom); ?>
                            </h3>
                        <?php } ?>
                        <?php if ($form_description_custom != '') { ?>
                            <p class="ekit-fluentform-widget-description">
                                <?php echo wp_kses($form_description_custom, \ElementsKit_Lite\Utils::get_kses_array()); ?>
                            </p>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php echo do_shortcode('[fluentform id="' . $form_list_id_sanitize . '"]'); ?>
            </div>

            <?php
        }
	}
}
