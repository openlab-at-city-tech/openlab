<?php
/*
Plugin Name: Calendar
Plugin URI: http://www.kieranoshea.com
Description: This plugin allows you to display a calendar of all your events and appointments as a page on your site.
Author: Kieran O'Shea
Author URI: http://www.kieranoshea.com
Version: 1.3.3
*/

/*  Copyright 2008  Kieran O'Shea  (email : kieran@kieranoshea.com)

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

// Enable internationalisation
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'calendar','wp-content/plugins/'.$plugin_dir, $plugin_dir);

// Define the tables used in Calendar
global $wpdb;
define('WP_CALENDAR_TABLE', $wpdb->prefix . 'calendar');
define('WP_CALENDAR_CONFIG_TABLE', $wpdb->prefix . 'calendar_config');
define('WP_CALENDAR_CATEGORIES_TABLE', $wpdb->prefix . 'calendar_categories');

// Check ensure calendar is installed and install it if not - required for
// the successful operation of most functions called from this point on
check_calendar();

// Create a master category for Calendar and its sub-pages
add_action('admin_menu', 'calendar_menu');

// Enable the ability for the calendar to be loaded from pages
add_filter('the_content','calendar_insert');
add_filter('the_content','minical_insert');

// Enable the ability for the lists to be loaded from pages
add_filter('the_content','upcoming_insert');
add_filter('the_content','todays_insert');

// Add the function that puts style information in the header
add_action('wp_head', 'calendar_wp_head');

// Add the function that deals with deleted users
add_action('delete_user', 'deal_with_deleted_user');

// Add the widgets if we are using version 2.8
add_action('widgets_init', 'widget_init_calendar_today');
add_action('widgets_init', 'widget_init_calendar_upcoming');
add_action('widgets_init', 'widget_init_events_calendar');

// Before we get on with the functions, we need to define the initial style used for Calendar

// Function to 
function call_caldav()
{
  
}

// Function to deal with events posted by a user when that user is deleted
function deal_with_deleted_user($id)
{
  global $wpdb;

  // Do the query
  $wpdb->get_results("UPDATE ".WP_CALENDAR_TABLE." SET event_author=".$wpdb->get_var("SELECT MIN(ID) FROM ".$wpdb->prefix."users",0,0)." WHERE event_author=".mysql_escape_string($id));
}

// Function to provide time with WordPress offset, localy replaces time()
function ctwo()
{
  return (time()+(3600*(get_option('gmt_offset'))));
}

// Function to add the calendar style into the header
function calendar_wp_head()
{
  global $wpdb;

  $style = $wpdb->get_var("SELECT config_value FROM " . WP_CALENDAR_CONFIG_TABLE . " WHERE config_item='calendar_style'");
  if ($style != '')
    {
	  echo '<style type="text/css">
';
          echo stripslashes($style).'
';
	  echo '</style>
';
    }
}

// Function to deal with adding the calendar menus
function calendar_menu() 
{
  global $wpdb;

  // Set admin as the only one who can use Calendar for security
  $allowed_group = 'manage_options';

  // Use the database to *potentially* override the above if allowed
  $configs = $wpdb->get_results("SELECT config_value FROM " . WP_CALENDAR_CONFIG_TABLE . " WHERE config_item='can_manage_events'");
  if (!empty($configs))
    {
      foreach ($configs as $config)
	{
	  $allowed_group = $config->config_value;
	}
    }

  // Add the admin panel pages for Calendar. Use permissions pulled from above
   if (function_exists('add_menu_page')) 
     {
       add_menu_page(__('Calendar','calendar'), __('Calendar','calendar'), $allowed_group, 'calendar', 'edit_calendar');
     }
   if (function_exists('add_submenu_page')) 
     {
       $calendar_manage_menu = add_submenu_page('calendar', __('Manage Calendar','calendar'), __('Manage Calendar','calendar'), $allowed_group, 'calendar', 'edit_calendar');
       add_action( "admin_head-".$calendar_manage_menu, 'calendar_add_javascript' );
       // Note only admin can change calendar options
       add_submenu_page('calendar', __('Manage Categories','calendar'), __('Manage Categories','calendar'), 'manage_options', 'calendar-categories', 'manage_categories');
       add_submenu_page('calendar', __('Calendar Config','calendar'), __('Calendar Options','calendar'), 'manage_options', 'calendar-config', 'edit_calendar_config');
     }
}

// Function to add the javascript to the admin header
function calendar_add_javascript()
{ 
  echo '<script type="text/javascript" src="';
  bloginfo('wpurl');
  echo '/wp-content/plugins/calendar/javascript.js"></script>
<style type="text/css">
.bcal-container{
	background-color: #fff;
	border-radius: 4px;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	float: left;
	padding: 5px;
	border: solid 1px #ccc;
	box-shadow: 0 0 3px #C0C0C0;
}
.bcal-table{
	border-collapse: separate;
	border-spacing: 0;
	border: solid 1px #A2A6AF;
	font-family: Tahoma, sans-serif;
	font-size: 11px;
	font-weight: normal;
	float: left;
	margin: 1px;
	padding: 0;
	width: 200px;
}
.bcal-table,
.bcal-table th,
.bcal-table td {
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
}
.bcal-table thead tr{
	background-color: #CECED2;
	border: 1px solid #A2A6AF;
	background-image: linear-gradient(bottom, rgb(206,206,210) 37%, rgb(241,240,242) 69%);
	background-image: -o-linear-gradient(bottom, rgb(206,206,210) 37%, rgb(241,240,242) 69%);
	background-image: -moz-linear-gradient(bottom, rgb(206,206,210) 37%, rgb(241,240,242) 69%);
	background-image: -webkit-linear-gradient(bottom, rgb(206,206,210) 37%, rgb(241,240,242) 69%);
	background-image: -ms-linear-gradient(bottom, rgb(206,206,210) 37%, rgb(241,240,242) 69%);
	background-image: -webkit-gradient(
		linear,
		left bottom,
		left top,
		color-stop(0.37, rgb(206,206,210)),
		color-stop(0.69, rgb(241,240,242))
	);
}
.bcal-table thead tr:first-child{
	line-height: 23px;
}
.bcal-table thead th{
	font-weight: bold;
	color: #404D5D;
	height: 20px;
	text-align: center;
}
.bcal-table tbody td{
	color: #3B4959;
	cursor: pointer;
	font-weight: normal;
	height: 25px;
	padding: 0 0 1px 1px;
	width: 25px;
	text-align: center;
}
.bcal-table tbody tr:first-child td{
	border-top: solid 1px #fff;
}
.bcal-table tbody td:first-child{
	border-left: none;
	padding: 0 0 1px;
}
.bcal-table .bcal-wday,
.bcal-table .bcal-wnum{
	font-weight: normal;
	border-right: 1px solid #A2A6AF;
	border-top: 1px solid #A2A6AF;
	border-bottom: 1px solid #A2A6AF;
	padding: 1px 1px 2px 2px;
}
.bcal-table .bcal-wnum{
	border-right: 1px solid #A2A6AF;
	border-bottom: 1px solid #A2A6AF;
	color: #CC6600;
	background-image: linear-gradient(bottom, rgb(255,225,159) 23%, rgb(255,250,234) 62%);
	background-image: -o-linear-gradient(bottom, rgb(255,225,159) 23%, rgb(255,250,234) 62%);
	background-image: -moz-linear-gradient(bottom, rgb(255,225,159) 23%, rgb(255,250,234) 62%);
	background-image: -webkit-linear-gradient(bottom, rgb(255,225,159) 23%, rgb(255,250,234) 62%);
	background-image: -ms-linear-gradient(bottom, rgb(255,225,159) 23%, rgb(255,250,234) 62%);
	background-image: -webkit-gradient(
		linear,
		left bottom,
		left top,
		color-stop(0.23, rgb(255,225,159)),
		color-stop(0.62, rgb(255,250,234))
	);
}
.bcal-table .bcal-wday:last-child{
	border-right: none;
}
.bcal-table .bcal-empty{
	background-color: #F6F6F7;
	border-right: solid 1px #fff;
	border-bottom: solid 1px #fff;
	color: #999;
	cursor: default;
}
.bcal-table tr:last-child .bcal-empty,
.bcal-table tr:last-child .bcal-week,
.bcal-table tr:last-child .bcal-date{
	border-bottom: none;
}
.bcal-table .bcal-date{
	border-right: solid 1px #fff;
	border-bottom: solid 1px #fff;
	background-color: #ECECEE;
}
.bcal-table .bcal-past{
	background-color: #e6e6e6;
	color: #999;
	cursor: default;
}
.bcal-table .bcal-today{
	background-color: #D6D6D1;
	color: #333;
}
.bcal-table .bcal-week{
	background-color: #FEF3DA;
	border-right: solid 1px #fff;
	border-bottom: solid 1px #fff;
	color: #CC6600;
	cursor: default;
}
.bcal-table .bcal-selected{
	background-color: #BDBDBD;
	color: #F2F2F2;
}
.bcal-table .bcal-over,
.bcal-table .bcal-date:hover{
	background-color: #B2B2A9;
	color: #fff;
}
.bcal-table .bcal-date:last-child,
.bcal-table .bcal-empty:last-child{
	border-right: none;
}
.bcal-table .bcal-past:hover{
	background-color: #e0e0e0;
	color: #666;
}
.bcal-table .bcal-month,
.bcal-table .bcal-navi,
.bcal-table .bcal-wnum,
.bcal-table .bcal-wday,
.bcal-table .bcal-week{
	cursor: text;
}
</style>
';
}

// Function to deal with loading the calendar into pages
function calendar_insert($content)
{
  if (preg_match('/\{CALENDAR*.+\}/',$content))
    {
      $cat_list = preg_split('/\{CALENDAR\;/',$content);
      if (sizeof($cat_list) > 1) {
	$cat_list = preg_split('/\}/',$cat_list[1]);
        $cat_list = $cat_list[0];
        $cal_output = calendar($cat_list);
      } else {
	$cal_output = calendar();
      }
      $content = preg_replace('/\{CALENDAR*.+\}/',$cal_output,$content);
    }
  return $content;
}

// Function to show a mini calendar in pages
function minical_insert($content)
{
  if (preg_match('/\{MINICAL*.+\}/',$content))
    {
      $cat_list= preg_split('/\{MINICAL\;/',$content);
      if (sizeof($cat_list) > 1) {
	$cat_list = preg_split('/\}/',$cat_list[1]);
	$cat_list= $cat_list[0];
	$cal_output = minical($cat_list);
      } else {
	$cal_output = minical();
      }
      $content = preg_replace('/\{MINICAL*.+\}/',$cal_output,$content);
    }
  return $content;
}

// Functions to allow the widgets to be inserted into posts and pages
function upcoming_insert($content)
{
  if (preg_match('/\{UPCOMING_EVENTS*.+\}/',$content))
    {
      $cat_list= preg_split('/\{UPCOMING_EVENTS\;/',$content);
      if (sizeof($cat_list) > 1) {
	$cat_list = preg_split('/\}/',$cat_list[1]);
	$cat_list= $cat_list[0];
	$cal_output = '<span class="page-upcoming-events">'.upcoming_events($cat_list).'</span>';
      } else {
	$cal_output = '<span class="page-upcoming-events">'.upcoming_events().'</span>';
      }
      $content = preg_replace('/\{UPCOMING_EVENTS*.+\}/',$cal_output,$content);
    }
  return $content;
}
function todays_insert($content)
{
  if (preg_match('/\{TODAYS_EVENTS*.+\}/',$content))
    {
      $cat_list= preg_split('/\{TODAYS_EVENTS\;/',$content);
      if (sizeof($cat_list) > 1) {
	$cat_list = preg_split('/\}/',$cat_list[1]);
	$cat_list= $cat_list[0];
	$cal_output = '<span class="page-todays-events">'.todays_events($cat_list).'</span>';
      } else {
	$cal_output = '<span class="page-todays-events">'.todays_events().'</span>';
      }
      $content = preg_replace('/\{TODAYS_EVENTS*.+\}/',$cal_output,$content);
    }
  return $content;
}

// Function to check what version of Calendar is installed and install if needed
function check_calendar()
{
  // Checks to make sure Calendar is installed, if not it adds the default
  // database tables and populates them with test data. If it is, then the 
  // version is checked through various means and if it is not up to date 
  // then it is upgraded.

  // Lets see if this is first run and create us a table if it is!
  global $wpdb, $initial_style;

  // All this style info will go into the database on a new install
  // This looks nice in the TwentyTen theme
  $initial_style = "    .calnk a:hover {
        background-position:0 0;
        text-decoration:none;  
        color:#000000;
        border-bottom:1px dotted #000000;
     }
    .calnk a:visited {
        text-decoration:none;
        color:#000000;
        border-bottom:1px dotted #000000;
    }
    .calnk a {
        text-decoration:none; 
        color:#000000; 
        border-bottom:1px dotted #000000;
    }
    .calnk a span { 
        display:none; 
    }
    .calnk a:hover span {
        color:#333333; 
        background:#F6F79B; 
        display:block;
        position:absolute; 
        margin-top:1px; 
        padding:5px; 
        width:150px; 
        z-index:100;
        line-height:1.2em;
    }
    .calendar-table {
        border:0 !important;
        width:100% !important;
        border-collapse:separate !important;
        border-spacing:2px !important;
    }
    .calendar-heading {
        height:25px;
        text-align:center;
        border:1px solid #D6DED5;
        background-color:#E4EBE3;
    }
    .calendar-next {
        width:25%;
        text-align:center;
    }
    .calendar-prev {
        width:25%;
        text-align:center;
    }
    .calendar-month {
        width:50%;
        text-align:center;
        font-weight:bold;
    }
    .normal-day-heading {
        text-align:center;
        width:25px;
        height:25px;
        font-size:0.8em;
        border:1px solid #DFE6DE;
        background-color:#EBF2EA;
    }
    .weekend-heading {
        text-align:center;
        width:25px;
        height:25px;
        font-size:0.8em;
        border:1px solid #DFE6DE;
        background-color:#EBF2EA;
        color:#FF0000;
    }
    .day-with-date {
        vertical-align:text-top;
        text-align:left;
        width:60px;
        height:60px;
        border:1px solid #DFE6DE;
    }
    .no-events {

    }
    .day-without-date {
        width:60px;
        height:60px;
        border:1px solid #E9F0E8;
    }
    span.weekend {
        color:#FF0000;
    }
    .current-day {
        vertical-align:text-top;
        text-align:left;
        width:60px;
        height:60px;
        border:1px solid #BFBFBF;
        background-color:#E4EBE3;
    }
    span.event {
        font-size:0.75em;
    }
    .kjo-link {
        font-size:0.75em;
        text-align:center;
    }
    .calendar-date-switcher {
        height:25px;
        text-align:center;
        border:1px solid #D6DED5;
        background-color:#E4EBE3;
    }
    .calendar-date-switcher form {
        margin:2px;
    }
    .calendar-date-switcher input {
        border:1px #D6DED5 solid;
        margin:0;
    }
    .calendar-date-switcher select {
        border:1px #D6DED5 solid;
        margin:0;
    }
    .calnk a:hover span span.event-title {
        padding:0;
        text-align:center;
        font-weight:bold;
        font-size:1.2em;
        margin-left:0px;
    }
    .calnk a:hover span span.event-title-break {
        width:96%;
        text-align:center;
        height:1px;
        margin-top:5px;
        margin-right:2%;
        padding:0;
        background-color:#000000;
        margin-left:0px;
    }
    .calnk a:hover span span.event-content-break {
        width:96%;
        text-align:center;
        height:1px;
        margin-top:5px;
        margin-right:2%;
        padding:0;
        background-color:#000000;
        margin-left:0px;
    }
    .page-upcoming-events {
        font-size:80%;
    }
    .page-todays-events {
        font-size:80%;
    }
    .calendar-table table,tbody,tr,td {
        margin:0 !important;
        padding:0 !important;
    }
    table.calendar-table {
        margin-bottom:5px !important;
    }
    .cat-key {
        width:100%;
        margin-top:30px;
        padding:5px;
        border:0 !important;
    }
    .cal-separate {
       border:0 !important;
       margin-top:10px;
    }
    table.cat-key {
       margin-top:5px !important;
       border:1px solid #DFE6DE !important;
       border-collapse:separate !important;
       border-spacing:4px !important;
       margin-left:2px !important;
       width:99.5% !important;
       margin-bottom:5px !important;
    }
    .cat-key td {
       border:0 !important;
    }";
     

  // Assume this is not a new install until we prove otherwise
  $new_install = false;
  $vone_point_one_upgrade = false;
  $vone_point_two_beta_upgrade = false;

  $wp_calendar_exists = false;
  $wp_calendar_config_exists = false;
  $wp_calendar_config_version_number_exists = false;

  // Determine the calendar version
  $tables = $wpdb->get_results("show tables");
  foreach ( $tables as $table )
    {
      foreach ( $table as $value )
        {
	  if ( $value == WP_CALENDAR_TABLE )
	    {
	      $wp_calendar_exists = true;
	    }
	  if ( $value == WP_CALENDAR_CONFIG_TABLE )
            {
              $wp_calendar_config_exists = true;
              
	      // We now try and find the calendar version number
              // This will be a lot easier than finding other stuff 
              // in the future.
	      $version_number = $wpdb->get_var("SELECT config_value FROM " . WP_CALENDAR_CONFIG_TABLE . " WHERE config_item='calendar_version'"); 
	      if ($version_number == "1.2")
		{
		  $wp_calendar_config_version_number_exists = true;
		}
            }
        }
    }

  if ($wp_calendar_exists == false && $wp_calendar_config_exists == false)
    {
      $new_install = true;
    }
  else if ($wp_calendar_exists == true && $wp_calendar_config_exists == false)
    {
      $vone_point_one_upgrade = true;
    }
  else if ($wp_calendar_exists == true && $wp_calendar_config_exists == true && $wp_calendar_config_version_number_exists == false)
    {
      $vone_point_two_beta_upgrade = true;
    }

  // Now we've determined what the current install is or isn't 
  // we perform operations according to the findings
  if ( $new_install == true )
    {
      $sql = "CREATE TABLE " . WP_CALENDAR_TABLE . " (
                                event_id INT(11) NOT NULL AUTO_INCREMENT ,
                                event_begin DATE NOT NULL ,
                                event_end DATE NOT NULL ,
                                event_title VARCHAR(30) NOT NULL ,
                                event_desc TEXT NOT NULL ,
                                event_time TIME ,
                                event_recur CHAR(1) ,
                                event_repeats INT(3) ,
                                event_author BIGINT(20) UNSIGNED ,
                                event_category BIGINT(20) UNSIGNED NOT NULL DEFAULT 1 ,
                                event_link TEXT ,
                                PRIMARY KEY (event_id)
                        )";
      $wpdb->get_results($sql);
      $sql = "CREATE TABLE " . WP_CALENDAR_CONFIG_TABLE . " (
                                config_item VARCHAR(30) NOT NULL ,
                                config_value TEXT NOT NULL ,
                                PRIMARY KEY (config_item)
                        )";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='can_manage_events', config_value='edit_posts'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='calendar_style', config_value='".$initial_style."'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='display_author', config_value='false'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='display_jump', config_value='false'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='display_todays', config_value='true'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='display_upcoming', config_value='true'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='display_upcoming_days', config_value=7";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='calendar_version', config_value='1.2'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='enable_categories', config_value='false'";
      $wpdb->get_results($sql);
      $sql = "CREATE TABLE " . WP_CALENDAR_CATEGORIES_TABLE . " ( 
                                category_id INT(11) NOT NULL AUTO_INCREMENT, 
                                category_name VARCHAR(30) NOT NULL , 
                                category_colour VARCHAR(30) NOT NULL , 
                                PRIMARY KEY (category_id) 
                             )";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO " . WP_CALENDAR_CATEGORIES_TABLE . " SET category_id=1, category_name='General', category_colour='#F6F79B'";
      $wpdb->get_results($sql);
    }
  else if ($vone_point_one_upgrade == true)
    {
      $sql = "ALTER TABLE ".WP_CALENDAR_TABLE." ADD COLUMN event_author BIGINT(20) UNSIGNED";
      $wpdb->get_results($sql);
      $sql = "UPDATE ".WP_CALENDAR_TABLE." SET event_author=".$wpdb->get_var("SELECT MIN(ID) FROM ".$wpdb->prefix."users",0,0);
      $wpdb->get_results($sql);
      $sql = "ALTER TABLE ".WP_CALENDAR_TABLE." MODIFY event_desc TEXT NOT NULL";
      $wpdb->get_results($sql);
      $sql = "CREATE TABLE " . WP_CALENDAR_CONFIG_TABLE . " (
                                config_item VARCHAR(30) NOT NULL ,
                                config_value TEXT NOT NULL ,
                                PRIMARY KEY (config_item)
                        )";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='can_manage_events', config_value='edit_posts'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='calendar_style', config_value='".$initial_style."'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='display_author', config_value='false'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='display_jump', config_value='false'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='display_todays', config_value='true'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='display_upcoming', config_value='true'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='display_upcoming_days', config_value=7";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='calendar_version', config_value='1.2'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='enable_categories', config_value='false'";
      $wpdb->get_results($sql);
      $sql = "ALTER TABLE ".WP_CALENDAR_TABLE." ADD COLUMN event_category BIGINT(20) UNSIGNED NOT NULL DEFAULT 1";
      $wpdb->get_results($sql);
      $sql = "ALTER TABLE ".WP_CALENDAR_TABLE." ADD COLUMN event_link TEXT";
      $wpdb->get_results($sql);
      $sql = "CREATE TABLE " . WP_CALENDAR_CATEGORIES_TABLE . " ( 
                                category_id INT(11) NOT NULL AUTO_INCREMENT, 
                                category_name VARCHAR(30) NOT NULL , 
                                category_colour VARCHAR(30) NOT NULL , 
                                PRIMARY KEY (category_id) 
                              )";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO " . WP_CALENDAR_CATEGORIES_TABLE . " SET category_id=1, category_name='General', category_colour='#F6F79B'";
      $wpdb->get_results($sql);
    }
  else if ($vone_point_two_beta_upgrade == true)
    {
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='calendar_version', config_value='1.2'";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO ".WP_CALENDAR_CONFIG_TABLE." SET config_item='enable_categories', config_value='false'";
      $wpdb->get_results($sql);
      $sql = "ALTER TABLE ".WP_CALENDAR_TABLE." ADD COLUMN event_category BIGINT(20) UNSIGNED NOT NULL DEFAULT 1";
      $wpdb->get_results($sql);
      $sql = "ALTER TABLE ".WP_CALENDAR_TABLE." ADD COLUMN event_link TEXT ";
      $wpdb->get_results($sql);
      $sql = "CREATE TABLE " . WP_CALENDAR_CATEGORIES_TABLE . " (
                                category_id INT(11) NOT NULL AUTO_INCREMENT, 
                                category_name VARCHAR(30) NOT NULL , 
                                category_colour VARCHAR(30) NOT NULL , 
                                PRIMARY KEY (category_id) 
                             )";
      $wpdb->get_results($sql);
      $sql = "INSERT INTO " . WP_CALENDAR_CATEGORIES_TABLE . " SET category_id=1, category_name='General', category_colour='#F6F79B'";
      $wpdb->get_results($sql);
      $sql = "UPDATE " . WP_CALENDAR_CONFIG_TABLE . " SET config_value='".$initial_style."' WHERE config_item='calendar_style'";
      $wpdb->get_results($sql);
    }
}

// Used on the manage events admin page to display a list of events
function wp_events_display_list(){

	global $wpdb;
	
	$events = $wpdb->get_results("SELECT * FROM " . WP_CALENDAR_TABLE . " ORDER BY event_begin DESC");
	
	if ( !empty($events) )
	{
?>
       	<table class="widefat page fixed" width="100%" cellpadding="3" cellspacing="3">
		        <thead>
			    <tr>
				<th class="manage-column" scope="col"><?php _e('ID','calendar') ?></th>
				<th class="manage-column" scope="col"><?php _e('Title','calendar') ?></th>
				<th class="manage-column" scope="col"><?php _e('Start Date','calendar') ?></th>
				<th class="manage-column" scope="col"><?php _e('End Date','calendar') ?></th>
		                <th class="manage-column" scope="col"><?php _e('Time','calendar') ?></th>
				<th class="manage-column" scope="col"><?php _e('Recurs','calendar') ?></th>
				<th class="manage-column" scope="col"><?php _e('Repeats','calendar') ?></th>
		                <th class="manage-column" scope="col"><?php _e('Author','calendar') ?></th>
		                <th class="manage-column" scope="col"><?php _e('Category','calendar') ?></th>
				<th class="manage-column" scope="col"><?php _e('Edit','calendar') ?></th>
				<th class="manage-column" scope="col"><?php _e('Delete','calendar') ?></th>
			    </tr>
		        </thead>
<?php
		$class = '';
		foreach ( $events as $event )
		{
			$class = ($class == 'alternate') ? '' : 'alternate';
			?>
			<tr class="<?php echo $class; ?>">
				<th scope="row"><?php echo stripslashes($event->event_id); ?></th>
				<td><?php echo stripslashes($event->event_title); ?></td>
				<td><?php echo stripslashes($event->event_begin); ?></td>
				<td><?php echo stripslashes($event->event_end); ?></td>
				<td><?php if ($event->event_time == '00:00:00') { echo __('N/A','calendar'); } else { echo stripslashes($event->event_time); } ?></td>
				<td>
				<?php 
					// Interpret the DB values into something human readable
					if ($event->event_recur == 'S') { echo __('Never','calendar'); } 
					else if ($event->event_recur == 'W') { echo __('Weekly','calendar'); }
					else if ($event->event_recur == 'M') { echo __('Monthly (date)','calendar'); }
			                else if ($event->event_recur == 'U') { echo __('Monthly (day)','calendar'); }
					else if ($event->event_recur == 'Y') { echo __('Yearly','calendar'); }
				?>
				</td>
				<td>
				<?php
				        // Interpret the DB values into something human readable
					if ($event->event_recur == 'S') { echo __('N/A','calendar'); }
					else if ($event->event_repeats == 0) { echo __('Forever','calendar'); }
					else if ($event->event_repeats > 0) { echo stripslashes($event->event_repeats).' '.__('Times','calendar'); }					
				?>
				</td>
				<td><?php $e = get_userdata($event->event_author); echo $e->display_name; ?></td>
                                <?php
				$sql = "SELECT * FROM " . WP_CALENDAR_CATEGORIES_TABLE . " WHERE category_id=".mysql_escape_string($event->event_category);
                                $this_cat = $wpdb->get_row($sql);
                                ?>
				<td style="background-color:<?php echo stripslashes($this_cat->category_colour);?>;"><?php echo stripslashes($this_cat->category_name); ?></td>
				<?php unset($this_cat); ?>
				<td><a href="<?php echo bloginfo('wpurl') ?>/wp-admin/admin.php?page=calendar&amp;action=edit&amp;event_id=<?php echo stripslashes($event->event_id);?>" class='edit'><?php echo __('Edit','calendar'); ?></a></td>
				<td><a href="
<?php echo wp_nonce_url(bloginfo('wpurl').'/wp-admin/admin.php?page=calendar&amp;action=delete&amp;event_id='.stripslashes($event->event_id),'calendar-delete_'.stripslashes($event->event_id)); ?>" class="delete" onclick="return confirm('<?php _e('Are you sure you want to delete this event?','calendar'); ?>')"><?php echo __('Delete','calendar'); ?></a></td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		?>
		<p><?php _e("There are no events in the database!",'calendar')	?></p>
		<?php	
	}
}


// The event edit form for the manage events admin page
function wp_events_edit_form($mode='add', $event_id=false)
{
	global $wpdb,$users_entries;
	$data = false;
	
	if ( $event_id !== false )
	{
		if ( intval($event_id) != $event_id )
		{
			echo "<div class=\"error\"><p>".__('Bad Monkey! No banana!','calendar')."</p></div>";
			return;
		}
		else
		{
			$data = $wpdb->get_results("SELECT * FROM " . WP_CALENDAR_TABLE . " WHERE event_id='" . mysql_escape_string($event_id) . "' LIMIT 1");
			if ( empty($data) )
			{
				echo "<div class=\"error\"><p>".__("An event with that ID couldn't be found",'calendar')."</p></div>";
				return;
			}
			$data = $data[0];
		}
		// Recover users entries if they exist; in other words if editing an event went wrong
		if (!empty($users_entries))
		  {
		    $data = $users_entries;
		  }
	}
	// Deal with possibility that form was submitted but not saved due to error - recover user's entries here
	else
	  {
	    $data = $users_entries;
	  }
	
	?>
        <div id="pop_up_cal" style="position:absolute;margin-left:150px;visibility:hidden;background-color:white;layer-background-color:white;z-index:1;"></div>
	<form name="quoteform" id="quoteform" class="wrap" method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=calendar">
		<input type="hidden" name="action" value="<?php echo $mode; ?>">
		<input type="hidden" name="event_id" value="<?php echo stripslashes($event_id); ?>">
		<?php 
			if (stripslashes($event_id) != "") {
				$nonce_string = 'calendar-'.$mode.'_'.stripslashes($event_id);
			} else {
				$nonce_string = 'calendar-'.$mode;
			}
			wp_nonce_field($nonce_string);
		?>
	
		<div id="linkadvanceddiv" class="postbox">
			<div style="float: left; width: 98%; clear: both;" class="inside">
                                <table cellpadding="5" cellspacing="5">
                                <tr>				
				<td><legend><?php _e('Event Title','calendar'); ?></legend></td>
				<td><input type="text" name="event_title" class="input" size="40" maxlength="30"
					value="<?php if ( !empty($data) ) echo htmlspecialchars(stripslashes($data->event_title)); ?>" /></td>
                                </tr>
                                <tr>
				<td style="vertical-align:top;"><legend><?php _e('Event Description','calendar'); ?></legend></td>
				<td><textarea name="event_desc" class="input" rows="5" cols="50"><?php if ( !empty($data) ) echo htmlspecialchars(stripslashes($data->event_desc)); ?></textarea></td>
                                </tr>
                                <tr>
				<td><legend><?php _e('Event Category','calendar'); ?></legend></td>
				<td>	 <select name="event_category">
					     <?php
					         // Grab all the categories and list them
						 $sql = "SELECT * FROM " . WP_CALENDAR_CATEGORIES_TABLE;
	                                         $cats = $wpdb->get_results($sql);
                                                 foreach($cats as $cat)
						   {
						     echo '<option value="'.stripslashes($cat->category_id).'"';
                                                     if (!empty($data))
						       {
							 if ($data->event_category == $cat->category_id)
							   {
							     echo 'selected="selected"';
							   }
						       }
                                                     echo '>'.stripslashes($cat->category_name).'</option>
';
						   }
                                             ?>
                                         </select>
                                </td>
                                </tr>
                                <tr>
				<td><legend><?php _e('Event Link (Optional)','calendar'); ?></legend></td>
                                <td><input type="text" name="event_link" class="input" size="40" value="<?php if ( !empty($data) ) echo htmlspecialchars(stripslashes($data->event_link)); ?>" /></td>
                                </tr>
                                <tr>
				<td><legend><?php _e('Start Date','calendar'); ?></legend></td>
                                <td>    
					<input type="text" name="event_begin" id="event_begin" class="input" size="12"
					value="<?php 
					if ( !empty($data) ) 
					{
						echo htmlspecialchars(stripslashes($data->event_begin));
					}
					else
					{
						echo date("Y-m-d",ctwo());
					} 
					?>" />
					<script type="text/javascript">
						var cal_1 = new Calendar({
							element: 'event_begin',
							startDay: <?php echo get_option('start_of_week'); ?>,
							onSelect: function unifydates(element) {
							  document.forms['quoteform'].event_end.value = document.forms['quoteform'].event_begin.value;
							}
						});
					</script>
				</td>
                                </tr>
                                <tr>
				<td><legend><?php _e('End Date','calendar'); ?></legend></td>
                                <td>    
					<input type="text" name="event_end" id="event_end" class="input" size="12" 
					value="<?php 
					if ( !empty($data) ) 
					{
						echo htmlspecialchars(stripslashes($data->event_end));
					}
					else
					{
						echo date("Y-m-d",ctwo());
					}
					?>" />
					<script type="text/javascript">
						var cal_2 = new Calendar({
							element: 'event_end',
							startDay: <?php echo get_option('start_of_week'); ?>,
							minDate: new Date(parseInt(document.forms['quoteform'].event_begin.value.split('-')[0]),parseInt(document.forms['quoteform'].event_begin.value.split('-')[1]-1),parseInt(document.forms['quoteform'].event_begin.value.split('-')[2]))
						});
					</script>
				</td>
                                </tr>
                                <tr>
				<td><legend><?php _e('Time (hh:mm)','calendar'); ?></legend></td>
				<td>	<input type="text" name="event_time" class="input" size=12
					value="<?php 
					if ( !empty($data) ) 
					{
						if ($data->event_time == "00:00:00")
						{
							echo '';
						}
						else
						{
							echo date("H:i",strtotime(htmlspecialchars(stripslashes($data->event_time))));
						}
					}
					else
					{
						echo date("H:i",ctwo());
					}
					?>" /> <?php _e('Optional, set blank if not required.','calendar'); ?> <?php _e('Current time difference from GMT is ','calendar'); echo get_option('gmt_offset'); _e(' hour(s)','calendar'); ?>
				</td>
                                </tr>
                                <tr>
				<td><legend><?php _e('Recurring Events','calendar'); ?></legend></td>
				<td>	<?php
					if (isset($data)) {
					  if ($data->event_repeats != NULL)
					    {
						$repeats = $data->event_repeats;
					    }
					  else
					    {
					        $repeats = 0;
					    }
					}
					else
					{
						$repeats = 0;
					}

					$selected_s = '';
					$selected_w = '';
					$selected_m = '';
					$selected_y = '';
					$selected_u = '';
					if (isset($data)) {
					if ($data->event_recur == "S")
					{
						$selected_s = 'selected="selected"';
					}
					else if ($data->event_recur == "W")
					{
						$selected_w = 'selected="selected"';
					}
					else if ($data->event_recur == "M")
					{
						$selected_m = 'selected="selected"';
					}
					else if ($data->event_recur == "Y")
					{
						$selected_y = 'selected="selected"';
					}
					else if ($data->event_recur == "U")
					  {
					    $selected_u = 'selected="selected"';
					  }
                                        }
					?>
					  <?php _e('Repeats for','calendar'); ?> 
					<input type="text" name="event_repeats" class="input" size="1" value="<?php echo $repeats; ?>" /> 
					<select name="event_recur" class="input">
						<option class="input" <?php echo $selected_s; ?> value="S"><?php _e('None') ?></option>
						<option class="input" <?php echo $selected_w; ?> value="W"><?php _e('Weeks') ?></option>
						<option class="input" <?php echo $selected_m; ?> value="M"><?php _e('Months (date)') ?></option>
						<option class="input" <?php echo $selected_u; ?> value="U"><?php _e('Months (day)') ?></option>
						<option class="input" <?php echo $selected_y; ?> value="Y"><?php _e('Years') ?></option>
					</select><br />
					<?php _e('Entering 0 means forever. Where the recurrance interval is left at none, the event will not reoccur.','calendar'); ?>
				</td>
                                </tr>
                                </table>
			</div>
			<div style="clear:both; height:1px;">&nbsp;</div>
		</div>
                <input type="submit" name="save" class="button bold" value="<?php _e('Save','calendar'); ?> &raquo;" />
	</form>
	<?php
}

// The actual function called to render the manage events page and 
// to deal with posts
function edit_calendar()
{
    global $current_user, $wpdb, $users_entries;
  ?>
  <style type="text/css">
<!--
	.error {
	  background: lightcoral;
	  border: 1px solid #e64f69;
	  margin: 1em 5% 10px;
	  padding: 0 1em 0 1em;
	}

	.center { 
	  text-align: center;	
	}
	.right { text-align: right;	
	}
        .left { 
	  text-align: left;		
	}
	.top { 
	  vertical-align: top;	
	}
	.bold { 
	  font-weight: bold; 
	}
	.private { 
	  color: #e64f69;		
	}
//-->
</style>

<?php

// First some quick cleaning up 
$edit = $create = $save = $delete = false;

// Make sure we are collecting the variables we need to select years and months
$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';
$event_id = !empty($_REQUEST['event_id']) ? $_REQUEST['event_id'] : '';

// Deal with adding an event to the database
if ( $action == 'add' )
{
	$title = !empty($_REQUEST['event_title']) ? $_REQUEST['event_title'] : '';
	$desc = !empty($_REQUEST['event_desc']) ? $_REQUEST['event_desc'] : '';
	$begin = !empty($_REQUEST['event_begin']) ? $_REQUEST['event_begin'] : '';
	$end = !empty($_REQUEST['event_end']) ? $_REQUEST['event_end'] : '';
	$time = !empty($_REQUEST['event_time']) ? $_REQUEST['event_time'] : '';
	$recur = !empty($_REQUEST['event_recur']) ? $_REQUEST['event_recur'] : '';
	$repeats = !empty($_REQUEST['event_repeats']) ? $_REQUEST['event_repeats'] : '';
	$category = !empty($_REQUEST['event_category']) ? $_REQUEST['event_category'] : '';
        $linky = !empty($_REQUEST['event_link']) ? $_REQUEST['event_link'] : '';

	if (wp_verify_nonce($_POST['_wpnonce'],'calendar-add') == false) {
		?>
		<div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e("Security check failure, try adding the event again",'calendar'); ?></p></div>
		<?php
	} else {

	// Perform some validation on the submitted dates - this checks for valid years and months
	$date_format_one = '/^([0-9]{4})-([0][1-9])-([0-3][0-9])$/';
        $date_format_two = '/^([0-9]{4})-([1][0-2])-([0-3][0-9])$/';
	if ((preg_match($date_format_one,$begin) || preg_match($date_format_two,$begin)) && (preg_match($date_format_one,$end) || preg_match($date_format_two,$end)))
	  {
            // We know we have a valid year and month and valid integers for days so now we do a final check on the date
            $begin_split = explode('-',$begin);
	    $begin_y = $begin_split[0]; 
	    $begin_m = $begin_split[1];
	    $begin_d = $begin_split[2];
            $end_split = explode('-',$end);
	    $end_y = $end_split[0];
	    $end_m = $end_split[1];
	    $end_d = $end_split[2];
            if (checkdate($begin_m,$begin_d,$begin_y) && checkdate($end_m,$end_d,$end_y))
	     {
	       // Ok, now we know we have valid dates, we want to make sure that they are either equal or that the end date is later than the start date
	       if (strtotime($end) >= strtotime($begin))
		 {
		   $start_date_ok = 1;
		   $end_date_ok = 1;
		 }
	       else
		 {
		   ?>
		   <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('Your event end date must be either after or the same as your event begin date','calendar'); ?></p></div>
		   <?php
		 }
	     } 
	    else
	      {
		?>
                <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('Your date formatting is correct but one or more of your dates is invalid. Check for number of days in month and leap year related errors.','calendar'); ?></p></div>
                <?php
	      }
	  }
	else
	  {
	    ?>
            <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('Both start and end dates must be entered and be in the format YYYY-MM-DD','calendar'); ?></p></div>
            <?php
	  }
        // We check for a valid time, or an empty one
        $time_format_one = '/^([0-1][0-9]):([0-5][0-9])$/';
	$time_format_two = '/^([2][0-3]):([0-5][0-9])$/';
        if (preg_match($time_format_one,$time) || preg_match($time_format_two,$time) || $time == '')
          {
            $time_ok = 1;
	    if ($time == '')
	      {
		$time_to_use = '00:00:00';
	      }
	    else if ($time == '00:00')
	      {
		$time_to_use = '00:00:01';
	      }
	    else
	      {
		$time_to_use = $time;
	      }
          }
        else
          {
            ?>
            <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('The time field must either be blank or be entered in the format hh:mm','calendar'); ?></p></div>
            <?php
	  }
	// We check to make sure the URL is alright                                                        
	if (preg_match('/^(http)(s?)(:)\/\//',$linky) || $linky == '')
	  {
	    $url_ok = 1;
	  }
	else
	  {
              ?>
              <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('The URL entered must either be prefixed with http:// or be completely blank','calendar'); ?></p></div>
              <?php
	  }
	// The title must be at least one character in length and no more than 30
	if (preg_match('/^.{1,30}$/',$title))
	  {
	    $title_ok =1;
	  }
	else
	  {
              ?>
              <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('The event title must be between 1 and 30 characters in length','calendar'); ?></p></div>
              <?php
	  }
	// We run some checks on recurrance
	$repeats = (int)$repeats;
	if (($repeats == 0 && $recur == 'S') || (($repeats >= 0) && ($recur == 'W' || $recur == 'M' || $recur == 'Y' || $recur == 'U')))
	  {
	    $recurring_ok = 1;
	  }
	else
	  {
              ?>
              <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('The repetition value must be 0 unless a type of recurrance is selected in which case the repetition value must be 0 or higher','calendar'); ?></p></div>
              <?php
	  }
	if (isset($start_date_ok) && isset($end_date_ok) && isset($time_ok) && isset($url_ok) && isset($title_ok) && isset($recurring_ok))
	  {
	    $sql = "INSERT INTO " . WP_CALENDAR_TABLE . " SET event_title='" . mysql_escape_string($title)
	     . "', event_desc='" . mysql_escape_string($desc) . "', event_begin='" . mysql_escape_string($begin) 
             . "', event_end='" . mysql_escape_string($end) . "', event_time='" . mysql_escape_string($time_to_use) . "', event_recur='" . mysql_escape_string($recur) . "', event_repeats='" . mysql_escape_string($repeats) . "', event_author=".$current_user->ID.", event_category=".mysql_escape_string($category).", event_link='".mysql_escape_string($linky)."'";
	     
	    $wpdb->get_results($sql);
	
	    $sql = "SELECT event_id FROM " . WP_CALENDAR_TABLE . " WHERE event_title='" . mysql_escape_string($title) . "'"
		. " AND event_desc='" . mysql_escape_string($desc) . "' AND event_begin='" . mysql_escape_string($begin) . "' AND event_end='" . mysql_escape_string($end) . "' AND event_recur='" . mysql_escape_string($recur) . "' AND event_repeats='" . mysql_escape_string($repeats) . "' LIMIT 1";
	    $result = $wpdb->get_results($sql);
	
	    if ( empty($result) || empty($result[0]->event_id) )
	      {
                ?>
		<div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('An event with the details you submitted could not be found in the database. This may indicate a problem with your database or the way in which it is configured.','calendar'); ?></p></div>
		<?php
	      }
	    else
	      {
		?>
		<div class="updated"><p><?php _e('Event added. It will now show in your calendar.','calendar'); ?></p></div>
		<?php
	      }
	  }
	else
	  {
	    // The form is going to be rejected due to field validation issues, so we preserve the users entries here
	    $users_entries->event_title = $title;
	    $users_entries->event_desc = $desc;
	    $users_entries->event_begin = $begin;
	    $users_entries->event_end = $end;
	    $users_entries->event_time = $time;
	    $users_entries->event_recur = $recur;
	    $users_entries->event_repeats = $repeats;
	    $users_entries->event_category = $category;
	    $users_entries->event_link = $linky;
	  }
	}
}
// Permit saving of events that have been edited
elseif ( $action == 'edit_save' )
{
	$title = !empty($_REQUEST['event_title']) ? $_REQUEST['event_title'] : '';
	$desc = !empty($_REQUEST['event_desc']) ? $_REQUEST['event_desc'] : '';
	$begin = !empty($_REQUEST['event_begin']) ? $_REQUEST['event_begin'] : '';
	$end = !empty($_REQUEST['event_end']) ? $_REQUEST['event_end'] : '';
	$time = !empty($_REQUEST['event_time']) ? $_REQUEST['event_time'] : '';
	$recur = !empty($_REQUEST['event_recur']) ? $_REQUEST['event_recur'] : '';
	$repeats = !empty($_REQUEST['event_repeats']) ? $_REQUEST['event_repeats'] : '';
	$category = !empty($_REQUEST['event_category']) ? $_REQUEST['event_category'] : '';
        $linky = !empty($_REQUEST['event_link']) ? $_REQUEST['event_link'] : '';
	
	if ( empty($event_id) )
	{
		?>
		<div class="error"><p><strong><?php _e('Failure','calendar'); ?>:</strong> <?php _e("You can't update an event if you haven't submitted an event id",'calendar'); ?></p></div>
		<?php		
	}
	elseif (wp_verify_nonce($_POST['_wpnonce'],'calendar-edit_save_'.$event_id) == false) {
		?>
		<div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e("Security check failure, try editing the event again",'calendar'); ?></p></div>
		<?php
	}
	else
	{
	  // Perform some validation on the submitted dates - this checks for valid years and months
          $date_format_one = '/^([0-9]{4})-([0][1-9])-([0-3][0-9])$/';
	  $date_format_two = '/^([0-9]{4})-([1][0-2])-([0-3][0-9])$/';
	  if ((preg_match($date_format_one,$begin) || preg_match($date_format_two,$begin)) && (preg_match($date_format_one,$end) || preg_match($date_format_two,$end)))
	    {
	      // We know we have a valid year and month and valid integers for days so now we do a final check on the date
              $begin_split = explode('-',$begin);
	      $begin_y = $begin_split[0];
	      $begin_m = $begin_split[1];
	      $begin_d = $begin_split[2];
	      $end_split = explode('-',$end);
	      $end_y = $end_split[0];
	      $end_m = $end_split[1];
	      $end_d = $end_split[2];
	      if (checkdate($begin_m,$begin_d,$begin_y) && checkdate($end_m,$end_d,$end_y))
		{
		  // Ok, now we know we have valid dates, we want to make sure that they are either equal or that the end date is later than the start date
                  if (strtotime($end) >= strtotime($begin))
		    {
		      $start_date_ok = 1;
		      $end_date_ok = 1;
		    }
		  else
		    {
                      ?>
                      <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('Your event end date must be either after or the same as your event begin date','calendar'); ?></p></div>
                      <?php
                    }
		}
	      else
		{
                ?>
                <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('Your date formatting is correct but one or more of your dates is invalid. Check for number of days in month and leap year related errors.','calendar'); ?></p></div>
                <?php
                }
	    }
	  else
	    {
            ?>
            <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('Both start and end dates must be entered and be in the format YYYY-MM-DD','calendar'); ?></p></div>
            <?php
	    }
	  // We check for a valid time, or an empty one
	  $time_format_one = '/^([0-1][0-9]):([0-5][0-9])$/';
	  $time_format_two = '/^([2][0-3]):([0-5][0-9])$/';
	  if (preg_match($time_format_one,$time) || preg_match($time_format_two,$time) || $time == '')
	    {
	      $time_ok = 1;
	      if ($time == '')
		{
		  $time_to_use = '00:00:00';
		}
	      else if ($time == '00:00')
		{
		  $time_to_use = '00:00:01';
		}
	      else
		{
		  $time_to_use = $time;
		}
	    }
	  else
	    {
            ?>
            <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('The time field must either be blank or be entered in the format hh:mm','calendar'); ?></p></div>
            <?php
	    }
          // We check to make sure the URL is alright
	  if (preg_match('/^(http)(s?)(:)\/\//',$linky) || $linky == '')
	    {
	      $url_ok = 1;
	    }
	  else
	    {
	      ?>
	      <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('The URL entered must either be prefixed with http:// or be completely blank','calendar'); ?></p></div>
	      <?php
	    }
	  // The title must be at least one character in length and no more than 30
	  if (preg_match('/^.{1,30}$/',$title))
            {
	      $title_ok =1;
	    }
          else
            {
	      ?>
              <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('The event title must be between 1 and 30 characters in length','calendar'); ?></p></div>
              <?php
	    }
	  // We run some checks on recurrance
	  $repeats = (int)$repeats;
          if (($repeats == 0 && $recur == 'S') || (($repeats >= 0) && ($recur == 'W' || $recur == 'M' || $recur == 'Y' || $recur == 'U')))
            {
              $recurring_ok = 1;
            }
          else
            {
              ?>
              <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('The repetition value must be 0 unless a type of recurrance is selected in which case the repetition value must be 0 or higher','calendar'); ?></p></div>
              <?php
	    }
	  if (isset($start_date_ok) && isset($end_date_ok) && isset($time_ok) && isset($url_ok) && isset($title_ok) && isset($recurring_ok))
	    {
		$sql = "UPDATE " . WP_CALENDAR_TABLE . " SET event_title='" . mysql_escape_string($title)
		     . "', event_desc='" . mysql_escape_string($desc) . "', event_begin='" . mysql_escape_string($begin) 
                     . "', event_end='" . mysql_escape_string($end) . "', event_time='" . mysql_escape_string($time_to_use) . "', event_recur='" . mysql_escape_string($recur) . "', event_repeats='" . mysql_escape_string($repeats) . "', event_author=".$current_user->ID . ", event_category=".mysql_escape_string($category).", event_link='".mysql_escape_string($linky)."' WHERE event_id='" . mysql_escape_string($event_id) . "'";
		     
		$wpdb->get_results($sql);
		
		$sql = "SELECT event_id FROM " . WP_CALENDAR_TABLE . " WHERE event_title='" . mysql_escape_string($title) . "'"
		     . " AND event_desc='" . mysql_escape_string($desc) . "' AND event_begin='" . mysql_escape_string($begin) . "' AND event_end='" . mysql_escape_string($end) . "' AND event_recur='" . mysql_escape_string($recur) . "' AND event_repeats='" . mysql_escape_string($repeats) . "' LIMIT 1";
		$result = $wpdb->get_results($sql);
		
		if ( empty($result) || empty($result[0]->event_id) )
		{
			?>
			<div class="error"><p><strong><?php _e('Failure','calendar'); ?>:</strong> <?php _e('The database failed to return data to indicate the event has been updated sucessfully. This may indicate a problem with your database or the way in which it is configured.','calendar'); ?></p></div>
			<?php
		}
		else
		{
			?>
			<div class="updated"><p><?php _e('Event updated successfully','calendar'); ?></p></div>
			<?php
		}
	    }
          else
	    {
	      // The form is going to be rejected due to field validation issues, so we preserve the users entries here
              $users_entries->event_title = $title;
	      $users_entries->event_desc = $desc;
	      $users_entries->event_begin = $begin;
	      $users_entries->event_end = $end;
	      $users_entries->event_time = $time;
	      $users_entries->event_recur = $recur;
	      $users_entries->event_repeats = $repeats;
	      $users_entries->event_category = $category;
	      $users_entries->event_link = $linky;
	      $error_with_saving = 1;
	    }		
	}
}
// Deal with deleting an event from the database
elseif ( $action == 'delete' )
{
	if ( empty($event_id) )
	{
		?>
		<div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e("You can't delete an event if you haven't submitted an event id",'calendar'); ?></p></div>
		<?php			
	}
	elseif (wp_verify_nonce($_GET['_wpnonce'],'calendar-delete_'.$event_id) == false) {
		?>
		<div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e("Security check failure, try deleting the event again",'calendar'); ?></p></div>
		<?php
	}
	else
	{
		$sql = "DELETE FROM " . WP_CALENDAR_TABLE . " WHERE event_id='" . mysql_escape_string($event_id) . "'";
		$wpdb->get_results($sql);
		
		$sql = "SELECT event_id FROM " . WP_CALENDAR_TABLE . " WHERE event_id='" . mysql_escape_string($event_id) . "'";
		$result = $wpdb->get_results($sql);
		
		if ( empty($result) || empty($result[0]->event_id) )
		{
			?>
			<div class="updated"><p><?php _e('Event deleted successfully','calendar'); ?></p></div>
			<?php
		}
		else
		{
			?>
			<div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e('Despite issuing a request to delete, the event still remains in the database. Please investigate.','calendar'); ?></p></div>
			<?php

		}		
	}
}

// Now follows a little bit of code that pulls in the main 
// components of this page; the edit form and the list of events
?>

<div class="wrap">
	<?php
	if ( $action == 'edit' || ($action == 'edit_save' && isset($error_with_saving)))
	{
		?>
		<h2><?php _e('Edit Event','calendar'); ?></h2>
		<?php
		if ( empty($event_id) )
		{
			echo "<div class=\"error\"><p>".__("You must provide an event id in order to edit it",'calendar')."</p></div>";
		}
		else
		{
			wp_events_edit_form('edit_save', $event_id);
		}	
	}
	else
	{
		?>
		<h2><?php _e('Add Event','calendar'); ?></h2>
		<?php wp_events_edit_form(); ?>
	
		<h2><?php _e('Manage Events','calendar'); ?></h2>
		<?php
			wp_events_display_list();
	}
	?>
</div>

<?php
 
}

// Display the admin configuration page
function edit_calendar_config()
{
  global $wpdb, $initial_style;

  if (isset($_POST['permissions']) && isset($_POST['style']) && wp_verify_nonce($_POST['_wpnonce'],'calendar-config') == false) {
		?>
		<div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e("Security check failure, try editing the config again",'calendar'); ?></p></div>
		<?php
  }
  elseif (isset($_POST['permissions']) && isset($_POST['style']))
    {
      if ($_POST['permissions'] == 'subscriber') { $new_perms = 'read'; }
      else if ($_POST['permissions'] == 'contributor') { $new_perms = 'edit_posts'; }
      else if ($_POST['permissions'] == 'author') { $new_perms = 'publish_posts'; }
      else if ($_POST['permissions'] == 'editor') { $new_perms = 'moderate_comments'; }
      else if ($_POST['permissions'] == 'admin') { $new_perms = 'manage_options'; }
      else { $new_perms = 'manage_options'; }

      $calendar_style = mysql_escape_string($_POST['style']);
      $display_upcoming_days = mysql_escape_string($_POST['display_upcoming_days']);

      if (mysql_escape_string($_POST['display_author']) == 'on')
	{
	  $disp_author = 'true';
	}
      else
	{
	  $disp_author = 'false';
	}

      if (mysql_escape_string($_POST['display_jump']) == 'on')
        {
          $disp_jump = 'true';
        }
      else
        {
          $disp_jump = 'false';
        }

      if (mysql_escape_string($_POST['display_todays']) == 'on')
        {
          $disp_todays = 'true';
        }
      else
        {
          $disp_todays = 'false';
        }

      if (mysql_escape_string($_POST['display_upcoming']) == 'on')
        {
          $disp_upcoming = 'true';
        }
      else
        {
          $disp_upcoming = 'false';
        }

      if (mysql_escape_string($_POST['enable_categories']) == 'on')
        {
          $enable_categories = 'true';
        }
      else
        {
	  $enable_categories = 'false';
        }

      $wpdb->get_results("UPDATE " . WP_CALENDAR_CONFIG_TABLE . " SET config_value = '".$new_perms."' WHERE config_item='can_manage_events'");
      $wpdb->get_results("UPDATE " . WP_CALENDAR_CONFIG_TABLE . " SET config_value = '".$calendar_style."' WHERE config_item='calendar_style'");
      $wpdb->get_results("UPDATE " . WP_CALENDAR_CONFIG_TABLE . " SET config_value = '".$disp_author."' WHERE config_item='display_author'");
      $wpdb->get_results("UPDATE " . WP_CALENDAR_CONFIG_TABLE . " SET config_value = '".$disp_jump."' WHERE config_item='display_jump'");
      $wpdb->get_results("UPDATE " . WP_CALENDAR_CONFIG_TABLE . " SET config_value = '".$disp_todays."' WHERE config_item='display_todays'");
      $wpdb->get_results("UPDATE " . WP_CALENDAR_CONFIG_TABLE . " SET config_value = '".$disp_upcoming."' WHERE config_item='display_upcoming'");
      $wpdb->get_results("UPDATE " . WP_CALENDAR_CONFIG_TABLE . " SET config_value = '".$display_upcoming_days."' WHERE config_item='display_upcoming_days'");
      $wpdb->get_results("UPDATE " . WP_CALENDAR_CONFIG_TABLE . " SET config_value = '".$enable_categories."' WHERE config_item='enable_categories'");

      // Check to see if we are replacing the original style
      if (isset($_POST['reset_styles'])) {
        if (mysql_escape_string($_POST['reset_styles']) == 'on')
          {
            $wpdb->get_results("UPDATE " . WP_CALENDAR_CONFIG_TABLE . " SET config_value = '".$initial_style."' WHERE config_item='calendar_style'");
          }
      }

      echo "<div class=\"updated\"><p><strong>".__('Settings saved','calendar').".</strong></p></div>";
    }

  // Pull the values out of the database that we need for the form
  $configs = $wpdb->get_results("SELECT config_value FROM " . WP_CALENDAR_CONFIG_TABLE . " WHERE config_item='can_manage_events'");
  if (!empty($configs))
    {
      foreach ($configs as $config)
        {
          $allowed_group = stripslashes($config->config_value);
        }
    }

  $configs = $wpdb->get_results("SELECT config_value FROM " . WP_CALENDAR_CONFIG_TABLE . " WHERE config_item='calendar_style'");
  if (!empty($configs))
    {
      foreach ($configs as $config)
        {
          $calendar_style = stripslashes($config->config_value);
        }
    }
  $configs = $wpdb->get_results("SELECT config_value FROM " . WP_CALENDAR_CONFIG_TABLE . " WHERE config_item='display_author'");
  $yes_disp_author = '';
  $no_disp_author = '';
  if (!empty($configs))
    {
      foreach ($configs as $config)
        {
	  if ($config->config_value == 'true')
	    {
	      $yes_disp_author = 'selected="selected"';
	    }
	  else
	    {
	      $no_disp_author = 'selected="selected"';
	    }
        }
    }
  $configs = $wpdb->get_results("SELECT config_value FROM " . WP_CALENDAR_CONFIG_TABLE . " WHERE config_item='display_jump'");
  $yes_disp_jump = '';
  $no_disp_jump = '';
  if (!empty($configs))
    {
      foreach ($configs as $config)
        {
          if ($config->config_value == 'true')
            {
              $yes_disp_jump = 'selected="selected"';
            }
          else
            {
              $no_disp_jump = 'selected="selected"';
            }
        }
    }
  $configs = $wpdb->get_results("SELECT config_value FROM " . WP_CALENDAR_CONFIG_TABLE . " WHERE config_item='display_todays'");
  $yes_disp_todays = '';
  $no_disp_todays = '';
  if (!empty($configs))
    {
      foreach ($configs as $config)
        {
          if ($config->config_value == 'true')
            {
              $yes_disp_todays = 'selected="selected"';
            }
          else
            {
              $no_disp_todays = 'selected="selected"';
            }
        }
    }
  $configs = $wpdb->get_results("SELECT config_value FROM " . WP_CALENDAR_CONFIG_TABLE . " WHERE config_item='display_upcoming'");
  $yes_disp_upcoming = '';
  $no_disp_upcoming = '';
  if (!empty($configs))
    {
      foreach ($configs as $config)
        {
          if ($config->config_value == 'true')
            {
              $yes_disp_upcoming = 'selected="selected"';
            }
          else
            {
              $no_disp_upcoming = 'selected="selected"';
            }
        }
    }
  $configs = $wpdb->get_results("SELECT config_value FROM " . WP_CALENDAR_CONFIG_TABLE . " WHERE config_item='display_upcoming_days'");
  if (!empty($configs))
    {
      foreach ($configs as $config)
        {
          $upcoming_days = stripslashes($config->config_value);
        }
    }
  $configs = $wpdb->get_results("SELECT config_value FROM " . WP_CALENDAR_CONFIG_TABLE . " WHERE config_item='enable_categories'");
  $yes_enable_categories = '';
  $no_enable_categories = '';
  if (!empty($configs))
    {
      foreach ($configs as $config)
        {
          if ($config->config_value == 'true')
            {
              $yes_enable_categories = 'selected="selected"';
            }
          else
            {
              $no_enable_categories = 'selected="selected"';
            }
        }
    }
  $subscriber_selected = '';
  $contributor_selected = '';
  $author_selected = '';
  $editor_selected = '';
  $admin_selected = '';
  if ($allowed_group == 'read') { $subscriber_selected='selected="selected"';}
  else if ($allowed_group == 'edit_posts') { $contributor_selected='selected="selected"';}
  else if ($allowed_group == 'publish_posts') { $author_selected='selected="selected"';}
  else if ($allowed_group == 'moderate_comments') { $editor_selected='selected="selected"';}
  else if ($allowed_group == 'manage_options') { $admin_selected='selected="selected"';}

  // Now we render the form
  ?>
  <style type="text/css">
  <!--
        .error {
	  background: lightcoral;
	  border: 1px solid #e64f69;
	  margin: 1em 5% 10px;
	  padding: 0 1em 0 1em;
	}

        .center { 
	  text-align: center; 
	}
        .right { 
	  text-align: right; 
	}
        .left { 
	  text-align: left; 
	}
        .top { 
	  vertical-align: top; 
	}
        .bold { 
	  font-weight: bold; 
	}
        .private { 
	  color: #e64f69; 
	}
  //-->                                                                                                                                                        
  </style>

  <div class="wrap">
  <h2><?php _e('Calendar Options','calendar'); ?></h2>
  <form name="quoteform" id="quoteform" class="wrap" method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=calendar-config">
		<?php wp_nonce_field('calendar-config'); ?>
                <div id="linkadvanceddiv" class="postbox">
                        <div style="float: left; width: 98%; clear: both;" class="inside">
                                <table cellpadding="5" cellspacing="5">
				<tr>
                                <td><legend><?php _e('Choose the lowest user group that may manage events','calendar'); ?></legend></td>
				<td>        <select name="permissions">
				            <option value="subscriber"<?php echo $subscriber_selected ?>><?php _e('Subscriber','calendar')?></option>
				            <option value="contributor" <?php echo $contributor_selected ?>><?php _e('Contributor','calendar')?></option>
				            <option value="author" <?php echo $author_selected ?>><?php _e('Author','calendar')?></option>
				            <option value="editor" <?php echo $editor_selected ?>><?php _e('Editor','calendar')?></option>
				            <option value="admin" <?php echo $admin_selected ?>><?php _e('Administrator','calendar')?></option>
				        </select>
                                </td>
                                </tr>
                                <tr>
				<td><legend><?php _e('Do you want to display the author name on events?','calendar'); ?></legend></td>
                                <td>    <select name="display_author">
                                        <option value="on" <?php echo $yes_disp_author ?>><?php _e('Yes','calendar') ?></option>
                                        <option value="off" <?php echo $no_disp_author ?>><?php _e('No','calendar') ?></option>
                                    </select>
                                </td>
                                </tr>
                                <tr>
				<td><legend><?php _e('Display a jumpbox for changing month and year quickly?','calendar'); ?></legend></td>
                                <td>    <select name="display_jump">
                                         <option value="on" <?php echo $yes_disp_jump ?>><?php _e('Yes','calendar') ?></option>
                                         <option value="off" <?php echo $no_disp_jump ?>><?php _e('No','calendar') ?></option>
                                    </select>
                                </td>
                                </tr>
                                <tr>
				<td><legend><?php _e('Display todays events?','calendar'); ?></legend></td>
                                <td>    <select name="display_todays">
						<option value="on" <?php echo $yes_disp_todays ?>><?php _e('Yes','calendar') ?></option>
						<option value="off" <?php echo $no_disp_todays ?>><?php _e('No','calendar') ?></option>
                                    </select>
                                </td>
                                </tr>
                                <tr>
				<td><legend><?php _e('Display upcoming events?','calendar'); ?></legend></td>
                                <td>    <select name="display_upcoming">
						<option value="on" <?php echo $yes_disp_upcoming ?>><?php _e('Yes','calendar') ?></option>
						<option value="off" <?php echo $no_disp_upcoming ?>><?php _e('No','calendar') ?></option>
                                    </select>
				    <?php _e('for','calendar'); ?> <input type="text" name="display_upcoming_days" value="<?php echo $upcoming_days ?>" size="1" maxlength="2" /> <?php _e('days into the future','calendar'); ?>
                                </td>
                                </tr>
                                <tr>
				<td><legend><?php _e('Enable event categories?','calendar'); ?></legend></td>
                                <td>    <select name="enable_categories">
				                <option value="on" <?php echo $yes_enable_categories ?>><?php _e('Yes','calendar') ?></option>
						<option value="off" <?php echo $no_enable_categories ?>><?php _e('No','calendar') ?></option>
                                    </select>
                                </td>
                                </tr>
                                <tr>
				<td style="vertical-align:top;"><legend><?php _e('Configure the stylesheet for Calendar','calendar'); ?></legend></td>
				<td><textarea name="style" rows="10" cols="60" tabindex="2"><?php echo $calendar_style; ?></textarea><br />
                                <input type="checkbox" name="reset_styles" /> <?php _e('Tick this box if you wish to reset the Calendar style to default','calendar'); ?></td>
                                </tr>
                                </table>
			</div>
                        <div style="clear:both; height:1px;">&nbsp;</div>
	        </div>
                <input type="submit" name="save" class="button bold" value="<?php _e('Save','calendar'); ?> &raquo;" />
  </form>
  </div>
  <?php


}

// Function to handle the management of categories
function manage_categories()
{
  global $wpdb;

?>
<style type="text/css">
  <!--
   .error {
     background: lightcoral;
     border: 1px solid #e64f69;
     margin: 1em 5% 10px;
     padding: 0 1em 0 1em;
   }

  .center {
    text-align: center;
  }
  .right {
    text-align: right;
  }
  .left {
    text-align: left;
  }
  .top {
    vertical-align: top;
  }
  .bold {
    font-weight: bold;
  }
  .private {
  color: #e64f69;
  }
  //-->
     
</style>
<?php
  // We do some checking to see what we're doing
  if (isset($_POST['mode']) && $_POST['mode'] == 'add')
    {
      if (wp_verify_nonce($_POST['_wpnonce'],'calendar-category_add') == false) {
        ?>
	  <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e("Security check failure, try adding the category again",'calendar'); ?></p></div>
	<?php
      } else {
      // Proceed with the save  
      $sql = "INSERT INTO " . WP_CALENDAR_CATEGORIES_TABLE . " SET category_name='".mysql_escape_string($_POST['category_name'])."', category_colour='".mysql_escape_string($_POST['category_colour'])."'";
      $wpdb->get_results($sql);
      echo "<div class=\"updated\"><p><strong>".__('Category added successfully','calendar')."</strong></p></div>";
      }
    }
  else if (isset($_GET['mode']) && isset($_GET['category_id']) && $_GET['mode'] == 'delete')
    {
      if (wp_verify_nonce($_GET['_wpnonce'],'calendar-category_delete_'.mysql_escape_string($_GET['category_id'])) == false) {
        ?>
	  <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e("Security check failure, try deleting the category again",'calendar'); ?></p></div>
	<?php
      } else {
        $sql = "DELETE FROM " . WP_CALENDAR_CATEGORIES_TABLE . " WHERE category_id=".mysql_escape_string($_GET['category_id']);
        $wpdb->get_results($sql);
        $sql = "UPDATE " . WP_CALENDAR_TABLE . " SET event_category=1 WHERE event_category=".mysql_escape_string($_GET['category_id']);
        $wpdb->get_results($sql);
        echo "<div class=\"updated\"><p><strong>".__('Category deleted successfully','calendar')."</strong></p></div>";
      }
    }
  else if (isset($_GET['mode']) && isset($_GET['category_id']) && $_GET['mode'] == 'edit' && !isset($_POST['mode']))
    {
      $sql = "SELECT * FROM " . WP_CALENDAR_CATEGORIES_TABLE . " WHERE category_id=".intval(mysql_escape_string($_GET['category_id']));
      $cur_cat = $wpdb->get_row($sql);
      ?>
<div class="wrap">
   <h2><?php _e('Edit Category','calendar'); ?></h2>
    <form name="catform" id="catform" class="wrap" method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=calendar-categories">
                <input type="hidden" name="mode" value="edit" />
                <input type="hidden" name="category_id" value="<?php echo stripslashes($cur_cat->category_id) ?>" />
		<?php wp_nonce_field('calendar-category_edit_'.stripslashes($cur_cat->category_id)); ?>
                <div id="linkadvanceddiv" class="postbox">
                        <div style="float: left; width: 98%; clear: both;" class="inside">
				<table cellpadding="5" cellspacing="5">
                                <tr>
				<td><legend><?php _e('Category Name','calendar'); ?>:</legend></td>
                                <td><input type="text" name="category_name" class="input" size="30" maxlength="30" value="<?php echo stripslashes($cur_cat->category_name) ?>" /></td>
				</tr>
                                <tr>
				<td><legend><?php _e('Category Colour (Hex format)','calendar'); ?>:</legend></td>
                                <td><input type="text" name="category_colour" class="input" size="10" maxlength="7" value="<?php echo stripslashes($cur_cat->category_colour) ?>" /></td>
                                </tr>
                                </table>
                        </div>
                        <div style="clear:both; height:1px;">&nbsp;</div>
                </div>
                <input type="submit" name="save" class="button bold" value="<?php _e('Save','calendar'); ?> &raquo;" />
    </form>
</div>
      <?php
    }
  else if (isset($_POST['mode']) && isset($_POST['category_id']) && isset($_POST['category_name']) && isset($_POST['category_colour']) && $_POST['mode'] == 'edit')
    {
      if (wp_verify_nonce($_POST['_wpnonce'],'calendar-category_edit_'.mysql_escape_string($_POST['category_id'])) == false) {
        ?>
	  <div class="error"><p><strong><?php _e('Error','calendar'); ?>:</strong> <?php _e("Security check failure, try editing the category again",'calendar'); ?></p></div>
	<?php
      } else {
      // Proceed with the save
        $sql = "UPDATE " . WP_CALENDAR_CATEGORIES_TABLE . " SET category_name='".mysql_escape_string($_POST['category_name'])."', category_colour='".mysql_escape_string($_POST['category_colour'])."' WHERE category_id=".mysql_escape_string($_POST['category_id']);
        $wpdb->get_results($sql);
        echo "<div class=\"updated\"><p><strong>".__('Category edited successfully','calendar')."</strong></p></div>";
      }
    }

  $get_mode = 0;
  $post_mode = 0;
  if (isset($_GET['mode'])) {
    if ($_GET['mode'] == 'edit') {
      $get_mode = 1;
    }
  }
  if (isset($_POST['mode'])) {
    if ($_POST['mode'] == 'edit') {
      $post_mode = 1;
    }
  }
  if ($get_mode != 1 || $post_mode == 1)
    {
?>

  <div class="wrap">
    <h2><?php _e('Add Category','calendar'); ?></h2>
    <form name="catform" id="catform" class="wrap" method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=calendar-categories">
                <input type="hidden" name="mode" value="add" />
                <input type="hidden" name="category_id" value="">
		<?php wp_nonce_field('calendar-category_add'); ?>
                <div id="linkadvanceddiv" class="postbox">
                        <div style="float: left; width: 98%; clear: both;" class="inside">
       				<table cellspacing="5" cellpadding="5">
                                <tr>
                                <td><legend><?php _e('Category Name','calendar'); ?>:</legend></td>
                                <td><input type="text" name="category_name" class="input" size="30" maxlength="30" value="" /></td>
                                </tr>
                                <tr>
                                <td><legend><?php _e('Category Colour (Hex format)','calendar'); ?>:</legend></td>
                                <td><input type="text" name="category_colour" class="input" size="10" maxlength="7" value="" /></td>
                                </tr>
                                </table>
                        </div>
		        <div style="clear:both; height:1px;">&nbsp;</div>
                </div>
                <input type="submit" name="save" class="button bold" value="<?php _e('Save','calendar'); ?> &raquo;" />
    </form>
    <h2><?php _e('Manage Categories','calendar'); ?></h2>
<?php
    
    // We pull the categories from the database	
    $categories = $wpdb->get_results("SELECT * FROM " . WP_CALENDAR_CATEGORIES_TABLE . " ORDER BY category_id ASC");

 if ( !empty($categories) )
   {
     ?>
     <table class="widefat page fixed" width="50%" cellpadding="3" cellspacing="3">
       <thead> 
       <tr>
         <th class="manage-column" scope="col"><?php _e('ID','calendar') ?></th>
	 <th class="manage-column" scope="col"><?php _e('Category Name','calendar') ?></th>
	 <th class="manage-column" scope="col"><?php _e('Category Colour','calendar') ?></th>
	 <th class="manage-column" scope="col"><?php _e('Edit','calendar') ?></th>
	 <th class="manage-column" scope="col"><?php _e('Delete','calendar') ?></th>
       </tr>
       </thead>
       <?php
       $class = '';
       foreach ( $categories as $category )
         {
	   $class = ($class == 'alternate') ? '' : 'alternate';
           ?>
           <tr class="<?php echo $class; ?>">
	     <th scope="row"><?php echo stripslashes($category->category_id); ?></th>
	     <td><?php echo stripslashes($category->category_name); ?></td>
	     <td style="background-color:<?php echo stripslashes($category->category_colour); ?>;">&nbsp;</td>
	     <td><a href="<?php echo bloginfo('wpurl')  ?>/wp-admin/admin.php?page=calendar-categories&amp;mode=edit&amp;category_id=<?php echo stripslashes($category->category_id);?>" class='edit'><?php echo __('Edit','calendar'); ?></a></td>
	     <?php
	     if ($category->category_id == 1)
	       {
		 echo '<td>'.__('N/A','calendar').'</td>';
	       }
             else
	       {
               ?>
               <td><a href="<?php echo wp_nonce_url(bloginfo('wpurl').'/wp-admin/admin.php?page=calendar-categories&amp;mode=delete&amp;category_id='.stripslashes($category->category_id), 'calendar-category_delete_'.stripslashes($category->category_id)); ?>" class="delete" onclick="return confirm('<?php echo __('Are you sure you want to delete this category?','calendar'); ?>')"><?php echo __('Delete','calendar'); ?></a></td>
               <?php
	       }
                ?>
              </tr>
                <?php
          }
      ?>
      </table>
      <?php
   }
 else
   {
     echo '<p>'.__('There are no categories in the database - something has gone wrong!','calendar').'</p>';
   }

 ?>
  </div>

 <?php
      } 
}

// Function to indicate the number of the day passed, eg. 1st or 2nd Sunday
function np_of_day($date)
{
  $instance = 0;
  $dom = date('j',strtotime($date));
  if (($dom-7) <= 0) { $instance = 1; }
  else if (($dom-7) > 0 && ($dom-7) <= 7) { $instance = 2; }
  else if (($dom-7) > 7 && ($dom-7) <= 14) { $instance = 3; }
  else if (($dom-7) > 14 && ($dom-7) <= 21) { $instance = 4; }
  else if (($dom-7) > 21 && ($dom-7) < 28) { $instance = 5; }
  return $instance;
}

// Function to provide date of the nth day passed (eg. 2nd Sunday)
function dt_of_sun($date,$instance,$day)
{
  $plan = array();
  $plan['Mon'] = 1;
  $plan['Tue'] = 2;
  $plan['Wed'] = 3;
  $plan['Thu'] = 4;
  $plan['Fri'] = 5;
  $plan['Sat'] = 6;
  $plan['Sun'] = 7;
  $proper_date = date('Y-m-d',strtotime($date));
  $begin_month = substr($proper_date,0,8).'01'; 
  $offset = $plan[date('D',strtotime($begin_month))]; 
  $result_day = 0;
  $recon = 0;
  if (($day-($offset)) < 0) { $recon = 7; }
  if ($instance == 1) { $result_day = $day-($offset-1)+$recon; }
  else if ($instance == 2) { $result_day = $day-($offset-1)+$recon+7; }
  else if ($instance == 3) { $result_day = $day-($offset-1)+$recon+14; }
  else if ($instance == 4) { $result_day = $day-($offset-1)+$recon+21; }
  else if ($instance == 5) { $result_day = $day-($offset-1)+$recon+28; }
  return substr($proper_date,0,8).$result_day;
}

// Function to return a prefix which will allow the correct 
// placement of arguments into the query string.
function permalink_prefix()
{
  // Get the permalink structure from WordPress
  if (is_home()) { 
    $p_link = get_bloginfo('url'); 
    if ($p_link[strlen($p_link)-1] != '/') { $p_link = $p_link.'/'; }
  } else { 
    $p_link = get_permalink(); 
  }

  // Based on the structure, append the appropriate ending
  if (!(strstr($p_link,'?'))) { $link_part = $p_link.'?'; } else { $link_part = $p_link.'&'; }

  return $link_part;
}

// Configure the "Next" link in the calendar
function next_link($cur_year,$cur_month,$minical = false)
{
  $mod_rewrite_months = array(1=>'jan','feb','mar','apr','may','jun','jul','aug','sept','oct','nov','dec');
  $next_year = $cur_year + 1;

  if ($cur_month == 12)
    {
      if ($minical) { $rlink = ''; } else { $rlink = __('Next','calendar'); }
      return '<a href="' . permalink_prefix() . 'month=jan&amp;yr=' . $next_year . '">'.$rlink.' &raquo;</a>';
    }
  else
    {
      $next_month = $cur_month + 1;
      $month = $mod_rewrite_months[$next_month];
      if ($minical) { $rlink = ''; } else { $rlink = __('Next','calendar'); }
      return '<a href="' . permalink_prefix() . 'month='.$month.'&amp;yr=' . $cur_year . '">'.$rlink.' &raquo;</a>';
    }
}

// Configure the "Previous" link in the calendar
function prev_link($cur_year,$cur_month,$minical = false)
{
  $mod_rewrite_months = array(1=>'jan','feb','mar','apr','may','jun','jul','aug','sept','oct','nov','dec');
  $last_year = $cur_year - 1;

  if ($cur_month == 1)
    {
      if ($minical) { $llink = ''; } else { $llink = __('Prev','calendar'); }
      return '<a href="' . permalink_prefix() . 'month=dec&amp;yr='. $last_year .'">&laquo; '.$llink.'</a>';
    }
  else
    {
      $next_month = $cur_month - 1;
      $month = $mod_rewrite_months[$next_month];
      if ($minical) { $llink = ''; } else { $llink = __('Prev','calendar'); }
      return '<a href="' . permalink_prefix() . 'month='.$month.'&amp;yr=' . $cur_year . '">&laquo; '.$llink.'</a>';
    }
}

// Print upcoming events
function upcoming_events($cat_list = '')
{
  global $wpdb;

  // Find out if we should be displaying upcoming events
  $display = $wpdb->get_var("SELECT config_value FROM ".WP_CALENDAR_CONFIG_TABLE." WHERE config_item='display_upcoming'",0,0);
  
  if ($display == 'true')
    {
      // Get number of days we should go into the future 
      $future_days = $wpdb->get_var("SELECT config_value FROM ".WP_CALENDAR_CONFIG_TABLE." WHERE config_item='display_upcoming_days'",0,0);
      $day_count = 1;
      
      $output = '';
      while ($day_count < $future_days+1)
	{
	  list($y,$m,$d) = explode("-",date("Y-m-d",mktime($day_count*24,0,0,date("m",ctwo()),date("d",ctwo()),date("Y",ctwo()))));
	  $events = grab_events($y,$m,$d,'upcoming',$cat_list);
	  usort($events, "time_cmp");
	  if (count($events) != 0) {
	    $output .= '<li>'.date_i18n(get_option('date_format'),mktime($day_count*24,0,0,date("m",ctwo()),date("d",ctwo()),date("Y",ctwo()))).'<ul>';
	  } 
	  foreach($events as $event)
	    {
	      if ($event->event_time == '00:00:00') {
		$time_string = ' '.__('all day','calendar');
	      }
	      else {
		$time_string = ' '.__('at','calendar').' '.date(get_option('time_format'), strtotime(stripslashes($event->event_time)));
	      }
              $output .= '<li>'.draw_event($event).$time_string.'</li>';
	    }
	  if (count($events) != 0) {
	    $output .= '</ul></li>';
	  }
	  $day_count = $day_count+1;
	}

      if ($output != '')
	{
	  $visual = '<ul>';
	  $visual .= $output;
	  $visual .= '</ul>';
	  return $visual;
	}
    }
}

// Print todays events
function todays_events($cat_list = '')
{
  global $wpdb;

  // Find out if we should be displaying todays events
  $display = $wpdb->get_var("SELECT config_value FROM ".WP_CALENDAR_CONFIG_TABLE." WHERE config_item='display_todays'",0,0);

  if ($display == 'true')
    {
      $output = '<ul>';
      $events = grab_events(date("Y",ctwo()),date("m",ctwo()),date("d",ctwo()),'todays',$cat_list);
      usort($events, "time_cmp");
      foreach($events as $event)
	{
	  if ($event->event_time == '00:00:00') {
	    $time_string = ' '.__('all day','calendar');
	  }
	  else {
	    $time_string = ' '.__('at','calendar').' '.date(get_option('time_format'), strtotime(stripslashes($event->event_time)));
	  }
	  $output .= '<li>'.draw_event($event).$time_string.'</li>';
	}
      $output .= '</ul>';
      if (count($events) != 0)
	{
	  return $output;
	}
    }
}

// Function to compare time in event objects
function time_cmp($a, $b)
{
  if ($a->event_time == $b->event_time) {
    return 0;
  }
  return ($a->event_time < $b->event_time) ? -1 : 1;
}

// Used to draw multiple events
function draw_events($events)
{
  // We need to sort arrays of objects by time
  usort($events, "time_cmp");
  $output = '';
  // Now process the events
  foreach($events as $event)
    {
      $output .= '* '.draw_event($event).'<br />';
    }
  return $output;
}

// The widget to show the mini calendar
function widget_init_events_calendar() { 
  // Check for required functions
  if (!function_exists('wp_register_sidebar_widget'))
    return;

  function widget_events_calendar($args) {
    extract($args);
    $the_title = stripslashes(get_option('events_calendar_widget_title'));
    $the_cats = stripslashes(get_option('events_calendar_widget_cats'));
    $widget_title = empty($the_title) ? __('Calendar','calendar') : $the_title;
    $the_events = minical($the_cats);
    if ($the_events != '') {
      echo $before_widget;
      echo $before_title . $widget_title . $after_title;
      echo '<br />'.$the_events;
      echo $after_widget;
    }
  }

  function widget_events_calendar_control() {
    $widget_title = stripslashes(get_option('events_calendar_widget_title'));
    $widget_cats = stripslashes(get_option('events_calendar_widget_cats'));
    if (isset($_POST['events_calendar_widget_title']) || isset($_POST['events_calendar_widget_cats'])) {
      update_option('events_calendar_widget_title',strip_tags($_POST['events_calendar_widget_title']));
      update_option('events_calendar_widget_cats',strip_tags($_POST['events_calendar_widget_cats']));
    }
    ?>
    <p>
       <label for="events_calendar_widget_title"><?php _e('Title','calendar'); ?>:<br />
       <input class="widefat" type="text" id="events_calendar_widget_title" name="events_calendar_widget_title" value="<?php echo $widget_title; ?>"/></label>
       <label for="events_calendar_widget_cats"><?php _e('Comma separated category id list','calendar'); ?>:<br />
       <input class="widefat" type="text" id="events_calendar_widget_cats" name="events_calendar_widget_cats" value="<?php echo $widget_cats; ?>"/></label>
    </p>
    <?php
  }

  wp_register_sidebar_widget('events_calendar',__('Calendar','calendar'),'widget_events_calendar',array('description'=>'A calendar of your events'));
  wp_register_widget_control('events_calendar','events_calendar','widget_events_calendar_control');
}

// The widget to show todays events in the sidebar
function widget_init_calendar_today() {
  // Check for required functions
  if (!function_exists('wp_register_sidebar_widget'))
    return;

  function widget_calendar_today($args) {
    extract($args);
    $the_title = stripslashes(get_option('calendar_today_widget_title'));
    $the_cats = stripslashes(get_option('calendar_today_widget_cats'));
    $widget_title = empty($the_title) ? __('Today\'s Events','calendar') : $the_title;
    $the_events = todays_events($the_cats);
    if ($the_events != '') {
      echo $before_widget;
      echo $before_title . $widget_title . $after_title;
      echo $the_events;
      echo $after_widget;
    }
  }

  function widget_calendar_today_control() {
    $widget_title = stripslashes(get_option('calendar_today_widget_title'));
    $widget_cats = stripslashes(get_option('calendar_today_widget_cats'));
    if (isset($_POST['calendar_today_widget_title']) || isset($_POST['calendar_today_widget_cats'])) {
      update_option('calendar_today_widget_title',strip_tags($_POST['calendar_today_widget_title']));
      update_option('calendar_today_widget_cats',strip_tags($_POST['calendar_today_widget_cats']));
    }
    ?>
    <p>
       <label for="calendar_today_widget_title"><?php _e('Title','calendar'); ?>:<br />
       <input class="widefat" type="text" id="calendar_today_widget_title" name="calendar_today_widget_title" value="<?php echo $widget_title; ?>"/></label>
       <label for="calendar_today_widget_cats"><?php _e('Comma separated category id list','calendar'); ?>:<br />
       <input class="widefat" type="text" id="calendar_today_widget_cats" name="calendar_today_widget_cats" value="<?php echo $widget_cats; ?>"/></label>
    </p>
    <?php
  }

  wp_register_sidebar_widget('todays_events_calendar',__('Today\'s Events','calendar'),'widget_calendar_today',array('description'=>'A list of your events today'));
  wp_register_widget_control('todays_events_calendar','todays_events_calendar','widget_calendar_today_control');
  }

// The widget to show todays events in the sidebar                                              
function widget_init_calendar_upcoming() {
  // Check for required functions                                                               
  if (!function_exists('wp_register_sidebar_widget'))
    return;

  function widget_calendar_upcoming($args) {
    extract($args);
    $the_title = stripslashes(get_option('calendar_upcoming_widget_title'));
    $the_cats = stripslashes(get_option('calendar_upcoming_widget_cats'));
    $widget_title = empty($the_title) ? __('Upcoming Events','calendar') : $the_title;
    $the_events = upcoming_events($the_cats);
    if ($the_events != '') {
      echo $before_widget;
      echo $before_title . $widget_title . $after_title;
      echo $the_events;
      echo $after_widget;
    }
  }

  function widget_calendar_upcoming_control() {
    $widget_title = stripslashes(get_option('calendar_upcoming_widget_title'));
    $widget_cats = stripslashes(get_option('calendar_upcoming_widget_cats'));
    if (isset($_POST['calendar_upcoming_widget_title']) || isset($_POST['calendar_upcoming_widget_cats'])) {
      update_option('calendar_upcoming_widget_title',strip_tags($_POST['calendar_upcoming_widget_title']));
      update_option('calendar_upcoming_widget_cats',strip_tags($_POST['calendar_upcoming_widget_cats']));
    }
    ?>
    <p>
       <label for="calendar_upcoming_widget_title"><?php _e('Title','calendar'); ?>:<br />
       <input class="widefat" type="text" id="calendar_upcoming_widget_title" name="calendar_upcoming_widget_title" value="<?php echo $widget_title; ?>"/></label>
       <label for="calendar_upcoming_widget_cats"><?php _e('Comma separated category id list','calendar'); ?>:<br />
       <input class="widefat" type="text" id="calendar_upcoming_widget_cats" name="calendar_upcoming_widget_cats" value="<?php echo $widget_cats; ?>"/></label>
    </p>
    <?php
  }

  wp_register_sidebar_widget('upcoming_events_calendar',__('Upcoming Events','calendar'),'widget_calendar_upcoming',array('description'=>'A list of your upcoming events'));
  wp_register_widget_control('upcoming_events_calendar','upcoming_events_calendar','widget_calendar_upcoming_control');
}

// Used to draw an event to the screen
function draw_event($event)
{
  global $wpdb;

  // Before we do anything we want to know if we                                             
  // should display the author and/or show categories. 
  // We check for this later                                      
  $display_author = $wpdb->get_var("SELECT config_value FROM ".WP_CALENDAR_CONFIG_TABLE." WHERE config_item='display_author'",0,0);
  $show_cat = $wpdb->get_var("SELECT config_value FROM ".WP_CALENDAR_CONFIG_TABLE." WHERE config_item='enable_categories'",0,0);
  $style = '';
  if ($show_cat == 'true')
    {
      $sql = "SELECT * FROM " . WP_CALENDAR_CATEGORIES_TABLE . " WHERE category_id=".mysql_escape_string($event->event_category);
      $cat_details = $wpdb->get_row($sql);
      $style = 'style="background-color:'.stripslashes($cat_details->category_colour).';"';
    }

  $header_details =  '<span class="event-title" '.$style.'>'.stripslashes($event->event_title).'</span><br />
<span class="event-title-break"></span><br />';
  if ($event->event_time != "00:00:00")
    {
      $header_details .= '<strong>'.__('Time','calendar').':</strong> ' . date(get_option('time_format'), strtotime(stripslashes($event->event_time))) . '<br />';
    }
  if ($display_author == 'true')
    {
      $e = get_userdata(stripslashes($event->event_author));
      $header_details .= '<strong>'.__('Posted by', 'calendar').':</strong> '.$e->display_name.'<br />';
    }
  if ($display_author == 'true' || $event->event_time != "00:00:00")
    {
      $header_details .= '<span class="event-content-break"></span><br />';
    }
  if ($event->event_link != '') { $linky = stripslashes($event->event_link); }
  else { $linky = '#'; }

  $details = '<span class="calnk"><a href="'.$linky.'" '.$style.'>' . stripslashes($event->event_title) . '<span '.$style.'>' . $header_details . '' . stripslashes($event->event_desc) . '</span></a></span>';

  return $details;
}

// Grab all events for the requested date from calendar
function grab_events($y,$m,$d,$typing,$cat_list = '')
{
  global $wpdb;

     $arr_events = array();

     // Get the date format right
     $date = $y . '-' . $m . '-' . $d;

     // Format the category list
     if ($cat_list == '') { $cat_sql = ''; }
     else { $cat_sql = 'AND event_category in ('.$cat_list.')'; }
     
     // The collated SQL code
     $sql = "SELECT a.*,'Normal' AS type  FROM " . WP_CALENDAR_TABLE . " AS a WHERE a.event_begin <= '$date' AND a.event_end >= '$date' AND a.event_recur = 'S' ".$cat_sql." 
UNION ALL 
SELECT b.*,'Yearly' AS type FROM " . WP_CALENDAR_TABLE . " AS b WHERE b.event_recur = 'Y' AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM b.event_begin) AND b.event_repeats = 0 ".$cat_sql." 
UNION ALL 
SELECT c.*,'Yearly' AS type FROM " . WP_CALENDAR_TABLE . " AS c WHERE c.event_recur = 'Y' AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM c.event_begin) AND c.event_repeats != 0 AND (EXTRACT(YEAR FROM '$date')-EXTRACT(YEAR FROM c.event_begin)) <= c.event_repeats ".$cat_sql." 
UNION ALL 
SELECT d.*,'Monthly' AS type FROM " . WP_CALENDAR_TABLE . " AS d WHERE d.event_recur = 'M' AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM d.event_begin) AND d.event_repeats = 0 ".$cat_sql." 
UNION ALL
SELECT e.*,'Monthly' AS type FROM " . WP_CALENDAR_TABLE . " AS e WHERE e.event_recur = 'M' AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM e.event_begin) AND e.event_repeats != 0 AND (PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM '$date'),EXTRACT(YEAR_MONTH FROM e.event_begin))) <= e.event_repeats ".$cat_sql." 
UNION ALL
SELECT f.*,'MonthSun' AS type FROM " . WP_CALENDAR_TABLE . " AS f WHERE f.event_recur = 'U' AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM f.event_begin) AND f.event_repeats = 0 ".$cat_sql." 
UNION ALL
SELECT g.*,'MonthSun' AS type FROM " . WP_CALENDAR_TABLE . " AS g WHERE g.event_recur = 'U' AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM g.event_begin) AND g.event_repeats != 0 AND (PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM '$date'),EXTRACT(YEAR_MONTH FROM g.event_begin))) <= g.event_repeats ".$cat_sql." 
UNION ALL
SELECT h.*,'Weekly' AS type FROM " . WP_CALENDAR_TABLE . " AS h WHERE h.event_recur = 'W' AND '$date' >= h.event_begin AND h.event_repeats = 0 ".$cat_sql." 
UNION ALL
SELECT i.*,'Weekly' AS type FROM " . WP_CALENDAR_TABLE . " AS i WHERE i.event_recur = 'W' AND '$date' >= i.event_begin AND i.event_repeats != 0 AND (i.event_repeats*7) >= (TO_DAYS('$date') - TO_DAYS(i.event_end)) ".$cat_sql." 
ORDER BY event_id";

     // Run the collated code
     $events =$wpdb->get_results($sql);
     if (!empty($events))
       {
	 foreach($events as $event)
	   {
	     if ($event->type == 'Normal')
	       {
		 array_push($arr_events, $event);
	       }
	     else if ($event->type == 'Yearly')
	       {
		 // This is going to get complex so lets setup what we would place in for
		 // an event so we can drop it in with ease

		 // Technically we don't care about the years, but we need to find out if the
		 // event spans the turn of a year so we can deal with it appropriately.
		 $year_begin = date('Y',strtotime($event->event_begin));
		 $year_end = date('Y',strtotime($event->event_end));

		 if ($year_begin == $year_end)
		   {
		     if (date('m-d',strtotime($event->event_begin)) <= date('m-d',strtotime($date)) &&
			 date('m-d',strtotime($event->event_end)) >= date('m-d',strtotime($date)))
		       {
			 array_push($arr_events, $event);
		       }
		   }
		 else if ($year_begin < $year_end)
		   {
		     if (date('m-d',strtotime($event->event_begin)) <= date('m-d',strtotime($date)) ||
			 date('m-d',strtotime($event->event_end)) >= date('m-d',strtotime($date)))
		       {
			 array_push($arr_events, $event);
		       }
		   }
	       }
	     else if ($event->type == 'Monthly')
	       {
		 // This is going to get complex so lets setup what we would place in for
		 // an event so we can drop it in with ease

		 // Technically we don't care about the years or months, but we need to find out if the
		 // event spans the turn of a year or month so we can deal with it appropriately.
		 $month_begin = date('m',strtotime($event->event_begin));
		 $month_end = date('m',strtotime($event->event_end));

		 if (($month_begin == $month_end) && (strtotime($event->event_begin) <= strtotime($date)))
		   {
		     if (date('d',strtotime($event->event_begin)) <= date('d',strtotime($date)) &&
			 date('d',strtotime($event->event_end)) >= date('d',strtotime($date)))
		       {
			 array_push($arr_events, $event);
		       }
		   }
		 else if (($month_begin < $month_end) && (strtotime($event->event_begin) <= strtotime($date)))
		   {
		     if ( ($event->event_begin <= date('Y-m-d',strtotime($date))) && (date('d',strtotime($event->event_begin)) <= date('d',strtotime($date)) ||
			   date('d',strtotime($event->event_end)) >= date('d',strtotime($date))) )
		       {
			 array_push($arr_events, $event);
		       }
		   }
	       }
	     else if ($event->type == 'MonthSun')
	       {
		 // This used to be complex but writing the dt_of_sun() function helped loads!

		 // Technically we don't care about the years or months, but we need to find out if the
		 // event spans the turn of a year or month so we can deal with it appropriately.
		 $month_begin = date('m',strtotime($event->event_begin));
		 $month_end = date('m',strtotime($event->event_end));

		 // Setup some variables and get some values
		 $dow = date('w',strtotime($event->event_begin));
		 if ($dow == 0) { $dow = 7; }
		 $start_ent_this = dt_of_sun($date,np_of_day($event->event_begin),$dow);
		 $start_ent_prev = dt_of_sun(date('Y-m-d',strtotime($date.'-1 month')),np_of_day($event->event_begin),$dow);
		 $len_ent = strtotime($event->event_end)-strtotime($event->event_begin);

		 // The grunt work
		 if (($month_begin == $month_end) && (strtotime($event->event_begin) <= strtotime($date)))
		   {
		     // The checks
		     if (strtotime($event->event_begin) <= strtotime($date) && strtotime($event->event_end) >= strtotime($date)) // Handle the first occurance
		       {
			 array_push($arr_events, $event);
		       }
		     else if (strtotime($start_ent_this) <= strtotime($date) && strtotime($date) <= strtotime($start_ent_this)+$len_ent) // Now remaining items
		       {
			 array_push($arr_events, $event);
		       }
		   }
		 else if (($month_begin < $month_end) && (strtotime($event->event_begin) <= strtotime($date)))
		   {
		     // The checks
		     if (strtotime($event->event_begin) <= strtotime($date) && strtotime($event->event_end) >= strtotime($date)) // Handle the first occurance
		       {
                         array_push($arr_events, $event);
		       }
		     else if (strtotime($start_ent_prev) <= strtotime($date) && strtotime($date) <= strtotime($start_ent_prev)+$len_ent) // Remaining items from prev month
		       {
                         array_push($arr_events, $event);
                       }
		     else if (strtotime($start_ent_this) <= strtotime($date) && strtotime($date) <= strtotime($start_ent_this)+$len_ent) // Remaining items starting this month
		       {
                         array_push($arr_events, $event);
                       }
		   }
	       }
	     else if ($event->type == 'Weekly')
	       {
		 // This is going to get complex so lets setup what we would place in for
		 // an event so we can drop it in with ease

		 // Now we are going to check to see what day the original event
		 // fell on and see if the current date is both after it and on
		 // the correct day. If it is, display the event!
		 $day_start_event = date('D',strtotime($event->event_begin));
		 $day_end_event = date('D',strtotime($event->event_end));
		 $current_day = date('D',strtotime($date));

		 $plan = array();
		 $plan['Mon'] = 1;
		 $plan['Tue'] = 2;
		 $plan['Wed'] = 3;
		 $plan['Thu'] = 4;
		 $plan['Fri'] = 5;
		 $plan['Sat'] = 6;
		 $plan['Sun'] = 7;

		 if ($plan[$day_start_event] > $plan[$day_end_event])
		   {
		     if (($plan[$day_start_event] <= $plan[$current_day]) || ($plan[$current_day] <= $plan[$day_end_event]))
		       {
			 array_push($arr_events, $event);
		       }
		   }
		 else if (($plan[$day_start_event] < $plan[$day_end_event]) || ($plan[$day_start_event]== $plan[$day_end_event]))
		   {
		     if (($plan[$day_start_event] <= $plan[$current_day]) && ($plan[$current_day] <= $plan[$day_end_event]))
		       {
			 array_push($arr_events, $event);
		       }
		   }
	       }
	   }
       }

     return $arr_events;
}

// Setup comparison functions for building the calendar later
function calendar_month_comparison($month)
{
  $current_month = strtolower(date("M", ctwo()));
  if (isset($_GET['yr']) && isset($_GET['month']))
    {
      if ($month == $_GET['month'])
	{
	  return ' selected="selected"';
	}
    }
  elseif ($month == $current_month)
    {
      return ' selected="selected"';
    }
}
function calendar_year_comparison($year)
{
  $current_year = strtolower(date("Y", ctwo()));
  if (isset($_GET['yr']) && isset($_GET['month']))
    {
      if ($year == $_GET['yr'])
	{
	  return ' selected="selected"';
	}
    }
  else if ($year == $current_year)
    {
      return ' selected="selected"';
    }
}

// Actually do the printing of the calendar
// Compared to searching for and displaying events
// this bit is really rather easy!
function calendar($cat_list = '')
{
  global $wpdb;

    // Deal with the week not starting on a monday
    if (get_option('start_of_week') == 0)
      {
	$name_days = array(1=>__('Sunday','calendar'),__('Monday','calendar'),__('Tuesday','calendar'),__('Wednesday','calendar'),__('Thursday','calendar'),__('Friday','calendar'),__('Saturday','calendar'));
      }
    // Choose Monday if anything other than Sunday is set
    else
      {
	$name_days = array(1=>__('Monday','calendar'),__('Tuesday','calendar'),__('Wednesday','calendar'),__('Thursday','calendar'),__('Friday','calendar'),__('Saturday','calendar'),__('Sunday','calendar'));
      }

    // Carry on with the script
    $name_months = array(1=>__('January','calendar'),__('February','calendar'),__('March','calendar'),__('April','calendar'),__('May','calendar'),__('June','calendar'),__('July','calendar'),__('August','calendar'),__('September','calendar'),__('October','calendar'),__('November','calendar'),__('December','calendar'));

    // If we don't pass arguments we want a calendar that is relevant to today
    if (empty($_GET['month']) || empty($_GET['yr']))
    {
        $c_year = date("Y",ctwo());
        $c_month = date("m",ctwo());
        $c_day = date("d",ctwo());
    }

    // Years get funny if we exceed 3000, so we use this check
    if (isset($_GET['yr'])) 
    {    
    if ($_GET['yr'] <= 3000 && $_GET['yr'] >= 0 && (int)$_GET['yr'] != 0)
    {
        // This is just plain nasty and all because of permalinks
        // which are no longer used, this will be cleaned up soon
        if ($_GET['month'] == 'jan' || $_GET['month'] == 'feb' || $_GET['month'] == 'mar' || $_GET['month'] == 'apr' || $_GET['month'] == 'may' || $_GET['month'] == 'jun' || $_GET['month'] == 'jul' || $_GET['month'] == 'aug' || $_GET['month'] == 'sept' || $_GET['month'] == 'oct' || $_GET['month'] == 'nov' || $_GET['month'] == 'dec')
	  {

	       // Again nasty code to map permalinks into something
	       // databases can understand. This will be cleaned up
               $c_year = mysql_escape_string($_GET['yr']);
               if ($_GET['month'] == 'jan') { $t_month = 1; }
               else if ($_GET['month'] == 'feb') { $t_month = 2; }
               else if ($_GET['month'] == 'mar') { $t_month = 3; }
               else if ($_GET['month'] == 'apr') { $t_month = 4; }
               else if ($_GET['month'] == 'may') { $t_month = 5; }
               else if ($_GET['month'] == 'jun') { $t_month = 6; }
               else if ($_GET['month'] == 'jul') { $t_month = 7; }
               else if ($_GET['month'] == 'aug') { $t_month = 8; }
               else if ($_GET['month'] == 'sept') { $t_month = 9; }
               else if ($_GET['month'] == 'oct') { $t_month = 10; }
               else if ($_GET['month'] == 'nov') { $t_month = 11; }
               else if ($_GET['month'] == 'dec') { $t_month = 12; }
               $c_month = $t_month;
               $c_day = date("d",ctwo());
        }
	// No valid month causes the calendar to default to today
        else
        {
               $c_year = date("Y",ctwo());
               $c_month = date("m",ctwo());
               $c_day = date("d",ctwo());
        }
    }
    }
    // No valid year causes the calendar to default to today
    else
    {
        $c_year = date("Y",ctwo());
        $c_month = date("m",ctwo());
        $c_day = date("d",ctwo());
    }

    // Fix the days of the week if week start is not on a monday
    if (get_option('start_of_week') == 0)
      {
	$first_weekday = date("w",mktime(0,0,0,$c_month,1,$c_year));
        $first_weekday = ($first_weekday==0?1:$first_weekday+1);
      }
    // Otherwise assume the week starts on a Monday. Anything other 
    // than Sunday or Monday is just plain odd
    else
      {
	$first_weekday = date("w",mktime(0,0,0,$c_month,1,$c_year));
	$first_weekday = ($first_weekday==0?7:$first_weekday);
      }

    $days_in_month = date("t", mktime (0,0,0,$c_month,1,$c_year));

    // Start the table and add the header and naviagtion
    $calendar_body = '';
    $calendar_body .= '
<table cellspacing="1" cellpadding="0" class="calendar-table">
';

    // We want to know if we should display the date switcher
    $date_switcher = $wpdb->get_var("SELECT config_value FROM ".WP_CALENDAR_CONFIG_TABLE." WHERE config_item='display_jump'",0,0);

    if ($date_switcher == 'true')
      {
	$calendar_body .= '<tr>
        <td colspan="7" class="calendar-date-switcher">
            <form method="get" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'">
';
	$qsa = array();
	parse_str($_SERVER['QUERY_STRING'],$qsa);
	foreach ($qsa as $name => $argument)
	  {
	    if ($name != 'month' && $name != 'yr')
	      {
		$calendar_body .= '<input type="hidden" name="'.strip_tags($name).'" value="'.strip_tags($argument).'" />
';
	      }
	  }

	// We build the months in the switcher
	$calendar_body .= '
            '.__('Month','calendar').': <select name="month" style="width:100px;">
            <option value="jan"'.calendar_month_comparison('jan').'>'.__('January','calendar').'</option>
            <option value="feb"'.calendar_month_comparison('feb').'>'.__('February','calendar').'</option>
            <option value="mar"'.calendar_month_comparison('mar').'>'.__('March','calendar').'</option>
            <option value="apr"'.calendar_month_comparison('apr').'>'.__('April','calendar').'</option>
            <option value="may"'.calendar_month_comparison('may').'>'.__('May','calendar').'</option>
            <option value="jun"'.calendar_month_comparison('jun').'>'.__('June','calendar').'</option>
            <option value="jul"'.calendar_month_comparison('jul').'>'.__('July','calendar').'</option> 
            <option value="aug"'.calendar_month_comparison('aug').'>'.__('August','calendar').'</option> 
            <option value="sept"'.calendar_month_comparison('sept').'>'.__('September','calendar').'</option> 
            <option value="oct"'.calendar_month_comparison('oct').'>'.__('October','calendar').'</option> 
            <option value="nov"'.calendar_month_comparison('nov').'>'.__('November','calendar').'</option> 
            <option value="dec"'.calendar_month_comparison('dec').'>'.__('December','calendar').'</option> 
            </select>
            '.__('Year','calendar').': <select name="yr" style="width:60px;">
';

	// The year builder is string mania. If you can make sense of this, you know your PHP!

	$past = 30;
	$future = 30;
	$fut = 1;
	$f = '';
	$p = '';
	while ($past > 0)
	  {
	    $p .= '            <option value="';
	    $p .= date("Y",ctwo())-$past;
	    $p .= '"'.calendar_year_comparison(date("Y",ctwo())-$past).'>';
	    $p .= date("Y",ctwo())-$past.'</option>
';
	    $past = $past - 1;
	  }
	while ($fut < $future) 
	  {
	    $f .= '            <option value="';
	    $f .= date("Y",ctwo())+$fut;
	    $f .= '"'.calendar_year_comparison(date("Y",ctwo())+$fut).'>';
	    $f .= date("Y",ctwo())+$fut.'</option>
';
	    $fut = $fut + 1;
	  } 
	$calendar_body .= $p;
	$calendar_body .= '            <option value="'.date("Y",ctwo()).'"'.calendar_year_comparison(date("Y",ctwo())).'>'.date("Y",ctwo()).'</option>
';
	$calendar_body .= $f;
        $calendar_body .= '</select>
            <input type="submit" value="'.__('Go','calendar').'" />
            </form>
        </td>
</tr>
';
      }

    // The header of the calendar table and the links. Note calls to link functions
    $calendar_body .= '<tr>
                <td colspan="7" class="calendar-heading">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                    <td class="calendar-prev">' . prev_link($c_year,$c_month) . '</td>
                    <td class="calendar-month">'.$name_months[(int)$c_month].' '.$c_year.'</td>
                    <td class="calendar-next">' . next_link($c_year,$c_month) . '</td>
                    </tr>
                    </table>
                </td>
</tr>
';

    // Print the headings of the days of the week
    $calendar_body .= '<tr>
';
    for ($i=1; $i<=7; $i++) 
      {
	// Colours need to be different if the starting day of the week is different
	if (get_option('start_of_week') == 0)
	  {
	    $calendar_body .= '        <td class="'.($i<7&&$i>1?'normal-day-heading':'weekend-heading').'">'.$name_days[$i].'</td>
';
	  }
	else
	  {
	    $calendar_body .= '        <td class="'.($i<6?'normal-day-heading':'weekend-heading').'">'.$name_days[$i].'</td>
';
	  }
      }
    $calendar_body .= '</tr>
';
    $go = FALSE;
    for ($i=1; $i<=$days_in_month;)
      {
        $calendar_body .= '<tr>
';
        for ($ii=1; $ii<=7; $ii++)
	  {
            if ($ii==$first_weekday && $i==1)
	      {
		$go = TRUE;
	      }
            elseif ($i > $days_in_month ) 
	      {
	    	$go = FALSE;
	      }
            if ($go) 
	      {
		// Colours again, this time for the day numbers
		if (get_option('start_of_week') == 0)
		  {
		    // This bit of code is for styles believe it or not.
		    $grabbed_events = grab_events($c_year,$c_month,$i,'calendar',$cat_list);
		    $no_events_class = '';
		    if (!count($grabbed_events))
		      {
			$no_events_class = ' no-events';
		      }
		    $calendar_body .= '        <td class="'.(date("Ymd", mktime (0,0,0,$c_month,$i,$c_year))==date("Ymd",ctwo())?'current-day':'day-with-date').$no_events_class.'"><span '.($ii<7&&$ii>1?'':'class="weekend"').'>'.$i++.'</span><span class="event"><br />' . draw_events($grabbed_events) . '</span></td>
';
		  }
		else
		  {
		    $grabbed_events = grab_events($c_year,$c_month,$i,'calendar',$cat_list);
		    $no_events_class = '';
	            if (!count($grabbed_events))
		      {
			$no_events_class = ' no-events';
		      }
		    $calendar_body .= '        <td class="'.(date("Ymd", mktime (0,0,0,$c_month,$i,$c_year))==date("Ymd",ctwo())?'current-day':'day-with-date').$no_events_class.'"><span '.($ii<6?'':'class="weekend"').'>'.$i++.'</span><span class="event"><br />' . draw_events($grabbed_events) . '</span></td>
';
		  }
	      }
            else 
	      {
		$calendar_body .= '        <td class="day-without-date">&nbsp;</td>
';
	      }
        }
        $calendar_body .= '</tr>
';
    }
    $calendar_body .= '</table>
';
    $show_cat = $wpdb->get_var("SELECT config_value FROM ".WP_CALENDAR_CONFIG_TABLE." WHERE config_item='enable_categories'",0,0);

    if ($show_cat == 'true')
      {
	$sql = "SELECT * FROM " . WP_CALENDAR_CATEGORIES_TABLE . " ORDER BY category_name ASC";
	$cat_details = $wpdb->get_results($sql);
        $calendar_body .= '<table class="cat-key">
<tr><td colspan="2" class="cat-key-cell"><strong>'.__('Category Key','calendar').'</strong></td></tr>
';
        foreach($cat_details as $cat_detail)
	  {
	    $calendar_body .= '<tr><td style="background-color:'.$cat_detail->category_colour.'; width:20px; height:20px;" class="cat-key-cell"></td>
<td class="cat-key-cell">&nbsp;'.$cat_detail->category_name.'</td></tr>';
	  }
        $calendar_body .= '</table>
';
      }

    // A little link to yours truly. See the README if you wish to remove this
    $calendar_body .= '<div class="kjo-link" style="visibility:visible !important;display:block !important;"><p>'.__('Calendar developed and supported by ', 'calendar').'<a href="http://www.kieranoshea.com">Kieran O\'Shea</a></p></div>
';

    // Phew! After that bit of string building, spit it all out.
    // The actual printing is done by the calling function.
    return $calendar_body;
}

// Used to create a hover will all a day's events in for minical
function minical_draw_events($events,$day_of_week = '')
{
  // We need to sort arrays of objects by time
  usort($events, "time_cmp");
  // Only show anything if there are events
  $output = '';
  if (count($events)) {
    // Setup the wrapper
    $output = '<span class="calnk"><a href="#" style="background-color:#F6F79B;">'.$day_of_week.'<span>';
    // Now process the events
    foreach($events as $event)
      {
	if ($event->event_time == '00:00:00') { $the_time = 'all day'; } else { $the_time = 'at '.date(get_option('time_format'), strtotime(stripslashes($event->event_time))); } 
	$output .= '* <strong>'.$event->event_title.'</strong> '.$the_time.'<br />';
      }
    // The tail
    $output .= '</span></a></span>';
  } else {
    $output .= $day_of_week;
  }
  return $output;
}

function minical($cat_list = '') {
  
  global $wpdb;

  // Deal with the week not starting on a monday                                                                                                                                  
  if (get_option('start_of_week') == 0)
    {
      $name_days = array(1=>__('Su','calendar'),__('Mo','calendar'),__('Tu','calendar'),__('We','calendar'),__('Th','calendar'),__('Fr','calendar'),__('Sa','calendar'));
    }
  // Choose Monday if anything other than Sunday is set                                                                                                                           
  else
    {
      $name_days = array(1=>__('Mo','calendar'),__('Tu','calendar'),__('We','calendar'),__('Th','calendar'),__('Fr','calendar'),__('Sa','calendar'),__('Su','calendar'));
    }

  // Carry on with the script                                                                                                                                                     
  $name_months = array(1=>__('January','calendar'),__('February','calendar'),__('March','calendar'),__('April','calendar'),__('May','calendar'),__('June','calendar'),__('July','\
calendar'),__('August','calendar'),__('September','calendar'),__('October','calendar'),__('November','calendar'),__('December','calendar'));

  // If we don't pass arguments we want a calendar that is relevant to today                                                                                                      
  if (empty($_GET['month']) || empty($_GET['yr']))
    {
      $c_year = date("Y",ctwo());
      $c_month = date("m",ctwo());
      $c_day = date("d",ctwo());
    }

  // Years get funny if we exceed 3000, so we use this check                                                                                                                      
  if (isset($_GET['yr']))
    {
      if ($_GET['yr'] <= 3000 && $_GET['yr'] >= 0 && (int)$_GET['yr'] != 0)
	{
	  // This is just plain nasty and all because of permalinks
	  // which are no longer used, this will be cleaned up soon
	  if ($_GET['month'] == 'jan' || $_GET['month'] == 'feb' || $_GET['month'] == 'mar' || $_GET['month'] == 'apr' || $_GET['month'] == 'may' || $_GET['month'] == 'jun' || $_GET['month'] == 'jul' || $_GET['month'] == 'aug' || $_GET['month'] == 'sept' || $_GET['month'] == 'oct' || $_GET['month'] == 'nov' || $_GET['month'] == 'dec')
	    {

	      // Again nasty code to map permalinks into something                                                                                                                 
	      // databases can understand. This will be cleaned up
	      $c_year = mysql_escape_string($_GET['yr']);
	      if ($_GET['month'] == 'jan') { $t_month = 1; }
	      else if ($_GET['month'] == 'feb') { $t_month = 2; }
	      else if ($_GET['month'] == 'mar') { $t_month = 3; }
	      else if ($_GET['month'] == 'apr') { $t_month = 4; }
	      else if ($_GET['month'] == 'may') { $t_month = 5; }
	      else if ($_GET['month'] == 'jun') { $t_month = 6; }
	      else if ($_GET['month'] == 'jul') { $t_month = 7; }
	      else if ($_GET['month'] == 'aug') { $t_month = 8; }
	      else if ($_GET['month'] == 'sept') { $t_month = 9; }
	      else if ($_GET['month'] == 'oct') { $t_month = 10; }
	      else if ($_GET['month'] == 'nov') { $t_month = 11; }
	      else if ($_GET['month'] == 'dec') { $t_month = 12; }
	      $c_month = $t_month;
	      $c_day = date("d",ctwo());
	    }
	  // No valid month causes the calendar to default to today
	  else
	    {
	      $c_year = date("Y",ctwo());
	      $c_month = date("m",ctwo());
	      $c_day = date("d",ctwo());
	    }
	}
    }
  // No valid year causes the calendar to default to today                                                                                                                        
  else
    {
      $c_year = date("Y",ctwo());
      $c_month = date("m",ctwo());
      $c_day = date("d",ctwo());
    }

  // Fix the days of the week if week start is not on a monday                                                                                                                    
  if (get_option('start_of_week') == 0)
    {
      $first_weekday = date("w",mktime(0,0,0,$c_month,1,$c_year));
      $first_weekday = ($first_weekday==0?1:$first_weekday+1);
    }
  // Otherwise assume the week starts on a Monday. Anything other                                                                                                                 
  // than Sunday or Monday is just plain odd                                                                                                                                      
  else
    {
      $first_weekday = date("w",mktime(0,0,0,$c_month,1,$c_year));
      $first_weekday = ($first_weekday==0?7:$first_weekday);
    }

  $days_in_month = date("t", mktime (0,0,0,$c_month,1,$c_year));

  // Start the table and add the header and naviagtion                                                                                                                            
  $calendar_body = '';
  $calendar_body .= '<div style="width:200px;"><table cellspacing="1" cellpadding="0" class="calendar-table">
';


  // The header of the calendar table and the links. Note calls to link functions
  $calendar_body .= '<tr>
               <td colspan="7" class="calendar-heading" style="height:0;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td class="calendar-prev">' . prev_link($c_year,$c_month,true) . '</td>
                            <td class="calendar-month">'.$name_months[(int)$c_month].' '.$c_year.'</td>
                            <td class="calendar-next">' . next_link($c_year,$c_month,true) . '</td>
                        </tr>
                    </table>
               </td>
</tr>
';

    // Print the headings of the days of the week
    $calendar_body .= '<tr>
';
    for ($i=1; $i<=7; $i++)
      {
        // Colours need to be different if the starting day of the week is different
	if (get_option('start_of_week') == 0)
          {
            $calendar_body .= '        <td class="'.($i<7&&$i>1?'normal-day-heading':'weekend-heading').'" style="height:0;">'.$name_days[$i].'</td>
';
          }
        else
          {
            $calendar_body .= '        <td class="'.($i<6?'normal-day-heading':'weekend-heading').'" style="height:0;">'.$name_days[$i].'</td>
';
          }
      }
    $calendar_body .= '</tr>
';
    $go = FALSE;
    for ($i=1; $i<=$days_in_month;)
      {
        $calendar_body .= '<tr>
';
        for ($ii=1; $ii<=7; $ii++)
          {
            if ($ii==$first_weekday && $i==1)
              {
                $go = TRUE;
              }
            elseif ($i > $days_in_month )
              {
                $go = FALSE;
              }
            if ($go)
              {
                // Colours again, this time for the day numbers                                                                                                                     
                if (get_option('start_of_week') == 0)
                  {
                    // This bit of code is for styles believe it or not.
		    $grabbed_events = grab_events($c_year,$c_month,$i,'calendar',$cat_list);
                    $no_events_class = '';
                    if (!count($grabbed_events))
                      {
                        $no_events_class = ' no-events';
                      }
                    $calendar_body .= '        <td class="'.(date("Ymd", mktime (0,0,0,$c_month,$i,$c_year))==date("Ymd",ctwo())?'current-day':'day-with-date').$no_events_class.'" style="height:0;"><span '.($ii<7&&$ii>1?'':'class="weekend"').'>'.minical_draw_events($grabbed_events,$i++).'</span></td>
';
                  }
                else
                  {
                    $grabbed_events = grab_events($c_year,$c_month,$i,'calendar',$cat_list);
                    $no_events_class = '';
                    if (!count($grabbed_events))
                      {
                        $no_events_class = ' no-events';
                      }
                    $calendar_body .= '        <td class="'.(date("Ymd", mktime (0,0,0,$c_month,$i,$c_year))==date("Ymd",ctwo())?'current-day':'day-with-date').$no_events_class.'" style="height:0;"><span '.($ii<6?'':'class="weekend"').'>'.minical_draw_events($grabbed_events,$i++).'</span></td>
';
                  }
              }
            else
              {
                $calendar_body .= '        <td class="day-without-date" style="height:0;">&nbsp;</td>
';
              }
	  }
        $calendar_body .= '</tr>
';
      }
    $calendar_body .= '</table>
';

    // A little link to yours truly. See the README if you wish to remove this
    $calendar_body .= '<div class="kjo-link" style="visibility:visible !important;display:block !important;"><p>'.__('Calendar by ', 'calendar').'<a href="http://www.kieranoshea.com">Kieran O\'Shea</a></p></div>
';

    // Closing div
    $calendar_body .= '</div>
';
    // Phew! After that bit of string building, spit it all out.
    // The actual printing is done by the calling function.
    return $calendar_body;

}

?>
