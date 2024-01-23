<?php
add_action('wp_enqueue_scripts', 'name_directory_add_frontend_assets');

/**
 * Add the CSS file to output
 */
function name_directory_add_frontend_assets()
{
    wp_register_style('name-directory-style', plugins_url('name_directory.css', __FILE__));
    wp_enqueue_style('name-directory-style');

    /* If Google reCAPTCHA for Name Directory is enabled, load it too */
    $name_directory_settings = get_option('name_directory_general_option');
    if(! empty($name_directory_settings) && ! empty($name_directory_settings['enable_recaptcha']))
    {
        wp_enqueue_script( 'name-directory-recaptcha', 'https://www.google.com/recaptcha/api.js', false, false );

        /* Make sure it's loaded 'defer async' */
        add_filter( 'script_loader_tag', function ( $tag, $handle ) {
            if ( 'name-directory-recaptcha' !== $handle ) {
                return $tag;
            }
            return str_replace( ' src', ' async defer src', $tag );
        }, 10, 2 );
    }
}


/**
 * Render function to display a namebox for a Name Directory
 * @param $entry
 * @param $directory
 */
function name_directory_render_namebox($entry, $directory)
{
    echo '<div class="name_directory_name_box">';
    echo '<a name="namedirectory_' . sanitize_html_class($entry['name']) . '"></a>';
    echo '<strong role="term">' . html_entity_decode(stripslashes($entry['name'])) . '</strong><br>';
    if(! empty($directory['show_description']) && ! empty($entry['description']))
    {
        $print_description = html_entity_decode(stripslashes($entry['description']));

        /* This toggles the read more/less indicators, these need extra html */
        if(! empty($directory['nr_words_description']))
        {
            $num_words = intval($directory['nr_words_description']);
            $short_desc = name_directory_get_words($print_description, $num_words);
            $print_description = str_replace($short_desc, "", $print_description);
            if(! empty($print_description))
            {
                echo '<div role="definition">
                      <input type="checkbox" class="name_directory_readmore_state" id="name-' . htmlspecialchars($entry['id']) . '" />
                      <span class="name_directory_readmore_wrap">' . $short_desc . ' <span class="name_directory_readmore_target">' . $print_description .'</span></span>
                      <label for="name-' . htmlspecialchars($entry['id']) . '" class="name_directory_readmore_trigger"></label>
                    </div>';
            }
            else
            {
                echo '<div role="definition">' . do_shortcode($short_desc) . '</div>';
            }

        }
        else {
            echo '<div role="definition">' . do_shortcode($print_description) . '</div>';
        }
    }
    if(! empty($directory['show_submitter_name']) && ! empty($entry['submitted_by']))
    {
        echo "<small>" . __('Submitted by:', 'name-directory') . " " . $entry['submitted_by'] . "</small>";
    }
    echo '</div>';
}


/**
 * Show and handle the submission form
 * @param $directory
 * @param $overview_url
 * @return string
 */
function name_directory_show_submit_form($directory, $overview_url)
{
    global $wpdb;
    global $name_directory_table_directory_name;
    $name_directory_settings = get_option('name_directory_general_option');

    /* Prevent a form from rendering if it's not the one to render, when there are multiple directories on a page */
    if(! empty($_GET['dir']) && ($directory != $_GET['dir']))
    {
        return '';
    }

    $recaptcha_enabled = false;
    $recaptcha_html = '';

    /* If WordPress Search for Name Directory is disabled, just return */
    if(! empty($name_directory_settings) && ! empty($name_directory_settings['enable_recaptcha']))
    {
        $recaptcha_enabled = true;
        $recaptcha_html = "<div class='name_directory_forminput'><br><div class='g-recaptcha' data-sitekey='" . $name_directory_settings['recaptcha_sitekey'] . "'></div></div>";
    }

    $directory_info = name_directory_get_directory_properties($directory);

    if(empty($directory_info['name_term_singular']))
    {
        $name = __('Name', 'name-directory');
        $back_txt = __('Back to name directory', 'name-directory');
        $error_empty_txt = __('Please fill in at least a name', 'name-directory');
    }
    else
    {
        $name = ucfirst($directory_info['name_term_singular']);
        $back_txt = sprintf(__('Back to %s directory', 'name-directory'), $directory_info['name_term_singular']);
        $error_empty_txt = sprintf(__('Fill in at least a %s', 'name-directory'), $directory_info['name_term_singular']);
    }

    $required = __('Required', 'name-directory');
    $description = __('Description', 'name-directory');
    $your_name = __('Submitter name', 'name-directory');
    $submit = __('Submit', 'name-directory');

    $result_class = '';
    $form_result = null;
    $alert_role = '';

    /* reCAPTCHA implementation */
    $proceed_submission = true;
    if($recaptcha_enabled === true)
    {
        /* Check whether user even checked the box */
        if(empty($_POST['g-recaptcha-response']))
        {
            if (! empty($error_empty_txt)) {
                $error_empty_txt .= "<br>";
            }
            $error_empty_txt .= __('Please prove you are not a robot', 'name-directory');
            $proceed_submission = false;
        }
        else
        {
            $recaptcha_verify_response = file_get_contents(sprintf('https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s',
                $name_directory_settings['recaptcha_secretkey'], $_POST['g-recaptcha-response']));
            $recaptcha_response = json_decode($recaptcha_verify_response);
            if(! empty($recaptcha_response->success))
            {
                $proceed_submission = true;
            }
            else
            {
                /* Place error message because it didn't work */
                if(! empty($error_empty_txt))
                {
                    $error_empty_txt .= "<br>";
                }
                $error_empty_txt .= __('reCAPTCHA failed, please try again', 'name-directory');
                $proceed_submission = false;
            }
        }
    }

    if($proceed_submission === true && ! empty($_POST['name_directory_name']))
    {
        $wpdb->get_results(
            sprintf("SELECT `id` FROM `%s` WHERE `name` = '%s' AND `directory` = %d",
            $name_directory_table_directory_name,
            esc_sql($_POST['name_directory_name']),
            esc_sql(intval($directory)))
        );

        if($wpdb->num_rows > 0)
        {
            $result_class = 'form-result-error';
            $form_result = sprintf(__('Sorry, %s was already on the list so your submission was not sent.', 'name-directory'),
                '<i>' . esc_sql($_POST['name_directory_name']) . '</i>');
        }
        else
        {
            $published = 0;
            if(empty($directory_info['check_submitted_names_first']))
            {
                $published = 1;
            }

            $db_success = $wpdb->insert(
                $name_directory_table_directory_name,
                array(
                    'directory'     => intval($directory),
                    'name'          => sanitize_text_field($_POST['name_directory_name']),
                    'letter'        => name_directory_get_first_char($_POST['name_directory_name']),
                    'description'   => wp_kses_post($_POST['name_directory_description']),
                    'published'     => $published,
                    'submitted_by'  => sanitize_text_field($_POST['name_directory_submitter']),
                ),
                array('%d', '%s', '%s', '%s', '%d', '%s')
            );

            if(! empty($db_success))
            {
                $result_class = 'form-result-success';

                if(! empty($directory_info['check_submitted_names_first']))
                {
                    $form_result = __('Thank you for your submission! It will be reviewed shortly.', 'name-directory');
                    name_directory_notify_admin_of_new_submission($directory, $_POST);
                } else {
                    $form_result = __('Thank you for your submission!', 'name-directory');
                }
            }
            else
            {
                $result_class = 'form-result-error';
                $form_result = __('Something must have gone terribly wrong. Would you please try it again?', 'name-directory');
            }
        }
    }
    else if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $result_class = 'form-result-error';
        $form_result = $error_empty_txt;
    }

    if( strpos( $result_class, 'error' ) !== false ) {
        $alert_role = "role='alert'";
    }

    $form = <<<HTML
        <form method='post' name='name_directory_submit'>

            <div class='name_directory_form_result {$result_class}' {$alert_role}>{$form_result}</div>

            <p><a href="{$overview_url}">{$back_txt}</a></p>

            <div class='name_directory_forminput'>
                <label for='name_directory_name'>{$name} <small>{$required}</small></label>
                <br />
                <input id='name_directory_name' type='text' autocomplete='off' name='name_directory_name' />
            </div>

            <div class='name_directory_forminput'>
                <label for='name_directory_description'>{$description}</label>
                <br />
                <textarea id='name_directory_description' name='name_directory_description'></textarea>
            </div>

            <div class='name_directory_forminput'>
                <label for='name_directory_submitter'>{$your_name}</label>
                <br />
                <input id='name_directory_submitter' type='text' autocomplete='name' name='name_directory_submitter' />
            </div>

            {$recaptcha_html}

            <div class='name_directory_forminput'>
                <br />
                <button type='submit'>{$submit}</button>
            </div>

        </form>
HTML;

    return $form;
}


/**
 * Function that takes care of displaying.. stuff
 * @param $attributes
 * @return mixed
 */
function name_directory_show_directory($attributes)
{
    $dir = null;
    $show_all_link = '';
    $show_latest_link = '';
    $jump_location = '';
    extract(shortcode_atts(
        array('dir' => '1'),
        $attributes
    ));

    $name_filter = array();
    if(! empty($_GET['name_directory_startswith']) && $_GET['name_directory_startswith'] == "latest")
    {
        $name_filter['character'] = "latest";
    }
    else if(isset($_GET['name_directory_startswith']) && isset($_GET['dir']) && ($_GET['dir'] == $dir))
    {
        $name_filter['character'] = name_directory_get_first_char($_GET['name_directory_startswith']);
    }
    else if(! empty($attributes['start_with']) && empty($_GET['name-directory-search-value']))
    {
        $name_filter['character'] = $attributes['start_with'];
    }

    $str_all = __('All', 'name-directory');
    $str_latest = __('Latest', 'name-directory');
    $highlight_search_term = false;
    $search_value = '';

    $letter_url = name_directory_make_plugin_url('name_directory_startswith', 'name-directory-search-value', $dir);
    $directory = name_directory_get_directory_properties($dir);
    if($directory === null)
    {
        echo sprintf(__('Error: Name Directory #%d does not exist (anymore). If you are the webmaster, please change the shortcode.', 'name-directory'), $dir);
        return false;
    }

    if(! empty($_GET['name-directory-search-value']) && ! empty($_GET['dir']) && $_GET['dir'] == $dir)
    {
        $search_value = htmlspecialchars($_GET['name-directory-search-value']);
        $name_filter['containing'] = $search_value;
        if(! empty($directory['search_highlight']))
        {
            $highlight_search_term = true;
        }
    }

    $names = name_directory_get_directory_names($directory, $name_filter);
    $num_names = count($names);

    if(isset($_GET['show_submitform']))
    {
        return name_directory_show_submit_form($dir, name_directory_make_plugin_url('','show_submitform', $dir));
    }

    ob_start();

    if(! empty($directory['jump_to_search_results']))
    {
        $jump_location = "#name_directory_position" . $dir;
    }

    echo "<a name='name_directory_position" . $dir . "'></a>";

    if(! empty($directory['show_title']))
    {
        echo "<h3 class='name_directory_title'>" . $directory['name'] . "</h3>";
    }

    if(! empty($directory['show_all_names_on_index']))
    {
        $show_all_link = '<a class="name_directory_startswith" href="' . $letter_url . $jump_location . '">' . $str_all . '</a> |';
    }

    if(! empty($directory['nr_most_recent']))
    {
        $show_latest_link = ' <a class="name_directory_startswith" href="' . $letter_url . 'latest' . $jump_location . '">' . $str_latest . '</a> |';
    }

    /* Prepare and print the index-letters */
    echo '<div class="name_directory_index">';
    echo $show_all_link;
    echo $show_latest_link;

    $index_letters = range('A', 'Z');
    array_unshift($index_letters, '#');
    $starting_letters = name_directory_get_directory_start_characters($dir);

    /* User does not want to show all the index characters */
    if(empty($directory['show_all_index_letters']))
    {
        $index_letters = $starting_letters;
    }

    foreach($index_letters as $index_letter)
    {
        $extra_classes = '';
        if(! empty($name_filter['character']) && $name_filter['character'] == $index_letter)
        {
            $extra_classes .= ' name_directory_active';
        }

        if(! in_array($index_letter, $starting_letters))
        {
            $extra_classes .= ' name_directory_empty';
        }

        echo ' <a class="name_directory_startswith ' . $extra_classes . '" href="' . $letter_url . urlencode($index_letter) . $jump_location . '">' . strtoupper($index_letter) . '</a> ';
    }

    if(! empty($directory['show_submit_form']))
    {
        if(empty($directory['name_term_singular']))
        {
            $submit_string = __('Submit a name', 'name-directory');
        }
        else
        {
            $submit_string =  sprintf(__('Submit a %s', 'name-directory'), $directory['name_term_singular']);
        }

        echo " | <a href='" . name_directory_make_plugin_url('','name_directory_startswith', $dir) . "&show_submitform=true'>" . $submit_string . "</a>";
    }

    if(! empty($directory['show_search_form']))
    {
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);
        $search_get_url = array();
        if(! empty($parsed_url['query']))
        {
            parse_str($parsed_url['query'], $search_get_url);
        }
        unset($search_get_url['name-directory-search-value']);

        echo "<br />";
        echo "<form role='search' method='get' action='" . $jump_location . "'>";
        foreach($search_get_url as $key_name => $value)
        {
            if(in_array($key_name, ['name_directory_startswith', 'dir']) || is_array($key_name) || is_array($value))
            {
                continue;
            }
            echo "<input type='hidden' name='" . htmlspecialchars($key_name) . "' value='" . htmlspecialchars($value) . "' />";
        }
        echo "<input type='search' autocomplete='off' aria-description='" . __('Directory names below will match your searchcriteria after submit', 'name-directory') . "' name='name-directory-search-value' id='name-directory-search-input-box' placeholder='" . __('Search for...', 'name-directory') . "' />";
        echo "<input type='hidden' name='dir' value='" . (int)$directory['id'] . "' />";
        echo "<input type='submit' id='name-directory-search-input-button' value='" . __('Search', 'name-directory') . "' />";
        echo "</form>";
    }
    echo '</div>';

    echo '<div class="name_directory_total">';
    if(empty($name_filter['character']) && empty($search_value) && $directory['show_current_num_names'])
    {
        if(empty($directory['name_term'])) {
            echo sprintf(__('There are currently %d names in this directory', 'name-directory'), $num_names);
        } else {
            if( $num_names == 1 ) {
                echo sprintf(__('There is currently %d %s in this directory', 'name-directory'), $num_names, $directory['name_term_singular']);
            } else {
                echo sprintf(__('There are currently %d %s in this directory', 'name-directory'), $num_names, $directory['name_term']);
            }
        }
    }
    else if(empty($name_filter['character']) && ! empty($search_value))
    {
        if(empty($directory['name_term'])) {
            echo sprintf(__('There are %d names in this directory containing the search term %s.', 'name-directory'), $num_names, "<em>" . stripslashes($search_value) . "</em>");
        } else {
            if( $num_names == 1 ) {
                echo sprintf(__('There is currently %d %s in this directory containing the search term %s.', 'name-directory'), $num_names, $directory['name_term_singular'], "<em>" . stripslashes($search_value) . "</em>");
            } else {
                echo sprintf(__('There are currently %d %s in this directory containing the search term %s.', 'name-directory'), $num_names, $directory['name_term'], "<em>" . stripslashes($search_value) . "</em>");
            }
        }

        echo " <a href='" . get_permalink() . "'><small>" . __('Clear results', 'name-directory') . "</small></a>.<br />";
    }
    else if($directory['show_current_num_names'] && isset($name_filter['character']) && $name_filter['character'] == 'latest')
    {
        if(empty($directory['name_term'])) {
            echo sprintf(__('Showing %d most recent names in this directory', 'name-directory'), $num_names);
        } else {
            echo sprintf(__('Showing %d most recent %s in this directory', 'name-directory'), $num_names, $directory['name_term']);
        }
    }
    else if($directory['show_current_num_names'])
    {
        if(empty($directory['name_term'])) {
            if( $num_names == 1 ) {
                echo sprintf(__('There is currently %d %s in this directory beginning with the letter %s.', 'name-directory'), $num_names, strtolower(__('Name', 'name-directory')), $name_filter['character']);
            } else {
                echo sprintf(__('There are currently %d %s in this directory beginning with the letter %s.', 'name-directory'), $num_names, __('names', 'name-directory'), $name_filter['character']);
            }
        } else {
            if( $num_names == 1 ) {
                echo sprintf(__('There is currently %d %s in this directory beginning with the letter %s.', 'name-directory'), $num_names, $directory['name_term_singular'], $name_filter['character']);
            } else {
                echo sprintf(__('There are currently %d %s in this directory beginning with the letter %s.', 'name-directory'), $num_names, $directory['name_term'], $name_filter['character']);
            }
        }
    }
    echo  '</div>';

    echo '<div class="name_directory_names">';
    if($num_names === 0 && empty($search_value))
    {
        echo '<p class="name_directory_entry_message">' . __('There are no entries in this directory at the moment', 'name-directory') . '</p>';
    }
    else if(isset($directory['show_all_names_on_index']) && $directory['show_all_names_on_index'] != 1 && empty($name_filter))
    {
        if($directory['show_index_instructions'])
        {
            echo '<p class="name_directory_entry_message">' . __('Please select a letter from the index (above) to see entries', 'name-directory') . '</p>';
        }
    }
    else
    {
        $split_at = null;
        if(! empty($directory['nr_columns']) && $directory['nr_columns'] > 1)
        {
            $split_at = round($num_names/$directory['nr_columns'])+1;
        }

        echo '<div class="name_directory_column name_directory_nr' . (int)$directory['nr_columns'] . '">';

        $i = 1;
        $split_i = 1;
        $this_letter = '---';
        foreach($names as $entry)
        {
            /* Show the header of the next starting letter */
            if(! empty($directory['show_character_header']))
            {
                if ($entry['letter'] !== $this_letter) {
                    $this_letter = $entry['letter'];

                    echo '<div class="name_directory_character_header">' . $this_letter . '</div>';
                    if(! empty($directory['show_line_between_names']))
                    {
                        echo '<hr />';
                    }
                }
            }

            name_directory_render_namebox($entry, $directory);

            if(! empty($directory['show_line_between_names']) && $num_names != $i)
            {
                echo '<hr />';
            }

            $split_i++;
            $i++;

            if($split_at == $split_i)
            {
                echo '</div><div class="name_directory_column name_directory_nr' . (int)$directory['nr_columns'] . '">';
                $split_i = 1;
            }
        }
        echo '</div>';
    }
    echo '</div>';

    if(! empty($directory['nr_columns']) && $directory['nr_columns'] > 1)
    {
        echo '<div class="name_directory_column_clear"></div>';
    }

    if(! empty($directory['show_submit_form']))
    {
        echo "<br /><br />
              <a href='" . name_directory_make_plugin_url('','name_directory_startswith', $dir) . "&show_submitform=true' 
                    class='name_directory_submit_bottom_link'>" . $submit_string . "</a>";
    }

    /** Sad to print it like this, but this is needed for translating the show more/less buttons */
    echo "<style>
        .name_directory_readmore_state ~ .name_directory_readmore_trigger:before {
            content: '... " . __('Show more', 'name-directory') . "';
        }
       
        .name_directory_readmore_state:checked ~ .name_directory_readmore_trigger:before {
            content: '" . __('Show less', 'name-directory') . "';
        }
        </style>";

    if($highlight_search_term == true)
    {

        wp_enqueue_script( 'name-directory-highlight', 'https://cdn.jsdelivr.net/mark.js/8.6.0/mark.min.js', array() );
        wp_add_inline_script( 'name-directory-highlight', 'var markInstance = new Mark(document.querySelector(".name_directory_names"));
            markInstance.mark("' . $search_value . '");');
    }

	return ob_get_clean();
}

add_shortcode('namedirectory', 'name_directory_show_directory');


/**
 * Display a random name from a given directory
 *   Thanks to @mastababa
 * @param $attributes
 * @return mixed
 */
function name_directory_show_random($attributes)
{
    $dir = null;
    extract(shortcode_atts(
        array('dir' => '1'),
        $attributes
    ));

    $directory = name_directory_get_directory_properties($dir);
    if($directory === null)
    {
        echo sprintf(__('Error: Name Directory #%d does not exist (anymore). If you are the webmaster, please change the shortcode.', 'name-directory'), $dir);
        return false;
    }

    $names = name_directory_get_directory_names($directory);

    if (! count($names))
    {
        echo __('There are no entries in this directory at the moment', 'name-directory');
    }

    $entry = $names[array_rand($names)];

    ob_start();

    echo '<div class="name_directory_random_name">';
    name_directory_render_namebox($entry, $directory);
    echo '</div>';

    return ob_get_clean();

}
add_shortcode('namedirectory_random', 'name_directory_show_random');


/**
 * Display a single name by ID
 *   -> Mind you, the name does have to be published!
 * @param $attributes
 * @return mixed
 */
function name_directory_show_single_name($attributes)
{
    $id = null;
    extract(shortcode_atts(
        array('id' => '1'),
        $attributes
    ));

    $name_entry = name_directory_get_single_name($id);
    $directory = name_directory_get_directory_properties($name_entry['directory']);

    ob_start();

    echo '<div class="name_directory_random_name">';
    name_directory_render_namebox($name_entry, $directory);
    echo '</div>';

    return ob_get_clean();
}
add_shortcode('namedirectory_single', 'name_directory_show_single_name');


/**
 * Search the Name Directory entries whenever necessary
 *      Add the search results to the WordPress search results
 * @param $where (is passed by WordPress)
 * @return string
 */
function name_directory_insert_sitewide_search_results($where)
{
    /* Only perform actions whenever we are on a search page and within the main query */
    if (is_search())
    {

        $name_directory_settings = get_option('name_directory_general_option');

        /* If WordPress search for Name Directory is disabled, just return */
        if(empty($name_directory_settings) || empty($name_directory_settings['search_on']))
        {
            return $where;
        }

        $directories = name_directory_get_directory_by_search_query(
            get_search_query(),
            $name_directory_settings['search_description'],
            $name_directory_settings['search_wildcard']
        );

        $page_ids = array();
        global $wpdb;

        /* If directories were found, get the page they are on */
        foreach($directories as $found => $dir)
        {
            /* Use a plain sql query, WP_Query unfortunately didn't work (infinite loop) */
            $host_pages = $wpdb->get_results("
			  SELECT * 
			  FROM `{$wpdb->prefix}posts` 
			  WHERE 
			    `post_type` IN ('page', 'post')
			    AND `post_status` = 'publish'
			    AND (`post_content` LIKE '%[namedirectory dir=\"{$dir['directory']}%' 
			      OR `post_content` LIKE '%[namedirectory dir={$dir['directory']}%'
			      OR `post_content` LIKE '%[namedirectory dir=''{$dir['directory']}%')");

            if ($host_pages)
            {
                /* Add the pages to the results that WordPress will present to the user */
                foreach($host_pages as $host_page)
                {
                    $page_ids[] = $host_page->ID;
                }
            }
        }

        $page_ids = array_unique($page_ids);
        if(! empty($page_ids))
        {
            $where .= " OR {$wpdb->posts}.ID IN (" . implode(",", $page_ids) . ")";
        }

    }

    return $where;
}

add_filter('posts_where', 'name_directory_insert_sitewide_search_results');


/**
 * Also add our results to Relevanssi
 */
function name_directory_insert_relevanssi_search_results($where)
{
    $new_where = name_directory_insert_sitewide_search_results($where);
    if(! empty($new_where)) {

        global $wpdb;
        $new_where = str_replace("OR {$wpdb->posts}.ID IN", " OR doc IN ", $new_where);
    }

    return $new_where;
}
add_filter('relevanssi_where', 'name_directory_insert_relevanssi_search_results');
