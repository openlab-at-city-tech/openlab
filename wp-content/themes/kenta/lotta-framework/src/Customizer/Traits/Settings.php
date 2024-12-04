<?php

namespace LottaFramework\Customizer\Traits;

use LottaFramework\Customizer\Control;
use LottaFramework\Utils;

trait Settings {

	/**
	 * Save all settings
	 *
	 * @var array
	 */
	protected $_settings = [];

	/**
	 * Get all settings
	 *
	 * @return array
	 */
	public function settings() {
		return $this->_settings;
	}

	/**
	 * Restore settings
	 *
	 * @param array $settings
	 *
	 * @return void
	 */
	public function restore($settings) {
		$this->_settings = $settings;
	}

	/**
	 * Register customize setting
	 *
	 * @param $args
	 * @param false $sub
	 */
	public function register( $args, bool $sub = false ) {
		if ( $args instanceof Control ) {
			$args = $args->toArray();
		}

		$id = $args['setting']['id'] ?? $args['id'];

		if ( ! isset( $this->options[ $id ] ) ) {
			$this->_settings[ $id ] = $args;
		}

		if ( $sub ) {
			foreach ( $this->getSubControls( $args ) as $control ) {
				$this->register( $control, $sub );
			}
		}
	}

	/**
	 * @param $id
	 *
	 * @return array|mixed
	 */
	public function getSettingArgs( $id ) {
		return $this->_settings[ $id ] ?? [];
	}

	/**
	 * Get setting
	 *
	 * @param $id
	 * @param array $settings
	 *
	 * @return mixed|void|null
	 */
	public function get( $id, array $settings = [] ) {

		if ( isset( $settings[ $id ] ) ) {
			return $settings[ $id ];
		}

		$settings = $this->_settings;
		if ( ! isset( $settings[ $id ] ) ) {
			return null;
		}

		return $settings[ $id ]['default'] ?? null;
	}

	/**
	 * Is this option checked
	 *
	 * @param $id
	 * @param array $settings
	 *
	 * @return bool
	 */
	public function checked( $id, array $settings = [] ): bool {
		return $this->get( $id, $settings ) === 'yes';
	}

	/**
	 * Get display value
	 *
	 * @param $id
	 * @param string $visible
	 * @param array $settings
	 *
	 * @return array|mixed|string|void|null
	 */
	public function display( $id, string $visible = 'block', array $settings = [] ) {

		$value = $this->get( $id, $settings );
		if ( ! is_array( $value ) ) {
			$value = $value === 'yes' ? $visible : 'none';
		} else {
			foreach ( $value as $device => $v ) {
				$value[ $device ] = $v === 'yes' ? $visible : 'none';
			}
		}

		return $value;
	}

	/**
	 * Get available layers from layers control
	 *
	 * @param $id
	 * @param array $settings
	 *
	 * @return array
	 */
	public function layers( $id, array $settings = [] ) {
		$layers = $this->get( $id, $settings );
		if ( ! is_array( $layers ) ) {
			return [];
		}

		$result = [];

		foreach ( $layers as $layer ) {
			if ( isset( $layer['id'] ) && ( $layer['visible'] ?? false ) ) {
				$result[] = $layer['id'];
			}
		}

		return $result;
	}

	/**
	 *  Get available items from repeater control
	 *
	 * @param $id
	 * @param array $settings
	 *
	 * @return array
	 */
	public function repeater( $id, $settings = [] ) {
		$items = $this->get( $id, $settings );
		if ( ! is_array( $items ) ) {
			return [];
		}

		$result = [];

		foreach ( $items as $item ) {
			if ( isset( $item['settings'] ) && ( $item['visible'] ?? false ) ) {
				$result[] = $item['settings'];
			}
		}

		return $result;
	}

	/**
	 * Get img tag attrs from image uploader control value
	 *
	 * @param $id
	 * @param array $settings
	 *
	 * @return array
	 */
	public function imgAttrs( $id, $settings = [] ) {
		$image      = $this->get( $id, $settings );
		$image_attr = [];

		if ( ! empty( $image ) && isset( $image['url'] ) && ! empty( $image['url'] ) ) {
			$attachment_id     = $image['attachment_id'] ?? 0;
			$alt_attribute     = $attachment_id <= 0 ? [] : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			$alt_attribute     = ! empty( $alt_attribute ) ? $alt_attribute : get_bloginfo( 'name' );
			$image_attr['src'] = esc_url( $image['url'] ?? '' );
			$image_attr['alt'] = esc_attr( $alt_attribute );
		}

		return $image_attr;
	}

	/**
	 * Has image
	 *
	 * @param $id
	 * @param array $settings
	 *
	 * @return bool
	 */
	public function hasImage( $id, $settings = [] ) {
		return ! empty( $this->imgAttrs( $id, $settings ) );
	}

	/**
	 * Get sub controls
	 *
	 * @param $args
	 *
	 * @return array
	 */
	protected function getSubControls( $args ) {
		if ( ! isset( $args['controls'] ) || ! isset( $args['options'] ) || empty( $args['controls'] ) ) {
			return [];
		}

		$controls = [];

		foreach ( $args['controls'] as $path => $collapse ) {
			$value    = Utils::array_path( $args['options'], $path );
			$controls = array_merge( $controls, $collapse ? Utils::array_collapse( $value ) : $value );
		}

		return $controls;
	}
}
