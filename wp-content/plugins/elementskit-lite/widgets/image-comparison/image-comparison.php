<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Image_Comparison_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Image_Comparison extends Widget_Base {
	use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;
    
    public function __construct( $data = [], $args = null ) {

        parent::__construct( $data, $args );

		$this->add_script_depends('event.move');
		$this->add_script_depends('twentytwenty');
		$this->add_script_depends('imagesloaded');
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
        return 'https://wpmet.com/doc/image-comparisn/';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'ekit_img_comparison_section_items',
            [
                'label' => esc_html__( 'Items', 'elementskit-lite' ),
            ]
        );


        $this->add_control(
            'ekit_img_comparison_container_style',
            [
                'label' => esc_html__( 'Container Style', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'horizontal' => esc_html__( 'Horizontal', 'elementskit-lite' ),
                    'vertical' => esc_html__( 'Vertical', 'elementskit-lite' ),
                ],
                'default' => 'vertical',
            ]
        );
        $this->add_control(
            'ekit_img_comparison_before_heading_section',
            [
                'label' => esc_html__( 'Before', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'ekit_img_comparison_image_before',
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
            ]
        );
        $this->add_control(
            'ekit_img_comparison_label_before',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => 'Before',
            ]
        );
        $this->add_control(
            'ekit_img_comparison_after_heading_section',
            [
                'label' => esc_html__( 'After', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'ekit_img_comparison_image_after',
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
            ]
        );
        $this->add_control(
            'ekit_img_comparison_label_after',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => 'After',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ekit_img_comparison_section_settings',
            [
                'label' => esc_html__( 'Settings', 'elementskit-lite' ),
            ]
        );
        $this->add_control(
			'ekit_img_comparison_offset',
			[
				'label' => esc_html__( 'Offset', 'elementskit-lite' ),
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
                'description' => esc_html__('How much of the before image is visible when the page loads', 'elementskit-lite'),
			]
		);
        $this->add_control(
            'ekit_img_comparison_overlay',
            [
                'label' => esc_html__( 'Remove overlay?', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => true,
                'default' => false,
                'description' => esc_html__('Do not show the overlay with before and after', 'elementskit-lite'),
            ]
        );
        $this->add_control(
            'ekit_img_comparison_move_slider_on_hover',
            [
                'label' => esc_html__( 'Move slider on hover?', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => true,
                'default' => false,
                'description' => esc_html__('Move slider on mouse hover?', 'elementskit-lite'),
            ]
        );
        $this->add_control(
            'ekit_img_comparison_click_to_move',
            [
                'label' => esc_html__( 'Click to move?', 'elementskit-lite' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' => esc_html__( 'No', 'elementskit-lite' ),
                'return_value' => true,
                'default' => false,
                'description' => esc_html__('Allow a user to click (or tap) anywhere on the image to move the slider to that location.', 'elementskit-lite'),
            ]
        );
        $this->end_controls_section();

        /**
		 * General Style Section
		 */
		$this->start_controls_section(
			'ekit_img_comparison_general_style',
			array(
				'label'      => esc_html__( 'General', 'elementskit-lite' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'ekit_img_comparison_container_border',
				'label'       => esc_html__( 'Border', 'elementskit-lite' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'  => '{{WRAPPER}} .elementskit-image-comparison',
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-image-comparison' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-image-comparison' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'ekit_img_comparison_container_box_shadow',
				'exclude' => array('box_shadow_position'), // PHPCS:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
				'selector' => '{{WRAPPER}} .elementskit-image-comparison',
			)
		);

		$this->end_controls_section();

		/**
		 * Label Style Section
		 */
		$this->start_controls_section(
			'ekit_img_comparison_label_style',
			array(
				'label'      => esc_html__( 'Label', 'elementskit-lite' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => ['ekit_img_comparison_overlay!' => 'true'],
			)
		);

		$this->start_controls_tabs( 'ekit_img_comparison_tabs_label_styles' );

		$this->start_controls_tab(
			'ekit_img_comparison_tab_label_before',
			array(
				'label' => esc_html__( 'Before', 'elementskit-lite' ),
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_before_label_color',
			array(
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-before-label:before' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-after-label:before' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ekit_img_comparison_before_label_typography_group',
				'selector' => '{{WRAPPER}} .elementskit-image-comparison .twentytwenty-before-label:before',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ekit_img_comparison_before_label_background_group',
				'selector' => '{{WRAPPER}} .elementskit-image-comparison .twentytwenty-before-label:before',
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_before_label_margin',
			array(
				'label'      => esc_html__( 'Margin', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-before-label:before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_before_label_padding',
			array(
				'label'      => esc_html__( 'Padding', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-before-label:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_img_comparison_tab_label_after',
			array(
				'label' => esc_html__( 'After', 'elementskit-lite' ),
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_after_label_color',
			array(
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-after-label:before' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ekit_img_comparison_after_label_typography_group',
				'selector' => '{{WRAPPER}} .elementskit-image-comparison .twentytwenty-after-label:before',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ekit_img_comparison_after_label_background_group',
				'selector' => '{{WRAPPER}} .elementskit-image-comparison .twentytwenty-after-label:before',
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_after_label_margin',
			array(
				'label'      => esc_html__( 'Margin', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-after-label:before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_after_label_padding',
			array(
				'label'      => esc_html__( 'Padding', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-after-label:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Handle Style Section
		 */
		$this->start_controls_section(
			'ekit_img_comparison_handle_style',
			array(
				'label'      => esc_html__( 'Handle', 'elementskit-lite' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_handle_control_width',
			array(
				'label'      => esc_html__( 'Control Width', 'elementskit-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle'  => 'width: {{SIZE}}{{UNIT}}; margin-left: calc( {{SIZE}}{{UNIT}} / -2 );',
				)
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_handle_control_height',
			array(
				'label'      => esc_html__( 'Height', 'elementskit-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle' => 'height: {{SIZE}}{{UNIT}};margin-top: calc( {{SIZE}}{{UNIT}} / -2 );',
				)
			)
		);

		$this->start_controls_tabs( 'ekit_img_comparison_tabs_handle_styles' );

		$this->start_controls_tab(
			'ekit_img_comparison_tab_handle_normal',
			array(
				'label' => esc_html__( 'Normal', 'elementskit-lite' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ekit_img_comparison_handle_control_background_group',
				'selector' => '{{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle',
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_handle_arrow_color',
			array(
				'label' => esc_html__( 'Arrow Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default'     => '#000',
				'selectors' => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle .twentytwenty-left-arrow' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}',
				),
				'condition' => [
					'ekit_img_comparison_container_style' => 'horizontal'
				]
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_handle_arrow_color_vertical',
			array(
				'label' => esc_html__( 'Arrow Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
                'default'     => '#000',
				'selectors' => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle .twentytwenty-up-arrow' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle .twentytwenty-down-arrow' => 'border-top-color: {{VALUE}}',
				),
				'condition' => [
					'ekit_img_comparison_container_style' => 'vertical'
				]
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'ekit_img_comparison_handle_control_box_shadow_group',
				'selector' => '{{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_img_comparison_tab_handle_hover',
			array(
				'label' => esc_html__( 'Hover', 'elementskit-lite' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ekit_img_comparison_handle_control_background_hover_group',
				'selector' => '{{WRAPPER}} .elementskit-image-comparison:hover .twentytwenty-handle',
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_handle_arrow_color_hover',
			array(
				'label' => esc_html__( 'Arrow Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,

				'selectors' => array(
					'{{WRAPPER}} .elementskit-image-comparison:hover .twentytwenty-handle .twentytwenty-left-arrow' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .elementskit-image-comparison:hover .twentytwenty-handle .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}',
				),
				'condition' => [
					'ekit_img_comparison_container_style' => 'horizontal'
				]
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_handle_arrow_color_hover_vertical',
			array(
				'label' => esc_html__( 'Arrow Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,

				'selectors' => array(
					'{{WRAPPER}} .elementskit-image-comparison:hover .twentytwenty-handle .twentytwenty-up-arrow' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .elementskit-image-comparison:hover .twentytwenty-handle .twentytwenty-down-arrow' => 'border-top-color: {{VALUE}}',
				),
				'condition' => [
					'ekit_img_comparison_container_style' => 'vertical'
				]
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'ekit_img_comparison_handle_control_box_shadow_hover_group',
				'selector' => '{{WRAPPER}} .elementskit-image-comparison:hover .twentytwenty-handle',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_img_comparison_handle_divider_margin',
			array(
				'label'      => esc_html__( 'Margin', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_handle_divider_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ekit_img_comparison_heading_handle_divider_style',
			array(
				'label'     => esc_html__( 'Handle Divider', 'elementskit-lite' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_handle_divider_width',
			array(
				'label'      => esc_html__( 'Divider Thickness', 'elementskit-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:after' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:after' => 'height: {{SIZE}}{{UNIT}};',
				)
			)
		);

		$this->add_responsive_control(
			'ekit_img_comparison_handle_divider_color',
			array(
				'label'   => esc_html__( 'Divider Color', 'elementskit-lite' ),
				'type'    => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle:before, {{WRAPPER}} .elementskit-image-comparison .twentytwenty-handle:after' => 'background-color: {{VALUE}};',
				),
			)
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

		if($settings['ekit_img_comparison_container_style'] == 'vertical') :
			$this->add_render_attribute( 'image_comparison_wrapper', 'class', 'elementskit-image-comparison image-comparison-container-vertical');
		else :
			$this->add_render_attribute( 'image_comparison_wrapper', 'class', 'elementskit-image-comparison image-comparison-container');
		endif;

		$this->add_render_attribute(
			'image_comparison_wrapper',
			[
				'data-offset' => esc_attr($settings['ekit_img_comparison_offset']['size'] / 100),
				'data-overlay' => esc_attr($settings['ekit_img_comparison_overlay']),
				'data-label_after' => esc_attr($settings['ekit_img_comparison_label_after']),
				'data-label_before' => esc_attr($settings['ekit_img_comparison_label_before']),
				'data-move_slider_on_hover' => esc_attr($settings['ekit_img_comparison_move_slider_on_hover']),
				'data-click_to_move' => esc_attr($settings['ekit_img_comparison_click_to_move']),
			]
		);

		$image_html = '';
		if ( ! empty( $settings['ekit_img_comparison_image_before']['url'] ) ) {
			$image_html .= Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'ekit_img_comparison_image_before' );
		}

		$image_before_html = '';
		if ( ! empty( $settings['ekit_img_comparison_image_after']['url'] ) ) {
			$image_html .= Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'ekit_img_comparison_image_after' );
		}
		?>

		<div <?php $this->print_render_attribute_string( 'image_comparison_wrapper' ); ?>>
			<?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
		</div>

	<?php }
}