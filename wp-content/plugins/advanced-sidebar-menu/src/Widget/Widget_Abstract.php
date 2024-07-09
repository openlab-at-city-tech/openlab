<?php
namespace Advanced_Sidebar_Menu\Widget;

use Advanced_Sidebar_Menu\Utils;

/**
 * Base class for this plugin's widgets.
 *
 * @author OnPoint Plugins
 * @phpstan-type WIDGET_ARGS array{
 *      name?:          string,
 *      id?:            string,
 *      id_increment?:  string,
 *      description?:   string,
 *      class?:         string,
 *      before_widget:  string,
 *      after_widget:   string,
 *      before_title:   string,
 *      after_title:    string,
 *      before_sidebar?:string,
 *      after_sidebar?: string,
 *      show_in_rest?:  boolean,
 *      widget_id?:     string,
 *      widget_name?:   string,
 * }
 *
 * @phpstan-template SETTINGS of array<string, string|int|array<string, string>>
 *
 * @phpstan-method SETTINGS set_instance( SETTINGS $instance, array $defaults )
 * @extends \WP_Widget<SETTINGS>
 */
abstract class Widget_Abstract extends \WP_Widget {
	/**
	 * The current widget instance
	 *
	 * @var SETTINGS
	 */
	protected $widget_settings;


	/**
	 * Support legacy method which has been moved to the Instance_Trait
	 * while we wait for the required basic version to be 9.5.0.
	 *
	 * @todo                     Remove this method once the required basic version is 9.5.0.
	 *
	 * @param string                    $name      - Name of method.
	 * @param array{SETTINGS, SETTINGS} $arguments - Arguments passed to method.
	 *
	 * @throws \BadMethodCallException -- If method does not exist.
	 * @phpstan-ignore-next-line
	 */
	public function __call( string $name, array $arguments ): array {
		if ( 'set_instance' === $name ) {
			$this->widget_settings = wp_parse_args( $arguments[0], $arguments[1] );

			return $this->widget_settings;
		}
		throw new \BadMethodCallException( esc_html( 'Method ' . $name . ' does not exist' ) );
	}


	/**
	 * Is this checkbox checked?
	 *
	 * Checks first for a value then verifies the value = 'checked'.
	 *
	 * @param string $name - Name of checkbox.
	 *
	 * @return bool
	 */
	public function checked( $name ): bool {
		return Utils::instance()->is_checked( $name, $this->widget_settings );
	}


	/**
	 * Hide an element_key if a controlling_checkbox is checked.
	 *
	 * @param string $controlling_checkbox - Name of controlling_checkbox field which controls whether to hide this
	 *                                     element or not.
	 * @param string $element_key          - Match the `element_to_reveal` passed to $this->checkbox() for the checkbox
	 *                                     which controls this.
	 * @param bool   $reverse              - hide on check instead of show on check.
	 *
	 * @return void
	 */
	public function hide_element( $controlling_checkbox, $element_key = null, $reverse = false ): void {
		$hide = false;
		if ( ( $reverse && $this->checked( $controlling_checkbox ) ) || ( ! $reverse && ! $this->checked( $controlling_checkbox ) ) ) {
			$hide = true;
		}

		if ( null !== $element_key ) {
			?> data-js="<?php echo esc_attr( $this->get_field_id( $element_key ) ); ?>"
			class="advanced-sidebar-menu-<?php echo esc_attr( $element_key ); ?>"
			<?php
		}
		// Append the hide to a global variable, so it can be picked up only if the advanced-sidebar-menu JS is present.
		// Prevents hiding of elements when widgets are loaded in unique ways like ajax.
		if ( $hide ) {
			?>
			data-advanced-sidebar-menu-hide="1"
			<?php
		}
	}


	/**
	 * Outputs a <input type="checkbox"> with id and name filled.
	 *
	 * @param string      $name              - Name of field.
	 * @param string|null $element_to_reveal - Element to reveal/hide when box is checked/unchecked.
	 *
	 * @return void
	 */
	public function checkbox( $name, $element_to_reveal = null ): void {
		if ( ! \array_key_exists( $name, $this->widget_settings ) ) {
			$this->widget_settings[ $name ] = '';
		}

		?>
		<!--suppress HtmlFormInputWithoutLabel -->
		<input
			id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>"
			type="checkbox"
			value="checked"
			data-js="advanced-sidebar-menu/widget/<?php echo esc_attr( $this->id_base ); ?>/<?php echo esc_attr( $name ); ?>"
			<?php
			if ( null !== $element_to_reveal ) {
				echo ' onclick="window.advancedSidebarMenuAdmin.clickReveal( \'' . esc_attr( $this->get_field_id( $element_to_reveal ) ) . '\')" ';
			}
			echo $this->checked( $name ) ? 'checked' : '';
			?>
		/>
		<?php
	}
}
