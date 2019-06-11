<?php

/**
 * Initial database configuration on plugin activation
 */
class OPLB_DATABASE
{

    const oplb_gradebook_db_version = 1.86;

    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'oplb_gradebook_upgrade_db'));
    }

    public function oplb_gradebook_upgrade_db()
    {

        if (!get_option('oplb_gradebook_db_version')) {
            $this->database_init();
        }

        if (self::oplb_gradebook_db_version > get_option('oplb_gradebook_db_version')) {
            $this->database_alter();
        }

    }

    public function database_alter()
    {
        /**Any alterations to the table after they have been created in a previous version should take place here.  This works
         * by looping through the necessary db alterations based on the current version of the db. To add an alteration use the following
         * template code block:
         * if(get_option( 'oplb_gradebook_db_version' )==[current_db_version]){
         *    do stuff to tables
         *    update_option( "oplb_gradebook_db_version", self::oplb_gradebook_db_version);
         *  }
         * where the constant oplb_gradebook_db_version should be changed to a larger number.
         */
        global $wpdb;
        if (get_option('oplb_gradebook_db_version') < 1.6) {
            $sql = "ALTER TABLE {$wpdb->prefix}oplb_gradebook_assignments ADD assign_grade_type VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'numeric'";
            $wpdb->query($sql);

            $sql = "ALTER TABLE {$wpdb->prefix}oplb_gradebook_assignments ADD assign_weight int(11) NOT NULL DEFAULT 1";
            $wpdb->query($sql);

            $sql = "ALTER TABLE {$wpdb->prefix}oplb_gradebook_assignments MODIFY assign_weight decimal(7,2)";
            $wpdb->query($sql);

            $sql = "ALTER TABLE {$wpdb->prefix}oplb_gradebook_users ADD current_grade_average decimal(7,2) NOT NULL DEFAULT 0.00";
            $wpdb->query($sql);

            update_option("oplb_gradebook_db_version", 1.6);
        }

        if (get_option('oplb_gradebook_db_version') < 1.83) {

            //some installs may already have this column, so this will check first
            $query = $wpdb->prepare("SHOW COLUMNS FROM {$wpdb->prefix}oplb_gradebook_users LIKE %s", 'mid_semester_grade');
            $check_columns = $wpdb->get_results($query);

            if (empty($check_columns)) {

                $sql = "ALTER TABLE {$wpdb->prefix}oplb_gradebook_users ADD mid_semester_grade VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '--'";
                $wpdb->query($sql);

                $sql = "ALTER TABLE {$wpdb->prefix}oplb_gradebook_users ADD final_grade VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '--'";
                $wpdb->query($sql);

            }

            update_option("oplb_gradebook_db_version", 1.83);
        }

        if (get_option('oplb_gradebook_db_version') < 1.84) {

            //some installs may already have this column, so this will check first
            $query = $wpdb->prepare("SHOW COLUMNS FROM {$wpdb->prefix}oplb_gradebook_cells LIKE %s", 'is_null');
            $check_columns = $wpdb->get_results($query);

            if (empty($check_columns)) {
                $sql = "ALTER TABLE {$wpdb->prefix}oplb_gradebook_cells ADD is_null tinyint unsigned NOT NULL DEFAULT 0";
                $wpdb->query($sql);
            }
            update_option("oplb_gradebook_db_version", 1.84);
        }

        if (get_option('oplb_gradebook_db_version') < 1.85) {
            //comments for cells
            $sql = "ALTER TABLE {$wpdb->prefix}oplb_gradebook_cells ADD comments longtext CHARACTER SET utf8 COLLATE utf8_general_ci";
            $wpdb->query($sql);
            update_option("oplb_gradebook_db_version", 1.85);
        }

        if (get_option('oplb_gradebook_db_version') < 1.86) {
            //comments for cells
            $sql = "ALTER TABLE {$wpdb->prefix}oplb_gradebook_users ADD mid_semester_comments longtext CHARACTER SET utf8 COLLATE utf8_general_ci";
            $wpdb->query($sql);

            $sql = "ALTER TABLE {$wpdb->prefix}oplb_gradebook_users ADD final_comments longtext CHARACTER SET utf8 COLLATE utf8_general_ci";
            $wpdb->query($sql);

            update_option("oplb_gradebook_db_version", 1.86);
        }

    }

    public function database_init()
    {
        global $wpdb;

        $db_name = "{$wpdb->prefix}oplb_gradebook_courses";
        $query = $wpdb->prepare('SHOW TABLES LIKE "%s"', $db_name);

        if ($wpdb->get_var($query) != $db_name) {
            $sql = 'CREATE TABLE ' . $db_name . ' (
			id int(11) NOT NULL AUTO_INCREMENT,
			name MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			school TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			semester TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			year int(11) NOT NULL,
			PRIMARY KEY  (id) )';
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        $db_name = "{$wpdb->prefix}oplb_gradebook_users";
        $query = $wpdb->prepare('SHOW TABLES LIKE "%s"', $db_name);

        if ($wpdb->get_var($query) != $db_name) {
            $sql = 'CREATE TABLE ' . $db_name . ' (
			id int(11) NOT NULL AUTO_INCREMENT,
			uid int(11) NOT NULL,
			gbid int(11) NOT NULL,
			role VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "student",
                        current_grade_average decimal(7,2) NOT NULL DEFAULT 0.00,
            mid_semester_grade VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "--",
            final_grade VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "--",
			PRIMARY KEY  (id)  )';
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        //The column headings that should be in the oplb_assignments table are stored in $table_columns
        $table_columns = array('id', 'gbid', 'assign_order', 'assign_name', 'assign_category', 'assign_visibility', 'assign_date', 'assign_due');
        $table_columns_specs = array(
            'id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'gbid' => 'int(11) NOT NULL',
            'assign_order' => 'int(11) NOT NULL',
            'assign_name' => 'mediumtext NOT NULL',
            'assign_category' => 'mediumtext NOT NULL',
            'assign_visibility' => 'VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "Students"',
            'assign_date' => 'DATE NOT NULL DEFAULT "0000-00-00"',
            'assign_due' => 'DATE NOT NULL DEFAULT "0000-00-00"',
            'assign_grade_type' => 'VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "numeric"',
            'assign_weight' => 'decimal(7,2) NOT NULL DEFAULT 1.00',
        );

        $db_name2 = "{$wpdb->prefix}oplb_gradebook_assignments";
        $query = $wpdb->prepare('SHOW TABLES LIKE "%s"', $db_name2);

        if ($wpdb->get_var($query) != $db_name2) {
            $sql = 'CREATE TABLE ' . $db_name2 . ' (
			id int(11) NOT NULL AUTO_INCREMENT,
			gbid int(11) NOT NULL,
			assign_order int(11) NOT NULL,		
			assign_name mediumtext NOT NULL,
			assign_category mediumtext NOT NULL,			
			assign_visibility VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "Students",
			assign_date DATE NOT NULL DEFAULT "0000-00-00",
			assign_due DATE NOT NULL DEFAULT "0000-00-00",
                        assign_grade_type VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "numeric",
                        assign_weight decimal(7,2) NOT NULL DEFAULT 1.00,
			PRIMARY KEY  (id) )';
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        } else {
            //Otherwise, check if there is something to upgrade in oplb_gradebook_assignments table		
            //anfixme: this needs to move to the database_alter
            $query = $wpdb->prepare("SELECT column_name FROM information_schema.columns
				WHERE table_name = %s ORDER BY ordinal_position", $db_name2);
            $oplb_assignments_columns = $wpdb->get_col($query);
            $missing_columns = array_diff($table_columns, $oplb_assignments_columns);
            if (count($missing_columns)) {
                //add missing columns
                $sql = "ALTER TABLE $db_name2";
                foreach ($missing_columns as $missing_column) {
                    $sql = $sql . 'ADD ' . $missing_column . ' ' . $table_columns_specs[$missing_column] . ', ';
                }
                $sql = $wpdb->prepare(rtrim(trim($sql), ','), '');
                $wpdb->query($sql);
            }
        }

        $db_name3 = "{$wpdb->prefix}oplb_gradebook_cells";
        $query = $wpdb->prepare('SHOW TABLES LIKE "%s"', $db_name3);

        if ($wpdb->get_var($query) != $db_name3) {
            $sql = 'CREATE TABLE ' . $db_name3 . ' (
			id int(11) NOT NULL AUTO_INCREMENT,
			uid int(11) NOT NULL,
			gbid int(11) NOT NULL,
    	    amid int(11) NOT NULL,
	        assign_order int(11) NOT NULL,
            assign_points_earned decimal(7,2) NOT NULL,
            is_null tinyint unsigned NOT NULL DEFAULT 0,
			PRIMARY KEY  (id) )';
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        update_option("oplb_gradebook_settings", array(
            'administrator' => true,
            'editor' => false,
            'contributor' => false,
            'author' => false,
            'subscriber' => false
        ));
        update_option("oplb_gradebook_db_version", self::oplb_gradebook_db_version);
    }

}
