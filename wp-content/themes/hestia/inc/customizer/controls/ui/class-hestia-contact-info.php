<?php
/**
 * A custom text control for Contact info.
 *
 * @package Hestia
 * @since Hestia 1.1.10
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

/**
 * A custom text control for Contact info.
 *
 * @since Hestia 1.0
 */
class Hestia_Contact_Info extends WP_Customize_Control {

	/**
	 * Enqueue function.
	 */
	public function enqueue() {
		Hestia_Plugin_Install_Helper::instance()->enqueue_scripts();
	}

	/**
	 * Render content for the control.
	 *
	 * @since Hestia 1.0
	 */
	public function render_content() {
		if ( ! defined( 'WPFORMS_VERSION' ) ) {

			echo '<span class="customize-control-title">' . esc_html__( 'Instructions', 'hestia' ) . '</span>';
			printf(
				/* translators: %1$s is Plugin name */
				esc_html__( 'In order to add a contact form to this section, you need to install the %1$s plugin. Then follow %2$sthis guide%3$s to create your form.', 'hestia' ),
				esc_html( 'WPForms Lite' ),
				'<a href="' . esc_url( 'https://docs.themeisle.com/article/949-how-to-create-the-hestia-contact-form-in-wpforms' ) . '" target="_blank">',
				'</a>'
			);
			echo $this->create_plugin_install_button(
				'wpforms-lite',
				array(
					'redirect' => admin_url( 'customize.php' ) . '?autofocus[control]=hestia_contact_form_shortcode',
				)
			);
		}
	}

	/**
	 * Create plugin install button.
	 *
	 * @param string $slug plugin slug.
	 *
	 * @return bool
	 */
	public function create_plugin_install_button( $slug, $settings = array() ) {
		return Hestia_Plugin_Install_Helper::instance()->get_button_html( $slug, $settings );
	}
}
