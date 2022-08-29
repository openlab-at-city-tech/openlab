<?php

namespace ElementsKit_Lite\Libs\Framework\Classes;

defined( 'ABSPATH' ) || exit;

class Plugin_Status {
	private static $instance;
	private $installedPlugins = array();
	private $activatedPlugins = array();

	public function __construct() {
		$this->collect_installed_plugins();
		$this->collect_activated_plugins();
	}

	private function collect_installed_plugins() {
		foreach ( get_plugins() as $key => $plugin ) {
			array_push( $this->installedPlugins, $key );
		}
	}

	private function collect_activated_plugins() {
		foreach ( apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) as $plugin ) {
			array_push( $this->activatedPlugins, $plugin );
		}
	}

	public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function get_installed_plugins() {
		return $this->installedPlugins;
	}

	public function get_activated_plugins() {
		return $this->activatedPlugins;
	}

	public function get_status( $name ) {
		$data = array(
			'url'              => '',
			'activation_url'   => '',
			'installation_url' => '',
			'title'            => '',
			'status'           => '',
		);

		if ( $this->check_installed_plugin( $name ) ) {
			if ( $this->check_activated_plugin( $name ) ) {
				$data['title']  = __( 'Activated', 'elementskit-lite' );
				$data['status'] = 'activated';
			} else {
				$data['title']          = __( 'Activate Now', 'elementskit-lite' );
				$data['status']         = 'installed';
				$data['activation_url'] = $this->activation_url( $name );
			}
		} else {
			$data['title']            = __( 'Install Now', 'elementskit-lite' );
			$data['status']           = 'not_installed';
			$data['installation_url'] = $this->installation_url( $name );
			$data['activation_url']   = $this->activation_url( $name );
		}

		return $data;
	}

	public function check_installed_plugin( $name ) {
		return in_array( $name, $this->installedPlugins );
	}

	public function check_activated_plugin( $name ) {
		return in_array( $name, $this->activatedPlugins );
	}

	public function activation_url( $pluginName ) {

		return wp_nonce_url(
			add_query_arg(
				array(
					'action'        => 'activate',
					'plugin'        => $pluginName,
					'plugin_status' => 'all',
					'paged'         => '1&s',
				),
				admin_url( 'plugins.php' )
			),
			'activate-plugin_' . $pluginName 
		);
	}

	public function installation_url( $pluginName ) {
		$action     = 'install-plugin';
		$pluginSlug = $this->get_plugin_slug( $pluginName );

		return wp_nonce_url(
			add_query_arg(
				array(
					'action' => $action,
					'plugin' => $pluginSlug,
				),
				admin_url( 'update.php' )
			),
			$action . '_' . $pluginSlug
		);
	}

	public function get_plugin_slug( $name ) {
		$split = explode( '/', $name );

		return isset( $split[0] ) ? $split[0] : null;
	}

	public function activated_url( $pluginName ) {
		return add_query_arg(
			array(
				'page' => $this->get_plugin_slug( $pluginName ),
			),
			admin_url( 'admin.php' ) 
		);
	}
}
