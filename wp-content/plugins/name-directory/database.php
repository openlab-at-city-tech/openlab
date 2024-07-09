<?php
/* Protection! */
if (! function_exists('add_action'))
{
    echo 'Nothing to See here. Move along now people.';
    exit;
}


/**
 * Delta the Directory table (install or update)
 */
function name_directory_db_tables()
{
    global $name_directory_db_version;
    global $name_directory_table_directory;
    global $name_directory_table_directory_name;

    $name_directory_table_queries = array("
        CREATE TABLE $name_directory_table_directory (
            id INT( 11 ) NOT NULL AUTO_INCREMENT,
            name VARCHAR( 255 ) NOT NULL,
            show_title BOOLEAN NULL,
            show_description BOOLEAN NULL DEFAULT 1,
            show_submit_form BOOLEAN NULL,
            show_submitter_name BOOLEAN NULL,
            show_line_between_names BOOLEAN NULL,
            show_character_header BOOLEAN NULL,
            show_search_form BOOLEAN NULL,
            show_all_names_on_index BOOLEAN NULL DEFAULT 1,
            show_all_index_letters BOOLEAN NULL DEFAULT 1,
            show_current_num_names BOOLEAN NULL DEFAULT 1,
            show_index_instructions BOOLEAN NULL DEFAULT 1,
            search_in_description BOOLEAN NULL DEFAULT 0,
            search_highlight BOOLEAN NULL DEFAULT 1,
            jump_to_search_results BOOLEAN NULL DEFAULT 0,
            nr_columns INT( 1 ) NULL,
            nr_most_recent INT(5) NULL DEFAULT 0,
            nr_words_description INT(5) NULL DEFAULT 0,
            description TEXT NULL,
            email_for_submission VARCHAR( 255 ) NULL,
            name_term VARCHAR( 255 ) NULL,
            name_term_singular VARCHAR( 255 ) NULL,
            check_submitted_names_first BOOLEAN NULL DEFAULT 1,
            UNIQUE KEY id (id),
            PRIMARY KEY (id));",
        "CREATE TABLE $name_directory_table_directory_name (
            id INT( 11 ) NOT NULL AUTO_INCREMENT,
            directory INT( 11 ) NOT NULL ,
            name VARCHAR( 255 ) NOT NULL ,
            letter VARCHAR( 1 ) NOT NULL ,
            description TEXT NULL ,
            published BOOL NOT NULL ,
            submitted_by VARCHAR( 255 ) NULL,
            UNIQUE KEY id (id),
            PRIMARY KEY (id));"
    );

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $name_directory_table_queries );

    update_option("name_directory_db_version", $name_directory_db_version);
}


/**
 * This function holds all kinds of maintenance that can be invoked after an update
 */
function name_directory_db_post_update_actions()
{
    global $wpdb;
    global $name_directory_db_version;
    global $name_directory_table_directory;
    global $name_directory_table_directory_name;

    $convert_dirs = "ALTER TABLE $name_directory_table_directory CONVERT TO CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';";
    $wpdb->query($convert_dirs);

    $convert_names = "ALTER TABLE $name_directory_table_directory_name CONVERT TO CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';";
    $wpdb->query($convert_names);

    $convert_opt = "UPDATE $name_directory_table_directory SET `show_all_names_on_index`=1 WHERE `show_all_names_on_index` IS NULL;";
    $wpdb->query($convert_opt);

    $wpdb->show_errors = false;
    $wpdb->suppress_errors = true;
    $make_index = "ALTER TABLE $name_directory_table_directory_name ADD INDEX `dir_idx` (`directory` DESC);";
    $wpdb->query($make_index);
    $make_index2 = "ALTER TABLE $name_directory_table_directory_name ADD INDEX `published_idx` (`published` DESC);";
    $wpdb->query($make_index2);
    $wpdb->suppress_errors = false;

    update_option("name_directory_db_version", $name_directory_db_version);
}


/**
 * Install some sample data on first install
 */
function name_directory_db_install_demo_data()
{
    global $wpdb;
    global $name_directory_table_directory;
    global $name_directory_table_directory_name;

    // Only insert sample data when there is no data
    $wpdb->query(sprintf("SELECT * FROM " . $name_directory_table_directory));
    if($wpdb->num_rows === 0)
    {
        $wpdb->insert($name_directory_table_directory, array(
            'id'                            => 1,
            'name'                          => 'Bird names (demo data)',
            'show_title'                    => 1,
            'show_description'              => 1,
            'show_submit_form'              => 1,
            'show_submitter_name'           => 0,
            'show_character_header'         => 0,
            'show_line_between_names'       => 1,
            'show_search_form'              => 1,
            'search_in_description'         => 1,
            'search_highlight'              => 0,
            'show_all_names_on_index'       => 1,
            'jump_to_search_results'        => 0,
            'nr_most_recent'                => 0,
            'nr_words_description'          => 0,
            'check_submitted_names_first'   => 1,
            'description'                   => 'Cool budgie names',
            'name_term'                     => 'budgies',
            'name_term_singular'            => 'budgie'
        ));
        $wpdb->insert($name_directory_table_directory_name, array(
            'directory'     => 1,
            'name'          => 'Navi',
            'letter'        => 'N',
            'description'   => 'Navi is a good aviator and navigator. A very strong and big budgie, almost English',
            'published'     => 1
        ));
        $wpdb->insert($name_directory_table_directory_name, array(
            'directory'     => 1,
            'name'          => 'Mister',
            'letter'        => 'M',
            'description'   => 'Mister is a name which can only be assigned to a typical English Budgie. Big, strong and stringent.',
            'published'     => 1
        ));
        $wpdb->insert($name_directory_table_directory_name, array(
            'directory'     => 1,
            'name'          => 'Isa',
            'letter'        => 'I',
            'description'   => 'Isa is a direct descent of Mister. As a fullblood daughter she is also a typical English Budgie.',
            'published'     => 1
        ));

        name_directory_db_post_update_actions();
    }
}
