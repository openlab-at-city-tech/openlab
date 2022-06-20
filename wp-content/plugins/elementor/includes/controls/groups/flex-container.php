<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Group_Control_Flex_Container extends Group_Control_Base {

	protected static $fields;

	public static function get_type() {
		return 'flex-container';
	}

	protected function init_fields() {
		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';

		$fields = [];

		$fields['direction'] = [
			'label' => esc_html_x( 'Direction', 'Flex Container Control', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'row' => [
					'title' => esc_html_x( 'Row', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-arrow-' . $end,
				],
				'column' => [
					'title' => esc_html_x( 'Column', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-arrow-down',
				],
				'row-reverse' => [
					'title' => esc_html_x( 'Reversed Row', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-arrow-' . $start,
				],
				'column-reverse' => [
					'title' => esc_html_x( 'Reversed Column', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-arrow-up',
				],
			],
			'default' => '',
			'selectors' => [
				'{{SELECTOR}}' => '--flex-direction: {{VALUE}};',
			],
			'responsive' => true,
		];

		$fields['_is_row'] = [
			'type' => Controls_Manager::HIDDEN,
			'prefix_class' => 'e-container--',
			'default' => 'row',
			'condition' => [
				'direction' => [
					'row',
					'row-reverse',
				],
			],
		];

		$fields['_is_column'] = [
			'type' => Controls_Manager::HIDDEN,
			'prefix_class' => 'e-container--',
			'default' => 'column',
			'condition' => [
				'direction' => [
					'',
					'column',
					'column-reverse',
				],
			],
		];

		$fields['align_items'] = [
			'label' => esc_html_x( 'Align Items', 'Flex Container Control', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'default' => '',
			'options' => [
				'flex-start' => [
					'title' => esc_html_x( 'Flex Start', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-align-start-v',
				],
				'center' => [
					'title' => esc_html_x( 'Center', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-align-center-v',
				],
				'flex-end' => [
					'title' => esc_html_x( 'Flex End', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-align-end-v',
				],
				'stretch' => [
					'title' => esc_html_x( 'Stretch', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-align-stretch-v',
				],
			],
			'selectors' => [
				'{{SELECTOR}}' => '--align-items: {{VALUE}};',
			],
			'responsive' => true,
		];

		$fields['justify_content'] = [
			'label' => esc_html_x( 'Justify Content', 'Flex Container Control', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'label_block' => true,
			'default' => '',
			'options' => [
				'flex-start' => [
					'title' => esc_html_x( 'Flex Start', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-justify-start-h',
				],
				'center' => [
					'title' => esc_html_x( 'Center', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-justify-center-h',
				],
				'flex-end' => [
					'title' => esc_html_x( 'Flex End', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-justify-end-h',
				],
				'space-between' => [
					'title' => esc_html_x( 'Space Between', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-justify-space-between-h',
				],
				'space-around' => [
					'title' => esc_html_x( 'Space Around', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-justify-space-around-h',
				],
				'space-evenly' => [
					'title' => esc_html_x( 'Space Evenly', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-justify-space-evenly-h',
				],
			],
			'selectors' => [
				'{{SELECTOR}}' => '--justify-content: {{VALUE}};',
			],
			'responsive' => true,
		];

		$fields['gap'] = [
			'label' => esc_html_x( 'Gap', 'Flex Item Control', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 500,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
				],
				'vw' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', '%', 'vw' ],
			'selectors' => [
				'{{SELECTOR}}' => '--gap: {{SIZE}}{{UNIT}};',
			],
			'responsive' => true,
		];

		$fields['wrap'] = [
			'label' => esc_html_x( 'Wrap', 'Flex Container Control', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'nowrap' => [
					'title' => esc_html_x( 'No Wrap', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-nowrap',
				],
				'wrap' => [
					'title' => esc_html_x( 'Wrap', 'Flex Container Control', 'elementor' ),
					'icon' => 'eicon-flex eicon-wrap',
				],
			],
			'description' => esc_html_x(
				'Define whether the items are forced in a single line (No Wrap) or can be flowed into multiple lines (Wrap)',
				'Flex Container Control',
				'elementor'
			),
			'default' => '',
			'selectors' => [
				'{{SELECTOR}}' => '--flex-wrap: {{VALUE}};',
			],
			'responsive' => true,
		];

		$fields['align_content'] = [
			'label' => esc_html_x( 'Align Content', 'Flex Container Control', 'elementor' ),
			'type' => Controls_Manager::SELECT,
			'default' => '',
			'options' => [
				'' => esc_html_x( 'Default', 'Flex Container Control', 'elementor' ),
				'center' => esc_html_x( 'Center', 'Flex Container Control', 'elementor' ),
				'flex-start' => esc_html_x( 'Flex Start', 'Flex Container Control', 'elementor' ),
				'flex-end' => esc_html_x( 'Flex End', 'Flex Container Control', 'elementor' ),
				'space-between' => esc_html_x( 'Space Between', 'Flex Container Control', 'elementor' ),
				'space-around' => esc_html_x( 'Space Around', 'Flex Container Control', 'elementor' ),
				'space-evenly' => esc_html_x( 'Space Evenly', 'Flex Container Control', 'elementor' ),
			],
			'selectors' => [
				'{{SELECTOR}}' => '--align-content: {{VALUE}};',
			],
			'condition' => [
				'wrap' => 'wrap',
			],
			'responsive' => true,
		];

		return $fields;
	}

	protected function get_default_options() {
		return [
			'popover' => false,
		];
	}
}
