<?php
/**
 * OpenLab Top Header Markup
 */

/**
 * Sitewide header markup
 * Includs sitewide logo and sitewide search
 */
function openlab_sitewide_header($location = 'header') {
    ?>

    <div class="header-mobile-wrapper visible-xs">
        <div class="container-fluid">
            <div class="navbar-header clearfix">
                <h1 class="menu-title pull-left"><a href="<?php echo home_url(); ?>" title="<?php _ex('Home', 'Home page banner link title', 'buddypress'); ?>"><?php bp_site_name(); ?></a></h1>
                <div class="pull-right search">
                    <div class="search-trigger-wrapper">
                        <span class="fa fa-search search-trigger" data-mode="mobile" data-location="<?= $location ?>"></span>
                    </div>
                </div>
            </div>
            <div class="search search-form row">
                <?php openlab_mu_site_wide_bp_search('mobile', $location); ?>
            </div>
        </div>
    </div>

    <?php
}

function openlab_sitewide_header_to_admin_and_group_sites() {
    
    if (get_current_blog_id() !== 1 || is_admin()) {
        ?>

        <nav class="navbar navbar-default oplb-bs navbar-location-oplb-bs visible-xs" role="navigation">
            <?php openlab_sitewide_header(); ?>
        </nav>

        <?php
    }
}

add_action('wp_footer', 'openlab_sitewide_header_to_admin_and_group_sites');
add_action('in_admin_header', 'openlab_sitewide_header_to_admin_and_group_sites');

function openlab_mu_site_wide_bp_search($mode = 'desktop', $location) {
    $mobile_mup = '';

    if ($mode == 'desktop'):

        $mobile_mup .= <<<HTML
<div class="search-trigger-wrapper">
    <span class="fa fa-search search-trigger" data-mode="desktop" data-location={$location}></span>
</div>
HTML;
    endif;

    $form_action = bp_search_form_action();
    $nonce = wp_nonce_field('bp_search_form', '_wpnonce', true, false);

    $mobile_mup .= <<<HTML
    <div class="search-form-wrapper search-mode-{$mode} search-form-location-{$location}">
    <form action="{$form_action}" method="post" id="search-form" class="form-inline">
        <div class="form-group">
        <input id="search-terms" class="form-control" type="text" name="search" placeholder="Search" />
        <select id="search-which" name="search-which" class="form-control">
            <option value="members">People</option>
            <option value="courses">Courses</option>
            <option value="projects">Projects</option>
            <option value="clubs">Clubs</option>
            <option value="portfolios">Portfolios</option>
        </select>

        <button class="btn btn-primary top-align" id="search-submit" type="submit"><i class="fa fa-search"></i></button>
        {$nonce}
        </div>
    </form><!-- #search-form -->
    </div>
HTML;

    echo $mobile_mup;
}

add_action('init', 'openlab_mu_search_override', 1);

function openlab_mu_search_override() {
    global $bp;
    if (isset($_POST['search']) && $_POST['search-which']) {
        if ($_POST['search-which'] == "members") {
            wp_redirect($bp->root_domain . '/people/?search=' . $_POST['search']);
            exit();
        } elseif ($_POST['search-which'] == "courses") {
            wp_redirect($bp->root_domain . '/courses/?search=' . $_POST['search']);
            exit();
        } elseif ($_POST['search-which'] == "projects") {
            wp_redirect($bp->root_domain . '/projects/?search=' . $_POST['search']);
            exit();
        } elseif ($_POST['search-which'] == "clubs") {
            wp_redirect($bp->root_domain . '/clubs/?search=' . $_POST['search']);
            exit();
        } elseif ($_POST['search-which'] == "portfolios") {
            wp_redirect($bp->root_domain . '/portfolios/?search=' . $_POST['search']);
            exit();
        }
    }
}
