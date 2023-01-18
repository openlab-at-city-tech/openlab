<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Wp_Forms_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Wp_Forms extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;
    
    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->add_script_depends('wpforms');
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
        return 'https://wpmet.com/doc/wp-forms/';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'ekit_wpform_section_tab', [
                'label' =>esc_html__( 'wpForm', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_wpform_form_id',
            [
                'label' => __( 'Select Your Form', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'default' => '0',
				'options' => \ElementsKit_Lite\Utils::ekit_get__forms('wpforms'),
            ]
        );

        $this->end_controls_section();

        /** Labels **/
        $this->start_controls_section(
            'ekit_wpForms_section_label_style',
            [
                'label'             => __( 'Labels', 'elementskit-lite' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ekit_wpForms_text_color_label',
            [
                'label'             => __( 'Text Color', 'elementskit-lite' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field label' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'ekit_wpForms_typography_label',
                'label'             => __( 'Typography', 'elementskit-lite' ),
                'selector'          => '{{WRAPPER}} .ekit_wpForms_container .wpforms-field label',
            ]
        );
        
        $this->end_controls_section();

        /** Input & Textarea **/
        $this->start_controls_section(
            'ekit_wpForms_section_fields_style',
            [
                'label'             => __( 'Input & Textarea', 'elementskit-lite' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
            'ekit_wpForms_input_alignment',
            [
                'label'                 => __( 'Alignment', 'elementskit-lite' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
                    'left'      => [
                        'title' => __( 'Left', 'elementskit-lite' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'    => [
                        'title' => __( 'Center', 'elementskit-lite' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'     => [
                        'title' => __( 'Right', 'elementskit-lite' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea, {{WRAPPER}} .ekit_wpForms_container .wpforms-field select' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'ekit_wpForms_tabs_fields_style' );

        $this->start_controls_tab(
            'ekit_wpForms_tab_fields_normal',
            [
                'label'                 => __( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_wpForms_field_bg_color',
            [
                'label'             => __( 'Background Color', 'elementskit-lite' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea, {{WRAPPER}} .ekit_wpForms_container .wpforms-field select' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_wpForms_field_text_color',
            [
                'label'             => __( 'Text Color', 'elementskit-lite' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea, {{WRAPPER}} .ekit_wpForms_container .wpforms-field select' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'              => 'ekit_wpForms_field_border',
                'label'             => __( 'Border', 'elementskit-lite' ),
                'placeholder'       => '1px',
                'default'           => '1px',
                'selector'          => '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea, {{WRAPPER}} .ekit_wpForms_container .wpforms-field select',
                'separator'         => 'before',
            ]
        );

        $this->add_control(
            'ekit_wpForms_field_radius',
            [
                'label'             => __( 'Border Radius', 'elementskit-lite' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea, {{WRAPPER}} .ekit_wpForms_container .wpforms-field select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_wpForms_hr_1',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
        
        $this->add_responsive_control(
            'ekit_wpForms_input_width',
            [
                'label'             => __( 'Input Width', 'elementskit-lite' ),
                'type'              => Controls_Manager::SLIDER,
                'range'             => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 1200,
                        'step'  => 1,
                    ],
                ],
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ekit_wpForms_container .wpforms-field select' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-form .wpforms-field-row.wpforms-field-medium' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
			'ekit_wpForms_hr_2',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
        
        $this->add_responsive_control(
            'ekit_wpForms_textarea_width',
            [
                'label'             => __( 'Textarea Width', 'elementskit-lite' ),
                'type'              => Controls_Manager::SLIDER,
                'range'             => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 1200,
                        'step'  => 1,
                    ],
                ],
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'ekit_wpForms_textarea_height',
            [
                'label'             => __( 'Textarea Height', 'elementskit-lite' ),
                'type'              => Controls_Manager::SLIDER,
                'range'             => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 400,
                        'step'  => 1,
                    ],
                ],
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea' => 'height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_wpForms_field_padding',
            [
                'label'             => __( 'Padding', 'elementskit-lite' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea, {{WRAPPER}} .ekit_wpForms_container .wpforms-field select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'         => 'before',
            ]
        );

        $this->add_responsive_control(
            'ekit_wpForms_field_spacing',
            [
                'label'             => __( 'Margin', 'elementskit-lite' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'         => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'              => 'ekit_wpForms_field_box_shadow',
                'selector'          => '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea, {{WRAPPER}} .ekit_wpForms_container .wpforms-field select',
                'separator'         => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_wpForms_tab_fields_focus',
            [
                'label'                 => __( 'Focus', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'              => 'ekit_wpForms_focus_input_border',
                'label'             => __( 'Border', 'elementskit-lite' ),
                'placeholder'       => '1px',
                'default'           => '1px',
                'selector'          => '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input:focus, {{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea:focus',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'              => 'ekit_wpForms_focus_box_shadow',
                'selector'          => '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input:focus, {{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea:focus',
                'separator'         => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        
        $this->end_controls_section();

        /** Field Description **/
        $this->start_controls_section(
            'ekit_wpForms_section_field_description_style',
            [
                'label'                 => __( 'Field Description', 'elementskit-lite' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ekit_wpForms_field_description_text_color',
            [
                'label'                 => __( 'Text Color', 'elementskit-lite' ),
                'type'                  => Controls_Manager::COLOR,
                'selectors'             => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field .wpforms-field-description, {{WRAPPER}} .ekit_wpForms_container .wpforms-field .wpforms-field-sublabel' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'ekit_wpForms_field_description_typography',
                'label'                 => __( 'Typography', 'elementskit-lite' ),
                'selector'              => '{{WRAPPER}} .ekit_wpForms_container .wpforms-field .wpforms-field-description, {{WRAPPER}} .ekit_wpForms_container .wpforms-field .wpforms-field-sublabel',
            ]
        );

        $this->add_responsive_control(
            'ekit_wpForms_field_description_spacing',
            [
                'label'             => __( 'Padding', 'elementskit-lite' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field .wpforms-field-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field .wpforms-field-sublabel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'         => 'before',
            ]
        );
        
        $this->end_controls_section();

        /** Placeholder **/
        $this->start_controls_section(
            'ekit_wpForms_section_placeholder_style',
            [
                'label'             => __( 'Placeholder', 'elementskit-lite' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'ekit_wpForms_field_typography',
                'label'             => __( 'Typography', 'elementskit-lite' ),
                'selector'          => '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea, {{WRAPPER}} .ekit_wpForms_container .wpforms-field select',
                'separator'         => 'before',
            ]
        );

        $this->add_control(
            'ekit_wpForms_text_color_placeholder',
            [
                'label'             => __( 'Text Color', 'elementskit-lite' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-field input::-webkit-input-placeholder, {{WRAPPER}} .ekit_wpForms_container .wpforms-field textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();


        /** Submit Button **/
        $this->start_controls_section(
            'ekit_wpForms_section_submit_button_style',
            [
                'label'             => __( 'Submit Button', 'elementskit-lite' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'ekit_wpForms_button_width_type',
            [
                'label'                 => __( 'Width', 'elementskit-lite' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'custom',
                'options'               => [
                    'full-width'    => __( 'Full Width', 'elementskit-lite' ),
                    'custom'        => __( 'Custom', 'elementskit-lite' ),
                ],
                'prefix_class'          => 'ekit_wpForms_container-form-button-',
            ]
        );

        $this->add_responsive_control(
            'ekit_wpForms_button_align',
            [
                'label'             => __( 'Alignment', 'elementskit-lite' ),
                'type'              => Controls_Manager::CHOOSE,
                'options'           => [
                    'left'        => [
                        'title'   => __( 'Left', 'elementskit-lite' ),
                        'icon'    => 'eicon-h-align-left',
                    ],
                    'center'      => [
                        'title'   => __( 'Center', 'elementskit-lite' ),
                        'icon'    => 'eicon-h-align-center',
                    ],
                    'right'       => [
                        'title'   => __( 'Right', 'elementskit-lite' ),
                        'icon'    => 'eicon-h-align-right',
                    ],
                ],
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container'   => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit' => 'display:inline-block;'
                ],
                'condition'             => [
                    'ekit_wpForms_button_width_type' => 'custom',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'ekit_wpForms_button_width',
            [
                'label'                 => __( 'Width', 'elementskit-lite' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 1200,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'ekit_wpForms_button_width_type' => 'custom',
                ],
            ]
        );

        $this->start_controls_tabs( 'ekit_wpForms_tabs_button_style' );

        $this->start_controls_tab(
            'ekit_wpForms_tab_button_normal',
            [
                'label'             => __( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'ekit_wpForms_button_typography',
                'label'             => __( 'Typography', 'elementskit-lite' ),
                'selector'          => '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit',
                'separator'         => 'before',
            ]
        );

        $this->add_control(
            'ekit_wpForms_button_bg_color_normal',
            [
                'label'             => __( 'Background Color', 'elementskit-lite' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_wpForms_button_text_color_normal',
            [
                'label'             => __( 'Text Color', 'elementskit-lite' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'              => 'ekit_wpForms_button_box_shadow',
                'selector'          => '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit',
                'separator'         => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'              => 'ekit_wpForms_button_border_normal',
                'label'             => __( 'Border', 'elementskit-lite' ),
                'placeholder'       => '1px',
                'default'           => '1px',
                'selector'          => '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit',
            ]
        );

        $this->add_control(
            'ekit_wpForms_button_border_radius',
            [
                'label'             => __( 'Border Radius', 'elementskit-lite' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_wpForms_button_padding',
            [
                'label'             => __( 'Padding', 'elementskit-lite' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_wpForms_button_margin',
            [
                'label'             => __( 'Margin', 'elementskit-lite' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_wpForms_tab_button_hover',
            [
                'label'             => __( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_wpForms_button_bg_color_hover',
            [
                'label'             => __( 'Background Color', 'elementskit-lite' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_wpForms_button_text_color_hover',
            [
                'label'             => __( 'Text Color', 'elementskit-lite' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_wpForms_button_border_color_hover',
            [
                'label'             => __( 'Border Color', 'elementskit-lite' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .ekit_wpForms_container .wpforms-submit-container .wpforms-submit:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();

        /** Errors **/
        $this->start_controls_section(
            'ekit_wpForms_section_error_style',
            [
                'label'                 => __( 'Errors', 'elementskit-lite' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ekit_wpForms_error_message_text_color',
            [
                'label'                 => __( 'Text Color', 'elementskit-lite' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .ekit_wpForms_container label.wpforms-error' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_wpForms_error_field_input_border',
				'label' => __( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit_wpForms_container input.wpforms-error, {{WRAPPER}} .ekit_wpForms_container textarea.wpforms-error',
			]
		);
        
        $this->end_controls_section();

        $this->insert_pro_message();
    }

    protected function render( ) {
        echo '<div class="ekit-wid-con ekit_wpForms_container">';
            $this->render_raw();
        echo '</div>';
	}

    protected function render_raw( ) {
        $settings = $this->get_settings();

		if ( ! empty( $settings['ekit_wpform_form_id'] ) ) {
            echo do_shortcode('[wpforms id="'.intval($settings['ekit_wpform_form_id']).'"]' );
		}
	}
}
