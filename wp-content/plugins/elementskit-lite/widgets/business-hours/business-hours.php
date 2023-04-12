<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Business_Hours_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Business_Hours extends Widget_Base {
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
        return 'https://wpmet.com/doc/business-hours/';
    }

    protected function register_controls() {


		$this->start_controls_section(
			'ekit_btn_section_content',
			array(
				'label' => esc_html__( 'Content', 'elementskit-lite' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'ekit_business_day',
			[
				'label'   => esc_html__( 'Day', 'elementskit-lite' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Saturday', 'elementskit-lite' ),
			]
		);

		$repeater->add_control(
			'ekit_business_time',
			[
				'label'   => esc_html__( 'Time', 'elementskit-lite' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( '9:00 AM - 6:00 PM', 'elementskit-lite' ),
			]
		);

		$repeater->add_control(
			'ekit_highlight_this_day',
			[
				'label'        => esc_html__( 'Highlight this day', 'elementskit-lite' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
				'separator'    => 'before',
			]
		);

		$repeater->add_responsive_control(
			'ekit_single_business_day_color',
			[
				'label'     => esc_html__( 'Day Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fa2d2d',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con {{CURRENT_ITEM}}.ekit-closed-day.ekit-single-day .ekit-business-day' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_highlight_this_day' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$repeater->add_responsive_control(
			'ekit_single_business_time_color',
			[
				'label'     => esc_html__( 'Time Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fa2d2d',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con {{CURRENT_ITEM}}.ekit-closed-day.ekit-single-day .ekit-business-time' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_highlight_this_day' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$repeater->add_responsive_control(
			'ekit_single_business_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con {{CURRENT_ITEM}}.ekit-closed-day' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_highlight_this_day' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_business_openday_list',
			[
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'ekit_business_day' => esc_html__( 'Sunday', 'elementskit-lite' ),
						'ekit_business_time' => esc_html__( 'Close','elementskit-lite' ),
						'ekit_highlight_this_day' => esc_html__( 'yes','elementskit-lite' ),
					],

					[
						'ekit_business_day' => esc_html__( 'Saturday', 'elementskit-lite' ),
						'ekit_business_time' => esc_html__( '10:00 AM to 7:00 PM','elementskit-lite' ),
						'ekit_highlight_this_day' => esc_html__( 'yes','elementskit-lite' ),
					],

					[
						'ekit_business_day' => esc_html__( 'Monday', 'elementskit-lite' ),
						'ekit_business_time' => esc_html__( '10:00 AM to 7:00 PM','elementskit-lite' ),
					],

					[
						'ekit_business_day' => esc_html__( 'Tues Day', 'elementskit-lite' ),
						'ekit_business_time' => esc_html__( '10:00 AM to 7:00 PM','elementskit-lite' ),
					],

					[
						'ekit_business_day' => esc_html__( 'Wednesday', 'elementskit-lite' ),
						'ekit_business_time' => esc_html__( '10:00 AM to 7:00 PM','elementskit-lite' ),
					],

					[
						'ekit_business_day' => esc_html__( 'Thursday', 'elementskit-lite' ),
						'ekit_business_time' => esc_html__( '10:00 AM to 7:00 PM','elementskit-lite' ),
					],

					[
						'ekit_business_day' => esc_html__( 'Friday', 'elementskit-lite' ),
						'ekit_business_time' => esc_html__( '10:00 AM to 7:00 PM','elementskit-lite' ),
					]
				],
				'title_field' => '{{{ ekit_business_day }}}',
			]
		);

		$this->end_controls_section();


        // Style Item section
        $this->start_controls_section(
            'ekit_business_item_style_section',
            [
                'label' => esc_html__( 'Item', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'ekit_business_item_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-single-day' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' =>'before',
			]
		);

		$this->add_responsive_control(
			'ekit_business_item_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-single-day' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' =>'after',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_business_item_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-single-day',
			]
		);

		$this->add_responsive_control(
			'ekit_business_item_item_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-single-day' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_business_item_border',
				'label' => esc_html__( 'Border', 'elementskit-lite' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-single-day:not(:last-child)',
			]
		);

        $this->end_controls_section();

        // Style Business day section
        $this->start_controls_section(
            'ekit_business_day_style_section',
            [
                'label' => esc_html__( 'Day', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'ekit_business_day_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-single-day .ekit-business-day' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_business_day_typography',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-single-day .ekit-business-day',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_business_day_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-single-day .ekit-business-day',
			]
		);

		$this->add_responsive_control(
			'ekit_business_item_day_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-single-day .ekit-business-day' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_business_item_day_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-single-day .ekit-business-day' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

        // Style Business Time section
        $this->start_controls_section(
            'ekit_business_time_style_section',
            [
                'label' => esc_html__( 'Time', 'elementskit-lite' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'ekit_business_time_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-single-day .ekit-business-time' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_business_time_typography',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-single-day .ekit-business-time',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_business_time_background',
				'label' => esc_html__( 'Background', 'elementskit-lite' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-single-day .ekit-business-time',
			]
		);

		$this->add_responsive_control(
			'ekit_business_item_time_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-single-day .ekit-business-time' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_business_item_time_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit-lite' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-single-day .ekit-business-time' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		<div class="ekit-business-hours-inner">
			<?php foreach ($settings['ekit_business_openday_list'] as $item) : ?>
			<div class="ekit-single-day elementor-repeater-item-<?php echo esc_attr($item['_id']); ?> <?php if( $item['ekit_highlight_this_day'] == 'yes' ){ echo esc_attr( 'ekit-closed-day','elementskit-lite'); }?>">
				<?php if( !empty( $item['ekit_business_day'] ) ) : ?>
				<span class="ekit-business-day"><?php echo esc_html($item['ekit_business_day']); ?></span>
				<?php endif; if( !empty( $item['ekit_business_time'] ) ): ?>
				<span class="ekit-business-time"><?php echo esc_html($item['ekit_business_time']); ?></span>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
        <?php
    }
}
