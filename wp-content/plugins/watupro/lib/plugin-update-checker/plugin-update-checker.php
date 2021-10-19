<?php
/**
 * Plugin Update Checker Library 4.10
 * http://w-shadow.com/
 *
 * Copyright 2020 Janis Elsts
 * Released under the MIT license. See license.txt for details.
 */
 
 // avoid fatal error in Play
if(!class_exists('PucFactory')) {
	class PucFactory {
		static function buildUpdateChecker($url, $path, $slug) {
			if(class_exists('Puc_v4_Factory')) Puc_v4_Factory::buildUpdateChecker($url, $path, $slug);
			return false;
		}
	}
} 

require dirname(__FILE__) . '/load-v4p10.php';