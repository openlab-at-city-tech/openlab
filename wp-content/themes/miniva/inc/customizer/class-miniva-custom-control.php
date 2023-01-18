<?php
/**
 * Custom Control for the Customizer
 *
 * @package Miniva
 */

/**
 * Displays a custom html.
 */
class Miniva_Custom_Control extends WP_Customize_Control {

	/**
	 * Whitelist heading parameter
	 *
	 * @var string
	 */
	public $heading = '';

	/**
	 * Whitelist content parameter
	 *
	 * @var string
	 */
	public $content = '';

	/**
	 * Whitelist link parameter
	 *
	 * @var string
	 */
	public $link = '';


	/**
	 * Whitelist link text parameter
	 *
	 * @var string
	 */
	public $link_text = '';

	/**
	 * Render Control
	 */
	public function render_content() {
		if ( ! empty( $this->heading ) ) {
			echo '<span class="customize-control-heading">' . esc_html( $this->heading ) . '</span>';
		}
		if ( ! empty( $this->label ) ) {
			echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
		}
		if ( ! empty( $this->content ) ) {
			echo wp_kses_post( $this->content );
		}
		if ( ! empty( $this->description ) ) {
			echo '<span class="description customize-control-description">' . wp_kses_post( $this->description ) . '</span>';
		}
		if ( ! empty( $this->link ) ) {
			echo '<p>';
			echo '<a href="' . esc_url( $this->link ) . '" target="_blank" class="button button-secondary">';
			if ( isset( $this->link_text ) ) {
				echo esc_html( $this->link_text );
			}
			echo '</a>';
		}
	}
}
