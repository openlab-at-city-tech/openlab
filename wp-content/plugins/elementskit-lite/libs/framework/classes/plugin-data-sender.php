<?php

namespace ElementsKit_Lite\Libs\Framework\Classes;

use ElementsKit_Lite\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

class Plugin_Data_Sender {

	use Singleton;

	private $installedPlugins = array();
	private $themes           = array();
	private $activatedPlugins = array();

	public function __construct() {
		$this->set_activated_plugins();
		$this->set_installed_plugins();
		$this->setThemes();
	}

	private function set_activated_plugins() {
		foreach ( apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) as $plugin ) {
			array_push( $this->activatedPlugins, $plugin );
		}
	}

	private function set_installed_plugins() {
		foreach ( get_plugins() as $key => $plugin ) {
			$status = false;
			if ( in_array( $key, $this->activatedPlugins ) ) {
				$status = true;
			}
			array_push(
				$this->installedPlugins,
				array(
					'name'      => $plugin['Name'],
					'version'   => $plugin['Version'],
					'is_active' => $status,
				) 
			);
		}
	}

	private function setThemes() {
		$activeTheme = wp_get_theme()->get( 'Name' );
		foreach ( wp_get_themes() as $key => $theme ) {
			array_push(
				$this->themes,
				array(
					'name'      => $theme->Name,
					'version'   => $theme->Version,
					'is_active' => $activeTheme == $theme->Name,
				)
			);
		}
	}


	private function getUrl( $route ) {
		return \ElementsKit_Lite::account_url() . '/sync/api/' . $route;
	}

	public function send( $route ) {
		return wp_remote_post(
			$this->getUrl( $route ),
			array(
				'method'      => 'POST',
				'data_format' => 'body',
				'headers'     => array(
					'Content-Type' => 'application/json',
				),
				'body'        => json_encode( $this->get_data() ),
			)
		);
	}

	public function sendAutomizyData( $route, $data ) {
		return wp_remote_post(
			$this->getUrl( $route ),
			array(
				'method'      => 'POST',
				'data_format' => 'body',
				'headers'     => array(
					'Content-Type' => 'application/json',
				),
				'body'        => json_encode( $data ),
			)
		);
	}

	public function get_data() {
		global $wpdb;
		return array(
			'environment_id'     => 1,
			'domain'             => get_site_url(),
			'total_user'         => count_users()['total_users'],
			'themes'             => $this->themes,
			'plugins'            => $this->installedPlugins,
			'php_version'        => phpversion(),
			'db_version'         => $wpdb->db_version(),
			'server_name'        => explode( ' ', isset($_SERVER['SERVER_SOFTWARE']) ? sanitize_text_field(wp_unslash($_SERVER['SERVER_SOFTWARE'])) : '' )[0],
			'max_execution_time' => ini_get( 'max_execution_time' ),
			'php_memory_size'    => ini_get( 'memory_limit' ),
			'language'           => get_locale(),
		);
	}
}
