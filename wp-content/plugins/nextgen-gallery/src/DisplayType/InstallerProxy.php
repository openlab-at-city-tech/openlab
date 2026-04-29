<?php

namespace Imagely\NGG\DisplayType;

/**
 * This class exists entirely because Pro's Film module extends C_Gallery_Display_Installer (which is now renamed to
 * \Imagely\NGG\DisplayType\Installer) but lacks the $hard = FALSE parameter in it's uninstall() method which creates
 * a fatal error. Until Pro no longer uses the legacy class this proxy is used.
 *
 * @deprecated
 * @TODO Remove this when POPE compatibility level one is reached
 */
class InstallerProxy {

	/**
	 * Proxy instance
	 *
	 * @var Installer|null
	 */
	public static $_proxy = null;

	/**
	 * Gets the proxy installer instance
	 *
	 * @return Installer
	 */
	public function get_proxy() {
		if ( ! self::$_proxy ) {
			self::$_proxy = new Installer();
		}
		return self::$_proxy;
	}

	/**
	 * Installs the display type
	 *
	 * @param bool $reset Whether to reset the installation.
	 * @return void
	 */
	public function install( $reset = false ) {
		$this->get_proxy()->install( $reset );
	}

	/**
	 * Uninstalls the display type
	 *
	 * @return void
	 */
	public function uninstall() {
		$this->get_proxy()->uninstall();
	}

	/**
	 * Magic method to proxy calls to the installer
	 *
	 * @param string $method The method name.
	 * @param array  $args The method arguments.
	 * @return mixed|null
	 */
	public function __call( $method, $args ) {
		try {
			$klass = new \ReflectionMethod( $this->get_proxy(), $method );
			return $klass->invokeArgs( $this->get_proxy(), $args );
		} catch ( \Exception $exception ) {
			return null; }
	}
}
