<?php
/*
Plugin Name: Custom taxonomy
Description: Checks whether the taxonomies assigned to a post via its terms are still registered in WordPress. Reports a broken relationship when a taxonomy has been unregistered (e.g. by deactivating a plugin or theme).
Version: 1.0
Author: Broken Link Checker

ModuleID: custom_taxonomy
ModuleCategory: container
ModuleClassName: blcTaxonomyContainerManager
*/

require_once __DIR__ . '/custom_taxonomy/class-blc-taxonomy-container.php';
require_once __DIR__ . '/custom_taxonomy/class-blc-taxonomy-container-manager.php';
