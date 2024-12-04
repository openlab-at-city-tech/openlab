<?php

namespace LottaFramework\Facades;

/**
 * @method static settings() array
 * @method static restore( array $settings )
 * @method static reset()
 * @method static register( $args, bool $sub = false )
 * @method static get( string $id, array $settings = [] ) mixed
 * @method static checked( string $id ) bool
 * @method static display( string $id, $visible = 'block' ) mixed
 * @method static layers( string $id ) array
 * @method static repeater( string $id ) array
 * @method static imgAttrs( string $id ) array
 * @method static hasImage( string $id ) bool
 * @method static getSettingArgs( $id ) mixed
 * @method static addSection( \WP_Customize_Manager $WP_customize, $id, $args = [], $controls = [] )
 * @method static addControl( \WP_Customize_Manager $wp_customize, $args, bool $has_control = true )
 * @method static getSubControls( $args )
 * @method static changeObject( \WP_Customize_Manager $wp_customize, string $type, string $id, $property, $value )
 * @method static registerPartials( \WP_Customize_Manager $wp_customize )
 * @method static bindSelectiveRefresh( $partial, $setting )
 * @method static addPartial( $id, $selector, $render_callback )
 * @method static addAsync( $id, $args )
 */
class CZ extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return \LottaFramework\Customizer\Customizer::class;
	}
}