<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

/**
 * creates all tables for the gallery
 * called during register_activation hook
 *
 * @access internal
 * @return void
 */
function nggallery_install($installer)
{
   	global $wpdb;

   	$nggpictures = $wpdb->prefix . 'ngg_pictures';
	$nggallery   = $wpdb->prefix . 'ngg_gallery';
	$nggalbum    = $wpdb->prefix . 'ngg_album';

	// Create pictures table
	$sql = "CREATE TABLE " . $nggpictures . " (
	pid BIGINT(20) NOT NULL AUTO_INCREMENT ,
	image_slug VARCHAR(255) NOT NULL ,
	post_id BIGINT(20) DEFAULT '0' NOT NULL ,
	galleryid BIGINT(20) DEFAULT '0' NOT NULL ,
	filename VARCHAR(255) NOT NULL ,
	description MEDIUMTEXT NULL ,
	alttext MEDIUMTEXT NULL ,
	imagedate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	exclude TINYINT NULL DEFAULT '0' ,
	sortorder BIGINT(20) DEFAULT '0' NOT NULL ,
	meta_data LONGTEXT,
	extras_post_id BIGINT(20) DEFAULT '0' NOT NULL,
	PRIMARY KEY  (pid),
	KEY extras_post_id_key (extras_post_id)
	);";
    $installer->upgrade_schema($sql);

	// Create gallery table
	$sql = "CREATE TABLE " . $nggallery . " (
	gid BIGINT(20) NOT NULL AUTO_INCREMENT ,
	name VARCHAR(255) NOT NULL ,
	slug VARCHAR(255) NOT NULL ,
	path MEDIUMTEXT NULL ,
	title MEDIUMTEXT NULL ,
	galdesc MEDIUMTEXT NULL ,
	pageid BIGINT(20) DEFAULT '0' NOT NULL ,
	previewpic BIGINT(20) DEFAULT '0' NOT NULL ,
	author BIGINT(20) DEFAULT '0' NOT NULL  ,
	extras_post_id BIGINT(20) DEFAULT '0' NOT NULL,
	PRIMARY KEY  (gid),
	KEY extras_post_id_key (extras_post_id)
	)";
    $installer->upgrade_schema($sql);

	// Create albums table
	$sql = "CREATE TABLE " . $nggalbum . " (
	id BIGINT(20) NOT NULL AUTO_INCREMENT ,
	name VARCHAR(255) NOT NULL ,
	slug VARCHAR(255) NOT NULL ,
	previewpic BIGINT(20) DEFAULT '0' NOT NULL ,
	albumdesc MEDIUMTEXT NULL ,
	sortorder LONGTEXT NOT NULL,
	pageid BIGINT(20) DEFAULT '0' NOT NULL,
	extras_post_id BIGINT(20) DEFAULT '0' NOT NULL,
	PRIMARY KEY  (id),
	KEY extras_post_id_key (extras_post_id)
	)";
    $installer->upgrade_schema($sql);

    // check one table again, to be sure
	if( !$wpdb->get_var( "SHOW TABLES LIKE '$nggpictures'" ) ) {
		update_option( "ngg_init_check", __('NextGEN Gallery : Tables could not created, please check your database settings',"nggallery") );
		return;
	}

	// if all is passed , save the DBVERSION
	add_option("ngg_db_version", NGG_DBVERSION);
}

/**
 * Deregister a capability from all classic roles
 *
 * @access internal
 * @param string $capability name of the capability which should be deregister
 * @return void
 */
function ngg_remove_capability($capability){
	// this function remove the $capability only from the classic roles
	$check_order = array("subscriber", "contributor", "author", "editor", "administrator");

	foreach ($check_order as $role) {
		$role = get_role($role);
		if (!is_null($role))
			$role->remove_cap($capability) ;
	}

}