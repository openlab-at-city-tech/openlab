<?php

if (!defined('POPE_VERSION')) {
	define('POPE_VERSION', '0.17');
	require_once('class.pope_cache.php');
	require_once('class.extensibleobject.php');
	require_once('interface.component.php');
	require_once('class.component.php');
	require_once('interface.component_factory.php');
	require_once('class.component_factory.php');
	require_once('class.component_registry.php');
	require_once('interface.pope_module.php');
	require_once('class.base_module.php');
	require_once('class.base_product.php');
}
