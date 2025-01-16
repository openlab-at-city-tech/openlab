<?php

namespace LottaFramework\Customizer;

class CallToActionSection extends \WP_Customize_Section {

	function __construct( $manager, $id, $args = array() ) {
		$manager->register_section_type( self::class );

		parent::__construct( $manager, $id, $args );
	}

	/**
	 * The type of customize section being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'lotta-cta-section';

	/**
	 * Call to action desc
	 *
	 * @var string
	 */
	public $desc = '';

	/**
	 * @var array
	 */
	public $link = array();

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.0.0
	 */
	public function json() {
		$json = parent::json();

		$json['desc'] = $this->desc;
		$json['link'] = $this->link;

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
        <li id="{{ data.id }}"
            class="lotta-cta-section accordion-section control-section control-section-{{ data.type }} cannot-expand">
            <h3 class="accordion-section-title" tabindex="0">
                <a href="{{ data.link.url }}" target="{{ data.link.target }}" class="button button-primary"
                   type="button">
                    {{ data.title }}
                </a>

                <# if (data.desc) { #>
                <span class="desc">
                    {{{ data.desc }}}
                </span>
                <# } #>
            </h3>
        </li>
		<?php
	}

}
