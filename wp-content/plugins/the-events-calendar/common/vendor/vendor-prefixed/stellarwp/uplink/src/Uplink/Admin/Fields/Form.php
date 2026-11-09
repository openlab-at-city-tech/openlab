<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Admin\Fields;

use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Uplink;
use TEC\Common\StellarWP\Uplink\Components\Controller;

class Form extends Controller{
	/**
	 * @var array<string, Field>
	 */
	protected array $fields = [];

	/**
	 * @var string
	 */
	protected string $slug = '';

	/**
	 * @var bool
	 */
	protected bool $show_button = true;

	/**
	 * @var string
	 */
	protected string $button_text = '';

	/**
	 * @var string
	 */
	protected const VIEW = 'admin/fields/form';

	/**
	 * Adds a field to the form.
	 *
	 * @param Field $field
	 *
	 * @return $this
	 */
	public function add_field( Field $field ): self {
		$this->fields[ $field->get_slug() ] = $field;

		return $this;
	}

	/**
	 * Gets the button text.
	 *
	 * @return string
	 */
	public function get_button_text(): string {
		if ( empty( $this->button_text ) ) {
			return esc_html__( 'Save Changes', 'tribe-common' );
		}

		return $this->button_text;
	}

	/**
	 * Gets the fields.
	 *
	 * @return array<string, Field>
	 */
	public function get_fields(): array {
		return $this->fields;
	}

	/**
	 * Renders the form.
	 *
	 * @return void
	 */
	public function render( array $args = [] ): void {
		echo $this->get_render_html();
	}

	/**
	 * Renders the form.
	 *
	 * @return string
	 */
	public function get_render_html(): string {
		$args = [ 'form' => $this ];
		$html = $this->view->render( self::VIEW, $args );

		/**
		 * Filters the form HTML.
		 *
		 * @since 2.0.0
		 *
		 * @param string $html The form HTML.
		 */
		return apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/license_form_html', $html );
	}

	/**
	 * Sets the submit button text.
	 *
	 * @param string $button_text The text to display on the button.
	 *
	 * @return $this
	 */
	public function set_button_text( string $button_text ): self {
		$this->button_text = $button_text;

		return $this;
	}

	/**
	 * Whether to show the field label.
	 *
	 * @param bool   $state       Whether to show the field label.
	 * @param string $button_text The button text.
	 *
	 * @return $this
	 */
	public function show_button( bool $state = true, string $button_text = '' ): self {
		if ( ! empty( $button_text ) ) {
			$this->set_button_text( $button_text );
		}

		$this->show_button = $state;

		return $this;
	}

	/**
	 * Whether to show the button.
	 *
	 * @return bool
	 */
	public function should_show_button(): bool {
		return $this->show_button;
	}
}
