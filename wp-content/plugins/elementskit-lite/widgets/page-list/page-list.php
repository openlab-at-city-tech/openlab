<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Page_List_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Page_List extends Widget_Base {
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

	public function get_keywords() {
        return Handler::get_keywords();
    }

    public function get_categories() {
        return Handler::get_categories();
    }

    public function get_help_url() {
        return 'https://wpmet.com/doc/page-list/';
    }

	protected function register_controls() {
		$this->start_controls_section(
			'section_icon',
			[
				'label' => esc_html__( 'List', 'elementskit-lite' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			[
				'label' => esc_html__( 'Text', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'placeholder' => esc_html__( 'List Title', 'elementskit-lite' ),
			]
		);

		$repeater->add_control(
			'ekit_menu_widget_sub_title',
			[
				'label' => esc_html__( 'Sub Title', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'placeholder' => esc_html__( 'Type your title here', 'elementskit-lite' ),
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_page_list_background_rep',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-wid-con {{CURRENT_ITEM}}',
				'exclude' => ['image'] // PHPCS:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			]
		);

		$repeater->add_control(
			'ekit_page_list_show_icon',
			[
				'label' => esc_html__( 'Show Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$repeater->add_control(
			'icons',
			[
				'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => true,
				'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-check',
                    'library' => 'fa-solid',
                ],
				'condition' => [
					'ekit_page_list_show_icon' => 'yes'
				],
			]
		);

		$repeater->add_responsive_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-icon-list-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-icon-list-icon svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};'
				],
				'condition' => [
					'ekit_page_list_show_icon' => 'yes'
				],
			]
		);

		$repeater->add_control(
			'ekit_page_list_select_page_or_custom_link',
			[
				'label' => esc_html__( 'Selct Page / Custom Link', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$repeater->add_control(
			'link',
			[
                'label' =>esc_html__('Select Page', 'elementskit-lite'),
				'type'      => ElementsKit_Controls_Manager::AJAXSELECT2,
				'options'   =>'ajaxselect2/page_list',
                'label_block' => true,
				'multiple'  => false,
				'condition' => [
					'ekit_page_list_select_page_or_custom_link' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'ekit_page_list_website_link',
			[
				'label' => esc_html__( 'Link', 'elementskit-lite' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://wpmet.com', 'elementskit-lite' ),
				'show_external' => true,
				'condition' => [
					'ekit_page_list_select_page_or_custom_link!' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'ekit_menu_list_show_label',
			[
				'label' => esc_html__( 'Show Label', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$repeater->add_control(
			'ekit_menu_list_label_title',
			[
				'label' => esc_html__( 'Label', 'elementskit-lite' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Default title', 'elementskit-lite' ),
				'placeholder' => esc_html__( 'Type your title here', 'elementskit-lite' ),
				'condition' => [
					'ekit_menu_list_show_label' => 'yes'
				]
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_menu_list_label_title_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', ],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .ekit_menu_label',
				'exclude' => ['image'], // PHPCS:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
				'condition' => [
					'ekit_menu_list_show_label' => 'yes'
				]
			]
		);

		$repeater->add_responsive_control(
			'ekit_menu_list_label_title_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ekit_menu_label' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_menu_list_show_label' => 'yes'
				]
			]
		);

		$this->add_control(
			'icon_list',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'page_list_settings',
			[
				'label' => esc_html__( 'Settings', 'elementskit-lite' )
			]
		);

		$this->add_control(
			'view',
			[
				'label' => esc_html__( 'Layout', 'elementskit-lite' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'traditional',
				'options' => [
					'traditional' => [
						'title' => esc_html__( 'Default', 'elementskit-lite' ),
						'icon' => 'eicon-editor-list-ul',
					],
					'inline' => [
						'title' => esc_html__( 'Inline', 'elementskit-lite' ),
						'icon' => 'eicon-ellipsis-h',
					],
				],
				'render_type' => 'template',
				'classes' => 'elementor-control-start-end',
				'label_block' => false,
				'style_transfer' => true,
			]
		);

		$this->add_responsive_control(
            'page_grid',
            [
                'label' => esc_html__( 'Columns Grid', 'elementskit-lite' ),
                'type' =>  Controls_Manager::SELECT,
                'options' => [
                    '12'  => esc_html__( '1 Columns', 'elementskit-lite' ),
                    '6'  => esc_html__( '2 Columns', 'elementskit-lite' ),
                    '4' => esc_html__( '3 Columns', 'elementskit-lite' ),
                    '3' => esc_html__( '4 Columns', 'elementskit-lite' ),
                    '2' => esc_html__( '6 Columns', 'elementskit-lite' ),
                ],
				'condition' => ['view' => 'inline']
            ]
		);

		$this->add_control(
			'ekit_href_target',
			[
				'label' => esc_html__( 'Target', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => '_blank',
				'options' => [
					'_blank'  => esc_html__( 'Blank', 'elementskit-lite' ),
					'self' => esc_html__( 'Self', 'elementskit-lite' ),
				],
			]
		);

		$this->add_control(
			'ekit_href_rel',
			[
				'label' => esc_html__( 'Rel', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit-lite' ),
				'label_off' => esc_html__( 'Hide', 'elementskit-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_list',
			[
				'label' => esc_html__( 'List', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_page_list_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_page_list_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_page_list_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .elementor-icon-list-item > a',
			]
		);

		$this->add_control(
            'ekit_page_list_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon-list-item > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_page_list_box_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementor-icon-list-item > a',
            ]
        );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_page_list_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementor-icon-list-item > a',
				'exclude' => ['image'] // PHPCS:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			]
		);
		$this->add_control(
			'ekit_page_list_background_title',
			[
				'label' => esc_html__( 'Hover', 'elementskit-lite' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_page_list_background_hover',
				'label' => esc_html__( 'Background Hover', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementor-icon-list-item > a:hover',
				'exclude' => ['image'] // PHPCS:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
			]
		);

		$this->add_control(
			'ekit_page_list_background_title_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);


		$this->add_responsive_control(
			'icon_align',
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
				'prefix_class' => 'elementor%s-align-',
			]
		);

		$this->add_control(
			'divider',
			[
				'label' => esc_html__( 'Divider', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'elementskit-lite' ),
				'label_on' => esc_html__( 'On', 'elementskit-lite' ),
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'content: ""',
				],
				'separator' => 'before',
				'prefix_class'	=> 'ekit-has-divider-'
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label' => esc_html__( 'Style', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid' => esc_html__( 'Solid', 'elementskit-lite' ),
					'dotted' => esc_html__( 'Dotted', 'elementskit-lite' ),
					'dashed' => esc_html__( 'Dashed', 'elementskit-lite' ),
				],
				'default' => 'solid',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:last-child):after' => 'border-top-style: {{VALUE}}',
					'{{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item:not(:last-child):after' => 'border-left-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label' => esc_html__( 'Weight', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:last-child):after' => 'border-top-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-inline-items .elementor-icon-list-item:not(:last-child):after' => 'border-left-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'condition' => [
					'divider' => 'yes',
					'view!' => 'inline',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_height',
			[
				'label' => esc_html__( 'Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'divider' => 'yes',
					'view' => 'inline',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ddd',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__( 'Icon', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
            'icon_position',
            [
                'label' => esc_html__( 'Icon Position', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'row',
                'options' => [
                    'row'  => esc_html__( 'Left', 'elementskit-lite' ),
                    'row-reverse' => esc_html__( 'Right', 'elementskit-lite' ),
                    'column' => esc_html__( 'Top', 'elementskit-lite' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_page_list_content' => 'flex-direction: {{VALUE}};'
				],
            ]
		);
		$this->add_responsive_control(
			'ekit_menu_list_icon_vetical_align',
			[
				'label' => esc_html__( 'Vertical Alignment', 'elementskit-lite' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => esc_html__( 'Bottom', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'condition' => [
					'icon_position!' => 'column'
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-icon' => 'display: flex; align-items: {{VALUE}}; justify-content: center',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_position_bg',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .elementor-icon-list-item .elementor-icon-list-icon',
			]
		);
		$this->add_responsive_control(
			'icon_width',
			[
				'label' => esc_html__( 'Width', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .elementor-icon-list-icon' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'icon_position_bg_background!' => ''
				]
			]
		);
		$this->add_responsive_control(
			'icon_height',
			[
				'label' => esc_html__( 'Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .elementor-icon-list-icon' => 'line-height: calc({{SIZE}}{{UNIT}} + 5{{UNIT}}); height: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'icon_position_bg_background!' => ''
				]
			]
		);

		$this->add_responsive_control(
			'icon_line_height',
			[
				'label' => esc_html__( 'Line Height', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .elementor-icon-list-icon' => 'line-height: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'icon_position_bg_background!' => ''
				]
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .elementor-icon-list-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
				'condition'	=> [
					'icon_position_bg_background!' => ''
				]
			]
		);


		$this->add_responsive_control(
			'icon_color_hover',
			[
				'label' => esc_html__( 'Icon Hover', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:hover .elementor-icon-list-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon-list-item:hover .elementor-icon-list-icon svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};'
				],
			]
		);
		$this->add_responsive_control(
			'ekit_icon_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Size', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'min' => 6,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-icon' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-icon-list-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-icon-list-icon svg'	=> 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			[
				'label' => esc_html__( 'Text', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_text_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_color_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:hover .elementor-icon-list-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_indent',
			[
				'label' => esc_html__( 'Padding Left', 'elementskit-lite' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-text' => is_rtl() ? 'padding-right: {{SIZE}}{{UNIT}};' : 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'icon_typography',
				'selector' => '{{WRAPPER}} .elementor-icon-list-item',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_menu_subtitle_style_tab',
			[
				'label' => esc_html__( 'Subtitle', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_menu_subtitle_typography',
				'label' => esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit_menu_subtitle',
			]
		);

		$this->add_control(
			'ekit_menu_subtitle_color',
			[
				'label' => esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_menu_subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_menu_subtitle_color_hover',
			[
				'label' => esc_html__( 'Color Hover', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item a:hover .ekit_menu_subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_menu_subtitle_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit_menu_subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_label',
			[
				'label' => esc_html__( 'Label', 'elementskit-lite' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_menu_list_label_title_typography',
				'label' => esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit_menu_label'
			]
		);
		$this->add_responsive_control(
			'ekit_menu_list_label_title_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit_menu_label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->add_responsive_control(
			'ekit_menu_list_label_title_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit_menu_label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->add_responsive_control(
			'ekit_menu_list_label_title_border_radius',
			[
				'label' => esc_html__( 'Border radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit_menu_label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'ekit_menu_list_label_align',
			[
				'label' => esc_html__( 'Horizontal Alignment', 'elementskit-lite' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'ekit_badge_left' => [
						'title' => esc_html__( 'Left', 'elementskit-lite' ),
						'icon' => 'eicon-text-align-left',
					],
					'ekit_badge_right' => [
						'title' => esc_html__( 'Right', 'elementskit-lite' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'ekit_badge_left',
				'toggle' => true
			]
		);

		$this->add_responsive_control(
			'ekit_menu_list_label_vetical_align_left',
			[
				'label' => esc_html__( 'Vertical Alignment', 'elementskit-lite' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => esc_html__( 'Bottom', 'elementskit-lite' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .ekit_menu_label' => 'align-self: {{VALUE}};',
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
		$settings = $this->get_settings_for_display();

		$grid_d = empty($settings['page_grid']) ? '' : 'col-lg-'.$settings['page_grid'];
		$grid_t = empty($settings['page_grid_tablet']) ? '' : 'col-md-'.$settings['page_grid_tablet'];
		$grid_m = empty($settings['page_grid_mobile']) ? '' : 'col-xs-'.$settings['page_grid_mobile'];

		$this->add_render_attribute( 'icon_list', 'class', 'elementor-icon-list-items' );
		$this->add_render_attribute( 'icon_list', 'class', (!empty($settings['page_grid']) ? 'row' : '') );
		$this->add_render_attribute( 'list_item', 'class', 'elementor-icon-list-item' );

		if ( 'inline' === $settings['view'] ) {
			$this->add_render_attribute( 'icon_list', 'class', 'elementor-inline-items' );
			$this->add_render_attribute( 'list_item', 'class', 'elementor-inline-item' );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'icon_list' ); ?>>
			<?php
			foreach ( $settings['icon_list'] as $index => $item ) :
				$post = !empty( $item['link'] ) ? get_post($item['link']) : null;
				$text_title = empty($item['text']) ? ($post instanceof \WP_Post ? $post->post_title : '') : $item['text'];

				if ( ! empty( $item['ekit_page_list_website_link']['url'] ) ) {
					$this->add_link_attributes( 'link_' . $index, $item['ekit_page_list_website_link'] );
				} else if ( ! empty( $item['link']  && (get_permalink($post) !== 0)) ) {
					$this->add_link_attributes( 'link_' . $index, ['url' => get_permalink($item['link'])] );
				}
				?>
				<div class="elementor-icon-list-item <?php echo esc_attr($grid_d); ?> <?php echo esc_attr($grid_t); ?> <?php echo esc_attr($grid_m); ?>">
						<a class="elementor-repeater-item-<?php echo esc_attr( $item[ '_id' ] ); ?> <?php echo  esc_attr( $settings['ekit_menu_list_label_align'] ); ?>" <?php $this->print_render_attribute_string( 'link_' . $index ); ?>>
							<div class="ekit_page_list_content">
								<?php if ( ! empty( $item['icons'] ) && $item['ekit_page_list_show_icon'] == 'yes') : ?>
									<span class="elementor-icon-list-icon">
										<?php
											// new icon
											$migrated = isset( $item['__fa4_migrated']['icons'] );
											// Check if its a new widget without previously selected icon using the old Icon control
											$is_new = empty( $item['icon'] );
											if ( $is_new || $migrated ) {
												// new icon
												Icons_Manager::render_icon( $item['icons'], [ 'aria-hidden' => 'true' ] );
											} else {
												?>
												<i class="<?php echo esc_attr($item['icon']); ?>" aria-hidden="true"></i>
												<?php
											}
										?>
									</span>
								<?php endif; ?>
								<span class="elementor-icon-list-text">
									<span class="ekit_page_list_title_title"><?php echo esc_html( $text_title ); ?></span>
									<?php if ($item['ekit_menu_widget_sub_title'] != '') : ?>
										<span class="ekit_menu_subtitle"><?php echo esc_html($item['ekit_menu_widget_sub_title']); ?></span>
									<?php endif; ?>
								</span>
							</div>
							<?php if ( ! empty( $item['ekit_menu_list_label_title'] ) && $item['ekit_menu_list_show_label'] == 'yes') : ?>
								<span class="ekit_menu_label">
									<?php echo esc_html( $item['ekit_menu_list_label_title'] ); ?>
								</span>
							<?php endif; ?>
						</a>
					</div>
				<?php
			endforeach; ?>
		</div>
		<?php
	}


}
