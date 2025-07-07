<?php

namespace TEC\Common\StellarWP\Installer\Contracts;

interface Handler {
	/**
	 * Activates the resource.
	 *
	 * @since 1.0.0
	 */
	public function activate();

	/**
	 * Clears the local properties that cache install and activation states.
	 *
	 * @since 1.0.0
	 */
	public function clear_install_and_activation_cache($plugin);

	/**
	 * Gets the js action of the resource.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_js_action(): string;

	/**
	 * Gets the name of the resource.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Gets the slug of the resource.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_slug(): string;

	/**
	 * Handles the request.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function handle_request();

	/**
	 * Installs the resource.
	 *
	 * @since 1.0.0
	 */
	public function install();

	/**
	 * Checks if the resource is active.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean True if active.
	 */
	public function is_active(): bool;

	/**
	 * Checks if the resource is installed.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean True if installed.
	 */
	public function is_installed(): bool;
}
