<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Header_Search_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Header_Search extends Widget_Base
{
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;

    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->add_script_depends('magnific-popup');
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
        return 'https://wpmet.com/doc/search-2/';
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'ekit_header_search',
            [
                'label' => esc_html__('Header Search', 'elementskit-lite'),
            ]
        );

        $this->add_control(
            'ekit_search_placeholder_text', [
                'label' => esc_html__('Placeholder Text', 'elementskit-lite'),
                'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'default'   => 'Search...',
                'label_block' => true,
            ]
        );


        $this->add_control(
            'ekit_search_icons',
            [
                'label' => esc_html__('Select Icon', 'elementskit-lite'),
                'fa4compatibility' => 'ekit_search_icon',
                'default' => [
                    'value' => 'icon icon-search',
                    'library' => 'ekiticons',
                ],
                'label_block' => true,
                'type' => Controls_Manager::ICONS,

            ]
        );

        $this->add_responsive_control(
            'ekit_search_icon_font_size',
            [
                'label'         => esc_html__('Font Size', 'elementskit-lite'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'em'],
                'default' => [
                    'unit' => 'px',
                    'size' => '20',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit_navsearch-button' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit_navsearch-button svg'    => 'max-width: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'ekit_header_search_section_tab_style',
            [
                'label' => esc_html__('Header Search', 'elementskit-lite'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'ekit_search_tab_search_tabs' );
            $this->start_controls_tab(
                'ekit_search_tab_search_normal',
                [
                    'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
                ]
            );
            $this->add_control(
                'ekit_searech_icon_color',
                [
                    'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navsearch-button, {{WRAPPER}} .ekit_search-button i' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .ekit_navsearch-button svg path, {{WRAPPER}} .ekit_search-button svg path'   => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ekit_seacrh_icon_bg_color',
                [
                    'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navsearch-button' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_search_tab_search_hover',
                [
                    'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
                ]
            );
            $this->add_control(
                'ekit_searech_icon_hover_color',
                [
                    'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navsearch-button:hover, {{WRAPPER}} .ekit_search-button:hover i' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .ekit_navsearch-button:hover svg path, {{WRAPPER}} .ekit_search-button:hover svg path'   => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ekit_seacrh_icon_hover_bg_color',
                [
                    'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit_navsearch-button:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
            $this->end_controls_tab();
        $this->end_controls_tabs();
		
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_border',
                'selector' => '{{WRAPPER}} .ekit_navsearch-button',
                'separator' => 'before',
            ]
        );

        // box shadow
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'       => 'ekit_header_search',
                'selector'   => '{{WRAPPER}} .ekit_navsearch-button',
            ]
        );
        // border radius
        $this->add_control(
            'ekit_header_border_radius',
            [
                'label' => esc_html__( 'Border radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit_navsearch-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_search_margin',
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
                    '{{WRAPPER}} .ekit_navsearch-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_search_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0' ,
                    'left' => '0',
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit_navsearch-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


        $this->add_control(
			'ekit_search_height_width_socher',
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
            'ekit_search_width',
            [
                'label'         => esc_html__('Width', 'elementskit-lite'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'em', '%'],
                'default' => [
                    'unit' => 'px',
                    'size' => '40',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit_navsearch-button' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_search_height_width_socher' => 'yes'
                ]
            ]
        );
        $this->add_responsive_control(
            'ekit_search_height',
            [
                'label'         => esc_html__('Height', 'elementskit-lite'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'em', '%'],
                'default' => [
                    'unit' => 'px',
                    'size' => '40',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit_navsearch-button' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_search_height_width_socher' => 'yes'
                ]
            ]
        );
        $this->add_responsive_control(
            'ekit_search_line_height',
            [
                'label'         => esc_html__('Line Height', 'elementskit-lite'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px', 'em', '%'],
                'default' => [
                    'unit' => 'px',
                    'size' => '40',
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit_navsearch-button' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_search_height_width_socher' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'ekit_search_icon_text_align',
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
                    '{{WRAPPER}} .ekit_navsearch-button' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
			'ekit_search_container_style_tabs',
			[
				'label' => __( 'Search Container', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_search_backdrop_background',
				'label' => __( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit_modal-searchPanel .ekit-search-group input:not([type="submit"])',
			]
        );

        $this->add_responsive_control(
			'ekit_search_content_title_color',
			[
				'label' => __( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_modal-searchPanel .ekit-search-group input:not([type=submit]), {{WRAPPER}} button.mfp-close' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ekit_modal-searchPanel .ekit-search-group .ekit_search-button, {{WRAPPER}} .ekit-promo-popup .mfp-close, {{WRAPPER}} .ekit_search-field' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_responsive_control(
			'ekit_search_placeholder_title_color',
			[
				'label' => __( 'Placeholder Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_search-field::-webkit-input-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit_search-field::-moz-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit_search-field:-ms-input-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit_search-field:-moz-placeholder' => 'color: {{VALUE}}',
				],
			]
        );

        $this->add_control(
			'ekit_search_border_heading',
			[
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_search_border',
				'label' => esc_html__( 'Border Type', 'elementskit-lite' ),
                'default' =>'',
				'selector' => '{{WRAPPER}} .ekit_modal-searchPanel .ekit-search-group input:not([type="submit"])',
			]
		);

        $this->add_control(
            'ekit_search_border_radius',
            [
                'label' => esc_html__( 'Border radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit_modal-searchPanel .ekit-search-group input:not([type="submit"])' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
			'ekit_search_input_height',
			[
				'label' => esc_html__( 'Height (px)', 'elementskit-lite' ),
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
					'{{WRAPPER}} .ekit_modal-searchPanel .ekit-search-group input:not([type="submit"])' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'ekit_search_input_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 350,
						'max' => 900
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_modal-searchPanel .ekit-search-panel' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'ekit_search_input_left',
			[
				'label' => esc_html__( 'Icon Left Position (px)', 'elementskit-lite' ),
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
					'{{WRAPPER}} .ekit_modal-searchPanel .ekit-search-group .ekit_search-button' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'ekit_search_background_overlay_color',
			[
				'label' => esc_html__( 'Background Overlay Color', 'elementskit-lite' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mfp-bg.ekit-promo-popup' => 'background-color: {{VALUE}}',
				],
                'separator' => 'before',
			]
		);

        $this->add_control(
            'ekit_search_box_align',
            [
                'label' => esc_html__( 'Alignment', 'elementskit-lite' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__( 'Top', 'elementskit-lite' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => esc_html__( 'Middle', 'elementskit-lite' ),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => esc_html__( 'Bottom', 'elementskit-lite' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-promo-popup > .mfp-container > .mfp-content' => 'vertical-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_search_box_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .ekit-promo-popup > .mfp-container > .mfp-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->end_controls_section();

        $this->start_controls_section(
			'ekit_search_close_button_style_tabs',
			[
				'label' => __( 'Close Button', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
            'ekit_search_close_button_border_radius',
            [
                'label'         => esc_html__('Border Radius', 'elementskit-lite'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['%', 'px'],
                'default' => [
                    'unit' => '%'
                ],
                'range' => [
					'min' => 0,
					'max' => 100,
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-promo-popup .mfp-close' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

        $this->add_responsive_control(
            'ekit_search_close_button_size',
            [
                'label'         => esc_html__('Size (px)', 'elementskit-lite'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'range' => [
                    'px' => [
						'min' => 30,
						'max' => 50,
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-promo-popup .mfp-close' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-promo-popup .mfp-close:hover' => 'width: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

        $this->start_controls_tabs( 'ekit_search_close_button_tabs' );

        $this->start_controls_tab(
            'ekit_search_close_button_normal',
            [
                'label' =>esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_search_close_button_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-promo-popup .mfp-close' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_search_close_button_bg_color',
            [
                'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-promo-popup .mfp-close' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_search_close_button_hover',
            [
                'label' =>esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_search_close_button_hover_color',
            [
                'label' =>esc_html__( 'Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-promo-popup .mfp-close:hover' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'ekit_search_close_button_hover_bg_color',
            [
                'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-promo-popup .mfp-close:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();

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
        /*
         *
         * Roots.io searchform.php template hack to fix Polylang search
         * https://gist.github.com/bramchi/d0767c32a772550486ea
         * Note: Polylang setting 'Hide URL language info for default language' should be enabled for this to work.
         * Soil-nice-search disabled in Roots.
         *
         */
        $language_prefix = (!function_exists('pll_current_language') ? '' : pll_current_language());

		$ekit_search_link = apply_filters('ekit_search_link', home_url( '/'.$language_prefix ));
		$placeholder_and_label = $settings['ekit_search_placeholder_text']; 
        ?>
        <a href="#ekit_modal-popup-<?php echo esc_attr($this->get_id()); ?>" class="ekit_navsearch-button ekit-modal-popup" aria-label="navsearch-button">
            <?php
                // new icon
                $migrated = isset( $settings['__fa4_migrated']['ekit_search_icons'] );
                // Check if its a new widget without previously selected icon using the old Icon control
                $is_new = empty( $settings['ekit_search_icon'] );
                if ( $is_new || $migrated ) {
                    // new icon
                    Icons_Manager::render_icon( $settings['ekit_search_icons'], [ 'aria-hidden' => 'true' ] );
                } else {
                    ?>
                    <i class="<?php echo esc_attr($settings['ekit_search_icon']); ?>" aria-hidden="true"></i>
                    <?php
                }
            ?>
        </a>
        <!-- language switcher strart -->
        <!-- xs modal -->
        <div class="zoom-anim-dialog mfp-hide ekit_modal-searchPanel" id="ekit_modal-popup-<?php echo esc_attr($this->get_id()); ?>">
            <div class="ekit-search-panel">
            <!-- Polylang search - thanks to Alain Melsens -->
                <form role="search" method="get" class="ekit-search-group" action="<?php echo esc_url( $ekit_search_link ); ?>">
                    <input type="search" class="ekit_search-field" aria-label="search-form" placeholder="<?php echo esc_attr($placeholder_and_label); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s">
					<button type="submit" class="ekit_search-button" aria-label="search-button">
                        <?php
                            // new icon
                            $migrated = isset( $settings['__fa4_migrated']['ekit_search_icons'] );
                            // Check if its a new widget without previously selected icon using the old Icon control
                            $is_new = empty( $settings['ekit_search_icon'] );
                            if ( $is_new || $migrated ) {
                                // new icon
                                Icons_Manager::render_icon( $settings['ekit_search_icons'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($settings['ekit_search_icon']); ?>" title="Search" aria-hidden="true"></i>
                                <?php
                            }
                        ?>
                    </button>
                </form>
            </div>
        </div><!-- End xs modal -->
        <!-- end language switcher strart -->
        <?php
    }
}
