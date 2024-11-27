<?php

namespace Advanced_Sidebar_Menu\Widget;

use Advanced_Sidebar_Menu\__Temp_Id_Proxy;
use Advanced_Sidebar_Menu\Utils;

/**
 * Common checkbox methods used in widgets.
 *
 * @since        9.6.0
 *
 * @template SETTINGS of array<string, mixed>
 */
trait Checkbox {
	/**
	 * Hide an element_key if a controlling_checkbox is checked.
	 *
	 * @param string  $controlling_checkbox                       - Name of controlling_checkbox field which controls whether to hide this
	 *                                                            element or not.
	 * @param ?string $element_key                                - Match the `element_to_reveal` passed to $this->checkbox() for the
	 *                                                            checkbox which controls this.
	 * @param bool    $reverse                                    - hide on check instead of show on check.
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
		// Append the hide to a data attribute, so it can be picked up only if the advanced-sidebar-menu JS is present.
		// Prevents hiding of elements when widgets are loaded in unique ways like ajax.
		if ( $hide ) {
			?>
			data-advanced-sidebar-menu-hide="1"
			<?php
		}
	}


	/**
	 * Outputs a <input type="checkbox"> with the id and name filled.
	 *
	 * @param string  $name              - Name of field.
	 * @param ?string $element_to_reveal - Element to reveal/hide when box is checked/unchecked.
	 *
	 * @return void
	 */
	public function checkbox( $name, $element_to_reveal = null ): void {
		?>
		<!--suppress HtmlFormInputWithoutLabel -->
		<input
			id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>"
			type="checkbox"
			value="checked"
			data-js="advanced-sidebar-menu/widget/<?php echo esc_attr( __Temp_Id_Proxy::factory( $this )->get_id_base() ); ?>/<?php echo esc_attr( $name ); ?>"
			<?php
			if ( null !== $element_to_reveal ) {
				echo ' onclick="window.advancedSidebarMenuAdmin.clickReveal( \'' . esc_attr( $this->get_field_id( $element_to_reveal ) ) . '\')" ';
			}
			echo $this->checked( $name ) ? 'checked' : '';
			?>
		/>
		<?php
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
}
