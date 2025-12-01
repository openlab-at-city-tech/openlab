<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Admin\Fields;

use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\View\Contracts\View;
use TEC\Common\StellarWP\Uplink\Resources\Resource;
// Use function statement is problematic with Strauss.
use TEC\Common\StellarWP\Uplink as UplinkNamespace;
use TEC\Common\StellarWP\Uplink\Admin\Asset_Manager;
use TEC\Common\StellarWP\Uplink\Admin\Group;

class Field {
	/**
	 * @var Resource
	 */
	protected Resource $resource;

	/**
	 * @var string
	 */
	protected string $field_id = '';

	/**
	 * @var string
	 */
	protected string $field_name = '';

	/**
	 * @var string
	 */
	protected string $label = '';

	/**
	 * @var string
	 */
	protected string $slug = '';

	/**
	 * @var bool
	 */
	protected bool $show_label = false;

	/**
	 * @var bool
	 */
	protected bool $show_heading = false;

	/**
	 * @var string
	 */
	protected const VIEW = 'admin/fields/field';

	/**
	 * @var View
	 */
	protected $view;

	/**
	 * @var Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * @var Group
	 */
	protected $group;

	/**
	 * Constructor!
	 *
	 * @param  View  $view  The View Engine to render views.
	 */
	public function __construct( View $view, Asset_Manager $asset_manager, Group $group ) {
		$this->view          = $view;
		$this->asset_manager = $asset_manager;
		$this->group         = $group;
	}

	/**
	 * Sets the resource.
	 *
	 * @param  Resource  $resource  The resource.
	 *
	 * @return static
	 */
	public function set_resource( Resource $resource ): self {
		$this->resource = $resource;

		return $this;
	}

	/**
	 * Gets the field ID.
	 *
	 * @return string
	 */
	public function get_field_id(): string {
		if ( empty( $this->field_id ) ) {
			return $this->resource->get_license_object()->get_key_option_name();
		}

		return $this->field_id;
	}

	/**
	 * Gets the field name.
	 *
	 * @return string
	 */
	public function get_field_name(): string {
		return $this->field_name;
	}

	/**
	 * Gets the field value.
	 *
	 * @return string
	 */
	public function get_field_value(): string {
		return $this->resource->get_license_key();
	}

	/**
	 * Gets the  HTML for the key status information.
	 *
	 * @return string
	 */
	public function get_key_status_html(): string {
		$html = $this->view->render( 'admin/fields/key-status' );

		/**
		 * Filters the key status HTML.
		 *
		 * @param string $html The HTML.
		 * @param string $slug The plugin slug.
		 */
		return apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/license_field_key_status_html', $html, $this->get_slug() );
	}

	/**
	 * Gets the field label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return $this->label;
	}

	/**
	 * Gets the nonce action.
	 *
	 * @return string
	 */
	public function get_nonce_action() : string {
		/**
		 * Filters the nonce action.
		 *
		 * @param string $group The Settings group.
		 */
		return apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/license_field_group_name', Config::get_hook_prefix_underscored() );
	}

	/**
	 * Gets the nonce field.
	 *
	 * @return string
	 */
	public function get_nonce_field(): string {
		$nonce_name   = "stellarwp-uplink-license-key-nonce__" . $this->get_slug();
		$nonce_action = $this->group->get_name();

		return '<input type="hidden" class="wp-nonce-fluent" name="' . esc_attr( $nonce_name ) . '" value="' . esc_attr( wp_create_nonce( $nonce_action ) ) . '" />';
	}

	/**
	 * Gets the field placeholder.
	 *
	 * @return string
	 */
	public function get_placeholder(): string {
		return __( 'License key', '%TEXTDOMAIN%' );
	}

	/**
	 * Gets the product name.
	 *
	 * @return string
	 */
	public function get_product(): string {
		return $this->resource->get_path();
	}

	/**
	 * Gets the product slug.
	 *
	 * @return string
	 */
	public function get_product_slug(): string {
		return $this->resource->get_slug();
	}

	/**
	 * Gets the field slug.
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return $this->resource->get_slug();
	}

	/**
	 * Gets the field classes.
	 *
	 * @return string
	 */
	public function get_classes(): string {
		return 'stellarwp-uplink-license-key-field';
	}

	/**
	 * Renders the field.
	 *
	 * @return void
	 */
	public function render( array $args = [] ): void {
		echo $this->get_render_html();
	}

	/**
	 * Returns the rendered field HTML.
	 *
	 * @return string
	 */
	public function get_render_html(): string {
		$this->asset_manager->enqueue_assets();

		$args = [
			'field' => $this,
			'resource' => $this->resource,
			'group' => $this->group->get_name( $this->get_slug() ),
		];

		$html = $this->view->render( self::VIEW, $args );

		/**
		 * Filters the field HTML.
		 *
		 * @param string $html The HTML.
		 * @param string $slug The plugin slug.
		 */
		return apply_filters(
			'stellarwp/uplink/' . Config::get_hook_prefix() . '/license_field_html',
			$html,
			$this->get_slug()
		);
	}

	/**
	 * Sets the field ID.
	 *
	 * @param string $field_id Field ID.
	 *
	 * @return self
	 */
	public function set_field_id( string $field_id ): self {
		$this->field_id = $field_id;

		return $this;
	}

	/**
	 * Sets the field name.
	 *
	 * @param string $field_name Field name.
	 *
	 * @return self
	 */
	public function set_field_name( string $field_name ): self {
		$this->field_name = $field_name;

		return $this;
	}

	/**
	 * Sets the field label.
	 *
	 * @param string $label Field label.
	 *
	 * @return self
	 */
	public function set_label( string $label ): self {
		$this->label = $label;

		return $this;
	}

	/**
	 * Whether to show the field heading.
	 *
	 * @return bool
	 */
	public function should_show_heading(): bool {
		return $this->show_heading;
	}

	/**
	 * Whether to show the field label.
	 *
	 * @return bool
	 */
	public function should_show_label(): bool {
		return $this->show_label;
	}

	/**
	 * Whether to show the field heading.
	 *
	 * @param bool $state Whether to show the field heading.
	 *
	 * @return $this
	 */
	public function show_heading( bool $state = true ): self {
		$this->show_heading = $state;

		return $this;
	}

	/**
	 * Whether to show the field label.
	 *
	 * @param bool $state Whether to show the field label.
	 *
	 * @return $this
	 */
	public function show_label( bool $state = true ): self {
		$this->show_label = $state;

		return $this;
	}
}
