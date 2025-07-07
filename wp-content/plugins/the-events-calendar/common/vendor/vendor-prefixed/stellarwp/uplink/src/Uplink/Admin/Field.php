<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Admin;

use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Resources\Collection;

abstract class Field {
	/**
	 * Path to page template
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $path = '';

	/**
	 * @var Group
	 */
	protected $group;

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	abstract public function register_settings(): void;

	/**
	 * Renders the field.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $show_title Whether to show the title or not.
	 * @param bool $show_button Whether to show the submit button or not.
	 *
	 * @return void
	 */
	abstract public function render( bool $show_title = true, bool $show_button = true ): void;

	public function __construct( Group $group ) {
		$this->group = $group;
	}

	/**
	 * @param array<string> $args
	 *
	 * @return void
	 */
	public function get_description( array $args = [] ): void {
		if ( empty( $args['description'] ) ) {
			return;
		}

		printf(
			'<p class="regular-text">%s</p>',
			esc_html( $args['description'] )
		);
	}

	/**
	 * @param array<string> $args
	 *
	 * @return string
	 */
	public function get_html_content( array $args = [] ) : string {
		if ( empty( $args['html'] ) ) {
			return '';
		}

		return $args['html'];
	}

	/**
	 * @param array<string> $args
	 *
	 * @return void
	 */
	public function field_html( array $args = [] ): void {
		$field = sprintf(
			'<div class="%6$s" id="%2$s" data-slug="%2$s" data-plugin="%9$s" data-plugin-slug="%10$s" data-action="%11$s">
                    <fieldset class="stellarwp-uplink__settings-group">
                        <input type="%1$s" name="%3$s" value="%4$s" placeholder="%5$s" class="regular-text stellarwp-uplink__settings-field" />
                        %7$s
                    </fieldset>
				    %8$s
				</div>',
			esc_attr( $args['type'] ),
			esc_attr( $args['path'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['value'] ),
			esc_attr( $args['placeholder'] ),
			esc_attr( $args['html_classes'] ?: '' ),
			$this->get_html_content( $args ),
			$this->add_nonce_field(),
			esc_attr( $args['plugin'] ),
			esc_attr( $args['plugin_slug'] ),
			esc_attr( Config::get_hook_prefix_underscored() )
		);

		echo apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/license_field_html_render', $field, $args );

		$this->get_description( $args );
	}

	/**
	 * @return string
	 */
	public function add_nonce_field() : string {
		return '<input type="hidden" value="' . esc_attr( wp_create_nonce( $this->group->get_name() )  ). '" class="wp-nonce" />';
	}

	/**
	 * @param array<mixed> $context
	 *
	 * @return false|string
	 */
	protected function get_content( array $context = [] ) {
		extract( $context );
		ob_start();
		include $this->get_path();

		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	public function get_path() : string {
		return apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/field-template_path', dirname( __DIR__, 2 ) . $this->path, $this->path );
	}

	/**
	 * Get the collection of Plugins/Services.
	 *
	 * @return Collection
	 */
	protected function get_resources(): Collection {
		return Config::get_container()->get( Collection::class );
	}

	/**
	 * Prints out the settings fields for a particular settings section.
	 *
	 * Part of the Settings API. Use this in a settings page to output
	 * a specific section. Should normally be called by do_settings_sections()
	 * rather than directly.
	 *
	 * @global array $wp_settings_fields Storage array of settings fields and their pages/sections.
	 *
	 * @since 1.0.0
	 *
	 * @param string $page Slug title of the admin page whose settings fields you want to show.
	 * @param string $section Slug title of the settings section whose fields you want to show.
	 * @param string $plugin_slug Slug title of the settings section whose fields you want to show.
	 * @param bool   $show_title Whether to show the title or not.
	 */
	public function do_settings_fields( string $page, string $section, string $plugin_slug, bool $show_title = true ): void {
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
			$class = '';

			if ( ! empty( $field['args']['class'] ) ) {
				$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
			}

			if ( ! empty( $plugin_slug ) ) {
				$field['args']['slug'] = $plugin_slug;
			}

			if ( $show_title ) {
				echo "<tr{$class}>";
				if ( ! empty( $field['args']['label_for'] ) ) {
					echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
				} else {
					echo '<th scope="row">' . $field['title'] . '</th>';
				}

				echo '<td>';
			}

			call_user_func( $field['callback'], $field['args'] );

			if ( $show_title ) {
				echo '</td>';
				echo '</tr>';
			}
		}
	}
}
