<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_TablePress_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_TablePress extends Widget_Base {
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
        return 'https://wpmet.com/doc/data-table-2/';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'ekit_tablepress_section_content_table',
            [
                'label' => esc_html__( 'Table', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
			'ekit_tablepress_table_id',
			[
				'label'   => esc_html__( 'Select Table', 'elementskit-lite' ),
				'type'    => Controls_Manager::SELECT,
                'label_block' => 'true',
				'default' => '0',
				'options' => \ElementsKit_Lite\Utils::tablepress_table_list(),
			]
		);

        
		if (class_exists('TablePress_Responsive_Tables')) {
			$this->add_control(
				'ekit_tablepress_table_responsive',
				[
                    'label'   => __( 'Responsive', 'elementskit-lite' ),
					'type'    => Controls_Manager::SELECT,
                    'default' => 'none',
                    'label_block' => 'true',
					'options' => [
                        'none'        => __( 'None', 'elementskit-lite' ),
						'flip'     => __( 'Flip', 'elementskit-lite' ),
						'scroll'   => __( 'Scroll', 'elementskit-lite' ),
						'collapse' => __( 'Collapse', 'elementskit-lite' ),
                        'stack'    => __( 'Stack', 'elementskit-lite' ),
					],
				]
			);
            $this->add_control(
                'ekit_tablepress_table_responsive_breakpoint',
                [
                    'label'   => __( 'Responsive Breakpoint', 'elementskit-lite' ),
                    'type'    => Controls_Manager::SELECT,
                    'label_block' => 'true',
                    'default' => 'none',
                    'options' => [
                        'none'        => __( 'None', 'elementskit-lite' ),
                        'phone'     => __( 'Phone', 'elementskit-lite' ),
                        'tablet'     => __( 'Tablet', 'elementskit-lite' ),
                        'desktop'   => __( 'Desktop', 'elementskit-lite' ),
                        'all' => __( 'All', 'elementskit-lite' ),
                    ],
                    'condition' => [
                        'ekit_tablepress_table_responsive!' => 'none'
                    ]
                ]
            );
        }
            
		$this->add_responsive_control(
            'ekit_tablepress_navigation_hide',
			[
                'label'     => esc_html__( 'Nav Hide', 'elementskit-lite' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .dataTables_length' => 'display: none;',
				],
                ]
		);
        
		$this->add_responsive_control(
			'ekit_tablepress_search_hide',
			[
                'label'     => esc_html__( 'Search Hide', 'elementskit-lite' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
                    '{{WRAPPER}} .elemenetskit-tablepress .dataTables_filter' => 'display: none;',
				],
                ]
            );

        $this->add_responsive_control(
			'ekit_tablepress_footer_info_hide',
			[
                'label'     => esc_html__( 'Footer Info Hide', 'elementskit-lite' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .dataTables_info' => 'display: none;',
				],
			]
		);

		$this->add_responsive_control(
            'ekit_tablepress_pagination_hide',
            [
                'label'     => esc_html__( 'Pagination Hide', 'elementskit-lite' ),
                'type'      => Controls_Manager::SWITCHER,
                'selectors' => [
                    '{{WRAPPER}} .elemenetskit-tablepress .dataTables_paginate' => 'display: none;',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tablepress_header_align',
            [
                'label'   => __( 'Header Alignment', 'elementskit-lite' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left'    => [
                        'title' => __( 'Left', 'elementskit-lite' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'elementskit-lite' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'elementskit-lite' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .elemenetskit-tablepress .tablepress th' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tablepress_body_align',
            [
                'label'   => __( 'Body Alignment', 'elementskit-lite' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left'    => [
                        'title' => __( 'Left', 'elementskit-lite' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'elementskit-lite' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'elementskit-lite' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .elemenetskit-tablepress table.tablepress tr td' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

		$this->start_controls_section(
			'ekit_tablepress_section_style_table',
			[
				'label' => __( 'Table', 'elementskit-lite' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_tablepress_table_text_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .dataTables_length, {{WRAPPER}} .elemenetskit-tablepress .dataTables_filter, {{WRAPPER}} .elemenetskit-tablepress .dataTables_info, {{WRAPPER}} .elemenetskit-tablepress .paginate_button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_tablepress_table_border_style',
			[
				'label'   => __( 'Border Style', 'elementskit-lite' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => __( 'None', 'elementskit-lite' ),
					'solid'  => __( 'Solid', 'elementskit-lite' ),
					'double' => __( 'Double', 'elementskit-lite' ),
					'dotted' => __( 'Dotted', 'elementskit-lite' ),
					'dashed' => __( 'Dashed', 'elementskit-lite' ),
					'groove' => __( 'Groove', 'elementskit-lite' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress table.tablepress' => 'border-style: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_tablepress_table_border_style!' => 'none'
                ]
			]
		);

		$this->add_control(
			'ekit_tablepress_table_border_width',
			[
				'label'   => __( 'Border Width', 'elementskit-lite' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'min'  => 0,
					'max'  => 20,
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress table.tablepress' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tablepress_table_border_style!' => 'none'
                ]
			]
		);

		$this->add_control(
			'ekit_tablepress_table_border_color',
			[
				'label'     => __( 'Border Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ccc',
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress table.tablepress' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_tablepress_table_border_style!' => 'none'
                ]
			]
		);
        
        $this->add_responsive_control(
			'ekit_tablepress_table_header_tools_gap',
			[
				'label' => __( 'Pagination And Serach Gap', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .dataTables_length, {{WRAPPER}} .elemenetskit-tablepress .dataTables_filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_responsive_control(
			'ekit_tablepress_table_footer_tools_gap',
			[
				'label' => __( 'Footer Text And Navigation gap', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .dataTables_info, {{WRAPPER}} .elemenetskit-tablepress .dataTables_paginate' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_tablepress_section_style_header',
			[
				'label' => __( 'Header', 'elementskit-lite' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_tablepress_header_background',
			[
				'label'     => __( 'Background', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#dfe3e6',
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_tablepress_header_active_background',
			[
				'label'     => __( 'Hover And Active Background', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ccd3d8',
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress .sorting:hover, {{WRAPPER}} .elemenetskit-tablepress .tablepress .sorting_asc, {{WRAPPER}} .elemenetskit-tablepress .tablepress .sorting_desc' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_tablepress_header_color',
			[
				'label'     => __( 'Text Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333',
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_tablepress_header_border_style',
			[
				'label'   => __( 'Border Style', 'elementskit-lite' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => __( 'None', 'elementskit-lite' ),
					'solid'  => __( 'Solid', 'elementskit-lite' ),
					'double' => __( 'Double', 'elementskit-lite' ),
					'dotted' => __( 'Dotted', 'elementskit-lite' ),
					'dashed' => __( 'Dashed', 'elementskit-lite' ),
					'groove' => __( 'Groove', 'elementskit-lite' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress th' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_tablepress_header_border_width',
			[
				'label'   => __( 'Border Width', 'elementskit-lite' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'min'  => 0,
					'max'  => 20,
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress th' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tablepress_header_border_style!' => 'none'
                ]
			]
		);

		$this->add_control(
			'ekit_tablepress_header_border_color',
			[
				'label'     => __( 'Border Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ccc',
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress th' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_tablepress_header_border_style!' => 'none'
                ]
			]
		);

		$this->add_responsive_control(
			'ekit_tablepress_header_padding',
			[
				'label'      => __( 'Padding', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'    => 1,
					'bottom' => 1,
					'left'   => 1,
					'right'  => 1,
					'unit'   => 'em'
				],
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_tablepress_section_style_body',
			[
				'label' => __( 'Body', 'elementskit-lite' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_tablepress_cell_border_style',
			[
				'label'   => __( 'Border Style', 'elementskit-lite' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => __( 'None', 'elementskit-lite' ),
					'solid'  => __( 'Solid', 'elementskit-lite' ),
					'double' => __( 'Double', 'elementskit-lite' ),
					'dotted' => __( 'Dotted', 'elementskit-lite' ),
					'dashed' => __( 'Dashed', 'elementskit-lite' ),
					'groove' => __( 'Groove', 'elementskit-lite' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress td' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_tablepress_cell_border_width',
			[
				'label'   => __( 'Border Width', 'elementskit-lite' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'min'  => 0,
					'max'  => 20,
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress td' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tablepress_cell_border_style!' => 'none'
                ]
			]
		);

		$this->add_responsive_control(
			'ekit_tablepress_cell_padding',
			[
				'label'      => __( 'Cell Padding', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'    => 0.5,
					'bottom' => 0.5,
					'left'   => 1,
					'right'  => 1,
					'unit'   => 'em'
				],
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->start_controls_tabs('ekit_tablepress_tabs_body_style');

		$this->start_controls_tab(
			'ekit_tablepress_tab_normal',
			[
				'label' => __( 'Normal', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_tablepress_normal_background',
			[
				'label'     => __( 'Background', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress tbody tr:nth-child(odd) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_tablepress_normal_color',
			[
				'label'     => __( 'Text Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress tbody tr:nth-child(odd) td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_tablepress_normal_border_color',
			[
				'label'     => __( 'Border Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ccc',
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress tbody tr:nth-child(odd) td' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_tablepress_cell_border_style!' => 'none'
                ]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_tablepress_tab_stripe',
			[
				'label' => __( 'Stripe', 'elementskit-lite' ),
			]
		);

		$this->add_control(
			'ekit_tablepress_stripe_background',
			[
				'label'     => __( 'Background', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f7f7f7',
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress tbody tr:nth-child(even) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_tablepress_stripe_color',
			[
				'label'     => __( 'Text Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress tbody tr:nth-child(even) td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_tablepress_stripe_border_color',
			[
				'label'     => __( 'Border Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ccc',
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress tbody tr:nth-child(even) td' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'ekit_tablepress_cell_border_style!' => 'none'
                ]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'ekit_tablepress_body_hover_background',
			[
				'label'     => __( 'Hover Background', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .tablepress .row-hover tr:hover td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_tablepress_section_search_layout_style',
			[
				'label'      => esc_html__( 'Filter And Search', 'elementskit-lite' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
				        [
							'name'  => 'ekit_tablepress_navigation_hide',
							'value' => '',
				        ],
				        [	
							'name'  => 'ekit_tablepress_search_hide',
							'value' => '',
				        ],
				    ],
				],
			]
		);

		$this->add_control(
			'ekit_tablepress_search_icon_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .dataTables_filter input, {{WRAPPER}} .elemenetskit-tablepress .dataTables_length select' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_tablepress_search_background',
			[
				'label'     => esc_html__( 'Background', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elemenetskit-tablepress .dataTables_filter input, {{WRAPPER}} .elemenetskit-tablepress .dataTables_length select' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_tablepress_search_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elemenetskit-tablepress .dataTables_filter input, {{WRAPPER}} .elemenetskit-tablepress .dataTables_length select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_tablepress_search_border',
				'label'       => esc_html__( 'Border', 'elementskit-lite' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .elemenetskit-tablepress .dataTables_filter input, {{WRAPPER}} .elemenetskit-tablepress .dataTables_length select',
			]
		);

		$this->add_responsive_control(
			'ekit_tablepress_search_radius',
			[
				'label'      => esc_html__( 'Radius', 'elementskit-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elemenetskit-tablepress .dataTables_filter input, {{WRAPPER}} .elemenetskit-tablepress .dataTables_length select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_tablepress_search_box_shadow',
				'selector' => '{{WRAPPER}} .elemenetskit-tablepress .dataTables_filter input, {{WRAPPER}} .elemenetskit-tablepress .dataTables_length select',
			]
		);

		$this->end_controls_section();

		$this->insert_pro_message();
    }

    private function get_shortcode() {
		$settings = $this->get_settings();

		$ekit_tablepress_table_id_sanitize = isset($settings['ekit_tablepress_table_id']) ? intval($settings['ekit_tablepress_table_id']) : 0;

		if (!$ekit_tablepress_table_id_sanitize) {
			return '<div class="elemenetskit-alert-info">'.esc_html__('Please Select A Table From Setting!', 'elementskit-lite').'</div>';
		}
		
		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			\TablePress::load_controller( 'frontend' );
			$controller = new \TablePress_Frontend_Controller();
			$controller->init_shortcodes();
		}

		$attributes = [
			'id'         => $ekit_tablepress_table_id_sanitize,
            'responsive' => (class_exists('TablePress_Responsive_Tables')) ? $settings['ekit_tablepress_table_responsive'] : '',
            'responsive_breakpoint' => (class_exists('TablePress_Responsive_Tables')) ? $settings['ekit_tablepress_table_responsive_breakpoint'] : '',
		];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = ['<div class="elemenetskit-tablepress ekit-wid-con" id="ekit_tablepress_'.esc_attr($this->get_id()).'">'];
		$shortcode[] = sprintf( '[table %s]', $this->get_render_attribute_string( 'shortcode' ) );
		$shortcode[] = '</div>';

		$output = implode("", $shortcode);

		return $output;
	}

	public function render() {
		$settings = $this->get_settings();
		
		 if( class_exists('TablePress') ) {
			echo do_shortcode( $this->get_shortcode() );
		 } else {
			echo '<div class="elemenetskit-alert-info">'.esc_html__('Please install and activate TablePress plugin to work this widget.', 'elementskit-lite').'</div>';
		 }

		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() && class_exists('TablePress') ) { ?>
			<script>
				jQuery(document).ready(function($){
					jQuery('#ekit_tablepress_<?php echo esc_attr($this->get_id()); ?>').find('.tablepress').dataTable();
                });
			</script>
        <?php }
	}
}
