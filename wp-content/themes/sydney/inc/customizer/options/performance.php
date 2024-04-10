<?php
/**
 * Performance Customizer Options
 *
 * @package Sydney
 */

$wp_customize->add_section(
    'sydney_section_performance',
    array(
        'title'      => esc_html__( 'Performance', 'sydney' ),
        'panel'      => 'sydney_panel_general',
        'priority'   => 999
    )
);

$wp_customize->add_setting(
	'perf_google_fonts_local',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox'
	)
);
$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'perf_google_fonts_local',
		array(
			'label'         	=> esc_html__( 'Load Google Fonts Locally?', 'sydney' ),
			'description'		=> esc_html__( 'This option refers only to Google Fonts loaded by the theme, not by third-party plugins.', 'sydney' ),
			'section'       	=> 'sydney_section_performance',
		)
	)
);

$wp_customize->add_setting(
	'perf_disable_preconnect',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox'
	)
);
$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'perf_disable_preconnect',
		array(
			'label'         	=> esc_html__( 'Disable Google Fonts preconnect', 'sydney' ),
			'section'       	=> 'sydney_section_performance',
		)
	)
);

$wp_customize->add_setting(
	'perf_disable_emojis',
	array(
		'default'           => 1,
		'sanitize_callback' => 'sydney_sanitize_checkbox'
	)
);

$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'perf_disable_emojis',
		array(
			'label'         	=> esc_html__( 'Disable Emojis', 'sydney' ),
			'section'       	=> 'sydney_section_performance',
		)
	)
);

$wp_customize->add_setting(
	'perf_jquery_migrate',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox'
	)
);

$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'perf_jquery_migrate',
		array(
			'label'         	=> esc_html__( 'Disable jQuery Migrate', 'sydney' ),
			'description'		=> esc_html__( 'This option will disable jQuery Migrate, which is used by some plugins to maintain backwards compatibility with older versions of jQuery.', 'sydney' ),
			'section'       	=> 'sydney_section_performance',
		)
	)
);

$wp_customize->add_setting(
	'perf_defer_block_styles',
	array(
		'default'           => 0,
		'sanitize_callback' => 'sydney_sanitize_checkbox'
	)
);

$wp_customize->add_control(
	new Sydney_Toggle_Control(
		$wp_customize,
		'perf_defer_block_styles',
		array(
			'label'         	=> esc_html__( 'Defer block styles', 'sydney' ),
			'description'		=> esc_html__( 'Checks if a post has block content and defers the block stylesheet if it does not.', 'sydney' ),
			'section'       	=> 'sydney_section_performance',
		)
	)
);

$wp_customize->add_setting( 'perf_guide_link',
	array(
		'default' 			=> '',
		'sanitize_callback' => 'esc_attr'
	)
);

$wp_customize->add_control( new Sydney_Text_Control( $wp_customize, 'perf_guide_link',
		array(
			'description'		=> sprintf( esc_html__( 'For more information on how to improve your website\'s performance, please read our %1$sPerformance Guide%2$s.', 'sydney' ), '<a href="https://docs.athemes.com/article/how-to-speed-up-your-sydney-powered-website/" target="_blank">', '</a>' ),
			'section' 			=> 'sydney_section_performance',
			'separator'			=> 'before'
		)
	)
);