<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// INSTALL -----------------------------------------------------------------------------------------

    function Zotpress_install( $taskType="install" )
    {
        global $wpdb;
        global $current_user;

        $Zotpress_main_db_version = "5.2";
        $Zotpress_oauth_db_version = "5.0.5";
        $Zotpress_zoteroItemImages_db_version = "5.2.6";
		$Zotpress_cache_version = "7.1.4";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


        // +----------------------------------+
        // | Remove old databases and options |
        // +----------------------------------+

        // CHANGED (7.3): We don't want to delete everything when updating,
        // so a task type (install or update) check was added.

        if ( $taskType == "install" )
        {
            // kfeuerherm: added next two lines 15 November 2019
            $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress;");
            $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_oauth;");

            $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroItems;");
            $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroCollections;");
            $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroTags;");
            $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroRelItemColl;");
            $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroRelItemTags;");

            // kfeuerherm: added next two lines 15 November 2019
            $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_cache;");
            $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroItemImages;");

            // kfeuerherm: added next nine lines 15 November 2019
            delete_option( 'Zotpress_cache_version' );
            delete_option( 'Zotpress_DefaultCPT' );
            delete_option( 'Zotpress_DefaultAccount' );
            delete_option( 'Zotpress_DefaultEditor' );
            delete_option( 'Zotpress_DefaultStyle' );
            delete_option( 'Zotpress_StyleList' );
            delete_option( 'Zotpress_update_version' );
            delete_option( 'Zotpress_main_db_version' );
            delete_option( 'Zotpress_oauth_db_version' );

            delete_option( 'Zotpress_zoteroItems_db_version' );
            delete_option( 'Zotpress_zoteroCollections_db_version' );
            delete_option( 'Zotpress_zoteroTags_db_version' );
            delete_option( 'Zotpress_zoteroRelItemColl_db_version' );
            delete_option( 'Zotpress_zoteroRelItemTags_db_version' );

            // kfeuerherm: added next three lines 15 November 2019
            delete_option( 'Zotpress_zoteroItemImages_db_version' );
            delete_option( 'Zotpress_update_notice_dismissed' );
            delete_option( 'Zotpress_zoteroItemImages_db_version' );

            // kfeuerherm: next two are in the reset code; not sure needed yet
            delete_user_meta( $current_user->ID, 'zotpress_5_2_ignore_notice' );
            delete_user_meta( $current_user->ID, 'zotpress_survey_notice_ignore' );

        } // taskType check


        // +-----------------------+
        // | Zotero accounts table |
        // +-----------------------+

		// For each table, the basic check is:
		// - If the table version option doesn't exist, OR
		// - If the table version is not the same as the update version (vars defined above)
		// Then add/update the table and add/update the option

        if ( ! get_option("Zotpress_main_db_version")
                || get_option("Zotpress_main_db_version") != $Zotpress_main_db_version
        )
        {
			$table_name = $wpdb->prefix . "zotpress";

            $structure = "CREATE TABLE $table_name (
                id INT(9) NOT NULL AUTO_INCREMENT,
                account_type VARCHAR(10) NOT NULL,
                api_user_id VARCHAR(10) NOT NULL,
                public_key VARCHAR(28) default NULL,
                nickname VARCHAR(200) default NULL,
                version VARCHAR(10) default '5.1',
                UNIQUE KEY id (id)
            );";

            dbDelta($structure);

            update_option("Zotpress_main_db_version", $Zotpress_main_db_version);
        }


        // +-------------+
        // | OAuth table |
        // +-------------+

        if ( ! get_option("Zotpress_oauth_db_version")
                || get_option("Zotpress_oauth_db_version") != $Zotpress_oauth_db_version
        )
        {
			$table_name = $wpdb->prefix . "zotpress_oauth";

            $structure = "CREATE TABLE $table_name (
                id INT(9) NOT NULL AUTO_INCREMENT,
                cache LONGTEXT NOT NULL,
                UNIQUE KEY id (id)
            );";

            dbDelta($structure);

            update_option("Zotpress_oauth_db_version", $Zotpress_oauth_db_version);

            // Initial populate
            if ($wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."zotpress_oauth;") == 0)
                $wpdb->query("INSERT INTO ".$wpdb->prefix."zotpress_oauth (cache) VALUES ('empty')");
        }


        // +--------------+
        // | Images table |
        // +--------------+

        if ( ! get_option("Zotpress_zoteroItemImages_db_version")
                || get_option("Zotpress_zoteroItemImages_db_version") != $Zotpress_zoteroItemImages_db_version
        )
        {
			$table_name = $wpdb->prefix . "zotpress_zoteroItemImages";

            $structure = "CREATE TABLE $table_name (
                id INT(9) AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                item_key VARCHAR(50),
                image TEXT,
                UNIQUE KEY id (id),
                PRIMARY KEY (api_user_id, item_key)
            );";

            dbDelta( $structure );

            update_option( "Zotpress_zoteroItemImages_db_version", $Zotpress_zoteroItemImages_db_version );
        }


        // +-------------+
        // | Cache table |
        // +-------------+

        if ( ! get_option("Zotpress_cache_version")
				|| get_option("Zotpress_cache_version") != $Zotpress_cache_version
        )
        {
            $structure = "CREATE TABLE ".$wpdb->prefix."zotpress_cache (
                id INT(9) NOT NULL AUTO_INCREMENT,
				request_id VARCHAR(200) NOT NULL,
                api_user_id VARCHAR(50),
                json BLOB,
                tags BLOB,
                headers MEDIUMTEXT,
                libver INT(9),
                retrieved VARCHAR(100),
                UNIQUE KEY id (id),
				PRIMARY KEY (request_id)
            );";

            dbDelta($structure);

            update_option("Zotpress_cache_version", $Zotpress_cache_version);
        }

	}
    register_activation_hook( ZOTPRESS_PLUGIN_FILE, 'Zotpress_install' );

// INSTALL -----------------------------------------------------------------------------------------


// UNINSTALL --------------------------------------------------------------------------------------

    function Zotpress_uninstall()
    {
        global $wpdb;
        global $current_user;

        // Drop all tables -- originally not including accounts/main, but not sure why
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_oauth;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroItems;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroItemImages;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroCollections;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroTags;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroRelItemColl;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroRelItemTags;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_cache;");

        // Delete options
        delete_option( 'Zotpress_DefaultCPT' );
        delete_option( 'Zotpress_DefaultAccount' );
        delete_option( 'Zotpress_DefaultStyle' );
        delete_option( 'Zotpress_StyleList' );
        delete_option( 'Zotpress_update_version' );
        delete_option( 'Zotpress_main_db_version' );
        delete_option( 'Zotpress_oauth_db_version' );
        delete_option( 'Zotpress_zoteroItems_db_version' );
        delete_option( 'Zotpress_zoteroCollections_db_version' );
        delete_option( 'Zotpress_zoteroTags_db_version' );
        delete_option( 'Zotpress_zoteroRelItemColl_db_version' );
        delete_option( 'Zotpress_zoteroRelItemTags_db_version' );
		delete_option( 'Zotpress_zoteroItemImages_db_version' );
		delete_option( 'Zotpress_cache_version' );
		delete_option( 'Zotpress_update_notice_dismissed' );

        // Delete user meta
        delete_user_meta( $current_user->ID, 'zotpress_5_2_ignore_notice' );
        delete_user_meta( $current_user->ID, 'zotpress_survey_notice_ignore' );
    }

    register_uninstall_hook( ZOTPRESS_PLUGIN_FILE, 'Zotpress_uninstall' );

// UNINSTALL ---------------------------------------------------------------------------------------


// UPDATE ------------------------------------------------------------------------------------------


	/**
	 *
	 * If update check option doesn't exist, OR
	 * If it exists but it's not the same version as the database update version
	 *
	 * Then, run the install, which installs or updates the databases
	 *
	**/
    if ( ! get_option( "Zotpress_update_version" )
			|| get_option("Zotpress_update_version") != $GLOBALS['Zotpress_update_db_by_version'] )
    {
        Zotpress_install("update");

        // Add or update version number
        if ( !get_option( "Zotpress_update_version" ) )
            add_option( "Zotpress_update_version", $GLOBALS['Zotpress_update_db_by_version'], "", "no" );
        else
            update_option( "Zotpress_update_version", $GLOBALS['Zotpress_update_db_by_version'] );
    }

// UPDATE ------------------------------------------------------------------------------------------


?>
