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
                <header class="menu-title pull-left"><a href="<?php echo bp_get_root_domain(); ?>" title="<?php _ex('Home', 'Home page banner link title', 'buddypress'); ?>"><?php bp_site_name(); ?></a></header>
                <div class="pull-right search">
                    <div class="search-trigger-wrapper">
                        <button  class="search-trigger btn-link" data-mode="mobile" data-location="<?= $location ?>" href="#"><span class="fa fa-search" aria-hidden="true"></span><span class="sr-only">Open Search</span></button>
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
	// Don't load on widgets or other API requests.
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	if ( ! cboxol_show_admin_bar_for_anonymous_users( get_current_blog_id() ) ) {
		return;
	}

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

function openlab_mu_site_wide_bp_search( $mode = 'desktop', $location = '' ) {
    $mobile_mup = '';

    if ($mode == 'desktop'):
		$wrapper_class = 'search-trigger-wrapper';
		if ( openlab_is_search_results_page() ) {
			$wrapper_class .= ' current-menu-item';
		}

        $mobile_mup = '<div class="' . esc_attr( $wrapper_class ) .  '">
    <button class="search-trigger btn-link" data-mode="desktop" data-location="' . esc_attr( $location ) . '" href="#">Search <span class="fa fa-search" aria-hidden="true"></span></button>
</div>';
    endif;

    $form_action = bp_search_form_action();
    $nonce = wp_create_nonce('bp_search_form');

    $mobile_mup .= <<<HTML
    <div class="search-form-wrapper search-mode-{$mode} search-form-location-{$location}">
    <form action="{$form_action}" method="post" id="search-form-{$mode}-{$location}" class="form-inline">
        <div class="form-group">
        <label for="search-terms-{$mode}-{$location}" class="sr-only">Search the OpenLab</label>
        <input id="search-terms-{$mode}-{$location}" class="form-control search-terms search-terms-{$mode}" type="text" name="search" placeholder="Search" />

        <button class="btn btn-primary top-align search-submit" id="search-submit-{$mode}-{$location}" type="submit"><i class="fa fa-search"></i><span class="sr-only">Submit</span></button>
        <input type="hidden" id="_bp_search_nonce_{$mode }_{$location}" name="_bp_search_nonce" value="{$nonce}" />
        </div>
    </form><!-- #search-form -->
    </div>
HTML;

    echo $mobile_mup;
}

add_action('init', 'openlab_mu_search_override', 1);

function openlab_mu_search_override() {
    if ( isset( $_POST['search'] ) ) {
        $nonce = isset( $_POST['_bp_search_nonce'] ) ? $_POST['_bp_search_nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, 'bp_search_form' ) ) {
            return;
        }

		$search_url   = get_home_url( bp_get_root_blog_id(), 'search' );
		$redirect_url = add_query_arg( 'search', wp_unslash( $_POST['search'] ), $search_url );
		wp_safe_redirect( $redirect_url );
		die;
    }
}

/**
 * Custom Login Logo.
 *
 * @return void
 */
function openlab_login_logo() {
	?>
	<style type="text/css">
	#login h1 a {
		background-image: url(<?php echo plugins_url( 'css/images/openlab-logo.png', __FILE__ ); ?>);
		background-size: 286px 94px;
		height: 94px;
		width: 286px;
	}
	</style>
	<?php
}
add_action( 'login_enqueue_scripts', 'openlab_login_logo' );
