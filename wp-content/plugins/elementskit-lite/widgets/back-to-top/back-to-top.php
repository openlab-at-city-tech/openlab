<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Back_To_Top_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Back_To_Top extends Widget_Base {
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
		return 'https://wpmet.com/doc/back-to-top/';
	}

	public function get_script_depends() {
		return ['animate-circle'];
	}

    protected function register_controls() {
        /* 
        Content Tab section 
        ---------------------
        -> Select appearane of back to top button
        -> Icon control
        -> Text control
        -> Aligment
        */ 
        
		$this->start_controls_section(
			'ekit_back_to_top_content_section',
			[
                'label' => esc_html__( 'Layout and Content', 'elementskit-lite' )
            ]
		);

      $this->add_control(
			'ekit_button_appearance',
			[
				'label' => esc_html__( 'Appearance', 'elementskit-lite' ),
				'type'  => Controls_Manager::SELECT,
				'default' => 'icon_only',
				'options' => [
					'icon_only'  => esc_html__( 'Icon Only', 'elementskit-lite' ),
					'text_only'  => esc_html__( 'Text Only', 'elementskit-lite' ),
					'progress_indicator'  => esc_html__( 'Progress Indicator', 'elementskit-lite' ),
				],
			]
		);

        // back to top icon show when user select icon only appearance
      $this->add_control(
			'ekit_btn_icons',
			[
            'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'type'  => Controls_Manager::ICONS,
				'default'       => [
					'value'     => 'fas fa-arrow-up',
					'library'   => 'fa-solid',
				],
				'condition'	=> [
					'ekit_button_appearance' => ['icon_only', 'progress_indicator']
				]
			]
		);

        // back to top text input control when user select text only appearance
        $this->add_control(
			'ekit_btn_text',
			[
				'label'       => esc_html__( 'Button Text', 'elementskit-lite' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'Top', 'elementskit-lite' ),
				'placeholder' => esc_html__( 'Type button label here', 'elementskit-lite' ),
            	'condition'   => [
					'ekit_button_appearance' => 'text_only'
				]
			]
		);

        
		$this->add_responsive_control(
			'ekit_button_alignment',
			[
				'label'     => esc_html__('Alignment', 'elementskit-lite'),
				'type'      => Controls_Manager::CHOOSE,
                'default'   => 'left',
				'options'   => [
					'left'   => [
						'description' => esc_html__('Left', 'elementskit-lite'),
						'icon'        => 'eicon-text-align-left',
					],
					'center' => [
						'description' => esc_html__('Center', 'elementskit-lite'),
						'icon'        => 'eicon-text-align-center',
					],
					'right'  => [
						'description' => esc_html__('Right', 'elementskit-lite'),
						'icon'        => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-btt' => 'text-align: {{VALUE}};',
				]
			]
		);


        $this->end_controls_section(); // end of content tab section

        /* ---------------------
            Settings Tab 
            -> Scroll Top Offset
        ------------------------*/ 
        $this->start_controls_section(
			'ekit_back_to_top_setting_section',
			[
                'label' => esc_html__( 'Setting', 'elementskit-lite' )
            ]
		);

        $this->add_control(
			'ekit_offset_top',
			[
				'label' => esc_html__( 'Offset Top (px)', 'elementskit-lite' ),
				'type'  => Controls_Manager::NUMBER,
				'min'   => 0,
				'step'  => 1,
				'default' => 0,
			]
		);

		$this->add_control(
			'ekit_show_button_after_switch',
			[
				'label'	=> esc_html__( 'Show button on scroll', 'elementskit-lite' ),
				'type'	=> Controls_Manager::SWITCHER,
				'label_on'	=> esc_html__( 'Yes', 'elementskit-lite' ),
				'label_off' => esc_html__( 'No', 'elementskit-lite' ),
				'default' => 'no',
			]
		);

      $this->add_control(
			'ekit_show_button_after',
			[
				'label' => esc_html__( 'Enter scrolled value (px)', 'elementskit-lite' ),
				'type'  => Controls_Manager::NUMBER,
				'min'   => 0,
				'step'  => 1,
				'default' => 400,
				'condition'	=> [
					'ekit_show_button_after_switch' => 'yes'
				]
			]
		);

        $this->end_controls_section(); // end of content tab section

        /* -------------------------
            back to top style tab
            -> Typogaphy
            -> Width
            -> Height
            -> Border radius
            -> Border control
				-> Stroke foreground and backgorund color
				-> Size of button (width and height together)
        	----------------------------*/ 
        	$this->start_controls_section(
			'ekit_back_to_top_style_section',
			[
                'label' => esc_html__( 'Button Style', 'elementskit-lite' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
			);

        	$this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
               'name'           => 'ekit_btn_typography',
               'label'          => esc_html__('Typography', 'elementskit-lite'),
               'selector'       => '{{WRAPPER}} .ekit-btt__button',
               'exclude'  => ['letter_spacing', 'font_style', 'text_decoration', 'line_height'], // PHPCS:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
               'fields_options'  => [
						'typography'  => [
							'default' => 'custom',
						],
						'font_weight' => [
							'default' => '400',
						],
						'font_size'    => [
							'default'  => [
									'size' => '16',
									'unit' => 'px'
							],
							'size_units' => ['px']
						],
						'text_transform' => [
							'default' => 'uppercase',
						],
               ],
            )
        );

			$this->add_control(
			'ekit_button_size',
			[
				'label'      => esc_html__('Button Size (px)', 'elementskit-lite'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-btt__button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'ekit_button_appearance' => 'progress_indicator'
				]
			]
			);

      	$this->add_control(
			'ekit_button_width',
			[
				'label'      => esc_html__('Width (px)', 'elementskit-lite'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-btt__button' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'ekit_button_appearance!' => 'progress_indicator'
				]
			]
			);

		

        $this->add_control(
			'ekit_button_height',
			[
				'label'      => esc_html__('Height (px)', 'elementskit-lite'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-btt__button' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'ekit_button_appearance!' => 'progress_indicator'
				]
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_button_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-btt__button',
            	'exclude' => ['border_color'], // PHPCS:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
				'condition'	=> [
					'ekit_button_appearance!' => 'progress_indicator'
				]
			]
		);

        $this->add_responsive_control(
			'ekit_button_radius',
			[
				'label'      => esc_html__('Border Radius', 'elementskit-lite'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default'    => [
					'unit'     => 'px',
					'top'      => 50,
					'right'    => 50,
					'bottom'   => 50,
					'left'     => 50,
					'isLinked' => true
				],
				'selectors'  => [
					'{{WRAPPER}} :is( .ekit-btt__button, #canvas )' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
					'ekit_button_appearance!' => 'progress_indicator'
				]
			]
		);

		$this->add_control(
			'ekit_button_prgoress_foreground',
			[
				'label'     => esc_html__('Line Foreground color', 'elementskit-lite'),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'default'	=> '#FF5050',
				'condition'	=> [
					'ekit_button_appearance' => 'progress_indicator'
				]
			]
		);

		$this->add_control(
			'ekit_button_prgoress_background',
			[
				'label'     => esc_html__('Line Background Color', 'elementskit-lite'),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'default'	=> '#eee',
				'condition'	=> [
					'ekit_button_appearance' => 'progress_indicator'
				]
			]
		);

      $this->start_controls_tabs('ekit_button_tabs');

		$this->start_controls_tab(
			'ekit_button_normal',
			[
				'label' => esc_html__('Normal', 'elementskit-lite'),
			]
		);

		$this->add_control(
			'ekit_button_normal_color',
			[
				'label'     => esc_html__('Color', 'elementskit-lite'),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-btt__button' => 'color: {{VALUE}}; border-color: {{VALUE}}'
				],
			]
		);

		$this->add_control(
			'ekit_button_normal_bg_color',
			[
				'label'     => esc_html__('Background', 'elementskit-lite'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-btt__button' => 'background: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_button_hover',
			[
				'label' => esc_html__('Hover', 'elementskit-lite'),
			]
		);

		$this->add_control(
			'ekit_button_hover_clr',
			[
				'label'     => esc_html__('Color', 'elementskit-lite'),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-btt__button:hover' => 'color: {{VALUE}}; border-color: {{VALUE}}',
					'{{WRAPPER}} .ekit-btt__button:focus' => 'color: {{VALUE}}; border-color: {{VALUE}}'
				],
			]
		);

		$this->add_control(
			'ekit_button_hover_bg_clr',
			[
				'label'     => esc_html__('Background', 'elementskit-lite'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-btt__button:hover' => 'background: {{VALUE}}',
					'{{WRAPPER}} .ekit-btt__button:focus' => 'background: {{VALUE}}'
				],
			]
		);


		$this->end_controls_tab();
		$this->end_controls_tabs();

        $this->end_controls_section(); // end of back to style tab
   }

	protected function render( ) {
		echo '<div class="ekit-wid-con" >';
			$this->render_raw();
		echo '</div>';
	}

	protected function render_raw() {
		$settings = $this->get_settings_for_display();
		$appearance = $settings['ekit_button_appearance'];
		$is_scroll = $settings['ekit_show_button_after_switch'] === 'yes' ? 'yes' : '';

		$args = [
			'offset_top' => $settings['ekit_offset_top'],
			'show_after' => $settings['ekit_show_button_after'],
				'show_scroll' =>  $is_scroll, 
				'style' => $appearance,
				'foreground' => $settings['ekit_button_prgoress_foreground'],
				'background' => $settings['ekit_button_prgoress_background']
		]
		?>
			<div class="ekit-back-to-top-container ekit-btt <?php echo esc_attr( $appearance ) ?>" data-settings="<?php echo esc_attr( wp_json_encode($args) ) ?>"> 
				<span class="ekit-btt__button <?php echo esc_attr( $is_scroll ) ?>">
					<?php // start container
					switch( $appearance ) {
						// show icon style by default 
						case 'icon_only':
							Icons_Manager::render_icon( $settings['ekit_btn_icons'], [ 'aria-hidden' => 'true' ] );
							break;
						
						// show text only style
						case 'text_only':
							echo esc_html($settings['ekit_btn_text']);
							break;

						// show progress indicator style (pro feature)
						case 'progress_indicator': ?>
							<div class="progress_indicator" >
								<canvas id="canvas-<?php echo esc_attr( $this->get_id()); ?>" class="canvas" data-canvas="<?php echo esc_attr( $this->get_id()); ?>"></canvas>
								<span><?php Icons_Manager::render_icon( $settings['ekit_btn_icons'], [ 'aria-hidden' => 'true' ] ); ?></span>
							</div>
							<?php break;
					} ?>
				</span>
			</div>
		<?php // end container
	}
}
