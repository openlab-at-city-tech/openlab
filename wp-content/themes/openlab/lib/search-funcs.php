<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//openlab search function
function openlab_site_wide_bp_search() {
    ?>
    <form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
        <input type="text" id="search-terms" name="search-terms" value="" />
        <?php //echo bp_search_form_type_select() ?>
        <select style="width: auto" id="search-which" name="search-which">
            <option value="members">People</option>
            <option value="courses">Courses</option>
            <option value="projects">Projects</option>
            <option value="clubs">Clubs</option>
            <option value="portfolios">Portfolios</option>
        </select>

        <input type="submit" name="search-submit" id="search-submit" value="<?php _e('Search', 'buddypress') ?>" />
        <?php wp_nonce_field('bp_search_form') ?>
    </form><!-- #search-form -->
    <?php
}

add_action('init', 'openlab_search_override', 1);

function openlab_search_override() {
    global $bp;
    if (isset($_POST['search-submit']) && $_POST['search-terms']) {
        if ($_POST['search-which'] == "members") {
            wp_redirect($bp->root_domain . '/people/?search=' . $_POST['search-terms']);
            exit();
        } elseif ($_POST['search-which'] == "courses") {
            wp_redirect($bp->root_domain . '/courses/?search=' . $_POST['search-terms']);
            exit();
        } elseif ($_POST['search-which'] == "projects") {
            wp_redirect($bp->root_domain . '/projects/?search=' . $_POST['search-terms']);
            exit();
        } elseif ($_POST['search-which'] == "clubs") {
            wp_redirect($bp->root_domain . '/clubs/?search=' . $_POST['search-terms']);
            exit();
        } elseif ($_POST['search-which'] == "portfolios") {
            wp_redirect($bp->root_domain . '/portfolios/?search=' . $_POST['search-terms']);
            exit();
        }
    }
}
