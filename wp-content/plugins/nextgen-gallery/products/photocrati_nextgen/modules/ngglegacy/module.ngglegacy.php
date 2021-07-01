<?php

define(
	'NGG_LEGACY_MOD_DIR',
    implode(DIRECTORY_SEPARATOR, array(
        rtrim(NGG_MODULE_DIR, "/\\"),
        basename(dirname(__FILE__))
    ))
);

class M_NggLegacy extends C_Base_Module
{
	function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
	{
		parent::define(
			'photocrati-nextgen-legacy',
			'NextGEN Legacy',
			'Embeds the original version of NextGEN 1.9.3 by Alex Rabe',
			'3.7.1',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);

		C_Photocrati_Installer::add_handler($this->module_id, 'C_NggLegacy_Installer');
	}

	function initialize()
	{
		parent::initialize();
        include_once(implode(DIRECTORY_SEPARATOR, array(
            dirname(__FILE__),
            'nggallery.php'
        )));
	}

	function get_type_list()
	{
		return array(
			'C_NggLegacy_Installer' => 'class.ngglegacy_installer.php'
		);
	}
}

class C_NggLegacy_Installer
{
	function install()
	{
		global $wpdb;
		include_once('admin/install.php');

		$this->remove_transients();

		if (is_multisite()) {
			$network=isset($_SERVER['SCRIPT_NAME'])?$_SERVER['SCRIPT_NAME']:"";
			$activate=isset($_GET['action'])?$_GET['action']:"";
			$isNetwork=($network=='/wp-admin/network/plugins.php')?true:false;
			$isActivation=($activate=='deactivate')?false:true;

			if ($isNetwork and $isActivation){
				$old_blog = $wpdb->blogid;
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs", NULL));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					nggallery_install($this);
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		// remove the update message
		delete_option( 'ngg_update_exists' );
		nggallery_install($this);
	}

	function uninstall($hard=FALSE)
	{
		include_once('admin/install.php');
		if ($hard) {
            delete_option('ngg_init_check');
            delete_option('ngg_update_exists');
            delete_option( 'ngg_options' );
            delete_option( 'ngg_db_version' );
            delete_option( 'ngg_update_exists' );
            delete_option( 'ngg_next_update' );
        }

		// now remove the capability
        ngg_remove_capability("NextGEN Attach Interface");
        ngg_remove_capability("NextGEN Change options");
        ngg_remove_capability("NextGEN Change style");
        ngg_remove_capability("NextGEN Edit album");
        ngg_remove_capability("NextGEN Gallery overview");
        ngg_remove_capability("NextGEN Manage gallery");
        ngg_remove_capability("NextGEN Upload images");
        ngg_remove_capability("NextGEN Use TinyMCE");
        ngg_remove_capability('NextGEN Manage others gallery');
        ngg_remove_capability('NextGEN Manage tags');

		$this->remove_transients();
	}

	function remove_transients()
	{
		global $wpdb, $_wp_using_ext_object_cache;

		// Fetch all transients
		$query = "
					SELECT option_name FROM {$wpdb->options}
					WHERE option_name LIKE '%ngg_request%'
				";
		$transient_names = $wpdb->get_col($query);;

		// Delete all transients in the database
		$query = "
					DELETE FROM {$wpdb->options}
					WHERE option_name LIKE '%ngg_request%'
				";
		$wpdb->query($query);

		// If using an external caching mechanism, delete the cached items
		if ($_wp_using_ext_object_cache) {
			foreach ($transient_names as $transient) {
				wp_cache_delete($transient, 'transient');
				wp_cache_delete(substr($transient, 11), 'transient');
			}
		}
	}

	function upgrade_schema($sql)
	{
		global $wpdb;

		// upgrade function changed in WordPress 2.3
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// add charset & collate like wp core
		$charset_collate = '';

		if ( version_compare($wpdb->get_var("SELECT VERSION() AS `mysql_version`"), '4.1.0', '>=') ) {
			if ( ! empty($wpdb->charset) )
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( ! empty($wpdb->collate) )
				$charset_collate .= " COLLATE $wpdb->collate";
		}

		// Add charset to table creation query
		$sql = str_replace($charset_collate, '', str_replace(';', '', $sql));

		// Execute the query
		return dbDelta($sql. ' '. $charset_collate. ';');
	}
}

new M_NggLegacy();