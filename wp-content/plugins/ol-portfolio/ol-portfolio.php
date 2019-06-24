<?php
/**
 * Plugin Name: OpenLab Portfolio
 * Description: OpneLab ePortolio feature.
 * Author: OpenLab
 * Version: 1.0.0
 * License: GPL-2.0-or-later
 */

namespace OpenLab\Portfolio;

const ROOT_DIR  = __DIR__;
const ROOT_FILE = __FILE__;

if ( file_exists( ROOT_DIR . '/vendor/autoload.php' ) ) {
	require ROOT_DIR . '/vendor/autoload.php';
}

Portfolio::create();
