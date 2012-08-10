<?php
/* add css and custom scripts */

    if (!weaver_use_inline_css(weaver_get_css_filename())) { // don't generate inline CSS
	$vers = weaver_getopt('ttw_style_version');
	if (!$vers) $vers = '1';
	else $vers = sprintf("%d",$vers);
	wp_register_style('weaver-style-sheet',weaver_get_css_url(),array(),$vers);
	wp_enqueue_style('weaver-style-sheet');
    }

    /* We add some JavaScript to pages with the comment form
     * to support sites with threaded comments (when in use).
     */
    if ( is_singular() && get_option( 'thread_comments' ) )
	wp_enqueue_script( 'comment-reply' );

    if (weaver_getopt('ttw_use_superfish')) {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('weaverSFhoverIntent', get_template_directory_uri().'/js/superfish/hoverIntent.js');
	wp_enqueue_script('weaverSF', get_template_directory_uri().'/js/superfish/superfish.js');

    }
    do_action('wvrx_plus_scripts');

    wp_head();

    if (weaver_getopt('ttw_use_superfish')) {
	echo("<script>
jQuery(function(){jQuery('ul.sf-menu').superfish({animation: {opacity:'show',height:'show'}, speed: 'fast'});});
</script>\n");
    }
?>
