<?php
namespace Advanced_Sidebar_Menu\Widget;

use Advanced_Sidebar_Menu\Utils;

/**
 * Base class for this plugin's widgets.
 *
 * @author OnPoint Plugins
 */
abstract class Widget_Abstract extends \WP_Widget {
	/**
	 * The current widget instance
	 *
	 * @var array
	 */
	protected $widget_settings;


	/**
	 * Store the instance to this class.
	 * We do this manually because filters hit the instance before we
	 * get to self::form() and self::widget()
	 *
	 * @see   \WP_Widget::form_callback()
	 *
	 * @param array $instance - widget settings.
	 * @param array $defaults - defaults for all widgets.
	 *
	 * @return array
	 */
	public function set_instance( array $instance, array $defaults ) {
		$this->widget_settings = wp_parse_args( $instance, $defaults );

		return $this->widget_settings;
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
	public function checked( $name ) {
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
	public function hide_element( $controlling_checkbox, $element_key = null, $reverse = false ) {
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
	public function checkbox( $name, $element_to_reveal = null ) {
		if ( ! \array_key_exists( $name, $this->widget_settings ) ) {
			$this->widget_settings[ $name ] = null;
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
