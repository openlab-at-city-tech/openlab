<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

*/


register_activation_hook(TFK_ROOT_FILE, 'tfk_install');

function tfk_install() {

global $wpdb;

$charset_collate = $wpdb->get_charset_collate();

// register options
$arr = array(
	'version'	=> '1.0',
	'mode'		=> 'company', // or club
	'page_id'		=> 0, //  page ID to TFWP
	'page_url'		=> '', // URL to page
	'tfk_all'		=> 'projects', // projects, tasks, recent, dashboard (PRO)
	'tasks_per_page'	=> 5,
	'format_date'		=> 'Y-m-d',
	'format_time'		=> 'H:i',
	'number_updates'	=> 7,
	'avatar_size'		=> 48,
	'proximity'			=> 0, // 1 to enable
	'prio_size'			=> 0, // 1 to enable
	'access_read'		=> 'subscriber',
	'access_comment'	=> 'contributor',
	'access_post'		=> 'author',
	'access_manage'		=> 'editor',
	'comment_upload'	=> 1, // 0 to disable
);
add_option('tfk_options', $arr, '', 'no'); // no = not autoloaded

// create custom tables (if needed)

require_once(ABSPATH.'wp-admin/includes/upgrade.php');

$table = $wpdb->prefix.'tfk_item';
$sql = "CREATE TABLE $table (
  item_id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  project_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  item_parent_id int(10) unsigned NOT NULL DEFAULT '0',
  priority tinyint(3) unsigned NOT NULL DEFAULT '0',
  context varchar(64) NOT NULL DEFAULT '0',
  title varchar(255) NOT NULL DEFAULT '',
  description text NOT NULL,
  creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  start_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  deadline_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  completion_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  expected_duration smallint(5) unsigned NOT NULL DEFAULT '0',
  actual_duration smallint(5) unsigned NOT NULL DEFAULT '0',
  show_in_calendar tinyint(1) unsigned NOT NULL DEFAULT '0',
  show_private tinyint(1) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  author_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  last_change_author_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (item_id),
  KEY project_id (project_id),
  KEY user_id (user_id),
  KEY author_id (author_id)
) $charset_collate;";
dbDelta($sql);

$table = $wpdb->prefix.'tfk_item_status';
$sql = "CREATE TABLE $table (
  item_id int(10) unsigned NOT NULL DEFAULT '0',
  status_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  status_key tinyint(3) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (item_id,status_date),
  KEY itemId (user_id)
) $charset_collate;";
dbDelta($sql);

$table = $wpdb->prefix.'tfk_item_like';
$sql = "CREATE TABLE $table (
  item_id mediumint(8) unsigned NOT NULL,
  user_id mediumint(8) unsigned NOT NULL,
  post_date datetime NOT NULL,
  PRIMARY KEY  (item_id,user_id)
) $charset_collate;";
dbDelta($sql);

$table = $wpdb->prefix.'tfk_item_comment';
$sql = "CREATE TABLE $table (
  item_comment_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  item_id int(10) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  post_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  body text NOT NULL,
  PRIMARY KEY  (item_comment_id),
  KEY itemId (item_id)
) $charset_collate;";
dbDelta($sql);

$table = $wpdb->prefix.'tfk_item_comment_like';
$sql = "CREATE TABLE $table (
  item_comment_id int(10) unsigned NOT NULL,
  user_id mediumint(8) unsigned NOT NULL,
  post_date datetime NOT NULL,
  vote tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (item_comment_id,user_id)
) $charset_collate;";
dbDelta($sql);

$table = $wpdb->prefix.'tfk_item_file';
$sql = "CREATE TABLE $table (
  item_file_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  item_id int(10) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  file_title varchar(200) NOT NULL DEFAULT '',
  file_name varchar(127) NOT NULL DEFAULT '',
  file_type varchar(30) NOT NULL DEFAULT '',
  file_size bigint(20) NOT NULL DEFAULT '0',
  post_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  file_tags varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (item_file_id),
  KEY item_id (item_id)
) $charset_collate;";
dbDelta($sql);

	
$table = $wpdb->prefix.'tfk_project';
$sql = "CREATE TABLE $table (
  project_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(120) NOT NULL DEFAULT '',
  description text NOT NULL,
  who_read varchar(64) NOT NULL,
  who_comment varchar(64) NOT NULL,
  who_post varchar(64) NOT NULL,
  who_manage varchar(64) NOT NULL,
  creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  trashed tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (project_id)
) $charset_collate;";
dbDelta($sql);
	
$table = $wpdb->prefix.'tfk_project_user';
$sql = "CREATE TABLE $table (
  project_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  position tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (project_id,user_id)
) $charset_collate;";
dbDelta($sql);

$table = $wpdb->prefix.'tfk_project_status';
$sql = "CREATE TABLE $table (
  project_id mediumint(10) unsigned NOT NULL DEFAULT '0',
  status_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  status_key tinyint(3) unsigned NOT NULL DEFAULT '0',
  user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (project_id,status_date)
) $charset_collate;";
dbDelta($sql);

$table = $wpdb->prefix.'tfk_log';
$sql = "CREATE TABLE $table (
  log_date datetime NOT NULL,
  user_id mediumint(8) unsigned NOT NULL DEFAULT 0,
  action_code varchar(3) NOT NULL DEFAULT '',
  item_id int(10) unsigned NOT NULL DEFAULT 0,
  project_id mediumint(8) unsigned NOT NULL DEFAULT 0,
  comment_id int(10) unsigned NOT NULL DEFAULT 0,
  info varchar(255) NOT NULL DEFAULT '',
  hidden tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (log_date,user_id,action_code),
  KEY project_id (project_id),
  KEY item_id (item_id)
) $charset_collate";
dbDelta($sql);

} // end install

register_deactivation_hook(TFK_ROOT_FILE, 'tfk_deactivate');

/**
 * called when deactivating (not deleting) plugin
 */
function tfk_deactivate() {
	// remove options
	delete_option('tfk_options');
}
