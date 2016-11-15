<?php
/*
Plugin Name: [UNMAINTAINED] TaskFreak! Free
Plugin URI: http://www.taskfreak.com
Description: Task Management made easy
Version: 1.0.19
Author: Tirzen
Author URI: http://www.tirzen.com
License: GPL2

Copyright 2013  Tirzen SARL  (email : taskfreakwp@tirzen.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

define('TFK_ROOT_FILE', __FILE__);
define('TFK_ROOT_PATH', plugin_dir_path(__FILE__));
define('TZN_PROJECT_PREFIX', 'tfk');

load_plugin_textdomain('taskfreak', false, basename(dirname( __FILE__ )).'/languages');

include TFK_ROOT_PATH.'inc/install.php';

include TFK_ROOT_PATH.'inc/classes/models.php';
include TFK_ROOT_PATH.'inc/classes/tools.php';
include TFK_ROOT_PATH.'inc/classes/controller.php';

include TFK_ROOT_PATH.'inc/models/log.php';
include TFK_ROOT_PATH.'inc/models/user.php';
include TFK_ROOT_PATH.'inc/models/project.php';
include TFK_ROOT_PATH.'inc/models/item.php';

// add_action('plugins_loaded', 'tfk_loaded_init');

if (is_admin()) {
	include TFK_ROOT_PATH.'inc/controllers/admin.php';
} else {
	include TFK_ROOT_PATH.'inc/controllers/front.php';
}
