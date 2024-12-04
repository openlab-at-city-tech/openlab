<?php


namespace ColibriWP\Theme\Customizer;

use ColibriWP\Theme\Customizer\Panel\ColibriPanel;
use WP_Customize_Panel;

class PanelFactory {
	private static $panels = array(
		'colibri_panel' => ColibriPanel::class,

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

		$panel = new $class( $wp_customize, $id, $data );
		$wp_customize->add_panel( $panel );

		return $panel;
	}

	private static function register() {
		if ( ! static::$registered ) {

			foreach ( static::$panels as $key => $panel ) {
				global $wp_customize;

				if ( ! in_array( $key, static::$register_exclusion ) ) {
					$wp_customize->register_panel_type( $panel );
				}
			}

			static::$registered = true;
		}
	}

	private static function getClassByType( $type ) {

		static::register();

		$class = isset( static::$panels[ $type ] ) ? static::$panels [ $type ] : WP_Customize_Panel::class;

		return $class;
	}
}
