<?php
/* ======== HEADER IMAGE ======== */
    if (!weaver_is_checked_page_opt('ttw-hide-header-image')) {
	if (HEADER_IMAGE_HEIGHT > 0 && !(weaver_getopt('ttw_header_frontpage_only') && is_front_page() )) {
	    if (weaver_getopt('ttw_link_site_image')) {
?>
		    <a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
<?php
	    }
	/* Check if this is a post or page, if it has a thumbnail,  and if it's a big one */
	if ( is_singular() && !weaver_getopt('ttw_hide_featured_header')
	    && has_post_thumbnail( $post->ID )
	    && (  $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'post-thumbnail' ) )  /* $src, $width, $height */
	    && $image[1] >= HEADER_IMAGE_WIDTH) {
		/*  Houston, we have a new header image! */
	    echo get_the_post_thumbnail( $post->ID, 'post-thumbnail' );
	    } else {
?>
			<img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />
<?php
	    }
	    if (weaver_getopt('ttw_link_site_image')) echo("</a>\n");	/* need to close link */
	} /* closes header > 0 */
    } /* end ttw-hide-header-image */
?>
