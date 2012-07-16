<?php
/* ======== SITE TITLE ======== */
    if (!weaver_is_checked_page_opt('ttw-hide-site-title')) { ?>
		<div id="ttw-site-logo"></div>
		<div id="ttw-site-logo-link" onclick="location.href='<?php echo home_url( '/' ); ?>';" style="cursor:pointer;"></div>
<?php
    }
    $heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div'; ?>
		<<?php echo $heading_tag; ?> id="site-title" <?php echo weaver_hide_site_title();?>>
		    <span>
			<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
		    </span>
		</<?php echo $heading_tag; ?>>
		<div id="site-description" <?php echo weaver_hide_site_title();?>><?php bloginfo( 'description' ); ?></div>
<?php
    /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */
?>
		<div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', WEAVER_TRANS ); ?>"><?php _e( 'Skip to content', WEAVER_TRANS ); ?></a></div>
