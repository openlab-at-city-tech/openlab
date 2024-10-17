<?php

namespace Elementor;

use \Elementor\ElementsKit_Widget_FAQ_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_FAQ extends Widget_Base {
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
        return 'https://wpmet.com/doc/faq/';
    }
    protected function is_dynamic_content(): bool {
        return false;
    }

    protected function register_controls() {
        $this->start_controls_section(
            'ekit_faq_section_tab', [
                'label' =>esc_html__( 'FAQ', 'elementskit-lite' ),
            ]
        );


        $repeater = new Repeater();

        $repeater->add_control(
            'ekit_faq_title',
            [
                'label' =>esc_html__( 'Title', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
                'label_block' => true,
                'placeholder' =>esc_html__( 'Title type here', 'elementskit-lite' ),
                'default' =>esc_html__( 'How to Change my Photo from Admin Dashboard?', 'elementskit-lite' ),
            ]
        );
        $repeater->add_control(
            'ekit_faq_content',
            [
                'label' =>esc_html__( 'Content', 'elementskit-lite' ),
                'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
                'label_block' => true,
                'placeholder' =>esc_html__( 'Description type here', 'elementskit-lite' ),
                'default' =>esc_html__( 'Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast', 'elementskit-lite' ),
            ]
        );

        $this->add_control(
            'ekit_faq_content_items',
            [
                'label' => esc_html__('Tab content', 'elementskit-lite'),
                'type' => Controls_Manager::REPEATER,
                'separator' => 'before',
                'title_field' => '{{ ekit_faq_title }}',
                'default' => [
                    [
                        'ekit_faq_title' => 'Wait. What is WordPress?',
                        'ekit_faq_content' => 'Far far away, behind the word Mountains far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmark',
                    ],
                    [
                        'ekit_faq_title' => 'How long do I get support?',
                        'ekit_faq_content' => 'Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line',
                    ],
                    [
                        'ekit_faq_title' => 'Do I need to renew my license?',
                        'ekit_faq_content' => 'Marks and devious Semikoli but the Little Blind Text didn’t listen. She packed her seven versalia, put her initial into the belt and made herself on the way.',
                    ],
                ],
                'fields' => $repeater->get_controls(),
            ]
        );

        //faq schema
        $this->add_control(
			'ekit_faq_schema',
			[
				'label' => esc_html__( 'FAQ Schema', 'elementskit-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
        );
        $this->end_controls_section();

        //Title Style Section

        $this->start_controls_section(
            'ekit_faq_section_title_style', [
                'label'	 =>esc_html__( 'Title', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ekit_faq_title_color', [
                'label'		 =>esc_html__( 'Title Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-title' => 'color: {{VALUE}};'
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_faq_title_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_faq_title_background_group',
                'label' => esc_html__( 'Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-header',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_faq_title_border_group',
                'label' => esc_html__( 'Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-header',
            ]
        );

        $this->add_control(
            'ekit_faq_border_radious',
            [
                'label' => esc_html__( 'Title Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_faq_title_padding',
            [
                'label' => esc_html__( 'Title Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => 	[
					'top' => '21',
					'right' => '40',
					'bottom' => '21',
					'left' => '40',
					'unit' => 'px',
				],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_faq_title_margin',
            [
                'label' => esc_html__( 'Title Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();

        //Content Style Section
        $this->start_controls_section(
            'ekit_faq_section_content_style', [
                'label'	 =>esc_html__( 'Content', 'elementskit-lite' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'ekit_faq_content_color', [
                'label'		 =>esc_html__( 'Color', 'elementskit-lite' ),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                    '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-body' => 'color: {{VALUE}};'
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_faq_content_typography_group',
                'selector'	 => '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-body',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_faq_content_background_group',
                'label' => esc_html__( 'Content Background', 'elementskit-lite' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-body',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_faq_content_border_group',
                'label' => esc_html__( 'Content Border', 'elementskit-lite' ),
                'selector' => '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-body',
            ]
        );

        $this->add_control(
            'ekit_faq_content_border_radious',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-body' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_faq_content_padding',
            [
                'label' => esc_html__( 'Content Padding', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => 	[
					'top' => '30',
					'right' => '40',
					'bottom' => '30',
					'left' => '40',
					'unit' => 'px',
				],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_faq_content_margin',
            [
                'label' => esc_html__( 'Content Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-faq .elementskit-faq-body' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_faq_container_margin',
            [
                'label' => esc_html__( 'Container Margin', 'elementskit-lite' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-single-faq:not(:last-child)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        extract($settings);
        ?>

        <?php if($ekit_faq_content_items > 0) : foreach($ekit_faq_content_items as $ekit_faq_content_item) : ?>
        <div class="elementskit-single-faq elementor-repeater-item-<?php echo esc_attr( $ekit_faq_content_item[ '_id' ] ); ?>">
            <div class="elementskit-faq-header">
                <h2 class="elementskit-faq-title"><?php echo esc_html($ekit_faq_content_item['ekit_faq_title']); ?></h2>
            </div>
            <div class="elementskit-faq-body">
                <?php if(!empty($ekit_faq_content_item['ekit_faq_content'])) {
					echo wp_kses($ekit_faq_content_item['ekit_faq_content'], \ElementsKit_Lite\Utils::get_kses_array());
                } ?>
            </div>
        </div>
        <?php endforeach; endif; ?>
        <?php
            if ( isset( $settings['ekit_faq_schema'] ) && 'yes' === $settings['ekit_faq_schema'] ) {
                $json = [
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => [],
                ];

                foreach ( $settings['ekit_faq_content_items'] as $index => $item ) {
                    $faq_schema_text = !empty( $item['ekit_faq_content'] ) ? $item['ekit_faq_content'] : '';
                    $json['mainEntity'][] = [
                        '@type' => 'Question',
                        'name' => esc_html($item['ekit_faq_title']),
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => \ElementsKit_Lite\Utils::kses( $faq_schema_text ),
                        ],
                    ];
                }
                ?>
                <script type="application/ld+json"><?php echo wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
                <?php
            }
        ?>

    <?php
    }
}