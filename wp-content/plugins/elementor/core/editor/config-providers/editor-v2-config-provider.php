<?php
namespace Elementor\Core\Editor\Config_Providers;

use Elementor\Core\Utils\Collection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Editor_V2_Config_Provider implements Config_Provider_Interface {
	const PACKAGE_TYPE_APP = 'app';
	const PACKAGE_TYPE_EXTENSION = 'extension';
	const PACKAGE_TYPE_UTIL = 'util';

	/**
	 * Cached packages data.
	 *
	 * @var Collection
	 */
	private $packages_data;

	public function get_script_configs() {
		$packages_data = $this->get_packages_data();

		$apps_handles = $packages_data
			->filter( function ( $package_data ) {
				return static::PACKAGE_TYPE_APP === $package_data['type'];
			} )
			->map( function ( $package_data ) {
				return $package_data['handle'];
			} )
			->values();

		$environment_script_config = [
			'handle' => 'elementor-editor-environment-v2',
			'src' => '{{ELEMENTOR_ASSETS_URL}}js/editor-environment-v2{{MIN_SUFFIX}}.js',
			'deps' => [
				'elementor-packages-env',
			],
		];

		$loader_script_config = [
			'handle' => 'elementor-editor-loader-v2',
			'src' => '{{ELEMENTOR_ASSETS_URL}}js/editor-loader-v2{{MIN_SUFFIX}}.js',
			'deps' => array_merge(
				[ 'elementor-editor' ],
				$apps_handles
			),
		];

		return array_merge(
			Editor_Common_Configs::get_script_configs(),
			$packages_data->values(),
			[
				$environment_script_config,
				$loader_script_config,
			]
		);
	}

	public function get_script_handles_to_enqueue() {
		$types_to_enqueue = [ static::PACKAGE_TYPE_EXTENSION ];

		return $this->get_packages_data()
			->filter( function ( $package_data ) use ( $types_to_enqueue ) {
				return in_array( $package_data['type'], $types_to_enqueue, true );
			} )
			->map( function ( $package_data ) {
				return $package_data['handle'];
			} )
			// Must be first.
			->prepend( 'elementor-editor-environment-v2' )
			// Must be last.
			->push( 'elementor-editor-loader-v2' )
			->values();
	}

	public function get_client_env() {
		$client_env = apply_filters( 'elementor/editor-v2/packages/client-env', [] );

		$v2_env = [
			'handle' => 'elementor-editor-environment-v2',
			'name' => 'elementorEditorV2Env',
			'env' => $client_env,
		];

		return array_merge(
			Editor_Common_Configs::get_client_env(),
			[ $v2_env ]
		);
	}

	public function get_style_configs() {
		return array_merge(
			Editor_Common_Configs::get_style_configs(),
			[
				[
					'handle' => 'elementor-editor-v2-overrides',
					'src' => '{{ELEMENTOR_ASSETS_URL}}css/editor-v2-overrides{{MIN_SUFFIX}}.css',
					'deps' => [ 'elementor-editor' ],
				],
			]
		);
	}

	public function get_style_handles_to_enqueue() {
		return [
			'elementor-editor-v2-overrides',
			'elementor-editor',
		];
	}

	public function get_template_body_file_path() {
		return __DIR__ . '/../templates/editor-body-v2.view.php';
	}

	public function get_additional_template_paths() {
		return Editor_Common_Configs::get_additional_template_paths();
	}

	private function get_packages_data() {
		if ( ! $this->packages_data ) {
			// Loading the file that is responsible for registering the packages in the filter.
			$loader = ELEMENTOR_ASSETS_PATH . 'js/packages/loader.php';

			if ( file_exists( $loader ) ) {
				require_once $loader;
			}

			$packages_data = apply_filters( 'elementor/editor-v2/packages/config', [] );

			$this->packages_data = Collection::make( $packages_data )
				->filter( function ( $package_data ) {
					return ! empty( $package_data['handle'] );
				} )
				->map_with_keys( function ( $data, $name ) {
					return [
						$name => array_replace( [
							'type' => static::PACKAGE_TYPE_UTIL,
						], $data ),
					];
				} );
		}

		return $this->packages_data;
	}
}
