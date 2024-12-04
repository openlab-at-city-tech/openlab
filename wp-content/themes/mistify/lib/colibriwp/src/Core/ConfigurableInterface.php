<?php


namespace ColibriWP\Theme\Core;

interface ConfigurableInterface {

	public static function options();

	public static function settingDefault( $name );

	public static function selectiveRefreshKey();

}
