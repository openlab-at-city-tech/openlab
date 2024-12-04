<?php


namespace ColibriWP\Theme\Customizer;

use ColibriWP\Theme\Customizer\Sections\ColibriSection;
use WP_Customize_Section;

class SectionFactory {
	private static $sections = array(
		'colibri_section' => ColibriSection::class,
	);

	private static $register_exclusion = array();
	private static $registered         = false;

	public static function make( $id, $data ) {

		$data = array_merge(
			array(
				'type' => 'default',
			),
			$data
		);

		$class = static::getClassByType( $data['type'] );

		global $wp_customize;

		unset( $data['type'] );

		$section = new $class( $wp_customize, $id, $data );
		$wp_customize->add_section( $section );

		return $section;
	}

	private static function register() {
		if ( ! static::$registered ) {

			foreach ( static::$sections as $key => $section ) {
				global $wp_customize;

				if ( ! in_array( $key, static::$register_exclusion ) ) {
					$wp_customize->register_section_type( $section );
				}
			}

			static::$registered = true;
		}
	}

	private static function getClassByType( $type ) {

		static::register();

		$class = isset( static::$sections[ $type ] ) ? static::$sections [ $type ] : WP_Customize_Section::class;

		return $class;
	}
}
