<?php
/**
 * POPE framework loader.
 *
 * Vendored from awesomemotive/pope-framework v0.19. See ../VENDORED.md.
 *
 * The POPE_VERSION guard is load-bearing: another plugin or theme shipping its own
 * copy of POPE (e.g. the legacy Photocrati theme) may have already defined these
 * classes, in which case this file must be a no-op.
 *
 * @package NextGEN Gallery
 */

if (!defined('POPE_VERSION')) {
	define('POPE_VERSION', '0.17');
	require_once(__DIR__ . '/class.pope_cache.php');
	require_once(__DIR__ . '/class.extensibleobject.php');
	require_once(__DIR__ . '/interface.component.php');
	require_once(__DIR__ . '/class.component.php');
	require_once(__DIR__ . '/interface.component_factory.php');
	require_once(__DIR__ . '/class.component_factory.php');
	require_once(__DIR__ . '/class.component_registry.php');
	require_once(__DIR__ . '/interface.pope_module.php');
	require_once(__DIR__ . '/class.base_module.php');
	require_once(__DIR__ . '/class.base_product.php');
}
