<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Header_Info_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Header_Info extends Widget_Base
{
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
        return 'https://wpmet.com/doc/header-info/';
    }
    protected function is_dynamic_content(): bool {
        return false;
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'ekit_header_info',
            [
                'label' => esc_html__('Header Info', 'elementskit-lite'),
            ]
        );

        $headerinfogroup = new Repeater();
        $headerinfogroup->add_control(
            'ekit_headerinfo_icons',
            [
                'label'         => esc_html__('Icon', 'elementskit-lite'),
                'label_block'   => true,
                'type'          => Controls_Manager::ICONS,
                'default'       => [
                    'value'         => 'icon icon-map',
                    'library'       => 'ekiticons',
                ],

            ]
        );

        $headerinfogroup->add_control(
            'ekit_headerinfo_text',
            [
                'label' => esc_html__('Text', 'elementskit-lite'),
                'label_block' => true,
                'type' => Controls_Manager::TEXT,
                'default' => '463 7th Ave, NY 10018, USA',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
        $headerinfogroup->add_control(
            'ekit_headerinfo_link',
            [
                'label' => esc_html__( 'Link', 'elementskit-lite' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://wpmet.com', 'elementskit-lite' ),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'ekit_headerinfo_group',
            [
                'label' => esc_html__( 'Header Info', 'elementskit-lite' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $headerinfogroup->get_controls(),
                'default' => [
                    [
                        'ekit_headerinfo_text' => esc_html__( '463 7th Ave, NY 10018, USA', 'elementskit-lite' ),
                    ],

                ],
                'title_field' => '{{{ ekit_headerinfo_text }}}',
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'ekit_header_icon_style',
            [
                'label' => esc_html__( 'Header Info', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
			'ekit_info_item_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-header-info > li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->add_responsive_control(
			'ekit_info_item_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-header-info > li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'ekit_info_text_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-header-info > li > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'item_bg',
            [
                'label'     => esc_html__( 'Background Color', 'elementskit-lite' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-header-info > li' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'elementskit_content_typography',
                'label' => esc_html__( 'Typography', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .ekit-header-info > li > a',
            ]
        );

        $this->add_control(
			'icon',
			[
				'label'     => __( 'Icon', 'elementskit-lite' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
            'ekit_info_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'elementskit-lite' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit-header-info > li > a i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit-header-info > li > a svg path'   => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_simple_tab_title_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 5,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-header-info > li > a i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-header-info > li > a svg' => 'max-width: {{SIZE}}{{UNIT}}; height: auto',
                ],
            ]
        );
        $this->add_responsive_control(
            'ekit_info_icon_spacing',
            [
                'label' => esc_html__( 'Icon Spacing', 'elementskit-lite' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-header-info > li > a i, {{WRAPPER}} .ekit-header-info > li > a svg' => 'margin-right: {{SIZE}}{{UNIT}};',
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
        ?>
        <ul class="ekit-header-info">
            <?php
            if ( $settings['ekit_headerinfo_group'] ){
                foreach (  $settings['ekit_headerinfo_group'] as $key => $item ){
                    if ( ! empty( $item['ekit_headerinfo_link']['url'] ) ) {
                        $this->add_link_attributes( 'button-' . $key, $item['ekit_headerinfo_link'] );
                    }
                    ?>
                        <li>
                            <a <?php echo $this->get_render_attribute_string( 'button-' . $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>> 
                                <?php Icons_Manager::render_icon( $item['ekit_headerinfo_icons'], [ 'aria-hidden' => 'true' ] ); ?>
                                <?php echo esc_html($item['ekit_headerinfo_text']);?>
                            </a>
                        </li>

                    <?php


                }
            }
            ?>
        </ul>
        <?php
    }
}
