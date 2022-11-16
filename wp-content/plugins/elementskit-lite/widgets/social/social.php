<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Social_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Social extends Widget_Base {
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
        return 'https://wpmet.com/doc/social-media-widget/';
    }

	protected function register_controls() {

		// start content section for social media
        $this->start_controls_section(
            'ekit_socialmedia_section_tab_content',
            [
                'label' => esc_html__('Social Icons', 'elementskit-lite'),
            ]
        );

        $this->add_control(
			'ekit_socialmedia_style',
			[
				'label' => esc_html__( 'Choose Style', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon'  => esc_html__( 'Icon', 'elementskit-lite' ),
					'text' => esc_html__( 'Text', 'elementskit-lite' ),
					'both' => esc_html__( 'Both', 'elementskit-lite' ),
				],
			]
        );

        $this->add_control(
			'ekit_socialmedia_style_icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before'  => esc_html__( 'Before', 'elementskit-lite' ),
					'after' => esc_html__( 'After', 'elementskit-lite' ),
                ],
                'condition' => [
                    'ekit_socialmedia_style' => 'both'
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_socialmedia_icon_padding_right',
			[
				'label' => esc_html__( 'Spacing Right', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} a > i' => 'padding-right: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedia_style' => 'both',
                    'ekit_socialmedia_style_icon_position' => 'before',
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedia_icon_padding_left',
			[
				'label' => esc_html__( 'Spacing Left', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} a > i' => 'padding-left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedia_style' => 'both',
                    'ekit_socialmedia_style_icon_position' => 'after',
                ]
			]
		);

        $this->add_responsive_control(
            'ekit_socialmedai_list_align',
            [
                'label' => esc_html__( 'Alignment', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
				'selectors' => [
                    '{{WRAPPER}} .ekit_social_media' => 'text-align: {{VALUE}};',
                ],
            ]
        );

		$socialMedia = new Repeater();

		// set social icon
        $socialMedia->add_control(
            'ekit_socialmedia_icons',
            [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
                'label_block' => true,
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_socialmedia_icon',
                'default' => [
                    'value' => 'icon icon-facebook',
                    'library' => 'ekiticons',
                ]
            ]
        );

		// set social icon label
        $socialMedia->add_control(
            'ekit_socialmedia_label',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => 'Facebook',
            ]
        );

		// set social link
        $socialMedia->add_control(
            'ekit_socialmedia_link',
            [
                'label' => esc_html__( 'Link', 'elementskit-lite' ),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => 'https://facebook.com',
                ],
            ]
        );

		// start tab for content
		$socialMedia->start_controls_tabs(
            'ekit_socialmedia_tabs'
        );

		// start normal tab
        $socialMedia->start_controls_tab(
            'ekit_socialmedia_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

		// set social icon color
        $socialMedia->add_responsive_control(
			'ekit_socialmedia_icon_color',
			[
				'label' =>esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#222222',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} > a' => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} > a svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		// set social icon background color
        $socialMedia->add_responsive_control(
			'ekit_socialmedia_icon_bg_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} > a' => 'background-color: {{VALUE}};',
				],
			]
        );

        $socialMedia->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_socialmedia_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a',
			]
		);

         $socialMedia->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'ekit_socialmedia_icon_normal_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a',
			]
        );

        $socialMedia->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'       => 'ekit_socialmedai_list_box_shadow',
                'selector'   => '{{WRAPPER}} {{CURRENT_ITEM}} > a',
            ]
        );

		$socialMedia->end_controls_tab();
		// end normal tab

		//start hover tab
		$socialMedia->start_controls_tab(
            'ekit_socialmedia_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

		// set social icon color
        $socialMedia->add_responsive_control(
			'ekit_socialmedia_icon_hover_color',
			[
				'label' =>esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} > a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} > a:hover svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		// set social icon background color
        $socialMedia->add_responsive_control(
			'ekit_socialmedia_icon_hover_bg_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#3b5998',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} > a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);


		$socialMedia->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'ekit_socialmedia_icon_hover_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover',
			]
        );

        $socialMedia->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'       => 'ekit_socialmedai_list_box_shadow_hover',
                'selector'   => '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover',
            ]
        );

        $socialMedia->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_socialmedia_border_hover',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover',
			]
		);

		$socialMedia->end_controls_tab();
		//end hover tab

		$socialMedia->end_controls_tabs();


		// set social icon add new control
        $this->add_control(
            'ekit_socialmedia_add_icons',
            [
                'label' => esc_html__('Add Social Media', 'elementskit-lite'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $socialMedia->get_controls(),
                'default' => [
                    [
                        'ekit_socialmedia_icons' => [
							'value'	=> 'icon icon-facebook',
							'library'	=> 'ekiticons'
						],
                        'ekit_socialmedia_label' => 'Facebook',
                        'ekit_socialmedia_icon_hover_bg_color' => '#3b5998',
                    ],
					[
                        'ekit_socialmedia_icons' => [
							'value'	=> 'icon icon-twitter',
							'library'	=> 'ekiticons'
						],
                        'ekit_socialmedia_label' => 'Twitter',
						'ekit_socialmedia_icon_hover_bg_color' => '#1da1f2',
                    ],
					[
                        'ekit_socialmedia_icons' => [
							'value'	=> 'icon icon-linkedin',
							'library'	=> 'ekiticons'
						],
                        'ekit_socialmedia_label' => 'LinkedIn',
						'ekit_socialmedia_icon_hover_bg_color' => '#0077b5',
                    ],
                ],
                'title_field' => '{{{ ekit_socialmedia_label }}}',

            ]
        );

		$this->end_controls_section();
		// end content section

	// start style section control

		// start Social media tab
		 $this->start_controls_section(
            'ekit_socialmedia_section_tab_style',
            [
                'label' => esc_html__('Social Media', 'elementskit-lite'),
				 'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
		// Alignment
        $this->add_responsive_control(
            'ekit_socialmedai_list_item_align',
            [
                'label' => esc_html__( 'Alignment', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'elementskit-lite' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
				'selectors' => [
                    '{{WRAPPER}} .ekit_social_media > li > a' => 'text-align: {{VALUE}};',
                ],
            ]
        );

		// Display design
		 $this->add_responsive_control(
            'ekit_socialmedai_list_display',
            [
                'label' => esc_html__( 'Display', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'inline-block',
                'options' => [
                    'inline-block' => esc_html__( 'Inline Block', 'elementskit-lite' ),
                    'block' => esc_html__( 'Block', 'elementskit-lite' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit_social_media > li' => 'display: {{VALUE}};',
                ],
            ]
        );

		// text decoration
		 $this->add_responsive_control(
            'ekit_socialmedai_list_decoration_box',
            [
                'label' => esc_html__( 'Decoration', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
				'default' => 'none',
                'options' => [
                    'none' => esc_html__( 'None', 'elementskit-lite' ),
                    'underline' => esc_html__( 'Underline', 'elementskit-lite' ),
                    'overline' => esc_html__( 'Overline', 'elementskit-lite' ),
                    'line-through' => esc_html__( 'Line Through', 'elementskit-lite' ),

                ],
                'selectors' => ['{{WRAPPER}} .ekit_social_media > li > a' => 'text-decoration: {{VALUE}};'],
            ]
        );

		// border radius
		 $this->add_responsive_control(
            'ekit_socialmedai_list_border_radius',
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
                    '{{WRAPPER}} .ekit_social_media > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		// Padding style

		 $this->add_responsive_control(
            'ekit_socialmedai_list_padding',
            [
                'label'         => esc_html__('Padding', 'elementskit-lite'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ekit_social_media > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		// margin style

		$this->add_responsive_control(
            'ekit_socialmedai_list_margin',
            [
                'label'         => esc_html__('Margin', 'elementskit-lite'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px', 'em'],
				'default' => [
					'top' => '5',
					'right' => '5',
					'bottom' => '5' ,
					'left' => '5',
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit_social_media > li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
		);
		
		$this->add_responsive_control(
            'ekit_socialmedai_list_icon_size',
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
                    '{{WRAPPER}} .ekit_social_media > li > a i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit_social_media > li > a svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_socialmedai_list_typography',
				'label' => esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit_social_media > li > a',
			]
		);

        $this->add_control(
			'ekit_socialmedai_list_style_use_height_and_width',
			[
				'label' => esc_html__( 'Use Height Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);


        $this->add_responsive_control(
			'ekit_socialmedai_list_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
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
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_social_media > li > a' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
					'ekit_socialmedai_list_style_use_height_and_width' => 'yes',
					'ekit_socialmedia_style' => 'icon',
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedai_list_height',
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
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_social_media > li > a' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedai_list_style_use_height_and_width' => 'yes',
					'ekit_socialmedia_style' => 'icon',
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_socialmedai_list_line_height',
			[
				'label' => esc_html__( 'Line Height', 'elementskit-lite' ),
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
					'size' => 28,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_social_media > li > a' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialmedai_list_style_use_height_and_width' => 'yes'
                ]
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
		   extract($settings);

		 ?>
			 <ul class="ekit_social_media">
				<?php foreach ($ekit_socialmedia_add_icons as $key => $icon): ?>
					<?php if($icon['ekit_socialmedia_icons'] != ''):

						if ( ! empty( $icon['ekit_socialmedia_link']['url'] ) ) {
							$this->add_link_attributes( 'button-' . $key, $icon['ekit_socialmedia_link'] );
						}
						
					?>
					<li class="elementor-repeater-item-<?php echo esc_attr( $icon[ '_id' ] ); ?>">
					    <a
						<?php echo $this->get_render_attribute_string(  'button-' . $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor

						// new icon
						$migrated = isset( $icon['__fa4_migrated']['ekit_socialmedia_icons'] );
						// Check if its a new widget without previously selected icon using the old Icon control
						$is_new = empty( $icon['ekit_socialmedia_icon'] );



						$getClass = explode('-', ($is_new || $migrated) ? $icon['ekit_socialmedia_icons']['library'] != 'svg' ? $icon['ekit_socialmedia_icons']['value'] : '' : $icon['ekit_socialmedia_icon'] );
						 $iconClass = !empty($getClass) ? end($getClass) : ''; ?> class="<?php echo esc_attr( $iconClass ); ?>" >
							<?php if($settings['ekit_socialmedia_style'] != 'text' && $settings['ekit_socialmedia_style_icon_position'] == 'before'): ?>
							
							<?php
								if ( $is_new || $migrated ) {
									// new icon
									Icons_Manager::render_icon( $icon['ekit_socialmedia_icons'], [ 'aria-hidden' => 'true' ] );
								} else {
									?>
									<i class="<?php echo esc_attr($icon['ekit_socialmedia_icon']); ?>" aria-hidden="true"></i>
									<?php
								}
							?>
									
                            <?php endif; ?>
                            <?php if($settings['ekit_socialmedia_style'] != 'icon' ): ?>
                            <?php echo esc_html($icon['ekit_socialmedia_label'])?>
                            <?php endif; ?>
                            <?php if($settings['ekit_socialmedia_style'] != 'text' && $settings['ekit_socialmedia_style_icon_position'] == 'after'): ?>
							
							<?php
								
								if ( $is_new || $migrated ) {
									// new icon
									Icons_Manager::render_icon( $icon['ekit_socialmedia_icons'], [ 'aria-hidden' => 'true' ] );
								} else {
									?>
									<i class="<?php echo esc_attr($icon['ekit_socialmedia_icon']); ?>" aria-hidden="true"></i>
									<?php
								}
							?>
							
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php
  	}
}
