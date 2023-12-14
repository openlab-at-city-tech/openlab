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

	static $_proxy = null;

	public function get_proxy() {
		if ( ! self::$_proxy ) {
			self::$_proxy = new Installer();
		}
		return self::$_proxy;
	}

	public function install( $reset = false ) {
		$this->get_proxy()->install( $reset );
	}

	public function uninstall() {
		$this->get_proxy()->uninstall();
	}


	public function __call( $method, $args ) {
		try {
			$klass = new \ReflectionMethod( $this->get_proxy(), $method );
			return $klass->invokeArgs( $this->get_proxy(), $args );
		} catch ( \Exception $exception ) {
			return null; }
	}
}
