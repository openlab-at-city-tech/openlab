<?php
    /* ========== HEAD TITLE ========== */
?><title><?php
    if (weaver_getopt('ttw_hide_metainfo')) {
	wp_title('');			/* this is compatible with SEO plugins */
    } else {
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	/* Add the blog name. */
	bloginfo( 'name' );

	/* Add the blog description for the home/front page. */
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	/* Add a page number if necessary: */
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', WEAVER_TRANS ), max( $paged, $page ) );
    }
?></title>
