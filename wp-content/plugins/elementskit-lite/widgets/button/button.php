<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Button_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Button extends Widget_Base {
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
        return 'https://wpmet.com/doc/button/';
    }

    protected function register_controls() {


		$this->start_controls_section(
			'ekit_btn_section_content',
			array(
				'label' => esc_html__( 'Content', 'elementskit-lite' ),
			)
		);

		$this->add_control(
			'ekit_btn_text',
			[
				'label' =>esc_html__( 'Label', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'default' =>esc_html__( 'Learn more ', 'elementskit-lite' ),
				'placeholder' =>esc_html__( 'Learn more ', 'elementskit-lite' ),
				'dynamic' => [
                    'active' => true,
                ],
			]
		);


		$this->add_control(
			'ekit_btn_url',
			[
				'label' =>esc_html__( 'URL', 'elementskit-lite' ),
				'type' => Controls_Manager::URL,
				'placeholder' =>esc_url('https://wpmet.com'),
				'dynamic' => [
                    'active' => true,
                ],
				'default' => [
					'url' => '#',
				],
			]
		);

        $this->add_control(
            'ekit_btn_section_settings',
            [
                'label' => esc_html__( 'Settings', 'elementskit-lite' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);
		
		$this->add_control(
            'ekit_btn_icons__switch',
            [
                'label' => esc_html__('Add icon? ', 'elementskit-lite'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' =>esc_html__( 'Yes', 'elementskit-lite' ),
                'label_off' =>esc_html__( 'No', 'elementskit-lite' ),
            ]
		);
		
		$this->add_control(
			'ekit_btn_icons',
			[
				'label' =>esc_html__( 'Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_btn_icon',
				'label_block' => true,
				'default' => [
                    'value' => '',
				],
				'condition'	=> [
					'ekit_btn_icons__switch'	=> 'yes'
				]
			]
		);
        $this->add_control(
            'ekit_btn_icon_align',
            [
                'label' =>esc_html__( 'Icon Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' =>esc_html__( 'Before', 'elementskit-lite' ),
                    'right' =>esc_html__( 'After', 'elementskit-lite' ),
                ],
                'condition'	=> [
					'ekit_btn_icons__switch'	=> 'yes'
				]
            ]
        );
		$this->add_responsive_control(
			'ekit_btn_align',
			[
				'label' =>esc_html__( 'Alignment', 'elementskit-lite' ),
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
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ekit-btn-wraper' => 'text-align: {{VALUE}};',
				],
			]
		);
	    $this->add_control(
		    'ekit_btn_class',
		    [
			    'label' => esc_html__( 'Class', 'elementskit-lite' ),
			    'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			    'placeholder' => esc_html__( 'Class Name', 'elementskit-lite' ),
		    ]
	    );

	    $this->add_control(
		    'ekit_btn_id',
		    [
			    'label' => esc_html__( 'id', 'elementskit-lite' ),
			    'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			    'placeholder' => esc_html__( 'ID', 'elementskit-lite' ),
		    ]
	    );


		$this->end_controls_section();


        $this->start_controls_section(
			'ekit_btn_section_style',
			[
				'label' =>esc_html__( 'Button', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label'			=> esc_html__( 'Width (%)', 'elementskit-lite' ),
				'type'			=> Controls_Manager::SLIDER,
				'selectors'		=> [
					'{{WRAPPER}} .elementskit-btn' => 'width: {{SIZE}}%;',
				]
			]
		);

		$this->add_responsive_control(
			'ekit_btn_text_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementskit-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_btn_typography',
				'label' =>esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementskit-btn',
			]
		);

        $this->add_group_control(
        	Group_Control_Text_Shadow::get_type(),
        	[
        		'name' => 'ekit_btn_shadow',
        		'selector' => '{{WRAPPER}} .elementskit-btn',
        	]
        );

		$this->start_controls_tabs( 'ekit_btn_tabs_style' );

		$this->start_controls_tab(
			'ekit_btn_tabnormal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_btn_text_color',
			[
				'label' =>esc_html__( 'Text Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementskit-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-btn svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_btn_bg_color',
				'default' => '',
				'selector' => '{{WRAPPER}} .elementskit-btn',
            )
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_btn_tab_button_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_btn_hover_color',
			[
				'label' =>esc_html__( 'Text Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .elementskit-btn:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementskit-btn:hover svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

	    $this->add_group_control(
		    Group_Control_Background::get_type(),
		    array(
			    'name'     => 'ekit_btn_bg_hover_color',
			    'default' => '',
			    'selector' => '{{WRAPPER}} .elementskit-btn:hover',
		    )
	    );

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

        $this->start_controls_section(
			'ekit_btn_border_style_tabs',
			[
				'label' =>esc_html__( 'Border', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_btn_border_style',
			[
				'label' => esc_html_x( 'Border Type', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__( 'None', 'elementskit-lite' ),
					'solid' => esc_html_x( 'Solid', 'Border Control', 'elementskit-lite' ),
					'double' => esc_html_x( 'Double', 'Border Control', 'elementskit-lite' ),
					'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementskit-lite' ),
					'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementskit-lite' ),
					'groove' => esc_html_x( 'Groove', 'Border Control', 'elementskit-lite' ),
				],
				'default'	=> 'none',
				'selectors' => [
					'{{WRAPPER}} .elementskit-btn' => 'border-style: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_btn_border_dimensions',
			[
				'label' 	=> esc_html_x( 'Width', 'Border Control', 'elementskit-lite' ),
				'type' 		=> Controls_Manager::DIMENSIONS,
				'condition'	=> [
					'ekit_btn_border_style!' => 'none'
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-btn' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'xs_tabs_button_border_style' );
		$this->start_controls_tab(
			'ekit_btn_tab_border_normal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_btn_border_color',
			[
				'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementskit-btn' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_btn_border_radius',
			[
				'label' =>esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '' ,
					'left' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-btn' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_btn_tab_button_border_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
			]
		);
		$this->add_control(
			'ekit_btn_hover_border_color',
			[
				'label' => esc_html_x( 'Color', 'Border Control', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementskit-btn:hover' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_btn_border_radius_h',
			[
				'label' =>esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .elementskit-btn:hover' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

        $this->start_controls_section(
			'ekit_btn_box_shadow_style',
			[
				'label' =>esc_html__( 'Shadow', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
			  'name' => 'ekit_btn_box_shadow_group',
			  'selector' => '{{WRAPPER}} .elementskit-btn',
			]
		);


		$this->end_controls_section();

        $this->start_controls_section(
			'ekit_btn_iconw_style',
			[
				'label' =>esc_html__( 'Icon', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'ekit_btn_icons__switch'	=> 'yes'
				]
			]
		);
		$this->add_responsive_control(
			'ekit_btn_normal_icon_font_size',
			array(
				'label'      => esc_html__( 'Font Size', 'elementskit-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'rem',
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementskit-btn > i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementskit-btn > svg'	=> 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'ekit_btn_normal_icon_padding_left',
			[
				'label' => esc_html__( 'Add space after icon', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-btn > i, {{WRAPPER}} .elementskit-btn > svg' => 'margin-right: {{SIZE}}{{UNIT}};',
					'.rtl {{WRAPPER}} .elementskit-btn > i, .rtl {{WRAPPER}} .elementskit-btn > svg' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',
				],
				'condition' => [
					'ekit_btn_icon_align' => 'left'
				]
			]
		);
		$this->add_responsive_control(
			'ekit_btn_normal_icon_padding_right',
			[
				'label' => esc_html__( 'Add space before icon', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' =>1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .elementskit-btn > i, {{WRAPPER}} .elementskit-btn > svg' => 'margin-left: {{SIZE}}{{UNIT}};',
					'.rtl {{WRAPPER}} .elementskit-btn > i, .rtl {{WRAPPER}} .elementskit-btn > svg' => 'margin-left: 0; margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_btn_icon_align' => 'right'
				]
			]
		);

        $this->add_responsive_control(
            'ekit_btn_normal_icon_vertical_align',
            array(
                'label'      => esc_html__( 'Move icon  Vertically', 'elementskit-lite' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array(
                    'px', 'em', 'rem',
                ),
                'range'      => array(
                    'px' => array(
                        'min' => -20,
                        'max' => 20,
                    ),
                    'em' => array(
                        'min' => -5,
                        'max' => 5,
                    ),
                    'rem' => array(
                        'min' => -5,
                        'max' => 5,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .elementskit-btn i, {{WRAPPER}} .elementskit-btn svg' => ' -webkit-transform: translateY({{SIZE}}{{UNIT}}); -ms-transform: translateY({{SIZE}}{{UNIT}}); transform: translateY({{SIZE}}{{UNIT}})',
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

        $btn_text = $settings['ekit_btn_text'];
        $btn_class = ($settings['ekit_btn_class'] != '') ? $settings['ekit_btn_class'] : '';
        $btn_id = ($settings['ekit_btn_id'] != '') ? 'id='.$settings['ekit_btn_id'] : '';

		$options_ekit_btn_icon_align = array_keys([
			'left' => esc_html__( 'Before', 'elementskit-lite' ),
			'right' => esc_html__( 'After', 'elementskit-lite' ),
		]);

        $icon_align = \ElementsKit_Lite\Utils::esc_options($settings['ekit_btn_icon_align'], $options_ekit_btn_icon_align, 'left');

		if ( ! empty( $settings['ekit_btn_url']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['ekit_btn_url'] );
		}
		
		// Reset Whitespace for this specific widget
		$btn_class .= ' whitespace--normal';
		?>
		<div class="ekit-btn-wraper">
			<?php if($icon_align == 'right'): ?>
				<a <?php echo $this->get_render_attribute_string( 'button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?> class="elementskit-btn <?php echo esc_attr( $btn_class ); ?>" <?php echo esc_attr($btn_id); ?>>
					<?php echo esc_html( $btn_text ); ?>

					<?php
						// new icon
						$migrated = isset( $settings['__fa4_migrated']['ekit_btn_icons'] );
						// Check if its a new widget without previously selected icon using the old Icon control
						$is_new = empty( $settings['ekit_btn_icon'] );
						if ( $is_new || $migrated ) {
							// new icon
							Icons_Manager::render_icon( $settings['ekit_btn_icons'], [ 'aria-hidden' => 'true' ] );
						} else {
							?>
							<i class="<?php echo esc_attr($settings['ekit_btn_icon']); ?>" aria-hidden="true"></i>
							<?php
						}
					?>

				</a>
				<?php elseif ($icon_align == 'left') : ?>
				<a <?php echo $this->get_render_attribute_string( 'button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?> class="elementskit-btn <?php echo esc_attr( $btn_class); ?>" <?php echo esc_attr($btn_id); ?>>
					
					<?php
						// new icon
						$migrated = isset( $settings['__fa4_migrated']['ekit_btn_icons'] );
						// Check if its a new widget without previously selected icon using the old Icon control
						$is_new = empty( $settings['ekit_btn_icon'] );
						if ( $is_new || $migrated ) {
							// new icon
							Icons_Manager::render_icon( $settings['ekit_btn_icons'], [ 'aria-hidden' => 'true' ] );
						} else {
							?>
							<i class="<?php echo esc_attr($settings['ekit_btn_icon']); ?>" aria-hidden="true"></i>
							<?php
						}
					?>

					<?php echo esc_html( $btn_text ); ?>
				</a>
				<?php else : ?>
				<a <?php echo $this->get_render_attribute_string( 'button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?> class="elementskit-btn <?php echo esc_attr( $btn_class); ?>" <?php echo esc_attr($btn_id); ?>>
					<?php echo esc_html( $btn_text ); ?>
				</a>
			<?php endif; ?>
		</div>
        <?php
    }
}
