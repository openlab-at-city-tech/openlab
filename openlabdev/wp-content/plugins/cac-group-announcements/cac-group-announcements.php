<?php

/*
Plugin Name: CAC Group Announcements
Plugin URI: http://commons.gc.cuny.edu
Description: Creates a group tab where admins and mods can send email announcements to the members of the group
Version: 0.1
Author: Boone Gorges - CUNY Academic Commons
Author URI: http://teleogistic.net
*/

/*  Copyright 2010  Boone Gorges - CUNY Academic Commons  (email : boonebgorges@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


function cac_group_announcements_widgets_init() {
	require( dirname( __FILE__ ) . '/cac-group-announcements-bp-functions.php' );
}
add_action( 'bp_init', 'cac_group_announcements_widgets_init' );
?>