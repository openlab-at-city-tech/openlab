<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Social_Share_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Social_Share extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;
    
    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->add_script_depends('goodshare');
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
        return 'https://wpmet.com/doc/social-share/';
    }

    protected function register_controls() {

        // start content section for social media
        $this->start_controls_section(
            'ekit_socialshare_section_tab_content',
            [
                'label' => esc_html__('Social Media', 'elementskit-lite'),
            ]
        );

        $this->add_control(
			'ekit_socialshare_style',
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
			'ekit_socialshare_style_icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'elementskit-lite' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before'  => esc_html__( 'Before', 'elementskit-lite' ),
					'after' => esc_html__( 'After', 'elementskit-lite' ),
                ],
                'condition' => [
                    'ekit_socialshare_style' => 'both'
                ]
			]
        );

        $this->add_responsive_control(
			'ekit_socialshare_icon_padding_right',
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
                    'ekit_socialshare_style' => 'both',
                    'ekit_socialshare_style_icon_position' => 'before',
                ]
			]
		);

        $this->add_responsive_control(
			'ekit_socialshare_icon_padding_left',
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
                    'ekit_socialshare_style' => 'both',
                    'ekit_socialshare_style_icon_position' => 'after',
                ]
			]
		);

        $this->add_responsive_control(
            'ekit_socialshare_list_align',
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
                    '{{WRAPPER}} .ekit_socialshare' => 'text-align: {{VALUE}};',
                ],
            ]
        );

		$socialshare = new Repeater();

		// set social icon
        $socialshare->add_control(
            'ekit_socialshare_icons',
            [
                'label' => esc_html__( 'Icon', 'elementskit-lite' ),
                'label_block' => true,
                'fa4compatibility' => 'ekit_socialshare_icon',
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'icon icon-facebook',
                    'library' => 'ekiticons',
                ],
            ]
        );

        // set social link
        $socialshare->add_control(
            'ekit_socialshare_label_text',
            [
                'label' => esc_html__( 'Social Media', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'facebook',
                'options' => [
                    'facebook'      => esc_html__( 'Facebook', 'elementskit-lite' ),
                    'twitter'       => esc_html__( 'Twitter', 'elementskit-lite' ),
                    'pinterest'     => esc_html__( 'Pinterest', 'elementskit-lite' ),
                    'linkedin'      => esc_html__( 'Linkedin', 'elementskit-lite' ),
                    'tumblr'        => esc_html__( 'Tumblr', 'elementskit-lite' ),
                    // 'snapchat'        => esc_html__( 'Snapchat', 'elementskit-lite' ),
                    'flicker'        => esc_html__( 'Flicker', 'elementskit-lite' ),
                    'vkontakte'     => esc_html__( 'Vkontakte', 'elementskit-lite' ),
                    'odnoklassniki' => esc_html__( 'Odnoklassniki', 'elementskit-lite' ),
                    'moimir'        => esc_html__( 'Moimir', 'elementskit-lite' ),
                    'live journal'   => esc_html__( 'Live journal', 'elementskit-lite' ),
                    'blogger'       => esc_html__( 'Blogger', 'elementskit-lite' ),
                    'digg'          => esc_html__( 'Digg', 'elementskit-lite' ),
                    'evernote'      => esc_html__( 'Evernote', 'elementskit-lite' ),
                    'reddit'        => esc_html__( 'Reddit', 'elementskit-lite' ),
                    'delicious'     => esc_html__( 'Delicious', 'elementskit-lite' ),
                    'stumbleupon'   => esc_html__( 'Stumbleupon', 'elementskit-lite' ),
                    'pocket'        => esc_html__( 'Pocket', 'elementskit-lite' ),
                    'surfingbird'   => esc_html__( 'Surfingbird', 'elementskit-lite' ),
                    'liveinternet'  => esc_html__( 'Liveinternet', 'elementskit-lite' ),
                    'buffer'        => esc_html__( 'Buffer', 'elementskit-lite' ),
                    'instapaper'    => esc_html__( 'Instapaper', 'elementskit-lite' ),
                    'xing'          => esc_html__( 'Xing', 'elementskit-lite' ),
                    'wordpress'     => esc_html__( 'WordPress', 'elementskit-lite' ),
                    'baidu'         => esc_html__( 'Baidu', 'elementskit-lite' ),
                    'renren'        => esc_html__( 'Renren', 'elementskit-lite' ),
                    'weibo'         => esc_html__( 'Weibo', 'elementskit-lite' ),
                    'skype'         => esc_html__( 'Skype', 'elementskit-lite' ),
                    'telegram'      => esc_html__( 'Telegram', 'elementskit-lite' ),
                    'viber'         => esc_html__( 'Viber', 'elementskit-lite' ),
                    'whatsapp'      => esc_html__( 'Whatsapp', 'elementskit-lite' ),
                    'line'          => esc_html__( 'Line', 'elementskit-lite' ),
                ],
            ]
        );

		// set social icon label
        $socialshare->add_control(
            'ekit_socialshare_label',
            [
                'label' => esc_html__( 'Label', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

		// start tab for content
		$socialshare->start_controls_tabs(
            'ekit_socialshare_tabs'
        );

		// start normal tab
        $socialshare->start_controls_tab(
            'ekit_socialshare_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit-lite' ),
            ]
        );

		// set social icon color
        $socialshare->add_responsive_control(
			'ekit_socialshare_icon_color',
			[
				'label' =>esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#222222',
				'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} > a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} > a svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		// set social icon background color
        $socialshare->add_responsive_control(
			'ekit_socialshare_icon_bg_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} > a' => 'background-color: {{VALUE}};',
				],
			]
        );

        $socialshare->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_socialshare_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a',
			]
		);

         $socialshare->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'ekit_socialshare_icon_normal_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a',
			]
        );

        $socialshare->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'       => 'ekit_socialshare_list_box_shadow',
                'selector'   => '{{WRAPPER}} {{CURRENT_ITEM}} > a',
            ]
        );

		$socialshare->end_controls_tab();
		// end normal tab

		//start hover tab
		$socialshare->start_controls_tab(
            'ekit_socialshare_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit-lite' ),
            ]
        );

		// set social icon color
        $socialshare->add_responsive_control(
			'ekit_socialshare_icon_hover_color',
			[
				'label' =>esc_html__( 'Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};'
				],
			]
		);

		// set social icon background color
        $socialshare->add_responsive_control(
			'ekit_socialshare_icon_hover_bg_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit-lite' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#3b5998',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} > a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);


		$socialshare->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'ekit_socialshare_icon_hover_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover',
			]
        );

        $socialshare->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name'       => 'ekit_socialshare_list_box_shadow_hover',
                'selector'   => '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover',
            ]
        );

        $socialshare->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_socialshare_border_hover',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > a:hover',
			]
		);

		$socialshare->end_controls_tab();
		//end hover tab

		$socialshare->end_controls_tabs();


		// set social icon add new control
        $this->add_control(
            'ekit_socialshare_add_icons',
            [
                'label' => esc_html__('Add Social Media', 'elementskit-lite'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $socialshare->get_controls(),
                'default' => [
                    [
                        'ekit_socialshare_icons' => [
                            'value' => 'icon icon-facebook',
                            'library'   => 'ekiticons'
                        ],
                        'ekit_socialshare_icon_hover_bg_color' => '#3b5998',
                        'ekit_socialshare_label_text' => 'facebook',
                    ],
					[
                        'ekit_socialshare_icons' => [
                            'value' => 'icon icon-twitter', 
                            'library'   => 'ekiticons'
                        ],
                        'ekit_socialshare_icon_hover_bg_color' => '#1da1f2',
                        'ekit_socialshare_label_text' => 'twitter',
                    ],
					[
                        'ekit_socialshare_icons' => [
                            'value' => 'icon icon-linkedin',
                            'library'   => 'ekiticons'
                        ],
                        'ekit_socialshare_icon_hover_bg_color' => '#0077b5',
                        'ekit_socialshare_label_text' => 'linkedin',
                    ],
                ],
                'title_field' => '{{{ ekit_socialshare_label_text }}}',

            ]
        );

		$this->end_controls_section();
		// end content section

	// start style section control

		// start Social media tab
		 $this->start_controls_section(
            'ekit_socialshare_section_tab_style',
            [
                'label' => esc_html__('Social Media', 'elementskit-lite'),
				 'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
		// Alignment
        $this->add_responsive_control(
            'ekit_socialshare_list_item_align',
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
                    '{{WRAPPER}} .ekit_socialshare > li > a' => 'text-align: {{VALUE}};',
                ],
            ]
        );

		// Display design
		 $this->add_responsive_control(
            'ekit_socialshare_list_display',
            [
                'label' => esc_html__( 'Display', 'elementskit-lite' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'inline-block',
                'options' => [
                    'inline-block' => esc_html__( 'Inline Block', 'elementskit-lite' ),
                    'block' => esc_html__( 'Block', 'elementskit-lite' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit_socialshare > li' => 'display: {{VALUE}};',
                ],
            ]
        );

		// text decoration
		 $this->add_responsive_control(
            'ekit_socialshare_list_decoration_box',
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
                'selectors' => ['{{WRAPPER}} .ekit_socialshare > li > a' => 'text-decoration: {{VALUE}};'],
            ]
        );

		// border radius
		 $this->add_responsive_control(
            'ekit_socialshare_list_border_radius',
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
                    '{{WRAPPER}} .ekit_socialshare > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'ekit_socialshare_list_style_use_height_and_width',
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
            'ekit_socialshare_list_item_width',
            [
                'label' => esc_html__( 'Width', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
                    'unit' => 'px',
                    'size' => 40,
                ],
                'range' => [
					'px'	=> [
						'min'	=> 0,
						'max'	=> 200
					],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit_socialshare > li > a' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialshare_list_style_use_height_and_width'  => 'yes',
                    'ekit_socialshare_style' => 'icon',
                ]
            ]
		);
		
		$this->add_responsive_control(
            'ekit_socialshare_list_item_height',
            [
                'label' => esc_html__( 'Height', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
                    'unit' => 'px',
                    'size' => 40,
                ],
                'range' => [
					'px'	=> [
						'min'	=> 0,
						'max'	=> 200
					],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit_socialshare > li > a' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialshare_list_style_use_height_and_width'  => 'yes',
                    'ekit_socialshare_style' => 'icon',
                ]
            ]
        );

        $this->add_responsive_control(
			'ekit_socialshare_list_line_height',
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
					'size' => 40,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_socialshare > li > a' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_socialshare_list_style_use_height_and_width' => 'yes'
                ]
			]
        );
        
        $this->add_responsive_control(
            'ekit_socialshare_list_icon_size',
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
                    '{{WRAPPER}} .ekit_socialshare > li > a i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit_socialshare > li > a svg' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_socialshare_list_typography',
				'label' => esc_html__( 'Typography', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit_socialshare > li > a',
			]
		);


		// margin style

		$this->add_responsive_control(
            'ekit_socialshare_list_margin',
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
                    '{{WRAPPER}} .ekit_socialshare > li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_socialshare_list_padding',
            [
                'label'         => esc_html__('Padding', 'elementskit-lite'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units'    => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ekit_socialshare > li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        $settings = $this->get_settings();
        extract($settings);
		?>
		<ul class="ekit_socialshare">
            <?php foreach ($ekit_socialshare_add_icons as $icon): 
                
                if($icon['ekit_socialshare_label_text'] == 'instagram') {
                    continue;
                }                
                
                if($icon['ekit_socialshare_icons'] != ''): ?>
                <li class="elementor-repeater-item-<?php echo esc_attr( $icon[ '_id' ] ); ?>" data-social="<?php echo esc_attr((preg_replace('/[#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\ ]/', '', strtolower($icon['ekit_socialshare_label_text']))))?>">
                    <a class="<?php echo esc_attr((preg_replace('/[#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\ ]/', '', strtolower($icon['ekit_socialshare_label_text']))))?>">
                        <?php if($settings['ekit_socialshare_style'] != 'text' && $settings['ekit_socialshare_style_icon_position'] == 'before'): ?>

                        <?php
                            // new icon
                            $migrated = isset( $icon['__fa4_migrated']['ekit_socialshare_icons'] );
                            // Check if its a new widget without previously selected icon using the old Icon control
                            $is_new = empty( $icon['ekit_socialshare_icon'] );
                            if ( $is_new || $migrated ) {
                                // new icon
                                Icons_Manager::render_icon( $icon['ekit_socialshare_icons'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($icon['ekit_socialshare_icon']); ?>" aria-hidden="true"></i>
                                <?php
                            }
                        ?>
                        
                        <?php endif; ?>
                        <?php if($settings['ekit_socialshare_style'] != 'icon' ): ?>
                        <?php if ($icon['ekit_socialshare_label'] == '') : ?>
                        <?php echo esc_html(preg_replace('/[#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', ' ', ucwords($icon['ekit_socialshare_label_text'])));?>
                        <?php else : ?>
                        <?php echo esc_html($icon['ekit_socialshare_label'])?>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php if($settings['ekit_socialshare_style'] != 'text' && $settings['ekit_socialshare_style_icon_position'] == 'after'): ?>
                        <?php
                            // new icon
                            $migrated = isset( $icon['__fa4_migrated']['ekit_socialshare_icons'] );
                            // Check if its a new widget without previously selected icon using the old Icon control
                            $is_new = empty( $icon['ekit_socialshare_icon'] );
                            if ( $is_new || $migrated ) {
                                // new icon
                                Icons_Manager::render_icon( $icon['ekit_socialshare_icons'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?>
                                <i class="<?php echo esc_attr($icon['ekit_socialshare_icon']); ?>" aria-hidden="true"></i>
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
