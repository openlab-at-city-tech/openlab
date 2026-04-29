<?php

namespace Imagely\NGG\DynamicStylesheets;

use Imagely\NGG\Display\View;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Settings\Settings;

/**
 * Manager for dynamic stylesheets.
 */
class Manager {

	/**
	 * Instances of the manager.
	 *
	 * @var array
	 */
	protected static $_instances = [];

	/**
	 * Registered templates.
	 *
	 * @var array<string>
	 */
	protected $templates = [];

	/**
	 * Get an instance of the manager.
	 *
	 * @param string|bool $context The context for the instance.
	 * @return Manager The manager instance.
	 */
	public static function get_instance( $context = false ) {
		if ( ! isset( self::$_instances[ $context ] ) ) {
			self::$_instances[ $context ] = new Manager();
		}
		return self::$_instances[ $context ];
	}

	/**
	 * Registers a template with the dynamic stylesheet utility. A template must be registered before it can be loaded.
	 *
	 * @param string $name The template name.
	 * @param string $template The template path.
	 */
	public function register( $name, $template ) {
		$this->templates[ $name ] = $template;
	}

	/**
	 * Get the CSS template by name.
	 *
	 * @param string $name The template name.
	 * @return string|false The template path or false if not found.
	 */
	public function get_css_template( $name ) {
		return $this->templates[ $name ];
	}

	/**
	 * Loads a template, along with the dynamic variables to be interpolated.
	 *
	 * @param string $name The template name.
	 * @param array  $data The data to pass to the template (optional).
	 */
	public function enqueue( $name, $data = [] ) {
		$template_name = $this->get_css_template( $name );
		if ( false !== $template_name ) {
			if ( is_subclass_of( $data, 'C_DataMapper_Model' ) ) {
				$data = $data->get_entity();
			}

			if ( defined( 'NGG_INLINE_DYNAMIC_CSS' ) && NGG_INLINE_DYNAMIC_CSS ) {
				$view = new View( $template_name, $data );
				$css  = $view->render( true );

				\wp_enqueue_style(
					'ngg_dyncss',
					StaticAssets::get_url( 'Display/DynamicStylesheets.css' ),
					[],
					NGG_SCRIPT_VERSION
				);
				wp_add_inline_style( 'ngg_dyncss', $css );
			} else {
				// Prevent albums with many children from creating GET URL that exceed the RFC limits.
				if ( isset( $data->original_album_entities ) ) {
					unset( $data->original_album_entities );
				}

				$slug         = Settings::get_instance()->get( 'dynamic_stylesheet_slug' );
				$encoded_data = $this->encode( $data );
				\wp_enqueue_style(
					'dyncss-' . $template_name . $encoded_data . '@dynamic',
					"/{$slug}?name={$name}&data={$encoded_data}",
					[],
					NGG_SCRIPT_VERSION
				);
			}
		}
	}

	/**
	 * Encodes $data
	 *
	 * Base64 encoding uses '==' to denote the end of the sequence, but keep it out of the url
	 *
	 * @param mixed $data The data to encode.
	 * @return string The encoded data.
	 */
	public function encode( $data ) {
		$data = wp_json_encode( $data );
		$data = base64_encode( $data );
		$data = str_replace( '/', '\\', $data );
		$data = rtrim( $data, '=' );
		return $data;
	}

	/**
	 * Decodes $data
	 *
	 * @param mixed $data The data to decode.
	 * @return array|mixed The decoded data.
	 */
	public function decode( $data ) {
		$data = str_replace( '\\', '/', $data );
		$data = base64_decode( $data . '==' );
		$data = json_decode( $data );
		return $data;
	}
}
