<?php

/**
 * Lists settings, default values and display of TABS layout.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Config_Layout_Tabs {

	const LAYOUT_NAME = 'Tabs';
	const CATEGORY_LEVELS = 6;

	/**
	 * Defines KB configuration for this theme.
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => 'false' )
	 *
	 * @return array with both basic and theme-specific configuration
	 */
	public static function get_fields_specification() {

		$config_specification = array(

			'choose_main_topic' => array(
				'label'       => esc_html__( 'Drop Down Title', 'echo-knowledge-base' ),
				'name'        => 'choose_main_topic',
				'max'         => '150',
				'mandatory' => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => esc_html__( 'Choose a Main Topic', 'echo-knowledge-base' )
			),

			/***  KB Main Page STYLE -> Category Tabs ***/
			'tab_typography' => array(
				'label'       => esc_html__( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'tab_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'tab_down_pointer' => array(
				'label'       => esc_html__( 'Down Pointer', 'echo-knowledge-base' ),
				'name'        => 'tab_down_pointer',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),


			/***  KB Main Page COLORS -> Category Tabs  ***/

			'tab_nav_active_font_color' => array(
				'label'       => esc_html__( 'Active Text Color', 'echo-knowledge-base' ),
				'name'        => 'tab_nav_active_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'tab_nav_active_background_color' => array(
				'label'       => esc_html__( 'Active Background Color', 'echo-knowledge-base' ),
				'name'        => 'tab_nav_active_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#F1F1F1'
			),
			'tab_nav_font_color' => array(
				'label'       => esc_html__( 'Text', 'echo-knowledge-base' ),
				'name'        => 'tab_nav_font_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#B3B3B3'
			),
			'tab_nav_background_color' => array(
				'label'       => esc_html__( 'Background', 'echo-knowledge-base' ),
				'name'        => 'tab_nav_background_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'tab_nav_border_color' => array(
				'label'       => esc_html__( 'Border', 'echo-knowledge-base' ),
				'name'        => 'tab_nav_border_color',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#686868'
			),
		);

		return $config_specification;
	}
}
