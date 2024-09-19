<?php
/**
 * The pro customize section extends the WP_Customize_Control class.
 *
 * @package kadence
 */

if ( ! class_exists( 'WP_Customize_Section' ) ) {
	return;
}


/**
 * Class Kadence_Section_Pro
 *
 * @access public
 */
class Kadence_Section_Pro extends WP_Customize_Section {
	/**
	 * Control type
	 *
	 * @var string
	 */
	public $type = 'kadence_section_pro';

	/**
	 * Link for pro version.
	 *
	 * @since  1.0.10
	 * @access public
	 * @var    string
	 */
	public $pro_link = '';
	/**
	 * Gather the parameters passed to client JavaScript via JSON.
	 *
	 * @since 4.1.0
	 *
	 * @return array The array to be exported to the client as JSON.
	 */
	public function json() {
		$json             = parent::json();
		$json['pro_link'] = esc_url_raw( $this->pro_link );
		return $json;
	}

	/**
	 * An Underscore (JS) template for rendering this section.
	 *
	 * Class variables for this section class are available in the `data` JS object;
	 * export custom variables by overriding WP_Customize_Section::json().
	 *
	 * @since 4.3.0
	 *
	 * @see WP_Customize_Section::print_template()
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-section cannon-expand control-section-{{ data.type }}">
			<h3 class="wp-ui-highlight">
				<a href="{{ data.pro_link }}" class="wp-ui-text-highlight" target="_blank" rel="noopener">{{ data.title }}</a>
			</h3>
		</li>
		<?php
	}
}
