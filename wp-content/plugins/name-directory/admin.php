<?php

add_action('admin_menu', 'name_directory_register_menu_entry');
add_action('admin_enqueue_scripts', 'name_directory_admin_add_resources');
add_action('wp_ajax_name_directory_ajax_names', 'name_directory_names');
add_action('wp_ajax_name_directory_switch_name_published_status', 'name_directory_ajax_switch_name_published_status');


/**
 * Add a menu entry on options
 */
function name_directory_register_menu_entry()
{
    foreach( name_directory_get_capabilities() as $capability)
    {
        if( current_user_can( $capability ) )
        {
            add_menu_page(
                __('Name Directory', 'name-directory'),
                __('Name Directory', 'name-directory'),
                $capability,
                'name-directory',
                'name_directory_options',
                'dashicons-index-card',
                120);

            add_submenu_page(
                'name-directory',
                __('Add directory', 'name-directory'),
                __('Add directory', 'name-directory'),
                $capability,
                'admin.php?page=name-directory&sub=new-directory');

            add_submenu_page(
                'name-directory',
                __('Quick import into new directory', 'name-directory'),
                __('Quick import', 'name-directory'),
                $capability,
                'admin.php?page=name-directory&sub=quick-import');

            break;
        }
    }
}


/**
 * This is a little router for the
 * name-directory plugin
 */
function name_directory_options()
{
    if ( ! name_directory_is_control_allowed() )
    {
        wp_die( __('You do not have sufficient permissions to access this page.', 'name-directory') );
    }

    $sub_page = '';
    if( ! empty( $_GET['sub'] ) )
    {
        $sub_page = $_GET['sub'];
    }

    switch( $sub_page )
    {
        case 'manage-directory':
            name_directory_names();
            break;
        case 'edit-directory':
            name_directory_edit();
            break;
        case 'new-directory':
            name_directory_edit('new');
            break;
        case 'quick-import':
            name_directory_quick_import();
            break;
        case 'import':
            name_directory_import();
            break;
        case 'export':
            name_directory_export();
            break;
        default:
            name_directory_show_list();
            break;
    }

}


/**
 * Show the list of directories and all of the
 * links to manage the directories
 */
function name_directory_show_list()
{
    global $wpdb;
    global $name_directory_table_directory;
    global $name_directory_table_directory_name;

    if(! empty( $_GET['delete_dir'] ) && is_numeric( $_GET['delete_dir'] ) && check_admin_referer('name-directory-action','secnonce') )
    {
        $name = $wpdb->get_var(sprintf("SELECT `name` FROM %s WHERE id=%d", $name_directory_table_directory, $_GET['delete_dir']));
        $wpdb->delete($name_directory_table_directory, array('id' => $_GET['delete_dir']), array('%d'));
        $wpdb->delete($name_directory_table_directory_name, array('directory' => $_GET['delete_dir']), array('%d'));
        echo "<div class='updated'><p><strong>"
            . sprintf(__('Name directory %s and all entries deleted', 'name-directory'), "<i>" . $name . "</i>")
            . "</strong></p></div>";
    }

    $wp_file = admin_url('admin.php');
    $wp_page = $_GET['page'];
    $wp_url_path = sprintf("%s?page=%s", $wp_file, $wp_page);
    $wp_new_url = sprintf("%s&sub=%s", $wp_url_path, 'new-directory');
    $wp_nonce = wp_create_nonce('name-directory-action');

    echo '<div class="wrap">';
    echo "<h2>"
        . __('Name Directory management', 'name-directory')
        . " <a href='" . $wp_new_url . "' class='add-new-h2'>" . __('Add directory', 'name-directory') . "</a>"
        . "</h2>";

    if(! empty($_POST['mode']) && ! empty($_POST['dir_id']) && check_admin_referer( 'name_directory_dirmanagement','name_directory_adminnonce' ))
    {
        $wpdb->update(
            $name_directory_table_directory,
            array(
                'name'                          => sanitize_text_field($_POST['name']),
                'description'                   => sanitize_text_field($_POST['description']),
                'show_title'                    => (int)$_POST['show_title'],
                'show_description'              => (int)$_POST['show_description'],
                'show_submit_form'              => (int)$_POST['show_submit_form'],
                'show_search_form'              => (int)$_POST['show_search_form'],
                'search_in_description'         => (int)$_POST['search_in_description'],
                'search_highlight'              => (int)$_POST['search_highlight'],
                'show_submitter_name'           => (int)$_POST['show_submitter_name'],
                'show_current_num_names'        => (int)$_POST['show_current_num_names'],
                'show_index_instructions'       => (int)$_POST['show_index_instructions'],
                'show_line_between_names'       => (int)$_POST['show_line_between_names'],
                'show_character_header'         => (int)$_POST['show_character_header'],
                'show_all_names_on_index'       => (int)$_POST['show_all_names_on_index'],
                'show_all_index_letters'        => (int)$_POST['show_all_index_letters'],
                'jump_to_search_results'        => (int)$_POST['jump_to_search_results'],
                'nr_columns'                    => (int)$_POST['nr_columns'],
                'nr_most_recent'                => intval($_POST['nr_most_recent']),
                'nr_words_description'          => intval($_POST['nr_words_description']),
                'email_for_submission'          => sanitize_email($_POST['email_for_submission']),
                'name_term'                     => sanitize_text_field($_POST['name_term']),
                'name_term_singular'            => sanitize_text_field($_POST['name_term_singular']),
                'check_submitted_names_first'   => intval($_POST['check_submitted_names_first']),
            ),
            array('id' => intval($_POST['dir_id']))
        );

        echo "<div class='updated'><p>"
            . sprintf(__('Directory %s updated.', 'name-directory'), "<i>" . sanitize_text_field($_POST['name']) . "</i>")
            . "</p></div>";

        unset($_GET['dir_id']);
    }
    elseif(! empty($_POST['mode']) && $_POST['mode'] == "new" && check_admin_referer( 'name_directory_dirmanagement','name_directory_adminnonce' ))
    {
        $cleaned_name = sanitize_text_field($_POST['name']);

        if( empty($cleaned_name))
        {
            echo "<div class='error'><p><strong>" . __('Please fill in at least a (valid) name to create new name directory', 'name-directory') . "</strong></p>";
            echo "<a href='#' onclick='javascript:history.back();'>" . __('Click here to go back and try again', 'name-directory') . "</a></div>";
        }
        else
        {
            $wpdb->insert(
                $name_directory_table_directory,
                array(
                    'name'                          => $cleaned_name,
                    'description'                   => sanitize_text_field($_POST['description']),
                    'show_title'                    => (int)$_POST['show_title'],
                    'show_description'              => (int)$_POST['show_description'],
                    'show_submit_form'              => (int)$_POST['show_submit_form'],
                    'show_search_form'              => (int)$_POST['show_search_form'],
                    'search_in_description'         => (int)$_POST['search_in_description'],
                    'search_highlight'              => (int)$_POST['search_highlight'],
                    'show_submitter_name'           => (int)$_POST['show_submitter_name'],
                    'show_line_between_names'       => (int)$_POST['show_line_between_names'],
                    'show_character_header'         => (int)$_POST['show_character_header'],
                    'show_all_names_on_index'       => (int)$_POST['show_all_names_on_index'],
                    'show_all_index_letters'        => (int)$_POST['show_all_index_letters'],
                    'show_current_num_names'        => (int)$_POST['show_current_num_names'],
                    'show_index_instructions'       => (int)$_POST['show_index_instructions'],
                    'jump_to_search_results'        => (int)$_POST['jump_to_search_results'],
                    'nr_columns'                    => (int)$_POST['nr_columns'],
                    'nr_most_recent'                => intval($_POST['nr_most_recent']),
                    'nr_words_description'          => intval($_POST['nr_words_description']),
                    'email_for_submission'          => sanitize_text_field($_POST['email_for_submission']),
                    'name_term'                     => sanitize_text_field($_POST['name_term']),
                    'name_term_singular'            => sanitize_text_field($_POST['name_term_singular']),
                    'check_submitted_names_first'   => (int)$_POST['check_submitted_names_first'],
                ),
                array('%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%d')
            );

            echo "<div class='updated'><p>"
                . sprintf(__('Directory %s created.', 'name-directory'), "<i>" . $cleaned_name . "</i>")
                . "</p></div>";
        }
    }

    $directories = $wpdb->get_results(sprintf("
        SELECT nd.* 
        FROM `%s` nd 
        LEFT JOIN `%s` ndn ON nd.id = ndn.directory 
        GROUP by nd.id
        ORDER BY MAX(ndn.id) IS NULL DESC, MAX(ndn.id) DESC", $name_directory_table_directory, $name_directory_table_directory_name));
    $num_directories = $wpdb->num_rows;
    $plural = ($num_directories==1)?__('name directory', 'name-directory'):__('name directories', 'name-directory');

    echo "<p>"
        . sprintf(__('You currently have %d %s.', 'name-directory'), $num_directories, $plural)
        . "</p>";
    ?>

    <table class="wp-list-table widefat fixed table-view-list name-directory" cellspacing="0">
        <thead><?php name_directory_render_admin_overview_table_headerfooter(); ?></thead>

        <tbody>
        <?php

        $alternate = "";
        foreach ( $directories as $directory )
        {
            $description = substr($directory->description, 0, 70);
            if(strlen($description) == '70')
            {
                $description .= "...";
            }

            $alternate = ( $alternate == "alternate" ) ? "" : "alternate";

            $entries = $wpdb->get_var(sprintf("SELECT COUNT(`id`) FROM %s WHERE directory=%d", $name_directory_table_directory_name, $directory->id));
            $unpublished = $wpdb->get_var(sprintf("SELECT COUNT(`id`) FROM %s WHERE directory=%d AND `published` = 0", $name_directory_table_directory_name, $directory->id));
            echo sprintf("
                <tr class='type-page status-publish hentry " . $alternate . " iedit author-self' valign='top'>
                    <th scope='row'>&nbsp;</th>
                    <td class='post-title page-title column-title title column-title has-row-actions column-primary'>
                        <strong><a class='row-title' href='" . $wp_url_path . "&sub=manage-directory&dir=%d' title='%s'>%s</a>
                            <span>&nbsp;%s</span></strong>
                        <div class='locked-info'>&nbsp;</div>
                        <div class='row-actions'>
                               <span class='manage'><a href='" . $wp_url_path . "&sub=manage-directory&dir=%d' title='%s'>%s</a>
                             | </span><span><a href='" . $wp_url_path . "&sub=manage-directory&dir=%d&display_all_names=no#anchor_add_form' title='%s'>%s</a>
                             | </span><span><a href='" . $wp_url_path . "&sub=edit-directory&dir=%d' title='%s'>%s</a>
                             | </span><span><a href='" . $wp_url_path . "&sub=import&dir=%d' title='%s'>%s</a>
                             | </span><span><a href='" . $wp_url_path . "&sub=export&dir=%d' title='%s'>%s</a>
                             | </span><span class='view'><a class='toggle-info' data-id='%s' href='" . $wp_url_path . "&sub=manage-directory&dir=%d#shortcode' title='%s'>%s</a></span>
                             | </span><span class='trash'><a class='namedirectory_confirmdelete submitdelete' href='" . $wp_url_path . "&delete_dir=%d&secnonce=%s' title=%s'>%s</a>
                        </div>
                    </td>
                    <td>
                        &nbsp; <strong title='%s'>%d</strong>
                        <br /><br />&nbsp;
                    </td>
                    <td>%d</td>
                    <td>%d</td>
                    </tr>",

                $directory->id, $directory->name, $directory->name,
                $description,
                $directory->id, __('Add, edit and remove names', 'name-directory'), __('Manage names', 'name-directory'),
                $directory->id, __('Go to the add-name-form on the Manage page', 'name-directory'), __('Add name', 'name-directory'),
                $directory->id, __('Edit name, description and appearance settings', 'name-directory'), __('Settings', 'name-directory'),
                $directory->id, __('Import entries for this directory by uploading a .csv file', 'name-directory'), __('Import', 'name-directory'),
                $directory->id, __('Download the contents of this directory as a .csv file', 'name-directory'), __('Export', 'name-directory'),
                $directory->id, $directory->id, __('Show the copy-paste shortcode for this directory', 'name-directory'), __('Shortcode', 'name-directory'),
                $directory->id, $wp_nonce, __('Permanently remove this name directory', 'name-directory'), __('Delete', 'name-directory'),

                __('Number of names in this directory', 'name-directory'),
                $entries,
                ($entries - $unpublished),
                $unpublished
            );
            echo sprintf("
                    <tr id='embed_code_%s' class='name_directory_embed_code'>
                        <td>&nbsp;</td>
                        <td align='right'>%s</td>
                        <td colspan='3'>
                            <input value='[namedirectory dir=\"%s\"]' type='text' size='25' />
                        </td>
                    </tr>",
                $directory->id,
                __('To show your directory on your website, use the shortcode on the right.', 'name-directory') . '<br />' .
                __('Copy the code and paste it in a post or in a page.', 'name-directory') . '<br /><small>' .
                __('If you want to start with a specific character, like "J", use [namedirectory dir="X" start_with="j"].', 'name-directory') . '</small>',
                $directory->id);
        }
        ?>
        </tbody>

        <tfoot><?php name_directory_render_admin_overview_table_headerfooter(); ?></tfoot>
    </table>
    <?php
}


/**
 * A double purpose function for editing a name-directory and
 * creating a new directory.
 * @param string $mode
 */
function name_directory_edit($mode = 'edit')
{
    if( ! name_directory_is_control_allowed() )
    {
        wp_die( __('You do not have sufficient permissions to access this page.', 'name-directory') );
    }

    global $wpdb;
    global $name_directory_table_directory;

    $wp_file = admin_url('admin.php');
    $wp_page = $_GET['page'];
    $wp_sub  = $_GET['sub'];
    $overview_url = sprintf("%s?page=%s", $wp_file, $wp_page, $wp_sub);
    $wp_url_path = sprintf("%s?page=%s", $wp_file, $wp_page);

    $directory_id = 0;
    if(! empty($_GET['dir']))
    {
        $directory_id = intval($_GET['dir']);
    }
    $directory = $wpdb->get_row("SELECT * FROM " . $name_directory_table_directory . " WHERE `id` = " . $directory_id, ARRAY_A);

    echo '<div class="wrap">';
    if($mode == "new")
    {
        $table_heading  = __('Create new name directory', 'name-directory');
        $button_text    = __('Create', 'name-directory');
        echo "<h2>" . __('Create new name directory', 'name-directory') . "</h2>";
        echo "<p>" . __('Complete the form below to create a new name directory.', 'name-directory');
    }
    else
    {
        $table_heading  = __('Edit this directory', 'name-directory');
        $button_text    = __('Save Changes', 'name-directory');
        echo "<h2>" . __('Edit name directory', 'name-directory') . "</h2>";
        echo "<p>"
            . sprintf(__('You are editing the name, description and settings of directory %s', 'name-directory'),
                $directory['name']);
    }
    echo " <a style='float: right;' href='" . $overview_url . "'>" . __('Back to the directory overview', 'name-directory') . "</a></p>";
    ?>

    <form name="add_name" method="post" action="<?php echo $wp_url_path; ?>">
        <table class="wp-list-table striped widefat" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="2">
                        <?php echo $table_heading; ?>
                        <input type="hidden" name="dir_id" value="<?php echo $directory_id; ?>">
                        <input type="hidden" name="mode" value="<?php echo $mode; ?>">
                    </th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <th valign="top" width="39%"><?php echo __('Title', 'name-directory'); ?></th>
                <td width="60%"><input type="text" name="name" autocomplete="off" value="<?php echo (! empty($directory['name']) ? esc_html($directory['name']) : ''); ?>" size="20" class="name_directory_widest"></td>
            </tr>
            <tr>
                <th valign="top"><?php echo __('Description', 'name-directory'); ?><br><small><?php echo __('Just for your own administration, it does not show on the front-end', 'name-directory'); ?></small></th>
                <td><textarea name="description" rows="5"  class="name_directory_widest"><?php echo (! empty($directory['description']) ? esc_textarea($directory['description']) : ''); ?></textarea></td>
            </tr>

            <?php
            $appearance_settings = array(
                'show_title' => array(
                    'friendly_name' => __('Show title', 'name-directory'),
                    'description' => __('Displays the title in a h3-heading', 'name-directory'),
                ),
                'show_description' => array(
                    'friendly_name' => __('Show description', 'name-directory'),
                    'description' => __('This is the description of the names on the front-end', 'name-directory'),
                ),
                'show_line_between_names' => array(
                    'friendly_name' => __('Show line between names', 'name-directory'),
                    'description' => false,
                ),
                'show_character_header' => array(
                    'friendly_name' => __('Show new character heading', 'name-directory'),
                    'description' => __('Show a B-heading after all words starting with A, which will that there is a new starting letter', 'name-directory'),
                ),
                'show_all_names_on_index' => array(
                    'friendly_name' => __('Show all names by default', 'name-directory'),
                    'description' => __('If no, user HAS to use the index before entries are shown', 'name-directory'),
                ),
            );
            name_directory_render_admin_directory_settings_section($directory, __('Appearance', 'name-directory'), $appearance_settings);
            $appearance_settings_options = array(
                    'nr_most_recent' => array(
                    'friendly_name' => __('Show most recent names', 'name-directory'),
                    'description' => __('If No, frontend will not show \'Latest\' option.', 'name-directory'),
                    'options' => array(0 => __('No', 'name-directory'), 3 => 3, 5 => 5, 10 => 10, 25 => 25, 50 => 50, 100 => 100)
                ),
                'nr_words_description' => array(
                    'friendly_name' => __('Limit amount of words in description', 'name-directory'),
                    'description' => __('Display a "read-more" link on the website if the description exceeds X characters.', 'name-directory'),
                    'options' => array(0 => __('No', 'name-directory'), 10 => 10, 20 => 20, 25 => 25, 50 => 50, 100 => 100)
                ),
                'nr_columns' => array(
                    'friendly_name' => __('Number of columns', 'name-directory'),
                    'description' => __('The number of (vertical) columns to display the names in', 'name-directory'),
                    'options' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4)
                )
            );
            foreach($appearance_settings_options as $setting_name => $setting_props)
            {
                name_directory_render_admin_setting_options($directory, $setting_name, $setting_props['friendly_name'], $setting_props['description'], $setting_props['options']);
            }


            $index_settings = array(
                'show_all_index_letters' => array(
                    'friendly_name' => __('Show all letters on index', 'name-directory'),
                    'description' => __('If no, just A B D E are shown if there are no entries starting with C', 'name-directory'),
                ),
                'show_current_num_names' => array(
                    'friendly_name' => __('Show current amount of names', 'name-directory'),
                    'description' => '',
                ),
                'show_index_instructions' => array(
                    'friendly_name' => __('Show instructions', 'name-directory'),
                    'description' => __('Show this text below the index characters', 'name-directory') . ': ' .__('Please select a letter from the index (above) to see entries', 'name-directory'),
                ),
            );
            name_directory_render_admin_directory_settings_section($directory, __('Index / Character bar', 'name-directory'), $index_settings);


            $search_options = array(
                'show_search_form' => array(
                    'friendly_name' => __('Show search form', 'name-directory'),
                    'description' => false,
                ),
                'search_in_description' => array(
                    'friendly_name' => __('Search in description', 'name-directory'),
                    'description' => __('If yes, searches will be performed in the name and the description. If no, it will search in the names only', 'name-directory'),
                ),
                'jump_to_search_results' => array(
                    'friendly_name' => __('Jump to Name Directory when searching', 'name-directory'),
                    'description' => __('On the front-end, jump to the Name Directory search box. Particularly useful if you have Name Directory on a long page or onepage websites', 'name-directory'),
                ),
                'search_highlight' => array(
                    'friendly_name' => __('Highlight search term', 'name-directory'),
                    'description' => __('If yes, the search term will be highlighted on the page so users can spot them easier', 'name-directory'),
                )
            );
            name_directory_render_admin_directory_settings_section($directory, __('Search', 'name-directory'), $search_options);


            $submission_settings = array(
                'show_submit_form' => array(
                    'friendly_name' => __('Submit form', 'name-directory'),
                    'description' => __('Visitors can submit suggestions', 'name-directory'),
                ),
                'show_submitter_name' => array(
                    'friendly_name' => __('Submitter name', 'name-directory'),
                    'description' => __('Show the name of the submitter', 'name-directory'),
                ),
                'check_submitted_names_first' => array(
                    'friendly_name' => __('Check submitted names', 'name-directory'),
                    'description' => __('Whether a submitted name has to be checked/approved by an admin before it will be visible', 'name-directory'),
                ),
            );
            name_directory_render_admin_directory_settings_section($directory, __('Submissions', 'name-directory'), $submission_settings);
            ?>

            <tr>
                <th valign="top"><?php echo __('Notification e-mail address', 'name-directory'); ?><br>
                    <small><?php echo __('Which e-mail address should receive notifications? If left blank, the general WordPress admin e-mail address will be used. Keep in mind this person has to have an account on the site and the proper rights to manage Name Directory.', 'name-directory'); ?></small></th>
                <td><input type="email" autocomplete="email" name="email_for_submission" value="<?php echo (! empty($directory['email_for_submission']) ? $directory['email_for_submission'] : ''); ?>" size="20"></td>
            </tr>

            <?php
            name_directory_render_admin_setting_heading(__('Naming', 'name-directory'));
            ?>
            <tr>
                <th valign="top"><?php echo __('Name term', 'name-directory'); ?><br>
                    <small><?php echo __('Alternative (plural) term for "names", i.e. movies', 'name-directory'); ?></small></th>
                <td><input type="text" name="name_term" value="<?php echo (! empty($directory['name_term']) ? $directory['name_term'] : ''); ?>" size="20">
                    <br>
                    <small><?php echo __('If you provide an alternative term for name, your website will display:', 'name-directory'); ?>
                        "<i><?php echo sprintf(__('There are currently %d %s in this directory', 'name-directory'), 1337, __('movies', 'name-directory')); ?></i>"<br>
                        <?php echo __('When left blank, the word "names" will be displayed.', 'name-directory'); ?></small></td>
            </tr>
            <tr>
                <th valign="top"><?php echo __('Name term (singular)', 'name-directory'); ?><br>
                    <small><?php echo __('The singular term for "names", i.e. movie', 'name-directory'); ?></small></th>
                <td><input type="text" name="name_term_singular" value="<?php echo (! empty($directory['name_term_singular']) ? $directory['name_term_singular'] : ''); ?>" size="20"></td>
            </tr>
            <tr>
                <td>
                    <?php wp_nonce_field( 'name_directory_dirmanagement','name_directory_adminnonce' ); ?>
                </td>
                <td>
                    <input type="submit" name="submit" class="button button-primary button-large"
                           value="<?php echo $button_text; ?>" />

                    <a class='button button-large' href='<?php echo $overview_url; ?>'>
                        <?php echo __('Cancel', 'name-directory'); ?>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </form>

    <?php

}


/**
 * Handle the names in the name directory
 *  - Display all names
 *  - Edit names (ajax and 'oldskool' view)
 *  - Create new names
 */
function name_directory_names()
{
    if(! name_directory_is_control_allowed() )
    {
        wp_die( __('You do not have sufficient permissions to access this page.', 'name-directory') );
    }

    global $wpdb;
    global $name_directory_table_directory;
    global $name_directory_table_directory_name;
    $name_directory_settings = get_option('name_directory_general_option');
    $show_all_names = true;

    if(! empty($_GET['delete_name']) && is_numeric($_GET['delete_name']) && check_admin_referer('name-directory-action','secnonce'))
    {
        $name = $wpdb->get_var(sprintf("SELECT `name` FROM %s WHERE id=%d", $name_directory_table_directory_name, $_GET['delete_name']));
        $wpdb->delete($name_directory_table_directory_name, array('id' => $_GET['delete_name']), array('%d'));
        echo "<div class='updated'><p>"
            . sprintf(__('Name %s deleted', 'name-directory'), "<i>" . esc_html($name) . "</i>")
            . "</p></div>";
    }
    else if(! empty($_POST['name_id']))
    {
        if(empty($_POST['name-directory-nonce']) || ! wp_verify_nonce( $_POST['name-directory-nonce'], 'name-directory-action'))
        {
            echo name_directory_get_csrf_error_message($_POST['name']);
            exit;
        }

        $description = stripslashes($_POST['description']);
        if( ! empty( $name_directory_settings['simple_wysiwyg_editor'] ) )
        {
            $description = nl2br($description);
        }

        $wpdb->update(
            $name_directory_table_directory_name,
            array(
                'name'          => wp_kses_post(stripslashes($_POST['name'])),
                'letter'        => name_directory_get_first_char($_POST['name']),
                'description'   => wp_kses_post(stripslashes($description)),
                'published'     => (int)$_POST['published'],
                'submitted_by'  => wp_kses_post($_POST['submitted_by']),
            ),
            array('id' => intval($_POST['name_id']))
        );

        if($_POST['action'] == "name_directory_ajax_names")
        {
            $refresh_url = str_replace('edit_name=', '', $_SERVER['HTTP_REFERER']);
            echo '<p>';
            echo sprintf(__('Name %s updated', 'name-directory'), "<i>" . esc_html($_POST['name']) . "</i>");
            echo '. <small><i>' . __('Will be visible when the page is refreshed.', 'name-directory') . '</i> ';
            echo ' <a href="' . $refresh_url . '">' . __('Refresh now', 'name-directory') . '</a></small>';
            echo '</p>';
            exit;
        }

        echo "<div class='updated'><p>"
            . sprintf(__('Name %s updated', 'name-directory'), "<i>" . esc_html($_POST['name']) . "</i>")
            . "</p></div>";

        unset($_GET['edit_name']);
    }
    else if(! empty($_POST['name']))
    {
        if(! wp_verify_nonce( $_POST['name-directory-nonce'], 'name-directory-action'))
        {
            echo name_directory_get_csrf_error_message($_POST['name']);
            exit;
        }

        $name_exists = name_directory_name_exists_in_directory($_POST['name'], $_POST['directory']);
        if($name_exists && $_POST['action'] == "name_directory_ajax_names")
        {
            echo '<p>';
            echo sprintf(__('Name %s was already on the list, so it was not added', 'name-directory'),
                '<i>' . esc_html($_POST['name']) . '</i>');
            echo '</p>';
            exit;
        }

        $description = $_POST['description'];
        if( ! empty( $name_directory_settings['simple_wysiwyg_editor'] ) )
        {
            $description = nl2br($description);
        }

        $wpdb->insert(
            $name_directory_table_directory_name,
            array(
                'directory'     => (int)$_POST['directory'],
                'name'          => wp_kses_post($_POST['name']),
                'letter'        => name_directory_get_first_char(wp_kses_post($_POST['name'])),
                'description'   => wp_kses_post($description),
                'published'     => (int)$_POST['published'],
                'submitted_by'  => wp_kses_post($_POST['submitted_by']),
            ),
            array('%d', '%s', '%s', '%s', '%d', '%s')
        );

        if($_POST['action'] == "name_directory_ajax_names")
        {
            echo '<p>';
            printf(__('New name %s added', 'name-directory'), '<i>' . esc_html($_POST['name']) . '</i> ');
            echo '. <small><i>' . __('Will be visible when the page is refreshed.', 'name-directory') . '</i> ';
            echo ' <a href="">' . __('Refresh now', 'name-directory') . '</a></small>';
            echo '</p>';
            exit;
        }

        echo "<div class='updated'><p><strong>"
            . sprintf(__('New name %s added', 'name-directory'), "<i>" . esc_html($_POST['name']) . "</i> ")
            . "</strong></p></div>";
    }
    else if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if($_POST['action'] == "name_directory_ajax_names")
        {
            echo '<p>' . __('Please fill in at least a name', 'name-directory') . '</p>';
            exit;
        }

        echo "<div class='error'><p><strong>"
            . __('Please fill in at least a name', 'name-directory')
            . "</strong></p></div>";
    }

    $wp_file = admin_url('admin.php');
    $wp_page = $_GET['page'];
    $wp_sub  = $_GET['sub'];
    $overview_url = sprintf("%s?page=%s", $wp_file, $wp_page);

    if(! array_key_exists('dir', $_GET)) {
        echo "<script>window.location.href = '" . $overview_url . "';</script>";
        exit;
    }

    $directory_id = intval($_GET['dir']);

    $wp_url_path = sprintf("%s?page=%s&sub=%s&dir=%d", $wp_file, $wp_page, $wp_sub, $directory_id);
    $wp_ndir_path = sprintf("%s?page=%s&sub=%s&dir=%d", $wp_file, $wp_page, 'manage-directory', $directory_id);
    $name_directory_settings = get_option('name_directory_general_option');
    $wp_nonce = wp_create_nonce('name-directory-action');

    $published_status = '0,1';
    $emphasis_class = 's_all';
    if(! empty($_GET['status']) && $_GET['status'] == 'published')
    {
        $published_status = '1';
        $emphasis_class = 's_published';
    }
    else if(! empty($_GET['status']) && $_GET['status'] == 'unpublished')
    {
        $published_status = '0';
        $emphasis_class = 's_unpublished';
    }

    $directory = $wpdb->get_row("SELECT * FROM " . $name_directory_table_directory . " WHERE `id` = " . $directory_id, ARRAY_A);
    $names = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE `directory` = %d AND `published` IN (%s) ORDER BY `name` ASC",
        $name_directory_table_directory_name, $directory_id, $published_status));

    echo '<div class="wrap">';
    echo "<h2>" . sprintf(__('Manage names for %s', 'name-directory'), $directory['name']) . "</h2>";
    ?>

    <?php
    if(! empty($_GET['edit_name']))
    {
        $name = $wpdb->get_row(sprintf("SELECT * FROM `%s` WHERE `id` = %d",
            $name_directory_table_directory_name, $_GET['edit_name']), ARRAY_A);
        $table_heading = __('Edit a name', 'name-directory');
        $save_button_txt = __('Save name', 'name-directory');
        $show_all_names = false;
    }
    else
    {
        $table_heading = __('Add a new name', 'name-directory');
        $save_button_txt = __('Add name', 'name-directory');
        $name = array('name' => null, 'description' => '', 'submitted_by' => null);
    }

    ?>
    <span style='float: right;'>
        <a href='<?php echo $overview_url; ?>'><?php _e('Back to the directory overview', 'name-directory'); ?></a>
    </span>

    <p>&nbsp;</p>

    <div class="hidden" id="add_result"></div>

    <a name="anchor_add_form"></a>
    <form name="add_name" id="add_name_ajax" method="post" action="<?php echo $wp_url_path; ?>">
        <table class="wp-list-table widefat" cellpadding="0">
            <thead>
            <tr>
                <th width="18%"><?php echo $table_heading; ?>
                    <input type="hidden" name="directory" value="<?php echo $directory_id; ?>">
                    <input type="hidden" name="name-directory-nonce" value="<?php echo $wp_nonce; ?>">
                    <?php
                    if(! empty($_GET['edit_name']))
                    {
                        echo '<input type="hidden" name="name_id" id="edit_name_id" value="' . intval($_GET['edit_name']) . '">';
                    }
                    ?>
                    <input type="hidden" name="action" value="0" id="add_form_ajax_submit" />
                </th>
                <th align="right">

                    <label id="input_compact" title="<?php echo __('Show the compact form, showing only the name, always published)', 'name-directory'); ?>">
                        <input type="radio" name="input_mode" />
                        <?php echo __('Quick add view', 'name-directory'); ?>
                    </label>
                    <label id="input_extensive" title="<?php echo __('Show the full form, which allows you to enter a description and submitter', 'name-directory'); ?>">
                        <input type="radio" name="input_mode" />
                        <?php echo __('Full add view', 'name-directory'); ?>
                    </label>

                </th>
            </tr>
            </thead>
            <tbody>
            <tr id="add_name">
                <td width="18%"><?php echo __('Name', 'name-directory'); ?></td>
                <td width="82%"><input type="text" name="name" value="<?php echo esc_html($name['name']); ?>" size="20" class="name_directory_widest"></td>
            </tr>
            <tr id="add_description">
                <td><?php echo __('Description', 'name-directory'); ?></td>
                <td><?php
                    /* Determine the kind of editor to use */
                    if(empty($name_directory_settings) || empty($name_directory_settings['simple_wysiwyg_editor']))
                    {
                        echo '<textarea name="description" rows="5" class="name_directory_widest">' . esc_textarea($name['description']) . '</textarea>';
                    }
                    else
                    {
                        wp_editor($name['description'], 'description', array('textarea_rows' => 5, 'textarea_name' => 'description'));
                    }
                    ?>
                    <small><strong><?php echo __('Please be careful!', 'name-directory'); ?></strong>
                        <?php echo __('HTML markup is allowed here, but please be aware it may affect the way Name Directory or a name is displayed. Also, features like "Read more" may behave differently if you use i.e. images or tables in the description of an image.', 'name-directory'); ?></small></td>
            </tr>
            <tr id="add_published">
                <td><?php echo __('Published', 'name-directory'); ?></td>
                <td>
                    <input type="radio" name="published" id="published_yes" value="1" checked="checked">
                    <label for="published_yes"><?php echo __('Yes', 'name-directory') ?></label>

                    <input type="radio" name="published" id="published_no" value="0"
                        <?php
                        if(isset($name['published']) && empty($name['published']))
                        {
                            echo 'checked="checked"';
                        }?>>
                    <label for="published_no"><?php echo __('No', 'name-directory') ?></label>
                </td>
            </tr>
            <tr id="add_submitter">
                <td><?php echo __('Submitted by', 'name-directory'); ?></td>
                <td><input type="text" name="submitted_by" value="<?php echo esc_html($name['submitted_by']); ?>" size="20" class="name_directory_widest"></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" id="add_button" name="Submit" class="button button-primary button-large"
                           value="<?php echo $save_button_txt; ?>" />
                </td>
            </tr>
            </tbody>
        </table>
    </form>

    <?php

    /* Don't show all the names when the user explicitly started the adding-mode */
    if(! empty($_GET['display_all_names']) && htmlspecialchars($_GET['display_all_names']) == "no")
    {
        $show_all_names = false;
    }

    if($show_all_names)
    {
        ?>
        <p>
            <a class='s_all' href='<?php echo $wp_url_path; ?>&status=all'><?php _e('all', 'name-directory'); ?></a> |
            <a class='s_published' href='<?php echo $wp_url_path; ?>&status=published'><?php _e('published', 'name-directory'); ?></a> |
            <a class='s_unpublished' href='<?php echo $wp_url_path; ?>&status=unpublished'><?php _e('unpublished', 'name-directory'); ?></a>
        </p>

        <?php
        $name_filter = array();
        $num_names = 0;

        $search_value = '';
        if(! empty($_GET['s']))
        {
            $search_value = htmlspecialchars($_GET['s']);
            $name_filter['containing'] = $search_value;

            $names = name_directory_get_directory_names($directory, $name_filter);
            $num_names = count($names);
        }

        $parsed_url = parse_url($_SERVER['REQUEST_URI']);
        $search_get_url = array();
        if(! empty($parsed_url['query']))
        {

            parse_str($parsed_url['query'], $search_get_url);
        }
        unset($search_get_url['s']);

        echo '<form method="get" role="search" action="">';
        echo '<p class="search-box">';
        foreach($search_get_url as $key_name=>$value)
        {
            if($key_name == 'name_directory_startswith')
            {
                continue;
            }
            echo "<input type='hidden' name='" . htmlspecialchars($key_name) . "' value='" . htmlspecialchars($value) . "' />";
        }
        echo "<input type='search' class='tagsdiv newtag' name='s' id='name-directory-search-input-box' value='" . $search_value . "' placeholder='" . __('Search', 'name-directory') . "...' />";
        echo "<input type='submit' id='name-directory-search-input-button' class='button' value='" . __('Search', 'name-directory') . "' />";


        echo '</p>';
        if(empty($name_filter['character']) && ! empty($search_value))
        {
            echo '<br><br><p class="search-box">';
            if(empty($directory['name_term']))
            {
                echo sprintf(__('There are %d names in this directory containing the search term %s.', 'name-directory'), $num_names, "<em><strong>" . esc_html($search_value) . "</strong></em>");
            }
            else
            {
                echo sprintf(__('There are %d %s in this directory containing the search term %s.', 'name-directory'), $num_names, $directory['name_term'], "<em><strong>" . esc_html($search_value) . "</strong></em>");
            }
            echo ' <a href="' . $wp_ndir_path . '"><strong><em>' . __('Clear results', 'name-directory') . '</em></strong></a>';
            echo "</p>";
        }
        else
        {
            echo "<br><br>";
        }
        echo "</form>";

        ?>

        <table class="wp-list-table widefat name_directory_names fixed" cellpadding="0">
            <thead>
            <tr>
                <th width="18%"><?php echo __('Name', 'name-directory'); ?></th>
                <th width="52%"><?php echo __('Description', 'name-directory'); ?></th>
                <th width="12%"><?php echo __('Submitter', 'name-directory'); ?></th>
                <th width="9%"><?php echo __('Published', 'name-directory'); ?></th>
                <th width="12%"><?php echo __('Manage', 'name-directory'); ?></th>
                <th width="5%"><?php echo __('ID', 'name-directory'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(empty($name))
            {
                echo sprintf("<tr class='empty-directory'><td colspan='5'>%s</td></tr>",
                    __('Currently, there are no names in this directory..', 'name-directory'));
            }

            foreach($names as $name)
            {
                if(is_array($name))
                {
                    $name = (object)$name;
                }

                echo sprintf("
                <tr>
                    <td>%s</td><td>%s</td><td>%s</td><td><label class='name_directory_switch'><input class='toggle_published' type='checkbox' id='nid_%d' data-nameid='%d' %s><span class='name_directory_slider' title='%s'></span></label></td>
                    <td><a class='button button-primary button-small' href='" . $wp_url_path . "&edit_name=%d#anchor_add_form'>%s</a>
                        <a class='button button-small' href='" . $wp_url_path . "&delete_name=%d&secnonce=%s'>%s</a>
                    </td><td>%s</td>
                </tr>",
                    html_entity_decode(stripslashes($name->name)),
                    html_entity_decode(stripslashes($name->description)),
                    sanitize_text_field(esc_html($name->submitted_by)),
                    $name->id,
                    $name->id,
                    ! empty($name->published)?' checked':'',
                    __('Toggle published status', 'name-directory'),
                    $name->id, __('Edit', 'name-directory'),
                    $name->id, $wp_nonce, __('Delete', 'name-directory'),
                    $name->id);
            }
            ?>
            </tbody>
        </table>

        <p>&nbsp;</p>

        <?php
    } else {
        echo "<p>";
        echo __('You are currently in editing or adding-mode, so not all the names are shown.', 'name-directory');
        echo " <a class='s_all' href='" . $wp_url_path . "&status=all'>" . __('View the names', 'name-directory') . "</a>";
        echo "</p>";
    }

    wp_add_inline_script('name_directory_admin', "jQuery('" . $emphasis_class . "').css('font-weight', 'bold');");

    if(! empty($_GET['edit_name']))
    {
        wp_add_inline_script('name_directory_admin', "jQuery('#input_extensive').trigger('click');");
    }
}


/**
 * Create a directory with just a name and then go to the import page
 */
function name_directory_quick_import()
{
    global $wpdb;
    global $name_directory_table_directory;

    echo '<div class="wrap">';
    echo "<h2>"
        . __('Import names into a new directory', 'name-directory')
        . "</h2>";

    if(! empty($_POST['mode']) && $_POST['mode'] == "new" && check_admin_referer('name-directory-quick','quicknonce'))
    {
        $cleaned_name = sanitize_text_field($_POST['name']);
        if(empty($cleaned_name)){
            $cleaned_name = __('Quick import', 'name-directory');
        }

        $wpdb->insert(
            $name_directory_table_directory,
            array(
                'name'                          => $cleaned_name,
                'description'                   => __('Quick import', 'name-directory'),
                'show_title'                    => 1,
                'show_description'              => 1,
                'show_submit_form'              => 0,
                'check_submitted_names_first'   => 0,
                'show_search_form'              => 1,
                'show_submitter_name'           => 0,
                'show_line_between_names'       => 0,
                'show_character_header'         => 0,
                'show_all_names_on_index'       => 0,
                'show_all_index_letters'        => 1,
                'jump_to_search_results'        => 1,
                'nr_columns'                    => 1,
                'nr_most_recent'                => 0,
                'nr_words_description'          => 0,
                'name_term'                     => '',
                'name_term_singular'            => '',
                'email_for_submission'          => '',
                'show_current_num_names'        => 1,
                'show_index_instructions'       => 1,
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%d', '%d')
        );

        $import_url = sprintf("%s?page=%s&sub=%s&dir=%d", admin_url('admin.php'), $_GET['page'], 'import', $wpdb->insert_id);

        echo "<div class='updated'><p>"
            . sprintf(__('Directory %s created.', 'name-directory'), "<i>" . $cleaned_name . "</i>")
            . "</p></div>";

        echo __('Loading the import page...', 'name-directory');
        echo "<script>window.location.href = '" . $import_url . "';</script>";
    }

    $wp_nonce = wp_create_nonce('name-directory-quick');
    ?>

    <form name="add_name" method="post" action="<?php echo sprintf("%s?page=%s&sub=quick-import&quicknonce=%s", admin_url('admin.php'), $_GET['page'], $wp_nonce); ?>">
        <table class="wp-list-table widefat" cellpadding="0">
            <thead>
            <tr>
                <th colspan="2">
                    <?php echo __('What would be the title of your new directory to import items into?', 'name-directory'); ?>
                    <input type="hidden" name="mode" value="new">
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td width="29%"><?php echo __('Title', 'name-directory'); ?></td>
                <td width="70%"><input type="text" name="name" value="" size="20" class="name_directory_widest"></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" name="submit" class="button button-primary button-large"
                           value="<?php echo __('Next step', 'name-directory') ?>" />
                </td>
            </tr>
            </tbody>
        </table>
    </form>

    <?php
}



/**
 * Import names from a csv file into directory
 */
function name_directory_import()
{
    if(! name_directory_is_control_allowed() )
    {
        wp_die( __('You do not have sufficient permissions to access this page.', 'name-directory') );
    }

    global $wpdb;
    global $name_directory_table_directory;
    global $name_directory_table_directory_name;

    $directory_id = intval($_GET['dir']);
    $import_success = false;
    $use_utf_import = false;

    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        check_admin_referer('import-upload');
        $file = wp_import_handle_upload();

        if( isset($file['error']))
        {
            echo $file['error'];
            return;
        }

        if(! empty($_POST['empty_dir_on_import']))
        {
            $wpdb->delete( $name_directory_table_directory_name, array( 'directory' => $directory_id ) );
        }

        if(! empty($_POST['use_utf8_import']))
        {
            $use_utf_import = true;
        }

        $csv = array_map( 'str_getcsv', file($file['file']) );

        wp_import_cleanup($file['id']);
        array_shift($csv);

        $names_error = 0;
        $names_imported = 0;
        $names_duplicate = 0;
        foreach($csv as $entry)
        {
            if ( ! $prepared_row = name_directory_prepared_import_row($entry, 1, $use_utf_import) )
            {
                continue;
            }

            if ( name_directory_name_exists_in_directory($prepared_row['name'], $directory_id) )
            {
                $names_duplicate++;
                continue;
            }

            $db_res = $wpdb->insert(
                $name_directory_table_directory_name,
                array(
                    'directory'     => $directory_id,
                    'name'          => sanitize_text_field($prepared_row['name']),
                    'letter'        => name_directory_get_first_char($prepared_row['name']),
                    'description'   => ! empty($prepared_row['description']) ? wp_kses_post($prepared_row['description']) : '',
                    'published'     => ! empty($prepared_row['published']) ? $prepared_row['published'] : '',
                    'submitted_by'  => ! empty($prepared_row['submitted_by']) ? sanitize_text_field($prepared_row['submitted_by']) : '',
                ),
                array('%d', '%s', '%s', '%s', '%d', '%s')
            );

            if($db_res === false)
            {
                $names_error++;
            }
            else
            {
                $names_imported++;
            }
        }

        $notice_class = 'updated';
        $import_success = true;
        $import_message = sprintf(__('Imported %d entries in this directory', 'name-directory'), $names_imported);

        if($names_imported === 0)
        {
            $notice_class = 'error';
            $import_success = false;
            $import_message = __('Could not import any names into Name Directory', 'name-directory');
        }

        if($names_error > 0)
        {
            $notice_class = 'error';
            $import_success = false;
            if($names_imported === 0)
            {
                $import_message .= "! ";
            }
            $import_message .= "<br>" . sprintf(__('There were %d names that produces errors with the WordPress database on import', 'name-directory'), $names_error);
        }

        if($names_duplicate > 0)
        {
            $ignored = (count($csv)==$names_duplicate)?__('all', 'name-directory'):$names_duplicate;
            echo '<div class="error" style="border-left: 4px solid #ffba00;"><p>'
                . sprintf(__('Ignored %s names, because they were duplicate (already in the directory)', 'name-directory'), $ignored)
                . '</p></div>';
        }
        elseif($names_imported === 0)
        {
            $import_message .= ', ' . __('please check your .csv-file', 'name-directory');
        }

        echo '<div class="' . $notice_class . '"><p>' . $import_message . '</p></div>';
    }

    $wp_file = admin_url('admin.php');
    $wp_page = $_GET['page'];
    $wp_sub  = $_GET['sub'];
    $overview_url = sprintf("%s?page=%s", $wp_file, $wp_page);
    $wp_url_path = sprintf("%s?page=%s&sub=%s&dir=%d", $wp_file, $wp_page, $wp_sub, $directory_id);
    $wp_ndir_path = sprintf("%s?page=%s&sub=%s&dir=%d", $wp_file, $wp_page, 'manage-directory', $directory_id);

    $directory = $wpdb->get_row("SELECT * FROM " . $name_directory_table_directory . " WHERE `id` = " . $directory_id, ARRAY_A);

    echo '<div class="wrap">';
    echo '<h2>' . sprintf(__('Import names for %s', 'name-directory'), $directory['name']) . '</h2>';
    echo '<div class="narrow name_directory_import_page"><p>';
    if(! $import_success && empty($names_duplicate))
    {
        echo __('Use the upload form below to upload a .csv-file containing all of your names (in the first column), description and submitter are optional.', 'name-directory') . ' ';
        echo '<h4>' . __('If you saved it from Excel or OpenOffice, please ensure that:', 'name-directory') . '</h4> ';
        echo '<ol><li>' . __('There is a header row (this contains the column names, the first row will NOT be imported)', 'name-directory');
        echo '</li><li>' . __('Fields are encapsulated by double quotes', 'name-directory');
        echo '</li><li>' . __('Fields are comma-separated', 'name-directory');
        echo '</li><li>' . __('If such an option presents itself, save as UTF-8 (not ANSI)', 'name-directory');
        echo '</li></ol>';
        echo '<p>' . sprintf(__('Please check out %s first and ensure your file is formatted the same.', 'name-directory'),
                '<a href="http://plugins.svn.wordpress.org/name-directory/assets/name-directory-import-example.csv" target="_blank" rel="noopener noreferrer">' .
                __('the example import file', 'name-directory') . '</a>') . '</p>';
        echo '<p><em>' . __('One of the best ways to verify if your file has the right format is opening it in a plain text editor, like Windows Notepad, Geany, SublimeText or Notepad++.', 'name-directory') . '</em></p>';
        echo '<br>';
        echo '<h4>' . __('If uploading or importing fails, these are your options', 'name-directory') . '</h4><ol>';
        echo '<li>
                <a href="https://www.freefileconvert.com" target="_blank" rel="noopener noreferrer">' .
                __('Use an online File Convertor', 'name-directory') . '</a>
              </li>
              <li>
                <a href="https://wiki.openoffice.org/wiki/Documentation/OOo3_User_Guides/Calc_Guide/Saving_spreadsheets#Saving_as_a_CSV_file">OpenOffice csv-export help</a>
              </li>
              <li>
                <a href="https://support.office.com/en-us/article/Import-or-export-text-txt-or-csv-files-e8ab9ff3-be8d-43f1-9d52-b5e8a008ba5c?CorrelationId=fa46399d-2d7a-40bd-b0a5-27b99e96cf68&ui=en-US&rs=en-US&ad=US#bmexport">Excel csv-export help</a>
              </li>
              <li>';
        echo sprintf(__('If everything else fails, you can always ask a question at the %s.', 'name-directory'),
                '<a href="https://wordpress.org/support/plugin/name-directory" target="_blank" rel="noopener noreferrer">' .
                __('plugin support forums', 'name-directory') . '</a>') . ' ' .
                __('Please make sure you include all the steps you did to create the file you are trying to import.', 'name-directory');
        echo '</li></ol></p>';
        echo '<p><em>' . __('When using the upload function, script-tags are being removed for security reasons.', 'name-directory') . '</em></p>';
        echo '<br>';

        if(! function_exists('str_getcsv'))
        {
            echo '<div class="error"><p>';
            echo __('Name Directory Import requires at least PHP 5.3, you seem to have an older version. Importing names will not work for your website.', 'name-directory');
            echo '</p></div>';
        }

        echo '<h3>' . __('Upload your .csv-file', 'name-directory') . '</h3>';
        wp_import_upload_form($wp_url_path);
    }
    echo '</div></div>';
    echo '<a href="' . $wp_ndir_path . '">' . sprintf(__('Back to %s', 'name-directory'), '<i>' . $directory['name'] . '</i>') . '</a>';
    echo ' | ';
    echo '<a href="' . $overview_url . '">' . __('Go to Name Directory Overview', 'name-directory') . '</a>';
}


/**
 * Page to export names from a directory file as a .csv-file
 */
function name_directory_export()
{
    if(! name_directory_is_control_allowed() )
    {
        wp_die( __('You do not have sufficient permissions to access this page.', 'name-directory') );
    }

    global $wpdb;
    global $name_directory_table_directory;

    $directory = $wpdb->get_row("SELECT * FROM " . $name_directory_table_directory . " WHERE `id` = " . intval($_GET['dir']), ARRAY_A);

    $names = name_directory_get_directory_names($directory);

    echo '<table id="export_names" class="hidden"><thead><tr><th>name</th><th>description</th><th>submitter</th></tr></thead><tbody>';
    foreach($names as $entry)
    {
        $description = str_replace(array("\n", "\r"), '', $entry['description']);
        echo '<tr><td>' . esc_html($entry['name']) . '</td><td>' . esc_html($description) . '</td><td>' . esc_html($entry['submitted_by']) . '</td></tr>';
    }
    echo '</tbody></table>';

    /* Notify the user of possible not-working export functionality */
    if(stripos($_SERVER['HTTP_USER_AGENT'], 'Chrome') === false && stripos($_SERVER['HTTP_USER_AGENT'], 'Firefox') === false)
    {
        echo '<div class="notice notice-warning"><p>';
        echo __('Name Directory Export works best in Mozilla Firefox, Google Chrome and Internet Explorer 10+.', 'name-directory') . ' ';
        echo __('If you encounter problems (or it does not export) in Internet Explorer or Microsoft Edge, please try another browser.', 'name-directory');
        echo '</div>';
    }

    echo '<div class="wrap">';
    echo '<h2>' . sprintf(__('Export directory %s', 'name-directory'), $directory['name']) . '</h2>';
    echo '<div class="narrow name_directory_export"><p>';
    echo __('Click the Export button to download a .csv file with the contents of your directory.', 'name-directory');
    echo '</p><p><a href="#" id="export_name_directory_names_button" class="button button-primary">' . __('Export', 'name-directory') . '</a></p>';
    echo '<p><a href="' . admin_url('admin.php') . '?page=name-directory">' . __('Go to Name Directory Overview', 'name-directory') . '</a></p>';
}


/**
 * Proxy for the AJAX request to switch published-statusses
 * No params, assumes POST
 */
function name_directory_ajax_switch_name_published_status()
{
    if(! name_directory_is_control_allowed() )
    {
        wp_die( __('You do not have sufficient permissions to access this page.', 'name-directory') );
    }

    $name_id = intval($_POST['name_id']);
    if( ! empty($name_id) )
    {
        echo name_directory_switch_name_published_status($name_id);
        exit;
    }

    echo 'Error!';
    exit;
}


/**
 * Add the javascript and css resources to the admin
 */
function name_directory_admin_add_resources()
{
    $admin_js_translation = array(
        'delete_question' => __('Are you sure you want to delete this name directory?', 'name-directory'),
        'empty_directory_on_import' => __('Remove all the entries of this directory before starting the import', 'name-directory'),
        'use_utf8_import' => __('Use special import option (use only when importing has failed before and if you are using non-latin characters)', 'name-directory'),
    );

    wp_register_script('name_directory_admin', plugins_url('name_directory_admin.js', __FILE__), array('jquery'), '1.0', true);
    wp_localize_script('name_directory_admin', 'name_directory_translation', $admin_js_translation);
    wp_enqueue_script('name_directory_admin');

    wp_enqueue_style('name_directory_admin', plugins_url('name_directory_admin.css', __FILE__), '', '1.0');
}