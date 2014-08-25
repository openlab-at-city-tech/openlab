<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//openlab search function
function openlab_site_wide_bp_search() {
    ?>
<div class="search-trigger-wrapper">
    <span class="fa fa-search search-trigger"></span>
</div>
    <div class="search-form-wrapper">
    <form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form" class="form-inline">
        <div class="form-group">
        <input id="search-terms" class="form-control" type="text" name="search" placeholder="Search" />
        <?php //echo bp_search_form_type_select() ?>
        <div class="hidden-custom-select">
        <select style="width: auto" id="search-which" name="search-which" class="form-control">
            <option value="members">People</option>
            <option value="courses">Courses</option>
            <option value="projects">Projects</option>
            <option value="clubs">Clubs</option>
            <option value="portfolios">Portfolios</option>
        </select>
        </div>

        <button class="btn btn-primary top-align" id="search-submit" type="submit"><i class="fa fa-search"></i></button>
        <?php wp_nonce_field('bp_search_form') ?>
        </div>
    </form><!-- #search-form -->
    </div>
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
