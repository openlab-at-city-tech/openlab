<?php
/**
 * Kadence\Template_Tags class
 *
 * @package kadence
 */

namespace Kadence;

use InvalidArgumentException;
use BadMethodCallException;
use RuntimeException;

/**
 * Template tags entry point.
 *
 * This class provides access to all available template tag methods.
 *
 * Its instance can be accessed through `kadence()`. For example, if there is a template tag called `posted_on`, it can
 * be accessed via `kadence()->posted_on()`.
 *
 * @method mixed block_header_options( mixed ...$args )
 * @method mixed customizer_palette_option( mixed ...$args )
 * @method mixed customizer_quick_link( mixed ...$args )
 * @method mixed default( mixed ...$args )
 * @method mixed desk_transparent_header( mixed ...$args )
 * @method mixed display_fallback_menu( mixed ...$args )
 * @method mixed display_footer_nav_menu( mixed ...$args )
 * @method mixed display_footer_row( mixed ...$args )
 * @method mixed display_header_row( mixed ...$args )
 * @method mixed display_mobile_header_row( mixed ...$args )
 * @method mixed display_mobile_nav_menu( mixed ...$args )
 * @method mixed display_primary_nav_menu( mixed ...$args )
 * @method mixed display_secondary_nav_menu( mixed ...$args )
 * @method mixed display_sidebar( mixed ...$args )
 * @method mixed footer_column_item_count( mixed ...$args )
 * @method mixed get_asset_version( mixed ...$args )
 * @method mixed get_breadcrumb( mixed ...$args )
 * @method mixed get_default_palette( mixed ...$args )
 * @method mixed get_feature_position( mixed ...$args )
 * @method mixed get_icon( mixed ...$args )
 * @method mixed get_option_name( mixed ...$args )
 * @method mixed get_option_type( mixed ...$args )
 * @method mixed get_palette( mixed ...$args )
 * @method mixed get_palette_for_customizer( mixed ...$args )
 * @method mixed get_post_types_objects( mixed ...$args )
 * @method mixed get_post_types_to_ignore( mixed ...$args )
 * @method mixed get_pro_url( mixed ...$args )
 * @method mixed get_public_post_types_to_ignore( mixed ...$args )
 * @method mixed get_template( mixed ...$args )
 * @method mixed get_title_layout( mixed ...$args )
 * @method mixed get_transparent_post_types_to_ignore( mixed ...$args )
 * @method mixed has_center_column( mixed ...$args )
 * @method mixed has_content( mixed ...$args )
 * @method mixed has_footer( mixed ...$args )
 * @method mixed has_header( mixed ...$args )
 * @method mixed has_header_styles( mixed ...$args )
 * @method mixed has_mobile_center_column( mixed ...$args )
 * @method mixed has_mobile_side_columns( mixed ...$args )
 * @method mixed has_side_columns( mixed ...$args )
 * @method mixed has_sidebar( mixed ...$args )
 * @method mixed is_amp( mixed ...$args )
 * @method mixed is_footer_nav_menu_active( mixed ...$args )
 * @method mixed is_mobile_nav_menu_active( mixed ...$args )
 * @method mixed is_primary_nav_menu_active( mixed ...$args )
 * @method mixed is_secondary_nav_menu_active( mixed ...$args )
 * @method mixed mobile_transparent_header( mixed ...$args )
 * @method mixed option( mixed ...$args )
 * @method mixed palette_option( mixed ...$args )
 * @method mixed print_breadcrumb( mixed ...$args )
 * @method mixed print_icon( mixed ...$args )
 * @method mixed print_microdata( mixed ...$args )
 * @method mixed print_styles( mixed ...$args )
 * @method mixed render_archive_title( mixed ...$args )
 * @method mixed render_footer( mixed ...$args )
 * @method mixed render_header( mixed ...$args )
 * @method mixed render_title( mixed ...$args )
 * @method mixed show_comments( mixed ...$args )
 * @method mixed show_feature( mixed ...$args )
 * @method mixed show_feature_above( mixed ...$args )
 * @method mixed show_feature_below( mixed ...$args )
 * @method mixed show_hero_title( mixed ...$args )
 * @method mixed show_in_content_title( mixed ...$args )
 * @method mixed show_post_navigation( mixed ...$args )
 * @method mixed sidebar_id_class( mixed ...$args )
 * @method mixed sidebar_options( mixed ...$args )
 * @method mixed sub_option( mixed ...$args )
 * @method mixed the_comments( mixed ...$args )
 * @method mixed using_amp_live_list_comments( mixed ...$args )
 */
class Template_Tags {

	/**
	 * Associative array of all available template tags.
	 *
	 * Method names are the keys, their callback information the values.
	 *
	 * @var array
	 */
	protected $template_tags = [];

	/**
	 * Constructor.
	 *
	 * Sets the theme components.
	 *
	 * @param array $components Optional. List of theme templating components. Each of these must implement the
	 *                          Templating_Component_Interface interface.
	 *
	 * @throws InvalidArgumentException Thrown if one of the $components does not implement
	 *                                  Templating_Component_Interface.
	 */
	public function __construct( array $components = [] ) {

		// Set the template tags for the components.
		foreach ( $components as $component ) {

			// Bail if a templating component is invalid.
			if ( ! $component instanceof Templating_Component_Interface ) {
				throw new InvalidArgumentException(
					sprintf(
						/* translators: 1: classname/type of the variable, 2: interface name */
						__( 'The theme templating component %1$s does not implement the %2$s interface.', 'kadence' ),
						gettype( $component ),
						Templating_Component_Interface::class
					)
				);
			}

			$this->set_template_tags( $component );
		}
	}

	/**
	 * Magic call method.
	 *
	 * Will proxy to the template tag $method, unless it is not available, in which case an exception will be thrown.
	 *
	 * @param string $method Template tag name.
	 * @param array  $args   Template tag arguments.
	 * @return mixed Template tag result, or null if template tag only outputs markup.
	 *
	 * @throws BadMethodCallException Thrown if the template tag does not exist.
	 */
	public function __call( string $method, array $args ) {
		if ( ! isset( $this->template_tags[ $method ] ) ) {
			throw new BadMethodCallException(
				sprintf(
					/* translators: %s: template tag name */
					__( 'The template tag %s does not exist.', 'kadence' ),
					'kadence()->' . $method . '()'
				)
			);
		}

		return call_user_func_array( $this->template_tags[ $method ]['callback'], $args );
	}

	/**
	 * Sets template tags for a given theme templating component.
	 *
	 * @param Templating_Component_Interface $component Theme templating component.
	 *
	 * @throws InvalidArgumentException Thrown when one of the template tags is invalid.
	 * @throws RuntimeException         Thrown when one of the template tags conflicts with an existing one.
	 */
	protected function set_template_tags( Templating_Component_Interface $component ) {
		$tags = $component->template_tags();

		foreach ( $tags as $method_name => $callback ) {
			if ( is_callable( $callback ) ) {
				$callback = [ 'callback' => $callback ];
			}

			if ( ! is_array( $callback ) || ! isset( $callback['callback'] ) ) {
				throw new InvalidArgumentException(
					sprintf(
						/* translators: 1: template tag method name, 2: component class name */
						__( 'The template tag method %1$s registered by theme component %2$s must either be a callable or an array.', 'kadence' ),
						$method_name,
						get_class( $component )
					)
				);
			}

			if ( isset( $this->template_tags[ $method_name ] ) ) {
				throw new RuntimeException(
					sprintf(
						/* translators: 1: template tag method name, 2: component class name */
						__( 'The template tag method %1$s registered by theme component %2$s conflicts with an already registered template tag of the same name.', 'kadence' ),
						$method_name,
						get_class( $component )
					)
				);
			}

			$this->template_tags[ $method_name ] = $callback;
		}
	}
}
